<?php
function fKalSeite(){
 global $kal_FeldName, $kal_FeldType, $kal_EingabeFeld, $kal_EingabeLang, $kal_PflichtFeld,
  $kal_Kategorien, $kal_Symbole, $kal_WochenTag;

 $nFelder=count($kal_FeldName); $aW=array(); $aWTag=array(); $aWT14=array(); $aUpl=array(); $aFehl=array(); $bOK=false; $sSta='1';
 $Et=''; $Es='Fehl'; $sPeri=''; $sWdhDat=''; $sWdhMal=''; $M1Day=NULL; $M2Day=NULL; $M3Day=NULL; $M4Day=NULL; $M5Day=NULL; $M6Day=NULL; $M7Day=NULL; $M8Day=NULL; $M9Day=NULL;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 //Formularzugang pruefen
 $bFormEingabe=false; $bSesOK=false; $bCaptcha=KAL_Captcha; $nNId='';
 $nNuPos=(int)array_search('u',$kal_FeldType); $sNutzerEml=''; $sNutzerName=KAL_TxAutor0000; $aN=array();

 if($sSes=substr(KAL_Session,17,12)){ //Session pruefen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if($p=$nNuPos)
    if(!$k=$GLOBALS['kal_NDetailFeld'][$p]) if(!$k=KAL_NNutzerListFeld) if(!$k=$GLOBALS['kal_DetailFeld'][$p]) $k=KAL_NutzerListFeld;
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
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }
 if($bSesOK) if(KAL_NEingabeAnders) $kal_EingabeFeld=$GLOBALS['kal_NEingabeFeld'];

 if($nNuPos>1&&KAL_NEingabeLogin){ //Login zwingend nötig
  $bCaptcha=KAL_Captcha||KAL_LoginCaptcha;
  if($bSesOK){$bFormEingabe=true; $bCaptcha=false;} else if($Et==''){$Et=KAL_TxNutzerLogin; $Es='Meld';}
 }else $bFormEingabe=true;

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if($bCaptcha){ //Captcha erzeugen
   $sCapTyp=KAL_CaptchaTyp; $bCapOk=false; $bCapErr=false;
   require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);
  }
  if(KAL_NutzerEMail){if(!$p=array_search('c',$kal_FeldType)) $p=array_search('e',$kal_FeldType); if($p) $aW[$p]=$sNutzerEml;} //NutzerEMail
  $aVg=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben);
  for($i=2;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]) if(isset($aVg[$i])) if($a=explode(';',trim($aVg[$i]))) if(isset($a[1])){
   switch($kal_FeldType[$i]){
   case 't': $aW[$i]=trim($a[1]); break;
   case 'm': $aW[$i]=str_replace('\n ',"\n",trim($a[1])); break;
   case 'j': case 'v': $aW[$i]=strtoupper(substr(trim($a[1]),0,1)); break;
   }
  }
  $sZentrum=(isset($_GET['kal_Zentrum'])?fKalRq1($_GET['kal_Zentrum']):'');
 }else{ //POST Formularauswertung
  if($bCaptcha){ //Captcha behandeln
   $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false;
   require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
   $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $aFehl[0]=true; }
  }
  $bUtf8=((isset($_POST['kal_JSSend'])||$_POST['kal_Utf8']=='1')?true:false);
  $sZentrum=(isset($_POST['kal_Zentrum'])?fKalRq1($_POST['kal_Zentrum']):'');
  $sZ=''; $sF=''; $sPCode=''; $sFehl=''; $sSta=(isset($_POST['kal_Sta'])?sprintf('%0d',$_POST['kal_Sta']):(KAL_Direkteintrag?'1':'2')); $nDtPos2=0;
  for($i=1;$i<$nFelder;$i++) //kal_Oh: hochgeladene;
    $aOh[$i]=(isset($_POST['kal_Oh'.$i])?$_POST['kal_Oh'.$i]:'');
  for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){ //alle Eingabefelder
   $s=str_replace('~@~','\n ',stripslashes(@strip_tags(str_replace('\n ','~@~',str_replace("\r",'',trim($_POST['kal_F'.$i]))))));
   $t=$kal_FeldType[$i];
   if(strlen($s)>0||!$kal_PflichtFeld[$i]||$t=='b'||$t=='f'||$t=='@'){
    if($bUtf8||KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); elseif(KAL_Zeichensatz==1) $s=html_entity_decode($s);
    if($t!='m'&&$t!='g') $s=str_replace('"',"'",$s); $v=$s; //s:Eingabe, v:Speicherwert
    switch($t){
    case 't': if($kal_FeldName[$i]=='KAPAZITAET') $s=str_replace(' ','',str_replace(' ','',$s)); $v=$s; break;
    case 'm': case 'a': case 'k': case 's': case 'j': case '#': case 'v': case 'g': case 'x': break; //Memo,Kategorie,Auswahl,Ja/Nein,StreetMap
    case 'd': if($s) //Datum
     if($v=fKalErzeugeDatum($s)){$s=fKalAnzeigeDatum($v); if($i==1&&$v<date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){$sFehl=KAL_TxAendereZuAlt; $aFehl[1]=true;}}
     else $aFehl[$i]=true;
     if($nDtPos2<2) $nDtPos2=$i;
     break;
    case '@': //EintragsDatum
     if($kal_FeldName[$i]!='ZUSAGE_BIS'){$v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).date(' H:i');}
     elseif($s){
      if($v=fKalErzeugeDatum($s)){
       if($p=strpos($s,' ')){
        $a=explode(':',str_replace('.',':',str_replace(',',':',trim(substr($s,$p)))));
        $u=sprintf(' %02d:%02d',(isset($a[0])?$a[0]:0),(isset($a[1])?$a[1]:0));
       }else $u='';
       $s=fKalAnzeigeDatum($v).$u; $v=substr($v,0,10).$u;
      }else $aFehl[$i]=true;
     }elseif($kal_PflichtFeld[$i]) $aFehl[$i]=true;
     break;
    case 'z': //Uhrzeit
     if($s){$a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',$a[0],(isset($a[1])?$a[1]:0)); $v=$s;} break;
    case 'e': case 'c': // E-Mail, Kontakt-E-Mail
     if($s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aFehl[$i]=true;
     if(!KAL_SQL) $v=fKalEnCode($s); break;
    case 'l': // Link oder E-Mail
     if($p=strpos(strtolower(substr($s,0,7)),'ttp://')){$s=substr($s,$p+6); $v=$s;} break;
    case 'b': //Bild
     if($aOh[$i]>'') $v=$aOh[$i];
     $UpNaJS=(isset($_POST['kal_UpNa_'.$i])?fKalDateiname(basename($_POST['kal_UpNa_'.$i])):'');
     $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):'');
     if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
     if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
      if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_BildMaxKByte)||KAL_BildMaxKByte<=0){
       if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['kal_Up'.$i]['tmp_name']);
       elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['kal_Up'.$i]['tmp_name']);
       elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['kal_Up'.$i]['tmp_name']);
       if(!empty($Src)){
        if($sAlt=$aOh[$i]){ //alte Uploads weg
         $p=strpos($sAlt,'|'); @unlink(KAL_Pfad.'temp/-'.substr($sAlt,0,$p)); @unlink(KAL_Pfad.'temp/_'.substr($sAlt,$p+1)); $aOh[$i]='';
        }
        $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $sAlt='#|'.implode('|',$aOh); $sZhl='A';
        if(strpos($sAlt,'|'.$UpBa.$UpEx)){while(strpos($sAlt,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelname
        if($Sx>KAL_VorschauBreit||$Sy>KAL_VorschauHoch){ //Vorschau verkleinern
         $Dw=min(KAL_VorschauBreit,$Sx);
         if($Sx>KAL_VorschauBreit) $Dh=round(KAL_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>KAL_VorschauHoch){$Dw=round(KAL_VorschauHoch/$Dh*$Dw); $Dh=KAL_VorschauHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         if(@imagejpeg($Dest,KAL_Pfad.'temp/-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','temp/'.$UpNa,KAL_TxDateiRechte);}
         imagedestroy($Dest); unset($Dest);
        }else{
         if(@copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','temp/'.$UpNa,KAL_TxDateiRechte);}
        }
        if($Sx>KAL_BildBreit||$Sy>KAL_BildHoch){ //Bild verkleinern
         $Dw=min(KAL_BildBreit,$Sx);
         if($Sx>KAL_BildBreit) $Dh=round(KAL_BildBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>KAL_BildHoch){$Dw=round(KAL_BildHoch/$Dh*$Dw); $Dh=KAL_BildHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         @imagejpeg($Dest,KAL_Pfad.'temp/_'.$UpBa.'.jpg');
         $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
        }else{$v.=$UpBa.$UpEx; @copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/_'.$UpBa.$UpEx);}
        imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
       }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,KAL_TxBildOeffnen);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_BildMaxKByte,KAL_TxBildGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxBildTyp);
     }elseif($s>'') if(isset($_POST['kal_Dl'.$i])&&$_POST['kal_Dl'.$i]){ //hochgeladenes Bild löschen
      $p=strrpos($s,'.'); @unlink(KAL_Pfad.'temp/-'.$s); @unlink(KAL_Pfad.'temp/_'.$s);
      if(strtolower(substr($s,$p))!='.jpg'){
       @unlink(KAL_Pfad.'temp/-'.substr($s,0,$p).'.jpg'); @unlink(KAL_Pfad.'temp/_'.substr($s,0,$p).'.jpg');
      }
      $s=''; $v=''; $aOh[$i]='';
     }
     break;
    case 'f': //Datei
     if($aOh[$i]>'') $v=$aOh[$i];
     $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
     $bUpEx=(strpos('#;'.KAL_DateiEndungen.';',';'.substr($UpEx,1).';')>0); if(!KAL_DateiEndgPositiv) $bUpEx=!$bUpEx;
     if($bUpEx&&($UpEx>'')){
      if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_DateiMaxKByte)){
       if($aOh[$i]>''){@unlink(KAL_Pfad.'temp/'.$aOh[$i]); $aOh[$i]='';} // alten Upload weg
       $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $u='#|'.implode('|',$aOh); $sZhl='A';
       if(strpos($u,'|'.$UpBa.$UpEx)){while(strpos($u,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelnamen
       if(@copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/'.$UpBa.$UpEx)){$s=$UpBa.$UpEx; $v=$s; $aOh[$i]=$v;}
       else{$aFehl[$i]=true; $sFehl=str_replace('#','temp/'.$UpNa,KAL_TxDateiRechte);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_DateiMaxKByte,KAL_TxDateiGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxDateiTyp);
     }elseif($s>'') if(isset($_POST['kal_Dl'.$i])&&$_POST['kal_Dl'.$i]=='1'){ //hochgeladene Datei löschen
      @unlink(KAL_Pfad.'temp/'.$s); $s=''; $v=''; $aOh[$i]='';
     }
     break;
    case 'w':
     $v=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),KAL_Dezimalstellen,'.','');
     $s=number_format((float)$v,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); break;
    case 'n': case '1': case '2': case '3': //Zahl
     $v=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),(int)$t,'.','');
     $s=number_format((float)$v,(int)$t,KAL_Dezimalzeichen,''); break;
    case 'r':
     $v=str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s));
     $s=str_replace('.',KAL_Dezimalzeichen,$v); break;
    case 'o': //PLZ
     if($s) if(strlen($s)!=KAL_PLZLaenge) $aFehl[$i]=true; break;
    case 'p': $v=fKalEnCode($s); break;
    case 'u': $s=sprintf('%04d',substr(KAL_Session,17,4)); $v=$s; break;
    }$aW[$i]=$s;
    if(KAL_SZeichenstz!=0) if(KAL_SZeichenstz==2) $v=iconv('ISO-8859-1','UTF-8',$v); else $v=htmlentities($v,ENT_COMPAT,'ISO-8859-1');
    if(!KAL_SQL) $sZ.=';'.str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
    else{$sZ.=',"'.str_replace("\n","\r\n",str_replace('\n ',"\n",str_replace('"','\"',$v))).'"'; $sF.=',kal_'.$i;}
   }else{$aFehl[$i]=true; if(!KAL_SQL) $sZ.=';';}
  }elseif($kal_FeldType[$i]=='u'){
   if(!KAL_SQL) $sZ.=';'.sprintf('%04d',substr(KAL_Session,17,4));
   else{$sZ.=',"'.sprintf('%04d',substr(KAL_Session,17,4)).'"'; $sF.=',kal_'.$i;}
  }elseif(!KAL_SQL) $sZ.=';';

  if($sPeri=(isset($_POST['kal_Periode'])?@strip_tags($_POST['kal_Periode']):'')){ //Periodik auswerten
   if($sWdhDat=@strip_tags(trim($_POST['kal_WdhDat']))){
    if($v=substr(fKalErzeugeDatum($sWdhDat),0,10)){$sWdhDat=fKalAnzeigeDatum($v); $sPCode=$sPeri.'|'.$v.'|';}else $aFehl['W']=true;
   }elseif($sWdhMal=(int)($_POST['kal_WdhMal'])){$sPCode=$sPeri.'|'.$sWdhMal.'|';} else {$aFehl['W']=true; $sWdhMal='';}
   if($sPeri=='A'){// mehrtaegig
   }elseif($sPeri=='B'){// wöchentlich
    $aWTag=(isset($_POST['kal_WTag'])?$_POST['kal_WTag']:NULL);
    if(is_array($aWTag)){foreach($aWTag as $v) $sPCode.=substr($v,1);}else{$aFehl['B']=true; $aWTag=array();}
   }elseif($sPeri=='C'){//14-tägig
    $aWT14=(isset($_POST['kal_WT14'])?$_POST['kal_WT14']:NULL);
    if(is_array($aWT14)){foreach($aWT14 as $v) $sPCode.=substr($v,1);}else{$aFehl['C']=true; $aWT14=array();}
   }elseif($sPeri=='D'){//monatlich-1
    if($M1Day=(int)(isset($_POST['kal_M1Day'])?$_POST['kal_M1Day']:0)){if($M1Day<32) $sPCode.=$M1Day; else $aFehl['D']=true;}else{$aFehl['D']=true; $M1Day='';}
    if($M2Day=(int)(isset($_POST['kal_M2Day'])?$_POST['kal_M2Day']:0)){if($M2Day<32) $sPCode.='|'.$M2Day; else $aFehl['D']=true;}else $M2Day='';
    $M3Day=(isset($_POST['kal_M3Day'])?$_POST['kal_M3Day']:''); $sPCode.='|'.$M3Day;
   }elseif($sPeri=='E'){//monatlich-2
    if($M4Day=(int)(isset($_POST['kal_M4Day'])?$_POST['kal_M4Day']:0)) $sPCode.=$M4Day; else{$aFehl['E']=true; $M4Day='';}
    $M5Day=strip_tags(isset($_POST['kal_M4Day'])?$_POST['kal_M5Day']:'');
    if(strlen($M5Day)) {$M5Day=(int)$M5Day; $sPCode.='|'.$M5Day;}else{$aFehl['E']=true; $M5Day='';}
    $M6Day=(isset($_POST['kal_M6Day'])?$_POST['kal_M6Day']:''); $sPCode.='|'.$M6Day;
   }elseif($sPeri=='F'){//jährlich
    if($Jahr=(isset($_POST['kal_Jahr'])?strip_tags($_POST['kal_Jahr']):'')) $sPCode.='0';
    else{
     if($M7Day=(int)$_POST['kal_M7Day']) $sPCode.=$M7Day; else{$aFehl['F']=true; $M7Day='';}
     $M8Day=strip_tags($_POST['kal_M8Day']); if(strlen($M8Day)) {$M8Day=(int)$M8Day; $sPCode.='|'.$M8Day;}else{$aFehl['F']=true; $M8Day='';}
     $M9Day=strip_tags($_POST['kal_M9Day']); if(strlen($M9Day)) {$M9Day=(int)$M9Day; $sPCode.='|'.$M9Day;}else{$aFehl['F']=true; $M9Day='';}
    }
   }
  }
  if($sFehl==''){ //alles OK, eintragen
   if(count($aFehl)==0){
    $aIds=array(); $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*KAL_HalteAltesNochTage)) $sRefDat='';
    $sMlTx=KAL_TxStatus.': '.($sSta=='2'?KAL_TxVormerk:($sSta!='0'?KAL_TxOnline:KAL_TxOffline))."\n\n";
    if(!KAL_SQL){ //ohne SQL
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $aTmp=array(); $nDtPos2++;
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $s=rtrim($aD[$i]); $p=strpos($s,';'); $sId=(int)substr($s,0,$p); $nId=max($nId,abs($sId));
      if(substr($s,$p+3,10)>=$sRefDat||$sId<0) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
      elseif(KAL_EndeDatum&&($nDtPos2>2)){
       $aZl=explode(';',$s,$nDtPos2+2);
       if(substr($aZl[$nDtPos2],0,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
       else $aLsch[(int)substr($s,0,$p)]=true;
      }else $aLsch[(int)substr($s,0,$p)]=true;
     }
     if(!KAL_Direkteintrag){
      $aTmp[(++$nId).';2']=substr($sZ,1).($sPCode!=''?';'.$sPCode:''); $aIds[]=$nId; //Termin anhaengen
      $sMlTx.=strtoupper(KAL_TxNummer).': '.$nId;
     }else{
      $aTmp[(++$nId).';'.$sSta]=substr($sZ,1); $aIds[]=$nId; //Termin anhaengen
      $sMlTx.=strtoupper(KAL_TxNummer).': '.$nId;
      if($sPCode!='') if($aWdhDat=fKalWdhDat(substr($sZ,1,10),$sPCode)){ //Wiederholungen
       $sZ=substr($sZ,13); reset($aWdhDat); foreach($aWdhDat as $v){$aTmp[(++$nId).';'.$sSta]=$v.$sZ; $aIds[]=$nId;}
      }
     }
     $aD=array(); $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i];
     $aD[0]=$s.";Periodik\n"; asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v."\n";
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){ //Termine neu schreiben
      fwrite($f,rtrim(implode('',$aD))."\n"); fclose($f); $bOK=true;
      if(KAL_ListenErinn>0||KAL_DetailErinn>0){// Erinnerungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],11,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
      }}
      if(KAL_ListenBenachr>0||KAL_DetailBenachr>0){//Benachrichtigungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],0,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
      }}
      if(KAL_ZusageSystem){//Zusagenliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],0,20); $n=(int)substr($s,1+strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
     }}}else if($Et=='') $Et=str_replace('#',KAL_Daten.KAL_Termine,KAL_TxDateiRechte);
    }elseif($DbO){ //bei SQL
     if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online'.$sF.',periodik) VALUES("'.(KAL_Direkteintrag?$sSta:'2').'"'.$sZ.',"'.$sPCode.'")')){
      if($nId=$DbO->insert_id){
       $aIds[]=$nId; $bOK=true; $sMlTx.=strtoupper(KAL_TxNummer).': '.$nId;
       if(KAL_Direkteintrag&&$sPCode!='') if($aWdhDat=fKalWdhDat(substr($sZ,2,10),$sPCode)){ //Wiederholungen
        $sZ=substr($sZ,14); reset($aWdhDat);
        foreach($aWdhDat as $v) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online'.$sF.',periodik) VALUES("'.$sSta.'","'.$v.$sZ.',"")')){
         if($nId=$DbO->insert_id) $aIds[]=$nId;
        }
       }
       $sDtFld2=''; if(KAL_EndeDatum&&($nDtPos2>1)) $sDtFld2=' AND kal_'.$nDtPos2.'<"'.$sRefDat.'"'; $sD='';
       if($rR=$DbO->query('SELECT id FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2)){
        while($a=$rR->fetch_row()){$aLsch[(int)$a[0]]=true; $sD.=' OR termin="'.$a[0].'"';} $rR->close();
        if($sD){
         $DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2); $sD=substr($sD,4);
         $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE '.$sD);
         $DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE '.$sD);
         $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.$sD);
      }}}else $Et=KAL_TxSqlEinfg;
     }else $Et=KAL_TxSqlEinfg;
    }//SQL
    if($bOK){ //Daten gespeichert
     $Et=(KAL_Direkteintrag?($sSta=='1'?KAL_TxEingabeErfo:KAL_TxEingabeOffl):KAL_TxVormerkErfo); $Es='Erfo'; $sMlTo=$sNutzerEml; $sMlTv='';
     $sBtrAdm=KAL_TxEintragAdminBtr; $sBtrEtr=KAL_TxEintragBtr; $sBtrLst=KAL_TxMailListeBtr; $sMlTL=$sMlTx;
     for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){
      $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
      if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
      if($UpNa=$aOh[$i]){ //neue Bilder und Dateien umspeichern
       if($t=='b'){
        $p=strpos($UpNa,'|'); $UpNa=substr($UpNa,0,$p);
        reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/-'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'-'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
        @unlink(KAL_Pfad.'temp/-'.$UpNa); $UpNa=substr($aOh[$i],$p+1);
        reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/_'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'_'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
        @unlink(KAL_Pfad.'temp/_'.$UpNa);
       }elseif($t=='f'){
        reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/'. $UpNa,KAL_Pfad.KAL_Bilder.$j.'~'.$UpNa)){$bOK=false; $sFehl=$UpNa;}
        @unlink(KAL_Pfad.'temp/'.$UpNa);
       }
       if(!$bOK) $Et.=str_replace('#',KAL_Bilder.$sFehl,KAL_TxDateiRechte); $bOK=true;
      }
      if($t!='c'&&$t!='e'&&$t!='p'){
       $s=($t!='u'?$aW[$i]:$sNutzerName); if(($t=='j'||$t=='v')&&$s>'') $s=($s!='J'?KAL_TxNein:KAL_TxJa);
       $sMlTx.="\n".strtoupper($sFN).': '.$s; $sMlTL.="\n".strtoupper($sFN).': '.fKalPlainText($s,$t,$aN);
       if(strpos($sBtrAdm,'{'.$kal_FeldName[$i].'}')) $sBtrAdm=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtrAdm);
       if(strpos($sBtrEtr,'{'.$kal_FeldName[$i].'}')) $sBtrEtr=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtrEtr);
       if(strpos($sBtrLst,'{'.$kal_FeldName[$i].'}')) $sBtrLst=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtrLst);
      }else $sMlTv.="\n".strtoupper($sFN).': '.$aW[$i];
      if($t=='c'&&$aW[$i]) $sMlTo=$aW[$i]; //Kontakt
      elseif($t=='e'&&($sMlTo==''||($aW[$i]&&!in_array('c',$kal_FeldType)))) $sMlTo=$aW[$i]; //E-Mail
      elseif($t=='l'&& $sMlTo==''&&strpos($aW[$i],'@')>0&&!(in_array('e',$kal_FeldType))) $sMlTo=$aW[$i]; //Link
     }elseif($kal_FeldType[$i]=='u'){
      $sMlTx.="\n".strtoupper($kal_FeldName[$i]).': '.$sNutzerName; $sMlTL.="\n".strtoupper($kal_FeldName[$i]).': '.$sNutzerName;
      if(strpos($sBtrAdm,'{'.$kal_FeldName[$i].'}')) $sBtrAdm=str_replace('{'.$kal_FeldName[$i].'}',$sNutzerName,$sBtrAdm);
      if(strpos($sBtrEtr,'{'.$kal_FeldName[$i].'}')) $sBtrEtr=str_replace('{'.$kal_FeldName[$i].'}',$sNutzerName,$sBtrEtr);
      if(strpos($sBtrLst,'{'.$kal_FeldName[$i].'}')) $sBtrLst=str_replace('{'.$kal_FeldName[$i].'}',$sNutzerName,$sBtrLst);
     }
     if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
     if(KAL_EintragAdminInfo){
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(strpos(KAL_EmpfTermin,'@')>0?KAL_EmpfTermin:KAL_Empfaenger); $s=date('s'); $s=substr($s,0,1).$aIds[0].substr($s,1,1); $c=(int)KAL_Schluessel; for($i=strlen($s);$i>=0;--$i) $c+=(int)substr($s,$i,1); $c='?i='.$s.'&c='.$c;
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$aIds[0],str_replace('#N',$aIds[0],$sBtrAdm));
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTx.$sMlTv,str_replace('#C',$c,str_replace('#N',$aIds[0],str_replace('\n ',"\n",KAL_TxEintragAdminTxt))));
      $Mailer->Send();
     }
     if(KAL_EintragMail&&$sMlTo!=''){
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sMlTo); $sWww=fKalHost();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,$sBtrEtr)); $Mailer->SetReplyTo($sMlTo);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTx.$sMlTv,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxEintragTxt)));
      $Mailer->Send();
     }
     if($sSta>'0'&&KAL_MailListeEintrag&&KAL_MailListeAdr!=''){
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(KAL_MailListeAdr); $sWww=fKalHost();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,$sBtrLst));
      $s=KAL_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetReplyTo($s,$t);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTL,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxMailListeTxt)));
      $Mailer->Send();
     }
     if(count($aLsch)>0&&(in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))){ //veraltete Bilder oder Dateien
      if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
       $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])) $aD[]=$s; closedir($f);
       foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
      }
     }
    }
   }else $Et=KAL_TxEingabeFehl;
  }else $Et=$sFehl;
 }//POST

 //Beginn der Ausgabe
 if($Et==''){$Et=KAL_TxEingabeMeld; $Es='Meld';} $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

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

 if($bFormEingabe){ //Eingabeformular

 //Formular- und Tabellenanfang
 if(in_Array('x',$kal_FeldType)) $X.="\n\n".'<script>function GeoWin(sURL){geoWin=window.open(sURL,"geowin","width='.(min(max(KAL_GMapBreit,500),725)+50).',height=700,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");geoWin.focus();}</script>';
 if(KAL_FormatCode) $X.="\n\n".'<script src="'.KAL_Url.'kalEingabe.js"></script>'."\n";
 $s='<link rel="stylesheet" type="text/css" href="'.KAL_Url.'tcal.css">
 <script src="'.KAL_Url.'tcal.js"></script>
 <script>

window.onmessage = function (e) {
  if (e.data === "Ergebnis gesendet") {
    alert("gesendet");
  }
};

  A_TCALCONF.format='."'".fKalTCalFormat()."'".';
  A_TCALCONF.weekdays=['."'".fKalTx(implode("','",$kal_WochenTag))."'".'];
  A_TCALCONF.months=['."'".fKalTx(str_replace(';',"','",KAL_TxLMonate))."'".'];
  A_TCALCONF.prevmonth='."'".fKalTx(KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat)."'".';
  A_TCALCONF.nextmonth='."'".fKalTx(KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat)."'".';
  A_TCALCONF.prevyear='."'".fKalTx(KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr)."'".';
  A_TCALCONF.nextyear='."'".fKalTx(KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr)."'".';
  A_TCALCONF.yearscroll='.(KAL_TCalYrScroll?'true':'false').';
  A_TIMECONF.starttime='.sprintf('%.2f',KAL_TimeStart).';
  A_TIMECONF.stopptime='.sprintf('%.2f',KAL_TimeStopp).';
  A_TIMECONF.intervall='.sprintf('%.2f',KAL_TimeIvall).';
 </script>';
 if(KAL_TCalPicker) $X.=str_replace("\n ","\n",str_replace("\r",'',trim($s)));
 $X.="\n".'<form class="kalEing" name="kalEingabe" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" onsubmit="return formSend()" enctype="multipart/form-data" method="post">'.rtrim("\n".KAL_Hidden);
 $X.="\n".'<input type="hidden" name="kal_Aktion" value="eingabe">';
 $X.="\n".'<script>';
 $X.="\n".' var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";';
 $X.="\n".' if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";';
 $X.="\n"." document.writeln('<input type=\"hidden\" name=\"kal_Utf8\" value=\"'+sUtf8+'\">');";
 $X.="\n".'</script>';
 if(KAL_Session!='') $X.="\n".'<input type="hidden" name="kal_Session" value="'.substr(KAL_Session,17,12).'">';
 if($sZentrum) $X.="\n".'<input type="hidden" name="kal_Zentrum" value="1">';

 $X.="\n".'<div class="kalTabl">';
 //Eingabeformularzeilen
 $nFarb=1; $bMitBild=false; $aVg=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); $aTrn=explode(',','#,'.KAL_EingabeTrenner); //Hinweise und Kategorien holen
 for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  $v=(isset($aW[$i])?$aW[$i]:''); //Feldinhalt
  if(isset($aVg[$i])) $aHlp=explode(';',trim($aVg[$i])); else $aHlp=array(0=>''); //Hilfetext und etwaige Vorgabewerte
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
  $X.="\n".'  <div class="kalTbSp1"><div id="kalLabel'.$i.'">'.fKalTx($sFN); // Feldname
  if($kal_PflichtFeld[$i]) $X.='*';
  if($t=='x') $X.='&nbsp;<a href="'.$sAjaxURL.(KAL_GMapSource=='O'?'openstreet':'google').'map.php?'.$i.($v?','.$v:'').'" target="geowin" onclick="GeoWin(this.href);return false;"><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx(KAL_TxKoordinaten).'" alt="'.fKalTx(KAL_TxKoordinaten).'"></a>';
  $X.='</div></div>'."\n".'  <div class="kalTbSp2">'; $sZ="\n".'   <div class="kal'.(isset($aFehl[$i])&&$aFehl[$i]?'Fhlt':'Eing').'">';
  switch($t){
  case 't': case 'l': case 'e': case 'c': case 'x': // Text, Link, E-Mail, Kontakt, StreetMap
   if($t=='t') $v=str_replace('\n ',"\n",$v);
   $X.= $sZ.'<input class="kalEing" type="text" name="kal_F'.$i.'" value="'.str_replace("\n",'\n ',str_replace("\r",'',fKalTx($v))).'" maxlength="'.($kal_EingabeLang[$i]>0?$kal_EingabeLang[$i]:'255').'"></div>';
   break;
  case 'm': // Memo
   if(KAL_FormatCode) $X.="\n".'   <div class="kalNorm" title="'.fKalTx(KAL_TxBB_X).'">'."\n".fKalBBToolbar($i)."\n"; else $X.="\n".'   <div class="kalNorm">';
   $X.= $sZ.'<textarea class="kalEing" name="kal_F'.$i.'" '.($kal_EingabeLang[$i]>0?'maxlength="'.$kal_EingabeLang[$i].'" ':'').'cols="80" rows="8">'.fKalTx($v).'</textarea></div>'."\n".'   </div>';
   break;
  case 'a': case 'k': case 's': //Aufzaehlung/Kategorie
   reset($aHlp); $sO=''; foreach($aHlp as $w) $sO.='<option value="'.fKalTx($w).'"'.($v==$w?' selected="selected"':'').'>'.fKalTx($w).'</option>';
   $X.= $sZ.'<select class="kalEing" name="kal_F'.$i.'" size="1"><option value="">---</option>'.substr($sO,strpos($sO,'<option',9)).'</select></div>';
   break;
  case 'd': // Datum
   $X.= $sZ.'<input class="kalTCal" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="10"> <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.fKalDatumsFormat().'</span></div>';
   break;
  case '@': // EintragsDatum
   if($kal_FeldName[$i]!='ZUSAGE_BIS') $X.=fKalAnzeigeDatum(date('Y-m-d')).date(' H:i');
   else $X.=$sZ.'<input class="kalEing" style="width:10em;" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16"> <span class="kalMini">'.fKalTx(KAL_TxFormat.' '.fKalDatumsFormat().' '.KAL_TxOder.' '.fKalDatumsFormat().' '.KAL_TxSymbUhr).'</span></div>';
   break;
  case 'z': // Zeit
   $X.= $sZ.'<input class="kalTime" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="5"> <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.KAL_TxSymbUhr.'</span></div>';
   break;
  case 'j': case '#': case 'v': // Ja/Nein
   $X.= $sZ.'<input class="kalRadio" type="radio" name="kal_F'.$i.'" value="J"'.($v!='J'?'':' checked="checked"').'> '.fKalTx(KAL_TxJa).' &nbsp; <input class="kalRadio" type="radio" name="kal_F'.$i.'" value="N"'.($v!='N'?'':' checked="checked"').'> '.fKalTx(KAL_TxNein).' &nbsp; <input class="kalRadio" type="radio" name="kal_F'.$i.'" value=""'.($v!=''?'':' checked="checked"').'> '.fKalTx(KAL_TxJNLeer).'</div>';
   break;
  case 'w': // Waehrung
   $X.= $sZ.'<input class="kalEing" style="width:7em;" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16"> '.KAL_Waehrung.'</div>';
   break;
  case 'n': case 'r': case '1': case '2': case '3': case 'o': // Zahlen
   $X.= $sZ.'<input class="kalEing" style="width:7em;" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="'.($kal_EingabeLang[$i]>0?$kal_EingabeLang[$i]:'12').'">'.($t!='o'?'':' <span class="kalMini">'.KAL_PLZLaenge.' '.KAL_TxStellen.'</span>').'</div>';
   break;
  case 'b': // Bild
   $X.= $sZ.'<input class="kalEing" type="file" name="kal_Up'.$i.'" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif"></div>'; $bMitBild=true;
   if($v) $X.="\n".'   <div class="kalNorm" style="float:left;"><input class="kalCheck" type="checkbox" name="kal_Dl'.$i.'" value="1"><input type="hidden" name="kal_F'.$i.'" value="'.$v.'"><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'"> <span class="kalMini">'.$v.' '.fKalTx(KAL_TxLoeschen).'</span></div>';
   $X.="\n".'   <div class="kalNorm" style="text-align:right;padding:1px;line-height:1.4em;"><span class="kalMini">'.(KAL_BildMaxKByte>0?'(max. '.KAL_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': // Datei
   $X.= $sZ.'<input class="kalEing" type="file" name="kal_Up'.$i.'"></div>';
   if($v) $X.="\n".'   <div class="kalNorm" style="float:left;"><input class="kalCheck" type="checkbox" name="kal_Dl'.$i.'" value="1"><input type="hidden" name="kal_F'.$i.'" value="'.$v.'"><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'"> <span class="kalMini">'.$v.' '.fKalTx(KAL_TxLoeschen).'</span></div>';
   $X.="\n".'   <div class="kalNorm" style="text-align:right;padding:1px;line-height:1.4em;"><span class="kalMini">(max. '.KAL_DateiMaxKByte.' KByte)</span></div>';
   break;
  case 'g': // Gastkommentar
   if(KAL_FormatCode) $X.="\n".'   <div class="kalNorm" title="'.fKalTx(KAL_TxBB_X).'">'."\n".fKalBBToolbar($i)."\n"; else $X.="\n".'   <div class="kalNorm">';
   $X.= $sZ.'<textarea class="kalEing" name="kal_F'.$i.'" cols="80" rows="8">'.fKalTx($v).'</textarea></div>'."\n".'   </div>';
   break;
  case 'u': // Benutzername
   $X.= $sZ.'<input class="kalEing" style="width:7em;" type="text" name="kal_F'.$i.'" value="'.sprintf('%04d',substr(KAL_Session,17,4)).'" readonly="readonly"></div>';
   break;
  case 'p': // Passwort
   $X.= $sZ.'<input class="kalEing" style="width:12em;" type="password" name="kal_F'.$i.'" value="'.fKalTx($v).'" maxlength="'.($kal_EingabeLang[$i]>0?$kal_EingabeLang[$i]:'16').'"> <span class="kalMini">'.fKalTx(KAL_TxPassRegel).'</span></div>';
   break;
  }
  if($v=$aHlp[0]) $X.="\n".'   <div class="kalNorm"><span class="kalMini">'.str_replace('`,',';',fKalTx($v)).'</span></div>'; // Eingabehilfe
  $X.="\n".'  </div>'."\n".' </div>';
  if(array_search($i,$aTrn)){
   $X.="\n".' <div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2">&nbsp;</div></div>'; if(--$nFarb<=0) $nFarb=2;
  }
 }

 if(KAL_AendernOnOff&&$bSesOK&&KAL_Direkteintrag){ //Direkteintrag
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1">'.fKalTx(KAL_TxStatus).'</div>
  <div class="kalTbSp2"><input class="kalRadio" type="radio" name="kal_Sta" value="1"'.($sSta=='1'?' checked="checked"':'').'> '.fKalTx(KAL_TxOnline).' &nbsp; <input class="kalRadio" type="radio" name="kal_Sta" value="0"'.($sSta!='1'?' checked="checked"':'').'> '.fKalTx(KAL_TxOffline).'</div>
 </div>'; if(--$nFarb<=0) $nFarb=2;
 }

 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">
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
  if(--$nFarb<=0) $nFarb=2;
 }

 $X.="\n".' <div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2 kalTbSpR">* <span class="kalMini">'.fKalTx(KAL_TxPflicht).'</span></div></div>';
 if(--$nFarb<=0) $nFarb=2;

 if(KAL_Periodik){ //Periodikzeilen
  $aMonatsNam=explode(';',';'.KAL_TxKMonate);
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="0"'.($sPeri!=''?'':' checked="checked"').'> '.fKalTx(KAL_TxEinmal).'</div>
  <div class="kalTbSp2"><input class="kalRadio" type="radio" name="kal_Periode" value="A"'.($sPeri!='A'?'':' checked="checked"').'> '.fKalTx(KAL_TxTaegig).'</div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="B"'.($sPeri!='B'?'':' checked="checked"').'> '.fKalTx(KAL_TxWoechig).'</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['B'])&&$aFehl['B']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxImmer).' &nbsp;';
  for($i=0;$i<7;$i++) $X.="\n".'  <span class="kalNoBr"><input class="kalCheck" type="checkbox" name="kal_WTag[]" value="W'.$i.'"'.(!in_array('W'.$i,$aWTag)?'':' checked="checked"').' onclick="fSelWt(this.checked,2)"> '.fKalTx($kal_WochenTag[$i]).'</span> &nbsp;';
  $X.='</div>'."\n".'  </div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="C"'.($sPeri!='C'?'':' checked="checked"').'> '.fKalTx(KAL_Tx14Taegig).'</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['C'])&&$aFehl['C']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxImmer).' &nbsp;';
  for($i=0;$i<7;$i++) $X.="\n".'  <span class="kalNoBr"><input class="kalCheck" type="radio" name="kal_WT14[]" value="W'.$i.'"'.(!in_array('W'.$i,$aWT14)?'':' checked="checked"').' onclick="fSelWt(this.checked,3)"> '.fKalTx($kal_WochenTag[$i]).'</span> &nbsp;';
  $X.='</div>'."\n".'  </div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="D"'.($sPeri!='D'?'':' checked="checked"').'> '.fKalTx(KAL_TxMonatig).'-1</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['D'])&&$aFehl['D']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxImmer.' '.KAL_TxAm).'
   <input class="kalEing" style="width:2.2em;" type="text" name="kal_M1Day" value="'.$M1Day.'" maxlength="2" onkeyup="fSelWt(this.value,4)">. '.fKalTx(KAL_TxTag.'  '.KAL_TxUnd.'  '.KAL_TxAm).'
   <span class="kalNoBr"><input class="kalEing" style="width:2.2em;" type="text" name="kal_M2Day" value="'.$M2Day.'" maxlength="2" onkeyup="fSelWt(this.value,4)">. '.fKalTx(KAL_TxTagDesMonat).'</span><br>
   <span class="kalNoBr">'.fKalTx(KAL_TxInJedem).' <select class="kalEing" style="width:6.5em;" name="kal_M3Day" onchange="fSelWt(this.value,4)"><option value="1"'.($M3Day!=1?'':' selected="selected"').'>'.fKalTx(KAL_TxMonat).'</option><option value="2"'.($M3Day!=2?'':' selected="selected"').'>2. '.fKalTx(KAL_TxMonat).'</option><option value="3"'.($M3Day!=3?'':' selected="selected"').'>3. '.fKalTx(KAL_TxMonat).'</option><option value="4"'.($M3Day!=4?'':' selected="selected"').'>4. '.fKalTx(KAL_TxMonat).'</option><option value="6"'.($M3Day!=6?'':' selected="selected"').'>6. '.fKalTx(KAL_TxMonat).'</option></select></span></div>
  </div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="E"'.($sPeri!='E'?'':' checked="checked"').'> '.fKalTx(KAL_TxMonatig).'-2</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['E'])&&$aFehl['E']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxImmer.' '.KAL_TxAm).'
   <select class="kalEing" style="width:6.0em;" name="kal_M4Day" onchange="fSelWt(this.value,5)"><option value="">-</option><option value="1"'.($M4Day!=1?'':' selected="selected"').'>1.</option><option value="2"'.($M4Day!=2?'':' selected="selected"').'>2.</option><option value="3"'.($M4Day!=3?'':' selected="selected"').'>3.</option><option value="4"'.($M4Day!=4?'':' selected="selected"').'>4.</option><option value="5"'.($M4Day!=5?'':' selected="selected"').'>5.</option><option value="6"'.($M4Day!=6?'':' selected="selected"').'>'.fKalTx(KAL_TxLetzten).'</option></select>
   <select class="kalEing" style="width:4.5em;" name="kal_M5Day" onchange="fSelWt(this.value,5)"><option value="">-</option>';
  for($i=0;$i<7;$i++) $X.='<option value="'.$i.'"'.($M5Day!=$i||!is_int($M5Day)?'':' selected="selected"').'>'.fKalTx($kal_WochenTag[$i]).'</option>';
  $X.='</select> '.fKalTx(KAL_TxTagDesMonat).'<br>
   <span class="kalNoBr">'.fKalTx(KAL_TxInJedem).' <select class="kalEing" style="width:6.5em;" name="kal_M6Day" onchange="fSelWt(this.value,5)"><option value="1"'.($M6Day!=1?'':' selected="selected"').'>'.fKalTx(KAL_TxMonat).'</option><option value="2"'.($M6Day!=2?'':' selected="selected"').'>2. '.fKalTx(KAL_TxMonat).'</option><option value="3"'.($M6Day!=3?'':' selected="selected"').'>3. '.fKalTx(KAL_TxMonat).'</option><option value="4"'.($M6Day!=4?'':' selected="selected"').'>4. '.fKalTx(KAL_TxMonat).'</option><option value="6"'.($M6Day!=6?'':' selected="selected"').'>6. '.fKalTx(KAL_TxMonat).'</option></select></span></div>
  </div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1"><input class="kalRadio" type="radio" name="kal_Periode" value="F"'.($sPeri!='F'?'':' checked="checked"').'> '.fKalTx(KAL_TxJaehrlich).'</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['F'])&&$aFehl['F']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxImmer.' '.KAL_TxAm).' <input class="kalCheck" type="checkbox" name="kal_Jahr" value="1"'.(empty($Jahr)?'':' checked="checked"').' onclick="fSelWt(this.value,6)"> '.fKalTx(KAL_TxSelbenDatum).' &nbsp; '.fKalTx(KAL_TxOder).'<br>'.fKalTx(KAL_TxImmer.' '.KAL_TxAm).'
   <select class="kalEing" style="width:6.0em;" name="kal_M7Day" onchange="fSelWt(this.value,6)"><option value="">-</option><option value="1"'.($M7Day!=1?'':' selected="selected"').'>1.</option><option value="2"'.($M7Day!=2?'':' selected="selected"').'>2.</option><option value="3"'.($M7Day!=3?'':' selected="selected"').'>3.</option><option value="4"'.($M7Day!=4?'':' selected="selected"').'>4.</option><option value="5"'.($M7Day!=5?'':' selected="selected"').'>5.</option><option value="6"'.($M7Day!=6?'':' selected="selected"').'>'.fKalTx(KAL_TxVorLetzten).'</option><option value="7"'.($M7Day!=7?'':' selected="selected"').'>'.fKalTx(KAL_TxLetzten).'</option></select>
   <select class="kalEing" style="width:4.5em;" name="kal_M8Day" onchange="fSelWt(this.value,6)"><option value="">-</option>';
  for($i=0;$i<7;$i++)  $X.='<option value="'.$i.'"'.($M8Day!=$i||!is_int($M8Day)?'':' selected="selected"').'>'.fKalTx($kal_WochenTag[$i]).'</option>';
  $X.='</select> '.KAL_TxIm.'
   <select class="kalEing" style="width:4.4em;" name="kal_M9Day" onchange="fSelWt(this.value,6)"><option value="0">'.KAL_TxJahr.'</option>';
  for($i=1;$i<13;$i++)  $X.='<option value="'.$i.'"'.($M9Day!=$i||!is_int($M9Day)?'':' selected="selected"').'>'.fKalTx($aMonatsNam[$i]).'</option>';
  $X.='</select></div>
  </div>
 </div><div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1" style="padding-left:22px;">'.fKalTx(KAL_TxInsgesamt).'</div>
  <div class="kalTbSp2"><div class="kal'.(isset($aFehl['W'])&&$aFehl['W']?'Fhlt':'Eing').'">'.fKalTx(KAL_TxBisZum).'
   <input class="kalEing" style="width:6.3em;" type="text" name="kal_WdhDat" value="'.$sWdhDat.'"> <span class="kalMini">('.fKalDatumsFormat().')</span>
   &nbsp; '.fKalTx(KAL_TxOder).'
   <input class="kalEing" style="width:2.2em;" type="text" name="kal_WdhMal" value="'.$sWdhMal.'"> '.fKalTx(KAL_TxMale).'</div>
  </div>
 </div>';
 }

 $X.="\n".'</div>'; // Tabelle
 if(!$bOK) $X.="\n".'<div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxEingabe).'" title="'.fKalTx(KAL_TxEingabe).'"></div>';
 $X.="\n".'</form>'."\n";

 if($bMitBild && KAL_BildResize){
  $X.="\n".'<script src="'.KAL_Url.'kalEingabeBild.js"></script>';
  $X.="\n".'<script>';
  $X.="\n".' sPostURL="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'";';
  $X.="\n".' nBildBreit='.KAL_BildBreit.'; nBildHoch='.KAL_BildHoch.';';
  $X.="\n".' nThumbBreit='.KAL_ThumbBreit.'; nThumbHoch='.KAL_ThumbHoch.';';
  $X.="\n".'</script>';
 }else{
  $X.="\n".'<script>';
  $X.="\n".' function formSend(){return true;} // normales Senden ohne Bilder;';
  $X.="\n".' function loadDatFile(inputField){return false;}';
  $X.="\n".' function loadImgFile(inputField){return false;}';
  $X.="\n".'</script>';
 }

 }else{ //Ende Eingabefomular - Beginn Loginformular

 $X.='
 <p class="kalMeld" style="margin-top:20px;">'.fKalTx(KAL_LoginLogin).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="login">
 <input type="hidden" name="kal_Schritt" value="login">
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1 kalNoBr">'.fKalTx(KAL_TxBenutzername).'<br>'.fKalTx(KAL_TxOder).'<br>'.fKalTx(KAL_TxMailAdresse).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_F2" maxlength="100"></div>
  </div>
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxPasswort).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="password" name="kal_F3" maxlength="16"></div>
  </div>
 </div>
 <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxAnmelden).'" title="'.fKalTx(KAL_TxAnmelden).'"></div>';
 if($bCaptcha) $X.='<input type="hidden" name="kal_CaptchaAntwort" value=""><input type="hidden" name="kal_CaptchaCode" value="'.$Cap->PublicKey.'"><input type="hidden" name="kal_CaptchaFrage" value="'.fKalTx($Cap->Question).'"><input name="kal_CaptchaTyp" type="hidden" value="'.$Cap->Type.'">';
 $X.="\n".' </form>'."\n";

 if(KAL_NutzerNeuErlaubt){
 $X.='
 <p class="kalMeld">'.fKalTx(KAL_LoginNeu).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="login">
 <input type="hidden" name="kal_Schritt" value="neu">
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1 kalNoBr">'.fKalTx(KAL_TxGewuenscht).'<div class="kalNorm">'.fKalTx(KAL_TxBenutzername).'</div><span class="kalMini">'.fKalTx(KAL_TxNutzerRegel).'</span></div>
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_F2" value="'.(isset($sNeuName)?$sNeuName:'').'" maxlength="25"></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="kalTbZl1">
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
 $X.='
 </div>
 <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxAnmelden).'" title="'.fKalTx(KAL_TxAnmelden).'"></div>
 </form>'."\n";
 }

 }//Ende Formulare

 return $X;
}

function fKalEnCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}

function fKalDateiname($s){
 //if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//IGNORE',$s); else $s=html_entity_decode($s);
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}

function fKalErzeugeDatum($w){
 $nJ=2; $nM=1; $nT=0; $w=substr($w,0,10); if(($p=strrpos($w,' '))&&$p>7) $w=trim(substr($w,0,$p));
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 $nJ=(isset($a[$nJ])?(strlen($a[$nJ])<=2?2000+$a[$nJ]:(int)$a[$nJ]):2000); $nM=(isset($a[$nM])?(int)$a[$nM]:0); $nT=(isset($a[$nT])?(int)$a[$nT]:0);
 if(checkdate($nM,$nT,$nJ)) return sprintf('%04d-%02d-%02d',$nJ,$nM,$nT).rtrim(@date(' w',@mktime(12,0,0,$nM,$nT,$nJ)));
 else return false;
}

function fKalDatumsFormat(){
 $s1=KAL_TxSymbTag; $s2=KAL_TxSymbMon; $s3=(KAL_Jahrhundert?KAL_TxSymbJhr:'').KAL_TxSymbJhr;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=KAL_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=KAL_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
function fKalTCalFormat(){
 $s1='d'; $s2='m'; $s3=(KAL_Jahrhundert?'Y':'y');
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3='d'; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2='d'; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalWdhDat($sBeg,$sCod){
 $aTmp=explode('|',$sCod); $sTyp=$aTmp[0]; $sEnd=(isset($aTmp[1])?$aTmp[1]:''); $sP1=(isset($aTmp[2])?$aTmp[2]:''); $sP2=(isset($aTmp[3])?(int)$aTmp[3]:''); $sP3=(isset($aTmp[4])?(int)$aTmp[4]:0);
 if(strpos($sEnd,'-')>0){// bis Enddatum
  $s=date('Y-m-d',KAL_MaxPeriode*86400+time()); if($sEnd>$s) $sEnd=$s;
  $nMax=3653;
 }else{ // n-Mal
  $nMax=min($sEnd,KAL_MaxWiederhol);
  $sEnd=date('Y-m-d',KAL_MaxPeriode*86400+time());
 }
 $bDo=true; $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),substr($sBeg,0,4)); $aDat=array(); if(!isset($aTmp[4])) $aTmp[4]='0'; $bMon1=false; $bMon11=false;
 while($bDo){
  switch($sTyp){
   case 'A': //mehrtaegig
    $nAkt+=86400; $Dat=date('Y-m-d w',$nAkt);
    break;
   case 'B': //woechentlich
    $nAkt+=86400; while(strpos($sP1,@date('w',$nAkt))===false) $nAkt+=86400; $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'C': //14-taegig
    if(@date('w',$nAkt)==$sP1) $nAkt+=(14*86400); else while(@date('w',$nAkt)!=$sP1) $nAkt+=86400;
    $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'D': //monatlich-1
    $m=$sP3;
    do{
     $nAkt+=86400; $n=(int)@date('d',$nAkt);
     while($n!=(int)$sP1&&$n!=(int)$sP2){$nAkt+=86400; $n=(int)@date('d',$nAkt);}
     if($n==(int)$sP1&& --$m<=0) $bMon1=true; elseif($n==(int)$sP2&&!$bMon11) $bMon1=true; $bMon11=true; //allererste Fundstelle ist 2.Datum
    }while(!$bMon1);
    $Dat=@date('Y-m-d w',$nAkt); if($n==(int)$sP2||$sP2==0) $bMon1=false; // Monat erledigt
    break;
   case 'E': //monatlich-2
    $m=$sP3;
    do{
     if($sP1<5) $Dat=date('Y-m-',$nAkt).(1+7*($sP1-1)); /* 1...4 */ else $Dat=date('Y-m-t',$nAkt); //5. oder letzter
     $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
     if($sP1<5){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400;} //1...4
     else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400;} //5. oder letzter
     $Dat=@date('Y-m-d w',$nAkt);
     $s=date('Y-m-t',$nAkt); $nAkt=@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))+86400;
     if(substr($Dat,0,10)<=$sBeg) $Dat=''; if($sP1==5) if((int)substr($Dat,8,2)<29) $Dat='';
     if($Dat) if(--$m>0) $Dat=''; // Monate auslassen
    }while(!$Dat);
    break;
   case 'F': //jaehrlich
    if($sP1<='0'){//fester Termin
     $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),date('Y',$nAkt)+1);
     while(date('m-d',$nAkt)>substr($sBeg,5,5)) $nAkt-=86400; $Dat=@date('Y-m-d w',$nAkt);
    }else{//variables Datum
     do{
      $sJ=date('Y',$nAkt); if(!$sM=(int)$aTmp[4]) if($sP1<6) $sM=1; else $sM=12; //Monat des Jahres
      $Dat=$sJ.'-'.sprintf('%02d',$sM).'-'.($sP1<6?'01':date('t',@mktime(12,0,0,$sM,1,$sJ)));
      $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
      if($sP1<6){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400; $nAkt+=(7*($sP1-1)*86400);} //1...5
      else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400; if($sP1==6) $nAkt-=(7*86400);} //(vor)letzter
      $Dat=@date('Y-m-d w',$nAkt); $nAkt=@mktime(12,0,0,1,2,1+(int)$sJ);
      if(substr($Dat,0,10)<=$sBeg||substr($Dat,0,4)!=$sJ||($aTmp[4]>'0'&&$sM!=(int)substr($Dat,5,2))) $Dat='';
     }while(!$Dat);
    }
    break;
  }
  if($nMax-->0&&substr($Dat,0,10)<=$sEnd){if($Dat) $aDat[]=$Dat;} else $bDo=false;
 }
 if(count($aDat)>0) return $aDat; else return false;
}

