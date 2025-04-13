<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>Multiple-Choice mit dem Testfragen-Script</title>
</head>

<body class="admDruck">
<h3 align="center">Video/Audio-Vorschau</h3>

<div align="center">
<?php
 if($sF=(isset($_GET['f'])?trim($_GET['f']):'')){
  $sExt=strtolower(substr($sF,strrpos($sF,'.')+1));
  if($sExt=='mp4'||$sExt=='ogg'||$sExt=='ogv'||$sExt=='webm'){
   if($sExt=='ogv') $sExt='ogg';
   echo '<video controls type="video/'.$sExt.'" src="../'.$sF.'">Video</video>';
  }elseif($sExt=='mp3'||$sExt=='ogg'){
   echo '<audio controls type="audio/'.$sExt.'" src="../'.$sF.'">Audio</audio>';
  }else echo '<p>ungueltiger Dateityp</p>';
 }else echo '<p>ungueltiger Aufruf</p>';
?>
</div>

</body>
</html>