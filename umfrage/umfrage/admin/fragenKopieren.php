<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Frage kopieren','<script src="eingabe.js" type="text/javascript"></script>','UFe');

// BB-Formatleiste
define('UMF_TxBB_X','Schreiben Sie hier Ihren Text. Markieren Sie dann gewünschte Textpassagen und formatieren sie mit dem passenden Schalter.');
define('UMF_TxBB_B','fetterText: [b]Text[/b]');
define('UMF_TxBB_I','kursiver Text: [i]Text[/i]');
define('UMF_TxBB_U','unterstrichener Text: [u]Text[/u]');
define('UMF_TxBB_H','hochgestellter Text: [sup]Text[/sup]');
define('UMF_TxBB_D','tiefgestellter Text: [sub]Text[/sub]');
define('UMF_TxBB_C','zentrierter Text: [center]Text[/center]');
define('UMF_TxBB_R','rechsbündiger Text: [right]Text[/right');
define('UMF_TxBB_E','Aufzählung: [list]Text[/list]');
define('UMF_TxBB_N','Numerierung: [list=o]Text[/list]');
define('UMF_TxBB_L','Link einfügen: [url]www.domain.de[/url] oder [url]www.domain.de Linktext[/url]');
define('UMF_TxBB_P','Bild: [img]pfad/datei.jpg[/img] oder [img]http://www.domain.de/pfad/datei.gif[/img]');
define('UMF_TxBB_O','Schriftfarbe: [color=blue]Text[/color]');
define('UMF_TxBB_S','Schriftgröße: [size=+2]grosser Text[/size]');

