<?php
@include 'hilfsFunktionen.php';
if(!defined('NL')){ //keine hilfsFunktionen.php
 if(!$sSelf=$_SERVER['PHP_SELF']) $sSelf=$_SERVER['SCRIPT_NAME']; $sSelf=str_replace("\\",'/',str_replace("\\\\",'/',$sSelf));
 $sAwww='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']).rtrim(dirname($sSelf),'/');
 header('Location: '.$sAwww.'/pfadTest.php');
 exit;
}
echo fSeitenKopf('Übersicht','','Idx');

$sKonf='Grundkonfiguration';if(isset($aKonf)) foreach($aKonf as $k=>$v) if($v>0) $sKonf.=', '.$v ;

$sHost='www.testfragen-script.de'; $sNewVer='???'; $nErrNo=0; $sErrStr=''; $sRcv='';
$sRq="GET /version/getVer.php?p=".time()." HTTP/1.1\r\nHost: ".$sHost."\r\nConnection: Close\r\n\r\n";
if($f=@fsockopen('ssl://'.$sHost,443,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}elseif($f=@fsockopen($sHost,80,$nErrNo,$sErrStr,15)){
 fwrite($f,$sRq); while(!feof($f)) $sRcv.=fgets($f,1024); fclose($f);
}//else echo $nErrNo.': '.$sErrStr;
if($sRcv){$sRcv=str_replace("\r",'',trim($sRcv)); if($p=strpos($sRcv,"\n\n")) $sNewVer=trim(substr($sRcv,++$p));}
?>

<p class="admMeld">Testfragen-Script-Administration - <?php echo (KONF<=0?'Grundkonfiguration':'Konfiguration-'.KONF) ?> - Version <?php $fraVersion='???'; @include(FRAPFAD.'fraVersion.php'); echo trim(substr($fraVersion,0,3)).' ('.trim(substr($fraVersion,4).')');?></p>
<?php if(strlen(FRA_Www)==0){?>
<div class="admBox"><p class="admFehl">Setup-Warnung:</p>
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfSetup.php"><img src="iconAendern.gif" width="12" height="13" border="0" alt="Setup ausführen"></a>
<a href="konfSetup.php" title="Setup">Setup/Update</a> auf, um das Programm einzurichten.</div><br />
<?php } if($fraVersion!=FRA_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $fraVersion?> sind bereits auf Ihrem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>fraWerte<?php if(KONF>0)echo KONF?>.php</i> noch auf dem früheren Stand <?php echo FRA_Version?>.
Bitte rufen Sie jetzt unbedingt den Menüpunkt <a href="konfSetup.php"><img src="iconAendern.gif" width="12" height="13" border="0" alt="Update einpflegen"></a>
<a href="konfSetup.php" title="Update">Setup/Update</a> auf, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php } else if($sNewVer!='???'&&$sNewVer>trim(substr($fraVersion,4))){?>
<div class="admBox"><p class="admErfo">Versions-Hinweis:</p>
Sie verwenden derzeit Version <?php echo $fraVersion?>.
Für registrierte Lizenznehmer ist Version <?php echo $sNewVer?> verfügbar.
Bitte informieren Sie sich, ob diese Version für Sie <a href="https://<?php echo $sHost?>/version3.html" target="_blank">nützliche Neuerungen</a> enthält.</div><br />
<?php }?>

<p class="admMeld">Überblick zu Konfigurationen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfKonf.php<?php if(KONF>0)echo'?konf='.KONF?>" title="auswählen"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Konfigurationen</td>
<td><?php if(isset($aKonf)) echo count($aKonf)?> Konfigurationen angelegt<div class="admMini">(<?php echo $sKonf?>)</div></td>
</tr>
</table><br />


<p class="admMeld">Datenüberblick zur <?php echo (KONF<=0?'Grundkonfiguration':'Konfiguration-'.KONF)?></p>
<?php
 $nF=0; $nN=0;
 if(!FRA_SQL){
  $a=@file(FRA_Pfad.FRA_Daten.FRA_Fragen); if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
  for($i=1;$i<$nSaetze;$i++){
   $r=explode(';',substr($a[$i],0,10)); $s=(isset($r[1])?$r[1]:''); if($s=='1') $nF++; elseif($s=='2') $nV++; else $nF++;
  }
  $a=@file(FRA_Pfad.FRA_Daten.FRA_Nutzer);  if(is_array($a)) $nN=max(count($a)-1,0);
 }else{
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.FRA_SqlTabF)){if($a=$rR->fetch_row()) $nF=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.FRA_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
 }
?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="liste.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Fragenliste</td>
<td><?php echo $nF?> veröffentlichte Fragen in <i><?php echo (!FRA_SQL?FRA_Fragen:FRA_SqlTabF)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="nutzerListe.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo (!FRA_SQL?FRA_Nutzer:FRA_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfDaten.php"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Datenbasis</td>
<td><?php echo(FRA_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!FRA_SQL?FRA_Daten:FRA_SqlHost.'.'.FRA_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<?php if(!defined('FRA_Empfaenger')){define('FRA_Empfaenger',(defined('FRA_MailTo')?FRA_MailTo:'??')); define('FRA_Sender',(defined('FRA_MailFrom')?FRA_MailFrom:'??'));}?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(FRA_Empfaenger,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> E-Mail-Absender</td>
<td><?php echo htmlspecialchars(FRA_Sender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfEmail.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Envelope Sender</td>
<td><?php echo htmlspecialchars(FRA_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfAllgemein.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Geheimschlüssel</td>
<td><?php echo FRA_Schluessel?> - Niemals verändern!<div class="admMini">(Nur notieren für eine eventuelle Neuinstallation des Programms bei vorhandenen Daten.)</div></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="delTemp.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bereinigen"></a> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
</tr><?php if(defined('ADF_Breite')&&ADF_Breite<950){?><tr class="admTabl">
<td class="admSpa1" style="width:14em"><a href="konfAdmin.php" title="bearbeiten"><img src="iconAendern.gif" width="12" height="13" border="0" alt="bearbeiten"></a> Administrationsbreite</td>
<td><?php echo ADF_Breite?> Pixel. Je nach Bildschirm sollten Sie mindestens 950, besser 1000 Pixel Breite einstellen.</td>
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