<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminbenachrichtigungen anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KNa');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den Benachrichtigungsservice.</p>';
 $ksListenBenachr=KAL_ListenBenachr; $ksListenBenachTitel=KAL_ListenBenachTitel; $ksGastLBenachr=KAL_GastLBenachr;
 $ksDetailBenachr=KAL_DetailBenachr; $ksGastDBenachr=KAL_GastDBenachr; $ksTxBenachrService=KAL_TxBenachrService;
 $ksTxBenachrMeld=KAL_TxBenachrMeld; $ksTxBenachrErfo=KAL_TxBenachrErfo; $ksTxBenachrVorbei=KAL_TxBenachrVorbei;
 $ksBenachrOkMail=KAL_BenachrOkMail; $ksTxBenachrOkBtr=KAL_TxBenachrOkBtr; $ksTxBenachrOkTxt=KAL_TxBenachrOkTxt;
 $ksFreischaltNeuMail=KAL_FreischaltNeuMail; $ksTxBenachrUnmoegl=KAL_TxBenachrUnmoegl; $ksFreischaltWin=KAL_FreischaltWin;
 $ksTxBenachrUnbekannt=KAL_TxBenachrUnbekannt; $ksTxBenachrUnbkBtr=KAL_TxBenachrUnbkBtr; $ksTxBenachrUnbkTxt=KAL_TxBenachrUnbkTxt;
 $ksTxBenachrSendBtr=KAL_TxBenachrSendBtr; $ksTxBenachrSendTxt=KAL_TxBenachrSendTxt; $ksFormAendZusageInfo=KAL_FormAendZusageInfo;
 $ksMailPopup=KAL_MailPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch;
 $ksBenachrNDetail=KAL_BenachrNDetail; $ksNutzerBenachrFeld=KAL_NutzerBenachrFeld; $ksBenachrMitMemo=KAL_BenachrMitMemo; $ksBenachrLink=KAL_BenachrLink;
 $ksTxNnUebersicht=KAL_TxNnUebersicht; $ksTxNnUnveraendert=KAL_TxNnUnveraendert;
 $ksTxNnKeineBenachr=KAL_TxNnKeineBenachr; $ksTxNnLoeschen=KAL_TxNnLoeschen; $ksTxNnGeloescht=KAL_TxNnGeloescht;
 $ksNachrichtDSE1=KAL_NachrichtDSE1; $ksNachrichtDSE2=KAL_NachrichtDSE2;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=(int)txtVar('ListenBenachr'); if(fSetzKalWert($v,'ListenBenachr','')) $bNeu=true;
 $v=txtVar('ListenBenachTitel'); if(fSetzKalWert($v,'ListenBenachTitel','"')) $bNeu=true;
 $v=txtVar('GastLBenachr'); if(fSetzKalWert(($v?true:false),'GastLBenachr','')) $bNeu=true;
 $v=(int)txtVar('DetailBenachr'); if(fSetzKalWert($v,'DetailBenachr','')) $bNeu=true;
 $v=txtVar('GastDBenachr'); if(fSetzKalWert(($v?true:false),'GastDBenachr','')) $bNeu=true;
 $v=txtVar('TxBenachrService'); if(fSetzKalWert($v,'TxBenachrService','"')) $bNeu=true;
 $v=txtVar('TxBenachrMeld'); if(fSetzKalWert($v,'TxBenachrMeld',"'")) $bNeu=true;
 $v=txtVar('TxBenachrErfo'); if(fSetzKalWert($v,'TxBenachrErfo',"'")) $bNeu=true;
 $v=txtVar('TxBenachrVorbei'); if(fSetzKalWert($v,'TxBenachrVorbei',"'")) $bNeu=true;
 $v=(int)txtVar('BenachrOkMail'); if(fSetzKalWert(($v?true:false),'BenachrOkMail','')) $bNeu=true;
 $v=txtVar('TxBenachrOkBtr'); if(fSetzKalWert($v,'TxBenachrOkBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrOkTxt')))); if(fSetzKalWert($v,'TxBenachrOkTxt',"'")) $bNeu=true;
 $v=(int)txtVar('FreischaltNeuMail'); if(fSetzKalWert(($v?true:false),'FreischaltNeuMail','')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnmoegl')))); if(fSetzKalWert($v,'TxBenachrUnmoegl',"'")) $bNeu=true;
 $v=txtVar('FreischaltWin'); if(fSetzKalWert($v,'FreischaltWin',"'")) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnbekannt')))); if(fSetzKalWert($v,'TxBenachrUnbekannt',"'")) $bNeu=true;
 $v=txtVar('TxBenachrUnbkBtr'); if(fSetzKalWert($v,'TxBenachrUnbkBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrUnbkTxt')))); if(fSetzKalWert($v,'TxBenachrUnbkTxt',"'")) $bNeu=true;
 $v=txtVar('TxBenachrSendBtr'); if(fSetzKalWert($v,'TxBenachrSendBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxBenachrSendTxt')))); if(fSetzKalWert($v,'TxBenachrSendTxt',"'")) $bNeu=true;
 $v=(int)txtVar('BenachrNDetail'); if(fSetzKalWert(($v?true:false),'BenachrNDetail','')) $bNeu=true;
 $v=(int)txtVar('BenachrMitMemo'); if(fSetzKalWert(($v?true:false),'BenachrMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerBenachrFeld'); if(fSetzKalWert($v,'NutzerBenachrFeld','')) $bNeu=true;
 $v=txtVar('BenachrLink'); if(fSetzKalWert($v,'BenachrLink',"'")) $bNeu=true;
 $v=txtVar('FormAendZusageInfo'); if(fSetzKalWert($v,'FormAendZusageInfo',"'")) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzKalWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('TxNnUebersicht'); if(fSetzKalWert($v,'TxNnUebersicht',"'")) $bNeu=true;
 $v=txtVar('TxNnUnveraendert'); if(fSetzKalWert($v,'TxNnUnveraendert',"'")) $bNeu=true;
 $v=txtVar('TxNnKeineBenachr'); if(fSetzKalWert($v,'TxNnKeineBenachr',"'")) $bNeu=true;
 $v=txtVar('TxNnLoeschen'); if(fSetzKalWert($v,'TxNnLoeschen',"'")) $bNeu=true;
 $v=txtVar('TxNnGeloescht'); if(fSetzKalWert($v,'TxNnGeloescht',"'")) $bNeu=true;
 $v=txtVar('NachrichtDSE1'); if(fSetzKalWert(($v?true:false),'NachrichtDSE1','')) $bNeu=true;
 $v=txtVar('NachrichtDSE2'); if(fSetzKalWert(($v?true:false),'NachrichtDSE2','')) $bNeu=true;
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

<form action="konfBenachr.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Benachrichtigungsservice ist aktiv, wenn in der Terminliste eine zusätzliche Spalte oder in der Detailanzeige eine zusätzliche Zeile mit einem <img src="<?php echo $sHttp?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter eingeblendet wird.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Terminliste</td>
 <td>zusätzlichen Schalter vor Spalte <select name="ListenBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLBenachr" value="1"<?php if($ksGastLBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenBenachTitel" value="<?php echo $ksListenBenachTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Detailanzeige</td>
 <td>zusätzliche Servicezeile vor Zeile <select name="DetailBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDBenachr" value="1"<?php if($ksGastDBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste<br />
 Zeilenbeschriftung <input type="text" name="TxBenachrService" value="<?php echo $ksTxBenachrService?>" size="25" style="width:180px;" /> <span class="admMini">Empfehlung: <i>Benachrichtigung anfordern</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn ein Benutzer eine Benachrichtigungsanforderung in das Formular einträgt, werden folgende Texte benutzt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungen</td>
 <td><input type="text" name="TxBenachrMeld" value="<?php echo $ksTxBenachrMeld?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Fordern Sie eine Benachrichtigung bei Terminänderung an.</i></div>
 <input type="text" name="TxBenachrErfo" value="<?php echo $ksTxBenachrErfo?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Ihr Benachrichtigungswunsch wurde vorgemerkt.</i></div>
 <input type="text" name="TxBenachrVorbei" value="<?php echo $ksTxBenachrVorbei?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Es sind keine Benachrichtigungen in der Vergangenheit möglich.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigungs-<br />E-Mail</td>
 <td><div><input class="admCheck" type="checkbox" name="BenachrOkMail" value="1"<?php if($ksBenachrOkMail) echo' checked="checked"'?> /> Bestätigung nach Benachrichtigungsanforderung versenden &nbsp; <span class="admMini">Empfehlung: <i>nicht</i> aktivieren</span></div>
 <input type="text" name="TxBenachrOkBtr" value="<?php echo $ksTxBenachrOkBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Benachchrichtigung bei #A vorgemerkt</i></div>
 <textarea name="TxBenachrOkTxt" cols="80" rows="9" style="height:10em"><?php echo str_replace('\n ',"\n",$ksTxBenachrOkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Benachrichtigung angefordert. Diese wird bei Veränderungen zu folgendem Termin erfolgen:<br />#D<br />Mit freundlichen Grüßen</i></div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Benachrichtigungen können nur von angemeldeten Benutzern oder von extra dafür freigeschalteten Gästen angefordert werden. Bei einem Benachrichtigungswunsch eines momentan nicht berechtigten Gastes werden folgende Texte verwendet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />wunsch</td>
 <td><div><input class="admRadio" type="radio" name="FreischaltNeuMail" value="0"<?php if(!$ksFreischaltNeuMail) echo' checked="checked"'?> /> nicht berechtigte Gäste können sich <i>nicht</i> selbst freischalten</div>
 <textarea name="TxBenachrUnmoegl" cols="80" rows="2" style="height:3em"><?php echo str_replace('\n ',"\n",$ksTxBenachrUnmoegl)?></textarea>
 <div class="admMini">Empfehlung: <i># ist für diesen Dienst nicht vorgesehen. Wenden Sie sich an den Webmaster.</i></div>
 <div style="margin-top:5px;"><input class="admRadio" type="radio" name="FreischaltNeuMail" value="1"<?php if($ksFreischaltNeuMail) echo' checked="checked"'?> /> aktuell nicht berechtigte Gäste können sich selbst freischalten</div>
 <textarea name="TxBenachrUnbekannt" cols="80" rows="2" style="height:3em"><?php echo str_replace('\n ',"\n",$ksTxBenachrUnbekannt)?></textarea>
 <div class="admMini">Empfehlung: <i># ist für diesen Dienst noch nicht freigeschaltet. Sie können jetzt die Freischaltung für # anfordern.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischalt-E-Mail<div class="admMini" style="margin-top:5px;color:#777;">nur bei aktivierter<br />Selbstfreischaltung</div></td>
 <td><input type="text" name="TxBenachrUnbkBtr" value="<?php echo $ksTxBenachrUnbkBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Benachrichtigungswunsch bei #A</i></div>
 <textarea name="TxBenachrUnbkTxt" cols="80" rows="9" style="height:10em"><?php echo str_replace('\n ',"\n",$ksTxBenachrUnbkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Benachrichtigung angefordert.<br />Ihre E-Mail-Adresse ist für diesen Dienst noch nicht freigeschaltet. Sie können diese Freischaltung erreichen, indem Sie jetzt den Link<br />#L<br /> aufrufen.<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td>Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($ksFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (kalSeite.htm)</option><option value="Popup<?php if($ksFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (kalPopup.htm)</option><option value="Freischalt<?php if($ksFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (kalFreischalt.htm)</option></select></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Benachrichtigungsformular wird genauso wie das Informationsformular und das Kontaktformular bzw. Erinnerungsformular normalerweise im selben Fenster wie die Terminübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$ksMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($ksMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="BenachrNDetail" value="0"<?php if(!$ksBenachrNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="BenachrNDetail" value="1"<?php if($ksBenachrNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="BenachrMitMemo" value="1"<?php if($ksBenachrMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerBenachrFeld" style="width:140px;"><?php echo str_replace('"'.$ksNutzerBenachrFeld.'"','"'.$ksNutzerBenachrFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Terminstruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Benachrichtigung mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Benachrichtigungsformular im selben Fenster wie der Kalender aufgerufen wird,
wird als Verweisziel auf die Termindetails in der E-Mail das aufrufende Script angegeben.
Das kann das Kalenderscript <i>kalender.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Benachrichtigungsformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Termindetails in der E-Mail normalerweise das Kalenderscript <i>kalender.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die die Termindetails in der E-Mail <i>Terminänderungsbenachrichtigung</i> angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input type="text" name="BenachrLink" value="<?php echo $ksBenachrLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Benachrichtigungs-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="NachrichtDSE1" value="1"<?php if($ksNachrichtDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="NachrichtDSE2" value="1"<?php if($ksNachrichtDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
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

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn wegen Terminveränderung der vereinbarte Benachrichtigungsfall eintritt, wird an den Besucher folgende E-Mail-Nachricht versandt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />E-Mail</td>
 <td><input type="text" name="TxBenachrSendBtr" value="<?php echo $ksTxBenachrSendBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Benachrichtigung von #A</i></div>
 <textarea name="TxBenachrSendTxt" cols="80" rows="9" style="height:10em"><?php echo str_replace('\n ',"\n",$ksTxBenachrSendTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Benachrichtigung angefordert, falls sich am folgenden Termin etwas ändert.<br />#D<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigung<br>an Zusagende</td>
 <td>
  <div><input class="admCheck" type="checkbox" name="FormAendZusageInfo" value="1"<?php if($ksFormAendZusageInfo) echo' checked="checked"'?> /> diese Nachricht über Terminveränderung auch an Zusagende senden</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Im Benutzerzentrum kann es eine Seite zum Verwalten der eigenen Benachrichtigungswünsche geben. Auf dieser werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br>verwaltung</td>
 <td><input type="text" name="TxNnUebersicht" value="<?php echo $ksTxNnUebersicht?>" size="40" style="width:50%" />
 <div class="admMini">Empfehlung: <i>Benachrichtigungsübersicht</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungen der<br>Benachrichtigungs-<br>seite</td>
 <td><input type="text" name="TxNnUnveraendert" value="<?php echo $ksTxNnUnveraendert?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Die Benachrichtigungen bleiben unverändert.</i></div>
 <input type="text" name="TxNnKeineBenachr" value="<?php echo $ksTxNnKeineBenachr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Sie haben derzeit keine aktiven Benachrichtigungswünsche.</i></div>
 <input type="text" name="TxNnLoeschen" value="<?php echo $ksTxNnLoeschen?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Wollen Sie die #N markierten Benachrichtigungen wirklich löschen?</i></div>
 <input type="text" name="TxNnGeloescht" value="<?php echo $ksTxNnGeloescht?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>#N Benachrichtigungen wurden gelöscht.</i></div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>