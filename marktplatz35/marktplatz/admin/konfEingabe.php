<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Eingabeformular kofigurieren','<script type="text/javascript">
 function colWin(sURL){cWin=window.open(sURL,"color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes"); cWin.focus();}
</script>','KEg');

$aStru=array(); $aET[1]='??'; $mpDauer='??';
if(!MP_SQL){//Text
 if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)){
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); $aET=explode(';',$aStru[15]); $mpDauer=$aET[1];
 }else $Meld='Bitte zuerst die Pfade im Setup einstellen!';
}elseif($DbO){//SQL
 if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
  $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
  if($i==1){$aStru=explode("\n",$a[1]); $aET=explode(';',$aStru[15]); $mpDauer=$aET[1];}
 }else $Meld=MP_TxSqlFrage;
}else $Meld=MP_TxSqlVrbdg;

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 if(!$Meld){$Meld='Kontrollieren oder ändern Sie die Einstellungen für das Eingabeformular. <a href="'.AM_Hilfe.'LiesMich.htm#2.8" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';}
 $mpDirekteintrag=MP_Direkteintrag;
 $mpTxEingabeMeld=MP_TxEingabeMeld; $mpTxEingabeFehl=MP_TxEingabeFehl;
 $mpTxEingabeErfo=MP_TxEingabeErfo; $mpTxVormerkErfo=MP_TxVormerkErfo;
 $mpTxNummerFehlt=MP_TxNummerFehlt; $mpTxNummerUnbek=MP_TxNummerUnbek;
 $mpTxNummerPassw=MP_TxNummerPassw; $mpTxNummerFremd=MP_TxNummerFremd;
 $mpDirektaendern=MP_Direktaendern; $mpTxAendereMeld=MP_TxAendereMeld;
 $mpHalteAltesNochTage=MP_HalteAltesNochTage; $mpBearbAltesNochTage=MP_BearbAltesNochTage;
 $mpTxKeineAenderung=MP_TxKeineAenderung; $mpTxAendereErfo=MP_TxAendereErfo; $mpTxAendereVmk=MP_TxAendereVmk;
 $mpTxLoeschFrage=MP_TxLoeschFrage; $mpTxLoeschErfol=MP_TxLoeschErfol;
 $mpTxKopiereMeld=MP_TxKopiereMeld; $mpTxKopiereErfo=MP_TxKopiereErfo;
 $mpNEingabeLogin=MP_NEingabeLogin; $mpNAendernFremde=MP_NAendernFremde; $mpNEingabeAnders=MP_NEingabeAnders;
 $mpNutzerEMail=MP_NutzerEMail; $mpFormatCode=MP_FormatCode; $mpBildMaxKByte=MP_BildMaxKByte; $mpDateiMaxKByte=MP_DateiMaxKByte;
 $mpBildBreit=MP_BildBreit; $mpBildHoch=MP_BildHoch; $mpVorschauBreit=MP_VorschauBreit; $mpVorschauHoch=MP_VorschauHoch; $mpVorschauRahmen=MP_VorschauRahmen;
 $mpThumbBreit=MP_ThumbBreit; $mpThumbHoch=MP_ThumbHoch; $mpBildResize=MP_BildResize;
 $mpEintragszeitNeu=MP_EintragszeitNeu; $mpAblaufdatumFest=MP_AblaufdatumFest;
 $mpEintragAdminInfo=MP_EintragAdminInfo; $mpTxEintragAdminBtr=MP_TxEintragAdminBtr; $mpTxAendernAdminBtr=MP_TxAendernAdminBtr; $mpTxEintragAdminTxt=MP_TxEintragAdminTxt;
 $mpEintragMail=MP_EintragMail; $mpAendernMail=MP_AendernMail; $mpTxEintragBtr=MP_TxEintragBtr; $mpTxAendernBtr=MP_TxAendernBtr; $mpTxEintragTxt=MP_TxEintragTxt;
 $mpFreischaltMail=MP_FreischaltMail; $mpTxFreischaltBtr=MP_TxFreischaltBtr; $mpTxFreischaltTxt=MP_TxFreischaltTxt;
 $mpMailListeAdr=MP_MailListeAdr; $mpMailListeEintrag=MP_MailListeEintrag; $mpMailListeAdmEint=MP_MailListeAdmEint;
 $mpMailListeFreischalt=MP_MailListeFreischalt; $mpMailListeAendern=MP_MailListeAendern; $mpMailListeAdmAend=MP_MailListeAdmAend;
 $mpTxMailListeBtr=MP_TxMailListeBtr; $mpTxMailListeTxt=MP_TxMailListeTxt; $mpMailListeNDetail=MP_MailListeNDetail;
 $mpTxMailLstBtrAend=MP_TxMailLstBtrAend; $mpNutzerMailListeFeld=MP_NutzerMailListeFeld;
 $mpEintragDSE1=MP_EintragDSE1; $mpEintragDSE2=MP_EintragDSE2;
 $mpCaptcha=MP_Captcha; $mpCaptchaHgFarb=MP_CaptchaHgFarb; $mpCaptchaTxFarb=MP_CaptchaTxFarb; $mpAendernCaptcha=MP_AendernCaptcha;
 $mpTxAgfFeld=MP_TxAgfFeld; $mpTxAgfText=MP_TxAgfText; $mpAgfLink=MP_AgfLink; $mpAendernMitLoeschen=MP_AendernMitLoeschen; $mpAendernOnOff=MP_AendernOnOff;
 $mpAgbPopup=MP_AgbPopup; $mpAgbBreit=MP_AgbBreit; $mpAgbHoch=MP_AgbHoch; $mpAgbZiel=MP_AgbZiel;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $s=(int)txtVar('Direkteintrag'); if(fSetzMPWert(($s?true:false),'Direkteintrag','')) $bNeu=true;
 $s=(int)txtVar('Direktaendern'); if(fSetzMPWert($s,'Direktaendern','')) $bNeu=true;
 $s=txtVar('TxEingabeMeld'); if(fSetzMPWert($s,'TxEingabeMeld','"')) $bNeu=true;
 $s=txtVar('TxEingabeFehl'); if(fSetzMPWert($s,'TxEingabeFehl','"')) $bNeu=true;
 $s=txtVar('TxEingabeErfo'); if(fSetzMPWert($s,'TxEingabeErfo','"')) $bNeu=true;
 $s=txtVar('TxVormerkErfo'); if(fSetzMPWert($s,'TxVormerkErfo','"')) $bNeu=true;
 $s=txtVar('TxNummerFehlt'); if(fSetzMPWert($s,'TxNummerFehlt','"')) $bNeu=true;
 $s=txtVar('TxNummerUnbek'); if(fSetzMPWert($s,'TxNummerUnbek','"')) $bNeu=true;
 $s=txtVar('TxNummerPassw'); if(fSetzMPWert($s,'TxNummerPassw','"')) $bNeu=true;
 $s=txtVar('TxNummerFremd'); if(fSetzMPWert($s,'TxNummerFremd','"')) $bNeu=true;
 $s=txtVar('TxAendereMeld'); if(fSetzMPWert($s,'TxAendereMeld','"')) $bNeu=true;
 $s=txtVar('TxKeineAenderung'); if(fSetzMPWert($s,'TxKeineAenderung','"')) $bNeu=true;
 $s=txtVar('TxAendereErfo'); if(fSetzMPWert($s,'TxAendereErfo','"')) $bNeu=true;
 $s=txtVar('TxAendereVmk');  if(fSetzMPWert($s,'TxAendereVmk','"')) $bNeu=true;
 $s=txtVar('TxLoeschFrage'); if(fSetzMPWert($s,'TxLoeschFrage','"')) $bNeu=true;
 $s=txtVar('TxLoeschErfol'); if(fSetzMPWert($s,'TxLoeschErfol','"')) $bNeu=true;
 $s=txtVar('TxKopiereMeld'); if(fSetzMPWert($s,'TxKopiereMeld','"')) $bNeu=true;
 $s=txtVar('TxKopiereErfo'); if(fSetzMPWert($s,'TxKopiereErfo','"')) $bNeu=true;
 $s=(int)txtVar('BearbAltesNochTage'); if(fSetzMPWert($s,'BearbAltesNochTage','')) $bNeu=true;
 $s=(int)txtVar('HalteAltesNochTage'); if(fSetzMPWert($s,'HalteAltesNochTage','')) $bNeu=true;
 $s=(int)txtVar('NEingabeLogin'); if(fSetzMPWert(($s?true:false),'NEingabeLogin','')) $bNeu=true;
 $s=(int)txtVar('NAendernFremde'); if(fSetzMPWert(($s?true:false),'NAendernFremde','')) $bNeu=true;
 $s=(int)txtVar('NEingabeAnders'); if(fSetzMPWert(($s?true:false),'NEingabeAnders','')) $bNeu=true;
 $s=(int)txtVar('NutzerEMail'); if(fSetzMPWert(($s?true:false),'NutzerEMail','')) $bNeu=true;
 $s=(int)txtVar('FormatCode'); if(fSetzMPWert(($s?true:false),'FormatCode','')) $bNeu=true;
 $s=(int)txtVar('BildMaxKByte'); if(fSetzMPWert($s,'BildMaxKByte','')) $bNeu=true;
 $s=(int)txtVar('DateiMaxKByte'); if(fSetzMPWert($s,'DateiMaxKByte','')) $bNeu=true;
 $s=(int)txtVar('BildBreit'); if(fSetzMPWert($s,'BildBreit','')) $bNeu=true;
 $s=(int)txtVar('BildHoch'); if(fSetzMPWert($s,'BildHoch','')) $bNeu=true;
 $s=(int)txtVar('VorschauBreit'); if(fSetzMPWert($s,'VorschauBreit','')) $bNeu=true;
 $s=(int)txtVar('VorschauHoch'); if(fSetzMPWert($s,'VorschauHoch','')) $bNeu=true;
 $s=(int)txtVar('VorschauRahmen'); if(fSetzMPWert(($s?true:false),'VorschauRahmen','')) $bNeu=true;
 $s=(int)txtVar('ThumbBreit'); if(fSetzMPWert($s,'ThumbBreit','')) $bNeu=true;
 $s=(int)txtVar('ThumbHoch'); if(fSetzMPWert($s,'ThumbHoch','')) $bNeu=true;
 $s=(int)txtVar('BildResize'); if(fSetzMPWert(($s?true:false),'BildResize','')) $bNeu=true;
 $s=(int)txtVar('EintragszeitNeu'); if(fSetzMPWert(($s?true:false),'EintragszeitNeu','')) $bNeu=true;
 $s=(int)txtVar('AblaufdatumFest'); if(fSetzMPWert(($s?true:false),'AblaufdatumFest','')) $bNeu=true;
 $s=(int)txtVar('EintragAdminInfo'); if(fSetzMPWert(($s?true:false),'EintragAdminInfo','')) $bNeu=true;
 $s=txtVar('TxEintragAdminBtr'); if(fSetzMPWert($s,'TxEintragAdminBtr','"')) $bNeu=true;
 $s=txtVar('TxAendernAdminBtr'); if(fSetzMPWert($s,'TxAendernAdminBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxEintragAdminTxt')))); if(fSetzMPWert($s,'TxEintragAdminTxt',"'")) $bNeu=true;
 $s=(int)txtVar('EintragMail'); if(fSetzMPWert(($s?true:false),'EintragMail','')) $bNeu=true;
 $s=(int)txtVar('AendernMail'); if(fSetzMPWert(($s?true:false),'AendernMail','')) $bNeu=true;
 $s=txtVar('TxEintragBtr'); if(fSetzMPWert($s,'TxEintragBtr','"')) $bNeu=true;
 $s=txtVar('TxAendernBtr'); if(fSetzMPWert($s,'TxAendernBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxEintragTxt')))); if(fSetzMPWert($s,'TxEintragTxt',"'")) $bNeu=true;
 $s=(int)txtVar('FreischaltMail'); if(fSetzMPWert(($s?true:false),'FreischaltMail','')) $bNeu=true;
 $s=txtVar('TxFreischaltBtr'); if(fSetzMPWert($s,'TxFreischaltBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxFreischaltTxt')))); if(fSetzMPWert($s,'TxFreischaltTxt',"'")) $bNeu=true;
 $s=txtVar('MailListeAdr'); if(fSetzMPWert($s,'MailListeAdr','"')) $bNeu=true;
 $s=(int)txtVar('MailListeEintrag'); if(fSetzMPWert(($s?true:false),'MailListeEintrag','')) $bNeu=true;
 $s=(int)txtVar('MailListeAdmEint'); if(fSetzMPWert(($s?true:false),'MailListeAdmEint','')) $bNeu=true;
 $s=(int)txtVar('MailListeFreischalt'); if(fSetzMPWert(($s?true:false),'MailListeFreischalt','')) $bNeu=true;
 $s=(int)txtVar('MailListeAendern'); if(fSetzMPWert(($s?true:false),'MailListeAendern','')) $bNeu=true;
 $s=(int)txtVar('MailListeAdmAend'); if(fSetzMPWert(($s?true:false),'MailListeAdmAend','')) $bNeu=true;
 $s=(int)txtVar('MailListeNDetail'); if(fSetzMPWert(($s?true:false),'MailListeNDetail','')) $bNeu=true;
 $s=txtVar('TxMailListeBtr'); if(fSetzMPWert($s,'TxMailListeBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxMailListeTxt')))); if(fSetzMPWert($s,'TxMailListeTxt',"'")) $bNeu=true;
 $s=txtVar('TxMailLstBtrAend'); if(fSetzMPWert($s,'TxMailLstBtrAend','"')) $bNeu=true;
 $s=(int)txtVar('NutzerMailListeFeld'); if(fSetzMPWert($s,'NutzerMailListeFeld','')) $bNeu=true;
 $s=txtVar('EintragDSE1'); if(fSetzMPWert(($s?true:false),'EintragDSE1','')) $bNeu=true;
 $s=txtVar('EintragDSE2'); if(fSetzMPWert(($s?true:false),'EintragDSE2','')) $bNeu=true;
 $s=txtVar('Captcha'); if(fSetzMPWert(($s?true:false),'Captcha','')) $bNeu=true;
 $s=txtVar('CaptchaTxFarb'); if(fSetzMPWert($s,'CaptchaTxFarb',"'")) $bNeu=true;
 $s=txtVar('CaptchaHgFarb'); if(fSetzMPWert($s,'CaptchaHgFarb',"'")) $bNeu=true;
 $s=txtVar('AendernCaptcha'); if(fSetzMPWert(($s?true:false),'AendernCaptcha','')) $bNeu=true;
 $s=txtVar('TxAgfFeld'); if(fSetzMPWert($s,'TxAgfFeld','"')) $bNeu=true;
 $s=txtVar('TxAgfText'); if(fSetzMPWert($s,'TxAgfText','"')) $bNeu=true;
 $s=str_replace("'",'',txtVar('AgfLink')); if(fSetzMPWert($s,'AgfLink',"'")) $bNeu=true;
 $s=(int)txtVar('AendernMitLoeschen'); if(fSetzMPWert(($s?true:false),'AendernMitLoeschen','')) $bNeu=true;
 $s=(int)txtVar('AendernOnOff'); if(fSetzMPWert(($s?true:false),'AendernOnOff','')) $bNeu=true;
 $s=(int)txtVar('AgbPopup'); if(fSetzMPWert(($s?true:false),'AgbPopup','')) $bNeu=true;
 $s=min(max(txtVar('AgbBreit'),50),1999); if(fSetzMPWert($s,'AgbBreit','')) $bNeu=true;
 $s=min(max(txtVar('AgbHoch' ),50),1999); if(fSetzMPWert($s,'AgbHoch', '')) $bNeu=true;
 $s=str_replace("'",'',txtVar('AgbZiel')); if(fSetzMPWert($s,'AgbZiel',"'")) $bNeu=true;

 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Eingabeeinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Eingabeeinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
$aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNFz=count($aNF);
$sNOpt='<option value="0">--</option><option value="2">'.str_replace(';','`,',$aNF[2]).'</option>'; for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.str_replace(';','`,',$aNF[$j]).'</option>';
?>

<form name="farbform" action="konfEingabe.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Inserateeingaben durch Besucher können als Direkteintrag mit sofortiger Veröffentlichung oder als Inseratevormerkung mit Freigabe nach Überprüfung durch den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td>Eintragsart</td>
 <td><input type="radio" class="admRadio" name="Direkteintrag" value="1"<?php if($mpDirekteintrag) echo ' checked="checked"'?> /> Direkteintrag &nbsp;
 <input type="radio" class="admRadio" name="Direkteintrag" value="0"<?php if(!$mpDirekteintrag) echo ' checked="checked"'?> /> Inseratevormerkung &nbsp; &nbsp;
 <span class="admMini">Empfehlung: <i>Inseratevormerkung!</i></span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsart</td>
 <td><input type="radio" class="admRadio" name="Direktaendern" value="1"<?php if($mpDirektaendern=='1') echo ' checked="checked"'?> /> Direktändern<br />
 <input type="radio" class="admRadio" name="Direktaendern" value="0"<?php if(!$mpDirektaendern) echo ' checked="checked"'?> /> Änderungsvorschlag mit Ausblenden des Originals<br />
 <input type="radio" class="admRadio" name="Direktaendern" value="2"<?php if($mpDirektaendern=='2') echo ' checked="checked"'?> /> Änderungsvorschlag mit Sichtbarlassen des Originals</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Eingabeformular für Inserate wird Besuchern folgende Aufforderungsmeldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderung Eingabe</td>
 <td><input type="text" name="TxEingabeMeld" value="<?php echo $mpTxEingabeMeld?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Tragen Sie jetzt Ihr neues Inserat ein!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefehler</td>
 <td><input type="text" name="TxEingabeFehl" value="<?php echo $mpTxEingabeFehl?>" style="width:99%" /><div class="admMini">(Wird auch im Informationsformular und Kontaktformular verwendet.)<br />Empfehlung: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Meldung Direkteintrag<div style="margin-top:20px;">Meldung Vormerkung</div></td>
 <td><input type="text" name="TxEingabeErfo" value="<?php echo $mpTxEingabeErfo?>" style="width:99%;<?php if(!$mpDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Das Inserat wurde eingetragen und veröffentlicht!</i></div>
     <input type="text" name="TxVormerkErfo" value="<?php echo $mpTxVormerkErfo?>" style="width:99%;<?php if($mpDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Das Inserat wurde vorgemerkt und der Webmaster informiert!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderungen Ändern</td>
 <td>
 <input type="text" name="TxNummerFehlt" value="<?php echo $mpTxNummerFehlt?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Bitte geben Sie die laufende Inseratenummer an!</i></div>
 <input type="text" name="TxNummerUnbek" value="<?php echo $mpTxNummerUnbek?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Datensatz nicht gefunden! Bitte geben Sie eine gültige Nummer an!</i></div>
 <input type="text" name="TxNummerPassw" value="<?php echo $mpTxNummerPassw?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Bitte geben Sie das korrekte Passwort zum Inserat an!</i></div>
 <input type="text" name="TxNummerFremd" value="<?php echo $mpTxNummerFremd?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Dieses Inserat gehört nicht Ihnen! Bitte geben Sie eine gültige Nummer an!</i></div>
 <input type="text" name="TxAendereMeld" value="<?php echo $mpTxAendereMeld?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Ändern Sie jetzt dieses Inserat ab!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigung Ändern</td>
 <td><input type="text" name="TxKeineAenderung" value="<?php echo $mpTxKeineAenderung?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Die Daten bleiben unverändert!</i></div>
 <input type="text" name="TxAendereErfo" value="<?php echo $mpTxAendereErfo?>" style="width:99%;<?php if(!$mpDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Die geänderten Inseratedaten wurden eingetragen!</i></div>
 <input type="text" name="TxAendereVmk" value="<?php echo $mpTxAendereVmk?>" style="width:99%;<?php if($mpDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Die Änderungen wurden vorgemerkt und der Webmaster informiert!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inserat löschen</td>
 <td><input type="text" name="TxLoeschFrage" value="<?php echo $mpTxLoeschFrage?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Wollen Sie das markierten Inserat wirklich löschen?</i></div>
 <input type="text" name="TxLoeschErfol" value="<?php echo $mpTxLoeschErfol?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Das markierte Inserat wurden gelöscht!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inseratekopie<div class="admMini">(Administrator/Autoren)</div></td>
 <td><input type="text" name="TxKopiereMeld" value="<?php echo $mpTxKopiereMeld?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Kopieren Sie dieses Inserat als ein neues Inserat!</i></div>
 <input type="text" name="TxKopiereErfo" value="<?php echo $mpTxKopiereErfo?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Die Inseratekopie wurde eingetragen und veröffentlicht!</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Anzeigedauer für Inserate in diesem Segment ist durch die Einstellung unter <i>Segmenteigenschaften</i> standardmäßig vorgegeben und kann nur dort geändert werden.
