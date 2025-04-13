<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusageliste','<script language="JavaScript" type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.ZusageListe.length;++i)
   if(self.document.ZusageListe.elements[i].type=="checkbox") self.document.ZusageListe.elements[i].checked=bStat;
 }
 function druWin(sURL){dWin=window.open(sURL,"druck","width=600,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus();}
 function fSubmitNavigFrm(){document.ZusageListe.submit();}
</script>','ZZl');

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $nAnzahlPos=0;
$kal_ListFelder=explode(';',KAL_ZusageListAdm); $kal_ListFelder[0]=0; $aListFelder=array_flip($kal_ListFelder); $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);

$bLoeschen=file_exists('zusageLoeschen.php'); $bFreigabe=file_exists('zusageFreigabe.php');
$aId=array(); $aL=array(); $aZ=array(); $aT=array(); $sQ=''; $sLschFrg=''; $bOK=false; $sEml=''; $nLsch=0; //Zusagen loeschen
if(isset($_POST['LschBtn_x'])&&($_POST['LschBtn_x']>0||$_POST['LschBtn_y']>0)&&$bLoeschen){
 foreach($_POST as $k=>$xx) if(substr($k,4,1)=='L') {$aId[(int)substr($k,5)]=true; $nLsch++;} //Loeschnummern
 if(count($aId)){
  if($_POST['kalLsch']=='1'){
   if(!KAL_SQL){ //Textdatei
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $nMx=0; $nLsch=0;
    for($i=1;$i<$nSaetze;$i++){ //loeschen
     $s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); $nMx=max($n,$nMx);
     if(isset($aId[$n])&&$aId[$n]){
      $a=explode(';',rtrim($aD[$i])); $a[8]=fKalDecode($a[8]); $aL[]=$a; $aD[$i]=''; $nLsch++;
    }}
    if(substr($aD[0],0,7)!='Nummer_'){ //Kopfzeile defekt
     $s='Nummer_'.$nMx; for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.$kal_ZusageFelder[$i]; $aD[0]=$s.NL;
    }
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
     $Msg='<p class="admMeld">Die '.$nLsch.' markierten Zusagen wurden gelöscht.</p>';
    }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $nZ=count($aL);
    for($j=0;$j<$nZ;$j++){
     $s=$aL[$j][1].';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $aT[(int)$a[0]]=$a; break;
    }}
   }elseif($DbO){$s=''; //SQL
    foreach($aId as $k=>$xx){
     $s.=' OR nr='.$k;
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$k.'"')){
      if($a=$rR->fetch_row()) $aL[]=$a; $rR->close();
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$a[1].'"')){
       if($a=$rR->fetch_row()){array_splice($a,1,1); $aT[(int)$a[0]]=$a;} $rR->close();
      }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
    }}
    if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.substr($s,4))){
     $Msg='<p class="admMeld">Die '.$nLsch.' markierten Zusagen wurden gelöscht.</p>';
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
  }else{$sLschFrg='1'; $Msg='<p class="admFehl">Wollen Sie die markierten '.$nLsch.' Zusagen wirklich löschen?</p>';}
 }else $Msg='<p class="admMeld">Die Zusagedaten bleiben unverändert.</p>';
}elseif(($nNum=(isset($_GET['kal_Num'])?$_GET['kal_Num']:''))&&$bFreigabe){ //Zusagenstatus aendern
 $nSta=(isset($_GET['kal_Status'])?substr($_GET['kal_Status'],0,1):''); $sNr=''; $sLNr=''; $sLschUsr='';
 if(!KAL_SQL){ //Textdatei
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$nNum.';'; $p=strlen($s); $bNeu=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
   $sZl=$aD[$i]; for($j=1;$j<6;$j++) $p=strpos($sZl,';',$p)+1;
   if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
    if((int)substr($sZl,$p,1)==1-$nSta||substr($sZl,$p,1)=='2'){$aD[$i]=substr_replace($sZl,$nSta,$p,1); $bNeu=true;}
   }elseif(substr($sZl,$p,1)=='-'||substr($sZl,$p,1)=='*'){ //widerrufen
    $a=explode(';',rtrim($sZl)); $sNr=(int)$a[1]; $sLschUsr=$a[8]; $a[8]=fKalDecode($a[8]); $aL[]=$a; $aD[$i]=''; $bNeu=true;
   }
   break;
  }
  if($sLschUsr&&$sNr>0){ //fruehere Zusagen loeschen
   $sNr=';'.$sNr.';'; $p=strlen($sNr);
   for($i=1;$i<$nSaetze;$i++){
    $s=$aD[$i]; if(substr($s,strpos($s,';'),$p)==$sNr){ //gefunden
     $a=explode(';',$s,10); if($sLschUsr==$a[8]){$sLNr.=', '.$a[0]; $aD[$i]=''; $bNeu=true;}
  }}}
  if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //neu schreiben
   fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.$sLNr.' wurde '.($nSta=='1'?'aktiv geschaltet':($nSta=='0'?'inaktiv geschaltet':'widerrufen')).'.</p>';
   if($nSta=='1'){ //aktiviert
    $aZ=explode(';',rtrim($sZl)); $sEml=fKalDeCode($aZ[8]);
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=(int)$aZ[1].';'; $p=strlen($s); //Termin holen
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
   }elseif(count($aL)){ //widerrufen
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$aL[0][1].';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $aT[(int)$a[0]]=$a; break;
   }}
  }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
 }elseif($DbO){ //bei SQL
  if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
   if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="'.$nSta.'" WHERE nr="'.$nNum.'"')){
    $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
    if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'"')){
     if($aZ=$rR->fetch_row()) $sEml=$aZ[8]; $rR->close();
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.(int)$aZ[1].'"')){ //Termin holen
      $aT=$rR->fetch_row(); $rR->close();
    }}
   }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
  }elseif($nSta=='-'||$nSta=='*'){ //widerrufen
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'"')){
    if($a=$rR->fetch_row()){
     $sNr=(int)$a[1]; $sLschUsr=$a[8]; $aL[]=$a; $rR->close();
     if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'"')){
      if($rR=$DbO->query('SELECT nr,termin FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       while($a=$rR->fetch_row()) $sLNr.=', '.$a[0]; $rR->close();
      }
      if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.$sLNr.' wurde widerrufen.</p>';
      }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sNr.'"')){ //Termin holen
       $a=$rR->fetch_row(); array_splice($a,1,1); $aT[(int)$a[0]]=$a; $rR->close();
    }}}
   }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
  }
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';

 if(count($aZ)>1&&(KAL_ZusageFreigabeMail||KAL_ZusageBstInfoAut)){ //Aktivierungsmails
  $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml=''; $sWww=fKalWww();
  $sMTx=KAL_TxZusageFreiMTx;
  $sZDat='STATUS: ';
  switch($nSta){
   case '1': $sZDat.=KAL_TxZusage1Status; break; case '2': $sZDat.=KAL_TxZusage2Status; break; case '0': $sZDat.=KAL_TxZusage0Status; break;
   case '-': $sZDat.=KAL_TxZusage3Status; break; case '*': $sZDat.=KAL_TxZusage4Status; break; case '7': $sZDat.=KAL_TxZusage7Status; break; 
   default: $sZDat.='unklar??';
  }
  $sZDat.="\nID-NUMMER: ".sprintf('%04d',$aZ[0])."\nTERMIN-NR: ".sprintf('%04d',$aZ[1]);
  for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){//Zusagedaten aufbereiten
   if($i==2) $aZ[2]=fKalAnzeigeDatum($aZ[2]); elseif($i==5) $aZ[5]=fKalAnzeigeDatum(substr($aZ[5],0,10)).substr($aZ[5],10); elseif($i==8) $aZ[8]=$sEml;
   $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.str_replace('`,',';',$aZ[$i]);
   $sMTx=str_replace('{'.str_replace('`,',';',$kal_ZusageFelder[$i]).'}',str_replace('`,',';',$aZ[$i]),$sMTx);
  }
  if((strpos($sMTx,'#D')>0||KAL_ZusageBstInfoAut)&&count($aT)>1){//Termindaten aufbereiten
   array_splice($aT,1,1); $a=fKalTerminPlainText($aT,$DbO); $sTDat=$a[0]; $sKontaktEml=$a[1];
  }
  if($sEml){//E-Mail an Zusagenden
   $sMTx=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",$sMTx))));
   require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail(); $sBtr=str_replace('#A',$sWww,KAL_TxZusageFreiBtr);
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
  }
  if(KAL_ZusageBstInfoAut&&strlen($sKontaktEml)>0){//E-Mail an Autor
   $sMTx=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxZusageInfoMTx))));
   require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail(); $sBtr=str_replace('#A',$sWww,KAL_TxZusageFreiBtr);
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sKontaktEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if($sEml) $Mailer->SetReplyTo($sEml);
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
 }}//Aktivierungsmails
}//GET $nNum

