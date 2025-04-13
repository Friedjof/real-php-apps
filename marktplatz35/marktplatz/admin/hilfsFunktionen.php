<?php
 define('MPPFAD','../');
/* ---------------------------------------------------------------
 Das ist die relative Pfadangabe,
 die vom Admin-Ordner (Backend) aus
 auf das Programmverzeichnis (Frontend) des Marktplatzes verweist
 mit einem / am Ende.
 Beispiel: define('MPPFAD','../');
 Die Angabe ist zu aendern, wenn der Admin-Ordner NICHT wie ueblich
 direkt unterhalb von marktplatz als marktplatz/admin liegt.
------------------------------------------------------------------ */

/* Ab hier nichts mehr veraendern! */

error_reporting(E_ALL); // error_reporting(E_ALL ^ E_NOTICE);

if(file_exists(MPPFAD.'mpWerte.php')) @include MPPFAD.'mpWerte.php';
define('NL',"\n"); $Meld=''; $MTyp='Fehl'; $DbO=NULL; $bAdmLoginOK=false;
if(phpversion()>='5.1.0') if(defined('MP_TimeZoneSet')) if(strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet);
if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME'];
$sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
$sAwww='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
if(defined('MP_Version')){
 if(defined('AM_MitLogin')&&AM_MitLogin){
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
  if((!isset($_SESSION['Id'])||$_SESSION['Id']!=md5(session_id())||(AM_SessionsAgent&&$_SESSION['Ua']!=md5($sUserAgent))||(AM_SessionsIPAddr&&$_SESSION['Ip']!=md5($sIp)))&&!strpos($sSelf,'adminLogin.php')){
   header('Location: '.$sAwww.'/adminLogin.php');
   exit;
  }else $bAdmLoginOK=true;
 }
 if(MP_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(defined('MP_SqlCharSet')&&MP_SqlCharSet||defined('AM_SqlZs')&&AM_SqlZs) $DbO->set_charset(AM_SqlZs?AM_SqlZs:MP_SqlCharSet);}else{$DbO=NULL; $SqE=MP_TxSqlVrbdg;}
  //$DbO->query("SET sql_mode='STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE'"); // MySQL-STRICT-Modus
 }
 $sHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.MP_Www;
}else{
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}

function fSeitenKopf($sTitel='',$sHead='',$sBar=''){
 global $nSegNo,$sSegNo,$sSegNam;
 header('Content-Type: text/html; charset=ISO-8859-1');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Marktplatz-Script - Administration</title>
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
 function Wechsel(SegNo){window.location.href='konfEigenschaft.php?seg='+SegNo;}
</script>
<?php if(!empty($sHead)) echo trim($sHead)."\n"?>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:<?php echo (defined('AM_Breite')?AM_Breite:950)?>px;"><!-- Seite -->
<div id="kopf">
 <div id="version">Version <?php $mpVersion='??'; if(file_exists(MPPFAD.'mpVersion.php')) @include(MPPFAD.'mpVersion.php'); echo trim(substr($mpVersion,0,3)).' ('.trim(substr($mpVersion,4).')')?></div>
 <h1><a href="https://www.server-scripts.de" target="_new"><img src="_markt.gif" style="margin-bottom:-5px;" width="16" height="24" border="0" title="Marktplatz-Script"></a>
 Marktplatz-Script: <?php echo $sTitel;?></h1>
</div>
<?php
 $aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); asort($aA); reset($aA); $sSegOpt='';
 if(isset($_GET['seg'])){$nSegNo=(int)$_GET['seg']; $sSegNo=sprintf('%02d',$nSegNo); $sSegNam=$aS[$nSegNo]; $sSegQs='?seg='.$nSegNo; }
 else{$nSegNo=0; $sSegNo='00'; $sSegNam='leeres Muster-Segment'; $sSegQs='';}
 foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER') $sSegOpt.='<option value="'.$k.($k!=$nSegNo?'':'" selected="selected').'">'.(substr($aS[$k],0,1)!='~'&&substr($aS[$k],0,1)!='*'?$aS[$k]:substr($aS[$k],1)).'</option>';
