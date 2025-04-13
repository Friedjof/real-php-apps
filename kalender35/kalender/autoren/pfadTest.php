<?php header('Content-Type: text/html; charset=ISO-8859-1')?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Kalender-Script - Autorenbereich</title>
<link rel="stylesheet" type="text/css" href="autoren.css">
<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
</script>
</head>

<body>
<div id="seite"><div id="rahmen" style="width:770px;">
<div id="kopf">
 <div id="version">Version <?php @include('../kalVersion.php'); echo (isset($kalVersion)?trim(substr($kalVersion,0,3)).' ('.trim(substr($kalVersion,4).')'):'unbekannt')?></div>
 <h1><a href="https://www.server-scripts.de" target="_new"><img src="_kalender.gif" style="margin-bottom:-5px;" width="19" height="25" border="0" title="Kalender-Script"></a>
 Kalender-Script - Autorenbereich: Pfadtest</h1>
</div><div id="navig"><div id="navpad" style="height:32em;">
<ul id="menu">
<li><a href="index.php">Autorenbereich</a></li>
<li><a href="https://www.server-scripts.de/kalender/hilfe.html" target="hilfe" onclick="hlpWin(this.href);return false;">Hilfe</a></li>
</ul>
</div></div><div id="inhalt"><div id="inhpad">
<?php
 $s=@implode('',@file('hilfsFunktionen.php'));
 if(($p=strpos($s,'KALPFAD'))&&($p=strpos($s,',',$p))){ //hilfsFunktionen gefunden
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
  @include $sRelPfad.'kalWerte.php';
  if(defined('KAL_Version')){ //kalWerte.php gefunden
?>
<p class="admErfo">Pfadtest - alle Installationspfade �berpr�ft!</p>
<p>Die Variablendatei des Kalenders konnte unter
<i><?php echo $sRelPfad.'kalWerte.php';?></i> eingelesen werden.</p>
<p>In dieser Variablendatei wurde �ber das Setup als Aufrufadresse
des Kalender-Scripts <i><?php echo (KAL_Www?KAL_Www:'NICHTS');?></i> eingetragen,
was mit der soeben ermittelten Adresse
<i><?php echo $sWww; if($sWww!=KAL_Www) echo ' <b>nicht</b>'; ?></i>
�bereinstimmt.</p>
<p>In die Variablendatei wurde beim Setup der physische Pfad zum Kalender-Script
<i><?php echo (KAL_Pfad?KAL_Pfad:'NICHTS');?></i> eingetragen,
der mit dem soeben ermittelten Dateipfad
<i><?php echo $sPhy; if($sPhy!=KAL_Pfad) echo ' <b>nicht</b>';?></i>
�bereinstimmt.</p>
<?php
  }else{ /* kalWerte.php nicht auswertbar  */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>kalWerte.php</i> im Programmverzeichnis des Kalender-Scripts
wurde nicht gefunden.</p>
<p>Entweder stimmt die relative Pfadangabe in der Datei <i>hilfsFunktionen.php</i>
vom Autorenordner aus hin zum Programmhauptordner des Kalender-Scripts
nicht oder die Datei <i>kalWerte.php</i> im Hauptordner des Kalender-Scripts
ist nicht vorhanden bzw. ist f�r die Autorenscripte nicht lesbar.</p>
<p>�berpr�fen Sie zun�chst die Angabe des relativen Pfades in der
Datei <i>hilfsFunktionen.php</i> im Autorenordner.
Dort ist momentan <i><?php echo ($sRelPfad?$sRelPfad:'NICHTS');?></i> eingetragen.
Damit wird die Variablendatei <i>kalWerte.php</i> des Kalenders
unter <i><?php echo $sRelPfad.'kalWerte.php';?></i> also dem
logischen Verzeichnispfad <i><?php echo $sDir.'kalWerte.php';?></i>
gesucht und dort leider nicht gefunden.
Beheben Sie zun�chst dieses Problem, indem Sie den relativen Pfad
in der Datei <i>hilfsFunktionen.php</i> gegebenenfalls korrigieren
oder die Datei <i>kalWerte.php</i> im Programmhauptordner des Kalender-Scripts
verf�gbar und f�r die Autorenscripte lesbar machen.</p>
<?php
  }
 }else{ /* hilfsFunktionen.php nicht gefunden */
?>
<p class="admFehl">Installationsfehler - Datei nicht gefunden!</p>
<p>Die Datei <i>hilfsFunktionen.php</i> im Autorenordner
<i><?php echo dirname($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);?></i>
ist nicht vorhanden oder nicht von PHP-Scripten auslesbar.
Falls die Datei vorhanden ist hat sie offensichtlich unpassende Dateirechte.
Beheben Sie zun�chst dieses Problem mit der Datei <i>hilfsFunktionen.php</i>.</p>
<?php } /* hilfsFunktionen.php nicht gefunden */ ?>
<div id="zeitangabe">--- <?php date_default_timezone_set('Europe/Berlin'); echo date('d.m.Y, H:i:s')?> ---</div>
</div></div><div id="fuss">
&copy; <a href="https://www.kalender-script.de">Kalender-Script</a>
</div></div></div>
</body>
</html>