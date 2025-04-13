<?php
include 'hilfsFunktionen.php'; // dieses Admin-Script ausnahmsweise in UTF-8
$bCKEd=(defined('ADF_CKEditor')&&ADF_CKEditor?true:false);
echo iconv('ISO-8859-1','UTF-8//TRANSLIT',fSeitenKopf('neue Frage eingeben','<script src="'.($bCKEd?'ckeditor':'eingabe').'.js" type="text/javascript"></script>','FFe','UTF-8'));

$sMs2=''; $bDo=false; $Frg=''; $Lsg=''; $FNr=''; $Onl=''; $Vst=''; $Kat=''; $Pkt=''; $Bld=''; $Vid=$VidF=$VidW=$VidH='';
$Aw1=''; $Aw2=''; $Aw3=''; $Aw4=''; $Aw5=''; $Aw6=''; $Aw7=''; $Aw8=''; $Aw9=''; $Bem=''; $B2m='';
$Pt1=''; $Pt2=''; $Pt3=''; $Pt4=''; $Pt5=''; $Pt6=''; $Pt7=''; $Pt8=''; $Pt9='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $sNd=''; $bDo=false;
 if(!$FNr=(isset($_POST['FNr'])?(int)$_POST['FNr']:'')) $FNr='';
 $Onl=sprintf('%0d',(isset($_POST['onl'])?$_POST['onl']:0)); $Vst=sprintf('%0d',(isset($_POST['vst'])?$_POST['vst']:0));
 if(!FRA_MehrfachKat) $Kat=(isset($_POST['Kat'])?$_POST['Kat']:'');
 else{$aKat=(isset($_POST['Kat'])?$_POST['Kat']:array()); $Kat=''; if(is_array($aKat)) foreach($aKat as $v) $Kat.=$v.', '; $Kat=substr($Kat,0,-2);}
 if(!$Frg=fFraEingabe('Frg')) $sNd.=', Frage'; elseif(substr($Frg,0,3)=='<p>'&&($p=strpos($Frg,'</p>'))) $Frg=substr(substr_replace($Frg,'',$p,4),3); $Kat=iconv('UTF-8','ISO-8859-1',$Kat);
 if(!$Lsg=str_replace('.',',',str_replace(';',',',str_replace(' ','',trim($_POST['Lsg']))))) $sNd.=', L�sung';
 if(FRA_PunkteTeilen) if(!$Pkt=(int)$_POST['Pkt']) $sNd.=', Punkte';
 if(!$Aw1=fFraEingabe('Aw1')) if(!FRA_OhneAntwort) $sNd.=', Antwort-1'; if(!$Aw2=fFraEingabe('Aw2')) if(!FRA_OhneAntwort) $sNd.=', Antwort-2';
 $Aw3=fFraEingabe('Aw3'); $Aw4=fFraEingabe('Aw4'); $Aw5=fFraEingabe('Aw5'); $Aw6=fFraEingabe('Aw6');
 $Aw7=fFraEingabe('Aw7'); $Aw8=fFraEingabe('Aw8'); $Aw9=fFraEingabe('Aw9'); $Bem=fFraEingabe('Bem'); $B2m=fFraEingabe('B2m');
 if(!FRA_PunkteTeilen){
  $Pt1=(int)fFraEingabe('Pt1'); $Pt2=(int)fFraEingabe('Pt2'); $Pt3=(int)fFraEingabe('Pt3');
  $Pt4=(int)fFraEingabe('Pt4'); $Pt5=(int)fFraEingabe('Pt5'); $Pt6=(int)fFraEingabe('Pt6');
  $Pt7=(int)fFraEingabe('Pt7'); $Pt8=(int)fFraEingabe('Pt8'); $Pt9=(int)fFraEingabe('Pt9');
  if(!empty($Aw1)) if(strlen($Pt1)<=0) $sNd.=', Punkte-1';
  if(!empty($Aw2)) if(strlen($Pt2)<=0) $sNd.=', Punkte-2';
  if(!empty($Aw3)) if(strlen($Pt3)<=0) $sNd.=', Punkte-3';
  if(!empty($Aw4)) if(strlen($Pt4)<=0) $sNd.=', Punkte-4';
  if(!empty($Aw5)) if(strlen($Pt5)<=0) $sNd.=', Punkte-5';
  if(!empty($Aw6)) if(strlen($Pt6)<=0) $sNd.=', Punkte-6';
  if(!empty($Aw7)) if(strlen($Pt7)<=0) $sNd.=', Punkte-7';
  if(!empty($Aw8)) if(strlen($Pt8)<=0) $sNd.=', Punkte-8';
  if(!empty($Aw9)) if(strlen($Pt9)<=0) $sNd.=', Punkte-9';
  $Pkt=$Pt1+$Pt2+$Pt3+$Pt4+$Pt5+$Pt6+$Pt7+$Pt8+$Pt9;
 }
 $Bld=$_POST['BlU']; //hochgeladenes Bild
 if(isset($_POST['BlD'])&&$_POST['BlD']){@unlink(FRA_Pfad.'temp/'.$Bld); $Bld='';} //temp Bild loeschen
 $ImN=(isset($_FILES['Bld'])?strtolower(fFraDateiname(basename($_FILES['Bld']['name']))):''); $ImE=strrchr($ImN,'.');
 if($ImE=='.jpg'||$ImE=='.gif'||$ImE=='.jpeg'||$ImE=='.png'){
  $i=$_FILES['Bld']['size'];
  if($i<=1024*FRA_BildKB){
   $aIm=@getimagesize($_FILES['Bld']['tmp_name']);
   if($aIm[0]<=FRA_BildW&&$aIm[1]<=FRA_BildH){ //direkt speichern
    if(copy($_FILES['Bld']['tmp_name'],FRA_Pfad.'temp/'.$ImN)){
     if($Bld) if($Bld!=$ImN) @unlink(FRA_Pfad.'temp/'.$Bld); $Bld=$ImN; //vorheriges Bild weg
    }else $sMs2='<p class="admFehl">'.str_replace('#','temp/'.$ImN,FRA_TxDateiRechte).'</p>';
   }else{//verkleinern
    if($ImE=='.jpg'||$ImE=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['Bld']['tmp_name']);
    elseif($ImE=='.gif') $Src=ImageCreateFromGIF($_FILES['Bld']['tmp_name']);
    elseif($ImE=='.png') $Src=ImageCreateFromPNG($_FILES['Bld']['tmp_name']);
    if($Src){
     $ImN=substr($ImN,0,-strlen($ImE)).'.jpg'; $Sx=ImageSX($Src); $Sy=ImageSY($Src);
     $Dw=min(FRA_BildW,$Sx); if($Sx>FRA_BildW) $Dh=round(FRA_BildW/$Sx*$Sy); else $Dh=$Sy;
     if($Dh>FRA_BildH){$Dw=round(FRA_BildH/$Dh*$Dw); $Dh=FRA_BildH;}
     $Dst=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dst,0,0,ImageColorAllocate($Dst,255,255,255));
     ImageCopyResampled($Dst,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
     if(ImageJPEG($Dst,FRA_Pfad.'temp/'.$ImN)){
      if($Bld) if($Bld!=$ImN) @unlink(FRA_Pfad.'temp/'.$Bld); $Bld=$ImN;
     }else $sMs2='<p class="admFehl">'.str_replace('#','temp/'.$ImN,FRA_TxDateiRechte).'</p>';
     imagedestroy($Dst); imagedestroy($Src); unset($Dst); unset($Src);
    }else $sMs2='<p class="admFehl">Das Bild <i>'.$ImN.'</i> konnte nicht verarbeitet werden!</p>';
   }
  }else $sMs2='<p class="admFehl">Bilder mit <i>'.$i.' KByte</i> Gr��e sind nicht erlaubt!</p>';
 }elseif(substr($ImE,0,1)=='.') $sMs2='<p class="admFehl">Bilder mit der Endung <i>'.$ImE.'</i> sind nicht erlaubt!</p>';
 if($VidF=$_POST['VidF']){$Vid=$VidF; //verlinktes Video
  if(!strpos($VidF,'://')||strpos($VidF,'http')===false) $sMs2='<p class="admFehl">Das Video/Audio hat keine vollst�ndige Adresse!</p>';
  if(!$VidH=(int)$_POST['VidH']) $VidH=''; if($VidW=(int)$_POST['VidW']){$Vid.=' '.$VidW; if($VidH) $Vid.=' '.$VidH;}else $VidW='';
 }
 if(!$sMs2) if(!$sNd){ //alles OK
  if(!FRA_SQL){ //Text
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nCnt=max(count($aD),1);
   $aD[0]='Nummer;aktiv;versteckt;Kategorie;Frage;Loesung;Punkte;Bild;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Anmerkung;Anmerkung2'.NL;
   if($FNr<=0) $FNr=((int)$aD[$nCnt-1])+1; $bSuch=true; $bKorr=false;
   $sNeu=$FNr.';'.$Onl.';'.$Vst.';'.$Kat.';'.fFraTxt($Frg).';'.$Lsg.';'.$Pkt.';'.($Vid?$Vid:$Bld).';'.fFraTxt($Aw1).($Pt1>0?'|#'.$Pt1:'').';'.fFraTxt($Aw2).($Pt2>0?'|#'.$Pt2:'').';'.fFraTxt($Aw3).($Pt3>0?'|#'.$Pt3:'').';'.fFraTxt($Aw4).($Pt4>0?'|#'.$Pt4:'').';'.fFraTxt($Aw5).($Pt5>0?'|#'.$Pt5:'').';'.fFraTxt($Aw6).($Pt6>0?'|#'.$Pt6:'').';'.fFraTxt($Aw7).($Pt7>0?'|#'.$Pt7:'').';'.fFraTxt($Aw8).($Pt8>0?'|#'.$Pt8:'').';'.fFraTxt($Aw9).($Pt9>0?'|#'.$Pt9:'').';'.fFraTxt($Bem).';'.fFraTxt($B2m);
   for($i=1;$i<$nCnt;$i++){
    $sLn=rtrim($aD[$i]); $p=strpos($sLn,';'); $n=(int)substr($sLn,0,$p);
    if($bSuch){ //Stelle noch nicht erreicht
     if($n>=$FNr){
      if($n==$FNr){$sLn=($n+1).substr($sLn,$p); $bKorr=true; $nL=$n+1;} else $nL=$n;
      $aD[$i]=$sNeu.NL.$sLn.NL; $bSuch=false; if(!$bKorr) break;
     }
    }elseif($bKorr) if($n==$nL) $aD[$i]=(++$nL).substr($sLn,$p).NL; else{$bKorr=false; break;} //dahinter Nummern korrigieren
   }
   if($bSuch) $aD[]=$sNeu.NL;
   if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Fragen,'w')){
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    $sMeld='<p class="admErfo">Die neue Frage wurde gespeichert!</p>'; $bDo=true;
   }else $sMeld='<p class="admFehl">'.str_replace('#',FRA_Daten.FRA_Fragen,FRA_TxDateiRechte).'</p>';
  }elseif($DbO){//SQL
   if($bNum=($FNr>0)){
    $aN=array();
    if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabF.' WHERE Nummer>='.$FNr.' ORDER BY Nummer')){
     $a=$rR->fetch_row(); $nL=$a[0];
     if($nL==$FNr){$aN[]=$nL++; while($a=$rR->fetch_row()){$n=$a[0]; if($n==$nL) $aN[]=$nL++; else break;}}
     $rR->close(); $nCnt=count($aN)-1;
     for($i=$nCnt;$i>=0;$i--) $DbO->query('UPDATE IGNORE '.FRA_SqlTabF.' SET Nummer=Nummer+1 WHERE Nummer='.$aN[$i]);
    }
   }
   if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabF.' ('.($bNum?'Nummer,':'').'aktiv,versteckt,Kategorie,Frage,Loesung,Punkte,Bild,Antwort1,Antwort2,Antwort3,Antwort4,Antwort5,Antwort6,Antwort7,Antwort8,Antwort9,Anmerkung,Anmerkung2) VALUES("'.($bNum?$FNr.'","':'').$Onl.'","'.$Vst.'","'.$Kat.'","'.fFraSql($Frg).'","'.$Lsg.'","'.$Pkt.'","'.$Bld.'","'.fFraSql($Aw1).($Pt1>0?'|#'.$Pt1:'').'","'.fFraSql($Aw2).($Pt2>0?'|#'.$Pt2:'').'","'.fFraSql($Aw3).($Pt3>0?'|#'.$Pt3:'').'","'.fFraSql($Aw4).($Pt4>0?'|#'.$Pt4:'').'","'.fFraSql($Aw5).($Pt5>0?'|#'.$Pt5:'').'","'.fFraSql($Aw6).($Pt6>0?'|#'.$Pt6:'').'","'.fFraSql($Aw7).($Pt7>0?'|#'.$Pt7:'').'","'.fFraSql($Aw8).($Pt8>0?'|#'.$Pt8:'').'","'.fFraSql($Aw9).($Pt9>0?'|#'.$Pt9:'').'","'.fFraSql($Bem).'","'.fFraSql($B2m).'")')){
    $FNr=$DbO->insert_id; $sMeld='<p class="admErfo">Die neue Frage wurde gespeichert!</p>'; $bDo=true;
   }else $sMeld='<p class="admFehl">'.FRA_TxSqlEinfg.'</p>';
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
  if($bDo&&$Bld>'') if(copy(FRA_Pfad.'temp/'.$Bld,FRA_Pfad.FRA_Bilder.$Bld)) @unlink(FRA_Pfad.'temp/'.$Bld);
  else $sMs2.='<p class="admFehl">'.str_replace('#',FRA_Bilder.$Bld,FRA_TxDateiRechte).'</p>';
 }else $sMeld='<p class="admFehl">Es fehlen Eintragungen bei'.substr($sNd,1).'!</p>';
}else{$sMeld='<p class="admMeld">Tragen Sie eine neue Frage in die Date'.(!$DbO?'i <i>'.FRA_Fragen:'ntabelle <i>'.FRA_SqlTabF).'</i> ein.</p>'; $Pkt='1'; $Onl='1';}//GET
echo iconv('ISO-8859-1','UTF-8',$sMeld.$sMs2).NL;
?>

