<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerübersicht','<script language="JavaScript" type="text/javascript">
function fSelAll(bStat){
 for(var i=0;i<self.document.NutzerListe.length;++i)
  if(self.document.NutzerListe.elements[i].type=="checkbox") self.document.NutzerListe.elements[i].checked=bStat;
}
</script>','NNl');

if($_SERVER['REQUEST_METHOD']=='POST'){ //Nutzer loeschen
 $sLOk=''; $bOK=false; $aNr=array(); $bSuch=false;
 foreach($_POST as $k=>$xx){
  if(substr($k,0,4)=='lsch') $aNr[(int)substr($k,4)]=true; //Loeschnummern
  elseif(substr($k,0,2)=='nr') $bSuch=true; //vom Suchformular
 }
 if(count($aNr)){
  if(isset($_POST['LOk'])&&$_POST['LOk']=='1'){
   if(!UMF_SQL){ //Textdatei
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $nMx=0;
    for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); $nMx=max($n,$nMx); if(isset($aNr[$n])&&$aNr[$n]) $aD[$i]='';} //löschen
    if(substr($aD[0],0,7)!='Nummer_') $aD[0]='Nummer_'.$nMx.substr(UMF_NutzerFelder,6).NL; //Kopfzeile defekt
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld='<p class="admErfo">Die markierten Benutzer wurden gelöscht.</p>';
    }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.UMF_Daten.UMF_Nutzer.'</i>',UMF_TxDateiRechte).'</p>';
   }elseif($DbO){ //bei SQL
    $s=''; foreach($aNr as $k=>$xx) $s.=' OR Nummer='.$k;
    if($DbO->query('DELETE FROM '.UMF_SqlTabN.' WHERE '.substr($s,4))){
     $sMeld='<p class="admErfo">Die markierten Benutzer wurden gelöscht.</p>';
    }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
   }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
  }else{$sLOk='1'; $sMeld='<p class="admFehl">Wollen Sie die markierten Benutzer wirklich löschen?</p>';}
 }elseif($bSuch) $sMeld='<p class="admMeld">Suchergebnis</p>';
  else $sMeld='<p class="admMeld">Die Benutzerdaten bleiben unverändert.</p>';
}elseif($nNum=(isset($_GET['num'])?$_GET['num']:'')){ //Nutzerstatus aendern
 $nSta=(isset($_GET['sta'])?(int)$_GET['sta']:0); $sNDat="NUMMER: ".sprintf('%05d',$nNum);
 if(!UMF_SQL){ //Textdatei
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$nNum.';'; $p=strlen($s); $bNeu=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
   $s=$aD[$i]; if((int)substr($s,$p,1)==1-$nSta){$aD[$i]=substr_replace($s,$nSta,$p,1); $bNeu=true;} break;
  }
  if($bNeu) if($f=@fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){//neu schreiben
   fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   if($nSta==1){
    $a=explode(';',rtrim($aD[$i])); $sEml=fUmfDeCode($a[4]); $aFelder=explode(';',UMF_NutzerFelder); $nFelder=count($aFelder);
    for($i=2;$i<$nFelder;$i++) $sNDat.="\n".strtoupper($aFelder[$i]!='GUELTIG_BIS'?$aFelder[$i]:(UMF_TxNutzerFrist>''?UMF_TxNutzerFrist:$aFelder[$i])).': '.($i>4?$a[$i]:fUmfDeCode($a[$i]));
   }
   $sMeld='<p class="admErfo">Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
  }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.UMF_Daten.UMF_Nutzer.'</i>',UMF_TxDateiRechte).'</p>';
 }elseif($DbO){ //bei SQL
  if($DbO->query('UPDATE IGNORE '.UMF_SqlTabN.' SET aktiv="'.$nSta.'" WHERE Nummer='.$nNum)){
   $sMeld='<p class="admErfo">Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
   if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer='.$nNum)){
    if($a=$rR->fetch_row()) $sEml=$a[4]; $rR->close(); $aFelder=explode(';',UMF_NutzerFelder); $nFelder=count($aFelder);
    for($i=2;$i<$nFelder;$i++) $sNDat.="\n".strtoupper($aFelder[$i]).': '.($i!=3?$a[$i]:fUmfDeCode($a[3]));
   }
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlAendr.'</p>';
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
 if(isset($sEml)&&UMF_NutzerAktivMail){ //Aktivierungsmail
  include_once UMF_Pfad.'class.plainmail.php'; $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
  if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
  $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
  $sWww=UMF_Www; if($p=strpos($sWww,'/')) $sWww=substr($sWww,0,$p);
  $Mailer->Subject=str_replace('#',$sWww,UMF_TxNutzerAktivBtr);
  $Mailer->Text=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",UMF_TxNutzerAktivTxt))); $Mailer->Send();
 }
}
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
$aD=array(); $aTmp=array(); $aIdx=array(); $aPflicht=explode(';',UMF_NutzerPflicht);
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
 }$aD=array();
}elseif($DbO){ //SQL
 $s='';
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
}else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';

