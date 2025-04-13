<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserateliste','<script type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.InserateListe.length;++i)
   if(self.document.InserateListe.elements[i].type=="checkbox") self.document.InserateListe.elements[i].checked=bStat;
 }
 function fSubmitNavigFrm(){
  document.NavigFrm.submit();
 }
</script>
','IIl');

$aStru=array();
if($nSegNo!=0){ //Segment gewählt

 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aLF=array(); $aAW=array(); $aKW=array(); $aSW=array();
 $bLschNun=false; $nListenIndex=1;
 if(MP_Pfad>''){
  if(!MP_SQL){//Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
    if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
 }else $Meld='Bitte zuerst die Pfade im Setup einstellen!';
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

 if($bLsch=(isset($_POST['LschBtn_x'])||isset($_POST['LschBtn_y']))){ //Inserate löschen
  $aId=array(); foreach($_POST as $k=>$xx) if(substr($k,3,2)=='CB') $aId[(int)substr($k,5)]=true; //Löschnummern
  if(count($aId)>0){
   if($bLsch&&file_exists('loeschen.php')){
    if($_POST['LschNun']!='1'){$bLschNun=true; $Meld=MP_TxLoescheFrag;} //nachfragen
    else{ //jetzt löschen
     if(!MP_SQL){ //Textdatei
      $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
      for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); if(isset($aId[$n])) $aD[$i]='';} //löschen
      if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){
       fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);
       $bOK=true; $Meld=MP_TxLoescheErfo; $MTyp='Meld';
      }else $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
     }elseif($DbO){ //bei SQL
      $s=''; foreach($aId as $k=>$xx) $s.=' OR nr='.$k;
      if($DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE '.substr($s,4))){
       $bOK=true; $Meld=MP_TxLoescheErfo; $MTyp='Meld';
      }else $Meld=MP_TxSqlFrage;
     }//SQL
     if((in_array('b',$aFT)||in_array('f',$aFT))&&$bOK){ //Bilder und Dateien
      if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){$aD=array();
       while($s=readdir($f)) if($i=(int)$s){
        if(MP_BldTrennen){if(isset($aId[$i])) $aD[]=$s;}
        elseif(substr($i,-2)==$sSegNo) if(isset($aId[(int)substr($i,0,-2)])) $aD[]=$s;
       }
       closedir($f); foreach($aD as $s) @unlink(MP_Pfad.MP_Bilder.$sBldDir.$s);
      }
     }//Bilder
    }//jetzt löschen
   }
  }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
 }//LschForm
 if(isset($_GET['mp_Sta'])&&isset($_GET['mp_Num'])){ //online/offline
  $nId=(int)$_GET['mp_Num']; $sSta=$_GET['mp_Sta'];
  if(!MP_SQL){ //Textdatei
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){
    $s=substr($aD[$i],0,12); $p=strpos($s,';');
    if((int)substr($s,0,$p)==$nId){$aD[$i]=substr_replace($aD[$i],$sSta,$p+1,1);break;}
   }
   if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){
    fwrite($f,rtrim(implode('',$aD)).NL); fclose($f); $bOK=true; $Meld='Das Inserat wurde o'.($sSta=='1'?'n':'ff').'line geschaltet.'; $MTyp='Meld';
   }else $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',KAL_TxDateiRechte); $MTyp='Meld';
  }elseif($DbO){ //bei SQL
   if($DbO->query('UPDATE IGNORE '.str_replace('%',$sSegNo,MP_SqlTabI).' SET online="'.$sSta.'" WHERE nr='.$nId)){
    $Meld='Das Inserat wurde o'.($sSta=='1'?'n':'ff').'line geschaltet.'; $MTyp='Meld';
   }else $Meld=MP_TxSqlFrage;
  }
 }//online/offline

 $aD=array(); $aSpalten=array(); $nSpalten=0; $aQ=array(); $sQ=''; $a1Filt=array(); $a2Filt=array(); $a3Filt=array();
 $bOhneGrenze=false;
 for($i=0;$i<$nFelder;$i++){ //Abfrageparameter aufbereiten
  $t=$aFT[$i]; $aSpalten[$aLF[$i]]=$i;
  $s=(isset($_POST['mp_'.$i.'F1'])?$_POST['mp_'.$i.'F1']:(isset($_GET['mp_'.$i.'F1'])?$_GET['mp_'.$i.'F1']:''));
  if(strlen($s)){
   $sQ.='&mp_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s; if($i<=1) $bOhneGrenze=true;
   if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else $a1Filt[$i]=fMpNormDatum($s);
  }
  $s=(isset($_POST['mp_'.$i.'F2'])?$_POST['mp_'.$i.'F2']:(isset($_GET['mp_'.$i.'F2'])?$_GET['mp_'.$i.'F2']:''));
  if(strlen($s)){
   $sQ.='&mp_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else{$a2Filt[$i]=fMpNormDatum($s); if($i<=1) $bOhneGrenze=true;}
   if(!isset($a1Filt[$i])||empty($a1Filt[$i]))
    if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i') $a1Filt[$i]='0';
    elseif($t=='j'||$t=='v') $a1Filt[$i]='';
  }
  $s=(isset($_POST['mp_'.$i.'F3'])?$_POST['mp_'.$i.'F3']:(isset($_GET['mp_'.$i.'F3'])?$_GET['mp_'.$i.'F3']:''));
  if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&mp_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
 }
 $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='99';
 if(isset($_GET['mp_Archiv'])||(isset($_POST['mp_Archiv'])&&$_POST['mp_Archiv'])){
  $bArchiv=true; $sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';
 }else $bArchiv=false;
 if($bOhneGrenze){$sIntervallAnfang='00'; $sIntervallEnde='99'; $bArchiv=false;}

 if($_SERVER['REQUEST_METHOD']!='POST'){//GET
  $bZeigeOnl=(isset($_GET['mp_Onl'])?(bool)$_GET['mp_Onl']:AM_ZeigeOnline);
  $bZeigeOfl=(isset($_GET['mp_Ofl'])?(bool)$_GET['mp_Ofl']:AM_ZeigeOffline);
  $bZeigeVmk=(isset($_GET['mp_Vmk'])?(bool)$_GET['mp_Vmk']:AM_ZeigeVormerk);
 }else{//POST
  $bZeigeOnl=(isset($_POST['mp_Onl'])?(bool)$_POST['mp_Onl']:false);
  $bZeigeOfl=(isset($_POST['mp_Ofl'])?(bool)$_POST['mp_Ofl']:false);
  $bZeigeVmk=(isset($_POST['mp_Vmk'])?(bool)$_POST['mp_Vmk']:false);
 }
 if(!($bZeigeOfl||$bZeigeVmk)) $bZeigeOnl=true;
 if($bZeigeOnl!=AM_ZeigeOnline) $sQ.='&amp;mp_Onl='.($bZeigeOnl?'1':'0');
 if($bZeigeOfl!=AM_ZeigeOffline) $sQ.='&amp;mp_Ofl='.($bZeigeOfl?'1':'0');
 if($bZeigeVmk!=AM_ZeigeVormerk) $sQ.='&amp;mp_Vmk='.($bZeigeVmk?'1':'0');

 $aSpalten[0]=0; if(!in_array(1,$aSpalten)){for($i=count($aSpalten)-1;$i>=1;$i--) $aSpalten[$i+1]=$aSpalten[$i]; $aSpalten[1]=1;}
 $nSpalten=count($aSpalten); $aTmp=array(); $aIdx=array(); $nA1=count($a1Filt); $nA3=count($a3Filt); //Daten bereitstellen
 if(!MP_SQL){ //Textdaten
  $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //über alle Datensaetze
   $a=explode(';',rtrim($aD[$i])); $sId=(int)$a[0]; $sAblaufDat=$a[2]; $sSta=$a[1];
   $b=($sSta=='1'&&$bZeigeOnl||$sSta=='0'&&$bZeigeOfl||$sSta=='2'&&$bZeigeVmk); array_splice($a,1,1);
   $b=$b&&(AM_ZeigeAltes||$sAblaufDat>=$sIntervallAnfang); //kommend oder laufend
   if($b&&$bArchiv) if($sAblaufDat>$sIntervallEnde) $b=false; //Archivfilter
   if($b&&$nA1){
    reset($a1Filt);
    foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
     $t=$aFT[$j]; $w=isset($a2Filt[$j])?$a2Filt[$j]:''; //$v Suchwort1, $w Suchwort2
     if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
      if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
      if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
     }elseif($t=='d'||$t=='@'){ //Datum
      $s=$a[$j]; if(empty($w)){if($s!=$v) $b=false;} elseif($s>$w||$s<$v) $b=false;
     }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
      $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
      $s=floatval(str_replace(',','.',$a[$j]));
      if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
     }elseif($t=='o'){
      if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
      if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $b=false;
     }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $b=false;}}
    }
   }
   if($b&&$nA3){ //Suchfiltern 3
    reset($a3Filt); foreach($a3Filt as $j=>$v)
     if($aFT[$j]!='o'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}}
     else{if(substr($a[$j],0,strlen($v))==$v){$b=false; break;}}
   }
   if($b){ //Datensatz gültig
    $aTmp[$sId]=array($sId);
    if($nListenIndex==1) $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',$i); //nach Datum
    elseif($nListenIndex>1){ //andere Sortierung
     $s=strtoupper(strip_tags($a[$nListenIndex])); $t=$aFT[$nListenIndex];
     for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
      if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
     if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
     elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
     $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.MP_NummerStellen.'d',$i);
    }
    elseif($nListenIndex==0) $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',$sId); //nach Nr
    for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace('\n ',NL,str_replace('`,',';',$a[$aSpalten[$j]]));
    $aTmp[$sId][]=$sSta;
   }
  }$aD=array();
 }elseif($DbO){ //SQL
  if($sIntervallAnfang>'00'&&!AM_ZeigeAltes) $s=' AND mp_1>"'.$sIntervallAnfang.'"';
  elseif($bArchiv) $s=' AND mp_1<"'.$sIntervallEnde.'"'; else $s='';
  if($nA1) foreach($a1Filt as $j=>$v) if($j>0){ //Suchfiltern 1-2
   $s.=' AND(mp_'.$j; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); $t=($aFT[$j]); //$v Suchwort1, $w Suchwort2
   if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
    $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR mp_'.$j.' LIKE "%'.$w.'%"';
   }elseif($t=='d'||$t=='@'){
    if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
   }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
    $v=str_replace(',','.',$v);
    if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
   }elseif($t=='o'){
    $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR mp_'.$j.' LIKE "'.$w.'%"';
   }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
   $s.=')';
  }else{
   $s.=' AND(nr'; $w=(isset($a2Filt[0])?$a2Filt[0]:'');
   if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.='="'.$v.'"';
   $s.=')';
  }
  if($nA3) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
   $t=$aFT[$j];
   if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
    $s.=' AND NOT(mp_'.$j.' LIKE "%'.$v.'%")';
   elseif($t=='o') $s.=' AND NOT(mp_'.$j.' LIKE "'.$v.'%")';
  }
  $t=''; $nListenIdx=0; $i=0;
  for($j=1;$j<$nSpalten;$j++){$t.=',mp_'.$aSpalten[$j]; if($aSpalten[$j]==$nListenIndex) $nListenIdx=$j;}
  if($nListenIdx==0&&$nListenIndex>0){$t.=',mp_'.$nListenIndex; $nListenIdx=$j;}
  $o=''; if($bZeigeOnl) $o.=' OR online="1"'; if($bZeigeOfl) $o.=' OR online="0"'; if($bZeigeVmk) $o.=' OR online="2"';
  $o=substr($o,4); $i=substr_count($o,'OR'); if($i==1) $o='('.$o.')'; elseif($i==2) $o='online>""';
  if($rR=$DbO->query('SELECT nr'.$t.',online FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE '.$o.$s.' ORDER BY mp_1'.($nFelder>1?',mp_2'.($nFelder>2?',mp_3':''):'').',nr')){
   while($a=$rR->fetch_row()){
    $sId=(int)$a[0]; $aTmp[$sId]=array($sId); ++$i;
    if($nListenIdx==1) $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',$i); //nach Datum
    elseif($nListenIdx>1){ //andere Sortierung
     $s=strtoupper(strip_tags($a[$nListenIdx])); $t=$aFT[$nListenIndex];
     for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
      if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
     if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
     elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
     $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.MP_NummerStellen.'d',$i);
    }
    elseif($nListenIndex==0) $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',$sId); //nach Nr
    for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]); $aTmp[$sId][]=$a[$nSpalten];
   }$rR->close();
  }else $Meld=MP_TxSqlFrage;
 }//SQL
 $nSeite=(int)(isset($_GET['mp_Seite'])?$_GET['mp_Seite']:(isset($_POST['mp_Seite'])?$_POST['mp_Seite']:1));
 $nStart=($nSeite-1)*AM_ListenLaenge+1; $nStop=$nStart+(AM_ListenLaenge>0?AM_ListenLaenge:count($aIdx));
 if($nListenIndex!=1){if(!AM_Rueckwaerts) asort($aIdx); else arsort($aIdx);} // nach Feldern
 elseif(AM_Rueckwaerts&&!$bArchiv||AM_ArchivRueckwaerts&&$bArchiv) arsort($aIdx); // nach Datum
 reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];
 if(!$Meld){
  if(!$sQ) $Meld='Gesamt-Inserate'.($bArchiv?'archiv ':'liste ');
  else $Meld=($bArchiv?'Archiv':'Inserate').'abfrageergebnis '; $MTyp='Meld';
  if($bZeigeOnl) $Meld.='<img src="'.MPPFAD.'grafik/punktGrn.gif" width="12" height="12" border="0" alt="online-Inserate" title="online-Inserate">';
  if($bZeigeOfl) $Meld.='<img src="'.MPPFAD.'grafik/punktRot.gif" width="12" height="12" border="0" alt="offline-Inserate" title="offline-Inserate">';
  if($bZeigeVmk) $Meld.='<img src="'.MPPFAD.'grafik/punktRtGn.gif" width="12" height="12" border="0" alt="Inseratevorschläge" title="Inseratevorschläge">';
 }
