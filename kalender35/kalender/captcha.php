<?php
header('Content-Type: text/plain; charset=utf-8');
header('Content-Transfer-Encoding: 8bit');

@include('./kalWerte.php');
date_default_timezone_set(defined('KAL_TimeZoneSet')&&KAL_TimeZoneSet>''?KAL_TimeZoneSet:'Europe/Berlin');

$sCapTyp=(isset($_GET['cod'])?substr($_GET['cod'],0,1):'N');
require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php');
$Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);

echo $sCapTyp.$Cap->PublicKey.iconv('ISO-8859-1','UTF-8',$Cap->Question)."\n";
?>
