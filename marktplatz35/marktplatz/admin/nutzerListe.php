<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Benutzerübersicht','<script type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.NutzerListe.length;++i)
   if(self.document.NutzerListe.elements[i].type=="checkbox") self.document.NutzerListe.elements[i].checked=bStat;
 }
</script>','NNl');

 $bNutzer=MP_NListeAnders||MP_NDetailAnders||MP_NEingabeAnders||MP_NVerstecktSehen;

 $sNavigator=''; $nStart=1; $sLschFrg='';
 $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1);
 $amNutzerFelder=min(AM_NutzerFelder,count($aNF)-1); $aTmp=array(); $aId=array();

 //Nutzer löschen
 if($_SERVER['REQUEST_METHOD']=='POST'){
  $bOK=false;
  foreach($_POST as $k=>$xx) if(substr($k,0,4)=='mp_L') $aId[(int)substr($k,4)]=true; //Loeschnummern
  if(count($aId)){
   if(isset($_POST['mpLsch'])&&$_POST['mpLsch']=='1'){
    if(!MP_SQL){ //Textdatei
     $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $nMx=0;
     for($i=1;$i<$nSaetze;$i++){
      $s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); $nMx=max($n,$nMx);
      if(isset($aId[$n])&&$aId[$n]){$aId[$n]=explode(';',rtrim($aD[$i])); $aD[$i]='';} //loeschen
     }
     if(substr($aD[0],0,7)!='Nummer_'){ //Kopfzeile defekt
      $s='Nummer_'.$nMx.';Session;aktiv'; $nNutzFelder=count($aNF); for($i=3;$i<$nNutzFelder;$i++) $s.=';'.$aNF[$i]; $aD[0]=$s.NL;
     }
     if($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Meld='Die markierten Benutzer wurden gelöscht.'; $MTyp='Meld';
     }else $Meld=str_replace('#','<i>'.MP_Daten.MP_Nutzer.'</i>',MP_TxDateiRechte);
    }else{ //bei SQL
     if($DbO){
      $s=''; foreach($aId as $k=>$xx) $s.=' OR nr='.$k; reset($aId);
      if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE '.substr($s,4))){
       while($a=$rR->fetch_row()) $aId[$a[0]]=$a; $rR->close();
       if($DbO->query('DELETE FROM '.MP_SqlTabN.' WHERE '.substr($s,4))){
        $Meld='Die markierten Benutzer wurden gelöscht.'; $MTyp='Meld';
       }else $Meld=MP_TxSqlFrage;
      }else $Meld=MP_TxSqlFrage;
     }else $Meld=MP_TxSqlVrbdg;
    }
    if($MTyp=='Meld'){ //es wurde geloescht
     if($sSubj=MP_TxNutzerLschBtrN){
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $sWww=fMpWww();
      $sSubj=str_replace('#A',$sWww,$sSubj); $sTxt=str_replace('#A',$sWww,str_replace('\n ',"\n",MP_TxNutzerLschTxtN));
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
     }else $Mailer=false;
     foreach($aId as $sId=>$aW){ // alle geloeschten Nutzer
      if($Mailer&&is_array($aW)){ // E-Mails
       if(!MP_SQL){$aW[3]=fMpDeCode($aW[3]); $aW[5]=fMpDeCode($aW[5]);}
       $Mailer->AddTo($aW[5]); $Mailer->SetReplyTo($aW[5]);
       $Mailer->Subject=str_replace('#U',$aW[2],str_replace('#E',$aW[5],str_replace('#N',$sId,$sSubj)));
       $Mailer->Text=str_replace('#U',$aW[3],str_replace('#E',$aW[5],str_replace('#N',$sId,$sTxt)));
       $Mailer->Send(); $Mailer->ClearTo();
      }
      $aSeg=array(); $aSeg=explode(';',MP_Segmente); $nSegmente=count($aSeg); // Inserate loeschen
      for($nSegNo=1;$nSegNo<$nSegmente;$nSegNo++) if(($sSegNam=$aSeg[$nSegNo])&&($sSegNam!='LEER')){ //ueber alle Segmente
       $sSegNo=sprintf('%02d',$nSegNo); $aLsch=array(); $sSegMeld='';
       if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
       $nFelder=0; $aStru=array(); $aMpFN=array(); $aMpFT=array(); $nUserPos=0; $nKontaktPos=0; //Struktur holen
       if(!MP_SQL){ //Text
        $aStru=(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)?file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur):array());
       }elseif($DbO){ //SQL
        if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSegNo.'"')){
         $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
       }}
       if(count($aStru)>1){
        $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
        if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
        $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
       }
       if($nUserPos=array_search('u',$aMpFT)){ //mit Benutzerfeld
        if(!MP_SQL){ //Inserate
         $aT=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aT);
         for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aT[$i])); array_splice($a,1,1); $nUser=(isset($a[$nUserPos])?sprintf('%0d',$a[$nUserPos]):'0');
          if($nUser==$sId){$aLsch[$a[0].$sBldSeg]=true; $aT[$i]='';}
         }
         if($n=count($aLsch)){
          if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)&&is_writable(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)){
           if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){ //Inserate neu schreiben
            fwrite($f,rtrim(str_replace("\r",'',implode('',$aT)))."\n"); fclose($f);
         }}}
        }elseif($DbO){ //bei SQL
         if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE CAST(mp_'.$nUserPos.' AS INTEGER)='.$sId)){
          while($a=$rR->fetch_row()) $aLsch[$a[0].$sBldSeg]=true; $rR->close();
          if($n=count($aLsch)) $DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE CAST(mp_'.$nUserPos.' AS INTEGER)='.$sId);
         }
        }
       }//nUserPos>0
       if(($n=count($aLsch))&&(in_array('b',$aMpFT)||in_array('f',$aMpFT))){ // Bilder und Dateien
        if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){
         $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])) $aD[]=$s; closedir($f);
         if($n=count($aD)) $sSegMeld.="\n  $n Bilder und Dateien zu abgelaufenen Inseraten geloescht";
         foreach($aD as $s) if(file_exists(MP_Pfad.MP_Bilder.$s)) unlink(MP_Pfad.MP_Bilder.$s);
       }}
      }//alle Segmente
     }//alle
    }//$MTyp
   }else{$sLschFrg='1'; $Meld='Wollen Sie die markierten Benutzer wirklich löschen?';}
  }else if(isset($_POST['mpLsch'])){$Meld='Die Benutzerdaten bleiben unverändert.'; $MTyp='Meld';}
 }
 //Nutzerstatus ändern
 if(isset($_GET['mp_Num'])&&($nNum=$_GET['mp_Num'])){
  if(isset($_GET['mp_Status'])) $nSta=(int)$_GET['mp_Status']; else $nSta=0; $sNDat="NUMMER: ".sprintf('%04d',$nNum);
  if(!MP_SQL){ //Textdatei
   $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNum.';'; $p=strlen($s); $bNeu=false;
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
    $s=$aD[$i]; $p=strpos($s,';',$p)+1;
    if((int)substr($s,$p,1)==1-$nSta||substr($s,$p,1)=='2'){ // zu aendern
     $aD[$i]=$nNum.';'.(time()>>6).';'.$nSta.substr($s,$p+1); $bNeu=true;
    }
    break;
   }
   if($bNeu) if($f=@fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w')){//neu schreiben
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    if($nSta==1){
     $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $sEml=fMpDeCode($a[4]); $nFelder=count($aNF);
     for($i=2;$i<$nFelder;$i++) $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[$i])).': '.($i>4?$a[$i]:fMpDeCode($a[$i]));
    }
    $Meld='Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.'; $MTyp='Erfo';
   }else $Meld=str_replace('#','<i>'.MP_Daten.MP_Nutzer.'</i>',MP_TxDateiRechte);
  }else{ //bei SQL
   if($DbO){
    if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session="'.(time()>>6).'",aktiv="'.$nSta.'" WHERE nr='.$nNum)){
     $Meld='Der Benutzer Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.'; $MTyp='Erfo';
     if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr='.$nNum)){
      if($a=$rR->fetch_row()){array_splice($a,1,1); $sEml=$a[4];} $rR->close(); $nFelder=count($aNF);
      for($i=2;$i<$nFelder;$i++) $sNDat.="\n".strtoupper(str_replace('`,',';',$aNF[$i])).': '.($i!=3?$a[$i]:fMpDeCode($a[3]));
     }
    }else $Meld=MP_TxSqlAendr;
   }else $Meld=MP_TxSqlVrbdg;
  }
  if(isset($sEml)&&MP_NutzerAktivMail){ //Aktivierungsmail
   if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
   $sBtr=str_replace('#A',$sWww,MP_TxNutzerAktivBtr); $sMTx=str_replace('#D',$sNDat,str_replace('#A',$sWww,str_replace('\n ',"\n",MP_TxNutzerAktivTxt)));
   require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
   if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
   $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
   if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
  }
 }

 //Abfrageparameter aufbereiten
 $nFelder=count($aNF); $aF1=array(); $aF2=array(); $aF2=array(); $sQ='';
 if($sIdn1=(int)(isset($_POST['Idn1'])?$_POST['Idn1']:(isset($_GET['Idn1'])?$_GET['Idn1']:0))){
  $sQ.='&Idn1='.$sIdn1;
  if($sIdn2=(int)(isset($_POST['Idn2'])?$_POST['Idn2']:(isset($_GET['Idn2'])?$_GET['Idn2']:0))) $sQ.='&Idn2='.$sIdn2;
 }else $sIdn2='';
 if($sLog=(isset($_POST['Log'])?(int)$_POST['Log']:(isset($_GET['Log'])?(int)$_GET['Log']:0))) $sQ.='&Log='.$sLog;
 if($sUsr1=trim(isset($_POST['Usr1'])?$_POST['Usr1']:(isset($_GET['Usr1'])?$_GET['Usr1']:''))){
  $sQ.='&Usr1='.urlencode($sUsr1);
  if($sUsr2=trim(isset($_POST['Usr2'])?$_POST['Usr2']:(isset($_GET['Usr2'])?$_GET['Usr2']:''))) $sQ.='&Usr2='.urlencode($sUsr2);
 }else $sUsr2='';
 if($sUsr3=trim(isset($_POST['Usr3'])?$_POST['Usr3']:(isset($_GET['Usr3'])?$_GET['Usr3']:''))) $sQ.='&Usr3='.urlencode($sUsr3);
 if($sEml1=trim(isset($_POST['Eml1'])?$_POST['Eml1']:(isset($_GET['Eml1'])?$_GET['Eml1']:''))){
  $sQ.='&Eml1='.urlencode($sEml1);
  if($sEml2=trim(isset($_POST['Eml2'])?$_POST['Eml2']:(isset($_GET['Eml2'])?$_GET['Eml2']:''))) $sQ.='&Eml2='.urlencode($sEml2);
 }else $sEml2='';
 if($sEml3=trim(isset($_POST['Eml3'])?$_POST['Eml3']:(isset($_GET['Eml3'])?$_GET['Eml3']:''))) $sQ.='&Eml3='.urlencode($sEml3);
 for($i=6;$i<$nFelder;$i++){
  if($aF1[$i]=trim(isset($_POST['F1'.$i])?$_POST['F1'.$i]:(isset($_GET['F1'.$i])?$_GET['F1'.$i]:''))){
   $sQ.='&F1'.$i.'='.urlencode($aF1[$i]);
   if($aF2[$i]=trim(isset($_POST['F2'.$i])?$_POST['F2'.$i]:(isset($_GET['F2'.$i])?$_GET['F2'.$i]:''))) $sQ.='&F2'.$i.'='.urlencode($aF2[$i]);
  }else $aF2[$i]='';
  if($aF3[$i]=trim(isset($_POST['F3'.$i])?$_POST['F3'.$i]:(isset($_GET['F3'.$i])?$_GET['F3'.$i]:''))) $sQ.='&F3'.$i.'='.urlencode($aF3[$i]);
 }
 $sSta1=(int)(isset($_POST['Sta1'])?$_POST['Sta1']:(isset($_GET['Sta1'])?$_GET['Sta1']:0));
 $sSta2=(int)(isset($_POST['Sta2'])?$_POST['Sta2']:(isset($_GET['Sta2'])?$_GET['Sta2']:0));
 $sSta3=(int)(isset($_POST['Sta3'])?$_POST['Sta3']:(isset($_GET['Sta3'])?$_GET['Sta3']:0));
 if($sSta1&&$sSta2&&$sSta3) $sSta1=$sSta2=$sSta3=0; if($sSta1) $sQ.='&Sta1=1'; if($sSta2) $sQ.='&Sta2=1'; if($sSta3) $sQ.='&Sta3=1';

 //Daten bereitstellen
 if(MP_Pfad>''){
  if($sLog) $nTm=(time()-86400*$sLog)>>6;
  if(!MP_SQL){ //Textdaten
   if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)){$aD=file(MP_Pfad.MP_Daten.MP_Nutzer); array_shift($aD);}
   else{$aD=array(); $Meld='Bitte zuerst die Pfade im Setup einstellen!';}
   foreach($aD as $s){
    $a=explode(';',rtrim($s)); $a[3]=fMpDeCode($a[3]); $a[5]=fMpDeCode($a[5]);
    if($sQ){ $b=true; //filtern
     if($sIdn1){
      if(!$sIdn2){if($sIdn1!=(int)$a[0]) $b=false;} else{if($sIdn1<(int)$a[0]||$sIdn2>(int)$a[0]) $b=false;}
     }
     if($sLog){$s=$a[1]; if(substr($s,0,1)=='~') $s=substr($s,1); if(strlen($s)>=8) if((int)$s>$nTm) $b=false;}
     if($sSta1&&!$sSta2&&!$sSta3) if($a[2]!='1') $b=false; if(($sSta2||$sSta3)&&!$sSta1) if($a[2]=='1') $b=false; // aktiv
     if($sSta2&&!$sSta1&&!$sSta3) if($a[2]!='0') $b=false; if(($sSta1||$sSta3)&&!$sSta2) if($a[2]=='0') $b=false; // inaktiv
     if($sSta3&&!$sSta1&&!$sSta2) if($a[2]!='2') $b=false; if(($sSta1||$sSta2)&&!$sSta3) if($a[2]=='2') $b=false; // wartend
     if($sUsr1){
      $s=str_replace('`,',';',$a[3]); $b1=stristr($s,$sUsr1);
      if(!$sUsr2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sUsr2))) $b=false;}
     }
     if($sUsr3) if(stristr(str_replace('`,',';',$a[3]),$sUsr3)) $b=false;
     if($sEml1){
      $s=$a[5]; $b1=stristr($s,$sEml1);
      if(!$sEml2){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$sEml2))) $b=false;}
     }
     if($sEml3) if(stristr($a[5],$sEml3)) $b=false;
     if($b) for($j=6;$j<$nFelder;$j++){
      if($t=$aF1[$j]){
       $s=str_replace('`,',';',$a[$j]); $b1=stristr($s,$t);
       if(!$t=$aF2[$j]){if(!$b1) $b=false;}else{if(!($b1||stristr($s,$t))) $b=false;}
      }
      if($t=$aF3[$j]) if(stristr(str_replace('`,',';',$a[$j]),$t)) $b=false;
     }
    }else $b=true;
    if($b) $aTmp[]=$a;
   }
  }elseif($DbO){ $sF=''; //SQL
   if($sIdn1){
    if(!$sIdn2) $sF.=' AND nr="'.$sIdn1.'"'; else $sF.=' AND nr BETWEEN "'.$sIdn1.'" AND "'.$sIdn2.'"';
   }
   if($sLog){$sF.=' AND(session LIKE "~%" AND session<"~'.$nTm.'" OR session<"'.$nTm.'")';}
   if($sSta1||$sSta2||$sSta3){$sF.=' AND('; if($sSta1) $sF.='aktiv="1"'; if($sSta2) $sF.=($sSta1?' OR ':'').'aktiv="0"'; if($sSta3) $sF.=($sSta1||$sSta2?' OR ':'').'aktiv="2"'; $sF.=')';}
   if($sUsr1){
    if(!$sUsr2) $sF.=' AND benutzer LIKE "%'.$sUsr1.'%"'; else $sF.=' AND (benutzer LIKE "%'.$sUsr1.'%" OR benutzer LIKE "%'.$sUsr2.'%")';
   }
   if($sUsr3) $sF.=' AND NOT benutzer LIKE "%'.$sUsr3.'%"';
   if($sEml1){
    if(!$sEml2) $sF.=' AND email LIKE "%'.$sEml1.'%"'; else $sF.=' AND (email LIKE "%'.$sEml1.'%" OR email LIKE "%'.$sEml2.'%")';
   }
   if($sEml3) $sF.=' AND NOT email LIKE "%'.$sEml3.'%"';
   for($j=6;$j<$nFelder;$j++){ $k=$j-1;
    if($t=$aF1[$j]){
     if(!$aF2[$j]) $sF.=' AND dat_'.$k.' LIKE "%'.$t.'%"'; else $sF.=' AND (dat_'.$k.' LIKE "%'.$t.'%" OR dat_'.$k.' LIKE "%'.$aF2[$j].'%")';
    }
    if($t=$aF3[$j]) $sF.=' AND NOT dat_'.$k.' LIKE "%'.$t.'%"';
   }
   if($sF) $sF=' WHERE'.substr($sF,4);
   $s=''; for($j=5;$j<=$amNutzerFelder;$j++) $s.=',dat_'.$j;
   if($rR=$DbO->query('SELECT nr,session,aktiv,benutzer,passwort,email'.$s.' FROM '.MP_SqlTabN.$sF.' ORDER BY nr')){
    while($a=$rR->fetch_row()) $aTmp[]=$a; $rR->close();
   }else $Msg='<p class="admFehl">'.MP_TxSqlFrage.'</p>';
  }else $Meld=MP_TxSqlVrbdg;
 }else $Meld='Bitte zuerst die Pfade im Setup einstellen!';
 if(isset($_GET['mp_Start'])) $nStart=(int)$_GET['mp_Start']; else if(isset($_POST['mp_Start'])) $nStart=(int)$_POST['mp_Start'];
 $nStop=$nStart+AM_NutzerLaenge; reset($aTmp); $nSaetze=count($aTmp); $aD=array(); $k=0;
 foreach($aTmp as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];
 if(!$Meld) if($bNutzer){$Meld='Benutzerliste'.($sQ?' (gefiltert)':''); $MTyp='Meld';} else $Meld='Die Benutzerverwaltung ist momentan inaktiv';

