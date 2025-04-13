<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Konfiguration sichern','','KSi');

$bFragen=true; $bFolgen=true; $bBilder=true; $bWerte=true; $aBld=false; $nFragen='keine';
if(!FRA_SQL){//Text
 $aF=@file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nFragen=(is_array($aF)?max(count($aF)-1,0):'keine');
}elseif($DbO){//SQL
 if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.FRA_SqlTabF)){
  if($a=$rR->fetch_row()) $nFragen=$a[0]; $rR->close();
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
}
if($H=@opendir(substr(FRA_Pfad.FRA_Bilder,0,-1))){//Bilder
 while($sF=readdir($H)) if($sF!='.'&&$sF!='..'&&$sF!='index.html'&&$sF!='.htaccess'){
  if(!is_dir(FRA_Pfad.FRA_Bilder.$sF)) $aBld[]=$sF;
  elseif($H2=@opendir(FRA_Pfad.FRA_Bilder.$sF)){
   while($sF2=readdir($H2)) if($sF2!='.'&&$sF2!='..'&&$sF2!='index.html'&&$sF2!='.htaccess'&&is_file(FRA_Pfad.FRA_Bilder.$sF.'/'.$sF2)) $aBld[]=$sF.'/'.$sF2;
   closedir($H2);
 }}
 closedir($H);
}
$bVerso=true&&file_exists(FRA_Pfad.'fraVersion.php');
$bStyle=true&&file_exists(FRA_Pfad.'fraStyle.css');
$bIndex=true&&file_exists(FRA_Pfad.'index.html');
$bSeite=true&&file_exists(FRA_Pfad.'fraSeite.htm');
$bFertg=true&&file_exists(FRA_Pfad.'fraFertig.inc.htm');
if($_SERVER['REQUEST_METHOD']=='POST'){
 $sFName='temp/test_'.sprintf('%02d',date('s')).'.zip';
 if($f=@fopen(FRA_Pfad.$sFName,'w')){
  fclose($f); unlink(FRA_Pfad.$sFName); $zip=new ZipArchive;
  if($zip->open(FRA_Pfad.$sFName,ZipArchive::CREATE)===true){
   $zip->addFromString('_TestfragenSicherung.txt','# Datensicherung zum Testfragen-Script vom '.date('d.m.Y, H:i').NL);
   if($bWerte=isset($_POST['werte'])&&$_POST['werte']) $zip->addFile(FRA_Pfad.'fraWerte'.(KONF>0?KONF:'').'.php','fraWerte'.(KONF>0?KONF:'').'.php');
   if($bVerso=isset($_POST['verso'])&&$_POST['verso']&&file_exists(FRA_Pfad.'fraVersion.php')) $zip->addFile(FRA_Pfad.'fraVersion.php','fraVersion.php');
   if($bStyle=isset($_POST['style'])&&$_POST['style']&&file_exists(FRA_Pfad.'fraStyle.css')) $zip->addFile(FRA_Pfad.'fraStyle.css','fraStyle.css');
   if($bIndex=isset($_POST['index'])&&$_POST['index']&&file_exists(FRA_Pfad.'index.html')) $zip->addFile(FRA_Pfad.'index.html','index.html');
   if($bSeite=isset($_POST['seite'])&&$_POST['seite']&&file_exists(FRA_Pfad.'fraSeite.htm')) $zip->addFile(FRA_Pfad.'fraSeite.htm','fraSeite.htm');
   if($bFertg=isset($_POST['fertg'])&&$_POST['fertg']&&file_exists(FRA_Pfad.'fraFertig.inc.htm')) $zip->addFile(FRA_Pfad.'fraFertig.inc.htm','fraFertig.inc.htm');
   if(!FRA_SQL){//Text
    if($bFragen=isset($_POST['fragen'])&&$_POST['fragen']&&file_exists(FRA_Pfad.FRA_Daten.FRA_Fragen)) $zip->addFile(FRA_Pfad.FRA_Daten.FRA_Fragen,FRA_Daten.FRA_Fragen);
    if($bFolgen=isset($_POST['folgen'])&&$_POST['folgen']&&file_exists(FRA_Pfad.FRA_Daten.FRA_Folgen)) $zip->addFile(FRA_Pfad.FRA_Daten.FRA_Folgen,FRA_Daten.FRA_Folgen);
   }elseif($DbO){//SQL
    if($bFragen=isset($_POST['fragen'])&&$_POST['fragen']){
     if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){//Fragen
      $s='Nummer;aktiv;versteckt;Kategorie;Frage;Loesung;Punkte;Bild;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Anmerkung;Anmerkung2';
      while($a=$rR->fetch_row()){$s.=NL.$a[0]; for($i=1;$i<19;$i++) $s.=';'.(isset($a[$i])?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i]))):'');}
      $rR->close(); $zip->addFromString('sql/'.FRA_SqlTabF.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
    }
    if($bFolgen=isset($_POST['folgen'])&&$_POST['folgen']){
     if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabT.' ORDER BY Folge')){//Testfolgen
      $s='Folge;Fragen;ProSeite'.NL;
      while($a=$rR->fetch_row()) if(!empty($a[1])) $s.=$a[0].';'.$a[1].';'.(isset($a[2])?$a[2]:'1').NL;
      $rR->close(); $zip->addFromString('sql/'.FRA_SqlTabT.'.txt',rtrim($s).NL);
     }else{$sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Folgentabelle <i>'.FRA_SqlTabT.'</i>!</p>';}
    }
   }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
   if($bBilder=isset($_POST['bilder'])&&$_POST['bilder']) if(is_array($aBld)) foreach($aBld as $s) $zip->addFile(FRA_Pfad.FRA_Bilder.$s,FRA_Bilder.$s);
   $zip->close();
   $sMeld='<p class="admErfo">Die Sicherungsdatei <a title="ZIP-Datei herunterladen" href="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.$sFName.'"><i>'.$sFName.'</i></a> kann heruntergeladen werden.</p>';
  }else $sMeld='<p class="admFehl">Fehler beim Anlegen der Sicherungsdatei <i>'.$sFName.'</i>.</p>';
 }else $sMeld='<p class="admFehl">Die Sicherungsdatei <i>'.$sFName.'</i> durfte nicht angelegt werden.</p>';
 echo $sMeld.NL;
}else{ //GET
 for($i=59;$i>=0;$i--) if(file_exists(FRA_Pfad.'temp/test_'.sprintf('%02d',$i).'.zip')) unlink(FRA_Pfad.'temp/test_'.sprintf('%02d',$i).'.zip');
 echo '<p class="admMeld">Stellen Sie die Daten für die Sicherung der '.(!KONF?'Grund-':'').'Konfiguration'.(KONF>0?'-'.KONF:'').' zusammen.</p>';
}
?>

