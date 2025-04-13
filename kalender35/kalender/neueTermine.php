<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./neueTermine.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$sKalHtmlVor=''; $sKalHtmlNach=''; $bKalOK=true; //Seitenkopf, Seitenfuss, Status

if(!strstr($sKalSelf,'/neueTermine.php')){ //includierter Aufruf
 if(defined('KAL_Version')){ //Variablen includiert
  define('KAL_NeueZiel',(KAL_NeueLink!=''?KAL_NeueLink:$sKalSelf));
 }else{ //Variablen nicht includiert
  $bKalOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>kalWerte.php</i> wurde nicht includiert!</b></p>';
 }
}else{//Script laeuft allein als neueTermine.php
 @include('kalWerte.php'); define('KAL_NeueZiel',(KAL_NeueLink==''?'kalender.php':KAL_NeueLink));
 if(defined('KAL_Version')){
  header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(KAL_Schablone){ //mit Seitenschablone
   $sKalHtmlNach=(file_exists(KAL_Pfad.'neueTermine.htm')?implode('',file(KAL_Pfad.'neueTermine.htm')):'');
   if($nKalJ=strpos($sKalHtmlNach,'{Inhalt}')){
    $sKalHtmlVor=substr($sKalHtmlNach,0,$nKalJ); $sKalHtmlNach=substr($sKalHtmlNach,$nKalJ+8); //Seitenkopf, Seitenfuﬂ
   }else{$sKalHtmlVor='<p style="color:#AA0033;">Layout-Schablone <i>neueTermine.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.$sKalHttp.KAL_Www.'kalStyles.css">'."\n\n";
  }
 }else{$bKalOK=false; echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bKalOK){ //Konfiguration eingelesen
 if(!KAL_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if(!defined('KAL_Url')) define('KAL_Url',$sKalHttp.KAL_Www); define('KAL_NeueSelf',$sKalSelf);
 //geerbte GET/POST-Parameter aufbewahren und einige Kalenderparameter ermitteln
 $sKalQry=''; $sKalHid=''; $sKalSession=''; $sKalSuchParam=''; $sKalIndex=''; $sKalRueck=''; $sKalStart=''; $sKalNummer='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['kal_Aktion'])) $sKalAktion=fKalRqN($_GET['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_GET['kal_Session'])&&$sKalAktion!='login') $sKalSession='&amp;kal_Session='.fKalRqN($_GET['kal_Session']);
  reset($_GET);
  if(!defined('KAL_Query')) foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }
 }else{ //bei POST
  if(isset($_POST['kal_Session'])&&$_POST['kal_Session']!='') $sKalSession='&amp;kal_Session='.fKalRqN($_POST['kal_Session']);
  reset($_POST);
  if(!defined('KAL_Query')) foreach($_POST as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_') if(is_string($sKalV)){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }
 }
 if(!defined('KAL_Query')) define('KAL_Query',$sKalQry); if(!defined('KAL_Hidden')) define('KAL_Hidden',$sKalHid);
 if(!defined('KAL_Session')) define('KAL_Session',$sKalSession);

 //Beginn der Ausgabe
 echo $sKalHtmlVor;
 if((KAL_DetailPopup||KAL_MailPopup)&&!defined('KAL_KalWin')){
  echo "\n".'<script>function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  define('KAL_KalWin',true);
 }
 if(KAL_NeuePopup&&(KAL_NeueTarget!='_self'||KAL_NeueTarget!='_parent'||KAL_NeueTarget!='_top')){
  echo "\n".'<script>function NeuWin(sURL){kalWin=window.open(sURL,"'.KAL_NeueTarget.'","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  define('KAL_NeueOnClk','" onclick="NeuWin(this.href);return false;');
 }else define('KAL_NeueOnClk','');

 echo "\n".'<div class="kalBox">'."\n";
 echo fKalNeueTrm();
 echo "\n".'</div>'."\n".$sKalHtmlNach;
}
echo "\n";

