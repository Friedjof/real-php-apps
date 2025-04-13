<?php
function fMpSeite(){
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 $Meld=''; $MTyp='Fehl'; $H=''; $U=''; $sHid=''; $sId=''; $sAn=''; $sBtr=''; $sTxt=''; $sVon=''; $sEml='';
 $bInf=true; $bBtr=true; $bTxt=true; $bVon=true; $bEml=true; $bCap=true; $bOK=true; $bDo=true; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

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
   $sId=sprintf('%d',$_GET['mp_Nummer']); $Meld=MP_TxMailMeld; $MTyp='Meld'; reset($_GET);
   foreach($_GET as $k=>$v) if(substr($k,0,3)=='mp_') $sHid.='<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
   $sBtr=MP_TxMailBtr; $sTxt=str_replace('#A',fMpUrl(),str_replace('#S',MP_SegName,str_replace('\n ',"\n",MP_TxMailTxt)));
  }
  $sNutzerEml=''; $sNutzerName='';
  if($sSes=MP_Session){ //Session prüfen
   $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
   if((time()>>6)<=$nTm){ //nicht abgelaufen
    $k=MP_NNutzerListFeld; if($k<=0) $k=MP_NutzerListFeld;
    if(!MP_SQL){
     $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
      if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
       $bSesOK=true; $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $sNutzerEml=fMpDeCode($aN[4]);
       if($k>1) if(!$sNutzerName=$aN[$k]) $sNutzerName=MP_TxAutorUnbekannt; elseif($k<5&&$k>1) $sNutzerName=fMpDeCode($sNutzerName);
      }break;
     }
    }elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
      if($rR->num_rows>0){
       $bSesOK=true; $aN=$rR->fetch_row(); array_splice($aN,1,1); $sNutzerEml=$aN[4];
       if($k>1) if(!$sNutzerName=$aN[$k]) $sNutzerName=MP_TxAutorUnbekannt;
      }
      $rR->close();
  }}}}
  $sEml=$sNutzerEml; $sVon=$sNutzerName; if(strlen(MP_TxInfoAbsender)<=0) $sVon=$sNutzerEml;
 }else{ //POST
  if(isset($_POST['mp_Nummer'])){
   $sId=sprintf('%d',$_POST['mp_Nummer']); reset($_POST);
   foreach($_POST as $k=>$v) if(substr($k,0,3)=='mp_'&&substr($k,3,1)!='i'&&substr($k,3,3)!='Cap'&&substr($k,3,3)!='DSE'){
    $sHid.='<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
   }else{
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($k=='mp_iBtr'){
     if($sBtr=$s){if($p=strpos($sBtr,"\n")) $sBtr=rtrim(substr($sBtr,0,$p));} else{$bBtr=false; $bOK=false;}
    }elseif($k=='mp_iTxt'){
     if(!$sTxt=$s){$bTxt=false; $bOK=false;}
    }elseif($k=='mp_iVon'){
     if($sVon=$s){if($p=strpos($sVon,"\n")) $sVon=rtrim(substr($sVon,0,$p));} else{$bVon=false; $bOK=false;}
    }elseif($k=='mp_iEml'){
     if($sEml=$s){
      if(!fMpIsEMailAdr($s)){$bEml=false; $bOK=false;}
     }elseif(MP_KontaktAbsPflicht){$bEml=false; $bOK=false;}
   }}
   if(MP_KontaktDSE1) if(isset($_POST['mp_DSE1'])&&$_POST['mp_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $bOK=false;}
   if(MP_KontaktDSE2) if(isset($_POST['mp_DSE2'])&&$_POST['mp_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $bOK=false;}
   if(empty($sEml)&&fMpIsEMailAdr($sVon)) $sEml=$sVon;
 }}

 $nSeite=(isset($_GET['mp_Seite'])?(int)$_GET['mp_Seite']:1);
 if($sId>''){ //Inseratedaten holen
  if(!MP_SQL){ //Textdatei
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $aR=explode(';',rtrim($aD[$i])); if($sId==$aR[0]&&$aR[1]=='1'){array_splice($aR,1,1); break;}
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'"')){
    $aR=$rR->fetch_row(); array_splice($aR,1,1); $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }

  if($i=(isset($_POST['mp_Eml'])?(int)$_POST['mp_Eml']:0)){ //EMail-Adresse holen
   $sAn=(!MP_SQL?fMpDeCode($aR[$i]):$aR[$i]);  if(!strpos($sAn,'@')||!strpos($sAn,'.')) $sAn='';
  }
  $nFarb=1; //Inseratedetails aufbereiten
  if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
  $H.="\n".'<div class="mpTabl">';
  for($i=1;$i<$nFelder;$i++){
   $t=$aMpFT[$i]; $u=''; if($t=='e'&&$sAn=='') $sAn=(!MP_SQL?fMpDeCode($aR[$i]):$aR[$i]);
   if($aMpDF[$i]>0&&$t!='p'&&$t!='c'&&substr($aMpFN[$i],0,5)!='META-'&&$aMpFN[$i]!='TITLE'){
    if($s=str_replace('`,',';',$aR[$i])){$u=$s;
     switch($t){
      case 't': $s=fMpBB(fMpDt($s)); $u=@strip_tags($s); break; //Text
      case 'm': if(MP_KontaktMitMemo){$s=fMpBB(fMpDt($s)); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
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
       $u=$s; $s=fMpDt($u); break;
      case 'u': //Benutzer
       if($nId=(int)$s){
        $s=MP_TxAutorUnbekannt;
        if(!MP_SQL){ //Textdaten
         $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
          if(!$s=$aN[MP_NutzerKontaktFeld]) $s=MP_TxAutorUnbekannt; elseif(MP_NutzerKontaktFeld<5&&MP_NutzerKontaktFeld>1) $s=fMpDeCode($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[MP_NutzerKontaktFeld]) $s=MP_TxAutorUnbekannt;}
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
  $sLnk=(MP_KontaktLink==''?fMpLref('detail','',$sId,(MP_DetailPopup?'&amp;mp_Popup=1':'')):MP_KontaktLink.(!strpos(MP_KontaktLink,'?')?'?':'&amp;').'mp_Aktion=detail&amp;mp_Segment='.MP_Segment.'&amp;mp_Nummer='.$sId.(MP_DetailPopup?'&amp;mp_Popup=1':'').MP_Query);
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')==false) $sLnk=fMpUrl().$sLnk;
  if($_SERVER['REQUEST_METHOD']=='POST'){ //Mail versenden
   if($bOK){
    $Ht='<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset='.(MP_Zeichensatz!=2?'iso-8859-1':'utf-8').'">
 <link rel="stylesheet" type="text/css" href="'.MP_Url.'mpStyles.css">
</head>
<body class="mpEMail">
<p>'.str_replace("\n","</p>\n<p>",str_replace("\n\n","\n",htmlspecialchars($sTxt,ENT_COMPAT,'ISO-8859-1'))).'</p>
<p>'.trim(htmlspecialchars($sVon,ENT_COMPAT,'ISO-8859-1').' '.($sEml?'<a href="mailto:'.$sEml.'">'.$sEml.'</a>':'')).'</p>
<p><a href="'.$sLnk.'">'.substr($sLnk,strpos($sLnk,':')+3).'</a></p>
<p style="margin-bottom:2px;">'.fMpTx(MP_TxDetail).':</p>
';
    require_once(MP_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
    if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
    $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    if(!fMpIsEMailAdr($sEml)){
     $u=MP_KeineAntwort; if($p=strpos($u,'<')){$v=substr($u,0,$p); $u=substr(substr($u,0,-1),$p+1);} else $v=''; $Mailer->SetReplyTo($u,$v);
    }else{$Mailer->SetReplyTo($sEml,$sVon);}
    $Mailer->AddTo($sAn); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
    $Mailer->PlainText=$sTxt."\n".$sVon.($sEml?"\n".$sEml:'')."\n\n".$sLnk."\n\n".trim($U);
    $Mailer->HtmlText=str_replace("\r",'',$Ht).(MP_Zeichensatz==0?$H:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$H):html_entity_decode($H)))."\n</body>\n</html>";
    if($Mailer->Send()){
     $Meld=MP_TxSendeErfo; $MTyp='Erfo'; $bDo=false;
     if($mpCaptcha){$Cap->Delete(); $mpCaptcha=false;} //Captcha loeschen
    }else{$Meld=MP_TxSendeFehl; $bOK=false;}
   }else{$Meld=MP_TxEingabeFehl; $bOK=false;}
  }//POST
 }//sId

 $X= "\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';
 if(MP_DSEPopUp&&(MP_KontaktDSE1||MP_KontaktDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.MP_DSEPopupW.',height='.MP_DSEPopupH.',left='.MP_DSEPopupX.',top='.MP_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';

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

 $X.="\n".'<form class="mpForm" action="'.fMpHref('kontakt',(MP_Popup?'':$nSeite),$sId,(MP_Popup?'&amp;mp_Popup=1':'')).'" method="post">'.rtrim("\n".$sHid).rtrim("\n".MP_Hidden);
 $X.="\n".'<div class="mpTabl">';
 $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1">'.fMpTx(MP_TxEmpfaenger).'</div><div class="mpTbSp2">'.fMpTx(MP_TxMailAn).'</div></div>';
 $X.="\n".'<div class="mpTbZl2"><div class="mpTbSp1">'.fMpTx(MP_TxBetreff).'</div><div class="mpTbSp2"><div class="mp'.($bBtr?'Eing':'Fhlt').'"><input class="mpEing" type="text" name="mp_iBtr" value="'.fMpTx($sBtr).'" /></div></div></div>';
 $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1">'.fMpTx(MP_TxMitteilung).'</div><div class="mpTbSp2"><div class="mp'.($bTxt?'Eing':'Fhlt').'"><textarea class="mpEing" name="mp_iTxt">'.fMpTx($sTxt).'</textarea></div></div></div>';
 $X.="\n".'<div class="mpTbZl2"><div class="mpTbSp1">'.fMpTx(MP_TxAbsender).'</div><div class="mpTbSp2"><div class="mp'.($bVon?'Eing':'Fhlt').'"><input class="mpEing" type="text" name="mp_iVon" value="'.fMpTx($sVon).'" /></div></div></div>';
 if(strlen(MP_TxKontaktAbsender)>0) $X.="\n".'<div class="mpTbZl2"><div class="mpTbSp1">'.fMpTx(MP_TxKontaktAbsender).'</div><div class="mpTbSp2"><div class="mp'.($bEml?'Eing':'Fhlt').'"><input class="mpEing" type="text" name="mp_iEml" value="'.fMpTx($sEml).'" /></div></div></div>';
 $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1">'.fMpTx(MP_TxInfoLink).'</div><div class="mpTbSp2"><textarea class="mpEing" readonly="readonly" style="height:4em;border-style:none;border-width:0">'.fMpTx($sLnk).'</textarea></div></div>';
 $X.="\n".'<div class="mpTbZl2"><div class="mpTbSp1">'.fMpTx(MP_TxDetail).'</div><div class="mpTbSp2">'.$H.'</div></div>';
 if(MP_KontaktDSE1) $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE1?'Fhlt':'Eing').'">'.fMpDSEFld(1,$bDSE1).'</div></div></div>';
 if(MP_KontaktDSE2) $X.="\n".'<div class="mpTbZl1"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE2?'Fhlt':'Eing').'">'.fMpDSEFld(2,$bDSE2).'</div></div></div>';
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
 return $X;
}

function fMpUrl(){
 $s=substr(MP_Url,0,strpos(MP_Url,':')).'://';
 if(isset($_SERVER['HTTP_HOST'])) $s.=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s.=$_SERVER['SERVER_NAME']; else $s.='localhost';
 return $s;
}

function fMpLref($sAct='',$sSei='',$sNum='',$sPar=''){ //erzeugt einen Link
 $sL=''; $sSeg=MP_Segment;
 if(!MP_Sef){ //normal
  if($sAct) $sL.='&amp;mp_Aktion='.$sAct;
  if($sSeg>''&&!strpos($sAct,'_')) $sL.='&amp;mp_Segment='.$sSeg;
  if(MP_ListenLaenge>0&&$sSei) $sL.='&amp;mp_Seite='.$sSei;
  if($sNum) $sL.='&amp;mp_Nummer='.$sNum;
  if($sPar) $sL.=$sPar; $sL.=MP_Query; if($sL) $sL='?'.substr($sL,5);
  $sL=(!strpos($sPar,'_Popup=1')?MP_Self:MP_Url.'marktplatz.php').$sL;
 }else{ // SEF
  $sL=substr(MP_Self,0,strrpos(MP_Self,'/')+1);
  if($sAct){
   if($sSeg>''&&!strpos($sAct,'_')){
    $aN=explode(';',MP_Segmente); if(!$sNam=$aN[$sSeg]) $sNam=MP_TxSegment;
    if($p=strpos($sNum,'-')){$sNam=substr($sNum,$p+1); $sNum=substr($sNum,0,$p);}
    $sL.=fMpNormAdr($sNam).'-'.$sAct.'-'.$sSeg;
    if((MP_ListenLaenge>0&&$sSei)||$sNum) $sL.='-'.$sSei; if($sNum) $sL.='-'.$sNum;
   }else{
    $sL.=$sAct;
    if(MP_ListenLaenge>0&&$sSei) $sL.='-'.$sSei;
   }
  }else $sL.='marktplatz';
  $sL.='.html';
  $sPar.=MP_Query; if($sPar) $sL.='?'.substr($sPar,5);
 }
 return $sL;
}
?>