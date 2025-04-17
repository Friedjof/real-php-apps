<?php
header('Content-Type: text/plain; charset=ISO-8859-1');
// Muster: https://sitemaps.org/protocol.html#sitemapXMLExample und https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF);

define('MP_SitemapDest','');  // sitemap.xml wenn leer, oder eigener [pfad/]dateiname.xml
define('MP_SitemapURL','');   // Markt-URL wenn leer, oder individuelle Adresse, an die die Marktparameter angehaengt werden
define('MP_SitemapImages',3); // Anzahl der Bilder pro Inserat im Sitemap, Wertebereich 0....3
define('MP_SmIdxPriority','0.95'); define('MP_SmIdxChngeFrq','weekly');
define('MP_SmLstPriority','0.90'); define('MP_SmLstChngeFrq','daily');
define('MP_SmDtlPriority','0.80'); define('MP_SmDtlChngeFrq','weekly');
define('MP_SmToDesktop',true);

if(isset($_GET['mp'])){
 include('./mpWerte.php');
 if(phpversion()>='5.1.0') if(strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet);
 if($_GET['mp']==MP_Schluessel){

 // --- Anfang SitemapGenerator ---
 $sMpHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'; define('MP_UrlSm',$sMpHttp.MP_Www);
 $sRes='Sitemap-Generator gestartet: '.date('H:i:s')."\n"; $X=''; $sRefDat=date('Y-m-d'); $bDo=false; $bImg=false;

 $bSQLOpen=false; $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; echo "\n".MP_TxSqlVrbdg;}
 }

 $aSeg=array(); $aSeg=explode(';',MP_Segmente); $nSegmente=count($aSeg);
 $X.="\n<url>";
 $X.="\n\t<loc>".(empty(MP_SitemapURL)?MP_UrlSm.'marktplatz'.(MP_Sef?'.html':'.php'):MP_SitemapURL).'</loc>';
 $X.="\n\t<lastmod>".$sRefDat.'</lastmod>';
 $X.="\n\t<changefreq>".MP_SmIdxChngeFrq."</changefreq>";
 $X.="\n\t<priority>".MP_SmIdxPriority."</priority>";
 $X.="\n</url>";
 //ueber alle Segmente
 for($nSegNo=1;$nSegNo<$nSegmente;$nSegNo++) if(($sSegNam=$aSeg[$nSegNo])&&($sSegNam!='LEER')){
  $sSegNo=sprintf('%02d',$nSegNo); $sMpSegName=$aSeg[$nSegNo]; if(substr($sMpSegName,0,1)=='*') $sMpSegName=substr($sMpSegName,1);
  if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;} $sSegDat=$sRefDat; $nIndex=1; $sFtIdx='?'; $bFtIdxIsTxt=false; $nDateDif=0;
  //Struktur holen
  $nFelder=0; $nSpalten=0; $nTitPos=0; $nSefPos=0; $aD=array(); $aStru=array(); $aMpFN=array(); $aMpFT=array(); $aMpLF=array(); $aMpSpalten=array(); $nDatPos=0; $nImg1Pos=$nImg2Pos=$nImg3Pos=0;
  if(!MP_SQL){ //Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
  }elseif($bSQLOpen){ //SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSegNo.'"')){
    $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
  if(count($aStru)>1){
   $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
   if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
   $aMpFT=explode(';',rtrim($aStru[1])); $nIndex=substr($aMpFT[0],14); $aMpFT[0]='i'; $aMpFT[1]='d'; $sFtIdx=$aMpFT[$nIndex]; if(strpos('#tamks',$sFtIdx)) $bFtIdxIsTxt=true; $nDatPos=array_search('@',$aMpFT);
   $aMpLF=explode(';',rtrim($aStru[2])); $aMpLF[0]=substr($aMpLF[0],14,1);
   $aMpET=explode(';',rtrim($aStru[15])); $nDateDif=86400*$aMpET[1];
   for($i=0;$i<$nFelder;$i++){
    $aMpSpalten[$aMpLF[$i]]=$i; $t=$aMpFT[$i];
    if($t=='b') if($nImg1Pos==0&&MP_SitemapImages>0) $nImg1Pos=$i; elseif($nImg2Pos==0&&MP_SitemapImages>1) $nImg2Pos=$i; elseif($nImg3Pos==0&&MP_SitemapImages>2) $nImg3Pos=$i;
   }
   $nSpalten=count($aMpSpalten); for($i=1;$i<$nSpalten;$i++) if($nSefPos==0) if($aMpFT[$aMpSpalten[$i]]=='t') $nSefPos=$aMpSpalten[$i];
   if($nTitPos=array_search('TITLE',$aMpFN)) if($aMpFT[$nTitPos]!='t'&&$aMpFT[$nTitPos]!='a') $nTitPos=0;
  }
  if(!MP_SQL){ //Inserate holen
   $sSegDat=date('Y-m-d',filemtime(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)); // Aenderungszeit
   $aT=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aT); // Inserate
   for($i=1;$i<$nSaetze;$i++){
    $a=explode(';',rtrim($aT[$i]));
    if($a[2]>=$sRefDat&&$a[1]=='1'){
     array_splice($a,1,1);
     $s=$a[$nIndex];
     if($bFtIdxIsTxt){
      $s=strtoupper(strip_tags($s));
      for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
      if(strlen($s)>32) $s=substr($s,0,32);
     }elseif($sFtIdx=='w') $s=sprintf('%09.2f',1+$s); elseif($sFtIdx=='n'||$sFtIdx=='i') $s=sprintf('%07d',1+$s);
     elseif($sFtIdx=='1'||$sFtIdx=='2'||$sFtIdx=='3'||$sFtIdx=='r') $s=sprintf('%010.3f',1+$s);
     $aD[$s.chr(255).sprintf('%0'.MP_NummerStellen.'d',$a[0])]=$a;
    }
   }
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SHOW TABLE STATUS WHERE NAME="'.str_replace('%',$sSegNo,MP_SqlTabI).'"')){ // Aenderungszeit
    if($a=$rR->fetch_assoc()){
     if(!$s=$a['Update_time']) if(!$s=$a['Create_time']) $s=$sRefDat; $sSegDat=substr($s,0,10);
    }$rR->close();
   }
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1>="'.$sRefDat.'" AND online="1" ORDER BY nr')){ // Inserate
    while($a=$rR->fetch_row()){
     array_splice($a,1,1);
     $s=$a[$nIndex];
     if($bFtIdxIsTxt){
      $s=strtoupper(strip_tags($s));
      for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
      if(strlen($s)>32) $s=substr($s,0,32);
     }elseif($sFtIdx=='w') $s=sprintf('%09.2f',1+$s); elseif($sFtIdx=='n'||$sFtIdx=='i') $s=sprintf('%07d',1+$s);
     elseif($sFtIdx=='1'||$sFtIdx=='2'||$sFtIdx=='3'||$sFtIdx=='r') $s=sprintf('%010.3f',1+$s);
     $aD[$s.chr(255).sprintf('%0'.MP_NummerStellen.'d',$a[0])]=$a;
    }$rR->close();
   }
  }
  if(MP_Rueckwaerts) krsort($aD); else ksort($aD);
  $nZhl=count($aD); $n=max(ceil($nZhl/MP_ListenLaenge),1); // Index-Seite(n) des Segments
  for($i=1;$i<=$n;$i++){
   $X.="\n<url>";
   $X.="\n\t<loc>".fHrefLSm($nSegNo,$i,$sSegNam).'</loc>';
   $X.="\n\t<lastmod>".$sSegDat.'</lastmod>';
   $X.="\n\t<changefreq>".MP_SmLstChngeFrq."</changefreq>";
   $X.="\n\t<priority>".MP_SmLstPriority."</priority>";
   $X.="\n</url>";
  }
  $n=0;
  foreach($aD as $a){
   if($nDatPos) $sDetDat=substr($a[$nDatPos],0,10); else $sDetDat=''; // Aenderungsdatum
   if($sDetDat=='') $sDetDat=date('Y-m-d',mktime(8,0,0,substr($a[1],5,2),substr($a[1],8,2),substr($a[1],0,4))-$nDateDif);
   if($sDetDat>$sRefDat||$sDetDat=='') $sDetDat=$sRefDat; if($sDetDat>$sSegDat) $sDetDat=$sSegDat; $sImg='';
   if(!MP_Sef) $sSefName='';
   else{ //SEF-Dateinamen bilden
    $sSefName=$sMpSegName;
    if($nTitPos>0&&($s=$a[$nTitPos])) $sSefName=fMpDtS(str_replace('�','',str_replace('�','',$s))); //zusaetzliche Titelspalte
    elseif($nSefPos) $sSefName=fMpDtS(str_replace('�','',str_replace('�','',$a[$nSefPos]))); //SEF-Suchen
    for($k=strlen($sSefName)-1;$k>=0;$k--) //BB-Code weg
     if(substr($sSefName,$k,1)=='[') if($v=strpos($sSefName,']',$k)) $sSefName=substr_replace($sSefName,'',$k,++$v-$k);
    $sSefName=trim(substr(str_replace("\n",' ',str_replace("\r",'',$sSefName)),0,50));
    $sSefName=trim(str_replace('.','_',trim(str_replace(':','',str_replace(';','',$sSefName)))));
   }
   $sBldId=$sBldDir.$a[0].$sBldSeg;
   if($nImg1Pos>0) if($s=$a[$nImg1Pos]) $sImg=fImgLoc($s,$sBldId); if($nImg2Pos>0) if($s=$a[$nImg2Pos]) $sImg.=fImgLoc($s,$sBldId); if($nImg3Pos>0) if($s=$a[$nImg3Pos]) $sImg.=fImgLoc($s,$sBldId);
   $X.="\n<url>";
   $X.="\n\t<loc>".fHrefDSm($nSegNo,ceil(++$n/MP_ListenLaenge),$a[0],$sSefName).'</loc>'.$sImg; if($sImg>'') $bImg=true;
   $X.="\n\t<lastmod>".$sDetDat.'</lastmod>';
   $X.="\n\t<changefreq>".MP_SmDtlChngeFrq."</changefreq>";
   $X.="\n\t<priority>".MP_SmDtlPriority."</priority>";
   $X.="\n</url>";
  }
  $sRes.="\nSegment ".$sSegNo.' ('.$sSegNam.') mit '.$nZhl.' Inserate'.($nZhl!=1?'n':'');
 }
 $X='<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.($bImg?' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"':'').'>'.$X."\n".'</urlset>';

 if($bSQLOpen) if($DbO) $DbO->close(); //SQL schliessen

 $sFN=(MP_SitemapDest?MP_SitemapDest:'sitemap.xml');
 if($f=fopen($sFN,'w')){fwrite($f,$X);  $sRes.="\nSitemapdatei \"".$sFN.'" wurde geschrieben';}else $sRes.="\nSchreibfehler Sitemapdatei ".$sFN;

 $sRes.="\n\nSitemap-Generator beendet: ".date('H:i:s')."\n"; echo $sRes;
 // --- Ende Cronjob ---
 if(MP_CronMail&&$bDo){
  require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
  if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
  $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); $Mailer->Subject='Marktplatz-Sitemap-Generator '.date('d.m.y');
  if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
  $Mailer->AddTo(MP_MailTo); $Mailer->SetReplyTo(MP_MailTo);
  $Mailer->Text=$sRes; $Mailer->Send();
 }
 if(MP_SmToDesktop) echo "\n\n".$X."\n";

 }else echo "\n".'unberechtigter Aufruf: Geheimschluessel falsch!';
}else echo "\n".'unvollstaendige Aufrufadresse!';

