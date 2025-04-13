<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisübersicht','<script language="JavaScript" type="text/javascript">
function druWin(href){dWin=window.open(href,"druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
function fSelAll(bStat){
 for(var i=0;i<self.document.ErgebnisListe.length;++i)
  if(self.document.ErgebnisListe.elements[i].type=="checkbox") self.document.ErgebnisListe.elements[i].checked=bStat;
}
</script>','EEl');

if($_SERVER['REQUEST_METHOD']=='POST'){ //Ergebnis loeschen
 $sLOk=''; $bOK=false; $aNr=array();
 foreach($_POST as $k=>$xx) if(substr($k,0,4)=='lsch') $aNr[(int)substr($k,4)]=true; //Loeschnummern
 if(count($aNr)){
  if(isset($_POST['LOk'])&&$_POST['LOk']=='1'){
   if(!FRA_SQL){ //Textdatei
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD); $nMx=0;
    for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); if(isset($aNr[$n])&&$aNr[$n]) $aD[$i]='';} //löschen
    if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Ergebnis,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld='<p class="admErfo">Die markierten Ergebnisse wurden gelöscht.</p>';
    }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.FRA_Daten.FRA_Ergebnis.'</i>',FRA_TxDateiRechte).'</p>';
   }elseif($DbO){ //beiSQL
    $s=''; foreach($aNr as $k=>$xx) $s.=' OR Eintrag='.$k;
    if($DbO->query('DELETE FROM '.FRA_SqlTabE.' WHERE '.substr($s,4))){
     $sMeld='<p class="admErfo">Die markierten Ergebnisse wurden gelöscht.</p>';
    }else $sMeld='<p class="admErfo">'.FRA_TxSqlFrage.'<p>';
   }
  }else{$sLOk='1'; $sMeld='<p class="admFehl">Wollen Sie die markierten Ergebnisse wirklich löschen?</p>';}
 }else $sMeld='<p class="admMeld">Die Ergebnisliste bleibt unverändert.</p>';
}

$aFragen=array(); $aGP=array(); $nFragen=0; // Fragen wegen Geamtpunkten holen
if(FRA_DatPunkteO&&FRA_TxZeigeAdmPkte||FRA_DatVerbalL){
 if(!FRA_SQL){ //Fragen holen
  $a=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($a);
  for($i=1;$i<$nSaetze;$i++) $aFragen[]=explode(';',rtrim($a[$i]));
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
   while($a=$rR->fetch_row()) $aFragen[]=$a; $rR->close();
 }}
 $nFragen=count($aFragen);
}
$aFnN=explode(';',FRA_NutzerFelder); $aFnT=explode(';',FRA_TeilnehmerFelder); $nFnN=count($aFnN); $nFnT=count($aFnT);
$aUsr=array(); $aUsT=array(); $nUsr=0;
if(FRA_DatPersonL&&FRA_DatPersonD&&FRA_DatPersonN){ // Suche nach Benutzerdaten
 if(!FRA_SQL){ //Textdatei
  $a=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nUsr=count($a);
  for($i=1;$i<$nUsr;$i++){
   $aTmp=explode(';',rtrim($a[$i])); $s=' '.FRA_DatPersonN;
   for($j=0;$j<$nFnN;$j++) if(strpos($s,'{'.$aFnN[$j].'}')) $s=str_replace('{'.$aFnN[$j].'}',$aTmp[$j],$s);
   $nId=(int)$aTmp[0]; $aUsr[$nId]=$aTmp; $aUsT[$nId]=$s;
 }}elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' ORDER BY Nummer')){
   while($a=$rR->fetch_row()){
    $nId=(int)$a[0]; $s=' '.FRA_DatPersonN;
    for($i=0;$i<$nFnN;$i++) if(strpos($s,'{'.$aFnN[$i].'}')) $s=str_replace('{'.$aFnN[$i].'}',$a[$i],$s);
    $aUsr[$nId]=$a; $aUsT[$nId]=$s;
   }$rR->close(); $nUsr=1;
 }}
}

