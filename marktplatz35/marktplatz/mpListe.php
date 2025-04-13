<?php
function fMpSeite(){ //Seiteninhalt
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 global $DbO,$aMpDaten,$aMpSpalten,$aMpFN,$aMpFT,$aMpLF,$aMpNL,$aMpOF,$aMpLK,$aMpSS,$aMpAW,$aMpKW,$aMpSW;

 //Sortierung und Startposition
 $nIndex=(isset($_GET['mp_Index'])?(int)$_GET['mp_Index']:((isset($_POST['mp_Index'])&&strlen($_POST['mp_Index'])>0)?(int)$_POST['mp_Index']:MP_ListenIndex));
 $sRueckw=(isset($_GET['mp_Rueck'])?fMpRq1($_GET['mp_Rueck']):(isset($_POST['mp_Rueck'])?fMpRq1($_POST['mp_Rueck']):''));
 $nSeite=(isset($_GET['mp_Seite'])?(int)$_GET['mp_Seite']:1);

 //Query_Strings fuer Links vorbereiten
 $X=''; $sQ=''; if($nIndex!=MP_ListenIndex) $sQ.='&amp;mp_Index='.$nIndex;
 if($sRueckw=='1'&&($nIndex!=1||!MP_Rueckwaerts)) $sQ.='&amp;mp_Rueck=1';
 elseif($sRueckw==='0'&&MP_Rueckwaerts&&$nIndex==1) $sQ.='&amp;mp_Rueck=0';
 $sQ.=MP_SuchParam;

  //Zusatzspalten ermitteln
 $mpListenInfo=(MP_GastLInfo||MP_SessionOK?MP_ListenInfo:-1);
 $mpListenAendern=(MP_GastLAendern||MP_SessionOK?MP_ListenAendern:-1);
 $mpListenKopieren=(MP_GastLKopieren||MP_SessionOK?MP_ListenKopieren:-1);
 $mpListenBenachr=(MP_GastLBenachr||MP_SessionOK?MP_ListenBenachr:-1);
 if(isset($_GET['mp_Aendern'])){$sQ.='&amp;mp_Aendern=1'; $mpListenAendern=max(0,$mpListenAendern);}
 if(isset($_GET['mp_Kopieren'])){$sQ.='&amp;mp_Kopieren=1'; $mpListenKopieren=max(0,$mpListenKopieren);}

 // Detail als Popup-Fenster
 if((MP_MailPopup||MP_DetailPopup)&&!defined('MP_MpWin')){$X="\n".'<script type="text/javascript">function MpWin(sURL){mpWin=window.open(sURL,"mpwin","width='.MP_PopupBreit.',height='.MP_PopupHoch.',left='.MP_PopupX.',top='.MP_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");mpWin.focus();}</script>'; define('MP_MpWin',true);}

 // Navigation, Schnellsuche
 if(MP_NaviOben>0||MP_NaviUnten>0) $sNavig=fMpNavigator($nSeite,MP_Saetze,$sQ);
 if(MP_SuchFilter>0) $sSuchFlt=fMpSuchFilter(isset($_GET['mp_Such'])?fMpRq($_GET['mp_Such']):(isset($_POST['mp_Such'])?fMpRq($_POST['mp_Such']):''),MP_Segment,$sQ,(MP_SuchFilter%2?'L':'R'));
 if(MP_SuchFilter==1||MP_SuchFilter==2) $X.="\n".$sSuchFlt;
 if(MP_NaviOben==1&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchFilter==3||MP_SuchFilter==4) $X.="\n".$sSuchFlt;

  // Meldung ausgeben
 $X.="\n".str_replace('#N',MP_Saetze,str_replace('#I',count($aMpDaten),MP_Meldung));

 if(MP_SuchFilter==5||MP_SuchFilter==6) $X.="\n".$sSuchFlt;
 if(MP_NaviOben==2&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchFilter==7||MP_SuchFilter==8) $X.="\n".$sSuchFlt;

 //eigene Layoutzeile pruefen
 $sLay=(defined('MP_Layout')?MP_Layout:'x');
 if($bEigeneZeilen=(MP_EigeneZeilen||$sLay=='e')&&($sLay!='t')&&(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm')||file_exists(MP_Pfad.'mpListenZeile.htm'))){
  if(!$sEigeneZeile=@implode('',(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm')?file(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm'):array(''))))
   $sEigeneZeile=@implode('',(file_exists(MP_Pfad.'mpListenZeile.htm')?file(MP_Pfad.'mpListenZeile.htm'):array('')));
  $s=strtolower($sEigeneZeile); if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }
 /*
 if($bEigeneZeilen=MP_EigeneZeilen){
  if(!$sEigeneZeile=@implode('',(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm')?file(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm'):array(''))))
   $sEigeneZeile=@implode('',(file_exists(MP_Pfad.'mpListenZeile.htm')?file(MP_Pfad.'mpListenZeile.htm'):array('')));
  $s=strtolower($sEigeneZeile); if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 } */

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#');
 if(($n=array_search('u',$aMpFT))&&$aMpLF[$n]>0){
  if(!MP_SQL){ //Textdaten
   if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fMpDeCode($a[2]); $a[4]=fMpDeCode($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;}
    $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $mpNLF=(MP_SessionOK?MP_NNutzerListFeld:MP_NutzerListFeld);
 }

 //Daten ausgeben: $i-Index, $j-Spalte, $k-Feld
 $a=array(); $nSpalten=count($aMpSpalten); $bMitID=$aMpLF[0]>0; $aSpTitle=array();
 $X.="\n\n".'<div class="mpTab'.(!$bEigeneZeilen?'l':'L').'">'; //Tabelle

 //Kopfzeile ausgeben
 if(!$bEigeneZeilen){ //Standardlayout
  $X.="\n".' <div class="mpTbZl0">'; //Zeile
  if($mpListenAendern==0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenAendTitel?fMpTx(MP_ListenAendTitel):'&nbsp;').'</div>';
  if($mpListenKopieren==0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenKopieTitel?fMpTx(MP_ListenKopieTitel):'&nbsp;').'</div>';
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){
   if($mpListenInfo==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</div>';
   if($mpListenAendern==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenAendTitel?fMpTx(MP_ListenAendTitel):'&nbsp;').'</div>';
   if($mpListenKopieren==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenKopieTitel?fMpTx(MP_ListenKopieTitel):'&nbsp;').'</div>';
   if($mpListenBenachr==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</div>';
   $k=$aMpSpalten[$j];
   if(!$aMpOF[$k]) $t='';
   else{
    if($k!=$nIndex){$t='e'; $w=''; $v='';} // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
    else{
     if($sRueckw!='1'&&!($nIndex==MP_ListenIndex&&MP_Rueckwaerts&&strlen($sRueckw)<=0)){
      $t='t'; $w=MP_TxAbsteigend;
      if($sRueckw==='0'&&MP_Rueckwaerts&&$nIndex==MP_ListenIndex) $v=''; else $v='&amp;mp_Rueck=1';
     }else{$t='r'; $w=MP_TxAufsteigend; $v=''; if($nIndex==MP_ListenIndex&&MP_Rueckwaerts) $v='&amp;mp_Rueck=0';}
    }
    $t='<img class="mpSorti" src="'.MP_Url.'grafik/sortier'.$t.'.gif" title="'.fMpTx($w.MP_TxSortieren).'" alt="'.fMpTx($w.MP_TxSortieren).'" />';
    $t='&nbsp;<a class="mpDetl" href="'.fMpHref('liste','','',($k!=MP_ListenIndex?'&amp;mp_Index='.$k:'').$v.MP_SuchParam).'">'.$t.'</a>';
   }
   $sFS=$aMpFT[$k]; if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M';
   $X.="\n".'  <div class="mpTbLst mpTbLs'.$sFS.'">'.fMpDt($aMpFN[$k]).$t.'</div>'; $aSpTitle[$k]=fMpTx($aMpFN[$k]).$t;
  }
  if($mpListenInfo>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</div>';
  if($mpListenAendern>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenAendTitel?fMpTx(MP_ListenAendTitel):'&nbsp;').'</div>';
  if($mpListenKopieren>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenKopieTitel?fMpTx(MP_ListenKopieTitel):'&nbsp;').'</div>';
  if($mpListenBenachr>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</div>';
  $X.="\n".' </div>'; // Kopfzeile
 }else{ //eigene Kopfzeile
  if(!$r=@implode('',(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Kopf.htm')?file(MP_Pfad.'mpListen'.$sSegNo.'Kopf.htm'):array(''))))
   $r=@implode('',(file_exists(MP_Pfad.'mpListenKopf.htm')?file(MP_Pfad.'mpListenKopf.htm'):array('')));
  $p=0; $s=strtolower($r); if(strpos($s,'<body')||strpos($s,'<head')) $r='';
  if($r){
   while($p=strpos($r,'{',$p+1)) if($i=strpos($r,'}',$p+1)){
    $v=substr($r,$p+1,$i-($p+1));
    if($k=array_search($v,$aMpFN)){
     if($k!=$nIndex){$t='e'; $w=''; $v='';} // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
     else{
      if($sRueckw!='1'&&!($nIndex==MP_ListenIndex&&MP_Rueckwaerts&&strlen($sRueckw)<=0)){
       $t='t'; $w=MP_TxAbsteigend;
       if($sRueckw==='0'&&MP_Rueckwaerts&&$nIndex==MP_ListenIndex) $v=''; else $v='&amp;mp_Rueck=1';
      }else{$t='r'; $w=MP_TxAufsteigend; $v=''; if($nIndex==MP_ListenIndex&&MP_Rueckwaerts) $v='&amp;mp_Rueck=0';}
     }
     $t='<img class="mpSorti" src="'.MP_Url.'grafik/sortier'.$t.'.gif" title="'.fMpTx($w.MP_TxSortieren).'" alt="'.fMpTx($w.MP_TxSortieren).'" />';
     $t='&nbsp;<a class="mpDetl" href="'.fMpHref('liste','','',($k!=MP_ListenIndex?'&amp;mp_Index='.$k:'').$v.MP_SuchParam).'">'.$t.'</a>';
    }else $t='';
    $r=substr_replace($r,$t,$p,$i-$p+1);
   }
   $X.="\n".' <div class="mpTbZL0">'."\n".$r."\n </div>";
  }
 }

 //alle Datenzeilen ausgeben
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
 $nKatPos=array_search('k',$aMpFT); $nFarb=1;
 if($mpListenAendern>=0){if(!$nDatPos=array_search('1',$aMpSpalten)) $nDatPos=-1; $sAendDat=date('Y-m-d',time()-86400*MP_BearbAltesNochTage);}
 foreach($aMpDaten as $a){
  $sZl=''; $sId=$a[0]; $sZS=$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
  if($nKatPos>0) if(isset($a[$nSpalten])) if($j=$a[$nSpalten]) $sZS.=' mpLstKat'.$j; //Kategorie aus Zusatzspalte
  if(MP_Sef){ //SEF-Dateinamen bilden
   $sSefName=MP_SegName; $bSefSuche=true;
   if(isset($a[-2])) $sSefName=fMpDt(str_replace('„','',str_replace('“','',$a[-2]))); //zusaetzliche Titelspalte
   else for($j=1;$j<$nSpalten;$j++) if($bSefSuche) if($aMpFT[$aMpSpalten[$j]]=='t'){ //SEF-Suchen
    $bSefSuche=false; $sSefName=fMpDt(str_replace('„','',str_replace('“','',$a[$j])));
    for($j=strlen($sSefName)-1;$j>=0;$j--) //BB-Code weg
     if(substr($sSefName,$j,1)=='[') if($v=strpos($sSefName,']',$j)) $sSefName=substr_replace($sSefName,'',$j,++$v-$j);
    $sSefName=trim(substr(str_replace("\n",' ',str_replace("\r",'',$sSefName)),0,50));
   }
   if($sSefName=trim(str_replace('.','_',trim(str_replace(':','',str_replace(';','',$sSefName)))))) $sSefName='-'.$sSefName;
  }else $sSefName='';
  if(!$bEigeneZeilen){ //Standardlayout
   if($mpListenAendern==0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(((int)$sId>0?MP_TxAendern:MP_TxAendereVmk)?fMpTx((int)$sId>0?MP_TxAendern:MP_TxAendereVmk):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="mpDetl" href="'.fMpHref('aendern',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /></a>':'&nbsp;').'</div>';
   if($mpListenKopieren==0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_TxKopieren?fMpTx(MP_TxKopieren):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('kopieren',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /></a></div>';
  }else $sZl=$sEigeneZeile; //eigenes Zeilenlayout
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aMpSpalten[$j]; $t=$aMpFT[$k]; $sStil=''; $sFS=''; $sKMemo='';
   if(!$bEigeneZeilen){ //Standardlayout
    if($mpListenInfo==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a></div>';
    if($mpListenAendern==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenAendTitel?fMpTx(MP_ListenAendTitel):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="mpDetl" href="'.fMpHref('aendern',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /></a>':'&nbsp;').'</div>';
    if($mpListenKopieren==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenKopieTitel?fMpTx(MP_ListenKopieTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('kopieren',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /></a></div>';
    if($mpListenBenachr==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a></div>';
   }
   if(($s=$a[$j])||strlen($s)>0){
    switch($t){
     case 't': $s=fMpBB(fMpDt($s)); break; //Text
     case 'm': if(MP_ListenMemoLaenge==0) $s=fMpBB(fMpDt($s)); else $s=fMpBB(fMpKurzMemo(fMpDt($s),MP_ListenMemoLaenge)); break; //Memo
     case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie/Postleitzahl
     case 'd': case '@': //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; break;
     case 'z': $sFS.=' mpTbLsM'; break; //Uhrzeit
     case 'w': //Waehrung
      if(((float)$s)!=0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sFS.=' mpTbLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein); $sFS.=' mpTbLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if(((float)$s)!=0||!MP_ZahlLeer){
       if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,MP_Tausendzeichen); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sFS.=' mpTbLsR';
      }else $s='&nbsp;';
      break;
     case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); $sFS.=' mpTbLsM'; break; //Nummer
     case 'l': //Link
      $aI=explode('|',$s); $s=$aI[0]; $v=fMpDt(isset($aI[1])?$aI[1]:$s);
      if(MP_LinkSymbol){$v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMpDt($s).'" alt="'.fMpDt($s).'" />'; $sFS.=' mpTbLsM';}
      $s='<a class="mpText" title="'.fMpDt($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.'</a>';
      break;
     case 'e': //EMail
      $s='<a class="mpText" href="'.fMpHref('kontakt',(MP_MailPopup?'':$nSeite),$sId,$sQ.'&amp;mp_Eml='.$k.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" title="'.fMpTx(MP_TxKontakt).'" alt="'.fMpTx(MP_TxKontakt).'" /></a>';
       $sFS.=' mpTbLsM';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=MP_TxAutorUnbekannt;
       for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
        if(!$s=$aNutzer[$n][$mpNLF]) $s=MP_TxAutorUnbekannt;
        break;
      }}else $s=MP_TxAutor0000;
      $s=fMpDt($s); break;
     case 's': $w=$s; //Symbol
      $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
      $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.fMpDt($w).'" />';  $sFS.=' mpTbLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
      $ho=floor((MP_VorschauHoch-$aI[1])*0.5); $hu=max(MP_VorschauHoch-($aI[1]+$ho),0);
      if(!MP_VorschauRahmen) $r=' class="mpTBld"'; else $r=' class="mpVBld" style="width:'.MP_VorschauBreit.'px;text-align:center;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $s='<div'.$r.'><img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.substr($s,strpos($s,'/')+1).'" title="'.substr($s,strpos($s,'/')+1).'" /></div>'; $sFS.=' mpTbLsM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fMpDt(strtoupper($w).'-'.MP_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' mpTbLsM';
      $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'" />';
      $s='<a class="mpText" href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&MP_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildKlein; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbLsM';
   }else $s='&nbsp;';
   if(($w=$aMpSS[$k])){$sStil=' style="'.str_replace('`,',';',$w).'"';}
   if($aMpLK[$k]>0||$t=='b') $s='<a class="mpDetl" href="'.fMpHref('detail',(MP_DetailPopup?'':$nSeite),$sId.$sSefName,$sQ.(MP_DetailPopup?'&amp;mp_Popup=1':'')).'" title="'.fMpTx(MP_TxDetail).(MP_DetailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'">'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="mpTbLst'.$sFS.'"'.$sStil.'><span class="mpTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$aMpFN[$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if(!$bEigeneZeilen){ //Standardlayout
   if($mpListenInfo>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a></div>';
   if($mpListenAendern>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenAendTitel?fMpTx(MP_ListenAendTitel):'&nbsp;').'</span>'.($a[$nDatPos]>=$sAendDat?'<a class="mpDetl" href="'.fMpHref('aendern',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /></a>':'&nbsp;').'</div>';
   if($mpListenKopieren>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenKopieTitel?fMpTx(MP_ListenKopieTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('kopieren',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /></a></div>';
   if($mpListenBenachr>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</span><a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a><div>';
   $X.="\n".' <div class="mpTbZl'.$sZS.'">'.$sZl."\n".' </div><div class="mpTbZlX"></div>';
  }else{ //eigenes Layout
   $sZl=str_replace('{Nummer}',($bMitID?(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId):''),$sZl);
   $sZl=str_replace('{SendInfo}',($mpListenInfo>=0?'<a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a>':''),$sZl);
   $sZl=str_replace('{Aendern}',($mpListenAendern>=0&&$a[$nDatPos]>=$sAendDat?'<a class="mpDetl" href="'.fMpHref('aendern',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconBearbeiten.gif" title="'.fMpTx(MP_TxAendern).'" alt="'.fMpTx(MP_TxAendern).'" /></a>':''),$sZl);
   $sZl=str_replace('{Kopieren}',($mpListenKopieren>=0?'<a class="mpDetl" href="'.fMpHref('kopieren',$nSeite,$sId,$sQ).'"><img class="mpIcon" src="'.MP_Url.'grafik/iconKopieren.gif" title="'.fMpTx(MP_TxKopieren).'" alt="'.fMpTx(MP_TxKopieren).'" /></a>':''),$sZl);
   $sZl=str_replace('{Nachricht}',($mpListenBenachr>=0?'<a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':'')).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a>':''),$sZl);
   $X.="\n".' <div class="mpTbZL'.$sZS.'">'."\n".$sZl."\n".' </div><div class="mpTbZLX"></div>';
  }
 }
 $X.="\n".'</div>'; // Tabelle
 //Navigator unter der Tabelle
 if(MP_SuchFilter==9||MP_SuchFilter==10) $X.="\n".$sSuchFlt;
 if(MP_NaviUnten==1&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchFilter==11||MP_SuchFilter==12) $X.="\n".$sSuchFlt;
 if(MP_CanoLink){$sC=fMpHref('liste',$nSeite,'','',MP_Segment,true); /* $p=strrpos($sC,'/'); if(!($p===false)) $sC=substr($sC,$p+1); */ define('MP_Canonical',str_replace('&amp;','&',$sC));}
 return $X;
}

function fMpSuchFilter($s,$sSeg,$sPar,$sAlign){ //Schnellsuchfilter zeichnen
if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
if($p=strpos('#'.$sPar,'&amp;mp_Such=')){
 if(!$e=strpos($sPar,'&amp;',$p+8)) $e=strlen($sPar); $sPar=substr_replace($sPar,'',--$p,$e-$p);
}
return '
<div class="mpFilt">
 <div class="mpSFlt'.$sAlign.'">
 <form class="mpFilt" action="'.fMpHref('liste','','',$sPar).'" method="post">'.rtrim("\n".MP_Hidden).'
 <div class="mpNoBr">'.fMpTx(MP_TxSuchen).' <input class="mpSFlt" name="mp_Such" value="'.fMpTx($s).'" /><input type="submit" class="mpKnopf" value="" title="'.fMpTx(MP_TxSuchen).'" /></div>
 </form>
 </div><div class="mpClear"></div>
</div>
';
}

function fMpNavigator($nSeite,$nZahl,$sQry){ //Navigator zum Blaettern
 $nSeitn=MP_ListenLaenge>0?ceil($nZahl/MP_ListenLaenge):1;
 $nAnf=$nSeite-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nSeitn){$nEnd=$nSeitn; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 $X ="\n".'<div class="mpNavL">';
 $X.="\n".'<div class="mpSZhl">'.fMpTx(MP_TxSeite).' '.$nSeite.'/'.$nSeitn.'</div>';
 $X.="\n".'<div class="mpNavi"><ul class="mpNavi">';
 $X.='<li class="mpNavL"><a href="'.fMpHref('liste','1','',$sQry).'" title="'.fMpTx(MP_TxAnfang).'">|&lt;</a></li>';
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nSeite) $sSeite=$i; else $sSeite='<b>'.$i.'</b>';
  $X.='<li class="mpNavL"><a href="'.fMpHref('liste',$i,'',$sQry).'" title="'.fMpTx(MP_TxSeite).$i.'">'.$sSeite.'</a></li>';
 }
 $X.='<li class="mpNavR"><a href="'.fMpHref('liste',max($nSeitn,1),'',$sQry).'" title="'.fMpTx(MP_TxEnde).'">&gt;|</a></li>';
 $X.='</ul></div>';
 $X.="\n".'<div class="mpClear"></div>';
 $X.="\n".'</div>';
 return $X;
}

//Text mit BB-Code einkuerzen
function fMpKurzMemo($s,$nL=80){
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