<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Termine löschen','<script language="JavaScript" type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.TerminListe.length;++i)
   if(self.document.TerminListe.elements[i].type=="checkbox") self.document.TerminListe.elements[i].checked=bStat;
 }
</script>
<link rel="stylesheet" type="text/css" href="'.KALPFAD.'kalStyles.css">','TTL');

//Vorschlag loeschen
$sLschFrg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $nFelder=count($kal_FeldName); $aId=array(); $sLschFrg=''; $bOK=false;
 foreach($_POST as $k=>$xx) if(substr($k,4,1)=='L') $aId[(int)substr($k,5)]=true; //Loeschnummern
 if(count($aId)){
  if($_POST['kalLsch']=='1'){
   $aT=array(); $aZ=array();
   if(!KAL_SQL){ //Textdatei
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
    for($i=1;$i<$nSaetze;$i++){ //loeschen
     $s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';'));
     if(isset($aId[$n])&&$aId[$n]||$n<=0){$a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $aT[(int)$a[0]]=$a; $aD[$i]='';}
    }
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $bOK=true; $Msg='<p class="admMeld">'.KAL_TxLoescheErfo.'</p>';
     if(KAL_ListenErinn>0||KAL_DetailErinn>0){// Erinnerungsliste kuerzen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD); $b=false;
      for($i=1;$i<$nSaetze;$i++){
       $s=substr($aD[$i],11,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aId[$n])&&$aId[$n]){$aD[$i]=''; $b=true;}
      }
      if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     }}
     if(KAL_ListenBenachr>0||KAL_DetailBenachr>0){//Benachrichtigungsliste kuerzen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD); $b=false;
      for($i=1;$i<$nSaetze;$i++){
       $s=substr($aD[$i],0,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aId[$n])&&$aId[$n]){$aD[$i]=''; $b=true;}
      }
      if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     }}
     if(KAL_ZusageSystem){//Zusagenliste kuerzen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $b=false;
      for($i=1;$i<$nSaetze;$i++){
       $s=substr($aD[$i],0,20); $n=(int)substr($s,1+strpos($s,';'));
       if(isset($aId[$n])&&$aId[$n]){$a=explode(';',rtrim($aD[$i])); $a[8]=fKalDecode($a[8]); $aZ[]=$a; $aD[$i]=''; $b=true;}
      }
      if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    }}}else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
   }elseif($DbO){ //bei SQL
    $sE=''; foreach($aId as $k=>$xx) $sE.=' OR id='.$k; $sE=substr($sE,3);
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE '.$sE)){
     while($a=$rR->fetch_row()){array_splice($a,1,1); $aT[(int)$a[0]]=$a;} $rR->close();
    }
    if($DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE '.$sE)){
     $bOK=true; $Msg='<p class="admMeld">'.KAL_TxLoescheErfo.'</p>'; $sE=str_replace(' id=',' termin=',$sE);
     if(KAL_ListenErinn>0||KAL_DetailErinn>0) $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE '.$sE);
     if(KAL_ListenBenachr>0||KAL_DetailBenachr>0) $DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE '.$sE);
     if(KAL_ZusageSystem){
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE '.$sE)){
       while($a=$rR->fetch_row()) $aZ[]=$a; $rR->close();
      }
      $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.$sE);
     }
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }//SQL
   if((in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))&&$bOK){ //Bilder und Dateien
    if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
     $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aId[$i])&&$aId[$i]) $aD[]=$s; closedir($f);
     foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
   }}
   if(count($aZ)&&KAL_ZusageLschInfoAut&&KAL_ZusageLschNzZusag){
    $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $nAnzahlPos=0;
    require_once(KAL_Pfad.'class.plainmail.php'); $sKontaktEml=''; $sWww=fKalWww();
    $sLnk=(KAL_ZusageLink==''?$sHttp.'kalender.php?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
    if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk='http://'.$sWww.$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
    foreach($aZ as $a){
     if((strpos(KAL_TxZusageLschMTx,'#D')>0)&&count($aT)>0){ //Termindaten aufbereiten
      $aD=$aT[$a[1]]; $aD=fKalTerminPlainText($aD,$DbO); $sTDat=$aD[0]; $sKontaktEml=$aD[1];
     }
     $sMTx=str_replace('#D',$sTDat,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
     $sZDat='ID-NUMMER: '.sprintf('%04d',$a[0])."\nTERMIN-NR: ".sprintf('%04d',$a[1]); //Zusagedaten
     for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){ //Zusagedaten aufbereiten
      if($i==2) $a[2]=fKalAnzeigeDatum($a[2]); elseif($i==5) $a[5]=fKalAnzeigeDatum(substr($a[5],0,10)).substr($a[5],10);
      $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.str_replace('`,',';',$a[$i]);
     }
     $Mailer=new PlainMail();
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
     $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageLschBtr);
     $Mailer->Text=str_replace('#Z',trim($sZDat),str_replace('#A',$sLnk.$a[1],$sMTx));
     if(KAL_ZusageLschInfoAut&&$sKontaktEml>''){ //Mail an Terminautor
      $Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();
     }
     if(KAL_ZusageLschNzZusag){ //Mail an Zusagenden
      $Mailer->AddTo($a[8]); $Mailer->SetReplyTo($a[8]); $Mailer->Send();
   }}}
  }else{$sLschFrg='1'; $Msg='<p class="admFehl">'.KAL_TxLoescheFrag.'</p>';}
 }else $Msg='<p class="admMeld">'.KAL_TxKeineAenderung.'</p>';
}

