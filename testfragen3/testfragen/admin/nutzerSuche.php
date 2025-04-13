<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzer suchen','<script language="JavaScript" type="text/javascript">
function fSetAktion(){
 if(document.SuchForm.rbForm[0].checked) document.SuchForm.action="nutzerListe.php";
 if(document.SuchForm.rbForm[1].checked) document.SuchForm.action="nutzerZuweisung.php";
 return true;
}
</script>','NNS');

$sMeld='';
$aFlds=explode(';',FRA_NutzerFelder); $nFlds=count($aFlds); for($i=2;$i<$nFlds;$i++) $aFlds[$i]=str_replace('`,',';',$aFlds[$i]);

if(!$sMeld) $sMeld='<p class="admMeld">Stellen Sie Ihre Suchanfrage zusammen!</p>';
echo $sMeld;

$Nr1=(isset($_GET['nr1'])?(int)$_GET['nr1']:''); $Nr2=(isset($_GET['nr2'])?(int)$_GET['nr2']:'');
$Onl=(isset($_GET['onl'])?$_GET['onl']:'');
$N12=(isset($_GET['n12'])?$_GET['n12']:''); $N22=(isset($_GET['n22'])?$_GET['n22']:''); $N32=(isset($_GET['n32'])?$_GET['n32']:'');
?>

<form name="SuchForm" action="nutzerListe.php" onsubmit="return fSetAktion();" method="post">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td style="width:34%">Benutzer-Nummer ist oder ab<br /><input type="text" name="nr1" value="<?php echo $Nr1?>" style="width:5em;" /></td>
  <td style="width:33%">bis<br /><input type="text" name="nr2" value="<?php echo $Nr2?>" style="width:5em;" /> </td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="onl" value="1"<?php if($Onl=='1') echo ' checked="checked"'?>> nur aktivierte Benutzer<br /><input type="checkbox" class="admCheck" name="onl" value="0"<?php if($Onl=='0') echo ' checked="checked"'?>> nur deaktivierte Benutzer</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Benutzername wie <input type="text" name="n12" value="<?php echo $N12?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="n22" value="<?php echo $N22?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="n32" value="<?php echo $N32?>" style="width:99%;" /></td>
 </tr>
<?php
 for($i=4;$i<$nFlds;$i++){
 $N1=(isset($_GET['n1'.$i])?$_GET['n1'.$i]:''); $N2=(isset($_GET['n2'.$i])?$_GET['n2'.$i]:''); $N3=(isset($_GET['n3'.$i])?$_GET['n3'.$i]:'');
 echo '
 <tr class="admTabl">
  <td style="width:34%">'.($aFlds[$i]!='GUELTIG_BIS'?$aFlds[$i]:(FRA_TxNutzerFrist>''?FRA_TxNutzerFrist:$aFlds[$i])).' wie <input type="text" name="n1'.$i.'" value="'.$N1.'" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="n2'.$i.'" value="'.$N2.'" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="n3'.$i.'" value="'.$N3.'" style="width:99%;" /></td>
 </tr>';
 }
?>

 <tr class="admTabl">
  <td colspan="3" align="center">suchen in &nbsp;
   <input class="admRadio" type="radio" name="rbForm" value="1" checked="checked" /> Benutzerliste <?php if(FRA_NutzerTests){ ?> &nbsp; oder in &nbsp;
   <input class="admRadio" type="radio" name="rbForm" value="2" /> Benutzer und Tests<?php }?>
  </td>
 </tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Suchen"></p>
</div>
</form>

<?php echo fSeitenFuss();?>