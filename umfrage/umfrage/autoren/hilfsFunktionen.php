<?php
 define('UMFPFAD','../');
/* ---------------------------------------------------------------
 Das ist die relative Pfadangabe,
 die vom Autoren-Ordner (Backend) aus
 auf das Programmverzeichnis (Frontend) des Umfrage-Scripts verweist
 mit einem / am Ende.
 Beispiel: define('UMFPFAD','../');
 Die Angabe ist zu aendern, wenn der Autoren-Ordner NICHT wie ueblich
 direkt unterhalb von umfrage als umfrage/autoren liegt.
------------------------------------------------------------------ */

/* Ab hier nichts mehr veraendern! */

error_reporting(E_ALL);

define('NL',"\n"); $sMeld=''; $DbO=NULL; $bAdmLoginOK=false;
define('KONF',(int)(isset($_GET['konf'])?$_GET['konf']:(isset($_POST['konf'])?$_POST['konf']:0)));
@include UMFPFAD.'umfWerte'.(KONF>0?KONF:'').'.php';
if(phpversion()>='5.1.0') if(defined('UMF_TimeZoneSet')) if(strlen(UMF_TimeZoneSet)>0) date_default_timezone_set(UMF_TimeZoneSet);
if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME']; $sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
$sAwww='http://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
if(defined('UMF_Version')){
 if(defined('ADU_AuthLogin')&&ADU_AuthLogin){
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
  if((!isset($_SESSION['Id'])||$_SESSION['Id']!=md5(session_id())||(ADU_SessionsAgent&&$_SESSION['Ua']!=md5($sUserAgent))||(ADU_SessionsIPAddr&&$_SESSION['Ip']!=md5($sIp)))&&!strpos($sSelf,'autorenLogin.php')){
   header('Location: '.$sAwww.'/autorenLogin.php');
   exit;
  }else $bAdmLoginOK=true;
 }
 if(UMF_SQL){mysqli_report(MYSQLI_REPORT_OFF); $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa); if(!mysqli_connect_errno()){if(defined('ADU_SqlCharSet')&&ADU_SqlCharSet) $DbO->set_charset(ADU_SqlCharSet);} else $DbO=NULL;}
 $aKonf=array(); $h=opendir(UMFPFAD); while($sF=readdir($h)) if(substr($sF,0,8)=='umfWerte'&&substr($sF,8,1)!='0'&&strpos($sF,'.php')>0) $aKonf[]=(int)substr($sF,8); closedir($h); sort($aKonf); if($aKonf[0]==0) $aKonf[0]='';
 $bAlleKonf=true; define('MULTIKONF',count($aKonf)>1);
 $sHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.UMF_Www;
}else{
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}

