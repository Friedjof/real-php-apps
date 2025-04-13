<?php
include 'hilfsFunktionen.php'; $usDSExTarget='';
echo fSeitenKopf('allgemeine Einstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>','SAl');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $usZeichensatz=UMF_Zeichensatz; $usSqlCharSet=UMF_SqlCharSet; $amSqlCharSet=ADU_SqlCharSet;
 $usDatumsformat=UMF_Datumsformat; $usTimeZoneSet=UMF_TimeZoneSet; $amStripSlashes=ADU_StripSlashes;
 $usCaptcha=UMF_Captcha; $usCaptchaTxFarb=UMF_CaptchaTxFarb; $usCaptchaHgFarb=UMF_CaptchaHgFarb;
 $usCaptchaTyp=UMF_CaptchaTyp; $usCaptchaGrafisch=UMF_CaptchaGrafisch; $usCaptchaNumerisch=UMF_CaptchaNumerisch; $usCaptchaTextlich=UMF_CaptchaTextlich;
 $usTxDSE1=UMF_TxDSE1; $usTxDSE2=UMF_TxDSE2; $usDSELink=UMF_DSELink; $usDSETarget=UMF_DSETarget; $usDSEPopUp=UMF_DSEPopUp; $usDSEPopupX=UMF_DSEPopupX; $usDSEPopupY=UMF_DSEPopupY; $usDSEPopupW=UMF_DSEPopupW; $usDSEPopupH=UMF_DSEPopupH;
 if($usDSETarget!='umfrage'&&$usDSETarget!='_self'&&$usDSETarget!='_parent'&&$usDSETarget!='_top'&&$usDSETarget!='_blank') $usDSExTarget=$usDSETarget;
 $usSchluessel=UMF_Schluessel; $usWarnMeldungen=UMF_WarnMeldungen;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=(int)txtVar('Zeichensatz'); if(fSetzUmfWert($v,'Zeichensatz','')) $bNeu=true;
  $v=txtVar('SqlCharSet'); if(fSetzUmfWert($v,'SqlCharSet',"'")) $bNeu=true;
  $v=txtVar('AdmSqlCharSet'); if(setzAdmWert($v,'SqlCharSet',"'")) $bNeu=true;
  $v=txtVar('TimeZoneSet'); if(fSetzUmfWert($v,'TimeZoneSet',"'")) $bNeu=true;
  $v=txtVar('Datumsformat'); if(fSetzUmfWert($v,'Datumsformat',"'")) $bNeu=true;
  $v=(int)txtVar('StripSlashes'); if(setzAdmWert(($v?true:false),'StripSlashes','')) $bNeu=true;
  $v=txtVar('Captcha'); if(fSetzUmfWert(($v?true:false),'Captcha','')) $bNeu=true;
  $v=txtVar('CaptchaTyp'); if(fSetzUmfWert($v,'CaptchaTyp',"'")) $bNeu=true;
  $v=txtVar('CaptchaGrafisch'); if(fSetzUmfWert(($v?true:false)||($usCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
  $v=txtVar('CaptchaNumerisch'); if(fSetzUmfWert(($v?true:false)||($usCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
  $v=txtVar('CaptchaTextlich'); if(fSetzUmfWert(($v?true:false)||($usCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
  $v=txtVar('CaptchaTxFarb'); if(fSetzUmfWert($v,'CaptchaTxFarb',"'")) $bNeu=true;
  $v=txtVar('CaptchaHgFarb'); if(fSetzUmfWert($v,'CaptchaHgFarb',"'")) $bNeu=true;
  $v=txtVar('TxDSE1'); if(fSetzUmfWert($v,'TxDSE1','"')) $bNeu=true;
  $v=txtVar('TxDSE2'); if(fSetzUmfWert($v,'TxDSE2','"')) $bNeu=true;
  $v=txtVar('DSELink'); if(fSetzUmfWert($v,'DSELink',"'")) $bNeu=true;
  if($v=txtVar('DSETarget')) $usDSExTarget=''; else{$v=txtVar('DSExTarget'); $usAktuellexTarget=$v;} if(fSetzUmfWert($v,'DSETarget',"'")) $bNeu=true;
  $v=(int)txtVar('DSEPopUp'); if(fSetzUmfWert(($v?true:false),'DSEPopUp','')) $bNeu=true;
  $v=max((int)txtVar('DSEPopupX'),0); if(fSetzUmfWert($v,'DSEPopupX','')) $bNeu=true; $v=max((int)txtVar('DSEPopupH'),100); if(fSetzUmfWert($v,'DSEPopupH','')) $bNeu=true;
  $v=max((int)txtVar('DSEPopupY'),0); if(fSetzUmfWert($v,'DSEPopupY','')) $bNeu=true; $v=max((int)txtVar('DSEPopupW'),100); if(fSetzUmfWert($v,'DSEPopupW','')) $bNeu=true;
  $v=(int)txtVar('WarnMeldungen'); if(fSetzUmfWert(($v?true:false),'WarnMeldungen','')) $bNeu=true;
  $v=sprintf('%06d',txtVar('Schluessel')); if(fSetzUmfWert($v,'Schluessel',"'")) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> durfte nicht geschrieben werden (Rechteproblem)!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Grundeinstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Grundeinstellungen bleiben unverändert.</p>';
}

//Scriptausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Stellen Sie die Grundfunktion des Umfrage-Scripts passend ein.</p>';
echo $sMeld.NL;
?>

<form name="farbform" action="konfAllgemein.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgaben des Umfrage-Scripts erfolgen normalerweise in der Kodierung des Standardzeichensatzes. Das ist üblicherweise <i>ISO-8859-1</i> (<i>Western</i>).
Falls Ihre Umgebung des Umfrage-Scripts einen anderen Zeichensatz erfordert (z.B. bei Einbindung in ein CMS) können Sie für die Ausgaben des Umfrage-Scripts im Besucherbereich eine alternative Zeichenkodierung einstellen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz</td>
 <td><select name="Zeichensatz" size="1"><option value="0">Standard</option><option value="1"<?php if($usZeichensatz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($usZeichensatz==2) echo' selected="selected"'?>>UTF-8</option></select> <span class="admMini">(Empfehlung: Standard)</span> <a href="<?php echo ADU_Hilfe ?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">In seltenen Fällen kann es nötig sein, die MySQL-Datenbankverbindung im Besucherbereich zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.
Tragen Sie dann hier diesen Zeichensatz ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz</td>
 <td><input type="text" name="SqlCharSet" value="<?php echo $usSqlCharSet?>" style="width:11em;" /> Zeichensatz für MySQL <span class="admMini">(Empfehlung: leer lassen oder z.B. <i>latin1</i> oder <i>utf8</i> bzw. <i>utf8mb4</i>)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">In seltenen Fällen kann es nötig sein, die MySQL-Datenbankverbindung im Administationsbereich zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.
Tragen Sie dann hier diesen Zeichensatz ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz<br>in der Administration</td>
 <td><input type="text" name="AdmSqlCharSet" value="<?php echo $amSqlCharSet?>" style="width:11em;" /> Zeichensatz für MySQL <span class="admMini">(Empfehlung: leer lassen oder z.B. <i>latin1</i> fast nie <i>utf8</i> bzw. <i>utf8mb4</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Einige PHP-Systeme behandeln eingegebenen \-Backslash-Zeichen nicht korrekt. Manchmal werden die eingegebenen \-Zeichen unzulässig entfernt, machmal auch zu \\-Zeichen verdoppelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Backslash-Korrektur</td>
 <td><input type="radio" class="admRadio" name="StripSlashes" value="0"<?php if(!$amStripSlashes) echo ' checked="checked"'?> /> \-Zeichen beibehalten &nbsp; <input type="radio" class="admRadio" name="StripSlashes" value="1"<?php if($amStripSlashes) echo ' checked="checked"'?> /> doppelte \\-Zeichen entfernen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Beim etwaigen Speichern der Umfrageergebnisse werden Datum und Uhrzeit der Umfrage vermerkt. In welchem Format sollen diese Angaben gespeichert werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitzone für PHP</td>
 <td><input type="text" name="TimeZoneSet" value="<?php echo $usTimeZoneSet?>" style="width:14em;" /> Muster: <i>Europe/Berlin</i>, <i>Europe/Vienna</i> oder <i>Europe/Zurich</i> o.ä.
 <div class="admMini">gültige PHP-Zeitzone gemäß <a style="color:#004" href="http://www.php.net/manual/de/timezones.php" target="hilfe" onclick="hlpWin(this.href);return false;">http://www.php.net/manual/de/timezones.php</a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td><select name="Datumsformat" size="1" style="width:14em">
 <option value="d.m.y"<?php if($usDatumsformat=="d.m.y") echo' selected="selected"'?>>TT.MM.JJ</option>
 <option value="d.m.Y"<?php if($usDatumsformat=="d.m.Y") echo' selected="selected"'?>>TT.MM.JJJJ</option>
 <option value="d.m.y H:i"<?php if($usDatumsformat=="d.m.y H:i") echo' selected="selected"'?>>TT.MM.JJ hh:mm</option>
 <option value="d.m.Y H:i"<?php if($usDatumsformat=="d.m.Y H:i") echo' selected="selected"'?>>TT.MM.JJJJ hh:mm</option>
 <option value="d.m.y H:i:s"<?php if($usDatumsformat=="d.m.y H:i:s") echo' selected="selected"'?>>TT.MM.JJ hh:mm:ss</option>
 <option value="d.m.Y H:i:s"<?php if($usDatumsformat=="d.m.Y H:i:s") echo' selected="selected"'?>>TT.MM.JJJJ hh:mm:ss</option>
 <option value="y-m-d"<?php if($usDatumsformat=="y-m-d") echo' selected="selected"'?>>JJ-MM-TT</option>
 <option value="Y-m-d"<?php if($usDatumsformat=="Y-m-d") echo' selected="selected"'?>>JJJJ-MM-TT</option>
 <option value="y-m-d H:i"<?php if($usDatumsformat=="y-m-d H:i") echo' selected="selected"'?>>JJ-MM-TT hh:mm</option>
 <option value="Y-m-d H:i"<?php if($usDatumsformat=="Y-m-d H:i") echo' selected="selected"'?>>JJJJ-MM-TT hh:mm</option>
 <option value="y-m-d H:i:s"<?php if($usDatumsformat=="y-m-d H:i:s") echo' selected="selected"'?>>JJ-MM-TT hh:mm:ss</option>
 <option value="Y-m-d H:i:s"<?php if($usDatumsformat=="Y-m-d H:i:s") echo' selected="selected"'?>>JJJJ-MM-TT hh:mm:ss</option>
 </select></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das dem Script zugrundeliegende PHP-Sprachsystem gibt bei Systemfehlern Fehlermeldungen bzw. bei Sprachverletzungen Warnmeldungen aus.
Die Warnmeldungen können ein- oder ausgeschaltet sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Warnmeldungen</td>
 <td><input class="admRadio" type="radio" name="WarnMeldungen" value="0"<?php if(!$usWarnMeldungen) echo' checked="checked"'?> /> Warnungen aus &nbsp; &nbsp; <input class="admRadio" type="radio" name="WarnMeldungen" value="1"<?php if($usWarnMeldungen) echo' checked="checked"'?> /> Warnungen ein &nbsp; &nbsp;
 <span class="admMini">(Empfehlung: ausgeschaltet)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter den Formuaren dieses Programmes gesonderte Einwilligungszeilen zum Datenschutz einzublenden.<a name="DSE"></a></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />erklärung<a name="DSE"></a></td>
 <td>
  <input type="text" name="TxDSE1" value="<?php echo $usTxDSE1?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich habe die <span style="white-space:nowrap">[L]Datenschutzerklärung[/L]</span> gelesen und stimme ihr zu.</i></div>
  <div class="admMini">Hinweis: <i>[L]</i> und <i>[/L]</i> stehen für  Linkanfang und Linkende und sind hier zwingend notwendig.</div>
  <div style="margin-top:6px;margin-bottom:2px">Linkadresse zur Datenschutzerklärung auf Ihrer Webseite: &nbsp; <span class="admMini">notfalls einschließlich https://</span></div>
  <input type="text" name="DSELink" value="<?php echo $usDSELink?>" style="width:98%" />
  <div style="margin-top:6px;margin-bottom:2px">Zielfenster für den Link zur Datenschutzerklärung:</div>
  <select name="DSETarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($usDSETarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($usDSETarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($usDSETarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($usDSETarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="umfrage"<?php if($usDSETarget=='umfrage') echo' selected="selected"'?>>umfrage: Umfragenfenster</option></select>&nbsp;
  oder anderes Zielfenster  <input type="text" name="DSExTarget" value="<?php echo $usDSExTarget?>" style="width:100px;" /> (Target)
  <div style="margin-top:4px"><input class="admRadio" type="checkbox" name="DSEPopUp" value="1"<?php if($usDSEPopUp) echo' checked="checked"'?>> als Popupfenster &nbsp;
  <input type="text" name="DSEPopupW" value="<?php echo $usDSEPopupW?>" size="4" style="width:32px" /> px breit &nbsp; <input type="text" name="DSEPopupH" value="<?php echo $usDSEPopupH?>" size="4" style="width:32px" /> px hoch &nbsp; &nbsp;
  <input type="text" name="DSEPopupY" value="<?php echo $usDSEPopupY?>" size="4" style="width:24px" /> px von oben &nbsp; <input type="text" name="DSEPopupX" value="<?php echo $usDSEPopupX?>" size="4" style="width:24px" /> px von links</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenverarbeitung<br/>und -speicherung</td>
 <td>
  <input type="text" name="TxDSE2" value="<?php echo $usTxDSE2?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich bin mit der Verarbeitung und Speicherung meiner persönlichen Daten im Rahmen der Datenschutzerklärung einverstanden.</i></div>
  <div class="admMini">Hinweis: Platzhalter <i>[L]</i> und <i>[L]</i> für die Verlinkung sind wie oben möglich aber hier <i>nicht</i> zwingend.</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen zur Benutzeranmeldung bzw. Teilnehmerregistrierung ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($usCaptcha) echo' checked="checked"'?> /> verwenden,
 bevorzugter Captchatyp: <select name="CaptchaTyp" size="1"><option value="G<?php if($usCaptchaTyp=='G') echo '" selected="selected';?>">grafisches Captcha</option><option value="N<?php if($usCaptchaTyp=='N') echo '" selected="selected';?>">mathematisches Captcha</option><option value="T<?php if($usCaptchaTyp=='T') echo '" selected="selected';?>">textliches Captcha</option></select></div>
 <div style="margin-top:5px;margin-bottom:5px;">Alternativen anbieten:
 <input class="admCheck" type="checkbox" name="CaptchaGrafisch" value="1"<?php if($usCaptchaGrafisch) echo' checked="checked"'?> /> grafisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaNumerisch" value="1"<?php if($usCaptchaNumerisch) echo' checked="checked"'?> /> mathematisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaTextlich" value="1"<?php if($usCaptchaTextlich) echo' checked="checked"'?> /> textliches Captcha</div>
 Grafikmuster <span style="color:<?php echo $usCaptchaTxFarb?>;background-color:<?php echo $usCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span> &nbsp; &nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $usCaptchaTxFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($usCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="javascript:ColWin()"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> &nbsp; &nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $usCaptchaHgFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($usCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="javascript:ColWin()"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Geheimschlüssel</td>
 <td><div style="float:left;padding-right:4px"><input type="text" name="Schluessel" value="<?php echo $usSchluessel?>" style="width:5em;color:#888888" /></div>
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
 if($w!=constant('ADU_'.$n)){
  $p=strpos($sWerte,'ADU_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADU_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}?>