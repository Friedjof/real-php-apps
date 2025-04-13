<?php header('Content-Type: text/html; charset=utf-8')?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Termineingabe:: OpenStreetMap - Koordinaten bestimmen</title>
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
<link rel="stylesheet" type="text/css" href="maps/leaflet.css">
<?php
 include('./kalWerte.php'); $nPb=0; $nPl=0;
 if(!$nBr=KAL_GMapBZentr) $nBr=51.05; if(!$nLn=KAL_GMapLZentr) $nLn=10.41; if(!$nZm=KAL_GMapZoom) $nZm=6;
 if($sQ=$_SERVER['QUERY_STRING']){
  $aQ=explode(',',$sQ); $nFld=(int)$aQ[0];
  if(isset($aQ[5])&&($n=0+$aQ[5])) $nZm=$n;
  if(isset($aQ[4])&&($n=0+$aQ[4])) $nPl=$n; if(isset($aQ[3])&&($n=0+$aQ[3])) $nPb=$n;
  if(isset($aQ[2])&&($n=0+$aQ[2])) $nLn=$n; if(isset($aQ[1])&&($n=0+$aQ[1])) $nBr=$n;
 }else $nFld=0;
?>
<script src="maps/leaflet.js"></script>
</head>

<body>
<h1 style="text-align:center">Karte f&uuml;r den Veranstaltungsort erstellen</h1>
<div><b>Anleitung:</b></div>
<div>Setzen Sie Ihren Veranstaltungsort auf der Karte auf einem von zwei Wegen:</div>
<ol style="list-style-type:upper-alpha;margin-top:3px;margin-bottom:3px;">
<li>entweder durch Ortseingabe im Suchfeld direkt in der Karte</li>
<li>oder durch Schieben und Klicken direkt in der Karte nach folgendem Ablauf:</li>
<ol style="margin-top:3px;padding-left:16px;">
<li>Verschieben Sie den Kartenausschnitt (durch Schieben mit der linken Maustaste) so, dass der Zielort in etwa mittig in der Karte liegt.</li>
<li>Ver&auml;ndern Sie den Kartenma&szlig;stab durch Klicken auf den +-Schalter in der Karte. Korrigieren Sie dabei bei Bedarf die Lage der Karte durch Schieben.</li>
<li>Setzen Sie mit einen pr&auml;zisen linken Mausklick einen Marker auf den Zielort.</li>
<li>Verkleinern Sie eventuell &uuml;ber den -Schalter den Kartenausschnitt soweit, bis die n&ouml;tige &Uuml;bersicht gegeben ist.</li>
</ol>
</ol>
<div style="width:496px;margin-left:auto;margin-right:auto;text-align:center;border:2px dotted #bbb;">
<div id="Karte" style="width:<?php echo KAL_GMapBreit?>px;height:<?php echo KAL_GMapHoch?>px;text-align:center;margin:4px;margin-left:auto;margin-right:auto"></div>
</div>
<form name="geo" action="">
<div style="text-align:center;margin-top:6px;">
<table style="width:500px;margin:4px;margin-left:auto;margin-right:auto;border:0">
<tr>
 <td>Karten&shy;mittel&shy;punkt:</td>
 <td><input class="geo" type="text" name="cb" value="<?php echo $nBr?>" size="19"> (Breite)</td>
 <td><input class="geo" type="text" name="cl" value="<?php echo $nLn?>" size="19"> (L&auml;nge)</td>
</tr>
<tr>
 <td>Veran&shy;stal&shy;tungs&shy;ort:</td>
 <td><input class="geo" type="text" name="pb" value="<?php echo $nPb?>" size="19"> (Breite)</td>
 <td><input class="geo" type="text" name="pl" value="<?php echo $nPl?>" size="19"> (L&auml;nge)</td>
</tr>
<tr>
 <td>Zoom&shy;faktor:</td>
 <td colspan="2" style="text-align:left">&nbsp;<input class="geo" style="width:18px;" type="text" name="zm" value="<?php echo $nZm?>" size="3"></td>
</tr>
</table>
<p><input type="button" value="Fertig &amp; Speichern" onclick="return clicked()"></p>
</div>
</form>
</body>
<script>
 var mbAttr='Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>';
 var mbUrl='https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=<?php echo KAL_SMapCode?>';
 var sat=L.tileLayer(mbUrl,{id:'mapbox/satellite-v9',tileSize:512,zoomOffset:-1,attribution:mbAttr});
 var osm=L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy OpenStreetMap',maxZoom:19});
 var bDrag=true; if(<?php echo(KAL_SMap2Finger?'true':'false');?>) bDrag=!L.Browser.mobile;
 var map=L.map('Karte',{center:[<?php echo $nBr?>,<?php echo $nLn?>],zoom:<?php echo $nZm?>,<?php if(KAL_SMap2Finger){ ?>dragging:bDrag,tap:bDrag,<?php } ?>scrollWheelZoom:false,layers:[osm]});
 var osmGeocoder=new L.Control.OSMGeocoder({placeholder:'Ort oder PLZ suchen...'}); map.addControl(osmGeocoder);
 if(<?php echo (KAL_SMapTypeControl?'true':'false')?>){var baseLayers={'Karte':osm,'Satellit':sat}; var layerControl=L.control.layers(baseLayers).addTo(map);}
 var marker=L.marker([<?php echo $nPb?>,<?php echo $nPl?>],{opacity:0.7<?php if(KAL_TxGMapOrt){?>,title:'<?php echo iconv('ISO-8859-1','UTF-8',KAL_TxGMapOrt)?>'<?php }?>}).addTo(map);
 var mapCenter=map.getCenter(); var nF=Math.pow(2,<?php echo $nZm?>); mapCenter.lng+=153.6/nF; mapCenter.lat-=64/nF;
 var tooltip=L.tooltip().setLatLng(mapCenter).setContent('Verschieben der Karte mit 2 Fingern!').addTo(map); if(bDrag) map.closeTooltip(tooltip);

 function onMapClick(e){
  var aLatLng=e.latlng; marker.setLatLng(aLatLng); document.geo.pb.value=aLatLng.lat; document.geo.pl.value=aLatLng.lng; map.closeTooltip(tooltip);
  aLatLng=map.getCenter(); document.geo.cb.value=aLatLng.lat; document.geo.cl.value=aLatLng.lng; document.geo.zm.value=map.getZoom();
 }
 function onMapMove(e){
  var aLatLng=map.getCenter(); document.geo.cb.value=aLatLng.lat; document.geo.cl.value=aLatLng.lng; map.closeTooltip(tooltip);
 }
 function onMapZoom(e){
  document.geo.zm.value=map.getZoom(); map.closeTooltip(tooltip);
 }
 map.on('click',onMapClick); map.on('moveend',onMapMove); map.on('zoomend',onMapZoom);
 function clicked(){
  window.opener.document.kalEingabe.kal_F<?php echo $nFld?>.value=document.geo.cb.value+','+document.geo.cl.value+','+document.geo.pb.value+','+document.geo.pl.value+','+document.geo.zm.value;
  window.close();
  return false;
 }
</script>
</html>
