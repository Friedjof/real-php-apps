<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Fragen suchen','','FFs');

$bCh=false;
if($FNr=(isset($_GET['fnr'])?$_GET['fnr']:'')) $bCh=true; $Onl=(isset($_GET['onl'])?$_GET['onl']:''); $Vst=(isset($_GET['vst'])?$_GET['vst']:''); if(strlen($Onl)>0||strlen($Vst)>0) $bCh=true;
if($Frg1=(isset($_GET['frg1'])?$_GET['frg1']:'')) $bCh=true; if($Frg2=(isset($_GET['frg2'])?$_GET['frg2']:'')) $bCh=true; if($Frg3=(isset($_GET['frg3'])?$_GET['frg3']:'')) $bCh=true;
if($Kat1=(isset($_GET['kat1'])?$_GET['kat1']:'')) $bCh=true; if($Kat2=(isset($_GET['kat2'])?$_GET['kat2']:'')) $bCh=true; if($Kat3=(isset($_GET['kat3'])?$_GET['kat3']:'')) $bCh=true;
if($Bem1=(isset($_GET['bem1'])?$_GET['bem1']:'')) $bCh=true; if($Bem2=(isset($_GET['bem2'])?$_GET['bem2']:'')) $bCh=true; if($Bem3=(isset($_GET['bem3'])?$_GET['bem3']:'')) $bCh=true;
if($B2m1=(isset($_GET['b2m1'])?$_GET['b2m1']:'')) $bCh=true; if($B2m2=(isset($_GET['b2m2'])?$_GET['b2m2']:'')) $bCh=true; if($B2m3=(isset($_GET['b2m3'])?$_GET['b2m3']:'')) $bCh=true;
echo '<p class="admMeld">'.(!$bCh?'Stellen Sie Ihre Suchanfrage zusammen!':'Verändern Sie Ihre Suchanfrage!').'</p>';
?>

<form name="fraEingabe" action="liste.php" method="get">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Frage-Nummer<br /><input type="text" name="fnr" value="<?php echo $FNr?>" style="width:4em;" /></td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="onl1" value="1"<?php if($Onl=='1') echo ' checked="checked"'?>> nur aktivierte Fragen<br /><input type="checkbox" class="admCheck" name="onl2" value="0"<?php if($Onl=='0') echo ' checked="checked"'?>> nur deaktivierte Fragen</td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="vst1" value="1"<?php if($Vst=='1') echo ' checked="checked"'?>> nur versteckte Fragen<br /><input type="checkbox" class="admCheck" name="vst2" value="0"<?php if($Vst=='0') echo ' checked="checked"'?>> nur öffentliche Fragen</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Fragetext wie <input type="text" name="frg1" value="<?php echo $Frg1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="frg2" value="<?php echo $Frg2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="frg3" value="<?php echo $Frg3?>" style="width:99%;" /></td>
 </tr><?php if(FRA_Kategorien>''){ ?>
 <tr class="admTabl">
  <td style="width:34%">Kategorie wie <select name="kat1" size="1" style="width:99%;"><option value=""></option><?php $aKat=explode(';',FRA_Kategorien); foreach($aKat as $v=>$k) if(!empty($k)){$k=str_replace('`,',';',$k); echo '<option value="'.$k.($k!=$Kat1?'':'" selected="selected').'">'.$k.'</option>';}?></select></td>
  <td style="width:33%">oder wie <input type="text" name="kat2" value="<?php echo $Kat2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="kat3" value="<?php echo $Kat3?>" style="width:99%;" /></td>
 </tr><?php }?>
 <tr class="admTabl">
  <td style="width:34%">Anmerkung wie <input type="text" name="bem1" value="<?php echo $Bem1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="bem2" value="<?php echo $Bem2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="bem3" value="<?php echo $Bem3?>" style="width:99%;" /></td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Anmerkung-2 wie <input type="text" name="b2m1" value="<?php echo $B2m1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="b2m2" value="<?php echo $B2m2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="b2m3" value="<?php echo $B2m3?>" style="width:99%;" /></td>
 </tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p>
</div>
</form>

<?php echo fSeitenFuss();?>