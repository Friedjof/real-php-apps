<?php
include 'hilfsFunktionen.php'; $fsDSExTarget='';
echo fSeitenKopf('allgemeine Einstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>','KAg');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $fsZeichensatz=FRA_Zeichensatz; $fsZeichnsNorm=FRA_ZeichnsNorm; $fsSqlCharSet=FRA_SqlCharSet;
 $fsDatumsformat=FRA_Datumsformat; $fsTimeZoneSet=FRA_TimeZoneSet;
 $fsDezimalzeichen=FRA_Dezimalzeichen; $amStripSlashes=ADF_StripSlashes;//$fsNummerStellen=FRA_NummerStellen;
 $fsCaptcha=FRA_Captcha; $fsCaptchaTxFarb=FRA_CaptchaTxFarb; $fsCaptchaHgFarb=FRA_CaptchaHgFarb;
 $fsCaptchaTyp=FRA_CaptchaTyp; $fsCaptchaGrafisch=FRA_CaptchaGrafisch; $fsCaptchaNumerisch=FRA_CaptchaNumerisch; $fsCaptchaTextlich=FRA_CaptchaTextlich;
 $fsSchluessel=FRA_Schluessel; $fsWarnMeldungen=FRA_WarnMeldungen;
 $fsTxDSE1=FRA_TxDSE1; $fsTxDSE2=FRA_TxDSE2; $fsDSELink=FRA_DSELink; $fsDSETarget=FRA_DSETarget; $fsDSEPopUp=FRA_DSEPopUp; $fsDSEPopupX=FRA_DSEPopupX; $fsDSEPopupY=FRA_DSEPopupY; $fsDSEPopupW=FRA_DSEPopupW; $fsDSEPopupH=FRA_DSEPopupH;
 if($fsDSETarget!='testfragen'&&$fsDSETarget!='_self'&&$fsDSETarget!='_parent'&&$fsDSETarget!='_top'&&$fsDSETarget!='_blank') $fsDSExTarget=$fsDSETarget;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=(int)txtVar('Zeichensatz'); if(fSetzFraWert($v,'Zeichensatz','')) $bNeu=true;
  $v=(int)txtVar('ZeichnsNorm'); if(fSetzFraWert($v,'ZeichnsNorm','')) $bNeu=true;
  $v=txtVar('SqlCharSet'); if(fSetzFraWert($v,'SqlCharSet',"'")) $bNeu=true;
  $v=txtVar('TimeZoneSet'); if(fSetzFraWert($v,'TimeZoneSet',"'")) $bNeu=true;
  $v=txtVar('Datumsformat'); if(fSetzFraWert($v,'Datumsformat',"'")) $bNeu=true;
  $v=txtVar('Dezimalzeichen'); if(fSetzFraWert($v,'Dezimalzeichen',"'")) $bNeu=true;
  $v=(int)txtVar('StripSlashes'); if(setzAdmWert(($v?true:false),'StripSlashes','')) $bNeu=true;
  //$v=txtVar('NummerStellen'); if(fSetzFraWert($v,'NummerStellen',"'")) $bNeu=true;
  $v=txtVar('Captcha'); if(fSetzFraWert(($v?true:false),'Captcha','')) $bNeu=true;
  $v=txtVar('CaptchaTyp'); if(fSetzFraWert($v,'CaptchaTyp',"'")) $bNeu=true;
  $v=txtVar('CaptchaGrafisch'); if(fSetzFraWert(($v?true:false)||($fsCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
  $v=txtVar('CaptchaNumerisch'); if(fSetzFraWert(($v?true:false)||($fsCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
  $v=txtVar('CaptchaTextlich'); if(fSetzFraWert(($v?true:false)||($fsCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
  $v=txtVar('CaptchaTxFarb'); if(fSetzFraWert($v,'CaptchaTxFarb',"'")) $bNeu=true;
  $v=txtVar('CaptchaHgFarb'); if(fSetzFraWert($v,'CaptchaHgFarb',"'")) $bNeu=true;
  $v=(int)txtVar('WarnMeldungen'); if(fSetzFraWert(($v?true:false),'WarnMeldungen','')) $bNeu=true;
  $v=txtVar('TxDSE1'); if(fSetzFraWert($v,'TxDSE1','"')) $bNeu=true;
  $v=txtVar('TxDSE2'); if(fSetzFraWert($v,'TxDSE2','"')) $bNeu=true;
  $v=txtVar('DSELink'); if(fSetzFraWert($v,'DSELink',"'")) $bNeu=true;
  if($v=txtVar('DSETarget')) $fsDSExTarget=''; else{$v=txtVar('DSExTarget'); $fsAktuellexTarget=$v;} if(fSetzFraWert($v,'DSETarget',"'")) $bNeu=true;
  $v=(int)txtVar('DSEPopUp'); if(fSetzFraWert(($v?true:false),'DSEPopUp','')) $bNeu=true;
  $v=max((int)txtVar('DSEPopupX'),0); if(fSetzFraWert($v,'DSEPopupX','')) $bNeu=true; $v=max((int)txtVar('DSEPopupH'),100); if(fSetzFraWert($v,'DSEPopupH','')) $bNeu=true;
  $v=max((int)txtVar('DSEPopupY'),0); if(fSetzFraWert($v,'DSEPopupY','')) $bNeu=true; $v=max((int)txtVar('DSEPopupW'),100); if(fSetzFraWert($v,'DSEPopupW','')) $bNeu=true;
  $v=sprintf('%06d',txtVar('Schluessel')); if(fSetzFraWert($v,'Schluessel',"'")) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Grundeinstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Grundeinstellungen bleiben unverändert.</p>';
}

//Scriptausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Stellen Sie die Grundfunktion des Testfragen-Scripts passend ein.</p>';
echo $sMeld.NL;
?>

<form name="farbform" action="konfAllgemein.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgaben des Testfragen-Scripts erfolgen normalerweise in der Kodierung des Standardzeichensatzes. Das ist üblicherweise <i>ISO-8859-1</i> (<i>Western</i>).
Falls Ihre Umgebung des Testfragen-Scripts einen anderen Zeichensatz erfordert (z.B. bei Einbindung in ein CMS) können Sie für die Ausgaben des Testfragen-Scripts im Besucherbereich eine alternative Zeichenkodierung einstellen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz</td>
 <td><select name="Zeichensatz" size="1"><option value="0">Standard</option><option value="1"<?php if($fsZeichensatz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($fsZeichensatz==2) echo' selected="selected"'?>>UTF-8</option></select> <span class="admMini">(Empfehlung: Standard)</span> <a href="<?php echo ADF_Hilfe ?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die MySQL-Datenbankverbindung erfolgt seitens des Testfragen-Scripts normalerweise ohne einen erzwungenen besonderen Zeichensatz über die Standardverbindung zwischen PHP und MySQL und wird mit dem üblichen Standard-Zeichensatz (meist <i>latin1</i>) angenommen.
Falls Ihre Datenbankanbindung des Testfragen-Scripts abweichend auf einen anderen Zeichensatz eingestellt ist (z.B. bei Einbindung des Scripts in ein CMS, das die Datenbankverbindung umstellt) können Sie versuchen, dem Testfragen-Scripts die verwendete alternative Zeichenkodierung der Datenbankverbindung hier mitzuteilen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">DB-Zeichensatz</td>
 <td><select name="ZeichnsNorm" size="1"><option value="0">Standard</option><option value="1"<?php if($fsZeichnsNorm==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($fsZeichnsNorm==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz der Datenbankverbindung im Normal-Fenster <span class="admMini">(Empfehlung: Standard)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">In extrem seltenen Fällen kann es nötig sein, die MySQL-Datenbankverbindung zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.
Tragen Sie dann hier diesen Zeichensatz ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz</td>
 <td><input type="text" name="SqlCharSet" value="<?php echo $fsSqlCharSet?>" style="width:11em;" /> Zeichensatz für MySQL <span class="admMini">(Empfehlung: leer lassen oder z.B. <i>latin1</i>, selten auch <i>utf8</i> bzw. <i>utf8mb4</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Einige PHP-Systeme behandeln eingegebenen \-Backslash-Zeichen nicht korrekt. Manchmal werden die eingegebenen \-Zeichen unzulässig entfernt, machmal auch zu \\-Zeichen verdoppelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Backslash-Korrektur</td>
 <td><input type="radio" class="admRadio" name="StripSlashes" value="0"<?php if(!$amStripSlashes) echo ' checked="checked"'?> /> \-Zeichen beibehalten &nbsp; <input type="radio" class="admRadio" name="StripSlashes" value="1"<?php if($amStripSlashes) echo ' checked="checked"'?> /> doppelte \\-Zeichen entfernen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Beim etwaigen Speichern der Testergebnisse werden Datum und Uhrzeit des Tests vermerkt.
In welchem Format sollen diese Angaben gespeichert werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitzone für PHP</td>
 <td><input type="text" name="TimeZoneSet" value="<?php echo $fsTimeZoneSet?>" style="width:14em;" /> Muster: <i>Europe/Berlin</i>, <i>Europe/Vienna</i> oder <i>Europe/Zurich</i> o.ä.
 <div class="admMini">gültige PHP-Zeitzone gemäß <a style="color:#004" href="http://www.php.net/manual/de/timezones.php" target="hilfe" onclick="hlpWin(this.href);return false;">http://www.php.net/manual/de/timezones.php</a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td><select name="Datumsformat" size="1" style="width:14em">
 <option value="d.m.y"<?php if($fsDatumsformat=="d.m.y") echo' selected="selected"'?>>TT.MM.JJ</option>
 <option value="d.m.Y"<?php if($fsDatumsformat=="d.m.Y") echo' selected="selected"'?>>TT.MM.JJJJ</option>
 <option value="d.m.y H:i"<?php if($fsDatumsformat=="d.m.y H:i") echo' selected="selected"'?>>TT.MM.JJ hh:mm</option>
 <option value="d.m.Y H:i"<?php if($fsDatumsformat=="d.m.Y H:i") echo' selected="selected"'?>>TT.MM.JJJJ hh:mm</option>
 <option value="d.m.y H:i:s"<?php if($fsDatumsformat=="d.m.y H:i:s") echo' selected="selected"'?>>TT.MM.JJ hh:mm:ss</option>
 <option value="d.m.Y H:i:s"<?php if($fsDatumsformat=="d.m.Y H:i:s") echo' selected="selected"'?>>TT.MM.JJJJ hh:mm:ss</option>
 <option value="y-m-d"<?php if($fsDatumsformat=="y-m-d") echo' selected="selected"'?>>JJ-MM-TT</option>
 <option value="Y-m-d"<?php if($fsDatumsformat=="Y-m-d") echo' selected="selected"'?>>JJJJ-MM-TT</option>
 <option value="y-m-d H:i"<?php if($fsDatumsformat=="y-m-d H:i") echo' selected="selected"'?>>JJ-MM-TT hh:mm</option>
 <option value="Y-m-d H:i"<?php if($fsDatumsformat=="Y-m-d H:i") echo' selected="selected"'?>>JJJJ-MM-TT hh:mm</option>
 <option value="y-m-d H:i:s"<?php if($fsDatumsformat=="y-m-d H:i:s") echo' selected="selected"'?>>JJ-MM-TT hh:mm:ss</option>
 <option value="Y-m-d H:i:s"<?php if($fsDatumsformat=="Y-m-d H:i:s") echo' selected="selected"'?>>JJJJ-MM-TT hh:mm:ss</option>
 </select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Dezimalzeichen</td>
 <td><select name="Dezimalzeichen" size="1">
 <option value=","<?php if($fsDezimalzeichen==",") echo' selected="selected"'?>>,</option>
 <option value="."<?php if($fsDezimalzeichen==".") echo' selected="selected"'?>>.</option>
 </select> <span class="admMini">Komma oder Punkt</span></td>
</tr>

<!--
<tr class="admTabl"><td colspan="2" class="admSpa2">Über oder unter den Fragen/Antworten kann die Nummer der aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Stellenanzahl</td>
 <td><select name="NummerStellen" size="1">
   <option value="0"></option>
   <option value="01<?php if($fsNummerStellen=='01') echo '" selected="selected'?>">1</option>
   <option value="02<?php if($fsNummerStellen=='02') echo '" selected="selected'?>">2</option>
   <option value="03<?php if($fsNummerStellen=='03') echo '" selected="selected'?>">3</option>
   <option value="04<?php if($fsNummerStellen=='04') echo '" selected="selected'?>">4</option>
   <option value="05<?php if($fsNummerStellen=='05') echo '" selected="selected'?>">5</option>
  </select> Stellen der Fragennummer anzeigen</td>
</tr> -->

<tr class="admTabl"><td colspan="2" class="admSpa2">Das dem Script zugrundeliegende PHP-Sprachsystem gibt bei Systemfehlern Fehlermeldungen bzw. bei Sprachverletzungen Warnmeldungen aus.
Die Warnmeldungen können ein- oder ausgeschaltet sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Warnmeldungen</td>
 <td><input class="admRadio" type="radio" name="WarnMeldungen" value="0"<?php if(!$fsWarnMeldungen) echo' checked="checked"'?> /> Warnungen aus &nbsp; &nbsp; <input class="admRadio" type="radio" name="WarnMeldungen" value="1"<?php if($fsWarnMeldungen) echo' checked="checked"'?> /> Warnungen ein &nbsp; &nbsp;
 <span class="admMini">(Empfehlung: ausgeschaltet)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter den Formuaren dieses Programmes gesonderte Einwilligungszeilen zum Datenschutz einzublenden.<a name="DSE"></a></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />erklärung<a name="DSE"></a></td>
 <td>
  <input type="text" name="TxDSE1" value="<?php echo $fsTxDSE1?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich habe die <span style="white-space:nowrap">[L]Datenschutzerklärung[/L]</span> gelesen und stimme ihr zu.</i></div>
  <div class="admMini">Hinweis: <i>[L]</i> und <i>[/L]</i> stehen für  Linkanfang und Linkende und sind hier zwingend notwendig.</div>
  <div style="margin-top:6px;margin-bottom:2px">Linkadresse zur Datenschutzerklärung auf Ihrer Webseite: &nbsp; <span class="admMini">notfalls einschließlich https://</span></div>
  <input type="text" name="DSELink" value="<?php echo $fsDSELink?>" style="width:98%" />
  <div style="margin-top:6px;margin-bottom:2px">Zielfenster für den Link zur Datenschutzerklärung:</div>
  <select name="DSETarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($fsDSETarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($fsDSETarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($fsDSETarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($fsDSETarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="testfragen"<?php if($fsDSETarget=='testfragen') echo' selected="selected"'?>>testfragen: Testfragenfenster</option></select>&nbsp;
  oder anderes Zielfenster  <input type="text" name="DSExTarget" value="<?php echo $fsDSExTarget?>" style="width:100px;" /> (Target)
  <div style="margin-top:4px"><input class="admRadio" type="checkbox" name="DSEPopUp" value="1"<?php if($fsDSEPopUp) echo' checked="checked"'?>> als Popupfenster &nbsp;
  <input type="text" name="DSEPopupW" value="<?php echo $fsDSEPopupW?>" size="4" style="width:32px" /> px breit &nbsp; <input type="text" name="DSEPopupH" value="<?php echo $fsDSEPopupH?>" size="4" style="width:32px" /> px hoch &nbsp; &nbsp;
  <input type="text" name="DSEPopupY" value="<?php echo $fsDSEPopupY?>" size="4" style="width:24px" /> px von oben &nbsp; <input type="text" name="DSEPopupX" value="<?php echo $fsDSEPopupX?>" size="4" style="width:24px" /> px von links</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenverarbeitung<br/>und -speicherung</td>
 <td>
  <input type="text" name="TxDSE2" value="<?php echo $fsTxDSE2?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich bin mit der Verarbeitung und Speicherung meiner persönlichen Daten im Rahmen der Datenschutzerklärung einverstanden.</i></div>
  <div class="admMini">Hinweis: Platzhalter <i>[L]</i> und <i>[L]</i> für die Verlinkung sind wie oben möglich aber hier <i>nicht</i> zwingend.</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen zur Benutzeranmeldung bzw. Teilnehmerregistrierung ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($fsCaptcha) echo' checked="checked"'?> /> verwenden,
 bevorzugter Captchatyp: <select name="CaptchaTyp" size="1"><option value="G<?php if($fsCaptchaTyp=='G') echo '" selected="selected';?>">grafisches Captcha</option><option value="N<?php if($fsCaptchaTyp=='N') echo '" selected="selected';?>">mathematisches Captcha</option><option value="T<?php if($fsCaptchaTyp=='T') echo '" selected="selected';?>">textliches Captcha</option></select></div>
 <div style="margin-top:5px;margin-bottom:5px;">Alternativen anbieten:
 <input class="admCheck" type="checkbox" name="CaptchaGrafisch" value="1"<?php if($fsCaptchaGrafisch) echo' checked="checked"'?> /> grafisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaNumerisch" value="1"<?php if($fsCaptchaNumerisch) echo' checked="checked"'?> /> mathematisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaTextlich" value="1"<?php if($fsCaptchaTextlich) echo' checked="checked"'?> /> textliches Captcha</div>
 Grafikmuster <span style="color:<?php echo $fsCaptchaTxFarb?>;background-color:<?php echo $fsCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span> &nbsp; &nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $fsCaptchaTxFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($fsCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="javascript:ColWin()"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> &nbsp; &nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $fsCaptchaHgFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($fsCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="javascript:ColWin()"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Geheimschlüssel</td>
 <td><div style="float:left"><input type="text" name="Schluessel" value="<?php echo $fsSchluessel?>" style="width:5em;color:#888888" /></div>
 <div class="admMini">Niemals manuell verändern!! Nur auf den notierten Wert setzen nach einer kompletten Rekonstruktion des Scripts bei noch vorhandenen Daten.</div></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADF_'.$n)){
  $p=strpos($sWerte,'ADF_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADF_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}?>