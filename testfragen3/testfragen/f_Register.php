<?php
error_reporting(E_ALL);
//error_reporting(E_ALL ^ E_NOTICE);

$sFraSelf=(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'f_Register.php'));
$sFraQS=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
$sFraHtmlVor=''; $sFraHtmlNach=''; $bFraOK=true; $sFraQry=''; $sFraHid=''; //Seitenkopf, Seitenfuss, Status

if(substr($sFraSelf,-14)=='f_Register.php'){ //direkter Aufruf
 if($fraAblauf=strstr($sFraQS,'fra_Ablauf=')) $fraAblauf=(int)substr($fraAblauf,11,2);
 elseif(isset($_POST['fra_Ablauf'])) $fraAblauf=(int)$_POST['fra_Ablauf']; else $fraAblauf='';
 include('fraWerte'.$fraAblauf.'.php'); define('FRA_Ablauf',$fraAblauf);
 if(defined('FRA_Version')){
  header('Content-Type: text/html; charset='.(FRA_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);
  $sFraSchablone=FRA_Schablone;
  if(strlen($sFraSchablone)>0){ //mit Seitenschablone
   $sFraHtmlNach=@implode('',@file(FRA_Pfad.$sFraSchablone));
   if($p=strpos($sFraHtmlNach,'{Inhalt}')){
    $sFraHtmlVor=substr($sFraHtmlNach,0,$p); $sFraHtmlNach=substr($sFraHtmlNach,$p+8); //Seitenkopf, Seitenfuss
   }else{$sFraHtmlVor='<p style="color:#A03;">HTML-Layout-Schablone <i>'.$sFraSchablone.'</i> nicht gefunden oder fehlerhaft!</p>'; $sFraHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.FRA_Http.'fraStyle.css">'."\n\n";
  }
 }else{$bFraOK=false; echo "\n".'<p style="color:#C03;">Konfiguration <i>fraWerte'.$fraAblauf.'.php</i> nicht gefunden oder fehlerhaft!</p>';}
}else{ //Aufruf per include
 if(defined('FRA_Version')){
  define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);
 }else{ //Variablen nicht includiert
  $bFraOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>fraWerte.php</i> wurde nicht includiert!</b></p>';
 }
}

if($bFraOK){ //Konfiguration eingelesen
 if(defined('FRA_WarnMeldungen')&&!FRA_WarnMeldungen) error_reporting(E_ALL ^ E_NOTICE);
 if(phpversion()>='5.1.0') if(strlen(FRA_TimeZoneSet)>0) date_default_timezone_set(FRA_TimeZoneSet);

 define('FRA_Self',$sFraSelf);

 //Beginn der Ausgabe
 echo $sFraHtmlVor."\n".'<div class="fraBox">'."\n"; include(FRA_Pfad.'fraVersion.php');
 if(FRA_Version!=$fraVersion||strlen(FRA_Www)==0) echo "\n".'<p class="fraFehl">'.fFraTxP(FRA_TxSetupFehlt).'</p>'."\n";

 //Seiteninhalt
 echo fFraSeite();

 //Ende der Ausgabebox und evt. Seitenfuss
 echo "\n</div><!-- /Box -->\n".$sFraHtmlNach;
}
echo "\n";

function fFraSeite(){ //Seiteninhalt
 $aNutzFld=explode(';',FRA_NutzerFelder); $nNutzerFelder=count($aNutzFld); $aNutzPflicht=explode(';',FRA_NutzerPflicht);
 $sForm='LoginForm'; $sBtn=FRA_TxAnmelden; $sNaechst=''; $sPw=''; $bZurFrage=false; $bZurWertung=false; $bZumZentrum=false;
 $aW=array('0','0','','',''); $aFehl=array(); $bCaptcha=false; $bOK=false; $sId=''; $sSes=''; $Meld=''; $MTyp='Fehl'; $X=''; $bToPayPal=false;
 $sClassVersion=(phpversion()>'5.3'?'':'4');

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
  for($i=2;$i<$nNutzerFelder;$i++) if(isset($_POST['fra_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['fra_F'.$i])))); if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n));
   $aW[$i]=(FRA_Zeichensatz==0?$s:(FRA_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else $aW[$i]='';

  if($sSchritt=='pruefen'){ //Benutzerdaten pruefen/aendern
   if(($sId=$_POST['fra_Id'])&&($sPw=$_POST['fra_Pw'])){
    if(FRA_Zeichensatz>0) if(FRA_Zeichensatz==2) $sPw=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sPw); else $sPw=html_entity_decode($sPw);
    $aW[1]=(isset($_POST['fra_F1'])?$_POST['fra_F1']:'0'); $aW[2]=strtolower($aW[2]); $s=fFraEnCodeP($sPw);
    if(strlen($aW[2])<4||strlen($aW[2])>25) $aFehl[2]=true; //Benutzer
    if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Passwort
    if(!fFraIsEMailAdrP($aW[4])) $aFehl[4]=true; //eMail
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
       $aW[0]=$sId; $aW[1]=$a[1]; $a[2]=fFraDeCodeP($a[2]); $a[3]=fFraDeCodeP($a[3]); $a[4]=fFraDeCodeP($a[4]);
       for($j=5;$j<$nNutzerFelder;$j++){
        $a[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        if($aNutzFld[$j]=='GUELTIG_BIS'){
         $aW[$j]=$a[$j]; if(FRA_NutzerFrist>0&&isset($a[$j])&&$a[$j]>''&&$a[$j]<date('Y-m-d')){$a[1]='0'; $aW[1]='0';} //abgelaufen
        }
       }
       if($a!=$aW){ //veraendert
        if($a[2]==$aW[2]||!strpos($sNam,';'.fFraEnCodeP($aW[2]).';')){ //Benutzername unveraendert oder frei
         $s=$sId.';'.$a[1].';'.fFraEnCodeP($aW[2]).';'.fFraEnCodeP($aW[3]).';'.fFraEnCodeP($aW[4]);
         for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
         if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=FRA_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
         }else $Meld=str_replace('#',FRA_TxBenutzer,FRA_TxDateiRechte);
        }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
       }else{ //unveraendert
        if($a[1]=='1'){ //aktiv
         $Meld=FRA_TxNutzerOK; $MTyp='Erfo'; $sBtn=FRA_TxWeiter; $sSes=fFraSessionNr($sId);
        }else{$Meld=FRA_TxNutzerUnveraendert; $MTyp='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen'; $sBtn=FRA_TxWeiter;} //nicht aktiv
       }
       if(count($aFehl)==0) $bToPayPal=true; $sBtn='Korrigieren';
      }else $Meld=FRA_TxNutzerFalsch;
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sId.'" AND Passwort="'.$s.'"')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){ //gefunden
        $sForm='NutzerForm'; $sNaechst='pruefen';
        $aW[0]=$sId; $aW[1]=$a[1]; $a[3]=fFraDeCodeP($a[3]); $s='';
        if($a[2]!=$aW[2]) $s.=', Benutzer="'.$aW[2].'"'; if($a[3]!=$aW[3]) $s.=', Passwort="'.fFraEnCodeP($aW[3]).'"';
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
          $Meld=FRA_TxNutzerOK; $MTyp='Erfo'; $sBtn=FRA_TxWeiter; $sSes=fFraSessionNr($sId);
         }else{$Meld=FRA_TxNutzerUnveraendert; $MTyp='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen'; $sBtn=FRA_TxWeiter;} //nicht aktiv
        }
        if(count($aFehl)==0) $bToPayPal=true; $sBtn='Korrigieren';
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
    if(!fFraIsEMailAdrP($aW[4])) $aFehl[4]=true; //eMail
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
      if(!strpos($sNam,';'.fFraEnCodeP($aW[2]).';')){
       $s='Nummer_'.(++$sId).';aktiv'; for($j=2;$j<$nNutzerFelder;$j++) $s.=';'.$aNutzFld[$j]; $aD[0]=$s."\n";
       $s=$sId.';'.(FRA_Nutzerfreigabe?'1':'0').';'.fFraEnCodeP($aW[2]).';'.fFraEnCodeP($aW[3]).';'.fFraEnCodeP($aW[4]);
       for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]);
       if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".$s."\n"); fclose($f);
        $Meld=FRA_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; /* $sPw=''; */ $sPw=$aW[3]; $sBtn=FRA_TxWeiter; $sBtn='Korrigieren';
       }else{$Meld=str_replace('#',FRA_TxBenutzer,FRA_TxDateiRechte); $sId='';}
      }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true; $sId='';}
     }elseif($bSQLOpen){ //bei SQL
      if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
       $i=$rR->num_rows; $rR->close();
       if($i==0){
        $s='Benutzer,Passwort,eMail'; $t='"'.$aW[2].'","'.fFraEnCodeP($aW[3]).'","'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){$s.=',dat_'.$j; $t.=',"'.$aW[$j].'"';}
        if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabN.' (aktiv,'.$s.') VALUES("'.(FRA_Nutzerfreigabe?'1':'0').'",'.$t.')')){
         if($sId=$DbO->insert_id){$Meld=FRA_TxNutzerNeu; $MTyp='Erfo'; $sNaechst=''; /* $sPw=''; */ $sPw=$aW[3]; $sBtn=FRA_TxWeiter; $sBtn='Korrigieren';}
         else $Meld=FRA_TxSqlEinfg;
        }else $Meld=FRA_TxSqlEinfg;
       }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true; $sId='';}
      }else $Meld=FRA_TxSqlFrage;
     }//SQL
     if(!empty($sId)){
      $sMlTx=''; for($j=2;$j<$nNutzerFelder;$j++) $sMlTx.="\n".strtoupper($aNutzFld[$j]!='GUELTIG_BIS'?$aNutzFld[$j]:(FRA_TxNutzerFrist?FRA_TxNutzerFrist:$aNutzFld[$j])).': '.$aW[$j];
      if(FRA_NutzerNeuMail){
       $sLnk=date('si').$sId; $n=(int)substr(FRA_Schluessel,-2); for($k=strlen($sLnk)-1;$k>=0;$k--) $n+=(int)(substr($sLnk,$k,1));
       $sLnk=FRA_Www.'frage.php?fra_Aktion=ok'.sprintf('%02x',$n).$sLnk; $sWww=fFraWwwP();
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
     }
     if(count($aFehl)==0){$bToPayPal=true; $sNaechst='pruefen';} $sForm='NutzerForm';
    }else $Meld=FRA_TxEingabeFehl;
   }else $Meld=FRA_TxCaptchaFehl;
  }
 }//POST

 //Beginn der Ausgabe
 if($sForm=='LoginForm'){ //Loginformular

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
     oForm.elements['fra_CaptchaFrage'].value='".FRA_TxCaptchaHilfe."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".FRA_Http.FRA_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>\n";

 //neuer Nutzer
 $X.="\n".'
 <p class="fraMeld">'.fFraTxP(FRA_TxLoginNeu).'</p>
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Schritt" value="neu" />
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP(FRA_TxGewuenscht).'<div class="fraNorm">'.fFraTxP(FRA_TxBenutzername).'</div><span class="fraMini">'.fFraTxP(FRA_TxNutzerRegel).'</span></td>
   <td class="fraLogi"><input class="fraLogi" type="text" name="fra_F2" value="'.(isset($sNeuName)?fFraTxP($sNeuName):'').'" maxlength="25" /></td>
  </tr>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <tr>
   <td class="fraLogi fra15Bs capCell" style="padding-top:6px;vertical-align:top;">'.fFraTxP(FRA_TxCaptchaFeld).'</td>
   <td class="fraLogi capCell">
    <input class="fraLogi capQuest" name="fra_CaptchaFrage" type="text" value="'.fFraTxP($Cap->Type!='G'?$Cap->Question:FRA_TxCaptchaHilfe).'" />
    <div'.($bCapErr&&$sSchritt=='neu'?' class="fraFehl"':'').' style="white-space:nowrap">
     <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.FRA_Http.FRA_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
     <input class="fraLogi capAnsw" name="fra_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" style="width:10em;" size="15" />
     <input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
     <input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
     '.(FRA_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fFraTxP(str_replace('#',FRA_TxZahlenCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fFraTxP(str_replace('#',FRA_TxTextCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     '.(FRA_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fFraTxP(str_replace('#',FRA_TxGrafikCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
    </div>
   </td>
  </tr>';
 }
 $X.='
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTxP(FRA_TxAnmelden).'" title="'.fFraTxP(FRA_TxAnmelden).'" />
 </form>';

 }elseif($sForm=='NutzerForm'){ //Benutzerdaten

 if($aW[1]=='1'){$s=''; $t='Grn';}else{$s=FRA_TxNicht.' '; $t='Rot';}
 if(FRA_DSEPopUp&&(FRA_NutzerDSE1||FRA_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.FRA_DSEPopupW.',height='.FRA_DSEPopupH.',left='.FRA_DSEPopupX.',top='.FRA_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 $X.='
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Schritt" value="'.$sNaechst.'" />
 <input type="hidden" name="fra_Id" value="'.$sId.'" />
 <input type="hidden" name="fra_Pw" value="'.fFraTxP($sPw).'" />
 <table class="fraLogi" style="width:98%;max-width:480px;" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP(FRA_TxNutzerNr).'</td>
   <td class="fraLogi">'.($sId!=''?sprintf('%05d ',$sId):'').'<img src="'.FRA_Http.'punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fFraTxP($s.FRA_TxAktiv).'"><input type="hidden" name="fra_F1" value="'.$aW[1].'" />'.($aW[1]=='1'?'':' <span class="fraMini">('.fFraTxP($s.FRA_TxAktiv).')</span>').'</td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP(FRA_TxBenutzername).'*<div class="fraNorm"><span class="fraMini">'.fFraTxP(FRA_TxNutzerRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[2])&&$aFehl[2]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F2" value="'.fFraTxP($aW[2]).'" maxlength="25" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP(FRA_TxPasswort).'*<div class="fraNorm"><span class="fraMini">'.fFraTxP(FRA_TxPassRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[3])&&$aFehl[3]?' class="fraFehl"':'').'><input class="fraLogi" type="password" name="fra_F3" value="'.fFraTxP($aW[3]).'" maxlength="16" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP(FRA_TxMailAdresse).'*</td>
   <td class="fraLogi"><div'.(isset($aFehl[4])&&$aFehl[4]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F4" value="'.fFraTxP($aW[4]).'" maxlength="100" /></div></td>
  </tr>';
 for($i=5;$i<$nNutzerFelder;$i++){
  if($aNutzFld[$i]!='GUELTIG_BIS') $bNutzerFrist=false; else{$bNutzerFrist=true; if(FRA_TxNutzerFrist) $aNutzFld[$i]=FRA_TxNutzerFrist;}
  $X.='
  <tr>
   <td class="fraLogi fra15Bs">'.fFraTxP($aNutzFld[$i]).($aNutzPflicht[$i]?'*':'').'</td>
   <td class="fraLogi"><div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F'.$i.'" value="'.fFraTxP($aW[$i]).($bNutzerFrist?'" style="width:8em;" readonly="readonly':'').'" maxlength="255" /></div></td>
  </tr>';
 }
 if(FRA_NutzerDSE1) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE1?'Fehl':'Norm').'">'.fFraDSEFldP(1,$bDSE1).'</div></td></tr>';
 if(FRA_NutzerDSE2) $X.="\n".'<tr><td class="fraLogi fra15Bs" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE2?'Fehl':'Norm').'">'.fFraDSEFldP(2,$bDSE2).'</div></td></tr>';
 $X.='
  <tr><td class="fraLogi fra15Bs">&nbsp;</td><td class="fraLogi" style="text-align:right;">* <span class="fraMini">'.fFraTxP(FRA_TxPflicht).'</span></td></tr>
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTxP($sBtn).'" title="'.fFraTxP($sBtn).'" />
 </form>';
 }

 if($bToPayPal){
  $nPPSum=sprintf('%0.2f',FRA_PPAmount);
  $sInvoice=$sId.rand(1001,9999); $n=substr(FRA_Schluessel,0,2); for($i=0;$i<strlen($sInvoice);$i++) $n+=(int)substr($sInvoice,$i,1); $sInvoice=sprintf('%02x',$n).$sInvoice; // xxizzzz
  $sPP_Id=  $sId.rand(1001,9999); $n=substr(FRA_Schluessel,-2); for($i=0;$i<strlen($sPP_Id);$i++) $n+=(int)substr($sPP_Id,$i,1); $sPP_Id=sprintf('%02x',$n).$sPP_Id; // xxizzzz

  $X.='<br /><p class="fraMeld">'.FRA_PPMsg.'</p>
<form class="ppForm" name="ppForm" action="'.FRA_PPCheckout.'" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="'.FRA_PPBusiness.'" />
<input type="hidden" name="item_name" value="Freischaltung als Benutzer" />
<input type="hidden" name="item_number" value="ID: '.$sId.', '.date('d.m.y H:i').' Uhr" />
<input type="hidden" name="quantity" value="1">
<input type="hidden" name="amount" value="'.$nPPSum.'" />
<input type="hidden" name="currency_code" value="'.FRA_PPCurrency.'" />
<input type="hidden" name="invoice" value="'.$sInvoice.'" />
<input type="hidden" name="charset" value="utf-8" />
<input type="hidden" name="return" value="'.FRA_PPReturn.'?pp_Id='.$sPP_Id.'" />
<input type="hidden" name="notify_url" value="'.FRA_PPNotify.'?pp_Id='.$sPP_Id.'" />
<input type="hidden" name="cancel_return" value="'.FRA_PPCancel.'?pp_Id='.$sPP_Id.'" />
<input type="hidden" name="rm" value="2" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="country" value="DE" />
<input type="hidden" name="lc" value="DE" />
<table class="fraLogi" style="width:98%;max-width:480px;" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td class="fraLogi" style="white-space:nowrap;">Ihr PayPal-Konto</td>
  <td class="fraLogi" style="width:75%"><input class="fraLogi" type="email" name="email" required="required" value="" style="width:98%" /></td>
 </tr>
 <tr>
  <td class="fraLogi" style="white-space:nowrap;">PayPal-Preis</td>
  <td class="fraLogi" style="width:75%">&nbsp;'.str_replace('.',',',$nPPSum).'&nbsp;&euro; &nbsp; <img src="PayPal.png" width="65" height="19" border="0" alt="PayPal"></td>
 </tr>
</table>
<input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif);background-size:100% 100%;width:175px;height:24px;" value="'.fFraTxP(FRA_PPBtn).'" title="'.fFraTxP(FRA_PPBtn).'" />
</form>
';

  if(FRA_PPLogging){ // Logfile schreiben
   $sNow=date('d.m.Y H:i:s'); $s="\n".$sNow.' Checkout '.$sId;
   if($f=fopen(FRA_Pfad.FRA_PPLogFile,'a')){fwrite($f,$s); fclose($f);}
  }
 }

 if(!isset($FehlSQL)){
  return "\n".' <p class="fra'.$MTyp.'">'.fFraTxP($Meld)."</p>\n".$X."\n";
 }else return "\n".' <p class="fraFehl">'.fFraTxP($FehlSQL)."</p>\n".$X."\n";
}

function fFraIsEMailAdrP($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}

function fFraSessionNr($sId){
 $n=(int)substr(FRA_Schluessel,-2); $sSes=rand(10,99).sprintf('%05d',$sId).((time()>>8)+round(FRA_MaxSessionZeit/4)); // 4-Minuten Intervalle
 for($i=strlen($sSes)-1;$i>=0;$i--) $n+=(int)substr($sSes,$i,1); return dechex($n).$sSes;
}

function fFraDSEFldP($z,$bCheck=false){
 $s='<a class="fraText" href="'.FRA_DSELink.'"'.(FRA_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(FRA_DSETarget?' target="'.FRA_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fFraTxP($z!=2?FRA_TxDSE1:FRA_TxDSE2)));
 return '<input class="fraCheck" type="checkbox" name="fra_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

function fFraEnCodeP($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fFraDeCodeP($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fFraWwwP(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; elseif(isset($_SERVER['SERVER_ADDR'])) $s=$_SERVER['SERVER_ADDR']; else $s='localhost';
 return $s;
}
function fFraTxP($sTx){ //TextKodierung
 if(FRA_Zeichensatz<=0) $s=$sTx; elseif(FRA_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8//TRANSLIT',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
?>