?>

<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><p class="adm<?php echo $MTyp?>"><?php echo $Meld?></p></td>
  <td align="right">
   <?php if(!AM_ZeigeAltes){?>[ <a href="liste.php?seg=<?php echo $nSegNo?>">Inserateliste</a> ] [ <a href="liste.php?seg=<?php echo $nSegNo?>&mp_Archiv=1">Inseratearchiv</a> ]<?php }?>
   <?php if(file_exists('suche.php')){?>[ <a href="suche.php?seg=<?php echo $nSegNo.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">Inseratesuche</a> ]<?php }?>
  </td>
 </tr>
</table>
<?php $sNavigator=fMpNavigator($nSegNo,$nSeite,count($aIdx),$sQ,$bArchiv); if(AM_ListenLaenge>0) echo $sNavigator;?>

<form name="InserateListe" action="liste.php?seg=<?php echo $nSegNo?>" method="post">
<input type="hidden" name="LschNun" value="<?php echo ($bLschNun?'1':'')?>" />
<input type="hidden" name="mp_Onl" value="<?php echo ($bZeigeOnl?'1':'0')?>" />
<input type="hidden" name="mp_Ofl" value="<?php echo ($bZeigeOfl?'1':'0')?>" />
<input type="hidden" name="mp_Vmk" value="<?php echo ($bZeigeVmk?'1':'0')?>" />
<input type="hidden" name="mp_Archiv" value="<?php echo ($bArchiv?'1':'')?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td width="1%">&nbsp;</td>';
 for($j=1;$j<$nSpalten;$j++){
  if($aSpalten[$j]!=$nListenIndex) $v=''; else $v='&nbsp;*';
  $sStil=''; $t=$aFT[$aSpalten[$j]];
  if(($t=='b'&&AM_BildVorschau)||($t=='f'&&AM_DateiSymbol)||($t=='s'&&AM_SymbSymbol)||(($t=='l'||$t=='e'||$t=='c')&&AM_LinkSymbol)||$t=='z'||$t=='j'||$t=='v') $sStil=' style="text-align:center"';
  elseif($t=='w'||$t=='n'||$t=='r'||$t=='1'||$t=='2'||$t=='3') $sStil=' style="text-align:right"';
  echo NL.' <td'.$sStil.'><b>'.$aFN[$aSpalten[$j]].$v.'</b></td>';
 }echo NL.'</tr>';
 $bAendern=file_exists('aendern.php'); $bKopiere=file_exists('kopieren.php'); $bDetail=file_exists('detail.php');
 if($nSeite>1) $sQ.='&mp_Seite='.$nSeite; $aQ['Seite']=$nSeite;
 foreach($aD as $a){ //Datenzeilen ausgeben
  $sId=$a[0]; $sSta=$a[$nSpalten]; $sAa=''; $sAe='';
  if($sSta!='2'){$sAa='<a href="liste.php?seg='.$nSegNo.'&mp_Num='.$sId.'&amp;mp_Sta='.($sSta=='0'?'1':'0').$sQ.($bArchiv?'&amp;mp_Archiv=1':'').'">'; $sAe='</a>';}
  echo NL.'<tr class="admTabl">';
  echo NL.' <td valign="top" align="right" style="white-space:nowrap;">'.$sId.'&nbsp;<input class="admCheck" type="checkbox" name="mp_CB'.$sId.'" value="1"'.(isset($aId[$sId])&&$aId[$sId]?' checked="checked"':'').' /></td>';
  echo NL.' <td valign="top">'.($bAendern?'<a href="aendern.php?seg='.$nSegNo.'&mp_Num='.$sId.$sQ.'"><img src="iconAendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  echo NL.' <td valign="top">'.($bKopiere?'<a href="kopieren.php?seg='.$nSegNo.'&mp_Num='.$sId.$sQ.'"><img src="iconKopie.gif" width="12" height="13" border="0" title="Kopieren"></a>':'&nbsp;').'</td>';
  echo NL.' <td align="center" valign="top">'.$sAa.'<img src="'.MPPFAD.'grafik/punkt'.($sSta=='1'?'Grn':($sSta=='0'?'Rot':'RtGn')).'.gif" width="12" height="12" border="0" title="'.($sSta=='1'?'online - jetzt deaktivieren':($sSta=='0'?'offline - jetzt aktivieren':'Inseratevorschlag')).'">'.$sAe.'</td>';
  for($j=1;$j<$nSpalten;$j++){
   $k=$aSpalten[$j]; $t=$aFT[$k]; $sStil='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'm': $s=fMpBB($s); break; // Text/Memo
     case 'a': case 'k': case 'o': case 'u': break; // so lassen
     case 'd': case '@': // Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      break;
     case 'z': $sStil.='text-align:center;'; break; // Uhrzeit
     case 'w': // Währung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sStil.='text-align:right;';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
      if($s=='J'||$s=='Y') $s=MP_TxJa; elseif($s=='N') $s=MP_TxNein; $sStil.='text-align:center;';
      break;
     case 'n': case '1': case '2': case '3': case 'r': // Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sStil.='text-align:right;';
      break;
     case 'l':
      $aI=explode('|',$s); $s=$aI[0]; $v=(isset($aI[1])?$aI[1]:$s);
      if(AM_LinkSymbol){$v='<img src="'.MPPFAD.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';}
      $s='<a href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="_blank">'.$v.'</a>';
      break;
     case 'e': case 'c':
      if(!MP_SQL) $s=fMpDeCode($s);
      if(AM_LinkSymbol){
       $v='<img src="'.MPPFAD.'grafik/mail.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';
      }else $v=$s;
      $s='<a href="mailto:'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 's': $w=$s;
      if(AM_SymbSymbol){
       $p=array_search($s,$aSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
       $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
       $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.$w.'" />'; $sStil.='text-align:center;';
      }
      break;
     case 'b':
      if(AM_BildVorschau){
       $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
       $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.substr($s,strpos($s,'/')+1).'" />'; $sStil.='text-align:center;';
      }else $s=fMpKurzName(substr($s,strpos($s,'|')+1));
      break;
     case 'f':
      if(AM_DateiSymbol){
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); //Datei
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil.='text-align:center;';
       $v='<img src="'.MPPFAD.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.MP_TxDatei.'" />';
      }else $v=fMpKurzName($s);
      $s='<a href="'.MPPFAD.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'">'.$v.'</a>';
      break;
     case 'x': break;
     case 'p': $s=str_repeat('*',strlen($s)/2); break;
    }
   }else $s='&nbsp;';
   if($j==1&&$bDetail) $s='<a href="detail.php?seg='.$nSegNo.'&mp_Num='.$sId.$sQ.($bArchiv?'&mp_Archiv=1':'').'">'.$s.'</a>';
   if(($w=$aSS[$k])||$sStil) $sStil=' style="'.$sStil.$w.'"';
   echo NL.' <td valign="top"'.$sStil.'>'.$s.'</td>';
  }
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td align="right"><input class="admCheck" type="checkbox" name="mp_All" value="1" onClick="fSelAll(this.checked)" /></td>
 <td><?php if(file_exists('loeschen.php')){?><input type="image" src="iconLoeschen.gif" name="LschBtn" width="12" height="13" border="0" title="markierte Inserate löschen" /><?php }else echo '&nbsp;'?></td>
  <td colspan="<?php echo $nSpalten+1 ?>"><a href="" target="hilfe"><img src="hilfe.gif" width="13" height="13" align="top" border="0" title="Hilfe"></a>&nbsp;</td>
 </tr>
</table>
<?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="mp_'.$k.'" value="'.$v.'" />'?>

</form>

<form name="NavigFrm" action="liste.php?seg=<?php echo $nSegNo?>" method="post">
<input type="hidden" name="mp_Archiv" value="<?php echo ($bArchiv?'1':'')?>" />

<?php
 if(AM_ListenLaenge>0){
  $sNavigator=substr_replace($sNavigator,'  <td style="white-space:nowrap;text-align:center;width:90%;"><input type="checkbox" class="admRadio" name="mp_Onl" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeOnl?' checked="checked"':'').' /> online-Inserate &nbsp; <input type="checkbox" class="admRadio" name="mp_Ofl" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeOfl?' checked="checked"':'').' /> offline-Inserate &nbsp; <input type="checkbox" class="admRadio" name="mp_Vmk" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeVmk?' checked="checked"':'').' /> Inseratevorschläge</td>',strpos($sNavigator,'<td style="width:17px;'),0);
  echo $sNavigator;
 }
 reset($aQ);
 foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="mp_'.$k.'" value="'.$v.'" />';
?>

</form>

<?php
}else echo '<p class="admMeld">Im leeren Muster-Segment gibt es keine Inserate. Bitte wählen Sie zuerst ein Segment.</p>';

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aLF,$aSS,$aAW,$aKW,$aSW,$nListenIndex;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $nListenIndex=$aFT[1]; $aFT[0]='i'; $aFT[1]='d';
 $aLF=explode(';',rtrim($aStru[2])); $aLF[0]=substr($aLF[0],14,1); //$aLF[1]=1;
 $aSS=explode(';',rtrim($aStru[6])); $aSS[0]='';
 $aAW=explode(';',rtrim($aStru[16])); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}

function fMpKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fMpNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fMpExtLink($s){
 if(!defined('MP_ZeichnsExtLink')||MP_ZeichnsExtLink==0) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode($s)));
 elseif(MP_ZeichnsExtLink==1) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s))));
 elseif(MP_ZeichnsExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fMpNavigator($nSegNo,$nSeite,$nZahl,$sQry,$bArchiv){
 $nSeitn=AM_ListenLaenge>0?ceil($nZahl/AM_ListenLaenge):1;
 $nAnf=$nSeite-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nSeitn){$nEnd=$nSeitn; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 $s='<td style="width:17px;text-align:center;white-space:nowrap;"><a href="liste.php?seg='.$nSegNo.$sQry.'&mp_Seite=1'.($bArchiv?'&mp_Archiv=1':'').'" title="Anfang">|&lt;</a></td>';
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nSeite) $sSeite=$i; else $sSeite='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:17px;text-align:center;white-space:nowrap;">&nbsp;<a href="liste.php?seg='.$nSegNo.$sQry.'&mp_Seite='.$i.($bArchiv?'&mp_Archiv=1':'').'" title="'.'">'.$sSeite.'</a>&nbsp;</td>';
 }
 $s.=NL.'  <td style="width:17px;text-align:center;white-space:nowrap;"><a href="liste.php?seg='.$nSegNo.$sQry.'&mp_Seite='.max($nSeitn,1).($bArchiv?'&mp_Archiv=1':'').'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:8px;margin-bottom:8px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td style="white-space:nowrap;">Seite '.$nSeite.'/'.$nSeitn.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}

function fMpBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.$o.'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="mpText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="mpText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="mpText"><li class="mpText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="mpText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>