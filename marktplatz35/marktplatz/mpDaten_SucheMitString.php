<?php
global $DbO,$aMpDaten,$aMpSpalten,
       $aMpFN,$aMpFT,$aMpLF,$aMpNL,$aMpOF,$aMpLK,$aMpSS,$aMpDF,$aMpND,$aMpZS,$aMpAW,$aMpKW,$aMpSW;
function fMpDaten($bListe,$bLimit=true){ //Daten bereitstellen
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else{define('MP_Meldung','<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>'); return false;}

 //definiert werden: MP_SuchParam, MP_MetaKey, MP_MetaDes, MP_Meldung, MP_Saetze, MP_SessionOK
 global $DbO,$aMpDaten,$aMpSpalten; //neu angelegt
 $Meld=''; $MFlt=''; $MTyp='Fehl'; $sQ=''; $bSes=false; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Struktur holen
 global $aMpFN,$aMpFT,$aMpLF,$aMpNL,$aMpOF,$aMpLK,$aMpSS,$aMpDF,$aMpND,$aMpZS,$aMpAW,$aMpKW,$aMpSW;
 $nFelder=0; $aStru=array(); $nListenIndex=1;
 $aMpFN=array(); $aMpFT=array(); $aMpLF=array(); $aMpNL=array(); $aMpOF=array(); $aMpLK=array(); $aMpSS=array();
 $aMpDF=array(); $aMpND=array(); $aMpZS=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
 if(!MP_SQL){ //Text
  if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)) $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); else $aStru=array();
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.MP_Segment.'"')){
   $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(count($aStru)>1){
  $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
  if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
  $aMpFT=explode(';',rtrim($aStru[1])); $nListenIndex=substr($aMpFT[0],14); $aMpFT[0]='i'; $aMpFT[1]='d';
  $aMpLF=explode(';',rtrim($aStru[2])); $aMpLF[0]=substr($aMpLF[0],14,1);
  $aMpNL=explode(';',rtrim($aStru[3])); $aMpNL[0]=substr($aMpNL[0],14,1);
  $aMpOF=explode(';',rtrim($aStru[4])); $aMpOF[0]=substr($aMpOF[0],14,1); $aMpOF[1]='1';
  $aMpLK=explode(';',rtrim($aStru[5])); $aMpLK[0]=substr($aMpLK[0],14,1);
  $aMpSS=explode(';',rtrim($aStru[6])); $aMpSS[0]='';
  $aMpDF=explode(';',rtrim($aStru[7])); $aMpDF[0]=substr($aMpDF[0],14,1);
  $aMpND=explode(';',rtrim($aStru[8])); $aMpND[0]=substr($aMpND[0],14,1);
  $aMpZS=explode(';',rtrim($aStru[9])); $aMpZS[0]='';
  $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]=''; $aMpAW[1]='';
  $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
  $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
 }
 define('MP_ListenIndex',$nListenIndex);

 //Session pruefen
 if(!$sSes=MP_Session) if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
 if(MP_NListeAnders||MP_NDetailAnders||MP_NEingabeLogin||MP_NEingabeAnders) if($sSes>''){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!MP_SQL){
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
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
 define('MP_SessionOK',$bSes); if($bSes&&MP_NListeAnders) $aMpLF=$aMpNL;

 //FeldFilter bestimmen, Listenspaltenfolge ermitteln
 $aMpSpalten=array(); $bSuchDat=false;
 if(isset($_GET['mp_Such'])) $s=fMpRq($_GET['mp_Such']); elseif(isset($_POST['mp_Such'])) $s=fMpRq($_POST['mp_Such']); else $s='';
 if($bSuch=(strlen($s)>0)){ //Schnellsuche
  if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;mp_Such='.rawurlencode($s); $MFlt.=', '.htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1');
  if(substr_count($s,(MP_Datumsformat==1?'.':(MP_Datumsformat==2||MP_Datumsformat==3?'/':'-')))>1){ //Separatoren enthalten
   $sSuch=fMpNormDatum($s); if(!strpos($sSuch,'00',5)){$bSuchDat=true; $bSuch=false;} else $sSuch=$s;
  }else $sSuch=$s;
 }else $sSuch='';
 for($i=0;$i<$nFelder;$i++){ //ueber alle Felder
  $t=$aMpFT[$i]; $aMpSpalten[$aMpLF[$i]]=($bLimit||(($t!='m'||MP_DruckLMemo))?$i:-1); //unlimitierte Druckliste ohne Memos
  if(strlen($sSuch)==0){ //keine Schnellsuche
   if(isset($_GET['mp_'.$i.'F1'])) $s=fMpRq($_GET['mp_'.$i.'F1']); elseif(isset($_POST['mp_'.$i.'F1'])) $s=fMpRq($_POST['mp_'.$i.'F1']); else $s='';
   if(strlen($s)){ //erstes Suchfeld ausgefuellt
    if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;mp_'.$i.'F1='.rawurlencode($s); $MFlt.=', '.$aMpFN[$i].' '.$s; // Domberg
    if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else{$a1Filt[$i]=fMpNormDatum($s); $a2Filt[$i]='';} //Datum
   }elseif($t=='v'){$a1Filt[$i]=(MP_NVerstecktSehen&&($sSes>'')?'':'N'); $a2Filt[$i]='';} //versteckt
   elseif($t=='u'&&$bSes&&isset($_GET['mp_Aendern'])&&!MP_NAendernFremde){$a1Filt[$i]=substr($sSes,0,4);} //aendern fuer User
   if(isset($_GET['mp_'.$i.'F2'])) $s=fMpRq($_GET['mp_'.$i.'F2']); elseif(isset($_POST['mp_'.$i.'F2'])) $s=fMpRq($_POST['mp_'.$i.'F2']); else $s='';
   if(strlen($s)){ //zweites Suchfeld ausgefuellt
    if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;mp_'.$i.'F2='.rawurlencode($s); if(!strpos($MFlt,$aMpFN[$i])) $MFlt.=', '.$aMpFN[$i].' '.$s; // Domberg
    if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
    elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
    if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else $a2Filt[$i]=fMpNormDatum($s);
   }
   if(isset($_GET['mp_'.$i.'F3'])) $s=fMpRq($_GET['mp_'.$i.'F3']); elseif(isset($_POST['mp_'.$i.'F3'])) $s=fMpRq($_POST['mp_'.$i.'F3']); else $s='';
   if(strlen($s)){ //drittes Suchfeld ausgefuellt
    if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;mp_'.$i.'F3='.rawurlencode($s); if(!strpos($MFlt,$aMpFN[$i])) $MFlt.=', '.$aMpFN[$i].' '.$s; // Domberg  
    $a3Filt[$i]=$s;
   }
  }//$sSuch
 }
 $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='9'; //Normalsuche
 if(MP_SuchArchiv) if(isset($_GET['mp_Archiv'])||(isset($_POST['mp_Archiv'])&&$_POST['mp_Archiv']>'')) //Archivsuche
  {$sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00'; $sQ.='&amp;mp_Archiv=1';}
 if($bSuchDat){$a1Filt[1]=$sSuch; $a2Filt[1]='';} //Schnellsuche nach Datum

 $aMpSpalten[0]=0; ksort($aMpSpalten);
 if(in_array(-1,$aMpSpalten)){$j=count($aMpSpalten); for($i=$j-1;$i>0;$i--) if($aMpSpalten[$i]<0) array_splice($aMpSpalten,$i,1);}
 $nSpalten=count($aMpSpalten); define('MP_SuchParam',$sQ);

 $nIndex=(isset($_GET['mp_Index'])?(int)$_GET['mp_Index']:((isset($_POST['mp_Index'])&&strlen($_POST['mp_Index'])>0)?(int)$_POST['mp_Index']:MP_ListenIndex)); //Sortierspalte bestimmen
 if((isset($_GET['mp_Neu'])||isset($_POST['mp_Neu']))&&($i=array_search('@',$aMpFT))){
  $nIndex=$i; $_GET['mp_Index']=$i; $_POST['mp_Index']=$i; $_GET['mp_Rueck']=1; $_POST['mp_Rueck']=1;
 }
 if($bListe){//eventuelle Datumsspalte, Kategorienspalte und SEF-Titelspalte vorbereiten
  if(!$nDatPos=array_search('1',$aMpSpalten)) $nDatPos=-1;
  $nKatPos=array_search('k',$aMpFT); $nTitPos=0;
  if(MP_Sef&&($nTitPos=array_search('TITLE',$aMpFN))) if($aMpFT[$nTitPos]!='t') $nTitPos=0;
 }else{$sId=(isset($_GET['mp_Nummer'])?(int)$_GET['mp_Nummer']:0); $nKatPos=0; $nTitPos=0;}

 //Daten holen
 $aTmp=array(); $aIdx=array(); //ListenDaten
 if(!MP_SQL){ //Textdaten
  $aD=(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)?file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate):array()); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$i])); $nId=(int)$a[0]; $sOnl=$a[1]; array_splice($a,1,1); $sAblaufDat=$a[1];
   if(!$bSuch){ //keine Schnellsuche
    $b=($sAblaufDat>=$sIntervallAnfang&&$sAblaufDat<=$sIntervallEnde&&($sOnl=='1'||$bSes&&isset($_GET['mp_Aendern']))); //laufend
    if($b&&is_array($a1Filt)){
     reset($a1Filt);
     foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
      $t=($aMpFT[$j]); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
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
   }else{$b=false; //Schnellsuche
    if($sAblaufDat>=$sIntervallAnfang&&$sAblaufDat<=$sIntervallEnde&&$sOnl=='1') for($j=1;$j<$nFelder;$j++){ //laufend
     $t=$aMpFT[$j];
     if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'){if(stristr($a[$j],$sSuch)){$b=true; break;}}
     elseif($t=='o') if(strpos($a[$j],$sSuch)===0){$b=true; break;}
   }}
   if($b){ //Datensatz gueltig
    $aTmp[$nId]=array($nId);
    if($bListe){
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aMpSpalten[$j]]));
     if($nKatPos>0) if($w=$a[$nKatPos]) if($w=array_search($w,$aMpKW)){ //Kategoriezusatzspalte
      if($w<27) $aTmp[$nId][]=chr(64+$w);
      else $aTmp[$nId][]=chr(64+floor(($w-1)/26)).chr(65+($w-1)%26);
     }
     if($nDatPos<=0) $aTmp[$nId][-1]=$a[1]; //Datum notfalls ergaenzen
     if($nTitPos>0) if($w=$a[$nTitPos]) $aTmp[$nId][-2]=$w; //Titelzusatzspalte fuer SEF-Titel
    }elseif($nId==$sId) for($j=1;$j<$nFelder;$j++) $aTmp[$nId][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$j]));
    if($nIndex==1) $aIdx[$nId]=sprintf('%0'.MP_NummerStellen.'d',$i); //nach Datum
    elseif($nIndex>1){ //andere Sortierung
     $s=strtoupper(strip_tags($a[$nIndex])); $t=$aMpFT[$nIndex];
     for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
      if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
     if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
     elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
     $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.MP_NummerStellen.'d',$i);
    }
    elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.MP_NummerStellen.'d',$nId); //nach Nr
   }//gueltig
  }//$nSaetze
 }elseif($DbO){ //SQL-Daten
  if(!$bSuch){ //keine Schnellsuche
   $s=' AND mp_1>="'.$sIntervallAnfang.'" AND mp_1<="'.$sIntervallEnde.'"';
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
    $s.=' AND('.($j>0?'mp_'.$j:'nr'); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$aMpFT[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
     $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR '.($j>0?'mp_'.$j:'nr').' LIKE "%'.$w.'%"';
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
    $t=$aMpFT[$j];
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
     $s.=' AND NOT('.($j>0?'mp_'.$j:'nr').' LIKE "%'.$v.'%")';
    elseif($t=='o') $s.=' AND NOT('.($j>0?'mp_'.$j:'nr').' LIKE "'.$v.'%")';
   }
  }else{$s=''; //Schnellsuche
   for($j=1;$j<$nFelder;$j++){
    $t=$aMpFT[$j];
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l') $s.=' OR mp_'.$j.' LIKE "%'.$sSuch.'%"';
    elseif($t=='o') $s.=' OR mp_'.$j.' LIKE "'.$sSuch.'%"';
   }$s=' AND mp_1>="'.$sIntervallAnfang.'" AND mp_1<="'.$sIntervallEnde.'" AND('.substr($s,4).')';
  }
  $t=''; $nDatPos=0; $i=$nSpalten; $bIdx=true; $nIdx=$aMpLF[$nIndex];
  if($bListe){ //besondere Felder ergaenzen
   for($j=1;$j<$nSpalten;$j++){
    $k=$aMpSpalten[$j]; $t.=',mp_'.$k; if($nIndex==$k) $bIdx=false;
    if($k==1) $nDatPos=$j;
   }
   if($nKatPos>0){$t.=',mp_'.$nKatPos; $i++;}
   if($nDatPos==0){$t.=',mp_1'; $nDatSel=$i++;}
   if($bIdx&&$nIndex>0){$t.=',mp_'.$nIndex; $aMpLF[$nIndex]=$i++;}
   if($nTitPos>0){$t.=',mp_'.$nTitPos; $nTitPos=$i;}
  }else{if($nIndex>1) $t.=',mp_'.$nIndex; $aMpLF[$nIndex]=1;}

  $o='online="1"'; if($bSes&&isset($_GET['mp_Aendern'])) $o='online>""';
  if($rR=$DbO->query('SELECT nr'.$t.' FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE '.$o.$s.' ORDER BY mp_1'.($nFelder>2?',mp_2'.($nFelder>3?',mp_3':''):'').',nr')){
   $i=0;
   while($a=$rR->fetch_row()){
    $nId=(int)$a[0]; $aTmp[$nId]=array($nId);
    if($bListe){
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace("\r",'',$a[$j]);
     if($nKatPos>0) if($j=$a[$nSpalten]) if($j=array_search($j,$aMpKW)){ //Kategoriezusatzspalte
      if($j<27) $aTmp[$nId][]=chr(64+$j);
      else $aTmp[$nId][]=chr(64+floor(($j-1)/26)).chr(65+($j-1)%26);
     }
     if($nTitPos>0&&isset($a[$nTitPos])) if($w=$a[$nTitPos]) $aTmp[$nId][-2]=$w; //Titelzusatzspalte fuer SEF-Titel
     if($nDatPos<=0) $aTmp[$nId][-1]=$a[$nDatSel]; //Datum notfalls ergaenzen
    }
    if($nIndex==1) $aIdx[$nId]=sprintf('%0'.MP_NummerStellen.'d',++$i); //nach Datum
    elseif($nIndex>1){ //andere Sortierung
     $s=strtoupper(strip_tags($a[$aMpLF[$nIndex]])); $t=$aMpFT[$nIndex];
     for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
      if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
     if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
     elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
     $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.MP_NummerStellen.'d',$i);
    }
    elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.MP_NummerStellen.'d',$nId); //nach Nr.
   }$rR->close();
   if(!$bListe){ //den Datensatz komplett holen
    if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'"')){
     if($a=$rR->fetch_row()){
      for($j=2;$j<=$nFelder;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]);
     }$rR->close();
    }
   }
  }else $Meld=MP_TxSqlFrage;
  $aMpLF[$nIndex]=$nIdx;
 }

 if($Meld==''){$MTyp='Meld';
  if(substr($MFlt,0,1)==',') $Meld=str_replace('#F',substr($MFlt,1),MP_TxListSuch); else $Meld=MP_TxListGsmt; $a='';
  if(strpos($sQ,'mp_Archiv=1')>0){$a=MP_TxArchiv; if($p=strpos(' '.$Meld,'#A')) if(substr($Meld,$p+1,1)!=' ') $a.='-';}
  $Meld=str_replace('#S',MP_SegName,str_replace('#A',$a,$Meld));
 }
 define('MP_Meldung','<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>');

 //Sortieren
 $sRw=''; if(isset($_GET['mp_Rueck'])) $sRw=fMpRq1($_GET['mp_Rueck']); elseif(isset($_POST['mp_Rueck'])) $sRw=fMpRq1($_POST['mp_Rueck']);
 if($sRw!='1'){if($nIndex!=1) asort($aIdx); else if(strlen($sRw)<=0&&MP_Rueckwaerts) arsort($aIdx);} else arsort($aIdx);

 $aMpDaten=array(); reset($aIdx); define('MP_Saetze',count($aIdx));
 if($bListe){ //Liste limitieren nach Startposition und Stopposition der Seite
  $nSeite=(int)(isset($_GET['mp_Seite'])?$_GET['mp_Seite']:(isset($_POST['mp_Seite'])?$_POST['mp_Seite']:1));
  $nStart=($nSeite-1)*MP_ListenLaenge+1; $nStop=$nStart+(MP_ListenLaenge>0?MP_ListenLaenge:count($aIdx));
  if(!$bLimit){$nStart=1; $nStop=99999;}
  $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aMpDaten[]=$aTmp[$i];
  if(strlen(MP_TxLMetaKey)>0) define('MP_MetaKey',MP_TxLMetaKey);
  if(strlen(MP_TxLMetaDes)>0) define('MP_MetaDes',MP_TxLMetaDes);
  if(strlen(MP_TxLMetaTit)>0) define('MP_MetaTit',MP_TxLMetaTit);
 }else{ //Detailarray mit Zusatzdaten ergaenzen
  $bGefunden=false; $nVorg=0; $nNachf=0; $nPos=0; $aMpDaten[0]=array();
  foreach($aIdx as $i=>$xx){
   if(!$bGefunden){
    $nPos++; if($i!=$sId){$nVorg=$i; $aVorg=$aTmp[$i];} else{$bGefunden=true; $aMpDaten[0]=$aTmp[$i];}
   }elseif($nNachf==0){$nNachf=$i; $aNachf=$aTmp[$i];}
  }
  if(!$bGefunden){$nVorg=0; $nNachf=0; $nPos=0; $aMpDaten[0][0]=$sId;}
  $aMpDaten[1]=$nVorg; $aMpDaten[2]=$nPos; $aMpDaten[3]=$nNachf;  $sTitel=''; $bSuchTit=true;
  if(count($aMpDaten[0])>1) for($i=1;$i<$nFelder;$i++) if($aMpFT[$i]=='t'){ //ueber alle TextFelder
   if($aMpFN[$i]=='META-KEY'&&(($s=fMpDt($aMpDaten[0][$i]))||($s=MP_TxDMetaKey))) define('MP_MetaKey',$s);
   elseif($aMpFN[$i]=='META-DES'&&(($s=fMpDt($aMpDaten[0][$i]))||($s=MP_TxDMetaDes))) define('MP_MetaDes',$s);
   elseif($aMpFN[$i]=='TITLE'&&($s=fMpDt($aMpDaten[0][$i]))){$sTitel=$s; $bSuchTit=false;}
   elseif($bSuchTit){$bSuchTit=false;
    if(MP_DTitelQuelle>0){
     $s=fMpDt($aMpDaten[0][$i]);
     for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
     $s=str_replace('  ',' ',str_replace("\n",' ',str_replace("\r",'',$s)));
     if(MP_DTitelQuelle==1){ //erste n Worte
      $nPos=-1; $l=strlen($s); $j=MP_DTitelWL; while($j--) $nPos=max(strpos($s.' ',' ',min($nPos+2,$l)),$nPos);
      if($nPos>0) $sTitel=trim(substr($s,0,$nPos)); else $sTitel=$s;
     }else $sTitel=trim(substr($s,0,MP_DTitelZL)); //erste n Zeichen
    }else $sTitel=MP_TxDMetaTit; // fester Ersatztitel
  }}
  if($sTitel) define('MP_MetaTit',$sTitel); $sTitel='';
 }
 return true;
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
