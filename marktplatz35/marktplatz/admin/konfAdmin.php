<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Administration kofigurieren','','KAd');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen für den Administrator-Bereich. <a href="'.AM_Hilfe.'LiesMich.htm#2.14" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $amMitLogin=AM_MitLogin; $amSessionsAgent=AM_SessionsAgent; $amSessionsIPAddr=AM_SessionsIPAddr;
 $amAdmin=AM_Admin; $amPasswort=fMpDeCode(AM_Passwort);
 $amAuthLogin=AM_AuthLogin; $amAuthor=AM_Author; $amAuthPass=fMpDeCode(AM_AuthPass); $amAuthCronJob=AM_AuthCronJob;
 $amBreite=AM_Breite; $amSqlZs=AM_SqlZs; $amHilfe=AM_Hilfe;
 $amRueckwaerts=AM_Rueckwaerts; $amArchivRueckwaerts=AM_ArchivRueckwaerts; $amZeigeAltes=AM_ZeigeAltes; $amListenLaenge=AM_ListenLaenge;
 $amZeigeOnline=AM_ZeigeOnline; $amZeigeOffline=AM_ZeigeOffline; $amZeigeVormerk=AM_ZeigeVormerk;
 $amBildVorschau=AM_BildVorschau; $amDateiSymbol=AM_DateiSymbol; $amLinkSymbol=AM_LinkSymbol; $amSymbSymbol=AM_SymbSymbol;
 $amNutzerLaenge=AM_NutzerLaenge; $amNutzerFelder=AM_NutzerFelder; $amDetailInfo=AM_DetailInfo;
 $amNutzerKontakt=AM_NutzerKontakt; $amNutzerBetreff=AM_NutzerBetreff; $amVormerkAlle=AM_VormerkAlle;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false; $aNF=explode(';',MP_NutzerFelder);
 $v=(int)txtVar('MitLogin'); if(fSetzAdmWert(($v?true:false),'MitLogin','')) $bNeu=true;
 $v=(int)txtVar('SessionsAgent'); if(fSetzAdmWert(($v?true:false),'SessionsAgent','')) $bNeu=true;
 $v=(int)txtVar('SessionsIPAddr'); if(fSetzAdmWert(($v?true:false),'SessionsIPAddr','')) $bNeu=true;
 $v=strtolower(txtVar('Admin')); if(fSetzAdmWert($v,'Admin',"'")) $bNeu=true;
 $v=(isset($_POST['Passwort'])?'#'.trim($_POST['Passwort']):''); $amPasswort=fMpDeCode(AM_Passwort);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('Passwort'))); if(fSetzAdmWert(fMpEnCode($v),'Passwort',"'")) $bNeu=true; $amPasswort=$v;
 }elseif(!strpos($Meld,'Administratorpasswort')) $Meld.='<p class="admFehl">Das Administratorpasswort darf kein &quot; oder &gt; enthalten!</p>';
 $v=(int)txtVar('AuthLogin'); if(fSetzAdmWert(($v?true:false),'AuthLogin','')) $bNeu=true;
 $v=strtolower(txtVar('Author')); if(fSetzAdmWert($v,'Author',"'")) $bNeu=true;
 $v=(isset($_POST['AuthPass'])?'#'.trim($_POST['AuthPass']):''); $amAuthPass=fMpDeCode(AM_AuthPass);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('AuthPass'))); if(fSetzAdmWert(fMpEnCode($v),'AuthPass',"'")) $bNeu=true; $amAuthPass=$v;
 }elseif(!strpos($Meld,'Autorenpasswort')) $Meld.='<p class="admFehl">Das Autorenpasswort darf kein &quot; oder &gt; enthalten!</p>';
 $v=(int)txtVar('AuthCronJob'); if(fSetzAdmWert(($v?true:false),'AuthCronJob','')) $bNeu=true;
 $v=max(min((int)txtVar('Breite'),1800),600); if(fSetzAdmWert($v,'Breite','')) $bNeu=true;
 $v=txtVar('SqlZs'); if(fSetzAdmWert($v,'SqlZs','"')) $bNeu=true;
 $v=txtVar('Hilfe'); if(substr($v,-1,1)!='/') $v.='/'; if(substr($v,0,8)!='https://'&&substr($v,0,7)!='http://') $v='http://'.$v; if(fSetzAdmWert(($v),'Hilfe',"'")) $bNeu=true;
 $v=(int)txtVar('Rueckwaerts'); if(fSetzAdmWert(($v?true:false),'Rueckwaerts','')) $bNeu=true;
 $v=(int)txtVar('ArchivRueckwaerts'); if(fSetzAdmWert(($v?true:false),'ArchivRueckwaerts','')) $bNeu=true;
 $v=(int)txtVar('ZeigeAltes'); if(fSetzAdmWert(($v?true:false),'ZeigeAltes','')) $bNeu=true;
 $v=(int)txtVar('ZeigeOnline'); if(fSetzAdmWert(($v?true:false),'ZeigeOnline','')) $bNeu=true;
 $v=(int)txtVar('ZeigeOffline'); if(fSetzAdmWert(($v?true:false),'ZeigeOffline','')) $bNeu=true;
 $v=(int)txtVar('ZeigeVormerk'); if(fSetzAdmWert(($v?true:false),'ZeigeVormerk','')) $bNeu=true;
 $v=max((int)txtVar('ListenLaenge'),0); if(fSetzAdmWert($v,'ListenLaenge','')) $bNeu=true;
 $v=(int)txtVar('VormerkAlle'); if(fSetzAdmWert(($v?true:false),'VormerkAlle','')) $bNeu=true;
 $v=(int)txtVar('BildVorschau');if(fSetzAdmWert(($v?true:false),'BildVorschau','')) $bNeu=true;
 $v=(int)txtVar('DateiSymbol'); if(fSetzAdmWert(($v?true:false),'DateiSymbol','')) $bNeu=true;
 $v=(int)txtVar('LinkSymbol');  if(fSetzAdmWert(($v?true:false),'LinkSymbol','')) $bNeu=true;
 $v=(int)txtVar('SymbSymbol');  if(fSetzAdmWert(($v?true:false),'SymbSymbol','')) $bNeu=true;
 $v=(int)txtVar('DetailInfo'); if(fSetzAdmWert($v,'DetailInfo','')) $bNeu=true;
 $v=min(count($aNF)-2,max((int)txtVar('NutzerFelder'),4)); if(fSetzAdmWert($v,'NutzerFelder','')) $bNeu=true;
 $v=max((int)txtVar('NutzerLaenge'),1); if(fSetzAdmWert($v,'NutzerLaenge','')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('NutzerKontakt')))); if(fSetzAdmWert($v,'NutzerKontakt',"'")) $bNeu=true;
 $v=str_replace('  ',' ',txtVar('NutzerBetreff')); if(fSetzAdmWert($v,'NutzerBetreff','"')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der geänderten Administrationseinstellungen wurden gespeichert.'; $MTyp='Erfo';
  }else $Meld.=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{if(!$Meld) $Meld.='Die Administrationseinstellungen bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
if(!is_dir(MP_Pfad.MP_Daten)) echo '<p class="admFehl">Bitte zuerst die Pfade im Setup einstellen!</p>'.NL;
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form action="konfAdmin.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Zugangsschutz zur gesamten Administration sollte bevorzugt serverseitig erfolgen.
Im Kundenmenü nahezu jedes heutigen Servers findet sich ein Menüpunkt wie <i>Zugangsschutz</i>, <i>geschütze Ordner</i> o.ä.
Alternativ dazu läßt sich der Zugangsschutz oft über einen .htaccess-Datei im Admin-Ordner einrichten.
<p>Sollte ein serverseitiger Schutz des Admin-Ordners auf Ihrem Server nicht möglich sein,
können Sie den scriptseitigen Zugangs-Schutz zur Administration einschalten.
Dieser scriptseitige Schutz bietet <i>nicht</i> die selbe hohe Sicherheit wie der serverseitige Schutz.
Ausserdem müssen Sie hierfür Sitzungs-Cookies in Ihrem Browser zulassen.</p></td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>der Administration</td>
 <td><input type="radio" class="admRadio" name="MitLogin" value=""<?php if(!$amMitLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="MitLogin" value="1"<?php if($amMitLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Administrator <input type="text" name="Admin" value="<?php echo $amAdmin?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="Passwort" value="<?php echo $amPasswort?>" style="width:10em" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sitzungsüberwachung</td>
 <td><div><input type="checkbox" class="admRadio" name="SessionsAgent" value="1"<?php if($amSessionsAgent) echo ' checked="checked"'?> /> Browserkennung überwachen</div>
 <div><input type="checkbox" class="admRadio" name="SessionsIPAddr" value="1"<?php if($amSessionsIPAddr) echo ' checked="checked"'?> /> IP-Adresse überwachen</div>
 <div class="admMini">ausschalten verringert die Sicherheit bei scriptseitigem Zugangsschutz zur Administration</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Auch für den Autorenbereich im entsprechenden Unterordner kann bei dessen Verwendung ein scriptseitiger Zugangsschutz eingerichtet werden.
Allerdings ist auch hier der serverseitige Schutz die zu bevorzugende Variante.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>des Autorenbereichs</td>
 <td><input type="radio" class="admRadio" name="AuthLogin" value=""<?php if(!$amAuthLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="AuthLogin" value="1"<?php if($amAuthLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Autorname <input type="text" name="Author" value="<?php echo $amAuthor?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="AuthPass" value="<?php echo $amAuthPass?>" style="width:10em" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Autorenberechtigung</td>
 <td><input type="checkbox" class="admCheck" name="AuthCronJob" value="1"<?php if($amAuthCronJob) echo ' checked="checked"'?> /> Autoren dürfen den Cron-Job <a href="<?php echo MPPFAD?>mpCronJob.php?mp=<?php echo MP_Schluessel?>"  target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten"><img src="<?php echo MPPFAD?>grafik/iconAendern.gif" width="12" height="13" border="0" alt="aufrufen"> mpCronJob.php</a> per Link von Hand aufrufen.</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die folgenden Einstellungen für den Administratorbereich gelten genauso für den Autorenbereich, falls dieser benutzt wird.</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Administrationsseiten sind in der Breite einstellbar.</td></tr>
<tr class="admTabl">
 <td width="10%">Fenstergröße</td>
 <td><input type="text" name="Breite" value="<?php echo $amBreite;?>"  size="4" /> Pixel Breite</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Administrationsseiten greifen bei Verwendung der MySQL-Datenbank statt der Textdatenbank auf diese mit dem Standard-Zeichensatz zu, d.h. ohne extra einen bestimmten Zeichensatz einzustellen.
Falls Ihre Serverumgebung es aus irgendwelchen Gründen erfordert, können Sie für die MySQL-Verbindung der Administration auch einen anderen Zeichensatz erzwingen.
Das entspricht einem MySQL-Befehl <i>mysqli_set_charset()</i> für jede SQL-Verbindung in der Administration.</td></tr>
<tr class="admTabl">
 <td style="white-space:nowrap">MySQL-Zeichensatz<br>der Administration</td>
 <td><input type="text" name="SqlZs" value="<?php echo $amSqlZs;?>" size="10" /> <span class="admMini">Empfehlung: <i>leer lassen</i> falls nicht verwendet, nur notfalls <i>latin1</i> oder <i>utf8</i> bzw. <i>utf8mb4</i></span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2"><u>Hinweis</u>: Der Zeichensatz für die Seitenausgaben im Admin-Ordner ist nicht direkt einstellbar. Bei Problemen sollten Sie über die Direktive <i>AddDefaultCharset</i> in der Datei <i>.htaccess</i> des Admin-Ordners korrigieren. <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.4_Admin" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td></tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die für die Administrationsseiten verwendete online-Hilfe LiesMich.htm liegt in folgendem Verzeichnis:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Pfad zur online-Hilfe</td>
 <td><input type="text" name="Hilfe" value="<?php echo $amHilfe?>" style="width:99%" />
 <div class="admMini">aktuell: <i>https://www.server-scripts.de/marktplatz/</i> &nbsp; &nbsp; &nbsp; (zur <a class="admMini" href="<?php echo $amHilfe?>LiesMich.htm" target="hilfe"><i>Hilfe-Datei</i></a>)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Listendarstellung in der Inserateübersicht im Administrationsbereich kann von der Darstellung im öffentlichen Bereich abweichen.</td></tr>
<tr class="admTabl">
 <td>Listensortierung</td>
 <td><input type="radio" class="admRadio" name="Rueckwaerts" value=""<?php if(!$amRueckwaerts) echo ' checked="checked"'?> /> vorwärts &nbsp; <input type="radio" class="admRadio" name="Rueckwaerts" value="1"<?php if($amRueckwaerts) echo ' checked="checked"'?> /> rückwärts</td>
</tr>
<tr class="admTabl">
 <td>Archivsortierung</td>
 <td><input type="radio" class="admRadio" name="ArchivRueckwaerts" value=""<?php if(!$amArchivRueckwaerts) echo ' checked="checked"'?> /> vorwärts &nbsp; <input type="radio" class="admRadio" name="ArchivRueckwaerts" value="1"<?php if($amArchivRueckwaerts) echo ' checked="checked"'?> /> rückwärts</td>
</tr>
<tr class="admTabl">
 <td>abgelaufene Inserate</td>
 <td><input type="radio" class="admRadio" name="ZeigeAltes" value="1"<?php if($amZeigeAltes) echo ' checked="checked"'?> /> anzeigen &nbsp; <input type="radio" class="admRadio" name="ZeigeAltes" value=""<?php if(!$amZeigeAltes) echo ' checked="checked"'?> /> ausblenden und nur als Archiv anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inseratearten in<br />der Inserateliste</td>
 <td><input type="checkbox" class="admRadio" name="ZeigeOnline" value="1"<?php if($amZeigeOnline) echo ' checked="checked"'?> /> online-Inserate anzeigen
 <div><input type="checkbox" class="admRadio" name="ZeigeOffline" value="1"<?php if($amZeigeOffline) echo ' checked="checked"'?> /> offline-Inserate anzeigen</div>
 <div><input type="checkbox" class="admRadio" name="ZeigeVormerk" value="1"<?php if($amZeigeVormerk) echo ' checked="checked"'?> /> Vormerk-Inserate anzeigen</div>
 <div class="admMini">Hinweis: Diese Inseratearten werden in der Liste als Standard ausgegeben. Abweichende Ausgaben sind über das Suchformular einstellbar.</div></td>
</tr>
<tr class="admTabl">
 <td>Listenlänge</td>
 <td><input type="text" name="ListenLaenge" value="<?php echo $amListenLaenge?>" size="2" /> Inseratezeilen auf einer Listenseite der Inserateübersicht
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Inserate pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td>Bildvorschau</td>
 <td><input type="radio" class="admRadio" name="BildVorschau" value="1"<?php if($amBildVorschau) echo ' checked="checked"'?> /> Bilder als Vorschaubild &nbsp; <input type="radio" class="admRadio" name="BildVorschau" value=""<?php if(!$amBildVorschau) echo ' checked="checked"'?> /> nur als Dateiname</td>
</tr>
<tr class="admTabl">
 <td>Dateianhänge</td>
 <td><input type="radio" class="admRadio" name="DateiSymbol" value="1"<?php if($amDateiSymbol) echo ' checked="checked"'?> /> Dateianhänge als Symbol &nbsp; <input type="radio" class="admRadio" name="DateiSymbol" value=""<?php if(!$amDateiSymbol) echo ' checked="checked"'?> /> als Dateiname</td>
</tr>
<tr class="admTabl">
 <td>Linkdarstellung</td>
 <td><input type="radio" class="admRadio" name="LinkSymbol" value=""<?php if(!$amLinkSymbol) echo ' checked="checked"'?> /> Links und E-Mails sichtbar ausgeschrieben &nbsp; <input type="radio" class="admRadio" name="LinkSymbol" value="1"<?php if($amLinkSymbol) echo ' checked="checked"'?> /> nur als Klick-Symbol</td>
</tr>
<tr class="admTabl">
 <td>Symbolfelder</td>
 <td><input type="radio" class="admRadio" name="SymbSymbol" value=""<?php if(!$amSymbSymbol) echo ' checked="checked"'?> /> Werte von Symbolfeldern ausgeschrieben &nbsp; <input type="radio" class="admRadio" name="SymbSymbol" value="1"<?php if($amSymbSymbol) echo ' checked="checked"'?> /> nur als Symbol</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Bei der Freischaltung vorgemerkter Inserate der Gäste/Benutzer
können auf der Freischaltseite nur die Vormerkungen des aktuell eingestellten Marktsegmentes erscheinen
oder sämtliche vorliegende Vormerkungen quer über alle Marktsegmente.</td></tr>
<tr class="admTabl">
 <td>Vormerkungen<br>freischalten</td>
 <td><input type="radio" class="admRadio" name="VormerkAlle" value=""<?php if(!$amVormerkAlle) echo ' checked="checked"'?> /> nur aus dem aktuellen Marktsegment &nbsp; <input type="radio" class="admRadio" name="VormerkAlle" value="1"<?php if($amVormerkAlle) echo ' checked="checked"'?> /> quer über alle Marktsegmente</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Auf den Seiten der Inseratedetails kann für den Administrator eine Zusatzzeile
mit der Funktion <i>Information versenden</i> (<i>sag's einem Freund</i>) eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td>Infofunktion</td>
 <td>zusätzliche Infozeile vor Zeile <select name="DetailInfo" size="1"><option value="-1">--</option><?php for($i=0;$i<=32;$i++) echo '<option value="'.$i.'"'.($amDetailInfo==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> in der Detaildarstellung zum Inserat im Admin-Bereich</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Liste der Benutzerübersicht im Administrationsbereich (sofern die Benutzerverwaltung im Marktsegment verwendet wird) kann individuell angepasst werden.</td></tr>
<tr class="admTabl">
 <td>Listenlänge</td>
 <td><input type="text" name="NutzerLaenge" value="<?php echo $amNutzerLaenge?>" size="2" /> Zeilen mit Benutzerdaten auf einer Listenseite der Benutzerübersicht
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Inserate pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td>Spaltenanzahl</td>
 <td><input type="text" name="NutzerFelder" value="<?php echo $amNutzerFelder?>" size="2" /> Felder in der Tabelle der Benutzerübersicht
 <div class="admMini">Empfehlung: die ersten 5...7 Felder der Benutzerdaten sind meist ausreichend</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Administrator kann aus der Benutzerübersicht heraus mit den Benutzern per E-Mail Kontakt aufnehmen.</td></tr>
<tr class="admTabl">
 <td>Benutzerbetreff</td>
 <td><input type="text" name="NutzerBetreff" style="width:99%" value="<?php echo $amNutzerBetreff?>" /><div class="admMini">(Dieser Text wird als überschreibbarer Betreff für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td style="vertical-align:top;padding-top:5px;">Benutzerkontakt</td>
 <td><textarea name="NutzerKontakt" cols="80" rows="6" style="width:99%;height:120px;"><?php echo str_replace('\n ',"\n",$amNutzerKontakt)?></textarea><div class="admMini">(Dieser Text wird als überschreibbare Standardvorlage für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php
echo fSeitenFuss();

function fSetzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n};
 if($t=="'") $w=str_replace("'",'´',$w); ${'am'.$n}=$w;
 if($w!=constant('AM_'.$n)){
  $p=strpos($sWerte,'AM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'AM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>