<?php
function fKalDaten($bListe,$bLimit=true){ //Daten bereitstellen
 global $kal_FeldName, $kal_FeldType, $kal_ListenFeld, $kal_Kategorien,
  $aKalDaten, $aKalSpalten; //neu angelegt
 //definiert werden: KAL_IFilter, KAL_SuchParam, KAL_Meldung, KAL_AktuelleId, KAL_LaufendeId, KAL_Saetze, KAL_SessionOK, KAL_MetaKey, KAL_MetaDes, KAL_Meldung,

 $Et=''; $Em=''; $Es='Fehl'; $sQ=''; $bSes=false; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL;

 //SQL-Verbindung öffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){$GLOBALS['oKalDbO']=$DbO; if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);} else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 //Session prüfen
 if(!$sSes=KAL_Session) if(defined('KAL_NeuSession')) $sSes=KAL_NeuSession;
 //if(KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeLogin||KAL_NEingabeAnders)
 if($sSes=substr($sSes,17,12)){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }
  }else $Et=KAL_TxSessionZeit;
 }
 define('KAL_SessionOK',$bSes); if($bSes&&KAL_NListeAnders) $kal_ListenFeld=$GLOBALS['kal_NListenFeld'];
 //IntervallFilter neu bestimmen
 $sIntervall=($bSes?KAL_NIntervall:KAL_Intervall); $sIv='';
 $sIntervallAnfang=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); $sIntervallEnde='';
 if(!isset($_GET['kal_Such'])){ //keine Schnellsuche
  if(isset($_GET['kal_Intervall'])) $sIv=fKalRq1($_GET['kal_Intervall']); elseif(isset($_POST['kal_Intervall'])) $sIv=fKalRq1($_POST['kal_Intervall']);
  if(isset($_POST['kal_Archiv'])) $sIv='@'; if(isset($_GET['kal_Aendern'])&&$sIv==''&&$sIntervall>'0') $sIv='0';
  if($sIntervall>'-'){ //Filter ist sichtbar
   if(strlen($sIv)>0&&$sIv!=$sIntervall){$sQ.='&amp;kal_Intervall='.$sIv; $sIntervall=$sIv;}
   if($sIntervall>'0'){ //filtern
    if($sIntervall<'a'){ //Normalintervall oder Archiv oder alle
     if($sIntervall!='@'&&$sIntervall!='[]'){ //kein Archiv oder Gesamtheit_für_InfoDetail
      if($sIntervall<'A'){$i=max((KAL_IvExakt?$sIntervall-1:$sIntervall),0); $k=0;} else{$i=0; $k=ord($sIntervall)-64;} //Normalintervall 1(Tag)...L(Jahr)
      $sIntervallEnde=date('Y-m-d',@mktime(8,8,8,date('m')+$k,date('d')+$i,date('Y')));
     }else{$sIntervallEnde=($sIntervall=='@'?$sIntervallAnfang:'99'); $sIntervallAnfang='00';}
    }else{ //Sonderintervalle
     $sIntervallAnfang=fKalSonderintervall($sIntervall);
     $sIntervallEnde=substr($sIntervallAnfang,10,10); $sIntervallAnfang=substr($sIntervallAnfang,0,10);
   }}
  }else /*Filter ist unsichtbar*/ if($sIv=='@'){$sQ.='&amp;kal_Intervall=@'; $sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';}
 }else /*bei Schnellsuche alle Termine*/ {if($sIntervall>'0') $sQ.='&amp;kal_Intervall=0'; $sIntervall='0';}

 //FeldFilter bestimmen, Listenspaltenfolge ermitteln
 $aKalSpalten=array(); $nFelder=count($kal_FeldName); $nDatFeld2=0; $nZeitFeld=0; $nZeitFeld2=0; $nWichtigFeld=0; $bSuchDat=false; $bASuch=false; $sSuchDat='';
 if(isset($_GET['kal_Such'])) $s=fKalRq($_GET['kal_Such']); elseif(isset($_POST['kal_Such'])) $s=fKalRq($_POST['kal_Such']); else $s='';
 if($bSuch=(strlen($s)>0)){ //Schnellsuche
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;kal_Such='.rawurlencode($s); $Em.=', '.htmlspecialchars($s,ENT_COMPAT,'ISO-8859-1'); $sDSep=(KAL_Datumsformat==1?'.':(KAL_Datumsformat==2||KAL_Datumsformat==3?'/':'-'));
  if(isset($_GET['kal_ASuch'])&&$_GET['kal_ASuch']=='1'||isset($_POST['kal_ASuch'])&&$_POST['kal_ASuch']=='1'){$sQ.='&amp;kal_ASuch=1'; $bASuch=true;}
  if(($p=strpos($s,$sDSep))&&($p=strpos($s,$sDSep,$p+1))&&(strlen($s)>$p+1)){ //Separator 2x enthalten
   $sSuch=fKalNormDatum($s); if(!strpos($sSuch,'00',5)){$bSuchDat=true; $sSuchDat=$s; $bSuch=false;} else $sSuch=$s;
  }else $sSuch=$s;
 }else $sSuch='';
 for($i=1;$i<$nFelder;$i++){ //ueber alle Felder
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  $aKalSpalten[$kal_ListenFeld[$i]]=($bLimit||(($t!='m'||KAL_DruckLMemo)&&$t!='g')?$i:-1); //unlimitierte Druckliste ohne Memos
  if(strlen($sSuch)==0){ //keine Schnellsuche
   if(isset($_GET['kal_'.$i.'F1'])) $s=fKalRq($_GET['kal_'.$i.'F1']); elseif(isset($_POST['kal_'.$i.'F1'])) $s=fKalRq($_POST['kal_'.$i.'F1']); else $s='';
   if(strlen($s)){ //erstes Suchfeld ausgefüllt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;kal_'.$i.'F1='.rawurlencode($s); $Em.=', '.$sFN;
    if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{ //falls Datum
     $a1Filt[$i]=fKalNormDatum($s); $a2Filt[$i]='';
     if($i==1){ //bei erstem Datumsfeld alle Termine
      $sIntervallAnfang='0'; $sIntervallEnde=''; $sIv='';
      if($sIntervall>'0'){$sIntervall='0'; if($p=strpos($sQ,'kal_Intervall=@')) $sQ=substr_replace($sQ,'0',$p+14,1);}
     }
    }
   }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&KAL_SessionOK?'':'N'); $a2Filt[$i]='';} //versteckt
   elseif($t=='u'&&$bSes&&isset($_GET['kal_Aendern'])&&!KAL_NAendernFremde){$a1Filt[$i]=substr($sSes,0,4);} //aendern fuer User
   if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRq($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRq($_POST['kal_'.$i.'F2']); else $s='';
   if(strlen($s)){ //zweites Suchfeld ausgefüllt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;kal_'.$i.'F2='.rawurlencode($s); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
    if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
    elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
    if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormDatum($s);
   }
   if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRq($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRq($_POST['kal_'.$i.'F3']); else $s='';
   if(strlen($s)){ //drittes Suchfeld ausgefüllt
    if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $sQ.='&amp;kal_'.$i.'F3='.rawurlencode($s); if(!strpos($Em,$sFN)) $Em.=', '.$sFN;
    $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s));
   }
  }//$sSuch
  if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; //2. Datum suchen
  if($t=='z'){if($nZeitFeld==0) $nZeitFeld=$i; elseif($nZeitFeld2==0) $nZeitFeld2=$i;} //1. und 2. Zeitfeld suchen
  if($t=='j'&&strtoupper($sFN)=='WICHTIG') $nWichtigFeld=$i;
 }
 define('KAL_IFilter',$sIntervall); /*für Filteranzeige in Terminliste*/ if($sIv=='@') $sIntervall='@';
 if($bSuchDat){$a1Filt[1]=$sSuch; $a2Filt[1]='';} //Schnellsuche nach Datum
 $aKalSpalten[0]=0; ksort($aKalSpalten);
 if(in_array(-1,$aKalSpalten)){$j=count($aKalSpalten); for($i=$j-1;$i>0;$i--) if($aKalSpalten[$i]<0) array_splice($aKalSpalten,$i,1);}
 $nSpalten=count($aKalSpalten); define('KAL_SuchParam',$sQ);

 //Sortierspalte bestimmen
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_ListenIndex);
 $p=0; for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='@'&&$kal_FeldName[$i]!='ZUSAGE_BIS') $p=$i;
 if((isset($_GET['kal_Neu'])||isset($_POST['kal_Neu']))&&$p>0){
  $nIndex=$p; $_GET['kal_Index']=$p; $_POST['kal_Index']=$p; $_GET['kal_Rueck']=1; $_POST['kal_Rueck']=1;
 }

 if($bListe){
  if($bAktuSuche=(KAL_Aktuelles&&($nIndex==1))){$sAktuDt='9'; $sAktuZt='9';}; //laufendes/aktuelles Ereignis vorbereiten
  if(($bLfndSuche=(KAL_Laufendes&&($nIndex==1)))||$bAktuSuche){$sJetztDat=date('Y-m-d'); $sJetztUhr=date('H:i'); $sGestern=date('Y-m-d',time()-86400);}
  $nKatPos=array_search('k',$kal_FeldType); $nKapPos=0; //Kategorienspalte und Kapazitaetsspalte vorbereiten
  if(KAL_ListenZusagZ>0&&(KAL_GastLZusagZ||$bSes)||KAL_ListenZusagS>0&&(KAL_GastLZusagS||$bSes)) $nKapPos=(int)array_search('KAPAZITAET',$kal_FeldName);
 }else{
  $sId=(isset($_GET['kal_Nummer'])?(int)$_GET['kal_Nummer']:0); $bAktuSuche=false; $bLfndSuche=false; $nKatPos=0; $nKapPos=0;
  if((KAL_DetailZusage>0||array_search('#',$kal_FeldType))&&(KAL_GastDZusage||$bSes)&&(KAL_DetailZusagS>0||strlen(KAL_TxDetailZusagZMuster)>0)) $nKapPos=(int)array_search('KAPAZITAET',$kal_FeldName);
 }
 //Daten holen
 $aTmp=array(); $aIdx=array(); $sAktuId='A;'; $sLfndId='L;'; $nVPos=array_search('v',$kal_FeldType); //ListenDaten
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //über alle Datensätze
   $a=explode(';',rtrim($aD[$i])); $nId=(int)$a[0]; $sOnl=$a[1]; array_splice($a,1,1);
   $b=false; $sADt=substr($a[1],0,10); $sEDt=$sADt; if(KAL_AendernLoeschArt==3&&$sOnl=='3') $sOnl='1';
   if(KAL_EndeDatum&&$nDatFeld2>0) if(!$sEDt=substr($a[$nDatFeld2],0,10)) $sEDt=$sADt;
   if(!$bSuch){ //keine Schnellsuche
    $b=(((KAL_EndeDatum?$sEDt:$sADt)>=$sIntervallAnfang||$bASuch)&&($sOnl=='1'||$bSes&&isset($_GET['kal_Aendern']))); //kommend oder laufend
    if($b&&$sIntervall>'0') if($sADt>$sIntervallEnde||($sIntervall=='@'&&$sEDt>$sIntervallEnde)) $b=false; //Intervallfilter
    if($b&&is_array($a1Filt)){
     reset($a1Filt);
     foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
      $t=($kal_FeldType[$j]); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
      if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
       if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
       if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
      }elseif($t=='d'||$t=='@'){ //Datum
       if($j==1&&KAL_EndeDatum){ //Termindatum
        if(empty($w)){if($sADt>$v||$sEDt<$v) $b=false;} elseif($sEDt<$v||$sADt>$w) $b=false;
       }else{ //sonstiges normales Datum
        $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
       }
      }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
       $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
       $s=floatval(str_replace(',','.',$a[$j]));
       if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
      }elseif($t=='o'){
       if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
       if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $b=false;
      }elseif($t=='u'){
       if($k=strlen($w)){if(sprintf('%0d',$a[$j])==$w) $b2=true; else $b2=false;} else $b2=false;
       if(!(sprintf('%0d',$a[$j])==$v||$b2)) $b=false;
      }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $b=false;}}
     }
    }
    if($b&&is_array($a3Filt)){ //Suchfiltern 3
     reset($a3Filt); foreach($a3Filt as $j=>$v) if($kal_FeldType[$j]!='u'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}}elseif(sprintf('%0d',$a[$j])==$v){$b=false; break;}
    }
    if($nWichtigFeld>0&&strtoupper($a[$nWichtigFeld])=='J') $b=true;
   }
   if($b==false&&($bSuch||$bSuchDat)){//Schnellsuche
    if(((KAL_EndeDatum?$sEDt:$sADt)>=$sIntervallAnfang||$bASuch)&&$sOnl=='1') for($j=1;$j<$nFelder /*$nSpalten */;$j++){ //kommend oder laufend
     $t=$kal_FeldType[$j /* $aKalSpalten[$j] */];
     if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='g') if(stristr($a[$j /* $aKalSpalten[$j] */],(!$bSuchDat?$sSuch:$sSuchDat))){$b=true; break;}
    }
    if($nVPos) if(!KAL_NVerstecktSehen||!KAL_SessionOK) if($a[$nVPos]=='J') $b=false; //versteckte
   }
   if($b){ //Datensatz gueltig
    $aTmp[$nId]=array($nId);
    if($bListe){
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aKalSpalten[$j]]));
     if($nKatPos>0) if($w=$a[$nKatPos]) if($w=$kal_Kategorien[$w]) $aTmp[$nId]['Kat']=$w; //Kategoriezusatzspalte
     if($nKapPos>0) $aTmp[$nId]['KAP']=$a[$nKapPos]; //Kapazitaetsspalte
    }elseif($nId==$sId){
     for($j=1;$j<$nFelder;$j++) $aTmp[$nId][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$j]));
     if($nKapPos>0) $aTmp[$nId]['KAP']=$a[$nKapPos]; //Kapazitaetsspalte
    }
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
    if($bAktuSuche||$bLfndSuche){ //aktuelle Ereignisse prüfen
     if((KAL_AktuZeit||KAL_LfndZeit)&&$nZeitFeld>0) $sAZt=$a[$nZeitFeld]; else $sAZt='';
     if(KAL_AktuEnde||KAL_LfndEnde){
      if($nDatFeld2>0) $sEDt=substr($a[$nDatFeld2],0,10); else $sEDt='';
      if((KAL_AktuZeit||KAL_LfndZeit)&&$nZeitFeld2>0) $sEZt=$a[$nZeitFeld2]; else $sEZt='';
     }else{$sEDt=''; $sEZt='';}
     if($bAktuSuche){
      if(KAL_AktuEnde&&$sEDt>=$sJetztDat){
       if(KAL_AktuZeit&&$sEDt==$sJetztDat){ //Endzeit bei heute berücksichtigen
        if($sEZt>=$sJetztUhr||$sEZt==''){ //Endzeit später oder ohne Endzeit
         if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
        }
       }else{if($sADt>$sAktuDt||KAL_AktuZeit&&$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}}
      }//AktuEnde
      if($sADt>=$sJetztDat){ //kommendes Anfangsdatum
       if(KAL_AktuZeit&&$sADt==$sJetztDat){ //Zeit bei heute berücksichtigen
        if($sAZt>=$sJetztUhr||$sAZt==''||KAL_AktuEnde&&$sEDt==''&&$sEZt>''&&($sEZt>=$sJetztUhr||$sEZt<$sAZt)){
         if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
        }
       }else{if($sADt>$sAktuDt||KAL_AktuZeit&&$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}}
      }elseif($sADt==$sGestern&&$sEDt==''&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr){
       if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
      }//AktuAnfang
     }//AktuSuche
     if($bLfndSuche){
      if($sADt<$sJetztDat){ //Anfangsdatum früher
       if(KAL_LfndEnde) if($sEDt==$sJetztDat&&(!KAL_LfndZeit||$sEZt>=$sJetztUhr||$sEZt=='')||$sEDt==''&&$sADt==$sGestern&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr||$sEDt>$sJetztDat) $sLfndId.=$nId.';';
      }elseif($sADt==$sJetztDat){ //Anfangdatum heute
       if(!KAL_LfndZeit||$sAZt==''||$sAZt<=$sJetztUhr&&KAL_LfndEnde&&($sEDt>=$sJetztDat||$sEZt>=$sJetztUhr||($sEZt>''&&$sEZt<$sAZt))) $sLfndId.=$nId.';';
      }else $bLfndSuche=false; //Anfangsdatum später
     }//LfndSuche
    }//AktuSuche||LfndSuche
   }//gueltig
  }//$nSaetze
 }elseif($DbO){ //SQL-Daten
  if($bSuchDat){$sSsIvAnf=$sIntervallAnfang; $sIntervallAnfang='0';} //Schnellsuche nach Datum
  if(!$bSuch){ //keine Schnellsuche
   if($sIntervallAnfang){
    if($nDatFeld2==0||!KAL_EndeDatum) $s=' AND kal_1>"'.$sIntervallAnfang.'"';
    else{
     if($sIntervall!='@') $s=' AND(kal_'.$nDatFeld2.'>"'.$sIntervallAnfang.'" OR kal_1>"'.$sIntervallAnfang.'")';
     else $s.=' AND kal_'.$nDatFeld2.'<="'.$sIntervallEnde.'~"';
    }
    if($sIntervall>'0') $s.=' AND kal_1<="'.$sIntervallEnde.'~"';
   }else $s='';
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
    $s.=' AND(kal_'.$j; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
     $s.=' LIKE "%'.fKalDtCoder($v).'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.fKalDtCoder($w).'%"';
    }elseif($t=='d'||$t=='@'){
     if($j==1&&KAL_EndeDatum){ //Termindatum
      if(empty($w)){$s.='<"'.$v.'~" AND kal_'.($nDatFeld2==0?1:$nDatFeld2).'>"'.$v.'" OR kal_'.$j.' LIKE "'.$v.'%"';} // nur 1 Wert
      else{$s.=' BETWEEN "'.$v.'" AND "'.$w.'~" OR kal_'.($nDatFeld2==0?1:$nDatFeld2).' BETWEEN "'.$v.'" AND "'.$w.'~"';}
     }else{if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';} //sonstiges Datum
    }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
     $v=str_replace(',','.',$v);
     if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
    }elseif($t=='o'){
     $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
    }elseif($t=='u'){
     $s=substr($s,0,strrpos($s,'(kal_')).'(CONVERT(kal_'.$j.',INTEGER)="'.$v.'"'; if(strlen($w)) $s.=' OR CONVERT(kal_'.$j.',INTEGER)="'.$w.'"';
    }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
    $s.=')';
   }
   if(is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
    $t=$kal_FeldType[$j];
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x')
     $s.=' AND NOT(kal_'.$j.' LIKE "%'.fKalDtCoder($v).'%")';
    elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
    elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.$v.'")';
   }
   if($nWichtigFeld>0) $s.=' OR ((online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').') AND kal_'.$nWichtigFeld.'="J")';
  }
  if($bSuch||$bSuchDat){ //Schnellsuche
   if($bSuch) $s=''; if($bSuchDat){$sSuch=$sSuchDat; $sIntervallAnfang=$sSsIvAnf;}
   for($j=1;$j<$nFelder /* $nSpalten */ ;$j++){
    $t=$kal_FeldType[$j /* $aKalSpalten[$j] */];
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='g') $s.=' OR kal_'.$j /* $aKalSpalten[$j] */.' LIKE "%'.fKalDtCoder($sSuch).'%"';
   }
   $s=(!$bASuch?($nDatFeld2==0||!KAL_EndeDatum?' AND kal_1>"'.$sIntervallAnfang.'"':' AND(kal_'.$nDatFeld2.'>"'.$sIntervallAnfang.'" OR kal_1>"'.$sIntervallAnfang.'")'):'').' AND('.substr($s,4).')';
   if($nVPos) if(!KAL_NVerstecktSehen||!KAL_SessionOK) $s.=' AND kal_'.$nVPos.'<>"J"'; //versteckte
  }
  $t=''; $nDatPos=0; $nDatPos2=0; $nZeitPos=0; $nZeitPos2=0; $i=$nSpalten; $bIdx=true; $nIdx=$kal_ListenFeld[$nIndex];
  if($bListe){ //besondere Felder ergaenzen
   for($j=1;$j<$nSpalten;$j++){
    $k=$aKalSpalten[$j]; $t.=',kal_'.$k; if($nIndex==$k) $bIdx=false;
    if($k==1) $nDatPos=$j; elseif($k==$nDatFeld2) $nDatPos2=$j; elseif($k==$nZeitFeld) $nZeitPos=$j; elseif($k==$nZeitFeld2) $nZeitPos2=$j;
   }
   if($nKatPos>0){$t.=',kal_'.$nKatPos; $nKatPos=$i++;}
   if($nKapPos>0){$t.=',kal_'.$nKapPos; $nKapPos=$i++;}
   if($nDatPos==0){$t.=',kal_1'; $nDatPos=$i++;}
   if($nDatFeld2>0&&$nDatPos2==0){$t.=',kal_'.$nDatFeld2; $nDatPos2=$i++;}
   if($nZeitFeld>0&&$nZeitPos==0){$t.=',kal_'.$nZeitFeld; $nZeitPos=$i++;}
   if($nZeitFeld2>0&&$nZeitPos2==0){$t.=',kal_'.$nZeitFeld2; $nZeitPos2=$i++;}
   if($bIdx&&$nIndex>0){$t.=',kal_'.$nIndex; $kal_ListenFeld[$nIndex]=$i++;}
  }else{if($nIndex>1) $t.=',kal_'.$nIndex; $kal_ListenFeld[$nIndex]=1;}
  $o='(online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').')'; if($bSes&&isset($_GET['kal_Aendern'])) $o='online>""';
  if($rR=$DbO->query('SELECT id'.$t.' FROM '.KAL_SqlTabT.' WHERE '.$o.$s.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
   $i=0;
   while($a=$rR->fetch_row()){
    $nId=(int)$a[0]; $aTmp[$nId]=array($nId);
    if($bListe){
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId][]=str_replace("\r",'',$a[$j]);
     if($nKatPos>0) if($j=$a[$nKatPos]) if($j=$kal_Kategorien[$j]) $aTmp[$nId]['Kat']=$j; //Kategoriezusatzspalte
     if($nKapPos>0) $aTmp[$nId]['KAP']=$a[$nKapPos]; //Kapazitaetsspalte
    }
    if($nIndex==1) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',++$i); //nach Datum
    elseif($nIndex>1){ //andere Sortierung
     $s=strtoupper(strip_tags($a[$kal_ListenFeld[$nIndex]])); $t=$kal_FeldType[$nIndex];
     for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
      if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
     if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
     elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
     $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i);
    }
    elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr.
    if($bAktuSuche||$bLfndSuche){ //aktuelle Ereignisse prüfen
     $sADt=substr($a[$nDatPos],0,10);
     if((KAL_AktuZeit||KAL_LfndZeit)&&$nZeitPos>0) $sAZt=$a[$nZeitPos]; else $sAZt='';
     if(KAL_AktuEnde||KAL_LfndEnde){
      if($nDatPos2>0) $sEDt=substr($a[$nDatPos2],0,10); else $sEDt='';
      if((KAL_AktuZeit||KAL_LfndZeit)&&$nZeitPos2>0) $sEZt=$a[$nZeitPos2]; else $sEZt='';
     }else{$sEDt=''; $sEZt='';}
     if($bAktuSuche){
      if(KAL_AktuEnde&&$sEDt>=$sJetztDat){
       if(KAL_AktuZeit&&$sEDt==$sJetztDat){ //Endzeit bei heute berücksichtigen
        if($sEZt>=$sJetztUhr||$sEZt==''){ //Endzeit später oder ohne Endzeit
         if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
        }
       }else{if($sADt>$sAktuDt||KAL_AktuZeit&&$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}}
      }//AktuEnde
      if($sADt>=$sJetztDat){ //kommendes Anfangsdatum
       if(KAL_AktuZeit&&$sADt==$sJetztDat){ //Zeit bei heute berücksichtigen
        if($sAZt>=$sJetztUhr||$sAZt==''||KAL_AktuEnde&&$sEDt==''&&$sEZt>''&&($sEZt>=$sJetztUhr||$sEZt<$sAZt)){
         if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
        }
       }else{if($sADt>$sAktuDt||KAL_AktuZeit&&$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}}
      }elseif($sADt==$sGestern&&$sEDt==''&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr){
       if($sADt>$sAktuDt||$sAZt>$sAktuZt) $bAktuSuche=false; else{$sAktuId.=$nId.';'; $sAktuDt=$sADt; $sAktuZt=$sAZt;}
      }//AktuAnfang
     }//AktuSuche
     if($bLfndSuche){
      if($sADt<$sJetztDat){ //Anfangsdatum früher
       if(KAL_LfndEnde) if($sEDt==$sJetztDat&&(!KAL_LfndZeit||$sEZt>=$sJetztUhr||$sEZt=='')||$sEDt==''&&$sADt==$sGestern&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr||$sEDt>$sJetztDat) $sLfndId.=$nId.';';
      }elseif($sADt==$sJetztDat){ //Anfangdatum heute
       if(!KAL_LfndZeit||$sAZt==''||$sAZt<=$sJetztUhr&&KAL_LfndEnde&&($sEDt>=$sJetztDat||$sEZt>=$sJetztUhr||($sEZt>''&&$sEZt<$sAZt))) $sLfndId.=$nId.';';
      }else $bLfndSuche=false; //Anfangsdatum später
     }//LfndSuche
    }//AktuSuche||LfndSuche
   }$rR->close();
   if(!$bListe){ //den Datensatz holen
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
     if($a=$rR->fetch_row()){
      $rR->close(); for($j=2;$j<=$nFelder;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]);
      if($nKapPos>0) $aTmp[$sId]['KAP']=(isset($a[$nKapPos+1])?$a[$nKapPos+1]:''); //Kapazitaetsspalte
   }}}
  }else $Et=KAL_TxSqlFrage;
  $kal_ListenFeld[$nIndex]=$nIdx;
 }

 if($Et==''){
  $Es='Meld';
  if(substr($Em,0,1)==',') $Et=str_replace('#S',substr($Em,1),KAL_TxListSuch); else $Et=KAL_TxListGsmt;
  if($sIntervall=='@') $Et.=' ('.KAL_TxArchiv.')';
 }
 define('KAL_Meldung','<p class="kal'.$Es.'">'.fKalTx($Et).'</p>'); define('KAL_AktuelleId',$sAktuId); define('KAL_LaufendeId',$sLfndId);

 //Sortieren
 $sRw=''; if(isset($_GET['kal_Rueck'])) $sRw=fKalRq1($_GET['kal_Rueck']); elseif(isset($_POST['kal_Rueck'])) $sRw=fKalRq1($_POST['kal_Rueck']);
 if($sRw!='1'){ //vorwaerts
  if($nIndex!=1) asort($aIdx); //nach Feldern
  else if(strlen($sRw)<=0&&(KAL_Rueckwaerts&&$sIntervall!='@'||KAL_ArchivRueckwaerts&&$sIntervall=='@')) arsort($aIdx);
 }else arsort($aIdx);

 $aKalDaten=array(); reset($aIdx); define('KAL_Saetze',count($aIdx));
 if($bListe){ //Liste limitieren nach Startposition und Stopposition der Seite
  if(isset($_GET['kal_Start'])&&$bLimit) $nStart=(int)$_GET['kal_Start']; else $nStart=1;
  $k=0; $nStop=($bLimit?$nStart+KAL_ListenLaenge:9999);
  foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aKalDaten[]=$aTmp[$i];
  if(strlen(KAL_TxLMetaKey)>0) define('KAL_MetaKey',KAL_TxLMetaKey);
  if(strlen(KAL_TxLMetaDes)>0) define('KAL_MetaDes',KAL_TxLMetaDes);
  if(strlen(KAL_TxLMetaTit)>0) define('KAL_MetaTit',KAL_TxLMetaTit);
 }else{ //Detailarray mit Zusatzdaten ergaenzen
  $bGefunden=false; $nVorg=0; $nNachf=0; $nPos=0; $aKalDaten[0]=array();
  foreach($aIdx as $i=>$xx){
   if(!$bGefunden){
    $nPos++; if($i!=$sId) $nVorg=$i; else{$bGefunden=true; $aKalDaten[0]=$aTmp[$i];}
   }elseif($nNachf==0) $nNachf=$i;
  }
  $aKalDaten[1]=$nVorg; $aKalDaten[2]=$nPos; $aKalDaten[3]=$nNachf; $sTitel=''; $bSuchTit=true;
  for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='t'){ //ueber alle TextFelder
   $s=fKalDt(isset($aKalDaten[0][$i])?$aKalDaten[0][$i]:'');
   if($kal_FeldName[$i]=='META-KEY'&&($s||($s=KAL_TxDMetaKey))) define('KAL_MetaKey',$s);
   elseif($kal_FeldName[$i]=='META-DES'&&($s||($s=KAL_TxDMetaDes))) define('KAL_MetaDes',$s);
   elseif($kal_FeldName[$i]=='TITLE'&&$s){$sTitel=$s; $bSuchTit=false;}
   elseif($bSuchTit){$bSuchTit=false;
    if(KAL_DTitelQuelle>0){
     for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
     $s=str_replace('  ',' ',str_replace("\n",' ',str_replace("\r",'',$s)));
     if(KAL_DTitelQuelle==1){ //erste n Worte
      $nPos=-1; $l=strlen($s); $j=KAL_DTitelWL; while($j--) $nPos=max(@strpos($s.' ',' ',min($nPos+2,$l)),$nPos);
      if($nPos>0) $sTitel=trim(substr($s,0,$nPos)); else $sTitel=$s;
     }else $sTitel=trim(substr($s,0,KAL_DTitelZL)); //erste n Zeichen
    }else $sTitel=KAL_TxDMetaTit; // fester Ersatztitel
  }}
  if($sTitel) define('KAL_MetaTit',$sTitel); $sTitel='';
  if(!defined('KAL_MetaKey')) if(strlen(KAL_TxDMetaKey)>0) define('KAL_MetaKey',KAL_TxDMetaKey);
  if(!defined('KAL_MetaDes')) if(strlen(KAL_TxDMetaDes)>0) define('KAL_MetaDes',KAL_TxDMetaDes);
 }
 return true;
}

