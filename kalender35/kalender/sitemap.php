<?php
header('Content-Type: text/plain; charset=ISO-8859-1');
// Muster: https://sitemaps.org/protocol.html#sitemapXMLExample und https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 

define('KAL_SitemapDest','');  // sitemap.xml wenn leer, oder eigener [pfad/]dateiname.xml
define('KAL_SitemapURL','');   // Kalender-URL wenn leer, oder individuelle Adresse, an die die Kalenderparameter angehaengt werden
define('KAL_SitemapImages',3); // Anzahl der Bilder pro Termin im Sitemap, Wertebereich 0....3
define('KAL_SmLstPriority','0.90'); define('KAL_SmLstChngeFrq','daily');
define('KAL_SmDtlPriority','0.80'); define('KAL_SmDtlChngeFrq','weekly');
define('KAL_SmToDesktop',true);

if(isset($_GET['kal'])){
 include('./kalWerte.php');
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if($_GET['kal']==KAL_Schluessel){

 // --- Anfang SitemapGenerator ---
 $sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'; define('KAL_UrlSm',$sKalHttp.KAL_Www);
 $sRes='Sitemap-Generator gestartet: '.date('H:i:s')."\n"; $X='';

 $bSQLOpen=false; $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; echo "\n".KAL_TxSqlVrbdg;}
 }

 $sNowDat=date('Y-m-d'); $sDatDat=$sNowDat; $sRefDat=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); $bDo=false; $bImg=false; $nImg1Pos=$nImg2Pos=$nImg3Pos=0;
 $nFelder=count($kal_FeldName); $sFtIdx=$kal_FeldType[KAL_ListenIndex]; $bFtIdxIsTxt=(strpos('#tamksoj',$sFtIdx)>0); $nDatPos=array_search('@',$kal_FeldType);
 for($i=0;$i<$nFelder;$i++) if($kal_FeldType[$i]=='b') if($nImg1Pos==0&&KAL_SitemapImages>0) $nImg1Pos=$i; elseif($nImg2Pos==0&&KAL_SitemapImages>1) $nImg2Pos=$i; elseif($nImg3Pos==0&&KAL_SitemapImages>2) $nImg3Pos=$i;

 if(!KAL_SQL){ //Termine holen
  $sDatDat=date('Y-m-d',filemtime(KAL_Pfad.KAL_Daten.KAL_Termine)); // Aenderungszeit
  $aT=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aT); // Termine
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aT[$i]));
   if($a[2]>=$sRefDat&&$a[1]=='1'){
    array_splice($a,1,1);
    $s=$a[KAL_ListenIndex];
    if($bFtIdxIsTxt){
     $s=strtoupper(strip_tags($s));
     for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
     if(strlen($s)>32) $s=substr($s,0,32);
    }elseif($sFtIdx=='w') $s=sprintf('%09.2f',1+$s); elseif($sFtIdx=='n'||$sFtIdx=='i') $s=sprintf('%07d',1+$s);
    elseif($sFtIdx=='1'||$sFtIdx=='2'||$sFtIdx=='3'||$sFtIdx=='r') $s=sprintf('%010.3f',1+$s);
    $aD[$s.chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i)]=$a;
   }
  }
 }elseif($bSQLOpen){ //bei SQL
  if($rR=$DbO->query('SHOW TABLE STATUS WHERE NAME="'.KAL_SqlTabT.'"')){ // Aenderungszeit
   if($a=$rR->fetch_assoc()){
    if(!$s=$a['Update_time']) if(!$s=$a['Create_time']) $s=$sRefDat; $sDatDat=substr($s,0,10);
   }$rR->close();
  }
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE kal_1>="'.$sRefDat.'" AND online="1" ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){$i=0; // Termine
   while($a=$rR->fetch_row()){
    array_splice($a,1,1);
    $s=$a[KAL_ListenIndex];
    if($bFtIdxIsTxt){
     $s=strtoupper(strip_tags($s));
     for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
     if(strlen($s)>32) $s=substr($s,0,32);
    }elseif($sFtIdx=='w') $s=sprintf('%09.2f',1+$s); elseif($sFtIdx=='n'||$sFtIdx=='i') $s=sprintf('%07d',1+$s);
    elseif($sFtIdx=='1'||$sFtIdx=='2'||$sFtIdx=='3'||$sFtIdx=='r') $s=sprintf('%010.3f',1+$s);
    $aD[$s.chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i)]=$a;
   }$rR->close();
  }
 }
 if(KAL_Rueckwaerts) krsort($aD); else ksort($aD);

 $nZhl=count($aD); $n=max(ceil($nZhl/KAL_ListenLaenge),1); // Listen-Seite(n)
  $X.="\n<url>";
  $X.="\n\t<loc>".(empty(KAL_SitemapURL)?KAL_UrlSm.'kalender.php':KAL_SitemapURL).'</loc>';
  $X.="\n\t<lastmod>".$sDatDat.'</lastmod>';
  $X.="\n\t<changefreq>".KAL_SmLstChngeFrq."</changefreq>";
  $X.="\n\t<priority>".KAL_SmLstPriority."</priority>";
  $X.="\n</url>";
 for($i=1;$i<$n;$i++){
  $X.="\n<url>";
  $X.="\n\t<loc>".(empty(KAL_SitemapURL)?KAL_UrlSm.'kalender.php?':KAL_SitemapURL).'kal_Start='.($i*KAL_ListenLaenge+1).'</loc>';
  $X.="\n\t<lastmod>".$sDatDat.'</lastmod>';
  $X.="\n\t<changefreq>".KAL_SmLstChngeFrq."</changefreq>";
  $X.="\n\t<priority>".KAL_SmLstPriority."</priority>";
  $X.="\n</url>";
 }
 $nSt=1; $i=1; $k=99999;
 foreach($aD as $a){
  if(++$k>=KAL_ListenLaenge){$k=0; $nSt=$i;} $i++;
  if($nDatPos) $sDetDat=substr($a[$nDatPos],0,10); else $sDetDat=''; // Aenderungsdatum
  if($sDetDat>$sNowDat||$sDetDat=='') $sDetDat=$sNowDat; if($sDetDat>$sDatDat) $sDetDat=$sDatDat;
  $sImg=''; if($nImg1Pos>0) if($s=$a[$nImg1Pos]) $sImg=fImgLoc($s,$a[0]); if($nImg2Pos>0) if($s=$a[$nImg2Pos]) $sImg.=fImgLoc($s,$a[0]); if($nImg3Pos>0) if($s=$a[$nImg3Pos]) $sImg.=fImgLoc($s,$a[0]);
  $X.="\n<url>";
  $X.="\n\t<loc>".(empty(KAL_SitemapURL)?KAL_UrlSm.'kalender.php?':KAL_SitemapURL).'kal_Aktion=detail&amp;kal_Start='.$nSt.'&amp;kal_Nummer='.$a[0].'</loc>'.$sImg; if($sImg>'') $bImg=true;
  $X.="\n\t<lastmod>".$sDetDat.'</lastmod>';
  $X.="\n\t<changefreq>".KAL_SmDtlChngeFrq."</changefreq>";
  $X.="\n\t<priority>".KAL_SmDtlPriority."</priority>";
  $X.="\n</url>";
 }
 $sRes.="\nTerminliste mit ".$nZhl.' Termin'.($nZhl!=1?'en':'');

 $X='<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.($bImg?' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"':'').'>'.$X."\n".'</urlset>';

 if($bSQLOpen) if($DbO) $DbO->close(); //SQL schliessen

 $sFN=(KAL_SitemapDest?KAL_SitemapDest:'sitemap.xml');
 if($f=fopen($sFN,'w')){fwrite($f,$X);  $sRes.="\nSitemapdatei \"".$sFN.'" wurde geschrieben';}else $sRes.="\nSchreibfehler Sitemapdatei ".$sFN;

 $sRes.="\n\nSitemap-Generator beendet: ".date('H:i:s')."\n"; echo $sRes;
 // --- Ende Cronjob ---
 if(KAL_CronMail&&$bDo){
  require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); $Mailer->Subject='Kalender-Sitemap-Generator '.date('d.m.y');
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
  $Mailer->AddTo(KAL_MailTo); $Mailer->SetReplyTo(KAL_MailTo);
  $Mailer->Text=$sRes; $Mailer->Send();
 }
 if(KAL_SmToDesktop) echo "\n\n".$X."\n";

 }else echo "\n".'unberechtigter Aufruf: Geheimschluessel falsch!';
}else echo "\n".'unvollstaendige Aufrufadresse!';

function fImgLoc($s,$sNr){
 $sR='';
 $s=substr($s,strpos($s,'|')+1); $s=KAL_Bilder.$sNr.'_'.$s;
 if(file_exists(KAL_Pfad.$s)) $sR="\n\t<image:image>\n\t\t<image:loc>".KAL_UrlSm.$s."</image:loc>\n\t</image:image>";
 return $sR;
}
?>