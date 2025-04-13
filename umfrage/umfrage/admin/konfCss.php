<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('CSS-Datei bearbeiten','','KFa');

$sCssDatei=UMF_CSSDatei; if(!file_exists(UMF_Pfad.$sCssDatei)) $sCssDatei='umfStyle.css';
if(file_exists(UMF_Pfad.$sCssDatei)){
 $s=str_replace("\r",'',trim(implode('',file(UMF_Pfad.$sCssDatei))));
 if($_SERVER['REQUEST_METHOD']=='POST'){
  $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',stripslashes(trim($_POST['css']))));
  if($t!=$s){ $s=$t;
   if($f=fopen(UMF_Pfad.$sCssDatei,'w')){
    fwrite($f,$s.NL); fclose($f);
    $sMeld='<p class="admErfo">Folgende Einstellungen sind nun gespeichert:</p>';
   }else $sMeld='<p class="admFehl">In die Datei <i>'.$sCssDatei.'</i> durfte nicht geschrieben werden (Rechteproblem)!</p>';
  }else $sMeld='<p class="admMeld">Die Einstellungen bleiben unverändert.</p>';
 }//POST
}else $sMeld='<p class="admFehl">Setup-Fehler: Die Datei <i>'.$sCssDatei.'</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie von Hand die Farb- und Layouteinstellungen.</p>';
echo $sMeld.NL;
if($sCssDatei!=UMF_CSSDatei) echo fMFehl('Die unter <a href="konfLayout.php'.(KONF?'?konf='.KONF:'').'">Layouteinstellung</a> angegebene Datei <i>'.UMF_CSSDatei.'</i> ist nicht verfügbar. Es wird <i>'.$sCssDatei.'</i> verwendet!');
?>

<p class="admMini"></p>

<form name="cssform" action="konfCss.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="8" cellspacing="1">
 <tr class="admTabl"><td>Die direkte Bearbeitung der CSS-Datei ist nur für Kundige gedacht.
Die Bedeutung der Klassen siehe Anleitung <a href="<?php echo ADU_Hilfe?>LiesMich.htm#3.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" alt="Hilfe"></a> <a href="<?php echo ADU_Hilfe?>LiesMich.htm#3.1" target="hilfe" onclick="hlpWin(this.href);return false;">Anleitung</a>.<br >
Ich empfehle jedoch eher die Bearbeitung mit einem Text-Editor statt mit diesem primitiven Formular.</td>
 </tr><tr class="admTabl">
  <td align="center"><div style="text-align:left;color:#644;margin-bottom:8px">CSS-Datei: <i><?php echo $sCssDatei ?></i></div><textarea name="css" cols="120" rows="36" style="height:48em;"><?php echo $s?></textarea></td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>