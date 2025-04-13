<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzer suchen','','NNs');

echo '<p class="admMeld">Suchen Sie anhand folgender Kriterien nach Benutzern.</p>';

$nFelder=count($kal_NutzerFelder); $aF1=array(); $aF2=array(); $aF3=array();
$sIdn1=(isset($_GET['Idn1'])?(int)$_GET['Idn1']:''); $sIdn2=(isset($_GET['Idn2'])?(int)$_GET['Idn2']:'');
$sSta1=(isset($_GET['Sta1'])&&$_GET['Sta1']?'1':''); $sSta2=(isset($_GET['Sta2'])&&$_GET['Sta2']?'1':'');
$sUsr1=(isset($_GET['Usr1'])?$_GET['Usr1']:''); $sUsr2=(isset($_GET['Usr2'])?$_GET['Usr2']:''); $sUsr3=(isset($_GET['Usr3'])?$_GET['Usr3']:'');
$sEml1=(isset($_GET['Eml1'])?$_GET['Eml1']:''); $sEml2=(isset($_GET['Eml2'])?$_GET['Eml2']:''); $sEml3=(isset($_GET['Eml3'])?$_GET['Eml3']:'');
for($i=6;$i<$nFelder;$i++){
 $aF1[$i]=(isset($_GET['F1'.$i])?$_GET['F1'.$i]:'');
 $aF2[$i]=(isset($_GET['F2'.$i])?$_GET['F2'.$i]:'');
 $aF3[$i]=(isset($_GET['F3'.$i])?$_GET['F3'.$i]:'');
}
?>

<form action="nutzerListe.php" method="post">
<input type="hidden" name="Suche" value="1" />
<div align="center">
<table class="admTabl" align="center" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td><?php echo $kal_NutzerFelder[0]?></td>
 <td><input style="width:6em" type="text" name="Idn1" value="<?php echo $sIdn1?>" /></td>
 <td align="center">bis</td>
 <td><input style="width:6em" type="text" name="Idn2" value="<?php echo $sIdn2?>" /></td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
<tr class="admTabl">
 <td><?php echo $kal_NutzerFelder[3]?></td>
 <td><input style="width:12em" type="text" name="Usr1" value="<?php echo $sUsr1?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="Usr2" value="<?php echo $sUsr2?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="Usr3" value="<?php echo $sUsr3?>" /></td>
</tr>
<tr class="admTabl">
 <td><?php echo $kal_NutzerFelder[5]?></td>
 <td><input style="width:12em" type="text" name="Eml1" value="<?php echo $sEml1?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="Eml2" value="<?php echo $sEml2?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="Eml3" value="<?php echo $sEml3?>" /></td>
</tr>
<?php for($i=6;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td><?php echo $kal_NutzerFelder[$i]?></td>
 <td><input style="width:12em" type="text" name="F1<?php echo $i?>" value="<?php echo $aF1[$i]?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="F2<?php echo $i?>" value="<?php echo $aF2[$i]?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="F3<?php echo $i?>" value="<?php echo $aF3[$i]?>" /></td>
</tr>
<?php }?>
<tr class="admTabl">
 <td><?php echo $kal_NutzerFelder[2]?></td>
 <td><input class="admCheck" type="checkbox" name="Sta1" value="1"<?php if($sSta1) echo ' checked="checked"'?> /> aktive &nbsp; <input class="admCheck" type="checkbox" name="Sta2" value="1"<?php if($sSta2) echo ' checked="checked"'?> /> inaktive</td>
 <td align="center">&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
</table>
</div>
<?php if(file_exists('nutzerListe.php')){?>
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