<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Benutzer ändern','','NNl');

 $nFelder=0; $nId='0'; $aD=array('??','0','','','','','','','','','','','','','','','',''); $sQ='';
 $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nFelder=count($aNF);
 if($_SERVER['REQUEST_METHOD']=='POST'){
  $nId=$_POST['mp_Num']; $sQ=$_POST['mp_Qry']; $sZ=''; $sNDat="NUMMER: ".sprintf('%04d',$nId);
  $s=(int)$_POST['mp_F1']; $sZ.=(MP_SQL?',aktiv="'.$s.'"':';'.$s); //aktiviert
  $s=strtolower(str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['mp_F2']))))); //Nutzer
  $sZ.=(MP_SQL?',benutzer="'.$s.'"':';'.fMpEnCode($s)); $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[2])).': '.$s;
  $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['mp_F3'])))); //Passwort
  $sZ.=(MP_SQL?',passwort="'.fMpEnCode($s).'"':';'.fMpEnCode($s)); $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[3])).': '.$s;
  $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['mp_F4'])))); //eMail
  $sZ.=(MP_SQL?',email="'.$s.'"':';'.fMpEnCode($s)); $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[4])).': '.$s;
  for($i=5;$i<$nFelder;$i++){
   $s=str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['mp_F'.$i]))));
   if($aNF[$i]=='HABEN'&&strlen($s)>0&&$s!=sprintf('%0d',$s)) $s=fMpErzeugeDatum($s);
   if($aNF[$i]=='WARNEN') $s=sprintf('%0d',(int)$s);
   $sZ.=(MP_SQL?',dat_'.$i.'="'.$s.'"':';'.str_replace(';','`,',$s)); $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[$i])).': '.$s;
  }
  if(!MP_SQL){ //Textdatei
   $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); $bOK=false;
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
    $s=rtrim($aD[$i]); $p=strpos($s,';',$p);
    if($sZ!=substr($s,$p)){
     $aD[$i]=$nId.';'.(time()>>6).$sZ.NL;
     if($f=@fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w')){//neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $Meld=MP_TxNutzerGeaendert; $MTyp='Erfo'; $sEml=trim($_POST['mp_F4']);
     }else $Meld=str_replace('#','<i>'.MP_Daten.MP_Nutzer.'</i>',MP_TxDateiRechte);
    }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
    break;
   }
  }else{ //bei SQL
   if($DbO){
    if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET '.substr($sZ,1).' WHERE nr="'.$nId.'"')){
     if($DbO->affected_rows>0){
      $Meld=MP_TxNutzerGeaendert; $MTyp='Erfo'; $sEml=trim($_POST['mp_F4']);
     }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
    }else $Meld=MP_TxSqlAendr;
   }else $Meld=MP_TxSqlVrbdg;
  }
  if(isset($sEml)&&MP_NutzerAktivMail&&($_POST['mp_Aktiv']=='0'||$_POST['mp_Aktiv']=='2')&&$_POST['mp_F1']=='1'){ //Aktivierungsmail
   if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
   $sBtr=str_replace('#A',$sWww,MP_TxNutzerAktivBtr); $sMTx=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",MP_TxNutzerAktivTxt)));
   require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
   if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
   $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
   if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
  }
 }else{ //GET
  $sQ=$_SERVER['QUERY_STRING']; if(isset($_GET['mp_Num'])) $nId=$_GET['mp_Num'];
  if(isset($_GET['mp_neu'])){ //neuen Datensatz einfügen
   if(!MP_SQL){ //Textdaten
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nNutzFelder=count($aNF);
    $nId=substr($aD[0],7,12); $p=strpos($nId,';'); $nId=1+substr($nId,0,$p); //Auto-ID-Nr holen
    $s='Nummer_'.$nId.';Session;aktiv'; for($i=2;$i<$nNutzFelder;$i++) $s.=';'.$aNF[$i]; $aD[0]=$s.NL; //neue Nummer
    $s=$nId.';'.(time()>>6).';0;'.fMpEnCode('???'); for($i=3;$i<$nNutzFelder;$i++) $s.=';'; $aD[]=$s;
    if($f=@fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w')){//neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    }else $Meld=str_replace('#','<i>'.MP_Daten.MP_Nutzer.'</i>',MP_TxDateiRechte);
   }else{ //SQL
    if($DbO){
     if($DbO->query('INSERT IGNORE INTO '.MP_SqlTabN.' (session,aktiv,benutzer) VALUES("'.(time()>>6).'","0","???")')){
      if(!$nId=$DbO->insert_id) $Meld=MP_TxSqlEinfg;
     }else $Meld=MP_TxSqlEinfg;
    }else $Meld=MP_TxSqlVrbdg;
   }//SQL
  }//neu
 }//GET

 //Scriptausgabe
 if(!MP_SQL){ //Textdaten
  $aTmp=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
  if(is_array($aD)) array_splice($aD,1,1); else $Meld='Keine Benutzerdaten zur Benutzernummer '.$nId;
 }else{ //SQL
  if($DbO){
   if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
    $aD=$rR->fetch_row(); $rR->close();
    if(is_array($aD)) array_splice($aD,1,1); else $Meld='Keine Benutzerdaten zur Benutzernummer '.$nId;
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
 }
 if(!$Meld){$Meld='Ändern Sie nun die Benutzerdaten ab.'; $MTyp='Meld';}

 echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form name="NutzerForm" action="nutzerAendern.php" method="post">
<input type="hidden" name="mp_Num" value="<?php echo $nId?>" />
<input type="hidden" name="mp_Qry" value="<?php echo $sQ?>" />
<input type="hidden" name="mp_Aktiv" value="<?php echo $aD[1]?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td>ID-Nummer</td><td style="padding-left:5px;"><?php echo $aD[0]?></td></tr>
 <tr class="admTabl">
  <td>Status</td>
  <td>
   <input class="admRadio" type="radio" name="mp_F1" value="1"<?php if($aD[1]=='1') echo ' checked="checked"'?> /> aktiviert &nbsp; &nbsp;
   <input class="admRadio" type="radio" name="mp_F1" value="0"<?php if(!$aD[1]) echo ' checked="checked"'?> /> deaktiviert &nbsp; &nbsp;
   <input class="admRadio" type="radio" name="mp_F1" value="2"<?php if($aD[1]=='2') echo ' checked="checked"'?> /> bestätigt
  </td>
 </tr>
<?php
 $bKontakt=file_exists('nutzerKontakt.php');
 for($i=2;$i<$nFelder;$i++){
  if(isset($aD[$i])) $s=$aD[$i]; else $s=''; $sHabHilfe='';
  if($i==3||($i==2||$i==4)&&!MP_SQL) $s=fMpDeCode($s);
  if($i>3){$sStyle=''; if(!MP_SQL) $s=str_replace('`,',';',$s);}else $sStyle=' style="width:170px;" maxlength="'.($i==2?25:16).'"';
  if(!$sFld=str_replace('`,',';',$aNF[$i])) $sFld='&nbsp;';
  if($sFld=='HABEN'){
   $sHabHilfe='<div class="admMini">Anzahl an Guthaben-Inseraten <i>oder</i> Endedatum der Eintragsberechtigung im Format '.fMpDatumsFormat().'</div>';
   if(substr($s,4,1)=='-'&&substr($s,7,1)=='-') $s=fMpAnzeigeDatum($s);
  }elseif($sFld=='WARNEN'){
   $sHabHilfe='<div class="admMini">Tag vor Inserateablauf, an dem eine Warnung versandt wird. Leer lassen für kein Versand.</div>';
   if($s<'1') $s='';
  }
  echo NL.' <tr class="admTabl">
  <td>'.$sFld.(($i!=4||!$bKontakt)?'':' <a href="nutzerKontakt.php?'.$sQ.'"><img src="'.MPPFAD.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.' kontaktieren"></a>').'</td>
  <td><input class="admEing"'.$sStyle.' type="text" name="mp_F'.$i.'" value="'.$s.'" />'.($i<4?' 4..'.($i==2?25:16).' Zeichen':'').$sHabHilfe.'</td>'.NL.' </tr>';
 }
?>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="&Auml;ndern"></p>

</form>
<p class="admSubmit">[ <a href="nutzerListe.php?<?php echo substr($sQ,3)?>">zur Benutzerliste</a> ]</p>

<?php echo fSeitenFuss();?>