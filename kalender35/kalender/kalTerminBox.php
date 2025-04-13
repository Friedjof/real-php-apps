<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
@include('kalWerte.php');
if(defined('KAL_Version')){
 header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
 if(defined('KAL_WarnMeldungen')&&!KAL_WarnMeldungen) error_reporting(E_ALL ^ E_NOTICE);

 $X=''; $Et=''; $Es='Fehl'; $sQ=''; $sKalQry=''; $bSes=false; $aT=array(); if(strlen(KAL_VBoxTxNr)) $kal_FeldName[0]=KAL_VBoxTxNr;
 $nHtT=(int)date('j'); $nHtM=(int)date('n'); $nHtJ=(int)date('Y'); //heute

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 if(isset($_GET['kal_Termin'])&&($sTId=(int)trim($_GET['kal_Termin']))){
  if($s=(isset($_GET['kal_Monat'])?$_GET['kal_Monat']:'')){$nJhr=(int)substr($s,0,4); $nMon=(int)substr($s,5,2);}
  if($nMon==0||$nJhr<100||$nMon>12){$nMon=$nHtM; $nJhr=$nHtJ;} $sMon=sprintf('%04d-%02d',$nJhr,$nMon); //aktueller Monat
  $sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.KAL_Www;
  foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_') $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
  if(!$sKalSelf=KAL_VBoxLink) $sKalSelf='kalender.php'; if(!strpos($sKalSelf,'?')) $sKalSelf.='?';
  if(substr($sKalSelf,-1,1)=='?') $sKalSelf.=substr($sKalQry.'&amp;',5); else $sKalSelf.=($sKalQry?$sKalQry.'&amp;':'');

  if($sSes=(isset($_GET['kal_Session'])?trim($_GET['kal_Session']):'')){ //Session pruefen
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
  if($bSes) $sSes='&amp;kal_Session='.$sSes; else $sSes='';

  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); if($aT[1]=='1'||KAL_AendernLoeschArt==3&&$aT[1]=='3') array_splice($aT,1,1); else $aT=array(); break;}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'" AND (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').')')){
    if($aT=$rR->fetch_row()) array_splice($aT,1,1); $rR->close();
   }else $Et=KAL_TxSqlFrage;
  }else $Et=$SqE;//SQL

  if(count($aT)>0){
   $nInfo=   (KAL_VBoxGastInfo||$bSes?KAL_VBoxInfo:-1);  //Zusatzzeilen ermitteln
   $nErinn=  (KAL_VBoxGastErinn||$bSes?KAL_VBoxErinn:-1);
   $nBenachr=(KAL_VBoxGastBenachr||$bSes?KAL_VBoxBenachr:-1);
   $nDrucken=(KAL_VBoxGastDruck||$bSes?KAL_VBoxDruck:-1);
   $nICal=   (KAL_VBoxGastICal||$bSes?KAL_VBoxICal:-1);
   $nZusage= (KAL_ZusageSystem&&(KAL_VBoxGastZusage||$bSes)?KAL_VBoxZusage:-1);
   $nZusagZ= (KAL_ZusageSystem&&(KAL_VBoxGastZusagZ||$bSes)?KAL_VBoxZusagZ:-1);

   //1-Klick-Zusagen
   $bEinKlickZusage=(KAL_ZusageSystem&&KAL_EinKlickLZusage&&$bSes); // if($bEinKlickZusage) include KAL_Pfad.'kalZusage1Klick.php';

   //eventuell Zusagedaten holen
   $bNichtZugesagt=true; $nZusagAktZ=0;
   if(KAL_ZusageSystem&&($nZusage>0||$nZusagZ>0)){
    $nNId=(int)substr($sSes,17,4);
    $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageAnzahlPos=array_search('ANZAHL',$kal_ZusageFelder);
    if(!KAL_SQL){
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $n=count($aD); $m=max(9,$nZusageAnzahlPos+2);
     for($i=1;$i<$n;$i++){
      $a=explode(';',$aD[$i],$m);
      if($a[1]==$sTId){
       if($bSes&&isset($a[7])&&(int)$a[7]==$nNId) $bNichtZugesagt=false;
       if($nZusageAnzahlPos>0) if($a[6]=='1'||!KAL_ZaehleAktiveZusagen) $nZusagAktZ+=(int)$a[$nZusageAnzahlPos];
      }
     }
    }elseif($DbO){//SQL
     if($rR=$DbO->query('SELECT nr,termin,aktiv,benutzer'.($nZusageAnzahlPos>0?',dat_'.$nZusageAnzahlPos:'').' FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'"')){
      while($a=$rR->fetch_row()){
       if($bSes&&(int)$a[3]==$nNId) $bNichtZugesagt=false;
       if($nZusageAnzahlPos>0) if($a[2]=='1'||!KAL_ZaehleAktiveZusagen) $nZusagAktZ+=(int)$a[4];
      }
      $rR->close();
     }
    }
   }

   if(KAL_VBoxEigenesLayout) if(!$Dtl=(file_exists(KAL_Pfad.'kalTerminBox.htm')?join('',file(KAL_Pfad.'kalTerminBox.htm')):'')) $Dtl='<p style="color:red;font-weight:bold;">'.fKalTx(str_replace('#','kalTerminBox.htm',KAL_TxKeinDetail)).'</p>';
   $nAlleFlds=count($kal_FeldName); $nKapPos=array_search('KAPAZITAET',$kal_FeldName);
   for($i=1;$i<$nAlleFlds;$i++) for($j=1;$j<4;$j++) if(isset($_GET['kal_'.$i.'F'.$j])) $sQ.='&amp;kal_'.$i.'F'.$j.'='.rawurlencode(fKalRq($_GET['kal_'.$i.'F'.$j]));
   $aFlds=explode(',',(!$bSes?KAL_VBoxFelder:KAL_VBoxNFelder)); $bMitId=$aFlds[0]>0; asort($aFlds); $aFlds=array_flip($aFlds);
   $nFlds=count($aFlds); $aLnks=explode(',',KAL_VBoxLinkFld); $aStil=explode(',',KAL_VBoxFldStil); $nCss=1;
   if(!KAL_VBoxEigenesLayout){ //Standardlayout
    if($bMitId){
     $X.='<div class="kalTbZl'.$nCss.'"><div class="kalTbSp1">'.fKalTx($kal_FeldName[0]).'</div><div class="kalTbSp2"'.($aStil[0]?' style="'.$aStil[0].'"':'').'>'.sprintf('%0'.KAL_VBoxNrStellen.'d',$aT[0]).'</div></div>';
     if(--$nCss<=0) $nCss=2;
    }
   }else $Dtl=str_replace('{Nummer}',sprintf('%0'.KAL_VBoxNrStellen.'d',$aT[0]),$Dtl); //eigene Details
   for($i=1;$i<$nAlleFlds;$i++){
    if($nInfo==$i){ //Info-Zeile
     $s='<a class="kalDetl" href="'.(KAL_MailPopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=info'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sTId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/iconInfo.gif" title="'.fKalTx(KAL_TxInfoSenden).'" alt="'.fKalTx(KAL_TxInfoSenden).'"> '.fKalTx(KAL_TxSendInfo).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxInfoSenden).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{SendInfo}',$s,$Dtl); //eigene Details
    }
    if($nErinn==$i){ //Erinnerungs-Zeile
     $s='<a class="kalDetl" href="'.(KAL_MailPopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=erinnern'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sTId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/iconErinnern.gif" title="'.fKalTx(KAL_TxErinnService).'" alt="'.fKalTx(KAL_TxErinnService).'"> '.fKalTx(KAL_TxErinnService).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxErinServ).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{Erinnern}',$s,$Dtl); //eigene Details
    }
    if($nBenachr==$i){ //Benachrichtigungs-Zeile
     $s='<a class="kalDetl" href="'.(KAL_MailPopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=nachricht'.$sSes.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sTId.$sQ.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/iconNachricht.gif" title="'.fKalTx(KAL_TxBenachrService).'" alt="'.fKalTx(KAL_TxBenachrService).'"> '.fKalTx(KAL_TxBenachrService).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxBenachServ).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{Nachricht}',$s,$Dtl); //eigene Details
    }
    if($nDrucken==$i){ //Drucken-Zeile
     if(KAL_DruckPopup) $s='<a class="kalDetl" href="'.$sKalHttp.'kalender.php?kal_Aktion=drucken'.$sSes.'&amp;kal_Nummer='.$sTId.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;"><img class="kalIcon" src="'.$sKalHttp.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
     else $s='<a class="kalDetl" href="'.$sKalSelf.'kal_Aktion=drucken'.$sSes.'&amp;kal_Nummer='.$sTId.$sQ.'"><img class="kalIcon" src="'.$sKalHttp.'grafik/iconDrucken.gif" title="'.fKalTx(KAL_TxDrucken).'" alt="'.fKalTx(KAL_TxDrucken).'"> '.fKalTx(KAL_TxTermin.' '.KAL_TxDrucken).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxDrucken).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{Drucken}',$s,$Dtl); //eigene Details
    }
    if($nICal==$i){ //iCal-Export-Zeile
     $s='<a class="kalDetl" href="'.(KAL_CalPopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=export'.$sSes.(KAL_CalPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sTId.$sQ.'"'.(KAL_CalPopup?' target="expwin" onclick="ExpWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/iconExport.gif" title="'.fKalTx(KAL_TxCalIcon).'" alt="'.fKalTx(KAL_TxCalIcon).'"> '.fKalTx(KAL_TxCalIcon).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxCalZeile).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{Export}',$s,$Dtl); //eigene Details
    }
    if($nZusage==$i){ //Zusage-Zeile
     $s='<a class="kalDetl" href="'.(KAL_ZusagePopup&&!$bEinKlickZusage?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion='.($bEinKlickZusage?'monat&amp;kal_Monat='.$sMon:'zusagen').$sSes.(KAL_ZusagePopup&&!$bEinKlickZusage?'&amp;kal_Popup=1':'').'&amp;kal_'.($bEinKlickZusage?'KlickZusage=':'Nummer=').$sTId.$sQ.'"'.(KAL_ZusagePopup&&!$bEinKlickZusage?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/icon'.($bNichtZugesagt?'Zusage':'Zugesagt').'.gif" title="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'" alt="'.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'"> '.fKalTx($bNichtZugesagt?KAL_TxZusageIcon:KAL_TxZugesagtIcon).'</a>';
     if(KAL_MonatZeigeZusage&&($sSes||KAL_GastMZeigeZusage)) $s.=', <a class="kalDetl" href="'.(KAL_ZusagePopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=zusagezeigen'.$sSes.(KAL_ZusagePopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sTId.$sQ.'"'.(KAL_ZusagePopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.$sKalHttp.'grafik/iconVorschau.gif" title="'.fKalTx(KAL_TxZeigeZusageIcon).'" alt="'.fKalTx(KAL_TxZeigeZusageIcon).'"> '.fKalTx(KAL_TxZeigeZusageIcon).'</a>';
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_TxZusageZeile).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }else $Dtl=str_replace('{Zusagen}',$s,$Dtl); //eigene Details
    }

    if($nZusagZ==$i){
     if($nZusagKapZ=($nKapPos>0?(isset($aT[$nKapPos])?(int)$aT[$nKapPos]:0):0)){
      $s=fKalTx(str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',max($nZusagKapZ-$nZusagAktZ,0),KAL_VBoxTxZusagZMuster))));
     }elseif(KAL_MonZusagZErsatz) $s=fKalTx(KAL_MonZusagZErsatz); else $s='';
     if(!KAL_EigeneDetails){ //Standardlayout
      if(strlen($s)>0||KAL_VBoxLeeres){
       $X.="\n".'<div class="kalTbZl'.$nCss.'">'; if(--$nCss<=0) $nCss=2;
       $X.="\n".' <div class="kalTbSp1">'.fKalTx(KAL_VBoxTxZusagZ).'</div>';
       $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
      }
     }else $Dtl=str_replace('{ZusageZahl}',$s,$Dtl); //eigene Details
    }
    if($i<$nFlds){ //ueber alle ausgewaehlten Felder
     $k=($i>0?$aFlds[$i]:0); $s=str_replace('`,',';',$aT[$k]); $t=$kal_FeldType[$k];
     if(strlen($s)){
      switch($t){
      case 't': case 'm': case 'g': $s=fKalBB(fKalTx($s)); break; //Text/Memo//Gastkommentar
      case 'a': case 'k': case 'o': $s=fKalTx($s); break; //Aufzaehlung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       if(KAL_MonatDLang>0&&$t=='d'){$aMonate=explode(';',';'.(KAL_MonatDLang==2?KAL_TxLMonate:KAL_TxKMonate)); $s2=fKalTx($aMonate[(int)$s2]);}
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MonatDLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.',' '.$s2.' ',$s);
        if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).' '.$s; elseif(KAL_MitWochentag==2) $s.=' '.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
       }elseif($w) $s.=' '.$w;
       break;
      case 'z': $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
      case 'w': //Waehrung
       if($s>0||!KAL_PreisLeer){
        $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.=' '.KAL_Waehrung;
       }else if(KAL_VBoxLeeres) $s=' '; else $s='';
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein);
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if(((float)$s)!=0||!KAL_ZahlLeer){
        if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       }else if(KAL_VBoxLeeres) $s=' '; else $s='';
       break;
      case 'i': $s=sprintf('%0'.KAL_VBoxNrStellen.'d',$s); break; //Zaehlnummer
      case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $v=fKalTx(isset($aI[1])?$aI[1]:$w);
        $s.='<img class="kalIcon" src="'.$sKalHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" alt="'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'"> '.(!KAL_DetailLinkSymbol?$v.', ':'  ');
       }$s=substr($s,0,-2); break;
      case 'e': //eMail
       $s='<img class="kalIcon" src="'.$sKalHttp.'grafik/mail.gif" alt="Mail"> '.(KAL_DruckDMailOffen?' '.(KAL_SQL?$s:fKalDeCode($s)):'');
       break;
      case 's': $w=$s; //Symbol
       $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
       $s='<img src="'.$sKalHttp.$s.'" '.$aI[3].' style="border:0" alt="Icon">';
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.abs($sTId).'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
       $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
       if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
       $w=fKalTx(substr($s,strpos($s,'-')+1,-4));
       $s='<div'.$r.'><img src="'.$sKalHttp.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'"></div>';
       break;
      case 'f': //Datei
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
       $v='<img class="kalIcon" src="'.$sKalHttp.'grafik/datei'.$v.'.gif" alt="Icon"> ';
       if(!KAL_DetailDateiSymbol) $v.=fKalKurzName($s);
       $s=$v;
       break;
      case 'u': //Benutzer
       if($nUId=(int)$s){
        $s=KAL_TxAutorUnbekannt;
        if(!KAL_SQL){ //Textdaten
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nUId.';'; $p=strlen($v);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
          if(!$s=$aN[$kal_DetailFeld[$k]]) $s=KAL_TxAutorUnbekannt; elseif($kal_DetailFeld[$k]<5&&$kal_DetailFeld[$k]>1) $s=fKalDeCode($s); $s=fKalTx($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nUId.'"')){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){
           array_splice($aN,1,1); if(!$s=$aN[$kal_DetailFeld[$k]]) $s=KAL_TxAutorUnbekannt;
          }else $s=KAL_TxAutorUnbekannt;
       }}}else $s=KAL_TxAutor0000;
       break;
      case 'x': $s='Karte'; break;
      case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
      }//switch
     }elseif(KAL_VBoxLeeres) $s=' ';
     $sFN=str_replace('`,',';',$kal_FeldName[$k]);
     if($sFN=='KAPAZITAET'){
      if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if($s>'0') $s=(int)$s;
     }elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     if($aLnks[$k]>0&&$s>' '){ //DetailLink
      $s='<a class="kalDetl" href="'.(KAL_DetailPopup?$sKalHttp.'kalender.php?':$sKalSelf).'kal_Aktion=detail&amp;kal_Nummer='.$sTId.$sSes.$sQ.'&amp;kal_Intervall=%5B%5D'.(KAL_DetailPopup?'&amp;kal_Popup=1" target="kalwin" onclick="KalWin(this.href);return false;':'').'">'.$s.'</a>';
     }
     if(!KAL_VBoxEigenesLayout){ //Standardlayout
      if(strlen($s)>0||KAL_VBoxLeeres){
       $X.='<div class="kalTbZl'.$nCss.'"><div class="kalTbSp1">'.fKalTx($sFN).'</div><div class="kalTbSp2"'.($aStil[$k]?' style="'.$aStil[$k].'"':'').'>'.$s.'</div></div>';
       if(--$nCss<=0) $nCss=2;
      }
     }else $Dtl=str_replace('{'.$kal_FeldName[$k].'}',$s,$Dtl); //eigene Details
    } //$i<$nFlds
   } //for $i
  }else $Et=str_replace('#',$sTId,KAL_TxKeinDatensatz);
 }else $Et=KAL_TxNummerUnbek;
 if($Et) echo "\n".'<p style="color:red;">'.fKalTx($Et).'</p>';
 if(!KAL_VBoxEigenesLayout){ //Standardlayout
?>
<div class="kalTabl">
<?php echo $X; ?>
</div>
<?php
 }else{ //Eigenes Layout
  $Dtl=str_replace('{Aendern}','',str_replace('{Kopieren}','',str_replace('{Nachricht}','',str_replace('{Erinnern}','',$Dtl))));
  $Dtl=str_replace('{SendInfo}','',str_replace('{Export}','',str_replace('{Drucken}','',str_replace('{ZusageZahl}','',str_replace('{Zusagen}','',$Dtl)))));
  echo "\n".$Dtl;
 }
}else echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';

function fKalTx($s){ //TextKodierung
 if(KAL_Zeichensatz!=2) $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1'); else $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',$s));
 $s=str_replace('"','&quot;',str_replace(chr(150),'-',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s)))));
 return str_replace('\n ','<br>',$s);
}
function fKalRq($sTx){ //Eingaben reinigen
 return stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
}
function fKalBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br>',str_replace("\n ",'<br>',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'',$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'',$p,7);
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
 }return $v;
}
function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}
?>