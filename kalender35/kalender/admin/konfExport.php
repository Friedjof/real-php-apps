<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminexport konfigurieren','','KIC');

if($_SERVER['REQUEST_METHOD']=='GET'){
 $ksListenCal=KAL_ListenCal; $ksGastLCal=KAL_GastLCal; $ksTxListenCalTitel=KAL_TxListenCalTitel;
 $ksDetailCal=KAL_DetailCal; $ksGastDCal=KAL_GastDCal; $ksTxCalZeile=KAL_TxCalZeile; $ksTxCalIcon=KAL_TxCalIcon;
 $ksICalName=KAL_ICalName; $ksICalNamNr=KAL_ICalNamNr;
 $ksCalDTSTART=KAL_CalDTSTART; $ksCalDTEND=KAL_CalDTEND; $ksCalZTSTART=KAL_CalZTSTART; $ksCalZTEND=KAL_CalZTEND;
 $ksCalTZID=KAL_CalTZID; $ksCalGanzTag=KAL_CalGanzTag; $ksCalStdDauer=KAL_CalStdDauer;
 $ksCalSUMMARY=KAL_CalSUMMARY; $ksCalSUMMARY2=KAL_CalSUMMARY2; $ksCalBindeSUMMA=KAL_CalBindeSUMMA;
 $ksCalDESCRIPTION=KAL_CalDESCRIPTION; $ksCalDESCRIPTION2=KAL_CalDESCRIPTION2; $ksCalBindeDESCR=KAL_CalBindeDESCR;
 $ksCalLOCATION=KAL_CalLOCATION; $ksCalLOCATION2=KAL_CalLOCATION2; $ksCalBindeLOCAT=KAL_CalBindeLOCAT;
 $ksCalORGANIZER=KAL_CalORGANIZER; $ksCalCATEGORIES=KAL_CalCATEGORIES;
 $ksCalGEO=KAL_CalGEO; $ksCalURL=KAL_CalURL; $ksCalATTACH=KAL_CalATTACH;
 $ksCalDTSTAMP=KAL_CalDTSTAMP; $ksCalCREATED=KAL_CalCREATED; $ksCalMODIFIED=KAL_CalMODIFIED;
 $ksCalSTATUS=KAL_CalSTATUS; $ksCalCLASS=KAL_CalCLASS; $ksCalTRANSP=KAL_CalTRANSP;
 $ksCalLenDESCR=KAL_CalLenDESCR; $ksCalPopup=KAL_CalPopup; $ksCalSSLExp=KAL_CalSSLExp; $ksCalSSLUrl=KAL_CalSSLUrl;
 $ksCalPopupBreit=KAL_CalPopupBreit; $ksCalPopupHoch=KAL_CalPopupHoch; $ksCalPopupX=KAL_CalPopupX; $ksCalPopupY=KAL_CalPopupY;
 $ksLinkOExpt=KAL_LinkOExpt; $ksLinkUExpt=KAL_LinkUExpt; $ksLnkOExptN=KAL_LnkOExptN; $ksLnkUExptN=KAL_LnkUExptN;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $s=txtVar('TxCalIcon'); if(fSetzKalWert($s,'TxCalIcon',"'")) $bNeu=true;
 $s=(int)txtVar('ListenCal'); if(fSetzKalWert($s,'ListenCal','')) $bNeu=true;
 $s=(int)txtVar('GastLCal'); if(fSetzKalWert(($s?true:false),'GastLCal','')) $bNeu=true;
 $s=txtVar('TxListenCalTitel'); if(fSetzKalWert($s,'TxListenCalTitel',"'")) $bNeu=true;
 $s=(int)txtVar('DetailCal'); if(fSetzKalWert($s,'DetailCal','')) $bNeu=true;
 $s=(int)txtVar('GastDCal'); if(fSetzKalWert(($s?true:false),'GastDCal','')) $bNeu=true;
 $s=txtVar('TxCalZeile'); if(fSetzKalWert($s,'TxCalZeile',"'")) $bNeu=true;
 $s=txtVar('CalTZID'); if(fSetzKalWert($s,'CalTZID',"'")) $bNeu=true;
 $s=str_replace(' ','_',str_replace(':','_',str_replace('/','_',txtVar('ICalName')))); if(fSetzKalWert($s,'ICalName',"'")) $bNeu=true;
 $s=(int)txtVar('ICalNamNr'); if(fSetzKalWert(($s?true:false),'ICalNamNr','')) $bNeu=true;
 $s=(int)txtVar('CalDTSTART'); if(fSetzKalWert($s,'CalDTSTART','')) $bNeu=true;
 $s=(int)txtVar('CalZTSTART'); if(fSetzKalWert($s,'CalZTSTART','')) $bNeu=true;
 $s=(int)txtVar('CalDTEND'); if(fSetzKalWert($s,'CalDTEND','')) $bNeu=true;
 $s=(int)txtVar('CalZTEND'); if(fSetzKalWert($s,'CalZTEND','')) $bNeu=true;
 $s=txtVar('CalGanzTagCb'); if(strlen($s)==0) $s=(int)txtVar('CalGanzTag'); if(fSetzKalWert($s,'CalGanzTag','')) $bNeu=true;
 $s=txtVar('CalStdDauerCb'); if(strlen($s)==0) $s=(int)txtVar('CalStdDauer'); if(fSetzKalWert($s,'CalStdDauer','')) $bNeu=true;
 $s=(int)txtVar('CalSUMMARY'); if(fSetzKalWert($s,'CalSUMMARY','')) $bNeu=true;
 $s=(int)txtVar('CalSUMMARY2'); if(fSetzKalWert($s,'CalSUMMARY2','')) $bNeu=true;
 $s=$_POST['CalBindeSUMMA']; if(fSetzKalWert($s,'CalBindeSUMMA',"'")) $bNeu=true;
 $s=(int)txtVar('CalDESCRIPTION'); if(fSetzKalWert($s,'CalDESCRIPTION','')) $bNeu=true;
 $s=(int)txtVar('CalDESCRIPTION2'); if(fSetzKalWert($s,'CalDESCRIPTION2','')) $bNeu=true;
 $s=$_POST['CalBindeDESCR']; if(fSetzKalWert($s,'CalBindeDESCR',"'")) $bNeu=true;
 $s=(int)txtVar('CalLenDESCR'); if(fSetzKalWert($s,'CalLenDESCR','')) $bNeu=true;
 $s=(int)txtVar('CalCATEGORIES'); if(fSetzKalWert($s,'CalCATEGORIES','')) $bNeu=true;
 $s=(int)txtVar('CalORGANIZER'); if(fSetzKalWert($s,'CalORGANIZER','')) $bNeu=true;
 $s=(int)txtVar('CalLOCATION'); if(fSetzKalWert($s,'CalLOCATION','')) $bNeu=true;
 $s=(int)txtVar('CalLOCATION2'); if(fSetzKalWert($s,'CalLOCATION2','')) $bNeu=true;
 $s=$_POST['CalBindeLOCAT']; if(fSetzKalWert($s,'CalBindeLOCAT',"'")) $bNeu=true;
 $s=(int)txtVar('CalGEO'); if(fSetzKalWert($s,'CalGEO','')) $bNeu=true;
 $s=txtVar('CalURL'); if(fSetzKalWert($s,'CalURL',"'")) $bNeu=true;
 $s=(int)txtVar('CalATTACH'); if(fSetzKalWert($s,'CalATTACH','')) $bNeu=true;
 $s=(int)txtVar('CalDTSTAMP'); if(fSetzKalWert($s,'CalDTSTAMP','')) $bNeu=true;
 $s=(int)txtVar('CalCREATED'); if(fSetzKalWert($s,'CalCREATED','')) $bNeu=true;
 $s=(int)txtVar('CalMODIFIED'); if(fSetzKalWert($s,'CalMODIFIED','')) $bNeu=true;
 $s=txtVar('CalSTATUS'); if(fSetzKalWert($s,'CalSTATUS',"'")) $bNeu=true;
 $s=txtVar('CalCLASS'); if(fSetzKalWert($s,'CalCLASS',"'")) $bNeu=true;
 $s=txtVar('CalTRANSP'); if(fSetzKalWert($s,'CalTRANSP',"'")) $bNeu=true;
 $s=max((int)txtVar('CalPopupBreit'),100); if(fSetzKalWert($s,'CalPopupBreit','')) $bNeu=true;
 $s=max((int)txtVar('CalPopupHoch'),150);  if(fSetzKalWert($s,'CalPopupHoch','')) $bNeu=true;
 $s=txtVar('CalPopupX'); if(strlen($s)<=0) $s=2; if(fSetzKalWert($s,'CalPopupX','')) $bNeu=true;
 $s=txtVar('CalPopupY'); if(strlen($s)<=0) $s=2; if(fSetzKalWert($s,'CalPopupY','')) $bNeu=true;
 $s=(int)txtVar('CalPopup'); if(fSetzKalWert(($s?true:false),'CalPopup','')) $bNeu=true;
 $s=txtVar('CalSSLExp'); if(fSetzKalWert(($s?true:false),'CalSSLExp','')) $bNeu=true;
 $s=txtVar('CalSSLUrl'); if(fSetzKalWert(($s?true:false),'CalSSLUrl','')) $bNeu=true;
 $v=txtVar('LinkOExpt'); if(fSetzKalWert($v,'LinkOExpt','"')) $bNeu=true;
 $v=txtVar('LinkUExpt'); if(fSetzKalWert($v,'LinkUExpt','"')) $bNeu=true;
 $v=txtVar('LnkOExptN'); if(fSetzKalWert(($v?true:false),'LnkOExptN','')) $bNeu=true;
 $v=txtVar('LnkUExptN'); if(fSetzKalWert(($v?true:false),'LnkUExptN','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   if(!$Msg) $Msg='<p class="admErfo">Die Einstellungen für den Terminexport wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
 }else if(!$Msg) $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}
if($ksCalLenDESCR<=0) $ksCalLenDESCR='';

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den Terminexport.</p>';
echo $Msg.NL;

$aDATE=array(); $aTIME=array(); $aDTSTAMP=array(0=>'aktuelle Uhrzeit'); $nFelder=count($kal_FeldName);
$aSUMMARY=array(); $aDESCRIPTION=array(); $aLOCATION=array();
$aCATEGORIES=array(); $aORGANIZER=array(); $aATTACH=array(); $aGEO=array();
for($i=1;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i]; $s=$kal_FeldName[$i];
 if($t=='d') $aDATE[$i]=$s; if($t=='z') $aTIME[$i]=$s; if($t=='@'&&$s!='ZUSAGE_BIS') $aDTSTAMP[$i]=$s;
 if($s!='TITLE'&&substr($s,0,5)!='META-'&&$t=='t'||$t=='a'||$t=='k'||$t=='m') $aSUMMARY[$i]=$s;
 if($s!='TITLE'&&substr($s,0,5)!='META-'&&$t=='t'||$t=='a'||$t=='k'||$t=='m') $aDESCRIPTION[$i]=$s;
 if($s!='TITLE'&&substr($s,0,5)!='META-'&&$t=='t'||$t=='a'||$t=='k'||$t=='o') $aLOCATION[$i]=$s;
 if($t=='k'||$t=='s'||$t=='a') $aCATEGORIES[$i]=$s; if($t=='e') $aORGANIZER[$i]=$s; if($t=='f'||$t=='b') $aATTACH[$i]=$s; if($t=='x') $aGEO[$i]=$s;
}
?>

