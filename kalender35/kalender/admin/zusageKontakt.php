<?php
include 'hilfsFunktionen.php';

$nTxNr=0; $aTxKz=array(); $aTxBt=array(); $aTxMt=array(); //Alternativtext holen
if(!KAL_SQL){
 $aT=file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $nTxte=count($aT);
 for($i=1;$i<$nTxte;$i++){
  $s=$aT[$i]; $k=(int)substr($s,0,4);
  if(substr($s,5,1)=='z'){
   $a=explode(';',rtrim($s));
   $aTxKz[$k]=str_replace('`,',';',$a[2]); $aTxBt[$k]=str_replace('`,',';',$a[3]); $aTxMt[$k]=str_replace('`,',';',$a[4]);
  }
 }
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' WHERE typ="z" ORDER BY id')){
  while($a=$rR->fetch_row()){
   $k=(int)$a[0]; $aTxKz[$k]=$a[2]; $aTxBt[$k]=$a[3]; $aTxMt[$k]=str_replace("\n",'\n ',str_replace("\r",'',$a[4]));
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}
$sJs="\n<script type=\"text/javascript\">
 var aKz=new Array(); var aBt=new Array(); var aMt=new Array();
 function fATxNr(n){
  document.getElementById('ABtNr').innerHTML=(n>0?n:'');
  document.getElementById('AMtNr').innerHTML=(n>0?n:'');
  document.getElementById('Btr').value=aBt[n];
  document.getElementById('Txt').value=aMt[n];
 }
</script>\n";

echo fSeitenKopf('Zusagekontakt',$sJs,'ZZl');

$aZ=array(); $aM=array(); $bTermin=false; $sTrm='';
if($sInd=(isset($_GET['kal_Num'])?$_GET['kal_Num']:(isset($_POST['kal_Num'])?$_POST['kal_Num']:''))){
 if(!KAL_SQL){ //Textdaten fuer 1 Zusage
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sInd.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aZ=explode(';',rtrim($aD[$i])); break;}
  if(isset($aZ[8])){$aZ[4]=str_replace('`,',';',$aZ[4]); $aZ[8]=fKalDeCode($aZ[8]); $aM[]=$aZ[8]; $aZ[2]=fKalAnzeigeDatum($aZ[2]); $aZ[5]=fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);}
  else $Msg='<p class="admFehl">Keine Zusagedaten zur Datensatznummer '.$sInd.'</p>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$sInd.'"')){
   $aZ=$rR->fetch_row(); $rR->close();
   if(isset($aZ[8])){$aM[]=$aZ[8]; $aZ[2]=fKalAnzeigeDatum($aZ[2]); $aZ[5]=fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);}
   else $Msg='<p class="admFehl">Keine Zusagedaten zur Datensatznummer '.$sInd.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
}elseif($sTrm=(isset($_GET['kal_Trm'])?$_GET['kal_Trm']:(isset($_POST['kal_Trm'])?$_POST['kal_Trm']:''))){
 $bTermin=true;
 if(!KAL_SQL){ //Textdaten fuer 1 Termin
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sTrm.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++){$sZl=$aD[$i];
   if(substr($sZl,strpos($sZl,';'),$p)==$s){
    $a=explode(';',rtrim($sZl)); $aM[]=fKalDeCode($a[8]); if(count($aZ)<=0) $aZ=$a;
  }}
  if(isset($aZ[8])){$aZ[4]=str_replace('`,',';',$aZ[4]); $aZ[8]=fKalDeCode($aZ[8]);}
  else $Msg='<p class="admFehl">Keine Zusagedaten zum Termin Nummer '.$sTrm.'</p>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTrm.'"')){
   $aZ=$rR->fetch_row(); $aM[]=$aZ[8]; while($a=$rR->fetch_row()) $aM[]=$a[8]; $rR->close();
   if(!isset($aZ[8])) $Msg='<p class="admFehl">Keine Zusagedaten zum Termin Nummer '.$sTrm.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Zusagen- bzw. Terminnummer!</p>';

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $sQ=substr($_SERVER['QUERY_STRING'],4); $sA=fKalURL(); $bOK=false;
 $sBtr=str_replace('#A',substr($sA,strpos($sA,'://')+3),KAL_TxZusageKontBtr);
 $sTx=str_replace('#D',$aZ[2]." '".$aZ[4]."'",KAL_TxZusageKontMTx);
 $sTx=str_replace('#Z',$aZ[5],str_replace('#A',$sA,$sTx));
 if(strpos('#'.$sTx,'{')){
  $aF=explode(';',KAL_ZusageFelder); $nF=count($aF); $nA=array_search('Anrede',$aF);
  if(strpos($sTx,'{r}')) $sTx=str_replace('{r}',(!$nA||$aZ[$nA]!='Herr'?'':'r'),$sTx);
  for($i=0;$i<$nF;$i++) if(strpos('#'.$sTx,'{'.$aF[$i].'}')) $sTx=str_replace('{'.$aF[$i].'}',$aZ[$i],$sTx);
 }
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sQ=$_POST['kal_Qry']; $nTxNr=(int)txtVar('ATxNr'); if(!$sBtr=txtVar('Btr')) $sBtr=KAL_TxZusageKontBtr; $bOK=false; $nOK=0; $nErr=0;
 if(($sTx=trim(str_replace('  ',' ',str_replace("\r",'',txtVar('Txt')))))&&($sTx!=trim(str_replace('\n ',NL,KAL_TxZusageKontMTx)))){
  require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->SetFrom($s,$t);
  $Mailer->Subject=$sBtr; $Mailer->Text=$sTx;
  foreach($aM as $sTo){
   $Mailer->AddTo($sTo); $Mailer->SetReplyTo($sTo);
   if($Mailer->Send()) $nOK++; else $nErr++; $Mailer->ClearTo();
  }
  if($nOK) $Msg='<p class="admErfo">Die Nachricht an <i>'.$nOK.'</i> Zusagende wurde versandt!</p>'; $bOK=true;
  if($nErr) $Msg.='<p class="admFehl">Die Nachricht an <i>'.$nErr.'</i> Zusagende konnte nicht versandt werden!</p>';
 }else $Msg='<p class="admFehl">Bitte geben Sie einen individuellen Text ein!</p>';
}//POST

