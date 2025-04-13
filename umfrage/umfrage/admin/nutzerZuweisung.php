<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf(' Benutzer-Umfragenzuweisung','<script language="JavaScript" type="text/javascript">
function fSelAll(bStat){
 for(var i=0;i<self.document.NutzerListe.length;++i)
  if(document.NutzerListe.elements[i].type=="checkbox"&&document.NutzerListe.elements[i].name.substr(0,4)=="lsch") document.NutzerListe.elements[i].checked=bStat;
}
function fSetFolge(sFolge){
 document.NutzerListe.submit();
}
function fSetAll(bOn){
 var oEl=""; var sNa=""; var sT=document.NutzerListe.TBAll.value;
 for(var i=0;i<document.NutzerListe.elements.length;++i){
  oEl=document.NutzerListe.elements[i]; sNa=oEl.name;
  if(bOn){if(sNa.substring(0,2)=="ta") oEl.checked=true; else if(sNa.substring(0,2)=="tb") oEl.value=sT;}
  else if(sNa.substring(0,2)=="ta") oEl.checked=false;
 }
}
</script>','NZw');

$sUfr=(isset($_POST['folge'])?$_POST['folge']:(isset($_GET['folge'])?$_GET['folge']:'0')); $sLOk='';

if(isset($_POST['Lsch_x'])&&$_POST['Lsch_x']>0||isset($_POST['Lsch_y'])&&$_POST['Lsch_y']>0){ //Nutzerzuweisung loeschen
 $bOK=false; $aNr=array(); $bSuch=false;
 foreach($_POST as $k=>$xx){
  if(substr($k,0,4)=='lsch') $aNr[(int)substr($k,4)]=true; //Loeschnummern
  elseif(substr($k,0,2)=='nr') $bSuch=true; //vom Suchformular
 }
 if(count($aNr)){
  if(isset($_POST['LOk'])&&$_POST['LOk']=='1'){
   if(!UMF_SQL){ //Textdatei
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nSaetze=count($aD); $bCh=false;
    for($i=1;$i<$nSaetze;$i++){$s=$aD[$i]; $n=(int)substr($s,0,strpos($s,';')); if(isset($aNr[$n])&&$aNr[$n]){$aD[$i]=''; $bCh=true;}} //loeschen
    if($bCh){
     if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Zuweisung,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $sMeld='<p class="admErfo">Die markierten Umfragezuweisungen wurden gelöscht.</p>'; $aNr=array();
     }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.UMF_Daten.UMF_Zuweisung.'</i>',UMF_TxDateiRechte).'</p>';
    }else $sMeld='<p class="admMeld">Die Umfragezuweisungen bleiben unverändert.</p>';
   }elseif($DbO){ //bei SQL
    $s=''; foreach($aNr as $k=>$xx) $s.=' OR Benutzer='.$k;
    if($DbO->query('DELETE FROM '.UMF_SqlTabZ.' WHERE '.substr($s,4))){
     $sMeld='<p class="admErfo">Die markierten Umfragezuweisungen wurden gelöscht.</p>'; $aNr=array();
    }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
   }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
  }else{$sLOk='1'; $sMeld='<p class="admFehl">Wollen Sie alle Umfragezuweisungen der markierten Nutzer wirklich löschen?</p>';}
 }elseif($bSuch) $sMeld='<p class="admMeld">Suchergebnis</p>';
  else $sMeld='<p class="admMeld">Die Umfragezuweisungen bleiben unverändert.</p>';
}
if(isset($_POST['Eintragen'])&&$_POST['Eintragen']=='Eintragen'||(isset($_POST['Lsch_x'])&&$_POST['Lsch_x']==0&&isset($_POST['Lsch_y'])&&$_POST['Lsch_y']==0)){
 $aNn=array(); $aD=array(); reset($_POST); $bCh=false; $s=';'.$sUfr.':';
 foreach($_POST as $k=>$xx) if(substr($k,0,2)=='nn'){$i=substr($k,2);
  $aNn[$i]=(isset($_POST['ta'.$i])&&$_POST['ta'.$i]==1); $aNb[$i]=(($t=trim($_POST['tb'.$i]))?fUmfHoleBedingung($t):'');
 }
 if(!UMF_SQL){ //Textdaten
  $aTmp=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nCnt=count($aTmp);
  for($i=1;$i<$nCnt;$i++){$v=rtrim($aTmp[$i]); $aD[(int)$v]=$v.(strpos($v,':')>0?';':'');}
  foreach($aNn as $k=>$v){
   if($v){
    if(isset($aD[$k])){ //Nutzer bekannt
     if($p=strpos($aD[$k],$s)){ //Umfrage schon da
      $t=$aD[$k]; $q=strpos($t,';',$p+1);
      if(substr($t,$p,$q-$p)!=$s.$aNb[$k]){$aD[$k]=substr_replace($t,$s.$aNb[$k],$p,$q-$p); $bCh=true;} //ggf. aendern
     }else{$aD[$k].=substr($s,1).$aNb[$k].';'; $bCh=true;} // Umfrage neu
    }else{$aD[$k]=$k.$s.$aNb[$k].';'; $bCh=true;} //Nutzer neu
   }else{ //Umfrage inaktiv
    if(isset($aD[$k])) if($p=strpos($aD[$k],$s)){ //Umfrage loeschen
     $t=$aD[$k]; $q=strpos($t,';',$p+1); $aD[$k]=substr_replace($t,'',$p,$q-$p); $bCh=true;
  }}}
  if($bCh){
   ksort($aD); reset($aD); $sD='Benutzer;zugewiesene_Umfragen'.NL;
   foreach($aD as $s) $sD.=(substr_count($s,':')>0?substr($s,0,-1):$s).NL;
   if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Zuweisung,'w')){
    fwrite($f,rtrim($sD)); fclose($f); $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
   }else $sMeld='<p class="admFehl">Die Zuweisungen durften nicht in <i>'.UMF_Zuweisung.'</i> gespeichert werden.</p>';
  }else $sMeld='<p class="admMeld">Die Zuweisungen bleiben unverändert.</p>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT Benutzer,Umfragen FROM '.UMF_SqlTabZ)){
   while($a=$rR->fetch_row()) $aD[(int)$a[0]]='#;'.$a[1].(strlen($a[1])>0?';':''); $rR->close();
  }
  foreach($aNn as $k=>$v){
   if($v){
    if(isset($aD[$k])){ //Nutzer bekannt
     if($p=strpos($aD[$k],$s)){ //Umfrage schon da
      $t=$aD[$k]; $q=strpos($t,';',$p+1);
      if(substr($t,$p,$q-$p)!=$s.$aNb[$k]){$aD[$k]=substr_replace($t,$s.$aNb[$k],$p,$q-$p); $bCh=true;} //ggf. aendern
     }else{$aD[$k].=substr($s,1).$aNb[$k].';'; $bCh=true;}
    }else{$aD[$k]='#'.$s.$aNb[$k].';'; $aN[$k]=true; $bCh=true;} //Nutzer neu
   }else{ //Umfrage inaktiv
    if(isset($aD[$k])) if($p=strpos($aD[$k],$s)){ //Umfrage loeschen
     $t=$aD[$k]; $q=strpos($t,';',$p+1); $aD[$k]=substr_replace($t,'',$p,$q-$p); $bCh=true;
  }}}
  if($bCh){
   ksort($aD); reset($aD); $bCh=false;
   foreach($aD as $k=>$s) if(substr_count($s,';')>1){
    if(isset($aN[$k])){if($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabZ.' (Benutzer,Umfragen) VALUES("'.$k.'","'.substr($s,2,-1).'")')) $bCh=true;}
    else{if($DbO->query('UPDATE IGNORE '.UMF_SqlTabZ.' SET Umfragen="'.substr($s,2,-1).'" WHERE Benutzer="'.$k.'"')) $bCh=true;}
   }else{if($DbO->query('UPDATE IGNORE '.UMF_SqlTabZ.' SET Umfragen="" WHERE Benutzer="'.$k.'"')) $bCh=true;}
   if($bCh) $sMeld='<p class="admErfo">Die Zuweisungen wurden gespeichert.</p>';
   else $sMeld='<p class="admFehl">Die Zuweisungen konnten nicht gespeichert werden.</p>';
  }else $sMeld='<p class="admMeld">Die Zuweisungen bleiben unverändert.</p>';
}}