if(count($aL)&&(KAL_ZusageLschInfoAut||KAL_ZusageLschNzZusag)){ //Loeschmails
  require_once(KAL_Pfad.'class.plainmail.php'); $sWww=fKalWww();
  $sLnk=(KAL_ZusageLink==''?$sHttp.'kalender.php?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk='http://'.$sWww.$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
  foreach($aL as $a){
   $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml='';
   if((strpos(KAL_TxZusageLschMTx,'#D')>0)&&isset($aT[$a[1]])){ //Termindaten aufbereiten
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
   if(KAL_ZusageLschInfoAut&&$sKontaktEml>''){$Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();} //Mail an Terminautor
   if(KAL_ZusageLschNzZusag){$Mailer->AddTo($a[8]); $Mailer->SetReplyTo($a[8]); $Mailer->Send();} //Mail an Zusagenden
  }
}//Loeschmails

if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $bZeigeAkt=(isset($_GET['kal_Akt'])?(bool)$_GET['kal_Akt']:KAL_ZusageAdmLstKommend);
 $bZeigeAlt=(isset($_GET['kal_Alt'])?(bool)$_GET['kal_Alt']:KAL_ZusageAdmLstVorbei);
}else{
 $bZeigeAkt=(isset($_POST['kal_Akt'])?(bool)$_POST['kal_Akt']:false);
 $bZeigeAlt=(isset($_POST['kal_Alt'])?(bool)$_POST['kal_Alt']:false);
}
if(!$bZeigeAlt) $bZeigeAkt=true;
if($bZeigeAkt!=KAL_ZusageAdmLstKommend) $sQ.='&amp;kal_Akt='.($bZeigeAkt?'1':'0');
if($bZeigeAlt!=KAL_ZusageAdmLstVorbei) $sQ.='&amp;kal_Alt='.($bZeigeAlt?'1':'0');

for($i=0;$i<=$nZusageFelder;$i++){ //Abfrageparameter aufbereiten
 $s=(isset($_POST['kal_'.$i.'F1'])?$_POST['kal_'.$i.'F1']:(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s;
  if($i!=5&&$i!=2) $a1Filt[$i]=$s; else $a1Filt[$i]=fKalNormDatum($s);
 }
 $s=(isset($_POST['kal_'.$i.'F2'])?$_POST['kal_'.$i.'F2']:(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($i!=5&&$i!=2) $a2Filt[$i]=$s; else $a2Filt[$i]=fKalNormDatum($s);
  if($i<=2||$i==5||$i==6){if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='0';}
 }
 $s=(isset($_POST['kal_'.$i.'F3'])?$_POST['kal_'.$i.'F3']:(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:''));
 if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;kal_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
}
$bZeigeOnl=true; $bZeigeOfl=true; $bZeigeBst=true; $bZeigeVmk=true;
$s=(isset($_POST['kal_Onl'])?$_POST['kal_Onl']:(isset($_GET['kal_Onl'])?$_GET['kal_Onl']:''));
$t=(isset($_POST['kal_Ofl'])?$_POST['kal_Ofl']:(isset($_GET['kal_Ofl'])?$_GET['kal_Ofl']:''));
$u=(isset($_POST['kal_Bst'])?$_POST['kal_Bst']:(isset($_GET['kal_Bst'])?$_GET['kal_Bst']:''));
$v=(isset($_POST['kal_Vmk'])?$_POST['kal_Vmk']:(isset($_GET['kal_Vmk'])?$_GET['kal_Vmk']:''));
if($s&&!($t&&$u&&$v)){$sQ.='&amp;kal_Onl=1'; $aQ['Onl']=1;} elseif(!$s&&($t||$u||$v)) $bZeigeOnl=false;
if($t&&!($s&&$u&&$v)){$sQ.='&amp;kal_Ofl=1'; $aQ['Ofl']=1;} elseif(!$t&&($s||$u||$v)) $bZeigeOfl=false;
if($u&&!($s&&$t&&$v)){$sQ.='&amp;kal_Bst=1'; $aQ['Bst']=1;} elseif(!$u&&($s||$t||$v)) $bZeigeBst=false;
if($v&&!($s&&$t&&$u)){$sQ.='&amp;kal_Vmk=1'; $aQ['Vmk']=1;} elseif(!$v&&($s||$t||$u)) $bZeigeVmk=false;

$nIndex=(int)(isset($_GET['kal_Index'])?$_GET['kal_Index']:(isset($_POST['kal_Index'])?$_POST['kal_Index']:0));
$sRueck=(isset($_GET['kal_Rueck'])?$_GET['kal_Rueck']:(isset($_POST['kal_Rueck'])?$_POST['kal_Rueck']:''));
if(!KAL_Zusagen){$nIndex=0; $sRueck='';} if($nIndex>0) $aQ['Index']=$nIndex; if($sRueck!='') $aQ['Rueck']=$sRueck;

for($j=0;$j<=$nZusageFelder;$j++){
 if($kal_ZusageFelder[$j]=='ANZAHL'){$nAnzahlPos=$j; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$j]=KAL_ZusageNameAnzahl;}
}

//Daten bereitstellen
$aT=array(); $aIdx=array(); $sRefDat=date('Y-m-d');
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sZId=(int)$a[0]; $sSta=$a[6];
  $b=($sSta=='1'&&$bZeigeOnl||($sSta=='0'||$sSta=='-')&&$bZeigeOfl||($sSta=='2'||$sSta=='*')&&$bZeigeBst||($sSta=='7')&&$bZeigeVmk);
  if($bZeigeAkt&&!$bZeigeAlt) $b=$b&&($a[2]>=$sRefDat);//kommende
  elseif($bZeigeAlt&&!$bZeigeAkt) $b=$b&&($a[2]<$sRefDat);//abgelaufene
  if($b&&isset($a1Filt)&&is_array($a1Filt)){
   reset($a1Filt);
   foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
    $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
    if($j==5||$j==2){ //Datum
     $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
    }elseif($j<2){ //Nr
     $v=(int)$v; $w=(int)$w; $s=(int)$a[$j];
     if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
    }elseif($j==8){ //EMail
     if(strlen($w)){if(stristr(fKalDeCode($a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr(fKalDeCode($a[$j]),$v)||$b2)) $b=false;
    }else{//Text
     if(strlen($w)){if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)||$b2)) $b=false;
  }}}
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v) if(stristr((isset($a[$j])?str_replace('`,',';',$a[$j]):''),$v)){$b=false; break;}
  }
  if($b){ //Datensatz gueltig
   $aT[$sZId]=array($sZId); $s=$a[$nIndex];
   if($nIndex==0) $aIdx[$sZId]=sprintf('%05d',$sZId); //Nr
   if($nIndex==1||$nIndex==7){ //Termin oder User
    $aIdx[$sZId]=sprintf('%05d',$s).'-'.sprintf('%05d',$sZId);
   }elseif($nIndex==2||$nIndex==5){ //Datum
    $aIdx[$sZId]=$s.sprintf('%05d',$sZId);
   }elseif($nIndex==8){ //EMail
    $aIdx[$sZId]=strtolower(fKalDeCode($s)).chr(255).sprintf('%05d',$sZId);
   }elseif($nIndex==4){ //Veranstaltung
    $s=strtoupper(strip_tags($s));
    for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
    $aIdx[$sZId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%05d',$sZId);
   }elseif($nIndex==$nAnzahlPos){ //Anzahl
    $aIdx[$sZId]=sprintf('%05d',abs($s)).'-'.sprintf('%05d',$sZId);
   }elseif($nIndex>8){
    $aIdx[$sZId]=(strlen($s)>0?strtoupper(strip_tags($s)):' ').chr(255).sprintf('%05d',$sZId);
   }
   for($j=1;$j<=$nZusageFelder;$j++) $aT[$sZId][]=(isset($a[$j])?str_replace('`,',';',$a[$j]):'');
  }
 }
}elseif($DbO){ //SQL
 $aFN=array('nr','termin','datum','zeit','veranstaltung','buchung','aktiv','email'); $s='';
 if($bZeigeAkt&&!$bZeigeAlt) $s.=' AND(datum>="'.$sRefDat.'")';//kommende
 elseif($bZeigeAlt&&!$bZeigeAkt) $s.=' AND(datum<"'.$sRefDat.'")';//abgelaufene
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  $s.=' AND('.($j<9?$aFN[$j]:'dat_'.$j); $w=(isset($a2Filt[$j])?$a2Filt[$j]:'');//$v Suchwort1, $w Suchwort2
  if($j==5||$j==2){
   if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
  }elseif($j<2){ //Nr
   $v=(int)$v;
   if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.((int)$w).'"'; else $s.='="'.$v.'"';
  }else{
   $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR '.($j<9?$aFN[$j]:'dat_'.$j).' LIKE "%'.$w.'%"';
  }
  $s.=')';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $s.=' AND NOT('.($j<9?$aFN[$j]:'dat_'.$j).' LIKE "%'.$v.'%")';
 }
 $o=''; if($bZeigeOnl) $o.=' OR aktiv="1"'; if($bZeigeOfl) $o.=' OR aktiv="0" OR aktiv="-"'; if($bZeigeBst) $o.=' OR aktiv="2" OR aktiv="*"'; if($bZeigeVmk) $o.=' OR aktiv="7"';
 if($o=substr($o,4)){$i=substr_count($o,'OR'); if($i>0) $o='('.$o.')'; if($i==4) $o='nr>0';} else $o='nr>0';
 $t=''; for($j=9;$j<=$nZusageFelder;$j++) $t.=',dat_'.$j; $i=0;
 if($rR=$DbO->query('SELECT nr,termin,datum,zeit,veranstaltung,buchung,aktiv,benutzer,email'.$t.' FROM '.KAL_SqlTabZ.' WHERE '.$o.$s.' ORDER BY nr')){
  while($a=$rR->fetch_row()){
   $sZId=(int)$a[0]; $aT[$sZId]=array($sZId); $s=$a[$nIndex];
   if($nIndex==0) $aIdx[$sZId]=sprintf('%05d',$sZId); //Nr
   if($nIndex==1||$nIndex==7){ //Termin oder User
    $aIdx[$sZId]=sprintf('%05d',$s).'-'.sprintf('%05d',$sZId);
   }elseif($nIndex==2||$nIndex==5){ //Datum
    $aIdx[$sZId]=$s.sprintf('%05d',$sZId);
   }elseif($nIndex==8){ //EMail
    $aIdx[$sZId]=strtolower($s).chr(255).sprintf('%05d',$sZId);
   }elseif($nIndex==4){ //Veranstaltung
    $s=strtoupper(strip_tags($s));
    for($j=strlen($s)-1;$j>=0;$j--) if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j); //BB-Code weg
    $aIdx[$sZId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%05d',$sZId);
   }elseif($nIndex==$nAnzahlPos){ //Anzahl
    $aIdx[$sZId]=sprintf('%05d',abs($s)).'-'.sprintf('%05d',$sZId);
   }elseif($nIndex>8){ //Anzahl
    $aIdx[$sZId]=(strlen($s)>0?strtoupper(strip_tags($s)):' ').chr(255).sprintf('%05d',$sZId);
   }
   for($j=1;$j<=$nZusageFelder;$j++) $aT[$sZId][]=str_replace("\r",'',$a[$j]);
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';//SQL

if($sRueck==''&&!KAL_ZusageAdmRueckw||$sRueck=='0'){if($nIndex!=0) asort($aIdx);} else arsort($aIdx);
$aD=array(); reset($aIdx); $k=0;
if(!$nStart=(int)(isset($_GET['kal_Start'])?$_GET['kal_Start']:(isset($_POST['kal_Start'])?$_POST['kal_Start']:0))) $nStart=1; $nStop=$nStart+KAL_ZusageAdmLstLaenge;
foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aT[$i];

//Scriptausgabe
if(!$Msg){
 if(!$sQ) $Msg='<p class="admMeld">Terminzusagen/Reservierungen/Bestellungen'.(KAL_Zusagen?'':' <span style="color:#557"> - eingeschränktes Modul (Demo)</span>').'</p>';
 else $Msg='<p class="admMeld">Abfrageergebnis - Terminzusagen/Reservierungen/Bestellungen</p>';
}
$sQs=$sQ; $sQ.=($nIndex?'&amp;kal_Index='.$nIndex:'').(strlen($sRueck)?'&amp;kal_Rueck='.$sRueck:'');
?>

<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $Msg;?></td>
  <td align="right">
  [ <a href="<?php echo (KAL_Zusagen?'zusageExport.php'.($sQ?'?'.substr($sQ,5):''):'zusageListe.php" title="nur in der Vollversion')?>">Export</a> ] &nbsp;
  [ <a href="<?php echo (KAL_Zusagen?'zusageDruckListe.php'.($sQ?'?'.substr($sQ,5):'').'" target="druck" onclick="druWin(this.href);return false;" title="Zusagen drucken':'zusageListe.php" title="nur in der Vollversion')?>"><img src="<?php echo $sHttp?>grafik/iconDrucken.gif" align="top" width="16" height="16" border="0" title="Zusagen drucken">&nbsp;drucken</a> ] &nbsp;
  [ <a href="<?php echo (KAL_Zusagen?'zusageSuche.php'.($sQs?'?'.substr($sQs,5):''):'zusageListe.php" title="nur in der Vollversion')?>">Zusagensuche</a> ]
  </td>
 </tr>
</table>

<?php
 $sNavigator=fKalNavigator($nStart,count($aIdx),KAL_ZusageAdmLstLaenge,$sQ); echo $sNavigator;
?>

<form name="ZusageListe" action="zusageListe.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 echo    '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td style="white-space:nowrap;"><b>Z-Nr.</b>'.fKalSortIcon(0,$sQ,KAL_Zusagen).'</td>';
 echo NL.' <td style="white-space:nowrap;"><b>T-Nr.</b>'.fKalSortIcon(1,$sQ,KAL_Zusagen).'</td>';
 for($j=2;$j<=$nZusageFelder;$j++) if(isset($aListFelder[$j])&&($i=$aListFelder[$j])){
  $sS=''; if($i<8&&$i!=4||$i==$nAnzahlPos) $sS=' style="text-align:center"'; if($aZusageFeldTyp[$i]=='w') $sS=' style="text-align:right"';
  echo NL.' <td'.$sS.'><b>'.str_replace('`,',';',$kal_ZusageFelder[$i]).'</b>'.($i!=3&&$i!=6 ?fKalSortIcon($i,$sQ,KAL_Zusagen):'').'</td>';
 }
 echo NL.'</tr>';
 if($nStart>1) $sQ.='&amp;kal_Start='.$nStart; $aQ['Start']=$nStart;
 foreach($aD as $a){ //Datenzeilen ausgeben
  echo NL.'<tr class="admTabl">'; $sZId=$a[0];
  echo NL.' <td style="white-space:nowrap;">'.($bLoeschen?'<input class="admCheck" type="checkbox" name="kal_L'.$sZId.'" value="1"'.(isset($aId[$sZId])&&$aId[$sZId]?' checked="checked"':'').' /> ':'').'<a href="zusageDetail.php?kal_Num='.$sZId.$sQ.'" title="Details zur Zusage-Nr. '.$sZId.' anzeigen">'.sprintf('%04d',$sZId).'</a></td>';
  echo NL.' <td style="text-align:center;"><a href="zusageTermin.php?kal_Num='.$a[1].$sQ.'" title="Zusagen zum Termin-Nr. '.$a[1].' anzeigen">'.sprintf('%0'.KAL_NummerStellen.'d',$a[1]).'</a></td>';
  for($j=2;$j<=$nZusageFelder;$j++) if(isset($aListFelder[$j])&&$i=$aListFelder[$j]){$s=(isset($a[$i])?str_replace('`,',';',$a[$i]):''); $sS='';
   if(strlen($s)>0){
    switch($i){
     case 2: case 5: $s=fKalAnzeigeDatum($s); $sS=' style="text-align:center;"'; break;
     case 3: $sS=' style="text-align:center;"'; break;
     case 4: $s=fKalKurzMemo($s,KAL_ZusageAdmLstVstBreit); break;
     case 6: $sS=' style="text-align:center;"';
      $sSta=$s; $sNeuSta='0'; $sImgSta='Grn.gif" title="gültig'; $sLnkStaA=''; $sLnkStaE='';
      if($sSta=='0'){$sNeuSta='1'; $sImgSta='Rot.gif" title="vorgemerkt'.($bFreigabe?' - jetzt akzeptieren':'');}
      elseif($sSta=='2'){$sNeuSta='1'; $sImgSta='RtGn.gif" title="bestätigt'.($bFreigabe?' - jetzt akzeptieren':'');}
      elseif($sSta=='-'){$sNeuSta='-'; $sImgSta='RotX.gif" title="Widerruf vorgemerkt'.($bFreigabe?' - jetzt widerrufen':'');}
      elseif($sSta=='*'){$sNeuSta='-'; $sImgSta='RtGnX.gif" title="Widerruf bestätigt'.($bFreigabe?' - jetzt widerrufen':'');}
      elseif($sSta=='7'){$sNeuSta='1'; $sImgSta='Glb.gif" title="auf der Warteliste'.($bFreigabe?' - jetzt akzeptieren':'');}
      if($bFreigabe){$sLnkStaA='<a href="zusageListe.php?kal_Start='.$nStart.'&amp;kal_Num='.$sZId.$sQ.'&amp;kal_Status='.$sNeuSta.'">'; $sLnkStaE='</a>';}
      $s=$sLnkStaA.'<img src="'.$sHttp.'grafik/punkt'.$sImgSta.'" width="12" height="12" border="0">'.$sLnkStaE;
      break;
     case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); $sS=' style="text-align:center;"'; break;
     case 8: if(!KAL_SQL) $s=fKalDeCode($s); $s='<a href="zusageKontakt.php?kal_Num='.$sZId.$sQ.'"><img src="'.$sHttp.'grafik/iconMail.gif" style="margin-top:-2px;margin-bottom:-2px" width="16" height="16" border="0" title="'.$s.' kontaktieren"></a> '.$s; break;
     case $nAnzahlPos: $s=abs($s); $sS=' style="text-align:center;"'; break;
    }
    if($aZusageFeldTyp[$i]=='w'){
     $s=(float)$s;
     if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s='';
     $sS=' style="text-align:right;"';
    }
   }else $s='&nbsp;';
   echo NL.' <td'.$sS.'>'.$s.'</td>';
  }
  echo NL.'</tr>';
 }
?>

<tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="kal_All" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" name="LschBtn" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" align="top" border="0" title="markierte Zusagen löschen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo count($aListFelder)-1;?>">&nbsp;</td>
</tr>
</table>
<input type="hidden" name="kalLsch" value="<?php echo $sLschFrg?>" />
<?php
foreach($aQ as $k=>$v) echo NL.'<input type="hidden" name="kal_'.$k.'" value="'.$v.'" />';

$sNavigator=substr_replace($sNavigator,'<td style="white-space:nowrap;text-align:center;width:90%"><input type="checkbox" class="admRadio" name="kal_Akt" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeAkt?' checked="checked"':'').' /> kommende Termine &nbsp; <input type="checkbox" class="admRadio" name="kal_Alt" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeAlt?' checked="checked"':'').' /> abgelaufene Termine </td>'."\n  ",strpos($sNavigator,'<td style="width:17px'),0);
echo $sNavigator;
?>

