<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Termine suchen',(ADM_TCalPicker?'<link rel="stylesheet" type="text/css" href="'.$sHttp.'tcal.css" />
<script type="text/javascript" src="'.$sHttp.'tcal.js"></script>
<script type="text/javascript">
 A_TCALCONF.format='."'".fKalTCalFormat()."'".';
 A_TCALCONF.weekdays=['."'".implode("','",$kal_WochenTag)."'".'];
 A_TCALCONF.months=['."'".str_replace(';',"','",KAL_TxLMonate)."'".'];
 A_TCALCONF.prevmonth='."'".KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat."'".';
 A_TCALCONF.nextmonth='."'".KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat."'".';
 A_TCALCONF.prevyear='."'".KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr."'".';
 A_TCALCONF.nextyear='."'".KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr."'".';
 A_TCALCONF.yearscroll='.(KAL_TCalYrScroll?'true':'false').';
 A_TIMECONF.starttime='.sprintf('%.2f',KAL_TimeStart).';
 A_TIMECONF.stopptime='.sprintf('%.2f',KAL_TimeStopp).';
 A_TIMECONF.intervall='.sprintf('%.2f',KAL_TimeIvall).';
</script>':''),'TTs');

$nFelder=count($kal_FeldName);
if(!$Msg) $Msg='<p class="admMeld">Suchen Sie anhand folgender Kriterien nach Terminen.</p>';

//Scriptausgabe
echo $Msg.NL;
?>

<form action="liste.php" method="post">
<div align="center">
<table class="admTabl" align="center" border="0" cellpadding="2" cellspacing="1">
<?php
 $bZeigeOnline=(isset($_GET['kal_Onl'])?$_GET['kal_Onl']:ADM_ZeigeOnline);
 $bZeigeOffline=(isset($_GET['kal_Ofl'])?$_GET['kal_Ofl']:ADM_ZeigeOffline);
 $bZeigeVormerk=(isset($_GET['kal_Vmk'])?$_GET['kal_Vmk']:ADM_ZeigeVormerk);
 for($i=0,$bTesten=true;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i];
  if($t=='d'&&$i>1&&$bTesten&&KAL_EndeDatum){$bFldOK=false; $bTesten=false;} else $bFldOK=true; //2.Datum evt. auslassen
  if($t!='z'&&$t!='p'&&$bFldOK){ //Teile ausgeben
   if($t=='a'||$t=='k'||$t=='s'){ //Optionen bilden für Select-Box
    if(!isset($aTmp)) $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); //Kategorien holen
    $a=explode(';',(isset($aTmp[$i])?trim($aTmp[$i]):'')); array_shift($a);
    $sO='<option value=""></option>'; foreach($a as $s) $sO.='<option value="'.$s.'">'.$s.'</option>';
   }
?>
<tr class="admTabl">
 <td><?php echo $kal_FeldName[$i]?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='x') echo 'wie';
  elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'||$t=='j'||$t=='v') echo 'ist'?></td>
 <td><?php
  if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') echo '<input type="text" name="kal_'.$i.'F1" value="'.(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:'').'" style="width:150px;" />';
  elseif($t=='d') echo '<input type="text" class="kalTCal" name="kal_'.$i.'F1" value="'.(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:'').'" style="width:8em;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:'').'"'); echo '<select name="kal_'.$i.'F1" size="1" style="width:150px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='j'||$t=='v') echo '<input class="admCheck" type="checkbox" name="kal_'.$i.'F1" value="J" style="width:15px;"'.(isset($_GET['kal_'.$i.'F1'])&&$_GET['kal_'.$i.'F1']?' checked="checked"':'').' /> ja';
 ?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='j'||$t=='v'||$t=='x') echo 'oder';
  elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i') echo 'bis'?></td>
 <td><?php
  if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') echo '<input type="text" name="kal_'.$i.'F2" value="'.(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:'').'" style="width:140px;" />';
  elseif($t=='d') echo '<input type="text" class="kalTCal" name="kal_'.$i.'F2" value="'.(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:'').'" style="width:8em;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:'').'"'); echo '<select name="kal_'.$i.'F2" size="1" style="width:140px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='j'||$t=='v') echo '<input class="admCheck" type="checkbox" name="kal_'.$i.'F2" value="N" style="width:15px;"'.(isset($_GET['kal_'.$i.'F2'])&&$_GET['kal_'.$i.'F2']?' checked="checked"':'').' /> nein';
 ?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='x') echo 'aber nicht';
  elseif($t=='d'||$t=='@') echo 'Format'; else echo '&nbsp;'?></td>
 <td><?php
  if($t!='a'&&$t!='k'&&$t!='s'&&$t!='d'&&$t!='@'&&$t!='i'&&$t!='j'&&$t!='v'&&$t!='w'&&$t!='n'&&$t!='1'&&$t!='2'&&$t!='3'&&$t!='r') echo '<input type="text" name="kal_'.$i.'F3" value="'.(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:'').'" style="width:140px;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:'').'"'); echo '<select name="kal_'.$i.'F3" size="1" style="width:140px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='d'||$t=='@') echo fKalDatumsFormat();
  elseif($t=='i'&&!ADM_ZeigeAltes) echo '<input class="admCheck" type="checkbox" name="kal_Archiv" value="1" style="width:15px;"'.(isset($_GET['kal_Archiv'])&&$_GET['kal_Archiv']?' checked="checked"':'').' /> Archivsuche';
  else echo '&nbsp;'?></td>
</tr>
<?php }}?>
<tr class="admTabl">
 <td>Terminarten</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="kal_Onl" value="1"<?php if($bZeigeOnline) echo ' checked="checked"'?> /> online-Termine</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="kal_Ofl" value="1"<?php if($bZeigeOffline) echo ' checked="checked"'?> /> offline-Termine</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="kal_Vmk" value="1"<?php if($bZeigeVormerk) echo ' checked="checked"'?> /> Terminvorschläge</td>
</tr>
</table>
</div>
<?php if(file_exists('liste.php')){?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p>
<?php }?>
</form>

<?php
echo fSeitenFuss();

function fKalTCalFormat(){
 $s1='d'; $s2='m'; $s3='Y';
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3='d'; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2='d'; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
?>