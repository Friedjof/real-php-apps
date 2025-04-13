<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusatzprogramm <i>neue Inserate</i> konfigurieren','<script type="text/javascript">
 function colWin(sURL){cWin=window.open(sURL,"color","width=300,height=380,left=2,top=2,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");cWin.focus();}
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}

</script>
','PnI');

if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET Werteform
 $mpNeueAnzahl=MP_NeueAnzahl; $mpNeueTage=MP_NeueTage; $mpNeueNachEintrag=MP_NeueNachEintrag; $mpNeueNachEZeit=MP_NeueNachEZeit;
 $mpNeueKopf=MP_NeueKopf; $mpNeueAbstand=MP_NeueAbstand; $mpNeueEigeneZeilen=MP_NeueEigeneZeilen; $mpNeueSegTrnZ=MP_NeueSegTrnZ;
 $mpNeueFelder=MP_NeueFelder; $mpNeueCssStyle=MP_NeueCssStyle;
 $mpNeueLink=MP_NeueLink; $mpNeueTarget=MP_NeueTarget; $mpNeuePopup=MP_NeuePopup; $mpNeueXTarget='';
 if($mpNeueTarget!='markt'&&$mpNeueTarget!='_self'&&$mpNeueTarget!='_parent'&&$mpNeueTarget!='_top'&&$mpNeueTarget!='_blank') $mpNeueXTarget=$mpNeueTarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST Werteform
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=(int)txtVar('NeueAnzahl'); if(fSetzMPWert($v,'NeueAnzahl','')) $bNeu=true;
 $v=(int)txtVar('NeueTage'); if(fSetzMPWert($v,'NeueTage','')) $bNeu=true;
 $v=txtVar('NeueNachEintrag'); if(fSetzMPWert(($v?true:false),'NeueNachEintrag','')) $bNeu=true;
 $v=(int)txtVar('NeueNachEZeit'); if(fSetzMPWert($v,'NeueNachEZeit','')) $bNeu=true;
 $v=txtVar('NeueKopf'); if(fSetzMPWert(($v?true:false),'NeueKopf','')) $bNeu=true;
 $v=(int)txtVar('NeueAbstand'); if(fSetzMPWert($v,'NeueAbstand','')) $bNeu=true;
 $v=txtVar('NeueEigeneZeilen'); if(fSetzMPWert(($v?true:false),'NeueEigeneZeilen','')) $bNeu=true;
 $v=txtVar('NeueSegTrnZ'); if(fSetzMPWert(($v?true:false),'NeueSegTrnZ','')) $bNeu=true;
 $v=str_replace("\n",';',str_replace(';','`,',str_replace("\r",'',txtVar('NeueFelder')))); if(fSetzMPWert($v,'NeueFelder','"')) $bNeu=true;
 $v=txtVar('NeueCssStyle'); if(fSetzMPWert(($v?true:false),'NeueCssStyle','')) $bNeu=true;
 $v=txtVar('NeueLink'); if(fSetzMPWert($v,'NeueLink','"')) $bNeu=true;
 if($v=txtVar('NeueTarget')) $mpNeueXTarget=''; else{$v=txtVar('NeueXTarget'); $mpNeueXTarget=$v;}
 if(txtVar('NeuePopup')>'0'){$v='mpwin'; $mpNeueXTarget='mpwin';} if(fSetzMPWert($v,'NeueTarget','"')) $bNeu=true;
 $v=txtVar('NeuePopup'); if(fSetzMPWert(($v?true:false),'NeuePopup','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Einstellungen zu neuen Inseraten wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Einstellungen zu neuen Inseraten bleiben unverändert.'; $MTyp='Meld';}
}//POST

