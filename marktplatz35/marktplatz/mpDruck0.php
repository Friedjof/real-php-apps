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

 //FeldFilter bestimmen
 if(isset($_GET['mp_Such'])) $s=fMpRq($_GET['mp_Such']); elseif(isset($_POST['mp_Such'])) $s=fMpRq($_POST['mp_Such']); else $s='';
 if(strlen($s)>0){ //Schnellsuche
  if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;mp_Such='.rawurlencode($s); $MFlt=htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1');
  if(substr_count($s,(MP_Datumsformat==1?'.':(MP_Datumsformat==2||MP_Datumsformat==3?'/':'-')))==2){ //Separatoren enthalten
   $sSuch=fMpNormDatum($s); if(strpos($sSuch,'00',5)) $sSuch=$s;
  }else $sSuch=$s;
 }else $sSuch='';

 //ueber alle Segmente
 $aSeg=explode(';',MP_Segmente); $aSgO=explode(';',MP_Anordnung); $bUTyp=false;
 $nSgI=0; $aSgS=array(); $aDaten=array(); $nTotal=0; $sIntervallAnfang=date('Y-m-d');
 while($nSegNo=array_search(++$nSgI,$aSgO))if(($sSegNam=$aSeg[$nSegNo])&&(substr($sSegNam,0,1)!='~'&&(substr($sSegNam,0,1)!='*'||$bSes))&&($sSegNam!='LEER')){
  $sSegNo=sprintf('%02d',$nSegNo);

  //Struktur holen
  $aFN=array(); $aFT=array(); $aLF=array(); $aNL=array(); $aLK=array(); $aSS=array();
  $aAW=array(); $aKW=array(); $aSW=array(); $aStru=array(); $nFelder=0;
  if(!MP_SQL){ //Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
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
  $aSpalten=array(); for($i=0;$i<$nFelder;$i++) $aSpalten[$aLF[$i]]=(($aFT[$i]!='m'||MP_DruckLMemo)?$i:-1);
  $aSpalten[0]=0; ksort($aSpalten);
  if(in_array(-1,$aSpalten)){$j=count($aSpalten); for($i=$j-1;$i>0;$i--) if($aSpalten[$i]<0) array_splice($aSpalten,$i,1);}
  $nSpalten=count($aSpalten);

  //eventuelle Datumsspalte, Kategorienspalte vorbereiten
  if(!$nDatPos=array_search('1',$aSpalten)) $nDatPos=-1;
  $nKatPos=array_search('k',$aFT);

  //Daten holen
  $aTmp=array();
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
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
     for($j=1;$j<$nSpalten;$j++) $aS[]=str_replace("\r",'',$a[$j]);
     if($nKatPos>0) if($j=$a[$nSpalten]) if($j=array_search($j,$aKW)) $aS[]=chr(64+$j); //Kategoriezusatzspalte
     if($nDatPos<=0) $aS[-1]=$a[$nDatSel]; //Datum notfalls ergaenzen
     $aTmp[]=$aS;
    }$rR->close();
   }else $Meld=MP_TxSqlFrage;
  }

  if(count($aTmp)>0){ //Daten aussondern
   if(MP_Rueckwaerts) krsort($aTmp); reset($aTmp); $b=false;
   foreach($aTmp as $i=>$a){
    $aDaten[]=array($nSegNo,$a); ++$nTotal;
   }
   if($bTypU) $bUTyp=true;
   $aSgs[$nSegNo]=array('FN'=>$aFN,'FT'=>$aFT,'LF'=>$aLF,'LK'=>$aLK,'SS'=>$aSS,'SW'=>$aSW,'SP'=>$aSpalten);
  }
 }//ueber alle Segmente

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#'); $nNutzerZahl=0; $nNLF=0;
 if($bUTyp){
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $n=count($aD);
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
 if($Meld==''){$MTyp='Meld';
  if($MFlt!='') $Meld=str_replace('#F',$MFlt,MP_TxListQSuch); else $Meld=MP_TxListQGsmt;
 }
 $X.="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';

 //Ausgabe ueber alle Segmente
 $nSegNo=0;
 foreach($aDaten as $a){ //ueber alle Datensaetze
  if($nSegNo!=$a[0]){ //neuer Kopf
   if($nSegNo!=0) $X.="\n".'</div>';
   $nSegNo=$a[0]; $sSegNo=sprintf('%02d',$nSegNo); $aStru=$aSgs[$nSegNo];
   if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

   if(MP_SegTrnZeile) $X.="\n".'<div class="mpTrnZl">'.fMpTx(MP_TxSegment).': '.fMpTx(substr($aSeg[$nSegNo],0,1)!='*'?$aSeg[$nSegNo]:substr($aSeg[$nSegNo],1)).'</div>';

   $nSpalten=count($aStru['SP']); $nSpAuslass=0; $bMitID=$aStru['LF'][0]>0; $nFarb=1;
   $X.="\n\n".'<div class="mpDrTab">'; //Tabelle
   $sCss=(MP_DruckLFarbig?'mpTbZl2':'mpTbZlDr');
   $sZl="\n".' <div class="'.$sCss.'">'; //Kopfzeile
   for($j=($bMitID?0:1);$j<$nSpalten;$j++){
    $t=$aStru['FT'][$aStru['SP'][$j]];
    if(($t!='e'||MP_DruckLMailOffen)&&($t!='l'||!MP_LinkSymbol))
     $sZl.="\n".'  <div class="mpDrKz">'.fMpDt($aStru['FN'][$aStru['SP'][$j]]).'</div>';
    else $nSpAuslass++;
   }
   $sZl.="\n".' </div>';
   $X.=$sZl;
  }//neuer Kopf

  //Datenzeilen
  $a=$a[1]; $sZl=''; $sId=$a[0]; $sCss='mpTbZlDr';
  if(MP_DruckLFarbig){
   $sCss='mpTbZl'.$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
   if(array_search('k',$aStru['FT'])>0) if(isset($a[$nSpalten])) if($j=$a[$nSpalten]) $sCss.=' mpLstKat'.$j; //Kategorie aus Zusatzspalte
  }
  if($bMitID) $sZl="\n".'  <div class="mpTbDr mpTbSpM" style="'.$aStru['SS'][0].'">'.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId).'</div>';
  for($j=1;$j<$nSpalten;$j++){ //alle Spalten
   $k=$aStru['SP'][$j]; $t=$aStru['FT'][$k]; $sStil=''; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': $s=fMpBB(fMpDt($s)); break; //Text
     case 'm': if(MP_DruckLMemoLaenge==0) $s=fMpBB(fMpDt($s)); else $s=fMpBB(fMpKurzMemo(fMpDt($s),MP_DruckLMemoLaenge)); break; //Memo
     case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie/Postleitzahl
     case 'd': case '@': //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; break;
     case 'z': $sFS.=' mpTbSpM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sFS.=' mpTbSpR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein); $sFS.=' mpTbSpM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sFS.=' mpTbSpR';
      break;
     case 'l': $aI=explode('|',$s); $s=fMpDt(isset($aI[1])?$aI[1]:$aI[0]); break; //Link
     case 'e': if(!MP_SQL) $s=fMpDeCode($s); break; //eMail
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
      $s='<img src="'.MP_Url.$s.'" '.$aI[3].' border="0" alt="'.fMpDt($w).'" />'; $sFS.=' mpTbSpM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s;if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.substr($s,strpos($s,'/')+1).'" title="'.substr($s,strpos($s,'/')+1).'" />'; $sFS.=' mpTbSpM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' mpTbSpM';
      $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" alt="" />';
      $s=$v;
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&MP_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildKlein; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbSpM';
   }else $s='&nbsp;';
   if(($w=$aStru['SS'][$k])){$sStil=' style="'.str_replace('`,',';',$w).'"';}
   if(($t!='e'||MP_DruckLMailOffen)&&($t!='l'||!MP_LinkSymbol)) $sZl.="\n".'  <div class="mpTbDr'.$sFS.'"'.$sStil.'>'.$s.'</div>';
  }
  $X.="\n".' <div class="'.$sCss.'">'.$sZl."\n".' </div>';
 }//ueber alle Datensaetze
 if($nSegNo!=0){
  $X.="\n".'</div>';
  if(MP_CanoLink&&(!MP_DruckPopup||MP_CanoPopup)){$sC=fMpHref('suchen','1','',$sQ,MP_Segment,true); /* $p=strrpos($sC,'/'); if(!($p===false)) $sC=substr($sC,$p+1); */ define('MP_Canonical',str_replace('&amp;','&',$sC));}
 }
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