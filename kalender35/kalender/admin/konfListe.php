<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminliste anpassen','','KTl');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Terminliste.</p>';
 $ksRueckwaerts=KAL_Rueckwaerts; $ksArchivRueckwaerts=KAL_ArchivRueckwaerts; $ksListenIndex=KAL_ListenIndex; $ksLinkLstLst=KAL_LinkLstLst;
 $ksTxLMetaKey=KAL_TxLMetaKey; $ksTxLMetaDes=KAL_TxLMetaDes; $ksTxLMetaTit=KAL_TxLMetaTit;
 $ksTxListGsmt=KAL_TxListGsmt; $ksTxListSuch=KAL_TxListSuch; $ksIntervall=KAL_Intervall; $ksNIntervall=KAL_NIntervall;
 $ksSucheBleibt=KAL_SucheBleibt; $ksIvExakt=KAL_IvExakt; $ksIvWerte=KAL_IvWerte; $ksNIvWerte=KAL_NIvWerte;
 $ksLaufendes=KAL_Laufendes; $ksLfndEnde=KAL_LfndEnde; $ksLfndZeit=KAL_LfndZeit;
 $ksAktuelles=KAL_Aktuelles; $ksAktuEnde=KAL_AktuEnde; $ksAktuZeit=KAL_AktuZeit; $ksListenLaenge=KAL_ListenLaenge;
 $ksNaviOben=KAL_NaviOben; $ksNaviUnten=KAL_NaviUnten; $ksNaviBild=KAL_NaviBild; $ksSuchFilter=KAL_SuchFilter; $ksSuchFltArchiv=KAL_SuchFltArchiv;
 $ksEigeneZeilen=KAL_EigeneZeilen; $ksEigeneDruckZeilen=KAL_EigeneDruckZeilen;
 $ksMonatLLang=KAL_MonatLLang; $ksMonatsTrenner=KAL_MonatsTrenner; $ksWochenTrenner=KAL_WochenTrenner; $ksTagesTrenner=KAL_TagesTrenner;
 $ksListenMemoLaenge=KAL_ListenMemoLaenge; $ksDruckLMemoLaenge=KAL_DruckLMemoLaenge; $ksDruckLFarbig=KAL_DruckLFarbig; $ksDruckLMemo=KAL_DruckLMemo; $ksDruckLMailOffen=KAL_DruckLMailOffen;
 $ksListeVertikal=KAL_ListeVertikal; $ksNummerStellen=KAL_NummerStellen; $ksNListeAnders=KAL_NListeAnders; $ksNVerstecktSehen=KAL_NVerstecktSehen;
 $ksTxNr=KAL_TxNr; $ksLinkSymbol=KAL_LinkSymbol; $ksErsatzBildKlein=KAL_ErsatzBildKlein;
 $ksListenInfo=KAL_ListenInfo; $ksListenInfTitel=KAL_ListenInfTitel; $ksGastLInfo=KAL_GastLInfo;
 $ksListenErinn=KAL_ListenErinn; $ksListenErinTitel=KAL_ListenErinTitel; $ksGastLErinn=KAL_GastLErinn;
 $ksListenBenachr=KAL_ListenBenachr; $ksListenBenachTitel=KAL_ListenBenachTitel; $ksGastLBenachr=KAL_GastLBenachr;
 $ksListenAendern=KAL_ListenAendern; $ksListenAendTitel=KAL_ListenAendTitel; $ksGastLAendern=KAL_GastLAendern;
 $ksListenKopieren=KAL_ListenKopieren; $ksListenKopieTitel=KAL_ListenKopieTitel; $ksGastLKopieren=KAL_GastLKopieren; $ksListenAendKopieren=KAL_ListenAendKopieren;
 $ksListenCal=KAL_ListenCal; $ksGastLCal=KAL_GastLCal; $ksTxListenCalTitel=KAL_TxListenCalTitel;
 $ksNutzerListFeld=KAL_NutzerListFeld; $ksNNutzerListFeld=KAL_NNutzerListFeld;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=(int)txtVar('ListenIndex');if(fSetzKalWert($v,'ListenIndex','')) $bNeu=true;
 $v=txtVar('Rueckwaerts');if(fSetzKalWert((($v&&$ksListenIndex==1)?true:false),'Rueckwaerts','')) $bNeu=true;
 $v=txtVar('ArchivRueckwaerts');if(fSetzKalWert((($v&&$ksListenIndex==1)?true:false),'ArchivRueckwaerts','')) $bNeu=true;
 $v=txtVar('LinkLstLst');if(fSetzKalWert(($v?true:false),'LinkLstLst','')) $bNeu=true;
 $v=txtVar('TxListGsmt'); if(fSetzKalWert($v,'TxListGsmt','"')) $bNeu=true;
 $v=txtVar('TxListSuch'); if(fSetzKalWert($v,'TxListSuch','"')) $bNeu=true;
 $v=txtVar('TxLMetaKey'); if(fSetzKalWert($v,'TxLMetaKey','"')) $bNeu=true;
 $v=txtVar('TxLMetaDes'); if(fSetzKalWert($v,'TxLMetaDes','"')) $bNeu=true;
 $v=txtVar('TxLMetaTit'); if(fSetzKalWert($v,'TxLMetaTit','"')) $bNeu=true;
 $v=txtVar('Intervall');  if(fSetzKalWert($v,'Intervall',"'")) $bNeu=true;
 $v=txtVar('NIntervall');  if(fSetzKalWert($v,'NIntervall',"'")) $bNeu=true;
 $v=(int)txtVar('IvExakt'); if(fSetzKalWert(($v?true:false),'IvExakt','')) $bNeu=true;
 $v=txtVar('SucheBleibt');  if(fSetzKalWert(($v?true:false),'SucheBleibt','')) $bNeu=true;
 $v=(isset($_POST['IWerte'])?$_POST['IWerte']:'');   if(is_array($v)){$t='#'; foreach($v as $w) $t.=$w; $v=$t;} if(fSetzKalWert($v,'IvWerte',"'")) $bNeu=true;
 $v=(isset($_POST['NIWerte'])?$_POST['NIWerte']:''); if(is_array($v)){$t='#'; foreach($v as $w) $t.=$w; $v=$t;} if(fSetzKalWert($v,'NIvWerte',"'")) $bNeu=true;
 $v=txtVar('Laufendes');  if(fSetzKalWert(($v?true:false),'Laufendes','')) $bNeu=true;
 $v=txtVar('LfndEnde');   if(fSetzKalWert(($v?true:false),'LfndEnde','')) $bNeu=true;
 $v=txtVar('LfndZeit');   if(fSetzKalWert(($v?true:false),'LfndZeit','')) $bNeu=true;
 $v=txtVar('Aktuelles');  if(fSetzKalWert(($v?true:false),'Aktuelles','')) $bNeu=true;
 $v=txtVar('AktuEnde');   if(fSetzKalWert(($v?true:false),'AktuEnde','')) $bNeu=true;
 $v=txtVar('AktuZeit');   if(fSetzKalWert(($v?true:false),'AktuZeit','')) $bNeu=true;
 $v=max((int)txtVar('ListenLaenge'),1); if(fSetzKalWert($v,'ListenLaenge','')) $bNeu=true;
 $v=(int)txtVar('NaviOben');   if(fSetzKalWert($v,'NaviOben','')) $bNeu=true;
 $v=(int)txtVar('NaviUnten');  if(fSetzKalWert($v,'NaviUnten','')) $bNeu=true;
 $v=txtVar('NaviBild');   if(fSetzKalWert(($v?true:false),'NaviBild','')) $bNeu=true;
 $v=(int)txtVar('SuchFilter');  if(fSetzKalWert($v,'SuchFilter','')) $bNeu=true;
 $v=txtVar('SuchFltArchiv'); if(fSetzKalWert(($v?true:false),'SuchFltArchiv','')) $bNeu=true;
 $v=txtVar('EigeneZeilen');  if(fSetzKalWert(($v?true:false),'EigeneZeilen','')) $bNeu=true;
 $v=txtVar('EigeneDruckZeilen');  if(fSetzKalWert(($v?true:false),'EigeneDruckZeilen','')) $bNeu=true;
 $v=(int)txtVar('MonatLLang'); if(fSetzKalWert($v,'MonatLLang','')) $bNeu=true;
 $v=(int)txtVar('MonatsTrenner'); if(fSetzKalWert($v,'MonatsTrenner','')) $bNeu=true;
 $v=(int)txtVar('WochenTrenner'); if(fSetzKalWert($v,'WochenTrenner','')) $bNeu=true;
 $v=(int)txtVar('TagesTrenner'); if(fSetzKalWert($v,'TagesTrenner','')) $bNeu=true;
 $v=(int)txtVar('ListenMemoLaenge'); if(fSetzKalWert($v,'ListenMemoLaenge','')) $bNeu=true;
 $v=(int)txtVar('DruckLMemoLaenge'); if(fSetzKalWert($v,'DruckLMemoLaenge','')) $bNeu=true;
 $v=(int)txtVar('DruckLFarbig'); if(fSetzKalWert(($v?true:false),'DruckLFarbig','')) $bNeu=true;
 $v=(int)txtVar('DruckLMemo'); if(fSetzKalWert(($v?true:false),'DruckLMemo','')) $bNeu=true;
 $v=(int)txtVar('DruckLMailOffen'); if(fSetzKalWert(($v?true:false),'DruckLMailOffen','')) $bNeu=true;
 $v=txtVar('ListeVertikal'); if(fSetzKalWert($v,'ListeVertikal',"'")) $bNeu=true;
 $v=max((int)txtVar('NummerStellen'),1); if(fSetzKalWert($v,'NummerStellen','')) $bNeu=true;
 $v=txtVar('NListeAnders'); if(fSetzKalWert(($v?true:false),'NListeAnders','')) $bNeu=true;
 $v=txtVar('NVerstecktSehen'); if(fSetzKalWert(($v?true:false),'NVerstecktSehen','')) $bNeu=true;
 $v=(int)txtVar('NutzerListFeld');  if(fSetzKalWert($v,'NutzerListFeld','')) $bNeu=true;
 $v=(int)txtVar('NNutzerListFeld'); if(fSetzKalWert($v,'NNutzerListFeld','')) $bNeu=true;
 $v=txtVar('TxNr'); if(fSetzKalWert($v,'TxNr','"')) $bNeu=true;
 $kal_ListenFeld=array(); $kal_NListenFeld=array(); $kal_SortierFeld=array(); $kal_LinkFeld=array(); $kal_SpaltenStil=array();
 for($i=0;$i<$nFelder;$i++){
  $kal_ListenFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_NListenFeld[$i]=(isset($_POST['N'.$i])?(int)$_POST['N'.$i]:0);
  $kal_SortierFeld[$i]=(isset($_POST['S'.$i])?(int)$_POST['S'.$i]:0);
  $kal_LinkFeld[$i]=(isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0);
  $kal_SpaltenStil[$i]=(isset($_POST['Z'.$i])?str_replace("'",'"',stripslashes($_POST['Z'.$i])):'');
 }
 asort($kal_ListenFeld); reset($kal_ListenFeld); asort($kal_NListenFeld); reset($kal_NListenFeld);
 $j=0; foreach($kal_ListenFeld as $k=>$v) if($v>0) if($k>0) $kal_ListenFeld[$k]=++$j;
 $j=0; foreach($kal_NListenFeld as $k=>$v) if($v>0) if($k>0) $kal_NListenFeld[$k]=++$j;
 $kal_SortierFeld[0]=min($kal_ListenFeld[0]+$kal_NListenFeld[0],1);
 if(fSetzArray($kal_ListenFeld,'ListenFeld','')) $bNeu=true;
 if(fSetzArray($kal_NListenFeld,'NListenFeld','')) $bNeu=true;
 if(fSetzArray($kal_SortierFeld,'SortierFeld','')) $bNeu=true;
 if(fSetzArray($kal_LinkFeld,'LinkFeld','')) $bNeu=true;
 if(fSetzArray($kal_SpaltenStil,'SpaltenStil',"'")) $bNeu=true;
 $v=txtVar('LinkSymbol'); if(fSetzKalWert(($v?true:false),'LinkSymbol','')) $bNeu=true;
 $v=txtVar('ErsatzBildKlein'); if(fSetzKalWert($v,'ErsatzBildKlein',"'")) $bNeu=true;
 $v=(int)txtVar('ListenInfo'); if(fSetzKalWert($v,'ListenInfo','')) $bNeu=true;
 $v=txtVar('ListenInfTitel'); if(fSetzKalWert($v,'ListenInfTitel','"')) $bNeu=true;
 $v=txtVar('GastLInfo'); if(fSetzKalWert(($v?true:false),'GastLInfo','')) $bNeu=true;
 $v=(int)txtVar('ListenErinn'); if(fSetzKalWert($v,'ListenErinn','')) $bNeu=true;
 $v=txtVar('ListenErinTitel'); if(fSetzKalWert($v,'ListenErinTitel','"')) $bNeu=true;
 $v=txtVar('GastLErinn'); if(fSetzKalWert(($v?true:false),'GastLErinn','')) $bNeu=true;
 $v=(int)txtVar('ListenBenachr'); if(fSetzKalWert($v,'ListenBenachr','')) $bNeu=true;
 $v=txtVar('ListenBenachTitel'); if(fSetzKalWert($v,'ListenBenachTitel','"')) $bNeu=true;
 $v=txtVar('GastLBenachr'); if(fSetzKalWert(($v?true:false),'GastLBenachr','')) $bNeu=true;
 $v=(int)txtVar('ListenAendern'); if(fSetzKalWert($v,'ListenAendern','')) $bNeu=true;
 $v=txtVar('ListenAendTitel'); if(fSetzKalWert($v,'ListenAendTitel','"')) $bNeu=true;
 $v=txtVar('GastLAendern'); if(fSetzKalWert(($v?true:false),'GastLAendern','')) $bNeu=true;
 $v=(int)txtVar('ListenKopieren'); if(fSetzKalWert($v,'ListenKopieren','')) $bNeu=true;
 $v=txtVar('ListenKopieTitel'); if(fSetzKalWert($v,'ListenKopieTitel','"')) $bNeu=true;
 $v=txtVar('GastLKopieren'); if(fSetzKalWert(($v?true:false),'GastLKopieren','')) $bNeu=true;
 $v=txtVar('ListenAendKopieren'); if(fSetzKalWert(($v?true:false),'ListenAendKopieren','')) $bNeu=true;
 $v=(int)txtVar('ListenCal'); if(fSetzKalWert($v,'ListenCal','')) $bNeu=true;
 $v=(int)txtVar('GastLCal'); if(fSetzKalWert(($v?true:false),'GastLCal','')) $bNeu=true;
 $v=txtVar('TxListenCalTitel'); if(fSetzKalWert($v,'TxListenCalTitel',"'")) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Listeneinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Listeneinstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
