<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 

@include('kalWerte.php');
$nId=(isset($_GET['kal_Id'])?(int)$_GET['kal_Id']:0);
if(defined('KAL_Version')&&$nId>0){
 if(defined('KAL_WarnMeldungen')&&!KAL_WarnMeldungen) error_reporting(E_ALL ^ E_NOTICE);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 $aR=array();
 if(!KAL_SQL){ //Termin holen
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$nId.';'; $l=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$l)==$s){ //gefunden
   $aR=explode(';',rtrim($aD[$i])); array_splice($aR,1,1); break;
 }}else{
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$nId.'"')){
    $aR=$rR->fetch_row(); array_splice($aR,1,1); $rR->close();
 }}}

 $sDom=(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost'));
 $sOut ="BEGIN:VCALENDAR\r\n";
 $sOut.="VERSION:2.0\r\n";
 $sOut.="PRODID:-//Kalender-Script//NONSGML Cal V.3//EN\r\n";
 $sOut.="CALSCALE:GREGORIAN\r\n";
 $sOut.="METHOD:PUBLISH\r\n";
 $sOut.="X-WR-CALNAME:".(KAL_ICalName?KAL_ICalName:'Termin').(KAL_ICalNamNr?'_'.$nId:'')."\r\n";
 $sOut.="X-WR-TIMEZONE:".KAL_CalTZID."\r\n";
 $sOut.="BEGIN:VTIMEZONE\r\nTZID:".KAL_CalTZID."\r\nX-LIC-LOCATION:".KAL_CalTZID."\r\n";
 $sOut.="BEGIN:DAYLIGHT\r\nTZOFFSETFROM:+0100\r\nTZOFFSETTO:+0200\r\nDTSTART:19700329T020000\r\nRRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3\r\nTZNAME:CEST\r\nEND:DAYLIGHT\r\n";
 $sOut.="BEGIN:STANDARD\r\nTZOFFSETFROM:+0200\r\nTZOFFSETTO:+0100\r\nDTSTART:19701025T030000\r\nRRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r\nTZNAME:CET\r\nEND:STANDARD\r\n";
 $sOut.="END:VTIMEZONE\r\n";

 $sSec=(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s');
 if(!$s=KAL_CalURL) $s='http'.$sSec.'://'.KAL_Www.'kalender.php?';
 else{$s.=(!strpos(s,'?')?'?':'&amp;'); if(!strpos($s,'://')) $s='http'.$sSec.'://'.$sDom.$s;}
 $sUrl=$s.'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$nId;
 $sDatT=date('Ymd'); $sDatZ=date('Hi').'00';
 $sOut.="BEGIN:VEVENT\r\nUID:termin_".$nId.'@'.$sDom."\r\nCLASS:".(strlen(KAL_CalCLASS)?KAL_CalCLASS:'PUBLIC')."\r\n";
 if($nFlds=count($aR)){
  $s=$sDatT; $u=$s; $t=(KAL_CalGanzTag>0?sprintf('%02d',KAL_CalGanzTag).'00':''); $v='';
  if(KAL_CalDTSTART>0&&($s=str_replace('-','',substr((isset($aR[KAL_CalDTSTART])?$aR[KAL_CalDTSTART]:''),0,10)))){ //Startdatum da
   if(KAL_CalZTSTART>0&&($w=str_replace(':','',substr((isset($aR[KAL_CalZTSTART])?$aR[KAL_CalZTSTART]:''),0,5)))) $t=$w; //Startzeit
   if(KAL_CalDTEND>0&&($u=str_replace('-','',substr((isset($aR[KAL_CalDTEND])?$aR[KAL_CalDTEND]:''),0,10)))){ //Endedatum da
    if(KAL_CalZTEND>0&&($w=str_replace(':','',substr((isset($aR[KAL_CalZTEND])?$aR[KAL_CalZTEND]:''),0,5)))) $v=$w; //Endezeit da
    else{ //Endedatum ohne Endezeit
     if(KAL_CalStdDauer>0&&$s==$u){ //endet am selben Tag
      $v=substr($t,0,2)+KAL_CalStdDauer;
      if($v<24) $v=sprintf('%02d',$v).substr($t,2,2);
      else{$v=''; $u=date('Ymd',@mktime(8,8,8,substr($u,4,2),substr($u,6,2),substr($u,0,4))+86400);}
     }else{$v=''; $u=date('Ymd',@mktime(8,8,8,substr($u,4,2),substr($u,6,2),substr($u,0,4))+86400);} //dauert bis anderen Tag ganztags
    }
   }else{$u=$s; //kein Endedatum
    if(KAL_CalZTEND>0&&($w=str_replace(':','',substr((isset($aR[KAL_CalZTEND])?$aR[KAL_CalZTEND]:''),0,5)))) $v=$w; //trotzdem Endezeit da
    else{ //Endezeitersatz
     if(KAL_CalStdDauer>0&&strlen($t)){ //Stunden nach Beginn
      $v=substr($t,0,2)+KAL_CalStdDauer;
      if($v<24) $v=sprintf('%02d',$v).substr($t,2,2);
      else{$v=sprintf('%02d',$v-24).substr($t,2,2); $u=date('Ymd',@mktime(8,8,8,substr($s,4,2),substr($s,6,2),substr($s,0,4))+86400);}
     }else{$v=''; $u=date('Ymd',@mktime(8,8,8,substr($s,4,2),substr($s,6,2),substr($s,0,4))+86400);} //ganztags
  }}}else $s=$sDatT; //kein Startdatum
  $sOut.='DTSTART;TZID='.KAL_CalTZID.':'.$s.($t?'T'.$t.'00':'')."\r\nDTEND;TZID=".KAL_CalTZID.':'.$u.($v?'T'.$v.'00':'')."\r\n";
  if(KAL_CalCREATED>=0){
   if(KAL_CalCREATED>0&&($s=(isset($aR[KAL_CalCREATED])?$aR[KAL_CalCREATED]:''))){$t=sprintf('%04d',str_replace(':','',substr($s,11,5))); $s=str_replace('-','',substr($s,0,10));}else{$s=$sDatT; $t=$sDatZ;}
   $sOut.="CREATED:".$s."T".$t."Z\r\n";
  }
  if(KAL_CalMODIFIED>=0){
   if(KAL_CalMODIFIED>0&&($s=(isset($aR[KAL_CalMODIFIED])?$aR[KAL_CalMODIFIED]:''))){$t=sprintf('%04d',str_replace(':','',substr($s,11,5))); $s=str_replace('-','',substr($s,0,10));}else{$s=$sDatT; $t=$sDatZ;}
   $sOut.="LAST-MODIFIED:".$s."T".$t."Z\r\n";
  }
  if(KAL_CalDTSTAMP>=0){
   if(KAL_CalDTSTAMP>0&&($s=(isset($aR[KAL_CalDTSTAMP])?$aR[KAL_CalDTSTAMP]:''))){$t=sprintf('%04d',str_replace(':','',substr($s,11,5))); $s=str_replace('-','',substr($s,0,10));}else{$s=$sDatT; $t=$sDatZ;}
   $sOut.="DTSTAMP:".$s."T".$t."Z\r\n";
  }
  if(KAL_CalSUMMARY>0) $s=fKalICalTx((isset($aR[KAL_CalSUMMARY])?$aR[KAL_CalSUMMARY]:'')); else $s='';
  if(KAL_CalSUMMARY2>0&&($t=fKalICalTx((isset($aR[KAL_CalSUMMARY2])?$aR[KAL_CalSUMMARY2]:'')))) $s=trim($s.KAL_CalBindeSUMMA.$t);
  if(strlen($s)>0) $sOut.="SUMMARY:".str_replace('\n',' ',$s)."\r\n"; else $sOut.="SUMMARY:kein Name\r\n";
  if(KAL_CalDESCRIPTION>0) $s=fKalICalTx((isset($aR[KAL_CalDESCRIPTION])?$aR[KAL_CalDESCRIPTION]:'')); else $s='';
  if(KAL_CalDESCRIPTION2>0&&($t=fKalICalTx((isset($aR[KAL_CalDESCRIPTION2])?$aR[KAL_CalDESCRIPTION2]:'')))) $s=trim($s.KAL_CalBindeDESCR.$t);
  if(strlen($s)>0) $sOut.="DESCRIPTION:".(KAL_CalLenDESCR<=0||strlen($s)<=KAL_CalLenDESCR?$s:substr($s,0,KAL_CalLenDESCR).'...').'\n\n'.$sUrl."\r\n";
  if(KAL_CalLOCATION>0) $s=fKalICalTx((isset($aR[KAL_CalLOCATION])?$aR[KAL_CalLOCATION]:'')); else $s='';
  if(KAL_CalLOCATION2>0&&($t=fKalICalTx((isset($aR[KAL_CalLOCATION2])?$aR[KAL_CalLOCATION2]:'')))) $s=trim($s.KAL_CalBindeLOCAT.$t);
  if(strlen($s)>0) $sOut.="LOCATION:".$s."\r\n";
  if(KAL_CalGEO>0&&($s=(isset($aR[KAL_CalGEO])?$aR[KAL_CalGEO]:''))){
   $a=explode(',',$s);
   $sOut.="GEO:".(isset($a[2])?$a[2]:KAL_GMapBZentr).';'.(isset($a[3])?$a[3]:KAL_GMapLZentr)."\r\n";
  }
  if(KAL_CalCATEGORIES>0&&($s=fKalICalTx((isset($aR[KAL_CalCATEGORIES])?$aR[KAL_CalCATEGORIES]:'')))) $sOut.="CATEGORIES:".$s."\r\n";
  if(KAL_CalORGANIZER>0&&($s=(isset($aR[KAL_CalORGANIZER])?$aR[KAL_CalORGANIZER]:''))) $sOut.="ORGANIZER:mailto:".(KAL_SQL?$s:fKalIDecode($s))."\r\n";
  if(KAL_CalATTACH>0&&($s=(isset($aR[KAL_CalATTACH])?$aR[KAL_CalATTACH]:''))) $sOut.="ATTACH:http'.$sSec.'://".$sDom.'/'.iconv('ISO-8859-1','UTF-8',$s)."\r\n";
  $sOut.="URL;VALUE=URI:".$sUrl."\r\n";
  if(strlen(KAL_CalSTATUS)>0) $sOut.="STATUS:".KAL_CalSTATUS."\r\n";
  if(strlen(KAL_CalTRANSP)>0) $sOut.="TRANSP:".KAL_CalTRANSP."\r\n";
 }else{
  $sOut.="DTSTART:".$sDatT."T090000\r\nDTEND:".$sDatT."T100000\r\nDTSTAMP:".$sDatT."T".$sDatZ."Z\r\n";
  $sOut.="SUMMARY:Termindaten fehlen\r\n";
 }
 $sOut.="END:VEVENT\r\n";

 $sOut.="END:VCALENDAR\r\n";
 $nSiz=strlen($sOut);
 header('Pragma: public');
 header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
 header('Expires: 0');
 header('Vary: Accept-Encoding');
 header('Content-Disposition: attachment; filename="termin_'.$nId.'.ics"');
 header('Content-Length: '.$nSiz);
 header('Content-Type: text/calendar; charset=UTF-8');
 header('Content-Language: de-DE');
 echo $sOut;
}else echo 'bad request';

function fKalIDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fKalICalTx($t){
 for($j=strlen($t)-1;$j>=0;$j--) //BB-Code weg
  if(substr($t,$j,1)=='[') if($v=strpos($t,']',$j)) $t=substr_replace($t,'',$j,++$v-$j);
 $t=str_replace("\n",'\n',str_replace('\n ','\n',str_replace("\r",'',str_replace('`,',';',$t))));
 return iconv('ISO-8859-1','UTF-8',$t);
}
?>