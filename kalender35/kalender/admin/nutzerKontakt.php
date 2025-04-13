<?php
include 'hilfsFunktionen.php';

$nTxNr=0; $aTxKz=array(); $aTxBt=array(); $aTxMt=array(); //Alternativtext holen
if(!KAL_SQL){
 $aT=file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $nTxte=count($aT);
 for($i=1;$i<$nTxte;$i++){
  $s=$aT[$i]; $k=(int)substr($s,0,4);
  if(substr($s,5,1)=='a'){
   $a=explode(';',rtrim($s));
   $aTxKz[$k]=str_replace('`,',';',$a[2]); $aTxBt[$k]=str_replace('`,',';',$a[3]); $aTxMt[$k]=str_replace('`,',';',$a[4]);
  }
 }
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' WHERE typ="a" ORDER BY id')){
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
 aKz[0]='Standardtext';
 aBt[0]='".str_replace("'",'´',ADM_NutzerBetreff)."';
 aMt[0]='".str_replace('\n ','\n',ADM_NutzerKontakt)."';";
foreach($aTxKz as $k=>$v){ $sJs.="
 aKz[".$k."]='".$aTxKz[$k]."';
 aBt[".$k."]='".$aTxBt[$k]."';
 aMt[".$k."]='".str_replace('\n ','\n',$aTxMt[$k])."';";
}
$sJs.="\n</script>\n";
asort($aTxKz); reset($aTxKz);

echo fSeitenKopf('Benutzerkontakt',$sJs,'NNl');

if($nId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:(isset($_POST['kal_Num'])?$_POST['kal_Num']:''))){
 if(!KAL_SQL){ //Textdaten
  $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
  if(is_array($aD)){$aD[1]=fKalDeCode($aD[2+1]); $aD[2]=fKalDeCode($aD[4+1]);}
  else $Msg='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,benutzer,email FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
   $aD=$rR->fetch_row(); $rR->close();
   if(!is_array($aD)) $Msg='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
  $sQ=substr($_SERVER['QUERY_STRING'],4); $sTx=ADM_NutzerKontakt; $sBtr=ADM_NutzerBetreff; $bOK=false;
 }else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  $sQ=$_POST['kal_Qry']; $nTxNr=(int)txtVar('ATxNr'); if(!$sBtr=txtVar('Btr')) $sBtr=ADM_NutzerBetreff; $bOK=false;
  if(($sTx=str_replace('  ',' ',str_replace("\r",'',txtVar('Txt'))))&&($sTx!=str_replace('\n ',NL,ADM_NutzerKontakt))){
   require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($aD[2]); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aD[2]);
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sTx;
   if($Mailer->Send()){
    $Msg='<p class="admErfo">Die Nachricht an <i>'.$aD[1].'</i> wurde versandt!</p>'; $bOK=true;
   }else $Msg='<p class="admFehl">Die Nachricht konnte soeben nicht versandt werden!</p>';
  }else $Msg='<p class="admFehl">Bitte geben Sie einen individuellen Text ein!</p>';
 }//POST
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Benutzernummer!</p>';

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Senden Sie eine Nachricht an den Benutzer <i>'.$aD[1].'</i>.</p>';
echo $Msg.NL;
?>

<form name="NutzerForm" action="nutzerKontakt.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $nId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1">ID-Nummer</td>
 <td><?php echo $aD[0]?></td>
</tr><tr class="admTabl">
 <td class="admSpa1">E-Mail-Adresse</td>
 <td><?php echo $aD[2]?></td>
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
<p class="admSubmit">[ <a href="nutzerListe.php?<?php echo $sQ?>">zur Benutzerliste</a> ]</p>

<?php
echo fSeitenFuss();
?>