if(file_exists(MPPFAD.'mpStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(MPPFAD.'mpStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sPageH=fLiesHGFarb('body.mpNeue');
  $sTListW=fLiesScreenW('neuen Inserate mit Spalten');
  $sTNeueR=fLiesRahmFarb('div.mpTbNLst'); $sTNeueA=fLiesRahmArt('div.mpTbNLst');
  $sNeuFS=fLiesFontS('div.mpTabN'); $sNeuW=fLiesWeite('div.mpTabN');
  $sZNeu1F=fLiesFarbe('div.mpTbNZl1'); $sZNeu1H=fLiesHGFarb('div.mpTbNZl1');
  $sZNeu2F=fLiesFarbe('div.mpTbNZl2'); $sZNeu2H=fLiesHGFarb('div.mpTbNZl2');
  $sZNeuKF=fLiesFarbe('div.mpTbNZl0'); $sZNeuKH=fLiesHGFarb('div.mpTbNZl0');
  $sANeuL=fLiesFarbe('a.mpNeue:link'); $sANeuA=fLiesFarbe('a.mpNeue:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sPageH=fTxtCol('PageH'); if(fSetzHGFarb($sPageH,'body.mpNeue')) $bNeu=true;
  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'neuen Inserate mit Spalten')) $bNeu=true;
  $sTNeueR=fTxtCol('TNeueR'); if($sTNeueR!=fLiesRahmFarb('div.mpTbNLst')){
   if(fSetzRahmFarb($sTNeueR,'div.mpTabN')) $bNeu=true;    if(fSetzRahmFarb($sTNeueR,'div.mpTabNE')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZl0')) $bNeu=true; if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZL0')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZl1')) $bNeu=true; if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZL1')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZl2')) $bNeu=true; if(fSetzRahmFarbB($sTNeueR,'div.mpTbNZL2')) $bNeu=true;
   if(fSetzRahmFarb($sTNeueR,'div.mpTbNLst')) $bNeu=true;
   if(fSetzRahmFarb($sTNeueR,'div.mpTbNLst',2)) $bNeu=true;
  }
  $sTNeueA=$_POST['TNeueA']; if($sTNeueA!=fLiesRahmArt('div.mpTbNLst')){
   if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZl0')) $bNeu=true; if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZL0')) $bNeu=true; if(fSetzeRahmArt($sTNeueA,'div.mpTbNZL0')) $bNeu=true;
   if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZl1')) $bNeu=true; if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZL1')) $bNeu=true; if(fSetzeRahmArt($sTNeueA,'div.mpTbNZL1')) $bNeu=true;
   if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZl2')) $bNeu=true; if(fSetzeRahmArtB($sTNeueA,'div.mpTbNZL2')) $bNeu=true; if(fSetzeRahmArt($sTNeueA,'div.mpTbNZL2')) $bNeu=true;
   if(fSetzeRahmArt($sTNeueA,'div.mpTbNLst')) $bNeu=true;
   if(fSetzeRahmArt($sTNeueA,'div.mpTbNLst',2)) $bNeu=true;
  }
  $sNeuW= fTxtSiz('NeuW');  if(fSetzeWeite($sNeuW,'div.mpTabN')) $bNeu=true;  if(fSetzeWeite($sNeuW,'div.mpTabNE')) $bNeu=true;
  $sNeuFS=fTxtSiz('NeuFS'); if(fSetzeFontS($sNeuFS,'div.mpTabN')) $bNeu=true; if(fSetzeFontS($sNeuFS,'div.mpTabNE')) $bNeu=true; 
  $sZNeu1F=fTxtCol('ZNeu1F'); if(fSetzeFarbe($sZNeu1F,'div.mpTbNZl1')) $bNeu=true; if(fSetzeFarbe($sZNeu1F,'div.mpTbNZL1')) $bNeu=true;
  $sZNeu1H=fTxtCol('ZNeu1H'); if(fSetzHGFarb($sZNeu1H,'div.mpTbNZl1')) $bNeu=true; if(fSetzHGFarb($sZNeu1H,'div.mpTbNZL1')) $bNeu=true;
  $sZNeu2F=fTxtCol('ZNeu2F'); if(fSetzeFarbe($sZNeu2F,'div.mpTbNZl2')) $bNeu=true; if(fSetzeFarbe($sZNeu2F,'div.mpTbNZL2')) $bNeu=true;
  $sZNeu2H=fTxtCol('ZNeu2H'); if(fSetzHGFarb($sZNeu2H,'div.mpTbNZl2')) $bNeu=true; if(fSetzHGFarb($sZNeu2H,'div.mpTbNZL2')) $bNeu=true;
  $sZNeuKF=fTxtCol('ZNeuKF'); if(fSetzeFarbe($sZNeuKF,'div.mpTbNZl0')) $bNeu=true; if(fSetzeFarbe($sZNeuKF,'div.mpTbNZL0')) $bNeu=true;
  $sZNeuKH=fTxtCol('ZNeuKH'); if(fSetzHGFarb($sZNeuKH,'div.mpTbNZl0')) $bNeu=true; if(fSetzHGFarb($sZNeuKH,'div.mpTbNZL0')) $bNeu=true;
  $sANeuL=fTxtCol('ANeuL'); if(fSetzeFarbe($sANeuL,'a.mpNeue:link')) $bNeu=true;
  $sANeuA=fTxtCol('ANeuA'); if(fSetzeFarbe($sANeuA,'a.mpNeue:hover')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(MPPFAD.'mpStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Meld='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Meld='<p class="admFehl">In die Datei <i>mpStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Meld) $Meld='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
 }//POST
}else $Meld.='<p class="admFehl">Setup-Fehler: Die Datei <i>mpStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Meld){$Meld='Ändern Sie die wesentlichsten Layout- und Farbeinstellungen. &nbsp; <span class="admMini" style="font-weight:normal;color:gray;">(Farbformular unten)</span>'; $MTyp='Meld';}
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;

