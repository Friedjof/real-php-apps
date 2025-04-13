<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerdaten ändern','','NNl');

$aFelder=explode(';',UMF_NutzerFelder); $aPflicht=explode(';',UMF_NutzerPflicht); $nFelder=count($aFelder);

if($_SERVER['REQUEST_METHOD']=='POST'){
 $nId=(isset($_POST['nnr'])?$_POST['nnr']:'');
 $sQ=(isset($_POST['qs'])?$_POST['qs']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 $sZ=''; $sNDat="NUMMER: ".sprintf('%05d',$nId); $s=(isset($_POST['f1'])?(int)$_POST['f1']:0);
 $sZ.=(UMF_SQL?',aktiv="'.$s.'"':';'.$s); //aktiviert
 $s=(isset($_POST['f2'])?strtolower(str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f2']))))):''); //Nutzer
 $sZ.=(UMF_SQL?',Benutzer="'.$s.'"':';'.fUmfEnCode($s)); $sNDat.="\n".strtoupper($aFelder[2]).': '.$s;
 $s=(isset($_POST['f3'])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f3'])))):''); //Passwort
 $sZ.=(UMF_SQL?',Passwort="'.fUmfEnCode($s).'"':';'.fUmfEnCode($s)); $sNDat.="\n".strtoupper($aFelder[3]).': '.$s;
 $s=(isset($_POST['f4'])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f4'])))):''); //eMail
 $sZ.=(UMF_SQL?',eMail="'.$s.'"':';'.fUmfEnCode($s)); $sNDat.="\n".strtoupper($aFelder[4]).': '.$s;
 for($i=5;$i<$nFelder;$i++){
  $s=(isset($_POST['f'.$i])?str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['f'.$i])))):'');
  if($aFelder[$i]=='GUELTIG_BIS') if(!empty($s)) $s=fUmfGetDate($s);
  $sZ.=(UMF_SQL?',dat_'.$i.'="'.$s.'"':';'.str_replace(';','`,',$s)); $sNDat.="\n".strtoupper($aFelder[$i]!='GUELTIG_BIS'?$aFelder[$i]:(UMF_TxNutzerFrist>''?UMF_TxNutzerFrist:$aFelder[$i])).': '.$s;
 }
 if(!UMF_SQL){ //Textdatei
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $p=strlen($s); $bOK=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$s=rtrim($aD[$i]); //gefunden
   if($sZ!=substr($s,--$p)){
    $aD[$i]=$nId.$sZ.NL;
    if($f=@fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){//neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $sMeld='<p class="admErfo">'.UMF_TxNutzerGeaendert.'</p>'; $sEml=(isset($_POST['f4'])?trim($_POST['f4']):'');
    }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.UMF_Daten.UMF_Nutzer.'</i>',UMF_TxDateiRechte).'</p>';
   }else $sMeld='<p class="admMeld">Die Benutzerdaten bleiben unverändert.</p>';
   break;
  }
 }elseif($DbO){ //bei SQL
  if($DbO->query('UPDATE IGNORE '.UMF_SqlTabN.' SET '.substr($sZ,1).' WHERE Nummer="'.$nId.'"')){
   if($DbO->affected_rows>0){
    $sMeld='<p class="admErfo">'.UMF_TxNutzerGeaendert.'</p>'; $sEml=(isset($_POST['f2'])?trim($_POST['f4']):'');
   }else $sMeld='<p class="admMeld">Die Benutzerdaten bleiben unverändert.</p>';
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlAendr.'</p>';
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';

 if(isset($sEml)&&UMF_NutzerAktivMail&&isset($_POST['ak'])&&$_POST['ak']=='0'&&isset($_POST['f1'])&&$_POST['f1']=='1'){ //Aktivierungsmail
  include UMF_Pfad.'class.plainmail.php'; $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
  if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
  $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
  $sWww=UMF_Www; if($p=strpos($sWww,'/')) $sWww=substr($sWww,0,$p);
  $Mailer->Subject=str_replace('#',$sWww,UMF_TxNutzerAktivBtr);
  $Mailer->Text=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",UMF_TxNutzerAktivTxt))); $Mailer->Send();
 }
}else{ //GET
 $nId=(isset($_GET['nnr'])?$_GET['nnr']:'');
 $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''); $sQo=str_replace('&','&amp;',substr($sQ,0,max(strpos($sQ,'nnr=')-1,0)));
 if(isset($_GET['neu'])&&$_GET['neu']){ //neuen Datensatz einfügen
  if(!UMF_SQL){ //Textdaten
   $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer);
   $nId=substr($aD[0],7,12); $p=strpos($nId,';'); $nId=1+substr($nId,0,$p); //Auto-ID-Nr holen
   $s='Nummer_'.$nId; $aD[0]=$s.substr(UMF_NutzerFelder,6).NL; $sZ=$nId.'; ;'.fUmfEnCode('???').';;;'; //neue ID-Nummer
   if($p=array_search('GUELTIG_BIS',$aFelder)) if(UMF_NutzerFrist>0){
    $n=5; while($n++<$p) $sZ.=';'; $sZ.=date('Y-m-d',time()+UMF_NutzerFrist*86400);
   }
   if($f=@fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){ //neu schreiben
    $aD[]=$sZ; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   }else $sMeld='<p class="admFehl">'.str_replace('#','<i>'.UMF_Daten.UMF_Nutzer.'</i>',UMF_TxDateiRechte).'</p>';
  }elseif($DbO){ //SQL
   if($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabN.' (aktiv,benutzer) VALUES(" ","???")')){
    if(!$nId=$DbO->insert_id) $sMeld='<p class="admFehl">'.UMF_TxSqlEinfg.'</p>';
   }else $sMeld='<p class="admFehl">'.UMF_TxSqlEinfg.'</p>';
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
 }//neu
}//GET

