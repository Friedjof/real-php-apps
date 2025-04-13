<?php
function fMpSeite(){ //Seiteninhalt
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 global $DbO,$aMpDaten,$aMpFN,$aMpFT,$aMpDF,$aMpND,$aMpZS,$aMpAW,$aMpKW,$aMpSW,$aMpLF;

 //Sortierung und Startposition
 $sId=(isset($_GET['mp_Nummer'])?sprintf('%d',$_GET['mp_Nummer']):0); $X=''; $sQ=''; $sGMap='';
 $nSeite=(isset($_GET['mp_Seite'])?(int)$_GET['mp_Seite']:1);
 $n=(isset($_GET['mp_Index'])?(int)$_GET['mp_Index']:MP_ListenIndex); if($n!=MP_ListenIndex) $sQ.='&amp;mp_Index='.$n; //1-Index
 if(isset($_GET['mp_Rueck'])) $sQ.='&amp;mp_Rueck='.fMpRq1($_GET['mp_Rueck']); //2-Rueck
 $sQ.=MP_SuchParam; //3-Suchparameter
 if(isset($_GET['mp_Aendern'])){$sQ.='&amp;mp_Aendern=1';}
 if(isset($_GET['mp_Kopieren'])){$sQ.='&amp;mp_Kopieren=1';}

 if(!$nVorg=$aMpDaten[1]) $nVorg=$sId; $nPos=$aMpDaten[2]; if(!$nNachf=$aMpDaten[3]) $nNachf=$sId;

 //Zusatzzeilen ermitteln
 $mpDetailInfo=(MP_GastDInfo||MP_SessionOK?MP_DetailInfo:-1);
 $mpDetailAendern=(MP_GastDAendern||MP_SessionOK?MP_DetailAendern:-1);
 $mpDetailKopieren=(MP_GastDKopieren||MP_SessionOK?MP_DetailKopieren:-1);
 $mpDetailBenachr=(MP_GastDBenachr||MP_SessionOK?MP_DetailBenachr:-1);
 $mpDetailDrucken=(MP_GastDDrucken||MP_SessionOK?MP_DetailDrucken:-1);

 // Mails als Popup-Fenster
 if((MP_MailPopup||MP_DruckPopup)&&!MP_DetailPopup&&!defined('MP_MpWin')){$X="\n".'<script type="text/javascript">function MpWin(sUrl){mpWin=window.open(sUrl,"mpwin","width='.MP_PopupBreit.',height='.MP_PopupHoch.',left='.MP_PopupX.',top='.MP_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");mpWin.focus();}</script>'; define('MP_MpWin',true);}

 // Navigator vorwaerts/rueckwaerts
 $sNavig=fMpNavigator($nPos,MP_Saetze,$nSeite,$nVorg,$nNachf,$sQ);
 if(MP_DetailNaviOben==1) $X.="\n".$sNavig;

 // Meldung ausgeben
 $X.="\n".(!strpos(MP_Meldung,'mpFehl')?'<p class="mpMeld">'.fMpTx(str_replace('#S',MP_SegName,str_replace('#N',sprintf('%0'.MP_NummerStellen.'d',$aMpDaten[0][0]),MP_TxDetails))).'</p>':MP_Meldung);

 // Navigator ueber der Tabelle
 if(MP_DetailNaviOben==2) $X.="\n".$sNavig;

 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
 if(!$sSes=MP_Session) if(defined('MP_NeuSession')) $sSes=MP_NeuSession; $nNDf=MP_NutzerDetailFeld;
 if(MP_SessionOK){$nNDf=MP_NNutzerDetailFeld; if(MP_NDetailAnders) $aMpDF=$aMpND;}

 //eigene Layoutzeilen pruefen
 if($bEigeneDetails=MP_EigeneDetails){
  if(!$sDtl=@join('',(file_exists(MP_Pfad.'mpDetail'.$sSegNo.'Zeilen.htm')?file(MP_Pfad.'mpDetail'.$sSegNo.'Zeilen.htm'):array(''))))
   $sDtl=@join('',(file_exists(MP_Pfad.'mpDetailZeilen.htm')?file(MP_Pfad.'mpDetailZeilen.htm'):array('')));
  $s=strtolower($sDtl); if(empty($sDtl)||strpos($s,'<body')>0||strpos($s,'<head')>0){$X.="\n".'<p class="mpFehl">'.fMpTx(MP_TxKeinDetail).'</p>'; $bEigeneDetails=false;}
 }
 if(!$bEigeneDetails) $X.="\n\n".'<div class="mpTabl">';

 $a=$aMpDaten[0]; $nFelder=count($aMpFN); $nFarb=1;
 if(count($a)>1){
  for($i=0;$i<$nFelder;$i++){
   if($mpDetailInfo==$i){ //Info-Zeile
    $s='<a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).'"'.(MP_MailPopup&&!MP_DetailPopup?' target="mpwin" onclick="MpWin(this.href)"':'').'><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxInfoSenden).'" alt="'.fMpTx(MP_TxInfoSenden).'" /> '.fMpTx(MP_TxSendInfo).'</a>';
    if(!$bEigeneDetails){ //Standardlayout
     $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxInfoSenden).'</div>';
     $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }else $sDtl=str_replace('{SendInfo}',$s,$sDtl); //eigene Details
   }
   if($mpDetailAendern==$i){ //Aendern-Zeile
    if($a[1]>=date('Y-m-d',time()-86400*MP_BearbAltesNochTage)){
     $sIcn='<img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /> ';
     $sRef=fMpHref('aendern','',$sId,$sQ);
     if((!$n=array_search('u',$aMpFT))||(!MP_SessionOK&&$a[$n]=='0000')) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx(MP_TxInseratAendern).'</a>';
     elseif(MP_SessionOK&&$a[$n]==(int)substr($sSes,0,4)) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx((MP_NAendernFremde?MP_TxInseratEigen.' ':'').MP_TxInseratAendern).'</a>';
     elseif(MP_SessionOK&&MP_NAendernFremde) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx(MP_TxInseratFremd.' '.MP_TxInseratAendern).'</a>';
     else $s='<span title="'.fMpTx(MP_TxNummerFremd).'">---</span>';
    }else $s='<span title="'.fMpTx(MP_TxAendereZuAlt).'">--</span>';
    if(!$bEigeneDetails){ //Standardlayout
     $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxAendern).'</div>';
     $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }else $sDtl=str_replace('{Aendern}',$s,$sDtl); //eigene Details
   }
   if($mpDetailKopieren==$i){ //Kopieren-Zeile
    $s='<a class="mpDetl" href="'.fMpHref('kopieren','',$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /> '.fMpTx(MP_TxInserat.' '.MP_TxKopieren).'</a>';
    if(!$bEigeneDetails){ //Standardlayout
     $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxKopieren).'</div>';
     $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }else $sDtl=str_replace('{Kopieren}',$s,$sDtl); //eigene Details
   }
   if($mpDetailBenachr==$i){ //Benachrichtigungs-Zeile
    $s='<a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).'"'.(MP_MailPopup&&!MP_DetailPopup?' target="mpwin" onclick="MpWin(this.href)"':'').'><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /> '.fMpTx(MP_TxBenachrService).'</a>';
    if(!$bEigeneDetails){ //Standardlayout
     $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxBenachServ).'</div>';
     $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }else $sDtl=str_replace('{Nachricht}',$s,$sDtl); //eigene Details
   }
   if($mpDetailDrucken==$i){ //Drucken-Zeile
    $s='<a class="mpDetl" href="'.fMpHref('drucken',(MP_DruckPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_DruckPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).(MP_DruckPopup?'" target="prwin" onclick="PrWin(this.href)':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconDrucken.gif" title="'.fMpTx(MP_TxDrucken).'" alt="'.fMpTx(MP_TxDrucken).'" /> '.fMpTx(MP_TxInserat.' '.MP_TxDrucken).'</a>';
    if(!$bEigeneDetails){ //Standardlayout
     $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxDrucken).'</div>';
     $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }else $sDtl=str_replace('{Drucken}',$s,$sDtl); //eigene Details
   }
   $t=$aMpFT[$i];
   if(($aMpDF[$i]>0||$bEigeneDetails)&&$t!='p'&&$t!='c'&&substr($aMpFN[$i],0,5)!='META-'&&$aMpFN[$i]!='TITLE'){
    if(($s=$a[$i])||strlen($s)>0){
     switch($t){
      case 't': $s=fMpBB(fMpDt($s)); break; //Text
      case 'm': $s=fMpBB(fMpDt($s)); break; //Memo
      case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie so lassen
      case 'd': case '@': //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3; break;
      case 'z': $s.=' '.fMpTx(MP_TxUhr); break; //Uhrzeit
      case 'w': //Waehrung
       if(((float)$s)!=0||!MP_PreisLeer){
        $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
        if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung;
       }else if(MP_ZeigeLeeres) $s='&nbsp;'; else $s='';
       break;
      case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein);
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if(((float)$s)!=0||!MP_ZahlLeer){
        if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,MP_Tausendzeichen); else $s=str_replace('.',MP_Dezimalzeichen,$s);
       }else if(MP_ZeigeLeeres) $s='&nbsp;'; else $s='';
       break;
      case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); break; //Zaehlnummer
      case 'l': //Link
       $aI=explode('|',$s); $s=$aI[0];
       $v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMpDt($s).'" alt="'.fMpDt($s).'" /> ';
       $s='<a class="mpText" title="'.fMpDt($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(MP_DetailLinkSymbol?'':fMpDt(isset($aI[1])?$aI[1]:$s)).'</a>';
       break;
      case 'e': //eMail
       $s='<a class="mpDetl" href="'.fMpHref('kontakt',(MP_MailPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.'&amp;mp_Eml='.$i.(MP_MailPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup&&!MP_DetailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" title="'.fMpTx(MP_TxKontakt).'" alt="'.fMpTx(MP_TxKontakt).'" /> '.fMpTx(MP_TxKontaktSenden).'</a>';
       break;
      case 's': $w=$s; //Symbol
       $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
       $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
       $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.fMpDt($w).'" alt="'.fMpDt($w).'" />';
       break;
      case 'b': //Bild
       $s=substr($s,strpos($s,'|')+1); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'_'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $w=fMpDt(substr($s,strpos($s,'_')+1,-4));
       $s='<img class="mpBild" src="'.MP_Url.$s.'" style="max-width:'.(isset($aI[0])?$aI[0]:'16').'px;max-height:'.(isset($aI[1])?$aI[1]:'16').'px;" title="'.$w.'" alt="'.$w.'" />';
       break;
      case 'f': //Datei
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fMpDt(strtoupper($w).'-'.MP_TxDatei);
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
       $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'" /> ';
       if(!MP_DetailDateiSymbol) $v.=fMpKurzName($s);
       $s='<a class="mpText" href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$v.'</a>';
       break;
      case 'u': //Benutzer
       if($nId=(int)$s){
        $s=MP_TxAutorUnbekannt;
        if(!MP_SQL){ //Textdaten
         $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
          if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt; elseif($nNDf<5&&$nNDf>1) $s=fMpDeCode($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt;}
          else $s=MP_TxAutorUnbekannt;
        }}
       }else $s=MP_TxAutor0000;
       $s=fMpDt($s); break;
      case 'x': $aI=explode(',',$s); //StreetMap
       if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
        $s='<div class="mpNorm" id="GGeo'.$i.'" style="width:99%;max-width:'.MP_GMapBreit.'px;height:'.MP_GMapHoch.'px;">'.fMpTx(MP_TxGMap1Warten).'<br /><a class="mpText" href="javascript:showMap'.$i.'()" title="'.fMpTx($aMpFN[$i]).'">'.fMpTx(MP_TxGMap2Warten).'</a></div>';
        $sGMap.=(MP_GMapSource=='O'?fMpOMap($i,$aI):fMpGMap($i,$aI));
       }else $s='&nbsp;';
       break;
      case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
     }//switch
    }elseif($t=='b'&&MP_ErsatzBildGross>''){ //keinBild
     $s='grafik/'.MP_ErsatzBildGross; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img class="mpBild" src="'.MP_Url.$s.'" style="max-width:'.(isset($aI[0])?$aI[0]:'16').'px;max-height:'.(isset($aI[1])?$aI[1]:'16').'px;" alt="" />';
    }elseif(MP_ZeigeLeeres) $s='&nbsp;';
    if(!$bEigeneDetails){ //Standardlayout
     if(strlen($s)>0){
      $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
      $X.="\n".' <div class="mpTbSp1">'.fMpDt($aMpFN[$i]).'</div>';
      $X.="\n".' <div class="mpTbSp2" '.($aMpZS[$i]?' style="'.str_replace('`,',';',$aMpZS[$i]).'"':'').'>'.$s."</div>\n</div>";
     }
    }else $sDtl=str_replace('{'.fMpDt($aMpFN[$i]).'}',$s,$sDtl); //eigene Details
   }
  }
  if($mpDetailInfo==$nFelder){ //Info-Zeile
   $s='<a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup&&!MP_DetailPopup?'" target="mpwin" onclick="MpWin(this.href)"':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxInfoSenden).'" alt="'.fMpTx(MP_TxInfoSenden).'" /> '.fMpTx(MP_TxSendInfo).'</a>';
   if(!$bEigeneDetails){ //Standardlayout
    $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxInfoSenden).'</div>';
    $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
   }else $sDtl=str_replace('{SendInfo}',$s,$sDtl); //eigene Details
  }
  if($mpDetailBenachr==$nFelder){ //Benachrichtigungs-Zeile
   $s='<a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup&&!MP_DetailPopup?'" target="mpwin" onclick="MpWin(this.href)"':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /> '.fMpTx(MP_TxBenachrService).'</a>';
   if(!$bEigeneDetails){ //Standardlayout
    $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxBenachServ).'</div>';
    $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
   }else $sDtl=str_replace('{Nachricht}',$s,$sDtl); //eigene Details
  }
  if($mpDetailAendern==$nFelder){ //Aendern-Zeile
   if($a[1]>=date('Y-m-d',time()-86400*MP_BearbAltesNochTage)){
    $sIcn='<img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /> ';
    $sRef=fMpHref('aendern','',$sId,$sQ);
    if((!$n=array_search('u',$aMpFT))||(!MP_SessionOK&&$a[$n]=='0000')) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx(MP_TxInseratAendern).'</a>';
    elseif(MP_SessionOK&&$a[$n]==(int)substr($sSes,0,4)) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx((MP_NAendernFremde?MP_TxInseratEigen.' ':'').MP_TxInseratAendern).'</a>';
    elseif(MP_SessionOK&&MP_NAendernFremde) $s='<a class="mpDetl" href="'.$sRef.'">'.$sIcn.fMpTx(MP_TxInseratFremd.' '.MP_TxInseratAendern).'</a>';
    else $s='<span title="'.fMpTx(MP_TxNummerFremd).'">---</span>';
   }else $s='<span title="'.fMpTx(MP_TxAendereZuAlt).'">--</span>';
   if(!$bEigeneDetails){ //Standardlayout
    $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxAendern).'</div>';
    $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
   }else $sDtl=str_replace('{Aendern}',$s,$sDtl); //eigene Details
  }
  if($mpDetailKopieren==$nFelder){ //Kopieren-Zeile
   $s='<a class="mpDetl" href="'.fMpHref('kopieren','',$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /> '.fMpTx(MP_TxInserat.' '.MP_TxKopieren).'</a>';
   if(!$bEigeneDetails){ //Standardlayout
    $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxKopieren).'</div>';
    $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
   }else $sDtl=str_replace('{Kopieren}',$s,$sDtl); //eigene Details
  }
  if($mpDetailDrucken==$nFelder){ //Drucken-Zeile
   $s='<a class="mpDetl" href="'.fMpHref('drucken',(MP_DruckPopup||MP_DetailPopup?'':$nSeite),$sId,$sQ.(MP_DruckPopup||MP_DetailPopup?'&amp;mp_Popup=1':'')).(MP_DruckPopup?'" target="prwin" onclick="PrWin(this.href)':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconDrucken.gif" title="'.fMpTx(MP_TxDrucken).'" alt="'.fMpTx(MP_TxDrucken).'" />'.fMpTx(MP_TxInserat.' '.MP_TxDrucken).'</a>';
   if(!$bEigeneDetails){ //Standardlayout
    $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $X.="\n".' <div class="mpTbSp1">'.fMpTx(MP_TxDrucken).'</div>';
    $X.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
   }else $sDtl=str_replace('{Drucken}',$s,$sDtl); //eigene Details
  }
 }else{$X.="\n".'<p class="mpFehl">'.fMpTx(MP_TxNummerUngueltig).'</p>'; define('MP_410Gone',true);} // count($a)

 if(!$bEigeneDetails) $X.="\n".'</div>'; //Tabelle
 else $X.="\n".str_replace('{SendInfo}','',str_replace('{Nachricht}','',str_replace('{Aendern}','',str_replace('{Kopieren}','',str_replace('{Drucken}','',$sDtl)))));

 // Navigator unter der Tabelle
 if(MP_DetailNaviUnten==1) $X.="\n".$sNavig;

 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(MP_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.MP_Url.'maps/leaflet.css" />'."\n".$X."\n\n".'<script type="text/javascript" src="'.MP_Url.'maps/leaflet.js"></script>';
  else $X.="\n\n".'<script type="text/javascript" src="'.MP_GMapURL.'"></script>';
  $X.="\n".'<script type="text/javascript">'.$sGMap."\n".'</script>';
 }

 if(MP_CanoLink&&(!MP_DetailPopup||MP_CanoPopup)){
  if(MP_Sef){ //SEF-Dateinamen bilden
   $aMpSpalten=array(); for($j=1;$j<$nFelder;$j++) $aMpSpalten[$aMpLF[$j]]=$j; $nSpalten=count($aMpSpalten);
   $sSefName=MP_SegName; $nTitPos=array_search('TITLE',$aMpFN); $bSefSuche=true;
   if($nTitPos&&isset($a[$nTitPos])&&($sSefName=$a[$nTitPos])) $sSefName=fMpDt(str_replace('„','',str_replace('“','',$sSefName))); //zusaetzliche Titelspalte
   //else for($j=1;$j<$nFelder;$j++) if($bSefSuche) if($aMpFT[$j]=='t'&&$aMpFN[$j]!='META-DES'){ //SEF-Suche alt
   // $bSefSuche=false; $sSefName=fMpDt(str_replace('„','',str_replace('“','',(isset($a[$j])?$a[$j]:''))));
   else for($j=1;$j<$nSpalten;$j++) if($bSefSuche) if($aMpFT[$aMpSpalten[$j]]=='t'){ //SEF-Suche wie bei Sitemap
    $bSefSuche=false; $sSefName=fMpDt(str_replace('„','',str_replace('“','',(isset($a[$aMpSpalten[$j]])?$a[$aMpSpalten[$j]]:''))));
    for($j=strlen($sSefName)-1;$j>=0;$j--) //BB-Code weg
     if(substr($sSefName,$j,1)=='[') if($v=strpos($sSefName,']',$j)) $sSefName=substr_replace($sSefName,'',$j,++$v-$j);
    $sSefName=trim(substr(str_replace("\n",' ',str_replace("\r",'',$sSefName)),0,50));
   }
   if($sSefName=trim(str_replace('.','_',trim(str_replace(':','',str_replace(';','',$sSefName)))))) $sSefName='-'.$sSefName;
  }else $sSefName='';
  $sC=fMpHref('detail','1',$sId.$sSefName,'',MP_Segment,true); /* $p=strrpos($sC,'/'); if(!($p===false)) $sC=substr($sC,$p+1); */ define('MP_Canonical',str_replace('&amp;','&',$sC));
 }
 return $X;
}

function fMpKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fMpNavigator($nPos,$nZahl,$nSeite,$nVorg,$nNach,$sQry){
 $X ="\n".'<div class="mpNavD">';
 $X.="\n".' <div class="mpNavR"><a class="mpDetR" href="'.fMpHref('detail',(MP_DetailPopup?'':$nSeite),$nVorg,$sQry.(MP_DetailPopup?'&amp;mp_Popup=1':'')).'" title="'.fMpTx(MP_TxZumAnfang).'"></a></div>';
 $X.="\n".' <div class="mpNavV"><a class="mpDetV" href="'.fMpHref('detail',(MP_DetailPopup?'':$nSeite),$nNach,$sQry.(MP_DetailPopup?'&amp;mp_Popup=1':'')).'" title="'.fMpTx(MP_TxZumEnde).'"></a></div>';
 $X.="\n".fMpTx(MP_TxInserat).' '.$nPos.'/'.$nZahl;
 $X.="\n".'</div>';
 return $X;
}

function fMpOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');
  var mbAttr=\'Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>\';
  var mbUrl=\'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='.MP_SMapCode.'\';
  var sat=L.tileLayer(mbUrl,{id:\'mapbox/satellite-v9\',tileSize:512,zoomOffset:-1,attribution:mbAttr});
  var osm=L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\',{attribution:\'&copy OpenStreetMap\',maxZoom:19});
  var bDrag=true; if('.(MP_SMap2Finger?'true':'false').') bDrag=!L.Browser.mobile;
  var map'.$n.'=L.map(\'GGeo'.$n.'\',{center:['.sprintf('%.15f,%.15f',$a[0],$a[1]).'],zoom:'.$a[4].(MP_SMap2Finger?',dragging:bDrag,tap:bDrag':'').',scrollWheelZoom:false,layers:[osm]});
  if('.(MP_SMapTypeControl?'true':'false').'){var baseLayers={\'Karte\':osm,\'Satellit\':sat}; var layerControl=L.control.layers(baseLayers).addTo(map'.$n.');}
  var marker=L.marker(['.sprintf('%.15f,%.15f',$a[2],$a[3]).'],{opacity:0.75'.(MP_TxGMapOrt>''?",title:'".MP_TxGMapOrt."'":'').'}).addTo(map'.$n.');
  var mapCenter=map'.$n.'.getCenter(); var nF=Math.pow(2,'.$a[4].'); mapCenter.lng+=153.6/nF; mapCenter.lat-=64/nF;
  var tooltip=L.tooltip().setLatLng(mapCenter).setContent(\'Verschieben der Karte mit 2 Fingern!\').addTo(map'.$n.'); if(bDrag) map'.$n.'.closeTooltip(tooltip);
  function onMapAction(e){map'.$n.'.closeTooltip(tooltip);}
  map'.$n.'.on(\'click\',onMapAction); map'.$n.'.on(\'zoomstart\',onMapAction); map'.$n.'.on(\'movestart\',onMapAction);
 }
 var showTm'.$n.'=window.setInterval(\'showMap'.$n.'()\','.(1000*max(1,MP_GMapWarten)+$n).');';
}

function fMpGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(MP_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(MP_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fMpTx(MP_TxGMapOrt)."'".'});':'
  if(GBrowserIsCompatible()){
   map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
   map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
   map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
   map'.$n.'.addControl(new GSmallMapControl());'.(MP_GMapTypeControl?'
   map'.$n.'.addControl(new GMapTypeControl());':'').'
  }').'
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,MP_GMapWarten)+$n).');';
}

?>