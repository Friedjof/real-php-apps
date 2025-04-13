<?php
include 'hilfsFunktionen.php'; header('Content-Type: text/html; charset=ISO-8859-1');
$aDr[0]=sprintf('%0d',(isset($_POST['drN'])?$_POST['drN']:0)); $p=(isset($_POST['drNn'])?(int)$_POST['drNn']:0); if($aDr[0]&&$p) $aDr[0]=$p;
$aDr[1]=sprintf('%0d',(isset($_POST['drA'])?$_POST['drA']:0)); $aDr[2]=sprintf('%0d',(isset($_POST['drV'])?$_POST['drV']:0)); $aDr[3]=sprintf('%0d',(isset($_POST['drK'])?$_POST['drK']:0));
$aDr[4]=sprintf('%0d',(isset($_POST['drF'])?$_POST['drF']:0)); $aDr[5]=sprintf('%0d',(isset($_POST['drL'])?$_POST['drL']:0)); $aDr[6]=sprintf('%0d',(isset($_POST['drP'])?$_POST['drP']:0));
$aDr[7]=sprintf('%0d',(isset($_POST['drG'])?$_POST['drG']:0));
$aDr['G']=(isset($_POST['d_G'])?$_POST['d_G']:''); $aDr['B']=(isset($_POST['d_B'])?(int)$_POST['d_B']:0);
$aDr[8]=sprintf('%0d',(isset($_POST['drT'])?$_POST['drT']:0)); $aDr[9]=sprintf('%0d',(isset($_POST['drB'])?$_POST['drB']:0));
$aDr[10]=sprintf('%0d',(isset($_POST['dr2'])?$_POST['dr2']:0));
$aDr[11]=sprintf('%0d',(isset($_POST['drS'])?$_POST['drS']:0)); $aDr[12]=sprintf('%0d',(isset($_POST['drR'])?$_POST['drR']:0)); $aDr[13]=sprintf('%0d',(isset($_POST['drZ'])?$_POST['drZ']:0));
$aDr[14]=(isset($_POST['drH'])?$_POST['drH']:'');
if($aDr[11]) $sHtml=@implode('',@file('druckListe.htm')); else $sHtml='';
if($sHtml) if($p=strpos($sHtml,'{Inhalt}')){echo substr($sHtml,0,$p); $sHtml=substr($sHtml,$p+8)."\n";}else $sHtml='';
if(!$sHtml) echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Testfragen-Script - Drucken</title>
<link rel="stylesheet" type="text/css" href="admin.css">
</head>

<body class="admDruck">
<h1 style="font-size:130%"><img src="_frage.gif" width="16" height="24" border="0" align="bottom" alt=""> Testfragen-Script: Fragenliste '.FRA_Konfiguration.'</h1>
';