<form name="fraEingabe" action="eingabe.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1">Frage-Nr.</td>
  <td>
   <input type="text" name="FNr" value="<?php echo $FNr?>" size="2" style="width:3em;" />
   <span class="admMini">(ohne Angabe einer Nummer wird die Frage automatisch fortlaufend nummeriert)</span>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Fragenstatus</td>
  <td>
   <input type="checkbox" class="admCheck" name="onl" value="1"<?php if($Onl) echo ' checked="checked"'?>> aktiviert
   <span style="width:45px;">&nbsp;</span>
   <input type="checkbox" class="admCheck" name="vst" value="1"<?php if($Vst) echo ' checked="checked"'?>> versteckt
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Frage</td>
  <td>
   <div title="Frage eingeben und dann formatieren"><?php if(!$bCKEd) echo fFraBBToolbar('Frg')?>
   <div><textarea class="admEing" id="Frg" name="Frg" cols="120" rows="6" style="width:99%;height:<?php echo round(1.5*ADF_FragenFeldHoehe,1)?>em;"><?php echo iconv('ISO-8859-1','UTF-8',$Frg)?></textarea></div>
   </div>
  </td>
 </tr><?php if(FRA_Kategorien>''){?><tr class="admTabl">
  <td class="admSpa1">Kategorie<?php if(FRA_MehrfachKat) echo 'n'?></td>
  <td><select name="Kat<?php if(FRA_MehrfachKat) echo'[]'?>" <?php $Kat=iconv('ISO-8859-1','UTF-8',$Kat); $aKat=explode(';',FRA_Kategorien); echo(!FRA_MehrfachKat?'size="1"':'size="'.min(6,count($aKat)+1).'" multiple="multiple"')?>><option value=""></option><?php foreach($aKat as $v=>$k) if(!empty($k)){$k=iconv('ISO-8859-1','UTF-8',str_replace('`,',';',$k)); echo '<option value="'.$k.(strpos($Kat,$k)===false?'':'" selected="selected').'">'.$k.'</option>';}?></select><?php if(FRA_MehrfachKat) echo ' <span class="admMini">(Mehrfachauswahl mit [Strg]-Taste möglich)</span>'?></td>
 </tr><?php } if(FRA_LayoutTyp>0){?><tr class="admTabl">
  <td class="admSpa1">Bild<div class="admMini">max. <?php echo FRA_BildKB?>KB</div><div style="margin-top:16px">oder</div><div>Video/Audio</div></td>
  <td><input type="file" name="Bld" size="80" style="width:99%;" />
  <div style="margin-top:3px"><?php if($Bld){?><input type="checkbox" class="admCheck" name="BlD" value="1" /> Bild <i><?php echo $Bld?></i> löschen<?php }?>&nbsp;</div>
  <div style="margin-top:12px"><input type="text" style="width:60%" name="VidF" value="<?php echo $VidF?>" /> &nbsp; &nbsp; Breite <input type="text" style="width:2.5em" name="VidW" value="<?php echo $VidW?>" /> px &nbsp; Höhe <input type="text" style="width:2.5em" name="VidH" value="<?php echo $VidH?>" /> px</div>
  <div class="admMini"><u>Hinweis</u>:vollständiger URL mit https:// oder http://&nbsp; selbst wenn es eine lokal hochgeladene Datei ist</div></td>
 </tr><?php }?><tr class="admTabl">
  <td class="admSpa1">Lösung</td>
  <td><div style="width:10.5em;float:left;"><input type="text" name="Lsg" value="<?php echo iconv('ISO-8859-1','UTF-8',$Lsg)?>" size="20" style="width:10em;" /></div><div class="admMini" style="margin-left:10.5em;">(falls mehrere richtige Lösungen dann die Nummern durch Komma getrennt angeben)<br />(bei Umfragen ohne richtige/falsche Antworten bitte dennoch eine Lösungsnummer eingeben)</div></td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Punktezahl</td>
  <td><input type="text" name="Pkt" value="<?php echo $Pkt?>" size="20" style="width:2em;<?php if(!FRA_PunkteTeilen) echo'color:#999;" readonly="readonly';?>" /> <span class="admMini">(für die komplett richtig beantwortete Frage, <?php if(FRA_PunkteTeilen){?>gleichmäßig <?php if(file_exists('konfAdmin.php')){?><a href="konfAdmin.php<?php if(KONF>0)echo'?konf='.KONF?>#fr">verteilt</a><?php }else{ ?>verteilt<?php } ?> auf alle Antworten<?php }else{?>individuell <?php if(file_exists('konfAdmin.php')){?><a href="konfAdmin.php<?php if(KONF>0)echo'?konf='.KONF?>#fr">verteilt</a><?php }else{ ?>verteilt<?php } ?> auf die Antworten<?php }?>)</span></td>
 </tr><?php for($i=1;$i<=ADF_AntwortZahl;$i++){?><tr class="admTabl">
  <td class="admSpa1">Antwort <?php echo $i?></td>
  <td><textarea class="admAntw" name="Aw<?php echo $i?>" cols="100" rows="2" style="width:99%;height:<?php echo round(1.5*ADF_AntwortFeldHoehe,1)?>em;"/><?php echo iconv('ISO-8859-1','UTF-8',${'Aw'.$i})?></textarea><?php if(!FRA_PunkteTeilen){?><br>Punkte: <input type="text" name="Pt<?php echo $i?>" value="<?php echo ${'Pt'.$i}?>" size="20" style="width:2em;" /><?php }?></td>
 </tr><?php } if(ADF_AnmerkZahl>0){?><tr class="admTabl">
  <td class="admSpa1">Anmerkung</td>
  <td>
   <div title="Anmerkung eingeben und zum Schluss formatieren"><?php if(!$bCKEd) echo fFraBBToolbar('Bem')?>
   <div><textarea class="admEing" id="Bem" name="Bem" cols="120" rows="5" style="width:99%;height:<?php echo round(1.5*ADF_AnmerkFeldHoehe,1)?>em;"><?php echo iconv('ISO-8859-1','UTF-8',$Bem)?></textarea></div>
   </div>
  </td>
 </tr><?php if(ADF_AnmerkZahl>1){?><tr class="admTabl">
  <td class="admSpa1">Anmerkung-2</td>
  <td>
   <div title="Anmerkung-2 eingeben und zum Schluss formatieren"><?php if(!$bCKEd) echo fFraBBToolbar('B2m')?>
   <div><textarea class="admEing" id="B2m" name="B2m" cols="120" rows="5" style="width:99%;height:<?php echo round(1.5*ADF_AnmerkFeldHoehe,1)?>em;"><?php echo iconv('ISO-8859-1','UTF-8',$B2m)?></textarea></div>
   </div>
  </td>
 </tr><?php }}?>
