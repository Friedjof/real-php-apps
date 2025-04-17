<?php
function fKalSeite(){ //Benutzerdaten
 global $kal_NutzerFelder, $kal_NutzerPflicht;

 $Et=''; $Es='Fehl'; $aW=array(); $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 array_splice($kal_NutzerFelder,1,1); $aNPfl=$kal_NutzerPflicht; array_splice($aNPfl,1,1); $nFelder=count($kal_NutzerFelder);

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 //Session pruefen
 $bSes=false; $sSession=substr(KAL_Session,0,29); $a=array(0,0);
 if($sSes=substr($sSession,17,12)){
  $nId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $bSes=true;
      $a[2]=fKalDeCode($a[2]); $a[3]=fKalDeCode($a[3]); $a[4]=fKalDeCode($a[4]);
      for($j=5;$j<$nFelder;$j++){
       $a[$j]=str_replace('`,',';',$a[$j]);
       if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
      }
     }else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows==1){
      $bSes=true; $a=$rR->fetch_row(); array_splice($a,1,1); $a[3]=fKalDeCode($a[3]);
      if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }
  }else $Et=KAL_TxSessionZeit;
 }else $Et=KAL_TxSessionUngueltig;

 if($_SERVER['REQUEST_METHOD']=='POST'){
  $sId=$a[0]; $aW[0]=$a[0]; $aW[1]=$a[1];
  for($i=2;$i<$nFelder;$i++) if(isset($_POST['kal_F'.$i])){ //Eingabefelder
   $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['kal_F'.$i]))));
   $aW[$i]=(KAL_Zeichensatz==0?$s:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$s):html_entity_decode($s)));
  }else $aW[$i]='';
  $aW[2]=strtolower($aW[2]); $aFehl=fKalPflichtFelder($aW,$nFelder); $sMlDt='';
  if(KAL_NutzerDSE1) if(isset($_POST['kal_DSE1'])&&$_POST['kal_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
  if(KAL_NutzerDSE2) if(isset($_POST['kal_DSE2'])&&$_POST['kal_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
  if(count($aFehl)==0){
   if($a!=$aW){
    if(!KAL_SQL){ //Textdateien
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $sNam='#;'; $bGefunden=false;
     for($i=1;$i<$nSaetze;$i++){
      $aN=explode(';',rtrim($aD[$i]));
      if($aN[0]!=$sId) $sNam.=$aN[3].';'; else{$k=$i; $bGefunden=true;}
     }
     if($bGefunden){ //gefunden
      if($a[2]==$aW[2]||!strpos($sNam,';'.fKalInCode($aW[2]).';')){ //Benutzername unveraendert oder frei
       $s=$sId.';'.substr($sSes,4).';'.$a[1].';'.fKalInCode($aW[2]).';'.fKalInCode($aW[3]).';'.fKalInCode($aW[4]);
       $sMlDt=strtoupper($kal_NutzerFelder[0]).': '.$sId."\n".strtoupper($kal_NutzerFelder[2]).': '.$aW[2]."\n".strtoupper($kal_NutzerFelder[3]).': '.str_repeat('*',strlen($aW[3])/2)."\n".strtoupper($kal_NutzerFelder[4]).': '.$aW[4];
       for($j=5;$j<$nFelder;$j++){$s.=';'.str_replace(';','`,',fKalDtCode($aW[$j])); $sMlDt.="\n".strtoupper($kal_NutzerFelder[$j]).': '.$aW[$j];} $aD[$k]=$s."\n";
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
        $Et=KAL_TxNutzerGeaendert; $Es='Erfo';
       }else{$Et=str_replace('#',KAL_TxBenutzer,KAL_TxDateiRechte); $sSes='';}
      }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
     }else $Et=KAL_TxNutzerFalsch;
    }elseif($DbO){ //bei SQL
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$sId.'"')){
      $i=$rR->num_rows; $aN=$rR->fetch_row(); $rR->close(); $s='';
      if($i==1){ //gefunden
       if($a[2]!=$aW[2]) $s.=', benutzer="'.fKalDtCode($aW[2]).'"';
       if($a[3]!=$aW[3]) $s.=', passwort="'.fKalInCode($aW[3]).'"';
       if($a[4]!=$aW[4]) $s.=', email="'.fKalDtCode($aW[4]).'"';
       $sMlDt=strtoupper($kal_NutzerFelder[0]).': '.$sId."\n".strtoupper($kal_NutzerFelder[2]).': '.$aW[2]."\n".strtoupper($kal_NutzerFelder[3]).': '.str_repeat('*',strlen($aW[3])/2)."\n".strtoupper($kal_NutzerFelder[4]).': '.$aW[4];
       for($j=5;$j<$nFelder;$j++){if($a[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.fKalDtCode($aW[$j]).'"'; $sMlDt.="\n".strtoupper($kal_NutzerFelder[$j]).': '.$aW[$j];}
       if($s!=''){ //veraendert
        if($a[2]!=$aW[2]){ //Benutzname
         if($rR=$DbO->query('SELECT nr FROM '.KAL_SqlTabN.' WHERE benutzer="'.fKalDtCode($aW[2]).'"')){
          $i=$rR->num_rows; $rR->close();
         }else $i=1;
        }else $i=0;
        if($i==0){ //Benutzername unveraendert oder frei
         if($DbO->query('UPDATE IGNORE '.KAL_SqlTabN.' SET '.substr($s,2).' WHERE nr="'.$sId.'"')){
          $Et=KAL_TxNutzerGeaendert; $Es='Erfo';
         }else{$Et=KAL_TxSqlAendr; $sSes='';}
        }else{$Et=KAL_TxNutzerVergeben; $aFehl[2]=true;}
       }else{$Et=KAL_TxKeineAenderung; $Es='Meld';}
      }else $Et=KAL_TxNutzerFalsch;
     }else $Et=KAL_TxSqlFrage;
    }//SQL
    if(KAL_NutzerAendAdmMail&&$Es=='Erfo'){ //Mail versenden
     require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
     $Mailer->AddTo(strpos(KAL_EmpfNutzer,'@')>0?KAL_EmpfNutzer:KAL_Empfaenger);
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->SetFrom($s,$t); if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
     $Mailer->Subject=str_replace('#N',$sId,KAL_TxNutzAendAdmBtr);
     $Mailer->Text=str_replace('#D',$sMlDt,str_replace('#N',$sId,str_replace('\n ',"\n",KAL_TxNutzAendAdmTxt)));
     $Mailer->Send();
    }
   }else{$Et=KAL_TxKeineAenderung; $Es='Meld';}
  }else $Et=KAL_TxEingabeFehl;

 }else{ //GET
  $sId=(isset($a[0])?$a[0]:''); $aW=$a;
 } //GET

 //Seitenausgabe
 if(!$bSes||empty($sId)) $Et=KAL_TxSessionUngueltig;
 elseif(!$Et){$Et=KAL_TxNutzerPruefe; $Es='Meld';}
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>';
 if(KAL_DSEPopUp&&(KAL_NutzerDSE1||KAL_NutzerDSE2)) $X.="\n".'<script>function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.KAL_DSEPopupW.',height='.KAL_DSEPopupH.',left='.KAL_DSEPopupX.',top='.KAL_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';

 $nFarb=2; if($aW[1]=='1'){$s=''; $t='Grn';}elseif($aW[1]=='0'){$s=KAL_TxNicht.' '; $t='Rot';}elseif($aW[1]=='2'){$s=KAL_TxMailAdresse.' '.KAL_TxAktiv.', '.KAL_TxBenutzer.' '.KAL_TxNicht.' '; $t='RtGn';}else{$s=KAL_TxNicht.' '; $t='Rot';}
 $X.='
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n ".KAL_Hidden).'
 <input type="hidden" name="kal_Aktion" value="ndaten">
 <input type="hidden" name="kal_Session" value="'.$sSes.'">
 <input type="hidden" name="kal_Zentrum" value="'.((int)$bSes).'">
 <input type="hidden" name="kal_Id" value="'.$sId.'">
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
 $X.="\n </div>";
 $X.="\n".' <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxEingabe).'" title="'.fKalTx(KAL_TxEingabe).'"></div>'."\n";
 $X.=' </form>';

 return $X;
}

function fKalPflichtFelder($aV,$nFelder){
 global $kal_NutzerPflicht;
 $aFe=array(); $aNPfl=$kal_NutzerPflicht; array_splice($aNPfl,1,1);
 if(strlen($aV[2])<4||strlen($aV[2])>25) $aFe[2]=true; //Benutzer
 if(strlen($aV[3])<4||strlen($aV[3])>16) $aFe[3]=true; //Passwort
 if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-z���_-]+(\.[0-9a-z���_-]+)*\.[a-z]{2,16}$/',strtolower($aV[4]))) $aFe[4]=true; //eMail
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