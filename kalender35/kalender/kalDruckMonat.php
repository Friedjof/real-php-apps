<?php
function fKalSeite(){ //Seiteninhalt
 global $kal_FeldName, $kal_FeldType, $kal_LinkFeld, $kal_Symbole, $kal_WochenTag,
  $aMonate, $sSes, $sMon, $aNutzer, $nNutzerZahl, $nId, $ksNutzerListFeld;

 $X=''; $Et=''; $Es='Fehl'; $Em=''; $nTrms=0;

 //StartMonat
 $nHtT=(int)date('j'); $nHtM=(int)date('n'); $nHtJ=(int)date('Y'); $nJhr=0; $nMon=0; //heute
 if($s=(isset($_GET['kal_Monat'])?$_GET['kal_Monat']:(isset($_POST['kal_Monat'])?$_POST['kal_Monat']:''))){$nJhr=(int)substr($s,0,4); $nMon=(int)substr($s,5,2);}
 if($nMon==0||$nJhr<100||$nMon>12){$nMon=$nHtM; $nJhr=$nHtJ;} $sMon=sprintf('%04d-%02d',$nJhr,$nMon); //aktueller Monat
 $nZt1=@mktime(16,0,0,$nMon,1,$nJhr); $sDatA=sprintf('%04d-%02d-01',$nJhr,$nMon); //erster Tag
 $nTagL=(int)date('t',$nZt1); $sDatE=sprintf('%04d-%02d-%02d',$nJhr,$nMon,$nTagL); //letzter Tag

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 if(!$sSes=KAL_Session) if(defined('KAL_NeuSession')) $sSes=KAL_NeuSession; $bSes=false; //Session pruefen
 if($sSes=substr($sSes,17,12)){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Et=KAL_TxSessionUngueltig;
     break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
  }}else $Et=KAL_TxSessionZeit;
 } $sId='';

 $s=(isset($_GET['kal_Such'])?fKalRq($_GET['kal_Such']):(isset($_POST['kal_Such'])?fKalRq($_POST['kal_Such']):'')); $sSuch='';
 if(strlen($s)>0){ //Schnellsuche
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sSuch=$s; $Em.=', '.htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1');
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
    if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{$a1Filt[$i]=fKalNormDatD($s); $a2Filt[$i]='';} $Em.=', '.$sFN;
   }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&$bSes?'':'N'); $a2Filt[$i]='';} //versteckt
   if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRq($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRq($_POST['kal_'.$i.'F2']); else $s='';
   if(strlen($s)){ //zweites Suchfeld ausgefuellt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
    elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
    if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormDatD($s); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
   }
   if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRq($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRq($_POST['kal_'.$i.'F3']); else $s='';
   if(strlen($s)){ //drittes Suchfeld ausgefuellt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
 }}}

 $bAnzeigen=$nVerstecktFeld==0||(KAL_NVerstecktSehen&&$bSes); //alle anzeigen oder einzeln pruefen
 if(KAL_MonOhneAltes&&empty($a1Filt[1])){
  $bOhneAltes=true; $sAltDat=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); if($sAltDat>$sDatA) $sDatA=$sAltDat;
 }else $bOhneAltes=false;

 //Daten holen
 $aTrm=array(); for($i=1;$i<=$nTagL;$i++) $aTag[$i]=array();
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
      $aTrm[$a[0]]=$a; $k=(int)substr($sAnfangDat,8,2); $aTag[$k][]=$a[0]; $nTrms++;//eintragen
      if($sAnfangDat!=$sEndeDat){ //Mehrtagstermin
       $w=(int)substr($sEndeDat,8,2); for($j=$k+1;$j<=$w;$j++) $aTag[$j][]=$a[0];
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
 $bMonatsZusagZ=(KAL_ZusageSystem&&(KAL_GastMoZusagZ||$bSes)?KAL_MonatsZusagZ:false);

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
 if(KAL_ZusageSystem&&($bMonatsZusagZ&&strpos('x'.KAL_MonZusagZMuster,'#Z')>0||strpos('x'.KAL_MonZusagZMuster,'#R')>0)||$bSes&&($bMonatsZusagZ||array_search('#',$kal_FeldType))){
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

 $aMonate=(KAL_MonatMLang>0?explode(';',';'.(KAL_MonatMLang==2?KAL_TxLMonate:KAL_TxKMonate)):array('-','1','2','3','4','5','6','7','8','9','10','11','12'));

 // Meldung ausgeben
 if(empty($Et)){
  if(substr($Em,0,1)==',') $Et=str_replace('#S',substr($Em,1),KAL_TxMonSuch); else $Et=KAL_TxMonGsmt;
  $Et=str_replace('#M',$aMonate[$nMon],str_replace('#Y',$nJhr,str_replace('#N',$nTrms,$Et))); $Es='Meld';
 }
 $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

 //eigene Layoutzeile pruefen
 if(($bEigeneZeilen=KAL_EigeneMDruckZelle)&&file_exists(KAL_Pfad.'kalDruckMonatsZelle.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'kalDruckMonatsZelle.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }

 // Tabellenkopf
 $X.="\n\n".'<div class="kalDrTab">';
 $X.="\n".' <div class="kalTbZl'.(KAL_DruckMFarbig?'0 kalTbZlDr':'Dr').'">';
 if(KAL_MonWochNr) $X.="\n".'  <div class="kalTbDr">'.(KAL_MonTxNr?fKalTx(KAL_MonTxNr):'&nbsp;').'</div>';
 $X.="\n".'  <div class="kalTbDr">'.fKalTx($kal_WochenTag[1]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[2]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[3]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[4]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[5]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[6]).'</div><div class="kalTbDr">'.fKalTx($kal_WochenTag[0]).'</div>';
 $X.="\n".' </div>';
 $X.="\n".' <div class="kalTbZl'.(KAL_DruckMFarbig?'M':'Dr').'">';
 $nWSp=date('w',$nZt1); if($nWSp<=0) $nWSp=7; $nZt0=$nZt1-86400*--$nWSp; //Anfangsspalte
 if(KAL_MonWochNr) $X.="\n".'  <div class="kalTb'.(KAL_DruckMFarbig?'SpW':'Dr" style="vertical-align:top').'">'.str_replace('#W',date('W',$nZt0),KAL_TxMWochNr).'</div>';
 if($nWSp>0){ //monatsfremder Anfang
  $s2=sprintf('%02d',$nMon-1); $s3=$nJhr; if($s2=='00'){$s2='12'; $s3--;} $sDtTpl=fKalDtTempl($s2,$s3);
  $j=date('j',$nZt1-86400*$nWSp); for($i=1;$i<=$nWSp;$i++) $X.="\n".'  <div class="kalTb'.(KAL_DruckMFarbig?'SpT kalGrey':'Dr').' kalTbMDr">'.(KAL_MonFremd?str_replace('##',sprintf('%02d',$j++),$sDtTpl):'.').'</div>';
 }
 $s2=sprintf('%02d',$nMon); $s3=$nJhr; $sDtTpl=fKalDtTempl($s2,$s3); //Datumstemplate vorbereiten
 for($i=1;$i<=$nTagL;$i++){ //ueber alle Tage
  if(++$nWSp>7){ //neue Zeile
   $X.="\n".' </div>';
   $X.="\n".' <div class="kalTbZl'.(KAL_DruckMFarbig?'M':'Dr').'">'; $nWSp=1; $nZt0+=604800; // 7*86400
   if(KAL_MonWochNr) $X.="\n".'  <div class="kalTb'.(KAL_DruckMFarbig?'SpW':'Dr" style="vertical-align:top').'">'.str_replace('#W',date('W',$nZt0),KAL_TxMWochNr).'</div>';
  }
  $sTrm=''; $sDtLnk1=''; $sDtLnk2=''; $sHgDet=''; $sHgDat=''; $sCss='Dat'; if($i==$nHtT&&$nMon==$nHtM&&$nJhr==$nHtJ) $sCss='Hte'; //heute?
  $sDt=str_replace('##',sprintf('%02d',$i),$sDtTpl); //anzuzeigendes Datum
  if($aTag[$i]){ //Datum ist besetzt
   $nTId=$aTag[$i][0]; $nTrms=count($aTag[$i]); $sMehr='';

   if(KAL_MTerminDetail){ //Details sollen gezeigt werden
    if(KAL_MTerminZahl>0&&KAL_MTerminZahl<$nTrms){ //mehr....
     $sMehr="\n   ....";
     $nTrms=KAL_MTerminZahl;
    }
    for($j=0;$j<$nTrms;$j++){ //ueber alle anzuzeigenden Termine
     $nTId=$aTag[$i][$j]; $sDet=''; $sIcn='';
     if($bMonatsZusagZ){
      if($nZusagKapZ=($nKapPos>0?(isset($aTrm[$nTId][$nKapPos])?(int)$aTrm[$nTId][$nKapPos]:0):0)){
       $nZusagAktZ=(isset($aZusageZahl[$nTId])?$aZusageZahl[$nTId]:'0');
       $sMZusZ=fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',max($nZusagKapZ-$nZusagAktZ,0),KAL_MonZusagZMuster))));
      }elseif(KAL_MonZusagZErsatz) $sMZusZ=fKalTx(KAL_MonZusagZErsatz); elseif(KAL_MonZusagZZeigeLeer) $sMZusZ='&nbsp;'; else $sMZusZ='';
     }
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
      $sTrm.="\n".'   <div class="kalNorm" style="padding:2px">'.$sDet;
      if($bMonatsZusagZ) $sIcn =$sMZusZ.($sIcn?"</div>\n   <div>":'').$sIcn;
      $sTrm.=($sIcn?"\n".'   <div class="kalNorm">'.trim($sIcn).'</div>':'')."\n   </div>";
     }else{ //eigenes Layout
      $sZl=$sEigeneZeile;
      for($k=1;$k<$nFelder;$k++){
       $sFN=str_replace('`,',';',$kal_FeldName[$k]);
       if($p=strpos($sZl,'{'.$sFN.'}')) $sZl=str_replace('{'.$sFN.'}',fKalZeigeMDet($aTrm[$nTId][$k],$k,$nTId),$sZl);
      }
      if(strpos($sZl,'{Nummer}')>0) $sZl=str_replace('{Nummer}',$nTId,$sZl);
      if(strpos($sZl,'{Info}')>0) $sZl=str_replace('{Info}','',$sZl);
      if(strpos($sZl,'{Erinnern}')>0) $sZl=str_replace('{Erinnern}','',$sZl);
      if(strpos($sZl,'{Nachricht}')>0) $sZl=str_replace('{Nachricht}','',$sZl);
      if(strpos($sZl,'{Export}')>0) $sZl=str_replace('{Export}','',$sZl);
      if(strpos($sZl,'{Zusagen}')>0) $sZl=str_replace('{Zusagen}','',$sZl);
      if(strpos($sZl,'{ZusageZahl}')>0) $sZl=str_replace('{ZusageZahl}',($bMonatsZusagZ?$sMZusZ:''),$sZl);
      $sTrm.=$sZl;
     }
    }//ueber alle
    $sTrm.=$sMehr;
   }else{//keine Details, nur Anzahl
    $sTrm=$nTrms.' '.fKalTx(KAL_TxTermine);
   }
  }

  $X.="\n".'  <div class="kalTb'.(KAL_DruckMFarbig?'SpT':'Dr').' kalTbMDr"'.($sHgDat?' style="'.$sHgDat.'"':'').'>'; // ToDo Höhenparameter KAL_MZellenHoehe in der CSS-Datei bei kalTbMDr berücksichtigen
  $X.="\n".'   <div class="kalM'.$sCss.'">'.$sDt.'</div>'.$sTrm;
  $X.="\n".'  </div>';
 }
 $s2=sprintf('%02d',$nMon+1); $s3=$nJhr; if($s2=='13'){$s2='01'; $s3--;} $sDtTpl=fKalDtTempl($s2,$s3); //monatsfremdes Ende
 $j=1; for($i=$nWSp;$i<7;$i++) $X.="\n".'    <div class="kalTb'.(KAL_DruckMFarbig?'SpT kalGrey':'Dr').' kalTbMDr">'.(KAL_MonFremd?str_replace('##',sprintf('%02d',$j++),$sDtTpl):'.').'</div>'; //monatsfremdes Ende
 $X.="\n".' </div>';
 $X.="\n".'</div>'; // Table

 return $X;
}

function fKalNormDatD($w){ //Suchdatum normieren
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
 global $kal_FeldName, $kal_FeldType, $kal_WochenTag, $kal_Symbole, $aMonate, $sSes, $sMon, $aNutzer, $nNutzerZahl, $nId, $ksNutzerListFeld;
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
     $s.=$v;
    }$s=substr($s,0,-2); break;
   case 'e': //eMail
    if(KAL_DruckLMailOffen){if(!KAL_SQL) $s=fKalDeCode($s);}
    else $s='<img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'">';
    break;
   case 'u': //Benutzer
    if($nId=(int)$s){
     $s=KAL_TxAutorUnbekannt;
     for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
      if(!$s=$aNutzer[$n][$ksNutzerListFeld]) $s=KAL_TxAutorUnbekannt; $s=fKalDt($s);
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
    $s=$v;
    break;
   case 'x': $s='Kartensymbol'; break; //StreetMap
   case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
  }
  if($kal_FeldName[$k]=='KAPAZITAET') if($s>'0') $s=(int)$s;
 }
 return $s;
}
?>