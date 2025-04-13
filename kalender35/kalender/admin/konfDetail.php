<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Termindetails anpassen','','KDt');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Termindetails.</p>';
 $ksTxDetails=KAL_TxDetails; $ksZeigeLeeres=KAL_ZeigeLeeres; $ksEigeneDetails=KAL_EigeneDetails; $ksEigeneDruckDetails=KAL_EigeneDruckDetails; $ksMonatDLang=KAL_MonatDLang;
 $ksTxDMetaKey=KAL_TxDMetaKey; $ksTxDMetaDes=KAL_TxDMetaDes; $ksTxDMetaTit=KAL_TxDMetaTit;
 $ksDTitelQuelle=KAL_DTitelQuelle; $ksDTitelWL=KAL_DTitelWL; $ksDTitelZL=KAL_DTitelZL;
 $ksDetailPopup=KAL_DetailPopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch;
 $ksDetailNaviOben=KAL_DetailNaviOben; $ksDetailNaviUnten=KAL_DetailNaviUnten; $ksDetailNaviBild=KAL_DetailNaviBild;
 $ksNummerStellen=KAL_NummerStellen; $ksTxNummer=KAL_TxNummer; $ksNDetailAnders=KAL_NDetailAnders;
 $ksDetailLinkSymbol=KAL_DetailLinkSymbol; $ksDetailDateiSymbol=KAL_DetailDateiSymbol;
 $ksErsatzBildGross=KAL_ErsatzBildGross; $ksTxInfoSenden=KAL_TxInfoSenden; $ksTxErinnService=KAL_TxErinnService; $ksTxBenachrService=KAL_TxBenachrService;
 $ksDetailInfo=KAL_DetailInfo; $ksGastDInfo=KAL_GastDInfo;
 $ksDetailAendern=KAL_DetailAendern; $ksGastDAendern=KAL_GastDAendern; $ksDetailKopieren=KAL_DetailKopieren; $ksGastDKopieren=KAL_GastDKopieren;
 $ksDetailErinn=KAL_DetailErinn; $ksGastDErinn=KAL_GastDErinn; $ksDetailBenachr=KAL_DetailBenachr; $ksGastDBenachr=KAL_GastDBenachr;
 $ksDetailCal=KAL_DetailCal; $ksGastDCal=KAL_GastDCal; $ksTxCalZeile=KAL_TxCalZeile;
 $ksDetailDrucken=KAL_DetailDrucken; $ksGastDDrucken=KAL_GastDDrucken; $ksDruckDFarbig=KAL_DruckDFarbig; $ksDruckDMailOffen=KAL_DruckDMailOffen;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=txtVar('TxDetails'); if(fSetzKalWert($v,'TxDetails','"')) $bNeu=true;
 $v=txtVar('TxDMetaKey'); if(fSetzKalWert($v,'TxDMetaKey','"')) $bNeu=true;
 $v=txtVar('TxDMetaDes'); if(fSetzKalWert($v,'TxDMetaDes','"')) $bNeu=true;
 $v=txtVar('TxDMetaTit'); if(fSetzKalWert($v,'TxDMetaTit','"')) $bNeu=true;
 $v=(int)txtVar('DTitelQuelle'); if(fSetzKalWert($v,'DTitelQuelle','')) $bNeu=true;
 $v=min(max(txtVar('DTitelWL'),1),5);  if(fSetzKalWert($v,'DTitelWL','')) $bNeu=true;
 $v=min(max(txtVar('DTitelZL'),1),80); if(fSetzKalWert($v,'DTitelZL','')) $bNeu=true;
 $v=(int)txtVar('DetailNaviOben');  if(fSetzKalWert($v,'DetailNaviOben','')) $bNeu=true;
 $v=(int)txtVar('DetailNaviUnten'); if(fSetzKalWert($v,'DetailNaviUnten','')) $bNeu=true;
 $v=txtVar('DetailNaviBild');   if(fSetzKalWert(($v?true:false),'DetailNaviBild','')) $bNeu=true;
 $v=(int)txtVar('DetailPopup'); if(fSetzKalWert(($v?true:false),'DetailPopup','')) $bNeu=true;
 $v=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($v,'PopupBreit','')) $bNeu=true;
 $v=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($v,'PopupHoch','')) $bNeu=true;
 $v=txtVar('EigeneDetails'); if(fSetzKalWert(($v?true:false),'EigeneDetails','')) $bNeu=true;
 $v=txtVar('EigeneDruckDetails'); if(fSetzKalWert(($v?true:false),'EigeneDruckDetails','')) $bNeu=true;
 $v=(int)txtVar('MonatDLang'); if(fSetzKalWert($v,'MonatDLang','')) $bNeu=true;
 $v=max((int)txtVar('NummerStellen'),1); if(fSetzKalWert($v,'NummerStellen','')) $bNeu=true;
 $v=txtVar('TxNummer'); if(fSetzKalWert($v,'TxNummer','"')) $bNeu=true;
 $v=txtVar('NDetailAnders');if(fSetzKalWert(($v?true:false),'NDetailAnders','')) $bNeu=true;
 $kal_DetailFeld=array(); $kal_NDetailFeld=array(); $kal_ZeilenStil=array();
 for($i=0;$i<$nFelder;$i++){
  $kal_DetailFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_NDetailFeld[$i]=(isset($_POST['N'.$i])?(int)$_POST['N'.$i]:0);
  $kal_ZeilenStil[$i]=(isset($_POST['Z'.$i])?str_replace("'",'"',stripslashes($_POST['Z'.$i])):'');
 }
 if(fSetzArray($kal_DetailFeld,'DetailFeld','')) $bNeu=true; if(fSetzArray($kal_NDetailFeld,'NDetailFeld','')) $bNeu=true;
 if(fSetzArray($kal_ZeilenStil,'ZeilenStil',"'")) $bNeu=true;
 $v=txtVar('ZeigeLeeres'); if(fSetzKalWert(($v?true:false),'ZeigeLeeres','')) $bNeu=true;
 $v=txtVar('DetailLinkSymbol'); if(fSetzKalWert(($v?true:false),'DetailLinkSymbol','')) $bNeu=true;
 $v=txtVar('DetailDateiSymbol'); if(fSetzKalWert(($v?true:false),'DetailDateiSymbol','')) $bNeu=true;
 $v=txtVar('ErsatzBildGross'); if(fSetzKalWert($v,'ErsatzBildGross',"'")) $bNeu=true;
 $v=(int)txtVar('DetailInfo'); if(fSetzKalWert($v,'DetailInfo','')) $bNeu=true;
 $v=txtVar('GastDInfo'); if(fSetzKalWert(($v?true:false),'GastDInfo','')) $bNeu=true;
 $v=(int)txtVar('DetailErinn'); if(fSetzKalWert($v,'DetailErinn','')) $bNeu=true;
 $v=txtVar('GastDErinn'); if(fSetzKalWert(($v?true:false),'GastDErinn','')) $bNeu=true;
 $v=(int)txtVar('DetailBenachr'); if(fSetzKalWert($v,'DetailBenachr','')) $bNeu=true;
 $v=txtVar('GastDBenachr'); if(fSetzKalWert(($v?true:false),'GastDBenachr','')) $bNeu=true;
 $v=(int)txtVar('DetailCal'); if(fSetzKalWert($v,'DetailCal','')) $bNeu=true;
 $v=(int)txtVar('GastDCal'); if(fSetzKalWert(($v?true:false),'GastDCal','')) $bNeu=true;
 $v=txtVar('TxCalZeile'); if(fSetzKalWert($v,'TxCalZeile',"'")) $bNeu=true;
 $v=(int)txtVar('DetailAendern'); if(fSetzKalWert($v,'DetailAendern','')) $bNeu=true;
 $v=(int)txtVar('GastDAendern'); if(fSetzKalWert(($v?true:false),'GastDAendern','')) $bNeu=true;
 $v=(int)txtVar('DetailKopieren'); if(fSetzKalWert($v,'DetailKopieren','')) $bNeu=true;
 $v=(int)txtVar('GastDKopieren'); if(fSetzKalWert(($v?true:false),'GastDKopieren','')) $bNeu=true;
 $v=(int)txtVar('DetailDrucken'); if(fSetzKalWert($v,'DetailDrucken','')) $bNeu=true;
 $v=txtVar('GastDDrucken'); if(fSetzKalWert(($v?true:false),'GastDDrucken','')) $bNeu=true;
 $v=(int)txtVar('DruckDFarbig'); if(fSetzKalWert(($v?true:false),'DruckDFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckDMailOffen'); if(fSetzKalWert(($v?true:false),'DruckDMailOffen','')) $bNeu=true;
 $v=txtVar('TxInfoSenden'); if(fSetzKalWert($v,'TxInfoSenden','"')) $bNeu=true;
 $v=txtVar('TxErinnService'); if(fSetzKalWert($v,'TxErinnService','"')) $bNeu=true;
 $v=txtVar('TxBenachrService'); if(fSetzKalWert($v,'TxBenachrService','"')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Detaileinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Detaileinstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
echo $Msg.NL;
?>

<form action="konfDetail.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="3" class="admSpa2">Über den Termindetails wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift</td>
 <td colspan="2"><input type="text" name="TxDetails" value="<?php echo $ksTxDetails?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Informationen zum Termin #</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Inseratelistenseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i>
mit folgenden Texten zusätzlich füllen, sofern nicht beim einzelnen Terminen
individuelle Angaben für die beiden Felder <i>META-KEY</i> und <i>META-DES</i> angegeben sind.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxDMetaKey" value="<?php echo $ksTxDMetaKey?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Veranstaltungsdetails Termineinzelheiten</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxDMetaDes" value="<?php echo $ksTxDMetaDes?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Veranstaltungsdetails</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die Angabe zum Seitentitel der Detailseite
über den Platzhalter <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i> zusätzlich befüllen.<br>
Bevorzugt entnimmt der Kalender die Angabe einem Textfeld <i>TITLE</i> der Terminstruktur.
Ist ein solches Feld nicht vorhanden oder nicht ausgefüllt so kann der Kalender die Titelinformation
alternativ aus folgenden Quellen entnehmen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><div><input type="radio" class="admRadio" name="DTitelQuelle" value="1"<?php if($ksDTitelQuelle=='1') echo ' checked="checked"'?> /> die ersten <input type="text" name="DTitelWL" value="<?php echo $ksDTitelWL?>" size="2" style="width:24px;" /> (1...5) Worte des ersten Feldes vom Typ Text im Inserat</div>
 <div><input type="radio" class="admRadio" name="DTitelQuelle" value="2"<?php if($ksDTitelQuelle=='2') echo ' checked="checked"'?> /> die ersten <input type="text" name="DTitelZL" value="<?php echo $ksDTitelZL?>" size="2" style="width:24px;" /> (1...80) Zeichen des ersten Feldes vom Typ Text im Inserat</div>
 <div><input type="radio" class="admRadio" name="DTitelQuelle" value="0"<?php if($ksDTitelQuelle=='0') echo ' checked="checked"'?> /> der folgende Text</div>
 <div><input type="text" name="TxDMetaTit" value="<?php echo $ksTxDMetaTit?>" style="width:100%" /></div>
 <div class="admMini">Beispiel: <i>Termindetail</i></div></td>
</tr>


<tr class="admTabl"><td colspan="3" class="admSpa2">Über und/oder unter der Terminanzeige im Besucherbereich
kann eine Navigationsleiste zum Vorwärts-/Rückwärtsblättern angezeigt werden.
An welchen Positionen soll eine solche Navigationsleiste erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator oberhalb<br />der Termindaten</td>
 <td colspan="2"><select name="DetailNaviOben" size="1" style="width:290px;"><option value="0">obere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksDetailNaviOben==1) echo ' selected="selected"'?>>Navigator über der Textmeldung</option><option value="2"<?php if($ksDetailNaviOben==2) echo ' selected="selected"'?>>Navigator unmittelbar über den Termindetails</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator unterhalb<br />der Termindaten</td>
 <td colspan="2"><select name="DetailNaviUnten" size="1" style="width:290px;"><option value="0">untere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksDetailNaviUnten==1) echo ' selected="selected"'?>>Navigator unmittelbar unter den Termindaten</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigatorstil</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DetailNaviBild" value="1"<?php if($ksDetailNaviBild) echo ' checked="checked"'?> /> die Navigationsleiste soll grafisch unterlegt sein</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Termindetails werden normalerweise im selben Fenster wie die Terminübersicht präsentiert.
