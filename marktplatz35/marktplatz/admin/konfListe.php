<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserateliste kofigurieren','','KIl');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für die Inserateliste. <a href="'.AM_Hilfe.'LiesMich.htm#2.6" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpRueckwaerts=MP_Rueckwaerts; $mpArchivRueckwaerts=MP_ArchivRueckwaerts; $mpTxListGsmt=MP_TxListGsmt; $mpTxListSuch=MP_TxListSuch;
 $mpTxLMetaKey=MP_TxLMetaKey; $mpTxLMetaDes=MP_TxLMetaDes; $mpTxLMetaTit=MP_TxLMetaTit;
 $mpListenLaenge=MP_ListenLaenge; $mpSuchFilter=MP_SuchFilter; $mpEigeneZeilen=MP_EigeneZeilen; $mpSegTrnZeile=MP_SegTrnZeile;
 $mpNaviOben=MP_NaviOben; $mpNaviUnten=MP_NaviUnten; $mpNaviBild=MP_NaviBild;
 $mpListeVertikal=MP_ListeVertikal; $mpNListeAnders=MP_NListeAnders; $mpNVerstecktSehen=MP_NVerstecktSehen;
 $mpListenInfo=MP_ListenInfo; $mpListenInfTitel=MP_ListenInfTitel; $mpGastLInfo=MP_GastLInfo;
 $mpListenBenachr=MP_ListenBenachr; $mpListenBenachTitel=MP_ListenBenachTitel; $mpGastLBenachr=MP_GastLBenachr;
 $mpListenAendern=MP_ListenAendern; $mpListenAendTitel=MP_ListenAendTitel; $mpGastLAendern=MP_GastLAendern;
 $mpListenKopieren=MP_ListenKopieren; $mpListenKopieTitel=MP_ListenKopieTitel; $mpGastLKopieren=MP_GastLKopieren; $mpListenAendKopieren=MP_ListenAendKopieren;
 $mpNutzerListFeld=MP_NutzerListFeld; $mpNNutzerListFeld=MP_NNutzerListFeld;
 $mpLinkSymbol=MP_LinkSymbol; $mpErsatzBildKlein=MP_ErsatzBildKlein;
 $mpListenMemoLaenge=MP_ListenMemoLaenge; $mpDruckLMemoLaenge=MP_DruckLMemoLaenge;
 $mpDruckLFarbig=MP_DruckLFarbig; $mpDruckLMemo=MP_DruckLMemo; $mpDruckLMemo=MP_DruckLMemo; $mpDruckLMailOffen=MP_DruckLMailOffen;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=(int)txtVar('Rueckwaerts'); if(fSetzMPWert(($v?true:false),'Rueckwaerts','')) $bNeu=true;
 $v=(int)txtVar('ArchivRueckwaerts'); if(fSetzMPWert(($v?true:false),'ArchivRueckwaerts','')) $bNeu=true;
 $v=txtVar('TxListGsmt'); if(fSetzMPWert($v,'TxListGsmt','"')) $bNeu=true;
 $v=txtVar('TxListSuch'); if(fSetzMPWert($v,'TxListSuch','"')) $bNeu=true;
 $v=txtVar('TxLMetaKey'); if(fSetzMPWert($v,'TxLMetaKey','"')) $bNeu=true;
 $v=txtVar('TxLMetaDes'); if(fSetzMPWert($v,'TxLMetaDes','"')) $bNeu=true;
 $v=txtVar('TxLMetaTit'); if(fSetzMPWert($v,'TxLMetaTit','"')) $bNeu=true;
 $v=max((int)txtVar('ListenLaenge'),0); if(fSetzMPWert($v,'ListenLaenge','')) $bNeu=true;
 $v=(int)txtVar('SuchFilter'); if(fSetzMPWert($v,'SuchFilter','')) $bNeu=true;
 $v=txtVar('EigeneZeilen'); if(fSetzMPWert(($v?true:false),'EigeneZeilen','')) $bNeu=true;
 $v=txtVar('SegTrnZeile'); if(fSetzMPWert(($v?true:false),'SegTrnZeile','')) $bNeu=true;
 $v=(int)txtVar('NaviOben');  if(fSetzMPWert($v,'NaviOben','')) $bNeu=true;
 $v=(int)txtVar('NaviUnten'); if(fSetzMPWert($v,'NaviUnten','')) $bNeu=true;
 // $v=txtVar('NaviBild'); if(fSetzMPWert(($v?true:false),'NaviBild','')) $bNeu=true;
 // $v=txtVar('ListeVertikal'); if(fSetzMPWert($v,'ListeVertikal',"'")) $bNeu=true;
 $v=(int)txtVar('NListeAnders'); if(fSetzMPWert(($v?true:false),'NListeAnders','')) $bNeu=true;
 $v=(int)txtVar('NVerstecktSehen'); if(fSetzMPWert(($v?true:false),'NVerstecktSehen','')) $bNeu=true;
 $v=(int)txtVar('ListenInfo'); if(fSetzMPWert($v,'ListenInfo','')) $bNeu=true;
 $v=txtVar('ListenInfTitel'); if(fSetzMPWert($v,'ListenInfTitel','"')) $bNeu=true;
 $v=(int)txtVar('GastLInfo'); if(fSetzMPWert(($v?true:false),'GastLInfo','')) $bNeu=true;
 $v=(int)txtVar('ListenBenachr'); if(fSetzMPWert($v,'ListenBenachr','')) $bNeu=true;
 $v=txtVar('ListenBenachTitel'); if(fSetzMPWert($v,'ListenBenachTitel','"')) $bNeu=true;
 $v=(int)txtVar('GastLBenachr'); if(fSetzMPWert(($v?true:false),'GastLBenachr','')) $bNeu=true;
 $v=(int)txtVar('ListenAendern'); if(fSetzMPWert($v,'ListenAendern','')) $bNeu=true;
 $v=txtVar('ListenAendTitel'); if(fSetzMPWert($v,'ListenAendTitel','"')) $bNeu=true;
 $v=txtVar('GastLAendern'); if(fSetzMPWert(($v?true:false),'GastLAendern','')) $bNeu=true;
 $v=(int)txtVar('ListenKopieren'); if(fSetzMPWert($v,'ListenKopieren','')) $bNeu=true;
 $v=txtVar('ListenKopieTitel'); if(fSetzMPWert($v,'ListenKopieTitel','"')) $bNeu=true;
 $v=txtVar('GastLKopieren'); if(fSetzMPWert(($v?true:false),'GastLKopieren','')) $bNeu=true;
 $v=txtVar('ListenAendKopieren'); if(fSetzMPWert(($v?true:false),'ListenAendKopieren','')) $bNeu=true;
 $v=(int)txtVar('NutzerListFeld');  if(fSetzMPWert($v,'NutzerListFeld','')) $bNeu=true;
 $v=(int)txtVar('NNutzerListFeld'); if(fSetzMPWert($v,'NNutzerListFeld','')) $bNeu=true;
 $v=txtVar('LinkSymbol'); if(fSetzMPWert(($v?true:false),'LinkSymbol','')) $bNeu=true;
 $v=txtVar('ErsatzBildKlein'); if(fSetzMPWert($v,'ErsatzBildKlein',"'")) $bNeu=true;
 $v=(int)txtVar('DruckLFarbig'); if(fSetzMPWert(($v?true:false),'DruckLFarbig','')) $bNeu=true;
 $v=(int)txtVar('ListenMemoLaenge'); if(fSetzMPWert($v,'ListenMemoLaenge','')) $bNeu=true;
 $v=(int)txtVar('DruckLMemoLaenge'); if(fSetzMPWert($v,'DruckLMemoLaenge','')) $bNeu=true;
 $v=(int)txtVar('DruckLFarbig'); if(fSetzMPWert(($v?true:false),'DruckLFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckLMemo'); if(fSetzMPWert(($v?true:false),'DruckLMemo','')) $bNeu=true;
 $v=(int)txtVar('DruckLMailOffen'); if(fSetzMPWert(($v?true:false),'DruckLMailOffen','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Listeneinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Listeneinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;

$aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNFz=count($aNF);
$sNOpt='<option value="0">--</option><option value="2">'.str_replace(';','`,',$aNF[2]).'</option>'; for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.str_replace(';','`,',$aNF[$j]).'</option>';
$sFOpt='<option value="-1">--</option><option value="0">0: Nummer</option><option value="1">1: Ablauf</option>'; for($j=2;$j<16;$j++) $sFOpt.='<option value="'.$j.'">'.$j.'</option>';
?>

<form action="konfListe.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Normalerweise wird die Inserateliste chronologisch nach aufsteigendem Datum (neue Inserate unten) sortiert. Abweichend kann die Inserateliste standardmäßig auch nach absteigendem Datum (neue Inserate oben) geordnet werden.</td></tr>
<tr class="admTabl">
 <td>Listensortierung</td>
 <td colspan="2" width="80%"><input type="radio" class="admRadio" name="Rueckwaerts" value="0"<?php if(!$mpRueckwaerts) echo ' checked="checked"'?> /> aufsteigende Inseratefolge &nbsp; <input type="radio" class="admRadio" name="Rueckwaerts" value="1"<?php if($mpRueckwaerts) echo ' checked="checked"'?> /> absteigende Inseratefolge</td>
</tr>
<tr class="admTabl">
 <td>Archivsortierung</td>
 <td colspan="2" width="80%"><input type="radio" class="admRadio" name="ArchivRueckwaerts" value="0"<?php if(!$mpArchivRueckwaerts) echo ' checked="checked"'?> /> aufsteigende Inseratefolge &nbsp; <input type="radio" class="admRadio" name="ArchivRueckwaerts" value="1"<?php if($mpArchivRueckwaerts) echo ' checked="checked"'?> /> absteigende Inseratefolge</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über der Inserateliste wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">komplette Liste</td>
 <td colspan="2"><input type="text" name="TxListGsmt" value="<?php echo $mpTxListGsmt?>" style="width:99%" />
 <div class="admMini">Empfehlung: <i>#S: #AGesamtliste</i><br />(#S steht als Platzhalter für den <i>Segmentnamen</i>, #N für die Inserateanzahl gesamt, #I für Inseratezahl auf der Seite, #A für <i>Archiv</i> bei Archivsuche)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Suchergebnisliste</td>
 <td colspan="2"><input type="text" name="TxListSuch" value="<?php echo $mpTxListSuch?>" style="width:99%" />
 <div class="admMini">Empfehlung: <i>#S: #ASuchergebnis für #F</i><br />(#S steht als Platzhalter für den <i>Segmentnamen</i>, #F steht für die <i>gesuchten Felder</i>, #N für die Inserateanzahl gesamt, #I für Inseratezahl auf der Seite, #A für <i>Archiv</i> bei Archivsuche)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Marktplatz eigenständig
mit der umhüllenden HTML-Schablone <i>mpSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Inseratelistenseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>mpSeite.htm</i>
mit folgenden Texten zusätzlich füllen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaKey" value="<?php echo $mpTxLMetaKey?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>#S, Kleinanzeigen, Inserate</i> &nbsp; (#S steht als Platzhalter für den <i>Segmentnamen</i>)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaDes" value="<?php echo $mpTxLMetaDes?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>Kleinanzeigen zum Thema #S</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaTit" value="<?php echo $mpTxLMetaTit?>" style="width:99%" />
 <div class="admMini">Beispiel: <i>:: Rubrik #S</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über und/oder unter der Inserateliste im Besucherbereich
kann eine Navigationsleiste zum seitenweisen Blättern durch lange Inseratelisten angezeigt werden.
An welchen Positionen und bei wieviel Inseraten soll eine solche Navigationsleiste erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td colspan="2"><input type="text" name="ListenLaenge" value="<?php echo $mpListenLaenge?>" size="2" /> Inseratezeilen auf einer Listenseite der Inserateliste &nbsp; <span class="admMini">(0 bedeutet unbegrenzt)</span>
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Inserate pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td>Navigator oberhalb<br />der Inserateliste</td>
 <td colspan="2"><select name="NaviOben" size="1" style="width:290px;"><option value="0">obere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($mpNaviOben==1) echo ' selected="selected"'?>>Navigator oberhalb der Meldungszeile</option><option value="2"<?php if($mpNaviOben==2) echo ' selected="selected"'?>>Navigator unterhalb der Meldungszeile</option></select></td>
</tr>
<tr class="admTabl">
 <td>Navigator unterhalb<br />der Inserateliste</td>
 <td colspan="2"><select name="NaviUnten" size="1" style="width:290px;"><option value="0">untere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($mpNaviUnten==1) echo ' selected="selected"'?>>Navigator unter der Inserateliste</option></select></td>
</tr>
<!-- <tr class="admTabl">
 <td>Navigatorstil</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NaviBild" value="1"<?php if($mpNaviBild) echo ' checked="checked"'?> /> die Navigationsleiste soll grafisch unterlegt sein</td>
</tr> -->

<tr class="admTabl"><td colspan="3" class="admSpa2">Über oder unter der Inserateliste im Besucherbereich kann ein Filter als Eingabefeldfeld für die Schnellsuche dargestellt werden.</td></tr>
<tr class="admTabl">
 <td>Schnellsuche</td>
 <td colspan="2"><select name="SuchFilter" size="1" style="width:450px;">
  <option value="0"<?php if($mpSuchFilter==0) echo ' selected="selected"'?>>Suchfilter nicht anzeigen</option>
  <option value="1"<?php if($mpSuchFilter==1) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile über dem Navigator - linksbündig</option>
  <option value="2"<?php if($mpSuchFilter==2) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile über dem Navigator - rechtsbündig</option>
  <option value="3"<?php if($mpSuchFilter==3) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile unter dem Navigator - linksbündig</option>
  <option value="4"<?php if($mpSuchFilter==4) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile unter dem Navigator - rechtsbündig</option>
  <option value="5"<?php if($mpSuchFilter==5) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile über dem Navigator - linksbündig</option>
  <option value="6"<?php if($mpSuchFilter==6) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile über dem Navigator - rechtsbündig</option>
  <option value="7"<?php if($mpSuchFilter==7) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile unter dem Navigator - linksbündig</option>
  <option value="8"<?php if($mpSuchFilter==8) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile unter dem Navigator - rechtsbündig</option>
  <option value="9"<?php if($mpSuchFilter==9) echo ' selected="selected"'?>>Suchfilter unter den Inseraten über dem Navigator - linksbündig</option>
  <option value="10"<?php if($mpSuchFilter==10) echo ' selected="selected"'?>>Suchfilter unter den Inseraten über dem Navigator - rechtsbündig</option>
  <option value="11"<?php if($mpSuchFilter==11) echo ' selected="selected"'?>>Suchfilter unter den Inseraten unter dem Navigator - linksbündig</option>
  <option value="12"<?php if($mpSuchFilter==12) echo ' selected="selected"'?>>Suchfilter unter den Inseraten unter dem Navigator - rechtsbündig</option>
 </select></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inserateliste wird standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt, die Sie im Menüpunkt <i>Segmenteigenschaften</i> segmentabhängig auswählen.
Abweichend davon kann jeder Inseratedatensatz in einem individuellen Layout dargestellt werden, das aus der allgemeinen Layoutschablone <i>mpListenZeile.htm</i> oder aus der segmentspezifischen Layoutschablone <i>mpXXListenZeile.htm</i> (wobei <i>XX</i> die Nummer des jeweiligen Segmentes ist) stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>
<div class="admMini" style="margin-top:5px;"><u>Hinweis</u>: Etliche der nachfolgenden Einstellungen gelten nur für das tabellarische Standardlayout.
Beim individuellen Layout müssen Sie für die meisten Feinheiten selbst sorgen.</div></td></tr>
<tr class="admTabl">
 <td>Listenlayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneZeilen" value="0"<?php if(!$mpEigeneZeilen) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneZeilen" value="1"<?php if($mpEigeneZeilen) echo ' checked="checked"'?> /> individuelles Layout aus der Schablone <i>mpListenZeile.htm</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Bei der Schnellsuche quer durch alle Segmente kann über jedem gefundenen Segment mit passenden Inseraten dessen Segmentname als Zwischenzeile erscheinen.</td></tr>
<tr class="admTabl">
 <td>Segement-<br />zwischenzeile</td>
 <td colspan="2"><input type="radio" class="admRadio" name="SegTrnZeile" value="1"<?php if($mpSegTrnZeile) echo ' checked="checked"'?> /> Segmenttrennzeile einblenden &nbsp; <input type="radio" class="admRadio" name="SegTrnZeile" value="0"<?php if(!$mpSegTrnZeile) echo ' checked="checked"'?> /> keine Segmentrennzeile zeigen</td>
</tr>

<!-- <tr class="admTabl"><td colspan="3" class="admSpa2">Der Text in jeder Zeile der Inserateliste kann mittig, obenbündig oder untenbündig ausgerichtet sein.
Wie soll die vertikale Ausrichtung erscheinen?</td></tr>
<tr class="admTabl">
 <td>Textausrichtung</td>
 <td colspan="2"><select name="ListeVertikal" size="1" style="width:100px;"><option value="">mittig</option><option value="top"<?php if($mpListeVertikal=='top') echo ' selected="selected"'?>>obenbündig</option><option value="bottom"<?php if($mpListeVertikal=='bottom') echo ' selected="selected"'?>>untenbündig</option></select> (in vertikaler Richtung)</td>
</tr> -->

<tr class="admTabl"><td colspan="3" class="admSpa2">Der Inseratemarkt kann mit einer Benutzerverwaltung kombiniert sein.
Damit ergeben sich unterschiedliche Darstellungs-Möglichkeiten in der Listenanzeige der Inserate für unangemeldete Gäste und für angemeldete Benutzer.
Hier entscheiden Sie, ob überhaupt Unterschiede in der Darstellung gemacht werden.
Welche Datenspalten dann für die jeweilige Gruppe sichtbar sind stellen Sie für jedes Marktsegment gesondert unter <i>Segmenteigenschaften</i> ein.
</td></tr>
<tr class="admTabl">
 <td>Listenansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NListeAnders" value="1"<?php if($mpNListeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Tabellenspalten/Spaltenreihenfolgen sehen als Gäste</td>
</tr>
<tr class="admTabl">
 <td>versteckte Inserate</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NVerstecktSehen" value="1"<?php if($mpNVerstecktSehen) echo ' checked="checked"'?> /> angemeldete Benutzer sollen versteckte Inserate sehen können</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls in der Inseratestruktur ein Feld vom Typ Benutzer enthalten ist und dieses in der Inserateliste angezeigt wird kann dessen anzuzeigender Inhalt festgelegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdarstellung</td>
 <td colspan="2"><select name="NutzerListFeld" style="width:140px;"><?php echo str_replace('"'.$mpNutzerListFeld.'"','"'.$mpNutzerListFeld.'" selected="selected"',$sNOpt)?></select> (Anzeige für unangemeldete Gäste)
 <div><select name="NNutzerListFeld" style="width:140px;"><?php echo str_replace('"'.$mpNNutzerListFeld.'"','"'.$mpNNutzerListFeld.'" selected="selected"',$sNOpt)?></select> (Anzeige für angemeldete Benutzer)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inserateliste (und die Inseratedetails) kann die Inseratenummer anzeigen

</td></tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inserateliste kann eine zusätzliche Spalte mit einem Klickschalter
für das Versenden einer Information über den Inserat an einen beliebigen E-Mail-Empfänger enthalten (tell-a-friend-Funktion).<br />
Vor welcher Spalte soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Informationsfunktion</td>
 <td style="white-space:nowrap;">zusätzliche <img src="<?php echo MPPFAD;?>grafik/iconInfo.gif" width="16" height="16" border="0" align="bottom" title="Info">-Infospalte einblenden vor Spalte <select name="ListenInfo" size="1"><?php echo str_replace('"'.$mpListenInfo.'"','"'.$mpListenInfo.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastLInfo" value="1"<?php if($mpGastLInfo) echo ' checked="checked"'?> /> auch für unangemeldete Gäste</div>
 <div>Spaltentitel <input type="text" name="ListenInfTitel" value="<?php echo $mpListenInfTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Spalten 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inserateliste kann eine zusätzliche Spalte mit einem Klickschalter
für den direkten Aufruf des Änderungsformulars oder Kopierformulars für den Inserat enthalten.
Vor welcher Spalte soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconAendern.gif" width="12" height="13" border="0" align="top" title="Ändern">-Änderungs-Klickschalter
bzw. <img src="<?php echo MPPFAD;?>grafik/iconKopie.gif" width="12" height="13" border="0" align="top" title="Kopieren">-Kopier-Klickschalter gegebenenfalls erscheinen?
<div>Wenn Sie keine Spaltenposition angeben wird standardmäßig kein Klickschalter zum Ändern bzw. Kiopieren in der Inserateliste eingeblendet.
Sofern aber ein Benutzer oder Gast auf den Verweis [<i>Ändern</i>] über/unter der Inserateliste klickt
wird ein solcher Änderungsschalter und Kopierschalter automatisch vor jedes Inserat der Inserateliste gesetzt. Die ausdrückliche Änderungsspalte/Kopierspalte ist also nicht immer hilfreich.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsspalte</td>
 <td style="white-space:nowrap;">zusätzlichen <img src="<?php echo MPPFAD;?>grafik/iconAendern.gif" width="12" height="13" border="0" align="bottom" title="Ändern">-Schalter einblenden vor Spalte <select name="ListenAendern" size="1"><?php echo str_replace('"'.$mpListenAendern.'"','"'.$mpListenAendern.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastLAendern" value="1"<?php if($mpGastLAendern) echo ' checked="checked"'?> /> auch für unangemeldete Gäste</div>
 <div>Spaltentitel <input type="text" name="ListenAendTitel" value="<?php echo $mpListenAendTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Spalten 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopierspalte</td>
 <td style="white-space:nowrap;">zusätzlichen <img src="<?php echo MPPFAD;?>grafik/iconKopie.gif" width="12" height="13" border="0" align="bottom" title="Kopieren">-Schalter einblenden vor Spalte <select name="ListenKopieren" size="1"><?php echo str_replace('"'.$mpListenKopieren.'"','"'.$mpListenKopieren.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastLKopieren" value="1"<?php if($mpGastLKopieren) echo ' checked="checked"'?> /> auch für unangemeldete Gäste</div>
 <div>Spaltentitel <input type="text" name="ListenKopieTitel" value="<?php echo $mpListenKopieTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Spalten 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>
<tr class="admTabl">
 <td>Kopierspalte in der<br />Änderungsliste</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="ListenAendKopieren" value="1"<?php if($mpListenAendKopieren) echo ' checked="checked"'?> /> Kopierspalte in der Änderungsliste auch dann anzeigen, wenn Änderungsspalte und Kopierspalte ausgeschaltet sind</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Inserateliste kann eine zusätzliche Spalte mit einem Klickschalter
für einen Benachrichtigungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er bei eventuellen Inserateänderungen oder Inseratelöschungen eine Benachritigungs-E-Mail zum betreffenden Inserat erhält.
Vor welcher Spalte soll ein solcher <img src="<?php echo MPPFAD;?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />service</td>
 <td style="white-space:nowrap;">zusätzlichen <img src="<?php echo MPPFAD;?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="bottom" title="Benachrichtigung">-Schalter einblenden vor Spalte <select name="ListenBenachr" size="1"><?php echo str_replace('"'.$mpListenBenachr.'"','"'.$mpListenBenachr.'" selected="selected"',$sFOpt)?></select>
 <div><input type="checkbox" class="admCheck" name="GastLBenachr" value="1"<?php if($mpGastLBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste</div>
 <div>Spaltentitel <input type="text" name="ListenBenachTitel" value="<?php echo $mpListenBenachTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
 <td class="admMini" style="vertical-align:top;"><u>Hinweis</u>: Es werden hier formal die Spalten 0..15 angeboten, unabhängig davon ob es in Ihrer Inseratestruktur wirklich 15 Felder gibt.</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Memo</i> enthält
und diese Felder in der Inserateliste aktiviert sind können wahlweise nur die ersten Zeichen des Feldinhalts angedeutet werden,
um Platzprobleme bei überlangen Feldinhalten zu vermeinden</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Memofelder kürzen</td>
 <td colspan="2"><input type="text" name="ListenMemoLaenge" value="<?php if($mpListenMemoLaenge) echo $mpListenMemoLaenge?>" style="width:40px;" /> <span class="admMini">leer lassen für ungekürzt oder Buchstabenanzahl nach der abgeschnitten werden soll</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Link</i> enthält
und diese Felder in der Inserateliste aktiviert sind wird ein solcher Link normalerweise in Kurzform als Symbol dargestellt.
Der Link kann aber auch in Textform mit Darstellung der Adresse erfolgen, was jedoch mehr Spaltenbreite beansprucht.</td></tr>
<tr class="admTabl">
 <td>Linkdarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="LinkSymbol" value="1"<?php if($mpLinkSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo MPPFAD;?>grafik/iconLink.gif" width="16" height="16" border="0" title="Link">-Symbol &nbsp; <input type="radio" class="admRadio" name="LinkSymbol" value=""<?php if(!$mpLinkSymbol) echo ' checked="checked"'?> /> Langform mit Adressangabe</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Inseratestruktur Felder vom Typ <i>Bild</i> enthält
und kein Bild zum Inserat hochgeladen wurde kann in der Inserateliste statt des Bildes
ein ErsatzBild aus dem Ordner <i>grafik</i> angezeigt werden.</td></tr>
<tr class="admTabl">
 <td>Ersatzbild</td>
 <td colspan="2"><select name="ErsatzBildKlein" size="1" style="width:180px;"><option value="">kein Ersatzbild anzeigen</option><option value="kein-Bild.jpg"<?php if($mpErsatzBildKlein=='kein-Bild.jpg') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.jpg</option><option value="kein-Bild.gif"<?php if($mpErsatzBildKlein=='kein-Bild.gif') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.gif</option><option value="kein-Bild.png"<?php if($mpErsatzBildKlein=='kein-Bild.png') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.png</option></select></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Druck der Inserateliste können folgende Einstellungen gewählt werden.</td></tr>
<tr class="admTabl">
 <td>Drucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckLFarbig" value="0"<?php if(!$mpDruckLFarbig) echo ' checked="checked"'?> /> simpel (im CSS-Stil <i>div.mpTbZlDr</i>) &nbsp; <input type="radio" class="admRadio" name="DruckLFarbig" value="1"<?php if($mpDruckLFarbig) echo ' checked="checked"'?> /> formatiert (im CSS-Stil der Bildschirm-Inserateliste)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Memofeld-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckLMemo" value="0"<?php if(!$mpDruckLMemo) echo ' checked="checked"'?> /> Memofelder in Inseratelisten <i>nicht</i> drucken &nbsp; <input type="radio" class="admRadio" name="DruckLMemo" value="1"<?php if($mpDruckLMemo) echo ' checked="checked"'?> /> Memofelder in Inseratelisten mit drucken<br />
 <input type="text" name="DruckLMemoLaenge" value="<?php if($mpDruckLMemoLaenge) echo $mpDruckLMemoLaenge?>" style="width:40px;" /> <span class="admMini">leer lassen für ungekürzt oder Anzahl der Zeichen nach denen abgeschnitten werden soll</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DruckLMailOffen" value="1"<?php if($mpDruckLMailOffen) echo ' checked="checked"'?> /> E-Mail-Adressen offen lesbar in der Druckliste darstellen
 <div class="admMini">Empfehlung: möglichst <i>nicht</i> aktivieren, weil auch Roboter/Spider die Druckseite einsehen könnten</div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>