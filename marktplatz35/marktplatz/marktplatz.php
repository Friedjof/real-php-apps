<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF);
$sMpSELF=(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./marktplatz.php')); $sMpSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:$sMpSELF); define('MP_Self',$sMpSelf); // ????
$sMpHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$bMpPopup=isset($_GET['mp_Popup'])||isset($_POST['mp_Popup']);
global $sMpOut; $sMpOut=''; $sMpHtmlVor=''; $sMpHtmlNach=''; $sMpTitel=''; $sMpSession=''; $bMpOK=true; //Seitenkopf, Seitenfuss, Segmentersatz, Status

if(!strstr($sMpSELF,'/marktplatz.php')){ //includierter Aufruf
 if(defined('MP_Version')){ //Variablen includiert
  if(!defined('MP_Url')) define('MP_Url',$sMpHttp.MP_Www);
 }else{$bMpOK=false; $sMpOut="\n".'<p style="color:red"><b>Konfiguration <i>mpWerte.php</i> wurde nicht includiert!</b></p>';}
}else{ //Script laeuft allein als marktplatz.php
 if(file_exists('mpWerte.php')) include('mpWerte.php');
 if(defined('MP_Version')){
  header('Content-Type: text/html; charset='.(MP_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(!defined('MP_Url')) define('MP_Url',$sMpHttp.MP_Www);
  if(MP_Schablone){ //mit Seitenschablone
   $bMpDruck=isset($_GET['mp_Aktion'])&&substr($_GET['mp_Aktion'],0,5)=='druck'; $bMpFrei=false;
   $sMpAktion=fMpRq1(isset($_GET['mp_Aktion'])?$_GET['mp_Aktion']:(isset($_POST['mp_Aktion'])?$_POST['mp_Aktion']:''));
   if(substr($sMpAktion,0,1)=='o') if(MP_FreischaltWin=='Freischalt'){$bMpFrei=true; $bMpPopup=true;}elseif(MP_FreischaltWin=='Popup') $bMpPopup=true;
   if(MP_IndexNormal||($sMpAktion!='index'&&$sMpAktion!='')) $sMpHtmlVor=''; else $sMpHtmlVor='1';
   $sMpHtmlVor=(!$bMpFrei?(!$bMpDruck?(!$bMpPopup?'mpSeite'.$sMpHtmlVor.'.htm':'mpPopup.htm'):'mpDruck.htm'):'mpFreischalt.htm');
   $sMpHtmlNach=@implode('',(file_exists(MP_Pfad.$sMpHtmlVor)?file(MP_Pfad.$sMpHtmlVor):array('')));
   if($nMpJ=strpos($sMpHtmlNach,'{Inhalt}')){
    $sMpHtmlVor=substr($sMpHtmlNach,0,$nMpJ); $sMpHtmlNach=substr($sMpHtmlNach,$nMpJ+8); //Seitenkopf, Seitenfuss
   }else{$sMpHtmlVor='<p style="color:red">HTML-Layout-Schablone <i>'.$sMpHtmlVor.'</i> nicht gefunden oder fehlerhaft!</p>'; $sMpHtmlNach='';}
  }else{ //ohne Seitenschablone
   $sMpOut="\n\n".'<link rel="stylesheet" type="text/css" href="'.MP_Url.'mpStyles.css">'."\n\n";
  }
 }else{$bMpOK=false; $sMpOut="\n".'<p style="color:red">Konfiguration <i>mpWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bMpOK){ //Konfiguration eingelesen
 if(!MP_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet);
 //geerbte GET/POST-Parameter aufbewahren und einige Parameter ermitteln
 $sMpQry=''; $sMpHid=''; $sMpSegment=''; $sMpSuchParam=''; $sMpSuchText=''; $sMpIndex=''; $sMpRueck=''; $sMpSeite=''; $sMpNummer='';
 if(isset($_GET['mp_Segment'])) $sMpSegment=(int)$_GET['mp_Segment']; elseif(isset($_POST['mp_Segment'])) $sMpSegment=(int)$_POST['mp_Segment'];
 if(isset($_GET['mp_Aktion'])) $sMpAktion=fMpRq1($_GET['mp_Aktion']); elseif(isset($_POST['mp_Aktion'])) $sMpAktion=fMpRq1($_POST['mp_Aktion']); else $sMpAktion=($sMpSegment?'liste':'index');
 if(isset($_GET['mp_Session'])&&$sMpAktion!='login') $sMpSession=fMpRq1($_GET['mp_Session']); elseif(isset($_POST['mp_Session'])&&$_POST['mp_Session']!='') $sMpSession=fMpRq1($_POST['mp_Session']);
 if(isset($_GET['mp_Seite'])) $sMpSeite=(int)$_GET['mp_Seite']; elseif(isset($_POST['mp_Seite'])) $sMpSeite=(int)$_POST['mp_Seite']; else $sMpSeite='1';
 if(isset($_GET['mp_Nummer'])) $sMpNummer=fMpRq1($_GET['mp_Nummer']); elseif(isset($_POST['mp_Nummer'])) $sMpNummer=fMpRq1($_POST['mp_Nummer']);

 if($nMpJ=strpos($_SERVER['QUERY_STRING'],'p_Layout')) $sMpLay=substr($_SERVER['QUERY_STRING'],$nMpJ+9,1); else $sMpLay=(isset($_GET['mp_Layout']))?$_GET['mp_Layout']:(isset($_POST['mp_Session'])?$_POST['mp_Session']:'');
 if($sMpLay=="e"||$sMpLay=="t") define('MP_Layout',$sMpLay);

 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['mp_Index'])) $sMpIndex='&amp;mp_Index='.(int)$_GET['mp_Index'];
  if(isset($_GET['mp_Rueck'])) $sMpRueck='&amp;mp_Rueck='.(int)$_GET['mp_Rueck'];
  reset($_GET);
  if(!defined('MP_Query')) foreach($_GET as $sMpK=>$sMpV) if(substr($sMpK,0,3)!='mp_'){
   $sMpQry.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
   $sMpHid.='<input type="hidden" name="'.$sMpK.'" value="'.$sMpV.'" />';
  }elseif(strrpos($sMpK,'F')>3&&strrpos($sMpK,'F')<8) $sMpSuchParam.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
 }else{ //bei POST
  if(isset($_POST['mp_Index'])) $sMpIndex='&amp;mp_Index='.(int)$_POST['mp_Index'];
  if(isset($_POST['mp_Rueck'])) $sMpRueck='&amp;mp_Rueck='.(int)$_POST['mp_Rueck'];
  reset($_POST);
  if(!defined('MP_Query')) foreach($_POST as $sMpK=>$sMpV) if(substr($sMpK,0,3)!='mp_') if(is_string($sMpV)){
   $sMpQry.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
   $sMpHid.='<input type="hidden" name="'.$sMpK.'" value="'.$sMpV.'" />';
  }elseif(strrpos($sMpK,'F')>3&&strrpos($sMpK,'F')<8) $sMpSuchParam.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
 }
 if(!defined('MP_Query')) define('MP_Query',$sMpQry); if(!defined('MP_Hidden')) define('MP_Hidden',$sMpHid); if(!defined('MP_Popup')) define('MP_Popup',$bMpPopup);
 if($sMpSegment){$aMpSN=explode(';',MP_Segmente); $sMpSegName=$aMpSN[$sMpSegment]; if(substr($sMpSegName,0,1)=='*') $sMpSegName=substr($sMpSegName,1) ;} else $sMpSegName='';
 if(!defined('MP_Segment')) define('MP_Segment',$sMpSegment); if(!defined('MP_SegName')) define('MP_SegName',$sMpSegName);
 if(!defined('MP_Session')) define('MP_Session',$sMpSession);

 //Aktionen - Programmverteiler
 $sMpAendAktion='liste'; $sMpAendParam=(MP_ListenAendern<0||(!MP_GastLAendern&&$sMpSession=='')?'&amp;mp_Aendern=1':'');
 $sMpAendParam.=(MP_ListenAendKopieren&&(MP_ListenKopieren<0||(!MP_GastLKopieren&&$sMpSession==''))?'&amp;mp_Kopieren=1':'');
 if($nMpJ=strrpos($sMpAktion,'_')){define('MP_Zusatz',substr($sMpAktion,++$nMpJ,1)); $sMpAktion='zusatz';}
 switch($sMpAktion){
  case 'liste':
   include(MP_Pfad.'mpDaten.php'); fMpDaten(true,true); include(MP_Pfad.'mpListe.php'); break; //Inserateliste
  case 'detail':
   include(MP_Pfad.'mpDaten.php'); fMpDaten(false,true); include(MP_Pfad.'mpDetail.php');
   $sMpAendAktion='aendern'; $sMpAendParam=''; break;
  case 'suchen':
   if(isset($_GET['mp_Such'])) $sMpSuchText=fMpRq($_GET['mp_Such']); elseif(isset($_POST['mp_Such'])) $sMpSuchText=fMpRq($_POST['mp_Such']);
   include(MP_Pfad.'mpListe0.php'); $sMpTitel=MP_TxQuerSuche; break;
  case 'index': include(MP_Pfad.'mpIndex.php'); break;
  case 'suche': include(MP_Pfad.'mpSuche.php'); break;
  case 'eingabe': include(MP_Pfad.'mpEingabe.php'); break;
  case 'aendern': include(MP_Pfad.'mpAendern.php'); break;
  case 'kopieren': include(MP_Pfad.'mpKopieren.php'); break;
  case 'druck': //ListenDruck
   if($sMpSegment!=''){
    include(MP_Pfad.'mpDaten.php'); fMpDaten(true,false); include(MP_Pfad.'mpDruck.php'); //Segmentliste
   }else{
    include(MP_Pfad.'mpDruck0.php'); $sMpTitel=MP_TxQuerSuche; //Quersuchliste
   }break;
  case 'drucken': include(MP_Pfad.'mpDrucken.php'); break; //Detaildruck
  case 'kontakt': include(MP_Pfad.'mpKontakt.php'); break; //Klick auf e-Mail-Feld
  case 'info': include(MP_Pfad.'mpInfo.php'); break; //tell-a-friend-Funktion
  case 'nachricht': include(MP_Pfad.'mpNachricht.php'); break; //Benachrichtigungs-Service
  case 'login': include(MP_Pfad.'mpLogin.php'); $sMpTitel=(MP_LinkULogi?MP_LinkULogi:MP_LinkOLogi); if($sMpV=fMpTestLogin()) $sMpSession=$sMpV; break; //Benutzerverwaltung
  case 'zusatz': include(MP_Pfad.'mpZusatz.php'); $sMpTitel=constant('MP_LinkZ'.MP_Zusatz); break; //Zusatzseiten
  default: if(substr($sMpAktion,0,1)=='o') include(MP_Pfad.'mpFreischalt.php'); else{include(MP_Pfad.'mpFehler.php'); header("HTTP/1.0 404 Not Found"); if(MP_ErrorPage&&file_exists(MP_Pfad.'error404.html')){readfile(MP_Pfad.'error404.html'); exit;}} //Freischaltung oder 404
 }
 if(defined('MP_SuchParam')) $sMpSuchParam=MP_SuchParam; //kommt von fMpDaten()
 if(defined('MP_MetaKey')) $sMpKey=MP_MetaKey; else $sMpKey=MP_TxAMetaKey;
 if(defined('MP_MetaDes')) $sMpDes=MP_MetaDes; else $sMpDes=MP_TxAMetaDes;
 if(defined('MP_MetaTit')) $sMpTit=MP_MetaTit; else $sMpTit='';

 //Beginn der Ausgabe
 $sMpHtmlVor=str_replace('{META-KEY}',str_replace('#S',fMpTx(MP_SegName),$sMpKey),str_replace('{META-DES}',str_replace('#S',fMpTx(MP_SegName),$sMpDes),str_replace('{TITLE}',str_replace('#S',fMpTx(MP_SegName),$sMpTit),$sMpHtmlVor)));
 $sMpX=str_replace('{Segment}',fMpTx($sMpTitel?$sMpTitel:MP_SegName),$sMpHtmlVor)."\n".'<div class="mpBox">'."\n"; include(MP_Pfad.'mpVersion.php');
 if(MP_Version!=$mpVersion||strlen(MP_Www)==0) $sMpX.="\n".'<p class="mpFehl">'.fMpTx(MP_TxSetupFehlt).'</p>'."\n";

 // Druck als Popup-Fenster
 if(MP_DruckPopup&&substr($sMpAktion,0,5)!='druck') $sMpX.="\n".'<script type="text/javascript">function PrWin(sUrl){prWin=window.open(sUrl,"prwin","width='.MP_PopupBreit.',height='.MP_PopupHoch.',left='.(MP_PopupX+4).',top='.(MP_PopupY+4).',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");prWin.focus();}</script>'."\n";

 //Aktivitaetslinks oben
 $sMp='';
 if(MP_LinkOIndx&&(!MP_LnkOIndxN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('','','','','-'),MP_LinkOIndx);
 if(MP_Segment&&MP_LinkOList&&(!MP_LnkOListN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('liste',($sMpAktion!='liste'?$sMpSeite:'1'),'',($sMpAktion!='liste'?$sMpIndex.$sMpRueck.$sMpSuchParam:'')),MP_LinkOList);
 if(MP_LinkODrck&&(!MP_LnkODrckN||$sMpSession))
  if($sMpAktion!='detail'){
   if($sMpAktion!='liste') $sMp.=fMpNavLnk(fMpHref('druck','1','',($sMpSuchText!=''?'&amp;mp_Such='.$sMpSuchText:'').(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkODrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
   else $sMp.=fMpNavLnk(fMpHref('druck','1','',$sMpIndex.$sMpRueck.$sMpSuchParam.(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkODrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
  }else $sMp.=fMpNavLnk(fMpHref('drucken',$sMpSeite,$sMpNummer,$sMpIndex.$sMpRueck.$sMpSuchParam.(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkODrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
 if(MP_Segment&&MP_LinkOSuch&&(!MP_LnkOSuchN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('suche','','',$sMpIndex.$sMpRueck.$sMpSuchParam),MP_LinkOSuch);
 if(MP_Segment&&MP_LinkOEing&&(!MP_LnkOEingN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('eingabe'),MP_LinkOEing);
 if(MP_Segment&&MP_LinkOAend&&(!MP_LnkOAendN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref($sMpAendAktion,$sMpSeite,$sMpNummer,$sMpAendParam),MP_LinkOAend);
 if(MP_LinkOLogi&&(!MP_LnkOLogiN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('login',$sMpSeite),($sMpSession==''||!defined('MP_LinkOLogx')?MP_LinkOLogi:MP_LinkOLogx));
 if(!$bMpPopup&&($sMpAktion!='index'||MP_IndexAktO)&&strlen($sMp)>0) $sMpX.="\n".'<ul class="mpMnuO">'.$sMp."\n".'</ul>'."\n"; $sMp='';


 //Seiteninhalt
 if($sMpAktion!='login') $sMpPg=fMpSeite()."\n"; else $sMpPg=fMpLogSeite()."\n";
 if(defined('MP_Canonical')) $sMpX=str_replace('<title>','<link rel="canonical" href="'.MP_Canonical.'" />'."\n".'<title>',$sMpX);
 if(defined('MP_410Gone')){
  header("HTTP/".(isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'1.0')." 410 Gone");
  if(MP_ErrorPage&&file_exists(MP_Pfad.'error410.html')){readfile(MP_Pfad.'error410.html'); exit;}
 }

 $sMpOut.=$sMpX; $sMpOut.=$sMpPg; // Kopf+Seite

 //Aktivitaetslinks unten
 $sMp='';
 if(MP_LinkUIndx&&(!MP_LnkUIndxN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('','','','','-'),MP_LinkUIndx);
 if(MP_Segment&&MP_LinkUList&&(!MP_LnkUListN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('liste',($sMpAktion!='liste'?$sMpSeite:'1'),'',($sMpAktion!='liste'?$sMpIndex.$sMpRueck.$sMpSuchParam:'')),MP_LinkUList);
 if(MP_LinkUDrck&&(!MP_LnkUDrckN||$sMpSession))
  if($sMpAktion!='detail'){
   if($sMpAktion!='liste') $sMp.=fMpNavLnk(fMpHref('druck','1','',($sMpSuchText!=''?'&amp;mp_Such='.$sMpSuchText:'').(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkUDrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
   else $sMp.=fMpNavLnk(fMpHref('druck','1','',$sMpIndex.$sMpRueck.$sMpSuchParam.(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkUDrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
  }else $sMp.=fMpNavLnk(fMpHref('drucken',$sMpSeite,$sMpNummer,$sMpIndex.$sMpRueck.$sMpSuchParam.(MP_DruckPopup?'&amp;mp_Popup=1':'')),MP_LinkUDrck,(MP_DruckPopup?' target="prwin" onclick="PrWin(this.href);return false;"':''));
 if(MP_Segment&&MP_LinkUSuch&&(!MP_LnkUSuchN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('suche','','',$sMpIndex.$sMpRueck.$sMpSuchParam),MP_LinkUSuch);
 if(MP_Segment&&MP_LinkUEing&&(!MP_LnkUEingN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('eingabe'),MP_LinkUEing);
 if(MP_Segment&&MP_LinkUAend&&(!MP_LnkUAendN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref($sMpAendAktion,$sMpSeite,$sMpNummer,$sMpIndex.$sMpRueck.$sMpSuchParam.$sMpAendParam),MP_LinkUAend);
 if(MP_LinkULogi&&(!MP_LnkULogiN||$sMpSession)) $sMp.=fMpNavLnk(fMpHref('login',$sMpSeite),($sMpSession==''||!defined('MP_LinkULogx')?MP_LinkULogi:MP_LinkULogx));
 if(!$bMpPopup&&($sMpAktion!='index'||MP_IndexAktU)&&strlen($sMp)>0) $sMpOut.="\n".'<ul class="mpMnuU">'.$sMp."\n".'</ul>'."\n";

 //Zusatzlinks unten
 $sMp='';
 for($mpI=1;$mpI<=7;$mpI++) if(constant('MP_LinkZ'.$mpI)&&(!constant('MP_LnkZ'.$mpI.'N')||$sMpSession)) $sMp.=fMpNavLnk(fMpHref(fMpNormAdr(constant('MP_LinkZ'.$mpI)).'_'.$mpI),constant('MP_LinkZ'.$mpI));
 if(!$bMpPopup&&($sMpAktion!='index'||MP_IndexAktZ)&&strlen($sMp)>0) $sMpOut.="\n".'<ul class="mpMnuU">'.$sMp."\n".'</ul>'."\n";

 //Ende der Ausgabebox und evt. Seitenfuss
 $sMpOut.="\n".'</div>'."\n".str_replace('{Segment}',fMpTx($sMpTitel?$sMpTitel:MP_SegName),$sMpHtmlNach);
}

if(!defined('MP_Echo')||MP_Echo||$bMpPopup) echo $sMpOut."\n";

function fMpIsEMailAdr($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}

function fMpDeCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fMpAnzeigeDatum($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fMpWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}

function fMpDSEFld($z,$bCheck=false){
 $s='<a class="mpText" href="'.MP_DSELink.'"'.(MP_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(MP_DSETarget?' target="'.MP_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',str_replace('{{','<',str_replace('}}','>',fMpTx(str_replace('<','{{',str_replace('>','}}',($z!=2?MP_TxDSE1:MP_TxDSE2))))))));
 return '<input class="mpCheck" type="checkbox" name="mp_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

function fMpTx($sTx){ //TextKodierung
 if(MP_Zeichensatz<=0) $s=$sTx; elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}

function fMpDt($s){ //DatenKodierung
 $mpZeichensatz=MP_ZeichnsNorm; if(MP_Popup) $mpZeichensatz=MP_ZeichnsPopf;
 if(MP_Zeichensatz==$mpZeichensatz){if(MP_Zeichensatz!=1) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));}
 else{
  if($mpZeichensatz!=0) if($mpZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  if(MP_Zeichensatz<=0) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));
  elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))))); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br />',$s);
}

function fMpExtLink($s){
 if(!defined('MP_ZeichnsExtLink')||MP_ZeichnsExtLink==0) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode($s)));
 elseif(MP_ZeichnsExtLink==1) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s))));
 elseif(MP_ZeichnsExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fMpRq($sTx){ //Eingaben reinigen
 return stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
}
function fMpRq1($sTx){ //nur 1 Wort der Eingabe
 $sTx=stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
 if($nP=strpos($sTx,' ')) $sTx=substr($sTx,0,$nP);
 return $sTx;
}

function fMpBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.$o.'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="mpText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="mpText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="mpText"><li class="mpText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="mpText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}

function fMpNormAdr($sNam){
 $sNam=str_replace('Ä','Ae',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',str_replace('ß','ss',str_replace('"','',str_replace(' ','_',strtolower($sNam))))))));
 $sNam=str_replace('Ã„','Ae',str_replace('Ã¤','ae',str_replace('Ã–','Oe',str_replace('Ã¶','oe',str_replace('Ãœ','Ue',str_replace('Ã¼','ue',str_replace('ÃŸ','ss',$sNam)))))));
 return str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace('#','_',str_replace('?','_',str_replace('/','_',$sNam)))))));
}

function fMpNavLnk($sLnk,$sLinkTx,$sTarget=''){
 return "\n".'<li><a href="'.$sLnk.'"'.$sTarget.'>'.fMpTx($sLinkTx).'</a></li>';
}

function fMpHref($sAct='',$sSei='',$sNum='',$sPar='',$sSeg=false,$bCanonical=false){ //erzeugt einen Link
 $sL=''; if(!$sSeg) $sSeg=MP_Segment; elseif($sSeg=='-') $sSeg='';
 if(!MP_Sef||strpos($sPar,'_Popup=1')){ //normal
  if($sAct) $sL.='&amp;mp_Aktion='.$sAct;
  if($sSeg>''&&!strpos($sAct,'_')) $sL.='&amp;mp_Segment='.$sSeg;
  if(MP_ListenLaenge>0&&$sSei) $sL.='&amp;mp_Seite='.$sSei;
  if($sNum) $sL.='&amp;mp_Nummer='.$sNum;
  if(MP_Session>''&&!$bCanonical) $sL.='&amp;mp_Session='.MP_Session; elseif(defined('MP_NeuSession')&&!$bCanonical) $sL.='&amp;mp_Session='.MP_NeuSession;
  if(defined('MP_Layout')&&!$bCanonical&&!strpos($sPar,'_Popup=1')) $sPar.='&amp;mp_Layout='.MP_Layout;
  if($sPar) $sL.=$sPar; $sL.=MP_Query; if($sL) $sL='?'.substr($sL,5);
  $sL=(!strpos($sPar,'_Popup=1')?MP_Self:MP_Url.'marktplatz.php').$sL;
 }else{ // SEF
  $sL=substr(MP_Self,0,strrpos(MP_Self,'/')+1);
  if($sAct){
   if($sSeg>''&&!strpos($sAct,'_')){
    $aN=explode(';',MP_Segmente); if(!$sNam=$aN[$sSeg]) $sNam=MP_TxSegment; if(substr($sNam,0,1)=='*') $sNam=substr($sNam,1);
    if($p=strpos($sNum,'-')){$sNam=substr($sNum,$p+1); $sNum=substr($sNum,0,$p);}
    $sL.=fMpNormAdr($sNam).'-'.$sAct.'-'.$sSeg;
    if((MP_ListenLaenge>0&&$sSei)||$sNum) $sL.='-'.$sSei; if($sNum) $sL.='-'.$sNum;
   }else{
    $sL.=$sAct;
    if(MP_ListenLaenge>0&&$sSei) $sL.='-'.$sSei;
   }
  }else $sL.='marktplatz';
  $sL.='.html';
  if(defined('MP_Layout')&&!$bCanonical) $sPar.='&amp;mp_Layout='.MP_Layout;
  if(MP_Session>''&&!$bCanonical) $sPar='&amp;mp_Session='.MP_Session.$sPar; elseif(defined('MP_NeuSession')&&!$bCanonical) $sPar='&amp;mp_Session='.MP_NeuSession.$sPar;
  $sPar.=MP_Query; if($sPar) $sL.='?'.substr($sPar,5);
 }
 return $sL;
}
?>