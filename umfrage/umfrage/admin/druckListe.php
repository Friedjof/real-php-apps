<?php
header('Content-Type: text/html; charset=ISO-8859-1'); include 'hilfsFunktionen.php';
$aDr[0]=sprintf('%0d',(isset($_POST['drN'])?$_POST['drN']:0)); $p=(isset($_POST['drNn'])?(int)$_POST['drNn']:0); if($aDr[0]&&$p) $aDr[0]=$p;
$aDr[1]=sprintf('%0d',(isset($_POST['drA'])?$_POST['drA']:0));
$aDr[2]=sprintf('%0d',(isset($_POST['drU'])?$_POST['drU']:0));
$aDr[3]=sprintf('%0d',(isset($_POST['drF'])?$_POST['drF']:0));
$aDr[4]=sprintf('%0d',(isset($_POST['drG'])?$_POST['drG']:0));
$aDr['G']=(isset($_POST['d_G'])?$_POST['d_G']:''); $aDr['B']=(isset($_POST['d_B'])?(int)$_POST['d_B']:0);
$aDr[5]=sprintf('%0d',(isset($_POST['drB'])?$_POST['drB']:0)); $aDr[6]=sprintf('%0d',(isset($_POST['dr2'])?$_POST['dr2']:0));
$aDr[7]=sprintf('%0d',(isset($_POST['drL'])?$_POST['drL']:0));
$aDr[10]=sprintf('%0d',(isset($_POST['drS'])?$_POST['drS']:0)); $aDr[11]=sprintf('%0d',(isset($_POST['drR'])?$_POST['drR']:0));
$aDr[12]=(isset($_POST['drH'])?$_POST['drH']:'');
if($aDr[10]) $sHtml=@implode('',@file('druckListe.htm')); else $sHtml='';
if($sHtml) if($p=strpos($sHtml,'{Inhalt}')){echo substr($sHtml,0,$p); $sHtml=substr($sHtml,$p+8)."\n";}else $sHtml='';
if(!$sHtml) echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Umfrage-Script - Drucken</title>
<link rel="stylesheet" type="text/css" href="admin.css">
</head>

<body class="admDruck">
<h1 style="font-size:130%"><img src="_frage.gif" width="16" height="24" border="0" align="bottom" alt="">Umfrage-Script: Fragenliste '.UMF_Konfiguration.'</h1>
';

$aQ=array(); $sQ=''; $nAntwAnzahl=max(20,ADU_AntwortZahl); //Suchparameter
if($FNr1=(isset($_POST['fnr1'])?$_POST['fnr1']:'')){$a1Filt[0]=$FNr1; $sQ.='&amp;fnr1='.$FNr1; $aQ['fnr1']=$FNr1;}
if($FNr2=(isset($_POST['fnr2'])?$_POST['fnr2']:'')){$a2Filt[0]=$FNr2; $sQ.='&amp;fnr2='.$FNr2; $aQ['fnr2']=$FNr2;}
$Onl=(isset($_POST['onl'])?$_POST['onl']:'').(isset($_POST['onl1'])?$_POST['onl1']:'').(isset($_POST['onl2'])?$_POST['onl2']:'');
if(strlen($Onl)!=1) $Onl=''; else {$a1Filt[1]=$Onl; $sQ.='&amp;onl='.$Onl; $aQ['onl1']=$Onl;}
$s=(isset($_POST['umf1'])?$_POST['umf1']:''); if(strlen($s)){$a1Filt[2]=$s; $sQ.='&amp;umf1='.rawurlencode($s); $aQ['umf1']=$s;}
$s=(isset($_POST['umf2'])?$_POST['umf2']:''); if(strlen($s)){$a2Filt[2]=$s; $sQ.='&amp;umf2='.rawurlencode($s); $aQ['umf2']=$s;}
$s=(isset($_POST['umf3'])?$_POST['umf3']:''); if(strlen($s)){$a3Filt[2]=$s; $sQ.='&amp;umf3='.rawurlencode($s); $aQ['umf3']=$s;}
$s=(isset($_POST['frg1'])?$_POST['frg1']:''); if(strlen($s)){$a1Filt[3]=$s; $sQ.='&amp;frg1='.rawurlencode($s); $aQ['frg1']=$s;}
$s=(isset($_POST['frg2'])?$_POST['frg2']:''); if(strlen($s)){$a2Filt[3]=$s; $sQ.='&amp;frg2='.rawurlencode($s); $aQ['frg2']=$s;}
$s=(isset($_POST['frg3'])?$_POST['frg3']:''); if(strlen($s)){$a3Filt[3]=$s; $sQ.='&amp;frg3='.rawurlencode($s); $aQ['frg3']=$s;}
$s=(isset($_POST['bem1'])?$_POST['bem1']:''); if(strlen($s)){$a1Filt[5]=$s;$sQ.='&amp;bem1='.rawurlencode($s); $aQ['bem1']=$s;}
$s=(isset($_POST['bem2'])?$_POST['bem2']:''); if(strlen($s)){$a2Filt[5]=$s;$sQ.='&amp;bem2='.rawurlencode($s); $aQ['bem2']=$s;}
$s=(isset($_POST['bem3'])?$_POST['bem3']:''); if(strlen($s)){$a3Filt[5]=$s;$sQ.='&amp;bem3='.rawurlencode($s); $aQ['bem3']=$s;}
$s=(isset($_POST['b2m1'])?$_POST['b2m1']:''); if(strlen($s)){$a1Filt[6]=$s;$sQ.='&amp;b2m1='.rawurlencode($s); $aQ['b2m1']=$s;}
$s=(isset($_POST['b2m2'])?$_POST['b2m2']:''); if(strlen($s)){$a2Filt[6]=$s;$sQ.='&amp;b2m2='.rawurlencode($s); $aQ['b2m2']=$s;}
$s=(isset($_POST['b2m3'])?$_POST['b2m3']:''); if(strlen($s)){$a3Filt[6]=$s;$sQ.='&amp;b2m3='.rawurlencode($s); $aQ['b2m3']=$s;}

