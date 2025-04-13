<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserat suchen','','IIs');

$aStru=array();
if($nSegNo!=0){ //Segment gewählt

 $bZeigeOnline=(isset($_GET['mp_Onl'])?$_GET['mp_Onl']:AM_ZeigeOnline);
 $bZeigeOffline=(isset($_GET['mp_Ofl'])?$_GET['mp_Ofl']:AM_ZeigeOffline);
 $bZeigeVormerk=(isset($_GET['mp_Vmk'])?$_GET['mp_Vmk']:AM_ZeigeVormerk);
 $aStru=array(); $aFN=array(); $aFT=array(); $aAW=array(); $aKW=array(); $aSW=array(); $nFelder=0;
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
 if(!$Meld){$Meld='Suchen Sie anhand folgender Kriterien nach Inseraten.'; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;
?>

<form action="liste.php?seg=<?php echo $nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 for($i=0;$i<$nFelder;$i++){
  $t=$aFT[$i];
  if($t!='z'&&$t!='p'){ //Teile ausgeben
   if($t=='a'||$t=='k'||$t=='s'){ //Optionen bilden für Select-Box
    if($t=='a') $a=explode('|','|'.$aAW[$i]); elseif($t=='k') $a=$aKW; elseif($t=='s') $a=$aSW; else $a=array();
    $sO=''; foreach($a as $s) $sO.='<option value="'.$s.'">'.$s.'</option>';
   }
?>
<tr class="admTabl">
 <td><?php echo $aFN[$i]?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='x') echo 'wie';
  elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'||$t=='j'||$t=='v') echo 'ist'?></td>
 <td><?php
  if($t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') echo '<input type="text" name="mp_'.$i.'F1" value="'.(isset($_GET['mp_'.$i.'F1'])?$_GET['mp_'.$i.'F1']:'').'" style="width:140px;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.(isset($_GET['mp_'.$i.'F1'])?$_GET['mp_'.$i.'F1']:'').'"'); echo '<select name="mp_'.$i.'F1" size="1" style="width:140px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='j'||$t=='v') echo '<input type="checkbox" name="mp_'.$i.'F1" value="J" style="width:15px;"'.(isset($_GET['mp_'.$i.'F1'])&&$_GET['mp_'.$i.'F1']?' checked="checked"':'').' /> ja';
 ?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='j'||$t=='v'||$t=='x') echo 'oder';
  elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i') echo 'bis'?></td>
 <td><?php
  if($t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') echo '<input type="text" name="mp_'.$i.'F2" value="'.(isset($_GET['mp_'.$i.'F2'])?$_GET['mp_'.$i.'F2']:'').'" style="width:140px;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=(isset($_GET['mp_'.$i.'F2'])?strpos($sO,'value="'.$_GET['mp_'.$i.'F2'].'"'):0); echo '<select name="mp_'.$i.'F2" size="1" style="width:140px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='j'||$t=='v') echo '<input type="checkbox" name="mp_'.$i.'F2" value="N" style="width:15px;"'.(isset($_GET['mp_'.$i.'F2'])&&$_GET['mp_'.$i.'F2']?' checked="checked"':'').' /> nein';
 ?></td>
 <td align="center"><?php
  if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='o'||$t=='u'||$t=='x') echo 'aber nicht';
  elseif($t=='d'||$t=='@') echo 'Format'; else echo '&nbsp;'?></td>
 <td><?php
  if($t!='a'&&$t!='k'&&$t!='s'&&$t!='d'&&$t!='@'&&$t!='i'&&$t!='j'&&$t!='v'&&$t!='w'&&$t!='n'&&$t!='1'&&$t!='2'&&$t!='3'&&$t!='r') echo '<input type="text" name="mp_'.$i.'F3" value="'.(isset($_GET['mp_'.$i.'F3'])?$_GET['mp_'.$i.'F3']:'').'" style="width:140px;" />';
  elseif($t=='a'||$t=='k'||$t=='s'){$j=(isset($_GET['mp_'.$i.'F3'])?strpos($sO,'value="'.$_GET['mp_'.$i.'F3'].'"'):0); echo '<select name="mp_'.$i.'F3" size="1" style="width:140px;">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
  elseif($t=='d'||$t=='@') echo fMpDatumsFormat();
  elseif($t=='i'&&!AM_ZeigeAltes) echo '<input class="admCheck" type="checkbox" name="mp_Archiv" value="1" style="width:15px;"'.(isset($_GET['mp_Archiv'])&&$_GET['mp_Archiv']?' checked="checked"':'').' /> Archivsuche';
  else echo '&nbsp;'?></td>
</tr>
<?php }}?>
<tr class="admTabl">
 <td>Inseratearten</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="mp_Onl" value="1"<?php if($bZeigeOnline) echo ' checked="checked"'?> /> online-Inserate</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="mp_Ofl" value="1"<?php if($bZeigeOffline) echo ' checked="checked"'?> /> offline-Inserate</td>
 <td>&nbsp;</td>
 <td><input type="checkbox" class="admRadio" name="mp_Vmk" value="1"<?php if($bZeigeVormerk) echo ' checked="checked"'?> />Inseratevorschläge</td>
</tr>
</table>
<?php if(file_exists('liste.php')){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p><?php }?>
</form>

<?php
}else echo '<p class="admMeld">Im leeren Muster-Segment gibt es keine Inserate. Bitte wählen Sie zuerst ein Segment.</p>';

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aSF,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}
?>