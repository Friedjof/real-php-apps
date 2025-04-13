<?php
function fKalSeite(){ //eigene Zusage aus E-Mail-Link freischalten
 global $kal_NutzerFelder, $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;

 $bOK=false; $sAkt=''; $sT=''; $Et=''; $Es='Fehl'; $aZ=array();

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET pruefen
  $sAkt=fKalRq1(isset($_GET['kal_Aktion'])?$_GET['kal_Aktion']:''); $sId=substr($sAkt,11);
  $nS=9; $s=KAL_Schluessel.hexdec(substr($sAkt,9,2)).$sId; for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
  if($nS==hexdec(substr($sAkt,7,2))){
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     $aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDecode($aZ[8]);
     break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$sId.'"')){
     $aZ=$rR->fetch_row(); $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
   if(is_array($aZ)&&count($aZ)>6){
    $bOK=true; $sT=KAL_TxZusageBestaetTxt; $Et=KAL_TxZusageBestaetigen; $Es='Meld';
    if($aZ[6]=='-'){$Et=KAL_TxZusageWiderruf; $sT=KAL_TxZusageWdrrufTxt;}
    $sT=str_replace('#V',str_replace('`,',';',$aZ[4]),str_replace('#T',fKalAnzeigeDatum($aZ[2]),str_replace('#N',$aZ[8],str_replace('#Z',fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10),$sT))));
   }else if(empty($Et)) $Et=KAL_TxKeinDatensatz;
  }else $Et=KAL_TxZusageBstCodeFehlt;
 }elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST freischalten
  $sAkt=fKalRq1(isset($_POST['kal_Aktion'])?$_POST['kal_Aktion']:''); $sId=substr($sAkt,11); $sSta='';
  $nS=9; $s=KAL_Schluessel.hexdec(substr($sAkt,9,2)).$sId; for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
  if($nS==hexdec(substr($sAkt,7,2))){
   if(!KAL_SQL){ //Textdateien
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s); $bNeu=false;
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     $aZ=explode(';',rtrim($aD[$i])); $sSta=(isset($aZ[6])?$aZ[6]:'?');
     if($sSta=='0'){ //freischalten
      $aZ[6]=(KAL_DirektZusage==2?'2':'1'); $aD[$i]=implode(';',$aZ)."\n"; $bNeu=true;
     }elseif($sSta=='-'){ //widerrufen
      if(KAL_DirektZusage==2){$aZ[6]='*'; $aD[$i]=implode(';',$aZ)."\n"; $bNeu=true;} //halbbestaetigt
      else{ //loeschen
       $aD[$i]=''; $sNr=';'.(int)$aZ[1].';'; $p=strlen($sNr); $sLschUsr=$aZ[8]; $sLNr=$sId; $bNeu=true;
       for($j=1;$j<$nSaetze;$j++){
        $s=$aD[$j]; if(substr($s,strpos($s,';'),$p)==$sNr){ //gefunden
         $a=explode(';',$s,10); if($sLschUsr==$a[8]){$sLNr.=', '.$a[0]; $aD[$j]='';}
      }}}
     }else $Es='Meld';
     if($bNeu) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $bNeu=false;
      $Et=($sSta=='-'?KAL_TxZusageIstWdrrufen:KAL_TxZusageBestaetigt); $Es='Erfo';
     }else $Et=str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte);
     $aZ[8]=fKalDecode($aZ[8]);
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nTSaetze=count($aD); $s=$aZ[1].';'; $p=strlen($s);
     for($j=1;$j<$nTSaetze;$j++) if(substr($aD[$j],0,$p)==$s){ //gefunden
      $aT=explode(';',rtrim($aD[$j])); array_splice($aT,1,1); break;
     }
     break;
   }}elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$sId.'"')){
     $aZ=$rR->fetch_row(); $rR->close(); $sSta=(isset($aZ[6])?$aZ[6]:'?');
     if($sSta=='0'){
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="'.(KAL_DirektZusage==2?'2':'1').'" WHERE nr="'.$sId.'"')){
       $Es='Erfo'; $Et=KAL_TxZusageBestaetigt;
      }else $Et=KAL_TxSqlAendr;
     }elseif($sSta=='-'){ //widerrufen
      if(KAL_DirektZusage==2){ //halbbestaetigt
       if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="*" WHERE nr="'.$sId.'"')){
        $Es='Erfo'; $Et=KAL_TxZusageIstWdrrufen;
       }else $Et=KAL_TxSqlAendr;
      }else{ //loeschen
       if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$sId.'"')){
        $sNr=(int)$aZ[1]; $sLschUsr=$aZ[8]; $sLNr=$sId; $Es='Erfo'; $Et=KAL_TxZusageIstWdrrufen;
        if($rR=$DbO->query('SELECT nr,termin FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
         while($a=$rR->fetch_row()) $sLNr.=', '.$a[0]; $rR->close();
        }
        if(!$DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"'))
         $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
      }}
     }else $Es='Meld';
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.(int)$aZ[1].'"')){
      if($aT=$rR->fetch_row()) array_splice($aT,1,1); $rR->close();
     }
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE; //SQL
   if(isset($aZ)&&is_array($aZ)&&count($aZ)>5){
    $sT=fKalAnzeigeDatum($aZ[2]).': '.str_replace('`,',';',$aZ[4]);
    if($Es=='Erfo'){ //behandelt
     $bOK=false; $sErsatzEml=''; $sKontaktEml=''; $sAutorEml=''; $sUT=''; $sUZ='';
     if(KAL_ZusageBstInfoAdm||KAL_ZusageBstInfoAut||KAL_ZusageLschInfoAdm||KAL_ZusageLschInfoAut){ //E-Mail an Admin / Besitzer
      $sUZ='ID-'.sprintf('%04d',$sId).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
      $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
      $sUZ.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[2])).': '.fKalAnzeigeDatum($aZ[2]);
      for($i=3;$i<=$nZusageFelder;$i++){
       if($i!=5&&$i!=6&&($s=trim($aZ[$i]))){
        $sFN=str_replace('`,',';',$kal_ZusageFelder[$i]);
        if($sFN=='ANZAHL'){if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl; if($sSta=='-') $s.=' ('.KAL_TxZusageLschFrueher.')';}
        $sUZ.="\n".strtoupper($sFN).': '.str_replace('`,',';',$s).($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?'':' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'));
       }
      }
      $nFelder=count($kal_FeldName); $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); //Termindetails aufbereiten
      for($i=1;$i<$nFelder;$i++){
       $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
       if($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
        if($u=$s){
         switch($t){
          case 't': case 'a': case 'k': case 'o': break; //Text
          case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKalBB(fKalDt($s))); else $u=''; break; //Memo
          case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
           if($t=='d'){
            if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
           }elseif($w) $u.=' '.$w;
           break;
          case 'z': $u=$s.' '.KAL_TxUhr; break; //Uhrzeit
          case 'w': //Waehrung
           $s=(float)$s;
           if($s>0||!KAL_PreisLeer){
            $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); $u=$s;
            if(KAL_Waehrung){$u=$s.' '.KAL_Waehrung; $s.='&nbsp;'.KAL_Waehrung;}
           }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
           break;
          case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
           if($s=='J'||$s=='Y') $u=KAL_TxJa; elseif($s=='N') $u=KAL_TxNein;
           break;
          case 'n': case '1': case '2': case '3': case 'r': //Zahl
           if($t!='r') $u=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $u=str_replace('.',KAL_Dezimalzeichen,$s);
           break;
          case 'e': //E-Mai
           if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
           $u=''; break;
          case 'l': //Link
           $aI=explode('|',$s); $s=$aI[0]; $u=$s;
           if($s) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
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
               $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
               if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKalDeCode($s);
               break;
              }
             }elseif($DbO){ //SQL-Daten
              if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
               $aN=$rR->fetch_row(); $rR->close();
               if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4]; if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt;}
               else $s=KAL_TxAutorUnbekannt;
            }}}
           }else $s=KAL_TxAutor0000;
           $u=$s; break;
          default: $u='';
         }//switch
        }
        if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
        if(strlen($u)>0) $sUT.="\n".strtoupper($sFN).': '.$u;
       }elseif($t=='c'){if($s) $sKontaktEml=$s;}
       elseif($t=='e'){if($s) $sErsatzEml=$s;}
       elseif($t=='l'){$aI=explode('|',$s); if($s=$aI[0]) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;}
       elseif($t=='u'){
        if($nId=(int)$s){
         if(!KAL_SQL){ //Textdaten
          $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
          for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
           $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
           break;
          }
         }elseif($DbO){ //SQL-Daten
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
           $aN=$rR->fetch_row(); $rR->close(); if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4];}
       }}}}
      }
      if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
      $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$aZ[1];
      if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aZ[8]);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      if($sSta=='0'){
       $sBtr=str_replace('#A',fKalHost(),KAL_TxZusageInfoBtr); $Mailer->Subject=$sBtr; $sTxt=KAL_TxZusageInfoMTx;
       $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
       if(KAL_ZusageBstInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageBstInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
      }elseif($sSta=='-'){
       $sBtr=str_replace('#A',fKalHost(),KAL_TxZusageLschBtr); $Mailer->Subject=$sBtr; $sTxt=KAL_TxZusageLschMTx;
       $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
       if(KAL_ZusageLschInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageLschInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
      }
     }
    }else if(empty($Et)) $Et=KAL_TxZusage0Status; //Erfo
   }else if(empty($Et)) $Et=KAL_TxKeinDatensatz;
  }else $Et=KAL_TxZusageBstCodeFehlt;
 }

 //Formular- und Tabellenanfang
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>
 <form class="kalLogi" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">
 <input type="hidden" name="kal_Aktion" value="'.$sAkt.'">
 <div class="kalTabl">
  <div class="kalTbZl1"><div class="kalTbSpa" style="padding:8px;text-align:center;">'.fKalTx($sT).'</div></div>
 </div>';
 if($bOK) $X.="\n".' <div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'"></div>'; else $X.='&nbsp;';
 $X.="\n".' </form>'."\n";

 return $X;
}
?>