Alternativ dazu kann angemeldeten Benutzern über das Feld namens <i>??</i> eine abweichende Inseratedauer zugeordnet werden, die die allgemeine Segmentanzeigedauer jedoch nicht überschreiten kann.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Inseratedauer</td>
 <td><input type="text" name="xDauer" value="<?php echo $mpDauer?>" style="width:40px;background-color:#EEEEFF;" readonly="readonly" /> max. Tage im Segment <i><?php echo $sSegNam;?></i></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Inserate werden am Ablauftage ausgeblendet, jedoch nicht sofort gelöscht.
Sie können danach noch einige Tage nachträglich bearbeitet (verändert oder von Hand gelöscht) werden,
ehe das endgültige Löschen automatisch erfolgt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bearbeitungsdauer</td>
 <td><input type="text" name="BearbAltesNochTage" value="<?php echo $mpBearbAltesNochTage?>" style="width:40px;" /> Tage nachträglich nach Ablauf noch möglich</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherdauer</td>
 <td><input type="text" name="HalteAltesNochTage" value="<?php echo $mpHalteAltesNochTage?>" style="width:40px;" /> Tage nachträglich nach Ablauf automatisch löschen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Eingabeformular für Inserate im Besucherbereich kann bezüglich der Eingabeberechtigungen konfiguriert werden.
