<?php
global $nSegNo,$sSegNo,$sSegNam; $bKeyOK=true;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Google-Map-Einstellungen','','KSe');

if($_SERVER['REQUEST_METHOD']=='GET'){
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für Google-Maps.'; $MTyp='Meld';
 $mpGMapV3=MP_GMapV3; $mpGMapCode=MP_GMapCode; $mpGMapURL=MP_GMapURL; $mpGMapBreit=MP_GMapBreit; $mpGMapHoch=MP_GMapHoch;
 $mpGMapWarten=MP_GMapWarten; $mpTxGMap1Warten=MP_TxGMap1Warten; $mpTxGMap2Warten=MP_TxGMap2Warten;
 $mpGMapBZentr=MP_GMapBZentr; $mpGMapLZentr=MP_GMapLZentr; $mpGMapZoom=MP_GMapZoom;
 $mpGMapTypeControl=MP_GMapTypeControl; $mpTxGMapOrt=MP_TxGMapOrt;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('GMapCode'); if(fSetzMPWert($v,'GMapCode',"'")) $bNeu=true;
 $s=txtVar('GMapURL');  if(fSetzMPWert($s,'GMapURL',"'")) $bNeu=true;
 if(($p=strpos($s,'?key='))||($p=strpos($s,'&key='))){
  if($l=strlen($v)){
   if(substr($s,$p+5,$l)!=$v) $bKeyOK=false;
  }else $bKeyOK=false;
 }
 $v=(int)txtVar('GMapV3'); if(fSetzMPWert(($v?true:false),'GMapV3','')) $bNeu=true;
 $v=(int)txtVar('GMapBreit');if(fSetzMPWert($v,'GMapBreit','')) $bNeu=true;
 $v=(int)txtVar('GMapHoch'); if(fSetzMPWert($v,'GMapHoch','')) $bNeu=true;
 $v=min(max((int)txtVar('GMapWarten'),1),15); if(fSetzMPWert($v,'GMapWarten','')) $bNeu=true;
 $v=txtVar('TxGMap1Warten'); if(fSetzMPWert($v,'TxGMap1Warten','"')) $bNeu=true;
 $v=txtVar('TxGMap2Warten'); if(fSetzMPWert($v,'TxGMap2Warten','"')) $bNeu=true;
 $v=txtVar('TxGMapOrt'); if(fSetzMPWert($v,'TxGMapOrt',"'")) $bNeu=true;
 $v=str_replace(',','.',txtVar('GMapBZentr')); if(fSetzMPWert($v,'GMapBZentr','')) $bNeu=true;
 $v=str_replace(',','.',txtVar('GMapLZentr')); if(fSetzMPWert($v,'GMapLZentr','')) $bNeu=true;
 $v=min(max((int)txtVar('GMapZoom'),1),17); if(fSetzMPWert($v,'GMapZoom','')) $bNeu=true;
 $v=(int)txtVar('GMapTypeControl'); if(fSetzMPWert(($v?true:false),'GMapTypeControl','')) $bNeu=true;
 if($bNeu&&$bKeyOK){//Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld='Die Einstellungen zu Google-Maps wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld='In die Datei <i>mpWerte.php</i> durfte nicht geschrieben werden!';
 }else{$Meld='Die Einstellungen zu Google-Maps bleiben unverändert.'; $MTyp='Meld';}
 if(!$bKeyOK) $Meld='Ihr persönlicher Key-Code ist nicht in der GoogleMaps-API-Adresse enthalten!';
}//POST

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;
if($mpGMapV3&&strpos($mpGMapURL,'key')>0) $mpGMapV3=2;
?>

