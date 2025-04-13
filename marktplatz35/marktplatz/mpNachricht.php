<?php
function fMpSeite(){
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 $Meld=''; $MTyp='Fehl'; $aR=NULL; $H=''; $U=strtoupper(MP_TxSegment).': '.MP_SegName;
 $sEml=''; $sHid=''; $sId=''; $sNutzerEml=''; $bEml=true; $bCap=true; $bOK=true; $bOkD=false; $bDo=true;

 //Captcha behandeln
 if($mpCaptcha=MP_Captcha){
  $sCapTyp=(isset($_POST['mp_CaptchaTyp'])?$_POST['mp_CaptchaTyp']:MP_CaptchaTyp); $bCapOk=false; $bCapErr=false;
  require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
  if($_SERVER['REQUEST_METHOD']=='POST'){
   $sCap=$_POST['mp_CaptchaFrage']; $sCap=(MP_Zeichensatz<=0?$sCap:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['mp_CaptchaAntwort'],$_POST['mp_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $bOK=false;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(MP_CaptchaTxFarb,MP_CaptchaHgFarb);}
 }

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Struktur holen
 $nFelder=0; $aStru=array(); $aMpFN=array(); $aMpFT=array();
 $aMpDF=array(); $aMpND=array(); $aMpZS=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
 if(!MP_SQL){ //Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.MP_Segment.'"')){
   $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(count($aStru)>1){
  $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
  if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
  $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
  $aMpDF=explode(';',rtrim($aStru[7])); $aMpDF[0]=substr($aMpDF[0],14,1);
  $aMpND=explode(';',rtrim($aStru[8])); $aMpND[0]=substr($aMpND[0],14,1);
  $aMpZS=explode(';',rtrim($aStru[9])); $aMpZS[0]='';
  $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]=''; $aMpAW[1]='';
  $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
  $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
 }

 //variable Daten holen
 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if(isset($_GET['mp_Nummer'])){
   $sId=sprintf('%d',$_GET['mp_Nummer']); reset($_GET);
   foreach($_GET as $k=>$v) if(substr($k,0,3)=='mp_') $sHid.='<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
  }
 }else{ //POST
  if(isset($_POST['mp_Nummer'])){
   $sId=sprintf('%d',$_POST['mp_Nummer']); reset($_POST);
   foreach($_POST as $k=>$v) if(substr($k,0,3)=='mp_'&&substr($k,3,1)!='b'&&substr($k,3,3)!='Cap'&&substr($k,3,3)!='DSE'){
    $sHid.='<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
   }else{
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($k=='mp_bEml'){
     if($sEml=$s){
      if(!fMpIsEMailAdr($s)){$bEml=false; $bOK=false;}
     }else{$bEml=false; $bOK=false;}
 }}}}

 $nSeite=(isset($_GET['mp_Seite'])?(int)$_GET['mp_Seite']:1);
 if($sId>''){//Inseratenummer
  if($sSes=MP_Session){ //Nutzer-eMail holen
   $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0; $bSesOK=false;
   if((time()>>6)<=$nTm){ //nicht abgelaufen
    if(!MP_SQL){
     $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
      if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
       $bSesOK=true; $aN=explode(';',rtrim($aD[$i])); $sNutzerEml=fMpDeCode($aN[5]);
      }break;
     }
     if(!$bSesOK) $Meld=MP_TxSessionUngueltig;
    }elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
      if($rR->num_rows>0){
       $bSesOK=true; $aN=$rR->fetch_row(); $sNutzerEml=$aN[5];
      }else $Meld=MP_TxSessionUngueltig;
      $rR->close();
   }}}else $Meld=MP_TxSessionZeit;
  }

  if(!MP_SQL){ //Inseratedaten holen
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $aR=explode(';',rtrim($aD[$i])); if($sId==$aR[0]&&$aR[1]=='1'){array_splice($aR,1,1); $bOkD=true; break;}
   }
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'" AND online="1"')){
    if($aR=$rR->fetch_row()){array_splice($aR,1,1); $bOkD=true;} $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }
  if($bOkD){
   $sAblaufDat=$aR[1]; $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='9';
   if(MP_SuchArchiv) if(isset($_GET['mp_Archiv'])){$sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';} //Archivsuche
   if($sAblaufDat<$sIntervallAnfang||$sAblaufDat>$sIntervallEnde){$bOkD=false; $Meld=MP_TxNummerUngueltig;}
  }else $Meld=MP_TxNummerUngueltig;

  if($bOkD){ //Inserate vorhanden
   if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
   $nFarb=1; //Inseratedetails aufbereiten
   $H.="\n".'<div class="mpTabl">';
   for($i=1;$i<$nFelder;$i++){
    $t=$aMpFT[$i]; $u='';
    if($aMpDF[$i]>0&&$t!='p'&&$t!='c'&&substr($aMpFN[$i],0,5)!='META-'&&$aMpFN[$i]!='TITLE'){
     if($s=str_replace('`,',';',$aR[$i])){$u=$s;
      switch($t){
       case 't': $s=fMpBB(fMpDt($s)); $u=@strip_tags($s); break; //Text
       case 'm': if(MP_BenachrMitMemo){$s=fMpBB(fMpDt($s)); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
       case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie so lassen
       case 'd': case '@': //Datum
        $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
        switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
         case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
         case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
        }
        $s=$s1.$v.$s2.$v.$s3; $u=$s; break;
       case 'z': $u=$s.' '.MP_TxUhr; $s.=' '.fMpTx(MP_TxUhr); break; //Uhrzeit
       case 'w': //Waehrung
        if($s>0||!MP_PreisLeer){
         $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
         if(MP_Waehrung){$u=$s.' '.str_replace('&#8364;','EUR',MP_Waehrung); $s.='&nbsp;'.MP_Waehrung;}
        }else if(MP_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
        break;
       case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
        if($s=='J'||$s=='Y'){$s=fMpTx(MP_TxJa); $u=MP_TxJa;}elseif($s=='N'){$s=fMpTx(MP_TxNein); $u=MP_TxNein;}
        break;
       case 'n': case '1': case '2': case '3': case 'r': //Zahl
        if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $u=$s;
        break;
       case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); $u=$s; break; //Zaehlnummer
       case 'l': //Link
        $aI=explode('|',$s); $s=$aI[0]; $u=$s;
        $v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMpDt($s).'" alt="'.fMpDt($s).'" /> ';
        $s='<a class="mpText" title="'.fMpDt($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="_blank">'.$v.(MP_DetailLinkSymbol?'':fMpDt(isset($aI[1])?$aI[1]:$s)).'</a>';
        break;
       case 's': $u=$s; //Symbol
       $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
       $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
       $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.fMpDt($u).'" alt="'.fMpDt($u).'" />';
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $u=MP_Url.$s; $w=fMpDt(substr($s,strpos($s,'-')+1,-4));
       $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.$w.'" alt="'.$w.'" />';
       break;
      case 'f': //Datei
       $u=MP_Url.MP_Bilder.$sId.'~'.$s; $s='<a href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.fMpDt($s).'</a>'; break;
       case 'u': //Benutzer
        if($nId=(int)$s){
         $s=MP_TxAutorUnbekannt;
         if(!MP_SQL){ //Textdaten
          $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
          for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
           $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
           if(!$s=$aN[MP_NutzerBenachrFeld]) $s=MP_TxAutorUnbekannt; elseif(MP_NutzerBenachrFeld<5&&MP_NutzerBenachrFeld) $s=fMpDeCode($s);
           break;
          }
         }elseif($DbO){ //SQL-Daten
          if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
           $aN=$rR->fetch_row(); $rR->close();
           if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[MP_NutzerBenachrFeld]) $s=MP_TxAutorUnbekannt;}
           else $s=MP_TxAutorUnbekannt;
         }}
        }else $s=MP_TxAutor0000; $u=$s;
        $s=fMpDt($s); break;
       default: {$s=''; $u='';}
      }//switch
     }
     if(strlen($u)>0) $U.="\n".strtoupper($aMpFN[$i]).': '.$u;
     if(strlen($s)>0){
      $H.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
      $H.="\n".' <div class="mpTbSpi">'.fMpTx($aMpFN[$i]).'</div>';
      $H.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
     }
    }
   }
   $H.="\n</div>\n";

   if($_SERVER['REQUEST_METHOD']=='POST'){
    if($bOK&&!empty($sEml)){ //Eintragen
     if($sEml==$sNutzerEml||fMpEmlBekannt($sEml,$DbO)){//eintragen
      if($aR[1]>date('Y-m-d')){
       if(!MP_SQL){
        $aD=file(MP_Pfad.MP_Daten.MP_Benachr); $aD[0]='#Inserat;eMail'."\n"; $aD[]=$sId.$sSegNo.';'.fMpEnCode($sEml);
        sort($aD); for($i=count($aD)-1;$i>0;$i--) $aD[$i]=rtrim($aD[$i])."\n";
        if(file_exists(MP_Pfad.MP_Daten.MP_Benachr)&&is_writable(MP_Pfad.MP_Daten.MP_Benachr)&&($f=@fopen(MP_Pfad.MP_Daten.MP_Benachr,'w'))){ //Benachr. neu schreiben
         fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f); $bDo=false;
        }else if($Meld=='') $Meld=str_replace('#',MP_Daten.MP_Benachr,MP_TxDateiRechte);
       }elseif($DbO){ //SQL
        if($DbO->query('INSERT IGNORE INTO '.MP_SqlTabB.' (inserat,email) VALUES("'.$sId.$sSegNo.'","'.$sEml.'")')) $bDo=false;
        else if($Meld=='') $Meld=MP_TxSqlEinfg;
       }
       if(!$bDo){
        $MTyp='Erfo'; $Meld=MP_TxBenachrErfo; if($mpCaptcha){$Cap->Delete(); $mpCaptcha=false;} //Captcha loeschen
        if(MP_BenachrOkMail){
         require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
         if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
         $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $sWww=fMpWww();
         $Mailer->SetFrom($s,$t); if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
         $Mailer->Subject=str_replace('#A',$sWww,MP_TxBenachrOkBtr);
         $Mailer->Text=str_replace('#D',$U,str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('\n ',"\n",MP_TxBenachrOkTxt))));
         $Mailer->Send();
        }
       }
      }else $Meld=MP_TxBenachrVorbei;
     }else{//erst freischalten
      if(MP_FreischaltNeuMail){
       if(isset($_POST['mp_NeuAdr'])&&$_POST['mp_NeuAdr']>'0'){
        srand((double)microtime()*1000000); $sCod=round(time()>>8).rand(1000,9999); $nAlt=0;
        for($i=0;$i<11;$i++) $nAlt+=substr($sCod,$i,1); $sCod.=$nAlt; $nAlt=round((time()-1209600)>>8); //14Tage
        if(!MP_SQL){
         $aD=file(MP_Pfad.MP_Daten.MP_MailAdr); $nSaetze=count($aD);
         for($i=1;$i<$nSaetze;$i++) if($n=strpos($aD[$i],';')) if((int)substr($aD[$i],0,7)<$nAlt) $aD[]=''; //altes raus
         $aD[0]="#eMail\n".$sCod.';'.fMpEnCode(strtolower($sEml))."\n";
         if(file_exists(MP_Pfad.MP_Daten.MP_MailAdr)&&is_writable(MP_Pfad.MP_Daten.MP_MailAdr)&&($f=@fopen(MP_Pfad.MP_Daten.MP_MailAdr,'w'))){ //Adressen neu schreiben
          fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f);
         }else{$bOK=false; if($Meld=='') $Meld=str_replace('#',MP_Daten.MP_MailAdr,MP_TxDateiRechte);}
        }elseif($DbO){ //SQL
         $DbO->query('DELETE FROM '.MP_SqlTabM.' WHERE email LIKE "%;%" AND email<"'.fMpDt($nAlt).'"'); //altes raus
         if(!$DbO->query('INSERT IGNORE INTO '.MP_SqlTabM.' (email) VALUES("'.$sCod.';'.fMpDt(strtolower($sEml)).'")')){$bOK=false; if($Meld=='') $Meld=MP_TxSqlEinfg;}
        }
        if($bOK){
         $Meld=MP_TxNutzerSend; $MTyp='Erfo'; $bDo=false; $sWww=fMpWww();
         if($mpCaptcha){$Cap->Delete(); $mpCaptcha=false;} //Captcha loeschen
         $sLnk=(MP_BenachrLink==''?MP_Url.'marktplatz.php?mp_Aktion=on'.$sCod:MP_BenachrLink.(!strpos(MP_BenachrLink,'?')?'?':'&amp;').'mp_Aktion=on'.$sCod);
         if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')==false) $sLnk=fMpUrl().$sLnk;
         require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
         if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
         $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
         $Mailer->SetFrom($s,$t); if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
         $Mailer->Text=str_replace('#L',$sLnk,str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('\n ',"\n",MP_TxBenachrUnbkTxt))));
         $Mailer->Subject=str_replace('#A',$sWww,MP_TxBenachrUnbkBtr); $Mailer->Send();
        }
       }else{
        $Meld=str_replace('#N',$sEml,MP_TxBenachrUnbekannt); $sHid.='<input type="hidden" name="mp_NeuAdr" value="1" />'."\n";
       }
      }else $Meld=str_replace('#N',$sEml,MP_TxBenachrUnmoegl);
     }
    }else $Meld=MP_TxEingabeFehl;
   }else{//GET
    $Meld=MP_TxBenachrMeld; $MTyp='Meld';
    if(!empty($sNutzerEml)) $sEml=$sNutzerEml; //vorbelegen
   }
  }else if(empty($Meld)) $Meld=str_replace('#',$sId,MP_TxKeinDatensatz);
 }

 $X= "\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';

 $sAjaxURL=MP_Url; $bWww=(strtolower(substr(fMpWww(),0,4))=='www.');
 if($bWww&&!strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://','://www.',$sAjaxURL);
 elseif(!$bWww&&strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://www.','://',$sAjaxURL);

 if($mpCaptcha) $X.="\n
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
   oForm=oFrm; oForm.elements['mp_CaptchaTyp'].value=sTyp; oDate=new Date();
   xmlHttpObject.open('get','".$sAjaxURL."captcha.php?cod='+sTyp+oDate.getTime());
   xmlHttpObject.onreadystatechange=showResponse;
   xmlHttpObject.send(null);
 }}

 function showResponse(){
  if(xmlHttpObject){
   if(xmlHttpObject.readyState==4){
    var sResponse=xmlHttpObject.responseText;
    var sQuestion=sResponse.substring(33,sResponse.length-1);
    var aSpans=oForm.getElementsByTagName('span'); var nQryId=0; var nImgId=0;
    for(var i=0;i<aSpans.length;i++) if(aSpans[i].className=='capQry') nQryId=i; else if(aSpans[i].className=='capImg') nImgId=i;
    oForm.elements['mp_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['mp_CaptchaFrage'].value=sQuestion;
     aSpans[nQryId].innerHTML=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['mp_CaptchaFrage'].value='".fMpTx(MP_TxCaptchaHilfe)."';
     aSpans[nQryId].innerHTML='".fMpTx(MP_TxCaptchaHilfe)."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".MP_Url.MP_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
 }}}}
