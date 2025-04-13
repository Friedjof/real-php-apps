<?php
global $nSegNo,$sSegNo,$sSegNam;
if(file_exists('hilfsFunktionen.php')) include 'hilfsFunktionen.php';
echo fSeitenKopf('Übersicht','','Idx');

if($bHttp=(($f=@fopen($sHttp.'grafik/iconKopie.gif','r'))?true:false)) fclose($f);
$sIcon=($bHttp?$sHttp:MPPFAD).'grafik/iconAendern.gif';

$sHost='www.marktplatz-script.de'; $sNewVer='???'; $nErrNo=0; $sErrStr=''; $sRcv='';
$sRq="GET /version/getVer.php?p=".time()." HTTP/1.1\r\nHost: ".$sHost."\r\nConnection: Close\r\n\r\n";
if($f=@fsockopen('ssl://'.$sHost,443,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}elseif($f=@fsockopen($sHost,80,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}//else echo $nErrNo.': '.$sErrStr;
if($sRcv){$sRcv=str_replace("\r",'',trim($sRcv)); if($p=strpos($sRcv,"\n\n")) $sNewVer=trim(substr($sRcv,++$p));}
?>

<p class="admMeld">Marktplatz-Script-Administration - Version <?php $mpVersion='???'; if(file_exists(MPPFAD.'mpVersion.php')) @include(MPPFAD.'mpVersion.php'); echo trim(substr($mpVersion,0,3)).' ('.trim(substr($mpVersion,4).')');?></p>
<?php if(strlen(MP_Www)==0){?>
<div class="admBox"><p class="admFehl">Setup-Warnung:</p>
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfSetup.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Setup ausführen"></a>
<a href="konfSetup.php" title="Setup">Setup</a> auf, um das Programm einzurichten.</div><br />
<?php } if($mpVersion!=MP_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $mpVersion?> sind bereits auf Ihrem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>mpWerte.php</i> noch auf dem früheren Stand <?php echo MP_Version?>.
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfUpdate.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Update einpflegen"></a>
<a href="konfUpdate.php" title="Update">Update</a> auf, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php } else if($sNewVer!='???'&&$sNewVer>trim(substr($mpVersion,4))){?>
<div class="admBox"><p class="admErfo">Versions-Hinweis:</p>
Sie verwenden derzeit Version <?php echo $mpVersion?>.
Für registrierte Lizenznehmer ist Version <?php echo $sNewVer?> verfügbar.
Bitte informieren Sie sich, ob diese Version für Sie <a href="https://<?php echo $sHost?>/version3.html" target="_blank">nützliche Neuerungen</a> enthält.</div><br />
<?php }?>

<p class="admMeld">Überblick zur Datenstatistik</p>
<?php
 $nN=0; $nM=0; $nB=0; $nT=0; $nV=0; $nF=0;
 if(!MP_SQL){
  if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer))  $a=@file(MP_Pfad.MP_Daten.MP_Nutzer);  else $a=array(); $nN=max(count($a)-1,0);
  if(file_exists(MP_Pfad.MP_Daten.MP_MailAdr)) $a=@file(MP_Pfad.MP_Daten.MP_MailAdr); else $a=array(); $nM=max(count($a)-1,0);
  if(file_exists(MP_Pfad.MP_Daten.MP_Benachr)) $a=@file(MP_Pfad.MP_Daten.MP_Benachr); else $a=array(); $nB=max(count($a)-1,0);
 }else{
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabM)){if($a=$rR->fetch_row()) $nM=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabB)){if($a=$rR->fetch_row()) $nB=$a[0]; $rR->close();}
 }
 $aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); asort($aA); reset($aA); $sSegList=''; $nSegCnt=0;
 foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER'){
  $sSegList.=', '.(substr($aS[$k],0,1)!='~'&&substr($aS[$k],0,1)!='*'?$aS[$k]:substr($aS[$k],1)); $nSegCnt++;
  if(!MP_SQL){
   if(file_exists(MP_Pfad.MP_Daten.sprintf('%02d',$k).MP_Inserate)) $a=@file(MP_Pfad.MP_Daten.sprintf('%02d',$k).MP_Inserate); else $a=array(); if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
   for($i=1;$i<$nSaetze;$i++){
    $r=explode(';',substr($a[$i],0,10)); $s=$r[1]; if($s=='1') $nT++; elseif($s=='2') $nV++; else $nF++;
   }
  }else{
   if($rR=$DbO->query('SELECT COUNT(nr) FROM '.str_replace('%',sprintf('%02d',$k),MP_SqlTabI).' WHERE online="1"')) {if($a=$rR->fetch_row()) $nT+=$a[0]; $rR->close();}
   if($rR=$DbO->query('SELECT COUNT(nr) FROM '.str_replace('%',sprintf('%02d',$k),MP_SqlTabI).' WHERE online="2"')) {if($a=$rR->fetch_row()) $nV+=$a[0]; $rR->close();}
   if($rR=$DbO->query('SELECT COUNT(nr) FROM '.str_replace('%',sprintf('%02d',$k),MP_SqlTabI).' WHERE online="0"')) {if($a=$rR->fetch_row()) $nF+=$a[0]; $rR->close();}
  }
 }
