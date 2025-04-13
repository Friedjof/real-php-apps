<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerexport','','NNl');

for($i=59;$i>=0;$i--) if(file_exists(MP_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv')) unlink(MP_Pfad.'temp/nutzer_'.sprintf('%02d',$i).'.csv');

$aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $Msg='';
$amNutzerFelder=min(AM_NutzerFelder,count($aNF)-1); $aTmp=array(); $aId=array();

//Abfrageparameter aufbereiten
$nFelder=count($aNF); $aF1=array(); $aF2=array(); $aF2=array(); $sQ='';
if($sIdn1=(int)(isset($_POST['Idn1'])?$_POST['Idn1']:(isset($_GET['Idn1'])?$_GET['Idn1']:0))){
 $sQ.='&Idn1='.$sIdn1;
 if($sIdn2=(int)(isset($_POST['Idn2'])?$_POST['Idn2']:(isset($_GET['Idn2'])?$_GET['Idn2']:0))) $sQ.='&Idn2='.$sIdn2;
}else $sIdn2='';
if($sLog=(isset($_POST['Log'])?(int)$_POST['Log']:(isset($_GET['Log'])?(int)$_GET['Log']:0))) $sQ.='&Log='.$sLog;
if($sUsr1=trim(isset($_POST['Usr1'])?$_POST['Usr1']:(isset($_GET['Usr1'])?$_GET['Usr1']:''))){
 $sQ.='&Usr1='.urlencode($sUsr1);
 if($sUsr2=trim(isset($_POST['Usr2'])?$_POST['Usr2']:(isset($_GET['Usr2'])?$_GET['Usr2']:''))) $sQ.='&Usr2='.urlencode($sUsr2);
}else $sUsr2='';
if($sUsr3=trim(isset($_POST['Usr3'])?$_POST['Usr3']:(isset($_GET['Usr3'])?$_GET['Usr3']:''))) $sQ.='&Usr3='.urlencode($sUsr3);
if($sEml1=trim(isset($_POST['Eml1'])?$_POST['Eml1']:(isset($_GET['Eml1'])?$_GET['Eml1']:''))){
 $sQ.='&Eml1='.urlencode($sEml1);
 if($sEml2=trim(isset($_POST['Eml2'])?$_POST['Eml2']:(isset($_GET['Eml2'])?$_GET['Eml2']:''))) $sQ.='&Eml2='.urlencode($sEml2);
}else $sEml2='';
if($sEml3=trim(isset($_POST['Eml3'])?$_POST['Eml3']:(isset($_GET['Eml3'])?$_GET['Eml3']:''))) $sQ.='&Eml3='.urlencode($sEml3);
for($i=6;$i<$nFelder;$i++){
 if($aF1[$i]=trim(isset($_POST['F1'.$i])?$_POST['F1'.$i]:(isset($_GET['F1'.$i])?$_GET['F1'.$i]:''))){
  $sQ.='&F1'.$i.'='.urlencode($aF1[$i]);
  if($aF2[$i]=trim(isset($_POST['F2'.$i])?$_POST['F2'.$i]:(isset($_GET['F2'.$i])?$_GET['F2'.$i]:''))) $sQ.='&F2'.$i.'='.urlencode($aF2[$i]);
 }else $aF2[$i]='';
 if($aF3[$i]=trim(isset($_POST['F3'.$i])?$_POST['F3'.$i]:(isset($_GET['F3'.$i])?$_GET['F3'.$i]:''))) $sQ.='&F3'.$i.'='.urlencode($aF3[$i]);
}
$sSta1=(int)(isset($_POST['Sta1'])?$_POST['Sta1']:(isset($_GET['Sta1'])?$_GET['Sta1']:0));
$sSta2=(int)(isset($_POST['Sta2'])?$_POST['Sta2']:(isset($_GET['Sta2'])?$_GET['Sta2']:0));
$sSta3=(int)(isset($_POST['Sta3'])?$_POST['Sta3']:(isset($_GET['Sta3'])?$_GET['Sta3']:0));
if($sSta1&&$sSta2&&$sSta3) $sSta1=$sSta2=$sSta3=0; if($sSta1) $sQ.='&Sta1=1'; if($sSta2) $sQ.='&Sta2=1'; if($sSta3) $sQ.='&Sta3=1';

//Daten bereitstellen
$aTmp=array(); $aD=array();
if($sLog) $nTm=(time()-86400*$sLog)>>6;
if(!MP_SQL){ //Textdaten
 $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); array_shift($aD);
 foreach($aD as $s){
  $a=explode(';',rtrim($s)); $a[3]=fMpDeCode($a[3]); $a[5]=fMpDeCode($a[5]);
  if($sQ){ $b=true; //filtern
   if($sIdn1){
    if(!$sIdn2){if($sIdn1!=(int)$a[0]) $b=false;} else{if($sIdn1<(int)$a[0]||$sIdn2>(int)$a[0]) $b=false;}
   }
   if($sLog){$s=$a[1]; if(substr($s,0,1)=='~') $s=substr($s,1); if(strlen($s)>=8) if((int)$s>$nTm) $b=false;}
   if($sSta1&&!$sSta2&&!$sSta3) if($a[2]!='1') $b=false; if(($sSta2||$sSta3)&&!$sSta1) if($a[2]=='1') $b=false; // aktiv
   if($sSta2&&!$sSta1&&!$sSta3) if($a[2]!='0') $b=false; if(($sSta1||$sSta3)&&!$sSta2) if($a[2]=='0') $b=false; // inaktiv
   if($sSta3&&!$sSta1&&!$sSta2) if($a[2]!='2') $b=false; if(($sSta1||$sSta2)&&!$sSta3) if($a[2]=='2') $b=false; // wartend
   if($sUsr1){
    $s=str_replace('`,',';',$a[3]); $b1=stristr($s,$sUsr1);
    if(!$sUsr2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sUsr2))) $b=false;}
   }
   if($sUsr3) if(stristr(str_replace('`,',';',$a[3]),$sUsr3)) $b=false;
   if($sEml1){
    $s=$a[5]; $b1=stristr($s,$sEml1);
    if(!$sEml2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sEml2))) $b=false;}
   }
   if($sEml3) if(stristr($a[5],$sEml3)) $b=false;
   if($b) for($j=6;$j<$nFelder;$j++){
    if($t=$aF1[$j]){
     $s=str_replace('`,',';',$a[$j]); $b1=stristr($s,$t);
     if(!$t=$aF2[$j]){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$t))) $b=false;}
    }
    if($t=$aF3[$j]) if(stristr(str_replace('`,',';',$a[$j]),$t)) $b=false;
   }
  }else $b=true;
  if($b) $aTmp[]=$a;
 }
}elseif($DbO){ $sF=''; //SQL
 if($sIdn1){
  if(!$sIdn2) $sF.=' AND nr="'.$sIdn1.'"'; else $sF.=' AND nr BETWEEN "'.$sIdn1.'" AND "'.$sIdn2.'"';
 }
 if($sLog){$sF.=' AND(session LIKE "~%" AND session<"~'.$nTm.'" OR session<"'.$nTm.'")';}
 if($sSta1||$sSta2||$sSta3){$sF.=' AND('; if($sSta1) $sF.='aktiv="1"'; if($sSta2) $sF.=($sSta1?' OR ':'').'aktiv="0"'; if($sSta3) $sF.=($sSta1||$sSta2?' OR ':'').'aktiv="2"'; $sF.=')';}
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
 if($rR=$DbO->query('SELECT nr,session,aktiv,benutzer,passwort,email'.$s.' FROM '.MP_SqlTabN.$sF.' ORDER BY nr')){
  while($a=$rR->fetch_row()) $aTmp[]=$a; $rR->close();
 }else $Msg='<p class="admFehl">'.MP_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.MP_TxSqlVrbdg.'</p>';

if(!$nStart=(int)(isset($_GET['mp_Start'])?$_GET['mp_Start']:(isset($_POST['mp_Start'])?$_POST['mp_Start']:0))) $nStart=1; $nStop=$nStart+AM_NutzerLaenge;
reset($aTmp); $nSaetze=count($aTmp); $aD=array(); $k=0;
foreach($aTmp as $i=>$xx) /* if(++$k<$nStop&&$k>=$nStart) */ $aD[]=$aTmp[$i];

if(!$Msg) $Msg='<p class="admMeld">Benutzerexportliste'.($sQ?' (gefiltert)':'').'</p>';

//Scriptausgabe
?>
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $Msg?></td>
  <td align="right">
   [ <a href="nutzerSuche.php<?php echo ($sQ?'?'.substr($sQ,1):'')?>">Suche</a> ]
   [ <a href="nutzerListe.php<?php echo ($sQ?'?'.substr($sQ,1):'')?>">Liste</a> ]
  </td>
 </tr>
</table>

<?php

$sEx=''; $a=$aNF; $l=count($a);
$sZl=''; for($i=0;$i<$l;$i++) if($i!=3) $sZl.=$a[$i].';'; $sEx.=substr($sZl,0,-1)."\n";
foreach($aD as $a){ //Datenzeilen ausgeben
 $sZl=$a[0].';'.$a[2].';'.$a[3].';'.$a[5].';';
 for($i=5;$i<$l;$i++) $sZl.=$a[$i+1].';'; $sEx.=substr($sZl,0,-1)."\n";
}

$sExNa='nutzer_'.date('s').'.csv';
if($f=fopen(MP_Pfad.'temp/'.$sExNa,'w')){
 fwrite($f,$sEx); fclose($f);
 $Msg='<p class="admErfo">Die Benutzerdatei liegt unter <a href="'.$sHttp.'temp/'.$sExNa.'" target="hilfe" onclick="hlpWin(this.href);return false;" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.</p>';
}else $Msg='<p class="admFehl">'.str_replace('#','<i>temp/'.$sExNa.'</i>',MP_TxDateiRechte).'</p>';

echo '<div style="margin:5em;text-align:center">'.$Msg.'</div>';

echo fSeitenFuss();
?>