<?php
function fKalSeite(){ //Seiteninhalt
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_ZeilenStil, $kal_WochenTag;

 $Et=''; $Es='Fehl'; $H=''; $U=''; $sHid=''; $sId=''; $sAn=''; $sBtr=''; $sTxt=''; $sVon=''; $sEml='';
 $bBtr=true; $bTxt=true; $bVon=true; $bEml=true; $bOK=true; $bDo=true; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

 //Captcha behandeln
 if($bCaptcha=KAL_Captcha){
  $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false;
  require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
  if($_SERVER['REQUEST_METHOD']=='POST'){
   $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $bOK=false;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);}
 }

 //SQL-Verbindung oeffnen
 $DbO=NULL;
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 //variable Daten holen
 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if(isset($_GET['kal_Nummer'])){
   $sId=sprintf('%0d',$_GET['kal_Nummer']); $Et=KAL_TxMailMeld; $Es='Meld'; reset($_GET);
   foreach($_GET as $k=>$v) if(substr($k,0,4)=='kal_') $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
   $sWww=fKalHost(); $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxMailBtr));
   $sTxt=str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxMailTxt));
  }

  $sNutzerEml=''; $sNutzerName='';
  if($sSes=substr(KAL_Session,17,12)){ //Session prüfen
   $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
   if((time()>>6)<=$nTm){ //nicht abgelaufen
    $k=KAL_NNutzerListFeld; if($k<=0) $k=KAL_NutzerListFeld;
    if(!KAL_SQL){
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
      if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
       $bSesOK=true; $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $sNutzerEml=fKalDeCode($aN[4]);
       if($k>1) if(!$sNutzerName=$aN[$k]) $sNutzerName=KAL_TxAutorUnbekannt; elseif($k<5&&$k>1) $sNutzerName=fKalDeCode($sNutzerName);
      }break;
     }
     if(!$bSesOK) $Et=KAL_TxSessionUngueltig;
    }elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
      if($rR->num_rows>0){
       $bSesOK=true; $aN=$rR->fetch_row(); array_splice($aN,1,1); $sNutzerEml=$aN[4];
       if($k>1) if(!$sNutzerName=$aN[$k]) $sNutzerName=KAL_TxAutorUnbekannt;
      }else $Et=KAL_TxSessionUngueltig;
      $rR->close();
     }else $Et=KAL_TxSqlFrage;
   }}else $Et=KAL_TxSessionZeit;
  }
  $sEml=$sNutzerEml; $sVon=$sNutzerName; if(strlen(KAL_TxKontaktAbsender)<=0) $sVon=$sNutzerEml;
 }else{ //POST
  if(isset($_POST['kal_Nummer'])){
   $sId=sprintf('%0d',$_POST['kal_Nummer']); reset($_POST); if(KAL_KontaktDSE1) $bErrDSE1=true; if(KAL_KontaktDSE2) $bErrDSE2=true;
   foreach($_POST as $k=>$v) if(substr($k,0,4)=='kal_'&&substr($k,4,1)!='i'&&substr($k,4,3)!='Cap'&&substr($k,4,3)!='DSE'){
    $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
   }else{
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($k=='kal_iBtr'){
     if($sBtr=$s){if($p=strpos($sBtr,"\n")) $sBtr=rtrim(substr($sBtr,0,$p));} else{$bBtr=false; $bOK=false;}
    }elseif($k=='kal_iTxt'){
     if(!$sTxt=$s){$bTxt=false; $bOK=false;}
    }elseif($k=='kal_iVon'){
     if($sVon=$s){if($p=strpos($sVon,"\n")) $sVon=rtrim(substr($sVon,0,$p));} else{$bVon=false; $bOK=false;}
    }elseif($k=='kal_iEml'){
     if($sEml=$s){
      if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sEml))) {$bEml=false; $bOK=false;}
     }elseif(KAL_KontaktAbsPflicht){$bEml=false; $bOK=false;}
    }
    elseif($k=='kal_DSE1'){if($s=='1'){$bDSE1=true; $bErrDSE1=false;}}
    elseif($k=='kal_DSE2'){if($s=='1'){$bDSE2=true; $bErrDSE2=false;}}
   }
   if($bErrDSE1||$bErrDSE2) $bOK=false;
  }
 }
 if($sId>''){ //Termindaten holen
  if(!KAL_SQL){ //Textdatei
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){ //über alle Datensätze
    $a=explode(';',rtrim($aD[$i])); if($sId==$a[0]){array_splice($a,1,1); break;}
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
    $a=$rR->fetch_row(); array_splice($a,1,1); $rR->close();
   }else $Et=KAL_TxSqlFrage;
  }
  $nFelder=count($kal_FeldName); $nFarb=1; //Termindetails aufbereiten
  if(KAL_KontaktNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  $H.="\n".'<div class="kalTabl">';
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i]; $u=''; if($t=='e'&&$sAn=='') $sAn=(!KAL_SQL?fKalDeCode($a[$i]):$a[$i]);
   if($kal_DetailFeld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
    if($s=$a[$i]){$u=$s;
     switch($t){
      case 't': $s=fKalBB(fKalDt($s)); $u=@strip_tags($s); break; //Text/Memo
      case 'm': if(KAL_KontaktMitMemo){$s=fKalBB(fKalDt($s)); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
      case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
       if($t=='d'){
        if($i==1) $sTxt=str_replace('#D',$u,$sTxt);
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
       }elseif($w) $u.=' '.$w;
       $s=str_replace(' ','&nbsp;',fKalTx($u));
       break;
      case 'z': $u=$s.' '.KAL_TxUhr; $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
      case 'w': //Währung
       if($s>0||!KAL_PreisLeer){
        $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
        if(KAL_Waehrung){$u=$s.' '.KAL_Waehrung; $s.='&nbsp;'.KAL_Waehrung;}
       }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y'){$s=fKalTx(KAL_TxJa); $u=KAL_TxJa;}elseif($s=='N'){$s=fKalTx(KAL_TxNein); $u=KAL_TxNein;}
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $u=$s;
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s=''; $z='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=fKalDt(isset($aI[1])?$aI[1]:$w); $z.=$w.', ';
        $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'"> ';
        $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
       }$s=substr($s,0,-2); $u=substr($z,0,-2); break;
      case 'u':
       if($nId=(int)$s){
        if(KAL_NutzerKontaktFeld>0){
         $s=KAL_TxAutorUnbekannt;
         if(!KAL_SQL){ //Textdaten
          $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
          for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
           $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
           if(!$s=$aN[KAL_NutzerKontaktFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerKontaktFeld<5&&KAL_NutzerKontaktFeld>1) $s=fKalDeCode($s); $s=fKalDt($s);
           break;
          }
         }elseif($DbO){ //SQL-Daten
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
           $aN=$rR->fetch_row(); $rR->close();
           if(is_array($aN)){array_splice($aN,1,1); if(!$s=fKalDt($aN[KAL_NutzerKontaktFeld])) $s=KAL_TxAutorUnbekannt;}
           else $s=KAL_TxAutorUnbekannt;
        }}}
       }else $s=KAL_TxAutor0000; $u=$s;
       break;
      default: {$s=''; $u='';}
     }//switch
    }
    if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}}
    elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
    if(strlen($u)>0) $U.="\n".strtoupper($sFN).': '.$u;
    if(strlen($s)>0){
     $H.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $H.="\n".' <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
     $H.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
    }
   }
  }
  $H.="\n</div>\n";
  $sLnk=(KAL_KontaktLink==''?KAL_Self.'?':KAL_KontaktLink.(!strpos(KAL_KontaktLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sId;
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
  if($_SERVER['REQUEST_METHOD']=='POST'){ //Mail versenden
   if($bOK){
    $Ht='<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite kalEMail">
<p>'.str_replace("\n","</p>\n<p>",str_replace("\n\n","\n",$sTxt)).'</p>
<p>'.trim($sVon.' '.$sEml).'</p>
<p><a href="'.$sLnk.'">'.substr($sLnk,strpos($sLnk,':')+3).'</a></p>
<p style="margin-bottom:2px;">'.fKalTx(KAL_TxDetail).':</p>
';
    require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sVon))){
     $u=KAL_KeineAntwort; if($p=strpos($u,'<')){$v=substr($u,0,$p); $u=substr(substr($u,0,-1),$p+1);} else $v=''; $Mailer->SetReplyTo($u,$v);
    }else $s=$sVon;
    $Mailer->AddTo($sAn); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
    $Mailer->PlainText=$sTxt."\n".$sVon.($sEml?"\n".$sEml:'')."\n\n".$sLnk."\n\n".trim($U);
    $Mailer->HtmlText=$Ht.(KAL_Zeichensatz==0?$H:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$H):html_entity_decode($H)))."\n</body>\n</html>";
    if($Mailer->Send()){
     $Et=KAL_TxSendeErfo; $Es='Erfo'; $bDo=false;
     if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
    }else{$Et=KAL_TxSendeFehl; $bOK=false;}
   }else{$Et=KAL_TxEingabeFehl; $bOK=false;}
  }//POST
 }//sId

 $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';
 if(KAL_DSEPopUp&&(KAL_KontaktDSE1||KAL_KontaktDSE2)) $X.="\n".'<script>function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.KAL_DSEPopupW.',height='.KAL_DSEPopupH.',left='.KAL_DSEPopupX.',top='.KAL_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';

 $sAjaxURL=KAL_Url; $bWww=(strtolower(substr(fKalHost(),0,4))=='www.');
 if($bWww&&!strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://','://www.',$sAjaxURL);
 elseif(!$bWww&&strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://www.','://',$sAjaxURL);

 if($bCaptcha) $X.="\n
<script>
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
   oForm=oFrm; oForm.elements['kal_CaptchaTyp'].value=sTyp; oDate=new Date();
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
    oForm.elements['kal_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['kal_CaptchaFrage'].value=sQuestion;
     aSpans[nQryId].innerHTML=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['kal_CaptchaFrage'].value='".fKalTx(KAL_TxCaptchaHilfe)."';
     aSpans[nQryId].innerHTML='".fKalTx(KAL_TxCaptchaHilfe)."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".KAL_Url.KAL_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\">';
 }}}}