//Termin freigeben
if(isset($_GET['kal_Frei'])&&$_GET['kal_Frei']){
 if($sId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'')){
  $nIdAbs=abs((int)$sId); $nFelder=count($kal_FeldName); $nDtPos2=0; $OId=0;
  for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='d'&&$nDtPos2<2) $nDtPos2=$i;
  $aIds=array(); $aObj=array(); $aZ=array(); $bOK=false; $sMTx=strtoupper(KAL_TxNummer).': '.$sId;
  $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*KAL_HalteAltesNochTage)) $sRefDat='';
  if(!KAL_SQL){ //Textdaten
   if(is_writable(KAL_Pfad.KAL_Daten.KAL_Termine)){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $sZ=''; $sPC=''; $aTmp=array(); $nDtPos2++;
    $nMxId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nMxId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
    for($i=1;$i<$nSaetze;$i++){
     $s=rtrim($aD[$i]); $p=strpos($s,';'); $nANr=(int)substr($s,0,$p); $nMxId=max($nMxId,$nANr);
     if(abs($nANr)!=$nIdAbs){//keine freizuschaltender
      if(substr($s,$p+3,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
      elseif(KAL_EndeDatum&&($nDtPos2>2)){
       $aZl=explode(';',$s,$nDtPos2+2);
       if(substr($aZl[$nDtPos2],0,10)>=$sRefDat) $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
       else $aLsch[(int)substr($s,0,$p)]=true;
      }else $aLsch[(int)substr($s,0,$p)]=true;
     }elseif(KAL_Direktaendern!=2&&$nANr>0||KAL_Direktaendern==2&&($nANr<0||!strpos(str_replace("\r",'',implode('',$aD)),"\n-".$nANr))){
      $aZ=explode(';',$s); array_splice($aZ,1,1); $sId=abs((int)$sId);
      for($j=2;$j<$nFelder;$j++){
       $sZ.=';'.$aZ[$j]; if($kal_FeldType[$j]=='b'||$kal_FeldType[$j]=='f') $aObj[$j]=$aZ[$j];
      }
     }
    }
    if(count($aZ)){
     $sId=$nIdAbs; $aTmp[$sId.';1']=$aZ[1].$sZ; $OId=$sId; $sPC=(isset($aZ[$nFelder])?$aZ[$nFelder]:''); //Termin anhaengen
     if($sPC) if($aWdhDat=fKalWdhDat(substr($aZ[1],0,10),$sPC)){ //Wiederholungen
      reset($aWdhDat); foreach($aWdhDat as $v){$aTmp[(++$nMxId).';1']=$v.$sZ; $aIds[]=$nMxId;}
     }
     $aD=array(); $s='Nummer_'.$nMxId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $aD[0]=$s.';Periodik'.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v){$aD[]=$k.';'.$v.NL;}
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){//neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bOK=true;
      if(KAL_ListenErinn>0||KAL_DetailErinn>0){// Erinnerungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],11,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      }}
      if(KAL_ListenBenachr>0||KAL_DetailBenachr>0){//Benachrichtigungsliste kuerzen
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD); $b=false;
       for($i=1;$i<$nSaetze;$i++){
        $s=substr($aD[$i],0,8); $n=(int)substr($s,0,strpos($s,';')); if(isset($aLsch[$n])&&$aLsch[$n]){$aD[$i]=''; $b=true;}
       }
       if($b) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Benachr,'w')){
        fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      }}
     }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
    }else $Msg='<p class="admFehl">Termin Nummer-'.$sId.' nicht gefunden!</p>';
   }else $Msg='<p class="admFehl">Die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';
  }elseif($DbO){ //SQL
   if($DbO->query('UPDATE IGNORE '.KAL_SqlTabT.' SET online="1" WHERE id='.$sId)){
    if($DbO->affected_rows){
     $bOK=true; $sF=''; for($i=1;$i<$nFelder;$i++) $sF.='kal_'.$i.','; $sF.='periodik'; $OId=$sId;
     if((int)$sId<0){
      $DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE id="'.$nIdAbs.'"');
      $DbO->query('UPDATE IGNORE '.KAL_SqlTabT.' SET id="'.$nIdAbs.'" WHERE id='.$sId);
      $sId=$nIdAbs;
     }
     if($rR=$DbO->query('SELECT id,'.$sF.' FROM '.KAL_SqlTabT.' WHERE id='.$sId)){
      $aZ=$rR->fetch_row(); $rR->close(); $sPC=$aZ[$nFelder]; $sZ='",';
      if($sPC) if($aWdhDat=fKalWdhDat(substr($aZ[1],0,10),$sPC)){
       for($i=2;$i<$nFelder;$i++){$sZ.='"'.$aZ[$i].'",'; if($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f') $aObj[$i]=$aZ[$i];}
       foreach($aWdhDat as $v) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online,'.$sF.') VALUES("1","'.$v.$sZ.'"")')){
        if($sId=$DbO->insert_id) $aIds[]=$sId;
      }}
      $sDtFld2=''; if(KAL_EndeDatum&&($nDtPos2>1)) $sDtFld2=' AND kal_'.$nDtPos2.'<"'.$sRefDat.'"'; $sE='';
      if($rR=$DbO->query('SELECT id FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2)){
       while($a=$rR->fetch_row()){$aLsch[(int)$a[0]]=true; $sE.=' OR termin="'.$a[0].'"';} $rR->close();
       $DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE kal_1<"'.$sRefDat.'"'.$sDtFld2);
       if($sE){$sE=substr($sE,4);
        if(KAL_ListenErinn>0||KAL_DetailErinn>0) $DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE '.$sE);
        if(KAL_ListenBenachr>0||KAL_DetailBenachr>0) $DbO->query('DELETE FROM '.KAL_SqlTabB.' WHERE '.$sE);
        if(KAL_ZusageSystem) $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.$sE);
      }}
     }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
    }else $Msg='<p class="admMeld">'.KAL_TxKeineAenderung.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
  }
  if($bOK){ //Daten gespeichert
   $Msg='<p class="admErfo">Der Termin wurde wieder veröffentlicht und nicht gelöscht!</p>'; $sMTo=''; $aN=NULL;
   if(count($aIds)) for($i=2;$i<$nFelder;$i++){ //Bilder und Dateien kopieren
    if($sONa=(isset($aObj[$i])?$aObj[$i]:'')){
     if($kal_FeldType[$i]=='b'){
      $p=strpos($sONa,'|'); $sONa=substr($sONa,0,$p);
      reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$OId.'-'.$sONa,KAL_Pfad.KAL_Bilder.$j.'-'.$sONa)) $bOK=false;
      $sONa=substr($aObj[$i],$p+1);
      reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$OId.'_'.$sONa,KAL_Pfad.KAL_Bilder.$j.'_'.$sONa)) $bOK=false;
     }elseif($kal_FeldType[$i]=='f'){
      reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$OId.'~'.$sONa,KAL_Pfad.KAL_Bilder.$j.'~'.$sONa)) $bOK=false;
     }
     if(!$bOK) $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Bilder.$sONa.'</i>',KAL_TxDateiRechte).'</p>'; $bOK=true;
   }}
   for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='d') $aZ[$i]=fKalAnzeigeDatum($aZ[$i]);
   $aDF=(KAL_BenachrNDetail?$kal_NDetailFeld:$kal_DetailFeld);
   if(KAL_FreischaltMail){ //Freischaltmail
    if(($i=array_search('u',$kal_FeldType))&&$aZ[$i]>'0000'){
     if(!KAL_SQL){
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=((int)$aZ[$i]).';'; $p=strlen($s);
      for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$s){
       $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); $sMTo=fKalDeCode($aN[4]);
       break;
     }}elseif($DbO){ //SQL
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$aZ[$i].'"')){
       $aN=$rR->fetch_row(); $rR->close(); array_splice($aN,1,1); $sMTo=$aN[4];
    }}}
    $sBtr=KAL_TxFreischaltBtr; $sMTx=strtoupper(KAL_TxNummer).': '.$sId; $sWww=fKalWww();
    for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]){
     $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i]; if($t=='d'&&$aZ[$i]=='..') $aZ[$i]='';
     if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
     if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     $sMTx.=NL.strtoupper($sFN).': '.(($t!='c'&&$t!='e'||KAL_SQL)&&$t!='p'?str_replace('\n ',NL,$aZ[$i]):fKalDeCode($aZ[$i]));
     if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',fKalPlainText($aZ[$i],$kal_FeldType[$i],true),$sBtr);
     if($t=='c'&&$aZ[$i]) $sMTo=(KAL_SQL?$aZ[$i]:fKalDeCode($aZ[$i])); //Kontakt
     elseif($t=='e'&&($sMTo==''||($aZ[$i]&&!in_array('c',$kal_FeldType)))) $sMTo=(KAL_SQL?$aZ[$i]:fKalDeCode($aZ[$i]));
     elseif($t=='l'&& $sMTo==''&&strpos($aZ[$i],'@')>0&&!(in_array('e',$kal_FeldType))) $sMTo=$aZ[$i];
    }elseif($kal_FeldType[$i]=='u'){
     $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.$aZ[$i];
     if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',$aZ[$i],$sBtr);
    }
    $sBtr=str_replace('#',$sWww, str_replace('#A',$sWww,str_replace('#N',$sId,$sBtr)));
    $sMTx=str_replace('#D',$sMTx,str_replace('#A',$sWww,str_replace('#N',$sId,str_replace('\n ',"\n",KAL_TxFreischaltTxt))));
    require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $sS=KAL_Sender; if($p=strpos($sS,'<')){$sF=substr($sS,0,$p); $sS=substr(substr($sS,0,-1),$p+1);} else $sF='';
    $Mailer->AddTo($sMTo); $Mailer->Subject=$sBtr; $Mailer->SetFrom($sS,$sF); $Mailer->SetReplyTo($sMTo);
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx;
    if($sMTo!='') $Mailer->Send();
   }
   if(KAL_MailListeFreischalt&&KAL_MailListeAdr!=''){ //Mailingliste
    $sBtr=KAL_TxMailListeBtr; $sMTx=strtoupper(KAL_TxNummer).': '.$sId; $sWww=fKalWww();
    for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]&&$kal_FeldType[$i]!='c'&&$kal_FeldType[$i]!='e'&&$kal_FeldType[$i]!='p'||$kal_FeldType[$i]=='u'){
     if($kal_FeldType[$i]!='u'){
      $sFN=$kal_FeldName[$i];
      if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
      if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
      $sMTx.=NL.strtoupper($sFN).': '.fKalPlainText($aZ[$i],$kal_FeldType[$i],true);
      if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',fKalPlainText($aZ[$i],$kal_FeldType[$i],true),$sBtr);
     }elseif($aZ[$i]>'0000'){
      $s=KAL_TxAutorUnbekannt; if(!$k=$kal_NDetailFeld[$i]) if(!$k=KAL_NNutzerListFeld) if(!$k=$kal_DetailFeld[$i]) $k=KAL_NutzerListFeld;
      if($k>1){
       if(isset($aN)&&is_array($aN)){ //Nutzer schon geholt
        if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt; elseif(!KAL_SQL&&$k<5&&$k>1) $s=fKalDeCode($s);
       }elseif(!KAL_SQL){ //Nutzerdaten holen
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=((int)$aZ[$i]).';'; $p=strlen($s);
        for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$s){
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt; elseif($k<5&&$k>1) $s=fKalDeCode($s);
         break;
       }}elseif($DbO){ //SQL
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$aZ[$i].'"')){
         $aN=$rR->fetch_row(); $rR->close(); array_splice($aN,1,1); if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt;
      }}}
      $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.$s;
      if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtr);
     }else $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.KAL_TxAutor0000;
    }
    $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,$sBtr));
    $sMTx=str_replace('#D',$sMTx,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxMailListeTxt)));
    require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $sS=KAL_Sender; if($p=strpos($sS,'<')){$sF=substr($sS,0,$p); $sS=substr(substr($sS,0,-1),$p+1);} else $sF='';
    $Mailer->AddTo(KAL_MailListeAdr); $Mailer->Subject=$sBtr; $Mailer->SetFrom($sS,$sF);
    $s=KAL_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetReplyTo($s,$t);
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
   }
   if(KAL_ListenBenachr>=0||KAL_DetailBenachr>=0){ $aM=array(); //Aenderungsbenachrichtigungen
    if(!KAL_SQL){ //ohne SQL
     $aD=@file(KAL_Pfad.KAL_Daten.KAL_Benachr); $aD[0]='#Termin;eMail'."\n"; $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aM[]=rtrim(substr($aD[$i],$p)); $aD[$i]='';}
    }elseif($DbO){ //mit SQL
     if($rR=$DbO->query('SELECT id,email FROM '.KAL_SqlTabB.' WHERE termin="'.$sId.'"')){
      while($a=$rR->fetch_row()) $aM[]=$a[1]; $rR->close();
    }}
    if(count($aM)>0){
     $sBtr=KAL_TxBenachrSendBtr; $sMTx=strtoupper(KAL_TxNummer).': '.$sId; $sWww=fKalWww();
     for($i=1;$i<$nFelder;$i++) if($aDF[$i]>0&&(KAL_ZeigeLeeres||!empty($aZ[$i]))&&($kal_FeldType[$i]!='m'||KAL_BenachrMitMemo)&&$kal_FeldType[$i]!='c'&&$kal_FeldType[$i]!='e'&&$kal_FeldType[$i]!='p'){
      if($kal_FeldType[$i]!='u'){
       $sFN=$kal_FeldName[$i];
       if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
       if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
       $sMTx.=NL.strtoupper($sFN).': '.fKalPlainText($aZ[$i],$kal_FeldType[$i]);
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',fKalPlainText($aZ[$i],$kal_FeldType[$i]),$sBtr);
      }elseif($aZ[$i]>'0000'){
       $s=$aZ[$i];
       if(KAL_NutzerBenachrFeld>0){
        if(isset($aN)&&is_array($aN)){ //Nutzer schon geholt
         if(!$s=$aN[KAL_NutzerBenachrFeld]) $s=KAL_TxAutorUnbekannt; elseif(!KAL_SQL&&KAL_NutzerBenachrFeld<5&&KAL_NutzerBenachrFeld>1) $s=fKalDeCode($s);
        }elseif(!KAL_SQL){ //Nuterdaten holen
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=((int)$aZ[$i]).';'; $p=strlen($s);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$s){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(!$s=$aN[KAL_NutzerBenachrFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerBenachrFeld<5&&KAL_NutzerBenachrFeld>1) $s=fKalDeCode($s);
          break;
        }}elseif($DbO){ //SQL
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$aZ[$i].'"')){
          $aN=$rR->fetch_row(); $rR->close(); array_splice($aN,1,1); if(!$s=$aN[KAL_NutzerBenachrFeld]) $s=KAL_TxAutorUnbekannt;
       }}}
       $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.$s;
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtr);
      }else{
       $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.KAL_TxAutor0000;
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',KAL_TxAutor0000,$sBtr);
      }
     }
     require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail(); $sWww=fKalWww();
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
     $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#',$sWww,str_replace('#A',$sWww,$sBtr));
     if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
     $Mailer->Text=str_replace('#D',$sMTx,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxBenachrSendTxt)));
     foreach($aM as $s){$Mailer->ClearTo(); $Mailer->AddTo($s); $Mailer->SetReplyTo($s); $Mailer->Send();}
   }}
   if(count($aLsch)>0&&(in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))){ //veraltete Bilder und Dateien
    if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
     $aD=array(); while($s=readdir($f)) if($i=(int)$s) if(isset($aLsch[$i])&&$aLsch[$i]) $aD[]=$s; closedir($f);
     foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
  }}}
 }else $Msg='<p class="admFehl">Fehlerhafter Seitenaufruf ohne Terminnummer!</p>';
}