$aQ=array(); $sQ=''; $NBn=(isset($_POST['nbn'])?$_POST['nbn']:'').(isset($_GET['nbn'])?$_GET['nbn']:''); //Suchparameter
$a1Filt=array(); $a2Filt=array(); $a3Filt=array();
if($ENr1=(isset($_POST['enr1'])?$_POST['enr1']:'').(isset($_GET['enr1'])?$_GET['enr1']:'')){$a1Filt[0]=$ENr1; $sQ.='&amp;enr1='.$ENr1; $aQ['enr1']=$ENr1;}
if($ENr2=(isset($_POST['enr2'])?$_POST['enr2']:'').(isset($_GET['enr2'])?$_GET['enr2']:'')){$a2Filt[0]=$ENr2; $sQ.='&amp;enr2='.$ENr2; $aQ['enr2']=$ENr2;}
$s=(isset($_POST['dat1'])?$_POST['dat1']:'').(isset($_GET['dat1'])?$_GET['dat1']:''); if(strlen($s)){$a1Filt[1]=$s; $sQ.='&amp;dat1='.rawurlencode($s); $aQ['dat1']=$s;}
$s=(isset($_POST['dat2'])?$_POST['dat2']:'').(isset($_GET['dat2'])?$_GET['dat2']:''); if(strlen($s)){$a2Filt[1]=$s; $sQ.='&amp;dat2='.rawurlencode($s); $aQ['dat2']=$s;}
$s=(isset($_POST['bnr1'])?$_POST['bnr1']:'').(isset($_GET['bnr1'])?$_GET['bnr1']:''); if(strlen($s)){$a1Filt[2]=$s; $sQ.='&amp;bnr1='.rawurlencode($s); $aQ['bnr1']=$s;}
$s=(isset($_POST['bnr2'])?$_POST['bnr2']:'').(isset($_GET['bnr2'])?$_GET['bnr2']:''); if(strlen($s)){$a2Filt[2]=$s; $sQ.='&amp;bnr2='.rawurlencode($s); $aQ['bnr2']=$s;}
$s=(isset($_POST['tln1'])?$_POST['tln1']:'').(isset($_GET['tln1'])?$_GET['tln1']:''); if(strlen($s)){$a1Filt[3]=$s; $sQ.='&amp;tln1='.rawurlencode($s); $aQ['tln1']=$s;}
$s=(isset($_POST['tln2'])?$_POST['tln2']:'').(isset($_GET['tln2'])?$_GET['tln2']:''); if(strlen($s)){$a2Filt[3]=$s; $sQ.='&amp;tln2='.rawurlencode($s); $aQ['tln2']=$s;}
$s=(isset($_POST['tln3'])?$_POST['tln3']:'').(isset($_GET['tln3'])?$_GET['tln3']:''); if(strlen($s)){$a3Filt[3]=$s; $sQ.='&amp;tln3='.rawurlencode($s); $aQ['tln3']=$s;}
if(($NTn=(isset($_POST['ntn'])?$_POST['ntn']:'').(isset($_GET['ntn'])?$_GET['ntn']:''))||$NBn){
 if(strlen($NTn.$NBn)>1){$NTn=''; $NBn='';}
 elseif(empty($NTn)){$sQ.='&amp;nbn=1'; $aQ['nbn']='1';} elseif(empty($NBn)){$sQ.='&amp;ntn=1'; $aQ['ntn']='1';}
}
$s=(isset($_POST['flg1'])?$_POST['flg1']:'').(isset($_GET['flg1'])?$_GET['flg1']:''); if(strlen($s)){$a1Filt[4]=$s; $sQ.='&amp;flg1='.rawurlencode($s); $aQ['flg1']=$s;}
$s=(isset($_POST['flg2'])?$_POST['flg2']:'').(isset($_GET['flg2'])?$_GET['flg2']:''); if(strlen($s)){$a2Filt[4]=$s; $sQ.='&amp;flg2='.rawurlencode($s); $aQ['flg2']=$s;}
$s=(isset($_POST['flg3'])?$_POST['flg3']:'').(isset($_GET['flg3'])?$_GET['flg3']:''); if(strlen($s)){$a3Filt[4]=$s; $sQ.='&amp;flg3='.rawurlencode($s); $aQ['flg3']=$s;}
$s=(isset($_POST['vrb1'])?$_POST['vrb1']:'').(isset($_GET['vrb1'])?$_GET['vrb1']:''); if(strlen($s)){$a1Filt[5]=$s; $sQ.='&amp;vrb1='.rawurlencode($s); $aQ['vrb1']=$s;}
$s=(isset($_POST['vrb2'])?$_POST['vrb2']:'').(isset($_GET['vrb2'])?$_GET['vrb2']:''); if(strlen($s)){$a2Filt[5]=$s; $sQ.='&amp;vrb2='.rawurlencode($s); $aQ['vrb2']=$s;}
$s=(isset($_POST['vrb3'])?$_POST['vrb3']:'').(isset($_GET['vrb3'])?$_GET['vrb3']:''); if(strlen($s)){$a3Filt[5]=$s; $sQ.='&amp;vrb3='.rawurlencode($s); $aQ['vrb3']=$s;}

