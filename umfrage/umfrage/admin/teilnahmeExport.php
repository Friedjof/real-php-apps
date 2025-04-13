<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Teilnahmeliste exportieren','','ETl');

for($i=59;$i>=0;$i--) if(file_exists(UMF_Pfad.'temp/tln_'.sprintf('%02d',$i).'.csv')) unlink(UMF_Pfad.'temp/tln_'.sprintf('%02d',$i).'.csv');

$aQ=array(); $sQ=''; $a1Filt=array(); $a2Filt=array(); $a3Filt=array(); $bNr=$bDt=$bSt=$bAr=$bNz=$bEr=true; //Suchparameter
if(defined('ADU_TeilnahmeExport')){
 $s=ADU_TeilnahmeExport;
 $bNr=(substr($s,0,1)=='1'?true:false); $bDt=(substr($s,2,1)=='1'?true:false); $bSt=(substr($s,4,1)=='1'?true:false);
 $bAr=(substr($s,6,1)=='1'?true:false); $bNz=(substr($s,8,1)=='1'?true:false); $bEr=(substr($s,10,1)=='1'?true:false);
}else{
 $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php')))); $bSt=$bEr=false;
 if($p=strpos($sWerte,"define('ADU_NutzerLaenge'")) $sWerte=substr_replace($sWerte,"define('ADU_TeilnahmeExport','1;1;0;1;1;0'); //**\n",$p,0);
 if($f=fopen(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php','w')){
  fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
 }
}

if($FNr1=(isset($_POST['fnr1'])?$_POST['fnr1']:'').(isset($_GET['fnr1'])?$_GET['fnr1']:'')){$a1Filt[0]=$FNr1; $sQ.='&amp;fnr1='.$FNr1; $aQ['fnr1']=$FNr1;}
if($FNr2=(isset($_POST['fnr2'])?$_POST['fnr2']:'').(isset($_GET['fnr2'])?$_GET['fnr2']:'')){$a2Filt[0]=$FNr2; $sQ.='&amp;fnr2='.$FNr2; $aQ['fnr2']=$FNr2;}
if($Dat1=fNormDat((isset($_POST['dat1'])?$_POST['dat1']:'').(isset($_GET['dat1'])?$_GET['dat1']:''))){$a1Filt[1]=$Dat1; $sQ.='&amp;dat1='.$Dat1; $aQ['dat1']=$Dat1;}
if($Dat2=fNormDat((isset($_POST['dat2'])?$_POST['dat2']:'').(isset($_GET['dat2'])?$_GET['dat2']:''))){$a2Filt[1]=$Dat2; $sQ.='&amp;dat2='.$Dat2; $aQ['dat2']=$Dat2;}
$s=(isset($_POST['sta1'])?$_POST['sta1']:'').(isset($_GET['sta1'])?$_GET['sta1']:''); if(strlen($s)){$a1Filt[2]=$s; $sQ.='&amp;sta1='.$s; $aQ['sta1']=$s;}
$s=(isset($_POST['sta2'])?$_POST['sta2']:'').(isset($_GET['sta2'])?$_GET['sta2']:''); if(strlen($s)){$a2Filt[2]=$s; $sQ.='&amp;sta2='.$s; $aQ['sta2']=$s;}
$s=(isset($_POST['sta3'])?$_POST['sta3']:'').(isset($_GET['sta3'])?$_GET['sta3']:''); if(strlen($s)){$a3Filt[2]=$s; $sQ.='&amp;sta3='.$s; $aQ['sta3']=$s;}
$s=(isset($_POST['art1'])?$_POST['art1']:'').(isset($_GET['art1'])?$_GET['art1']:''); if(strlen($s)){$a1Filt[3]=$s; $sQ.='&amp;art1='.$s; $aQ['art1']=$s;}
$s=(isset($_POST['art2'])?$_POST['art2']:'').(isset($_GET['art2'])?$_GET['art2']:''); if(strlen($s)){$a2Filt[3]=$s; $sQ.='&amp;art2='.$s; $aQ['art2']=$s;}
$s=(isset($_POST['art3'])?$_POST['art3']:'').(isset($_GET['art3'])?$_GET['art3']:''); if(strlen($s)){$a3Filt[3]=$s; $sQ.='&amp;art3='.$s; $aQ['art3']=$s;}
$s=(isset($_POST['ntz1'])?$_POST['ntz1']:'').(isset($_GET['ntz1'])?$_GET['ntz1']:''); if(strlen($s)){$a1Filt[4]=$s; $sQ.='&amp;ntz1='.rawurlencode($s); $aQ['ntz1']=$s;}
$s=(isset($_POST['ntz2'])?$_POST['ntz2']:'').(isset($_GET['ntz2'])?$_GET['ntz2']:''); if(strlen($s)){$a2Filt[4]=$s; $sQ.='&amp;ntz2='.rawurlencode($s); $aQ['ntz2']=$s;}
$s=(isset($_POST['ntz3'])?$_POST['ntz3']:'').(isset($_GET['ntz3'])?$_GET['ntz3']:''); if(strlen($s)){$a3Filt[4]=$s; $sQ.='&amp;ntz3='.rawurlencode($s); $aQ['ntz3']=$s;}

$aD=array(); //$aD[0]='#'; //Daten holen
if(!UMF_SQL){ //Textdaten
 $aT=@file(UMF_Pfad.UMF_Daten.UMF_Teilnahme); array_shift($aT); $i=0;
 foreach($aT as $s){
  $a=explode(';',rtrim($s),5); array_unshift($a,++$i);
  $b=explode(',',$a[4]); $t=array_shift($b); if($a[3]=='T') $t=fUmfDeCode($t); foreach($b as $s) $t.=', '.fUmfDeCode($s); $a[4]=$t;
  $b=true;
  if(count($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
   foreach($a1Filt as $j=>$v) if($b&&$j>1){
    $w=(isset($a2Filt[$j])?$a2Filt[$j]:'');
    if(strlen($w)){if(strlen(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$w))) $b2=true; else $b2=false;} else $b2=false;
    if(!(strlen(stristr((isset($a[$j])?str_replace('`,',';','~'.$a[$j]):''),$v))||$b2)) $b=false;
   }elseif($j==0){ //Nr
    if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)){if($a[0]!=$v) $b=false;}else{if($a[0]<$v||$a[0]>$w) $b=false;}
   }else{ //Datum
    if(!$w=(isset($a2Filt[1])?$a2Filt[1]:0)){if(substr($a[1],0,10)!=$v) $b=false;}else{if($a[1]<$v||$a[1]>($w.'x')) $b=false;}
   }
  }
  if($b&&count($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v) if(strlen(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v))){$b=false; break;}
  }
  if($b) $aD[]=$a;
 }
}elseif($DbO){ //SQL-Daten
 $s='';
 if(count($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sF=($j==3?'Art':($j==2?'Status':'Nutzer'));
   $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){ //Nr
   if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)) $s.=' AND Nummer="'.$v.'"'; else $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"';
  }else{ //Datum
   if(!$w=(isset($a2Filt[1])?$a2Filt[1]:0)) $s.=' AND Datum LIKE "'.$v.'%"'; else $s.=' AND Datum BETWEEN "'.$v.'" AND "'.$w.'x"';
  }
 }
 if(count($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j==3?'Art':($j==2?'Status':'Nutzer')).' LIKE "%'.$v.'%")';
 }
 if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabT.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  while($a=$rR->fetch_row()) $aD[]=$a; $rR->close();
 }
}