function fSeitenKopf($sTitel='',$sHead='',$sBar=''){
 header('Content-Type: text/html; charset=ISO-8859-1');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Umfrage-Script - Autorenbereich</title>
<link rel="stylesheet" type="text/css" href="autoren.css">
<script type="text/javascript">
 function konfWechsel(Konf){if(Konf==0) window.location.href='index.php'; else window.location.href='index.php?konf='+Konf;}
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>
<?php if(!empty($sHead)) echo trim($sHead)."\n"?>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:<?php echo (defined('ADU_Breite')?ADU_Breite:950)?>px;"><!-- Seite -->
<div id="kopf">
 <div id="version">Version <?php echo trim(substr(UMF_Version,0,3)).' ('.trim(substr(UMF_Version,4).')')?></div>
 <h1><a href="http://www.server-scripts.de" target="_new"><img src="_frage.gif" style="margin-bottom:-5px;" width="16" height="24" border="0" title="Umfrage-Script"></a>
 Umfrage-Script: <?php echo $sTitel;?></h1>
</div>
<div id="navig"><div id="navpad"><!-- Navigation -->
<form action="index.php" method="get">
<ul id="menu">
<li class="rubrik">Autoren-Menü</li>
<li<?php if($sBar=='Idx') echo' class="aktiv"'?>><a href="index.php<?php if(KONF>0)echo'?konf='.KONF?>">Übersicht</a></li>
<?php if(defined('ADU_AuthLogin')&&ADU_AuthLogin){?>
<li<?php if($sBar=='Log') echo' class="aktiv"'?>><a href="autorenLogin.php">Login/Logout</a></li>
<?php }?>
<li><a href="<?php echo (defined('ADU_Hilfe')?ADU_Hilfe:'http://www.server-scripts.de/umfrage/')?>LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>

<?php //Konfigurationen
 global $aKonf; $sO='';
 foreach($aKonf as $k=>$v) if($v>0) $sO.='<option value="'.$v.($v!=KONF?'':'" selected="selected').'">Konfiguration '.$v.'</option>'; reset($aKonf);
?>
<li class="rubrik"><div style="float:left">Konfigurationen</div><div style="text-align:right"><button type="submit" style="height:17px;width:26px;line-height:9px;font-size:9px;padding:0;">OK</button></div></li>
<li><select name="konf" id="naviKonf" onchange="konfWechsel(this.value)" size="1"><option value="0">Grundkonfiguration</option><?php echo $sO?></select></li>

<li class="rubrik">Umfragen verwalten</li>
<?php   if(file_exists('fragenListe.php')){?><li<?php if($sBar=='UFl') echo' class="aktiv"'?>><a href="fragenListe.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragenliste</a></li>
<?php } if(file_exists('fragenEingabe.php')){?><li<?php if($sBar=='UFe') echo' class="aktiv"'?>><a href="fragenEingabe.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragen eingeben</a></li>
<?php } if(file_exists('fragenSuche.php')){?><li<?php if($sBar=='UFs') echo' class="aktiv"'?>><a href="fragenSuche.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragen suchen</a></li>
<?php } if(file_exists('druckSuche.php')){?><li<?php if($sBar=='UFd') echo' class="aktiv"'?>><a href="druckSuche.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragen drucken</a></li>
<?php } if(file_exists('konfUmfrage.php')){?><li<?php if($sBar=='UVU') echo' class="aktiv"'?>><a href="konfUmfrage.php<?php if(KONF>0)echo'?konf='.KONF?>">vorbereitete Umfragen</a></li>
<?php } ?>

<li class="rubrik">Ergebnisse verwalten</li>
<?php   if(file_exists('ergebnisListe.php')){?><li<?php if($sBar=='EEl') echo' class="aktiv"'?>><a href="ergebnisListe.php<?php if(KONF>0)echo'?konf='.KONF?>">Ergebnisliste</a></li>
<?php } if(file_exists('ergebnisSuche.php')){?><li<?php if($sBar=='EEs') echo' class="aktiv"'?>><a href="ergebnisSuche.php<?php if(KONF>0)echo'?konf='.KONF?>">Ergebnisse suchen</a></li>
<?php } if(file_exists('teilnahmeListe.php')){?><li<?php if($sBar=='ETl') echo' class="aktiv"'?>><a href="teilnahmeListe.php<?php if(KONF>0)echo'?konf='.KONF?>">Teilnahmeliste</a></li>
<?php } if(file_exists('ipListe.php')){?><li<?php if($sBar=='ETk') echo' class="aktiv"'?>><a href="ipListe.php<?php if(KONF>0)echo'?konf='.KONF?>">Teilnehmerkennungen</a></li>
<?php } ?>

<li class="rubrik">Benutzer verwalten</li>
<?php   if(file_exists('nutzerListe.php')){?><li<?php if($sBar=='NNl') echo' class="aktiv"'?>><a href="nutzerListe.php<?php if(KONF>0)echo'?konf='.KONF?>">Benutzerliste</a></li>
<?php } if(file_exists('nutzerSuche.php')){?><li<?php if($sBar=='NNs') echo' class="aktiv"'?>><a href="nutzerSuche.php<?php if(KONF>0)echo'?konf='.KONF?>">Benutzer suchen</a></li>
<?php } if(file_exists('nutzerZuweisung.php')&&defined('UMF_NutzerUmfragen')&&UMF_NutzerUmfragen){?>
<li<?php if($sBar=='NZw') echo' class="aktiv"'?>><a href="nutzerZuweisung.php<?php if(KONF>0)echo'?konf='.KONF?>">Benutzer &amp; Umfragen</a></li>
<?php }?>

<?php   if(file_exists('konfAblauf.php')){?><li class="rubrik">Funktionsanpassung</li>
<li<?php if($sBar=='KAl') echo' class="aktiv"'?>><a href="konfAblauf.php<?php if(KONF>0)echo'?konf='.KONF?>">Ablaufeinstellungen</a></li>
<?php }?>

<li class="rubrik">Zusatzfunktionen</li>
<?php   if(file_exists('fragenExport.php')){?><li<?php if($sBar=='ZFe') echo' class="aktiv"'?>><a href="fragenExport.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragenexport</a></li>
<?php } if(file_exists('fragenImport.php')){?><li<?php if($sBar=='ZFi') echo' class="aktiv"'?>><a href="fragenImport.php<?php if(KONF>0)echo'?konf='.KONF?>">Fragenimport</a></li>
<?php } if(file_exists('upload.php')){?><li<?php if($sBar=='ZUp') echo' class="aktiv"'?>><a href="upload.php<?php if(KONF>0)echo'?konf='.KONF?>">Datei-Upload</a></li>
<?php } if(file_exists('../admin/index.php')){?><li><a href="../admin/index.php">Adminbereich</a></li>
<?php }?>
</ul>
</form>
</div></div><!-- /Navigation -->
<div id="inhalt"><div id="inhpad"><!-- Inhalt -->

<?php
}
function fSeitenFuss(){
?>
<div id="zeitangabe">--- <?php echo @date('d.m.Y, H:i:s')?> ---</div>
</div></div><!-- /Inhalt -->
<div id="fuss">&copy; <a href="http://www.server-scripts.de/software/">Umfrage-Script</a></div>
</div></div><!-- /Seite -->
</body>
</html>
<?php
}

function fUmfEnCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fUmfDeCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fSetzUmfWert($w,$n,$t){
 global $sWerte, ${'us'.$n};
 if($t=="'") $w=str_replace("'",'´',$w); ${'us'.$n}=$w;
 if($w!=constant('UMF_'.$n)){
  $p=strpos($sWerte,'UMF_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'UMF_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function txtVar($Var){return isset($_POST[$Var])?str_replace('"',"'",stripslashes(trim($_POST[$Var]))):'';}
function fMMeld($s){return '<p class="admMeld">'.$s.'</p>'.NL;}
function fMErfo($s){return '<p class="admErfo">'.$s.'</p>'.NL;}
function fMFehl($s){return '<p class="admFehl">'.$s.'</p>'.NL;}
?>