<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Daten aus Version 2.x importieren','','Im2');

$bUploadOK=false; $sAltAdr='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 if($sAltAdr=(isset($_POST['AltAdr'])?stripslashes(trim($_POST['AltAdr'])):'')){
  if(substr($sAltAdr,-1,1)!='/'&&substr($sAltAdr,-1,1)!="\\") $sAltAdr.='/'; $nAltZhl=0;
  if(!$p=strpos($sAltAdr,'://')){ //local
   if($aD=@file($sAltAdr.'daten/daten.txt')) $nAltZhl=count($aD); //lokale Datei gelesen
   else $Msg='<p class="admFehl">Unter <i>'.$sAltAdr.'daten/daten.txt</i> kann keine Datei gelesen werden!</p>';
  }else{ //remote
   $s=substr($sAltAdr,$p+3); $errNo=0; $errStr='';
   if($p=strpos($s,'/')){$sAltH=substr($s,0,$p); $sAltP=substr($s,$p);}else{$sAltH=$s; $sAltP='/';}
   if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
    fputs($Sck,'GET '.$sAltP."daten/daten.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
    while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
    if($aD=explode("\n",str_replace("\r",'',trim(strstr($s,"\r\n\r\n"))))) $nAltZhl=count($aD);
    else $Msg='<p class="admFehl">Unter <i>http://'.$sAltH.$sAltP.'daten/daten.txt</i> kann derzeit keine Datei gelesen werden!</p>';
   }else $Msg='<p class="admFehl">Unter <i>http://'.$sAltH.'</i> kann derzeit keine Datei geöffnet werden!</p><p>'.$errNo.' '.$errStr.'</p>';
  }
  if($nAltZhl>0){
   $aHd=explode(';',$aD[0]); $nFlds=count($aHd); for($i=0;$i<19;$i++) $aFPos[$i]=-1;
   $aFPos=array(); $nNr=0;  $nImp=0; $sFFeld=''; $aBld=array();
   for($i=0;$i<$nFlds;$i++){
    $s=str_replace('-','',str_replace('_','',str_replace(' ','',trim($aHd[$i])))); $t=strtolower(substr($s,0,4));
    switch($t){
     case 'nr': $aFPos[0]=$i; break; case 'antw': if($k=(int)substr($s,4,1)){$aFPos[7+$k]=$i; $nNr++;} break;
     case 'kate': $aFPos[3]=$i; break; case 'frag': $aFPos[4]=$i; $nNr++; break; case 'loes': $aFPos[5]=$i; break;
     case 'punk': $aFPos[6]=$i; break; case 'bild': $aFPos[7]=$i; break; case 'anme': $aFPos[17]=$i; break;
     case 'akti': $aFPos[1]=$i; break; break; default: $sFFeld.=', '.htmlentities($aHd[$i],ENT_COMPAT,'ISO-8859-1');
    }
   }
   if(empty($sFFeld)){//keine unbekanntes Feld
    if($nNr>2){//mindestens Frage und 2 Antworten
     if(!FRA_SQL){//Text
      $aDat=array('*'); $nNr=0; $aNr=array(0);
      for($j=1;$j<$nAltZhl;$j++){
       $aZl=explode(';',$aD[$j]);
       if($aFPos[0]>=0){if(!$sZl=(int)$aZl[$aFPos[0]]) $sZl=++$nNr;} else $sZl=++$nNr; if(in_array($sZl,$aNr)) $sZl=++$nNr;
       $aNr[]=$sZl; if($aZl[$aFPos[7]]) $aBld[$sZl]=$aZl[$aFPos[7]];
       $sZl.=';'.($aFPos[1]>=0?sprintf('%0d',$aZl[$aFPos[1]]):'0'); $sZl.=';'.($aFPos[2]>=0?sprintf('%0d',$aZl[$aFPos[2]]):'0');
       for($i=3;$i<19;$i++) $sZl.=';'.(isset($aFPos[$i])&&($aFPos[$i]>=0)?str_replace('"',"'",trim($aZl[$aFPos[$i]])):''); $aDat[]=$sZl; $nImp++;
      }
      asort($aNr); reset($aNr);
      if($f=@fopen(FRA_Pfad.FRA_Daten.FRA_Fragen,'w')){//neu schreiben
       fwrite($f,'Nummer;aktiv;versteckt;Kategorie;Frage;Loesung;Punkte;Bild;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Anmerkung;Anmerkung2'."\n");
       foreach($aNr as $i=>$xx) if($i>0) fwrite($f,rtrim($aDat[$i])."\n"); fclose($f);
      }else $Msg=str_replace('#','<i>'.FRA_Daten.FRA_Fragen.'</i>',FRA_TxDateiRechte);
     }elseif($DbO){//SQL
      $DbO->query('DELETE FROM '.FRA_SqlTabF); $sNr='#;';
      for($j=1;$j<$nAltZhl;$j++){
       $aZl=explode(';',$aD[$j]); $sZl=''; $bNum=false;
       if($aFPos[0]>=0) if($sZl=(int)$aZl[$aFPos[0]]) if(!strpos($sNr,';'.$sZl.';')){$sNr.=$sZl.';'; $sZl=','.$sZl; $bNum=true;} else $sZl='';
       $sZl.=',"'.($aFPos[1]>=0?sprintf('%0d',$aZl[$aFPos[1]]):'0').'"'; $sZl.=',"'.($aFPos[2]>=0?sprintf('%0d',$aZl[$aFPos[2]]):'0').'"';
       for($i=3;$i<19;$i++) $sZl.=',"'.(isset($aFPos[$i])&&($aFPos[$i]>=0)?str_replace('\n ',"\r\n",str_replace('"',"'",str_replace('`,',';',trim($aZl[$aFPos[$i]])))):'').'"';
       if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabF.' ('.($bNum?'Nummer,':'').'aktiv,versteckt,Kategorie,Frage,Loesung,Punkte,Bild,Antwort1,Antwort2,Antwort3,Antwort4,Antwort5,Antwort6,Antwort7,Antwort8,Antwort9,Anmerkung) VALUES('.substr($sZl,1).')')){
        $nImp++; if($aZl[$aFPos[7]]) $aBld[$DbO->insert_id]=$aZl[$aFPos[7]];
       }else $sMeld='<p class="admFehl">Nicht jeder Datensatz konnte eingefügt werden!</p>';
      }
     }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
    }else $sMeld='<p class="admFehl">Es werden mindestens die Felder <i>Frage</i> und 2x <i>Antwort</i> erwartet!</p>';
   }else $sMeld='<p class="admFehl">Die Kopfzeile enthält unbekannte Felder wie: <i>'.substr($sFFeld,2).'</i></p>';
  }else $sMeld='<p class="admFehl">Aus der Datei <i>'.$sAltAdr.'daten/daten.txt</i> kann nichts gelesen werden!</p>';
 }else $sMeld='<p class="admFehl">Geben Sie den Ort des alten Frage-Antwort-Scripts (fas) Version 2.x an!</p>';
 if(empty($sMeld)){
  $sMeld='<p class="admErfo">Es wurden '.$nImp.' von '.(--$nAltZhl).' Fragedatensätzen importiert!</p>';
  if($f=opendir(FRA_Pfad.substr(FRA_Bilder,0,-1))){ //alte Bilder löschen
   $a=array(); while($s=readdir($f)) if($s!='.'&&$s!='..'&&$s!='index.html'&&$s!=FRA_BildErsatz) $a[]=$s; closedir($f);
   foreach($a as $s) @unlink(FRA_Pfad.FRA_Bilder.$s);
  }
  if(count($aBld)){
   if(!FRA_SQL) $sDat=implode('',file(FRA_Pfad.FRA_Daten.FRA_Fragen)); else $sDat='';
   foreach($aBld as $k=>$s){
    $sEx=strtolower(strrchr($s,'.')); $sNam=substr($s,0,-1*strlen($sEx));
    if($sEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($sAltAdr.'daten/'.$s);
    elseif($sEx=='.gif') $Src=ImageCreateFromGIF($sAltAdr.'daten/'.$s);
    elseif($sEx=='.png') $Src=ImageCreateFromPNG($sAltAdr.'daten/'.$s);
    if(!empty($Src)){
     $Sx=ImageSX($Src); $Sy=ImageSY($Src);
     if($Sx>FRA_BildW||$Sy>FRA_BildH){ //Bild verkleinern
      $Dw=min(FRA_BildW,$Sx);
      if($Sx>FRA_BildW) $Dh=round(FRA_BildW/$Sx*$Sy); else $Dh=$Sy;
      if($Dh>FRA_BildH){$Dw=round(FRA_BildH/$Dh*$Dw); $Dh=FRA_BildH;}
      $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
      ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
      if(!@imagejpeg($Dest,FRA_Pfad.FRA_Bilder.$sNam.'.jpg')) $bFehlB=true;
      imagedestroy($Dest); unset($Dest);
      if(!FRA_SQL){
       $sDat=substr_replace($sDat,'.jpg',strpos($sDat,';'.$s.';')+strlen($sNam)+1,strlen($sEx)); $bBldUpd=true;
      }elseif($bSQLOpen){
       $DbO->query('UPDATE IGNORE '.FRA_SqlTabF.' SET Bild="'.$sNam.'.jpg" WHERE Nummer="'.$k.'"');
      }
     }else{if(!@copy($sAltAdr.'daten/'.$s,FRA_Pfad.FRA_Bilder.$s)) $bFehlB=true;}
     imagedestroy($Src); unset($Src);
    }else{$bFehlB=true;}
   }
   if($bFehlB) $sMeld.='<p class="admMeld">Es konnten <i>nicht</i> alle Bilder umgespeichert werden.</p>';
   elseif($bBldUpd&&!FRA_SQL) if($f=@fopen(FRA_Pfad.FRA_Daten.FRA_Fragen,'w')){fwrite($f,$sDat); fclose($f);} //neu schreiben
  }
 }else $sMeld='<p class="admFehl">Datenimport nicht durchgeführt:</p>'.$sMeld;
}else{ //GET
 $sMeld='<p class="admMeld">Stellen Sie die Daten für den Import zusammen.</p>';
}

