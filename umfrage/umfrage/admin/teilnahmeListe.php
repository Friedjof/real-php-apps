<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Teilnahmeliste','','ETl');

$DDl='';  //Listenaktionen

if($_SERVER['REQUEST_METHOD']=='POST'){
 foreach($_POST as $k=>$xx) if(substr($k,0,3)=='del'&&strpos($k,'x')>0) $nDel=(int)substr($k,3); reset($_POST);
 if($nDel>0){ //löschen
  if($nDel==$_POST['ddl']){
   if(!UMF_SQL){ //Textdaten
    $aD=@file(UMF_Pfad.UMF_Daten.UMF_Teilnahme); $aD[$nDel]='';
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Teilnahme,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld=fMErfo('Der Eintrag wurde gelöscht.');
    }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Teilnahme,UMF_TxDateiRechte));
   }elseif($DbO){ //SQL-Daten
    if($DbO->query('DELETE FROM '.UMF_SqlTabT.' WHERE Nummer="'.$nDel.'" LIMIT 1')){
     $sMeld=fMErfo('Der Eintrag wurde gelöscht.');
    }else $sMeld=fMFehl(UMF_TxSqlAendr);
   }
  }else{$DDl=$nDel; $sMeld=fMFehl('Den Eintrag Nummer '.$nDel.' wirklich löschen?');}
 }elseif($nDel<0){ //leeren
  if($nDel==$_POST['ddl']){
   if(!UMF_SQL){ //Textdaten
    $aD=array('Datum;Status;Art;Nutzer;Ergebnis');
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Teilnahme,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld=fMErfo('Die Teilnahmeliste wurde geleert.');
    }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Teilnahme,UMF_TxDateiRechte));
   }elseif($DbO){ //SQL-Daten
    if($DbO->query('DELETE FROM '.UMF_SqlTabT)){
     $sMeld=fMErfo('Die Teilnahmeliste wurde geleert.');
    }else $sMeld=fMFehl(UMF_TxSqlAendr);
   }
  }else{$DDl=-1; $sMeld=fMFehl('Die gesamte Teilnahmeliste wirklich leeren?');}
 }else $sMeld=fMMeld('Die Einträge bleiben unverändert');
}

$aQ=array(); $sQ=''; $a1Filt=array(); $a2Filt=array(); $a3Filt=array(); //Suchparameter
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
 if(is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
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

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADU_TeilnahmeLaenge;
if(ADU_TeilnahmeRueckw) arsort($aD); reset($aD); $k=0; $aT=array();
foreach($aD as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aT[]=$aD[$i];
if(!$sMeld) $sMeld=fMMeld(!$sQ?'GesamtTeilnahmeliste':'Abfrageergebnis');
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld?></td>
  <td align="right">[ <a href="teilnahmeExport.php<?php if($sQ) echo '?'.$sQ?>">Export</a>] &nbsp; [ <a href="teilnahmeSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ]</td>
 </tr>
</table>

<?php
$aArt=array('G'=>'Gast','T'=>'Teilnehmer','N'=>'Benutzer');
$aSta=array(0=>'ignoriert',1=>'eingetragen');

$sNavigator=fUmfNavigator($nStart,count($aD),ADU_TeilnahmeLaenge,$sQ); echo $sNavigator;
if($nStart>1) $sQ.=($sQ?'&amp;':'').'start='.$nStart; $aQ['start']=$nStart; $sAmp=($sQ?'&amp;':'');
$bLoeschen=file_exists('teilnahmeLoeschen.php');

?>

<form name="umfListe" action="teilnahmeListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td>Nr</td><td>Datum</td><td>Status</td><td>Art</td><td>Nutzer</td><td>Ergebnis</td><?php if($bLoeschen) echo '<td>&nbsp;</td>'; ?>
 </tr>
<?php
foreach($aT as $a){
 $nNr=$a[0]; $sD=$a[1]; $sD=substr($sD,8,2).'.'.substr($sD,5,2).'.'.substr($sD,0,4).substr($sD,10);
 echo "\n".'<tr class="admTabl">';
 echo "\n".' <td style="text-align:center">'.$nNr.'</td>';
 echo "\n".' <td style="">'.$sD.'</td>';
 echo "\n".' <td>'.$aSta[$a[2]].'</td>';
 echo "\n".' <td>'.$aArt[$a[3]].'</td>';
 echo "\n".' <td>'.$a[4].'</td>';
 echo "\n".' <td>'.$a[5].'</td>';
 if($bLoeschen) echo "\n".' <td><input type="image" src="iconLoeschen.gif" name="del'.$nNr.'" width="12" height="13" border="0" title="Eintrag '.$nNr.' löschen" /></td>';
 echo "\n".'</tr>';
}
if($bLoeschen){
 echo ' <tr class="admTabl">'.NL;
 echo '  <td align="center"><input type="image" name="del-1" src="iconLoeschen.gif" width="12" height="13" border="0" title="ganze Teilnahmeliste rücksetzen"></td>'.NL;
 echo '  <td colspan="3"><i>gesamte Teilnahmeliste leeren</i></td>'.NL.'  <td>&nbsp;</td>'.NL.'  <td>&nbsp;</td>'.NL.'  <td>&nbsp;</td>'.NL;
 echo ' </tr>'.NL;
}
?>

</table>
<input type="hidden" name="ddl" value="<?php echo $DDl?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>
</form>
<?php echo $sNavigator?>
<p style="text-align:center;">[ <a href="teilnahmeListe.php<?php if($sQ) echo '?'.$sQ?>">aktualisieren</a> ]</p>

<?php
echo fSeitenFuss();

function fUmfNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="teilnahmeListe.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="teilnahmeListe.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="teilnahmeListe.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:3px;margin-bottom:3px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}
function fNormDat($s){
 if(strpos($s,'.')){
  $a=explode('.',str_replace(':','.',str_replace(',','.',str_replace(' ','',trim($s)))));
  $s=sprintf('%d-%02d-%02d',(isset($a[2])?((int)$a[2]<2000?2000+$a[2]:$a[2]):2000),(isset($a[1])?$a[1]:0),$a[0]);
 }
 return $s;
}
?>