<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Allgemeine Einstellungen','','KEm'); $mpSmtpTLS=(SMTP_No_TLS?'0':'1');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Stellen Sie den E-Mail-Betrieb des Marktplatz-Scripts passend ein.'; $MTyp='Meld';
 $mpMailTo=MP_MailTo; $mpMailFrom=MP_MailFrom;
 $mpSmtp=MP_Smtp; $mpSmtpHost=MP_SmtpHost; $mpSmtpPort=MP_SmtpPort;
 $mpSmtpAuth=MP_SmtpAuth; $mpSmtpUser=MP_SmtpUser; $mpSmtpPass=MP_SmtpPass;
 $mpEnvelopeSender=MP_EnvelopeSender; $mpKeineAntwort=MP_KeineAntwort; $mpCronMail=MP_CronMail;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('MailTo'); if(fSetzMPWert($v,'MailTo',"'")) $bNeu=true;
 $v=txtVar('MailFrom'); if(fSetzMPWert($v,'MailFrom',"'")) $bNeu=true;
 $v=(int)txtVar('Smtp'); if(fSetzMPWert(($v?true:false),'Smtp',"'")) $bNeu=true;
 $v=txtVar('SmtpHost'); if(fSetzMPWert($v,'SmtpHost',"'")) $bNeu=true;
 $v=(int)txtVar('SmtpPort'); if(fSetzMPWert($v,'SmtpPort','')) $bNeu=true;
 $v=(int)txtVar('SmtpTLS'); if(fSetzSmtpNoTLS($v?false:true)) $bNeu=true;
 $v=(int)txtVar('SmtpAuth'); if(fSetzMPWert(($v?true:false),'SmtpAuth','')) $bNeu=true;
 $v=txtVar('SmtpUser'); if(fSetzMPWert($v,'SmtpUser',"'")) $bNeu=true;
 $v=txtVar('SmtpPass'); if(fSetzMPWert($v,'SmtpPass',"'")) $bNeu=true;
 $v=txtVar('EnvelopeSender'); if(fSetzMPWert($v,'EnvelopeSender',"'")) $bNeu=true;
 $v=txtVar('KeineAntwort'); if(fSetzMPWert($v,'KeineAntwort',"'")) $bNeu=true;
 $v=(int)txtVar('CronMail'); if(fSetzMPWert(($v?true:false),'CronMail','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Maileinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Einstellungen bleiben unverändert.'; $MTyp='Meld';}
}
//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form action="konfEmail.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Inserateeintragungen durch Besucher und bei Verwendung der Informationsfunktion, Erinnerungsfunktion, Benachrichtigungsfunktion und Benutzerfreischaltung werden E-Mail-Nachrichten versandt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Empfängeradresse<br />des Webmasters</td>
 <td><input type="text" name="MailTo" value="<?php echo $mpMailTo?>" size="90" style="width:99%" />
 <div class="admMini">reine E-Mail-Adresse name@domain.de <i>ohne</i> Real-Namen (wird nirgends öffentlich gemacht)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Absenderadresse</td>
 <td><input type="text" name="MailFrom" value="<?php echo $mpMailFrom?>" size="90" style="width:99%" />
 <div class="admMini">Absendernamen und E-Mail-Adresse möglich in der Form: Absender &lt;name@domain.de&gt;</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">keine Antwort</td>
 <td><div><input type="text" name="KeineAntwort" value="<?php echo $mpKeineAntwort?>" style="width:99%" /></div>
 <div class="admMini">Format: noreply@domain.de &nbsp; oder Format: Niemand &lt;noreply@domain.de&gt;</div></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Mailtransport</td>
 <td><input type="radio" class="admRadio" name="Smtp" value="0"<?php if(!$mpSmtp){echo ' checked="checked"'; $sSmtpStyle='color:#888;';}?> /> per PHP-mail()-Funktion &nbsp;
 <input type="radio" class="admRadio" name="Smtp" value="1"<?php if($mpSmtp){echo ' checked="checked"'; $sSmtpStyle='';}?> /> über einen SMTP-Server
 <div class="admMini">Standard: PHP-mail() &nbsp; (nur dann SMTP verwenden, wenn PHP-mail() nicht zur Verfügung steht)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls PHP-mail() aktiviert ist muss in seltenen Fällen folgender Parameter gesetzt sein:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Envelope-<br>Absenderadresse</td>
 <td><input type="text" name="EnvelopeSender" value="<?php echo $mpEnvelopeSender?>" size="90" style="width:99%" />
 <div class="admMini">leer lassen (nur ausfüllen mit reiner E-Mail-Adresse name@domain.de wenn Ihr Provider eine Envelope-Absenderadresse als sendmail-Parameter -f ausdrücklich verlangt)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls SMTP aktiviert ist müssen die folgenden Parameter gesetzt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div style="height:2.0em;">SMTP-Host</div><div style="height:3.2em;">SMTP-Port</div><div style="height:1.8em;">Authentifizierung</div><div style="height:1.8em;">SMTP-Benutzer</div><div style="height:1.8em;">SMTP-Passwort</div></td>
 <td><input type="text" name="SmtpHost" value="<?php echo $mpSmtpHost?>" style="width:250px;<?php echo $sSmtpStyle?>" />
 <div><input type="text" name="SmtpPort" value="<?php echo $mpSmtpPort?>" style="width:32px;<?php echo $sSmtpStyle?>" /> <span class="admMini">(Standard: 25)</span></div>
 <div><input type="checkbox" class="admCheck" name="SmtpTLS" value="1"<?php if($mpSmtpTLS) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> TLS-Verschlüsselung verwenden (soweit vom Server angeboten)</div>
 <div style="margin-top:4px"><input type="checkbox" class="admCheck" name="SmtpAuth" value="1"<?php if($mpSmtpAuth) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> Authentifizieren am SMTP-Server mit folgenden Daten:</div>
 <div><input type="text" name="SmtpUser" value="<?php echo $mpSmtpUser?>" style="width:250px;<?php echo $sSmtpStyle?>" /></div>
 <div><input type="text" name="SmtpPass" value="<?php echo $mpSmtpPass?>" style="width:250px;<?php echo $sSmtpStyle?>" /></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie den <a href="<?php echo $sHttp?>mpCronJob.php?mp=<?php echo MP_Schluessel?>" target="hilfe" onclick="hlpWin(this.href);return false;" title="mpCronJob.php">Cron-Job</a> verwenden
und der ausführende Server keinen Report über den Lauf des Cron-Jobs zustellt kann der Cron-Job selbst eine Nachricht mit einem Report versenden, sofern eine nennenswerte Aktion stattfand.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Cron-Job E-Mail</td>
 <td><input type="checkbox" class="admCheck" name="CronMail" value="1"<?php if($mpCronMail) echo ' checked="checked"'?> /> versenden &nbsp; <span class="admMini">Empfehlung: normalerwiese nicht notwendig</span></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php
echo fSeitenFuss();

function fSetzSmtpNoTLS($w){
 global $sWerte, $mpSmtpTLS;
 if($w!=SMTP_No_TLS){
  $p=strpos($sWerte,"SMTP_No_TLS',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,"SMTP_No_TLS',".($w?'true':'false'),$p,$e-$p); $mpSmtpTLS=!$w; return true;
  }else return false;
 }else return false;
}
?>