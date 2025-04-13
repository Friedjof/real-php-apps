<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserate-Export','<script type="text/javascript">function selSeg(){document.ExForm.Src[0].checked=true;}</script>','Exp');

if(!MP_Pfad) $Meld='Bitte zuerst die Pfade im Setup einstellen!';
for($i=59;$i>=0;$i--){
 if(file_exists(MP_Pfad.'temp/inserate_'.sprintf('%02d',$i).'.csv')) unlink(MP_Pfad.'temp/inserate_'.sprintf('%02d',$i).'.csv');
 if(file_exists(MP_Pfad.'temp/inserate_'.sprintf('%02d',$i).'.zip')) unlink(MP_Pfad.'temp/inserate_'.sprintf('%02d',$i).'.zip');
}

$nSeg=0; $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aSortSeg=array(); $sSrc='Zip';
$aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); asort($aA); reset($aA);
foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER') $aSortSeg[]=$k;

if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sSrc=(isset($_POST['Src'])?$_POST['Src']:'Zip'); $sEx=''; $bZipOk=false;
 if($sSrc=='Seg'){$nSeg=(isset($_POST['ExS'])?(int)$_POST['ExS']:0); $aSortSeg=array($nSeg);}
 elseif($sSrc=='Zip'){
  $zip=new ZipArchive; $sExNa='inserate_'.date('s').'.zip';
  if($zip->open(MP_Pfad.'temp/'.$sExNa,ZipArchive::CREATE)===true) $bZipOk=true;
  else $Meld='Die Datei <i>temp/'.$sExNa.'</i> konnte nicht angelegt werden';
 }

 if($nSeg==0&&$sSrc=='Seg') $Meld='Es ist kein Segment zum Export ausgewählt.';
 else foreach($aSortSeg as $n){
  $sSg=sprintf('%02d',$n);
  if(count($aStru)<=0||$sSrc=='Zip'){ //Struktur holen
   if(!MP_SQL){//Text
    $aStru=file(MP_Pfad.MP_Daten.$sSg.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
   }elseif($DbO){//SQL
    if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSg.'"')){
     $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
     if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
    }else $Meld=MP_TxSqlFrage;
   }else $Meld=MP_TxSqlVrbdg;
   $sEx='Nummer'; for($i=1;$i<$nFelder;$i++) $sEx.=';'.$aFN[$i]; $sEx.=NL; //Kopfzeile 1
  }

  if(!MP_SQL){ //Textdaten holen
   $aD=file(MP_Pfad.MP_Daten.$sSg.MP_Inserate); $nSaetze=count($aD);
   for($j=1;$j<$nSaetze;$j++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aD[$j])); $sEx.=($sSrc!='Sum'?'':$sSg.'_').$a[0]; array_splice($a,1,1);
    for($i=1;$i<$nFelder;$i++){
     $s=(isset($a[$i])?str_replace('`,',',',$a[$i]):''); $t=$aFT[$i];
     if(strlen($s)>0) switch($t){
      case 'd': if(strlen($s)>0) $s=fMpAnzeigeDatum($s); //Datum
       break;
      case '@': if(strlen($s)>0) $s=fMpAnzeigeDatum($s).', '.substr($s,11,5); //Eintragsdatum
       break;
      case 'w': $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen); //Währung
       break;
      case 'e': case 'c': $s=fMpDeCode($s); //eMail und Kontakt
       break;
      case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=MP_TxJa; elseif($s=='N') $s=MP_TxNein;
       break;
      case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,'');
       else $s=str_replace('.',MP_Dezimalzeichen,$s);
       break;
     }
     $sEx.=';'.$s;
    }$sEx.=NL;
   }
  }else{ //SQL
   $sF=''; $sS=''; for($i=1;$i<$nFelder;$i++){$sF.=',mp_'.$i; if($i<4) $sS.='mp_'.$i.',';}
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSg,MP_SqlTabI).' WHERE online="1" ORDER BY '.$sS.'nr')){
    while($a=$rR->fetch_row()){
     $sEx.=($sSrc!='Sum'?'':$sSg.'_').$a[0]; array_splice($a,1,1);
     for($i=1;$i<$nFelder;$i++){
      $s=(isset($a[$i])?str_replace(';',',',$a[$i]):''); $t=$aFT[$i];
      if(strlen($s)>0) switch($t){
       case 't': case 'm': $s=str_replace("\r\n",'\n ',$s); //Memo
        break;
       case 'd': if(strlen($s)>0) $s=fMpAnzeigeDatum($s); //Datum
        break;
       case '@': if(strlen($s)>0) $s=fMpAnzeigeDatum($s).', '.substr($s,11,5); //Eintragsdatum
        break;
       case 'w': $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen); //Währung
        break;
       case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
        if($s=='J') $s=MP_TxJa; elseif($s=='N') $s=MP_TxNein;
        break;
       case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
        break;
       case 'n': case '1': case '2': case '3': case 'r': //Zahl
        if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,'');
        else $s=str_replace('.',MP_Dezimalzeichen,$s);
        break;
      }
      $sEx.=';'.$s;
     }$sEx.=NL;
    }$rR->close();
   }else $Meld=MP_TxSqlFrage;
  }//SQL
  if($bZipOk) $zip->addFromString($sSg.'segment.txt',$sEx);
 }//foreach

 if($bZipOk){
  $zip->close(); $MTyp='Erfo';
  $Meld='Die Exportdatei liegt unter <a href="'.MPPFAD.'temp/'.$sExNa.'" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.';
 }else if(empty($Meld)){
  $sExNa='inserate_'.date('s').'.csv';
  if($f=fopen(MP_Pfad.'temp/'.$sExNa,'w')){
   fwrite($f,$sEx); fclose($f); $MTyp='Erfo';
   $Meld='Die Exportdatei liegt unter <a href="'.MPPFAD.'temp/'.$sExNa.'" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.';
  }else $Meld=str_replace('#','<i>temp/'.$sExNa.'</i>',MP_TxDateiRechte);
 }

}//POST
if(empty($Meld)){$Meld='Wählen Sie die Datenquelle und das Format der zu exportierenden Daten aus.'; $MTyp='Meld';}
echo '<p class="admSubmit">Segment '.$nSegNo.': '.$sSegNam.'</p>';
echo '<p class="adm'.$MTyp.'" style="margin-top:32px;text-align:center;">'.$Meld.'</p>'.NL;

