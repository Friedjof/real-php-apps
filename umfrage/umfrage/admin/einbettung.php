<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Einbettung','','Afr');
?>

<p class="admMeld">Auf dieser Seite finden Sie Beispiele für den Aufruf des Umfrage-Scripts.</p>

<p class="admMeld">direkte Linkadressen:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Frameset</td>
<td><input style="width:98%" type="text" value="http://<?php echo UMF_Www?>index.html"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Umfrage</td>
<td><input style="width:98%" type="text" value="http://<?php echo UMF_Www?>umfrage.php<?php if(KONF)echo'?umf_Ablauf='.KONF?>"></td>
</tr><tr class="admTabl">
<td class="admSpa1">Grafik</td>
<td><input style="width:98%" type="text" value="http://<?php echo UMF_Www?>umfrage.php?<?php if(KONF)echo'umf_Ablauf='.KONF.'&'?>umf_Aktion=grafik"></td>
</tr>
</table><br />
</form>

<p class="admMeld">Aufruf im iFrame:</p>
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">im&nbsp;iFrame</td>
<td><textarea cols="80" rows="5" style="height:7em">
&lt;iframe name=&quot;umfrage&quot; src=&quot;http://<?php echo UMF_Www?>umfrage.php<?php if(KONF)echo'?umf_Ablauf='.KONF?>&quot; marginwidth=&quot;0&quot; marginheight=&quot;0&quot; border=&quot;0&quot; frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;600&quot;&gt;
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
 &lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;http://<?php echo UMF_Www?>umfStyle.css&quot;&gt;
</div><br />
<form>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1">Umfrage</td>
<td><textarea cols="80" rows="8" style="height:8.5em;">
&lt;?php
 include_once '<?php echo UMF_Pfad?>umfWerte.php';
<?php if(KONF>0){?>
 $_GET['umf_Ablauf']=<?php echo KONF?>;
 $_POST['umf_Ablauf']=<?php echo KONF?>;
<?php }?>
 include '<?php echo UMF_Pfad?>umfrage.php';
?&gt;</textarea></td>
</tr>
</table>
</form>

<?php echo fSeitenFuss();?>