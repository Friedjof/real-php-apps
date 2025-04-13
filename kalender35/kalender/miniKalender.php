<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./miniKalender.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$sKalHtmlVor=''; $sKalHtmlNach=''; $bKalOK=true;  //Seitenkopf, Seitenfuss, Status,

if(!strstr($sKalSelf,'/miniKalender.php')){ //includierter Aufruf
 if(defined('KAL_Version')){ //Variablen includiert
  define('KAL_MiniZiel',(KAL_MiniLink!=''?KAL_MiniLink:$sKalSelf));
 }else{ //Variablen nicht includiert
  $bKalOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>kalWerte.php</i> wurde nicht includiert!</b></p>';
 }
}else{//Script laeuft allein als miniKalender.php
 @include('kalWerte.php'); define('KAL_MiniZiel',(KAL_MiniLink==''?'kalender.php':KAL_MiniLink));
 if(defined('KAL_Version')){
  header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(KAL_Schablone){ //mit Seitenschablone
   $sKalHtmlNach=(file_exists(KAL_Pfad.'miniKalender.htm')?implode('',file(KAL_Pfad.'miniKalender.htm')):'');
   if($nKalJ=strpos($sKalHtmlNach,'{Inhalt}')){
    $sKalHtmlVor=substr($sKalHtmlNach,0,$nKalJ); $sKalHtmlNach=substr($sKalHtmlNach,$nKalJ+8); //Seitenkopf, Seitenfuss
   }else{$sKalHtmlVor='<p style="color:#AA0033;">Layout-Schablone <i>miniKalender.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.$sKalHttp.KAL_Www.'kalStyles.css">'."\n\n";
  }
 }else{$bKalOK=false; echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bKalOK){ //Konfiguration eingelesen
 if(defined('KAL_WarnMeldungen')&&KAL_WarnMeldungen) error_reporting(E_ALL ^ E_NOTICE);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if(!defined('KAL_Url')) define('KAL_Url',$sKalHttp.KAL_Www); define('KAL_MiniSelf',$sKalSelf);
 //geerbte GET/POST-Parameter aufbewahren und einige Kalenderparameter ermitteln
 $sKalQry=''; $sKalHid=''; $sKalSession=''; $sKalSuchParam=''; $sKalIndex=''; $sKalRueck=''; $sKalStart=''; $sKalNummer='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['kal_Aktion'])) $sKalAktion=fKalRqM($_GET['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_GET['kal_Session'])&&$sKalAktion!='login') $sKalSession='&amp;kal_Session='.fKalRqM($_GET['kal_Session']);
  reset($_GET);
  if(!defined('KAL_Query')) foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }
 }else{ //bei POST
  if(isset($_POST['kal_Session'])&&$_POST['kal_Session']!='') $sKalSession='&amp;kal_Session='.fKalRqM($_POST['kal_Session']);
  reset($_POST);
  if(!defined('KAL_Query')) foreach($_POST as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_') if(is_string($sKalV)){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }
 }
 if(!defined('KAL_Query')) define('KAL_Query',$sKalQry); if(!defined('KAL_Hidden')) define('KAL_Hidden',$sKalHid);
 if(!defined('KAL_Session')) define('KAL_Session',$sKalSession);

 //Beginn der Ausgabe
 echo $sKalHtmlVor;
 if(KAL_MiniPopup&&(KAL_MiniTarget!='_self'||KAL_MiniTarget!='_parent'||KAL_MiniTarget!='_top')){
  echo "\n".'<script>function MinWin(sURL){kalWin=window.open(sURL,"'.KAL_MiniTarget.'","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  define('KAL_MiniOnClk','" onclick="MinWin(this.href);return false;');
 }else define('KAL_MiniOnClk','');

 echo "\n".'<div class="kalBox">'."\n\n".'<table class="kalBlnd" style="border:0">';
 echo fKalMini();
 echo "\n".'</table>'."\n\n".'</div>'."\n".$sKalHtmlNach;
}
echo "\n";