$aQ=array(); $sQ=''; //Suchparameter
if($FNr1=(isset($_POST['fnr1'])?$_POST['fnr1']:'')){$a1Filt[0]=$FNr1; $sQ.='&amp;fnr1='.$FNr1; $aQ['fnr1']=$FNr1;}
if($FNr2=(isset($_POST['fnr2'])?$_POST['fnr2']:'')){$a2Filt[0]=$FNr2; $sQ.='&amp;fnr2='.$FNr2; $aQ['fnr2']=$FNr2;}
$Onl=(isset($_POST['onl'])?$_POST['onl']:'').(isset($_POST['onl1'])?$_POST['onl1']:'').(isset($_POST['onl2'])?$_POST['onl2']:'');
$Vst=(isset($_POST['vst'])?$_POST['vst']:'').(isset($_POST['vst1'])?$_POST['vst1']:'').(isset($_POST['vst2'])?$_POST['vst2']:'');
if(strlen($Onl)!=1) $Onl=''; else {$a1Filt[1]=$Onl; $sQ.='&amp;onl='.$Onl; $aQ['onl1']=$Onl;}
if(strlen($Vst)!=1) $Vst=''; else {$a1Filt[2]=$Vst; $sQ.='&amp;vst='.$Vst; $aQ['vst1']=$Vst;}
$s=(isset($_POST['kat1'])?$_POST['kat1']:''); if(strlen($s)){$a1Filt[3]=$s; $sQ.='&amp;kat1='.rawurlencode($s); $aQ['kat1']=$s;}
$s=(isset($_POST['kat2'])?$_POST['kat2']:''); if(strlen($s)){$a2Filt[3]=$s; $sQ.='&amp;kat2='.rawurlencode($s); $aQ['kat2']=$s;}
$s=(isset($_POST['kat3'])?$_POST['kat3']:''); if(strlen($s)){$a3Filt[3]=$s; $sQ.='&amp;kat3='.rawurlencode($s); $aQ['kat3']=$s;}
$s=(isset($_POST['frg1'])?$_POST['frg1']:''); if(strlen($s)){$a1Filt[4]=$s; $sQ.='&amp;frg1='.rawurlencode($s); $aQ['frg1']=$s;}
$s=(isset($_POST['frg2'])?$_POST['frg2']:''); if(strlen($s)){$a2Filt[4]=$s; $sQ.='&amp;frg2='.rawurlencode($s); $aQ['frg2']=$s;}
$s=(isset($_POST['frg3'])?$_POST['frg3']:''); if(strlen($s)){$a3Filt[4]=$s; $sQ.='&amp;frg3='.rawurlencode($s); $aQ['frg3']=$s;}
$s=(isset($_POST['bem1'])?$_POST['bem1']:''); if(strlen($s)){$a1Filt[17]=$s;$sQ.='&amp;bem1='.rawurlencode($s); $aQ['bem1']=$s;}
$s=(isset($_POST['bem2'])?$_POST['bem2']:''); if(strlen($s)){$a2Filt[17]=$s;$sQ.='&amp;bem2='.rawurlencode($s); $aQ['bem2']=$s;}
$s=(isset($_POST['bem3'])?$_POST['bem3']:''); if(strlen($s)){$a3Filt[17]=$s;$sQ.='&amp;bem3='.rawurlencode($s); $aQ['bem3']=$s;}
$s=(isset($_POST['b2m1'])?$_POST['b2m1']:''); if(strlen($s)){$a1Filt[18]=$s;$sQ.='&amp;b2m1='.rawurlencode($s); $aQ['b2m1']=$s;}
$s=(isset($_POST['b2m2'])?$_POST['b2m2']:''); if(strlen($s)){$a2Filt[18]=$s;$sQ.='&amp;b2m2='.rawurlencode($s); $aQ['b2m2']=$s;}
$s=(isset($_POST['b2m3'])?$_POST['b2m3']:''); if(strlen($s)){$a3Filt[18]=$s;$sQ.='&amp;b2m3='.rawurlencode($s); $aQ['b2m3']=$s;}