echo $Msg.NL; $sSortOpt='';
for($i=0;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i];
 if($t!='z'&&$t!='v'&&$t!='l'&&$t!='e'&&$t!='b'&&$t!='x'&&$t!='f'&&$t!='c'&&$t!='p'&&$t!='#') $sSortOpt.='<option value="'.$i.'"'.($ksListenIndex!=$i||$i==1?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
}
?>

<form action="konfListe.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Normalerweise wird die Terminliste chronologisch nach aufsteigendem Datum (spätere Termine unten) sortiert.
Abweichend kann die Terminliste standardmäßig auch nach einem anderen Feld oder absteigender Sortierfolge (spätere Termine oben) geordnet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfeld</td>
 <td colspan="2"><select name="ListenIndex"><option value="1">Standard</option><?php echo $sSortOpt;?></select> Empfehlung: Standard (nach Datum)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfolge</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Rueckwaerts" value="0"<?php if(!$ksRueckwaerts) echo ' checked="checked"'?> /> aufsteigende Datumsfolge &nbsp; <input type="radio" class="admRadio" name="Rueckwaerts" value="1"<?php if($ksRueckwaerts) echo ' checked="checked"'?> /> absteigende Datumsfolge</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Archivsortierung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="ArchivRueckwaerts" value="0"<?php if(!$ksArchivRueckwaerts) echo ' checked="checked"'?> /> aufsteigende Datumsfolge &nbsp; <input type="radio" class="admRadio" name="ArchivRueckwaerts" value="1"<?php if($ksArchivRueckwaerts) echo ' checked="checked"'?> /> absteigende Datumsfolge</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über der Terminliste wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">komplette Liste</td>
 <td colspan="2"><input type="text" name="TxListGsmt" value="<?php echo $ksTxListGsmt?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Gesamtliste</i> oder <i>Gesamtliste mit #N von #M Terminen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Suchergebnisliste</td>
 <td colspan="2"><input type="text" name="TxListSuch" value="<?php echo $ksTxListSuch?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Suchergebnis für #S</i> &nbsp; (#S steht als Platzhalter für die gesuchten Felder)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweis zur Liste</td>
 <td colspan="2"><input class="admRadio" type="checkbox" name="LinkLstLst" value="1"<?php if($ksLinkLstLst) echo' checked="checked"'?> /> Link [ <?php echo (KAL_LinkOList?KAL_LinkOList:KAL_LinkUList)?> ] auch auf der Seite der Terminliste selbst anzeigen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Inseratelistenseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i>
