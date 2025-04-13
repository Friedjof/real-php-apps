<?php
function fKalSeite(){ //Selbst-Freischaltung
 global $kal_NutzerFelder;

 $bOK=false; $sAkt='';
 $C=KAL_TxAktivFehl; $Et=KAL_TxAktivFehl; $Es='Fehl'; //ungültig

 //SQL-Verbindung öffnen
 $bSQLOpen=false;
 if(KAL_SQL){
  if($DbC=@mysql_connect(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass)){
   if(@mysql_select_db(KAL_SqlDaBa,$DbC)) $bSQLOpen=true; else $SqE=KAL_TxSqlDaBnk;
  }else $SqE=KAL_TxSqlVrbdg;
 }

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET prüfen
  $sAkt=isset($_GET['kal_Aktion'])?$_GET['kal_Aktion']:'';
  if(substr($sAkt,0,2)=='ok'){ //Nutzerfreischaltung
   $sId=substr($sAkt,6); $sCod=substr($sAkt,2,4); $C=KAL_LoginNeu;
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     $s=substr($aD[$i],$p);
     if(substr($s,0,4)==$sCod){
      $Et=KAL_TxAktivieren; $Es='Meld'; $bOK=true;
      $s=substr($s,7); $C=fKalDeCode(substr($s,0,strpos($s,';'))).': '.$C;
     }
     break;
   }}elseif($bSQLOpen){ //SQL
    if($rR=mysql_query('SELECT nr,session,benutzer FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'"')){
     if($a=mysql_fetch_row($rR)) if($a[1]==$sCod){
      $Et=KAL_TxAktivieren; $Es='Meld'; $bOK=true; $C=$a[2].': '.$C;
     }
     mysql_free_result($rR);
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE; //SQL
  }elseif(substr($sAkt,0,2)=='on'){ //Adressenfreischaltung
   $sCod=0; for($i=2;$i<13;$i++) $sCod+=substr($sAkt,$i,1); $C=KAL_TxEMailFrei;
   if($sCod==substr($sAkt,13)){
    $sCod=substr($sAkt,2,13).';';
    if(!KAL_SQL){ //Textdateien
     if(strpos(implode('',file(KAL_Pfad.KAL_Daten.KAL_MailAdr)),"\n".$sCod)>0){
      $Et=KAL_TxEMailFrei; $Es='Meld'; $bOK=true;
    }}elseif($bSQLOpen){ //SQL
     if($rR=mysql_query('SELECT id FROM '.KAL_SqlTabM.' WHERE email LIKE "'.$sCod.'%"')){
      if(mysql_num_rows($rR)>0){$Et=KAL_TxEMailFrei; $Es='Meld'; $bOK=true;}
      mysql_free_result($rR);
     }else $Et=KAL_TxSqlFrage;
    }else $Et=$SqE;
  }}elseif(substr($sAkt,0,2)=='of'){//Adressenabmeldung


 }}else{ //POST freischalten
  $sAkt=$_POST['kal_Aktion'];
  if(substr($sAkt,0,2)=='ok'){ //Nutzerfreischaltung
   $sId=substr($sAkt,6); $sCod=substr($sAkt,2,4); $sNeuSta=(KAL_FreischaltAdmin?'2':'1');
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr(substr($aD[$i],$p),0,5)==$sCod.';'){
      $s=$sId.';-;'.$sNeuSta.substr(substr(rtrim($aD[$i]),$p),6); $aD[$i]=$s."\n"; $C=KAL_TxAktiv;
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Et=KAL_TxAktiviert; $Es='Erfo';
       $aD=explode(';',$s); for($j=3;$j<6;$j++) $aD[$j]=fKalDeCode($aD[$j]);
      }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $bOK=true;}
     }break;
   }}elseif($bSQLOpen){ //SQL
    if($rR=mysql_query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'"')){
     $i=mysql_num_rows($rR); $aD=mysql_fetch_row($rR); mysql_free_result($rR);
     if($i==1&&$aD[1]==$sCod){
      $C=KAL_TxAktiv;
      if(mysql_query('UPDATE '.KAL_SqlTabN.' SET session="-",aktiv="'.$sNeuSta.'" WHERE nr="'.$sId.'"')){
       $Et=KAL_TxAktiviert; $Es='Erfo'; $aD[4]=fKalDeCode($aD[4]);
      }else{$Et=KAL_TxSqlAendr; $bOK=true;}
    }}else $Et=KAL_TxSqlFrage;
   }
   if($Es=='Erfo'&&KAL_NutzerNeuAdmMail&&KAL_FreischaltAdmin){
    $sMlTx=''; $nFelder=count($kal_NutzerFelder);
    for($j=3;$j<$nFelder;$j++) $sMlTx.="\n".strtoupper($kal_NutzerFelder[$j]).': '.$aD[$j]; $sWww=fKalWww();
    require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(KAL_Empfaenger);
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',sprintf('%04d',$sId),str_replace('#N',sprintf('%04d',$sId),KAL_TxNutzNeuAdmBtr));
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
    $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",KAL_TxNutzNeuAdmTxt)));
    $Mailer->Send();
   }
  }elseif(substr($sAkt,0,2)=='on'){ //Adressenfreischaltung
   $sCod=0; for($i=2;$i<13;$i++) $sCod+=substr($sAkt,$i,1); $C=KAL_TxEMailFrei;
   if($sCod==substr($sAkt,13)){
    $sCod=substr($sAkt,2,13).';';
    if(!KAL_SQL){ //Textdateien
     $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $aD[0]="#eMail\n"; $nSaetze=count($aD);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,14)==$sCod){
      $aD[$i]=rtrim(substr($aD[$i],14))."\n"; $aD=array_unique($aD);
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_MailAdr,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Et=KAL_TxEMailErfo; $Es='Erfo';
      }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $bOK=true;}
      break;
    }}elseif($bSQLOpen){ //SQL
     if($rR=mysql_query('SELECT id,email FROM '.KAL_SqlTabM.' WHERE email LIKE "'.$sCod.'%"')){
      $i=mysql_num_rows($rR); $a=mysql_fetch_row($rR); mysql_free_result($rR);
      if($i>0&&($sEml=substr($a[1],14))){
       mysql_query('DELETE FROM '.KAL_SqlTabM.' WHERE email="'.$sEml.'"');
       if(mysql_query('UPDATE '.KAL_SqlTabM.' SET email="'.$sEml.'" WHERE id='.$a[0])){
        $Et=KAL_TxEMailErfo; $Es='Erfo';
       }else{$Et=KAL_TxSqlEinfg; $bOK=true;}
     }}else $Et=KAL_TxSqlFrage;
    }else $Et=$SqE;
  }}elseif(substr($sAkt,0,2)=='of'){//Adressenabmeldung

  }
 }//POST

 //Formular- und Tabellenanfang
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">
 <input type="hidden" name="kal_Aktion" value="'.$sAkt.'" />
 <div class="kalTabl">
  <div class="kalTbZl1"><div class="kalTbSp2" style="padding:8px;text-align:center;">'.fKalTx($C).'</div></div>';
 $X.="\n".' </div>';
 if($bOK) $X.="\n".' <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'" /></div>'; else $X.='&nbsp;';
 $X.="\n".' </form>'."\n";

 return $X;
}
?>