<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Farbeinstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>','KFa');

$sCssDatei=UMF_CSSDatei; if(!file_exists(UMF_Pfad.$sCssDatei)) $sCssDatei='umfStyle.css';
if(file_exists(UMF_Pfad.$sCssDatei)){
 $sCss=str_replace("\r",'',trim(implode('',file(UMF_Pfad.$sCssDatei)))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['SchablonenForm'])){
  $sBUmfF=fLiesFarbe('body.umfSeite'); $sBUmfH=fLiesHintergrund('body.umfSeite');

  $sPMeld=fLiesFarbe('p.umfMeld'); $sPErfo=fLiesFarbe('p.umfErfo'); $sPFehl=fLiesFarbe('p.umfFehl');
  $sTGsmtR=fLiesRahmen('div.umfGsmt'); $sTGsmtH=fLiesHintergrund('div.umfGsmt');
  $sDTextR=fLiesRahmen('div.umfTxBl'); $sDTextH=fLiesHintergrund('div.umfTxBl');

  $sDFragF=fLiesFarbe('div.umfFrag'); $sDFragH=fLiesHintergrund('div.umfFrag');
  $sDAntwF=fLiesFarbe('div.umfAntw'); $sDAntwH=fLiesHintergrund('div.umfAntw');
  $sIAntwF=fLiesFarbe('input.umfAntw'); $sIAntwH=fLiesHintergrund('input.umfAntw');
  $sDFrNrF=fLiesFarbe('div.umfFrNr'); $sDFrNrH=fLiesHintergrund('div.umfFrNr');
  $sDAnmkF=fLiesFarbe('div.umfAnmk'); $sDAnmkH=fLiesHintergrund('div.umfAnmk');

  $sTGrafR=fLiesRahmen('table.umfGraf'); $sTGrafH=fLiesHintergrund('table.umfGraf');
  $sZGraFF=fLiesFarbe('td.umfGraF'); $sZGraFH=fLiesHintergrund('td.umfGraF');
  $sZGrASF=fLiesFarbe('td.umfGrAS'); $sZGrASH=fLiesHintergrund('td.umfGrAS');
  $sZGraEF=fLiesFarbe('td.umfGraE'); $sZGraEH=fLiesHintergrund('td.umfGraE');
                                     $sZGrGSH=fLiesHintergrund('td.umfGrGS');
  $sDFrtgR=fLiesRahmen('div.umfFrtg');  $sDFrtgF=fLiesFarbe('div.umfFrtg'); $sDFrtgH=fLiesHintergrund('div.umfFrtg');
  $sDLogiR=fLiesRahmen('td.umfLogi');  $sDLogiF=fLiesFarbe('td.umfLogi'); $sDLogiH=fLiesHintergrund('td.umfLogi');
  $sALink=fLiesFarbe('a.umfLink:link'); $sALinA=fLiesFarbe('a.umfLink:hover');
  $sDCaptF=fLiesFarbe('div.umfCapt');   $sDCaptH=fLiesHintergrund('div.umfCapt');
  $sICapAF=fLiesFarbe('input.capAnsw'); $sICapAH=fLiesHintergrund('input.capAnsw');
 }elseif($_SERVER['REQUEST_METHOD']=='POST'){
  $sBUmfF=fTxtCol('BUmfF'); if(fSetzeFarbe($sBUmfF,'body.umfSeite')) $bNeu=true;
  $sBUmfH=fTxtCol('BUmfH'); if(fSetzeHintergrund($sBUmfH,'body.umfSeite')) $bNeu=true;

  $sPMeld=fTxtCol('PMeld'); if(fSetzeFarbe($sPMeld,'p.umfMeld')) $bNeu=true; $sPErfo=fTxtCol('PErfo'); if(fSetzeFarbe($sPErfo,'p.umfErfo')) $bNeu=true; $sPFehl=fTxtCol('PFehl'); if(fSetzeFarbe($sPFehl,'p.umfFehl')) $bNeu=true;
  $sTGsmtR=fTxtCol('TGsmtR'); if(fSetzeRahmen($sTGsmtR,'div.umfGsmt')) $bNeu=true; $sTGsmtH=fTxtCol('TGsmtH'); if(fSetzeHintergrund($sTGsmtH,'div.umfGsmt')) $bNeu=true;
  $sDTextR=fTxtCol('DTextR'); if(fSetzeRahmen($sDTextR,'div.umfTxBl')) $bNeu=true; $sDTextH=fTxtCol('DTextH'); if(fSetzeHintergrund($sDTextH,'div.umfTxBl')) $bNeu=true;

  $sDFragF=fTxtCol('DFragF'); if(fSetzeFarbe($sDFragF,'div.umfFrag')) $bNeu=true; $sDFragH=fTxtCol('DFragH'); if(fSetzeHintergrund($sDFragH,'div.umfFrag')) $bNeu=true;
  $sDAntwF=fTxtCol('DAntwF'); if(fSetzeFarbe($sDAntwF,'div.umfAntw')) $bNeu=true; $sDAntwH=fTxtCol('DAntwH'); if(fSetzeHintergrund($sDAntwH,'div.umfAntw')) $bNeu=true;
  $sIAntwF=fTxtCol('IAntwF'); if(fSetzeFarbe($sIAntwF,'input.umfAntw')) $bNeu=true; $sIAntwH=fTxtCol('IAntwH'); if(fSetzeHintergrund($sIAntwH,'input.umfAntw')) $bNeu=true;
  $sDFrNrF=fTxtCol('DFrNrF'); if(fSetzeFarbe($sDFrNrF,'div.umfFrNr')) $bNeu=true; $sDFrNrH=fTxtCol('DFrNrH'); if(fSetzeHintergrund($sDFrNrH,'div.umfFrNr')) $bNeu=true;
  $sDAnmkF=fTxtCol('DAnmkF'); if(fSetzeFarbe($sDAnmkF,'div.umfAnmk')) $bNeu=true; $sDAnmkH=fTxtCol('DAnmkH'); if(fSetzeHintergrund($sDAnmkH,'div.umfAnmk')) $bNeu=true;

  $sTGrafR=fTxtCol('TGrafR'); if(fSetzeRahmen($sTGrafR,'table.umfGraf')) $bNeu=true; $sTGrafH=fTxtCol('TGrafH'); if(fSetzeHintergrund($sTGrafH,'table.umfGraf')) $bNeu=true;
  $sZGraFF=fTxtCol('ZGraFF'); if(fSetzeFarbe($sZGraFF,'td.umfGraF')) $bNeu=true; $sZGraFH=fTxtCol('ZGraFH'); if(fSetzeHintergrund($sZGraFH,'td.umfGraF')) $bNeu=true;
  $sZGrASF=fTxtCol('ZGrASF'); if(fSetzeFarbe($sZGrASF,'td.umfGrAS')) $bNeu=true; $sZGrASH=fTxtCol('ZGrASH'); if(fSetzeHintergrund($sZGrASH,'td.umfGrAS')) $bNeu=true;
  $sZGraEF=fTxtCol('ZGraEF'); if(fSetzeFarbe($sZGraEF,'td.umfGraE')) $bNeu=true; $sZGraEH=fTxtCol('ZGraEH'); if(fSetzeHintergrund($sZGraEH,'td.umfGraE')) $bNeu=true;
                                                                                 $sZGrGSH=fTxtCol('ZGrGSH'); if(fSetzeHintergrund($sZGrGSH,'td.umfGrGS')) $bNeu=true;
  $sDFrtgR=fTxtCol('DFrtgR'); if(fSetzeRahmen($sDFrtgR,'div.umfFrtg'))$bNeu=true;
  $sDFrtgF=fTxtCol('DFrtgF'); if(fSetzeFarbe($sDFrtgF,'div.umfFrtg')) $bNeu=true; $sDFrtgH=fTxtCol('DFrtgH'); if(fSetzeHintergrund($sDFrtgH,'div.umfFrtg')) $bNeu=true;

  $sDLogiR=fTxtCol('DLogiR'); if(fSetzeRahmen($sDLogiR,'td.umfLogi'))$bNeu=true;
  $sDLogiF=fTxtCol('DLogiF'); if(fSetzeFarbe($sDLogiF,'td.umfLogi')) $bNeu=true; $sDLogiH=fTxtCol('DLogiH'); if(fSetzeHintergrund($sDLogiH,'td.umfLogi')) $bNeu=true;

  $sALink=fTxtCol('ALink'); if(fSetzeFarbe($sALink,'a.umfLink:link')) $bNeu=true; $sALinA=fTxtCol('ALinA'); if(fSetzeFarbe($sALinA,'a.umfLink:hover')) $bNeu=true;

  $sDCaptF=fTxtCol('DCaptF'); if(fSetzeFarbe($sDCaptF,'div.umfCapt')) $bNeu=true;   $sDCaptH=fTxtCol('DCaptH'); if(fSetzeHintergrund($sDCaptH,'div.umfCapt')) $bNeu=true;
  $sICapAF=fTxtCol('ICapAF'); if(fSetzeFarbe($sICapAF,'input.capAnsw')) $bNeu=true; $sICapAH=fTxtCol('ICapAH'); if(fSetzeHintergrund($sICapAH,'input.capAnsw')) $bNeu=true;

  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.$sCssDatei,'w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $sMeld=fMErfo('Die geänderten Farbeinstellungen wurden gespeichert.');
   }else $sMeld=fMFehl('In die Datei <i>'.$sCssDatei.'</i> durfte nicht geschrieben werden!');
  }else if(!$sMeld) $sMeld=fMMeld('Die Farbeinstellungen bleiben unverändert.');
 }//POST
}else $sMeld.=fMFehl('Setup-Fehler: Die Datei <i>'.$sCssDatei.'</i> im Programmverzeichnis kann nicht gelesen werden!');

