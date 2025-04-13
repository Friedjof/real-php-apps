<?php
function fKalSeite(){ //eigene Zusage aendern
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag, $kal_NutzerFelder;
 array_splice($kal_NutzerFelder,1,1); $nFelder=count($kal_NutzerFelder);

 $Et=''; $Es='Fehl'; $sQ=''; $sHid=''; $sTId='??'; $sUT=''; $sUZ=''; $Et2='';
 $aOk=array(); $aW=array(); $bOK=true; $bDo=true;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 $bSesOK=false; $sSession=substr(KAL_Session,0,29); $nNId=0; $sNutzerEml=''; //Session pruefen
 if($sSes=substr($sSession,17,12)){
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nNId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $bSesOK=true;
      $a[2]=fKalDeCode($a[2]); $a[3]=fKalDeCode($a[3]); $a[4]=fKalDeCode($a[4]); $sNutzerEml=$a[4];
      for($j=5;$j<$nFelder;$j++){
       $a[$j]=str_replace('`,',';',$a[$j]);
       if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
      }
     }else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows==1){
      $bSesOK=true; $a=$rR->fetch_row(); array_splice($a,1,1);
      if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]); $sNutzerEml=$a[4];
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }else $Et=KAL_TxSessionUngueltig;

 if($bCaptcha=KAL_Captcha&&!$bSesOK){ //Captcha behandeln
  $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false;
  require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
  if($_SERVER['REQUEST_METHOD']=='POST'){
   $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $bOK=false;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);}
 }

 $sTermDat=''; $sTermZeit=''; $sTermVeranst=''; $sFristAnfang=''; $sFristEnde=''; $HT=''; $bFremdZ=false; $bEigenZ=false;
 $nKapazitaet=0; $nZusagenSumme=0; $nZusagenNeu=0; $nZusagenAlt=0; $nZusageAnzahlPos=0; $nTNId=0; $nZNId=0; $nPreis=0; $nPreisId=0;
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
 $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); if(strpos(KAL_ZusageFeldTyp,'a')) $kal_ZusageAuswahl=explode(';',KAL_ZusageAuswahl);
 $kal_ZusageQuellen=explode(';',KAL_ZusageQuellen); $kal_ZusagePflicht=explode(';',KAL_ZusagePflicht);
 for($i=2;$i<=$nZusageFelder;$i++){
  $aOk[$i]=true; $aW[$i]='';
  $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]); if($kal_ZusageFelder[$i]=='ANZAHL') $nZusageAnzahlPos=$i;
 }

 if($bSesOK){ //Zusage und Termin holen
  if($sId=fKalRq1(isset($_GET['kal_Nummer'])?sprintf('%0d',$_GET['kal_Nummer']):(isset($_POST['kal_Nummer'])?sprintf('%0d',$_POST['kal_Nummer']):''))){
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s); $sSta='';
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDeCode($aZ[8]); $sSta=$aZ[6]; $nZusageSatz=$i; break;}
    if(isset($aZ[1])&&($sTId=$aZ[1])){
     if($nZusageAnzahlPos>0){//Zusagesumme holen
      $s=';'.$sTId.';'; $l=strlen($s);
      for($i=1;$i<$nSaetze;$i++){
       $sZ=rtrim($aD[$i]); $p=strpos($sZ,';');
       if(substr($sZ,$p,$l)==$s){$a=explode(';',$sZ,$nZusageAnzahlPos+2); $nZusagenSumme+=($a[6]>='0'&&$a[6]<=($sSta<='2'?'2':'7')?$a[$nZusageAnzahlPos]:0);}
     }}
     $aE=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aE); $s=$sTId.';'; $p=strlen($s); //Termin holen
     for($i=1;$i<$nSaetze;$i++) if(substr($aE[$i],0,$p)==$s){$aT=explode(';',rtrim($aE[$i])); array_splice($aT,1,1); break;}
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr='.$sId)){
     $aZ=$rR->fetch_row(); $rR->close();
     if(isset($aZ[1])&&($sTId=$aZ[1])){$sSta=$aZ[6];
      if($nZusageAnzahlPos>0){//Zusagesumme holen
       if($rR=$DbO->query('SELECT COUNT(nr),SUM(dat_'.$nZusageAnzahlPos.') FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'" AND aktiv>="0" AND aktiv<="'.($sSta<='2'?'2':'7').'"')){
        $a=$rR->fetch_row(); $nZusagenSumme=(int)$a[1]; $rR->close();
      }}
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){
       $aT=$rR->fetch_row(); array_splice($aT,1,1); $rR->close();
      }else $Et=KAL_TxSqlFrage;
     }
    }else $Et=KAL_TxSqlFrage;
   }//SQL
  }else $Et=KAL_TxNummerFehlt; //keine Datensatznummer

  if(isset($aT[1])){ //Termindetails aufbereiten
   $HT="\n<br>\n".'<p class="kalMeld">'.fKalTx(str_replace('#',$sTId,KAL_TxDetails)).'</p>';
   $nFelder=count($kal_FeldName); $nFarb=1; $sKontaktEml=''; $sErsatzEml=''; $sAutorEml='';
   if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
   $HT.="\n".'<div class="kalTabl">';
   for($i=1;$i<$nFelder;$i++){
    $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i]; $u='';
    if($t=='d'){if($i==1) $sFristAnfang=$s; elseif(KAL_ZusageBisEnde&&KAL_EndeDatum&&empty($sFristEnde)) $sFristEnde=$s.' #';}
    if($kal_DetailFeld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
     if(!empty($s)){$u=$s;
      switch($t){
       case 't': $s=fKalDt($s); break; //Text/Memo
       case 'm': if(KAL_InfoMitMemo){$s=fKalBB(fKalDt($s)); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
       case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie so lassen
       case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
        if($t=='d'){
         if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
        }elseif($w) $u.=' '.$w;
        $s=str_replace(' ','&nbsp;',fKalTx($u));
        break;
       case 'z': $u=$s.' '.KAL_TxUhr; $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
       case 'w': //Währung
        $s=(float)$s;
        if($s>0||!KAL_PreisLeer){
         $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); $u=$s;
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
       case 'b': //Bild
        $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sTId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $u=KAL_Url.$s; $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
        $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">';
        break;
       case 'f': //Datei
        $u=KAL_Url.KAL_Bilder.$sTId.'~'.$s; $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$sTId.'~'.$s.'" target="_blank">'.fKalDt($s).'</a>'; break;
       case 'u':
        if(($nId=(int)$s)&&($nTNId=$nId)){
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
        }else $s=KAL_TxAutor0000; $u=$s;
        break;
       default: {$s=''; $u='';}
      }//switch
     }
     if($sFN=='KAPAZITAET'){$nKapazitaet=(int)$s; if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}}
     elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     if(strlen($u)>0) $sUT.="\n".strtoupper($sFN).': '.$u;
     if(strlen($s)>0){
      $HT.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
      $HT.="\n".' <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
      $HT.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
     }
    }elseif($t=='c'){if($s) $sKontaktEml=$s;}
    elseif($t=='e'){if($s) $sErsatzEml=$s;}
    elseif($t=='n'||$t=='t'){if($sFN=='KAPAZITAET') $nKapazitaet=(int)$s;}
    elseif($t=='l'){$aI=explode('|',$s); if($s=$aI[0]) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;}
    elseif($t=='u'){
     if(($nId=(int)$s)&&($nTNId=$nId)){
      if(!KAL_SQL){ //Textdaten
       $aE=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aE); $v=$nId.';'; $p=strlen($v);
       for($j=1;$j<$nSaetze;$j++) if(substr($aE[$j],0,$p)==$v){
        $aN=explode(';',rtrim($aE[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
        break;
       }
      }elseif($DbO){ //SQL-Daten
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
        $aN=$rR->fetch_row(); $rR->close(); if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4];}
    }}}}
   }
   $HT.="\n</div>\n";
  }else{//kein Termin gefunden
   $HT.="\n".'<p class="kalMeld">'.fKalTx(str_replace('#',$sTId,KAL_TxDetails)).'</p>';
   $HT.="\n".'<p class="kalFehl">'.fKalTx(str_replace('#',$sTId,KAL_TxKeinDatensatz)).'</p>';
  }//isset($aT[1])
  $bFremdZ=($nNId==$nTNId); $bEigenZ=($nNId==(isset($aZ[7])?(int)$aZ[7]:0));
  if(isset($aZ[2])){$sTermDat=$aZ[2]; $aZ[2]=fKalAnzeigeDatum($aZ[2]);} $aW=$aZ; $sId=$aW[0];

  if($_SERVER['REQUEST_METHOD']=='POST'){reset($_POST); $nFarb=1; $bWarteListe=false; $sSta=$aZ[6]; //Eingaben pruefen
   foreach($_POST as $k=>$v) if(substr($k,0,4)=='kal_'&&substr($k,4,2)!='zf'&&substr($k,4,3)!='Cap'){
    $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
    if($k=='kal_Sort'||$k=='kal_Abst'||$k=='kal_Index'||$k=='kal_Rueck'||$k=='kal_Start'||$k=='kal_Alt'||$k=='kal_Akt') $sQ.='&amp;'.$k.'='.$v;
    elseif($k=='kal_ZSuch') $sQ.='&amp;kal_ZSuch='.rawurlencode($v);
   }elseif(substr($k,0,6)=='kal_zf'){
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    $i=(int)substr($k,6); $aW[$i]=$s; $sFN=$kal_ZusageFelder[$i]; if($sFN=='ANZAHL') $sFN=KAL_ZusageNameAnzahl;
    if(empty($s)&&$kal_ZusagePflicht[$i]){$aOk[$i]=false; $bOK=false;}
    elseif($i==2){if($sTermDat=fKalErzeugeDatum($s)){$aW[2]=fKalAnzeigeDatum($sTermDat);}else{$aOk[2]=false; $bOK=false;}}
    elseif($i==8){if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))){$aOk[8]=false; $bOK=false;}}
    elseif($i==$nZusageAnzahlPos){
     $nZusagenNeu=(int)$s; $nZusagenAlt=(int)$aZ[$i];
     if(KAL_PruefeZusageKapaz&&$nKapazitaet>0){
      if($nZusagenNeu<=0||$nKapazitaet-$nZusagenSumme-$nZusagenNeu+$nZusagenAlt<0){
       if(!KAL_ZusageVormerkErlaubt||$sSta<='2'){$aOk[$i]=false; $bOK=false;}
       elseif($nKapazitaet-$nZusagenSumme>0){$aOk[$i]=false; $bOK=false;}
       else $bWarteListe=true;
      }
     }
    }
    if($kal_ZusageFeldTyp[$i]=='w'){
     $s=(float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s));
     if($s!=0||!KAL_PreisLeer){
      $aW[$i]=number_format($s,KAL_Dezimalstellen,'.',''); $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); if(KAL_Waehrung) $s.=' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR');
     }else{$s=''; $aW[$i]=$s;}
    }
    if(strlen($s)>0) $sUZ.="\n".strtoupper($sFN).': '.$s;
   }
   if($aZ!=$aW){ //geaendert
    if($bOK){ //eintragen
     $sZusageZeit=date('Y-m-d H:i',time()); $sZusageZeit=$aZ[5]; // ToDo: Zeit aendern????
     if(!KAL_SQL){ //Textdaten
      $nSaetze=count($aD); $s=rtrim($aD[0]);
      if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
      else for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $nId=max((int)substr($s,0,strpos($s,';')),$nId);}
      $s='Nummer_'.$nId; for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.str_replace(';','`,',$kal_ZusageFelder[$i]); $aD[0]=$s."\n";
      $s =$sId.';'.$aW[1].';'.$sTermDat.';'.str_replace(';','`,',$aW[3]).';'.str_replace(';','`,',$aW[4]).';';
      $s.=$sZusageZeit.';'.((KAL_DirektZusage==1||$bFremdZ)?$aW[6]:'0').';'.$aW[7].';'.fKalEnCode($aW[8]);
      for($i=9;$i<=$nZusageFelder;$i++) $s.=';'.str_replace(';','`,',$aW[$i]);
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
       $aD[$nZusageSatz]=rtrim($s)."\n";
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
       $Et=(!$bWarteListe?KAL_TxZusageEintr:KAL_TxZusageVormerk); $Es='Erfo'; $nZusagenSumme+=($nZusagenNeu-$nZusagenAlt);
      }else{$Et=str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte); $bOK=false;}
     }elseif($DbO){
      $sF='datum="'.$sTermDat.'",zeit="'.$aW[3].'",veranstaltung="'.$aW[4].'",buchung="'.$sZusageZeit.'",aktiv="'.((KAL_DirektZusage==1||$bFremdZ)?$aW[6]:'0').'",email="'.$aW[8].'"';
      for($i=9;$i<=$nZusageFelder;$i++){$sF.=',dat_'.$i.'="'.str_replace('"','\"',$aW[$i]).'"';}
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET '.$sF.' WHERE nr="'.$sId.'"')){
       if($DbO->affected_rows>0){$Et=(!$bWarteListe?KAL_TxZusageEintr:KAL_TxZusageVormerk); $Es='Erfo'; $nZusagenSumme+=($nZusagenNeu-$nZusagenAlt);}
       else{$Et=KAL_TxNzUnveraendert; $Es='Meld'; $bOK=false;}
      }else{$Et=KAL_TxSqlEinfg; $bOK=false;}
     }
     if($bOK){ //eingetragen, senden
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;}
      $sUZ='ID-'.sprintf('%04d',$sId).': '.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10).$sUZ; $sWww=fKalHost();
      $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sTId;
      if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
      if(KAL_ZusageAendernMail){ //E-Mail an Zusagenden
       srand((double)microtime()*1000000); $sCod=rand(100,255); $nS=9;
       $s=KAL_Schluessel.$sCod.$sId; for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
       $sLkF=KAL_Url.'kalender.php?kal_Aktion=zusage_'.dechex($nS).dechex($sCod).$sId;
       $sBtr=str_replace('#A',$sWww,KAL_TxZusageAendBtr); $sTxt=KAL_TxZusageAendMTx;
       for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',$aW[$i],$sTxt);
       require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
       if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
       $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->AddTo($aW[8]); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aW[8]);
       if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
       $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('#L',$sLkF,str_replace('\n ',"\n",$sTxt)))));
       if(!$Mailer->Send()) $Et2='</p><p class="kalFehl">'.fKalTx(KAL_TxSendeFehl);
      }
      if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
      if(KAL_ZusageAendInfoAdm||(KAL_ZusageAendInfoAut&&!empty($sKontaktEml))){ //E-Mail an Admin / Besitzer
       $sBtr=str_replace('#A',$sWww,KAL_TxZusageAenABtr); $sTxt=KAL_TxZusageAenAMTx;
       require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
       if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
       $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aW[8]);
       if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
       $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
       if(KAL_ZusageNeuInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageNeuInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
      }
     }//bOK
    }else $Et=KAL_TxEingabeFehl;
   }else{$Et=KAL_TxKeineAenderung; $Es='Meld';}
  }else{//GET
   reset($_GET);
   foreach($_GET as $k=>$v) if(substr($k,0,4)=='kal_') {
    $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
    if($k=='kal_Sort'||$k=='kal_Abst'||$k=='kal_Index'||$k=='kal_Rueck'||$k=='kal_Start'||$k=='kal_Alt'||$k=='kal_Akt') $sQ.='&amp;'.$k.'='.$v;
    elseif($k=='kal_ZSuch') $sQ.='&amp;kal_ZSuch='.rawurlencode($v);
   }
   if(KAL_ZusageFrist>=0){//Frist beachten
    if(KAL_ZusageBisEnde&&strlen($sFristEnde)>9) $sFristAnfang=substr($sFristEnde,0,10);
    if($sFristAnfang<date('Y-m-d',time()+86400*KAL_ZusageFrist)){$bDo=false; $Et=KAL_TxZusageSperre;}
  }}
 }//bSesOK

 //Seitenausgabe
 if(!($bFremdZ||$bEigenZ)){$bDo=false; if(empty($Et)) $Et=KAL_TxNummerFremd;}
 if(!$Et){$Et=KAL_TxNzZusageAenden; $Es='Meld';}
 $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).$Et2.'</p>';

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

 $aZusageFrmFeld=explode(';',KAL_ZusageFrmFeld); $n=count($aZusageFrmFeld); $aZFF=array();
 for($i=1;$i<$n;$i++) if($aZusageFrmFeld[$i]>0) $aZFF[$i]=$aZusageFrmFeld[$i]; asort($aZFF); reset($aZFF);
 $X.="\n".'<form name="zusageForm" class="kalForm" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).rtrim("\n".$sHid);
 $X.="\n".'<div class="kalTabl">'; $sSta=$aW[6];
 if($sSta=='1') $sSta='Grn.gif" title="'.fKalTx(KAL_TxZusage1Status); //Status
 elseif($sSta=='0') $sSta='Rot.gif" title="'.fKalTx(KAL_TxZusage0Status);
 elseif($sSta=='2') $sSta='RtGn.gif" title="'.fKalTx(KAL_TxZusage2Status);
 elseif($sSta=='-') $sSta='RotX.gif" title="'.fKalTx(KAL_TxZusage3Status);
 elseif($sSta=='*') $sSta='RtGnX.gif" title="'.fKalTx(KAL_TxZusage4Status);
 elseif($sSta=='7') $sSta='Glb.gif" title="'.fKalTx(KAL_TxZusage7Status);
 $sSta=' <img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$sSta.'">';
 $X.="\n".'<div class="kalTbZl1"><div class="kalTbSp1">Nr.</div><div class="kalTbSp2">'.(isset($sId)?sprintf('%04d',$sId):'??').$sSta.'</div></div>'; $nCssStil=1;
 foreach($aZFF as $i=>$xx){
  $sFN=$kal_ZusageFelder[$i]; $sFT=$kal_ZusageFeldTyp[$i]; $sFS=''; $sFH=''; if(--$nCssStil<=0) $nCssStil=2; $bAnz=false;
  if($i==2){$sFS='style="width:7em;" '; $sFH=' <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.fKalDatumsFormat().'</span>';}
  elseif($i==3){$sFS='style="width:7em;" ';}
  elseif($sFN=='ANZAHL'){
   if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl; $bAnz=true;
   if($sFH=($nKapazitaet>0?KAL_TxZusageKapazRest:KAL_TxZusageKapazNull)) $sFH=' '.fKalTx(str_replace('#R',max($nKapazitaet-$nZusagenSumme,0),str_replace('#Z',$nZusagenSumme,str_replace('#K',$nKapazitaet,$sFH))));
  }
  if($sFT!='a'&&$sFT!='j'){
   if($sFT=='n') $sFS='style="width:7em;" ';
   elseif($sFT=='w'){
    $s=(float)$aW[$i]; $nPreisId=$i;
    $sFS='style="width:7em;" '; if(!$bFremdZ&&KAL_SperreZusagePreis&&$s>0) $sFS.='readonly="readonly" '; $sFH=' '.fKalTx(KAL_Waehrung);
    if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); else $s=''; $aW[$i]=$s;
   }
   if($bAnz) $sFS='style="width:7em;" '.(KAL_RechneZusagePreis?'onchange="fZusagenPreis(this.value)" ':'');
   $sFS='<input class="kalEing" type="text" name="kal_zf'.$i.'" '.$sFS.'value="'.fKalDt($aW[$i]).'">';
  }else{ //Selectbox
   $sFS='<select class="kalEing kalAuto" name="kal_zf'.$i.'" style="min-width:7.3em" size="1"><option value="">---</option>';
   if($sFT=='a'){
    $aAww=explode('|',trim($kal_ZusageAuswahl[$i])); $nAww=count($aAww);
    for($j=0;$j<$nAww;$j++) $sFS.='<option value="'.fKalTx($aAww[$j]).($aW[$i]==$aAww[$j]?'" selected="selected':'').'">'.fKalTx($aAww[$j]).'</option>';
   }elseif($sFT=='j') $sFS.='<option value="J'.($aW[$i]=='J'?'" selected="selected':'').'">'.fKalTx(KAL_TxJa).'</option><option value="N'.($aW[$i]=='N'?'" selected="selected':'').'">'.fKalTx(KAL_TxNein).'</option>';
   $sFS.='</select>';
  }
  $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">'.fKalTx($sFN).($kal_ZusagePflicht[$i]?'*':'').'</div><div class="kalTbSp2"><div class="kal'.($aOk[$i]?'Eing':'Fhlt').'">'.$sFS.$sFH.'</div></div></div>';
  if(--$nFarb<=0) $nFarb=2;
 }
 $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2 kalTbSpR"><span class="kalMini">* Pflichtfeld</span>&nbsp;</div></div>';
 if(--$nFarb<=0) $nFarb=2;
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
 $X.="\n".'</div>';
 if($bDo) $X.="\n".'<div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSenden).'" title="'.fKalTx(KAL_TxSenden).'"></div>';
 $X.="\n".'</form>'."\n";

 if($n=array_search('w',$kal_ZusageFeldTyp)) if($n=$kal_ZusageQuellen[$n]) if(strpos($n,'T')){$n=(int)$n; $nPreis=(isset($aT[$n])?$aT[$n]:0);}

 $X.="
