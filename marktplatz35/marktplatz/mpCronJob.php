<?php
header('Content-Type: text/plain; charset=ISO-8859-1');
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 
if(isset($_GET['mp'])){
 include('./mpWerte.php');
 if(phpversion()>='5.1.0') if(strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet);
 if($_GET['mp']==MP_Schluessel){

 // --- Anfang Cronjob ---
 $sRes='Marktplatz-Cron-Job gestartet: '.date('H:i:s')."\n"; $bDo=false;

 $bSQLOpen=false; $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; echo "\n".MP_TxSqlVrbdg;}
 }

 // alte abgelaufene Inserate loeschen und Warnungen fuer ablaufende Inserate senden
 $aU=NULL; $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1);
 $nWarnenPos=array_search('WARNEN',$aNF);
 if(!$sWrnDat=@date('Y-m-d',time()+86400*MP_WarnFristNeu)) $sWrnDat=date('Y-m-d');
 if(!$sRefDat=@date('Y-m-d',time()-86400*MP_HalteAltesNochTage)) $sRefDat='';

 $aSeg=array(); $aSeg=explode(';',MP_Segmente); $nSegmente=count($aSeg);
 //ueber alle Segmente
 for($nSegNo=1;$nSegNo<$nSegmente;$nSegNo++) if(($sSegNam=$aSeg[$nSegNo])&&($sSegNam!='LEER')){
  $sSegNo=sprintf('%02d',$nSegNo); $aLsch=array(); $sSegMeld='';
  if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

  //Struktur holen
  $nFelder=0; $aStru=array(); $aMpFN=array(); $aMpFT=array(); $nUserPos=0; $nKontaktPos=0;
  if(!MP_SQL){ //Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
  }elseif($bSQLOpen){ //SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSegNo.'"')){
    $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
  if(count($aStru)>1){
   $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
   if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
   $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
  }
  $nKontaktPos=array_search('c',$aMpFT); $nUserPos=array_search('u',$aMpFT); $nWarnMails=0;

  if((MP_WarnFristNeu>0||$nWarnenPos>0)&&$nUserPos>0&&!is_array($aU)){ //evt. einmalig User holen
   if(!MP_SQL){ //Text
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD);
    for($i=1;$i<$nSaetze;$i++){
     $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
     $a[4]=fMpDeCode($a[4]); $aU[$a[0]]=$a;
   }}elseif($bSQLOpen){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' ORDER BY nr')){
     while($a=$rR->fetch_row()){array_splice($a,1,1); $aU[$a[0]]=$a;}
     $rR->close();
  }}}

  if(!MP_SQL){ //Inserate
   $aT=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aT);
   for($i=1;$i<$nSaetze;$i++){
    $p=strpos($aT[$i],';'); $sDat=substr($aT[$i],$p+3,10); $sWarnenEml='';
    if($sDat<$sRefDat){$aLsch[(int)(substr($aT[$i],0,$p).$sBldSeg)]=true; $aT[$i]='';} //loeschen

    if(MP_WarnFristNeu>0||$nWarnenPos>0){ //Warnungen sollen prinzipiell versandt werden
     if($nUserPos>0){ //mit Benutzerfeld
      $a=explode(';',rtrim($aT[$i])); array_splice($a,1,1); $nUser=(isset($a[$nUserPos])?(int)$a[$nUserPos]:0);
      if($nUser>0&&isset($aU[$nUser])){
       if($nWarnenPos>0) $nWarnenTage=(isset($aU[$nUser][$nWarnenPos])?(int)$aU[$nUser][$nWarnenPos]:0); else $nWarnenTage=MP_WarnFristNeu;
       if($nWarnenTage>0&&$sDat==date('Y-m-d',time()+$nWarnenTage*86400)) $sWarnenEml=$aU[$nUser][4];
      }elseif($sDat==$sWrnDat) $sWarnenEml=MP_MailTo;
     }elseif($nKontaktPos>0){ //mit Kontaktfeld
      if($sDat==$sWrnDat){ //StandardErinnerungstag
       $a=explode(';',rtrim($aT[$i])); array_splice($a,1,1); $sWarnenEml=(isset($a[$nKontaktPos])?fMpDecode($a[$nKontaktPos]):'');
     }}
     if(!empty($sWarnenEml)){ $sDat=''; $sId=$a[0]; //Erinnerung versenden
      for($j=0;$j<$nFelder;$j++){
       if($s=$a[$j]) $sDat.="\n".strtoupper($aMpFN[$j]).': '.fMpFldFmt($s,$aMpFT[$j]);
      }
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $sAdr=fMpWww();
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sAdr,MP_TxWarnenBtr);
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->AddTo($sWarnenEml); $Mailer->SetReplyTo($sWarnenEml);
      $sMlTx=str_replace('#S',$sSegNam,str_replace('#A',$sAdr,str_replace('\n ',"\n",MP_TxWarnenTxt)));
      $sLnk=(MP_InfoLink==''?'http://'.MP_Www.'marktplatz.php?':MP_InfoLink.(!strpos(MP_InfoLink,'?')?'?':'&')).'mp_Aktion=detail&mp_Segment='.$nSegNo.'&mp_Nummer='.$sId.(MP_DetailPopup?'&mp_Popup=1':'');
      $Mailer->Text=str_replace('#D',trim($sDat),str_replace('#L',$sLnk,$sMlTx)); $Mailer->Send(); $nWarnMails++;
     }
    }
   }
   if($nWarnMails>0) $sSegMeld.="\n  $nWarnMails Warnungen wegen Inserateablauf versandt";
   if($n=count($aLsch)){ $bDo=true;
    if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)&&is_writable(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)){
     if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){ //Inserate neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aT)))."\n"); fclose($f);
      $sSegMeld.="\n  $n abgelaufene Inserate geloescht";
     }else $sSegMeld.="\n  keine Erlaubnis zum Loeschen von $n Inseraten im Segment $sSegNo";
    }else $sSegMeld.="\n  keine Erlaubnis zum Loeschen von $n Inseraten im Segment $sSegNo";
   }else $sSegMeld.="\n  keine Inserate im Segment $sSegNo  zu loeschen";
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"')){
    while($a=$rR->fetch_row()) $aLsch[(int)($a[0].$sBldSeg)]=true; $rR->close();
    if($n=count($aLsch)){ $bDo=true;
     if($DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"'))
      $sSegMeld.="\n  $n abgelaufene Inserate geloescht";
     else $sSegMeld.="\n  Loeschen von $n Inseraten gescheitert";
    }
   }else $sSegMeld.="\n  Abfragefehler bei alten Inseraten";
   if(MP_WarnFristNeu>0||$nWarnenPos>0){ //Warnungen sollen prinzipiell versandt werden
    if($nUserPos>0){ //mit Benutzerfeld
     if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1>"'.date('Y-m-d').'"')){
      while($a=$rR->fetch_row()){ array_splice($a,1,1); $sId=$a[0]; $sWarnenEml='';
       $sDat=$a[1]; $nUser=(isset($a[$nUserPos])?(int)$a[$nUserPos]:0);
       if($nUser>0&&isset($aU[$nUser])){
        if($nWarnenPos>0) $nWarnenTage=(isset($aU[$nUser][$nWarnenPos])?(int)$aU[$nUser][$nWarnenPos]:0); else $nWarnenTage=MP_WarnFristNeu;
        if($nWarnenTage>0&&$sDat==date('Y-m-d',time()+$nWarnenTage*86400)) $sWarnenEml=$aU[$nUser][4];
       }elseif($sDat==$sWrnDat) $sWarnenEml=MP_MailTo; $sDat='';
       if(!empty($sWarnenEml)){ //Erinnerung versenden
        for($j=0;$j<$nFelder;$j++){
         if($s=$a[$j]) $sDat.="\n".strtoupper($aMpFN[$j]).': '.fMpFldFmt($s,$aMpFT[$j]);
        }
        require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $sAdr=fMpWww();
        if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
        $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
        $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sAdr,MP_TxWarnenBtr);
        if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
        $Mailer->AddTo($sWarnenEml); $Mailer->SetReplyTo($sWarnenEml);
        $sMlTx=str_replace('#S',$sSegNam,str_replace('#A',$sAdr,str_replace('\n ',"\n",MP_TxWarnenTxt)));
        $sLnk=(MP_InfoLink==''?'http://'.MP_Www.'marktplatz.php?':MP_InfoLink.(!strpos(MP_InfoLink,'?')?'?':'&')).'mp_Aktion=detail&mp_Segment='.$nSegNo.'&mp_Nummer='.$sId.(MP_DetailPopup?'&mp_Popup=1':'');
        $Mailer->Text=str_replace('#D',trim($sDat),str_replace('#L',$sLnk,$sMlTx)); $Mailer->Send(); $nWarnMails++;
       }
      }$rR->close();
     }else $sSegMeld.="\n  Abfragefehler bei ablaufenden Inseraten";
    }elseif($nKontaktPos>0){ //mit Kontaktfeld
     if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1="'.$sWrnDat.'"')){
      while($a=$rR->fetch_row()){ array_splice($a,1,1); $sId=$a[0]; $sWarnenEml=$a[$nKontaktPos]; $sDat='';
       if(!empty($sWarnenEml)){ //Erinnerung versenden
        for($j=0;$j<$nFelder;$j++){
         if($s=$a[$j]) $sDat.="\n".strtoupper($aMpFN[$j]).': '.fMpFldFmt($s,$aMpFT[$j]);
        }
        require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $sAdr=fMpWww();
        if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
        $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
        $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sAdr,MP_TxWarnenBtr);
        if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
        $Mailer->AddTo($sWarnenEml); $Mailer->SetReplyTo($sWarnenEml);
        $sMlTx=str_replace('#S',$sSegNam,str_replace('#A',$sAdr,str_replace('\n ',"\n",MP_TxWarnenTxt)));
        $sLnk=(MP_InfoLink==''?'http://'.MP_Www.'marktplatz.php?':MP_InfoLink.(!strpos(MP_InfoLink,'?')?'?':'&')).'mp_Aktion=detail&mp_Segment='.$nSegNo.'&mp_Nummer='.$sId.(MP_DetailPopup?'&mp_Popup=1':'');
        $Mailer->Text=str_replace('#D',trim($sDat),str_replace('#L',$sLnk,$sMlTx)); $Mailer->Send(); $nWarnMails++;
       }
      }$rR->close();
   }}}
   if($nWarnMails>0) $sSegMeld.="\n $nWarnMails Warnungen wegen Inserateablauf versandt";
  }
  if(($n=count($aLsch))&&(in_array('b',$aMpFT)||in_array('f',$aMpFT))){ //veraltete Bilder und Dateien
   if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){
    $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])) $aD[]=$s; closedir($f);
    if($n=count($aD)) $sSegMeld.="\n  $n Bilder und Dateien zu abgelaufenen Inseraten geloescht";
    foreach($aD as $s) if(file_exists(MP_Pfad.MP_Bilder.$s)) unlink(MP_Pfad.MP_Bilder.$s);
  }}
  if(!empty($sSegMeld)) $sRes.="\nSegment ".$sSegNo.' '.$sSegNam.$sSegMeld;
 }

 // eMailadressen bereinigen
 $nAlt=round((time()-1209600)>>8); $nLsch=0; //14Tage
 if(!MP_SQL){ //ohne SQL
  $aD=file(MP_Pfad.MP_Daten.MP_MailAdr); $aD[0]="#eMail\n"; $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++) if($n=strpos($aD[$i],';')) if((int)substr($aD[$i],0,7)<$nAlt){$aD[]=''; $nLsch++;}//altes raus
  if($nLsch>0){
   if(file_exists(MP_Pfad.MP_Daten.MP_MailAdr)&&is_writable(MP_Pfad.MP_Daten.MP_MailAdr)){
    if($f=@fopen(MP_Pfad.MP_Daten.MP_MailAdr,'w')){ //Adressen neu schreiben
     fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f);
     $sRes.="\nE-Mail-Adressen: $nLsch nicht freigeschaltete Adressen geloescht.";
    }else $sRes.="\nE-Mail-Adressen: kein Schreibzugriff auf ".MP_Daten.MP_MailAdr.".";
   }else $sRes.="\nE-Mail-Adressen: kein Zugriff auf ".MP_Daten.MP_MailAdr.".";
  }
 }elseif($bSQLOpen){ //mit SQL
  $DbO->query('DELETE FROM '.MP_SqlTabM.' WHERE email LIKE "%;%" AND email<"'.$nAlt.'"');
  if($nLsch=$DbO->affected_rows) $sRes.="\nE-Mail-Adressen: $nLsch nicht freigeschaltete Adressen geloescht.";
 }

 // temp bereinigen
 $nAltZt=time()-21600; // 6 Stunden alt
 if($f=opendir(MP_Pfad.'temp')){
  $aLsch=array();
  while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html')
   if(filemtime(MP_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
  closedir($f);
  if($n=count($aLsch)) $sRes.="\nTemp-Verzeichnis bereinigt: $n Dateien";
  foreach($aLsch as $s) if(file_exists(MP_Pfad.'temp/'.$s)) unlink(MP_Pfad.'temp/'.$s);
 }

 // captcha bereinigen
 if($f=opendir(MP_Pfad.MP_CaptchaPfad)){
  $aLsch=array();
  while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=MP_CaptchaSpeicher)
   if(filemtime(MP_Pfad.MP_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
  closedir($f);
  if($n=count($aLsch)) $sRes.="\nCaptcha-Verzeichnis bereinigt: $n Dateien";
  foreach($aLsch as $s) if(file_exists(MP_Pfad.MP_CaptchaPfad.$s)) unlink(MP_Pfad.MP_CaptchaPfad.$s);
 }

 if($bSQLOpen) if($DbO) $DbO->close(); //SQL schliessen
 $sRes.="\n\nMarktplatz-Cron-Job beendet: ".date('H:i:s')."\n"; echo $sRes;
 // --- Ende Cronjob ---
 if(MP_CronMail&&$bDo){
  require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
  if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
  $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); $Mailer->Subject='Marktplatz-Cron-Job '.date('d.m.y');
  if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
  $Mailer->AddTo(MP_MailTo); $Mailer->SetReplyTo(MP_MailTo);
  $Mailer->Text=$sRes; $Mailer->Send();
 }

 }else echo "\n".'unberechtigter Aufruf: Geheimschluessel falsch!';
}else echo "\n".'unvollstaendige Aufrufadresse!';

function fMpAnzeigeDatum($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
function fMpDeCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fMpWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
function fMpFldFmt($sVal,$sTyp){
 $s=$sVal;
 switch($sTyp){
  case 'd': if($s) $s=fMpAnzeigeDatum($s); break;
  case 'b': case 'f': if($p=strpos($s,'|')) $s=substr($s,1+$p); break;
  case 'w': case 'n': case '1': case '2': case '3': case 'r': $s=str_replace('.',MP_Dezimalzeichen,$s); break;
  case 'e': case 'c': if(!MP_SQL) $s=fMpDeCode($s); break;
  case 'p': $s=fMpDeCode($s); break;
  case '@': $s=trim(fMpAnzeigeDatum($s).strstr($s,' ')); break;
  default: $s=str_replace('\n ',"\n",str_replace('`,',';',$s));
 }
 return $s;
}
?>