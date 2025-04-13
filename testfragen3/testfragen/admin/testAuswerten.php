<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Test auswerten','','ETa');

if($_SERVER['REQUEST_METHOD']=='POST'){
 //Filter holen
 $sSENr='alle'; $sSDat='alle'; $sSFlg='alle';
 if($ENr1=trim($_POST['enr1'])){$sSENr=$ENr1; if($ENr2=trim($_POST['enr2'])) $sSENr.=' bis '.$ENr2;}
 if($Flg1=trim($_POST['flg1'])){$sSFlg=$Flg1; if($Flg2=trim($_POST['flg2'])) $sSFlg.=' oder '.$Flg2;}
 if($Dat1=fNormDat($_POST['dat1'])){$sSDat=fAnzeigeDat($Dat1); if($Dat2=fNormDat($_POST['dat2'])) $sSDat.=' bis '.fAnzeigeDat($Dat2);}
 if($Dat3=fNormDat($_POST['dat3'])) $sSDat.=' ohne '.fAnzeigeDat($Dat3);
 if($ENr3=trim($_POST['enr3'])) $sSENr.=' ohne '.$ENr3;
 if($Flg3=trim($_POST['flg3'])) $sSFlg.=' ohne '.$Flg3;

 $aE=array(); $sFrNr='#'; //Ergebnisdaten filtern
 if(!FRA_SQL){ //Textdaten
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aD[$i]),14); $b=true; $nNr=(int)$a[0]; $sDat=fNormDatum($a[1]);
   $sFlg=(isset($a[12])&&!empty($a[12])?str_replace('`,',';',$a[12]):FRA_TxStandardTest);
   if($v=$ENr1){ //Eintragsnummer
    if($w=$ENr2){if($nNr<(int)$v||$nNr>(int)$w) $b=false;} else if($nNr!=(int)$v) $b=false;
   }
   if($b) if($v=$Dat1){ //Datum
    if($w=$Dat2){if($sDat<$v||$sDat>$w.'x') $b=false;} else if(substr($sDat,0,strlen($v))!=$v) $b=false;
   }
   if($b) if($v=$Flg1){ //Folge
    if($w=$Flg2){if(stristr($sFlg,$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr($sFlg,$v)||$b2)) $b=false;
   }
   if($b) if($v=$ENr3){ //ohne Eintraege
    $a3=explode(',',str_replace(';',',',str_replace(' ',',',$v))); $nC=count($a3);
    for($j=0;$j<$nC;$j++) if($nNr==(int)$a3[$j]) $b=false;
   }
   if($b) if($v=$Dat3){ //ohne Datum

   }
   if($b) if($v=$Flg3) if(stristr($sFlg,$v)) $b=false; //ohne Folge
   if($b){$aE[$nNr]=$a; $sFrNr.='|'.$a[10];}
  }
 }elseif($DbO){$s=''; //SQL-Daten
  if($v=$ENr1){ //Eintragsnummer
   if($w=$ENr2) $s.=' AND Eintrag BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Eintrag="'.$v.'"';
  }
  if($v=$Dat1){ //Datum
   if($w=$Dat2) $s.=' AND Datum BETWEEN "'.$v.'" AND "'.$w.'x"'; else $s.=' AND Datum LIKE "'.$v.'%"';
  }
  if($v=$Flg1){ //Folge
   $s.=' AND(Testfolge LIKE "%'.$v.'%"'; if($w=$Flg2) $s.=' OR Testfolge LIKE "%'.$w.'%"';
   if(stripos('#'.FRA_TxStandardTest,$v)||stripos('#'.FRA_TxStandardTest,$w)) $s.=' OR Testfolge=""'; $s.=')';
  }
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.($s?' WHERE '.substr($s,4):'').' ORDER BY Eintrag')){
   while($a=$rR->fetch_row()){$aE[(int)$a[0]]=$a; $sFrNr.='|'.$a[10];} $rR->close();
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
 }//SQL

 $aL=array(); $aP=array(); $aZ=array(); $aK=array(); $aTP=array(0); //Fragenbasis vorbereiten
 if(!FRA_SQL){
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',$aD[$i]); $n=$a[0];
   if(strpos($sFrNr,'|'.$n.':')){
    $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
    for($j=8;$j<17;$j++){
     $t=trim($a[$j]);
     if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
     if(!empty($t)) $nA++; else break;
    }
    $aZ[$n]=$nA;
  }}
 }else{ //SQL
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){
   $bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
    while($a=$rR->fetch_row()){ $n=$a[0];
     if(strpos($sFrNr,'|'.$n.':')){
      $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
      for($j=8;$j<17;$j++){
       $t=trim($a[$j]);
       if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
       if(!empty($t)) $nA++; else break;
      }
      $aZ[$n]=$nA;
    }}
    $rR->close();
   }else $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlFrage).'</p>';
  }else $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlVrbdg).'</p>';
 }

 //Auswerten
 $nSaetze=count($aE); $nNr0=999999; $nNr1=0; $nFZhl0=999999; $nFZhl1=0; $nDau0=999999; $nDau1=0; $sDau0=''; $sDau1='';
 $aPkte=array(); $aRchtg=array(); $aFlsch=array(); $aVersu=array(); $aAusla=array(); $aFrgn=array(); $aKate=array();
 $bFNr=true;
 foreach($aE as $a){
  $n=(int)$a[0]; $nNr0=min($n,$nNr0); $nNr1=max($n,$nNr1);
  $b=explode(':',$a[2]); $n=0; for($j=0;$j<count($b);$j++) $n=100*$n+$b[$j];
  if($n<$nDau0){$nDau0=$n; $sDau0=$a[2];} if($n>$nDau1){$nDau1=$n; $sDau1=$a[2];}
  $n=(int)$a[3]; $nFZhl0=min($n,$nFZhl0); $nFZhl1=max($n,$nFZhl1);
  $n=(int)$a[4]; if(isset($aRchtg[$n])) $aRchtg[$n]++; else $aRchtg[$n]=1;
  $n=(int)$a[5]; if(isset($aFlsch[$n])) $aFlsch[$n]++; else $aFlsch[$n]=1;
  $n=sprintf('%07.3f',$a[6]); if(isset($aPkte[$n])) $aPkte[$n]++; else $aPkte[$n]=1;
  $n=(int)$a[7]; if(isset($aVersu[$n])) $aVersu[$n]++; else $aVersu[$n]=1;
  $n=(int)$a[8]; if(isset($aAusla[$n])) $aAusla[$n]++; else $aAusla[$n]=1;
  if(FRA_DatFrageNr){ //FragenBewertung
   $c=explode('|',$a[9]); $nZl=count($c);
   for($i=0;$i<$nZl;$i++){
    $t=$c[$i]; $p=strpos($t,':'); $n=(int)substr($t,0,$p); $b=explode(',',substr($t,$p+1)); $j=0; $k=(isset($aK[$n])?$aK[$n]:'[???]');
    if($n>0){
     if(!isset($aFrgn[$n])) $aFrgn[$n]=array('r'=>0,'f'=>0,'p'=>0,'v'=>0,'a'=>0,0,0,0,0,0,0,0,0,0,0); $aFrgn[$n][0]++;
     if(!isset($aKate[$k])) $aKate[$k]=array('r'=>0,'f'=>0,'p'=>0,'s'=>0,'v'=>0,'a'=>0,'z'=>0); $aKate[$k]['z']++;
     if(FRA_DatErgebnis){$t=$b[$j++]; if($t=='r'){$aFrgn[$n]['r']++; $aKate[$k]['r']++;}else{$aFrgn[$n]['f']++; $aKate[$k]['f']++;}}
     if(FRA_DatPunkte){$aFrgn[$n]['p']+=$b[$j]; $aKate[$k]['p']+=$b[$j++]; $aKate[$k]['s']+=(isset($aP[$n])?$aP[$n]:0);}
     if(FRA_DatVersuche){$aFrgn[$n]['v']+=$b[$j]; $aKate[$k]['v']+=$b[$j++];}
     if(FRA_DatAuslassen){$aFrgn[$n]['a']+=$b[$j]; $aKate[$k]['a']+=$b[$j];}
    }else $bFNr=false;
   }
  }
  $c=explode('|',$a[10]); $nZl=count($c); //Antworten
  for($i=0;$i<$nZl;$i++){
   $t=$c[$i]; $p=strpos($t,':'); $n=(int)substr($t,0,$p); $b=explode(',',substr($t,$p+1));
   if($n>0){
    if(!isset($aFrgn[$n])) $aFrgn[$n]=array('r'=>0,'f'=>0,'p'=>0,'v'=>0,'a'=>0,0,0,0,0,0,0,0,0,0,0);
    foreach($b as $j) if(isset($aFrgn[$n][$j])) $aFrgn[$n][$j]++;
   }
  }
 }
 ksort($aPkte); ksort($aRchtg); ksort($aFlsch); ksort($aVersu); ksort($aAusla); ksort($aFrgn); ksort($aKate);

 $sL=''; $sK='';
 foreach($aFrgn as $k=>$a){
  $s=''; for($i=1;$i<=9;$i++) if($j=$a[$i]) $s.=$i.': '.$j.' x, ';
  $sL.='<tr class="admTabl">';
  $sL.='<td align="center">'.$k.'</td>';
  $sL.='<td align="center">'.$a[0].' x</td>';
  $sL.='<td align="center">'.$a['r'].' x</td>';
  $sL.='<td align="center">'.$a['f'].' x</td>';
  $sL.='<td align="center">'.str_replace('.',',',sprintf('%.2f',$a['p']/max($a[0],1))).' von '.(isset($aP[$k])?$aP[$k]:'??').'</td>';
  $sL.='<td>'.substr($s,0,-2).'</td>';
  $sL.='<td align="center">'.$a['v'].' x</td>';
  $sL.='<td align="center">'.$a['a'].' x</td>';
  $sL.='</tr>';
 }
 foreach($aKate as $k=>$a){
  $sK.='<tr class="admTabl">';
  $sK.='<td>'.str_replace(',','<br>',str_replace('#','-&gt;',$k)).'</td>';
  $sK.='<td align="center">'.$a['z'].' x</td>';
  $sK.='<td align="center">'.$a['r'].' x</td>';
  $sK.='<td align="center">'.$a['f'].' x</td>';
  $sK.='<td align="center">'.str_replace('.',',',sprintf('%.2f',$a['p'])).' von '.$a['s'].'</td>';
  $sK.='<td align="center">'.$a['v'].' x</td>';
  $sK.='<td align="center">'.$a['a'].' x</td>';
  $sK.='</tr>';
 }

