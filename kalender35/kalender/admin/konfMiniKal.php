<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Mini-Kalender anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','PMK');

$nFelder=count($kal_FeldName); $ksMiniXTarget=''; $bNeu=false;
if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET
 $ksMiniMonate=KAL_MiniMonate; $ksMiniVertikal=KAL_MiniVertikal; $ksMiniReihen=max(KAL_MiniReihen,1);
 $ksMiniFremd=KAL_MiniFremd; $ksMiniTextFeld=KAL_MiniTextFeld; $ksMiniTextFld2=KAL_MiniTextFld2; $ksMiniOhneAltes=KAL_MiniOhneAltes;
 $ksMiniWochenNr=KAL_MiniWochenNr; $ksMiniTxWo=KAL_MiniTxWo; $ksMiniTxNr=KAL_MiniTxNr;
 $ksMiniLink=KAL_MiniLink; $ksMiniTarget=KAL_MiniTarget; $ksMiniPopup=KAL_MiniPopup; $ksMiniSicht=KAL_MiniSicht;
 if($ksMiniTarget!='kalender'&&$ksMiniTarget!='_self'&&$ksMiniTarget!='_parent'&&$ksMiniTarget!='_top'&&$ksMiniTarget!='_blank') $ksMiniXTarget=$ksMiniTarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php'))));
 $v=min(max(txtVar('MiniReihen'),1),6); if(fSetzKalWert($v,'MiniReihen','')) $bNeu=true;
 $v=ceil(min(max(txtVar('MiniMonate'),1),12)/$v)*$v; if(fSetzKalWert($v,'MiniMonate','')) $bNeu=true;
 $v=txtVar('MiniVertikal'); if(fSetzKalWert(($v?true:false),'MiniVertikal','')) $bNeu=true;
 $v=txtVar('MiniFremd'); if(fSetzKalWert(($v?true:false),'MiniFremd','')) $bNeu=true;
 $v=max(txtVar('MiniTextFeld'),1); if(fSetzKalWert($v,'MiniTextFeld','')) $bNeu=true;
 $v=max(txtVar('MiniTextFld2'),0); if(fSetzKalWert($v,'MiniTextFld2','')) $bNeu=true;
 $v=txtVar('MiniOhneAltes'); if(fSetzKalWert(($v?true:false),'MiniOhneAltes','')) $bNeu=true;
 $v=txtVar('MiniWochenNr'); if(fSetzKalWert(($v?true:false),'MiniWochenNr','')) $bNeu=true;
 $v=txtVar('MiniTxWo'); if(fSetzKalWert($v,'MiniTxWo',"'")) $bNeu=true;
 $v=txtVar('MiniTxNr'); if(fSetzKalWert($v,'MiniTxNr',"'")) $bNeu=true;
 $v=txtVar('MiniLink'); if(fSetzKalWert($v,'MiniLink','"')) $bNeu=true;
 if($v=txtVar('MiniTarget')) $ksMiniXTarget=''; else{$v=txtVar('MiniXTarget'); $ksMiniXTarget=$v;} if(fSetzKalWert($v,'MiniTarget','"')) $bNeu=true;
 $v=txtVar('MiniPopup'); if(fSetzKalWert(($v?true:false),'MiniPopup','')) $bNeu=true;
 $v=(int)txtVar('MiniSicht'); if(fSetzKalWert($v,'MiniSicht','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die geänderte Minikalendereinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST
$sOptMLnk=''; $sOptMLk2='';
for($i=1;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i];
 if($t=='d'||$t=='t'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='w'){
  $sOptMLnk.='<option value="'.$i.'"'.($ksMiniTextFeld!=$i?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
  $sOptMLk2.='<option value="'.$i.'"'.($ksMiniTextFld2!=$i?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
 }
}

if(file_exists(KALPFAD.'kalStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sPageH=fLiesHGFarb('body.kalMinikalender');
  $sMinFS=fLiesFontS('table.kalMini'); $sMinW=fLiesWeite('table.kalMini');
  $sTMiniR=fLiesRahmFarb('table.kalMini'); $sTMiniA=fLiesRahmArt('table.kalMini');
  if(!$sZMinDF=fLiesFarbe('td.kalMinD',2)) $sZMinDF=fLiesFarbe('td.kalMinD');
  if(!$sZMinDH=fLiesHGFarb('td.kalMinD',2)) $sZMinDH=fLiesHGFarb('td.kalMinD');
  if(!$sZMinLF=fLiesFarbe('td.kalMinL',2)) $sZMinLF=fLiesFarbe('td.kalMinL');
  if(!$sZMinLH=fLiesHGFarb('td.kalMinL',2)) $sZMinLH=fLiesHGFarb('td.kalMinL');
  if(!$sZMinHF=fLiesFarbe('td.kalMinH',2)) $sZMinHF=fLiesFarbe('td.kalMinH');
  if(!$sZMinHH=fLiesHGFarb('td.kalMinH',2)) $sZMinHH=fLiesHGFarb('td.kalMinH');
  if(!$sZMinXF=fLiesFarbe('td.kalMinX',2)) $sZMinXF=fLiesFarbe('td.kalMinX');
  if(!$sZMinXH=fLiesHGFarb('td.kalMinX',2)) $sZMinXH=fLiesHGFarb('td.kalMinX');
  if(!$sZMinKF=fLiesFarbe('td.kalMinK',2)) $sZMinKF=fLiesFarbe('td.kalMinK');
  if(!$sZMinKH=fLiesHGFarb('td.kalMinK',2)) $sZMinKH=fLiesHGFarb('td.kalMinK');
  $sAMinL=fLiesFarbe('a.kalMinL:link'); $sAMinA=fLiesFarbe('a.kalMinL:hover');
  $sAMiKL=fLiesFarbe('a.kalMinK:link'); $sAMiKA=fLiesFarbe('a.kalMinK:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sPageH=fTxtCol('PageH'); if(fSetzHGFarb($sPageH,'body.kalMinikalender')) $bNeu=true;
  $sTMiniR=fTxtCol('TMiniR'); if($sTMiniR!=fLiesRahmFarb('table.kalMini')){
   if(fSetzRahmFarb($sTMiniR,'table.kalMini')) $bNeu=true;
   if(fSetzRahmFarb($sTMiniR,'td.kalMinD,td.kalMinL')) $bNeu=true;
  }
  $sTMiniA=$_POST['TMiniA']; if($sTMiniA!=fLiesRahmArt('table.kalMini')){
   if(fSetzeRahmArt($sTMiniA,'table.kalMini')) $bNeu=true;
   if(fSetzeRahmArt($sTMiniA,'td.kalMinD,td.kalMinL')) $bNeu=true;
  }
  $sMinW= fTxtSiz('MinW');  if(fSetzeWeite($sMinW,'table.kalMini')) $bNeu=true;
  $sMinFS=fTxtSiz('MinFS'); if(fSetzeFontS($sMinFS,'table.kalMini')) $bNeu=true;
  $sZMinDF=fTxtCol('ZMinDF'); if(fSetzeFarbe($sZMinDF,'td.kalMinD',2)) $bNeu=true; if(fSetzeFarbe($sZMinDF,'td.kalMinD')) $bNeu=true;
  $sZMinDH=fTxtCol('ZMinDH'); if(fSetzHGFarb($sZMinDH,'td.kalMinD',2)) $bNeu=true; if(fSetzHGFarb($sZMinDH,'td.kalMinD')) $bNeu=true;
  $sZMinLF=fTxtCol('ZMinLF'); if(fSetzeFarbe($sZMinLF,'td.kalMinL',2)) $bNeu=true; if(fSetzeFarbe($sZMinLF,'td.kalMinL')) $bNeu=true;
  $sZMinLH=fTxtCol('ZMinLH'); if(fSetzHGFarb($sZMinLH,'td.kalMinL',2)) $bNeu=true; if(fSetzHGFarb($sZMinLH,'td.kalMinL')) $bNeu=true;
  $sZMinHF=fTxtCol('ZMinHF'); if(fSetzeFarbe($sZMinHF,'td.kalMinH',2)) $bNeu=true; if(fSetzeFarbe($sZMinHF,'td.kalMinH')) $bNeu=true;
  $sZMinHH=fTxtCol('ZMinHH'); if(fSetzHGFarb($sZMinHH,'td.kalMinH',2)) $bNeu=true; if(fSetzHGFarb($sZMinHH,'td.kalMinH')) $bNeu=true;
  $sZMinXF=fTxtCol('ZMinXF'); if(fSetzeFarbe($sZMinXF,'td.kalMinX',2)) $bNeu=true; if(fSetzeFarbe($sZMinXF,'td.kalMinX')) $bNeu=true;
  $sZMinXH=fTxtCol('ZMinXH'); if(fSetzHGFarb($sZMinXH,'td.kalMinX',2)) $bNeu=true; if(fSetzHGFarb($sZMinXH,'td.kalMinX')) $bNeu=true;
  $sZMinKF=fTxtCol('ZMinKF'); if(fSetzeFarbe($sZMinKF,'td.kalMinK',2)) $bNeu=true; if(fSetzeFarbe($sZMinKF,'td.kalMinK')) $bNeu=true;
  $sZMinKH=fTxtCol('ZMinKH'); if(fSetzHGFarb($sZMinKH,'td.kalMinK',2)) $bNeu=true; if(fSetzHGFarb($sZMinKH,'td.kalMinK')) $bNeu=true;
  $sAMinL=fTxtCol('AMinL'); if(fSetzeFarbe($sAMinL,'a.kalMinL:link')) $bNeu=true;
  $sAMinA=fTxtCol('AMinA'); if(fSetzeFarbe($sAMinA,'a.kalMinL:hover')) $bNeu=true;
  $sAMiKL=fTxtCol('AMiKL'); if(fSetzeFarbe($sAMiKL,'a.kalMinK:link')) $bNeu=true;
  $sAMiKA=fTxtCol('AMiKA'); if(fSetzeFarbe($sAMiKA,'a.kalMinK:hover')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(KALPFAD.'kalStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Msg='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Msg='<p class="admFehl">In die Datei <i>kalStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Msg) $Msg='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
 }//POST
}else $Msg.='<p class="admFehl">Setup-Fehler: Die Datei <i>kalStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die wesentlichsten Funktions- sowie Farb- und Layouteinstellungen.</p>';
echo $Msg.NL;
$sIcon=$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfMiniKal.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Minikalender kann 1 bis 12 aufeinanderfolgende Monate entweder untereinander oder nebeneinander abbilden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kalenderintervall<br />und Anordnung</td>
 <td><input type="text" name="MiniMonate" value="<?php echo $ksMiniMonate?>" style="width:20px;" /> Monate <span class="admMini">(1...12)</span> &nbsp; &nbsp;
 <input class="admRadio" type="radio" name="MiniVertikal" value="1"<?php if($ksMiniVertikal) echo' checked="checked"'?>> untereinander &nbsp;
 <input class="admRadio" type="radio" name="MiniVertikal" value="0"<?php if(!$ksMiniVertikal) echo' checked="checked"'?>> nebeneinander &nbsp;
 in <input type="text" name="MiniReihen" value="<?php echo $ksMiniReihen?>" style="width:20px;" /> Reihen</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Vor den Wochen im Minikalender kann die Wochen-Nummer eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Wochen-Nr.</td>
 <td><input class="admCheck" type="checkbox" name="MiniWochenNr" value="1"<?php if($ksMiniWochenNr) echo' checked="checked"'?>> Wochennummern einblenden &nbsp;
 als <input type="text" name="MiniTxWo" value="<?php echo $ksMiniTxWo?>" style="width:25px;" /> / <input type="text" name="MiniTxNr" value="<?php echo $ksMiniTxNr?>" style="width:25px;" /> &nbsp; <span class="admMini">Empfehlung: <i>Wo Nr</i> &nbsp; oder leer lassen</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn ein Monat nicht mit Montag beginnt und nicht mit Sonntag endet entstehen leere Felder im Minikalender.
Sollen diese Felder leer bleiben oder mit jeweiligen Tagesdatum des vorhergehenden bzw. nachfolgenden Monats aufgefüllt werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatsgrenzen</td>
 <td><input class="admRadio" type="radio" name="MiniFremd" value="0"<?php if(!$ksMiniFremd) echo' checked="checked"'?>> nicht auffüllen &nbsp; &nbsp;
 <input class="admRadio" type="radio" name="MiniFremd" value="1"<?php if($ksMiniFremd) echo' checked="checked"'?>> mit fremden Monatstagen auffüllen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn ein Datum im Minikalender mit einem Termin hinterlegt ist
so erscheint beim Überfahren mit der Maus eine Vorschau auf den Termin.
Aus welchem Feld der Terminstruktur soll der Vorschautext entnommen werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Vorschautext aus</td>
 <td><select name="MiniTextFeld" size="1" style="width:150px;"><option value="0">???</option><?php echo $sOptMLnk?></select> + <select name="MiniTextFld2" size="1" style="width:150px;"><option value="0">---</option><?php echo $sOptMLk2?></select></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Minikalender kann nur die gültige Termine anzeigen
(d.h. nur die Termine, die bei den Einstellungen der Terminliste als nicht abgelaufen vereinbart sind)
oder auch abgelaufene Termine hervorheben und darstellen. </td></tr>
<tr class="admTabl">
 <td class="admSpa1">Terminbereich</td>
 <td><input class="admRadio" type="radio" name="MiniOhneAltes" value="0"<?php if(!$ksMiniOhneAltes) echo' checked="checked"'?>> alle (auch abgelaufene) Termine &nbsp; &nbsp;
 <input class="admRadio" type="radio" name="MiniOhneAltes" value="1"<?php if($ksMiniOhneAltes) echo' checked="checked"'?>> nur gültige Termine</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei einem Klick auf einen Link im Minikalender
soll sich das Kalenderscript öffnen und die Termine anzeigen. In welchem Zielfenster (Target) soll das passieren?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zielfenster</td>
 <td><select name="MiniTarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($ksMiniTarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($ksMiniTarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($ksMiniTarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($ksMiniTarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="kalender"<?php if($ksMiniTarget=='kalender') echo' selected="selected"'?>>kalender: Kalenderfenster</option></select>&nbsp;
 oder anderes Zielfenster <input type="text" name="MiniXTarget" value="<?php echo $ksMiniXTarget?>" style="width:100px;" /> (Target) &nbsp;
 <input class="admRadio" type="checkbox" name="MiniPopup" value="1"<?php if($ksMiniPopup) echo' checked="checked"'?>> als Popupfenster</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern der Minikalender direkt aufgerufen wird (auch in einem i-Frame)
wird als Verweisziel für die Kalendertermine aus dem Minikalender heraus
automatisch das Kalenderscript <i>kalender.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Kalenders hier angeben.<br />
Wenn der Minikalender in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
wird als Verweisziel das aufrufende PHP-Script selbst angenommen,
es sei denn Sie vereinbaren hier ein anderes Verweisziel zur Anzeige der Termine.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input style="width:100%" type="text" name="MiniLink" value="<?php echo $ksMiniLink?>" />
 <div class="admMini">leer lassen oder Scriptname,  eventuell mit absoluter Pfadangabe aber ohne Domain und ohne QueryString</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei einem Klick auf einen Link im Minikalender
kann als Sichtweise eine Terminliste zum entsprechenden Datum angezeigt werden oder aber gleich die Termindetails zum betreffenden Termin.
Sofern aber mehrere Termine an einem Datum vorhanden sind muss bei den Termindetails eventuell ein Kompromiss gemacht werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sichtweise</td>
 <td><input class="admRadio" type="radio" name="MiniSicht" value="0"<?php if($ksMiniSicht=='0') echo' checked="checked"'?>> immer Terminliste anzeigen<br>
 <input class="admRadio" type="radio" name="MiniSicht" value="1"<?php if($ksMiniSicht=='1') echo' checked="checked"'?>> bei einem Termin <i>Termindetails</i>, bei mehreren Terminen <i>Terminliste</i><br>
 <input class="admRadio" type="radio" name="MiniSicht" value="2"<?php if($ksMiniSicht=='2') echo' checked="checked"'?>> immer Termindetails, bei mehreren Terminen irgendeinen davon</td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p style="margin-top:20px;">Die folgenden Farben und Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> editieren.</p>
<form name="farbform" action="konfMiniKal.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Hintergrund um den Minikalender</b> wird (sofern der Minikalender <i>eigenständig</i> läuft und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Der Minikalender erhält einen farbigen Rahmen und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmen und<br>Gitternetzlinien</td>
 <td><select name="TMiniA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTMiniA)?></select> Linien</td>
 <td><input type="text" name="TMiniR" value="<?php echo $sTMiniR?>" style="width:70px">
 <a href="<?php echo fColorRef('TMiniR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTMiniA?> <?php echo $sTMiniR?>;color:<?php echo $sTMiniR?>;background-color:<?php echo $sZMinDH?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Der Minikalender kann eine individuelle Schriftgröße erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schriftgröße</td>
 <td colspan="3"><input type="text" name="MinFS" value="<?php echo $sMinFS?>" style="width:70px"> (Masseinheit <i>em</i> oder <i>px</i> unbedingt angeben!)</td>
 <td class="admMini">Empfehlung: 0.8em</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Der Minikalender kann eine individuelle Breitenangabe erhalten.
Dies ist in den meisten Fällen aber nicht notwendig, da sich die Breite über die Schriftgröße von selbst einregelt.
Bei mehreren Minikalendern untereinander empfiehlt sich für eine einheitliche Breite die Angabe <i>99%</i>.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenbreite</td>
 <td colspan="3"><input type="text" name="MinW" value="<?php echo $sMinW?>" style="width:70px"> (<i>auto</i> oder mit Masseinheit <i>%</i> oder <i>px</i>)</td>
 <td class="admMini">Empfehlung: 99%</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Im Minikalender treten folgende Datumszellen auf.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">normale Datumszelle<br />ohne Termin</td>
 <td><input type="text" name="ZMinDF" value="<?php echo $sZMinDF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinDF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZMinDH" value="<?php echo $sZMinDH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinDH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMinDF?>;background-color:<?php echo $sZMinDH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumszelle mit<br />hinterlegtem Termin</td>
 <td><input type="text" name="ZMinLF" value="<?php echo $sZMinLF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinLF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZMinLH" value="<?php echo $sZMinLH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinLH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMinLF?>;background-color:<?php echo $sZMinLH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumszelle des<br />heutigen Tages</td>
 <td><input type="text" name="ZMinHF" value="<?php echo $sZMinHF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinHF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZMinHH" value="<?php echo $sZMinHH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinHH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMinHF?>;background-color:<?php echo $sZMinHH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Randzelle mit<br />monatsfremdem Datum</td>
 <td><input type="text" name="ZMinXF" value="<?php echo $sZMinXF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinXF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZMinXH" value="<?php echo $sZMinXH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinXH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMinXF?>;background-color:<?php echo $sZMinXH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: Textfarbe<br />unscheinbar (grau)</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Mit Terminen hinterlegte Monatsdatumszahlen im Minikalender werden als Verweis in den Kalender dargestellt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="AMinL" value="<?php echo $sAMinL?>" style="width:70px"> <a href="<?php echo fColorRef('AMinL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="AMinA" value="<?php echo $sAMinA?>" style="width:70px"> <a href="<?php echo fColorRef('AMinA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAMinL?>;background-color:<?php echo $sZMinLH?>;" onmouseover="this.style.color='<?php echo $sAMinA?>'" onmouseout="this.style.color='<?php echo $sAMinL?>'">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Der Minikalender hat zwei Kopfzeilen mit dem jeweiligen Monatsnamen und mit den Wochentagen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeilenzelle</td>
 <td><input type="text" name="ZMinKF" value="<?php echo $sZMinKF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZMinKH" value="<?php echo $sZMinKH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMinKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMinKF?>;background-color:<?php echo $sZMinKH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Im Kopf des Minikalenders gibt es die Links zum Weiterblättern bzw. den Link für den gesamten Monatsauszug im Kalender.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="AMiKL" value="<?php echo $sAMiKL?>" style="width:70px"> <a href="<?php echo fColorRef('AMiKL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="AMiKA" value="<?php echo $sAMiKA?>" style="width:70px"> <a href="<?php echo fColorRef('AMiKA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTMiniR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAMiKL?>;background-color:<?php echo $sZMinKH?>;" onmouseover="this.style.color='<?php echo $sAMiKA?>'" onmouseout="this.style.color='<?php echo $sAMiKL?>'">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fLiesFarbe($w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
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
function fSetzeFarbe($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
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
function fLiesHGFarb($w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
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
function fSetzHGFarb($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmFarb($w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
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