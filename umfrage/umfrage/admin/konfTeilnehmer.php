<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Teilnehmerregistrierung einstellen',"<script type=\"text/javascript\">
function registerWenn(s){
 document.getElementById('RegWenn').style.display=(s=='nachher'?'table-row':'none');
 document.getElementById('ausWahl').style.color=(s!='vorher'?'#999999':'#000000');
 return false;
}
</script>",'KTn');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $aFelder=explode(';',';'.UMF_TeilnehmerFelder); $aPflicht=explode(';',';'.UMF_TeilnehmerPflicht); $nFelder=count($aFelder);
 for($i=1;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
 $usRegistrierung=UMF_Registrierung; $usMaxSessionZeit=UMF_MaxSessionZeit; $usNachRegisterWohin=UMF_NachRegisterWohin;
 $usTeilnehmerLog=UMF_TeilnehmerLog; $usTxRegistNicht=UMF_TxRegistNicht;
 $usTxVorVorErfassen=UMF_TxVorVorErfassen; $usTxNachVorErfassen=UMF_TxNachVorErfassen; $usTxVorNachErfassen=UMF_TxVorNachErfassen;
 $usTxLoginErfassen=UMF_TxLoginErfassen; $usNutzerzwang=UMF_Nutzerzwang; $usGrafikOhneLogin=UMF_GrafikOhneLogin;
 $usTeilnehmerSperre=UMF_TeilnehmerSperre; $usTeilnehmerMitCode=UMF_TeilnehmerMitCode; $usTxTeilnehmerSperre=UMF_TxTeilnehmerSperre;
 $usTeilnehmerNormUmfrage=UMF_TeilnehmerNormUmfrage;
 $usTeilnehmerDrucken=UMF_TeilnehmerDrucken; $usTeilnehmerKennfeld=UMF_TeilnehmerKennfeld;
 $usTeilnehmerDSE1=UMF_TeilnehmerDSE1; $usTeilnehmerDSE2=UMF_TeilnehmerDSE2;
 $usCaptcha=UMF_Captcha;
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $nFeldAnzahl=max((int)txtVar('FeldAnzahl'),1); $nFelder=substr_count(UMF_TeilnehmerFelder,';')+2;
  $aFelder=array(''); $sFelder=''; $aPflicht=array(0); $sPflicht='';
  for($i=1;$i<=$nFeldAnzahl;$i++){
   $aFelder[$i]=txtVar('F'.$i); $sFelder.=';'.str_replace(';','`,',$aFelder[$i]);
   $aPflicht[$i]=(!empty($aFelder[$i])?(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0):0); $sPflicht.=';'.(!empty($aFelder[$i])?$aPflicht[$i]:'');
  }
  if(fSetzUmfWert(substr($sFelder,1),'TeilnehmerFelder','"')){$bNeu=true; $bFelderNeu=true;}else $bFelderNeu=false;
  if(fSetzUmfWert(substr($sPflicht,1),'TeilnehmerPflicht','"')) $bNeu=true;
  $s=(int)txtVar('Nutzerzwang'); if(fSetzUmfWert(($s?true:false),'Nutzerzwang','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerSperre'); if(fSetzUmfWert(($s?true:false),'TeilnehmerSperre','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerNormUmfrage'); if(fSetzUmfWert(($s?true:false),'TeilnehmerNormUmfrage','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerDrucken'); if(fSetzUmfWert(($s?true:false),'TeilnehmerDrucken','')) $bNeu=true; if($s&&strlen(UMF_TxDrucken)==0) fSetzUmfWert('Drucken','TxDrucken',"'");
  $s=(int)txtVar('TeilnehmerKennfeld'); if(fSetzUmfWert($s,'TeilnehmerKennfeld','')) $bNeu=true;
  $s=txtVar('TxTeilnehmerSperre'); if(fSetzUmfWert($s,'TxTeilnehmerSperre',"'")) $bNeu=true;
  $s=txtVar('Registrierung'); if($s==''&&!$usNutzerzwang) $s=UMF_Nutzerverwaltung; if($usNutzerzwang&&UMF_Nutzerverwaltung>'') $s=''; if(fSetzUmfWert($s,'Registrierung',"'")) $bNeu=true;
  if($usRegistrierung>''&&UMF_Nutzerverwaltung>'') if(fSetzUmfWert($usRegistrierung,'Nutzerverwaltung',"'")) $bNeu=true; //angleichen
  $s=txtVar('NachRegisterWohin'); if($s=='Auswahl'&&$usRegistrierung!='vorher') $s='Fragen'; if(fSetzUmfWert($s,'NachRegisterWohin',"'")) $bNeu=true;
  $s=(int)txtVar('TeilnehmerMitCode'); if(fSetzUmfWert(($s?true:false),'TeilnehmerMitCode','')) $bNeu=true;
  $s=min(max((int)txtVar('MaxSessionZeit'),60),300); if(fSetzUmfWert($s,'MaxSessionZeit','')) $bNeu=true;
  $v=(int)txtVar('TeilnehmerLog'); if(fSetzUmfWert(($v?true:false),'TeilnehmerLog','')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxRegistNicht')))); if(fSetzUmfWert($s,'TxRegistNicht',"'")) $bNeu=true;
  $s=txtVar('TxVorVorErfassen'); if(fSetzUmfWert($s,'TxVorVorErfassen','"')) $bNeu=true;
  $s=txtVar('TxNachVorErfassen'); if(fSetzUmfWert($s,'TxNachVorErfassen','"')) $bNeu=true;
  $s=txtVar('TxVorNachErfassen'); if(fSetzUmfWert($s,'TxVorNachErfassen','"')) $bNeu=true;
  $s=txtVar('TxLoginErfassen'); if(fSetzUmfWert($s,'TxLoginErfassen','"')) $bNeu=true;
  $s=(int)txtVar('GrafikOhneLogin'); if(fSetzUmfWert(($s?true:false),'GrafikOhneLogin','')) $bNeu=true;
  $v=txtVar('TeilnehmerDSE1'); if(fSetzUmfWert(($v?true:false),'TeilnehmerDSE1','')) $bNeu=true;
  $v=txtVar('TeilnehmerDSE2'); if(fSetzUmfWert(($v?true:false),'TeilnehmerDSE2','')) $bNeu=true;
  $v=(int)txtVar('Captcha'); if(fSetzUmfWert(($v?true:false),'Captcha','')) $bNeu=true;
  if($bNeu){
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    $nFelder=$nFeldAnzahl+1;
   }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> durfte nicht geschrieben werden (Rechteproblem)!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Teilnehmer-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Teilnehmer-Einstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$sMeld){
 $sMeld.='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Teilnehmerregistrierung.</p>';
 if(empty($usRegistrierung)) $sMeld.='<p class="admFehl">Die Teilnehmerregistrierung ist momentan nicht eingeschaltet.</p>';
}
echo $sMeld.NL;
?>

<form name="TlnForm" action="konfTeilnehmer.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Umfrage-Script kann mit einer Teilnehmerregistrierung und/oder einer Benutzerverwaltung gekoppelt sein.
 Besucher können die Umfrage entweder als <i>unangemeldete Gäste</i> oder als für die Dauer einer Umfrage <i>registrierte Teilnehmer</i>
 oder als längerfristig <i>angemeldetete Benutzer</i> absolvieren.<br />
 Auf dieser Seite wird <i>nur</i> das Verhalten bezüglich der Teilnehmerregistrierung eingestellt.
 Die alternative <a href="konfNutzer.php<?php if(KONF>0)echo'?konf='.KONF?>">Benutzerverwaltung</a> ist momentan <u><?php echo ($usNutzerzwang?'ohne':'mit')?></u> Teilnehmerregistrierung <u><?php echo (UMF_Nutzerverwaltung?'ein':'aus')?></u>geschaltet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmersperre</td>
 <td><input type="radio" class="admRadio" name="TeilnehmerSperre" value="0"<?php if(!$usTeilnehmerSperre) echo' checked="checked"'?> /> Umfrage für Teilnehmer möglich &nbsp;
  <input type="radio" class="admRadio" name="TeilnehmerSperre" value="1"<?php if($usTeilnehmerSperre) echo' checked="checked"'?> /> Umfrage für Teilnehmer gesperrt
  <div><input type="text" name="TxTeilnehmerSperre" value="<?php echo $usTxTeilnehmerSperre?>" style="width:98%;" /></div>
  <div class="admMini">Muster: <i>Der Zugang für Teilnehmer ist momentan gesperrt.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmererlaubnis</td>
 <td><input type="radio" class="admRadio" name="Nutzerzwang" value="0"<?php if(!$usNutzerzwang) echo' checked="checked"'?> /> Umfrage auch für Gäste/Teilnehmer &nbsp;
 <input type="radio" class="admRadio" name="Nutzerzwang" value="1"<?php if($usNutzerzwang) echo' checked="checked"'?> /> Umfrage nur für angemeldete Benutzer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerregistrierung</td>
 <td>
  <input type="radio" class="admRadio" name="Registrierung" value=""<?php if(!$usRegistrierung) echo' checked="checked"'?> onclick="registerWenn(this.value)" /> anonym ohne Registrierung &nbsp;
  <input type="radio" class="admRadio" name="Registrierung" value="vorher"<?php if($usRegistrierung=='vorher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> vor der Umfrage &nbsp;
  <input type="radio" class="admRadio" name="Registrierung" value="nachher"<?php if($usRegistrierung=='nachher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> nach der Umfrage</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenbestätigung</td>
 <td>
 <input type="radio" class="admRadio" name="NachRegisterWohin" value="Daten"<?php if($usNachRegisterWohin=='Daten') echo' checked="checked"'?> /> nach der Erfassung der Teilnehmerdaten diese noch einmal zur Bestätigung anzeigen<br />
 <input type="radio" class="admRadio" name="NachRegisterWohin" value="Fragen"<?php if($usNachRegisterWohin=='Fragen') echo' checked="checked"'?> /> nach Teilnehmerdatenerfassung sofort weiter zu den Fragen bzw. zur Auswertung
 <div id="ausWahl<?php if($usRegistrierung!='vorher') echo'" style="color:#999999'?>"><input type="radio" class="admRadio" name="NachRegisterWohin" value="Auswahl"<?php if($usNachRegisterWohin=='Auswahl') echo' checked="checked"'?> /> nach der Teilnehmerdatenerfassung erst einmal zur Umfrageauswahlliste</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Auswertegrafik</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="GrafikOhneLogin" value="1"<?php if($usGrafikOhneLogin) echo' checked="checked"'?> /> Auswertegrafik immer ohne Login / vorbei am Login abrufbar</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmercode</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerMitCode" value="1"<?php if($usTeilnehmerMitCode) echo' checked="checked"'?> /> Umfrage für Teilnehmer nur nach Eingabe eines 4-stelligen Aktiv-Codes
 <div class="admMini"><u>Empfehlung</u>: nicht einschalten, nur in seltenen Situationen sinnvoll</div>
 <div class="admMini"><u>Erklärung</u>: Beispielsweise könnten mehrere Gruppen/Klassen parallel unterschiedliche Umfragen bearbeiten ohne die Gefahr, dass einzelnen Teilnehmer die falsche Umfrage starten, da jeder Gruppe/Klasse nur der Aktiv-Code bekanntgegeben wird, der für ihre Umfrage aktuell gültig ist.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">max. Sitzungszeit</td>
 <td><input type="text" name="MaxSessionZeit" value="<?php echo $usMaxSessionZeit?>" size="2" /> 60...300 Minuten &nbsp; <span class="admMini">(nur falls Teilnehmerverwaltung/Benutzerverwaltung aktiv)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Nach der Abstimmung werden die Antworten in jedem Fall in der Ergebnisliste anonym aufsummiert.
Darüberhinaus können die Daten in einer zusätzlichen Teilnahmeliste aus Datum/Uhrzeit, Personendaten und Antwortfolge abgelegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnahmeliste</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerLog" value="1"<?php if($usTeilnehmerLog) echo '" checked="checked'?>" /> Abstimmung von Teilnehmern aufzeichnen</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern zur Umfrageauswahlliste für Teilnehmer geleitet wird kann diese Liste anbieten:</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardumfrage</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerNormUmfrage" value="1"<?php if($usTeilnehmerNormUmfrage) echo' checked="checked"'?> /> Teilnehmern soll die Standardumfrage angeboten werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Drucken erlauben</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerDrucken" value="1"<?php if($usTeilnehmerDrucken) echo' checked="checked"'?> /> Liste der Fragen und Antworten in der Druckversion anbieten</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">In der Teilnehmerregistrierung können folgenden Informationen erfasst werden.</td></tr>
<tr class="admTabl">
 <td>Datenfeldanzahl</td>
 <td><input type="text" name="FeldAnzahl" value="<?php echo $nFelder-1?>" size="1" /> maximale Anzahl der Datenfelder bei der Teilnehmererfassung&nbsp; <span class="admMini">(Empfehlung: 2 ... max. 5)</span></td>
</tr>

<tr class="admTabl"><td class="admSpa1"><b>Datenfeld</b></td><td><b>Bezeichnung&nbsp;/&nbsp;Pflichtfeld / Kennfeld</b></td></tr>

<?php for($i=1;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1"><?php echo $i?>. Feld</td>
 <td><input type="text" name="F<?php echo $i?>" value="<?php echo $aFelder[$i]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P<?php echo $i?>" value="1"<?php if($aPflicht[$i]) echo' checked="checked"'?> /> <span style="width:55px">&nbsp;</span>
 <input type="radio" class="admRadio" name="TeilnehmerKennfeld" value="<?php echo $i; if($usTeilnehmerKennfeld==$i) echo'" checked="checked'?>" /></td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="2" class="admSpa2">Im Zusammenhang mit der Teilnehmererfassung werden folgende Meldungen im Umfrageablauf verwendet:</td></tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmererfassung vor der Umfrage stattfindet</td>
 <td valign="top"><input type="text" name="TxVorVorErfassen" value="<?php echo $usTxVorVorErfassen?>" style="width:98%;" /><div class="admMini">Muster: <i>Vor Beginn der Umfrage müssen Sie sich registrieren.</i></div>
 <input type="text" name="TxNachVorErfassen" value="<?php echo $usTxNachVorErfassen?>" style="width:98%;" /><div class="admMini">Muster: <i>Ihre Daten wurden forgendermaßen erfasst.</i></div></td>
</tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmererfassung erst nach der Umfrage erfolgt</td>
 <td valign="top"><input type="text" name="TxVorNachErfassen" value="<?php echo $usTxVorNachErfassen?>" style="width:98%;" /><div class="admMini">Muster: <i>Sie haben alle # Fragen abgearbeitet. Tragen Sie Ihre Daten ein.</i></div></td>
</tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmerregistrierung zusätzlich im Login-Formular der Benutzeranmeldung angeboten wird</td>
 <td valign="top"><input type="text" name="TxLoginErfassen" value="<?php echo $usTxLoginErfassen?>" style="width:98%;" /><div class="admMini">Muster: <i>Registrierung nur für diesen einen Umfragedurchlauf.</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="TeilnehmerDSE1" value="1"<?php if($usTeilnehmerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="TeilnehmerDSE2" value="1"<?php if($usTeilnehmerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Registrierung/Anmeldung von Benutzern und Versand vergessener Passworte über ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td>Captcha</td>
 <td><input type="checkbox" class="admCheck" name="Captcha" value="1"<?php if($usCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>