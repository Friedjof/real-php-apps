<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisliste','','EEl');

$DDl=''; $bCh=false; $nAntwAnzahl=max(20,ADU_AntwortZahl); $s0=''; for($i=1;$i<=$nAntwAnzahl;$i++) $s0.=';0';

if($_SERVER['REQUEST_METHOD']=='POST'){
 foreach($_POST as $k=>$xx) if(substr($k,0,3)=='del'&&strpos($k,'x')>0) $nDel=(int)substr($k,3); reset($_POST);
 if($nDel>0){ //loeschen
  if($nDel==(isset($_POST['ddl'])?$_POST['ddl']:'')){
   if(!UMF_SQL){ //Textdaten
    $aE=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $i=-1; $bOk=false;
    foreach($aE as $s){
     $a=explode(';',$s,2); $i++;
     if($a[0]==$nDel){$aE[$i]=$nDel.$s0."\n"; $bOk=true; break;}
    }
    if($bOk){
     if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Ergebnis,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aE))).NL); fclose($f);
      $sMeld=fMErfo('Das Ergebnis zur Frage '.$nDel.' wurde auf Null gesetzt!');
     }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Ergebnis,UMF_TxDateiRechte));
    }else $sMeld=fMFehl('Dieses Ergebnis Nummer '.$nDel.' existiert nicht.');
   }elseif($DbO){ //beiSQL
    if($DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="'.substr($s0,1).'" WHERE Nummer="'.$nDel.'"')){
     $sMeld=fMErfo('Das Ergebnis zur Frage '.$nDel.' wurde auf Null gesetzt!');
    }else $sMeld=fMFehl(UMF_TxSqlAendr);
   }
  }else{$DDl=$nDel; $sMeld=fMFehl('Das Ergebnis zur Frage Nummer '.$nDel.' wirklich auf Null setzen?');}
 }elseif($nDel<0){ // leeren
  if($nDel==(isset($_POST['ddl'])?$_POST['ddl']:'')){
   if(!UMF_SQL){ //Textdaten
    $aE=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $i=-1; $bOk=false;
    foreach($aE as $s){$a=explode(';',$s,2); $i++; $aE[$i]=$a[0].$s0."\n";}
    $aE[0]="IP;\n";
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Ergebnis,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aE))).NL); fclose($f);
     $sMeld=fMErfo('Die Ergebnisliste wurde geleert!');
    }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Ergebnis,UMF_TxDateiRechte));
   }elseif($DbO){ //beiSQL
    if($DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="'.substr($s0,1).'" WHERE Nummer>"0"')){
     $DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="IP;" WHERE Nummer="0"');
     $sMeld=fMErfo('Die Ergebnisliste wurde geleert!');
    }else $sMeld=fMFehl(UMF_TxSqlAendr);
   }
  }else{$DDl=-1; $sMeld=fMFehl('Die gesamte Ergebnisliste wirklich komplett leeren?');}
 }
}