mit folgenden Texten zusätzlich füllen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaKey" value="<?php echo $ksTxLMetaKey?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine Veranstaltungen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaDes" value="<?php echo $ksTxLMetaDes?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Terminliste</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><input type="text" name="TxLMetaTit" value="<?php echo $ksTxLMetaTit?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Terminliste</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über der Terminliste im Besucherbereich kann ein Intervallfilter als Auswahlfeld dargestellt werden.
Mit welchen Auswahloptionen soll diese Auswahlbox für unangemeldete Gäste gefüllt sein und welches Standardintervall soll für unangemeldete Gäste in diesem Filter voreingestellt sein?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Optionswerte<br />im Intervallfilter<br />für Gäste</td>
 <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="0"<?php if(strpos($ksIvWerte,'0')>0) echo ' checked="checked"'?> /> alle Termine</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="1"<?php if(strpos($ksIvWerte,'1')>0) echo ' checked="checked"'?> /> 1 Tag</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="3"<?php if(strpos($ksIvWerte,'3')>0) echo ' checked="checked"'?> /> 3 Tage</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="7"<?php if(strpos($ksIvWerte,'7')>0) echo ' checked="checked"'?> /> 1 Woche</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="4"<?php if(strpos($ksIvWerte,'4')>0) echo ' checked="checked"'?> /> 2 Wochen</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="A"<?php if(strpos($ksIvWerte,'A')>0) echo ' checked="checked"'?> /> 1 Monat</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="C"<?php if(strpos($ksIvWerte,'C')>0) echo ' checked="checked"'?> /> 3 Monate</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="F"<?php if(strpos($ksIvWerte,'F')>0) echo ' checked="checked"'?> /> 6 Monate</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="L"<?php if(strpos($ksIvWerte,'L')>0) echo ' checked="checked"'?> /> 1 Jahr</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="@"<?php if(strpos($ksIvWerte,'@')>0) echo ' checked="checked"'?> /> Terminarchiv</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="a"<?php if(strpos($ksIvWerte,'a')>0) echo ' checked="checked"'?> /> diese Woche</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="d"<?php if(strpos($ksIvWerte,'d')>0) echo ' checked="checked"'?> /> dieser Monat</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="g"<?php if(strpos($ksIvWerte,'g')>0) echo ' checked="checked"'?> /> dieses Quartal</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="j"<?php if(strpos($ksIvWerte,'j')>0) echo ' checked="checked"'?> /> dieses Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="m"<?php if(strpos($ksIvWerte,'m')>0) echo ' checked="checked"'?> /> dieses Jahr</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="c"<?php if(strpos($ksIvWerte,'c')>0) echo ' checked="checked"'?> /> nächste Woche</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="f"<?php if(strpos($ksIvWerte,'f')>0) echo ' checked="checked"'?> /> nächster Monat</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="i"<?php if(strpos($ksIvWerte,'i')>0) echo ' checked="checked"'?> /> nächstes Quartal</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="l"<?php if(strpos($ksIvWerte,'l')>0) echo ' checked="checked"'?> /> nächstes Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="o"<?php if(strpos($ksIvWerte,'o')>0) echo ' checked="checked"'?> /> nächstes Jahr</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="b"<?php if(strpos($ksIvWerte,'b')>0) echo ' checked="checked"'?> /> vorige Woche</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="e"<?php if(strpos($ksIvWerte,'e')>0) echo ' checked="checked"'?> /> voriger Monat</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="h"<?php if(strpos($ksIvWerte,'h')>0) echo ' checked="checked"'?> /> voriges Quartal</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="k"<?php if(strpos($ksIvWerte,'k')>0) echo ' checked="checked"'?> /> voriges Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="IWerte[]" value="n"<?php if(strpos($ksIvWerte,'n')>0) echo ' checked="checked"'?> /> voriges Jahr</td>
  </tr>
 </table></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Standardintervall<br />für Gäste</td>
 <td colspan="2">
 <select name="Intervall" size="1"><option value="-"<?php if($ksIntervall=='-') echo ' selected="selected"'?>>Intervallfilter für unangemeldete Gäste nicht anzeigen</option>
  <option value="0"<?php if($ksIntervall=='0') echo ' selected="selected"'?>>alle Termine</option>
  <option value="1"<?php if($ksIntervall=='1') echo ' selected="selected"'?>>1 Tag</option>
  <option value="3"<?php if($ksIntervall=='3') echo ' selected="selected"'?>>3 Tage</option>
  <option value="7"<?php if($ksIntervall=='7') echo ' selected="selected"'?>>1 Woche</option>
  <option value="14"<?php if($ksIntervall=='14')echo' selected="selected"'?>>2 Wochen</option>
  <option value="A"<?php if($ksIntervall=='A') echo ' selected="selected"'?>>1 Monat</option>
  <option value="C"<?php if($ksIntervall=='C') echo ' selected="selected"'?>>3 Monate</option>
  <option value="F"<?php if($ksIntervall=='F') echo ' selected="selected"'?>>6 Monate</option>
  <option value="L"<?php if($ksIntervall=='L') echo ' selected="selected"'?>>1 Jahr</option>
  <option value="a"<?php if($ksIntervall=='a') echo ' selected="selected"'?>>diese Woche</option>
  <option value="d"<?php if($ksIntervall=='d') echo ' selected="selected"'?>>dieser Monat</option>
  <option value="g"<?php if($ksIntervall=='g') echo ' selected="selected"'?>>dieses Quartal</option>
  <option value="j"<?php if($ksIntervall=='j') echo ' selected="selected"'?>>dieses Halbjahr</option>
  <option value="m"<?php if($ksIntervall=='m') echo ' selected="selected"'?>>dieses Jahr</option>
  <option value="c"<?php if($ksIntervall=='c') echo ' selected="selected"'?>>nächste Woche</option>
  <option value="f"<?php if($ksIntervall=='f') echo ' selected="selected"'?>>nächster Monat</option>
  <option value="i"<?php if($ksIntervall=='i') echo ' selected="selected"'?>>nächstes Quartal</option>
  <option value="l"<?php if($ksIntervall=='l') echo ' selected="selected"'?>>nächste Halbjahr</option>
  <option value="o"<?php if($ksIntervall=='o') echo ' selected="selected"'?>>nächstes Jahr</option>
  <option value="b"<?php if($ksIntervall=='b') echo ' selected="selected"'?>>vorige Woche</option>
  <option value="e"<?php if($ksIntervall=='e') echo ' selected="selected"'?>>voriger Monat</option>
  <option value="h"<?php if($ksIntervall=='h') echo ' selected="selected"'?>>voriges Quartal</option>
  <option value="k"<?php if($ksIntervall=='k') echo ' selected="selected"'?>>voriges Halbjahr</option>
  <option value="n"<?php if($ksIntervall=='n') echo ' selected="selected"'?>>voriges Jahr</option>
  <option value="@"<?php if($ksIntervall=='@') echo ' selected="selected"'?>>Terminarchiv</option>
 </select><div class="admMini">(Das eingestellte Standardintervall muss oben auch ausgewählt sein.)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Mit welchen Auswahloptionen soll diese Auswahlbox für angemeldete Benutzer gefüllt sein und welches Standardintervall soll für angemeldete Benutzer in diesem Filter voreingestellt sein?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Optionswerte<br />im Intervallfilter<br />für Benutzer</td>
 <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="0"<?php if(strpos($ksNIvWerte,'0')>0) echo ' checked="checked"'?> /> alle Termine</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="1"<?php if(strpos($ksNIvWerte,'1')>0) echo ' checked="checked"'?> /> 1 Tag</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="3"<?php if(strpos($ksNIvWerte,'3')>0) echo ' checked="checked"'?> /> 3 Tage</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="7"<?php if(strpos($ksNIvWerte,'7')>0) echo ' checked="checked"'?> /> 1 Woche</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="4"<?php if(strpos($ksNIvWerte,'4')>0) echo ' checked="checked"'?> /> 2 Wochen</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="A"<?php if(strpos($ksNIvWerte,'A')>0) echo ' checked="checked"'?> /> 1 Monat</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="C"<?php if(strpos($ksNIvWerte,'C')>0) echo ' checked="checked"'?> /> 3 Monate</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="F"<?php if(strpos($ksNIvWerte,'F')>0) echo ' checked="checked"'?> /> 6 Monate</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="L"<?php if(strpos($ksNIvWerte,'L')>0) echo ' checked="checked"'?> /> 1 Jahr</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="@"<?php if(strpos($ksNIvWerte,'@')>0) echo ' checked="checked"'?> /> Terminarchiv</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="a"<?php if(strpos($ksNIvWerte,'a')>0) echo ' checked="checked"'?> /> diese Woche</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="d"<?php if(strpos($ksNIvWerte,'d')>0) echo ' checked="checked"'?> /> dieser Monat</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="g"<?php if(strpos($ksNIvWerte,'g')>0) echo ' checked="checked"'?> /> dieses Quartal</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="j"<?php if(strpos($ksNIvWerte,'j')>0) echo ' checked="checked"'?> /> dieses Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="m"<?php if(strpos($ksNIvWerte,'m')>0) echo ' checked="checked"'?> /> dieses Jahr</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="c"<?php if(strpos($ksNIvWerte,'c')>0) echo ' checked="checked"'?> /> nächste Woche</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="f"<?php if(strpos($ksNIvWerte,'f')>0) echo ' checked="checked"'?> /> nächster Monat</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="i"<?php if(strpos($ksNIvWerte,'i')>0) echo ' checked="checked"'?> /> nächstes Quartal</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="l"<?php if(strpos($ksNIvWerte,'l')>0) echo ' checked="checked"'?> /> nächstes Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="o"<?php if(strpos($ksNIvWerte,'o')>0) echo ' checked="checked"'?> /> nächstes Jahr</td>
  </tr><tr>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="b"<?php if(strpos($ksNIvWerte,'b')>0) echo ' checked="checked"'?> /> vorige Woche</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="e"<?php if(strpos($ksNIvWerte,'e')>0) echo ' checked="checked"'?> /> voriger Monat</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="h"<?php if(strpos($ksNIvWerte,'h')>0) echo ' checked="checked"'?> /> voriges Quartal</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="k"<?php if(strpos($ksNIvWerte,'k')>0) echo ' checked="checked"'?> /> voriges Halbjahr</td>
   <td><input type="checkbox" class="admCheck" name="NIWerte[]" value="n"<?php if(strpos($ksNIvWerte,'n')>0) echo ' checked="checked"'?> /> voriges Jahr</td>
  </tr>
 </table></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Standardintervall<br />für Benutzer</td>
 <td colspan="2">
 <select name="NIntervall" size="1"><option value="-"<?php if($ksNIntervall=='-') echo ' selected="selected"'?>>Intervallfilter für angemeldete Benutzer nicht anzeigen</option>
  <option value="0"<?php if($ksNIntervall=='0') echo ' selected="selected"'?>>alle Termine</option>
  <option value="1"<?php if($ksNIntervall=='1') echo ' selected="selected"'?>>1 Tag</option>
  <option value="3"<?php if($ksNIntervall=='3') echo ' selected="selected"'?>>3 Tage</option>
  <option value="7"<?php if($ksNIntervall=='7') echo ' selected="selected"'?>>1 Woche</option>
  <option value="14"<?php if($ksNIntervall=='14')echo' selected="selected"'?>>2 Wochen</option>
  <option value="A"<?php if($ksNIntervall=='A') echo ' selected="selected"'?>>1 Monat</option>
  <option value="C"<?php if($ksNIntervall=='C') echo ' selected="selected"'?>>3 Monate</option>
  <option value="F"<?php if($ksNIntervall=='F') echo ' selected="selected"'?>>6 Monate</option>
  <option value="L"<?php if($ksNIntervall=='L') echo ' selected="selected"'?>>1 Jahr</option>
  <option value="a"<?php if($ksNIntervall=='a') echo ' selected="selected"'?>>diese Woche</option>
  <option value="d"<?php if($ksNIntervall=='d') echo ' selected="selected"'?>>dieser Monat</option>
  <option value="g"<?php if($ksNIntervall=='g') echo ' selected="selected"'?>>dieses Quartal</option>
  <option value="j"<?php if($ksNIntervall=='j') echo ' selected="selected"'?>>dieses Halbjahr</option>
  <option value="m"<?php if($ksNIntervall=='m') echo ' selected="selected"'?>>dieses Jahr</option>
  <option value="c"<?php if($ksNIntervall=='c') echo ' selected="selected"'?>>nächste Woche</option>
  <option value="f"<?php if($ksNIntervall=='f') echo ' selected="selected"'?>>nächster Monat</option>
  <option value="i"<?php if($ksNIntervall=='i') echo ' selected="selected"'?>>nächstes Quartal</option>
  <option value="l"<?php if($ksNIntervall=='l') echo ' selected="selected"'?>>nächste Halbjahr</option>
  <option value="o"<?php if($ksNIntervall=='o') echo ' selected="selected"'?>>nächstes Jahr</option>
  <option value="b"<?php if($ksNIntervall=='b') echo ' selected="selected"'?>>vorige Woche</option>
  <option value="e"<?php if($ksNIntervall=='e') echo ' selected="selected"'?>>voriger Monat</option>
  <option value="h"<?php if($ksNIntervall=='h') echo ' selected="selected"'?>>voriges Quartal</option>
  <option value="k"<?php if($ksNIntervall=='k') echo ' selected="selected"'?>>voriges Halbjahr</option>
  <option value="n"<?php if($ksNIntervall=='n') echo ' selected="selected"'?>>voriges Jahr</option>
  <option value="@"<?php if($ksNIntervall=='@') echo ' selected="selected"'?>>Terminarchiv</option>
 </select><div class="admMini">(Das eingestellte Standardintervall für Benutzer muss oben auch ausgewählt sein.)</div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Das Suchverhalten ist wie folgt beeinflussbar:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Suchschärfe <br />bei Tagesanzahl</td>
 <td colspan="2"><div>Bei Tagesanzahl 1, 3, 7 oder 14 im Intervallfilter kann streng oder großzügig gesucht werden.</div>
 <input type="radio" class="admRadio" name="IvExakt" value="1"<?php if($ksIvExakt) echo ' checked="checked"'?> /> exakt nur soviel Tage anzeigen &nbsp;
 <input type="radio" class="admRadio" name="IvExakt" value="0"<?php if(!$ksIvExakt) echo ' checked="checked"'?> /> großzügig auch den Folgetag anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Suchverhalten</td>
 <td colspan="2">Bei Benutzung des Intervallfilters kann eine vorhergehende Suchabfrage aus Feldwerten des Suchformulars aufgehoben oder im neu gewählten Intervall beibehalten werden.
 <div style="margin-top:3px;"><input type="radio" class="admRadio" name="SucheBleibt" value="0"<?php if(!$ksSucheBleibt) echo ' checked="checked"'?> /> bisherige Suche aufheben &nbsp; <input type="radio" class="admRadio" name="SucheBleibt" value="1"<?php if($ksSucheBleibt) echo ' checked="checked"'?> /> Suchbedingungen beibehalten</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">In der Terminliste können laufende Ereignisse (Termine die heute bzw. jetzt stattfinden) und aktuelle Ereignisse (der nächste anstehende oder laufende Termin in der nach Datum sortierten Liste) jeweils in einem besonderen Format der CSS-Klasse <i>div.kalTbZlAktE</i> bzw. <i>div.kalTbZlLfdE</i> dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">laufendes Ereignis</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="Laufendes" value="1"<?php if($ksLaufendes) echo ' checked="checked"'?> /> laufende Ereignisse hervorheben,
 <div>erkennen <input type="radio" class="admRadio" name="LfndEnde" value="1"<?php if($ksLfndEnde) echo ' checked="checked"'?> /> anhand von Beginn <i>und</i> Ende &nbsp; <input type="radio" class="admRadio" name="LfndEnde" value="0"<?php if(!$ksLfndEnde) echo ' checked="checked"'?> /> nur anhand des Beginns</div>
 erkennen <input type="radio" class="admRadio" name="LfndZeit" value="1"<?php if($ksLfndZeit) echo ' checked="checked"'?> /> anhand von Datum <i>und</i> Uhrzeit &nbsp; <input type="radio" class="admRadio" name="LfndZeit" value="0"<?php if(!$ksLfndZeit) echo ' checked="checked"'?> /> nur anhand des Datum
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">aktuelles Ereignis</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="Aktuelles" value="1"<?php if($ksAktuelles) echo ' checked="checked"'?> /> aktuelles Ereignis hervorheben,
 <div>erkennen <input type="radio" class="admRadio" name="AktuEnde" value="1"<?php if($ksAktuEnde) echo ' checked="checked"'?> /> anhand von Beginn <i>und</i> Ende &nbsp; <input type="radio" class="admRadio" name="AktuEnde" value="0"<?php if(!$ksAktuEnde) echo ' checked="checked"'?> /> nur anhand des Beginns</div>
 erkennen <input type="radio" class="admRadio" name="AktuZeit" value="1"<?php if($ksAktuZeit) echo ' checked="checked"'?> /> anhand von Datum <i>und</i> Uhrzeit &nbsp; <input type="radio" class="admRadio" name="AktuZeit" value="0"<?php if(!$ksAktuZeit) echo ' checked="checked"'?> /> nur anhand des Datum
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div>
 <div class="admMini"><u>Hinweis</u>: Falls Sie laufendes <i>und</i> aktuelles Ereignis gleichzeitig aktiviert haben, haben laufende Ereignisse Vorrang und es wird bei deren Vorhandensein kein aktuelles Ereignis gesondert hervorgehoben.</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über und/oder unter der Terminliste im Besucherbereich
