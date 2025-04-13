<?php
function fKalSeite(){ //Seiteninhalt
 global $kal_FeldName, $kal_FeldType, $kal_ListenFeld, $kal_SortierFeld, $kal_LinkFeld, $kal_SpaltenStil,
  $kal_Symbole, $kal_WochenTag, $aKalDaten, $aKalSpalten, $oKalDbO;

 $DbO=$oKalDbO; $sSes=''; if(KAL_SessionOK) if(!$sSes=KAL_Session) $sSes=KAL_NeuSession;

 //Sortierung und Startposition
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_ListenIndex);
 $sRueckw=(isset($_GET['kal_Rueck'])?fKalRq1($_GET['kal_Rueck']):'');
 $nStart=(isset($_GET['kal_Start'])?(int)$_GET['kal_Start']:1);

 //Query_Strings fuer Links vorbereiten
 $X=''; $sQ=''; $Et=''; $Et2=''; $Es='Fehl'; if($nIndex!=KAL_ListenIndex) $sQ.='&amp;kal_Index='.$nIndex; //1-Index
 if($sRueckw=='1'&&($nIndex!=1||!KAL_Rueckwaerts&&KAL_IFilter!='@'||!KAL_ArchivRueckwaerts&&KAL_IFilter=='@')) $sQ.='&amp;kal_Rueck=1'; //2-Rueck
 elseif($sRueckw==='0'&&$nIndex==1&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')) $sQ.='&amp;kal_Rueck=0';
 $sQ.=(isset($_GET['kal_Monat'])&&strlen($_GET['kal_Monat'])==7?'&amp;kal_Monat='.$_GET['kal_Monat']:'').(isset($_GET['kal_Woche'])&&strlen($_GET['kal_Woche'])==7?'&amp;kal_Woche='.$_GET['kal_Woche']:'').KAL_SuchParam; //3-Suchparameter

 //Zusatzspalten ermitteln
 $ksListenInfo=(KAL_GastLInfo||KAL_SessionOK?KAL_ListenInfo:-1);
 $ksListenAendern=(KAL_GastLAendern||KAL_SessionOK?KAL_ListenAendern:-1);
 $ksListenKopieren=(KAL_GastLKopieren||KAL_SessionOK?KAL_ListenKopieren:-1);
 $ksListenErinn=(KAL_GastLErinn||KAL_SessionOK?KAL_ListenErinn:-1);
 $ksListenBenachr=(KAL_GastLBenachr||KAL_SessionOK?KAL_ListenBenachr:-1);
 $ksListenZusage=(KAL_ZusageSystem&&(KAL_GastLZusage||KAL_SessionOK)?KAL_ListenZusage:-1);
 $ksListenZusagS=(KAL_ZusageSystem&&(KAL_GastLZusagS||KAL_SessionOK)?KAL_ListenZusagS:-1);
 $ksListenZusagZ=(KAL_ZusageSystem&&(KAL_GastLZusagZ||KAL_SessionOK)?KAL_ListenZusagZ:-1);
 $ksListenCal=(KAL_GastLCal||KAL_SessionOK?KAL_ListenCal:-1);
 if(isset($_GET['kal_Aendern'])){$sQ.='&amp;kal_Aendern=1'; $ksListenAendern=max(0,$ksListenAendern);}
 if(isset($_GET['kal_Kopieren'])){$sQ.='&amp;kal_Kopieren=1'; $ksListenKopieren=max(0,$ksListenKopieren);}

 //1-Klick-Zusagen
 $bEinKlickZusage=(KAL_EinKlickLZusage&&KAL_SessionOK); if($bEinKlickZusage) include KAL_Pfad.'kalZusage1Klick.php';

 //Detail als Popup-Fenster
 if((KAL_DetailPopup||KAL_MailPopup||(KAL_ZusagePopup&&KAL_ZusageSystem))&&!defined('KAL_KalWin')){$X="\n".'<script>function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'; define('KAL_KalWin',true);}
 if(($ksListenCal||KAL_LinkOExpt||KAL_LinkUExpt)&&KAL_CalPopup) $X.="\n".'<script>function ExpWin(sURL){expWin=window.open(sURL,"expwin","width='.KAL_CalPopupBreit.',height='.KAL_CalPopupHoch.',left='.KAL_CalPopupX.',top='.KAL_CalPopupY.',menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");expWin.focus();}</script>';
 //Navigation, Schnellsuche und Intervallfilter
 if(KAL_NaviOben>0||KAL_NaviUnten>0) $sNavig=fKalNavigator($nStart,KAL_Saetze,$sQ,$sSes);
 if(KAL_SuchFilter>0) $sSuch=fKalSuchFilter(isset($_GET['kal_Such'])?fKalRq($_GET['kal_Such']):(isset($_POST['kal_Such'])?fKalRq($_POST['kal_Such']):''),$sSes);
 if(KAL_SuchFilter==1) $X.="\n".$sSuch;
 if(KAL_NaviOben==1) $X.="\n".$sNavig;
 if(KAL_SuchFilter==2) $X.="\n".$sSuch;
 if((KAL_IFilter>'-'&&strlen((KAL_SessionOK?KAL_NIvWerte:KAL_IvWerte))>0)||KAL_SuchFilter==4||KAL_SuchFilter==3)
  $X.="\n".'<div class="kalFilt">'.(KAL_SuchFilter==4||KAL_SuchFilter==3?$sSuch:'').fKalIntervallFilter($sSes).(KAL_SuchFilter==4||KAL_SuchFilter==3?"\n".'<div class="kalClear"></div>':'')."\n</div>\n";

 if(KAL_SuchFilter==5) $X.="\n".$sSuch;
 if(KAL_NaviOben==2) $X.="\n".$sNavig;
 if(KAL_SuchFilter==6) $X.="\n".$sSuch;

 //Meldung ausgeben
 if(empty($Et)) $X.="\n".str_replace('#M',KAL_Saetze,str_replace('#N',count($aKalDaten),KAL_Meldung));
 else $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).$Et2.'</p>';

 if(KAL_SuchFilter==7) $X.="\n".$sSuch;
 if(KAL_NaviOben==3) $X.="\n".$sNavig;
 if(KAL_SuchFilter==8) $X.="\n".$sSuch;

 //eigene Layoutzeile pruefen
 if($bEigeneZeilen=KAL_EigeneZeilen&&file_exists(KAL_Pfad.'kalListenZeile.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'kalListenZeile.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#'); $nNutzerZahl=0;
 if(($n=array_search('u',$kal_FeldType))&&$kal_ListenFeld[$n]>0){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fKalDeCode($a[2]); $a[4]=fKalDeCode($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;} $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $ksNutzerListFeld=(KAL_NListeAnders&&KAL_SessionOK?KAL_NNutzerListFeld:KAL_NutzerListFeld);
 }

 //eventuell Zusagedaten holen
 $aZusageTermine=array(); $aZusageZahl=array();
 if(KAL_ZusageSystem&&($ksListenZusagZ>0&&(strpos('x'.KAL_TxListenZusagZMuster,'#Z')>0||strpos('x'.KAL_TxListenZusagZMuster,'#R')>0)||$ksListenZusagS>0)||KAL_SessionOK&&($ksListenZusage>0||(($n=array_search('#',$kal_FeldType))&&$kal_ListenFeld[$n]>0))){
  $nNId=(int)substr($sSes,17,4);
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
 }}}

 //eventuell Monate holen
 if(KAL_MonatLLang>0) $aMonate=explode(';',';'.(KAL_MonatLLang==2?KAL_TxLMonate:KAL_TxKMonate));
 if(KAL_MonatsTrenner>0){$sMonTrn=''; if(KAL_MonatsTrenner>1) $aMonTrn=explode(';',';'.KAL_TxLMonate);}
 $bMonTrn=false; $bWocTrn=false; $bTagTrn=false; $sJhrTrn=''; $sWocTrn=''; $sTagTrn='';

 //Daten ausgeben: $i-Index, $j-Spalte, $k-Feld
 $a=array(); $nSpalten=count($aKalSpalten); $kal_FeldName[0]=KAL_TxNr; $bMitID=$kal_ListenFeld[0]>0; $aSpTitle=array();
 $X.="\n\n".'<div class="kalTabl">'; //Tabelle

 //Kopfzeile ausgeben
 if(!$bEigeneZeilen){ //Standardlayout
  $X.="\n".' <div class="kalTbZl0">'; //Zeile
  if($ksListenAendern==0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenAendTitel?fKalTx(KAL_ListenAendTitel):'&nbsp;').'</div>';
  if($ksListenKopieren==0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenKopieTitel?fKalTx(KAL_ListenKopieTitel):'&nbsp;').'</div>';
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){
   if($ksListenInfo==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenInfTitel?fKalTx(KAL_ListenInfTitel):'&nbsp;').'</div>';
   if($ksListenAendern==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenAendTitel?fKalTx(KAL_ListenAendTitel):'&nbsp;').'</div>';
   if($ksListenKopieren==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenKopieTitel?fKalTx(KAL_ListenKopieTitel):'&nbsp;').'</div>';
   if($ksListenErinn==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenErinTitel?fKalTx(KAL_ListenErinTitel):'&nbsp;').'</div>';
   if($ksListenBenachr==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenBenachTitel?fKalTx(KAL_ListenBenachTitel):'&nbsp;').'</div>';
   if($ksListenZusagS==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusagSTitel?fKalTx(KAL_TxListenZusagSTitel):'&nbsp;').'</div>';
   if($ksListenZusage==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusageTitel?fKalTx(KAL_TxListenZusageTitel):'&nbsp;').'</div>';
   if($ksListenZusagZ==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusagZTitel?fKalTx(KAL_TxListenZusagZTitel):'&nbsp;').'</div>';
   if($ksListenCal==$j&&$j>0) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenCalTitel?fKalTx(KAL_TxListenCalTitel):'&nbsp;').'</div>';
   $k=$aKalSpalten[$j];
   if(!$kal_SortierFeld[$k]) $t='';
   else{
    if($k!=$nIndex){$t='e'; $w=''; $v='';} //$t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
    else{
     if($sRueckw!='1'&&!($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')&&strlen($sRueckw)<=0)){
      $t='t'; $w=KAL_TxAbsteigend;
      if($sRueckw==='0'&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@') &&$nIndex==KAL_ListenIndex) $v=''; else $v='&amp;kal_Rueck=1';
     }else{$t='r'; $w=KAL_TxAufsteigend; $v=''; if($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')) $v='&amp;kal_Rueck=0';}
    }
    $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
    $t='&nbsp;<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.($k!=KAL_ListenIndex?'&amp;kal_Index='.$k:'').$v.KAL_SuchParam,5).'">'.$t.'</a>';
   }
   $sFN=$kal_FeldName[$k]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   $sFS=$kal_FeldType[$k]; if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M';
   $X.="\n".'  <div class="kalTbLst kalTbLs'.$sFS.'">'.fKalTx($sFN).$t.'</div>'; $aSpTitle[$k]=fKalTx($sFN).$t;
  }
  if($ksListenInfo>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenInfTitel?fKalTx(KAL_ListenInfTitel):'&nbsp;').'</div>';
  if($ksListenAendern>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenAendTitel?fKalTx(KAL_ListenAendTitel):'&nbsp;').'</div>';
  if($ksListenKopieren>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenKopieTitel?fKalTx(KAL_ListenKopieTitel):'&nbsp;').'</div>';
  if($ksListenErinn>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenErinTitel?fKalTx(KAL_ListenErinTitel):'&nbsp;').'</div>';
  if($ksListenBenachr>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_ListenBenachTitel?fKalTx(KAL_ListenBenachTitel):'&nbsp;').'</div>';
  if($ksListenZusagS>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusagSTitel?fKalTx(KAL_TxListenZusagSTitel):'&nbsp;').'</div>';
  if($ksListenZusage>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusageTitel?fKalTx(KAL_TxListenZusageTitel):'&nbsp;').'</div>';
  if($ksListenZusagZ>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenZusagZTitel?fKalTx(KAL_TxListenZusagZTitel):'&nbsp;').'</div>';
  if($ksListenCal>=$j) $X.="\n".'  <div class="kalTbLst">'.(KAL_TxListenCalTitel?fKalTx(KAL_TxListenCalTitel):'&nbsp;').'</div>';
  $X.="\n".' </div>'; //Kopfzeile
 }elseif(file_exists(KAL_Pfad.'kalListenKopf.htm')){ //eigene Kopfzeile
  $r=@implode('',@file(KAL_Pfad.'kalListenKopf.htm')); $s=strtolower($r); $p=0;
  while($p=strpos($r,'{',$p+1)) if($i=strpos($r,'}',$p+1)){
   $v=substr($r,$p+1,$i-($p+1));
   if($k=array_search($v,$kal_FeldName)){
    if($k!=$nIndex){$t='e'; $w=''; $v='';} //$t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
    else{
     if($sRueckw!='1'&&!($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')&&strlen($sRueckw)<=0)){
      $t='t'; $w=KAL_TxAbsteigend;
      if($sRueckw==='0'&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')&&$nIndex==KAL_ListenIndex) $v=''; else $v='&amp;kal_Rueck=1';
     }else{$t='r'; $w=KAL_TxAufsteigend; $v=''; if($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts&&KAL_IFilter!='@'||KAL_ArchivRueckwaerts&&KAL_IFilter=='@')) $v='&amp;kal_Rueck=0';}
    }
    $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
    $t='&nbsp;<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.($k!=KAL_ListenIndex?'&amp;kal_Index='.$k:'').$v.KAL_SuchParam,5).'">'.$t.'</a>';
   }else $t='';
   $r=substr_replace($r,$t,$p,$i-$p+1);
  }
  if(!strpos($s,'<body')&&!strpos($s,'<head')) $X.="\n".' <div class="kalTbZl0">'."\n".$r."\n </div>";
 }

 //alle Datenzeilen ausgeben
 if($sVStil=KAL_ListeVertikal) $sVStil='vertical-align:'.$sVStil.';'; $nKatPos=array_search('k',$kal_FeldType); $nFarb=1; $sCSS='';
 if($nStart>1) $sQ='&amp;kal_Start='.$nStart.$sQ; //0-Start, 1-Index, 2-Rueck, 3-Suchparameter
 if($ksListenAendern>=0){$nDatPos=array_search('1',$aKalSpalten); $sAendDat=date('Y-m-d',time()-86400*KAL_BearbAltesNochTage);}
 foreach($aKalDaten as $a){
  $sZl=''; $sId=$a[0]; $sCSS=$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
  $bNichtZugesagt=(!($ksListenZusage&&KAL_SessionOK&&isset($aZusageTermine[$sId]))); $bZsPunkt=true; $bZsGrn=true;
  if(KAL_Laufendes&&strpos(KAL_LaufendeId,';'.$sId.';')>0) $sCSS.=' kalTbZlLfdE'; //laufendes Ereignis
  elseif(KAL_Aktuelles&&strpos(KAL_AktuelleId,';'.$sId.';')>0) $sCSS.=' kalTbZlAktE'; //aktuelles Ereignis
  elseif($nKatPos>0&&defined('KAL_ListenKateg')&&KAL_ListenKateg&&isset($a['Kat'])&&($j=$a['Kat'])) $sCSS.=' kalTrmKat'.$j; //Kategorie aus Zusatzspalte
  if($ksListenZusagZ>0||$ksListenZusagS>0){ //Zusagesummenspalte
   $nZusagAktZ=(isset($aZusageZahl[$sId])?$aZusageZahl[$sId]:'0'); $nZusagKapG=0; $nZusagFreZ=KAL_ListenZusagRLeer;
   $nZusagKapZ=(isset($a['KAP'])?$a['KAP']:'');
   if($k=strpos($nZusagKapZ,'(')){$nZusagKapG=(KAL_ZusageStatusSchwelle?(int)substr($nZusagKapZ,$k+1):0); $nZusagKapZ=(int)$nZusagKapZ;}
   if((int)$nZusagKapZ>0) $nZusagFreZ=max((int)$nZusagKapZ-$nZusagAktZ,0); //Kap vorhanden
   elseif(strlen($nZusagKapZ)==0) $nZusagKapZ=KAL_ListenZusagZLeer; elseif(substr($nZusagKapZ,0,1)==='0') $nZusagFreZ=0;
   if($ksListenZusagS>0){
    $bZsGrn=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusGlb:$nZusagKapG)||$nZusagFreZ===KAL_ListenZusagRLeer);
    $bZsGlb=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusRot:1));
   }
  }
  if(!$bEigeneZeilen){ //Standardlayout
   if($ksListenAendern==0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk)?fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=aendern'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'" alt="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'"></a>':'&nbsp;').'</div>';
   if($ksListenKopieren==0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxKopieren?fKalTx(KAL_TxKopieren):'&nbsp;').'</span><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"></a></div>';
  }else $sZl=$sEigeneZeile; //eigenes Zeilenlayout

  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //nach Zusagefeldinhalt suchen
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; if($t=='#'&&KAL_ZusageSystem){$s=strtoupper(substr($a[$j],0,1)); if($s!='J'&&$s!='Y') $bZsPunkt=false;}
  }
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; /* $sStil=$sVStil; */ $sStil=''; $sFS=''; $sKMemo='';
   if(!$bEigeneZeilen){ //Standardlayout
    if($ksListenInfo==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxSendInfo?fKalTx(KAL_TxSendInfo):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxSendInfo).'" alt="'.fKalTx(KAL_TxSendInfo).'"></a></div>';
    if($ksListenAendern==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk)?fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=aendern'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'" alt="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'"></a>':'&nbsp;').'</div>';
    if($ksListenKopieren==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxKopieren?fKalTx(KAL_TxKopieren):'&nbsp;').'</span><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"></a></div>';
    if($ksListenErinn==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxErinnService?fKalTx(KAL_TxErinnService):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"></a></div>';
    if($ksListenBenachr==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxBenachrService?fKalTx(KAL_TxBenachrService):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"></a></div>';
    if($ksListenZusagS==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM">'.($bZsPunkt?'<span class="kalTbLst">'.(($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot))?fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)):'&nbsp;').'</span><img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.gif" title="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'" alt="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'">':'').'</div>'; // Punkt nicht anzeigen, wenn Zusagefeld != JA
    if($ksListenZusage==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon)?fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'liste':'zusagen').$sSes.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1':'').'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$sId?'':'Klick2Zusage='.$sId.'&amp;kal_').'KlickZusage=':'Nummer=').$sId.$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_ListeZeigeZusage&&(KAL_SessionOK||KAL_GastLZeigeZusage))?'<a class="kalDetl" href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.(KAL_ZusagePopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_ZusagePopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':'').'</div>';
    if($ksListenZusagZ==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxZusageZeile?fKalTx(KAL_TxZusageZeile):'&nbsp;').'</span>'.fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_TxListenZusagZMuster)))).'</div>';
    if($ksListenCal==$j&&$j>0) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxCalIcon?fKalTx(KAL_TxCalIcon):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_CalPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export'.$sSes.(KAL_CalPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"></a></div>';
   }
   if(($s=$a[$j])||strlen($s)>0){
    switch($t){
     case 't': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Gastkommentar
     case 'm': if(KAL_ListenMemoLaenge==0) $s=fKalBB(fKalDt($s)); else{$s=fKalBB(fKalDt(fKalKurzMemo($s,KAL_ListenMemoLaenge))); if(substr($s,-4)=='....'){$sKMemo=substr($s,0,-4); $s='....';}} break; //Memo
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzaehlung/Kategorie/Postleitzahl
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      if($k==1){$sMon=$s2; $sJhr=substr($s,0,4);} if(KAL_MonatLLang>0&&$t=='d') $s2=fKalTx($aMonate[(int)$s2]);
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      if($t=='d'){
       if(KAL_MonatLLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
       if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
       if($k==1&&$nIndex==1){
        if(KAL_MonatsTrenner>0&&($sMon!=$sMonTrn||$sJhr!=$sJhrTrn)){$bMonTrn=true; $sMonTrn=$sMon; $sJhrTrn=$sJhr;}
        if(KAL_WochenTrenner>0){$sWoc=date('W',@mktime(8,0,0,$sMon,$s1,$sJhr)); if($sWoc!=$sWocTrn||$sJhr!=$sJhrTrn){$bWocTrn=true; $sWocTrn=$sWoc; $sJhrTrn=$sJhr;}}
        if(KAL_TagesTrenner>0&&$s!=$sTagTrn){$bTagTrn=true; $sTagTrn=$s;}
        if((int)$sId<0) $s='<span title="'.fKalDt(KAL_TxAendereVmk).'">'.$s.'*</span>';
       }
      }elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sFS.=' kalTbLsM'; break; //Uhrzeit
     case 'w': //Waehrung
      if(((float)$s)!=0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sFS.=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein); $sFS.=' kalTbLsM';
      break;
     case '#': if(KAL_ZusageSystem) $s=strtoupper(substr($s,0,1)); else $s=''; //Zusage
      if($s=='J'||$s=='Y') $s='<a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'liste':'zusagen').$sSes.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1':'').'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$sId?'':'Klick2Zusage='.$sId.'&amp;kal_').'KlickZusage=':'Nummer=').$sId.$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_ListeZeigeZusage&&(KAL_SessionOK||KAL_GastLZeigeZusage))?'<a class="kalDetl" href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.(KAL_ZusagePopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_ZusagePopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':''); else $s='&nbsp;'; $sFS.=' kalTbLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if(((float)$s)!=0||!KAL_ZahlLeer){
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sFS.=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); $sFS.=' kalTbLsM'; break; //Nummer
     case 'l': //Link
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalDt(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(KAL_LinkSymbol){$v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">'; $sFS.=' kalTbLsM';}
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //EMail
      $s='<a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=kontakt'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'"></a>';
      $sFS.=' kalTbLsM';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=KAL_TxAutorUnbekannt;
       for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
        if(!$s=$aNutzer[$n][$ksNutzerListFeld]) $s=KAL_TxAutorUnbekannt;
        break;
      }}else $s=KAL_TxAutor0000;
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.fKalDt($w).'" alt="'.fKalDt($w).'">'; $sFS.=' kalTbLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.abs($sId).'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
      if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
      $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'"></div>'; $sFS.=' kalTbLsM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalDt(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' kalTbLsM';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
      $s='<a class="kalDetl" href="'.KAL_Url.KAL_Bilder.abs($sId).'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sFS.=' kalTbLsM';
   }else $s='&nbsp;';
   if($kal_FeldName[$k]=='KAPAZITAET'){if($s>'0') $s=(int)$s; $s.='&nbsp;'; $sFS.=' kalTbLsR';}
   if(($w=$kal_SpaltenStil[$k])) $sStil=' style="'.$sStil.$w.'"';
   if($kal_LinkFeld[$k]>0||strlen($sKMemo)>0) $s=$sKMemo.'<a class="kalDetl" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail'.$sSes.(KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'" title="'.fKalTx(KAL_TxDetail).'"'.(KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'>'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="kalTbLst'.$sFS.'"'.$sStil.'><span class="kalTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$kal_FeldName[$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if($bMonTrn){$bMonTrn=false; $X.="\n".' <div class="kalTbZlT">'.(KAL_MonatsTrenner<2?'&nbsp;':fKalTx($aMonTrn[(int)$sMon])).(KAL_MonatsTrenner>2?'&nbsp;'.$sJhr:'').'</div>';}
  if($bWocTrn){$bWocTrn=false; $X.="\n".' <div class="kalTbZlT">'.(KAL_WochenTrenner<2?'&nbsp;':$sWocTrn.'.&nbsp;'.fKalTx(KAL_TxWoche)).(KAL_WochenTrenner>2?'&nbsp;'.$sJhr:'').'</div>';}
  if($bTagTrn){$bTagTrn=false; $X.="\n".' <div class="kalTbZlT">'.(KAL_TagesTrenner<2?'&nbsp;':fKalTx($sTagTrn)).'</div>';}
  if(!$bEigeneZeilen){ //Standardlayout
   if($ksListenInfo>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxSendInfo?fKalTx(KAL_TxSendInfo):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxSendInfo).'" alt="'.fKalTx(KAL_TxSendInfo).'"></a></div>';
   if($ksListenAendern>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk)?fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=aendern'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'" alt="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'"></a>':'&nbsp;').'</div>';
   if($ksListenKopieren>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxKopieren?fKalTx(KAL_TxKopieren):'&nbsp;').'</span><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"></a></div>';
   if($ksListenErinn>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxErinnService?fKalTx(KAL_TxErinnService):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"></a></div>';
   if($ksListenBenachr>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxBenachrService?fKalTx(KAL_TxBenachrService):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"></a></div>';
   if($ksListenZusagS>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM">'.($bZsPunkt?'<span class="kalTbLst">'.(($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot))?fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)):'&nbsp;').'</span><img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.gif" title="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'" alt="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'">':'').'</div>'; // Punkt nicht anzeigen, wenn Zusagefeld != JA
   if($ksListenZusage>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon)?fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'liste':'zusagen').$sSes.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1':'').'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$sId?'':'Klick2Zusage='.$sId.'&amp;kal_').'KlickZusage=':'Nummer=').$sId.$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_ListeZeigeZusage&&(KAL_SessionOK||KAL_GastLZeigeZusage))?'<a class="kalDetl" href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.(KAL_ZusagePopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_ZusagePopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':'').'</div>';
   if($ksListenZusagZ>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxZusageZeile?fKalTx(KAL_TxZusageZeile):'&nbsp;').'</span>'.fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_TxListenZusagZMuster)))).'</div>';
   if($ksListenCal>=$j) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.(KAL_TxCalIcon?fKalTx(KAL_TxCalIcon):'&nbsp;').'</span><a class="kalDetl" href="'.(KAL_CalPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export'.$sSes.(KAL_CalPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"></a></div>';
   $X.="\n".' <div class="kalTbZl'.$sCSS.'">'.$sZl."\n".' </div><div class="kalTbZlX"></div>';
  }else{ //eigenes Layout
   $sZl=str_replace('{Nummer}',($bMitID?sprintf('%0'.KAL_NummerStellen.'d',$sId):''),$sZl);
   $sZl=str_replace('{Info}',($ksListenInfo>0?'<a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxSendInfo).'" alt="'.fKalTx(KAL_TxSendInfo).'"></a>':''),$sZl);
   $sZl=str_replace('{Aendern}',($ksListenAendern>=0&&$a[$nDatPos]>=$sAendDat?'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=aendern'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'" alt="'.fKalTx((int)$sId>0?KAL_TxAendern:KAL_TxAendereVmk).'"></a>':''),$sZl);
   $sZl=str_replace('{Kopieren}',($ksListenKopieren>=0?'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'"'.'><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"></a>':''),$sZl);
   $sZl=str_replace('{Erinnern}',($ksListenErinn>0?'<a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"></a>':''),$sZl);
   $sZl=str_replace('{Nachricht}',($ksListenBenachr>0?'<a class="kalDetl" href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"></a>':''),$sZl);
   $sZl=str_replace('{Zusagen}',($ksListenZusage>=0?'<a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'liste':'zusagen').$sSes.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1':'').'&amp;kal_'.($bEinKlickZusage?($nEinKlickTId!=$sId?'':'Klick2Zusage='.$sId.'&amp;kal_').'KlickZusage=':'Nummer=').$sId.$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"></a>'.((KAL_ListeZeigeZusage&&(KAL_SessionOK||KAL_GastLZeigeZusage))?'<a class="kalDetl" href="'.(KAL_ZusagePopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.$sSes.(KAL_ZusagePopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_ZusagePopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"></a>':''):''),$sZl);
   $sZl=str_replace('{ZusageStatus}',(($ksListenZusagS>=0&&$bZsPunkt)?'<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.gif" title="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'" alt="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'">':''),$sZl);
   $sZl=str_replace('{ZusageZahl}',($ksListenZusagZ>=0?fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_TxListenZusagZMuster)))):''),$sZl);
   $sZl=str_replace('{Export}',($ksListenCal>0?'<a class="kalDetl" href="'.(KAL_CalPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export'.$sSes.(KAL_CalPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"></a>':''),$sZl);
   for($j=count($kal_FeldName)-1;$j>=0;$j--) if(!(strpos($sZl,'{'.$kal_FeldName[$j].'}')===false)) $sZl=str_replace('{'.$kal_FeldName[$j].'}','&nbsp;',$sZl);
   $X.="\n".' <div class="kalTbZl'.$sCSS.'">'."\n".$sZl."\n </div>";
  }
 }
 $X.="\n".'</div>'; //Tabelle

 //Navigator unter der Tabelle
 if(KAL_SuchFilter==9) $X.="\n".$sSuch;
 if(KAL_NaviUnten) $X.="\n".$sNavig;
 if(KAL_SuchFilter==10) $X.="\n".$sSuch;

 return $X;
}

