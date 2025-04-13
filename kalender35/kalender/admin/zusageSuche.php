<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusagen suchen','','ZZl');

$nFelder=count($kal_FeldName);
if(!$Msg) $Msg='<p class="admMeld">Suchen Sie anhand folgender Kriterien nach Zusagen.</p>';

//Scriptausgabe
echo $Msg.NL;

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
$bZeigeOnline=(isset($_GET['kal_Onl'])?$_GET['kal_Onl']:false);
$bZeigeOffline=(isset($_GET['kal_Ofl'])?$_GET['kal_Ofl']:false);
$bZeigeBestaet=(isset($_GET['kal_Bst'])?$_GET['kal_Bst']:false);
$bZeigeVormerk=(isset($_GET['kal_Vmk'])?$_GET['kal_Vmk']:false);

$bZeigeAkt=(isset($_GET['kal_Akt'])?$_GET['kal_Akt']:KAL_ZusageAdmLstKommend);
$bZeigeAlt=(isset($_GET['kal_Alt'])?$_GET['kal_Alt']:KAL_ZusageAdmLstVorbei);
if(!$bZeigeAlt) $bZeigeAkt=true;
?>

<form action="zusageListe.php" method="post">
<div align="center">
<table class="admTabl" align="center" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td>Zusagen-Nr.</td>
 <td align="center">ist</td>
 <td><input type="text" name="kal_0F1" value="<?php echo(isset($_GET['kal_0F1'])?$_GET['kal_0F1']:'')?>" style="width:150px;" /></td>
 <td align="center">bis</td>
 <td><input type="text" name="kal_0F2" value="<?php echo(isset($_GET['kal_0F2'])?$_GET['kal_0F2']:'')?>" style="width:140px;" /></td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr><tr class="admTabl">
 <td>Termin-Nr.</td>
 <td align="center">ist</td>
 <td><input type="text" name="kal_1F1" value="<?php echo(isset($_GET['kal_1F1'])?$_GET['kal_1F1']:'')?>" style="width:150px;" /></td>
 <td align="center">bis</td>
 <td><input type="text" name="kal_1F2" value="<?php echo(isset($_GET['kal_1F2'])?$_GET['kal_1F2']:'')?>" style="width:140px;" /></td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr><tr class="admTabl">
 <td>Datum</td>
 <td align="center">ist</td>
 <td><input type="text" name="kal_2F1" value="<?php echo(isset($_GET['kal_2F1'])?$_GET['kal_2F1']:'')?>" style="width:150px;" /></td>
 <td align="center">bis</td>
 <td><input type="text" name="kal_2F2" value="<?php echo(isset($_GET['kal_2F2'])?$_GET['kal_2F2']:'')?>" style="width:140px;" /></td>
 <td align="center">Format</td>
 <td><?php echo fKalDatumsFormat()?></td>
</tr><tr class="admTabl">
 <td>Buchung</td>
 <td align="center">ist</td>
 <td><input type="text" name="kal_5F1" value="<?php echo(isset($_GET['kal_5F1'])?$_GET['kal_5F1']:'')?>" style="width:150px;" /></td>
 <td align="center">bis</td>
 <td><input type="text" name="kal_5F2" value="<?php echo(isset($_GET['kal_5F2'])?$_GET['kal_5F2']:'')?>" style="width:140px;" /></td>
 <td align="center">Format</td>
 <td><?php echo fKalDatumsFormat()?></td>
</tr><tr class="admTabl">
 <td>Status</td>
 <td>&nbsp;</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="kal_Onl" value="1"<?php if($bZeigeOnline) echo ' checked="checked"'?> /> <img src="<?php echo $sHttp?>grafik/punktGrn.gif" title="gültig" width="12" height="12" border="0">gültige</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="kal_Ofl" value="1"<?php if($bZeigeOffline) echo ' checked="checked"'?> /> <img src="<?php echo $sHttp?>grafik/punktRot.gif" title="vorgemerkt" width="12" height="12" border="0">unbestätigte &nbsp; <input type="checkbox" class="admRadio" name="kal_Bst" value="1"<?php if($bZeigeBestaet) echo ' checked="checked"'?> /> <img src="<?php echo $sHttp?>grafik/punktRtGn.gif" title="bestätigt" width="12" height="12" border="0">teilbestätigte</td>
 <td><input type="checkbox" class="admRadio" name="kal_Vmk" value="1"<?php if($bZeigeVormerk) echo ' checked="checked"'?> /> <img src="<?php echo $sHttp?>grafik/punktGlb.gif" title="Warteliste" width="12" height="12" border="0">auf der Warteliste</td>
</tr><tr class="admTabl">
 <td>Veranstaltung</td>
 <td align="center">wie</td>
 <td><input type="text" name="kal_4F1" value="<?php echo(isset($_GET['kal_4F1'])?$_GET['kal_4F1']:'')?>" style="width:150px;" /></td>
 <td align="center">oder</td>
 <td><input type="text" name="kal_4F2" value="<?php echo(isset($_GET['kal_4F2'])?$_GET['kal_4F2']:'')?>" style="width:140px;" /></td>
 <td align="center">aber nicht</td>
 <td><input type="text" name="kal_4F3" value="<?php echo(isset($_GET['kal_4F3'])?$_GET['kal_4F3']:'')?>" style="width:140px;" /></td>
</tr><tr class="admTabl">
 <td>E-Mail</td>
 <td align="center">wie</td>
 <td><input type="text" name="kal_8F1" value="<?php echo(isset($_GET['kal_8F1'])?$_GET['kal_8F1']:'')?>" style="width:150px;" /></td>
 <td align="center">oder</td>
 <td><input type="text" name="kal_8F2" value="<?php echo(isset($_GET['kal_8F2'])?$_GET['kal_8F2']:'')?>" style="width:140px;" /></td>
 <td align="center">aber nicht</td>
 <td><input type="text" name="kal_8F3" value="<?php echo(isset($_GET['kal_8F3'])?$_GET['kal_8F3']:'')?>" style="width:140px;" /></td>
</tr>

<?php for($i=9;$i<=$nZusageFelder;$i++){?>
<tr class="admTabl">
 <td><?php echo str_replace('`,',';',$kal_ZusageFelder[$i])?></td>
 <td align="center">wie</td>
 <td><input type="text" name="kal_<?php echo $i?>F1" value="<?php echo(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:'')?>" style="width:150px;" /></td>
 <td align="center">oder</td>
 <td><input type="text" name="kal_<?php echo $i?>F2" value="<?php echo(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:'')?>" style="width:140px;" /></td>
 <td align="center">aber nicht</td>
 <td><input type="text" name="kal_<?php echo $i?>F3" value="<?php echo(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:'')?>" style="width:140px;" /></td>
</tr>
<?php }?>

<tr class="admTabl">
 <td>betrifft</td>
 <td>&nbsp;</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="kal_Akt" value="1"<?php if($bZeigeAkt) echo ' checked="checked"'?> /> künfige Termine</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="kal_Alt" value="1"<?php if($bZeigeAlt) echo ' checked="checked"'?> /> abgelaufene Termine</td>
 <td>&nbsp;</td>
</tr>
</table>
</div>
<?php if(file_exists('zusageListe.php')){?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p>
<?php }?>
</form>

<?php echo fSeitenFuss()?>