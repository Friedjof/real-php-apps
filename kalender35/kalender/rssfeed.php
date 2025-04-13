<?php
header("Content-type: text/xml"); global $kal_RssZeichensatz;
error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED); mysqli_report(MYSQLI_REPORT_OFF); 
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./rssfeed.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
if(strstr($sKalSelf,'rssfeed.php')) @include('kalWerte.php'); //direkter Aufruf
if(defined('KAL_Version')){
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 $kal_TxRssTitel=KAL_TxRssTitel;
 $kal_TxRssBeschreibung=KAL_TxRssBeschreibung;
 $kal_TxRssUrheber=KAL_TxRssUrheber;
 $kal_RssKopfLink=$sKalHttp.KAL_Www.'kalender.php';
 $kal_RssImage=$sKalHttp.KAL_Www.'grafik/rss.gif';
 $aRssFelder=explode(';','0;'.KAL_RssFelder); array_shift($aRssFelder);
 $aRssTrenner=explode('";"','0";'.KAL_RssTrenner.';"'); array_shift($aRssTrenner);
 $kal_RssLink=KAL_RssLink;
 $kal_RssIntervall=KAL_RssIntervall;
 $kal_RssAbHeute=KAL_RssAbHeute;
 $kal_RssMitEnde=KAL_RssMitEnde;
 $kal_RssMitZeit=KAL_RssMitZeit;
 $kal_RssAnzahl=KAL_RssAnzahl;
 $kal_RssSortFeld=KAL_RssSortFeld;
 $kal_RssRueckw=KAL_RssRueckw;
 $aRssFilter=explode('";"','0";'.KAL_RssFilter.';"'); array_shift($aRssFilter);
 $kal_RssSprache=KAL_RssSprache;
 $kal_RssZeichensatz=KAL_RssZeichensatz;
 $kal_RssBBFormat=KAL_RssBBFormat;
 if(isset($_GET['version'])) @include('rssWerte'.((int)$_GET['version']).'.php'); //alternative Konfiguration
 $sRssEncoding=($kal_RssZeichensatz!=2?'ISO-8859-1':'utf-8');
}else{ //Variablen nicht includiert
 $kal_TxRssTitel='RSS-Fehler';
 $kal_TxRssBeschreibung='Die Datei kalWerte.php ist nicht eingebunden!';
 $kal_TxRssUrheber='(c) Webmaster';
 $kal_RssKopfLink='https://www.kalender-script.de/index.html';
 $kal_RssImage='https://www.server-scripts.de/kalender/_kalender.gif';
 $kal_RssSprache='de-de';
 $sRssEncoding='ISO-8859-1';
}
echo '<?xml version="1.0" encoding="'.$sRssEncoding.'"?>'."\n";
?>
<rss version="2.0">
 <channel>
  <title><?php echo fKalTxR($kal_TxRssTitel)?></title>
  <link><?php echo $kal_RssKopfLink?></link>
  <description><?php echo fKalTxR($kal_TxRssBeschreibung)?></description>
  <language><?php echo fKalTxR($kal_RssSprache)?></language>
  <copyright><?php echo fKalTxR($kal_TxRssUrheber)?></copyright>
  <pubDate><?php $Tm=date('Z'); if($Tm<0) $TS='-'; else $TS='+'; $Tm=abs($Tm); echo date("D, j M Y H:i:s ").$TS.sprintf('%04d',($Tm/3600)*100+($Tm%3600)/60);?></pubDate>
  <image>
   <url><?php echo $kal_RssImage?></url>
   <title><?php echo fKalTxR($kal_TxRssTitel)?></title>
   <link><?php echo $kal_RssKopfLink?></link>
  </image>
