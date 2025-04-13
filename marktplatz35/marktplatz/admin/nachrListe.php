<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Benachrichtigungsliste','','NaL');

 if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['neuMl'])&&($sEmail=$_POST['neuMl'])){//neu eintragen
   $a=explode(NL,str_replace("\r",'',trim($sEmail))); $nZahl=count($a); $sEmail=''; $aNeuAdr=array();
   for($i=0;$i<$nZahl;$i++){$s=trim($a[$i]);
    if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-z���_-]+(\.[0-9a-z���_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aNeuAdr[]=$s;
   }
   if(count($aNeuAdr)){
    if(!MP_SQL){ //Textdaten
     $aD=@file(MP_Pfad.MP_Daten.MP_MailAdr);
     $s='#eMail'."\n"; foreach($aNeuAdr as $sEmail) $s.=fMpEnCode(strtolower($sEmail))."\n"; $aD[0]=$s;
     if($f=fopen(MP_Pfad.MP_Daten.MP_MailAdr,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Meld=count($aNeuAdr).' neue E-Mail-Adressen wurden eingetragen.'; $MTyp='Erfo';
     }else $Meld='In die Datei <i>'.MP_Daten.MP_MailAdr.'</i> durfte nicht geschrieben werden.';
    }elseif($DbO){ //SQL
     $k=0;
     foreach($aNeuAdr as $sEmail) if($DbO->query('INSERT IGNORE INTO '.MP_SqlTabM.' (email) VALUES ("'.strtolower($sEmail).'")')) $k++;
     $Meld=$k.' neue E-Mail-Adressen wurden eingetragen.'; $MTyp='Erfo';
    }
   }else $Meld='Die E-Mail-Adressen waren fehlerhaft!';
  }elseif(isset($_POST['email'])&&($aEmail=$_POST['email'])){//Mails l�schen
   $k=0;
   if(!MP_SQL){ //Textdaten
    $aD=@file(MP_Pfad.MP_Daten.MP_MailAdr);
    foreach($aEmail as $s){
     if(!$p=strpos($s,';')) $s=fMpEnCode($s); else $s=substr($s,0,$p).';'.fMpEnCode(substr($s,$p+1));
     if($p=array_search($s.NL,$aD)){$aD[$p]=''; $k++;}
    }
    if($f=fopen(MP_Pfad.MP_Daten.MP_MailAdr,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Meld=$k.' E-Mail-Adressen wurden gel�scht.'; $MTyp='Erfo';
    }else $Meld='In die Datei <i>'.MP_Daten.MP_MailAdr.'</i> durfte nicht geschrieben werden.';
   }elseif($DbO){ //SQL
    foreach($aEmail as $s) if($DbO->query('DELETE FROM '.MP_SqlTabM.' WHERE email="'.$s.'" LIMIT 1')) $k++;
    $Meld=$k.' E-Mail-Adressen wurden gel�scht.'; $MTyp='Erfo';
   }
  }elseif(isset($_POST['nachr'])&&($aNachr=$_POST['nachr'])){//Benachrichtigungen l�schen
   $k=0;
   if(!MP_SQL){ //Textdaten
    $aD=@file(MP_Pfad.MP_Daten.MP_Benachr);
    foreach($aNachr as $s){
     if($p=strpos($s,';')) $s=substr($s,0,$p).';'.fMpEnCode(substr($s,$p+1));
     if($p=array_search($s.NL,$aD)){$aD[$p]=''; $k++;}
    }
    if($f=fopen(MP_Pfad.MP_Daten.MP_Benachr,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Meld=$k.' Benachrichtigungen wurden gel�scht.'; $MTyp='Erfo';
    }else $Meld='In die Datei <i>'.MP_Daten.MP_MailAdr.'</i> durfte nicht geschrieben werden.';
   }elseif($DbO){ //SQL
    foreach($aNachr as $s){
     $a=explode(';',$s);
     if($DbO->query('DELETE FROM '.MP_SqlTabB.' WHERE inserat="'.$a[0].'" AND email="'.$a[1].'" LIMIT 1')) $k++;
    }
    $Meld=$k.' Benachrichtigungen wurden gel�scht.'; $MTyp='Erfo';
   }
  }
  if(!$Meld){$Meld='Die Daten bleiben unver�ndert.'; $MTyp='Meld';}
 }//POST

 //Daten bereitstellen
 $sEmail=''; $sNachr='';
 if(!MP_SQL){ //Textdaten
  $aD=@file(MP_Pfad.MP_Daten.MP_MailAdr);
  if(is_array($aD)){$nZhl=count($aD);
   for($i=1;$i<$nZhl;$i++){
    $s=rtrim($aD[$i]);
    if(!$p=strpos($s,';')) $s=fMpDeCode($s); else $s=substr($s,0,$p).';'.fMpDeCode(substr($s,$p+1));
    $sEmail.='<option value="'.$s.'">'.$s.'</option>';
  }}
  $aD=@file(MP_Pfad.MP_Daten.MP_Benachr);
  if(is_array($aD)){$nZhl=count($aD);
   for($i=1;$i<$nZhl;$i++){
    $s=rtrim($aD[$i]);
    if($p=strpos($s,';')) $s=substr($s,0,$p).';'.fMpDeCode(substr($s,$p+1));
    $sNachr.='<option value="'.$s.'">'.$s.'</option>';
 }}}elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,email FROM '.MP_SqlTabM.' ORDER BY nr DESC')){
   while($aR=$rR->fetch_row()) $sEmail.='<option value="'.$aR[1].'">'.$aR[1].'</option>'; $rR->close();
   if($rR=$DbO->query('SELECT nr,inserat,email FROM '.MP_SqlTabB.' ORDER BY nr DESC')){
    while($aR=$rR->fetch_row()) $sNachr.='<option value="'.$aR[1].';'.$aR[2].'">'.$aR[1].';'.$aR[2].'</option>'; $rR->close();
  }}else $Meld=MP_TxSqlFrage;
 }

 //Seitenausgabe
 if(empty($Meld)){$Meld='Kontrollieren oder �ndern Sie die Benachrichtigungsw�nsche!'; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<table border="0" align="center" cellpadding="0" cellspacing="0"><tr><td width="280" valign="top">
 <p>freigegebene Empf�ngeradressen</p>
 <form action="nachrListe.php" method="post">
 <table class="admTabl" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
  <td>
  <select name="email[]" size="32" style="width:99%" multiple="multiple"><?php echo $sEmail?></select>
  </td>
 </tr></table>
 <p class="admSubmit"><input class="admSubmit" type="submit" value="L�schen"></p>
 </form>
</td><td width="16" rowspan="2">&nbsp</td><td width="280" valign="top" rowspan="2">
 <p>�nderungsbenachrichtigungsw�nsche</p>
 <form action="nachrListe.php" method="post">
 <table class="admTabl" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
  <td>
  <select name="nachr[]" size="40" style="width:99%" multiple="multiple"><?php echo $sNachr?></select>
  </td>
 </tr></table>
 <p class="admSubmit"><input class="admSubmit" type="submit" value="L�schen"></p>
 </form>
</td></tr><tr><td width="280" valign="top">
 <form action="nachrListe.php" method="post">
 <table class="admTabl" width="100%" border="0" cellpadding="5" cellspacing="1"><tr class="admTabl">
  <td>
  <textarea name="neuMl" rows="3" style="width:99%;height:45px;"></textarea>
  </td>
 </tr></table>
 <p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
 </form>
</td></tr></table>

<div class="admBox"><p><span class="admFehl"><u>Erkl�rungen</u>:<ul>
<li>freigegebene Empf�ngeradresse umfassen alle die Besucher, die f�r das Anfordern von �nderungs-Benachrichtigungen freigeschaltet sind</li>
<li>steht bei den freigegebenen Empf�ngeradressen eine Zahl und ein Semikolon vor der E-Mail-Adresse so hat der Besucher eine Freischaltung beantragt, diese aber noch nicht selbst best�tigt.</li>
<li>registrierte Benutzer aus der Benutzerliste ben�tigen keinen Eintrag in der Liste der freigegebene Empf�ngeradresse. Sie k�nnen sofern sie eingelogt sind Benachrichtigungen anfordern.</li>
<li>bei den �nderungsbenachrichtigungsw�nschen bedeutet die Zahl vor der Adresse die Inseratenummer und das Marktsegment, zu denen eine �nderungsmeldung erw�nscht ist.</li>
</ul>
<p><u>Hinweis</u>: Mehrfachmarkierungen zum L�schen sind mit &lt;Strg&gt; m�glich.</p>
</div>

<?php echo fSeitenFuss();?>