$aD=array(); $aTmp=array(); $aIdx=array(); //Daten holen
if(!UMF_SQL){ //Textdaten
 $aD=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true;
  if(isset($a1Filt)&&is_array($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
  foreach($a1Filt as $j=>$v) if($b&&$j>2){
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
  if($b){ //Datensatz gueltig
   $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; //Nr,akt,Umf
   $aTmp[$sNr][3]=str_replace('\n ',NL,str_replace('`,',';',$a[3])); $aTmp[$sNr][4]=$a[4]; //Fra,Bld,Anm,Anm2
   $aTmp[$sNr][5]=str_replace('\n ',NL,str_replace('`,',';',$a[5])); $aTmp[$sNr][6]=str_replace('\n ',NL,str_replace('`,',';',$a[6]));
   if($aDr[7]=='1'){$s=''; for($k=1;$k<=$nAntwAnzahl;$k++) if($t=(isset($a[6+$k])?$a[6+$k]:'')) $s.='<li>'.str_replace('\n ',NL,str_replace('`,',';',$t)).'</li>'; $aTmp[$sNr][7]=$s;}
   $aIdx[$sNr]=sprintf('%0'.UMF_NummerStellen.'d',$i);
  }
 }$aD=array();
}elseif($DbO){ //SQL-Daten
 $s='';
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sF=($j==3?'Frage':($j==2?'Umfrage':($j==5?'Anmerkung1':($j==6?'Anmerkung2':''))));
   $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if($w=(isset($a2Filt[0])?$a2Filt[0]:0)) $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Nummer="'.$v.'"';
  }else $s.=' AND '.($j==0?'Nummer':($j==1?'aktiv':'')).'="'.$v.'"';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j==3?'Frage':($j==2?'Umfrage':($j==5?'Anmerkung1':($j==6?'Anmerkung2':'')))).' LIKE "%'.$v.'%")';
 }
 $sF=''; for($i=1;$i<=$nAntwAnzahl;$i++) $sF.=',Antwort'.$i;
 if($rR=$DbO->query('SELECT Nummer,aktiv,Umfrage,Frage,Bild,Anmerkung1,Anmerkung2'.($aDr[7]=='1'?$sF:'').' FROM '.UMF_SqlTabF.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  $i=0;
  while($a=$rR->fetch_row()){
   $sNr=(int)$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; //Nr,akt,Umf
   $aTmp[$sNr][3]=str_replace("\r",'',$a[3]); $aTmp[$sNr][4]=$a[4]; //Fra,Bld,Anm,Anm2
   $aTmp[$sNr][5]=str_replace("\r",'',$a[5]); $aTmp[$sNr][6]=str_replace("\r",'',$a[6]);
   if($aDr[7]=='1'){$s=''; for($k=1;$k<=$nAntwAnzahl;$k++) if($t=(isset($a[6+$k])?$a[6+$k]:'')) $s.='<li>'.str_replace('\n ',NL,str_replace('`,',';',$t)).'</li>'; $aTmp[$sNr][7]=$s;}
   $aIdx[$sNr]=sprintf('%0'.UMF_NummerStellen.'d',++$i);
  }$rR->close();
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">keine MySQL-Verbindung!</p>';

if($aDr[11]) arsort($aIdx);
reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i]; $nNr=0;
if(!$sMeld) if($aDr[12]) $sMeld='<p class="admMeld">'.$aDr[12].'</p>';
if($aDr[10]&&!$sHtml) $sMeld='<p class="admFehl">Druckschablone <i>druckListe.htm</i> fehlt oder fehlerhaft!</p>';

echo '
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td>'.$sMeld.NL.'</td>
  <td style="width:64px;background-image:url(drucken.gif);background-repeat:no-repeat;"><a href="javascript:window.print()"><img src="'.UMFPFAD.'pix.gif" width="64" height="16" border="0" alt="drucken"></a></td>
 </tr>
</table>

<table class="admDru" border="0" cellpadding="0" cellspacing="0">
 <tr class="admTabl">'.
  ($aDr[0]>='1'?"\n  ".'<td class="admDru" align="center" width="1%"><b>Nr</b></td>':'').
  ($aDr[1]=='1'?"\n  ".'<td class="admDru" width="15" align="center" title="aktiviert"><b>A</b></td>':'').
  ($aDr[2]=='1'?"\n  ".'<td class="admDru"><b>Umfr.</b></td>':'').
  ($aDr[3]=='1'||$aDr[5]=='1'||$aDr[6]=='1'?"\n  ".'<td class="admDru"><b>'.rtrim(($aDr[3]=='1'?'Frage ':'').($aDr[5]=='1'?'Anmerkung-1 ':'').($aDr[6]=='1'?'Anmerkung-2 ':'')).'</b></td>':'').
  ($aDr[7]=='1'?"\n  ".'<td class="admDru"><b>Antworten</b></td>':'').
  ($aDr[4]>='1'?"\n  ".'<td class="admDru" align="center"'.($aDr['G']=='b'&&$aDr['B']?' style="width:'.$aDr['B'].'px"':'').'><b>Bild</b></td>':'').'
 </tr>
';
foreach($aD as $a){ //Datenzeilen ausgeben
 $sNr=$a[0]; $sR=','.$a[5];
 echo ' <tr class="admTabl">'.NL;
 if($aDr[0]>='1') echo '  <td class="admDru" align="center" width="1%">'.sprintf('%'.UMF_NummerStellen.'d',($aDr[0]<'2'?$sNr:++$nNr)).'</td>'.NL;
 if($aDr[1]=='1') echo '  <td class="admDru" align="center">'.($a[1]?'a':'&nbsp;').'</td>'.NL;
 if($aDr[2]=='1') echo '  <td class="admDru" align="center">'.($a[2]?$a[2]:'&nbsp;').'</td>'.NL;
 if($aDr[3]=='1'||$aDr[5]=='1'||$aDr[6]=='1'){
  echo '  <td class="admDru">'."\n";
  if($aDr[3]=='1') echo '   <div>'.fUmfBB($a[3])."</div>\n";
  if($aDr[5]=='1'&&($a[5]||$a[6])) echo '   <div>-----<br />'.fUmfBB($a[5])."</div>\n";
  if($aDr[6]=='1'&&$a[6]) echo '   <div>-----<br />'.fUmfBB($a[6])."</div>\n";
  echo '  </td>'.NL;
 }
 if($aDr[7]=='1') echo '  <td class="admDru" align="left"><ul style="margin:0;padding:1.1em;">'.($a[7]?$a[7]:'&nbsp;').'</ul></td>'.NL;
 if($aDr[4]>='1') echo '  <td class="admDru" style="width:auto">'.($a[4]?($aDr['G']!='b'?$a[4]:'<img src="http://'.UMF_Www.UMF_Bilder.$a[4].'"'.($aDr['B']?' style="width:98%;min-width:'.$aDr['B'].'px;max-width:'.$aDr['B'].'px"':'').' border="0" alt="Bild">'):'&nbsp;').'</td>'.NL;
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
function fUmfBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
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
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="fraText" target="_blank" href="http://',$p,(substr($v,$o,7)!='http://'?5:12));
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5)); $a=NULL; $u='';
   if(strpos($w,'://')){ //URL
    if(!$a=@getimagesize($w)) if($e=strpos($w,UMF_Www)) $a=@getimagesize(UMF_Pfad.substr($w,$e+strlen(UMF_Www)));
   }else{ //nur Pfad
    if(substr($w,0,1)=='/'){ //absoluter Pfad
     $u=$_SERVER['DOCUMENT_ROOT']; if(!strpos($w,substr($u,strpos($u,'/')+1)).'/') $u.=$w; $a=@getimagesize($u); $u='';
    }else{$w=FRAPFAD.$w; $a=@getimagesize($w); $u=FRAPFAD;} //relativer Pfad
   }
   $w='<img class="fraText" '.(is_array($a)?$a[3].' ':'').'src="'.$u; $v=substr_replace($v,$w,$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
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