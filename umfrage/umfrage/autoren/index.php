<?php
@include 'hilfsFunktionen.php';
if(!defined('NL')){ //keine hilfsFunktionen.php
 if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME']; $sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
 $sAwww='http://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}
echo fSeitenKopf('Übersicht Autorenbereich','','Idx');

$sKonf='Grundkonfiguration'; if(isset($aKonf)) foreach($aKonf as $k=>$v) if($v>0) $sKonf.=', '.$v ;
?>

<p class="admMeld">Umfrage-Script - <?php echo (KONF<=0?'Grundkonfiguration':'Konfiguration-'.KONF) ?> - Version <?php $umfVersion='???'; @include(UMFPFAD.'umfVersion.php'); echo trim(substr($umfVersion,0,3)).' ('.trim(substr($umfVersion,4).')');?></p>
<?php if(strlen(UMF_Www)==0||!file_exists(UMF_Pfad.'umfVersion.php')){?>
<div class="admBox"><p class="admFehl">Setup-Warnung:</p>
Bitte lassen Sie jetzt unbedingt den Administrator den Menüpunkt <a href="../admin/konfSetup.php"><img src="iconAendern.gif" width="12" height="13" border="0" alt="Setup ausführen"></a>
<a href="../admin/konfSetup.php" title="Setup">Setup/Update</a> aufrufen, um das Programm einzurichten.</div><br />
<?php } if($umfVersion!=UMF_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $umfVersion?> sind bereits auf Ihrem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>umfWerte<?php if(KONF>0)echo KONF?>.php</i> noch auf dem früheren Stand <?php echo UMF_Version?>.
Bitte lassen Sie jetzt den Administrator unbedingt den Menüpunkt <a href="konfUpdate.php"><img src="iconAendern.gif" width="12" height="13" border="0" alt="Update einpflegen"></a>
<a href="konfUpdate.php" title="Update">Setup/Update</a> aufrufen, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php
 }
 if(!(file_exists('fragenListe.php')||file_exists('fragenEingabe.php')||file_exists('fragenSuche.php')||file_exists('nutzerListe.php')||file_exists('ergebnisListe.php')||file_exists('teilnahmeListe.php')||file_exists('fragenExport.php')||file_exists('fragenImport.php'))){
  echo '<p class="admFehl">Der Autorenbereich wurde vom Administrator noch nicht eingerichtet.</p>'."\n";
 }
?>

<p class="admMeld">Überblick zu Konfigurationen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><span style="width:12px;display:inline-block">&nbsp;</span> Konfigurationen</td>
<td><?php if(isset($aKonf)) echo count($aKonf)?> Konfigurationen angelegt<div class="admMini">(<?php echo $sKonf?>)</div></td>
</tr>
</table><br />

<p class="admMeld">Datenüberblick zur <?php echo (KONF<=0?'Grundkonfiguration':'Konfiguration-'.KONF)?></p>
<?php
 $nF=0; $nN=0; $nE=0; $nT=0;
 if(!UMF_SQL){
  $a=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
  for($i=1;$i<$nSaetze;$i++){
   $r=explode(';',substr($a[$i],0,10)); $s=(isset($r[1])?$r[1]:''); if($s!='') $nF++;
  }
  $a=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); if(is_array($a)) $nE=max(count($a)-1,0);
  $a=@file(UMF_Pfad.UMF_Daten.UMF_Nutzer); if(is_array($a)) $nN=max(count($a)-1,0);
  $a=@file(UMF_Pfad.UMF_Daten.UMF_Teilnahme); if(is_array($a)) $nT=max(count($a)-1,0);
 }else{
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabF)){if($a=$rR->fetch_row()) $nF=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabE)){if($a=$rR->fetch_row()) $nE=Max($a[0]-1,0); $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabT)){if($a=$rR->fetch_row()) $nT=$a[0]; $rR->close();}
 }
?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('fragenListe.php')){?><a href="fragenListe.php<?php if(KONF>0)echo'?konf='.KONF?>" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> Fragenliste</td>
<td><?php echo $nF?> eingetragene Fragen in <i><?php echo(!UMF_SQL?UMF_Fragen:UMF_SqlTabF)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('ergebnisListe.php')){?><a href="ergebnisListe.php<?php if(KONF>0)echo'?konf='.KONF?>" title="anzeigen"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> Ergebnisliste</td>
<td><?php echo $nE?> dazugehörige Ergebnisse in <i><?php echo(!UMF_SQL?UMF_Ergebnis:UMF_SqlTabE)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('nutzerListe.php')){?><a href="nutzerListe.php<?php if(KONF>0)echo'?konf='.KONF?>" title="anzeigen"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo(!UMF_SQL?UMF_Nutzer:UMF_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('teilnahmeListe.php')){?><a href="teilnahmeListe.php<?php if(KONF>0)echo'?konf='.KONF?>" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> Teilnahmeliste</td>
<td><?php echo $nT?> eingetragene Teilnahmen in <i><?php echo(!UMF_SQL?UMF_Teilnahme:UMF_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><span style="width:12px;display:inline-block">&nbsp;</span> Datenbasis</td>
<td><?php echo(UMF_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!UMF_SQL?UMF_Daten:UMF_SqlHost.'.'.UMF_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<?php if(!defined('UMF_Empfaenger')){define('UMF_Empfaenger',(defined('UMF_MailTo')?UMF_MailTo:'??')); define('UMF_Sender',(defined('UMF_MailFrom')?UMF_MailFrom:'??'));}?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('konfEmail.php')){?><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(UMF_Empfaenger,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('konfEmail.php')){?><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> E-Mail-Absender</td>
<td><?php echo htmlspecialchars(UMF_Sender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><?php if(file_exists('konfEmail.php')){?><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;display:inline-block">&nbsp;</span>'?> Envelope Sender</td>
<td><?php echo htmlspecialchars(UMF_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"> Geheimschlüssel</td>
<td><?php echo UMF_Schluessel?> - Niemals verändern!<div class="admMini">(Nur notieren für eine eventuelle Neuinstallation des Programms bei vorhandenen Daten.)</div></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="delTemp.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bereinigen"></a> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
</tr><?php if(defined('ADU_Breite')&&ADU_Breite<950){?><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfAdmin.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Administrationsbreite</td>
<td><?php echo ADU_Breite?> Pixel. Je nach Bildschirm sollten Sie mindestens 950, besser 1000 Pixel Breite einstellen.</td>
</tr><?php }?>
</table><br />

<?php if($bAdmLoginOK){ ?>

<p class="admMeld">Sicherheit</p>
<div class="admBox">
<p>Sie haben momentan den scriptbasierten Zugangsschutz
zur Administration eingeschaltet und verwenden ausdrücklich nicht
den als sicher geltenden serverseitigen Zugangsschutz zum Administrationsordner.</p>
<?php if(file_exists('info.php')){?><p>Ausserdem ist die Datei <a href="info.php" target="hilfe" onclick="hlpWin(this.href);return false;">info.php</a> in der Administration vorhanden,
die nicht vom Zugangsschutz gesichert wird.
Falls Ihnen diese ungeschützte Datei als Sicherheitsrisiko erscheint,
so löschen Sie die Datei bitte.</p><?php }?>
</div>

<?php
 }
echo fSeitenFuss();?>