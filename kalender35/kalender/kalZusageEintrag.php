<?php
function fKalSeite(){ //Zusageneintragsformular
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;

 $Et=''; $Et2=''; $Es='Fehl'; $sQ=''; $sHid=''; $sLnk=''; $sHT=''; $sUT=''; $sHZ=''; $sUZ='';
 $aOk=array(); $aW=array(); $bOK=true; $bDo=true; $bForm=true; $bLschZusage=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 $sNutzerNr='0'; $sNutzerEml=''; $sNutzerName=''; $sBuchungsMail=''; $aNtz=array(); $bSesOK=false;
 if($sSes=substr(KAL_Session,17,12)){ //Session pruefen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   $k=KAL_NNutzerListFeld; if($k<=0) $k=KAL_NutzerListFeld;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $bSesOK=true; $aNtz=explode(';',rtrim($aD[$i])); array_splice($aNtz,1,1); $sNutzerNr=$nNId;
      $aNtz[2]=fKalDeCode($aNtz[2]); $aNtz[4]=fKalDeCode($aNtz[4]); $sNutzerEml=$aNtz[4];
      if($k>1) if(!$sNutzerName=$aNtz[$k]) $sNutzerName=KAL_TxAutorUnbekannt;
     }break;
    }
    if(!$bSesOK) $Et=KAL_TxSessionUngueltig;
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0){
      $bSesOK=true; $aNtz=$rR->fetch_row(); array_splice($aNtz,1,1); $sNutzerEml=$aNtz[4]; $sNutzerNr=$nNId;
      if($k>1) if(!$sNutzerName=$aNtz[$k]) $sNutzerName=KAL_TxAutorUnbekannt;
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }

 if($bCaptcha=KAL_Captcha&&!$bSesOK){ //Captcha behandeln
  $sCapTyp=(isset($_POST['kal_CaptchaTyp'])?$_POST['kal_CaptchaTyp']:KAL_CaptchaTyp); $bCapOk=false; $bCapErr=false;
  require_once(KAL_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(KAL_Pfad.KAL_CaptchaPfad,KAL_CaptchaSpeicher);
  if($_SERVER['REQUEST_METHOD']=='POST'){
   $sCap=$_POST['kal_CaptchaFrage']; $sCap=(KAL_Zeichensatz<=0?$sCap:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sCap):html_entity_decode($sCap)));
   if($Cap->Test($_POST['kal_CaptchaAntwort'],$_POST['kal_CaptchaCode'],$sCap)) $bCapOk=true;
   else{$bCapErr=true; $bOK=false;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(KAL_CaptchaTxFarb,KAL_CaptchaHgFarb);}
 }

 $sTermDat=''; $sTermZeit=''; $sTermVeranst=''; $sFristAnfang=''; $sFristEnde=''; $sFristPunkt=''; $nPreis=0; $nPreisId=0; $aWarteListeFrei=array();
 $nKapazitaet=0; $bKapLeer=false; $nZusagenSumme=0; $nZusagen=0; $nZusageLschZahl=0; $nZusageAnzahlPos=0; $nUsrSumme=0; $bUsrSumme=false; $bKapMaximum=false; $nKapGrenze=0; $bKapGrenze=false;
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
 $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); if(strpos(KAL_ZusageFeldTyp,'a')) $kal_ZusageAuswahl=explode(';',KAL_ZusageAuswahl);
 $kal_ZusageQuellen=explode(';',KAL_ZusageQuellen); $kal_ZusagePflicht=explode(';',KAL_ZusagePflicht);
 for($i=2;$i<=$nZusageFelder;$i++){
  $aOk[1000+$i]=true; $aW[1000+$i]='';
  $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]); if($kal_ZusageFelder[$i]=='ANZAHL') $nZusageAnzahlPos=$i;
 }

 if($sId=fKalRq1(isset($_GET['kal_Nummer'])?sprintf('%0d',$_GET['kal_Nummer']):(isset($_POST['kal_Nummer'])?sprintf('%0d',$_POST['kal_Nummer']):''))){
  $sUsrEml=(isset($_POST['kal_zf1008'])?$_POST['kal_zf1008']:''); if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $sUsrEml=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sUsrEml); else $sUsrEml=html_entity_decode($sUsrEml);
  if(!KAL_SQL){ //Termindaten holen
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $l=strlen($s); $sUsrEml=fKalEncode($sUsrEml);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    if(substr($aD[$i],0,$l)==$s){$aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); break;}
   }
   if($nZusageAnzahlPos>0){ //Zusagesumme holen
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sId.';'; $l=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     $sZ=$aD[$i];
     if(substr($sZ,strpos($sZ,';'),$l)==$s){
      $aZ=explode(';',rtrim($sZ),$nZusageAnzahlPos+2);
      $nZusagenSumme+=($aZ[6]>='0'?abs($aZ[$nZusageAnzahlPos]):0);
      if($aZ[8]==$sUsrEml){$nUsrSumme+=($aZ[6]>='0'?abs($aZ[$nZusageAnzahlPos]):0); $bUsrSumme=true;}
   }}}elseif($sUsrEml&&((KAL_LoeschGastZusage&&!$bSesOK)||($bSesOK&&KAL_LoeschNutzerZusage))){ //nur Usersumme holen
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sId.';'; $l=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     $sZ=$aD[$i];
     if(substr($sZ,strpos($sZ,';'),$l)==$s){$aZ=explode(';',rtrim($sZ),10); if($aZ[8]==$sUsrEml){$nUsrSumme++; $bUsrSumme=true;}}
    }
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
    $aT=$rR->fetch_row(); array_splice($aT,1,1); $rR->close();
   }else $Et=KAL_TxSqlFrage;
   if($nZusageAnzahlPos>0){ //Zusagesumme holen
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sId.'"')){
     while($aZ=$rR->fetch_row()){
      $nZusagenSumme+=($aZ[6]>='0'?abs($aZ[$nZusageAnzahlPos]):0);
      if($aZ[8]==$sUsrEml){$nUsrSumme+=($aZ[6]>='0'?abs($aZ[$nZusageAnzahlPos]):0); $bUsrSumme=true;}
     }$rR->close();
    }else $Et=KAL_TxSqlFrage;
   }elseif($sUsrEml&&((KAL_LoeschGastZusage&&!$bSesOK)||($bSesOK&&KAL_LoeschNutzerZusage))){ //ohne Anzahl, nur Usersumme holen
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sId.'" AND email="'.$sUsrEml.'"')){
     while($aZ=$rR->fetch_row()){$nUsrSumme++; $bUsrSumme=true;} $rR->close();
    }else $Et=KAL_TxSqlFrage;
  }}
  $nFelder=count($kal_FeldName); $nFarb=1; //Termindetails aufbereiten
  if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld; $sKontaktEml=''; $sErsatzEml=''; $sAutorEml='';
  $sHT="\n".'<div class="kalTabl">';
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
   if($t=='d'){if($i==1) $sFristAnfang=$s; elseif(KAL_ZusageBisEnde&&KAL_EndeDatum&&empty($sFristEnde)) $sFristEnde=$s.' #';}
   if($kal_DetailFeld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
    if($u=$s){
     switch($t){
      case 't': $s=fKalBB(fKalDt($s)); $u=@strip_tags(fKalBB($u)); break; //Text
      case 'm': if(KAL_InfoMitMemo){$s=fKalBB(fKalDt($s)); $u=@strip_tags(fKalBB($u));} else{$s=''; $u='';} break; //Memo
      case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzaehlung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
       if($i==KAL_TerminDatumFeld) $sTermDat=substr($s,0,10);
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
       }else{if($w) $u.=' '.$w; if($sFN=='ZUSAGE_BIS') $sFristPunkt=$s;}
       $s=str_replace(' ','&nbsp;',fKalTx($u));
       break;
      case 'z': if($i==KAL_TerminZeitFeld) $sTermZeit=$s; $u=$s.' '.KAL_TxUhr; $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
      case 'w': //Waehrung
       $s=(float)$s;
       if($s>0||!KAL_PreisLeer){
        $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); $u=$s;
        if(KAL_Waehrung){$u=$s.' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'); $s.='&nbsp;'.KAL_Waehrung;}
       }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y'){$s=fKalTx(KAL_TxJa); $u=KAL_TxJa;}elseif($s=='N'){$s=fKalTx(KAL_TxNein); $u=KAL_TxNein;}
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $u=$s;
       break;
      case 'e': //E-Mail
       if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
       $s=''; $u=''; break;
      case 'l': //Link
       $aL=explode('||',$s); $s=''; $z='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=fKalDt(isset($aI[1])?$aI[1]:$w); $z.=$w.', ';
        $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'"> ';
        $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
       }$s=substr($s,0,-2); $u=substr($z,0,-2); break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $u=KAL_Url.$s; $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
       $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">';
       break;
      case 'f': //Datei
       $u=KAL_Url.KAL_Bilder.$sId.'~'.$s; $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.fKalDt($s).'</a>'; break;
      case 'u':
       if($nId=(int)$s){
        if(KAL_NutzerInfoFeld>0){
         $s=KAL_TxAutorUnbekannt;
         if(!KAL_SQL){ //Textdaten
          $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
          for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
           $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
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
    if($i==KAL_TerminVeranstFeld) $sTermVeranst=str_replace('\n ',' ',$u);
    if($sFN=='KAPAZITAET'){
     $nKapazitaet=(int)$s; $bKapLeer=(strlen($s)==0); $nKapGrenze=$nKapazitaet; if(strpos($s,'(')){$aI=explode('(',$s); $nKapGrenze=(int)$aI[1];}
     if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}
    }elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
    if(strlen($u)>0) $sUT.="\n".strtoupper($sFN).': '.$u;
    if(strlen($s)>0){
     $sHT.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $sHT.="\n".' <div class="kalTbSp1">'.fKalTx($sFN).'</div>';
     $sHT.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
   }}
   elseif($i==KAL_TerminVeranstFeld&&($s)) $sTermVeranst=str_replace('\n ',' ',$s);
   elseif($t=='d'&&$i==KAL_TerminDatumFeld) $sTermDat=substr($s,0,10);
   elseif($t=='z'&&$i==KAL_TerminZeitFeld) $sTermZeit=$s;
   elseif($t=='c'){if($s) $sKontaktEml=$s;}
   elseif($t=='e'){if($s) $sErsatzEml=$s;}
   elseif($t=='n'){if($sFN=='KAPAZITAET'){$nKapazitaet=(int)$s; $bKapLeer=(strlen($s)==0); $nKapGrenze=$nKapazitaet; if(strpos($s,'(')){$aI=explode('(',$s); $nKapGrenze=(int)$aI[1];}}}
   elseif($t=='@'){if($sFN=='ZUSAGE_BIS') $sFristPunkt=$s;}
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
  $sHT.="\n</div>\n";
  $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sId;
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;

  if($_SERVER['REQUEST_METHOD']=='POST'){reset($_POST); $nFarb=1; //Eingaben pruefen
   $bLschZusage=(isset($_POST['kal_zf0LschZusage'])&&$_POST['kal_zf0LschZusage']=='1'); $bWarteListe=false;
   if(strlen($sTermVeranst)>KAL_ZusageVeranstLaenge) $sTermVeranst=substr($sTermVeranst,0,KAL_ZusageVeranstLaenge).'...';
   $aW[1002]=fKalAnzeigeDatum($sTermDat); $aW[1003]=$sTermZeit; $aW[1004]=$sTermVeranst; $aW[1008]=$sNutzerEml; $sBuchungsMail=$sNutzerEml; $nGrp=10;
   if($nZusageAnzahlPos>0){
    $i=$nZusageAnzahlPos+1000; $aOk[$i]=true; $s=(isset($_POST['kal_zf'.$i])?$_POST['kal_zf'.$i]:''); $nZusagen=abs((int)$s);
    if(strlen($s)>0) $bLschZusage=false; else if(!$bLschZusage){$aOk[$i]=false; $bOK=false;}
    if($nZusagen==0&&!$bLschZusage&&!KAL_ErlaubeZusageNull){$aOk[$i]=false; $bOK=false;}
    if(KAL_PruefeZusageKapaz&&!$bKapLeer&&($nKapazitaet-$nZusagenSumme-$nZusagen)<0){
     if(!KAL_ZusageVormerkErlaubt){$aOk[$i]=false; $bOK=false;}
     elseif($nKapazitaet-$nZusagenSumme>0){$aOk[$i]=false; $bOK=false;}
     else $bWarteListe=true;
    }
    if($nZusagenSumme<$nKapazitaet&&$nZusagenSumme+$nZusagen>=$nKapazitaet) $bKapMaximum=true;
    elseif($nZusagenSumme<$nKapGrenze&&$nZusagenSumme+$nZusagen>=$nKapGrenze) $bKapGrenze=true;
   }
   foreach($_POST as $k=>$v) if(substr($k,0,4)=='kal_'&&substr($k,4,2)!='zf'&&substr($k,4,3)!='Cap'){
    $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
   }elseif(substr($k,0,6)=='kal_zf'){
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if(KAL_Zeichensatz>0) if(KAL_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
    if($i=(int)substr($k,6)){
     $aW[$i]=$s; $j=$i%100; if($j!=$nZusageAnzahlPos) $aOk[$i]=true;
     $sFN=$kal_ZusageFelder[$j]; if($sFN=='ANZAHL') if(!$sFN=KAL_ZusageNameAnzahl) $sFN=KAL_TxZusageAnzahl;
     if($i<100*$nZusagen+1100){
      if(strlen($s)==0&&$j!=$nZusageAnzahlPos&&$kal_ZusagePflicht[$j]){$aOk[$i]=false; $bOK=false;}
      elseif($j==2){if($t=fKalErzeugeDatum($s)){$aW[$i]=fKalAnzeigeDatum($t); if($i==1002) $sTermDat=$t;}else{$aOk[$i]=false; $bOK=false;}}
      elseif($j==8){
       if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) if(($i==1008||!$bLschZusage)&&$kal_ZusagePflicht[$j]){$aOk[$i]=false; $bOK=false;}
       if($i==1008) $sBuchungsMail=strtolower($s);
      }
      if($kal_ZusageFeldTyp[$j]=='w'){
       $s=(float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s));
       if($s!=0||!KAL_PreisLeer){
        $aW[$i]=number_format($s,KAL_Dezimalstellen,'.',''); $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,'');
       }else{$s=''; $aW[$i]=$s;}
      }
      if(strlen($s)>0){
       if(floor($i*0.01)>$nGrp){
        $nGrp=(int)floor($i*0.01);
        $sUZ.="\n".($nGrp-10).'. '.KAL_TxZusageZeile;
        $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1"></div>'."\n".' <div class="kalTbSp2"><b>'.($nGrp-10).'. '.KAL_TxZusageZeile."</b></div>\n</div>";
        if(--$nFarb<=0) $nFarb=2;
       }
       $sUZ.="\n ".strtoupper($sFN).': '.$s.($kal_ZusageFeldTyp[$j]!='w'||!KAL_Waehrung?'':' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'));
       $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$sFN."</div>\n".' <div class="kalTbSp2">'.$s.($kal_ZusageFeldTyp[$j]!='w'||!KAL_Waehrung?'':'&nbsp;'.KAL_Waehrung)."</div>\n</div>";
       if(--$nFarb<=0) $nFarb=2;
      }
     }
    }elseif($bLschZusage&&substr($k,6)=='0LschZusage'){
     $sUZ.="\n".strtoupper(KAL_TxLoeschen).': '.KAL_TxZusageLschFrueher; $s='-'.$nUsrSumme;
     $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.KAL_TxLoeschen."</div>\n".' <div class="kalTbSp2">'.KAL_TxZusageLschFrueher."</div>\n</div>";
     if(--$nFarb<=0) $nFarb=2;
     if($nZusageAnzahlPos<=0) $sFN=strtoupper(KAL_TxLoeschen);
     else{$sFN=$kal_ZusageFelder[$nZusageAnzahlPos]; if($sFN=='ANZAHL') $sFN=KAL_ZusageNameAnzahl;}
    }
   }
   if($bOK&&$bLschZusage){ //Loeschwunsch pruefen
    if($nUsrSumme>0||(KAL_ErlaubeZusageNull&&$bUsrSumme)){
     $nZusagen=abs(0-$nUsrSumme); $aW[1000+$nZusageAnzahlPos]='-'.$nZusagen;
    }else{$bOK=false; $Et=KAL_TxZusageKeinFrueher; $Es='Meld';}
   }

   if($bOK&&!$bLschZusage&&KAL_KeineDoppelBuchung&&$sBuchungsMail>''){ // doppelte Buchung anhand E-Mail pruefen
    if(!KAL_SQL){ //Zusage holen
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sId.';'; $l=strlen($s);
     for($i=1;$i<$nSaetze;$i++){
      $sZ=$aD[$i];
      if($bOK) if(substr($sZ,strpos($sZ,';'),$l)==$s){$aZ=explode(';',rtrim($sZ),10); if(strtolower(fKalDeCode($aZ[8]))==$sBuchungsMail){$bOK=false;}}
     }
    }elseif($DbO){ //SQL-Daten
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sId.'" AND email="'.$sBuchungsMail.'"')){
      if($aZ=$rR->fetch_row())$bOK=false; $rR->close();
     }
    }
    if(!$bOK) if(!$Et) $Et=KAL_TxKeineDoppelBuchung;
   }

   if(KAL_ZusageDSE1) if(isset($_POST['kal_DSE1'])&&$_POST['kal_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $bOK=false;}
   if(KAL_ZusageDSE2) if(isset($_POST['kal_DSE2'])&&$_POST['kal_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $bOK=false;}

   if($bOK){ //eintragen und senden
    $sZusageZeit=date('Y-m-d H:i',time()); $nId=0;
    if(!KAL_SQL){ //Textdaten
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=rtrim($aD[0]); $nNeu=0;
     if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     else for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $nId=max((int)substr($s,0,strpos($s,';')),$nId);}
     if(KAL_DirektZusage!=1||!$bLschZusage){ //keine Direktzusage oder kein Loeschen
      $s =(++$nId).';'.$sId.';'.$sTermDat.';'.str_replace(';','`,',$aW[1003]).';'.str_replace(';','`,',$aW[1004]).';';
      $s.=$sZusageZeit.';'.(KAL_DirektZusage==1?(!$bWarteListe?'1':'7'):(!$bLschZusage?(!$bWarteListe?'0':'7'):'-')).';'.$sNutzerNr.';'.fKalEnCode($aW[1008]);
      for($i=9;$i<=$nZusageFelder;$i++) $s.=';'.($i!=$nZusageAnzahlPos||!KAL_ZusageNamentlich?str_replace(';','`,',$aW[1000+$i]):min(1,$nZusagen));
      if(KAL_ZusageNamentlich&&$nZusagen>1) for($j=2;$j<=$nZusagen;$j++){
       $k=100*$j+1000;
       $s.="\n".($nId+(++$nNeu)).';'.$sId.';'.(isset($aW[$k+2])&&$aW[$k+2]?fKalErzeugeDatum($aW[$k+2]):$sTermDat).';'.(isset($aW[$k+3])&&$aW[$k+3]?str_replace(';','`,',$aW[$k+3]):$aW[1003]).';'.(isset($aW[$k+4])&&$aW[$k+4]?str_replace(';','`,',$aW[$k+4]):$aW[1004]).';';
       $s.=$sZusageZeit.';'.(KAL_DirektZusage==1?'1':(!$bLschZusage?'0':'-')).';'.$sNutzerNr.';'.fKalEnCode($aW[$k+8]);
       for($i=9;$i<=$nZusageFelder;$i++){if($i!=$nZusageAnzahlPos) $s.=';'.(isset($aW[$k+$i])?str_replace(';','`,',$aW[$k+$i]):''); else $s.=';1';}
      }
     }else{ //Loeschen bei Direktzusage
      $s=';'.$sId.';'; $l=strlen($s); $p=max($nZusageAnzahlPos+2,11);
      for($i=1;$i<$nSaetze;$i++){
       $sZ=$aD[$i];
       if(substr($sZ,strpos($sZ,';'),$l)==$s){
        $aZ=explode(';',rtrim($sZ),$p);
        if($aZ[8]==$sUsrEml){
         $aD[$i]=''; $bWarteListe=false;
         if(KAL_ZusageVormerkErlaubt&&$nZusageAnzahlPos>0&&isset($aZ[$nZusageAnzahlPos])) $nZusageLschZahl+=$aZ[$nZusageAnzahlPos];
      }}}
      $p=max($nZusageAnzahlPos+2,8); $bDo=true;
      if($nZusageLschZahl>0) for($i=1;$i<$nSaetze;$i++) if($bDo){ // Wartelisteneintrag hochstufen
       $aZ=explode(';',rtrim($aD[$i]),$p);
       if(isset($aZ[6])&&$aZ[1]==$sId&&$aZ[6]=='7'&&$aZ[$nZusageAnzahlPos]<=$nZusageLschZahl){
        $aZ[6]='1'; $aD[$i]=implode(';',$aZ)."\n"; $aWarteListeFrei[]=explode(';',rtrim($aD[$i]));
        $nZusageLschZahl-=$aZ[$nZusageAnzahlPos]; if($nZusageLschZahl<=0) $bDo=false;
      }}
      $s=''; $bDo=true;
     }
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
      $t='Nummer_'.($nId+$nNeu); for($i=1;$i<=$nZusageFelder;$i++) $t.=';'.str_replace(';','`,',$kal_ZusageFelder[$i]); $aD[0]=$t."\n";
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n".($s>''?trim($s)."\n":'')); fclose($f);
      $Et=(!$bLschZusage?(!$bWarteListe?KAL_TxZusageEintr:KAL_TxZusageVormerk):KAL_TxZusageGeloescht); $Es='Erfo'; $nZusagenSumme+=(!$bLschZusage?$nZusagen:-$nUsrSumme);
     }else{$Et=str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte); $bOK=false;}
    }elseif($DbO){
     $sF='termin,datum,zeit,veranstaltung,buchung,aktiv,benutzer,email';
     $sV='"'.$sId.'","'.$sTermDat.'","'.$aW[1003].'","'.$aW[1004].'",';
     $sV.='"'.$sZusageZeit.'","'.(KAL_DirektZusage==1?(!$bWarteListe?'1':'7'):(!$bLschZusage?(!$bWarteListe?'0':'7'):'-')).'","'.$sNutzerNr.'","'.$aW[1008].'"';
     for($i=9;$i<=$nZusageFelder;$i++){$sF.=',dat_'.$i; if($i!=$nZusageAnzahlPos||!KAL_ZusageNamentlich) $sV.=',"'.str_replace('"','\"',$aW[1000+$i]).'"'; else $sV.=',"1"';}
     if(KAL_DirektZusage!=1||!$bLschZusage){ //keine Direktzusage oder kein Loeschen
      if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' ('.$sF.') VALUES('.$sV.')')){
       if($nId=$DbO->insert_id){
        if(KAL_ZusageNamentlich&&$nZusagen>1) for($j=2;$j<=$nZusagen;$j++){
         $k=100*$j+1000;
         $sV='"'.$sId.'","'.(isset($aW[$k+2])&&$aW[$k+2]?fKalErzeugeDatum($aW[$k+2]):$sTermDat).'","'.(isset($aW[$k+3])&&$aW[$k+3]?$aW[$k+3]:$aW[1003]).'","'.(isset($aW[$k+4])&&$aW[$k+4]?$aW[$k+4]:$aW[1004]).'",';
         $sV.='"'.$sZusageZeit.'","'.(KAL_DirektZusage==1?'1':(!$bLschZusage?'0':'-')).'","'.$sNutzerNr.'","'.$aW[$k+8].'"';
         for($i=9;$i<=$nZusageFelder;$i++){if($i!=$nZusageAnzahlPos||!KAL_ZusageNamentlich) $sV.=',"'.str_replace('"','\"',$aW[$k+$i]).'"'; else $sV.=',"1"';}
         $DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' ('.$sF.') VALUES('.$sV.')');
        }
        $Et=(!$bLschZusage?(!$bWarteListe?KAL_TxZusageEintr:KAL_TxZusageVormerk):KAL_TxZusageGeloescht); $Es='Erfo'; $nZusagenSumme+=$nZusagen;
       }else{$Et=KAL_TxSqlEinfg; $bOK=false;}
      }else{$Et=KAL_TxSqlEinfg; $bOK=false;}
     }else{ //Direktzusage und Loeschen
      if(KAL_ZusageVormerkErlaubt&&$nZusageAnzahlPos) if($rR=$DbO->query('SELECT COUNT(nr),SUM(dat_'.$nZusageAnzahlPos.') FROM '.KAL_SqlTabZ.' WHERE email="'.$sUsrEml.'" AND termin="'.$sId.'"')){
       if($a=$rR->fetch_row()) $nZusageLschZahl=$a[1]; $rR->close();
      }
      if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE email="'.$sUsrEml.'" AND termin="'.$sId.'"')){
       $Et=(!$bLschZusage?KAL_TxZusageEintr:KAL_TxZusageGeloescht); $Es='Erfo'; $nZusagenSumme-=$nUsrSumme;
       while($nZusageLschZahl>0){
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sId.'" AND aktiv="7" AND dat_'.$nZusageAnzahlPos.'<="'.$nZusageLschZahl.'" LIMIT 1')){
         if($a=$rR->fetch_row()){
          $nZusageLschZahl-=$a[1]; $aWarteListeFrei[]=$a;
          $DbO->query('UPDATE '.KAL_SqlTabZ.' SET aktiv="1" WHERE nr="'.$a[0].'" AND termin="'.$sId.'" AND aktiv="7"');
         }else $nZusageLschZahl=0; $rR->close();
        }else $nZusageLschZahl=0;
       }
      }else{$Et=KAL_TxSqlEinfg; $bOK=false;}
    }}
    if($bOK){ //eingetragen
     if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} $bDo=false; $sWww=fKalHost(); $sDv=''; //Captcha loeschen
     if(KAL_ZusageNamentlich&&$nZusagen>1){
      $sUZ="\n".'1. '.KAL_TxZusageZeile.$sUZ;
      $sHZ="\n".'<div class="kalTbZl2"><div class="kalTbSp1"></div>'."\n".' <div class="kalTbSp2"><b>1. '.KAL_TxZusageZeile."</b></div>\n</div>".$sHZ;
     }
     $sUZ=trim('ID-'.sprintf('%04d',$nId--).': '.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10).$sUZ).($bWarteListe?' '.KAL_TxZusage7Status:''); $sUT=trim($sUT); if(!KAL_Zusagen){$sDv='ov'.'ers'; $sDv="\n\n".chr(68).'em'.$sDv.'io'.'n';}
     //E-Mail an Zusagende
     if(KAL_ZusageEintragMail){
      $nEnd=(KAL_ZusageNamentlich?$nZusagen:1); srand((double)microtime()*1000000);
      for($j=1;$j<=$nEnd;$j++){
       $sUZM='ID-'.sprintf('%04d',$nId+$j).substr($sUZ,strpos($sUZ,':')); $k=($j==1?1000:100*$j+1000);
       $sHZM="\n".'<div class="kalTbZl1">'."\n".' <div class="kalTbSp1"><b>ID-'.sprintf('%04d',$nId+$j)."</b></div>\n".' <div class="kalTbSp2">'.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10).($bWarteListe?' '.KAL_TxZusage7Status:'')."</div>\n</div>".$sHZ;
       $sHZM="\n".'<div class="kalTabl">'.$sHZM."\n</div>\n";
       $sHtml=str_replace("\r",'','<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite kalEMail">');
       $sCod=rand(100,255); $nS=9; $s=KAL_Schluessel.$sCod.($nId+$j); for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
       $sLkF=KAL_Url.'kalender.php?kal_Aktion=zusage_'.sprintf('%02X',$nS).sprintf('%02X',$sCod).($nId+$j);
       $sBtr=str_replace('#A',$sWww,KAL_TxZusageEintrBtr); $sTxt=KAL_TxZusageEintrMTx;
       for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',(isset($aW[$k+$i])?$aW[$k+$i]:''),$sTxt);
       $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
       $sHTx=str_replace('#A','<a href="'.$sLnk.'">'.str_replace('&amp;','&',$sLnk).'</a>',str_replace('#L','<a href="'.$sLkF.'">'.$sLkF.'</a>',$sHTx));
       $sHTx=str_replace('#D',trim(KAL_Zeichensatz<=0?$sHT:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sHT):html_entity_decode($sHT))),str_replace('#Z',trim($sHZM),$sHTx)); if($sDv) $sHTx.="\n<div>".$sDv.'</div>';
       $sHTx=str_replace("\r",'',$sHtml)."\n".str_replace(' style="width:100%"','',str_replace(' style="width:15%"','',$sHTx))."\n</body>\n</html>";
       require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
       if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
       $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
       $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if($aW[$k+8]){$Mailer->AddTo($aW[$k+8]); $Mailer->SetReplyTo($aW[$k+8]);}
       if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->HtmlText=$sHTx;
       $Mailer->PlainText=str_replace('#D',$sUT,str_replace('#Z',$sUZM,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('#L',$sLkF,str_replace('\n ',"\n",$sTxt)))));
       if(!$Mailer->Send()) if($aW[$k+8]) $Et2='</p><p class="kalFehl">'.fKalTx(KAL_TxSendeFehl);
      }
     }
     //Eintrag-Mail an Admin/Besitzer
     if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
     if(KAL_ZusageNeuInfoAdm||(KAL_ZusageNeuInfoAut&&!empty($sKontaktEml))){
      $sHZ="\n".'<div class="kalTbZl1">'."\n".' <div class="kalTbSp1"><b>ID-'.sprintf('%04d',$nId+1)."</b></div>\n".' <div class="kalTbSp2">'.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10).($bWarteListe?' '.KAL_TxZusage7Status:'')."</div>\n</div>".$sHZ;
      $sHZ="\n".'<div class="kalTabl">'.$sHZ."\n</div>\n";
      $sHtml=str_replace("\r",'','<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite">');
      $sBtr=str_replace('#A',$sWww,KAL_TxZusageInfoBtr); $sTxt=KAL_TxZusageInfoMTx;
      for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',(isset($aW[$k+$i])?$aW[$k+$i]:''),$sTxt);
      $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
      $sHTx=str_replace('#A','<a href="'.$sLnk.'">'.str_replace('&amp;','&',$sLnk).'</a>',$sHTx);
      $sHTx=str_replace('#D',trim(KAL_Zeichensatz<=0?$sHT:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sHT):html_entity_decode($sHT))),str_replace('#Z',trim($sHZ),$sHTx)); if($sDv) $sHTx.="\n<div>".$sDv.'</div>';
      $sHTx=str_replace("\r",'',$sHtml)."\n".str_replace(' style="width:100%"','',str_replace(' style="width:15%"','',$sHTx))."\n</body>\n</html>";
      require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if($aW[1008]) $Mailer->SetReplyTo($aW[1008]);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->HtmlText=$sHTx;
      $Mailer->PlainText=str_replace('#D',$sUT,str_replace('#Z',$sUZ,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
      if(KAL_ZusageNeuInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
      if(KAL_ZusageNeuInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
     }
     //Kapazitaets-Mail an Admin / Besitzer
     if($bKapMaximum&&(KAL_ZusageMaxKapInfoAdm||(KAL_ZusageMaxKapInfoAut&&!empty($sKontaktEml)))||$bKapGrenze&&(KAL_ZusageGrenzeInfoAdm||(KAL_ZusageGrenzeInfoAut&&!empty($sKontaktEml)))){
      $sBtr=str_replace('#A',$sWww,($bKapMaximum?KAL_TxZusageMaxKapBtr:KAL_TxZusageGrenzeBtr)); $sTxt=KAL_TxZusageMaxKapMTx;
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if($aW[1008]) $Mailer->SetReplyTo($aW[1008]);
      if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $sTxt=str_replace('#N',$nZusagenSumme,str_replace('#K',$nKapazitaet,$sTxt));
      $Mailer->Text=str_replace('#D',$sUT,str_replace('#Z',$sUZ,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
      if($bKapMaximum){
       if(KAL_ZusageMaxKapInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageMaxKapInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
      }elseif($bKapGrenze){
       if(KAL_ZusageGrenzeInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageGrenzeInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
      }
     }
     //Wartelisten Hochstufung benachrichtigen // ToDo
     if(KAL_ZusageVormerkErlaubt&&KAL_ZusageVmkInfoVorbei&&count($aWarteListeFrei)){
      $sBtr=str_replace('#A',$sWww,KAL_TxZusageVmkVorbeiBtr); $sTxt=KAL_TxZusageVmkVorbeiMTx;
      require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      foreach($aWarteListeFrei as $aV){
       $aV[8]=fKalDecode($aV[8]); for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',(isset($aV[$i])?$aV[$i]:''),$sTxt);
       $s=$aV[0]; $sUZf="\nID: ".$s;
       $sHZf="\n".'<div class="kalTbZl1">'."\n".' <div class="kalTbSp1">Zusagen-Nr.'."</div>\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
       $s=fKalAnzeigeDatum($aV[2]); $sUZf.="\nDATUM: ".$s;
       $sHZf.="\n".'<div class="kalTbZl2">'."\n".' <div class="kalTbSp1">Termindatum'."</div>\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
       for($i=3;$i<=$nZusageFelder;$i++) if($i!=6&&$i!=7){
        $s=$aV[$i]; if($i==5) $s=fKalAnzeigeDatum($s).substr($s,10); $sFN=$kal_ZusageFelder[$i]; if($sFN=='ANZAHL') if(!$sFN=KAL_ZusageNameAnzahl) $sFN=KAL_TxZusageAnzahl; if(--$nFarb<=0) $nFarb=2;
        $sUZf.="\n".strtoupper($sFN).': '.$s.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?'':' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'));
        $sHZf.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$sFN."</div>\n".' <div class="kalTbSp2">'.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?$s:str_replace('.',',',$s).'&nbsp;'.KAL_Waehrung)."</div>\n</div>";
       }
       $sHtml=str_replace("\r",'','<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite kalEMail">');
       $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
       $sHTx=str_replace('#A','<a href="'.$sLnk.'">'.str_replace('&amp;','&',$sLnk).'</a>',$sHTx);
       $sHTx=str_replace('#D',trim(KAL_Zeichensatz<=0?$sHT:(KAL_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//TRANSLIT',$sHT):html_entity_decode($sHT))),str_replace('#Z',"\n".'<div class="kalTabl">'.$sHZf."\n</div>\n",$sHTx));
       $sHTx=str_replace("\r",'',$sHtml)."\n".str_replace(' style="width:100%"','',str_replace(' style="width:15%"','',$sHTx))."\n</body>\n</html>";
       $Mailer->HtmlText=$sHTx;
       $Mailer->PlainText=str_replace('#D',$sUT,str_replace('#Z',$sUZf,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt))));
       if($aV[8]){$Mailer->AddTo($aV[8]); $Mailer->SetReplyTo($aV[8]);}
       $Mailer->Send(); $Mailer->ClearTo();
     }}
    }//bOK
   }elseif(!$Et) $Et=KAL_TxEingabeFehl;
  }else{//GET
   reset($_GET); foreach($_GET as $k=>$v) if(substr($k,0,4)=='kal_') $sHid.='<input type="hidden" name="'.$k.'" value="'.fKalRq($v).'">';
   if(strlen($sTermVeranst)>KAL_ZusageVeranstLaenge) $sTermVeranst=substr($sTermVeranst,0,KAL_ZusageVeranstLaenge).'...';
   $aW[1002]=fKalAnzeigeDatum($sTermDat); $aW[1003]=$sTermZeit; $aW[1004]=$sTermVeranst; $aW[1008]=$sNutzerEml;
   for($i=9;$i<=$nZusageFelder;$i++) if($j=$kal_ZusageQuellen[$i]){
    $t=substr($j,-1); $j=(int)substr($j,0,-1); $aW[1000+$i]=($t!='T'?(isset($aNtz[$j])?$aNtz[$j]:''):(isset($aT[$j])?$aT[$j]:''));
   }
   if(KAL_ZusageNamentlich){
    $nMaxNam=($nKapazitaet>0?min($nKapazitaet-$nZusagenSumme,KAL_ZusageMaxNamenMitKapaz):KAL_ZusageMaxNamenOhneKapaz);
    for($k=2;$k<=$nMaxNam;$k++){
     $j=100*$k+1000; for($i=2;$i<=$nZusageFelder;$i++){$aW[$j+$i]=''; $aOk[$j+$i]=true;}
     $aW[$j+2]=$aW[1002]; $aW[$j+3]=$aW[1003]; $aW[$j+4]=$aW[1004]; $aW[$j+8]=$aW[1008];
     for($i=9;$i<=$nZusageFelder;$i++) if(strpos($kal_ZusageQuellen[$i],'T')) $aW[$j+$i]=$aW[1000+$i];
    }
   }
   if(KAL_ZusageFrist>=0){//Frist beachten
    if($sFristPunkt==''){//Standardfrist
     if(KAL_ZusageBisEnde&&strlen($sFristEnde)>9) $sFristAnfang=substr($sFristEnde,0,10);
     if($sFristAnfang<date('Y-m-d',time()+86400*KAL_ZusageFrist)){$bDo=false; $bForm=false; $Et=KAL_TxZusageSperre;}
    }else{//Anmeldefrist gegeben
     if($sFristPunkt<date('Y-m-d H:i',time())){$bDo=false; $bForm=false; $Et=KAL_TxZusageSperre;}
    }
  }}//GET
 }else{$bDo=false; $bForm=false;}//sId

 if(!$Et){$Et=KAL_TxZusageMeld; $Es='Meld';} //Seitenausgabe
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).$Et2.'</p>'."\n";

 if($bForm){ //Formular erzeugen
 $aZusageFrmFeld=explode(';',KAL_ZusageFrmFeld); $n=count($aZusageFrmFeld); $aZFF=array();
 for($i=1;$i<$n;$i++) if($aZusageFrmFeld[$i]>0) $aZFF[$i]=$aZusageFrmFeld[$i]; asort($aZFF); reset($aZFF);
 $bCbLsch=((KAL_LoeschGastZusage&&!$bSesOK)||($bSesOK&&KAL_LoeschNutzerZusage));

 if(KAL_DSEPopUp&&(KAL_ZusageDSE1||KAL_ZusageDSE2)) $X.='<script>function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.KAL_DSEPopupW.',height='.KAL_DSEPopupH.',left='.KAL_DSEPopupX.',top='.KAL_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>'."\n";

 $sAjaxURL=KAL_Url; $bWww=(strtolower(substr(fKalHost(),0,4))=='www.');
 if($bWww&&!strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://','://www.',$sAjaxURL);
 elseif(!$bWww&&strpos($sAjaxURL,'://www.')) $sAjaxURL=str_replace('://www.','://',$sAjaxURL);

 if($bCaptcha) $X.="
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

 $X.="\n".'<form name="zusageForm" class="kalForm" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden).rtrim("\n".$sHid);
 $X.="\n".'<div class="kalTabl">'; $nCssStil=1;
 foreach($aZFF as $i=>$xx){
  $sFN=$kal_ZusageFelder[$i]; $sFT=$kal_ZusageFeldTyp[$i]; $sFS=''; $sFH=''; $sFOder=''; $bAnz=false;
  if($i==2) $sFH=' <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.fKalDatumsFormat().'</span>';
  elseif($i==3) $sFH=' <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.KAL_TxSymbUhr.'</span>';
  elseif($sFN=='ANZAHL'){ // Anzahl vorbereiten
   if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl; else $sFN=KAL_TxZusageAnzahl; $bAnz=true;
   if($sFH=($nKapazitaet>0?KAL_TxZusageKapazRest:KAL_TxZusageKapazNull)) $sFH=' '.fKalTx(str_replace('#R',max($nKapazitaet-$nZusagenSumme,0),str_replace('#Z',$nZusagenSumme,str_replace('#K',$nKapazitaet,$sFH))));
   if($nKapazitaet>0&&max($nKapazitaet-$nZusagenSumme,0)==0&&KAL_ZusageVormerkErlaubt){ // ueberbuchen
    $sFH.=' '.(KAL_ZusageVormerkTxtZeile?'<div>':'').fKalTx(KAL_TxZusageVmkErlaubt).(KAL_ZusageVormerkTxtZeile?'</div>':'');
   }
   if($bCbLsch){
    $sFH.='<div style="margin-top:2px;"><input class="kalCheck" type="checkbox" name="kal_zf0LschZusage" value="1"'.($bLschZusage?' checked="checked"':'').'> '.fKalTx(KAL_TxZusageLschFrueher).'</div>';
    $sFOder='<div style="margin-top:6px;">'.fKalTx(KAL_TxOder).'</div>'; $bCbLsch=false;
   }
  }
  if(!$bAnz||!KAL_ZusageSelectAnzahl){ //keine Anzahlbox
   if($sFT!='a'&&$sFT!='j'){ //keine Selectbox
    if(!$bAnz){ //nicht Anzahl
     if($sFT=='n'||$sFT=='d'||$sFT=='z') $sFS='style="width:7em;" ';
     elseif($sFT=='w'){
      $s=(float)$aW[1000+$i]; $nPreisId=1000+$i;
      $sFS='style="width:7em;" '; if(KAL_SperreZusagePreis&&$s>0) $sFS.='readonly="readonly" '; $sFH=' '.fKalTx(KAL_Waehrung);
      if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); else $s=''; $aW[1000+$i]=$s;
     }
    }else $sFS='style="width:7em;" '.(KAL_ZusageNamentlich?'onchange="fFormularZeigen(this.value)" ':(KAL_RechneZusagePreis?'onchange="fZusagenPreis(this.value)" ':''));
    $sFS='<input class="kalEing" type="text" name="kal_zf'.(1000+$i).'" '.$sFS.'value="'.fKalTx($aW[1000+$i]).'">';
   }else{ //Selectbox
    $sFS='<select class="kalEing kalAuto" style="min-width:7em" name="kal_zf'.(1000+$i).'" size="1"><option value="">---</option>';
    if($sFT=='a'){
     $aAww=explode('|',trim($kal_ZusageAuswahl[$i])); $nAww=count($aAww);
     for($j=0;$j<$nAww;$j++) $sFS.='<option value="'.fKalTx($aAww[$j]).($aW[1000+$i]==$aAww[$j]?'" selected="selected':'').'">'.fKalTx($aAww[$j]).'</option>';
    }elseif($sFT=='j') $sFS.='<option value="J'.($aW[1000+$i]=='J'?'" selected="selected':'').'">'.fKalTx(KAL_TxJa).'</option><option value="N'.($aW[1000+$i]=='N'?'" selected="selected':'').'">'.fKalTx(KAL_TxNein).'</option>';
    $sFS.='</select>';
   }
  }else{ //Anzahl als Box
   $k=(strlen($aW[1000+$i])?(int)$aW[1000+$i]:-199);
   $nMaxNam=($nKapazitaet>0?min($nKapazitaet-$nZusagenSumme,KAL_ZusageMaxNamenMitKapaz):($bKapLeer?KAL_ZusageMaxNamenOhneKapaz:0));
   $sFS='<select class="kalEing kalAuto" name="kal_zf'.(1000+$i).'"'.(KAL_ZusageNamentlich?' onchange="fFormularZeigen(this.value)"':(KAL_RechneZusagePreis?' onchange="fZusagenPreis(this.value)"':'')).'><option value="">--</option>';
   for($j=(KAL_ErlaubeZusageNull?0:1);$j<=$nMaxNam;$j++) $sFS.='<option value="'.$j.($j!=$k?'':'" selected="selected').'">'.$j.'</option>';
   if($nKapazitaet>0&&max($nKapazitaet-$nZusagenSumme,0)==0&&KAL_ZusageVormerkErlaubt){ // ueberbuchen
    for($j=1;$j<=KAL_ZusageMaxNamenOhneKapaz;$j++) $sFS.='<option value="'.$j.($j!=$k?'':'" selected="selected').'">'.$j.'</option>';
   }
   if($k<0&&$k>-199)$sFS.='<option value="'.$k.'" selected="selected">'.$k.'</option>';
   $sFS.='</select>';
  }
  $X.="\n".'<div class="kalTbZl'.$nCssStil.'"><div class="kalTbSp1">'.fKalTx($sFN).($kal_ZusagePflicht[$i]?'*':'').$sFOder.'</div><div class="kalTbSp2"><div class="kal'.($aOk[1000+$i]?'Eing':'Fhlt').'">'.$sFS.$sFH.'</div></div></div>';
  if(--$nCssStil<=0) $nCssStil=2; // if(--$nFarb<=0) $nFarb=2;
 }
 if($bCbLsch){
  $X.="\n".'<div class="kalTbZl'.$nCssStil.'"><div class="kalTbSp1">'.fKalTx(KAL_TxLoeschen).'</div><div class="kalTbSp2"><input class="kalCheck" type="checkbox" name="kal_zf0LschZusage" value="1"'.($bLschZusage?' checked="checked"':'').'> '.fKalTx(KAL_TxZusageLschFrueher).'</div></div>'; // if(--$nFarb<=0) $nFarb=2;
  if(--$nCssStil<=0) $nCssStil=2;
 }
 $X.="\n".'<div class="kalTbZl'.$nCssStil.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2 kalTbSpR"><span class="kalMini">* '.fKalTx(KAL_TxPflicht).'</span>&nbsp;</div></div>';
 if(--$nCssStil<=0) $nCssStil=2; // if(--$nFarb<=0) $nFarb=2;

 if(KAL_ZusageNamentlich){//weitere Eingabebloecke
  $nMaxNam=($nKapazitaet>0?min($nKapazitaet-$nZusagenSumme,KAL_ZusageMaxNamenMitKapaz):KAL_ZusageMaxNamenOhneKapaz);
  for($k=2;$k<=$nMaxNam;$k++){
   if($k>$nZusagen) $sDS='none'; else $sDS='table-row';
   $X.="\n".'<div class="kalZs'.sprintf('%02d',$k).' kalTbZl'.$nCssStil.'" style="display:'.$sDS.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2">'.$k.'. '.fKalTx(KAL_TxZusageZeile).'</div></div>';
   reset($aZFF); $j=100*$k+1000; if(--$nCssStil<=0) $nCssStil=2;
   foreach($aZFF as $i=>$xx){
    $sFN=$kal_ZusageFelder[$i]; $sFT=$kal_ZusageFeldTyp[$i]; $sFS=''; $sFH='';
    if($i==2){$sFS='style="width:7em;" '; $sFH=' <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.fKalDatumsFormat().'</span>';} elseif($i==3){$sFS='style="width:7em;" '; $sFH=' <span class="kalMini">'.fKalTx(KAL_TxFormat).' '.KAL_TxSymbUhr.'</span>';}
    if($sFT!='a'&&$sFT!='j'){ //Textfeld
     if($sFT=='n') $sFS='style="width:7em;" ';
     elseif($sFT=='w'){
      $s=(float)(isset($aW[$j+$i])?$aW[$j+$i]:0);
      $sFS='style="width:7em;" '; if(KAL_SperreZusagePreis&&$s>0) $sFS.='readonly="readonly" '; $sFH=' '.fKalTx(KAL_Waehrung);
      if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); else $s=''; $aW[$j+$i]=$s;
     }
     $sFS='<input class="kalEing" '.$sFS.'type="text" name="kal_zf'.($j+$i).'" value="'.(isset($aW[$j+$i])?fKalTx($aW[$j+$i]):'').'">';
    }else{ //Selectbox
     $sFS='<select class="kalEing kalAuto" style="min-width:7em" name="kal_zf'.($j+$i).'" size="1"><option value="">---</option>';
     if($sFT=='a'){
      $aAww=explode('|',trim($kal_ZusageAuswahl[$i])); $nAww=count($aAww);
      for($z=0;$z<$nAww;$z++) $sFS.='<option value="'.fKalTx($aAww[$z]).(isset($aW[$j+$i])&&$aW[$j+$i]==$aAww[$z]?'" selected="selected':'').'">'.fKalTx($aAww[$z]).'</option>';
     }elseif($sFT=='j') $sFS.='<option value="J'.(isset($aW[$j+$i])&&$aW[$j+$i]=='J'?'" selected="selected':'').'">'.fKalTx(KAL_TxJa).'</option><option value="N'.(isset($aW[$j+$i])&&$aW[$j+$i]=='N'?'" selected="selected':'').'">'.fKalTx(KAL_TxNein).'</option>';
     $sFS.='</select>';
    }
    if($sFN!='ANZAHL') $X.="\n".'<div class="kalZs'.sprintf('%02d',$k).' kalTbZl'.$nCssStil.'" style="display:'.$sDS.'"><div class="kalTbSp1">'.fKalTx($sFN).($kal_ZusagePflicht[$i]?'*':'').'</div><div class="kalTbSp2"><div class="kal'.(isset($aOk[$j+$i])&&$aOk[$j+$i]?'Eing':'Fhlt').'">'.$sFS.$sFH.'</div></div></div>';
    if(--$nCssStil<=0) $nCssStil=2;
   }
   $X.="\n".'<div class="kalZs'.sprintf('%02d',$k).' kalTbZl'.$nCssStil.'" style="display:'.$sDS.'"><div class="kalTbSp1">&nbsp;</div><div class="kalTbSp2 kalTbSpR"><span class="kalMini">* '.fKalTx(KAL_TxPflicht).'</span>&nbsp;</div></div>';
   if(--$nCssStil<=0) $nCssStil=2;
  }
 }
 $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1">'.fKalTx(KAL_TxDetail).'</div><div class="kalTbSp2">'.$sHT.'</div></div>'; if(--$nFarb<=0) $nFarb=2;

 if(KAL_ZusageDSE1) $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE1?'Fhlt':'Eing').'">'.fKalDSEFld(1,$bDSE1).'</div></div></div>';
 if(KAL_ZusageDSE2) $X.="\n".'<div class="kalTbZl'.$nFarb.'"><div class="kalTbSp1 kalTbSpR">*</div><div class="kalTbSp2"><div class="kal'.($bErrDSE2?'Fhlt':'Eing').'">'.fKalDSEFld(2,$bDSE2).'</div></div></div>';
 if(KAL_ZusageDSE1||KAL_ZusageDSE2) if(--$nFarb<=0) $nFarb=2;
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

 }//bForm

 $X.="
<script>
function fFormularZeigen(nZusagen){
 if(isNaN(nZusagen)) var nZusagen=1;
 var nRowNr; var aRows=document.forms['zusageForm'].getElementsByTagName('div');
 for(var i=aRows.length-1;i>0;i--) if(aRows[i].className.substr(0,5)=='kalZs'){
  nRowNr=parseInt(aRows[i].className.substr(5,2),10);
  if(nRowNr<=nZusagen&&aRows[i].style.display!='table-row') aRows[i].style.display='table-row';
  else if(nRowNr>nZusagen&&aRows[i].style.display!='none') aRows[i].style.display='none';
 }
}
function fZusagenPreis(nZusagen){
 var nPreis=".$nPreis.";
 if(".$nPreisId.">0&&nPreis>0) document.zusageForm.kal_zf".$nPreisId.".value=((nZusagen*nPreis).toFixed(".KAL_Dezimalstellen.")).replace(/\./,'".KAL_Dezimalzeichen."');
}
</script>\n";

 if(KAL_ZusageFormMitListe&&($bSesOK||KAL_GastZusageFormMitL)){include(KAL_Pfad.'kalZusageZeigen.php'); $X.=fKalZeigeZusagen();}

 return $X;
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