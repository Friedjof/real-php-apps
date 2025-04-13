<?php
 header('Content-Type: text/html; charset=ISO-8859-1');
 if(phpversion()>='5.1.0') date_default_timezone_set('Europe/Berlin');
 $s=@implode('',@file('hilfsFunktionen.php')); $sRelPfad='../';
 if(($p=strpos($s,'UMFPFAD'))&&($p=strpos($s,',',$p))){
  $t=trim(substr($s,$p,255)); $p=strpos($t,"'"); $q=strpos($t,'"');
  if($p>0&&($q==0||$q>$p)){$t=substr($t,$p+1); if($p=strpos($t,"'")) $sRelPfad=substr($t,0,$p);}
  elseif($q>0&&($p==0||$p>$q)){$t=substr($t,$q+1); if($p=strpos($t,'"')) $sRelPfad=substr($t,0,$p);}
  define('UMFPFAD',$sRelPfad);
 }
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Umfrage-Script - Autorenbereich</title>
<link rel="stylesheet" type="text/css" href="autoren.css">
<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:770px;">
<div id="kopf">
 <div id="version">Version <?php @include(UMFPFAD.'umfVersion.php'); echo (isset($umfVersion)?trim(substr($umfVersion,0,3)).' ('.trim(substr($umfVersion,4).')'):'unbekannt')?></div>
 <h1><a href="http://www.server-scripts.de" target="_new"><img src="_frage.gif" style="margin-bottom:-5px;" width="16" height="24" border="0" title="Umfrage-Script"></a>
 Umfrage-Script - Autorenbereich: Pfadtest</h1>
</div><div id="navig"><div id="navpad" style="height:32em;">
<ul id="menu">
<li><a href="index.php">Autorenmenü</a></li>
<li><a href="http://www.server-scripts.de/umfrage/hilfe.html" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>
<li><a href="info.php" target="hilfe" onclick="hlpWin(this.href);return false;">PHP-Info</a></li>
</ul>
</div></div><div id="inhalt"><div id="inhpad">
<?php
 if(($p=strpos($s,'UMFPFAD'))&&($p=strpos($s,',',$p))){ //hilfsFunktionen gefunden
  $s=trim(substr($s,$p,255));
  $p=strpos($s,"'"); $q=strpos($s,'"');
  if($p>0&&($q==0||$q>$p)){
   $s=substr($s,$p+1); if($p=strpos($s,"'")) $sRelPfad=substr($s,0,$p);
  }elseif($q>0&&($p==0||$p>$q)){
   $s=substr($s,$q+1); if($p=strpos($s,'"')) $sRelPfad=substr($s,0,$p);
  }
  $sP=$sRelPfad; if(!$sDir=$_SERVER['PHP_SELF']) $sDir=$_SERVER['SCRIPT_NAME'];
  $sDir=rtrim(str_replace("\\",'/',str_replace("\\\\",'/',dirname($sDir))),'/');
  $sPhy=rtrim(str_replace("\\",'/',str_replace("\\\\",'/',dirname(realpath('pfadTest.php')))),'/');
  while($p=strpos('#'.$sP,'../')){
   $sP=substr($sP,$p);
   if($p=strrpos('#'.$sDir,'/')) $sDir=substr($sDir,0,$p-1);
   if($p=strrpos('#'.$sPhy,'/')) $sPhy=substr($sPhy,0,$p-1);
  }
  $sPhy.='/'; $sDir.=substr($sP,1);
  $sWww=($_SERVER['HTTP_HOST']?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).$sDir;
  @include $sRelPfad.'umfWerte.php';
  if(defined('UMF_Version')){ //umfWerte.php gefunden
?>
<p class="admErfo">Pfadtest - alle Installationspfade überprüft!</p>
<p>Die Variablendatei des Umfrage-Scripts konnte unter
<i><?php echo $sRelPfad.'umfWerte.php';?></i> eingelesen werden.</p>
<p>In dieser Variablendatei wurde über das Setup als Aufrufadresse
des Umfrage-Scripts <i><?php echo (UMF_Www?UMF_Www:'NICHTS');?></i> eingetragen,
was mit der soeben ermittelten Adresse
<i><?php echo $sWww; if($sWww!=UMF_Www) echo ' <b>nicht</b>'; ?></i>
übereinstimmt.</p>
<p>In die Variablendatei wurde beim Setup der physische Pfad zum Umfrage-Script
<i><?php echo (UMF_Pfad?UMF_Pfad:'NICHTS');?></i> eingetragen,
der mit dem soeben ermittelten Dateipfad
<i><?php echo $sPhy; if($sPhy!=UMF_Pfad) echo ' <b>nicht</b>';?></i>
übereinstimmt.</p>
<?php
  }else{ /* umfWerte.php nicht auswertbar  */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>umfWerte.php</i> im Programmverzeichnis des Umfrage-Scripts
wurde nicht gefunden.</p>
<p>Entweder stimmt die relative Pfadangabe in der Datei <i>hilfsFunktionen.php</i>
vom Autorenordner aus hin zum Programmhauptordner des Umfrage-Scripts
nicht oder die Datei <i>umfWerte.php</i> im Hauptordner des Umfrage-Scripts
ist nicht vorhanden bzw. ist für die Autorenscripte nicht lesbar.</p>
<p>Überprüfen Sie zunächst die Angabe des relativen Pfades in der
Datei <i>hilfsFunktionen.php</i> im Autorenordner.
Dort ist momentan <i><?php echo ($sRelPfad?$sRelPfad:'NICHTS');?></i> eingetragen.
Damit wird die Variablendatei <i>umfWerte.php</i> des Umfrage-Scripts
unter <i><?php echo $sRelPfad.'umfWerte.php';?></i> also dem
logischen Verzeichnispfad <i><?php echo $sDir.'umfWerte.php';?></i>
gesucht und dort leider nicht gefunden.
Beheben Sie zunächst dieses Problem, indem Sie den relativen Pfad
in der Datei <i>hilfsFunktionen.php</i> gegebenenfalls korrigieren
oder die Datei <i>umfWerte.php</i> im Programmhauptordner des Umfrage-Scripts
verfügbar und für die Autorenscripte lesbar machen.</p>
<?php
  }
 }else{ /* hilfsFunktionen.php nicht gefunden */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>hilfsFunktionen.php</i> im Autorenordner
<i><?php echo dirname($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);?></i>
ist nicht vorhanden oder nicht von PHP-Scripten auslesbar.
Falls die Datei vorhanden ist hat sie offensichtlich unpassende Dateirechte.
Beheben Sie zunächst dieses Problem mit der Datei <i>hilfsFunktionen.php</i>.</p>
<?php } /* hilfsFunktionen.php nicht gefunden */ ?>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
</div></div><div id="fuss">
&copy; <a href="http://www.server-script.de/software">Umfrage-Script</a>
</div></div></div>
</body>
</html>