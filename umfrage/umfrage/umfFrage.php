<?php
if(!function_exists('fUmfSeite') ){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfFrage(true);}
}

function fUmfFrage($bDirekt){ //Seiteninhalt
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false; $bNtz=false; $bTln=false; $nNr=1; $bAktivCodeErr=false;

 if($sSes=($bDirekt?UMF_Session:UMF_NeuSession)){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $bSes=true; $bNtz=(substr($sSes,4,1)!='9'); $bTln=!$bNtz;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(UMF_Nutzerverwaltung=='vorher'||UMF_Registrierung=='vorher') $Meld=UMF_TxNutzerLogin;

 //Captcha behandeln
 $sCapTyp=(isset($_POST['umf_CaptchaTyp'])?$_POST['umf_CaptchaTyp']:UMF_CaptchaTyp); $bCapOk=false; $bCapErr=false;
 if($bCaptcha=UMF_Captcha&&(!(UMF_Nutzerzwang||UMF_Registrierung))){
  require_once(UMF_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
  if(isset($_POST['umf_CaptchaCode'])){
   if($Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);}
 }

 $sAntwort=UMF_Antwort; $nAntwAnzahl=max(20,ADU_AntwortZahl); $aChk=array(0); for($i=1;$i<=$nAntwAnzahl;$i++) $aChk[$i]='';
 if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  if(isset($_POST['umf_Frage'])) $nNr=max((int)$_POST['umf_Frage'],1);
  if(isset($_POST['umf_Antw'])&&($aA=$_POST['umf_Antw'])){ //gerade eingegebene Antwort
   $t=''; for($i=0;$i<count($aA);$i++){$j=$aA[$i]; $t.=','.$j; if($bCapErr) $aChk[$j]=' checked="checked"';}
   if(!$bCaptcha||$bCapOk){
    if(strlen($t)>0){$sAntwort.=(strlen($sAntwort)>0?';':'').(isset($_POST['umf_FrgNr'])?(int)$_POST['umf_FrgNr']:0).':'.substr($t,1); $nNr++;}
   }else $Meld=UMF_TxCaptchaFehl;
  }elseif(!defined('UMF_NeuSession')) if(empty($Meld)) $Meld=str_replace('#',$nNr,UMF_TxAntwortFehlt);
 }

 $aF=NULL; $nFragen=0; $sUmfrage=(defined('UMF_BUmfrage')?UMF_BUmfrage:UMF_Umfrage); //Frage holen
 $bUnscharf=UMF_UmfrUnscharf; if($sUmfrage){$s=substr(constant('UMF_Umfr'.$sUmfrage),0,1); if($s=='1') $bUnscharf=true; elseif($s==='0') $bUnscharf=false;}
 if(!UMF_SQL){
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); array_shift($aD);
  foreach($aD as $s){
   $a=explode(';',$s,4);
   if($a[1]>'0'&&(!$sUmfrage||$a[2]==$sUmfrage||($bUnscharf&&$a[2]==''))){
    if(++$nFragen==$nNr) $aF=explode(';',rtrim($s));
  }}
 }else{ //SQL
  if($DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa)){
   if(!mysqli_connect_errno()){if(defined('UMF_SqlCharSet')&&UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
   if($DbO){
    if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabF.' WHERE aktiv="1"'.(!$sUmfrage?'':' AND(Umfrage="'.$sUmfrage.'"'.($bUnscharf?' OR Umfrage=""':'').')'))){
     if($a=$rR->fetch_row()) $nFragen=$a[0]; $rR->close();
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' WHERE aktiv="1"'.(!$sUmfrage?'':' AND(Umfrage="'.$sUmfrage.'"'.($bUnscharf?' OR Umfrage=""':'').')').' LIMIT '.($nNr-1).',1')){
      $aF=$rR->fetch_row(); $rR->close();
     }else $Meld=UMF_TxSqlFrage;
    }else $Meld=UMF_TxSqlFrage;
   }else $Meld=UMF_TxSqlDaBnk;
  }else $Meld=UMF_TxSqlVrbdg;
 }

 if(UMF_NutzerMitCode&&$bNtz||UMF_TeilnehmerMitCode&&$bSes&&$bTln){ //Aktivcode
  $sACode=(isset($_POST['umf_Code'])?$_POST['umf_Code']:(isset($_GET['umf_Code'])?$_GET['umf_Code']:'#'));
  if($sUmfrage){ //Umfrage mit Code
   $a=explode(';',constant('UMF_Umfr'.$sUmfrage));
   if($bNtz&&$a[1]||$bTln&&$a[2]) if($sACode!=$a[4]){$bAktivCodeErr=true; define('UMF_AktivCodeErr',true);}
  }elseif(UMF_StdUmfrCode>0&&$sACode!=UMF_StdUmfrCode){$bAktivCodeErr=true; define('UMF_AktivCodeErr',true);} //Standardumfrage
 }else $sACode='';

 if(!$bAktivCodeErr&&(!$bSes&&UMF_Nutzerverwaltung!='vorher'&&UMF_Registrierung!='vorher'||$bSes)){ //Session OK
  if(is_array($aF)&&count($aF)>2){ //Frage gefunden, Formularausgabe
   $sBtn="\n".'<div class="umfScha"><input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxAbstimmen).'" /></div>';
   $sZnr="\n".' <div class="umfFrNr">'.fUmfTx(UMF_TxFrage).' '.str_replace('#N',sprintf('%'.UMF_NummerStellen.'d',$nNr),str_replace('#I',sprintf('%'.UMF_NummerStellen.'d',$aF[0]),str_replace('#M',sprintf('%'.UMF_NummerStellen.'d',$nFragen),UMF_NummernText))).'</div>';
   $sAm1=($aF[5]?"\n".' <div class="umfAnmk">'.fUmfBB(fUmfTx($aF[5])).'</div>':'');
   $sAm2=($aF[6]?"\n".' <div class="umfAnmk">'.fUmfBB(fUmfTx($aF[6])).'</div>':'');

   $X="\n".'<div class="umfTxBl"><!-- TextBlock -->'; //TextBlock Anfang
   if(UMF_ZeigeBemerkung=='oben2') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben2') $X.=$sAm2;
   if(UMF_ZeigeNummer=='oben') $X.=$sZnr;
   if(UMF_ZeigeBemerkung=='oben3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben3') $X.=$sAm2;
   $X.="\n".' <div class="umfFrag">'.fUmfBB(fUmfTx(trim(UMF_TxVorFrage.' '.$aF[3]))).'</div>';
   if(UMF_ZeigeBemerkung=='oben4') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben4') $X.=$sAm2;
   $i=0; if(UMF_RadioButton) $s='radio'; else $s='checkbox';
   while(++$i<=$nAntwAnzahl&&($t=(isset($aF[6+$i])?$aF[6+$i]:''))){//Antwortenschleife
    $X.="\n".' <div class="umfAntw" onclick="toggleInp('.$i.')"><input class="umfAntw" id="umfAntw'.$i.'" type="'.$s.'" name="umf_Antw[]" value="'.$i.'"'.$aChk[$i].' onclick="clickInp()" />&nbsp;'.fUmfBB(fUmfTx($t)).'</div>';
   }
   if(UMF_ZeigeBemerkung=='unten1') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten1') $X.=$sAm2;
   if(UMF_ZeigeNummer=='unten') $X.=$sZnr;
   if(UMF_ZeigeBemerkung=='unten2') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten2') $X.=$sAm2;
   if($bCaptcha) $X.="\n".' <div class="umfCapt">
    <div>'.fUmfTx(UMF_TxCaptchaFeld).':</div>
    <div>
     <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
     <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
     <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
     <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
     <div'.($bCapErr?' class="umfFehl"':'').'>
      <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.UMF_Url.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
      <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
     </div>
    </div>
   </div>';
   if(UMF_Layout==0) {
    if(UMF_ZeigeBemerkng2=='oben1'&&$t=$aF[6]) $X=$sAm2.$X; if(UMF_ZeigeBemerkung=='oben1'&&$t=$aF[5]) $X=$sAm1.$X;
    $X.="\n ".$sBtn;
    if(UMF_ZeigeBemerkung=='unten3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten3') $X.=$sAm2;
   }
   $X.="\n</div><!-- /TextBlock -->"; //TextBlock Ende

   if(UMF_Layout>0){ // BildLayout Anfang
    if($sBld=$aF[4]) $sBld=UMF_Bilder.$sBld; elseif(UMF_BildErsatz) $sBld=UMF_Bilder.UMF_BildErsatz;
    if($sBld){
     $a=@getimagesize(UMF_Pfad.$sBld);
     $sBld='<img class="umfBild" src="'.UMF_Url.$sBld.'" '.$a[3].' border="0" alt="'.fUmfTx(UMF_TxFrage).'-'.$nNr.'" title="'.fUmfTx(UMF_TxFrage).'-'.$nNr.'" />';
    }else $sBld='&nbsp;';
    if(UMF_Layout==1){ // Bild links
     $X="\n".'<div class="umfBldL"><!-- Bild links -->'."\n ".$sBld."\n</div><!-- /Bild -->\n".'<div class="umfTBlR"><!-- rechter Block -->'.$X."\n</div><!-- /rechter Block -->\n".'<div class="umfClrB"></div><!-- Floaten aufheben -->'.$sBtn;
    }elseif(UMF_Layout==2){ // Bild rechts
     $X="\n".'<div class="umfTBlL"><!-- linker Block -->'.$X."\n".'</div><!-- /linker Block -->'."\n".'<div class="umfBldR"><!-- Bild rechts -->'."\n ".$sBld."\n".'</div><!-- /Bild -->'."\n".'<div class="umfClrB"></div><!-- Floaten aufheben -->'.$sBtn;
    }else{ // Bild oben
     $X="\n".'<div class="umfBldO"><!-- Bild -->'."\n ".$sBld."\n".'</div><!-- /Bild -->'.$X.$sBtn;
    }
    if(UMF_ZeigeBemerkng2=='oben1'&&$t=rtrim($aF[6])) $X=$sAm2.$X; if(UMF_ZeigeBemerkung=='oben1'&&$t=rtrim($aF[5])) $X=$sAm1.$X;
    if(UMF_ZeigeBemerkung=='unten3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten3') $X.=$sAm2;
    $X="\n".'<div class="umfGsmt"><!-- Gesamt -->'.$X."\n</div><!-- /Gesamt -->\n";
   }

   if(empty($Meld)){$Meld=str_replace('#',$nNr,UMF_TxBeantworten); $MTyp='Meld';}
   $Meld='<p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n";
   $X=$Meld.'<form name="umfForm" class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="frage" />
   <input type="hidden" name="umf_Session" value="'.$sSes.'" />'.(!$sUmfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.$sUmfrage.'" />').(empty($sACode)?'':'
   <input type="hidden" name="umf_Code" value="'.$sACode.'" />').'
   <input type="hidden" name="umf_Frage" value="'.$nNr.'" />
   <input type="hidden" name="umf_FrgNr" value="'.$aF[0].'" />
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n  ".UMF_Hidden).$X.'</form>'.fJSInpCode();
   if($bCaptcha) $X.=fJSCapCode();
  }else{ //keine Frage mehr gefunden
   if(strlen($sAntwort)>0){ //fertig, eintragen
    if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
    $nUmfGespeichert=0;
    if(isset($_SERVER['HTTP_USER_AGENT'])) $sIP=' '.str_replace(';',',',$_SERVER['HTTP_USER_AGENT']);
    elseif(isset($_SERVER['ALL_HTTP'])){
     $sIP=substr(strstr($_SERVER['ALL_HTTP'],'HTTP_USER_AGENT'),16,500); if($p=strpos($sIP,'HTTP_')) $sIP=substr($sIP,0,$p);
     $sIP=' '.str_replace(';',',',trim(str_replace("\n",' ',str_replace("\r",'',$sIP))));
    }else $sIP='';
    $sIP=(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:(isset($_ENV['REMOTE_ADDR'])?$_ENV['REMOTE_ADDR']:'')).$sIP;
    if($sIP!=''||UMF_Anonym){ //IP vorhanden
     $aD=array();
     if(!UMF_SQL){ //Text-Datei
      $aE=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nEs=count($aE); $aD[0]=rtrim($aE[0]);
      for($i=1;$i<$nEs;$i++){$s=rtrim($aE[$i]); $nP=strpos($s,';'); $aD[(int)substr($s,0,$nP)]=substr($s,++$nP);}
     }elseif($DbO){ //SQL
      if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabE.' ORDER BY Nummer')){
       while($a=$rR->fetch_row()) $aD[(int)$a[0]]=$a[1]; $rR->close();
      }else $Meld=UMF_TxSqlFrage;
     }
     if(isset($aD[0])) $aIP=explode(';',$aD[0]); else $aIP=array('IP');
     if(UMF_IPAdressen<=0||!in_array($sIP,$aIP)){ //IP akzeptiert
      $aIP[0]=$sIP; $k=max(min(UMF_IPAdressen,count($aIP)),1); $sIP='IP'; for($i=0;$i<$k;$i++) $sIP.=';'.$aIP[$i]; $aD[0]=$sIP;
      $aA=explode(';','#;'.$sAntwort); $nAws=count($aA); $sMlTx='';
      for($i=1;$i<$nAws;$i++){
       $a=explode(':',$aA[$i]); $nFrg=(int)$a[0]; $sA=(isset($a[1])?$a[1]:'0');
       if(isset($aD[$nFrg])) $aP=explode(';',$aD[$nFrg]); else $aP=array();
       $a=explode(',',$sA); foreach($a as $p) $aP[--$p]=(isset($aP[$p])?$aP[$p]+1:1);
       $s=''; for($j=0;$j<$nAntwAnzahl;$j++) $s.=';'.(isset($aP[$j])?$aP[$j]:'0'); $aD[$nFrg]=substr($s,1);
       $sMlTx.="\n".UMF_TxFrage.'-'.$nFrg.': '.$sA;
      }
      if(!UMF_SQL){ //Ergebnis eintragen
       $s=$aD[0]; foreach($aD as $k=>$v) $aD[$k]=$k.';'.$v."\n"; $aD[0]=$s."\n"; ksort($aD);
       if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Ergebnis,'w')){
        fwrite($f,implode('',$aD)); fclose($f); $Meld=UMF_TxAbgestimmt; $MTyp='Erfo'; $nUmfGespeichert=1;
       }else $Meld=str_replace('#',UMF_Daten.UMF_Ergebnis,UMF_TxDateiRechte);
      }elseif($DbO){ //SQL
       if(!$DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="'.str_replace('"','\"',rtrim($aD[0])).'" WHERE Nummer=0')) $Meld=UMF_TxSqlEinfg;
       foreach($aD as $k=>$v) if($k!=0){
        if((!($DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="'.$v.'" WHERE Nummer="'.$k.'"')))||$DbO->affected_rows<1)
         if((!($DbO->query('INSERT IGNORE INTO '.UMF_SqlTabE.' (Nummer,Inhalt) VALUES("'.$k.'","'.$v.'")')))||$DbO->affected_rows<1) $Meld=UMF_TxSqlEinfg;
       }
       if($Meld!=UMF_TxSqlEinfg){$Meld=UMF_TxAbgestimmt; $MTyp='Erfo'; $nUmfGespeichert=1;}
      }
      if(UMF_NutzerUmfragen&&$bNtz){ // Zuweisungen runterzaehlen
       if(!UMF_SQL){
        $aZ=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nZhl=count($aZ); $s=(int)$sNId.';'; $l=strlen($s);
        for($j=1;$j<$nZhl;$j++) if(substr($aZ[$j],0,$l)==$s){ //Nutzer gefunden
         $sZw='#;'.rtrim(substr($aZ[$j],$l)).';'; $nZz=$j; break;
       }}elseif($DbO){ //bei SQL
        if($rR=$DbO->query('SELECT Nummer,Umfragen FROM '.UMF_SqlTabZ.' WHERE Nummer="'.$sNId.'"')){
         if($aZ=$rR->fetch_row()) $sZw='#;'.$aZ[1].';'; $rR->close(); //Nutzerzuordnung gefunden
       }}
       if(isset($sZw)&&($p=strpos($sZw,';'.($sUmfrage?$sUmfrage:'0').':'))){ //TestZuordnungen
        $p+=3; $w=substr($sZw,$p); $w=substr($w,0,strpos($w,';'));
        if(strpos($w,'x')){ // herunterzaehlen
         $sZw=substr_replace($sZw,sprintf('%0d',max((int)$w-1,0)),$p,strlen($w)-1); $sZw=substr($sZw,2,-1);
         if(!UMF_SQL){ //Textdateien
          $aZ[$nZz]=(int)$sNId.';'.$sZw."\n";
          if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Zuweisung,'w')){
           fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ)))."\n"); fclose($f);
         }}elseif($DbO){ //bei SQL
          $DbO->query('UPDATE IGNORE '.UMF_SqlTabZ.' SET Tests="'.$sZw.'" WHERE Nummer="'.$sNId.'"');
       }}}
      }

      if(UMF_FertigMail){ // ToDo: zu zeitig, keine Nutzerdaten
       require_once(UMF_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(UMF_Empfaenger);
       $s=UMF_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); $Mailer->Subject=UMF_TxFertigMlBtr;
       if(strlen(UMF_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(UMF_EnvelopeSender);
       $Mailer->Text=str_replace('#',trim($sMlTx."\n\n".$Meld),str_replace('\n ',"\n",UMF_TxFertigMlTxt));
       $Mailer->Send();
      }
     }else $Meld=UMF_TxGleicheAdresse;

     if(strlen(UMF_Session)){ //Teilnahme eintragen
      if(UMF_TeilnehmerLog&&substr($sSes,4,1)=='9'){include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln($nUmfGespeichert,'T',$sAntwort,$sSes,$DbO);}
      elseif(UMF_NutzerLog&&substr($sSes,4,1)!='9'){include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln($nUmfGespeichert,'N',$sAntwort,$sSes,$DbO);}
     }elseif(UMF_GastLog&&UMF_Registrierung!='nacher'&&UMF_Nutzerverwaltung!='nachher'){
      include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln($nUmfGespeichert,'G',$sAntwort,'',$DbO);
     }
    }else $Meld=UMF_TxAnonymeAdresse;

    define('UMF_FertigAntwort',$sAntwort);
    if(UMF_Nutzerverwaltung=='nachher'&&strlen(UMF_Session)==0){
     include UMF_Pfad.'umfLogin.php'; define('UMF_Gespeichert',$nUmfGespeichert); $X=fUmfLogin(false);
    }elseif(UMF_Registrierung=='nachher'&&strlen(UMF_Session)==0){
     include UMF_Pfad.'umfErfassen.php'; define('UMF_Gespeichert',$nUmfGespeichert); $X=fUmfErfassen(false);
    }elseif(UMF_NachAbstimmen=='Fertig'){ //zum Fertigtext
     if(!UMF_FertigHtml){
      if(strlen(UMF_TxFertigText)>0) $X='  <p>'.fUmfBB(fUmfTx(UMF_TxFertigText))."</p>\n";
      if(strlen(UMF_GrafikLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').($sUmfrage?'&amp;umf_Umfrage='.$sUmfrage:'').'">'.fUmfTx(UMF_GrafikLink)."</a> ]</span>\n";
      if(strlen(UMF_ZentrumLink)>0&&$sSes) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').'">'.fUmfTx(UMF_ZentrumLink)."</a> ]</span>\n";
      if(strlen(UMF_NeuAnfangLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.'">'.fUmfTx(UMF_NeuAnfangLink)."</a> ]</span>\n";
      if(strlen($X)>0) $X="\n".' <div class="umfFrtg">'."\n".$X." </div>";
     }else{
      if($X=@implode('',file(UMF_Pfad.'umfFertig.inc.htm'))){
       $X=str_replace('{Grafik}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').($sUmfrage?'&amp;umf_Umfrage='.$sUmfrage:''),$X);
       $X=str_replace('{Zentrum}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').($sUmfrage?'&amp;umf_Umfrage='.$sUmfrage:''),$X);
       $X=str_replace('{Neuanfang}',UMF_Self,$X);
      }else $X=' <p class="umfFehl">Schablone <i>umfFertig.inc.htm</i> fehlt!</p>'."\n";
     }
     $X=(!empty($Meld)?' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n":'').$X;
    }else{include UMF_Pfad.'umfGrafik.php'; $X=fUmfGrafik($Meld,$MTyp);} //zur Grafik
   }else{ //Fehler, keine Frage vorhanden
    $X=' <p class="umfFehl">'.fUmfTx(UMF_TxFrageFehlt)."</p>\n";
    if($Meld) $X.=' <p class="umfFehl">'.fUmfTx($Meld)."</p>\n";
   }
  } //keine Frage mehr
 }elseif($bAktivCodeErr){ // Aktivcode falsch
  if($bNtz){include UMF_Pfad.'umfZentrum.php'; $X=fUmfZentrum(true);}
  elseif($bTln){include UMF_Pfad.'umfAuswahl.php'; $X=fUmfAuswahl(true);}
  else $X=' <p class="umfFehl">'.fUmfTx(UMF_TxAktivCodeNoetig)."</p>\n";
 }else $X=' <p class="umfFehl">'.fUmfTx($Meld)."</p>\n"; //Sessionsproblem
 return $X;
}

function fJSInpCode(){
 return "
<script type=\"text/javascript\">
 var iChkClicked=false;
 function clickInp(){iChkClicked=true;}
 function toggleInp(nId){
  if(!iChkClicked){var iChk=document.getElementById('umfAntw'+nId); iChk.checked=!iChk.checked;}
  iChkClicked=false;
 }
</script>";
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