function fKalPlainText($s,$t,$aN=array()){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||count($aN)<=0){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  case 'l':
   $aL=explode('||',$s); $s='';
   foreach($aL as $w){if(!$nP=strpos($w,'|')) $s.=$w; else $s.=substr($w,0,$nP).', ';} $s=substr($s,0,-2);
   break;
  case 'u':
   if(KAL_NutzerBenachrFeld>0){
    if($s>'0000'){$sN=$s; if(!$s=(count($aN)>=KAL_NutzerBenachrFeld?$aN[KAL_NutzerBenachrFeld]:'')) $s=$sN;}else $s=KAL_TxAutor0000;
   }
   break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}

function fKalBBToolbar($Nr){
 $X ="\n".' <div style="float:left;margin-right:9px;">';
 $X.="\n".'  '.fDrawToolBtn($Nr,'Bold',   0);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Italic', 2);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Uline',  4);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Center', 6);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Right',  8);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Enum',  10);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Number',12);
 $X.="\n".'  '.fDrawToolBtn($Nr,'Link',  16);
 $X.="\n".' </div>';
 $X.="\n".' <div style="float:left;">';
 $X.="\n".'
   <span class="kalNoBr" style="margin-right:3px;">
   <img class="kalTool" src="'.KAL_Url.'grafik/tbColor.gif" style="vertical-align:top;cursor:default;" title="'.fKalTx(KAL_TxBB_O).'" alt="'.fKalTx(KAL_TxBB_O).'">
   <select class="kalTool" name="kal_Col'.$Nr.'" onchange="fCol('.$Nr.',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.fKalTx(KAL_TxBB_O).'">
    <option value="">-</option>
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
   <span class="kalNoBr">
   <img class="kalTool" src="'.KAL_Url.'grafik/tbSize.gif" style="vertical-align:top;cursor:default;" title="'.fKalTx(KAL_TxBB_S).'" alt="'.fKalTx(KAL_TxBB_S).'">
   <select class="kalTool" name="kal_Siz'.$Nr.'" onchange="fSiz('.$Nr.',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.fKalTx(KAL_TxBB_S).'">
    <option value="">-</option>
    <option value="+3">&nbsp;+3</option>
    <option value="+2">&nbsp;+2</option>
    <option value="+1">&nbsp;+1</option>
    <option value="-1">&nbsp;- 1</option>
    <option value="-2">&nbsp;- 2</option>
   </select>
   </span>
  </div>';
 return $X;
}
function fDrawToolBtn($Nr,$vImg,$nTag){
 $w=fKalTx(constant('KAL_TxBB_'.substr($vImg,0,1)));
 return '<img class="kalTool" src="'.KAL_Url.'grafik/tb'.$vImg.'.gif" onclick="fFmt('.$Nr.','.$nTag.')" title="'.$w.'" alt="'.$w.'">';
}
?>