kann eine Navigationsleiste zum seitenweisen Blättern durch lange Terminlisten angezeigt werden.
An welchen Positionen und bei wieviel Terminen soll eine solche Navigationsleiste erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td colspan="2"><input type="text" name="ListenLaenge" value="<?php echo $ksListenLaenge?>" size="2" /> Terminzeilen auf einer Listenseite der Terminliste
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Termine pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator oberhalb<br />der Terminliste</td>
 <td colspan="2"><select name="NaviOben" size="1" style="width:290px;"><option value="0">obere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksNaviOben==1) echo ' selected="selected"'?>>Navigator über dem Intervallfilter</option><option value="2"<?php if($ksNaviOben==2) echo ' selected="selected"'?>>Navigator unter dem Intervallfilter</option><option value="3"<?php if($ksNaviOben==3) echo ' selected="selected"'?>>Navigator unmittelbar über der Terminliste</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator unterhalb<br />der Terminliste</td>
 <td colspan="2"><select name="NaviUnten" size="1" style="width:290px;"><option value="0">untere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksNaviUnten==1) echo ' selected="selected"'?>>Navigator unmittelbar unter der Terminliste</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigatorstil</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NaviBild" value="1"<?php if($ksNaviBild) echo ' checked="checked"'?> /> die Navigationsleiste soll grafisch unterlegt sein</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über oder unter Terminliste im Besucherbereich kann ein Filter als Eingabefeldfeld für die Schnellsuche dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schnellsuche</td>
 <td colspan="2"><select name="SuchFilter" size="1" style="width:290px;">
  <option value="0"<?php if($ksSuchFilter==0) echo ' selected="selected"'?>>Suchfilter nicht anzeigen</option>
  <option value="1"<?php if($ksSuchFilter==1) echo ' selected="selected"'?>>Suchfilter über Navigator und Intervallfilter</option>
  <option value="2"<?php if($ksSuchFilter==2) echo ' selected="selected"'?>>Suchfilter über dem Intervallfilter</option>
  <option value="3"<?php if($ksSuchFilter==3) echo ' selected="selected"'?>>Suchfilter links vom Intervallfilter</option>
  <option value="4"<?php if($ksSuchFilter==4) echo ' selected="selected"'?>>Suchfilter rechts vom Intervallfilter</option>
  <option value="5"<?php if($ksSuchFilter==5) echo ' selected="selected"'?>>Suchfilter unter dem Intervallfilter</option>
  <option value="6"<?php if($ksSuchFilter==6) echo ' selected="selected"'?>>Suchfilter über der Ergebnismeldung</option>
  <option value="7"<?php if($ksSuchFilter==7) echo ' selected="selected"'?>>Suchfilter unter der Ergebnismeldung</option>
  <option value="8"<?php if($ksSuchFilter==8) echo ' selected="selected"'?>>Suchfilter direkt über der Terminliste</option>
  <option value="9"<?php if($ksSuchFilter==9) echo ' selected="selected"'?>>Suchfilter direkt unter der Terminliste</option>
  <option value="10"<?php if($ksSuchFilter==10) echo ' selected="selected"'?>>Suchfilter unter dem unteren Navigator</option>
 </select>
 <div><input type="checkbox" class="admCheck" name="SuchFltArchiv" value="1"<?php if($ksSuchFltArchiv) echo ' checked="checked"'?> /> Schnellsuche auch über das Archiv ermöglichen</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Monatsangabe innerhalb der Datumsanzeigen in der Terminliste können als zweistellige Zahl oder als ausgeschriebener Monatsname erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatsformat</td>
 <td colspan="2"><input type="radio" class="admRadio" name="MonatLLang" value="0"<?php if($ksMonatLLang<1) echo ' checked="checked"'?> /> Monat als Zahl &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatLLang" value="1"<?php if($ksMonatLLang==1) echo ' checked="checked"'?>/> Monatsname kurz &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatLLang" value="2"<?php if($ksMonatLLang==2) echo ' checked="checked"'?>/> Monatsname lang &nbsp; &nbsp; (<span class="admMini">Empfehlung: <i>als Zahl</i></span>)</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Nach jedem Monat, jeder Woche und/oder jedem Tag kann in der Terminliste eine Trennzeile eingeschoben werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatstrennzeile</td>
 <td colspan="2"><input type="radio" class="admRadio" name="MonatsTrenner" value="0"<?php if($ksMonatsTrenner<1) echo ' checked="checked"'?> /> keine &nbsp; <input type="radio" class="admRadio" name="MonatsTrenner" value="1"<?php if($ksMonatsTrenner==1) echo ' checked="checked"'?>/> leere Zeile &nbsp; <input type="radio" class="admRadio" name="MonatsTrenner" value="2"<?php if($ksMonatsTrenner==2) echo ' checked="checked"'?>/> Trennzeile mit Monatsnamen &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatsTrenner" value="3"<?php if($ksMonatsTrenner==3) echo ' checked="checked"'?>/> Trennzeile mit Monat und Jahr</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Wochentrennzeile</td>
 <td colspan="2"><input type="radio" class="admRadio" name="WochenTrenner" value="0"<?php if($ksWochenTrenner<1) echo ' checked="checked"'?> /> keine &nbsp; <input type="radio" class="admRadio" name="WochenTrenner" value="1"<?php if($ksWochenTrenner==1) echo ' checked="checked"'?>/> leere Zeile &nbsp; <input type="radio" class="admRadio" name="WochenTrenner" value="2"<?php if($ksWochenTrenner==2) echo ' checked="checked"'?>/> Trennzeile mit Wochennummer &nbsp; <input type="radio" class="admRadio" name="WochenTrenner" value="3"<?php if($ksWochenTrenner==3) echo ' checked="checked"'?>/> Trennzeile mit Woche und Jahr</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Tagestrennzeile</td>
 <td colspan="2"><input type="radio" class="admRadio" name="TagesTrenner" value="0"<?php if($ksTagesTrenner<1) echo ' checked="checked"'?> /> keine &nbsp; <input type="radio" class="admRadio" name="TagesTrenner" value="1"<?php if($ksTagesTrenner==1) echo ' checked="checked"'?>/> leere Zeile &nbsp; <input type="radio" class="admRadio" name="TagesTrenner" value="2"<?php if($ksTagesTrenner==2) echo ' checked="checked"'?>/> Trennzeile mit Datum</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste wird standardmäßig als Tabelle
