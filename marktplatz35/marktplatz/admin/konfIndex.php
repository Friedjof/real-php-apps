<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Startseiten-Einstellungen','','KIx');

if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['FarbForm'])){ //GET Werteform
 $mpTxIndexSeite=MP_TxIndexSeite; $mpIndexNormal=MP_IndexNormal; $mpSuchQFilter=MP_SuchQFilter; $mpSuchQLeer=MP_SuchQLeer;
 $mpIndexInZeilen=MP_IndexInZeilen; $mpIndexZeilen=MP_IndexZeilen; $mpIndexSpalten=MP_IndexSpalten; $mpIndexFuellen=MP_IndexFuellen;
 $mpIndexHorizontal=MP_IndexHorizontal; $mpIndexVertikal=MP_IndexVertikal; $mpIndexZelleW=MP_IndexZelleW; $mpIndexZelleH=MP_IndexZelleH;
 $mpIndexZaehler=MP_IndexZaehler; $mpIndexIcons=MP_IndexIcons; $mpIndexIconLinks=MP_IndexIconLinks;
 $mpIndexAktO=MP_IndexAktO; $mpIndexAktU=MP_IndexAktU; $mpIndexAktZ=MP_IndexAktZ;
 if($mpIndexZelleW=='auto') $mpIndexZelleW='240px';
}else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['FarbForm'])){ //POST Werteform
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('TxIndexSeite'); if(fSetzMPWert($v,'TxIndexSeite','"')) $bNeu=true;
 $v=(int)txtVar('IndexInZeilen'); if(fSetzMPWert(($v?true:false),'IndexInZeilen','')) $bNeu=true;
 $v=(int)txtVar('IndexZeilen'); if(fSetzMPWert($v,'IndexZeilen','')) $bNeu=true;
 $v=(int)txtVar('IndexSpalten'); if(fSetzMPWert($v,'IndexSpalten','')) $bNeu=true;
 $v=(int)txtVar('IndexFuellen'); if(fSetzMPWert(($v?true:false),'IndexFuellen','')) $bNeu=true;
 $v=txtVar('IndexHorizontal'); if(fSetzMPWert($v,'IndexHorizontal',"'")) $bNeu=true;
 $v=txtVar('IndexVertikal'); if(fSetzMPWert($v,'IndexVertikal',"'")) $bNeu=true;
 $v=txtVar('IndexZelleW'); if(fSetzMPWert($v,'IndexZelleW',"'")) $bNeu=true;
 $v=txtVar('IndexZelleH'); if(fSetzMPWert($v,'IndexZelleH',"'")) $bNeu=true;
 $v=(int)txtVar('SuchQFilter'); if(fSetzMPWert($v,'SuchQFilter','')) $bNeu=true;
 $v=(int)txtVar('SuchQLeer'); if(fSetzMPWert(($v?true:false),'SuchQLeer','')) $bNeu=true;
 $v=(int)txtVar('IndexZaehler'); if(fSetzMPWert(($v?true:false),'IndexZaehler','')) $bNeu=true;
 $v=(int)txtVar('IndexIcons'); if(fSetzMPWert(($v?true:false),'IndexIcons','')) $bNeu=true;
 $v=(int)txtVar('IndexIconLinks'); if(fSetzMPWert(($v?true:false),'IndexIconLinks','')) $bNeu=true;
 $v=(int)txtVar('IndexNormal'); if(fSetzMPWert(($v?true:false),'IndexNormal','')) $bNeu=true;
 $v=(int)txtVar('IndexAktO'); if(fSetzMPWert(($v?true:false),'IndexAktO','')) $bNeu=true;
 $v=(int)txtVar('IndexAktU'); if(fSetzMPWert(($v?true:false),'IndexAktU','')) $bNeu=true;
 $v=(int)txtVar('IndexAktZ'); if(fSetzMPWert(($v?true:false),'IndexAktZ','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Seiteneinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Seiteneinstellungen bleiben unverändert.'; $MTyp='Meld';}
}//POST

if(file_exists(MPPFAD.'mpStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(MPPFAD.'mpStyles.css')))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['WerteForm'])){
  $sTIdxR=fLiesRahmFarb('div.mpIdxZe'); $sTIdxA=fLiesRahmArt('div.mpIdxZe');
  $sTIdxH=fLiesHGFarb('div.mpIdxZe');
  $sAIdxL=fLiesFarbe('a.mpIdx:link'); $sAIdxA=fLiesFarbe('a.mpIdx:hover');
 }else if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['WerteForm'])){
  $sTIdxA=$_POST['TIdxA'];  if(fSetzeRahmArt($sTIdxA,'div.mpIdxZe')) $bNeu=true;
  $sTIdxR=fTxtCol('TIdxR'); if(fSetzRahmFarb($sTIdxR,'div.mpIdxZe')) $bNeu=true;
  $sTIdxH=fTxtCol('TIdxH'); if(fSetzHGFarb($sTIdxH,'div.mpIdxZe')) $bNeu=true;
  $sAIdxL=fTxtCol('AIdxL'); if(fSetzeFarbe($sAIdxL,'a.mpIdx:link')) $bNeu=true;
  $sAIdxA=fTxtCol('AIdxA'); if(fSetzeFarbe($sAIdxA,'a.mpIdx:hover')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(MPPFAD.'mpStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Meld='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Meld='<p class="admFehl">In die Datei <i>mpStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Meld) $Meld='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
 }//POST
}else $Meld.='<p class="admFehl">Setup-Fehler: Die Datei <i>mpStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Meld){$Meld='Stellen Sie die Startseite/Übersichtsseite des Marktplatz-Scripts passend ein. &nbsp; <span class="admMini" style="font-weight:normal;color:gray;">(Farbformular unten)</span>'; $MTyp='Meld';}
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;

$sIcon='iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<form name="werteform" action="konfIndex.php" method="post">
<input type="hidden" name="WerteForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über der Startseite mit der Übersicht der vorhandenen Marktsegmente
kann eine spezielle Textmeldung/Überschrift im optischen Stil der Meldung <i>p.mpMeld</i> erscheinen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Überschrift</td>
 <td><input type="text" name="TxIndexSeite" value="<?php echo $mpTxIndexSeite?>" size="90" style="width:99%" /></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Überblick über die vorhandenen Marktsegmente wird als Tabelle präsentiert.
Die Gestaltung dieser Tabelle können Sie beeinflussen.
Sollen die Marktsegmente in Zeilen nebeneinander oder in Spalten untereinander angeordnet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Segmentanordnung</td>
 <td><input class="admRadio" type="radio" name="IndexInZeilen" value="1"<?php if($mpIndexInZeilen) echo' checked="checked"'?>> nebeneinander in Zeilen&nbsp; mit <input type="text" name="IndexSpalten" value="<?php echo $mpIndexSpalten?>" size="2" style="width:32px;"> Spalten<br>
 <input class="admRadio" type="radio" name="IndexInZeilen" value="0"<?php if(!$mpIndexInZeilen) echo' checked="checked"'?>> untereinander in Spalten mit <input type="text" name="IndexZeilen" value="<?php echo $mpIndexZeilen?>" size="2" style="width:32px;"> Zeilen<br>
 <input class="admRadio" type="checkbox" name="IndexFuellen" value="1"<?php if($mpIndexFuellen) echo' checked="checked"'?>> Zeilen bzw. Spalten mit leeren Flächen optisch auf Symmetrie auffüllen
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Format jeder<br />Segmentzelle</td>
 <td>
 <div>Breite: <input type="text" name="IndexZelleW" value="<?php echo $mpIndexZelleW?>" size="4" style="width:50px;" /> (Maßeinheit px oder em mit angeben, niemals Wert <i>auto</i>)</div>
 <div>Höhe: &nbsp;<input type="text" name="IndexZelleH" value="<?php echo $mpIndexZelleH?>" size="4" style="width:50px;" /> (Maßeinheit px oder em mit angeben, niemals Wert <i>auto</i>)</div>
 <div><select name="IndexHorizontal" size="1" style="width:100px;"><option value="left<?php if($mpIndexHorizontal=='left') echo '" selected="selected' ?>">linksbündig</option><option value="center<?php if($mpIndexHorizontal=='center') echo '" selected="selected' ?>">zentriert</option><option value="right<?php if($mpIndexHorizontal=='right') echo '" selected="selected' ?>">rechtsbündig</option></select> horizontale Ausrichtung des Zelleninhaltes</div>
 <div><select name="IndexVertikal" size="1" style="width:100px;"><option value="top<?php if($mpIndexVertikal=='top') echo '" selected="selected' ?>">obenbündig</option><option value="middle<?php if($mpIndexVertikal=='middle') echo '" selected="selected' ?>">mittig</option><option value="bottom<?php if($mpIndexVertikal=='bottom') echo '" selected="selected' ?>">untenbündig</option></select> vertikale Ausrichtung des Zelleninhaltes</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Über und unter der Segmentübersicht im Besucherbereich werden normalerweise die Verweise zur Navigation (siehe <a href="konfLayout.php">Layouteinstellungen</a>) dargestellt. Auf der Startseite können diese abweichend unterdrückt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Navigationsverweise</td>
 <td><input class="admRadio" type="checkbox" name="IndexAktO" value="1"<?php if($mpIndexAktO) echo' checked="checked"'?> /> Navigationslinks oberhalb der Segmentübersicht sichtbar<br>
 <input class="admRadio" type="checkbox" name="IndexAktU" value="1"<?php if($mpIndexAktU) echo' checked="checked"'?> /> Navigationslinks unterhalb der Segmentübersicht sichtbar<br>
 <input class="admRadio" type="checkbox" name="IndexAktZ" value="1"<?php if($mpIndexAktZ) echo' checked="checked"'?> /> Zusatzseitenlinks unterhalb der Segmentübersicht sichtbar</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Über der Segmentübersicht im Besucherbereich kann ein Filter als Eingabefeldfeld für die Schnellsuche quer durch <i>alle</i> Segmente dargestellt werden. (nicht fertig)</td></tr>
<tr class="admTabl">
 <td>Schnellsuche</td>
 <td><select name="SuchQFilter" size="1">
  <option value="0"<?php if($mpSuchQFilter==0) echo ' selected="selected"'?>>Suchfilter nicht anzeigen</option>
  <option value="1"<?php if($mpSuchQFilter==1) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile über dem Navigator - linksbündig</option>
  <option value="2"<?php if($mpSuchQFilter==2) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile über dem Navigator - rechtsbündig</option>
  <option value="3"<?php if($mpSuchQFilter==3) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile unter dem Navigator - linksbündig</option>
  <option value="4"<?php if($mpSuchQFilter==4) echo ' selected="selected"'?>>Suchfilter über der Meldungszeile unter dem Navigator - rechtsbündig</option>
  <option value="5"<?php if($mpSuchQFilter==5) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile über dem Navigator - linksbündig</option>
  <option value="6"<?php if($mpSuchQFilter==6) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile über dem Navigator - rechtsbündig</option>
  <option value="7"<?php if($mpSuchQFilter==7) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile unter dem Navigator - linksbündig</option>
  <option value="8"<?php if($mpSuchQFilter==8) echo ' selected="selected"'?>>Suchfilter unter der Meldungszeile unter dem Navigator - rechtsbündig</option>
  <option value="9"<?php if($mpSuchQFilter==9) echo ' selected="selected"'?>>Suchfilter unter den Inseraten über dem Navigator - linksbündig</option>
  <option value="10"<?php if($mpSuchQFilter==10) echo ' selected="selected"'?>>Suchfilter unter den Inseraten über dem Navigator - rechtsbündig</option>
  <option value="11"<?php if($mpSuchQFilter==11) echo ' selected="selected"'?>>Suchfilter unter den Inseraten unter dem Navigator - linksbündig</option>
  <option value="12"<?php if($mpSuchQFilter==12) echo ' selected="selected"'?>>Suchfilter unter den Inseraten unter dem Navigator - rechtsbündig</option>
 </select></td>
</tr>
<tr class="admTabl">
 <td>leere Schnellsuche</td>
 <td>bei leerem Schnellsuchfeld <input class="admRadio" type="radio" name="SuchQLeer" value="0"<?php if(!$mpSuchQLeer) echo' checked="checked"'?> /> keine Inserate anzeigen &nbsp;
 <input class="admRadio" type="radio" name="SuchQLeer" value="1"<?php if($mpSuchQLeer) echo' checked="checked"'?> /> alle Inserate anzeigen
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Auf der Überblicksseite kann neben jedem Segmentnamen
das hochgeladene Segmentsinnbild (Icon) und die Anzahl der in das Segment eingetragenen Inserate erscheinen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Segmentsinnbilder</td>
 <td><input class="admRadio" type="checkbox" name="IndexIcons" value="1"<?php if($mpIndexIcons) echo' checked="checked"'?> /> Segmentsinnbilder anzeigen
 <div><input class="admRadio" type="radio" name="IndexIconLinks" value="1"<?php if($mpIndexIconLinks) echo' checked="checked"'?> /> links vom Segmentnamen&nbsp;
 <input class="admRadio" type="radio" name="IndexIconLinks" value="0"<?php if(!$mpIndexIconLinks) echo' checked="checked"'?> /> über dem Segmentnamen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inserate zählen</td>
 <td><input class="admRadio" type="checkbox" name="IndexZaehler" value="1"<?php if($mpIndexZaehler) echo' checked="checked"'?> /> Inserate zählen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Umfeld um die Übersichtstabelle kann bei eigenständigem
(nicht eingebettetem) Aufruf des Marktplatz-Scriptes aus der normalen HTML-Schablone <i>mpSeite.htm</i> stammen
oder aus einer extra HTML-Schablone <i>mpSeite1.htm</i>,
die nur für die Startseite gedacht ist und die Sie speziell gestalten können.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">HTML-Schablone</td>
 <td><input class="admRadio" type="radio" name="IndexNormal" value="1"<?php if($mpIndexNormal) echo' checked="checked"'?> /> Standard-HTML-Schablone <i>mpSeite.html</i> auch für die Startseite verwenden<br />
 <input class="admRadio" type="radio" name="IndexNormal" value="0"<?php if(!$mpIndexNormal) echo' checked="checked"'?> /> spezielle HTML-Schablone <i>mpSeite1.html</i> für die Startseite verwenden</td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>


<p style="margin-top:20px;">Die folgenden Farben und Attribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> mpStyles.css</a> editieren.</p>
<form name="farbform" action="konfIndex.php" method="post">
<input type="hidden" name="FarbForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Die Segmente in der Übersicht erhalten einen farbigen <b>Rahmen</b> und farbige Gitternetzlinien.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />und Gitternetz</td>
 <td><select name="TIdxA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTIdxA)?></select> Linien</td>
 <td><input type="text" name="TIdxR" value="<?php echo $sTIdxR?>" style="width:70px">
 <a href="<?php echo fColorRef('TIdxR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTIdxA?> <?php echo $sTIdxR?>;color:<?php echo $sTIdxR?>;background-color:<?php echo $sTIdxH?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Zellenhintergrund</b> der Segmente wird in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="TIdxH" value="<?php echo $sTIdxH?>" style="width:70px">
 <a href="<?php echo fColorRef('TIdxH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sTIdxH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2"><b>Verweise</b> zu den Marktplatzsegmenten sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="AIdxL" value="<?php echo $sAIdxL?>" style="width:70px"> <a href="<?php echo fColorRef('AIdxL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="AIdxA" value="<?php echo $sAIdxA?>" style="width:70px"> <a href="<?php echo fColorRef('AIdxA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTIdxR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAIdxL?>;background-color:<?php echo $sTIdxH?>;" onmouseover="this.style.color='<?php echo $sAIdxA?>'" onmouseout="this.style.color='<?php echo $sAIdxL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
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