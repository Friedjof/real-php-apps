<?php
 define('FRAPFAD','../');
/* ---------------------------------------------------------------
 Das ist die relative Pfadangabe,
 die vom Admin-Ordner (Backend) aus
 auf das Programmverzeichnis (Frontend) des Frage-Scripts verweist
 mit einem / am Ende.
 Beispiel: define('FRAPFAD','../');
 Die Angabe ist zu aendern, wenn der Admin-Ordner NICHT wie ueblich
 direkt unterhalb von testfragen als testfragen/admin liegt.
------------------------------------------------------------------ */

/* Ab hier nichts mehr veraendern! */

error_reporting(E_ALL);

define('NL',"\n"); $sMeld=''; $DbO=NULL; $bAdmLoginOK=false;
define('KONF',(int)(isset($_GET['konf'])?$_GET['konf']:(isset($_POST['konf'])?$_POST['konf']:0)));
@include FRAPFAD.'fraWerte'.(KONF>0?KONF:'').'.php';
if(defined('FRA_TimeZoneSet')) if(strlen(FRA_TimeZoneSet)>0) date_default_timezone_set(FRA_TimeZoneSet);
if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME']; $sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
$sAwww='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
if(defined('FRA_Version')){
 if(defined('ADF_MitLogin')&&ADF_MitLogin){
  ini_set('session.use_cookies',true); ini_set('session.use_only_cookies',true); ini_set('session.cookie_lifetime',0);
  session_start();
  if(!isset($_SERVER['REMOTE_ADDR'])||!($sIp=$_SERVER['REMOTE_ADDR'])) $sIp='??';
  if(!isset($_SERVER['HTTP_USER_AGENT'])||!($sUserAgent=$_SERVER['HTTP_USER_AGENT'])){
   $sUserAgent=' '.(isset($_SERVER['ALL_HTTP'])?$_SERVER['ALL_HTTP']:'@');
   if($p=strpos($sUserAgent,'HTTP_USER_AGENT')){
    $sUserAgent=trim(substr($sUserAgent,$p+16));
    if($p=strpos(strtoupper($sUserAgent),'HTTP_')) $sUserAgent=rtrim(substr($sUserAgent,0,$p-1));
   }else $sUserAgent='???';
  }
  if((!isset($_SESSION['Id'])||$_SESSION['Id']!=md5(session_id())||(ADF_SessionsAgent&&$_SESSION['Ua']!=md5($sUserAgent))||(ADF_SessionsIPAddr&&$_SESSION['Ip']!=md5($sIp)))&&!strpos($sSelf,'adminLogin.php')){
   header('Location: '.$sAwww.'/adminLogin.php');
   exit;
  }else $bAdmLoginOK=true;
 }
 if(FRA_SQL){mysqli_report(MYSQLI_REPORT_OFF); $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa); if(!mysqli_connect_errno()){if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $DbO=NULL;}
 $aKonf=array(); $h=opendir(FRAPFAD); while($sF=readdir($h)) if(substr($sF,0,8)=='fraWerte'&&substr($sF,8,1)!='0'&&strpos($sF,'.php')>0) $aKonf[]=(int)substr($sF,8); closedir($h); sort($aKonf); if($aKonf[0]==0) $aKonf[0]='';
 $bAlleKonf=true; define('MULTIKONF',count($aKonf)>1);
 $sHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www;
}else{
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}

