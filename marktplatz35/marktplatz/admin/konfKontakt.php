<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Kontaktformular konfigurieren','','KKo');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für das Kontaktformular. <a href="'.AM_Hilfe.'LiesMich.htm#2.11" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpTxMailMeld=MP_TxMailMeld; $mpTxEingabeFehl=MP_TxEingabeFehl; $mpTxSendeErfo=MP_TxSendeErfo; $mpTxSendeFehl=MP_TxSendeFehl;
 $mpTxMailAn=MP_TxMailAn; $mpTxMailBtr=MP_TxMailBtr; $mpTxMailTxt=MP_TxMailTxt; $mpKontaktNDetail=MP_KontaktNDetail; $mpNutzerKontaktFeld=MP_NutzerKontaktFeld;
 $mpTxKontaktAbsender=MP_TxKontaktAbsender; $mpKontaktAbsPflicht=MP_KontaktAbsPflicht; $mpKontaktMitMemo=MP_KontaktMitMemo;
 $mpMailPopup=MP_MailPopup; $mpPopupBreit=MP_PopupBreit; $mpPopupHoch=MP_PopupHoch;
 $mpKontaktDSE1=MP_KontaktDSE1; $mpKontaktDSE2=MP_KontaktDSE2;
 $mpKontaktLink=MP_KontaktLink; $mpCaptcha=MP_Captcha;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('TxMailMeld'); if(fSetzMPWert($v,'TxMailMeld','"')) $bNeu=true;
 $v=txtVar('TxEingabeFehl'); if(fSetzMPWert($v,'TxEingabeFehl','"')) $bNeu=true;
 $v=txtVar('TxSendeErfo'); if(fSetzMPWert($v,'TxSendeErfo','"')) $bNeu=true;
 $v=txtVar('TxSendeFehl'); if(fSetzMPWert($v,'TxSendeFehl','"')) $bNeu=true;
 $v=txtVar('TxMailAn'); if(fSetzMPWert($v,'TxMailAn','"')) $bNeu=true;
 $v=txtVar('TxMailBtr'); if(fSetzMPWert($v,'TxMailBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxMailTxt')))); if(fSetzMPWert($v,'TxMailTxt',"'")) $bNeu=true;
 $v=(int)txtVar('KontaktNDetail'); if(fSetzMPWert(($v?true:false),'KontaktNDetail','')) $bNeu=true;
 $v=(int)txtVar('KontaktMitMemo'); if(fSetzMPWert(($v?true:false),'KontaktMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerKontaktFeld'); if(fSetzMPWert($v,'NutzerKontaktFeld','')) $bNeu=true;
 $v=(int)txtVar('KontaktAbsPflicht'); if(fSetzMPWert(($v?true:false),'KontaktAbsPflicht','')) $bNeu=true; $w=$v;
 $v=txtVar('TxKontaktAbsender'); if($w&&!$v) $v='Absender-eMail'; if(fSetzMPWert($v,'TxKontaktAbsender','"')) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzMPWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzMPWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzMPWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('KontaktLink'); if(fSetzMPWert($v,'KontaktLink',"'")) $bNeu=true;
 $v=txtVar('KontaktDSE1'); if(fSetzMPWert(($v?true:false),'KontaktDSE1','')) $bNeu=true;
 $v=txtVar('KontaktDSE2'); if(fSetzMPWert(($v?true:false),'KontaktDSE2','')) $bNeu=true;
 $v=txtVar('Captcha'); if(fSetzMPWert(($v?true:false),'Captcha','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld='Die Formulareinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld='In die Datei <i>mpWerte.php</i> durfte nicht geschrieben werden!';
 }else{$Meld='Die Formulareinstellungen bleiben unverändert.'; $MTyp='Meld';}
}
$mpCaptchaTxFarb=MP_CaptchaTxFarb; $mpCaptchaHgFarb=MP_CaptchaHgFarb;

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
$aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNFz=count($aNF);
$sNOpt='<option value="0">--</option><option value="2">'.str_replace(';','`,',$aNF[2]).'</option>'; for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.str_replace(';','`,',$aNF[$j]).'</option>';
?>

<form action="konfKontakt.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Kontaktformular werden Besuchern folgende Meldungen angezeigt.</td></tr>
<tr class="admTabl">
 <td>Überschrift</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxMailMeld" value="<?php echo $mpTxMailMeld?>" /><div class="admMini">Empfehlung: <i>Treten Sie mit dem Verfasser des Inserates in Kontakt!</i></div></td>
</tr>
<tr class="admTabl">
 <td>Eingabefehler</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxEingabeFehl" value="<?php echo $mpTxEingabeFehl?>" /><div class="admMini">(Wird auch im Eingabeformular und Informationsformular verwendet.)<br />Empfehlung: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div></td>
</tr>
<tr class="admTabl">
 <td>Sendebestätigung</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxSendeErfo" value="<?php echo $mpTxSendeErfo?>" /><div class="admMini">Empfehlung: <i>Die Information wurde soeben versandt!</i> &nbsp; (Wird auch im Informationsformular verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td>Sendefehler</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxSendeFehl" value="<?php echo $mpTxSendeFehl?>" /><div class="admMini">Empfehlung: <i>Die Nachricht konnte soeben nicht versandt werden!</i> &nbsp; (Wird auch im Infoformular verwendet.)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im Kontaktformular werden folgende Standardtexte vorgelegt, die jedoch vom Besucher überschrieben werden können.</td></tr>
<tr class="admTabl">
 <td>E-Mail-Empfänger</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxMailAn" value="<?php echo $mpTxMailAn?>" /><div class="admMini">Empfehlung: <i>Verfasser dieses Inserates</i></div></td>
</tr>
<tr class="admTabl">
 <td>Standardbetreff</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxMailBtr" value="<?php echo $mpTxMailBtr?>" /><div class="admMini">Empfehlung: <i>Re: Ihr Inserateeintrag</i></div></td>
</tr>
<tr class="admTabl">
 <td valign="top" style="padding-top:6px;">Standardmitteilung</td>
 <td><textarea class="admEing" name="TxMailTxt" style="height:9em;"><?php echo str_replace('\n ',"\n",$mpTxMailTxt)?></textarea><div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter der Adresse #A ein Inserat im Segment #S veröffentlicht. Zu diesem Inserat interessiert mich ...<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="KontaktNDetail" value="0"<?php if(!$mpKontaktNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="KontaktNDetail" value="1"<?php if($mpKontaktNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="KontaktMitMemo" value="1"<?php if($mpKontaktMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerKontaktFeld" style="width:140px;"><?php echo str_replace('"'.$mpNutzerKontaktFeld.'"','"'.$mpNutzerKontaktFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Inseratestruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Information mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kontaktformular enthält standardmäßig ein Feld, in das der Absender seinen Namen und/oder seine E-Mail-Adresse eintragen kann.
Es kann aber auch ein zweites Feld extra <i>nur</i> für die Absender-E-Mail-Adresse bereitgestellt werden.</td></tr>
<tr class="admTabl">
 <td valign="top" style="padding-top:5px;">zweites<br />Absenderfeld</td>
 <td style="padding-top:5px;"><input type="text" name="TxKontaktAbsender" value="<?php echo $mpTxKontaktAbsender?>" size="10" style="width:10em;" /> angezeigter Name des Feldes <div class="admMini">(z.B. <i>E-Mail-Absender</i> oder leer lassen falls nicht verwendet)</div>
 <div><input type="checkbox" class="admCheck" name="KontaktAbsPflicht<?php if($mpKontaktAbsPflicht) echo '" checked="checked'?>" value="1" /> als Pflichtfeld mit Ausfüllzwang</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kontaktformular bzw. Informationsformular wird normalerweise im selben Fenster wie die Inserateübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td valign="top" style="padding-top:5px;">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$mpMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($mpMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $mpPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; <input type="text" name="PopupHoch" value="<?php echo $mpPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Kontaktformular im selben Fenster wie der Marktplatz aufgerufen wird,
wird als Verweisziel auf die Inseratedetails in der E-Mail das aufrufende Script angegeben.
Das kann das Martplatzscript <i>marktplatz.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Kontaktformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Inseratedetails in der E-Mail normalerweise das Marktplatzscript <i>marktplatz.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die Inseratedetails in der E-Mail angeben.</td></tr>
<tr class="admTabl">
 <td>Verweisziel</td>
 <td><input class="admEing" style="width:99%" type="text" name="KontaktLink" value="<?php echo $mpKontaktLink?>" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://), evt. auch mit QueryString</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Kontakt-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="KontaktDSE1" value="1"<?php if($mpKontaktDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="KontaktDSE2" value="1"<?php if($mpKontaktDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td>Captcha</td>
 <td><div style="padding-top:3px;"><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($mpCaptcha) echo' checked="checked"'?> /> verwenden &nbsp;
 Muster <span style="color:<?php echo $mpCaptchaTxFarb?>;background-color:<?php echo $mpCaptchaHgFarb?>;padding:1px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span></div></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>