//Daten bereitstellen
$aD=array(); $aTmp=array(); $aIdx=array(); $i=0;
if(!FRA_SQL){ //Textdaten
 $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){
  $a=explode(';',rtrim($aD[$i]),14); $nNr=(int)$a[0]; $b=true; if(!isset($a[12])||empty($a[12])) $a[12]=FRA_TxStandardTest;
  if($v=(isset($a1Filt[0])?$a1Filt[0]:'')){ //Eintragsnummer
   if($w=(isset($a2Filt[0])?$a2Filt[0]:'')){if($nNr<(int)$v||$nNr>(int)$w) $b=false;} else if($nNr!=(int)$v) $b=false;
  }
  if($v=(isset($a1Filt[1])?$a1Filt[1]:'')){ //Datum
   $t=fFraDatum($a[1]);
   if($w=(isset($a2Filt[1])?$a2Filt[1]:'')){if($t<fFraDatum($v)||$t>fFraDatum($w)) $b=false;} else if($t!=fFraDatum($v)) $b=false;
  }
  if($v=(isset($a1Filt[2])?$a1Filt[2]:'')){ //Benutzernummer
   if($w=(isset($a2Filt[2])?$a2Filt[2]:'')){if(!((int)$a[13]==(int)$v||(int)$a[13]==(int)$w)) $b=false;} else if((int)$a[13]!=(int)$v) $b=false;
  }
  $sVs=rtrim($a[13]); if(FRA_DatPersonD&&($n=(int)$sVs)&&isset($aUsT[$n])) $sVs=$aUsT[$n];
  if($v=(isset($a1Filt[3])?$a1Filt[3]:'')){ //Person
   if($w=(isset($a2Filt[3])?$a2Filt[3]:'')){if(stristr(str_replace('`,',';',$sVs),$w)) $b2=true; else $b2=false;} else $b2=false;
   if(!(stristr(str_replace('`,',';',$sVs),$v)||$b2)) $b=false;
  }
  if($v=(isset($a3Filt[3])?$a3Filt[3]:'')) if(stristr(str_replace('`,',';',$sVs),$v)) $b=false; //nicht Person
  if($NTn){if($a[13]<'@') $b=false;} elseif($NBn){if($a[13]>'@') $b=false;} //nur Teilnehmer/Benutzer
  if($v=(isset($a1Filt[4])?$a1Filt[4]:'')){ //Folge
   if($w=(isset($a2Filt[4])?$a2Filt[4]:'')){if(stristr(str_replace('`,',';',$a[12]),$w)) $b2=true; else $b2=false;} else $b2=false;
   if(!(stristr(str_replace('`,',';',$a[12]),$v)||$b2)) $b=false;
  }
  if($v=(isset($a3Filt[4])?$a3Filt[4]:'')) if(stristr(str_replace('`,',';',$a[12]),$v)) $b=false; //nicht Folge
  if(isset($a1Filt[5])||isset($a3Filt[5])){$nG=1; // Verbalbewertung bilden
   if(FRA_DatPunkteO||FRA_DatVerbalL){
    $nG=0; $sV='#|'.$a[10]; for($j=0;$j<$nFragen;$j++) if(strpos($sV,'|'.$aFragen[$j][0].':')) $nG+=$aFragen[$j][6];
   }
   $sV=str_replace('#R',$a[4],str_replace('#F',$a[5],str_replace('#A',$a[3],str_replace('#P',$a[6],str_replace('#G',$nG,FRA_VerbalTx0)))));
   if(FRA_VerbalPunkte) $p=round(100*$a[6]/max($nG,1)); else $p=round(100*$a[4]/max($a[3],1));
   for($j=6;$j>0;$j--) if(($n=constant('FRA_VerbalAb'.$j))&&$p>=$n) $sV=str_replace('#R',$a[4],str_replace('#F',$a[5],str_replace('#A',$a[3],str_replace('#P',$a[6],str_replace('#G',$nG,constant('FRA_VerbalTx'.$j))))));
  }else $sV='';
  if($v=(isset($a1Filt[5])?$a1Filt[5]:'')){ // Verbal
   if($w=(isset($a2Filt[5])?$a2Filt[5]:'')){if(stristr($sV,$w)) $b2=true; else $b2=false;} else $b2=false;
   if(!(stristr($sV,$v)||$b2)) $b=false;
  }
  if($v=(isset($a3Filt[5])?$a3Filt[5]:'')) if(stristr($sV,$v)) $b=false; //nicht Verbal
  if($b){ //Datensatz gultig
   $aTmp[$i]=array($nNr); $aTmp[$i][1]=$a[1]; $aTmp[$i][2]=$a[3]; $aTmp[$i][3]=$a[4]; $aTmp[$i][4]=$a[5]; $aTmp[$i][5]=$a[6];
   $aTmp[$i][6]=str_replace('`,',';',$a[12]); $aTmp[$i][7]=str_replace('`,',';',$a[13]); $aTmp[$i][8]=$a[10]; $aIdx[$i]=$nNr;
  }
 }
}elseif($DbO){$s=''; //SQL-Daten
 if($v=(isset($a1Filt[0])?$a1Filt[0]:'')){ //Eintragsnummer
  if($w=(isset($a2Filt[0])?$a2Filt[0]:'')) $s.=' AND Eintrag BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Eintrag="'.$v.'"';
 }
 if($v=(isset($a1Filt[1])?$a1Filt[1]:'')){ //Datum
  if($w=(isset($a2Filt[1])?$a2Filt[1]:'')) $s.=' AND Datum BETWEEN "'.fFraDatum($v).'" AND "'.fFraDatum($w).'x"';
  else $s.=' AND Datum LIKE "'.fFraDatum($v).'%"';
 }
 if($v=(isset($a1Filt[2])?$a1Filt[2]:'')){ //Benutzernummer
  $s.=' AND(Benutzer LIKE "'.sprintf('%05d',$v).';%"'; if($w=(isset($a2Filt[2])?$a2Filt[2]:'')) $s.=' OR Benutzer LIKE "'.sprintf('%05d',$w).';%"'; $s.=')';
 }
 //if($v=(isset($a1Filt[3])?$a1Filt[3]:'')){ //Person
 // $s.=' AND(Benutzer LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[3])?$a2Filt[3]:'')) $s.=' OR Benutzer LIKE "%'.$w.'%"'; $s.=')';
 //}
 if($v=(isset($a1Filt[4])?$a1Filt[4]:'')){ //Folge
  $s.=' AND(Testfolge LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[3])?$a2Filt[3]:'')) $s.=' OR Testfolge LIKE "%'.$w.'%"';
  if(stripos('#'.FRA_TxStandardTest,$v)||stripos('#'.FRA_TxStandardTest,$w)) $s.=' OR Testfolge=""'; $s.=')';
 }
 if($v=(isset($a3Filt[3])?$a3Filt[3]:'')) $s.=' AND NOT(Benutzer LIKE "%'.$v.'%")'; //nicht Person
 if($v=(isset($a3Filt[4])?$a3Filt[4]:'')){$s.=' AND NOT(Testfolge LIKE "%'.$v.'%")'; if(stripos('#'.FRA_TxStandardTest,$v)) $s.=' AND NOT(Testfolge="")';} //nicht Folge
 if($NTn) $s.=' AND Benutzer>"@"'; elseif($NBn) $s.=' AND Benutzer<"@"'; //nur Teilnehmer/Benutzer
 if($rR=$DbO->query('SELECT Eintrag,Datum,Anzahl,Richtige,Falsche,Punkte,Testfolge,Benutzer,Antwortkette FROM '.FRA_SqlTabE.($s?' WHERE '.substr($s,4):'').' ORDER BY Eintrag')){
  while($a=$rR->fetch_row()){$b=true;
   $sVs=$a[7]; if(FRA_DatPersonD&&($n=(int)$sVs)&&isset($aUsT[$n])) $sVs=$aUsT[$n];
   if($v=(isset($a1Filt[3])?$a1Filt[3]:'')){ //Person
    if($w=(isset($a2Filt[3])?$a2Filt[3]:'')){if(stristr(str_replace('`,',';',$sVs),$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr(str_replace('`,',';',$sVs),$v)||$b2)) $b=false;
   }
   if($v=(isset($a3Filt[3])?$a3Filt[3]:'')) if(stristr(str_replace('`,',';',$a[13]),$v)) $b=false; //nicht Person
   if(isset($a1Filt[5])||isset($a3Filt[5])){$nG=1; // Verbalbewertung bilden
    if(FRA_DatPunkteO||FRA_DatVerbalL){
     $nG=0; $sV='#|'.$a[8]; for($j=0;$j<$nFragen;$j++) if(strpos($sV,'|'.$aFragen[$j][0].':')) $nG+=$aFragen[$j][6];
    }
    $sV=str_replace('#R',$a[3],str_replace('#F',$a[4],str_replace('#A',$a[2],str_replace('#P',$a[5],str_replace('#G',$nG,FRA_VerbalTx0)))));
    if(FRA_VerbalPunkte) $p=round(100*$a[5]/max($nG,1)); else $p=round(100*$a[3]/max($a[2],1));
    for($j=6;$j>0;$j--) if(($n=constant('FRA_VerbalAb'.$j))&&$p>=$n) $sV=str_replace('#R',$a[3],str_replace('#F',$a[4],str_replace('#A',$a[2],str_replace('#P',$a[5],str_replace('#G',$nG,constant('FRA_VerbalTx'.$j))))));
   }else $sV='';
   if($v=(isset($a1Filt[5])?$a1Filt[5]:'')){ // Verbal
    if($w=(isset($a2Filt[5])?$a2Filt[5]:'')){if(stristr($sV,$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr($sV,$v)||$b2)) $b=false;
   }
   if($v=(isset($a3Filt[5])?$a3Filt[5]:'')) if(stristr($sV,$v)) $b=false; //nicht Verbal
   if($b){
    $nNr=(int)$a[0]; $aTmp[++$i]=array($nNr); $aIdx[$i]=$nNr; $sD=$a[1];
    $aTmp[$i][1]=date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)));
    $aTmp[$i][2]=$a[2]; $aTmp[$i][3]=$a[3]; $aTmp[$i][4]=$a[4]; $aTmp[$i][5]=$a[5]; $aTmp[$i][6]=str_replace('`,',';',$a[6]); $aTmp[$i][7]=str_replace('`,',';',$a[7]); $aTmp[$i][8]=$a[8];
   }
  }
  $rR->close();
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">keine MySQL-Verbindung</p>';
if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:0).(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADF_ErgebnisLaenge;
if(ADF_ErgebnisRueckw) arsort($aIdx); $aD=array();
reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];
if(!$sMeld) if(!$sQ) $sMeld='<p class="admMeld">Gesamt-Ergebnisliste</p>'; else $sMeld='<p class="admErfo">Abfrageergebnis</p>';
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld;?></td>
  <td align="right">[ <a href="ergebnisSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ]</td>
 </tr>
</table>

<?php
$sNavigator=fFraNavigator($nStart,count($aIdx),ADF_ErgebnisLaenge,$sQ); echo $sNavigator;
if($nStart>1) $sQ.='&amp;start='.$nStart; $aQ['start']=$nStart; $sAmp=($sQ?'&amp;':'');
?>

<form name="ErgebnisListe" action="ergebnisListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 $bLoeschen=file_exists('ergebnisLoeschen.php'); $nSp=9;
 //optionales Urkundenmodul
 $nUk=(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'X')?1:0); $nAdmKey=substr(substr(FRA_Schluessel,0,-1),1);
 //Ende Urkundenmodul
 echo '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td align="right"><b>Eintrag</b></td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td width="1%">&nbsp;</td>';
 if($nUk) echo NL.' <td>&nbsp;</td>'; //optionales Urkundenmodul
 echo NL.' <td><b>Datum</b></td>';
 echo NL.' <td><b>Testfolge</b></td>';
 if(FRA_DatAnzahlO) echo NL.' <td width="4%"><b>Fragen</b></td>'; else $nSp--;
 if(FRA_DatRichtigeO) echo NL.' <td width="4%"><b>Richtig</b></td>'; else $nSp--;
 if(FRA_DatFalscheO) echo NL.' <td width="4%"><b>Falsch</b></td>'; else $nSp--;
 if(FRA_DatPunkteO) echo NL.' <td width="4%"><b>Punkte</b></td>'; else $nSp--;
 if(FRA_DatVerbalL) echo NL.' <td><b>Bewertung</b></td>'; else $nSp--;
 if(FRA_DatPersonL) echo NL.' <td><b>Teilnehmer/Benutzer</b></td>'.NL.'</tr>';
 foreach($aD as $a){ //Datenzeilen ausgeben
  echo NL.'<tr class="admTabl">'; $sId=$a[0]; $nG=1;
  if(FRA_DatPunkteO||FRA_DatVerbalL){
   $nG=0; $sAntwort='#|'.$a[8]; for($i=0;$i<$nFragen;$i++) if(strpos($sAntwort,'|'.$aFragen[$i][0].':')) $nG+=$aFragen[$i][6];
  }
  echo NL.' <td style="white-space:nowrap">'.($bLoeschen?'<input class="admCheck" type="checkbox" name="lsch'.$sId.'" value="1"'.(isset($aNr[$sId])?' checked="checked"':'').' />':'').sprintf('%05d',$sId).'</td>';
  echo NL.' <td><a href="ergebnisDetail.php?'.$sQ.$sAmp.'nr='.$sId.'"><img src="iconVorschau.gif" width="13" height="13" border="0" style="margin-right:2px;vertical-align:middle" title="Ergebnisdetails '.$sId.'"></a></td>';
  echo NL.' <td><a href="ergebnisDruck.php?nr='.$sId.'" onclick="druWin(this.href)" target="druck"><img src="iconDrucken.gif" width="14" height="14" border="0" style="margin-right:2px;vertical-align:middle" title="Ergebnis-Druck '.$sId.'"></a></td>';
  if($nUk) echo NL.' <td><a href="urkunde.php?nr='.$sId.(KONF>0?'&konf='.KONF:'').'&adm='.$nAdmKey.'" onclick="druWin(this.href)" target="druck"><img src="iconDrucken.gif" width="14" height="14" border="0" style="margin-right:2px;vertical-align:middle" title="Urkunden-Druck '.$sId.'"></a></td>'; //optionales Urkundenmodul
  echo NL.' <td style="white-space:nowrap">'.$a[1].'</td>';
  echo NL.' <td>'.($a[6]?$a[6]:FRA_TxStandardTest).'</td>';
  if(FRA_DatAnzahlO){if(!$s=$a[2]) $s='0'; echo NL.' <td align="center">'.$s.'</td>';}
  if(FRA_DatRichtigeO){if(!$s=$a[3]) $s='0'; echo NL.' <td align="center">'.$s.'</td>';}
  if(FRA_DatFalscheO){if(!$s=$a[4]) $s='0'; echo NL.' <td align="center">'.$s.'</td>';}
  if(FRA_DatPunkteO){if(!$s=$a[5]) $s='0'; if(FRA_TxZeigeAdmPkte) $s=str_replace('#W',str_replace('.',',',round(100*$s/max($nG,1),1)).'%',str_replace('#G',$nG,str_replace('#P',rund($s),FRA_TxZeigeAdmPkte))); else $s=rund($s); echo NL.' <td align="center">'.$s.'</td>';}
  if(FRA_DatVerbalL){
   $s=str_replace('#R',$a[3],str_replace('#F',$a[4],str_replace('#A',$a[2],str_replace('#P',$a[5],str_replace('#G',$nG,FRA_VerbalTx0)))));
   if(FRA_VerbalPunkte) $p=round(100*$a[5]/max($nG,1)); else $p=round(100*$a[3]/max($a[2],1));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$a[3],str_replace('#F',$a[4],str_replace('#A',$a[2],str_replace('#P',$a[5],str_replace('#G',$nG,constant('FRA_VerbalTx'.$i))))));
   echo NL.' <td>'.$s.'</td>';
  }
  if(FRA_DatPersonL){$s=(isset($a[7])?$a[7]:''); // Teilnehmerdaten
   if(FRA_DatPersonD){
    if($a=explode(';',$s)){
     if($n=(int)$a[0]){ //Benutzer
      if(isset($aUsT[$n])) $s=$aUsT[$n];
     }else{ //Teilnehmer
      $s=' '.FRA_DatPersonT; for($i=0;$i<$nFnT;$i++) if(strpos($s,'{'.$aFnT[$i].'}')) $s=str_replace('{'.$aFnT[$i].'}',$a[$i],$s);
     }
    }
   }
   echo NL.' <td>'.trim($s).'</td>';
  }
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="fraAll" value="1" onClick="fSelAll(this.checked)" /> <input type="image" src="iconLoeschen.gif" width="12" height="13" align="top" border="0" title="markierte Ergebnisse löschen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo $nSp+1+$nUk/* optionales Urkundenmodul */ ?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="LOk" value="<?php echo $sLOk?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>

</form>
<?php
echo $sNavigator;

echo fSeitenFuss();

function fFraNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="ergebnisListe.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:3px;margin-bottom:3px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}
function fFraDatum($s){
 $aDat=explode('.',str_replace(',','.',str_replace(',','.',$s)));
 $s=(int)$aDat[2]; if($s<2000) $s+=2000; $s.='-'.sprintf('%02d-%02d',$aDat[1],$aDat[0]);
 return $s;
}
function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
?>