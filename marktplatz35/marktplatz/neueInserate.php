<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
$sMpSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./neueInserate.php')));
$sMpHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$bMpPopup=isset($_GET['mp_Popup'])||isset($_POST['mp_Popup']); if(!defined('MP_Popup')) define('MP_Popup',$bMpPopup);
global $sMpOutN; $sMpOutN=''; $sMpHtmlVor=''; $sMpHtmlNach=''; $sMpTitel=''; $sMpSession=''; $bMpOK=true; //Seitenkopf, Seitenfuss, Segmentersatz, Status

if(!strstr($sMpSelf,'/neueInserate.php')){ //includierter Aufruf
 if(defined('MP_Version')){ //Variablen includiert
  define('MP_NeueZiel',(MP_NeueLink!=''?MP_NeueLink:$sMpSelf));
  if(!defined('MP_Url')) define('MP_Url',$sMpHttp.MP_Www);
 }else{$bMpOK=false; $sMpOutN="\n".'<p style="color:red"><b>Konfiguration <i>mpWerte.php</i> wurde nicht includiert!</b></p>';}
}else{ //Script laeuft allein als neueInserate.php
 if(file_exists('mpWerte.php')) include('mpWerte.php'); define('MP_NeueZiel',(MP_NeueLink==''?'marktplatz.php':MP_NeueLink));
 if(defined('MP_Version')){
  header('Content-Type: text/html; charset='.(MP_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(!defined('MP_Url')) define('MP_Url',$sMpHttp.MP_Www);
  if(MP_Schablone){ //mit Seitenschablone
   $sMpHtmlNach=@implode('',(file_exists(MP_Pfad.'neueInserate.htm')?file(MP_Pfad.'neueInserate.htm'):array('')));
   if($nMpJ=strpos($sMpHtmlNach,'{Inhalt}')){
    $sMpHtmlVor=substr($sMpHtmlNach,0,$nMpJ); $sMpHtmlNach=substr($sMpHtmlNach,$nMpJ+8); //Seitenkopf, Seitenfuss
   }else{$sMpHtmlVor='<p style="color:red">HTML-Layout-Schablone <i>neueInserate.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sMpHtmlNach='';}
  }else{ //ohne Seitenschablone
   $sMpOutN="\n\n".'<link rel="stylesheet" type="text/css" href="'.MP_Url.'mpStyles.css">'."\n\n";
  }
 }else{$bMpOK=false; $sMpOutN="\n".'<p style="color:red">Konfiguration <i>mpWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bMpOK){ //Konfiguration eingelesen
 if(!MP_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet);
 //geerbte GET/POST-Parameter aufbewahren und einige Parameter ermitteln
 $sMpQry=''; $sMpHid='';
 if(isset($_GET['mp_Aktion'])) $sMpAktion=fMpRq1N($_GET['mp_Aktion']); elseif(isset($_POST['mp_Aktion'])) $sMpAktion=fMpRq1N($_POST['mp_Aktion']); else $sMpAktion='index';
 if(isset($_GET['mp_Session'])&&$sMpAktion!='login') $sMpSession=fMpRq1N($_GET['mp_Session']); elseif(isset($_POST['mp_Session'])&&$_POST['mp_Session']!='') $sMpSession=fMpRq1N($_POST['mp_Session']);
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  reset($_GET);
  if(!defined('MP_Query')) foreach($_GET as $sMpK=>$sMpV) if(substr($sMpK,0,3)!='mp_'){
   $sMpQry.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
   $sMpHid.='<input type="hidden" name="'.$sMpK.'" value="'.$sMpV.'" />';
  }elseif(strrpos($sMpK,'F')>3) $sMpSuchParam.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
 }else{ //bei POST
  reset($_POST);
  if(!defined('MP_Query')) foreach($_POST as $sMpK=>$sMpV) if(substr($sMpK,0,3)!='mp_') if(is_string($sMpV)){
   $sMpQry.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
   $sMpHid.='<input type="hidden" name="'.$sMpK.'" value="'.$sMpV.'" />';
  }elseif(strrpos($sMpK,'F')>3) $sMpSuchParam.='&amp;'.$sMpK.'='.rawurlencode($sMpV);
 }
 if(!defined('MP_Query')) define('MP_Query',$sMpQry);
 if(!defined('MP_Session')) define('MP_Session',$sMpSession);

 //Beginn der Ausgabe
 $sMpOutN.=$sMpHtmlVor;

 // Detail als Popup-Fenster
 if(MP_NeuePopup){
  $sMpOutN.='<script type="text/javascript">function MpWin(sURL){mpWin=window.open(sURL,"mpwin","width='.MP_PopupBreit.',height='.MP_PopupHoch.',left='.MP_PopupX.',top='.MP_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");mpWin.focus();}</script>'."\n";
  define('MP_NeuesOnClk','" onclick="MpWin(this.href);return false;');
 }else define('MP_NeuesOnClk','');

 $sMpOutN.="\n".'<div class="mpNBox">'.fMpNeueInserate()."\n\n".'</div>';
 $sMpOutN.="\n".$sMpHtmlNach;
}

if(!defined('MP_Echo')||MP_Echo||$bMpPopup) echo $sMpOutN."\n";

function fMpNeueInserate(){
 $Meld=''; $MTyp='Fehl'; $sQ=''; $bSes=false;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Session pruefen
 if(!$sSes=MP_Session) if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
 if($sSes>''){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!MP_SQL){
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true;
 }}}}

 //individuelle Standardfelder holen
 $aNeueFelder=explode(';',MP_NeueFelder); $aNeueFelder=array_merge(array('##'),$aNeueFelder);
 for($i=count($aNeueFelder)-1;$i>0;$i--) $aNeueFelder[$i]=str_replace('`,',';',$aNeueFelder[$i]);

 //ueber alle Segmente
 $aSeg=explode(';',MP_Segmente); $aSgO=explode(';',MP_Anordnung); $bUTyp=false;
 $nSgI=0; $aSgS=array(); $aDaten=array(); $nTotal=0; $sIntervallAnfang=date('Y-m-d');
 while($nSegNo=array_search(++$nSgI,$aSgO))if(($sSegNam=$aSeg[$nSegNo])&&($sSegNam!='LEER')&&(substr($sSegNam,0,1)!='~'&&(substr($sSegNam,0,1)!='*'||$bSes))){
  if(substr($sSegNam,0,1)=='*') $sSegNam=substr($sSegNam,1);
  $sSegNo=sprintf('%02d',$nSegNo); $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL;
  //Struktur holen
  $aFN=array(); $aFT=array(); $aLF=array(); $aNL=array(); $aLK=array(); $aSS=array();
  $aAW=array(); $aKW=array(); $aSW=array(); $aStru=array(); $nFelder=0;
  if(!MP_SQL){ //Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
  if(count($aStru)>1){
   $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); $nFelder=count($aFN);
   if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
   $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
   $aLF=explode(';',rtrim($aStru[2])); $aLF[0]=substr($aLF[0],14,1);
   $aNL=explode(';',rtrim($aStru[3])); $aNL[0]=substr($aNL[0],14,1);
   $aLK=explode(';',rtrim($aStru[5])); $aLK[0]=substr($aLK[0],14,1);
   $aSS=explode(';',rtrim($aStru[6])); $aSS[0]='';
   $aET=explode(';',rtrim($aStru[15])); $nZeitKorr=$aET[1]*(-86400);
   $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]=''; $aAW[1]='';
   $s=(isset($aStru[17])?rtrim($aStru[17]):''); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
   $s=(isset($aStru[18])?rtrim($aStru[18]):''); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
  }
  if($bSes&&MP_NListeAnders) $aLF=$aNL; $bTypU=false;

  if(!MP_NeueEigeneZeilen&&$aNeueFelder[1]>''){ //alternativ aufgezaehlte Spalten
   $aNL=array(0); $bFld=false; $aNeuFld=$aNeueFelder;
   if($p=array_search($aFN[0],$aNeuFld)) array_splice($aNeuFld,$p,1); //Nummer raus
   for($i=1;$i<$nFelder;$i++){
    if($p=(int)array_search($aFN[$i],$aNeuFld)) $bFld=true; $aNL[]=$p;
   }
   if($bFld){
    asort($aNL); reset($aNL); $i=0; foreach($aNL as $k=>$v) if($v>0) $aNL[$k]=++$i; ksort($aNL);
    $aNL[0]=(array_search($aFN[0],$aNeueFelder)?1:0); $aLF=$aNL;
   }
  }

  //FeldFilter bestimmen, Listenspaltenfolge ermitteln
  $aSpalten=array(); $bSuchDat=false; $nIdxFld=(MP_NeueNachEintrag?0:1);
  if(isset($_GET['mp_Such'])) $s=fMpRqN($_GET['mp_Such']); elseif(isset($_POST['mp_Such'])) $s=fMpRqN($_POST['mp_Such']); else $s='';
  if($bSuch=(strlen($s)>0)){ //Schnellsuche
   if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   $sQ.='&amp;mp_Such='.rawurlencode($s);
   if(substr_count($s,(MP_Datumsformat==1?'.':(MP_Datumsformat==2||MP_Datumsformat==3?'/':'-')))==2){ //Separatoren enthalten
    $sSuch=fMpNormDatum($s); if(!strpos($sSuch,'00',5)){$bSuchDat=true; $bSuch=false;} else $sSuch=$s;
   }else $sSuch=$s;
  }else $sSuch='';
  for($i=0;$i<$nFelder;$i++){ //ueber alle Felder
   $t=$aFT[$i]; $aSpalten[$aLF[$i]]=($t!='m'||MP_DruckLMemo?$i:-1); //Liste meist ohne Memos
   if($t=='@'&&$nIdxFld==0) $nIdxFld=$i; //Sortierfeld
   if(strlen($sSuch)==0){ //keine Schnellsuche
    if(isset($_GET['mp_'.$i.'F1'])) $s=fMpRqN($_GET['mp_'.$i.'F1']); elseif(isset($_POST['mp_'.$i.'F1'])) $s=($_POST['mp_'.$i.'F1']); else $s='';
    if(strlen($s)){ //erstes Suchfeld ausgefuellt
     if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
     $sQ.='&amp;mp_'.$i.'F1='.rawurlencode($s);
     if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else{$a1Filt[$i]=fMpNormDatum($s); $a2Filt[$i]='';} //Datum
    }elseif($t=='v'){$a1Filt[$i]=(MP_NVerstecktSehen&&($sSes>'')?'':'N'); $a2Filt[$i]='';} //versteckt
    elseif($t=='u'&&$bSes&&isset($_GET['mp_Aendern'])&&!MP_NAendernFremde){$a1Filt[$i]=substr($sSes,0,4);} //aendern fuer User
    if(isset($_GET['mp_'.$i.'F2'])) $s=fMpRqN($_GET['mp_'.$i.'F2']); elseif(isset($_POST['mp_'.$i.'F2'])) $s=fMpRqN($_POST['mp_'.$i.'F2']); else $s='';
    if(strlen($s)){ //zweites Suchfeld ausgefuellt
     if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
     $sQ.='&amp;mp_'.$i.'F2='.rawurlencode($s);
     if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
     elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
     if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else $a2Filt[$i]=fMpNormDatum($s);
    }
    if(isset($_GET['mp_'.$i.'F3'])) $s=fMpRqN($_GET['mp_'.$i.'F3']); elseif(isset($_POST['mp_'.$i.'F3'])) $s=fMpRqN($_POST['mp_'.$i.'F3']); else $s='';
    if(strlen($s)){ //drittes Suchfeld ausgefuellt
     if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
     $sQ.='&amp;mp_'.$i.'F3='.rawurlencode($s);
     $a3Filt[$i]=$s;
    }
   }//$sSuch
  }
  $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='9'; //Normalsuche
  if(MP_SuchArchiv) if(isset($_GET['mp_Archiv'])||(isset($_POST['mp_Archiv'])&&$_POST['mp_Archiv']>'')) //Archivsuche
   {$sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00'; $sQ.='&amp;mp_Archiv=1';}
  if($bSuchDat){$a1Filt[1]=$sSuch; $a2Filt[1]='';} //Schnellsuche nach Datum

  $aSpalten[0]=0; ksort($aSpalten);
  if(in_array(-1,$aSpalten)){$j=count($aSpalten); for($i=$j-1;$i>0;$i--) if($aSpalten[$i]<0) array_splice($aSpalten,$i,1);}
  $nSpalten=count($aSpalten); for($j=1;$j<$nSpalten;$j++) if($aFT[$aSpalten[$j]]=='u') $bTypU=true;

  if(!$nDatPos=array_search('1',$aSpalten)) $nDatPos=-1;
  $nKatPos=array_search('k',$aFT); $nTitPos=0;
  if(MP_Sef&&($nTitPos=array_search('TITLE',$aFN))) if($aFT[$nTitPos]!='t') $nTitPos=0;

  //Daten holen
  $aTmp=array();
  if($nIdxFld>0||MP_NeueNachEZeit==2){//Sortierdaten vorhanden
   if(!MP_SQL){ //Textdaten
    $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
    for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
     $a=explode(';',rtrim($aD[$i])); $nId=(int)$a[0]; $b=$a[1]=='1'; array_splice($a,1,1); $sAblaufDat=$a[1];
     if(!$bSuch){ //keine Schnellsuche
      $b=($b&&$sAblaufDat>=$sIntervallAnfang&&$sAblaufDat<=$sIntervallEnde); //laufend
      if($b&&is_array($a1Filt)){
       reset($a1Filt);
       foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
        $t=($aFT[$j]); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
        if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
         if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
         if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
        }elseif($t=='d'||$t=='@'){ //Datum
         $s=$a[$j]; if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
        }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
         $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
         $s=floatval(str_replace(',','.',$a[$j]));
         if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
        }elseif($t=='o'){
         if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
         if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $b=false;
        }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $b=false;}}
       }
      }
      if($b&&is_array($a3Filt)){ //Suchfiltern 3
       reset($a3Filt); foreach($a3Filt as $j=>$v) if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}
      }
     }elseif($b){$b=false; //Schnellsuche
      if($sAblaufDat>=$sIntervallAnfang&&$sAblaufDat<=$sIntervallEnde) for($j=1;$j<$nFelder;$j++){ //laufend
       $t=$aFT[$j];
       if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'){if(stristr($a[$j],$sSuch)){$b=true; break;}}
       elseif($t=='o') if(strpos($a[$j],$sSuch)===0){$b=true; break;}
     }}
     if($b){ //Datensatz gueltig
      $aS=array($nId);
      for($j=1;$j<$nSpalten;$j++) $aS[]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aSpalten[$j]]));
      if($nKatPos>0) if($w=$a[$nKatPos]) if($w=array_search($w,$aKW)) $aS[]=chr(64+$w); //Kategoriezusatzspalte
      if($nTitPos>0) if($w=$a[$nTitPos]) $aS[]=$w; //Titelzusatzspalte fuer SEF-Titel
      if($nDatPos<=0) $aS[-1]=$a[1]; //Datum notfalls ergaenzen
      if($nIdxFld>0) $sIdx=(MP_NeueNachEZeit==1?$a[$nIdxFld]:substr($a[$nIdxFld],0,10));
      else{$sIdx=$a[1]; $sIdx=date('Y-m-d',@mktime(8,0,0,(int)substr($sIdx,5,2),(int)substr($sIdx,8,2),(int)substr($sIdx,0,4))+$nZeitKorr);  }
      $aTmp[$sIdx.'|'.rand(100,999)]=$aS; //ToDo: nur Datum und Zufallszahl
     }//gueltig
    }//$nSaetze
   }elseif($DbO){$s=''; //SQL-Daten
    if(!$bSuch){ //keine Schnellsuche
     $s=' AND mp_1>="'.$sIntervallAnfang.'" AND mp_1<="'.$sIntervallEnde.'"';
     if(is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
      $s.=' AND('.($j>0?'mp_'.$j:'nr'); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$aFT[$j]; //$v Suchwort1, $w Suchwort2
      if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
       $s.=' LIKE "%'.fMpDtN($v).'%"'; if(strlen($w)) $s.=' OR '.($j>0?'mp_'.$j:'nr').' LIKE "%'.fMpDtN($w).'%"';
      }elseif($t=='d'||$t=='@'){
       {if(empty($w)) $s.='="'.$v.'"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'"';} //sonstiges Datum
      }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
       $v=str_replace(',','.',$v);
       if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
      }elseif($t=='o'){
       $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR '.($j>0?'mp_'.$j:'nr').' LIKE "'.$w.'%"';
      }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
      $s.=')';
     }
     if(is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
      $t=$aFT[$j];
      if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
       $s.=' AND NOT('.($j>0?'mp_'.$j:'nr').' LIKE "%'.fMpDtN($v).'%")';
      elseif($t=='o') $s.=' AND NOT('.($j>0?'mp_'.$j:'nr').' LIKE "'.$v.'%")';
     }
    }else{$s=''; //Schnellsuche
     for($j=1;$j<$nFelder;$j++){
      $t=$aFT[$j];
      if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l') $s.=' OR mp_'.$j.' LIKE "%'.fMpDtN($sSuch).'%"';
      elseif($t=='o') $s.=' OR mp_'.$j.' LIKE "'.$sSuch.'%"';
     }$s=' AND mp_1>="'.$sIntervallAnfang.'" AND mp_1<="'.$sIntervallEnde.'" AND('.substr($s,4).')';
    }
    $t=''; $nDatPos=0; $i=$nSpalten;
    for($j=1;$j<$nSpalten;$j++){
     $k=$aSpalten[$j]; $t.=',mp_'.$k;
     if($k==1) $nDatPos=$j; if($aFT[$k]=='u') $bTypU=true;
    }
    if($nKatPos>0){$t.=',mp_'.$nKatPos; $i++;}
    if($nDatPos==0){$t.=',mp_1'; $nDatSel=$i++;}
    $nIdxPos=$i;
    if($rR=$DbO->query('SELECT nr'.$t.',mp_'.($nIdxFld>0?$nIdxFld:1).' FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE online="1"'.$s.' ORDER BY mp_1,nr')){
     while($a=$rR->fetch_row()){
      $nId=(int)$a[0]; $aS=array($nId);
      for($j=1;$j<$nSpalten;$j++) $aS[]=str_replace("\r",'',$a[$j]);
      if($nKatPos>0) if($j=$a[$nSpalten]) if($j=array_search($j,$aKW)) $aS[]=chr(64+$j); //Kategoriezusatzspalte
      if($nDatPos<=0) $aS[-1]=$a[$nDatSel]; //Datum notfalls ergaenzen
      if($nIdxFld>0) $sIdx=(MP_NeueNachEZeit==1?$a[$nIdxPos]:substr($a[$nIdxPos],0,10));
      else{$sIdx=$a[$nIdxPos]; $sIdx=date('Y-m-d',@mktime(8,0,0,(int)substr($sIdx,5,2),(int)substr($sIdx,8,2),(int)substr($sIdx,0,4))+$nZeitKorr);  }
      $aTmp[$sIdx.'|'.rand(100,999)]=$aS; //ToDo: nur Datum und Zufallszahl
     }$rR->close();
    }else $Meld=MP_TxSqlFrage;
   }//SQL
  }//$nIdxFld>0||MP_NeueNachEZeit==2

  if(count($aTmp)>0){ //Daten pro Segment limitieren
   krsort($aTmp); reset($aTmp); $sTag='2000'; $nTagZ=0; $nMaxZ=0; $sSgSrt=sprintf('%02d',100-$nSgI);
   foreach($aTmp as $i=>$a){
    if(MP_NeueAnzahl>0&&++$nMaxZ>MP_NeueAnzahl) break;
    elseif(MP_NeueTage>0&&substr($i,0,10)!=$sTag&&($sTag=substr($i,0,10))&&++$nTagZ>MP_NeueTage) break;
    else $aDaten[$i.'|'.$sSgSrt]=array($nSegNo,$a);
   }
   if($bTypU) $bUTyp=true;
   $aSgs[$nSegNo]=array('FN'=>$aFN,'FT'=>$aFT,'LF'=>$aLF,'LK'=>$aLK,'SS'=>$aSS,'SW'=>$aSW,'SP'=>$aSpalten);
  }
 }//ueber alle Segmente

 krsort($aDaten); reset($aDaten); $sTag='2000'; $nTagZ=0; $nMaxZ=0; $aTmp=array();
 foreach($aDaten as $i=>$a){ //Daten insgesamt limitieren
  if(MP_NeueAnzahl>0&&++$nMaxZ>MP_NeueAnzahl) break;
  elseif(MP_NeueTage>0&&substr($i,0,10)!=$sTag&&($sTag=substr($i,0,10))&&++$nTagZ>MP_NeueTage) break;
  else $aTmp[substr($i,-2,2).'|'.$i]=$a;
 }
 $aDaten=$aTmp; krsort($aDaten); reset($aDaten);

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#'); $nNutzerZahl=0; $nNLF=0;
 if($bUTyp){
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fMpDeCodeN($a[2]); $a[4]=fMpDeCodeN($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;}
    $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $nNLF=($bSes?MP_NNutzerListFeld:MP_NutzerListFeld);
 }

 //Ausgabe ueber alle Segmente
 $X=''; $nSegNo=0; $sSegNam=''; $nFarb=2;
 foreach($aDaten as $a){ //ueber alle Datensaetze
  if($nSegNo!=$a[0]){ //neuer Kopf
   if($nSegNo!=0) $X.="\n".'</div>';
   $nSegNo=$a[0]; $sSegNo=sprintf('%02d',$nSegNo); $aStru=$aSgs[$nSegNo];
   if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
   $sSegNam=$aSeg[$nSegNo]; if(substr($sSegNam,0,1)=='*') $sSegNam=substr($sSegNam,1);
   //eigene Layoutzeile pruefen
   if($bEigeneZeilen=MP_NeueEigeneZeilen&&(file_exists(MP_Pfad.'neue'.$sSegNo.'Zeile.htm')||file_exists(MP_Pfad.'neueZeile.htm'))){
    if(!$sEigeneZeile=@implode('',(file_exists(MP_Pfad.'neue'.$sSegNo.'Zeile.htm')?file(MP_Pfad.'neue'.$sSegNo.'Zeile.htm'):array('')))) $sEigeneZeile=@implode('',(file_exists(MP_Pfad.'neueZeile.htm')?file(MP_Pfad.'neueZeile.htm'):array('')));
    $s=strtolower($sEigeneZeile);
    if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
   }

   $nSpalten=count($aStru['SP']); $bMitID=$aStru['LF'][0]>0; $nFarb=2;
   if(MP_NeueSegTrnZ) $X.="\n".' <div class="mpTrnNZl">'.fMpTxN(MP_TxSegment).': '.fMpTxN(substr($sSegNam,0,1)!='*'?$sSegNam:substr($sSegNam,1)).'</div>';
   $X.="\n\n".'<div class="mpTab'.(!$bEigeneZeilen?'N':'NE').'">'; //Tabelle

   if(MP_NeueKopf){
    if(!$bEigeneZeilen){ //Standardlayout
     $X.="\n".' <div class="mpTbNZl0">';
     for($j=($bMitID?0:1);$j<$nSpalten;$j++){
      $X.="\n".'  <div class="mpTbNLst mpTbNLsM">'.fMpDtN($aStru['FN'][$aStru['SP'][$j]]).'</div>';
     }
     $X.="\n".' </div>';
    }else{
     if(file_exists(MP_Pfad.'neue'.$sSegNo.'Kopf.htm')||file_exists(MP_Pfad.'neueKopf.htm')){ //eigene Kopfzeile
      if(!$r=@implode('',(file_exists(MP_Pfad.'neue'.$sSegNo.'Kopf.htm')?file(MP_Pfad.'neue'.$sSegNo.'Kopf.htm'):array('')))) $r=@implode('',(file_exists(MP_Pfad.'neueKopf.htm')?file(MP_Pfad.'neueKopf.htm'):array('')));
      $p=0; while($p=strpos($r,'{',$p+1)) if($i=strpos($r,'}',$p+1)) $r=substr_replace($r,'',$p,$i-$p+1);
      $s=strtolower($r); if(!strpos($s,'<body')&&!strpos($s,'<head')) $X.="\n".' <div class="mpTbNZL0">'."\n".$r."\n </div>";
     }
    }
   }
  }//neuer Kopf

  //Datenzeilen
  $a=$a[1]; $sZl=''; $sId=$a[0]; $sCSS='Dat'.$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
  if(array_search('k',$aStru['FT'])>0) if(isset($a[$nSpalten])) if($j=$a[$nSpalten]) $sCSS='Kat'.$j; // ToDo Kategorie aus Zusatzspalte
  if(MP_Sef){ //SEF-Dateinamen bilden
   $sSefName=$sSegNam; $bSefSuche=true;
   if(isset($a[$nSpalten+1])) $sSefName=fMpDtN($a[$nSpalten+1]); //zusaetzliche Titelspalte
   else for($j=1;$j<$nSpalten;$j++) if($bSefSuche) if($aStru['FT'][$aStru['SP'][$j]]=='t'){ //SEF-Suchen
    $bSefSuche=false; $sSefName=fMpDtN($a[$j]);
    for($j=strlen($sSefName)-1;$j>=0;$j--) //BB-Code weg
     if(substr($sSefName,$j,1)=='[') if($v=strpos($sSefName,']',$j)) $sSefName=substr_replace($sSefName,'',$j,++$v-$j);
    $sSefName=trim(substr(str_replace("\n",' ',str_replace("\r",'',$sSefName)),0,50));
   }
   if($sSefName=trim(str_replace('.','_',trim(str_replace(';','',str_replace(':','',str_replace('�','',str_replace('�','',$sSefName)))))))) $sSefName='-'.$sSefName;
  }else $sSefName='';
  if($bEigeneZeilen) $sZl=$sEigeneZeile; //eigenes Zeilenlayout
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aStru['SP'][$j]; $t=$aStru['FT'][$k]; $sStil=''; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': $s=fMpBBN(fMpDtN($s)); break; //Text
     case 'm': $s=fMpBBN(fMpDtN($s)); break; //Memo
     case 'a': case 'k': case 'o': $s=fMpDtN($s); break; //Aufzaehlung/Kategorie/Postleitzahl
     case 'd': case '@': //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; break;
     case 'z': $sFS.=' mpTbNLsM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sFS.=' mpTbNLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTxN(MP_TxJa); elseif($s=='N') $s=fMpTxN(MP_TxNein); $sFS.=' mpTbNLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sFS.=' mpTbNLsR';
      break;
     case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); $sFS.=' mpTbNLsM'; break; //Zaehlnummer
     case 'l': //Link
      $aI=explode('|',$s); $s=$aI[0]; $v=fMpDtN(isset($aI[1])?$aI[1]:$s);
      if(MP_LinkSymbol){$v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMpDtN($s).'" alt="'.fMpDtN($s).'" />'; $sFS.=' mpTbNLsM';}
      $s='<a class="mpNeue" title="'.fMpDtN($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLinkN($s)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.'</a>';
      break;
     case 'e': //eMail
      $s='<img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" title="'.fMpTxN(MP_TxKontakt).'" alt="'.fMpTxN(MP_TxKontakt).'" />';
      $sFS.=' mpTbNLsM';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       if($nNLF>0){
        $s=MP_TxAutorUnbekannt;
        for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
         if(!$s=$aNutzer[$n][$nNLF]) $s=MP_TxAutorUnbekannt;
         break;
        }
       }else $s=sprintf('%04d',$nId);
      }else $s=MP_TxAutor0000;
      $s=fMpDtN($s); break;
     case 's': $w=fMpDtN($s); //Symbol
      $p=array_search($s,$aStru['SW']); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
      $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.$w.'" alt="'.$w.'" />'; $sFS.=' mpTbNLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s;if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
      $ho=floor((MP_VorschauHoch-$aI[1])*0.5); $hu=max(MP_VorschauHoch-($aI[1]+$ho),0);
      if(!MP_VorschauRahmen) $r=' class="mpTBld"'; else $r=' class="mpVBld" style="width:'.MP_VorschauBreit.'px;text-align:center;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $s='<div'.$r.'><img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.substr($s,strpos($s,'/')+1).'" title="'.substr($s,strpos($s,'/')+1).'" /></div>'; $sFS.=' mpTbNLsM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fMpDtN(strtoupper($w).'-'.MP_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' mpTbNLsM';
      $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'" />';
      $s='<a class="mpNeue" href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&MP_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildKlein; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbNLsM';
   }else $s='&nbsp;';
   if(MP_NeueCssStyle&&($w=$aStru['SS'][$k])||$sStil){$sStil=' style="'.$sStil.(MP_NeueCssStyle?str_replace('`,',';',$w):'').'"';}
   if($aStru['LK'][$k]>0||$t=='b') $s='<a class="mpNeue" href="'.fMpHrefN('detail',(MP_NeuePopup?'':1),$sId.$sSefName,$sQ.(MP_NeuePopup?'&amp;mp_Popup=1':''),$nSegNo).'" title="'.fMpTxN(MP_TxDetail).'"'.(MP_NeueTarget==''?'':' target="'.MP_NeueTarget.MP_NeuesOnClk.'"').'>'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="mpTbNLst'.$sFS.'"'.$sStil.'>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$aStru['FN'][$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if(!$bEigeneZeilen){ //Standardlayout
   $X.="\n".' <div class="mpTbNZl'.$nFarb.'">'.$sZl."\n".' </div>';
  }else{ //eigenes Layout
   $sZl=str_replace('{Nummer}',($bMitID?(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId):''),$sZl);
   $sZl=str_replace('{SendInfo}','&nbsp;',$sZl);
   $sZl=str_replace('{Aendern}','&nbsp;',$sZl);
   $sZl=str_replace('{Kopieren}','&nbsp;',$sZl);
   $sZl=str_replace('{Nachricht}','&nbsp;',$sZl); $p=-1;
   for($j=count($aStru['FN'])-1;$j>=0;$j--) if(!(strpos($sZl,'{'.$aStru['FN'][$j])===false)) $sZl=str_replace('{'.$aStru['FN'][$j].'}','&nbsp;',$sZl);
   while($p=strpos($sZl,'{',$p+1)) if($j=strpos($sZl,'}',$p+1)) if($j<$p+50) $sZl=substr_replace($sZl,'',$p,$j+1-$p);
   $X.="\n".' <div class="mpTbNZL'.$nFarb.'">'."\n".$sZl."\n </div>";
  }
 }//ueber alle Datensaetze
 if($nSegNo!=0) $X.="\n".'</div>'; // Tabelle

 return $X;
}

