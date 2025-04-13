<?php
include 'hilfsFunktionen.php'; $mpDSExTarget='';
echo fSeitenKopf('Allgemeine Einstellungen','<script type="text/javascript">
 function colWin(sURL){cWin=window.open(sURL,"color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes"); cWin.focus();}
</script>','KAg');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Stellen Sie die Grundfunktion des Marktplatz-Scripts passend ein.'; $MTyp='Meld'; $mpWarnMeldungen=MP_WarnMeldungen;
 $mpEcho=MP_Echo; $mpZeichensatz=MP_Zeichensatz; $mpZeichnsNorm=MP_ZeichnsNorm; $mpZeichnsPopf=MP_ZeichnsPopf; $mpZeichnsExtLink=MP_ZeichnsExtLink; $mpSqlCharSet=MP_SqlCharSet;
 $mpTxKeinSegment=MP_TxKeinSegment; $mpTxNummerUngueltig=MP_TxNummerUngueltig; $mpErrorPage=MP_ErrorPage;
 $mpTxAMetaKey=MP_TxAMetaKey; $mpTxAMetaDes=MP_TxAMetaDes; $mpCanoLink=MP_CanoLink; $mpCanoPopup=MP_CanoPopup; $mpSef=MP_Sef;
 $mpTimeZoneSet=MP_TimeZoneSet; $mpDatumsformat=MP_Datumsformat; $mpJahrhundert=MP_Jahrhundert;
 $mpDezimalstellen=MP_Dezimalstellen; $mpDezimalzeichen=MP_Dezimalzeichen; $mpTausendzeichen=MP_Tausendzeichen; $mpZahlLeer=MP_ZahlLeer;
 $mpWaehrung=MP_Waehrung; $mpPreisLeer=MP_PreisLeer; $mpPLZLaenge=MP_PLZLaenge; $mpNummerStellen=MP_NummerStellen; $mpNummerMitSeg=MP_NummerMitSeg;
 $mpPopupBreit=MP_PopupBreit; $mpPopupHoch=MP_PopupHoch; $mpPopupX=MP_PopupX; $mpPopupY=MP_PopupY;
 $mpDruckPopup=MP_DruckPopup; $mpDruckLFarbig=MP_DruckLFarbig; $mpDruckDFarbig=MP_DruckDFarbig;
 $mpCaptcha=MP_Captcha; $mpCaptchaTxFarb=MP_CaptchaTxFarb; $mpCaptchaHgFarb=MP_CaptchaHgFarb; $mpSchluessel=MP_Schluessel;
 $mpTxDSE1=MP_TxDSE1; $mpTxDSE2=MP_TxDSE2; $mpDSELink=MP_DSELink; $mpDSETarget=MP_DSETarget; $mpDSEPopUp=MP_DSEPopUp; $mpDSEPopupX=MP_DSEPopupX; $mpDSEPopupY=MP_DSEPopupY; $mpDSEPopupW=MP_DSEPopupW; $mpDSEPopupH=MP_DSEPopupH;
 if($mpDSETarget!='marktplatz'&&$mpDSETarget!='_self'&&$mpDSETarget!='_parent'&&$mpDSETarget!='_top'&&$mpDSETarget!='_blank') $mpDSExTarget=$mpDSETarget;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=(int)txtVar('WarnMeldungen'); if(fSetzMPWert(($v?true:false),'WarnMeldungen','')) $bNeu=true;
 $v=(int)txtVar('Echo'); if(fSetzMPWert(($v?true:false),'Echo','')) $bNeu=true;
 $v=(int)txtVar('Zeichensatz'); if(fSetzMPWert($v,'Zeichensatz','')) $bNeu=true;
 $v=txtVar('SqlCharSet'); if(fSetzMPWert($v,'SqlCharSet',"'")) $bNeu=true;
 $v=(int)txtVar('ZeichnsNorm'); if(fSetzMPWert($v,'ZeichnsNorm','')) $bNeu=true;
 $v=(int)txtVar('ZeichnsPopf'); if(fSetzMPWert($v,'ZeichnsPopf','')) $bNeu=true;
 $v=(int)txtVar('ZeichnsExtLink'); if(fSetzMPWert($v,'ZeichnsExtLink','')) $bNeu=true;
 $v=(int)txtVar('Datumsformat'); if(fSetzMPWert($v,'Datumsformat','')) $bNeu=true;
 $v=txtVar('TimeZoneSet'); if(fSetzMPWert($v,'TimeZoneSet',"'")) $bNeu=true;
 $v=txtVar('TxKeinSegment'); if(fSetzMPWert($v,'TxKeinSegment','"')) $bNeu=true;
 $v=txtVar('TxNummerUngueltig'); if(fSetzMPWert($v,'TxNummerUngueltig','"')) $bNeu=true;
 $v=(int)txtVar('ErrorPage');  if(fSetzMPWert(($v?true:false),'ErrorPage','')) $bNeu=true;
 $v=txtVar('TxAMetaKey'); if(fSetzMPWert($v,'TxAMetaKey','"')) $bNeu=true;
 $v=txtVar('TxAMetaDes'); if(fSetzMPWert($v,'TxAMetaDes','"')) $bNeu=true;
 $v=txtVar('CanoLink'); if(fSetzMPWert(($v?true:false),'CanoLink','')) $bNeu=true;
 $v=txtVar('CanoPopup'); if(fSetzMPWert(($v?true:false),'CanoPopup','')) $bNeu=true;
 $v=txtVar('Sef'); if(fSetzMPWert(($v?true:false),'Sef','')) $bNeu=true;
 $v=txtVar('Jahrhundert'); if(fSetzMPWert(($v?true:false),'Jahrhundert','')) $bNeu=true;
 $v=txtVar('Dezimalstellen'); if(fSetzMPWert($v,'Dezimalstellen',"'")) $bNeu=true;
 $v=txtVar('Dezimalzeichen'); if(fSetzMPWert($v,'Dezimalzeichen','"')) $bNeu=true;
 $v=txtVar('Tausendzeichen'); if(fSetzMPWert($v,'Tausendzeichen','"')) $bNeu=true;
 $v=txtVar('ZahlLeer'); if(fSetzMPWert(($v?true:false),'ZahlLeer','')) $bNeu=true;
 $v=txtVar('Waehrung'); if($v=='€') $v='&#8364;'; if(fSetzMPWert($v,'Waehrung','"')) $bNeu=true;
 $v=txtVar('PreisLeer'); if(fSetzMPWert(($v?true:false),'PreisLeer','')) $bNeu=true;
 $v=min(max((int)txtVar('PLZLaenge'),0),8); if(fSetzMPWert(sprintf('%0d',$v),'PLZLaenge','')) $bNeu=true;
 $v=txtVar('NummerStellen'); if(fSetzMPWert($v,'NummerStellen',"'")) $bNeu=true;
 $v=(int)txtVar('NummerMitSeg'); if(fSetzMPWert(($v?true:false),'NummerMitSeg','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzMPWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzMPWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('PopupX'); if(strlen($v)<=0) $v=5; if(fSetzMPWert($v,'PopupX','')) $bNeu=true;
 $v=txtVar('PopupY'); if(strlen($v)<=0) $v=5; if(fSetzMPWert($v,'PopupY','')) $bNeu=true;
 $v=(int)txtVar('DruckLFarbig'); if(fSetzMPWert(($v?true:false),'DruckLFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckDFarbig'); if(fSetzMPWert(($v?true:false),'DruckDFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckPopup'); if(fSetzMPWert(($v?true:false),'DruckPopup','')) $bNeu=true;
 $v=txtVar('Captcha'); if(fSetzMPWert(($v?true:false),'Captcha','')) $bNeu=true;
 $v=txtVar('CaptchaTxFarb'); if(fSetzMPWert($v,'CaptchaTxFarb',"'")) $bNeu=true;
 $v=txtVar('CaptchaHgFarb'); if(fSetzMPWert($v,'CaptchaHgFarb',"'")) $bNeu=true;
 $v=sprintf('%06d',txtVar('Schluessel')); if(fSetzMPWert($v,'Schluessel',"'")) $bNeu=true;
 $v=txtVar('TxDSE1'); if(fSetzMPWert($v,'TxDSE1','"')) $bNeu=true;
 $v=txtVar('TxDSE2'); if(fSetzMPWert($v,'TxDSE2','"')) $bNeu=true;
 $v=txtVar('DSELink'); if(fSetzMPWert($v,'DSELink',"'")) $bNeu=true;
 if($v=txtVar('DSETarget')) $mpDSExTarget=''; else{$v=txtVar('DSExTarget'); $mpAktuellexTarget=$v;} if(fSetzMPWert($v,'DSETarget',"'")) $bNeu=true;
 $v=(int)txtVar('DSEPopUp'); if(fSetzMPWert(($v?true:false),'DSEPopUp','')) $bNeu=true;
 $v=max((int)txtVar('DSEPopupX'),0); if(fSetzMPWert($v,'DSEPopupX','')) $bNeu=true; $v=max((int)txtVar('DSEPopupH'),100); if(fSetzMPWert($v,'DSEPopupH','')) $bNeu=true;
 $v=max((int)txtVar('DSEPopupY'),0); if(fSetzMPWert($v,'DSEPopupY','')) $bNeu=true; $v=max((int)txtVar('DSEPopupW'),100); if(fSetzMPWert($v,'DSEPopupW','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Grundeinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Grundeinstellungen bleiben unverändert.'; $MTyp='Meld';}
}
//Seitenausgabe
if(!is_dir(MP_Pfad.MP_Daten)) echo '<p class="admFehl">Bitte zuerst die Pfade im Setup einstellen!</p>'.NL;
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form name="farbform" action="konfAllgemein.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die HTML-Ausgabe des Marktes kann für <i>seltene</i> Anwendungsfälle unterdrückt werden und statt dessen alle Ausgaben nur in der globalen Variablen <i>$sMpOut</i> gesammelt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ausgabeverhalten</td>
 <td><div style="margin-bottom:3px"><input class="admRadio" type="radio" name="Echo" value="1"<?php if($mpEcho) echo' checked="checked"'?> /> HTML-Code wie üblich ausgeben (Standardeinstellung)</div><div style="margin-bottom:5px"><input class="admRadio" type="radio" name="Echo" value="0"<?php if(!$mpEcho) echo' checked="checked"'?> /> HTML-Ausgabe nur in der globalen Variablen $sMpOut sammeln (sehr selten)</div>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgaben des Marktplatz-Scripts im Besucherbereich erfolgen normalerweise in der Kodierung des Standardzeichensatzes. Das ist üblicherweise <i>ISO-8859-1</i> (<i>Western</i>).
Falls Ihre Umgebung des Marktplatz-Scripts einen anderen Zeichensatz erfordert (z.B. bei Einbindung in ein CMS) können Sie für die Ausgaben des Marktplatz-Scripts im Besucherbereich eine alternative Zeichenkodierung einstellen.
(Im Administratorbereich ist der Zeichensatz hingegen fest eingestellt. <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.4_Admin" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>)</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:39em">Zeichensatz</td>
 <td><select name="Zeichensatz" size="1"><option value="0">Standard</option><option value="1"<?php if($mpZeichensatz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($mpZeichensatz==2) echo' selected="selected"'?>>UTF-8</option></select> <span class="admMini">(Empfehlung: UTF-8)</span> <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.4" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">In <i>zunehmenden</i> Fällen scheint es nötig sein, die MySQL-Datenbankverbindung des Besucherbereiches zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.
Tragen Sie dann hier diesen Zeichensatz ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz<br />im Besucherbereich</td>
 <td><input type="text" name="SqlCharSet" value="<?php echo $mpSqlCharSet?>" style="width:11em;" /> <span class="admMini">(Empfehlung: leer lassen oder z.B. <i>latin1</i>, selten auch <i>utf8</i> bzw. <i>utf8mb4</i>)</span>
 <div class="admMini" style="margin-bottom:3px"><u>Hinweis</u>: Für die <a href="konfAdmin.php">Administration</a>sseiten gibt es eine eigene Einstellung zum MySQL-Zeichensatz.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sollten obige Einstellungen nicht für korrekte Zeichendarstellung im Zusammenhang mit der Datenbank reichen, besteht <i>in seltenen Fällen</i> auf <i>extrem eigenwillig konfigurierten Servern</i> eine weitere Möglichkeit. Deren missbräuchlicher Gebrauch kann aber auch schädlich sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">DB-Zeichensatz<br>im Normal-Fenster</td>
 <td><select name="ZeichnsNorm" size="1"><option value="0">Standard</option><option value="1"<?php if($mpZeichnsNorm==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($mpZeichnsNorm==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz der Datenbankverbindung im Normal-Fenster <span class="admMini">(Empfehlung: Standard)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">DB-Zeichensatz<br>im Popup-Fenster</td>
 <td><select name="ZeichnsPopf" size="1"><option value="0">Standard</option><option value="1"<?php if($mpZeichnsPopf==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($mpZeichnsPopf==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz der Datenbankverbindung im Popup-Fenster <span class="admMini">(Empfehlung: Standard)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Verlinkungen, die aus Inseraten des Marktplatz-Scripts herausführen, enthalten manchmal deutsche Umlaute (obwohl das eigentlich vermieden werden sollte).
Wie sollen die Umlaute in solchen externen Links codiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Umlaut-Codierung<br>externer Links</td>
 <td><select name="ZeichnsExtLink" size="1"><option value="0">URL-codiert ISO-8859-1</option><option value="1<?php if($mpZeichnsExtLink==1) echo'" selected="selected'?>">URL-codiert UTF-8</option><option value="2<?php if($mpZeichnsExtLink==2) echo'" selected="selected'?>">uncodiert UTF-8</option><option value="3<?php if($mpZeichnsExtLink==3) echo'" selected="selected'?>">uncodiert ASCII</option></select> <span class="admMini">(Empfehlung: ISO-8859-1)</span> (soweit Umlaute in externen Links vorhanden)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das dem Marktplatz zugrundeliegende PHP-Sprachsystem gibt bei Systemfehlern Fehlermeldungen bzw. bei Sprachverletzungen Warnmeldungen aus.
Die Warnmeldungen können ein- oder ausgeschaltet sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Warnmeldungen</td>
 <td><input class="admRadio" type="radio" name="WarnMeldungen" value="0"<?php if(!$mpWarnMeldungen) echo' checked="checked"'?> /> Warnungen aus &nbsp; &nbsp; <input class="admRadio" type="radio" name="WarnMeldungen" value="1"<?php if($mpWarnMeldungen) echo' checked="checked"'?> /> Warnungen ein &nbsp; &nbsp;
 <span class="admMini">(Empfehlung: ausgeschaltet)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern ein Besucher eine Aktion veranlasst
und kein Marktsegment ausgewählt ist erscheint folgende Fehlermeldung.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Segmentfehler</td>
 <td><input type="text" name="TxKeinSegment" value="<?php echo $mpTxKeinSegment?>" style="width:99%" />
 <div class="admMini">Beispiel: Es ist momentan leider kein Marktsegment ausgewählt!</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern eine Suchmaschine oder ein Besucher
eine Adresse (URL) aufruft zu einem Inserat, das abgelaufen oder inzwischen gelöscht ist soll Folgendes passieren:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Inserat <br />nicht gefunden</td>
 <td><div style="margin-bottom:3px"><input class="admRadio" type="radio" name="ErrorPage" value="1"<?php if($mpErrorPage) echo' checked="checked"'?> /> Fehlerseite <i>error404.html</i> bzw. <i>error410.html</i> (NotFound/Gone) ausgeben</div><div style="margin-bottom:5px"><input class="admRadio" type="radio" name="ErrorPage" value="0"<?php if(!$mpErrorPage) echo' checked="checked"'?> /> die nachfolgende Fehlermeldung in der Marktseite anzeigen:</div>
 <input type="text" name="TxNummerUngueltig" value="<?php echo $mpTxNummerUngueltig?>" style="width:99%" />
 <div class="admMini">Beispiel: Datensatznummer ungültig!</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern der Marktplatz eigenständig
mit der umhüllenden HTML-Schablone <i>mpSeite.htm</i> läuft (nicht per PHP-inclue eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> in der Seite
über die Platzhalter <i>{META-KEY}</i> und <i>{META-DES}</i> der HTML-Schablone <i>mpSeite.htm</i> mit folgenden Texten ergänzend füllen.
<div class="admMini"><u>Hinweis</u>: Die hier hinterlegten allgemeinen Angaben
werden nur in den allgemeinen Seiten des Marktplatzes wie Übersicht, Suchformular, Loginseite usw. verwendet.
In den Inseratelisten und Detailseiten können speziellere Meta-Tags definiert werden.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td><input type="text" name="TxAMetaKey" value="<?php echo $mpTxAMetaKey?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>Kleinanzeigen, Inserate</i> &nbsp; (#S steht als Platzhalter für den <i>Segmentnamen</i>)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</td>
 <td><input type="text" name="TxAMetaDes" value="<?php echo $mpTxAMetaDes?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>Kleinanzeigenmarktplatz</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Marktplatz-Script kann auf sogenannte <i>suchmaschinenfreundliche Adressen</i> (search-engine-friendly-URL: SEF) umgeschaltet werden.
Voraussetzung dafür ist jedoch, dass das Server-Zusatzmodul <i>mod_rewrite</i> auf Ihrem Webserver installiert ist und von Ihnen genutzt werden kann.
Ausserdem muss die Datei <i>.htaccess</i> im Aufrufverzeichnis der Marktplatzes mit den Umschreibungs-Regeln (RewriteRule) korrekt eingerichtet sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">SEF-URL-Format</td>
 <td><input class="admRadio" type="radio" name="Sef" value="0"<?php if(!$mpSef) echo' checked="checked"'?> /> ausgeschaltet oder <input class="admRadio" type="radio" name="Sef" value="1"<?php if($mpSef) echo' checked="checked"'?> /> eingeschaltet &nbsp; (<span class="admMini">Standard: ausgeschaltet</span>) <a href="<?php echo AM_Hilfe?>LiesMich.htm#5.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Marktplatz-Script kann zur Suchmaschinenoptimierung (SEO) zusätzlich sogenannte <i>kanonische Links</i> (<i>canonical links</i>) erzeugen und unsichtbar in die Seiten einbetten. Dadurch soll die Auffindbarkeit der Seiten unter eindeutigen Adressen in den Suchmaschinen verbessert werden.
<div class="admMini">(<u>Hinweis</u>: Die Regeln für die Auswertung von <i>kanonischen Links</i> durch die Suchmaschinen sind nicht endgültig. Im seltenen Einzelfall könnten die <i>kanonischen Links</i> auch die Auffindbarkeit der Seiten in den Suchmaschinen verschlechtern.)</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">kanonische Links</td>
 <td><input class="admCck" type="checkbox" name="CanoLink" value="1"<?php if($mpCanoLink) echo' checked="checked"'?> /> kanonische Links ezeugen <span style="padding-left:80px"><input class="admCck" type="checkbox" name="CanoPopup" value="1"<?php if($mpCanoPopup) echo' checked="checked"'?> /> auch in Popup-Fenstern</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Inseratedatum kann in unterschiedlichen Formaten
mit 2-stelligen oder 4-stelligen Jahresangabenn dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td><select name="Datumsformat" style="width:90;" size="1"><?php for($i=0;$i<5;$i++) echo'<option value="'.$i.'"'.($mpDatumsformat!=$i?'':' selected="selected"').'>'.fMpDatumsFormat($i,$mpJahrhundert).'</option>'?></select> &nbsp; &nbsp;
 Jahreszahl <input class="admRadio" type="radio" name="Jahrhundert" value="1"<?php if($mpJahrhundert) echo' checked="checked"'?> /> 4-stellig oder <input class="admRadio" type="radio" name="Jahrhundert" value="0"<?php if(!$mpJahrhundert) echo' checked="checked"'?> /> 2-stellig</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitzone für PHP</td>
 <td><input type="text" name="TimeZoneSet" value="<?php echo $mpTimeZoneSet?>" style="width:10em;" /> Muster: <i>Europe/Berlin</i>, <i>Europe/Vienna</i> oder <i>Europe/Zurich</i> o.ä.
 <div class="admMini">gültige PHP-Zeitzone gemäß <a style="color:#004" href="http://www.php.net/manual/de/timezones.php" target="hilfe" onclick="hlpWin(this.href);return false;">http://www.php.net/manual/de/timezones.php</a></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Welche Formatierungen sollen für Felder vom Typ <i>Währung</i> oder <i>Zahl</i> verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Währungsanzeige</td>
 <td><select name="Waehrung" size="1"><option value=""></option><option value="EUR"<?php if($mpWaehrung=='EUR') echo' selected="selected"'?>>EUR</option><option value="CHF"<?php if($mpWaehrung=='CHF') echo' selected="selected"'?>>CHF</option><option value="SFr"<?php if($mpWaehrung=='SFr') echo' selected="selected"'?>>SFr</option><option value="&#8364;"<?php if($mpWaehrung=='&#8364;'||$mpWaehrung=='€') echo' selected="selected"'?>>&#8364;</option><option value="$"<?php if($mpWaehrung=='$') echo' selected="selected"'?>>$</option></select> &nbsp;
 leere Preise <input class="admRadio" type="radio" name="PreisLeer" value="1"<?php if($mpPreisLeer) echo' checked="checked"'?> /> nicht anzeigen oder <input class="admRadio" type="radio" name="PreisLeer" value="0"<?php if(!$mpPreisLeer) echo' checked="checked"'?> /> als 0 anzeigen &nbsp; &nbsp;
 <input type="text" name="Dezimalstellen" value="<?php echo $mpDezimalstellen?>" style="width:18px;" /> Dezimalstellen <span class="admMini">(Empfehlung: <i>2</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zahlenformat</td>
 <td>Dezimaltrennzeichen <select name="Dezimalzeichen" size="1"><option value=","<?php if($mpDezimalzeichen==',') echo' selected="selected"'?>>,</option><option value="."<?php if($mpDezimalzeichen=='.') echo' selected="selected"'?>>.</option></select> &nbsp;
 Zifferngruppierung/Tausendertrennzeichen <select name="Tausendzeichen" size="1"><option value=""></option><option value="."<?php if($mpTausendzeichen=='.') echo' selected="selected"'?>>.</option><option value=","<?php if($mpTausendzeichen==',') echo' selected="selected"'?>>,</option><option value="'"<?php if($mpTausendzeichen=="'") echo' selected="selected"'?>>'</option></select><br />
 Nullwerte <input class="admRadio" type="radio" name="ZahlLeer" value="1"<?php if($mpZahlLeer) echo' checked="checked"'?> /> nicht anzeigen oder <input class="admRadio" type="radio" name="ZahlLeer" value="0"<?php if(!$mpZahlLeer) echo' checked="checked"'?> /> als 0 anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Postleitzahlen</td>
 <td><select name="PLZLaenge" size="1">
   <option value="0">beliebig</option>
   <option value="3<?php if($mpPLZLaenge=='3') echo '" selected="selected'?>">3</option>
   <option value="4<?php if($mpPLZLaenge=='4') echo '" selected="selected'?>">4</option>
   <option value="5<?php if($mpPLZLaenge=='5') echo '" selected="selected'?>">5</option>
   <option value="6<?php if($mpPLZLaenge=='6') echo '" selected="selected'?>">6</option>
   <option value="7<?php if($mpPLZLaenge=='7') echo '" selected="selected'?>">7</option>
   <option value="8<?php if($mpPLZLaenge=='8') echo '" selected="selected'?>">8</option>
  </select> Stellen lang</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Vor den Inseraten kann die laufende Inseratenummer eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Stellenanzahl</td>
 <td><div style="margin-botton:4px">Inseratenummer dann <select name="NummerStellen" size="1">
   <option value="0"></option>
   <option value="01<?php if($mpNummerStellen=='01') echo '" selected="selected'?>">1</option>
   <option value="02<?php if($mpNummerStellen=='02') echo '" selected="selected'?>">2</option>
   <option value="03<?php if($mpNummerStellen=='03') echo '" selected="selected'?>">3</option>
   <option value="04<?php if($mpNummerStellen=='04') echo '" selected="selected'?>">4</option>
   <option value="05<?php if($mpNummerStellen=='05') echo '" selected="selected'?>">5</option>
  </select>-stellig anzeigen</div>
  <input type="checkbox" name="NummerMitSeg" value="1"<?php if($mpNummerMitSeg) echo ' checked="checked"'?> /> zweistellige Segmentnummer davorsetzen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Inseratedetails und/oder Info-Formular sowie Druckausgaben können wahlweise in einem Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Popup-Fenster</td>
 <td valign="top"><div><input type="text" name="PopupBreit" value="<?php echo $mpPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; <input type="text" name="PopupHoch" value="<?php echo $mpPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.4" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 <div><input type="text" name="PopupX" value="<?php echo $mpPopupX?>" size="4" style="width:36px;" /> Pixel Popup vom linken Rand &nbsp; <input type="text" name="PopupY" value="<?php echo $mpPopupY?>" size="4" style="width:36px;" /> Pixel Popup-Beginn von oben</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sowohl die Inserateliste als auch die Detaildarstellung können gedruckt werden. Standardmäßig erfolgt das Drucken über ein Popup-Fenster mit druckoptimierten Layout. Sie können aber auch statt im Popup-Fenster im normalen Fenster jedoch dann ohne die optimierte Darstellung drucken.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druckfunktion</td>
 <td><div style="margin-top:2px;"><input class="admCheck" type="checkbox" name="DruckLFarbig" value="1"<?php if($mpDruckLFarbig) echo' checked="checked"'?> />Inserateliste <i>farbig</i> drucken &nbsp; &nbsp; <input class="admCheck" type="checkbox" name="DruckDFarbig" value="1"<?php if($mpDruckDFarbig) echo' checked="checked"'?> /> Inseratedetails <i>farbig</i> drucken &nbsp; <span class="admMini">(Empfehlung: <i>nicht</i> farbig drucken)</span></div>
 <div style="margin-top:3px;"><input class="admCheck" type="checkbox" name="DruckPopup" value="1"<?php if($mpDruckPopup) echo' checked="checked"'?> /> im Popup-Fenster drucken &nbsp; &nbsp; <span class="admMini">(Empfehlung: <i>Popup-Fenster zum Drucken aktivieren</i>)</span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter den Formuaren dieses Programmes gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />erklärung<a name="DSE"></a></td>
 <td>
  <input type="text" name="TxDSE1" value="<?php echo $mpTxDSE1?>" style="width:99%" />
  <div class="admMini">Muster: <i>Ich habe die <span style="white-space:nowrap">[L]Datenschutzerklärung[/L]</span> gelesen und stimme ihr zu.</i></div>
  <div class="admMini">Hinweis: <i>[L]</i> und <i>[/L]</i> stehen für  Linkanfang und Linkende und sind hier zwingend notwendig.</div>
  <div style="margin-top:6px;margin-bottom:2px">Linkadresse zur Datenschutzerklärung auf Ihrer Webseite: &nbsp; <span class="admMini">notfalls einschließlich https://</span></div>
  <input type="text" name="DSELink" value="<?php echo $mpDSELink?>" style="width:99%" />
  <div style="margin-top:6px;margin-bottom:2px">Zielfenster für den Link zur Datenschutzerklärung:</div>
  <select name="DSETarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($mpDSETarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($mpDSETarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($mpDSETarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($mpDSETarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="marktplatz"<?php if($mpDSETarget=='marktplatz') echo' selected="selected"'?>>markt: Marktfenster</option></select>&nbsp;
  oder anderes Zielfenster  <input type="text" name="DSExTarget" value="<?php echo $mpDSExTarget?>" style="width:100px;" /> (Target)
  <div style="margin-top:4px"><input class="admRadio" type="checkbox" name="DSEPopUp" value="1"<?php if($mpDSEPopUp) echo' checked="checked"'?>> als Popupfenster &nbsp;
  <input type="text" name="DSEPopupW" value="<?php echo $mpDSEPopupW?>" size="4" style="width:32px" /> px breit &nbsp; <input type="text" name="DSEPopupH" value="<?php echo $mpDSEPopupH?>" size="4" style="width:32px" /> px hoch &nbsp; &nbsp;
  <input type="text" name="DSEPopupY" value="<?php echo $mpDSEPopupY?>" size="4" style="width:24px" /> px von oben &nbsp; <input type="text" name="DSEPopupX" value="<?php echo $mpDSEPopupX?>" size="4" style="width:24px" /> px von links</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenverarbeitung<br/>und -speicherung</td>
 <td>
  <input type="text" name="TxDSE2" value="<?php echo $mpTxDSE2?>" style="width:99%" />
  <div class="admMini">Muster: <i>Ich bin mit der Verarbeitung und Speicherung meiner persönlichen Daten im Rahmen der Datenschutzerklärung einverstanden.</i></div>
  <div class="admMini">Hinweis: Platzhalter <i>[L]</i> und <i>[/L]</i> für die Verlinkung sind wie oben möglich aber hier <i>nicht</i> zwingend.</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen zur Benutzeranmeldung bzw. Teilnehmerregistrierung oder zum Inserateeintrag ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($mpCaptcha) echo' checked="checked"'?> /> verwenden&nbsp;
 Muster <span style="color:<?php echo $mpCaptchaTxFarb?>;background-color:<?php echo $mpCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span>&nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $mpCaptchaTxFarb?>" style="width:65px" />
 <a href="colors.php?col=<?php echo substr($mpCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="colWin(this.href);return false"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>&nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $mpCaptchaHgFarb?>" style="width:65px" />
 <a href="colors.php?col=<?php echo substr($mpCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="colWin(this.href);return false"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Geheimschlüssel</td>
 <td><div style="float:left"><input type="text" name="Schluessel" value="<?php echo $mpSchluessel?>" style="width:5em;color:#888888" /></div>
 <div class="admMini">Niemals manuell verändern, nur notieren!! Nur nach einer kompletten Rekonstruktion des Marktplatzes bei noch vorhandenen Daten manuell in die Datei <i>mpWerte.php</i> an der Stelle <i>MP_Schluessel</i> einpflegen.</div></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>