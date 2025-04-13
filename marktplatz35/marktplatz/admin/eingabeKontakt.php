<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserenten kontaktieren','','NNl');

$aStru=array(); $sAdr='???'; $sBtr=''; $sTx=''; $aD=array('??'); $bOK=false;
if(defined('MP_Version')){
 if(isset($_GET['mp_Num'])) $nId=$_GET['mp_Num']; else if(isset($_POST['mp_Num'])) $nId=$_POST['mp_Num'];
 if($nSegNo!=0&&($nId)){ //Segment und Inserat gewaehlt
  $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aDF=array(); $aND=array();
  $aZS=array(); $aAW=array(); $aKW=array(); $aSW=array();
  if(!MP_SQL){//Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
    if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
  if($nKontaktPos=array_search('c',$aFT)){
   if(!MP_SQL){ //Textdaten
    $aTmp=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aTmp);
    for($i=1;$i<$nSaetze;$i++){
     $s=rtrim($aTmp[$i]); $p=strpos($s,';'); if($nId==substr($s,0,$p)){$aD=explode(';',$s); array_splice($aD,1,1); break;}
    }
    if(is_array($aD)) $sAdr=fMpDeCode($aD[$nKontaktPos]);
    else $Meld='Kein Inserateeintrag zur Inserat-Nummer '.$nId;
   }elseif($DbO){//SQL
    if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$nId.'"')){
     $aD=$rR->fetch_row(); $rR->close(); array_splice($aD,1,1); $sAdr=$aD[$nKontaktPos];
     if(!is_array($aD)) $Meld='Kein Inserateeintrag zur Inserate-Nummer '.$nId;
    }else $Meld=MP_TxSqlFrage;
   }else $Meld=MP_TxSqlVrbdg;
   if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
    $sQ=$_SERVER['QUERY_STRING'];
    $sBtr=AM_NutzerBetreff; $sTx=AM_NutzerKontakt.NL.NL.MP_TxSegment.': '.$sSegNam.' ('.$sSegNo.')'.NL.MP_TxInserateNr.': '.$nId.NL.$aFN[1].': '.fMpAnzeigeDatum($aD[1]).NL;
   }else{ //POST
    $sQ=$_POST['mp_Qry']; if(!$sBtr=txtVar('Btr')) $sBtr=AM_NutzerBetreff;
    if(($sTx=str_replace('  ',' ',str_replace("\r",'',txtVar('Txt'))))&&($sTx!=str_replace('\n ',NL,AM_NutzerKontakt))){
     require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
     if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
     $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->AddTo($sAdr); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sAdr);
     if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender); $Mailer->Text=$sTx;
     if($Mailer->Send()){
      $Meld='Die Nachricht an <i>'.$sAdr.'</i> wurde versandt!'; $MTyp='Erfo'; $bOK=true;
     }else $Meld='Die Nachricht konnte soeben nicht versandt werden!';
    }else $Meld='Bitte geben Sie einen individuellen Text ein!';
   }//POST
  }else $Meld='Ungültiger Seitenaufruf ohne Kontaktadresse!';
 }else $Meld='Ungültiger Seitenaufruf ohne Segment oder Inseratenummer!';
}else $Meld='Setup-Fehler: Die Datei <i>mpWerte.php</i> im Programmverzeichnis kann nicht gelesen werden!';

//Scriptausgabe
if(!$Meld){$Meld='Senden Sie eine Nachricht an den Inserenten <i>'.$sAdr.'</i>.'; $MTyp='Meld';}
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form name="NutzerForm" action="eingabeKontakt.php?seg=<?php echo $nSegNo?>" method="post">
<input type="hidden" name="mp_Num" value="<?php echo $nId?>" />
<input type="hidden" name="mp_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td>Inserat</td><td style="padding-left:5px;">Nr. <?php echo $aD[0]?> im Segment <?php echo $sSegNam;?> </td></tr>
 <tr class="admTabl"><td>E-Mail-Adresse</td><td style="padding-left:5px;"><?php echo $sAdr?></td></tr>
 <tr class="admTabl"><td>Betreff</td><td style="padding-left:5px;"><input type="text" name="Btr" value="<?php echo $sBtr?>" style="width:99%"/></td></tr>
 <tr class="admTabl"><td class="admSpa1">Nachricht</td><td style="padding-left:5px;"><textarea name="Txt" style="width:99%;height:350px;"><?php echo str_replace('\n ',NL,$sTx)?></textarea></td></tr>
</table>
<?php if(!$bOK){?><p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p><?php }?>
</form>
<p style="margin:12px;padding-left:295px;">[ <a href="detail.php?<?php echo $sQ?>">zurück zum Inserat</a> ]</p>

<?php
echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aDF,$aND,$aZS,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]=substr($aDF[0],14,1);
 $aND=explode(';',rtrim($aStru[8])); $aND[0]=substr($aND[0],14,1);
 $aZS=explode(';',rtrim($aStru[6])); $aZS[0]='';
 $aAW=explode(';',rtrim($aStru[16])); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}
?>