<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Übersicht','','Idx');

if($bHttp=(($f=@fopen($sHttp.'grafik/icon_Kopie.gif','r'))?true:false)) fclose($f);
$sIcon=($bHttp?$sHttp:KALPFAD).'grafik/icon_Aendern.gif';

$sHost='www.kalender-script.de'; $sNewVer='???'; $nErrNo=0; $sErrStr=''; $sRcv='';
$sRq="GET /version/getVer.php?p=".time()." HTTP/1.1\r\nHost: ".$sHost."\r\nConnection: Close\r\n\r\n";
if($f=@fsockopen('ssl://'.$sHost,443,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}elseif($f=@fsockopen($sHost,80,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}//else echo $nErrNo.': '.$sErrStr;
if($sRcv){$sRcv=str_replace("\r",'',trim($sRcv)); if($p=strpos($sRcv,"\n\n")) $sNewVer=trim(substr($sRcv,++$p));}
?>

<p class="admMeld">Kalender-Script-Administration - Version <?php $kalVersion='???'; @include(KALPFAD.'kalVersion.php'); echo trim(substr($kalVersion,0,3)).' ('.trim(substr($kalVersion,4).')');?></p>
<?php if(strlen(KAL_Www)==0){?>
<div class="admBox"><p class="admFehl">Setup-Warnung:</p>
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfSetup.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Setup ausführen"></a>
<a href="konfSetup.php" title="Setup">Setup</a> auf, um das Programm einzurichten.</div><br />
<?php } if($kalVersion!=KAL_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $kalVersion?> sind bereits auf Ihrem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>kalWerte.php</i> noch auf dem früheren Stand <?php echo KAL_Version?>.
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfUpdate.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Update einpflegen"></a>
<a href="konfUpdate.php" title="Update">Update</a> auf, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php } else if($sNewVer!='???'&&$sNewVer!=trim(substr($kalVersion,4))){?>
<div class="admBox"><p class="admErfo">Versions-Hinweis:</p>
Sie verwenden derzeit Version <?php echo $kalVersion?>.
Für registrierte Lizenznehmer ist Version <?php echo $sNewVer?> verfügbar.
Bitte informieren Sie sich, ob diese Version für Sie <a href="https://<?php echo $sHost?>/version3.html" target="_blank">nützliche Neuerungen</a> enthält.</div><br />
<?php }?>

<p class="admMeld">Überblick zur Datenstatistik</p>
<?php
 $nT=0; $nV=0; $nF=0; $nD=0; $nN=0; $nM=0; $nE=0; $nB=0; $nZ=0;
 if(!KAL_SQL){
  $a=@file(KAL_Pfad.KAL_Daten.KAL_Termine); if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
  for($i=1;$i<$nSaetze;$i++){
   $r=explode(';',substr($a[$i],0,10)); $s=(isset($r[1])?$r[1]:''); if($s=='1') $nT++; elseif($s=='2') $nV++; elseif($s=='3') $nD++; else $nF++;
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
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabT.' WHERE online="3"')) {if($a=$rR->fetch_row()) $nD=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.KAL_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabM)){if($a=$rR->fetch_row()) $nM=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabE)){if($a=$rR->fetch_row()) $nE=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(id) FROM '.KAL_SqlTabB)){if($a=$rR->fetch_row()) $nB=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.KAL_SqlTabZ)){if($a=$rR->fetch_row()) $nZ=$a[0]; $rR->close();}
 }else echo '<p class="admFehl">MySQL-Datenbank nicht geöffnet!</p>';
?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="liste.php?kal_Onl=1&amp;kal_Ofl=0&amp;kal_Vmk=0" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Terminliste</td>
<td><?php echo $nT?> veröffentlichte Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="freigabe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Terminliste</td>
<td><?php echo $nV?> vorgeschlagene Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="liste.php?kal_Ofl=1&amp;kal_Onl=0&amp;kal_Vmk=0" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Terminliste</td>
<td><?php echo $nF?> deaktivierte Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="terminLoeschung.php" title="löschen"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Terminliste</td>
<td><?php echo $nD?> zu löschende Termine in <i><?php echo (!KAL_SQL?KAL_Termine:KAL_SqlTabT)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="nutzerListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo (!KAL_SQL?KAL_Nutzer:KAL_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Mail-Adressen</td>
<td><?php echo $nM?> Adressen für Erinnerungen/Benachrichtigungen in <i><?php echo (!KAL_SQL?KAL_MailAdr:KAL_SqlTabM)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Erinnerungen</td>
<td><?php echo $nE?> Erinnerungswünsche zu anstehenden Terminen in <i><?php echo (!KAL_SQL?KAL_Erinner:KAL_SqlTabE)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="nachrListen.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Benachrichtigungen</td>
<td><?php echo $nB?> Benachrichtigungswünsche zu Terminänderungen in <i><?php echo (!KAL_SQL?KAL_Benachr:KAL_SqlTabB)?></i></td>
</tr><?php if(defined('KAL_ZusageSystem')&&KAL_ZusageSystem){?>
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="zusageListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Zusagen</td>
<td><?php echo $nZ?> Zusagen zu Terminen in <i><?php echo (!KAL_SQL?KAL_Zusage:KAL_SqlTabZ)?></i></td>
</tr><?php }?>
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfDaten.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Datenbasis</td>
<td><?php echo(KAL_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!KAL_SQL?KAL_Daten:KAL_SqlHost.'.'.KAL_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(KAL_Empfaenger.(defined('KAL_EmpfTermin')&&strpos(KAL_EmpfTermin,'@')>0?', '.KAL_EmpfTermin:'').(defined('KAL_EmpfNutzer')&&strpos(KAL_EmpfNutzer,'@')>0?', '.KAL_EmpfNutzer:'').(KAL_ZusageSystem&&defined('KAL_EmpfZusage')&&strpos(KAL_EmpfZusage,'@')>0?', '.KAL_EmpfZusage:''),ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Absender</td>
<td><?php echo htmlspecialchars(KAL_Sender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Envelope Sender</td>
<td><?php echo htmlspecialchars(KAL_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Sende-Automat</td>
<td><?php echo htmlspecialchars(KAL_KeineAntwort,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfAllgemein.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Geheimschlüssel</td>
<td><?php echo KAL_Schluessel?> - Niemals verändern!<div class="admMini">(Nur notieren für eine eventuelle Neuinstallation des Programms bei vorhandenen Daten.)</div></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="delTemp.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bereinigen"></a> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="<?php echo ($bHttp?$sHttp:KALPFAD)?>kalCronJob.php?kal=<?php echo KAL_Schluessel?>"  target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="aufrufen"></a> CronJob</td>
<td>Programm <i>kalCronJob.php</i> im Besucherordner probeweise von Hand aufrufen.</td>
</tr><?php if(ADM_Breite<950){?><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfAdmin.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Administrationsbreite</td>
<td><?php echo ADM_Breite?> Pixel. Je nach Bildschirm sollten Sie mindestens 950, besser 1000 Pixel Breite einstellen.</td>
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
echo fSeitenFuss();
?>