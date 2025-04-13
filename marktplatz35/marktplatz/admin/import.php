<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserate-Importieren','','Imp');

if($nSegNo!=0){ //Segment gewaehlt
 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aET=array(); $nSchritt=0; $sSep=';'; $sOnl='1'; $sKpf='1';
 if(MP_Pfad>''){
  if(!MP_SQL){//Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
    if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
 }else $Meld='Bitte zuerst die Pfade im Setup einstellen!';

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if(file_exists(MP_Pfad.'temp/import.txt')) unlink(MP_Pfad.'temp/import.txt');
 }else{ //POST
  $nSchritt=(isset($_POST['Schritt'])?$_POST['Schritt']:0);
  $sSep=(isset($_POST['Sep'])?$_POST['Sep']:';');
  $sKpf=(isset($_POST['Kpf'])?$_POST['Kpf']:'0');
  $sOnl=(isset($_POST['Onl'])?sprintf('%0d',$_POST['Onl']):'1');
  if($nSchritt==1){$nSchritt=0; // vom Upload-Formular
   $ImpNa=str_replace(' ','_',basename($_FILES['Dat']['name'])); $ImpEx=strtolower(strrchr($ImpNa,'.'));
   if($ImpEx=='.txt'||$ImpEx=='.csv'){
    if($fi=fopen($_FILES['Dat']['tmp_name'],'r')){
     $aH=fgetcsv($fi,16000,($sSep!='t'?$sSep:"\t")); $aZ=fgetcsv($fi,16000,($sSep!='t'?$sSep:"\t")); $nF=count($aH); fclose($fi);
     if($nF>1){
      if(@copy($_FILES['Dat']['tmp_name'],MP_Pfad.'temp/import.txt')){
       $sOpt='<option value="">----</option>'; include('feldtypenInc.php');
       for($i=1;$i<$nF;$i++){
        $sF=trim($aH[$i]); if(strlen($sF)>35) $sF=substr($sF,0,35);
        $sT=trim((isset($aZ[$i])?$aZ[$i]:'')); if(strlen($sT)>35) $sT=substr($sT,0,35).'...';
        $sOpt.='<option value="'.$i.'">'.$sF.($sT?' (Beispiel: '.$sT.')':'').'</option>';
       }
       $nSchritt=1; $Meld='Ordnen Sie den Inseratefeldern die Felder der Importdatei zu'; $MTyp='Meld';
      }else $Meld='Die Datei konnte nicht unter <i>temp/import.txt</i> gespeichert werden!';
     }else $Meld='Die hochgeladenen Datei enthält nicht das angegebene Trennzeichen!';
    }else $Meld='Die hochgeladenen Datei konnte nicht geöffnet werden!';
   }else $Meld='Es sind nur Dateien mit der Endung <i>txt</i> oder <i>csv</i> erlaubt!';
  }elseif($nSchritt==2){$nSchritt=0; //von Feldzuordnungen
   $aOrd=array(); $aNeu=array(); $bWrn=false; $sF=''; $nFehl=0; $nErfo=0; $bWrnD=false;
   for($i=0;$i<$nFelder;$i++){
    if(isset($_POST['F'.$i])) $k=$_POST['F'.$i]; else $k=''; if(strlen($k)>0) $aOrd[$i]=(int)$k;
    if($i>0) $sF.=',mp_'.$i;
   }
   if(count($aOrd)>1){
    $aI=file(MP_Pfad.'temp/import.txt'); $nImpZhl=count($aI);
    $sRefDat=date('Y-m-d'); $sNeuDat=date('Y-m-d',min(86400*$aET[1]+time(),2147483647));
    for($i=($sKpf>'0'?1:0);$i<$nImpZhl;$i++){ //alle Importzeilen
     $s=''; $aZl=explode(($sSep!='t'?$sSep:"\t"),$aI[$i]);
     if(isset($aOrd[1])){ //Ablaufdatum
      $t=$aZl[$aOrd[1]]; $a=explode('.',$t);
      if(count($a)==3){$s=sprintf("%04d-%02d-%02d",($a[2]<100?$a[2]+2000:$a[2]),$a[1],$a[0]);}
      else{$a=explode('-',$t); if(count($a)==3){$s=sprintf("%04d-%02d-%02d",($a[0]<100?$a[0]+2000:$a[0]),$a[1],$a[2]);}}
     }
     $sZl=($s>$sRefDat?$s:$sNeuDat); if(MP_SQL) $sZl=',"'.$sZl.'"';
     for($j=2;$j<$nFelder;$j++){ //weitere Inseratefelder
      if(isset($aOrd[$j])) $s=str_replace('"','',@strip_tags(trim($aZl[$aOrd[$j]]))); else $s='';
      if($s){
       $t=$aFT[$j];
       switch($t){
        case 't': case 'm': case 'a': case 'k': case 's': case 'o': case 'j': case 'v': case 'x': break;
        case 'd': if($v=fMpErzeugeDatum($s)) $s=fMpAnzeigeDatum($v); break;
        case 'z': $a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',$a[0],(isset($a[1])?$a[1]:0)); break;
        case '@': $s=fMpAnzeigeDatum(date('Y-m-d H:i')).date(' H:i'); break;
        case 'n': case '1': case '2': case '3': $s=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),(int)$t,'.',''); break;
        case 'r': $s=str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s));  break;
        case 'w': $s=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),MP_Dezimalstellen,'.',''); break;
        case 'l': if($p=strpos(strtolower(substr($s,0,7)),'ttp://')) $s=substr($s,$p+6); break;
        case 'e': case 'c': if(!MP_SQL) $s=fMpEnCode($s); break;
        case 'b': case 'f': $bWrnD=true; if($t=='b'&&strpos($s,'|')<=0) $s.='|'.$s; break;
        case 'u': $s=sprintf('%04d',$s); break;
        case 'p': $s=fMpEnCode($s); break; //Passwort
       }
      }
      if(!MP_SQL) $sZl.=';'.str_replace(NL,'\n ',str_replace("\r",'',str_replace(';','`,',$s)));
      else $sZl.=',"'.str_replace(NL,"\r\n",str_replace('\n ',NL,str_replace('"','\"',$s))).'"';
     }
     if(!MP_SQL) $aNeu[]=$sZl; //ohne SQL
     elseif($DbO){ //bei SQL
      if($DbO->query('INSERT IGNORE INTO '.str_replace('%',$sSegNo,MP_SqlTabI).' (online'.$sF.') VALUES("'.$sOnl.'"'.$sZl.')')) $nErfo++;
      else $nFehl++;
     }else $Meld='MySQL-Datenbank nicht geöffnet!';
    }
    if(!MP_SQL){
     $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $s=rtrim($aD[$i]); $p=strpos($s,';'); $nId=max($nId,(int)substr($s,0,$p));
      $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
     }
     foreach($aNeu as $s){$aTmp[(++$nId).';'.$sOnl]=$s; $nErfo++; } // anhaengen
     $aD=array(); $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v.NL;
     if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){//neu schreiben
      fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);
     }else $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
    }else if($nFehl) $Meld='Einfügefehler bei '.$nFehl.' Datensätzen.';
    if(empty($Meld)){
     $MTyp='Erfo'; $Meld='Es wurden '.$nErfo.' Inserate importiert.';
     if($bWrnD) $Meld.='</p><p class="admFehl" style="text-align:center;">Bilder und Dateianhänge wurde jedoch nicht importiert.';
    }
   }else $Meld='Es wurden weniger als 2 Felder zugeordnet, der Import erfolgte nicht!';
  }else $Meld='Es wurden keine Daten verarbeitet!';
 }//POST

 if(empty($Meld)){$Meld='Importieren Sie jetzt Inserate in das Segment '.$sSegNam.''; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'" style="text-align:center;">'.$Meld.'</p>'.NL;
 echo '<p class="admSubmit">Segment '.$nSegNo.': '.$sSegNam.'</p>';

 if($nSchritt==0){ //GET oder falscher Upload
?>

<form action="import.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="Schritt" value="1" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">
Laden Sie zuerst die Datei der zu importierenden Inserate hoch.
Es werden nur Dateien mit der Endung <i>.txt</i> oder <i>.csv</i> akzeptiert.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Importdatei</td>
 <td><input type="file" name="Dat" size="80" style="width:99%" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Feldtrenner</td>
 <td>
  <select name="Sep" style="width:50px;">
   <option value=";"<?php if($sSep==';') echo ' selected="selected"'?>>;</option>
   <option value=","<?php if($sSep==',') echo ' selected="selected"'?>>,</option>
   <option value="t"<?php if($sSep=="\t") echo ' selected="selected"'?>>TAB</option>
  </select> &nbsp; <span class="admMini">(dieses Zeichen trennt in der zu importierenden Datei die Werte)</span>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile</td>
 <td><input type="checkbox" class="admCheck" name="Kpf<?php if($sKpf>'0') echo '" checked="checked'?>" value="1" /> &nbsp;
 <span class="admMini">(die erste Zeile der Importdatei ist eine Kopfzeile)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inseratestatus</td>
 <td>
  <input type="radio" class="admRadio" name="Onl<?php if($sOnl>'0') echo '" checked="checked'?>" value="1" /> online &nbsp;
  <input type="radio" class="admRadio" name="Onl<?php if($sOnl<'1') echo '" checked="checked'?>" value="0" /> offline &nbsp;
  <span class="admMini">(die Inserate sind nach dem Import in diesem Zustand)</span>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Hochladen"></p>
</form>

<?php
 }elseif($nSchritt==1){ //Upload OK, Felder zuordnen
?>
<form action="import.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="Schritt" value="2" />
<input type="hidden" name="Sep" value="<?php echo $sSep?>" />
<input type="hidden" name="Onl" value="<?php echo $sOnl?>" />
<input type="hidden" name="Kpf" value="<?php echo $sKpf?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td>Feld im Marktsegment</td><td>Feld aus der Importdatei</td></tr>
<tr class="admTabl">
 <td><?php echo str_replace('`,',',',$aFN[0]).'<div class="admMini">Typ: '. $aTyp[$aFT[0]].'<div>' ?></td>
 <td>wird automatisch befüllt</td>
</tr>
<?php for($i=1;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td><?php echo str_replace('`,',',',$aFN[$i]).'<div class="admMini">Typ: '. $aTyp[$aFT[$i]].'<div>' ?></td>
 <td><select name="F<?php echo $i?>"><?php echo $sOpt?></select></td>
</tr>
<?php }?>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>
</form>
<?php
 }

}else echo '<p class="admMeld"style="margin-top:32px;text-align:center;">In das leere Muster-Segment kann nicht importiert werden. Bitte wählen Sie zuerst ein Segment.</p>';

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aET;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aET=explode(';',rtrim($aStru[15])); $aET[0]='';  //$aET[1]='';
 return true;
}
?>