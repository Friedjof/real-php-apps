<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('laufende Termine anpassen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','PLf');

$nFelder=count($kal_FeldName); $bNeu=false; $ksLaufendeXTarget='';
if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET
 $ksLaufendeIndex=KAL_LaufendeIndex; $ksLaufendeRueckw=KAL_LaufendeRueckw; $ksLaufendeEnde=KAL_LaufendeEnde; $ksLaufendeZeit=KAL_LaufendeZeit;
 $ksLaufendeKopf=KAL_LaufendeKopf; $ksLaufendeKpfWdh=KAL_LaufendeKpfWdh; $ksLaufendeAbstand=KAL_LaufendeAbstand;
 $ksLaufendeMitWochentag=KAL_LaufendeMitWochentag; $ksLaufendeJahrhundert=KAL_LaufendeJahrhundert;
 $ksLaufendeEigeneZeilen=KAL_LaufendeEigeneZeilen; $ksLaufendeNotErsatz=KAL_LaufendeNotErsatz;
 $ksLaufendeLink=KAL_LaufendeLink; $ksLaufendeTarget=KAL_LaufendeTarget; $ksLaufendePopup=KAL_LaufendePopup;
 if($ksLaufendeTarget!='kalender'&&$ksLaufendeTarget!='_self'&&$ksLaufendeTarget!='_parent'&&$ksLaufendeTarget!='_top'&&$ksLaufendeTarget!='_blank') $ksLaufendeXTarget=$ksLaufendeTarget;
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php'))));
 $v=(int)txtVar('LaufendeIndex');if(fSetzKalWert($v,'LaufendeIndex','')) $bNeu=true;
 $v=(int)txtVar('LaufendeRueckw'); if(fSetzKalWert((($v&&$ksLaufendeIndex==1)?true:false),'LaufendeRueckw','')) $bNeu=true;
 $v=txtVar('LaufendeEnde'); if(fSetzKalWert(($v?true:false),'LaufendeEnde','')) $bNeu=true;
 $v=txtVar('LaufendeZeit'); if(fSetzKalWert(($v?true:false),'LaufendeZeit','')) $bNeu=true;
 $v=(int)txtVar('LaufendeNotErsatz'); if(fSetzKalWert(($v?true:false),'LaufendeNotErsatz','')) $bNeu=true;
 $v=txtVar('LaufendeKopf'); if(fSetzKalWert(($v?true:false),'LaufendeKopf','')) $bNeu=true;
 $v=txtVar('LaufendeKpfWdh'); if(fSetzKalWert(($v?true:false),'LaufendeKpfWdh','')) $bNeu=true;
 $v=max(txtVar('LaufendeAbstand'),0); if(fSetzKalWert($v,'LaufendeAbstand','')) $bNeu=true;
 $v=(int)txtVar('LaufendeMitWochentag'); if(fSetzKalWert($v,'LaufendeMitWochentag','')) $bNeu=true;
 $v=txtVar('LaufendeJahrhundert'); if(fSetzKalWert(($v?true:false),'LaufendeJahrhundert','')) $bNeu=true;
 $v=txtVar('LaufendeEigeneZeilen'); if(fSetzKalWert(($v?true:false),'LaufendeEigeneZeilen','')) $bNeu=true;
 $kal_LaufendeFeld=array(); $kal_LaufendeLink=array(); $kal_LaufendeStil=array();
 for($i=0;$i<$nFelder;$i++){
  $kal_LaufendeFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $kal_LaufendeLink[$i]=(isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0);
  $kal_LaufendeStil[$i]=(isset($_POST['Z'.$i])?str_replace("'",'"',stripslashes($_POST['Z'.$i])):'');
 }
 asort($kal_LaufendeFeld); reset($kal_LaufendeFeld);
 $j=0; foreach($kal_LaufendeFeld as $k=>$v) if($v>0) if($k>0) $kal_LaufendeFeld[$k]=++$j;
 if(fSetzArray($kal_LaufendeFeld,'LaufendeFeld','')) $bNeu=true;
 if(fSetzArray($kal_LaufendeLink,'LaufendeLink','')) $bNeu=true;
 if(fSetzArray($kal_LaufendeStil,'LaufendeStil',"'")) $bNeu=true;
 $v=txtVar('LaufendeLink'); if(fSetzKalWert($v,'LaufendeLink','"')) $bNeu=true;
 if($v=txtVar('LaufendeTarget')) $ksLaufendeXTarget=''; else{$v=txtVar('LaufendeXTarget'); $ksLaufendeXTarget=$v;}  if(fSetzKalWert($v,'LaufendeTarget','"')) $bNeu=true;
 $v=txtVar('LaufendePopup'); if(fSetzKalWert(($v?true:false),'LaufendePopup','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die geänderten Einstellungen für laufende Termine wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST

if(file_exists(KALPFAD.'kalStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sPageH=fLiesHGFarb('body.kalLaufende');
  $sTListW=fLiesScreenW('laufenden Termine mit Spalten');
  $sTLfndR=fLiesRahmFarb('div.kalTbLLst'); $sTLfndA=fLiesRahmArt('div.kalTbLLst');
  $sLfdFS=fLiesFontS('div.kalTabL'); $sLfdW=fLiesWeite('div.kalTabL');
  $sZLfd1F=fLiesFarbe('div.kalTbLZl1'); $sZLfd1H=fLiesHGFarb('div.kalTbLZl1');
  $sZLfd2F=fLiesFarbe('div.kalTbLZl2'); $sZLfd2H=fLiesHGFarb('div.kalTbLZl2');
  $sZLfdKF=fLiesFarbe('div.kalTbLZl0'); $sZLfdKH=fLiesHGFarb('div.kalTbLZl0');
  $sALfdL=fLiesFarbe('a.kalLfnd:link'); $sALfdA=fLiesFarbe('a.kalLfnd:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sPageH=fTxtCol('PageH'); if(fSetzHGFarb($sPageH,'body.kalLaufende')) $bNeu=true;
  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'laufenden Termine mit Spalten')) $bNeu=true;
  $sTLfndR=fTxtCol('TLfndR'); if($sTLfndR!=fLiesRahmFarb('div.kalTbLLst')){
   if(fSetzRahmFarb($sTLfndR,'div.kalTabL')) $bNeu=true;
   if(fSetzRahmFarbB($sTLfndR,'div.kalTbLZl0')) $bNeu=true;
   if(fSetzRahmFarbB($sTLfndR,'div.kalTbLZl1')) $bNeu=true;
   if(fSetzRahmFarbB($sTLfndR,'div.kalTbLZl2')) $bNeu=true;
   if(fSetzRahmFarb($sTLfndR,'div.kalTbLLst')) $bNeu=true;
   if(fSetzRahmFarb($sTLfndR,'div.kalTbLLst',2)) $bNeu=true;
  }
  $sTLfndA=$_POST['TLfndA']; if($sTLfndA!=fLiesRahmArt('div.kalTbLLst')){
   if(fSetzeRahmArtB($sTLfndA,'div.kalTbLZl0')) $bNeu=true;
   if(fSetzeRahmArtB($sTLfndA,'div.kalTbLZl1')) $bNeu=true;
   if(fSetzeRahmArtB($sTLfndA,'div.kalTbLZl2')) $bNeu=true;
   if(fSetzeRahmArt($sTLfndA,'div.kalTbLLst')) $bNeu=true;
   if(fSetzeRahmArt($sTLfndA,'div.kalTbLLst',2)) $bNeu=true;
  }
  $sLfdW= fTxtSiz('LfdW');  if(fSetzeWeite($sLfdW,'div.kalTabL')) $bNeu=true;
  $sLfdFS=fTxtSiz('LfdFS'); if(fSetzeFontS($sLfdFS,'div.kalTabL')) $bNeu=true;
  $sZLfd1F=fTxtCol('ZLfd1F'); if(fSetzeFarbe($sZLfd1F,'div.kalTbLZl1')) $bNeu=true;
  $sZLfd1H=fTxtCol('ZLfd1H'); if(fSetzHGFarb($sZLfd1H,'div.kalTbLZl1')) $bNeu=true;
  $sZLfd2F=fTxtCol('ZLfd2F'); if(fSetzeFarbe($sZLfd2F,'div.kalTbLZl2')) $bNeu=true;
  $sZLfd2H=fTxtCol('ZLfd2H'); if(fSetzHGFarb($sZLfd2H,'div.kalTbLZl2')) $bNeu=true;
  $sZLfdKF=fTxtCol('ZLfdKF'); if(fSetzeFarbe($sZLfdKF,'div.kalTbLZl0')) $bNeu=true;
  $sZLfdKH=fTxtCol('ZLfdKH'); if(fSetzHGFarb($sZLfdKH,'div.kalTbLZl0')) $bNeu=true;
  $sALfdL=fTxtCol('ALfdL'); if(fSetzeFarbe($sALfdL,'a.kalLfnd:link')) $bNeu=true;
  $sALfdA=fTxtCol('ALfdA'); if(fSetzeFarbe($sALfdA,'a.kalLfnd:hover')) $bNeu=true;
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
 if($t!='z'&&$t!='v'&&$t!='l'&&$t!='e'&&$t!='b'&&$t!='x'&&$t!='f'&&$t!='c'&&$t!='p') $sSortOpt.='<option value="'.$i.'"'.($ksLaufendeIndex!=$i||$i==1?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
}
$sIcon=$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfLaufend.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1">laufende Auswahl</td>
 <td colspan="2"><input class="admRadio" type="radio" name="LaufendeEnde" value="0"<?php if(!$ksLaufendeEnde) echo' checked="checked"'?>> nur Terminbeginn auswerten &nbsp;
 <input class="admRadio" type="radio" name="LaufendeEnde" value="1"<?php if($ksLaufendeEnde) echo' checked="checked"'?>> auch evt. vorhandenes Ende berücksichtigen
 <div><input class="admRadio" type="radio" name="LaufendeZeit" value="0"<?php if(!$ksLaufendeZeit) echo' checked="checked"'?>> nur nach Datum entscheiden &nbsp;
 <input class="admRadio" type="radio" name="LaufendeZeit" value="1"<?php if($ksLaufendeZeit) echo' checked="checked"'?>> auch Uhrzeiten berücksichtigen</div>
 <div class="admMini"><u>Hinweis</u>: Die generelle Endeerkennung für Termine unter <i>Allgemeines</i> ist momentan <?php echo KAL_EndeDatum?'ein':'aus' ?>geschaltet.</div></td>
</td>
<tr class="admTabl">
 <td class="admSpa1">Notauswahl</td>
 <td colspan="2">
 <input class="admCheck" type="checkbox" name="LaufendeNotErsatz" value="1"<?php if($ksLaufendeNotErsatz) echo' checked="checked"'?>>
 wenn keine laufenden Termine vorrätig sind dann kommende/abgelaufene Termine zeigen
 <div class="admMini"><u>Empfehlung</u>: <i>NICHT</i> einschalten</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">DatumsSortierung</td>
 <td colspan="2"><input class="admRadio" type="radio" name="LaufendeRueckw" value="0"<?php if(!$ksLaufendeRueckw) echo' checked="checked"'?>> vorwärts sortiert &nbsp;
 <input class="admRadio" type="radio" name="LaufendeRueckw" value="1"<?php if($ksLaufendeRueckw) echo' checked="checked"'?>> rückwärts sortiert</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfeld</td>
 <td colspan="2" width="80%"><select name="LaufendeIndex"><option value="1">Standard</option><?php echo $sSortOpt;?></select> Empfehlung: Standard (nach Datum)</td>
</tr>

</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Das Erscheinungsbild der laufenden Termine kann individuell eingestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile</td>
 <td colspan="2"><input class="admRadio" type="checkbox" name="LaufendeKopf" value="1"<?php if($ksLaufendeKopf) echo' checked="checked"'?>> über den laufenden Terminen soll ein Kopf angezeigt werden</br >
 <input class="admRadio" type="checkbox" name="LaufendeKpfWdh" value="1"<?php if($ksLaufendeKpfWdh) echo' checked="checked"'?>> die Kopfzeile soll über jedem einzelnen laufenden Termin wiederholend erscheinen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminabstand</td>
 <td colspan="2"><input type="text" name="LaufendeAbstand" value="<?php echo $ksLaufendeAbstand?>" style="width:24px;" /> Pixel vertikaler Abstand zwischen den einzelnen laufenden Terminen &nbsp; <span class="admMini">(Empfehlung: <i>0</i>)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td colspan="2"><select name="LaufendeMitWochentag" size="1"><option value="0">Datum ohne Wochentage</option><option value="1"<?php if($ksLaufendeMitWochentag==1) echo ' selected="selected"'?>>Wochentag vor dem Datum</option><option value="2"<?php if($ksLaufendeMitWochentag==2) echo ' selected="selected"'?>>Wochentag nach dem Datum</option></select> &nbsp; &nbsp;
 Jahreszahl <input class="admRadio" type="radio" name="LaufendeJahrhundert" value="1"<?php if($ksLaufendeJahrhundert) echo' checked="checked"'?> /> 4-stellig oder <input class="admRadio" type="radio" name="LaufendeJahrhundert" value="0"<?php if(!$ksLaufendeJahrhundert) echo' checked="checked"'?> /> 2-stellig</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die laufenden Ereignisse werden standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt.
Abweichend davon kann jeder Termindatensatz in einem individuellen Layout dargestellt werden, das aus der Layoutschablone <i>laufendeZeile.htm</i> und gegebenfalls <i>laufendeKopf.htm</i> stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#4.2.eigen" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout</td>
 <td colspan="2"><input class="admRadio" type="radio" name="LaufendeEigeneZeilen" value="0"<?php if(!$ksLaufendeEigeneZeilen) echo' checked="checked"'?>> tabellarisches Standardlayout &nbsp;
 <input class="admRadio" type="radio" name="LaufendeEigeneZeilen" value="1"<?php if($ksLaufendeEigeneZeilen) echo' checked="checked"'?>> individuelles Layout aus den Layoutschablonen</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die laufenden Termine können bezüglich der anzuzeigenden Felder konfiguriert werden.
Welche Felder sollen in den laufenden Terminen wie erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Feld-Nr.</td>
 <td>Anzeigespalte im laufenden Termin</td>
 <td>optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.6.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<?php
 include('feldtypenInc.php');
 $sOpt='<option value="0">---</option>'; for($i=1;$i<$nFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i];
  if(!$k=$kal_LaufendeFeld[$i]) $sO=$sOpt; else $sO=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if($i!=1){if($t=='v') $sO='<option value="0">---</option>';} //versteckt
  else $sO=substr($sO,strpos($sO,'<option',1)); //Datum
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo sprintf('%02d',$i).') '.$kal_FeldName[$i].'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div>'?></td>
 <td>
<?php if($t!='c'&&$t!='u'&&$t!='p'){?>
 <select name="F<?php echo $i?>" size="1" style="width:42px;"><?php echo $sO?></select> &nbsp; &nbsp;
<?php if($t!='f'&&$t!='l'&&$t!='e'&&$t!='v'){?>
 <input type="checkbox" class="admCheck" name="L<?php echo $i?>" value="1"<?php if($kal_LaufendeLink[$i]) echo ' checked="checked"'?> /> als&nbsp;Detaillink
<?php }?>
 </td>
 <td><input type="text" name="Z<?php echo $i?>" style="width:200px;" value="<?php echo $kal_LaufendeStil[$i]?>" />
<?php }else{?>
 &nbsp;----<input type="hidden" name="F<?php echo $i?>" value="0" />
 </td>
 <td>&nbsp;
<?php }?>
 </td>
</tr>
<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Bei einem Klick auf einen Link in den laufenden Terminen
soll sich das Kalenderscript öffnen und die Termindetails anzeigen. In welchem Zielfenster (Target) soll das passieren?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zielfenster</td>
 <td colspan="2"><select name="LaufendeTarget" size="1" style="width:150px;"><option value=""></option><option value="_self"<?php if($ksLaufendeTarget=='_self') echo' selected="selected"'?>>_self: selbes Fenster</option><option value="_parent"<?php if($ksLaufendeTarget=='_parent') echo' selected="selected"'?>>_parent: Elternfenster</option><option value="_top"<?php if($ksLaufendeTarget=='_top') echo' selected="selected"'?>>_top: Hauptfenster</option><option value="_blank"<?php if($ksLaufendeTarget=='_blank') echo' selected="selected"'?>>_blank: neues Fenster</option><option value="kalender"<?php if($ksLaufendeTarget=='kalender') echo' selected="selected"'?>>kalender: Kalenderfenster</option></select>&nbsp;
 oder anderes Zielfenster  <input type="text" name="LaufendeXTarget" value="<?php echo $ksLaufendeXTarget?>" style="width:100px;" /> (Target) &nbsp;
 <input class="admRadio" type="checkbox" name="LaufendePopup" value="1"<?php if($ksLaufendePopup) echo' checked="checked"'?>> als Popupfenster</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern die laufenden Termine direkt aufgerufen werden (auch in einem i-Frame)
wird als Verweisziel für die Kalendertermine aus den laufenden Terminen heraus
automatisch das Kalenderscript <i>kalender.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Kalenders hier angeben.<br />
Wenn die laufenden Termine in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
wird als Verweisziel das aufrufende PHP-Script selbst angenommen,
es sei denn Sie vereinbaren hier ein anderes Verweisziel zur Anzeige der Termine.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td colspan="2"><input type="text" name="LaufendeLink" value="<?php echo $ksLaufendeLink?>" style="width:100%;" />
 <div class="admMini">leer lassen oder Scriptname, eventuell mit absoluter Pfadangabe aber ohne Domain und ohne QueryString</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>


<p style="margin-top:20px;">Die folgenden Farben und Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> editieren.</p>
<form name="farbform" action="konfLaufend.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern die LaufendenTermine <i>eigenständig</i> laufen und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die laufenden Termine erhalten einen farbigen Rahmen und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />und Gitternetz</td>
 <td><select name="TLfndA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTLfndA)?></select> Linien</td>
 <td><input type="text" name="TLfndR" value="<?php echo $sTLfndR?>" style="width:70px">
 <a href="<?php echo fColorRef('TLfndR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTLfndA?> <?php echo $sTLfndR?>;color:<?php echo $sTLfndR?>;background-color:<?php echo $sZLfd1H?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die laufenden Termine können eine individuelle Schriftgröße erhalten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schriftgröße</td>
 <td colspan="3"><input type="text" name="LfdFS" value="<?php echo $sLfdFS?>" style="width:70px"> (Masseinheit <i>em</i> oder <i>px</i> unbedingt angeben!)</td>
 <td class="admMini">Empfehlung: 0.8em</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die umhüllende Tabelle der laufenden Termine kann eine individuelle Breitenangabe erhalten.
Dies ist in den meisten Fällen aber nicht notwendig, da sich die Breite über die Schriftgröße von selbst einregelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenbreite</td>
 <td colspan="3"><input type="text" name="LfdW" value="<?php echo $sLfdW?>" style="width:70px"> (<i>auto</i> oder mit Masseinheit <i>%</i> oder <i>px</i>)</td>
 <td class="admMini">keine Empfehlung<br />evt. leer lassen</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die laufenden Termine werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Terminfelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Terminliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 200...600px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In den laufenden Terminen treten folgende Datenzellen auf.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 1<br />ungerade Zeile</td>
 <td><input type="text" name="ZLfd1F" value="<?php echo $sZLfd1F?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfd1F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZLfd1H" value="<?php echo $sZLfd1H?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfd1H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTLfndR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZLfd1F?>;background-color:<?php echo $sZLfd1H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzelle 2<br />gerade Zeile</td>
 <td><input type="text" name="ZLfd2F" value="<?php echo $sZLfd2F?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfd2F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZLfd2H" value="<?php echo $sZLfd2H?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfd2H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTLfndR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZLfd2F?>;background-color:<?php echo $sZLfd2H?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die laufenden Termine können eine Kopfzeile besitzen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeilenzelle</td>
 <td><input type="text" name="ZLfdKF" value="<?php echo $sZLfdKF?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfdKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="ZLfdKH" value="<?php echo $sZLfdKH?>" style="width:70px"> <a href="<?php echo fColorRef('ZLfdKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTLfndR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZLfdKF?>;background-color:<?php echo $sZLfdKH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Verweis in den Kalender sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="ALfdL" value="<?php echo $sALfdL?>" style="width:70px"> <a href="<?php echo fColorRef('ALfdL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="ALfdA" value="<?php echo $sALfdA?>" style="width:70px"> <a href="<?php echo fColorRef('ALfdA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTLfndR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sALfdL?>;background-color:<?php echo $sZLfd1H?>;" onmouseover="this.style.color='<?php echo $sALfdA?>'" onmouseout="this.style.color='<?php echo $sALfdL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
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