<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisvorschau','<link rel="stylesheet" type="text/css" href="'.UMFPFAD.'umfStyle.css">','EEl');

$sQs=$_SERVER['QUERY_STRING']; if(!$p=strpos($sQs,'&amp;nr=')) if(!$p=strpos($sQs,'&nr=')) $p=strpos($sQs,'nr=');
if($sQs=substr($sQs,0,$p)) $sQs='?'.$sQs; $X=''; $nAntwAnzahl=max(20,ADU_AntwortZahl);
if($nNr=(int)$_GET['nr']){

 $aF=array(); $aE=array(); $nSaetze=0; //Frage holen
 if(!UMF_SQL){
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $sLn=$aD[$i]; $p=strpos($sLn,';'); if(substr($sLn,0,$p)==$nNr){$aF=explode(';',rtrim($sLn)); break;}
  }
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nCnt=count($aD);
  for($i=1;$i<$nCnt;$i++) if((int)$aD[$i]==$nNr){$aE=explode(';',rtrim($aD[$i])); array_shift($aE); break;}
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' WHERE Nummer="'.$nNr.'"')){
   $aF=$rR->fetch_row(); $rR->close();
   if($rR=$DbO->query('SELECT Nummer,Inhalt FROM '.UMF_SqlTabE.' WHERE Nummer="'.$nNr.'"')){
    if($a=$rR->fetch_row()) $aE=explode(';',$a[1]); $rR->close();
   }else $SqlFehl=UMF_TxSqlFrage;
  }else $SqlFehl=UMF_TxSqlFrage;
 }

 if(count($aF)&&count($aE)&&count($aF)>2&&count($aE)>1){ //Frage+Antworten gefunden
  $sF=fUmfBB(fUmfTx($aF[3])); $aA=array(); $i=-1; $nMx=0; $nSum=0; $k=0;
  for($i=0;$i<$nAntwAnzahl;$i++){if(isset($aF[7+$i])&&$aF[7+$i]) $aA[$k++]=$aF[7+$i]; $nMx=max($aE[$i],$nMx); $nSum+=$aE[$i];} //Antwortenschleife
  $nZ=count($aA); $nF=UMF_GrafikMaximum/max($nMx,1); $nW=round(100/$nZ); if($nSum==0) $nSum=1;
  $X =' <table class="umfGraf">';
  if(UMF_GrafikBalken){
   $sFrage="\n  <tr>\n"; $sFrage.='   <td class="umfGraF" colspan="'.(UMF_GrafikWerte=='links'||UMF_GrafikWerte=='rechts'?4:3).'">'.$sF.(UMF_GrafikTlnAnz?' ('.$nSum.'&nbsp;'.fUmfTx(UMF_TxTeilnehmer).')':'')."</td>\n  </tr>";
   if(UMF_GrafikFrage=='oben') $X.=$sFrage;
   for($i=0;$i<$nZ;$i++){
    $X.="\n  <tr>";
    $X.="\n".'   <td class="umfGrAB">'.fUmfBB(fUmfTx($aA[$i])).'</td>';
    if(UMF_GrafikWerte=='links') $X.="\n".'   <td class="umfGraE">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
    $X.="\n".'   <td class="umfGrGB"><img src="'.UMFPFAD.'balken.gif" width="'.round($nF*$aE[$i]).'" height="'.UMF_GrafikDicke.'" border="0" alt="'.$aE[$i].'" title="'.$aE[$i].'"></td>';
    if(UMF_GrafikWerte=='rechts') $X.="\n".'   <td class="umfGraE">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
    $X.="\n  </tr>";
   }
  }else{ //Saeulengrafik
   $sFrage="\n  <tr>\n"; $sFrage.='   <td class="umfGraF" colspan="'.$nZ.'">'.$sF."</td>\n  </tr>";
   $sWerte="\n  <tr>";
   for($i=0;$i<$nZ;$i++) $sWerte.="\n".'   <td class="umfGraE" style="width:'.$nW.'%">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
   $sWerte.="\n  </tr>";
   if(UMF_GrafikFrage=='oben') $X.=$sFrage;
   if(UMF_GrafikWerte=='oben') $X.=$sWerte;
   $X.="\n  <tr>";
   for($i=0;$i<$nZ;$i++) $X.="\n".'   <td class="umfGrGS" style="width:'.$nW.'%"><img src="'.UMFPFAD.'saeule.gif" width="'.UMF_GrafikDicke.'" height="'.round($nF*$aE[$i]).'" border="0" alt="'.$aE[$i].'" title="'.$aE[$i].'"></td>';
   $X.="\n  </tr>\n  <tr>";
   for($i=0;$i<$nZ;$i++) $X.="\n".'   <td class="umfGrAS" style="width:'.$nW.'%">'.fUmfBB(fUmfTx($aA[$i])).'</td>';
   $X.="\n  </tr>";
   if(UMF_GrafikWerte=='unten') $X.=$sWerte;
  }
  if(UMF_GrafikFrage=='unten') $X.=$sFrage;
  $X.="\n </table>";
  $X=(!empty($Meld)?' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n":'').$X;
 }else{ //keine Frage gefunden
  $X=' <p class="umfFehl">'.fUmfTx(UMF_TxFrageFehlt)."</p>\n";
  if(isset($SqlFehl)) $X.=' <p class="umfFehl">'.fUmfTx($SqlFehl)."</p>\n";
 }

}else $nNr='UNBEKANNT';
?>

