<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Umfragedaten importieren','','ZFi');

$bUploadOK=false; $sSep=''; $bWrnBld=false; $bErsatz=true; $bOrigin=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);
if($_SERVER['REQUEST_METHOD']=='POST'){
 $ImpNa=str_replace(' ','_',basename($_FILES['Dat']['name'])); $ImpEx=strtolower(strrchr($ImpNa,'.'));
 $sSep=(isset($_POST['Sep'])?$_POST['Sep']:''); if($sSep=='t') $sSep="\t";
 $bErsatz=(isset($_POST['ersatz'])&&$_POST['ersatz']?true:false);
 $bOrigin=(isset($_POST['origin'])&&$_POST['origin']?true:false);
 if($ImpEx=='.txt'||$ImpEx=='.csv'){
  if($fi=fopen($_FILES['Dat']['tmp_name'],'r')){
   $nNr=0; $nImp=0; $sFFeld=''; $aFPos=array(); for($i=0;$i<29;$i++) $aFPos[$i]=-1;
   $aHd=fgetcsv($fi,16000,$sSep); $nFlds=count($aHd);
   for($i=0;$i<$nFlds;$i++){
    $s=str_replace('-','',str_replace('_','',str_replace(' ','',trim($aHd[$i])))); $t=strtolower(substr($s,0,7));
    switch($t){
     case 'nummer': $aFPos[0]=$i; break; case 'antwort':if($k=(int)substr($s,7,2)){$aFPos[6+$k]=$i; $nNr++;} break;
     case 'umfrage':$aFPos[2]=$i; break; case 'frage':  $aFPos[3]=$i; $nNr++; break;
     case 'bild':   $aFPos[4]=$i; break; case 'anmerku':if(substr($s,9,1)=="2") $aFPos[6]=$i; else $aFPos[5]=$i; break;
     case 'aktiv':  $aFPos[1]=$i; break; default: $sFFeld.=', '.$aHd[$i];
    }
   }
   if(empty($sFFeld)){//keine unbekanntes Feld
    if($nNr>2){//mindestens Frage und 2 Antworten
     if(!UMF_SQL){//Text
      $aDat=file(UMF_Pfad.UMF_Daten.UMF_Fragen);
      $s=''; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=';Antwort'.$i; $aDat[0]='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'.$s.NL;
      $nDat=count($aDat); $aAltNr=array(0); $nNr=0;
      for($i=1;$i<$nDat;$i++){$nNr=(int)substr($aDat[$i],0,6); $aAltNr[$i]=$nNr;} $aNeuNr=$aAltNr;
      while($aZl=fgetcsv($fi,16000,$sSep)){
       $sZl=''; if($aFPos[0]<0) $nNeu=0; else $nNeu=(int)$aZl[$aFPos[0]]; //Import-Nr holen
       $sZl.=';'.($aFPos[1]>=0?sprintf('%0d',$aZl[$aFPos[1]]):'0');
       $sZl.=';'.($aFPos[2]>=0?sprintf('%0d',$aZl[$aFPos[2]]):'0');
       for($i=3;$i<27;$i++) $sZl.=';'.(isset($aFPos[$i])&&($aFPos[$i]>=0)?str_replace('"',"'",trim($aZl[$aFPos[$i]])):'');
       if($bOrigin&&$nNeu>0){
        if($nPos=array_search($nNeu,$aAltNr)) $aDat[$nPos]=$nNeu.$sZl; // ersetzen
        else{$aDat[]=$nNeu.$sZl; $aNeuNr[]=$nNeu;} // anhaengen
       }elseif($bErsatz&&$nNeu>0&&($nPos=array_search($nNeu,$aAltNr))){ // ersetzen
        $aDat[$nPos]=$nNeu.$sZl;
       }else{ // anhaengen
        $aDat[]=(++$nNr).$sZl; $aNeuNr[]=$nNr;
       }
       $nImp++; if($aFPos[4]>=0) if($aZl[$aFPos[4]]) $bWrnBld=true;
      }
      asort($aNeuNr); reset($aNeuNr);
      if($f=@fopen(UMF_Pfad.UMF_Daten.UMF_Fragen,'w')){ //neu schreiben
       foreach($aNeuNr as $i=>$xx) fwrite($f,rtrim($aDat[$i])."\n"); fclose($f);
      }else $Msg=str_replace('#','<i>'.UMF_Daten.UMF_Fragen.'</i>',UMF_TxDateiRechte);
     }elseif($DbO){ //SQL
      $sAltNr='#;'; $nNxt=0;
      if($rR=$DbO->query('SELECT Nummer FROM '.UMF_SqlTabF)){
       while($a=$rR->fetch_row()){$sAltNr.=$a[0].';'; $nNxt=max((int)$a[0],$nNxt);} $rR->close();
      }
      while($aZl=fgetcsv($fi,16000,$sSep)){
       $sZl=''; if($aFPos[0]<0) $nNeu=0; else $nNeu=(int)$aZl[$aFPos[0]]; //Import-Nr holen
       if($bErsatz&&$nNeu>0&&strpos($sAltNr,';'.$nNeu.';')){ // Update
        $s=''; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=',Antwort'.$i;
        $aFn=array('#','aktiv','Umfrage','Frage','Bild','Anmerkung1','Anmerkung2'.$s);
        $sZl.=','.$aFn[1].'="'.($aFPos[1]>=0?sprintf('%0d',$aZl[$aFPos[1]]):'0').'"';
        $sZl.=','.$aFn[2].'="'.($aFPos[2]>=0?sprintf('%0d',$aZl[$aFPos[2]]):'0').'"';
        for($i=3;$i<17;$i++) $sZl.=','.$aFn[$i].'="'.(isset($aFPos[$i])&&($aFPos[$i]>=0)&&isset($aZl[$aFPos[$i]])?str_replace('\n ',"\r\n",str_replace('"',"'",str_replace('`,',';',trim($aZl[$aFPos[$i]])))):'').'"';
        $sQ='UPDATE IGNORE '.UMF_SqlTabF.' SET '.substr($sZl,1).' WHERE Nummer="'.$nNeu.'"';
       }else{ //Insert
        if($nNeu==0||strpos($sAltNr,';'.$nNeu.';')) $sZl=','.(++$nNxt);
        else $sZl=','.$nNeu; $sAltNr.=$nNeu.';';
        $sZl.=',"'.($aFPos[1]>=0?sprintf('%0d',$aZl[$aFPos[1]]):'0').'"'; //Aktiv
        $sZl.=',"'.($aFPos[2]>=0?sprintf('%0d',$aZl[$aFPos[2]]):'0').'"'; //Umfrage
        $sF=''; for($i=1;$i<=$nAntwAnzahl;$i++) $sF.=',Antwort'.$i;
        for($i=3;$i<$nAntwAnzahl+7;$i++) $sZl.=',"'.(isset($aFPos[$i])&&($aFPos[$i]>=0)&&isset($aZl[$aFPos[$i]])?str_replace('\n ',"\r\n",str_replace('"',"'",str_replace('`,',';',trim($aZl[$aFPos[$i]])))):'').'"';
        $sQ='INSERT IGNORE INTO '.UMF_SqlTabF.' (Nummer,aktiv,Umfrage,Frage,Bild,Anmerkung1,Anmerkung2'.$sF.') VALUES('.substr($sZl,1).')';
       }
       if($DbO->query($sQ)){
        $nImp++; if($aFPos[4]>=0) if(isset($aZl[$aFPos[4]])&&$aZl[$aFPos[4]]) $bWrnBld=true;
       }else $sMeld='<p class="admFehl">Nicht jeder Datensatz konnte eingefügt werden!</p>';
      }//while
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
    }else $sMeld='<p class="admFehl">Es werden mindestens die Felder <i>Frage</i> und 2x <i>Antwort</i> erwartet!</p>';
   }else $sMeld='<p class="admFehl">unbekanntes Feld: '.substr($sFFeld,2).'</p>';
   fclose($fi);
  }else $sMeld='<p class="admFehl">Die hochgeladenen Datei konnte nicht geöffnet werden!</p>';
 }else $sMeld='<p class="admFehl">Es sind nur Dateien mit der Endung <i>txt</i> oder <i>csv</i> erlaubt!</p>';
 if(empty($sMeld)){
  $sMeld='<p class="admErfo">Es wurden '.$nImp.' Fragedatensätze importiert!</p>';
  if($bWrnBld) $sMeld.='<p class="admMeld">Bei den Fragen mit Bild wurden die Bilder jedoch <i>nicht</i> hochgeladen!</p>';
 }else $sMeld='<p class="admFehl">Datenimport nicht durchgeführt:</p>'.$sMeld;
}else{ //GET
 $sMeld='<p class="admMeld">Stellen Sie die Daten für den Import zusammen.</p>';
}
echo $sMeld.NL;

if(!$bUploadOK){ //GET oder falscher Pfad
?>

<form action="fragenImport.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">
Laden Sie die Datei der zu importierenden Fragen hoch.
Es werden nur Dateien mit der Endung <i>.txt</i> oder <i>.csv</i> akzeptiert.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Importdatei</td>
 <td><input class="admEing" type="file" name="Dat" size="80" style="width:98%" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Feldtrenner</td>
 <td><select class="admEing" name="Sep" style="width:50px;">
   <option value=";"<?php if($sSep==';') echo ' selected="selected"'?>>;</option>
   <option value=","<?php if($sSep==',') echo ' selected="selected"'?>>,</option>
   <option value="t"<?php if($sSep=="\t") echo ' selected="selected"'?>>TAB</option>
  </select>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Überschreiben</td>
 <td><input type="checkbox" class="admCheck" name="ersatz" value="1<?php if($bErsatz) echo '" checked="checked';?>" /> Fragen mit gleicher Fragennummer überschreiben</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Nummerierung</td>
 <td><input type="checkbox" class="admCheck" name="origin" value="1<?php if($bOrigin) echo '" checked="checked';?>" /> Originalnummern der Fragen aus der Importdatei beibehalten</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>
</form><br />

<?php }?>

<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2"><p>Hinweise:</p>
<p>Die zu importierenden Daten müssen im einfachen CSV-Format vorliegen.
Jeder zu importierende Fragendatensatz entspricht einer Zeile dieser Datei.
Die Datenfelder müssen üblicherweise durch ein Semikolon ; voneinander getrennt sein.
Alternativ kann ein anderes Trennzeichen (z.B. Komma oder Tabulator) angewandt werden, sofern dieses oben eingestellt wird.</p>
<p>Die erste Zeile der Importdatei muss die Datenfeldnamen in der gleichen Bezeichnung enthalten,
die in der Fragenstruktur dieses Scripts verwendet werden. Das sind:</p>
<pre style="margin:3px;">Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2;
  Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Antwort10;....</pre>
<p style="margin-top:6px;">Dabei dürfen jedoch Felder fehlen. Wenn es in der Importdatei kein Feld
namens <i>Nummer</i>, <i>aktiv</i>, <i>Umfrage</i> oder <i>Anmerkung2</i>
bzw. <i>Antwort8</i>, <i>Antwort9</i>, <i>Antwort10</i>, <i>Antwort11</i> usw. gibt so wird das beim Import automatisch berücksichtigt.</p>
<p>Die Fragen werden kumulativ importiert.
Es wird <i>nicht</i> geprüft, ob eine vergleichbare Frage bereits existiert.
Mehrfacher Import führt somit zwangsläufig zu mehrfachen Datensätzen.
Doppelt vergebene Fragenummern führen zu Problemen, verzichten Sie dann lieber ganz auf Fragenummern.
Sichern Sie eventuell vor dem Import Ihre bisherige Fragentabelle!</p>
</td></tr>
</table>

<?php echo fSeitenFuss();?>