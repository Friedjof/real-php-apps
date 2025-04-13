<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Testzuweisung Benutzer','','NZw');

$sMeld=''; $sLschOK='';

$aT=array(FRA_TxStandardTest,FRA_TxSpontanFolge); //Testnamen holen
if(!FRA_SQL){ //Textdaten
 $aD=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){$s=$aD[$i]; $aT[]=substr($s,0,strpos($s,';'));}
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT Folge FROM '.FRA_SqlTabT)){ //Testnamen holen
  while($a=$rR->fetch_row()) $aT[]=$a[0]; $rR->close();
 }
}else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';

if($nId=(isset($_GET['nnr'])?$_GET['nnr']:'').(isset($_POST['nnr'])?$_POST['nnr']:'')){
 if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  $sQ=(isset($_POST['qs'])?$_POST['qs']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0))); $sZ='';
  if(!FRA_SQL){ //Zuweisungen holen
   $aZ=@file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nCnt=count($aZ); $t=$nId.';'; $l=strlen($t);
   for($i=1;$i<$nCnt;$i++) if(substr($aZ[$i],0,$l)==$t){$nZ=$i; $sZ=rtrim($aZ[$i]).';'; break;}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT Nummer,Tests FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$nId.'"')){ //Zuweisungen holen
    if($a=$rR->fetch_row()){$nZ=$a[0]; $sZ='#;'.$a[1].';';} $rR->close();
   }
  }
  if(isset($_POST['Eintragen'])&&$_POST['Eintragen']=='Eintragen'||(isset($_POST['lsch_x'])&&$_POST['lsch_x']==0&&isset($_POST['lsch_y'])&&$_POST['lsch_y']==0)){ // eintragen
   $nCnt=count($aT); $bCh=false; if(!FRA_SQL) $sZn=$nId.';'; else $sZn='#;';
   for($i=0;$i<$nCnt;$i++){
    if($p=strpos($sZ,';'.$aT[$i].'=')){ //Test gefunden
     $bT=true; $p=strpos($sZ,'=',$p); $q=strpos($sZ,';',$p++); $sTB=substr($sZ,$p,$q-$p);
    }else{$bT=false; $sTB='';} //Test nicht aktiv
    if(isset($_POST['ta'.$i])&&$_POST['ta'.$i]==1){ //aktiv
     if($bT){if($t=$_POST['tb'.$i]) $t=fFraHoleBedingung($t); if($sTB!=$t) $bCh=true;} //schon aktiv
     else{$t=fFraHoleBedingung($_POST['tb'.$i]); $bCh=true;} //neu
     $sZn.=str_replace(';',',',$aT[$i]).'='.$t.';';
    }else{if($bT) $bCh=true;} //passiv
   }
   if($bCh){
    if(substr_count($sZn,';')>1) $sZn=substr($sZn,0,-1);
    if(!FRA_SQL){
     if(isset($nZ)) $aZ[$nZ]=$sZn.NL; else $aZ[]=$sZn.NL;
     if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Zuweisung,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ))).NL); fclose($f);
      $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
     }else $sMeld='<p class="admFehl">Die Zuweisungen durften nicht in <i>'.FRA_Zuweisung.'</i> gespeichert werden.</p>';
    }elseif($DbO){ //SQL
     if(isset($nZ)) $bCh=$DbO->query('UPDATE IGNORE '.FRA_SqlTabZ.' SET Tests="'.substr($sZn,2).'" WHERE Nummer="'.$nId.'"');
     else $bCh=$DbO->query('INSERT IGNORE INTO '.FRA_SqlTabZ.' VALUES("'.$nId.'","'.substr($sZn,2).'")');
     if($bCh) $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
     else $sMeld='<p class="admFehl">Die Zuweisungen konnten nicht in <i>'.FRA_SqlTabZ.'</i> gespeichert werden.</p>';
    }
   }else $sMeld='<p class="admMeld">Die Zuweisungen bleiben unverändert.</p>';
  }elseif(isset($_POST['lsch_x'])||isset($_POST['lsch_y'])){ //loeschen
   if(isset($nZ)){
    if(isset($_POST['LschOK'])&&$_POST['LschOK']=='1'){
     if(!FRA_SQL){$aZ[$nZ]='';
      if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Zuweisung,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ))).NL); fclose($f);
       $sMeld='<p class="admErfo">Die Zuweisungen wurden gelöscht.</p>';
      }else $sMeld='<p class="admFehl">Die Zuweisungen durften nicht aus <i>'.FRA_Zuweisung.'</i> gelöscht werden.</p>';
     }elseif($DbO){ //SQL
      if($DbO->query('DELETE FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$nId.'" LIMIT 1'))
       $sMeld='<p class="admErfo">Die Zuweisungen wurden gelöscht.</p>';
      else $sMeld='<p class="admFehl">Die Zuweisungen konnten nicht aus <i>'.FRA_SqlTabZ.'</i> gelöscht werden.</p>';
     }
    }else{$sMeld='<p class="admFehl">Alle Zuweisungen des Benutzers wirklich löschen?</p>'; $sLschOK='1';}
   }else $sMeld='<p class="admMeld">Keine Zuweisungen zum Löschen!</p>';
  }
 }else{ //GET
  $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 }

 $aD=array(); $aN=array(); $aA=array(); $aB=array(); //Anzeigedaten holen
 if(!FRA_SQL){ //Textdaten
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); //Nutzer holen
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aN=explode(';',$aD[$i]); break;}
  if(count($aN)>3){$aN[2]=fFraDeCode($aN[2]); $aN[3]=fFraDeCode($aN[3]); $aN[4]=fFraDeCode($aN[4]);}
  else $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  $aD=@file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nCnt=count($aD); $t=$nId.';'; $l=strlen($t); //Zuweisungen (neu) holen
  for($i=1;$i<$nCnt;$i++) if(substr($aD[$i],0,$l)==$t){
   $t=rtrim($aD[$i]).';'; $nCt=count($aT); $bNn=false;
   for($j=0;$j<$nCt;$j++) if($p=strpos($t,';'.$aT[$j].'=')){
    $aA[$j]=true; $p=strpos($t,'=',$p); $q=strpos($t,';',$p++); $aB[$j]=substr($t,$p,$q-$p); $bNn=true;
   }break;
  }
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$nId.'"')){
   $aN=$rR->fetch_row(); $rR->close();
   if(count($aN)<3) $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
  if($rR=$DbO->query('SELECT Nummer,Tests FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$nId.'"')){
   if($rR->num_rows){
    $nCt=count($aT); $a=$rR->fetch_row(); $t='#;'.$a[1].';'; $bNn=false;
    for($j=0;$j<$nCt;$j++) if($p=strpos($t,';'.$aT[$j].'=')){
     $aA[$j]=true; $p=strpos($t,'=',$p); $q=strpos($t,';',$p++); $aB[$j]=substr($t,$p,$q-$p); $bNn=true;
   }}$rR->close();
  }
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
}else $sMeld='<p class="admFehl">Ungültiger Seitenaufruf ohne Benutzernummer!</p>';

//Scriptausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Testzuordnungen des Benutzers <i>'.$aN[2].'</i>.</p>';
echo $sMeld.NL;
?>

<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 $aFelder=explode(';',FRA_NutzerFelder); $nFelder=min(8,count($aFelder)); for($i=2;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
 echo '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td align="center"><b>Nr</b>.</td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td><b>'.$aFelder[2].'</b></td>';
 for($j=4;$j<$nFelder;$j++){if(!$s=$aFelder[$j]) $s='&nbsp;'; echo NL.' <td><b>'.($s!='GUELTIG_BIS'?$s:(FRA_TxNutzerFrist>''?FRA_TxNutzerFrist:$s)).'</b></td>';}
 echo NL.'</tr>';
 echo NL.'<tr class="admTabl">';
 echo NL.' <td>'.sprintf('%05d',$nId).'</td>';
 if(isset($bNn)){
  if($bNn) $sSta='<img src="punktGrn.gif" width="12" height="12" border="0" title="Testzuweisungen sind vorhanden">';
  else $sSta='<img src="punktRtGn.gif" width="12" height="12" border="0" title="keine Testzuweisungen eingetragen">';
 }else $sSta='<img src="punktRot.gif" width="12" height="12" border="0" title="Nutzer ohne Testzuweisungen">';
 echo NL.' <td align="center">'.$sSta.'</td>';
 if(!$s=$aN[2]) $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
 if(!$s=$aN[4]) $s='&nbsp;';
 echo NL.' <td>'.$s.'</td>';
 for($j=5;$j<$nFelder;$j++){if(!$s=$aN[$j]) $s='&nbsp;'; echo NL.' <td>'.(FRA_SQL?$s:str_replace('`,',';',$s)).'</td>';}
 echo NL.'</tr>';
?>
</table><br />

<form name="NutzerListe" action="nutzerTests.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="nnr" value="<?php echo $nId?>" />
<input type="hidden" name="qs" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td style="width:25%"><b>Testname</b></td><td style="width:4%"><b>aktiv</b></td><td><b>Eigenschaft</b></td></tr>
<?php
 $nCnt=count($aT);
 for($i=0;$i<$nCnt;$i++){
  echo ' <tr class="admTabl">'.NL;
  echo '  <td>'.$aT[$i].'</td>'.NL;
  echo '  <td align="center"><input class="admCheck" type="checkbox" name="ta'.$i.'" value="1'.(isset($aA[$i])?'" checked="checked':'').'" /></td>'.NL;
  echo '  <td><input type="text" name="tb'.$i.'" value="'.(isset($aB[$i])?fFraZeigeBedingung($aB[$i]):'').'" style="width:160px;" /></td>'.NL;
  echo ' </tr>'.NL;
 }
?>
<tr class="admTabl">
 <td>alle Testzuordnungen löschen<input type="hidden" name="LschOK" value="<?php echo $sLschOK;?>" /></td>
 <td align="center"><input type="image" src="iconLoeschen.gif" name="lsch" width="12" height="13" align="top" border="0" title="Benutzer-Tests löschen" tabindex="2" /></td>
 <td>(Benutzer <i><?php echo $aN[2]?> ist dann nicht mehr in der Liste der Testzuweisungen)</i></td>
</tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" name="Eintragen" value="Eintragen" tabindex="1" /></p>
</div>
</form>

<p style="text-align:center"><?php
echo '[ <a href="nutzerListe.php?'.$sQo.'">Benutzerliste</a> ] [ <a href="nutzerZuweisung.php?'.$sQo.'">Benutzer und Tests</a> ] [ <a href="nutzerAendern.php?'.$sQ.'">Benutzerdaten</a> ] ';
?></p>

<p><u>Hinweise</u>:</p>
<p>Die einzelnen Testfolgen können für den Benutzer erlaubt oder deaktiviert werden. Zusätzlich ist es bei erlaubter Testfolge möglich eventuell mit speziellen Begrenzungen zu arbeiten:</p>
<ul>
<li>Testdurchführung mit begrenzter Anzahl: Der Test kann vom Benutzer insgesamt höchstens so oft wie angegeben durchlaufen werden. Danach wird er im Benutzermenü nicht mehr angeboten.<br>Muster: <i>5x</i></li>
<li>Testdurchführung am Stichtag: Der Test wird nur am eingetragenen Tag im Benutzerzentrum angeboten.<br>Muster: <i>am 30.12.2015</i></li>
<li>Testdurchführung ab Stichtag: Der Test wird erst ab dem angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>ab 30.12.2015</i></li>
<li>Testdurchführung bis Stichtag: Der Test wird nur bis zum angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>bis 30.12.2015</i></li>
<li>Die Kriterien <i>begrenzte Anzahl</i> und <i>Stichtag</i> können auch kombiniert werden.</li>
</ul>

<p>Das Statussymbol des Benutzers bedeutet:</p>
<table border="0" cellpadding="2" cellspacing="0">
<tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktRot.gif" width="12" height="12" border="0" title="Benutzer ohne Testzuweisungen"></td>
<td>Benutzer ist nicht in der Liste der individuellen Testzuweisungen enthalten. Er bekommt im Benutzerzentrum die im Menüpunkt <i>Benutzerfunktionen</i> für alle Benutzer eingestellten Testfolgen zu sehen.</td>
</tr><tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktRtGn.gif" width="12" height="12" border="0" title="keine Testzuweisungen eingetragen"></td>
<td>Benutzer ist in der Liste der Testzuweisungen enthalten, hat aber momentan keine Testzuweisungen. Er bekommt damit im Benutzerzentrum aktuell <i>keine</i> Tests angeboten.</td>
</tr><tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktGrn.gif" width="12" height="12" border="0" title="Testzuweisungen sind vorhanden"></td>
<td>Benutzer hat individuelle Testzuweisungen. Er bekommt im Benutzerzentrum genau diese Tests angeboten.</td>
</tr>
</table>

<?php
echo fSeitenFuss();

function fFraZeigeBedingung($s){
 if($p=strpos($s,'x')) $t=trim(substr($s,max(0,(int)strrpos($s,' ')))).' '; else $t='';
 if($p=strpos($s,'-')){
  $p=max($p-4,0); $a=explode('-',substr($s,$p,10)); $s=trim(substr($s,0,$p)).sprintf(' %02d.%02d.%04d',$a[2],$a[1],$a[0]);
 }else $s='';
 return trim($t.$s);
}
function fFraHoleBedingung($s){
 $s=str_replace(' x','x',str_replace('  ',' ',str_replace('  ',' ',str_replace('   ',' ','# '.$s)))); $t='';
 if($p=strpos($s,'x')){
  $t=sprintf(' %0d',substr($s,(int)strrpos(substr($s,0,$p),' '),$p)).'x';
 }
 if(strpos($s,'.')){
  if($p=strpos($s,'bis')){$w='bis'; $s=trim(substr($s,$p+3));}
  elseif($p=strpos($s,'ab')){$w='ab'; $s=trim(substr($s,$p+2));}
  elseif($p=strpos($s,'am')){$w='am'; $s=trim(substr($s,$p+2));}
  else{$w='am'; $s=trim(substr($s,max(strpos($s,'.')-2,0)));}
  $a=explode('.',$s); if(!isset($a[2])) $a[2]=date('Y'); elseif((int)$a[2]<100) $a[2]=(int)$a[2]+2000;
  if(isset($a[1])) $s=$w.sprintf('%04d-%02d-%02d',$a[2],min(max($a[1],1),12),min(max($a[0],1),31)); else $s='';
 }else $s='';
 return trim($s.$t);
}
?>