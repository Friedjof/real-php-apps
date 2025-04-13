<?php header('Content-Type: text/html; charset=ISO-8859-1')?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Testfragen-Script - Administration</title>
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:770px;">
<div id="kopf">
 <div id="version">Version <?php @include(FRAPFAD.'fraVersion.php'); echo (isset($fraVersion)?trim(substr($fraVersion,0,3)).' ('.trim(substr($fraVersion,4).')'):'unbekannt')?></div>
 <h1><a href="https://www.server-scripts.de" target="_new"><img src="_frage.gif" style="margin-bottom:-5px;" width="16" height="24" border="0" title="Testfragen-Script"></a>
 Testfragen-Script - Administration: Pfadtest</h1>
</div><div id="navig"><div id="navpad" style="height:32em;">
<ul id="menu">
<li><a href="index.php">Administration</a></li>
<li><a href="https://www.server-scripts.de/testfragen/hilfe.html" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>
<li><a href="info.php" target="hilfe" onclick="hlpWin(this.href);return false;">PHP-Info</a></li>
</ul>
</div></div><div id="inhalt"><div id="inhpad">
<?php
 $s=@implode('',@file('hilfsFunktionen.php'));
 if(($p=strpos($s,'FRAPFAD'))&&($p=strpos($s,',',$p))){ //hilfsFunktionen gefunden
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
  @include $sRelPfad.'fraWerte.php';
  if(defined('FRA_Version')){ //fraWerte.php gefunden
?>
<p class="admErfo">Pfadtest - alle Installationspfade überprüft!</p>
<p>Die Variablendatei des Testfragen-Scripts konnte unter
<i><?php echo $sRelPfad.'fraWerte.php';?></i> eingelesen werden.</p>
<p>In dieser Variablendatei wurde über das Setup als Aufrufadresse
des Testfragen-Scripts <i><?php echo (FRA_Www?FRA_Www:'NICHTS');?></i> eingetragen,
was mit der soeben ermittelten Adresse
<i><?php echo $sWww; if($sWww!=FRA_Www) echo ' <b>nicht</b>'; ?></i>
übereinstimmt.</p>
<p>In die Variablendatei wurde beim Setup der physische Pfad zum Testfragen-Script
<i><?php echo (FRA_Pfad?FRA_Pfad:'NICHTS');?></i> eingetragen,
der mit dem soeben ermittelten Dateipfad
<i><?php echo $sPhy; if($sPhy!=FRA_Pfad) echo ' <b>nicht</b>';?></i>
übereinstimmt.</p>
<?php
  }else{ /* fraWerte.php nicht auswertbar  */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>fraWerte.php</i> im Programmverzeichnis des Testfragen-Scripts
wurde nicht gefunden.</p>
<p>Entweder stimmt die relative Pfadangabe in der Datei <i>hilfsFunktionen.php</i>
vom Administrationsordner aus hin zum Programmhauptordner des Testfragen-Scripts
nicht oder die Datei <i>fraWerte.php</i> im Hauptordner des Testfragen-Scripts
ist nicht vorhanden bzw. ist für die Administrationsscripte nicht lesbar.</p>
<p>Überprüfen Sie zunächst die Angabe des relativen Pfades in der
Datei <i>hilfsFunktionen.php</i> im Administrationsordner.
Dort ist momentan <i><?php echo ($sRelPfad?$sRelPfad:'NICHTS');?></i> eingetragen.
Damit wird die Variablendatei <i>fraWerte.php</i> des Testfragen-Scripts
unter <i><?php echo $sRelPfad.'fraWerte.php';?></i> also dem
logischen Verzeichnispfad <i><?php echo $sDir.'fraWerte.php';?></i>
gesucht und dort leider nicht gefunden.
Beheben Sie zunächst dieses Problem, indem Sie den relativen Pfad
in der Datei <i>hilfsFunktionen.php</i> gegebenenfalls korrigieren
oder die Datei <i>fraWerte.php</i> im Programmhauptordner des Testfragen-Scripts
verfügbar und für die Administrationsscripte lesbar machen.</p>
<?php
  }
 }else{ /* hilfsFunktionen.php nicht gefunden */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>hilfsFunktionen.php</i> im Administrationsordner
<i><?php echo dirname($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);?></i>
ist nicht vorhanden oder nicht von PHP-Scripten auslesbar.
Falls die Datei vorhanden ist hat sie offensichtlich unpassende Dateirechte.
Beheben Sie zunächst dieses Problem mit der Datei <i>hilfsFunktionen.php</i>.</p>
<?php } /* hilfsFunktionen.php nicht gefunden */ ?>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
</div></div><div id="fuss">
&copy; <a href="https://www.testfragen-script.de">Testfragen-Script</a>
</div></div></div>
</body>
</html>