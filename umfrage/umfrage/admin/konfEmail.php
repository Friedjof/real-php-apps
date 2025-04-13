<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('E-Mail-Einstellungen','','SEm'); $usSmtpTLS=(SMTP_No_TLS?'0':'1');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $sMeld='<p class="admMeld">Stellen Sie den E-Mail-Betrieb des Umfrage-Scripts passend ein.</p>';
 $usEmpfaenger=UMF_Empfaenger; $usSender=UMF_Sender; $usEnvelopeSender=UMF_EnvelopeSender;
 $usSmtp=UMF_Smtp; $usSmtpHost=UMF_SmtpHost; $usSmtpPort=UMF_SmtpPort;
 $usSmtpAuth=UMF_SmtpAuth; $usSmtpUser=UMF_SmtpUser; $usSmtpPass=UMF_SmtpPass;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('Empfaenger'); if(fSetzUmfWert($v,'Empfaenger',"'")) $bNeu=true;
  $v=txtVar('Sender'); if(fSetzUmfWert($v,'Sender',"'")) $bNeu=true;
  $v=txtVar('EnvelopeSender'); if(fSetzUmfWert($v,'EnvelopeSender',"'")) $bNeu=true;
  $v=(int)txtVar('Smtp'); if(fSetzUmfWert(($v?true:false),'Smtp','')) $bNeu=true;
  $v=txtVar('SmtpHost'); if(fSetzUmfWert($v,'SmtpHost',"'")) $bNeu=true;
  $v=(int)txtVar('SmtpPort'); if(fSetzUmfWert($v,'SmtpPort','')) $bNeu=true;
  $v=(int)txtVar('SmtpTLS'); if(fSetzSmtpNoTLS($v?false:true)) $bNeu=true;
  $v=(int)txtVar('SmtpAuth'); if(fSetzUmfWert(($v?true:false),'SmtpAuth','')) $bNeu=true;
  $v=txtVar('SmtpUser'); if(fSetzUmfWert($v,'SmtpUser',"'")) $bNeu=true;
  $v=txtVar('SmtpPass'); if(fSetzUmfWert($v,'SmtpPass',"'")) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden (Rechteproblem)!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die E-Mail-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die E-Mail-Einstellungen bleiben unverändert.</p>';
}//POST

//Scriptausgabe
echo $sMeld.NL;
?>

<form name="emailform" action="konfEmail.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Umfragenabschluss, Benutzerfreischaltung usw. werden E-Mail-Nachrichten versandt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Empfang</td>
 <td><input type="text" name="Empfaenger" value="<?php echo $usEmpfaenger?>" style="width:210px;" /> E-Mail-Adresse des Webmasters &nbsp; <span class="admMini">(Wird nirgends veröffentlicht!)</span>
 <div class="admMini">Mehrere Adressen durch Komma getrennt - das funktioniert jedoch nicht mit Garantie auf jedem Server!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Absender</td>
 <td><div style="float:left;width:330px;"><input type="text" name="Sender" value="<?php echo $usSender?>" style="width:330px;" /></div>
 <div class="admMini" style="margin-left:335px;">Format: absender@domain.de &nbsp; oder<br />Format: Absender &lt;absender@domain.de&gt;</div></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Mailtransport</td>
 <td><input type="radio" class="admRadio" name="Smtp" value="0"<?php if(!$usSmtp){echo ' checked="checked"'; $sSmtpStyle='color:#888;';}?> /> per PHP-mail()-Funktion &nbsp;
 <input type="radio" class="admRadio" name="Smtp" value="1"<?php if($usSmtp){echo ' checked="checked"'; $sSmtpStyle='';}?> /> über einen SMTP-Server
 <div class="admMini">Standard: PHP-mail() &nbsp; (nur dann SMTP verwenden, wenn PHP-mail() nicht zur Verfügung steht)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls PHP-mail() aktiviert ist muss in seltenen Fällen folgender Parameter gesetzt sein:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Envelope-<br />Absenderadresse</td>
 <td><div><input type="text" name="EnvelopeSender" value="<?php echo $usEnvelopeSender?>" style="width:330px;" /></div>
 <div class="admMini">leer lassen (nur ausfüllen mit reiner E-Mail-Adresse name@domain.de wenn Ihr Provider eine Envelope-Absenderadresse als sendmail-Parameter -f ausdrücklich verlangt)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls SMTP aktiviert ist müssen die folgenden Parameter gesetzt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div style="height:2.0em;">SMTP-Host</div><div style="height:3.2em;">SMTP-Port</div><div style="height:1.8em;">Authentifizierung</div><div style="height:1.8em;">SMTP-Benutzer</div><div style="height:1.8em;">SMTP-Passwort</div></td>
 <td><input type="text" name="SmtpHost" value="<?php echo $usSmtpHost?>" style="width:330px;<?php echo $sSmtpStyle?>" />
 <div><input type="text" name="SmtpPort" value="<?php echo $usSmtpPort?>" style="width:32px;<?php echo $sSmtpStyle?>" /> <span class="admMini">(Standard: 25)</span></div>
 <div><input type="checkbox" class="admCheck" name="SmtpTLS" value="1"<?php if($usSmtpTLS) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> TLS-Verschlüsselung verwenden (soweit vom Server angeboten)</div>
 <div style="margin-top:4px"><input type="checkbox" class="admCheck" name="SmtpAuth" value="1"<?php if($usSmtpAuth) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> Authentifizieren am SMTP-Server mit folgenden Daten:</div>
 <div><input type="text" name="SmtpUser" value="<?php echo $usSmtpUser?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div>
 <div><input type="text" name="SmtpPass" value="<?php echo $usSmtpPass?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fSetzSmtpNoTLS($w){
 global $sWerte, $usSmtpTLS;
 if($w!=SMTP_No_TLS){
  $p=strpos($sWerte,"SMTP_No_TLS',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,"SMTP_No_TLS',".($w?'true':'false'),$p,$e-$p); $usSmtpTLS=!$w; return true;
  }else return false;
 }else return false;
}
?>