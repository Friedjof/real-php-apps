<?php
function fMpSeite(){ //Selbst-Freischaltung
 $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNutzerFelder=count($aNF);
 $bOK=false; $sAkt=''; $C=MP_TxAktivFehl; $Meld=MP_TxAktivFehl; $MTyp='Fehl'; //ungueltig

 //SQL-Verbindung oeffnen
 $DbO=NULL;
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET pruefen
  $sAkt=isset($_GET['mp_Aktion'])?fMpRq1($_GET['mp_Aktion']):'';
  if(substr($sAkt,0,2)=='ok'){ //Nutzerfreischaltung
   $sId=substr($sAkt,6); $sCod=substr($sAkt,2,4); $C=MP_TxLoginNeu;
   if(!MP_SQL){ //Textdateien
    if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr(substr($aD[$i],$p),0,4)==$sCod){$Meld=MP_TxAktivieren; $MTyp='Meld'; $bOK=true;}
     break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'"')){
     if($a=$rR->fetch_row()) if($a[1]==$sCod){$Meld=MP_TxAktivieren; $MTyp='Meld'; $bOK=true;}
     $rR->close();
    }else $Meld=MP_TxSqlFrage;
   }else $Meld=$SqE; //SQL
  }elseif(substr($sAkt,0,2)=='on'){ //Adressenfreischaltung
   $sCod=0; for($i=2;$i<13;$i++) $sCod+=substr($sAkt,$i,1); $C=MP_TxEMailFrei;
   if($sCod==substr($sAkt,13)){
    $sCod=substr($sAkt,2,13).';';
    if(!MP_SQL){ //Textdateien
     if(strpos(implode('',file(MP_Pfad.MP_Daten.MP_MailAdr)),"\n".$sCod)>0){
      $Meld=MP_TxEMailFrei; $MTyp='Meld'; $bOK=true;
    }}elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT nr FROM '.MP_SqlTabM.' WHERE email LIKE "'.$sCod.'%"')){
      if($rR->num_rows>0){$Meld=MP_TxEMailFrei; $MTyp='Meld'; $bOK=true;}
      $rR->close();
     }else $Meld=MP_TxSqlFrage;
    }else $Meld=$SqE;
  }}elseif(substr($sAkt,0,2)=='of'){//Adressenabmeldung


 }}else{ //POST freischalten
  $sAkt=fMpRq1($_POST['mp_Aktion']);
  if(substr($sAkt,0,2)=='ok'){ //Nutzerfreischaltung
   $sId=substr($sAkt,6); $sCod=substr($sAkt,2,4); $sNeuSta=(MP_FreischaltAdmin?'2':'1');
   if(!MP_SQL){ //Textdateien
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr(substr($aD[$i],$p),0,5)==$sCod.';'){
      $s=$sId.';'.(time()>>6).';'.$sNeuSta.substr(substr(rtrim($aD[$i]),$p),6); $aD[$i]=$s."\n";
      if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Meld=MP_TxAktiviert; $MTyp='Erfo'; $C=MP_TxLoginLogin;
       $aD=explode(';',$s); for($j=3;$j<6;$j++) $aD[$j]=fMpExCode($aD[$j]);
      }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $bOK=true;}
     }break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'"')){
     $i=$rR->num_rows; $aD=$rR->fetch_row(); $rR->close();
     if($i==1&&$aD[1]==$sCod){
      if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session="'.(time()>>6).'",aktiv="'.$sNeuSta.'" WHERE nr="'.$sId.'"')){
       $Meld=MP_TxAktiviert; $MTyp='Erfo'; $C=MP_TxLoginLogin; $aD[4]=fMpExCode($aD[4]);
      }else{$Meld=MP_TxSqlAendr; $bOK=true;}
    }}else $Meld=MP_TxSqlFrage;
   }
   if($MTyp=='Erfo'&&MP_NutzerNeuAdmMail&&MP_FreischaltAdmin){
    $sMlTx='';
    for($j=2;$j<$nNutzerFelder;$j++) $sMlTx.="\n".strtoupper(str_replace('`,',';',$aNF[$j])).': '.$aD[$j+1]; $sWww=fMpWww();
    require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailTo);
    if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
    $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#N',sprintf('%04d',$sId),MP_TxNutzNeuAdmBtr);
    if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
    $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",MP_TxNutzNeuAdmTxt)));
    $Mailer->Send();
   }
  }elseif(substr($sAkt,0,2)=='on'){ //Adressenfreischaltung
   $sCod=0; for($i=2;$i<13;$i++) $sCod+=substr($sAkt,$i,1); $C=MP_TxEMailFrei;
   if($sCod==substr($sAkt,13)){
    $sCod=substr($sAkt,2,13).';';
    if(!MP_SQL){ //Textdateien
     $aD=file(MP_Pfad.MP_Daten.MP_MailAdr); $aD[0]="#eMail\n"; $nSaetze=count($aD);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,14)==$sCod){
      $aD[$i]=rtrim(substr($aD[$i],14))."\n"; $aD=array_unique($aD);
      if(file_exists(MP_Pfad.MP_Daten.MP_MailAdr)&&is_writable(MP_Pfad.MP_Daten.MP_MailAdr)&&($f=fopen(MP_Pfad.MP_Daten.MP_MailAdr,'w'))){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Meld=MP_TxEMailErfo; $MTyp='Erfo';
      }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $bOK=true;}
      break;
    }}elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT nr,email FROM '.MP_SqlTabM.' WHERE email LIKE "'.$sCod.'%"')){
      $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
      if($i>0&&($sEml=substr($a[1],14))){
       $DbO->query('DELETE FROM '.MP_SqlTabM.' WHERE email="'.$sEml.'"');
       if($DbO->query('UPDATE IGNORE '.MP_SqlTabM.' SET email="'.$sEml.'" WHERE nr="'.$a[0].'"')){
        $Meld=MP_TxEMailErfo; $MTyp='Erfo';
       }else{$Meld=MP_TxSqlEinfg; $bOK=true;}
     }}else $Meld=MP_TxSqlFrage;
    }else $Meld=$SqE;
  }}elseif(substr($sAkt,0,2)=='of'){//Adressenabmeldung

  }
 }//POST

 //Formular- und Tabellenanfang
 $X=' <p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>
 <form class="mpForm" action="'.MP_Self.(MP_Query!=''?'?'.substr(MP_Query,5):'').'" method="post">
 <input type="hidden" name="mp_Aktion" value="'.$sAkt.'" />
 <div class="mpTabl">
  <div class="mpTbZl1"><div class="mpTbSp2" style="padding:8px;text-align:center;">'.fMpTx($C).'</div></div>
 </div>';
 if($bOK) $X.="\n".' <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxSenden).'" title="'.fMpTx(MP_TxSenden).'" /></div>'; else $X.='&nbsp;';

 $X.="\n".' </form>'."\n";

 return $X;
}

function fMpExCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>