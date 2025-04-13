<?php header('Content-Type: text/html; charset=ISO-8859-1')?><!DOCTYPE html>
<html>
<head>
<title>Farbauswahl</title>
<style type="text/css">
 p,div,td,input{font-family:Verdana,Arial,Helvetica;font-size:11px;}
 p{margin-top:6px;margin-bottom:6px;}
</style>
<?php
 $Fld=(isset($_GET['fld'])?$_GET['fld']:'');
 $Col=(isset($_GET['col'])?$_GET['col']:'eeeeee'); if($Col!='transparent') $Col='#'.$Col;
 if($X=(isset($_GET['pal_x'])?$_GET['pal_x']:0)){
  $X-=4; $Y=(isset($_GET['pal_y'])?$_GET['pal_y']-4:0);
  if($X>0){$X=floor($X/6); if($X>35) $X=35;} else $X=0; if($Y>0){$Y=floor($Y/6); if($Y>5) $Y=5;} else $Y=0;
  $R=strtolower(sprintf('%02x',51*floor($X/6)));
  $G=strtolower(sprintf('%02x',51*($X%6)));
  $B=strtolower(sprintf('%02x',51*$Y));
  $Col='#'.$R.$G.$B;
 }
?>
<script language="JavaScript">
var bTransparent; bTransparent=<?php echo ($Col!='transparent'?'false':'true');?>;
function error(sCol){window.alert(sCol+"-Wert ung�ltig!");return false;}
function dec2hex(n){
 var c=new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
 return c[Math.floor(n/16)]+ c[n%16];
}

function clicked(Action){
 if(!bTransparent){
  var rt=Number(document.fo.r.value); if (isNaN(rt)) {error("Rot");  return false;} if ((rt<0)||(rt>255)) {error("Rot");  return false;}
  var gn=Number(document.fo.g.value); if (isNaN(gn)) {error("Gr�n"); return false;} if ((gn<0)||(gn>255)) {error("Blau"); return false;}
  var bl=Number(document.fo.b.value); if (isNaN(bl)) {error("Blau"); return false;} if ((bl<0)||(bl>255)) {error("Blau"); return false;}
  document.fo.cod.value='#'+dec2hex(rt)+dec2hex(gn)+dec2hex(bl);
  document.bgColor=document.fo.cod.value;
 }else{
  document.fo.cod.value='transparent'; document.bgColor='transparent';
 }
 if(Action){
  opener.document.farbform.<?php echo $Fld?>.value=document.fo.cod.value;
  window.close();
 }
 return false;
}
function setTransparent(){
 document.fo.r.value=''; document.fo.g.value=''; document.fo.b.value='';
 bTransparent=true; document.fo.cod.value='transparent'; document.bgColor='transparent';
}
</script>
</head>

<body marginwidth="0" marginheight="0" style="margin-top:8px;margin-left:4px;margin-right:4px;margin-bottom:4px">
<form name="fo" action="colors.php" method="get">
<input type="hidden" name="fld" value="<?php echo $_GET['fld']?>">
<input type="hidden" name="col" value="<?php echo $_GET['col']?>">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0">
 <tr><td><input type="image" style="padding:0px;margin:0px;" name="pal" src="colors.gif" width="222" height="42" border="0" alt="Farbe w�hlen"></td></tr>
 <tr><td style="text-align:center;padding:8px;"><span style="cursor:pointer;border:1px solid #c0bdc0;background-color:#e7e7e7;" onclick="setTransparent()">&nbsp; transparent &nbsp;</span></td></tr>
 <tr><td style="text-align:center;padding:4px;background-color:#d0cdd0;">W�hlen Sie eine Farbe aus der Palette<br>bzw. 'transparent' bzw.<br> tragen Sie je einen Wert zwischen<br>0...255 f�r Rot, Gr�n und Blau ein<br>und mischen Sie Ihre Wunschfarbe!</td></tr>
 <tr><td>&nbsp;</td></tr>
 <tr>
  <td align="center">
   <table border="0" cellpadding="3" cellspacing="1">
    <tr bgcolor="#ffffff"><td>Rot: </td><td><input type="text" name="r" value="<?php if($Col!='transparent') echo hexdec(substr($Col,1,2));?>" size="10"></td></tr>
    <tr bgcolor="#ffffff"><td>Gr�n:</td><td><input type="text" name="g" value="<?php if($Col!='transparent') echo hexdec(substr($Col,3,2));?>" size="10"></td></tr>
    <tr bgcolor="#ffffff"><td>Blau:</td><td><input type="text" name="b" value="<?php if($Col!='transparent') echo hexdec(substr($Col,5,2));?>" size="10"></td></tr>
    <tr bgcolor="#ffffff"><td>Farbwert:</td><td><input type="text" style="background-color:#cccccc" name="cod" value="<?php echo $Col;?>" size="10" readonly></td></tr>
   </table>
   <p><input type="button" value="Farbe testen" onClick="return clicked(false)"></p>
   <p><input type="button" value="Farbe setzen" onClick="return clicked(true)"></p>
  </td>
 </tr>
</table>
</div>
</form>

</body>
</html>
<script language="JavaScript">
 document.bgColor='<?php echo $Col;?>';
</script>