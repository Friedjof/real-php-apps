<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benachrichtigungsservice konfigurieren','','KNa');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für den Benachrichtigungsservice. <a href="'.AM_Hilfe.'LiesMich.htm#2.12" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpListenBenachr=MP_ListenBenachr; $mpGastLBenachr=MP_GastLBenachr; $mpListenBenachTitel=MP_ListenBenachTitel;
 $mpDetailBenachr=MP_DetailBenachr; $mpGastDBenachr=MP_GastDBenachr; $mpTxBenachrService=MP_TxBenachrService;
 $mpTxBenachrMeld=MP_TxBenachrMeld; $mpTxBenachrErfo=MP_TxBenachrErfo;
 $mpBenachrOkMail=MP_BenachrOkMail; $mpTxBenachrOkBtr=MP_TxBenachrOkBtr; $mpTxBenachrOkTxt=MP_TxBenachrOkTxt;
 $mpFreischaltNeuMail=MP_FreischaltNeuMail; $mpTxBenachrUnmoegl=MP_TxBenachrUnmoegl; $mpFreischaltWin=MP_FreischaltWin;
 $mpTxBenachrUnbekannt=MP_TxBenachrUnbekannt; $mpTxBenachrUnbkBtr=MP_TxBenachrUnbkBtr; $mpTxBenachrUnbkTxt=MP_TxBenachrUnbkTxt;
 $mpTxBenachrSendBtr=MP_TxBenachrSendBtr; $mpTxBenachrSendTxt=MP_TxBenachrSendTxt;
 $mpMailPopup=MP_MailPopup; $mpPopupBreit=MP_PopupBreit; $mpPopupHoch=MP_PopupHoch; $mpCaptcha=MP_Captcha;
 $mpBenachrNDetail=MP_BenachrNDetail; $mpNutzerBenachrFeld=MP_NutzerBenachrFeld; $mpBenachrMitMemo=MP_BenachrMitMemo; $mpBenachrLink=MP_BenachrLink;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=(int)txtVar('ListenBenachr'); if(fSetzMPWert($v,'ListenBenachr','')) $bNeu=true;
 $v=txtVar('ListenBenachTitel'); if(fSetzMPWert($v,'ListenBenachTitel','"')) $bNeu=true;
 $v=txtVar('GastLBenachr'); if(fSetzMPWert(($v?true:false),'GastLBenachr','')) $bNeu=true;
 $v=(int)txtVar('DetailBenachr'); if(fSetzMPWert($v,'DetailBenachr','')) $bNeu=true;
 $v=txtVar('GastDBenachr'); if(fSetzMPWert(($v?true:false),'GastDBenachr','')) $bNeu=true;
 $v=txtVar('TxBenachrService'); if(fSetzMPWert($v,'TxBenachrService','"')) $bNeu=true;
 $v=txtVar('TxBenachrMeld'); if(fSetzMPWert($v,'TxBenachrMeld',"'")) $bNeu=true;
 $v=txtVar('TxBenachrErfo'); if(fSetzMPWert($v,'TxBenachrErfo',"'")) $bNeu=true;
 $v=(int)txtVar('BenachrOkMail'); if(fSetzMPWert(($v?true:false),'BenachrOkMail','')) $bNeu=true;
 $v=txtVar('TxBenachrOkBtr'); if(fSetzMPWert($v,'TxBenachrOkBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrOkTxt')))); if(fSetzMPWert($v,'TxBenachrOkTxt',"'")) $bNeu=true;
 $v=(int)txtVar('FreischaltNeuMail'); if(fSetzMPWert(($v?true:false),'FreischaltNeuMail','')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnmoegl')))); if(fSetzMPWert($v,'TxBenachrUnmoegl',"'")) $bNeu=true;
 $v=txtVar('FreischaltWin'); if(fSetzMPWert($v,'FreischaltWin',"'")) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnbekannt')))); if(fSetzMPWert($v,'TxBenachrUnbekannt',"'")) $bNeu=true;
 $v=txtVar('TxBenachrUnbkBtr'); if(fSetzMPWert($v,'TxBenachrUnbkBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnbkTxt')))); if(fSetzMPWert($v,'TxBenachrUnbkTxt',"'")) $bNeu=true;
 $v=txtVar('TxBenachrSendBtr'); if(fSetzMPWert($v,'TxBenachrSendBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrSendTxt')))); if(fSetzMPWert($v,'TxBenachrSendTxt',"'")) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzMPWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzMPWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzMPWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('Captcha'); if(fSetzMPWert(($v?true:false),'Captcha','')) $bNeu=true;
 $v=(int)txtVar('BenachrNDetail'); if(fSetzMPWert(($v?true:false),'BenachrNDetail','')) $bNeu=true;
 $v=(int)txtVar('BenachrMitMemo'); if(fSetzMPWert(($v?true:false),'BenachrMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerBenachrFeld'); if(fSetzMPWert($v,'NutzerBenachrFeld','')) $bNeu=true;
 $v=txtVar('BenachrLink'); if(fSetzMPWert($v,'BenachrLink',"'")) $bNeu=true;
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

<form action="konfBenachr.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Benachrichtigungsservice ist aktiv, wenn in der Inserateliste eine zusätzliche Spalte oder in der Detailanzeige eine zusätzliche Zeile mit einem <img src="<?php echo MPPFAD?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter eingeblendet wird.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Inserateliste</td>
 <td>zusätzlichen Schalter vor Spalte <select name="ListenBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<51;$i++) echo '<option value="'.$i.'"'.($mpListenBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLBenachr" value="1"<?php if($mpGastLBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenBenachTitel" value="<?php echo $mpListenBenachTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Detailanzeige</td>
 <td>zusätzliche Servicezeile vor Zeile <select name="DetailBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<51;$i++) echo '<option value="'.$i.'"'.($mpDetailBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDBenachr" value="1"<?php if($mpGastDBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste<br />
 Zeilenbeschriftung <input type="text" name="TxBenachrService" value="<?php echo $mpTxBenachrService?>" size="25" style="width:150px;" /> <span class="admMini">Empfehlung: <i>Benachrichtigung anfordern</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn ein Benutzer eine Benachrichtigungsanforderung in das Formular einträgt, werden folgende Texte benutzt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungen</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxBenachrMeld" value="<?php echo $mpTxBenachrMeld?>" size="40" />
 <div class="admMini">Empfehlung: <i>Fordern Sie eine Benachrichtigung bei Inserateänderung an.</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxBenachrErfo" value="<?php echo $mpTxBenachrErfo?>" size="40" />
 <div class="admMini">Empfehlung: <i>Ihr Benachrichtigungswunsch wurde vorgemerkt.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigungs-<br />E-Mail</td>
 <td><div><input class="admCheck" type="checkbox" name="BenachrOkMail" value="1"<?php if($mpBenachrOkMail) echo' checked="checked"'?> /> Bestätigung nach Benachrichtigungsanforderung versenden &nbsp; <span class="admMini">Empfehlung: <i>nicht</i> aktivieren</span></div>
 <input class="admEing" style="width:99%" type="text" name="TxBenachrOkBtr" value="<?php echo $mpTxBenachrOkBtr?>" size="40" />
 <div class="admMini">Empfehlung: <i>Re: Benachchrichtigung bei #A vorgemerkt</i></div>
 <textarea class="admEing" name="TxBenachrOkTxt" style="height:9em;"><?php echo str_replace('\n ',"\n",$mpTxBenachrOkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A im Segment #S eine Benachrichtigung angefordert. Diese wird bei Veränderungen zu folgendem Inserat erfolgen:<br />#D<br />Mit freundlichen Grüßen</i></div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Benachrichtigungen können nur von angemeldeten Benutzern oder von extra dafür freigeschalteten Gästen angefordert werden. Bei einem Benachrichtigungswunsch eines momentan nicht berechtigten Gastes werden folgende Texte verwendet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />wunsch</td>
 <td><div><input class="admRadio" type="radio" name="FreischaltNeuMail" value="0"<?php if(!$mpFreischaltNeuMail) echo' checked="checked"'?> /> nicht berechtigte Gäste können sich <i>nicht</i> selbst freischalten</div>
 <textarea class="admEing" name="TxBenachrUnmoegl" rows="2"><?php echo str_replace('\n ',"\n",$mpTxBenachrUnmoegl)?></textarea>
 <div class="admMini">Empfehlung: <i>#N ist für diesen Dienst nicht vorgesehen. Wenden Sie sich an den Webmaster.</i></div>
 <div style="margin-top:5px;"><input class="admRadio" type="radio" name="FreischaltNeuMail" value="1"<?php if($mpFreischaltNeuMail) echo' checked="checked"'?> /> aktuell nicht berechtigte Gäste können sich selbst freischalten</div>
 <textarea class="admEing" name="TxBenachrUnbekannt" rows="2"><?php echo str_replace('\n ',"\n",$mpTxBenachrUnbekannt)?></textarea>
 <div class="admMini">Empfehlung: <i>#N ist für diesen Dienst noch nicht freigeschaltet. Sie können jetzt die Freischaltung für #N anfordern.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischalt-E-Mail<div class="admMini" style="margin-top:5px;color:#777;">nur bei aktivierter<br />Selbstfreischaltung</div></td>
 <td><input class="admEing" style="width:99%" type="text" name="TxBenachrUnbkBtr" value="<?php echo $mpTxBenachrUnbkBtr?>" size="40" />
 <div class="admMini">Empfehlung: <i>Re: Benachrichtigungswunsch bei #A</i></div>
 <textarea class="admEing" name="TxBenachrUnbkTxt" style="height:9em;"><?php echo str_replace('\n ',"\n",$mpTxBenachrUnbkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A im Segment #S eine Benachrichtigung angefordert.<br />Ihre E-Mail-Adresse ist für diesen Dienst noch nicht freigeschaltet. Sie können diese Freischaltung erreichen, indem Sie jetzt den Link<br />#L<br /> aufrufen.<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td>Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($mpFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (marktSeite.htm)</option><option value="Popup<?php if($mpFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (marktPopup.htm)</option><option value="Freischalt<?php if($mpFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (marktFreischalt.htm)</option></select></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kontaktformular bzw. Informationsformular wird normalerweise im selben Fenster wie die Inserateübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$mpMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($mpMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $mpPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $mpPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div style="padding-top:3px;"><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($mpCaptcha) echo' checked="checked"'?> /> verwenden &nbsp;
 Muster <span style="color:<?php echo $mpCaptchaTxFarb?>;background-color:<?php echo $mpCaptchaHgFarb?>;padding:1px;border-color:#234;border-style:solid;border-width:1px;"><b>X1234</b></span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn wegen Inserateveränderung der vereinbarte Benachrichtigungsfall eintritt, wird an den Besucher folgende E-Mail-Nachricht versandt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />E-Mail</td>
 <td><input class="admEing" style="width:99%" type="text" name="TxBenachrSendBtr" value="<?php echo $mpTxBenachrSendBtr?>" size="40" />
 <div class="admMini">Empfehlung: <i>Re: Benachrichtigung von #A</i></div>
 <textarea class="admEing" name="TxBenachrSendTxt" style="height:9em;"><?php echo str_replace('\n ',"\n",$mpTxBenachrSendTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A im Segment #S eine Benachrichtigung angefordert, falls sich am folgenden Inserat etwas ändert.<br />#D<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="BenachrNDetail" value="0"<?php if(!$mpBenachrNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="BenachrNDetail" value="1"<?php if($mpBenachrNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="BenachrMitMemo" value="1"<?php if($mpBenachrMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerBenachrFeld" style="width:140px;"><?php echo str_replace('"'.$mpNutzerBenachrFeld.'"','"'.$mpNutzerBenachrFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Inseratestruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Information mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">In der Benachrichtigungs-E-Mail wird auch ein Link zu den Inseratedetails angegeben.
Das ist standardmäßig das Martplatzscript <i>marktplatz.php</i>.<br>
Bei includierten Aufrufen kann es aber auch das einbettende Script sein.
Für diesen Fall können Sie das Verweisziel auf die Inseratedetails hier angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input class="admEing" style="width:99%" type="text" name="BenachrLink" value="<?php echo $mpBenachrLink?>" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://), evt. auch mit QueryString</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>