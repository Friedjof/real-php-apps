<?php

function fLogTln($nStat,$sArt,$sAw,$sSes,$DbO){
 if(!UMF_SQL){$s=''; //Text-Datei
  if($sArt=='T'&&$sSes){ //Teilnehmer
   if($a=file(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses')) if($a=explode(';',rtrim($a[0]))){
    foreach($a as $t) $s.=fUmfEnCode($t).','; $s=substr($s,0,-1);
  }}elseif($sArt=='N'&&$sSes){ //Benutzer
   $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sNId=sprintf('%d',substr($sSes,4,5)); $l=strlen($sNId);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$l)==$sNId){ //gefunden
    $a=explode(';',rtrim($aD[$i]),6); $s=$a[0].(isset($a[2])?','.$a[2]:'').(isset($a[4])?','.$a[4]:''); break;
  }}
  $s=date('Y-m-d H:i:s').';'.$nStat.';'.$sArt.';'.$s.';'.$sAw."\n";
  $s=rtrim(implode('',@file(UMF_Pfad.UMF_Daten.UMF_Teilnahme)))."\n".$s;
  if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Teilnahme,'w')){fwrite($f,$s); fclose($f);}
 }elseif($DbO){$s=''; // ToDo:
  if($sArt=='T'&&$sSes){ //Teilnehmer
   if($a=file(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses')) if($a=explode(';',rtrim($a[0]))){
    foreach($a as $t) $s.=$t.','; $s=substr($s,0,-1);
   }
  }elseif($sArt=='N'&&$sSes){ //Benutzer
   if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.sprintf('%d',substr($sSes,4,5)).'"')){
    if($a=$rR->fetch_row()) $s=$a[0].(isset($a[2])?','.$a[2]:'').(isset($a[4])?','.$a[4]:''); $rR->close();
   }
  }
  $DbO->query('INSERT IGNORE INTO '.UMF_SqlTabT.' (Datum,Status,Art,Nutzer,Ergebnis) VALUES("'.date('Y-m-d H:i:s').'","'.$nStat.'","'.$sArt.'","'.$s.'","'.$sAw.'")');
 }
 return true;
}

if(!function_exists('fUmfEnCode') ){ //bei direktem Aufruf
 function fUmfEnCode($w){
  $nCod=(int)substr(UMF_Schluessel,-2); $s='';
  for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
  return $s;
 }
}
?>