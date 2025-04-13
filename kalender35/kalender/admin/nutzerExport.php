<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerexport','','NNl');

for($i=59;$i>=0;$i--) if(file_exists(KAL_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv')) unlink(KAL_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv');

//Abfrageparameter aufbereiten
$nFelder=count($kal_NutzerFelder); $aF1=array(); $aF2=array(); $aF2=array(); $sQ='';
if($sIdn1=(int)(isset($_POST['Idn1'])?$_POST['Idn1']:(isset($_GET['Idn1'])?$_GET['Idn1']:0))){
 $sQ.='&amp;Idn1='.$sIdn1;
 if($sIdn2=(int)(isset($_POST['Idn2'])?$_POST['Idn2']:(isset($_GET['Idn2'])?$_GET['Idn2']:0))) $sQ.='&amp;Idn2='.$sIdn2;
}else $sIdn2='';
if($sUsr1=trim(isset($_POST['Usr1'])?$_POST['Usr1']:(isset($_GET['Usr1'])?$_GET['Usr1']:''))){
 $sQ.='&amp;Usr1='.urlencode($sUsr1);
 if($sUsr2=trim(isset($_POST['Usr2'])?$_POST['Usr2']:(isset($_GET['Usr2'])?$_GET['Usr2']:''))) $sQ.='&amp;Usr2='.urlencode($sUsr2);
}else $sUsr2='';
if($sUsr3=trim(isset($_POST['Usr3'])?$_POST['Usr3']:(isset($_GET['Usr3'])?$_GET['Usr3']:''))) $sQ.='&amp;Usr3='.urlencode($sUsr3);
if($sEml1=trim(isset($_POST['Eml1'])?$_POST['Eml1']:(isset($_GET['Eml1'])?$_GET['Eml1']:''))){
 $sQ.='&amp;Eml1='.urlencode($sEml1);
 if($sEml2=trim(isset($_POST['Eml2'])?$_POST['Eml2']:(isset($_GET['Eml2'])?$_GET['Eml2']:''))) $sQ.='&amp;Eml2='.urlencode($sEml2);
}else $sEml2='';
if($sEml3=trim(isset($_POST['Eml3'])?$_POST['Eml3']:(isset($_GET['Eml3'])?$_GET['Eml3']:''))) $sQ.='&amp;Eml3='.urlencode($sEml3);
for($i=6;$i<$nFelder;$i++){
 if($aF1[$i]=trim(isset($_POST['F1'.$i])?$_POST['F1'.$i]:(isset($_GET['F1'.$i])?$_GET['F1'.$i]:''))){
  $sQ.='&amp;F1'.$i.'='.urlencode($aF1[$i]);
  if($aF2[$i]=trim(isset($_POST['F2'.$i])?$_POST['F2'.$i]:(isset($_GET['F2'.$i])?$_GET['F2'.$i]:''))) $sQ.='&amp;F2'.$i.'='.urlencode($aF2[$i]);
 }else $aF2[$i]='';
 if($aF3[$i]=trim(isset($_POST['F3'.$i])?$_POST['F3'.$i]:(isset($_GET['F3'.$i])?$_GET['F3'.$i]:''))) $sQ.='&amp;F3'.$i.'='.urlencode($aF3[$i]);
}
$sSta1=(int)(isset($_POST['Sta1'])?$_POST['Sta1']:(isset($_GET['Sta1'])?$_GET['Sta1']:0));
$sSta2=(int)(isset($_POST['Sta2'])?$_POST['Sta2']:(isset($_GET['Sta2'])?$_GET['Sta2']:0));
if($sSta1&&$sSta2) $sSta1=$sSta2=0; if($sSta1) $sQ.='&amp;Sta1=1'; if($sSta2) $sQ.='&amp;Sta2=1';

//Daten bereitstellen
$bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
array_splice($kal_NutzerFelder,1,1); $amNutzerFelder=min(ADM_NutzerFelder,count($kal_NutzerFelder)-1); $aTmp=array(); $aD=array();
if(!KAL_SQL){ //Textdaten
 if($sQ){ //filtern
  $aTmp=array(); $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); array_shift($aD); $nSaetze=count($aD);
  for($i=0;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aD[$i])); $b=true;
   if($sIdn1){
    if(!$sIdn2){if($sIdn1!=(int)$a[0]) $b=false;} else{if($sIdn1<(int)$a[0]||$sIdn2>(int)$a[0]) $b=false;}
   }
   if($sSta1) if($a[2]!='1') $b=false; if($sSta2) if($a[2]!='0') $b=false;
   if($sUsr1){
    $s=fKalDeCode(str_replace('`,',';',$a[3])); $b1=stristr($s,$sUsr1);
    if(!$sUsr2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sUsr2))) $b=false;}
   }
   if($sUsr3) if(stristr(fKalDeCode(str_replace('`,',';',$a[3]),$sUsr3))) $b=false;
   if($sEml1){
    $s=fKalDeCode($a[5]); $b1=stristr($s,$sEml1);
    if(!$sEml2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sEml2))) $b=false;}
   }
   if($sEml3) if(stristr(fKalDeCode($a[5]),$sEml3)) $b=false;
   if($b) for($j=6;$j<$nFelder;$j++){
    if($t=$aF1[$j]){
     $s=str_replace('`,',';',$a[$j]); $b1=stristr($s,$t);
     if(!$t=$aF2[$j]){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$t))) $b=false;}
    }
    if($t=$aF3[$j]) if(stristr(str_replace('`,',';',$a[$j]),$t)) $b=false;
   }
   if($b) $aTmp[]=$aD[$i]; //gueltig
  }
 }else{$aTmp=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); array_shift($aTmp);}
}elseif($DbO){ $sF=''; //SQL
 if($sIdn1){
  if(!$sIdn2) $sF.=' AND nr="'.$sIdn1.'"'; else $sF.=' AND nr BETWEEN "'.$sIdn1.'" AND "'.$sIdn2.'"';
 }
 if($sSta1) $sF.=' AND aktiv="1"'; elseif($sSta2) $sF.=' AND aktiv="0"';
 if($sUsr1){
  if(!$sUsr2) $sF.=' AND benutzer LIKE "%'.$sUsr1.'%"'; else $sF.=' AND (benutzer LIKE "%'.$sUsr1.'%" OR benutzer LIKE "%'.$sUsr2.'%")';
 }
 if($sUsr3) $sF.=' AND NOT benutzer LIKE "%'.$sUsr3.'%"';
 if($sEml1){
  if(!$sEml2) $sF.=' AND email LIKE "%'.$sEml1.'%"'; else $sF.=' AND (email LIKE "%'.$sEml1.'%" OR email LIKE "%'.$sEml2.'%")';
 }
 if($sEml3) $sF.=' AND NOT email LIKE "%'.$sEml3.'%"';
 for($j=6;$j<$nFelder;$j++){ $k=$j-1;
  if($t=$aF1[$j]){
   if(!$aF2[$j]) $sF.=' AND dat_'.$k.' LIKE "%'.$t.'%"'; else $sF.=' AND (dat_'.$k.' LIKE "%'.$t.'%" OR dat_'.$k.' LIKE "%'.$aF2[$j].'%")';
  }
  if($t=$aF3[$j]) $sF.=' AND NOT dat_'.$k.' LIKE "%'.$t.'%"';
 }
 if($sF) $sF=' WHERE'.substr($sF,4);
 $s=''; for($j=5;$j<=$amNutzerFelder;$j++) $s.=',dat_'.$j;
 if($rR=$DbO->query('SELECT nr,aktiv,benutzer,passwort,email'.$s.' FROM '.KAL_SqlTabN.$sF.' ORDER BY nr')){
  while($a=$rR->fetch_row()){
   $s=$a[0]; for($j=1;$j<=$amNutzerFelder;$j++) $s.=';'.$a[$j]; $aTmp[]=$s;
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
if(!$nStart=(int)(isset($_GET['kal_Start'])?$_GET['kal_Start']:(isset($_POST['kal_Start'])?$_POST['kal_Start']:0))) $nStart=1; $nStop=$nStart+ADM_NutzerLaenge;
reset($aTmp); $nSaetze=count($aTmp); $aD=array(); $k=0;
foreach($aTmp as $i=>$xx) /* if(++$k<$nStop&&$k>=$nStart) */ $aD[]=$aTmp[$i];

if(!$Msg){
 if($bNutzer) $Msg='<p class="admMeld">Benutzerexportliste'.($sQ?' (gefiltert)':'').'</p>';
 else $Msg='<p class="admFehl">Die Benutzerverwaltung ist ohne ein Feld vom Typ <i>Benutzer</i> in der Terminstruktur momentan inaktiv!</p>';
}

//Scriptausgabe
?>
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $Msg?></td>
  <td align="right">
   [ <a href="nutzerSuche.php<?php echo ($sQ?'?'.substr($sQ,5):'')?>"><img src="<?php echo $sHttp?>grafik/icon_Lupe.gif" width="12" height="13" border="0" title="suchen" alt="suchen">&nbsp;suchen</a> ]
   [ <a href="nutzerListe.php<?php echo ($sQ?'?'.substr($sQ,5):'')?>"><img src="<?php echo $sHttp?>grafik/icon_Kopie.gif" align="top" width="12" height="13" border="0" title="Liste">&nbsp;Liste</a> ]
  </td>
 </tr>
</table>

<?php

$sEx=''; $a=$kal_NutzerFelder; $l=count($a);
$sZl=''; for($i=0;$i<$l;$i++) if($i!=3) $sZl.=$a[$i].';'; $sEx.=substr($sZl,0,-1)."\n";
foreach($aD as $a){ //Datenzeilen ausgeben
 $a=explode(';',rtrim($a)); if(!KAL_SQL) array_splice($a,1,1);
 $sZl=$a[0].';'.$a[1].';'.(!KAL_SQL?fKalDeCode($a[2]):$a[2]).';'.(!KAL_SQL?fKalDeCode($a[4]):$a[4]).';';
 for($i=5;$i<$l;$i++) $sZl.=$a[$i].';'; $sEx.=substr($sZl,0,-1)."\n";
}

$sExNa='nutzer_'.date('s').'.csv';
if($f=fopen(KAL_Pfad.'temp/'.$sExNa,'w')){
 fwrite($f,$sEx); fclose($f);
 $Msg='<p class="admErfo">Die Benutzerdatei liegt unter <a href="'.$sHttp.'temp/'.$sExNa.'" target="hilfe" onclick="hlpWin(this.href);return false;" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.</p>';
}else $Msg='<p class="admFehl">'.str_replace('#','<i>temp/'.$sExNa.'</i>',KAL_TxDateiRechte).'</p>';

echo '<div style="margin:5em;text-align:center">'.$Msg.'</div>';

echo fSeitenFuss();
?>