<div align="center">

<?php echo '<p class="umfMeld" style="margin-top:32px;">Frage Nummer '.$nNr."</p>\n\n".$X?>

</div>
<p align="center" style="margin:32px;">[ <a href="ergebnisListe.php<?php echo $sQs?>">zurück zur Liste</a> ]</p>

<?php
echo fSeitenFuss();

function fUmfTx($sTx){ //TextKodierung
 return str_replace('\n ','<br />',$sTx);
}
function fUmfBB($v){//BB-Code zu HTML
 $p=strpos($v,'[');
 while(!($p===false)){
  $t=substr($v,$p,9);
  if(substr($t,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($t,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($t,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($t,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($t,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($t,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($t,0,7)=='[color='){$w=substr($v,$p+7,9); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="color:'.$w.';">',$p,8+strlen($w));}
  elseif(substr($t,0,6)=='[size='){ $w=substr($v,$p+6,4); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($w)).'0%;">',$p,7+strlen($w));}
  elseif(substr($t,0,8)=='[/color]')$v=substr_replace($v,'</span>',$p,8);
  elseif(substr($t,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($t,0,8)=='[center]'){$v=substr_replace($v,'<p class="umfText" style="text-align:center">',$p,8);if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,7)=='[right]') {$v=substr_replace($v,'<p class="umfText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0)=='[/center]') {$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,5)=='[url]'){
   $m=$p+5; if(!$e=min(strpos($v,'[',$m),strpos($v,' ',$m))) $e=strpos($v,'[',$m);
   if(substr($v,$e,1)==' ') $v=substr_replace($v,'">',$e,1); else $v=substr_replace($v,'">'.substr($v,$m,$e-$m),$e,0);
   $v=substr_replace($v,'<a class="umfText" target="_blank" href="http://',$p,5);
  }elseif(substr($t,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($t,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5));
   if(substr($w,0,1)=='/') if($e=strrpos($w,'/')) if($e=strpos(UMF_Pfad,substr($w,0,$e+1))) $w=substr(UMF_Pfad,0,$e).$w;
   if(($a=@getimagesize($w))&&is_array($a)) $w='<img class="umfText" '.$a[3].' src="'; else $w='<pic a="';
   $v=substr_replace($v,$w,$p,5);
  }elseif(substr($t,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($t,0,5)=='[list'){
   if(substr($t,5,2)=='=o'){$w='o';$m=2;}else{$w='u';$m=0;}
   $v=substr_replace($v,'<'.$w.'l class="umfText"><li class="umfText">',$p,6+$m);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$w.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $m=strpos($v,'<br />',$p);
   while($m<$e&&$m>0){$v=substr_replace($v,'</li><li class="umfText">',$m,6); $e+=19; $m=strpos($v,'<br />',$m);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>