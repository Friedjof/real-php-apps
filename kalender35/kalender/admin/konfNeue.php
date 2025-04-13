<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('neue Termine anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','PnT');

$nFelder=count($kal_FeldName); $bNeu=false; $ksNeueXTarget=''; $bEintragszeit=false;
if($a=array_keys($kal_FeldType,'@')) if(count($a)>0) for($i=0;$i<count($a);$i++) if($kal_FeldName[$a[$i]]!='ZUSAGE_BIS') $bEintragszeit=true;
if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET
 $ksNeueAnzahl=KAL_NeueAnzahl; $ksNeueFrist=KAL_NeueFrist; $ksNeueNachId=KAL_NeueNachId; $ksNeueKommend=KAL_NeueKommend;
 $ksNeueIndex=KAL_NeueIndex; $ksNeueRueckw=KAL_NeueRueckw; $ksNeueEnde=KAL_NeueEnde; $ksNeueZeit=KAL_NeueZeit;
 $ksNeueKopf=KAL_NeueKopf; $ksNeueKpfWdh=KAL_NeueKpfWdh; $ksNeueAbstand=KAL_NeueAbstand;
 $ksNeueMitWochentag=KAL_NeueMitWochentag; $ksNeueJahrhundert=KAL_NeueJahrhundert;
 $ksNeueEigeneZeilen=KAL_NeueEigeneZeilen;
 $ksNeueLink=KAL_NeueLink; $ksNeueTarget=KAL_NeueTarget; $ksNeuePopup=KAL_NeuePopup;
 if($ksNeueTarget!='kalender'&&$ksNeueTarget!='_self'&&$ksNeueTarget!='_parent'&&$ksNeueTarget!='_top'&&$ksNeueTarget!='_blank') $ksNeueXTarget=$ksNeueTarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php'))));
 $v=min(max((int)txtVar('NeueAnzahl'),1),999); if(fSetzKalWert($v,'NeueAnzahl','')) $bNeu=true;
 $v=min(max((int)txtVar('NeueFrist'),0),2999); if(fSetzKalWert($v,'NeueFrist','')) $bNeu=true;
 $v=(int)txtVar('NeueNachId'); if(!$bEintragszeit) $v=true; if(fSetzKalWert(($v?true:false),'NeueNachId','')) $bNeu=true;
 $v=(int)txtVar('NeueKommend'); if(fSetzKalWert(($v?true:false),'NeueKommend','')) $bNeu=true;
 $v=(int)txtVar('NeueIndex');if(fSetzKalWert($v,'NeueIndex','')) $bNeu=true;
 $v=(int)txtVar('NeueRueckw'); if(fSetzKalWert(($v?true:false),'NeueRueckw','')) $bNeu=true;
 $v=(int)txtVar('NeueEnde'); if(fSetzKalWert(($v?true:false),'NeueEnde','')) $bNeu=true;
 $v=(int)txtVar('NeueZeit'); if(fSetzKalWert(($v?true:false),'NeueZeit','')) $bNeu=true;
 $v=(int)txtVar('NeueKopf'); if(fSetzKalWert(($v?true:false),'NeueKopf','')) $bNeu=true;
 $v=(int)txtVar('NeueKpfWdh'); if(fSetzKalWert(($v?true:false),'NeueKpfWdh','')) $bNeu=true;
 $v=max((int)txtVar('NeueAbstand'),0); if(fSetzKalWert($v,'NeueAbstand','')) $bNeu=true;
 $v=(int)txtVar('NeueMitWochentag'); if(fSetzKalWert($v,'NeueMitWochentag','')) $bNeu=true;
 $v=txtVar('NeueJahrhundert'); if(fSetzKalWert(($v?true:false),'NeueJahrhundert','')) $bNeu=true;
 $v=txtVar('NeueEigeneZeilen'); if(fSetzKalWert(($v?true:false),'NeueEigeneZeilen','')) $bNeu=true;
 $kal_NeueFeld=array(); $kal_NeueLink=array(); $kal_NeueStil=array();
 for($i=0;$i<$nFelder;$i++){
  $kal_NeueFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_NeueLink[$i]=(isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0);
  $kal_NeueStil[$i]=(isset($_POST['Z'.$i])?str_replace("'",'"',stripslashes($_POST['Z'.$i])):'');
 }
 asort($kal_NeueFeld); reset($kal_NeueFeld);
 $j=0; foreach($kal_NeueFeld as $k=>$v) if($v>0) if($k>0) $kal_NeueFeld[$k]=++$j;
 if(fSetzArray($kal_NeueFeld,'NeueFeld','')) $bNeu=true;
 if(fSetzArray($kal_NeueLink,'NeueLink','')) $bNeu=true;
 if(fSetzArray($kal_NeueStil,'NeueStil',"'")) $bNeu=true;
 $v=txtVar('NeueLink'); if(fSetzKalWert($v,'NeueLink','"')) $bNeu=true;
 if($v=txtVar('NeueTarget')) $ksNeueXTarget=''; else{$v=txtVar('NeueXTarget'); $ksNeueXTarget=$v;}  if(fSetzKalWert($v,'NeueTarget','"')) $bNeu=true;
 $v=txtVar('NeuePopup'); if(fSetzKalWert(($v?true:false),'NeuePopup','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die geänderten Einstellungen für neue Termine wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST

if(file_exists(KALPFAD.'kalStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sPageH=fLiesHGFarb('body.kalNeue');
  $sTListW=fLiesScreenW('neuen Termine mit Spalten');
  $sTNeueR=fLiesRahmFarb('div.kalTbNLst'); $sTNeueA=fLiesRahmArt('div.kalTbNLst');
  $sNeuFS=fLiesFontS('div.kalTabN'); $sNeuW=fLiesWeite('div.kalTabN');
  $sZNeu1F=fLiesFarbe('div.kalTbNZl1'); $sZNeu1H=fLiesHGFarb('div.kalTbNZl1');
  $sZNeu2F=fLiesFarbe('div.kalTbNZl2'); $sZNeu2H=fLiesHGFarb('div.kalTbNZl2');
  $sZNeuKF=fLiesFarbe('div.kalTbNZl0'); $sZNeuKH=fLiesHGFarb('div.kalTbNZl0');
  $sANeuL=fLiesFarbe('a.kalNeue:link'); $sANeuA=fLiesFarbe('a.kalNeue:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sPageH=fTxtCol('PageH'); if(fSetzHGFarb($sPageH,'body.kalNeue')) $bNeu=true;
  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'neuen Termine mit Spalten')) $bNeu=true;
  $sTNeueR=fTxtCol('TNeueR'); if($sTNeueR!=fLiesRahmFarb('div.kalTbNLst')){
   if(fSetzRahmFarb($sTNeueR,'div.kalTabN')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.kalTbNZl0')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.kalTbNZl1')) $bNeu=true;
   if(fSetzRahmFarbB($sTNeueR,'div.kalTbNZl2')) $bNeu=true;
   if(fSetzRahmFarb($sTNeueR,'div.kalTbNLst')) $bNeu=true;
   if(fSetzRahmFarb($sTNeueR,'div.kalTbNLst',2)) $bNeu=true;
  }
  $sTNeueA=$_POST['TNeueA']; if($sTNeueA!=fLiesRahmArt('div.kalTbNLst')){
   if(fSetzeRahmArtB($sTNeueA,'div.kalTbNZl0')) $bNeu=true;
   if(fSetzeRahmArtB($sTNeueA,'div.kalTbNZl1')) $bNeu=true;
   if(fSetzeRahmArtB($sTNeueA,'div.kalTbNZl2')) $bNeu=true;
   if(fSetzeRahmArt($sTNeueA,'div.kalTbNLst')) $bNeu=true;
   if(fSetzeRahmArt($sTNeueA,'div.kalTbNLst',2)) $bNeu=true;
  }
  $sNeuW= fTxtSiz('NeuW');  if(fSetzeWeite($sNeuW,'div.kalTabN')) $bNeu=true;
  $sNeuFS=fTxtSiz('NeuFS'); if(fSetzeFontS($sNeuFS,'div.kalTabN')) $bNeu=true;
  $sZNeu1F=fTxtCol('ZNeu1F'); if(fSetzeFarbe($sZNeu1F,'div.kalTbNZl1')) $bNeu=true;
  $sZNeu1H=fTxtCol('ZNeu1H'); if(fSetzHGFarb($sZNeu1H,'div.kalTbNZl1')) $bNeu=true;
  $sZNeu2F=fTxtCol('ZNeu2F'); if(fSetzeFarbe($sZNeu2F,'div.kalTbNZl2')) $bNeu=true;
  $sZNeu2H=fTxtCol('ZNeu2H'); if(fSetzHGFarb($sZNeu2H,'div.kalTbNZl2')) $bNeu=true;
  $sZNeuKF=fTxtCol('ZNeuKF'); if(fSetzeFarbe($sZNeuKF,'div.kalTbNZl0')) $bNeu=true;
  $sZNeuKH=fTxtCol('ZNeuKH'); if(fSetzHGFarb($sZNeuKH,'div.kalTbNZl0')) $bNeu=true;
  $sANeuL=fTxtCol('ANeuL'); if(fSetzeFarbe($sANeuL,'a.kalNeue:link')) $bNeu=true;
  $sANeuA=fTxtCol('ANeuA'); if(fSetzeFarbe($sANeuA,'a.kalNeue:hover')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(KALPFAD.'kalStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Msg='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Msg='<p class="admFehl">In die Datei <i>kalStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Msg) $Msg='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
 }//POST
}else $Msg.='<p class="admFehl">Setup-Fehler: Die Datei <i>kalStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die wesentlichsten Funktions- sowie Farb- und Layouteinstellungen.</p>';
echo $Msg.NL; $sSortOpt='';
for($i=0;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i];
 if($t!='z'&&$t!='v'&&$t!='l'&&$t!='e'&&$t!='b'&&$t!='x'&&$t!='f'&&$t!='c'&&$t!='p') $sSortOpt.='<option value="'.$i.'"'.($ksNeueIndex!=$i||$i==1?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
}
$sIcon=$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfNeue.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Als neue Termine können einige der zuletzt eingetragenen Termine abgebildet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Auswahl der<br>neuen Termine</td>
 <td colspan="2"> <input class="admRadio" type="radio" name="NeueNachId" value="1"<?php if($ksNeueNachId) echo' checked="checked"'?>> anhand der lfd. Terminnummer (<i>empfohlen</i>) &nbsp;
 <input class="admRadio" type="radio" name="NeueNachId" value="0"<?php if(!$ksNeueNachId) echo' checked="checked"'?>> anhand der Eintragszeit
 <div class="admMini">Hinweis: <?php echo($bEintragszeit?'Sofern':'Da')?> kein Feld vom Typ <i>Eintragszeit</i> vorhanden ist wird die lfd. Nr verwendet.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">neue Anzahl</td>
 <td colspan="2"><input type="text" name="NeueAnzahl" value="<?php echo $ksNeueAnzahl?>" style="width:3em;" /> Termine <span class="admMini">(Empfehlung: ca. 1...10)</span>
 <div class="admMini">Maximalzahl der neuen Termine die dargestellt werden sollen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="3">Filterkriterien</td>
 <td colspan="2">nur Termine verwenden, die innerhalb der letzen <input type="text" name="NeueFrist" value="<?php echo ($ksNeueFrist>0?$ksNeueFrist:'')?>" style="width:3em;" size="3" /> Tage eingetragen wurden
 <div class="admMini"><u>Empfehlung</u>: leer lassen für <i>unbegrenzt</i></div>
 <div class="admMini"><u>Hinweis</u>: <?php echo($bEintragszeit?'Sofern':'Da')?> kein Feld vom Typ <i>Eintragszeit</i> vorhanden ist wird dieser Parameter nicht ausgewertet und nur nach obiger Anzahl entschieden.</div></td>
</tr>
<tr class="admTabl">
 <td colspan="2">
 <input class="admRadio" type="radio" name="NeueKommend" value="1"<?php if($ksNeueKommend) echo' checked="checked"'?>> nur kommende Termine &nbsp;
 <input class="admRadio" type="radio" name="NeueKommend" value="0"<?php if(!$ksNeueKommend) echo' checked="checked"'?>> auch laufende/vergangene Termine</td>
</tr>
<tr class="admTabl">
 <td colspan="2">
 <input class="admRadio" type="radio" name="NeueEnde" value="0"<?php if(!$ksNeueEnde) echo' checked="checked"'?>> nur Terminbeginn auswerten &nbsp;
 <input class="admRadio" type="radio" name="NeueEnde" value="1"<?php if($ksNeueEnde) echo' checked="checked"'?>> auch laufende Termine (evt. vorhandenes Ende berücksichtigen)
 <div><input class="admRadio" type="radio" name="NeueZeit" value="0"<?php if(!$ksNeueZeit) echo' checked="checked"'?>> nur nach Datum entscheiden &nbsp;
 <input class="admRadio" type="radio" name="NeueZeit" value="1"<?php if($ksNeueZeit) echo' checked="checked"'?>> auch Uhrzeiten berücksichtigen</div>
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfeld</td>
 <td colspan="2" width="80%"><select name="NeueIndex"><option value="1">Standard</option><?php echo $sSortOpt;?></select> Empfehlung: Standard (nach Datum)<br>
 <input class="admRadio" type="radio" name="NeueRueckw" value="0"<?php if(!$ksNeueRueckw) echo' checked="checked"'?>> vorwärts sortiert &nbsp;
 <input class="admRadio" type="radio" name="NeueRueckw" value="1"<?php if($ksNeueRueckw) echo' checked="checked"'?>> rückwärts sortiert</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Das Erscheinungsbild der neuen Termine kann individuell eingestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile</td>
 <td colspan="2"><input class="admRadio" type="checkbox" name="NeueKopf" value="1"<?php if($ksNeueKopf) echo' checked="checked"'?>> über den neuen Terminen soll ein Kopf angezeigt werden</br >
 <input class="admRadio" type="checkbox" name="NeueKpfWdh" value="1"<?php if($ksNeueKpfWdh) echo' checked="checked"'?>> die Kopfzeile soll über jedem einzelnen neuen Termin wiederholend erscheinen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminabstand</td>
 <td colspan="2"><input type="text" name="NeueAbstand" value="<?php echo $ksNeueAbstand?>" style="width:24px;" /> Pixel vertikaler Abstand zwischen den einzelnen neuen Terminen &nbsp; <span class="admMini">(Empfehlung: <i>0</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td colspan="2"><select name="NeueMitWochentag" size="1"><option value="0">Datum ohne Wochentage</option><option value="1"<?php if($ksNeueMitWochentag==1) echo ' selected="selected"'?>>Wochentag vor dem Datum</option><option value="2"<?php if($ksNeueMitWochentag==2) echo ' selected="selected"'?>>Wochentag nach dem Datum</option></select> &nbsp; &nbsp;
 Jahreszahl <input class="admRadio" type="radio" name="NeueJahrhundert" value="1"<?php if($ksNeueJahrhundert) echo' checked="checked"'?> /> 4-stellig oder <input class="admRadio" type="radio" name="NeueJahrhundert" value="0"<?php if(!$ksNeueJahrhundert) echo' checked="checked"'?> /> 2-stellig</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die neuen Termine werden standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt.
Abweichend davon kann jeder Termindatensatz in einem individuellen Layout dargestellt werden, das aus der Layoutschablone <i>neueZeile.htm</i> und gegebenfalls <i>neueKopf.htm</i> stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#4.2.eigen" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout</td>
 <td colspan="2"><input class="admRadio" type="radio" name="NeueEigeneZeilen" value="0"<?php if(!$ksNeueEigeneZeilen) echo' checked="checked"'?>> tabellarisches Standardlayout &nbsp;
 <input class="admRadio" type="radio" name="NeueEigeneZeilen" value="1"<?php if($ksNeueEigeneZeilen) echo' checked="checked"'?>> individuelles Layout aus den Layoutschablonen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die neuen Termine können bezüglich der anzuzeigenden Felder konfiguriert werden.
Welche Felder sollen in den neuen Terminen wie erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Feld-Nr.</td>
 <td>Anzeigespalte im neuen Termin</td>
 <td>optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.6.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<?php
 include('feldtypenInc.php');
 $sOpt='<option value="0">---</option>'; for($i=1;$i<$nFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i];
  if(!$k=$kal_NeueFeld[$i]) $sO=$sOpt; else $sO=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if($i!=1){if($t=='v') $sO='<option value="0">---</option>';} //versteckt
  else $sO=substr($sO,strpos($sO,'<option',1)); //Datum
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo sprintf('%02d',$i).') '.$kal_FeldName[$i].'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div>'?></td>
 <td>
<?php if($t!='c'&&$t!='u'&&$t!='p'){?>
 <select name="F<?php echo $i?>" size="1" style="width:42px;"><?php echo $sO?></select> &nbsp; &nbsp;
<?php if($t!='f'&&$t!='l'&&$t!='e'&&$t!='v'){?>
 <input type="checkbox" class="admCheck" name="L<?php echo $i?>" value="1"<?php if($kal_NeueLink[$i]) echo ' checked="checked"'?> /> als&nbsp;Detaillink
<?php }?>
 </td>
 <td><input type="text" name="Z<?php echo $i?>" style="width:200px;" value="<?php echo $kal_NeueStil[$i]?>" />
<?php }else{?>
 &nbsp;----<input type="hidden" name="F<?php echo $i?>" value="0" />
 </td>
 <td>&nbsp;
<?php }?>
 </td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Bei einem Klick auf einen Link in den neuen Terminen
soll sich das Kalenderscript öffnen und die Termindetails anzeigen. In welchem Zielfenster (Target) soll das passieren?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zielfenster</td>
 <td colspan="2"><select name="NeueTarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($ksNeueTarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($ksNeueTarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($ksNeueTarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($ksNeueTarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="kalender"<?php if($ksNeueTarget=='kalender') echo' selected="selected"'?>>kalender: Kalenderfenster</option></select>&nbsp;
 oder anderes Zielfenster  <input type="text" name="NeueXTarget" value="<?php echo $ksNeueXTarget?>" style="width:100px;" /> (Target) &nbsp;
 <input class="admRadio" type="checkbox" name="NeuePopup" value="1"<?php if($ksNeuePopup) echo' checked="checked"'?>> als Popupfenster</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern die neuen Termine direkt aufgerufen werden (auch in einem i-Frame)
wird als Verweisziel für die Kalendertermine aus den neuen Terminen heraus
automatisch das Kalenderscript <i>kalender.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Kalenders hier angeben.<br />
Wenn die neuen Termine in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
wird als Verweisziel das aufrufende PHP-Script selbst angenommen,
es sei denn Sie vereinbaren hier ein anderes Verweisziel zur Anzeige der Termine.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td colspan="2"><input type="text" name="NeueLink" value="<?php echo $ksNeueLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname, eventuell mit absoluter Pfadangabe aber ohne Domain und ohne QueryString</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p style="margin-top:20px;">Die folgenden Farben und Attribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> editieren.</p>
<form name="farbform" action="konfNeue.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern die NeuenTermine <i>eigenständig</i> laufen und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Termine erhalten einen farbigen Rahmen und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />und Gitternetz</td>
 <td><select name="TNeueA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTNeueA)?></select> Linien</td>
 <td><input type="text" name="TNeueR" value="<?php echo $sTNeueR?>" style="width:70px">
 <a href="<?php echo fColorRef('TNeueR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTNeueA?> <?php echo $sTNeueR?>;color:<?php echo $sTNeueR?>;background-color:<?php echo $sZNeu1H?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Termine können eine individuelle Schriftgröße erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schriftgröße</td>
 <td colspan="3"><input type="text" name="NeuFS" value="<?php echo $sNeuFS?>" style="width:70px"> (Masseinheit <i>em</i> oder <i>px</i> unbedingt angeben!)</td>
 <td class="admMini">Empfehlung: 0.8em</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die umhüllende Tabelle der neuen Termine kann eine individuelle Breitenangabe erhalten.
Dies ist in den meisten Fällen aber nicht notwendig, da sich die Breite über die Schriftgröße von selbst einregelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenbreite</td>
 <td colspan="3"><input type="text" name="NeuW" value="<?php echo $sNeuW?>" style="width:70px"> (<i>auto</i> oder mit Masseinheit <i>%</i> oder <i>px</i>)</td>
 <td class="admMini">keine Empfehlung<br />evt. leer lassen</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Termine werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Terminfelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Terminliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 200...600px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In den neuen Terminen treten folgende Datenzellen auf.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 1<br />ungerade Zeile</td>
 <td><input type="text" name="ZNeu1F" value="<?php echo $sZNeu1F?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu1F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeu1H" value="<?php echo $sZNeu1H?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu1H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeu1F?>;background-color:<?php echo $sZNeu1H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 2<br />gerade Zeile</td>
 <td><input type="text" name="ZNeu2F" value="<?php echo $sZNeu2F?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu2F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeu2H" value="<?php echo $sZNeu2H?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeu2H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeu2F?>;background-color:<?php echo $sZNeu2H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die neuen Termine können eine Kopfzeile besitzen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeilenzelle</td>
 <td><input type="text" name="ZNeuKF" value="<?php echo $sZNeuKF?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeuKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZNeuKH" value="<?php echo $sZNeuKH?>" style="width:70px"> <a href="<?php echo fColorRef('ZNeuKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZNeuKF?>;background-color:<?php echo $sZNeuKH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Verweis in den Kalender sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="ANeuL" value="<?php echo $sANeuL?>" style="width:70px"> <a href="<?php echo fColorRef('ANeuL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="ANeuA" value="<?php echo $sANeuA?>" style="width:70px"> <a href="<?php echo fColorRef('ANeuA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTNeueR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sANeuL?>;background-color:<?php echo $sZNeu1H?>;" onmouseover="this.style.color='<?php echo $sANeuA?>'" onmouseout="this.style.color='<?php echo $sANeuL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fLiesFarbe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1); $q+=5; $z=strpos($sCss,';',$q);
   if($q>5&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFarbe($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1);
   $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesHGFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   if($q=strpos($sCss,'background-color',$p)) $q+=16; $z=strpos($sCss,';',$q);
   if($q>16&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzHGFarb($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e);
      if($e<$p+14) return trim(substr($sCss,$p,$e-$p)); else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarb($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarbB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmArt($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,$l); else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArt($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArtB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesWeite($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'width',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fLiesFontS($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeWeite($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'width',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFontS($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesScreenW($w){
 global $sCss;
 if($p=strpos($sCss,$w)) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p&&$q>$e) return str_replace(' ','',trim(substr($sCss,++$p,$e-$p))); else return false;
  }else return false;
 }else return false;
}
function fSetzScreenW($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p++&&$q>$e){
    if(substr($sCss,$p,$e-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fRahmenArten($s){
 return '<option value="none">unsichtbar</option><option value="solid"'.($s!='solid'?'':' selected="selected"').'>volle Linie</option><option value="dotted"'.($s!='dotted'?'':' selected="selected"').'>gepunktet</option><option value="dashed"'.($s!='dashed'?'':' selected="selected"').'>gestrichelt</option>';
}
function fColorRef($n){$s=$GLOBALS['s'.$n]; return 'colors.php?col='.($s!='transparent'?substr($s,1):$s).'&fld='.$n.'" target="color" onClick="javascript:ColWin()';}
function fTxtCol($Var){
 $s=(isset($_POST[$Var])?strtolower(str_replace('"',"'",stripslashes(trim($_POST[$Var])))):'');
 if(strlen($s)>0&&$s!='transparent'){if(substr($s,0,1)!='#') $s='#'.$s; while(strlen($s)<7) $s.='0';}
 return $s;
}
function fTxtSiz($Var){return (isset($_POST[$Var])?strtolower(str_replace('"',"'",str_replace(',','.',str_replace(' ','',stripslashes(trim($_POST[$Var])))))):'');}
?>