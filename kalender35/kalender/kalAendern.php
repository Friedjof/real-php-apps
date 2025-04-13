<?php
function fKalSeite(){
 global $kal_FeldName, $kal_FeldType, $kal_EingabeFeld, $kal_EingabeLang, $kal_PflichtFeld, $kal_DetailFeld, $kal_NDetailFeld,
  $kal_Kategorien, $kal_Symbole, $kal_WochenTag;

 $nFelder=count($kal_FeldName); $aW=array(); $aV=array(); $aUpl=array(); $aOh=array(); $aOa=array(); $aOs=array(); $aFehl=array();
 $Et=''; $Es='Fehl'; $sId=''; $bDatenAktion=false; $bOK=false; $sLschTrm='1'; $sSta='0'; $sODt=''; $sNDt=''; $sZentrum='';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 //Formularzugang pruefen
 $bFormEingabe=false; $bFormLogin=false; $bSesOK=false; $bCaptcha=KAL_Captcha&&KAL_AendernCaptcha;
 $nNId=0; $sNutzerEml=''; $sNutzerName=KAL_TxAutor0000; $aN=array();
 $nPwPos=array_search('p',$kal_FeldType); $bPwFeld=$nPwPos>0; $nNuPos=array_search('u',$kal_FeldType);

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
 if($bSesOK){$bCaptcha=false; if(KAL_NEingabeAnders) $kal_EingabeFeld=$GLOBALS['kal_NEingabeFeld'];}

 if($nNuPos>0&&KAL_NEingabeLogin) if(!$bSesOK) if($Et==''){$Et=KAL_TxNutzerLogin; $Es='Meld';}

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET - Datensatz bereitstellen
  if($sId=(isset($_GET['kal_Nummer'])?sprintf('%0d',$_GET['kal_Nummer']):'')){ //Datensatznummer vorhanden
   if(!$bPwFeld){ //Termin ohne Passwort
    if($bSesOK||!KAL_NEingabeLogin||!$nNuPos){ //angemeldet oder JederDarf oder ohneUser
     if(!KAL_SQL){ //Textdaten holen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
      for($i=1;$i<$nSaetze;$i++){
       if(substr($aD[$i],0,$p)==$s){$aW=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); break;}
      }
     }elseif($DbO){ //SQL-Daten
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
       if(!$aW=$rR->fetch_row()) $aW=array(); $rR->close();
      }else $Et=KAL_TxSqlFrage;
     }else $Et=$SqE;
     if(count($aW)>0){ //vorhanden
      $sSta=$aW[1]; array_splice($aW,1,1);
      if(($nNuPos>0&&($nNId==(int)$aW[$nNuPos]||($bSesOK&&KAL_NAendernFremde)))||!$nNuPos){ //eigener Termin
       $aV=$aW; $bFormEingabe=true; if($aW[1]<date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){$Et=KAL_TxAendereZuAlt; $bOK=true;}
       for($i=1;$i<$nFelder;$i++){
        if(KAL_LZeichenstz==0) $aW[$i]=$aW[$i]; elseif(KAL_LZeichenstz==2) $aW[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$i]); else $s=html_entity_decode($aW[$i]);
        switch($kal_FeldType[$i]){
         case 'd': if($aW[$i]) $aW[$i]=fKalAnzeigeDatum($aW[$i]); if($i==1) $sODt=$aW[1]; break;
         case 'b': case 'f': $aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p); break;
         case 'w': case 'n': case '1': case '2': case '3': case 'r': $aW[$i]=str_replace('.',KAL_Dezimalzeichen,$aW[$i]); break;
         case 'e': case 'c': if(!KAL_SQL) $aW[$i]=fKalDeCode($aW[$i]); break;
         case 'p': $aW[$i]=fKalDeCode($aW[$i]); break;
         case '@': if(KAL_EintragszeitNeu&&$kal_FeldName[$i]!='ZUSAGE_BIS') $aW[$i]=trim(fKalAnzeigeDatum(date('Y-m-d')).date(' H:i'));
                   elseif($aW[$i]) $aW[$i]=trim(fKalAnzeigeDatum($aW[$i]).strstr($aW[$i],' ')); break;
       }}
       if($bCaptcha){ //Captcha erzeugen
        $sCapTyp=KAL_CaptchaTyp; $bCapOk=false; $bCapErr=false;
        require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
        if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);
       }
      }else{$Et=KAL_TxNummerFremd; $aW=array();} //fremder Termin
     }else if($Et=='') $Et=KAL_TxNummerUnbek;
    }else{$bFormLogin=true; if($Et==''){$Et=KAL_TxNutzerLogin; $Es='Meld';}} //Login fehlt
   }//Termine mit Passwort
  }else{ //keine Datensatznummer
   $Et=KAL_TxNummerFehlt; $Es='Meld';
  }//Datensatznummer
  $sZentrum=(isset($_GET['kal_Zentrum'])?fKalRq1($_GET['kal_Zentrum']):'');
 }else{ //POST Formularauswertung
  if($sId=(isset($_POST['kal_Nummer'])?sprintf('%0d',$_POST['kal_Nummer']):'')){ //Datensatznummer vorhanden
   if(!$bPwFeld){ //Termin ohne Passwort
    if($bSesOK||!KAL_NEingabeLogin||!$nNuPos){ //angemeldet oder JederDarf oder ohneUser
     if(isset($_POST['kal_Daten'])){ //vom Datenformular
      $bFormEingabe=true; $bDatenAktion=true;
      if(!KAL_SQL){ //Textdaten holen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
       for($i=1;$i<$nSaetze;$i++){
        if(substr($aD[$i],0,$p)==$s){
         $aV=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); $sStV=$aV[1]; array_splice($aV,1,1);
         break;
      }}}
     }else{ //vom Nummernformular
      if(!KAL_SQL){ //Textdaten holen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
       for($i=1;$i<$nSaetze;$i++){
        if(substr($aD[$i],0,$p)==$s){$aW=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); break;}
       }
      }elseif($DbO){ //SQL-Daten
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
        if(!$aW=$rR->fetch_row()) $aW=array(); $rR->close();
       }else $Et=KAL_TxSqlFrage;
      }
      if(count($aW)>0){
       $sSta=$aW[1]; array_splice($aW,1,1);
       if(($nNuPos>0&&$nNId==(int)$aW[$nNuPos])||!$nNuPos){ //eigener Termin
        $aV=$aW; $bFormEingabe=true; if($aW[1]<date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){$Et=KAL_TxAendereZuAlt; $bOK=true;}
        for($i=1;$i<$nFelder;$i++){
         if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $aW[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$i]); else $s=html_entity_decode($aW[$i]);
         switch($kal_FeldType[$i]){
          case 'd': if($aW[$i]) $aW[$i]=fKalAnzeigeDatum($aW[$i]); if($i==1) $sODt=$aW[1]; break;
          case 'b': case 'f': $aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p); break;
          case 'w': case 'n': case '1': case '2': case '3': case 'r': $aW[$i]=str_replace('.',KAL_Dezimalzeichen,$aW[$i]); break;
          case 'e': case 'c': if(!KAL_SQL) $aW[$i]=fKalDeCode($aW[$i]); break;
          case 'p': $aW[$i]=fKalDeCode($aW[$i]); break;
          case '@': if(KAL_EintragszeitNeu&&$kal_FeldName[$i]!='ZUSAGE_BIS') $aW[$i]=trim(fKalAnzeigeDatum(date('Y-m-d')).date(' H:i'));
                    elseif($aW[$i]) $aW[$i]=trim(fKalAnzeigeDatum($aW[$i]).strstr($aW[$i],' ')); break;
        }}
       }else{$Et=KAL_TxNummerFremd; $aW=array();} //fremder Termin
      }else if($Et=='') $Et=KAL_TxNummerUnbek;
     }//vom Nummernformular
    }else{$bFormLogin=true; $Et=KAL_TxNutzerLogin; $Es='Meld';} //nicht angemeldet
   }else{ //Termine mit Passwort
    if($sPw=fKalRq1($_POST['kal_Pw'])){
     if(!KAL_SQL){ //Textdaten holen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
      for($i=1;$i<$nSaetze;$i++){
       if(substr($aD[$i],0,$p)==$s){$aW=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); break;}
      }
     }elseif($DbO){ //SQL-Daten
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
       if(!$aW=$rR->fetch_row()) $aW=array(); $rR->close();
      }else $Et=KAL_TxSqlFrage;
     }
     if(count($aW)>0){
      $sSta=$aW[1]; array_splice($aW,1,1);
      if($sPw==fKalDeCode($aW[$nPwPos])){ //Passwort OK
       $bFormEingabe=true;
       if(isset($_POST['kal_Daten'])){$bDatenAktion=true; $aW=array();}//vom Datenformular
       else{ //vom Nummernformular
        $aV=$aW; if($aW[1]<date('Y-m-d',time()-86400*KAL_BearbAltesNochTage)){$Et=KAL_TxAendereZuAlt; $bOK=true;}
        for($i=1;$i<$nFelder;$i++){
         if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $aW[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$i]); else $s=html_entity_decode($aW[$i]);
         switch($kal_FeldType[$i]){
          case 'd': if($aW[$i]) $aW[$i]=fKalAnzeigeDatum($aW[$i]); if($i==1) $sODt=$aW[1]; break;
          case 'b': case 'f': $aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p); break;
          case 'w': case 'n': case '1': case '2': case '3': case 'r': $aW[$i]=str_replace('.',KAL_Dezimalzeichen,$aW[$i]); break;
          case 'e': case 'c': if(!KAL_SQL) $aW[$i]=fKalDeCode($aW[$i]); break;
          case 'p': $aW[$i]=fKalDeCode($aW[$i]); break;
          case '@':
           if((KAL_EintragszeitNeu||empty($aW[$i]))&&$kal_FeldName[$i]!='ZUSAGE_BIS') $aW[$i]=fKalAnzeigeDatum(date('Y-m-d')).date(' H:i');
           elseif($aW[$i]) $aW[$i]=trim(fKalAnzeigeDatum($aW[$i]).strstr($aW[$i],' '));
           break;
        }}
       }
      }else{$Et=KAL_TxNummerPassw; $aW=array();} //falsches Passwort
     }else if($Et=='') $Et=KAL_TxNummerUnbek;
    }else $Et=KAL_TxNummerPassw;
   }
  }else{ //keine Datensatznummer
   $Et=KAL_TxNummerFehlt; $Es='Meld';
  }//Datensatznummer

  if($bDatenAktion){// Eingaben holen

  if($bCaptcha){ //Captcha behandeln
   $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false;
   require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
   $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $aFehl[0]=true; }
  }
  $bUtf8=((isset($_POST['kal_JSSend'])||$_POST['kal_Utf8']=='1')?true:false);
  $sZentrum=(isset($_POST['kal_Zentrum'])?fKalRq1($_POST['kal_Zentrum']):'');
  $sZ=''; $sFehl=''; $sNPw=''; $bLschTrm=false; $sSta=(isset($_POST['kal_Sta'])?sprintf('%0d',$_POST['kal_Sta']):(KAL_Direktaendern=='1'?'1':'2'));
  $sODt=(isset($_POST['kal_ODt'])?$_POST['kal_ODt']:'');
  for($i=1;$i<$nFelder;$i++) //kal_Oh: hochgeladene  kal_Oa: alte
   {$aOh[$i]=(isset($_POST['kal_Oh'.$i])?$_POST['kal_Oh'.$i]:''); $aOa[$i]=(isset($_POST['kal_Oa'.$i])?$_POST['kal_Oa'.$i]:'');}
  for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){ //alle Eingabefelder
   $s=str_replace('~@~','\n ',stripslashes(@strip_tags(str_replace('\n ','~@~',str_replace("\r",'',trim($_POST['kal_F'.$i]))))));
   $t=$kal_FeldType[$i]; $aOs[$i]=''; // $aOs: zu speichernde
   if(strlen($s)>0||!$kal_PflichtFeld[$i]||$t=='b'||$t=='f'){
    if($bUtf8||KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); elseif(KAL_Zeichensatz==1) $s=html_entity_decode($s);
    if($t!='m'&&$t!='g') $s=str_replace('"',"'",$s); $v=$s; //s:Eingabe, v:Speicherwert
    switch($t){
    case 't': if($kal_FeldName[$i]=='KAPAZITAET') $s=str_replace(' ','',str_replace(' ','',$s)); $v=$s; break;
    case 'm': case 'a': case 'k': case 's': case 'j': case '#': case 'v': case 'g': case 'x': break; //Memo,Kategorie,Auswahl,Ja/Nein,StreetMap
    case 'd': if($s) //Datum
     if($v=fKalErzeugeDatum($s)){$s=fKalAnzeigeDatum($v); if($i==1) $sNDt=$s;} else $aFehl[$i]=true;
     break;
    case '@': //EintragsDatum
     if($kal_FeldName[$i]!='ZUSAGE_BIS'){
      if(KAL_EintragszeitNeu){$v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
      else{
       if($s){if($v=fKalErzeugeDatum($s)) $v=substr($v,0,10).strstr($s,' '); else $v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
       else{$v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
     }}elseif($s){
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
    case 'l': //Link oder E-Mail
     if($p=strpos(strtolower(substr($s,0,7)),'ttp://')){$s=substr($s,$p+6); $v=$s;} break;
    case 'b': //Bild
     if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i]; //kal_Up: neue Datei; kal_Dl: zu löschen
     $UpNaJS=(isset($_POST['kal_UpNa_'.$i])?fKalDateiname(basename($_POST['kal_UpNa_'.$i])):'');
     $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):'');
     if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
     if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
      if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_BildMaxKByte)||KAL_BildMaxKByte<=0){
       if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['kal_Up'.$i]['tmp_name']);
       elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['kal_Up'.$i]['tmp_name']);
       elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['kal_Up'.$i]['tmp_name']);
       if(!empty($Src)){
        // Demo
        imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
       }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,KAL_TxBildOeffnen);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_BildMaxKByte,KAL_TxBildGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxBildTyp);
     }
     $aOs[$i]=$v; break;
    case 'f': //Datei
     if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i];
     $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
     $bUpEx=(strpos('#;'.KAL_DateiEndungen.';',';'.substr($UpEx,1).';')>0); if(!KAL_DateiEndgPositiv) $bUpEx=!$bUpEx;
     if($bUpEx&&($UpEx>'')){
      // Demo
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxDateiTyp);
     }elseif($s>'') if(isset($_POST['kal_Dl'.$i])&&$_POST['kal_Dl'.$i]=='1'){ //hochgeladene Datei löschen
      @unlink(KAL_Pfad.'temp/'.$s); $s=''; $v=''; $aOh[$i]='';
     }
     $aOs[$i]=$v; break;
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
    case 'p': $v=fKalEnCode($s); $sNPw=$s; break;
    case 'u': $s=sprintf('%04d',substr(KAL_Session,17,4)); $v=$s; break;
    }$aW[$i]=$s;
    if(KAL_SZeichenstz!=0) if(KAL_SZeichenstz==2) $v=iconv('ISO-8859-1','UTF-8//TRANSLIT',$v); else $v=htmlentities($v,ENT_COMPAT,'ISO-8859-1');
    if(!KAL_SQL) $sZ.=';'.str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
    else $sZ.=',kal_'.$i.'="'.str_replace("\n","\r\n",str_replace('\n ',"\n",str_replace('"','\"',$v))).'"';
   }else{$aFehl[$i]=true; $aW[$i]=''; if(!KAL_SQL) $sZ.=';';}
  }elseif($kal_FeldType[$i]=='u'){
   if(!KAL_SQL) $sZ.=';'.sprintf('%04d',substr(KAL_Session,17,4));
   else $sZ.=',kal_'.$i.'="'.sprintf('%04d',substr(KAL_Session,17,4)).'"';
  }elseif(!KAL_SQL) $sZ.=';'.str_replace("\n",'\n ',$aV[$i]); //nFelder

  if(isset($_POST['kal_LschTrm'])&&($sLschTrm=$_POST['kal_LschTrm'])){ //Loeschen ueberpruefen
   if($sLschTrm=='2'){$bLschTrm=true; $sFehl=''; $aFehl=array(); $sLschTrm='1';} else $sLschTrm='2';
  }

  if($sLschTrm!='2'){ //loeschen nicht beantragt
  if($sFehl==''){ //alles OK, eintragen
   if(count($aFehl)==0){ //keine Eingabefehler

    $Et='Demoversion: Der Termin bleibt wie vorher!';

   }else $Et=KAL_TxEingabeFehl;
  }else $Et=$sFehl;
  }else $Et=KAL_TxLoeschFrage;

  }//Datenaktion
 }//POST

 //Beginn der Ausgabe
 if($Et==''){$Et=KAL_TxAendereMeld; $Es='Meld';} $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

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
 $X.="\n".'<input type="hidden" name="kal_Aktion" value="aendern">';
 $X.="\n".'<script>';
 $X.="\n".' var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";';
 $X.="\n".' if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";';
 $X.="\n"." document.writeln('<input type=\"hidden\" name=\"kal_Utf8\" value=\"'+sUtf8+'\">');";
 $X.="\n".'</script>';
 if(KAL_Session!='') $X.="\n".'<input type="hidden" name="kal_Session" value="'.substr(KAL_Session,17,12).'">';
 if($sZentrum) $X.="\n".'<input type="hidden" name="kal_Zentrum" value="1">';
 if(isset($sId)&&$sId>'') $X.="\n".'<input type="hidden" name="kal_Nummer" value="'.$sId.'">';
 if(isset($sPw)&&$sPw>'') $X.="\n".'<input type="hidden" name="kal_Pw" value="'.$sPw.'">';
 $X.="\n".'<input type="hidden" name="kal_ODt" value="'.$sODt.'">';
 $X.="\n".'<input type="hidden" name="kal_Daten" value="1">';

 $X.="\n".'<div class="kalTabl">';
 //Eingabeformularzeilen
 $X.="\n".' <div class="kalTbZl1">'; $nFarb=2; $bMitBild=false;
 $X.="\n".'  <div class="kalTbSp1">'.fKalTx(KAL_TxNummer).'</div>';
 $X.="\n".'  <div class="kalTbSp2" title="'.((int)$sId>0?fKalTx(KAL_TxNummer):fKalTx(KAL_TxAendereVmk)).'">'.sprintf('%0'.KAL_NummerStellen.'d',$sId).'</div>'."\n".' </div>';
 $aVg=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); $aTrn=explode(',','#,'.KAL_EingabeTrenner); //Hinweise und Kategorien holen
 for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  $v=(isset($aW[$i])?str_replace('`,',';',$aW[$i]):''); //Feldinhalt
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
  case 'a': case 'k': case 's': //Aufzählung/Kategorie
   reset($aHlp); $sO=''; foreach($aHlp as $w) $sO.='<option value="'.fKalTx($w).'"'.($v==$w?' selected="selected"':'').'>'.fKalTx($w).'</option>';
   $X.= $sZ.'<select class="kalEing" name="kal_F'.$i.'" size="1"><option value="">---</option>'.substr($sO,strpos($sO,'<option',9)).'</select></div>';
   break;
  case 'd': // Datum
   $X.= $sZ.'<input class="kalTCal" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="10"> <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.fKalDatumsFormat().'</span></div>';
   break;
  case '@': // EintragsDatum
   if($kal_FeldName[$i]!='ZUSAGE_BIS') $X.= $sZ.$v.'<input type="hidden" name="kal_F'.$i.'" value="'.$v.'"></div>';
   else $X.=$sZ.'<input class="kalEing" style="width:12em;" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16"> <span class="kalMini">'.fKalTx(KAL_TxFormat.' '.fKalDatumsFormat().' '.KAL_TxOder.' '.fKalDatumsFormat().' '.KAL_TxSymbUhr).'</span></div>';
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
   $X.= $sZ.'<input class="kalEing" type="file" name="kal_Up'.$i.'" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif"><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'"></div>'; $bMitBild=true;
   if($v) $X.="\n".'   <div class="kalNorm" style="float:left;"><input class="kalCheck" type="checkbox" name="kal_Dl'.$i.'" value="1"><input type="hidden" name="kal_F'.$i.'" value="'.$v.'"><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'"> <span class="kalMini">'.$v.' '.fKalTx(KAL_TxLoeschen).'</span></div>';
   $X.="\n".'   <div class="kalNorm" style="text-align:right;padding:1px;line-height:1.4em;"><span class="kalMini">'.(KAL_BildMaxKByte>0?'(max. '.KAL_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': // Datei
   $X.= $sZ.'<input class="kalEing" type="file" name="kal_Up'.$i.'"><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'"></div>';
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

 if(KAL_AendernOnOff&&$bSesOK){
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">
  <div class="kalTbSp1">'.fKalTx(KAL_TxStatus).'</div>
  <div class="kalTbSp2"><span class="kalNoBr"><input class="kalRadio" type="radio" name="kal_Sta" value="1"'.($sSta=='1'?' checked="checked"':'').(KAL_Direktaendern!='1'?' onclick="this.checked=false;"':'').'> '.fKalTx(KAL_TxOnline).'</span> &nbsp; <span class="kalNoBr"><input class="kalRadio" type="radio" name="kal_Sta" value="'.(KAL_Direktaendern=='1'?'0':'2').'"'.(($sSta=='0'||$sSta=='2')?' checked="checked"':'').'> '.fKalTx(KAL_Direktaendern=='1'?KAL_TxOffline:KAL_TxVormerk).'</span>'.(KAL_AendernLschOnOff?' &nbsp; <span class="kalNoBr"><input class="kalRadio" type="radio" name="kal_Sta" value="3"'.($sSta=='3'?' checked="checked"':'').'> '.fKalTx(KAL_TxLoeschen).'</span>':'').'</div>
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
  </div>'; if(--$nFarb<=0) $nFarb=2;
 }

 if(KAL_AendernMitLoeschen){
  $X.="\n".' <div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">'.fKalTx(KAL_TxTermin).'&nbsp;'.fKalTx(KAL_TxLoeschen).'</div><div class="kalTbSp2"><div class="kalNorm" style="float:left;width:50px;"><input class="kalCheck" type="checkbox" name="kal_LschTrm" value="'.$sLschTrm.($sLschTrm!='2'?'':'" checked="checked').'"> <img class="kalIcon" src="'.KAL_Url.'grafik/iconLoeschen.gif" title="'.fKalTx(KAL_TxLoeschen).'" alt="'.fKalTx(KAL_TxLoeschen).'"></div><div class="kalNorm" style="margin-left:60px;text-align:right;">* <span class="kalMini">'.fKalTx(KAL_TxPflicht).'</span></div></div></div>';
  if(--$nFarb<=0) $nFarb=2;
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

 }else{ //Ende Eingabefomular - Beginn Loginformulare

 if(!$bFormLogin){ //Terminlogin
 $X.='
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="aendern">';
 if(KAL_Session!='') $X.="\n".'<input type="hidden" name="kal_Session" value="'.substr(KAL_Session,17,12).'">';
 $X.='
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxTerminNr).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_Nummer" value="'.$sId.'"></div>
  </div>';
 if($bPwFeld) $X.='
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxPasswort).'<div  class="kalNorm"><span class="kalMini">'.fKalTx(KAL_TxPassHilfe).'</span></div></div>
   <div class="kalTbSp2"><input class="kalEing" type="password" name="kal_Pw" maxlength="16"></div>
  </div>';
 $X.='
 </div>
 <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxEingabe).'" title="'.fKalTx(KAL_TxEingabe).'"></div>
 </form>
 <p><span class="kalMini" style="width:300px;back;">'.fKalTx(KAL_TxNummerHilfe).'</span></p>
 '."\n";

 }else{ //Benutzerlogin

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
 </div>'."\n";
 if($bCaptcha){
  if(!isset($Cap)){
   $sCapTyp=KAL_CaptchaTyp; $bCapOk=false; $bCapErr=false;
   require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);
  }
  $X.=' <input type="hidden" name="kal_CaptchaAntwort" value=""><input type="hidden" name="kal_CaptchaCode" value="'.$Cap->PublicKey.'"><input type="hidden" name="kal_CaptchaFrage" value="'.fKalTx($Cap->Question).'"><input name="kal_CaptchaTyp" type="hidden" value="'.$Cap->Type.'">';
 }
 $X.='
 <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxAnmelden).'" title="'.fKalTx(KAL_TxAnmelden).'"></div>
 </form>'."\n";
 }//Benutzerlogin

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