<?php
if(!function_exists('fKalSeite') ){ //bei direktem Aufruf
 function fKalSeite(){return fKalZeigeZusagen();}
}

function fKalZeigeZusagen(){ //Zusagenliste zum Termin
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag, $kal_NutzerFelder;

 $sTId=fKalRq(isset($_GET['kal_Nummer'])?$_GET['kal_Nummer']:(isset($_POST['kal_Nummer'])?$_POST['kal_Nummer']:'0')); $aT=array();
 $Et=''; $Es='Fehl'; $sHid=''; $bSesOK=false; $bNtzZ=false; $bErlaubt=true; $nNPos=array_search('u',$kal_FeldType); $sUT='';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 if($sSes=substr(KAL_Session,17,12)){ //Session prufen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   $k=KAL_NNutzerListFeld; if($k<=0) $k=KAL_NutzerListFeld;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSesOK=true;
     break;
    }
    if(!$bSesOK) $Et=KAL_TxSessionUngueltig;
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSesOK=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }

 if($bSesOK&&(isset($_GET['kal_Zusagen'])||isset($_POST['kal_Zusagen']))&&(isset($_GET['kal_Zentrum'])||isset($_POST['kal_Zentrum']))) $bNtzZ=true;
 if($bNtzZ&&$nNPos>0&&!KAL_NAendernFremde){ //Terminbesitzer pruefen
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $k=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$k)==$s){
    $aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); if((int)$aT[$nNPos]!=$nNId) $bErlaubt=false;
    break;
   }
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){
    $aT=$rR->fetch_row(); $rR->close(); if(count($aT)>1){array_splice($aT,1,1); if((int)$aT[$nNPos]!=$nNId) $bErlaubt=false;}
 }}}

 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); $nAnzahlPos=-1; $nZusSum=0; //Felder aufbereiten
 $kal_ZusageLstFeld=explode(';',(!$bSesOK?KAL_ZusageLstFeld:KAL_NZusageLstFld)); $nZusageFelder=substr_count(KAL_ZusageFelder,';');

 $sLsch=''; $aLsch=array();// Zusagen loeschen
 if($bNtzZ&&$bErlaubt){
  if($_SERVER['REQUEST_METHOD']=='POST'){
   $Et=KAL_TxNzUnveraendert; $Es='Meld';
   if(isset($_POST['kal_Lsch_x'])||isset($_POST['kal_Lsch_y'])){
    reset($_POST); $n=0;
    if(isset($_POST['kal_LschNun'])&&$_POST['kal_LschNun']=='1'){
     if(!KAL_SQL){
      $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aTmp); $aD[0]=rtrim($aTmp[0])."\n";
      for($i=1;$i<$nSaetze;$i++){$s=rtrim($aTmp[$i]); $aD[(int)$s]=$s."\n";}
      foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_'&&($i=(int)substr($k,6))){
       $a=explode(';',rtrim($aD[$i])); $n++; $aD[$i]=''; $a[8]=fKalDeCode($a[8]); $aLsch[]=$a;
      }
      if($n){
       if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //Zusagen neu schreiben
        fwrite($f,str_replace("\r",'',rtrim(implode('',$aD)))."\n"); fclose($f);
        $Et=str_replace('#N',$n,KAL_TxNzGeloescht); $Es='Erfo';
       }else{$Et=str_replace('#',KAL_Daten.KAL_Erinner,KAL_TxDateiRechte); $Es='Fehl';}
      }
     }elseif($DbO){ //SQL
      foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_'){
       $i=(int)substr($k,6);
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$i.'" AND termin="'.$sTId.'"')){
        $a=$rR->fetch_row(); $rR->close();
        if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$i.'" AND termin="'.$sTId.'"')){
         if($DbO->affected_rows>0){$n++; $aLsch[]=$a;}
      }}}
      if($n){$Et=str_replace('#N',$n,KAL_TxNeGeloescht); $Es='Erfo';}
     }
     if(KAL_ZusageLschNzZusag){ //Mails versenden
      $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
      if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
      $sUT=trim(fKalTerminPlainText($aT)); $sTxt=str_replace('#D',$sUT,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
      foreach($aLsch as $a){
       $sUZ=fKalZusagePlainText($a,$kal_ZusageFelder);
       $Mailer->Subject=str_replace('#A',fKalHost(),KAL_TxZusageLschBtr); $Mailer->SetReplyTo($a[8]);
       $Mailer->Text=str_replace('#Z',trim($sUZ),str_replace('#A',$sLnk.$aT[0],$sTxt));
       $Mailer->AddTo($a[8]); $Mailer->Send(); $Mailer->ClearTo();
     }}
     $aLsch=array();
    }else{
     foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_') $aLsch[(int)substr($k,6)]=true;
     if($n=count($aLsch)){$Et=str_replace('#N',$n,KAL_TxNeLoeschen); $Es='Fehl'; $sLsch='1';}
   }}
  }elseif(isset($_GET['kal_Status'])){ //Status aendern
   $nZId=fKalRq1($_GET['kal_Status']); $Et=KAL_TxNzUnveraendert; $Es='Meld'; $bMail=false;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$nZId.';'; $k=strlen($s); $bNeu=false;
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$k)==$s){ //gefunden
     $aZ=explode(';',rtrim($aD[$i])); $sSta=$aZ[6];
     if((int)$aZ[1]==(int)$sTId){ //passt zum Termin
      if($sSta=='0'){$aZ[6]='1'; $bNeu=true;}elseif($sSta=='1'){$aZ[6]='0'; $bNeu=true;}elseif($sSta=='7'){$aZ[6]='1'; $bNeu=true;} $aD[$i]=implode(';',$aZ)."\n";
      if($sSta=='*'){ //widerrufen
       $aD[$i]=''; $sTNr=';'.(int)$aZ[1].';'; $k=strlen($sTNr); $sLschUsr=$aZ[8]; $a=$aZ; $a[8]=fKalDeCode($a[8]); $aLsch[]=$a; $bNeu=true;
       for($j=1;$j<$nSaetze;$j++){
        $s=$aD[$j]; if(substr($s,strpos($s,';'),$k)==$sTNr){
         $a=explode(';',rtrim($s)); if($a[8]==$sLschUsr) $aD[$j]='';
      }}}
      if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //Zusagen neu schreiben
       fwrite($f,str_replace("\r",'',rtrim(implode('',$aD)))."\n"); fclose($f);
       $Et=str_replace('#N',$nZId,KAL_TxNzZusageStat); $Es='Erfo'; $aZ[8]=fKalDecode($aZ[8]);
       if($sSta=='*') $Et=str_replace('#N','1',KAL_TxNzGeloescht); $bMail=($aZ[6]=='1');
      }else{$Et=str_replace('#',KAL_Daten.KAL_Erinner,KAL_TxDateiRechte); $Es='Fehl';}
     }
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nZId.'" AND termin="'.$sTId.'"')){
     $aZ=$rR->fetch_row(); $rR->close(); $sSta=$aZ[6]; $sLschUsr=$aZ[8];
     if($sSta=='0'||$sSta=='1'||$sSta=='7'){ //Status aendern
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="'.($sSta!='1'?'1':'0').'" WHERE nr="'.$nZId.'" AND termin="'.$sTId.'"')){
       if($DbO->affected_rows>0){$Et=str_replace('#N',$nZId,KAL_TxNzZusageStat); $Es='Erfo'; $bMail=($sSta!='1');}
      }
     }elseif($sSta=='*'){ //widerrufen
      if($rR=$DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$nZId.'" AND termin="'.$sTId.'"')){
       $Et=str_replace('#N','1',KAL_TxNzGeloescht); $Es='Erfo'; $aLsch[]=$aZ;
       $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'" AND email="'.$sLschUsr.'"');
      }elseif(!$Et) $Et=KAL_TxSqlAendr;
     }
    }elseif(!$Et) $Et=KAL_TxSqlFrage;
   }elseif(!$Et) $Et=KAL_TxSqlVrbdg;
   if((KAL_ZusageFreigabeMail||KAL_ZusageBstInfoAdm)&&$bMail){ //Freigabe-E-Mails
    require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
    $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
    if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->SetFrom($s,$t); $sUZ=fKalZusagePlainText($aZ,$kal_ZusageFelder); if(empty($sUT)) $sUT=trim(fKalTerminPlainText($aT));
    if(KAL_ZusageFreigabeMail){
     $Mailer->Subject=str_replace('#A',fKalHost(),KAL_TxZusageFreiBtr); $Mailer->SetReplyTo($aZ[8]); $sTxt=KAL_TxZusageFreiMTx ;
     for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',$aZ[$i],$sTxt);
     $Mailer->Text=str_replace('#D',$sUT,str_replace('#Z',trim($sUZ),str_replace('#A',$sLnk.$aT[0],str_replace('\n ',"\n",$sTxt))));
     $Mailer->AddTo($aZ[8]); $Mailer->Send(); $Mailer->ClearTo();
    }
    if(KAL_ZusageBstInfoAdm){
     $Mailer->Subject=str_replace('#A',fKalHost(),KAL_TxZusageInfoBtr); $Mailer->SetReplyTo($aZ[8]); $sTxt=KAL_TxZusageInfoMTx;
     $Mailer->Text=str_replace('#D',$sUT,str_replace('#Z',trim($sUZ),str_replace('#A',$sLnk.$aT[0],str_replace('\n ',"\n",$sTxt))));
     $Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send();
    }
   }elseif(KAL_ZusageLschNzZusag&&count($aLsch)>0){ //LoeschMails versenden
    $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
    if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
    $sUT=trim(fKalTerminPlainText($aT)); $sTxt=str_replace('#D',$sUT,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
    require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
    foreach($aLsch as $a){
     $sUZ=fKalZusagePlainText($a,$kal_ZusageFelder);
     $Mailer->Subject=str_replace('#A',fKalHost(),KAL_TxZusageLschBtr); $Mailer->SetReplyTo($a[8]);
     $Mailer->Text=str_replace('#Z',trim($sUZ),str_replace('#A',$sLnk.$aT[0],$sTxt));
     $Mailer->AddTo($a[8]); $Mailer->Send(); $Mailer->ClearTo();
   }}
  }
 }

 $nSort=(int)(isset($_GET['kal_Sort'])?$_GET['kal_Sort']:(isset($_POST['kal_Sort'])?$_POST['kal_Sort']:0));
 $nAbst=(int)(isset($_GET['kal_Abst'])?$_GET['kal_Abst']:(isset($_POST['kal_Abst'])?$_POST['kal_Abst']:0));
 $nStart=(int)(isset($_GET['kal_Start'])?$_GET['kal_Start']:(isset($_POST['kal_Start'])?$_POST['kal_Start']:1));
 $sIndex=fKalRq1(isset($_GET['kal_Index'])?$_GET['kal_Index']:(isset($_POST['kal_Index'])?$_POST['kal_Index']:''));
 $sRueck=fKalRq1(isset($_GET['kal_Rueck'])?$_GET['kal_Rueck']:(isset($_POST['kal_Rueck'])?$_POST['kal_Rueck']:''));
 $sQ=($sIndex?'&amp;kal_Index='.$sIndex:'').($sRueck?'&amp;kal_Rueck='.$sRueck:'').($nStart>1?'&amp;kal_Start='.$nStart:'');
 $sQry=($nSort?'&amp;kal_Sort='.$nSort:'').($nAbst?'&amp;kal_Abst='.$nAbst:''); $bSuchDat=false; $sSuchTxt='';
 if($s=fKalRq(isset($_GET['kal_ZSuch'])?$_GET['kal_ZSuch']:(isset($_POST['kal_ZSuch'])?$_POST['kal_ZSuch']:''))){ //3-Suchparameter
  if(KAL_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  $sQ.='&amp;kal_ZSuch='.rawurlencode($s); $sSuchTxt=$s;
  $sDSep=(KAL_Datumsformat==1?'.':(KAL_Datumsformat==2||KAL_Datumsformat==3?'/':'-'));
  if(($p=strpos($s,$sDSep))&&($p=strpos($s,$sDSep,$p+1))&&strlen($s)<11){ //Separator 2x enthalten
   $sSuch=fKalNormDatum($s); if(!strpos($sSuch,'00',2)) $bSuchDat=true; else $sSuch=substr($sSuch,strpos($sSuch,'-'));
  }else $sSuch=$s;
 }else $sSuch='';

 $aSpalten=array(); $a=array(); if($kal_ZusageLstFeld[0]>'0') $a[0]=0; //Anzeigespalten aufbereiten
 for($i=1;$i<=$nZusageFelder;$i++){
  $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]); if($s=$kal_ZusageLstFeld[$i]) $a[$i]=(int)$s;
  if($kal_ZusageFelder[$i]=='ANZAHL'){$nAnzahlPos=$i; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$i]=KAL_ZusageNameAnzahl;}
 }
 if($bNtzZ&&$bErlaubt){unset($a[0]); unset($a[6]); if($nAnzahlPos>=0) $a[$nAnzahlPos]=0.6; $a[8]=0.8;} //E-Mail
 asort($a); reset($a); foreach($a as $i=>$xx) $aSpalten[]=$i; $nSpalten=count($aSpalten);

 $aZusagen=array(); $aIdx=array(); //Zusagen holen

 if($bErlaubt){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sTId.';'; $k=strlen($s);
   for($i=1;$i<$nSaetze;$i++){ $sZl=$aD[$i]; //ueber alle Datensaetze
    if(substr($sZl,strpos($sZl,';'),$k)==$s){
     $a=explode(';',rtrim($aD[$i])); $a[8]=fKalDeCode($a[8]); $sSta=$a[6]; $nZusSum+=($nAnzahlPos>0?$a[$nAnzahlPos]:1); $bOk=true;
     if($bNtzZ||KAL_ZusageListeStatus==1&&$sSta=='1'||KAL_ZusageListeStatus==0||KAL_ZusageListeStatus==2&&$sSta>'0'){
      if(!empty($sSuch)){
       if(!$bSuchDat){
        $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
       }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
      }
      if($bOk){$aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
     }
   }}
  }elseif($DbO){ //SQL
   if(KAL_ZusageListeStatus==1) $s='1'; elseif(KAL_ZusageListeStatus==2) $s='1" OR aktiv="2'; else $s='1" OR aktiv>"!';
   if($bNtzZ) $s='1" OR aktiv>"!';
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'" AND (aktiv="'.$s.'") ORDER BY nr')){
    while($a=$rR->fetch_row()){
     $sSta=$a[6]; $nZusSum+=($nAnzahlPos>0?$a[$nAnzahlPos]:1); $bOk=true;
     if(!empty($sSuch)){
      if(!$bSuchDat){
       $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
      }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
     }
     if($bOk){$aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
    }$rR->close();
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }
  if($nAbst<=0) asort($aIdx); else arsort($aIdx); reset($aIdx);
 }

 if(!$Et){if(!$sSuchTxt) $Et=KAL_TxZusagenBisher; else $Et=KAL_TxNfSuchErgebnis; $Es='Meld';} //Seitenausgabe
 $X="\n".' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

 if($bNtzZ) $X.="\n".'