</table>
<input type="hidden" name="BlU" value="<?php echo $Bld?>" />
<div align="center">
<p class="admSubmit"><?php if(!$bDo){?><input class="admSubmit" type="submit" value="Eintragen"><?php }else{?>[ <a href="eingabe.php<?php if(KONF>0)echo'?konf='.KONF?>">neue Frage</a> ]<?php }?></p>
</div>
</form>

<p><u>Hinweise</u>:</p>
<ul>
<li>Fragen können durch Eingabe einer anderen Frage-Nr. umsortiert werden.</li>
<li>Formatierungen innerhalb von Frage, Antworten und Anmekungen sind als BB-Code und als HTML-Code möglich. Dazu können Sie unter <?php if(file_exists('konfAdmin.php')){?><a href="konfAdmin.php<?php if(KONF>0)echo'?konf='.KONF?>#fr">Admin-Einstellungen</a><?php }else{ ?>Admin-Einstellungen<?php } ?> den <i>BB-Editor</i> oder <i>CKEditor</i> aktivieren. Die damit formatierten Texte sind nicht zueinander kompatibel. Wechseln Sie also möglichst nicht nachträglich den einmal gewählten Editor.</li>
<li>Formatierungen als BB-Code innerhalb von Fragen und Anmerkungen mittels Formatierungs-Leiste müssen sorgfältig ausgeführt werden. Unsaubere Verschachtelungen der BB-Codes oder fehlende schließende Code-Tags führen bei der Darstellung zu Problemen.</li>
<li>Auch in den Antworten kann BB-Code oder HTML zur Formatierung benutzt werden. Dieser müsste jedoch gänzlich von Hand eingetragen werden.</li>
<li>Eventuelle Grafiken, Sounds oder Videos direkt innerhalb des Fragetextes, Antworttextes bzw. Anmerkungstextes in Form von BB-Code oder HTML-Code müssen mit anderen Mitteln als diesem Testfragen-Script am angegebenen Ort hinterlegt werden.</li>
<?php if(!FRA_PunkteTeilen){?><li>Bei individueller ungleichmäßiger Punkteverteilung auf die Antworten sind bei uninteressante Antworten 0 Punkte einzutragen.</li><?php }?>
</ul>

