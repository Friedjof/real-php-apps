<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Konfiguration sichern','','ZBu');

$bFragen=true; $bErgebnis=true; $bZuweisung=true; $bNutzer=true; $bTeilnahme=true; $bBilder=true; $bWerte=true; $aBld=array(); $nFragen='keine';
if(!UMF_SQL){//Text
 $a=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nFragen=(is_array($a)?max(count($a)-1,0):'keine');
 $a=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nErgebnis=(is_array($a)?max(count($a)-1,0):'keine');
 $a=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nZuweisung=(is_array($a)?max(count($a)-1,0):'keine');
 $a=@file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nNutzer=(is_array($a)?max(count($a)-1,0):'keine');
 $a=@file(UMF_Pfad.UMF_Daten.UMF_Teilnahme); $nTeilnahme=(is_array($a)?max(count($a)-1,0):'keine');
}elseif($DbO){//SQL
 if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabF)){if($a=$rR->fetch_row()) $nFragen=$a[0]; $rR->close();}
 else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabF.'</i></p>';
 if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabE)){if($a=$rR->fetch_row()) $nErgebnis=max($a[0]-1,0); $rR->close();}
 else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabE.'</i></p>';
 if($rR=$DbO->query('SELECT COUNT(Benutzer) FROM '.UMF_SqlTabZ)){if($a=$rR->fetch_row()) $nZuweisung=$a[0]; $rR->close();}
 else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabZ.'</i></p>';
 if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabN)){if($a=$rR->fetch_row()) $nNutzer=$a[0]; $rR->close();}
 else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabN.'</i></p>';
 if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabT)){if($a=$rR->fetch_row()) $nTeilnahme=$a[0]; $rR->close();}
 else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabT.'</i></p>';
}
if($H=@opendir(substr(UMF_Pfad.UMF_Bilder,0,-1))){//Bilder
 while($sF=readdir($H)) if($sF!='.'&&$sF!='..'&&$sF!='index.html'&&$sF!='.htaccess'){
  if(!is_dir(UMF_Pfad.UMF_Bilder.$sF)) $aBld[]=$sF;
  elseif($H2=@opendir(UMF_Pfad.UMF_Bilder.$sF)){
   while($sF2=readdir($H2)) if($sF2!='.'&&$sF2!='..'&&$sF2!='index.html'&&$sF2!='.htaccess'&&is_file(UMF_Pfad.UMF_Bilder.$sF.'/'.$sF2)) $aBld[]=$sF.'/'.$sF2;
   closedir($H2);
 }}
 closedir($H);
}
$bVerso=true&&file_exists(UMF_Pfad.'umfVersion.php');
$bStyle=true&&file_exists(UMF_Pfad.'umfStyle.css');
$bIndex=true&&file_exists(UMF_Pfad.'index.html');
$bSeite=true&&file_exists(UMF_Pfad.'umfSeite.htm');
$bFertg=true&&file_exists(UMF_Pfad.'umfFertig.inc.htm');
if($_SERVER['REQUEST_METHOD']=='POST'){
 $sFName='temp/umfrage_'.sprintf('%02d',date('s')).'.zip';
 if($f=@fopen(UMF_Pfad.$sFName,'w')){
  fclose($f); unlink(UMF_Pfad.$sFName); $zip=new ZipArchive;
  if($zip->open(UMF_Pfad.$sFName,ZipArchive::CREATE)===true){
   $zip->addFromString('_UmfrageSicherung.sav','# Datensicherung zum Umfrage-Script vom '.date('d.m.Y, H:i').NL);
   if($bWerte=isset($_POST['werte'])&&$_POST['werte']) $zip->addFile(UMF_Pfad.'umfWerte'.(KONF>0?KONF:'').'.php','umfWerte'.(KONF>0?KONF:'').'.php');
   if($bVerso=isset($_POST['verso'])&&$_POST['verso']&&file_exists(UMF_Pfad.'umfVersion.php')) $zip->addFile(UMF_Pfad.'umfVersion.php','umfVersion.php');
   if($bStyle=isset($_POST['style'])&&$_POST['style']&&file_exists(UMF_Pfad.'umfStyle.css')) $zip->addFile(UMF_Pfad.'umfStyle.css','umfStyle.css');
   if($bIndex=isset($_POST['index'])&&$_POST['index']&&file_exists(UMF_Pfad.'index.html')) $zip->addFile(UMF_Pfad.'index.html','index.html');
   if($bSeite=isset($_POST['seite'])&&$_POST['seite']&&file_exists(UMF_Pfad.'umfSeite.htm')) $zip->addFile(UMF_Pfad.'umfSeite.htm','umfSeite.htm');
   if($bFertg=isset($_POST['fertg'])&&$_POST['fertg']&&file_exists(UMF_Pfad.'umfFertig.inc.htm')) $zip->addFile(UMF_Pfad.'umfFertig.inc.htm','umfFertig.inc.htm');
   if(!UMF_SQL){//Text
    if($bFragen=isset($_POST['fragen'])&&$_POST['fragen']&&file_exists(UMF_Pfad.UMF_Daten.UMF_Fragen)) $zip->addFile(UMF_Pfad.UMF_Daten.UMF_Fragen,UMF_Daten.UMF_Fragen);
    if($bErgebnis=isset($_POST['ergebnis'])&&$_POST['ergebnis']&&file_exists(UMF_Pfad.UMF_Daten.UMF_Ergebnis)) $zip->addFile(UMF_Pfad.UMF_Daten.UMF_Ergebnis,UMF_Daten.UMF_Ergebnis);
    if($bZuweisung=isset($_POST['zuweisung'])&&$_POST['zuweisung']&&file_exists(UMF_Pfad.UMF_Daten.UMF_Zuweisung)) $zip->addFile(UMF_Pfad.UMF_Daten.UMF_Zuweisung,UMF_Daten.UMF_Zuweisung);
    if($bNutzer=isset($_POST['nutzer'])&&$_POST['nutzer']&&file_exists(UMF_Pfad.UMF_Daten.UMF_Nutzer)) $zip->addFile(UMF_Pfad.UMF_Daten.UMF_Nutzer,UMF_Daten.UMF_Nutzer);
    if($bTeilnahme=isset($_POST['teilnahme'])&&$_POST['teilnahme']&&file_exists(UMF_Pfad.UMF_Daten.UMF_Teilnahme)) $zip->addFile(UMF_Pfad.UMF_Daten.UMF_Teilnahme,UMF_Daten.UMF_Teilnahme);
   }elseif($DbO){//SQL
    if($bFragen=isset($_POST['fragen'])&&$_POST['fragen']){
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' ORDER BY Nummer')){//Fragen
      $s='Nummer;aktiv;versteckt;Kategorie;Frage;Loesung;Punkte;Bild;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Anmerkung1;Anmerkung2';
      while($a=$rR->fetch_row()){$s.=NL.$a[0]; for($i=1;$i<19;$i++) $s.=';'.(isset($a[$i])?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i]))):'');}
      $rR->close(); $zip->addFromString('sql/'.UMF_SqlTabF.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabF.'</i></p>';
    }
    if($bErgebnis=isset($_POST['ergebnis'])&&$_POST['ergebnis']){
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabE.' ORDER BY Nummer')){//Ergebnis
      $s='Nummer;Inhalt';
      while($a=$rR->fetch_row()) $s.=NL.$a[0].';'.str_replace('\"','"',$a[1]);
      $rR->close(); $zip->addFromString('sql/'.UMF_SqlTabE.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabE.'</i></p>';
    }
    if($bZuweisung=isset($_POST['zuweisung'])&&$_POST['zuweisung']){
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabZ.' ORDER BY Benutzer')){//Zuweisungen
      $s='Benutzer;Umfragen';
      while($a=$rR->fetch_row()) $s.=NL.$a[0].';'.str_replace('\"','"',$a[1]);
      $rR->close(); $zip->addFromString('sql/'.UMF_SqlTabZ.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabZ.'</i></p>';
    }
    if($bNutzer=isset($_POST['nutzer'])&&$_POST['nutzer']){
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' ORDER BY Nummer')){//Nutzer
      $s='Nummer;aktiv;Benutzer;Passwort;eMail'; $a=explode(';',UMF_NutzerFelder); $nNtzFld=count($a); for($i=5;$i<$nNtzFld;$i++) $s.=';dat_'.$i;
      while($a=$rR->fetch_row()){
       $s.=NL.$a[0].';'.$a[1].';'.$a[2].';'.$a[3].';'.$a[4]; for($i=5;$i<$nNtzFld;$i++) $s.=';'.(isset($a[$i])?str_replace(';','`,',str_replace('\"','"',$a[$i])):'');
      }$rR->close(); $zip->addFromString('sql/'.UMF_SqlTabN.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabN.'</i></p>';
    }
    if($bTeilnahme=isset($_POST['teilnahme'])&&$_POST['teilnahme']){
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabT.' ORDER BY Nummer')){//Teilnahme
      $s='Datum;Status;Art;Nutzer;Ergebnis';
      while($a=$rR->fetch_row()) $s.=NL.$a[1].';'.$a[2].';'.$a[3].';'.$a[4].';'.$a[5];
      $rR->close(); $zip->addFromString('sql/'.UMF_SqlTabT.'.txt',rtrim($s).NL);
     }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.' in Tabelle <i>'.UMF_SqlTabT.'</i></p>';
    }
   }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
   if($bBilder=isset($_POST['bilder'])&&$_POST['bilder']) if(is_array($aBld)&&count($aBld)) foreach($aBld as $s) $zip->addFile(UMF_Pfad.UMF_Bilder.$s,UMF_Bilder.$s);
   $zip->close();
   $sMeld='<p class="admErfo">Die Sicherungsdatei <a title="ZIP-Datei herunterladen" href="http://'.UMF_Www.$sFName.'"><i>'.$sFName.'</i></a> kann heruntergeladen werden.</p>';
  }else $sMeld='<p class="admFehl">Fehler beim Anlegen der Sicherungsdatei <i>'.$sFName.'</i>.</p>';
 }else $sMeld='<p class="admFehl">Die Sicherungsdatei <i>'.$sFName.'</i> durfte nicht angelegt werden.</p>';
 echo $sMeld.NL;
}else{ //GET
 for($i=59;$i>=0;$i--) if(file_exists(UMF_Pfad.'temp/umfrage_'.sprintf('%02d',$i).'.zip')) unlink(UMF_Pfad.'temp/umfrage_'.sprintf('%02d',$i).'.zip');
 echo '<p class="admMeld">Stellen Sie die Daten für die Sicherung der '.(!KONF?'Grund-':'').'Konfiguration'.(KONF>0?'-'.KONF:'').' zusammen.</p>';
}
?>