$aQ=array(); $sQ=''; //Suchparameter
$aFelder=explode(';',UMF_NutzerFelder); $nFelder=count($aFelder); for($i=2;$i<$nFelder;$i++) $aFelder[$i]=str_replace('´,',';',$aFelder[$i]);
if($Nr1=(isset($_POST['nr1'])?$_POST['nr1']:'').(isset($_GET['nr1'])?$_GET['nr1']:'')){$a1Filt[0]=$Nr1; $sQ.='&amp;nr1='.$Nr1; $aQ['nr1']=$Nr1;}
if($Nr2=(isset($_POST['nr2'])?$_POST['nr2']:'').(isset($_GET['nr2'])?$_GET['nr2']:'')){$a2Filt[0]=$Nr2; $sQ.='&amp;nr2='.$Nr2; $aQ['nr2']=$Nr2;}
$Onl=(isset($_POST['onl'])?$_POST['onl']:'').(isset($_GET['onl'])?$_GET['onl']:''); if(strlen($Onl)){$a1Filt[1]=$Onl; $sQ.='&amp;onl='.((int)$Onl);}
for($i=2;$i<$nFelder;$i++){
 $s=(isset($_POST['n1'.$i])?$_POST['n1'.$i]:'').(isset($_GET['n1'.$i])?$_GET['n1'.$i]:''); if(strlen($s)){$a1Filt[$i]=$s; $sQ.='&amp;n1'.$i.'='.rawurlencode($s); $aQ['n1'.$i]=$s;}
 $s=(isset($_POST['n2'.$i])?$_POST['n2'.$i]:'').(isset($_GET['n2'.$i])?$_GET['n2'.$i]:''); if(strlen($s)){$a2Filt[$i]=$s; $sQ.='&amp;n2'.$i.'='.rawurlencode($s); $aQ['n2'.$i]=$s;}
 $s=(isset($_POST['n3'.$i])?$_POST['n3'.$i]:'').(isset($_GET['n3'.$i])?$_GET['n3'.$i]:''); if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;n3'.$i.'='.rawurlencode($s); $aQ['n3'.$i]=$s;}
}