?>
<p class="admMeld">Testauswertung</p>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td width="8%"><b>Filterbedingungen</b></td>
  <td>&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td>Eintrags-Nummer</td>
  <td><?php echo $sSENr?></td>
 </tr>
 <tr class="admTabl">
  <td>Testzeitpunkt</td>
  <td><?php echo $sSDat?></td>
 </tr>
 <tr class="admTabl">
  <td>Testname</td>
  <td><?php echo $sSFlg?></td>
 </tr>
 <tr class="admTabl">
  <td><b>gefilterte Tests</b></td>
  <td>&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td>Anzahl der Tests</td>
  <td><?php echo $nSaetze?></td>
 </tr>
 <tr class="admTabl">
  <td>Eintrags-Nummern</td>
  <td><?php echo $nNr0.' bis '.$nNr1?></td>
 </tr>
 <tr class="admTabl">
  <td>Anzahl der Fragen</td>
  <td><?php echo $nFZhl0; if($nFZhl1>$nFZhl0) echo ' bis '.$nFZhl1?></td>
 </tr>
 <tr class="admTabl">
  <td>Bearbeitungszeit</td>
  <td><?php echo $sDau0; if($sDau1>$sDau0) echo ' bis '.$sDau1?></td>
 </tr>
 <tr class="admTabl">
  <td><b>Zusammenfassungen</b></td>
  <td>&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top"><div style="float:left">Richtige</div><div style="float:right"><img src="iconVorschau.gif" id="iZR" onclick="zeige('ZR')" width="13" height="13" border="0" title="Einzelheiten zeigen" alt=""></div></td>
  <td><table id="tZR" class="admTabl" style="width:15em" border="0" cellpadding="1" cellspacing="1"><?php foreach($aRchtg as $k=>$v) echo '<tr class="admTabl"><td style="width:10em;text-align:right">'.$k.' Richtige</td><td style="width:5em;text-align:right">'.$v.' x</td></tr> ' ?></table></td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top"><div style="float:left">Falsche</div><div style="float:right"><img src="iconVorschau.gif" id="iZF" onclick="zeige('ZF')" width="13" height="13" border="0" title="Einzelheiten zeigen" alt=""></div></td>
  <td><table id="tZF" class="admTabl" style="width:15em" border="0" cellpadding="1" cellspacing="1"><?php foreach($aFlsch as $k=>$v) echo '<tr class="admTabl"><td style="width:10em;text-align:right">'.$k.' Falsche</td><td style="width:5em;text-align:right">'.$v.' x</td></tr> ' ?></table></td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top"><div style="float:left">Punkte</div><div style="float:right"><img src="iconVorschau.gif" id="iZP" onclick="zeige('ZP')" width="13" height="13" border="0" title="Einzelheiten zeigen" alt=""></div></td>
  <td><table id="tZP" class="admTabl" style="width:15em" border="0" cellpadding="1" cellspacing="1"><?php foreach($aPkte as $k=>$v) echo '<tr class="admTabl"><td style="width:10em;text-align:right">'.str_replace('.',',',sprintf('%.2f',$k)).' Punkte</td><td style="width:5em;text-align:right">'.$v.' x</td></tr> ' ?></table></td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top"><div style="float:left">Versuche</div><div style="float:right"><img src="iconVorschau.gif" id="iZV" onclick="zeige('ZV')" width="13" height="13" border="0" title="Einzelheiten zeigen" alt=""></div></td>
  <td><table id="tZV" class="admTabl" style="width:15em" border="0" cellpadding="1" cellspacing="1"><?php foreach($aVersu as $k=>$v) echo '<tr class="admTabl"><td style="width:10em;text-align:right">'.$k.' Versuche</td><td style="width:5em;text-align:right">'.$v.' x</td></tr> ' ?></table></td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top"><div style="float:left">Auslassungen</div><div style="float:right"><img src="iconVorschau.gif" id="iZA" onclick="zeige('ZA')" width="13" height="13" border="0" title="Einzelheiten zeigen" alt=""></div></td>
  <td><table id="tZA" class="admTabl" style="width:15em" border="0" cellpadding="1" cellspacing="1"><?php foreach($aAusla as $k=>$v) echo '<tr class="admTabl"><td style="width:10em;text-align:right">'.$k.' Auslassungen</td><td style="width:5em;text-align:right">'.$v.' x</td></tr> ' ?></table></td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top;"><b>Fragen</b></td>
  <td>
   <table class="admTabl" style="width:auto;" border="0" cellpadding="2" cellspacing="1">
    <tr class="admTabl">
     <td align="center">Frage</td>
     <td align="center">Anzahl</td>
     <td align="center">Richtig</td>
     <td align="center">Falsch</td>
     <td align="center">Punkte</td>
     <td>Antworten</td>
     <td align="center">Versuche</td>
     <td align="center">Auslass.</td>
    </tr>
