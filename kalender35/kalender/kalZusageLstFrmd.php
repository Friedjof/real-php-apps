<?php
function fKalSeite(){ //Liste fremder Zusagetermine
 global $kal_FeldName, $kal_FeldType, $kal_NListenFeld, $kal_SpaltenStil, $kal_SortierFeld,
  $kal_NutzerFelder, $kal_Symbole, $kal_WochenTag;
 array_splice($kal_NutzerFelder,1,1); $nFelder=count($kal_NutzerFelder); $kal_NListenFeld[0]=1;
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 $bSes=false; $sSession=substr(KAL_Session,0,29); $nNId=0; $sNutzerEml=''; //Session pruefen
 if($sSes=substr($sSession,17,12)){
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nNId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $bSes=true;
      $a[2]=fKalDeCode($a[2]); $a[3]=fKalDeCode($a[3]); $a[4]=fKalDeCode($a[4]); $sNutzerEml=$a[4];
      for($j=5;$j<$nFelder;$j++){
       $a[$j]=str_replace('`,',';',$a[$j]);
       if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
      }
     }else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows==1){
      $bSes=true; $a=$rR->fetch_row(); array_splice($a,1,1);
      if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]); $sNutzerEml=$a[4];
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }else $Et=KAL_TxSessionUngueltig;

 //Sortierung und Startposition
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_ListenIndex);
 $sRueckw=fKalRq1(isset($_GET['kal_Rueck'])?$_GET['kal_Rueck']:'');
 $nStart=(isset($_GET['kal_Start'])?(int)$_GET['kal_Start']:1);

 $X=''; $Et=''; $Es='Fehl'; $sSuch=''; $sSuchTxt='';

 $sQ=''; $sQSuch=''; $bSuchDat=false; if($nIndex!=KAL_ListenIndex) $sQ.='&amp;kal_Index='.$nIndex; //1-Index
 if($sRueckw=='1'&&($nIndex!=1||!KAL_Rueckwaerts)) $sQ.='&amp;kal_Rueck=1'; //2-Rueck
 elseif($sRueckw==='0'&&$nIndex==1&&KAL_Rueckwaerts) $sQ.='&amp;kal_Rueck=0';
 if($s=fKalRq(isset($_GET['kal_ZSuch'])?$_GET['kal_ZSuch']:(isset($_POST['kal_ZSuch'])?$_POST['kal_ZSuch']:''))){ //3-Suchparameter
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQSuch='&amp;kal_ZSuch='.rawurlencode($s); $sQ.=$sQSuch; $sSuchTxt=$s;
  $sDSep=(KAL_Datumsformat==1?'.':(KAL_Datumsformat==2||KAL_Datumsformat==3?'/':'-'));
  if(($p=strpos($s,$sDSep))&&($p=strpos($s,$sDSep,$p+1))&&strlen($s)<11){ //Separator 2x enthalten
   $sSuch=fKalNormDatum($s); if(!strpos($sSuch,'00',2)) $bSuchDat=true; else $sSuch=substr($sSuch,strpos($sSuch,'-'));
  }else $sSuch=$s;
 }else $sSuch='';

 $aZusagen=array(); $aKalDaten=array(); $aIdx=array(); $aKalSpalten=array(); $nFelder=count($kal_FeldName); $nSaetze=0; $nSpalten=0;
 if($bSes){
  $nAnzPos=0; for($j=9;$j<=$nZusageFelder;$j++) if($kal_ZusageFelder[$j]=='ANZAHL') $nAnzPos=$j;
  if(!KAL_SQL){ //Textdaten
   $aZ=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aZ);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aZ[$i])); $a[8]=fKalDeCode($a[8]); $bOk=true;
    if(!empty($sSuch)){
     if(!$bSuchDat){
      $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
     }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
    }
    if($bOk){$nId=(int)$a[1]; $n=($nAnzPos?(int)$a[$nAnzPos]:1); if(isset($aZusagen[$nId])) $aZusagen[$nId]+=$n; else $aZusagen[$nId]=$n;}
  }}elseif($DbO){ //SQL
   //if(KAL_ZusageListeStatus==1) $s='1'; elseif(KAL_ZusageListeStatus==2) $s='1" OR aktiv="2'; else $s='1" OR aktiv>"!';
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' ORDER BY nr')){
    while($a=$rR->fetch_row()){
     $bOk=true;
     if(!empty($sSuch)){
      if(!$bSuchDat){
       $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
      }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
     }
     if($bOk){$nId=(int)$a[1]; $n=($nAnzPos>=9?$a[$nAnzPos]:1); if(isset($aZusagen[$nId])) $aZusagen[$nId]+=$n; else $aZusagen[$nId]=$n;}
    }$rR->close();
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }

  for($i=1;$i<$nFelder;$i++){ //Feldfolge aufbereiten
   $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
   if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   $aKalSpalten[$kal_NListenFeld[$i]]=$i;
  }
  $aKalSpalten[0]=0; ksort($aKalSpalten);
  if(in_array(-1,$aKalSpalten)){$j=count($aKalSpalten); for($i=$j-1;$i>0;$i--) if($aKalSpalten[$i]<0) array_splice($aKalSpalten,$i,1);}
  $nSpalten=count($aKalSpalten); $i=0;

  if(!$nNPos=array_search('u',$kal_FeldType)){$nNPos=1; $nNId='X';}
  if(!KAL_SQL){ //Termine holen
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); if($nNPos>1) $nNPos++;
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aD[$i])); $nId=(int)$a[0];
    if((int)$a[$nNPos]==$nNId&&isset($aZusagen[$nId])){
     array_splice($a,1,1); $aTmp[$nId]=array($nId);
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aKalSpalten[$j]]));
     if($nIndex==1) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
     elseif($nIndex>1){ //andere Sortierung
      $s=strtoupper(strip_tags($a[$nIndex])); $t=$kal_FeldType[$nIndex];
      for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
       if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
      if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
      elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
      $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
     }
     elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr
   }}
  }elseif($DbO){ //SQL-Termine holen
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE (kal_'.$nNPos.')+0="'.$nNId.'" ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
    while($a=$rR->fetch_row()){$nId=(int)$a[0];
     if(isset($aZusagen[$nId])){
      array_splice($a,1,1); $aTmp[$nId]=array($nId);
      for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace("\r",'',$a[$aKalSpalten[$j]]);
      if($nIndex==1) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',++$i); //nach Datum
      elseif($nIndex>1){ //andere Sortierung
       $s=strtoupper(strip_tags($a[$nIndex])); $t=$kal_FeldType[$nIndex];
       for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
        if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
       if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
       elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
       $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i);
      }
      elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr.
    }}
    $rR->close();
   }else $Et=KAL_TxSqlFrage;
  }

  if($sRueckw!='1'){ //Sortieren
   if($nIndex!=1) asort($aIdx); //nach Feldern
   else if(strlen($sRueckw)<=0&&(KAL_Rueckwaerts)) arsort($aIdx);
  }else arsort($aIdx);
  $k=0; $nStop=$nStart+KAL_ListenLaenge; // vereinzeln
  foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aKalDaten[]=$aTmp[$i];
  $sSes='&amp;kal_Session='.$sSes.'&amp;kal_Zentrum=1';
 }//bSes

 // Seitenausgabe
 if(KAL_NaviOben>0||KAL_NaviUnten>0) $sNavig=fKalNavigator($nStart,count($aIdx),$sQ,$sSes);
 if(KAL_NaviOben==1) $X.="\n".$sNavig;
 $X.="\n".'<div class="kalFilt">'.fKalSuchFilter($sSuchTxt,$sSes)."\n".'<div class="kalClear"></div>'."\n</div>\n";
 if(KAL_NaviOben==2) $X.="\n".$sNavig;
 if(!$bSes){$Et=KAL_TxSessionUngueltig; $Es='Fehl';}
 elseif(!$Et){if($sSuch) $Et=KAL_TxNfSuchErgebnis; else $Et=KAL_TxNfUebersicht; $Es='Meld';}
 $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).(KAL_Zusagen&&KAL_ZUser!='D'.'em'.'o'?'':' ('.'D'.'em'.'o'.'ve'.'rsi'.'on)').'</p>';
 if(KAL_NaviOben==3) $X.="\n".$sNavig;
 $X.="\n\n".'<div class="kalTabl">';

 $X.="\n".' <div class="kalTbZl0">'; $kal_FeldName[0]=KAL_TxNr; $aSpTitle=array(); //Kopfzeile ausgeben
 for($j=0;$j<$nSpalten;$j++){
  $k=$aKalSpalten[$j];
  if(!$kal_SortierFeld[$k]) $t='';
  else{
   if($k!=$nIndex){$t='e'; $w=''; $v='';} // $t-Iconart, $v-Rückwaerts, $w-Text: ab-/aufsteigend
   else{
    if($sRueckw!='1'&&!($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts)&&strlen($sRueckw)<=0)){
     $t='t'; $w=KAL_TxAbsteigend;
     if($sRueckw==='0'&&KAL_Rueckwaerts&&$nIndex==KAL_ListenIndex) $v=''; else $v='&amp;kal_Rueck=1';
    }else{$t='r'; $w=KAL_TxAufsteigend; $v=''; if($nIndex==KAL_ListenIndex&&(KAL_Rueckwaerts)) $v='&amp;kal_Rueck=0';}
   }
   $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
   $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;kal_Aktion=nzusagenfliste',5).$sSes.($k!=KAL_ListenIndex?'&amp;kal_Index='.$k:'').$v.($sQSuch?$sQSuch:'').'">'.$t.'</a>';
  }
  $sFN=$kal_FeldName[$k]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  $sFS=$kal_FeldType[$k]; if($sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M';
  $X.="\n".'  <div class="kalTbLst kalTbSp'.$sFS.'">'.fKalTx($sFN).$t.'</div>'; $aSpTitle[$k]=fKalTx($sFN).$t;
 }
 $X.="\n".' </div>';
 //alle Datenzeilen ausgeben
 $nFarb=1;
 if($nStart>1) $sQ='&amp;kal_Start='.$nStart.$sQ; //0-Start, 1-Index, 2-Rueck, 3-Suchparameter
 foreach($aKalDaten as $a){
  $sZl=''; $sTId=$a[0]; $sCSS='Dat'.$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
  $sZl.="\n".'  <div class="kalTbLst"><span class="kalTbLst">'.$aSpTitle[0].'</span>'.sprintf('%0'.KAL_NummerStellen.'d',$sTId).' <a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=zusagezeigen'.$sSes.'&amp;kal_Nummer='.$sTId.'&amp;kal_Zusagen=1'.$sQ.'">['.$aZusagen[$sTId].']</a></div>';
  for($j=1;$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; $sKMemo=''; $sStil=' kalTbLsL';
   if(($s=$a[$j])||strlen($s)>0){
    switch($t){
     case 't': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Gastkommentar
     case 'm': if(KAL_ListenMemoLaenge==0) $s=fKalBB(fKalDt($s)); else{$s=fKalBB(fKalDt(fKalKurzMemo($s,KAL_ListenMemoLaenge))); if(substr($s,-4)=='....'){$sKMemo=substr($s,0,-4); $s='....';}} break; //Memo
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie/Postleitzahl
     case 'd': case '@': $w=trim(substr($s,11)); $s=fKalAnzeigeDatum($s); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; else $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]);}
      }elseif($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sStil=' kalTbLsM'; break; //Uhrzeit
     case 'w': //Währung
      if(((float)$s)!=0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sStil=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': case '#': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein); $sStil=' kalTbLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if(((float)$s)!=0||!KAL_ZahlLeer){
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sStil=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'l': //Link
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalDt(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(KAL_LinkSymbol){$v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">'; $sStil=' kalTbLsM';}
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //eMail
      $s='<img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'">';
      $sStil=' kalTbLsM';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       if(KAL_NutzerListFeld>0){
        $s=KAL_TxAutorUnbekannt;
        if(!KAL_SQL){ //Textdaten
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nNutzerZahl=count($aD); $v=$nId.';'; $p=strlen($v);
         for($n=1;$n<$nNutzerZahl;$n++) if(substr($aD[$n],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$n])); array_splice($aN,1,1);
          if(!$s=$aN[KAL_NutzerListFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerListFeld<5&&KAL_NutzerListFeld>1) $s=fKalDeCode($s); $s=fKalDt($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=fKalDt($aN[KAL_NutzerListFeld])) $s=KAL_TxAutorUnbekannt;}
          else $s=KAL_TxAutorUnbekannt;
       }}}
      }else $s=KAL_TxAutor0000;
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.fKalDt($w).'" alt="'.fKalDt($w).'">'; $sStil=' kalTbLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sTId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
      if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
      $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'"></div>'; $sStil=' kalTbLsM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalDt(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil=' kalTbLsM';
      $s='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sStil=' kalTbLsM';
   }else $s='&nbsp;';
   if(strlen($sKMemo)>0) $s=$sKMemo.$s;
   if($sFN=='KAPAZITAET') $sStil=' kalTbLsM'; $sCss=''; if($w=$kal_SpaltenStil[$k]) $sCss=' style="'.$w.'"';
   $sZl.="\n".'  <div class="kalTbLst'.$sStil.'"'.$sCss.'><span class="kalTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>';
  }
  $X.="\n".' <div class="kalTbZl1">'.$sZl."\n".' </div>';
 }
 $X.="\n".'</div>';

 if(KAL_NaviUnten) $X.="\n".$sNavig;

 return $X;
}

