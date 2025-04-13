<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminzusagen','<script language="JavaScript" type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.ZusageListe.length;++i)
   if(self.document.ZusageListe.elements[i].type=="checkbox") self.document.ZusageListe.elements[i].checked=bStat;
 }
 function druWin(sURL){dWin=window.open(sURL,"druck","width=600,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus();}
</script>','ZZl');

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $nAnzahlPos=0; $nAnzahlSumme=0;
$kal_ListFelder=explode(';',KAL_ZusageListAdm); $kal_ListFelder[0]=0; $kal_ListFelder[1]=0; $aListFelder=array_flip($kal_ListFelder);

if(!$sQ=$_SERVER['QUERY_STRING']) $sQ='kal_Num=0&amp;'.(isset($_POST['kal_Qry'])?$_POST['kal_Qry']:''); $sQLst='';
if($p=strpos($sQ,'kal_Zid')){if(substr($sQ,$p-5,1)=='&') $p-=5; elseif(substr($sQ,$p-1,1)=='&') $p--; $sQ=substr($sQ,0,$p);}
if($p=strpos($sQ,'kal_',8)) $sQ=substr($sQ,$p); else $sQ='';

$aD=array(); $aZ=array(); $aT=array(); $aL=array(); $aA=array();
$bLoeschen=file_exists('zusageLoeschen.php'); $bFreigabe=file_exists('zusageFreigabe.php'); $bZurTrmLst=false;
if($sTId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:(isset($_POST['kal_Num'])?$_POST['kal_Num']:0))){
 $aId=array(); $sLschFrg=''; $bOK=false; //Zusagen loeschen
 if(isset($_POST['LschBtn_x'])&&($_POST['LschBtn_x']>0||$_POST['LschBtn_y']>0)&&$bLoeschen){
  foreach($_POST as $k=>$xx) if(substr($k,4,1)=='L') $aId[(int)substr($k,5)]=true; //Loeschnummern
  if(count($aId)){
   if($_POST['kalLsch']=='1'){
    if(!KAL_SQL){ //Textdatei
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $nMx=0;
     for($i=1;$i<$nSaetze;$i++){
      $s=substr($aD[$i],0,12); $n=(int)substr($s,0,strpos($s,';')); $nMx=max($n,$nMx);
      if(isset($aId[$n])&&$aId[$n]){$a=explode(';',rtrim($aD[$i])); $a[8]=fKalDecode($a[8]); $aL[]=$a; $aD[$i]='';} //loeschen
     }
     if(substr($aD[0],0,7)!='Nummer_'){ //Kopfzeile defekt
      $s='Nummer_'.$nMx; for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.$kal_ZusageFelder[$i]; $aD[0]=$s.NL;
     }
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $Msg='<p class="admMeld">Die markierten Zusagen wurden gelöscht.</p>';
     }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
    }elseif($DbO){$s=''; //bei SQL
     foreach($aId as $k=>$xx){$s.=' OR nr='.$k;
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$k.'"' )){
       if($a=$rR->fetch_row()) $aL[]=$a; $rR->close();
     }}
     if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE '.substr($s,4))){
      $Msg='<p class="admMeld">Die markierten Zusagen wurden gelöscht.</p>';
     }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
    }
   }else{$sLschFrg='1'; $Msg='<p class="admFehl">Wollen Sie die markierten Zusagen wirklich löschen?</p>';}
  }else $Msg='<p class="admMeld">Die Zusagedaten bleiben unverändert.</p>';
 }elseif(($nNum=(isset($_GET['kal_Zid'])?$_GET['kal_Zid']:''))&&$bFreigabe){ //Zusagenstatus aendern
  $sNr=''; $sLNr=''; $sLschUsr='';
  $nSta=(isset($_GET['kal_Status'])?substr($_GET['kal_Status'],0,1):'');
  if(!KAL_SQL){ //Textdatei
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$nNum.';'; $p=strlen($s); $bNeu=false;
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
    $s=$aD[$i]; for($j=1;$j<6;$j++) $p=strpos($s,';',$p)+1;
    if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
     if((int)substr($s,$p,1)==1-$nSta||substr($s,$p,1)=='2'){$aD[$i]=substr_replace($s,$nSta,$p,1); $bNeu=true;}
    }elseif(substr($s,$p,1)=='-'||substr($s,$p,1)=='*'){ //widerrufen
     $aZ=explode(';',rtrim($s)); $sNr=(int)$aZ[1]; $sLschUsr=$aZ[8]; $aD[$i]=''; $bNeu=true; $aZ[8]=fKalDecode($aZ[8]); $aL[]=$aZ;
    }
    break;
   }
   if($sLschUsr&&$sNr>0){ //fruehere Zusagen loeschen
    $sNr=';'.$sNr.';'; $p=strlen($sNr);
    for($i=1;$i<$nSaetze;$i++){
     $s=$aD[$i]; if(substr($s,strpos($s,';'),$p)==$sNr){ //gefunden
      $aZ=explode(';',$s,10); if($sLschUsr==$aZ[8]){$sLNr.=', '.$aZ[0]; $aD[$i]=''; $bNeu=true;}
   }}}
   if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //neu schreiben
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.$sLNr.' wurde '.($nSta=='1'?'aktiv geschaltet':($nSta=='0'?'inaktiv geschaltet':'widerrufen')).'.</p>';
    if($nSta=='1'){ //aktiviert
     $aA=explode(';',rtrim($aD[$i])); $aA[8]=fKalDeCode($aA[8]);
   }}else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
  }elseif($DbO){ //bei SQL
   if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
    if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="'.$nSta.'" WHERE nr="'.$nNum.'"')){
     $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
     if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'"')){
      $aA=$rR->fetch_row(); $rR->close();
    }}else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
   }else{ //widerrufen
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'"')){
     if($aZ=$rR->fetch_row()){$sNr=(int)$aZ[1]; $sLschUsr=$aZ[8]; $aL[]=$aZ;} $rR->close();
     if($sLschUsr&&$sNr>0){
      $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$nNum.'" AND email="'.$sLschUsr.'"');
      if($rR=$DbO->query('SELECT nr,termin FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       while($aZ=$rR->fetch_row()) $sLNr.=', '.$aZ[0]; $rR->close();
      }
      if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       $Msg='<p class="admErfo">Die Zusage Nr. '.$nNum.$sLNr.' wurde widerrufen.</p>';
      }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
     }
    }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
 }}}

 $aZ=array(); $aT=array(); $sKapaz=''; //Daten holen
 if(!KAL_SQL){
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sTId.';'; $l=strlen($s);
  for($i=1;$i<$nSaetze;$i++){
   $sZ=rtrim($aD[$i]); $p=strpos($sZ,';'); if(substr($sZ,$p,$l)==$s){$a=explode(';',$sZ); $a[8]=fKalDeCode($a[8]); $aZ[]=$a;}
  }
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'"')){
   while($a=$rR->fetch_row()) $aZ[]=$a; $rR->close();
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){
    $aT=$rR->fetch_row(); $rR->close();
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 if(count($aT)){array_splice($aT,1,1); if($i=array_search('KAPAZITAET',$kal_FeldName)) $sKapaz=$aT[$i];}

 if(count($aL)&&(KAL_ZusageLschInfoAut||KAL_ZusageLschNzZusag)){ //Loeschmails
  require_once(KAL_Pfad.'class.plainmail.php'); $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml=''; $sWww=fKalWww();
  if((strpos(KAL_TxZusageLschMTx,'#D')>0)&&count($aT)>1){ //Termindaten aufbereiten
   $a=fKalTerminPlainText($aT,$DbO); $sTDat=$a[0]; $sKontaktEml=$a[1];
  }
  $sLnk=(KAL_ZusageLink==''?$sHttp.'kalender.php?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
  if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk='http://'.$sWww.$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
  foreach($aL as $a){
   $sMTx=str_replace('#D',$sTDat,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
   $sZDat='ID-NUMMER: '.sprintf('%04d',$a[0])."\nTERMIN-NR: ".sprintf('%04d',$a[1]); //Zusagedaten
   for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){ //Zusagedaten aufbereiten
    if($i==2) $a[2]=fKalAnzeigeDatum($a[2]); elseif($i==5) $a[5]=fKalAnzeigeDatum(substr($a[5],0,10)).substr($a[5],10);
    $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.(isset($a[$i])?str_replace('`,',';',$a[$i]):'');
   }
   $Mailer=new PlainMail();
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
   $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageLschBtr);
   $Mailer->Text=str_replace('#Z',trim($sZDat),str_replace('#A',$sLnk.(isset($aT[0])?$aT[0]:'??'),$sMTx));
   if(KAL_ZusageLschInfoAut&&$sKontaktEml>''){ //Mail an Terminautor
    $Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();
   }
   if(KAL_ZusageLschNzZusag){ //Mail an Zusagenden
    $Mailer->AddTo($a[8]); $Mailer->SetReplyTo($a[8]); $Mailer->Send();
  }}
 }else if(count($aA)>1&&(KAL_ZusageFreigabeMail||KAL_ZusageBstInfoAut)){ //Aktivierungsmails
  $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml=''; $sEml=$aA[8]; $sWww=fKalWww();
  $sMTx=KAL_TxZusageFreiMTx;
  $sZDat='STATUS: ';
  switch($nSta){
   case '1': $sZDat.=KAL_TxZusage1Status; break; case '2': $sZDat.=KAL_TxZusage2Status; break; case '0': $sZDat.=KAL_TxZusage0Status; break;
   case '-': $sZDat.=KAL_TxZusage3Status; break; case '*': $sZDat.=KAL_TxZusage4Status; break; case '7': $sZDat.=KAL_TxZusage7Status; break;
   default: $sZDat.='unklar??';
  }
  $sZDat.="\nID-NUMMER: ".sprintf('%04d',$aA[0])."\nTERMIN-NR: ".sprintf('%04d',$aA[1]);
  for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){ //Zusagedaten aufbereiten
   if($i==2) $aA[2]=fKalAnzeigeDatum($aA[2]); elseif($i==5) $aA[5]=fKalAnzeigeDatum(substr($aA[5],0,10)).substr($aA[5],10);
   $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.(isset($aA[$i])?str_replace('`,',';',$aA[$i]):'');
   $sMTx=str_replace('{'.str_replace('`,',';',$kal_ZusageFelder[$i]).'}',(isset($aA[$i])?str_replace('`,',';',$aA[$i]):''),$sMTx);
  }
  if((strpos($sMTx,'#D')>0||KAL_ZusageBstInfoAut)&&count($aT)>1){ //Termindaten aufbereiten
   $a=fKalTerminPlainText($aT,$DbO); $sTDat=$a[0]; $sKontaktEml=$a[1];
  }
  if($sEml){ //E-Mail an Zusager
   $sMTx=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",$sMTx))));
   require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail(); $sBtr=str_replace('#A',$sWww,KAL_TxZusageFreiBtr);
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sEml);
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
  }
  if(KAL_ZusageBstInfoAut&&strlen($sKontaktEml)>0){ //E-Mail an Autor
   $sMTx=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxZusageInfoMTx))));
   require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail(); $sBtr=str_replace('#A',$sWww,KAL_TxZusageFreiBtr);
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->AddTo($sKontaktEml); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); if($sEml) $Mailer->SetReplyTo($sEml);
   if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
 }}
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Terminummer!</p>';

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Zusagenübersicht zum Termin-Nr. <i>'.sprintf('%0'.KAL_NummerStellen.'d',$sTId).(isset($aT[1])?' - '.fKalAnzeigeDatum($aT[1]).' - '.(isset($aZ[0][4])?str_replace('`,',';',$aZ[0][4]):' (keine Zusagen)'):' (unbekannt)').'</i></p>';
echo $Msg.NL;
for($j=9;$j<=$nZusageFelder;$j++){
 if($kal_ZusageFelder[$j]=='ANZAHL'){$nAnzahlPos=$j; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$j]=KAL_ZusageNameAnzahl;}
}
?>

<form name="ZusageListe" action="zusageTermin.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $sTId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 echo    '<tr class="admTabl">'; //Kopfzeile
 echo NL.' <td style="white-space:nowrap;"><b>Z-Nr.</b></td>';
 for($j=2;$j<=$nZusageFelder;$j++) if(isset($aListFelder[$j])&&($i=$aListFelder[$j])){
  $sS=''; if($i<8&&$i!=4||$i==$nAnzahlPos) $sS=' style="text-align:center"';
  echo NL.' <td'.$sS.'><b>'.str_replace('`,',';',$kal_ZusageFelder[$i]).'</b></td>';
 }
 echo NL.'</tr>';

 foreach($aZ as $a){ //Datenzeilen ausgeben
  echo NL.'<tr class="admTabl">'; $nZId=$a[0]; if($nAnzahlPos) $nAnzahlSumme+=$a[$nAnzahlPos]; else $nAnzahlSumme++;
  echo NL.' <td style="white-space:nowrap;">'.($bLoeschen?'<input class="admCheck" type="checkbox" name="kal_L'.$nZId.'" value="1"'.(isset($aId[$nZId])&&$aId[$nZId]?' checked="checked"':'').' /> ':'').'<a href="zusageDetail.php?kal_Num='.$nZId.($sQ?'&amp;'.$sQ:'').$sQLst.'" title="Details dieser Zusage-Nr. '.$nZId.' anzeigen">'.sprintf('%04d',$nZId).'</a></td>';
  for($j=2;$j<=$nZusageFelder;$j++) if(isset($aListFelder[$j])&&$i=$aListFelder[$j]){$s=(isset($a[$i])?str_replace('`,',';',$a[$i]):''); $sS='';
   if(strlen($s)>0) switch($i){
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
     if($bFreigabe){$sLnkStaA='<a href="zusageTermin.php?kal_Num='.$sTId.($sQ?'&amp;'.$sQ:'').'&amp;kal_Zid='.$nZId.'&amp;kal_Status='.$sNeuSta.'">'; $sLnkStaE='</a>';}
     $s=$sLnkStaA.'<img src="'.$sHttp.'grafik/punkt'.$sImgSta.'" width="12" height="12" border="0">'.$sLnkStaE;
     break;
    case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); $sS=' style="text-align:center;"'; break;
    case 8: $s='<a href="zusageKontakt.php?kal_Num='.$nZId.($sQ?'&amp;'.$sQ:'').'"><img src="'.$sHttp.'grafik/iconMail.gif" style="margin-top:-2px;margin-bottom:-2px" width="16" height="16" border="0" title="'.$s.' kontaktieren"></a> '.$s; break;
    case $nAnzahlPos: $s=abs($s); $sS=' style="text-align:center;"'; break;
   }else $s='&nbsp;';
   echo NL.' <td'.$sS.'>'.$s.'</td>';
  }
  echo NL.'</tr>';
 }

 if($p=strpos('#'.$sQ,'kal_Lst')){
  if(substr($sQ,(--$p)-5,1)=='&') $i=5; elseif(substr($sQ,$p-1,1)=='&') $i=1; else $i=0;
  $bZurTrmLst=true; $sQL=substr_replace($sQ,'',$p-$i,9+$i);
 }else{$bZurTrmLst=false; $sQL=$sQ;}