//Scriptausgabe
$aD=array();
if(!UMF_SQL){ //Textdaten
 $aTmp=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
 for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aD=explode(';',rtrim($aTmp[$i])); break;}
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.$nId.'"')){
  $aD=$rR->fetch_row(); $rR->close();
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
}else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
if(!$sMeld) if(count($aD)>4) $sMeld='<p class="admMeld">'.UMF_TxNutzerAendere.'</p>'; else $sMeld='<p class="admFehl">Keine Benutzerdaten zur Benutzernummer '.$nId.'</p>';

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
  $s=(isset($aD[$i])?$aD[$i]:''); if($i==3||$i<5&&!UMF_SQL) $s=fUmfDeCode($s); $bNutzerFrist=false;
  if($i>3){$sStyle='100%'; if(!UMF_SQL) $s=str_replace('`,',';',$s);}else $sStyle='170px;" maxlength="'.($i==2?25:16).'"';
  if($sFld=$aFelder[$i]){if($sFld=='GUELTIG_BIS'){$sFld=UMF_TxNutzerFrist; $sStyle='9em;'; $bNutzerFrist=true;}} else $sFld='&nbsp;';
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
<?php if(UMF_NutzerUmfragen&&file_exists('nutzerUmfragen.php')) echo '[ <a href="nutzerZuweisung.php?'.$sQo.'">Benutzer &amp; Umfragen</a> ] [ <a href="nutzerUmfragen.php?'.$sQ.'">Umfragezuweisungen</a> ]';?>
</p>


<?php
echo fSeitenFuss();

function fUmfGetDate($s){
 if(strpos($s,'-')) $a=explode('-',$s); elseif(strpos($s,'.')){$a=explode('.',$s); $t=$a[0]; $a[0]=(isset($a[2])?$a[2]:0); $a[2]=$t;} else $a=explode('-',$s);
 $s=sprintf('%04d-%02d-%02d',(isset($a[0])?($a[0]>2000?$a[0]:2000+$a[0]):2000),(isset($a[1])?$a[1]:1),(isset($a[2])?$a[2]:1));
 return $s;
}
?>