</script>\n";

 $X.="\n".'<form class="mpForm" action="'.fMpHref('nachricht',(MP_Popup?'':$nSeite),$sId,(MP_Popup?'&amp;mp_Popup=1':'')).'" method="post">'.rtrim("\n".$sHid).rtrim("\n".MP_Hidden);
 $X.="\n".'<div class="mpTabl">';
 $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1">'.fMpTx(MP_TxEmpfaenger).'<br />'.fMpTx(MP_TxMailAdresse).'</div><div class="mpTbSp2"><div class="mp'.($bEml?'Eing':'Fhlt').'"><input class="mpEing" type="text" name="mp_bEml" value="'.fMpTx($sEml).'" /></div></div></div>';
 $X.="\n".'<div class="mpTbZl2"><div class="mpTbSp1">'.fMpTx(MP_TxDetail).'</div><div class="mpTbSp2">'.$H.'</div></div>';
 if($mpCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="mpTbZl1">
   <div class="mpTbSp1">'.fMpTx(MP_TxCaptchaFeld).'*</div>
   <div class="mpTbSp2">
    <div class="mpNorm"><span class="capQry">'.fMpTx($Cap->Type!='G'?$Cap->Question:MP_TxCaptchaHilfe).'</span></div>
    <div class="mpNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.MP_Url.MP_CaptchaPfad.$Cap->Question.'" />').'</span></div>
    <div class="mp'.($bCapErr?'Fhlt':'Eing').'">
     <input class="mpEing capAnsw" name="mp_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><input name="mp_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" /><input name="mp_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" /><input name="mp_CaptchaFrage" type="hidden" value="'.fMpTx($Cap->Type!='G'?$Cap->Question:MP_TxCaptchaHilfe).'" />
     <span class="mpNoBr">
      '.(MP_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fMpTx(str_replace('#',MP_TxZahlenCaptcha,MP_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(MP_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fMpTx(str_replace('#',MP_TxTextCaptcha,MP_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(MP_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fMpTx(str_replace('#',MP_TxGrafikCaptcha,MP_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     </span>
    </div>
   </div>
  </div>';
 }
 $X.="\n".'</div>';
 if($bDo) $X.="\n".'<div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxSenden).'" title="'.fMpTx(MP_TxSenden).'" /></div>';
 $X.="\n".'</form>'."\n";
 if(!$bOkD){$X="\n".'<p class="mpFehl">'.fMpTx($Meld).'</p>'; define('MP_410Gone',true);}
 return $X;
}

function fMpEmlBekannt($sEml,$DbO){
 if(!MP_SQL){
  return strpos(str_replace("\r",'',implode('',file(MP_Pfad.MP_Daten.MP_MailAdr)))."\n","\n".fMpEnCode(strtolower($sEml))."\n")>0;
 }elseif($DbO){
  if($rR=$DbO->query('SELECT nr FROM '.MP_SqlTabM.' WHERE email="'.$sEml.'"')){
   $a=$rR->fetch_row(); $rR->close(); return (isset($a[0])&&$a[0]>'0');
  }else return false;
 }else return false;
}
function fMpEnCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fMpUrl(){
 $s=substr(MP_Url,0,strpos(MP_Url,':')).'://';
 if(isset($_SERVER['HTTP_HOST'])) $s.=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s.=$_SERVER['SERVER_NAME']; else $s.='localhost';
 return $s;
}
?>