<?php
if($bCKEd) echo '
<script>
 ClassicEditor
  .create(document.querySelector("#Frg"))
  .then( Frg => { console.log(Frg); })
  .catch( error => { console.error(error); });
 ClassicEditor
  .create(document.querySelector("#Bem"))
  .then( Bem => { console.log(Bem); })
  .catch( error => { console.error(error); });
 ClassicEditor
  .create(document.querySelector("#B2m"))
  .then( B2m => { console.log(B2m); })
  .catch( error => { console.error(error); });
</script>
';
echo fSeitenFuss();

function fFraEingabe($sVar){
 if(isset($_POST[$sVar])){
  $s=str_replace("\r",'',trim($_POST[$sVar])); if(ADF_StripSlashes) $s=stripslashes($s);
  $l=strlen($s)-1;
  if(strpos($s,'"')) for($i=$l;$i>=0;$i--) if(substr($s,$i,1)=='"'){
   $p=strpos($s,'>',$i); $q=strpos($s,'<',$i); if($p<=0||($q>0&&$q<$p)) $s=substr_replace($s,'&quot;',$i,1);
  }
 }else $s='';
 return iconv('UTF-8','ISO-8859-1',$s);
}
function fFraTxt($s){
 return str_replace("\n",'\n ',str_replace(';','`,',$s));
}
function fFraSql($s){
 return str_replace('"','\"',str_replace("\n","\r\n",$s));
}

