<?php header('Content-Type: text/html; charset=ISO-8859-1')?><!DOCTYP html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Marktplatz: Segmentbild <?php $sNr=''; if(isset($_GET['seg'])) $sNr=$_GET['seg']; echo $sNr?></title>
</head>
<body>

<?php
 include('hilfsFunktionen.php'); $sBld='';
 if($sNr) if($f=opendir(MP_Pfad.MP_Daten)){
  while($s=readdir($f)) if(substr($s,0,3)==$sNr.'_'){$sBld=$s; break;} closedir($f);
 }
 if($sBld){
  echo '<div align="center"><img src="'.MPPFAD.MP_Daten.$sBld.'" border="0"></div>'."\n";
  echo '<p align="center">'.$sBld.'</p>';
 }else echo '<p align="center">kein Segmentbild</p>';

?>

</body>
</html>