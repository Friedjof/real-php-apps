<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('RSS-Feed anpassen','','Rss');

$nFelder=count($kal_FeldName); $ksRssLanguage='';
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den RSS-Feed.</p>';
 $ksTxRssTitel=KAL_TxRssTitel; $ksTxRssBeschreibung=KAL_TxRssBeschreibung; $ksTxRssUrheber=KAL_TxRssUrheber;
 $ksRssSprache=KAL_RssSprache; $ksRssZeichensatz=KAL_RssZeichensatz; $ksRssBBFormat=KAL_RssBBFormat;
 $ksRssIntervall=KAL_RssIntervall; $ksRssAbHeute=KAL_RssAbHeute; $ksRssMitEnde=KAL_RssMitEnde; $ksRssMitZeit=KAL_RssMitZeit;
 $ksRssAnzahl=KAL_RssAnzahl; $ksRssSortFeld=KAL_RssSortFeld; $ksRssRueckw=KAL_RssRueckw;
 $aKalFelder=explode(';','0;'.KAL_RssFelder); $aKalTrenner=explode('";"','";'.KAL_RssTrenner.';"');
 $aKalFilter=explode('";"','";'.KAL_RssFilter.';"'); array_shift($aKalFilter); $ksRssLink=KAL_RssLink;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=inpVar('TxRssTitel'); if(fSetzKalWert($v,'TxRssTitel',"'")) $bNeu=true;
 $v=inpVar('TxRssBeschreibung'); if(fSetzKalWert($v,'TxRssBeschreibung',"'")) $bNeu=true;
 $v=inpVar('TxRssUrheber'); if(fSetzKalWert($v,'TxRssUrheber',"'")) $bNeu=true;
 if(!$v=inpVar('RssSprache')) $v=str_replace("'",'',inpVar('RssLanguage')); if(fSetzKalWert($v,'RssSprache',"'")) $bNeu=true;
 $v=(int)inpVar('RssZeichensatz'); if(fSetzKalWert($v,'RssZeichensatz','')) $bNeu=true;
 $v=(int)inpVar('RssBBFormat'); if(fSetzKalWert($v,'RssBBFormat','')) $bNeu=true;
 $v=$v=str_replace("'",'',inpVar('RssIntervall')); if(fSetzKalWert($v,'RssIntervall',"'")) $bNeu=true;
 $v=(int)inpVar('RssAbHeute'); if(fSetzKalWert(($v?true:false),'RssAbHeute','')) $bNeu=true;
 $v=(int)inpVar('RssMitEnde'); if(fSetzKalWert(($v?true:false),'RssMitEnde','')) $bNeu=true;
 $v=(int)inpVar('RssMitZeit'); if(fSetzKalWert(($v?true:false),'RssMitZeit','')) $bNeu=true;
 $v=(int)max(inpVar('RssAnzahl'),0); if(fSetzKalWert($v,'RssAnzahl','')) $bNeu=true;
 $v=(int)inpVar('RssSortFeld'); if(fSetzKalWert($v,'RssSortFeld','')) $bNeu=true;
 $v=(int)inpVar('RssRueckw'); if(fSetzKalWert(($v?true:false),'RssRueckw','')) $bNeu=true;
 $aKalFelder[1]=inpVar('RssTit1Fld'); $aKalFelder[2]=inpVar('RssTit2Fld'); $aKalFelder[3]=inpVar('RssTit3Fld');
 $aKalFelder[4]=inpVar('RssInh1Fld'); $aKalFelder[5]=inpVar('RssInh2Fld'); $aKalFelder[6]=inpVar('RssInh3Fld');
 $aKalFelder[7]=inpVar('RssKatFld');  $aKalFelder[8]=inpVar('RssAutFld');  $aKalFelder[9]=inpVar('RssPubFld');
 $aKalFelder[10]=inpVar('RssPubDFld'); $aKalFelder[11]=inpVar('RssPubZFld');
 $aKalTrenner[1]=inpVar('RssTit1Sep'); $aKalTrenner[2]=inpVar('RssTit2Sep');
 $aKalTrenner[3]=inpVar('RssInh1Sep'); $aKalTrenner[4]=inpVar('RssInh2Sep');
 $aKalFilter[0]=inpVar('RssFilt1Fld'); $aKalFilter[1]=inpVar('RssFilt11'); $aKalFilter[2]=inpVar('RssFilt12'); $aKalFilter[3]=inpVar('RssFilt13');
 $aKalFilter[4]=inpVar('RssFilt2Fld'); $aKalFilter[5]=inpVar('RssFilt21'); $aKalFilter[6]=inpVar('RssFilt22'); $aKalFilter[7]=inpVar('RssFilt23');
 $v=''; for($i=1;$i<=11;$i++) $v.=';'.sprintf('%0d',$aKalFelder[$i]); if(fSetzKalWert(substr($v,1),'RssFelder',"'")) $bNeu=true;
 $v=''; for($i=1;$i<=4;$i++) $v.=';"'.$aKalTrenner[$i].'"'; if(fSetzKalWert(substr($v,1),'RssTrenner',"'")) $bNeu=true;
 $v=''; for($i=0;$i<=7;$i++) $v.=';"'.$aKalFilter[$i].'"'; if(fSetzKalWert(substr($v,1),'RssFilter',"'")) $bNeu=true;
 $v=inpVar('RssLink'); if($v==KAL_Www.'kalender.php?kal_Aktion=detail') $v=''; if(fSetzKalWert($v,'RssLink',"'")) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Formulareinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}//POST
