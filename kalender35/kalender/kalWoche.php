<?php
function fKalSeite(){ //Seiteninhalt
 global $kal_FeldName, $kal_FeldType, $kal_LinkFeld, $kal_Symbole, $kal_WochenTag, $kal_Kategorien,
  $aMonate, $sSes, $sMon, $sWoc, $sQ, $aNutzer, $nNutzerZahl, $ksNutzerListFeld, $bEinKlickZusage, $bNichtZugesagt, $nEinKlickTId;

 $X=''; $sQ=''; $Et=''; $Es='Fehl'; $Em=''; $nTrms=0;

 $nHtT=(int)date('j'); $nHtM=(int)date('n'); $nHtJ=(int)date('Y'); // heute
 $sWoc=$_GET['kal_Woche']; $nJhr=(int)substr($sWoc,0,4); $nWoc=(int)substr($sWoc,5,2); //StartWoche

 $nZt=@mktime(16,0,0,1,1,$nJhr); $nWt1=date('N',$nZt); if($nWt1>4) $nZt+=(8-$nWt1)*86400; elseif($nWt1>1) $nZt+=(1-$nWt1)*86400; // Beginn 1. Woche
 if($nWoc>1) $nZt+=($nWoc-1)*604800; // 86400*7 -> Wochenbeginn
 $sDatA=date('Y-m-d',$nZt); $sDatE=date('Y-m-d',$nZt+518400); $sMon=(int)substr((substr($sDatE,8,2)>'03'?$sDatE:$sDatA),5,2); // erster und letzter Tag der Woche

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 if(!$sSes=KAL_Session) if(defined('KAL_NeuSession')) $sSes=KAL_NeuSession; $bSes=false; //Session pruefen
 if($sSes=substr($sSes,17,12)){
  $sUId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sUId=$sUId.';'; $p=strlen($sUId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sUId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Et=KAL_TxSessionUngueltig;
     break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.KAL_SqlTabN.' WHERE nr="'.$sUId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
  }}else $Et=KAL_TxSessionZeit;
 }

 $s=(isset($_GET['kal_Such'])?fKalRq($_GET['kal_Such']):(isset($_POST['kal_Such'])?fKalRq($_POST['kal_Such']):'')); $sSuch='';
 if(strlen($s)>0){ //Schnellsuche
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sSuch=$s; $sQ.='&amp;kal_Such='.rawurlencode($s); $Em.=', '.htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1');
 }
 //3-Suchparameter
 $nFelder=count($kal_FeldName); $nDatFeld2=0; $nVerstecktFeld=0; $nKapPos=0; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL; $s='';
 for($i=1;$i<$nFelder;$i++){ //ueber alle Felder
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; if($t=='v') $nVerstecktFeld=$i; if($sFN=='KAPAZITAET') $nKapPos=$i;
  if(strlen($sSuch)==0){ //keine Schnellsuche
   if(isset($_GET['kal_'.$i.'F1'])) $s=fKalRq($_GET['kal_'.$i.'F1']); elseif(isset($_POST['kal_'.$i.'F1'])) $s=fKalRq($_POST['kal_'.$i.'F1']); else $s='';
   if(strlen($s)){ //erstes Suchfeld ausgefuellt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{$a1Filt[$i]=fKalNormMDat($s); $a2Filt[$i]='';} $sQ.='&amp;kal_'.$i.'F1='.rawurlencode($s); $Em.=', '.$sFN;
   }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&$bSes?'':'N'); $a2Filt[$i]='';} //versteckt
   if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRq($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRq($_POST['kal_'.$i.'F2']); else $s='';
   if(strlen($s)){ //zweites Suchfeld ausgefuellt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
    elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
    if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormMDat($s); $sQ.='&amp;kal_'.$i.'F2='.rawurlencode($s); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
   }
   if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRq($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRq($_POST['kal_'.$i.'F3']); else $s='';
   if(strlen($s)){ //drittes Suchfeld ausgefuellt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); $sQ.='&amp;kal_'.$i.'F3='.rawurlencode($s); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
 }}}

 $bAnzeigen=$nVerstecktFeld==0||(KAL_NVerstecktSehen&&$bSes); //alle anzeigen oder einzeln pruefen
 if(KAL_MonOhneAltes&&empty($a1Filt[1])){
  $bOhneAltes=true; $sAltDat=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); if($sAltDat>$sDatA) $sDatA=$sAltDat;
 }else $bOhneAltes=false;

 //Daten holen
 $aTrm=array(); for($i=1;$i<=31;$i++) $aTag[$i]=array();
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$i])); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1);
   if($bOK&&($bAnzeigen||$a[$nVerstecktFeld]!='J')){ //kein versteckter Termin
    $sAnfangDat=substr($a[1],0,10); if(!$sEndeDat=substr($a[($nDatFeld2>0?$nDatFeld2:1)],0,10)) $sEndeDat=$sAnfangDat;
    if($sEndeDat>=$sDatA&&$sAnfangDat<=$sDatE){ //Termin laeuft im Intervall
     if(strlen($sSuch)==0){ //keine Schnellsuche
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
     }elseif($bOK){ $bOK=false; //Schnellsuche
      for($j=1;$j<$nFelder;$j++){
       $t=$kal_FeldType[$j];
       if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='g') if(stristr($a[$j],$sSuch)){$bOK=true; break;}
      }
     }
     if($bOK){
      if($sAnfangDat<$sDatA) $sAnfangDat=$sDatA; if($sEndeDat>$sDatE) $sEndeDat=$sDatE;
      $nId=$a[0]; $aTrm[$nId]=$a; $k=(int)substr($sAnfangDat,8,2); $aTag[$k][]=$nId; $nTrms++; //eintragen
      if($sAnfangDat!=$sEndeDat){ //Mehrtagstermin
       $w=(int)substr($sEndeDat,8,2); for($j=$k+1;$j<=$w;$j++) $aTag[$j][]=$nId;
      }
     }
    }
   }
  }
 }elseif($DbO){ //SQL
  if(strlen($sSuch)==0){ //keine Schnellsuche
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v) if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //Suchfiltern 1-2
    $s.=' AND(kal_'.$j; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
     $s.=' LIKE "%'.fKalDtCodeM($v).'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.fKalDtCodeM($w).'%"';
    }elseif($t=='d'||$t=='@'){
     if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"'; //sonstiges Datum
    }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
     $v=str_replace(',','.',$v);
     if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
    }elseif($t=='o'){
     $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
    }elseif($t=='u'){
     $s=substr($s,0,strrpos($s,'(kal_')).'(CONVERT(kal_'.$j.',INTEGER)="'.$v.'"'; if(strlen($w)) $s.=' OR CONVERT(kal_'.$j.',INTEGER)="'.$w.'"'; //ToDo
    }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
    $s.=')';
   }
   if(is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
    $t=$kal_FeldType[$j];
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x')
     $s.=' AND NOT(kal_'.$j.' LIKE "%'.fKalDtCodeM($v).'%")';
    elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
    elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.$v.'")';
   }
  }else{ //Schnellsuche
   for($j=1;$j<$nFelder;$j++){
    $t=$kal_FeldType[$j];
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='g') $s.=' OR kal_'.$j.' LIKE "%'.fKalDtCodeM($sSuch).'%"';
   }
   $s=' AND('.substr($s,4).')';
  }
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').') AND(kal_1 BETWEEN "'.$sDatA.'" AND "'.$sDatE.'x"'.($nDatFeld2>1?' OR kal_'.$nDatFeld2.' BETWEEN "'.$sDatA.'" AND "'.$sDatE.'x")':')').$s.($bAnzeigen?'':' AND kal_'.$nVerstecktFeld.'<>"J"').' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
   while($a=$rR->fetch_row()){
    array_splice($a,1,1);
    $sAnfangDat=substr($a[1],0,10); if(!$sEndeDat=substr($a[($nDatFeld2>0?$nDatFeld2:1)],0,10)) $sEndeDat=$sAnfangDat;
    if($sAnfangDat<$sDatA) $sAnfangDat=$sDatA; if($sEndeDat>$sDatE) $sEndeDat=$sDatE;
    $nId=$a[0]; $aTrm[$nId]=$a; $k=(int)substr($sAnfangDat,8,2); $aTag[$k][]=$nId; $nTrms++; //eintragen
    if($sAnfangDat!=$sEndeDat){ //Mehrtagstermin
     $w=(int)substr($sEndeDat,8,2); for($j=$k+1;$j<=$w;$j++) $aTag[$j][]=$nId;
   }}$rR->close();
  }else $Et=KAL_TxSqlFrage;
 }//SQL

 //Zusatzicons ermitteln
 $bMonatsInfo=(KAL_GastMoInfo||$bSes?KAL_MonatsInfo:false);
 $bMonatsErinn=(KAL_GastMoErinn||$bSes?KAL_MonatsErinn:false);
 $bMonatsNachr=(KAL_GastMoBenachr||$bSes?KAL_MonatsBenachr:false);
 $bMonatsZusage=(KAL_ZusageSystem&&(KAL_GastMoZusage||$bSes)?KAL_MonatsZusage:false);
 $bMonatsZusagS=(KAL_ZusageSystem&&(KAL_GastMoZusagS||$bSes)&&$nKapPos?KAL_MonatsZusagS:false);
 $bMonatsZusagHG=false; if($bMonatsZusagS) if($bMonatsZusagHG=KAL_MonZusageHG) $bMonatsZusagS=false;
 $bMonatsZusagZ=(KAL_ZusageSystem&&(KAL_GastMoZusagZ||$bSes)?KAL_MonatsZusagZ:false);
 $bMonatsICal=(KAL_GastMoICal||$bSes&&KAL_MonatsICal?KAL_MonatsICal:false);

 //1-Klick-Zusagen
 $bEinKlickZusage=(KAL_ZusageSystem&&KAL_EinKlickLZusage&&$bSes); if($bEinKlickZusage) include KAL_Pfad.'kalZusage1Klick.php';

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#'); $nNutzerZahl=0;
 if(($n=array_search('u',$kal_FeldType))&&($n==KAL_MDetail3Fld||$n==KAL_MDetail2Fld||$n==KAL_MDetail1Fld)){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fKalDeCode($a[2]); $a[4]=fKalDeCode($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;} $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $ksNutzerListFeld=(KAL_NListeAnders&&$bSes?KAL_NNutzerListFeld:KAL_NutzerListFeld);
 }

 //eventuell Zusagedaten holen
 $aZusageTermine=array(); $aZusageZahl=array();
 if(KAL_ZusageSystem&&($bMonatsZusagZ&&(strpos('x'.KAL_MonZusagZMuster,'#Z')>0||strpos('x'.KAL_MonZusagZMuster,'#R')>0)||KAL_MonatsZusagS)||$bSes&&($bMonatsZusage||array_search('#',$kal_FeldType))){
  $nNId=(int)substr($sSes,0,4);
  $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageAnzahlPos=array_search('ANZAHL',$kal_ZusageFelder);
  if(!KAL_SQL){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $n=count($aD); $m=max(9,$nZusageAnzahlPos+2);
   for($i=1;$i<$n;$i++){
    $a=explode(';',$aD[$i],$m); $k=(int)$a[1];
    if($a[7]==$nNId) $aZusageTermine[$k]=true;
    if($nZusageAnzahlPos>0) if($z=(int)$a[$nZusageAnzahlPos]) if($a[6]=='1'||!KAL_ZaehleAktiveZusagen) if(isset($aZusageZahl[$k])) $aZusageZahl[$k]+=$z; else $aZusageZahl[$k]=$z;
   }
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,termin,aktiv,benutzer'.($nZusageAnzahlPos>0?',dat_'.$nZusageAnzahlPos:'').' FROM '.KAL_SqlTabZ)){
    while($a=$rR->fetch_row()){
     $k=(int)$a[1]; if($a[3]==$nNId) $aZusageTermine[$k]=true; //schon zugesagt
     if($nZusageAnzahlPos>0) if($z=(int)$a[4]) if($a[2]=='1'||!KAL_ZaehleAktiveZusagen) if(isset($aZusageZahl[$k])) $aZusageZahl[$k]+=$z; else $aZusageZahl[$k]=$z;
    }
    $rR->close();
  }}
 }

 // Detail als Popup-Fenster
 if((KAL_DetailPopup||KAL_MailPopup||(KAL_ZusagePopup&&KAL_ZusageSystem))&&!defined('KAL_KalWin')){$X="\n".'<script>function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");'.(KAL_VBoxMon?"hideVBox('0');":'').'kalWin.focus();}</script>'; define('KAL_KalWin',true);}
 if(KAL_CalPopup&&($bMonatsICal||KAL_VBoxMon&&KAL_VBoxICal>=0)) $X.="\n".'<script>function ExpWin(sURL){expWin=window.open(sURL,"expwin","width='.KAL_CalPopupBreit.',height='.KAL_CalPopupHoch.',left='.KAL_CalPopupX.',top='.KAL_CalPopupY.',menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");expWin.focus();}</script>';

 // Meldung vorbereiten
 $aMonate=(KAL_MonatMLang>0?explode(';',';'.(KAL_MonatMLang==2?KAL_TxLMonate:KAL_TxKMonate)):array('-','1','2','3','4','5','6','7','8','9','10','11','12'));
 if(empty($Et)){
  if(substr($Em,0,1)==',') $Et=str_replace('#S',substr($Em,1),KAL_TxWocSuch); else $Et=KAL_TxWocGsmt;
  $Et=str_replace('#W',$nWoc,str_replace('#Y',$nJhr,str_replace('#N',$nTrms,$Et))); $Es='Meld';
 }

 // Navigation, Schnellsuche
 if($sSes) $sSes='&amp;kal_Session='.$sSes.(strpos(KAL_Session,'Z')?'&amp;kal_Zentrum=1':'');
 if(KAL_MNaviOben>0||KAL_MNaviUnten>0) $sNavig=fKalMNavigator($nJhr,$nWoc,$sQ,$sSes);
 if(KAL_MSuchFilter>0) $sSuch=fKalSuchFilter(isset($_GET['kal_Such'])?fKalRq($_GET['kal_Such']):(isset($_POST['kal_Such'])?fKalRq($_POST['kal_Such']):''),$sWoc,$sSes);
 if(KAL_MSuchFilter>0&&KAL_MSuchFilter<4) $X.="\n".$sSuch;
 if(KAL_MNaviOben==1) $X.="\n".str_replace('...','<p class="kal'.$Es.'">'.fKalTx($Et).'</p>',$sNavig);
 else $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';
 if(KAL_MSuchFilter>3&&KAL_MSuchFilter<7) $X.="\n".$sSuch; $sNavig=str_replace('...','&nbsp;',$sNavig);

 //eigene Layoutzeile pruefen
 if(($bEigeneZeilen=KAL_MEigenesLayout)&&file_exists(KAL_Pfad.'kalMonatsZelle.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'kalMonatsZelle.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }
 $nKatPos=array_search('k',$kal_FeldType);

 $X.="\n\n".'<div class="kalTabl">';
 $X.="\n".' <div class="kalTZ0M">'; // Kopfzeile
 if(KAL_MonWochNr) $X.="\n".'  <div class="kalTbSp0">'.(KAL_MonTxNr?fKalTx(KAL_MonTxNr):'&nbsp;').'</div>';
 $X.="\n".'  <div class="kalTbSpG">';
 $X.="\n".'   <div class="kalTabT">';
 $X.="\n".'    <div class="kalTbSpK kalTbWK1">'.fKalTx($kal_WochenTag[1]).'</div><div class="kalTbSpK kalTbWK2">'.fKalTx($kal_WochenTag[2]).'</div><div class="kalTbSpK kalTbWK3">'.fKalTx($kal_WochenTag[3]).'</div><div class="kalTbSpK kalTbWK4">'.fKalTx($kal_WochenTag[4]).'</div><div class="kalTbSpK kalTbWK5">'.fKalTx($kal_WochenTag[5]).'</div><div class="kalTbSpK kalTbWK6">'.fKalTx($kal_WochenTag[6]).'</div><div class="kalTbSpK kalTbWK7">'.fKalTx($kal_WochenTag[0]).'</div>';
 $X.="\n".'   </div>';
 $X.="\n".'  </div>';
 $X.="\n".' </div>';
 $X.="\n".' <div class="kalTbZlM">';
 if(KAL_MonWochNr) $X.="\n".'  <div class="kalTbSpW">'.str_replace('#W',date('W',$nZt),KAL_TxMWochNr).'</div>';
 $X.="\n".'  <div class="kalTbSpG">';
 $X.="\n".'   <div class="kalTabT">';

 for($i=1;$i<=7;$i++){ //ueber alle Tage
  $sD=date('Y-m-d',$nZt); $s2=substr($sD,5,2); $s3=substr($sD,0,4); $s1=substr($sD,8,2); $nD=(int)$s1; $nM=(int)$s2; $nJ=(int)$s3; $nZt+=86400;
  $sTrm=''; $sDtLnk1=''; $sDtLnk2=''; $sHgDet=''; $sHgDat=''; $sCss='Dat'; if($nD==$nHtT&&$nM==$nHtM&&$nJ==$nHtJ) $sCss='Hte'; //heute?
  $sDtTpl=fKalDtTempl($s2,$s3); $sDt=str_replace('##',sprintf('%02d',$s1),$sDtTpl); //anzuzeigendes Datum
  if($nTrms=count($aTag[$nD])){ //Datum ist besetzt
   $nTId=$aTag[$nD][0]; $sMehr=''; $sDtLnk2='</a>';
   switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
    case 0: $v='-'; $s1=$s3; $s3=sprintf('%02d',$nD); break; case 1: $v='.'; break;
    case 2: $v='/'; $s1=$s2; $s2=sprintf('%02d',$nD); break; case 3: $v='/'; break; case 4: $v='-'; break;
   }
   $sSDt=$s1.$v.$s2.$v.$s3; //Such-Link-Datum
   if(KAL_MLinkZiel==1&&$nTrms>1||KAL_MLinkZiel==0){ //Datumslink
    $sDtLnk1='<a class="kalM'.$sCss.'" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=liste&amp;kal_Woche='.$sWoc.'&amp;kal_1F1='.$sSDt.$sSes.$sQ.'">';
   }else{
    $sDtLnk1='<a class="kalM'.$sCss.'" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Woche='.$sWoc.'&amp;kal_Nummer='.$nTId.'&amp;kal_Intervall=%5B%5D'.$sSes.$sQ.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'">';
   }
   if(KAL_MTerminDetail){ //Details sollen gezeigt werden
    if(KAL_MTerminZahl>0&&KAL_MTerminZahl<$nTrms){ //mehr....
     if($nTrms>1) $sMehr="\n".'   <a class="kalMDet" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=liste&amp;kal_Woche='.$sWoc.'&amp;kal_1F1='.$sSDt.$sSes.$sQ.'">....</a>';
     else $sMehr="\n".'   <a class="kalMDet" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Woche='.$sWoc.'&amp;kal_Nummer='.$nTId.'&amp;kal_Intervall=%5B%5D'.$sSes.$sQ.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'">....</a>';
     $nTrms=KAL_MTerminZahl;
    }
    for($j=0;$j<$nTrms;$j++){ //ueber alle pro Tag
     $nTId=$aTag[$nD][$j]; $sDet=''; $sIcn=''; $bNichtZugesagt=(!($bMonatsZusage&&$bSes&&isset($aZusageTermine[$nTId])));
     $sAjaxLink=(KAL_VBoxMon?' onmouseover="startVBox('."'".$nTId."'".')" onmouseout="'.(KAL_VBoxAutoAus?"hideVBox('0')":'stopVBox()').'"':'');
     if($bMonatsInfo)   $sMInfo=' <a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info&amp;kal_Nummer='.$nTId.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxSendInfo).'" alt="'.fKalTx(KAL_TxSendInfo).'"></a>';
     if($bMonatsErinn)  $sMErin=' <a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern&amp;kal_Nummer='.$nTId.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"></a>';
     if($bMonatsNachr)  $sMNach=' <a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht&amp;kal_Nummer='.$nTId.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"></a>';
     if($bMonatsICal)   $sMICal=' <a href="'.(KAL_CalPopup ?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export&amp;kal_Nummer='.$nTId.$sSes.(KAL_CalPopup?'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"></a>';
     if($bMonatsZusagZ||$bMonatsZusagHG||$bMonatsZusagS){
      $nZusagAktZ=(isset($aZusageZahl[$nTId])?$aZusageZahl[$nTId]:'0'); $nZusagKapG=0; $nZusagFreZ='?';
      $nZusagKapZ=($nKapPos>0&&isset($aTrm[$nTId][$nKapPos])?$aTrm[$nTId][$nKapPos]:'-');
      if($k=strpos($nZusagKapZ,'(')){$nZusagKapG=(KAL_ZusageStatusSchwelle?(int)substr($nZusagKapZ,$k+1):0); $nZusagKapZ=(int)$nZusagKapZ;}
      if((int)$nZusagKapZ>0) $nZusagFreZ=max((int)$nZusagKapZ-$nZusagAktZ,0); // Kap vorhanden
      elseif(substr($nZusagKapZ,0,1)==='0') $nZusagFreZ=0;
      if($bMonatsZusagHG||$bMonatsZusagS){
       $bZsGrn=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusGlb:$nZusagKapG)||$nZusagFreZ==='?');
       $bZsGlb=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusRot:1));
      }
     }
     if($bMonatsZusagS) $sMZusS=' <img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.gif" title="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'" alt="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'">';
     if($bMonatsZusagHG){
      $sHgDat='background-image:url(grafik/block'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.jpg); background-size:100% 100%;';
      if(KAL_MonZusagHGBl=='Det'){$sHgDet=' style="'.$sHgDat.'"'; $sHgDat='';}
     }
     if($bMonatsZusage) $sMZusa=' <a href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'woche&amp;kal_Woche='.$sWoc:'zusagen').$sSes.'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$nTId?'':'Klick2Zusage='.$nTId.'&amp;kal_').'KlickZusage=':'Nummer=').$nTId.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"'.$sAjaxLink.'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_MonatZeigeZusage&&($bSes||KAL_GastMZeigeZusage))?'<a href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.'&amp;kal_Nummer='.$nTId.(KAL_ZusagePopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':'');
     if($bMonatsZusagZ){
      if((int)$nZusagKapZ>0){ // Kap vorhanden
       $sMZusZ=fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_MonZusagZMuster))));
      }elseif(KAL_MonZusagZErsatz) $sMZusZ=fKalTx(KAL_MonZusagZErsatz); elseif(KAL_MonZusagZZeigeLeer) $sMZusZ='&nbsp;'; else $sMZusZ='';
     }
     $bKat=false; // evt. Kategorie holen
     if(KAL_MonatsKateg&&$nKatPos>0) if(isset($aTrm[$nTId][$nKatPos])&&($sKat=$aTrm[$nTId][$nKatPos])) if(isset($kal_Kategorien[$sKat])&&($sKat=$kal_Kategorien[$sKat])) $bKat=true;

     if(!$bEigeneZeilen){ //Standardlayout
      if(KAL_MDetail1Fld>=0){
       $sDet=fKalZeigeMDet($aTrm[$nTId][KAL_MDetail1Fld],KAL_MDetail1Fld,$nTId);
       if(KAL_MDetail2Fld>=0){
        $sDet.=($sDet>''&&$aTrm[$nTId][KAL_MDetail2Fld]>''?KAL_MDetail1Trn:'').fKalZeigeMDet($aTrm[$nTId][KAL_MDetail2Fld],KAL_MDetail2Fld,$nTId);
        if(KAL_MDetail3Fld>=0){
         $sDet.=($aTrm[$nTId][KAL_MDetail3Fld]>''?KAL_MDetail2Trn:'').fKalZeigeMDet($aTrm[$nTId][KAL_MDetail3Fld],KAL_MDetail3Fld,$nTId);
         if(KAL_MDetail4Fld>=0){
          $sDet.=($aTrm[$nTId][KAL_MDetail4Fld]>''?KAL_MDetail3Trn:'').fKalZeigeMDet($aTrm[$nTId][KAL_MDetail4Fld],KAL_MDetail4Fld,$nTId);
      }}}}
      $sTrm.="\n".'     <div class="kalMDet'.($sTrm?' kalMTrn':'').($bKat?' kalTrmKat'.$sKat:'').'"'.$sHgDet.'>'."\n".'      <a class="kalMDet" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Woche='.$sWoc.'&amp;kal_Nummer='.$nTId.'&amp;kal_Intervall=%5B%5D'.$sSes.$sQ.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'"'.$sAjaxLink.'>'.$sDet.'</a>';
      if($bMonatsInfo) $sIcn.=$sMInfo; if($bMonatsErinn) $sIcn.=$sMErin; if($bMonatsNachr) $sIcn.=$sMNach; if($bMonatsICal) $sIcn.=$sMICal;
      if($bMonatsZusagS) $sIcn.=$sMZusS; if($bMonatsZusage) $sIcn.=$sMZusa; if($bMonatsZusagZ) $sIcn=$sMZusZ.($sIcn?"</div>\n".'      <div class="kalMIcon">':'').trim($sIcn);
      if($sIcn) $sTrm.="\n".'      <div class="kalMIcon">'.trim($sIcn).'</div>';
      $sTrm.="\n     </div>";
     }else{ //eigenes Layout
      $sZl=$sEigeneZeile;
      if(strpos($sZl,'href="detail"')>0){
       $s='href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Woche='.$sWoc.'&amp;kal_Nummer='.$nTId.'&amp;kal_Intervall=%5B%5D'.$sSes.$sQ.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'"'.$sAjaxLink;
       $sZl=str_replace('href="detail"',$s,$sZl);
      }
      for($k=1;$k<$nFelder;$k++){
       $sFN=str_replace('`,',';',$kal_FeldName[$k]);
       if($p=strpos($sZl,'{'.$sFN.'}')) $sZl=str_replace('{'.$sFN.'}',fKalZeigeMDet($aTrm[$nTId][$k],$k,$nTId),$sZl);
      }
      if(strpos($sZl,'{Nummer}')>0) $sZl=str_replace('{Nummer}',$nTId,$sZl);
      if(strpos($sZl,'{Info}')>0) $sZl=str_replace('{Info}',($bMonatsInfo?$sMInfo:''),$sZl);
      if(strpos($sZl,'{Erinnern}')>0) $sZl=str_replace('{Erinnern}',($bMonatsErinn?$sMErin:''),$sZl);
      if(strpos($sZl,'{Nachricht}')>0) $sZl=str_replace('{Nachricht}',($bMonatsNachr?$sMNach:''),$sZl);
      if(strpos($sZl,'{Export}')>0) $sZl=str_replace('{Export}',($bMonatsICal?$sMICal:''),$sZl);
      if(strpos($sZl,'{Zusagen}')>0) $sZl=str_replace('{Zusagen}',($bMonatsZusage?$sMZusa:''),$sZl);
      if(strpos($sZl,'{ZusageZahl}')>0) $sZl=str_replace('{ZusageZahl}',($bMonatsZusagZ?$sMZusZ:''),$sZl);
      if(strpos($sZl,'{ZusageStatus}')>0) $sZl=str_replace('{ZusageStatus}',($bMonatsZusagS?$sMZusS:''),$sZl);
      $sTrm.=$sZl;
     }
    }//ueber alle pro Tag
    $sTrm.=$sMehr;
   }else{//keine Details, nur Anzahl
    $sTrm=$nTrms.' '.fKalTx($nTrms!=1?KAL_TxTermine:KAL_TxTermin);
    if($nTrms>0){
     if($nTrms>1) $sTrm='<a class="kalMDet" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=liste&amp;kal_1F1='.$sSDt.$sSes.$sQ.'">'.$sTrm.'</a>';
     else $sTrm='<a class="kalMDet" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Woche='.$sWoc.'&amp;kal_Nummer='.$nTId.'&amp;kal_Intervall=%5B%5D'.$sSes.$sQ.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'">'.$sTrm.'</a>';
   }}
  }elseif(KAL_MLinkLeerNeu){ //bei leerem Datum
   $sDtLnk1='<a class="kalM'.$sCss.'" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=eingabe'.$sSes.$sQ.'">'; $sDtLnk2='</a>';
  }
  //if(KAL_WZellenHoehe>4) $sHgDat.='height:'.floor(1.4*KAL_WZellenHoehe).'em;'; // nicht responsiv
  $X.="\n".'    <div class="kalTbSpT kalTbSpt kalTbWS'.$i.'"'.($sHgDat?' style="'.$sHgDat.'"':'').'>';
  $X.="\n".'     <div class="kalM'.$sCss.'">'.$sDtLnk1.$sDt.$sDtLnk2.'</div>'.$sTrm.'<div class="kalMClr"></div>';
  $X.="\n".'    </div>';
 }
 $X.="\n".'   </div>';
 $X.="\n".'  </div>';
 $X.="\n".' </div>';
 $X.="\n".'</div>'; // Table

 //Navigator unter der Tabelle
 if(KAL_MSuchFilter>6&&KAL_MSuchFilter<=9) $X.="\n".$sSuch;
 if(KAL_MNaviUnten) $X.="\n".$sNavig;

 $sAjaxURL=KAL_Url; $bWww=(strtolower(substr(fKalHost(),0,4))=='www.');
 if($bWww&&!strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://','://www.',$sAjaxURL);
 elseif(!$bWww&&strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://www.','://',$sAjaxURL);

 return (KAL_VBoxMon?"\n<script>
 IE=document.all&&!window.opera; DOM=document.getElementById&&!IE; var ieBody=null; //Browserweiche
 var xmlHttpObject=null; var mouseX=0; var mouseY=0; var oTimer=null;
 function initAjaxVBox(){
  if(IE) ieBody=(window.document.compatMode=='CSS1Compat')?window.document.documentElement:window.document.body||null;
  if(typeof XMLHttpRequest!='undefined') xmlHttpObject=new XMLHttpRequest();
  if(!xmlHttpObject){
   try{xmlHttpObject=new ActiveXObject('Msxml2.XMLHTTP');}
   catch(e){
    try{xmlHttpObject=new ActiveXObject('Microsoft.XMLHTTP');}
    catch(e){xmlHttpObject=null;}
  }}
  if(document.addEventListener) document.addEventListener('mousemove',getMousePos,false);
  else if(window.attachEvent) document.attachEvent('onmousemove',getMousePos);
  else document.onmousemove=getMousePos;".(KAL_VBoxAutoAus?'':"\n  document.onclick=hideVBox;")."
 }
 function getMousePos(ev){
  mouseX=(IE)?window.event.clientX:ev.pageX; mouseY=(IE)?(window.event.clientY+ieBody.scrollTop):ev.pageY;
 }
 function showVBox(){
  if(xmlHttpObject.readyState==4){
   var oBox=document.getElementById('kalVBox'); oBox.innerHTML=xmlHttpObject.responseText;
   var nTop=((IE)?ieBody.scrollTop:window.pageYOffset); var nBott=nTop+((IE)?ieBody.clientHeight:window.innerHeight);
   oBox.style.display='block';
   if(parseInt(oBox.style.top)+oBox.offsetHeight>nBott) oBox.style.top=Math.max(nBott+".KAL_VBoxVOffset."-oBox.offsetHeight,nTop+1)+'px';
  }
 }
 function prepareVBox(sPara){
  if(oTimer) window.clearTimeout(oTimer); oTimer=null;
  var nWidth=((IE)?ieBody.clientWidth:window.innerWidth);
  var nTop=((IE)?ieBody.scrollTop:window.pageYOffset);
  var nBott=nTop+((IE)?ieBody.clientHeight:window.innerHeight);
  var nBoxX=mouseX+10; if(nBoxX>nWidth-".KAL_VBoxWidth.") nBoxX=mouseX-(".KAL_VBoxWidth."+((IE)?20:40)); if(nBoxX<0) nBoxX=mouseX+10;
  var nBoxY=Math.max(Math.min(mouseY-".ceil(KAL_VBoxHeight/2).",nBott-(".KAL_VBoxHeight."+30)),nTop+1);
  xmlHttpObject.open('get','".$sAjaxURL.'kalTerminBox.php?'.(KAL_Query?substr(str_replace('&amp;','&',KAL_Query),1).'&':'').''."kal_Termin='+sPara+'&kal_Monat=".$sMon."&kal_Woche=".$sWoc.str_replace("&amp;","&",$sSes.$sQ)."');
  xmlHttpObject.onreadystatechange=showVBox;
  xmlHttpObject.send(null);
  var oBox=document.getElementById('kalVBox'); oBox.style.left=(nBoxX+".KAL_VBoxHOffset.")+'px'; oBox.style.top=(nBoxY+".KAL_VBoxVOffset.")+'px';
 }
 function startVBox(sPara){
  if(oTimer) window.clearTimeout(oTimer); oTimer=window.setTimeout('prepareVBox(\"'+sPara+'\")',".KAL_VBoxWarten.");
 }
 function stopVBox(){
  if(oTimer) window.clearTimeout(oTimer); oTimer=null;
 }
 function hideVBox(ev){
  if(oTimer) window.clearTimeout(oTimer); oTimer=null;
  var oBox=document.getElementById('kalVBox'); oBox.style.display='none';
  oBox.style.left='0px'; oBox.style.top='0px'; oBox.innerHTML='...nicht mehr aktuell';
 }
</script>
":'').$X.(KAL_VBoxMon?'

<div id="kalVBox" style="min-height:'.KAL_VBoxHeight.'px;width:'.KAL_VBoxWidth.'px;">...wird geladen</div>

<script>initAjaxVBox();</script>':'');
}

function fKalSuchFilter($s,$sWoc,$sSes){ //Schnellsuchfilter zeichnen
$sDir=''; if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
if(KAL_MSuchFilter==3||KAL_MSuchFilter==6||KAL_MSuchFilter==9) $sDir='R'; elseif(KAL_MSuchFilter==1||KAL_MSuchFilter==4||KAL_MSuchFilter==7) $sDir='L';
return '
<div class="kalFilt">
<div class="kalSFlt'.$sDir.'">
<form class="kalFilt" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'."\n".'<input type="hidden" name="kal_Aktion" value="woche"><input type="hidden" name="kal_Woche" value="'.$sWoc.'">'.rtrim("\n".KAL_Hidden).rtrim("\n".($sSes!=''?'<input type="hidden" name="kal_Session" value="'.substr($sSes,17,12).'">'.(strpos($sSes,'Z')?'<input type="hidden" name="kal_Zentrum" value="1">':''):'')).'
 <div class="kalNoBr">'.fKalTx(KAL_TxSuchen).' <input class="kalSFlt" name="kal_Such" value="'.fKalTx($s).'"> <input type="submit" class="kalKnopf" value="" title="'.fKalTx(KAL_TxSuchen).'"></div>
</form>
</div>
<div class="kalClear"></div>
</div>
';
}

function fKalMNavigator($nJr,$nWo,$sQry,$sSes){ //Navigator zum Blaettern
 $sL=KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=woche'.$sSes.$sQry.'&amp;kal_Woche=';
 if($nWo==1||$nWo>=52){
  $nZt=@mktime(16,0,0,1,1,$nJr); $nWt1=date('N',$nZt); if($nWt1>4) $nZt+=(8-$nWt1)*86400; elseif($nWt1>1) $nZt+=(1-$nWt1)*86400; // Beginn 1. Woche
  if($nWo>1) $nZt+=($nWo-1)*604800; // 86400*7 Wochenbeginn
 }
 $X ="\n".'<div class="kalNavD">';
 if($nW=$nWo-1) $nJ=$nJr; else{$nW=(int)date('W',$nZt-604800); $nJ=$nJr-1;}
 $X.="\n".' <div class="kalNavR"><a class="kalDetR" href="'.$sL.sprintf('%04d-%02d',$nJ,$nW).'" title="'.fKalTx(KAL_TxZumAnfang).'"></a></div>';
 if($nWo<52){$nW=$nWo+1; $nJ=$nJr;}else{$nW=(int)date('W',$nZt+604800); if($nW==1) $nJ=$nJr+1;}
 $X.="\n".' <div class="kalNavV"><a class="kalDetV" href="'.$sL.sprintf('%04d-%02d',$nJ,$nW).'" title="'.fKalTx(KAL_TxZumEnde).'"></a></div>';
 $X.="\n".'...';
 $X.="\n".'</div>';
 return $X;
}

function fKalNormMDat($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fKalDtCodeM($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
}

function fKalDtTempl($s2,$s3){
 global $aMonate; $s1='##'; $v='';
 switch(KAL_MDatumsformat){ //0:dd. 1:dd.mm. 2:dd.mmm. 3:dd.mm.yy 4:mm/dd/yy 5:dd/mm/yy 6:dd-mm-yy 7:yy-mm-dd
  case 0: $s1.='.'; $s2=''; $s3=''; break;
  case 1: $s3=''; $v='.'; break;
  case 2: $s3=''; $s1.='. '; $s2=fKalTx($aMonate[(int)$s2]); break;
  case 3: $v='.'; break;
  case 4: $v='/'; $s=$s1; $s1=$s2; $s2=$s; break;
  case 5: $v='/'; break;
  case 6: $v='-'; break;
  case 7: $v='-'; $s=$s1; $s1=$s3; $s3=$s; break;
 }
 return $s1.$v.$s2.$v.$s3;
}

//Text mit BB-Code einkuerzen
function fKalKurzMemo($s,$nL=80){
 if(strlen($s)>$nL){
  $v='#'.substr($s,0,$nL);
  if($p=strrpos($v,'[')){ //BB-Code enthalten
   if(strrpos($v,']')<$p) $v=substr($v,0,$p); //angeschnittenen Codes streichen
   $p=0; $aTg=array();
   while($p=strpos($v,'[',++$p)){ //Codes erkennen
    if($q=strpos($v,']',++$p)){$t=substr($v,$p,$q-$p); $aTg[]=(($q=strpos($t,'='))?substr($t,0,$q):$t);}
   }
   $n=count($aTg)-1;
   for($i=$n;$i>=0;$i--){ //Codes durchsuchen
    $s=$aTg[$i];
    if(substr($s,0,1)!='/'){
     $bFnd=false;
     for($j=$i;$j<=$n;$j++) if($aTg[$j]=='/'.$s){$aTg[$j]='#'; $bFnd=true; break;}
     if(!$bFnd) $v.='[/'.$s.']'; //fehlenden Code anhaengen
  }}}
  return substr($v,1).'...';
 }else return $s;
}

function fKalZeigeMDet($s,$k,$sId=0){
 global $kal_FeldName, $kal_FeldType, $kal_WochenTag, $kal_Symbole, $aMonate, $sSes, $sMon, $sQ, $aNutzer, $nNutzerZahl, $ksNutzerListFeld, $bEinKlickZusage, $bNichtZugesagt, $nEinKlickTId;
 $t=$kal_FeldType[$k]; if(!KAL_SQL) $s=str_replace('`,',';',$s);
 if(strlen($s)>0){
  switch ($t){
   case 't': case 'g': case 'm': if(KAL_MDetailKuerzen==0) $s=fKalBB(fKalDt($s)); else $s=fKalBB(fKalDt(fKalKurzMemo($s,KAL_MDetailKuerzen))); break;
   case 'a': case 'k': if(KAL_MDetailKuerzen==0) $s=fKalDt($s); else $s=fKalDt(fKalKurzMemo($s,KAL_MDetailKuerzen)); break; //Aufzaehlung/Kategorie/Postleitzahl
   case 'd': case '@': $w=trim(substr($s,11)); // Datum
    $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2)); if(KAL_MonatMLang>0&&$t=='d') $s2=fKalTx($aMonate[(int)$s2]);
    switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
     case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
     case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
    }
    $s=$s1.$v.$s2.$v.$s3;
    if($t=='d'){
     if(KAL_MonatMLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
     if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
    }elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
    break;
   case 'z': case 'o': break; //Uhrzeit/PLZ
   case 'w': //Waehrung
    if(((float)$s)!=0||!KAL_PreisLeer){
     $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung;
    }else $s='&nbsp;';
    break;
   case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
    if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein);
    break;
   case '#': if(KAL_ZusageSystem) $s=strtoupper(substr($s,0,1)); else $s=''; //Zusage
    if($s=='J'||$s=='Y') $s='<a href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'woche&amp;kal_Woche='.$sWoc:'zusagen').$sSes.'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$sId?'':'Klick2Zusage='.$sId.'&amp;kal_').'KlickZusage=':'Nummer=').$sId.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_ListeZeigeZusage&&($sSes||KAL_GastLZeigeZusage))?'<a href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.'&amp;kal_Nummer='.$sId.(KAL_ZusagePopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':''); else $s='&nbsp;';
    break;
   case 'n': case '1': case '2': case '3': case 'r': //Zahl
    if(((float)$s)!=0||!KAL_ZahlLeer){
     if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
    }else $s='&nbsp;';
    break;
   case 'l': //Link
    $aL=explode('||',$s); $s='';
    foreach($aL as $w){
     $aI=explode('|',$w); $w=$aI[0]; $v=fKalDt(isset($aI[1])?$aI[1]:$w); $u=$v;
     $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">';
     $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.'</a>  ';
    }$s=substr($s,0,-2); break;
   case 'e': //EMail
    $s='<a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=kontakt'.$sSes.'&amp;kal_Nummer='.$sId.(KAL_MailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':$sQ).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'"></a>';
    break;
   case 'u': //Benutzer
    if($nUId=(int)$s){
     $s=KAL_TxAutorUnbekannt;
     for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nUId){
      if(!$s=$aNutzer[$n][$ksNutzerListFeld]) $s=KAL_TxAutorUnbekannt;
      break;
    }}else $s=KAL_TxAutor0000;
    break;
   case 's': $w=$s; //Symbol
    $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
    $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.fKalDt($w).'" alt="'.fKalDt($w).'">';
    break;
   case 'b': //Bild
    $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.abs($sId).'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
    $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
    if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
    $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
    $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'"></div>';
    break;
   case 'f': //Datei
    $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalDt(strtoupper($w).'-'.KAL_TxDatei);
    if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
    $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
    $s='<a href="'.KAL_Url.KAL_Bilder.abs($sId).'~'.$s.'" target="_blank">'.$v.'</a>';
    break;
   case 'x': $s='Kartensymbol'; break; //StreetMap
   case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
  }
  if($kal_FeldName[$k]=='KAPAZITAET') if($s>'0') $s=(int)$s;
 }
 return $s;
}

