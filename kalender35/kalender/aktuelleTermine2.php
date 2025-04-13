<?php
// error_reporting(E_ALL);
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./aktuelleTermine.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$sKalHtmlVor=''; $sKalHtmlNach=''; $bKalOK=true; //Seitenkopf, Seitenfuss, Status

if(!strstr($sKalSelf,'/aktuelleTermine.php')){ //includierter Aufruf
 if(defined('KAL_Version')){ //Variablen includiert
  if(!defined('KAL_AktuelleZiel')) define('KAL_AktuelleZiel',(KAL_AktuelleLink!=''?KAL_AktuelleLink:$sKalSelf));
 }else{ //Variablen nicht includiert
  $bKalOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>kalWerte.php</i> wurde nicht includiert!</b></p>';
 }
}else{//Script laeuft allein als aktuelleTermine.php
 @include('kalWerte.php'); if(!defined('KAL_AktuelleZiel')) define('KAL_AktuelleZiel',(KAL_AktuelleLink==''?'kalender.php':KAL_AktuelleLink));
 if(defined('KAL_Version')){
  header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(KAL_Schablone){ //mit Seitenschablone
   $sKalHtmlNach=@implode('',@file(KAL_Pfad.'aktuelleTermine.htm'));
   if($nKalJ=strpos($sKalHtmlNach,'{Inhalt}')){
    $sKalHtmlVor=substr($sKalHtmlNach,0,$nKalJ); $sKalHtmlNach=substr($sKalHtmlNach,$nKalJ+8); //Seitenkopf, Seitenfuﬂ
   }else{$sKalHtmlVor='<p style="color:#AA0033;">Layout-Schablone <i>aktuelleTermine.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.$sKalHttp.KAL_Www.'kalStyles.css">'."\n\n";
  }
 }else{$bKalOK=false; echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bKalOK){ //Konfiguration eingelesen
 if(!KAL_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if(!defined('KAL_Url')) define('KAL_Url',$sKalHttp.KAL_Www); if(!defined('KAL_AktuelleSelf')) define('KAL_AktuelleSelf',$sKalSelf);
 //geerbte GET/POST-Parameter aufbewahren und einige Kalenderparameter ermitteln
 $sKalQry=''; $sKalHid=''; $sKalSession=''; $sKalSuchParam=''; $sKalIndex=''; $sKalRueck=''; $sKalStart=''; $sKalNummer='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['kal_Aktion'])) $sKalAktion=fKalRqA($_GET['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_GET['kal_Session'])&&$sKalAktion!='login') $sKalSession='&amp;kal_Session='.fKalRqA($_GET['kal_Session']);
  reset($_GET);
  if(!defined('KAL_Query')) foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'" />';
  }
 }else{ //bei POST
  if(isset($_POST['kal_Session'])&&$_POST['kal_Session']!='') $sKalSession='&amp;kal_Session='.fKalRqA($_POST['kal_Session']);
  reset($_POST);
  if(!defined('KAL_Query')) foreach($_POST as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'" />';
  }
 }
 if(!defined('KAL_Query')) define('KAL_Query',$sKalQry); if(!defined('KAL_Hidden')) define('KAL_Hidden',$sKalHid);
 if(!defined('KAL_Session')) define('KAL_Session',$sKalSession);

 //Beginn der Ausgabe
 echo $sKalHtmlVor;
 if((KAL_DetailPopup||KAL_MailPopup)&&!defined('KAL_KalWin')){
  echo "\n".'<script type="text/javascript">function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  if(!defined('KAL_KalWin')) define('KAL_KalWin',true);
 }
 if(KAL_AktuellePopup&&(KAL_AktuelleTarget!='_self'||KAL_AktuelleTarget!='_parent'||KAL_AktuelleTarget!='_top')){
  echo "\n".'<script type="text/javascript">function AktWin(sURL){kalWin=window.open(sURL,"'.KAL_AktuelleTarget.'","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  if(!defined('KAL_AktuOnClk')) define('KAL_AktuOnClk','" onclick="AktWin(this.href);return false;');
 }else if(!defined('KAL_AktuOnClk')) define('KAL_AktuOnClk','');

 echo "\n".'<div class="kalBox">'."\n";
 echo fKalAktuelle();
 echo "\n".'</div>'."\n".$sKalHtmlNach;
}
echo "\n";
?>