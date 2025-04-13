<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('alte Umfrage-Fragen importieren','','Z31');

$bUploadOK=false; $sSep=''; $bWrnBld=false; $bErsatz=true; $bOrigin=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);
if($_SERVER['REQUEST_METHOD']=='POST'){
 $ImpNa=str_replace(' ','_',basename($_FILES['Dat']['name'])); $ImpEx=strtolower(strrchr($ImpNa,'.'));
 $sSep=(isset($_POST['Sep'])?$_POST['Sep']:''); if($sSep=='t') $sSep="\t";
 if($ImpEx=='.txt'||$ImpEx=='.csv'){
  if($fi=fopen($_FILES['Dat']['tmp_name'],'r')){
   $nNr=0; $nImp=0; $sFFeld=''; $aFPos=array(); for($i=0;$i<25;$i++) $aFPos[$i]=-1;
   $aHd=fgetcsv($fi,16000,$sSep); $nFlds=count($aHd);
   for($i=0;$i<$nFlds;$i++){
    $s=str_replace('-','',str_replace('_','',str_replace(' ','',trim($aHd[$i])))); $t=strtolower(substr($s,0,7));
    switch($t){
     case 'frage': $aFPos[0]=$i; $nNr++; break; case 'bild': $aFPos[10]=$i; break; case 'anmerku': $aFPos[11]=$i; break;
     case 'antwort':if($k=(int)substr($s,7,2)){$aFPos[$k]=$i; $nNr++;} break;
     default: $sFFeld.=', '.$aHd[$i];
    }
   }
   if(empty($sFFeld)){//keine unbekanntes Feld
    if($nNr>2){//mindestens Frage und 2 Antworten
     if(!UMF_SQL){//Text
      $aDat=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nNr=0;
      $s='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'; for($i=1;$i<=$nAntwAnzahl;$i++) $s=';Antwort'.$i; $s.="\n";
      $aDat[0]=$s; $nDat=count($aDat); $aDat[$nDat-1]=trim($aDat[$nDat-1])."\n";
      for($i=1;$i<$nDat;$i++) $nNr=max((int)substr($aDat[$i],0,6),$nNr);
      while($aZl=fgetcsv($fi,16000,$sSep)){
       $sZl=';1;';
       $sZl.=';'.(isset($aFPos[0])&&isset($aZl[$aFPos[0]])?str_replace('"',"'",trim($aZl[$aFPos[0]])):'');  //Frage
       $sZl.=';'.(isset($aFPos[10])&&isset($aZl[$aFPos[10]])?str_replace('"',"'",trim($aZl[$aFPos[10]])):''); //Bild
       $sZl.=';'.(isset($aFPos[11])&&isset($aZl[$aFPos[11]])?str_replace('"',"'",trim($aZl[$aFPos[11]])):''); //Anmerkung
       $sZl.=';'; // Anmerkung2
       for($i=1;$i<=$nAntwAnzahl;$i++) $sZl.=';'.(isset($aFPos[$i])&&($aFPos[$i]>0)?(isset($aZl[$aFPos[$i]])?str_replace('"',"'",trim($aZl[$aFPos[$i]])):''):'');
       $aDat[]=(++$nNr).$sZl."\n"; $nImp++; if($aFPos[10]>=0) if($aZl[$aFPos[10]]) $bWrnBld=true;
      }
      if($f=@fopen(UMF_Pfad.UMF_Daten.UMF_Fragen,'w')){ //neu schreiben
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aDat)))."\n"); fclose($f);
      }else $Msg=str_replace('#','<i>'.UMF_Daten.UMF_Fragen.'</i>',UMF_TxDateiRechte);
     }elseif($DbO){ //SQL
      $sZlNr=0;
      if($rR=$DbO->query('SELECT MAX(Nummer) FROM '.UMF_SqlTabF)){
       if($a=$rR->fetch_row()) $sZlNr=(int)$a[0]; $rR->close();
      }
      while($aZl=fgetcsv($fi,16000,$sSep)){
       $sZl='"'.(++$sZlNr).'","1';
       $sZl.='","'.(isset($aFPos[0])&&isset($aZl[$aFPos[0]])?str_replace('"',"'",trim($aZl[$aFPos[0]])):'');  //Frage
       $sZl.='","'.(isset($aFPos[10])&&isset($aZl[$aFPos[10]])?str_replace('"',"'",trim($aZl[$aFPos[10]])):''); //Bild
       $sZl.='","'.(isset($aFPos[11])&&isset($aZl[$aFPos[11]])?str_replace('"',"'",trim($aZl[$aFPos[11]])):''); //Anmerkung
       for($i=1;$i<10;$i++) $sZl.='","'.(isset($aFPos[$i])&&($aFPos[$i]>0)?(isset($aZl[$aFPos[$i]])?str_replace('"',"'",trim($aZl[$aFPos[$i]])):''):'');
       if($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabF.' (Nummer,aktiv,Frage,Bild,Anmerkung1,Antwort1,Antwort2,Antwort3,Antwort4,Antwort5,Antwort6,Antwort7,Antwort8,Antwort9) VALUES('.$sZl.'")')){
        $nImp++; if($aFPos[10]>=0) if($aZl[$aFPos[10]]) $bWrnBld=true;
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

<form action="importV31.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
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
die in der Fragenstruktur des Umfrage-Scripts 3.1 verwendet wurden. Das sind:</p>
<pre style="margin:3px;">Frage;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Bild;Anmerkung</pre>
<p style="margin-top:6px;">Die Fragen werden kumulativ importiert.
Es wird <i>nicht</i> geprüft, ob eine vergleichbare Frage bereits existiert.
Mehrfacher Import führt somit zwangsläufig zu mehrfachen Datensätzen.</p>
</td></tr>
</table>

<?php echo fSeitenFuss();?>