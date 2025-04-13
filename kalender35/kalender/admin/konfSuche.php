<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Suchformular anpassen','','KSf');

if($_SERVER['REQUEST_METHOD']=='GET'){
 $nFelder=count($kal_FeldName); $ksTxSuchMeld=KAL_TxSuchMeld; $ksSuchArchiv=KAL_SuchArchiv; $ksSuchIntervall=KAL_SuchIntervall;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $nFelder=count($kal_FeldName);
 $kal_SuchFeld=array('0'); for($i=1;$i<$nFelder;$i++) $kal_SuchFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
 if(fSetzArray($kal_SuchFeld,'SuchFeld','')) $bNeu=true;
 $s=txtVar('TxSuchMeld'); if(fSetzKalWert($s,'TxSuchMeld',"'")) $bNeu=true;
 $s=(int)txtVar('SuchArchiv'); if(fSetzKalWert(($s?true:false),'SuchArchiv','')) $bNeu=true;
 $s=(int)txtVar('SuchIntervall'); if(fSetzKalWert(($s?true:false),'SuchIntervall','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   if(!$Msg) $Msg='<p class="admErfo">Die Einstellungen für das Suchformular wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
 }else if(!$Msg) $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Terminsuchformular.</p>';
echo $Msg.NL;
?>

<form action="konfSuche.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Suchformular für Termine wird Besuchern folgende Aufforderungsmeldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderung</td>
 <td><input type="text" name="TxSuchMeld" value="<?php echo $ksTxSuchMeld?>" style="width:100%" /><div class="admMini">Empfehlung: <i>Stellen Sie Ihre Suchanfrage zusammen!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular für Termine im Besucherbereich kann bezüglich der Suchfelder konfiguriert werden.
Welche Felder der aktuellen Terminstruktur sollen wie oft <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.9.mal" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> im Suchformular erscheinen?</td></tr>

<?php include('feldtypenInc.php'); for($i=1;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php $t=$kal_FeldType[$i]; echo $kal_FeldName[$i].'<div class="admMini">Typ <i>'.$aTyp[$t].'</i></div>'?></td>
 <td>
  <div style="margin-bottom:3px;"><input type="radio" class="admRadio" name="F<?php echo $i?>" value="0"<?php if($kal_SuchFeld[$i]==0) echo ' checked="checked"'?> />Feld im Suchformular nicht verwenden</div>
<?php if($t!='z'&&$t!='p'&&$t!='c'&&$t!='j'&&$t!='v'){?>
  <input type="radio" class="admRadio" name="F<?php echo $i?>" value="1"<?php if($kal_SuchFeld[$i]==1) echo ' checked="checked"'?> />einmal als <i>ist</i> bzw. <i>wie</i> &nbsp;
  <input type="radio" class="admRadio" name="F<?php echo $i?>" value="2"<?php if($kal_SuchFeld[$i]==2) echo ' checked="checked"'?> />zweimal mit <i>oder wie</i> bzw. <i>bis</i> &nbsp;
<?php if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='u'||$t=='e'||$t=='l'||$t=='b'||$t=='f'||$t=='o'||$t=='x'){?>
  <input type="radio" class="admRadio" name="F<?php echo $i?>" value="3"<?php if($kal_SuchFeld[$i]==3) echo ' checked="checked"'?> />dreimal mit <i>aber nicht wie</i> anzeigen
<?php }}else if($t=='j'||$t=='v'){?>
  <input type="radio" class="admRadio" name="F<?php echo $i?>" value="1"<?php if($kal_SuchFeld[$i]==1) echo ' checked="checked"'?> />einspaltig <i>Ja</i> und <i>Nein</i>&nbsp; &nbsp;
  <input type="radio" class="admRadio" name="F<?php echo $i?>" value="2"<?php if($kal_SuchFeld[$i]==2) echo ' checked="checked"'?> />zweispaltig <i>Ja</i> und <i>Nein</i>
<?php }?>
 </td>
</tr>

<?php }?>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular kann zusätzlich eine Auswahlmöglichkeit für die Suche im Terminarchiv (abgelaufene und ausgebelendete, aber noch nicht gelöschte Termine) enthalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Archivsuche</td>
 <td><input type="radio" class="admRadio" name="SuchArchiv" value="1"<?php if($ksSuchArchiv) echo' checked="checked"'?>" />einblenden &nbsp; &nbsp; <input type="radio" class="admRadio" name="SuchArchiv" value="0"<?php if(!$ksSuchArchiv) echo' checked="checked"'?>" />ausblenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular kann zusätzlich einen Intervallfilter ähnlich dem über der Terminliste enthalten. Das derart eingestellte Intervall wird wirksam, solange vom Besucher kein anderes Suchdatum in das Formular eingegeben wird.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Suchintervall</td>
 <td><input type="radio" class="admRadio" name="SuchIntervall" value="1"<?php if($ksSuchIntervall) echo' checked="checked"'?>" />einblenden &nbsp; &nbsp; <input type="radio" class="admRadio" name="SuchIntervall" value="0"<?php if(!$ksSuchIntervall) echo' checked="checked"'?>" />ausblenden</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss()?>