<?php
 $aA=array(); $aIdx=array();
 if(defined('KAL_Version')){
  $nFelder=count($kal_FeldName);
  $nDatFeld2=0; $nZeitFeld=0; $nZeitFeld2=0; $nVerstecktFeld=0;
  for($i=1;$i<$nFelder;$i++){ //über alle Felder
   $t=$kal_FeldType[$i];

   if($t=='v') $nVerstecktFeld=$i;
   if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; //2. Datum suchen
   if($t=='z'){if($nZeitFeld==0) $nZeitFeld=$i; elseif($nZeitFeld2==0) $nZeitFeld2=$i;} //1. und 2. Zeitfeld suchen
  }
  $sJetztDat=date('Y-m-d'); $sJetztUhr=date('H:i');
  if($kal_RssAbHeute) $sStartDat=$sJetztDat; else $sStartDat=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage);
  if($kal_RssIntervall>'0'){ //nur Intervall
   if($kal_RssIntervall!='@'){ //kein Archiv
    if($kal_RssIntervall<'A'){$i=$kal_RssIntervall; $k=0;} else{$i=0; $k=ord($kal_RssIntervall)-64;} //Intervall 1(Tag)...L(Jahr)
    if($i==1) $i=0; $sEndeDat=date('Y-m-d',@mktime(8,8,8,date('m')+$k,date('d')+$i,date('Y')));
   }else{$sEndeDat=$sStartDat; $sStartDat='00';} //Archiv
  }
  //Daten holen
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); if(!$m=$kal_RssAnzahl) $m=9999;
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aD[$i])); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1);
    if($bOK&&($nVerstecktFeld==0||$a[$nVerstecktFeld]!='J')){
     $bOK=false;
     if($kal_RssMitEnde&&$nDatFeld2>0&&($sDat=substr($a[$nDatFeld2],0,10))&&$sDat>=$sJetztDat){ //kommendes Endedatum
      if($sDat==$sJetztDat&&$kal_RssMitZeit&&$nZeitFeld2>0&&($sDat=$a[$nZeitFeld2])){ //endet heute und Zeit prüfbar
       if($sDat>=$sJetztUhr) $bOK=true; //endet heute noch
      }else $bOK=true; //kommendes Ende oder heute ohne Zeit
     }
     if(!$bOK&&($sDat=substr($a[1],0,10))&&$sDat>=$sStartDat){ //kommendes Anfangsdatum
      if($sDat==$sStartDat&&$kal_RssMitZeit&&$nZeitFeld>0&&($sDat=$a[$nZeitFeld])){ //beginnt heute und Zeit prüfbar
       if($sDat>=$sJetztUhr) $bOK=true; //beginnt heute noch
       elseif($kal_RssMitEnde&&$nZeitFeld2>0&&$nDatFeld2<=0&&($t=$a[$nZeitFeld2])){ //hat kein Enddatum aber Endzeit
        if($t>=$sJetztUhr||$t<$sDat) $bOK=true; //Endezeit künftig oder morgen
       }
      }else $bOK=true; //kommendes Datum oder heute ohne Zeit
     }
     if($bOK&&$kal_RssIntervall>'0'&&($sDat=substr($a[1],0,10))&&$sDat>$sEndeDat) $bOK=false; //Intervallende
     if($bOK) for($n=0;$n<=4;$n+=4) if($j=(int)$aRssFilter[$n]){ //Filterfeld angegeben
      if($v=$aRssFilter[1+$n]){ //Filtern 1-2
       $t=$kal_FeldType[$j]; $w=$aRssFilter[2+$n];
       if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
        if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
        if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $bOK=false;
       }elseif($t=='d'||$t=='@'){ //Datum
        if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //kein Termindatum
         $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $bOK=false;} elseif($s<$v||$s>$w) $bOK=false;
        }
       }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
        $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
        $s=floatval(str_replace(',','.',$a[$j]));
        if($w<=0){if($s!=$v) $bOK=false;} else{if($s<$v||$s>$w) $bOK=false;}
       }elseif($t=='o'){
        if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
        if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $bOK=false;
       }elseif($t=='u'){
        if($k=strlen($w)){if(sprintf('%0d',$a[$j])==sprintf('%0d',$w)) $b2=true; else $b2=false;} else $b2=false;
        if(!(sprintf('%0d',$a[$j])==sprintf('%0d',$v)||$b2)) $bOK=false;
       }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $bOK=false;}}
      }
      if($bOK&&($v=$aRssFilter[3+$n])) if($kal_FeldType[$j]!='u'){if(stristr(str_replace('`,',';',$a[$j]),$v)) $bOK=false;} elseif(sprintf('%0d',$a[$j])==sprintf('%0d',$v)) $bOK=false; //Suchfiltern 3
     }
     if($bOK){//gueltiger Termin
      $nId=$a[0]; $aDt=array($nId);
      for($j=1;$j<=6;$j++) if($n=(int)$aRssFelder[$j-1]) $aDt[]=str_replace('`,',';',$a[$n]); else $aDt[]=''; //Titel+Inhalt
      if($n=(int)$aRssFelder[7-1]) $aDt[]=$a[$n]; else $aDt[]=''; //Category
      if($n=(int)$aRssFelder[8-1]){$t=$kal_FeldType[$n]; if($t=='e'||$t=='c') $aDt[]=fKalDeCodeR($a[$n]); else $aDt[]=str_replace('`,',';',$a[$n]);} else $aDt[]=''; //Author
      if($n=(int)$aRssFelder[9-1]) $aDt[]=$a[$n]; //PubDate
      elseif($n=(isset($aRssFelder[9])?(int)$aRssFelder[9]:0)){//ErsatzPubDate
       if($t=substr($a[$n],0,10)){if($n=(isset($aRssFelder[10])?(int)$aRssFelder[10]:0)) if($a[$n]) $t.=' '.$a[$n];}
       $aDt[]=$t;
      }else $aDt[]='';
      if($kal_RssSortFeld==1) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
      elseif($kal_RssSortFeld>1){ //andere Sortierung
       $t=strtoupper(strip_tags($a[$kal_RssSortFeld]));
       for($j=strlen($t)-1;$j>=0;$j--) //BB-Code weg
        if(substr($t,$j,1)=='[') if($v=strpos($t,']',$j)) $t=substr_replace($t,'',$j,++$v-$j);
       $aIdx[$nId]=(strlen($t)>0?$t:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
      }
      elseif($kal_RssSortFeld==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr
      $aA[$nId]=$aDt; if((--$m)<=0) break;
     }//bOK
    }//versteckt
   }//ueber alle
  }else{//SQL
   $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
   if(!mysqli_connect_errno()){
    if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);

    $f=''; $s=''; $nDatPos=0; $nDatPos2=0; $nZeitPos=0; $nZeitPos2=0; $nSortPos=0; $i=10;
    for($j=1;$j<=9;$j++) if($n=(int)$aRssFelder[$j-1]){
     $f.=',kal_'.$n;
     if($n==1) $nDatPos=$j; elseif($n==$nDatFeld2) $nDatPos2=$j; elseif($n==$nZeitFeld) $nZeitPos=$j; elseif($n==$nZeitFeld2) $nZeitPos2=$j; elseif($n==$kal_RssSortFeld) $nSortPos=$j;
    }else $f.=',"" AS x'.$j;
    if($nDatPos==0){$f.=',kal_1'; $nDatPos=$i++;}
    if($nDatFeld2>0&&$nDatPos2==0){$f.=',kal_'.$nDatFeld2; $nDatPos2=$i++;}
    if($nZeitFeld>0&&$nZeitPos==0){$f.=',kal_'.$nZeitFeld; $nZeitPos=$i++;}
    if($nZeitFeld2>0&&$nZeitPos2==0){$f.=',kal_'.$nZeitFeld2; $nZeitPos2=$i++;}
    if($kal_RssSortFeld>1&&$nSortPos==0){$f.=',kal_'.$kal_RssSortFeld; $nSortPos=$i++;}
    for($n=0;$n<=4;$n+=4) if($j=(int)$aRssFilter[$n]){ //Filterfeld angegeben
     if($v=$aRssFilter[1+$n]){ //Filtern 1-2
      $s.=' AND(kal_'.$j; $w=$aRssFilter[2+$n]; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
      if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
       $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.$w.'%"';
      }elseif($t=='d'||$t=='@'){
       if($j==1&&KAL_EndeDatum){ //Termindatum
        if(empty($w)){$s.='<"'.$v.'~" AND kal_'.($nDatFeld2==0?1:$nDatFeld2).'>"'.$v.'" OR kal_'.$j.' LIKE "'.$v.'%"';} // nur 1 Wert
        else{$s.=' BETWEEN "'.$v.'" AND "'.$w.'~" OR kal_'.($nDatFeld2==0?1:$nDatFeld2).' BETWEEN "'.$v.'" AND "'.$w.'~"';}
       }else{if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';} //sonstiges Datum
      }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
       $v=str_replace(',','.',$v);
       if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
      }elseif($t=='o'){
       $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
      }elseif($t=='u'){
       $s=substr($s,0,strrpos($s,'(kal_')).'(CONVERT(kal_'.$j.',INTEGER)="'.sprintf('%0d',$v).'"'; if(strlen($w)) $s.=' OR CONVERT(kal_'.$j.',INTEGER)="'.sprintf('%0d',$w).'"';
      }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
      $s.=')';
     }
     if($v=$aRssFilter[3+$n]){
      $t=$kal_FeldType[$j];
      if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
       $s.=' AND NOT(kal_'.$j.' LIKE "%'.$v.'%")';
      elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
      elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.sprintf('%0d',$v).'")';
     }
    }
    if($rR=$DbO->query('SELECT id'.$f.' FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').') AND(kal_1>"'.$sStartDat.($kal_RssMitEnde&&$nDatFeld2>0?'" OR kal_'.$nDatFeld2.'>"'.$sStartDat:'').'")'.($kal_RssIntervall>'0'?' AND(kal_1<"'.$sEndeDat.'~")':'').($nVerstecktFeld==0?'':' AND kal_'.$nVerstecktFeld.'<>"J"').$s.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id'.($kal_RssAnzahl==0?'':' LIMIT 0,'.$kal_RssAnzahl))){
     $i=0;
     while($a=$rR->fetch_row()){$bOK=false;
      if($kal_RssMitEnde&&$nDatPos2>0&&($s=substr($a[$nDatPos2],0,10))&&$s>=$sJetztDat){ //kommendes Endedatum
       if($s==$sJetztDat&&$kal_RssMitZeit&&$nZeitPos2>0&&($s=$a[$nZeitPos2])){ //endet heute und Zeit prüfbar
        if($s>=$sJetztUhr) $bOK=true; //endet heute noch
       }else $bOK=true; //kommendes Ende oder heute ohne Zeit
      }
      if(!$bOK&&($t=substr($a[$nDatPos],0,10))&&$t>=$sStartDat){ //kommendes Anfangsdatum
       if($t==$sJetztDat&&$kal_RssMitZeit&&$nZeitPos>0&&($s=$a[$nZeitPos])){ //beginnt heute und Zeit prüfbar
        if($s>=$sJetztUhr) $bOK=true; //beginnt heute noch
        elseif($kal_RssMitEnde&&$nZeitPos2>0&&$nDatPos2<=0&&($t=$a[$nZeitPos2])){ //hat kein Enddatum aber Endzeit
         if($t>=$sJetztUhr||$t<$s) $bOK=true; //Endezeit künftig oder morgen
        }
       }else $bOK=true; //kommendes Datum oder heute ohne Zeit
      }
      if($bOK){
       $nId=$a[0]; $aDt=array($nId); for($j=1;$j<=9;$j++) $aDt[]=$a[$j]; //Titel+Inhalt Category|Author|PubDate
       $aA[$nId]=$aDt;

       if($kal_RssSortFeld==1) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',++$i); //nach Datum
       elseif($kal_RssSortFeld>1){ //andere Sortierung
        $t=strtoupper(strip_tags($a[$nSortPos]));
        for($j=strlen($t)-1;$j>=0;$j--) //BB-Code weg
         if(substr($t,$j,1)=='[') if($v=strpos($t,']',$j)) $t=substr_replace($t,'',$j,++$v-$j);
        $aIdx[$nId]=(strlen($t)>0?$t:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i);
       }
       elseif($kal_RssSortFeld==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr.
      }
     }$rR->close();
    }
   }$DbO->close();
  }//SQL
 }
 if(!$kal_RssRueckw){if($kal_RssSortFeld!=1) asort($aIdx);} else arsort($aIdx); reset($aIdx);

 if(empty($kal_RssLink)) $kal_RssLink=KAL_Www.'kalender.php?kal_Aktion=detail'; $kal_RssLink=$sKalHttp.$kal_RssLink.'&amp;kal_Intervall=%5b%5d&amp;kal_Nummer=';
 foreach($aIdx as $k=>$xx){
  $a=$aA[$k]; $nId=$a[0]; $sTit=''; $sTxt='';
  for($i=1;$i<=3;$i++){
   if(($n=$aRssFelder[$i-1])&&($s=$a[$i])){
    if($s=fKalFmtR($s,$kal_FeldType[$n],$kal_RssBBFormat)) $sTit.=(($i>1)&&($sTit>'')?($i==2)?$aRssTrenner[1-1]:$aRssTrenner[2-1]:'').$s;
   }
  }
  for($i=4;$i<=6;$i++){
   if(($n=$aRssFelder[$i-1])&&($s=$a[$i])){
    if($s=fKalFmtR($s,$kal_FeldType[$n],$kal_RssBBFormat)) $sTxt.=fKalFmtR((($i>4)&&($sTxt>'')?($i==5)?$aRssTrenner[3-1]:$aRssTrenner[4-1]:''),'a',$kal_RssBBFormat).$s;
   }
  }
?>
  <item>
   <title><?php echo $sTit?></title>
   <description><?php echo $sTxt?></description>
   <link><?php echo $kal_RssLink.$nId?></link>
   <guid><?php echo $kal_RssLink.$nId?></guid>
<?php if($aRssFelder[7-1]>0){ ?>
   <category><?php if($s=$a[7]) echo fKalFmtR($s,'k',$kal_RssBBFormat)?></category>
<?php }if($aRssFelder[8-1]>0){ ?>
   <author><?php echo $a[8]?></author>
<?php }if(($aRssFelder[9-1]>0||(isset($aRssFelder[9])&&$aRssFelder[9]>0))&&($s=$a[9])){ ?>
   <pubDate><?php echo fKalPubDatR($s)?></pubDate>
<?php } ?>
  </item>
<?php
 }
