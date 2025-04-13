<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Detailanzeige kofigurieren','','KDt');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für die Detailanzeige. <a href="'.AM_Hilfe.'LiesMich.htm#2.7" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpTxDetails=MP_TxDetails; $mpTxDMetaKey=MP_TxDMetaKey; $mpTxDMetaDes=MP_TxDMetaDes;
 $mpDTitelQuelle=MP_DTitelQuelle; $mpDTitelWL=MP_DTitelWL; $mpDTitelZL=MP_DTitelZL; $mpTxDMetaTit=MP_TxDMetaTit;
 $mpNDetailAnders=MP_NDetailAnders; $mpEigeneDetails=MP_EigeneDetails; $mpZeigeLeeres=MP_ZeigeLeeres;
 $mpDetailNaviOben=MP_DetailNaviOben; $mpDetailNaviUnten=MP_DetailNaviUnten; $mpDetailNaviBild=MP_DetailNaviBild;
 $mpNutzerDetailFeld=MP_NutzerDetailFeld; $mpNNutzerDetailFeld=MP_NNutzerDetailFeld;
 $mpDetailPopup=MP_DetailPopup; $mpPopupBreit=MP_PopupBreit; $mpPopupHoch=MP_PopupHoch;
 $mpTxInfoSenden=MP_TxInfoSenden; $mpTxBenachrService=MP_TxBenachrService;
 $mpDetailInfo=MP_DetailInfo; $mpGastDInfo=MP_GastDInfo; $mpDetailBenachr=MP_DetailBenachr; $mpGastDBenachr=MP_GastDBenachr;
 $mpDetailAendern=MP_DetailAendern; $mpGastDAendern=MP_GastDAendern; $mpDetailKopieren=MP_DetailKopieren; $mpGastDKopieren=MP_GastDKopieren;
 $mpDetailDrucken=MP_DetailDrucken; $mpGastDDrucken=MP_GastDDrucken; $mpDruckDFarbig=MP_DruckDFarbig; $mpDruckDMailOffen=MP_DruckDMailOffen;
 $mpErsatzBildGross=MP_ErsatzBildGross; $mpDetailLinkSymbol=MP_DetailLinkSymbol; $mpDetailDateiSymbol=MP_DetailDateiSymbol;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('TxDetails'); if(fSetzMPWert($v,'TxDetails','"')) $bNeu=true;
 $v=txtVar('TxDMetaKey'); if(fSetzMPWert($v,'TxDMetaKey','"')) $bNeu=true;
 $v=txtVar('TxDMetaDes'); if(fSetzMPWert($v,'TxDMetaDes','"')) $bNeu=true;
 $v=(int)txtVar('DTitelQuelle'); if(fSetzMPWert($v,'DTitelQuelle','')) $bNeu=true;
 $v=min(max(txtVar('DTitelWL'),1),5);  if(fSetzMPWert($v,'DTitelWL','')) $bNeu=true;
 $v=min(max(txtVar('DTitelZL'),1),80); if(fSetzMPWert($v,'DTitelZL','')) $bNeu=true;
 $v=txtVar('TxDMetaTit'); if(fSetzMPWert($v,'TxDMetaTit','"')) $bNeu=true;
 $v=txtVar('DetailNaviOben');  if(fSetzMPWert($v,'DetailNaviOben','')) $bNeu=true;
 $v=txtVar('DetailNaviUnten'); if(fSetzMPWert($v,'DetailNaviUnten','')) $bNeu=true;
 //$v=txtVar('DetailNaviBild');  if(fSetzMPWert(($v?true:false),'DetailNaviBild','')) $bNeu=true;
 $v=(int)txtVar('DetailPopup'); if(fSetzMPWert(($v?true:false),'DetailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzMPWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzMPWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('EigeneDetails'); if(fSetzMPWert(($v?true:false),'EigeneDetails','')) $bNeu=true;
 $v=txtVar('NDetailAnders');if(fSetzMPWert(($v?true:false),'NDetailAnders','')) $bNeu=true;
 $v=txtVar('ZeigeLeeres'); if(fSetzMPWert(($v?true:false),'ZeigeLeeres','')) $bNeu=true;
 $v=(int)txtVar('NutzerDetailFeld'); if(fSetzMPWert($v,'NutzerDetailFeld','')) $bNeu=true;
 $v=(int)txtVar('NNutzerDetailFeld'); if(fSetzMPWert($v,'NNutzerDetailFeld','')) $bNeu=true;
 $v=(int)txtVar('DetailInfo'); if(fSetzMPWert($v,'DetailInfo','')) $bNeu=true;
 $v=txtVar('GastDInfo'); if(fSetzMPWert(($v?true:false),'GastDInfo','')) $bNeu=true;
 $v=txtVar('TxInfoSenden'); if(fSetzMPWert($v,'TxInfoSenden','"')) $bNeu=true;
 $v=(int)txtVar('DetailBenachr'); if(fSetzMPWert($v,'DetailBenachr','')) $bNeu=true;
 $v=txtVar('GastDBenachr'); if(fSetzMPWert(($v?true:false),'GastDBenachr','')) $bNeu=true;
 $v=txtVar('TxBenachrService'); if(fSetzMPWert($v,'TxBenachrService','"')) $bNeu=true;
 $v=(int)txtVar('DetailAendern'); if(fSetzMPWert($v,'DetailAendern','')) $bNeu=true;
 $v=(int)txtVar('GastDAendern'); if(fSetzMPWert(($v?true:false),'GastDAendern','')) $bNeu=true;
 $v=(int)txtVar('DetailKopieren'); if(fSetzMPWert($v,'DetailKopieren','')) $bNeu=true;
 $v=(int)txtVar('GastDKopieren'); if(fSetzMPWert(($v?true:false),'GastDKopieren','')) $bNeu=true;
 $v=(int)txtVar('DetailDrucken'); if(fSetzMPWert($v,'DetailDrucken','')) $bNeu=true;
 $v=txtVar('GastDDrucken'); if(fSetzMPWert(($v?true:false),'GastDDrucken','')) $bNeu=true;
 $v=(int)txtVar('DruckDMailOffen'); if(fSetzMPWert(($v?true:false),'DruckDMailOffen','')) $bNeu=true;
 $v=(int)txtVar('DruckDFarbig'); if(fSetzMPWert(($v?true:false),'DruckDFarbig','')) $bNeu=true;
 $v=txtVar('ErsatzBildGross'); if(fSetzMPWert($v,'ErsatzBildGross',"'")) $bNeu=true;
 $v=txtVar('DetailLinkSymbol'); if(fSetzMPWert(($v?true:false),'DetailLinkSymbol','')) $bNeu=true;
 $v=txtVar('DetailDateiSymbol'); if(fSetzMPWert(($v?true:false),'DetailDateiSymbol','')) $bNeu=true;

 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Detaileinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Detaileinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;

$aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNFz=count($aNF);
$sNOpt='<option value="0">--</option><option value="2">'.str_replace(';','`,',$aNF[2]).'</option>'; for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.str_replace(';','`,',$aNF[$j]).'</option>';
$sFOpt='<option value="-1">--</option><option value="0">0: Nummer</option><option value="1">1: Ablauf</option>'; for($j=2;$j<16;$j++) $sFOpt.='<option value="'.$j.'">'.$j.'</option>';
?>

<form action="konfDetail.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Über den Inseratedetails wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift</td>
 <td colspan="2" width="80%"><input type="text" name="TxDetails" value="<?php echo $mpTxDetails?>" style="width:99%" />
 <div class="admMini">Empfehlung: <i>Details zum Inserat #N</i><br>(#N ist ein Platzhalter für die Inseratenummer, #S für den Segmentnamen)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Marktplatz eigenständig
mit der umhüllenden HTML-Schablone <i>mpSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> in der Detailseite
über die Platzhalter <i>{META-KEY}</i> und <i>{META-DES}</i> der HTML-Schablone <i>mpSeite.htm</i>
mit folgenden Standardtexten zusätzlich füllen, sofern nicht beim einzelnen Inserat
individuelle Angaben für die beiden Felder <i>META-KEY</i> und <i>META-DES</i> angegeben sind.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxDMetaKey" value="<?php echo $mpTxDMetaKey?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>#S, Inserat</i> &nbsp; (#S steht als Platzhalter für den <i>Segmentnamen</i>)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxDMetaDes" value="<?php echo $mpTxDMetaDes?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>Inserat zum Thema #S</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Marktplatz eigenständig
mit der umhüllenden HTML-Schablone <i>mpSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die Angabe zum Seitentitel der Detailseite
über den Platzhalter <i>{TITLE}</i> der HTML-Schablone <i>mpSeite.htm</i> zusätzlich befüllen.<br>
Bevorzugt entnimmt der Marktplatz die Angabe einem Textfeld <i>TITLE</i> der Inseratestruktur.
Ist ein solches Feld nicht vorhanden oder nicht ausgefüllt so kann der Marktplatz die Titelinformation
alternativ aus folgenden Quellen entnehmen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><div><input type="radio" class="admRadio" name="DTitelQuelle" value="1"<?php if($mpDTitelQuelle=='1') echo ' checked="checked"'?> /> die ersten <input type="text" name="DTitelWL" value="<?php echo $mpDTitelWL?>" size="2" style="width:24px;" /> (1...5) Worte des ersten Feldes vom Typ Text im Inserat</div>
 <div><input type="radio" class="admRadio" name="DTitelQuelle" value="2"<?php if($mpDTitelQuelle=='2') echo ' checked="checked"'?> /> die ersten <input type="text" name="DTitelZL" value="<?php echo $mpDTitelZL?>" size="2" style="width:24px;" /> (1...80) Zeichen des ersten Feldes vom Typ Text im Inserat</div>
 <div><input type="radio" class="admRadio" name="DTitelQuelle" value="0"<?php if($mpDTitelQuelle=='0') echo ' checked="checked"'?> /> der folgende Text</div>
 <div><input type="text" name="TxDMetaTit" value="<?php echo $mpTxDMetaTit?>" style="width:99%" /></div>
 <div class="admMini">Beispiel: <i>:: Inserat zu #S</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über und/oder unter der Inserateanzeige im Besucherbereich
kann eine Navigationsleiste zum Vorwärts-/Rückwärtsblättern angezeigt werden.
An welchen Positionen soll eine solche Navigationsleiste erscheinen?</td></tr>
<tr class="admTabl">
 <td>Navigator oberhalb<br />der Inseratedaten</td>
 <td colspan="2" width="80%"><select name="DetailNaviOben" size="1" style="width:290px;"><option value="0">obere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($mpDetailNaviOben==1) echo ' selected="selected"'?>>Navigator über der Textmeldung</option><option value="2"<?php if($mpDetailNaviOben==2) echo ' selected="selected"'?>>Navigator unmittelbar über den Inseratedetails</option></select></td>
</tr>
<tr class="admTabl">
 <td>Navigator unterhalb<br />der Inseratedaten</td>
 <td colspan="2"><select name="DetailNaviUnten" size="1" style="width:290px;"><option value="0">untere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($mpDetailNaviUnten==1) echo ' selected="selected"'?>>Navigator unmittelbar unter den Inseratedaten</option></select></td>
</tr>
<!-- <tr class="admTabl">
 <td>Navigatorstil</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DetailNaviBild" value="1"<?php if($mpDetailNaviBild) echo ' checked="checked"'?> /> die Navigationsleiste soll grafisch unterlegt sein</td>
</tr> -->

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inseratedetails werden normalerweise im selben Fenster wie die Inserateübersicht präsentiert.
Abweichend davon können die Inseratedetails in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td valign="top" style="padding-top:5px;">Detaildarstellung</td>
 <td colspan="2" style="padding-top:5px;"><input type="radio" class="admRadio" name="DetailPopup" value=""<?php if(!$mpDetailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="DetailPopup" value="1"<?php if($mpDetailPopup) echo ' checked="checked"'?> /> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $mpPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $mpPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Das Inserat wird normalerweise in einer automatisch generierten Tabelle dargestellt,
die in der linken Spalte den jeweiligen Feldnamen und in der rechten Spalte die zugehörigen Feldinhalte anzeigt.
Die Auswahl der Detailzeilen erfolgt segmentabhängig über den Menüpunkt <i>Segmenteigenschaften</i>.
Sie können statt dessen eine eigene Layoutschablone für die Inseratedetails verwenden, in der eine andere Anordnung realisiert wird.
Diese Layoutschablone im HTML-Format mit dem allgemeingültigen Namen <i>mpDetailZeilen.htm</i> bzw. eine segmentspezifische Schablone namens <i>mpDetailXXZeilen.htm</i> (wobei <i>XX</i> die Nummer des jeweiligen Segmentes ist) müssen Sie jedoch zuvor manuell anfertigen.
<a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td>Detaillayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneDetails" value=""<?php if(!$mpEigeneDetails) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneDetails" value="1"<?php if($mpEigeneDetails) echo ' checked="checked"'?>/> eigene Detailschablone <i>mpDetailZeilen.htm</i> verwenden</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls ein beliebiges Inserat leere Felder enthält
können diese als Felder mit leerem Inhalt angezeigt oder die betreffenden leeren Zeilen aus der Detailanzeige ausgeblendet werden.</td></tr>
<tr class="admTabl">
 <td>leere Zeilen</td>
 <td colspan="2"><input type="radio" class="admRadio" name="ZeigeLeeres" value=""<?php if(!$mpZeigeLeeres) echo ' checked="checked"'?> /> leere Zeilen nicht darstellen &nbsp; <input type="radio" class="admRadio" name="ZeigeLeeres" value="1"<?php if($mpZeigeLeeres) echo ' checked="checked"'?>/> leere Zeilen anzeigen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Der Inseratemarkt kann mit einer Benutzerverwaltung kombiniert sein.
Damit ergeben sich unterschiedliche Darstellungs-Möglichkeiten in der Detailanzeige eines Inserates für unangemeldete Gäste und für angemeldete Benutzer.
Hier entscheiden Sie, ob überhaupt Unterschiede bei der Darstellung gemacht werden sollen.
Welche Datenzeilen das jeweils betrifft stellen Sie für jedes Marktsegment unter <i>Segmenteigenschaften</i> ein.</td></tr>
<tr class="admTabl">
 <td>Benutzeransicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NDetailAnders" value="1"<?php if($mpNDetailAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Datenzeilen sehen als Gäste</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls in der Inseratestruktur ein Feld vom Typ Benutzer enthalten ist und dieses bei den Inseratedetails angezeigt wird kann dessen anzuzeigender Inhalt festgelegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td colspan="2"><select name="NutzerDetailFeld" style="width:140px;"><?php echo str_replace('"'.$mpNutzerDetailFeld.'"','"'.$mpNutzerDetailFeld.'" selected="selected"',$sNOpt)?></select> (Anzeige für unangemeldete Gäste)
 <div><select name="NNutzerDetailFeld" style="width:140px;"><?php echo str_replace('"'.$mpNNutzerDetailFeld.'"','"'.$mpNNutzerDetailFeld.'" selected="selected"',$sNOpt)?></select> (Anzeige für angemeldete Benutzer)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Inserat kann eine zusätzliche Zeile mit einem Klickschalter
für das Versenden einer Information über das Inserat an einen beliebigen E-Mail-Empfänger enthalten (tell-a-friend-Funktion).
Vor welcher Zeile soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Informationsfunktion</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconInfo.gif" width="16" height="16" border="0" align="bottom" title="Info">-Infozeile einfügen vor Zeile <select name="DetailInfo" size="1"><?php echo str_replace('"'.$mpDetailInfo.'"','"'.$mpDetailInfo.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastDInfo" value="1"<?php if($mpGastDInfo) echo ' checked="checked"'?> /> auch für Gäste,
 Zeilenbeschriftung <input type="text" name="TxInfoSenden" value="<?php echo $mpTxInfoSenden?>" size="15" style="width:120px;" /></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Zeilen 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Inserat kann eine zusätzliche Zeile mit einem Klickschalter
für den direkten Aufruf des Änderungsformulars oder Kopierformulars für das Inserat enthalten.
Vor welcher Zeile soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconAendern.gif" width="12" height="13" border="0" align="top" title="Ändern">-Ändern-Klickschalter
oder <img src="<?php echo MPPFAD;?>grafik/iconKopie.gif" width="12" height="13" border="0" align="top" title="Ändern">-Kopier-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungszeile</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconAendern.gif" width="12" height="13" border="0" align="bottom" title="Ändern">-Klickzeile einfügen vor Zeile <select name="DetailAendern" size="1"><?php echo str_replace('"'.$mpDetailAendern.'"','"'.$mpDetailAendern.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastDAendern" value="1"<?php if($mpGastDAendern) echo ' checked="checked"'?> /> auch für Gäste</td>
 <td rowspan="2" class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Zeilen 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopierzeile</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconKopie.gif" width="12" height="13" border="0" align="bottom" title="Ändern">-Klickzeile einfügen vor Zeile <select name="DetailKopieren" size="1"><?php echo str_replace('"'.$mpDetailKopieren.'"','"'.$mpDetailKopieren.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastDKopieren" value="1"<?php if($mpGastDKopieren) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Inserat kann eine zusätzliche Zeile mit einem Klickschalter
für einen Benachrichtigungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er bei eventuellen Inserateänderungen oder Inseratelöschungen eine Benachritigungs-E-Mail zum betreffenden Inserat erhält.
Vor welcher Zeile soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />service</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="bottom" title="Benachrichtigung">-Klickzeile einfügen vor Zeile <select name="DetailBenachr" size="1"><?php echo str_replace('"'.$mpDetailBenachr.'"','"'.$mpDetailBenachr.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastDBenachr" value="1"<?php if($mpGastDBenachr) echo ' checked="checked"'?> /> auch für Gäste,
 Zeilenbeschriftung <input type="text" name="TxBenachrService" value="<?php echo $mpTxBenachrService?>" size="15" style="width:123px;" /></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Zeilen 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Inserat kann eine zusätzliche Zeile mit einem Klickschalter
zum Drucken des Inserates enthalten.
Vor welcher Zeile soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconDrucken.gif" width="16" height="14" border="0" align="top" title="Drucken">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druckzeile</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconDrucken.gif" width="16" height="14" border="0" align="bottom" title="Drucken">-Druckzeile einfügen vor Zeile <select name="DetailDrucken" size="1"><?php echo str_replace('"'.$mpDetailDrucken.'"','"'.$mpDetailDrucken.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastDDrucken" value="1"<?php if($mpGastDDrucken) echo ' checked="checked"'?> /> auch für Gäste</div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Zeilen 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>
<tr class="admTabl">
 <td>Drucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckDFarbig" value="0"<?php if(!$mpDruckDFarbig) echo ' checked="checked"'?> /> simpel (im CSS-Stil <i>div.mpTbZlDr</i>) &nbsp; <input type="radio" class="admRadio" name="DruckDFarbig" value="1"<?php if($mpDruckDFarbig) echo ' checked="checked"'?> /> formatiert (im CSS-Stil der Bildschirm-Detailanzeige)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DruckDMailOffen" value="1"<?php if($mpDruckDMailOffen) echo ' checked="checked"'?> /> E-Mail-Adressen offen lesbar im Detaildruck darstellen
 <div class="admMini">Empfehlung: möglichst <i>nicht</i> aktivieren, weil auch Roboter/Spider die Druckseite einsehen könnten</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Bild</i> enthält
und kein Bild zum Inserat hochgeladen wurde kann bei der Anzeige der Inseratedetails statt des Bildes
ein Ersatzbild aus dem Ordner <i>grafik</i> angezeigt werden.</td></tr>
<tr class="admTabl">
 <td>Ersatzbild</td>
 <td colspan="2"><select name="ErsatzBildGross" size="1" style="width:180px;"><option value="">kein Ersatzbild anzeigen</option><option value="kein_Bild.jpg"<?php if($mpErsatzBildGross=='kein_Bild.jpg') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.jpg</option><option value="kein_Bild.gif"<?php if($mpErsatzBildGross=='kein_Bild.gif') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.gif</option><option value="kein_Bild.png"<?php if($mpErsatzBildGross=='kein_Bild.png') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.png</option></select></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Link</i> enthält
und diese Felder in der Detailanzeige aktiviert sind wird ein solcher Link normalerweise in textlicher Langform dargestellt.
Der Link kann aber auch in Kurzformform mit Darstellung als Symbol erscheinen.</td></tr>
<tr class="admTabl">
 <td>Linkdarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DetailLinkSymbol" value=""<?php if(!$mpDetailLinkSymbol) echo ' checked="checked"'?> /> Langform mit Adressangabe &nbsp; <input type="radio" class="admRadio" name="DetailLinkSymbol" value="1"<?php if($mpDetailLinkSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo MPPFAD;?>grafik/iconLink.gif" width="16" height="16" border="0" title="Link">-Symbol</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Datei</i> enthält
und diese Felder in der Detailanzeige aktiviert sind wird eine solche Datei normalerweise durch den Dateinamen dargestellt.
Die Anzeige kann aber auch in Kurzformform als Symbol erscheinen.</td></tr>
<tr class="admTabl">
 <td>Dateidarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DetailDateiSymbol" value=""<?php if(!$mpDetailDateiSymbol) echo ' checked="checked"'?> /> Langform mit Dateiname &nbsp; <input type="radio" class="admRadio" name="DetailDateiSymbol" value="1"<?php if($mpDetailDateiSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo MPPFAD;?>grafik/dateiDat.gif" width="16" height="16" border="0" title="Datei">-Symbol</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>