//Daten bereitstellen
$aD=array(); $aSpalten=array(); $nSpalten=0; $nFelder=count($kal_FeldName); if(KAL_NListeAnders) $kal_ListenFeld=$kal_NListenFeld;
for($i=0;$i<$nFelder;$i++) $aSpalten[$kal_ListenFeld[$i]]=$i;
$aSpalten[0]=0; $nSpalten=count($aSpalten); $aTmp=array(); $aIdx=array();
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //über alle Datensaetze
  $a=explode(';',rtrim($aD[$i]));
  if(isset($a[1])&&$a[1]=='3'){
   $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$i); array_splice($a,1,1);
   for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace('\n ',NL,str_replace('`,',';',$a[$aSpalten[$j]])); $aTmp[$sId][]=(isset($a[$nFelder])?$a[$nFelder]:'');
  }
 }$aD=array();
}elseif($DbO){ //SQL
  $t=''; for($j=1;$j<$nSpalten;$j++) $t.=',kal_'.$aSpalten[$j]; $i=0;
  if($rR=$DbO->query('SELECT id'.$t.',periodik FROM '.KAL_SqlTabT.' WHERE online="3" ORDER BY id')){
   while($a=$rR->fetch_row()){
    $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',++$i);
    for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]); $aTmp[$sId][]=(isset($a[$nSpalten])?$a[$nSpalten]:'');
   }$rR->close();
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
if(ADM_Rueckwaerts) arsort($aIdx); reset($aIdx);
foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i];
if(!$Msg) $Msg='<p class="admMeld">Für folgende Termine wurde von Besuchern das Löschen beantragt.</p>';