mit nebeneinanderstehenden Spalten erzeugt.
Abweichend davon kann jeder Termindatensatz in einem individuellen Layout dargestellt werden,
das aus der Layoutschablone <i>kalListenZeile.htm</i> stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#3.2" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>
<div class="admMini" style="margin-top:5px;"><u>Hinweis</u>: Etliche der nachfolgenden Einstellungen gelten nur für das tabellarische Standardlayout.
Beim individuellen Layout müssen Sie für die meisten Feinheiten selbst sorgen.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneZeilen" value="0"<?php if(!$ksEigeneZeilen) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneZeilen" value="1"<?php if($ksEigeneZeilen) echo ' checked="checked"'?> /> individuelles Layout aus der Schablone <i>kalListenZeile.htm</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Der Text in jeder Zeile der Terminliste kann mittig, obenbündig oder untenbündig ausgerichtet sein.
Wie soll die vertikale Ausrichtung erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">vertikale<br />Textausrichtung</td>
 <td colspan="2"><select name="ListeVertikal" size="1" style="width:100px;"><option value="">mittig</option><option value="top"<?php if($ksListeVertikal=='top') echo ' selected="selected"'?>>obenbündig</option><option value="bottom"<?php if($ksListeVertikal=='bottom') echo ' selected="selected"'?>>untenbündig</option></select></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste im Besucherbereich kann bezüglich der anzuzeigenden Felder konfiguriert werden.
