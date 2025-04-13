<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Übersicht','','Idx');

if($bHttp=(($f=@fopen($sHttp.'grafik/icon_Kopie.gif','r'))?true:false)) fclose($f);
$sIcon=($bHttp?$sHttp:KALPFAD).'grafik/iconAendern.gif';
?>

<p class="admMeld">Kalender-Script (Autorenbereich) - Version <?php $kalVersion='???'; @include(KALPFAD.'kalVersion.php'); echo trim(substr($kalVersion,0,3)).' ('.trim(substr($kalVersion,4).')');?></p>
<?php if($kalVersion!=KAL_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $kalVersion?> sind bereits auf dem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>kalWerte.php</i> noch auf dem früheren Stand <?php echo KAL_Version?>.
Der Administrator muss jetzt unbedingt den Menüpunkt <img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Update einpflegen">
<u>Setup/Update</u> aufrufen, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php
 }
 if(!(file_exists('liste.php')||file_exists('eingabe.php')||file_exists('freigabe.php')||file_exists('suche.php')||file_exists('nutzerListe.php')||file_exists('nachrListen.php')||file_exists('export.php')||file_exists('import.php')||file_exists('zusageListe.php'))){
  echo '<p class="admFehl">Der Autorenbereich wurde vom Administrator noch nicht eingerichtet. <a href="'.ADM_Hilfe.'LiesMich.htm#4.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" alt="online-Hilfe aufrufen" title="online-Hilfe aufrufen"></a></p>'."\n";
 }
?>

<p class="admMeld">Überblick zur Datenstatistik</p>
<?php
 $nT=0; $nV=0; $nF=0; $nN=0; $nM=0; $nE=0; $nB=0; $nZ=0;
 if(!KAL_SQL){
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Termine); if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
  for($i=1;$i<$nSaetze;$i++){
   $r=explode(';',substr($a[$i],0,10)); $s=(isset($r[1])?$r[1]:''); if($s=='1') $nT++; elseif($s=='2') $nV++; else $nF++;
  }
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Nutzer);  if(is_array($a)) $nN=max(count($a)-1,0);
  $a=@file(KAL_Pfad.KAL_Daten.KAL_MailAdr); if(is_array($a)) $nM=max(count($a)-1,0);
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Erinner); if(is_array($a)) $nE=max(count($a)-1,0);
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Benachr); if(is_array($a)) $nB=max(count($a)-1,0);
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Zusage); if(is_array($a)) $nZ=max(count($a)-1,0);
 }elseif($DbO){
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabT.' WHERE online="1"')) {if($a=$rR->fetch_row()) $nT=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabT.' WHERE online="2"')) {if($a=$rR->fetch_row()) $nV=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabT.' WHERE online="0"')) {if($a=$rR->fetch_row()) $nF=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.KAL_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabM)){if($a=$rR->fetch_row()) $nM=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabE)){if($a=$rR->fetch_row()) $nE=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabB)){if($a=$rR->fetch_row()) $nB=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.KAL_SqlTabZ)){if($a=$rR->fetch_row()) $nZ=$a[0]; $rR->close();}
 }else echo '<p class="admFehl">MySQL-Datenbank nicht geöffnet!</p>';
?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('liste.php')){?><a href="liste.php?kal_Onl=1&amp;kal_Ofl=0&amp;kal_Vmk=0" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Terminliste</td>
<td><?php echo $nT?> veröffentlichte Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('freigabe.php')){?><a href="freigabe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Terminliste</td>
<td><?php echo $nV?> vorgeschlagene Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('liste.php')){?><a href="liste.php?kal_Ofl=1&amp;kal_Onl=0&amp;kal_Vmk=0" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Terminliste</td>
<td><?php echo $nF?> deaktivierte Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nutzerListe.php')){?><a href="nutzerListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo (!KAL_SQL?KAL_Nutzer:KAL_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nachrListen.php')){?><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Mail-Adressen</td>
<td><?php echo $nM?> Adressen für Erinnerungen/Benachrichtigungen in <i><?php echo (!KAL_SQL?KAL_MailAdr:KAL_SqlTabM)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nachrListen.php')){?><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Erinnerungen</td>
<td><?php echo $nE?> Erinnerungswünsche zu anstehenden Terminen in <i><?php echo (!KAL_SQL?KAL_Erinner:KAL_SqlTabE)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nachrListen.php')){?><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Benachrichtigungen</td>
<td><?php echo $nB?> Benachrichtigungswünsche zu Terminänderungen in <i><?php echo (!KAL_SQL?KAL_Benachr:KAL_SqlTabB)?></i></td>
</tr><?php if(defined('KAL_ZusageSystem')&&KAL_ZusageSystem){?>
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('zusageListe.php')){?><a href="zusageListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Zusagen</td>
<td><?php echo $nZ?> Zusagen zu Terminen in <i><?php echo (!KAL_SQL?KAL_Zusage:KAL_SqlTabZ)?></i></td>
</tr><?php }?>
<tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;">Datenbasis</td>
<td><?php echo(KAL_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!KAL_SQL?KAL_Daten:KAL_SqlHost.'.'.KAL_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;">E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(KAL_Empfaenger,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;">E-Mail-Absender</td>
<td><?php echo htmlspecialchars(KAL_Sender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;">Envelope Sender</td>
<td><?php echo htmlspecialchars(KAL_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;">Sende-Automat</td>
<td><?php echo htmlspecialchars(KAL_KeineAntwort,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="delTemp.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
</tr><?php if(ADM_AuthCronJob){?><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="<?php echo ($bHttp?$sHttp:KALPFAD)?>kalCronJob.php?kal=<?php echo KAL_Schluessel?>"  target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> CronJob</td>
<td>Programm <i>kalCronJob.php</i> im Besucherordner probeweise von Hand aufrufen.</td>
</tr><?php }?>
</table><br />

<?php if($bAdmLoginOK){ ?>

<p class="admMeld">Sicherheit</p>
<div class="admBox">
<p>Sie haben momentan den scriptbasierten Zugangsschutz
zum Autorenbereich eingeschaltet und verwenden ausdrücklich nicht
den als sicher geltenden serverseitigen Zugangsschutz zum Autorenordner.</p>
</div>

<?php
 }
echo fSeitenFuss();
?>