</script>\n";

 $X.="\n".'<form class="kalForm" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).rtrim("\n".$sHid);
 $X.="\n".'<div class="kalTabl">';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxEmpfaenger).'</div><div class="kalTbSp2">'.fKalTx(KAL_TxMailAn).'</div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxBetreff).'</div><div class="kalTbSp2"><div class="kal'.($bBtr?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iBtr" value="'.fKalTx($sBtr).'"></div></div></div>';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxMitteilung).'</div><div class="kalTbSp2"><div class="kal'.($bTxt?'Eing':'Fhlt').'"><textarea class="kalEing" name="kal_iTxt">'.fKalTx($sTxt).'</textarea></div></div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxAbsender).'</div><div class="kalTbSp2"><div class="kal'.($bVon?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iVon" value="'.fKalTx($sVon).'"></div></div></div>';
 if(strlen(KAL_TxKontaktAbsender)>0) $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxKontaktAbsender).'</div><div class="kalTbSp2"><div class="kal'.($bEml?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iEml" value="'.fKalTx($sEml).'"></div></div></div>';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxInfoLink).'</div><div class="kalTbSp2"><textarea class="kalEing" readonly="readonly" style="height:4em">'.fKalTx($sLnk).'</textarea></div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxDetail).'</div><div class="kalTbSp2">'.$H.'</div></div>';
 if(KAL_KontaktDSE1) $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE1?'Fhlt':'Eing').'">'.fKalDSEFld(1,$bDSE1).'</div></div></div>';
 if(KAL_KontaktDSE2) $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE2?'Fhlt':'Eing').'">'.fKalDSEFld(2,$bDSE2).'</div></div></div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="kalTbZl'.(KAL_KontaktDSE1||KAL_KontaktDSE2?'2':'1').'">
   <div class="kalTbSp1">'.fKalTx(KAL_TxCaptchaFeld).'*</div>
   <div class="kalTbSp2">
    <div class="kalNorm"><span class="capQry">'.fKalTx($Cap->Type!='G'?$Cap->Question:KAL_TxCaptchaHilfe).'</span></div>
    <div class="kalNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.KAL_Url.KAL_CaptchaPfad.$Cap->Question.'">').'</span></div>
    <div class="kal'.($bCapErr?'Fhlt':'Eing').'">
     <input class="kalEing capAnsw" name="kal_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15"><input name="kal_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'"><input name="kal_CaptchaTyp" type="hidden" value="'.$Cap->Type.'"><input name="kal_CaptchaFrage" type="hidden" value="'.fKalTx($Cap->Type!='G'?$Cap->Question:KAL_TxCaptchaHilfe).'">
     <span class="kalNoBr">
      '.(KAL_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fKalTx(str_replace('#',KAL_TxZahlenCaptcha,KAL_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(KAL_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fKalTx(str_replace('#',KAL_TxTextCaptcha,KAL_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(KAL_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fKalTx(str_replace('#',KAL_TxGrafikCaptcha,KAL_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     </span>
    </div>
   </div>
  </div>';
 }
 $X.="\n".'</div>';
 if($bDo) $X.="\n".'<div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'"></div>';
 $X.="\n".'</form>'."\n";
 return $X;
}

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}
?>