//Scriptausgabe
echo $Msg.NL;
?>

<form name="TerminListe" action="terminLoeschung.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td>&nbsp;</td>'.NL.' <td>&nbsp;</td>';
 for($j=1;$j<$nSpalten;$j++){
  $sFN=$kal_FeldName[$aSpalten[$j]];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  echo NL.' <td><b>'.$sFN.'</b></td>';
 }
 echo NL.' <td><b>Periodik</b></td>';
 echo NL.'</tr>';
 $bAendern=file_exists('aendern.php'); $bDetail=file_exists('detail.php');
 foreach($aD as $a){ //Datenzeilen ausgeben
  $sId=$a[0];
  echo NL.'<tr class="admTabl">';
  echo NL.' <td align="right" valign="top">'.$sId.'&nbsp;<input class="admCheck" type="checkbox" name="kal_L'.$sId.'" value="1"'.(isset($aId[$sId])&&$aId[$sId]?' checked="checked"':'').' /></td>';
  echo NL.' <td align="center" valign="top">'.($bAendern?'<a href="aendern.php?kal_Num='.$sId.'&kal_Lsch=1"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  echo NL.' <td align="center" valign="top"><a href="terminLoeschung.php?kal_Num='.$sId.'&kal_Frei=1"><img src="'.$sHttp.'grafik/icon_Freigabe.gif" width="12" height="13" border="0" title="statt Löschen wieder freigeben"></a></td>';
  for($j=1;$j<$nSpalten;$j++){
   $k=$aSpalten[$j]; $t=$kal_FeldType[$k]; $sStil='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'm': case 'g': $s=fKalBB($s); break; // Text/Memo
     case 'a': case 'k': case 'o': case 'u': break; // so lassen
     case 'd': case '@': $w=trim(substr($s,11)); // Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; $sStil.='text-align:center;';
      if($t=='d'){if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].'&nbsp;'.$s; else $s.='&nbsp;'.$kal_WochenTag[$w];}
      elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      if($j==1&&$bDetail) $s='<a href="detail.php?kal_Num='.$sId.'&amp;kal_Lsch=1">'.$s.'</a>';
      break;
     case 'z': $sStil.='text-align:center;'; break; // Uhrzeit
     case 'w': // Währung
      if($s>0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
       if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sStil.='text-align:right;';
      }else $s='&nbsp;';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
      if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein; $sStil.='text-align:center;';
      break;
     case 'n': case '1': case '2': case '3': case 'r': // Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sStil.='text-align:right;';
      break;
     case 'l':
      $aI=explode('|',$s); $s=$aI[0]; $v=(isset($aI[1])?$aI[1]:$s);
      if(ADM_LinkSymbol){$v='<img src="'.$sHttp.'grafik/icon'.(strpos($s,'@')?'Mail':'Link').'.gif" width="16" height="16" border="0" title="'.$v.'">'; $sStil.='text-align:center;';}
      $s='<a href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($s)).'" target="_blank">'.$v.'</a>';
      break;
     case 'e': case 'c':
      if(!KAL_SQL) $s=fKalDeCode($s);
      if(ADM_LinkSymbol){
       $v='<img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';
      }else $v=$s;
      $s='<a href="mailto:'.$s.'" target="_blank">'.$v.'</a>';
      break;
     case 's': $w=$s;
      if(ADM_SymbSymbol){
       $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" alt="'.$w.'" />'; $sStil.='text-align:center;';
      }
      break;
     case 'b':
      if(ADM_BildVorschau){
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.abs($sId).'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); // Bild
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'/')+1).'" />'; $sStil.='text-align:center;';
      }else $s=fKalKurzName(substr($s,strpos($s,'|')+1));
      break;
     case 'f':
      if(ADM_DateiSymbol){
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); // Datei
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil.='text-align:center;';
       $v='<img src="'.$sHttp.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.KAL_TxDatei.'" />';
      }else $v=fKalKurzName($s);
      $s='<a href="'.$sHttp.KAL_Bilder.abs($sId).'~'.$s.'">'.$v.'</a>';
      break;
     case 'x': break;
     case 'p': $s=str_repeat('*',strlen($s)/2); break;
    }
   }else $s='&nbsp;';
   if(($w=$kal_SpaltenStil[$k])||$sStil) $sStil=' style="'.$sStil.$w.'"';
   echo NL.' <td valign="top"'.$sStil.'>'.$s.'</td>';
  }
  if($s=$a[$nSpalten]){
   $a=explode('|',$s); $s=(isset($a[1])?$a[1]:''); if(strpos($s,'-')) $t=' bis '.fKalAnzeigeDatum($s); else $t=', noch '.((int)$s).' mal'; $s=$a[0];
   if($s=='A') $s='täglich'; elseif($s=='B') $s='wöchentl.'; elseif($s=='C') $s='14-tägig'; elseif($s=='D'||$s=='E') $s='monatlich'; elseif($s=='F') $s='jährlich';
   echo NL.' <td valign="top">'.$s.$t.'</td>';
  }else echo NL.' <td>&nbsp;</td>';
  echo NL.'</tr>';
 }
