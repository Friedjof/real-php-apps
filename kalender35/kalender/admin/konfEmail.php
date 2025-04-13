<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('E-Mail-Einstellungen','','KEm'); $ksSmtpTLS=(SMTP_No_TLS?'0':'1');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $Msg='<p class="admMeld">Stellen Sie den E-Mail-Betrieb des Kalender-Scripts passend ein.</p>';
 $ksEmpfaenger=KAL_Empfaenger; $ksEmpfTermin=KAL_EmpfTermin; $ksEmpfNutzer=KAL_EmpfNutzer; $ksEmpfZusage=KAL_EmpfZusage;
 $ksSender=KAL_Sender; $ksKeineAntwort=KAL_KeineAntwort;
 $ksSmtp=KAL_Smtp; $ksSmtpHost=KAL_SmtpHost; $ksSmtpPort=KAL_SmtpPort;
 $ksSmtpAuth=KAL_SmtpAuth; $ksSmtpUser=KAL_SmtpUser; $ksSmtpPass=KAL_SmtpPass;
 $ksEnvelopeSender=KAL_EnvelopeSender; $ksCronMail=KAL_CronMail;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=txtVar('Empfaenger'); if(fSetzKalWert($v,'Empfaenger',"'")) $bNeu=true;
 $v=txtVar('EmpfTermin'); if(fSetzKalWert($v,'EmpfTermin',"'")) $bNeu=true;
 $v=txtVar('EmpfNutzer'); if(fSetzKalWert($v,'EmpfNutzer',"'")) $bNeu=true;
 $v=txtVar('EmpfZusage'); if(fSetzKalWert($v,'EmpfZusage',"'")) $bNeu=true;
 $v=txtVar('Sender'); if(fSetzKalWert($v,'Sender',"'")) $bNeu=true;
 $v=txtVar('KeineAntwort'); if(fSetzKalWert($v,'KeineAntwort',"'")) $bNeu=true;
 $v=(int)txtVar('Smtp'); if(fSetzKalWert(($v?true:false),'Smtp',"'")) $bNeu=true;
 $v=txtVar('SmtpHost'); if(fSetzKalWert($v,'SmtpHost',"'")) $bNeu=true;
 $v=(int)txtVar('SmtpPort'); if(fSetzKalWert($v,'SmtpPort','')) $bNeu=true;
 $v=(int)txtVar('SmtpTLS'); if(fSetzSmtpNoTLS($v?false:true)) $bNeu=true;
 $v=(int)txtVar('SmtpAuth'); if(fSetzKalWert(($v?true:false),'SmtpAuth','')) $bNeu=true;
 $v=txtVar('SmtpUser'); if(fSetzKalWert($v,'SmtpUser',"'")) $bNeu=true;
 $v=txtVar('SmtpPass'); if(fSetzKalWert($v,'SmtpPass',"'")) $bNeu=true;
 $v=txtVar('EnvelopeSender'); if(fSetzKalWert($v,'EnvelopeSender',"'")) $bNeu=true;
 $v=(int)txtVar('CronMail'); if(fSetzKalWert(($v?true:false),'CronMail','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die E-Mail-Einstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die E-Mail-Einstellungen bleiben unverändert.</p>';
}//POST

//Scriptausgabe
echo $Msg.NL;
?>

<form name="emailform" action="konfEmail.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Termineintragungen durch Besucher und bei Verwendung der Informationsfunktion, Erinnerungsfunktion, Benachrichtigungsfunktion und Benutzerfreischaltung werden E-Mail-Nachrichten versandt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Empfang</td>
 <td><input type="text" name="Empfaenger" value="<?php echo $ksEmpfaenger?>" style="width:210px;" /> E-Mail-Adresse des Webmasters &nbsp; <span class="admMini">(Wird nirgends veröffentlicht!)</span>
 <div class="admMini">Mehrere Adressen durch Komma getrennt - das funktioniert jedoch nicht mit Garantie auf jedem Server!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br>E-Mail-Empfang<div style="margin-top:6px">(nur bei Bedarf)</div></td>
 <td><div>Sofern nachfolgend nichts abweichendes eingetragen ist, werden alle Webmaster-E-Mails an obige Adresse versandt.</div>
  <input type="text" name="EmpfTermin" value="<?php echo $ksEmpfTermin?>" style="width:210px;" /> E-Mail-Adresse des Terminverantwortlichen &nbsp; <span class="admMini">(oder leer lassen)</span><br>
  <input type="text" name="EmpfNutzer" value="<?php echo $ksEmpfNutzer?>" style="width:210px;" /> E-Mail-Adresse des Benutzerverwalters &nbsp; <span class="admMini">(oder leer lassen)</span><br>
  <?php if(KAL_ZusageSystem){?><input type="text" name="EmpfZusage" value="<?php echo $ksEmpfZusage?>" style="width:210px;" /> E-Mail-Adresse des Zusagenbearbeiters &nbsp; <span class="admMini">(oder leer lassen)</span><br><?php }else echo '<input type="hidden" name="EmpfZusage" value="'.$ksEmpfZusage.'" />'; ?>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Absender</td>
 <td><div style="float:left;width:330px;"><input type="text" name="Sender" value="<?php echo $ksSender?>" style="width:330px;" /></div>
 <div class="admMini" style="margin-left:335px;">Format: absender@domain.de &nbsp; oder<br />Format: Absender &lt;absender@domain.de&gt;</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">keine Antwort</td>
 <td><div style="float:left;width:330px;"><input type="text" name="KeineAntwort" value="<?php echo $ksKeineAntwort?>" style="width:330px;" /></div>
 <div class="admMini" style="margin-left:335px;">Format: noreply@domain.de &nbsp; oder<br />Format: Niemand &lt;noreply@domain.de&gt;</div></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Mailtransport</td>
 <td><input type="radio" class="admRadio" name="Smtp" value="0"<?php if(!$ksSmtp){echo ' checked="checked"'; $sSmtpStyle='color:#888;';}?> /> per PHP-mail()-Funktion &nbsp;
 <input type="radio" class="admRadio" name="Smtp" value="1"<?php if($ksSmtp){echo ' checked="checked"'; $sSmtpStyle='';}?> /> über einen SMTP-Server
 <div class="admMini">Standard: PHP-mail() &nbsp; (nur dann SMTP verwenden, wenn PHP-mail() nicht zur Verfügung steht)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls PHP-mail() aktiviert ist muss in seltenen Fällen folgender Parameter gesetzt sein:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Envelope-<br />Absenderadresse</td>
 <td><div><input type="text" name="EnvelopeSender" value="<?php echo $ksEnvelopeSender?>" style="width:330px;" /></div>
 <div class="admMini">leer lassen (nur ausfüllen mit reiner E-Mail-Adresse name@domain.de wenn Ihr Provider eine Envelope-Absenderadresse als sendmail-Parameter -f ausdrücklich verlangt)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls SMTP aktiviert ist müssen die folgenden Parameter gesetzt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div style="height:2.0em;">SMTP-Host</div><div style="height:3.2em;">SMTP-Port</div><div style="height:1.8em;">Authentifizierung</div><div style="height:1.8em;">SMTP-Benutzer</div><div style="height:1.8em;">SMTP-Passwort</div></td>
 <td><input type="text" name="SmtpHost" value="<?php echo $ksSmtpHost?>" style="width:330px;<?php echo $sSmtpStyle?>" />
 <div><input type="text" name="SmtpPort" value="<?php echo $ksSmtpPort?>" style="width:32px;<?php echo $sSmtpStyle?>" /> <span class="admMini">(Standard: 587 oder 25)</span></div>
 <div><input type="checkbox" class="admCheck" name="SmtpTLS" value="1"<?php if($ksSmtpTLS) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> TLS-Verschlüsselung verwenden (soweit vom Server angeboten)</div>
 <div style="margin-top:4px"><input type="checkbox" class="admCheck" name="SmtpAuth" value="1"<?php if($ksSmtpAuth) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> Authentifizieren am SMTP-Server mit folgenden Daten:</div>
 <div><input type="text" name="SmtpUser" value="<?php echo $ksSmtpUser?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div>
 <div><input type="text" name="SmtpPass" value="<?php echo $ksSmtpPass?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie den <a href="<?php echo KALPFAD?>kalCronJob.php?kal=<?php echo KAL_Schluessel?>"  target="hilfe" onclick="hlpWin(this.href);return false;" title="kalCronJob.php">Cron-Job</a> verwenden
und der ausführende Server keinen Report über den Lauf des Cron-Jobs zustellt kann der Cron-Job selbst eine Nachricht mit einem Report versenden, sofern eine nennenswerte Aktion stattfand.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Cron-Job E-Mail</td>
 <td><input type="checkbox" class="admCheck" name="CronMail" value="1"<?php if($ksCronMail) echo ' checked="checked"'?> /> versenden &nbsp; <span class="admMini">Empfehlung: normalerwiese nicht notwendig</span></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fSetzSmtpNoTLS($w){
 global $sWerte, $ksSmtpTLS;
 if($w!=SMTP_No_TLS){
  $p=strpos($sWerte,"SMTP_No_TLS',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,"SMTP_No_TLS',".($w?'true':'false'),$p,$e-$p); $ksSmtpTLS=!$w; return true;
  }else return false;
 }else return false;
}
?>