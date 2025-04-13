<?php header('Content-Type: text/html; charset=utf-8')?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="expires" content="0">
<title>Inserateeingabe:: Google-Maps - Koordinaten bestimmen</title>
<style type="text/css">
 h1,p,div,td,li,a,input{font-family:Verdana,Arial,Helvetica;}
 p,div,li,a,input{font-size:11px;}
 h1{margin-top:0px; margin-bottom:3px; font-size:14px;}
 p {margin-top:3px; margin-bottom:3px;}
 table{
  font-size:11px; font-weight:normal;
  background-color:#F7F7F7;
  border-color:#BBBBBB; border-style:solid; border-width:1px; border-collapse:collapse;
  margin:0px; table-layout:auto;
 }
 td{
  font-size:1.0em; font-weight:normal;
  border-color:#CCCCCC; border-width:1px; border-style:solid;
  padding:3px; vertical-align:center;
  color:#000000; background-color:#F7F7FC;
 }
 input.geo{
  width:98%; max-width:128px; color:#000066;
  border-color:#AAAAAA; border-width:1px; border-style:solid;
 }
</style>
<?php
 include('./mpWerte.php'); $nPb=0; $nPl=0;
 if(!$nBr=MP_GMapBZentr) $nBr=51.05; if(!$nLn=MP_GMapLZentr) $nLn=10.41; if(!$nZm=MP_GMapZoom) $nZm=6;
 if($sQ=$_SERVER['QUERY_STRING']){
  $aQ=explode(',',$sQ); $nFld=(int)$aQ[0];
  if(isset($aQ[5])&&($n=0+$aQ[5])) $nZm=$n;
  if(isset($aQ[4])&&($n=0+$aQ[4])) $nPl=$n; if(isset($aQ[3])&&($n=0+$aQ[3])) $nPb=$n;
  if(isset($aQ[2])&&($n=0+$aQ[2])) $nLn=$n; if(isset($aQ[1])&&($n=0+$aQ[1])) $nBr=$n;
 }else $nFld=0;
?>
<script type="text/javascript" src="<?php echo MP_GMapURL?>"></script>
<script type="text/javascript">
 var img; var poi; poi=null;