?>
<div id="navig"><div id="navpad"><!-- Navigation -->
<ul id="menu">
<li class="rubrik">Administrator-Menü</li>
<li<?php if($sBar=='Idx') echo' class="aktiv"'?>><a href="index.php<?php echo $sSegQs?>">Übersicht</a></li>
<?php if(defined('AM_MitLogin')&&AM_MitLogin){?>
<li<?php if($sBar=='Log') echo' class="aktiv"'?>><a href="adminLogin.php<?php echo $sSegQs?>">Login/Logout</a></li>
<?php }?>
<li<?php if($sBar=='Afr') echo' class="aktiv"'?>><a href="einbettung.php<?php echo $sSegQs?>">Aufruf/Einbettung</a></li>
<li><a href="<?php if(defined('AM_Hilfe')) echo AM_Hilfe?>LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>

<li class="rubrik">Inserate verwalten</li>
<form style="margin:0" action="konfEigenschaft.php" method="get">
<li><select name="seg" style="font-size:80%;width:11.8em;" onchange="Wechsel(this.value)" size="1"><option value="">leeres Segment</option><?php echo $sSegOpt?></select><span style="display:inline;vertical-align:middle;padding-left:2px;"><input type="image" src="ok.gif" width="15" height="15" border="0" alt="OK" title="OK" /></span></li>
</form>
<?php if(!empty($sSegQs)){?>
<li<?php if($sBar=='IIl') echo' class="aktiv"'?>><a href="liste.php<?php echo $sSegQs?>">Inserateliste</a></li>
<li<?php if($sBar=='IIe') echo' class="aktiv"'?>><a href="eingeben.php<?php echo $sSegQs?>">Inserate eingeben</a></li>
<li<?php if($sBar=='IIf') echo' class="aktiv"'?>><a href="freigeben.php<?php echo $sSegQs?>">Inserate freigeben</a></li>
<li<?php if($sBar=='IIs') echo' class="aktiv"'?>><a href="suche.php<?php echo $sSegQs?>">Inserate suchen</a></li>
<?php }?>

<li class="rubrik">Benutzer verwalten</li>
<li<?php if($sBar=='NNl') echo' class="aktiv"'?>><a href="nutzerListe.php">Benutzerliste</a></li>
<li<?php if($sBar=='NaL') echo' class="aktiv"'?>><a href="nachrListe.php">Nachrichtenlisten</a></li>

<li class="rubrik">Systemeinstellungen</li>
<li<?php if($sBar=='KSu') echo' class="aktiv"'?>><a href="konfSetup.php">Setup</a></li>
<li<?php if($sBar=='KUp') echo' class="aktiv"'?>><a href="konfUpdate.php">Update</a></li>
<li<?php if($sBar=='KAd') echo' class="aktiv"'?>><a href="konfAdmin.php">Admin-Einstellungen</a></li>
<li<?php if($sBar=='KAg') echo' class="aktiv"'?>><a href="konfAllgemein.php">Allgemeines</a></li>
<li<?php if($sBar=='KLy') echo' class="aktiv"'?>><a href="konfLayout.php">Navigation/Aktionen</a></li>
<li<?php if($sBar=='KFa') echo' class="aktiv"'?>><a href="konfFarben.php">Farbeinstellungen</a></li>
<li<?php if($sBar=='KEm') echo' class="aktiv"'?>><a href="konfEmail.php">E-Mail-Einstellungen</a></li>
<li<?php if($sBar=='KDb') echo' class="aktiv"'?>><a href="konfDaten.php">Datenbasis</a></li>
<li<?php if($sBar=='KSg') echo' class="aktiv"'?>><a href="konfSegmente.php<?php echo $sSegQs?>">Marktsegmente</a></li>