?>
 <tr class="admTabl">
 <td>
  <?php if($bLoeschen){?><input class="admCheck" type="checkbox" name="kal_All" value="1" onClick="fSelAll(this.checked)" />&nbsp;<input type="image" name="LschBtn" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" align="top" border="0" title="markierte Zusagen löschen" /><?php }else echo '&nbsp;'?>
 </td>
 <td colspan="<?php echo count($aListFelder)-1;?>">
  insgesamt <b><?php echo $nAnzahlSumme; if($sKapaz>'0') echo ' von '.((int)$sKapaz) ?></b> Zusagen<?php if(file_exists('zusageEingabe.php')){?>, <a title="Zusage neu eingeben" href="zusageEingabe.php?kal_Trm=<?php echo $sTId.($sQ?'&amp;'.$sQ:'')?>"><img src="<?php echo $sHttp?>grafik/icon_Zusagen.gif" align="top" width="12" height="13" border="0" title="Zusage neu eingeben"> neue Zusage</a><?php } if($nAnzahlSumme>0){if(file_exists('zusageDruckTermin.php')){ ?>, <a title="Zusagen drucken" target="druck" onclick="druWin(this.href);return false;" href="zusageDruckTermin.php?kal_Num=<?php echo $sTId.($sQ?'&amp;'.$sQ:'')?>"><img src="<?php echo $sHttp?>grafik/iconDrucken.gif" align="top" width="16" height="16" border="0" title="Zusagen drucken"> drucken</a><?php }?>, <a title="alle Zusager kontaktieren<?php if(!KAL_Zusagen) echo ' - nur in der Vollversion'?>" href="zusage<?php echo(KAL_Zusagen?'Kontakt':'Termin')?>.php?kal_<?php echo(KAL_Zusagen?'Trm=':'Num=').$sTId.($sQ?'&amp;'.$sQ:'')?>"><img src="<?php echo $sHttp?>grafik/iconMail.gif" align="top" width="16" height="16" border="0" title="alle kontaktieren<?php if(!KAL_Zusagen) echo ' - nur in der Vollversion'?>"> E-Mail an alle</a> <?php if(!KAL_Zusagen) echo ' <span style="color:#557">(nur in der Vollversion)</span>';}?>
 </td>
 </tr>
</table>
<input type="hidden" name="kalLsch" value="<?php echo $sLschFrg?>" />
</form>

<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]</p>