<?php if(MP_GMapV3){?>
 function showMap(){
  var mapLatLng=new google.maps.LatLng(<?php echo $nBr?>,<?php echo $nLn?>);
  var mapOption={zoom:<?php echo $nZm?>,center:mapLatLng,panControl:true,mapTypeControl:<?php echo(MP_GMapTypeControl?'true':'false')?>,streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  img=new google.maps.Map(document.getElementById('Karte'),mapOption);
  var geocoder=new google.maps.Geocoder();
  document.getElementById('gSearch').addEventListener('click',function(){geocodeAddress(geocoder,img);});
<?php if($nPb!=0||$nPl!=0){?>
  var poiLatLng=new google.maps.LatLng(<?php echo $nPb?>,<?php echo $nPl?>);
  poi=new google.maps.Marker({position:poiLatLng,map:img,title:'<?php echo iconv('ISO-8859-1','UTF-8',MP_TxGMapOrt)?>'});
<?php }?>
  google.maps.event.addListener(img,'click',function(event){placeMrk(event.latLng);});
  google.maps.event.addListener(img,'center_changed',function(event){moveMap();});
  google.maps.event.addListener(img,'zoom_changed',function(event){zoomMap();});
 }
 function geocodeAddress(geocoder,img){
  var address=document.getElementById('address').value;
  geocoder.geocode({'address': address}, function(results, status){
   if(status===google.maps.GeocoderStatus.OK){
    if(poi!=null){poi.setMap(null); poi=null;}
    img.setCenter(results[0].geometry.location);
    poi=new google.maps.Marker({position:results[0].geometry.location,map:img,title:'<?php echo iconv('ISO-8859-1','UTF-8',MP_TxGMapOrt)?>'});
    document.geo.pb.value=results[0].geometry.location.lat(); document.geo.pl.value=results[0].geometry.location.lng();
   }else alert('Geocode-Fehler: '+status);
  });
 }
 function placeMrk(location){
  if(poi!=null){poi.setMap(null); poi=null;}
  poi=new google.maps.Marker({position:location,map:img,title:'<?php echo iconv('ISO-8859-1','UTF-8',MP_TxGMapOrt)?>'});
  document.geo.pb.value=location.lat(); document.geo.pl.value=location.lng();
 }
 function moveMap(){mCntr=img.getCenter(); document.geo.cb.value=mCntr.lat(); document.geo.cl.value=mCntr.lng();}
 function zoomMap(){document.geo.zm.value=img.getZoom();}
<?php }else{ /* Ver.2 */ ?>
 function showMap(){
  if(GBrowserIsCompatible()){
   img=new GMap2(document.getElementById('Karte'));
   img.setCenter(new GLatLng(<?php echo $nBr?>,<?php echo $nLn?>),<?php echo $nZm?>);
   img.addControl(new GSmallMapControl());
   <?php if(MP_GMapTypeControl) echo 'img.addControl(new GMapTypeControl());'."\n"?>
   <?php if($nPb!=0||$nPl!=0) echo 'img.addOverlay(new GMarker(new GLatLng('.$nPb.','.$nPl.')));'."\n"?>

   GEvent.addListener(img,'click',function(overlay,point){
    if(point){
     img.clearOverlays(); img.addOverlay(new GMarker(new GLatLng(point.y,point.x)));
     document.geo.pb.value=point.y; document.geo.pl.value=point.x; document.geo.zm.value=img.getZoom();
   }});
   GEvent.addListener(img,'moveend',function(){
    var cntr=img.getCenter();
    document.geo.cb.value=cntr.lat(); document.geo.cl.value=cntr.lng(); document.geo.zm.value=img.getZoom();
   });
 }}
<?php }?>
 function clicked(){
  window.opener.document.mpEingabe.mp_F<?php echo $nFld?>.value=document.geo.cb.value+','+document.geo.cl.value+','+document.geo.pb.value+','+document.geo.pl.value+','+document.geo.zm.value;
  window.close();
  return false;
 }
</script>
</head>

<body onload="showMap()"<?php echo(MP_GMapV3?'':' onunload="GUnload"')?>>
<h1 style="text-align:center">Karte f&uuml;r den Inserateort erstellen</h1>
<div><b>Anleitung:</b></div>
<div>Setzen Sie Ihren Inserateort auf der Karte auf einem von zwei Wegen:</div>
<ol style="list-style-type:upper-alpha;margin-top:3px;margin-bottom:3px;">
<li>entweder durch PLZ-Eingabe oder Ortseingabe direkt &uuml;ber der Karte</li>
<li>oder durch Schieben und Klicken direkt in der Karte nach folgendem Ablauf:</li>
<ol style="margin-top:3px;padding-left:16px;">
<li>Verschieben Sie den Kartenausschnitt (durch Schieben mit der linken Maustaste) so, dass der Zielort in etwa mittig in der Karte liegt.</li>
<li>Ver&auml;ndern Sie den Kartenma&szlig;stab durch Klicken auf den +-Schalter in der Karte. Korrigieren Sie dabei bei Bedarf die Lage der Karte durch Schieben.</li>
<li>Setzen Sie mit einen pr&auml;zisen linken Mausklick einen Marker auf den Zielort. Sie k&ouml;nnen das solange wiederholen, bis der Marker an der passenden Stelle sitzt.</li>
<li>Verkleinern Sie eventuell &uuml;ber den -Schalter den Kartenausschnitt soweit, bis die n&ouml;tige &Uuml;bersicht gegeben ist.</li>
</ol>
</ol>
<div style="width:98%;margin-left:auto;margin-right:auto;text-align:center;border:1px dotted #bbb;">
<div id="Panel" style="margin:4px;"><input id="address" type="textbox" value="" style="width:98%;max-width:300px" placeholder="Ort, Strasse"> <input id="gSearch" type="button" value="Ort suchen"></div>
<div id="Karte" style="width:98%;max-width:<?php echo MP_GMapBreit?>px;height:<?php echo MP_GMapHoch?>px;text-align:center;margin:4px;margin-left:auto;margin-right:auto"></div>
</div>
<form name="geo" action="">
<div style="text-align:center;margin-top:6px;">
<table style="width:98%;margin:4px;margin-left:auto;margin-right:auto" border="0" cellpadding="0" cellspacing="0">
<tr>
 <td>Karten&shy;mittel&shy;punkt:</td>
 <td style="text-align:left"><input class="geo" type="text" name="cb" value="<?php echo $nBr?>" size="19" /> (Breite)</td>
 <td style="text-align:left"><input class="geo" type="text" name="cl" value="<?php echo $nLn?>" size="19" /> (L&auml;nge)</td>
</tr>
<tr>
 <td>Veran&shy;stal&shy;tungs&shy;ort:</td>
 <td style="text-align:left"><input class="geo" type="text" name="pb" value="<?php echo $nPb?>" size="19" /> (Breite)</td>
 <td style="text-align:left"><input class="geo" type="text" name="pl" value="<?php echo $nPl?>" size="19" /> (L&auml;nge)</td>
</tr>
<tr>
 <td>Zoom&shy;faktor:</td>
 <td colspan="2" style="text-align:left"><input class="geo" style="width:18px;" type="text" name="zm" value="<?php echo $nZm?>" size="3" /></td>
</tr>
</table>
<p><input type="button" value="Fertig &amp; Speichern" onclick="return clicked()" /></p>
</div>
</form>
</body>
</html>
