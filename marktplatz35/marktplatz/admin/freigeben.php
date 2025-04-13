<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('vorgemerkte Inserate freigeben','<script type="text/javascript">
 function fSelAll(bStat,nSgA){
  formName="Inserate"+nSgA+"Liste";
  for(var i=0;i<self.document.forms[formName].length;++i)
   if(self.document.forms[formName].elements[i].type=="checkbox") self.document.forms[formName].elements[i].checked=bStat;
 }
</script>','IIf');

$aStru=array(); $aS=explode(';',MP_Segmente);
$nSgZhl=substr_count(MP_Segmente,';'); $nSegO=$nSegNo; $bAngezeigt=false;
for($nSgA=1;$nSgA<=$nSgZhl;$nSgA++) if(AM_VormerkAlle&&!empty($aS[$nSgA])&&$aS[$nSgA]!='LEER'||$nSgA==$nSegO){
 $nSegNo=$nSgA; $sSegNo=sprintf('%02d',$nSegNo); $sSegNam=$aS[$nSegNo];

 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aLF=array(); $aSS=array(); $aDF=array(); $aND=array(); $Meld=''; $MTyp='Fehl';
 $aEF=array(); $aNE=array(); $aET=array(); $aAW=array(); $aKW=array(); $aSW=array(); $bLschNun=false; $sLschFrg=''; $bOK=false;

 if(!MP_SQL){//Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
   $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
   if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

 if($_SERVER['REQUEST_METHOD']=='POST'&&$nSgA==(int)$_GET['seg']){ //Vorschlaege loeschen
  $aId=array();
  foreach($_POST as $k=>$xx) if(substr($k,3,2)=='CB') $aId[(int)substr($k,5)]=true; //Loeschnummern
  if(count($aId)>0){
   if(isset($_POST['mpLsch'.$nSgA])&&$_POST['mpLsch'.$nSgA]=='1'){
    if(!MP_SQL){ //Textdatei
     $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
     for($i=1;$i<$nSaetze;$i++){ //loeschen
      $s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';'));
      if(isset($aId[$n])&&$aId[$n]) $aD[$i]='';
     }
     if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){
      fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);
      $bOK=true; $Meld='Die markierten Inserate im Segment-'.$nSgA.' wurden gelöscht.'; $MTyp='Meld';
     }else $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
    }elseif($DbO){ //bei SQL
     $s=''; foreach($aId as $k=>$xx) $s.=' OR nr='.$k;
     if($DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE '.substr($s,4))){
      $bOK=true; $Meld='Die markierten Inserate im Segment-'.$nSgA.' wurden gelöscht.'; $MTyp='Meld';
     }else $Meld=MP_TxSqlFrage;
    }
    if((in_array('b',$aFT)||in_array('f',$aFT))&&$bOK){ //Bilder und Dateien
     if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){$aD=array();
      while($s=readdir($f)) if($i=(int)$s){
       if(MP_BldTrennen){if(isset($aId[$i])) $aD[]=$s;}
       elseif(substr($i,-2)==$sSegNo) if(isset($aId[(int)substr($i,0,-2)])) $aD[]=$s;
      }
      closedir($f); foreach($aD as $s) @unlink(MP_Pfad.MP_Bilder.$sBldDir.$s);
    }}//Bilder
   }else{$Meld='Wollen Sie die markierten Inserate im Segment-'.$nSgA.' wirklich löschen?'; $sLschFrg='1';}
  }else{$Meld=MP_TxKeineAenderung; $MTyp='Meld';}
 }

 if(isset($_GET['mp_Frei'])&&$_GET['mp_Frei']&&$nSgA==(int)$_GET['seg']){ //Inserate freigeben
  if($sId=(isset($_GET['mp_Num'])?$_GET['mp_Num']:'')){
   $nIdAbs=abs((int)$sId); $aId=array(); $aZ=array(); $bOK=false; $sMTx=strtoupper(MP_TxNummer).': '.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nIdAbs);
   $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*MP_HalteAltesNochTage)) $sRefDat='';
   if(!MP_SQL){ //Textdaten
    $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD); $sZ=''; $aTmp=array();
    $nMxId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nMxId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
    for($i=1;$i<$nSaetze;$i++){
     $s=rtrim($aD[$i]); $p=strpos($s,';'); $nANr=(int)substr($s,0,$p); $nMxId=max($nMxId,$nANr);
     if(abs($nANr)!=$nIdAbs){//keine freizuschaltender
      if(substr($s,$p+3,10)>=$sRefDat&&(int)substr($s,0,1)>0) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
      else $aLsch[(int)(substr($s,0,$p).$sSegNo)]=true;
     }elseif(MP_Direktaendern!=2&&$nANr>0||MP_Direktaendern==2&&($nANr<0||!strpos(str_replace("\r",'',implode('',$aD)),"\n-".$nANr))){
      $aZ=explode(';',$s); array_splice($aZ,1,1); $sId=abs((int)$sId); if($aZ[1]<date('Y-m-d')) $aZ[1]=date('Y-m-d');
      for($j=2;$j<$nFelder;$j++){
       $sZ.=';'.$aZ[$j]; if($aFT[$j]=='b'||$aFT[$j]=='f') $aObj[$j]=$aZ[$j];
      }
     }
    }
    if(count($aZ)){
     $sId=$nIdAbs; $aTmp[$sId.';1']=$aZ[1].$sZ; //Inserat anhaengen
     $aD=array(); $s='Nummer_'.max($nMxId,(int)$sId).';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v.NL;
     if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){//neu schreiben
      fwrite($f,rtrim(implode('',$aD)).NL); fclose($f); $bOK=true;
     }else $Meld=str_replace('#','<i>'.MP_Daten.$sSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
    }else $Meld='Inserat Nummer '.$sId.' nicht gefunden!';
   }elseif($DbO){//SQL
    if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr='.$sId)){
     $aZ=$rR->fetch_row(); $rR->close(); array_splice($aZ,1,1); if($aZ[1]<date('Y-m-d')) $aZ[1]=date('Y-m-d');
     if($DbO->query('UPDATE IGNORE '.str_replace('%',$sSegNo,MP_SqlTabI).' SET online="1",mp_1="'.$aZ[1].'" WHERE nr='.$sId)){
      if($DbO->affected_rows){
       $bOK=true;
       if((int)$sId<0){
        $DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$nIdAbs.'"');
        $DbO->query('UPDATE IGNORE '.str_replace('%',$sSegNo,MP_SqlTabI).' SET nr="'.$nIdAbs.'" WHERE nr='.$sId);
        $sId=$nIdAbs;
       }
       if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"')){
        while($a=$rR->fetch_row()) $aLsch[(int)$a[0]]=true; $rR->close();
        $DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"');
       }
      }else $Meld=MP_TxKeineAenderung;
     }else $Meld=MP_TxSqlAendr;
    }else $Meld='Inserat Nummer '.$sId.' nicht gefunden!';
   }//SQL
   if($bOK){//eingetragen
    $Meld='Der Inseratevorschlag im Segment-'.$nSgA.' wurde veröffentlicht.'; $MTyp='Erfo'; $sMTo=''; $aN=NULL;
    if(count($aLsch)>0&&(in_array('b',$aFT)||in_array('f',$aFT))){ //veraltete Bilder und Dateien
     if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){$aD=array();
      while($s=readdir($f)) if($i=(int)$s){
       if(MP_BldTrennen){if(isset($aId[$i])) $aD[]=$s;}
       elseif(substr($i,-2)==$sSegNo) if(isset($aId[(int)substr($i,0,-2)])) $aD[]=$s;
      }
      closedir($f); foreach($aD as $s) @unlink(MP_Pfad.MP_Bilder.$sBldDir.$s);
    }}
    for($i=1;$i<$nFelder;$i++){
     if(isset($aZ[$i])) if($aFT[$i]=='d'){if($aZ[$i]) $aZ[$i]=fMpAnzeigeDatum($aZ[$i]);}
     elseif($aFT[$i]=='b'){if($p=strpos($aZ[$i],'|')) $aZ[$i]=substr($aZ[$i],1+$p);}
     elseif($aFT[$i]=='w'||$aFT[$i]=='n'||$aFT[$i]=='1'||$aFT[$i]=='2'||$aFT[$i]=='3'||$aFT[$i]=='r') $aZ[$i]=str_replace('.',MP_Dezimalzeichen,$aZ[$i]);
     elseif(($aFT[$i]=='e'||$aFT[$i]=='c')&&!MP_SQL) $aZ[$i]=fMpDeCode($aZ[$i]);
     elseif($aFT[$i]=='p') $aZ[$i]=fMpDeCode($aZ[$i]);
     elseif($aFT[$i]=='@'){$aZ[$i]=trim(fMpAnzeigeDatum($aZ[$i]).strstr($aZ[$i],' '));}
    }

    $sNutzerEml=''; $sNutzerAutor=''; $sNutzerMailing=''; $sNutzerBenachr=''; $p=1;
    if((MP_FreischaltMail||MP_MailListeFreischalt||1==2)&&($p=array_search('u',$aFT))&&$aZ[$p]>'0000'){ //Benutzerdaten suchen
     $nNId=(int)$aZ[$p];
     if(!MP_SQL){
      $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
      for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
       $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1); $sNutzerEml=fMpDeCode($aN[4]); $sNutzerAutor=fMpDeCode($aN[2]);
       if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld]))
        $sNutzerMailing=MP_NutzerMailListeFeld<5&&MP_NutzerMailListeFeld>1?fMpDeCode($aN[MP_NutzerMailListeFeld]):$aN[MP_NutzerMailListeFeld];
       if(MP_NutzerBenachrFeld&&isset($aN[MP_NutzerBenachrFeld]))
        $sNutzerBenachr=MP_NutzerBenachrFeld<5&&MP_NutzerBenachrFeld>1?fMpDeCode($aN[MP_NutzerBenachrFeld]):$aN[MP_NutzerBenachrFeld];
       break;
     }}elseif($DbO){ //SQL
      if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nNId.'"')){
       if($rR->num_rows>0){
        $aN=$rR->fetch_row(); array_splice($aN,1,1); $sNutzerEml=$aN[4]; $sNutzerAutor=$aN[2];
        if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld])) $sNutzerMailing=$aN[MP_NutzerMailListeFeld];
        if(MP_NutzerBenachrFeld&&isset($aN[MP_NutzerBenachrFeld])) $sNutzerBenachr=$aN[MP_NutzerBenachrFeld];
       }$rR->close();
    }}}elseif($p>0&&$aZ[$p]=='0000'){$sNutzerAutor=MP_TxAutor0000; $sNutzerMailing=MP_TxAutor0000; $sNutzerBenachr=MP_TxAutor0000;}
    $aFA=(isset($nNId)&&$nNId>0)?$aNE:$aEF; $aFL=MP_MailListeNDetail?$aND:$aDF; $aFB=MP_BenachrNDetail?$aND:$aDF;
    $sMlTxA=strtoupper(MP_TxNummer).': '.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nIdAbs); $sMlLs=$sMlTxA; $sMlTxB=$sMlTxA; $sAutorMlTo=$sNutzerEml;
    for($i=1;$i<$nFelder;$i++){$t=$aFT[$i];
     if($aFA[$i]) $sMlTxA.="\n".strtoupper($aFN[$i]).': '.trim(str_replace('`,',';',str_replace('\n ',"\n",$aZ[$i])).($t!='u'?'':' '.$sNutzerAutor));
     if($aFL[$i]&&$t!='c'&&$t!='e'&&$t!='p') $sMlLs.="\n".strtoupper($aFN[$i]).': '.trim(str_replace('`,',';',str_replace('\n ',"\n",$aZ[$i])).($t!='u'?'':' '.$sNutzerMailing));
     if($aFB[$i]&&$t!='c'&&$t!='e'&&$t!='p') $sMlTxB.="\n".strtoupper($aFN[$i]).': '.trim(str_replace('`,',';',str_replace('\n ',"\n",$aZ[$i])).($t!='u'?'':' '.$sNutzerBenachr));
     if(MP_FreischaltMail){
      if($t=='c'&&$aZ[$i]) $sAutorMlTo=$aZ[$i]; //Kontakt
      elseif($t=='e'&&($sAutorMlTo==''||($aZ[$i]&&!in_array('c',$aFT)))) $sAutorMlTo=$aZ[$i]; //e-Mail
      elseif($t=='l'&& $sAutorMlTo==''&&strpos($aZ[$i],'@')>0&&!(in_array('e',$aFT))) $sAutorMlTo=$aZ[$i]; //Link
     }
    }
    if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
    if(MP_FreischaltMail&&$sAutorMlTo!=''){ //Freischaltmail
     require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sAutorMlTo);
     if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
     $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),MP_TxFreischaltBtr)));
     if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
     $Mailer->Text=str_replace('#D',$sMlTxA,str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),str_replace('\n ',"\n",MP_TxFreischaltTxt)))));
     $Mailer->Send();
    }
    if(MP_MailListeFreischalt&&MP_MailListeAdr!=''){//Mailingliste
     require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailListeAdr);
     if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
     $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),MP_TxMailListeBtr)));
     $s=MP_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetReplyTo($s,$t);
     if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
     $Mailer->Text=str_replace('#D',$sMlLs,str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),str_replace('\n ',"\n",MP_TxMailListeTxt)))));
     $Mailer->Send();
    }
    if(1==2){//Benachrichtigungen

    }
   }
  }else $Meld='Fehlerhafter Seitenaufruf ohne Inseratenummer!';
 }

 $aD=array(); $aSpalten=array(); $nSpalten=0; //Daten bereitstellen
 for($i=0;$i<$nFelder;$i++) $aSpalten[$aLF[$i]]=$i;
 $aSpalten[0]=0; $nSpalten=count($aSpalten); $aTmp=array(); $aIdx=array();
 if(!MP_SQL){ //Textdaten
  $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$i]));
   if($a[1]=='2'){
    $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',$i); array_splice($a,1,1);
    for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace('\n ',NL,str_replace('`,',';',$a[$aSpalten[$j]]));
   }
  }$aD=array();
 }elseif($DbO){ //SQL
   $t=''; for($j=1;$j<$nSpalten;$j++) $t.=',mp_'.$aSpalten[$j]; $i=0;
   if($rR=$DbO->query('SELECT nr'.$t.' FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE online="2" ORDER BY nr')){
    while($a=$rR->fetch_row()){
     $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $aIdx[$sId]=sprintf('%0'.MP_NummerStellen.'d',++$i);
     for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]);
    }$rR->close();
   }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlDaBnk;
 if(AM_Rueckwaerts) arsort($aIdx); reset($aIdx);
 foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i];

 //Ausgabe Segment
 if(count($aD)>0){
  if(!$Meld){$Meld='Bearbeiten Sie die vorliegenden Inseratevorschläge im Segment-'.$nSgA.' <i>'.$sSegNam.'</i>.'; $MTyp='Meld';}
  echo '<p class="adm'.$MTyp.'" style="margin-top:12px;">'.$Meld.'</p>'.NL; $bAngezeigt=true;
?>


<form name="Inserate<?php echo $nSgA?>Liste" action="freigeben.php?seg=<?php echo $nSegNo?>" method="post">
<table class="admTabl"  border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td width="1%">&nbsp;</td>'.NL.' <td width="1%">&nbsp;</td>';
 for($j=1;$j<$nSpalten;$j++){
  $sStil=''; $t=$aFT[$aSpalten[$j]];
  if(($t=='b'&&AM_BildVorschau)||($t=='f'&&AM_DateiSymbol)||($t=='s'&&AM_SymbSymbol)||(($t=='l'||$t=='e'||$t=='c')&&AM_LinkSymbol)||$t=='z'||$t=='j'||$t=='v') $sStil=' style="text-align:center"';
  elseif($t=='w'||$t=='n'||$t=='r'||$t=='1'||$t=='2'||$t=='3') $sStil=' style="text-align:right"';
  echo NL.' <td'.$sStil.'><b>'.$aFN[$aSpalten[$j]].'</b></td>';
 }echo NL.'</tr>';
 $bAendern=file_exists('aendern.php'); $bKopiere=file_exists('kopieren.php');
 foreach($aD as $a){ //Datenzeilen ausgeben
  $sId=$a[0];
  echo NL.'<tr class="admTabl">';
  echo NL.' <td valign="top" align="right" style="white-space:nowrap;">'.$sId.'&nbsp;<input class="admCheck" type="checkbox" name="mp_CB'.$sId.'" value="1"'.(isset($aId[$sId])&&$aId[$sId]?' checked="checked"':'').' /></td>';
  echo NL.' <td valign="top">'.($bAendern?'<a href="aendern.php?seg='.$nSegNo.'&mp_Num='.$sId.'&mp_Vmk=1"><img src="iconAendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  echo NL.' <td valign="top"><a href="freigeben.php?seg='.$nSegNo.'&mp_Num='.$sId.'&mp_Frei=1"><img src="iconFreigabe.gif" width="12" height="13" border="0" title="Freischalten"></a></td>';
  for($j=1;$j<$nSpalten;$j++){
   $k=$aSpalten[$j]; $t=$aFT[$k]; $sStil='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'm': $s=fMpBB($s); break; // Text/Memo
     case 'a': case 'k': case 'o': case 'u': break; // so lassen
     case 'd': case '@':  // Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      break;
     case 'z': $sStil.='text-align:center;'; break; // Uhrzeit
     case 'w': // Währung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sStil.='text-align:right;';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
      if($s=='J'||$s=='Y') $s=MP_TxJa; elseif($s=='N') $s=MP_TxNein; $sStil.='text-align:center;';
      break;
     case 'n': case '1': case '2': case '3': case 'r': // Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sStil.='text-align:right;';
      break;
     case 'l':
      $aI=explode('|',$s); $s=$aI[0]; $v=(isset($aI[1])?$aI[1]:$s);
      if(AM_LinkSymbol){$v='<img src="'.MPPFAD.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';}
      $s='<a href="'.(strpos($s,'@')?'mailto:':(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://')).$s.'" target="_blank">'.$v.'</a>';
      break;
     case 'e': case 'c':
      if(!MP_SQL) $s=fMpDeCode($s);
      if(AM_LinkSymbol){
       $v='<img src="'.MPPFAD.'grafik/mail.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';
      }else $v=$s;
      $s='<a href="mailto:'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 's': $w=$s;
      if(AM_SymbSymbol){
       $p=array_search($s,$aSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
       $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
       $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.$w.'" />'; $sStil.='text-align:center;';
      }
      break;
     case 'b':
      if(AM_BildVorschau){
       $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
       $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.substr($s,strpos($s,'/')+1).'" />'; $sStil.='text-align:center;';
      }else $s=fMpKurzName(substr($s,strpos($s,'|')+1));
      break;
     case 'f':
      if(AM_DateiSymbol){
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); //Datei
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil.='text-align:center;';
       $v='<img src="'.MPPFAD.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.MP_TxDatei.'" />';
      }else $v=fMpKurzName($s);
      $s='<a href="'.MPPFAD.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'">'.$v.'</a>';
      break;
     case 'x': break;
     case 'p': $s=str_repeat('*',strlen($s)/2); break;
    }
   }else $s='&nbsp;';
   if(($w=$aSS[$k])||$sStil) $sStil=' style="'.$sStil.$w.'"';
   echo NL.' <td valign="top"'.$sStil.'>'.$s.'</td>';
  }
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td align="right">
  <input type="image" src="iconLoeschen.gif" width="12" height="13" border="0" title="markierte Inserate löschen" />&nbsp;<input class="admCheck" type="checkbox" name="mp_All" value="1" onClick="fSelAll(this.checked,<?php echo $nSgA?>)" />
 </td>
 <td colspan="<?php echo $nSpalten+1?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="mpLsch<?php echo $nSgA?>" value="<?php echo $sLschFrg?>" />
</form>

<?php
 }else if(!empty($Meld)) echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;; //Ausgabe Segment
}//alle Segmente
if(!$bAngezeigt) echo '<p class="admMeld" style="text-align:center;margin:32px;">Es sind keine vorgemerkten Inserate zum Freigeben vorrätig.</p>'.NL;

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aLF,$aSS,$aDF,$aND,$aEF,$aNE,$aET,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aLF=explode(';',rtrim($aStru[2])); $aLF[0]=substr($aLF[0],14,1); //$aLF[1]=1;
 $aSS=explode(';',rtrim($aStru[6])); $aSS[0]='';
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]='1';
 $aND=explode(';',rtrim($aStru[8])); $aND[0]='1';
 $aEF=explode(';',rtrim($aStru[11])); $aEF[0]='1';
 $aNE=explode(';',rtrim($aStru[12])); $aNE[0]='1';
 $aET=explode(';',rtrim($aStru[15])); $aET[0]='';  //$aET[1]='';
 $aAW=explode(';',rtrim($aStru[16])); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}

function fMpKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fMpBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.$o.'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="mpText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="mpText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="mpText"><li class="mpText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="mpText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>