function fKalDtCoder($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
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

function fKalSonderintervall($w){ //Sonderintervall bilden
 $s=''; $Dt=date('Y-m-d'); $J=(int)substr($Dt,0,4); $M=(int)substr($Dt,5,2);
 switch($w){
  case 'a': $n=date('w')-1; if($n<0) $n=6; $n=time()-$n*86400; $s=date('Y-m-d',$n).date('Y-m-d',$n+518400); break; //aktuelle Woche
  case 'd': $s=substr($Dt,0,8).'01'.substr($Dt,0,8).'31'; break; //aktueller Monat
  case 'g': $M-=(($M-1)%3); $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+2); break; //aktuelles Quartal
  case 'j': $M-=(($M-1)%6); $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+5); break; //aktuelles Halbjahr
  case 'm': $s=substr($Dt,0,5).'01-01'.substr($Dt,0,5).'12-31'; break; //aktuelles Jahr
  case 'c': $n=8-date('w'); if($n>7) $n=1; $n=time()+$n*86400; $s=date('Y-m-d',$n).date('Y-m-d',$n+518400); break; //nächste Woche
  case 'f': if(++$M>12){$M= 1; $J++;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M); break; //nächster Monat
  case 'i': $M-=(($M-1)%3)-3; if($M>12){$M=1; $J++;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+2); break; //nächstes Quartal
  case 'l': $M-=(($M-1)%6)-6; if($M>12){$M=1; $J++;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+5); break; //nächstes Halbjahr
  case 'o': $s=sprintf('%04d-01-01%04d-12-31',++$J,$J); break; //nächstes Jahr
  case 'b': $n=date('w')+6; if($n<7) $n=13;$n=time()-$n*86400; $s=date('Y-m-d',$n).date('Y-m-d',$n+518400); break; //vorige Woche
  case 'e': if(--$M<=0){$M=12; $J--;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M); break; //Vormonat
  case 'h': $M-=(($M-1)%3)+3; if($M<0){$M=10; $J--;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+2); break; //voriges Quartal
  case 'k': $M-=(($M-1)%6)+6; if($M<0){$M=10; $J--;} $s=sprintf('%04d-%02d-01%04d-%02d-31',$J,$M,$J,$M+5); break; //voriges Halbjahr
  case 'n': $s=sprintf('%04d-01-01%04d-12-31',--$J,$J); break; //voriges Jahr
 }
 return $s;
}
?>