<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('aktuelle Termine anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','PAk');

$nFelder=count($kal_FeldName); $bNeu=false; $ksAktuelleXTarget='';
if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET
 $ksAktuelleAnzahl=KAL_AktuelleAnzahl; $ksAktuelleTage=KAL_AktuelleTage; $ksAktuelleTagLfd=KAL_AktuelleTagLfd;
 $ksAktuelleIndex=KAL_AktuelleIndex; $ksAktuelleRueckw=KAL_AktuelleRueckw; $ksAktuelleEnde=KAL_AktuelleEnde; $ksAktuelleZeit=KAL_AktuelleZeit;
 $ksAktuelleKopf=KAL_AktuelleKopf; $ksAktuelleKpfWdh=KAL_AktuelleKpfWdh; $ksAktuelleAbstand=KAL_AktuelleAbstand;
 $ksAktuelleMitWochentag=KAL_AktuelleMitWochentag; $ksAktuelleJahrhundert=KAL_AktuelleJahrhundert;
 $ksAktuelleEigeneZeilen=KAL_AktuelleEigeneZeilen; $ksAktuelleNotErsatz=KAL_AktuelleNotErsatz;
 $ksAktuelleLink=KAL_AktuelleLink; $ksAktuelleTarget=KAL_AktuelleTarget; $ksAktuellePopup=KAL_AktuellePopup;
 if($ksAktuelleTarget!='kalender'&&$ksAktuelleTarget!='_self'&&$ksAktuelleTarget!='_parent'&&$ksAktuelleTarget!='_top'&&$ksAktuelleTarget!='_blank') $ksAktuelleXTarget=$ksAktuelleTarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php'))));
 $v=min(max(txtVar('AktuelleAnzahl'),1),999); if(fSetzKalWert($v,'AktuelleAnzahl','')) $bNeu=true;
 $v=min(max((int)txtVar('AktuelleTage'),0),999); if(fSetzKalWert($v,'AktuelleTage','')) $bNeu=true;
 $v=(int)txtVar('AktuelleTagLfd'); if(fSetzKalWert(($v?true:false),'AktuelleTagLfd','')) $bNeu=true;
 $v=(int)txtVar('AktuelleIndex');if(fSetzKalWert($v,'AktuelleIndex','')) $bNeu=true;
 $v=(int)txtVar('AktuelleRueckw'); if(fSetzKalWert((($v&&$ksAktuelleIndex==1)?true:false),'AktuelleRueckw','')) $bNeu=true;
 $v=(int)txtVar('AktuelleEnde'); if(fSetzKalWert(($v?true:false),'AktuelleEnde','')) $bNeu=true;
 $v=(int)txtVar('AktuelleZeit'); if(fSetzKalWert(($v?true:false),'AktuelleZeit','')) $bNeu=true;
 $v=(int)txtVar('AktuelleNotErsatz'); if(fSetzKalWert(($v?true:false),'AktuelleNotErsatz','')) $bNeu=true;
 $v=(int)txtVar('AktuelleKopf'); if(fSetzKalWert(($v?true:false),'AktuelleKopf','')) $bNeu=true;
 $v=(int)txtVar('AktuelleKpfWdh'); if(fSetzKalWert(($v?true:false),'AktuelleKpfWdh','')) $bNeu=true;
 $v=max((int)txtVar('AktuelleAbstand'),0); if(fSetzKalWert($v,'AktuelleAbstand','')) $bNeu=true;
 $v=(int)txtVar('AktuelleMitWochentag'); if(fSetzKalWert($v,'AktuelleMitWochentag','')) $bNeu=true;
 $v=txtVar('AktuelleJahrhundert'); if(fSetzKalWert(($v?true:false),'AktuelleJahrhundert','')) $bNeu=true;
 $v=txtVar('AktuelleEigeneZeilen'); if(fSetzKalWert(($v?true:false),'AktuelleEigeneZeilen','')) $bNeu=true;
 $kal_AktuelleFeld=array(); $kal_AktuelleLink=array(); $kal_AktuelleStil=array();
 for($i=0;$i<$nFelder;$i++){
  $kal_AktuelleFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_AktuelleLink[$i]=(isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0);
  $kal_AktuelleStil[$i]=(isset($_POST['Z'.$i])?str_replace("'",'"',stripslashes($_POST['Z'.$i])):'');
 }
 asort($kal_AktuelleFeld); reset($kal_AktuelleFeld);
 $j=0; foreach($kal_AktuelleFeld as $k=>$v) if($v>0) if($k>0) $kal_AktuelleFeld[$k]=++$j;
 if(fSetzArray($kal_AktuelleFeld,'AktuelleFeld','')) $bNeu=true;
 if(fSetzArray($kal_AktuelleLink,'AktuelleLink','')) $bNeu=true;
 if(fSetzArray($kal_AktuelleStil,'AktuelleStil',"'")) $bNeu=true;
 $v=txtVar('AktuelleLink'); if(fSetzKalWert($v,'AktuelleLink','"')) $bNeu=true;
 if($v=txtVar('AktuelleTarget')) $ksAktuelleXTarget=''; else{$v=txtVar('AktuelleXTarget'); $ksAktuelleXTarget=$v;}  if(fSetzKalWert($v,'AktuelleTarget','"')) $bNeu=true;
 $v=txtVar('AktuellePopup'); if(fSetzKalWert(($v?true:false),'AktuellePopup','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die geänderten Einstellungen für aktuelle Termine wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST

if(file_exists(KALPFAD.'kalStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sPageH=fLiesHGFarb('body.kalAktuelle');
  $sTListW=fLiesScreenW('aktuellen Termine mit Spalten');
  $sTAktuR=fLiesRahmFarb('div.kalTbALst'); $sTAktuA=fLiesRahmArt('div.kalTbALst');
  $sAktFS=fLiesFontS('div.kalTabA'); $sAktW=fLiesWeite('div.kalTabA');
  $sZAkt1F=fLiesFarbe('div.kalTbAZl1'); $sZAkt1H=fLiesHGFarb('div.kalTbAZl1');
  $sZAkt2F=fLiesFarbe('div.kalTbAZl2'); $sZAkt2H=fLiesHGFarb('div.kalTbAZl2');
  $sZAktKF=fLiesFarbe('div.kalTbAZl0'); $sZAktKH=fLiesHGFarb('div.kalTbAZl0');
  $sAAktL=fLiesFarbe('a.kalAktu:link'); $sAAktA=fLiesFarbe('a.kalAktu:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sPageH=fTxtCol('PageH'); if(fSetzHGFarb($sPageH,'body.kalAktuelle')) $bNeu=true;
  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'aktuellen Termine mit Spalten')) $bNeu=true;
  $sTAktuR=fTxtCol('TAktuR'); if($sTAktuR!=fLiesRahmFarb('div.kalTbALst')){
   if(fSetzRahmFarb($sTAktuR,'div.kalTabA')) $bNeu=true;
   if(fSetzRahmFarbB($sTAktuR,'div.kalTbAZl0')) $bNeu=true;
   if(fSetzRahmFarbB($sTAktuR,'div.kalTbAZl1')) $bNeu=true;
   if(fSetzRahmFarbB($sTAktuR,'div.kalTbAZl2')) $bNeu=true;
   if(fSetzRahmFarb($sTAktuR,'div.kalTbALst')) $bNeu=true;
   if(fSetzRahmFarb($sTAktuR,'div.kalTbALst',2)) $bNeu=true;
  }
  $sTAktuA=$_POST['TAktuA']; if($sTAktuA!=fLiesRahmArt('div.kalTbALst')){
   if(fSetzeRahmArtB($sTAktuA,'div.kalTbAZl0')) $bNeu=true;
   if(fSetzeRahmArtB($sTAktuA,'div.kalTbAZl1')) $bNeu=true;
   if(fSetzeRahmArtB($sTAktuA,'div.kalTbAZl2')) $bNeu=true;
   if(fSetzeRahmArt($sTAktuA,'div.kalTbALst')) $bNeu=true;
   if(fSetzeRahmArt($sTAktuA,'div.kalTbALst',2)) $bNeu=true;
  }
  $sAktW= fTxtSiz('AktW');  if(fSetzeWeite($sAktW,'div.kalTabA')) $bNeu=true;
  $sAktFS=fTxtSiz('AktFS'); if(fSetzeFontS($sAktFS,'div.kalTabA')) $bNeu=true;
  $sZAkt1F=fTxtCol('ZAkt1F'); if(fSetzeFarbe($sZAkt1F,'div.kalTbAZl1')) $bNeu=true;
  $sZAkt1H=fTxtCol('ZAkt1H'); if(fSetzHGFarb($sZAkt1H,'div.kalTbAZl1')) $bNeu=true;
  $sZAkt2F=fTxtCol('ZAkt2F'); if(fSetzeFarbe($sZAkt2F,'div.kalTbAZl2')) $bNeu=true;
  $sZAkt2H=fTxtCol('ZAkt2H'); if(fSetzHGFarb($sZAkt2H,'div.kalTbAZl2')) $bNeu=true;
  $sZAktKF=fTxtCol('ZAktKF'); if(fSetzeFarbe($sZAktKF,'div.kalTbAZl0')) $bNeu=true;
  $sZAktKH=fTxtCol('ZAktKH'); if(fSetzHGFarb($sZAktKH,'div.kalTbAZl0')) $bNeu=true;
  $sAAktL=fTxtCol('AAktL'); if(fSetzeFarbe($sAAktL,'a.kalAktu:link')) $bNeu=true;
  $sAAktA=fTxtCol('AAktA'); if(fSetzeFarbe($sAAktA,'a.kalAktu:hover')) $bNeu=true;
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
 if($t!='z'&&$t!='v'&&$t!='l'&&$t!='e'&&$t!='b'&&$t!='x'&&$t!='f'&&$t!='c'&&$t!='p') $sSortOpt.='<option value="'.$i.'"'.($ksAktuelleIndex!=$i||$i==1?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
}
$sIcon=$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfAktuell.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Als aktuelle Termine können einige der demnächst stattfindenden Ereignisse abgebildet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">aktuelle Anzahl</td>
 <td colspan="2"><input type="text" name="AktuelleAnzahl" value="<?php echo $ksAktuelleAnzahl?>" style="width:3em;" /> Termine <span class="admMini">(Empfehlung: ca. 1...10)</span>
 <div class="admMini">Maximalzahl der aktuellen Termine die dargestellt werden sollen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">aktuelle Zeitraum</td>
 <td colspan="2"><input type="text" name="AktuelleTage" value="<?php echo $ksAktuelleTage?>" style="width:3em;" /> Tage <span class="admMini">(ca. 3...10, 0 für nicht nach dem Datum begrenzt)</span>
 <div><input class="admRadio" type="radio" name="AktuelleTagLfd" value="1"<?php if($ksAktuelleTagLfd) echo' checked="checked"'?>> gemeint als Intervall der Kalendertage ab heute gerechnet</div>
 <div><input class="admRadio" type="radio" name="AktuelleTagLfd" value="0"<?php if(!$ksAktuelleTagLfd) echo' checked="checked"'?>> gemeint als Anzahl an Kalendertagen, die mit einem Termin belegt sind</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">aktuelle Auswahl</td>
 <td colspan="2"><input class="admRadio" type="radio" name="AktuelleEnde" value="0"<?php if(!$ksAktuelleEnde) echo' checked="checked"'?>> nur Terminbeginn auswerten &nbsp;
 <input class="admRadio" type="radio" name="AktuelleEnde" value="1"<?php if($ksAktuelleEnde) echo' checked="checked"'?>> auch laufende Termine (evt. vorhandenes Ende berücksichtigen)
 <div><input class="admRadio" type="radio" name="AktuelleZeit" value="0"<?php if(!$ksAktuelleZeit) echo' checked="checked"'?>> nur nach Datum entscheiden &nbsp;
 <input class="admRadio" type="radio" name="AktuelleZeit" value="1"<?php if($ksAktuelleZeit) echo' checked="checked"'?>> auch Uhrzeiten berücksichtigen</div>
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Notauswahl</td>
 <td colspan="2">
 <input class="admCheck" type="checkbox" name="AktuelleNotErsatz" value="1"<?php if($ksAktuelleNotErsatz) echo' checked="checked"'?>>
 wenn keine aktuellen Termine vorrätig sind dann unaktuelle/abgelaufene Termine zeigen
 <div class="admMini"><u>Empfehlung</u>: <i>NICHT</i> einschalten</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">DatumsSortierung</td>
 <td colspan="2"><input class="admRadio" type="radio" name="AktuelleRueckw" value="0"<?php if(!$ksAktuelleRueckw) echo' checked="checked"'?>> vorwärts sortiert &nbsp;
 <input class="admRadio" type="radio" name="AktuelleRueckw" value="1"<?php if($ksAktuelleRueckw) echo' checked="checked"'?>> rückwärts sortiert</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfeld</td>
 <td colspan="2" width="80%"><select name="AktuelleIndex"><option value="1">Standard</option><?php echo $sSortOpt;?></select> Empfehlung: Standard (nach Datum)</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Das Erscheinungsbild der aktuellen Termine kann individuell eingestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile</td>
 <td colspan="2"><input class="admRadio" type="checkbox" name="AktuelleKopf" value="1"<?php if($ksAktuelleKopf) echo' checked="checked"'?>> über den aktuellen Terminen soll ein Kopf angezeigt werden</br >
 <input class="admRadio" type="checkbox" name="AktuelleKpfWdh" value="1"<?php if($ksAktuelleKpfWdh) echo' checked="checked"'?>> die Kopfzeile soll über jedem einzelnen aktuellen Termin wiederholend erscheinen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminabstand</td>
 <td colspan="2"><input type="text" name="AktuelleAbstand" value="<?php echo $ksAktuelleAbstand?>" style="width:24px;" /> Pixel vertikaler Abstand zwischen den einzelnen aktuellen Terminen &nbsp; <span class="admMini">(Empfehlung: <i>0</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td colspan="2"><select name="AktuelleMitWochentag" size="1"><option value="0">Datum ohne Wochentage</option><option value="1"<?php if($ksAktuelleMitWochentag==1) echo ' selected="selected"'?>>Wochentag vor dem Datum</option><option value="2"<?php if($ksAktuelleMitWochentag==2) echo ' selected="selected"'?>>Wochentag nach dem Datum</option></select> &nbsp; &nbsp;
 Jahreszahl <input class="admRadio" type="radio" name="AktuelleJahrhundert" value="1"<?php if($ksAktuelleJahrhundert) echo' checked="checked"'?> /> 4-stellig oder <input class="admRadio" type="radio" name="AktuelleJahrhundert" value="0"<?php if(!$ksAktuelleJahrhundert) echo' checked="checked"'?> /> 2-stellig</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die aktuellen Ereignisse werden standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt.
Abweichend davon kann jeder Termindatensatz in einem individuellen Layout dargestellt werden, das aus der Layoutschablone <i>aktuelleZeile.htm</i> und gegebenfalls <i>aktuelleKopf.htm</i> stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#4.2.eigen" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout</td>
 <td colspan="2"><input class="admRadio" type="radio" name="AktuelleEigeneZeilen" value="0"<?php if(!$ksAktuelleEigeneZeilen) echo' checked="checked"'?>> tabellarisches Standardlayout &nbsp;
 <input class="admRadio" type="radio" name="AktuelleEigeneZeilen" value="1"<?php if($ksAktuelleEigeneZeilen) echo' checked="checked"'?>> individuelles Layout aus den Layoutschablonen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die aktuellen Termine können bezüglich der anzuzeigenden Felder konfiguriert werden.
Welche Felder sollen in den aktuellen Terminen wie erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Feld-Nr.</td>
 <td>Anzeigespalte im aktuellen Termin</td>
 <td>optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.6.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<?php
 include('feldtypenInc.php');
 $sOpt='<option value="0">---</option>'; for($i=1;$i<$nFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i];
  if(!$k=$kal_AktuelleFeld[$i]) $sO=$sOpt; else $sO=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if($i!=1){if($t=='v') $sO='<option value="0">---</option>';} //versteckt
  else $sO=substr($sO,strpos($sO,'<option',1)); //Datum
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo sprintf('%02d',$i).') '.$kal_FeldName[$i].'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div>'?></td>
 <td>
<?php if($t!='c'&&$t!='u'&&$t!='p'){?>
 <select name="F<?php echo $i?>" size="1" style="width:42px;"><?php echo $sO?></select> &nbsp; &nbsp;
<?php if($t!='f'&&$t!='l'&&$t!='e'&&$t!='v'){?>
 <input type="checkbox" class="admCheck" name="L<?php echo $i?>" value="1"<?php if($kal_AktuelleLink[$i]) echo ' checked="checked"'?> /> als&nbsp;Detaillink
<?php }?>
 </td>
 <td><input type="text" name="Z<?php echo $i?>" style="width:200px;" value="<?php echo $kal_AktuelleStil[$i]?>" />
<?php }else{?>
 &nbsp;----<input type="hidden" name="F<?php echo $i?>" value="0" />
 </td>
 <td>&nbsp;
<?php }?>
 </td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Bei einem Klick auf einen Link in den aktuellen Terminen
soll sich das Kalenderscript öffnen und die Termindetails anzeigen. In welchem Zielfenster (Target) soll das passieren?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zielfenster</td>
 <td colspan="2"><select name="AktuelleTarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($ksAktuelleTarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($ksAktuelleTarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($ksAktuelleTarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($ksAktuelleTarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="kalender"<?php if($ksAktuelleTarget=='kalender') echo' selected="selected"'?>>kalender: Kalenderfenster</option></select>&nbsp;
 oder anderes Zielfenster  <input type="text" name="AktuelleXTarget" value="<?php echo $ksAktuelleXTarget?>" style="width:100px;" /> (Target) &nbsp;
 <input class="admRadio" type="checkbox" name="AktuellePopup" value="1"<?php if($ksAktuellePopup) echo' checked="checked"'?>> als Popupfenster</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern die aktuellen Termine direkt aufgerufen werden (auch in einem i-Frame)
wird als Verweisziel für die Kalendertermine aus den aktuellen Terminen heraus
automatisch das Kalenderscript <i>kalender.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Kalenders hier angeben.<br />
Wenn die aktuellen Termine in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
wird als Verweisziel das aufrufende PHP-Script selbst angenommen,
es sei denn Sie vereinbaren hier ein anderes Verweisziel zur Anzeige der Termine.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td colspan="2"><input type="text" name="AktuelleLink" value="<?php echo $ksAktuelleLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname, eventuell mit absoluter Pfadangabe aber ohne Domain und ohne QueryString</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p style="margin-top:20px;">Die folgenden Farben und Attribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> editieren.</p>
<form name="farbform" action="konfAktuell.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern die AktuellenTermine <i>eigenständig</i> laufen und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die aktuellen Termine erhalten einen farbigen Rahmen und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />außen</td>
 <td><select name="TAktuA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTAktuA)?></select> Linien</td>
 <td><input type="text" name="TAktuR" value="<?php echo $sTAktuR?>" style="width:70px">
 <a href="<?php echo fColorRef('TAktuR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTAktuA?> <?php echo $sTAktuR?>;color:<?php echo $sTAktuR?>;background-color:<?php echo $sZAkt1H?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die aktuellen Termine können eine individuelle Schriftgröße erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schriftgröße</td>
 <td colspan="3"><input type="text" name="AktFS" value="<?php echo $sAktFS?>" style="width:70px"> (Masseinheit <i>em</i> oder <i>px</i> unbedingt angeben!)</td>
 <td class="admMini">Empfehlung: 0.8em</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die umhüllende Tabelle der aktuellen Termine kann eine individuelle Breitenangabe erhalten.
Dies ist in den meisten Fällen aber nicht notwendig, da sich die Breite über die Schriftgröße von selbst einregelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenbreite</td>
 <td colspan="3"><input type="text" name="AktW" value="<?php echo $sAktW?>" style="width:70px"> (<i>auto</i> oder mit Masseinheit <i>%</i> oder <i>px</i>)</td>
 <td class="admMini">keine Empfehlung<br />evt. leer lassen</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die aktuellen Termine werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Terminfelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Terminliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 200...600px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In den aktuellen Terminen treten folgende Datumszellen auf.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 1<br />ungerade Zeile</td>
 <td><input type="text" name="ZAkt1F" value="<?php echo $sZAkt1F?>" style="width:70px"> <a href="<?php echo fColorRef('ZAkt1F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZAkt1H" value="<?php echo $sZAkt1H?>" style="width:70px"> <a href="<?php echo fColorRef('ZAkt1H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTAktuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZAkt1F?>;background-color:<?php echo $sZAkt1H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 2<br />gerade Zeile</td>
 <td><input type="text" name="ZAkt2F" value="<?php echo $sZAkt2F?>" style="width:70px"> <a href="<?php echo fColorRef('ZAkt2F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZAkt2H" value="<?php echo $sZAkt2H?>" style="width:70px"> <a href="<?php echo fColorRef('ZAkt2H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTAktuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZAkt2F?>;background-color:<?php echo $sZAkt2H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die aktuellen Termine können eine Kopfzeile besitzen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeilenzelle</td>
 <td><input type="text" name="ZAktKF" value="<?php echo $sZAktKF?>" style="width:70px"> <a href="<?php echo fColorRef('ZAktKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZAktKH" value="<?php echo $sZAktKH?>" style="width:70px"> <a href="<?php echo fColorRef('ZAktKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTAktuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZAktKF?>;background-color:<?php echo $sZAktKH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Verweis in den Kalender sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="AAktL" value="<?php echo $sAAktL?>" style="width:70px"> <a href="<?php echo fColorRef('AAktL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="AAktA" value="<?php echo $sAAktA?>" style="width:70px"> <a href="<?php echo fColorRef('AAktA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTAktuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAAktL?>;background-color:<?php echo $sZAkt1H?>;" onmouseover="this.style.color='<?php echo $sAAktA?>'" onmouseout="this.style.color='<?php echo $sAAktL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
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