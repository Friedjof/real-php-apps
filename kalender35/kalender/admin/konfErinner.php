<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminerinnerungen anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KEi');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den Erinnerungsservice.</p>';
 $ksListenErinn=KAL_ListenErinn; $ksListenErinTitel=KAL_ListenErinTitel; $ksGastLErinn=KAL_GastLErinn;
 $ksDetailErinn=KAL_DetailErinn; $ksGastDErinn=KAL_GastDErinn; $ksTxErinnService=KAL_TxErinnService;
 $ksTxErinnMeld=KAL_TxErinnMeld; $ksTxErinnErfo=KAL_TxErinnErfo; $ksTxErinnVorbei=KAL_TxErinnVorbei;
 $ksErinnOkMail=KAL_ErinnOkMail; $ksTxErinnOkBtr=KAL_TxErinnOkBtr; $ksTxErinnOkTxt=KAL_TxErinnOkTxt;
 $ksFreischaltNeuMail=KAL_FreischaltNeuMail; $ksTxErinnUnmoegl=KAL_TxErinnUnmoegl; $ksFreischaltWin=KAL_FreischaltWin;
 $ksTxErinnUnbekannt=KAL_TxErinnUnbekannt; $ksTxErinnUnbkBtr=KAL_TxErinnUnbkBtr; $ksTxErinnUnbkTxt=KAL_TxErinnUnbkTxt;
 $ksTxErinnSendBtr=KAL_TxErinnSendBtr; $ksTxErinnSendTxt=KAL_TxErinnSendTxt;
 $ksMailPopup=KAL_MailPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch;
 $ksErinnNDetail=KAL_ErinnNDetail; $ksNutzerErinnFeld=KAL_NutzerErinnFeld; $ksErinnMitMemo=KAL_ErinnMitMemo; $ksErinnLink=KAL_ErinnLink;
 $ksTxNeUebersicht=KAL_TxNeUebersicht; $ksTxNeUnveraendert=KAL_TxNeUnveraendert;
 $ksTxNeKeineErinn=KAL_TxNeKeineErinn; $ksTxNeLoeschen=KAL_TxNeLoeschen; $ksTxNeGeloescht=KAL_TxNeGeloescht;
 $ksErinnernDSE1=KAL_ErinnernDSE1; $ksErinnernDSE2=KAL_ErinnernDSE2;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=(int)txtVar('ListenErinn'); if(fSetzKalWert($v,'ListenErinn','')) $bNeu=true;
 $v=txtVar('ListenErinTitel'); if(fSetzKalWert($v,'ListenErinTitel','"')) $bNeu=true;
 $v=txtVar('GastLErinn'); if(fSetzKalWert(($v?true:false),'GastLErinn','')) $bNeu=true;
 $v=(int)txtVar('DetailErinn'); if(fSetzKalWert($v,'DetailErinn','')) $bNeu=true;
 $v=txtVar('GastDErinn'); if(fSetzKalWert(($v?true:false),'GastDErinn','')) $bNeu=true;
 $v=txtVar('TxErinnService'); if(fSetzKalWert($v,'TxErinnService','"')) $bNeu=true;
 $v=txtVar('TxErinnMeld'); if(fSetzKalWert($v,'TxErinnMeld',"'")) $bNeu=true;
 $v=txtVar('TxErinnErfo'); if(fSetzKalWert($v,'TxErinnErfo',"'")) $bNeu=true;
 $v=txtVar('TxErinnVorbei'); if(fSetzKalWert($v,'TxErinnVorbei',"'")) $bNeu=true;
 $v=(int)txtVar('ErinnOkMail'); if(fSetzKalWert(($v?true:false),'ErinnOkMail','')) $bNeu=true;
 $v=txtVar('TxErinnOkBtr'); if(fSetzKalWert($v,'TxErinnOkBtr','"')) $bNeu=true;
 $v=(int)txtVar('ErinnNDetail'); if(fSetzKalWert(($v?true:false),'ErinnNDetail','')) $bNeu=true;
 $v=(int)txtVar('ErinnMitMemo'); if(fSetzKalWert(($v?true:false),'ErinnMitMemo','')) $bNeu=true;
 $v=(int)txtVar('NutzerErinnFeld'); if(fSetzKalWert($v,'NutzerErinnFeld','')) $bNeu=true;
 $v=txtVar('ErinnLink'); if(fSetzKalWert($v,'ErinnLink',"'")) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxErinnOkTxt')))); if(fSetzKalWert($v,'TxErinnOkTxt',"'")) $bNeu=true;
 $v=(int)txtVar('FreischaltNeuMail'); if(fSetzKalWert(($v?true:false),'FreischaltNeuMail','')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxErinnUnmoegl')))); if(fSetzKalWert($v,'TxErinnUnmoegl',"'")) $bNeu=true;
 $v=txtVar('FreischaltWin'); if(fSetzKalWert($v,'FreischaltWin',"'")) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxErinnUnbekannt')))); if(fSetzKalWert($v,'TxErinnUnbekannt',"'")) $bNeu=true;
 $v=txtVar('TxErinnUnbkBtr'); if(fSetzKalWert($v,'TxErinnUnbkBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxErinnUnbkTxt')))); if(fSetzKalWert($v,'TxErinnUnbkTxt',"'")) $bNeu=true;
 $v=txtVar('TxErinnSendBtr'); if(fSetzKalWert($v,'TxErinnSendBtr','"')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxErinnSendTxt')))); if(fSetzKalWert($v,'TxErinnSendTxt',"'")) $bNeu=true;
 $v=(int)txtVar('MailPopup'); if(fSetzKalWert(($v?true:false),'MailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('TxNeUebersicht'); if(fSetzKalWert($v,'TxNeUebersicht',"'")) $bNeu=true;
 $v=txtVar('TxNeUnveraendert'); if(fSetzKalWert($v,'TxNeUnveraendert',"'")) $bNeu=true;
 $v=txtVar('TxNeKeineErinn'); if(fSetzKalWert($v,'TxNeKeineErinn',"'")) $bNeu=true;
 $v=txtVar('TxNeLoeschen'); if(fSetzKalWert($v,'TxNeLoeschen',"'")) $bNeu=true;
 $v=txtVar('TxNeGeloescht'); if(fSetzKalWert($v,'TxNeGeloescht',"'")) $bNeu=true;
 $v=txtVar('ErinnernDSE1'); if(fSetzKalWert(($v?true:false),'ErinnernDSE1','')) $bNeu=true;
 $v=txtVar('ErinnernDSE2'); if(fSetzKalWert(($v?true:false),'ErinnernDSE2','')) $bNeu=true;
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

<form action="konfErinner.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Erinnerungsservice ist aktiv, wenn in der Terminliste eine zusätzliche Spalte oder in der Detailanzeige eine zusätzliche Zeile mit einem <img src="<?php echo $sHttp?>grafik/iconErinnern.gif" width="16" height="16" border="0" align="top" title="Erinnerung">-Klickschalter eingeblendet wird.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Terminliste</td>
 <td>zusätzlichen Schalter vor Spalte <select name="ListenErinn" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenErinn==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLErinn" value="1"<?php if($ksGastLErinn) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenErinTitel" value="<?php echo $ksListenErinTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Detailanzeige</td>
 <td>zusätzliche Servicezeile vor Zeile <select name="DetailErinn" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailErinn==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDErinn" value="1"<?php if($ksGastDErinn) echo ' checked="checked"'?> /> auch für unangemeldete Gäste<br />
 Zeilenbeschriftung <input type="text" name="TxErinnService" value="<?php echo $ksTxErinnService?>" size="25" style="width:180px;" /> <span class="admMini">Empfehlung: <i>Erinnerung anfordern</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn ein Benutzer eine Erinnerungsanforderung in das Formular einträgt, werden folgende Texte benutzt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungen</td>
 <td><input type="text" name="TxErinnMeld" value="<?php echo $ksTxErinnMeld?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Fordern Sie eine Erinnerung kurz vor Veranstaltungsbeginn an.</i></div>
 <input type="text" name="TxErinnErfo" value="<?php echo $ksTxErinnErfo?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Ihr Erinnerungswunsch wurde vorgemerkt.</i></div>
 <input type="text" name="TxErinnVorbei" value="<?php echo $ksTxErinnVorbei?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Es sind keine Erinnerungstermine in der Vergangenheit möglich.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigungs-<br />E-Mail</td>
 <td><div><input class="admCheck" type="checkbox" name="ErinnOkMail" value="1"<?php if($ksErinnOkMail) echo' checked="checked"'?> /> Bestätigung nach Erinnerungsanforderung versenden &nbsp; <span class="admMini">Empfehlung: <i>nicht</i> aktivieren</span></div>
 <input type="text" name="TxErinnOkBtr" value="<?php echo $ksTxErinnOkBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Terminerinnerung bei #A vorgemerkt</i></div>
 <textarea name="TxErinnOkTxt" cols="80" rows="8" style="height:9em"><?php echo str_replace('\n ',"\n",$ksTxErinnOkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Terminerinnerung angefordert. Diese wird am #T zu folgendem Termin erfolgen:<br />#D<br />Mit freundlichen Grüßen</i></div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Erinnerungen können nur von angemeldeten Benutzern oder von extra dafür freigeschalteten Gästen angefordert werden. Bei einem Erinnerungswunsch eines momentan nicht berechtigten Gastes werden folgende Texte verwendet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungswunsch</td>
 <td><div><input class="admRadio" type="radio" name="FreischaltNeuMail" value="0"<?php if(!$ksFreischaltNeuMail) echo' checked="checked"'?> /> nicht berechtigte Gäste können sich <i>nicht</i> selbst freischalten</div>
 <textarea name="TxErinnUnmoegl" cols="80" rows="2" style="height:3em"><?php echo str_replace('\n ',"\n",$ksTxErinnUnmoegl)?></textarea>
 <div class="admMini">Empfehlung: <i># ist für diesen Dienst nicht vorgesehen. Wenden Sie sich an den Webmaster.</i></div>
 <div style="margin-top:5px;"><input class="admRadio" type="radio" name="FreischaltNeuMail" value="1"<?php if($ksFreischaltNeuMail) echo' checked="checked"'?> /> aktuell nicht berechtigte Gäste können sich selbst freischalten</div>
 <textarea name="TxErinnUnbekannt" cols="80" rows="2" style="height:3em"><?php echo str_replace('\n ',"\n",$ksTxErinnUnbekannt)?></textarea>
 <div class="admMini">Empfehlung: <i># ist für diesen Dienst noch nicht freigeschaltet. Sie können jetzt die Freischaltung für # anfordern.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischalt-E-Mail<div class="admMini" style="margin-top:5px;color:#777;">nur bei aktivierter<br />Selbstfreischaltung</div></td>
 <td><input type="text" name="TxErinnUnbkBtr" value="<?php echo $ksTxErinnUnbkBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Terminerinnerungswunsch bei #A</i></div>
 <textarea name="TxErinnUnbkTxt" cols="80" rows="8" style="height:9em"><?php echo str_replace('\n ',"\n",$ksTxErinnUnbkTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Terminerinnerung angefordert.<br />Ihre E-Mail-Adresse ist für diesen Dienst noch nicht freigeschaltet. Sie können diese Freischaltung erreichen, indem Sie jetzt den Link<br />#L<br /> aufrufen.<br />Mit freundlichen Grüßen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td>Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($ksFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (kalSeite.htm)</option><option value="Popup<?php if($ksFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (kalPopup.htm)</option><option value="Freischalt<?php if($ksFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (kalFreischalt.htm)</option></select></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Erinnerungsformular wird genauso wie das Informationsformular und das Kontaktformular bzw. Benachrichtigungsformular normalerweise im selben Fenster wie die Terminübersicht präsentiert.
Abweichend davon kann das Formular in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="MailPopup" value=""<?php if(!$ksMailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="MailPopup" value="1"<?php if($ksMailPopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="ErinnNDetail" value="0"<?php if(!$ksErinnNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="ErinnNDetail" value="1"<?php if($ksErinnNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer
 <div><input type="checkbox" class="admRadio" name="ErinnMitMemo" value="1"<?php if($ksErinnMitMemo) echo ' checked="checked"'?> /> einschließlich Felder vom Typ <i>Memo</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerErinnFeld" style="width:140px;"><?php echo str_replace('"'.$ksNutzerErinnFeld.'"','"'.$ksNutzerErinnFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Terminstruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Benachrichtigung mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Benachrichtigungsformular im selben Fenster wie der Kalender aufgerufen wird,
wird als Verweisziel auf die Termindetails in der E-Mail das aufrufende Script angegeben.
Das kann das Kalenderscript <i>kalender.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Benachrichtigungsformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Termindetails in der E-Mail normalerweise das Kalenderscript <i>kalender.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die die Termindetails in der E-Mail <i>Terminerinnerung</i> angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td><input type="text" name="ErinnLink" value="<?php echo $ksErinnLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Erinnerungs-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="ErinnernDSE1" value="1"<?php if($ksErinnernDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="ErinnernDSE2" value="1"<?php if($ksErinnernDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
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

<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn der vereinbarte Erinnerungszeitpunkt eintritt, wird an den Besucher folgende E-Mail-Nachricht versandt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungs-E-Mail</td>
 <td><input type="text" name="TxErinnSendBtr" value="<?php echo $ksTxErinnSendBtr?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Re: Terminerinnerung von #A</i></div>
 <textarea name="TxErinnSendTxt" cols="80" rows="8" style="height:9em"><?php echo str_replace('\n ',"\n",$ksTxErinnSendTxt)?></textarea>
 <div class="admMini">Empfehlung:<br /><i>Sehr geehrte Damen und Herren,<br />Sie haben unter #A eine Terminerinnerung zu folgendem Termin angefordert.<br />#D<br />Mit freundlichen Grüßen</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Im Benutzerzentrum kann es eine Seite zum Verwalten der eigenen Erinnerungswünsche geben. Auf dieser werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungs-<br>verwaltung</td>
 <td><input type="text" name="TxNeUebersicht" value="<?php echo $ksTxNeUebersicht?>" size="40" style="width:50%" />
 <div class="admMini">Empfehlung: <i>Erinnerungsübersicht</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungen der<br>Erinnerungsseite</td>
 <td><input type="text" name="TxNeUnveraendert" value="<?php echo $ksTxNeUnveraendert?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Die Erinnerungen bleiben unverändert.</i></div>
 <input type="text" name="TxNeKeineErinn" value="<?php echo $ksTxNeKeineErinn?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Sie haben derzeit keine aktiven Erinnerungswünsche.</i></div>
 <input type="text" name="TxNeLoeschen" value="<?php echo $ksTxNeLoeschen?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Wollen Sie die #N markierten Erinnerungen wirklich löschen?</i></div>
 <input type="text" name="TxNeGeloescht" value="<?php echo $ksTxNeGeloescht?>" size="40" style="width:100%" />
 <div class="admMini">Empfehlung: <i>#N Erinnerungen wurden gelöscht.</i></div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>