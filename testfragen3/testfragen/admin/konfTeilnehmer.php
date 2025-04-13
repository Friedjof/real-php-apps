<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Teilnehmerregistrierung einstellen',"<script type=\"text/javascript\">
function registerWenn(s){
 document.getElementById('RegWenn').style.display=(s=='nachher'?'table-row':'none');
 document.getElementById('zentrum').style.color=(s!='vorher'?'#999999':'#000000');
 return false;
}
</script>",'KTn');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $aFelder=explode(';',';'.FRA_TeilnehmerFelder); $aPflicht=explode(';',';'.FRA_TeilnehmerPflicht); $nFelder=count($aFelder);
 for($i=1;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
 $fsRegistrierung=FRA_Registrierung; $fsMaxSessionZeit=FRA_MaxSessionZeit; $fsNachRegisterWohin=FRA_NachRegisterWohin;
 $fsRegistGrenze=FRA_RegistGrenze; $fsRegistWenn=FRA_RegistWenn; $fsTxRegistNicht=FRA_TxRegistNicht;
 $fsTxVorVorErfassen=FRA_TxVorVorErfassen; $fsTxNachVorErfassen=FRA_TxNachVorErfassen; $fsTxVorNachErfassen=FRA_TxVorNachErfassen;
 $fsTxLoginErfassen=FRA_TxLoginErfassen; $fsNutzerzwang=FRA_Nutzerzwang;
 $fsTeilnehmerSperre=FRA_TeilnehmerSperre; $fsTeilnehmerMitCode=FRA_TeilnehmerMitCode; $fsTxTeilnehmerSperre=FRA_TxTeilnehmerSperre;
 $fsTeilnehmerStandardtest=FRA_TeilnehmerStandardtest; $fsTeilnehmerSpontaneFolge=FRA_TeilnehmerSpontaneFolge; $fsTeilnehmerAlleFolgen=FRA_TeilnehmerAlleFolgen;
 $fsTeilnehmerDrucken=FRA_TeilnehmerDrucken; $fsTeilnehmerKennfeld=FRA_TeilnehmerKennfeld;
 $fsTeilnehmerDSE1=FRA_TeilnehmerDSE1; $fsTeilnehmerDSE2=FRA_TeilnehmerDSE2;
 $fsCaptcha=FRA_Captcha;
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $nFeldAnzahl=max((int)txtVar('FeldAnzahl'),1); $nFelder=substr_count(FRA_TeilnehmerFelder,';')+2;
  $aFelder=array(''); $sFelder=''; $aPflicht=array(0); $sPflicht='';
  for($i=1;$i<=$nFeldAnzahl;$i++){
   $aFelder[$i]=txtVar('F'.$i); $sFelder.=';'.str_replace(';','`,',$aFelder[$i]);
   $aPflicht[$i]=(!empty($aFelder[$i])?(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0):0); $sPflicht.=';'.(!empty($aFelder[$i])?$aPflicht[$i]:'');
  }
  if(fSetzFraWert(substr($sFelder,1),'TeilnehmerFelder','"')){$bNeu=true; $bFelderNeu=true;}else $bFelderNeu=false;
  if(fSetzFraWert(substr($sPflicht,1),'TeilnehmerPflicht','"')) $bNeu=true;
  $s=(int)txtVar('Nutzerzwang'); if(fSetzFraWert(($s?true:false),'Nutzerzwang','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerSperre'); if(fSetzFraWert(($s?true:false),'TeilnehmerSperre','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerStandardtest'); if(fSetzFraWert(($s?true:false),'TeilnehmerStandardtest','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerSpontaneFolge'); if(fSetzFraWert(($s?true:false),'TeilnehmerSpontaneFolge','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerAlleFolgen'); if(fSetzFraWert(($s?true:false),'TeilnehmerAlleFolgen','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerDrucken'); if(fSetzFraWert(($s?true:false),'TeilnehmerDrucken','')) $bNeu=true;
  $s=(int)txtVar('TeilnehmerKennfeld'); if(fSetzFraWert($s,'TeilnehmerKennfeld','')) $bNeu=true;
  $s=txtVar('TxTeilnehmerSperre'); if(fSetzFraWert($s,'TxTeilnehmerSperre',"'")) $bNeu=true;
  $s=txtVar('Registrierung'); if($s==''&&!$fsNutzerzwang) $s=FRA_Nutzerverwaltung; if($fsNutzerzwang&&FRA_Nutzerverwaltung>'') $s=''; if(fSetzFraWert($s,'Registrierung',"'")) $bNeu=true;
  if($fsRegistrierung>''&&FRA_Nutzerverwaltung>'') if(fSetzFraWert($fsRegistrierung,'Nutzerverwaltung',"'")) $bNeu=true; //angleichen
  $s=txtVar('NachRegisterWohin'); if($s=='Zentrum'&&$fsRegistrierung!='vorher') $s='Fragen'; if(fSetzFraWert($s,'NachRegisterWohin',"'")) $bNeu=true;
  $s=(int)txtVar('TeilnehmerMitCode'); if(fSetzFraWert(($s?true:false),'TeilnehmerMitCode','')) $bNeu=true;
  $s=min(max((int)txtVar('MaxSessionZeit'),60),300); if(fSetzFraWert($s,'MaxSessionZeit','')) $bNeu=true;
  $v=txtVar('RegistWenn'); if(fSetzFraWert($v,'RegistWenn',"'")) $bNeu=true;
  $v=max((int)txtVar('RegistGrenze'),1); if(fSetzFraWert($v,'RegistGrenze','')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxRegistNicht')))); if(fSetzFraWert($s,'TxRegistNicht',"'")) $bNeu=true;
  $s=txtVar('TxVorVorErfassen'); if(fSetzFraWert($s,'TxVorVorErfassen','"')) $bNeu=true;
  $s=txtVar('TxNachVorErfassen'); if(fSetzFraWert($s,'TxNachVorErfassen','"')) $bNeu=true;
  $s=txtVar('TxVorNachErfassen'); if(fSetzFraWert($s,'TxVorNachErfassen','"')) $bNeu=true;
  $s=txtVar('TxLoginErfassen'); if(fSetzFraWert($s,'TxLoginErfassen','"')) $bNeu=true;
  $v=txtVar('TeilnehmerDSE1'); if(fSetzFraWert(($v?true:false),'TeilnehmerDSE1','')) $bNeu=true;
  $v=txtVar('TeilnehmerDSE2'); if(fSetzFraWert(($v?true:false),'TeilnehmerDSE2','')) $bNeu=true;
  $v=(int)txtVar('Captcha'); if(fSetzFraWert(($v?true:false),'Captcha','')) $bNeu=true;
  if($bNeu){
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    $nFelder=$nFeldAnzahl+1;
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Teilnehmer-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Teilnehmer-Einstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$sMeld){
 $sMeld.='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Teilnehmerregistrierung.</p>';
 if(empty($fsRegistrierung)) $sMeld.='<p class="admFehl">Die Teilnehmerregistrierung ist momentan nicht eingeschaltet.</p>';
}
echo $sMeld.NL;
?>

<form name="TlnForm" action="konfTeilnehmer.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Testfragen-Script kann mit einer Teilnehmerregistrierung und/oder einer Benutzerverwaltung gekoppelt sein.
 Besucher können den Test entweder als <i>unangemeldete Gäste</i> oder als für die Dauer eines Tests <i>registrierte Teilnehmer</i>
 oder als längerfristig <i>angemeldetete Benutzer</i> absolvieren.<br />
 Auf dieser Seite wird <i>nur</i> das Verhalten bezüglich der Teilnehmerregistrierung eingestellt.
 Die alternative <a href="konfNutzer.php<?php if(KONF>0)echo'?konf='.KONF?>">Benutzerverwaltung</a> ist momentan <u><?php echo ($fsNutzerzwang?'ohne':'mit')?></u> Teilnehmerregistrierung <u><?php echo (FRA_Nutzerverwaltung?'ein':'aus')?></u>geschaltet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmersperre</td>
 <td><input type="radio" class="admRadio" name="TeilnehmerSperre" value="0"<?php if(!$fsTeilnehmerSperre) echo' checked="checked"'?> /> Testdurchführung für Teilnehmer möglich&nbsp;
  <input type="radio" class="admRadio" name="TeilnehmerSperre" value="1"<?php if($fsTeilnehmerSperre) echo' checked="checked"'?> /> Testdurchführung für Teilnehmer gesperrt
  <div><input type="text" name="TxTeilnehmerSperre" value="<?php echo $fsTxTeilnehmerSperre?>" style="width:99%;" /></div>
  <div class="admMini">Muster: <i>Der Zugang für Teilnehmer ist momentan gesperrt.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmererlaubnis</td>
 <td><input type="radio" class="admRadio" name="Nutzerzwang" value="0"<?php if(!$fsNutzerzwang) echo' checked="checked"'?> /> Testdurchführung auch für Gäste/Teilnehmer &nbsp;
 <input type="radio" class="admRadio" name="Nutzerzwang" value="1"<?php if($fsNutzerzwang) echo' checked="checked"'?> /> Testdurchführung nur für angemeldete Benutzer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerregistrierung</td>
 <td>
  <input type="radio" class="admRadio" name="Registrierung" value=""<?php if(!$fsRegistrierung) echo' checked="checked"'?> onclick="registerWenn(this.value)" /> anonym ohne Registrierung &nbsp;
  <input type="radio" class="admRadio" name="Registrierung" value="vorher"<?php if($fsRegistrierung=='vorher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> vor dem Test &nbsp;
  <input type="radio" class="admRadio" name="Registrierung" value="nachher"<?php if($fsRegistrierung=='nachher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> nach den Testfragen</td>
</tr>
<tr class="admTabl" id="RegWenn" style="display:<?php echo($fsRegistrierung=='nachher'?'table-row':'none')?>">
 <td class="admSpa1">bedingte<br>Teilnehmerregistrierung<div class="admMini">(falls Registrierung<br>nach dem Test)</div>
 <div style="margin-top:7.4em">Abweise-Meldung</div><div class="admMini">(Registrierung)</div></td>
 <td><input type="radio" class="admRadio" name="RegistWenn" value="PProz<?php if($fsRegistWenn=='PProz') echo'" checked="checked'?>" /> nur Registrierung, wenn mindestens <?php echo $fsRegistGrenze?>% der Punkte erreicht wurden (Positivwertung)<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="PPkte<?php if($fsRegistWenn=='PPkte') echo'" checked="checked'?>" /> nur Registrierung, wenn mindestens <?php echo $fsRegistGrenze?> Punkte erreicht wurden (Positivwertung)<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="NProz<?php if($fsRegistWenn=='NProz') echo'" checked="checked'?>" /> nur Registrierung, wenn höchstens <?php echo $fsRegistGrenze?>% der Fehlerpunkte zustande kamen (Negativwertung)<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="NPkte<?php if($fsRegistWenn=='NPkte') echo'" checked="checked'?>" /> nur Registrierung, wenn höchstens <?php echo $fsRegistGrenze?> Fehlerpunkte zustande kamen (Negativwertung)<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="PRtig<?php if($fsRegistWenn=='PRtig') echo'" checked="checked'?>" /> nur Registrierung, wenn mindestens <?php echo $fsRegistGrenze?> richtige Antworten erreicht wurden<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="PFehl<?php if($fsRegistWenn=='PFehl') echo'" checked="checked'?>" /> nur Registrierung, wenn höchstens <?php echo $fsRegistGrenze?> falsche Antworten zustande kamen<br>
  <input type="radio" class="admRadio" name="RegistWenn" value="<?php if(empty($fsRegistWenn)) echo'" checked="checked'?>" /> unabhängig vom Ergebnis immer registrieren
  <div><input type="text" name="RegistGrenze" value="<?php echo $fsRegistGrenze?>" size="3" style="width:3em" /> Grenzwert in <i>Prozent</i> oder in <i>Punkten</i> oder in <i>Antworten</i></div>
  <textarea name="TxRegistNicht" rows="4" style="width:99%;height:4.5em;"><?php echo str_replace('\n ',"\n",$fsTxRegistNicht)?></textarea>
  <div class="admMini"><u>Muster</u>: Sie haben weniger als <?php echo $fsRegistGrenze?> Punkte erreicht. Ihr Versuch wird nicht gewertet.</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenbestätigung</td>
 <td>
 <input type="radio" class="admRadio" name="NachRegisterWohin" value="Daten"<?php if($fsNachRegisterWohin=='Daten') echo' checked="checked"'?> /> nach der Erfassung der Teilnehmerdaten diese noch einmal zur Bestätigung anzeigen<br />
 <input type="radio" class="admRadio" name="NachRegisterWohin" value="Fragen"<?php if($fsNachRegisterWohin=='Fragen') echo' checked="checked"'?> /> nach Teilnehmerdatenerfassung sofort weiter zu den Fragen bzw. zur Auswertung
 <div id="zentrum<?php if($fsRegistrierung!='vorher') echo'" style="color:#999999'?>"><input type="radio" class="admRadio" name="NachRegisterWohin" value="Zentrum"<?php if($fsNachRegisterWohin=='Zentrum') echo' checked="checked"'?> /> nach der Teilnehmerdatenerfassung erst einmal zur Testauswahlliste</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmercode</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerMitCode" value="1"<?php if($fsTeilnehmerMitCode) echo' checked="checked"'?> /> Testdurchführung für Teilnehmer nur nach Eingabe eines 4-stelligen Aktiv-Codes
 <div class="admMini"><u>Empfehlung</u>: nicht einschalten, nur in seltenen Situationen sinnvoll</div>
 <div class="admMini"><u>Erklärung</u>:Beispielsweise könnten mehrere Gruppen/Klassen parallel unterschiedliche Tests bearbeiten ohne die Gefahr, dass einzelnen Teilnehmer den falschen Test starten, da jeder Gruppe/Klasse nur der Aktiv-Code bekanntgegeben wird, der für ihren Test aktuell gültig ist.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">max. Sitzungszeit</td>
 <td><input type="text" name="MaxSessionZeit" value="<?php echo $fsMaxSessionZeit?>" size="2" /> 60...300 Minuten &nbsp; <span class="admMini">(nur falls Teilnehmerverwaltung/Benutzerverwaltung aktiv)</span></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern zur Testauswahlliste für Teilnehmer geleitet wird kann diese Liste anbieten:</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardtest</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerStandardtest" value="1"<?php if($fsTeilnehmerStandardtest) echo' checked="checked"'?> /> Teilnehmern soll der Standardtestablauf angeboten werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Spontanfolge</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerSpontaneFolge" value="1"<?php if($fsTeilnehmerSpontaneFolge) echo' checked="checked"'?> /> die momentane <i>spontane Fragenfolge</i> aus der <i>Testfragenauswahl</i> anbieten</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenfolgen</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerAlleFolgen" value="1"<?php if($fsTeilnehmerAlleFolgen) echo' checked="checked"'?> /> Liste der <i>gespeicherten Fragenfolgen</i> aus der <i>Testfragenauswahl</i> anbieten</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Druckliste</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerDrucken" value="1"<?php if($fsTeilnehmerDrucken) echo' checked="checked"'?> /> Liste der Fragen und Antworten in der Druckversion anbieten</td>
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
 <input type="radio" class="admRadio" name="TeilnehmerKennfeld" value="<?php echo $i; if($fsTeilnehmerKennfeld==$i) echo'" checked="checked'?>" /></td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="2" class="admSpa2">Im Zusammenhang mit der Teilnehmererfassung werden folgende Meldungen im Testablauf verwendet:</td></tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmererfassung vor den Testfragen stattfindet</td>
 <td valign="top"><input type="text" name="TxVorVorErfassen" value="<?php echo $fsTxVorVorErfassen?>" style="width:99%;" /><div class="admMini">Muster: <i>Vor Beginn des Testes müssen Sie sich registrieren.</i></div>
 <input type="text" name="TxNachVorErfassen" value="<?php echo $fsTxNachVorErfassen?>" style="width:99%;" /><div class="admMini">Muster: <i>Ihre Daten wurden erfasst. Beginnen Sie nun mit dem Beantworten der Fragen.</i></div></td>
</tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmererfassung erst nach den Testfragen erfolgt</td>
 <td valign="top"><input type="text" name="TxVorNachErfassen" value="<?php echo $fsTxVorNachErfassen?>" style="width:99%;" /><div class="admMini">Muster: <i>Sie haben alle # Fragen abgearbeitet. Tragen Sie Ihre Daten ein.</i></div></td>
</tr>
<tr class="admTabl">
 <td valign="top">falls eine Teilnehmerregistrierung zusätzlich im Login-Formular der Benutzeranmeldung angeboten wird</td>
 <td valign="top"><input type="text" name="TxLoginErfassen" value="<?php echo $fsTxLoginErfassen?>" style="width:99%;" /><div class="admMini">Muster: <i>Registrierung nur für diesen Testdurchlauf.</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="TeilnehmerDSE1" value="1"<?php if($fsTeilnehmerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="TeilnehmerDSE2" value="1"<?php if($fsTeilnehmerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Registrierung/Anmeldung von Benutzern und Versand vergessener Passworte über ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td>Captcha</td>
 <td><input type="checkbox" class="admCheck" name="Captcha" value="1"<?php if($fsCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>