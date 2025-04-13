<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerdaten ändern','','NNl');

$aFelder=explode(';',FRA_NutzerFelder); $aPflicht=explode(';',FRA_NutzerPflicht); $nFelder=count($aFelder);

if($_SERVER['REQUEST_METHOD']=='POST'){
 $nId=(isset($_POST['nnr'])?$_POST['nnr']:'');
 $sQ=(isset($_POST['qs'])?$_POST['qs']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 $sZ=''; $sNDat="NUMMER: ".sprintf('%05d',$nId); $s=(isset($_POST['f1'])?(int)$_POST['f1']:0);
 $sZ.=(FRA_SQL?',aktiv="'.$s.'"':';'.$s); //aktiviert
 $s=(isset($_POST['f2'])?strtolower(str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f2']))))):''); //Nutzer
 $sZ.=(FRA_SQL?',Benutzer="'.$s.'"':';'.fFraEnCode($s)); $sNDat.="\n".strtoupper($aFelder[2]).': '.$s;
 $s=(isset($_POST['f3'])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f3'])))):''); //Passwort
 $sZ.=(FRA_SQL?',Passwort="'.fFraEnCode($s).'"':';'.fFraEnCode($s)); $sNDat.="\n".strtoupper($aFelder[3]).': '.$s;
 $s=(isset($_POST['f4'])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f4'])))):''); //eMail
 $sZ.=(FRA_SQL?',eMail="'.$s.'"':';'.fFraEnCode($s)); $sNDat.="\n".strtoupper($aFelder[4]).': '.$s;
 for($i=5;$i<$nFelder;$i++){
  $s=(isset($_POST['f'.$i])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f'.$i])))):'');
  if($aFelder[$i]=='GUELTIG_BIS') if(!empty($s)) $s=fraGetDate($s);
  $sZ.=(FRA_SQL?',dat_'.$i.'="'.$s.'"':';'.str_replace(';','`,',$s)); $sNDat.="\n".strtoupper($aFelder[$i]!='GUELTIG_BIS'?$aFelder[$i]:(FRA_TxNutzerFrist>''?FRA_TxNutzerFrist:$aFelder[$i])).': '.$s;
 }
 if(!FRA_SQL){ //Textdatei
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); $bOK=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$s=rtrim($aD[$i]); //gefunden
   if($sZ!=substr($s,--$p)){
    $aD[$i]=$nId.$sZ.NL;
    if($f=@fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){//neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld='<p class="admErfo">'.FRA_TxNutzerGeaendert.'</p>'; $sEml=(isset($_POST['f4'])?trim($_POST['f4']):'');
    }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.FRA_Daten.FRA_Nutzer.'</i>',FRA_TxDateiRechte).'</p>';
   }else $sMeld='<p class="admMeld">Die Benutzerdaten bleiben unverändert.</p>';
   break;
  }
 }elseif($DbO){ //bei SQL
  if($DbO->query('UPDATE IGNORE '.FRA_SqlTabN.' SET '.substr($sZ,1).' WHERE Nummer="'.$nId.'"')){
   if($DbO->affected_rows>0){
    $sMeld='<p class="admErfo">'.FRA_TxNutzerGeaendert.'</p>'; $sEml=(isset($_POST['f2'])?trim($_POST['f4']):'');
   }else $sMeld='<p class="admMeld">Die Benutzerdaten bleiben unverändert.</p>';
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlAendr.'</p>';
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';

 if(isset($sEml)&&FRA_NutzerAktivMail&&isset($_POST['ak'])&&$_POST['ak']=='0'&&isset($_POST['f1'])&&$_POST['f1']=='1'){ //Aktivierungsmail
  include FRA_Pfad.'class.plainmail.php'; $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
  if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
  $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
  $sWww=FRA_Www; if($p=strpos($sWww,'/')) $sWww=substr($sWww,0,$p);
  $Mailer->Subject=str_replace('#',$sWww,FRA_TxNutzerAktivBtr);
  $Mailer->Text=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",FRA_TxNutzerAktivTxt))); $Mailer->Send();
 }
}else{ //GET
 $nId=(isset($_GET['nnr'])?$_GET['nnr']:'');
 $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 if(isset($_GET['neu'])&&$_GET['neu']){ //neuen Datensatz einfügen
  if(!FRA_SQL){ //Textdaten
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer);
   $nId=substr($aD[0],7,12); $p=strpos($nId,';'); $nId=1+substr($nId,0,$p); //Auto-ID-Nr holen
   $s='Nummer_'.$nId; $aD[0]=$s.substr(FRA_NutzerFelder,6).NL; $sZ=$nId.'; ;'.fFraEnCode('???').';;;'; //neue ID-Nummer
   if($p=array_search('GUELTIG_BIS',$aFelder)) if(FRA_NutzerFrist>0){
    $n=5; while($n++<$p) $sZ.=';'; $sZ.=date('Y-m-d',time()+FRA_NutzerFrist*86400);
   }
   if($f=@fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){ //neu schreiben
    $aD[]=$sZ; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.FRA_Daten.FRA_Nutzer.'</i>',FRA_TxDateiRechte).'</p>';
  }elseif($DbO){ //SQL
   if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabN.' (aktiv,benutzer) VALUES(" ","???")')){
    if(!$nId=$DbO->insert_id) $sMeld='<p class="admFehl">'.FRA_TxSqlEinfg.'</p>';
   }else $sMeld='<p class="admFehl">'.FRA_TxSqlEinfg.'</p>';
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
 }//neu
}//GET

//Scriptausgabe
$aD=array();
if(!FRA_SQL){ //Textdaten
 $aTmp=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
 for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$nId.'"')){
  $aD=$rR->fetch_row(); $rR->close();
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
if(!$sMeld) if(count($aD)>4) $sMeld='<p class="admMeld">'.FRA_TxNutzerAendere.'</p>'; else $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';

//Scriptausgabe
echo $sMeld.NL;
?>

<form name="NutzerForm" action="nutzerAendern.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="nnr" value="<?php echo $nId?>" />
<input type="hidden" name="qs" value="<?php echo $sQ?>" />
<input type="hidden" name="ak" value="<?php echo $aD[1]?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl"><td width="128">Nummer</td><td style="padding-left:5px;"><?php echo sprintf('%05d',$aD[0])?></td></tr>
 <tr class="admTabl">
  <td width="128">Status</td>
  <td>
   <input class="admRadio" type="radio" name="f1" value="1"<?php if($aD[1]) echo ' checked="checked"'?> /> aktiviert &nbsp; &nbsp;
   <input class="admRadio" type="radio" name="f1" value="0"<?php if(!$aD[1]) echo ' checked="checked"'?> /> deaktiviert
  </td>
 </tr>
<?php
 for($i=2;$i<$nFelder;$i++){
  $s=(isset($aD[$i])?$aD[$i]:''); if($i==3||$i<5&&!FRA_SQL) $s=fFraDeCode($s); $bNutzerFrist=false;
  if($i>3){$sStyle='100%'; if(!FRA_SQL) $s=str_replace('`,',';',$s);}else $sStyle='170px;" maxlength="'.($i==2?25:16).'"';
  if($sFld=$aFelder[$i]){if($sFld=='GUELTIG_BIS'){$sFld=FRA_TxNutzerFrist; $sStyle='9em;'; $bNutzerFrist=true;}} else $sFld='&nbsp;';
  echo NL.' <tr class="admTabl">
  <td width="128">'.$sFld.'</td>
  <td><input class="admEing" style="width:'.$sStyle.'" type="'.($i!=3?'text':'password').'" name="f'.$i.'" value="'.$s.'" />'.($i<4?' 4..'.($i==2?25:16).' Zeichen':(!$bNutzerFrist?'':'Format: JJJJ-MM-TT')).'</td>'.NL.' </tr>';
 }
?>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="&Auml;ndern"></p>
</form>
<p style="text-align:center">
[ <a href="nutzerListe.php?<?php echo $sQo?>">zur Benutzerliste</a> ]
<?php if(FRA_NutzerTests&&file_exists('nutzerTests.php')) echo '[ <a href="nutzerZuweisung.php?'.$sQo.'">Benutzer und Tests</a> ] [ <a href="nutzerTests.php?'.$sQ.'">Testzuweisungen</a> ]';?>
</p>


<?php
echo fSeitenFuss();

function fraGetDate($s){
 if(strpos($s,'-')) $a=explode('-',$s); elseif(strpos($s,'.')){$a=explode('.',$s); $t=$a[0]; $a[0]=(isset($a[2])?$a[2]:0); $a[2]=$t;} else $a=explode('-',$s);
 $s=sprintf('%04d-%02d-%02d',(isset($a[0])?($a[0]>2000?$a[0]:2000+$a[0]):2000),(isset($a[1])?$a[1]:1),(isset($a[2])?$a[2]:1));
 return $s;
}
?>