$sMs2=''; $bDo=false; $Frg=''; $FNr=''; $Onl=''; $Umf=''; $Bld=''; $BlA=''; $BlH=''; $BlD=''; $Bem=''; $B2m='';
$nAntwAnzahl=max(20,ADU_AntwortZahl); $aAw=array('*'); for($i=1;$i<=$nAntwAnzahl;$i++) $aAw[$i]='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $sNd=''; $bDo=false;
 if(!$FNr=(isset($_POST['FNr'])?(int)$_POST['FNr']:'')) $FNr='';
 $Onl=sprintf('%0d',(isset($_POST['onl'])?$_POST['onl']:0));
 $Umf=(isset($_POST['Umf'])?$_POST['Umf']:'');
 if(!$Frg=fUmfEingabe('Frg')) $sNd.=', Frage';
 if(!$aAw[1]=fUmfEingabe('Aw1')) $sNd.=', Antwort-1'; if(!$aAw[2]=fUmfEingabe('Aw2')) $sNd.=', Antwort-2';
 $Bem=fUmfEingabe('Bem'); $B2m=fUmfEingabe('B2m'); for($i=3;$i<=$nAntwAnzahl;$i++) $aAw[$i]=fUmfEingabe('Aw'.$i);
 if($BlA=(isset($_POST['BlA'])?$_POST['BlA']:'')) $Bld=$BlA; // altes kopieren
 if($BlH=(isset($_POST['BlH'])?$_POST['BlH']:'')) $Bld=$BlH; // hochgeladenes
 if(isset($_POST['BlD'])&&$_POST['BlD']){@unlink(UMF_Pfad.'temp/'.$Bld); $Bld=''; $BlH=''; $BlA='';} //temp Bild loeschen
 $ImN=strtolower(fUmfDateiname(basename($_FILES['Bld']['name']))); $ImE=strrchr($ImN,'.');
 if($ImE=='.jpg'||$ImE=='.gif'||$ImE=='.jpeg'||$ImE=='.png'){
  $i=$_FILES['Bld']['size'];
  if($i<=1024*UMF_BildKB){
   $aIm=@getimagesize($_FILES['Bld']['tmp_name']);
   if($aIm[0]<=UMF_BildW&&$aIm[1]<=UMF_BildH){ //direkt speichern
    if(copy($_FILES['Bld']['tmp_name'],UMF_Pfad.'temp/'.$ImN)){
     if($Bld) if($Bld!=$ImN) @unlink(UMF_Pfad.'temp/'.$Bld); $Bld=$ImN; $BlH=$ImN; //vorheriges Bild weg
    }else $sMs2='<p class="admFehl">'.str_replace('#','temp/'.$ImN,UMF_TxDateiRechte).'</p>';
   }else{//verkleinern
    if($ImE=='.jpg'||$ImE=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['Bld']['tmp_name']);
    elseif($ImE=='.gif') $Src=ImageCreateFromGIF($_FILES['Bld']['tmp_name']);
    elseif($ImE=='.png') $Src=ImageCreateFromPNG($_FILES['Bld']['tmp_name']);
    if($Src){
     $ImN=substr($ImN,0,-strlen($ImE)).'.jpg'; $Sx=ImageSX($Src); $Sy=ImageSY($Src);
     $Dw=min(UMF_BildW,$Sx); if($Sx>UMF_BildW) $Dh=round(UMF_BildW/$Sx*$Sy); else $Dh=$Sy;
     if($Dh>UMF_BildH){$Dw=round(UMF_BildH/$Dh*$Dw); $Dh=UMF_BildH;}
     $Dst=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dst,0,0,ImageColorAllocate($Dst,255,255,255));
     ImageCopyResampled($Dst,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
     if(ImageJPEG($Dst,UMF_Pfad.'temp/'.$ImN)){
      if($Bld) if($Bld!=$ImN) @unlink(UMF_Pfad.'temp/'.$Bld); $Bld=$ImN; $BlH=$ImN;
     }else $sMs2='<p class="admFehl">'.str_replace('#','temp/'.$ImN,UMF_TxDateiRechte).'</p>';
     imagedestroy($Dst); imagedestroy($Src); unset($Dst); unset($Src);
    }else $sMs2='<p class="admFehl">Das Bild <i>'.$ImN.'</i> konnte nicht verarbeitet werden!</p>';
   }
  }else $sMs2='<p class="admFehl">Bilder mit <i>'.$i.' KByte</i> Größe sind nicht erlaubt!</p>';
 }elseif(substr($ImE,0,1)=='.') $sMs2='<p class="admFehl">Bilder mit der Endung <i>'.$ImE.'</i> sind nicht erlaubt!</p>';
 if(!$sMs2) if(!$sNd){ //alles OK
  if(!UMF_SQL){ //Text
   $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=max(count($aD),1);
   $s=''; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=';Antwort'.$i; $aD[0]='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'.$s.NL;
   if($FNr<=0) $FNr=((int)$aD[$nCnt-1])+1; $bSuch=true; $bKorr=false;
   $sNeu=$FNr.';'.$Onl.';'.$Umf.';'.fUmfTxt($Frg).';'.$Bld.';'.fUmfTxt($Bem).';'.fUmfTxt($B2m);
   for($i=1;$i<=$nAntwAnzahl;$i++) $sNeu.=';'.fUmfTxt($aAw[$i]);
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
   if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Fragen,'w')){
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    $sMeld='<p class="admErfo">Die kopierte Frage wurde gespeichert!</p>'; $bDo=true;
   }else $sMeld='<p class="admFehl">'.str_replace('#',UMF_Daten.UMF_Fragen,UMF_TxDateiRechte).'</p>';
  }elseif($DbO){//SQL
   if($bNum=($FNr>0)){
    $aN=array();
    if($rR=$DbO->query('SELECT Nummer FROM '.UMF_SqlTabF.' WHERE Nummer>='.$FNr.' ORDER BY Nummer')){
     $a=$rR->fetch_row(); $nL=$a[0];
     if($nL==$FNr){$aN[]=$nL++; while($a=$rR->fetch_row()){$n=$a[0]; if($n==$nL) $aN[]=$nL++; else break;}}
     $rR->close(); $nCnt=count($aN)-1;
     for($i=$nCnt;$i>=0;$i--) $DbO->query('UPDATE IGNORE '.UMF_SqlTabF.' SET Nummer=Nummer+1 WHERE Nummer='.$aN[$i]);
    }
   }
   $sF=''; $sV=''; for($i=1;$i<=$nAntwAnzahl;$i++){$sF.=',Antwort'.$i; $sV.=',"'.fUmfSql($aAw[$i]).'"';}
   if($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabF.' ('.($bNum?'Nummer,':'').'aktiv,Umfrage,Frage,Bild,Anmerkung1,Anmerkung2'.$sF.') VALUES("'.($bNum?$FNr.'","':'').$Onl.'","'.$Umf.'","'.fUmfSql($Frg).'","'.$Bld.'","'.fUmfSql($Bem).'","'.fUmfSql($B2m).'"'.$sV.')')){
    $FNr=$DbO->insert_id; $sMeld='<p class="admErfo">Die kopierte Frage wurde gespeichert!</p>'; $bDo=true;
   }else $sMeld='<p class="admFehl">'.UMF_TxSqlEinfg.'</p>';
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
  if($bDo&&$BlH>''&&$Bld>''){
   if(copy(UMF_Pfad.'temp/'.$BlH,UMF_Pfad.UMF_Bilder.$BlH)) @unlink(UMF_Pfad.'temp/'.$BlH);
   else $sMs2.='<p class="admFehl">'.str_replace('#',UMF_Bilder.$BlH,UMF_TxDateiRechte).'</p>';
  }
 }else $sMeld='<p class="admFehl">Es fehlen Eintragungen bei'.substr($sNd,1).'!</p>';
}else{ //GET
 $FNr=(isset($_GET['nr'])?$_GET['nr']:''); $ONr=$FNr; $sMeld='<p class="admMeld">Kopieren Sie die bisherige Frage Nummer '.$FNr.'.</p>';
 if(!UMF_SQL){
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD);
  for($i=0;$i<$nCnt;$i++){$sLn=$aD[$i]; $p=strpos($sLn,';'); if(substr($sLn,0,$p)==$FNr){$aR=explode(';',rtrim($sLn)); break;}}
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' WHERE Nummer='.$FNr)){
   $aR=$rR->fetch_row(); $rR->close();
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
 }
 $Onl=$aR[1]; $Umf=$aR[2]; $Frg=str_replace('\n ',"\n",str_replace("\r",'',str_replace('`,',';',$aR[3]))); $BlA=$aR[4]; $Bld=$BlA;
 $Bem=str_replace('\n ',"\n",str_replace("\r",'',str_replace('`,',';',$aR[5])));
 $B2m=str_replace('\n ',"\n",str_replace("\r",'',str_replace('`,',';',$aR[6])));
 for($i=1;$i<=$nAntwAnzahl;$i++) $aAw[$i]=str_replace('\n ',"\n",str_replace("\r",'',str_replace('`,',';',(isset($aR[6+$i])?$aR[6+$i]:''))));
 $FNr='';
}

