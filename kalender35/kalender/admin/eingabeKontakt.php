<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerkontakt','','NNl');

if($nId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:(isset($_POST['kal_Num'])?$_POST['kal_Num']:''))){
 if($nKontaktPos=array_search('c',$kal_FeldType)){
  if(!KAL_SQL){ //Textdaten
   $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aTmp);
   for($i=1;$i<$nSaetze;$i++){
    $s=rtrim($aTmp[$i]); $p=strpos($s,';'); if($nId==substr($s,0,$p)){$aD=explode(';',$s); break;}
   }
   if(is_array($aD)){array_splice($aD,1,1); $sAdr=fKalDeCode($aD[$nKontaktPos]);}
   else $Msg='<p class="admFehl">Kein Termineintrag zur Termin-Nummer '.$nId.'</p>';
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$nId.'"')){
    $aD=$rR->fetch_row(); $rR->close(); array_splice($aD,1,1); $sAdr=$aD[$nKontaktPos];
    if(!is_array($aD)) $Msg='<p class="admFehl">Kein Termineintrag zur Termin-Nummer '.$nId.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
  if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
   $sQ=$_SERVER['QUERY_STRING']; $bOK=false;
   $sBtr=ADM_NutzerBetreff; $sTx=ADM_NutzerKontakt.NL.NL.'Termin_Nummer: '.$nId.NL.$kal_FeldName[1].': '.fKalAnzeigeDatum($aD[1]).NL;
  }else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
   $sQ=$_POST['kal_Qry']; if(!$sBtr=txtVar('Btr')) $sBtr=ADM_NutzerBetreff; $bOK=false;
   if(($sTx=str_replace('  ',' ',str_replace("\r",'',txtVar('Txt'))))&&($sTx!=str_replace('\n ',NL,ADM_NutzerKontakt))){
    require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->AddTo($sAdr); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sAdr);
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sTx;
    if($Mailer->Send()){
     $Msg='<p class="admErfo">Die Nachricht an <i>'.$sAdr.'</i> wurde versandt!</p>'; $bOK=true;
    }else $Msg='<p class="admFehl">Die Nachricht konnte soeben nicht versandt werden!</p>';
   }else $Msg='<p class="admFehl">Bitte geben Sie einen individuellen Text ein!</p>';
  }//POST
 }else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Kontaktadresse!</p>';
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Terminnummer!</p>';

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Senden Sie eine Nachricht an den Autor <i>'.$sAdr.'</i>.</p>';
echo $Msg.NL;
?>

<form name="NutzerForm" action="eingabeKontakt.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $nId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1">Termin-Nummer</td><td style="padding-left:5px;"><?php echo $aD[0]?></td>
</tr><tr class="admTabl">
 <td class="admSpa1">E-Mail-Adresse</td><td style="padding-left:5px;"><?php echo $sAdr?></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Betreff</td><td style="padding-left:5px;"><input type="text" name="Btr" value="<?php echo $sBtr?>" style="width:100%;"/></td>
</tr><tr class="admTabl">
 <td class="admSpa1">Nachricht</td><td style="padding-left:5px;"><textarea name="Txt" cols="80" rows="20" style="height:25em"><?php echo str_replace('\n ',NL,$sTx)?></textarea></td>
</tr>
</table>
<?php if(!$bOK){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p><?php }?>
</form>
<p class="admSubmit">[ <a href="detail.php?<?php echo $sQ?>">zurück zum Termin</a> ]</p>

<?php
echo fSeitenFuss();
?>