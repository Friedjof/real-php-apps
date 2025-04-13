<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerliste','<script language="JavaScript" type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.NutzerListe.length;++i)
   if(self.document.NutzerListe.elements[i].type=="checkbox") self.document.NutzerListe.elements[i].checked=bStat;
 }
 function druWin(sURL){dWin=window.open(sURL,"druck","width=600,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus();}
</script>','NNl');

//Nutzer loeschen
$aId=array(); $sQ=''; $sLschFrg=''; $bOK=false;
if($_SERVER['REQUEST_METHOD']=='POST'&&!isset($_POST['Suche'])){
 foreach($_POST as $k=>$xx) if(substr($k,4,1)=='L') $aId[(int)substr($k,5)]=true; //Loeschnummern
 if(count($aId)){
  if($_POST['kalLsch']=='1'){
   if(!KAL_SQL){ //Textdatei
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nMx=0;
    for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); $nMx=max($n,$nMx); if(isset($aId[$n])&&$aId[$n]) $aD[$i]='';} //loeschen
    if(substr($aD[0],0,7)!='Nummer_'){ //Kopfzeile defekt
     $s='Nummer_'.$nMx.';Sitzung;aktiv'; $nNutzFelder=count($kal_NutzerFelder); for($i=3;$i<$nNutzFelder;$i++) $s.=';'.$kal_NutzerFelder[$i]; $aD[0]=$s.NL;
    }
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Msg='<p class="admMeld">Die markierten Benutzer wurden gel&ouml;scht.</p>';
    }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Nutzer.'</i>',KAL_TxDateiRechte).'</p>';
   }elseif($DbO){ //bei SQL
     $s=''; foreach($aId as $k=>$xx) $s.=' OR nr='.$k;
     if($DbO->query('DELETE FROM '.KAL_SqlTabN.' WHERE '.substr($s,4))){
      $Msg='<p class="admMeld">Die markierten Benutzer wurden gel&ouml;scht.</p>';
     }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
  }else{$sLschFrg='1'; $Msg='<p class="admFehl">Wollen Sie die markierten Benutzer wirklich l&ouml;schen?</p>';}
 }else $Msg='<p class="admMeld">Die Benutzerdaten bleiben unver&auml;ndert.</p>';
}else
//Nutzerstatus aendern
if($nNum=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'')){
 $nSta=(isset($_GET['kal_Status'])?(int)$_GET['kal_Status']:''); $sNDat="NUMMER: ".sprintf('%04d',$nNum);
 if(!KAL_SQL){ //Textdatei
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNum.';'; $p=strlen($s); $bNeu=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
   $s=$aD[$i]; $p=strpos($s,';',$p)+1; if((int)substr($s,$p,1)==1-$nSta||substr($s,$p,1)=='2'){$aD[$i]=substr_replace($s,$nSta,$p,1); $bNeu=true;}
   break;
  }
  if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){//neu schreiben
   fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   if($nSta==1){
    $a=explode(';',rtrim($aD[$i])); $sEml=fKalDeCode($a[4+1]); $nFelder=count($kal_NutzerFelder);
    for($i=2+1;$i<$nFelder;$i++) $sNDat.="\n".strtoupper($kal_NutzerFelder[$i]).': '.($i>4+1?$a[$i]:fKalDeCode($a[$i]));
   }
   $Msg='<p class="admErfo">Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
  }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Nutzer.'</i>',KAL_TxDateiRechte).'</p>';
 }elseif($DbO){ //bei SQL
  if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET aktiv="'.$nSta.'" WHERE nr='.$nNum)){
   $Msg='<p class="admErfo">Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
   if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nNum)){
    if($a=$rR->fetch_row()) $sEml=$a[4+1]; $rR->close(); $nFelder=count($kal_NutzerFelder);
    for($i=2+1;$i<$nFelder;$i++) $sNDat.="\n".strtoupper($kal_NutzerFelder[$i]).': '.($i!=3+1?$a[$i]:fKalDeCode($a[3+1]));
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 if(isset($sEml)&&KAL_NutzerAktivMail){ //Aktivierungsmail
  $sWww=fKalWww(); $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxNutzerAktivBtr));
  $sMTx=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxNutzerAktivTxt)));
  require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
 }
}