</form>

<?php
echo fSeitenFuss();

function fKalNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fKalNavigator($nStart,$nCount,$nListenLaenge,$sQry){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge);
 $s ='<td style="width:17px;text-align:center;"><a href="zusageListe.php?'.substr($sQry.'&amp;kal_Start=',5).'1" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:17px;text-align:center;"><a href="zusageListe.php?'.substr($sQry.'&amp;kal_Start=',5).(($i-1)*$nListenLaenge+1).'" title="'.'">&nbsp;'.$nPg.'&nbsp;</a></td>';
 }
 $s.=NL.'  <td style="width:17px;text-align:center;"><a href="zusageListe.php?'.substr($sQry.'&amp;kal_Start=',5).(max($nPgs-1,0)*$nListenLaenge+1).'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:8px;margin-bottom:8px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td style="white-space:nowrap;">Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
}

function fKalSortIcon($nSp,$sQry,$bV){//Sortierdreieck
 if($p=strpos($sQry,'amp;kal_Index=')){$nIdx=(int)substr($sQry,$p+14); $sQry=substr_replace($sQry,'',$p-1,16+($nIdx<10?0:1));} else $nIdx=0;
 if($p=strpos($sQry,'amp;kal_Rueck=')){$sRw=substr($sQry,$p+14); $sQry=substr_replace($sQry,'',$p-1,16);} else $sRw='';
 $t=''; $w=''; // $t-Iconart, $w-Text: ab-/aufsteigend
 $sQry.=($nSp!=0?'&amp;kal_Index='.$nSp:'');
 if($nSp!=$nIdx) $t='e';
 else{
  if($sRw==''&&!KAL_ZusageAdmRueckw||$sRw=='0'){
   $t='t'; $w='absteigend '; if($sRw!='0') $sQry.='&amp;kal_Rueck=1';
  }else{$t='r'; $w='aufsteigend '; if(KAL_ZusageAdmRueckw) $sQry.='&amp;kal_Rueck=0';}
 }
 $t='<img src="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.KAL_Www.'grafik/sortier'.$t.'.gif" width="10" height="10" border="0" title="'.$w.'sortieren'.($bV?'':' - nur in der Vollversion').'" alt="'.$w.'sortieren" />';
 return '&nbsp;<a href="zusageListe.php'.($sQry?'?'.substr($sQry,5):'').'">'.$t.'</a>';
}

