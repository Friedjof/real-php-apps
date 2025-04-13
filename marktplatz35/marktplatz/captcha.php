<?php
header('Content-Type: text/plain; charset=utf-8');
header('Content-Transfer-Encoding: 8bit');

if(file_exists('./mpWerte.php')) include('./mpWerte.php');
date_default_timezone_set(defined('MP_TimeZoneSet')&&MP_TimeZoneSet>''?MP_TimeZoneSet:'Europe/Berlin');

$sCapTyp=(isset($_GET['cod'])?substr($_GET['cod'],0,1):'N');
require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php');
$Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(MP_CaptchaTxFarb,MP_CaptchaHgFarb);

echo $sCapTyp.$Cap->PublicKey.iconv('ISO-8859-1','UTF-8',$Cap->Question)."\n";
?>
