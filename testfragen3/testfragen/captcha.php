<?php
header('Content-Type: text/plain; charset=utf-8');
header('Content-Transfer-Encoding: 8bit');

@include('./fraWerte'.(defined('FRA_Ablauf')&&FRA_Ablauf>0?FRA_Ablauf:'').'.php');
date_default_timezone_set(defined('FRA_TimeZoneSet')&&FRA_TimeZoneSet>''?FRA_TimeZoneSet:'Europe/Berlin');

$sCapTyp=(isset($_GET['cod'])?substr($_GET['cod'],0,1):'N');
require_once(FRA_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php');
$Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(FRA_CaptchaTxFarb,FRA_CaptchaHgFarb);

echo $sCapTyp.$Cap->PublicKey.iconv('ISO-8859-1','UTF-8//TRANSLIT',$Cap->Question)."\n";
?>