//Abfrageparameter aufbereiten
$nFelder=count($kal_NutzerFelder); $aF1=array(); $aF2=array(); $aF2=array();
if($sIdn1=(int)(isset($_POST['Idn1'])?$_POST['Idn1']:(isset($_GET['Idn1'])?$_GET['Idn1']:0))){
 $sQ.='&amp;Idn1='.$sIdn1;
 if($sIdn2=(int)(isset($_POST['Idn2'])?$_POST['Idn2']:(isset($_GET['Idn2'])?$_GET['Idn2']:0))) $sQ.='&amp;Idn2='.$sIdn2;
}else $sIdn2='';
if($sUsr1=trim(isset($_POST['Usr1'])?$_POST['Usr1']:(isset($_GET['Usr1'])?$_GET['Usr1']:''))){
 $sQ.='&amp;Usr1='.urlencode($sUsr1);
 if($sUsr2=trim(isset($_POST['Usr2'])?$_POST['Usr2']:(isset($_GET['Usr2'])?$_GET['Usr2']:''))) $sQ.='&amp;Usr2='.urlencode($sUsr2);
}else $sUsr2='';
if($sUsr3=trim(isset($_POST['Usr3'])?$_POST['Usr3']:(isset($_GET['Usr3'])?$_GET['Usr3']:''))) $sQ.='&amp;Usr3='.urlencode($sUsr3);
if($sEml1=trim(isset($_POST['Eml1'])?$_POST['Eml1']:(isset($_GET['Eml1'])?$_GET['Eml1']:''))){
 $sQ.='&amp;Eml1='.urlencode($sEml1);
 if($sEml2=trim(isset($_POST['Eml2'])?$_POST['Eml2']:(isset($_GET['Eml2'])?$_GET['Eml2']:''))) $sQ.='&amp;Eml2='.urlencode($sEml2);
}else $sEml2='';
if($sEml3=trim(isset($_POST['Eml3'])?$_POST['Eml3']:(isset($_GET['Eml3'])?$_GET['Eml3']:''))) $sQ.='&amp;Eml3='.urlencode($sEml3);
for($i=6;$i<$nFelder;$i++){
 if($aF1[$i]=trim(isset($_POST['F1'.$i])?$_POST['F1'.$i]:(isset($_GET['F1'.$i])?$_GET['F1'.$i]:''))){
  $sQ.='&amp;F1'.$i.'='.urlencode($aF1[$i]);
  if($aF2[$i]=trim(isset($_POST['F2'.$i])?$_POST['F2'.$i]:(isset($_GET['F2'.$i])?$_GET['F2'.$i]:''))) $sQ.='&amp;F2'.$i.'='.urlencode($aF2[$i]);
 }else $aF2[$i]='';
 if($aF3[$i]=trim(isset($_POST['F3'.$i])?$_POST['F3'.$i]:(isset($_GET['F3'.$i])?$_GET['F3'.$i]:''))) $sQ.='&amp;F3'.$i.'='.urlencode($aF3[$i]);
}
$sSta1=(int)(isset($_POST['Sta1'])?$_POST['Sta1']:(isset($_GET['Sta1'])?$_GET['Sta1']:0));
$sSta2=(int)(isset($_POST['Sta2'])?$_POST['Sta2']:(isset($_GET['Sta2'])?$_GET['Sta2']:0));
if($sSta1&&$sSta2) $sSta1=$sSta2=0; if($sSta1) $sQ.='&amp;Sta1=1'; if($sSta2) $sQ.='&amp;Sta2=1';