<form action="konfGoogleMap.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2"><u>wichtiger Hinweis</u>:
Zur Kartendarstellung wird das Google-Maps-API verwendet.
Das JavaScript Maps API von Google ist ein kostenloser Service und verfügbar für alle Websites, die für Besucher kostenlos sind.
Das gilt zumeist auch für kommerziell betriebene Webseiten,
solange Besucher diese ungehindert und unentgeltlich sehen können.
Es gibt jedoch einige einschränkende Bedingungen seitens Google.
Informieren Sie sich vor Nutzung dieser Funktion in Ihrem Marktplatz-Script,
unter welchen Bedingungen der Gebrauch des Google-Maps-API eventuell <i>nicht</i> erlaubt ist.
Informationen dazu finden Sie in den <a href="https://developers.google.com/maps/terms?hl=de" target="_blank">Nutzungsbedingungen</a> des Google Maps JavaScript API.</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Mit welchen Parametern soll die Anzeige der Karte bei den Inseratedetails im Besucherbereich erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kartengröße</td>
 <td><input type="text" name="GMapBreit" value="<?php echo $mpGMapBreit?>" style="width:40px;" /> px Breite &nbsp;
  <input type="text" name="GMapHoch" value="<?php echo $mpGMapHoch?>" style="width:40px;" /> px Höhe &nbsp;
  <span class="admMini">(Empfehlung: <i>200...500 Pixel</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verzögerungszeit</td>
 <td><input type="text" name="GMapWarten" value="<?php echo $mpGMapWarten?>" style="width:40px;" /> Sekunden nach Seitenaufbau erscheint die Karte
  <div class="admMini">Empfehlung: <i>1...10 Sekunden</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verzögerungstext<div style="margin-top:9px;">Verzögerungslink</div></td>
 <td><input type="text" name="TxGMap1Warten" value="<?php echo $mpTxGMap1Warten?>" style="width:99%" />
  <div><input type="text" name="TxGMap2Warten" value="<?php echo $mpTxGMap2Warten?>" style="width:99%" /></div>
  <div class="admMini">Empfehlung: <i>Karte wird geladen...... anderenfalls hier klicken</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Markerbeschriftung</td>
 <td><input type="text" name="TxGMapOrt" value="<?php echo $mpTxGMapOrt?>" style="width:180px;" /> &nbsp;
  <span class="admMini">Empfehlung: <i>Veranstaltungsort</i></span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Beim Eingeben von Inseraten durch Besucher, Autoren oder Administratoren müssen die zum Inseraten gehörigen Karten-Koordinaten erzeugt werden. Mit welchen voreingestellten Parametern soll die interaktive Karte dem Eintragenden zu Beginn der Koordinateneingabe präsentiert werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kartenmittelpunkt</td>
 <td><input type="text" name="GMapBZentr" value="<?php echo $mpGMapBZentr?>" style="width:180px;" />° geogr. Breite &nbsp;
  <span class="admMini">(Empfehlung: <i> 51.050 und 10.410 für Deutschland</i>)</span>
  <div><input type="text" name="GMapLZentr" value="<?php echo $mpGMapLZentr?>" style="width:180px;" />° geogr. Länge</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kartenmaßstab</td>
 <td><input type="text" name="GMapZoom" value="<?php echo $mpGMapZoom?>" style="width:40px;" /> &nbsp;
 <span class="admMini">Empfehlung: <i>Zoomfaktor 5...7 für Deutschland</i></span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Schaltflächen</td>
 <td><input type="checkbox" class="admCheck" name="GMapTypeControl" value="1"<?php if($mpGMapTypeControl) echo ' checked="checked"'?> /> Schaltfläche [Karte/Satellit] anzeigen </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im aktuellen Google-Maps-V3 (ab Juli 2016) ist ein persönlicher 40-stelliger KeyCode <i>API_KEY</i> notwendig.
Im Google-Maps-V3 (vor Juli 2016) war ein solcher Code nicht nötig. Die uralte Google-Maps-V2 (falls die noch funktioniert) benötigte zwingend einen meist 86-stelligen Code.<br />
Ihren persönlichen KeyCode (API-Key, Browserschlüssel oder wie immer die Begriffe auch künftig lauten) können Sie nur direkt bei <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google</a> anfordern.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">persönlicher<br />Key-Code</td>
 <td><input id="GMapCode" type="text" name="GMapCode" value="<?php echo $mpGMapCode?>" style="width:99%" />
 <div class="admMini"><i>Hinweis</i>: gültigen persönlichen Key-Code eintragen oder leer lassen bei Altinstallationen vor Juli 2016</div>
</tr>
<tr class="admTabl">
 <td class="admSpa1">GoogleMaps-<br />API-Version</td>
 <td><input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(2)" value="2"<?php if($mpGMapV3==2) echo ' checked="checked"'?> /> aktuelle API-Version 3 MIT KeyCode &nbsp; (bei Neuinstallationen nach Juni 2016)<br />
 <input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(1)" value="1"<?php if($mpGMapV3==1) echo ' checked="checked"'?> /> API-Version 3 OHNE KeyCode &nbsp; (Bestandsinstallationen vor Juli 2016)<br />
 <input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(0)" value="0"<?php if(!$mpGMapV3) echo ' checked="checked"'?> /> uralte API-Version 2 &nbsp; (falls es noch funktioniert)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">GoogleMaps-<br />API-Adresse</td>
 <td><input id="GMapURL" type="text" name="GMapURL" value="<?php echo $mpGMapURL?>" style="width:99%" /><div class="admMini"><i>Hinweis</i>: Die möglichen Werte je nach oben gemachten Einstellungen sind:<br />- https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&language=de<br />- http://maps.google.com/maps/api/js?sensor=false&language=de<br />- http://maps.google.com/maps?file=api&v=2&hl=de&key=YOUR_API_KEY<br />und sollten nur manuell verändert werden, wenn Google seinen Dienst verändert hat.</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
<p class="admSubmit">[ <a href="konfEigenschaft.php<?php if($nSegNo) echo '?seg='.$nSegNo?>">zu den Segementeigenschaften</a> ]</p>

<script type="text/javascript">
 function fGMapURL(nVer){
  var nKey; var nUrl; nKey=document.getElementById("GMapCode").value;
  if(nVer==2)     nUrl="https://maps.googleapis.com/maps/api/js?key="+nKey+"&language=de";
  else if(nVer==1) nUrl="http://maps.google.com/maps/api/js?sensor=false&language=de";
  else if(nVer==0) nUrl="http://maps.google.com/maps?file=api&v=2&hl=de&key="+nKey;
  document.getElementById("GMapURL").value=nUrl;
  return true;
 }
</script>

<?php echo fSeitenFuss();?>