$aQ=array(); $sQ=''; $a1Filt=array(); $a2Filt=array(); $a3Filt=array(); //Suchparameter
if($FNr1=(isset($_POST['fnr1'])?$_POST['fnr1']:'').(isset($_GET['fnr1'])?$_GET['fnr1']:'')){$a1Filt[0]=$FNr1; $sQ.='&amp;fnr1='.$FNr1; $aQ['fnr1']=$FNr1;}
if($FNr2=(isset($_POST['fnr2'])?$_POST['fnr2']:'').(isset($_GET['fnr2'])?$_GET['fnr2']:'')){$a2Filt[0]=$FNr2; $sQ.='&amp;fnr2='.$FNr2; $aQ['fnr2']=$FNr2;}
$Onl=(isset($_POST['onl'])?$_POST['onl']:'').(isset($_GET['onl'])?$_GET['onl']:'').(isset($_POST['onl1'])?$_POST['onl1']:'').(isset($_GET['onl1'])?$_GET['onl1']:'').(isset($_POST['onl2'])?$_POST['onl2']:'').(isset($_GET['onl2'])?$_GET['onl2']:'');
if(strlen($Onl)!=1) $Onl=''; else {$a1Filt[1]=$Onl; $sQ.='&amp;onl='.$Onl; $aQ['onl1']=$Onl;}
$s=(isset($_POST['ufr1'])?$_POST['ufr1']:'').(isset($_GET['ufr1'])?$_GET['ufr1']:''); if(strlen($s)){$a1Filt[2]=$s; $sQ.='&amp;ufr1='.rawurlencode($s); $aQ['ufr1']=$s;}
$s=(isset($_POST['ufr2'])?$_POST['ufr2']:'').(isset($_GET['ufr2'])?$_GET['ufr2']:''); if(strlen($s)){$a2Filt[2]=$s; $sQ.='&amp;ufr2='.rawurlencode($s); $aQ['ufr2']=$s;}
$s=(isset($_POST['ufr3'])?$_POST['ufr3']:'').(isset($_GET['ufr3'])?$_GET['ufr3']:''); if(strlen($s)){$a3Filt[2]=$s; $sQ.='&amp;ufr3='.rawurlencode($s); $aQ['ufr3']=$s;}
$s=(isset($_POST['frg1'])?$_POST['frg1']:'').(isset($_GET['frg1'])?$_GET['frg1']:''); if(strlen($s)){$a1Filt[3]=$s; $sQ.='&amp;frg1='.rawurlencode($s); $aQ['frg1']=$s;}
$s=(isset($_POST['frg2'])?$_POST['frg2']:'').(isset($_GET['frg2'])?$_GET['frg2']:''); if(strlen($s)){$a2Filt[3]=$s; $sQ.='&amp;frg2='.rawurlencode($s); $aQ['frg2']=$s;}
$s=(isset($_POST['frg3'])?$_POST['frg3']:'').(isset($_GET['frg3'])?$_GET['frg3']:''); if(strlen($s)){$a3Filt[3]=$s; $sQ.='&amp;frg3='.rawurlencode($s); $aQ['frg3']=$s;}
$s=(isset($_POST['bem1'])?$_POST['bem1']:'').(isset($_GET['bem1'])?$_GET['bem1']:''); if(strlen($s)){$a1Filt[5]=$s; $sQ.='&amp;bem1='.rawurlencode($s); $aQ['bem1']=$s;}
$s=(isset($_POST['bem2'])?$_POST['bem2']:'').(isset($_GET['bem2'])?$_GET['bem2']:''); if(strlen($s)){$a2Filt[5]=$s; $sQ.='&amp;bem2='.rawurlencode($s); $aQ['bem2']=$s;}
$s=(isset($_POST['bem3'])?$_POST['bem3']:'').(isset($_GET['bem3'])?$_GET['bem3']:''); if(strlen($s)){$a3Filt[5]=$s; $sQ.='&amp;bem3='.rawurlencode($s); $aQ['bem3']=$s;}
$s=(isset($_POST['b2m1'])?$_POST['b2m1']:'').(isset($_GET['b2m1'])?$_GET['b2m1']:''); if(strlen($s)){$a1Filt[6]=$s; $sQ.='&amp;b2m1='.rawurlencode($s); $aQ['b2m1']=$s;}
$s=(isset($_POST['b2m2'])?$_POST['b2m2']:'').(isset($_GET['b2m2'])?$_GET['b2m2']:''); if(strlen($s)){$a2Filt[6]=$s; $sQ.='&amp;b2m2='.rawurlencode($s); $aQ['b2m2']=$s;}
$s=(isset($_POST['b2m3'])?$_POST['b2m3']:'').(isset($_GET['b2m3'])?$_GET['b2m3']:''); if(strlen($s)){$a3Filt[6]=$s; $sQ.='&amp;b2m3='.rawurlencode($s); $aQ['b2m3']=$s;}

