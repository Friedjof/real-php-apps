<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzer suchen','','NNl');

echo '<p class="admMeld">Suchen Sie anhand folgender Kriterien nach Benutzern.</p>';
$aNF=explode(';',MP_NutzerFelder); //array_splice($aNF,1,1);
$nFelder=count($aNF); $aF1=array(); $aF2=array(); $aF3=array();
$sIdn1=(isset($_GET['Idn1'])?(int)$_GET['Idn1']:''); $sIdn2=(isset($_GET['Idn2'])?(int)$_GET['Idn2']:''); $sLog=(isset($_GET['Log'])?(int)$_GET['Log']:'');
$sSta1=(isset($_GET['Sta1'])&&$_GET['Sta1']?'1':''); $sSta2=(isset($_GET['Sta2'])&&$_GET['Sta2']?'1':''); $sSta3=(isset($_GET['Sta3'])&&$_GET['Sta3']?'1':'');
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
 <td><?php echo $aNF[0]?></td>
 <td><input style="width:6em" type="text" name="Idn1" value="<?php echo $sIdn1?>" /></td>
 <td align="center">bis</td>
 <td><input style="width:6em" type="text" name="Idn2" value="<?php echo $sIdn2?>" /></td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
<tr class="admTabl">
 <td>Login älter als</td>
 <td><input style="width:6em" type="text" name="Log" value="<?php echo $sLog?>" /> Tage</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
<tr class="admTabl">
 <td><?php echo $aNF[3]?></td>
 <td><input style="width:12em" type="text" name="Usr1" value="<?php echo $sUsr1?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="Usr2" value="<?php echo $sUsr2?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="Usr3" value="<?php echo $sUsr3?>" /></td>
</tr>
<tr class="admTabl">
 <td><?php echo $aNF[5]?></td>
 <td><input style="width:12em" type="text" name="Eml1" value="<?php echo $sEml1?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="Eml2" value="<?php echo $sEml2?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="Eml3" value="<?php echo $sEml3?>" /></td>
</tr>
<?php for($i=6;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td><?php echo str_replace('`,',';',$aNF[$i])?></td>
 <td><input style="width:12em" type="text" name="F1<?php echo $i?>" value="<?php echo $aF1[$i]?>" /></td>
 <td align="center">oder</td>
 <td><input style="width:12em" type="text" name="F2<?php echo $i?>" value="<?php echo $aF2[$i]?>" /></td>
 <td align="center">aber nicht</td>
 <td><input style="width:12em" type="text" name="F3<?php echo $i?>" value="<?php echo $aF3[$i]?>" /></td>
</tr>
<?php }?>
<tr class="admTabl">
 <td><?php echo $aNF[2]?></td>
 <td><input class="admCheck" type="checkbox" name="Sta1" value="1"<?php if($sSta1) echo ' checked="checked"'?> /> aktive &nbsp; <input class="admCheck" type="checkbox" name="Sta2" value="1"<?php if($sSta2) echo ' checked="checked"'?> /> inaktive</td>
 <td>&nbsp;</td>
 <td><input class="admCheck" type="checkbox" name="Sta3" value="1"<?php if($sSta3) echo ' checked="checked"'?> /> freizuschaltende</td>
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
?>