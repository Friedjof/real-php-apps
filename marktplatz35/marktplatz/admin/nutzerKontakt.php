<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Benutzer kontaktieren','','NNl');

 $nFelder=0; $nId='0'; $aD=array('??','0','','','','','','','','','','','','','','','',''); $sQ=''; $sTx=''; $sBtr=''; $bOK=false;
 if(isset($_GET['mp_Num'])) $nId=$_GET['mp_Num']; else if(isset($_POST['mp_Num'])) $nId=$_POST['mp_Num'];
 if($nId){
  if(!MP_SQL){ //Textdaten
   $aTmp=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
   if(is_array($aD)){$aD[1]=fMpDeCode($aD[2+1]); $aD[2]=fMpDeCode($aD[4+1]);}
   else $Meld='Keine Benutzerdaten zur Benutzernummer '.$nId.'!';
  }else{ //SQL
   if($DbO){
    if($rR=$DbO->query('SELECT nr,benutzer,email FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
     $aD=$rR->fetch_row(); $rR->close();
     if(!is_array($aD)) $Meld='Keine Benutzerdaten zur Benutzernummer '.$nId.'.';
    }else $Meld=MP_TxSqlFrage;
   }else $Meld=MP_TxSqlVrbdg;
  }
  if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
   $sQ=$_SERVER['QUERY_STRING']; $sTx=AM_NutzerKontakt; $sBtr=AM_NutzerBetreff;
  }else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
   $sQ=$_POST['mp_Qry']; if(!$sBtr=txtVar('Btr')) $sBtr=AM_NutzerBetreff;
   if(($sTx=str_replace('  ',' ',str_replace("\r",'',txtVar('Txt'))))&&($sTx!=str_replace('\n ',NL,AM_NutzerKontakt))){
    require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
    if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
    $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->AddTo($aD[2]); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aD[2]);
    if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender); $Mailer->Text=$sTx;
    if($Mailer->Send()){
     $Meld='Die Nachricht an <i>'.$aD[1].'</i> wurde versandt!'; $MTyp='Erfo'; $bOK=true;
    }else $Meld='Die Nachricht konnte soeben nicht versandt werden!';
   }else $Meld='Bitte geben Sie einen individuellen Text ein!';
  }//POST
 }else $Meld='Ungültiger Seitenaufruf ohne Benutzernummer!';

 //Scriptausgabe
 if(!$Meld){$Meld='Senden Sie eine Nachricht an den Benutzer <i>'.$aD[1].'</i>.'; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form name="NutzerForm" action="nutzerKontakt.php" method="post">
<input type="hidden" name="mp_Num" value="<?php echo $nId?>" />
<input type="hidden" name="mp_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td>ID-Nummer</td><td style="padding-left:5px;"><?php echo sprintf('%04d',$aD[0])?></td></tr>
 <tr class="admTabl"><td style="white-space:nowrap;">E-Mail-Adresse</td><td style="padding-left:5px;"><?php echo $aD[2]?></td></tr>
 <tr class="admTabl"><td>Betreff</td><td style="padding-left:5px;"><input type="text" name="Btr" value="<?php echo $sBtr?>" style="width:620px;"/></td></tr>
 <tr class="admTabl"><td class="admSpa1">Nachricht</td><td style="padding-left:5px;"><textarea name="Txt" style="width:620px;height:350px;"><?php echo str_replace('\n ',NL,$sTx)?></textarea></td></tr>
</table>
<?php if(!$bOK){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p><?php }?>
</form>
<p class="admSubmit">[ <a href="nutzerListe.php?<?php echo substr($sQ,3)?>">zur Benutzerliste</a> ]</p>

<?php echo fSeitenFuss();?>