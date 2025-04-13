<?php
header('Content-Type: text/plain; charset=utf-8');
header('Content-Transfer-Encoding: 8bit');

@include('./umfWerte'.(defined('UMF_Ablauf')&&UMF_Ablauf>0?UMF_Ablauf:'').'.php');
date_default_timezone_set(defined('UMF_TimeZoneSet')&&UMF_TimeZoneSet>''?UMF_TimeZoneSet:'Europe/Berlin');

$sCapTyp=(isset($_GET['cod'])?substr($_GET['cod'],0,1):'N');
require_once(UMF_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php');
$Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);

echo $sCapTyp.$Cap->PublicKey.iconv('ISO-8859-1','UTF-8',$Cap->Question)."\n";
?>
