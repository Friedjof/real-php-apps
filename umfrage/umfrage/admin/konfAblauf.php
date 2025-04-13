<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ablauf-Einstellungen','<script src="eingabe.js" type="text/javascript"></script>','KAl');

// BB-Formatleiste
define('UMF_TxBB_X','Schreiben Sie hier Ihren Text. Markieren Sie dann gewünschte Textpassagen und formatieren sie mit dem passenden Schalter.');
define('UMF_TxBB_B','fetterText: [b]Text[/b]');
define('UMF_TxBB_I','kursiver Text: [i]Text[/i]');
define('UMF_TxBB_U','unterstrichener Text: [u]Text[/u]');
define('UMF_TxBB_H','hochgestellter Text: [sup]Text[/sup]');
define('UMF_TxBB_D','tiefgestellter Text: [sub]Text[/sub]');
define('UMF_TxBB_C','zentrierter Text: [center]Text[/center]');
define('UMF_TxBB_R','rechsbündiger Text: [right]Text[/right');
define('UMF_TxBB_E','Aufzählung: [list]Text[/list]');
define('UMF_TxBB_N','Numerierung: [list=o]Text[/list]');
define('UMF_TxBB_L','Link einfügen: [url]www.domain.de[/url] oder [url]www.domain.de Linktext[/url]');
define('UMF_TxBB_P','Bild: [img]pfad/datei.jpg[/img] oder [img]http://www.domain.de/pfad/datei.gif[/img]');
define('UMF_TxBB_O','Schriftfarbe: [color=blue]Text[/color]');
define('UMF_TxBB_S','Schriftgröße: [size=+2]grosser Text[/size]');