if($_SERVER['REQUEST_METHOD']=='POST'){
 $sZ=''; $sADU='';
 if($bNr=(isset($_POST['Nr'])?($_POST['Nr']?true:false):false)){$sADU.='1;'; $sZ.='Nummer;';}else $sADU.='0;';
 if($bDt=(isset($_POST['Dt'])?($_POST['Dt']?true:false):false)){$sADU.='1;'; $sZ.='Datum;';}else $sADU.='0;';
 if($bSt=(isset($_POST['St'])?($_POST['St']?true:false):false)){$sADU.='1;'; $sZ.='Status;';}else $sADU.='0;';
 if($bAr=(isset($_POST['Ar'])?($_POST['Ar']?true:false):false)){$sADU.='1;'; $sZ.='Art;';}else $sADU.='0;';
 if($bNz=(isset($_POST['Nz'])?($_POST['Nz']?true:false):false)){$sADU.='1;'; $sZ.='Besucher;';}else $sADU.='0;';
 if($bEr=(isset($_POST['Er'])?($_POST['Er']?true:false):false)){$sADU.='1;'; $sZ.='Ergebnis;';}else $sADU.='0;';
 $sEx=substr($sZ,0,-1)."\n"; $sADU=substr($sADU,0,-1);
 foreach($aD as $a){
  $sZ=''; $sD=$a[1];
  if($bNr) $sZ.=$a[0].';';
  if($bDt) $sZ.=substr($sD,8,2).'.'.substr($sD,5,2).'.'.substr($sD,0,4).substr($sD,10).';';
  if($bSt) $sZ.=$a[2].';';
  if($bAr) $sZ.=$a[3].';';
  if($bNz) $sZ.=$a[4].';';
  if($bEr) $sZ.=$a[5].';';
  $sEx.=substr($sZ,0,-1)."\n";
 }
 if($sEx=trim($sEx)){
  $i=sprintf('%02d',date('s'));
  if($f=fopen(UMF_Pfad.'temp/tln_'.$i.'.csv','w')){
   $sMeld.='<p class="admErfo">Die Daten wurden als <a href="http://'.UMF_Www.'temp/tln_'.$i.'.csv"><i>tln_'.$i.'.csv</i></a> ins Verzeichnis <i>/temp/</i> exportiert!</p>';
   fwrite($f,$sEx."\n"); fclose($f); $MTyp='Erfo';
  }else $sMeld='<p class="admFehl">'.str_replace('#','temp/tln_'.$i.'.csv',UMF_TxDateiRechte).'</p>';
 }else $sMeld=fMMeld('Keine Daten zu exportieren!');
 if($sADU!=ADU_TeilnahmeExport){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php'))));
  setzAdmWert($sADU,'TeilnahmeExport',"'");
  if($f=fopen(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
  }
 }
}

if(!$sMeld) $sMeld=fMMeld((!$sQ?'Gesamt-Teilnahmeliste':'Abfrageergebnis').' exportieren ('.count($aD).' Eintragungen)');
echo '<div style="text-align:center;margin:16px">'.$sMeld.'</div>';

$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<form name="umfListe" action="teilnahmeExport.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" style="width:auto;margin:auto" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td>Nummer</td><td>Datum</td><td>Status</td><td>Gast/Teilnehmer/Benutzer</td><td>Besucherdaten</td><td>Ergebnis</td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Nr<?php if($bNr) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Dt<?php if($bDt) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="St<?php if($bSt) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Ar<?php if($bAr) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Nz<?php if($bNz) echo '" checked="checked'?>" value="1" /></td>
  <td style="text-align:center"><input type="checkbox" class="admCheck" name="Er<?php if($bEr) echo '" checked="checked'?>" value="1" /></td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Exportieren" /></p>
<?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>
</form>
<p style="text-align:center;">[ <a href="teilnahmeListe.php<?php if($sQ) echo '?'.$sQ?>">zurück zur Teilnahmeliste</a> ]</p>

<?php
echo fSeitenFuss();

function fNormDat($s){
 if(strpos($s,'.')){
  $a=explode('.',str_replace(':','.',str_replace(',','.',str_replace(' ','',trim($s)))));
  $s=sprintf('%d-%02d-%02d',(isset($a[2])?((int)$a[2]<2000?2000+$a[2]:$a[2]):2000),(isset($a[1])?$a[1]:0),$a[0]);
 }
 return $s;
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