function fMpDeCodeN($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fMpDtN($s){ //DatenKodierung
 $mpZeichensatz=MP_ZeichnsNorm; if(MP_Popup) $mpZeichensatz=MP_ZeichnsPopf;
 if(MP_Zeichensatz==$mpZeichensatz){if(MP_Zeichensatz!=1) $s=str_replace('"','&quot;',$s);}
 else{
  if($mpZeichensatz!=0) if($mpZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  if(MP_Zeichensatz<=0) $s=str_replace('"','&quot;',$s); elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',$s)); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br />',$s);
}
function fMpTxN($sTx){ //TextKodierung
 if(MP_Zeichensatz<=0) $s=$sTx; elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
function fMpExtLinkN($s){
 if(!defined('MP_ZeichnsExtLink')||MP_ZeichnsExtLink==0) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode($s)));
 elseif(MP_ZeichnsExtLink==1) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s))));
 elseif(MP_ZeichnsExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}
function fMpRqN($sTx){ //Eingaben reinigen
 return stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
}
function fMpRq1N($sTx){ //nur 1 Wort der Eingabe
 $sTx=stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
 if($nP=strpos($sTx,' ')) $sTx=substr($sTx,0,$nP);
 return $sTx;
}
function fMpBBN($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.$o.'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="mpNText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="mpNText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpNeue" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpNeue" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="mpNText"><li class="mpNText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="mpNText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
function fMpNormAdrN($sNam){
 $sNam=str_replace('�','ae',str_replace('�','oe',str_replace('�','ue',str_replace('�','ss',str_replace('"','',str_replace(' ','_',strtolower($sNam)))))));
 $sNam=str_replace('Ä','Ae',str_replace('ä','ae',str_replace('Ö','Oe',str_replace('ö','oe',str_replace('Ü','Ue',str_replace('ü','ue',str_replace('ß','ss',$sNam)))))));
 return str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace('#','_',str_replace('?','_',str_replace('/','_',$sNam)))))));
}
function fMpHrefN($sAct='',$sSei='',$sNum='',$sPar='',$sSeg=false){ //erzeugt einen Link
 $sL=''; if(!$sSeg) $sSeg=MP_Segment; elseif($sSeg=='-') $sSeg='';
 if(!MP_Sef){ //normal
  if($sAct) $sL.='&amp;mp_Aktion='.$sAct;
  if($sSeg>''&&!strpos($sAct,'_')) $sL.='&amp;mp_Segment='.$sSeg;
  if(MP_ListenLaenge>0&&$sSei) $sL.='&amp;mp_Seite='.$sSei;
  if($sNum) $sL.='&amp;mp_Nummer='.$sNum;
  if(MP_Session>'') $sL.='&amp;mp_Session='.MP_Session;
  elseif(defined('MP_NeuSession')) $sL.='&amp;mp_Session='.MP_NeuSession;
  if($sPar) $sL.=$sPar; $sL.=MP_Query; if($sL) $sL='?'.substr($sL,5);
  $sL=(!strpos($sPar,'_Popup=1')?MP_NeueZiel:MP_Url.'marktplatz.php').$sL;
 }else{ // SEF
  if(strrpos(MP_NeueZiel,'/')) $sL=substr(MP_NeueZiel,0,strrpos(MP_NeueZiel,'/')+1);
  if($sAct){
   if($sSeg>''&&!strpos($sAct,'_')){
    $aN=explode(';',MP_Segmente); if(!$sNam=$aN[$sSeg]) $sNam=MP_TxSegment; if(substr($sNam,0,1)=='*') $sNam=substr($sNam,1);
    if($p=strpos($sNum,'-')){$sNam=substr($sNum,$p+1); $sNum=substr($sNum,0,$p);}
    $sL.=fMpNormAdrN($sNam).'-'.$sAct.'-'.$sSeg;
    if((MP_ListenLaenge>0&&$sSei)||$sNum) $sL.='-'.$sSei; if($sNum) $sL.='-'.$sNum;
   }else{
    $sL.=$sAct;
    if(MP_ListenLaenge>0&&$sSei) $sL.='-'.$sSei;
   }
  }else $sL.='marktplatz';
  $sL.='.html';
  if(MP_Session>'') $sPar='&amp;mp_Session='.MP_Session.$sPar;
  elseif(defined('MP_NeuSession')) $sPar='&amp;mp_Session='.MP_NeuSession.$sPar;
  $sPar.=MP_Query; if($sPar) $sL.='?'.substr($sPar,5);
 }
 return $sL;
}
?>