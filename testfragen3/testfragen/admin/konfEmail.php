<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('E-Mail-Einstellungen','','KEm'); $fsSmtpTLS=(SMTP_No_TLS?'0':'1');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $sMeld='<p class="admMeld">Stellen Sie den E-Mail-Betrieb des Testfragen-Scripts passend ein.</p>';
 $fsEmpfaenger=FRA_Empfaenger; $fsSender=FRA_Sender; $fsEnvelopeSender=FRA_EnvelopeSender;
 $fsSmtp=FRA_Smtp; $fsSmtpHost=FRA_SmtpHost; $fsSmtpPort=FRA_SmtpPort;
 $fsSmtpAuth=FRA_SmtpAuth; $fsSmtpUser=FRA_SmtpUser; $fsSmtpPass=FRA_SmtpPass;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('Empfaenger'); if(fSetzFraWert($v,'Empfaenger',"'")) $bNeu=true;
  $v=txtVar('Sender'); if(fSetzFraWert($v,'Sender',"'")) $bNeu=true;
  $v=txtVar('EnvelopeSender'); if(fSetzFraWert($v,'EnvelopeSender',"'")) $bNeu=true;
  $v=(int)txtVar('Smtp'); if(fSetzFraWert(($v?true:false),'Smtp','')) $bNeu=true;
  $v=txtVar('SmtpHost'); if(fSetzFraWert($v,'SmtpHost',"'")) $bNeu=true;
  $v=(int)txtVar('SmtpPort'); if(fSetzFraWert($v,'SmtpPort','')) $bNeu=true;
  $v=(int)txtVar('SmtpTLS'); if(fSetzSmtpNoTLS($v?false:true)) $bNeu=true;
  $v=(int)txtVar('SmtpAuth'); if(fSetzFraWert(($v?true:false),'SmtpAuth','')) $bNeu=true;
  $v=txtVar('SmtpUser'); if(fSetzFraWert($v,'SmtpUser',"'")) $bNeu=true;
  $v=txtVar('SmtpPass'); if(fSetzFraWert($v,'SmtpPass',"'")) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
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
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei Testabschluss, Benutzerfreischaltung usw. werden E-Mail-Nachrichten versandt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Empfang</td>
 <td><input type="text" name="Empfaenger" value="<?php echo $fsEmpfaenger?>" style="width:210px;" /> E-Mail-Adresse des Webmasters &nbsp; <span class="admMini">(Wird nirgends veröffentlicht!)</span>
 <div class="admMini">Mehrere Adressen durch Komma getrennt - das funktioniert jedoch nicht mit Garantie auf jedem Server!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Absender</td>
 <td><div style="float:left;width:330px;"><input type="text" name="Sender" value="<?php echo $fsSender?>" style="width:330px;" /></div>
 <div class="admMini" style="margin-left:335px;">Format: absender@domain.de &nbsp; oder<br />Format: Absender &lt;absender@domain.de&gt;</div></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Mailtransport</td>
 <td><input type="radio" class="admRadio" name="Smtp" value="0"<?php if(!$fsSmtp){echo ' checked="checked"'; $sSmtpStyle='color:#888;';}?> /> per PHP-mail()-Funktion &nbsp;
 <input type="radio" class="admRadio" name="Smtp" value="1"<?php if($fsSmtp){echo ' checked="checked"'; $sSmtpStyle='';}?> /> über einen SMTP-Server
 <div class="admMini">Standard: PHP-mail() &nbsp; (nur dann SMTP verwenden, wenn PHP-mail() nicht zur Verfügung steht)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls PHP-mail() aktiviert ist muss in seltenen Fällen folgender Parameter gesetzt sein:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Envelope-<br />Absenderadresse</td>
 <td><div><input type="text" name="EnvelopeSender" value="<?php echo $fsEnvelopeSender?>" style="width:330px;" /></div>
 <div class="admMini">leer lassen (nur ausfüllen mit reiner E-Mail-Adresse name@domain.de wenn Ihr Provider eine Envelope-Absenderadresse als sendmail-Parameter -f ausdrücklich verlangt)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls SMTP aktiviert ist müssen die folgenden Parameter gesetzt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div style="height:2.0em;">SMTP-Host</div><div style="height:3.2em;">SMTP-Port</div><div style="height:1.8em;">Authentifizierung</div><div style="height:1.8em;">SMTP-Benutzer</div><div style="height:1.8em;">SMTP-Passwort</div></td>
 <td><input type="text" name="SmtpHost" value="<?php echo $fsSmtpHost?>" style="width:330px;<?php echo $sSmtpStyle?>" />
 <div><input type="text" name="SmtpPort" value="<?php echo $fsSmtpPort?>" style="width:32px;<?php echo $sSmtpStyle?>" /> <span class="admMini">(Standard: 25)</span></div>
 <div><input type="checkbox" class="admCheck" name="SmtpTLS" value="1"<?php if($fsSmtpTLS) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> TLS-Verschlüsselung verwenden (soweit vom Server angeboten)</div>
 <div style="margin-top:4px"><input type="checkbox" class="admCheck" name="SmtpAuth" value="1"<?php if($fsSmtpAuth) echo ' checked="checked"'; if($sSmtpStyle) echo ' style="'.$sSmtpStyle.'"'?> /> Authentifizieren am SMTP-Server mit folgenden Daten:</div>
 <div><input type="text" name="SmtpUser" value="<?php echo $fsSmtpUser?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div>
 <div><input type="text" name="SmtpPass" value="<?php echo $fsSmtpPass?>" style="width:180px;<?php echo $sSmtpStyle?>" /></div></td>
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
 global $sWerte, $fsSmtpTLS;
 if($w!=SMTP_No_TLS){
  $p=strpos($sWerte,"SMTP_No_TLS',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,"SMTP_No_TLS',".($w?'true':'false'),$p,$e-$p); $fsSmtpTLS=!$w; return true;
  }else return false;
 }else return false;
}
?>