if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('TxBeantworten'); if(fSetzUmfWert($v,'TxBeantworten','"')) $bNeu=true;
  $v=txtVar('TxAntwortFehlt'); if(fSetzUmfWert($v,'TxAntwortFehlt','"')) $bNeu=true;
  $v=(int)txtVar('UmfrUnscharf'); if(fSetzUmfWert(($v?true:false),'UmfrUnscharf','')) $bNeu=true;
  $v=txtVar('NachAbstimmen'); if(fSetzUmfWert($v,'NachAbstimmen',"'")) $bNeu=true;
  $v=(int)txtVar('GastLog'); if(fSetzUmfWert(($v?true:false),'GastLog','')) $bNeu=true;
  $v=(int)txtVar('TeilnehmerLog'); if(fSetzUmfWert(($v?true:false),'TeilnehmerLog','')) $bNeu=true;
  $v=(int)txtVar('NutzerLog'); if(fSetzUmfWert(($v?true:false),'NutzerLog','')) $bNeu=true;
  $v=txtVar('TxAbgestimmt'); if(fSetzUmfWert($v,'TxAbgestimmt','"')) $bNeu=true;
  $v=txtVar('TxGrafik'); if(fSetzUmfWert($v,'TxGrafik','"')) $bNeu=true;
  $v=str_replace('  ',' ',str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxFertigText'))))); if(fSetzUmfWert($v,'TxFertigText',"'")) $bNeu=true;
  $v=(int)txtVar('FertigHtml'); if(fSetzUmfWert(($v?true:false),'FertigHtml','')) $bNeu=true;
  $v=(int)txtVar('FertigMail'); if(fSetzUmfWert(($v?true:false),'FertigMail','')) $bNeu=true;
  $v=txtVar('TxFertigMlBtr'); if(fSetzUmfWert($v,'TxFertigMlBtr','"')) $bNeu=true;
  $v=str_replace('  ',' ',str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxFertigMlTxt'))))); if(fSetzUmfWert($v,'TxFertigMlTxt',"'")) $bNeu=true;
  $v=txtVar('GrafikLink'); if(fSetzUmfWert($v,'GrafikLink','"')) $bNeu=true;
  $v=txtVar('ZentrumLink'); if(fSetzUmfWert($v,'ZentrumLink','"')) $bNeu=true;
  $v=txtVar('NeuAnfangLink'); if(fSetzUmfWert($v,'NeuAnfangLink','"')) $bNeu=true;
  $v=(int)txtVar('Anonym'); if(fSetzUmfWert(($v?true:false),'Anonym','')) $bNeu=true;
  $v=max(0,(int)txtVar('IPAdressen')); if($usAnonym) $v=0;  if(fSetzUmfWert($v,'IPAdressen','')) $bNeu=true;
  $v=txtVar('TxGleicheAdresse'); if(fSetzUmfWert($v,'TxGleicheAdresse','"')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> durfte nicht geschrieben werden (Rechteproblem)!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Ablauf-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Ablauf-Einstellungen bleiben unverändert.</p>';
}else{ //GET
 $sMeld='<p class="admMeld">Stellen Sie den Ablauf des Umfrage-Scripts passend ein.</p>';
 $usTxBeantworten=UMF_TxBeantworten; $usTxAntwortFehlt=UMF_TxAntwortFehlt; $usUmfrUnscharf=UMF_UmfrUnscharf;
 $usNachAbstimmen=UMF_NachAbstimmen; $usTxAbgestimmt=UMF_TxAbgestimmt; $usTxGrafik=UMF_TxGrafik; $usTxFertigText=UMF_TxFertigText;
 $usGastLog=UMF_GastLog; $usTeilnehmerLog=UMF_TeilnehmerLog; $usNutzerLog=UMF_NutzerLog;
 $usFertigHtml=UMF_FertigHtml; $usGrafikLink=UMF_GrafikLink; $usZentrumLink=UMF_ZentrumLink; $usNeuAnfangLink=UMF_NeuAnfangLink;
 $usFertigMail=UMF_FertigMail; $usTxFertigMlBtr=UMF_TxFertigMlBtr; $usTxFertigMlTxt=UMF_TxFertigMlTxt;
 $usAnonym=UMF_Anonym; $usIPAdressen=UMF_IPAdressen; $usTxGleicheAdresse=UMF_TxGleicheAdresse;
}

//Scriptausgabe
echo $sMeld.NL;
?>

<form name="umfEingabe" action="konfAblauf.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über der Frage erscheint eine Meldung, die den Teilnehmer zum Handeln auffordert.
Sollte der Teilnehmer ohne jegliche Auswahl zur nächsten Frage übergehen wollen, erscheint eine Fehlermeldung.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Standardmeldung</td>
 <td><input type="text" name="TxBeantworten" value="<?php echo $usTxBeantworten?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Beantworten Sie nun die #. Frage.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlermeldung</td>
 <td><input type="text" name="TxAntwortFehlt" value="<?php echo $usTxAntwortFehlt?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Bitte beantworten Sie die Frage #!</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Eine Umfrage kann als Umfrage-A...Z mit einer Teilmenge der Fragen veranstaltet werden sofern alle oder einige Fragen der Umfrage-A...Z zugeordnet wurden. Für die Fragenauswahl während der Umfrage bestehen zwei Möglichkeiten.<br />
Diese Einstellung gilt jedoch <i>nur</i> für die Umfragen-A...Z, die <i>nicht</i> über den Menüpunkt <i>vorbereitete Umfragen</i> vereinbar wurden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schärfe bei der<br />Fragenauswahl</td>
 <td><input type="radio" class="admRadio" name="UmfrUnscharf" value="0<?php if(!$usUmfrUnscharf) echo '" checked="checked'?>" /> streng (scharf) &nbsp; &nbsp; <input type="radio" class="admRadio" name="UmfrUnscharf" value="1<?php if($usUmfrUnscharf) echo '" checked="checked'?>" /> unscharf (tolerant)
 <div class="admMini">Erklärung: Bei <i>streng</i> gehören <i>nur</i> die Fragen aus der Fragenliste zur Umfrage, die den jeweiligen Kennbuchstaben haben. Bei <i>unscharf</i> gehören noch die Fragen zusätzlich zur Umfrage, die gänzlich <i>ohne</i> Umfragenzuordnung sind.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Nach der Abstimmung werden die Antworten in jedem Fall in der Ergebnisliste anonym aufsummiert.
Darüberhinaus können die Daten in einer zusätzlichen Teilnahmeliste aus Datum/Uhrzeit, Personendaten (sofern vorhanden) und Antwortfolge abgelegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnahmeliste</td>
 <td><input type="checkbox" class="admCheck" name="GastLog" value="1"<?php if($usGastLog) echo '" checked="checked'?>" /> Abstimmung von Gästen aufzeichnen (anonym, ohne Personendaten)<br />
 <input type="checkbox" class="admCheck" name="TeilnehmerLog" value="1"<?php if($usTeilnehmerLog) echo '" checked="checked'?>" /> Abstimmung von Teilnehmern aufzeichnen (sofern Teilnehmerfunktionen aktiviert)<br />
 <input type="checkbox" class="admCheck" name="NutzerLog" value="1"<?php if($usNutzerLog) echo '" checked="checked'?>" /> Abstimmung von Benutzern aufzeichnen (sofern Benutzerfunktionen aktiviert)</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Nach der Abstimmung kann auf eine abschließende Textseite
oder auf eine Diagrammseite zur grafischen Darstellung der bisherigen Ergebnisse weitergeführt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">nach der Abstimmung</td>
 <td><input type="radio" class="admRadio" name="NachAbstimmen" value="Fertig<?php if($usNachAbstimmen=='Fertig') echo '" checked="checked'?>" /> erst einmal die Fertig-Textseite anzeigen &nbsp;
 <input type="radio" class="admRadio" name="NachAbstimmen" value="Grafik<?php if($usNachAbstimmen=='Grafik') echo '" checked="checked'?>" /> sofort Diagramm-Seite anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Diagrammüberschrift</td>
 <td><input type="text" name="TxGrafik" value="<?php echo $usTxGrafik?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Bisher wurde so abgestimmt:</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls die Abschluß-Textseite verwendet wird können auf dieser folgende Elemente vorkommen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Abschlußmeldung</td>
 <td><input type="text" name="TxAbgestimmt" value="<?php echo $usTxAbgestimmt?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Vielen Dank für Ihre Teilnahme!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="padding-top:28px;">Abschlußtext</td>
 <td><div title="Frage eingeben und dann formatieren"><?php echo fUmfBBToolbar('TxFertigText')?>
 <div><textarea name="TxFertigText" style="width:98%;height:8em;"><?php echo str_replace('\n ',"\n",$usTxFertigText)?></textarea></div>
 </div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweis (Link) zum<br />Auswertediagramm</td>
 <td><input type="text" name="GrafikLink" value="<?php echo $usGrafikLink?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: <i>zur Auswertung</i> oder leer lassen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweis (Link) zum<br />Nutzerzentrum bzw.<br />zur Umfrageauswahl</td>
 <td><input type="text" name="ZentrumLink" value="<?php echo $usZentrumLink?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: <i>zur Benutzerzentrum</i> oder <i>zur Umfragenauswahl</i> oder leer lassen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweis (Link) zum<br />Neustart/Neuanfang</td>
 <td><input type="text" name="NeuAnfangLink" value="<?php echo $usNeuAnfangLink?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: <i>zur Anfang</i> oder <i>zurück</i> oder <i>Logout</i> oder leer lassen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">HTML-Schablone</td>
 <td><input class="admCheck" type="checkbox" name="FertigHtml" value="1<?php if($usFertigHtml) echo '" checked="checked'?>" /> statt der drei obigen Texte die selbstgestaltete Schablone <i>umfFertig.inc.htm</i> verwenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Anläßlich jeder Abstimmung kann der Administrator eine E-Mail erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Abstimmungs-E-Mail</td>
 <td><input class="admCheck" type="checkbox" name="FertigMail" value="1<?php if($usFertigMail) echo '" checked="checked'?>" /> versenden &nbsp;
 <span class="admMini">(Empfehlung: <i>nicht</i> aktivieren)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Betreff</td>
 <td><input type="text" name="TxFertigMlBtr" value="<?php echo $usTxFertigMlBtr?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: neue Abstimmung im Umfragescript</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inhaltstext</td>
 <td><textarea name="TxFertigMlTxt" style="width:98%;height:5em;"><?php echo str_replace('\n ',"\n",$usTxFertigMlTxt)?></textarea>
 <div class="admMini">Textvorschlag: Im Umfragescript wurde soeben folgende Antwort eingetragen: #</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Umfrage-Script kann versuchen, mehrfache Abstimmungen ein und des selben Teilnehmers zu verhindern.
Dazu werden dessen IP-Adresse und dessen Browsertyp mit den vorherigen Abstimmungen verglichen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Mehrfachabstimmungen</td>
 <td><input type="radio" class="admRadio" name="Anonym" value="0<?php if(!$usAnonym) echo '" checked="checked'?>" /> Teilnehmerkennungen auswerten &nbsp;
 <input type="radio" class="admRadio" name="Anonym" value="1<?php if($usAnonym) echo '" checked="checked'?>" /> anonymes Abstimmen ohne Teilnehmererkennung zulassen
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Speichertiefe</td>
 <td><input type="text" name="IPAdressen" value="<?php echo $usIPAdressen?>" size="2" style="width:2.5em;"> letzte Benutzerkennungen berücksichtigen &nbsp;
 <span class="admMini">(Empfehlung: 3...10)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlermeldung</td>
 <td><input type="text" name="TxGleicheAdresse" value="<?php echo $usTxGleicheAdresse?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Wiederholte Abstimmungen werden nicht akzeptiert!</div></td>
</tr>
</table>

<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fUmfBBToolbar($Nam){
 $sHttp='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 $X =NL.'<table class="umfTool" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Bold',   0,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Italic', 2,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Uline',  4,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Center', 6,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Right',  8,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Enum',  10,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Number',12,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Pict',  14,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Link',  16,$sHttp).'</td>';
 $X.=NL.'  <td><img class="umfTool" src="tbColor.gif" style="margin-right:0;cursor:default;" title="'.UMF_TxBB_O.'" /></td>';
 $X.=NL.'  <td>
   <select class="umfTool" name="umf_Col'.$Nam.'" onChange="fCol('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.UMF_TxBB_O.'">
    <option value=""></option>
    <option style="color:black" value="black">Abc9</option>
    <option style="color:red;" value="red">Abc9</option>
    <option style="color:violet;" value="violet">Abc9</option>
    <option style="color:brown;" value="brown">Abc9</option>
    <option style="color:yellow;" value="yellow">Abc9</option>
    <option style="color:green;" value="green">Abc9</option>
    <option style="color:lime;" value="lime">Abc9</option>
    <option style="color:olive;" value="olive">Abc9</option>
    <option style="color:cyan;" value="cyan">Abc9</option>
    <option style="color:blue;" value="blue">Abc9</option>
    <option style="color:navy;" value="navy">Abc9</option>
    <option style="color:gray;" value="gray">Abc9</option>
    <option style="color:silver;" value="silver">Abc9</option>
    <option style="color:white;background-color:#999999" value="white">Abc9</option>
   </select>
  </td>';
 $X.=NL.'  <td><img class="umfTool" src="tbSize.gif" style="margin-right:0;cursor:default;" title="'.UMF_TxBB_S.'" /></td>';
 $X.=NL.'  <td>
   <select class="umfTool" name="umf_Siz'.$Nam.'" onChange="fSiz('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.UMF_TxBB_S.'">
    <option value=""></option>
    <option value="+2">&nbsp;+2</option>
    <option value="+1">&nbsp;+1</option>
    <option value="-1">&nbsp;- 1</option>
    <option value="-2">&nbsp;- 2</option>
   </select>
  </td>';
 $X.=NL.' </tr>';
 $X.=NL.'</table>'.NL;
 return $X;
}
function fDrawToolBtn($Nam,$vImg,$nTag,$sHttp){
 return '<img class="umfTool" src="tb'.$vImg.'.gif" onClick="fFmt('."'".$Nam."'".','.$nTag.')" style="background-image:url(tool.gif);" title="'.constant('UMF_TxBB_'.substr($vImg,0,1)).'" />';
}
?>