if($ksRssSprache!='de-de'&&$ksRssSprache!='de-at'&&$ksRssSprache!='de-ch'&&$ksRssSprache!='en-us'&&$ksRssSprache!='en-uk') $ksRssLanguage=$ksRssSprache;
if($ksRssLink=='') $ksRssLink=KAL_Www.'kalender.php?kal_Aktion=detail';
$ksRssTit1Fld=$aKalFelder[1]; $ksRssTit2Fld=$aKalFelder[2]; $ksRssTit3Fld=$aKalFelder[3];
$ksRssInh1Fld=$aKalFelder[4]; $ksRssInh2Fld=$aKalFelder[5]; $ksRssInh3Fld=$aKalFelder[6];
$ksRssKatFld=$aKalFelder[7]; $ksRssAutFld=$aKalFelder[8]; $ksRssPubFld=$aKalFelder[9];
$ksRssPubDFld=(isset($aKalFelder[10])?$aKalFelder[10]:0); $ksRssPubZFld=(isset($aKalFelder[11])?$aKalFelder[11]:0);
$ksRssTit1Sep=$aKalTrenner[1]; $ksRssTit2Sep=$aKalTrenner[2]; $ksRssInh1Sep=$aKalTrenner[3]; $ksRssInh2Sep=$aKalTrenner[4];
$ksRssFilt1Fld=$aKalFilter[0]; $ksRssFilt11=$aKalFilter[1]; $ksRssFilt12=$aKalFilter[2]; $ksRssFilt13=$aKalFilter[3];
$ksRssFilt2Fld=$aKalFilter[4]; $ksRssFilt21=$aKalFilter[5]; $ksRssFilt22=$aKalFilter[6]; $ksRssFilt23=$aKalFilter[7];

//Seitenausgabe
echo $Msg.NL;
$nFld=count($kal_FeldName); $sFldT='<option value="0"></option>'; $sFldI=$sFldT; $sFldD=$sFldT; $sFldZ=$sFldT; $nPubFld=0;
for($i=1;$i<$nFld;$i++){
 $t=$kal_FeldType[$i];
 if($t=='a'||$t=='d'||$t=='k'||$t=='o'||$t=='s'||$t=='t'||$t=='z') $sFldT.='<option value="'.$i.'">'.$kal_FeldName[$i].'</option>';
 if($t=='a'||$t=='d'||$t=='@'||$t=='e'||$t=='j'||$t=='k'||$t=='m'||$t=='o'||$t=='s'||$t=='t'||$t=='w'||$t=='u'||$t=='z') $sFldI.='<option value="'.$i.'">'.$kal_FeldName[$i].'</option>';
 if($t=='d') $sFldD.='<option value="'.$i.'">'.$kal_FeldName[$i].'</option>';
 if($t=='z') $sFldZ.='<option value="'.$i.'">'.$kal_FeldName[$i].'</option>';
 if($t=='@'&&$kal_FeldName[$i]!='ZUSAGE_BIS') $nPubFld=$i;
} ?>

