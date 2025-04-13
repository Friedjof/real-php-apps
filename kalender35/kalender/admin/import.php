<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminimport','','Imp');

$bUploadOK=false;
if($_SERVER['REQUEST_METHOD']=='GET'){
 $nFelder=count($kal_FeldName); $sSep=";"; $bNum=false;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $nFelder=count($kal_FeldName);
 $ImpNa=str_replace(' ','_',basename($_FILES['Dat']['name'])); $ImpEx=strtolower(strrchr($ImpNa,'.'));
 $sSep=$_POST['Sep']; $bNum=$_POST['Num']=='1'; if($sSep=='t') $sSep="\t";
 if($ImpEx=='.txt'||$ImpEx=='.csv'){
  if($fi=fopen($_FILES['Dat']['tmp_name'],'r')){
   $aH=fgetcsv($fi,16000,$sSep); $nC=count($aH); $bOK=true; $Nd=''; $Ff=''; $Ad=''; $k=($bNum?0:1); $nDtPos2=0;
   for($i=1;$i<$nFelder;$i++){
    if($kal_FeldName[$i]!=(isset($aH[$i-$k])?rtrim($aH[$i-$k]):'~')){$bOK=false; $Nd.=', '.$kal_FeldName[$i]; $Ff.=', '.(isset($aH[$i-$k])?rtrim($aH[$i-$k]):'???');}
    if($kal_FeldType[$i]=='d'&&$nDtPos2<2) $nDtPos2=$i;
   }
   $i-=$k; for($i=$i;$i<$nC;$i++){$bOK=false; $Ad.=', '.(rtrim($aH[$i])!=''?rtrim($aH[$i]):';');}
   if($bOK){ //Kopfzeile OK
    if(!KAL_SQL) $aImp=array(); $aLsch=array(); //ohne SQL
    $z=0; $bWrnD=false; $sWrnZ=''; $sWrnQ=''; if(!$sRefDat=@date('Y-m-d',time()-86400*KAL_HalteAltesNochTage)) $sRefDat='';
    while($aL=fgetcsv($fi,16000,$sSep)){ //ueber alle Importzeilen
     $sZ=''; $sF=''; $z++; $bOK=true;
     for($i=($bNum?1:0);$i<$nC;$i++){
      $s=str_replace('"','',@strip_tags(trim($aL[$i]))); $t=$kal_FeldType[$i+$k];
      switch($t){
      case 't': case 'm': case 'a': case 'k': case 's': case 'j': case '#': case 'v': case 'g': case 'x': //Text,Memo,Kategorie,Auswahl,Ja/Nein,StreetMap
       break;
      case 'd': //Datum
       if($s) if(!$s=fKalErzeugeDatum($s)) $s=''; if($sZ==''&&($s==''||substr($s,0,10)<$sRefDat)) $bOK=false; break;
      case '@': //Eintragsdatum
       if($s){$v=$s; if($s=substr(fKalErzeugeDatum($s),0,10)){if($p=strpos($v,':')) $s.=' '.trim(substr($v,$p-2,5));} else $s='';} break;
      case 'z': //Uhrzeit
       if($s){$a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',$a[0],$a[1]);} break;
      case 'e': case 'c': // E-Mail, Kontakt-E-Mail
       if($s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) /* */;
       if(!KAL_SQL) $s=fKalEnCode($s); break;
      case 'l': //Link oder E-Mail
       /* if($p=strpos(strtolower(substr($s,0,7)),'ttp://')) $s=substr($s,$p+6); */ break;
      case 'b': case 'f': //Bild,Datei
       if($s){$bWrnD=true; if($t=='b'&&strpos($s,'|')<=0) $s.='|'.$s;} break;
      case 'w': //Waehrung
       $s=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),KAL_Dezimalstellen,'.',''); break;
      case 'n': case '1': case '2': case '3': //Zahl
       $s=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),(int)$t,'.',''); break;
      case 'r': //Zahl
       $s=str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)); break;
      case 'o': //PLZ
       if($s) if(strlen($s)!=KAL_PLZLaenge) /* */; break;
      case 'u': $s=sprintf('%04d',$s); break;
      case 'p': $s=fKalEnCode($s); break; //Passwort
      }
      if(!KAL_SQL) $sZ.=';'.str_replace(';','`,',$s); else{$sZ.=',"'.str_replace("'","\'",str_replace('\n ',"\r\n",str_replace('`,',';',$s))).'"'; $sF.=',kal_'.($i+$k);}
     }
     if($bOK){
      if(!KAL_SQL) $aImp[]=substr($sZ,1); //ohne SQL
      elseif($DbO){ //bei SQL
       if(!$DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online'.$sF.',periodik) VALUES("1"'.$sZ.',"")')) $sWrnQ.=','.$z;
      }else echo '<p class="admFehl">MySQL-Datenbank nicht geöffnet!</p>';
     }else $sWrnZ.=','.$z;
    } //while
    if(!KAL_SQL){ //ohne SQL speichern
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $aTmp=array(); $nDtPos2++;
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $s=rtrim($aD[$i]); $p=strpos($s,';'); $nId=max($nId,(int)substr($s,0,$p));
      if(substr($s,$p+3,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
      elseif(KAL_EndeDatum&&($nDtPos2>2)){
       $aZl=explode(';',$s,$nDtPos2+2);
       if(substr($aZl[$nDtPos2],0,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
       else $aLsch[(int)substr($s,0,$p)]=true;
      }else $aLsch[(int)substr($s,0,$p)]=true;
     }

     foreach($aImp as $v){$aTmp[(++$nId).';1']=$v; $aIds[]=$nId;} $aD=array();
     $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v.NL;
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){ //neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bOK=true;
      if(KAL_ListenErinn>0||KAL_DetailErinn>0){ // Erinnerungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],11,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      }}
      if(KAL_ListenBenachr>0||KAL_DetailBenachr>0){ //Benachrichtigungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],0,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      }}
      if(KAL_ZusageSystem){//Zusagenliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],0,20); $n=(int)substr($s,1+strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     }}}else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
    }elseif($DbO){ //SQL
     $sDtFld2=''; if(KAL_EndeDatum&&($nDtPos2>1)) $sDtFld2=' AND kal_'.$nDtPos2.'<"'.$sRefDat.'"'; $sE='';
     if($rR=$DbO->query('SELECT id FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2)){
      while($a=$rR->fetch_row()){$aLsch[(int)$a[0]]=true; $sE.=' OR termin="'.$a[0].'"';} $rR->close();
      $DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2);
      if($sE){ $sE=substr($sE,4);
       if(KAL_ListenErinn>0||KAL_DetailErinn>0) $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE '.$sE);
       if(KAL_ListenBenachr>0||KAL_DetailBenachr>0) $DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE '.$sE);
       if(KAL_ZusageSystem) $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.$sE);
     }}
    }
    if(count($aLsch)>0&&(in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))){ //veraltete Bilder und Dateien
     if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
      $aD=array(); while($s=readdir($f)) if($i=(int)$s) if($aLsch[$i]) $aD[]=$s; closedir($f);
      foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
    }}
   }else{
    $Msg='<p class="admFehl">Die erste Zeile der Importdatei enthält abweichende Feldnamen/Feldreihenfolgen zur Terminstuktur!</p>';
    if(!empty($Nd)) $Msg.='<p>Es fehlt die Entsprechung zum Feld <i>'.substr($Nd,2).'</i>.<br />Statt dessen erscheint das Feld <i>'.substr($Ff,2).'</i>.</p>';
    if(!empty($Ad)) $Msg.='<p>Das zusätzliche Importfeld <i>'.substr($Ad,2).'</i> hat keine Entsprechung im Kalender.</p>';
   }
   fclose($fi);
  }else $Msg='<p class="admFehl">Die hochgeladenen Datei konnte nicht geöffnet werden!</p>';
 }else $Msg='<p class="admFehl">Es sind nur Dateien mit der Endung <i>txt</i> oder <i>csv</i> erlaubt!</p>';
 if(empty($Msg)){
  $Msg='<p class="admErfo">Der Datenimport wurde durchgeführt!</p>';
  if($sWrnZ) $Msg.='<p class="admMeld">Die Zeile '.substr($sWrnZ,1).' wurden wegen ungültigen Datums nicht importiert!</p>';
  if($sWrnQ) $Msg.='<p class="admMeld">Die Zeile '.substr($sWrnQ,1).' wurden wegen MySQL-Fehler nicht importiert!</p>';
  if($bWrnD) $Msg.='<p class="admMeld">Es wurden Daten für Bilder/Dateianlagen importiert, diese jedoch nicht hochgeladen!</p>';
 }else $Msg='<p class="admFehl">Der Datenimport wurde nicht durchgeführt!</p>'.$Msg;
}

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Sie können jetzt Termine aus einer Text- oder CSV-Datei importieren.</p>';
echo $Msg.NL;
$sKopf=''; if(is_array($kal_FeldName)) for($i=($bNum?0:1);$i<$nFelder;$i++) $sKopf.=$kal_FeldName[$i].';';