$aD=array(); $aTmp=array(); $aIdx=array(); //Daten holen
if(!FRA_SQL){ //Textdaten
 $aD=@file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true;
  if(isset($a1Filt)&&is_array($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
   foreach($a1Filt as $j=>$v) if($b&$j>2){
    if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')){if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)||$b2)) $b=false;
   }elseif($j==0){
    if($w=(isset($a2Filt[0])?$a2Filt[0]:0)){if($a[0]<$v||$a[0]>$w) $b=false;}
    else if($a[0]!=$v) $b=false;
   }else if($a[$j]!=$v) $b=false;
  }
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v) if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}
  }
  if($b){ //Datensatz gültig
   $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; $aTmp[$sNr][3]=str_replace('`,',';',$a[3]); //Nr,akt,vst,Kat
   $aTmp[$sNr][4]=str_replace('\n ',NL,str_replace('`,',';',$a[4])); $aTmp[$sNr][5]=$a[5]; $aTmp[$sNr][6]=$a[6]; $aTmp[$sNr][7]=$a[7]; //Fra,Lsg,Pkt,Bld
   if($aDr[8]=='1') for($k=1;$k<=9;$k++) $aTmp[$sNr][7+$k]=str_replace('\n ',NL,str_replace('`,',';',$a[7+$k]));
   if($aDr[9]=='1') $aTmp[$sNr][17]=str_replace('\n ',NL,str_replace('`,',';',$a[17])); //Antw,Anm
   if($aDr[10]=='1') $aTmp[$sNr][18]=(isset($a[18])?str_replace('\n ',NL,str_replace('`,',';',$a[18])):''); //Anm2
   $aIdx[$sNr]=sprintf('%0'.FRA_NummerStellen.'d',$i);
  }
 }$aD=array();
}elseif($DbO){ //SQL-Daten
 $s='';
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>2){
   $sF=($j==4?'Frage':($j==3?'Kategorie':($j!=18?'Anmerkung':'Anmerkung2')));
   $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if($w=(isset($a2Filt[0])?$a2Filt[0]:0)) $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Nummer="'.$v.'"';
  }else $s.=' AND '.($j==0?'Nummer':($j==1?'aktiv':'versteckt')).'="'.$v.'"';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j==4?'Frage':($j==3?'Kategorie':($j!=18?'Anmerkung':'Anmerkung2'))).' LIKE "%'.$v.'%")';
 }
 if($rR=$DbO->query('SELECT Nummer,aktiv,versteckt,Kategorie,Frage,Loesung,Punkte,Bild'.($aDr[8]=='1'?',Antwort1,Antwort2,Antwort3,Antwort4,Antwort5,Antwort6,Antwort7,Antwort8,Antwort9':'').($aDr[9]=='1'?',Anmerkung':'').($aDr[10]=='1'?',Anmerkung2':'').' FROM '.FRA_SqlTabF.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  $i=0;
  while($a=$rR->fetch_row()){
   $sNr=(int)$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; $aTmp[$sNr][3]=$a[3]; //Nr,akt,vst,Kat
   $aTmp[$sNr][4]=str_replace("\r",'',$a[4]); $aTmp[$sNr][5]=$a[5]; $aTmp[$sNr][6]=$a[6]; $aTmp[$sNr][7]=$a[7]; //Fra,Lsg,Pkt,Bld
   if($aDr[8]=='1') for($k=1;$k<=9;$k++) $aTmp[$sNr][7+$k]=str_replace("\r",'',$a[7+$k]);
   if($aDr[9]=='1') $aTmp[$sNr][17]=str_replace("\r",'',$a[($aDr[8]=='1'?17:8)]); //Antw,Anm
   if($aDr[10]=='1') $aTmp[$sNr][18]=str_replace("\r",'',$a[($aDr[8]=='1'?($aDr[9]=='1'?18:17):8)]); //Anm2
   $aIdx[$sNr]=sprintf('%0'.FRA_NummerStellen.'d',++$i);
  }$rR->close();
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">keine MySQL-Verbindung!</p>';

if($aDr[12]) arsort($aIdx);
reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i]; $nNr=0;
if(!$sMeld) if($aDr[14]) $sMeld='<p class="admMeld">'.$aDr[14].'</p>';
if($aDr[11]&&!$sHtml) $sMeld='<p class="admFehl">Druckschablone <i>druckListe.htm</i> fehlt oder fehlerhaft!</p>';

echo '
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td>'.$sMeld.NL.'</td>
  <td style="width:64px;background-image:url(drucken.gif);background-repeat:no-repeat;"><a href="javascript:window.print()"><img src="'.FRAPFAD.'pix.gif" width="64" height="16" border="0" alt="drucken"></a></td>
 </tr>
</table>

<table class="admDru" border="0" cellpadding="0" cellspacing="0">
 <tr class="admTabl">'.
  ($aDr[0]>='1'?"\n  ".'<td class="admDru" align="center" width="1%"><b>Nr</b></td>':'').
  ($aDr[1]=='1'?"\n  ".'<td class="admDru" width="15" align="center" title="aktiviert"><b>A</b></td>':'').
  ($aDr[2]=='1'?"\n  ".'<td class="admDru" align="center" width="15" title="versteckt"><b>V</b></td>':'').
  ($aDr[3]=='1'&&FRA_Kategorien>''?"\n  ".'<td class="admDru"><b>Kategorie</b></td>':'').
  ($aDr[4]=='1'||$aDr[8]=='1'||$aDr[9]=='1'?"\n  ".'<td class="admDru"><b>'.rtrim(($aDr[4]=='1'?'Frage ':'').($aDr[8]=='1'?'Antworten ':'').($aDr[9]=='1'?'Anmerkung ':'').($aDr[10]=='1'?'Anmerkung-2':'')).'</b></td>':'').
  ($aDr[7]>='1'&&FRA_LayoutTyp>0?"\n  ".'<td class="admDru" align="center"'.($aDr['G']=='b'&&$aDr['B']?' style="width:'.$aDr['B'].'px"':'').'><b>Bild</b></td>':'').
  ($aDr[5]=='1'||$aDr[6]=='1'?"\n  ".'<td class="admDru" align="center" width="1%">'.($aDr[5]=='1'?'<b>Lösung</b>':'').($aDr[6]=='1'?'<div><b>Punkte</b></div>':'').'</td>':'').'
 </tr>
';
foreach($aD as $a){ //Datenzeilen ausgeben
 $sNr=$a[0]; $sR=','.$a[5];
 echo ' <tr class="admTabl" style="page-break-inside:avoid">'.NL;
 if($aDr[0]>='1') echo '  <td class="admDru" align="center" width="1%">'.sprintf('%'.FRA_NummerStellen.'d',($aDr[0]<'2'?$sNr:++$nNr)).'</td>'.NL;
 if($aDr[1]=='1') echo '  <td class="admDru" align="center">'.($a[1]?'a':'&nbsp;').'</td>'.NL;
 if($aDr[2]=='1') echo '  <td class="admDru" align="center">'.($a[2]?'v':'&nbsp;').'</td>'.NL;
 if(FRA_Kategorien>''&&$aDr[3]=='1') echo '  <td class="admDru">'.($a[3]?str_replace('#',' -&gt; ',$a[3]):'').'</td>'.NL;
 if($aDr[4]=='1'||$aDr[8]=='1'||$aDr[9]=='1'){
  echo '  <td class="admDru">'."\n";
  if($aDr[4]=='1') echo '   <div>'.fFraBB($a[4])."</div>\n";
  if($aDr[8]=='1'){
   $aA=array('#'); $aR=array(false); $sR='#,'.$a[5].',';
   for($i=1;$i<=9;$i++) if($s=$a[7+$i]){$aA[]=$s; $aR[]=(strpos($sR,','.$i.',')?true:false);}
   $nAw=count($aA); $m=$nAw; $sR='';
   for($i=1;$i<$nAw;$i++){
    if($aDr[13]){$k=rand(1,--$m); $s=$aA[$k]; if($aR[$k]) $sR.=','.$i; array_splice($aA,$k,1); array_splice($aR,$k,1);}
    else{$s=$aA[$i]; $sR=','.$a[5];}
    if($nP=strpos($s,'|#')) $s=substr($s,0,$nP).' ('.substr($s,$nP+2).' Pkt)';
    echo '   <div>('.$i.') '.fFraBB($s)."</div>\n";
   }
  }
  if($aDr[9]=='1') if($s=$a[17]) echo '   <div>(*) '.fFraBB($s)."</div>\n";
  if($aDr[10]=='1') if($s=(isset($a[18])?$a[18]:'')) echo '   <div>(#) '.fFraBB($s)."</div>\n";
  echo '  </td>'.NL;
 }
 if(FRA_LayoutTyp>0&&$aDr[7]>='1'){
  if(!$sBld=$a[7]) if(FRA_BildErsatz) $sBld=FRA_BildErsatz; $aV=explode(' ',$sBld); $sBld=$aV[0]; $sExt=strtolower(substr($sBld,strrpos($sBld,'.')+1));
  if($sExt=='jpg'||$sExt=='png'||$sExt=='gif'||$sExt=='jpeg'){
   echo '  <td class="admDru" style="width:auto">'.($aDr['G']!='b'?$sBld:'<img src="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.FRA_Bilder.$sBld.'"'.($aDr['B']?' style="width:100%;min-width:'.$aDr['B'].'px;max-width:'.$aDr['B'].'px"':'').' border="0" alt="Bild">').'</td>'.NL;
  }elseif($sExt=='mp4'||$sExt=='ogg'||$sExt=='ogv'||$sExt=='webm'){
    $VidW=(isset($aV[1])?(int)$aV[1]:0); $VidH=(isset($aV[2])?(int)$aV[2]:0); if($sExt=='ogv') $sExt='ogg';
    echo '  <td class="admDru" style="width:auto">'.($aDr['G']!='b'?$sBld:'<video controls type="video/'.$sExt.'" src="'.$sBld.'" style="'.($VidW?'width:'.$VidW.'px;':'').($VidH?'height:'.$VidH.'px;':'').'max-width:100%" title="'.$sBld.'">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></video>').'</td>'.NL;
  }elseif($sExt=='mp3'||$sExt=='ogg'){
    echo '  <td class="admDru" style="width:auto">'.($aDr['G']!='b'?$sBld:'<audio controls type="audio/'.$sExt.'" src="'.$sBld.'" style="max-width:100%" title="'.$sBld.'">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></audio>').'</td>'.NL;
  }elseif(strpos($sBld,'youtube.com/')){
   $VidW=(isset($aV[1])?(int)$aV[1]:0); $VidH=(isset($aV[2])?(int)$aV[2]:0);
   echo '  <td class="admDru" style="width:auto">'.($aDr['G']!='b'?$sBld:'<iframe src="'.$sBld.'" style="'.($VidW?'width:'.$VidW.'px;':'').($VidH?'height:'.$VidH.'px;':'').'max-width:100%" frameborder="0" allowfullscreen="">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></iframe>').'</td>'.NL;
  }else echo '  <td class="admDru" style="width:auto">&nbsp;</td>'.NL;

 }
 if($aDr[5]=='1'||$aDr[6]=='1') echo '  <td class="admDru" align="center">'.($aDr[5]=='1'?substr($sR,1):'').'<div>'.($aDr[6]=='1'?$a[6]:'').'</div></td>'.NL;
 echo ' </tr>'.NL;
}
echo '</table>
<p>'.date('d.m.Y, H:i:s').'</p>
'.$sHtml;

if(!$sHtml) echo '
</body>
</html>
';

//BB-Code zu HTML wandeln
function fFraBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,10);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));}
  elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($o)).'0%">',$p,7+strlen($o));}
  elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="fraText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="fraText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[sup]') $v=substr_replace($v,'<sup>',$p,5); elseif(substr($Tg,0,6)=='[/sup]') $v=substr_replace($v,'</sup>',$p,6);
  elseif(substr($Tg,0,5)=='[sub]') $v=substr_replace($v,'<sub>',$p,5); elseif(substr($Tg,0,6)=='[/sub]') $v=substr_replace($v,'</sub>',$p,6);
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; $e=strpos($v,'[',$o); $l=strpos($v,' ',$o); if($l<$e&&$l>$p) $e=$l; if(substr($v,$e,1)!=' ') $l=5; else $l-=$p;
   $v=substr_replace($v,'',$p,$l);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'',$p,6);
  elseif(substr($Tg,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5)); $a=NULL; $u='';
   if(strpos($w,'://')){ //URL
    if(!$a=@getimagesize($w)) if($e=strpos($w,FRA_Www)) $a=@getimagesize(FRA_Pfad.substr($w,$e+strlen(FRA_Www)));
   }else{ //nur Pfad
    if(substr($w,0,1)=='/'){ //absoluter Pfad
     $u=$_SERVER['DOCUMENT_ROOT']; if(!strpos($w,substr($u,strpos($u,'/')+1)).'/') $u.=$w; $a=@getimagesize($u); $u='';
    }else{$w=FRAPFAD.$w; $a=@getimagesize($w); $u=FRAPFAD;} //relativer Pfad
   }
   $w='<img class="fraText" '.(is_array($a)?$a[3].' ':'').'src="'.$u; $v=substr_replace($v,$w,$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($Tg,0,9)=='[youtube '){
   $n=strpos($v,']',$p+9); $w=trim(substr($v,$p+9,$n-($p+9))); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+9); $w=trim(substr($v,++$n,$e-$n));
    $v=substr_replace($v,'<iframe width="'.$a[0].'" height="'.$a[1].'" src="',$p,$l+10);
   }else{$v=substr_replace($v,'',$p+8,$n-($p+8)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($Tg,0,9)=='[youtube]'){
   $e=strpos($v,'[',$p+9); $w=trim(substr($v,$p+9,$e-($p+9)));
   $v=substr_replace($v,'<iframe src="',$p,9);
  }elseif(substr($Tg,0,10)=='[/youtube]'){$v=substr_replace($v,'" frameborder="0" allowfullscreen="">Ihr Browser zeigt keine iFrames. Siehe <a href="'.$w.'" target="_new">Youtube</a>.</iframe>',$p,10);}
  elseif(substr($Tg,0,7)=='[video '){
   $n=strpos($v,']',$p+7); $w=substr($v,$p+7,$n-($p+7)); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+7);  $w=substr($v,$n+1,$e-($n+1));
    $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
    $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
    $u='<video width="'.$a[0].'" height="'.$a[1].'" controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,$l+8);
   }else{$v=substr_replace($v,'',$p+6,$n-($p+6)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($Tg,0,7)=='[video]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<video controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($Tg,0,8)=='[/video]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Video</a> nicht.</video>',$p,8);
  elseif(substr($Tg,0,7)=='[audio]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<audio controls'.($e?' type="audio/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($Tg,0,8)=='[/audio]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Audio</a> nicht.</audio>',$p,8);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="fraText"><li class="fraText">',$p,6+$l);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$q.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $l=strpos($v,'<br />',$p);
   while($l<$e&&$l>0){$v=substr_replace($v,'</li><li class="fraText">',$l,6); $e+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>