<script>
 function fSelAll(bStat){
  for(var i=0;i<self.document.ZusageListe.length;++i)
   if(self.document.ZusageListe.elements[i].type=="checkbox") self.document.ZusageListe.elements[i].checked=bStat;
 }
</script>

<form name="ZusageListe" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).'
<input type="hidden" name="kal_Aktion" value="zusagezeigen">
<input type="hidden" name="kal_Session" value="'.substr(KAL_Session,17,12).'">
<input type="hidden" name="kal_Nummer" value="'.$sTId.'">
<input type="hidden" name="kal_Sort" value="'.$nSort.'">
<input type="hidden" name="kal_Abst" value="'.$nAbst.'">
<input type="hidden" name="kal_Start" value="'.$nStart.'">
<input type="hidden" name="kal_Index" value="'.$sIndex.'">
<input type="hidden" name="kal_Rueck" value="'.$sRueck.'">
<input type="hidden" name="kal_Zentrum" value="1">
<input type="hidden" name="kal_Zusagen" value="1">
<input type="hidden" name="kal_ZSuch" value="'.$sSuchTxt.'">';

 $X.="\n".'<div class="kalTabl">';

 //Kopfzeile ausgeben
 $t='e'; $w=''; $v=''; // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
 if($nSort<=0) if($nAbst<=0){$t='t'; $w=KAL_TxAbsteigend; $v='&amp;kal_Abst=1';}else{$t='r'; $w=KAL_TxAufsteigend;}
 $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
 $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=zusagezeigen'.KAL_Session.$v.'&amp;kal_Nummer='.$sTId.$sQ.($bNtzZ?'&amp;kal_Zusagen=1':'').'" title="'.fKalTx($w.KAL_TxSortieren).'">'.$t.'</a>';
 $X.="\n".' <div class="kalTbZl0">'; $nFarb='1';
 if($bNtzZ&&$bErlaubt) $X.="\n".'  <div class="kalTbLst">Nr.'.$t.'</div>'; $aSpTitle=array();
 for($i=0;$i<$nSpalten;$i++){
  $k=$aSpalten[$i]; $t=''; $sStil=' kalTbLsL'; if($k<4||$k==5||$k==6||$k==$nAnzahlPos) $sStil=' kalTbLsM';
  if($k==2||$k==8){
   $t='e'; $w=''; $v=''; // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
   if($nSort==$k) if($nAbst<=0){$t='t'; $w=KAL_TxAbsteigend; $v='&amp;kal_Abst=1';}else{$t='r'; $w=KAL_TxAufsteigend;}
   $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
   $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=zusagezeigen'.KAL_Session.$v.'&amp;kal_Sort='.$k.'&amp;kal_Nummer='.$sTId.$sQ.($bNtzZ?'&amp;kal_Zusagen=1':'').'" title="'.fKalTx($w.KAL_TxSortieren).'">'.$t.'</a>';
  }
  $X.="\n".'  <div class="kalTbLst'.$sStil.'">'.fKalTx($kal_ZusageFelder[$k]).$t.'</div>'; $aSpTitle[$k]= fKalTx($kal_ZusageFelder[$k]).$t;
 }

 foreach($aIdx as $n=>$xx){ //alle Datenzeilen
  $sZl=''; $sSta='';
  $a=$aZusagen[$n]; $s=$a[6]; if($s=='1') $sSta='Grn.gif" title="'.fKalTx(KAL_TxZusage1Status); //Status
  elseif($s=='0') $sSta='Rot.gif" title="'.fKalTx(KAL_TxZusage0Status);
  elseif($s=='2') $sSta='RtGn.gif" title="'.fKalTx(KAL_TxZusage2Status);
  elseif($s=='-') $sSta='RotX.gif" title="'.fKalTx(KAL_TxZusage3Status);
  elseif($s=='*') $sSta='RtGnX.gif" title="'.fKalTx(KAL_TxZusage4Status);
  elseif($s=='7') $sSta='Glb.gif" title="'.fKalTx(KAL_TxZusage7Status);
  $sSta='<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$sSta.'">';
  if($bNtzZ&&$bErlaubt){
   $nZId=$a[0];
   $sSta='<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=zusagezeigen'.KAL_Session.'&amp;kal_Nummer='.$sTId.'&amp;kal_Status='.$nZId.$sQ.$sQry.'&amp;kal_Zusagen=1">'.$sSta.'</a>';
   $s='<input class="kalCheck" type="checkbox" name="kal_L_'.$nZId.'" value="1"'.(isset($aLsch[$nZId])?' checked="checked"':'').'> '.$sSta.' ';
   $s.='<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusageaendern'.KAL_Session.'&amp;kal_Nummer='.$nZId.$sQ.$sQry.'&amp;kal_Zusagen=1" title="'.fKalTx((strlen(KAL_TxZeigeZusageIcon)?KAL_TxZeigeZusageIcon.' / ':'').KAL_TxAendern).'">'.sprintf('%0'.KAL_NummerStellen.'d',$nZId).'</a>';
   $sZl="\n".'  <div class="kalTbLst kalTbLsL"><span class="kalTbLst">Nr.</span>'.$s.'</div>';
  }
  for($i=0;$i<$nSpalten;$i++){ //alle Spalten
   $k=$aSpalten[$i]; $s=$a[$k]; $sStil=' kalTbLsL';
   if(strlen($s)>0){
    switch ($k){
     case 0: case 1: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //Nummer
     case 2: $s=fKalAnzeigeDatum($s); break; //Datum
     case 5: $s=fKalAnzeigeDatum($s).substr($s,10); break; //Buchung
     case 6: $s=$sSta; break; //Status
     case 8: if(!(KAL_ZeigeZusageEml||$bNtzZ)) $s=str_repeat(':',strlen($s)); break; //Eml
     default: $s=fKalDt(str_replace('`,',';',$s));
    }//switch
    if($k<4||$k==5||$k==6||$k==$nAnzahlPos) $sStil=' kalTbLsM';
    if($k==$nAnzahlPos&&$a[6]<'0') $s='-'.abs((int)$s);
   }else $s='&nbsp;';
   $sZl.="\n".'  <div class="kalTbLst'.$sStil.'"><span class="kalTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>'; //Standardlayout
  }
  $X.="\n".' </div><div class="kalTbZl'.$nFarb.'"> '.$sZl; if(--$nFarb<=0) $nFarb='2';
 }

 if(count($aZusagen)<=0){
  $X.="\n".' </div><div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb='2'; $bM1=true;
  for($i=0;$i<$nSpalten;$i++){
   $sZl='&nbsp;'; if($bM1&&$aZusageFeldTyp[$i]=='t'){$sZl=fKalTx($bErlaubt?KAL_TxKeineZusagen:KAL_TxNummerFremd); $bM1=false;}
   $X.="\n".'  <div class="kalTbLst">'.$sZl.'</div>';
  }
 }
 if($bNtzZ&&$bErlaubt){
  $sKapaz=''; if($i=array_search('KAPAZITAET',$kal_FeldName)) $sKapaz=(isset($aT[$i])?(int)$aT[$i]:''); $sKapaz=sprintf('%0d',$nZusSum).($sKapaz>0?' '.KAL_TxVon.' '.$sKapaz:'');
  if(strpos('*'.KAL_TxZusageKapazNull,'#Z')>0) $sZl=str_replace('#Z',$sKapaz,KAL_TxZusageKapazNull); else $sZl=KAL_TxNfUebersicht.': '.$sKapaz;
  $sAll=', <a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusagekontakt'.KAL_Session.'&amp;kal_Nummer='.$sTId.$sQ.$sQry.'&amp;kal_Zusagen=1" title="'.fKalTx(KAL_TxNZusageAlle).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif"> '.fKalTx(KAL_TxNZusageAlle).'</a>';
  $X.="\n".' </div><div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb='2'; $bM1=true;
  $X.="\n".'  <div class="kalTbLst kalTbLsL"><span class="kalTbLst">'.fKalTx(KAL_TxAlle.' '.KAL_TxLoeschen).'</span><input class="kalCheck" type="checkbox" kal_All" value="1" onClick="fSelAll(this.checked)"> <input type="image" class="kalIcon" name="kal_Lsch" src="'.KAL_Url.'grafik/iconLoeschen.gif" title="'.fKalTx(KAL_TxLoeschen).'"><input type="hidden" name="kal_LschNun" value="'.$sLsch.'"></div>';
  //for($i=0;$i<$nSpalten;$i++) $X.="\n".'  <div class="kalTbLst">&nbsp;</div>';
 }
 $X.="\n </div>\n</div>"; if($bNtzZ) $X.="\n</form>";

 if($bNtzZ&&$bErlaubt){ //Termin anzeigen
  $X.="\n".'<div class="kalSchalter">'.fKalTx($sZl).$sAll.'</div>';
  $X.="\n".'<div class="kalSchalter">'.KAL_LinkAnf.'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusagenfliste'.KAL_Session.'&amp;kal_Nummer='.$sTId.$sQ.'&amp;kal_Zusagen=1">'.fKalTx(KAL_TxNfUebersicht).'</a>'.KAL_LinkEnd.'</div>';
  if(isset($aT[1])){ //Termindetails aufbereiten
   $X.="\n<br>\n".'<p class="kalMeld">'.fKalTx(str_replace('#',$sTId,KAL_TxDetails)).'</p>';
   $nFelder=count($kal_FeldName); $nFarb=1; $sKontaktEml=''; $sErsatzEml=''; $sAutorEml='';
   $X.="\n".'<div class="kalTabl">';
   for($i=1;$i<$nFelder;$i++){
    $t=$kal_FeldType[$i]; $s=$aT[$i]; $sFN=$kal_FeldName[$i];
    if($t=='d'){if($i==1) $sFristAnfang=$s; elseif(KAL_ZusageBisEnde&&KAL_EndeDatum&&empty($sFristEnde)) $sFristEnde=$s.' #';}
    if($kal_NDetailFeld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
     if(!empty($s)){
      switch($t){
       case 't': $s=fKalDt($s); break; //Text/Memo
       case 'm': if(KAL_InfoMitMemo) $s=fKalBB(fKalDt($s)); else $s=''; break; //Memo
       case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie so lassen
       case 'd': case '@': $w=trim(substr($s,11)); $s=fKalAnzeigeDatum($s); //Datum
        if($t=='d'){
         if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].'&nbsp;'.$s; else $s.='&nbsp;'.$kal_WochenTag[$w];}
        }elseif($w) $s.='&nbsp;'.$w;
        break;
       case 'z': $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
       case 'w': //Währung
        if($s>0||!KAL_PreisLeer){
         $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
         if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung;
        }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
        break;
       case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
        if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein);
        break;
       case 'n': case '1': case '2': case '3': case 'r': //Zahl
        if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
        break;
       case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=fKalDt(isset($aI[1])?$aI[1]:$w);
        $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'"> ';
        $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
       }$s=substr($s,0,-2); break;
       case 'b': //Bild
        $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sTId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
        $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">';
        break;
       case 'f': //Datei
        $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$sTId.'~'.$s.'" target="_blank">'.fKalDt($s).'</a>'; break;
       case 'u':
        if($nId=(int)$s){
         if(KAL_NutzerInfoFeld>0){
          $s=KAL_TxAutorUnbekannt;
          if(!KAL_SQL){ //Textdaten
           $aE=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aE); $v=$nId.';'; $p=strlen($v);
           for($j=1;$j<$nSaetze;$j++) if(substr($aE[$j],0,$p)==$v){
            $aN=explode(';',rtrim($aE[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
            if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKalDeCode($s); $s=fKalDt($s);
            break;
           }
          }elseif($DbO){ //SQL-Daten
           if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
            $aN=$rR->fetch_row(); $rR->close();
            if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4]; if(!$s=fKalDt($aN[KAL_NutzerInfoFeld])) $s=KAL_TxAutorUnbekannt;}
            else $s=KAL_TxAutorUnbekannt;
         }}}
        }else $s=KAL_TxAutor0000;
        break;
       default: $s='';
      }//switch
     }
     if($sFN=='KAPAZITAET'){$nKapazitaet=(int)$s; if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt) $s='';}
     elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     if(strlen($s)>0){
      $X.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
      $X.="\n".' <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
      $X.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }
    }
   }
   $X.="\n</div>\n";
   $X.="\n".'<div class="kalSchalter">'.KAL_LinkAnf.'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusagenfliste'.KAL_Session.'&amp;kal_Nummer='.$sTId.$sQ.'&amp;kal_Zusagen=1">'.fKalTx(KAL_TxNfUebersicht).'</a>'.KAL_LinkEnd.'</div>';
 }}

 return $X;
}

