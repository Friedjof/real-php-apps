<?php
global $nSegNo,$sSegNo,$sSegNam; $bKeyOK=true;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Stra�enkarten-Einstellungen','','KSe');

if($_SERVER['REQUEST_METHOD']=='GET'){
 $Meld='Kontrollieren oder �ndern Sie die Einstellungen f�r Stra�enkarten.'; $MTyp='Meld'; $sDef='';
 if(!defined('MP_SMapCode'))       {$sDef.="define('MP_SMapCode','');\n";          define('MP_SMapCode','');}
 if(!defined('MP_SMapTypeControl')){$sDef.="define('MP_SMapTypeControl',false);\n";define('MP_SMapTypeControl',false);}
 if($sDef){ // neue Defines noetig
  $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
  if($p=strpos($sWerte,"define('MP_GMapBreit")){
   $sWerte=substr_replace($sWerte,$sDef,$p,0); $bNeu=true;
  }else{$Meld='Es wurde keine Stelle f�r die neuen Kartenparameter in der Datei <i>mpWerte.php</i> gefunden!'; $MTyp='Fehl';}
  if($bNeu){//neue Defines speichern
   if($f=fopen(MP_Pfad.'mpWerte.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   }else{$Meld='Die Kartenparameter durften nicht in die Datei <i>mpWerte.php</i> geschrieben werden!'; $MTyp='Fehl';}
  }
 }
 $mpGMapV3=MP_GMapV3; $mpGMapCode=MP_GMapCode; $mpGMapURL=MP_GMapURL; $mpGMapBreit=MP_GMapBreit; $mpGMapHoch=MP_GMapHoch;
 $mpGMapWarten=MP_GMapWarten; $mpTxGMap1Warten=MP_TxGMap1Warten; $mpTxGMap2Warten=MP_TxGMap2Warten;
 $mpGMapBZentr=MP_GMapBZentr; $mpGMapLZentr=MP_GMapLZentr; $mpGMapZoom=MP_GMapZoom;
 $mpGMapTypeControl=MP_GMapTypeControl; $mpTxGMapOrt=MP_TxGMapOrt; $mpGMapSource=MP_GMapSource;
 $mpSMapCode=MP_SMapCode; $mpSMapTypeControl=MP_SMapTypeControl; $mpSMap2Finger=MP_SMap2Finger;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('GMapSource'); if(fSetzMPWert($v,'GMapSource',"'")) $bNeu=true;
 $v=txtVar('GMapCode'); if(fSetzMPWert($v,'GMapCode',"'")) $bNeu=true;
 $s=txtVar('GMapURL');  if(fSetzMPWert($s,'GMapURL',"'")) $bNeu=true;
 if(($p=strpos($s,'?key='))||($p=strpos($s,'&key='))){
  if($l=strlen($v)){
   if(substr($s,$p+5,$l)!=$v) if($mpGMapSource=='G') $bKeyOK=false;
  }else if($mpGMapSource=='G') $bKeyOK=false;
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
 $v=txtVar('SMapCode'); if(fSetzMPWert($v,'SMapCode',"'")) $bNeu=true;
 $v=(int)txtVar('SMap2Finger'); if(fSetzMPWert(($v?true:false),'SMap2Finger','')) $bNeu=true;
 $v=(int)txtVar('SMapTypeControl'); if(fSetzMPWert(($v?true:false),'SMapTypeControl','')) $bNeu=true;
 if($bNeu&&$bKeyOK){//Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld='Die Einstellungen zu Stra�enkarten wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld='In die Datei <i>mpWerte.php</i> durfte nicht geschrieben werden!';
 }else{$Meld='Die Einstellungen zu Stra�enkarten bleiben unver�ndert.'; $MTyp='Meld';}
 if(!$bKeyOK) $Meld='Ihr pers�nlicher Key-Code ist nicht in der GoogleMaps-API-Adresse enthalten!';
}//POST

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;
if($mpGMapV3&&strpos($mpGMapURL,'key')>0) $mpGMapV3=2;
?>

<form action="konfStreetMap.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1">Kartenanbieter</td>
 <td><input class="admRadio" type="radio" name="GMapSource" value="O"<?php if($mpGMapSource=='O') echo ' checked="checked"'?> /> OpenStreetMap &nbsp; &nbsp;  <input class="admRadio" type="radio" name="GMapSource" value="G"<?php if($mpGMapSource=='G') echo ' checked="checked"'?> /> GoogleMap</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Mit welchen Parametern soll die Anzeige der Karte bei den Inseratedetails im Besucherbereich erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kartengr��e</td>
 <td><input type="text" name="GMapBreit" value="<?php echo $mpGMapBreit?>" style="width:40px;" /> px Breite &nbsp;
  <input type="text" name="GMapHoch" value="<?php echo $mpGMapHoch?>" style="width:40px;" /> px H�he &nbsp;
  <span class="admMini">(Empfehlung: <i>200...500 Pixel</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Markerbeschriftung</td>
 <td><input type="text" name="TxGMapOrt" value="<?php echo $mpTxGMapOrt?>" style="width:180px;" /> &nbsp;
  <span class="admMini">Empfehlung: <i>Veranstaltungsort</i></span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verz�gerungszeit</td>
 <td><input type="text" name="GMapWarten" value="<?php echo $mpGMapWarten?>" style="width:40px;" /> Sekunden nach Seitenaufbau erscheint die Karte
  <div class="admMini">Empfehlung: <i>1...10 Sekunden</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verz�gerungstext<div style="margin-top:9px;">Verz�gerungslink</div></td>
 <td><input type="text" name="TxGMap1Warten" value="<?php echo $mpTxGMap1Warten?>" style="width:99%" />
  <div><input type="text" name="TxGMap2Warten" value="<?php echo $mpTxGMap2Warten?>" style="width:99%" /></div>
  <div class="admMini">Empfehlung: <i>Karte wird geladen...... anderenfalls hier klicken</i></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Beim Eingeben von Inseraten durch Besucher, Autoren oder Administratoren m�ssen die zum Inseraten geh�rigen Karten-Koordinaten erzeugt werden. Mit welchen voreingestellten Parametern soll die interaktive Karte dem Eintragenden zu Beginn der Koordinateneingabe pr�sentiert werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kartenmittelpunkt</td>
 <td><input type="text" name="GMapBZentr" value="<?php echo $mpGMapBZentr?>" style="width:180px;" />� geogr. Breite &nbsp;
  <span class="admMini">(Empfehlung: <i> 51.050 und 10.410 f�r Deutschland</i>)</span>
  <div><input type="text" name="GMapLZentr" value="<?php echo $mpGMapLZentr?>" style="width:180px;" />� geogr. L�nge</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kartenma�stab</td>
 <td><input type="text" name="GMapZoom" value="<?php echo $mpGMapZoom?>" style="width:40px;" /> &nbsp;
 <span class="admMini">Empfehlung: <i>Zoomfaktor 5...7 f�r Deutschland</i></span></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
<p class="admSubmit">[ <a href="konfEigenschaft.php<?php if($nSegNo) echo '?seg='.$nSegNo?>">zu den Segementeigenschaften</a> ]</p>

<p class="admMeld">Der folgende Abschnitt gilt zus�tzlich <i>NUR</i> f�r Kartendarstellungen unter OpenStreetMap.</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1" style="min-width:9.6em">Karte verschieben</td>
 <td><input type="radio" class="admCheck" name="SMap2Finger" value="1"<?php if($mpSMap2Finger) echo ' checked="checked"'?> /> 2 Finger &nbsp; <input type="radio" class="admCheck" name="SMap2Finger" value="0"<?php if(!$mpSMap2Finger) echo ' checked="checked"'?> /> 1 Finger&nbsp; zum Verschieben des Karteninhaltes auf Mobilger�ten </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">
Bei einer Kartendarstellung �ber OpenStreetMap kann optional auch eine Satellitenansicht eingeschaltet werden.
Diese benutzet jedoch das Satellitenkartenmaterial der Firma <a href="https://www.mapbox.com" target="_blank">MapBox</a>,
welches im Gegensatz zu den Stra�enkarten von OpenStreetMap nicht g�nzlich kostenlos ist.
Es gibt ein monatlich freies Limit an Satellitenkartenaufrufen. Nach dessen �berschreitung fallen Kosten an.
Informationen dazu finden Sie in den <a href="https://www.mapbox.com/pricing" target="_blank">Preisbedingungen</a> der Firma <a href="https://www.mapbox.com" target="_blank">MapBox</a>.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="min-width:9.6em">Schaltfl�chen</td>
 <td><input type="checkbox" class="admCheck" name="SMapTypeControl" value="1"<?php if($mpSMapTypeControl) echo ' checked="checked"'?> /> Schaltfl�che [Karte/Satellit] anzeigen </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">access_token</td>
 <td><input id="SMapCode" type="text" name="SMapCode" value="<?php echo $mpSMapCode?>" style="width:99%" />
 <div class="admMini"><i>Hinweis</i>: Einen notwendigen pers�nlicher Zugriffscode <i>access_token</i> erhalten Sie im Kundenbereich von <a href="https://www.mapbox.com" target="_blank">MapBox</a>.</div>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
<p class="admSubmit">[ <a href="konfEigenschaft.php<?php if($nSegNo) echo '?seg='.$nSegNo?>">zu den Segementeigenschaften</a> ]</p>

<p class="admMeld">Der folgende Abschnitt gilt zus�tzlich <i>NUR</i> f�r Google-Maps.</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">
Zur Kartendarstellung kann das Google-Maps-API verwendet werden.
Das JavaScript Maps API von Google war bis Sommer 2018 ein kostenloser Service und verf�gbar f�r alle Websites, die f�r Besucher kostenlos sind.
Das galt zumeist auch f�r kommerziell betriebene Webseiten, solange Besucher diese ungehindert und unentgeltlich sehen k�nnen.
Seit Sommer 2018 ist der Dienst zwar teils immer noch kostenlos nutzbar, es wird jedoch von Google die Hinterlegung eines Zahlungskontos verlangt.
Informieren Sie sich vor Nutzung dieser Funktion in Ihrem Marktplatz-Script,
unter welchen Bedingungen der Gebrauch des Google-Maps-API eventuell <i>nicht</i> erlaubt ist oder Kosten verursacht.
Informationen dazu finden Sie in den <a href="https://developers.google.com/maps/terms?hl=de" target="_blank">Nutzungsbedingungen</a> des Google Maps JavaScript API.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="min-width:9.6em">Schaltfl�chen</td>
 <td><input type="checkbox" class="admCheck" name="GMapTypeControl" value="1"<?php if($mpGMapTypeControl) echo ' checked="checked"'?> /> Schaltfl�che [Karte/Satellit] anzeigen </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im aktuellen Google-Maps-V3 (ab Juli 2016) ist ein pers�nlicher 40-stelliger KeyCode <i>API_KEY</i> notwendig.
Im Google-Maps-V3 (vor Juli 2016) war ein solcher Code nicht n�tig. Die uralte Google-Maps-V2 (falls die noch funktioniert) ben�tigte zwingend einen meist 86-stelligen Code.<br />
Ihren pers�nlichen KeyCode (API-Key, Browserschl�ssel oder wie immer die Begriffe auch k�nftig lauten) k�nnen Sie nur direkt bei <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google</a> anfordern.
Es ist jedoch seit Sommer 2018 die Hinterlegung eines Zahlungskontos notwendig, selbst wenn der kostenlose Kartendienst bzw. Kartenumfang genutzt wird.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">pers�nlicher<br />Key-Code</td>
 <td><input id="GMapCode" type="text" name="GMapCode" value="<?php echo $mpGMapCode?>" style="width:99%" />
 <div class="admMini"><i>Hinweis</i>: g�ltigen pers�nlichen Key-Code eintragen oder leer lassen bei Altinstallationen vor Juli 2016</div>
</tr>
<tr class="admTabl">
 <td class="admSpa1">GoogleMaps-<br />API-Version</td>
 <td><input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(2)" value="2"<?php if($mpGMapV3==2) echo ' checked="checked"'?> /> aktuelle API-Version 3 MIT KeyCode &nbsp; (bei Neuinstallationen nach Juni 2016)<br />
 <input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(1)" value="1"<?php if($mpGMapV3==1) echo ' checked="checked"'?> /> API-Version 3 OHNE KeyCode &nbsp; (Bestandsinstallationen vor Juli 2016)<br />
 <input type="radio" class="admCheck" name="GMapV3" onchange="fGMapURL(0)" value="0"<?php if(!$mpGMapV3) echo ' checked="checked"'?> /> uralte API-Version 2 &nbsp; (falls es noch funktioniert)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">GoogleMaps-<br />API-Adresse</td>
 <td><input id="GMapURL" type="text" name="GMapURL" value="<?php echo $mpGMapURL?>" style="width:99%" /><div class="admMini"><i>Hinweis</i>: Die m�glichen Werte je nach oben gemachten Einstellungen sind:<br />- https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&language=de<br />- http://maps.google.com/maps/api/js?sensor=false&language=de<br />- http://maps.google.com/maps?file=api&v=2&hl=de&key=YOUR_API_KEY<br />und sollten nur manuell ver�ndert werden, wenn Google seinen Dienst ver�ndert hat.</div></td>
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