function fSeitenKopf($sTitel='',$sHead='',$sBar='',$bUtf8=false){
 if($bUtf8==false) header('Content-Type: text/html; charset=ISO-8859-1'); else header('Content-Type: text/html; charset=UTF-8');
 //Konfigurationen
 global $aKonf; $sO='';
 foreach($aKonf as $k=>$v) if($v>0) $sO.='<option value="'.$v.($v!=KONF?'':'" selected="selected').'">Konfiguration '.$v.'</option>'; reset($aKonf);
 return '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset='.($bUtf8==false?'iso-8859-1':'utf-8').'">
<meta http-equiv="expires" content="0">
<title>Testfragen-Script - Administration</title>
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript">
 function konfWechsel(Konf){if(Konf==0) window.location.href="index.php"; else window.location.href="index.php?konf="+Konf;}
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>'.(!empty($sHead)?"\n".trim($sHead):'').'
</head>

<body>
<div id="seite"><div id="rahmen" style="width:'.(defined('ADF_Breite')?ADF_Breite:950).'px;"><!-- Seite -->
<div id="kopf">
 <div id="version">Version '.trim(substr(FRA_Version,0,3)).' ('.trim(substr(FRA_Version,4)).')</div>
 <h1><a href="https://www.server-scripts.de" target="_new"><img src="_frage.gif" style="margin-bottom:-5px;" width="16" height="24" border="0" title="Testfragen-Script"></a>
 Testfragen-Script: '.$sTitel.'</h1>
</div>
<div id="navig"><div id="navpad"><!-- Navigation -->
<form action="index.php" method="get">
<ul id="menu">
<li class="rubrik">Administrator-Menü</li>
<li'.($sBar=='Idx'?' class="aktiv"':'').'><a href="index.php'.(KONF>0?'?konf='.KONF:'').'">Übersicht</a></li>
'.((defined('ADF_MitLogin')&&ADF_MitLogin)?'<li'.($sBar=='Log'?' class="aktiv"':'').'><a href="adminLogin.php">Login/Logout</a></li>':'').'
<li'.($sBar=='Afr'?' class="aktiv"':'').'><a href="einbettung.php'.(KONF>0?'?konf='.KONF:'').'">Aufruf/Einbettung</a></li>
<li><a href="'.(defined('ADF_Hilfe')?ADF_Hilfe:'https://www.server-scripts.de/testfragen/').'LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>

<li class="rubrik"><div style="float:left">Konfigurationen</div><div style="text-align:right"><button type="submit" style="height:17px;width:26px;line-height:9px;font-size:9px;padding:0;">OK</button></div></li>
<li><select name="konf" id="naviKonf" onchange="konfWechsel(this.value)" size="1"><option value="0">Grundkonfiguration</option>'.$sO.'</select></li>
<li'.($sBar=='Kfg'?' class="aktiv"':'').'><a href="konfKonf.php'.(KONF>0?'?konf='.KONF:'').'">Konfigurationen</a></li>

<li class="rubrik">Testfragen verwalten</li>
<li'.($sBar=='FFl'?' class="aktiv"':'').'><a href="liste.php'.(KONF>0?'?konf='.KONF:'').'">Fragenliste</a></li>
<li'.($sBar=='FFe'?' class="aktiv"':'').'><a href="eingabe.php'.(KONF>0?'?konf='.KONF:'').'">Fragen eingeben</a></li>
<li'.($sBar=='FFs'?' class="aktiv"':'').'><a href="suche.php'.(KONF>0?'?konf='.KONF:'').'">Fragen suchen</a></li>
<li'.($sBar=='FDr'?' class="aktiv"':'').'><a href="druckSuche.php'.(KONF>0?'?konf='.KONF:'').'">Fragen drucken</a></li>
<li'.($sBar=='FFk'?' class="aktiv"':'').'><a href="kategorien.php'.(KONF>0?'?konf='.KONF:'').'">Fragenkategorien</a></li>

<li class="rubrik">Ergebnisse verwalten</li>
<li'.($sBar=='EEl'?' class="aktiv"':'').'><a href="ergebnisListe.php'.(KONF>0?'?konf='.KONF:'').'">Ergebnisliste</a></li>
<li'.($sBar=='EEs'?' class="aktiv"':'').'><a href="ergebnisSuche.php'.(KONF>0?'?konf='.KONF:'').'">Ergebnisse suchen</a></li>
<li'.($sBar=='ETa'?' class="aktiv"':'').'><a href="testAuswerten.php'.(KONF>0?'?konf='.KONF:'').'">Tests auswerten</a></li>

<li class="rubrik">Benutzer verwalten</li>
<li'.($sBar=='NNl'?' class="aktiv"':'').'><a href="nutzerListe.php'.(KONF>0?'?konf='.KONF:'').'">Benutzerliste</a></li>
<li'.($sBar=='NNS'?' class="aktiv"':'').'><a href="nutzerSuche.php'.(KONF>0?'?konf='.KONF:'').'">Benutzer suchen</a></li>'.((defined('FRA_NutzerTests')&&FRA_NutzerTests)?'
<li'.($sBar=='NZw'?' class="aktiv"':'').'><a href="nutzerZuweisung.php'.(KONF>0?'?konf='.KONF:'').'">Benutzer und Tests</a></li>':'').'
<li class="rubrik">Funktionsanpassung</li>
<li'.($sBar=='KTa'?' class="aktiv"':'').'><a href="konfTest.php'.(KONF>0?'?konf='.KONF:'').'">Testfragenauswahl</a></li>
<li'.($sBar=='KAl'?' class="aktiv"':'').'><a href="konfAblauf.php'.(KONF>0?'?konf='.KONF:'').'">Ablaufeinstellungen</a></li>
<li'.($sBar=='KBr'?' class="aktiv"':'').'><a href="konfBewerten.php'.(KONF>0?'?konf='.KONF:'').'">Bewertungsregeln</a></li>'.(file_exists('konfUrkunde.php')?'
<li'.($sBar=='KUk'?' class="aktiv"':'').'><a href="konfUrkunde.php'.(KONF>0?'?konf='.KONF:'').'">Urkundeneinstellungen</a></li>':'').'
<li'.($sBar=='KTn'?' class="aktiv"':'').'><a href="konfTeilnehmer.php'.(KONF>0?'?konf='.KONF:'').'">Teilnehmerfunktionen</a></li>
<li'.($sBar=='KBn'?' class="aktiv"':'').'><a href="konfNutzer.php'.(KONF>0?'?konf='.KONF:'').'">Benutzerfunktionen</a></li>
<li'.($sBar=='KBS'?' class="aktiv"':'').'><a href="konfStatistik.php'.(KONF>0?'?konf='.KONF:'').'">Bestenliste/Statistik</a></li>
<li'.($sBar=='KDr'?' class="aktiv"':'').'><a href="konfDrucken.php'.(KONF>0?'?konf='.KONF:'').'">Druckeinstellungen</a></li>

<li class="rubrik">Systemeinstellungen</li>
<li'.($sBar=='KSU'?' class="aktiv"':'').'><a href="konfSetup.php">Setup/Update</a></li>
<li'.($sBar=='KDb'?' class="aktiv"':'').'><a href="konfDaten.php'.(KONF>0?'?konf='.KONF:'').'">Datenbasis</a></li>
<li'.($sBar=='KAd'?' class="aktiv"':'').'><a href="konfAdmin.php'.(KONF>0?'?konf='.KONF:'').'">Admin-Einstellungen</a></li>
<li'.($sBar=='KAg'?' class="aktiv"':'').'><a href="konfAllgemein.php'.(KONF>0?'?konf='.KONF:'').'">Allgemeines</a></li>
<li'.($sBar=='KLy'?' class="aktiv"':'').'><a href="konfLayout.php'.(KONF>0?'?konf='.KONF:'').'">Layouteinstellungen</a></li>
<li'.($sBar=='KFa'?' class="aktiv"':'').'><a href="konfFarben.php'.(KONF>0?'?konf='.KONF:'').'">Farbeinstellungen</a></li>
<li'.($sBar=='KEm'?' class="aktiv"':'').'><a href="konfEmail.php'.(KONF>0?'?konf='.KONF:'').'">E-Mail-Einstellungen</a></li>

<li class="rubrik">Zusatzfunktionen</li>
<li'.($sBar=='Exp'?' class="aktiv"':'').'><a href="export.php'.(KONF>0?'?konf='.KONF:'').'">Fragenexport</a></li>
<li'.($sBar=='Imp'?' class="aktiv"':'').'><a href="import.php'.(KONF>0?'?konf='.KONF:'').'">Fragenimport</a></li>
<li'.($sBar=='KSi'?' class="aktiv"':'').'><a href="backup.php'.(KONF>0?'?konf='.KONF:'').'">Konfiguration sichern</a></li>
<li'.($sBar=='KLa'?' class="aktiv"':'').'><a href="restore.php'.(KONF>0?'?konf='.KONF:'').'">Konfiguration laden</a></li>
<li'.($sBar=='Upl'?' class="aktiv"':'').'><a href="upload.php'.(KONF>0?'?konf='.KONF:'').'">Datei-Upload</a></li>
<li'.($sBar=='Im2'?' class="aktiv"':'').'><a href="importVer2x.php'.(KONF>0?'?konf='.KONF:'').'">Import Version 2.x</a></li>
<li'.($sBar=='Afr'?' class="aktiv"':'').'><a href="einbettung.php'.(KONF>0?'?konf='.KONF:'').'">Aufruf/Einbettung</a></li>
'.(file_exists('../autoren/index.php')?'<li><a href="../autoren/index.php">Autorenbereich</a></li>':'').'
<li><a href="info.php" target="hilfe" onclick="hlpWin(this.href);return false;">PHP-Info</a></li>
</ul>
</form>
</div></div><!-- /Navigation -->
<div id="inhalt"><div id="inhpad"><!-- Inhalt -->
';
}
function fSeitenFuss(){
return '
<div id="zeitangabe">--- '.@date('d.m.Y, H:i:s').' ---</div>
</div></div><!-- /Inhalt -->
<div id="fuss">&copy; <a href="https://www.testfragen-script.de">Testfragen-Script</a></div>
</div></div><!-- /Seite -->
</body>
</html>
';
}

function fFraEnCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fSetzFraWert($w,$n,$t){
 global $sWerte, ${'fs'.$n};
 if($t=="'") $w=str_replace("'",'´',$w); ${'fs'.$n}=$w;
 if($w!=constant('FRA_'.$n)){
  $p=strpos($sWerte,'FRA_'.$n."',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'FRA_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function txtVar($Var){return isset($_POST[$Var])?str_replace('"',"'",stripslashes(trim($_POST[$Var]))):'';}
?>