//Text mit BB-Code einkuerzen
function fKalKurzMemo($s,$nL=80){
 if(strlen($s)>$nL){
  $v='#'.substr($s,0,$nL);
  if($p=strrpos($v,'[')){ //BB-Code enthalten
   if(strrpos($v,']')<$p) $v=substr($v,0,$p); //angeschnittenen Codes streichen
   $p=0; $aTg=array();
   while($p=strpos($v,'[',++$p)){ //Codes erkennen
    if($q=strpos($v,']',++$p)){$t=substr($v,$p,$q-$p); $aTg[]=(($q=strpos($t,'='))?substr($t,0,$q):$t);}
   }
   $n=count($aTg)-1;
   for($i=$n;$i>=0;$i--){ //Codes durchsuchen
    $s=$aTg[$i];
    if(substr($s,0,1)!='/'){
     $bFnd=false;
     for($j=$i;$j<=$n;$j++) if($aTg[$j]=='/'.$s){$aTg[$j]='#'; $bFnd=true; break;}
     if(!$bFnd) $v.='[/'.$s.']'; //fehlenden Code anhaengen
  }}}
  return substr($v,1).'....';
 }else return $s;
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
      $s=(float)$s;
      if($s>0||!KAL_PreisLeer){
       $u=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.KAL_Waehrung;
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