Abweichend davon können die Termindetails in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Detaildarstellung</td>
 <td colspan="2" style="padding-top:5px;"><input type="radio" class="admRadio" name="DetailPopup" value=""<?php if(!$ksDetailPopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="DetailPopup" value="1"<?php if($ksDetailPopup) echo ' checked="checked"'?> /> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Der Termin wird normalerweise in einer automatisch generierten Tabelle dargestellt,
die in der linken Spalte den jeweiligen Feldnamen und in der rechten Spalte die zugehörigen Feldinhalte anzeigt.
Sie können statt dessen eine eigene Layoutschablone für die Termindetails verwenden, in der eine andere Anordnung realisiert wird.
Diese Layoutschablone im HTML-Format mit dem Namen <i>kalDetail.htm</i> müssen Sie jedoch zuvor manuell anfertigen.
<a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.7.Eigene" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Detaillayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneDetails" value=""<?php if(!$ksEigeneDetails) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneDetails" value="1"<?php if($ksEigeneDetails) echo ' checked="checked"'?>/> eigene Detailschablone <i>kalDetail.htm</i> verwenden</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Monatsangabe innerhalb der Datumsanzeige in der Detailseite kann als zweistellige Zahl oder als ausgeschriebener Monatsname erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatsformat</td>
 <td colspan="2"><input type="radio" class="admRadio" name="MonatDLang" value="0"<?php if($ksMonatDLang<1) echo ' checked="checked"'?> /> Monat als Zahl &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatDLang" value="1"<?php if($ksMonatDLang==1) echo ' checked="checked"'?>/> Monatsname kurz &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatDLang" value="2"<?php if($ksMonatDLang==2) echo ' checked="checked"'?>/> Monatsname lang &nbsp; &nbsp; (<span class="admMini">Empfehlung: <i>als Zahl</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige im Besucherbereich kann bezüglich der anzuzeigenden Felder konfiguriert werden.
Welche Felder (getrennt für Gäste und gegebenefalls für angemeldete Benutzer) der aktuellen Terminstruktur sollen in der Detailanzeige wie erscheinen?
(<span class="admMini"><u>Achtung</u>: Die folgenden Einstellungen gelten <i>nur</i> für das automatische Standardlayout</span>)</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Detailansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NDetailAnders" value="1"<?php if($ksNDetailAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Datenzeilen sehen als Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">&nbsp;</td>
 <td>Feld anzeigen für Gäste / für angemeldete Benutzer</td>
 <td style="width:2%">optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.7.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="vertical-align:bottom;">laufende Nummer <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.3.Nummer" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a><div class="admMini">Typ <i>Zählnummer</i></div></td>
 <td valign="bottom" style="width:350px;">00) <input class="admCheck" type="checkbox" name="F0" value="1"<?php if($kal_DetailFeld[0]==1) echo' checked="checked"'?>> / <input class="admCheck" type="checkbox" name="N0" value="1"<?php if($kal_NDetailFeld[0]==1) echo' checked="checked"'?>>
 mit <input type="text" name="NummerStellen" value="<?php echo $ksNummerStellen?>" size="1" style="width:18px;" /> stelliger lfd. Nr.
 als <input type="text" name="TxNummer" value="<?php echo $ksTxNummer?>" size="15" style="width:100px;" /></td>
 <td><input type="text" name="Z0" style="width:220px;" value="<?php echo $kal_ZeilenStil[0]?>" /></td>
</tr>
<?php
 include('feldtypenInc.php');
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if($t=='u'){
   array_splice($kal_NutzerFelder,1,1); $nNFz=count($kal_NutzerFelder);
   $sNOpt='<option value="0">--</option><option value="2">'.$kal_NutzerFelder[2].'</option>';
   for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.$kal_NutzerFelder[$j].'</option>';
  }
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo $kal_FeldName[$i]?> <div class="admMini">(Typ <i><?php echo $aTyp[$t]?></i>)</div></td>
 <td><?php echo sprintf('%02d',$i).')&nbsp;'.($t!='c'&&$t!='u'&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'?'<input class="admCheck" type="checkbox" name="F'.$i.'" value="1"'.($kal_DetailFeld[$i]==1?' checked="checked"':'').'> / <input class="admCheck" type="checkbox" name="N'.$i.'" value="1"'.($kal_NDetailFeld[$i]==1?' checked="checked"':'').'>':($t!='u'?'&nbsp;--':'<select name="F'.$i.'" size="1" style="width:140px;">'.str_replace('value="'.$kal_DetailFeld[$i].'"','value="'.$kal_DetailFeld[$i].'" selected="selected"',$sNOpt).'</select> / <select name="N'.$i.'" size="1" style="width:140px;">'.str_replace('value="'.$kal_NDetailFeld[$i].'"','value="'.$kal_NDetailFeld[$i].'" selected="selected"',$sNOpt).'</select>'))?></td>
 <td><input type="text" name="Z<?php echo $i?>" style="width:220px;" value="<?php echo $kal_ZeilenStil[$i]?>" /></td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls ein beliebiger Detailtermin leere Felder enthält
können diese als Felder mit leerem Inhalt angezeigt oder die betreffenden leeren Zeilen aus der Detailanzeige ausgeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">leere Zeilen</td>
 <td colspan="2"><input type="radio" class="admRadio" name="ZeigeLeeres" value=""<?php if(!$ksZeigeLeeres) echo ' checked="checked"'?> /> leere Zeilen nicht darstellen &nbsp; <input type="radio" class="admRadio" name="ZeigeLeeres" value="1"<?php if($ksZeigeLeeres) echo ' checked="checked"'?>/> leere Zeilen anzeigen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter
für das Versenden einer Information über den Termin an einen beliebigen E-Mail-Empfänger enthalten (tell-a-friend-Funktion).<br />
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Informationsfunktion</td>
 <td colspan="2">zusätzliche Infozeile vor Zeile <select name="DetailInfo" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailInfo==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxInfoSenden" value="<?php echo $ksTxInfoSenden?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDInfo" value="1"<?php if($ksGastDInfo) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter
für einen Erinnerungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er einige Tage vor dem Termin eine Erinnerungs-E-Mail zum betreffenden Termin erhält.<br />
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/iconErinnern.gif" width="16" height="16" border="0" align="top" title="Erinnerung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungsservice</td>
 <td colspan="2">zusätzliche Servicezeile vor Zeile <select name="DetailErinn" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailErinn==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxErinnService" value="<?php echo $ksTxErinnService?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDErinn" value="1"<?php if($ksGastDErinn) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter
für den direkten Aufruf des Änderungsformulars oder Kopierformulars für den Termin enthalten.
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" align="top" title="Ändern">-Ändern-Klickschalter
oder <img src="<?php echo $sHttp?>grafik/icon_Kopie.gif" width="12" height="13" border="0" align="top" title="Ändern">-Kopier-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungszeile</td>
 <td colspan="2">zusätzliche Klickzeile vor Zeile <select name="DetailAendern" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailAendern==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDAendern" value="1"<?php if($ksGastDAendern) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopierzeile</td>
 <td colspan="2">zusätzliche Klickzeile vor Zeile <select name="DetailKopieren" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailKopieren==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDKopieren" value="1"<?php if($ksGastDKopieren) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter
für einen Benachrichtigungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er bei eventuellen Terminänderungen oder Terminlöschungen eine Benachritigungs-E-Mail zum betreffenden Termin erhält.<br />
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />service</td>
 <td colspan="2">zusätzliche Servicezeile vor Zeile <select name="DetailBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxBenachrService" value="<?php echo $ksTxBenachrService?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDBenachr" value="1"<?php if($ksGastDBenachr) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter für einen Terminexport enthalten.
Über diese Funktion kann der Besucher den jeweiligen Termin im iCal-Format exportieren und in eine Kalenderapplikation auf seinem Endgerät importieren/einfügen.<br />
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo KAL_TxCalIcon?>">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">iCal-Export</td>
 <td colspan="2">zusätzliche Servicezeile vor Zeile <select name="DetailCal" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailCal==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxCalZeile" value="<?php echo $ksTxCalZeile?>" size="15" style="width:8em;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDCal" value="1"<?php if($ksGastDCal) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>


<tr class="admTabl"><td colspan="3" class="admSpa2">Die Detailanzeige zum Termin kann eine zusätzliche Zeile mit einem Klickschalter
zum Drucken des Termins enthalten.
Vor welcher Zeile soll ein solcher <img src="<?php echo $sHttp?>grafik/iconDrucken.gif" width="16" height="16" border="0" align="top" title="Drucken">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druckzeile</td>
 <td colspan="2">zusätzliche Druckzeile vor Zeile <select name="DetailDrucken" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailDrucken==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastDDrucken" value="1"<?php if($ksGastDDrucken) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Drucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneDruckDetails" value=""<?php if(!$ksEigeneDruckDetails) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneDruckDetails" value="1"<?php if($ksEigeneDruckDetails) echo ' checked="checked"'?>/> eigene Detailschablone <i>kalDruckDetail.htm</i> verwenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standarddrucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckDFarbig" value="0"<?php if(!$ksDruckDFarbig) echo ' checked="checked"'?> /> simpel &nbsp; &nbsp; <input type="radio" class="admRadio" name="DruckDFarbig" value="1"<?php if($ksDruckDFarbig) echo ' checked="checked"'?> /> formatiert (im CSS-Stil der Bildschirm-Detailanzeige)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DruckDMailOffen" value="1"<?php if($ksDruckDMailOffen) echo ' checked="checked"'?> /> E-Mail-Adressen offen lesbar im Detaildruck darstellen
 <div class="admMini">Empfehlung: möglichst <i>nicht</i> aktivieren, weil auch Roboter/Spider die Druckseite einsehen könnten</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Bild</i> enthält
und kein Bild zum Termin hochgeladen wurde kann in der Terminanzeige statt des Bildes
ein Ersatzbild aus dem Ordner <i>grafik</i> angezeigt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ersatzbild</td>
 <td colspan="2"><select name="ErsatzBildGross" size="1" style="width:180px;"><option value="">kein Ersatzbild anzeigen</option><option value="kein_Bild.jpg"<?php if($ksErsatzBildGross=='kein_Bild.jpg') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.jpg</option><option value="kein_Bild.gif"<?php if($ksErsatzBildGross=='kein_Bild.gif') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.gif</option><option value="kein_Bild.png"<?php if($ksErsatzBildGross=='kein_Bild.png') echo ' selected="selected"'?>>Ersatzbild: kein_Bild.png</option></select></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Link</i> enthält
und diese Felder in der Detailanzeige aktiviert sind wird ein solcher Link normalerweise in textlicher Langform dargestellt.
Der Link kann aber auch in Kurzformform mit Darstellung als Symbol erscheinen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkdarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DetailLinkSymbol" value=""<?php if(!$ksDetailLinkSymbol) echo ' checked="checked"'?> /> Langform mit Adressangabe &nbsp; <input type="radio" class="admRadio" name="DetailLinkSymbol" value="1"<?php if($ksDetailLinkSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo $sHttp?>grafik/iconLink.gif" width="16" height="16" border="0" title="Link">-Symbol</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Datei</i> enthält
und diese Felder in der Detailanzeige aktiviert sind wird eine solche Datei normalerweise durch den Dateinamen dargestellt.
Die Anzeige kann aber auch in Kurzformform als Symbol erscheinen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Dateidarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DetailDateiSymbol" value=""<?php if(!$ksDetailDateiSymbol) echo ' checked="checked"'?> /> Langform mit Dateiname &nbsp; <input type="radio" class="admRadio" name="DetailDateiSymbol" value="1"<?php if($ksDetailDateiSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo $sHttp?>grafik/dateiDat.gif" width="16" height="16" border="0" title="Datei">-Symbol</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>