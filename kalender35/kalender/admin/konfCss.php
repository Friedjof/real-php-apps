<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('CSS-Datei bearbeiten','','KFa');

if(file_exists(KALPFAD.'kalStyles.css')){
 $s=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css'))));
 if($_SERVER['REQUEST_METHOD']=='POST'){
  $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',stripslashes(trim($_POST['css']))));
  if($t!=$s){ $s=$t;
   if($f=fopen(KALPFAD.'kalStyles.css','w')){
    fwrite($f,$s.NL); fclose($f);
    $Msg='<p class="admErfo">Folgende Einstellungen sind gespeichert:</p>';
   }else $Msg='<p class="admFehl">In die Datei kalStyles.css konnte nicht geschrieben werden!</p>';
  }else $Msg='<p class="admMeld">Die Einstellungen bleiben unverändert.</p>';
 }//POST
}else $Msg='<p class="admFehl">Setup-Fehler: Die Datei <i>kalStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie von Hand die Farb- und Layouteinstellungen.</p>';
echo $Msg.NL;
?>

<p class="admMini">Die direkte Bearbeitung der CSS-Datei ist nur für Kundige gedacht.
Die Bedeutung der Klassen siehe Anleitung <a href="<?php echo ADM_Hilfe?>LiesMich.htm#3.7" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" alt="Hilfe"></a> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#3.7" target="hilfe" onclick="hlpWin(this.href);return false;">LiesMich.htm</a>.<br >
Ich empfehle jedoch eher die Bearbeitung mit einem CSS-Editor statt mit diesem primitiven Formular.</p>
<p class="admMini"><u>Hinweis</u>: Vor umfangreichen Veränderungen sollten Sie unbedingt eine Sicherheitskopie der Datei <a href="<?php echo $sHttp?>kalStyles.css" target="_blank">kalStyles.css</a> anfertigen.</p>


<form action="konfCss.php" name="cssform" method="post">
<table class="admTabl"  border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl">
  <td align="center"><textarea name="css" cols="120" rows="36" style="height:48em;"><?php echo $s?></textarea></td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();
?>