?>
 </channel>
</rss>

<?php
function fKalDeCodeR($w){ //entschlüsseln
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fKalPubDatR($s){ //Datum kodieren
 $Tm=date('Z'); if($Tm<0) $TS='-'; else $TS='+'; $Tm=abs($Tm);
 return date('D, j M Y H:i',@mktime(substr($s,11,2),substr($s,14,2),1,substr($s,5,2),substr($s,8,2),substr($s,0,4))).':00 '.$TS.sprintf('%04d',($Tm/3600)*100+($Tm%3600)/60);
}

//BB-Code zu HTML wandeln
function fKalBBH($s){
 $v=str_replace("\n",'<br>',str_replace("\n ",'<br>',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" style="border:0">',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="kalText"><li class="kalText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,4)=='<br>') $l=4; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br>',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="kalText">',$l,4); $n+=19; $l=strpos($v,'<br>',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}

//BB-Code zu einfachem Text wandeln
function fKalBBT($s){
 $v=str_replace("\r",'',$s); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'||substr($Tg,0,3)=='[i]'||substr($Tg,0,3)=='[u]') $v=substr_replace($v,'',$p,3);
  elseif(substr($Tg,0,4)=='[/b]'||substr($Tg,0,4)=='[/i]'||substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']'));  $v=substr_replace($v,'',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]')  $v=substr_replace($v,'',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,"\n",$p,8);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v," \n",$p,9);}
  elseif(substr($Tg,0,7)=='[right]') {$v=substr_replace($v,"\n",$p,7);} elseif(substr($Tg,0,8)=='[/right]') {$v=substr_replace($v," \n",$p,8);}
  elseif(substr($Tg,0,5)=='[url]') $v=substr_replace($v,'',$p,5); elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'',$p,6);
  elseif(substr($Tg,0,6)=='[link]')$v=substr_replace($v,'',$p,6); elseif(substr($Tg,0,7)=='[/link]')$v=substr_replace($v,'',$p,7);
  elseif(substr($Tg,0,5)=='[img]') $v=substr_replace($v,'',$p,5); elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   $v=substr_replace($v," \n",$p,6+(substr($Tg,5,2)=='=o'?2:0));
   $n=strpos($v,'[/list]',$p+1); $v=substr_replace($v," \n",$n,7); $l=strpos($v,"\n",$p);
   while($l<$n&&$l>0){$v=substr_replace($v,"\n - ",$l,1); $n+=3; $l=strpos($v,"\n",$l+1);}
  }
  $p=strpos($v,'[',$p+1);
 }
 return $v;
}

