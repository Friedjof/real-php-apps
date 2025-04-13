<?php
error_reporting(E_ALL);
$sUmfSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./umfrage.php')));
$sUmfHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$sUmfQS=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
$sUmfHtmlVor=''; $sUmfHtmlNach=''; $bUmfOK=true; $sUmfQry=''; $sUmfHid=''; //Seitenkopf, Seitenfuﬂ, Status

if(!strstr($sUmfSelf,'/umfrage.php')){ //includierter Aufruf
 if(!defined('UMF_Version')){
  $bUmfOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>umfWerte.php</i> wurde nicht per include eingebunden!</b></p>';
 }else define('UMF_Url',$sUmfHttp.UMF_Www);
}else{ //Script laeuft allein als umfrage.php
 $umfAblauf=(isset($_GET['umf_Ablauf'])?(int)$_GET['umf_Ablauf']:(isset($_POST['umf_Ablauf'])?(int)$_POST['umf_Ablauf']:''));
 @include('umfWerte'.$umfAblauf.'.php'); define('UMF_Ablauf',$umfAblauf);
 if(defined('UMF_Version')){
  header('Content-Type: text/html; charset='.(UMF_Zeichensatz!=2?'ISO-8859-1':'utf-8')); define('UMF_Url',$sUmfHttp.UMF_Www);
  $sUmfCss=(defined('UMF_CSSDatei')?UMF_CSSDatei:'umfStyle.css'); if(!file_exists(UMF_Pfad.$sUmfCss)) $sUmfCss='umfStyle.css';
  if(strlen(UMF_Schablone)>0){ //mit Seitenschablone
   if(file_exists(UMF_Pfad.UMF_Schablone)) $sUmfHtmlNach=@implode('',@file(UMF_Pfad.UMF_Schablone));
   if($p=strpos($sUmfHtmlNach,'{Inhalt}')){
    $sUmfHtmlVor=substr($sUmfHtmlNach,0,$p); $sUmfHtmlNach=substr($sUmfHtmlNach,$p+8); //Seitenkopf, Seitenfuﬂ
    if($sUmfCss!='umfStyle.css') if($p=strpos($sUmfHtmlVor,'umfStyle.css')) $sUmfHtmlVor=substr_replace($sUmfHtmlVor,$sUmfCss,$p,12);
   }else{$sUmfHtmlVor='<p style="color:red">HTML-Layout-Schablone <i>'.UMF_Schablone.'</i> nicht gefunden oder fehlerhaft!</p>'; $sUmfHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.UMF_Url.$sUmfCss.'">'."\n\n";
  }
 }else{$bUmfOK=false; echo "\n".'<p style="color:red">Konfiguration <i>umfWerte'.$umfAblauf.'.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bUmfOK){ //Konfiguration eingelesen
 if(!UMF_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED); if(UMF_SQL) mysqli_report(MYSQLI_REPORT_OFF);
 if(strlen(UMF_TimeZoneSet)>0) date_default_timezone_set(UMF_TimeZoneSet);
 //geerbte GET/POST-Parameter aufbewahren und einige Ablaufparameter ermitteln
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  $sUmfAktion=(isset($_GET['umf_Aktion'])?$_GET['umf_Aktion']:'frage'); //Erstaufruf ohne Aktion
  if(!isset($_GET['umf_Session'])&&substr($sUmfAktion,0,2)!='ok'){
   if($sUmfAktion!='grafik'||!defined('UMF_GrafikOhneLogin')||!UMF_GrafikOhneLogin){
    if(UMF_Nutzerverwaltung=='vorher') $sUmfAktion='login';
    elseif(UMF_Registrierung=='vorher') $sUmfAktion='erfassen';
   }
  }
  $sUmfUmfrage=(isset($_GET['umf_Umfrage'])?strtoupper(substr($_GET['umf_Umfrage'],0,1)):'');
  define('UMF_Antwort',(isset($_GET['umf_Antwort'])?$_GET['umf_Antwort']:''));
  define('UMF_Session',(isset($_GET['umf_Session'])&&$sUmfAktion!='login'?$_GET['umf_Session']:''));
  reset($_GET);
  foreach($_GET as $sUmfK=>$sUmfV) if(substr($sUmfK,0,4)!='umf_'){
   $sUmfQry.='&amp;'.$sUmfK.'='.rawurlencode($sUmfV);
   $sUmfHid.='<input type="hidden" name="'.$sUmfK.'" value="'.$sUmfV.'" />';
  }
 }else{ //bei POST
  $sUmfAktion=(isset($_POST['umf_Aktion'])?$_POST['umf_Aktion']:'frage'); //Aufruf ohne Aktion
  $sUmfUmfrage=(isset($_POST['umf_Umfrage'])?strtoupper(substr($_POST['umf_Umfrage'],0,1)):'');
  define('UMF_Antwort',(isset($_POST['umf_Antwort'])?$_POST['umf_Antwort']:''));
  define('UMF_Session',(isset($_POST['umf_Session'])?$_POST['umf_Session']:''));
  if(isset($_POST['umf_Gespeichert'])) define('UMF_Gespeichert',(int)$_POST['umf_Gespeichert']);
  reset($_POST); $aUmfQS=(empty($sUmfQS)?NULL:explode('&',$sUmfQS)); $aUmfQKeys=array();
  if(is_array($aUmfQS)) foreach($aUmfQS as $sUmfQS) if(substr($sUmfQS,0,4)!='umf_') if(is_string($sUmfQS)){
   if(!$nUmfP=strpos($sUmfQS,'=')) !$nUmfP=strlen($sUmfQS);
   $sUmfQry.='&amp;'.$sUmfQS; $aUmfQKeys[]=rawurldecode(substr($sUmfQS,0,$nUmfP));
   $sUmfHid.='<input type="hidden" name="'.rawurldecode(substr($sUmfQS,0,$nUmfP)).'" value="'.rawurldecode(substr($sUmfQS,$nUmfP+1)).'" />';
  }
  foreach($_POST as $sUmfK=>$sUmfV) if(substr($sUmfK,0,4)!='umf_'&&!in_array($sUmfK,$aUmfQKeys)){
   $sUmfQry.='&amp;'.$sUmfK.'='.rawurlencode($sUmfV);
   $sUmfHid.='<input type="hidden" name="'.$sUmfK.'" value="'.$sUmfV.'" />';
  }
 }
 if(!empty($umfAblauf)){$sUmfQry.='&amp;umf_Ablauf='.$umfAblauf; $sUmfHid='<input type="hidden" name="umf_Ablauf" value="'.$umfAblauf.'" />'.$sUmfHid;}
 define('UMF_Self',$sUmfSelf.(strlen($sUmfQry)!=0?$sUmfQry='?'.substr($sUmfQry,5):''));
 define('UMF_Qry',$sUmfQry); define('UMF_Hidden',$sUmfHid); define('UMF_Umfrage',$sUmfUmfrage);

 //Aktionen - Programmverteiler
 switch($sUmfAktion){
  case 'frage': include UMF_Pfad.'umfFrage.php'; break;
  case 'grafik': include UMF_Pfad.'umfGrafik.php'; break;    //Auswertegrafik
  case 'login': include UMF_Pfad.'umfLogin.php'; break;      //Benutzerlogin
  case 'erfassen': include UMF_Pfad.'umfErfassen.php'; break;//Teilnehmer erfassen
  case 'zentrum': include UMF_Pfad.'umfZentrum.php'; break;  //Benutzerzentrum
  case 'auswahl': include UMF_Pfad.'umfAuswahl.php'; break;  //Auswahlzentrum fuer Teilnehmer
  case 'benutzer': include UMF_Pfad.'umfNutzerDaten.php'; break; //Benutzerdaten editieren
  case 'ergebnis': include UMF_Pfad.'umfErgebnis.php'; break; //Ergebnis
  case 'drucken': include UMF_Pfad.'umfDrucken.php'; break;
  default: if(substr($sUmfAktion,0,2)=='ok') include UMF_Pfad.'umfFreischalt.php'; //Freischaltung
 }

 //Beginn der Ausgabe
 echo $sUmfHtmlVor."\n".'<div class="umfBox">'."\n"; include(UMF_Pfad.'umfVersion.php');
 if(UMF_Version!=$umfVersion||strlen(UMF_Www)==0) echo "\n".'<p class="umfFehl">'.fUmfTx(UMF_TxSetupFehlt).'</p>'."\n";

 //Seiteninhalt
 echo fUmfSeite()."\n";

 //Ende der Ausgabebox und evt. Seitenfuﬂ
 echo "\n".'</div>'."\n".$sUmfHtmlNach;
}
echo "\n";

function fUmfTx($sTx){ //TextKodierung
 if(UMF_Zeichensatz<=0) $s=$sTx; elseif(UMF_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
function fUmfBB($s){ //BB-Code zu HTML
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.($o*14+100).'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="umfText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="umfText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="umfText" target="_blank" href="'.(substr($v,$o,4)!='http'?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="umfText" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="umfText"><li class="umfText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="umfText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>