<li class="rubrik">Funktionsanpassung</li>
<li<?php if($sBar=='KSe') echo' class="aktiv"'?>><a href="konfEigenschaft.php<?php echo $sSegQs?>">Segmenteigenschaf..</a></li>
<li<?php if($sBar=='KIx') echo' class="aktiv"'?>><a href="konfIndex.php<?php echo $sSegQs?>">Startseite</a></li>
<li<?php if($sBar=='KIl') echo' class="aktiv"'?>><a href="konfListe.php<?php echo $sSegQs?>">Inserateliste</a></li>
<li<?php if($sBar=='KDt') echo' class="aktiv"'?>><a href="konfDetail.php<?php echo $sSegQs?>">Inseratedetails</a></li>
<li<?php if($sBar=='KEg') echo' class="aktiv"'?>><a href="konfEingabe.php<?php echo $sSegQs?>">Eingabeformular</a></li>
<li<?php if($sBar=='KSf') echo' class="aktiv"'?>><a href="konfSuche.php<?php echo $sSegQs?>">Suchformular</a></li>
<li<?php if($sBar=='KIf') echo' class="aktiv"'?>><a href="konfInfo.php<?php echo $sSegQs?>">Informationsformular</a></li>
<li<?php if($sBar=='KKo') echo' class="aktiv"'?>><a href="konfKontakt.php<?php echo $sSegQs?>">Kontaktformular</a></li>
<li<?php if($sBar=='KNz') echo' class="aktiv"'?>><a href="konfNutzer.php">Benutzerfunktionen</a></li>
<li<?php if($sBar=='KNa') echo' class="aktiv"'?>><a href="konfBenachr.php">Benachrichtigungen</a></li>

<li class="rubrik">Zusatzprogramme</li>
<li<?php if($sBar=='PnI') echo' class="aktiv"'?>><a href="konfNeues.php">neue Inserate</a></li>

<li class="rubrik">Zusatzfunktionen</li>
<li<?php if($sBar=='Exp') echo' class="aktiv"'?>><a href="export.php<?php echo $sSegQs?>">Inserateexport</a></li>
<li<?php if($sBar=='Imp') echo' class="aktiv"'?>><a href="import.php<?php echo $sSegQs?>">Inserateimport</a></li>
<li<?php if($sBar=='Im2') echo' class="aktiv"'?>><a href="importVer2.php">Import Version 2.x</a></li>
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
<div id="fuss">&copy; <a href="https://www.marktplatz-script.de">Marktplatz-Script</a></div>
</div></div><!-- /Seite -->
</body>
</html>
<?php
}

function fMpErzeugeDatum($w){ //internes Speicherformat
 $nJ=2; $nM=1; $nT=0;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 $nJ=(strlen($a[$nJ])<=2?2000+$a[$nJ]:(int)$a[$nJ]); $nM=(int)$a[$nM]; $nT=(int)$a[$nT];
 if(checkdate($nM,$nT,$nJ)) return sprintf('%04d-%02d-%02d',$nJ,$nM,$nT);
 else return false;
}
function fMpAnzeigeDatum($w){ //sichtbares Datum
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
function fMpDatumsFormat($n=MP_Datumsformat,$bJ=MP_Jahrhundert){
 $s1=MP_TxSymbTag; $s2=MP_TxSymbMon; $s3=($bJ?MP_TxSymbJhr:'').MP_TxSymbJhr;
 switch($n){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=MP_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=MP_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fMpEnCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fMpDeCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fSetzMPWert($w,$n,$t){
 global $sWerte, ${'mp'.$n};
 if($t=="'") $w=str_replace("'",'´',$w); ${'mp'.$n}=$w;
 if($w!=constant('MP_'.$n)){
  $p=strpos($sWerte,'MP_'.$n."',"); $e=strrpos(substr($sWerte,0,strpos($sWerte,"\n",$p)),')');
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'MP_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function txtVar($Var){return (isset($_POST[$Var])?str_replace('"',"'",stripslashes(trim($_POST[$Var]))):'');}
?>