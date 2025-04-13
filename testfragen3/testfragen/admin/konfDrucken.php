<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Druckfunktionen einstellen','','KDr');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $fsDrucken=FRA_Drucken; $fsDruckGast=FRA_DruckGast; $fsTxDrucken=FRA_TxDrucken; $fsDruckRueckw=FRA_DruckRueckw; $fsDruckZufallsAw=FRA_DruckZufallsAw; $fsDruckBildW=FRA_DruckBildW;
 $fsDruckSchablone=FRA_DruckSchablone; $aDruckSuche=explode(';',FRA_DruckSuche); $aDruckSpalten=explode(';',FRA_DruckSpalten);
 $fsTxDruckFilter=FRA_TxDruckFilter; $fsTxDruckSpalten=FRA_TxDruckSpalten; $fsDruckSuchSpalten=FRA_DruckSuchSpalten;
 $fsTxDruckGanzeListe=FRA_TxDruckGanzeListe; $fsTxDruckFilterListe=FRA_TxDruckFilterListe;
 $nDruckNummer=$aDruckSpalten[0];
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $s=txtVar('Drucken'); if(fSetzFraWert($s,'Drucken',"'")) $bNeu=true;
  $s=txtVar('DruckSchablone'); if(fSetzFraWert($s,'DruckSchablone',"'")) $bNeu=true;
  $s=max((int)txtVar('DruckBildW'),0); if(fSetzFraWert($s,'DruckBildW','')) $bNeu=true;
  $s=(int)txtVar('DruckGast'); if(fSetzFraWert(($s?true:false),'DruckGast','')) $bNeu=true;
  $s=max(min((int)txtVar('DruckSuchSpalten'),3),1); if(fSetzFraWert($s,'DruckSuchSpalten','')) $bNeu=true;
  $s=txtVar('TxDrucken'); if(fSetzFraWert($s,'TxDrucken',"'")) $bNeu=true;
  $s=txtVar('TxDruckFilter'); if(fSetzFraWert($s,'TxDruckFilter',"'")) $bNeu=true;
  $s=txtVar('TxDruckSpalten'); if(fSetzFraWert($s,'TxDruckSpalten',"'")) $bNeu=true;
  for($i=0;$i<9;$i++){$aDruckSuche[$i]=(int)txtVar('DruckSuche'.$i); $aDruckSpalten[$i]=(int)txtVar('DruckSpalte'.$i);}
  $nDruckNummer=max((int)txtVar('DruckNummer'),1); if($aDruckSpalten[0]) $aDruckSpalten[0]=$nDruckNummer; else $nDruckNummer=0;
  $s=implode(';',$aDruckSuche); if(fSetzFraWert($s,'DruckSuche',"'")) $bNeu=true;
  $s=implode(';',$aDruckSpalten); if(fSetzFraWert($s,'DruckSpalten',"'")) $bNeu=true;
  $s=(int)txtVar('DruckRueckw'); if(fSetzFraWert(($s?true:false),'DruckRueckw','')) $bNeu=true;
  $s=(int)txtVar('DruckZufallsAw'); if(fSetzFraWert(($s?true:false),'DruckZufallsAw','')) $bNeu=true;
  $s=txtVar('TxDruckGanzeListe'); if(fSetzFraWert($s,'TxDruckGanzeListe',"'")) $bNeu=true;
  $s=txtVar('TxDruckFilterListe'); if(fSetzFraWert($s,'TxDruckFilterListe',"'")) $bNeu=true;
  if($bNeu){
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Druck-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Druck-Einstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$sMeld){
 $sMeld.='<p class="admMeld">Kontrollieren und ändern Sie die Einstellungen für die Druckfunktion.</p>';
 if(empty($fsDrucken)) $sMeld.='<p class="admFehl">Die Druckfunktion für Besucher ist momentan ausgeschaltet.</p>';
}
echo $sMeld.NL;
?>

