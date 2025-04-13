<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Eingabeformular anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KEg');

if($_SERVER['REQUEST_METHOD']=='GET'){
 $nFelder=count($kal_FeldName);
 $ksDirekteintrag=KAL_Direkteintrag; $ksTxEingabeMeld=KAL_TxEingabeMeld; $ksTxEingabeFehl=KAL_TxEingabeFehl;
 $ksTxEingabeErfo=KAL_TxEingabeErfo; $ksTxEingabeOffl=KAL_TxEingabeOffl; $ksTxVormerkErfo=KAL_TxVormerkErfo;
 $ksTxNummerFehlt=KAL_TxNummerFehlt; $ksTxNummerUnbek=KAL_TxNummerUnbek;
 $ksTxNummerPassw=KAL_TxNummerPassw; $ksTxNummerFremd=KAL_TxNummerFremd;
 $ksDirektaendern=KAL_Direktaendern; $ksTxAendereMeld=KAL_TxAendereMeld;
 $ksTxKeineAenderung=KAL_TxKeineAenderung; $ksTxAendereErfo=KAL_TxAendereErfo; $ksTxAendereVmk=KAL_TxAendereVmk;
 $ksTxLoeschFrage=KAL_TxLoeschFrage; $ksTxLoeschErfol=KAL_TxLoeschErfol;
 $ksTxKopiereMeld=KAL_TxKopiereMeld; $ksTxKopiereErfo=KAL_TxKopiereErfo;
 $ksNEingabeLogin=KAL_NEingabeLogin; $ksNAendernFremde=KAL_NAendernFremde; $ksNKopiereEigene=KAL_NKopiereEigene; $ksNEingabeAnders=KAL_NEingabeAnders;
 $ksNutzerEMail=KAL_NutzerEMail; $ksFormatCode=KAL_FormatCode; $ksBildMaxKByte=KAL_BildMaxKByte; $ksDateiMaxKByte=KAL_DateiMaxKByte;
 $ksDateiEndgPositiv=KAL_DateiEndgPositiv; $ksDateiEndungen=KAL_DateiEndungen;
 $ksBildBreit=KAL_BildBreit; $ksBildHoch=KAL_BildHoch; $ksVorschauBreit=KAL_VorschauBreit; $ksVorschauHoch=KAL_VorschauHoch; $ksVorschauRahmen=KAL_VorschauRahmen;
 $ksThumbBreit=KAL_ThumbBreit; $ksThumbHoch=KAL_ThumbHoch; $ksBildResize=KAL_BildResize;
 $ksPeriodik=KAL_Periodik; $ksMaxPeriode=KAL_MaxPeriode; $ksMaxWiederhol=KAL_MaxWiederhol; $ksEintragszeitNeu=KAL_EintragszeitNeu; $ksEingabeTrenner=KAL_EingabeTrenner;
 $ksEintragAdminInfo=KAL_EintragAdminInfo; $ksEmpfTermin=KAL_EmpfTermin; $ksTxEintragAdminBtr=KAL_TxEintragAdminBtr; $ksTxAendernAdminBtr=KAL_TxAendernAdminBtr; $ksTxEintragAdminTxt=KAL_TxEintragAdminTxt;
 $ksEintragMail=KAL_EintragMail; $ksAendernMail=KAL_AendernMail; $ksTxEintragBtr=KAL_TxEintragBtr; $ksTxAendernBtr=KAL_TxAendernBtr; $ksTxEintragTxt=KAL_TxEintragTxt;
 $ksFreischaltMail=KAL_FreischaltMail; $ksTxFreischaltBtr=KAL_TxFreischaltBtr; $ksTxFreischaltTxt=KAL_TxFreischaltTxt;
 $ksMailListeAdr=KAL_MailListeAdr; $ksMailListeEintrag=KAL_MailListeEintrag; $ksMailListeFreischalt=KAL_MailListeFreischalt; $ksMailListeAendern=KAL_MailListeAendern;
 $ksTxMailListeBtr=KAL_TxMailListeBtr; $ksTxMailListeTxt=KAL_TxMailListeTxt; $ksAendernMitLoeschen=KAL_AendernMitLoeschen; $ksAendernLoeschArt=KAL_AendernLoeschArt; $ksAendernOnOff=KAL_AendernOnOff; $ksAendernLschOnOff=KAL_AendernLschOnOff;
 $ksTxMailLstBtrAend=KAL_TxMailLstBtrAend; $ksAendernCaptcha=KAL_AendernCaptcha;
 $ksTCalPicker=KAL_TCalPicker; $ksTCalYrScroll=KAL_TCalYrScroll;
 $ksTimeStart=KAL_TimeStart; $ksTimeStopp=KAL_TimeStopp; $ksTimeIvall=KAL_TimeIvall;
 $ksEintragDSE1=KAL_EintragDSE1; $ksEintragDSE2=KAL_EintragDSE2;
 $ksCaptcha=KAL_Captcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $s=(int)txtVar('Direkteintrag'); if(fSetzKalWert(($s?true:false),'Direkteintrag','')) $bNeu=true;
 $s=(int)txtVar('Direktaendern'); if(fSetzKalWert($s,'Direktaendern','')) $bNeu=true;
 $s=txtVar('TxEingabeMeld'); if(fSetzKalWert($s,'TxEingabeMeld','"')) $bNeu=true;
 $s=txtVar('TxEingabeFehl'); if(fSetzKalWert($s,'TxEingabeFehl','"')) $bNeu=true;
 $s=txtVar('TxEingabeErfo'); if(fSetzKalWert($s,'TxEingabeErfo','"')) $bNeu=true;
 $s=txtVar('TxEingabeOffl'); if(fSetzKalWert($s,'TxEingabeOffl','"')) $bNeu=true;
 $s=txtVar('TxVormerkErfo'); if(fSetzKalWert($s,'TxVormerkErfo','"')) $bNeu=true;
 $s=txtVar('TxNummerFehlt'); if(fSetzKalWert($s,'TxNummerFehlt','"')) $bNeu=true;
 $s=txtVar('TxNummerUnbek'); if(fSetzKalWert($s,'TxNummerUnbek','"')) $bNeu=true;
 $s=txtVar('TxNummerPassw'); if(fSetzKalWert($s,'TxNummerPassw','"')) $bNeu=true;
 $s=txtVar('TxNummerFremd'); if(fSetzKalWert($s,'TxNummerFremd','"')) $bNeu=true;
 $s=txtVar('TxAendereMeld'); if(fSetzKalWert($s,'TxAendereMeld','"')) $bNeu=true;
 $s=txtVar('TxKeineAenderung'); if(fSetzKalWert($s,'TxKeineAenderung','"')) $bNeu=true;
 $s=txtVar('TxAendereErfo'); if(fSetzKalWert($s,'TxAendereErfo','"')) $bNeu=true;
 $s=txtVar('TxAendereVmk');  if(fSetzKalWert($s,'TxAendereVmk','"')) $bNeu=true;
 $s=txtVar('TxLoeschFrage'); if(fSetzKalWert($s,'TxLoeschFrage','"')) $bNeu=true;
 $s=txtVar('TxLoeschErfol'); if(fSetzKalWert($s,'TxLoeschErfol','"')) $bNeu=true;
 $s=txtVar('TxKopiereMeld'); if(fSetzKalWert($s,'TxKopiereMeld','"')) $bNeu=true;
 $s=txtVar('TxKopiereErfo'); if(fSetzKalWert($s,'TxKopiereErfo','"')) $bNeu=true;
 $s=(int)txtVar('AendernMitLoeschen'); if(fSetzKalWert(($s?true:false),'AendernMitLoeschen','')) $bNeu=true;
 $s=(int)txtVar('AendernLoeschArt'); if(fSetzKalWert($s,'AendernLoeschArt','')) $bNeu=true;
 $s=(int)txtVar('AendernOnOff'); if(fSetzKalWert(($s?true:false),'AendernOnOff','')) $bNeu=true;
 $s=(int)txtVar('AendernLschOnOff'); if(fSetzKalWert(($s?true:false),'AendernLschOnOff','')) $bNeu=true;
 $s=(int)txtVar('NEingabeLogin'); if(fSetzKalWert(($s?true:false),'NEingabeLogin','')) $bNeu=true;
 $s=(int)txtVar('NAendernFremde'); if(fSetzKalWert(($s?true:false),'NAendernFremde','')) $bNeu=true;
 $s=(int)txtVar('NKopiereEigene'); if(fSetzKalWert(($s?true:false),'NKopiereEigene','')) $bNeu=true;
 $s=(int)txtVar('NEingabeAnders'); if(fSetzKalWert(($s?true:false),'NEingabeAnders','')) $bNeu=true;
 $nFelder=count($kal_FeldName); $bLenErr=false;
 for($i=0;$i<$nFelder;$i++){
  $kal_EingabeFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_NEingabeFeld[$i]=(isset($_POST['N'.$i])?(int)$_POST['N'.$i]:0);
  $kal_EingabeLang[$i]=(isset($_POST['M'.$i])?(int)$_POST['M'.$i]:0);
  $kal_KopierFeld[$i]=min((isset($_POST['K'.$i])?(int)$_POST['K'.$i]:0),$kal_EingabeFeld[$i]);
  $kal_NKopierFeld[$i]=min((isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0),$kal_NEingabeFeld[$i]);
  $kal_PflichtFeld[$i]=(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0);
  $t=$kal_FeldType[$i]; $sL='1';
  switch($t){
   case 't': $sL='255'; break;
   case 'm': $sL='64000'; break;
   case 'n': $sL='9'; break;
   case '1': $sL='10'; break;
   case '2': $sL='11'; break;
   case '3': $sL='12'; break;
   case 'r': $sL='12'; break;
   case 'o': $sL='9'; break;
   case 'l': $sL='255'; break;
   case 'e': case 'c': $sL='127'; break;
   case 'p': $sL='20'; break;
  }
  if($kal_EingabeLang[$i]>(int)$sL) $bLenErr=true;
 }
 $kal_EingabeFeld[1]=1; $kal_NEingabeFeld[1]=1; $kal_KopierFeld[1]=1; $kal_NKopierFeld[1]=1; $kal_PflichtFeld[1]=1;
 if(fSetzArray($kal_EingabeFeld,'EingabeFeld','')) $bNeu=true;
 if(fSetzArray($kal_NEingabeFeld,'NEingabeFeld','')) $bNeu=true;
 if(fSetzArray($kal_EingabeLang,'EingabeLang','')) $bNeu=true;
 if(fSetzArray($kal_KopierFeld,'KopierFeld','')) $bNeu=true;
 if(fSetzArray($kal_NKopierFeld,'NKopierFeld','')) $bNeu=true;
 if(fSetzArray($kal_PflichtFeld,'PflichtFeld','')) $bNeu=true;
 $s=(int)txtVar('NutzerEMail'); if(fSetzKalWert(($s?true:false),'NutzerEMail','')) $bNeu=true;
 $s=(int)txtVar('FormatCode'); if(fSetzKalWert(($s?true:false),'FormatCode','')) $bNeu=true;
 $s=(int)txtVar('BildMaxKByte'); if(fSetzKalWert($s,'BildMaxKByte','')) $bNeu=true;
 $s=(int)txtVar('DateiMaxKByte'); if(fSetzKalWert($s,'DateiMaxKByte','')) $bNeu=true;
 $s=(int)txtVar('BildBreit'); if(fSetzKalWert($s,'BildBreit','')) $bNeu=true;
 $s=(int)txtVar('BildHoch'); if(fSetzKalWert($s,'BildHoch','')) $bNeu=true;
 $s=(int)txtVar('DateiEndgPositiv'); if(fSetzKalWert(($s?true:false),'DateiEndgPositiv','')) $bNeu=true;
 $s=str_replace(';;',';',str_replace("\n",';',trim(str_replace("\r",'',str_replace('.','',strtolower(txtVar('DateiEndungen'))))))); if(fSetzKalWert($s,'DateiEndungen',"'")) $bNeu=true;
 $s=(int)txtVar('VorschauBreit'); if(fSetzKalWert($s,'VorschauBreit','')) $bNeu=true;
 $s=(int)txtVar('VorschauHoch'); if(fSetzKalWert($s,'VorschauHoch','')) $bNeu=true;
 $s=(int)txtVar('VorschauRahmen'); if(fSetzKalWert(($s?true:false),'VorschauRahmen','')) $bNeu=true;
 $s=(int)txtVar('ThumbBreit'); if(fSetzKalWert($s,'ThumbBreit','')) $bNeu=true;
 $s=(int)txtVar('ThumbHoch'); if(fSetzKalWert($s,'ThumbHoch','')) $bNeu=true;
 $s=(int)txtVar('BildResize'); if(fSetzKalWert(($s?true:false),'BildResize','')) $bNeu=true;
 $s=str_replace(';',',',str_replace(' ','',str_replace(' ','',txtVar('EingabeTrenner'))));
 while(substr($s,0,1)==',') $s=substr($s,1); while(substr($s,-1,1)==',') $s=substr($s,0,-1);
 if(fSetzKalWert($s,'EingabeTrenner',"'")) $bNeu=true;
 $s=(int)txtVar('Periodik'); if(fSetzKalWert(($s?true:false),'Periodik','')) $bNeu=true;
 $s=(int)txtVar('MaxPeriode'); if(fSetzKalWert($s,'MaxPeriode','')) $bNeu=true;
 $s=(int)txtVar('MaxWiederhol'); if(fSetzKalWert($s,'MaxWiederhol','')) $bNeu=true;
 $s=(int)txtVar('EintragszeitNeu'); if(fSetzKalWert(($s?true:false),'EintragszeitNeu','')) $bNeu=true;
 $s=(int)txtVar('EintragAdminInfo'); if(fSetzKalWert(($s?true:false),'EintragAdminInfo','')) $bNeu=true;
 $s=txtVar('EmpfTermin'); if(fSetzKalWert($s,'EmpfTermin',"'")) $bNeu=true;
 $s=txtVar('TxEintragAdminBtr'); if(fSetzKalWert($s,'TxEintragAdminBtr','"')) $bNeu=true;
 $s=txtVar('TxAendernAdminBtr'); if(fSetzKalWert($s,'TxAendernAdminBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxEintragAdminTxt')))); if(fSetzKalWert($s,'TxEintragAdminTxt',"'")) $bNeu=true;
 $s=(int)txtVar('EintragMail'); if(fSetzKalWert(($s?true:false),'EintragMail','')) $bNeu=true;
 $s=(int)txtVar('AendernMail'); if(fSetzKalWert(($s?true:false),'AendernMail','')) $bNeu=true;
 $s=txtVar('TxEintragBtr'); if(fSetzKalWert($s,'TxEintragBtr','"')) $bNeu=true;
 $s=txtVar('TxAendernBtr'); if(fSetzKalWert($s,'TxAendernBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxEintragTxt')))); if(fSetzKalWert($s,'TxEintragTxt',"'")) $bNeu=true;
 $s=(int)txtVar('FreischaltMail'); if(fSetzKalWert(($s?true:false),'FreischaltMail','')) $bNeu=true;
 $s=txtVar('TxFreischaltBtr'); if(fSetzKalWert($s,'TxFreischaltBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxFreischaltTxt')))); if(fSetzKalWert($s,'TxFreischaltTxt',"'")) $bNeu=true;
 $s=txtVar('MailListeAdr'); if(fSetzKalWert($s,'MailListeAdr','"')) $bNeu=true;
 $s=(int)txtVar('MailListeEintrag'); if(fSetzKalWert(($s?true:false),'MailListeEintrag','')) $bNeu=true;
 $s=(int)txtVar('MailListeFreischalt'); if(fSetzKalWert(($s?true:false),'MailListeFreischalt','')) $bNeu=true;
 $s=(int)txtVar('MailListeAendern'); if(fSetzKalWert(($s?true:false),'MailListeAendern','')) $bNeu=true;
 $s=txtVar('TxMailListeBtr'); if(fSetzKalWert($s,'TxMailListeBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxMailListeTxt')))); if(fSetzKalWert($s,'TxMailListeTxt',"'")) $bNeu=true;
 $s=txtVar('TxMailLstBtrAend'); if(fSetzKalWert($s,'TxMailLstBtrAend','"')) $bNeu=true;
 $s=(int)txtVar('TCalPicker'); if(fSetzKalWert(($s?true:false),'TCalPicker','')) $bNeu=true;
 $s=(int)txtVar('TCalYrScroll'); if(fSetzKalWert(($s?true:false),'TCalYrScroll','')) $bNeu=true;
 $s=txtVar('TimeStart'); if(fSetzKalWert(sprintf('%.2f',$s),'TimeStart','')) $bNeu=true;
 $s=txtVar('TimeStopp'); if(fSetzKalWert(sprintf('%.2f',$s),'TimeStopp','')) $bNeu=true;
 $s=max(0.25,txtVar('TimeIvall')); if(fSetzKalWert(sprintf('%.2f',$s),'TimeIvall','')) $bNeu=true;
 $s=txtVar('EintragDSE1'); if(fSetzKalWert(($s?true:false),'EintragDSE1','')) $bNeu=true;
 $s=txtVar('EintragDSE2'); if(fSetzKalWert(($s?true:false),'EintragDSE2','')) $bNeu=true;
 $s=txtVar('Captcha'); if(fSetzKalWert(($s?true:false),'Captcha','')) $bNeu=true;
 $s=txtVar('CaptchaTxFarb'); if(fSetzKalWert($s,'CaptchaTxFarb',"'")) $bNeu=true;
 $s=txtVar('CaptchaHgFarb'); if(fSetzKalWert($s,'CaptchaHgFarb',"'")) $bNeu=true;
 $s=txtVar('CaptchaTyp'); if(fSetzKalWert($s,'CaptchaTyp',"'")) $bNeu=true;
 $s=txtVar('CaptchaGrafisch'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
 $s=txtVar('CaptchaNumerisch'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
 $s=txtVar('CaptchaTextlich'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
 $s=txtVar('AendernCaptcha'); if(fSetzKalWert(($s?true:false),'AendernCaptcha','')) $bNeu=true;
 if($bNeu){//Speichern
  if(!$bLenErr){
   if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    if(!$Msg) $Msg='<p class="admErfo">Die Einstellungen für das Eingabeformular wurden gespeichert.</p>';
   }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
  }else $Msg='<p class="admFehl">Mindestens eine Eingabefeldlänge überschreitet den Maximalwert!</p>';
 }else if(!$Msg) $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Termineingabeformular.</p>';
echo $Msg.NL;
?>

<form name="farbform" action="konfEingabe.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Termineingaben durch Besucher können als Direkteintrag mit sofortiger Veröffentlichung
oder als Terminvorschlag mit Freigabe nach Überprüfung durch den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Eintragsart</td>
 <td><input type="radio" class="admRadio" name="Direkteintrag" value="1"<?php if($ksDirekteintrag) echo ' checked="checked"'?> /> Direkteintrag &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="Direkteintrag" value="0"<?php if(!$ksDirekteintrag) echo ' checked="checked"'?> /> Terminvorschlag &nbsp; &nbsp;
 <span class="admMini">Empfehlung: <i>Terminvorschlag!</i></span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsart</td>
 <td><input type="radio" class="admRadio" name="Direktaendern" value="1"<?php if($ksDirektaendern=='1') echo ' checked="checked"'?> /> Direktändern<br />
 <input type="radio" class="admRadio" name="Direktaendern" value="0"<?php if(!$ksDirektaendern) echo ' checked="checked"'?> /> Änderungsvorschlag mit Ausblenden des Originals<br />
 <input type="radio" class="admRadio" name="Direktaendern" value="2"<?php if($ksDirektaendern=='2') echo ' checked="checked"'?> /> Änderungsvorschlag mit Sichtbarlassen des Originals</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Eingabeformular für Termine wird Besuchern folgende Aufforderungsmeldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderung Eingabe</td>
 <td><div><input type="text" name="TxEingabeMeld" value="<?php echo $ksTxEingabeMeld?>" style="width:100%" /></div><div class="admMini">Empfehlung: <i>Tragen Sie jetzt Ihren neuen Termin ein!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefehler</td>
 <td><input type="text" name="TxEingabeFehl" value="<?php echo $ksTxEingabeFehl?>" style="width:100%" /><div class="admMini">(Wird auch im Informationsformular und Kontaktformular verwendet.)<br />Empfehlung: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigung Vorschlag<div style="margin-top:20px;">Bestätigung Direkteintrag</div></td>
 <td><input type="text" name="TxVormerkErfo" value="<?php echo $ksTxVormerkErfo?>" style="width:100%;<?php if($ksDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Der Termin wurde vorgemerkt und der Webmaster informiert!</i></div>
 <input type="text" name="TxEingabeErfo" value="<?php echo $ksTxEingabeErfo?>" style="width:100%;<?php if(!$ksDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Der Termin wurde eingetragen und veröffentlicht!</i></div>
 <input type="text" name="TxEingabeOffl" value="<?php echo $ksTxEingabeOffl?>" style="width:100%;<?php if(!$ksDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Der Termin wurde eingetragen aber nicht veröffentlicht!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderungen Ändern</td>
 <td>
 <input type="text" name="TxNummerFehlt" value="<?php echo $ksTxNummerFehlt?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Bitte geben Sie die laufende Terminnummer an!</i></div>
 <input type="text" name="TxNummerUnbek" value="<?php echo $ksTxNummerUnbek?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Datensatz nicht gefunden! Bitte geben Sie eine gültige Nummer an!</i></div>
 <input type="text" name="TxNummerPassw" value="<?php echo $ksTxNummerPassw?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Bitte geben Sie das korrekte Passwort zum Termin an!</i></div>
 <input type="text" name="TxNummerFremd" value="<?php echo $ksTxNummerFremd?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Dieser Termin gehört nicht Ihnen! Bitte geben Sie eine gültige Nummer an!</i></div>
 <input type="text" name="TxAendereMeld" value="<?php echo $ksTxAendereMeld?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Ändern Sie jetzt diesen Termin ab!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestätigung Ändern</td>
 <td><input type="text" name="TxKeineAenderung" value="<?php echo $ksTxKeineAenderung?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Daten bleiben unverändert!</i></div>
 <input type="text" name="TxAendereErfo" value="<?php echo $ksTxAendereErfo?>" style="width:100%;<?php if(!$ksDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Die geänderten Termindaten wurden eingetragen!</i></div>
 <input type="text" name="TxAendereVmk" value="<?php echo $ksTxAendereVmk?>" style="width:100%;<?php if($ksDirekteintrag) echo'color:#999999;'?>" /><div class="admMini">Empfehlung: <i>Die Änderungen wurden vorgemerkt und der Webmaster informiert!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termin löschen</td>
 <td><input type="text" name="TxLoeschFrage" value="<?php echo $ksTxLoeschFrage?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Wollen Sie den markierten Termin wirklich löschen?</i></div>
 <input type="text" name="TxLoeschErfol" value="<?php echo $ksTxLoeschErfol?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Der markierte Termin wurde gelöscht!</i><br />oder: <i>Der Termin wurde zum Löschen vorgemerkt und der Administrator benachrichtigt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminkopie<div class="admMini">(Administrator/Autoren)</div></td>
 <td><input type="text" name="TxKopiereMeld" value="<?php echo $ksTxKopiereMeld?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Kopieren Sie diesen Termin auf ein neues Datum!</i></div>
 <input type="text" name="TxKopiereErfo" value="<?php echo $ksTxKopiereErfo?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Die Terminkopie wurde eingetragen und veröffentlicht!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Eingabeformular für Termine im Besucherbereich kann bezüglich der Eingabeberechtigungen konfiguriert werden.
Welche Eingabe- und Änderungsmöglichkeiten sollen für unangemeldete Gäste bzw. für angemeldete Benutzer vorhanden sein,
sofern die Benutzerverwaltung durch ein Feld vom Typ <i>Benutzer</i> in der Terminstruktur eingeschaltet ist? (momentan <?php echo(in_array('u',$kal_FeldType)?'':'<span style="color:#AA0000;">nicht</span> ')?>der Fall)</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabeberechtigungen</td>
 <td><div><input type="checkbox" class="admCheck" name="NEingabeLogin" value="1"<?php if($ksNEingabeLogin) echo ' checked="checked"'?> /> <i>nur</i> angemeldete Benutzer sollen Eingaben und Änderungen ausführen können <a href="<?php echo ADM_Hilfe?>LiesMich.htm#6.3" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 <div><input type="checkbox" class="admCheck" name="NAendernFremde" value="1"<?php if($ksNAendernFremde) echo ' checked="checked"'?> /> angemeldete Benutzer sollen auch fremde Termine ändern dürfen <span class="admMini">(Empfehlung: <i>nein</i>)</span></div>
 <div><input type="checkbox" class="admCheck" name="NKopiereEigene" value="1"<?php if($ksNKopiereEigene) echo ' checked="checked"'?> /> angemeldete Benutzer sollen nur eigene Termine kopieren dürfen <span class="admMini">(Empfehlung: <i>nein</i>)</span></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Formularvarianten</td>
 <td><input type="checkbox" class="admCheck" name="NEingabeAnders" value="1"<?php if($ksNEingabeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Eingabefelder sehen als Gäste</td>
</tr>
<tr class="admTabl">
 <td>Deaktivieren<br>von Terminen</td>
 <td><input type="checkbox" class="admCheck" name="AendernOnOff" value="1"<?php if($ksAendernOnOff) echo ' checked="checked"'?> /> Unter den Eigabeformularen können beim Ändern durch angemeldete Benutzer 2 oder 3 Radioschalter zum Online/Offlineschalten bzw. Löschen von Terminen angeboten werden. &nbsp; <span class="admMini">(keine Empfehlung)</span><div style="margin-top:3px"><input type="checkbox" class="admCheck" name="AendernLschOnOff" value="1"<?php if($ksAendernLschOnOff) echo ' checked="checked"'?> /> mit drittem Lösch-Schalter</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Löschmöglichkeit</td>
 <td>
  <div><input type="checkbox" class="admCheck" name="AendernMitLoeschen" value="1"<?php if($ksAendernMitLoeschen) echo ' checked="checked"'?> /> Unter dem Änderungsformular kann ein Kontrollkästchen zum Terminlöschen angeboten werden. &nbsp; <span class="admMini">Empfehlung: anbieten</span></div>
  <div>&nbsp;<input type="radio" class="admRadio" name="AendernLoeschArt" value="1"<?php if($ksAendernLoeschArt==1) echo ' checked="checked"'?> /> sofortiges direktes Löschen durch den Besucher<br />
       &nbsp;<input type="radio" class="admRadio" name="AendernLoeschArt" value="2"<?php if($ksAendernLoeschArt==2) echo ' checked="checked"'?> /> unsichtbar schalten und Löschen durch den Administrator<br />
       &nbsp;<input type="radio" class="admRadio" name="AendernLoeschArt" value="3"<?php if($ksAendernLoeschArt==3) echo ' checked="checked"'?> /> sichtbar lassen bis zum Löschen durch den Administrator</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1"><div style="float:right;" title="Längenbegrenzung eintragen oder leer lassen">Länge<br />&nbsp;max.</div><?php echo $kal_FeldName[1]?><div class="admMini">(Typ <i>Datum</i>)</div></td>
 <td>01) <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" alt="ja">&nbsp; Pflichtfeld &nbsp; &nbsp; <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" alt="ja">/<img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" alt="ja"> Eingabefeld Gast/Benutzer</td>
</tr>
<?php
 include('feldtypenInc.php');
 for($i=2;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $sL='';
  switch($t){
   case 't': $sL='255'; break;
   case 'm': $sL='64000'; break;
   case 'n': $sL='9'; break;
   case '1': $sL='10'; break;
   case '2': $sL='11'; break;
   case '3': $sL='12'; break;
   case 'r': $sL='12'; break;
   case 'o': $sL='9'; break;
   case 'l': $sL='255'; break;
   case 'e': case 'c': $sL='127'; break;
   case 'p': $sL='20'; break;
  }
  if($sL) $sL='<div style="float:right;"><input style="width:2.6em;" type="text" name="M'.$i.'" value="'.($kal_EingabeLang[$i]>0?$kal_EingabeLang[$i]:'').'" title="gewünschte Längenbegrenzung eintragen'."\n".'oder leer lassen für max. '.$sL.' Zeichen" /></div>';
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:1%;"><?php echo $sL.$kal_FeldName[$i]?><div class="admMini">(Typ <i><?php echo $aTyp[$t]?></i>)</div></td>
 <td><?php echo sprintf('%02d',$i).') '.($t!='g'?'<input class="admCheck" type="checkbox" name="P'.$i.'" value="1"'.($kal_PflichtFeld[$i]==1?' checked="checked"':'').'> Pflichtfeld &nbsp; &nbsp; <input class="admCheck" type="checkbox" name="F'.$i.'" value="1"'.($kal_EingabeFeld[$i]==1?' checked="checked"':'').' />/<input class="admCheck" type="checkbox" name="N'.$i.'" value="1"'.($kal_NEingabeFeld[$i]==1?' checked="checked"':'').'> Eingabefeld Gast/Benutzer'.($t!='@'||$kal_FeldName[$i]=='ZUSAGE_BIS'?' &nbsp; <input class="admCheck" type="checkbox" name="K'.$i.'" value="1"'.($kal_KopierFeld[$i]==1?' checked="checked"':'').' />/<input class="admCheck" type="checkbox" name="L'.$i.'" value="1"'.($kal_NKopierFeld[$i]==1?' checked="checked"':'').'> Kopierfeld Gast/Benutzer':''):'')?></td>
</tr>
<?php }?>
<tr class="admTabl">
 <td class="admSpa1">eventuelle Trennzeilen<br />im Formular</td>
 <td><div style="float:left;"><input type="text" style="width:105px" name="EingabeTrenner" value="<?php echo $ksEingabeTrenner?>" /></div>
 Zeilennummern durch Komma getrennt auflisten, nach denen aus rein optischen Gründen eine Leerzeile eingefügt werden soll</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls ein Feld vom Typ <i>E-Mail</i> bzw. vom Typ <i>Kontakt</i> in der Terminstruktur vorhanden ist
und die Benutzerverwaltung aktiv ist, kann die E-Mail-Adresse eines angemeldeten Benutzers im leeren Eingabeformular voreingetragen werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Vorbelegung</td>
 <td><input type="checkbox" class="admCheck" name="NutzerEMail" value="1"<?php if($ksNutzerEMail) echo ' checked="checked"'?> /> <i>E-Mail-</i> oder <i>Kontakt-</i>Feld mit Benutzer-E-Mail vorbelegen &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Memofelder und Gastkommentare können durch sogenannten BB-Code (fett, kursiv...) formatiert werden.
 Soll über den Eingabefeldern eine Symbolleiste mit Formatierungselementen eingeblendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formatierung</td>
 <td><input type="checkbox" class="admRadio" name="FormatCode" value="1"<?php if($ksFormatCode) echo ' checked="checked"'?> /> Formatierungsmöglichkeit anbieten &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Datums- und Zeitfelder können mit einer aufklickbaren Eingabehilfe in Form eines kleinen Kalenderblattes oder einer Stundentafel ergänzt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datums- und Zeitpicker</td>
 <td><input type="checkbox" class="admRadio" name="TCalPicker" value="1"<?php if($ksTCalPicker) echo ' checked="checked"'?> /> Datums- und Zeitpicker anbieten &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitpicker</td>
 <td>
 Startzeit <select name="TimeStart" style="width:5.5em;"><?php for($i=0;$i<23.9;$i+=0.25) echo '<option value="'.sprintf('%.2f',$i).($i!=$ksTimeStart?'':'" selected="selected').'">'.fFmtTime($i).'</option>' ?></select> &nbsp;
 Stoppzeit <select name="TimeStopp" style="width:5.5em;"><?php for($i=0;$i<23.9;$i+=0.25) echo '<option value="'.sprintf('%.2f',$i).($i!=$ksTimeStopp?'':'" selected="selected').'">'.fFmtTime($i).'</option>' ?></select> &nbsp;
 Intervall <select name="TimeIvall" style="width:5em;"><option value="0.25<?php if($ksTimeIvall==0.25) echo '" selected="selected' ?>">0:15</option><option value="0.50<?php if($ksTimeIvall==0.50) echo '" selected="selected' ?>">0:30</option><option value="1.00<?php if($ksTimeIvall==1.00) echo '" selected="selected' ?>">1:00</option><option value="1.50<?php if($ksTimeIvall==1.50) echo '" selected="selected' ?>">1:30</option><option value="2.00<?php if($ksTimeIvall==2.00) echo '" selected="selected' ?>">2:00</option></select></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Datumspicker</td>
 <td><input type="checkbox" class="admRadio" name="TCalYrScroll" value="1"<?php if($ksTCalYrScroll) echo ' checked="checked"'?> /> Weiterblättern in Jahresschritten ermöglichen &nbsp; (<span class="admMini">Empfehlung: <i>nicht aktivieren</i></i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls in der Terminstruktur Bilder oder Dateien enthalten sind können deren Größen begrenzt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilder hochladen</td>
 <td style="padding-top:5px;">
 <div><span style="width:11em;display:inline-block">Hochladen bis max.</span> <input type="text" name="BildMaxKByte" value="<?php echo $ksBildMaxKByte?>" style="width:30px;" /> KByte erlauben  &nbsp; (<span class="admMini">Empfehlung: höchstens 250 KByte, 0 für unbegrenzt</i></span>)</div>
 <div><span style="width:11em;display:inline-block">große Bilder auf max.</span> <input type="text" name="BildBreit" value="<?php echo $ksBildBreit?>" style="width:30px;" /> x <input type="text" name="BildHoch" value="<?php echo $ksBildHoch?>" style="width:30px;" /> Pixel (Breite x Höhe) verkleinern &nbsp; (<span class="admMini">Empfehlung: 400x300</i></span>)</div>
 <div><span style="width:11em;display:inline-block;margin-top:3px;margin-bottom:5px">verkleinern</span> <input type="checkbox" class="admCheck" name="BildResize" value="1"<?php if($ksBildResize) echo ' checked="checked"';?> /> Bild bereits schon <i>vor</i> dem Upload per Java-Script im Browser verkleinern</div>
 <div><span style="width:11em;display:inline-block">Vorschaubilder auf</span> <input type="text" name="VorschauBreit" value="<?php echo $ksVorschauBreit?>" style="width:30px;" /> x <input type="text" name="VorschauHoch" value="<?php echo $ksVorschauHoch?>" style="width:30px;" /> Pixel (Breite x Höhe) verkleinern &nbsp; (<span class="admMini">Empfehlung: 100x80</i></span>)</div>
 <div><span style="width:11em;display:inline-block;margin-top:3px;margin-bottom:5px">Vorschaubilder</span> <input type="checkbox" class="admCheck" name="VorschauRahmen" value="1"<?php if($ksVorschauRahmen) echo ' checked="checked"';?> /> einrahmen mit Rahmen in exakt dieser Größe</div>
 <div><span style="width:11em;display:inline-block">FormularIcons</span> <input type="text" name="ThumbBreit" value="<?php echo $ksThumbBreit?>" style="width:30px;" /> x <input type="text" name="ThumbHoch" value="<?php echo $ksThumbHoch?>" style="width:30px;" /> px (im Eingabeformular Breite x Höhe)&nbsp; (<span class="admMini">Empfehlung: 90x64</i></span>)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Dateien hochladen<br><br>Dateiendungen</td>
 <td><span style="width:9em;display:inline-block">Dateianhänge bis höchstens</span> <input type="text" name="DateiMaxKByte" value="<?php echo $ksDateiMaxKByte?>" style="width:30px;" /> KByte erlauben  &nbsp; (<span class="admMini">Empfehlung: höchstens 500 KByte</i></span>)
  <table border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-right:8px;"><textarea name="DateiEndungen" cols="9" rows="4" style="width:9em;height:6.6em;"><?php echo trim(str_replace(';',"\n",$ksDateiEndungen));?></textarea></td>
   <td><input class="admRadio" type="radio" name="DateiEndgPositiv" value="1<?php if($ksDateiEndgPositiv) echo '" checked="checked'?>" /> als erlaubte Dateiendungen (Positivliste)<br><input class="admRadio" type="radio" name="DateiEndgPositiv" value="0<?php if(!$ksDateiEndgPositiv) echo '" checked="checked'?>" /> als verbotene Dateiendungen (Negativliste)</td></tr></table>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Unter den Eingabefeldern kann ein zusätzlicher Bereich
mit Eingabemöglichkeiten für eine periodische Terminwiederholung eingeblendert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Periodikverhalten</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="Periodik" value="1"<?php if($ksPeriodik) echo ' checked="checked"'?> /> Periodik verwenden &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="Periodik" value="0"<?php if(!$ksPeriodik) echo ' checked="checked"'?> /> Periodik ausschalten &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)
 <div>ein Termin kann durch die Periodik längstens bis zu <input type="text" name="MaxPeriode" value="<?php echo $ksMaxPeriode?>" style="width:35px;" /> Tage im Voraus eingetragen werden</div>
 <div>ein Termin kann durch die Periodik maximal <input type="text" name="MaxWiederhol" value="<?php echo $ksMaxWiederhol?>" style="width:25px;" /> mal im Voraus eingetragen werden</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihre Terminstruktur ein Feld vom Typ <i>Eintragszeit</i> enthält wird dieses automatisch beim Termineintrag mit dem dortigen Datum/Uhrzeit gefüllt.
Bei Terminänderungen bleibt es normalerweise im Originalzustand. Abweichend davon kann das Feld bei Terminänderungen mit dem Zeitstempel der Terminänderung aktualisiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Eintrags- und<br />Änderungszeit</td>
 <td style="padding-top:5px;"><input type="radio" class="admRadio" name="EintragszeitNeu" value="0"<?php if(!$ksEintragszeitNeu) echo ' checked="checked"'?> /> Original-Eintragszeit beibehalten &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="EintragszeitNeu" value="1"<?php if($ksEintragszeitNeu) echo ' checked="checked"'?> /> Änderungszeit eintragen &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Termineintragungen durch Benutzer können E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an die in Feldern vom Typ <i>Kontakt</i> bzw. <i>E-Mail</i> eingetragenen Adressen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Webmaster<div style="margin-top:5px;">alternative Adresse</div><div style="margin-top:24px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td><div><input class="admCheck" type="checkbox" name="EintragAdminInfo" value="1"<?php if($ksEintragAdminInfo) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <div><input type="text" name="EmpfTermin" value="<?php echo $ksEmpfTermin?>" style="width:220px" /> <span class="admMini">leer lassen oder E-Mail-Adresse des Terminverwalters</span></div>
 <div class="admMini" style="margin-bottom:5px">(Wird bei Terminaktionen anstatt <i><?php echo KAL_Empfaenger?></i> für die E-Mails an den Webmaster verwendet.)</div>
 <div><input type="text" name="TxEintragAdminBtr" value="<?php echo $ksTxEintragAdminBtr?>" style="width:100%" /><div class="admMini">Muster: <i>neuer Kalendereintrag Nr. #N</i></div>
 <div><input type="text" name="TxAendernAdminBtr" value="<?php echo $ksTxAendernAdminBtr?>" style="width:100%" /><div class="admMini">Muster: <i>geaenderter Kalendereintrag Nr. #N</i></div>
 <textarea name="TxEintragAdminTxt" cols="80" rows="4" style="height:5em"><?php echo str_replace('\n ',"\n",$ksTxEintragAdminTxt)?></textarea></div><div class="admMini">Muster: <i>Im Kalender wurde folgender Eintrag #N vorgenommen: #D</i></div><div class="admMini">Hinweis: In einem Freischaltlink wie <i>https://ihr-web.de/kalender/freigeben.php#C</i> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.8C" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" style="vertical-align:-3px" border="0" title="Hilfe zum Freigabecode"></a> ergänzt #C den Freischaltcode.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Benutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td><div><input class="admCheck" type="checkbox" name="EintragMail" value="1"<?php if($ksEintragMail) echo' checked="checked"'?> /> beim Eintragen &nbsp; &nbsp; <input class="admCheck" type="checkbox" name="AendernMail" value="1"<?php if($ksAendernMail) echo' checked="checked"'?> />  beim Ändern &nbsp; <span class="admMini">(Empfehlung: <i>nicht versenden</i>)</i></span></div>
 <div><input type="text" name="TxEintragBtr" value="<?php echo $ksTxEintragBtr?>" style="width:100%" /><div class="admMini">Muster: <i>Re: Ihr Kalendereintrag bei #A</i></div>
 <div><input type="text" name="TxAendernBtr" value="<?php echo $ksTxAendernBtr?>" style="width:100%" /><div class="admMini">Muster: <i>Re: Ihr geänderter Kalendereintrag bei #A</i></div>
 <textarea name="TxEintragTxt" cols="80" rows="6" style="height:7em"><?php echo str_replace('\n ',"\n",$ksTxEintragTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Eintrag im Kalender bei #A wurde mit folgenden Daten entgegengenommen: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail bei Freischaltung<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td><div><input class="admCheck" type="checkbox" name="FreischaltMail" value="1"<?php if($ksFreischaltMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <div><input type="text" name="TxFreischaltBtr" value="<?php echo $ksTxFreischaltBtr?>" style="width:100%" /><div class="admMini">Muster: <i>Re: Ihr Kalendereintrag bei #A</i></div>
 <textarea name="TxFreischaltTxt" cols="80" rows="6" style="height:7em"><?php echo str_replace('\n ',"\n",$ksTxFreischaltTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Eintrag im Kalender bei #A wurde mit folgenden Daten entgegengenommen: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Mailingliste<div style="margin-top:23px;">Betreff</div><div style="margin-top:50px;">Text</div></td>
 <td><div><input type="text" name="MailListeAdr" value="<?php echo $ksMailListeAdr?>" style="width:322px;" /> <span class="admMini">E-Mail-Adresse der Liste</span></div>
 <div><input class="admCheck" type="checkbox" name="MailListeEintrag" value="1"<?php if($ksMailListeEintrag) echo' checked="checked"'?> /> beim Termineintragen &nbsp; <input class="admCheck" type="checkbox" name="MailListeAendern" value="1"<?php if($ksMailListeAendern) echo' checked="checked"'?> /> beim Terminändern &nbsp; <input class="admCheck" type="checkbox" name="MailListeFreischalt" value="1"<?php if($ksMailListeFreischalt) echo' checked="checked"'?> /> beim Terminfreigeben</div>
 <div><input type="text" name="TxMailListeBtr" value="<?php echo $ksTxMailListeBtr?>" style="width:100%" /><div class="admMini">Muster: <i>neuer Kalendereintrag bei #A</i></div>
 <div><input type="text" name="TxMailLstBtrAend" value="<?php echo $ksTxMailLstBtrAend?>" style="width:100%" /><div class="admMini">Muster: <i>geänderter Kalendereintrag bei #A</i></div>
 <textarea name="TxMailListeTxt" cols="80" rows="6" style="height:7em"><?php echo str_replace('\n ',"\n",$ksTxMailListeTxt)?></textarea></div><div class="admMini">Muster: <i>Im Kalender bei #A wurde folgender Termin veröffentlicht: #D</i>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Termin-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td><input class="admCheck" type="checkbox" name="EintragDSE1" value="1"<?php if($ksEintragDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="EintragDSE2" value="1"<?php if($ksEintragDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
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
 <div><input class="admCheck" type="checkbox" name="AendernCaptcha" value="1"<?php if($ksAendernCaptcha) echo' checked="checked"'?> /> Captcha auch im Termin<i>änderungs</i>-Formular <span class="admMini">(Empfehlung: normalerweise <i>nicht</i> notwendig)</span></div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
 echo fSeitenFuss();

 function fFmtTime($v){$h=floor($v); return sprintf('%02d:%02d',$h,($v-$h)*60);}
?>