function fKalFmtR($s,$t,$nBBFmt){
 global $kal_WochenTag;
 switch($t){  //RssBBFormat: 2-HTML, 1-BB bleibt, 0-blanker Text
  case 't': case 'm': if($nBBFmt==2) $s=fKalBBH(fKalTxR($s)); elseif($nBBFmt==0) $s=fKalBBT(fKalTxR($s)); else $s=fKalTxR($s); break; //Text/Memo/
  case 'a': case 'k': case 's': case 'e': case 'o': $s=fKalTxR($s); break; //Aufzählung/Kategorie so lassen
  case 'd': case '@': $w=trim(substr($s,11)); //Datum
   $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
   switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
    case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
    case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
   }
   $s=$s1.$v.$s2.$v.$s3;
   if($t=='d'){if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTxR($kal_WochenTag[$w]).' '.$s; else $s.=' '.fKalTxR($kal_WochenTag[$w]);}
   elseif($w) $s.=', '.$w;
   break;
  case 'z': $s.=' '.fKalTxR(KAL_TxUhr); break; //Uhrzeit
  case 'w': //Währung
   if($s>0||!KAL_PreisLeer){
    $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.=' '.KAL_Waehrung;
   }else $s='';
   break;
 }//switch
 return str_replace('"','&quot;',str_replace('<','&lt;',str_replace('>','&gt;',$s)));
}

function fKalTxR($sTx){ //TextKodierung
 global $kal_RssZeichensatz;
 if($kal_RssZeichensatz==0) $s=str_replace("'",'&apos;',htmlspecialchars($sTx,ENT_COMPAT,'ISO-8859-1'));
 else $s=iconv('ISO-8859-1','UTF-8',str_replace("'",'&apos;',htmlspecialchars($sTx,ENT_COMPAT,'ISO-8859-1')));
 return str_replace('\n '," \n",$s);
}
?>