<?php
error_reporting(E_ALL); date_default_timezone_set('Europe/Berlin');
$sUkErr='fehlerhafter Aufruf!'; $bUkOK=false; $bSQLOpen=false;

if($sSes=trim(isset($_GET['ses'])?$_GET['ses']:'')){
 if($sAbl=trim(isset($_GET['abl'])?$_GET['abl']:'')) $sAbl=(int)$sAbl;
 include('fraWerte'.$sAbl.'.php');
 if(defined('FRA_Version')){
  $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $bUkOK=true;
   if(FRA_SQL){
    if(!(isset($DbO)&&$DbO)){
     mysqli_report(MYSQLI_REPORT_OFF);
     $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
     if(!mysqli_connect_errno()){
      $bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
     }else{$sUkErr='Keine Verbindung zur SQL-Datenbank!'; $bUkOK=false;}
    }else $bSQLOpen=true;
   }
  }else $sUkErr=FRA_TxSessionZeit; else $sUkErr=FRA_TxSessionUngueltig;
 }else $sUkErr='fehlerhafter Aufruf ohne Konfigurationsdatei.';
}else $sUkErr='fehlerhafter Aufruf ohne Sitzungskennung.';

if($bUkOK){ //PDF erzeugen
 require_once(FRA_Pfad.'urkunde.inc.php');
}
if($bUkOK){ //PDF erzeugen
 $pdf->Output(FRA_UkDatei.'.pdf','I');
}else{
 header('Content-Type: text/html; charset=ISO-8859-1');
 echo '<!DOCTYPE html>
<html>
<head>
<title>Fehlerseite</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
</head>

<body>
<h2 style="text-align:center">Fehlerseite!</h2>
<p style="text-align:center;color:#e42"><b>'.$sUkErr.'</b></p>
<hr size="1" width="96%">
<p style="text-align:center">'.date('d.m.Y H:i:s').'</p>
</body>
</html>
';
}
?>