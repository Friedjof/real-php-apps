<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Layouteinstellungen','','KLy');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $ksLinkOList=KAL_LinkOList; $ksLinkOMona=KAL_LinkOMona; $ksLinkOWoch=KAL_LinkOWoch; $ksLinkODrck=KAL_LinkODrck; $ksLinkOSuch=KAL_LinkOSuch; $ksLinkOEing=KAL_LinkOEing; $ksLinkOAend=KAL_LinkOAend; $ksLinkOLogi=KAL_LinkOLogi; $ksLinkOLogx=KAL_LinkOLogx; $ksLinkOExpt=KAL_LinkOExpt;
 $ksLinkUList=KAL_LinkUList; $ksLinkUMona=KAL_LinkUMona; $ksLinkUWoch=KAL_LinkUWoch; $ksLinkUDrck=KAL_LinkUDrck; $ksLinkUSuch=KAL_LinkUSuch; $ksLinkUEing=KAL_LinkUEing; $ksLinkUAend=KAL_LinkUAend; $ksLinkULogi=KAL_LinkULogi; $ksLinkULogx=KAL_LinkULogx; $ksLinkUExpt=KAL_LinkUExpt;
 $ksLnkOListN=KAL_LnkOListN; $ksLnkOMonaN=KAL_LnkOMonaN; $ksLnkOWochN=KAL_LnkOWochN; $ksLnkODrckN=KAL_LnkODrckN; $ksLnkOSuchN=KAL_LnkOSuchN; $ksLnkOEingN=KAL_LnkOEingN; $ksLnkOAendN=KAL_LnkOAendN; $ksLnkOLogiN=KAL_LnkOLogiN; $ksLnkOExptN=KAL_LnkOExptN;
 $ksLnkUListN=KAL_LnkUListN; $ksLnkUMonaN=KAL_LnkUMonaN; $ksLnkUWochN=KAL_LnkUWochN; $ksLnkUDrckN=KAL_LnkUDrckN; $ksLnkUSuchN=KAL_LnkUSuchN; $ksLnkUEingN=KAL_LnkUEingN; $ksLnkUAendN=KAL_LnkUAendN; $ksLnkULogiN=KAL_LnkULogiN; $ksLnkUExptN=KAL_LnkUExptN;
 $ksLinkAnf=KAL_LinkAnf; $ksLinkEnd=KAL_LinkEnd; $ksSchablone=KAL_Schablone; $ksLinkLstLst=KAL_LinkLstLst;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=txtVar('LinkLstLst');if(fSetzKalWert(($v?true:false),'LinkLstLst','')) $bNeu=true;
 $v=txtVar('LinkOList'); if(fSetzKalWert($v,'LinkOList','"')) $bNeu=true; $v=txtVar('LinkODrck'); if(fSetzKalWert($v,'LinkODrck','"')) $bNeu=true;
 $v=txtVar('LinkOMona'); if(fSetzKalWert($v,'LinkOMona','"')) $bNeu=true;
 $v=txtVar('LinkOWoch'); if(fSetzKalWert($v,'LinkOWoch','"')) $bNeu=true;
 $v=txtVar('LinkOSuch'); if(fSetzKalWert($v,'LinkOSuch','"')) $bNeu=true; $v=txtVar('LinkOExpt'); if(fSetzKalWert($v,'LinkOExpt','"')) $bNeu=true;
 $v=txtVar('LinkOEing'); if(fSetzKalWert($v,'LinkOEing','"')) $bNeu=true; $v=txtVar('LinkOAend'); if(fSetzKalWert($v,'LinkOAend','"')) $bNeu=true;
 $v=txtVar('LinkOLogi'); if(fSetzKalWert($v,'LinkOLogi','"')) $bNeu=true; $v=txtVar('LinkOLogx'); if(fSetzKalWert($v,'LinkOLogx','"')) $bNeu=true;
 $v=txtVar('LinkUList'); if(fSetzKalWert($v,'LinkUList','"')) $bNeu=true; $v=txtVar('LinkUDrck'); if(fSetzKalWert($v,'LinkUDrck','"')) $bNeu=true;
 $v=txtVar('LinkUMona'); if(fSetzKalWert($v,'LinkUMona','"')) $bNeu=true;
 $v=txtVar('LinkUWoch'); if(fSetzKalWert($v,'LinkUWoch','"')) $bNeu=true;
 $v=txtVar('LinkUSuch'); if(fSetzKalWert($v,'LinkUSuch','"')) $bNeu=true; $v=txtVar('LinkUExpt'); if(fSetzKalWert($v,'LinkUExpt','"')) $bNeu=true;
 $v=txtVar('LinkUEing'); if(fSetzKalWert($v,'LinkUEing','"')) $bNeu=true; $v=txtVar('LinkUAend'); if(fSetzKalWert($v,'LinkUAend','"')) $bNeu=true;
 $v=txtVar('LinkULogi'); if(fSetzKalWert($v,'LinkULogi','"')) $bNeu=true; $v=txtVar('LinkULogx'); if(fSetzKalWert($v,'LinkULogx','"')) $bNeu=true;
 $v=txtVar('LnkOListN'); if(fSetzKalWert(($v?true:false),'LnkOListN','')) $bNeu=true; $v=txtVar('LnkODrckN'); if(fSetzKalWert(($v?true:false),'LnkODrckN','')) $bNeu=true;
 $v=txtVar('LnkOMonaN'); if(fSetzKalWert(($v?true:false),'LnkOMonaN','')) $bNeu=true;
 $v=txtVar('LnkOWochN'); if(fSetzKalWert(($v?true:false),'LnkOWochN','')) $bNeu=true;
 $v=txtVar('LnkOSuchN'); if(fSetzKalWert(($v?true:false),'LnkOSuchN','')) $bNeu=true; $v=txtVar('LnkOExptN'); if(fSetzKalWert(($v?true:false),'LnkOExptN','')) $bNeu=true;
 $v=txtVar('LnkOEingN'); if(fSetzKalWert(($v?true:false),'LnkOEingN','')) $bNeu=true; $v=txtVar('LnkOAendN'); if(fSetzKalWert(($v?true:false),'LnkOAendN','')) $bNeu=true;
 $v=txtVar('LnkOLogiN'); if(fSetzKalWert(($v?true:false),'LnkOLogiN','')) $bNeu=true;
 $v=txtVar('LnkUListN'); if(fSetzKalWert(($v?true:false),'LnkUListN','')) $bNeu=true; $v=txtVar('LnkUDrckN'); if(fSetzKalWert(($v?true:false),'LnkUDrckN','')) $bNeu=true;
 $v=txtVar('LnkUMonaN'); if(fSetzKalWert(($v?true:false),'LnkUMonaN','')) $bNeu=true;
 $v=txtVar('LnkUWochN'); if(fSetzKalWert(($v?true:false),'LnkUWochN','')) $bNeu=true;
 $v=txtVar('LnkUSuchN'); if(fSetzKalWert(($v?true:false),'LnkUSuchN','')) $bNeu=true; $v=txtVar('LnkUExptN'); if(fSetzKalWert(($v?true:false),'LnkUExptN','')) $bNeu=true;
 $v=txtVar('LnkUEingN'); if(fSetzKalWert(($v?true:false),'LnkUEingN','')) $bNeu=true; $v=txtVar('LnkUAendN'); if(fSetzKalWert(($v?true:false),'LnkUAendN','')) $bNeu=true;
 $v=txtVar('LnkULogiN'); if(fSetzKalWert(($v?true:false),'LnkULogiN','')) $bNeu=true;
 $v=str_replace(' ','&nbsp;',str_replace('"',"'",stripslashes($_POST['LinkAnf']))); if(fSetzKalWert($v,'LinkAnf','"')) $bNeu=true;
 $v=str_replace(' ','&nbsp;',str_replace('"',"'",stripslashes($_POST['LinkEnd']))); if(fSetzKalWert($v,'LinkEnd','"')) $bNeu=true;
 $v=txtVar('Schablone'); if(fSetzKalWert(($v?true:false),'Schablone','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die geänderte Schabloneneinstellung wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Konfigurationseinstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Layouteinstellungen.</p>';
echo $Msg.NL;
?>

<form action="konfLayout.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über und/oder unter dem Kalender können Verweise zur Navigation angezeigt werden.
Welche der folgenden Verknüpfungen sollen mit welchem Wortlaut verwendet werden? (Nicht benötigte Verweise einfach leer lassen!)<br />
Die Verweise sind normalerweise für alle Besucher sichtbar.
Sie können sie aber für Gäste ausblenden und nur für <i>angemeldete</i> Besucher extra einblenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">oberhalb des<br />Kalenders</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="1">
   <tr>
    <td><input type="text" name="LinkOList" value="<?php echo $ksLinkOList?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Liste</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOListN" value="1"<?php if($ksLnkOListN) echo' checked="checked"'?> /> nur für Benutzer
    &nbsp; &nbsp; <input class="admRadio" type="checkbox" name="LinkLstLst" value="1"<?php if($ksLinkLstLst) echo' checked="checked"'?> /> auch auf der Listenseite selbst</td>
   </tr><tr>
    <td><input type="text" name="LinkODrck" value="<?php echo $ksLinkODrck?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Druck</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkODrckN" value="1"<?php if($ksLnkODrckN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOSuch" value="<?php echo $ksLinkOSuch?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Suche</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOSuchN" value="1"<?php if($ksLnkOSuchN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOMona" value="<?php echo $ksLinkOMona?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Monatsblatt</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOMonaN" value="1"<?php if($ksLnkOMonaN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOWoch" value="<?php echo $ksLinkOWoch?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Wochenblatt</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOWochN" value="1"<?php if($ksLnkOWochN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOExpt" value="<?php echo $ksLinkOExpt?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Export</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOExptN" value="1"<?php if($ksLnkOExptN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOEing" value="<?php echo $ksLinkOEing?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Eintragen</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOEingN" value="1"<?php if($ksLnkOEingN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOAend" value="<?php echo $ksLinkOAend?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Ändern</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOAendN" value="1"<?php if($ksLnkOAendN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkOLogi" value="<?php echo $ksLinkOLogi?>" maxlength="32" style="width:75px;" /><input type="text" name="LinkOLogx" value="<?php echo $ksLinkOLogx?>" maxlength="32" style="width:75px;" /></td>
    <td style="padding-left:5px;">Anmelden/Abmelden</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkOLogiN" value="1"<?php if($ksLnkOLogiN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr>
  </table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">unterhalb des<br />Kalenders</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="1">
   <tr>
    <td><input type="text" name="LinkUList" value="<?php echo $ksLinkUList?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Liste</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUListN" value="1"<?php if($ksLnkUListN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUDrck" value="<?php echo $ksLinkUDrck?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Druck</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUDrckN" value="1"<?php if($ksLnkUDrckN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUMona" value="<?php echo $ksLinkUMona?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Monatsblatt</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUMonaN" value="1"<?php if($ksLnkUMonaN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUWoch" value="<?php echo $ksLinkUWoch?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Wochenblatt</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUWochN" value="1"<?php if($ksLnkUWochN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUSuch" value="<?php echo $ksLinkUSuch?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Suche</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUSuchN" value="1"<?php if($ksLnkUSuchN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUExpt" value="<?php echo $ksLinkUExpt?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Export</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUExptN" value="1"<?php if($ksLnkUExptN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUEing" value="<?php echo $ksLinkUEing?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Eintragen</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUEingN" value="1"<?php if($ksLnkUEingN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkUAend" value="<?php echo $ksLinkUAend?>" maxlength="32" style="width:150px;" /></td>
    <td style="padding-left:5px;">Ändern</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkUAendN" value="1"<?php if($ksLnkUAendN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr><tr>
    <td><input type="text" name="LinkULogi" value="<?php echo $ksLinkULogi?>" maxlength="32" style="width:75px;" /><input type="text" name="LinkULogx" value="<?php echo $ksLinkULogx?>" maxlength="32" style="width:75px;" /></td>
    <td style="padding-left:5px;">Anmelden/Abmelden</td>
    <td style="padding-left:5px;"><input class="admRadio" type="checkbox" name="LnkULogiN" value="1"<?php if($ksLnkULogiN) echo' checked="checked"'?> /> nur für Benutzer</td>
   </tr>
  </table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">optische<br />Trennzeichen</td>
 <td><div>Zwischen diesen Navigationslinks (jeweils links und rechts davon) sollte eine optische Trennung erfolgen.</div>
  <input type="text" name="LinkAnf" value="<?php echo str_replace('&nbsp;',' ',$ksLinkAnf)?>" style="width:32px;" /> Verweistext
  <input type="text" name="LinkEnd" value="<?php echo str_replace('&nbsp;',' ',$ksLinkEnd)?>" style="width:32px;" /> &nbsp;
  <span class="admMini">Empfehlung: <i>[</i> bzw. <i>]</i> &nbsp; (mit je einem Leerzeichen davor und danach)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Kalender-Script als eigenständige Seite läuft (nicht includiert wird)
wird der eigentliche Kalender in eine frei gestaltbare HTML-Schablonenseite <i>kalSeite.htm</i> eingepasst.
Diese Layoutschablone kann im Ausnahmefall deaktiviert werden.
Dann hat die Ausgabe des Kalender-Scripts jedoch auch keinen &lt;head&gt;- und &lt;body&gt;-Tag.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layout-<br />schablone</td>
 <td>HTML-Seitenvorlage <i>kalSeite.htm</i>
  <input class="admRadio" type="radio" name="Schablone" value="1"<?php if($ksSchablone) echo' checked="checked"'?>> benutzen <span class="admMini">(empfohlen)</span> &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="Schablone" value="0"<?php if(!$ksSchablone) echo' checked="checked"'?>> nicht benutzen &nbsp; &nbsp;
  ( <a href="<?php echo KALPFAD?>kalSeite.htm" target="hilfe" onclick="hlpWin(this.href);return false;">Vorschau</a> )</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2" style="padding-top:2px;font-size:10px;"><u>Hinweis</u>:
Im Falle eines eingebetten Aufrufes per <i>include()</i>-Befehl wird die Schablone <i>kalSeite.htm</i> prinzipiell nicht benutzt.</div></td></tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p>Weitere Änderungen am Layout können Sie über die <a href="konfFarben.php">Farbeistellungen</a> als auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> vornehmen.</p>


<?php echo fSeitenFuss()?>