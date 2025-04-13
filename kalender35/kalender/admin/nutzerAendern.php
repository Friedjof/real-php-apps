<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerdaten','','NNl');

array_splice($kal_NutzerFelder,1,1);
if($_SERVER['REQUEST_METHOD']=='POST'){
 $nFelder=count($kal_NutzerFelder);
 $nId=$_POST['kal_Num']; $sQ=$_POST['kal_Qry']; $sZ=''; $sMlDat='NUMMER: '.sprintf('%04d',$nId);
 $s=(int)$_POST['kal_F1']; $sZ.=(KAL_SQL?',aktiv="'.$s.'"':';'.$s); //aktiviert
 $s=strtolower(str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['kal_F2']))))); //Nutzer
 $sZ.=(KAL_SQL?',benutzer="'.$s.'"':';'.fKalEnCode($s)); $sMlDat.="\n".strtoupper($kal_NutzerFelder[2]).': '.$s;
 $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['kal_F3'])))); //Passwort
 $sZ.=(KAL_SQL?',passwort="'.fKalEnCode($s).'"':';'.fKalEnCode($s)); $sMlDat.="\n".strtoupper($kal_NutzerFelder[3]).': '.$s;
 $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['kal_F4'])))); //eMail
 $sZ.=(KAL_SQL?',email="'.$s.'"':';'.fKalEnCode($s)); $sMlDat.="\n".strtoupper($kal_NutzerFelder[4]).': '.$s;
 for($i=5;$i<$nFelder;$i++){
  $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['kal_F'.$i]))));
  $sZ.=(KAL_SQL?',dat_'.$i.'="'.$s.'"':';'.str_replace(';','`,',$s)); $sMlDat.="\n".strtoupper($kal_NutzerFelder[$i]).': '.$s;
 }
 if(!KAL_SQL){ //Textdatei
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); $bOK=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
   $s=rtrim($aD[$i]); $p=strpos($s,';',$p);
   if($sZ!=substr($s,$p)){
    $aD[$i]=substr($s,0,$p).$sZ.NL;
    if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){//neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $Msg='<p class="admErfo">'.KAL_TxNutzerGeaendert.'</p>'; $sEml=trim($_POST['kal_F4']);
    }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Nutzer.'</i>',KAL_TxDateiRechte).'</p>';
   }else $Msg='<p class="admMeld">'.KAL_TxKeineAenderung.'</p>';
   break;
  }
 }elseif($DbO){ //bei SQL
   if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET '.substr($sZ,1).' WHERE nr="'.$nId.'"')){
    if($DbO->affected_rows>0){
     $Msg='<p class="admErfo">'.KAL_TxNutzerGeaendert.'</p>'; $sEml=trim($_POST['kal_F4']);
    }else $Msg='<p class="admMeld">'.KAL_TxKeineAenderung.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 if(isset($sEml)&&KAL_NutzerAktivMail&&($_POST['kal_Aktiv']=='0'||$_POST['kal_Aktiv']=='2')&&$_POST['kal_F1']=='1'){ //Aktivierungsmail
  $sWww=fKalWww(); $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxNutzerAktivBtr));
  $sMTx=str_replace('#D',$sMlDat,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxNutzerAktivTxt)));
  require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
 }
}else{ //GET
 $sQ=substr($_SERVER['QUERY_STRING'],4); $nId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'');
 if(isset($_GET['kal_neu'])&&$_GET['kal_neu']){ //neuen Datensatz einfügen
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer);
   $nNutzFelder=count($kal_NutzerFelder);
   $nId=substr($aD[0],7,12); $p=strpos($nId,';'); $nId=1+substr($nId,0,$p); //Auto-ID-Nr holen
   $s='Nummer_'.$nId.';Session;aktiv'; for($i=2;$i<$nNutzFelder;$i++) $s.=';'.$kal_NutzerFelder[$i]; $aD[0]=$s.NL; //neue ID-Nummer
   $aD[]=$nId.';-;0;'.fKalEnCode('???').';;;';
   if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){//neu schreiben
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Nutzer.'</i>',KAL_TxDateiRechte).'</p>';
  }elseif($DbO){ //SQL
   if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabN.' (session,aktiv,benutzer) VALUES("-","0","???")')){
    if(!$nId=$DbO->insert_id) echo '<p class="admFehl">'.KAL_TxSqlEinfg.' (2)</p>';
   }else echo '<p class="admFehl">'.KAL_TxSqlEinfg.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 }//neu
}//GET

//Scriptausgabe
$nFelder=count($kal_NutzerFelder);
if(!KAL_SQL){ //Textdaten
 $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
 for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
 if(is_array($aD)) array_splice($aD,1,1); else $Msg='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
  $aD=$rR->fetch_row(); $rR->close();
  if(is_array($aD)) array_splice($aD,1,1); else $Msg='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
if(!$Msg) $Msg='<p class="admMeld">'.KAL_TxNutzerAendere.'</p>';
echo $Msg.NL;
?>

<form name="NutzerForm" action="nutzerAendern.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $nId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<input type="hidden" name="kal_Aktiv" value="<?php echo $aD[1]?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td class="admSpa1">ID-Nummer</td><td style="padding-left:5px;"><?php echo $aD[0]?></td></tr>
 <tr class="admTabl">
  <td class="admSpa1">Status</td>
  <td>
   <input class="admRadio" type="radio" name="kal_F1" value="1"<?php if($aD[1]=='1') echo ' checked="checked"'?> /> aktiviert &nbsp; &nbsp;
   <input class="admRadio" type="radio" name="kal_F1" value="0"<?php if(!$aD[1]) echo ' checked="checked"'?> /> deaktiviert &nbsp; &nbsp;
   <input class="admRadio" type="radio" name="kal_F1" value="2"<?php if($aD[1]=='2') echo ' checked="checked"'?> /> bestätigt
  </td>
 </tr>
<?php
 for($i=2;$i<$nFelder;$i++){
  $s=(isset($aD[$i])?$aD[$i]:''); if($i==3||($i==2||$i==4)&&!KAL_SQL) $s=fKalDeCode($s);
  if($i>3){$sStyle=' style="width:100%"'; if(!KAL_SQL) $s=str_replace('`,',';',$s);}else $sStyle=' style="width:170px" maxlength="'.($i==2?25:16).'"';
  if(!$sFld=$kal_NutzerFelder[$i]) $sFld='&nbsp;';
  echo NL.' <tr class="admTabl">
  <td class="admSpa1">'.$sFld.'</td>
  <td><input'.$sStyle.' type="'.($i!=3?'text':'password').'" name="kal_F'.$i.'" value="'.$s.'" />'.($i<4?' 4..'.($i==2?25:16).' Zeichen':'').'</td>'.NL.' </tr>';
 }
?>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Ändern"></p>
</form>
<p class="admSubmit">[ <a href="nutzerListe.php?<?php echo $sQ?>">zur Benutzerliste</a> ]</p>


<?php
echo fSeitenFuss();

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>