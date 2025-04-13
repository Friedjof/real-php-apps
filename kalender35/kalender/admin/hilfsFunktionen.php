<?php
 define('KALPFAD','../');
/* ---------------------------------------------------------------
 Das ist die relative Pfadangabe,
 die vom Admin-Ordner (Backend) aus
 auf das Programmverzeichnis (Frontend) des Kalenders verweist
 mit einem / am Ende.
 Beispiel: define('KALPFAD','../');
 Die Angabe ist zu aendern, wenn der Admin-Ordner NICHT wie ueblich
 direkt unterhalb von kalender als kalender/admin liegt.
------------------------------------------------------------------ */

/* Ab hier nichts mehr veraendern! */

error_reporting(E_ALL); //error_reporting(E_ALL ^ E_NOTICE);

@include KALPFAD.'kalWerte.php';
define('NL',"\n"); $Msg=''; $DbO=NULL; $bAdmLoginOK=false;
if(phpversion()>='5.1.0') if(defined('KAL_TimeZoneSet')) if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME'];
$sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
$sAwww='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
if(defined('KAL_Version')){
 if(defined('ADM_MitLogin')&&ADM_MitLogin){
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
  if((!isset($_SESSION['Id'])||$_SESSION['Id']!=md5(session_id())||(ADM_SessionsAgent&&$_SESSION['Ua']!=md5($sUserAgent))||(ADM_SessionsIPAddr&&$_SESSION['Ip']!=md5($sIp)))&&!strpos($sSelf,'adminLogin.php')){
   header('Location: '.$sAwww.'/adminLogin.php');
   exit;
  }else $bAdmLoginOK=true;
 }
 if(KAL_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(defined('KAL_SqlCharSet')&&KAL_SqlCharSet||defined('ADM_SqlZs')&&ADM_SqlZs) $DbO->set_charset(defined('ADM_SqlZs')&&ADM_SqlZs?ADM_SqlZs:KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
  // $DbO->query("SET sql_mode='STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE'"); // MySQL-STRICT-Modus
 }
 $sHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.KAL_Www;
}else{
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}

function fSeitenKopf($sTitel='',$sHead='',$sBar=''){
 header('Content-Type: text/html; charset=ISO-8859-1');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Kalender-Script - Administration</title>
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>
<?php if(!empty($sHead)) echo trim($sHead)."\n"?>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:<?php echo (defined('ADM_Breite')?ADM_Breite:950)?>px;"><!-- Seite -->
<div id="kopf">
 <div id="version">Version <?php $kalVersion='??'; @include(KALPFAD.'kalVersion.php'); echo trim(substr($kalVersion,0,3)).' ('.trim(substr($kalVersion,4).')')?></div>
 <h1><a href="https://www.server-scripts.de" target="_new"><img src="_kalender.gif" style="margin-bottom:-5px;" width="19" height="25" border="0" title="Kalender-Script"></a>
 Kalender-Script: <?php echo $sTitel;?></h1>
</div>
<div id="navig"><div id="navpad"><!-- Navigation -->
<ul id="menu">
<li class="rubrik">Administrator-Menü</li>
<li<?php if($sBar=='Idx') echo' class="aktiv"'?>><a href="index.php">Übersicht</a></li>
<?php if(defined('ADM_MitLogin')&&ADM_MitLogin){?>
<li<?php if($sBar=='Log') echo' class="aktiv"'?>><a href="adminLogin.php">Login/Logout</a></li>
<?php }?>
<li<?php if($sBar=='Afr') echo' class="aktiv"'?>><a href="einbettung.php">Aufruf/Einbettung</a></li>
<li><a href="<?php echo ADM_Hilfe?>LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>

<li class="rubrik">Termine verwalten</li>
<li<?php if($sBar=='TTl') echo' class="aktiv"'?>><a href="liste.php">Terminliste zeigen</a></li>
<li<?php if($sBar=='TTe') echo' class="aktiv"'?>><a href="eingabe.php">Termine eingeben</a></li>
<li<?php if($sBar=='TTf') echo' class="aktiv"'?>><a href="freigabe.php">Termine freigeben</a></li>
<?php if(defined('KAL_AendernLoeschArt')&&KAL_AendernLoeschArt>1){?>
<li<?php if($sBar=='TTL') echo' class="aktiv"'?>><a href="terminLoeschung.php">Termine löschen</a></li>
<?php }?>
<li<?php if($sBar=='TTs') echo' class="aktiv"'?>><a href="suche.php">Termine suchen</a></li>
<?php if(defined('KAL_ZusageSystem')&&KAL_ZusageSystem){?>
<li<?php if($sBar=='ZZl') echo' class="aktiv"'?>><a href="zusageListe.php">Zusagenliste</a></li>
<?php }?>

<li class="rubrik">Benutzer verwalten</li>
<li<?php if($sBar=='NNl') echo' class="aktiv"'?>><a href="nutzerListe.php">Benutzerliste</a></li>
<li<?php if($sBar=='NNs') echo' class="aktiv"'?>><a href="nutzerSuche.php">Benutzer suchen</a></li>
<li<?php if($sBar=='NaL') echo' class="aktiv"'?>><a href="nachrListen.php">Nachrichtenlisten</a></li>

<li class="rubrik">Systemeinstellungen</li>
<li<?php if($sBar=='KSu') echo' class="aktiv"'?>><a href="konfSetup.php">Setup</a></li>
<li<?php if($sBar=='KUp') echo' class="aktiv"'?>><a href="konfUpdate.php">Update</a></li>
<li<?php if($sBar=='KAd') echo' class="aktiv"'?>><a href="konfAdmin.php">Admin-Einstellungen</a></li>
<li<?php if($sBar=='KAg') echo' class="aktiv"'?>><a href="konfAllgemein.php">Allgemeines</a></li>
<li<?php if($sBar=='KLy') echo' class="aktiv"'?>><a href="konfLayout.php">Navigation/Aktionen</a></li>
<li<?php if($sBar=='KFa') echo' class="aktiv"'?>><a href="konfFarben.php">Farbeinstellungen</a></li>
<li<?php if($sBar=='KEm') echo' class="aktiv"'?>><a href="konfEmail.php">E-Mail-Einstellungen</a></li>
<li<?php if($sBar=='KDb') echo' class="aktiv"'?>><a href="konfDaten.php">Datenbasis</a></li>

<li class="rubrik">Funktionsanpassung</li>
<li<?php if($sBar=='KTs') echo' class="aktiv"'?>><a href="konfTermine.php">Terminstruktur</a></li>
<li<?php if($sBar=='KTl') echo' class="aktiv"'?>><a href="konfListe.php">Terminliste</a></li>
<li<?php if($sBar=='KMo') echo' class="aktiv"'?>><a href="konfMonat.php">Monats-/Wochenblatt</a></li>
<li<?php if($sBar=='KDt') echo' class="aktiv"'?>><a href="konfDetail.php">Termindetails</a></li>
<li<?php if($sBar=='KEg') echo' class="aktiv"'?>><a href="konfEingabe.php">Eingabeformular</a></li>
<li<?php if($sBar=='KSf') echo' class="aktiv"'?>><a href="konfSuche.php">Suchformular</a></li>
<li<?php if($sBar=='KIf') echo' class="aktiv"'?>><a href="konfInfo.php">Informationsformular</a></li>
<li<?php if($sBar=='KKo') echo' class="aktiv"'?>><a href="konfKontakt.php">Kontaktformular</a></li>
<li<?php if($sBar=='KNz') echo' class="aktiv"'?>><a href="konfNutzer.php">Benutzerfunktionen</a></li>
<li<?php if($sBar=='KEi') echo' class="aktiv"'?>><a href="konfErinner.php">Erinnerungen</a></li>
<li<?php if($sBar=='KNa') echo' class="aktiv"'?>><a href="konfBenachr.php">Benachrichtigungen</a></li>
<li<?php if($sBar=='KIC') echo' class="aktiv"'?>><a href="konfExport.php">iCal-Export</a></li>
<li<?php if($sBar=='KZs') echo' class="aktiv"'?>><a href="konfZusage.php">Zusagesystem</a></li>

<li class="rubrik">Zusatzprogramme</li>
<li<?php if($sBar=='PMK') echo' class="aktiv"'?>><a href="konfMiniKal.php">Minikalender</a></li>
<li<?php if($sBar=='PAk') echo' class="aktiv"'?>><a href="konfAktuell.php">aktuelle Termine</a></li>
<li<?php if($sBar=='PnT') echo' class="aktiv"'?>><a href="konfNeue.php">neue Termine</a></li>
<li<?php if($sBar=='PLf') echo' class="aktiv"'?>><a href="konfLaufend.php">laufende Termine</a></li>
<li<?php if($sBar=='Rss') echo' class="aktiv"'?>><a href="konfRss.php">RSS-Feed</a></li>

<li class="rubrik">Zusatzfunktionen</li>
<li<?php if($sBar=='Exp') echo' class="aktiv"'?>><a href="export.php">Terminexport</a></li>
<li<?php if($sBar=='Imp') echo' class="aktiv"'?>><a href="import.php">Terminimport</a></li>
<li<?php if($sBar=='Im2') echo' class="aktiv"'?>><a href="importVer2x.php">Import Version 2.x</a></li>
<?php if(file_exists('../autoren/index.php')){?>
<li><a href="../autoren/index.php">Autorenbereich</a></li>
<?php }?>
<li><a href="info.php" target="hilfe" onclick="hlpWin(this.href);return false;">PHP-Info</a></li>
</ul>
</div></div><!-- /Navigation -->
<div id="inhalt"><div id="inhpad"><!-- Inhalt -->

<?php
}
function fSeitenFuss(){
?>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
</div></div><!-- /Inhalt -->
<div id="fuss">&copy; <a href="https://www.kalender-script.de">Kalender-Script</a></div>
</div></div><!-- /Seite -->
</body>
</html>
<?php
}

function fKalEnCode($w,$sKey=''){
 $nCod=(int)substr(($sKey?$sKey:KAL_Schluessel),-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fKalDeCode($w,$sKey=''){
 $nCod=(int)substr(($sKey?$sKey:KAL_Schluessel),-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function Cod($u,$v){
 if(!empty($u)&&!empty($v)){
  if($u!=chr(68).'e'.'mo'){
   $S=10000; $l=strlen($u); $F=30; for($i=0;$i<$l;$i++) $S+=ord(substr($u,$i,1))*($F--);
   $Q=0; $S=(string)$S; $l=strlen($S); for($i=0;$i<$l;$i++) $Q+=(int)substr($S,$i,1);
   $Q=substr($Q,-1); if(strlen($v)<6) $v='0'.$v; $r=($S==substr($v,-5)&&$Q==substr($v,0,1));
  }elseif($v=='0'.'81'.'5') $r=1;
 }else $r=0;
 return (isset($r)?$r:0);
}
function fKalExtLink($s){
 if(!defined('KAL_ZSatzExtLink')||KAL_ZSatzExtLink==0) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode($s))));
 elseif(KAL_ZSatzExtLink==1) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s)))));
 elseif(KAL_ZSatzExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fKalErzeugeDatum($w){ //internes Speicherformat
 $nJ=2; $nM=1; $nT=0; $w=substr($w,0,10); if(($p=strrpos($w,' '))&&$p>7) $w=trim(substr($w,0,$p));
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w))))); while(count($a)<3) $a[]='';
 $a[$nJ]=(int)$a[$nJ]; $nJ=(strlen($a[$nJ])<=2?2000+$a[$nJ]:$a[$nJ]); $nM=(int)$a[$nM]; $nT=(int)$a[$nT];
 if(checkdate($nM,$nT,$nJ)) return sprintf('%04d-%02d-%02d',$nJ,$nM,$nT).rtrim(@date(' w',@mktime(12,0,0,$nM,$nT,$nJ)));
 else return false;
}
function fKalAnzeigeDatum($w){ //sichtbares Datum
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
function fKalDatumsFormat(){ //Formatschablone
 $s1=KAL_TxSymbTag; $s2=KAL_TxSymbMon; $s3=(KAL_Jahrhundert?KAL_TxSymbJhr:'').KAL_TxSymbJhr;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=KAL_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=KAL_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fSetzKalWert($w,$n,$t){
 global $sWerte, ${'ks'.$n};
 if($t=="'") $w=str_replace("'",'´',$w); ${'ks'.$n}=$w;
 if($w!=constant('KAL_'.$n)){
  $p=strpos($sWerte,'KAL_'.$n."',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'KAL_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function fSetzArray($a,$n,$t){
 global $sWerte;
 $p=strpos($sWerte,'$kal_'.$n.'='); $e=strpos($sWerte,');',$p); $p=strpos($sWerte,'array(',$p);
 if($p>0&&$e>$p){
  $k=count($a); $s=$t.$a[0].$t; for($i=1;$i<$k;$i++) $s.=','.$t.$a[$i].$t;
  if(substr($sWerte,$p+6,$e-($p+6))!=$s){
   $sWerte=substr_replace($sWerte,'array('.$s,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function txtVar($Var){return (isset($_POST[$Var])?str_replace('"',"'",stripslashes(trim($_POST[$Var]))):'');}
?>