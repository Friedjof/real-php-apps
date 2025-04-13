<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Einbettung','','Afr');
 $sProto='http'.(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443'?'s':'').'://';
 if(!is_dir(MP_Pfad.MP_Daten)) echo '<p class="admFehl">Bitte zuerst die Pfade im Setup einstellen!</p>'.NL;
?>

<p class="admMeld">Auf dieser Seite finden Sie Beispiele für den Aufruf des Marktplatz-Scripts.</p>

<p class="admMeld">direkte Linkadressen:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Frameset</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>index.html"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Übersichtsliste</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>marktplatz.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Inserateliste</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>marktplatz.php?mp_Aktion=liste&amp;mp_Segment=1"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Benutzer-<br />anmeldung</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>marktplatz.php?mp_Aktion=login"></td>
</tr><tr class="admTabl">
<td class="admSpa1">neue Inserate</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>neueInserate.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">CronJob</td>
<td><input style="width:99%" type="text" value="<?php echo $sProto.MP_Www?>mpCronJob.php?mp=<?php echo MP_Schluessel?>"></td>
</tr>
</table><br />
</form>

<p class="admMeld">Aufruf im iFrame:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Inseratemarkt</td>
<td><textarea cols="80" rows="5" style="width:99%;height:7em">
&lt;iframe name=&quot;markt&quot; src=&quot;<?php echo $sProto.MP_Www?>marktplatz.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;600&quot;&gt;
 Ihr Browser zeigt keine iFrames.
&lt;/iframe&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">neue Inserate</td>
<td><textarea cols="80" rows="5" style="width:99%;height:7em">
&lt;iframe name=&quot;neues&quot; src=&quot;<?php echo $sProto.MP_Www?>neueInserate.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;600&quot; height=&quot;300&quot;&gt;
 Ihr Browser zeigt keine iFrames.
&lt;/iframe&gt;</textarea></td>
</tr>
</table>
<p>Die passenden Werte für Breite und Höhe müssen Sie selbst experimentell ermitteln.</p>
<br />
</form>

<p class="admMeld">Einbettung in PHP-Seiten per <i>include</i>:</p>
<p>In jedem Fall müssen Sie vor dem Befehl <i>include</i> auf Ihrer eigenen PHP-Seite
so weit oben wie möglich, möglichst im &lt;head&gt;...&lt;/head&gt;-Bereich
die folgende Anweisung platzieren:</p>
<div class="admBox">
 &lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;<?php echo $sProto.MP_Www?>mpStyles.css&quot;&gt;
</div><br />
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Übersichtsliste</td>
<td><textarea cols="80" rows="4" style="width:99%;height:5.3em;">
&lt;?php
 include_once '<?php echo MP_Pfad?>mpWerte.php';
 include '<?php echo MP_Pfad?>marktplatz.php';
?&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">neue Inserate</td>
<td><textarea cols="80" rows="4" style="width:99%;height:5.3em;">
&lt;?php
 include_once '<?php echo MP_Pfad?>mpWerte.php';
 include '<?php echo MP_Pfad?>neueInserate.php';
?&gt;</textarea></td>
</tr>
</table>
</form>

<?php echo fSeitenFuss() ?>