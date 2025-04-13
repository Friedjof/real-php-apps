<?php
function fMpTestLogin(){ //Login testen und Session liefern
 $Ret=false;
 if(isset($_POST['mp_Schritt'])&&$_POST['mp_Schritt']=='login'){

  $DbO=NULL; //SQL-Verbindung oeffnen
  if(MP_SQL){
   $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
   if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
  }

  $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNutzerFelder=count($aNF); $nHabenPos=array_search('HABEN',$aNF); $nWarnenPos=array_search('WARNEN',$aNF);
  for($i=2;$i<4;$i++) if(isset($_POST['mp_F'.$i])){ //2 Eingabefelder
   $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\n",'',str_replace("\r",'',trim($_POST['mp_F'.$i]))))));
   $aW[$i]=(MP_Zeichensatz==0?$s:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else $aW[$i]='';
  if(($sNam=$aW[2])&&($sPw=$aW[3])){ //Logindaten
   $s=fMpInCode($sPw); $sSes='';
   if(!MP_SQL){ //Textdateien
    if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sEml=fMpInCode($sNam); $sNam=fMpInCode(strtolower($sNam));
    for($i=1;$i<$nSaetze;$i++){
     $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
     if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
      $aW=$a; $aW[2]=fMpExCode($a[2]); $aW[3]=$sPw; $aW[4]=fMpExCode($a[4]);
      $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
      if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
       $sSes=sprintf('%04d%08d',$a[0],(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
       $s=rtrim($aD[$i]); $aD[$i]=$a[0].';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
       if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);}
       else $sSes='';
      }
      break;
    }}
   }elseif($DbO){ //bei SQL
    if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE passwort="'.$s.'" AND(benutzer="'.strtolower($sNam).'" OR email="'.$sNam.'")')){
     $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
     if($i==1){ //gefunden
      array_splice($a,1,1);
      //if(MP_ZeichnsNorm>0) for($i=2;$i<5;$i++) if(MP_ZeichnsNorm==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
      $aW=$a; $aW[3]=$sPw;
      $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
      if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
       $sSes=sprintf('%04d%08d',$a[0],(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
       if(!$DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session='.substr($sSes,4).' WHERE nr="'.$a[0].'"')) $sSes='';
     }}
    }
   }//SQL
   if($sSes!=''){$Ret=$sSes; define('MP_NeuSession',$sSes);}
 }}
 return $Ret;
}

function fMpLogSeite(){
 $aNF=explode(';',MP_NutzerFelder); array_splice($aNF,1,1); $nNutzerFelder=count($aNF); $nHabenPos=array_search('HABEN',$aNF); $nWarnenPos=array_search('WARNEN',$aNF);
 $aNutzPflicht=explode(';',MP_NutzerPflicht); array_splice($aNutzPflicht,1,1);
 $sForm='LoginForm'; $sAktion='login'; $sBtn=MP_TxAnmelden; $sNaechst=''; $sPw='';
 $X=''; $Meld=''; $MTyp='Fehl'; $bSes=false; $sSes=''; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false; $sLsch=''; $sLschOK=''; $bLschOK=false;
 $aW=array('0','0','','',''); $aFehl=array(); $bOK=false; $sId='';
 $bCaptcha=false; $sCapTyp=(isset($_POST['mp_CaptchaTyp'])?$_POST['mp_CaptchaTyp']:MP_CaptchaTyp); $bCapOk=false; $bCapErr=false;
 if(isset($_GET['mp_Seite'])) $sMpSeite=sprintf('%d',$_GET['mp_Seite']); elseif(isset($_POST['mp_Seite'])) $sMpSeite=sprintf('%d',$_POST['mp_Seite']); else $sMpSeite='';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if($bCaptcha=MP_Captcha||MP_LoginCaptcha){ //Captcha erzeugen
   require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
   if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(MP_CaptchaTxFarb,MP_CaptchaHgFarb);
  }
  if(isset($_GET['mp_Session'])){ //abmelden
   $sSes=fMpRq1($_GET['mp_Session']);
   if(!MP_SQL){ //Textdateien
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=(int)substr($sSes,0,4).';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$sZ=substr(rtrim($aD[$i]),$p); break;}
    if(isset($sZ)&&(substr($sSes,4)==substr($sZ,0,8))){ //Benutzer und Session gefunden
     $aD[$i]=$s.'~'.(time()>>6).substr($sZ,8)."\n";
     if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Meld=MP_TxNutzerLogout; $MTyp='Erfo';
     }else $Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte);
    }
   }elseif($DbO){ //SQL
    if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session="~'.(time()>>6).'" WHERE nr="'.substr($sSes,0,4).'" AND session="'.substr($sSes,4).'"')){
     if($DbO->affected_rows>0){$Meld=MP_TxNutzerLogout; $MTyp='Erfo';}
    }else $Meld=MP_TxSqlFrage;
   }//SQL
  }//abmelden
 }else{ //POST Formularauswertung
  $sSes=MP_Session; $sSchritt=fMpRq1($_POST['mp_Schritt']);
  for($i=1;$i<$nNutzerFelder;$i++) if(isset($_POST['mp_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\n",'',str_replace("\r",'',trim($_POST['mp_F'.$i]))))));
   if($i==$nWarnenPos) $s=sprintf('%0d',max(min((int)$s,99),0));
   $aW[$i]=(MP_Zeichensatz==0?$s:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else{$aW[$i]=''; if($i==$nWarnenPos) $aW[$i]=MP_WarnFristNeu;}

  //Loginversuch auswerten
  if($sSchritt=='login'){
   if($bCaptcha=MP_Captcha||MP_LoginCaptcha){ //Captcha behandeln
    require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
    if(isset($_POST['mp_CaptchaCode'])){
     $sCap=$_POST['mp_CaptchaFrage']; $sCap=(MP_Zeichensatz<=0?$sCap:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
     $Cap->Test($_POST['mp_CaptchaAntwort'],$_POST['mp_CaptchaCode'],$sCap);
    }
   }
   if(($sNam=$aW[2])&&($sPw=$aW[3])){
    $s=fMpInCode($sPw);
    if(!MP_SQL){ //Textdateien
     if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sEml=fMpInCode($sNam); $sNam=fMpInCode(strtolower($sNam));
     for($i=1;$i<$nSaetze;$i++){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
      if($a[3]==$s&&($a[2]==$sNam||$a[4]==$sEml)){ //gefunden
       $sId=$a[0]; $aW=$a; $aW[2]=fMpExCode($a[2]); $aW[3]=$sPw; $aW[4]=fMpExCode($a[4]);
       for($j=5;$j<$nNutzerFelder;$j++){
        $aW[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
        //if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $aW[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$aW[$j]); else $aW[$j]=html_entity_decode($aW[$j]);
       }
       $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
       if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
        if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
        else{
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
         $s=rtrim($aD[$i]); $aD[$i]=$sId.';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
         if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);}
         else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sSes='';}
        }
       }
       break;
     }}
    }elseif($DbO){ //bei SQL
     if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE passwort="'.$s.'" AND(benutzer="'.strtolower($sNam).'" OR email="'.$sNam.'")')){
      $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
      if($i==1){ //gefunden
       $sId=$a[0]; array_splice($a,1,1);
       //if(MP_ZeichnsNorm>0) for($i=2;$i<$nNutzerFelder;$i++) if(MP_ZeichnsNorm==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
       $aW=$a; $aW[3]=$sPw;
       $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
       if($a[1]=='1'&&count($aFehl)==0){ //aktiv, Session erzeugen
        if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
        else{
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
         if(!$DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session='.substr($sSes,4).' WHERE nr="'.$sId.'"')){$Meld=MP_TxSqlAendr; $sSes='';}
        }
       }
      }
     }else $Meld=MP_TxSqlFrage;
    }//SQL
    if($sId!=''){ //gefunden
     if(empty($sSes)||isset($_POST['mp_Daten'])&&$_POST['mp_Daten']){ //Daten editieren
      $Meld=MP_TxNutzerPruefe; $MTyp='Meld'; $sForm='NutzerForm'; $sNaechst='pruefen';
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;}
     }else{ //sofort zum Markt
      if(!defined('MP_NeuSession')) define('MP_NeuSession',$sSes); $sForm='##';
      if(MP_Segment){
       include(MP_Pfad.'mpDaten.php'); fMpDaten(true,true); include(MP_Pfad.'mpListe.php');
      }else include(MP_Pfad.'mpIndex.php');
      $X=fMpSeite();
     }
    }else $Meld=MP_TxNutzerFalsch;
   }else $Meld=MP_TxNutzerNamePass;

  //Benutzerdaten pruefen/aendern
  }elseif($sSchritt=='pruefen'){
   if(($sId=fMpRq1($_POST['mp_Id']))&&($sPw=fMpRq($_POST['mp_Pw']))){
    if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $sPw=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sPw); else $sPw=html_entity_decode($sPw);
    $aW[2]=strtolower($aW[2]); $s=fMpInCode($sPw);
    $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
    if(MP_TxAgbFeld>''&&isset($_POST['mp_F3'])&&$_POST['mp_F3']>'')
     if(isset($_POST['mp_Agb'])&&$_POST['mp_Agb']=='1') $bAgb=true; else $aFehl['Agb']=true;
    if(MP_NutzerDSE1) if(isset($_POST['mp_DSE1'])&&$_POST['mp_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(MP_NutzerDSE2) if(isset($_POST['mp_DSE2'])&&$_POST['mp_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(MP_NutzerLoeschen>0) if($sLsch=(isset($_POST['mp_Lsch'])?$_POST['mp_Lsch']:'')) $sLschOK=(isset($_POST['mp_LschOK'])?$_POST['mp_LschOK']:'');
    if(count($aFehl)==0){
     if(!MP_SQL){ //Textdateien
      if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sNam='#;'; $bGefunden=false; $sOldSes='0';
      for($i=1;$i<$nSaetze;$i++){
       $aN=explode(';',rtrim($aD[$i]));
       if($aN[0]!=$sId||$aN[4]!=$s) $sNam.=$aN[3].';';
       else{$sOldSes=$aN[1]; array_splice($aN,1,1); $a=$aN; $k=$i; $bGefunden=true;}
      }
      if($bGefunden){ //gefunden
       $sForm='NutzerForm'; $sNaechst='pruefen';
       $aW[0]=$sId; $aW[1]=$a[1]; $a[2]=fMpExCode($a[2]); $a[3]=fMpExCode($a[3]); $a[4]=fMpExCode($a[4]);
       for($j=5;$j<$nNutzerFelder;$j++){
        $a[$j]=(isset($a[$j])?str_replace('`,',';',$a[$j]):''); if($aNF[$j]=='HABEN') $aW[$j]=(isset($a[$j])?$a[$j]:'');
        //if(MP_Zeichensatz>0) if(MP_Zeichensatz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
       }
       if($sLsch){ //loeschen
        if(MP_NutzerLoeschen==1){ // sofort loeschen vormerken
         $aW[1]='0'; $sSes=sprintf('%04d%08d',$sId,(time()-1)>>6); //inaktiv, abgelaufene Session erzeugen, 0 Minuten
         $s=$sId.';'.substr($sSes,4).';0;'.fMpInCode($aW[2]).';'.fMpInCode($aW[3]).';'.fMpInCode($aW[4]);
         for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
         if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=MP_TxNutzerLschMeld; $MTyp='Erfo'; $bOK=true; $sPw=$aW[3]; $bLschOK=true; $sLschOK=$sId;
         }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sSes='';}
        }elseif(MP_NutzerLoeschen==2&&$sLschOK==$sId){ // Direktloeschen
         if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
          $aD[$k]=''; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=MP_TxNutzerLschMeld; $MTyp='Erfo'; $bOK=true; $aW[1]='0'; $sPw='?'; $sSes=''; $bLschOK=true; $sLschOK=$sId;
          // ToDo: Inserate und Bilder loeschen
         }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sSes='';}
        }else{$Meld=MP_TxBenutzer.' <i>'.$aW[2].'</i> '.MP_TxLoeschen.'?'; $MTyp='Fehl'; $sLschOK=$sId; $sForm='NutzerForm'; $sNaechst='pruefen';} // nachfragen
       }elseif($a!=$aW){ //veraendert
        if($a[2]==$aW[2]||!strpos($sNam,';'.fMpInCode($aW[2]).';')){ //Benutzername unveraendert oder frei
         if($a[1]=='1') $sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); //aktiv, Session erzeugen, ca. 40 Minuten
         $s=$sId.';'.($sSes?substr($sSes,4):$sOldSes).';'.$a[1].';'.fMpInCode($aW[2]).';'.fMpInCode($aW[3]).';'.fMpInCode($aW[4]);
         for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
         if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=MP_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
         }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sSes='';}
        }else{$Meld=MP_TxNutzerVergeben; $aFehl[2]=true;}
       }elseif($sSes==''){ //Daten fertig
        if($a[1]=='1'){ //aktiv, Session erzeugen
         $sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
         $s=rtrim($aD[$k]); $aD[$k]=$sId.';'.substr($sSes,4).substr($s,strpos($s,';',strpos($s,';')+1))."\n";
         if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
          $Meld=MP_TxKeineAenderung; $MTyp='Meld';
         }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sSes='';}
        }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
       }else{$Meld=MP_TxNutzerOK; $MTyp='Erfo'; $bOK=true;} //Session OK
      }else $Meld=MP_TxNutzerFalsch;
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'" AND passwort="'.$s.'"')){
       $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
       if($i==1){ //gefunden
        $sForm='NutzerForm'; $sNaechst='pruefen';
        array_splice($a,1,1); $aW[0]=$sId; $aW[1]=$a[1]; $a[3]=fMpExCode($a[3]); $s='';
        //if(MP_ZeichnsNorm>0) for($j=2;$j<$nNutzerFelder;$j++) if($j!=3) if(MP_ZeichnsNorm==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
        if($a[2]!=$aW[2]) $s.=', benutzer="'.$aW[2].'"';
        if($a[3]!=$aW[3]) $s.=', passwort="'.fMpInCode($aW[3]).'"';
        if($a[4]!=$aW[4]) $s.=', email="'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){
         if($aNF[$j]=='HABEN') $aW[$j]=(isset($a[$j])?$a[$j]:'');
         if((!isset($a[$j]))||$a[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.$aW[$j].'"';
        }
        if($sLsch){ //loeschen
         if(MP_NutzerLoeschen==1){ // vormerken
          $aW[1]='0'; $sSes=sprintf('%04d%08d',$sId,(time()-1)>>6); //inaktiv, abgelaufene Session erzeugen, 0 Minuten
          if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET aktiv="0",session="'.substr($sSes,4).'" WHERE nr="'.sprintf('%0d',$sId).'"')){
           $Meld=MP_TxNutzerLschMeld; $MTyp='Erfo'; $bOK=true; $sPw=$aW[3]; $bLschOK=true; $sLschOK=$sId;
          }else{$Meld=MP_TxSqlAendr; $sSes='';}
         }elseif(MP_NutzerLoeschen==2&&$sLschOK==$sId){ // Direktloeschen
          if($DbO->query('DELETE FROM '.MP_SqlTabN.' WHERE nr="'.sprintf('%0d',$sId).'" LIMIT 1')){
           $Meld=MP_TxNutzerLschMeld; $MTyp='Erfo'; $bOK=true; $sPw='?'; $sSes=''; $aW[1]='0'; $bLschOK=true; $sLschOK=$sId;
          }else{$Meld=MP_TxSqlAendr; $sSes='';}
         }else{$Meld=MP_TxBenutzer.' <i>'.$aW[2].'</i> '.MP_TxLoeschen.'?'; $MTyp='Fehl'; $sLschOK=$sId; $sForm='NutzerForm'; $sNaechst='pruefen';} // nachfragen
        }elseif($s!=''){ //veraendert
         if($a[2]!=$aW[2]){ //Benutzname
          if($rR=$DbO->query('SELECT nr FROM '.MP_SqlTabN.' WHERE benutzer="'.$aW[2].'"')){
           $i=$rR->num_rows; $rR->close();
          }else $i=1;
         }else $i=0;
         if($i==0){ //Benutzername unveraendert oder frei
          if($a[1]=='1'){$sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); $s.=', session="'.substr($sSes,4).'"';} //aktiv, Session erzeugen ca. 40 Minuten
          if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET '.substr($s,2).' WHERE nr="'.$sId.'"')){
           $Meld=MP_TxNutzerGeaendert; $MTyp='Erfo'; $sPw=$aW[3];
          }else{$Meld=MP_TxSqlAendr; $sSes='';}
         }else{$Meld=MP_TxNutzerVergeben; $aFehl[2]=true;}
        }elseif($sSes==''){ //Daten fertig
         if($a[1]=='1'){ //aktiv, Session erzeugen
          $sSes=sprintf('%04d%08d',$sId,(time()+(60*MP_SessionZeit))>>6); //ca. 40 Minuten
          if($DbO->query('UPDATE IGNORE '.MP_SqlTabN.' SET session="'.substr($sSes,4).'" WHERE nr="'.$sId.'"')){
           $Meld=MP_TxKeineAenderung; $MTyp='Meld';
          }else{$Meld=MP_TxSqlAendr; $sSes='';}
         }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
        }else{$Meld=MP_TxNutzerOK; $MTyp='Erfo'; $bOK=true;} //Session OK
       }else $Meld=MP_TxNutzerFalsch;
      }else $Meld=MP_TxSqlFrage;
     }//SQL
    }else{$Meld=MP_TxEingabeFehl; $sForm='NutzerForm'; $sNaechst='pruefen';}
   }else $Meld=MP_TxNutzerFalsch;
   if($bLschOK){ // Nacharbeit Nutzer loeschen
    if($sSubj=(MP_NutzerLoeschen>1?MP_TxNutzerLschBtrN:MP_TxNutzerLschBtrA)){ // E-Mails
     require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_NutzerLoeschen>1?$aW[4]:MP_MailTo);
     if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
     $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $sWww=fMpWww();
     $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#U',$aW[2],str_replace('#E',$aW[4],str_replace('#N',$sId,str_replace('#A',$sWww,$sSubj)))); $Mailer->SetReplyTo($aW[4]);
     if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
     $Mailer->Text=str_replace('#U',$aW[2],str_replace('#E',$aW[4],str_replace('#N',$sId,str_replace('#A',$sWww,str_replace('\n ',"\n",(MP_NutzerLoeschen>1?MP_TxNutzerLschTxtN:MP_TxNutzerLschTxtA))))));
     $Mailer->Send(); if(MP_NutzerLoeschen>1){$Mailer->ClearTo(); $Mailer->AddTo(MP_MailTo); $Mailer->Send();}
    }
    $aSeg=array(); $aSeg=explode(';',MP_Segmente); $nSegmente=count($aSeg); // Inserate loeschen
    if(MP_NutzerLoeschen>1) for($nSegNo=1;$nSegNo<$nSegmente;$nSegNo++) if(($sSegNam=$aSeg[$nSegNo])&&($sSegNam!='LEER')){ //ueber alle Segmente
     $sSegNo=sprintf('%02d',$nSegNo); $aLsch=array(); $sSegMeld='';
     if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
     $nFelder=0; $aStru=array(); $aMpFN=array(); $aMpFT=array(); $nUserPos=0; $nKontaktPos=0; //Struktur holen
     if(!MP_SQL){ //Text
      $aStru=(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)?file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur):array());
     }elseif($DbO){ //SQL
      if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSegNo.'"')){
       $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
     }}
     if(count($aStru)>1){
      $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
      if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
      $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
     }
     if($nUserPos=array_search('u',$aMpFT)){ //mit Benutzerfeld
      if(!MP_SQL){ //Inserate
       $aT=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aT);
       for($i=1;$i<$nSaetze;$i++){
        $a=explode(';',rtrim($aT[$i])); array_splice($a,1,1); $nUser=(isset($a[$nUserPos])?sprintf('%0d',$a[$nUserPos]):'0');
        if($nUser==$sId){$aLsch[$a[0].$sBldSeg]=true; $aT[$i]='';}
       }
       if($n=count($aLsch)){
        if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)&&is_writable(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)){
         if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){ //Inserate neu schreiben
          fwrite($f,rtrim(str_replace("\r",'',implode('',$aT)))."\n"); fclose($f);
       }}}
      }elseif($DbO){ //bei SQL
       if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE CAST(mp_'.$nUserPos.' AS INTEGER)='.$sId)){
        while($a=$rR->fetch_row()) $aLsch[$a[0].$sBldSeg]=true; $rR->close();
        if($n=count($aLsch)) $DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE CAST(mp_'.$nUserPos.' AS INTEGER)='.$sId);
       }
      }
     }//nUserPos>0
     if(($n=count($aLsch))&&(in_array('b',$aMpFT)||in_array('f',$aMpFT))){ // Bilder und Dateien
      if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){
       $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])) $aD[]=$s; closedir($f);
       if($n=count($aD)) $sSegMeld.="\n  $n Bilder und Dateien zu abgelaufenen Inseraten geloescht";
       foreach($aD as $s) if(file_exists(MP_Pfad.MP_Bilder.$s)) unlink(MP_Pfad.MP_Bilder.$s);
     }}
    }//alle Segmente
   }//bLschOK
  //neuer Benutzer
  }elseif($sSchritt=='neu'||$sSchritt=='erfassen'){
   if($bCaptcha=(MP_Captcha||MP_LoginCaptcha)&&$sSchritt=='neu'){ //Captcha behandeln
    require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
    $sCap=$_POST['mp_CaptchaFrage']; $sCap=(MP_Zeichensatz<=0?$sCap:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
    if($Cap->Test($_POST['mp_CaptchaAntwort'],$_POST['mp_CaptchaCode'],$sCap)){
     $bCapOk=true; $Cap->Delete(); $bCaptcha=false;
    }else{$bCapErr=true; $aFehl[0]=true; $sNeuName=$aW[2]; $aW[2]='';}
   }else $bCapOk=true;
   if($bCapOk){
    $sForm='NutzerForm'; $sNaechst='erfassen'; $aW[2]=strtolower($aW[2]); srand((double)microtime()*1000000); $sCod=rand(1000,9999);
    for($j=5;$j<$nNutzerFelder;$j++) if($aNF[$j]=='HABEN') $aW[$j]=MP_GuthabenNeu; elseif($aNF[$j]=='WARNEN') $aW[$j]=sprintf('%0d',max(min((int)$aW[$j],99),0));
    $aFehl=fMpPflichtFehler($aW,$nNutzerFelder,$nHabenPos,$nWarnenPos);
    if(MP_TxAgbFeld>''&&isset($_POST['mp_F3'])&&$_POST['mp_F3']>'')
     if(isset($_POST['mp_Agb'])&&$_POST['mp_Agb']=='1') $bAgb=true; else $aFehl['Agb']=true;
    if($sSchritt=='erfassen'){
     if(MP_NutzerDSE1) if(isset($_POST['mp_DSE1'])&&$_POST['mp_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
     if(MP_NutzerDSE2) if(isset($_POST['mp_DSE2'])&&$_POST['mp_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    }
    if(count($aFehl)==0){
     if(!MP_SQL){ //Textdateien
      if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sNam='#;';
      for($i=1;$i<$nSaetze;$i++){$a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $sNam.=$a[2].';';}
      if(!strpos($sNam,';'.fMpInCode($aW[2]).';')){
       $s=rtrim($aD[0]);
       if(substr($s,0,7)=='Nummer_'){ //neue Nummer
        $i=strpos($s,';'); $sId=1+substr($s,7,$i-7); $aD[0]=substr_replace($s,$sId,7,$i-7)."\n";
       }else{ //Kopfzeile neu aufbauen
        $sId=0; for($i=1;$i<$nSaetze;$i++) $sId=max($sId,(int)substr($aD[$i],0,5));
        $s='Nummer_'.(++$sId).';Session;aktiv'; for($j=2;$j<$nNutzerFelder;$j++) $s.=';'.$aNF[$j]; $aD[0]=$s."\n";
       }
       $s=$sId.';'.$sCod.';0;'.fMpInCode($aW[2]).';'.fMpInCode($aW[3]).';'.fMpInCode($aW[4]);
       for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]);
       if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)&&is_writable(MP_Pfad.MP_Daten.MP_Nutzer)&&($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w'))){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".$s."\n"); fclose($f);
        $Meld=MP_TxNutzerNeu; $MTyp='Erfo'; $bOK=true;
       }else{$Meld=str_replace('#',MP_TxBenutzer,MP_TxDateiRechte); $sId='';}
      }else{$Meld=MP_TxNutzerVergeben; $aFehl[2]=true;}
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT nr FROM '.MP_SqlTabN.' WHERE benutzer="'.$aW[2].'"')){
       $i=$rR->num_rows; $rR->close();
       if($i==0){
        $s=',benutzer,passwort,email'; $t=',"'.$aW[2].'","'.fMpInCode($aW[3]).'","'.$aW[4].'"';
        for($j=5;$j<$nNutzerFelder;$j++){$s.=',dat_'.$j; $t.=',"'.$aW[$j].'"';}
        if($DbO->query('INSERT IGNORE INTO '.MP_SqlTabN.' (session,aktiv'.$s.') VALUES("'.$sCod.'","0"'.$t.')')){
         if($sId=$DbO->insert_id){
          $Meld=MP_TxNutzerNeu; $MTyp='Erfo'; $bOK=true;
         }else $Meld=MP_TxSqlEinfg;
        }else $Meld=MP_TxSqlEinfg;
       }else{$Meld=MP_TxNutzerVergeben; $aFehl[2]=true;}
      }else $Meld=MP_TxSqlFrage;
     }//SQL
     if($sId!=''){
      $sMlTx=''; for($j=2;$j<$nNutzerFelder;$j++) $sMlTx.="\n".strtoupper(str_replace('`,',';',$aNF[$j])).': '.$aW[$j]; $sWww=fMpWww();
      if(MP_NutzerNeuMail){
       $sLnk=MP_Url.'marktplatz.php?mp_Aktion=ok'.$sCod.$sId;
       require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($aW[4]);
       if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
       $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,MP_TxNutzerNeuBtr); $Mailer->SetReplyTo($aW[4]);
       if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#L',$sLnk,str_replace('#A',$sWww,str_replace('\n ',"\n",MP_TxNutzerNeuTxt))));
       $Mailer->Send();
      }
      if(MP_NutzerNeuAdmMail&&!MP_FreischaltAdmin){
       require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailTo);
       if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
       $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#N',sprintf('%04d',$sId),MP_TxNutzNeuAdmBtr);
       if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
       $Mailer->Text=str_replace('#D',$sMlTx,str_replace('#N',$sId,str_replace('\n ',"\n",MP_TxNutzNeuAdmTxt)));
       $Mailer->Send();
      }
     }
    }else $Meld=MP_TxEingabeFehl;
   }else $Meld=MP_TxCaptchaFehl;

  //Passwort vergessen
  }elseif($sSchritt=='senden'){
   if($bCaptcha=MP_Captcha||MP_LoginCaptcha){ //Captcha behandeln
    require_once(MP_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(MP_Pfad.MP_CaptchaPfad,MP_CaptchaSpeicher);
    $sCap=$_POST['mp_CaptchaFrage']; $sCap=(MP_Zeichensatz<=0?$sCap:(MP_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
    if($Cap->Test($_POST['mp_CaptchaAntwort'],$_POST['mp_CaptchaCode'],$sCap)) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
   }else $bCapOk=true;
   if($bCapOk){
    if($sNam=$aW[2]){
     if(!MP_SQL){ //Textdateien
      if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sEml=fMpInCode($sNam); $sNam=fMpInCode(strtolower($sNam));
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
       if($a[2]==$sNam||$a[4]==$sEml){$sId=$a[0]; $sNam=fMpExCode($a[2]); $sPass=fMpExCode($a[3]); $sEml=fMpExCode($a[4]); break;}
      }
     }elseif($DbO){ //bei SQL
      if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE benutzer="'.strtolower($sNam).'" OR email="'.$sNam.'"')){
       if($a=$rR->fetch_row()){ //gefunden
        array_splice($a,1,1); $sId=$a[0];
        //if(MP_ZeichnsNorm>0) for($j=2;$j<5;$j++) if($j!=3) if(MP_ZeichnsNorm==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
        $sNam=$a[2]; $sPass=fMpExCode($a[3]); $sEml=$a[4];
       }
       $rR->close();
      }else $Meld=MP_TxSqlFrage;
     }//SQL
     if(isset($sPass)){
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sEml); $sWww=fMpWww();
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,MP_TxNutzerDatBtr); $Mailer->SetReplyTo($sEml);
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->Text=str_replace('#P',$sPass,str_replace('#B',$sNam,str_replace('#N',sprintf('%04d',$sId),str_replace('#A',$sWww,str_replace('\n ',"\n",MP_TxNutzerDaten)))));
      if($Mailer->Send()){
       $Meld=MP_TxNutzerSend; $MTyp='Erfo'; $bOK=true;
       if($bCaptcha){$Cap->Delete(); $bCapErr=false; $bCapOk=false; if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(MP_CaptchaTxFarb,MP_CaptchaHgFarb);} //Captcha loeschen und neu
      }else $Meld=MP_TxSendeFehl;
     }else $Meld=MP_TxNutzerFalsch;
    }else $Meld=MP_TxNutzerNameMail;
   }else $Meld=MP_TxCaptchaFehl;
  }

 }//POST

 //Beginn der Ausgabe
 if($Meld==''){$Meld=MP_TxNutzerLogin; $MTyp='Meld';}
 if(empty($X)) $X="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';

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

 if($sForm=='LoginForm'){ //Loginformulare
 //Loginmaske
 $X.='
 <p class="mpMeld" style="margin-top:20px;">'.fMpTx(MP_TxLoginLogin).'</p>
 <form class="mpLogi" action="'.fMpHref('login',$sMpSeite).'" method="post">
 <input type="hidden" name="mp_Aktion" value="login" />
 <input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />
 <input type="hidden" name="mp_Schritt" value="login" />'.rtrim("\n ".MP_Hidden).($sMpSeite?"\n ".'<input type="hidden" name="mp_Seite" value="'.$sMpSeite.'" />':'').'
 <div class="mpTabl">
  <div class="mpTbZl1">
   <div class="mpTbSpi mpNoBr">'.fMpTx(MP_TxBenutzername).'<br />'.fMpTx(MP_TxOder).'<br />'.fMpTx(MP_TxMailAdresse).'</div>
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
   <div class="mpTbSpi mpNoBr">'.fMpTx(MP_TxGewuenscht).'<div>'.fMpTx(MP_TxBenutzername).'</div><span class="mpMini">'.fMpTx(MP_TxNutzerRegel).'</span></div>
   <div class="mpTbSp2"><input class="mpEing" type="text" name="mp_F2" value="'.(isset($sNeuName)?fMpTx($sNeuName):'').'" maxlength="25" /></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxCaptchaFeld).'*</div>
   <div class="mpTbSp2">
    <div class="mpNorm"><span class="capQry">'.fMpTx($Cap->Type!='G'?$Cap->Question:MP_TxCaptchaHilfe).'</span></div>
    <div class="mpNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.MP_Url.MP_CaptchaPfad.$Cap->Question.'" />').'</span></div>
    <div class="mpNorm">
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
 </form>'."\n";
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
   <div class="mpTbSpi mpNoBr">'.fMpTx(MP_TxBenutzername).'<br />'.fMpTx(MP_TxOder).'<br />'.fMpTx(MP_TxMailAdresse).'</div>
   <div class="mpTbSp2"><input class="mpEing" type="text" name="mp_F2" value="'.fMpTx($aW[2]).'" maxlength="100" /></div>
  </div>';
 if($bCaptcha){ //Captcha-Zeile
  $X.="\n".' <div class="mpTbZl1">
   <div class="mpTbSpi">'.fMpTx(MP_TxCaptchaFeld).'*</div>
   <div class="mpTbSp2">
    <div class="mpNorm"><span class="capQry">'.fMpTx($Cap->Type!='G'?$Cap->Question:MP_TxCaptchaHilfe).'</span></div>
    <div class="mpNorm"><span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.MP_Url.MP_CaptchaPfad.$Cap->Question.'" />').'</span></div>
    <div class="mpNorm">
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
 if(!$bOK) $X.=' <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxSenden).'" title="'.fMpTx(MP_TxSenden).'" /></div>'."\n"; else $X.='&nbsp;';
 $X.=' </form>';
 }

 }elseif($sForm=='NutzerForm'){ //Benutzerdaten

 if(MP_DSEPopUp&&(MP_NutzerDSE1||MP_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.MP_DSEPopupW.',height='.MP_DSEPopupH.',left='.MP_DSEPopupX.',top='.MP_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 $nFarb=2; if($aW[1]=='1'){$s=''; $t='Grn';}elseif($aW[1]=='0'){$s=MP_TxNicht.' '; $t='Rot';}elseif($aW[1]=='2'){$s=MP_TxMailAdresse.' '.MP_TxAktiv.', '.MP_TxBenutzer.' '.MP_TxNicht.' '; $t='RtGn';}else{$s=MP_TxNicht.' '; $t='Rot';}
 $X.='
 <form class="mpLogi" name="mpLogi" action="'.fMpHref('login',$sMpSeite).'" method="post">'.rtrim("\n ".MP_Hidden).($sMpSeite?"\n ".'<input type="hidden" name="mp_Seite" value="'.$sMpSeite.'" />':'').'
 <input type="hidden" name="mp_Aktion" value="login" />
 <input type="hidden" name="mp_Segment" value="'.MP_Segment.'" />
 <input type="hidden" name="mp_Schritt" value="'.$sNaechst.'" />
 <input type="hidden" name="mp_Session" value="'.$sSes.'" />
 <input type="hidden" name="mp_Id" value="'.$sId.'" />
 <input type="hidden" name="mp_Pw" value="'.fMpTx($sPw).'" />
 <input type="hidden" name="mp_F1" value="'.$aW[1].'" />
 <div class="mpTabl">
  <div class="mpTbZl1">
   <div class="mpTbSp1">'.fMpTx(MP_TxNutzerNr).'</div>
   <div class="mpTbSp2">'.($sId!=''?sprintf('%04d ',$sId):'').'<img src="'.MP_Url.'grafik/punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fMpTx($s.MP_TxAktiv).'" title="'.fMpTx($s.MP_TxAktiv).'" />'.($aW[1]=='1'?'':' <span class="mpMini">('.fMpTx($s.MP_TxAktiv).')</span>').'</div>
  </div>
  <div class="mpTbZl2">
   <div class="mpTbSp1">'.fMpTx(MP_TxBenutzername).'*<div class="mpNorm"><span class="mpMini">'.fMpTx(MP_TxNutzerRegel).'</span></div></div>
   <div class="mpTbSp2"><div class="mp'.(isset($aFehl[2])&&$aFehl[2]?'Fhlt':'Eing').'"><input class="mpEing" type="text" name="mp_F2" value="'.fMpTx($aW[2]).'" maxlength="25" /></div></div>
  </div>
  <div class="mpTbZl1">
   <div class="mpTbSp1">'.fMpTx(MP_TxPasswort).'*<div class="mpNorm"><span class="mpMini">'.fMpTx(MP_TxPassRegel).'</span></div></div>
   <div class="mpTbSp2"><div class="mp'.(isset($aFehl[3])&&$aFehl[3]?'Fhlt':'Eing').'"><input class="mpEing" type="password" name="mp_F3" value="'.fMpTx($aW[3]).'" maxlength="16" /></div></div>
  </div>
  <div class="mpTbZl2">
   <div class="mpTbSp1">'.fMpTx(MP_TxMailAdresse).'*</div>
   <div class="mpTbSp2"><div class="mp'.(isset($aFehl[4])&&$aFehl[4]?'Fhlt':'Eing').'"><input class="mpEing" type="text" name="mp_F4" value="'.fMpTx($aW[4]).'" maxlength="100" /></div></div>
  </div>';
 for($i=5;$i<$nNutzerFelder;$i++){
  $sNFn=str_replace('`,',';',$aNF[$i]); $sFStyle=''; if(--$nFarb<=0) $nFarb=2;
  if($sNFn=='HABEN'){
   $sNFn=MP_TxGuthaben; $aFehl[$i]=false; $nGh=trim($aW[$i]);
   if(strlen($nGh)<=0||$nGh==sprintf('%d',$nGh)) $sFStyle=' readonly="readonly" style="width:4em;"';
   else{ //Datum
    $nGh=trim(substr($nGh,(int)strpos($nGh,' ')));
    if(substr($nGh,4,1)=='-'&&substr($nGh,7,1)=='-') $nGh=fMpZeigeDatum($nGh);
    $aW[$i]=fMpTx(MP_TxBis).' '.$nGh; $sFStyle=' readonly="readonly" style="width:9em;"';
   }
  }elseif($sNFn=='WARNEN'){
   $sNFn=MP_TxWarnen; $aFehl[$i]=false; $aNutzPflicht[$i]=false; if($aW[$i]<'1') $aW[$i]=''; $sFStyle=' style="width:4em;"';
  }
  $X.='
  <div class="mpTbZl'.$nFarb.'">
   <div class="mpTbSp1">'.fMpTx($sNFn).($aNutzPflicht[$i]?'*':'').'</div>
   <div class="mpTbSp2"><div class="mp'.(isset($aFehl[$i])&&$aFehl[$i]?'Fhlt':'Eing').'"><input class="mpEing" type="text" name="mp_F'.$i.'" value="'.fMpTx($aW[$i]).'" maxlength="255"'.$sFStyle.' />'.($sFStyle?' '.fMpTx($sNFn==MP_TxGuthaben?MP_TxInserate:MP_TxWarnTage):'').'</div></div>
  </div>';
 }
 if(MP_TxAgbFeld>''){
  if(--$nFarb<=0) $nFarb=2; $s=fMpTx(MP_TxAgbText);
  if($n=strrpos($s,']')){
   $s=substr_replace($s,'</a>',$n,1);
   if($n=strpos($s,'[')){
    $s=substr_replace($s,'<a class="mpDetl" href="'.MP_AgbLink.'">',$n,1);
    if(MP_AgbPopup) $s=substr_replace($s,' target="'.(MP_AgbZiel?MP_AgbZiel:'txwin').'" onclick="TxWin(this.href);return false;"',$n+2,0);
    elseif(MP_AgbZiel) $s=substr_replace($s,' target="'.MP_AgbZiel.'"',$n+2,0);
   }
  }
  $X.="\n".' <div class="mpTbZl'.$nFarb.'">
   <div class="mpTbSp1">'.fMpTx(MP_TxAgbFeld).'*</div>
   <div class="mpTbSp2"><div class="mp'.(isset($aFehl['Agb'])&&$aFehl['Agb']?'Fhlt':'Eing').'"><input class="mpCheck" type="checkbox" name="mp_Agb" value="1"'.(isset($bAgb)?' checked="checked"':'').' /> '.$s.'</div></div>
  </div>';
 }

 if(MP_NutzerDSE1||MP_NutzerDSE2) if(--$nFarb<=0) $nFarb=2;
 if(MP_NutzerDSE1) $X.="\n".'<div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE1?'Fhlt':'Eing').'">'.fMpDSEFld(1,$bDSE1).'</div></div></div>';
 if(MP_NutzerDSE2) $X.="\n".'<div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1 mpTbSpR">*</div><div class="mpTbSp2"><div class="mp'.($bErrDSE2?'Fhlt':'Eing').'">'.fMpDSEFld(2,$bDSE2).'</div></div></div>';

 if(MP_NutzerLoeschen>0){ if(--$nFarb<=0) $nFarb=2;
  $X.="\n".'<div class="mpTbZl'.$nFarb.'"><div class="mpTbSp1">'.fMpTx(MP_TxBenutzer.' '.MP_TxLoeschen).'</div><div class="mpTbSp2"><input class="mpCheck" type="checkbox" name="mp_Lsch" value="1"'.($sLsch?' checked="checked"':'').' /><input type="hidden" name="mp_LschOK" value="'.$sLschOK.'" /> <img class="mpIcon" src="'.MP_Url.'grafik/iconLoeschen.gif" border="0" onclick="return document.mpLogi.submit()" title="'.fMpTx(MP_TxBenutzer.' '.MP_TxLoeschen).'" alt=""></div></div>';
 }

 $X.="\n".' <div class="mpTbZl'.(3-$nFarb).'"><div class="mpTbSp1">&nbsp;</div><div class="mpTbSp2 mpTbSpR">* <span class="mpMini">'.fMpTx(MP_TxPflicht).'</span></div></div>';
 $X.="\n </div>\n";
 if(!$bOK) $X.=' <div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxAnmelden).'" title="'.fMpTx(MP_TxAnmelden).'" /></div>'."\n";
 $X.=' </form>';
 }
 if(MP_TxAgbFeld>''&&MP_AgbPopup){
  $X.="\n\n".'<script type="text/javascript">function TxWin(sUrl){txWin=window.open(sUrl,"'.(MP_AgbZiel?MP_AgbZiel:'txwin').'","width='.MP_AgbBreit.',height='.MP_AgbHoch.',left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");txWin.focus();}</script>';
 }
 return $X;
}

function fMpZeigeDatum($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fMpPflichtFehler($aV,$nFelder,$nHbPos=0,$nWrnPos=0){
 $aFe=array(); $aNutzPflicht=explode(';',MP_NutzerPflicht); array_splice($aNutzPflicht,1,1);
 if(strlen($aV[2])<4||strlen($aV[2])>25) $aFe[2]=true; //Benutzer
 if(strlen($aV[3])<4||strlen($aV[3])>16) $aFe[3]=true; //Passwort
 if(!fMpIsEMailAdr($aV[4])) $aFe[4]=true; //eMail
 for($j=5;$j<$nFelder;$j++) if($aNutzPflicht[$j]==1&&empty($aV[$j])&&$j!=$nHbPos&&$j!=$nWrnPos) $aFe[$j]=true;
 return $aFe;
}
function fMpInCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fMpExCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>