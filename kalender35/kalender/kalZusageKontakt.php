<?php
function fKalSeite(){
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_ZeilenStil, $kal_WochenTag;

 $Et=''; $Es='Fehl'; $Et2=''; $sHT=''; $sUT=''; $sHid=''; $sTId=''; $sAn=''; $sBtr=''; $sTxt=''; $sVon=''; $sEml=''; $sQ='';
 $bBtr=true; $bTxt=true; $bVon=true; $bEml=true; $bCap=true; $bOK=true; $bDo=true;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 $sNutzerEml=''; $sNutzerName=''; $bSesOK=false;
 if($sSes=substr(KAL_Session,17,12)){ //Session pruefen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   $k=KAL_NNutzerListFeld; if($k<=0) $k=KAL_NutzerListFeld;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $bSesOK=true; $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $sNutzerEml=fKalDeCode($aN[4]);
      if($k>1) $sNutzerName=$aN[$k]; if($k<5&&$k>1) $sNutzerName=fKalDeCode($sNutzerName);
     }break;
    }
    if(!$bSesOK) $Et=KAL_TxSessionUngueltig;
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0){
      $bSesOK=true; $aN=$rR->fetch_row(); array_splice($aN,1,1); $sNutzerEml=$aN[4];
      if($k>1) $sNutzerName=$aN[$k];
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }
 $sEml=$sNutzerEml; $sVon=$sNutzerName; $aZsgn=array(); $aZ=array(); $aM=array(); $aB=array(); $aT=array(); $sLnk='';

 if($sTId=fKalRq1(isset($_GET['kal_Nummer'])?$_GET['kal_Nummer']:(isset($_POST['kal_Nummer'])?$_POST['kal_Nummer']:''))){//Daten holen
  if(!KAL_SQL){ //Zusagen fuer  Termin
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sTId.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++){$sZl=$aD[$i];
    if(substr($sZl,strpos($sZl,';'),$p)==$s){
     $a=explode(';',rtrim($sZl)); $aM[]=fKalDeCode($a[8]); $aZ[]=$a[5]; $aB[]=$a[2]; $aZsgn[]=$a;
   }}
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $p=strlen($s); //Termin
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); break;}
  }elseif($DbO){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'"')){ //Zusagen
     while($a=$rR->fetch_row()){$aM[]=$a[8]; $aZ[]=$a[5]; $aB[]=$a[2]; $aZsgn[]=$a;} $rR->close();
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){ //Termin
      $aT=$rR->fetch_row(); array_splice($aT,1,1); $rR->close();
     }
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
  $sAn=(isset($aM[0])?$aM[0]:'').(isset($aM[1])?', '.$aM[1]:'').(isset($aM[2])?', '.$aM[2]:'').(isset($aM[3])?', ...':'');

  $nFelder=count($kal_FeldName); $nFarb=1; //Termindetails aufbereiten
  if(KAL_KontaktNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  $sHT.="\n".'<div class="kalTabl">';
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i]; $u='';
   if($kal_DetailFeld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
    if($s=str_replace('`,',';',$aT[$i])){$u=$s;
     switch($t){
      case 't': $s=fKalBB(fKalDt($s)); $u=@strip_tags($s); break; //Text/Memo
      case 'm': if(KAL_KontaktMitMemo){$s=fKalBB(fKalDt($s)); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
      case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
       if($t=='d'){
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
         }else{ //SQL-Daten
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
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
    if(strlen($u)>0) $sUT.="\n".strtoupper($sFN).': '.$u;
    if(strlen($s)>0){
     $sHT.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $sHT.="\n".' <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
     $sHT.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
    }
   }
  }
  $sHT.="\n</div>\n";
  $sLnk=(KAL_KontaktLink==''?KAL_Self.'?':KAL_KontaktLink.(!strpos(KAL_KontaktLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sTId;
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;

  if($_SERVER['REQUEST_METHOD']=='POST'){ //Mail versenden
   reset($_POST);
   foreach($_POST as $k=>$v) if(substr($k,0,4)=='kal_'&&substr($k,4,1)!='i'){
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
     if($sEml=$s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sEml))) {$bEml=false; $bOK=false;}
    }
   }
   if($bOK){
    $sHtml='<!DOCTYPE html>
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
    require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail(); $Mailer->Subject=$sBtr; $nOK=0; $nErr=0; $i=0;
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->SetFrom(($sEml?$sEml:$s),($sVon?$sVon:$t)); if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
    $aZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=count($aZusageFelder);
    foreach($aM as $k=>$sTo){ $sAnr='';
     $sTxtM=$sTxt; $sHtmlM=$sHtml;
     for($j=1;$j<$nZusageFelder;$j++) if(strpos('#'.$sTxt,'{'.$aZusageFelder[$j].'}')){
      if(strtolower($aZsgn[$i][$j])=='herr') $sAnr='r';
      $sTxtM=str_replace('{'.$aZusageFelder[$j].'}',$aZsgn[$i][$j],$sTxtM);
      $sHtmlM=str_replace('{'.$aZusageFelder[$j].'}',$aZsgn[$i][$j],$sHtmlM);
     }
     if(strpos($sTxt,'{r}')){$sTxtM=str_replace('{r}',$sAnr,$sTxtM); $sHtmlM=str_replace('{r}',$sAnr,$sHtmlM);}
     $Mailer->PlainText=str_replace('{#Z}',fKalAnzeigeDatum($aZ[$i]).substr($aZ[$i],10),str_replace('{#D}',fKalAnzeigeDatum($aB[$i]),$sTxtM))."\n".$sVon.($sEml?"\n".$sEml:'')."\n\n".$sLnk."\n\n".trim($sUT);
     $Mailer->HtmlText =str_replace('{#Z}',fKalAnzeigeDatum($aZ[$i]).substr($aZ[$i],10),str_replace('{#D}',fKalAnzeigeDatum($aB[$i]),$sHtmlM)).(KAL_Zeichensatz==0?$sHT:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sHT):html_entity_decode($sHT)))."\n</body>\n</html>";
     $Mailer->AddTo($sTo); if($Mailer->Send()) $nOK++; else $nErr++; $Mailer->ClearTo(); $i++;
    }
    if($nOK){$Et=KAL_TxSendeErfo.' ('.$nOK.')'; $Es='Erfo'; $bDo=false;}
    if($nErr){$Et2='</p><p calss="kalFehl">'.fKalTx(KAL_TxSendeFehl).' ('.$nErr.')';}
   }else{$Et=KAL_TxEingabeFehl; $bOK=false;}
  }else{//GET
   if($bSesOK){$Et=KAL_TxNZusageAlle; $Es='Meld';} reset($_GET);
   foreach($_GET as $k=>$v) if(substr($k,0,4)=='kal_') $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
   $sWww=fKalHost(); $sBtr=str_replace('#A',$sWww,KAL_TxZusageKontBtr);
   $sTxt=str_replace('#D','{#D}',str_replace('#Z','{#Z}',str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxZusageKontMTx))));
  }
 }else $Et=KAL_TxNummerUnbek;

 if($n=(int)(isset($_GET['kal_Sort'])?$_GET['kal_Sort']:(isset($_POST['kal_Sort'])?$_POST['kal_Sort']:0))) $sQ.='&amp;kal_Sort='.$n;
 if($n=(int)(isset($_GET['kal_Abst'])?$_GET['kal_Abst']:(isset($_POST['kal_Abst'])?$_POST['kal_Abst']:0))) $sQ.='&amp;kal_Abst='.$n;
 if($n=(int)(isset($_GET['kal_Start'])?$_GET['kal_Start']:(isset($_POST['kal_Start'])?$_POST['kal_Start']:0))) $sQ.='&amp;kal_Start='.$n;
 if($s=fKalRq1(isset($_GET['kal_Index'])?$_GET['kal_Index']:(isset($_POST['kal_Index'])?$_POST['kal_Index']:'')))$sQ.='&amp;kal_Index='.$s;
 if($s=fKalRq1(isset($_GET['kal_Rueck'])?$_GET['kal_Rueck']:(isset($_POST['kal_Rueck'])?$_POST['kal_Rueck']:'')))$sQ.='&amp;kal_Rueck='.$s;
 if($s=fKalRq(isset($_GET['kal_ZSuch'])?$_GET['kal_ZSuch']:(isset($_POST['kal_ZSuch'])?$_POST['kal_ZSuch']:''))){
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;kal_ZSuch='.rawurlencode($s);
 }

 //Seitenausgabe
 $X ="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).(KAL_Zusagen?'':' ('.'De'.'m'.'ov'.'er'.'si'.'on'.')').$Et2.'</p>';
 $X.="\n".'<form class="kalForm" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).rtrim("\n".$sHid);
 $X.="\n".'<div class="kalTabl">';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxAbsender).'</div><div class="kalTbSp2"><div class="kal'.($bVon?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iVon" value="'.fKalTx($sVon).'"></div><div class="kal'.($bEml?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iEml" value="'.fKalTx($sEml).'"></div></div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxEmpfaenger).'</div><div class="kalTbSp2">'.$sAn.'</div></div>';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxBetreff).'</div><div class="kalTbSp2"><div class="kal'.($bBtr?'Eing':'Fhlt').'"><input class="kalEing" type="text" name="kal_iBtr" value="'.fKalTx($sBtr).'"></div></div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxMitteilung).'</div><div class="kalTbSp2"><div class="kal'.($bTxt?'Eing':'Fhlt').'"><textarea class="kalEing" name="kal_iTxt">'.fKalTx($sTxt).'</textarea></div><span class="kalMini">'.fKalTx(KAL_TxPlatzhZusage).'</span></div></div>';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">'.fKalTx(KAL_TxInfoLink).'</div><div class="kalTbSp2"><textarea class="kalEing" readonly="readonly" style="height:4em;border-style:none; border-width:0;">'.fKalTx($sLnk).'</textarea></div></div>';
 $X.="\n".'<div class="kalTbZl2"><div class="kalTbSp1">'.fKalTx(KAL_TxDetail).'</div><div class="kalTbSp2">'.$sHT.'</div></div>';
 $X.="\n".'</div>';
 if($bDo&&$bSesOK) $X.="\n".'<div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'"></div>';
 $X.="\n".'</form>'."\n";
 $X.="\n".'<div class="kalSchalter">'.KAL_LinkAnf.'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=zusagezeigen'.KAL_Session.'&amp;kal_Nummer='.$sTId.$sQ.'&amp;kal_Zusagen=1">'.fKalTx(KAL_TxNfUebersicht).'</a>'.KAL_LinkEnd.'</div>';
 return $X;
}

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}
?>