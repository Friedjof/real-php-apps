<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle=''; $fsProSeite=1; $fsProSeiteN='';
echo fSeitenKopf('Ablaufregeln festlegen','<script type="text/javascript" src="eingabe.js"></script>','KAl');

if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $fsLernBemerkung=FRA_LernBemerkung;
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('TxBeginn'); if(fSetzFraWert($v,'TxBeginn','"')) $bNeu=true;
  $v=txtVar('TxNormal'); if(fSetzFraWert($v,'TxNormal','"')) $bNeu=true;
  $v=txtVar('TxHandle'); if(fSetzFraWert($v,'TxHandle','"')) $bNeu=true;
  $fsProSeite=(int)txtVar('ProSeite'); $fsProSeiteN=max(2,txtVar('ProSeiteN')); if($fsProSeite>1) $fsProSeite=$fsProSeiteN; else $fsProSeiteN='';
  if(fSetzFraWert($fsProSeite,'ProSeite','')) $bNeu=true;
  $v=(int)txtVar('ZufallsAntwort'); if(fSetzFraWert(($v?true:false),'ZufallsAntwort','')) $bNeu=true;
  $v=(int)txtVar('RadioButton'); if(fSetzFraWert(($v?true:false),'RadioButton','')) $bNeu=true;
  $v=(int)txtVar('SofortBeiKlick'); if(fSetzFraWert($v,'SofortBeiKlick','')) $bNeu=true;
  $v=(int)txtVar('Auslassen'); if(fSetzFraWert(($v?true:false),'Auslassen','')) $bNeu=true;
  $v=txtVar('TxAnsEnde'); if(fSetzFraWert($v,'TxAnsEnde','"')) $bNeu=true;
  $v=(int)txtVar('PruefeAntw'); if(fSetzFraWert(($v?true:false),'PruefeAntw','')) $bNeu=true;
  $v=(int)txtVar('HilfeBemerkung'); if(fSetzFraWert(($v?true:false),'HilfeBemerkung','')) $bNeu=true;
  $v=txtVar('TxNochMal'); if(fSetzFraWert($v,'TxNochMal','"')) $bNeu=true;
  $v=(int)txtVar('PruefeAnzahl'); if(fSetzFraWert(($v?true:false),'PruefeAnzahl','')) $bNeu=true;
  $v=txtVar('TxHalb'); if(fSetzFraWert($v,'TxHalb','"')) $bNeu=true;
  $v=(int)txtVar('AntwortVersuche'); if(fSetzFraWert($v,'AntwortVersuche','')) $bNeu=true;
  $v=(int)txtVar('Offenlegen'); if(fSetzFraWert(($v?true:false),'Offenlegen','')) $bNeu=true;
  $v=(int)txtVar('OffenNurFalsche'); if(fSetzFraWert(($v?true:false),'OffenNurFalsche','')) $bNeu=true;
  $v=txtVar('TxStimmt'); if(fSetzFraWert($v,'TxStimmt','"')) $bNeu=true;
  $v=txtVar('TxAnders'); if(fSetzFraWert($v,'TxAnders','"')) $bNeu=true;
  $v=txtVar('ZeigeBemerkung'); if(txtVar('CbBemerkung2')) $v='selektiv'; elseif(txtVar('CbBemerkung')) $v='aufdecken'; elseif($v=='selektiv'||$v=='aufdecken') $v='';
  if($v!=FRA_ZeigeBemerkung){if(fSetzFraWert($v,'ZeigeBemerkung','"')) $bNeu=true;}else $fsZeigeBemerkung=$v;
  $v=(int)txtVar('LernModus'); if(fSetzFraWert(($v?true:false),'LernModus','')) $bNeu=true;
  $v=txtVar('TxLernen'); if(fSetzFraWert($v,'TxLernen','"')) $bNeu=true;
  $v=0; if(txtVar('CbLMBemerk2')) $v=2; elseif(txtVar('CbLMBemerk')) $v=1; elseif(txtVar('CbLMBemerk4')) $v=4; elseif(txtVar('CbLMBemerk3')) $v=3; if(fSetzFraWert($v,'LernBemerkung','')) $bNeu=true;
  $v=(int)txtVar('ZeitLimitM')*60+(int)txtVar('ZeitLimitS'); if(fSetzFraWert($v,'ZeitLimit','')) $bNeu=true;
  $v=(int)txtVar('RestZeit'); if(fSetzFraWert(($v?true:false),'RestZeit','')) $bNeu=true;
  $v=txtVar('SchalterTxZurueck'); if(fSetzFraWert($v,'SchalterTxZurueck',"'")) $bNeu=true;
  $v=txtVar('SchalterTxGeheZu'); if(fSetzFraWert($v,'SchalterTxGeheZu',"'")) $bNeu=true;
  $v=(int)txtVar('Schalter2Zeilen'); if(fSetzFraWert(($v?true:false),'Schalter2Zeilen','')) $bNeu=true;
  $v=txtVar('TxAllesFertig'); if(fSetzFraWert($v,'TxAllesFertig','"')) $bNeu=true;
  $v=txtVar('TxLernOk'); if(fSetzFraWert($v,'TxLernOk','"')) $bNeu=true;
  $v=(int)txtVar('BewertungsSeite'); if(fSetzFraWert(($v?true:false),'BewertungsSeite','')) $bNeu=true;
  $v=txtVar('TxZeigeLoesung'); if(fSetzFraWert($v,'TxZeigeLoesung',"'")) $bNeu=true;
  $v=(int)txtVar('LoesungsSeite'); if(fSetzFraWert(($v?true:false),'LoesungsSeite','')) $bNeu=true;
  $v=(int)txtVar('LoesungsFalsche'); if(fSetzFraWert(($v?true:false),'LoesungsFalsche','')) $bNeu=true;
  $v=max((int)txtVar('LoesungsAnmk'),(int)txtVar('LoesungsAnm2')); if(fSetzFraWert(($v?$v:false),'LoesungsAnmk','')) $bNeu=true;
  $v=str_replace('  ',' ',str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxFertigText'))))); if(fSetzFraWert($v,'TxFertigText',"'")) $bNeu=true;
  $v=txtVar('TxNeuStart'); if(fSetzFraWert($v,'TxNeuStart','"')) $bNeu=true;
  $v=(int)txtVar('FertigHtml'); if(fSetzFraWert(($v?true:false),'FertigHtml','')) $bNeu=true;
  if($bNeu){ //Speichern
   if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
    if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
   }else{$sMeld='<p class="admFehl">Wollen Sie die Änderung wirklich für <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Ablauf-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 elseif(!$bNeu) $sMeld.='<p class="admMeld">Die Ablauf-Einstellungen bleiben unverändert.</p>';
}else{ //GET
 $fsTxBeginn=FRA_TxBeginn; $fsTxNormal=FRA_TxNormal; $fsTxHandle=FRA_TxHandle;
 $fsZufallsAntwort=FRA_ZufallsAntwort; $fsRadioButton=FRA_RadioButton; $fsSofortBeiKlick=FRA_SofortBeiKlick;
 $fsAuslassen=FRA_Auslassen; $fsTxAnsEnde=FRA_TxAnsEnde;
 $fsPruefeAntw=FRA_PruefeAntw; $fsTxNochMal=FRA_TxNochMal;
 $fsPruefeAnzahl=FRA_PruefeAnzahl; $fsTxHalb=FRA_TxHalb; $fsAntwortVersuche=FRA_AntwortVersuche;
 $fsOffenlegen=FRA_Offenlegen; $fsOffenNurFalsche=FRA_OffenNurFalsche; $fsTxStimmt=FRA_TxStimmt; $fsTxAnders=FRA_TxAnders;
 $fsProSeite=FRA_ProSeite; if($fsProSeite>1){$fsProSeiteN=$fsProSeite; $fsProSeite=2;}
 $fsZeigeBemerkung=FRA_ZeigeBemerkung; $fsLernBemerkung=FRA_LernBemerkung; $fsHilfeBemerkung=FRA_HilfeBemerkung;
 $fsLernModus=FRA_LernModus; $fsTxLernen=FRA_TxLernen; $fsZeitLimit=FRA_ZeitLimit; $fsRestZeit=FRA_RestZeit;
 $fsSchalterTxZurueck=FRA_SchalterTxZurueck; $fsSchalterTxGeheZu=FRA_SchalterTxGeheZu; $fsSchalter2Zeilen=FRA_Schalter2Zeilen;
 $fsTxAllesFertig=FRA_TxAllesFertig; $fsTxLernOk=FRA_TxLernOk; $fsTxFertigText=FRA_TxFertigText; $fsTxNeuStart=FRA_TxNeuStart;
 $fsBewertungsSeite=FRA_BewertungsSeite; $fsFertigHtml=FRA_FertigHtml;
 $fsLoesungsSeite=FRA_LoesungsSeite; $fsTxZeigeLoesung=FRA_TxZeigeLoesung; $fsLoesungsFalsche=FRA_LoesungsFalsche; $fsLoesungsAnmk=FRA_LoesungsAnmk;
}//GET

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Stellen Sie den Ablauf des Testfragen-Scripts passend ein.</p>';
echo $sMeld.NL;
?>

<form name="fraEingabe" action="konfAblauf.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Standardkonzept des Testfragen-Scripts geht davon aus,
dass pro Bildschirmseite genau <i>eine</i> Frage dargeboten wird. Es sind aber auch andere Aufteilungen möglich.<br>
Falls Sie jedoch eine der anderen Fragenaufteilungen wählen, werden einige der untenfolgenden Einstellungen eventuell nicht funktionieren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:12em;">Aufteilung der <br>Fragen pro Seite</td>
 <td>
  <div><input type="radio" class="admRadio" name="ProSeite" value="1<?php if($fsProSeite==1) echo '" checked="checked'?>" /> eine Frage pro Bildschirmseite (Standard)</div>
  <div><input type="radio" class="admRadio" name="ProSeite" value="2<?php if($fsProSeite>1)  echo '" checked="checked'?>" /> mehrere Fragen pro Seite und zwar <input type="text" name="ProSeiteN" value="<?php echo $fsProSeiteN?>" size="3" style="width:2.5em;" /> Fragen</div>
  <div><input type="radio" class="admRadio" name="ProSeite" value="0<?php if($fsProSeite<=0) echo '" checked="checked'?>" /> alle Fragen auf einer Seite</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Über der <i>ersten</i> Frage(seite) erscheint eine Meldung, die den Teilnehmer zum Handeln auffordert.
Über den weiteren Fragen(seiten) erscheint eine andere Meldung, solange keine aussergewöhnliche Situation auftritt.
Sollte der Teilnehmer ohne jegliche Auswahl zur nächsten Frage(seite) übergehen wollen, erscheint eine Fehlermeldung.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Startmeldung</td>
 <td><input type="text" name="TxBeginn" value="<?php echo $fsTxBeginn?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: Beginnen Sie jetzt mit dem Beantworten der #Z Fragen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardmeldung</td>
 <td><input type="text" name="TxNormal" value="<?php echo $fsTxNormal?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Frage Nummer #N</i> &nbsp; oder &nbsp; <i>Beantworten Sie die nächsten #Z Fragen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlermeldung</td>
 <td><input type="text" name="TxHandle" value="<?php echo $fsTxHandle?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Bitte beantworten Sie die Frage #N!</i> &nbsp; oder &nbsp;<i>Bitte beantworten Sie die rot markierten Fragen!</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Antworten zu den Fragen können stets in der eingegebene Reihenfolge
oder auch in einer zufälligen Antwortreihenfolge präsentiert werden.
<span class="admMini">(Achtung: Es geht hier um die <i>Antworten</i> und nicht um die Fragen!)</span></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortreihenfolge</td>
 <td><input type="radio" class="admRadio" name="ZufallsAntwort<?php if(!$fsZufallsAntwort) echo '" checked="checked'?>" value="0"> natürliche Reihenfolge &nbsp; <input type="radio" class="admRadio" name="ZufallsAntwort<?php if($fsZufallsAntwort) echo '" checked="checked'?>" value="1"> Zufallsreihenfolge</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zum Auswählen der Antworten werden standardmäßig Kontrollkästchen (Checkboxen) verwendet, damit mehrere Antworten möglich sind.
Alternativ können bei Fragen mit genau <i>einer</i> richtigen Antwort auch Radioschalter (Radiobuttons) verwendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Radioschalter</td>
 <td><input type="checkbox" class="admCheck" name="RadioButton<?php if($fsRadioButton) echo '" checked="checked'?>" value="1"> Radioschalter statt Checkboxen bei Fragen mit genau einer richtigen Lösung verwenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Wenn bei Ihren Fragen <i>stets genau eine Antwort</i> richtig ist
und nur eine Frage pro Bildschirmseite dargestellt wird
kann auf den Formularschalter zum Absenden unter dem Formular verzichtet werden
und sofort bei Klick auf eine Antwort zur nächsten Fragenseite gesprungen werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sofortbeantworten</td>
 <td><input type="radio" class="admRadio" name="SofortBeiKlick<?php if($fsSofortBeiKlick==1) echo '" checked="checked'?>" value="1"> Bei Auswahl eines Antwortkästchens sofort zur nächsten Frage<br>
 <input type="radio" class="admRadio" name="SofortBeiKlick<?php if($fsSofortBeiKlick==2) echo '" checked="checked'?>" value="2"> Antworten in Form von großen Klickschaltern zum Sofortbeantworten darbieten<br>
 <input type="radio" class="admRadio" name="SofortBeiKlick<?php if($fsSofortBeiKlick<=0) echo '" checked="checked'?>" value="0"> normales Absenden über den Formularschalter unterhalb der Antworten</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Unter den Auswahlantworten zu einer Frage kann eine Zeile mit dem Sinn &quot;<i>Frage jetzt auslassen, später beantworten</i>&quot; erscheinen.
Anstatt die Frage zu beantworten kann der Teilnehmer dann diese Zeile auswählen und somit die Frage ans Ende stellen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Auslassungen<br>verwenden</td>
 <td><div style="float:left;width:18px;padding-top:4px;"><input type="checkbox" class="admCheck" name="Auslassen<?php if($fsAuslassen) echo '" checked="checked'?>" value="1"></div>
 <div style="margin-left:20px;"><input type="text" name="TxAnsEnde" value="<?php echo $fsTxAnsEnde?>" size="90" style="width:99%;"></div>
 <div class="admMini" style="padding-left:22px;clear:both;">Textvorschlag: Frage #N jetzt [i]nicht[/i] beantworten, am Ende erneut vorlegen</div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die aktuelle Frage kann unmittelbar nach der Beantwortung auf Richtigkeit geprüft werden, völlig unabhängig von der detaillierten Auswertung am Schluß.
Fehlerhaft beantwortete Fragen werden dann dem Teilnehmer mit einem entsprechenden Fehlerhinweise sofort wieder vorgelegt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Prüfe die Antwort<br>sofort exakt</td>
 <td><div style="float:left;width:18px;padding-top:4px;"><input type="checkbox" class="admCheck" name="PruefeAntw<?php if($fsPruefeAntw) echo '" checked="checked'?>" value="1"></div>
 <div style="margin-left:20px;"><input type="text" name="TxNochMal" value="<?php echo $fsTxNochMal?>" size="90" style="width:99%;"></div>
 <div class="admMini" style="padding-left:22px;clear:both;">Textvorschlag: <i>Die Frage #N wurde falsch beantwortet. Korrigieren Sie!</i> &nbsp; oder &nbsp; <i>Die markierten Fragen wurde falsch beantwortet. Korrigieren Sie!</i></div>

 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="HilfeBemerkung<?php if($fsHilfeBemerkung) echo '" checked="checked'?>" value="1">
 bei falscher Beantwortung den Anmerkungstext-1 zur Frage unterhalb des Antwortblockes einblenden</div></td>

</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortversuche<br>bei falscher Antwort</td>
 <td>nach max. <input type="text" name="AntwortVersuche" value="<?php echo (!empty($fsAntwortVersuche)?$fsAntwortVersuche:'')?>" size="3" style="width:28px;"> Antwortversuchen zur nächsten Frage gehen
 <div class="admMini" style="margin-top:3px;"><u>Hinweis</u>: leer lassen für beliebig viele Antwortversuche zur Frage</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Prüfe nur formal<br>auf passende<br>Antwortanzahl</td>
 <td><div style="float:left;width:18px;padding-top:4px;"><input type="checkbox" class="admCheck" name="PruefeAnzahl<?php if($fsPruefeAnzahl) echo '" checked="checked'?>" value="1"></div>
 <div style="margin-left:20px;"><input type="text" name="TxHalb" value="<?php echo $fsTxHalb?>" size="90" style="width:99%;"></div>
 <div class="admMini" style="padding-left:22px;clear:both;">Textvorschlag: <i>Die Anzahl der Antworten zur Frage #N stimmt nicht.</i> &nbsp; oder &nbsp; <i>Die Anzahl der Antworten zu den markierten Fragen stimmt nicht.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die aktuelle Frage kann unmittelbar nach der Beantwortung mit der korrekten Antwort aufgedeckt werden.
Der Teilnehmer sieht dann die Frage noch einmal mit selektierten richtigen Antworten und einer Meldung, ob seine Antwort richtig oder falsch war.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Offenlegen</td>
 <td><input type="checkbox" class="admCheck" name="Offenlegen<?php if($fsOffenlegen) echo '" checked="checked'?>" value="1"> Antworten sofort aufdecken, jedoch nur <input type="checkbox" class="admCheck" name="OffenNurFalsche<?php if($fsOffenNurFalsche) echo '" checked="checked'?>" value="1"> wenn falsch beantwortet<br>
 <input type="text" name="TxStimmt" value="<?php echo $fsTxStimmt?>" size="90" style="width:99%;">
 <div class="admMini" style="margin-bottom:5px;">Textvorschlag: <i>Die Frage #N wurde richtig beantwortet.</i> &nbsp; oder &nbsp; <i>Die #Z Fragen wurden richtig beantwortet.</i></div>
 <input type="text" name="TxAnders" value="<?php echo $fsTxAnders?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Falsch, die richtige Antwort zur Frage #N hätte gelautet:</i> &nbsp; oder &nbsp; <i>Falsch, die richtigen Antworten zu den markierten Fragen hätten gelautet:</i></div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbBemerkung<?php if($fsZeigeBemerkung=='aufdecken') echo '" checked="checked'?>" value="1"><input type="hidden" name="ZeigeBemerkung" value="<?php echo $fsZeigeBemerkung?>" />
 beim Aufdecken den Anmerkungstext-1 zur Frage unterhalb des Antwortblockes einblenden</div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbBemerkung2<?php if($fsZeigeBemerkung=='selektiv') echo '" checked="checked'?>" value="1">
 beim Aufdecken Anmerkung-1 oder Anmerkung-2 je nach richtiger/falscher Beantwortung zeigen</div> </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Testfragen-Script kann in einem Lernmodus betrieben werden
und zeigt sofort und ausschließlich nur die Lösungen unter den Fragen an.
Welche Hinweis-Meldung soll im eingeschalteten Lernmodus über den Fragen stehen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Lernmodus</td>
 <td><div style="float:left;width:18px;padding-top:4px;"><input type="checkbox" class="admCheck" name="LernModus<?php if($fsLernModus) echo '" checked="checked'?>" value="1"></div>
 <div style="margin-left:20px;"><input type="text" name="TxLernen" value="<?php echo $fsTxLernen?>" size="90" style="width:99%;"></div>
 <div class="admMini" style="padding-left:22px;clear:both;">Textvorschlag: <i>Lernmodus: Die Lösung zur Frage #N lautet:</i> &nbsp; oder &nbsp; <i>Lernmodus: Die Lösungen zu den #Z Fragen lauten:</i></div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbLMBemerk<?php if($fsLernBemerkung==1) echo '" checked="checked'?>" value="1">
 im Lernmodus den Anmerkungstext-1 zur Frage unterhalb des Antwortblockes einblenden</div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbLMBemerk2<?php if($fsLernBemerkung==2) echo '" checked="checked'?>" value="1">
 im Lernmodus Anmerkung-1 und Anmerkung-2 unterhalb des Antwortblockes einblenden</div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbLMBemerk3<?php if($fsLernBemerkung==3) echo '" checked="checked'?>" value="1">
 im Lernmodus den Anmerkungstext-1 zur Frage unterhalb des Gesamtblockes einblenden</div>
 <div style="margin-top:2px;"><input type="checkbox" class="admCheck" name="CbLMBemerk4<?php if($fsLernBemerkung==4) echo '" checked="checked'?>" value="1">
 im Lernmodus Anmerkung-1 und Anmerkung-2 unterhalb des Gesamtblockes einblenden</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Testablauf kann durch ein Zeitlimit begrenzt werden.
Ist die vorgegebene Zeitspanne abgelaufen wird der Test mit der nächsten Antwort abgebrochen und zu Ende/Auswertung gegangen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitlimit</td>
 <td><input type="text" name="ZeitLimitM" value="<?php echo (!empty($fsZeitLimit)?floor($fsZeitLimit/60):'')?>" size="3" style="width:28px;">min :
 <input type="text" name="ZeitLimitS" value="<?php echo (!empty($fsZeitLimit)?sprintf('%02d',$fsZeitLimit % 60):'')?>" size="3" style="width:28px;">sec &nbsp;
 <span class="admMini">(bei unbegrenzter Zeit einfach leer lassen)</span> &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="RestZeit<?php if($fsRestZeit) echo '" checked="checked'?>" value="1"> Restzeit anzeigen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Normalerweise gibt es unten auf der Bildschirmseite nur <i>einen</i> Schalter zum Beantworten der Frage und Weitergehen.
Unter Frage und Auswahlantworten kann aber als Ausnahme eine zusätzliche Zeile mit einem weiteren Schalter zum <i>zurück-Blättern</i> und/oder eine Zeile mit einem Schalter mit der Funktion <i>Gehe zu Frage Nr. XX</i> eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sprungschalter</td>
 <td><input type="text" name="SchalterTxZurueck" value="<?php echo $fsSchalterTxZurueck?>" style="width:15em" size="20"> <span class="admMini">Empfehlung: leer lassen bei Nichtverwendung oder&nbsp; <i>eine Seite rückwärts blättern</i></span><br>
 <input type="text" name="SchalterTxGeheZu" value="<?php echo $fsSchalterTxGeheZu?>" style="width:15em" size="20"> <span class="admMini">Empfehlung: leer lassen bei Nichtverwendung oder&nbsp; <i>Gehe zu Frage Nr:</i></span><br>
 <input type="radio" class="admRadio" name="Schalter2Zeilen" value="0<?php if(!$fsSchalter2Zeilen) echo '" checked="checked'?>"> beide Schalter in einer Zeile &nbsp; <input type="radio" class="admRadio" name="Schalter2Zeilen" value="1<?php if($fsSchalter2Zeilen) echo '" checked="checked'?>"> in zwei Zeilen untereinander</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Nach Abschluss des Beantwortens aller Fragen
und nach erfolgter automatischer Bewertung kann eine ausführliche Bewertungsseite mit dem Testergebnis angezeigt werden
oder sofort zu einer ergebnisneutralen Abschlusseite gesprungen werden.
Die automatische Bewertung/Ergebnisspeicherung erfolgt unabhängig davon in jedem Fall.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungsseite</td>
 <td><input class="admRadio" type="radio" name="BewertungsSeite<?php if($fsBewertungsSeite) echo '" checked="checked'?>" value="1" /> Bewertungsseite anzeigen &nbsp;
 <input class="admRadio" type="radio" name="BewertungsSeite<?php if(!$fsBewertungsSeite) echo '" checked="checked'?>" value="0" /> ohne Ergebnisanzeige sofort zur Abschlusseite</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Nach Abschluss des Beantwortens aller Fragen
und nach erfolgter Bewertung kann eine Lösungsseite angezeigt werden, die alle Fragen und Antworten nebst Anmerkungen zu Lern- und Kontrollzwecken aufdeckt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Lösungsseite</td>
 <td><input class="admCheck" type="checkbox" name="LoesungsSeite<?php if($fsLoesungsSeite) echo '" checked="checked'?>" value="1" /> Lösungsseite() mit allen Antworten anzeigen<br>
 <input class="admCheck" type="checkbox" name="LoesungsFalsche<?php if($fsLoesungsFalsche) echo '" checked="checked'?>" value="1" /> dabei nur die falsch gelösten Fragen darbieten<br>
 <input class="admCheck" type="checkbox" name="LoesungsAnmk<?php if($fsLoesungsAnmk==1) echo '" checked="checked'?>" value="1" /> Anmerkung-1 unterhalb der Antworten pro Frage anzeigen<br>
 <input class="admCheck" type="checkbox" name="LoesungsAnm2<?php if($fsLoesungsAnmk==2) echo '" checked="checked'?>" value="2" /> Anmerkung-1 oder Anmerkung-2 je nach richtig/falsch anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Lösungstext</td>
 <td><input type="text" name="TxZeigeLoesung" value="<?php echo $fsTxZeigeLoesung?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Lösung zur Frage #N</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">
Nach allen Fragen und der eventuellen Bewertungsseite wird die Abschlusseite gezeigt.
Diese kann vom Testfragen-Script unter Verwendung der folgenden drei Texte erzeugt werden
oder aus einer selbst zu erstellenden HTML-Schablone namens <i>fraFertig.inc.htm</i> gebildet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">nach Abschluss<div style="margin-top:32px;">im Lernmodus</div></td>
 <td><div><input type="text" name="TxAllesFertig" value="<?php echo $fsTxAllesFertig?>" size="90" style="width:99%;"></div>
 <div class="admMini">Textvorschlag: Sie haben den gesamten Test mit allen #Z Fragen absolviert!</div>
 <div class="admMini">Hinweis: #Z ist ein möglicher Platzhalter für die Fragenanzahl</div>
 <div style="margin-top:4px;"><input type="text" name="TxLernOk" value="<?php echo $fsTxLernOk?>" size="90" style="width:99%;"></div>
 <div class="admMini">Textvorschlag: Lernmodus: Sie haben alle #Z Fragen und Antworten gesehen.</div>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="padding-top:28px;">Zusatztext</td>
 <td><div title="Frage eingeben und dann formatieren"><?php echo fFraBBToolbar('TxFertigText')?>
 <div><textarea name="TxFertigText" style="width:99%;height:8em;"><?php echo str_replace('\n ',"\n",$fsTxFertigText)?></textarea></div>
 </div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Neustart-Schleife</td>
 <td><div style="margin-top:4px;"><input type="text" name="TxNeuStart" value="<?php echo $fsTxNeuStart?>" size="90" style="width:99%;"></div>
 <div class="admMini">Vorschlag: <i>Test von vorn starten</i> oder <i>zum Benutzerzentrum</i> &nbsp; (bzw. leer lassen falls keine Neustart-Schleife gewünscht)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">HTML-Schablone</td>
 <td><input type="checkbox" class="admCheck" name="FertigHtml<?php if($fsFertigHtml) echo '" checked="checked'?>" value="1">
 Statt obiger Texte das HTML-Schablonenfragment <i>fraFertig.inc.htm</i> als Schlussseite verwenden.
 <div class="admMini">Hinweis: #Z ist ein Platzhalter für die Fragenanzahl</div>
 <div class="admMini">{Selbst} und {QueryString} sind Platzhalter für die Verlinkung zum Neustart des Testfragenscripts</div>
 <div class="admMini">{Urkunde} ist ein Platzhalter für einen Link zur Teilnehmerurkunde, sofern das zusätzliche Urkundenmodul verwendet wird.</div>
 </td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fFraBBToolbar($Nam){
 $sHttp='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 $X =NL.'<table class="fraTool" border="0" cellpadding="0" cellspacing="0">';
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
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Youtube',22,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Video', 24,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nam,'Audio', 26,$sHttp).'</td>';
 $X.=NL.'  <td><img class="fraTool" src="tbColor.gif" style="margin-right:0;cursor:default;" title="'.FRA_TxBB_O.'" /></td>';
 $X.=NL.'  <td>
   <select class="fraTool" name="fra_Col'.$Nam.'" onChange="fCol('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.FRA_TxBB_O.'">
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
 $X.=NL.'  <td><img class="fraTool" src="tbSize.gif" style="margin-right:0;cursor:default;" title="'.FRA_TxBB_S.'" /></td>';
 $X.=NL.'  <td>
   <select class="fraTool" name="fra_Siz'.$Nam.'" onChange="fSiz('."'".$Nam."'".',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.FRA_TxBB_S.'">
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
 return '<img class="fraTool" src="tb'.$vImg.'.gif" onClick="fFmt('."'".$Nam."'".','.$nTag.')" style="background-image:url(tool.gif);" title="'.constant('FRA_TxBB_'.substr($vImg,0,1)).'" />';
}
?>