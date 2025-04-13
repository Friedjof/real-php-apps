<?php
$nEinKlickTId=''; $bOK=true; $nZusageAnzahlPos=0; $nZusagenSumme=0; $nKapazitaet=0; $bKapMaximum=false; $bKapGrenze=false;
if($nTId=(isset($_GET['kal_KlickZusage'])?(int)$_GET['kal_KlickZusage']:0)){
 $nNId=(int)substr($sSes,(strlen($sSes)>12?17:0),4);  $nZId=0;
 if(!KAL_SQL){//
  $aZ=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $n=count($aZ);
  for($i=1;$i<$n;$i++){
   $a=explode(';',$aZ[$i],9);
   if(count($a)>6&&$a[1]==$nTId&&$a[7]==$nNId){$nZId=$i; $a=explode(';',rtrim($aZ[$i])); $a[8]=fKalDeCode($a[8]); break;}
  }
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.((int)$nTId).'" AND benutzer="'.$nNId.'"')){
   if($a=$rR->fetch_row()) $nZId=$a[0]; $rR->close();
  }
 }
 if($nZId<=0){//noch nicht zugesagt
  if(!KAL_SQL){//Termin holen
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $n=count($aD); $s=$nTId.';'; $l=strlen($s);
   for($i=1;$i<$n;$i++) if(substr($aD[$i],0,$l)==$s){//Termin gefunden
    $aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); break;
  }}elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.((int)$nTId).'"')){
    if($aT=$rR->fetch_row()) array_splice($aT,1,1); $rR->close();
  }}
  if(isset($_GET['kal_Klick2Zusage'])&&$_GET['kal_Klick2Zusage']==$nTId){//zusagen
   if(!KAL_SQL){//
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $n=count($aD); $s=$nNId.';'; $l=strlen($s);
    for($i=1;$i<$n;$i++) if(substr($aD[$i],0,$l)==$s){//Nutzer gefunden
     $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $aN[2]=fKalDeCode($aN[2]); $aN[4]=fKalDeCode($aN[4]); break;
   }}elseif($DbO){//SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'"')){
     if($aN=$rR->fetch_row()) array_splice($aN,1,1); $rR->close();
   }}
   if(isset($aT)&&count($aT)>2){ //Termin geholt
    if(KAL_ZusageFrist>=0){ //Frist beachten
     if(($i=array_search('ZUSAGE_BIS',$kal_FeldName))&&($sFristPunkt=$aT[$i])){//Anmeldefrist gegeben
      if($sFristPunkt<date('Y-m-d H:i',time())) $bOK=false;
     }else{//Standardfrist
      $sFristAnfang=$aT[1]; $sFristEnde='';
      if(KAL_ZusageBisEnde&&KAL_EndeDatum) for($i=count($aT);$i>1;$i--) if($kal_FeldType[$i]=='d') $sFristEnde=$aT[$i];
      if(strlen($sFristEnde)>9) $sFristAnfang=substr($sFristEnde,0,10);
      if($sFristAnfang<date('Y-m-d',time()+86400*KAL_ZusageFrist)) $bOK=false;
    }}
    if($bOK){ //kein Fristproblem
     if(KAL_Pruefe1KlickKapaz&&($i=array_search('KAPAZITAET',$kal_FeldName))&&(($nKapazitaet=(int)$aT[$i])||substr($aT[$i],0,1)==='0')){ //Kapazitaet pruefen
      $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageAnzahlPos=array_search('ANZAHL',$kal_ZusageFelder);
      if($nZusageAnzahlPos>0){ //Zusagesumme holen
       $nKapGrenze=$nKapazitaet; if(strpos($aT[$i],'(')){$a=explode('(',$aT[$i]); $nKapGrenze=(int)$a[1];}
       if(!KAL_SQL){
        $nSaetze=count($aZ); $s=';'.$nTId.';'; $l=strlen($s);
        for($i=1;$i<$nSaetze;$i++){
         $sZ=rtrim($aZ[$i]); $p=strpos($sZ,';');
         if(substr($sZ,$p,$l)==$s){
          $a=explode(';',$sZ,$nZusageAnzahlPos+2);
          $nZusagenSumme+=($a[6]>='0'?abs($a[$nZusageAnzahlPos]):-abs($a[$nZusageAnzahlPos]));
         }
        }
       }elseif($DbO){
        if($rR=$DbO->query('SELECT nr,aktiv,dat_'.$nZusageAnzahlPos.' FROM '.KAL_SqlTabZ.' WHERE termin="'.$nTId.'"')){
         while($a=$rR->fetch_row()) $nZusagenSumme+=($a[1]>='0'?abs($a[2]):-abs($a[2])); $rR->close();
       }}
       if($nZusagenSumme>=$nKapazitaet) $bOK=false;
     }}
     if($bOK){ //kein Kapazitaetsproblem
      $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $kal_ZusageQuellen=explode(';',KAL_ZusageQuellen); $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); $bOK=false; $sUZ=''; $sHZ='';
      $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $sZusageZeit=date('Y-m-d H:i',time());
      $sTermDat=(isset($aT[KAL_TerminDatumFeld])?substr($aT[KAL_TerminDatumFeld],0,10):'2000-01-01');
      $sTermZeit=(isset($aT[KAL_TerminZeitFeld])?$aT[KAL_TerminZeitFeld]:'');
      $sTermVeranst=(isset($aT[KAL_TerminVeranstFeld])?@strip_tags(fKalBB(str_replace('\n ',' ',str_replace('`,',';',$aT[KAL_TerminVeranstFeld])))):'');
      if(strlen($sTermVeranst)>KAL_ZusageVeranstLaenge) $sTermVeranst=substr($sTermVeranst,0,KAL_ZusageVeranstLaenge).'...';
      $sUZ= "\n".strtoupper($kal_ZusageFelder[1]).': '.$nTId;
      $sUZ.="\n".strtoupper($kal_ZusageFelder[2]).': '.fKalAnzeigeDatum($sTermDat);
      if($sTermZeit) $sUZ.="\n".strtoupper($kal_ZusageFelder[3]).': '.$sTermZeit;
      $sUZ.="\n".strtoupper($kal_ZusageFelder[4]).': '.$sTermVeranst;
      $sUZ.="\n".strtoupper($kal_ZusageFelder[8]).': '.$aN[4]; $nFarb=1;
      $sHZ.="\n".'<div class="kalTbZl1">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[1]."</div>\n".' <div class="kalTbSp2">'.$nTId."</div>\n</div>";
      $sHZ.="\n".'<div class="kalTbZl2">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[2]."</div>\n".' <div class="kalTbSp2">'.fKalAnzeigeDatum($sTermDat)."</div>\n</div>";
      if($sTermZeit){$sHZ.="\n".'<div class="kalTbZl1">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[3]."</div>\n".' <div class="kalTbSp2">'.$sTermZeit."</div>\n</div>"; $nFarb=2;}
      $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[4]."</div>\n".' <div class="kalTbSp2">'.$sTermVeranst."</div>\n</div>"; if(--$nFarb<=0) $nFarb=2;
      $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[8]."</div>\n".' <div class="kalTbSp2">'.$aN[4]."</div>\n</div>"; if(--$nFarb<=0) $nFarb=2;
      if(!KAL_SQL){//
       $nSaetze=count($aZ); $s=rtrim($aZ[0]);
       if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
       else for($i=1;$i<$nSaetze;$i++){$s=substr($aZ[$i],0,12); $nId=max((int)substr($s,0,strpos($s,';')),$nId);}
       $s='Nummer_'.(++$nId); for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.str_replace(';','`,',$kal_ZusageFelder[$i]); $aZ[0]=$s."\n";
       $s =$nId.';'.$nTId.';'.$sTermDat.';'.$sTermZeit.';'.str_replace(';','`,',$sTermVeranst).';';
       $s.=$sZusageZeit.';'.(KAL_DirektZusage==1?'1':'0').';'.$nNId.';'.fKalEnCode($aN[4]);
       for($i=9;$i<=$nZusageFelder;$i++) if($kal_ZusageFelder[$i]){$t='';
        if($j=$kal_ZusageQuellen[$i]){$t=substr($j,-1); $j=(int)substr($j,0,-1); $t=($t!='T'?(isset($aN[$j])?$aN[$j]:''):(isset($aT[$j])?$aT[$j]:''));}
        elseif($kal_ZusageFelder[$i]=='ANZAHL'){$t='1'; if(!$kal_ZusageFelder[$i]=KAL_ZusageNameAnzahl) $kal_ZusageFelder[$i]='Anzahl';}
        $s.=';'.$t; $aW[$i]=$t;
        if($t) $sUZ.="\n".strtoupper($kal_ZusageFelder[$i]).': '.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?$t:number_format((float)$t,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'));
        $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[$i]."</div>\n".' <div class="kalTbSp2">'.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?$t:number_format((float)$t,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').'&nbsp;'.KAL_Waehrung)."</div>\n</div>";
        if(--$nFarb<=0) $nFarb=2;
       }
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ)))."\n".trim($s)."\n"); fclose($f); $bOK=true;
       }else{$Et=str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte);}
      }elseif($DbO){//SQL
       $sF='termin,datum,zeit,veranstaltung,buchung,aktiv,benutzer,email';
       $sV='"'.$nTId.'","'.$sTermDat.'","'.$sTermZeit.'","'.$sTermVeranst.'","'.$sZusageZeit.'","'.(KAL_DirektZusage==1?'1':'0').'","'.$nNId.'","'.$aN[4].'"';
       for($i=9;$i<=$nZusageFelder;$i++){$t='';
        if($j=$kal_ZusageQuellen[$i]){$t=substr($j,-1); $j=(int)substr($j,0,-1); $t=($t!='T'?(isset($aN[$j])?$aN[$j]:''):(isset($aT[$j])?$aT[$j]:''));}
        elseif($kal_ZusageFelder[$i]=='ANZAHL'){$t='1'; if(!$kal_ZusageFelder[$i]=KAL_ZusageNameAnzahl) $kal_ZusageFelder[$i]='Anzahl';}
        $sV.=',"'.str_replace('"','\"',$t).'"'; $sF.=',dat_'.$i; $aW[$i]=$t;
        if($t) $sUZ.="\n".strtoupper($kal_ZusageFelder[$i]).': '.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?$t:number_format((float)$t,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').' '.KAL_Waehrung);
        $sHZ.="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1">'.$kal_ZusageFelder[$i]."</div>\n".' <div class="kalTbSp2">'.($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?$t:number_format((float)$t,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').'&nbsp;'.KAL_Waehrung)."</div>\n</div>";
        if(--$nFarb<=0) $nFarb=2;
       }
       if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' ('.$sF.') VALUES('.$sV.')')){
        if($nId=$DbO->insert_id) $bOK=true; else $Et=KAL_TxSqlEinfg;
       }else $Et=KAL_TxSqlEinfg;
      }
      if($bOK){ //eingetragen
       $Et=KAL_TxZusageEintr; $Es='Erfo'; $sDv='';
       $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$nTId;
       if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk; if(!KAL_Zusagen){$sDv='ov'.'ers'; $sDv="\n\n".chr(68).'em'.$sDv.'io'.'n';}
       $sUZ=trim('ID-'.sprintf('%04d',$nId).': '.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10).$sUZ); $sWww=fKalHost();
       $sHZ="\n".'<div class="kalTbZl'.$nFarb.'">'."\n".' <div class="kalTbSp1"><b>ID-'.sprintf('%04d',$nId+$j).'</b></div>'."\n".' <div class="kalTbSp2">'.fKalAnzeigeDatum($sZusageZeit).substr($sZusageZeit,10)."</div>\n</div>".$sHZ;
       $sHZ="\n".'<div class="kalTabl">'.$sHZ."\n</div>\n";
       $a=fKalTerminText($aT,$DbO); $sUT=trim($a[0]); $sHT=$a[1]; $sKontaktEml=$a[2];
       if(KAL_ZusageEintragMail){ //E-Mail an Zusagenden
        $sHtml=str_replace("\r",'','<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite kalEMail">');
        srand((double)microtime()*1000000); $sCod=rand(100,255); $nS=9;
        $s=KAL_Schluessel.$sCod.$nId; for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
        $sLkF=KAL_Url.'kalender.php?kal_Aktion=zusage_'.sprintf('%02X',$nS).sprintf('%02X',$sCod).$nId;
        $sBtr=str_replace('#A',$sWww,KAL_TxZusageEintrBtr); $sTxt=KAL_TxZusageEintrMTx;
        for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$kal_ZusageFelder[$i].'}',(isset($aW[$i])?$aW[$i]:$kal_ZusageFelder[$i]),$sTxt);
        $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
        $sHTx=str_replace('#A','<a href="'.$sLnk.'">'.str_replace('&amp;','&',$sLnk).'</a>',str_replace('#L','<a href="'.$sLkF.'">'.$sLkF.'</a>',$sHTx));
        $sHTx=str_replace('#D',trim($sHT),str_replace('#Z',trim($sHZ),$sHTx)); if($sDv) $sHTx.="\n<div>".$sDv.'</div>';
        $sHTx=str_replace("\r",'',$sHtml)."\n".$sHTx."\n</body>\n</html>";
        require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
        if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
        $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
        $Mailer->AddTo($aN[4]); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aN[4]);
        if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->HtmlText=$sHTx;
        $Mailer->PlainText=str_replace('#D',$sUT,str_replace('#Z',$sUZ,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('#L',$sLkF,str_replace('\n ',"\n",$sTxt))))).$sDv;
        if(!$Mailer->Send()) $Et2='</p><p class="kalFehl">'.fKalTx(KAL_TxSendeFehl);
       }
       if(KAL_ZusageNeuInfoAdm||(KAL_ZusageNeuInfoAut&&!empty($sKontaktEml))){ //E-Mail an Admin / Besitzer
        $sHtml=str_replace("\r",'','<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.KAL_Url.'kalStyles.css">
</head>
<body class="kalSeite">');
        $sBtr=str_replace('#A',$sWww,KAL_TxZusageInfoBtr); $sTxt=KAL_TxZusageInfoMTx;
        $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
        $sHTx=str_replace('#A','<a href="'.$sLnk.'">'.str_replace('&amp;','&',$sLnk).'</a>',$sHTx);
        $sHTx=str_replace('#D',trim($sHT),str_replace('#Z',trim($sHZ),$sHTx)); if($sDv) $sHTx.="\n<div>".$sDv.'</div>';
        $sHTx=str_replace("\r",'',$sHtml)."\n".str_replace(' style="width:100%"','',str_replace(' style="width:15%"','',$sHTx))."\n</body>\n</html>";
        require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
        if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
        $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
        $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aN[4]);
        if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->HtmlText=$sHTx;
        $Mailer->PlainText=str_replace('#D',$sUT,str_replace('#Z',$sUZ,str_replace('#A',str_replace('&amp;','&',$sLnk),str_replace('\n ',"\n",$sTxt)))).$sDv;
        if(KAL_ZusageNeuInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
        if(KAL_ZusageNeuInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
       }
       if($nKapazitaet>0){ //Limits pruefen
        if(($nZusagenSumme++)<$nKapazitaet&&$nZusagenSumme>=$nKapazitaet) $bKapMaximum=true; elseif($nZusagenSumme==$nKapGrenze) $bKapGrenze=true;
       }
       if($bKapMaximum&&(KAL_ZusageMaxKapInfoAdm||(KAL_ZusageMaxKapInfoAut&&!empty($sKontaktEml)))||$bKapGrenze&&(KAL_ZusageGrenzeInfoAdm||(KAL_ZusageGrenzeInfoAut&&!empty($sKontaktEml)))){ //Kapazitaets-Mail an Admin / Besitzer
        $sBtr=str_replace('#A',$sWww,($bKapMaximum?KAL_TxZusageMaxKapBtr:KAL_TxZusageGrenzeBtr)); $sTxt=KAL_TxZusageMaxKapMTx;
        require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
        if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
        $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
        $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aN[4]);
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
      }//bOK
     }else $Et=KAL_TxZusageKapazEnde;
    }else $Et=KAL_TxZusageSperre;
   }else $Et=str_replace('#',$nTId,KAL_TxKeinDatensatz);
  }else{//nachfragen
   $Et=str_replace('#T',(isset($aT[1])?fKalAnzeigeDatum($aT[1]):$nTId),KAL_TxZEinKlickEFrage); $Es='Meld'; $nEinKlickTId=$nTId;
  }
 }elseif(KAL_LoeschKlickZusage){//jetzt loeschen
  if(isset($_GET['kal_Klick2Zusage'])&&$_GET['kal_Klick2Zusage']==$nTId){ $bOK=false;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $n=count($aD); $s=$nTId.';'; $l=strlen($s);
    for($i=1;$i<$n;$i++) if(substr($aD[$i],0,$l)==$s){//Termin gefunden
     $aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); break;
    }
    if(isset($aT)&&count($aT)>2){ //Termin geholt
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
      $aZ[$nZId]=''; fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ)))."\n"); fclose($f); $bOK=true;
     }else $Et=str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte);
    }else $Et=str_replace('#',$nTId,KAL_TxKeinDatensatz);
   }elseif($DbO){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$nTId.'"')){
     if($aT=$rR->fetch_row()) array_splice($aT,1,1); $rR->close();
    }
    if(isset($aT)&&count($aT)>2){ //Termin geholt
     if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$nZId.'" AND benutzer="'.$nNId.'"')){
      if($DbO->affected_rows>0) $bOK=true; else $Et=KAL_TxSqlAendr;
     }else $Et=KAL_TxSqlAendr;
    }else $Et=str_replace('#',$nTId,KAL_TxKeinDatensatz);
   }
   if($bOK){ //geloescht
    $Et=str_replace('#Z',fKalAnzeigeDatum($a[2]).rtrim(' '.$a[3]),KAL_TxZEinKlickLoesch); $Es='Erfo';
    if(KAL_ZusageLschInfoAut||KAL_ZusageLschInfoAdm){ //EMail
     $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $sReplyTo=$a[8];
     $sUZ=trim(fKalZusagePlainText($a,$kal_ZusageFelder)); $a=fKalTerminText($aT); $sUT=trim($a[0]); $sKontaktEml=$a[2];
     $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='; //+Id
     if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
     $sBtr=str_replace('#A',fKalHost(),KAL_TxZusageLschBtr); $sTxt=KAL_TxZusageLschMTx;
     require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sReplyTo);
     if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
     $Mailer->Text=str_replace('#D',$sUT,str_replace('#Z',$sUZ,str_replace('#A',str_replace('&amp;','&',$sLnk).$aT[0],str_replace('\n ',"\n",$sTxt))));
     if(KAL_ZusageLschInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
     if(KAL_ZusageLschInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();}
    }
   }
  }else{//nachfragen
   $Et=str_replace('#Z',fKalAnzeigeDatum($a[2]).rtrim(' '.$a[3]),KAL_TxZEinKlickLFrage); $nEinKlickTId=$nTId;
  }
 }else{$Et=KAL_TxNzUnveraendert; $Es='Meld';} //keine Aenderung
}

function fKalEnCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}

function fKalZusagePlainText($aZ,$kal_ZusageFelder){
 $nZusageFelder=count($kal_ZusageFelder); $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 $sZ='ID-'.sprintf('%04d',$aZ[0]).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
 $sZ.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[2])).': '.fKalAnzeigeDatum($aZ[2]);
 for($i=3;$i<$nZusageFelder;$i++){
  if($i!=5&&$i!=6&&($s=(isset($aZ[$i])?trim($aZ[$i]):''))){
   $sFN=str_replace('`,',';',$kal_ZusageFelder[$i]);
   if($sFN=='ANZAHL') if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl;
   $sZ.="\n".strtoupper($sFN).': '.($i!=7?($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?str_replace('`,',';',$s):number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR')):sprintf('%04d',$s));
  }
 }
 return $sZ;
}

function fKalTerminText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sH="\n".'<div class="kalTabl">';
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 $sH.="\n".'<div class="kalTbZl1">'; $nFarb=2;
 $sH.="\n".' <div class="kalTbSp1">'.$kal_FeldName[0].'</div>';
 $sH.="\n".' <div class="kalTbSp2">'.$aT[0]."</div>\n</div>";
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
   if($u=$s){
    switch($t){
     case 't': $s=fKalBB($s); $u=@strip_tags($s); break; //Text
     case 'a': case 'k': case 'o': break;
     case 'm': if(KAL_InfoMitMemo){$s=fKalBB($s); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
     case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
      }else if($w) $u.=' '.$w;
      $s=str_replace(' ','&nbsp;',$u);
      break;
     case 'z': $u=$s.' '.KAL_TxUhr; $s.=' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      $s=(float)$s;
      if($s>0||!KAL_PreisLeer){
       $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); $u=$s;
       if(KAL_Waehrung){$u=$s.' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR'); $s.='&nbsp;'.KAL_Waehrung;}
      }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y'){$s=KAL_TxJa; $u=KAL_TxJa;}elseif($s=='N'){$s=KAL_TxNein; $u=KAL_TxNein;}
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $u=$s;
      break;
     case 'e': //E-Mai
      if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
      $s=''; $u=''; break;
     case 'l': //Link
      $aL=explode('||',$s); $s=''; $z='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w); $z.=$w.', ';
       $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'"> ';
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
      }$s=substr($s,0,-2); $u=substr($z,0,-2); break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$aT[0].'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $u=KAL_Url.$s; $w=substr($s,strpos($s,'-')+1,-4);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'">';
      break;
     case 'f': //Datei
      $u=KAL_Url.KAL_Bilder.$aT[0].'~'.$s; $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$aT[0].'~'.$s.'" target="_blank">'.$s.'</a>'; break;
      break;
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
      }else $s=KAL_TxAutor0000; $u=$s;
      break;
     default: {$s=''; $u='';}
    }//switch
   }
   if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}}
   elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(strlen($s)>0){
    $sH.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $sH.="\n".' <div class="kalTbSp1">'.$sFN.'</div>';
    $sH.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
   }
   if(strlen($u)>0) $sT.="\n".strtoupper($sFN).': '.$u;
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
 $sH.="\n</div>\n";
 if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
 return array($sT,$sH,$sKontaktEml);
}
?>