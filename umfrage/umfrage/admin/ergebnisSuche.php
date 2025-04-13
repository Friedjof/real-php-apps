<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnissuche','','EEs');

$bCh=false;
if($FNr1=(isset($_GET['fnr1'])?$_GET['fnr1']:'')) $bCh=true; if($FNr2=(isset($_GET['fnr2'])?$_GET['fnr2']:'')) $bCh=true; $Onl=(isset($_GET['onl'])?$_GET['onl']:''); if(strlen($Onl)>0) $bCh=true;
if($Ufr1=(isset($_GET['ufr1'])?$_GET['ufr1']:'')) $bCh=true; if($Ufr2=(isset($_GET['ufr2'])?$_GET['ufr2']:'')) $bCh=true; if($Ufr3=(isset($_GET['ufr3'])?$_GET['ufr3']:'')) $bCh=true;
if($Frg1=(isset($_GET['frg1'])?$_GET['frg1']:'')) $bCh=true; if($Frg2=(isset($_GET['frg2'])?$_GET['frg2']:'')) $bCh=true; if($Frg3=(isset($_GET['frg3'])?$_GET['frg3']:'')) $bCh=true;
if($Bem1=(isset($_GET['bem1'])?$_GET['bem1']:'')) $bCh=true; if($Bem2=(isset($_GET['bem2'])?$_GET['bem2']:'')) $bCh=true; if($Bem3=(isset($_GET['bem3'])?$_GET['bem3']:'')) $bCh=true;
if($B2m1=(isset($_GET['b2m1'])?$_GET['b2m1']:'')) $bCh=true; if($B2m2=(isset($_GET['b2m2'])?$_GET['b2m2']:'')) $bCh=true; if($B2m3=(isset($_GET['b2m3'])?$_GET['b2m3']:'')) $bCh=true;
echo '<p class="admMeld">'.(!$bCh?'Stellen Sie Ihre Suchanfrage für Ergebnisse zusammen!':'Verändern Sie Ihre Suchanfrage für Ergebnisse!').'</p>';
$sOptU=''; for($i=1;$i<=26;$i++) $sOptU.='<option value="'.chr($i+64).'">Umfrage '.chr($i+64).'</option>';
?>

<form name="umfListe" action="ergebnisListe.php" method="get">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Frage-Nummer <input type="text" name="fnr1" value="<?php echo $FNr1?>" style="width:4em;" /> bis <input type="text" name="fnr2" value="<?php echo $FNr2?>" style="width:4em;" /></td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="onl1" value="1"<?php if($Onl=='1') echo ' checked="checked"'?>> nur aktivierte Fragen</td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="onl2" value="0"<?php if($Onl=='0') echo ' checked="checked"'?>> nur deaktivierte Fragen</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Umfrage <select name="ufr1" size="1" style="width:auto"><option value=""></option><?php echo ($Ufr1?str_replace('value="'.$Ufr1.'"','value="'.$Ufr1.'" selected="selected"',$sOptU):$sOptU)?></select></td>
  <td style="width:33%">oder <select name="ufr2" size="1" style="width:auto"><option value=""></option><?php echo ($Ufr2?str_replace('value="'.$Ufr2.'"','value="'.$Ufr2.'" selected="selected"',$sOptU):$sOptU)?></select> </td>
  <td style="width:33%">aber nicht <select name="ufr3" size="1" style="width:auto"><option value=""></option><?php echo ($Ufr3?str_replace('value="'.$Ufr3.'"','value="'.$Ufr3.'" selected="selected"',$sOptU):$sOptU)?></select></td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Fragetext wie <input type="text" name="frg1" value="<?php echo $Frg1?>" style="width:97%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="frg2" value="<?php echo $Frg2?>" style="width:97%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="frg3" value="<?php echo $Frg3?>" style="width:97%;" /></td>
 </tr><tr class="admTabl">
  <td style="width:34%">Anmerkung-1 wie <input type="text" name="bem1" value="<?php echo $Bem1?>" style="width:97%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="bem2" value="<?php echo $Bem2?>" style="width:97%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="bem3" value="<?php echo $Bem3?>" style="width:97%;" /></td>
 </tr><tr class="admTabl">
  <td style="width:34%">Anmerkung-2 wie <input type="text" name="b2m1" value="<?php echo $B2m1?>" style="width:97%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="b2m2" value="<?php echo $B2m2?>" style="width:97%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="b2m3" value="<?php echo $B2m3?>" style="width:97%;" /></td>
 </tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Suchen"></p>
</form>

<?php echo fSeitenFuss();?>