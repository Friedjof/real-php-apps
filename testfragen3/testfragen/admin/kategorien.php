<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('Kategorien verwalten','','FFk');

if($_SERVER['REQUEST_METHOD']=='POST'){
 $sKat=(isset($_POST['kat'])?str_replace(NL,';',str_replace("\n\n",NL,str_replace("\n\n\n",NL,str_replace('"',"'",str_replace(';','`,',str_replace(' ','',stripslashes(@strip_tags(str_replace("\r",'',trim($_POST['kat'])))))))))):'');
 $fsMehrfachKat=(isset($_POST['MehrfachKat'])&&$_POST['MehrfachKat']=='1'?true:false);
 if($sKat!=FRA_Kategorien||$fsMehrfachKat!=FRA_MehrfachKat){
  $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
  foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
   if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
    $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php'))));
    fSetzFraWert($sKat,'Kategorien','"'); fSetzFraWert($fsMehrfachKat,'MehrfachKat','');
    if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
   }else{$sMeld='<p class="admFehl">Wollen Sie die Änderung wirklich für <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
  }//while
  if($sErfo) $sMeld.='<p class="admErfo">Die geänderten Kategorien wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 }else $sMeld='<p class="admMeld">Die Fragekategorien bleiben unverändert.</p>';
}else{ //GET
 $sMeld='<p class="admMeld">Kontrollieren und ändern Sie die aktuellen Fragekategorien.</p>';
 $sKat=FRA_Kategorien; $fsMehrfachKat=FRA_MehrfachKat;
}
echo $sMeld.NL;
?>

<form name="fraKategorien" action="kategorien.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td colspan="3">Die Fragen in der Fragenliste <i><?php echo (!FRA_SQL?FRA_Daten.FRA_Fragen:FRA_SqlDaBa.'.'.FRA_SqlTabF)?></i> können bei der Eingabe bestimmten Kategorien zugeordnet werden,
  die Sie hier vereinbaren können.</td>
 </tr>
 <tr class="admTabl">
  <td class="admSpa1">Kategorien zum<br>Spezifizieren der<br>Fragen sollen sein:</td>
  <td style="text-align:top;"><textarea name="kat" cols="32" rows="24" style="width:24em;height:28em;"><?php echo str_replace('`,',';',str_replace(';',NL,$sKat))?></textarea></td>
  <td style="vertical-align:top;"><p>einfach pro Zeile einen Kategorie-Begriff in der Sortierfolge eintragen, in der die Kategorien im Eingabeformular erscheinen sollen</p>
  <p>Bei Verwendung von Unterkategorien Zeilen nach dem Muster <i>Kategorie#Unterkategorie</i> erzeugen</p>
  <p style="padding-top:32px;padding-bottom:5em;">(Wenn Sie keine Kategorien verwenden wollen - Liste einfach leer lassen)</p>
  <div><input type="radio" class="admRadio" name="MehrfachKat" value="0"<?php if(!$fsMehrfachKat) echo ' checked="checked"'?> /> Frage soll maximal <i>einer</i> Kategorie angehören</div>
  <div><input type="radio" class="admRadio" name="MehrfachKat" value="1"<?php if($fsMehrfachKat) echo ' checked="checked"'?> /> Frage kann auch <i>mehreren</i> Kategorien zugehören</div></td>
 </tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p><u>Hinweise</u>:</p>
<ul>
<li>Sie müssen in Ihrer Fragendatenbasis <i>nicht</i> zwangsläufig mit Kategorien arbeiten.</li>
<li>Kategorien können auch später angelegt/geändert werden unabhängig von den bisher eingegebenen Fragen.</li>
<li>Die Kategoriebezeichnungen sollten möglichst kurz sein und aus einem Wort bestehen.</li>
<li>Unterkategorien sind möglich und werden einfach nach einem #-Zeichen hinter die übergeordnete Kategorie geschrieben. Jede Zeile muss dabei erneut mit der Hauptkategorie beginnen. Auf die Reihenfolge der Unterkategoriezeilen ist selbst zu achten, eine automatische Sortierung erfolgt nicht.</li>
<li>Beim Löschen einer Kategorie bleiben alle Fragen unverändert erhalten, die dieser Kategorie zugeordnet waren.</li>
</ul>

<?php echo fSeitenFuss();?>