function fFraBBToolbar($Nam){
 $sHttp='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 $X =NL.'<table class="admTool" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Bold',   0,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Italic', 2,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Uline',  4,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'HSup',  18,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'DSub',  20,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Center', 6,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Right',  8,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Enum',  10,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Number',12,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Pict',  14,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Link',  16,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Youtube',22,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Video', 24,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Audio', 26,$sHttp).'</td>';
 $X.=NL.'  <td><img class="admTool" src="tbColor.gif" style="margin-right:0;cursor:default;" title="'.iconv('ISO-8859-1','UTF-8',FRA_TxBB_O).'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="fra_Col'.$Nam.'" onChange="fCol('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.iconv('ISO-8859-1','UTF-8',FRA_TxBB_O).'">
    <option value=""></option>
    <option style="color:black" value="black">Abc9</option>
    <option style="color:red;" value="red">Abc9</option>
    <option style="color:violet;" value="violet">Abc9</option>
    <option style="color:brown;" value="brown">Abc9</option>
    <option style="color:yellow;" value="yellow">Abc9</option>
    <option style="color:green;" value="green">Abc9</option>
    <option style="color:lime;" value="lime">Abc9</option>
    <option style="color:olive;" value="olive">Abc9</option>
    <option style="color:cyan;" value="cyan">Abc9</option>
    <option style="color:blue;" value="blue">Abc9</option>
    <option style="color:navy;" value="navy">Abc9</option>
    <option style="color:gray;" value="gray">Abc9</option>
    <option style="color:silver;" value="silver">Abc9</option>
    <option style="color:white;background-color:#999999" value="white">Abc9</option>
   </select>
  </td>';
 $X.=NL.'  <td><img class="admTool" src="tbSize.gif" style="margin-right:0;cursor:default;" title="'.iconv('ISO-8859-1','UTF-8',FRA_TxBB_S).'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="fra_Siz'.$Nam.'" onChange="fSiz('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.iconv('ISO-8859-1','UTF-8',FRA_TxBB_S).'">
    <option value=""></option>
    <option value="+2">&nbsp;+2</option>
    <option value="+1">&nbsp;+1</option>
    <option value="-1">&nbsp;- 1</option>
    <option value="-2">&nbsp;- 2</option>
   </select>
  </td>';
 $X.=NL.' </tr>';
 $X.=NL.'</table>'.NL;
 return $X;
}
function fDrawToolBtn($Nam,$vImg,$nTag,$sHttp){
 return '<img class="admTool" src="tb'.$vImg.'.gif" onClick="fFmt('."'".$Nam."'".','.$nTag.')" style="background-image:url(tool.gif);" title="'.iconv('ISO-8859-1','UTF-8',constant('FRA_TxBB_'.substr($vImg,0,1))).'" />';
}

function fFraDateiname($s){
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',$s))));
 $s=str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)));
 $s=str_replace('�','Ae',str_replace('�','Oe',str_replace('�','Ue',str_replace('�','ss',$s))));
 return str_replace('�','ae',str_replace('�','oe',str_replace('�','ue',str_replace(' ','_',$s))));
}
?>