function fKalMini(){
 global $kal_FeldName, $kal_FeldType, $kal_WochenTag;

 if(!isset($_GET['kal_Mini'])){$nM=date('n'); $nJ=date('Y');} else list($nJ,$nM)=explode('-',fKalRqM($_GET['kal_Mini']));
 $nM=(int)$nM; $nJ=(int)$nJ; $nMn=$nM+1; $nMv=$nM-1; $nJn=$nJ; $nJv=$nJ; if($nMn>12){$nMn=1; $nJn++;} if($nMv<1){$nMv=12; $nJv--;}
 $sS=sprintf('%04d-%02d',$nJ,$nM); $sN=sprintf('%04d-%02d',$nJn,$nMn); $sV=sprintf('%04d-%02d',$nJv,$nMv); //Blaettern-Parameter
 $nHt=(int)date('j'); $nHm=(int)date('n'); $nHj=(int)date('Y'); //heute
 $nDatA=@mktime(10,0,0,$nM,1,$nJ); $sDatA=sprintf('%04d-%02d-01',$nJ,$nM); //Anfang
 $nDatE=@mktime(12,0,0,$nM+KAL_MiniMonate,1,$nJ); $sDatE=date('Y-m-d',$nDatE); //Ende
 $aDat=array(); $aNrs=array(); for($i=$nDatA;$i<=$nDatE;$i+=86400) $aDat[date('m-d',$i)]=''; //Terminarray vorbereiten

 $nFelder=count($kal_FeldName); $nDatFeld2=0; $nTextFeld=1; $nVerstecktFeld=0; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL; $sQ=''; $s='';
 for($i=1;$i<$nFelder;$i++){ //ueber alle Felder
  $t=$kal_FeldType[$i]; if($t=='v') $nVerstecktFeld=$i;
  if(isset($_GET['kal_'.$i.'F1'])) $s=fKalRqM($_GET['kal_'.$i.'F1']); elseif(isset($_POST['kal_'.$i.'F1'])) $s=fKalRqM($_POST['kal_'.$i.'F1']); else $s='';
  if(strlen($s)){ //erstes Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{$a1Filt[$i]=fKalNormDatM($s); $a2Filt[$i]='';} $sQ.='&amp;kal_'.$i.'F1='.rawurlencode($s);
  }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&(KAL_Session!='')?'':'N'); $a2Filt[$i]='';} //versteckt
  if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRqM($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRqM($_POST['kal_'.$i.'F2']); else $s='';
  if(strlen($s)){ //zweites Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
   elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
   if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormDatM($s); $sQ.='&amp;kal_'.$i.'F2='.rawurlencode($s);
  }
  if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRqM($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRqM($_POST['kal_'.$i.'F3']); else $s='';
  if(strlen($s)){ //drittes Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); $sQ.='&amp;kal_'.$i.'F3='.rawurlencode($s);
  }
  if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; //2. Datum suchen
 }

 $bAnzeigen=$nVerstecktFeld==0||(KAL_NVerstecktSehen&&KAL_Session!=''); //alle anzeigen oder einzeln pruefen
 if(KAL_MiniOhneAltes&&empty($a1Filt[1])){
  $bOhneAltes=true; $sIvAnf=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); if($sIvAnf>$sDatA) $sDatA=$sIvAnf;
 }else $bOhneAltes=false;
 //Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$i])); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1);
   if($bOK&&($bAnzeigen||$a[$nVerstecktFeld]!='J')){ //kein versteckter Termin
    $sAnfangDat=substr($a[1],0,10); if(!$sEndeDat=substr($a[($nDatFeld2>0?$nDatFeld2:1)],0,10)) $sEndeDat=$sAnfangDat;
    if($sEndeDat>=$sDatA&&$sAnfangDat<$sDatE){ //Termin laeuft im Intervall
     if(is_array($a1Filt)){
      reset($a1Filt);
      foreach($a1Filt as $j=>$v) if($bOK){ //Suchfiltern 1-2
       $t=($kal_FeldType[$j]); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
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
        if($k=strlen($w)){if(sprintf('%0d',$a[$j])==$w) $b2=true; else $b2=false;} else $b2=false;
        if(!(sprintf('%0d',$a[$j])==$v||$b2)) $bOK=false;
       }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $bOK=false;}}
      }
     }
     if($bOK&&is_array($a3Filt)){ //Suchfiltern 3
      reset($a3Filt); foreach($a3Filt as $j=>$v) if($kal_FeldType[$j]!='u'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$bOK=false; break;}}elseif(sprintf('%0d',$a[$j])==$v){$bOK=false; break;}
     }
     if($bOK){
      if((!$t=str_replace('`,',';',str_replace('\n ',"\n",$a[KAL_MiniTextFeld])))||KAL_MiniTextFeld==1) $t=fKalZeigeDatumM($sAnfangDat); $t=", \n".$t;
      if(KAL_MiniTextFld2>0){if((!$v=str_replace('`,',';',str_replace('\n ',"\n",$a[KAL_MiniTextFld2])))||KAL_MiniTextFld2==1) $v=fKalZeigeDatumM($sAnfangDat); $t.=", \n".$v;}
      if($sAnfangDat<$sDatA) $sAnfangDat=$sDatA; if($sEndeDat>$sDatE) $sEndeDat=$sDatE;
      $sIdx=substr($sAnfangDat,5,5); $aDat[$sIdx].=$t; $aNrs[$sIdx]=(!isset($aNrs[$sIdx])?$a[0]:(KAL_MiniSicht==1?0:$aNrs[$sIdx]));  //eintragen
      if($sAnfangDat!=$sEndeDat){ //Mehrtagstermin
       $j=@mktime(10,0,0,substr($sAnfangDat,5,2),substr($sAnfangDat,8,2),substr($sAnfangDat,0,4))+86400; $s=date('Y-m-d',$j);
       while($s<=$sEndeDat){$sIdx=substr($s,5,5); $aDat[$sIdx].=$t; $aNrs[$sIdx]=(!isset($aNrs[$sIdx])?$a[0]:(KAL_MiniSicht==1?0:$aNrs[$sIdx])); $j+=86400; $s=date('Y-m-d',$j);}
  }}}}}
 }else{ //SQL
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){
   if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v) if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //Suchfiltern 1-2
    $s.=' AND(kal_'.$j; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
     $s.=' LIKE "%'.fKalMDtCode($v).'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.fKalMDtCode($w).'%"';
    }elseif($t=='d'||$t=='@'){
     if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"'; //sonstiges Datum
    }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
     $v=str_replace(',','.',$v);
     if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
    }elseif($t=='o'){
     $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
    }elseif($t=='u'){
     $s=substr($s,0,strrpos($s,'(kal_')).'(CONVERT(kal_'.$j.',INTEGER)="'.$v.'"'; if(strlen($w)) $s.=' OR CONVERT(kal_'.$j.',INTEGER)="'.$w.'"';
    }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
    $s.=')';
   }
   if(is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
    $t=$kal_FeldType[$j];
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x')
     $s.=' AND NOT(kal_'.$j.' LIKE "%'.fKalMDtCode($v).'%")';
    elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
    elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.$v.'")';
   }

   if($rR=$DbO->query('SELECT id,kal_1,kal_'.($nDatFeld2>0?$nDatFeld2:1).',kal_'.KAL_MiniTextFeld.(KAL_MiniTextFld2>0?',kal_'.KAL_MiniTextFld2:'').' FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').') AND(kal_1 BETWEEN "'.$sDatA.'" AND "'.$sDatE.'"'.($nDatFeld2>1?' OR kal_'.$nDatFeld2.' BETWEEN "'.$sDatA.'" AND "'.$sDatE.'")':')').$s.($bAnzeigen?'':' AND kal_'.$nVerstecktFeld.'<>"J"'))){
    while($a=$rR->fetch_row()){
     $sAnfangDat=substr($a[1],0,10); if(!$sEndeDat=substr($a[2],0,10)) $sEndeDat=$sAnfangDat;
     if((!$t=$a[3])||KAL_MiniTextFeld==1) $t=fKalZeigeDatumM($sAnfangDat); $t=", \n".$t;
     if(KAL_MiniTextFld2>0){if((!$v=$a[4])||KAL_MiniTextFld2==1) $v=fKalZeigeDatumM($sAnfangDat); $t.=", \n".$v;}
     if($sAnfangDat<$sDatA) $sAnfangDat=$sDatA; if($sEndeDat>$sDatE) $sEndeDat=$sDatE;
     $sIdx=substr($sAnfangDat,5,5); $aDat[$sIdx].=$t; $aNrs[$sIdx]=(!isset($aNrs[$sIdx])?$a[0]:(KAL_MiniSicht==1?0:$aNrs[$sIdx])); //eintragen
     if($sAnfangDat!=$sEndeDat){ //Mehrtagstermin
      $j=@mktime(10,0,0,substr($sAnfangDat,5,2),substr($sAnfangDat,8,2),substr($sAnfangDat,0,4))+86400; $s=date('Y-m-d',$j);
      while($s<=$sEndeDat){$sIdx=substr($s,5,5); $aDat[$sIdx].=$t; $aNrs[$sIdx]=(!isset($aNrs[$sIdx])?$a[0]:(KAL_MiniSicht==1?0:$aNrs[$sIdx])); $j+=86400; $s=date('Y-m-d',$j);}
    }}$rR->close();
 }}}//SQL

 $X=''; $sM=$sQ; $nMO=$nM; $nJO=$nJ; $aMonate=explode(';',';'.KAL_TxKMonate); //Ausgabe
 if($m=strpos($sM,'kal_1F2=')) $sM=strstr(substr($sM,$m+15),'&amp;'); if($m=strpos($sM,'kal_1F1=')) $sM=strstr(substr($sM,$m+15),'&amp;');
 if(KAL_MiniVertikal){$nZl=floor(KAL_MiniMonate/KAL_MiniReihen); $nSp=KAL_MiniReihen; $nZI=1; $nSI=$nZl;}
 else{$nZl=KAL_MiniReihen; $nSp=floor(KAL_MiniMonate/KAL_MiniReihen); $nZI=$nSp; $nSI=1;}

 $sQzS='?'; $sQzZ='?'; if(strpos(KAL_MiniSelf,'?')) $sQzS='&amp;'; if(strpos(KAL_MiniZiel,'?')) $sQzZ='&amp;';
 for($iZ=1;$iZ<=$nZl;$iZ++){
  $X.="\n".'<tr>';
  for($iS=1;$iS<=$nSp;$iS++){
   $T1=@mktime(16,0,0,$nM,1,$nJ); $Tl=date('t',$T1); $nWSp=date('w',$T1); if($nWSp<=0) $nWSp=7; $nWSp--; //erster-letzter Tag, Anfangsspalte
   $T0=$T1-86400*$nWSp;
   $sDat1=sprintf('%04d-%02d-01',$nJ,$nM); if($bOhneAltes) if($sIvAnf>$sDat1) $sDat1=$sIvAnf;
   $X.="\n".'<td class="kalBlnd" style="vertical-align:top;"><!-- MiniKalender '.$iZ.'.'.$iS.' -->'; //Monatskopf
   $X.="\n".'<div class="kalMini">';
   $X.="\n".'<table class="kalMini" style="border:0">
 <tr>'."\n";
   if(KAL_MiniWochenNr) $X.='<td class="kalMinK">'.(KAL_MiniTxWo?fKalTxM(KAL_MiniTxWo):'&nbsp;')."</td>\n";
   $X.='  <td class="kalMinK"><a class="kalMinK" href="'.KAL_MiniSelf.$sQzS.substr(KAL_Query.KAL_Session.'&amp;',5).'kal_Mini='.$sV.$sQ.'" title="'.fKalTxM($aMonate[$nMv]).' '.sprintf('%04d',$nJv).'">&laquo;</a></td>
  <td class="kalMinK" colspan="5"><a class="kalMinK" href="'.KAL_MiniZiel.$sQzZ.substr(KAL_Query.KAL_Session.'&amp;',5).'kal_Intervall=0&amp;kal_1F1='.fKalZeigeDatumM($sDat1).'&amp;kal_1F2='.fKalZeigeDatumM(sprintf('%04d-%02d-%02d',$nJ,$nM,$Tl)).$sM.'&amp;kal_Mini='.$sS.'" target="'.KAL_MiniTarget.KAL_MiniOnClk.'">'.fKalTxM($aMonate[$nM]).' '.$nJ.'</a></td>
  <td class="kalMinK"><a class="kalMinK" href="'.KAL_MiniSelf.$sQzS.substr(KAL_Query.KAL_Session.'&amp;',5).'kal_Mini='.$sN.$sQ.'" title="'.fKalTxM($aMonate[$nMn]).' '.sprintf('%04d',$nJn). '">&raquo;</a></td>
 </tr>
 <tr>'."\n";
   if(KAL_MiniWochenNr) $X.='<td class="kalMinK">'.(KAL_MiniTxNr?fKalTxM(KAL_MiniTxNr):'&nbsp;')."</td>\n";
   $X.='  <td class="kalMinK">'.fKalTxM($kal_WochenTag[1]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[2]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[3]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[4]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[5]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[6]).'</td><td class="kalMinK">'.fKalTxM($kal_WochenTag[0]).'</td>
 </tr>
 <tr>'."\n";
   if(KAL_MiniWochenNr) $X.='  <td class="kalMinK">'.date('W',$T0).'</td>'."\n";
   if($nWSp>0){ //monatsfremder Anfang
    $j=date('j',$T1-86400*$nWSp); for($i=1;$i<=$nWSp;$i++) $X.='  <td class="kalMinX">'.(KAL_MiniFremd?$j++:'.').'</td>'."\n";
   }
   for($i=1;$i<=$Tl;$i++){ //ueber alle Tage
    if(++$nWSp>7){//neue Zeile
     $nWSp=1; $X.=" </tr>\n <tr>\n"; $T0+=604800; // 7*86400
     if(KAL_MiniWochenNr) $X.='  <td class="kalMinK">'.date('W',$T0)."</td>\n";
    }
    $sIdx=sprintf('%02d-%02d',$nM,$i); if($i!=$nHt||$nM!=$nHm||$nJ!=$nHj) $sCss='D'; else $sCss='H'; //heute?
    if($t=$aDat[$sIdx]){
     $sLa='<a class="kalMinL" href="'.KAL_MiniZiel.$sQzZ.substr(KAL_Query.KAL_Session.'&amp;',5).(KAL_MiniSicht>0?(isset($aNrs[$sIdx])&&$aNrs[$sIdx]>0?'kal_Aktion=detail&amp;kal_Nummer='.$aNrs[$sIdx].'&amp;':''):'').'kal_Intervall=0&amp;kal_1F1='.fKalZeigeDatumM(sprintf('%04d-%02d-%02d',$nJ,$nM,$i)).$sM.'&amp;kal_Mini='.$sS.'" target="'.KAL_MiniTarget.KAL_MiniOnClk.'" title="'.fKalTxM(substr($t,3)).'">';
     $sLe='</a>'; if($sCss!='H') $sCss='L'; if($i<10){$sLa.='&nbsp;'; $sLe='&nbsp;'.$sLe;}
    }else{$sLa=''; $sLe='';}
    $X.='  <td class="kalMin'.$sCss.'">'.$sLa.$i.$sLe."</td>\n";
   }
   $j=1; for($i=$nWSp;$i<7;$i++) $X.='  <td class="kalMinX">'.(KAL_MiniFremd?$j++:'.').'</td>'."\n"; //monatsfremdes Ende
   $X.=" </tr>\n</table>\n</div>\n</td>"; //Monatsende
   $nM+=$nSI; if($nM>12){$nM-=12; $nJ++;} $nMn=$nM+1; $nMv=$nM-1; $nJn=$nJ; $nJv=$nJ; if($nMn>12){$nMn=1; $nJn++;} if($nMv<1){$nMv=12; $nJv--;}
  }
  $X.="\n".'</tr>';
  if(KAL_MiniVertikal){$nM=$nMO; $nJ=$nJO; if(++$nM>12){$nM=1; $nJ++;} $nMn=$nM+1; $nMv=$nM-1; $nJn=$nJ; $nJv=$nJ; if($nMn>12){$nMn=1; $nJn++;} if($nMv<1){$nMv=12; $nJv--;} $nMO=$nM; $nJO=$nJ;}
 }
 return $X;
}

function fKalZeigeDatumM($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalNormDatM($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}
function fKalMDtCode($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
}
function fKalTxM($sTx){ //TextKodierung
 if(KAL_Zeichensatz==0) return str_replace('"','&quot;',$sTx); elseif(KAL_Zeichensatz==2) return iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',$sTx)); else return htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
}
function fKalRqM($sTx){
 return stripslashes(str_replace('"',"'",trim($sTx)));
}
?>