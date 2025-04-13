<?php
if(!function_exists('fFraSeite')){ //bei direktem Aufruf
 function fFraSeite(){return fFraLogin(true);}
}

function fFraLogin($bDirekt){ //Seiteninhalt
 $aNutzFld=explode(';',FRA_NutzerFelder); $nNutzerFelder=count($aNutzFld); $aNutzPflicht=explode(';',FRA_NutzerPflicht);
 $sForm='LoginForm'; $sAktion='login'; $sBtn=FRA_TxAnmelden; $sNaechst=''; $sPw=''; $bZurFrage=false; $bZurWertung=false; $bZumZentrum=false;
 $aW=array('0','0','','',''); $aFehl=array(); $bCaptcha=false; $bOK=false; $sId=''; $sSes=''; $Meld=''; $MTyp='Fehl'; $X='';
 $sClassVersion=(phpversion()>'5.3'?'':'4');

 if($bDirekt){//direkter Aufruf
  $sAntwort=FRA_Antwort; $sVerlauf=FRA_Verlauf; $sZeit=(defined('FRA_Zeit')?FRA_Zeit:''); $sDat=''; //evt. geerbte Werte
  if(isset($_POST['fra_Start'])&&($s=(int)$_POST['fra_Start'])) $sZeit.='" /><input type="hidden" name="fra_Start" value="'.$s;
 }else{ //includierter Aufruf wenn Fragen fertig
  $sSes=FRA_Session; $sAntwort=FRA_FertigAntwort; $sVerlauf=FRA_FertigVerlauf;
  $sZeit=time()-(int)FRA_Zeit; if($sZeit<3600) $sZeit=date('i:s',$sZeit); else $sZeit=date('H:i:s',$sZeit);
  $sZeit.='" /><input type="hidden" name="fra_Start" value="'.FRA_Zeit;
 }

 $bSQLOpen=false; //SQL-Verbindung oeffnen
 if(FRA_SQL){
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
 }

 $sCapTyp=(isset($_POST['fra_CaptchaTyp'])?$_POST['fra_CaptchaTyp']:FRA_CaptchaTyp); $bCapOk=false; $bCapErr=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 if($_SERVER['REQUEST_METHOD']!='POST'||(!$sSchritt=(isset($_POST['fra_Schritt'])?$_POST['fra_Schritt']:''))){ //GET
  $Meld=FRA_TxNutzerLogin; $MTyp='Meld';
  if($bCaptcha=FRA_Captcha&&(!(FRA_Nutzerzwang||FRA_TeilnehmerSperre)||FRA_NutzerNeuErlaubt||FRA_PasswortSenden)){ //Captcha erzeugen
   require_once(FRA_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(FRA_CaptchaTxFarb,FRA_CaptchaHgFarb);
  }
 }else{ //POST Formularauswertung
  $sSes=FRA_Session;
  for($i=2;$i<$nNutzerFelder;$i++) if(isset($_POST['fra_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['fra_F'.$i])))); if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n));
   $aW[$i]=(FRA_Zeichensatz==0?$s:(FRA_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//IGNORE',$s):html_entity_decode($s)));
  }else $aW[$i]='';

  if($sSchritt=='login'){ //Loginversuch auswerten
   if($bCaptcha=FRA_Captcha){ //Captcha behandeln
    require_once(FRA_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
    if(isset($_POST['fra_CaptchaCode'])) $Cap->Test($_POST['fra_CaptchaAntwort'],$_POST['fra_CaptchaCode'],$_POST['fra_CaptchaFrage']);
   }
   if(!FRA_NutzerSperre){
    if(($sNam=$aW[2])&&($sPw=$aW[3])){
     $s=fFraEnCode($sPw);
     if(!FRA_SQL){ //Textdateien
      $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $sEml=fFraEnCode($sNam); $sNam=fFraEnCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i]));
       if(is_array($a)&&count($a)>3) if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
        $sId=$a[0]; $aW=$a; $aW[2]=fFraDeCodeL($a[2]); $aW[3]=$sPw; $aW[4]=fFraDeCodeL($a[4]);
        for($j=5;$j<$nNutzerFelder;$j++) $aW[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        break;
      }}
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Passwort="'.$s.'" AND(Benutzer="'.strtolower($sNam).'" OR eMail="'.$sNam.'")')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){$sId=$a[0]; $aW=$a; $aW[3]=$sPw;}
      }else $Meld=FRA_TxSqlFrage;
     }//SQL
     if($sId!=''){ //gefunden
      $Meld=FRA_TxNutzerPruefe; $MTyp='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen';
      if(FRA_NutzerFrist>0&&($p=array_search('GUELTIG_BIS',$aNutzFld))&&isset($a[$p])&&$a[$p]>''&&$a[$p]<date('Y-m-d')) //abgelaufen
       {$a[1]='0'; $aW[1]='0';}
      if($a[1]=='1'&&(FRA_NachLoginWohin>'DatenX'||FRA_Nutzerverwaltung=='nachher')){ //aktiv
       $sSes=fFraSessionNr($sId);  // $aW[2],$aW[4] in TempSession??
       if(FRA_Nutzerverwaltung=='nachher') $bZurWertung=true; elseif(substr(FRA_NachLoginWohin,0,6)=='Fragen') $bZurFrage=true; else $bZumZentrum=true;
      }
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;}
     }else $Meld=FRA_TxNutzerFalsch;
    }else $Meld=FRA_TxNutzerNamePass;
   }else $Meld=FRA_TxNutzerSperre;
  }elseif($sSchritt=='pruefen'){ //Benutzerdaten pruefen/aendern
   if(($sId=$_POST['fra_Id'])&&($sPw=$_POST['fra_Pw'])){
    if(FRA_Zeichensatz>0) if(FRA_Zeichensatz==2) $sPw=iconv('UTF-8','ISO-8859-1//IGNORE',$sPw); else $sPw=html_entity_decode($sPw);
    $aW[1]=(isset($_POST['fra_F1'])?$_POST['fra_F1']:'0'); $aW[2]=strtolower($aW[2]); $s=fFraEnCode($sPw);
    if(strlen($aW[2])<4||strlen($aW[2])>25) $aFehl[2]=true; //Benutzer
    if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Passwort
    if(!fFraIsEMailAdrL($aW[4])) $aFehl[4]=true; //eMail
    for($i=5;$i<$nNutzerFelder;$i++) if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
    if(FRA_NutzerDSE1) if(isset($_POST['fra_DSE1'])&&$_POST['fra_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(FRA_NutzerDSE2) if(isset($_POST['fra_DSE2'])&&$_POST['fra_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(count($aFehl)==0){
     if(!FRA_SQL){ //Textdateien
      $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $sNam='#;'; $k=0;
      for($i=1;$i<$nSaetze;$i++){
       $aN=explode(';',rtrim($aD[$i]));
       if($aN[0]!=$sId||$aN[3]!=$s) $sNam.=$aN[2].';'; else{$a=$aN; $k=$i;}
      }
      if($k>0){ //gefunden
       $sForm='NutzerForm'; $sNaechst='pruefen';
       $aW[0]=$sId; $aW[1]=$a[1]; $a[2]=fFraDeCodeL($a[2]); $a[3]=fFraDeCodeL($a[3]); $a[4]=fFraDeCodeL($a[4]);
       for($j=5;$j<$nNutzerFelder;$j++){
        $a[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        if($aNutzFld[$j]=='GUELTIG_BIS'){
         $aW[$j]=$a[$j]; if(FRA_NutzerFrist>0&&isset($a[$j])&&$a[$j]>''&&$a[$j]<date('Y-m-d')){$a[1]='0'; $aW[1]='0';} //abgelaufen
        }
       }
       if($a!=$aW){ //veraendert
        if($a[2]==$aW[2]||!strpos($sNam,';'.fFraEnCode($aW[2]).';')){ //Benutzername unveraendert oder frei
         $s=$sId.';'.$a[1].';'.fFraEnCode($aW[2]).';'.fFraEnCode($aW[3]).';'.fFraEnCode($aW[4]);
         for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
         if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=FRA_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
         }else $Meld=str_replace('#',FRA_TxBenutzer,FRA_TxDateiRechte);
        }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
       }else{ //Login fertig
        if($a[1]=='1'){ //aktiv
         //$sSes=fFraSessionNeu($sId,$aW[2],$aW[4],NULL);
         $sSes=fFraSessionNr($sId); $Meld=FRA_TxNutzerOK; $MTyp='Erfo'; $sAktion='frage'; $sBtn=FRA_TxWeiter;
         if(FRA_NachLoginWohin=='DatenKorr') $bZurFrage=true;
        }else{$Meld=FRA_TxPassiv; $sAktion='login'; $sNaechst=''; $sPw=''; $sBtn=FRA_TxWeiter;} //nicht aktiv
       }
      }else $Meld=FRA_TxNutzerFalsch;
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sId.'" AND Passwort="'.$s.'"')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){ //gefunden
        $sForm='NutzerForm'; $sNaechst='pruefen';
        $aW[0]=$sId; $aW[1]=$a[1]; $a[3]=fFraDeCodeL($a[3]); $s='';
        if($a[2]!=$aW[2]) $s.=', Benutzer="'.$aW[2].'"'; if($a[3]!=$aW[3]) $s.=', Passwort="'.fFraEnCode($aW[3]).'"';
        if($a[4]!=$aW[4]) $s.=', eMail="'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){
         if($aNutzFld[$j]=='GUELTIG_BIS'){
          $aW[$j]=$a[$j]; if(FRA_NutzerFrist>0&&isset($a[$j])&&$a[$j]>''&&$a[$j]<date('Y-m-d')){$a[1]='0'; $aW[1]='0';} //abgelaufen
         }
         if($a[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.$aW[$j].'"';
        }
        if($s!=''){ //veraendert
         if($a[2]!=$aW[2]){ //Benutzname
          if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
           $i=$rR->num_rows; $rR->close();
          }else $i=1;
         }else $i=0;
         if($i==0){ //Benutzername unveraendert oder frei
          if($DbO->query('UPDATE IGNORE '.FRA_SqlTabN.' SET '.substr($s,2).' WHERE Nummer='.$sId)){
           $Meld=FRA_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
          }else $Meld=FRA_TxSqlAendr;
         }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
        }else{ //Login fertig
         if($a[1]=='1'){ //aktiv
          $sSes=fFraSessionNr($sId); $Meld=FRA_TxNutzerOK; $MTyp='Erfo'; $sAktion='frage'; $sBtn=FRA_TxWeiter;
          if(FRA_NachLoginWohin=='DatenKorr') $bZurFrage=true;
         }else{$Meld=FRA_TxPassiv; $sAktion='login'; $sNaechst=''; $sPw=''; $sBtn=FRA_TxWeiter;} //nicht aktiv
        }
       }else $Meld=FRA_TxNutzerFalsch;
      }else $Meld=FRA_TxSqlFrage;
     }//SQL
    }else{$Meld=FRA_TxEingabeFehl; $sForm='NutzerForm'; $sNaechst='pruefen';}
   }else $Meld=FRA_TxNutzerFalsch;
  }elseif($sSchritt=='neu'||$sSchritt=='erfassen'){ //neuer Benutzer
   if(($bCaptcha=FRA_Captcha)&&$sSchritt=='neu'){ //Captcha behandeln
    require_once(FRA_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
    if($Cap->Test($_POST['fra_CaptchaAntwort'],$_POST['fra_CaptchaCode'],$_POST['fra_CaptchaFrage'])){
     $bCapOk=true; $Cap->Delete(); $bCaptcha=false;
    }else{$bCapErr=true; $aFehl[0]=true; $sNeuName=$aW[2]; $aW[2]='';}
   }else $bCapOk=true;
   if($bCapOk){
    $sForm='NutzerForm'; $sNaechst='erfassen'; $aW[2]=strtolower($aW[2]);
    if(strlen($aW[2])<4||strlen($aW[2])>16) $aFehl[2]=true; //Benutzer
    if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Passwort
    if(!fFraIsEMailAdrL($aW[4])) $aFehl[4]=true; //eMail
    for($i=5;$i<$nNutzerFelder;$i++){
     if($aNutzFld[$i]=='GUELTIG_BIS') if(FRA_NutzerFrist>0){$aW[$i]=date('Y-m-d',time()+FRA_NutzerFrist*86400);} else $aW[$i]='';
     if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
    }
    if($sSchritt=='erfassen'){
     if(FRA_NutzerDSE1) if(isset($_POST['fra_DSE1'])&&$_POST['fra_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
     if(FRA_NutzerDSE2) if(isset($_POST['fra_DSE2'])&&$_POST['fra_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    }
    if(count($aFehl)==0){
     if(!FRA_SQL){ //Textdateien
      $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $sNam='#;'; $sId=0;
      for($i=1;$i<$nSaetze;$i++){$a=explode(';',rtrim($aD[$i])); $sNam.=$a[2].';'; $sId=max((int)$a[0],$sId);}
      if(!strpos($sNam,';'.fFraEnCode($aW[2]).';')){
       $s='Nummer_'.(++$sId).';aktiv'; for($j=2;$j<$nNutzerFelder;$j++) $s.=';'.$aNutzFld[$j]; $aD[0]=$s."\n";
       $s=$sId.';'.(FRA_Nutzerfreigabe?'1':'0').';'.fFraEnCode($aW[2]).';'.fFraEnCode($aW[3]).';'.fFraEnCode($aW[4]);
       for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]);
       if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".$s."\n"); fclose($f);
        $Meld=FRA_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; $sPw=''; $sBtn=FRA_TxWeiter;
       }else{$Meld=str_replace('#',FRA_TxBenutzer,FRA_TxDateiRechte); $sId='';}
      }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
       $i=$rR->num_rows; $rR->close();
       if($i==0){
        $s='Benutzer,Passwort,eMail'; $t='"'.$aW[2].'","'.fFraEnCode($aW[3]).'","'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){$s.=',dat_'.$j; $t.=',"'.$aW[$j].'"';}
        if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabN.' (aktiv,'.$s.') VALUES("'.(FRA_Nutzerfreigabe?'1':'0').'",'.$t.')')){
         if($sId=$DbO->insert_id){$Meld=FRA_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; $sPw=''; $sBtn=FRA_TxWeiter;}
         else $Meld=FRA_TxSqlEinfg;
        }else $Meld=FRA_TxSqlEinfg;
       }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
      }else $Meld=FRA_TxSqlFrage;
     }//SQL
     if(!empty($sId)){
      $sMlTx=''; for($j=2;$j<$nNutzerFelder;$j++) $sMlTx.="\n".strtoupper($aNutzFld[$j]!='GUELTIG_BIS'?$aNutzFld[$j]:(FRA_TxNutzerFrist?FRA_TxNutzerFrist:$aNutzFld[$j])).': '.$aW[$j];
      if(FRA_NutzerNeuMail){
       $sLnk=date('si').$sId; $n=(int)substr(FRA_Schluessel,-2); for($k=strlen($sLnk)-1;$k>=0;$k--) $n+=(int)substr($sLnk,$k,1);
       $sLnk=FRA_Www.'frage.php?fra_Aktion=ok'.sprintf('%02x',$n).$sLnk; $sWww=fFraWww();
       if($sAbl=(isset($_GET['fra_Ablauf'])?$_GET['fra_Ablauf']:(isset($_POST['fra_Ablauf'])?$_POST['fra_Ablauf']:''))) $sLnk.='&fra_Ablauf='.$sAbl;
       require_once(FRA_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($aW[4]); $Mailer->SetReplyTo($aW[4]);
       if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
       $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
       $Mailer->Subject=str_replace('#',$sWww,FRA_TxNutzerNeuBtr);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#L',$sLnk,str_replace('#A',$sWww,str_replace('\n ',"\n",FRA_TxNutzerNeuTxt))));
       $Mailer->Send();
      }
      if(FRA_NutzerNeuAdmMail){
       require_once(FRA_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(FRA_Empfaenger);
       if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
       $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
       $Mailer->Subject=str_replace('#',sprintf('%05d',$sId),FRA_TxNutzNeuAdmBtr);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",FRA_TxNutzNeuAdmTxt)));
       $Mailer->Send();
      }
      if(FRA_Nutzerverwaltung=='nachher'){
       $sSes=fFraSessionNr($sId); $bZurWertung=true;
      }
     }
    }else $Meld=FRA_TxEingabeFehl;
   }else $Meld=FRA_TxCaptchaFehl;
  }elseif($sSchritt=='senden'){ //Passwort vergessen
   if($bCaptcha=FRA_Captcha){ //Captcha behandeln
    require_once(FRA_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
    if($Cap->Test($_POST['fra_CaptchaAntwort'],$_POST['fra_CaptchaCode'],$_POST['fra_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
   }else $bCapOk=true;
   if($bCapOk){
    if($sNam=$aW[2]){
     if(!FRA_SQL){ //Textdateien
      $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $sEml=fFraEnCode($sNam); $sNam=fFraEnCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i]));
       if($a[2]==$sNam||$a[4]==$sEml){$sId=$a[0]; $sNam=fFraDeCodeL($a[2]); $sPass=fFraDeCodeL($a[3]); $sEml=fFraDeCodeL($a[4]); break;} //gefunden
      }
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Benutzer="'.strtolower($sNam).'" OR eMail="'.$sNam.'"')){
       if($a=$rR->fetch_row()){$sId=$a[0]; $sNam=$a[2]; $sPass=fFraDeCodeL($a[3]); $sEml=$a[4];} //gefunden
       $rR->close();
      }else $Meld=FRA_TxSqlFrage;
     }//SQL
     if(isset($sPass)){
      require_once(FRA_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
      if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
      $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $sWww=fFraWww();
      $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
      $Mailer->Subject=str_replace('#',$sWww,FRA_TxNutzerDatBtr);
      $Mailer->Text=str_replace('#P',$sPass,str_replace('#B',$sNam,str_replace('#N',sprintf('%05d',$sId),str_replace('#A',$sWww,str_replace('\n ',"\n",FRA_TxNutzerDaten)))));
      if($Mailer->Send()){
       $Meld=FRA_TxNutzerSend; $MTyp='Erfo'; $bOK=true;
       if($bCaptcha){$Cap->Delete(); $bCapErr=false; $bCapOk=false; if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(FRA_CaptchaTxFarb,FRA_CaptchaHgFarb);} //Captcha loeschen und neu
      }else $Meld=FRA_TxSendeFehl;
     }else $Meld=FRA_TxNutzerFalsch;
    }else $Meld=FRA_TxNutzerNameMail;
   }else $Meld=FRA_TxCaptchaFehl;
  }
 }//POST

 //Beginn der Ausgabe
 if($sForm=='LoginForm'){ //Loginformulare

 if($bCaptcha) $X="
<script type=\"text/javascript\">
 IE=document.all&&!window.opera; DOM=document.getElementById&&!IE; var ieBody=null; //Browserweiche
 var xmlHttpObject=null; var oForm=null;

 if(typeof XMLHttpRequest!='undefined') xmlHttpObject=new XMLHttpRequest();
 if(!xmlHttpObject){
  try{xmlHttpObject=new ActiveXObject('Msxml2.XMLHTTP');}
  catch(e){
   try{xmlHttpObject=new ActiveXObject('Microsoft.XMLHTTP');}
   catch(e){xmlHttpObject=null;}
 }}

 function reCaptcha(oFrm,sTyp){
  if(xmlHttpObject){
   oForm=oFrm; oForm.elements['fra_CaptchaTyp'].value=sTyp; oDate=new Date();
   xmlHttpObject.open('get','".FRA_Http."captcha.php?cod='+sTyp+oDate.getTime());
   xmlHttpObject.onreadystatechange=showResponse;
   xmlHttpObject.send(null);
 }}

 function showResponse(){
  if(xmlHttpObject){
   if(xmlHttpObject.readyState==4){
    var sResponse=xmlHttpObject.responseText;
    var sQuestion=sResponse.substring(33,sResponse.length-1);
    var aSpans=oForm.getElementsByTagName('span');
    var nImgId=0; for(var i=0;i<aSpans.length;i++) if(aSpans[i].className=='capImg'){nImgId=i; break;}
    oForm.elements['fra_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['fra_CaptchaFrage'].value=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['fra_CaptchaFrage'].value='".fFraTx(FRA_TxCaptchaHilfe)."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".FRA_Http.FRA_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>\n";

 if(!FRA_TeilnehmerSperre){ //keine Teilnehmersperre
 if(!FRA_Nutzerzwang){ //Teilnehmerregistrierung
 $aTlnFld=explode(';',';'.FRA_TeilnehmerFelder); $nTlnFelder=count($aTlnFld); $aTlnPfl=explode(';',';'.FRA_TeilnehmerPflicht);

 if(FRA_DSEPopUp&&(FRA_TeilnehmerDSE1||FRA_TeilnehmerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.FRA_DSEPopupW.',height='.FRA_DSEPopupH.',left='.FRA_DSEPopupX.',top='.FRA_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 $X.='
 <p class="fraMeld" style="margin-top:20px;">'.fFraTx(FRA_TxLoginErfassen).'</p>
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="erfassen" />
 <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
 <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
 <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />
 <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
 <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />
 <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />
 <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">';
 for($i=1;$i<$nTlnFelder;$i++) $X.="\n".'   <tr>
  <td class="fraLogi fra15Bs">'.fFraTx(str_replace('`,',';',$aTlnFld[$i])).(empty($aTlnPfl[$i])?'':'*').'</td>
  <td class="fraLogi"><div class="fraNorm"><input class="fraLogi" type="text" name="fra_Tln'.$i.'" value="'.(isset($aDat[$i])?$aDat[$i]:'').'" size="25" /></div></td>
 </tr>';
 if(FRA_TeilnehmerDSE1) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE1?'Fehl':'Norm').'">'.fFraDSEFld(1,$bDSE1).'</div></td></tr>';
 if(FRA_TeilnehmerDSE2) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE2?'Fehl':'Norm').'">'.fFraDSEFld(2,$bDSE2).'</div></td></tr>';
 $X.='
 <tr>
  <td class="fraLogi fra15Bs"><span class="fraMini">&nbsp;</span></td>
  <td class="fraLogi" style="text-align:right;"><span class="fraMini">* '.fFraTx(FRA_TxPflicht).'</span></td>
 </tr>';
 if($bCaptcha){ //Captcha-Zeile
 $X.='
 <tr>
  <td class="fraLogi fra15Bs capCell" style="padding-top:6px;vertical-align:top;">'.fFraTx(FRA_TxCaptchaFeld).'</td>
  <td class="fraLogi capCell">
   <input class="fraLogi capQuest" name="fra_CaptchaFrage" type="text" value="'.fFraTx($Cap->Type!='G'?$Cap->Question:FRA_TxCaptchaHilfe).'" />
   <div'.($bCapErr&&$sSchritt=='erfassen'?' class="fraFehl"':'').' style="white-space:nowrap">
    <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.FRA_Http.FRA_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
    <input class="fraLogi capAnsw" name="fra_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" />
    <input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
    <input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
    '.(FRA_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxZahlenCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
    '.(FRA_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxTextCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
    '.(FRA_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxGrafikCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
   </div>
  </td>
 </tr>';
 }
 $X.='
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxEintragen).'" title="'.fFraTx(FRA_TxEintragen).'" />
 </form>';
 }
 }else{
 $X.='
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi"><p class="fraFehl">'.fFraTx(FRA_TxTeilnehmerSperre).'</p></td>
  </tr>
 </table>';
 }

 if(!FRA_NutzerSperre){ //keine Benutzersperre
 //Loginmaske
 $X.='
 <p class="fraMeld" style="margin-top:20px;">'.fFraTx(FRA_TxLoginLogin).'</p>
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="login" />
 <input type="hidden" name="fra_Schritt" value="login" />
 <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
 <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
 <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />
 <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
 <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />
 <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />
 <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxBenutzername).'<br />'.fFraTx(FRA_TxOder).'<br />'.fFraTx(FRA_TxMailAdresse).'</td>
   <td class="fraLogi"><input class="fraLogi" type="text" name="fra_F2" value="'.fFraTx($aW[2]).'" maxlength="100" /></td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxPasswort).'</td>
   <td class="fraLogi"><input class="fraLogi" type="password" name="fra_F3" maxlength="16" /></td>
  </tr>
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxAnmelden).'" title="'.fFraTx(FRA_TxAnmelden).'" />';
 if($bCaptcha) $X.='<input type="hidden" name="fra_CaptchaAntwort" value="" /><input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" /><input name="fra_CaptchaFrage" type="hidden" value="'.fFraTx($Cap->Question).'" /><input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />';
 $X.="\n".' </form>';
 }else{
 $X.='
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi"><p class="fraFehl">'.fFraTx(FRA_TxNutzerSperre).'</p></td>
  </tr>
 </table>';
 }

 if(FRA_NutzerNeuErlaubt){ //neuer Nutzer
 $X.="\n".'
 <p class="fraMeld">'.fFraTx(FRA_TxLoginNeu).'</p>
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="login" />
 <input type="hidden" name="fra_Schritt" value="neu" />
 <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
 <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
 <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
 <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxGewuenscht).'<div class="fraNorm">'.fFraTx(FRA_TxBenutzername).'</div><span class="fraMini">'.fFraTx(FRA_TxNutzerRegel).'</span></td>
   <td class="fraLogi"><input class="fraLogi" type="text" name="fra_F2" value="'.(isset($sNeuName)?fFraTx($sNeuName):'').'" maxlength="25" /></td>
  </tr>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <tr>
   <td class="fraLogi fra15Bs capCell" style="padding-top:6px;vertical-align:top;">'.fFraTx(FRA_TxCaptchaFeld).'</td>
   <td class="fraLogi capCell">
    <input class="fraLogi capQuest" name="fra_CaptchaFrage" type="text" value="'.fFraTx($Cap->Type!='G'?$Cap->Question:FRA_TxCaptchaHilfe).'" />
    <div'.($bCapErr&&$sSchritt=='neu'?' class="fraFehl"':'').' style="white-space:nowrap">
     <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.FRA_Http.FRA_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
     <input class="fraLogi capAnsw" name="fra_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" style="width:10em;" size="15" />
     <input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
     <input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
     '.(FRA_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxZahlenCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxTextCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxGrafikCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
    </div>
   </td>
  </tr>';
 }
 $X.='
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxAnmelden).'" title="'.fFraTx(FRA_TxAnmelden).'" />
 </form>';
 }

 if(FRA_PasswortSenden){ //Passwort zusenden
 $X.="\n".'
 <p class="fraMeld">'.fFraTx(FRA_TxLoginVergessen).'</p>
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="login" />
 <input type="hidden" name="fra_Schritt" value="senden" />
 <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
 <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
 <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
 <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />
 <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />
 <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />
 <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxBenutzername).'<br />'.fFraTx(FRA_TxOder).'<br />'.fFraTx(FRA_TxMailAdresse).'</td>
   <td class="fraLogi"><input class="fraLogi" type="text" name="fra_F2" value="'.fFraTx($aW[2]).'" maxlength="100" /></td>
  </tr>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <tr>
   <td class="fraLogi fra15Bs capCell" style="padding-top:6px;vertical-align:top;">'.fFraTx(FRA_TxCaptchaFeld).'</td>
   <td class="fraLogi capCell">
    <input class="fraLogi capQuest" name="fra_CaptchaFrage" type="text" value="'.fFraTx($Cap->Type!='G'?$Cap->Question:FRA_TxCaptchaHilfe).'" />
    <div'.($bCapErr&&$sSchritt=='senden'?' class="fraFehl"':'').' style="white-space:nowrap">
     <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.FRA_Http.FRA_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
     <input class="fraLogi capAnsw" name="fra_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" style="width:10em;" size="15" />
     <input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
     <input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
     '.(FRA_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxZahlenCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxTextCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxGrafikCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
    </div>
   </td>
  </tr>';
 }
 $X.='
 </table>'."\n";
 if(!$bOK) $X.=' <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxSenden).'" title="'.fFraTx(FRA_TxSenden).'" />'."\n"; else $X.='&nbsp;';
 $X.=' </form>';
 }

 }elseif($sForm=='NutzerForm'){ //Benutzerdaten

 if($aW[1]=='1'){$s=''; $t='Grn';}else{$s=FRA_TxNicht.' '; $t='Rot';}
 if(FRA_DSEPopUp&&(FRA_NutzerDSE1||FRA_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.FRA_DSEPopupW.',height='.FRA_DSEPopupH.',left='.FRA_DSEPopupX.',top='.FRA_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 $X.='
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="'.$sAktion.'" />
 <input type="hidden" name="fra_Session" value="'.$sSes.'" />
 <input type="hidden" name="fra_Schritt" value="'.$sNaechst.'" />
 <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
 <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
 <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />
 <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
 <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />
 <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />
 <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />
 <input type="hidden" name="fra_Id" value="'.$sId.'" />
 <input type="hidden" name="fra_Pw" value="'.fFraTx($sPw).'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxNutzerNr).'</td>
   <td class="fraLogi">'.($sId!=''?sprintf('%05d ',$sId):'').'<img src="'.FRA_Http.'punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fFraTx($s.FRA_TxAktiv).'"><input type="hidden" name="fra_F1" value="'.$aW[1].'" />'.($aW[1]=='1'?'':' <span class="fraMini">('.fFraTx($s.FRA_TxAktiv).')</span>').'</td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxBenutzername).'*<div class="fraNorm"><span class="fraMini">'.fFraTx(FRA_TxNutzerRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[2])&&$aFehl[2]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F2" value="'.fFraTx($aW[2]).'" maxlength="25" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxPasswort).'*<div class="fraNorm"><span class="fraMini">'.fFraTx(FRA_TxPassRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[3])&&$aFehl[3]?' class="fraFehl"':'').'><input class="fraLogi" type="password" name="fra_F3" value="'.fFraTx($aW[3]).'" maxlength="16" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx(FRA_TxMailAdresse).'*</td>
   <td class="fraLogi"><div'.(isset($aFehl[4])&&$aFehl[4]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F4" value="'.fFraTx($aW[4]).'" maxlength="100" /></div></td>
  </tr>';
 for($i=5;$i<$nNutzerFelder;$i++){
  if($aNutzFld[$i]!='GUELTIG_BIS') $bNutzerFrist=false; else{$bNutzerFrist=true; if(FRA_TxNutzerFrist) $aNutzFld[$i]=FRA_TxNutzerFrist;}
  $X.='
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTx($aNutzFld[$i]).($aNutzPflicht[$i]?'*':'').'</td>
   <td class="fraLogi"><div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F'.$i.'" value="'.fFraTx($aW[$i]).($bNutzerFrist?'" style="width:8em;" readonly="readonly':'').'" maxlength="255" /></div></td>
  </tr>';
 }
 if(FRA_NutzerDSE1) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE1?'Fehl':'Norm').'">'.fFraDSEFld(1,$bDSE1).'</div></td></tr>';
 if(FRA_NutzerDSE2) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE2?'Fehl':'Norm').'">'.fFraDSEFld(2,$bDSE2).'</div></td></tr>';
 $X.='
  <tr><td class="fraLogi fra15Bs">&nbsp;</td><td class="fraLogi" style="text-align:right;">* <span class="fraMini">'.fFraTx(FRA_TxPflicht).'</span></td></tr>
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx($sBtn).'" title="'.fFraTx($sBtn).'" />
 </form>';
 }

 if(!isset($FehlSQL)){
  if(!($bZurFrage||$bZumZentrum||$bZurWertung)){ //Datenformular
   return "\n".' <p class="fra'.$MTyp.'">'.fFraTx($Meld)."</p>\n".$X."\n";
  }else{
   define('FRA_NeuSession',$sSes);
   if($bZurFrage){ //zum Test
    if(FRA_NachLoginWohin=='FragenB'){ //BenutzerTest
     if(!FRA_SQL){
      $a=@file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nZhl=count($a); $s=(int)$sId.';'; $l=strlen($s);
      for($j=1;$j<$nZhl;$j++) if(substr($a[$j],0,$l)==$s){$sZw=rtrim(substr($a[$j],$l)); break;} //Nutzer gefunden
     }elseif($bSQLOpen) if($rR=$DbO->query('SELECT Nummer,Tests FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$sId.'"')){
      if($a=$rR->fetch_row()) $sZw=$a[1]; $rR->close();
     }
     if(isset($sZw)){$a=explode(';',$sZw); $a=explode('=',$a[0]); if(strlen($a[0])) define('FRA_BFolgeName',$a[0]);}
    }
    include FRA_Pfad.'fraFrage.php'; return fFraFrage(false);
   }
   elseif($bZurWertung){define('FRA_FertigSession',$sSes); include(FRA_Pfad.'fraBewerten.php'); return fFraBewerten(false);}
   else{include FRA_Pfad.'fraZentrum.php'; return fFraZentrum(false);}
  }
 }else return "\n".' <p class="fraFehl">'.fFraTx($FehlSQL)."</p>\n".$X."\n";
}

function fFraIsEMailAdrL($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}

function fFraSessionNr($sId){
 $n=(int)substr(FRA_Schluessel,-2); $sSes=rand(10,99).sprintf('%05d',$sId).((time()>>8)+round(FRA_MaxSessionZeit/4)); // 4-Minuten Intervalle
 for($i=strlen($sSes)-1;$i>=0;$i--) $n+=(int)substr($sSes,$i,1); return dechex($n).$sSes;
}

function fFraDSEFld($z,$bCheck=false){
 $s='<a class="fraText" href="'.FRA_DSELink.'"'.(FRA_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(FRA_DSETarget?' target="'.FRA_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fFraTx($z!=2?FRA_TxDSE1:FRA_TxDSE2)));
 return '<input class="fraCheck" type="checkbox" name="fra_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

function fFraEnCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fFraDeCodeL($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fFraWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; elseif(isset($_SERVER['SERVER_ADDR'])) $s=$_SERVER['SERVER_ADDR']; else $s='localhost';
 return $s;
}
?>