<form action="konfExport.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Für die Besucher das Kalenders ist der Export einzelner Termine über einen Klickschalter innerhalb der Terminliste oder einen Klickschalter innerhalb der Detailanzeige zum Termin erreichbar.
Der <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxCalIcon?>">-Klickschalter kann wahlweise zu jedem Termin als zusätzliche Spalte in der Terminliste bzw. als zusätzliche Zeile in den Termindetails angeboten werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schalterbeschriftung</td>
 <td><img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxCalIcon?>">
  <input style="width:13em" type="text" name="TxCalIcon" value="<?php echo $ksTxCalIcon?>" />
  <span class="admMini">Empfehlung: <i>Export</i> oder <i>Termin übernehmen</i></span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminliste</td>
 <td>zusätzliche <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxCalIcon?>">-Exportspalte vor Spalte <select name="ListenCal" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($ksListenCal==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLCal" value="1"<?php if($ksGastLCal) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="TxListenCalTitel" value="<?php echo $ksTxListenCalTitel?>" style="width:8em;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Detailanzeige</td>
 <td>zusätzliche <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxCalIcon?>">-Exportzeile vor Zeile <select name="DetailCal" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailCal==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxCalZeile" value="<?php echo $ksTxCalZeile?>" size="15" style="width:8em;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDCal" value="1"<?php if($ksGastDCal) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Popup-Fenster</td>
 <td><input class="admCheck" type="checkbox" name="CalPopup" value="1"<?php if($ksCalPopup) echo ' checked="checked"'?> /> Exportseite als Popup-Fenster öffnen &nbsp; <span class="admMini"><u>Empfehlung</u>: aktivieren</span>
 <div><input type="text" name="CalPopupBreit" value="<?php echo $ksCalPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="CalPopupHoch" value="<?php echo $ksCalPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe</div>
 <div><input type="text" name="CalPopupX" value="<?php echo $ksCalPopupX?>" size="4" style="width:36px;" /> Pixel Popup vom linken Rand &nbsp; <input type="text" name="CalPopupY" value="<?php echo $ksCalPopupY?>" size="4" style="width:36px;" /> Pixel Popup-Beginn von oben</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über und unter der Terminliste kann inmitten der Navigationszeile ein Link für den Export der jeweils sichtbaren Terminliste eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Link oberhalb<br />des Kalenders</td>
 <td><input type="text" name="LinkOExpt" value="<?php echo $ksLinkOExpt?>" maxlength="32" style="width:150px;" /> Export &nbsp;
 <input class="admRadio" type="checkbox" name="LnkOExptN" value="1"<?php if($ksLnkOExptN) echo' checked="checked"'?> /> nur für Benutzer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Link unterhalb<br />des Kalenders</td>
 <td><input type="text" name="LinkUExpt" value="<?php echo $ksLinkUExpt?>" maxlength="32" style="width:150px;" /> Export &nbsp;
 <input class="admRadio" type="checkbox" name="LnkUExptN" value="1"<?php if($ksLnkUExptN) echo' checked="checked"'?> /> nur für Benutzer</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für den Terminexport ist die Angabe eine passenden Zeitzone notwendig.