echo $sMeld.$sMs2.NL;
$sOptU=''; for($i=1;$i<=26;$i++) $sOptU.='<option value="'.chr($i+64).'">Umfrage '.chr($i+64).'</option>';
?>

<form name="umfEingabe" action="fragenKopieren.php<?php if(isset($_SERVER['QUERY_STRING'])&&($sQ=$_SERVER['QUERY_STRING'])) echo '?'.$sQ?>" enctype="multipart/form-data" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1">Frage-Nr.</td>
  <td>
   <input type="text" name="FNr" value="<?php echo $FNr?>" size="2" style="width:3em;" />
   <span class="admMini">(ohne Angabe einer Nummer wird die Frage automatisch fortlaufend nummeriert)</span>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Fragenstatus</td>
  <td><input type="checkbox" class="admCheck" name="onl" value="1"<?php if($Onl) echo ' checked="checked"'?>> aktiviert</td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Zuordnung zu<br>einer Umfrage</td>
  <td><select name="Umf" size="1" style="width:auto"><option value=""></option><?php echo ($Umf?str_replace('value="'.$Umf.'"','value="'.$Umf.'" selected="selected"',$sOptU):$sOptU)?></select> <span class="admMini">(optional, nur wenn mehrere separate Umfragen veranstaltet werden sollen)</span></td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Frage</td>
  <td>
   <div title="Frage eingeben und dann formatieren"><?php echo fUmfBBToolbar('Frg')?>
   <div><textarea class="admEing" name="Frg" cols="120" rows="6" style="width:98%;height:<?php echo round(1.5*ADU_FragenFeldHoehe,1)?>em;"><?php echo $Frg?></textarea></div>
   </div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Bild<div class="admMini">max. <?php echo UMF_BildKB?>KB</div></td>
  <td><input type="file" name="Bld" size="80" style="width:98%;" />
  <?php if($Bld){?><input type="checkbox" class="admCheck" name="BlD" value="1" /> Bild <i><?php echo $Bld?></i> löschen<?php }?></td>
 </tr><?php for($i=1;$i<=ADU_AntwortZahl;$i++){?><tr class="admTabl">
  <td class="admSpa1">Antwort <?php echo $i?></td>
  <td><textarea class="admAntw" name="Aw<?php echo $i?>" cols="100" rows="2" style="width:98%;height:<?php echo round(1.5*ADU_AntwortFeldHoehe,1)?>em;"/><?php echo $aAw[$i]?></textarea></td>
 </tr><?php } if(ADU_AnmerkZahl>0){?><tr class="admTabl">
  <td class="admSpa1">Anmerkung-1</td>
  <td>
   <div title="Anmerkung eingeben und zum Schluss formatieren"><?php echo fUmfBBToolbar('Bem')?>
   <div><textarea class="admEing" name="Bem" cols="120" rows="5" style="width:98%;height:<?php echo round(1.5*ADU_AnmerkFeldHoehe,1)?>em;"><?php echo $Bem?></textarea></div>
   </div>
  </td>
 </tr><?php if(ADU_AnmerkZahl>1){?><tr class="admTabl">
  <td class="admSpa1">Anmerkung-2</td>
  <td>
   <div title="Anmerkung eingeben und zum Schluss formatieren"><?php echo fUmfBBToolbar('B2m')?>
   <div><textarea class="admEing" name="B2m" cols="120" rows="5" style="width:98%;height:<?php echo round(1.5*ADU_AnmerkFeldHoehe,1)?>em;"><?php echo $B2m?></textarea></div>
   </div>
  </td>
 </tr><?php }}?>