echo $sMeld.NL;

if(!$bUploadOK){ //GET oder falscher Pfad
?>

<form action="importVer2x.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">
Tragen Sie den Ort des alten Frage-Antwort-Scripts Version 2.x als absoluten lokalen Pfad oder als fernen URL ein.
Ein lokaler Pfad könnte beispielsweise lauten: <i>/var/kunden/webs/www.ein-web.de/htdocs/fas</i>,
eine ferne Adresse beispielsweise <i>http://www.ein-web.de/fas</i>
</td></tr>
<tr class="admTabl">
 <td style="width:12%">Importadresse</td>
 <td><input class="admEing" style="width:99%" type="text" name="AltAdr" value="<?php echo $sAltAdr?>" /></td>
</tr>
<tr class="admTabl"><td class="admSpa2" colspan="2"><u>Information</u>: Diese Installation des Testfragen-Scripts liegt im absoluten Pfad
<i><?php echo substr(FRA_Pfad,0,-1)?></i> bzw. unter der Adresse <i>http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo substr(FRA_Www,0,-1)?></i>.
</td></tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>
</form><br />

<?php }?>

<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2"><p>Hinweise:</p>
<p>Die Fragendatei kann aus einem lokalen Pfad des selben Servers oder von einem fernen Server importiert werden.
Beim Import vom selben Server ist der absolute Pfad anzugeben.
Der Import vom fernen Server erfolgt über den URL des alten Frage-Antwort-Scripts.
Ein lokaler Import ist stets vorzuziehen.</p>
<p>Bei einem Import von Fragen aus der Version 2.x werden die alten Fragen und Antworten übernommen.
Die momentane Fragendatei in Version 3.x wird überschrieben
und alle eventuell vorhandenen Fragen sowie Bilder der Version 3.x werden gelöscht.
Eventuell gespeicherte Testergebnisse aus der Version 2.x werden <i>nich</i>t übernommen.</p>
<p>Es werden nur die Fragen und Antworten importiert,
<i>nicht</i> die Einstellungen zum Testablauf, zu den Farben oder zum Layout.
Setzen Sie also z.B. die Parameter für die Bildgröße <i>vor</i> dem Import auf gewünschte Werte.</p>
<p>Falls Ihr Frage-Antwort-Script in der Version 2.x sehr viele Bilder enthält kann der Import je nach Leistung des Servers länger dauern.
Auf üblichen Servern wird aber die Ausführungszeit für ein PHP-Script vom Provider auf meist maximal 30 Sekunden begrenzt.
Überschreitet die Importdauer dieses Zeitlimit, wird Ihr Server den Import abbrechen.
Verluste an den Daten dieser Version 3.x sind bei solch einem unkontrollierten Abbruch nicht auszuschließen!
Die Daten des alten Scripts Version 2.x werden jedoch keinesfalls beschädigt.
Das Risiko ist also nicht allzu hoch.</p>
</td></tr>
</table>

<?php echo fSeitenFuss();?>