//Seitenausgabe
if(!$sMeld) $sMeld=fMMeld('Kontrollieren oder ändern Sie die wesentlichsten Farbeinstellungen.');
echo $sMeld.NL;
if($sCssDatei!=UMF_CSSDatei) echo fMFehl('Die unter <a href="konfLayout.php'.(KONF?'?konf='.KONF:'').'">Layouteinstellung</a> angegebene Datei <i>'.UMF_CSSDatei.'</i> ist nicht verfügbar. Es wird <i>'.$sCssDatei.'</i> verwendet!');
?>

<p style="margin-top:12px;"><u>Hinweis</u>: Die folgenden Farben und anderen Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php<?php if(KONF) echo '?konf='.KONF ?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> <?php echo $sCssDatei?></a> editieren.</p>
<form name="farbform" action="konfFarben.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Hintergrundfarbe und Textfarbe soll der Seitenhintergrund bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Seitenfarbe</td>
 <td><?php echo fFarbFeld('BUmfF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('BUmfH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sBUmfF,$sBUmfH,'#CCCCCC')?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Über den Formularen und Listen der Umfrage-Scripts werden Meldungstexte angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Meldungstextfarbe</td>
 <td colspan="2"><?php echo fFarbFeld('PMeld')?></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPMeld?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #000000 (schwarz)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Erfolgstextfarbe</td>
 <td colspan="2"><?php echo fFarbFeld('PErfo')?></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPErfo?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #008800 (grün)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlertextfarbe</td>
 <td colspan="2"><?php echo fFarbFeld('PFehl')?></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPFehl?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #BB0033 (rot)</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Hintergrundfarbe und Rahmenfarbe soll der Gesamtcontainer für den Bildblock und dem Textblock bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Gesamtcontainer</td>
 <td><?php echo fFarbFeld('TGsmtR')?> Rahmen</td>
 <td><?php echo fFarbFeld('TGsmtH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTGsmtR,$sTGsmtH,$sTGsmtR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Hintergrundfarbe und Rahmenfarbe soll der Textblock mit den Fragen und Antworten bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Textblock gesamt</td>
 <td><?php echo fFarbFeld('DTextR')?> Rahmen</td>
 <td><?php echo fFarbFeld('DTextH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sDTextR,$sDTextH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Textfarben und Hintergrundfarben sollen die einzelnen Zeilen im Textblock bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenzeile</td>
 <td><?php echo fFarbFeld('DFragF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('DFragH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sDFragF,$sDFragH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortzeilen</td>
 <td><?php echo fFarbFeld('DAntwF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('DAntwH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sDAntwF,$sDAntwH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortkästchen</td>
 <td><?php echo fFarbFeld('IAntwF')?> Auswahlfarbe</td>
 <td><?php echo fFarbFeld('IAntwH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sIAntwF,$sIAntwH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeile mit der<br />Fragennummer</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DFrNrF')?> Textfarbe</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DFrNrH')?> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><?php echo fMusterFeld($sDFrNrF,$sDFrNrH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkungszeile</td>
 <td><?php echo fFarbFeld('DAnmkF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('DAnmkH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sDAnmkF,$sDAnmkH,$sDTextR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Farben sollen für die Tabelle mit dem Säulendiagramm/Balkendiagramm verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Diagrammtabelle</td>
 <td><?php echo fFarbFeld('TGrafR')?> Rahmen</td>
 <td><?php echo fFarbFeld('TGrafH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTGrafR,$sTGrafH,$sTGrafR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenzeile</td>
 <td><?php echo fFarbFeld('ZGraFF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('ZGraFH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sZGraFF,$sZGraFH,$sTGrafR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortzellen</td>
 <td><?php echo fFarbFeld('ZGrASF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('ZGrASH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sZGrASF,$sZGrASH,$sTGrafR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebniszellen</td>
 <td><?php echo fFarbFeld('ZGraEF')?> Textfarbe</td>
 <td><?php echo fFarbFeld('ZGraEH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sZGraEF,$sZGraEH,$sTGrafR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Balkenzelle</td>
 <td style="vertical-align:middle">siehe balken.gif und saeule.gif</td>
 <td><?php echo fFarbFeld('ZGrGSH')?> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld('#6666AA',$sZGrGSH,$sTGrafR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Farben sollen für den Container mit dem <i>Fertig</i>-Text verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzellen</td>
 <td><?php echo fFarbFeld('DFrtgR')?> Rahmen
 <br><?php echo fFarbFeld('DFrtgF')?> Textfarbe</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DFrtgH')?> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><?php echo fMusterFeld($sDFrtgF,$sDFrtgH,$sDFrtgR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Link <i>[zur&nbsp;Grafik]</i></td>
 <td><?php echo fFarbFeld('ALink')?> (normal)</td>
 <td><?php echo fFarbFeld('ALinA')?> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sALink,$sALinA)?></td>
 <td class="admMini" style="vertical-align:middle">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Farben sollen für alle sonstigen Tabellen besonders die Login-Formulare oder die Benutzermenü-Tabelle verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzellen</td>
 <td><?php echo fFarbFeld('DLogiR')?> Rahmen
 <br><?php echo fFarbFeld('DLogiF')?> Textfarbe</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DLogiH')?> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><?php echo fMusterFeld($sDLogiF,$sDLogiH,$sDLogiR)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Farben sollen für den Container mit dem Captcha verwendet werden? Die Farben für die Captcha-Grafik selbst sind nicht in der CSS-Style-Datei sondern unter <i>Allgemeines</i> einstellbar.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Capcha-Abschnitt<br />auf der Fragenseite</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DCaptF')?> Textfarbe</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('DCaptH')?> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><?php echo fMusterFeld($sDCaptF,$sDCaptH)?></td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefeld<br />für Captcha-Code</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('ICapAF')?> Textfarbe</td>
 <td style="vertical-align:middle"><?php echo fFarbFeld('ICapAH')?> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><?php echo fMusterFeld($sICapAF,$sICapAH)?></td>
 <td class="admMini" style="vertical-align:middle">Empfehlung: schwarz/weiß</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
<p style="margin-top:16px;"><u>Hinweis</u>: Die Hintergrundfarbe des gesamten Umfrage-Scripts ist in der HTML-Vorlagenschablone <i>umfSeite.htm</i> bestimmt.</p>
<p style="margin-top:3px;padding-left:52px;">Die Farben für die Captcha-Grafik sind in der Administration unter <i>Allgemeines</i> einstellbar.</p>

<?php
echo fSeitenFuss();

function fLiesFarbe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+9); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+6||$p==$q+7)&&$e>$p) return substr($sCss,$p,7); else return false;
  }else return false;
 }else return false;
}
function fLiesHintergrund($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+17||$p==$q+18)&&$e>$p) return substr($sCss,$p,7); else return false;
  }else return false;
 }else return false;
}
function fLiesRahmen($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-color',$p); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+13||$p==$q+14)&&$e>$p) return substr($sCss,$p,7); else return false;
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
function fLiesHoehe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'height',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFarbe($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+9); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+6||$p==$q+7)&&$e>$p){
    if(substr($sCss,$p,7)!=$v){$sCss=substr_replace($sCss,$v.';',$p,8); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeHintergrund($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+17||$p==$q+18)&&$e>$p){
    if(substr($sCss,$p,7)!=$v){$sCss=substr_replace($sCss,$v.';',$p,8); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmen($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-color',$p); $p=strpos($sCss,'#',$q);
   if($q>0&&($p==$q+13||$p==$q+14)&&$e>$p){
    if(substr($sCss,$p,7)!=$v){$sCss=substr_replace($sCss,$v.';',$p,8); return true;}else return false;
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
function fSetzeHoehe($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'height',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fFarbFeld($n){return '<input type="text" name="'.$n.'" value="'.$GLOBALS['s'.$n].'" style="width:70px"> <a href="colors.php?col='.substr($GLOBALS['s'.$n],1).'&fld='.$n.'" target="color" onClick="javascript:ColWin()"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>';}
function fMusterLink($n,$h,$r='#FFFFFF'){return '<table bgcolor="'.$r.'" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:#F7F7F7;" onmouseover="this.style.color='."'".$h."'".'" onmouseout="this.style.color='."'".$n."'".'">&nbsp;Muster&nbsp;</td></tr></table>';}
function fMusterFeld($n='#999999',$h='#CCCCCC',$r='#FFFFFF'){return '<table bgcolor="'.$r.'" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:'.$h.';">&nbsp;Muster&nbsp;</td></tr></table>';}
function fTxtCol($Var){
 $s=strtoupper(str_replace('"',"'",stripslashes(trim($_POST[$Var]))));
 if(substr($s,0,1)!='#') $s='#'.$s; if(strlen($s)>7) $s=substr($s,0,7); else while(strlen($s)<7) $s.='0';
 return $s;
}
function fTxtSiz($Var){return strtolower(str_replace('"',"'",str_replace(' ','',stripslashes(trim($_POST[$Var])))));}
?>