<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Informationsformular anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KIf');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Informationsformular.</p>';
 $ksTxInfoMeld=KAL_TxInfoMeld; $ksTxEingabeFehl=KAL_TxEingabeFehl; $ksTxSendeErfo=KAL_TxSendeErfo; $ksTxSendeFehl=KAL_TxSendeFehl;
 $ksTxInfoBtr=KAL_TxInfoBtr; $ksTxInfoTxt=KAL_TxInfoTxt; $ksInfoLink=KAL_InfoLink;
 $ksInfoAbsPflicht=KAL_InfoAbsPflicht; $ksTxInfoAbsender=KAL_TxInfoAbsender;
 $ksInfoNDetail=KAL_InfoNDetail; $ksInfoMitMemo=KAL_InfoMitMemo; $ksNutzerInfoFeld=KAL_NutzerInfoFeld;
 $ksMailPopup=KAL_MailPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch;
 $ksInfoDSE1=KAL_InfoDSE1; $ksInfoDSE2=KAL_InfoDSE2;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=txtVar('TxInfoMeld'); if(fSetzKalWert($v,'TxInfoMeld','"')) $bNeu=true;
 $v=txtVar('TxEingabeFehl'); if(fSetzKalWert($v,'TxEingabeFehl','"')) $bNeu=true;
 $v=txtVar('TxSendeErfo'); if(fSetzKalWert($v,'TxSendeErfo','"')) $bNeu=true;
 $v=txtVar('TxSendeFehl'); if(fSetzKalWert($v,'TxSendeFehl','"')) $bNeu=true;
 $v=txtVar('TxInfoBtr'); if(fSetzKalWert($v,'TxInfoBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxInfoTxt')))); if(fSetzKalWert($v,'TxInfoTxt',"'")) $bNeu=true;
 $v=(int)txtVar('InfoNDetail'); if(fSetzKalWert(($v?true:false),'InfoNDetail','')) $bNeu=true;
 $v=(int)txtVar('InfoMitMemo'); if(fSetzKalWert(($v?true:false),'InfoMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerInfoFeld'); if(fSetzKalWert($v,'NutzerInfoFeld','')) $bNeu=true;
 $v=(int)txtVar('InfoAbsPflicht'); if(fSetzKalWert(($v?true:false),'InfoAbsPflicht','')) $bNeu=true; $w=$v;
 $v=txtVar('TxInfoAbsender'); if($w&&!$v) $v='Absender-eMail'; if(fSetzKalWert($v,'TxInfoAbsender','"')) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzKalWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('InfoLink'); if(fSetzKalWert($v,'InfoLink',"'")) $bNeu=true;
 $v=txtVar('InfoDSE1'); if(fSetzKalWert(($v?true:false),'InfoDSE1','')) $bNeu=true;
 $v=txtVar('InfoDSE2'); if(fSetzKalWert(($v?true:false),'InfoDSE2','')) $bNeu=true;
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

<form name="farbform" action="konfInfo.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Informationsformular werden Besuchern folgende Meldungen angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift</td>
 <td><input type="text" name="TxInfoMeld" value="<?php echo $ksTxInfoMeld?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Informieren Sie einen Bekannten über diesen Termin!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefehler</td>
 <td><input type="text" name="TxEingabeFehl" value="<?php echo $ksTxEingabeFehl?>" style="width:100%" /><div class="admMini">(Wird auch im Eingabeformular und Kontaktformular verwendet.)<br />Empfehlung: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sendebestätigung</td>
 <td><input type="text" name="TxSendeErfo" value="<?php echo $ksTxSendeErfo?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Information wurde soeben versandt!</i> &nbsp; (Wird auch im Kontaktformular verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sendefehler</td>
 <td><input type="text" name="TxSendeFehl" value="<?php echo $ksTxSendeFehl?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Nachricht konnte soeben nicht versandt werden!</i> &nbsp; (Wird auch im Kontaktformular verwendet.)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im Informationsformular werden folgende Standardtexte vorgelegt, die jedoch vom Besucher überschrieben werden können.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Standardbetreff</td>
 <td><input type="text" name="TxInfoBtr" value="<?php echo $ksTxInfoBtr?>" style="width:100%" /><div class="admMini">Empfehlung: <i>interessanter Termin bei #A</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardmitteilung</td>
 <td><textarea name="TxInfoTxt" cols="80" rows="8" style="height:9em"><?php echo str_replace('\n ',"\n",$ksTxInfoTxt)?></textarea><div class="admMini">Empfehlung:<br /><i>Hallo,<br />unter der Adresse #A habe ich einen interessanten Termin gefunden. Unten der Link und ein Auszug.<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="InfoNDetail" value="0"<?php if(!$ksInfoNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="InfoNDetail" value="1"<?php if($ksInfoNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="InfoMitMemo" value="1"<?php if($ksInfoMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerInfoFeld" style="width:140px;"><?php echo str_replace('"'.$ksNutzerInfoFeld.'"','"'.$ksNutzerInfoFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Terminstruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Information mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Informationsformular enthält standardmäßig <i>ein</i> Feld, in das der Absender seinen Namen und/oder seine E-Mail-Adresse eintragen kann.
Es kann aber auch ein zweites Feld extra <i>nur</i> für die Absender-E-Mail-Adresse bereitgestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">zweites<br />Absenderfeld</td>
 <td style="padding-top:5px;"><input type="text" name="TxInfoAbsender" value="<?php echo $ksTxInfoAbsender?>" size="10" style="width:10em;" /> angezeigter Name des Feldes
 <div class="admMini">(z.B. <i>E-Mail-Absender</i> oder leer lassen falls nicht verwendet)</div>
 <div><input type="checkbox" class="admCheck" name="InfoAbsPflicht<?php if($ksInfoAbsPflicht) echo '" checked="checked'?>" value="1" /> als Pflichtfeld mit Ausfüllzwang</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Informationsformular bzw. Kontaktformular wird normalerweise im selben Fenster wie die Terminübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$ksMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($ksMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Informationsformular im selben Fenster wie der Kalender aufgerufen wird,
wird als Verweisziel auf die Termindetails in der E-Mail das aufrufende Script angegeben.
Das kann das Kalenderscript <i>kalender.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Informationsformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Termindetails in der E-Mail normalerweise das Kalenderscript <i>kalender.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die die Termindetails in der E-Mail <i>Termininformation</i> angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input type="text" name="InfoLink" value="<?php echo $ksInfoLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Informations-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="InfoDSE1" value="1"<?php if($ksInfoDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="InfoDSE2" value="1"<?php if($ksInfoDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
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