<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Konfigurationen verwalten','','UKf');

$nKonfNr=''; $nLschNr=''; $sKommentar='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $nNr=(isset($_POST['Lsch'])?$_POST['Lsch']:'');
 if(strlen($nNr)>0){//Loeschen
  $nNr=(int)$nNr;
  if($nNr!=KONF||$nNr==0){
   if((isset($_POST['LschNr'])?(int)$_POST['LschNr']:0)==$nNr){
    if(unlink(UMF_Pfad.'umfWerte'.$nNr.'.php')) $sMeld='<p class="admErfo">Die Konfiguration-'.$nNr.' wurde gelöscht.</p>';
    else $sMeld='<p class="admFehl">Die Konfigurationsdatei '.$nNr.' konnte nicht gelöscht werden.</p>';
   }else{$sMeld='<p class="admFehl">Die Konfiguration Nr. '.$nNr.' wirklich löschen?</p>'; $nLschNr=$nNr;}
  }else $sMeld='<p class="admFehl">Die momentan aktivierte Konfiguration kann nicht gelöscht werden.</p>';
 }
 if($nNr=(isset($_POST['Konf'])?(int)$_POST['Konf']:0)){//Anlegen
  $sKommentar=(isset($_POST['Kommentar'])?str_replace("'",'',str_replace('"','',stripslashes(trim($_POST['Kommentar'])))):'');
  if((isset($_POST['KonfNr'])?$_POST['KonfNr']:'')==$nNr||!file_exists(UMF_Pfad.'umfWerte'.$nNr.'.php')){
   $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',rtrim(implode('',file(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php')))));
   if(!$p=strpos($t,"define('UMF_Konfiguration'")){
    if(!$p=strpos($t,"define('UMF_Www'")) $p=strpos($t,"define('UMF_Version'"); if($p>0) $t=substr_replace($t," \n",$p,0);
   }
   $t=substr_replace($t,"define('UMF_Konfiguration','".$sKommentar."');\n",$p,strpos($t,"\n",$p)+1-$p)."\n";
   if($f=fopen(UMF_Pfad.'umfWerte'.$nNr.'.php','w')){
    fwrite($f,$t); fclose($f); $nKonfNr=''; $sKommentar='';
    $sMeld='<p class="admErfo">Die aktuelle Konfiguration wurde als <i>umfWerte'.$nNr.'.php</i> gespeichert.</p>';
   }else $sMeld='<p class="admFehl">Die Konfigurationsdatei <i>umfWerte'.$nNr.'.php</i> konnte nicht geschrieben werden.</p>';
  }else{$sMeld='<p class="admFehl">Soll die vorhandene Konfiguration-'.$nNr.' überschrieben werden?</p>'; $nKonfNr=$nNr;}
 }
 if(empty($sMeld)) $sMeld='<p class="admMeld">Die Konfigurationen bleiben unverändert.</p>';
}

//Ausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Bearbeiten Sie die vorhandenen Konfigurationen.</p>';
echo $sMeld.NL;
?>

<p class="admMeld" style="margin-top:10px;">neue Konfiguration anlegen</p>
<form name="umfEingabe" action="konfKonf.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="KonfNr" value="<?php echo $nKonfNr?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Sie können die aktuelle Konfiguration<?php if(KONF>0) echo '-'.KONF?> mit
allen eingestellten Parametern und Werten in einer anderen Konfigurationsdatei abspeichern und diese danach gegebenfalls modfizieren.
Umfrageveranstaltungen mit alternativen Konfigurationen können über die Adresse
wie beispielsweise <i>http://<?php echo UMF_Www?>umfrage.php?umf_Ablauf=<?php echo (KONF+1)?></i> aufgerufen werden. <a href="<?php echo ADU_Hilfe?>LiesMich.htm#5B" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td class="admSpa1">neue Konfiguration</td>
 <td>Die aktuelle umfWerte<?php echo (KONF>0?KONF:'')?>.php jetzt als
 <i>umfWerte</i><input type="text" name="Konf" value="<?php echo $nKonfNr?>" size="2" maxlen="2" style="width:22px;"><i>.php</i> abspeichern &nbsp;
 <span class="admMini">(<u>Hinweis</u>: Nr. 1...99 möglich)</span>
 <div class="admMini"><u>Achtung</u>: Eine eventuell vorhandene Konfigurationsdatei mit gleicher Nummer wird dabei überschrieben!</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kommentar zur<br>neuen Konfiguration</td>
 <td style="vertical-align:middle;"><input type="text" name="Kommentar" value="<?php echo $sKommentar?>" size="90" style="width:98%;"></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" name="Btn" value="Speichern"></p>
</form>

<p class="admMeld" style="margin-top:20px;">vorhandene Konfiguration löschen</p>

<?php
$aF=array(); $aK=array();
if($h=opendir(UMF_Pfad)){
 while($s=readdir($h)) if(substr($s,0,8)=='umfWerte'&&strpos($s,'.php')>0) $aF[]=(int)substr($s,8);
 closedir($h); sort($aF); reset($aF); if($aF[0]==0) $aF[0]='';
 foreach($aF as $n){
  $t=implode('',file(UMF_Pfad.'umfWerte'.$n.'.php'));
  if($p=strpos($t,'UMF_Konfiguration')){$t=substr($t,$p+20,250); $aK[]=substr($t,0,strpos($t,"'"));}else $aK[]='';
 }
}
$nAnzahl=count($aK);
?>

<form name="umfLoeschen" action="konfKonf.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="LschNr" value="<?php echo $nLschNr?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Sie können zusätzliche Konfigurationen wieder löschen, falls Sie diese nicht länger benötigen.</td></tr>
<tr class="admTabl">
 <td align="center" width="1%"><input type="image" src="iconLoeschen.gif" width="12" height="13" border="0" alt="L&ouml;schen" title="L&ouml;schen"></td>
 <td width="10%"><b>Konfiguration</b></td>
 <td><b>Kommentar</b></td>
</tr>
<tr class="admTabl">
 <td align="center"><b>*</b></td>
 <td>umfWerte.php</td>
 <td><?php echo($aF[0]==''?$aK[0]:'<span style="color:#AA0033;">Datei fehlt!</span>')?></td>
</tr>
<?php for($i=1;$i<$nAnzahl;$i++){?>
<tr class="admTabl">
 <td><input class="admRadio" type="radio" name="Lsch" value="<?php echo $aF[$i]; if($aF[$i]==$nLschNr) echo '" checked="checked'?>" title="<?php echo $aF[$i]?> Löschen" /></td>
 <td>umfWerte<?php echo $aF[$i]?>.php</td>
 <td><?php echo($aK[$i]?$aK[$i]:'&nbsp;')?></td>
</tr>
<?php }?>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" name="Btn" value="L&ouml;schen"></p>
</form>

<?php echo fSeitenFuss();?>