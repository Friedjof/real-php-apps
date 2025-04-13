<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Autorenbereich:: Übersicht','','Idx');

 if($bHttp=(($f=@fopen($sHttp.'grafik/iconKopie.gif','r'))?true:false)) fclose($f);
 $sIcon=($bHttp?$sHttp:MPPFAD).'grafik/iconAendern.gif';
?>

<p class="admMeld">Marktplatz-Script-Autorenbereich - Version <?php $mpVersion='???'; @include(MPPFAD.'mpVersion.php'); echo trim(substr($mpVersion,0,3)).' ('.trim(substr($mpVersion,4).')');?></p>
<?php if($mpVersion!=MP_Version){?>
<div class="admBox"><p class="admFehl">Versions-Warnung:</p>
Die Dateien zur Version <?php echo $mpVersion?> sind bereits auf Ihrem Server vorhanden,
jedoch ist die Variablen- und Einstelldatei <i>mpWerte.php</i> noch auf dem früheren Stand <?php echo MP_Version?>.
Der Administrator muss jetzt unbedingt den Menüpunkt <img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="Update einpflegen">
<u>Setup/Update</u> aufrufen, um die erneuerte Version endgültig einzupflegen.</div><br />
<?php
 }
 if(!(file_exists('liste.php')||file_exists('eingeben.php')||file_exists('freigeben.php')||file_exists('suche.php')||file_exists('nutzerListe.php')||file_exists('nachrListen.php')||file_exists('export.php')||file_exists('import.php'))){
  echo '<p class="admFehl">Der Autorenbereich wurde vom Administrator noch nicht eingerichtet. <a href="'.AM_Hilfe.'LiesMich.htm#4.2" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" alt="online-Hilfe aufrufen" title="online-Hilfe aufrufen"></a></p>'."\n";
 }
?>

<p class="admMeld">Überblick zur Datenstatistik</p>
<?php
 $nN=0; $nM=0; $nB=0; $nT=0; $nV=0; $nF=0;
 if(!MP_SQL){
  $a=@file(MP_Pfad.MP_Daten.MP_Nutzer);  if(is_array($a)) $nN=max(count($a)-1,0);
  $a=@file(MP_Pfad.MP_Daten.MP_MailAdr); if(is_array($a)) $nM=max(count($a)-1,0);
  $a=@file(MP_Pfad.MP_Daten.MP_Benachr); if(is_array($a)) $nB=max(count($a)-1,0);
 }else{
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabN)){if($a=$rR->fetch_row()) $nN=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabM)){if($a=$rR->fetch_row()) $nM=$a[0]; $rR->close();}
  if($rR=$DbO->query('SELECT COUNT(nr) FROM '.MP_SqlTabB)){if($a=$rR->fetch_row()) $nB=$a[0]; $rR->close();}
 }
 $aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); asort($aA); reset($aA); $sSegList=''; $nSegCnt=0;
 foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER'){
  $sSegList.=', '.(substr($aS[$k],0,1)!='~'&&substr($aS[$k],0,1)!='*'?$aS[$k]:substr($aS[$k],1)); $nSegCnt++;
  if(!MP_SQL){
   $a=@file(MP_Pfad.MP_Daten.sprintf('%02d',$k).MP_Inserate);
   if(is_array($a)) $nSaetze=count($a); else $nSaetze=0;
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
<td class="admSpa1" style="width:13em"><?php if(file_exists('konfSegmente.php')){?><a href="konfSegmente.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Segmente</td>
<td><?php echo $nSegCnt?> veröffentlichte Segmente unter Segmente</i><p class="admMini">(<?php echo substr($sSegList,2);?>)</p></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('liste.php')){?><a href="liste.php?mp_Onl=1&amp;mp_Ofl=0&amp;mp_Vmk=0<?php if($nSegNo) echo '&amp;seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Inseratelisten</td>
<td><?php echo $nT?> veröffentlichte Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('freigeben.php')){?><a href="freigeben.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Inseratelisten</td>
<td><?php echo $nV?> vorgeschlagene Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('liste.php')){?><a href="liste.php?mp_Ofl=1&amp;mp_Onl=0&amp;mp_Vmk=0<?php if($nSegNo) echo '&amp;seg='.$nSegNo?>" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Inseratelisten</td>
<td><?php echo $nF?> deaktivierte Inserate gesamt in allen <i><?php echo (!MP_SQL?'XX'.MP_Inserate:str_replace('%','XX',MP_SqlTabI))?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nutzerListe.php')){?><a href="nutzerListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Benutzerliste</td>
<td><?php echo $nN?> registrierte Benutzer in <i><?php echo (!MP_SQL?MP_Nutzer:MP_SqlTabN)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nachrListe.php')){?><a href="nachrListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Mail-Adressen</td>
<td><?php echo $nM?> Adressen für Benachrichtigungen in <i><?php echo (!MP_SQL?MP_MailAdr:MP_SqlTabM)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('nachrListe.php')){?><a href="nachrListe.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Benachrichtigungen</td>
<td><?php echo $nB?> Benachrichtigungswünsche zu Inserateänderungen in <i><?php echo (!MP_SQL?MP_Benachr:MP_SqlTabB)?></i></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('konfDaten.php')){?><a href="konfDaten.php"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bearbeiten"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Datenbasis</td>
<td><?php echo(MP_SQL?'MySQL':'Text');?>-Datenbank unter <i><?php echo (!MP_SQL?MP_Daten:MP_SqlHost.'.'.MP_SqlDaBa)?></i> aktiviert</td>
</tr>
</table><br />

<p class="admMeld">verwendete E-Mail-Adressen</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;"> E-Mail-Empfang</td>
<td><?php echo htmlspecialchars(MP_MailTo,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;"> E-Mail-Absender</td>
<td><?php echo htmlspecialchars(MP_MailFrom,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;"> Envelope Sender</td>
<td><?php echo htmlspecialchars(MP_EnvelopeSender,ENT_COMPAT,'ISO-8859-1')?></td>
</tr><tr class="admTabl">
<td class="admSpa1" style="width:13em;padding-left:18px;"> Sende-Automat</td>
<td><?php echo htmlspecialchars(MP_KeineAntwort,ENT_COMPAT,'ISO-8859-1')?></td>
</tr>
</table><br />

<p class="admMeld">Programmwartung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
<td class="admSpa1" style="width:13em"><?php if(file_exists('delTemp.php')){?><a href="delTemp.php" title="bearbeiten"><img src="<?php echo $sIcon?>" width="12" height="13" border="0" alt="bereinigen"></a><?php }else echo '<span style="width:12px;">&nbsp;</span>'?> Bereinigung</td>
<td>temporäre Dateien löschen, falls die regelmäßige automatische Löschung Reste hinterlassen hat</td>
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