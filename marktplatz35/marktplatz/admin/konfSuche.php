<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Suchformular kofigurieren','','KSf');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für das Suchformular. <a href="'.AM_Hilfe.'LiesMich.htm#2.9" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpTxSuchMeld=MP_TxSuchMeld; $mpSuchSpalten=MP_SuchSpalten; $mpSuchArchiv=MP_SuchArchiv; $mpSuchSortiert=MP_SuchSortiert;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $s=txtVar('TxSuchMeld'); if(fSetzMPWert($s,'TxSuchMeld',"'")) $bNeu=true;
 $s=(int)txtVar('SuchSpalten'); if(fSetzMPWert($s,'SuchSpalten','')) $bNeu=true;
 $s=(int)txtVar('SuchArchiv'); if(fSetzMPWert(($s?true:false),'SuchArchiv','')) $bNeu=true;
 $s=(int)txtVar('SuchSortiert'); if(fSetzMPWert(($s?true:false),'SuchSortiert','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Sucheinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Sucheinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form action="konfSuche.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über dem Suchformular für Inserate wird Besuchern folgende Aufforderungsmeldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aufforderung</td>
 <td><input type="text" name="TxSuchMeld" value="<?php echo $mpTxSuchMeld?>" style="width:99%" /><div class="admMini">Empfehlung: <i>Suchen Sie im Segment #S!</i> &nbsp; (#S ist ein Platzhalter für den Segmentnamen)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular kann jedes zu suchende Feld mehrmals enthalten. Es kann einspaltig, zweispaltig oder dreispaltig sein.
Bei zwei Spalten pro Feld wird das Formular interpretiert als '<i>wie Suchbegriff-1 oder wie Suchbegriff-2</i>' bzw. als '<i>von Suchwert-1 bis Suchwert-2</i>'.
Bei drei Spalten ist die Interpretation '<i>wie Suchbegriff-1 oder wie Suchbegriff-2 aber nicht wie Suchbegriff-3</i>'.
<div>Welche Datenfelder überhaupt als Suchfeld benutzt werden stellen Sie für das jeweilige Segment unter <i>Segmenteigenschaften</i> ein.</div>
</td></tr>
<tr class="admTabl">
 <td>Suchfelder</td>
 <td><select name="SuchSpalten" size="1"><option value="1<?php if($mpSuchSpalten==1) echo '" selected="selected';?>">1</option><option value="2<?php if($mpSuchSpalten==2) echo '" selected="selected';?>">2</option><option value="3<?php if($mpSuchSpalten==3) echo '" selected="selected';?>">3</option></select> Spalten &nbsp; <span class="admMini">Empfehlung: <i>2 Spalten</i></span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular kann zusätzlich eine Auswahlmöglichkeit für die Auswahl einer Sortierfolge der Suchergebnisliste enthalten.</td></tr>
<tr class="admTabl">
 <td>Sortierfolge</td>
 <td><input type="radio" class="admRadio" name="SuchSortiert" value="1"<?php if($mpSuchSortiert) echo' checked="checked"'?>" />einblenden &nbsp; &nbsp; <input type="radio" class="admRadio" name="SuchSortiert" value="0"<?php if(!$mpSuchSortiert) echo' checked="checked"'?>" />ausblenden</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Suchformular kann zusätzlich eine Auswahlmöglichkeit für die Suche im Inseratearchiv (abgelaufene und ausgebelendete, aber noch nicht gelöschte Inserate) enthalten.</td></tr>
<tr class="admTabl">
 <td>Archivsuche</td>
 <td><input type="radio" class="admRadio" name="SuchArchiv" value="1"<?php if($mpSuchArchiv) echo' checked="checked"'?>" />einblenden &nbsp; &nbsp; <input type="radio" class="admRadio" name="SuchArchiv" value="0"<?php if(!$mpSuchArchiv) echo' checked="checked"'?>" />ausblenden</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<div class="admBox"><p>Welche Felder im konkreten Suchformular überhaupt verwendet werden
stellen Sie für das jeweilige Marktsegment unter <i>Segmenteigenschaften</i> ein.</p></div>

<?php echo fSeitenFuss();?>