<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('CSS-Datei direkt bearbeiten');

if(file_exists(MP_Pfad.'mpStyles.css')){
 $s=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpStyles.css'))));
 if($_SERVER['REQUEST_METHOD']!='POST'){//GET
  $Meld='Kontrollieren oder ändern Sie die wesentlichsten Farb- und Layouteinstellungen.'; $MTyp='Meld';
 }else{//POST
  $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',stripslashes(trim($_POST['css']))));
  if($t!=$s){$s=$t;
   if($f=fopen(MP_Pfad.'mpStyles.css','w')){
    fwrite($f,$s.NL); fclose($f);
    $Meld='Folgende Einstellungen wurden gespeichert:'; $MTyp='Erfo';
   }else $Meld='In die Datei <i>mpStyles.css</i> im Programmverzeichnis durfte nicht geschrieben werden!';
  }else{$Meld='Die Einstellungen bleiben unverändert.'; $MTyp='Meld';}
 }//POST
}else $Meld='Setup-Fehler: Die Datei <i>mpStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!';

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<p class="admMini"><u>Hinweis</u>: Die direkte Bearbeitung der CSS-Datei ist nur für Kundige gedacht.
Die Bedeutung der Klassen siehe Anleitung <a href="<?php echo AM_Hilfe?>LiesMich.htm#" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>.<br >
Ich empfehle jedoch eher die Bearbeitung mit einem CSS-Editor statt mit diesem primitiven Formular.</p>

<form action="konfCss.php" method="post">
<table class="admTabl" border="0" cellpadding="5" cellspacing="1">
 <tr class="admTabl">
  <td align="center"><textarea name="css" cols="120" rows="42" style="width:99%;height:48em;"><?php echo $s?></textarea></td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php echo fSeitenFuss();?>