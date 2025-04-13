<?php
include 'hilfsFunktionen.php';

if(!$sId=session_id()) $sId=(isset($_COOKIE['PHPSESSID'])?$_COOKIE['PHPSESSID']:'');
$sName=''; $sPass='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['login'])){
  $sName=stripslashes(strip_tags(trim($_POST['name'])));
  $sPass=stripslashes(strip_tags(trim($_POST['pass'])));
  if(strtolower($sName)==ADF_Author&&$sPass==fFraDeCode(ADF_AuthPass)){//Zugangsdaten OK
   if(!isset($_SERVER['REMOTE_ADDR'])||!($sIp=$_SERVER['REMOTE_ADDR'])) $sIp='??';
   if(!isset($_SERVER['HTTP_USER_AGENT'])||!($sUserAgent=$_SERVER['HTTP_USER_AGENT'])){
    $sUserAgent=' '.(isset($_SERVER['ALL_HTTP'])?$_SERVER['ALL_HTTP']:'@');
    if($p=strpos($sUserAgent,'HTTP_USER_AGENT')){
     $sUserAgent=trim(substr($sUserAgent,$p+16));
     if($p=strpos(strtoupper($sUserAgent),'HTTP_')) $sUserAgent=rtrim(substr($sUserAgent,0,$p-1));
    }else $sUserAgent='???';
   }
   $_SESSION['Id']=md5($sId);
   $_SESSION['Ip']=md5($sIp);
   $_SESSION['Ua']=md5($sUserAgent);
   header('Location: '.$sAwww.'/index.php');
   exit;
  }
 }elseif(isset($_POST['logout'])) $_SESSION['Id']='00';
}

echo fSeitenKopf('Anmeldung','','Log');

if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 if(!isset($_SESSION['Id'])||$_SESSION['Id']!=md5($sId)){
  echo '<p class="admMeld" style="text-align:center">Weisen Sie sich als berechtigter Author aus.</p>';
  echo fLoginForm();
 }else{
  echo '<p class="admMeld" style="text-align:center">Wollen Sie sich wirklich abmelden?</p>';
  echo fLogoutForm();
 }
}else{//POST
 echo '<p class="admFehl" style="text-align:center">Bitte geben Sie passende Zugangsdaten ein.</p>';
 echo fLoginForm($sName);
}//POST
?>

<div class="admBox">
<p><u>Hinweis</u>: Sie haben momentan den scriptbasierten Zugangsschutz
zum Autorenbereich eingeschaltet und verwenden ausdrücklich nicht
den als sicher geltenden serverseitigen Zugangsschutz zum Autorenordner.</p>
<?php if(!isset($_COOKIE['PHPSESSID'])){?>
<p>Für das scriptbasierte Sitzungsmanagement sind Cookies nötig.
Ihr Browser nimmt aber offensichtlich keine Sitzungs-Cookies an.
Somit wird die Anmeldung als Autor wahrscheinlich nicht funktionieren.
Aktivieren Sie in Ihrem Browser die Annahme von Sitzungs-Cookies.</p>
<?php } ?>
</div>

<?php
echo fSeitenFuss();

function fLoginForm($sN=''){
 $X='
<form action="autorenLogin.php" method="post">
<input type="hidden" name="login" value="1">
<div align="center">
<table class="admTabl" style="width:1%;" align="center" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td>Autor</td>
<td><input style="width:12em" type="text" name="name" value="'.$sN.'">
</td>
</tr><tr class="admTabl">
<td>Passwort</td>
<td><input style="width:12em" type="password" name="pass"></td>
</tr>
</table>
</div>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Anmelden"></p>
</form>
';
 return $X;
}
function fLogoutForm(){
 $X='
<form action="autorenLogin.php" method="post">
<input type="hidden" name="logout" value="1">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Abmelden"></p>
</form>
';
 return $X;
}
?>