reset($aA); $sSegOpt='';
foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER') $sSegOpt.='<option value="'.$k.($k!=$nSeg?'':'" selected="selected').'">'.(substr($aS[$k],0,1)!='~'&&substr($aS[$k],0,1)!='*'?$aS[$k]:substr($aS[$k],1)).'</option>';

?>

<form name="ExForm" action="export.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<input type="hidden" name="ExSeg" value="<?php echo 1?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td><input class="admRadio" type="radio" name="Src<?php if($sSrc=='Seg') echo '" checked="checked'?>" value="Seg" /> Daten des Segments <select name="ExS" style="font-size:96%" onchange="selSeg()" size="1"><option value="">-----</option><?php echo $sSegOpt?></select> als CSV-Datei</td></tr>
<tr class="admTabl"><td><input class="admRadio" type="radio" name="Src<?php if($sSrc=='Zip') echo '" checked="checked'?>" value="Zip" /> Daten aller Segmente als CSV-Dateien, gepackt in ein ZIP-Archiv</td></tr>
<tr class="admTabl"><td><input class="admRadio" type="radio" name="Src<?php if($sSrc=='Sum') echo '" checked="checked'?>" value="Sum" /> Daten aller Segmente als eine Gesamt-CSV-Datei mit der Struktur des ersten Segments</td></tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Export"></p>
</form>

<?php
echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 return true;
}
?>