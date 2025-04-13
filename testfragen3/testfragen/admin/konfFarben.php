<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Farbeinstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>','KFa');

$sCssDatei=FRA_CSSDatei; if(!file_exists(FRA_Pfad.$sCssDatei)) $sCssDatei='fraStyle.css';
if(file_exists(FRA_Pfad.$sCssDatei)){
 $sCss=str_replace("\r",'',trim(implode('',file(FRA_Pfad.$sCssDatei)))); $bNeu=false;
 if($_SERVER['REQUEST_METHOD']=='GET'||isset($_POST['SchablonenForm'])){
  $sPMeld=fLiesFarbe('p.fraMeld'); $sPErfo=fLiesFarbe('p.fraErfo'); $sPFehl=fLiesFarbe('p.fraFehl');
  $sTGsmtR=fLiesRahmen('table.fraGsmt'); $sTGsmtH=fLiesHintergrund('table.fraGsmt');
  $sDTextR=fLiesRahmen('div.fraText'); $sDTextH=fLiesHintergrund('div.fraText');

  $sDFragF=fLiesFarbe('div.fraFrag'); $sDFragH=fLiesHintergrund('div.fraFrag');
  $sDAntwF=fLiesFarbe('div.fraAntw'); $sDAntwH=fLiesHintergrund('div.fraAntw');
  $sIAntwF=fLiesFarbe('input.fraAntw'); $sIAntwH=fLiesHintergrund('input.fraAntw');
  $sDAuslF=fLiesFarbe('div.fraAusl'); $sDAuslH=fLiesHintergrund('div.fraAusl');
  $sDKatgF=fLiesFarbe('div.fraKatg'); $sDKatgH=fLiesHintergrund('div.fraKatg');
  $sDFrNrF=fLiesFarbe('div.fraFrNr'); $sDFrNrH=fLiesHintergrund('div.fraFrNr');
  $sDInfoF=fLiesFarbe('div.fraInfo'); $sDInfoH=fLiesHintergrund('div.fraInfo');
  $sDAnmkF=fLiesFarbe('div.fraAnmk'); $sDAnmkH=fLiesHintergrund('div.fraAnmk');

  $sTMenuR=fLiesRahmen('table.fraMenu'); $sTMenuH=fLiesHintergrund('table.fraMenu');
  $sZMenuR=fLiesRahmen('td.fraMenu'); $sZMenuF=fLiesFarbe('td.fraMenu'); $sZMenuH=fLiesHintergrund('td.fraMenu');
  $sAMenu=fLiesFarbe('a.fraMenu:visited'); $sAMnuA=fLiesFarbe('a.fraMenu:hover');

  $sTBwrtR=fLiesRahmen('table.fraBwrt'); $sTBwrtH=fLiesHintergrund('table.fraBwrt');
  $sZBwrtR=fLiesRahmen('td.fraBwrt'); $sZBwrtF=fLiesFarbe('td.fraBwrt'); $sZBwrtH=fLiesHintergrund('td.fraBwrt');

  $sTLogiR=fLiesRahmen('table.fraLogi'); $sTLogiH=fLiesHintergrund('table.fraLogi');
  $sZLogiR=fLiesRahmen('td.fraLogi'); $sZLogiF=fLiesFarbe('td.fraLogi'); $sZLogiH=fLiesHintergrund('td.fraLogi');
  $sILogiF=fLiesFarbe('input.fraLogi'); $sILogiH=fLiesHintergrund('input.fraLogi');

 }elseif($_SERVER['REQUEST_METHOD']=='POST'){
  $sPMeld=fTxtCol('PMeld'); if(fSetzeFarbe($sPMeld,'p.fraMeld')) $bNeu=true; $sPErfo=fTxtCol('PErfo'); if(fSetzeFarbe($sPErfo,'p.fraErfo')) $bNeu=true; $sPFehl=fTxtCol('PFehl'); if(fSetzeFarbe($sPFehl,'p.fraFehl')) $bNeu=true;
  $sTGsmtR=fTxtCol('TGsmtR'); if(fSetzeRahmen($sTGsmtR,'table.fraGsmt')) $bNeu=true; $sTGsmtH=fTxtCol('TGsmtH'); if(fSetzeHintergrund($sTGsmtH,'table.fraGsmt')) $bNeu=true;
  $sDTextR=fTxtCol('DTextR'); if(fSetzeRahmen($sDTextR,'div.fraText')) $bNeu=true; $sDTextH=fTxtCol('DTextH'); if(fSetzeHintergrund($sDTextH,'div.fraText')) $bNeu=true;

  $sDFragF=fTxtCol('DFragF'); if(fSetzeFarbe($sDFragF,'div.fraFrag')) $bNeu=true; $sDFragH=fTxtCol('DFragH'); if(fSetzeHintergrund($sDFragH,'div.fraFrag')) $bNeu=true;
  $sDAntwF=fTxtCol('DAntwF'); if(fSetzeFarbe($sDAntwF,'div.fraAntw')) $bNeu=true; $sDAntwH=fTxtCol('DAntwH'); if(fSetzeHintergrund($sDAntwH,'div.fraAntw')) $bNeu=true;
  $sIAntwF=fTxtCol('IAntwF'); if(fSetzeFarbe($sIAntwF,'input.fraAntw')) $bNeu=true; $sIAntwH=fTxtCol('IAntwH'); if(fSetzeHintergrund($sIAntwH,'input.fraAntw')) $bNeu=true;
  $sDAuslF=fTxtCol('DAuslF'); if(fSetzeFarbe($sDAuslF,'div.fraAusl')) $bNeu=true; $sDAuslH=fTxtCol('DAuslH'); if(fSetzeHintergrund($sDAuslH,'div.fraAusl')) $bNeu=true;
  $sDKatgF=fTxtCol('DKatgF'); if(fSetzeFarbe($sDKatgF,'div.fraKatg')) $bNeu=true; $sDKatgH=fTxtCol('DKatgH'); if(fSetzeHintergrund($sDKatgH,'div.fraKatg')) $bNeu=true;
  $sDFrNrF=fTxtCol('DFrNrF'); if(fSetzeFarbe($sDFrNrF,'div.fraFrNr')) $bNeu=true; $sDFrNrH=fTxtCol('DFrNrH'); if(fSetzeHintergrund($sDFrNrH,'div.fraFrNr')) $bNeu=true;
  $sDInfoF=fTxtCol('DInfoF'); if(fSetzeFarbe($sDInfoF,'div.fraInfo')) $bNeu=true; $sDInfoH=fTxtCol('DInfoH'); if(fSetzeHintergrund($sDInfoH,'div.fraInfo')) $bNeu=true;
  $sDAnmkF=fTxtCol('DAnmkF'); if(fSetzeFarbe($sDAnmkF,'div.fraAnmk')) $bNeu=true; $sDAnmkH=fTxtCol('DAnmkH'); if(fSetzeHintergrund($sDAnmkH,'div.fraAnmk')) $bNeu=true;

  $sTMenuR=fTxtCol('TMenuR'); if(fSetzeRahmen($sTMenuR,'table.fraMenu')) $bNeu=true; $sTMenuH=fTxtCol('TMenuH'); if(fSetzeHintergrund($sTMenuH,'table.fraMenu')) $bNeu=true;
  $sZMenuR=fTxtCol('ZMenuR'); if(fSetzeRahmen($sZMenuR,'td.fraMenu')){$bNeu=true; fSetzeRahmen($sZMenuR,'td.fraMnuL'); fSetzeRahmen($sZMenuR,'td.fraMnuM'); fSetzeRahmen($sZMenuR,'td.fraMnuR');}
  $sZMenuF=fTxtCol('ZMenuF'); if(fSetzeFarbe($sZMenuF,'td.fraMenu')) $bNeu=true; $sZMenuH=fTxtCol('ZMenuH'); if(fSetzeHintergrund($sZMenuH,'td.fraMenu')) $bNeu=true;
  $sAMenu=fTxtCol('AMenu'); if(fSetzeFarbe($sAMenu,'a.fraMenu:visited')) $bNeu=true; $sAMnuA=fTxtCol('AMnuA'); if(fSetzeFarbe($sAMnuA,'a.fraMenu:hover')) $bNeu=true;

  $sTBwrtR=fTxtCol('TBwrtR'); if(fSetzeRahmen($sTBwrtR,'table.fraBwrt')) $bNeu=true; $sTBwrtH=fTxtCol('TBwrtH'); if(fSetzeHintergrund($sTBwrtH,'table.fraBwrt')) $bNeu=true;
  $sZBwrtR=fTxtCol('ZBwrtR'); if(fSetzeRahmen($sZBwrtR,'td.fraBwrt')) $bNeu=true;
  $sZBwrtF=fTxtCol('ZBwrtF'); if(fSetzeFarbe($sZBwrtF,'td.fraBwrt')) $bNeu=true; $sZBwrtH=fTxtCol('ZBwrtH'); if(fSetzeHintergrund($sZBwrtH,'td.fraBwrt')) $bNeu=true;

  $sTLogiR=fTxtCol('TLogiR'); if(fSetzeRahmen($sTLogiR,'table.fraLogi')) $bNeu=true; $sTLogiH=fTxtCol('TLogiH'); if(fSetzeHintergrund($sTLogiH,'table.fraLogi')) $bNeu=true;
  $sZLogiR=fTxtCol('ZLogiR'); if(fSetzeRahmen($sZLogiR,'td.fraLogi')) $bNeu=true;
  $sZLogiF=fTxtCol('ZLogiF'); if(fSetzeFarbe($sZLogiF,'td.fraLogi')) $bNeu=true; $sZLogiH=fTxtCol('ZLogiH'); if(fSetzeHintergrund($sZLogiH,'td.fraLogi')) $bNeu=true;
  $sILogiF=fTxtCol('ILogiF'); if(fSetzeFarbe($sILogiF,'input.fraLogi')) $bNeu=true; $sILogiH=fTxtCol('ILogiH'); if(fSetzeHintergrund($sILogiH,'input.fraLogi')) $bNeu=true;

  if($bNeu){//Speichern
   if($f=fopen(FRA_Pfad.$sCssDatei,'w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $sMeld='<p class="admErfo">Die geänderten Farbeinstellungen wurden gespeichert.</p>';
   }else $sMeld='<p class="admFehl">In die Datei <i>'.$sCssDatei.'</i> konnte nicht geschrieben werden!</p>';
  }else if(!$sMeld) $sMeld='<p class="admMeld">Die Farbeinstellungen bleiben unverändert.</p>';
 }//POST
}else $sMeld.='<p class="admFehl">Setup-Fehler: Die Datei <i>'.$sCssDatei.'</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie die wesentlichen Farbeinstellungen.</p>';
echo $sMeld.NL;
?>

<p class="admMini" style="margin-top:12px;"><u>Hinweis</u>: Die folgenden Farben und anderen Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php<?php if(KONF) echo '?konf='.KONF ?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> <?php echo $sCssDatei ?></a> editieren.</p>
<form name="farbform" action="konfFarben.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Über den Formularen und Listen der Testfragen-Scripts werden Meldungstexte angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:1%">Meldungstextfarbe</td>
 <td colspan="2"><input type="text" name="PMeld" value="<?php echo $sPMeld?>" style="width:70px">
 <a href="<?php echo fColorRef('PMeld')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPMeld?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #000000 (schwarz)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Erfolgstextfarbe</td>
 <td colspan="2"><input type="text" name="PErfo" value="<?php echo $sPErfo?>" style="width:70px">
 <a href="<?php echo fColorRef('PErfo')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPErfo?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #008800 (grün)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlertextfarbe</td>
 <td colspan="2"><input type="text" name="PFehl" value="<?php echo $sPFehl?>" style="width:70px">
 <a href="<?php echo fColorRef('PFehl')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPFehl?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #BB0033 (rot)</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">1.) Frage-Antwort-Seiten: Welche Hintergrundfarbe und Rahmenfarbe soll die Gesamttabelle mit dem Bild und dem Container für den Textblock bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Gesamttabelle</td>
 <td><input type="text" name="TGsmtR" value="<?php echo $sTGsmtR?>" style="width:70px"> <a href="<?php echo fColorRef('TGsmtR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen</td>
 <td><input type="text" name="TGsmtH" value="<?php echo $sTGsmtH?>" style="width:70px"> <a href="<?php echo fColorRef('TGsmtH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sTGsmtR?>;background-color:<?php echo $sTGsmtH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Hintergrundfarbe und Rahmenfarbe soll der Textblock mit den Fragen und Antworten bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Textblock gesamt</td>
 <td><input type="text" name="DTextR" value="<?php echo $sDTextR?>" style="width:70px"> <a href="<?php echo fColorRef('DTextR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen</td>
 <td><input type="text" name="DTextH" value="<?php echo $sDTextH?>" style="width:70px"> <a href="<?php echo fColorRef('DTextH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sDTextR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDTextR?>;background-color:<?php echo $sDTextH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Welche Textfarben und Hintergrundfarben sollen die einzelnen Zeilen im Textblock bekommen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenzeile</td>
 <td><input type="text" name="DFragF" value="<?php echo $sDFragF?>" style="width:70px"> <a href="<?php echo fColorRef('DFragF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="DFragH" value="<?php echo $sDFragH?>" style="width:70px"> <a href="<?php echo fColorRef('DFragH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDFragF?>;background-color:<?php echo $sDFragH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortzeilen</td>
 <td><input type="text" name="DAntwF" value="<?php echo $sDAntwF?>" style="width:70px"> <a href="<?php echo fColorRef('DAntwF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="DAntwH" value="<?php echo $sDAntwH?>" style="width:70px"> <a href="<?php echo fColorRef('DAntwH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDAntwF?>;background-color:<?php echo $sDAntwH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortkästchen</td>
 <td><input type="text" name="IAntwF" value="<?php echo $sIAntwF?>" style="width:70px"> <a href="<?php echo fColorRef('IAntwF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Auswahlfarbe</td>
 <td><input type="text" name="IAntwH" value="<?php echo $sIAntwH?>" style="width:70px"> <a href="<?php echo fColorRef('IAntwH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sIAntwF?>;background-color:<?php echo $sIAntwH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Auslassungszeile</td>
 <td><input type="text" name="DAuslF" value="<?php echo $sDAuslF?>" style="width:70px"> <a href="<?php echo fColorRef('DAuslF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="DAuslH" value="<?php echo $sDAuslH?>" style="width:70px"> <a href="<?php echo fColorRef('DAuslH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDAuslF?>;background-color:<?php echo $sDAuslH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kategoriezeile</td>
 <td><input type="text" name="DKatgF" value="<?php echo $sDKatgF?>" style="width:70px"> <a href="<?php echo fColorRef('DKatgF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="DKatgH" value="<?php echo $sDKatgH?>" style="width:70px"> <a href="<?php echo fColorRef('DKatgH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDKatgF?>;background-color:<?php echo $sDKatgH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zeile mit der<br>Fragennummer</td>
 <td style="vertical-align:middle"><input type="text" name="DFrNrF" value="<?php echo $sDFrNrF?>" style="width:70px"> <a href="<?php echo fColorRef('DFrNrF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="DFrNrH" value="<?php echo $sDFrNrH?>" style="width:70px"> <a href="<?php echo fColorRef('DFrNrH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDFrNrF?>;background-color:<?php echo $sDFrNrH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Informationszeile<br>mit Punktezahl,<br>Antwortzahl usw.</td>
 <td style="vertical-align:middle"><input type="text" name="DInfoF" value="<?php echo $sDInfoF?>" style="width:70px"> <a href="<?php echo fColorRef('DInfoF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="DInfoH" value="<?php echo $sDInfoH?>" style="width:70px"> <a href="<?php echo fColorRef('DInfoH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDInfoF?>;background-color:<?php echo $sDInfoH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkungszeile</td>
 <td><input type="text" name="DAnmkF" value="<?php echo $sDAnmkF?>" style="width:70px"> <a href="<?php echo fColorRef('DAnmkF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="DAnmkH" value="<?php echo $sDAnmkH?>" style="width:70px"> <a href="<?php echo fColorRef('DAnmkH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTGsmtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sDAnmkF?>;background-color:<?php echo $sDAnmkH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">2.) Login-Formular: Welche Farben sollen für die Tabelle des Registrierungs-, Anmelde- und Loginformulars verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulartabelle</td>
 <td><input type="text" name="TLogiR" value="<?php echo $sTLogiR?>" style="width:70px"> <a href="<?php echo fColorRef('TLogiR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen</td>
 <td><input type="text" name="TLogiH" value="<?php echo $sTLogiH?>" style="width:70px"> <a href="<?php echo fColorRef('TLogiH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTLogiR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sTLogiR?>;background-color:<?php echo $sTLogiH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzellen</td>
 <td><input type="text" name="ZLogiR" value="<?php echo $sZLogiR?>" style="width:70px"> <a href="<?php echo fColorRef('ZLogiR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen
 <br><input type="text" name="ZLogiF" value="<?php echo $sZLogiF?>" style="width:70px"> <a href="<?php echo fColorRef('ZLogiF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="ZLogiH" value="<?php echo $sZLogiH?>" style="width:70px"> <a href="<?php echo fColorRef('ZLogiH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sZLogiR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZLogiF?>;background-color:<?php echo $sZLogiH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabefelder</td>
 <td><input type="text" name="ILogiF" value="<?php echo $sILogiF?>" style="width:70px"> <a href="<?php echo fColorRef('ILogiF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td><input type="text" name="ILogiH" value="<?php echo $sILogiH?>" style="width:70px"> <a href="<?php echo fColorRef('ILogiH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sZLogiR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sILogiF?>;background-color:<?php echo $sILogiH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">3.) Benutzer-Zentrum: Welche Farben sollen für die Tabelle des Benutzermenüs verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Menütabelle</td>
 <td><input type="text" name="TMenuR" value="<?php echo $sTMenuR?>" style="width:70px"> <a href="<?php echo fColorRef('TMenuR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen</td>
 <td><input type="text" name="TMenuH" value="<?php echo $sTMenuH?>" style="width:70px"> <a href="<?php echo fColorRef('TMenuH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTMenuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sTMenuR?>;background-color:<?php echo $sTMenuH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Menüzellen<div>mit Text</div></td>
 <td><input type="text" name="ZMenuR" value="<?php echo $sZMenuR?>" style="width:70px"> <a href="<?php echo fColorRef('ZMenuR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen
 <br><input type="text" name="ZMenuF" value="<?php echo $sZMenuF?>" style="width:70px"> <a href="<?php echo fColorRef('ZMenuF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="ZMenuH" value="<?php echo $sZMenuH?>" style="width:70px"> <a href="<?php echo fColorRef('ZMenuH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sZMenuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZMenuF?>;background-color:<?php echo $sZMenuH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">[ OK ] - Link auf<div>den Schaltflächen</div></div></td>
 <td style="vertical-align:middle"><input type="text" name="AMenu" value="<?php echo $sAMenu?>" style="width:70px"> <a href="<?php echo fColorRef('AMenu')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Normalfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="AMnuA" value="<?php echo $sAMnuA?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuA')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Aktivfarbe</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sTMenuR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAMenu?>;background-color:<?php echo $sZMenuH?>;" onmouseover="this.style.color='<?php echo $sAMnuA?>'" onmouseout="this.style.color='<?php echo $sAMenu?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">4.) Bewertungsseiten: Welche Farben sollen für die Tabelle der Bewertungsseite verwendet werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Auswertungstabelle</td>
 <td><input type="text" name="TBwrtR" value="<?php echo $sTBwrtR?>" style="width:70px"> <a href="<?php echo fColorRef('TBwrtR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen</td>
 <td><input type="text" name="TBwrtH" value="<?php echo $sTBwrtH?>" style="width:70px"> <a href="<?php echo fColorRef('TBwrtH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center"><table bgcolor="<?php echo $sTBwrtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sTBwrtR?>;background-color:<?php echo $sTBwrtH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenzellen</td>
 <td><input type="text" name="ZBwrtR" value="<?php echo $sZBwrtR?>" style="width:70px"> <a href="<?php echo fColorRef('ZBwrtR')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Rahmen
 <br><input type="text" name="ZBwrtF" value="<?php echo $sZBwrtF?>" style="width:70px"> <a href="<?php echo fColorRef('ZBwrtF')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Textfarbe</td>
 <td style="vertical-align:middle"><input type="text" name="ZBwrtH" value="<?php echo $sZBwrtH?>" style="width:70px"> <a href="<?php echo fColorRef('ZBwrtH')?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> Hintergrund</td>
 <td align="center" style="vertical-align:middle"><table bgcolor="<?php echo $sZBwrtR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sZBwrtF?>;background-color:<?php echo $sZBwrtH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" style="vertical-align:middle">keine Empfehlung</td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
<p style="margin-top:16px;"><u>Hinweis</u>: Die Hintergrundfarbe des gesamten Testfragen-Scripts ist in der HTML-Vorlagenschablone <i>fraSeite.htm</i> bestimmt.</p>
<p style="padding-left:58px;">Die Farben für das Captcha sind in der Administration unter <i>Allgemeines</i> einstellbar.</p>

<?php
echo fSeitenFuss();

function fLiesFarbe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  while($n=strpos($sCss,$w,$p+1)) $p=$n;
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+9); $p=strpos($sCss,'#',$q);
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,7); else return false;
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
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,7); else return false;
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
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,7); else return false;
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
   if($q>0&&$p>$q&&$e>$p){
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
   if($q>0&&$p>$q&&$e>$p){
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
   if($q>0&&$p>$q&&$e>$p){
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
function fColorRef($n){return 'colors.php?col='.substr($GLOBALS['s'.$n],1).'&fld='.$n.'" target="color" onClick="javascript:ColWin()';}
function fMusterLink($n,$h){return '<table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:#F7F7F7;" onmouseover="this.style.color='."'".$h."'".'" onmouseout="this.style.color='."'".$n."'".'">&nbsp;Muster&nbsp;</td></tr></table>';}
function fMusterFeld($n,$h){return '<table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:'.$h.';">&nbsp;Muster&nbsp;</td></tr></table>';}
function fTxtCol($Var){
 $s=(isset($_POST[$Var])?strtoupper(str_replace('"',"'",stripslashes(trim($_POST[$Var])))):'');
 if(substr($s,0,1)!='#') $s='#'.$s; while(strlen($s)<7) $s.='0';
 return $s;
}
function fTxtSiz($Var){return (isset($_POST[$Var])?strtolower(str_replace('"',"'",str_replace(' ','',stripslashes(trim($_POST[$Var]))))):'');}
?>