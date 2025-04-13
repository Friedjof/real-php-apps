<?php
if(!function_exists('fUmfSeite')){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfLogin(true);}
}

function fUmfLogin($bDirekt){ //Seiteninhalt
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false;
 $aNutzFld=explode(';',UMF_NutzerFelder); $nNutzerFelder=count($aNutzFld); $aNutzPflicht=explode(';',UMF_NutzerPflicht);
 $sForm='LoginForm'; $sAktion='login'; $sBtn=UMF_TxAnmelden; $sNaechst=''; $sPw='';
 $aW=array('0','0','','',''); $aFehl=array(); $bCaptcha=false; $bOK=false; $sId='';
 $sClassVersion=(phpversion()>'5.3'?'':'4');

 if($bDirekt){//direkter Aufruf
  $sSes=''; $sAntwort=UMF_Antwort; $sDat=''; //evt. geerbte Werte
 }else{ //includierter Aufruf wenn Fragen fertig
  $sSes=UMF_Session; $sAntwort=UMF_FertigAntwort;
 }

 if(UMF_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa);
  if(!mysqli_connect_errno()){if(UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
 }

 $sCapTyp=(isset($_POST['umf_CaptchaTyp'])?$_POST['umf_CaptchaTyp']:UMF_CaptchaTyp); $bCapOk=false; $bCapErr=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 if($_SERVER['REQUEST_METHOD']!='POST'||(!$sSchritt=(isset($_POST['umf_Schritt'])?$_POST['umf_Schritt']:''))){ //GET
  $Meld=UMF_TxNutzerLogin; $MTyp='Meld';
  if($bCaptcha=UMF_Captcha&&(!(UMF_Nutzerzwang||UMF_TeilnehmerSperre)||UMF_NutzerNeuErlaubt||UMF_PasswortSenden)){ //Captcha erzeugen
   require_once(UMF_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);
  }
 }else{ //POST Formularauswertung
  $sSes=UMF_Session;
  for($i=2;$i<$nNutzerFelder;$i++) if(isset($_POST['umf_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['umf_F'.$i])))); if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n));
   $aW[$i]=(UMF_Zeichensatz==0?$s:(UMF_Zeichensatz==2?iconv('UTF-8','ISO-8859-1',$s):html_entity_decode($s)));
  }else $aW[$i]='';

  if($sSchritt=='login'){ //Loginversuch auswerten
   if($bCaptcha=UMF_Captcha){ //Captcha behandeln
    require_once(UMF_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
    if(isset($_POST['umf_CaptchaCode'])) $Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage']);
   }
   if(!UMF_NutzerSperre){
    if(($sNam=$aW[2])&&($sPw=$aW[3])){
     $s=fUmfEnCode($sPw);
     if(!UMF_SQL){ //Textdateien
      $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sEml=fUmfEnCode($sNam); $sNam=fUmfEnCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i]));
       if(is_array($a)&&count($a)>3) if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
        $sId=$a[0]; $aW=$a; $aW[2]=fUmfDeCodeL($a[2]); $aW[3]=$sPw; $aW[4]=fUmfDeCodeL($a[4]);
        for($j=5;$j<$nNutzerFelder;$j++) $aW[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        break;
      }}
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Passwort="'.$s.'" AND(Benutzer="'.strtolower($sNam).'" OR eMail="'.$sNam.'")')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){$sId=$a[0]; $aW=$a; $aW[3]=$sPw;}
      }else $Meld=UMF_TxSqlFrage;
     }//SQL
     if($sId!=''){ //gefunden
      $Meld=UMF_TxNutzerPruefe; $MTyp='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen';
      if(UMF_NutzerFrist>0&&($p=array_search('GUELTIG_BIS',$aNutzFld))&&isset($a[$p])&&$a[$p]>''&&$a[$p]<date('Y-m-d')) //abgelaufen
       {$a[1]='0'; $aW[1]='0';}
      if($a[1]=='1'&&UMF_NachLoginWohin!='Daten'){ //aktiv
       $sSes=fUmfSessionNr($sId); $sForm='?'; $sNaechst='?'; // $aW[2],$aW[4] in TempSession??
       if(UMF_NachLoginWohin!='Zentrum'){
        if(UMF_Nutzerverwaltung=='vorher'||UMF_Registrierung=='vorher') $sAktion='frage'; else $sAktion='nachher';
       }else $sAktion='zentrum';
      }
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;}
     }else $Meld=UMF_TxNutzerFalsch;
    }else $Meld=UMF_TxNutzerNamePass;
   }else $Meld=UMF_TxNutzerSperre;
  }elseif($sSchritt=='pruefen'){ //Benutzerdaten pruefen/aendern
   if(($sId=$_POST['umf_Id'])&&($sPw=$_POST['umf_Pw'])){
    if(UMF_Zeichensatz>0) if(UMF_Zeichensatz==2) $sPw=iconv('UTF-8','ISO-8859-1',$sPw); else $sPw=html_entity_decode($sPw);
    $aW[1]=(isset($_POST['umf_F1'])?$_POST['umf_F1']:'0'); $aW[2]=strtolower($aW[2]); $s=fUmfEnCode($sPw);
    if(strlen($aW[2])<4||strlen($aW[2])>25) $aFehl[2]=true; //Benutzer
    if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Passwort
    if(!fUmfIsEMailAdrL($aW[4])) $aFehl[4]=true; //eMail
    for($i=5;$i<$nNutzerFelder;$i++) if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
    if(UMF_NutzerDSE1) if(isset($_POST['umf_DSE1'])&&$_POST['umf_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(UMF_NutzerDSE2) if(isset($_POST['umf_DSE2'])&&$_POST['umf_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(count($aFehl)==0){
     if(!UMF_SQL){ //Textdateien
      $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sNam='#;'; $k=0;
      for($i=1;$i<$nSaetze;$i++){
       $aN=explode(';',rtrim($aD[$i]));
       if($aN[0]!=$sId||$aN[3]!=$s) $sNam.=$aN[2].';'; else{$a=$aN; $k=$i;}
      }
      if($k>0){ //gefunden
       $sForm='NutzerForm'; $sNaechst='pruefen';
       $aW[0]=$sId; $aW[1]=$a[1]; $a[2]=fUmfDeCodeL($a[2]); $a[3]=fUmfDeCodeL($a[3]); $a[4]=fUmfDeCodeL($a[4]);
       for($j=5;$j<$nNutzerFelder;$j++){
        $a[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        if($aNutzFld[$j]=='GUELTIG_BIS'){
         $aW[$j]=$a[$j]; if(UMF_NutzerFrist>0&&isset($a[$j])&&$a[$j]>''&&$a[$j]<date('Y-m-d')){$a[1]='0'; $aW[1]='0';} //abgelaufen
        }
       }
       if($a!=$aW){ //veraendert
        if($a[2]==$aW[2]||!strpos($sNam,';'.fUmfEnCode($aW[2]).';')){ //Benutzername unveraendert oder frei
         $s=$sId.';'.$a[1].';'.fUmfEnCode($aW[2]).';'.fUmfEnCode($aW[3]).';'.fUmfEnCode($aW[4]);
         for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
         if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=UMF_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
         }else $Meld=str_replace('#',UMF_TxBenutzer,UMF_TxDateiRechte);
        }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
       }else{ //Login fertig
        if($a[1]=='1'){ //aktiv
         $sSes=fUmfSessionNr($sId); $Meld=UMF_TxNutzerOK; $MTyp='Erfo'; $sForm='?'; $sNaechst='?'; $sBtn=UMF_TxWeiter;
         if(UMF_NachLoginWohin!='Zentrum'){
          if(UMF_Nutzerverwaltung!='nachher') $sAktion='frage'; else $sAktion='nachher';
         }else $sAktion='zentrum';
        }else{$Meld=UMF_TxPassiv; $sAktion='login'; $sNaechst=''; $sPw=''; $sBtn=UMF_TxWeiter;} //nicht aktiv
       }
      }else $Meld=UMF_TxNutzerFalsch;
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.$sId.'" AND Passwort="'.$s.'"')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){ //gefunden
        $sForm='NutzerForm'; $sNaechst='pruefen';
        $aW[0]=$sId; $aW[1]=$a[1]; $a[3]=fUmfDeCodeL($a[3]); $s='';
        if($a[2]!=$aW[2]) $s.=', Benutzer="'.$aW[2].'"'; if($a[3]!=$aW[3]) $s.=', Passwort="'.fUmfEnCode($aW[3]).'"';
        if($a[4]!=$aW[4]) $s.=', eMail="'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){
         if($aNutzFld[$j]=='GUELTIG_BIS'){
          $aW[$j]=$a[$j]; if(UMF_NutzerFrist>0&&isset($a[$j])&&$a[$j]>''&&$a[$j]<date('Y-m-d')){$a[1]='0'; $aW[1]='0';} //abgelaufen
         }
         if($a[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.$aW[$j].'"';
        }
        if($s!=''){ //veraendert
         if($a[2]!=$aW[2]){ //Benutzname
          if($rR=$DbO->query('SELECT Nummer FROM '.UMF_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
           $i=$rR->num_rows; $rR->close();
          }else $i=1;
         }else $i=0;
         if($i==0){ //Benutzername unveraendert oder frei
          if($DbO->query('UPDATE IGNORE '.UMF_SqlTabN.' SET '.substr($s,2).' WHERE Nummer='.$sId)){
           $Meld=UMF_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
          }else $Meld=UMF_TxSqlAendr;
         }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
        }else{ //Login fertig
         if($a[1]=='1'){ //aktiv
          $sSes=fUmfSessionNr($sId); $Meld=UMF_TxNutzerOK; $MTyp='Erfo'; $sForm='?'; $sNaechst='?'; $sAktion='frage'; $sBtn=UMF_TxWeiter;
         }else{$Meld=UMF_TxPassiv; $sAktion='login'; $sNaechst=''; $sPw=''; $sBtn=UMF_TxWeiter;} //nicht aktiv
        }
       }else $Meld=UMF_TxNutzerFalsch;
      }else $Meld=UMF_TxSqlFrage;
     }//SQL
    }else{$Meld=UMF_TxEingabeFehl; $sForm='NutzerForm'; $sNaechst='pruefen';}
   }else $Meld=UMF_TxNutzerFalsch;
  }elseif($sSchritt=='neu'||$sSchritt=='erfassen'){ //neuer Benutzer
   if(($bCaptcha=UMF_Captcha)&&$sSchritt=='neu'){ //Captcha behandeln
    require_once(UMF_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
    if($Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage'])){
     $bCapOk=true; $Cap->Delete(); $bCaptcha=false;
    }else{$bCapErr=true; $aFehl[0]=true; $sNeuName=$aW[2]; $aW[2]='';}
   }else $bCapOk=true;
   if($bCapOk){
    $sForm='NutzerForm'; $sNaechst='erfassen'; $aW[2]=strtolower($aW[2]);
    if(strlen($aW[2])<4||strlen($aW[2])>16) $aFehl[2]=true; //Benutzer
    if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Passwort
    if(!fUmfIsEMailAdrL($aW[4])) $aFehl[4]=true; //eMail
    for($i=5;$i<$nNutzerFelder;$i++){
     if($aNutzFld[$i]=='GUELTIG_BIS') if(UMF_NutzerFrist>0){$aW[$i]=date('Y-m-d',time()+UMF_NutzerFrist*86400);} else $aW[$i]='';
     if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
    }
    if($sSchritt=='erfassen'){
     if(UMF_NutzerDSE1) if(isset($_POST['umf_DSE1'])&&$_POST['umf_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
     if(UMF_NutzerDSE2) if(isset($_POST['umf_DSE2'])&&$_POST['umf_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    }
    if(count($aFehl)==0){
     if(!UMF_SQL){ //Textdateien
      $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sNam='#;'; $sId=0;
      for($i=1;$i<$nSaetze;$i++){$a=explode(';',rtrim($aD[$i])); $sNam.=$a[2].';'; $sId=max((int)$a[0],$sId);}
      if(!strpos($sNam,';'.fUmfEnCode($aW[2]).';')){
       $s='Nummer_'.(++$sId).';aktiv'; for($j=2;$j<$nNutzerFelder;$j++) $s.=';'.$aNutzFld[$j]; $aD[0]=$s."\n";
       $s=$sId.';'.(UMF_Nutzerfreigabe?'1':'0').';'.fUmfEnCode($aW[2]).';'.fUmfEnCode($aW[3]).';'.fUmfEnCode($aW[4]);
       for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]);
       if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".$s."\n"); fclose($f);
        $Meld=UMF_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; $sPw=''; $sBtn=UMF_TxWeiter;
       }else{$Meld=str_replace('#',UMF_TxBenutzer,UMF_TxDateiRechte); $sId='';}
      }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT Nummer FROM '.UMF_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
       $i=$rR->num_rows; $rR->close();
       if($i==0){
        $s='Benutzer,Passwort,eMail'; $t='"'.$aW[2].'","'.fUmfEnCode($aW[3]).'","'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){$s.=',dat_'.$j; $t.=',"'.$aW[$j].'"';}
        if($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabN.' (aktiv,'.$s.') VALUES("'.(UMF_Nutzerfreigabe?'1':'0').'",'.$t.')')){
         if($sId=$DbO->insert_id){$Meld=UMF_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; $sPw=''; $sBtn=UMF_TxWeiter;}
         else $Meld=UMF_TxSqlEinfg;
        }else $Meld=UMF_TxSqlEinfg;
       }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
      }else $Meld=UMF_TxSqlFrage;
     }//SQL
     if(!empty($sId)){
      $sMlTx=''; for($j=2;$j<$nNutzerFelder;$j++) $sMlTx.="\n".strtoupper($aNutzFld[$j]!='GUELTIG_BIS'?$aNutzFld[$j]:(UMF_TxNutzerFrist?UMF_TxNutzerFrist:$aNutzFld[$j])).': '.$aW[$j];
      if(UMF_NutzerNeuMail){
       $sLnk=date('si').$sId; $n=(int)substr(UMF_Schluessel,-2); for($k=strlen($sLnk)-1;$k>=0;$k--) $n+=(int)(substr($sLnk,$k,1));
       $sLnk=UMF_Url.'umfrage.php?umf_Aktion=ok'.sprintf('%02x',$n).$sLnk; $sWww=fUmfWww();
       if($sAbl=(isset($_GET['umf_Ablauf'])?$_GET['umf_Ablauf']:(isset($_POST['umf_Ablauf'])?$_POST['umf_Ablauf']:''))) $sLnk.='&umf_Ablauf='.$sAbl;
       require_once(UMF_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($aW[4]); $Mailer->SetReplyTo($aW[4]);
       if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
       $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
       $Mailer->Subject=str_replace('#',$sWww,UMF_TxNutzerNeuBtr);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#L',$sLnk,str_replace('#A',$sWww,str_replace('\n ',"\n",UMF_TxNutzerNeuTxt))));
       $Mailer->Send();
      }
      if(UMF_NutzerNeuAdmMail){
       require_once(UMF_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(UMF_Empfaenger);
       if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
       $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
       $Mailer->Subject=str_replace('#',sprintf('%05d',$sId),UMF_TxNutzNeuAdmBtr);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",UMF_TxNutzNeuAdmTxt)));
       $Mailer->Send();
      }
      if(UMF_Nutzerverwaltung=='nachher'){$sSes=fUmfSessionNr($sId); $sAktion='xx'; $sAktion='grafik';}
     }
    }else $Meld=UMF_TxEingabeFehl;
   }else $Meld=UMF_TxCaptchaFehl;
  }elseif($sSchritt=='senden'){ //Passwort vergessen
   if($bCaptcha=UMF_Captcha){ //Captcha behandeln
    require_once(UMF_Pfad.'class'.$sClassVersion.'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
    if($Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
   }else $bCapOk=true;
   if($bCapOk){
    if($sNam=$aW[2]){
     if(!UMF_SQL){ //Textdateien
      $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sEml=fUmfEnCode($sNam); $sNam=fUmfEnCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i]));
       if($a[2]==$sNam||$a[4]==$sEml){$sId=$a[0]; $sNam=fUmfDeCodeL($a[2]); $sPass=fUmfDeCodeL($a[3]); $sEml=fUmfDeCodeL($a[4]); break;} //gefunden
      }
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Benutzer="'.strtolower($sNam).'" OR eMail="'.$sNam.'"')){
       if($a=$rR->fetch_row()){$sId=$a[0]; $sNam=$a[2]; $sPass=fUmfDeCodeL($a[3]); $sEml=$a[4];} //gefunden
       $rR->close();
      }else $Meld=UMF_TxSqlFrage;
     }//SQL
     if(isset($sPass)){
      require_once(UMF_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
      if(UMF_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=UMF_SmtpHost; $Mailer->SmtpPort=UMF_SmtpPort; $Mailer->SmtpAuth=UMF_SmtpAuth; $Mailer->SmtpUser=UMF_SmtpUser; $Mailer->SmtpPass=UMF_SmtpPass;}
      $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $sWww=fUmfWww();
      $Mailer->SetFrom($s,$t); if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
      $Mailer->Subject=str_replace('#',$sWww,UMF_TxNutzerDatBtr);
      $Mailer->Text=str_replace('#P',$sPass,str_replace('#B',$sNam,str_replace('#N',sprintf('%05d',$sId),str_replace('#A',$sWww,str_replace('\n ',"\n",UMF_TxNutzerDaten)))));
      if($Mailer->Send()){
       $Meld=UMF_TxNutzerSend; $MTyp='Erfo'; $bOK=true;
       if($bCaptcha){$Cap->Delete(); $bCapErr=false; $bCapOk=false; if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);} //Captcha loeschen und neu
      }else $Meld=UMF_TxSendeFehl;
     }else $Meld=UMF_TxNutzerFalsch;
    }else $Meld=UMF_TxNutzerNameMail;
   }else $Meld=UMF_TxCaptchaFehl;
  } //Passwort
 } //POST

