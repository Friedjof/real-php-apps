<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./laufendeTermine.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$sKalHtmlVor=''; $sKalHtmlNach=''; $bKalOK=true; //Seitenkopf, Seitenfuss, Status

if(!strstr($sKalSelf,'/laufendeTermine.php')){ //includierter Aufruf
 if(defined('KAL_Version')){ //Variablen includiert
  define('KAL_LaufendeZiel',(KAL_LaufendeLink!=''?KAL_LaufendeLink:$sKalSelf));
 }else{ //Variablen nicht includiert
  $bKalOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>kalWerte.php</i> wurde nicht includiert!</b></p>';
 }
}else{//Script laeuft allein als laufendeTermine.php
 @include('kalWerte.php'); define('KAL_LaufendeZiel',(KAL_LaufendeLink==''?'kalender.php':KAL_LaufendeLink));
 if(defined('KAL_Version')){
  header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(KAL_Schablone){ //mit Seitenschablone
   $sKalHtmlNach=(file_exists(KAL_Pfad.'laufendeTermine.htm')?implode('',file(KAL_Pfad.'laufendeTermine.htm')):'');
   if($nKalJ=strpos($sKalHtmlNach,'{Inhalt}')){
    $sKalHtmlVor=substr($sKalHtmlNach,0,$nKalJ); $sKalHtmlNach=substr($sKalHtmlNach,$nKalJ+8); //Seitenkopf, Seitenfu�
   }else{$sKalHtmlVor='<p style="color:#AA0033;">Layout-Schablone <i>laufendeTermine.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.$sKalHttp.KAL_Www.'kalStyles.css">'."\n\n";
  }
 }else{$bKalOK=false; echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}

if($bKalOK){ //Konfiguration eingelesen
 if(!KAL_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if(!defined('KAL_Url')) define('KAL_Url',$sKalHttp.KAL_Www); define('KAL_LaufendeSelf',$sKalSelf);
 //geerbte GET/POST-Parameter aufbewahren und einige Kalenderparameter ermitteln
 $sKalQry=''; $sKalHid=''; $sKalSession=''; $sKalSuchParam=''; $sKalIndex=''; $sKalRueck=''; $sKalStart=''; $sKalNummer='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['kal_Aktion'])) $sKalAktion=fKalRqL($_GET['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_GET['kal_Session'])&&$sKalAktion!='login') $sKalSession='&amp;kal_Session='.fKalRqL($_GET['kal_Session']);
  reset($_GET);
  if(!defined('KAL_Query')) foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }
 }else{ //bei POST
  if(isset($_POST['kal_Session'])&&$_POST['kal_Session']!='') $sKalSession='&amp;kal_Session='.fKalRqL($_POST['kal_Session']);
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
 if(KAL_LaufendePopup&&(KAL_LaufendeTarget!='_self'||KAL_LaufendeTarget!='_parent'||KAL_LaufendeTarget!='_top')){
  echo "\n".'<script>function LfdWin(sURL){kalWin=window.open(sURL,"'.KAL_LaufendeTarget.'","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>'."\n";
  define('KAL_LfndOnClk','" onclick="LfdWin(this.href);return false;');
 }else define('KAL_LfndOnClk','');

 echo "\n".'<div class="kalBox">'."\n";
 echo fKalLaufende();
 echo "\n".'</div>'."\n".$sKalHtmlNach;
}
echo "\n";