Welche Felder (getrennt für Gäste und gegebenefalls für angemeldete Benutzer) sollen in der Terminliste wie erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listenansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NListeAnders" value="1"<?php if($ksNListeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Tabellenspalten/Spaltenreihenfolgen sehen als Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">versteckte Termine</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NVerstecktSehen" value="1"<?php if($ksNVerstecktSehen) echo ' checked="checked"'?> /> angemeldete Benutzer sollen versteckte Termine sehen können</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">&nbsp;</td>
 <td>Tabellenspalte für Gäste / für angemeldete Benutzer <a href="<?php echo ADM_Hilfe?>LiesMich.htm#0.0" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td style="width:2%">optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.6.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">laufende Nummer <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.3.Nummer" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a><div class="admMini">Typ <i>Zählnummer</i></div></td>
 <td><select name="F0" size="1" style="width:42px;"><option value="0">---</option><option value="1"<?php if($kal_ListenFeld[0]==1) echo ' selected="selected"'?>>0</option></select> / <select name="N0" size="1" style="width:42px;"><option value="0">---</option><option value="1"<?php if($kal_NListenFeld[0]==1) echo ' selected="selected"'?>>0</option></select>
 mit <input type="text" name="NummerStellen" value="<?php echo $ksNummerStellen?>" size="1" style="width:18px;" /> stelliger lfd. Nr.
 als <input type="text" name="TxNr" value="<?php echo $ksTxNr?>" size="10" style="width:75px;" /></td>
 <td><input type="text" name="Z0" style="width:220px" value="<?php echo $kal_SpaltenStil[0]?>" /></td>