//Seitenausgabe
 echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
 echo fMpNavigator($nStart,$nSaetze,AM_NutzerLaenge,true,$sQ);
 $sNavigator=fMpNavigator($nStart,$nSaetze,AM_NutzerLaenge,false,$sQ);
?>

<form name="NutzerListe" action="nutzerListe.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 $bAendern=file_exists('nutzerAendern.php'); $bKontakt=file_exists('nutzerKontakt.php'); $bLoeschen=file_exists('nutzerLoeschen.php');
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td>&nbsp;</td>'.NL.' <td width="1%"><b>aktiv</b></td>'.NL.' <td><b>'.str_replace('`,',';',$aNF[2]).'</b></td>'.NL.' <td width="16">&nbsp;</td>';
 for($j=4;$j<=$amNutzerFelder;$j++){if(!$s=str_replace('`,',';',$aNF[$j])) $s='&nbsp;'; echo NL.' <td><b>'.$s.'</b></td>';}
 echo NL.'</tr>';
 if($nStart>1) $sQ.='&mp_Start='.$nStart; $aQ['Start']=$nStart;
 foreach($aD as $a){ //Datenzeilen ausgeben
  $Id=$a[0];
  echo NL.'<tr class="admTabl">';
  echo NL.' <td style="white-space:nowrap">'.($bLoeschen?'<input class="admCheck" type="checkbox" name="mp_L'.$Id.'" value="1"'.(isset($aId[$Id])?' checked="checked"':'').' /> ':'').sprintf('%04d',$Id).'</td>';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerAendern.php?mp_Num='.$Id.$sQ.'"><img src="iconAendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  $sSta=(isset($a[2])?$a[2]:'0');
  if($sSta=='1') $sSta='0"><img src="'.MPPFAD.'grafik/punktGrn.gif" width="12" height="12" border="0" title="freigeschaltet - jetzt sperren">';
  elseif($sSta=='0') $sSta='1"><img src="'.MPPFAD.'grafik/punktRot.gif" width="12" height="12" border="0" title="inaktiv - jetzt freischalten">';
  elseif($sSta=='2') $sSta='1"><img src="'.MPPFAD.'grafik/punktRtGn.gif" width="12" height="12" border="0" title="bestätigt - jetzt freischalten">';
  echo NL.' <td align="center">'.($bAendern?'<a href="nutzerListe.php?mp_Start='.$nStart.'&mp_Num='.$Id.$sQ.'&mp_Status='.$sSta.'</a>':substr($sSta,3)).'</td>';
  echo NL.' <td>'.(isset($a[3])?$a[3]:'').'</td>'; $s=(isset($a[5])?$a[5]:'');
  echo NL.' <td>'.($bKontakt?'<a href="nutzerKontakt.php?mp_Num='.$Id.$sQ.'"><img src="'.MPPFAD.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.' kontaktieren"></a>':'&nbsp;').'</td>';
  echo NL.' <td>'.$s.'</td>';
  for($j=5;$j<=$amNutzerFelder;$j++){if(isset($a[$j+1])){
   if(!$s=$a[$j+1]) $s='&nbsp;';}else $s='&nbsp;';
   if($aNF[$j]=='HABEN'&&substr($s,4,1)=='-'&&substr($s,7,1)=='-') $s=fMpAnzeigeDatum($s);
   echo NL.' <td>'.(MP_SQL?$s:str_replace('`,',';',$s)).'</td>';
  }
  echo NL.'</tr>';
 }