function fKalInitWoc(){
 if($s=(isset($_GET['kal_Woche'])?$_GET['kal_Woche']:(isset($_POST['kal_Woche'])?$_POST['kal_Woche']:''))){
  $nJhr=(int)substr($s,0,4); $nWoc=(int)substr($s,5,2);
 }else{$nJhr=0; $nWoc=0;}
 if($nWoc==0||$nJhr<100||$nWoc>53){$nWoc=(int)date('W'); $nJhr=(int)date('Y');}
 $s=sprintf('%04d-%02d',$nJhr,$nWoc); $_GET['kal_Woche']=$s; $_POST['kal_Woche']=$s;
 //$aWochen=(KAL_MonatMLang>0?explode(';',';'.(KAL_MonatMLang==2?KAL_TxLMonate:KAL_TxKMonate)):array('-','1','2','3','4','5','6','7','8','9','10','11','12'));
 if(strlen(KAL_TxWMetaKey)>0) define('KAL_MetaKey',str_replace('#W',$nWoc,str_replace('#Y',$nJhr,KAL_TxWMetaKey)));
 if(strlen(KAL_TxWMetaDes)>0) define('KAL_MetaDes',str_replace('#W',$nWoc,str_replace('#Y',$nJhr,KAL_TxWMetaDes)));
 if(strlen(KAL_TxWMetaTit)>0) define('KAL_MetaTit',str_replace('#W',$nWoc,str_replace('#Y',$nJhr,KAL_TxWMetaTit)));
 return $s; //aktuelle Woche
}
?>