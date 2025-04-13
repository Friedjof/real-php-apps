<?php
function fMpSeite(){
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 $aW=array(); $aUpl=array(); $aOh=array(); $aOa=array(); $aOs=array(); $aFehl=array(); $bOK=false; $bOkD=false;
 $Meld=''; $MTyp='Fehl'; $sSta='1'; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Struktur holen
 $nFelder=0; $aStru=array(); $aMpFN=array(); $aMpFT=array(); $aMpDF=array(); $aMpND=array(); $sAblaufMax='???';
 $aMpEF=array(); $aMpNE=array(); $aMpEL=array(); $aMpPF=array(); $aMpTZ=array(); $aMpET=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
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
  $aMpDF=explode(';',rtrim($aStru[7])); $aMpDF[0]='1';
  $aMpND=explode(';',rtrim($aStru[8])); $aMpND[0]='1';
  $aMpEF=explode(';',rtrim($aStru[11])); $aMpEF[0]='1';
  $aMpNE=explode(';',rtrim($aStru[12])); $aMpNE[0]='1';
  $aMpPF=explode(';',rtrim($aStru[13])); $aMpPF[0]='';
  $aMpTZ=explode(';',rtrim($aStru[14])); $aMpTZ[0]='0';
  $aMpET=explode(';',rtrim($aStru[15])); $aMpET[0]=''; $sAblaufMax=date('Y-m-d',min(86400*$aMpET[1]+time(),2147483647));
  $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]=''; $aMpAW[1]='';
  $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
  $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
  $aMpEL=explode(';',rtrim($aStru[19])); $aMpEL[0]='1';
 }

 //Formularzugang pruefen
 $bFormEingabe=false; $bSesOK=false; $nNId=''; $bCaptcha=MP_Captcha; $nGhPos=0; $nGh=0; $bGhNum=true;
 $sNutzerEml=''; $sNutzerMailing=MP_TxAutorGast; $sNutzerName=MP_TxAutorGast; $nNuPos=array_search('u',$aMpFT);

 if($sSes=MP_Session){ //Session pruefen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if($p=$nNuPos){
    if(!$k=$aMpND[$p]) if(!$k=MP_NNutzerListFeld) if(!$k=$aMpDF[$p]) $k=MP_NutzerListFeld;
    $aNF=explode(';',MP_NutzerFelder); if($nGhPos=array_search('HABEN',$aNF)) --$nGhPos;
   }
   if(!MP_SQL){
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $bSesOK=true; $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $sNutzerEml=fMpDeCode($aN[4]);
      if(MP_NNutzerDetailFeld&&isset($aN[MP_NNutzerDetailFeld]))
       $sNutzerName=MP_NNutzerDetailFeld<5&&MP_NNutzerDetailFeld>1?fMpDeCode($aN[MP_NNutzerDetailFeld]):$aN[MP_NNutzerDetailFeld];
      elseif(MP_NutzerDetailFeld&&isset($aN[MP_NutzerDetailFeld]))
       $sNutzerName=MP_NutzerDetailFeld<5&&MP_NutzerDetailFeld>1?fMpDeCode($aN[MP_NutzerDetailFeld]):$aN[MP_NutzerDetailFeld];
      else $sNutzerName=$nNId;
      if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld]))
       $sNutzerMailing=MP_NutzerMailListeFeld<5&&MP_NutzerMailListeFeld>1?fMpDeCode($aN[MP_NutzerMailListeFeld]):$aN[MP_NutzerMailListeFeld];
      else $sNutzerMailing=$nNId;
      if($nGhPos){
       $nGh=trim($aN[$nGhPos]);
       if(substr_count($nGh,'-')<2) $nGh=(int)$nGh; else $bGhNum=false;
      }
     }break;
    }
    if(!$bSesOK) $Meld=MP_TxSessionUngueltig;
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0){
      $bSesOK=true; $aN=$rR->fetch_row(); array_splice($aN,1,1); $sNutzerEml=$aN[4];
      if(MP_NNutzerDetailFeld&&isset($aN[MP_NNutzerDetailFeld])) $sNutzerName=$aN[MP_NNutzerDetailFeld];
      elseif(MP_NutzerDetailFeld&&isset($aN[MP_NutzerDetailFeld])) $sNutzerName=$aN[MP_NutzerDetailFeld];
      else $sNutzerName=$nNId;
      if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld])) $sNutzerMailing=$aN[MP_NutzerMailListeFeld];
      else $sNutzerMailing=$nNId;
      if($nGhPos){
       $nGh=trim($aN[$nGhPos]);
       if(substr_count($nGh,'-')<2) $nGh=(int)$nGh; else $bGhNum=false;
      }
     }else $Meld=MP_TxSessionUngueltig;
     $rR->close();
    }else $Meld=MP_TxSqlFrage;
  }}else $Meld=MP_TxSessionZeit;
 }
 if($bSesOK) if(MP_NEingabeAnders) $aMpEF=$aMpNE;

 if($nNuPos>1&&MP_NEingabeLogin){ //Login zwingend noetig
  $bCaptcha=MP_Captcha||MP_LoginCaptcha;
  if($bSesOK){$bFormEingabe=true; $bCaptcha=false;}
  elseif($Meld==''){$Meld=MP_TxNutzerLogin; $MTyp='Meld';}
 }else $bFormEingabe=true;

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET - Datensatz bereitstellen
  if($sAltNr=(isset($_GET['mp_Nummer'])?sprintf('%d',$_GET['mp_Nummer']):'')){ //Datensatznummer vorhanden
   if(!MP_SQL){ //Textdaten holen
    $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD); $s=$sAltNr.';1;'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     if(substr($aD[$i],0,$p)==$s){$aW=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); $bOkD=true; break;}
    }
   }elseif($DbO){ //SQL-Daten
    if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sAltNr.'" AND online="1"')){
     if($aW=$rR->fetch_row()) $bOkD=true; else $aW=array(); $rR->close();
    }else $Meld=MP_TxSqlFrage;
   }
   if($bOkD){
    $sAblaufDat=$aW[2]; $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='9';
    if(MP_SuchArchiv) if(isset($_GET['mp_Archiv'])){$sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';} //Archivsuche
    if($sAblaufDat<$sIntervallAnfang||$sAblaufDat>$sIntervallEnde){$bOkD=false; $Meld=MP_TxNummerUngueltig;}
   }
   if($bOkD){ //vorhanden
    $sSta=$aW[1]; array_splice($aW,1,1); $aW[1]=fMpAnzeigeDatum($sAblaufMax); $nDatFeld2=0;
    for($i=2;$i<$nFelder;$i++){
     //if(MP_LZeichenstz>0) if(MP_LZeichenstz==2) $aW[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$i]); else $s=html_entity_decode($aW[$i]);
     switch($aMpFT[$i]){
      case 'd': if($aW[$i]) $aW[$i]=fMpAnzeigeDatum($aW[$i]); break;
      case 'b': case 'f': $aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p); break;
      case 'w': case 'n': case '1': case '2': case '3': case 'r': $aW[$i]=str_replace('.',MP_Dezimalzeichen,$aW[$i]); break;
      case 'e': case 'c': if(!MP_SQL) $aW[$i]=fMpDeCode($aW[$i]); break;
      case 'p': $aW[$i]=fMpDeCode($aW[$i]); break;
      case '@': $aW[$i]=trim(fMpAnzeigeDatum(date('Y-m-d')).date(' H:i')); break;
    }}
   }else{if($Meld=='') $Meld=MP_TxNummerUnbek; define('MP_410Gone',true);}
  }else{ //keine Datensatznummer
   $Meld=MP_TxNummerFehlt; $MTyp='Meld';
  }//Datensatznummer
  if(MP_NutzerEMail){if(!$p=array_search('c',$aMpFT)) $p=array_search('e',$aMpFT); if($p) $aW[$p]=$sNutzerEml;} //NutzerEMail
  if($nGhPos) if($bGhNum?$nGh<=0:$nGh<date('Y-m-d')) if(!$Meld) $Meld=MP_TxGuthaben0;

  if($bCaptcha){ //Captcha erzeugen
   $sCapTyp=MP_CaptchaTyp; $bCapOk=false; $bCapErr=false;
   require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(MP_CaptchaTxFarb,MP_CaptchaHgFarb);
  }
 }else{ //POST Formularauswertung
  if($bCaptcha){ //Captcha behandeln
   $sCapTyp=(isset($_POST['mp_CaptchaTyp'])?$_POST['mp_CaptchaTyp']:MP_CaptchaTyp); $bCapOk=false; $bCapErr=false;
   require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
   $sCap=(isset($_POST['mp_CaptchaFrage'])?$_POST['mp_CaptchaFrage']:''); $sCap=(MP_Zeichensatz<=0?$sCap:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test((isset($_POST['mp_CaptchaAntwort'])?$_POST['mp_CaptchaAntwort']:''),(isset($_POST['mp_CaptchaCode'])?$_POST['mp_CaptchaCode']:''),$sCap)) $bCapOk=true;
   else{$bCapErr=true; $aFehl[0]=true;}
  }
  $sZ=''; $sF=''; $sFehl=''; $sSta=(isset($_POST['mp_Sta'])?sprintf('%0d',$_POST['mp_Sta']):(MP_Direkteintrag?'1':'2')); $sAltNr=(isset($_POST['mp_Nummer'])?sprintf('%d',$_POST['mp_Nummer']):'');
  $bOkD=true; $bUtf8=((isset($_POST['mp_JSSend'])||$_POST['mp_Utf8']=='1')?true:false);
  if(MP_TxAgfFeld>'')
   if(isset($_POST['mp_Agb'])&&$_POST['mp_Agb']=='1') $bAgb=true; else $aFehl['Agb']=true;
  for($i=1;$i<$nFelder;$i++) //mp_Oh: hochgeladene  mp_Oa: alte
   {$aOh[$i]=(isset($_POST['mp_Oh'.$i])?$_POST['mp_Oh'.$i]:''); $aOa[$i]=(isset($_POST['mp_Oa'.$i])?$_POST['mp_Oa'.$i]:'');}
  for($i=1;$i<$nFelder;$i++) if($aMpEF[$i]||$i==1){ //alle Eingabefelder
   $s=str_replace('~@~','\n ',stripslashes(@strip_tags(str_replace('\n ','~@~',str_replace("\r",'',trim($_POST['mp_F'.$i]))))));
   $t=$aMpFT[$i]; $aOs[$i]=''; // $aOs: zu speichernde
   if(strlen($s)>0||!$aMpPF[$i]||$t=='b'||$t=='f'||$t=='@'){
    if($bUtf8||MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); elseif(MP_Zeichensatz==1) $s=html_entity_decode($s);
    if($t!='m') $s=str_replace('"',"'",$s); $v=$s; //s:Eingabe, v:Speicherwert
    switch($t){
    case 't': case 'm': case 'a': case 'k': case 's': case 'j': case 'v': case 'x': break; //Text,Memo,Kategorie,Auswahl,Ja/Nein,StreetMap
    case 'd': if($s) //Datum
     if($v=fMpErzeugeDatum($s)){$s=fMpAnzeigeDatum($v); if($i==1&&($v<date('Y-m-d',time()-86400*MP_BearbAltesNochTage)||$v>$sAblaufMax)){$sFehl=MP_TxExIntervall; $aFehl[1]=true;}}
     else $aFehl[$i]=true; break;
    case '@': //EintragsDatum
     $v=date('Y-m-d H:i'); $s=fMpAnzeigeDatum($v).date(' H:i'); break;
    case 'z': //Uhrzeit
     if($s){$a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',$a[0],(isset($a[1])?$a[1]:0)); $v=$s;} break;
    case 'e': case 'c': // e-Mail, Kontakt-e-Mail
     if($s) if(!fMpIsEMailAdr($s)) $aFehl[$i]=true;
     if(!MP_SQL) $v=fMpEnCode($s); break;
    case 'l': //Link oder e-Mail
     if($p=strpos(strtolower(substr($s,0,7)),'ttp://')){$s=substr($s,$p+6); $v=$s;} break;
    case 'b': //Bild
     if($aOh[$i]>'') $v=$aOh[$i]; else{if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]=='1') $aOa[$i]=''; $v=$aOa[$i];} //mp_Up: neue Datei; mp_Dl: zu loeschen
     $UpNaJS=(isset($_POST['mp_UpNa_'.$i])?fMpDateiname(basename($_POST['mp_UpNa_'.$i])):'');
     $UpNa=(isset($_FILES['mp_Up'.$i])?fMpDateiname(basename($_FILES['mp_Up'.$i]['name'])):'');
     if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
     if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
      if($_FILES['mp_Up'.$i]['size']<=(1024*MP_BildMaxKByte)||MP_BildMaxKByte<=0){
       if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['mp_Up'.$i]['tmp_name']);
       elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['mp_Up'.$i]['tmp_name']);
       elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['mp_Up'.$i]['tmp_name']);
       if(!empty($Src)){
        if($sAlt=$aOh[$i]){ //alte Uploads weg
         $p=strpos($sAlt,'|'); if(file_exists(MP_Pfad.'temp/-'.substr($sAlt,0,$p))) unlink(MP_Pfad.'temp/-'.substr($sAlt,0,$p)); if(file_exists(MP_Pfad.'temp/_'.substr($sAlt,$p+1))) unlink(MP_Pfad.'temp/_'.substr($sAlt,$p+1)); $aOh[$i]='';
        }
        $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $sAlt='#|'.implode('|',$aOh); $sZhl='A';
        if(strpos($sAlt,'|'.$UpBa.$UpEx)){while(strpos($sAlt,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelname
        if($Sx>MP_VorschauBreit||$Sy>MP_VorschauHoch){ //Vorschau verkleinern
         $Dw=min(MP_VorschauBreit,$Sx);
         if($Sx>MP_VorschauBreit) $Dh=round(MP_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>MP_VorschauHoch){$Dw=round(MP_VorschauHoch/$Dh*$Dw); $Dh=MP_VorschauHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         if(is_writable(MP_Pfad.'temp')&&@imagejpeg($Dest,MP_Pfad.'temp/-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
         imagedestroy($Dest); unset($Dest);
        }else{
         if(is_writable(MP_Pfad.'temp')&&@copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
        }
        if($Sx>MP_BildBreit||$Sy>MP_BildHoch){ //Bild verkleinern
         $Dw=min(MP_BildBreit,$Sx);
         if($Sx>MP_BildBreit) $Dh=round(MP_BildBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>MP_BildHoch){$Dw=round(MP_BildHoch/$Dh*$Dw); $Dh=MP_BildHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         if(is_writable(MP_Pfad.'temp')) @imagejpeg($Dest,MP_Pfad.'temp/_'.$UpBa.'.jpg');
         $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
        }else{$v.=$UpBa.$UpEx; if(is_writable(MP_Pfad.'temp')) @copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/_'.$UpBa.$UpEx);}
        imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
       }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,MP_TxBildOeffnen);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',MP_BildMaxKByte,MP_TxBildGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),MP_TxBildTyp);
     }elseif($s>'') if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]!=''){ //hochgeladenes Bild loeschen
      $p=strrpos($s,'.'); if(file_exists(MP_Pfad.'temp/-'.$s)) unlink(MP_Pfad.'temp/-'.$s); if(file_exists(MP_Pfad.'temp/_'.$s)) unlink(MP_Pfad.'temp/_'.$s);
      if(strtolower(substr($s,$p))!='.jpg'){
       if(file_exists(MP_Pfad.'temp/-'.substr($s,0,$p).'.jpg')) unlink(MP_Pfad.'temp/-'.substr($s,0,$p).'.jpg'); if(file_exists(MP_Pfad.'temp/_'.substr($s,0,$p).'.jpg')) unlink(MP_Pfad.'temp/_'.substr($s,0,$p).'.jpg');
      }
      $s=''; $v=''; $aOh[$i]='';
     }
     $aOs[$i]=$v; break;
    case 'f': //Datei
     if($aOh[$i]>'') $v=$aOh[$i]; else{if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]=='1') $aOa[$i]=''; $v=$aOa[$i];}
     $UpNa=(isset($_FILES['mp_Up'.$i])?fMpDateiname(basename($_FILES['mp_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
     if($UpEx&&$UpEx!='.php'&&$UpEx!='.php3'&&$UpEx!='.php5'&&$UpEx!='.pl'){
      if($_FILES['mp_Up'.$i]['size']<=(1024*MP_DateiMaxKByte)){
       if($aOh[$i]>''){if(file_exists(MP_Pfad.'temp/'.$aOh[$i])) unlink(MP_Pfad.'temp/'.$aOh[$i]); $aOh[$i]='';} //alten Upload weg
       $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $u='#|'.implode('|',$aOh); $sZhl='A';
       if(strpos($u,'|'.$UpBa.$UpEx)){while(strpos($u,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelnamen
       if(is_writable(MP_Pfad.'temp')&&@copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/'.$UpBa.$UpEx)){$s=$UpBa.$UpEx; $v=$s; $aOh[$i]=$v;}
       else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',MP_DateiMaxKByte,MP_TxDateiGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),MP_TxDateiTyp);
     }elseif($s>'') if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]!=''){ //hochgeladene Datei loeschen
      if(file_exists(MP_Pfad.'temp/'.$s)) unlink(MP_Pfad.'temp/'.$s); $s=''; $v=''; $aOh[$i]='';
     }
     $aOs[$i]=$v; break;
    case 'w': //Waehrung
     $v=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),MP_Dezimalstellen,'.','');
     $s=number_format((float)$v,MP_Dezimalstellen,MP_Dezimalzeichen,''); break;
    case 'n': case '1': case '2': case '3': //Zahl
     $v=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),(int)$t,'.','');
     $s=number_format((float)$v,(int)$t,MP_Dezimalzeichen,''); break;
    case 'r': //Zahl
     $v=str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s));
     $s=str_replace('.',MP_Dezimalzeichen,$v); break;
    case 'o': //PLZ
     if($s) if(MP_PLZLaenge>0&&strlen($s)!=MP_PLZLaenge) $aFehl[$i]=true; break;
    case 'p': $v=fMpEnCode($s); break; //Passwort
    case 'u': $s=sprintf('%04d',substr(MP_Session,0,4)); $v=$s; break; //Benutzernummer
    }$aW[$i]=$s;
    //if(MP_SZeichenstz!=0) if(MP_SZeichenstz==2) $v=iconv('ISO-8859-1','UTF-8',$v); else $v=htmlentities($v);
    if(!MP_SQL) $sZ.=';'.str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
    else{$sZ.=',"'.str_replace("\n","\r\n",str_replace('\n ',"\n",str_replace('"','\"',$v))).'"'; $sF.=',mp_'.$i;}
   }else{$aFehl[$i]=true; if(!MP_SQL) $sZ.=';';}
  }elseif($aMpFT[$i]=='u'){
   if(!MP_SQL) $sZ.=';'.sprintf('%04d',substr(MP_Session,0,4));
   else{$sZ.=',"'.sprintf('%04d',substr(MP_Session,0,4)).'"'; $sF.=',mp_'.$i;}
  }elseif(!MP_SQL) $sZ.=';';

  if(MP_EintragDSE1) if(isset($_POST['mp_DSE1'])&&$_POST['mp_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
  if(MP_EintragDSE2) if(isset($_POST['mp_DSE2'])&&$_POST['mp_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}

  if($nGhPos<=0||($bGhNum?$nGh>0:$nGh>=date('Y-m-d'))){ //Guthaben OK
  if($sFehl==''){ //alles OK, eintragen
   if(count($aFehl)==0){
    $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*MP_HalteAltesNochTage)) $sRefDat='';
    $sMlTx=MP_TxStatus.': '.($sSta=='2'?MP_TxVormerk:($sSta!='0'?MP_TxOnline:MP_TxOffline))."\n\n";
    if(!MP_SQL){ //ohne SQL
     $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD); $aTmp=array();
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $s=rtrim($aD[$i]); $p=strpos($s,';'); $nId=max($nId,(int)substr($s,0,$p));
      if(substr($s,$p+3,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
      else $aLsch[(int)substr($s,0,$p)]=true;
     }
     $aTmp[(++$nId).';'.(MP_Direkteintrag?$sSta:'2')]=substr($sZ,1); //Inserat anhaengen
     $sMlTx.=strtoupper(MP_TxNummer).': '.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId); $aD=array();
     $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aMpFN[$i]; $aD[0]=$s."\n";
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v){$aD[]=$k.';'.$v."\n";}
     if(is_writable(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)&&($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w'))){//neu schreiben
      fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f); $bOK=true;
     }else if($Meld=='') $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
    }elseif($DbO){ //bei SQL
     if($DbO->query('INSERT IGNORE INTO '.str_replace('%',$sSegNo,MP_SqlTabI).' (online'.$sF.') VALUES("'.(MP_Direkteintrag?$sSta:'2').'"'.$sZ.')')){
      if($nId=$DbO->insert_id){
       $bOK=true; $sMlTx=strtoupper(MP_TxNummer).': '.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId);
       if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"')){
        while($a=$rR->fetch_row()) $aLsch[(int)($a[0].$sSegNo)]=true; $rR->close();
        $DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"');
       }
      }else $Meld=MP_TxSqlEinfg;
     }else $Meld=MP_TxSqlEinfg;
    }//SQL
    if($bOK){ //Daten gespeichert
     $Meld=(MP_Direkteintrag?($sSta=='1'?MP_TxEingabeErfo:MP_TxEingabeOffl):MP_TxVormerkErfo); $MTyp='Erfo'; $sMlTo=$sNutzerEml; $sMlLs=$sMlTx;
     if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
     if($nGhPos) if($bGhNum&&$nGh>0){ //Guthaben abbuchen
      if(!MP_SQL){ //ohne SQL
       $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
       for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
        $aN[$nGhPos]=--$nGh; $k=count($aNF)-1;
        $s.=substr($sSes,4); for($j=1;$j<$k;$j++) $s.=';'.$aN[$j]; $aD[$i]=$s."\n";
        if(is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=@fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){ //Guthaben neu schreiben
         fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
        }break;
       }
      }elseif($DbO){ //bei SQL
       $DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET dat_'.$nGhPos.'="'.(--$nGh).'" WHERE nr="'.$nNId.'" AND session="'.$nTm.'"');
      }//SQL
     }

     if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
     if(MP_MailListeNDetail) $aMpDF=$aMpND;
     for($i=1;$i<$nFelder;$i++) if($aMpEF[$i]){$t=$aMpFT[$i]; //Restarbeiten
      if($UpNa=$aOh[$i]){ //neue Bilder und Dateien umspeichern
       if($t=='b'){
        $p=strpos($UpNa,'|'); $UpNa=substr($UpNa,0,$p);
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.'temp/-'.$UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'-'.$UpNa)) $bOK=false;
        if(file_exists(MP_Pfad.'temp/-'.$UpNa)) unlink(MP_Pfad.'temp/-'.$UpNa); $UpNa=substr($aOh[$i],$p+1);
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.'temp/_'.$UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'_'.$UpNa)) $bOK=false;
        if(file_exists(MP_Pfad.'temp/_'.$UpNa)) unlink(MP_Pfad.'temp/_'.$UpNa);
       }elseif($t=='f'){
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.'temp/'. $UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'~'.$UpNa)) $bOK=false;
        if(file_exists(MP_Pfad.'temp/'.$UpNa)) unlink(MP_Pfad.'temp/'.$UpNa);
       }
       if(!$bOK) $Meld.='</p><p class="mpFehl">'.str_replace('#','<i>'.MP_Bilder.$sBldDir.$UpNa.'</i>',MP_TxDateiRechte); $bOK=true;
      }elseif($UpNa=$aOs[$i]){ //vorhandene Bilder und Dateien umspeichern
       if($t=='b'){
        $p=strpos($UpNa,'|'); $UpNa=substr($UpNa,0,$p);
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sAltNr.$sBldSeg.'-'.$UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'-'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
        $UpNa=substr($aOs[$i],$p+1);
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sAltNr.$sBldSeg.'_'.$UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'_'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
       }elseif($t=='f'){
        if(!is_writable(MP_Pfad.MP_Bilder.$sBldDir)||!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sAltNr.$sBldSeg.'~'.$UpNa,MP_Pfad.MP_Bilder.$sBldDir.$nId.$sBldSeg.'~'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
       }
       if(!$bOK) $Meld.='</p><p class="mpFehl">'.str_replace('#','<i>'.MP_Bilder.$sBldDir.$UpNa.'</i>',MP_TxDateiRechte); $bOK=true;
      }
      if($t!='c'&&$t!='e'&&$t!='p'){
       $sMlTx.="\n".strtoupper($aMpFN[$i]).': '.($t!='u'?$aW[$i]:$sNutzerName);
       if($aMpDF[$i]) $sMlLs.="\n".strtoupper($aMpFN[$i]).': '.($t!='u'?$aW[$i]:$sNutzerMailing);
      }else $sMlTx.="\n".strtoupper($aMpFN[$i]).': '.$aW[$i];
      if($t=='c'&&$aW[$i]) $sMlTo=$aW[$i]; //Kontakt
      elseif($t=='e'&&($sMlTo==''||($aW[$i]&&!in_array('c',$aMpFT)))) $sMlTo=$aW[$i]; //e-Mail
      elseif($t=='l'&& $sMlTo==''&&strpos($aW[$i],'@')>0&&!(in_array('e',$aMpFT))) $sMlTo=$aW[$i]; //Link
     }elseif($aMpFT[$i]=='u'){
      $sMlTx.="\n".strtoupper($aMpFN[$i]).': '.$sNutzerName;
      if($aMpDF[$i]) $sMlLs.="\n".strtoupper($aMpFN[$i]).': '.$sNutzerMailing;
     }
     if(MP_EintragAdminInfo){ //Mails senden
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailTo); $s=$sSegNo.$nId; $c=(int)MP_Schluessel; for($i=strlen($s);$i>=0;--$i) $c+=(int)substr($s,$i,1); $c='?i='.$s.'&c='.$c;
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),MP_TxEintragAdminBtr));
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#C',$c,str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),str_replace('\n ',"\n",MP_TxEintragAdminTxt)))));
      $Mailer->Send();
     }
     if(MP_EintragMail&&$sMlTo!=''){
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sMlTo); $sWww=fMpWww();
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),MP_TxEintragBtr)));
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender); $Mailer->SetReplyTo($sMlTo);
      $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),str_replace('\n ',"\n",MP_TxEintragTxt)))));
      $Mailer->Send();
     }
     if($sSta>'0'&&MP_MailListeEintrag&&MP_MailListeAdr!=''){//Mailingliste
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailListeAdr); $sWww=fMpWww();
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),MP_TxMailListeBtr)));
      $s=MP_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetReplyTo($s,$t);
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlLs,str_replace('#A',$sWww,str_replace('#S',MP_SegName,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId),str_replace('\n ',"\n",MP_TxMailListeTxt)))));
      $Mailer->Send();
     }
     if(count($aLsch)>0&&(in_array('b',$aMpFT)||in_array('f',$aMpFT))){ //veraltete Bilder und Dateien
      if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){$aD=array();
       while($s=readdir($f)) if($i=(int)$s){
        if(MP_BldTrennen){if(isset($aLsch[$i])) $aD[]=$s;}
        elseif(substr($i,-2)==$sSegNo) if(isset($aLsch[(int)substr($i,0,-2)])) $aD[]=$s;
       }
       closedir($f); foreach($aD as $s) if(file_exists(MP_Pfad.MP_Bilder.$sBldDir.$s)) unlink(MP_Pfad.MP_Bilder.$sBldDir.$s);
     }}
    }
   }else $Meld=MP_TxEingabeFehl;
  }else $Meld=$sFehl;
  }else if(!$Meld) $Meld=MP_TxGuthaben0;
 }//POST

 //Beginn der Ausgabe
 if($Meld==''){$Meld=MP_TxEingabeMeld; $MTyp='Meld';}
 $X="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';
 if(isset($_GET['mp_Seite'])) $sMpSeite=sprintf('%d',$_GET['mp_Seite']); else $sMpSeite='';
 if(MP_DSEPopUp&&(MP_EintragDSE1||MP_EintragDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.MP_DSEPopupW.',height='.MP_DSEPopupH.',left='.MP_DSEPopupX.',top='.MP_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';

 $sAjaxURL=MP_Url; $bWww=(strtolower(substr(fMpWww(),0,4))=='www.');
 if($bWww&&!strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://','://www.',$sAjaxURL);
 elseif(!$bWww&&strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://www.','://',$sAjaxURL);

 if($bCaptcha) $X.="\n
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

 if($bFormEingabe){ //Eingabeformular

 //Formular- und Tabellenanfang
 if(in_Array('x',$aMpFT)) $X.="\n\n".'<script type="text/javascript">function GmWin(sUrl){gmWin=window.open(sUrl,"gmwin","width='.(min(max(MP_GMapBreit,500),725)+50).',height=700,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");gmWin.focus();}</script>';
 if(MP_FormatCode) $X.="\n\n".'<script src="'.MP_Url.'mpEingabe.js" type="text/javascript"></script>'."\n";
 $X.="\n".'<form class="mpEing" name="mpEingabe" action="'.fMpHref('kopieren',$sMpSeite,$sAltNr,'').'" onsubmit="return formSend()" enctype="multipart/form-data" method="post"><input type="hidden" name="mp_Dmy" value="xx" />'.rtrim("\n".MP_Hidden);
 $X.="\n".'<input type="hidden" name="mp_Aktion" value="eingabe" />';
 $X.="\n".'<input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />';
 $X.="\n".'<script type="text/javascript">';
 $X.="\n".' var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";';
 $X.="\n".' if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";';
 $X.="\n"." document.writeln('<input type=\"hidden\" name=\"mp_Utf8\" value=\"'+sUtf8+'\" />');";
 $X.="\n".'</script>';
 if(MP_Session!='') $X.="\n".'<input type="hidden" name="mp_Session" value="'.MP_Session.'" />';
 if(isset($sAltNr)&&$sAltNr>'') $X.="\n".'<input type="hidden" name="mp_Nummer" value="'.$sAltNr.'" />';
 $X.="\n".'<div class="mpTabl">';

 //Eingabeformularzeilen
 $nFarb=2; $bAblaufHide=false; $bMitBild=false;
 if($bOkD) for($i=1;$i<$nFelder;$i++) if($aMpEF[$i]||($i==1&&$aMpPF[1])){
  $t=$aMpFT[$i]; $v=(isset($aW[$i])?str_replace('`,',';',$aW[$i]):''); //Feldinhalt
  $X.="\n".' <div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
  $X.="\n".'  <div class="mpTbSp1"><div id="mpLabel'.$i.'">'.fMpTx($aMpFN[$i]); // Feldname
  if($aMpPF[$i]) $X.='*';
  if($t=='x') $X.='&nbsp;<a href="'.$sAjaxURL.(MP_GMapSource=='O'?'openstreet':'google').'map.php?'.$i.($v?','.$v:'').'" target="gmwin" onclick="GmWin(this.href);"><img src="'.MP_Url.'grafik/iconAendern.gif" width="12" height="13" border="0" title="'.fMpTx(MP_TxKoordinaten).'" alt="'.fMpTx(MP_TxKoordinaten).'" /></a>';
  $X.='</div></div>'."\n".'  <div class="mpTbSp2">'; $sZ="\n".'   <div class="mp'.(isset($aFehl[$i])&&$aFehl[$i]?'Fhlt':'Eing').'">';
  switch($t){
  case 't': case 'l': case 'e': case 'c': case 'x': // Text, Link, e-Mail, Kontakt, StreetMap
   if($t=='t') $v=str_replace('\n ',"\n",$v);
   $X.=$sZ.'<input class="mpEing" type="text" name="mp_F'.$i.'" value="'.str_replace("\n",'\n ',str_replace("\r",'',fMpTx($v))).'" maxlength="'.(isset($aMpEL[$i])&&$aMpEL[$i]>0?$aMpEL[$i]:'255').'" /></div>';
   break;
  case 'm': // Memo
   if(MP_FormatCode) $X.="\n".'   <div title="'.fMpTx(MP_TxBB_X).'">'."\n".fMpBBToolbar('mp_F'.$i); else $X.="\n".'   <div>';
   $X.=$sZ.'<textarea class="mpEing" name="mp_F'.$i.'" '.($aMpEL[$i]>0?'maxlength="'.$aMpEL[$i].'" ':'').'cols="80" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="initInsertions('."'mp_F".$i."'".');">'.fMpTx($v).'</textarea></div>'."\n".'   </div>';
   break;
  case 'a': //Aufzaehlung
   $aHlp=isset($aMpAW[$i])?explode('|','|'.$aMpAW[$i]):array(''); $nW=15; $sO=''; foreach($aHlp as $w){$sO.='<option value="'.fMpTx($w).'"'.($v==$w?' selected="selected"':'').'>'.fMpTx($w).'</option>'; $nW=max(strlen($w),$nW);}
   $X.=$sZ.'<select class="mpEing" style="max-width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 'k': //Kategorie
   reset($aMpKW); $sO=''; $nW=15; foreach($aMpKW as $w){$sO.='<option value="'.fMpTx($w).'"'.($v==$w?' selected="selected"':'').'>'.fMpTx($w).'</option>'; $nW=max(strlen($w),$nW);}
   $X.=$sZ.'<select class="mpEing" style="max-width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 's': //Symbol
   reset($aMpSW); $sO=''; $nW=15; foreach($aMpSW as $w){$sO.='<option value="'.fMpTx($w).'"'.($v==$w?' selected="selected"':'').'>'.fMpTx($w).'</option>'; $nW=max(strlen($w),$nW);}
   $X.=$sZ.'<select class="mpEing" style="max-width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 'd': //Datum
   if($aMpEF[$i])
    $X.=$sZ.'<input class="mpEing mpEin7" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="10" /> <span class="mpMini">'.fMpTx(MP_TxFormat).' '.fMpDatumsFormat().($i!=1?'':', '.fMpTx(MP_TxMaximal).'&nbsp;'.fMpAnzeigeDatum($sAblaufMax)).'</span></div>';
   else $X.=fMpAnzeigeDatum($sAblaufMax).'<input type="hidden" name="mp_F'.$i.'" value="'.fMpAnzeigeDatum($sAblaufMax).'" /></div>';
   break;
  case '@': $X.=$sZ.$v.'<input type="hidden" name="mp_F'.$i.'" value="'.$v.'" /></div>'; break;
  case 'z': //Zeit
   $X.=$sZ.'<input class="mpEing mpEin7" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="5" /> <span class="mpMini">'.fMpTx(MP_TxFormat.' '.MP_TxSymbUhr).'</span></div>';
   break;
  case 'j': case 'v': //Ja/Nein
   $X.=$sZ.'<input class="mpRadio" type="radio" name="mp_F'.$i.'" value="J"'.($v!='J'?'':' checked="checked"').' /> '.fMpTx(MP_TxJa).' &nbsp; <input class="mpRadio" type="radio" name="mp_F'.$i.'" value="N"'.($v!='N'?'':' checked="checked"').' /> '.fMpTx(MP_TxNein).'</div>';
   break;
  case 'w': //Waehrung
   $X.=$sZ.'<input class="mpEing mpEin7" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="16" /> '.MP_Waehrung.'</div>';
   break;
  case 'n': case 'r': case '1': case '2': case '3': case 'o': //Zahlen
   $X.=$sZ.'<input class="mpEing mpEin7" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="'.(isset($aMpEL[$i])&&$aMpEL[$i]>0?$aMpEL[$i]:'12').'" />'.($t!='o'?'':' <span class="mpMini">'.(MP_PLZLaenge>0?MP_PLZLaenge.' '.fMpTx(MP_TxStellen):'').'</span>').'</div>';
   break;
  case 'b': //Bild
   $X.=$sZ.'<input class="mpEing" type="file" name="mp_Up'.$i.'" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif" /><input type="hidden" name="mp_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>'; $bMitBild=true;
   if($v) $X.="\n".'   <div class="mpNorm" style="float:left;"><input class="mpCheck" type="checkbox" name="mp_Dl'.$i.'" value="1" /><input type="hidden" name="mp_F'.$i.'" value="'.$v.'" /><input type="hidden" name="mp_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="mpMini">'.$v.' '.fMpTx(MP_TxLoeschen).'</span></div>';
   $X.="\n".'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="mpMini">'.(MP_BildMaxKByte>0?'('.fMpTx(MP_TxMaximal).' '.MP_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': //Datei
   $X.=$sZ.'<input class="mpEing" type="file" name="mp_Up'.$i.'" onchange="loadDatFile(this)" /><input type="hidden" name="mp_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>';
   if($v) $X.="\n".'   <div class="mpNorm" style="float:left;"><input class="mpCheck" type="checkbox" name="mp_Dl'.$i.'" value="1" /><input type="hidden" name="mp_F'.$i.'" value="'.$v.'" /><input type="hidden" name="mp_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="mpMini">'.$v.' '.fMpTx(MP_TxLoeschen).'</span></div>';
   $X.="\n".'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="mpMini">('.fMpTx(MP_TxMaximal).' '.MP_DateiMaxKByte.' KByte)</span></div>';
   break;
  case 'x':
   $X.=$sZ.'<div style="text-align:right;float:right;padding-top:2px;"><a href="'.$sRelPfad.(MP_GMapSource=='O'?'openstreet':'google').'map.php?'.$i.($v?','.$v:'').'" target="geown" onclick="geoWin(this.href);return false;"><img src="iconAendern.gif" width="12" height="13" border="0" title="Koordinaten bearbeiten" alt="Koordinaten bearbeiten"></a></div><div style="margin-right:18px;"><input class="mpEing" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="255" /></div></div>';
   break;
  case 'u': // Benutzername
   $X.=$sZ.'<input class="mpEing mpEin7" type="text" name="mp_F'.$i.'" value="'.sprintf('%04d',substr(MP_Session,0,4)).'" readonly="readonly" /></div>';
   break;
  case 'p': // Passwort
   $X.=$sZ.'<input class="mpEing mpEin12" type="password" name="mp_F'.$i.'" value="'.$v.'" maxlength="'.($aMpEL[$i]>0?$aMpEL[$i]:'16').'" /> <span class="mpMini">'.fMpTx(MP_TxPassRegel).'</span></div>';
   break;
  }
  if($i>1&&($v=$aMpET[$i])) $X.="\n".'   <div class="mpNorm"><span class="mpMini">'.str_replace('`,',';',fMpTx($v)).'</span></div>'; // Eingabehilfe
  $X.="\n".'  </div>'."\n".' </div>';
  if(isset($aMpTZ[$i])&&$aMpTZ[$i]>'0'){
   if(++$nFarb>2) $nFarb=1;
   $X.="\n".' <div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1">&nbsp;</div><div class="mpTbSp2">&nbsp;</div></div>';
  }
 }elseif($i==1) $bAblaufHide=true; //unsichtbar

 if($bOkD){
  if($nGhPos){ //Guthabenanzeige
   if(++$nFarb>2) $nFarb=1;
   $X.="\n".' <div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1">'.fMpTx(MP_TxGuthaben).'</div><div class="mpTbSp2"><div class="mp'.(($bGhNum?$nGh>0:$nGh>=date('Y-m-d'))?'Eing':'Fhlt').'">'.($bGhNum?$nGh:fMpTx(MP_TxBis).' '.fMpAnzeigeDatum($nGh)).' '.fMpTx(MP_TxInserate).'</div></div></div>';
  }

  if(MP_TxAgfFeld>''){
   if(++$nFarb>2) $nFarb=1; $s=fMpTx(MP_TxAgfText);
   if($n=strrpos($s,']')){
    $s=substr_replace($s,'</a>',$n,1);
    if($n=strpos($s,'[')){
     $s=substr_replace($s,'<a class="mpDetl" href="'.MP_AgfLink.'">',$n,1);
     if(MP_AgbPopup) $s=substr_replace($s,' target="'.(MP_AgbZiel?MP_AgbZiel:'txwin').'" onclick="TxWin(this.href);return false;"',$n+2,0);
     elseif(MP_AgbZiel) $s=substr_replace($s,' target="'.MP_AgbZiel.'"',$n+2,0);
    }
   }
   $X.="\n".' <div class="mpTbZl'.$nFarb.'">
    <div class="mpTbSp1">'.fMpTx(MP_TxAgfFeld).'*</div>
    <div class="mpTbSp2"><div class="mp'.(isset($aFehl['Agb'])&&$aFehl['Agb']?'Fhlt':'Eing').'"><input class="mpCheck" type="checkbox" name="mp_Agb" value="1"'.(isset($bAgb)?' checked="checked"':'').' /> '.$s.'</div></div>
   </div>';
  }

  if(MP_EintragDSE1||MP_EintragDSE2){ if(--$nFarb<=0) $nFarb=2;
   if(MP_EintragDSE1) $X.="\n".' <div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE1?'Fhlt':'Eing').'">'.fMpDSEFld(1,$bDSE1).'</div></div></div>';
   if(MP_EintragDSE2) $X.="\n".' <div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE2?'Fhlt':'Eing').'">'.fMpDSEFld(2,$bDSE2).'</div></div></div>';
  }

  if(MP_AendernOnOff&&$bSesOK&&MP_Direkteintrag){ //Direkteintrag
   if(--$nFarb<=0) $nFarb=2;
   $X.="\n".' <div class="mpTbZl'.$nFarb.'">
   <div class="mpTbSp1">'.fMpTx(MP_TxStatus).'</div>
   <div class="mpTbSp2"><input class="mpRadio" type="radio" name="mp_Sta" value="1"'.($sSta=='1'?' checked="checked"':'').' /> '.fMpTx(MP_TxOnline).' &nbsp; <input class="mpRadio" type="radio" name="mp_Sta" value="0"'.($sSta!='1'?' checked="checked"':'').' /> '.fMpTx(MP_TxOffline).'</div>
  </div>';
  }

  if($bCaptcha){ //Captcha-Zeile
   if(++$nFarb>2) $nFarb=1;
   $X.="\n".' <div class="mpTbZl'.$nFarb.'">
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
  if(++$nFarb>2) $nFarb=1; //Pflichtzeile
  $X.="\n".' <div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1">&nbsp;</div><div class="mpTbSp2 mpTbSpR">* <span class="mpMini">'.fMpTx(MP_TxPflicht).'</span></div></div>';
  $X.="\n".'</div>'; //Tabelle
  if($bAblaufHide) $X.="\n".'<input type="hidden" name="mp_F1" value="'.fMpAnzeigeDatum($sAblaufMax).'" />';
  $X.="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';
  if(!$bOK) $X.="\n".'<div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxEingabe).'" title="'.fMpTx(MP_TxEingabe).'" /></div>';
 }
 $X.="\n".'</form>'."\n";

 if(MP_TxAgfFeld>''&&MP_AgbPopup) $X.="\n".'<script type="text/javascript">function TxWin(sUrl){txWin=window.open(sUrl,"'.(MP_AgbZiel?MP_AgbZiel:'txwin').'","width='.MP_AgbBreit.',height='.MP_AgbHoch.',left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");txWin.focus();}</script>';

 if($bMitBild && MP_BildResize){
  $X.="\n".'<script src="'.MP_Url.'mpEingabeBild.js" type="text/javascript"></script>';
  $X.="\n".'<script type="text/javascript">';
  $X.="\n".' sPostURL="'.fMpHref('kopieren',$sMpSeite,$sAltNr,'').'";';
  $X.="\n".' nBildBreit='.MP_BildBreit.'; nBildHoch='.MP_BildHoch.';';
  $X.="\n".' nThumbBreit='.MP_ThumbBreit.'; nThumbHoch='.MP_ThumbHoch.';';
  $X.="\n".'</script>';
 }else{
  $X.="\n".'<script type="text/javascript">';
  $X.="\n".' function formSend(){return true;} // normales Senden ohne Bilder;';
  $X.="\n".' function loadDatFile(inputField){return false;}';
  $X.="\n".' function loadImgFile(inputField){return false;}';
  $X.="\n".'</script>';
 }

 }else{ //Ende Eingabefomular - Beginn Loginformular

 $aW[2]=''; if(isset($_GET['mp_Seite'])) $sMpSeite=sprintf('%d',$_GET['mp_Seite']); else $sMpSeite='';
 $X.='
 <p class="mpMeld" style="margin-top:20px;">'.fMpTx(MP_TxLoginLogin).'</p>
 <form class="mpLogi" action="'.fMpHref('login',$sMpSeite).'" method="post">
 <input type="hidden" name="mp_Aktion" value="login" />
 <input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />
 <input type="hidden" name="mp_Schritt" value="login" />'.rtrim("\n ".MP_Hidden).($sMpSeite?"\n ".'<input type="hidden" name="mp_Seite" value="'.$sMpSeite.'" />':'').'
 <div class="mpTabl">
  <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxBenutzername).'<br />'.fMpTx(MP_TxOder).'<br />'.fMpTx(MP_TxMailAdresse).'</div>
   <div class="mpTbSp2"><input class="mpEing" type="text" name="mp_F2" value="'.fMpTx($aW[2]).'" maxlength="100" /></div>
  </div>
  <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxPasswort).'</div>
   <div class="mpTbSp2"><input class="mpEing" type="password" name="mp_F3" maxlength="16" /></div>
  </div>
  <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxBenutzerDat).'</div>
   <div class="mpTbSp2"><input class="mpCheck" type="checkbox" name="mp_Daten" value="1" /> '.fMpTx(MP_TxBenutzerDat.' '.MP_TxAendern).'</div>
  </div>
 </div>
 <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxAnmelden).'" title="'.fMpTx(MP_TxAnmelden).'" /></div>';

 if($bCaptcha) $X.='<input type="hidden" name="mp_CaptchaAntwort" value="" /><input type="hidden" name="mp_CaptchaCode" value="'.$Cap->PublicKey.'" /><input type="hidden" name="mp_CaptchaFrage" value="'.fMpTx($Cap->Question).'" /><input name="mp_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />';
 $X.="\n".' </form>';

 if(MP_NutzerNeuErlaubt){ //neuer Nutzer
 $X.="\n".'
 <p class="mpMeld">'.fMpTx(MP_TxLoginNeu).'</p>
 <form class="mpLogi" action="'.fMpHref('login').'" method="post">
 <input type="hidden" name="mp_Aktion" value="login" />
 <input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />
 <input type="hidden" name="mp_Schritt" value="neu" />'.rtrim("\n ".MP_Hidden).'
 <div class="mpTabl">
  <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxGewuenscht).'<div>'.fMpTx(MP_TxBenutzername).'</div><span class="mpMini">'.fMpTx(MP_TxNutzerRegel).'</span></div>
   <div class="mpTbSp2"><input class="mpEing" type="text" name="mp_F2" value="'.(isset($sNeuName)?fMpTx($sNeuName):'').'" maxlength="25" /></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxCaptchaFeld).'*</div>
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
 $X.='
 </div>
 <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxAnmelden).'" title="'.fMpTx(MP_TxAnmelden).'" /></div>
 </form>';
 }

 if(MP_PasswortSenden){ //Passwort zusenden
 $X.="\n".'
 <p class="mpMeld">'.fMpTx(MP_TxLoginVergessen).'</p>
 <form class="mpLogi" action="'.fMpHref('login').'" method="post">
 <input type="hidden" name="mp_Aktion" value="login" />
 <input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />
 <input type="hidden" name="mp_Schritt" value="senden" />'.rtrim("\n ".MP_Hidden).'
 <div class="mpTabl">
  <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxBenutzername).'<br />'.fMpTx(MP_TxOder).'<br />'.fMpTx(MP_TxMailAdresse).'</div>
   <div class="mpTbSp2"><input class="mpEing" type="text" name="mp_F2" value="'.fMpTx($aW[2]).'" maxlength="100" /></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxCaptchaFeld).'*</div>
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
 $X.='
 </div>'."\n";
 if(!$bOK) $X.=' <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxAnmelden).'" title="'.fMpTx(MP_TxAnmelden).'" /></div>'."\n"; else $X.='&nbsp;';
 $X.=' </form>';
 }

 }//Ende Formulare
 return $X;
}

function fMpEnCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}

function fMpDateiname($s){
 //if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//IGNORE',$s); else $s=html_entity_decode($s);
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}

function fMpErzeugeDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 $nJ=(strlen($a[$nJ])<=2?2000+$a[$nJ]:(int)$a[$nJ]); $nM=(int)$a[$nM]; $nT=(int)$a[$nT];
 if(checkdate($nM,$nT,$nJ)) return sprintf('%04d-%02d-%02d',$nJ,$nM,$nT);
 else return false;
}

function fMpDatumsFormat(){
 $s1=MP_TxSymbTag; $s2=MP_TxSymbMon; $s3=(MP_Jahrhundert?MP_TxSymbJhr:'').MP_TxSymbJhr;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=MP_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=MP_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fMpBBToolbar($sTBox){
 $sElNr=substr($sTBox,strpos($sTBox,'_'));
 $X ="\n".' <div style="float:left;margin-right:9px;">';
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Bold',  "'[b]','[/b]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Italic',"'[i]','[/i]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Uline', "'[u]','[/u]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Center',"'[center]','[/center]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Right', "'[right]','[/right]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Enum',  "'[list]','[/list]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Number',"'[list=o]','[/list]'");
 $X.="\n".'  '.fDrawToolBtn($sTBox,'Link',  "'[url]','[/url]'");
 $X.="\n".' </div>';

 $X.="\n".' <div style="float:left;">
   <span class="mpNoBr" style="margin-right:3px;">
   <img class="mpTool" src="'.MP_Url.'grafik/tbColor.gif" style="margin-right:0;vertical-align:top;cursor:default;" title="'.fMpTx(MP_TxBB_O).'" />
   <select class="mpTool" name="mp_Col'.$sElNr.'" onchange="bbTag('."'".$sTBox."','[color='+this.form.mp_Col".$sElNr.".options[this.form.mp_Col".$sElNr.".selectedIndex].value+']','[/color]'".');this.form.mp_Col'.$sElNr.'.selectedIndex=0;" title="'.fMpTx(MP_TxBB_O).'">
    <option value=""></option>
    <option style="color:black" value="black">Abc9</option>
    <option style="color:red;" value="red">Abc9</option>
    <option style="color:violet;" value="violet">Abc9</option>
    <option style="color:brown;" value="brown">Abc9</option>
    <option style="color:yellow;" value="yellow">Abc9</option>
    <option style="color:green;" value="green">Abc9</option>
    <option style="color:lime;" value="lime">Abc9</option>
    <option style="color:olive;" value="olive">Abc9</option>
    <option style="color:cyan;" value="cyan">Abc9</option>
    <option style="color:blue;" value="blue">Abc9</option>
    <option style="color:navy;" value="navy">Abc9</option>
    <option style="color:gray;" value="gray">Abc9</option>
    <option style="color:silver;" value="silver">Abc9</option>
    <option style="color:white;background-color:#999999" value="white">Abc9</option>
   </select>
   </span>
   <span class="mpNoBr">
   <img class="mpTool" src="'.MP_Url.'grafik/tbSize.gif" style="margin-right:0;vertical-align:top;cursor:default;" title="'.fMpTx(MP_TxBB_S).'" />
   <select class="mpTool" name="mp_Siz'.$sElNr.'" onchange="bbTag('."'".$sTBox."','[size='+this.form.mp_Siz".$sElNr.".options[this.form.mp_Siz".$sElNr.".selectedIndex].value+']','[/size]'".');this.form.mp_Siz'.$sElNr.'.selectedIndex=0;" title="'.fMpTx(MP_TxBB_S).'">
    <option value=""></option>
    <option value="80">&nbsp;80%</option>
    <option value="90">&nbsp;90%</option>
    <option value="110">110%</option>
    <option value="120">120%</option>
   </select>
   </span>
  </div>';
 return $X;
}
function fDrawToolBtn($sTBox,$vImg,$nTag){
 return '<img class="mpTool" src="'.MP_Url.'grafik/tb'.$vImg.'.gif" onClick="bbTag('."'".$sTBox."'".','.$nTag.')" style="background-image:url('.MP_Url.'grafik/tool.gif);" title="'.fMpTx(constant('MP_TxBB_'.substr($vImg,0,1))).'" />';
}
?>