//Daten bereitstellen
$aTmp=array(); $aIdx=array(); $aNn=array(); $aZ=array(); $aB=array(); $sQ2='';
$sSF=';'.$sUfr.':'; if($sUfr!=UMF_TxStandardUmf) $sQ2.='&amp;folge='.$sUfr;
$sUmfOpt='<option value="0">'.UMF_TxStandardUmf.'</option>';
for($i=1;$i<=26;$i++){
 $k=chr(64+$i); $s=constant('UMF_Umfr'.$k);
 if(substr($s,0,1)!=';'){$a=explode(';',$s); if($s=$a[3]) $sUmfOpt.='<option value="'.$k.($sUfr!=$k?'':'" selected="selected').'">'.$s.' ('.$k.')'.'</option>'; }
}
if(!UMF_SQL){ //Textdaten
 $aD=@file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nCnt=count($aD);
 for($i=1;$i<$nCnt;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true;
  if(isset($a1Filt)&&is_array($a1Filt)){reset($a1Filt); //Suchfiltern 1,2
   foreach($a1Filt as $j=>$v) if($b&&$j>1){
    if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')){if(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fUmfDecode($a[$j]):$a[$j])):''),$w)) $b2=true; else $b2=false;} else $b2=false;
    if(!(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fUmfDecode($a[$j]):$a[$j])):''),$v)||$b2)) $b=false;
   }elseif($j==0){if(!$w=(isset($a2Filt[0])?$a2Filt[0]:0)){if($a[0]!=$v) $b=false;}else{if($a[0]<$v||$a[0]>$w) $b=false;}}
  }
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v){if(stristr((isset($a[$j])?str_replace('`,',';',($j<5?fUmfDecode($a[$j]):$a[$j])):''),$v)){$b=false; break;}}
  }
  if($b){ //Datensatz gueltig
   $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,akt
   $aTmp[$sNr][2]=fUmfDecode($a[2]); $aTmp[$sNr][3]=fUmfDecode($a[3]); $aTmp[$sNr][4]=fUmfDecode($a[4]); //User,PW,E-Mail
   for($j=5;$j<$nFelder;$j++) $aTmp[$sNr][$j]=(isset($a[$j])?str_replace('\n ',NL,str_replace('`,',';',$a[$j])):'');
   $aIdx[$sNr]=sprintf('%0'.UMF_NummerStellen.'d',$i);
  }
 }
 $aD=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nCnt=count($aD); //Zuweisungen holen
 for($i=1;$i<$nCnt;$i++){
  $t=rtrim($aD[$i]).';'; $n=(int)$t; $aNn[$n]=(strpos($t,':')>0);
  if($p=strpos($t,$sSF)){$aZ[$n]=true; $p=strpos($t,':',$p); $q=strpos($t,';',$p++); $t=substr($t,$p,$q-$p); $aB[$n]=$t;}
 }
}elseif($DbO){$s=''; //SQL
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  if($j>1){
   $sS=($j==4?'eMail':($j==3?'Passwort':($j==2?'Benutzer':'dat_'.$j)));
   $s.=' AND('.$sS.' LIKE "%'.$v.'%"'; if($w=(isset($a2Filt[$j])?$a2Filt[$j]:'')) $s.=' OR '.$sS.' LIKE "%'.$w.'%"'; $s.=')';
  }elseif($j==0){
   if($w=(isset($a2Filt[0])?(int)$a2Filt[0]:'')) $s.=' AND Nummer BETWEEN '.(int)$v.' AND '.(int)$w; else $s.=' AND Nummer='.(int)$v;
  }else $s.=' AND aktiv="'.$v.'"';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  if($j>1) $s.=' AND NOT('.($j==4?'eMail':($j==3?'Passwort':($j==2?'Benutzer':'dat_'.$j))).' LIKE "%'.$v.'%")';
  else $s.=' AND '.($j==0?'Nummer':'aktiv').'<>"'.$v.'"';
 }
 $sS=''; for($j=5;$j<$nFelder;$j++) $sS.=',dat_'.$j; $i=0;
 if($rR=$DbO->query('SELECT Nummer,aktiv,Benutzer,Passwort,eMail'.$sS.' FROM '.UMF_SqlTabN.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
  while($a=$rR->fetch_row()){
   $sNr=$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,akt
   for($j=2;$j<=$nFelder;$j++) $aTmp[$sNr][$j]=(isset($a[$j])?$a[$j]:'');
   $aIdx[$sNr]=sprintf('%0'.UMF_NummerStellen.'d',++$i);
  }$rR->close();
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
 if($rR=$DbO->query('SELECT Benutzer,Umfragen FROM '.UMF_SqlTabZ.' ORDER BY Benutzer')){ //Zuweisungen holen
  while($a=$rR->fetch_row()){
   $t='#;'.$a[1].';'; $n=(int)$a[0]; $aNn[$n]=(strpos($t,':')>0);
   if($p=strpos($t,$sSF)){$aZ[$n]=true; $p=strpos($t,':',$p); $q=strpos($t,';',$p++); $t=substr($t,$p,$q-$p); $aB[$n]=$t;}
  }$rR->close();
 }
}else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADU_NutzerLaenge;
if(ADU_NutzerRueckw) arsort($aIdx);
$aD=array(); reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];