<script>
function fZusagenPreis(nZusagen){
 var nPreis=".$nPreis.";
 if(".$nPreisId.">0&&nPreis>0) document.zusageForm.kal_zf".$nPreisId.".value=((nZusagen*nPreis).toFixed(".KAL_Dezimalstellen.")).replace(/\./,'".KAL_Dezimalzeichen."');
}
</script>\n";

 if($bFremdZ){$s='nzusagenfliste'; $t=KAL_TxNfUebersicht;} else{$s='nzusageneliste'; $t=KAL_TxNzUebersicht;}
 $X.="\n".'<div class="kalSchalter">'.KAL_LinkAnf.'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion='.$s.$sSession.$sQ.'&amp;kal_Zentrum=1">'.fKalTx($t).(KAL_Zusagen?'':' (D'.'em'.'ov'.'er'.'si'.'on'.')').'</a>'.KAL_LinkEnd.'</div>';
 $HT.="\n".'<div class="kalSchalter">'.KAL_LinkAnf.'<a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion='.$s.$sSession.$sQ.'&amp;kal_Zentrum=1">'.fKalTx($t).'</a>'.KAL_LinkEnd.'</div>';

 return $X.$HT;
}

function fKalEnCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}

function fKalErzeugeDatum($w){
 $nJ=2; $nM=1; $nT=0; $w=substr($w,0,10); if(($p=strrpos($w,' '))&&$p>7) $w=trim(substr($w,0,$p));
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 $nJ=(isset($a[$nJ])?(strlen($a[$nJ])<=2?2000+$a[$nJ]:(int)$a[$nJ]):2000); $nM=(isset($a[$nM])?(int)$a[$nM]:0); $nT=(isset($a[$nT])?(int)$a[$nT]:0);
 if(checkdate($nM,$nT,$nJ)) return sprintf('%04d-%02d-%02d',$nJ,$nM,$nT);
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
?>