if(!$nStart=(int)((isset($_GET['start'])?$_GET['start']:'').(isset($_POST['start'])?$_POST['start']:''))) $nStart=1; $nStop=$nStart+ADU_NutzerLaenge;
if(ADU_NutzerRueckw) arsort($aIdx);
reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];

if(!$sMeld) if(UMF_Nutzerverwaltung){if(!$sQ) $sMeld='<p class="admMeld">Benutzerliste</p>'; else $sMeld='<p class="admMeld">Suchergebnis</p>';}else $sMeld='<p class="admFehl">Die Benutzerverwaltung ist momentan inaktiv!</p>';
$sQ=(KONF>0?'konf='.KONF.$sQ:substr($sQ,5));

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

<form name="NutzerListe" action="nutzerListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 $bAendern=file_exists('nutzerAendern.php'); $bKontakt=file_exists('nutzerKontakt.php'); $bLoeschen=file_exists('nutzerLoeschen.php');
 echo '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td align="center"><b>Nr</b>.</td>'.NL.' <td width="1">&nbsp;</td>'.NL.' <td width="1">&nbsp;</td>'.NL.' <td><b>'.$aFelder[2].'</b></td>'.(UMF_NutzerUmfragen&&file_exists('nutzerUmfragen.php')?NL.' <td width="1">&nbsp;</td>':'');
 for($j=4;$j<$nFelder;$j++){if($s=$aFelder[$j]){if($s=='GUELTIG_BIS') $s=UMF_TxNutzerFrist;} else $s='&nbsp;'; echo NL.' <td><b>'.$s.'</b></td>';} echo NL.'</tr>';
 foreach($aD as $a){ //Datenzeilen ausgeben
  echo NL.'<tr class="admTabl">'; $Id=$a[0];
  echo NL.' <td>'.($bLoeschen?'<input class="admCheck" type="checkbox" name="lsch'.$Id.'" value="1"'.(isset($aNr[$Id])&&$aNr[$Id]?' checked="checked"':'').' />':'').sprintf('%05d',$Id).'</td>';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerAendern.php?'.$sQ.$sAmp.'nnr='.$Id.'"><img src="iconAendern.gif" width="12" height="13" border="0" title="Benutzerdaten bearbeiten"></a>':'&nbsp;').'</td>';
  if($sSta=$a[1]) $sSta='0"><img src="punktGrn.gif" width="12" height="12" border="0" title="freigeschaltet">';
  else $sSta='1"><img src="punktRot.gif" width="12" height="12" border="0" title="inaktiv - jetzt freischalten">';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerListe.php?'.$sQ.$sAmp.'num='.$Id.'&amp;sta='.$sSta.'</a>':substr($sSta,3)).'</td>';
  if(!$s=$a[2]) $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
  if(UMF_NutzerUmfragen&&file_exists('nutzerUmfragen.php')) echo NL.' <td align="center"><a href="nutzerUmfragen.php?'.$sQ.$sAmp.'nnr='.$Id.'"><img src="iconAuswahl.gif" width="12" height="13" border="0" title="Umfragen zuweisen"></a></td>';
  if(!$s=$a[4]) $s='&nbsp;';
  echo NL.' <td>'.($bKontakt?'<a href="nutzerKontakt.php?'.$sQ.$sAmp.'nnr='.$Id.'"><img src="iconMail.gif" width="16" height="16" border="0" style="margin-right:2px;vertical-align:middle" title="'.$s.' kontaktieren"></a>':'').$s.'</td>';
  for($j=5;$j<$nFelder;$j++){if(!$s=$a[$j]) $s='&nbsp;'; echo NL.' <td>'.(UMF_SQL?$s:str_replace('`,',';',$s)).'</td>';}
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="fraAll" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" src="iconLoeschen.gif" width="12" height="13" align="top" border="0" title="markierte Benutzer löschen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo $nFelder+(UMF_NutzerUmfragen&&file_exists('nutzerUmfragen.php')?0:-1)?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="LOk" value="<?php echo $sLOk?>" /><?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="'.$k.'" value="'.$v.'" />'?>

</form>
<?php echo $sNavigator; ?>

<p style="text-align:center"><?php
if($bAendern) echo '[ <a href="nutzerAendern.php?'.$sQ.$sAmp.'neu=1">neuer Benutzer</a> ] ';
if(UMF_NutzerUmfragen&&file_exists('nutzerZuweisung.php')) echo '[ <a href="nutzerZuweisung.php?'.$sQ.'">Benutzer &amp; Umfragen</a> ] ';
?></p>

<?php
echo fSeitenFuss();

function fUmfNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge); if($sQry) $sQry.='&amp;';
 $s ='<td style="width:16px;text-align:center;"><a href="nutzerListe.php?'.$sQry.'start=1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?'.$sQry.'start='.(($i-1)*$nListenLaenge+1).'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?'.$sQry.'start='.(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:3px;margin-bottom:3px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}
?>