function fKalNavigator($nStart,$nCount,$sQry,$sSes){ //Navigator zum Blaettern
 $nPgs=ceil($nCount/KAL_ListenLaenge); $nPag=ceil($nStart/KAL_ListenLaenge);
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 $X ="\n".'<div class="kalNavL">';
 $X.="\n".'<div class="kalSZhl">'.fKalTx(KAL_TxSeite).' '.$nPag.'/'.$nPgs.'</div>';
 $X.="\n".'<div class="kalNavi"><ul class="kalNavi">';
 $X.='<li class="kalNavL"><a href="'.KAL_Self.(KAL_Query.$sSes.$sQry?'?':'').substr(KAL_Query.$sSes.$sQry,5).'" title="'.fKalTx(KAL_TxAnfang).'">|&lt;</a></li>';
 $sL='<li><a href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.'&amp;',5).'kal_Start=';
 for($i=$nAnf;$i<=$nEnd;$i++){
  $X.=$sL.(($i-1)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxSeite).$i.'">'.($i!=$nPag?$i:'<b>'.$i.'</b>').'</a></li>';
 }
 $X.='<li class="kalNavR"><a href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.'&amp;',5).'kal_Start='.(max($nPgs-1,0)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxEnde).'">&gt;|</a></li>';
 $X.='</ul></div>';
 $X.="\n".'<div class="kalClear"></div>';
 $X.="\n".'</div>';
 return $X;
}

