<?php
function fKalSeite(){ //Seiteninhalt
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_ZeilenStil,
  $kal_Kategorien, $kal_Symbole, $kal_WochenTag, $aKalDaten, $oKalDbO;
 if(KAL_SessionOK&&KAL_NDetailAnders) $kal_DetailFeld=$GLOBALS['kal_NDetailFeld'];
 $DbO=$oKalDbO; $sSes=''; if(KAL_SessionOK) $sSes=KAL_Session;

 //Sortierung und Startposition
 $sId=(isset($_GET['kal_Nummer'])?fKalRq1($_GET['kal_Nummer']):''); $sQ=''; $X=''; $sGMap=''; $Et=''; $Et2=''; $Es='Fehl';
 if(isset($_GET['kal_Start'])){$n=(int)$_GET['kal_Start']; if($n>1) $sQ.='&amp;kal_Start='.$n;} //0-Start
 $n=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_ListenIndex); if($n!=KAL_ListenIndex) $sQ.='&amp;kal_Index='.$n; //1-Index
 if(isset($_GET['kal_Rueck'])) $sQ.='&amp;kal_Rueck='.fKalRq1($_GET['kal_Rueck']); //2-Rueck
 if(isset($_GET['kal_Monat'])&&($s=$_GET['kal_Monat'])&&strlen($s)==7) $sQ.='&amp;kal_Monat='.$s; //Monatsblatt
 if(isset($_GET['kal_Woche'])&&($s=$_GET['kal_Woche'])&&strlen($s)==7) $sQ.='&amp;kal_Woche='.$s; //Wochenblatt
 $sQ.=KAL_SuchParam; //3-Suchparameter
 if(isset($_GET['kal_Aendern'])){$sQ.='&amp;kal_Aendern=1';}
 if(isset($_GET['kal_Kopieren'])){$sQ.='&amp;kal_Kopieren=1';}

 //1-Klick-Zusagen
 $bEinKlickZusage=(KAL_EinKlickDZusage&&KAL_SessionOK); if($bEinKlickZusage) include KAL_Pfad.'kalZusage1Klick.php';

 if(!$nVorg=$aKalDaten[1]) $nVorg=$sId; $nPos=$aKalDaten[2]; if(!$nNachf=$aKalDaten[3]) $nNachf=$sId;

 //Zusatzzeilen ermitteln
 $ksDetailInfo=(KAL_GastDInfo||KAL_SessionOK?KAL_DetailInfo:-1);
 $ksDetailAendern=(KAL_GastDAendern||KAL_SessionOK?KAL_DetailAendern:-1);
 $ksDetailKopieren=(KAL_GastDKopieren||KAL_SessionOK?KAL_DetailKopieren:-1);
 $ksDetailErinn=(KAL_GastDErinn||KAL_SessionOK?KAL_DetailErinn:-1);
 $ksDetailBenachr=(KAL_GastDBenachr||KAL_SessionOK?KAL_DetailBenachr:-1);
 $ksDetailDrucken=(KAL_GastDDrucken||KAL_SessionOK?KAL_DetailDrucken:-1);
 $ksDetailZusage=(KAL_ZusageSystem&&(KAL_GastDZusage||KAL_SessionOK)?KAL_DetailZusage:-1);
 $ksDetailCal=(KAL_GastDCal||KAL_SessionOK?KAL_DetailCal:-1);

 //eventuell Zusagedaten holen
 $bNichtZugesagt=true; $sZusag=''; $nZusagAktZ=0;
 if(KAL_ZusageSystem&&($ksDetailZusage>0||(($n=array_search('#',$kal_FeldType))&&$kal_DetailFeld[$n]>0))){
  $nNId=(int)substr($sSes,17,4); $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageAnzahlPos=array_search('ANZAHL',$kal_ZusageFelder);
  if(!KAL_SQL){//
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $n=count($aD); $m=max(9,$nZusageAnzahlPos+2);
   for($i=1;$i<$n;$i++){
    $a=explode(';',$aD[$i],$m);
    if(KAL_SessionOK&&isset($a[7])&&$a[7]==$nNId&&$a[1]==$sId) $bNichtZugesagt=false;
    if($a[1]==$sId&&$nZusageAnzahlPos>0) if($a[6]=='1'||!KAL_ZaehleAktiveZusagen) if($z=(int)$a[$nZusageAnzahlPos]) $nZusagAktZ+=$z;
   }
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,termin,aktiv,benutzer'.($nZusageAnzahlPos?',dat_'.$nZusageAnzahlPos:'').' FROM '.KAL_SqlTabZ.' WHERE termin="'.$sId.'"')){
    while($a=$rR->fetch_row()){
     if(KAL_SessionOK&&(int)$a[3]==$nNId) $bNichtZugesagt=false;
     if($nZusageAnzahlPos>0) if($a[2]=='1'||!KAL_ZaehleAktiveZusagen) $nZusagAktZ+=$a[4];
    }$rR->close();
   }
  }
  $sZusag='<a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion='.($bEinKlickZusage?'detail':'zusagen').KAL_Session.(KAL_ZusagePopup&&!$bEinKlickZusage||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.($bEinKlickZusage?($nEinKlickTId==$sId?'&amp;kal_Klick2Zusage='.$sId:'').'&amp;kal_KlickZusage='.$sId:'').$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"> '.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'</a>';
  if(KAL_DetailZeigeZusage&&(KAL_SessionOK||KAL_GastDZeigeZusage)) $sZusag.=', <a class="kalDetl" href="'.(KAL_ZusagePopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=zusagezeigen'.KAL_Session.(KAL_ZusagePopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_ZusagePopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"> '.fKalTx(KAL_TxZeigeZusageIcon).'</a>';
  if(KAL_DetailZusagS||strlen(KAL_TxDetailZusagZMuster)>0){
   $nZusagKapG=0; $nZusagFreZ=KAL_DetailZusagRLeer;
   $nZusagKapZ=(isset($aKalDaten[0]['KAP'])?$aKalDaten[0]['KAP']:'');
   if($k=strpos($nZusagKapZ,'(')){$nZusagKapG=(KAL_ZusageStatusSchwelle?(int)substr($nZusagKapZ,$k+1):0); $nZusagKapZ=substr($nZusagKapZ,0,$k);}
   if((int)$nZusagKapZ>0) $nZusagFreZ=max((int)$nZusagKapZ-$nZusagAktZ,0); // Kap vorhanden
   elseif(strlen($nZusagKapZ)==0) $nZusagKapZ=KAL_DetailZusagKLeer; elseif(substr($nZusagKapZ,0,1)==='0') $nZusagFreZ=0;
   if(KAL_DetailZusagS){
    $bZsGrn=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusGlb:$nZusagKapG)||$nZusagFreZ===KAL_DetailZusagRLeer);
    $bZsGlb=($nZusagFreZ>0&&$nZusagFreZ>=(!KAL_ZusageStatusSchwelle||!$nZusagKapG?KAL_ZusageStatusRot:1));
    $sZusag='<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.($bZsGrn?'Grn':($bZsGlb?'Glb':'Rot')).'.gif" title="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'" alt="'.fKalTx($bZsGrn?KAL_TxZusageStatusGrn:($bZsGlb?KAL_TxZusageStatusGlb:KAL_TxZusageStatusRot)).'"> '.$sZusag;
   }
   if(strlen(KAL_TxDetailZusagZMuster)>0){
    $sZusag.=' '.fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_TxDetailZusagZMuster))));
   }
  }
 }

 // Mails als Popup-Fenster
 if((KAL_MailPopup||(KAL_ZusagePopup&&KAL_ZusageSystem)||KAL_DruckPopup)&&!KAL_DetailPopup&&!defined('KAL_KalWin')){$X="\n".'<script>function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'; define('KAL_KalWin',true);}
 if(($ksDetailCal||KAL_LinkOExpt||KAL_LinkUExpt)&&KAL_CalPopup) $X.="\n".'<script>function ExpWin(sURL){expWin=window.open(sURL,"expwin","width='.KAL_CalPopupBreit.',height='.KAL_CalPopupHoch.',left='.KAL_CalPopupX.',top='.KAL_CalPopupY.',menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");expWin.focus();}</script>';

 // Navigator vorwaerts/rueckwaerts
 $sNavig=fKalNavigator($nPos,KAL_Saetze,$nVorg,$nNachf,$sQ); $sDatum='';
 if(KAL_DetailNaviOben==1) $X.="\n".$sNavig;

 // Meldung ausgeben
 if(empty($Et)) $X.="\n".(!strpos(KAL_Meldung,'kalFehl')?'<p class="kalMeld">'.fKalTx(KAL_TxDetails).'</p>':KAL_Meldung); //'#' wird tiefer ersetzt
 else $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).$Et2.'</p>';

 // Navigator ueber der Tabelle
 if(KAL_DetailNaviOben==2) $X.="\n".$sNavig;

 $a=$aKalDaten[0]; $nFelder=count($kal_FeldName); $kal_FeldName[0]=KAL_TxNummer; $nFarb=1;
 if(!KAL_EigeneDetails) $X.="\n\n".'<div class="kalTabl">';
 else if(!$Dtl=(file_exists(KAL_Pfad.'kalDetail.htm')?join('',file(KAL_Pfad.'kalDetail.htm')):'')) $Dtl='<p style="color:red;font-weight:bold;">'.fKalTx(str_replace('#','kalDetail.htm',KAL_TxKeinDetail)).'</p>';
 for($i=0;$i<$nFelder;$i++){
  if($ksDetailInfo==$i){ //Info-Zeile
   $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxInfoSenden).'" alt="'.fKalTx(KAL_TxInfoSenden).'"> '.fKalTx(KAL_TxSendInfo).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxInfoSenden).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{SendInfo}',$s,$Dtl); //eigene Details
  }
  if($ksDetailAendern==$i){ //Aendern-Zeile
   if($a[1]>=date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){
    if((!$n=array_search('u',$kal_FeldType))||(!KAL_SessionOK&&$a[$n]=='0000')) $s=fKalAendernLink($sId.$sQ,'');
    elseif(KAL_SessionOK&&$a[$n]==(int)substr(KAL_Session,17,4)) $s=fKalAendernLink($sId.$sQ,(KAL_NAendernFremde?KAL_TxTerminEigen.' ':''),(int)$a[0]<0);
    elseif(KAL_SessionOK&&KAL_NAendernFremde) $s=fKalAendernLink($sId.$sQ,KAL_TxTerminFremd.' ');
    else $s='<span title="'.fKalTx(KAL_TxNummerFremd).'">---</span>';
   }else $s='<span title="'.fKalTx(KAL_TxAendereZuAlt).'">--</span>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxAendern).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Aendern}',$s,$Dtl); //eigene Details
  }
  if($ksDetailKopieren==$i){ //Kopieren-Zeile
   $s='<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.KAL_Session.'&amp;kal_Nummer='.$sId.$sQ.'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxKopieren).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxKopieren).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Kopieren}',$s,$Dtl); //eigene Details
  }
  if($ksDetailErinn==$i){ //Erinnerungs-Zeile
   $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"> '.fKalTx(KAL_TxErinnService).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxErinServ).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Erinnern}',$s,$Dtl); //eigene Details
  }
  if($ksDetailBenachr==$i){ //Benachrichtigungs-Zeile
   $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"> '.fKalTx(KAL_TxBenachrService).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxBenachServ).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Nachricht}',$s,$Dtl); //eigene Details
  }
  if($ksDetailCal==$i){ //iCal-Export-Zeile
   $s='<a class="kalDetl" href="'.(KAL_CalPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export'.KAL_Session.(KAL_CalPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"> '.fKalTx(KAL_TxCalIcon).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxCalZeile).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Export}',$s,$Dtl); //eigene Details
  }
  if($ksDetailDrucken==$i){ //Drucken-Zeile
   if(KAL_DruckPopup) $s='<a class="kalDetl" href="'.KAL_Url.'kalender.php?kal_Aktion=drucken'.KAL_Session.'&amp;kal_Nummer='.$sId.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;"><img class="kalIcon" src="'.KAL_Url.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
   else $s='<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=drucken'.KAL_Session.'&amp;kal_Nummer='.$sId.$sQ.'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxDrucken).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Drucken}',$s,$Dtl); //eigene Details
  }
  if($ksDetailZusage==$i){ //Zusage-Zeile
   $s=$sZusag;
   if(!KAL_EigeneDetails){ //Standardlayout
    $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxZusageZeile).'</div>';
    $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
   }else $Dtl=str_replace('{Zusagen}',$s,$Dtl); //eigene Details
  }
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if(($kal_DetailFeld[$i]>0||KAL_EigeneDetails)&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
   if(($s=(isset($a[$i])?$a[$i]:''))||strlen($s)>0){
    switch($t){
     case 't': case 'm': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Memo//Gastkommentar
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      if(KAL_MonatDLang>0&&$t=='d'){$aMonate=explode(';',';'.(KAL_MonatDLang==2?KAL_TxLMonate:KAL_TxKMonate)); $s2=fKalTx($aMonate[(int)$s2]);}
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      if($t=='d'){
       if(KAL_MonatDLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
       if($i==1) if($nP=strpos($X,'#',strpos($X,'class="kalMeld"'))) $X=substr_replace($X,$s,$nP,1);
       if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
       if($i==1&&(int)$a[0]<0) $s='<span title="'.fKalTx(KAL_TxAendereVmk).'">'.$s.' *</span>';
      }else if($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
     case 'w': //Waehrung
      if(((float)$s)!=0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung;
      }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein);
      break;
     case '#': if(KAL_ZusageSystem) $s=strtoupper(substr($s,0,1)); else $s=''; //Zusage
      if($s=='J'||$s=='Y'){
       $s=$sZusag;
      }elseif($s=='N') $s=fKalTx(KAL_TxNein); else $s='&nbsp;';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if(((float)$s)!=0||!KAL_ZahlLeer){
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
      }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
      break;
     case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); if((int)$a[0]<0) $s='<span title="'.fKalTx(KAL_TxAendereVmk).'">'.$s.' *</span>'; break; //Zaehlnummer
     case 'l': //Link
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $u=fKalDt(isset($aI[1])?$aI[1]:$w);
       $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'"> ';
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //eMail
      $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=kontakt'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'"> '.fKalTx(KAL_TxKontaktSenden).'</a>';
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.fKalDt($w).'" alt="'.fKalDt($w).'">';
      break;
     case 'b': //Bild
      $s=substr($s,strpos($s,'|')+1); $s=KAL_Bilder.$sId.'_'.$s; $aI=@getimagesize(KAL_Pfad.$s); $w=fKalDt(substr($s,strpos($s,'_')+1,-4));
      $s='<img class="kalBild" src="'.KAL_Url.$s.'" style="max-width:'.$aI[0].'px;max-height:'.$aI[1].'px;" title="'.$w.'" alt="'.$w.'">';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalDt(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'"> ';
      if(!KAL_DetailDateiSymbol) $v.=fKalKurzName($s);
      $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=KAL_TxAutorUnbekannt;
       if(!KAL_SQL){ //Textdaten
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
        for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
         if(!$s=$aN[$kal_DetailFeld[$i]]) $s=KAL_TxAutorUnbekannt; elseif($kal_DetailFeld[$i]<5&&$kal_DetailFeld[$i]>1) $s=fKalDeCode($s); $s=fKalDt($s);
         break;
        }
       }elseif($DbO){ //SQL-Daten
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
         $aN=$rR->fetch_row(); $rR->close();
         if(is_array($aN)){array_splice($aN,1,1); if(!$s=fKalDt($aN[$kal_DetailFeld[$i]])) $s=KAL_TxAutorUnbekannt;}
         else $s=KAL_TxAutorUnbekannt;
       }}
      }else $s=KAL_TxAutor0000;
      break;
     case 'x': $aI=explode(',',$s); //StreetMap
      if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
       $s='<div class="kalNorm" id="GGeo'.$i.'" style="width:99%;max-width:'.KAL_GMapBreit.'px;height:'.KAL_GMapHoch.'px;">'.fKalTx(KAL_TxGMap1Warten).'<br><a class="kalText" href="javascript:showMap'.$i.'()" title="'.fKalTx($sFN).'">'.fKalTx(KAL_TxGMap2Warten).'</a></div>';
       $sGMap.=(KAL_GMapSource=='O'?fKalOMap($i,$aI):fKalGMap($i,$aI));
      }else $s='&nbsp;';
      break;
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }//switch
   }elseif($t=='b'&&KAL_ErsatzBildGross>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildGross; $aI=@getimagesize(KAL_Pfad.$s); $s='<img class="kalBild" src="'.KAL_Url.$s.'" style="max-width:'.$aI[0].'px;max-height:'.$aI[1].'px;border:0" alt="kein Bild">';
   }elseif(KAL_ZeigeLeeres) $s='&nbsp;';
   if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt) $s=''; elseif($s>'0') $s=(int)$s;}
   elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(!KAL_EigeneDetails){ //Standardlayout
    if(strlen($s)>0){
     $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".'  <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
     $X.="\n".'  <div class="kalTbSp2"'.($kal_ZeilenStil[$i]?' style="'.$kal_ZeilenStil[$i].'"':'').'>'.$s."</div>\n </div>";
    }
   }else $Dtl=str_replace('{'.$kal_FeldName[$i].'}',$s,$Dtl); //eigene Details
  }
 }
 if($ksDetailInfo>=$nFelder){ //Info-Zeile am Ende
  $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=info'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxInfoSenden).'" alt="'.fKalTx(KAL_TxInfoSenden).'"> '.fKalTx(KAL_TxSendInfo).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxInfoSenden).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{SendInfo}',$s,$Dtl); //eigene Details
 }
 if($ksDetailErinn>=$nFelder){ //Erinnerungs-Zeile
  $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=erinnern'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"> '.fKalTx(KAL_TxErinnService).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxErinServ).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Erinnern}',$s,$Dtl); //eigene Details
 }
 if($ksDetailBenachr>=$nFelder){ //Benachrichtigungs-Zeile
  $s='<a class="kalDetl" href="'.(KAL_MailPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=nachricht'.KAL_Session.(KAL_MailPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_MailPopup&&!KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"> '.fKalTx(KAL_TxBenachrService).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxBenachServ).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Nachricht}',$s,$Dtl); //eigene Details
 }
 if($ksDetailAendern>=$nFelder){ //Aendern-Zeile
  if($a[1]>=date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){
   if((!$n=array_search('u',$kal_FeldType))||(!KAL_SessionOK&&$a[$n]=='0000')) $s=fKalAendernLink($sId.$sQ,'');
   elseif(KAL_SessionOK&&$a[$n]==(int)substr(KAL_Session,17,4)) $s=fKalAendernLink($sId.$sQ,(KAL_NAendernFremde?KAL_TxTerminEigen.' ':''),(int)$a[0]<0);
   elseif(KAL_SessionOK&&KAL_NAendernFremde) $s=fKalAendernLink($sId.$sQ,KAL_TxTerminFremd.' ');
   else $s='<span title="'.fKalTx(KAL_TxNummerFremd).'">---</span>';
  }else $s='<span title="'.fKalTx(KAL_TxAendereZuAlt).'">--</span>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxAendern).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Aendern}',$s,$Dtl); //eigene Details
 }
 if($ksDetailKopieren>=$nFelder){ //Kopieren-Zeile
  $s='<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=kopieren'.KAL_Session.'&amp;kal_Nummer='.$sId.$sQ.'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconKopieren.gif" title="'.fKalTx(KAL_TxKopieren).'" alt="'.fKalTx(KAL_TxKopieren).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxKopieren).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxKopieren).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Kopieren}',$s,$Dtl); //eigene Details
 }
 if($ksDetailCal>=$nFelder){ //iCal-Export-Zeile
  $s='<a class="kalDetl" href="'.(KAL_CalPopup||KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=export'.KAL_Session.(KAL_CalPopup||KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"> '.fKalTx(KAL_TxCalZeile).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxCalZeile).'</div>';
   $X.="\n".'  <div class="kalTbSp2">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Export}',$s,$Dtl); //eigene Details
 }
 if($ksDetailDrucken>=$nFelder){ //Druck-Zeile
  if(KAL_DruckPopup) $s='<a class="kalDetl" href="'.KAL_Url.'kalender.php?kal_Aktion=drucken'.KAL_Session.'&amp;kal_Nummer='.$sId.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;"><img class="kalIcon" src="'.KAL_Url.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
  else $s='<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=drucken'.KAL_Session.'&amp;kal_Nummer='.$sId.$sQ.'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxDrucken).'</div>';
   $X.="\n".'  <div class="kalTbSp1">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Drucken}',$s,$Dtl); //eigene Details
 }
 if($ksDetailZusage>=$nFelder){ //Zusage-Zeile
  $s=$sZusag;
  if(!KAL_EigeneDetails){ //Standardlayout
   $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxZusageZeile).'</div>';
   $X.="\n".'  <div class="kalTbSp1">'.$s."</div>\n </div>";
  }else $Dtl=str_replace('{Zusagen}',$s,$Dtl); //eigene Details
 }
 if(!KAL_EigeneDetails) $X.="\n".'</div>'; // Tabelle
 else{
  $Dtl=str_replace('{Aendern}','',str_replace('{Kopieren}','',str_replace('{Nachricht}','',str_replace('{Erinnern}','',$Dtl))));
  $Dtl=str_replace('{SendInfo}','',str_replace('{Export}','',str_replace('{Drucken}','',str_replace('{Zusagen}','',$Dtl))));
  $X.="\n".$Dtl;
 }

 // Navigator unter der Tabelle
 if(KAL_DetailNaviUnten==1) $X.="\n".$sNavig;

 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(KAL_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.KAL_Url.'maps/leaflet.css">'."\n".$X."\n\n".'<script src="'.KAL_Url.'maps/leaflet.js"></script>';
  else $X.="\n\n".'<script src="'.KAL_GMapURL.'"></script>';
  $X.="\n".'<script>'.$sGMap."\n".'</script>';
 }
 return $X;
}

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalAendernLink($sNrQ,$sHint,$bVmk=false){
 return '<a class="kalDetl"'.($bVmk?' title="'.fKalTx(KAL_TxAendereVmk).'"':'').' href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=aendern'.KAL_Session.'&amp;kal_Nummer='.$sNrQ.'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx(KAL_TxAendern).'" alt="'.fKalTx(KAL_TxAendern).'"> '.fKalTx(trim($sHint.KAL_TxTerminAendern)).'</a>';
}

