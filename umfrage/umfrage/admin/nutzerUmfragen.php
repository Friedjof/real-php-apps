<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Umfragenzuweisung Benutzer','','NZw');

$sMeld=''; $sLschOK='';

$aU=array(UMF_TxStandardUmf); //Umfragenamen holen
for($i=1;$i<=26;$i++){
 $k=chr(64+$i); $s=constant('UMF_Umfr'.$k);
 if(substr($s,0,1)!=';'){$a=explode(';',$s); if($s=$a[3]) $aU[$k]=$s.' ('.$k.')';}
}

if($nId=(isset($_GET['nnr'])?$_GET['nnr']:'').(isset($_POST['nnr'])?$_POST['nnr']:'')){
 if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  $sQ=(isset($_POST['qs'])?$_POST['qs']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0))); $sZ='';
  if(!UMF_SQL){ //Zuweisungen holen
   $aZ=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nCnt=count($aZ); $t=$nId.';'; $l=strlen($t);
   for($i=1;$i<$nCnt;$i++) if(substr($aZ[$i],0,$l)==$t){$nZ=$i; $sZ=rtrim($aZ[$i]).';'; break;}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT Benutzer,Umfragen FROM '.UMF_SqlTabZ.' WHERE Benutzer="'.$nId.'"')){ //Zuweisungen holen
    if($a=$rR->fetch_row()){$nZ=$a[0]; $sZ='#;'.$a[1].';';} $rR->close();
   }
  }
  if(isset($_POST['Eintragen'])&&$_POST['Eintragen']=='Eintragen'||(isset($_POST['lsch_x'])&&$_POST['lsch_x']==0&&isset($_POST['lsch_y'])&&$_POST['lsch_y']==0)){ // eintragen
   reset($aU); $bCh=false; if(!UMF_SQL) $sZn=$nId.';'; else $sZn='#;';
   foreach($aU as $k=>$v){
    if($p=strpos($sZ,';'.$k.':')){ //Umfrage gefunden
     $bU=true; $p=strpos($sZ,':',$p); $q=strpos($sZ,';',$p++); $sUB=substr($sZ,$p,$q-$p);
    }else{$bU=false; $sUB='';} //Umfrage nicht aktiv
    if(isset($_POST['ta'.$k])&&$_POST['ta'.$k]==1){ //aktiv
     if($bU){if($t=$_POST['tb'.$k]) $t=fUmfHoleBedingung($t); if($sUB!=$t) $bCh=true;} //schon aktiv
     else{$t=fUmfHoleBedingung($_POST['tb'.$k]); $bCh=true;} //neu
     $sZn.=str_replace(';',',',$k).':'.$t.';';
    }else{if($bU) $bCh=true;} //passiv
   }
   if($bCh){
    if(substr_count($sZn,';')>1) $sZn=substr($sZn,0,-1);
    if(!UMF_SQL){
     if(isset($nZ)) $aZ[$nZ]=$sZn.NL; else $aZ[]=$sZn.NL;
     if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Zuweisung,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ))).NL); fclose($f);
      $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
     }else $sMeld='<p class="admFehl">Die Zuweisungen durften nicht in <i>'.UMF_Zuweisung.'</i> gespeichert werden.</p>';
    }elseif($DbO){ //SQL
     if(isset($nZ)) $bCh=$DbO->query('UPDATE IGNORE '.UMF_SqlTabZ.' SET Umfragen="'.substr($sZn,2).'" WHERE Benutzer="'.$nId.'"');
     else $bCh=$DbO->query('INSERT IGNORE INTO '.UMF_SqlTabZ.' VALUES("'.$nId.'","'.substr($sZn,2).'")');
     if($bCh) $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
     else $sMeld='<p class="admFehl">Die Zuweisungen konnten nicht in <i>'.UMF_SqlTabZ.'</i> gespeichert werden.</p>';
    }
   }else $sMeld='<p class="admMeld">Die Zuweisungen bleiben unverändert.</p>';
  }elseif(isset($_POST['lsch_x'])||isset($_POST['lsch_y'])){ //loeschen
   if(isset($nZ)){
    if(isset($_POST['LschOK'])&&$_POST['LschOK']=='1'){
     if(!UMF_SQL){$aZ[$nZ]='';
      if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Zuweisung,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ))).NL); fclose($f);
       $sMeld='<p class="admErfo">Die Zuweisungen wurden gelöscht.</p>';
      }else $sMeld='<p class="admFehl">Die Zuweisungen durften nicht aus <i>'.UMF_Zuweisung.'</i> gelöscht werden.</p>';
     }elseif($DbO){ //SQL
      if($DbO->query('DELETE FROM '.UMF_SqlTabZ.' WHERE Benutzer="'.$nId.'" LIMIT 1'))
       $sMeld='<p class="admErfo">Die Zuweisungen wurden gelöscht.</p>';
      else $sMeld='<p class="admFehl">Die Zuweisungen konnten nicht aus <i>'.UMF_SqlTabZ.'</i> gelöscht werden.</p>';
     }
    }else{$sMeld='<p class="admFehl">Alle Zuweisungen des Benutzers wirklich löschen?</p>'; $sLschOK='1';}
   }else $sMeld='<p class="admMeld">Keine Zuweisungen zum Löschen!</p>';
  }
 }else{ //GET
  $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 }

 $aD=array(); $aN=array(); $aA=array(); $aB=array(); //Anzeigedaten holen
 if(!UMF_SQL){ //Textdaten
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); //Nutzer holen
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aN=explode(';',$aD[$i]); break;}
  if(count($aN)>3){$aN[2]=fUmfDeCode($aN[2]); $aN[3]=fUmfDeCode($aN[3]); $aN[4]=fUmfDeCode($aN[4]);}
  else $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  $aD=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nCnt=count($aD); $t=$nId.';'; $l=strlen($t); //Zuweisungen (neu) holen
  for($i=1;$i<$nCnt;$i++) if(substr($aD[$i],0,$l)==$t){ //Nutzer gefunden
   $t=rtrim($aD[$i]).';'; $bNn=false; reset($aU);
   foreach($aU as $k=>$v) if($p=strpos($t,';'.$k.':')){
    $aA[$k]=true; $p=strpos($t,':',$p); $q=strpos($t,';',$p++); $aB[$k]=substr($t,$p,$q-$p); $bNn=true;
   }break;
  }
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.$nId.'"')){
   $aN=$rR->fetch_row(); $rR->close();
   if(count($aN)<3) $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
  if($rR=$DbO->query('SELECT Benutzer,Umfragen FROM '.UMF_SqlTabZ.' WHERE Benutzer="'.$nId.'"')){
   if($rR->num_rows){
    reset($aU); $a=$rR->fetch_row(); $t='#;'.$a[1].';'; $bNn=false;
    foreach($aU as $k=>$v) if($p=strpos($t,';'.$k.':')){
     $aA[$k]=true; $p=strpos($t,':',$p); $q=strpos($t,';',$p++); $aB[$k]=substr($t,$p,$q-$p); $bNn=true;
   }}$rR->close();
  }
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
}else $sMeld='<p class="admFehl">Ungültiger Seitenaufruf ohne Benutzernummer!</p>';

//Scriptausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Umfragezuordnungen des Benutzers <i>'.$aN[2].'</i>.</p>';
echo $sMeld.NL;
?>

<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 $aFelder=explode(';',UMF_NutzerFelder); $nFelder=min(8,count($aFelder)); for($i=2;$i<$nFelder;$i++) $aFelder[$i]=str_replace('´,',';',$aFelder[$i]);
 echo '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td align="center"><b>Nr</b>.</td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td><b>'.$aFelder[2].'</b></td>';
 for($j=4;$j<$nFelder;$j++){if(!$s=$aFelder[$j]) $s='&nbsp;'; echo NL.' <td><b>'.($s!='GUELTIG_BIS'?$s:(UMF_TxNutzerFrist>''?UMF_TxNutzerFrist:$s)).'</b></td>';}
 echo NL.'</tr>';
 echo NL.'<tr class="admTabl">';
 echo NL.' <td>'.sprintf('%05d',$nId).'</td>';
 if(isset($bNn)){
  if($bNn) $sSta='<img src="punktGrn.gif" width="12" height="12" border="0" title="Umfragezuweisungen sind vorhanden">';
  else $sSta='<img src="punktRtGn.gif" width="12" height="12" border="0" title="keine Umfragezuweisungen eingetragen">';
 }else $sSta='<img src="punktRot.gif" width="12" height="12" border="0" title="Nutzer ohne Umfragezuweisungen">';
 echo NL.' <td align="center">'.$sSta.'</td>';
 if(!$s=$aN[2]) $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
 if(!$s=$aN[4]) $s='&nbsp;';
 echo NL.' <td>'.$s.'</td>';
 for($j=5;$j<$nFelder;$j++){if(!$s=$aN[$j]) $s='&nbsp;'; echo NL.' <td>'.(UMF_SQL?$s:str_replace('`,',';',$s)).'</td>';}
 echo NL.'</tr>';
?>
</table><br />

<form name="NutzerListe" action="nutzerUmfragen.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="nnr" value="<?php echo $nId?>" />
<input type="hidden" name="qs" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td style="width:25%"><b>Umfrage</b></td><td style="width:4%"><b>aktiv</b></td><td><b>Eigenschaft</b></td></tr>
<?php
 reset($aU);
 foreach($aU as $k=>$v){
  echo ' <tr class="admTabl">'.NL;
  echo '  <td>'.$v.'</td>'.NL;
  echo '  <td align="center"><input class="admCheck" type="checkbox" name="ta'.$k.'" value="1'.(isset($aA[$k])?'" checked="checked':'').'" /></td>'.NL;
  echo '  <td><input type="text" name="tb'.$k.'" value="'.(isset($aB[$k])?fUmfZeigeBedingung($aB[$k]):'').'" style="width:160px;" /></td>'.NL;
  echo ' </tr>'.NL;
 }