<?php echo $sL?>
   </table>
  </td>
 </tr>
 <tr class="admTabl">
  <td style="vertical-align:top;"><b>Kategorien</b></td>
  <td>
   <table class="admTabl" style="width:auto;" border="0" cellpadding="2" cellspacing="1">
    <tr class="admTabl">
     <td>Kategorie</td>
     <td align="center">Anzahl</td>
     <td align="center">Richtig</td>
     <td align="center">Falsch</td>
     <td align="center">Punkte</td>
     <td align="center">Versuche</td>
     <td align="center">Auslass.</td>
    </tr>
<?php echo $sK?>
   </table>
  </td>
 </tr>
</table>

<script type="text/javascript">
var ImPlus=new Image(); ImMinu=new Image(); ImPlus.src='iconVorschau.gif'; ImMinu.src='iconVorschZu.gif';
var b = new Array(); b['ZR']=true; b['ZF']=true; b['ZP']=true; b['ZV']=true; b['ZA']=true;

function zeige(id){
 if(!b[id]){
  document.getElementById('t'+id).style.display='table'; b[id]=true;
  document.getElementById('i'+id).src=ImMinu.src; document.getElementById('i'+id).title='Einzelheiten verbergen';
 }else{
  document.getElementById('t'+id).style.display='none';  b[id]=false;
  document.getElementById('i'+id).src=ImPlus.src; document.getElementById('i'+id).title='Einzelheiten zeigen';
 }
 return true;
}

