<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusagedetail','<script language="JavaScript" type="text/javascript">
 function druWin(sURL){dWin=window.open(sURL,"druck","width=600,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus();}
</script>','ZZl');

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
$sQ=$_SERVER['QUERY_STRING']; $aZ=array(); $aL=array(); $aT=array();
if($p=strpos($sQ,'kal_Status')){if(substr($sQ,$p-5,1)=='&') $p-=5; elseif(substr($sQ,$p-1,1)=='&') $p-=1; $sQ=substr($sQ,0,$p);}
if($p=strpos($sQ,'kal_',8)) $sQ=substr($sQ,$p); else $sQ='';
$bFreigabe=file_exists('zusageFreigabe.php'); $bAendern=file_exists('zusageAendern.php'); $bZurTrmLst=false;

if($sZId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'')){
 $nSta=(isset($_GET['kal_Status'])?substr($_GET['kal_Status'],0,1):''); $sTrmNr='';
 if(strlen($nSta)>0&&$bFreigabe){ //Status aendern
  $sLNr=''; $sLschUsr='';
  if(!KAL_SQL){ //Textdatei
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sZId.';'; $p=strlen($s); $bNeu=false;
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
    $sZl=$aD[$i]; for($j=1;$j<6;$j++) $p=strpos($sZl,';',$p)+1;
    if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
     if((int)substr($sZl,$p,1)==1-$nSta||substr($sZl,$p,1)=='2'){$aD[$i]=substr_replace($sZl,$nSta,$p,1); $bNeu=true;}
    }elseif(substr($sZl,$p,1)=='-'||substr($sZl,$p,1)=='*'){ //widerrufen
     $a=explode(';',rtrim($sZl)); $sNr=(int)$a[1]; $sLschUsr=$a[8]; $a[8]=fKalDecode($a[8]); $aL=$a; $aD[$i]=''; $bNeu=true;
    }
    break;
   }
   if($sLschUsr&&$sNr>0){ //fruehere Zusagen loeschen
    $sTrmNr=$sNr; $sNr=';'.$sNr.';'; $p=strlen($sNr);
    for($i=1;$i<$nSaetze;$i++){
     $s=$aD[$i]; if(substr($s,strpos($s,';'),$p)==$sNr){ //gefunden
      $a=explode(';',$s,10); if($sLschUsr==$a[8]){$sLNr.=', '.$a[0]; $aD[$i]=''; $bNeu=true;}
   }}}
   if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //neu schreiben
    fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
    $Msg='<p class="admErfo">Die Zusage Nr. '.$sZId.$sLNr.' wurde '.($nSta=='1'?'aktiv geschaltet':($nSta=='0'?'inaktiv geschaltet':'widerrufen')).'.</p>';
    if($nSta=='1'){ //aktiviert
     $aZ=explode(';',rtrim($sZl)); $sEml=fKalDeCode($aZ[8]);
     if($sNr=(int)$aZ[1]){//Termindaten holen
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sNr.';'; $p=strlen($s);
      for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
     }
    }elseif(count($aL)&&($sNr=(int)$aL[1])){ //widerrufen
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sNr.';'; $p=strlen($s);
     for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
    }
   }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
  }elseif($DbO){ //bei SQL
   if($nSta=='0'||$nSta=='1'){ //aktiv/inaktiv
    if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET aktiv="'.$nSta.'" WHERE nr="'.$sZId.'"')){
     $Msg='<p class="admErfo">Die Zusage Nr. '.$sZId.' wurde '.($nSta?'':'in').'aktiv geschaltet.</p>';
     if($nSta==1) if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$sZId.'"')){
      if($aZ=$rR->fetch_row()) $sEml=$aZ[8]; $rR->close();
      if($sNr=(int)$aZ[1]){//Termindaten holen
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sNr.'"')){
        $aT=$rR->fetch_row(); $rR->close();
    }}}}else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
   }else{ //widerrufen
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$sZId.'"')){
     if($a=$rR->fetch_row()){$sNr=(int)$a[1]; $sLschUsr=$a[8]; $aL=$a;} $rR->close();
     if($sLschUsr&&$sNr>0){
      $DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$sZId.'" AND email="'.$sLschUsr.'"'); $sTrmNr=$sNr;
      if($rR=$DbO->query('SELECT nr,termin FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       while($a=$rR->fetch_row()) $sLNr.=', '.$a[0]; $rR->close();
      }
      if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE termin="'.$sNr.'" AND email="'.$sLschUsr.'"')){
       $Msg='<p class="admErfo">Die Zusage Nr. '.$sZId.$sLNr.' wurde widerrufen.</p>';
      }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sNr.'"')){ //Termin holen
       $aT=$rR->fetch_row(); $rR->close();
      }
     }
    }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
   }
  }

  if(count($aL)&&(KAL_ZusageLschInfoAut||KAL_ZusageLschNzZusag)){ //Loeschmails
   $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml=''; $sWww=fKalWww();
   if((strpos(KAL_TxZusageLschMTx,'#D')>0)&&count($aT)>1){ //Termindaten aufbereiten
    array_splice($aT,1,1); $a=fKalTerminPlainText($aT,$DbO); $sTDat=$a[0]; $sKontaktEml=$a[1];
   }
   $sLnk=(KAL_ZusageLink==''?$sHttp.'kalender.php?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).'kal_Aktion=detail&kal_Intervall=%5B%5D&kal_Nummer=';
   if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk='http://'.$sWww.$sLnk; $sLnk=str_replace('&amp;','&',$sLnk);
   $sMTx=str_replace('#D',$sTDat,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
   $sZDat='ID-NUMMER: '.sprintf('%04d',$aL[0])."\nTERMIN-NR: ".sprintf('%04d',$aL[1]); //Zusagedaten
   for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){ //Zusagedaten aufbereiten
    if($i==2) $aL[2]=fKalAnzeigeDatum($aL[2]); elseif($i==5) $aL[5]=fKalAnzeigeDatum(substr($aL[5],0,10)).substr($aL[5],10);
    $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.str_replace('`,',';',$aL[$i]);
   }
   require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
   if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
   $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
   $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageLschBtr);
   $Mailer->Text=str_replace('#Z',trim($sZDat),str_replace('#A',$sLnk.(isset($aT[0])?$aT[0]:'??'),$sMTx));
   if(KAL_ZusageLschInfoAut&&$sKontaktEml>''){$Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();} //Mail an Terminautor
   if(KAL_ZusageLschNzZusag){$Mailer->AddTo($aL[8]); $Mailer->SetReplyTo($aL[8]); $Mailer->Send();} //Mail an Zusagenden
  }elseif(count($aZ)>1&&(KAL_ZusageFreigabeMail||KAL_ZusageBstInfoAut)){ //Aktivierungsmails
   $sTDat="\n\nTermin nicht gefunden\n\n"; $sKontaktEml=''; $sWww=fKalWww();
   $sMTx=KAL_TxZusageFreiMTx;
   $sZDat='STATUS: ';
   switch($nSta){
    case '1': $sZDat.=KAL_TxZusage1Status; break; case '2': $sZDat.=KAL_TxZusage2Status; break; case '0': $sZDat.=KAL_TxZusage0Status; break;
    case '-': $sZDat.=KAL_TxZusage3Status; break; case '*': $sZDat.=KAL_TxZusage4Status; break; case '7': $sZDat.=KAL_TxZusage7Status; break; 
    default: $sZDat.='unklar??';
   }
   $sZDat.="\nID-NUMMER: ".sprintf('%04d',$aZ[0])."\nTERMIN-NR: ".sprintf('%04d',$aZ[1]);
   for($i=2;$i<=$nZusageFelder;$i++) if($i!=6){ //Zusagedaten aufbereiten
    if($i==2) $aZ[2]=fKalAnzeigeDatum($aZ[2]); elseif($i==5) $aZ[5]=fKalAnzeigeDatum(substr($aZ[5],0,10)).substr($aZ[5],10); elseif($i==8) $aZ[8]=$sEml;
    $sZDat.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[$i])).': '.(isset($aZ[$i])?str_replace('`,',';',$aZ[$i]):'');
    $sMTx=str_replace('{'.str_replace('`,',';',$kal_ZusageFelder[$i]).'}',(isset($aZ[$i])?str_replace('`,',';',$aZ[$i]):''),$sMTx);
   }
   if((strpos($sMTx,'#D')>0||KAL_ZusageBstInfoAut)&&count($aT)>1){ //Termindaten aufbereiten
    array_splice($aT,1,1); $a=fKalTerminPlainText($aT,$DbO); $sTDat=$a[0]; $sKontaktEml=$a[1];
   }//Termindaten
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
 }//Status

 $aZ=array(); $aT=array(); // Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sZId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDeCode($aZ[8]); break;}
  if((isset($aZ[1])&&$sNr=$aZ[1])||($sNr=$sTrmNr)){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sNr.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
  }
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr='.$sZId)){
   $aZ=$rR->fetch_row(); $rR->close();
   if((isset($aZ[1])&&$sNr=$aZ[1])||($sNr=$sTrmNr)){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id='.$sNr)){
     $aT=$rR->fetch_row(); $rR->close();
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Zusagenummer!</p>';

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Detailinformationen zur Zusage-Nr. <i>'.sprintf('%0'.KAL_NummerStellen.'d',$sZId).'</i></p>';
echo $Msg.NL;

$sLnkAend=''; $sLnkAenU=''; $sLnkDru=''; $sQL='';
if($p=strpos('#'.$sQ,'kal_Lst')){
 if(substr($sQ,(--$p)-5,1)=='&') $i=5; elseif(substr($sQ,$p-1,1)=='&') $i=1; else $i=0;
 $bZurTrmLst=true; $sQL=substr_replace($sQ,'',$p-$i,9+$i);
}else{$bZurTrmLst=false; $sQL=$sQ;}
if(count($aZ)){
  $sSta=$aZ[6]; $sNeuSta='0'; $sImgSta='Grn.gif" title="gültig'; $sLnkStaA=''; $sLnkStaE='';
  if($sSta=='0'){$sNeuSta='1'; $sImgSta='Rot.gif" title="vorgemerkt'.($bFreigabe?' - jetzt akzeptieren':'');}
  elseif($sSta=='2'){$sNeuSta='1'; $sImgSta='RtGn.gif" title="bestätigt'.($bFreigabe?' - jetzt akzeptieren':'');}
  elseif($sSta=='-'){$sNeuSta='-'; $sImgSta='RotX.gif" title="Widerruf vorgemerkt'.($bFreigabe?' - jetzt widerrufen':'');}
  elseif($sSta=='*'){$sNeuSta='-'; $sImgSta='RtGnX.gif" title="Widerruf bestätigt'.($bFreigabe?' - jetzt widerrufen':'');}
  elseif($sSta=='7'){$sNeuSta='1'; $sImgSta='Glb.gif" title="auf der Warteliste'.($bFreigabe?' - jetzt akzeptieren':'');}
  if($bFreigabe){$sLnkStaA='<a href="zusageDetail.php?kal_Num='.$sZId.($sQ?'&amp;'.$sQ:'').'&amp;kal_Status='.$sNeuSta.'">'; $sLnkStaE='</a>';}
  if($bAendern){$sLnkAend='<a href="zusageAendern.php?kal_Num='.$sZId.($sQ?'&amp;'.$sQ:'').'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Zusage &auml;ndern"></a> '; $sLnkAenU=' &nbsp; [ <a href="zusageAendern.php?kal_Num='.$sZId.($sQ?'&amp;'.$sQ:'').'">Zusage &auml;ndern</a> ]';}
  if(KAL_Zusagen) $sLnkDru=' <a href="zusageDruckDetail.php?kal_Num='.$sZId.($sQ?'&amp;'.$sQ:'').'" target="druck" onclick="druWin(this.href);return false;" title="Zusagen drucken">';
  if($p=strpos('#'.$sQ,'kal_Lst')){
   if(substr($sQ,(--$p)-5,1)=='&') $i=5; elseif(substr($sQ,$p-1,1)=='&') $i=1; else $i=0;
   $bZurTrmLst=true; $sQL=substr_replace($sQ,'',$p-$i,9+$i);
  }else{$bZurTrmLst=false; $sQL=$sQ;}
?>

<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Zusage-Nr.</td>
 <td><?php echo $sLnkAend.$sLnkStaA.'<img src="'.$sHttp.'grafik/punkt'.$sImgSta.'" width="12" height="12" border="0">'.$sLnkStaE.sprintf(' %0'.KAL_NummerStellen.'d',$aZ[0]).($sLnkDru?$sLnkDru:' ').'<img src="'.$sHttp.'grafik/iconDrucken.gif" align="top" width="16" height="16" border="0" title="'.($sLnkDru?'Zusage drucken':'nur in der Vollversion').'">'.($sLnkDru?'</a>':'');?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Buchungszeit</td>
 <td><?php echo fKalAnzeigeDatum($aZ[5]).','.substr($aZ[5],10).' '.KAL_TxUhr ?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Terminzeit</td>
 <td><?php echo fKalAnzeigeDatum($aZ[2]); if($aZ[3]) echo ', '.$aZ[3].' '.KAL_TxUhr ?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Veranstaltung</td>
 <td><?php echo str_replace('`,',';',$aZ[4]) ?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Benutzer</td>
 <td><?php if($aZ[7]>'0'){?><a href="nutzerKontakt.php?kal_Num=<?php echo $aZ[7] ?>"><img src="<?php echo $sHttp ?>grafik/iconMail.gif" width="16" height="16" border="0" title="Nutzer <?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[7]) ?> kontaktieren"></a> <a href="nutzerKontakt.php?kal_Num=<?php echo $aZ[7] ?>"><?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[7]) ?></a><?php }else echo 'unbekannt'?></td>
</tr>
<tr class="admTabl">
 <td width="10%">E-Mail</td>
 <td><a href="zusageKontakt.php?kal_Num=<?php echo $sZId.($sQ?'&amp;'.$sQ:'') ?>"><img src="<?php echo $sHttp ?>grafik/iconMail.gif" width="16" height="16" border="0" title="<?php echo $aZ[8] ?> kontaktieren"></a> <a href="zusageKontakt.php?kal_Num=<?php echo $sZId.($sQ?'&amp;'.$sQ:'') ?>"><?php echo $aZ[8] ?></a></td>
</tr>
<?php
 $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 for($i=9;$i<=$nZusageFelder;$i++){
  $s=(isset($aZ[$i])?str_replace('`,',';',$aZ[$i]):''); $t=$aZusageFeldTyp[$i];
  if($t=='w'){
   $s=(float)$s;
   if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen).' '.KAL_Waehrung; else $s='';
  }elseif($t=='j'){if($s=='J') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;}
?>
<tr class="admTabl">
 <td width="10%"><?php $sNam=str_replace('`,',';',$kal_ZusageFelder[$i]); if($sNam=='ANZAHL'&&strlen(KAL_ZusageNameAnzahl)>0) $sNam=KAL_ZusageNameAnzahl; echo $sNam ?></td>
 <td><?php echo (strlen($s)>0?$s:'&nbsp;')?></td>
</tr>
<?php }?>
</table><br>
<?php }?>
<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]<?php echo $sLnkAenU; if($sLnkDru) echo ' &nbsp; [ '.$sLnkDru.'Zusage drucken</a> ]'?></p>

<p class="admMeld">Detailinformationen zum Termin-Nr. <i><?php echo sprintf('%0'.KAL_NummerStellen.'d',(isset($aZ[1])?$aZ[1]:$sTrmNr))?></i></p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Termin-Nr.</td>
 <td><?php echo (count($aT)>1?sprintf('%0'.KAL_NummerStellen.'d',$aT[0]):'Termin nicht vorhanden!')?></td>
</tr>
<?php
if(count($aT)>1){
  $nFelder=count($kal_FeldName); array_splice($aT,1,1); if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i];
   if(($kal_DetailFeld[$i]>0&&$t!='p'&&$kal_FeldName[$i]!='TITLE'&&substr($kal_FeldName[$i],0,5)!='META-')||$t=='v'){
    if($s=str_replace('\n ',NL,str_replace('`,',';',$aT[$i]))){
     switch($t){
      case 't': $s=fKalBB($s); break; //Text
      case 'a': case 'k': case 'o': break; //Aufzaehlung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].' '.$s; else $s.=' '.$kal_WochenTag[$w];}
       }else{$s.=', '.$w.' '.KAL_TxUhr;}
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