function fKalNormDatum($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

//Navigator zum Blaettern
function fKalNavigator($nStart,$nCount,$sQry,$sSes){
 $nPgs=ceil($nCount/KAL_ListenLaenge); $nPag=ceil($nStart/KAL_ListenLaenge);
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 $X ="\n".'<div class="kalNavL">';
 $X.="\n".'<div class="kalSZhl">'.fKalTx(KAL_TxSeite).' '.$nPag.'/'.$nPgs.'</div>';
 $X.="\n".'<div class="kalNavi"><ul class="kalNavi">';
 $X.='<li class="kalNavL"><a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;kal_Aktion=nzusagenfliste',5).$sSes.$sQry.'" title="'.fKalTx(KAL_TxAnfang).'">|&lt;</a></li>';
 $sL='<li><a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;kal_Aktion=nzusagenfliste',5).$sSes.'&amp;kal_Start=';
 for($i=$nAnf;$i<=$nEnd;$i++){
  $X.=$sL.(($i-1)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxSeite).$i.'">'.($i!=$nPag?$i:'<b>'.$i.'</b>').'</a></li>';
 }
 $X.='<li class="kalNavR"><a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;kal_Aktion=nzusagenfliste',5).$sSes.'&amp;kal_Start='.(max($nPgs-1,0)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxEnde).'">&gt;|</a></li>';
 $X.='</ul></div>';
 $X.="\n".'<div class="kalClear"></div>';
 $X.="\n".'</div>';
 return $X;
}

function fKalSuchFilter($s,$sSes){ //Schnellsuchfilter zeichnen
if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
return '
<div class="kalSFlt'.(KAL_SuchFilter==4?'R':(KAL_SuchFilter==3?'L':'')).'">
<form class="kalFilt" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).'<input type="hidden" name="kal_Aktion" value="nzusagenfliste"><input type="hidden" name="kal_Session" value="'.substr($sSes,17,12).'"><input type="hidden" name="kal_Zentrum" value="1">
<div class="kalNoBr">'.fKalTx(KAL_TxSuchen).' <input class="kalSFlt" name="kal_ZSuch" value="'.fKalTx($s).'"> <input type="submit" class="kalKnopf" value="" title="'.fKalTx(KAL_TxSuchen).'"></div>
</form>
</div>';
}
?>