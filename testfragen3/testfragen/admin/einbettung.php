<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Einbettung','','Afr');
?>

<p class="admMeld">Auf dieser Seite finden Sie Beispiele für den Aufruf des Testfragen-Scripts.</p>

<p class="admMeld">direkte Linkadressen:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Frameset</td>
<td><input style="width:99%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo FRA_Www?>index.html"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Testfragen</td>
<td><input style="width:99%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo FRA_Www?>frage.php<?php if(KONF)echo'?fra_Ablauf='.KONF?>"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Statistik</td>
<td><input style="width:99%" type="text" value="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo FRA_Www?>frage.php?fra_Aktion=statistik<?php if(KONF)echo'&fra_Ablauf='.KONF?>"></td>
</tr>
</table><br />
</form>

<p class="admMeld">Aufruf im iFrame:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">im&nbsp;i-Frame</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;fragen&quot; src=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo FRA_Www?>frage.php<?php if(KONF)echo'?fra_Ablauf='.KONF?>&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;600&quot;&gt;
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
 &lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo FRA_Www?>fraStyle.css&quot;&gt;
</div><br />
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Fragenliste</td>
<td><textarea cols="80" rows="8" style="height:8.5em;">
&lt;?php
 include_once '<?php echo FRA_Pfad?>fraWerte.php';
<?php if(KONF>0){?>
 $_GET['fra_Ablauf']=<?php echo KONF?>;
 $_POST['fra_Ablauf']=<?php echo KONF?>;
<?php }?>
 include '<?php echo FRA_Pfad?>frage.php';
?&gt;</textarea></td>
</tr>
</table>
</form>

<?php echo fSeitenFuss();?>