$sIcon='iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfNeues.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Mit dem Zusatzprogramm <i>neue Inserate</i> können einige der zuletzt eingetragenen Inserate unabhängig von der sonstigen Marktplatz-Ausgabe dargestellt werden. Die Auswahl erfolgt in Kombination folgender Kritierien:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">neue Anzahl</td>
 <td><input type="text" name="NeueAnzahl" value="<?php echo $mpNeueAnzahl?>" style="width:24px;" /> Inserate <span class="admMini">(ca. 3...10,&nbsp; 0 für nicht nach der Anzahl begrenzt)</span><br>
 Maximalzahl der Inserate die dargestellt werden sollen.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">neue Intervall</td>
 <td><input type="text" name="NeueTage" value="<?php echo $mpNeueTage?>" style="width:24px;" /> Inserate <span class="admMini">(ca. 3...10,&nbsp; 0 für nicht nach dem Datum begrenzt)</span><br>
 Anzahl der unterschiedlichen Kalendertage, aus denen die Inserate stammen dürfen.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">neue Auswahl</td>
 <td>
  <input class="admRadio" type="radio" name="NeueNachEintrag" value="1"<?php if($mpNeueNachEintrag) echo' checked="checked"'?>> nach neustem Eintragsdatum/Eintragszeit der Inserate auswählen<br /><div style="color:#<?php echo($mpNeueNachEintrag?'000':'999')?>;">
  &nbsp; &nbsp; <input class="admRadio" type="radio" name="NeueNachEZeit" value="1"<?php if($mpNeueNachEZeit=="1") echo' checked="checked"'?>> <i>vorhandenes</i> Eintragsdatum <i>und</i> Eintragszeit der Inserate auswerten<br />
  &nbsp; &nbsp; <input class="admRadio" type="radio" name="NeueNachEZeit" value="0"<?php if(!$mpNeueNachEZeit) echo' checked="checked"'?>> nur <i>vorhandenes</i> Eintragsdatum der Inserate auswerten<br />
  &nbsp; &nbsp; <input class="admRadio" type="radio" name="NeueNachEZeit" value="2"<?php if($mpNeueNachEZeit=="2") echo' checked="checked"'?>> notfalls ein (aus Ablaufdatum und Anzeigedauer) <i>berechnetes</i> Eintragsdatum verwenden<br /></div>
  <input class="admRadio" type="radio" name="NeueNachEintrag" value="0"<?php if(!$mpNeueNachEintrag) echo' checked="checked"'?>> nach längstem Ablaufdatum der Inserate auswählen<br />
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei neuen Inseraten quer durch alle Segmente kann über jedem gefundenen Segment mit passenden neuen Inseraten dessen Segmentname als Zwischenzeile erscheinen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Segement-<br />zwischenzeile</td>
 <td><input type="radio" class="admRadio" name="NeueSegTrnZ" value="1"<?php if($mpNeueSegTrnZ) echo ' checked="checked"'?> /> Segmenttrennzeile einblenden &nbsp; <input type="radio" class="admRadio" name="NeueSegTrnZ" value="0"<?php if(!$mpNeueSegTrnZ) echo ' checked="checked"'?> /> keine Segmentrennzeile zeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile</td>
 <td><input class="admRadio" type="checkbox" name="NeueKopf" value="1"<?php if($mpNeueKopf) echo' checked="checked"'?>> über den neuen Inseraten soll pro Segment ein Kopf mit den Feldnamen angezeigt werden.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Segmentabstand</td>
 <td><input type="text" name="NeueAbstand" value="<?php echo $mpNeueAbstand?>" style="width:24px;" /> Pixel vertikaler Abstand zwischen den einzelnen Segmenten mit neuen Inseraten &nbsp; <span class="admMini">(Empfehlung: <i>5</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die neuen Inserate werden standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt.
Abweichend davon kann jeder Inseratedatensatz in einem individuellen Layout dargestellt werden, das aus der Layoutschablone <i>neueXXZeile.htm</i> (wobei XX für die Segmentnummer steht) bzw. <i>neueZeile.htm</i> und gegebenfalls <i>neueXXKopf.htm</i> (wobei XX für die Segmentnummer steht) bzw. <i>neueKopf.htm</i> stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout</td>
 <td><input class="admRadio" type="radio" name="NeueEigeneZeilen" value="0"<?php if(!$mpNeueEigeneZeilen) echo' checked="checked"'?>> tabellarisches Standardlayout &nbsp;
 <input class="admRadio" type="radio" name="NeueEigeneZeilen" value="1"<?php if($mpNeueEigeneZeilen) echo' checked="checked"'?>> individuelles Layout aus den Layoutschablonen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei individuellem Layout werden die Felder entsprechend der Platzhalteranordnung in den HTML-Layoutschablonen dargestellt.
Bei tabellarischem Standardlayout hingegen werden normalerweise alle unter <i>Segmenteigenschaften</i> eingestellten <i>Listenspalten</i> angezeigt.
Es können bei Standardlayout aber auch <i>weniger</i> Spalten gezeigt werden, indem deren Feldnamen hier aufgezählt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">nur folgende<br>Spalten zeigen</td>
 <td><textarea name="NeueFelder" cols="30" rows="5" style="width:200px;"><?php echo str_replace('`,',';',str_replace(';',"\n",$mpNeueFelder))?></textarea> Feldnamen untereinander aufzählen oder leer lassen.
 <div class="admMini">Achtung: Eine eingetragene Feldnamenaufzählung gilt segmentübergreifend für alle Segmente!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">optionale<br>CSS-Styles</td>
 <td><input class="admRadio" type="radio" name="NeueCssStyle" value="0"<?php if(!$mpNeueCssStyle) echo' checked="checked"'?>> nicht berücksichtigen &nbsp;
 <input class="admRadio" type="radio" name="NeueCssStyle" value="1"<?php if($mpNeueCssStyle) echo' checked="checked"'?>> berücksichtigen wie im Formular <i>Segmenteigenschaften</i> eingetragen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei einem Klick auf einen Link in den neuen Inseraten
soll sich das Marktplatzscript öffnen und die Inseratedetails anzeigen. In welchem Zielfenster (Target) soll das passieren?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zielfenster</td>
 <td><select name="NeueTarget" size="1" style="width:180px;"><option value=""></option><option value="_self"<?php if($mpNeueTarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($mpNeueTarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($mpNeueTarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($mpNeueTarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="marktplatz"<?php if($mpNeueTarget=='marktplatz') echo' selected="selected"'?>>marktplatz: Marktplatzfenster</option></select>&nbsp;
 oder anderes Zielfenster  <input type="text" name="NeueXTarget" value="<?php echo $mpNeueXTarget?>" style="width:110px;" /> (Target)<br>
 <input class="admRadio" type="checkbox" name="NeuePopup" value="1"<?php if($mpNeuePopup) echo' checked="checked"'?>> als Popupfenster</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern die neuen Inserate direkt aufgerufen werden (auch in einem i-Frame)
wird als Verweisziel für die Anzeige aus den neuen Inseraten heraus
automatisch das Marktplatzscript <i>marktplatz.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Marktplatzes hier angeben.<br />
Wenn die neuen Inserate in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
wird als Verweisziel das aufrufende PHP-Script selbst angenommen,
es sei denn Sie vereinbaren hier ein anderes Verweisziel zur Anzeige der Inserate.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input type="text" name="NeueLink" value="<?php echo $mpNeueLink?>" style="width:99%" />
 <div class="admMini">leer lassen oder Scriptname, eventuell mit absoluter Pfadangabe, notfalls mit Domain und QueryString</div></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p style="margin-top:20px;">Die folgenden Farben und Attribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> mpStyles.css</a> editieren.</p>
<form name="farbform" action="konfNeues.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern die NeuenInserate <i>eigenständig</i> laufen und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Inserate erhalten einen farbigen Rahmen und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />und Gitternetz</td>
 <td><select name="TNeueA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTNeueA)?></select> Linien</td>
 <td><input type="text" name="TNeueR" value="<?php echo $sTNeueR?>" style="width:70px">
 <a href="<?php echo fColorRef('TNeueR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTNeueA?> <?php echo $sTNeueR?>;color:<?php echo $sTNeueR?>;background-color:<?php echo $sZNeu1H?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Inserate können eine individuelle Schriftgröße erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schriftgröße</td>
 <td colspan="3"><input type="text" name="NeuFS" value="<?php echo $sNeuFS?>" style="width:70px"> (Masseinheit <i>em</i> oder <i>px</i> unbedingt angeben!)</td>
 <td class="admMini">Empfehlung: 0.8em</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die umhüllende Tabelle der neuen Inserate kann eine individuelle Breitenangabe erhalten.
Dies ist in den meisten Fällen aber nicht notwendig, da sich die Breite über die Schriftgröße von selbst einregelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenbreite</td>
 <td colspan="3"><input type="text" name="NeuW" value="<?php echo $sNeuW?>" style="width:70px"> (<i>auto</i> oder mit Masseinheit <i>%</i> oder <i>px</i>)</td>
 <td class="admMini">keine Empfehlung<br />evt. leer lassen</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Inserate werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Inseratefelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Inserateliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 200...600px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In den neuen Inseraten treten folgende Datenzellen auf.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 1<br />ungerade Zeile</td>
 <td><input type="text" name="ZNeu1F" value="<?php echo $sZNeu1F?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu1F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeu1H" value="<?php echo $sZNeu1H?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu1H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeu1F?>;background-color:<?php echo $sZNeu1H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 2<br />gerade Zeile</td>
 <td><input type="text" name="ZNeu2F" value="<?php echo $sZNeu2F?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu2F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeu2H" value="<?php echo $sZNeu2H?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu2H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeu2F?>;background-color:<?php echo $sZNeu2H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Inserate können eine Kopfzeile besitzen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeilenzelle</td>
 <td><input type="text" name="ZNeuKF" value="<?php echo $sZNeuKF?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeuKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeuKH" value="<?php echo $sZNeuKH?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeuKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeuKF?>;background-color:<?php echo $sZNeuKH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Verweis in den Marktplatz sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="ANeuL" value="<?php echo $sANeuL?>" style="width:70px"> <a href="<?php echo fColorRef('ANeuL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="ANeuA" value="<?php echo $sANeuA?>" style="width:70px"> <a href="<?php echo fColorRef('ANeuA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sANeuL?>;background-color:<?php echo $sZNeu1H?>;" onmouseover="this.style.color='<?php echo $sANeuA?>'" onmouseout="this.style.color='<?php echo $sANeuL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fLiesFarbe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1); $q+=5; $z=strpos($sCss,';',$q);
   if($q>5&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFarbe($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1);
   $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesHGFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   if($q=strpos($sCss,'background-color',$p)) $q+=16; $z=strpos($sCss,';',$q);
   if($q>16&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzHGFarb($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e);
      if($e<$p+14) return trim(substr($sCss,$p,$e-$p)); else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarb($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarbB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmArt($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,$l); else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArt($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArtB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesWeite($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'width',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fLiesFontS($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeWeite($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'width',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFontS($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesScreenW($w){
 global $sCss;
 if($p=strpos($sCss,$w)) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p&&$q>$e) return str_replace(' ','',trim(substr($sCss,++$p,$e-$p))); else return false;
  }else return false;
 }else return false;
}
function fSetzScreenW($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p++&&$q>$e){
    if(substr($sCss,$p,$e-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fRahmenArten($s){
 return '<option value="none">unsichtbar</option><option value="solid"'.($s!='solid'?'':' selected="selected"').'>volle Linie</option><option value="dotted"'.($s!='dotted'?'':' selected="selected"').'>gepunktet</option><option value="dashed"'.($s!='dashed'?'':' selected="selected"').'>gestrichelt</option>';
}
function fColorRef($n){$s=$GLOBALS['s'.$n]; return 'colors.php?col='.($s!='transparent'?substr($s,1):$s).'&fld='.$n.'" target="color" onClick="javascript:ColWin()';}
function fTxtCol($Var){
 $s=(isset($_POST[$Var])?strtolower(str_replace('"',"'",stripslashes(trim($_POST[$Var])))):'');
 if(strlen($s)>0&&$s!='transparent'){if(substr($s,0,1)!='#') $s='#'.$s; while(strlen($s)<7) $s.='0';}
 return $s;
}
function fTxtSiz($Var){return (isset($_POST[$Var])?strtolower(str_replace('"',"'",str_replace(',','.',str_replace(' ','',stripslashes(trim($_POST[$Var])))))):'');}
?>