?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfSegmente.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Segmente</td>
<td><?php echo $nSegCnt?> veröffentlichte Segmente unter Segmente</i><p class="admMini">(<?php echo substr($sSegList,2);?>)</p></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="liste.php?mp_Onl=1&amp;mp_Ofl=0&amp;mp_Vmk=0<?php if($nSegNo) echo '&amp;seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Inseratelisten</td>
<td><?php echo $nT?> veröffentlichte Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="freigeben.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Inseratelisten</td>
<td><?php echo $nV?> vorgeschlagene Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="liste.php?mp_Ofl=1&amp;mp_Onl=0&amp;mp_Vmk=0<?php if($nSegNo) echo '&amp;seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Inseratelisten</td>
<td><?php echo $nF?> deaktivierte Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="nutzerListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo (!MP_SQL?MP_Nutzer:MP_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="nachrListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Mail-Adressen</td>
<td><?php echo $nM?> Adressen für Benachrichtigungen in <i><?php echo (!MP_SQL?MP_MailAdr:MP_SqlTabM)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="nachrListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Benachrichtigungen</td>
<td><?php echo $nB?> Benachrichtigungswünsche zu Inserateänderungen in <i><?php echo (!MP_SQL?MP_Benachr:MP_SqlTabB)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfDaten.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Datenbasis</td>
<td><?php echo(MP_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!MP_SQL?MP_Daten:MP_SqlHost.'.'.MP_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(MP_MailTo,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Absender</td>
<td><?php echo htmlspecialchars(MP_MailFrom,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Envelope Sender</td>
<td><?php echo htmlspecialchars(MP_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfEmail.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Sende-Automat</td>
<td><?php echo htmlspecialchars(MP_KeineAntwort,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1"><?php if((defined('AM_Breite')&&AM_Breite<950)||(defined('ADM_Breite')&&ADM_Breite<950)) {?>
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfAdmin.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Admin-Fenster</td>
<td><?php echo (defined('AM_Breite')?AM_Breite:ADM_Breite)?> &nbsp; <span class="admMini">(Breiten-Werte unter 950 Pixel sind ungünstig für die Darstellung.)</span></td>
</tr><?php }?><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="konfAllgemein.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a> Geheimschlüssel</td>
<td><?php echo MP_Schluessel?> - Niemals verändern!<div class="admMini">(Nur notieren für eine eventuelle Neuinstallation des Programms bei vorhandenen Daten.)</div></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="delTemp.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bereinigen"></a> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><a href="<?php echo ($bHttp?$sHttp:MPPFAD)?>mpCronJob.php?mp=<?php echo MP_Schluessel?>" target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="aufrufen"></a> CronJob</td>
<td>Programm <i>mpCronJob.php</i> im Besucherordner probeweise von Hand aufrufen.</td>
</tr>
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