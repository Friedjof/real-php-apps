<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Einbettung','','Afr');
?>

<p class="admMeld">Auf dieser Seite finden Sie Beispiele für den Aufruf des Kalender-Scripts.</p>

<p class="admMeld">direkte Linkadressen:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1"">Frameset</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>frame.html"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Terminliste</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalender.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Suchformular</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalender.php?kal_Aktion=suche"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Termineintrag</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalender.php?kal_Aktion=eingabe"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Benutzer-<br />anmeldung</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalender.php?kal_Aktion=login"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Minikalender</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>miniKalender.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">aktuelle Termine</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>aktuelleTermine.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">laufende Termine</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>laufendeTermine.php"></td>
</tr><tr class="admTabl">
<td class="admSpa1">CronJob</td>
<td><input style="width:100%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalCronJob.php?kal=<?php echo KAL_Schluessel?>"></td>
</tr>
</table><br />
</form>

<p class="admMeld">Aufruf im iFrame:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Terminliste</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;kalender&quot; src=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalender.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;600&quot;&gt;
 Ihr Browser zeigt keine iFrames.
&lt;/iframe&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">Minikalender</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;minikalender&quot; src=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>miniKalender.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;180&quot; height=&quot;200&quot;&gt;
 Ihr Browser zeigt keine iFrames.
&lt;/iframe&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">aktuelle Termine</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;aktuelles&quot; src=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>aktuelleTermine.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;600&quot; height=&quot;300&quot;&gt;
 Ihr Browser zeigt keine iFrames.
&lt;/iframe&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">laufende Termine</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;laufendes&quot; src=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>laufendeTermine.php&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;600&quot; height=&quot;300&quot;&gt;
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
 &lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>kalStyles.css&quot;&gt;
</div><br />
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Terminliste</td>
<td><textarea cols="80" rows="4" style="height:5.3em;">
&lt;?php
 include_once '<?php echo KAL_Pfad?>kalWerte.php';
 include '<?php echo KAL_Pfad?>kalender.php';
?&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">Minikalender</td>
<td><textarea cols="80" rows="4" style="height:5.3em;">
&lt;?php
 include_once '<?php echo KAL_Pfad?>kalWerte.php';
 include '<?php echo KAL_Pfad?>miniKalender.php';
?&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">aktuelle Termine</td>
<td><textarea cols="80" rows="4" style="height:5.3em;">
&lt;?php
 include_once '<?php echo KAL_Pfad?>kalWerte.php';
 include '<?php echo KAL_Pfad?>aktuelleTermine.php';
?&gt;</textarea></td>
</tr><tr class="admTabl">
<td class="admSpa1">laufende Termine</td>
<td><textarea cols="80" rows="4" style="height:5.3em;">
&lt;?php
 include_once '<?php echo KAL_Pfad?>kalWerte.php';
 include '<?php echo KAL_Pfad?>laufendeTermine.php';
?&gt;</textarea></td>
</tr>
</table>
</form>

<?php echo fSeitenFuss() ?>