<p class="admMeld">Detailinformationen zum Termin-Nr. <i><?php echo sprintf('%0'.KAL_NummerStellen.'d',($sTId))?></i></p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Termin-Nr.</td>
 <td><?php echo (count($aT)>1?sprintf('%0'.KAL_NummerStellen.'d',$aT[0]):'Termin nicht vorhanden!')?></td>
</tr>
<?php
if(count($aT)>1){
  $nFelder=count($kal_FeldName); if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i];
   if(($kal_DetailFeld[$i]>0&&$t!='p'&&$kal_FeldName[$i]!='TITLE'&&substr($kal_FeldName[$i],0,5)!='META-')||$t=='v'){
    if($s=str_replace('\n ',NL,str_replace('`,',';',$aT[$i]))){
     switch($t){
      case 't': $s=fKalBB($s); break; //Text
      case 'a': case 'k': case 'o': break; //Aufzählung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].' '.$s; else $s.=' '.$kal_WochenTag[$w];}
       }else{$s.=' '.$w;}
       break;
      case 'z': $s.=' '.KAL_TxUhr; break; //Uhrzeit
      case 'w': //Waehrung
       $s=(float)$s;
       if($s>0||!KAL_PreisLeer){
        $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
        if(KAL_Waehrung) $s.=' '.KAL_Waehrung;
       }else if(KAL_ZeigeLeeres) $s=' '; else $s='';
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w);
        $v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" style="margin-right:4px;" title="'.$u.'" />';
        $s.='<a title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="_blank">'.$v.$u.'</a>, ';
       }$s=substr($s,0,-2); break;
      case 'e':
       if(!KAL_SQL) $s=fKalDeCode($s);
       $s='<a href="mailto:'.$s.'" target="_blank"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.'"></a>&nbsp;<a href="mailto:'.$s.'" target="_blank">'.$s.'</a>';
       break;
      case 'c':
       if(!KAL_SQL) $s=fKalDeCode($s);
       if(file_exists('eingabeKontakt.php')) $s='<a href="eingabeKontakt.php?kal_Num='.$aT[0].($sQ?'&amp;'.$sQ:'').'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="'.$s.'"> '.$s.'</a>';
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$aT[0].'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'-')+1,-4).'" />';
       break;
      case 'f': //Datei
       $s='<a class="kalText" href="'.$sHttp.KAL_Bilder.$aT[0].'~'.$s.'" target="_blank">'.$s.'</a>'; break;
      case 'u':
       if($nId=(int)$s){
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
        }}
       }else $s=KAL_TxAutor0000;
       break;
      default: $s='';
     }//switch
    }
    if(strlen($s)>0){
     $sFN=$kal_FeldName[$i];
     if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
     if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     echo "\n".'<tr class="admTabl">';
     echo "\n".' <td width="10%" valign="top">'.$sFN.'</td>';
     echo "\n".' <td>'.$s."</td>\n</tr>";
    }
   }
  }
}?>
</table><br>
<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]</p>

<?php
echo fSeitenFuss();

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