<form name="umfExport" action="zipBackup.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="fragen<?php if($bFragen) echo '" checked="checked'?>" value="1" /></td>
  <td>Fragen</td>
  <td><?php echo $nFragen?> Fragen in der Fragen-Tabelle <i><?php echo (!UMF_SQL?UMF_Fragen:UMF_SqlTabF)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="ergebnis<?php if($bErgebnis) echo '" checked="checked'?>" value="1" /></td>
  <td>Ergebnisse</td>
  <td><?php echo $nErgebnis?> Ergebnisse in der Ergebnis-Tabelle <i><?php echo (!UMF_SQL?UMF_Ergebnis:UMF_SqlTabE)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="zuweisung<?php if($bZuweisung) echo '" checked="checked'?>" value="1" /></td>
  <td>Zuweisungen</td>
  <td><?php echo $nZuweisung?> Zuweisungen in der Zuweisungs-Tabelle <i><?php echo (!UMF_SQL?UMF_Zuweisung:UMF_SqlTabZ)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="nutzer<?php if($bNutzer) echo '" checked="checked'?>" value="1" /></td>
  <td>Benutzer</td>
  <td><?php echo $nNutzer?> Benutzer in der Nutzer-Tabelle <i><?php echo (!UMF_SQL?UMF_Nutzer:UMF_SqlTabN)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="teilnahme<?php if($bTeilnahme) echo '" checked="checked'?>" value="1" /></td>
  <td>Teilnahme</td>
  <td><?php echo $nTeilnahme?> Teilnahmen in der Teilnahme-Tabelle <i><?php echo (!UMF_SQL?UMF_Teilnahme:UMF_SqlTabT)?></i></td>
 </tr><tr class="admTabl"><td class="admSpa1"></td><td></td><td></td></tr>
 <tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="bilder<?php if($bBilder) echo '" checked="checked'?>" value="1" /></td>
  <td>Bilder</td>
  <td><?php echo (is_array($aBld)&&count($aBld)?count($aBld):'keine')?> Bilder im Ordner <i><?php echo substr(UMF_Bilder,0,-1)?></i></td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="werte<?php if($bWerte) echo '" checked="checked'?>" value="1" /></td>
  <td>umfWerte<?php if(KONF>0)echo KONF?>.php</td>
  <td>zentrale Parameter- und Einstelldatei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="verso<?php if($bVerso) echo '" checked="checked'?>" value="1" /></td>
  <td>umfVersion.php</td>
  <td>Versions-Datei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="style<?php if($bStyle) echo '" checked="checked'?>" value="1" /></td>
  <td>umfStyle.css</td>
  <td>CSS-Styles-Formatierungsdatei</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="index<?php if($bIndex) echo '" checked="checked'?>" value="1" /></td>
  <td>index.html</td>
  <td>umhüllende Einstiegsseite</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="seite<?php if($bSeite) echo '" checked="checked'?>" value="1" /></td>
  <td>umfSeite.htm</td>
  <td>umhüllende HTML-Schablone</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admCheck" type="checkbox" name="fertg<?php if($bFertg) echo '" checked="checked'?>" value="1" /></td>
  <td>umfFertig.inc.htm</td>
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