<form action="konfDrucken.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Drucken kann für angemeldete Besucher und auch für Gäste erlaubt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Link zur Druckseite</td>
 <td>
  <input type="text" name="Drucken" value="<?php echo $fsDrucken?>" style="width:10em;" /> <span class="admMini">Empfehlung: <i>Drucken</i> oder <i>Fragen drucken</i></span> &nbsp; &nbsp;
  <input type="checkbox" class="admCheck" name="DruckGast" value="1"<?php if($fsDruckGast) echo' checked="checked"'?> /> auch für Gäste
  <div class="admMini">Leer lassen, wenn Druckfunktion nicht vorhanden sein soll.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Vor dem eigentlichen Drucken kann in einem Formular die Druckausgabe angepasst werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formularüberschrift</td>
 <td><input type="text" name="TxDrucken" value="<?php echo $fsTxDrucken?>" style="width:99%;" /> <div class="admMini">Empfehlung: <i>Stellen Sie Ihre Druckliste zusammen!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zwischenüberschrift-1</td>
 <td><input type="text" name="TxDruckFilter" value="<?php echo $fsTxDruckFilter?>" style="width:99%;" /> <div class="admMini">Empfehlung: <i>Auswahl der zu druckenden Fragen anhand folgender Filterbedingungen:</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">angezeigte <br>Filterfelder</td>
 <td>
  <table class="admTabl" border="0" cellpadding="3" cellspacing="1" style="width:auto;">
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche0" value="1"<?php if($aDruckSuche[0]) echo' checked="checked"'?> /></td><td>Nr.</td><td style="width:9em;">(empfohlen)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche1" value="1"<?php if($aDruckSuche[1]) echo' checked="checked"'?> /></td><td>Kategorie</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche2" value="1"<?php if($aDruckSuche[2]) echo' checked="checked"'?> /></td><td>Frage</td><td>(empfohlen)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche3" value="1"<?php if($aDruckSuche[3]) echo' checked="checked"'?> /></td><td>Lösung</td><td>(nicht sinnvoll)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche4" value="1"<?php if($aDruckSuche[4]) echo' checked="checked"'?> /></td><td>Punkte</td><td>(nicht sinnvoll)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche5" value="1"<?php if($aDruckSuche[5]) echo' checked="checked"'?> /></td><td>Bild</td><td>(nicht sinnvoll)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche6" value="1"<?php if($aDruckSuche[6]) echo' checked="checked"'?> /></td><td>Antwort</td><td>(empfohlen)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche7" value="1"<?php if($aDruckSuche[7]) echo' checked="checked"'?> /></td><td>Anmerkung-1</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSuche8" value="1"<?php if($aDruckSuche[8]) echo' checked="checked"'?> /></td><td>Anmerkung-2</td><td>&nbsp;</td></tr>
  </table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anzahl Filterfelder</td>
 <td>
  <input class="admRadio" type="radio" name="DruckSuchSpalten" value="1"<?php if($fsDruckSuchSpalten==1) echo' checked="checked"'?> /> 1-spaltig (nur: wie) &nbsp;
  <input class="admRadio" type="radio" name="DruckSuchSpalten" value="2"<?php if($fsDruckSuchSpalten==2) echo' checked="checked"'?> /> 2-spaltig (wie <i>oder</i> wie) &nbsp;
  <input class="admRadio" type="radio" name="DruckSuchSpalten" value="3"<?php if($fsDruckSuchSpalten==3) echo' checked="checked"'?> /> 3-spaltig (wie <i>oder</i> wie <i>aber nicht</i> wie)
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zwischenüberschrift-2</td>
 <td><input type="text" name="TxDruckSpalten" value="<?php echo $fsTxDruckSpalten?>" style="width:99%;" /> <div class="admMini">Empfehlung: <i>In der Druckliste sollen folgende Spalten erscheinen:</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">angebotene <br>Druckfelder</td>
 <td>
  <table class="admTabl" border="0" cellpadding="3" cellspacing="1" style="width:100%;">
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte0" value="1"<?php if($aDruckSpalten[0]) echo' checked="checked"'?> /></td><td>Nr.</td><td>(empfohlen) &nbsp; &nbsp; <input class="admCheck" type="radio" name="DruckNummer" value="1"<?php if($nDruckNummer==1) echo' checked="checked"'?> /> Original-Nummern &nbsp; <input class="admCheck" type="radio" name="DruckNummer" value="2"<?php if($nDruckNummer==2) echo' checked="checked"'?> /> chronologische Nummerierung</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte1" value="1"<?php if($aDruckSpalten[1]) echo' checked="checked"'?> /></td><td>Kategorie</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte2" value="1"<?php if($aDruckSpalten[2]) echo' checked="checked"'?> /></td><td>Frage</td><td>(empfohlen)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte3" value="1"<?php if($aDruckSpalten[3]) echo' checked="checked"'?> /></td><td>Lösung</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte4" value="1"<?php if($aDruckSpalten[4]) echo' checked="checked"'?> /></td><td>Punkte</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte5" value="1"<?php if($aDruckSpalten[5]) echo' checked="checked"'?> /></td><td>Bild</td><td>maximale Druckbreite des Bildes oder leer lassen falls in Originalgröße <div><input type="text" name="DruckBildW" value="<?php echo($fsDruckBildW>0?$fsDruckBildW:'')?>" style="width:5em;"></input> px</div></td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte6" value="1"<?php if($aDruckSpalten[6]) echo' checked="checked"'?> /></td><td>Antwort</td><td>(empfohlen)</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte7" value="1"<?php if($aDruckSpalten[7]) echo' checked="checked"'?> /></td><td>Anmerkung-1</td><td>&nbsp;</td></tr>
   <tr class="admTabl"><td><input class="admCheck" type="checkbox" name="DruckSpalte8" value="1"<?php if($aDruckSpalten[8]) echo' checked="checked"'?> /></td><td>Anmerkung-2</td><td>&nbsp;</td></tr>
  </table>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die eigentliche Druckliste mit den Fragen soll wie folgt erscheinen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druck-Schablone<br>(für Besucher)</td>
 <td>
 <select name="DruckSchablone"><option value="">bitte wählen</option><option value="fraSeite.htm<?php if($fsDruckSchablone=='fraSeite.htm') echo '" selected="selected'?>">fraSeite.htm</option><option value="fraDrucken.htm<?php if($fsDruckSchablone=='fraDrucken.htm') echo '" selected="selected'?>">fraDrucken.htm</option></select>
 Schablone <i>fraSeite.htm</i> oder <i>fraDrucken.htm</i> um die Druckausgabe hüllen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Drucklistensortierung</td>
 <td>
  <input class="admRadio" type="radio" name="DruckRueckw" value="0"<?php if(!$fsDruckRueckw) echo' checked="checked"'?> /> vorwärts &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="DruckRueckw" value="1"<?php if($fsDruckRueckw) echo' checked="checked"'?> /> rückwärts
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortsortierung</td>
 <td>
  <input class="admRadio" type="radio" name="DruckZufallsAw" value="0"<?php if(!$fsDruckZufallsAw) echo' checked="checked"'?> /> natürliche Reihenfolge &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="DruckZufallsAw" value="1"<?php if($fsDruckZufallsAw) echo' checked="checked"'?> /> zufällige Reihenfolge
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift über der<br>kompletten Druckliste</td>
 <td><input type="text" name="TxDruckGanzeListe" value="<?php echo $fsTxDruckGanzeListe?>" style="width:99%;" /> <div class="admMini">Empfehlung: <i>Gesamt-Fragenliste</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift über der<br>gefilterten Druckliste</td>
 <td><input type="text" name="TxDruckFilterListe" value="<?php echo $fsTxDruckFilterListe?>" style="width:99%;" /> <div class="admMini">Empfehlung: <i>Auszug aus der Fragenliste</i></div></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>