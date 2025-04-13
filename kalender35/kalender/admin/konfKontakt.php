<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Kontaktformular anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KKo');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Kontaktformular.</p>';
 $ksTxMailMeld=KAL_TxMailMeld; $ksTxEingabeFehl=KAL_TxEingabeFehl; $ksTxSendeErfo=KAL_TxSendeErfo; $ksTxSendeFehl=KAL_TxSendeFehl;
 $ksTxMailAn=KAL_TxMailAn; $ksTxMailBtr=KAL_TxMailBtr; $ksTxMailTxt=KAL_TxMailTxt; $ksKontaktLink=KAL_KontaktLink;
 $ksKontaktNDetail=KAL_KontaktNDetail; $ksKontaktMitMemo=KAL_KontaktMitMemo; $ksNutzerKontaktFeld=KAL_NutzerKontaktFeld;
 $ksKontaktAbsPflicht=KAL_KontaktAbsPflicht; $ksTxKontaktAbsender=KAL_TxKontaktAbsender;
 $ksMailPopup=KAL_MailPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch;
 $ksKontaktDSE1=KAL_KontaktDSE1; $ksKontaktDSE2=KAL_KontaktDSE2;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=txtVar('TxMailMeld'); if(fSetzKalWert($v,'TxMailMeld','"')) $bNeu=true;
 $v=txtVar('TxEingabeFehl'); if(fSetzKalWert($v,'TxEingabeFehl','"')) $bNeu=true;
 $v=txtVar('TxSendeErfo'); if(fSetzKalWert($v,'TxSendeErfo','"')) $bNeu=true;
 $v=txtVar('TxSendeFehl'); if(fSetzKalWert($v,'TxSendeFehl','"')) $bNeu=true;
 $v=txtVar('TxMailAn'); if(fSetzKalWert($v,'TxMailAn','"')) $bNeu=true;
 $v=txtVar('TxMailBtr'); if(fSetzKalWert($v,'TxMailBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxMailTxt')))); if(fSetzKalWert($v,'TxMailTxt',"'")) $bNeu=true;
 $v=(int)txtVar('KontaktNDetail'); if(fSetzKalWert(($v?true:false),'KontaktNDetail','')) $bNeu=true;
 $v=(int)txtVar('KontaktMitMemo'); if(fSetzKalWert(($v?true:false),'KontaktMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerKontaktFeld'); if(fSetzKalWert($v,'NutzerKontaktFeld','')) $bNeu=true;
 $v=(int)txtVar('KontaktAbsPflicht'); if(fSetzKalWert(($v?true:false),'KontaktAbsPflicht','')) $bNeu=true; $w=$v;
 $v=txtVar('TxKontaktAbsender'); if($w&&!$v) $v='Absender-eMail'; if(fSetzKalWert($v,'TxKontaktAbsender','"')) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzKalWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('KontaktLink'); if(fSetzKalWert($v,'KontaktLink',"'")) $bNeu=true;
 $v=txtVar('KontaktDSE1'); if(fSetzKalWert(($v?true:false),'KontaktDSE1','')) $bNeu=true;
 $v=txtVar('KontaktDSE2'); if(fSetzKalWert(($v?true:false),'KontaktDSE2','')) $bNeu=true;
 $v=txtVar('Captcha'); if(fSetzKalWert(($v?true:false),'Captcha','')) $bNeu=true;
 $v=txtVar('CaptchaTxFarb'); if(fSetzKalWert($v,'CaptchaTxFarb',"'")) $bNeu=true;
 $v=txtVar('CaptchaHgFarb'); if(fSetzKalWert($v,'CaptchaHgFarb',"'")) $bNeu=true;
 $v=txtVar('CaptchaTyp'); if(fSetzKalWert($v,'CaptchaTyp',"'")) $bNeu=true;
 $v=txtVar('CaptchaGrafisch'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
 $v=txtVar('CaptchaNumerisch'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
 $v=txtVar('CaptchaTextlich'); if(fSetzKalWert(($v?true:false)||($ksCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Formulareinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
echo $Msg.NL;
array_splice($kal_NutzerFelder,1,1); $nNFz=count($kal_NutzerFelder);
$sNOpt='<option value="0">--</option><option value="2">'.str_replace(';','`,',$kal_NutzerFelder[2]).'</option>'; for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.str_replace(';','`,',$kal_NutzerFelder[$j]).'</option>';
?>

<form action="konfKontakt.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Kontaktformular werden Besuchern folgende Meldungen angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift</td>
 <td><input type="text" name="TxMailMeld" value="<?php echo $ksTxMailMeld?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Treten Sie mit dem Verfasser des Termins in Kontakt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefehler</td>
 <td><input type="text" name="TxEingabeFehl" value="<?php echo $ksTxEingabeFehl?>" style="width:100%" /><div class="admMini">(Wird auch im Eingabeformular und Informationsformular verwendet.)<br />Empfehlung: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sendebestätigung</td>
 <td><input type="text" name="TxSendeErfo" value="<?php echo $ksTxSendeErfo?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Information wurde soeben versandt!</i> &nbsp; (Wird auch im Informationsformular verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sendefehler</td>
 <td><input type="text" name="TxSendeFehl" value="<?php echo $ksTxSendeFehl?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Nachricht konnte soeben nicht versandt werden!</i> &nbsp; (Wird auch im Infoformular verwendet.)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im Kontaktformular werden folgende Standardtexte vorgelegt, die jedoch vom Besucher überschrieben werden können.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Empfänger</td>
 <td><input type="text" name="TxMailAn" value="<?php echo $ksTxMailAn?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Verfasser dieses Termins</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardbetreff</td>
 <td><input type="text" name="TxMailBtr" value="<?php echo $ksTxMailBtr?>" style="width:100%" /><div class="admMini">Empfehlung: <i>interessanter Termin bei #A</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardmitteilung</td>
 <td><textarea name="TxMailTxt" cols="80" ros="8" style="height:9em"><?php echo str_replace('\n ',"\n",$ksTxMailTxt)?></textarea><div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter der Adresse #A einen Termin für den #D veröffentlicht. Zu diesem Termin interessiert mich ...<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="KontaktNDetail" value="0"<?php if(!$ksKontaktNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="KontaktNDetail" value="1"<?php if($ksKontaktNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="KontaktMitMemo" value="1"<?php if($ksKontaktMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerKontaktFeld" style="width:140px;"><?php echo str_replace('"'.$ksNutzerKontaktFeld.'"','"'.$ksNutzerKontaktFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Terminstruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Kontaktnachricht mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kontaktformular enthält standardmäßig ein Feld, in das der Absender seinen Namen und/oder seine E-Mail-Adresse eintragen kann.
Es kann aber auch ein zweites Feld extra <i>nur</i> für die Absender-E-Mail-Adresse bereitgestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">zweites<br />Absenderfeld</td>
 <td style="padding-top:5px;"><input type="text" name="TxKontaktAbsender" value="<?php echo $ksTxKontaktAbsender?>" size="10" style="width:10em;" /> angezeigter Name des Feldes
 <div class="admMini">(z.B. <i>E-Mail-Absender</i> oder leer lassen falls nicht verwendet)</div>
 <div><input type="checkbox" class="admCheck" name="KontaktAbsPflicht<?php if($ksKontaktAbsPflicht) echo '" checked="checked'?>" value="1" /> als Pflichtfeld mit Ausfüllzwang</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kontaktformular bzw. Informationsformular wird normalerweise im selben Fenster wie die Terminübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$ksMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($ksMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Kontaktformular im selben Fenster wie der Kalender aufgerufen wird,
wird als Verweisziel auf die Termindetails in der E-Mail das aufrufende Script angegeben.
Das kann das Kalenderscript <i>kalender.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Kontaktformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Termindetails in der E-Mail normalerweise das Kalenderscript <i>kalender.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die die Termindetails in der E-Mail <i>Kontaktaufnahme</i> angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input type="text" name="KontaktLink" value="<?php echo $ksKontaktLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Kontakt-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="KontaktDSE1" value="1"<?php if($ksKontaktDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="KontaktDSE2" value="1"<?php if($ksKontaktDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
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
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>