Der Terminexport in diesem Script funktioniert jedoch nur problemlos für die <i>Mitteleuropäischen Zeitzonen</i>.
Es dürfen nur gültige Zeitzonen gesetzt werden wie z.B. <i>Europe/Berlin</i>, <i>Europe/Vienna</i> oder <i>Europe/Zurich</i>.
<span class="admMini">(Eine vollständige Liste der gültigen Zeitzonen ist u.a. zu finden unter <a style="color:#004" href="https://www.php.net/manual/de/timezones.php" target="hilfe" onclick="hlpWin(this.href);return false;">www.php.net/manual/de/timezones.php</a>)</span></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitzone</td>
 <td><input type="text" name="CalTZID" value="<?php echo $ksCalTZID?>" size="20" style="width:15em;" /><span class="admMini">Hinweis: <i>Europe/Berlin</i> funktioniert in ganz Mitteleuropa</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für den Export der Termine stellen Sie hier die passenden Felder Ihrer Terminstruktur als Datenquelle zusammen.
Bitte beachten Sie, dass nicht alle importierenden Programme und Geräte alle der hier genannten Felder verarbeiten können.
Beschränken Sie sich auf das für Ihre durchschnittliche Besucher wahrscheinlich Wesentliche.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Terminbeginn<div class="admMini">DTSTART</div></td>
 <td>Datum <select name="CalDTSTART" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aDATE,$ksCalDTSTART)?></select>
 und Uhrzeit <select name="CalZTSTART" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aTIME,$ksCalZTSTART)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminende<div class="admMini">DTEND</div></td>
 <td>Datum <select name="CalDTEND" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aDATE,$ksCalDTEND)?></select>
 und Uhrzeit <select name="CalZTEND" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aTIME,$ksCalZTEND)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termine ohne Uhrzeit</td>
 <td>sind <input class="admCheck" type="checkbox" name="CalGanzTagCb" value="0"<?php if(!$ksCalGanzTag) echo ' checked="checked"'?> /> Ganztagstermine oder beginnen um <select name="CalGanzTag"><?php echo admDrawOptions(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23),$ksCalGanzTag)?></select> Uhr</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termine ohne Ende</td>
 <td>sind <input class="admCheck" type="checkbox" name="CalStdDauerCb" value="0"<?php if(!$ksCalStdDauer) echo ' checked="checked"'?> /> Ganztagstermine oder dauern <select name="CalStdDauer"><?php echo admDrawOptions(array(0,1,2,3,4,5,6,7,8,9,10,11,12),$ksCalStdDauer)?></select> Stunden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kurzbezeichnung<div class="admMini">SUMMARY</div></td>
 <td><select name="CalSUMMARY" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aSUMMARY,$ksCalSUMMARY)?></select>
 <select name="CalBindeSUMMA" size="1"><option value=""></option><?php echo admDrawOptions(array('-'=>'-',' '=>' ',', '=>', ',': '=>': ',' - '=>' - '),$ksCalBindeSUMMA)?></select>
 <select name="CalSUMMARY2" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aSUMMARY,$ksCalSUMMARY2)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Langbeschreibung<div class="admMini">DESCRIPTION</div></td>
 <td><select name="CalDESCRIPTION" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aDESCRIPTION,$ksCalDESCRIPTION)?></select>
 <select name="CalBindeDESCR" size="1"><option value=""></option><?php echo admDrawOptions(array('-'=>'-',' '=>' ',', '=>', ',': '=>': ',' - '=>' - '),$ksCalBindeDESCR)?></select>
 <select name="CalDESCRIPTION2" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aDESCRIPTION,$ksCalDESCRIPTION2)?></select><br>
 jedoch maximal <input type="text" name="CalLenDESCR" value="<?php echo $ksCalLenDESCR?>" size="3" maxlength="5" style="width:3.5em;" /> Zeichen <span class="admMini">(leer lassen für <i>unbegrenzt</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ort<div class="admMini">LOCATION</div></td>
 <td><select name="CalLOCATION" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aLOCATION,$ksCalLOCATION)?></select>
 <select name="CalBindeLOCAT" size="1"><option value=""></option><?php echo admDrawOptions(array('-'=>'-',' '=>' ',', '=>', '),$ksCalBindeLOCAT)?></select>
 <select name="CalLOCATION2" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aLOCATION,$ksCalLOCATION2)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Koordinaten<div class="admMini">GEO</div></td>
 <td><select name="CalGEO" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aGEO,$ksCalGEO)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kategorie<div class="admMini">CATEGORIES</div></td>
 <td><select name="CalCATEGORIES" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aCATEGORIES,$ksCalCATEGORIES)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Organisator<div class="admMini">ORGANIZER</div></td>
 <td><select name="CalORGANIZER" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aORGANIZER,$ksCalORGANIZER)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Link zum Termin<div class="admMini">URL</div></td>
 <td><input type="text" name="CalURL" value="<?php echo $ksCalURL?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Dateianhang<div class="admMini">ATTACH</div></td>
 <td><select name="CalATTACH" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions($aATTACH,$ksCalATTACH)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termin erzeugt<div class="admMini">CREATED</div></td>
 <td><select name="CalCREATED" size="1" style="width:150px"><option value="-1">---</option><?php echo admDrawOptions($aDTSTAMP,$ksCalCREATED)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">zuletzt geändert<div class="admMini">LAST-MODIFIED</div></td>
 <td><select name="CalMODIFIED" size="1" style="width:150px"><option value="-1">---</option><?php echo admDrawOptions($aDTSTAMP,$ksCalMODIFIED)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitstempel<div class="admMini">DTSTAMP</div></td>
 <td><select name="CalDTSTAMP" size="1" style="width:150px"><option value="-1">---</option><?php echo admDrawOptions($aDTSTAMP,$ksCalDTSTAMP)?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminklasse<div class="admMini">CLASS</div></td>
 <td><select name="CalCLASS" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions(array('PUBLIC'=>'PUBLIC','PRIVATE'=>'PRIVATE','CONFIDENTIAL'=>'CONFIDENTIAL'),$ksCalCLASS)?></select> <span class="admMini">(nur in besonderen Fällen einstellen)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminstatus<div class="admMini">STATUS</div></td>
 <td><select name="CalSTATUS" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions(array('TENTATIVE'=>'TENTATIVE','CONFIRMED'=>'CONFIRMED','CANCELLED'=>'CANCELLED'),$ksCalSTATUS)?></select> <span class="admMini">(nur in besonderen Fällen einstellen)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Farbtransparenz<div class="admMini">TRANSP</div></td>
 <td><select name="CalTRANSP" size="1" style="width:150px"><option value="">---</option><?php echo admDrawOptions(array('OPAQUE'=>'OPAQUE','TRANSPARENT'=>'TRANSPARENT'),$ksCalTRANSP)?></select> <span class="admMini">(nur in besonderen Fällen einstellen)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Beim Terminexport erhält der gesamte exportierte Kalender bzw. der exportierte Einzeltermin einen Namen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Exportname</td>
 <td><input type="text" name="ICalName" value="<?php echo $ksICalName?>" size="20" style="width:15em;" />, um eine Zufallsnummer ergänzen <input class="admCheck" type="checkbox" name="ICalNamNr" value="1"<?php if($ksICalNamNr) echo ' checked="checked"'?> /></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Beim Terminexport zu Google gibt es zeitweilig Schwierigkeiten bezüglich SSL. Hier kann eingestellt wrden, ob der Export per SSL erfolgen soll oder nicht.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Export per Schalterklick</td>
 <td><input class="admCheck" type="radio" name="CalSSLExp" value="0"<?php if(!$ksCalSSLExp) echo ' checked="checked"'?>> ohne SSL (empfohlen) &nbsp; &nbsp; <input class="admCheck" type="radio" name="CalSSLExp" value="1"<?php if($ksCalSSLExp) echo ' checked="checked"'?>> mit SSL exportieren zum Google-Calendar per Schalterklick</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Hinzufügen per URL</td>
 <td><input class="admCheck" type="radio" name="CalSSLUrl" value="0"<?php if(!$ksCalSSLUrl) echo ' checked="checked"'?>> ohne SSL (empfohlen) &nbsp; &nbsp; <input class="admCheck" type="radio" name="CalSSLUrl" value="1"<?php if($ksCalSSLUrl) echo ' checked="checked"'?>> mit SSL hinzufügen per URL vom Google-Calendar aus</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();

function admDrawOptions($aV,$sSel){
 $sO='';
 foreach($aV as $k=>$v){
  $sO.='<option value="'.$k.'"'.($k!=$sSel?'':' selected="selected"').'>'.$v.'</option>';
 }
 return $sO;
}
?>