function fMpDtS($s){ //DatenKodierung
 $mpZeichensatz=MP_ZeichnsNorm;
 if(MP_Zeichensatz==$mpZeichensatz){if(MP_Zeichensatz!=1) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));}
 else{
  if($mpZeichensatz!=0) if($mpZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  if(MP_Zeichensatz<=0) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));
  elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))))); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br />',$s);
}
function fMpNormAdrS($sNam){
 $sNam=str_replace('�','Ae',str_replace('�','ae',str_replace('�','oe',str_replace('�','ue',str_replace('�','ss',str_replace('"','',str_replace(' ','_',strtolower($sNam))))))));
 $sNam=str_replace('Ä','Ae',str_replace('ä','ae',str_replace('Ö','Oe',str_replace('ö','oe',str_replace('Ü','Ue',str_replace('ü','ue',str_replace('ß','ss',$sNam)))))));
 return str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace('#','_',str_replace('?','_',str_replace('/','_',$sNam)))))));
}
function fHrefLSm($nSegNo,$nSei,$sSegNa){
 if(!MP_Sef){ //normal
  $sL=(empty(MP_SitemapURL)?MP_UrlSm.'marktplatz.php?':MP_SitemapURL).'mp_Aktion=liste&amp;mp_Segment='.$nSegNo.'&amp;mp_Seite='.$nSei;
 }else{ //SEF
  $sL=(empty(MP_SitemapURL)?MP_UrlSm:MP_SitemapURL).fMpNormAdrS($sSegNa).'-liste-'.$nSegNo.'-'.$nSei.'.html';
 }
 return $sL;
}
function fHrefDSm($nSegNo,$nSei,$sNum,$sNam=''){
 if(!MP_Sef){ //normal
  $sL=(empty(MP_SitemapURL)?MP_UrlSm.'marktplatz.php?':MP_SitemapURL).'mp_Aktion=detail&amp;mp_Segment='.$nSegNo.'&amp;mp_Seite='.$nSei.'&amp;mp_Nummer='.$sNum;
 }else{ //SEF
  $sL=(empty(MP_SitemapURL)?MP_UrlSm:MP_SitemapURL).fMpNormAdrS($sNam).'-detail-'.$nSegNo.'-'.$nSei.'-'.$sNum.'.html';
 }
 return $sL;
}
function fImgLoc($s,$sDir){
 $sR='';
 $s=substr($s,strpos($s,'|')+1); $s=MP_Bilder.$sDir.'_'.$s;
 if(file_exists(MP_Pfad.$s)) $sR="\n\t<image:image>\n\t\t<image:loc>".MP_UrlSm.$s."</image:loc>\n\t</image:image>";
 return $sR;
}
?>