Welche Eingabe- und Änderungsmöglichkeiten sollen für unangemeldete Gäste bzw. für angemeldete Benutzer vorhanden sein,
sofern die Benutzerverwaltung durch ein Feld vom Typ <i>Benutzer</i> in der Inseratestruktur eingeschaltet ist?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabeberechtigungen</td>
 <td><div><input type="checkbox" class="admCheck" name="NEingabeLogin" value="1"<?php if($mpNEingabeLogin) echo ' checked="checked"'?> /> <i>nur</i> angemeldete Benutzer sollen Eingaben und Änderungen ausführen können <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 <div><input type="checkbox" class="admCheck" name="NAendernFremde" value="1"<?php if($mpNAendernFremde) echo ' checked="checked"'?> /> angemeldete Benutzer sollen auch fremde Inserate ändern dürfen <span class="admMini">(Empfehlung: <i>nein</i>)</span></div></td>
</tr>
<tr class="admTabl">
 <td>Formularvarianten</td>
 <td><input type="checkbox" class="admCheck" name="NEingabeAnders" value="1"<?php if($mpNEingabeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Eingabefelder sehen als Gäste
 <div class="admMini">(Die konkreten Felder stellen Sie für jedes Marktsegment gesondert unter <i>Segmenteigenschaften</i> ein.)</div></td>
</tr>
<tr class="admTabl">
 <td>Deaktivieren<br>von Inseraten</td>
 <td><input type="checkbox" class="admCheck" name="AendernOnOff" value="1"<?php if($mpAendernOnOff) echo ' checked="checked"'?> /> Unter den Eingabeformularen können 2 Radioschalter zum Online-/Offlineschalten von Iseraten angeboten werden. &nbsp; <span class="admMini">keine Empfehlung</span></td>
</tr>
<tr class="admTabl">
 <td>Löschmöglichkeit</td>
 <td><input type="checkbox" class="admCheck" name="AendernMitLoeschen" value="1"<?php if($mpAendernMitLoeschen) echo ' checked="checked"'?> /> Unter dem Änderungsformular kann ein Kontrollkästchen zum Löschen des Inserates angeboten werden. &nbsp; <span class="admMini">Empfehlung: anbieten</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Als zusätzliche letzte Zeile im Eingabeformular kann eine Zeile eingeblendet werden,
die ein Kontrollkästchen mit Ausfüllzwang enthält. Dieses kann beispielsweise für eine Einverständniserklärung mit <i>Allgemeinen Geschäftsbedingungen (AGB)</i> benutzt werden.
Alternativ hierzu können Sie ein solches Kästchen bei den Benutzereinstellungen/Benutzerlogin plazieren.</td></tr>
<tr class="admTabl">
 <td>Bestätigungsfeld</td>
 <td><input class="admEing" type="text" name="TxAgfFeld" value="<?php echo $mpTxAgfFeld?>" maxlength="25" style="width:100px;" /> <span class="admMini">Empfehlung: <i>AGB</i> oder <i>Bestätigung</i> oder leer lassen bei Nichtverwendung</span></td>
</tr>
<tr class="admTabl">
 <td>Beschreibung<br>(hinter dem Kästchen)</td>
 <td><input class="admEing" type="text" name="TxAgfText" value="<?php echo $mpTxAgfText?>" maxlength="250" /> <span class="admMini">Muster: <i>Ich akzeptiere die [AGB].</i></span>
 <div class="admMini"><u>Hinweis</u>: Der Text muß [ ] enthalten für den Teil des Textes, der auf die AGB verlinkt.</div></td>
</tr>
<tr class="admTabl">
 <td>Verweisadresse</td>
 <td><input class="admEing" type="text" name="AgfLink" value="<?php echo $mpAgfLink?>" /> <span class="admMini">funktionierende Verweisadresse auf die AGB-Seite, meist einschließlich <i>http://</i></span></td>
</tr>
<tr class="admTabl">
 <td>Verweis als Popup-Fenster</td>
 <td><input type="checkbox" class="admCheck" name="AgbPopup" value="1"<?php if($mpAgbPopup) echo' checked="checked"'?> /> &nbsp;
 <input class="admEing" type="text" name="AgbBreit" value="<?php echo $mpAgbBreit?>" maxlength="4" style="width:40px;" /> px Fensterbreite, &nbsp;
 <input class="admEing" type="text" name="AgbHoch" value="<?php echo $mpAgbHoch?>" maxlength="4" style="width:40px;" /> px Fensterhöhe, &nbsp;
 eventuell Zielfenster/Target <input class="admEing" type="text" name="AgbZiel" value="<?php echo $mpAgbZiel?>" maxlength="32" style="width:100px;" /></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls ein Feld vom Typ <i>E-Mail</i> bzw. vom Typ <i>Kontakt</i> in der Inseratestruktur vorhanden ist
und die Benutzerverwaltung aktiv ist, kann die E-Mail-Adresse eines angemeldeten Benutzers im leeren Eingabeformular voreingetragen werden.</td></tr>
<tr class="admTabl">
 <td>E-Mail-Vorbelegung</td>
 <td><input type="checkbox" class="admRadio" name="NutzerEMail" value="1"<?php if($mpNutzerEMail) echo ' checked="checked"'?> /> <i>E-Mail-</i> oder <i>Kontakt-</i>Feld mit Benutzer-E-Mail vorbelegen &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Memofelder können durch sogenannten BB-Code (fett, kursiv...) formatiert werden.
 Soll über den Eingabefeldern eine Symbolleiste mit Formatierungselementen eingeblendet werden?</td></tr>
<tr class="admTabl">
 <td>Formatierung</td>
 <td><input type="checkbox" class="admRadio" name="FormatCode" value="1"<?php if($mpFormatCode) echo ' checked="checked"'?> /> Formatierungsmöglichkeit anbieten &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls in der Inseratestruktur Bilder oder Dateien enthalten sind können deren Größen begrenzt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilder hochladen</td>
 <td>
 <table cellpadding="0" cellspacing="0" border="0">
  <tr><td>Bild-Hochladen</td><td style="white-space:nowrap;">&nbsp;<input type="text" name="BildMaxKByte" value="<?php echo $mpBildMaxKByte?>" style="width:30px;" /> KByte maximal erlauben&nbsp;</td><td>(<span class="admMini">Empfehlung: max. 4000 KByte, 0 für unbegrenzt</i></span>)</td></tr>
  <tr><td>große Bilder</td><td style="white-space:nowrap;">&nbsp;<input type="text" name="BildBreit" value="<?php echo $mpBildBreit?>" style="width:30px;" /> x <input type="text" name="BildHoch" value="<?php echo $mpBildHoch?>" style="width:30px;" /> Pixel (verkleinerte Breite x Höhe)&nbsp;</td><td>(<span class="admMini">Empfehlung: 500x400</i></span>)</td></tr>
  <tr><td>verkleinern</td><td colspan="2">&nbsp;<input type="checkbox" class="admCheck" name="BildResize" value="1"<?php if($mpBildResize) echo ' checked="checked"';?> /> Bild bereits schon <i>vor</i> dem Upload per Java-Script im Browser verkleinern</td></tr>
  <tr><td>Vorschaubilder</td><td style="white-space:nowrap;">&nbsp;<input type="text" name="VorschauBreit" value="<?php echo $mpVorschauBreit?>" style="width:30px;" /> x <input type="text" name="VorschauHoch" value="<?php echo $mpVorschauHoch?>" style="width:30px;" /> Pixel (verkleinerte Breite x Höhe)&nbsp;</td><td>(<span class="admMini">Empfehlung: 100x130</i></span>)</td></tr>
  <tr><td>Vorschaubilder</td><td colspan="2">&nbsp;<input type="checkbox" class="admCheck" name="VorschauRahmen" value="1"<?php if($mpVorschauRahmen) echo ' checked="checked"';?> /> einrahmen mit Rahmen in exakt dieser Größe</td></tr>
  <tr><td>FormularIcons</td><td style="white-space:nowrap;">&nbsp;<input type="text" name="ThumbBreit" value="<?php echo $mpThumbBreit?>" style="width:30px;" /> x <input type="text" name="ThumbHoch" value="<?php echo $mpThumbHoch?>" style="width:30px;" /> px (im Eingabeformular Breite x Höhe)&nbsp;</td><td>(<span class="admMini">Empfehlung: 90x64</i></span>)</td></tr>
 </table>
 </td>
</tr>
<tr class="admTabl">
 <td>Dateien hochladen</td>
 <td><span style="width:164px;">Dateianhänge bis höchstens</span> <input type="text" name="DateiMaxKByte" value="<?php echo $mpDateiMaxKByte?>" style="width:30px;" /> KByte erlauben  &nbsp; (<span class="admMini">Empfehlung: höchstens 500 KByte</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihre Inseratestruktur ein Feld vom Typ <i>Eintragszeit</i> enthält wird dieses automatisch beim Inserateeintrag mit dem dortigen Datum/Uhrzeit gefüllt.
Bei Inserateänderungen bleibt es normalerweise im Originalzustand. Abweichend davon kann das Feld bei Inserateänderungen mit dem Zeitstempel der Inserateänderung aktualisiert werden.</td></tr>
<tr class="admTabl">
 <td>Eintrags- und Änderungszeit</td>
 <td><input type="radio" class="admRadio" name="EintragszeitNeu" value="0"<?php if(!$mpEintragszeitNeu) echo ' checked="checked"'?> /> Original-Eintragszeit beibehalten &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="EintragszeitNeu" value="1"<?php if($mpEintragszeitNeu) echo ' checked="checked"'?> /> Änderungszeit eintragen &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls das Feld vom Typ <i>Ablaufdatum</i> im Eingabeformular bearbeitet werden kann
könnte es im Änderungsformular jederzeit nachbearbeitet werden und mit einem neuen Ablaufdatum versehen werden.
Abweichend davon kann das Feld bei Inserateänderungen gegen Veränderungen gesperrt werden.</td></tr>
<tr class="admTabl">
 <td>Ablaufdatum</td>
 <td><input type="radio" class="admRadio" name="AblaufdatumFest" value="0"<?php if(!$mpAblaufdatumFest) echo ' checked="checked"'?> /> Ablaufdatum kann geändert werden &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="AblaufdatumFest" value="1"<?php if($mpAblaufdatumFest) echo ' checked="checked"'?> /> Ablaufdatum ist gesperrt &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Inserateeintragungen durch Benutzer können E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an die in Feldern vom Typ <i>Kontakt</i> bzw. <i>E-Mail</i> eingetragenen Adressen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Administrator<div style="margin-top:5px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td style="padding-top:5px;"><div><input class="admCheck" type="checkbox" name="EintragAdminInfo" value="1"<?php if($mpEintragAdminInfo) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <div><input type="text" name="TxEintragAdminBtr" value="<?php echo $mpTxEintragAdminBtr?>" style="width:99%" /><div class="admMini">Muster: <i>neuer Inserateeintrag Nr. #N bei #S</i></div>
 <div><input type="text" name="TxAendernAdminBtr" value="<?php echo $mpTxAendernAdminBtr?>" style="width:99%" /><div class="admMini">Muster: <i>geaenderter Inserateeintrag Nr. #N bei #S</i></div>
 <textarea name="TxEintragAdminTxt" style="width:99%;height:5em;"><?php echo str_replace('\n ',"\n",$mpTxEintragAdminTxt)?></textarea></div><div class="admMini">Muster: <i>Im Marktplatz im Segment #S wurde folgender Eintrag #N vorgenommen: #D</i></div><div class="admMini">Hinweis: In einem Freischaltlink wie <i>https://ihr-web.de/markt/freigabe.php#C</i> <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.8C" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" style="vertical-align:-3px" border="0" title="Hilfe zum Freigabecode"></a> ergänzt #C den Freischaltcode.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Benutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td style="padding-top:5px;"><div><input class="admCheck" type="checkbox" name="EintragMail" value="1"<?php if($mpEintragMail) echo' checked="checked"'?> /> beim Eintragen &nbsp; &nbsp; <input class="admCheck" type="checkbox" name="AendernMail" value="1"<?php if($mpAendernMail) echo' checked="checked"'?> />  beim Ändern &nbsp; <span class="admMini">(Empfehlung: <i>nicht versenden</i>)</i></span></div>
 <div><input type="text" name="TxEintragBtr" value="<?php echo $mpTxEintragBtr?>" style="width:99%" /><div class="admMini">Muster: <i>Re: Ihr Inserateeintrag bei #A</i></div>
 <div><input type="text" name="TxAendernBtr" value="<?php echo $mpTxAendernBtr?>" style="width:99%" /><div class="admMini">Muster: <i>Re: Ihr geänderter Inserateeintrag bei #A</i></div>
 <textarea name="TxEintragTxt" style="width:99%;height:7em;"><?php echo str_replace('\n ',"\n",$mpTxEintragTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Eintrag Nr. #N im Segment #S im Marktplatz bei #A wurde mit folgenden Daten entgegengenommen: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail bei Freischaltung<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td style="padding-top:5px;"><div><input class="admCheck" type="checkbox" name="FreischaltMail" value="1"<?php if($mpFreischaltMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <div><input type="text" name="TxFreischaltBtr" value="<?php echo $mpTxFreischaltBtr?>" style="width:99%" /><div class="admMini">Muster: <i>Re: Inserateeintrag freigeschaltet bei #A</i></div>
 <textarea name="TxFreischaltTxt" style="width:99%;height:7em;"><?php echo str_replace('\n ',"\n",$mpTxFreischaltTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Eintrag Nr. #N im Segment #S im Marktplatz bei #A wurde mit folgenden Daten veröffentlicht: #D</i></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Inserateeintragungen oder Änderungen kann eine E-Mail an eine &quot;<i>Mailingliste</i>&quot; versandt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Mailingliste<div style="margin-top:6px;">Auslöseereignis</div><div style="margin-top:24px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td style="padding-top:5px;"><div><input type="text" name="MailListeAdr" value="<?php echo $mpMailListeAdr?>" style="width:322px;" /> <span class="admMini">E-Mail-Adresse der Liste</span></div>
 <div>Besucher: <input class="admCheck" type="checkbox" name="MailListeEintrag" value="1"<?php if($mpMailListeEintrag) echo' checked="checked"'?> /> beim Inserateeintragen &nbsp; <input class="admCheck" type="checkbox" name="MailListeAendern" value="1"<?php if($mpMailListeAendern) echo' checked="checked"'?> /> beim Inserateändern</div>
 <div>Administ.: <input class="admCheck" type="checkbox" name="MailListeAdmEint" value="1"<?php if($mpMailListeAdmEint) echo' checked="checked"'?> /> beim Inserateeintragen &nbsp; <input class="admCheck" type="checkbox" name="MailListeAdmAend" value="1"<?php if($mpMailListeAdmAend) echo' checked="checked"'?> /> beim Inserateändern &nbsp; <input class="admCheck" type="checkbox" name="MailListeFreischalt" value="1"<?php if($mpMailListeFreischalt) echo' checked="checked"'?> /> beim Inseratefreischalten</div>
 <div><input type="text" name="TxMailListeBtr" value="<?php echo $mpTxMailListeBtr?>" style="width:99%" /><div class="admMini">Muster: <i>neuer Inserateeintrag bei #A</i></div>
 <div><input type="text" name="TxMailLstBtrAend" value="<?php echo $mpTxMailLstBtrAend?>" style="width:99%" /><div class="admMini">Muster: <i>geänderter Inserateeintrag bei #A</i></div>
 <textarea name="TxMailListeTxt" style="width:99%;height:7em;"><?php echo str_replace('\n ',"\n",$mpTxMailListeTxt)?></textarea></div><div class="admMini">Muster: <i>Im Segment #S im Marktplatz bei #A wurde folgendes Inserat veröffentlicht: #D</i>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzeileninhalte</td>
 <td><input type="radio" class="admRadio" name="MailListeNDetail" value="0"<?php if(!$mpMailListeNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Gäste &nbsp;
 <input type="radio" class="admRadio" name="MailListeNDetail" value="1"<?php if($mpMailListeNDetail) echo ' checked="checked"'?> /> Detailzeilen wie eingestellt für Benutzer </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td><select name="NutzerMailListeFeld" style="width:140px;"><?php echo str_replace('"'.$mpNutzerMailListeFeld.'"','"'.$mpNutzerMailListeFeld.'" selected="selected"',$sNOpt)?></select>
 <div class="admMini">Falls in der Inseratestruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Mailingliste mit versendet wird kann dessen zu übermittelnder Inhalt hier festgelegt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Inserate-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="EintragDSE1" value="1"<?php if($mpEintragDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="EintragDSE2" value="1"<?php if($mpEintragDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($mpCaptcha) echo' checked="checked"'?> /> verwenden&nbsp;
 Muster <span style="color:<?php echo $mpCaptchaTxFarb?>;background-color:<?php echo $mpCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span>&nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $mpCaptchaTxFarb?>" style="width:65px" />
 <a href="colors.php?col=<?php echo substr($mpCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="colWin(this.href);return false"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>&nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $mpCaptchaHgFarb?>" style="width:65px" />
 <a href="colors.php?col=<?php echo substr($mpCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="colWin(this.href);return false"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 <div><input class="admCheck" type="checkbox" name="AendernCaptcha" value="1"<?php if($mpAendernCaptcha) echo' checked="checked"'?> /> Captcha auch im Inserate<i>änderungs</i>-Formular <span class="admMini">(Empfehlung: normalerweise <i>nicht</i> notwendig)</span></div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>