?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="mp_All" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" src="iconLoeschen.gif" width="12" height="13" align="top" border="0" title="markierte Benutzer löschen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo $amNutzerFelder+1?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="mpLsch" value="<?php echo $sLschFrg?>" />
<?php foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="mp_'.$k.'" value="'.$v.'" />'?>

</form>

<?php
echo $sNavigator;
if($bAendern) echo '<p style="text-align:center;">[ <a href="nutzerAendern.php?mp_neu=1">neuer Benutzer</a> ]</p>';

echo fSeitenFuss();

function fMpNavigator($nStart,$nCount,$nListenLaenge,$bS,$sQ){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge);
 $s ='<td style="width:16px;text-align:center;"><a href="nutzerListe.php?mp_Start=1'.($sQ?$sQ:'').'" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?mp_Start='.(($i-1)*$nListenLaenge+1).($sQ?$sQ:'').'" title="'.'">'.$nPg.'</a></td>';
 }
 $s.=NL.'  <td style="width:16px;text-align:center;"><a href="nutzerListe.php?mp_Start='.(max($nPgs-1,0)*$nListenLaenge+1).($sQ?$sQ:'').'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:8px;margin-bottom:8px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.($bS?'<td style="text-align:right">[ <a href="nutzerExport.php'.($sQ?'?'.substr($sQ,1):'').'">Export</a> ]&nbsp;[ <a href="nutzerSuche.php'.($sQ?'?'.substr($sQ,1):'').'">Suche</a> ]&nbsp;</td>':'').$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}
function fMpWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>