?>
 <tr class="admTabl">
 <td align="right">
  <input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" border="0" title="markierte Termine löschen" />&nbsp;<input class="admCheck" type="checkbox" name="kal_All" value="1" onClick="fSelAll(this.checked)" />
 </td>
 <td colspan="<?php echo 2+$nSpalten?>">&nbsp;</td>
 </tr>
</table>
<input type="hidden" name="kalLsch" value="<?php echo $sLschFrg?>" />
</form>

<?php
echo fSeitenFuss();

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalWdhDat($sBeg,$sCod){
 $aTmp=explode('|',$sCod); $sTyp=$aTmp[0]; $sEnd=(isset($aTmp[1])?$aTmp[1]:''); $sP1=(isset($aTmp[2])?$aTmp[2]:''); $sP2=(isset($aTmp[3])?(int)$aTmp[3]:0);
 if(strpos($sEnd,'-')>0){// bis Enddatum
  $s=date('Y-m-d',KAL_MaxPeriode*86400+time()); if($sEnd>$s) $sEnd=$s;
  $nMax=3653;
 }else{ // n-Mal
  $nMax=min($sEnd,KAL_MaxWiederhol);
  $sEnd=date('Y-m-d',KAL_MaxPeriode*86400+time());
 }
 $bDo=true; $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),substr($sBeg,0,4));
 while($bDo){
  switch($sTyp){
   case 'A': //mehrtägig
    $nAkt+=86400; $Dat=date('Y-m-d w',$nAkt);
    break;
   case 'B': //wöchentlich
    $nAkt+=86400; while(strpos($sP1,@date('w',$nAkt))===false) $nAkt+=86400; $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'C': //14-tägig
    if(@date('w',$nAkt)==$sP1) $nAkt+=(14*86400); else while(@date('w',$nAkt)!=$sP1) $nAkt+=86400;
    $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'D': //monatlich-1
    $nAkt+=86400; $n=(int)@date('d',$nAkt);
    while($n!=(int)$sP1&&$n!=(int)$sP2){$nAkt+=86400; $n=(int)@date('d',$nAkt);} $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'E': //monatlich-2
    do{
     if($sP1<5) $Dat=date('Y-m-',$nAkt).(1+7*($sP1-1)); /* 1...4 */ else $Dat=date('Y-m-t',$nAkt); //5. oder letzter
     $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
     if($sP1<5){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400;} //1...4
     else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400;} //5. oder letzter
     $Dat=@date('Y-m-d w',$nAkt);
     $s=date('Y-m-t',$nAkt); $nAkt=@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))+86400;
     if(substr($Dat,0,10)<=$sBeg) $Dat=''; if($sP1==5) if((int)substr($Dat,8,2)<29) $Dat='';
    }while(!$Dat);
    break;
   case 'F': //jährlich
    if($sP1<='0'){//fester Termin
     $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),date('Y',$nAkt)+1);
     while(date('m-d',$nAkt)>substr($sBeg,5,5)) $nAkt-=86400; $Dat=@date('Y-m-d w',$nAkt);
    }else{//variables Datum
     do{
      $sJ=date('Y',$nAkt); if(!$sM=(int)$aTmp[4]) if($sP1<6) $sM=1; else $sM=12; //Monat des Jahres
      $Dat=$sJ.'-'.sprintf('%02d',$sM).'-'.($sP1<6?'01':date('t',@mktime(12,0,0,$sM,1,$sJ)));
      $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
      if($sP1<6){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400; $nAkt+=(7*($sP1-1)*86400);} //1...5
      else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400; if($sP1==6) $nAkt-=(7*86400);} //(vor)letzter
      $Dat=@date('Y-m-d w',$nAkt); $nAkt=@mktime(12,0,0,1,2,1+(int)$sJ);
      if(substr($Dat,0,10)<=$sBeg||substr($Dat,0,4)!=$sJ||($aTmp[4]>'0'&&$sM!=(int)substr($Dat,5,2))) $Dat='';
     }while(!$Dat);
    }
    break;
  }
  if($nMax-->0&&substr($Dat,0,10)<=$sEnd){if($Dat) $aDat[]=$Dat;} else $bDo=false;
 }
 if(isset($aDat)) return $aDat; else return false;
}

