<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Fragenliste','
<script language="JavaScript" type="text/javascript">
function SelAll(bStat){
 for(var i=0;i<self.document.flist.length;++i)
  if(self.document.flist.elements[i].type=="checkbox"&&self.document.flist.elements[i].name.substring(0,3)=="akt") self.document.flist.elements[i].checked=bStat;
}
function UmfAll(nIdx){
 for(var i=0;i<self.document.flist.length;++i)
  if(self.document.flist.elements[i].type.substring(0,6)=="select"&&self.document.flist.elements[i].name.substring(0,3)=="umf") self.document.flist.elements[i].selectedIndex=nIdx;
}
</script>
<link rel="stylesheet" type="text/css" href="http://'.UMF_Www.'umfStyle.css">','UFl');

$DDl=''; $bCh=$bUmf=false; $nDel=''; $nAntwAnzahl=max(20,ADU_AntwortZahl); //Listenaktionen
if($_SERVER['REQUEST_METHOD']=='POST'){
 foreach($_POST as $k=>$xx) if(substr($k,0,3)=='del'&&strpos($k,'x')>0) $nDel=(int)substr($k,3); reset($_POST);
 if($nDel>0){ //loeschen
  if($nDel==(isset($_POST['ddl'])?$_POST['ddl']:'')){
   if(!UMF_SQL){ //Textdaten
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD); $sDel=$nDel.';'; $l=strlen($sDel);
    for($i=1;$i<$nCnt;$i++) if(substr($aD[$i],0,$l)==$sDel){
     $aR=explode(';',$aD[$i],9); $aD[$i]=''; $sBld=$aR[4]; $s=rtrim(str_replace("\r",'',implode('',$aD)));
     if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Fragen,'w')){ //speichern
      fwrite($f,$s.NL); fclose($f); $sMeld=fMErfo('Die bisherige Frage '.$nDel.' wurde gelöscht!');
      if($sBld) if(strpos($s,';'.$sBld.';')==false) @unlink(UMF_Pfad.UMF_Bilder.$sBld);
     }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Fragen,UNF_TxDateiRechte));
     break;
    }
   }elseif($DbO){ //beiSQL
    if($rR=$DbO->query('SELECT Nummer,Bild FROM '.UMF_SqlTabF.' WHERE Nummer='.$nDel)){
     $aR=$rR->fetch_row(); $rR->close(); $sBld=$aR[1];
     if($DbO->query('DELETE FROM '.UMF_SqlTabF.' WHERE Nummer='.$nDel)&&$DbO->affected_rows>0){
      $sMeld=fMErfo('Die bisherige Frage '.$nDel.' wurde gelöscht!');
      if($sBld) if($rR=$DbO->query('SELECT Nummer,Bild FROM '.UMF_SqlTabF.' WHERE Bild="'.$sBld.'"')){
       if($rR->num_rows==0) @unlink(UMF_Pfad.UMF_Bilder.$sBld); $rR->close();
      }
     }else $sMeld=fMFehl(UMF_TxSqlAendr);
    }else $sMeld=fMFehl(UMF_TxSqlFrage);
   }
  }else{$DDl=$nDel; $sMeld=fMFehl('Die Frage Nummer '.$nDel.' wirklich löschen?');}
 }else{ //aktiv/Umf
  if(!UMF_SQL){ //Textdaten
   $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD);
   $s=''; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=';Antwort'.$i; $aD[0]='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'.$s.NL;
   for($i=1;$i<$nCnt;$i++){
    $sLn=rtrim($aD[$i]); $p=strpos($sLn,';'); $k=(int)substr($sLn,0,$p++);
    if(isset($_POST['num'.$k])&&$_POST['num'.$k]){
     $v=sprintf('%0d',(isset($_POST['akt'.$k])?$_POST['akt'.$k]:0));
     if(substr($sLn,$p,1)!=$v){$sLn=$k.';'.$v.substr($sLn,$p+1); $aD[$i]=$sLn.NL; $bCh=true;}
     $v=(isset($_POST['umf'.$k])?$_POST['umf'.$k]:''); $p+=2;
     if(strlen($v)==0&&substr($sLn,$p,1)!=';'||strlen($v)==1&&substr($sLn,$p,1)!=$v){
      $aD[$i]=substr($sLn,0,$p).$v.substr($sLn,$p+(substr($sLn,$p,1)==';'?0:1)).NL; $bUmf=true;
     }
    }
   }
   if($bCh||$bUmf){
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Fragen,'w')){ //speichern
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     if($bUmf) $sMeld=fMErfo('Die Frage(n) wurden neu zugeordnet!');
     else $sMeld=fMErfo('Die Frage(n) wurden aktiviert/deaktiviert!');
    }else $sMeld=fMFehl(str_replace('#',UMF_Daten.UMF_Fragen,UMF_TxDateiRechte));
   }else $sMeld=fMMeld('Die Fragen bleiben unverändert.');
  }elseif($DbO){ //beiSQL
   if($rR=$DbO->query('SELECT Nummer,aktiv,Umfrage FROM '.UMF_SqlTabF.' ORDER BY Nummer')){
    $aA=array(); $aU=array(); while($aR=$rR->fetch_row()){$aA[$aR[0]]=$aR[1]; $aU[$aR[0]]=$aR[2];} $rR->close(); $bCh=false; $bUmf=false;
    foreach($aA as $k=>$s) if(isset($_POST['num'.$k])&&$_POST['num'.$k]){
     $v=sprintf('%0d',(isset($_POST['akt'.$k])?$_POST['akt'.$k]:0));
     if($v!=$s) if($DbO->query('UPDATE IGNORE '.UMF_SqlTabF.' SET aktiv="'.substr($v,0,1).'" WHERE Nummer='.$k)) $bCh=true;
     $v=(isset($_POST['umf'.$k])?$_POST['umf'.$k]:'');
     if($v!=$aU[$k]) if($DbO->query('UPDATE IGNORE '.UMF_SqlTabF.' SET Umfrage="'.substr($v,0,1).'" WHERE Nummer='.$k)) $bUmf=true;
    }
    if($bUmf) $sMeld=fMErfo('Die Frage(n) wurden neu zugeordnet!');
    elseif($bCh) $sMeld=fMErfo('Die Frage wurden aktiviert/deaktiviert!');
    else $sMeld=fMMeld('Die Fragen bleiben unverändert.');
   }else $sMeld=fMFehl(UMF_TxSqlFrage);
  }
 }
}//POST

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