//Navigator zum Blaettern
function fKalNavigator($nPos,$nZahl,$nVorg,$nNachf,$sQry){
 $sL=(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail'.KAL_Session.(KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer=';
 $X ="\n".'<div class="kalNavD">';
 $X.="\n".' <div class="kalNavR"><a class="kalDetR" href="'.$sL.$nVorg.$sQry.'" title="'.fKalTx(KAL_TxZumAnfang).'"></a></div>';
 $X.="\n".' <div class="kalNavV"><a class="kalDetV" href="'.$sL.$nNachf.$sQry.'" title="'.fKalTx(KAL_TxZumEnde).'"></a></div>';
 $X.="\n".fKalTx(KAL_TxTermin).' '.$nPos.'/'.$nZahl;
 $X.="\n".'</div>';
 return $X;
}

function fKalOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');
  var mbAttr=\'Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>\';
  var mbUrl=\'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='.KAL_SMapCode.'\';
  var sat=L.tileLayer(mbUrl,{id:\'mapbox/satellite-v9\',tileSize:512,zoomOffset:-1,attribution:mbAttr});
  var osm=L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\',{attribution:\'&copy OpenStreetMap\',maxZoom:19});
  var bDrag=true; if('.(KAL_SMap2Finger?'true':'false').') bDrag=!L.Browser.mobile;
  var map'.$n.'=L.map(\'GGeo'.$n.'\',{center:['.sprintf('%.15f,%.15f',$a[0],$a[1]).'],zoom:'.$a[4].(KAL_SMap2Finger?',dragging:bDrag,tap:bDrag':'').',scrollWheelZoom:false,layers:[osm]});
  if('.(KAL_SMapTypeControl?'true':'false').'){var baseLayers={\'Karte\':osm,\'Satellit\':sat}; var layerControl=L.control.layers(baseLayers).addTo(map'.$n.');}
  var marker=L.marker(['.sprintf('%.15f,%.15f',$a[2],$a[3]).'],{opacity:0.75'.(KAL_TxGMapOrt>''?",title:'".fKalTx(KAL_TxGMapOrt)."'":'').'}).addTo(map'.$n.');
  var mapCenter=map'.$n.'.getCenter(); var nF=Math.pow(2,'.$a[4].'); mapCenter.lng+=153.6/nF; mapCenter.lat-=64/nF;
  var tooltip=L.tooltip().setLatLng(mapCenter).setContent(\'Verschieben der Karte mit 2 Fingern!\').addTo(map'.$n.'); if(bDrag) map'.$n.'.closeTooltip(tooltip);
  function onMapAction(e){map'.$n.'.closeTooltip(tooltip);}
  map'.$n.'.on(\'click\',onMapAction); map'.$n.'.on(\'zoomstart\',onMapAction); map'.$n.'.on(\'movestart\',onMapAction);
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,KAL_GMapWarten)+$n).');';
}

function fKalGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(KAL_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(KAL_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fKalTx(KAL_TxGMapOrt)."'".'});':'
  if(GBrowserIsCompatible()){
   map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
   map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
   map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
   map'.$n.'.addControl(new GSmallMapControl());'.(KAL_GMapTypeControl?'
   map'.$n.'.addControl(new GMapTypeControl());':'').'
  }').'
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,KAL_GMapWarten)+$n).');';
}
?>