function fKalSuchFilter($s,$sSes){ //Schnellsuchfilter zeichnen
if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
return '
<div class="kalSFlt'.(KAL_SuchFilter==4?'R':(KAL_SuchFilter==3?'L':'')).'">
<form class="kalFilt" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).rtrim("\n".($sSes!=''?'<input type="hidden" name="kal_Session" value="'.substr($sSes,17,12).'">'.(strpos($sSes,'Z')?'<input type="hidden" name="kal_Zentrum" value="1">':''):'')).'
<div class="kalNoBr">'.fKalTx(KAL_TxSuchen).' <input class="kalSFlt" name="kal_Such" value="'.fKalTx($s).'"> <input type="submit" class="kalKnopf" value="" title="'.fKalTx(KAL_TxSuchen).'"></div>'.(!KAL_SuchFltArchiv?'':'
<div class="kalNoBr"><input class="kalCheck" type="checkbox" name="kal_ASuch" value="1" '.(isset($_GET['kal_ASuch'])&&$_GET['kal_ASuch']=='1'||isset($_POST['kal_ASuch'])&&$_POST['kal_ASuch']=='1'?'checked="checked" ':'').'> '.fKalTx(KAL_TxMitArchiv).'</div>').'
</form>
</div>';
}

function fKalIntervallFilter($sSes){ //Intervallfilter zeichnen
$sIvWerte=(KAL_SessionOK?KAL_NIvWerte:KAL_IvWerte); $sHid=''; $s=' selected="selected"';
if(strlen($sIvWerte)>0){
if(KAL_SucheBleibt){
 $aQ=explode('&amp;',substr(KAL_SuchParam,5));
 if(is_array($aQ)) foreach($aQ as $v) if(substr($v,0,13)!='kal_Intervall') if($p=strpos($v,'=')) $sHid.='<input type="hidden" name="'.substr($v,0,$p).'" value="'.fKalTx(urldecode(substr($v,$p+1))).'">';
}
$r='
<div class="kalIFlt'.(KAL_SuchFilter==4?'L':(KAL_SuchFilter==3?'R':'')).'">
<form class="kalFilt" action="'.KAL_Self.'" method="get">'.rtrim("\n".KAL_Hidden).rtrim("\n".($sSes!=''?'<input type="hidden" name="kal_Session" value="'.substr($sSes,17,12).'">'.(strpos($sSes,'Z')?'<input type="hidden" name="kal_Zentrum" value="1">':''):'')).'
 <div class="kalNoBr">
 <select class="kalIFlt" name="kal_Intervall" size="1">'."\n";
if(strpos($sIvWerte,'0')>0) $r.='  <option value="0"'.(KAL_IFilter=='0'?$s:'').'>'.fKalTx(KAL_TxAlle.' '.KAL_TxTermine).'</option>'."\n";
if(strpos($sIvWerte,'1')>0) $r.='  <option value="1"'.(KAL_IFilter=='1'?$s:'').'>1 '.fKalTx(KAL_TxTag).'</option>'."\n";
if(strpos($sIvWerte,'3')>0) $r.='  <option value="3"'.(KAL_IFilter=='3'?$s:'').'>3 '.fKalTx(KAL_TxTage).'</option>'."\n";
if(strpos($sIvWerte,'7')>0) $r.='  <option value="7"'.(KAL_IFilter=='7'?$s:'').'>1 '.fKalTx(KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'4')>0) $r.='  <option value="14"'.(KAL_IFilter=='14'?$s:'').'>2 '.fKalTx(KAL_TxWochen).'</option>'."\n";
if(strpos($sIvWerte,'A')>0) $r.='  <option value="A"'.(KAL_IFilter=='A'?$s:'').'>1 '.fKalTx(KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'C')>0) $r.='  <option value="C"'.(KAL_IFilter=='C'?$s:'').'>3 '.fKalTx(KAL_TxMonate).'</option>'."\n";
if(strpos($sIvWerte,'F')>0) $r.='  <option value="F"'.(KAL_IFilter=='F'?$s:'').'>6 '.fKalTx(KAL_TxMonate).'</option>'."\n";
if(strpos($sIvWerte,'L')>0) $r.='  <option value="L"'.(KAL_IFilter=='L'?$s:'').'>1 '.fKalTx(KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'a')>0) $r.='  <option value="a"'.(KAL_IFilter=='a'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'d')>0) $r.='  <option value="d"'.(KAL_IFilter=='d'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'g')>0) $r.='  <option value="g"'.(KAL_IFilter=='g'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'j')>0) $r.='  <option value="j"'.(KAL_IFilter=='j'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'m')>0) $r.='  <option value="m"'.(KAL_IFilter=='m'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'c')>0) $r.='  <option value="c"'.(KAL_IFilter=='c'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'f')>0) $r.='  <option value="f"'.(KAL_IFilter=='f'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'i')>0) $r.='  <option value="i"'.(KAL_IFilter=='i'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'l')>0) $r.='  <option value="l"'.(KAL_IFilter=='l'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'o')>0) $r.='  <option value="o"'.(KAL_IFilter=='o'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'b')>0) $r.='  <option value="b"'.(KAL_IFilter=='b'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'e')>0) $r.='  <option value="e"'.(KAL_IFilter=='e'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'h')>0) $r.='  <option value="h"'.(KAL_IFilter=='h'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'k')>0) $r.='  <option value="k"'.(KAL_IFilter=='k'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'n')>0) $r.='  <option value="n"'.(KAL_IFilter=='n'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'@')>0) $r.='  <option value="@"'.(KAL_IFilter=='@'?$s:'').'>'.fKalTx(KAL_TxArchiv).'</option>'."\n";
$r.=' </select>'.$sHid.'
 <input type="submit" class="kalKnopf" value="" title="OK">
 </div>
</form>
</div>';} else $r='';
return $r;
}

function fKalKurzMemo($s,$nL=80){ //Text mit BB-Code einkuerzen
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
  return substr($v,1).'....';
 }else return $s;
}
?>