// echo $sForm.' | '.$sNaechst.' | '.$sAktion.' | '.$sSes;   // ToDo
 //Beginn der Ausgabe
 if($sForm=='LoginForm'){ //Loginformulare
  if(!UMF_TeilnehmerSperre&&!UMF_Nutzerzwang){ // Teilnehmer erlaubt
   $aTlnFld=explode(';',';'.UMF_TeilnehmerFelder); $nTlnFelder=count($aTlnFld); $aTlnPfl=explode(';',';'.UMF_TeilnehmerPflicht);
   if(UMF_DSEPopUp&&(UMF_TeilnehmerDSE1||UMF_TeilnehmerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.UMF_DSEPopupW.',height='.UMF_DSEPopupH.',left='.UMF_DSEPopupX.',top='.UMF_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
   $X.='
   <p class="umfMeld" style="margin-top:24px;">'.fUmfTx(UMF_TxLoginErfassen).'</p>
   <form class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="erfassen" />'.(!UMF_Umfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').(!defined('UMF_Gespeichert')?'':'
   <input type="hidden" name="umf_Gespeichert" value="'.UMF_Gespeichert.'" />').'
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n ".UMF_Hidden).'
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">';
   for($i=1;$i<$nTlnFelder;$i++) $X.="\n".'   <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx(str_replace('`,',';',$aTlnFld[$i])).(empty($aTlnPfl[$i])?'':'*').'</td>
    <td class="umfLogi"><div class="umfNorm"><input class="umfLogi" type="text" name="umf_Tln'.$i.'" value="'.(isset($aDat[$i])?$aDat[$i]:'').'" size="25" /></div></td>
   </tr>';
   if(UMF_TeilnehmerDSE1) $X.="\n".'<tr><td class="umfLogi umf15Bs" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE1?'Fehl':'Norm').'">'.fUmfDSEFld(1,$bDSE1).'</div></td></tr>';
   if(UMF_TeilnehmerDSE2) $X.="\n".'<tr><td class="umfLogi umf15Bs" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE2?'Fehl':'Norm').'">'.fUmfDSEFld(2,$bDSE2).'</div></td></tr>';
   $X.='
   <tr class="umfTr">
    <td class="umfLogi umf15Bs"><span class="umfMini">&nbsp;</span></td>
    <td class="umfLogi" style="text-align:right;"><span class="umfMini">* '.fUmfTx(UMF_TxPflicht).'</span></td>
   </tr>';
   if($bCaptcha){ //Captcha-Zeile
    $X.="\n".'    <tr class="umfTr">
     <td class="umfLogi umf15Bs capCell">'.fUmfTx(UMF_TxCaptchaFeld).'</td>
     <td class="umfLogi capCell">
      <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
      <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
      <div'.($bCapErr&&$sSchritt=='erfassen'?' class="umfFehl"':'').'>
       <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.UMF_Url.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
       <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
      </div>
     </td>
    </tr>';
   }
   $X.='
   </table>
   <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxEintragen).'" title="'.fUmfTx(UMF_TxEintragen).'" />
   </form>';
  }elseif(UMF_TeilnehmerSperre){ //TeilnehmerSperre
   $X.='
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi"><p class="umfFehl">'.fUmfTx(UMF_TxTeilnehmerSperre).'</p></td>
    </tr>
   </table>';
  }
  if(!UMF_NutzerSperre){ //keine Benutzersperre - Loginmaske
   $X.='
   <p class="umfMeld" style="margin-top:24px;">'.fUmfTx(UMF_TxLoginLogin).'</p>
   <form class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="login" />
   <input type="hidden" name="umf_Schritt" value="login" />'.(!UMF_Umfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').(!defined('UMF_Gespeichert')?'':'
   <input type="hidden" name="umf_Gespeichert" value="'.UMF_Gespeichert.'" />').'
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n ".UMF_Hidden).'
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxBenutzername).'<br />'.fUmfTx(UMF_TxOder).'<br />'.fUmfTx(UMF_TxMailAdresse).'</td>
     <td class="umfLogi"><input class="umfLogi" type="text" name="umf_F2" value="'.fUmfTx($aW[2]).'" maxlength="100" /></td>
    </tr>
    <tr class="umfTr">
     <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxPasswort).'</td>
     <td class="umfLogi"><input class="umfLogi" type="password" name="umf_F3" maxlength="16" /></td>
    </tr>
   </table>
   <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxAnmelden).'" title="'.fUmfTx(UMF_TxAnmelden).'" />';
   if($bCaptcha) $X.='<input type="hidden" name="umf_CaptchaAntwort" value="" /><input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" /><input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Question).'" /><input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />';
   $X.="\n".'   </form>';
  }else{ //NutzerSperre
   $X.='
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi"><p class="umfFehl">'.fUmfTx(UMF_TxNutzerSperre).'</p></td>
    </tr>
   </table>';
  }
  if(UMF_NutzerNeuErlaubt){ //neuer Nutzer
   $X.="\n".'
   <p class="umfMeld" style="margin-top:24px;">'.fUmfTx(UMF_TxLoginNeu).'</p>
   <form class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="login" />
   <input type="hidden" name="umf_Schritt" value="neu" />'.(!UMF_Umfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').'
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n ".UMF_Hidden).'
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxGewuenscht).'<div class="umfNorm">'.fUmfTx(UMF_TxBenutzername).'</div><span class="umfMini">'.fUmfTx(UMF_TxNutzerRegel).'</span></td>
     <td class="umfLogi"><input class="umfLogi" type="text" name="umf_F2" value="'.(isset($sNeuName)?fUmfTx($sNeuName):'').'" maxlength="25" /></td>
    </tr>';
   if($bCaptcha){ //Captcha-Zeile
    $X.="\n".'    <tr class="umfTr">
     <td class="umfLogi umf15Bs capCell">'.fUmfTx(UMF_TxCaptchaFeld).'</td>
     <td class="umfLogi capCell">
      <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
      <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
      <div'.($bCapErr&&$sSchritt=='neu'?' class="umfFehl"':'').'>
       <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.UMF_Url.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
       <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
      </div>
     </td>
    </tr>';
   }
   $X.='
   </table>
   <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxAnmelden).'" title="'.fUmfTx(UMF_TxAnmelden).'" />
   </form>';
  }
  if(UMF_PasswortSenden){ //Passwort zusenden
   $X.="\n".'
   <p class="umfMeld" style="margin-top:24px;">'.fUmfTx(UMF_TxLoginVergessen).'</p>
   <form class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="login" />
   <input type="hidden" name="umf_Schritt" value="senden" />'.(!UMF_Umfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').(!defined('UMF_Gespeichert')?'':'
   <input type="hidden" name="umf_Gespeichert" value="'.UMF_Gespeichert.'" />').'
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n ".UMF_Hidden).'
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxBenutzername).'<br />'.fUmfTx(UMF_TxOder).'<br />'.fUmfTx(UMF_TxMailAdresse).'</td>
     <td class="umfLogi"><input class="umfLogi" type="text" name="umf_F2" value="'.fUmfTx($aW[2]).'" maxlength="100" /></td>
    </tr>';
   if($bCaptcha){ //Captcha-Zeile
    $X.="\n".'    <tr class="umfTr">
     <td class="umfLogi umf15Bs capCell">'.fUmfTx(UMF_TxCaptchaFeld).'</td>
     <td class="umfLogi capCell">
      <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
      <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
      <div'.($bCapErr&&$sSchritt=='senden'?' class="umfFehl"':'').'>
       <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.UMF_Url.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
       <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
      </div>
     </td>
    </tr>';
   }
   $X.='
   </table>'."\n";
   if(!$bOK) $X.='   <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxSenden).'" title="'.fUmfTx(UMF_TxSenden).'" />'."\n"; else $X.='&nbsp;';
   $X.='   </form>';
  } //Passwort
  if($bCaptcha) $X.=fJSCapCode();
 }elseif($sForm=='NutzerForm'){ //Benutzerdaten
  if($aW[1]=='1'){$s=''; $t='Grn';}else{$s=UMF_TxNicht.' '; $t='Rot';}
  if(UMF_DSEPopUp&&(UMF_NutzerDSE1||UMF_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.UMF_DSEPopupW.',height='.UMF_DSEPopupH.',left='.UMF_DSEPopupX.',top='.UMF_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
  $X.='
  <form class="umfForm" action="'.UMF_Self.'" method="post">
  <input type="hidden" name="umf_Aktion" value="'.$sAktion.'" />
  <input type="hidden" name="umf_Session" value="'.$sSes.'" />
  <input type="hidden" name="umf_Schritt" value="'.$sNaechst.'" />'.(!UMF_Umfrage?'':'
  <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').'
  <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.(!defined('UMF_Gespeichert')?'':'
  <input type="hidden" name="umf_Gespeichert" value="'.UMF_Gespeichert.'" />').'
  <input type="hidden" name="umf_Id" value="'.$sId.'" />
  <input type="hidden" name="umf_Pw" value="'.fUmfTx($sPw).'" />'.rtrim("\n ".UMF_Hidden).'
  <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
   <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxNutzerNr).'</td>
    <td class="umfLogi">'.($sId!=''?sprintf('%05d ',$sId):'').'<img src="'.UMF_Url.'punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fUmfTx($s.UMF_TxAktiv).'"><input type="hidden" name="umf_F1" value="'.$aW[1].'" />'.($aW[1]=='1'?'':' <span class="umfMini">('.fUmfTx($s.UMF_TxAktiv).')</span>').'</td>
   </tr>
   <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxBenutzername).'*<div class="umfNorm"><span class="umfMini">'.fUmfTx(UMF_TxNutzerRegel).'</span></div></td>
    <td class="umfLogi"><div'.(isset($aFehl[2])&&$aFehl[2]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F2" value="'.fUmfTx($aW[2]).'" placeholder="'.fUmfTx(UMF_TxBenutzername).'" maxlength="25" /></div></td>
   </tr>
   <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxPasswort).'*<div class="umfNorm"><span class="umfMini">'.fUmfTx(UMF_TxPassRegel).'</span></div></td>
    <td class="umfLogi"><div'.(isset($aFehl[3])&&$aFehl[3]?' class="umfFehl"':'').'><input class="umfLogi" type="password" name="umf_F3" value="'.fUmfTx($aW[3]).'" placeholder="'.fUmfTx(UMF_TxPasswort).'" maxlength="16" /></div></td>
   </tr>
   <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx(UMF_TxMailAdresse).'*</td>
    <td class="umfLogi"><div'.(isset($aFehl[4])&&$aFehl[4]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F4" value="'.fUmfTx($aW[4]).'"  placeholder="'.fUmfTx(UMF_TxMailAdresse).'" maxlength="100" /></div></td>
   </tr>';
  for($i=5;$i<$nNutzerFelder;$i++){
   if($aNutzFld[$i]!='GUELTIG_BIS') $bNutzerFrist=false; else{$bNutzerFrist=true; if(UMF_TxNutzerFrist) $aNutzFld[$i]=UMF_TxNutzerFrist;}
   $X.='
    <tr class="umfTr">
    <td class="umfLogi umf15Bs">'.fUmfTx($aNutzFld[$i]).($aNutzPflicht[$i]?'*':'').'</td>
    <td class="umfLogi"><div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F'.$i.'" value="'.fUmfTx($aW[$i]).($bNutzerFrist?'" style="width:8em" readonly="readonly':'').'" maxlength="255" /></div></td>
   </tr>';
  }
  if(UMF_NutzerDSE1) $X.="\n".'<tr><td class="umfLogi umf15Bs" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE1?'Fehl':'Norm').'">'.fUmfDSEFld(1,$bDSE1).'</div></td></tr>';
  if(UMF_NutzerDSE2) $X.="\n".'<tr><td class="umfLogi umf15Bs" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE2?'Fehl':'Norm').'">'.fUmfDSEFld(2,$bDSE2).'</div></td></tr>';
  $X.='
   <tr class="umfTr"><td class="umfLogi umf15Bs">&nbsp;</td><td class="umfLogi" style="text-align:right">* <span class="umfMini">'.fUmfTx(UMF_TxPflicht).'</span></td></tr>
  </table>
  <input type="submit" class="umfScha" value="'.fUmfTx($sBtn).'" title="'.fUmfTx($sBtn).'" />
  </form>';
 } // Benutzerdaten

 if(!isset($FehlSQL)){
  if($sAktion!='frage'&&$sAktion!='zentrum'&&$sAktion!='nachher'){ //LoginDatenformulare
   return "\n".' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n".$X."\n";
  }elseif(!empty($sSes)){ // Login fertig
   define('UMF_NeuSession',$sSes);
   if($sAktion=='frage'){ //zur Umfrage
    if(UMF_NachLoginWohin=='FragenB'){ //benutzerabhaengige Umfrage
     if(!UMF_SQL){
      $a=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nZhl=count($a); $s=(int)$sId.';'; $l=strlen($s);
      for($j=1;$j<$nZhl;$j++) if(substr($a[$j],0,$l)==$s){$sZw=rtrim(substr($a[$j],$l)); break;} //Nutzer gefunden
     }elseif($DbO) if($rR=$DbO->query('SELECT Nummer,Umfragen FROM '.UMF_SqlTabZ.' WHERE Nummer="'.$sId.'"')){
      if($a=$rR->fetch_row()) $sZw=$a[1]; $rR->close();
     }
     if(isset($sZw)){$a=explode(';',$sZw); $a=explode(':',$a[0]); if(strlen($a[0])) define('UMF_BUmfrage',$a[0]);}
    }
    include UMF_Pfad.'umfFrage.php'; return fUmfFrage(false);
   }elseif($sAktion=='zentrum'){
    if(UMF_Nutzerverwaltung=='nacher'&&UMF_NutzerLog){include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln((defined('UMF_Gespeichert')?UMF_Gespeichert:0),'N',$sAntwort,$sSes,$DbO);}
    include UMF_Pfad.'umfZentrum.php'; return fUmfZentrum(false);
   }else{ // zum Ende
    if((defined('UMF_Gespeichert')?UMF_Gespeichert:0)){$Meld=UMF_TxAbgestimmt; $MTyp='Erfo';} else{$Meld=UMF_TxGleicheAdresse; $MTyp='Fehl';}
    if(UMF_NutzerLog){include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln((defined('UMF_Gespeichert')?UMF_Gespeichert:0),'N',$sAntwort,$sSes,$DbO);}
    if(UMF_NachAbstimmen=='Fertig'){ //zum Fertigtext
     if(!UMF_FertigHtml){
      if(strlen(UMF_TxFertigText)>0) $X='  <p>'.fUmfBB(fUmfTx(UMF_TxFertigText))."</p>\n";
      if(strlen(UMF_GrafikLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:'').'">'.fUmfTx(UMF_GrafikLink)."</a> ]</span>\n";
      if(strlen(UMF_ZentrumLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').'">'.fUmfTx(UMF_ZentrumLink)."</a> ]</span>\n";
      if(strlen(UMF_NeuAnfangLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.'">'.fUmfTx(UMF_NeuAnfangLink)."</a> ]</span>\n";
      if(strlen($X)>0) $X="\n".' <div class="umfFrtg">'."\n".$X." </div>";
     }else{
      if($X=@implode('',file(UMF_Pfad.'umfFertig.inc.htm'))){
       $X=str_replace('{Grafik}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:''),$X);
       $X=str_replace('{Zentrum}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:''),$X);
       $X=str_replace('{Neuanfang}',UMF_Self,$X);
      }else $X=' <p class="umfFehl">Schablone <i>umfFertig.inc.htm</i> fehlt!</p>'."\n";
     }
     $X=(!empty($Meld)?' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n":'').$X;
    }else{include UMF_Pfad.'umfGrafik.php'; $X=fUmfGrafik($Meld,$MTyp);} //zur Grafik
   }
   return $X;
  }else return "\n".' <p class="umfFehl">'.fUmfTx(UMF_TxSessionUngueltig).'</p><p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n".$X."\n";
 }else return "\n".' <p class="umfFehl">'.fUmfTx($FehlSQL)."</p>\n".$X."\n";
}

function fUmfIsEMailAdrL($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}

function fUmfSessionNr($sId){
 $n=(int)substr(UMF_Schluessel,-2); $sSes=rand(10,99).sprintf('%05d',$sId).((time()>>8)+round(UMF_MaxSessionZeit/4)); //n*256sec=120min
 for($i=strlen($sSes)-1;$i>=0;$i--) $n+=(int)substr($sSes,$i,1); return dechex($n).$sSes;
}
function fUmfDSEFld($z,$bCheck=false){
 $s='<a class="umfText" href="'.UMF_DSELink.'"'.(UMF_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(UMF_DSETarget?' target="'.UMF_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fUmfTx($z!=2?UMF_TxDSE1:UMF_TxDSE2)));
 return '<input class="umfCheck" type="checkbox" name="umf_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}
function fUmfEnCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fUmfDeCodeL($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fUmfWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; elseif(isset($_SERVER['SERVER_ADDR'])) $s=$_SERVER['SERVER_ADDR']; else $s='localhost';
 return $s;
}
if(!function_exists('fJSCapCode')){function fJSCapCode(){
return "
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
   oForm=oFrm; oForm.elements['umf_CaptchaTyp'].value=sTyp; oDate=new Date();
   xmlHttpObject.open('get','".UMF_Url."captcha.php?cod='+sTyp+oDate.getTime());
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
    var nQstId=0; for(var i=0;i<aSpans.length;i++) if(aSpans[i].className=='capQuest'){nQstId=i; break;}
    oForm.elements['umf_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['umf_CaptchaFrage'].value=sQuestion;
     aSpans[nQstId].innerHTML=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['umf_CaptchaFrage'].value='".UMF_TxCaptchaHilfe."';
     aSpans[nQstId].innerHTML='".UMF_TxCaptchaHilfe."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".UMF_Url.UMF_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>";
}}
?>