function fKalPlainText($s,$t,$bMemo=false){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||$bMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'd': if($s=='..') $s=''; break;
  case '@': $s=fKalAnzeigeDatum($s).substr($s,10); break;
  case 'l': case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}
//BB-Code zu HTML wandeln
function fKalBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="kalText"><li class="kalText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="kalText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}

function fKalTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if(($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')||$t=='v'){
   if($u=$s){
    switch($t){
     case 't': $s=fKalBB($s); $u=@strip_tags($s); break; //Text
     case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKalBB($s)); else $u=''; break; //Memo
     case 'a': case 'k': case 'o': break;  //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $u=fKalAnzeigeDatum($s); $w=trim(substr($s,11)); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0) if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];
      }else{if($w) $u.=' '.$w;}
      break;
     case 'z': $u=$s.' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $u=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.KAL_Waehrung;
      }else $u='';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); if($s=='J'||$s=='Y') $u=KAL_TxJa; elseif($s=='N') $u=KAL_TxNein; //Ja/Nein
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $u=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $u=str_replace('.',KAL_Dezimalzeichen,$s);
      break;
     case 'e': //E-Mai
      if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
      $u=''; break;
     case 'l': //Link
      $aI=explode('|',$s); $u=$aI[0];
      if($u) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($u))) $sErsatzEml=$u;
      break;
     case 'b': $s=substr($s,0,strpos($s,'|')); $u=KAL_Www.KAL_Bilder.$aT[0].'-'.$s; break; //Bild
     case 'f': $u=KAL_Www.KAL_Bilder.$aT[0].'~'.$s; break; //Datei
     case 'u':
      if($nId=(int)$s){
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
       }}
      }else $s=KAL_TxAutor0000;
      $u=$s; break;
     default: $u='';
    }//switch
   }
   if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
   if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
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
 if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
 return array($sT,$sKontaktEml);
}

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>