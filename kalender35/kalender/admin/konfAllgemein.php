<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('allgemeine Einstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KAg');

$nFelder=count($kal_FeldName); $ksDSExTarget='';
if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $Msg='<p class="admMeld">Stellen Sie die Funktion des Kalender-Scripts passend ein.</p>';
 $ksHalteAltesNochTage=KAL_HalteAltesNochTage; $ksZeigeAltesNochTage=KAL_ZeigeAltesNochTage; $ksBearbAltesNochTage=KAL_BearbAltesNochTage;
 $ksDatumsformat=KAL_Datumsformat; $ksJahrhundert=KAL_Jahrhundert; $ksMitWochentag=KAL_MitWochentag; $ksEndeDatum=KAL_EndeDatum;
 $ksTxAMetaKey=KAL_TxAMetaKey; $ksTxAMetaDes=KAL_TxAMetaDes; $ksTxAMetaTit=KAL_TxAMetaTit;
 $ksTimeZoneSet=KAL_TimeZoneSet; $ksWaehrung=KAL_Waehrung; $ksPreisLeer=KAL_PreisLeer; $ksPLZLaenge=KAL_PLZLaenge;
 $ksDezimalstellen=KAL_Dezimalstellen; $ksDezimalzeichen=KAL_Dezimalzeichen; $ksTausendzeichen=KAL_Tausendzeichen; $ksZahlLeer=KAL_ZahlLeer;
 $ksZeichensatz=KAL_Zeichensatz; $ksLZeichenstz=KAL_LZeichenstz; $ksSZeichenstz=KAL_SZeichenstz; $ksZSatzExtLink=KAL_ZSatzExtLink;
 $ksLZsatzPopup=KAL_LZsatzPopup; $ksSZsatzPopup=KAL_SZsatzPopup; $ksSqlCharSet=KAL_SqlCharSet;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
 $ksDruckLFarbig=KAL_DruckLFarbig; $ksDruckDFarbig=KAL_DruckDFarbig; $ksSchluessel=KAL_Schluessel; $ksWarnMeldungen=KAL_WarnMeldungen;
 $ksDruckPopup=KAL_DruckPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch; $ksPopupX=KAL_PopupX; $ksPopupY=KAL_PopupY; $ksSchablone=KAL_Schablone;
 $ksTxDSE1=KAL_TxDSE1; $ksTxDSE2=KAL_TxDSE2; $ksDSELink=KAL_DSELink; $ksDSETarget=KAL_DSETarget; $ksDSEPopUp=KAL_DSEPopUp; $ksDSEPopupX=KAL_DSEPopupX; $ksDSEPopupY=KAL_DSEPopupY; $ksDSEPopupW=KAL_DSEPopupW; $ksDSEPopupH=KAL_DSEPopupH;
 if($ksDSETarget!='kalender'&&$ksDSETarget!='_self'&&$ksDSETarget!='_parent'&&$ksDSETarget!='_top'&&$ksDSETarget!='_blank') $ksDSExTarget=$ksDSETarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=max((int)txtVar('ZeigeAltesNochTage'),0); if(fSetzKalWert($v,'ZeigeAltesNochTage','')) $bNeu=true;
 $v=max((int)txtVar('HalteAltesNochTage'),1,$v); if(fSetzKalWert($v,'HalteAltesNochTage','')) $bNeu=true;
 $v=min(max((int)txtVar('BearbAltesNochTage'),0),$v); if(fSetzKalWert($v,'BearbAltesNochTage','')) $bNeu=true;
 $v=txtVar('TxAMetaKey'); if(fSetzKalWert($v,'TxAMetaKey','"')) $bNeu=true;
 $v=txtVar('TxAMetaDes'); if(fSetzKalWert($v,'TxAMetaDes','"')) $bNeu=true;
 $v=txtVar('TxAMetaTit'); if(fSetzKalWert($v,'TxAMetaTit','"')) $bNeu=true;
 $v=txtVar('TimeZoneSet'); if(fSetzKalWert($v,'TimeZoneSet',"'")) $bNeu=true;
 $v=txtVar('Datumsformat'); if(fSetzKalWert($v,'Datumsformat','')) $bNeu=true;
 $v=txtVar('Jahrhundert'); if(fSetzKalWert(($v?true:false),'Jahrhundert','')) $bNeu=true;
 $v=txtVar('MitWochentag'); if(fSetzKalWert($v,'MitWochentag','')) $bNeu=true;
 $v=explode(',',str_replace(';',',',txtVar('WochenTag'))); for($i=0;$i<7;$i++) $v[$i]=trim($v[$i]); if(fSetzArray($v,'WochenTag','"')){$bNeu=true; $kal_WochenTag=$v;}
 $v=txtVar('EndeDatum'); if(fSetzKalWert(($v?true:false),'EndeDatum','')) $bNeu=true;
 $v=txtVar('Waehrung'); if($v=='€') $v='&#8364;'; if(fSetzKalWert($v,'Waehrung','"')) $bNeu=true;
 $v=txtVar('PreisLeer'); if(fSetzKalWert(($v?true:false),'PreisLeer','')) $bNeu=true;
 $v=txtVar('ZahlLeer'); if(fSetzKalWert(($v?true:false),'ZahlLeer','')) $bNeu=true;
 $v=min(max((int)txtVar('PLZLaenge'),4),8); if(fSetzKalWert($v,'PLZLaenge','')) $bNeu=true;
 $v=min(max((int)txtVar('Dezimalstellen'),0),4); if(fSetzKalWert($v,'Dezimalstellen','')) $bNeu=true;
 $v=txtVar('Dezimalzeichen'); if(fSetzKalWert($v,'Dezimalzeichen','"')) $bNeu=true;
 $v=txtVar('Tausendzeichen'); if(fSetzKalWert($v,'Tausendzeichen','"')) $bNeu=true;
 $v=txtVar('SqlCharSet'); if(fSetzKalWert($v,'SqlCharSet',"'")) $bNeu=true;
 $v=(int)txtVar('Zeichensatz'); if(fSetzKalWert($v,'Zeichensatz','')) $bNeu=true;
 $v=(int)txtVar('LZeichenstz'); if(fSetzKalWert($v,'LZeichenstz','')) $bNeu=true;
 $v=(int)txtVar('SZeichenstz'); if(fSetzKalWert($v,'SZeichenstz','')) $bNeu=true;
 $v=(int)txtVar('LZsatzPopup'); if(fSetzKalWert($v,'LZsatzPopup','')) $bNeu=true;
 $v=(int)txtVar('SZsatzPopup'); if(fSetzKalWert($v,'SZsatzPopup','')) $bNeu=true;
 $v=(int)txtVar('ZSatzExtLink'); if(fSetzKalWert($v,'ZSatzExtLink','')) $bNeu=true;
 $v=(int)txtVar('DruckLFarbig'); if(fSetzKalWert(($v?true:false),'DruckLFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckDFarbig'); if(fSetzKalWert(($v?true:false),'DruckDFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckPopup'); if(fSetzKalWert(($v?true:false),'DruckPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('PopupX'); if(strlen($v)<=0) $v=5; if(fSetzKalWert($v,'PopupX','')) $bNeu=true;
 $v=txtVar('PopupY'); if(strlen($v)<=0) $v=5; if(fSetzKalWert($v,'PopupY','')) $bNeu=true;
 $v=txtVar('Captcha'); if(fSetzKalWert(($v?true:false),'Captcha','')) $bNeu=true;
 $v=txtVar('CaptchaTxFarb'); if(fSetzKalWert($v,'CaptchaTxFarb',"'")) $bNeu=true;
 $v=txtVar('CaptchaHgFarb'); if(fSetzKalWert($v,'CaptchaHgFarb',"'")) $bNeu=true;
 $v=txtVar('CaptchaTyp'); if(fSetzKalWert($v,'CaptchaTyp',"'")) $bNeu=true;
 $v=txtVar('CaptchaGrafisch'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
 $v=txtVar('CaptchaNumerisch'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
 $v=txtVar('CaptchaTextlich'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
 $v=(int)txtVar('WarnMeldungen'); if(fSetzKalWert(($v?true:false),'WarnMeldungen','')) $bNeu=true;
 $v=sprintf('%06d',txtVar('Schluessel')); if(fSetzKalWert($v,'Schluessel',"'")) $bNeu=true;
 $v=txtVar('Schablone'); if(fSetzKalWert(($v?true:false),'Schablone','')) $bNeu=true;
 $v=txtVar('TxDSE1'); if(fSetzKalWert($v,'TxDSE1','"')) $bNeu=true;
 $v=txtVar('TxDSE2'); if(fSetzKalWert($v,'TxDSE2','"')) $bNeu=true;
 $v=txtVar('DSELink'); if(fSetzKalWert($v,'DSELink',"'")) $bNeu=true;
 if($v=txtVar('DSETarget')) $ksDSExTarget=''; else{$v=txtVar('DSExTarget'); $ksAktuellexTarget=$v;} if(fSetzKalWert($v,'DSETarget',"'")) $bNeu=true;
 $v=(int)txtVar('DSEPopUp'); if(fSetzKalWert(($v?true:false),'DSEPopUp','')) $bNeu=true;
 $v=max((int)txtVar('DSEPopupX'),0); if(fSetzKalWert($v,'DSEPopupX','')) $bNeu=true; $v=max((int)txtVar('DSEPopupH'),100); if(fSetzKalWert($v,'DSEPopupH','')) $bNeu=true;
 $v=max((int)txtVar('DSEPopupY'),0); if(fSetzKalWert($v,'DSEPopupY','')) $bNeu=true; $v=max((int)txtVar('DSEPopupW'),100); if(fSetzKalWert($v,'DSEPopupW','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Konfigurationsinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST
$kal_FeldType[1]='x'; $kal_FeldType[(int)array_search('z',$kal_FeldType)]='x'; $bDatum2=in_array('d',$kal_FeldType)||in_array('z',$kal_FeldType);

//Scriptausgabe
echo $Msg.NL;
?>

<form name="farbform" action="konfAllgemein.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Abgelaufene Termine bleiben im Kalender noch einige Zeit gespeichert, werden aber für Besucher nicht mehr angezeigt.
Wie sollen abgelaufene Termine für Besucher behandelt werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Anzeigedauer</td>
 <td><input type="text" name="ZeigeAltesNochTage" value="<?php echo $ksZeigeAltesNochTage?>" style="width:35px;" /> Tage nach Ablauf werden Termine für Besucher ausgeblendet &nbsp; <span class="admMini">(Empfehlung: <i>0...7</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherdauer</td>
 <td><input type="text" name="HalteAltesNochTage" value="<?php echo $ksHalteAltesNochTage?>" style="width:35px;" /> Tage nach dem (Beginn-) Datum werden Termine aus dem Kalender gelöscht &nbsp; <span class="admMini">(Empfehlung: <i>1...400</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bearbeitungsdauer</td>
 <td><input type="text" name="BearbAltesNochTage" value="<?php echo $ksBearbAltesNochTage?>" style="width:35px;" /> Tage nach dem (Beginn-) Datum können Termine noch bearbeitet werden &nbsp; <span class="admMini">(Empfehlung: <i>0...400</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Inseratelistenseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i>
mit folgenden Texten zusätzlich füllen.
<div class="admMini"><u>Hinweis</u>: Die hier hinterlegten allgemeinen Angaben
werden nur in den allgemeinen Seiten des Kalenders wie Suchformular, Loginseite usw. verwendet.
In den Terminlisten und Detailseiten können speziellere Meta-Tags definiert werden.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td><input type="text" name="TxAMetaKey" value="<?php echo $ksTxAMetaKey?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine Veranstaltungen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td><input type="text" name="TxAMetaDes" value="<?php echo $ksTxAMetaDes?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Veranstaltungskalender</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td><input type="text" name="TxAMetaTit" value="<?php echo $ksTxAMetaTit?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Kalender</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Termindatum kann in unterschiedlichen Formaten
mit 2-stelligen oder 4-stelligen Jahresangaben sowie mit oder ohne Angabe des Wochentages dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td><select name="Datumsformat" style="width:88px;" size="1"><?php for($i=0;$i<5;$i++) echo'<option value="'.$i.'"'.($ksDatumsformat!=$i?'':' selected="selected"').'>'.fKalKDatumsFormat($i,$ksJahrhundert).'</option>'?></select> &nbsp; &nbsp;
 Jahreszahl <input class="admRadio" type="radio" name="Jahrhundert" value="1"<?php if($ksJahrhundert) echo' checked="checked"'?> /> 4-stellig oder <input class="admRadio" type="radio" name="Jahrhundert" value="0"<?php if(!$ksJahrhundert) echo' checked="checked"'?> /> 2-stellig</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Wochentage</td>
 <td><select name="MitWochentag" size="1"><option value="0">Datum ohne Wochentage</option><option value="1"<?php if($ksMitWochentag==1) echo' selected="selected"'?>>Wochentag vor dem Datum</option><option value="2"<?php if($ksMitWochentag==2) echo' selected="selected"'?>>Wochentag nach dem Datum</option><option value="3"<?php if($ksMitWochentag==3) echo' selected="selected"'?>>nur Wochentag ohne Datum</option></select>
 als Kürzel <input type="text" name="WochenTag" value="<?php echo $kal_WochenTag[0]; for($i=1;$i<7;$i++) echo ','.$kal_WochenTag[$i]?>" style="width:160px;" />
 <span class="admMini">(Empfehlung: So,Mo,Di,...,Sa)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitzone für PHP</td>
 <td><input type="text" name="TimeZoneSet" value="<?php echo $ksTimeZoneSet?>" style="width:10em;" /> Muster: <i>Europe/Berlin</i>, <i>Europe/Vienna</i> oder <i>Europe/Zurich</i> o.ä.
 <div class="admMini">gültige PHP-Zeitzone gemäß <a style="color:#004" href="https://www.php.net/manual/de/timezones.php" target="hilfe" onclick="hlpWin(this.href);return false;">https://www.php.net/manual/de/timezones.php</a></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn der Kalender ein zweites Feld vom Typ <i>Datum</i> und/oder ein zweites Feld vom Typ <i>Zeit</i> enthält kann dieses in der Deutung eines <i>Ende</i> der Veranstaltung behandelt werden. (Die gewählte Einstellung gilt für die Terminauswahl in der <i>Terminliste</i>, der <i>Detailanzeige</i>, dem <i>Minikalender</i> und den <i>aktuellen Terminen</i>.)</td></tr>
<tr class="admTabl">
 <td class="admSpa1">zweites Datums-<br />oder Zeitfeld</td>
 <td><input class="admRadio" type="radio" name="EndeDatum" value="1"<?php if($ksEndeDatum) echo' checked="checked"'?> /> als Terminende auffassen &nbsp; &nbsp; <input class="admRadio" type="radio" name="EndeDatum" value="0"<?php if(!$ksEndeDatum) echo' checked="checked"'?> /> als normales Feld behandeln
 <div class="admMini">(Zweites Datum/Zeit ist momentan in der Terminstruktur <?php if(!$bDatum2){?><span style="color:992200;"><i>nicht</i></span> <?php }?>vorhanden.)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Welche Formatierungen sollen für Felder vom Typ <i>Währung</i> oder <i>Zahl</i> verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Währungsanzeige</td>
 <td><select name="Waehrung" size="1"><option value=""></option><option value="EUR"<?php if($ksWaehrung=='EUR') echo' selected="selected"'?>>EUR</option><option value="CHF"<?php if($ksWaehrung=='CHF') echo' selected="selected"'?>>CHF</option><option value="SFr"<?php if($ksWaehrung=='SFr') echo' selected="selected"'?>>SFr</option><option value="&#8364;"<?php if($ksWaehrung=='&#8364;') echo' selected="selected"'?>>&#8364;</option><option value="$"<?php if($ksWaehrung=='$') echo' selected="selected"'?>>$</option></select> &nbsp;
 leere Preise <input class="admRadio" type="radio" name="PreisLeer" value="1"<?php if($ksPreisLeer) echo' checked="checked"'?> /> nicht anzeigen oder <input class="admRadio" type="radio" name="PreisLeer" value="0"<?php if(!$ksPreisLeer) echo' checked="checked"'?> /> als 0 anzeigen &nbsp; &nbsp;
 <input type="text" name="Dezimalstellen" value="<?php echo $ksDezimalstellen?>" style="width:18px;" /> Dezimalstellen <span class="admMini">(Empfehlung: <i>2</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zahlenformat</td>
 <td>Dezimaltrennzeichen <select name="Dezimalzeichen" size="1"><option value=","<?php if($ksDezimalzeichen==',') echo' selected="selected"'?>>,</option><option value="."<?php if($ksDezimalzeichen=='.') echo' selected="selected"'?>>.</option></select> &nbsp;
 Zifferngruppierung/Tausendertrennzeichen <select name="Tausendzeichen" size="1"><option value=""></option><option value="."<?php if($ksTausendzeichen=='.') echo' selected="selected"'?>>.</option><option value=","<?php if($ksTausendzeichen==',') echo' selected="selected"'?>>,</option><option value="'"<?php if($ksTausendzeichen=="'") echo' selected="selected"'?>>'</option></select><br />
 Nullwerte <input class="admRadio" type="radio" name="ZahlLeer" value="1"<?php if($ksZahlLeer) echo' checked="checked"'?> /> nicht anzeigen oder <input class="admRadio" type="radio" name="ZahlLeer" value="0"<?php if(!$ksZahlLeer) echo' checked="checked"'?> /> als 0 anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Postleitzahlen</td>
 <td><input type="text" name="PLZLaenge" value="<?php echo $ksPLZLaenge?>" style="width:18px;" /> Stellen lang</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das dem Kalender zugrundeliegende PHP-Sprachsystem gibt bei Systemfehlern Fehlermeldungen bzw. bei Sprachverletzungen Warnmeldungen aus.
Die Warnmeldungen können ein- oder ausgeschaltet sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Warnmeldungen</td>
 <td><input class="admRadio" type="radio" name="WarnMeldungen" value="0"<?php if(!$ksWarnMeldungen) echo' checked="checked"'?> /> Warnungen aus &nbsp; &nbsp; <input class="admRadio" type="radio" name="WarnMeldungen" value="1"<?php if($ksWarnMeldungen) echo' checked="checked"'?> /> Warnungen ein &nbsp; &nbsp;
 <span class="admMini">(Empfehlung: ausgeschaltet)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgabe und Datenspeicherung des Kalender-Scripts erfolgen normalerweise in der Kodierung des Standardzeichensatzes. Das ist üblicherweise <i>ISO-8859-1</i> (<i>Western</i>) für die HTML-Ausgabe und <i>Latin</i> für die Datenbank.
Falls Ihre Umgebung des Kalenders einen anderen Zeichensatz verwendet (z.B. bei Einbindung in ein CMS) können Sie für die Ausgabe bzw. Datenspeicherung des Kalenders im Besucherbereich passende Zeichenkodierung einstellen.
Diese Einstellungen gelten jedoch <i>nur</i> für die Besucherseiten, <i>nicht</i> für die Administrationsseiten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz<br />für Ausgaben</td>
 <td><select name="Zeichensatz" size="1"><option value="0">Standard ISO-8859-1</option><option value="2"<?php if($ksZeichensatz==2) echo' selected="selected"'?>>UTF-8</option><option value="1"<?php if($ksZeichensatz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option></select> Zeichensatz der Ausgabeseiten des Scriptes
 <div class="admMini">(Empfehlung: Standard&nbsp;ISO-8859-1)</div>
 <div class="admMini" style="margin-top:2px">Hinweis: Das Kalender-Script produziert die Ausgabeseiten mit diesem Zeichensatz.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Verlinkungen, die aus Terminen des Kalender-Scripts herausführen, enthalten manchmal deutsche Umlaute (obwohl das eigentlich vermieden werden sollte).
Wie sollen die Umlaute in solchen externen Links codiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Umlaut-Codierung<br>externer Links</td>
 <td><select name="ZSatzExtLink" size="1"><option value="0">URL-codiert ISO-8859-1</option><option value="1<?php if($ksZSatzExtLink==1) echo'" selected="selected'?>">URL-codiert UTF-8</option><option value="2<?php if($ksZSatzExtLink==2) echo'" selected="selected'?>">uncodiert UTF-8</option><option value="3<?php if($ksZSatzExtLink==3) echo'" selected="selected'?>">uncodiert ASCII</option></select> <span class="admMini">(Empfehlung: ISO-8859-1)</span> (soweit Umlaute in externen Links vorhanden)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">In <i>extrem seltenen</i> Fällen kann es nötig sein, die MySQL-Datenbankverbindung zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.
Tragen Sie dann hier diesen Zeichensatz ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz<br>im Besucherbereich</td>
 <td><input type="text" name="SqlCharSet" value="<?php echo $ksSqlCharSet?>" style="width:11em;" /> <span class="admMini">(Empfehlung: meist leer lassen, selten <i>latin1</i>, gelegentlich auch <i>utf8</i> bzw. <i>utf8mb4</i>)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die folgenden Zeichensatzeinstellungen zum Datenspeicher wirken ebenfalls <i>nur</i> bei MySQL-Datenbank und sind ebenfalls nur in<i> extrem seltenen</i> Ausnahmen anzuwenden. Belassen Sie die Einstellungen auf <i>Standard</i>, solange nichts anderes abgesprochen ist. Die Einstellung des Zeichensatzes oben bei <i>Zeichensatz für Ausgaben</i> reicht auf nahezu jedem Server.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz des<br />Datenspeichers<br />im Normalfenster</td>
 <td><select name="LZeichenstz" size="1"><option value="0">Standard</option><option value="1"<?php if($ksLZeichenstz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($ksLZeichenstz==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz, in dem der Datenspeicher die Daten beim Lesen ausliefert
 <div><select name="SZeichenstz" size="1"><option value="0">Standard</option><option value="1"<?php if($ksSZeichenstz==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($ksSZeichenstz==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz, in dem der Datenspeicher die Daten beim Schreiben braucht</div>
 <div class="admMini" style="margin-top:2px">Hinweis: Das Kalender-Script erzwingt nicht etwa diesen Zeichensatz gegenüber der Datenbank sondern verarbeitet lediglich die Daten gemäß des angegebenen Zeichensatzes. Einstellungen abweichend von <i>Standard</i> sind hier nur in extrem seltenen Fällen notwendig!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz des<br />Datenspeichers<br />im Popup-Fenster</td>
 <td><select name="LZsatzPopup" size="1"><option value="0">Standard</option><option value="1"<?php if($ksLZsatzPopup==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($ksLZsatzPopup==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz, in dem der Datenspeicher die Daten beim Lesen ausliefert
 <div><select name="SZsatzPopup" size="1"><option value="0">Standard</option><option value="1"<?php if($ksSZsatzPopup==1) echo' selected="selected"'?>>HTML-&amp;-maskiert</option><option value="2"<?php if($ksSZsatzPopup==2) echo' selected="selected"'?>>UTF-8</option></select> Zeichensatz, in dem der Datenspeicher die Daten beim Schreiben braucht</div>
 <div class="admMini" style="margin-top:2px">Hinweis: Das Kalender-Script erzwingt nicht etwa diesen Zeichensatz gegenüber der Datenbank sondern verarbeitet lediglich die Daten gemäß des angegebenen Zeichensatzes.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sowohl die Terminliste als auch die Detaildarstellung können gedruckt werden. Standardmäßig erfolgt das Drucken über ein Popup-Fenster mit druckoptimierten Layout. Sie können aber auch statt im Popup-Fenster im normalen Fenster jedoch dann ohne die optimierte Darstellung drucken.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druckfunktion<div style="margin-top:24px;">Größe des Popup</div></td>
 <td><div style="margin-top:2px;"><input class="admCheck" type="checkbox" name="DruckLFarbig" value="1"<?php if($ksDruckLFarbig) echo' checked="checked"'?> />Terminliste <i>farbig</i> drucken &nbsp; &nbsp; <input class="admCheck" type="checkbox" name="DruckDFarbig" value="1"<?php if($ksDruckDFarbig) echo' checked="checked"'?> /> Termindetails <i>farbig</i> drucken &nbsp; <span class="admMini">(Empfehlung: <i>nicht</i> farbig drucken)</span></div>
 <div style="margin-top:3px;"><input class="admCheck" type="checkbox" name="DruckPopup" value="1"<?php if($ksDruckPopup) echo' checked="checked"'?> /> im Popup-Fenster drucken &nbsp; &nbsp; <span class="admMini">(Empfehlung: <i>Popup-Fenster zum Drucken aktivieren</i>)</span></div>
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" align="bottom" border="0" title="Hilfe"></a></div>
 <div><input type="text" name="PopupX" value="<?php echo $ksPopupX?>" size="4" style="width:36px;" /> Pixel Popup vom linken Rand &nbsp; <input type="text" name="PopupY" value="<?php echo $ksPopupY?>" size="4" style="width:36px;" /> Pixel Popup-Beginn von oben</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Kalender-Script als eigenständige Seite läuft (nicht includiert wird)
wird der eigentliche Kalender in eine frei gestaltbare HTML-Schablonenseite <i>kalSeite.htm</i> eingepasst.
Diese Layoutschablone kann im Ausnahmefall deaktiviert werden.
Dann hat die Ausgabe des Kalender-Scripts jedoch auch keinen &lt;head&gt;- und &lt;body&gt;-Tag.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout-<br />schablone</td>
 <td>HTML-Seitenvorlage <i>kalSeite.htm</i>
  <input class="admRadio" type="radio" name="Schablone" value="1"<?php if($ksSchablone) echo' checked="checked"'?>> benutzen <span class="admMini">(empfohlen)</span> &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="Schablone" value="0"<?php if(!$ksSchablone) echo' checked="checked"'?>> nicht benutzen &nbsp; &nbsp;
  ( <a href="<?php echo KALPFAD?>kalSeite.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Vorschau</a> )</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2" style="padding-top:2px;font-size:10px;"><u>Hinweis</u>:
 Im Falle eines eingebetten Aufrufes per <i>include()</i>-Befehl wird die Schablone <i>kalSeite.htm</i> prinzipiell nicht benutzt.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter den Formuaren dieses Programmes gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />erklärung<a name="DSE"></a></td>
 <td>
  <input type="text" name="TxDSE1" value="<?php echo $ksTxDSE1?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich habe die <span style="white-space:nowrap">[L]Datenschutzerklärung[/L]</span> gelesen und stimme ihr zu.</i></div>
  <div class="admMini">Hinweis: <i>[L]</i> und <i>[/L]</i> stehen für  Linkanfang und Linkende und sind hier zwingend notwendig.</div>
  <div style="margin-top:6px;margin-bottom:2px">Linkadresse zur Datenschutzerklärung auf Ihrer Webseite: &nbsp; <span class="admMini">notfalls einschließlich https://</span></div>
  <input type="text" name="DSELink" value="<?php echo $ksDSELink?>" style="width:98%" />
  <div style="margin-top:6px;margin-bottom:2px">Zielfenster für den Link zur Datenschutzerklärung:</div>
  <select name="DSETarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($ksDSETarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($ksDSETarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($ksDSETarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($ksDSETarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="kalender"<?php if($ksDSETarget=='kalender') echo' selected="selected"'?>>kalender: Kalenderfenster</option></select>&nbsp;
  oder anderes Zielfenster  <input type="text" name="DSExTarget" value="<?php echo $ksDSExTarget?>" style="width:100px;" /> (Target)
  <div style="margin-top:4px"><input class="admRadio" type="checkbox" name="DSEPopUp" value="1"<?php if($ksDSEPopUp) echo' checked="checked"'?>> als Popupfenster &nbsp;
  <input type="text" name="DSEPopupW" value="<?php echo $ksDSEPopupW?>" size="4" style="width:32px" /> px breit &nbsp; <input type="text" name="DSEPopupH" value="<?php echo $ksDSEPopupH?>" size="4" style="width:32px" /> px hoch &nbsp; &nbsp;
  <input type="text" name="DSEPopupY" value="<?php echo $ksDSEPopupY?>" size="4" style="width:24px" /> px von oben &nbsp; <input type="text" name="DSEPopupX" value="<?php echo $ksDSEPopupX?>" size="4" style="width:24px" /> px von links</div>
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Datenverarbeitung<br/>und -speicherung</td>
 <td>
  <input type="text" name="TxDSE2" value="<?php echo $ksTxDSE2?>" style="width:98%" />
  <div class="admMini">Muster: <i>Ich bin mit der Verarbeitung und Speicherung meiner persönlichen Daten im Rahmen der Datenschutzerklärung einverstanden.</i></div>
  <div class="admMini">Hinweis: Platzhalter <i>[L]</i> und <i>[L]</i> für die Verlinkung sind wie oben möglich aber hier <i>nicht</i> zwingend.</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($ksCaptcha) echo' checked="checked"'?> /> verwenden,
 bevorzugter Captchatyp: <select name="CaptchaTyp" size="1"><option value="G<?php if($ksCaptchaTyp=='G') echo '" selected="selected';?>">grafisches Captcha</option><option value="N<?php if($ksCaptchaTyp=='N') echo '" selected="selected';?>">mathematisches Captcha</option><option value="T<?php if($ksCaptchaTyp=='T') echo '" selected="selected';?>">textliches Captcha</option></select></div>
 <div style="margin-top:5px;margin-bottom:5px;">Alternativen anbieten:
 <input class="admCheck" type="checkbox" name="CaptchaGrafisch" value="1"<?php if($ksCaptchaGrafisch) echo' checked="checked"'?> /> grafisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaNumerisch" value="1"<?php if($ksCaptchaNumerisch) echo' checked="checked"'?> /> mathemat. Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaTextlich" value="1"<?php if($ksCaptchaTextlich) echo' checked="checked"'?> /> textliches Captcha</div>
 Grafikmuster <span style="color:<?php echo $ksCaptchaTxFarb?>;background-color:<?php echo $ksCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span> &nbsp; &nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $ksCaptchaTxFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($ksCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="javascript:ColWin()"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> &nbsp; &nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $ksCaptchaHgFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($ksCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="javascript:ColWin()"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Geheimschlüssel</td>
 <td><div style="float:left"><input type="text" name="Schluessel" value="<?php echo $ksSchluessel?>" style="width:5em;color:#888888" /></div>
 <div class="admMini">Niemals manuell verändern!! Nur auf den notierten Wert setzen nach einer kompletten Rekonstruktion des Kalenders bei noch vorhandenen Daten.</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fKalKDatumsFormat($n,$bJ){
 $s1=KAL_TxSymbTag; $s2=KAL_TxSymbMon; $s3=($bJ?KAL_TxSymbJhr:'').KAL_TxSymbJhr;
 switch($n){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=KAL_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=KAL_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
?>