if(!$bUploadOK){ //GET oder falscher Pfad
?>

<form action="import.php" enctype="multipart/form-data" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">
Laden Sie zuerst die Datei der zu importierenden Termine hoch.
Es werden nur Dateien mit der Endung <i>.txt</i> oder <i>.csv</i> akzeptiert.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Importdatei</td>
 <td><input type="file" name="Dat" size="80" style="width:100%" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Einzelheiten</td>
 <td>
  Feldtrenner: <select name="Sep" style="width:50px;">
   <option value=";"<?php if($sSep==';') echo ' selected="selected"'?>>;</option>
   <option value=","<?php if($sSep==',') echo ' selected="selected"'?>>,</option>
   <option value="t"<?php if($sSep=="\t") echo ' selected="selected"'?>>TAB</option>
  </select> &nbsp; &nbsp;
  mit vorangestellter lfd. Nummer:
  <input class="admRadio" type="radio" name="Num" value="1"<?php if($bNum) echo ' checked="checked"'?> />ja &nbsp;
  <input class="admRadio" type="radio" name="Num" value="0"<?php if(!$bNum) echo' checked="checked"'?> />nein
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>
</form>

<?php
}else{ //Upload OK

}
?>

<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2"><p>Hinweise:</p>
<p>Die zu importierenden Termine müssen im einfachen CSV-Format vorliegen.
Jeder zu importierende Termindatensatz entspricht einer Zeile dieser Datei.
Die Datenfelder müssen üblicherweise durch ein Semikolon ; voneinander getrennt sein.
Alternativ kann ein anderes Trennzeichen (z.B. Komma oder Tabulator) angewandt werden, sofern dieses extra angegeben wird.</p>
<p>Die erste Zeile der Importdatei muss die Datenfeldnamen in exakt der gleichen Reihenfolge enthalten,
die momentan in der Terminstruktur verwandt werden. Das sind (bis auf das eventuell vorangestellte Nummernfeld):</p>
<pre style="margin:3px;"><?php echo substr($sKopf,0,-1);?></pre>
<p style="margin-top:6px;">Die Termine werden kumulativ importiert.
Es wird <i>nicht</i> geprüft, ob ein vergleichbarer Termin bereits existiert.
Mehrfacher Import führt somit zwangsläufig zu mehrfachen Termindatensätzen.
Sichern Sie eventuell vor dem Import Ihre bisherige Termintabelle!</p>
</td></tr>
</table>

<?php

echo fSeitenFuss();

function DeCryptOldPw($Pw,$CC){
 $j=0; for($k=strlen($Pw)/2-1;$k>=0;$k--) $Out.=chr($CC+($j++)+hexdec(substr($Pw,$k+$k,2)));
 return $Out;
}
?>