function fKalNormDatum($w){ //Suchdatum normieren
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fKalZusagePlainText($aZ,$kal_ZusageFelder){
 $nZusageFelder=count($kal_ZusageFelder); $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 $sZ='ID-'.sprintf('%04d',$aZ[0]).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
 $sZ.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[2])).': '.fKalAnzeigeDatum($aZ[2]);
 for($i=3;$i<$nZusageFelder;$i++){
  if($i!=5&&$i!=6&&($s=trim($aZ[$i]))){
   $sFN=str_replace('`,',';',$kal_ZusageFelder[$i]);
   if($sFN=='ANZAHL') if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl;
   $sZ.="\n".strtoupper($sFN).': '.($i!=7?($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?str_replace('`,',';',$s):number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR')):sprintf('%04d',$s));
  }
 }
 return $sZ;
}

function fKalTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0];
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
   if($u=$s){
    switch($t){
     case 't': $u=@strip_tags(fKalBB(fKalDt($s))); break; //Text
     case 'a': case 'k': case 'o': break;
     case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKalBB(fKalDt($s))); else $u=''; break; //Memo
     case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
      }elseif($w) $u.=' '.$w;
      break;
     case 'z': $u=$s.' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $u=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR');
      }else $u='';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $u=KAL_TxJa; elseif($s=='N') $u=KAL_TxNein;
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $u=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $u=str_replace('.',KAL_Dezimalzeichen,$s);
      break;
     case 'e': //E-Mail
      if(!KAL_SQL) $u=fKalDecode($s); break;
     case 'l': //Link
      $aI=explode('|',$s); $s=$aI[0]; $u=$s;
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$aT[0].'-'.$s; $u=KAL_Url.$s;
      break;
     case 'f': //Datei
      $u=KAL_Url.KAL_Bilder.$aT[0].'~'.$s; break;
     case 'u':
      if($nId=(int)$s){
       if(KAL_NutzerInfoFeld>0){
        $s=KAL_TxAutorUnbekannt;
        if(!KAL_SQL){ //Textdaten
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
          if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKalDeCode($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt;}
          else $s=KAL_TxAutorUnbekannt;
       }}}
      }else $s=KAL_TxAutor0000;
      $u=$s; break;
     default: $u='';
    }//switch
   }
   if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}}
   elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(strlen($u)>0) $sT.="\n".strtoupper($sFN).': '.$u;
  }
 }
 return $sT;
}
?>