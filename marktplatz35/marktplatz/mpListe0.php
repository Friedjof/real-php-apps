<?php
function fMpSeite(){ //Quersuchliste

 $Meld=''; $MTyp='Fehl'; $MFlt=''; $sQ=''; $bSes=false;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Session pruefen
 if(!$sSes=MP_Session) if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
 if(MP_NListeAnders||MP_NDetailAnders||MP_NEingabeLogin||MP_NEingabeAnders) if($sSes>''){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!MP_SQL){
    if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Meld=MP_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Meld=MP_TxSessionUngueltig;
    }else $Meld=MP_TxSqlFrage;
   }
  }else $Meld=MP_TxSessionZeit;
 }

 //FeldFilter bestimmen
 if(isset($_GET['mp_Such'])) $s=fMpRq($_GET['mp_Such']); elseif(isset($_POST['mp_Such'])) $s=fMpRq($_POST['mp_Such']); else $s='';
 if(strlen($s)>0){ //Schnellsuche
  if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;mp_Such='.rawurlencode($s); $MFlt=htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1');
  if(substr_count($s,(MP_Datumsformat==1?'.':(MP_Datumsformat==2||MP_Datumsformat==3?'/':'-')))==2){ //Separatoren enthalten
   $sSuch=fMpNormDatum($s); if(strpos($sSuch,'00',5)) $sSuch=$s;
  }else $sSuch=$s;
 }else $sSuch='';

 $nSeite=(int)(isset($_GET['mp_Seite'])?$_GET['mp_Seite']:(isset($_POST['mp_Seite'])?$_POST['mp_Seite']:1));
 $nStart=($nSeite-1)*MP_ListenLaenge+1; $nStop=$nStart+(MP_ListenLaenge>0?MP_ListenLaenge:count($aIdx));

 //ueber alle Segmente
 $aSeg=explode(';',MP_Segmente); $aSgO=explode(';',MP_Anordnung); $bUTyp=false;
 $nSgI=0; $aSgS=array(); $aDaten=array(); $nTotal=0; $sIntervallAnfang=date('Y-m-d');
 while($nSegNo=array_search(++$nSgI,$aSgO))if(($sSegNam=$aSeg[$nSegNo])&&(substr($sSegNam,0,1)!='~'&&(substr($sSegNam,0,1)!='*'||$bSes))&&($sSegNam!='LEER')){
  $sSegNo=sprintf('%02d',$nSegNo);

  //Struktur holen
  $aFN=array(); $aFT=array(); $aLF=array(); $aNL=array(); $aLK=array(); $aSS=array();
  $aAW=array(); $aKW=array(); $aSW=array(); $aStru=array(); $nFelder=0;
  if(!MP_SQL){ //Text
   if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)) $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); else $aStru=array();
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
   $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]=''; $aAW[1]='';
   $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
   $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
  }
  if($bSes&&MP_NListeAnders) $aLF=$aNL; $bTypU=false;

  //Listenspaltenfolge ermitteln
  $aSpalten=array(); for($i=0;$i<$nFelder;$i++) $aSpalten[$aLF[$i]]=$i; $aSpalten[0]=0; ksort($aSpalten);
  if(in_array(-1,$aSpalten)){$j=count($aSpalten); for($i=$j-1;$i>0;$i--) if($aSpalten[$i]<0) array_splice($aSpalten,$i,1);}
  $nSpalten=count($aSpalten);

  //eventuelle Datumsspalte, Kategorienspalte und SEF-Titelspalte vorbereiten
  if(!$nDatPos=array_search('1',$aSpalten)) $nDatPos=-1;
  $nKatPos=array_search('k',$aFT); $nTitPos=0;
  if(MP_Sef&&($nTitPos=array_search('TITLE',$aFN))) if($aFT[$nTitPos]!='t') $nTitPos=0;

  //Daten holen
  $aTmp=array();
  if(!MP_SQL){ //Textdaten
   if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)) $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); else $aD=array(); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aD[$i])); $b=false;
    if($a[1]=='1'&&$a[2]>=$sIntervallAnfang){
     array_splice($a,1,1);
     if(!empty($sSuch)) for($j=1;$j<$nFelder;$j++){ //laufend
      $t=$aFT[$j];
      if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'){if(stristr($a[$j],$sSuch)){$b=true; break;}}
      elseif($t=='o'){if(strpos($a[$j],$sSuch)===0){$b=true; break;}}
      elseif($t=='d'||$t=='@'){if(substr($a[$j],0,10)==$sSuch){$b=true; break;}}
     }else $b=MP_SuchQLeer;
    }
    if($b){$aS=array($a[0]); //Datensatz gueltig
     for($j=1;$j<$nSpalten;$j++) $aS[]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aSpalten[$j]]));
     if($nKatPos>0) if($w=$a[$nKatPos]) if($w=array_search($w,$aKW)) $aS[]=chr(64+$w); //Kategoriezusatzspalte
     if($nTitPos>0) if($w=$a[$nTitPos]) $aS[]=$w; //Titelzusatzspalte fuer SEF-Titel
     if($nDatPos<=0) $aS[-1]=$a[1]; //Datum notfalls ergaenzen
     $aTmp[]=$aS;
    }//gueltig
   }//$nSaetze
   for($j=1;$j<$nSpalten;$j++) if($aFT[$aSpalten[$j]]=='u') $bTypU=true;
  }elseif($DbO){$s=''; //SQL-Daten
   if(!empty($sSuch)) for($j=1;$j<$nFelder;$j++){$t=$aFT[$j];
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l') $s.=' OR mp_'.$j.' LIKE "%'.$sSuch.'%"';
    elseif($t=='o') $s.=' OR mp_'.$j.' LIKE "'.$sSuch.'%"';
    elseif($t=='d'||$t=='@')  $s.=' OR mp_'.$j.' LIKE "'.$sSuch.'%"';
   }else $s='AND nr'.(MP_SuchQLeer?'>':'<').'0';
   $s=' AND mp_1>="'.$sIntervallAnfang.'" AND('.substr($s,4).')';
   $t=''; $nDatPos=0; $i=$nSpalten;
   for($j=1;$j<$nSpalten;$j++){
    $k=$aSpalten[$j]; $t.=',mp_'.$k;
    if($k==1) $nDatPos=$j; if($aFT[$k]=='u') $bTypU=true;
   }
   if($nKatPos>0){$t.=',mp_'.$nKatPos; $i++;}
   if($nDatPos==0){$t.=',mp_1'; $nDatSel=$i++;}
   if($rR=$DbO->query('SELECT nr'.$t.' FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE online="1"'.$s.' ORDER BY mp_1'.($nFelder>2?',mp_2'.($nFelder>3?',mp_3':''):'').',nr')){
    while($a=$rR->fetch_row()){$aS=array($a[0]);
     for($j=1;$j<$nSpalten;$j++) $aS[]=str_replace("\n",'',$a[$j]);
     if($nKatPos>0) if($j=$a[$nSpalten]) if($j=array_search($j,$aKW)) $aS[]=chr(64+$j); //Kategoriezusatzspalte
     if($nDatPos<=0) $aS[-1]=$a[$nDatSel]; //Datum notfalls ergaenzen
     $aTmp[]=$aS;
    }$rR->close();
   }else $Meld=MP_TxSqlFrage;
  }

  if(count($aTmp)>0){ //Daten aussondern
   if(MP_Rueckwaerts) krsort($aTmp); reset($aTmp); $b=false;
   foreach($aTmp as $i=>$a){
    if(++$nTotal<$nStop&&$nTotal>=$nStart){$aDaten[]=array($nSegNo,$a); $b=true;}
   }
   if($b){
    if($bTypU) $bUTyp=true;
    $aSgs[$nSegNo]=array('FN'=>$aFN,'FT'=>$aFT,'LF'=>$aLF,'LK'=>$aLK,'SS'=>$aSS,'SW'=>$aSW,'SP'=>$aSpalten);
   }
  }
 }//ueber alle Segmente

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#'); $nNutzerZahl=0; $nNLF=0;
 if($bUTyp){
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
  $nNutzerZahl=count($aNutzer); $nNLF=($bSes?MP_NNutzerListFeld:MP_NutzerListFeld);
 }

 $X='';
 // Detail als Popup-Fenster
 if((MP_MailPopup||MP_DetailPopup)&&!defined('MP_MpWin')){$X="\n".'<script type="text/javascript">function MpWin(sURL){mpWin=window.open(sURL,"mpwin","width='.MP_PopupBreit.',height='.MP_PopupHoch.',left='.MP_PopupX.',top='.MP_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");mpWin.focus();}</script>'; define('MP_MpWin',true);}

 // Navigation, Schnellsuche
 if(MP_NaviOben>0||MP_NaviUnten>0) $sNavig=fMpNavigator($nSeite,$nTotal,$sQ);
 if(MP_SuchQFilter>0) $sSuchFlt=fMpSuchFilter(isset($_GET['mp_Such'])?fMpRq($_GET['mp_Such']):(isset($_POST['mp_Such'])?fMpRq($_POST['mp_Such']):''),(MP_SuchQFilter%2?'L':'R'));
 if(MP_SuchQFilter==1||MP_SuchQFilter==2) $X.="\n".$sSuchFlt;
 if(MP_NaviOben==1&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchQFilter==3||MP_SuchQFilter==4) $X.="\n".$sSuchFlt;

 if($Meld==''){$MTyp='Meld';
  if($MFlt!='') $Meld=str_replace('#F',$MFlt,MP_TxListQSuch); else $Meld=MP_TxListQGsmt;
 }
 $X.="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';

 if(MP_SuchQFilter==5||MP_SuchQFilter==6) $X.="\n".$sSuchFlt;
 if(MP_NaviOben==2&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchQFilter==7||MP_SuchQFilter==8) $X.="\n".$sSuchFlt;

 $mpListenInfo=(MP_GastLInfo||$bSes?MP_ListenInfo:-1);
 $mpListenBenachr=(MP_GastLBenachr||$bSes?MP_ListenBenachr:-1);

 $sLay=(defined('MP_Layout')?MP_Layout:'x');

 //Ausgabe ueber alle Segmente
 $nSegNo=0; $sEigeneZeile='';
 foreach($aDaten as $a){ //ueber alle Datensaetze
  if($nSegNo!=$a[0]){ //neuer Kopf
   if($nSegNo!=0) $X.="\n".'</div>'; // Tabelle
   $nSegNo=$a[0]; $sSegNo=sprintf('%02d',$nSegNo); $aStru=$aSgs[$nSegNo];
   if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

   //eigene Layoutzeile pruefen
   if($bEigeneZeilen=(MP_EigeneZeilen||$sLay=='e')&&($sLay!='t')&&(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm')||file_exists(MP_Pfad.'mpListenZeile.htm'))){
    if(!$sEigeneZeile=@implode('',@file(MP_Pfad.'mpListen'.$sSegNo.'Zeile.htm'))) $sEigeneZeile=@implode('',@file(MP_Pfad.'mpListenZeile.htm'));
    $s=strtolower($sEigeneZeile);
    if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
   }

   if(MP_SegTrnZeile) $X.="\n".'<div class="mpTrnZl">'.fMpTx(MP_TxSegment).': '.fMpTx(substr($aSeg[$nSegNo],0,1)!='*'?$aSeg[$nSegNo]:substr($aSeg[$nSegNo],1)).'</div>';
   $X.="\n\n".'<div class="mpTab'.(!$bEigeneZeilen?'l':'L').'">'; $nFarb=1;

   $nSpalten=count($aStru['SP']); $bMitID=$aStru['LF'][0]>0;
   if(!$bEigeneZeilen){ //Standardlayout
    $X.="\n".' <div class="mpTbZl0">'; // Kopfzeile
    if($mpListenInfo==0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</div>';
    if($mpListenBenachr==0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</div>';
    for($j=($bMitID?0:1);$j<$nSpalten;$j++){
     if($mpListenInfo==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</div>';
     if($mpListenBenachr==$j&&$j>0) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</div>';
     $sFS=$aStru['FT'][$aStru['SP'][$j]]; if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M';
     $X.="\n".'  <div class="mpTbLst mpTbLs'.$sFS.'">'.fMpDt($aStru['FN'][$aStru['SP'][$j]]).'</div>';
    }
    if($mpListenInfo>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</div>';
    if($mpListenBenachr>=$j) $X.="\n".'  <div class="mpTbLst mpTbLsM">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</div>';
    $X.="\n".' </div>';
   }else{ //eigene Kopfzeile
    if(!$r=@implode('',(file_exists(MP_Pfad.'mpListen'.$sSegNo.'Kopf.htm')?file(MP_Pfad.'mpListen'.$sSegNo.'Kopf.htm'):array(''))))
     $r=@implode('',(file_exists(MP_Pfad.'mpListenKopf.htm')?file(MP_Pfad.'mpListenKopf.htm'):array('')));
    $s=strtolower($r); if(strpos($s,'<body')||strpos($s,'<head')) $r='';
    if($r){$p=0; while($p=strpos($r,'{',$p+1)) if($i=strpos($r,'}',$p+1)) $r=substr_replace($r,'',$p,$i-$p+1); $X.="\n ".'<div class="mpTbZL0">'.$r."\n </div>";}
   }
  }//neuer Kopf

  //Datenzeilen
  $a=$a[1]; $sZl=''; $sId=$a[0]; $sZS=$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
  if(array_search('k',$aStru['FT'])>0) if(isset($a[$nSpalten])) if($j=$a[$nSpalten]) $sZS.=' mpLstKat'.$j; //Kategorie aus Zusatzspalte
  if(MP_Sef){ //SEF-Dateinamen bilden
   $sSefName=MP_SegName; $bSefSuche=true;
   if(isset($a[$nSpalten+1])) $sSefName=fMpDt($a[$nSpalten+1]); //zusaetzliche Titelspalte
   else for($j=1;$j<$nSpalten;$j++) if($bSefSuche) if($aStru['FT'][$aStru['SP'][$j]]=='t'){ //SEF-Suchen
    $bSefSuche=false; $sSefName=fMpDt($a[$j]);
    for($j=strlen($sSefName)-1;$j>=0;$j--) //BB-Code weg
     if(substr($sSefName,$j,1)=='[') if($v=strpos($sSefName,']',$j)) $sSefName=substr_replace($sSefName,'',$j,++$v-$j);
    $sSefName=trim(substr(str_replace("\n",' ',str_replace("\n",'',$sSefName)),0,50));
   }
   if($sSefName=trim(str_replace('.','_',trim(str_replace(';','',str_replace(':','',str_replace('„','',str_replace('“','',$sSefName)))))))) $sSefName='-'.$sSefName;
  }else $sSefName='';
  if(!$bEigeneZeilen){ //Standardlayout
   if($mpListenInfo==0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</span><a href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a></div>';
   if($mpListenBenachr==0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</span><a href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a></div>';
  }else $sZl=$sEigeneZeile; //eigenes Zeilenlayout

  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aStru['SP'][$j]; $t=$aStru['FT'][$k]; $sStil=''; $sFS='';
   if(!$bEigeneZeilen){ //Standardlayout
    if($mpListenInfo==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenInfTitel?fMpTx(MP_ListenInfTitel):'&nbsp;').'</span><a href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a></div>';
    if($mpListenBenachr==$j&&$j>0) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><span class="mpTbLst">'.(MP_ListenBenachTitel?fMpTx(MP_ListenBenachTitel):'&nbsp;').'</span><a href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a></div>';
   }
   if($s=$a[$j]){
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
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sFS.=' mpTbLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein); $sFS.=' mpTbLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sFS.=' mpTbLsR';
      break;
     case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); $sFS.=' mpTbLsM'; break; //Nummer
     case 'l': //Link
      $aI=explode('|',$s); $s=$aI[0]; $v=fMpDt(isset($aI[1])?$aI[1]:$s);
      if(MP_LinkSymbol){$v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMpDt($s).'" alt="'.fMpDt($s).'" />'; $sFS.=' mpTbLsM';}
      $s='<a class="mpText" title="'.fMpDt($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.'</a>';
      break;
     case 'e': //eMail
      $s='<a href="'.fMpHref('kontakt',(MP_MailPopup?'':$nSeite),$sId,$sQ.'&amp;mp_Eml='.$k.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" title="'.fMpTx(MP_TxKontakt).'" alt="'.fMpTx(MP_TxKontakt).'" /></a>';
      $sFS.=' mpTbLsM';
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
      $s=fMpDt($s); break;
     case 's': $w=$s; //Symbol
      $p=array_search($s,$aStru['SW']); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
      $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.fMpDt($w).'" />'; $sFS.=' mpTbLsM';
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
      $s='<a href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&MP_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildKlein; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbLsM';
   }else $s='&nbsp;';
   if(($w=$aStru['SS'][$k])){$sStil=' style="'.str_replace('`,',';',$w).'"';}
   if($aStru['LK'][$k]>0||$t=='b') $s='<a class="mpDetl" href="'.fMpHref('detail','',$sId.$sSefName,$sQ.(MP_DetailPopup?'&amp;mp_Popup=1':''),$nSegNo).'" title="'.fMpTx(MP_TxDetail).(MP_DetailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'">'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="mpTbLst'.$sFS.'"'.$sStil.'><span class="mpTbLst">'.fMpTx($aStru['FN'][$k]).'</span>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$aStru['FN'][$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if(!$bEigeneZeilen){ //Standardlayout
   if($mpListenInfo>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><a href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a></div>';
   if($mpListenBenachr>=$j) $sZl.="\n".'  <div class="mpTbLst mpTbLsM"><a href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a></div>';
   $X.="\n".' <div class="mpTbZl'.$sZS.'">'.$sZl."\n".' </div><div class="mpTbZlX"></div>';
  }else{ //eigenes Layout
   $sZl=str_replace('{Nummer}',($bMitID?(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId):''),$sZl);
   $sZl=str_replace('{SendInfo}',($mpListenInfo>=0?'<a class="mpDetl" href="'.fMpHref('info',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconInfo.gif" title="'.fMpTx(MP_TxSendInfo).'" alt="'.fMpTx(MP_TxSendInfo).'" /></a>':''),$sZl);
   $sZl=str_replace('{Aendern}','&nbsp;',$sZl);
   $sZl=str_replace('{Kopieren}','&nbsp;',$sZl);
   $sZl=str_replace('{Nachricht}',($mpListenBenachr>=0?'<a class="mpDetl" href="'.fMpHref('nachricht',(MP_MailPopup?'':$nSeite),$sId,$sQ.(MP_MailPopup?'&amp;mp_Popup=1':''),$nSegNo).(MP_MailPopup?'" target="mpwin" onclick="MpWin(this.href);return false;':'').'"><img class="mpIcon" src="'.MP_Url.'grafik/iconNachricht.gif" title="'.fMpTx(MP_TxBenachrService).'" alt="'.fMpTx(MP_TxBenachrService).'" /></a>':''),$sZl);
   $X.="\n".' <div class="mpTbZL'.$sZS.'">'."\n".$sZl."\n".' </div><div class="mpTbZLX"></div>';
  }
 }//ueber alle Datensaetze
 if($nSegNo!=0) $X.="\n".'</div>';

 //Navigator unter der Tabelle
 if(MP_SuchQFilter==9||MP_SuchQFilter==10) $X.="\n".$sSuchFlt;
 if(MP_NaviUnten==1&&MP_ListenLaenge>0) $X.="\n".$sNavig;
 if(MP_SuchQFilter==11||MP_SuchQFilter==12) $X.="\n".$sSuchFlt;

 return $X;
}

function fMpSuchFilter($s,$sAlign){ //Schnellsuchfilter zeichnen
if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
return '
<div class="mpFilt">
 <div class="mpSFlt'.$sAlign.'">
 <form class="mpFilt" action="'.fMpHref('suchen').'" method="post">'.rtrim("\n".MP_Hidden).'
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
 $X.='<li class="mpNavL"><a href="'.fMpHref('suchen','1','',$sQry).'" title="'.fMpTx(MP_TxAnfang).'">|&lt;</a></li>';
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nSeite) $sSeite=$i; else $sSeite='<b>'.$i.'</b>';
  $X.='<li class="mpNavL"><a href="'.fMpHref('suchen',$i,'',$sQry).'" title="'.fMpTx(MP_TxSeite).$i.'">'.$sSeite.'</a></li>';
 }
 $X.='<li class="mpNavR"><a href="'.fMpHref('suchen',max($nSeitn,1),'',$sQry).'" title="'.fMpTx(MP_TxEnde).'">&gt;|</a></li>';
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

function fMpNormDatum($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}
?>