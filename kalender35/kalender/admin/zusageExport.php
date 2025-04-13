<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusageliste','','ZZl');

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $nAnzahlPos=0; $sQ='';
$kal_ExptFelder=explode(';',KAL_ZusageExptAdm); $bMitNr=($kal_ExptFelder[0]>0); $kal_ExptFelder[0]=0; $aExptFelder=array_flip($kal_ExptFelder);

if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $bZeigeAkt=(isset($_GET['kal_Akt'])?(bool)$_GET['kal_Akt']:KAL_ZusageAdmLstKommend);
 $bZeigeAlt=(isset($_GET['kal_Alt'])?(bool)$_GET['kal_Alt']:KAL_ZusageAdmLstVorbei);
}
if(!$bZeigeAlt) $bZeigeAkt=true;
if($bZeigeAkt!=KAL_ZusageAdmLstKommend) $sQ.='&amp;kal_Akt='.($bZeigeAkt?'1':'0');
if($bZeigeAlt!=KAL_ZusageAdmLstVorbei) $sQ.='&amp;kal_Alt='.($bZeigeAlt?'1':'0');

for($i=0;$i<=$nZusageFelder;$i++){ //Abfrageparameter aufbereiten
 $s=(isset($_POST['kal_'.$i.'F1'])?$_POST['kal_'.$i.'F1']:(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s;
  if($i!=5&&$i!=2) $a1Filt[$i]=$s; else $a1Filt[$i]=fKalNormDatum($s);
 }
 $s=(isset($_POST['kal_'.$i.'F2'])?$_POST['kal_'.$i.'F2']:(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($i!=5&&$i!=2) $a2Filt[$i]=$s; else $a2Filt[$i]=fKalNormDatum($s);
  if($i<=2||$i==5||$i==6){if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='0';}
 }
 $s=(isset($_POST['kal_'.$i.'F3'])?$_POST['kal_'.$i.'F3']:(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:''));
 if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;kal_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
}
$bZeigeOnl=true; $bZeigeOfl=true; $bZeigeBst=true;
$s=(isset($_POST['kal_Onl'])?$_POST['kal_Onl']:(isset($_GET['kal_Onl'])?$_GET['kal_Onl']:''));
$t=(isset($_POST['kal_Ofl'])?$_POST['kal_Ofl']:(isset($_GET['kal_Ofl'])?$_GET['kal_Ofl']:''));
$u=(isset($_POST['kal_Bst'])?$_POST['kal_Bst']:(isset($_GET['kal_Bst'])?$_GET['kal_Bst']:''));
if($s&&!($t&&$u)){$sQ.='&amp;kal_Onl=1'; $aQ['Onl']=1;} elseif(!$s&&($t||$u)) $bZeigeOnl=false;
if($t&&!($s&&$u)){$sQ.='&amp;kal_Ofl=1'; $aQ['Ofl']=1;} elseif(!$t&&($s||$u)) $bZeigeOfl=false;
if($u&&!($s&&$t)){$sQ.='&amp;kal_Bst=1'; $aQ['Bst']=1;} elseif(!$u&&($s||$t)) $bZeigeBst=false;

$nIndex=(int)(isset($_GET['kal_Index'])?$_GET['kal_Index']:(isset($_POST['kal_Index'])?$_POST['kal_Index']:0));
$sRueck=(isset($_GET['kal_Rueck'])?$_GET['kal_Rueck']:(isset($_POST['kal_Rueck'])?$_POST['kal_Rueck']:''));
if(!KAL_Zusagen){$nIndex=0; $sRueck='';} if($nIndex>0) $aQ['Index']=$nIndex; if($sRueck!='') $aQ['Rueck']=$sRueck;

//Daten bereitstellen
$aT=array(); $aIdx=array(); $sRefDat=date('Y-m-d');
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sZId=(int)$a[0]; $sSta=$a[6];
  $b=($sSta=='1'&&$bZeigeOnl||($sSta=='0'||$sSta=='-')&&$bZeigeOfl||($sSta=='2'||$sSta=='*')&&$bZeigeBst);
  if($bZeigeAkt&&!$bZeigeAlt) $b=$b&&($a[2]>=$sRefDat);//kommende
  elseif($bZeigeAlt&&!$bZeigeAkt) $b=$b&&($a[2]<$sRefDat);//abgelaufene
  if($b&&isset($a1Filt)&&is_array($a1Filt)){
   reset($a1Filt);
   foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
    $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
    if($j==5||$j==2){ //Datum
     $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
    }elseif($j<2){ //Nr
     $v=(int)$v; $w=(int)$w; $s=(int)$a[$j];
     if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
    }elseif($j==8){ //EMail
     if(strlen($w)){if(stristr(fKalDeCode($a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr(fKalDeCode($a[$j]),$v)||$b2)) $b=false;
    }else{//Text
     if(strlen($w)){if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)||$b2)) $b=false;
  }}}
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v) if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)){$b=false; break;}
  }
  if($b){ //Datensatz gueltig
   $aT[$sZId]=array($sZId); $s=$a[$nIndex];
   if($nIndex==0) $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',$sZId); //Nr
   if($nIndex==1||$nIndex==7){ //Termin oder User
    $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',$s).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==2||$nIndex==5){ //Datum
    $aIdx[$sZId]=$s.sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==8){ //EMail
    $aIdx[$sZId]=strtolower(fKalDeCode($s)).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==4){ //Veranstaltung
    $s=strtoupper(strip_tags($s));
    for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
    $aIdx[$sZId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==$nAnzahlPos){ //Anzahl
    $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',abs($s)).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex>8){
    $aIdx[$sZId]=(strlen($s)>0?strtoupper(strip_tags($s)):' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }
   for($j=1;$j<=$nZusageFelder;$j++) $aT[$sZId][]=(isset($a[$j])?str_replace('`,',',',$a[$j]):'');
  }
 }
}elseif($DbO){ //SQL
 $aFN=array('nr','termin','datum','zeit','veranstaltung','buchung','aktiv','email'); $s='';
 if($bZeigeAkt&&!$bZeigeAlt) $s.=' AND(datum>="'.$sRefDat.'")';//kommende
 elseif($bZeigeAlt&&!$bZeigeAkt) $s.=' AND(datum<"'.$sRefDat.'")';//abgelaufene
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  $s.=' AND('.($j<9?$aFN[$j]:'dat_'.$j); $w=(isset($a2Filt[$j])?$a2Filt[$j]:'');//$v Suchwort1, $w Suchwort2
  if($j==5||$j==2){
   if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
  }elseif($j<2){ //Nr
   $v=(int)$v;
   if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.((int)$w).'"'; else $s.='="'.$v.'"';
  }else{
   $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR '.($j<9?$aFN[$j]:'dat_'.$j).' LIKE "%'.$w.'%"';
  }
  $s.=')';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j<9?$aFN[$j]:'dat_'.$j).' LIKE "%'.$v.'%")';
 }
 $o=''; if($bZeigeOnl) $o.=' OR aktiv="1"'; if($bZeigeOfl) $o.=' OR aktiv="0" OR aktiv="-"'; if($bZeigeBst) $o.=' OR aktiv="2" OR aktiv="*"';
 if($o=substr($o,4)){$i=substr_count($o,'OR'); if($i>0) $o='('.$o.')'; if($i==4) $o='nr>0';} else $o='nr>0';
 $t=''; for($j=9;$j<=$nZusageFelder;$j++) $t.=',dat_'.$j; $i=0;
 if($rR=$DbO->query('SELECT nr,termin,datum,zeit,veranstaltung,buchung,aktiv,benutzer,email'.$t.' FROM '.KAL_SqlTabZ.' WHERE '.$o.$s.' ORDER BY nr')){
  while($a=$rR->fetch_row()){
   $sZId=(int)$a[0]; $aT[$sZId]=array($sZId); $s=$a[$nIndex];
   if($nIndex==0) $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',$sZId); //Nr
   if($nIndex==1||$nIndex==7){ //Termin oder User
    $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',$s).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==2||$nIndex==5){ //Datum
    $aIdx[$sZId]=$s.sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==8){ //EMail
    $aIdx[$sZId]=strtolower($s).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==4){ //Veranstaltung
    $s=strtoupper(strip_tags($s));
    for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
    $aIdx[$sZId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex==$nAnzahlPos){ //Anzahl
    $aIdx[$sZId]=sprintf('%0'.KAL_NummerStellen.'d',abs($s)).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }elseif($nIndex>8){ //Anzahl
    $aIdx[$sZId]=(strlen($s)>0?strtoupper(strip_tags($s)):' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$sZId);
   }
   for($j=1;$j<=$nZusageFelder;$j++) $aT[$sZId][]=str_replace("\r",'',str_replace(';',',',$a[$j]));
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';//SQL

if($sRueck==''&&!KAL_ZusageAdmRueckw||$sRueck=='0'){if($nIndex!=0) asort($aIdx);} else arsort($aIdx);
$aD=array(); reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aT[$i];

for($j=0;$j<=$nZusageFelder;$j++){
 if($kal_ZusageFelder[$j]=='ANZAHL'){$nAnzahlPos=$j; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$j]=KAL_ZusageNameAnzahl;}
}

$X=''; if($bMitNr) $X=$kal_ZusageFelder[0].';'; //Kopfzeile
for($j=1;$j<=$nZusageFelder;$j++) if(isset($aExptFelder[$j])&&($i=$aExptFelder[$j])) $X.=str_replace('`,',',',$kal_ZusageFelder[$i]).';';
$X=substr($X,0,-1).NL;

foreach($aD as $aZ){ //Datenzeilen ausgeben
 $L=''; if($bMitNr) $L=sprintf('%04d',$aZ[0]).';';
 for($j=1;$j<=$nZusageFelder;$j++) if(isset($aExptFelder[$j])&&$i=$aExptFelder[$j]){$s=str_replace('`,',',',$aZ[$i]);
  if(strlen($s)>0) switch($i){
   case 1: case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break;
   case 2: case 5: $s=fKalAnzeigeDatum($s); break;
   case 4: $s=fKalKurzMemo($s,KAL_ZusageAdmLstVstBreit); break;
   case 6: if($s=='1') $s='gültig'; elseif($s=='0') $s='vorgemerkt'; elseif($s=='2') $s='bestätigt'; elseif($s=='-') $s='Widerruf vorgemerkt'; elseif($s=='*') $s='Widerruf bestätigt'; elseif($s=='7') $s='auf der Warteliste'; break;
   case 8: if(!KAL_SQL) $s=fKalDeCode($s); break;
   case $nAnzahlPos: $s=abs($s); break;
  }
  $L.=$s.';';
 }
 $X.=substr($L,0,-1).NL;
}

//Scriptausgabe
if(!$Msg){
 if(!$sQ) $Msg='<p class="admMeld">Terminzusagen/Reservierungen/Bestellungen exportieren</p>';
 else $Msg='<p class="admMeld">Abfrageergebnis - Terminzusagen/Reservierungen/Bestellungen exportieren</p>';
}
echo $Msg;

for($i=59;$i>=0;$i--) if(file_exists(KAL_Pfad.'temp/zusagen_'.sprintf('%02d',$i).'.csv')) unlink(KAL_Pfad.'temp/zusagen_'.sprintf('%02d',$i).'.csv');
$sExNa='zusagen_'.date('s').'.csv';
if($f=fopen(KAL_Pfad.'temp/'.$sExNa,'w')){
 fwrite($f,$X."\r\n"); fclose($f);
 $Msg='<p class="admErfo">Die Exportdatei liegt unter <a href="'.$sHttp.'temp/'.$sExNa.'" target="hilfe" onclick="hlpWin(this.href);return false;" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.</p>';
}else $Msg='<p class="admFehl">'.str_replace('#','<i>temp/'.$sExNa.'</i>',KAL_TxDateiRechte).'</p>';

echo '<div style="margin-top:50px;margin-bottom:90px;">'.$Msg.'</div>'."\n";
echo '<p style="text-align:center">[ <a href="zusageListe.php'.($sQ?'?'.substr($sQ,5):'').'">Zusagenliste</a> ] &nbsp; [ <a href="zusageSuche.php'.($sQ?'?'.substr($sQ,5):'').'">Zusagensuche</a> ]</p>'."\n";

echo fSeitenFuss();

function fKalNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

//Text mit BB-Code einkuerzen
function fKalKurzMemo($s,$nL=80){
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

//BB-Code zu HTML wandeln
function fKalBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
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
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="kalText"><li class="kalText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="kalText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}
?>