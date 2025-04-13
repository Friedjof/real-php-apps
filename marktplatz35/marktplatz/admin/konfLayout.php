<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Layout im Besucherbereich festlegen','','KLy');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $mpLinkOIndx=MP_LinkOIndx; $mpLnkOIndxN=MP_LnkOIndxN; $mpLinkUIndx=MP_LinkUIndx; $mpLnkUIndxN=MP_LnkUIndxN;
 $mpLinkOList=MP_LinkOList; $mpLinkODrck=MP_LinkODrck; $mpLinkOSuch=MP_LinkOSuch; $mpLinkOEing=MP_LinkOEing; $mpLinkOAend=MP_LinkOAend; $mpLinkOLogi=MP_LinkOLogi; $mpLinkOLogx=MP_LinkOLogx;
 $mpLinkUList=MP_LinkUList; $mpLinkUDrck=MP_LinkUDrck; $mpLinkUSuch=MP_LinkUSuch; $mpLinkUEing=MP_LinkUEing; $mpLinkUAend=MP_LinkUAend; $mpLinkULogi=MP_LinkULogi; $mpLinkULogx=MP_LinkULogx;
 $mpLnkOListN=MP_LnkOListN; $mpLnkODrckN=MP_LnkODrckN; $mpLnkOSuchN=MP_LnkOSuchN; $mpLnkOEingN=MP_LnkOEingN; $mpLnkOAendN=MP_LnkOAendN; $mpLnkOLogiN=MP_LnkOLogiN;
 $mpLnkUListN=MP_LnkUListN; $mpLnkUDrckN=MP_LnkUDrckN; $mpLnkUSuchN=MP_LnkUSuchN; $mpLnkUEingN=MP_LnkUEingN; $mpLnkUAendN=MP_LnkUAendN; $mpLnkULogiN=MP_LnkULogiN;
 $mpLinkZ1=MP_LinkZ1; $mpLinkZ2=MP_LinkZ2; $mpLinkZ3=MP_LinkZ3; $mpLinkZ4=MP_LinkZ4; $mpLinkZ5=MP_LinkZ5; $mpLinkZ6=MP_LinkZ6; $mpLinkZ7=MP_LinkZ7;
 $mpLnkZ1N=MP_LnkZ1N; $mpLnkZ2N=MP_LnkZ2N; $mpLnkZ3N=MP_LnkZ3N; $mpLnkZ4N=MP_LnkZ4N; $mpLnkZ5N=MP_LnkZ5N; $mpLnkZ6N=MP_LnkZ6N; $mpLnkZ7N=MP_LnkZ7N;
 $mpLinkAnf=MP_LinkAnf; $mpLinkEnd=MP_LinkEnd; $mpSchablone=MP_Schablone;
 $mpEigeneZeilen=MP_EigeneZeilen; $mpEigeneDetails=MP_EigeneDetails;
}else{ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $v=txtVar('LinkOIndx'); if(fSetzMPWert($v,'LinkOIndx','"')) $bNeu=true; $v=txtVar('LinkOList'); if(fSetzMPWert($v,'LinkOList','"')) $bNeu=true;
 $v=txtVar('LinkODrck'); if(fSetzMPWert($v,'LinkODrck','"')) $bNeu=true; $v=txtVar('LinkOSuch'); if(fSetzMPWert($v,'LinkOSuch','"')) $bNeu=true;
 $v=txtVar('LinkOEing'); if(fSetzMPWert($v,'LinkOEing','"')) $bNeu=true; $v=txtVar('LinkOAend'); if(fSetzMPWert($v,'LinkOAend','"')) $bNeu=true;
 $v=txtVar('LinkOLogi'); if(fSetzMPWert($v,'LinkOLogi','"')) $bNeu=true; $v=txtVar('LinkOLogx'); if(fSetzMPWert($v,'LinkOLogx','"')) $bNeu=true;
 $v=txtVar('LinkUIndx'); if(fSetzMPWert($v,'LinkUIndx','"')) $bNeu=true; $v=txtVar('LinkUList'); if(fSetzMPWert($v,'LinkUList','"')) $bNeu=true;
 $v=txtVar('LinkUDrck'); if(fSetzMPWert($v,'LinkUDrck','"')) $bNeu=true; $v=txtVar('LinkUSuch'); if(fSetzMPWert($v,'LinkUSuch','"')) $bNeu=true;
 $v=txtVar('LinkUEing'); if(fSetzMPWert($v,'LinkUEing','"')) $bNeu=true; $v=txtVar('LinkUAend'); if(fSetzMPWert($v,'LinkUAend','"')) $bNeu=true;
 $v=txtVar('LinkULogi'); if(fSetzMPWert($v,'LinkULogi','"')) $bNeu=true; $v=txtVar('LinkULogx'); if(fSetzMPWert($v,'LinkULogx','"')) $bNeu=true;
 $v=txtVar('LnkOIndxN'); if(fSetzMPWert(($v?true:false),'LnkOIndxN','')) $bNeu=true; $v=txtVar('LnkOListN'); if(fSetzMPWert(($v?true:false),'LnkOListN','')) $bNeu=true;
 $v=txtVar('LnkODrckN'); if(fSetzMPWert(($v?true:false),'LnkODrckN','')) $bNeu=true; $v=txtVar('LnkOSuchN'); if(fSetzMPWert(($v?true:false),'LnkOSuchN','')) $bNeu=true;
 $v=txtVar('LnkOEingN'); if(fSetzMPWert(($v?true:false),'LnkOEingN','')) $bNeu=true; $v=txtVar('LnkOAendN'); if(fSetzMPWert(($v?true:false),'LnkOAendN','')) $bNeu=true;
 $v=txtVar('LnkOLogiN'); if(fSetzMPWert(($v?true:false),'LnkOLogiN','')) $bNeu=true;
 $v=txtVar('LnkUIndxN'); if(fSetzMPWert(($v?true:false),'LnkUIndxN','')) $bNeu=true; $v=txtVar('LnkUListN'); if(fSetzMPWert(($v?true:false),'LnkUListN','')) $bNeu=true;
 $v=txtVar('LnkUDrckN'); if(fSetzMPWert(($v?true:false),'LnkUDrckN','')) $bNeu=true; $v=txtVar('LnkUSuchN'); if(fSetzMPWert(($v?true:false),'LnkUSuchN','')) $bNeu=true;
 $v=txtVar('LnkUEingN'); if(fSetzMPWert(($v?true:false),'LnkUEingN','')) $bNeu=true; $v=txtVar('LnkUAendN'); if(fSetzMPWert(($v?true:false),'LnkUAendN','')) $bNeu=true;
 $v=txtVar('LnkULogiN'); if(fSetzMPWert(($v?true:false),'LnkULogiN','')) $bNeu=true;
 for($i=1;$i<=7;$i++){
  $v=txtVar('LinkZ'.$i); if(fSetzMPWert($v,'LinkZ'.$i,'"')) $bNeu=true;
  $v=txtVar('LnkZ'.$i.'N'); if(fSetzMPWert(($v?true:false),'LnkZ'.$i.'N','')) $bNeu=true;
 }
 $v=str_replace(' ','&nbsp;',str_replace('"',"'",stripslashes($_POST['LinkAnf']))); if(fSetzMPWert($v,'LinkAnf','"')) $bNeu=true;
 $v=str_replace(' ','&nbsp;',str_replace('"',"'",stripslashes($_POST['LinkEnd']))); if(fSetzMPWert($v,'LinkEnd','"')) $bNeu=true;
 $v=txtVar('Schablone'); if(fSetzMPWert(($v?true:false),'Schablone','')) $bNeu=true;
 $v=txtVar('EigeneZeilen'); if(fSetzMPWert(($v?true:false),'EigeneZeilen','')) $bNeu=true;
 $v=txtVar('EigeneDetails'); if(fSetzMPWert(($v?true:false),'EigeneDetails','')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Layouteinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Layouteinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
if(!$Meld){$Meld='Ändern Sie die wesentlichsten Layouteinstellungen im Besucherbereich.'; $MTyp='Meld';}
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form name="schablonenform" action="konfLayout.php" method="post">
<input type="hidden" name="SchablonenForm" value="1" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Über und/oder unter dem Marktplatz können Verweise zur Navigation angezeigt werden.
Welche der folgenden Verknüpfungen sollen mit welchem Wortlaut verwendet werden? (Nicht benötigte Verweise einfach leer lassen!)<br />
Die Verweise sind normalerweise für alle Besucher sichtbar.
Sie können sie aber für Gäste ausblenden und nur für <i>angemeldete</i> Besucher extra einblenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">oberhalb<br>des<br>Marktplatzes</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="1"><tr>
   <td><input type="text" name="LinkOIndx" value="<?php echo $mpLinkOIndx?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkOList" value="<?php echo $mpLinkOList?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkODrck" value="<?php echo $mpLinkODrck?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkOSuch" value="<?php echo $mpLinkOSuch?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkOEing" value="<?php echo $mpLinkOEing?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkOAend" value="<?php echo $mpLinkOAend?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkOLogi" value="<?php echo $mpLinkOLogi?>" maxlength="24" style="width:62px;" /><input type="text" name="LinkOLogx" value="<?php echo $mpLinkOLogx?>" maxlength="24" style="width:62px;" /></td>
  </tr><tr>
   <td style="padding-left:3px;">Übersicht</td><td style="padding-left:3px;">Liste</td><td style="padding-left:3px;">Druck</td><td style="padding-left:3px;">Suche</td><td style="padding-left:3px;">Eintragen</td><td style="padding-left:3px;">Ändern</td><td style="padding-left:3px;">Anmelden/Abmelden</td>
  </tr><tr>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkOIndxN" value="1"<?php if($mpLnkOIndxN) echo' checked="checked"'?> /><span class="admMini">nur für User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkOListN" value="1"<?php if($mpLnkOListN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkODrckN" value="1"<?php if($mpLnkODrckN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkOSuchN" value="1"<?php if($mpLnkOSuchN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkOEingN" value="1"<?php if($mpLnkOEingN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkOAendN" value="1"<?php if($mpLnkOAendN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkOLogiN" value="1"<?php if($mpLnkOLogiN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
  </tr></table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">unterhalb<br>des<br>Marktplatzes</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="1"><tr>
   <td><input type="text" name="LinkUIndx" value="<?php echo $mpLinkUIndx?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkUList" value="<?php echo $mpLinkUList?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkUDrck" value="<?php echo $mpLinkUDrck?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkUSuch" value="<?php echo $mpLinkUSuch?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkUEing" value="<?php echo $mpLinkUEing?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkUAend" value="<?php echo $mpLinkUAend?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkULogi" value="<?php echo $mpLinkULogi?>" maxlength="24" style="width:62px;" /><input type="text" name="LinkULogx" value="<?php echo $mpLinkULogx?>" maxlength="24" style="width:62px;" /></td>
  </tr><tr>
   <td style="padding-left:3px;">Übersicht</td><td style="padding-left:3px;">Liste</td><td style="padding-left:3px;">Druck</td><td style="padding-left:3px;">Suche</td><td style="padding-left:3px;">Eintragen</td><td style="padding-left:3px;">Ändern</td><td style="padding-left:3px;">Anmelden/Abmelden</td>
  </tr><tr>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkUIndxN" value="1"<?php if($mpLnkUIndxN) echo' checked="checked"'?> /><span class="admMini">nur für User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkUListN" value="1"<?php if($mpLnkUListN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkUDrckN" value="1"<?php if($mpLnkUDrckN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkUSuchN" value="1"<?php if($mpLnkUSuchN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkUEingN" value="1"<?php if($mpLnkUEingN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkUAendN" value="1"<?php if($mpLnkUAendN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:3px;"><input class="admRadio" type="checkbox" name="LnkULogiN" value="1"<?php if($mpLnkULogiN) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
  </tr></table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Links auf <a href="<?php echo AM_Hilfe?>LiesMich.htm#3.8" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a><br>Zusatzseiten<br>unterhalb<br>des Marktes</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="1"><tr>
   <td><input type="text" name="LinkZ1" value="<?php echo $mpLinkZ1?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ2" value="<?php echo $mpLinkZ2?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ3" value="<?php echo $mpLinkZ3?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ4" value="<?php echo $mpLinkZ4?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ5" value="<?php echo $mpLinkZ5?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ6" value="<?php echo $mpLinkZ6?>" maxlength="24" style="width:80px;" /></td>
   <td><input type="text" name="LinkZ7" value="<?php echo $mpLinkZ7?>" maxlength="24" style="width:80px;" /></td>
  </tr><tr>
   <td style="padding-left:1px;">mpZusatz1</td><td style="padding-left:1px;">mpZusatz2</td><td style="padding-left:1px;">mpZusatz3</td><td style="padding-left:1px;">mpZusatz4</td><td style="padding-left:1px;">mpZusatz5</td><td style="padding-left:1px;">mpZusatz6</td><td style="padding-left:1px;">mpZusatz7</td>
  </tr><tr>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ1N" value="1"<?php if($mpLnkZ1N) echo' checked="checked"'?> /><span class="admMini">nur für User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ2N" value="1"<?php if($mpLnkZ2N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ3N" value="1"<?php if($mpLnkZ3N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ4N" value="1"<?php if($mpLnkZ4N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ5N" value="1"<?php if($mpLnkZ5N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ6N" value="1"<?php if($mpLnkZ6N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
   <td style="padding-left:0px;"><input class="admRadio" type="checkbox" name="LnkZ7N" value="1"<?php if($mpLnkZ7N) echo' checked="checked"'?> /><span class="admMini">nur f. User</span></td>
  </tr></table>
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">optische<br>Trennzeichen</td>
 <td><div>Zwischen diesen Navigationslinks (jeweils links und rechts davon) sollte eine optische Trennung erfolgen.</div>
  <input type="text" name="LinkAnf" value="<?php echo str_replace('&nbsp;',' ',$mpLinkAnf)?>" style="width:32px;" /> Verweistext
  <input type="text" name="LinkEnd" value="<?php echo str_replace('&nbsp;',' ',$mpLinkEnd)?>" style="width:32px;" /> &nbsp;
  <span class="admMini">Empfehlung: <i>[</i> bzw. <i>]</i> &nbsp; (mit je einem Leerzeichen davor und danach)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Marktplatz-Script als eigenständige Seite läuft (nicht mit dem PHP-Befehl <i>include</i> eingebunden wird)
wird der eigentliche Marktplatz in eine frei gestaltbare HTML-Schablonenseite <i>mpSeite.htm</i> eingepasst.
Diese Layoutschablone kann im Ausnahmefall deaktiviert werden.
Dann hat die Ausgabe des Marktplatz-Scripts jedoch auch keinen &lt;head&gt;- und &lt;body&gt;-Tag.</td></tr>
<tr class="admTabl">
 <td>Schablone</td>
 <td>HTML-Seitenvorlage <a href="<?php echo AM_Hilfe?>LiesMich.htm#3.1" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> <i>mpSeite.htm</i>
  <input class="admRadio" type="radio" name="Schablone" value="1"<?php if($mpSchablone) echo' checked="checked"'?>> benutzen <span class="admMini">(empfohlen)</span> &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="Schablone" value="0"<?php if(!$mpSchablone) echo' checked="checked"'?>> nicht benutzen &nbsp; &nbsp;
  ( <a href="<?php echo MPPFAD?>mpSeite.htm" target="_blank">Vorschau</a> )</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa1" style="padding-top:2px;font-size:10px;"><u>Hinweis</u>:
Im Falle eines eingebetten Aufrufes per <i>include</i>-Befehl wird die Schablone <i>mpSeite.htm</i> prinzipiell nicht benutzt.</div></td></tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Inserateliste wird standardmäßig als Tabelle mit nebeneinanderstehenden Spalten erzeugt, die Sie im Menüpunkt <i>Segmenteigenschaften</i> segmentabhängig auswählen.
Abweichend davon kann jeder Inseratedatensatz in einem individuellen Layout dargestellt werden, das aus der allgemeinen Layoutschablone <i>mpListenZeile.htm</i> oder aus der segmentspezifischen Layoutschablone <i>mpXXListenZeile.htm</i> (wobei <i>XX</i> die Nummer des jeweiligen Segmentes ist) stammt.
Diese Layoutschablone müssten Sie aber zuvor selbst mit einem Editor in passendem HTML-Code gestalten. <a href="<?php echo AM_Hilfe?>LiesMich.htm#3.2" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td>Listenlayout</td>
 <td><input type="radio" class="admRadio" name="EigeneZeilen" value="0"<?php if(!$mpEigeneZeilen) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneZeilen" value="1"<?php if($mpEigeneZeilen) echo ' checked="checked"'?> /> individuelles Layout aus der Schablone <i>mpListenZeile.htm</i></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Inserat wird normalerweise in einer automatisch generierten Tabelle dargestellt,
die in der linken Spalte den jeweiligen Feldnamen und in der rechten Spalte die zugehörigen Feldinhalte anzeigt.
Die Auswahl der Detailzeilen erfolgt segmentabhängig über den Menüpunkt <i>Segmenteigenschaften</i>.
Sie können statt dessen eine eigene Layoutschablone für die Inseratedetails verwenden, in der eine andere Anordnung realisiert wird.
Diese Layoutschablone im HTML-Format mit dem allgemeingültigen Namen <i>mpDetailZeilen.htm</i> bzw. eine segmentspezifische Schablone namens <i>mpDetailXXZeilen.htm</i> (wobei <i>XX</i> die Nummer des jeweiligen Segmentes ist) müssen Sie jedoch zuvor manuell anfertigen.
<a href="<?php echo AM_Hilfe?>LiesMich.htm#3.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>
<tr class="admTabl">
 <td>Detaillayout</td>
 <td><input type="radio" class="admRadio" name="EigeneDetails" value=""<?php if(!$mpEigeneDetails) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneDetails" value="1"<?php if($mpEigeneDetails) echo ' checked="checked"'?>/> eigene Detailschablone <i>mpDetailZeilen.htm</i> verwenden</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<p style="margin-top:20px;">Weitere Änderungen am Layout können Sie über die <a href="konfFarben.php">Farbeistellungen</a> oder direkt in der CSS-Datei <a href="konfCss.php"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> mpStyles.css</a> vornehmen.</p>

<?php echo fSeitenFuss();?>