zeige('ZA'); zeige('ZV');  zeige('ZP'); zeige('ZF'); zeige('ZR');
</script>

<?php
}else{ //GET
 $ENr1=''; $ENr2=''; $ENr3='';
 $Dat1=''; $Dat2=''; $Dat3='';
 $Flg1=''; $Flg2=''; $Flg3='';
?>
<p class="admMeld">Setzen Sie die Filterbedingungen für einen auszuwertenden Test.</p>

<form name="fraEingabe" action="testAuswerten.php" method="post">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Eintrag-Nummer ab<br><input type="text" name="enr1" value="<?php echo $ENr1?>" style="width:8em;" /></td>
  <td style="width:34%">Eintrag-Nummer bis<br><input type="text" name="enr2" value="<?php echo $ENr2?>" style="width:8em;" /></td>
  <td style="width:32%">ohne Eintrag-Nummern<br><input type="text" name="enr3" value="<?php echo $ENr3?>" style="width:8em;" /></td>
 </tr><tr class="admTabl">
  <td style="width:34%">Datum/Zeit wie oder ab<br><input type="text" name="dat1" value="<?php echo $Dat1?>" style="width:8em;" /><div class="admMini">(TT.MM.JJ oder TT.MM.JJ hh:mm)</div></td>
  <td style="width:34%">Datum/Zeit bis<br><input type="text" name="dat2" value="<?php echo $Dat2?>" style="width:8em;" /><div class="admMini">(TT.MM.JJ oder TT.MM.JJ hh:mm)</div></td>
  <td style="width:32%">ohne Datum<br><input type="text" name="dat3" value="<?php echo $Dat3?>" style="width:8em;" /><div class="admMini">(TT.MM.JJ oder TT.MM.JJ hh:mm)</div></td>
 </tr><tr class="admTabl">
  <td style="width:34%">Testfolgenname wie<br><input type="text" name="flg1" value="<?php echo $Flg1?>" style="width:99%;" /></td>
  <td style="width:34%">oder wie<br><input type="text" name="flg2" value="<?php echo $Flg2?>" style="width:99%;" /></td>
  <td style="width:32%">aber nicht wie<br><input type="text" name="flg3" value="<?php echo $Flg3?>" style="width:99%;" /></td>
 </tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Auswerten"></p>
</div>
</form>

<?php
}
echo fSeitenFuss();

function fNormDatum($w){
 $a=explode('.',str_replace('-','.',str_replace(' ','.',strtolower(FRA_Datumsformat))));
 $nJ=array_search('y',$a); $nM=array_search('m',$a); $nT=array_search('d',$a);
 $a=explode('.',str_replace('-','.',str_replace(' ','.',$w)));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}
function fNormDat($s){
 if($s=trim($s)){
  $a=explode(' ',$s); $s=str_replace(':','.',str_replace(',','.',$a[0])); $t=(isset($a[1])?str_replace('.',':',$a[1]):'');
  $a=explode('.',$s); $s=sprintf('%d-%02d-%02d',(isset($a[2])?((int)$a[2]<2000?2000+$a[2]:$a[2]):2000),(isset($a[1])?$a[1]:0),$a[0]);
  if($t>''){$a=explode(':',$t); $s.=sprintf(' %02d:%02d',$a[0],(isset($a[1])?$a[1]:0));}
 }
 return $s;
}
function fAnzeigeDat($s){
 return substr($s,8,2).'.'.substr($s,5,2).'.'.substr($s,0,4).substr($s,10);
}
?>