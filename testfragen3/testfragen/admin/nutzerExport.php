<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf(' Benutzeexport','','NNl');

for($i=59;$i>=0;$i--) if(file_exists(FRA_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv')) unlink(FRA_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv');

$aQ=array(); $sQ=''; //Suchparameter
$aFelder=explode(';',FRA_NutzerFelder); $nFelder=count($aFelder); for($i=2;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
if($Nr1=(isset($_POST['nr1'])?$_POST['nr1']:'').(isset($_GET['nr1'])?$_GET['nr1']:'')){$a1Filt[0]=$Nr1; $sQ.='&amp;nr1='.$Nr1; $aQ['nr1']=$Nr1;}
if($Nr2=(isset($_POST['nr2'])?$_POST['nr2']:'').(isset($_GET['nr2'])?$_GET['nr2']:'')){$a2Filt[0]=$Nr2; $sQ.='&amp;nr2='.$Nr2; $aQ['nr2']=$Nr2;}
$Onl=(isset($_POST['onl'])?$_POST['onl']:'').(isset($_GET['onl'])?$_GET['onl']:''); if(strlen($Onl)){$a1Filt[1]=$Onl; $sQ.='&amp;onl='.((int)$Onl);}
for($i=2;$i<$nFelder;$i++){
 $s=(isset($_POST['n1'.$i])?$_POST['n1'.$i]:'').(isset($_GET['n1'.$i])?$_GET['n1'.$i]:''); if(strlen($s)){$a1Filt[$i]=$s; $sQ.='&amp;n1'.$i.'='.rawurlencode($s); $aQ['n1'.$i]=$s;}
 $s=(isset($_POST['n2'.$i])?$_POST['n2'.$i]:'').(isset($_GET['n2'.$i])?$_GET['n2'.$i]:''); if(strlen($s)){$a2Filt[$i]=$s; $sQ.='&amp;n2'.$i.'='.rawurlencode($s); $aQ['n2'.$i]=$s;}
 $s=(isset($_POST['n3'.$i])?$_POST['n3'.$i]:'').(isset($_GET['n3'.$i])?$_GET['n3'.$i]:''); if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;n3'.$i.'='.rawurlencode($s); $aQ['n3'.$i]=$s;}
}

//Daten bereitstellen
$aD=array(); $aTmp=array(); $aIdx=array(); $aPflicht=explode(';',FRA_NutzerPflicht);
if(!FRA_SQL){ //Textdaten
 $aD=@file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true;
  if(isset($a1Filt)&&is_array($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
   foreach($a1Filt as $j=>$v) if($b&&$j>1){
    if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')){if(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fFraDecode($a[$j]):$a[$j])):''),$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fFraDecode($a[$j]):$a[$j])):''),$v)||$b2)) $b=false;
   }else if($a[$j]!=$v) $b=false;
  }
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v){if(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fFraDecode($a[$j]):$a[$j])):''),$v)){$b=false; break;}}
  }
  if($b){ //Datensatz gueltig
   $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,akt
   $aTmp[$sNr][2]=fFraDecode($a[2]); $aTmp[$sNr][3]=fFraDecode($a[3]); $aTmp[$sNr][4]=fFraDecode($a[4]); //User,PW,E-Mail
   for($j=5;$j<$nFelder;$j++) $aTmp[$sNr][$j]=(isset($a[$j])?str_replace('\n ',NL,str_replace('`,',';',$a[$j])):'');
   $aIdx[$sNr]=sprintf('%0'.FRA_NummerStellen.'d',$i);
  }
 }$aD=array();
}elseif($DbO){ //SQL
 $s='';
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sS=($j==4?'eMail':($j==3?'Passwort':($j==2?'Benutzer':'dat_'.$j)));
   $s.=' AND('.$sS.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sS.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if($w=(isset($a2Filt[0])?(int)$a2Filt[0]:'')) $s.=' AND (Nummer>="'.((int)$v).'" AND Nummer<="'.$w.'")';
   else $s.=' AND Nummer="'.((int)$v).'"';
  }else $s.=' AND aktiv="'.$v.'"';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  if($j>1) $s.=' AND NOT('.($j==4?'eMail':($j==3?'Passwort':($j==2?'Benutzer':'dat_'.$j))).' LIKE "%'.$v.'%")';
  else $s.=' AND '.($j==0?'Nummer':'aktiv').'<>"'.$v.'"';
 }
 $sS=''; for($j=5;$j<$nFelder;$j++) $sS.=',dat_'.$j; $i=0;
 if($rR=$DbO->query('SELECT Nummer,aktiv,Benutzer,Passwort,eMail'.$sS.' FROM '.FRA_SqlTabN.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  while($a=$rR->fetch_row()){
   $sNr=$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,akt
   for($j=2;$j<=$nFelder;$j++) $aTmp[$sNr][$j]=(isset($a[$j])?$a[$j]:'');
   $aIdx[$sNr]=sprintf('%0'.FRA_NummerStellen.'d',++$i);
  }$rR->close();
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADF_NutzerLaenge;
if(ADF_NutzerRueckw) arsort($aIdx);
reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) /* if(++$k<$nStop&&$k>=$nStart) */ $aD[]=$aTmp[$i];

if(!$sMeld) if(!$sQ) $sMeld='<p class="admMeld">Benutzerexportliste</p>'; else $sMeld='<p class="admMeld">Suchergebnis</p>';
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));

//Scriptausgabe
?>

<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld?></td>
  <td align="right">[ <a href="nutzerSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ] [ <a href="nutzerListe.php<?php if($sQ) echo '?'.$sQ?>">Liste</a> ]</td>
 </tr>
</table>

<?php
$sEx='';
$sZl=''; for($i=0;$i<$nFelder;$i++) if($i!=3) $sZl.=$aFelder[$i].';'; $sEx.=substr($sZl,0,-1)."\n";
foreach($aD as $a){ //Datenzeilen ausgeben
 $sZl=$a[0].';'.$a[1].';'.(!FRA_SQL?$a[2]:$a[2]).';'.(!FRA_SQL?$a[4]:$a[4]).';';
 for($i=5;$i<$nFelder;$i++) $sZl.=$a[$i].';'; $sEx.=substr($sZl,0,-1)."\n";
}

$sExNa='nutzer_'.date('s').'.csv';
if($f=fopen(FRA_Pfad.'temp/'.$sExNa,'w')){
 fwrite($f,$sEx); fclose($f);
 $sMeld='<p class="admErfo">Die Benutzerdatei liegt unter <a href="'.$sHttp.'temp/'.$sExNa.'" target="hilfe" onclick="hlpWin(this.href);return false;" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.</p>';
}else $sMeld='<p class="admFehl">'.str_replace('#','<i>temp/'.$sExNa.'</i>',FRA_TxDateiRechte).'</p>';

echo '<div style="margin:5em;text-align:center">'.$sMeld.'</div>';

echo fSeitenFuss();
?>