//Daten bereitstellen
$bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
array_splice($kal_NutzerFelder,1,1); $amNutzerFelder=min(ADM_NutzerFelder,count($kal_NutzerFelder)-1); $aTmp=array();
if(!KAL_SQL){ //Textdaten
 if($sQ){ //filtern
  $aTmp=array(); $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); array_shift($aD); $nSaetze=count($aD);
  for($i=0;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aD[$i])); $b=true;
   if($sIdn1){
    if(!$sIdn2){if($sIdn1!=(int)$a[0]) $b=false;} else{if($sIdn1<(int)$a[0]||$sIdn2>(int)$a[0]) $b=false;}
   }
   if($sSta1) if($a[2]!='1') $b=false; if($sSta2) if($a[2]!='0') $b=false;
   if($sUsr1){
    $s=fKalDeCode(str_replace('`,',';',$a[3])); $b1=stristr($s,$sUsr1);
    if(!$sUsr2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sUsr2))) $b=false;}
   }
   if($sUsr3) if(stristr(fKalDeCode(str_replace('`,',';',$a[3]),$sUsr3))) $b=false;
   if($sEml1){
    $s=fKalDeCode($a[5]); $b1=stristr($s,$sEml1);
    if(!$sEml2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sEml2))) $b=false;}
   }
   if($sEml3) if(stristr(fKalDeCode($a[5]),$sEml3)) $b=false;
   if($b) for($j=6;$j<$nFelder;$j++){
    if($t=$aF1[$j]){
     $s=str_replace('`,',';',$a[$j]); $b1=stristr($s,$t);
     if(!$t=$aF2[$j]){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$t))) $b=false;}
    }
    if($t=$aF3[$j]) if(stristr(str_replace('`,',';',$a[$j]),$t)) $b=false;
   }
   if($b) $aTmp[]=$aD[$i]; //gueltig
  }
 }else{$aTmp=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); array_shift($aTmp);}
}elseif($DbO){ $sF=''; //SQL
 if($sIdn1){
  if(!$sIdn2) $sF.=' AND nr="'.$sIdn1.'"'; else $sF.=' AND nr BETWEEN "'.$sIdn1.'" AND "'.$sIdn2.'"';
 }
 if($sSta1) $sF.=' AND aktiv="1"'; elseif($sSta2) $sF.=' AND aktiv="0"';
 if($sUsr1){
  if(!$sUsr2) $sF.=' AND benutzer LIKE "%'.$sUsr1.'%"'; else $sF.=' AND (benutzer LIKE "%'.$sUsr1.'%" OR benutzer LIKE "%'.$sUsr2.'%")';
 }
 if($sUsr3) $sF.=' AND NOT benutzer LIKE "%'.$sUsr3.'%"';
 if($sEml1){
  if(!$sEml2) $sF.=' AND email LIKE "%'.$sEml1.'%"'; else $sF.=' AND (email LIKE "%'.$sEml1.'%" OR email LIKE "%'.$sEml2.'%")';
 }
 if($sEml3) $sF.=' AND NOT email LIKE "%'.$sEml3.'%"';
 for($j=6;$j<$nFelder;$j++){ $k=$j-1;
  if($t=$aF1[$j]){
   if(!$aF2[$j]) $sF.=' AND dat_'.$k.' LIKE "%'.$t.'%"'; else $sF.=' AND (dat_'.$k.' LIKE "%'.$t.'%" OR dat_'.$k.' LIKE "%'.$aF2[$j].'%")';
  }
  if($t=$aF3[$j]) $sF.=' AND NOT dat_'.$k.' LIKE "%'.$t.'%"';
 }
 if($sF) $sF=' WHERE'.substr($sF,4);
 $s=''; for($j=5;$j<=$amNutzerFelder;$j++) $s.=',dat_'.$j;
 if($rR=$DbO->query('SELECT nr,aktiv,benutzer,passwort,email'.$s.' FROM '.KAL_SqlTabN.$sF.' ORDER BY nr')){
  while($a=$rR->fetch_row()){
   $s=$a[0]; for($j=1;$j<=$amNutzerFelder;$j++) $s.=';'.$a[$j]; $aTmp[]=$s;
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
if(!$nStart=(int)(isset($_GET['kal_Start'])?$_GET['kal_Start']:(isset($_POST['kal_Start'])?$_POST['kal_Start']:0))) $nStart=1; $nStop=$nStart+ADM_NutzerLaenge;
reset($aTmp); $nSaetze=count($aTmp); $aD=array(); $k=0;
foreach($aTmp as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];
if(!$Msg){
 if($bNutzer) $Msg='<p class="admMeld">Benutzerliste'.($sQ?' (gefiltert)':'').'</p>';
 else $Msg='<p class="admFehl">Die Benutzerverwaltung ist ohne ein Feld vom Typ <i>Benutzer</i> in der Terminstruktur momentan inaktiv!</p>';
}

//Scriptausgabe
?>
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $Msg?></td>
  <td align="right">
   [ <a href="nutzerExport.php<?php echo ($sQ?'?'.substr($sQ,5):'')?>"><img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" title="exportieren" alt="exportieren">&nbsp;exportieren</a>]
   [ <a href="nutzerDrucken.php<?php echo ($sQ?'?'.substr($sQ,5):'')?>" target="druck" onclick="druWin(this.href);return false;" title="drucken"><img src="<?php echo $sHttp?>grafik/iconDrucken.gif" align="top" width="16" height="16" border="0" title="drucken">&nbsp;drucken</a> ]
   [ <a href="nutzerSuche.php<?php echo ($sQ?'?'.substr($sQ,5):'')?>"><img src="<?php echo $sHttp?>grafik/icon_Lupe.gif" width="12" height="13" border="0" title="suchen" alt="suchen">&nbsp;suchen</a> ]
  </td>
 </tr>
</table>

<?php
$sNavigator=fKalNavigator($nStart,$nSaetze,ADM_NutzerLaenge,$sQ); echo $sNavigator;
?>

<form name="NutzerListe" action="nutzerListe.php<?php if($sQ) echo '?'.substr($sQ,5)?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 $bAendern=file_exists('nutzerAendern.php'); $bKontakt=file_exists('nutzerKontakt.php'); $bLoeschen=file_exists('nutzerLoeschen.php');
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td>&nbsp;</td>'.NL.' <td>&nbsp;</td>'.NL.' <td><b>'.$kal_NutzerFelder[2].'</b></td>'.NL.' <td width="16">&nbsp;</td>';
 for($j=4;$j<=$amNutzerFelder;$j++){if(!$s=$kal_NutzerFelder[$j]) $s='&nbsp;'; echo NL.' <td><b>'.$s.'</b></td>';}
 echo NL.'</tr>';
 if($nStart>1) $sQ.='&amp;kal_Start='.$nStart; $aQ['Start']=$nStart;
 foreach($aD as $a){ //Datenzeilen ausgeben
  $a=explode(';',rtrim($a)); $Id=$a[0]; if(!KAL_SQL) array_splice($a,1,1);
  echo NL.'<tr class="admTabl">';
  echo NL.' <td style="white-space:nowrap;">'.($bLoeschen?'<input class="admCheck" type="checkbox" name="kal_L'.$Id.'" value="1"'.(isset($aId[$Id])&&$aId[$Id]?' checked="checked"':'').' /> ':'').sprintf('%04d',$Id).'</td>';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerAendern.php?kal_Num='.$Id.$sQ.'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  $sSta=$a[1];
  if($sSta=='1') $sSta='0"><img src="'.$sHttp.'grafik/punktGrn.gif" width="12" height="12" border="0" title="freigeschaltet - jetzt sperren">';
  elseif($sSta=='0') $sSta='1"><img src="'.$sHttp.'grafik/punktRot.gif" width="12" height="12" border="0" title="inaktiv - jetzt freischalten">';
  elseif($sSta=='2') $sSta='1"><img src="'.$sHttp.'grafik/punktRtGn.gif" width="12" height="12" border="0" title="best&auml;tigt - jetzt freischalten">';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerListe.php?kal_Start='.$nStart.$sQ.'&amp;kal_Num='.$Id.'&amp;kal_Status='.$sSta.'</a>':substr($sSta,3)).'</td>';
  if($s=$a[2]){if(!KAL_SQL) $s=fKalDeCode($s);}else $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
  echo NL.' <td>'.($bKontakt?'<a href="nutzerKontakt.php?kal_Num='.$Id.$sQ.'"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.' kontaktieren"></a>':'&nbsp;').'</td>';
  if($s=$a[4]){if(!KAL_SQL) $s=fKalDeCode($s);}else $s='&nbsp;'; echo NL.' <td>'.$s.'</td>';
  for($j=5;$j<=$amNutzerFelder;$j++){if(!$s=(isset($a[$j])?$a[$j]:'')) $s='&nbsp;'; echo NL.' <td>'.(KAL_SQL?$s:str_replace('`,',';',$s)).'</td>';}
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="kal_All" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" align="top" border="0" title="markierte Benutzer l&ouml;schen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo $amNutzerFelder+1?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="kalLsch" value="<?php echo $sLschFrg?>" />
<?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="kal_'.$k.'" value="'.$v.'" />'?>

</form>
<?php
echo $sNavigator;

if($bAendern) echo '<p style="text-align:center">[ <a href="nutzerAendern.php?kal_neu=1">neuer Benutzer</a> ]</p>'.NL.NL;

echo fSeitenFuss();

function fKalNavigator($nStart,$nCount,$nListenLaenge,$sQ=''){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge);
 $s ='<td style="width:16px;text-align:center;"><a href="nutzerListe.php?kal_Start=1'.$sQ.'" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?kal_Start='.(($i-1)*$nListenLaenge+1).$sQ.'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?kal_Start='.(max($nPgs-1,0)*$nListenLaenge+1).$sQ.'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:8px;margin-bottom:8px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>