</tr>
<?php
 include('feldtypenInc.php');
 $sOpt='<option value="0">---</option>'; for($i=1;$i<$nFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if(!$k=$kal_ListenFeld[$i])  $sO=$sOpt; else $sO=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if(!$k=$kal_NListenFeld[$i]) $sN=$sOpt; else $sN=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if($i!=1){if($t=='v') $sO='<option value="0">---</option>';} //versteckt
  //else{$sO=substr($sO,strpos($sO,'<option',1)); $sN=substr($sN,strpos($sN,'<option',1));} //Datum
  if($t=='u'){
   array_splice($kal_NutzerFelder,1,1); $nNFz=count($kal_NutzerFelder);
   $sNOpt='<option value="0">--</option><option value="2">'.$kal_NutzerFelder[2].'</option>';
   for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.$kal_NutzerFelder[$j].'</option>';
  }
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo sprintf('%02d',$i).')&nbsp;'.$sFN.'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div>'?></td>
 <td>
<?php if($t!='c'&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){?>
 <select name="F<?php echo $i?>" size="1" style="width:42px;"><?php echo $sO?></select> / <select name="N<?php echo $i?>" size="1" style="width:42px;"><?php echo $sN?></select> &nbsp; &nbsp; &nbsp;
<?php if($t!='b'&&$t!='f'&&$t!='#'){?>
 <input type="checkbox" class="admCheck" name="S<?php echo $i?>" value="1" <?php if($kal_SortierFeld[$i]) echo ' checked="checked"'?> /> als&nbsp;Sortierfeld &nbsp; &nbsp;
<?php }else echo '<span style="padding-left:10em;">&nbsp;<span>';
       if($t!='l'&&$t!='e'&&$t!='v'&&$t!='f'&&$t!='#'){?>
 <input type="checkbox" class="admCheck" name="L<?php echo $i?>" value="1"<?php if($kal_LinkFeld[$i]) echo ' checked="checked"'?> /> als&nbsp;Detaillink
<?php } if($t=='u'){ ?>
 <div><select name="NutzerListFeld" style="width:140px;"><?php echo str_replace('value="'.($ksNutzerListFeld).'"','value="'.($ksNutzerListFeld).'" selected="selected"',$sNOpt)?></select> / <select name="NNutzerListFeld" style="width:140px;"><?php echo str_replace('value="'.($ksNNutzerListFeld).'"','value="'.($ksNNutzerListFeld).'" selected="selected"',$sNOpt)?></select></div>
<?php } ?>
 </td>
 <td><input type="text" name="Z<?php echo $i?>" style="width:220px;" value="<?php echo $kal_SpaltenStil[$i]?>" />
<?php }else{?>
 &nbsp;----<input type="hidden" name="F<?php echo $i?>" value="0" />
 </td>
 <td>&nbsp;
<?php }?>
 </td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste kann eine zusätzliche Spalte mit einem Klickschalter
für das Versenden einer Information über den Termin an einen beliebigen E-Mail-Empfänger enthalten (tell-a-friend-Funktion).<br />
Vor welcher Spalte soll ein solcher <img src="<?php echo $sHttp?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Informationsfunktion</td>
 <td colspan="2">zusätzliche Infospalte vor Spalte <select name="ListenInfo" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenInfo==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLInfo" value="1"<?php if($ksGastLInfo) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenInfTitel" value="<?php echo $ksListenInfTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste kann eine zusätzliche Spalte mit einem Klickschalter
für einen Erinnerungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er einige Tage vor dem Termin eine Erinnerungs-E-Mail zum betreffenden Termin erhält.<br />
Informieren Sie sich vor Aktivierung dieser Funktion jedoch über die Systemvoraussetzungen. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" align="top" width="13" height="13" border="0" title="Hilfe"></a><br />
Vor welcher Spalte soll ein solcher <img src="<?php echo $sHttp?>grafik/iconErinnern.gif" width="16" height="16" border="0" align="top" title="Erinnerung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungsservice</td>
 <td colspan="2">zusätzlichen Schalter vor Spalte <select name="ListenErinn" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenErinn==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLErinn" value="1"<?php if($ksGastLErinn) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenErinTitel" value="<?php echo $ksListenErinTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste kann eine zusätzliche Spalte mit einem Klickschalter
für den direkten Aufruf des Änderungsformulars oder Kopierformulars für den Termin enthalten.
Vor welcher Spalte soll ein solcher <img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" align="top" title="Ändern">-Änderungs-Klickschalter
bzw. <img src="<?php echo $sHttp?>grafik/icon_Kopie.gif" width="12" height="13" border="0" align="top" title="Kopieren">-Kopier-Klickschalter gegebenenfalls erscheinen?
<div>Wenn Sie keine Spaltenposition angeben wird standardmäßig kein Klickschalter zum Ändern bzw. Kiopieren in der Terminliste eingeblendet.
Sofern aber ein Benutzer oder Gast auf den Verweis [<i>Ändern</i>] über/unter dem Kalender klickt
wird ein solcher Änderungsschalter und Kopierschalter automatisch vor jeden Termin der Terminliste gesetzt.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsspalte</td>
 <td colspan="2">zusätzlichen Schalter vor Spalte <select name="ListenAendern" size="1"><option value="-1">--</option><?php for($i=0;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenAendern==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLAendern" value="1"<?php if($ksGastLAendern) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenAendTitel" value="<?php echo $ksListenAendTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopierspalte</td>
 <td colspan="2">zusätzlichen Schalter vor Spalte <select name="ListenKopieren" size="1"><option value="-1">--</option><?php for($i=0;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenKopieren==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLKopieren" value="1"<?php if($ksGastLKopieren) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenKopieTitel" value="<?php echo $ksListenKopieTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopierspalte in der<br />Änderungsliste</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="ListenAendKopieren" value="1"<?php if($ksListenAendKopieren) echo ' checked="checked"'?> /> Kopierspalte in der Änderungsliste auch dann anzeigen, wenn Änderungsspalte und Kopierspalte ausgeschaltet sind</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste kann eine zusätzliche Spalte mit einem Klickschalter
für einen Benachrichtigungsservice enthalten.
Über diese Funktion kann der Besucher erbitten, das er bei eventuellen Terminänderungen oder Terminlöschungen eine Benachritigungs-E-Mail zum betreffenden Termin erhält.<br />
Vor welcher Spalte soll ein solcher <img src="<?php echo $sHttp?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br />service</td>
 <td colspan="2">zusätzlichen Schalter vor Spalte <select name="ListenBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLBenachr" value="1"<?php if($ksGastLBenachr) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="ListenBenachTitel" value="<?php echo $ksListenBenachTitel?>" style="width:80px;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Terminliste kann eine zusätzliche Spalte mit einem Klickschalter für einen Terminexport enthalten.
Über diese Funktion kann der Besucher den jeweiligen Termin im iCal-Format exportieren und in eine Kalenderapplikation auf seinem Endgerät importieren/einfügen.<br />
Vor welcher Spalte soll ein solcher <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo KAL_TxCalIcon?>">-Klickschalter gegebenenfalls erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">iCal-Export</td>
 <td colspan="2">zusätzlichen Schalter vor Spalte <select name="ListenCal" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenCal==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLCal" value="1"<?php if($ksGastLCal) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="TxListenCalTitel" value="<?php echo $ksTxListenCalTitel?>" style="width:8em;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Memo</i> enthält
und diese Felder in der Terminliste aktiviert sind können wahlweise nur die ersten Zeichen des Feldinhalts angedeutet werden,
um Platzprobleme bei überlangen Feldinhalten zu vermeinden</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Memofelder kürzen</td>
 <td colspan="2"><input type="text" name="ListenMemoLaenge" value="<?php if($ksListenMemoLaenge) echo $ksListenMemoLaenge?>" style="width:40px;" /> <span class="admMini">leer lassen für ungekürzt oder Buchstabenanzahl nach der abgeschnitten werden soll</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Link</i> enthält
und diese Felder in der Terminliste aktiviert sind wird ein solcher Link normalerweise in Kurzform als Symbol dargestellt.
Der Link kann aber auch in Textform mit Darstellung der Adresse erfolgen, was jedoch mehr Spaltenbreite beansprucht.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkdarstellung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="LinkSymbol" value="1"<?php if($ksLinkSymbol) echo ' checked="checked"'?> /> Kurzform als <img src="<?php echo $sHttp?>grafik/iconLink.gif" width="16" height="16" border="0" title="Link">-Symbol &nbsp; <input type="radio" class="admRadio" name="LinkSymbol" value=""<?php if(!$ksLinkSymbol) echo ' checked="checked"'?> /> Langform mit Adressangabe</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls die Terminstruktur Felder vom Typ <i>Bild</i> enthält
und kein Bild zum Termin hochgeladen wurde kann in der Terminliste statt des Bildes
ein ErsatzBild aus dem Ordner <i>grafik</i> angezeigt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ersatzbild</td>
 <td colspan="2"><select name="ErsatzBildKlein" size="1" style="width:180px;"><option value="">kein Ersatzbild anzeigen</option><option value="kein-Bild.jpg"<?php if($ksErsatzBildKlein=='kein-Bild.jpg') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.jpg</option><option value="kein-Bild.gif"<?php if($ksErsatzBildKlein=='kein-Bild.gif') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.gif</option><option value="kein-Bild.png"<?php if($ksErsatzBildKlein=='kein-Bild.png') echo ' selected="selected"'?>>Ersatzbild: kein-Bild.png</option></select></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Druck der Terminliste können folgende Einstellungen gewählt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Drucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneDruckZeilen" value="0"<?php if(!$ksEigeneDruckZeilen) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneDruckZeilen" value="1"<?php if($ksEigeneDruckZeilen) echo ' checked="checked"'?> /> individuelles Layout aus der Schablone <i>kalDruckListenZeile.htm</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standarddrucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckLFarbig" value="0"<?php if(!$ksDruckLFarbig) echo ' checked="checked"'?> /> simpel &nbsp; &nbsp; <input type="radio" class="admRadio" name="DruckLFarbig" value="1"<?php if($ksDruckLFarbig) echo ' checked="checked"'?> /> formatiert (im CSS-Stil der Bildschirm-Terminliste)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Memofeld-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckLMemo" value="0"<?php if(!$ksDruckLMemo) echo ' checked="checked"'?> /> Memofelder in Terminlisten <i>nicht</i> drucken &nbsp; <input type="radio" class="admRadio" name="DruckLMemo" value="1"<?php if($ksDruckLMemo) echo ' checked="checked"'?> /> Memofelder in Terminlisten mit drucken<br />
 <input type="text" name="DruckLMemoLaenge" value="<?php if($ksDruckLMemoLaenge) echo $ksDruckLMemoLaenge?>" style="width:40px;" /> <span class="admMini">leer lassen für ungekürzt oder Anzahl der Zeichen nach denen abgeschnitten werden soll</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DruckLMailOffen" value="1"<?php if($ksDruckLMailOffen) echo ' checked="checked"'?> /> E-Mail-Adressen offen lesbar in der Druckliste darstellen
 <div class="admMini">Empfehlung: möglichst <i>nicht</i> aktivieren, weil auch Roboter/Spider die Druckseite einsehen könnten</div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>