$aD=array(); $aE=array(); $aTmp=array(); //Daten holen
if(!UMF_SQL){ //Textdaten
 $aTmp=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); array_shift($aTmp);
 foreach($aTmp as $s){ //ueber alle Ergebnissaetze
  $p=strpos($s,';'); $aE[(int)substr($s,0,$p)]=rtrim(substr($s,++$p));
 }
 $aTmp=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); array_shift($aTmp);
 foreach($aTmp as $s){ //ueber alle Fragensaetze
  $a=explode(';',rtrim($s)); $nNr=(int)$a[0]; $b=true;
  if(count($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
   foreach($a1Filt as $j=>$v) if($b&&$j>1){
    if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')){if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)||$b2)) $b=false;
   }elseif($j==0){
    if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)){if($a[0]!=$v) $b=false;}else{if($a[0]<$v||$a[0]>$w) $b=false;}
   }elseif($a[$j]!=$v) $b=false;
  }
  if($b&&count($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v) if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)){$b=false; break;}
  }
  if($b){ //Datensatz gueltig
   $aD[$nNr]=array($nNr); //Nr
   $aD[$nNr][1]=str_replace('\n ',NL,str_replace('`,',',',$a[3])); //Fra
   $aD[$nNr][2]=(isset($aE[$nNr])?$aE[$nNr]:substr($s0,1)); //Erg
   $nA=6; while(isset($a[++$nA])&&strlen($a[$nA])>0); $aD[$nNr][3]=$nA-6;
  }
 }
}elseif($DbO){ //SQL-Daten
 $s='';
 if(count($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sF='f.'.($j==3?'Frage':($j==2?'Umfrage':($j!=6?'Anmerkung1':'Anmerkung2')));
   $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)) $s.=' AND f.Nummer='.(int)$v; else $s.=' AND f.Nummer BETWEEN '.(int)$v.' AND '.(int)$w;
  }else $s.=' AND f.aktiv="'.$v.'"';
 }
 if(count($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT(f.'.($j==3?'Frage':($j==2?'Umfrage':($j!=6?'Anmerkung1':'Anmerkung2'))).' LIKE "%'.$v.'%")';
 }
 if($rR=$DbO->query('SELECT f.*,'.UMF_SqlTabE.'.Inhalt FROM '.UMF_SqlTabF.' AS f LEFT JOIN '.UMF_SqlTabE.' ON f.Nummer='.UMF_SqlTabE.'.Nummer'.($s?' WHERE '.substr($s,4):'').' ORDER BY f.Nummer')){
  while($a=$rR->fetch_row()){
   $sNr=(int)$a[0]; $aD[$sNr]=array($sNr); //Nr
   $aD[$sNr][1]=str_replace("\r",'',str_replace(';',',',$a[3])); $aD[$sNr][2]=$a[count($a)-1]; //Fra,Erg
   $nA=6; while(isset($a[++$nA])&&strlen($a[$nA])>0); $aD[$sNr][3]=$nA-6;
  }$rR->close();
 }else $sMeld=fMFehl(UMF_TxSqlFrage);
}

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADU_ErgebnisLaenge;
if(ADU_ErgebnisRueckw) arsort($aD); reset($aD); $k=0; $aE=array();
foreach($aD as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aE[]=$aD[$i];
if(!$sMeld) $sMeld=fMMeld(!$sQ?'Gesamt-Ergebnisliste':'Abfrageergebnis');
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld?></td>
  <td align="right">[ <a href="ergebnisExport.php<?php if($sQ) echo '?'.$sQ?>">Export</a>] &nbsp; [ <a href="ergebnisSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ]</td>
 </tr>
</table>
<?php
 $sNavigator=fUmfNavigator($nStart,count($aD),ADU_ErgebnisLaenge,$sQ); echo $sNavigator;
 if($nStart>1) $sQ.=($sQ?'&amp;':'').'start='.$nStart; $aQ['start']=$nStart; $sAmp=($sQ?'&amp;':'');
 $bAendern=file_exists('aendern.php'); $bLoeschen=file_exists('ergebnisLoeschen.php');
?>

<form name="flist" action="ergebnisListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl">
  <td align="center" width="1%"><b>Nr</b></td>
  <td><b>Frage</b></td>
  <td width="13">&nbsp;</td>
  <td align="center"><b>Tln.</b></td>
  <td><b>Ergebnisse</b></td>
  <?php if($bLoeschen){?><td width="12">&nbsp;</td><?php }?>
 </tr>
<?php
foreach($aE as $a){ //Datenzeilen ausgeben
 $sNr=$a[0]; $sE=$a[2].';0;0'; $n=$a[3]-1;
 $nG=0; $p=-1; for($i=0;$i<$n;$i++){$nG+=(int)substr($sE,++$p); $p=strpos($sE,';',$p);}
 echo ' <tr class="admTabl">'.NL;
 echo '  <td align="center" width="1%">'.sprintf('%'.UMF_NummerStellen.'d',$sNr).'</td>'.NL;
 echo '  <td>'.fUmfBB($a[1]).'</td>'.NL;
 echo '  <td><a href="ergebnisVorschau.php?'.$sQ.$sAmp.'nr='.$sNr.'"><img src="iconVorschau.gif" width="13" height="13" border="0" title="Grafikvorschau"></a></td>'.NL;
 echo '  <td align="center">'.$nG.'</td>'.NL;
 echo '  <td>'.substr($sE,0,$p).'</td>'.NL;
 if($bLoeschen) echo '  <td align="center"><input type="image" name="del'.$sNr.'" src="iconLoeschen.gif" width="12" height="13" border="0" title="Ergebnis '.$sNr.' rücksetzen"></td>'.NL;
 echo ' </tr>'.NL;
}
if($bLoeschen){
 echo ' <tr class="admTabl">'.NL;
 echo '  <td align="center"><input type="image" name="del-1" src="iconLoeschen.gif" width="12" height="13" border="0" title="ganze Ergebnisliste rücksetzen"></td>'.NL;
 echo '  <td><i>gesamte Ergeblisliste leeren</i></td>'.NL.'  <td colspan="3">&nbsp;</td>'.NL.'  <td>&nbsp;</td>'.NL;
 echo ' </tr>'.NL;
}
?>

</table>
<input type="hidden" name="ddl" value="<?php echo $DDl?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>
</form>
<?php echo $sNavigator?>
<p style="text-align:center;">[ <a href="ergebnisListe.php<?php if($sQ) echo '?'.$sQ?>">aktualisieren</a> ]</p>

<?php
echo fSeitenFuss();

function fUmfNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:3px;margin-bottom:3px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}

//BB-Code zu HTML wandeln
function fUmfBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($o)).'0%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="admText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="admText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="admText" target="_blank" href="http://',$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="admText"><li class="admText">',$p,6+$l);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$q.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $l=strpos($v,'<br />',$p);
   while($l<$e&&$l>0){$v=substr_replace($v,'</li><li class="admText">',$l,6); $e+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>