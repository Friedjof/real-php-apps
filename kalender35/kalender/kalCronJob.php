<?php
header('Content-Type: text/plain; charset=ISO-8859-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); mysqli_report(MYSQLI_REPORT_OFF);
if(isset($_GET['kal'])){
 @include('./kalWerte.php'); $DbO=NULL;
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if($_GET['kal']==KAL_Schluessel){
  if(KAL_SQL){ //SQL-Verbindung oeffnen
   $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
   if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);} else{$DbO=NULL; echo "\n".KAL_TxSqlVrbdg;}
  }

 // --- Anfang Cronjob ---
 $sRes='Kalender-Cron-Job gestartet: '.date('H:i:s')."\n"; $aT=array(); $nSaetze=0; $bDo=false;

 // alte abgelaufene Termine l�schen
 $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*KAL_HalteAltesNochTage)) $sRefDat=''; $nFelder=count($kal_FeldName);
 $nDtPos2=0; for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='d') if($nDtPos2<2) $nDtPos2=$i;
 if(!KAL_SQL){ //ohne SQL
  $aT=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aT);
  $nDtPos2++; $b2=KAL_EndeDatum&&($nDtPos2>2);
  for($i=1;$i<$nSaetze;$i++){
   $s=rtrim($aT[$i]); $p=strpos($s,';'); $b=true;
   if(substr($s,$p+3,10)<$sRefDat){ //erstes Datum abgelaufen
    if($b2){$aZl=explode(';',$s,$nDtPos2+2); if(substr($aZl[$nDtPos2],0,10)>$sRefDat) $b=false;}//zweites Datum nicht abgelaufen
    if($b){$aLsch[(int)substr($aT[$i],0,$p)]=true; $aT[$i]='';}
   }
  }
  if($n=count($aLsch)){ $bDo=true;
   if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){ //Termine neu schreiben
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aT)))."\n"); fclose($f);
    $sRes.="\nTermindatei: $n abgelaufene Termine gel�scht"; $bDo=true;
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nD=count($aD); $b=false; //Erinnerungen bereinigen
    for($i=1;$i<$nD;$i++){
     $s=substr($aD[$i],11,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
    }
    if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
    }
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nD=count($aD); $b=false; //Benachrichtigungen bereinigen
    for($i=1;$i<$nD;$i++){
     $s=substr($aD[$i],0,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
    }
    if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
    }
    if(KAL_ZusageSystem){//Zusagenliste kuerzen
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nD=count($aD); $b=false;
     for($i=1;$i<$nD;$i++){
      $s=substr($aD[$i],0,20); $n=(int)substr($s,1+strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
     }
     if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
   }}}else $sRes.="\nTermindatei: keine Erlaubnis zum L�schen von $n Terminen";
 }}elseif($DbO){ //bei SQL
  $sDtFld2=''; if(KAL_EndeDatum&&($nDtPos2>1)) $sDtFld2=' AND kal_'.$nDtPos2.'<"'.$sRefDat.'"'; $sD='';
  if($rR=$DbO->query('SELECT id FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2)){
   while($a=$rR->fetch_row()){$aLsch[(int)$a[0]]=true; $sD.=' OR termin="'.$a[0].'"';} $rR->close();
   if($n=count($aLsch)){$bDo=true;
    if($DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2)){
     $sRes.="\nTermintabelle: $n abgelaufene Termine gel�scht"; $sD=substr($sD,4);
     $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE '.$sD);
     $DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE '.$sD);
     $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.$sD);
    }else $sRes.="\nTermintabelle: L�schen von $n Terminen gescheitert";
  }}else $sRes.="\nTermintabelle: Abfragefehler bei alten Terminen";
 }
 if($n=count($aLsch)&&(in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))){ //veraltete Bilder und Dateien
  if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
   $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])) $aD[]=$s; closedir($f);
   if($n=count($aD)) $sRes.="\nBilder-Verzeichnis: $n Bilder und Dateien zu abgelaufenen Terminen gel�scht";
   foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
 }}

 //uralte verwaiste Erinnerungen loeschen
 if(!KAL_SQL){ //ohne SQL
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nD=count($aD); $n=0;
  for($i=1;$i<$nD;$i++) if(substr($aD[$i],0,10)<$sRefDat){$aD[$i]=''; $n++;}
  if($n>0) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
   fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
  }
 }elseif($DbO){ //bei SQL
  if($DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE datum<"'.$sRefDat.'"')) $n=$DbO->affected_rows; else $n=0;
 }
 if($n>0) $sRes.="\nErinnerungen: $n alte verwaiste Erinnerungen gel�scht.";

 //TerminErinnerungen versenden
 if(KAL_ListenErinn>=0||KAL_DetailErinn>=0){
  $sRefDat=date('Y-m-d'); $aE=array();
  if(!KAL_SQL){ //ohne SQL
   $aD=@file(KAL_Pfad.KAL_Daten.KAL_Erinner); $aD[0]='#Datum;Termin;eMail'."\n"; $nD=count($aD);
   for($i=1;$i<$nD;$i++) if(substr($aD[$i],0,10)<=$sRefDat){
    $a=explode(';',rtrim(substr($aD[$i],11))); $aE[(int)$a[0]][]=$a[1]; $aD[$i]='';
   }
  }elseif($DbO){ //mit SQL
   if($rR=$DbO->query('SELECT id,termin,email FROM '.KAL_SqlTabE.' WHERE datum<="'.$sRefDat.'"')){
    while($a=$rR->fetch_row()) $aE[(int)$a[1]][]=$a[2]; $rR->close();
   }else $sRes.="\nErinnerungen: Abfragefehler in der Erinnerungstabelle.";
  }
  if($n=count($aE)){
   $sRes.="\nErinnerungen: $n Erinnerungen zum Versand anstehend."; $aN=array(); $bDo=true;
   require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $sWww=fKalHost(); $nE=0; $nT=0; $nFelder=count($kal_FeldName);
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxErinnSendBtr));
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
   if(KAL_NutzerErinnFeld>0&&in_array('u',$kal_FeldType)){// evt. erst mal User holen
    if(!KAL_SQL){//Text
     $aU=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nU=count($aU);
     for($j=1;$j<$nU;$j++){
      $a=explode(';',rtrim($aU[$j])); array_splice($a,1,1);
      if(!$s=$a[KAL_NutzerErinnFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerErinnFeld<5&&KAL_NutzerErinnFeld>1) $s=fKalDeCode($s);
      $aN[(int)$a[0]]=$s;
    }}elseif($DbO){//mit SQL
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' ORDER BY nr')){
      while($a=$rR->fetch_row()){
       array_splice($a,1,1);
       if(!$s=$a[KAL_NutzerErinnFeld]) $s=KAL_TxAutorUnbekannt;
       $aN[(int)$a[0]]=$s;
      }$rR->close();
   }}}
   $aDF=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld);
   if(!KAL_SQL){//Text
    for($j=1;$j<$nSaetze;$j++){
     $nId=(int)substr($aT[$j],0,strpos($aT[$j],';'));
     if(isset($aE[$nId])){
      $a=explode(';',rtrim($aT[$j])); array_splice($a,1,1);
      if($a[1]>=$sRefDat){
       $sMlTx=strtoupper(KAL_TxNummer).': '.$nId; $nT++;
       for($i=1;$i<$nFelder;$i++){
        $s=$a[$i]; $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
        if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; if(strlen($s)) $s=(int)$s;}
        elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
        if($aDF[$i]>0&&(KAL_ZeigeLeeres||!empty($s))&&($t!='m'||KAL_ErinnMitMemo)&&$t!='c'&&$t!='e'&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')
         $sMlTx.="\n".strtoupper($sFN).': '.fKalPlainText($s,$t,$aN);
       }
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxErinnSendTxt)));
       $aM=$aE[$nId]; foreach($aM as $s){$Mailer->ClearTo(); $Mailer->AddTo($s); $Mailer->SetReplyTo($s); $Mailer->Send(); $nE++;}
   }}}}elseif($DbO){//mit SQL
    foreach($aE as $nId=>$aM){
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$nId.'" AND online="1"')){
      $a=$rR->fetch_row(); $rR->close();
      if($a[2]>=$sRefDat){
       array_shift($a); $sMlTx=strtoupper(KAL_TxNummer).': '.$nId; $nT++;
       for($i=1;$i<$nFelder;$i++){
        $s=$a[$i]; $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
        if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; if(strlen($s)) $s=(int)$s;}
        elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
        if($aDF[$i]>0&&(KAL_ZeigeLeeres||!empty($s))&&($t!='m'||KAL_ErinnMitMemo)&&$t!='c'&&$t!='e'&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')
         $sMlTx.="\n".strtoupper($sFN).': '.fKalPlainText($s,$t,$aN);
       }
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxErinnSendTxt)));
       $aM=$aE[$nId]; foreach($aM as $s){$Mailer->ClearTo(); $Mailer->AddTo($s); $Mailer->SetReplyTo($s); $Mailer->Send(); $nE++;}
   }}}}
   $sRes.="\nErinnerungen: $nE Erinnerungen zu $nT Terminen versandt.";
   if($nE>0){ //kuerzen
    if(!KAL_SQL){
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
     }
    }elseif($DbO) $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE datum<="'.$sRefDat.'"');
    $sRes.="\nErinnerungen: Erinnerungsliste um $n Eintr�ge gek�rzt.";
   }
  }else $sRes.="\nErinnerungen: keine Erinnerungen zum Versand anstehend.";
 }

 // eMailadressen bereinigen
 $nAlt=round((time()-1209600)>>8); $nLsch=0; //14Tage
 if(!KAL_SQL){ //ohne SQL
  $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $aD[0]="#eMail\n"; $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++) if($n=strpos($aD[$i],';')) if((int)substr($aD[$i],0,7)<$nAlt){$aD[$i]=''; $nLsch++;}//altes raus
  if($nLsch>0){ $bDo=true;
   if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_MailAdr,'w')){ //Adressen neu schreiben
    fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f);
    $sRes.="\nE-Mail-Adressen: $nLsch nicht freigeschaltete Adressen gel�scht.";
   }else $sRes.="\nE-Mail-Adressen: kein Schreibzugriff auf ".KAL_Daten.KAL_MailAdr.".";
  }
 }elseif($DbO){ //mit SQL
  $DbO->query('DELETE FROM '.KAL_SqlTabM.' WHERE email LIKE "%;%" AND email<"'.$nAlt.'"');
  if($nLsch=$DbO->affected_rows){$sRes.="\nE-Mail-Adressen: $nLsch nicht freigeschaltete Adressen gel�scht."; $bDo=true;}
 }

 // temp bereinigen
 $nAltZt=time()-21600; // 6 Stunden alt
 if($f=opendir(KAL_Pfad.'temp')){
  $aLsch=array();
  while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(KAL_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
  closedir($f);
  if($n=count($aLsch)) $sRes.="\nTemp-Verzeichnis bereinigt: $n Dateien";
  foreach($aLsch as $s) @unlink(KAL_Pfad.'temp/'.$s);
 }

 // captcha bereinigen
 if($f=opendir(KAL_Pfad.KAL_CaptchaPfad)){
  $aLsch=array();
  while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=KAL_CaptchaSpeicher) if(filemtime(KAL_Pfad.KAL_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
  closedir($f);
  if($n=count($aLsch)) $sRes.="\nCaptcha-Verzeichnis bereinigt: $n Dateien";
  foreach($aLsch as $s) @unlink(KAL_Pfad.KAL_CaptchaPfad.$s);
 }

 // --- Ende Cronjob ---
 $sRes.="\n\nKalender-Cron-Job beendet: ".date('H:i:s')."\n"; echo $sRes;
 if(KAL_CronMail&&$bDo){
  require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); $Mailer->Subject='Kalender-Cron-Job '.date('d.m.y');
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
  $Mailer->AddTo(KAL_Empfaenger); $Mailer->SetReplyTo(KAL_Empfaenger);
  $Mailer->Text=$sRes; $Mailer->Send();
 }

  if($DbO) $DbO->close(); //SQL schliessen
 }else echo "\n".'unberechtigter Aufruf: Geheimschluessel falsch!';
}else echo "\n".'unvollstaendige Aufrufadresse!';

//-----------------------------------------------------------------

function fKalAnzeigeDatum($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalPlainText($s,$t,$aN){
 if($s) switch($t){
  case 'd': $s=fKalAnzeigeDatum($s); break; // Datum
  case '@': $s=fKalAnzeigeDatum($s).' '.trim(substr($s,10)); break; // Eintrag
  case 'z': $s.=' '.KAL_TxUhr; break; //Uhrzeit
  case 'm':  //Memo
   if(KAL_ErinnMitMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'l': case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  case 'u':
   if(KAL_NutzerErinnFeld>0){
    if($n=(int)$s){
     $s=$aN[$n];
    }else $s=KAL_TxAutor0000;
   }
   break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}

function fKalDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fKalHost(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>