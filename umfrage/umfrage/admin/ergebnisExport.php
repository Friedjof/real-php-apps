<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisliste exportieren','','EEl');

for($i=59;$i>=0;$i--) if(file_exists(UMF_Pfad.'temp/erg_'.sprintf('%02d',$i).'.csv')) unlink(UMF_Pfad.'temp/erg_'.sprintf('%02d',$i).'.csv');

$aQ=array(); $sQ=''; $a1Filt=array(); $a2Filt=array(); $a3Filt=array(); $bNr=$bFr=$bTn=$bEg=true; //Suchparameter
if(defined('ADU_ErgebnisExport')){
 $s=ADU_ErgebnisExport;
 $bNr=(substr($s,0,1)=='1'?true:false); $bFr=(substr($s,2,1)=='1'?true:false);
 $bTn=(substr($s,4,1)=='1'?true:false); $bEg=(substr($s,6,1)=='1'?true:false);
}else{
 $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php')))); $bFr=$bTn=false;
 if($p=strpos($sWerte,"define('ADU_TeilnahmeLaenge'")) $sWerte=substr_replace($sWerte,"define('ADU_ErgebnisExport','1;0;0;1'); //**\n",$p,0);
 if($f=fopen(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php','w')){
  fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
 }
}

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
   $aD[$nNr][1]=str_replace('\n ',NL,str_replace('`,',';',$a[3])); //Fra
   $aD[$nNr][2]=(isset($aE[$nNr])?$aE[$nNr]:'0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0'); //Erg
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
   $aD[$sNr][1]=str_replace("\r",'',$a[3]); $aD[$sNr][2]=$a[27]; //Fra,Erg
   $nA=6; while(isset($a[++$nA])&&strlen($a[$nA])>0); $aD[$sNr][3]=$nA-6;
  }$rR->close();
 }else $sMeld=fMFehl(UMF_TxSqlFrage);
}

$aE=array(); foreach($aD as $i=>$xx) $aE[]=$aD[$i];

if($_SERVER['REQUEST_METHOD']=='POST'){
 $sZ=''; $sADU='';
 if($bNr=(isset($_POST['Nr'])?($_POST['Nr']?true:false):false)){$sADU.='1;'; $sZ.='Fragen-Nr;';}else $sADU.='0;';
 if($bFr=(isset($_POST['Fr'])?($_POST['Fr']?true:false):false)){$sADU.='1;'; $sZ.='Frage;';}else $sADU.='0;';
 if($bTn=(isset($_POST['Tn'])?($_POST['Tn']?true:false):false)){$sADU.='1;'; $sZ.='Teilnehmeranzahl;';}else $sADU.='0;';
 if($bEg=(isset($_POST['Eg'])?($_POST['Eg']?true:false):false)) $sADU.='1;'; else $sADU.='0;';
 $sEx=''; $sEx1=substr($sZ,0,-1); $sADU=substr($sADU,0,-1); $nEMx=0;
 foreach($aE as $a){
  $sZ=''; $sNr=$a[0]; $sE=$a[2].';0;0'; $n=$a[3]-1; $nEMx=max($n,$nEMx);
  $nG=0; $p=-1; for($i=0;$i<$n;$i++){$nG+=(int)substr($sE,++$p); $p=strpos($sE,';',$p);}
  if($bNr) $sZ.=$sNr.';';
  if($bFr) $sZ.=$a[1].';';
  if($bTn) $sZ.=$nG.';';
  if($bEg) $sZ.=substr($sE,0,$p).';';
  $sEx.=substr($sZ,0,-1)."\n";
 }
 if($sEx=trim($sEx)){
  $i=sprintf('%02d',date('s')); if($bEg) for($i=1;$i<=$nEMx;$i++) $sEx1.=';Antwort-'.$i;
  if($f=fopen(UMF_Pfad.'temp/erg_'.$i.'.csv','w')){
   $sMeld.='<p class="admErfo">Die Daten wurden als <a href="http://'.UMF_Www.'temp/erg_'.$i.'.csv"><i>erg_'.$i.'.csv</i></a> ins Verzeichnis <i>/temp/</i> exportiert!</p>';
   fwrite($f,$sEx1."\n".$sEx."\n"); fclose($f); $MTyp='Erfo';
  }else $sMeld='<p class="admFehl">'.str_replace('#','temp/erg_'.$i.'.csv',UMF_TxDateiRechte).'</p>';
 }else $sMeld=fMMeld('Keine Daten zu exportieren!');
 if($sADU!=ADU_ErgebnisExport){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php'))));
  setzAdmWert($sADU,'ErgebnisExport',"'");
  if($f=fopen(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
  }
 }
}

if(!$sMeld) $sMeld=fMMeld((!$sQ?'Gesamt-Ergebnisliste':'Abfrageergebnis').' exportieren ('.count($aD).' Eintragungen)');
echo '<div style="text-align:center;margin:16px">'.$sMeld.'</div>';

$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<form name="flist" action="ergebnisExport.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" style="width:auto;margin:auto" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td>Fragen-Nr</td><td>Frage</td><td>Teilnehmeranzahl</td><td>Ergebnisse</td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Nr<?php if($bNr) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Fr<?php if($bFr) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Tn<?php if($bTn) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Eg<?php if($bEg) echo '" checked="checked'?>" value="1" /></td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Exportieren" /></p>
<?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>
</form>
<p style="text-align:center;">[ <a href="ergebnisListe.php<?php if($sQ) echo '?'.$sQ?>">zurück zur Ergebnisliste</a> ]</p>

<?php
echo fSeitenFuss();

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
function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADU_'.$n)){
  $p=strpos($sWerte,'ADU_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADU_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>