<form action="konfRss.php" method="post">
<table class="admTabl"  border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Im Kopf Ihres RSS-Feeds werden folgende Angaben übermittelt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Titel<div>&lt;title&gt;</div></td>
 <td><input type="text" name="TxRssTitel" value="<?php echo $ksTxRssTitel?>" maxlength="100" style="width:100%" /><div class="admMini">Empfehlung: <i>Veranstaltungstermine</i> oder <i>aktuelle Termine</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Beschreibung<div>&lt;description&gt;</div></td>
 <td><input type="text" name="TxRssBeschreibung" value="<?php echo $ksTxRssBeschreibung?>" maxlength="200" style="width:100%" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Urheber/Verfasser<div>&lt;copyright&gt;</div></td>
 <td><input style="width:100%" type="text" name="TxRssUrheber" value="<?php echo $ksTxRssUrheber?>" maxlength="100" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sprachangabe<div>&lt;language&gt;</div></td>
 <td><select name="RssSprache" size="1" style="width:120px;"><option value=""></option><option value="de-de"<?php if($ksRssSprache=='de-de') echo' selected="selected"'?>>deutsch (de-de)</option><option value="de-at"<?php if($ksRssSprache=='de-at') echo' selected="selected"'?>>deutsch (de-at)</option><option value="de-ch"<?php if($ksRssSprache=='de-ch') echo' selected="selected"'?>>deutsch (de-ch)</option>><option value="en-us"<?php if($ksRssSprache=='en-us') echo' selected="selected"'?>>english (en-us)</option><option value="en-uk"<?php if($ksRssSprache=='en-uk') echo' selected="selected"'?>>english (en-uk)</option></select> &nbsp; oder andere Sprachkodierung <input type="text" name="RssLanguage" value="<?php echo $ksRssLanguage?>" style="width:50px;" /> </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeichensatz</td>
 <td><select name="RssZeichensatz" size="1" style="width:120px;"><option value="0">Standard</option><option value="2"<?php if($ksRssZeichensatz==2) echo' selected="selected"'?>>UTF-8</option></select> <span class="admMini">(Empfehlung: Standard, entspricht <i>ISO-8859-1</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Für Auswahl und Sortierung der Beiträge des RSS-Feeds werden folgende Kriterien benutzt:</td></tr>

<tr class="admTabl">
 <td class="admSpa1">Datumsbereich</div></td>
 <td>
  <select name="RssIntervall" size="1" style="width:120px;">
   <option value="0">alle Termine</option>
   <option value="1"<?php if($ksRssIntervall=='1') echo' selected="selected"'?>>heute</option>
   <option value="3"<?php if($ksRssIntervall=='3') echo' selected="selected"'?>>3 Tage</option>
   <option value="7"<?php if($ksRssIntervall=='7') echo' selected="selected"'?>>1 Woche</option>
   <option value="14"<?php if($ksRssIntervall=='14') echo' selected="selected"'?>>2 Wochen</option>
   <option value="A"<?php if($ksRssIntervall=='A') echo' selected="selected"'?>>1 Monat</option>
   <option value="C"<?php if($ksRssIntervall=='C') echo' selected="selected"'?>>3 Monate</option>
   <option value="F"<?php if($ksRssIntervall=='F') echo' selected="selected"'?>>6 Monate</option>
   <option value="L"<?php if($ksRssIntervall=='L') echo' selected="selected"'?>>1 Jahr</option>
   <option value="@"<?php if($ksRssIntervall=='@') echo' selected="selected"'?>>abgelaufene Termine</option>
  </select> &nbsp; <input class="admRadio" type="radio" name="RssAbHeute" value="1"<?php if($ksRssAbHeute) echo ' checked="checked"'?> /> ab heute &nbsp; <input class="admRadio" type="radio" name="RssAbHeute" value="0"<?php if(!$ksRssAbHeute) echo ' checked="checked"'?> /> ab Anzeigedauer (<?php echo KAL_ZeigeAltesNochTage?> Tage rückwirkend, <span class="admMini">siehe <i>Allgemeines</i></span>)
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bereichsstart</div></td>
 <td><div>erkennen <input type="radio" class="admRadio" name="RssMitEnde" value="1"<?php if($ksRssMitEnde) echo ' checked="checked"'?> /> anhand von Beginn <i>und</i> Ende &nbsp; <input type="radio" class="admRadio" name="RssMitEnde" value="0"<?php if(!$ksRssMitEnde) echo ' checked="checked"'?> /> nur anhand des Beginns</div>
 erkennen <input type="radio" class="admRadio" name="RssMitZeit" value="1"<?php if($ksRssMitZeit) echo ' checked="checked"'?> /> anhand von Datum <i>und</i> Uhrzeit &nbsp; <input type="radio" class="admRadio" name="RssMitZeit" value="0"<?php if(!$ksRssMitZeit) echo ' checked="checked"'?> /> nur anhand des Datum
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inhaltsfilter</div></td>
 <td>
  <div>
  <select name="RssFilt1Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssFilt1Fld.'"','="'.$ksRssFilt1Fld.'" selected="selected"',$sFldI)?></select>
  wie <input type="text" name="RssFilt11" value="<?php echo $ksRssFilt11?>" style="width:95px;" />
  oder wie <input type="text" name="RssFilt12" value="<?php echo $ksRssFilt12?>" style="width:95px;" />
  aber nicht wie <input type="text" name="RssFilt13" value="<?php echo $ksRssFilt13?>" style="width:95px;" />
  </div><div>
  <select name="RssFilt2Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssFilt2Fld.'"','="'.$ksRssFilt2Fld.'" selected="selected"',$sFldI)?></select>
  wie <input type="text" name="RssFilt21" value="<?php echo $ksRssFilt21?>" style="width:95px;" />
  oder wie <input type="text" name="RssFilt22" value="<?php echo $ksRssFilt22?>" style="width:95px;" />
  aber nicht wie <input type="text" name="RssFilt23" value="<?php echo $ksRssFilt23?>" style="width:95px;" />
  </div>oder alles leer lassen für ungefilterte Ausgabe
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Beitragsanzahl</div></td>
 <td><input name="RssAnzahl" style="width:32px;" value="<?php echo ($ksRssAnzahl?$ksRssAnzahl:'')?>" /> <span class="admMini"><i>1...50</i> oder leer lassen für <i>alle</i> im Intervall</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfolge</div></td>
 <td><select name="RssSortFeld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssSortFeld.'"','="'.$ksRssSortFeld.'" selected="selected"',$sFldI)?></select> &nbsp; <input class="admRadio" type="radio" name="RssRueckw" value="0"<?php if(!$ksRssRueckw) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input class="admRadio" type="radio" name="RssRueckw" value="1"<?php if($ksRssRueckw) echo ' checked="checked"'?> /> absteigend</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Für den Inhalt der einzelnen Beiträge des RSS-Feeds werden folgende Datenelemente benutzt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Beitragstitel<div>&lt;title&gt;</div></td>
 <td>
  <div style="float:left;width:473px;">
  <select name="RssTit1Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssTit1Fld.'"','="'.$ksRssTit1Fld.'" selected="selected"',$sFldT)?></select>
  <select name="RssTit1Sep" size="1" style="width:40px;">
   <option value=" "<?php if($ksRssTit1Sep==' ') echo' selected="selected"'?>>&nbsp;</option>
   <option value=", "<?php if($ksRssTit1Sep==', ') echo' selected="selected"'?>>, </option>
   <option value=": "<?php if($ksRssTit1Sep==': ') echo' selected="selected"'?>>: </option>
   <option value=" - "<?php if($ksRssTit1Sep==' - ') echo' selected="selected"'?>>&nbsp;- </option>
  </select>
  <select name="RssTit2Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssTit2Fld.'"','="'.$ksRssTit2Fld.'" selected="selected"',$sFldT)?></select>
  <select name="RssTit2Sep" size="1" style="width:40px;">
   <option value=" "<?php if($ksRssTit2Sep==' ') echo' selected="selected"'?>>&nbsp;</option>
   <option value=", "<?php if($ksRssTit2Sep==', ') echo' selected="selected"'?>>, </option>
   <option value=": "<?php if($ksRssTit2Sep==': ') echo' selected="selected"'?>>: </option>
   <option value=" - "<?php if($ksRssTit2Sep==' - ') echo' selected="selected"'?>>&nbsp;- </option>
  </select>
  <select name="RssTit3Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssTit3Fld.'"','="'.$ksRssTit3Fld.'" selected="selected"',$sFldT)?></select>
  <div class="admMini">Empfehlung: <i>Datum</i>, <i>Ort</i> oder <i>Datum</i>: <i>Veranstaltung</i></div>
  </div>
  <div class="admMini" style="margin-left:475px;">aus 2 oder 3 Feldern zusammensetzen</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Beitragsbeschreibung<div>&lt;description&gt;</div></td>
 <td>
  <div style="float:left;width:473px;">
  <select name="RssInh1Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssInh1Fld.'"','="'.$ksRssInh1Fld.'" selected="selected"',$sFldI)?></select>
  <input type="text" name="RssInh1Sep" value="<?php echo $ksRssInh1Sep?>" style="width:40px;" />
  <select name="RssInh2Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssInh2Fld.'"','="'.$ksRssInh2Fld.'" selected="selected"',$sFldI)?></select>
  <input type="text" name="RssInh2Sep" value="<?php echo $ksRssInh2Sep?>" style="width:40px;" />
  <select name="RssInh3Fld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssInh3Fld.'"','="'.$ksRssInh3Fld.'" selected="selected"',$sFldI)?></select>
  <div class="admMini">Beispiel-1: <i>Veranstaltung</i> in <i>Ort</i>, <i>Preis</i> &nbsp; Beispiel-2: <i>Veranstaltung</i> &lt;br /&gt; Eintritt: <i>Preis</i></div>
  </div>
  <div class="admMini" style="margin-left:475px;">aus 1 bis 3 Feldern und Trennelementen zusammensetzen</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Textformatierung</td>
 <td>sofern im Text BB-Code vorkommt soll dieser <select name="RssBBFormat" size="1" style="width:120px;"><option value="2"<?php if($ksRssBBFormat==2) echo' selected="selected"'?>>HTML-formatiert</option><option value="0"<?php if($ksRssBBFormat==0) echo' selected="selected"'?>>als blanker Text</option><option value="1"<?php if($ksRssBBFormat==1) echo' selected="selected"'?>>als BB-Code</option></select> ausgegeben werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verknüpfung<div>&lt;link&gt;</div></td>
 <td><input type="text" name="RssLink" value="<?php echo $ksRssLink?>" style="width:500px;" /> <span class="admMini">(ohne <i>http://</i>)</span>
 <div class="admMini">Standard: <i><?php echo KAL_Www.'kalender.php?kal_Aktion=detail'?></i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kategorie (optional)<div>&lt;category&gt;</div></td>
 <td><select name="RssKatFld" size="1" style="width:120px;"><option value=""></option><?php if($p=array_search('k',$kal_FeldType)) echo'<option value="'.$p.'"'.($ksRssKatFld!=$p?'':' selected="selected"').'>'.$kal_FeldName[$p].'</option>'?></select> <span class="admMini">(möglich, sofern ein Feld vom Typ <i>Kategorie</i> in der Terminstruktur enthalten)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Autor (optional)<div>&lt;author&gt;</div></td>
 <td><select name="RssAutFld" size="1" style="width:120px;"><option value=""></option><?php if($p=array_search('e',$kal_FeldType)) echo'<option value="'.$p.'"'.($ksRssAutFld!=$p?'':' selected="selected"').'>'.$kal_FeldName[$p].'</option>'?></select> <span class="admMini">(möglich, sofern ein Feld vom Typ <i>E-Mail</i> in der Terminstruktur enthalten)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eintragsdatum (opt.)<div>&lt;pubDate&gt;</div></td>
 <td><select name="RssPubFld" size="1" style="width:120px;"><option value=""></option><?php if($nPubFld>0) echo'<option value="'.$nPubFld.'"'.($ksRssPubFld!=$nPubFld?'':' selected="selected"').'>'.$kal_FeldName[$nPubFld].'</option>'?></select> <span class="admMini">(möglich, sofern ein Feld vom Typ <i>Eintragszeit</i> in der Terminstruktur enthalten)</span><br>
 <select name="RssPubDFld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssPubDFld.'"','="'.$ksRssPubDFld.'" selected="selected"',$sFldD)?></select>&nbsp;<select name="RssPubZFld" size="1" style="width:120px;"><?php echo str_replace('="'.$ksRssPubZFld.'"','="'.$ksRssPubZFld.'" selected="selected"',$sFldZ)?></select> <span class="admMini">(alternativ aus einem <i>Datum</i> plus <i>Zeit</i> zusammengesetzt)</span></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function inpVar($Var){return (isset($_POST[$Var])?str_replace('"',"'",str_replace('  ',' ',stripslashes($_POST[$Var]))):'');}
?>