?>
<tr class="admTabl">
 <td>alle Umfragezuordnungen löschen<input type="hidden" name="LschOK" value="<?php echo $sLschOK;?>" /></td>
 <td align="center"><input type="image" src="iconLoeschen.gif" name="lsch" width="12" height="13" align="top" border="0" title="Benutzer-Umfragen löschen" tabindex="2" /></td>
 <td>(Benutzer <i><?php echo $aN[2]?> ist dann nicht mehr in der Liste der Umfragezuweisungen)</i></td>
</tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" name="Eintragen" value="Eintragen" tabindex="1" /></p>
</div>
</form>

<p style="text-align:center"><?php
echo '[ <a href="nutzerListe.php?'.$sQo.'">Benutzerliste</a> ] [ <a href="nutzerZuweisung.php?'.$sQo.'">Benutzer &amp; Umfragen</a> ] [ <a href="nutzerAendern.php?'.$sQ.'">Benutzerdaten</a> ] ';
?></p>

<p><u>Hinweise</u>:</p>
<p>Die einzelnen Umfragen können für den Benutzer erlaubt oder deaktiviert werden. Zusätzlich ist es bei erlaubter Umfrage möglich eventuell mit speziellen Begrenzungen zu arbeiten:</p>
<ul>
<li>Umfragedurchführung mit begrenzter Anzahl: Die Umfrage kann vom Benutzer insgesamt höchstens so oft wie angegeben durchlaufen werden. Danach wird er im Benutzermenü nicht mehr angeboten.<br>Muster: <i>5x</i></li>
<li>Umfragedurchführung am Stichtag: Die Umfrage wird nur am eingetragenen Tag im Benutzerzentrum angeboten.<br>Muster: <i>am 30.12.2015</i></li>
<li>Umfragedurchführung ab Stichtag: Die Umfrage wird erst ab dem angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>ab 30.12.2015</i></li>
<li>Umfragedurchführung bis Stichtag: Die Umfrage wird nur bis zum angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>bis 30.12.2015</i></li>
</ul>

<p>Das Statussymbol des Benutzers bedeutet:</p>
<table border="0" cellpadding="2" cellspacing="0">
<tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktRot.gif" width="12" height="12" border="0" title="Benutzer ohne Umfragezuweisungen"></td>
<td>Benutzer ist nicht in der Liste der individuellen Umfragezuweisungen enthalten. Er bekommt im Benutzerzentrum die im Menüpunkt <i>Benutzerfunktionen</i> für alle Benutzer eingestellten Umfragen zu sehen.</td>
</tr><tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktRtGn.gif" width="12" height="12" border="0" title="keine Umfragezuweisungen eingetragen"></td>
<td>Benutzer ist in der Liste der Umfragezuweisungen enthalten, hat aber momentan keine Umfragezuweisungen. Er bekommt damit im Benutzerzentrum aktuell <i>keine</i> Umfragen angeboten.</td>
</tr><tr>
<td style="padding-left:22px;padding-right:5px;vertical-align:top;"><img src="punktGrn.gif" width="12" height="12" border="0" title="Umfragezuweisungen sind vorhanden"></td>
<td>Benutzer hat individuelle Umfragezuweisungen. Er bekommt im Benutzerzentrum genau diese Umfragen angeboten.</td>
</tr>
</table>

<?php
echo fSeitenFuss();

function fUmfZeigeBedingung($s){
 if($p=strpos($s,'-')){
  $p=max($p-4,0); $a=explode('-',substr($s,$p,10)); $s=trim(substr($s,0,$p)).sprintf(' %02d.%02d.%04d',$a[2],$a[1],$a[0]);
 }
 return $s;
}
function fUmfHoleBedingung($s){
 if($p=strpos($s,'.')){
  if(substr($s,0,3)=='bis'){$w='bis'; $t=trim(substr($s,3));}
  elseif(substr($s,0,2)=='am'){$w='am'; $t=trim(substr($s,2));}
  elseif(substr($s,0,2)=='ab'){$w='ab'; $t=trim(substr($s,2));}
  else{$w='am'; $t=trim(substr($s,max($p-2,0)));}
  $a=explode('.',$t); if(!isset($a[2])) $a[2]=date('Y'); elseif((int)$a[2]<100) $a[2]+=2000;
  if(isset($a[1])) $s=$w.sprintf('%04d-%02d-%02d',$a[2],$a[1],$a[0]); else $s='';
 }else if(strlen($s)>0&&(substr($s,0,1)=='0'||($s=(int)$s))) $s=sprintf('%0d',$s).'x'; else $s='';
 return $s;
}
?>