<form name="fraExport" action="backup.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="fragen<?php if($bFragen) echo '" checked="checked'?>" value="1" /></td>
  <td>Fragen</td>
  <td><?php echo $nFragen?> Fragen in der Fragen-Tabelle <i><?php echo (!FRA_SQL?FRA_Fragen:FRA_SqlTabF)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="folgen<?php if($bFolgen) echo '" checked="checked'?>" value="1" /></td>
  <td>Testfolgen</td>
  <td><?php ?>Testfolgen in der Folgen-Tabelle <i><?php echo (!FRA_SQL?FRA_Folgen:FRA_SqlTabT)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="bilder<?php if($bBilder) echo '" checked="checked'?>" value="1" /></td>
  <td>Bilder</td>
  <td><?php echo (is_array($aBld)?count($aBld):'keine')?> Bilder im Ordner <i><?php echo substr(FRA_Bilder,0,-1)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="werte<?php if($bWerte) echo '" checked="checked'?>" value="1" /></td>
  <td>fraWerte<?php if(KONF>0)echo KONF?>.php</td>
  <td>zentrale Parameter- und Einstelldatei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="verso<?php if($bVerso) echo '" checked="checked'?>" value="1" /></td>
  <td>fraVersion.php</td>
  <td>Versions-Datei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="style<?php if($bStyle) echo '" checked="checked'?>" value="1" /></td>
  <td>fraStyle.css</td>
  <td>CSS-Styles-Formatierungsdatei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="index<?php if($bIndex) echo '" checked="checked'?>" value="1" /></td>
  <td>index.html</td>
  <td>umhüllendes Frameset</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="seite<?php if($bSeite) echo '" checked="checked'?>" value="1" /></td>
  <td>fraSeite.htm</td>
  <td>umhüllende HTML-Schablone</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="fertg<?php if($bFertg) echo '" checked="checked'?>" value="1" /></td>
  <td>fraFertig.inc.htm</td>
  <td>Vorlage für Fertig-Meldung</td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Sichern"></p>
</form>

<p><u>Hinweis</u>:</p>
<ul>
<li>Die Datensicherung der gewählten <?php if(!KONF) echo 'Grund-'?>Konfiguration<?php if(KONF>0) echo '-'.KONF?> erfolgt als ZIP-Datei.
Bei der Datensicherung werden keine Dateien und Einstellungen verändert.</li>
<li>Im ZIP-Archiv enthalten sind alle Daten,
die für die aktuelle <?php if(!KONF) echo 'Grund-'?>Konfiguration<?php if(KONF>0) echo '-'.KONF?> bedeutsam sind.
Eventuell sind auch einige Dateien dabei, die für andere Konfigurationen ebenfalls von Bedeutung sind.</li>
</ul>

<?php echo fSeitenFuss();?>