if(!$sMeld) if(UMF_Nutzerverwaltung){if(!$sQ) $sMeld='<p class="admMeld">Benutzer &amp; Umfragezuweisungen</p>'; else $sMeld='<p class="admMeld">Suchergebnis Benutzer</p>';}else $sMeld='<p class="admFehl">Die Benutzerverwaltung ist momentan inaktiv!</p>';
$sQ=(KONF>0?'konf='.KONF.$sQ2.$sQ:substr($sQ2.$sQ,5));

//Scriptausgabe
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $sMeld;?></td>
  <td align="right">[ <a href="nutzerSuche.php<?php if($sQ) echo '?'.$sQ?>">Suche</a> ]</td>
 </tr>
</table>
<?php
$sNavigator=fUmfNavigator($nStart,count($aIdx),ADU_NutzerLaenge,$sQ); echo $sNavigator;
if($nStart>1) $sQ.='&amp;start='.$nStart; $aQ['start']=$nStart; $sAmp=($sQ?'&amp;':'');
?>

<form name="NutzerListe" action="nutzerZuweisung.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1" style="width:auto;">
<?php
 $nFelder=min(8,$nFelder); $bLoeschen=file_exists('nutzerLoeschen.php');
 echo '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td align="center"><b>Nr</b>.</td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td><b>'.$aFelder[2].'</b></td>'.NL.' <td width="1%">&nbsp;</td>';
 for($j=4;$j<$nFelder;$j++){if(!$s=$aFelder[$j]) $s='&nbsp;'; echo NL.' <td><b>'.($s!='GUELTIG_BIS'?$s:(UMF_TxNutzerFrist>''?UMF_TxNutzerFrist:$s)).'</b></td>';}
 echo NL.' <td align="center"><b>Umfr.:</b> <select name="folge" size="1" onchange="fSetFolge(this.value)">'.$sUmfOpt.'</select></td>';
 echo NL.'</tr>';
 foreach($aD as $a){ //Datenzeilen ausgeben
  echo NL.'<tr class="admTabl">'; $Id=$a[0];
  echo NL.' <td>'.($bLoeschen?'<input class="admCheck" type="checkbox" name="lsch'.$Id.'" value="1"'.(isset($aNr[$Id])&&$aNr[$Id]?' checked="checked"':'').' />':'').sprintf('%05d',$Id).'</td>';
  if(isset($aNn[$Id])){
   if($aNn[$Id]) $sSta='<img src="punktGrn.gif" width="12" height="12" border="0" title="Umfragezuweisungen sind vorhanden">';
   else $sSta='<img src="punktRtGn.gif" width="12" height="12" border="0" title="keine Umfragezuweisungen eingetragen">';
  }else $sSta='<img src="punktRot.gif" width="12" height="12" border="0" title="Benutzer ohne Umfragezuweisungen">';
  echo NL.' <td align="center">'.$sSta.'</td>';
  if(!$s=$a[2]) $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
  echo NL.' <td align="center"><a href="nutzerUmfragen.php?'.$sQ.$sAmp.'nnr='.$Id.'"><img src="iconAuswahl.gif" width="12" height="13" border="0" title="Umfragen zuweisen"></a></td>';
  if(!$s=$a[4]) $s='&nbsp;';
  echo NL.' <td>'.$s.'</td>';
  for($j=5;$j<$nFelder;$j++){if(!$s=$a[$j]) $s='&nbsp;'; echo NL.' <td>'.(UMF_SQL?$s:str_replace('`,',';',$s)).'</td>';}
  echo NL.' <td align="center"><input class="admCheck" type="checkbox" name="ta'.$Id.'" value="1"'.(isset($aZ[$Id])&&$aZ[$Id]?' checked="checked"':'').' /> <input type="text" name="tb'.$Id.'" value="'.(isset($aB[$Id])?fUmfZeigeBedingung($aB[$Id]):'').'" style="width:110px;" /><input type="hidden" name="nn'.$Id.'" value="1" /></td>';
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="fraAll" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" name="Lsch" src="iconLoeschen.gif" width="12" height="13" align="top" border="0" title="Zuweisungen markierter Benutzer löschen" tabindex="2" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo $nFelder-1?>" style="text-align:right">für alle</td>
 <td align="center"><input class="admCheck" type="checkbox" name="TAAll" value="1" onclick="fSetAll(this.checked)" /> <input type="text" name="TBAll" value="" style="width:110px;" /></td>
 </tr>
</table>
<input type="hidden" name="LOk" value="<?php echo $sLOk?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>

<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" name="Eintragen" value="Eintragen" tabindex="1"></p>
</div>
</form>
<?php echo $sNavigator; ?>

<p style="text-align:center"><?php

echo '[ <a href="nutzerListe.php?'.(($p=strpos($sQ,'olge='))?substr($sQ,0,max($p-6,0)):$sQ).'">Benutzerliste</a> ] ';
?></p>

<p><u>Hinweise</u>:</p>
<p>Die ausgewählte Umfrage kann für die Benutzer generell erlaubt oder deaktiviert werden. Zusätzlich ist es bei erlaubter Umfrage möglich eventuell mit speziellen Begrenzungen zu arbeiten:</p>
<ul>
<li>Umfragedurchführung mit begrenzter Anzahl: Die Umfrage kann vom Benutzer insgesamt höchstens so oft wie angegeben durchlaufen werden. Danach wird er im Benutzermenü nicht mehr angeboten.<br>Muster: <i>5x</i></li>
<li>Umfragedurchführung am Stichtag: Die Umfrage wird nur am eingetragenen Tag im Benutzerzentrum angeboten.<br>Muster: <i>am 30.12.2015</i></li>
<li>Umfragedurchführung ab Stichtag: Die Umfrage wird erst ab dem angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>ab 30.12.2015</i></li>
<li>Umfragedurchführung bis Stichtag: Die Umfrage wird nur bis zum angegebenen Datum über das Benutzerzentrum angeboten.<br>Muster: <i>bis 30.12.2015</i></li>
</ul>

<p>Die Statussymbole bedeuten:</p>
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

function fUmfNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="nutzerZuweisung.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerZuweisung.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerZuweisung.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:3px;margin-bottom:3px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}

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