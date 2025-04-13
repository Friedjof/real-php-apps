<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzer kontaktieren','','NNl');

$sBtr=''; $sTx=''; $bOK=false; $aD=array('','','','','','');
if($nId=(isset($_GET['nnr'])?$_GET['nnr']:'').(isset($_POST['nnr'])?$_POST['nnr']:'')){$aD=array();
 if(!UMF_SQL){ //Textdaten
  $aTmp=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',$aTmp[$i]); break;}
  if(count($aD)>3){$aD[1]=fUmfDeCode($aD[2]); $aD[2]=fUmfDeCode($aD[4]);}
  else $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT Nummer,Benutzer,eMail FROM '.UMF_SqlTabN.' WHERE Nummer="'.$nId.'"')){
   $aD=$rR->fetch_row(); $rR->close();
   if(count($aD)<3) $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''); $sQ=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
  $sTx=trim(str_replace('\n ',NL,ADU_NutzerKontakt)); $sBtr=ADU_NutzerBetreff;
 }else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  $sQ=$_POST['qs']; if(!$sBtr=txtVar('Btr')) $sBtr=ADU_NutzerBetreff;
  if(($sTx=str_replace('  ',' ',str_replace("\r",'',txtVar('Txt'))))&&($sTx!=trim(str_replace('\n ',NL,ADU_NutzerKontakt)))){
   include UMF_Pfad.'class.plainmail.php'; $Mailer=new PlainMail(); $Mailer->AddTo($aD[2]); $Mailer->SetReplyTo($aD[2]);
   if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
   $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
   $Mailer->Subject=$sBtr; $Mailer->Text=$sTx;
   if($Mailer->Send()){$sMeld='<p class="admErfo">Die Nachricht an <i>'.$aD[1].'</i> wurde versandt!</p>'; $bOK=true;}
   else $sMeld='<p class="admFehl">Die Nachricht konnte soeben nicht versandt werden!</p>';
  }else $sMeld='<p class="admFehl">Bitte geben Sie einen individuellen Text ein!</p>';
 }//POST
}else $sMeld='<p class="admFehl">Ungültiger Seitenaufruf ohne Benutzernummer!</p>';

//Scriptausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Senden Sie eine Nachricht an den Benutzer <i>'.$aD[1].'</i>.</p>';
echo $sMeld.NL;
?>

<form name="NutzerForm" action="nutzerKontakt.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="nnr" value="<?php echo $nId?>" />
<input type="hidden" name="qs" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td width="100">ID-Nummer</td><td style="padding-left:5px;"><?php echo sprintf('%05d',$aD[0])?></td></tr>
 <tr class="admTabl"><td width="100">E-Mail-Adresse</td><td style="padding-left:5px;"><?php echo $aD[2]?></td></tr>
 <tr class="admTabl"><td width="100">Betreff</td><td style="padding-left:5px;"><input type="text" name="Btr" value="<?php echo $sBtr?>" style="width:98%;"/></td></tr>
 <tr class="admTabl"><td width="100" style="vertical-align:top;padding-top:8px;">Nachricht</td><td style="padding-left:5px;"><textarea name="Txt" style="width:98%;height:30em;"><?php echo $sTx?></textarea></td></tr>
</table>
<?php if(!$bOK){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p><?php }?>
</form>
<p style="margin:12px;text-align:center">[ <a href="nutzerListe.php?<?php echo $sQ?>">zur Benutzerliste</a> ]</p>

<?php echo fSeitenFuss();?>