$aD=array(); $aTmp=array(); $aIdx=array(); //Daten holen
if(!UMF_SQL){ //Textdaten
 $aD=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true;
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
   $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; $aTmp[$sNr][3]=str_replace('`,',';',$a[3]); //Nr,akt,Aumf,Fra
   $aIdx[$sNr]=sprintf('%02d',$i);
  }
 }$aD=array();
}elseif($DbO){ //SQL-Daten
 $s='';
 if(count($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sF=($j==3?'Frage':($j==2?'Umfrage':($j!=6?'Anmerkung1':'Anmerkung2')));
   $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)) $s.=' AND Nummer="'.$v.'"'; else $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"';
  }else $s.=' AND aktiv="'.$v.'"';
 }
 if(count($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j==3?'Frage':($j==2?'Umfrage':($j!=6?'Anmerkung1':'Anmerkung2'))).' LIKE "%'.$v.'%")';
 }
 if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  $i=0;
  while($a=$rR->fetch_row()){
   $sNr=(int)$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; $aTmp[$sNr][2]=$a[2]; $aTmp[$sNr][3]=str_replace("\r",'',$a[3]); //Nr,akt,Umf,Fra
   $aIdx[$sNr]=sprintf('%02d',++$i);
  }$rR->close();
 }else $sMeld=fMFehl(UMF_TxSqlFrage);
}

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADU_ListenLaenge;
if(ADU_Rueckwaerts) arsort($aIdx); reset($aIdx); $k=0;
foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];
if(!$sMeld) $sMeld=fMMeld(!$sQ?'Gesamt-Fragenliste':'Abfrageergebnis');
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld?></td>
  <td align="right">[ <a href="fragenSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ]</td>
 </tr>
</table>
<?php
 $sNavigator=fUmfNavigator($nStart,count($aIdx),ADU_ListenLaenge,$sQ); echo $sNavigator;
 if($nStart>1) $sQ.=($sQ?'&amp;':'').'start='.$nStart; $aQ['start']=$nStart; $sAmp=($sQ?'&amp;':'');
 $bAendern=file_exists('fragenAendern.php'); $bKopieren=file_exists('fragenKopieren.php'); $bLoeschen=file_exists('fragenLoeschen.php');
?>

<form name="flist" action="fragenListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl">
  <td align="center" width="1%"><b>Nr</b></td>
  <?php if($bAendern){?><td width="12">&nbsp;</td><?php }?>
  <?php if($bKopieren){?><td width="12">&nbsp;</td><?php }?>
  <td width="13">&nbsp;</td>
  <td width="15" align="center" title="aktiv"><b>A</b></td>
  <td width="15" align="center" title="Umfrage"><b>Umfr.</b></td>
  <td><b>Frage</b></td>
  <?php if($bLoeschen){?><td width="12">&nbsp;</td><?php }?>
 </tr>
<?php
$sOptU=''; for($i=1;$i<=26;$i++) $sOptU.='<option value="'.chr($i+64).'">'.chr($i+64).'</option>';
foreach($aD as $a){ //Datenzeilen ausgeben
 $sNr=$a[0];
 echo ' <tr class="admTabl">'.NL;
 echo '  <td style="vertical-align:top;text-align:center" width="1%">'.sprintf('%2d',$sNr).'</td>'.NL;
 if($bAendern) echo '  <td style="vertical-align:top;text-align:center"><a href="fragenAendern.php?'.$sQ.$sAmp.'nr='.$sNr.'"><img src="iconAendern.gif" width="12" height="13" border="0" title="bearbeiten"></a></td>'.NL;
 if($bKopieren)echo '  <td style="vertical-align:top;text-align:center"><a href="fragenKopieren.php?'.$sQ.$sAmp.'nr='.$sNr.'"><img src="iconKopie.gif" width="12" height="13" border="0" title="kopieren"></a></td>'.NL;
 echo '  <td style="vertical-align:top;text-align:center"><a href="fragenVorschau.php?'.$sQ.$sAmp.'nr='.$sNr.'"><img src="iconVorschau.gif" width="13" height="13" border="0" title="Vorschau"></a></td>'.NL;
 echo '  <td style="vertical-align:top;text-align:center"><input class="admCheck" type="checkbox" name="akt'.$sNr.'" value="1"'.($a[1]?' checked="checked"':'').' title="'.($a[1]?'':'de').'aktiviert"></td>'.NL;
 echo '  <td style="vertical-align:top"><select name="umf'.$sNr.'" style="width:auto" size="1"><option value=""></option>'.($a[2]?str_replace('value="'.$a[2].'"','value="'.$a[2].'" selected="selected"',$sOptU):$sOptU).'</select></td>'.NL;
 echo '  <td style="vertical-align:top">'.($a[3]?fUmfBB($a[3]):'').'</td>'.NL;
 if($bLoeschen) echo '  <td style="vertical-align:top;text-align:center"><input type="image" name="del'.$sNr.'" src="iconLoeschen.gif" width="12" height="13" border="0" title="Frage '.$sNr.' löschen"><input type="hidden" name="num'.$sNr.'" value="*"/></td>'.NL;
 echo ' </tr>'.NL;
}
?>
 <tr class="admTabl">
  <td colspan="<?php echo($bAendern&&$bKopieren?'4':($bAendern||$bKopieren?'3':'2'))?>" align="right">alle</td>
  <td align="center"><input class="admCheck" type="checkbox" name="allO" onclick="SelAll(this.checked)" title="alle aktivieren/deaktivieren"></td>
  <td align="center"><select name="allU" style="width:auto" size="1" onchange="UmfAll(this.selectedIndex)" title="zu einer Umfrage zuordnen"><option value=""></option><?php echo $sOptU ?></select></td>
  <td align="left" colspan="<?php echo($bLoeschen?2:1)?>"><input type="submit" class="admSubmit" value="speichern"></td>
 </tr>
</table>
<input type="hidden" name="ddl" value="<?php echo $DDl?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>
</form>
<?php echo $sNavigator?>
<p style="text-align:center;">[ <a href="fragenListe.php<?php if($sQ) echo '?'.$sQ?>">aktualisieren</a> ]</p>

<?php
echo fSeitenFuss();

function fUmfNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="fragenListe.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:18px;text-align:center;"><a href="fragenListe.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="Seite-'.$i.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="fragenListe.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
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
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="fraText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="fraText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[sup]') $v=substr_replace($v,'<sup>',$p,5); elseif(substr($Tg,0,6)=='[/sup]') $v=substr_replace($v,'</sup>',$p,6);
  elseif(substr($Tg,0,5)=='[sub]') $v=substr_replace($v,'<sub>',$p,5); elseif(substr($Tg,0,6)=='[/sub]') $v=substr_replace($v,'</sub>',$p,6);
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="fraText" target="_blank" href="http://',$p,(substr($v,$o,7)!='http://'?5:12));
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
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