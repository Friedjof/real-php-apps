<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Administratorbereich anpassen','','KAd');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $amMitLogin=ADF_MitLogin; $amSessionsAgent=ADF_SessionsAgent; $amSessionsIPAddr=ADF_SessionsIPAddr;
 $amAdmin=ADF_Admin; $amPasswort=fFraDeCode(ADF_Passwort); $amBreite=ADF_Breite; $amHilfe=ADF_Hilfe;
 $amAuthLogin=ADF_AuthLogin; $amAuthor=ADF_Author; $amAuthPass=fFraDeCode(ADF_AuthPass);
 $amAntwortZahl=ADF_AntwortZahl; $amAnmerkZahl=ADF_AnmerkZahl; $fsPunkteTeilen=FRA_PunkteTeilen; $amOhneAntwort=FRA_OhneAntwort;
 $amFragenFeldHoehe=ADF_FragenFeldHoehe; $amAntwortFeldHoehe=ADF_AntwortFeldHoehe; $amAnmerkFeldHoehe=ADF_AnmerkFeldHoehe;
 $amStripSlashes=ADF_StripSlashes; $amCKEditor=ADF_CKEditor;
 $amListenLaenge=ADF_ListenLaenge; $amRueckwaerts=ADF_Rueckwaerts;
 $amNutzerLaenge=ADF_NutzerLaenge; $amNutzerRueckw=ADF_NutzerRueckw;
 $amNutzerBetreff=ADF_NutzerBetreff; $amNutzerKontakt=ADF_NutzerKontakt;
 $amErgebnisLaenge=ADF_ErgebnisLaenge; $amErgebnisRueckw=ADF_ErgebnisRueckw;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=(int)txtVar('MitLogin'); if(setzAdmWert(($v?true:false),'MitLogin','')) $bNeu=true;
  $v=(int)txtVar('SessionsAgent'); if(setzAdmWert(($v?true:false),'SessionsAgent','')) $bNeu=true;
  $v=(int)txtVar('SessionsIPAddr'); if(setzAdmWert(($v?true:false),'SessionsIPAddr','')) $bNeu=true;
  $v=strtolower(txtVar('Admin')); if(setzAdmWert($v,'Admin',"'")) $bNeu=true;
  $v=(isset($_POST['Passwort'])?'#'.trim($_POST['Passwort']):''); $amPasswort=fFraDeCode(ADF_Passwort);
  if(!strpos($v,'"')&&!strpos($v,'>')){
   $v=txtVar('Passwort'); if(setzAdmWert(fFraEnCode($v),'Passwort',"'")) $bNeu=true; $amPasswort=$v;
  }elseif(!strpos($sMeld,'Administratorpasswort')) $sMeld.='<p class="admFehl">Das Administratorpasswort darf kein &quot; oder &gt; enthalten!</p>';
  $v=(int)txtVar('AuthLogin'); if(setzAdmWert(($v?true:false),'AuthLogin','')) $bNeu=true;
  $v=strtolower(txtVar('Author')); if(setzAdmWert($v,'Author',"'")) $bNeu=true;
  $v=(isset($_POST['AuthPass'])?'#'.trim($_POST['AuthPass']):''); $amAuthPass=fFraDeCode(ADF_AuthPass);
  if(!strpos($v,'"')&&!strpos($v,'>')){
   $v=txtVar('AuthPass'); if(setzAdmWert(fFraEnCode($v),'AuthPass',"'")) $bNeu=true; $amAuthPass=$v;
  }elseif(!strpos($sMeld,'Autorenpasswort')) $sMeld.='<p class="admFehl">Das Autorenpasswort darf kein &quot; oder &gt; enthalten!</p>';
  if($bAlleKonf||(int)$sKonf==KONF){
   $v=max(min((int)txtVar('AntwortZahl'),9),2); if(setzAdmWert(($v),'AntwortZahl','')) $bNeu=true;
   $v=1-(int)txtVar('OhneAntwort'); if(fSetzFraWert(($v?true:false),'OhneAntwort','')) $bNeu=true; $amOhneAntwort=$v; // !!! mit Absicht vertauscht
   $v=max(min((int)txtVar('AnmerkZahl'),2),0); if(setzAdmWert(($v),'AnmerkZahl','')) $bNeu=true;
   $v=(int)txtVar('PunkteTeilen'); if(fSetzFraWert(($v?true:false),'PunkteTeilen','')) $bNeu=true;
   $v=max(min((int)txtVar('FragenFeldHoehe'),45),2); if(setzAdmWert(($v),'FragenFeldHoehe','')) $bNeu=true;
   $v=max(min((int)txtVar('AntwortFeldHoehe'),15),2); if(setzAdmWert(($v),'AntwortFeldHoehe','')) $bNeu=true;
   $v=max(min((int)txtVar('AnmerkFeldHoehe'),35),2); if(setzAdmWert(($v),'AnmerkFeldHoehe','')) $bNeu=true;
   $v=(int)txtVar('StripSlashes'); if(setzAdmWert(($v?true:false),'StripSlashes','')) $bNeu=true;
   $v=max(txtVar('ListenLaenge'),0); if(setzAdmWert($v,'ListenLaenge','')) $bNeu=true;
   $v=(int)txtVar('Rueckwaerts'); if(setzAdmWert(($v?true:false),'Rueckwaerts','')) $bNeu=true;
   $v=(int)txtVar('NutzerRueckw'); if(setzAdmWert(($v?true:false),'NutzerRueckw','')) $bNeu=true;
   $v=max(txtVar('NutzerLaenge'),1); if(setzAdmWert($v,'NutzerLaenge','')) $bNeu=true;
   $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('NutzerKontakt')))); if(setzAdmWert($v,'NutzerKontakt',"'")) $bNeu=true;
   $v=str_replace('  ',' ',txtVar('NutzerBetreff')); if(setzAdmWert($v,'NutzerBetreff','"')) $bNeu=true;
   $v=(int)txtVar('ErgebnisRueckw'); if(setzAdmWert(($v?true:false),'ErgebnisRueckw','')) $bNeu=true;
   $v=max(txtVar('ErgebnisLaenge'),1); if(setzAdmWert($v,'ErgebnisLaenge','')) $bNeu=true;
   $v=(int)txtVar('CKEditor'); if(setzAdmWert(($v?true:false),'CKEditor','')) $bNeu=true;
  }
  $v=max(min((int)txtVar('Breite'),1800),600); if(setzAdmWert($v,'Breite','')) $bNeu=true;
  $v=txtVar('Hilfe'); if(substr($v,-1,1)!='/') $v.='/'; if(substr($v,0,4)!='http') $v='https://'.$v; if(setzAdmWert(($v),'Hilfe',"'")) $bNeu=true;
  if($bNeu){ //Speichern
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Administrator-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 elseif(!$sMeld) $sMeld.='<p class="admMeld">Die Administrator-Einstellungen bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den Administrator-Bereich.</p>';
echo $sMeld.NL;

?>

<form action="konfAdmin.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
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
 <td><div><input type="checkbox" class="admRadio" name="SessionsAgent" value="1"<?php if($amSessionsAgent) echo ' checked="checked"'?> /> Browserkennung überwachen &nbsp; &nbsp;
 <input type="checkbox" class="admRadio" name="SessionsIPAddr" value="1"<?php if($amSessionsIPAddr) echo ' checked="checked"'?> /> IP-Adresse überwachen</div>
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

<tr class="admTabl"><td colspan="2" class="admSpa2">Die folgenden Einstellungen für den Administratorbereich gelten genauso für den Autorenbereich, falls dieser benutzt wird.</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Anzeigebreite der Administratorseiten kann auf Ihren Bildschirm abgestimmt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fensterbreite der<br>Administration</td>
 <td><input type="text" name="Breite" value="<?php echo $amBreite?>" style="width:4em" /> Pixel &nbsp;
 <span class="admMini">Empfehlung: nicht unter 920</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die für die Administrationsseiten verwendete online-Hilfe <i>LiesMich.htm</i> liegt in folgendem Verzeichnis:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Pfad zur online-Hilfe</td>
 <td><input type="text" name="Hilfe" value="<?php echo $amHilfe?>" style="width:99%" />
 <div class="admMini">aktuell: <i>https://www.server-scripts.de/testfragen/</i> &nbsp; &nbsp; &nbsp; (zur <a class="admMini" href="<?php echo $amHilfe?>LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;"><i>Hilfe-Datei</i></a>)</div></td>
</tr>

<tr class="admLeer">
 <td colspan="2"><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Das Formular zum Eingeben und Ändern der Fragen kann an Ihre Bedürfnisse angepasst werden.<a name="fr"> </a></td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Im Formular können bis zu 9 Antwortfelder und 2 Anmerkungsfelder vorhanden sein. Wieviele Felder sollen dargestellt werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortanzahl</td>
 <td><input type="text" name="AntwortZahl" value="<?php echo $amAntwortZahl?>" size="2" /> &nbsp; (<span class="admMini">2...9</span>)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antworteingabepflicht</td>
 <td><input type="checkbox" class="admRadio" name="OhneAntwort" value="1<?php if(!$amOhneAntwort) echo '" checked="checked'?>" /> &nbsp; (<span class="admMini">Empfehlung: aktivieren</span>)<div>In seltenen Ausnahmefällen kann es für den Druck von Fragenlisten zur offline-Verwendung sinnvoll sein, bei der Frageneingabe durch den Administrtator/Autor auch Fragen <i>ganz ohne</i> Auswahlantworten zuzulassen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Punkteverteilung</td>
 <td><input type="radio" class="admRadio" name="PunkteTeilen" value="1"<?php if($fsPunkteTeilen) echo ' checked="checked"'?> /> Punkte gleichmäßig auf die Antworten verteilen (Standard)<br>
 <input type="radio" class="admRadio" name="PunkteTeilen" value="0"<?php if(!$fsPunkteTeilen) echo ' checked="checked"'?> /> Punkte pro Antwort individuell eingeben</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkungsanzahl</td>
 <td><input type="text" name="AnmerkZahl" value="<?php echo $amAnmerkZahl?>" size="2" /> &nbsp; (<span class="admMini">0...2</span>)</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Fragen und Anmerkungen können formatierten Text enthalten. Der Editor für diese Felder im Eingabeformular kann entweder ein BB-Code-Editor oder der CKEditor für HTML-Text sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabeeditor</td>
 <td><input type="radio" class="admRadio" name="CKEditor" value="0"<?php if(!$amCKEditor) echo ' checked="checked"'?> /> BB-Editor &nbsp; &nbsp; <input type="radio" class="admRadio" name="CKEditor" value="1"<?php if($amCKEditor) echo ' checked="checked"'?> /> CKEditor
 <div class="admMini"><u>Hinweis</u>: Die von den Editoren erzeugten Formatierungsn sind miteinander nicht verträglich. Wenn Sie jemals nachträglich den Editor wechseln, wird zwar die Fragendarstellung im Besucherbereich weiterhin korrekt erfolgen, die Darstellung in den Eingabeformularen im Admin-Bereich wird jedoch nicht optimal an den anderen Editor angepasst sein.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Größe der Eingabefelder für Frage und Antworten kann angepasst werden. Wie hoch sollen diese Felder sein?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenfeldhöhe</td>
 <td><input type="text" name="FragenFeldHoehe" value="<?php echo $amFragenFeldHoehe?>" size="2" /> Zeilen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortfeldhöhe</td>
 <td><input type="text" name="AntwortFeldHoehe" value="<?php echo $amAntwortFeldHoehe?>" size="2" /> Zeilen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkungsfeldhöhe</td>
 <td><input type="text" name="AnmerkFeldHoehe" value="<?php echo $amAnmerkFeldHoehe?>" size="2" /> Zeilen</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Einige PHP-Systeme behandeln eingegebenen \-Backslash-Zeichen nicht korrekt. Manchmal werden die eingegebenen \-Zeichen unzulässig entfernt, machmal auch zu \\-Zeichen verdoppelt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Backslash-Korrektur</td>
 <td><input type="radio" class="admRadio" name="StripSlashes" value="0"<?php if(!$amStripSlashes) echo ' checked="checked"'?> /> \-Zeichen beibehalten &nbsp; <input type="radio" class="admRadio" name="StripSlashes" value="1"<?php if($amStripSlashes) echo ' checked="checked"'?> /> doppelte \\-Zeichen entfernen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragenliste im Administratorbereich kann angepasst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listensortierung</td>
 <td><input type="radio" class="admRadio" name="Rueckwaerts" value=""<?php if(!$amRueckwaerts) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input type="radio" class="admRadio" name="Rueckwaerts" value="1"<?php if($amRueckwaerts) echo ' checked="checked"'?> /> absteigend &nbsp; nach der Fragennummer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td><input type="text" name="ListenLaenge" value="<?php echo $amListenLaenge?>" size="2" /> Fragezeilen auf einer Listenseite der Fragenliste
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Fragen pro Seite</div></td>
</tr>

<tr class="admLeer">
 <td colspan="2"><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ergebnisliste im Administratorbereich kann angepasst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listensortierung</td>
 <td><input type="radio" class="admRadio" name="ErgebnisRueckw" value=""<?php if(!$amErgebnisRueckw) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input type="radio" class="admRadio" name="ErgebnisRueckw" value="1"<?php if($amErgebnisRueckw) echo ' checked="checked"'?> /> absteigend &nbsp; nach der lfd. Ergebnisnummer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td><input type="text" name="ErgebnisLaenge" value="<?php echo $amErgebnisLaenge?>" size="2" /> Ergebniszeilen auf einer Listenseite der Ergebnisliste
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Ergebnisse pro Seite</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Liste der Benutzerübersicht im Administrationsbereich kann angepasst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listensortierung</td>
 <td><input type="radio" class="admRadio" name="NutzerRueckw" value=""<?php if(!$amNutzerRueckw) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input type="radio" class="admRadio" name="NutzerRueckw" value="1"<?php if($amNutzerRueckw) echo ' checked="checked"'?> /> absteigend &nbsp; nach den Benutzernummer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td><input type="text" name="NutzerLaenge" value="<?php echo $amNutzerLaenge?>" size="2"<?php if(!FRA_Nutzerverwaltung) echo ' style="color:#8C8C8C;"'?> /> Zeilen mit Benutzerdaten auf einer Listenseite der Benutzerübersicht
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Benutzer pro Seite</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Administrator kann aus der Benutzerübersicht heraus mit den Benutzern per E-Mail Kontakt aufnehmen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerbetreff</td>
 <td><input type="text" name="NutzerBetreff" value="<?php echo $amNutzerBetreff?>" style="width:99%;" /><div class="admMini">(Dieser Text wird als überschreibbarer Betreff für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerkontakt</td>
 <td><textarea name="NutzerKontakt" cols="120" rows="8" style="width:99%;height:150px;"><?php echo str_replace('\n ',"\n",$amNutzerKontakt)?></textarea><div class="admMini">(Dieser Text wird als überschreibbare Standardvorlage für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADF_'.$n)){
  $p=strpos($sWerte,'ADF_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADF_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>