function fKalLaufende(){
 global $kal_FeldName, $kal_FeldType, $kal_LaufendeFeld, $kal_LaufendeLink, $kal_LaufendeStil, $kal_WochenTag, $kal_Symbole;

 $aA=array(); $aIdx=array(); $aIdN=array(); $aIdA=array(); $aKalSpalten=array(); $nFelder=count($kal_FeldName); $sQ='';
 $nDatFeld2=0; $nZeitFeld=0; $nZeitFeld2=0; $nVerstecktFeld=0; $a1Filt=NULL; $a2Filt=NULL; $a3Filt=NULL;
 for($i=1;$i<$nFelder;$i++){ //�ber alle Felder
  $t=$kal_FeldType[$i]; $aKalSpalten[$kal_LaufendeFeld[$i]]=$i;
  if(isset($_GET['kal_'.$i.'F1'])) $s=fKalRqL($_GET['kal_'.$i.'F1']); elseif(isset($_POST['kal_'.$i.'F1'])) $s=fKalRqL($_POST['kal_'.$i.'F1']); else $s='';
  if(strlen($s)){ //erstes Suchfeld ausgef�llt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t!='d'&&$t!='@') $a1Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else{$a1Filt[$i]=fKalNormDatL($s); $a2Filt[$i]='';} $sQ.='&amp;kal_'.$i.'F1='.rawurlencode($s);
  }elseif($t=='v'){$a1Filt[$i]=(KAL_NVerstecktSehen&&(KAL_Session!='')?'':'N'); $a2Filt[$i]='';} //versteckt
  if(isset($_GET['kal_'.$i.'F2'])) $s=fKalRqL($_GET['kal_'.$i.'F2']); elseif(isset($_POST['kal_'.$i.'F2'])) $s=fKalRqL($_POST['kal_'.$i.'F2']); else $s='';
  if(strlen($s)){ //zweites Suchfeld ausgef�llt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='i'||$t=='r'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
   elseif($t=='j'){if(empty($a1Filt[$i])) $a1Filt[$i]='';}
   if($t!='d'&&$t!='@') $a2Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); else $a2Filt[$i]=fKalNormDatL($s); $sQ.='&amp;kal_'.$i.'F2='.rawurlencode($s);
  }
  if(isset($_GET['kal_'.$i.'F3'])) $s=fKalRqL($_GET['kal_'.$i.'F3']); elseif(isset($_POST['kal_'.$i.'F3'])) $s=fKalRqL($_POST['kal_'.$i.'F3']); else $s='';
  if(strlen($s)){ //drittes Suchfeld ausgef�llt
   if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
   $a3Filt[$i]=($t!='u'?$s:sprintf('%0d',$s)); $sQ.='&amp;kal_'.$i.'F3='.rawurlencode($s);
  }
  if($t=='v') $nVerstecktFeld=$i;
  if($t=='d'&&$i>1&&$nDatFeld2==0) $nDatFeld2=$i; //2. Datum suchen
  if($t=='z'){if($nZeitFeld==0) $nZeitFeld=$i; elseif($nZeitFeld2==0) $nZeitFeld2=$i;} //1. und 2. Zeitfeld suchen
 }
 $bAnzeigen=$nVerstecktFeld==0||(KAL_NVerstecktSehen&&KAL_Session!=''); //alle anzeigen oder einzeln pr�fen
 $aKalSpalten[0]=0; ksort($aKalSpalten); $nSpalten=count($aKalSpalten); $sJetztDat=date('Y-m-d'); $sJetztUhr=date('H:i'); $sGestern=date('Y-m-d',time()-86400);
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_LaufendeIndex);
 //Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //�ber alle Datens�tze
   $a=explode(';',rtrim($aD[$i])); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1);
   if($bOK&&($bAnzeigen||$a[$nVerstecktFeld]!='J')){ //kein versteckter Termin
    $bOK=false;
    $sADt=substr($a[1],0,10); if(KAL_LaufendeZeit&&$nZeitFeld>0) $sAZt=$a[$nZeitFeld]; else $sAZt='';
    if(KAL_LaufendeEnde){
     if($nDatFeld2>0) $sEDt=substr($a[$nDatFeld2],0,10); else $sEDt='';
     if(KAL_LaufendeZeit&&$nZeitFeld2>0) $sEZt=$a[$nZeitFeld2]; else $sEZt='';
    }else{$sEDt=''; $sEZt='';}
    if($sADt<$sJetztDat){ //Anfangsdatum fr�her
     if(KAL_LaufendeEnde) if($sEDt==$sJetztDat&&(!KAL_LaufendeZeit||$sEZt>=$sJetztUhr||$sEZt=='')||$sEDt==''&&$sADt==$sGestern&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr||$sEDt>$sJetztDat) $bOK=true;
    }elseif($sADt==$sJetztDat){ //Anfangdatum heute
     if(!KAL_LaufendeZeit||$sAZt==''||$sAZt<=$sJetztUhr&&KAL_LaufendeEnde&&($sEDt>=$sJetztDat||$sEZt>=$sJetztUhr||($sEZt>''&&$sEZt<$sAZt))) $bOK=true;
    }else break; //Anfangsdatum sp�ter
    if($bErsatz=KAL_LaufendeNotErsatz&&!$bOK) $bOK=true; //Ersatztermin
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
     }
    }
    if($bOK&&is_array($a3Filt)){ //Suchfiltern 3
     reset($a3Filt); foreach($a3Filt as $j=>$v) if($kal_FeldType[$j]!='u'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$bOK=false; break;}}elseif(sprintf('%0d',$a[$j])==$v){$bOK=false; break;}
    }
    if($bOK){//g�ltiger Termin
     $nId=(int)$a[0]; $aA[$nId]=array($nId); $v='';
     for($j=1;$j<$nSpalten;$j++){
      $w=str_replace('\n ',"\n",str_replace('`,',';',$a[$aKalSpalten[$j]]));
      $aA[$nId][]=$w; $v.=substr($w,0,10).chr(255);
     }
     if($nIndex==1) $aIdN[$nId]=$v.sprintf('%0'.KAL_NummerStellen.'d',$nId);
     elseif($nIndex>1){ //andere Sortierung
      $s=strtoupper(strip_tags($a[$nIndex])); $t=$kal_FeldType[$nIndex];
      for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
       if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
      if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
      elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
      $aIdN[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
     }
     elseif($nIndex==0) $aIdN[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr
     if(!$bErsatz) $aIdx[$nId]=$aIdN[$nId]; elseif($sADt>=$sJetztDat) $aIdA[$nId]=$aIdN[$nId];
    }
   }//versteckt
  }
 }else{ //SQL
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){
   if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);
   $f=''; $s=''; $nDatPos=0; $nDatPos2=0; $nZeitPos=0; $nZeitPos2=0; $i=$nSpalten;
   if(is_array($a1Filt)) foreach($a1Filt as $j=>$v) if(!($j==1||($j==$nDatFeld2&&KAL_EndeDatum))){ //Suchfiltern 1-2
    $s.=' AND(kal_'.$j; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; $t=$kal_FeldType[$j]; //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='x'){
     $s.=' LIKE "%'.fKalLDtCode($v).'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.fKalLDtCode($w).'%"';
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
     $s.=' AND NOT(kal_'.$j.' LIKE "%'.fKalLDtCode($v).'%")';
    elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
    elseif($t=='u') $s.=' AND NOT(CONVERT(kal_'.$j.',INTEGER)="'.$v.'")';
   }
   for($j=1;$j<$nSpalten;$j++){
    $k=$aKalSpalten[$j]; $f.=',kal_'.$k;
    if($k==1) $nDatPos=$j; elseif($k==$nDatFeld2) $nDatPos2=$j; elseif($k==$nZeitFeld) $nZeitPos=$j; elseif($k==$nZeitFeld2) $nZeitPos2=$j;
   }
   if($nDatPos==0){$f.=',kal_1'; $nDatPos=$i++;}
   if($nDatFeld2>0&&$nDatPos2==0){$f.=',kal_'.$nDatFeld2; $nDatPos2=$i++;}
   if($nZeitFeld>0&&$nZeitPos==0){$f.=',kal_'.$nZeitFeld; $nZeitPos=$i++;}
   if($nZeitFeld2>0&&$nZeitPos2==0){$f.=',kal_'.$nZeitFeld2; $nZeitPos2=$i++;}
   if($rR=$DbO->query('SELECT id'.$f.' FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').')'.(!KAL_LaufendeNotErsatz?' AND(kal_1>"'.$sGestern.(KAL_LaufendeEnde&&$nDatFeld2>0?'" OR kal_'.$nDatFeld2.'>"'.$sJetztDat:'').'")':'').($bAnzeigen?'':' AND kal_'.$nVerstecktFeld.'<>"J"').$s.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
    while($a=$rR->fetch_row()){$bOK=false; $bErsatz=false;
     $sADt=substr($a[$nDatPos],0,10); if(KAL_LaufendeZeit&&$nZeitPos>0) $sAZt=$a[$nZeitPos]; else $sAZt='';
     if(KAL_LaufendeEnde){
      if($nDatPos2>0) $sEDt=substr($a[$nDatPos2],0,10); else $sEDt='';
      if(KAL_LaufendeZeit&&$nZeitPos2>0) $sEZt=$a[$nZeitPos2]; else $sEZt='';
     }else{$sEDt=''; $sEZt='';}
     if($sADt<$sJetztDat){ //Anfangsdatum fr�her
      if(KAL_LaufendeEnde) if($sEDt==$sJetztDat&&(!KAL_LaufendeZeit||$sEZt>=$sJetztUhr||$sEZt=='')||$sEDt==''&&$sADt==$sGestern&&$sEZt>''&&$sEZt<$sAZt&&$sEZt>=$sJetztUhr||$sEDt>$sJetztDat) $bOK=true;
     }elseif($sADt==$sJetztDat){ //Anfangdatum heute
      if(!KAL_LaufendeZeit||$sAZt==''||$sAZt<=$sJetztUhr&&KAL_LaufendeEnde&&($sEDt>=$sJetztDat||$sEZt>=$sJetztUhr||($sEZt>''&&$sEZt<$sAZt))) $bOK=true;
     }elseif(!KAL_LaufendeNotErsatz) break; //Anfangsdatum sp�ter
     if($bErsatz=KAL_LaufendeNotErsatz&&!$bOK) $bOK=true; //Ersatztermin
     if($bOK){//g�ltiger Termin
      $nId=(int)$a[0]; $aA[$nId]=array($nId); $v='';
      for($j=1;$j<$nSpalten;$j++){
       $w=str_replace('\n ',"\n",str_replace('`,',';',$a[$j]));
       $aA[$nId][]=$w; $v.=substr($w,0,10).chr(255);
      }
      if($nIndex==1) $aIdN[$nId]=$v.sprintf('%0'.KAL_NummerStellen.'d',$nId);
      elseif($nIndex>1){ //andere Sortierung
       $s=strtoupper(strip_tags($a[$kal_LaufendeFeld[$nIndex]])); $t=$kal_FeldType[$nIndex];
       for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
        if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
       if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
       elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
       $aIdN[$nId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',++$i);
      }
      elseif($nIndex==0) $aIdN[$nId]=sprintf('%0'.KAL_NummerStellen.'d',$nId); //nach Nr.
      if(!$bErsatz) $aIdx[$nId]=$aIdN[$nId]; elseif($sADt>=$sJetztDat) $aIdA[$nId]=$aIdN[$nId];
     }
    }$rR->close();
 }}}//SQL

 //eigene Layoutzeile pruefen
 if($bEigeneZeilen=KAL_LaufendeEigeneZeilen&&file_exists(KAL_Pfad.'laufendeZeile.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'laufendeZeile.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }

 //Beginn Ausgabe
 if($nKapPos=(int)array_search('KAPAZITAET',$kal_FeldName)) $nKapPos=(int)array_search($nKapPos,$aKalSpalten);
 $X="\n".'<div class="kalTabL">'; $bNaechste=false; $aSpTitle=array(); $sKopf='';
 if(KAL_LaufendeKopf){
  if(!$bEigeneZeilen){ //Standardlayout
   $sKopf.="\n".' <div class="kalTbLZl0">';
   for($j=1;$j<$nSpalten;$j++){
    $s=''; $k=$aKalSpalten[$j];
    if($j==$kal_LaufendeFeld[$nIndex]) $s='&nbsp;<img class="kalSorti" src="'.KAL_Url.'grafik/sortiere.gif" alt="'.fKalTxL(KAL_TxSortieren).'">';
    $sFN=$kal_FeldName[$k]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
    $sFS=$kal_FeldType[$k]; if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M'; if($j==$nKapPos) $sFS='M';
    $sKopf.="\n".'  <div class="kalTbLLst kalTbLLs'.$sFS.'">'.fKalTxL($sFN).$s.'</div>'; $aSpTitle[$k]=fKalTxL($sFN).$s;
   }
   $sKopf.="\n".' </div>';
  }elseif(file_exists(KAL_Pfad.'laufendeKopf.htm')){ //eigene Kopfzeile
   $r=@implode('',@file(KAL_Pfad.'laufendeKopf.htm')); $s=strtolower($t);
   if(!strpos($s,'<body')&&!strpos($s,'<head')) $sKopf="\n".' <div class="kalTbAZl0">'."\n".$r."\n </div>";
  }
  $X.=$sKopf;
 }

 $sQzS='?'; $sQzZ='?'; if(defined('KAL_Self')&&strpos(KAL_Self,'?')) $sQzS='&amp;'; if(strpos(KAL_LaufendeZiel,'?')) $sQzZ='&amp;';

 $aIdx=array_flip($aIdx);
 if(count($aIdx)==0&&KAL_LaufendeNotErsatz){ //keine Laufenden
  $aIdA=array_flip($aIdA); $i=0; //Aktuelle/Kommende
  foreach($aIdA as $k=>$j){$aIdx[$k]=$j; if(++$i>4) break;}
  if(count($aIdx)==0){ //keine Aktuellen
   $aIdN=array_flip($aIdN); //Nottermine
   foreach($aIdN as $k=>$j) $aIdx[$k]=$j;
   $aIdx=array_slice($aIdx,-5);
  }
 }
 if(!KAL_LaufendeRueckw) {if($nIndex!=1) ksort($aIdx);} else krsort($aIdx); $nFarb=1;

 foreach($aIdx as $i){
  $a=$aA[$i]; $sId=$i; $sZl=''; if($bEigeneZeilen) $sZl=$sEigeneZeile; //eigenes Zeilenlayout
  for($j=1;$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'm': case 'g': if($j!=$nKapPos) $s=fKalBL(fKalTxL($s)); else{$s=(int)$s; $sFS=' kalTbLLsM';} break; //Text/Memo/Gastkommentar
     case 'a': case 'k': case 'o': case 'u': $s=fKalTxL($s); break; //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_LaufendeJahrhundert?substr($s,0,4):substr($s,2,2));
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; if($t=='@') $sFS=' kalTbLLsM';
      if($t=='d'){if(KAL_LaufendeMitWochentag) if(KAL_LaufendeMitWochentag<2) $s=fKalTxL($kal_WochenTag[$w]).'&nbsp;'.$s; else $s.='&nbsp;'.fKalTxL($kal_WochenTag[$w]);}
      elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sFS=' kalTbLLsM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sFS=' kalTbLLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTxL(KAL_TxJa); elseif($s=='N') $s=fKalTxL(KAL_TxNein); $sFS=' kalTbLLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sFS=' kalTbLLsR';
      break;
     case 'l': //Link
      $aL=explode('||',$s); $s=''; $sFS=' kalTbLLsM';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalTxL(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(KAL_LinkSymbol){$v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">'; $sFS=' kalTbLLsM';}
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLinkL($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //eMail
      $s='<a href="'.(KAL_MailPopup?KAL_Url.'kalender.php?':KAL_Self.$sQzS.substr(KAL_Query.'&amp;',5)).'kal_Aktion=kontakt'.KAL_Session.(KAL_MailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$sId.'"'.(KAL_MailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTxL(KAL_TxKontakt).'" alt="'.fKalTxL(KAL_TxKontakt).'"></a>';
      $sFS=' kalTbLLsM';
      break;
     case 's': $w=fKalTxL($s); //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">'; $sFS=' kalTbLLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
      if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $v=fKalTxL(substr($s,strpos($s,'-')+1,-4));
      $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$v.'" alt="'.$v.'"></div>'; $sFS=' kalTbLLsM';
      break;
     case 'f': // Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalTxL(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS=' kalTbLLsM';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
      $s='<a href="'.KAL_Url.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'x': $s=fKalTxL(KAL_TxJa); $sFS=' kalTbLLsM'; break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); $sFS=' kalTbLLsM'; break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sFS=' kalTbLLsM';
   }else $s='&nbsp;';
   if($sStil=$kal_LaufendeStil[$k]) $sStil=' style="'.$sStil.'"';
   if($kal_LaufendeLink[$k]>0) $s='<a class="kalLfnd" href="'.KAL_LaufendeZiel.$sQzZ.substr(KAL_Query.'&amp;',5).'kal_Aktion=detail'.$sQ.KAL_Session.(KAL_LfndOnClk?'&amp;kal_Popup=1':'').'&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sId.'" target="'.KAL_LaufendeTarget.KAL_LfndOnClk.'" title="'.fKalTxL(KAL_TxDetail).'">'.$s.'</a>';
   if(!$bEigeneZeilen) $sZl.="\n".'  <div class="kalTbLLst'.$sFS.'"'.$sStil.'><span class="kalTbLLst">'.$aSpTitle[$k].'</span>'.$s.'</div>'; //Standardlayout
   else $sZl=str_replace('{'.$kal_FeldName[$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if(KAL_LaufendeAbstand>0&&$bNaechste) $X.="\n".' <div class="kalTbLZlX" style="height:'.KAL_LaufendeAbstand.'px">&nbsp;</div>';
  if(KAL_LaufendeKpfWdh&&$bNaechste&&KAL_LaufendeKopf) $X.=$sKopf; $bNaechste=true;
  if(!$bEigeneZeilen){ //Standardlayout
   $X.="\n".' <div class="kalTbLZl'.$nFarb.'">'.$sZl."\n".' </div>'; if(--$nFarb<=0) $nFarb=2;
  }else{ //eigenes Layout
   $X.="\n".' <div class="kalTbLZl'.$nFarb.'">'.$sZl."\n".' </div>'; if(--$nFarb<=0) $nFarb=2;
  }
 }
 $X.="\n</div>\n";
 return $X;
}

function fKalNormDatL($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}
function fKalExtLinkL($s){
 if(!defined('KAL_ZSatzExtLink')||KAL_ZSatzExtLink==0) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode($s))));
 elseif(KAL_ZSatzExtLink==1) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s)))));
 elseif(KAL_ZSatzExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}
//BB-Code zu HTML wandeln
function fKalBL($s){
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
function fKalLDtCode($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
}
function fKalTxL($sTx){ //TextKodierung
 if(KAL_Zeichensatz==0) return str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$sTx)))); elseif(KAL_Zeichensatz==2) return iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$sTx))))); else return htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
}
function fKalRqL($sTx){
 return stripslashes(str_replace('"',"'",trim($sTx)));
}
?>