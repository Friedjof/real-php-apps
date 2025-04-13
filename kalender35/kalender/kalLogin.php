<?php
function fKalTestLogin(){ //Login testen und Session liefern
 global $kal_NutzerFelder;

 $Ret=false; $DbO=NULL;
 if(isset($_POST['kal_Schritt'])&&$_POST['kal_Schritt']=='login'){
  if(KAL_SQL){ //SQL-Verbindung oeffnen
   $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
   if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else $DbO=NULL;
  }
  $aNF=$kal_NutzerFelder; array_splice($aNF,1,1); $nNutzerFelder=count($aNF);
  for($i=2;$i<4;$i++) if(isset($_POST['kal_F'.$i])){ //2 Eingabefelder
   $s=str_replace('"',"'",@strip_tags(stripslashes(trim($_POST['kal_F'.$i]))));
   $aW[$i]=(KAL_Zeichensatz==0?$s:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else $aW[$i]='';
  if(($sNam=$aW[2])&&($sPw=$aW[3])){ //Logindaten
   $s=fKalInCode($sPw); $sSes='';
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sEml=fKalInCode($sNam); $sNam=fKalInCode(strtolower($sNam));
    for($i=1;$i<$nSaetze;$i++){
     $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
     if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
      $aW=$a; $aW[2]=fKalDeCode($a[2]); $aW[3]=$sPw; $aW[4]=fKalDeCode($a[4]);
      $aFehl=fKalPflichtFelder($aW,$nNutzerFelder);
      if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
       $sSes=sprintf('%04d%08d',$a[0],(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
       $s=rtrim($aD[$i]); $aD[$i]=$a[0].';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);}
       else $sSes='';
      }
      break;
    }}
   }elseif($DbO){ //bei SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE passwort="'.$s.'" AND(benutzer="'.fKalDtCode(strtolower($sNam)).'" OR email="'.fKalDtCode($sNam).'")')){
     $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
     if($i==1){ //gefunden
      array_splice($a,1,1);
      if(KAL_LZeichenstz>0) for($i=2;$i<5;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
      $aW=$a; $aW[3]=$sPw;
      $aFehl=fKalPflichtFelder($aW,$nNutzerFelder);
      if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
       $sSes=sprintf('%04d%08d',$a[0],(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
       if(!$DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET session='.substr($sSes,4).' WHERE nr="'.$a[0].'"')) $sSes='';
     }}
    }
   }//SQL
   if($sSes!=''){$Ret='&amp;kal_Session='.$sSes; define('KAL_NeuSession','&amp;kal_Session='.$sSes);}
 }}
 return $Ret;
}

function fKalLogSeite(){ //Benutzerverwaltung
 global $kal_NutzerFelder, $kal_NutzerPflicht;

 $Et=''; $Es='Fehl'; array_splice($kal_NutzerFelder,1,1); $aNPfl=$kal_NutzerPflicht; array_splice($aNPfl,1,1); $nFelder=count($kal_NutzerFelder);
 $sForm='LoginForm'; $sNaechst=''; $sSes=''; $sId=''; $sPw=''; $aW=array('0','0','','',''); $aFehl=array(); $bOK=false; $kal_Zentrum=false;
 $bCaptcha=false; $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 //SQL-Verbindung oeffnen
 $DbO=NULL;
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 if($_SERVER['REQUEST_METHOD']!='POST'){ $kal_Zentrum=KAL_NutzerZentrSel; //GET
  if($bCaptcha=KAL_Captcha||KAL_LoginCaptcha){ //Captcha erzeugen
   require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);
  }
  if(isset($_GET['kal_Session'])){ //abmelden
   $sSes=fKalRq1($_GET['kal_Session']);
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=(int)substr($sSes,0,4).';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$sZ=substr(rtrim($aD[$i]),$p); break;}
    if(isset($sZ)&&(substr($sSes,4)==substr($sZ,0,8))){ //Benutzer und Session gefunden
     $aD[$i]=$s.'~'.substr($sZ,8)."\n";
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Et=KAL_TxNutzerLogout; $Es='Erfo';
     }else $Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte);
    }
   }elseif($DbO){ //SQL
    if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET session="~" WHERE nr="'.substr($sSes,0,4).'" AND session="'.substr($sSes,4).'"')){
     if($DbO->affected_rows>0){$Et=KAL_TxNutzerLogout; $Es='Erfo';}
    }else $Et=KAL_TxSqlFrage;
   }//SQL
  }//abmelden
 }else{ //POST Formularauswertung
  $sSes=substr(KAL_Session,17,12); $sSchritt=(isset($_POST['kal_Schritt'])?strip_tags($_POST['kal_Schritt']):'');
  for($i=1;$i<$nFelder;$i++) if(isset($_POST['kal_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['kal_F'.$i]))));
   $aW[$i]=(KAL_Zeichensatz==0?$s:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else $aW[$i]='';

  //Loginversuch auswerten
  if($sSchritt=='login'){
   if($bCaptcha=KAL_Captcha||KAL_LoginCaptcha){ //Captcha behandeln
    require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
    if(isset($_POST['kal_CaptchaCode'])){
     $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
     $Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap);
    }
   }
   $kal_Zentrum=(isset($_POST['kal_Zentrum'])?$_POST['kal_Zentrum']:false);
   if(($sNam=$aW[2])&&($sPw=$aW[3])){
    $s=fKalInCode($sPw);
    if(!KAL_SQL){ //Textdateien
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sEml=fKalInCode($sNam); $sNam=fKalInCode(strtolower($sNam));
     for($i=1;$i<$nSaetze;$i++){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
      if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
       $sId=$a[0]; $aW=$a; $aW[2]=fKalDeCode($a[2]); $aW[3]=$sPw; $aW[4]=fKalDeCode($a[4]);
       for($j=5;$j<$nFelder;$j++){
        $aW[$j]=str_replace('`,',';',$a[$j]);
        if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $aW[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$j]); else $aW[$j]=html_entity_decode($aW[$j]);
       }
       $aFehl=fKalPflichtFelder($aW,$nFelder);
       if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
        if(defined('KAL_NeuSession')) $sSes=KAL_NeuSession;
        else{
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
         $s=rtrim($aD[$i]); $aD[$i]=$sId.';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
         if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);}
         else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $sSes='';}
        }
       }else $sSes='';
       break;
     }}
    }elseif($DbO){ //bei SQL
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE passwort="'.$s.'" AND(benutzer="'.fKalDtCode(strtolower($sNam)).'" OR email="'.fKalDtCode($sNam).'")')){
      $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
      if($i==1){ //gefunden
       $sId=$a[0]; array_splice($a,1,1);
       if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
       $aW=$a; $aW[3]=$sPw;
       $aFehl=fKalPflichtFelder($aW,$nFelder);
       if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
        if(defined('KAL_NeuSession')) $sSes=KAL_NeuSession;
        else{
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
         if(!$DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET session='.substr($sSes,4).' WHERE nr="'.$sId.'"')) $Et=KAL_TxSqlAendr;
        }
       }else $sSes='';
      }
     }else $Et=KAL_TxSqlFrage;
    }//SQL
    if($sId!=''){ //gefunden
     if(empty($sSes)||isset($_POST['kal_Daten'])&&$_POST['kal_Daten']){ //Daten editieren
      $Et=KAL_TxNutzerPruefe; $Es='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen';
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;}
     }else{ //sofort zur Liste
      if(!defined('KAL_NeuSession')) define('KAL_NeuSession',$sSes); $sForm='##';
      if(!$kal_Zentrum){
       include(KAL_Pfad.'kalDaten.php'); fKalDaten(true,true); include(KAL_Pfad.'kalListe.php');
      }else{include(KAL_Pfad.'kalNZentrum.php');}
      $X=fKalSeite();
     }
    }else $Et=KAL_TxNutzerFalsch;
   }else $Et=KAL_TxNutzerNamePass;

  //Benutzerdaten pruefen/aendern
  }elseif($sSchritt=='pruefen'){
   if(($sId=fKalRq1($_POST['kal_Id']))&&($sPw=fKalRq1($_POST['kal_Pw']))){
    if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $sPw=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sPw); else $sPw=html_entity_decode($sPw);
    $aW[2]=strtolower($aW[2]); $s=fKalInCode($sPw);
    $aFehl=fKalPflichtFelder($aW,$nFelder);
    if(KAL_NutzerDSE1) if(isset($_POST['kal_DSE1'])&&$_POST['kal_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(KAL_NutzerDSE2) if(isset($_POST['kal_DSE2'])&&$_POST['kal_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(count($aFehl)==0){
     if(!KAL_SQL){ //Textdateien
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sNam='#;'; $bGefunden=false;
      for($i=1;$i<$nSaetze;$i++){
       $aN=explode(';',rtrim($aD[$i]));
       if($aN[0]!=$sId||$aN[4]!=$s) $sNam.=$aN[3].';';
       else{$sOldSes=$aN[1]; array_splice($aN,1,1); $a=$aN; $k=$i; $bGefunden=true;}
      }
      if($bGefunden){ //gefunden
       $sForm='NutzerForm'; $sNaechst='pruefen';
       $aW[0]=$sId; $aW[1]=$a[1]; $a[2]=fKalDeCode($a[2]); $a[3]=fKalDeCode($a[3]); $a[4]=fKalDeCode($a[4]);
       for($j=5;$j<$nFelder;$j++){
        $a[$j]=str_replace('`,',';',$a[$j]);
        if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
       }
       if($a!=$aW){ //verändert
        if($a[2]==$aW[2]||!strpos($sNam,';'.fKalInCode($aW[2]).';')){ //Benutzername unverändert oder frei
         if($a[1]=='1') $sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); //aktiv, Session erzeugen, ca. 40 Minuten
         $s=$sId.';'.($sSes?substr($sSes,4):$sOldSes).';'.$a[1].';'.fKalInCode($aW[2]).';'.fKalInCode($aW[3]).';'.fKalInCode($aW[4]);
         for($j=5;$j<$nFelder;$j++) $s.=';'.str_replace(';','`,',fKalDtCode($aW[$j])); $aD[$k]=$s."\n";
         if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Et=KAL_TxNutzerGeaendert; $Es='Erfo'; $sPw=$aW[3];
         }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $sSes='';}
        }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
       }elseif($sSes==''){ //Daten fertig
        if($a[1]=='1'){ //aktiv, Session erzeugen
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
         $s=rtrim($aD[$k]); $aD[$k]=$sId.';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
         if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Et=KAL_TxKeineAenderung; $Es='Meld';
         }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $sSes='';}
        }else{$Et=KAL_TxKeineAenderung; $Es='Meld';}
       }else{$Et=KAL_TxNutzerOK; $Es='Erfo'; $bOK=true;} //Session OK
      }else $Et=KAL_TxNutzerFalsch;
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'" AND passwort="'.$s.'"')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){ //gefunden
        $sForm='NutzerForm'; $sNaechst='pruefen';
        array_splice($a,1,1); $aW[0]=$sId; $aW[1]=$a[1]; $a[3]=fKalDeCode($a[3]); $s='';
        if(KAL_LZeichenstz>0) for($j=2;$j<$nFelder;$j++) if($j!=3) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
        if($a[2]!=$aW[2]) $s.=', benutzer="'.fKalDtCode($aW[2]).'"';
        if($a[3]!=$aW[3]) $s.=', passwort="'.fKalInCode($aW[3]).'"';
        if($a[4]!=$aW[4]) $s.=', email="'.fKalDtCode($aW[4]).'"';
        for($j=5;$j<$nFelder;$j++) if($a[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.fKalDtCode($aW[$j]).'"';
        if($s!=''){ //veraendert
         if($a[2]!=$aW[2]){ //Benutzname
          if($rR=$DbO->query('SELECT nr FROM '.KAL_SqlTabN.' WHERE benutzer="'.fKalDtCode($aW[2]).'"')){
           $i=$rR->num_rows; $rR->close();
          }else $i=1;
         }else $i=0;
         if($i==0){ //Benutzername unverändert oder frei
          if($a[1]=='1'){$sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); $s.=', session="'.substr($sSes,4).'"';} //aktiv, Session erzeugen ca. 40 Minuten
          if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET '.substr($s,2).' WHERE nr="'.$sId.'"')){
           $Et=KAL_TxNutzerGeaendert; $Es='Erfo'; $sPw=$aW[3];
          }else{$Et=KAL_TxSqlAendr; $sSes='';}
         }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
        }elseif($sSes==''){ //Daten fertig
         if($a[1]=='1'){ //aktiv, Session erzeugen
          $sSes=sprintf('%04d%08d',$sId,(time()+(60*KAL_SessionZeit))>>6); //ca. 40 Minuten
          if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET session="'.substr($sSes,4).'" WHERE nr="'.$sId.'"')){
           $Et=KAL_TxKeineAenderung; $Es='Meld';
          }else{$Et=KAL_TxSqlAendr; $sSes='';}
         }else{$Et=KAL_TxKeineAenderung; $Es='Meld';}
        }else{$Et=KAL_TxNutzerOK; $Es='Erfo'; $bOK=true;} //Session OK
       }else $Et=KAL_TxNutzerFalsch;
      }else $Et=KAL_TxSqlFrage;
     }//SQL
    }else{$Et=KAL_TxEingabeFehl; $sForm='NutzerForm'; $sNaechst='pruefen';}
   }else $Et=KAL_TxNutzerFalsch;

  //neuer Benutzer
  }elseif($sSchritt=='neu'||$sSchritt=='erfassen'){
   if($bCaptcha=(KAL_Captcha||KAL_LoginCaptcha)&&$sSchritt=='neu'){ //Captcha behandeln
    require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
    $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
    if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)){
     $bCapOk=true; $Cap->Delete(); $bCaptcha=false;
    }else{$bCapErr=true; $aFehl[0]=true; $sNeuName=$aW[2]; $aW[2]='';}
   }else $bCapOk=true;
   if($bCapOk){
    $sForm='NutzerForm'; $sNaechst='erfassen'; $aW[2]=strtolower($aW[2]); srand((double)microtime()*1000000); $sCod=rand(1000,9999);
    $aFehl=fKalPflichtFelder($aW,$nFelder);
    if($sSchritt=='erfassen'){
     if(KAL_NutzerDSE1) if(isset($_POST['kal_DSE1'])&&$_POST['kal_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
     if(KAL_NutzerDSE2) if(isset($_POST['kal_DSE2'])&&$_POST['kal_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    }
    if(count($aFehl)==0){
     if(!KAL_SQL){ //Textdateien
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sNam='#;';
      for($i=1;$i<$nSaetze;$i++){$a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $sNam.=$a[2].';';}
      if(!strpos($sNam,';'.fKalInCode($aW[2]).';')){
       $s=rtrim($aD[0]);
       if(substr($s,0,7)=='Nummer_'){ //neue Nummer
        $i=strpos($s,';'); $sId=1+substr($s,7,$i-7); $aD[0]=substr_replace($s,$sId,7,$i-7)."\n";
       }else{ //Kopfzeile neu aufbauen
        $sId=0; for($i=1;$i<$nSaetze;$i++) $sId=max($sId,substr($aD[$i],0,5));
        $s='Nummer_'.(++$sId).';Session;aktiv'; for($j=2;$j<$nFelder;$j++) $s.=';'.$kal_NutzerFelder[$j]; $aD[0]=$s."\n";
       }
       $s=$sId.';'.$sCod.';0;'.fKalInCode($aW[2]).';'.fKalInCode($aW[3]).';'.fKalInCode($aW[4]);
       for($j=5;$j<$nFelder;$j++) $s.=';'.str_replace(';','`,',fKalDtCode($aW[$j]));
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".$s."\n"); fclose($f);
        $Et=KAL_TxNutzerNeu; $Es='Erfo'; $bOK=true;
       }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $sId='';}
      }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT nr FROM '.KAL_SqlTabN.' WHERE benutzer="'.fKalDtCode($aW[2]).'"')){
       $i=$rR->num_rows; $rR->close();
       if($i==0){
        $s=',benutzer,passwort,email'; $t=',"'.fKalDtCode($aW[2]).'","'.fKalInCode($aW[3]).'","'.fKalDtCode($aW[4]).'"';
        for($j=5;$j<$nFelder;$j++){$s.=',dat_'.$j; $t.=',"'.fKalDtCode($aW[$j]).'"';}
        if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabN.' (session,aktiv'.$s.') VALUES("'.$sCod.'","0"'.$t.')')){
         if($sId=$DbO->insert_id){$Et=KAL_TxNutzerNeu; $Es='Erfo'; $bOK=true;}else $Et=KAL_TxSqlEinfg;
        }else $Et=KAL_TxSqlEinfg;
       }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
      }else $Et=KAL_TxSqlFrage;
     }//SQL
     if($sId!=''){
      $sMlTx=''; for($j=2;$j<$nFelder;$j++) $sMlTx.="\n".strtoupper($kal_NutzerFelder[$j]).': '.($j!=3?$aW[$j]:'*****'); $sWww=fKalHost();
      if(KAL_NutzerNeuMail){
       $sLnk=KAL_Url.'kalender.php?kal_Aktion=ok'.$sCod.$sId;
       require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($aW[4]);
       if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
       $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxNutzerNeuBtr)); $Mailer->SetReplyTo($aW[4]);
       if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#L',$sLnk,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxNutzerNeuTxt))));
       $Mailer->Send();
      }
      if(KAL_NutzerNeuAdmMail&&!KAL_FreischaltAdmin){
       require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(strpos(KAL_EmpfNutzer,'@')>0?KAL_EmpfNutzer:KAL_Empfaenger);
       if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
       $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',sprintf('%04d',$sId),str_replace('#N',sprintf('%04d',$sId),KAL_TxNutzNeuAdmBtr));
       if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",KAL_TxNutzNeuAdmTxt)));
       $Mailer->Send();
      }
     }
    }else $Et=KAL_TxEingabeFehl;
   }else $Et=KAL_TxCaptchaFehl;

  //Passwort vergessen
  }elseif($sSchritt=='vergessen'){
   if($bCaptcha=KAL_Captcha||KAL_LoginCaptcha){ //Captcha behandeln
    require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
    $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
    if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
   }else $bCapOk=true;
   if($bCapOk){
    if($sNam=$aW[2]){
     if(!KAL_SQL){ //Textdateien
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sEml=fKalInCode($sNam); $sNam=fKalInCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
       if($a[2]==$sNam||$a[4]==$sEml){$sId=$a[0]; $sNam=fKalDeCode($a[2]); $sPass=fKalDeCode($a[3]); $sEml=fKalDeCode($a[4]); break;}
      }
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE benutzer="'.fKalDtCode(strtolower($sNam)).'" OR email="'.fKalDtCode($sNam).'"')){
       if($a=$rR->fetch_row()){ //gefunden
        array_splice($a,1,1); $sId=$a[0];
        if(KAL_LZeichenstz>0) for($j=2;$j<5;$j++) if($j!=3) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
        $sNam=$a[2]; $sPass=fKalDeCode($a[3]); $sEml=$a[4];
       }
       $rR->close();
      }else $Et=KAL_TxSqlFrage;
     }//SQL
     if(isset($sPass)){
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $sWww=fKalHost();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxNutzerDatBtr)); $Mailer->SetReplyTo($sEml);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $Mailer->Text=str_replace('#P',$sPass,str_replace('#B',$sNam,str_replace('#N',sprintf('%04d',$sId),str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxNutzerDaten)))));
      if($Mailer->Send()){
       $Et=KAL_TxNutzerSend; $Es='Erfo'; $bOK=true;
       if($bCaptcha){$Cap->Delete(); $bCapErr=false; $bCapOk=false; if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);} //Captcha loeschen und neu
      }else $Et=KAL_TxSendeFehl;
     }else $Et=KAL_TxNutzerFalsch;
    }else $Et=KAL_TxNutzerNameMail;
   }else $Et=KAL_TxCaptchaFehl;
  }

 }//POST

 //Beginn der Ausgabe
 if($Et==''){$Et=KAL_TxNutzerLogin; $Es='Meld';}
 if(empty($X)) $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

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

 if($sForm=='LoginForm'){ //Loginformulare
 $X.='
 <p class="kalMeld" style="margin-top:20px;">'.fKalTx(KAL_LoginLogin).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="login">
 <input type="hidden" name="kal_Schritt" value="login">
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1 kalNoBr">'.fKalTx(KAL_TxBenutzername).'<br>'.fKalTx(KAL_TxOder).'<br>'.fKalTx(KAL_TxMailAdresse).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_F2" value="'.fKalTx($aW[2]).'" maxlength="100"></div>
  </div>
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxPasswort).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="password" name="kal_F3" maxlength="16"></div>
  </div>';
 if(KAL_NutzerZentrum){ //Benutzerzentrum zeigen
 $X.='
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxNZentrum).'</div>
   <div class="kalTbSp2"><input class="kalCheck" type="checkbox" name="kal_Zentrum" value="1"'.($kal_Zentrum?' checked="checked"':'').'> '.fKalTx(KAL_TxNZentrZeigen).'</div>
  </div>';
 }
 elseif(KAL_NutzerAendern){ //Nutzerdaten aendern
 $X.='
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxBenutzerDat).'</div>
   <div class="kalTbSp2"><input class="kalCheck" type="checkbox" name="kal_Daten" value="1"> '.fKalTx(KAL_TxNDatAendern).'</div>
  </div>';
 }
 $X.='
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
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_F2" value="'.(isset($sNeuName)?fKalTx($sNeuName):'').'" maxlength="25"></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxCaptchaFeld).'*</div>
   <div class="kalTbSp2">
    <div class="kalNorm"><span class="capQry">'.fKalTx($Cap->Type!='G'?$Cap->Question:KAL_TxCaptchaHilfe).'</span></div>
    <div class="kalNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.KAL_Url.KAL_CaptchaPfad.$Cap->Question.'">').'</span></div>
    <div class="kalNorm">
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

 if(KAL_PasswortSenden){ //Passwort zusenden
 $X.='
 <p class="kalMeld">'.fKalTx(KAL_LoginVergessen).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="login">
 <input type="hidden" name="kal_Schritt" value="vergessen">
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1 kalNoBr">'.fKalTx(KAL_TxBenutzername).'<br>'.fKalTx(KAL_TxOder).'<br>'.fKalTx(KAL_TxMailAdresse).'</div>
   <div class="kalTbSp2"><input class="kalEing" type="text" name="kal_F2" value="'.fKalTx($aW[2]).'" maxlength="100"></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxCaptchaFeld).'*</div>
   <div class="kalTbSp2">
    <div class="kalNorm"><span class="capQry">'.fKalTx($Cap->Type!='G'?$Cap->Question:KAL_TxCaptchaHilfe).'</span></div>
    <div class="kalNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.KAL_Url.KAL_CaptchaPfad.$Cap->Question.'">').'</span></div>
    <div class="kalNorm">
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
 </div>'."\n";
 if(!$bOK) $X.=' <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'"></div>'."\n"; else $X.='&nbsp;';
 $X.=' </form>';
 }

 }elseif($sForm=='NutzerForm'){ //Benutzerdaten

 $nFarb=2; if($aW[1]=='1'){$s=''; $t='Grn';}elseif($aW[1]=='0'){$s=KAL_TxNicht.' '; $t='Rot';}elseif($aW[1]=='2'){$s=KAL_TxMailAdresse.' '.KAL_TxAktiv.', '.KAL_TxBenutzer.' '.KAL_TxNicht.' '; $t='RtGn';}else{$s=KAL_TxNicht.' '; $t='Rot';}
 if(KAL_DSEPopUp&&(KAL_NutzerDSE1||KAL_NutzerDSE2)) $X.="\n".'<script>function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.KAL_DSEPopupW.',height='.KAL_DSEPopupH.',left='.KAL_DSEPopupX.',top='.KAL_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 $X.='
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="login">
 <input type="hidden" name="kal_Session" value="'.$sSes.'">
 <input type="hidden" name="kal_Schritt" value="'.$sNaechst.'">
 <input type="hidden" name="kal_Id" value="'.$sId.'">
 <input type="hidden" name="kal_Pw" value="'.fKalTx($sPw).'">
 <input type="hidden" name="kal_F1" value="'.$aW[1].'">
 <div class="kalTabl">
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxNutzerNr).'</div>
   <div class="kalTbSp2">'.($sId!=''?sprintf('%04d ',$sId):'').'<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$t.'.gif" title="'.fKalTx($s.KAL_TxAktiv).'">'.($aW[1]=='1'?'':' <span class="kalMini">('.fKalTx($s.KAL_TxAktiv).')</span>').'</div>
  </div>
  <div class="kalTbZl2">
   <div class="kalTbSp1">'.fKalTx(KAL_TxBenutzername).'*<div class="kalNorm"><span class="kalMini">'.fKalTx(KAL_TxNutzerRegel).'</span></div></div>
   <div class="kalTbSp2"><div class="kal'.(isset($aFehl[2])&&$aFehl[2]?'Fhlt':'Eing').'"><input class="kalEing" type="text" name="kal_F2" value="'.fKalTx($aW[2]).'" maxlength="25"></div></div>
  </div>
  <div class="kalTbZl1">
   <div class="kalTbSp1">'.fKalTx(KAL_TxPasswort).'*<div class="kalNorm"><span class="kalMini">'.fKalTx(KAL_TxPassRegel).'</span></div></div>
   <div class="kalTbSp2"><div class="kal'.(isset($aFehl[3])&&$aFehl[3]?'Fhlt':'Eing').'"><input class="kalEing" type="password" name="kal_F3" value="'.fKalTx($aW[3]).'" maxlength="16"></div></div>
  </div>
  <div class="kalTbZl2">
   <div class="kalTbSp1">'.fKalTx(KAL_TxMailAdresse).'*</div>
   <div class="kalTbSp2"><div class="kal'.(isset($aFehl[4])&&$aFehl[4]?'Fhlt':'Eing').'"><input class="kalEing" type="text" name="kal_F4" value="'.fKalTx($aW[4]).'" maxlength="100"></div></div>
  </div>';
 for($i=5;$i<$nFelder;$i++){ if(--$nFarb<=0) $nFarb=2;
  $X.='
  <div class="kalTbZl'.$nFarb.'">
   <div class="kalTbSp1">'.fKalTx($kal_NutzerFelder[$i]).($aNPfl[$i]?'*':'').'</div>
   <div class="kalTbSp2"><div class="kal'.(isset($aFehl[$i])&&$aFehl[$i]?'Fhlt':'Eing').'"><input class="kalEing" type="text" name="kal_F'.$i.'" value="'.fKalTx($aW[$i]).'" maxlength="255"></div></div>
  </div>';
 }
 if(KAL_NutzerDSE1||KAL_NutzerDSE2) if(--$nFarb<=0) $nFarb=2;
 if(KAL_NutzerDSE1) $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE1?'Fhlt':'Eing').'">'.fKalDSEFld(1,$bDSE1).'</div></div></div>';
 if(KAL_NutzerDSE2) $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE2?'Fhlt':'Eing').'">'.fKalDSEFld(2,$bDSE2).'</div></div></div>';
 $X.="\n".' <div class="kalTbZl'.(3-$nFarb).'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2 kalTbSpR">* <span class="kalMini">'.fKalTx(KAL_TxPflicht).'</span></div></div>';
 $X.="\n </div>\n";
 if(!$bOK) $X.=' <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxAnmelden).'" title="'.fKalTx(KAL_TxAnmelden).'"></div>'."\n";
 $X.=' </form>';
 }

 return $X;
}

function fKalPflichtFelder($aV,$nFelder){
 global $kal_NutzerPflicht;
 $aFe=array(); $aNPfl=$kal_NutzerPflicht; array_splice($aNPfl,1,1);
 if(strlen($aV[2])<4||strlen($aV[2])>25) $aFe[2]=true; //Benutzer
 if(strlen($aV[3])<4||strlen($aV[3])>16) $aFe[3]=true; //Passwort
 if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($aV[4]))) $aFe[4]=true; //eMail
 for($j=5;$j<$nFelder;$j++) if($aNPfl[$j]==1&&empty($aV[$j])) $aFe[$j]=true;
 return $aFe;
}
function fKalInCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fKalDtCode($w){
 if(KAL_SZeichenstz==0) return $w; elseif(KAL_SZeichenstz==2) return iconv('ISO-8859-1','UTF-8',$w); else return htmlentities($w,ENT_COMPAT,'ISO-8859-1');
}
?>