</table>
<input type="hidden" name="ONr" value="<?php echo $ONr?>" />
<input type="hidden" name="BlA" value="<?php echo $BlA?>" />
<input type="hidden" name="BlH" value="<?php echo $BlH?>" />
<div align="center">
<p class="admSubmit"><?php if(!$bDo){?><input class="admSubmit" type="submit" value="Eintragen"><?php }else{?>[ <a href="fragenEingabe.php<?php if(KONF>0)echo'?konf='.KONF?>">neue Frage</a> ]<?php }?></p>
<p>[ <a href="fragenListe.php<?php if($sQ) echo '?'.$sQ?>">zurück zur Liste</a> ]</p>
</div>
</form>

<p><u>Hinweise</u>:</p>
<ul>
<li>Formatierungen innerhalb von Frage, Antworten und Anmekungen sind als BB-Code und als HTML-Code möglich.</li>
<li>Formatierungen als BB-Code innerhalb von Fragen und Anmerkungen mittels Formatierungs-Leiste müssen sorgfältig ausgeführt werden.
Unsaubere Verschachtelungen der BB-Codes oder fehlende schließende Code-Tags führen bei der Darstellung zu Problemen.</li>
<li>Auch in den Antworten kann BB-Code zur Formatierung benutzt werden. Dieser müsste jedoch gänzlich von Hand eingetragen werden.</li>
<li>Formatierungen mittels HTML-Code sind komplett von Hand einzutragen. Auf saubere Codierung und korrekte Verschachtelung ist selbst zu achten. Es findet keinerlei Prüfung durch des Script statt.</li>
<li>Eventuelle Grafiken, Sounds oder Videos direkt innerhalb des Fragetextes, Antworttextes bzw. Anmerkungstextes in Form von BB-Code oder HTML-Code müssen mit anderen Mitteln als diesem Umfragen-Script am angegebenen Ort hinterlegt werden.</li>
</ul>

<?php
echo fSeitenFuss();

function fUmfEingabe($sVar){
 if(isset($_POST[$sVar])){
  $s=str_replace("\r",'',trim($_POST[$sVar])); if(ADU_StripSlashes) $s=stripslashes($s);
  $l=strlen($s)-1;
  if(strpos($s,'"')) for($i=$l;$i>=0;$i--) if(substr($s,$i,1)=='"'){
   $p=strpos($s,'>',$i); $q=strpos($s,'<',$i); if($p<=0||($q>0&&$q<$p)) $s=substr_replace($s,'&quot;',$i,1);
  }
 }else $s='';
 return $s;
}
function fUmfTxt($s){
 return str_replace("\n",'\n ',str_replace(';','`,',$s));
}
function fUmfSql($s){
 return str_replace('"','\"',str_replace("\n","\r\n",$s));
}

function fUmfBBToolbar($Nam){
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
 $X.=NL.'  <td><img class="admTool" src="tbColor.gif" style="margin-right:0;cursor:default;" title="'.UMF_TxBB_O.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="umf_Col'.$Nam.'" onChange="fCol('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.UMF_TxBB_O.'">
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
 $X.=NL.'  <td><img class="admTool" src="tbSize.gif" style="margin-right:0;cursor:default;" title="'.UMF_TxBB_S.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="umf_Siz'.$Nam.'" onChange="fSiz('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.UMF_TxBB_S.'">
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
 return '<img class="admTool" src="tb'.$vImg.'.gif" onClick="fFmt('."'".$Nam."'".','.$nTag.')" style="background-image:url(tool.gif);" title="'.constant('UMF_TxBB_'.substr($vImg,0,1)).'" />';
}

function fUmfDateiname($s){
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',$s))));
 $s=str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)));
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',$s))));
 return str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',str_replace(' ','_',$s))));
}
?>