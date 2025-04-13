<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Teilnahmesuche','','ETl');

$bCh=false;
if($FNr1=(isset($_GET['fnr1'])?$_GET['fnr1']:'')) $bCh=true; if($FNr2=(isset($_GET['fnr2'])?$_GET['fnr2']:'')) $bCh=true;
if($Dat1=fDispDat(isset($_GET['dat1'])?$_GET['dat1']:'')) $bCh=true; if($Dat2=fDispDat(isset($_GET['dat2'])?$_GET['dat2']:'')) $bCh=true;
$Sta1=(isset($_GET['sta1'])?$_GET['sta1']:''); if(strlen($Sta1)) $bCh=true; $Sta2=(isset($_GET['sta2'])?$_GET['sta2']:''); if(strlen($Sta2)) $bCh=true; $Sta3=(isset($_GET['sta3'])?$_GET['sta3']:''); if(strlen($Sta3)) $bCh=true;
if($Art1=(isset($_GET['art1'])?$_GET['art1']:'')) $bCh=true; if($Art2=(isset($_GET['art2'])?$_GET['art2']:'')) $bCh=true; if($Art3=(isset($_GET['art3'])?$_GET['art3']:'')) $bCh=true;
if($Ntz1=(isset($_GET['ntz1'])?$_GET['ntz1']:'')) $bCh=true; if($Ntz2=(isset($_GET['ntz2'])?$_GET['ntz2']:'')) $bCh=true; if($Ntz3=(isset($_GET['ntz3'])?$_GET['ntz3']:'')) $bCh=true;
echo fMMeld(!$bCh?'Stellen Sie Ihre Suchanfrage für Teilnahme zusammen!':'Verändern Sie Ihre Suchanfrage für Teilnahme!');
$sOptA='<option value="G">Gast</option><option value="T">Teilnehmer</option><option value="N">Benutzer</option>';
$sOptS='<option value="0">ignoriert</option><option value="1">eingetragen</option>';
?>

<form name="umfListe" action="teilnahmeListe.php" method="get">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Eintrag-Nummer<br /> <input type="text" name="fnr1" value="<?php echo $FNr1?>" style="width:4em;" /></td>
  <td style="width:33%">bis<br /> <input type="text" name="fnr2" value="<?php echo $FNr2?>" style="width:4em;" /></td>
  <td style="width:33%">&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Datum<br /> <input type="text" name="dat1" value="<?php echo $Dat1?>" style="width:8em;" /> Format: TT.MM.JJJJ</td>
  <td style="width:33%">bis<br /> <input type="text" name="dat2" value="<?php echo $Dat2?>" style="width:8em;" /> Format: TT.MM.JJJJ</td>
  <td style="width:33%">&nbsp;</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Status<br /> <select name="sta1" size="1" style="width:auto"><option value=""></option><?php echo (strlen($Sta1)?str_replace('value="'.$Sta1.'"','value="'.$Sta1.'" selected="selected"',$sOptS):$sOptS)?></select></td>
  <td style="width:33%">oder<br /> <select name="sta2" size="1" style="width:auto"><option value=""></option><?php echo (strlen($Sta2)?str_replace('value="'.$Sta2.'"','value="'.$Sta2.'" selected="selected"',$sOptS):$sOptS)?></select> </td>
  <td style="width:33%">aber nicht<br /> <select name="sta3" size="1" style="width:auto"><option value=""></option><?php echo (strlen($Sta3)?str_replace('value="'.$Sta3.'"','value="'.$Sta3.'" selected="selected"',$sOptS):$sOptS)?></select></td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Art<br /> <select name="art1" size="1" style="width:auto"><option value=""></option><?php echo ($Art1?str_replace('value="'.$Art1.'"','value="'.$Art1.'" selected="selected"',$sOptA):$sOptA)?></select></td>
  <td style="width:33%">oder<br /> <select name="art2" size="1" style="width:auto"><option value=""></option><?php echo ($Art2?str_replace('value="'.$Art2.'"','value="'.$Art2.'" selected="selected"',$sOptA):$sOptA)?></select> </td>
  <td style="width:33%">aber nicht<br /> <select name="art3" size="1" style="width:auto"><option value=""></option><?php echo ($Art3?str_replace('value="'.$Art3.'"','value="'.$Art3.'" selected="selected"',$sOptA):$sOptA)?></select></td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Nutzer wie<br /> <input type="text" name="ntz1" value="<?php echo $Ntz1?>" style="width:97%;" /></td>
  <td style="width:33%">oder wie<br /> <input type="text" name="ntz2" value="<?php echo $Ntz2?>" style="width:97%;" /></td>
  <td style="width:33%">aber nicht wie<br /> <input type="text" name="ntz3" value="<?php echo $Ntz3?>" style="width:97%;" /></td>
 </tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Suchen"></p>
</form>

<?php
echo fSeitenFuss();

function fDispDat($s){
 if($s) $s=substr($s,8,2).'.'.substr($s,5,2).'.'.substr($s,0,4);
 return $s;
}
?>