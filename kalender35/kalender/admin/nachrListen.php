<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benachrichtigungslisten','','NaL');

if(file_exists(KALPFAD.'kalWerte.php')){
 if($_SERVER['REQUEST_METHOD']=='POST'){
  if($sIn=(isset($_POST['neuMl'])?$_POST['neuMl']:'')){//neu Eintragen
   $a=explode("\n",str_replace("\r",'',trim($sIn))); $nZahl=count($a); $sIn='';
   for($i=0;$i<$nZahl;$i++){
    $s=trim($a[$i]);
    if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sIn.=$s."\n";
   }
   if(!empty($sIn)){
    if(!KAL_SQL){ //Textdaten
     $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $aD[0]='#eMail'."\n".$sIn;
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_MailAdr,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Msg='<p class="admErfo">'.substr_count($sIn,NL).' neue E-Mail-Adressen wurden eingetragen.</p>';
     }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_MailAdr.'</i> konnte nicht geschrieben werden.</p>';
    }elseif($DbO){ //SQL
     $a=explode(NL,rtrim($sIn)); $nZahl=count($a); $k=0;
     for($i=0;$i<$nZahl;$i++) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabM.' (email) VALUES ("'.rtrim($a[$i]).'")')) $k++;
     $Msg='<p class="admErfo">'.$k.' neue E-Mail-Adressen wurden eingetragen.</p>';
    }
   }else $Msg='<p class="admFehl">die E-Mail-Adressen waren fehlerhaft!</p>';
  }elseif($sIn=(isset($_POST['neuEr'])?$_POST['neuEr']:'')){//neue Erinnerung
   $a=explode("\n",str_replace("\r",'',trim($sIn))); $nZahl=count($a); $aN=array(); $k=0;
   for($i=0;$i<$nZahl;$i++){
    $s=str_replace(',',';',trim($a[$i])); $aD=explode(';',$s);
    if(isset($aD[2])&&preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($aD[2])))
    if(strlen($aD[0])==10&&substr($aD[0],4,1)=='-'&&substr($aD[0],7,1)=='-') $aN[]=$s."\n";
   }
   if($nZahl=count($aN)){
    if(!KAL_SQL){ //Textdaten
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $aD[0]='#Datum;Termin;eMail'."\n"; $i=count($aD)-1; $aD[$i]=rtrim($aD[$i])."\n";
     $aD=array_merge($aD,$aN); sort($aD);
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Msg='<p class="admErfo">'.$nZahl.' neue Erinnerungen wurden eingetragen.</p>';
     }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Erinner.'</i> konnte nicht geschrieben werden.</p>';
    }elseif($DbO){ //SQL
     for($i=0;$i<$nZahl;$i++){
      $a=explode(';',rtrim($aN[$i]));
      if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabE.' (datum,termin,email) VALUES("'.$a[0].'","'.$a[1].'","'.$a[2].'")')) $k++;
      $Msg='<p class="admErfo">'.$k.' neue Erinnerungen wurden eingetragen.</p>';
     }
    }
   }else $Msg='<p class="admFehl">die Terminerinnerungen waren fehlerhaft!</p>';
  }elseif($sIn=(isset($_POST['neuBn'])?$_POST['neuBn']:'')){//neue Benachrichtigung
   $a=explode("\n",str_replace("\r",'',trim($sIn))); $nZahl=count($a); $aN=array(); $k=0;
   for($i=0;$i<$nZahl;$i++){
    $s=str_replace(',',';',trim($a[$i])); $aD=explode(';',$s);
    if(isset($aD[1])&&preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($aD[1])))
    $aN[]=$s."\n";
   }
   if($nZahl=count($aN)){
    if(!KAL_SQL){ //Textdaten
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $aD[0]='#Termin;eMail'."\n"; $i=count($aD)-1; $aD[$i]=rtrim($aD[$i])."\n";
     $aD=array_merge($aD,$aN); sort($aD);
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Msg='<p class="admErfo">'.$nZahl.' neue Benachrichtigungen wurden eingetragen.</p>';
     }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Benachr.'</i> konnte nicht geschrieben werden.</p>';
    }elseif($DbO){ //SQL
     for($i=0;$i<$nZahl;$i++){
      $a=explode(';',rtrim($aN[$i]));
      if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabB.' (termin,email) VALUES("'.$a[0].'","'.$a[1].'")')) $k++;
      $Msg='<p class="admErfo">'.$k.' neue Benachrichtigungen wurden eingetragen.</p>';
     }
    }
   }else $Msg='<p class="admFehl">die Benachrichtigungen waren fehlerhaft!</p>';
  }elseif($aEmail=(isset($_POST['email'])?$_POST['email']:'')){//Mails loeschen
   $k=0;
   if(!KAL_SQL){ //Textdaten
    $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr);
    foreach($aEmail as $s) if($p=array_search($s.NL,$aD)){$aD[$p]=''; $k++;}
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_MailAdr,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Msg='<p class="admErfo">'.$k.' E-Mail-Adressen wurden gelöscht.</p>';
    }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_MailAdr.'</i> durfte nicht geschrieben werden.</p>';
   }elseif($DbO){ //SQL
    foreach($aEmail as $s) if($DbO->query('DELETE FROM '.KAL_SqlTabM.' WHERE email="'.$s.'" LIMIT 1')) $k++;
    $Msg='<p class="admErfo">'.$k.' E-Mail-Adressen wurden gelöscht.</p>';
   }
  }elseif($aErinn=(isset($_POST['erinn'])?$_POST['erinn']:'')){//Erinnerungen löschen
   $k=0;
   if(!KAL_SQL){ //Textdaten
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner);
    foreach($aErinn as $s) if($p=array_search($s.NL,$aD)){$aD[$p]=''; $k++;}
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Msg='<p class="admErfo">'.$k.' Erinnerungen wurden gelöscht.</p>';
    }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Benachr.'</i> durfte nicht geschrieben werden.</p>';
   }elseif($DbO){ //SQL
    foreach($aErinn as $s){
     $a=explode(';',$s);
     if($DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE datum="'.$a[0].'" AND termin="'.$a[1].'" AND email="'.$a[2].'" LIMIT 1')) $k++;
    }
    $Msg='<p class="admErfo">'.$k.' Erinnerungen wurden gelöscht.</p>';
   }
  }elseif($aNachr=(isset($_POST['nachr'])?$_POST['nachr']:'')){//Benachrichtigungen löschen
   $k=0;
   if(!KAL_SQL){ //Textdaten
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr);
    foreach($aNachr as $s) if($p=array_search($s.NL,$aD)){$aD[$p]=''; $k++;}
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Msg='<p class="admErfo">'.$k.' Benachrichtigungen wurden gelöscht.</p>';
    }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Benachr.'</i> durfte nicht geschrieben werden.</p>';
   }elseif($DbO){ //SQL
    foreach($aNachr as $s){
     $a=explode(';',$s);
     if($DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE termin="'.$a[0].'" AND email="'.$a[1].'" LIMIT 1')) $k++;
    }
    $Msg='<p class="admErfo">'.$k.' Benachrichtigungen wurden gelöscht.</p>';
   }
  }
  if(!$Msg) $Msg='<p class="admMeld">Die Daten bleiben unverändert.</p>';
 }else{
  $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Nachrichtenversandwünsche. <a href="'.ADM_Hilfe.'LiesMich.htm#2.13" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
 }

 //Daten bereitstellen
 $sEmail=''; $sErinn=''; $sNachr='';
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $nZhl=count($aD); for($i=1;$i<$nZhl;$i++) $sEmail.='<option value="'.rtrim($aD[$i]).'">'.rtrim($aD[$i]).'</option>';
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nZhl=count($aD); for($i=1;$i<$nZhl;$i++) $sNachr.='<option value="'.rtrim($aD[$i]).'">'.rtrim($aD[$i]).'</option>';
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nZhl=count($aD); for($i=1;$i<$nZhl;$i++) $sErinn.='<option value="'.rtrim($aD[$i]).'">'.rtrim($aD[$i]).'</option>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT id,email FROM '.KAL_SqlTabM)){
   while($aR=$rR->fetch_row()) $sEmail.='<option value="'.$aR[1].'">'.$aR[1].'</option>'; $rR->close();
   if($rR=$DbO->query('SELECT id,termin,email FROM '.KAL_SqlTabB)){
    while($aR=$rR->fetch_row()) $sNachr.='<option value="'.$aR[1].';'.$aR[2].'">'.$aR[1].';'.$aR[2].'</option>'; $rR->close();
   }
   if($rR=$DbO->query('SELECT id,datum,termin,email FROM '.KAL_SqlTabE)){
    while($aR=$rR->fetch_row()) $sErinn.='<option value="'.$aR[1].';'.$aR[2].';'.$aR[3].'">'.$aR[1].';'.$aR[2].';'.$aR[3].'</option>'; $rR->close();
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
}else $Msg='<p class="admFehl">Setup-Fehler: Die Datei <i>kalWerte.php</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Scriptausgabe
echo $Msg.NL;
?>

<table width="100%" border="0" cellpadding="3" cellspacing="0"><tr>
<td width="30%" valign="top">freigegebene Empfänger</td>
<td width="35%" valign="top">Terminerinnerungen</td>
<td width="35%" valign="top">Änderungsbenachrichtigungen</td>
</tr><tr>
<td width="30%" valign="top">
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <select name="email[]" size="25" style="width:100%;" multiple="multiple"><?php echo $sEmail?>
 </select>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Löschen"></p>
</form>
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <textarea name="neuMl" cols="80" rows="3" style="heigth:3em"></textarea>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
</td>
<td width="35%" valign="top">
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <select name="erinn[]" size="25" style="width:100%;" multiple="multiple"><?php echo $sErinn?>
 </select>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Löschen"></p>
</form>
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <textarea name="neuEr" cols="50" rows="3" style="heigth:3em"></textarea>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
</td>
<td width="35%" valign="top">
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <select name="nachr[]" size="25" style="width:100%;" multiple="multiple"><?php echo $sNachr?>
 </select>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Löschen"></p>
</form>
<form action="nachrListen.php" method="post">
<table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
 <td>
 <textarea name="neuBn" cols="50" rows="3" style="heigth:3em"></textarea>
 </td>
</tr></table>
<p class="admSubmit" style="margin:8px;"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
</td>
</tr></table>
<div class="admBox"><u>Hinweis</u>: Mehrfachmarkierungen beim Löschen sind mit &lt;Strg&gt; möglich.</div>

<?php
echo fSeitenFuss();

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>