function fKalNeueTrm(){
 global $kal_FeldName, $kal_FeldType, $kal_NeueFeld, $kal_NeueLink, $kal_NeueStil, $kal_WochenTag, $kal_Symbole;

 $aA=array(); $aIdx=array(); $aSep=array(); $aKalSpalten=array(); $nFelder=count($kal_FeldName); $sQ=''; $sEintragDt='';
 $nDatFeld2=0; $nZeitFeld=0; $nZeitFeld2=0; $nVerstecktFeld=0; $nEintragFeld=0; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL;
 for($i=1;$i<$nFelder;$i++){ //ueber alle Felder
  $t=$kal_FeldType[$i]; $aKalSpalten[$kal_NeueFeld[$i]]=$i;
  if(isset($_GET['kal_'.$i.'F1'])) $s=fKalRqN($_GET['kal_'.$i.'F1']); elseif(isset($_POST['kal_'.$i.'F1'])) $s=fKalRqN($_POST['kal_'.$i.'F1']); else $s='';
  if(strlen($s)){ //erstes Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{$a1Filt[$i]=fKalNormDatN($s); $a2Filt[$i]='';} $sQ.='&amp;kal_'.$i.'F1='.rawurlencode($s);
  }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&(KAL_Session!='')?'':'N'); $a2Filt[$i]='';} //versteckt
  if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRqN($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRqN($_POST['kal_'.$i.'F2']); else $s='';
  if(strlen($s)){ //zweites Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
   elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
   if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormDatN($s); $sQ.='&amp;kal_'.$i.'F2='.rawurlencode($s);
  }
  if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRqN($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRqN($_POST['kal_'.$i.'F3']); else $s='';
  if(strlen($s)){ //drittes Suchfeld ausgefuellt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); $sQ.='&amp;kal_'.$i.'F3='.rawurlencode($s);
  }
  if($t=='v') $nVerstecktFeld=$i;
  if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; //2. Datum suchen
  if($t=='z'){if($nZeitFeld==0) $nZeitFeld=$i; elseif($nZeitFeld2==0) $nZeitFeld2=$i;} //1. und 2. Zeitfeld suchen
  if($t=='@') if($nEintragFeld==0) if($kal_FeldName[$i]!='ZUSAGE_BIS') $nEintragFeld=$i; //Eintragsdatum
 }
 $bAnzeigen=$nVerstecktFeld==0||(KAL_NVerstecktSehen&&KAL_Session!=''); //alle anzeigen oder einzeln pr¸fen
 $aKalSpalten[0]=0; ksort($aKalSpalten); $nSpalten=count($aKalSpalten);
 $sJetztDat=date('Y-m-d'); $sJetztUhr=date('H:i'); $sGestern=date('Y-m-d',time()-86400); $sStartDat='00';
 if(KAL_NeueFrist>0&&$nEintragFeld>0) $sStartDat=date('Y-m-d',time()-86400*KAL_NeueFrist);
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_NeueIndex);
 //Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //ueber alle Datens‰tze
   $a=explode(';',rtrim($aD[$i])); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1);
   if($bOK&&($bAnzeigen||$a[$nVerstecktFeld]!='J')){ //kein versteckter Termin
    $bOK=false; if($nEintragFeld>0) $sEintragDt=$a[$nEintragFeld]; else $sEintragDt='';
    if(KAL_NeueKommend){ //nur kommende
     $sADt=substr($a[1],0,10); if(KAL_NeueZeit&&$nZeitFeld>0) $sAZt=$a[$nZeitFeld]; else $sAZt='';
     if(KAL_NeueEnde){
      if($nDatFeld2>0) $sEDt=substr($a[$nDatFeld2],0,10); else $sEDt='';
      if(KAL_NeueZeit&&$nZeitFeld2>0) $sEZt=$a[$nZeitFeld2]; else $sEZt='';
      if($sEDt>$sJetztDat||$sEDt==$sJetztDat&&(!KAL_NeueZeit||($sEZt>=$sJetztUhr||$sEZt==''))) $bOK=true; //Enddatum aktuell
     }else{$sEDt=''; $sEZt='';}
     if($sADt==$sGestern&&$sEDt==''&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr) $bOK=true; //seit gestern laufend
     elseif($sADt==$sJetztDat){ //beginnt heute
      if(!KAL_NeueZeit||$sAZt>=$sJetztUhr||$sAZt=='') $bOK=true; //Startzeit sp‰ter oder ohne Startzeit
      elseif(KAL_NeueEnde&&$sEDt==''&&$sEZt>''&&($sEZt>=$sJetztUhr||$sEZt<$sAZt)) $bOK=true; //kein Enddatum aber Endzeit kunftig oder morgen
     }elseif($sADt>$sJetztDat) $bOK=true; //kommendes Anfangsdatum
    }else $bOK=true;
    if(KAL_NeueFrist>0&&$nEintragFeld>0) if($sStartDat>=$sEintragDt&&$sEintragDt>'') $bOK=false; //zu altes Eintragsdatum
    if($bOK&&is_array($a1Filt)){
     reset($a1Filt);
     foreach($a1Filt as $j=>$v) if($bOK){ //Suchfiltern 1-2
      $t=($kal_FeldType[$j]); $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
      if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
       if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
       if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $bOK=false;
      }elseif($t=='d'||$t=='@'){ //Datum
       if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //kein Termindatum
        $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $bOK=false;} elseif($s<$v||$s>$w) $bOK=false;
       }
      }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
       $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
       $s=floatval(str_replace(',','.',$a[$j]));
       if($w<=0){if($s!=$v) $bOK=false;} else{if($s<$v||$s>$w) $bOK=false;}
      }elseif($t=='o'){
       if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
       if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $bOK=false;
      }elseif($t=='u'){
       if($k=strlen($w)){if(sprintf('%0d',$a[$j])==$w) $b2=true; else $b2=false;} else $b2=false;
       if(!(sprintf('%0d',$a[$j])==$v||$b2)) $bOK=false;
      }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $bOK=false;}}
    }}
    if($bOK&&is_array($a3Filt)){ //Suchfiltern 3
     reset($a3Filt); foreach($a3Filt as $j=>$v) if($kal_FeldType[$j]!='u'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$bOK=false; break;}}elseif(sprintf('%0d',$a[$j])==$v){$bOK=false; break;}
    }
    if($bOK){ //gueltiger Termin
     $nId=(int)$a[0]; $aT=array($nId); $v='';
     for($j=1;$j<$nSpalten;$j++){
      $w=str_replace('\n ',"\n",str_replace('`,',';',$a[$aKalSpalten[$j]])); $aT[]=$w; $v.=substr($w,0,10).chr(255);
     }
     $aA[$nId]=$aT;
     if($nIndex==1) $aIdx[$nId]=$v.sprintf('%0'.KAL_NummerStellen.'d',$nId); //Datum
     elseif($nIndex>1){ //andere Sortierung
      $s=strtoupper(strip_tags($a[$nIndex])); $t=$kal_FeldType[$nIndex];
      for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
       if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
      if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
      elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
      $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
     }
     elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr
     if(KAL_NeueNachId||$nEintragFeld<=0) $aSep[$nId]=$nId;
     else $aSep[$nId]=$sEintragDt.chr(33).sprintf('%0'.KAL_NummerStellen.'d',$i);
    }
   }//versteckt
  }
 }else{ //SQL
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){
   if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);
   $f=''; $s=''; $nDatPos=0; $nDatPos2=0; $nZeitPos=0; $nZeitPos2=0; $nEintragPos=0; $i=$nSpalten;
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v) if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //Suchfiltern 1-2
    $s.=' AND(kal_'.$j; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
     $s.=' LIKE "%'.fKalNDtCode($v).'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.fKalNDtCode($w).'%"';
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
     $s.=' AND NOT(kal_'.$j.' LIKE "%'.fKalNDtCode($v).'%")';
    elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
    elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.$v.'")';
   }
   for($j=1;$j<$nSpalten;$j++){
    $k=$aKalSpalten[$j]; $f.=',kal_'.$k;
    if($k==1) $nDatPos=$j; elseif($k==$nDatFeld2) $nDatPos2=$j; elseif($k==$nZeitFeld) $nZeitPos=$j; elseif($k==$nZeitFeld2) $nZeitPos2=$j; elseif($k==$nEintragFeld) $nEintragPos=$j;
   }
   if($nDatPos==0){$f.=',kal_1'; $nDatPos=$i++;}
   if($nDatFeld2>0&&$nDatPos2==0){$f.=',kal_'.$nDatFeld2; $nDatPos2=$i++;}
   if($nZeitFeld>0&&$nZeitPos==0){$f.=',kal_'.$nZeitFeld; $nZeitPos=$i++;}
   if($nZeitFeld2>0&&$nZeitPos2==0){$f.=',kal_'.$nZeitFeld2; $nZeitPos2=$i++;}
   if($nEintragFeld>0&&$nEintragPos==0){$f.=',kal_'.$nEintragFeld; $nEintragPos=$i++;}
   if($rR=$DbO->query('SELECT id'.$f.' FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').')'.(KAL_NeueKommend?' AND(kal_1>"'.$sGestern.(KAL_NeueEnde&&$nDatFeld2>0?'" OR kal_'.$nDatFeld2.'>="'.$sJetztDat:'').'")':'').($bAnzeigen?'':' AND kal_'.$nVerstecktFeld.'<>"J"').$s.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
    while($a=$rR->fetch_row()){$bOK=false;
     if(KAL_NeueKommend){ //nur kommende
      $sADt=substr($a[$nDatPos],0,10); if(KAL_NeueZeit&&$nZeitPos>0) $sAZt=$a[$nZeitPos]; else $sAZt='';
      if(KAL_NeueEnde){
       if($nDatPos2>0) $sEDt=substr($a[$nDatPos2],0,10); else $sEDt='';
       if(KAL_NeueZeit&&$nZeitPos2>0) $sEZt=$a[$nZeitPos2]; else $sEZt='';
       if($sEDt>$sJetztDat||$sEDt==$sJetztDat&&(!KAL_NeueZeit||($sEZt>=$sJetztUhr||$sEZt==''))) $bOK=true; //Enddatum aktuell
      }else{$sEDt=''; $sEZt='';}
      if($sADt==$sGestern&&$sEDt==''&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr) $bOK=true; //seit gestern laufend
      elseif($sADt==$sJetztDat){ //beginnt heute
       if(!KAL_NeueZeit||$sAZt>=$sJetztUhr||$sAZt=='') $bOK=true; //Startzeit spaeter oder ohne Startzeit
       elseif(KAL_NeueEnde&&$sEDt==''&&$sEZt>''&&($sEZt>=$sJetztUhr||$sEZt<$sAZt)) $bOK=true; //kein Enddatum aber Endzeit k¸nftig oder morgen
      }elseif($sADt>$sJetztDat) $bOK=true; //kommendes Anfangsdatum
     }else $bOK=true;
     if($nEintragPos>0) $sEintragDt=$a[$nEintragPos]; else $sEintragDt='';
     if(KAL_NeueFrist>0&&$nEintragPos>0) if($sStartDat>=$sEintragDt&&$sEintragDt>'') $bOK=false; //zu altes Eintragsdatum
     if($bOK){//gueltiger Termin
      $nId=(int)$a[0]; $aT=array($nId); $v='';
      for($j=1;$j<$nSpalten;$j++){
       $w=str_replace('\n ',"\n",str_replace('`,',';',$a[$j])); $aT[]=$w; $v.=substr($w,0,10).chr(255);
      }
      $aA[$nId]=$aT;
      if($nIndex==1) $aIdx[$nId]=$v.sprintf('%0'.KAL_NummerStellen.'d',$nId);
      elseif($nIndex>1){ //andere Sortierung
       $s=strtoupper(strip_tags($a[$kal_NeueFeld[$nIndex]])); $t=$kal_FeldType[$nIndex];
       for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
        if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
       if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
       elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
       $aIdx[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i);
      }
      elseif($nIndex==0) $aIdx[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr.
      if(KAL_NeueNachId||$nEintragFeld<=0) $aSep[$nId]=$nId;
      else $aSep[$nId]=$sEintragDt.chr(33).sprintf('%0'.KAL_NummerStellen.'d',$nId);
     }
    }$rR->close();
 }}}//SQL

 //eigene Layoutzeile pruefen
 if($bEigeneZeilen=KAL_NeueEigeneZeilen&&file_exists(KAL_Pfad.'neueZeile.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'neueZeile.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }

 //Beginn Ausgabe
 if($nKapPos=(int)array_search('KAPAZITAET',$kal_FeldName)) $nKapPos=(int)array_search($nKapPos,$aKalSpalten);
 $X="\n".'<div class="kalTabN">'; $bNaechste=false; $aSpTitle=array(); $sKopf='';
 if(KAL_NeueKopf){
  if(!$bEigeneZeilen){ //Standardlayout
   $sKopf.="\n".' <div class="kalTbNZl0">';
   for($j=1;$j<$nSpalten;$j++){
    $s=''; $k=$aKalSpalten[$j];
    if($j==$kal_NeueFeld[$nIndex]) $s='&nbsp;<img class="kalSorti" src="'.KAL_Url.'grafik/sortiere.gif" alt="'.fKalTxN(KAL_TxSortieren).'">';
    $sFN=$kal_FeldName[$k]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
    $sFS=$kal_FeldType[$k]; if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M'; if($j==$nKapPos) $sFS='M';
    $sKopf.="\n".'  <div class="kalTbNLst kalTbNLs'.$sFS.'">'.fKalTxN($sFN).$s.'</div>'; $aSpTitle[$k]=fKalTxN($sFN).$s;
   }
   $sKopf.="\n".' </div>';
  }elseif(file_exists(KAL_Pfad.'neueKopf.htm')){ //eigene Kopfzeile
   $r=@implode('',@file(KAL_Pfad.'neueKopf.htm')); $s=strtolower($t);
   if(!strpos($s,'<body')&&!strpos($s,'<head')) $sKopf="\n".' <div class="kalTbNZl0">'."\n".$r."\n </div>";
  }
  $X.=$sKopf;
 }

 $sQzS='?'; $sQzZ='?'; if(defined('KAL_Self')&&strpos(KAL_Self,'?')) $sQzS='&amp;'; if(strpos(KAL_NeueZiel,'?')) $sQzZ='&amp;';

 arsort($aSep); $a=$aIdx; $aIdx=array(); $nNeuZhl=(KAL_NeueAnzahl>0?KAL_NeueAnzahl:99999);
 foreach($aSep as $k=>$xx){$aIdx[$a[$k]]=$k; if((--$nNeuZhl)<=0) break;} //vereinzeln

 if(!KAL_NeueRueckw) ksort($aIdx); else krsort($aIdx); $nFarb=1; //sortieren

 foreach($aIdx as $i){
  $a=$aA[$i]; $sId=$i; $sZl=''; if($bEigeneZeilen) $sZl=$sEigeneZeile; //eigenes Zeilenlayout
  for($j=1;$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'm': case 'g': if($j!=$nKapPos) $s=fKalBN(fKalTxN($s)); else{$s=(int)$s; $sFS=' kalTbNLsM';} break; //Text/Memo/Gastkommentar
     case 'a': case 'k': case 'o': case 'u': $s=fKalTxN($s); break; //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_NeueJahrhundert?substr($s,0,4):substr($s,2,2));
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; if($t=='@') $sFS=' kalTbNLsM';
      if($t=='d'){if(KAL_NeueMitWochentag) if(KAL_NeueMitWochentag<2) $s=fKalTxN($kal_WochenTag[$w]).'&nbsp;'.$s; else $s.='&nbsp;'.fKalTxN($kal_WochenTag[$w]);}
      elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sFS=' kalTbNLsM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sFS=' kalTbNLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTxN(KAL_TxJa); elseif($s=='N') $s=fKalTxN(KAL_TxNein); $sFS=' kalTbNLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sFS=' kalTbNLsR';
      break;
     case 'l': //Link
      $aL=explode('||',$s); $s=''; $sFS=' kalTbNLsM';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalTxN(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(KAL_LinkSymbol){$v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">'; $sFS=' kalTbNLsM';}
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLinkN($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //EMail
      $s='<a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.$sQzS.substr(KAL_Query.'&amp;',5)).'kal_Aktion=kontakt'.KAL_Session.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTxN(KAL_TxKontakt).'" alt="'.fKalTxN(KAL_TxKontakt).'"></a>';
      $sFS=' kalTbNLsM';
      break;
     case 's': $w=fKalTxN($s); //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">'; $sFS=' kalTbNLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
      if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $v=fKalTxN(substr($s,strpos($s,'-')+1,-4));
      $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$v.'" alt="'.$v.'"></div>'; $sFS=' kalTbNLsM';
      break;
     case 'f': // Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalTxN(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS=' kalTbNLsM';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
      $s='<a href="'.KAL_Url.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': $s=fKalTxN(KAL_TxJa); $sFS=' kalTbNLsM'; break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); $sFS=' kalTbNLsM'; break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sFS=' kalTbNLsM';
   }else $s='&nbsp;';
   if($sStil=$kal_NeueStil[$k]) $sStil=' style="'.$sStil.'"';
   if($kal_NeueLink[$k]>0) $s='<a class="kalNeue" href="'.KAL_NeueZiel.$sQzZ.substr(KAL_Query.'&amp;',5).'kal_Aktion=detail'.$sQ.KAL_Session.(KAL_NeueOnClk?'&amp;kal_Popup=1':'').'&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sId.'" target="'.KAL_NeueTarget.KAL_NeueOnClk.'" title="'.fKalTxN(KAL_TxDetail).'">'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="kalTbNLst'.$sFS.'"'.$sStil.'><span class="kalTbNLst">'.$aSpTitle[$k].'</span>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$kal_FeldName[$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if(KAL_NeueAbstand>0&&$bNaechste) $X.="\n".' <div class="kalTbNZlX" style="height:'.KAL_NeueAbstand.'px">&nbsp;</div>';
  if(KAL_NeueKpfWdh&&$bNaechste&&KAL_NeueKopf) $X.=$sKopf; $bNaechste=true;
  if(!$bEigeneZeilen){ //Standardlayout
   $X.="\n".' <div class="kalTbNZl'.$nFarb.'">'.$sZl."\n".' </div>'; if(--$nFarb<=0) $nFarb=2;
  }else{ //eigenes Layout
   $X.="\n".' <div class="kalTbNZl'.$nFarb.'">'.$sZl."\n".' </div>'; if(--$nFarb<=0) $nFarb=2;
  }
 }
 $X.="\n</div>\n";
 return $X;
}

function fKalNormDatN($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}
function fKalExtLinkN($s){
 if(!defined('KAL_ZSatzExtLink')||KAL_ZSatzExtLink==0) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode($s))));
 elseif(KAL_ZSatzExtLink==1) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s)))));
 elseif(KAL_ZSatzExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

//BB-Code zu HTML wandeln
function fKalBN($s){
 $v=str_replace("\n",'<br>',str_replace("\n ",'<br>',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
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
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}
function fKalNDtCode($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
}
function fKalTxN($sTx){ //TextKodierung
 if(KAL_Zeichensatz==0) return str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$sTx)))); elseif(KAL_Zeichensatz==2) return iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$sTx))))); else return htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
}
function fKalRqN($sTx){
 return stripslashes(str_replace('"',"'",trim($sTx)));
}
?>