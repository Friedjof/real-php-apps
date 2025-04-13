<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisse suchen','','EEs');

$bCh=false;
if($ENr1=(isset($_GET['enr1'])?$_GET['enr1']:'')) $bCh=true; if($ENr2=(isset($_GET['enr2'])?$_GET['enr2']:'')) $bCh=true;
if($Dat1=(isset($_GET['dat1'])?$_GET['dat1']:'')) $bCh=true; if($Dat2=(isset($_GET['dat2'])?$_GET['dat2']:'')) $bCh=true;
if($Flg1=(isset($_GET['flg1'])?$_GET['flg1']:'')) $bCh=true; if($Flg2=(isset($_GET['flg2'])?$_GET['flg2']:'')) $bCh=true; if($Flg3=(isset($_GET['flg3'])?$_GET['flg3']:'')) $bCh=true;
if($Vrb1=(isset($_GET['vrb1'])?$_GET['vrb1']:'')) $bCh=true; if($Vrb2=(isset($_GET['vrb2'])?$_GET['vrb2']:'')) $bCh=true; if($Vrb3=(isset($_GET['vrb3'])?$_GET['vrb3']:'')) $bCh=true;
if($Tln1=(isset($_GET['tln1'])?$_GET['tln1']:'')) $bCh=true; if($Tln2=(isset($_GET['tln2'])?$_GET['tln2']:'')) $bCh=true; if($Tln3=(isset($_GET['tln3'])?$_GET['tln3']:'')) $bCh=true;
if($BNr1=(isset($_GET['bnr1'])?$_GET['bnr1']:'')) $bCh=true; if($BNr2=(isset($_GET['bnr2'])?$_GET['bnr2']:'')) $bCh=true;
if($NTn=(isset($_GET['ntn'])?$_GET['ntn']:'')) $bCh=true; if($NBn=(isset($_GET['nbn'])?$_GET['nbn']:'')) $bCh=true;
echo '<p class="admMeld">'.(!$bCh?'Stellen Sie Ihre Suchanfrage zusammen!':'Verändern Sie Ihre Suchanfrage!').'</p>';
?>

<form name="fraEingabe" action="ergebnisListe.php" method="get">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Eintrag-Nummer ist bzw. ab<br /><input type="text" name="enr1" value="<?php echo $ENr1?>" style="width:8em;" /></td>
  <td style="width:34%">Eintrag-Nummer bis<br /><input type="text" name="enr2" value="<?php echo $ENr2?>" style="width:8em;" /></td>
  <td style="width:33%">&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Datum ist bzw. ab<br /><input type="text" name="dat1" value="<?php echo $Dat1?>" style="width:8em;" /> (TT.MM.JJ)</td>
  <td style="width:33%">Datum bis<br /><input type="text" name="dat2" value="<?php echo $Dat2?>" style="width:8em;" /> (TT.MM.JJ)</td>
  <td style="width:33%">&nbsp;</td>
 </tr><tr class="admTabl">
  <td style="width:34%">Testfolgenname wie <input type="text" name="flg1" value="<?php echo $Flg1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="flg2" value="<?php echo $Flg2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="flg3" value="<?php echo $Flg3?>" style="width:99%;" /></td>
 </tr><?php if(FRA_DatVerbalL) {?><tr class="admTabl">
  <td style="width:34%">verbale Bewertung wie <input type="text" name="vrb1" value="<?php echo $Vrb1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="vrb2" value="<?php echo $Vrb2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="vrb3" value="<?php echo $Vrb3?>" style="width:99%;" /></td>
 </tr><?php }?><tr class="admTabl">
  <td style="width:34%">Teilnehmer/Benutzer wie <input type="text" name="tln1" value="<?php echo $Tln1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="tln2" value="<?php echo $Tln2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="tln3" value="<?php echo $Tln3?>" style="width:99%;" /></td>
 </tr><tr class="admTabl">
  <td style="width:34%">Benutzer-Nummer ist <input type="text" name="bnr1" value="<?php echo $BNr1?>" style="width:99%;" /></td>
  <td style="width:33%">oder ist <input type="text" name="bnr2" value="<?php echo $BNr2?>" style="width:99%;" /></td>
  <td style="width:33%" style="vertical-align:bottom;"><input type="checkbox" class="admCheck" name="ntn" value="1"<?php if($NTn=='1') echo ' checked="checked"'?>> nur Ergebnisse von Teilnehmern<br /><input type="checkbox" class="admCheck" name="nbn" value="1"<?php if($NBn=='1') echo ' checked="checked"'?>> nur Ergebnisse von Benutzern</td>
 </tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p>
</div>
</form>

<?php echo fSeitenFuss();?>