//Scriptausgabe
$sTo=(isset($aM[0])?$aM[0]:'???').(isset($aM[1])?', '.$aM[1]:'').(isset($aM[2])?', '.$aM[2]:'').(isset($aM[3])?' ...':'');
if(!$Msg) $Msg='<p class="admMeld">Senden Sie eine Nachricht an <i>'.$sTo.'</i>.</p>';
echo $Msg.NL;

$sA=fKalURL(); $sA=substr($sA,strpos($sA,'://')+3); $aF=explode(';',KAL_ZusageFelder); $nF=count($aF); $nA=array_search('Anrede',$aF);
$sD=$aZ[2].' ´'.$aZ[4].'´'; $sZ=$aZ[5];
$sT=KAL_TxZusageKontMTx;
if(strpos('#'.$sT,'{')){ // Platzhalter
 if(strpos($sT,'{r}')) $sT=str_replace('{r}',(!$nA||$aZ[$nA]!='Herr'?'':'r'),$sT);
 for($i=0;$i<$nF;$i++) if(strpos('#'.$sT,'{'.$aF[$i].'}')) $sT=str_replace('{'.$aF[$i].'}',$aZ[$i],$sT);
}
$sJs="\n<script type=\"text/javascript\">
 aKz[0]='Standardtext';
 aBt[0]='".str_replace('#A',$sA,str_replace("'",'´',KAL_TxZusageKontBtr))."';
 aMt[0]='".str_replace('#A',$sA,str_replace('#Z',$sZ,str_replace('#D',$sD,str_replace('\n ','\n',$sT))))."';";
foreach($aTxKz as $k=>$v){
 if(strpos('#'.$aTxMt[$k],'{')){ // Platzhalter
  $sT=$aTxMt[$k]; if(strpos($sT,'{r}')) $sT=str_replace('{r}',(!$nA||$aZ[$nA]!='Herr'?'':'r'),$sT);
  for($i=0;$i<$nF;$i++) if(strpos('#'.$sT,'{'.$aF[$i].'}')) $sT=str_replace('{'.$aF[$i].'}',$aZ[$i],$sT);
  $aTxMt[$k]=$sT;
 }
 $sJs.="
 aKz[".$k."]='".$aTxKz[$k]."';
 aBt[".$k."]='".str_replace('#A',$sA,$aTxBt[$k])."';
 aMt[".$k."]='".str_replace('#A',$sA,str_replace('#Z',$sZ,str_replace('#D',$sD,str_replace('\n ','\n',$aTxMt[$k]))))."';";
}
$sJs.="\n</script>\n"; echo $sJs;
asort($aTxKz); reset($aTxKz);
?>

<form name="ZusageForm" action="zusageKontakt.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $sInd?>" />
<input type="hidden" name="kal_Trm" value="<?php echo $sTrm?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1"><?php echo ($bTermin?'Termin':'Zusage')?>-Nr.</td>
 <td><?php echo sprintf('%0'.KAL_NummerStellen.'d',($bTermin?$aZ[1]:$aZ[0])).' <span class="admMini">(betrifft Termin '.$aZ[2].' '.substr($aZ[4],0,90).')</span>'?></td>
</tr><tr class="admTabl">
 <td class="admSpa1">E-Mail-Adresse</td>
 <td><?php echo $sTo?></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Textvariante</td>
 <td><select name="ATxNr" size="1" onchange="fATxNr(this.value)"><option value="0">Standardtext</option><?php foreach($aTxKz as $k=>$v) echo '<option value="'.$k.($nTxNr!=$k?'':'" selected="selected').'">'.sprintf('%04d: ',$k).$v.'</option>'?></select> <span class="admMini">(Weitere Textvarianten können unter <i>Admin-Einstellungen</i> angelegt werden.)</span></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Betreff <span id="ABtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td><input name="Btr" id="Btr" type="text" style="width:100%" value="<?php echo $sBtr?>" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Nachricht <span id="AMtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td><textarea name="Txt" id="Txt" cols="80" rows="20" style="height:300px"><?php echo str_replace('\n ',NL,$sTx)?></textarea></td>
</tr>
</table>
<?php if(!$bOK){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p><?php }?>
</form>
<p class="admSubmit">[ <a href="zusageListe.php?<?php echo $sQ?>">zur Zusagenliste</a> ]</p>

<?php
echo fSeitenFuss();

function fKalURL(){
 $s='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 if(isset($_SERVER['HTTP_HOST'])) $s.=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s.=$_SERVER['SERVER_NAME']; else $s.='localhost';
 return $s;
}
?>