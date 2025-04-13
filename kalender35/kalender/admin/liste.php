<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminliste','<script language="JavaScript" type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.TerminListe.length;++i)
   if(self.document.TerminListe.elements[i].type=="checkbox") self.document.TerminListe.elements[i].checked=bStat;
 }
 function druWin(sURL){dWin=window.open(sURL,"druck","width=600,height=580,left=5,top=3,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus();}
 function fSubmitNavigFrm(){document.NavigFrm.submit();}
</script>
<link rel="stylesheet" type="text/css" href="'.KALPFAD.'kalStyles.css">','TTl');

$bOK=false; $bLsch=false; $bJKop=false;
$nFelder=count($kal_FeldName); if(KAL_NListeAnders) $kal_ListenFeld=$kal_NListenFeld; $sLschNun=''; $sJKopNun='';
if(($bLsch=(isset($_POST['LschBtnO_x'])&&($_POST['LschBtnO_x']>0||$_POST['LschBtnO_y']>0)||isset($_POST['LschBtnU_x'])&&($_POST['LschBtnU_x']>0||$_POST['LschBtnU_y']>0)))||($bJKop=(isset($_POST['JKopBtnO_x'])&&($_POST['JKopBtnO_x']>0||$_POST['JKopBtnO_y']>0)||isset($_POST['JKopBtnU_x'])&&($_POST['JKopBtnU_x']>0||$_POST['JKopBtnU_y']>0)))){ //Termine loeschen/Jahreskopie
 $aId=array(); foreach($_POST as $k=>$xx) if(substr($k,4,2)=='CB') $aId[(int)substr($k,6)]=true; //Loeschnummern
 if(count($aId)>0){
  if($bLsch&&file_exists('loeschen.php')){
   if($_POST['LschNun']!='1'){$sLschNun='1'; $Msg='<p class="admFehl">'.KAL_TxLoescheFrag.'</p>';} //nachfragen
   else{ //jetzt loeschen
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
      if(KAL_ZusageSystem){//Zusagenliste kuerzen
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
   }//jetzt loeschen
  }elseif($bJKop&&file_exists('kopieren.php')){//Kopie ins naechste Jahr
   if($_POST['JKopNun']!='1'){$sJKopNun='1'; $Msg='<p class="admFehl">Die markierten Termine wirklich ins neue Jahr kopieren?</p>';} //nachfragen
   else{ //jetzt kopieren
    $m=0; $nD2=0; if(KAL_EndeDatum) for($j=$nFelder-1;$j>1;$j--) if($kal_FeldType[$j]=='d') $nD2=$j;
    if(!KAL_SQL){ //Textdatei
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $aTmp=array(); $aNeu=array(); $aOnl=array();
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $sZ=rtrim($aD[$i]); $aZ=explode(';',$sZ); $sOnl=$aZ[1]; array_splice($aZ,1,1);
      $nId=max($nId,(int)$aZ[0]); $aTmp[$aZ[0].';'.$sOnl]=substr($sZ,strlen($aZ[0])+3);
      if(isset($aId[(int)$aZ[0]])&&$aId[(int)$aZ[0]]){
       if($nD2>0) if($sD=substr($aZ[$nD2],0,10)){//Zweitdatum
        $sD=(1+substr($sD,0,4)).substr($sD,4); if(substr($sD,5,5)=='02-29') $sD=substr($sD,0,9).'8';
        $sD.=rtrim(@date(' w',@mktime(12,0,0,substr($sD,5,2),substr($sD,8,2),substr($sD,0,4)))); $aZ[$nD2]=$sD;
       }
       $sD=substr($aZ[1],0,10); $sD=(1+substr($sD,0,4)).substr($sD,4); if(substr($sD,5,5)=='02-29') $sD=substr($sD,0,9).'8';
       $sD.=rtrim(@date(' w',@mktime(12,0,0,substr($sD,5,2),substr($sD,8,2),substr($sD,0,4)))); $aZ[1]=$sD;
       $aOnl[$m]=$sOnl; $aNeu[$m++]=$aZ;
      }
     }
     $nSaetze=count($aNeu); $aD=array();
     for($i=0;$i<$nSaetze;$i++){
      $aZ=$aNeu[$i]; $sZ=$aZ[1]; $nId++;
      for($j=2;$j<$nFelder;$j++){$t=$kal_FeldType[$j]; //Bilder und Dateien kopieren, Eintragsdatum
       if(($t=='b')&&($s=$aZ[$j])){
        $s1=substr($s,0,strpos($s,'|')); if(!@copy(KAL_Pfad.KAL_Bilder.$aZ[0].'-'.$s1,KAL_Pfad.KAL_Bilder.$nId.'-'.$s1));
        $s2=substr($s,strpos($s,'|')+1); if(!@copy(KAL_Pfad.KAL_Bilder.$aZ[0].'_'.$s2,KAL_Pfad.KAL_Bilder.$nId.'_'.$s2));
       }elseif(($t=='f')&&($s=$aZ[$j])){ if(!@copy(KAL_Pfad.KAL_Bilder.$aZ[0].'~'.$s,KAL_Pfad.KAL_Bilder.$nId.'~'.$s));}
       elseif($t=='@'&&KAL_EintragszeitNeu&&$kal_FeldName[$j]!='ZUSAGE_BIS') $aZ[$j]=date('Y-m-d H:i');
       $sZ.=';'.$aZ[$j];
      }$aTmp[$nId.';'.$aOnl[$i]]=$sZ;
     }
     $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v ){$aD[]=$k.';'.$v.NL;}
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){//neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
      $aId=array(); $Msg='<p class="admErfo">'.$nSaetze.' markierte Termine wurden ins neue Jahr kopiert.</p>';
     }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
    }elseif($DbO){ //bei SQL
     foreach($aId as $k=>$xx){
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$k.'"')){
       $aZ=$rR->fetch_row(); array_shift($aZ); $rR->close();
       if($nD2>0) if($sD=substr($aZ[$nD2],0,10)){//Zweitdatum
        $sD=(1+substr($sD,0,4)).substr($sD,4); if(substr($sD,5,5)=='02-29') $sD=substr($sD,0,9).'8';
        $sD.=rtrim(@date(' w',@mktime(12,0,0,substr($sD,5,2),substr($sD,8,2),substr($sD,0,4)))); $aZ[$nD2]=$sD;
       }
       $sD=substr($aZ[1],0,10); $sD=(1+substr($sD,0,4)).substr($sD,4); if(substr($sD,5,5)=='02-29') $sD=substr($sD,0,9).'8';
       $sD.=rtrim(@date(' w',@mktime(12,0,0,substr($sD,5,2),substr($sD,8,2),substr($sD,0,4))));
       $sZ='kal_1'; $sV='"'.$sD.'"';
       for($j=2;$j<$nFelder;$j++){
        if($kal_FeldType[$j]=='@'&&KAL_EintragszeitNeu&&$kal_FeldName[$j]!='ZUSAGE_BIS') $aZ[$j]=date('Y-m-d H:i'); //Eintragsdatum
        $sZ.=',kal_'.$j; $sV.=',"'.str_replace('"','\"',$aZ[$j]).'"';
       }
       if(!strpos($sZ,',periodik')){$sZ.=',periodik'; $sV.=',""';}
       if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online,'.$sZ.') VALUES("1",'.$sV.')')){
        if($nId=$DbO->insert_id){
         for($j=2;$j<$nFelder;$j++){$t=$kal_FeldType[$j]; //Bilder und Dateien kopieren
          if(($t=='b')&&($s=$aZ[$j])){
           $s1=substr($s,0,strpos($s,'|')); if(!@copy(KAL_Pfad.KAL_Bilder.$k.'-'.$s1,KAL_Pfad.KAL_Bilder.$nId.'-'.$s1));
           $s2=substr($s,strpos($s,'|')+1); if(!@copy(KAL_Pfad.KAL_Bilder.$k.'_'.$s2,KAL_Pfad.KAL_Bilder.$nId.'_'.$s2));
          }elseif(($t=='f')&&($s=$aZ[$j])){ if(!@copy(KAL_Pfad.KAL_Bilder.$k.'~'.$s,KAL_Pfad.KAL_Bilder.$nId.'~'.$s));}
         } $m++;
        }else $Msg.='<p class="admFehl">Speicherfehler beim alten Termin '.$k.'.</p>';
       }else $Msg.='<p class="admFehl">Einfügefehler beim alten Termin '.$k.'.</p>';
      }else $Msg.='<p class="admFehl">Abfragefehler beim alten Termin '.$k.'.</p>';
     }
     if($m){$aId=array(); $Msg='<p class="admErfo">'.$m.' markierte Termine wurden ins neue Jahr kopiert.</p>';}
    }//SQL
   }//jetzt kopieren
  }//kopieren
 }else $Msg='<p class="admMeld">'.KAL_TxKeineAenderung.'</p>';
}//LschForm
if(isset($_GET['kal_Sta'])&&isset($_GET['kal_Num'])){ //online/offline
 $nId=(int)$_GET['kal_Num']; $sSta=$_GET['kal_Sta'];
 if(!KAL_SQL){ //Textdatei
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $s=substr($aD[$i],0,12); $p=strpos($s,';');
   if((int)substr($s,0,$p)==$nId){$aD[$i]=substr_replace($aD[$i],$sSta,$p+1,1);break;}
  }
  if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){
   fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bOK=true; $Msg='<p class="admMeld">Der Termin wurde o'.($sSta=='1'?'n':'ff').'line geschaltet.</p>';
  }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
 }elseif($DbO){ //bei SQL
  if($DbO->query('UPDATE IGNORE '.KAL_SqlTabT.' SET online="'.$sSta.'" WHERE id='.$nId)){
   $Msg='<p class="admMeld">Der Termin wurde o'.($sSta=='1'?'n':'ff').'line geschaltet.</p>';
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }
}//online/offline

if(ADM_ListenZusagSp>0&&KAL_ZusageSystem){ //eventuell Zusagedaten holen
 $aZusageZahl=array();
 if(KAL_ZusageSystem&&(strpos('x'.KAL_TxListenZusagZMuster,'#Z')>0||strpos('x'.KAL_TxListenZusagZMuster,'#R')>0)||((($n=array_search('#',$kal_FeldType))&&$kal_ListenFeld[$n]>0))){
  $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageAnzahlPos=array_search('ANZAHL',$kal_ZusageFelder);
  if(!KAL_SQL){//
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $n=count($aD); $m=max(9,$nZusageAnzahlPos+2);
   for($i=1;$i<$n;$i++){
    $a=explode(';',$aD[$i],$m); $k=(int)$a[1];
    if($nZusageAnzahlPos>0) if($z=(int)$a[$nZusageAnzahlPos]) if($a[6]=='1'||!KAL_ZaehleAktiveZusagen) if(isset($aZusageZahl[$k])) $aZusageZahl[$k]+=$z; else $aZusageZahl[$k]=$z;
   }
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,termin,aktiv,benutzer'.($nZusageAnzahlPos>0?',dat_'.$nZusageAnzahlPos:'').' FROM '.KAL_SqlTabZ)){
    while($a=$rR->fetch_row()){
     $k=(int)$a[1];
     if($nZusageAnzahlPos>0) if($z=(int)$a[4]) if($a[2]=='1'||!KAL_ZaehleAktiveZusagen) if(isset($aZusageZahl[$k])) $aZusageZahl[$k]+=$z; else $aZusageZahl[$k]=$z;
    }
    $rR->close();
  }}
 }
}

$aD=array(); $aSpalten=array(); $nSpalten=0; $aQ=array(); $sQ=''; $nDatFeld2=0; $bOhneGrenze=false; //Abfrageparameter aufbereiten
for($i=0;$i<$nFelder;$i++){ //Abfrageparameter aufbereiten
 $t=$kal_FeldType[$i]; $aSpalten[$kal_ListenFeld[$i]]=$i;
 $s=(isset($_POST['kal_'.$i.'F1'])?$_POST['kal_'.$i.'F1']:(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s; if($i<=1) $bOhneGrenze=true;
  if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else $a1Filt[$i]=fKalNormDatum($s);
 }
 $s=(isset($_POST['kal_'.$i.'F2'])?$_POST['kal_'.$i.'F2']:(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else{$a2Filt[$i]=fKalNormDatum($s); if($i==1) $bOhneGrenze=true;}
  if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='0';}
  elseif($t=='j'||$t=='v') if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='';
 }
 $s=(isset($_POST['kal_'.$i.'F3'])?$_POST['kal_'.$i.'F3']:(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:''));
 if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;kal_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
 if($t=='d'&&$i>1&&$nDatFeld2==0&&KAL_EndeDatum) $nDatFeld2=$i; //2.Datum
}
$sIntervallAnfang=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); $sIntervallEnde='99';
if(isset($_GET['kal_Archiv'])&&$_GET['kal_Archiv']||isset($_POST['kal_Archiv'])&&$_POST['kal_Archiv']){$bArchiv=true; $sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';} else $bArchiv=false;
if($bOhneGrenze){$sIntervallAnfang='00'; $sIntervallEnde='99'; $bArchiv=false;}

if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $bZeigeOnl=(isset($_GET['kal_Onl'])?(bool)$_GET['kal_Onl']:ADM_ZeigeOnline);
 $bZeigeOfl=(isset($_GET['kal_Ofl'])?(bool)$_GET['kal_Ofl']:ADM_ZeigeOffline);
 $bZeigeVmk=(isset($_GET['kal_Vmk'])?(bool)$_GET['kal_Vmk']:ADM_ZeigeVormerk);
}else{//POST
 $bZeigeOnl=(isset($_POST['kal_Onl'])?(bool)$_POST['kal_Onl']:false);
 $bZeigeOfl=(isset($_POST['kal_Ofl'])?(bool)$_POST['kal_Ofl']:false);
 $bZeigeVmk=(isset($_POST['kal_Vmk'])?(bool)$_POST['kal_Vmk']:false);
}
if(!($bZeigeOfl||$bZeigeVmk)) $bZeigeOnl=true;
if($bZeigeOnl!=ADM_ZeigeOnline) $sQ.='&amp;kal_Onl='.($bZeigeOnl?'1':'0');
if($bZeigeOfl!=ADM_ZeigeOffline) $sQ.='&amp;kal_Ofl='.($bZeigeOfl?'1':'0');
if($bZeigeVmk!=ADM_ZeigeVormerk) $sQ.='&amp;kal_Vmk='.($bZeigeVmk?'1':'0');

$aSpalten[0]=0; $nSpalten=count($aSpalten); $aTmp=array(); $aIdx=array(); $nKapPos=0; //Daten bereitstellen
if(ADM_ListenZusagSp>0&&KAL_ZusageSystem) $nKapPos=(int)array_search('KAPAZITAET',$kal_FeldName);
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //über alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sId=(int)$a[0]; $sSta=$a[1];
  $b=($sSta=='1'&&$bZeigeOnl||$sSta=='3'&&KAL_AendernLoeschArt==3&&$bZeigeOnl||$sSta=='0'&&$bZeigeOfl||$sSta=='2'&&$bZeigeVmk); array_splice($a,1,1);
  $sAnfangDat=substr($a[1],0,10); $sEndeDat=$sAnfangDat;
  if(KAL_EndeDatum&&$nDatFeld2>0) if(!$sEndeDat=substr($a[$nDatFeld2],0,10)) $sEndeDat=$sAnfangDat;
  $b=$b&&(ADM_ZeigeAltes||(KAL_EndeDatum?$sEndeDat:$sAnfangDat)>=$sIntervallAnfang); //kommend oder laufend
  if($b&&$bArchiv) if($sAnfangDat>$sIntervallEnde) $b=false; //Archivfilter
  if($b&&isset($a1Filt)&&is_array($a1Filt)){
   reset($a1Filt);
   foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
    $t=$kal_FeldType[$j]; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
     if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
    }elseif($t=='d'){ //Datum
     $s=substr($a[$j],0,10); //$s Datensatzdatum
     if($j==1&&KAL_EndeDatum){ //Termindatum
      if(!$sEndeDatum=substr($a[$nDatFeld2],0,10)) $sEndeDatum=$s;
      if(empty($w)){if($s>$v||$sEndeDatum<$v) $b=false;} elseif($s>$w||$sEndeDatum<$v) $b=false;
     }else{if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;} //sonstiges Datum
    }elseif($t=='@'){ //EintragsDatum
     $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
    }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
     $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
     $s=floatval(str_replace(',','.',$a[$j]));
     if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
    }elseif($t=='o'){
     if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
     if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $b=false;
    }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $b=false;}}
   }
  }
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v)
    if($kal_FeldType[$j]!='o'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}}
    else{if(substr($a[$j],0,strlen($v))==$v){$b=false; break;}}
  }
  if($b){ //Datensatz gueltig
   $aTmp[$sId]=array($sId);
   if(ADM_ListenIndex==1) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
   elseif(ADM_ListenIndex>1){ //andere Sortierung
    $s=strtoupper(strip_tags($a[ADM_ListenIndex])); $t=$kal_FeldType[ADM_ListenIndex];
    for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
     if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
    if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
    elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
    $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
   }
   elseif(ADM_ListenIndex==0) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$sId); //nach Nr
   for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace('\n ',NL,str_replace('`,',';',$a[$aSpalten[$j]]));
   $aTmp[$sId][]=$sSta;
   if($nKapPos>0) if($w=$a[$nKapPos]) $aTmp[$sId]['KAP']=$w; //Kapazitaetsspalte
  }
 }$aD=array();
}elseif($DbO){ //SQL
 if($sIntervallAnfang>'00'&&!ADM_ZeigeAltes){
  if($nDatFeld2==0||!KAL_EndeDatum) $s=' AND kal_1>"'.$sIntervallAnfang.'"';
  else $s=' AND(kal_'.$nDatFeld2.'>"'.$sIntervallAnfang.'" OR kal_1>"'.$sIntervallAnfang.'")';
 }elseif($bArchiv) $s=' AND kal_1<="'.$sIntervallEnde.'~"'; else $s='';
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  $s.=' AND(kal_'.$j; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); $t=($kal_FeldType[$j]); //$v Suchwort1, $w Suchwort2
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
   $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.$w.'%"';
  }elseif($t=='d'){
   if($j==1&&KAL_EndeDatum){ //Termindatum
    if(empty($w)){$s.='<"'.$v.'~" AND kal_'.($nDatFeld2==0?1:$nDatFeld2).'>"'.$v.'" OR kal_'.$j.' LIKE "'.$v.'%"';} // nur 1 Wert
    else{$s.=' BETWEEN "'.$v.'" AND "'.$w.'~" OR kal_'.($nDatFeld2==0?1:$nDatFeld2).' BETWEEN "'.$v.'" AND "'.$w.'~"';}
   }else{if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';} //sonstiges Datum
  }elseif($t=='@'){
   if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
  }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
   $v=str_replace(',','.',$v);
   if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
  }elseif($t=='o'){
   $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
  }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
  $s.=')';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $t=$kal_FeldType[$j];
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
   $s.=' AND NOT(kal_'.$j.' LIKE "%'.$v.'%")';
  elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
 }
 $t=''; $nListenIdx=0; $i=0; $s=str_replace('kal_0','id',$s);
 for($j=1;$j<$nSpalten;$j++){$t.=',kal_'.$aSpalten[$j]; if($aSpalten[$j]==ADM_ListenIndex) $nListenIdx=$j;}
 if($nListenIdx==0&&ADM_ListenIndex>0){$t.=',kal_'.ADM_ListenIndex; $nListenIdx=$j;}
 $o=''; if($bZeigeOnl) $o.=' OR online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"'); if($bZeigeOfl) $o.=' OR online="0"'; if($bZeigeVmk) $o.=' OR online="2"';
 $o=substr($o,4); $i=substr_count($o,'OR'); if($i==1) $o='('.$o.')'; elseif($i==2) $o='online>""';
 if($rR=$DbO->query('SELECT id'.$t.',online'.($nKapPos>0?',kal_'.$nKapPos:'').' FROM '.KAL_SqlTabT.' WHERE '.$o.$s.' ORDER BY kal_1'.($nFelder>1?',kal_2'.($nFelder>2?',kal_3':''):'').',id')){
  if($nKapPos>0) $nKapPos=$nSpalten+1;
  while($a=$rR->fetch_row()){
   $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $i++;
   if($nListenIdx==1) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
   elseif($nListenIdx>1){ //andere Sortierung
    $s=strtoupper(strip_tags($a[$nListenIdx])); $t=$kal_FeldType[ADM_ListenIndex];
    for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
     if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
    if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
    elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
    $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
   }
   elseif(ADM_ListenIndex==0) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$sId); //nach Nr
   for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]); $aTmp[$sId][]=$a[$nSpalten];
   if($nKapPos>0) if($j=$a[$nKapPos]) $aTmp[$sId]['KAP']=$j; //Kapazitaetsspalte
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';//SQL
if(!$nStart=(isset($_GET['kal_Start'])?(int)$_GET['kal_Start']:(isset($_POST['kal_Start'])?(int)$_POST['kal_Start']:0))) $nStart=1; $nStop=$nStart+ADM_ListenLaenge;
if(ADM_ListenIndex!=1) asort($aIdx); //nach Feldern
elseif(ADM_Rueckwaerts&&!$bArchiv||ADM_ArchivRueckwaerts&&$bArchiv) arsort($aIdx);
reset($aIdx); $k=0; foreach($aIdx as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aD[]=$aTmp[$i];

if(!$Msg){
 if(!$sQ) $Msg='<p class="admMeld">Gesamt-Termin'.($bArchiv?'archiv ':'liste ');
 else $Msg='<p class="admMeld">'.($bArchiv?'Archiv':'Termin').'abfrageergebnis ';
 if($bZeigeOnl) $Msg.='<img src="'.$sHttp.'grafik/punktGrn.gif" width="12" height="12" border="0" alt="online-Termine" title="online-Termine">';
 if($bZeigeOfl) $Msg.='<img src="'.$sHttp.'grafik/punktRot.gif" width="12" height="12" border="0" alt="offline-Termine" title="offline-Termine">';
 if($bZeigeVmk) $Msg.='<img src="'.$sHttp.'grafik/punktRtGn.gif" width="12" height="12" border="0" alt="Terminvorschläge" title="Terminvorschläge">';
 $Msg.='</p>';
}

//Scriptausgabe
?>
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><?php echo $Msg?></td>
  <td align="right">
   [ <a href="drucken.php<?php echo (strlen($sQ)>0||$bArchiv?'?'.substr($sQ.($bArchiv?'&amp;kal_Archiv=1':''),5):'')?>" target="druck" onclick="druWin(this.href);return false;" title="drucken"><img src="<?php echo $sHttp?>grafik/iconDrucken.gif" align="top" width="16" height="16" border="0" title="drucken">&nbsp;drucken</a> ]
   <?php if(!ADM_ZeigeAltes){?>[ <a href="liste.php">Terminliste</a> ] [ <a href="liste.php?kal_Archiv=1">Terminarchiv</a> ]<?php }?>
   <?php if(file_exists('suche.php')){?>[ <a href="suche.php?<?php echo substr($sQ.($bArchiv?'&amp;kal_Archiv=1':''),5)?>">Terminsuche</a> ]<?php }?>
  </td>
 </tr>
</table>
<?php $sNavigator=fKalNavigator($nStart,count($aIdx),ADM_ListenLaenge,$sQ,$bArchiv); echo $sNavigator;?>

<form name="TerminListe" action="liste.php" method="post">
<input type="hidden" name="LschNun" value="<?php echo $sLschNun?>" />
<input type="hidden" name="JKopNun" value="<?php echo $sJKopNun?>" />
<input type="hidden" name="kal_Onl" value="<?php echo ($bZeigeOnl?'1':'0')?>" />
<input type="hidden" name="kal_Ofl" value="<?php echo ($bZeigeOfl?'1':'0')?>" />
<input type="hidden" name="kal_Vmk" value="<?php echo ($bZeigeVmk?'1':'0')?>" />
<input type="hidden" name="kal_Archiv" value="<?php echo ($bArchiv?'1':'')?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php //Kopfzeile
 $bAendern=file_exists('aendern.php'); $bKopiere=file_exists('kopieren.php'); $bDetail=file_exists('detail.php');
 $bZusageZ=KAL_ZusageSystem&&ADM_ZusageTrmListe&&file_exists('zusageTermin.php');
 $bZusageE=KAL_ZusageSystem&&ADM_ZusageTrmEintrag&&file_exists('zusageEingabe.php');
 echo    '<tr class="admTabl">';
 echo NL.' <td align="center"><b>Nr.</b></td>'.NL.' <td>&nbsp;</td>'.NL.' <td>&nbsp;</td>'.NL.($bZusageZ||$bZusageE?' <td>&nbsp;</td>'.NL:'').' <td>&nbsp;</td>';
 for($j=1;$j<$nSpalten;$j++){
  if($j==ADM_ListenZusagSp&&KAL_ZusageSystem){echo NL.' <td><b>'.(KAL_TxListenZusagZTitel?KAL_TxListenZusagZTitel:'&nbsp;').'</b></td>';}
  if($aSpalten[$j]!=ADM_ListenIndex) $v=''; else{$v='&nbsp;*'; if((ADM_Rueckwaerts&&!$bArchiv||ADM_ArchivRueckwaerts&&$bArchiv)&&$aSpalten[$j]==1) $v='&nbsp;+';}
  $sFN=$kal_FeldName[$aSpalten[$j]];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  echo NL.' <td><b>'.$sFN.$v.'</b></td>';
 }
 echo NL.'</tr>';
?>

<tr class="admTabl">
 <td align="right"><input class="admCheck" type="checkbox" name="kal_AllO" value="1" onClick="fSelAll(this.checked)" /></td>
 <td align="center"><?php if(file_exists('loeschen.php')){?><input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" name="LschBtnO" width="16" height="16" border="0" title="markierte Termine löschen" /><?php }else echo '&nbsp;'?></td>
 <td align="center"><?php if($bKopiere){?><input type="image" src="<?php echo $sHttp?>grafik/icon_KopieAlle.gif" name="JKopBtnO" width="12" height="13" border="0" title="markierte Termine ins nächste Jahr kopieren" /><?php }else echo '&nbsp;'?></td>
 <td colspan="<?php echo $nSpalten+($bZusageZ||$bZusageE?1:0)+(ADM_ListenZusagSp>0&&KAL_ZusageSystem?1:0)?>">&nbsp;</td>
</tr>
<?php
 if($nStart>1) $sQ.='&amp;kal_Start='.$nStart; $aQ['Start']=$nStart; $nZusagAktZ=$nZusagKapZ=$nZusagFreZ=0;
 foreach($aD as $a){ //Datenzeilen ausgeben
  $sId=$a[0]; $sSta=$a[$nSpalten]; $sAa=''; $sAe='';
  if($sSta!='2'){$sAa='<a href="liste.php?kal_Num='.$sId.'&amp;kal_Sta='.($sSta=='0'?'1':'0').$sQ.($bArchiv?'&amp;kal_Archiv=1':'').'">'; $sAe='</a>';}
  echo NL.'<tr class="admTabl">';
  echo NL.' <td align="right" valign="top" style="white-space:nowrap;"><span title="'.((int)$sId>0?'':KAL_TxAendereVmk).'">'.$sId.'&nbsp;</span><input class="admCheck" type="checkbox" name="kal_CB'.$sId.'" value="1"'.(isset($aId[$sId])?' checked="checked"':'').' /></td>';
  echo NL.' <td align="center" valign="top">'.($bAendern?'<a href="aendern.php?kal_Num='.$sId.$sQ.'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Bearbeiten"></a>':'&nbsp;').'</td>';
  echo NL.' <td align="center" valign="top">'.($bKopiere?'<a href="kopieren.php?kal_Num='.$sId.$sQ.'"><img src="'.$sHttp.'grafik/icon_Kopie.gif" width="12" height="13" border="0" title="Kopieren"></a>':'&nbsp;').'</td>';
  if($bZusageZ||$bZusageE) echo NL.' <td align="center" valign="top" style="white-space:nowrap">'.trim(($bZusageZ?'<a href="zusageTermin.php?kal_Num='.$sId.$sQ.'&amp;kal_Lst=1"><img src="'.$sHttp.'grafik/icon_Lupe.gif" width="12" height="13" border="0" title="'.KAL_TxZeigeZusageIcon.'"></a> ':'').($bZusageE?'<a href="zusageEingabe.php?kal_Trm='.$sId.$sQ.'&amp;kal_Lst=1"><img src="'.$sHttp.'grafik/icon_Zusagen.gif" width="12" height="13" border="0" title="'.KAL_TxZusageIcon.'"></a>':'')).'</td>';
  echo NL.' <td align="center" valign="top">'.$sAa.'<img src="'.$sHttp.'grafik/punkt'.($sSta=='1'?'Grn':($sSta=='0'?'Rot':($sSta=='2'?'RtGn':'Glb'))).'.gif" width="12" height="12" border="0" title="'.($sSta=='1'?'online - jetzt deaktivieren':($sSta=='0'?'offline - jetzt aktivieren':($sSta=='2'?'Terminvorschlag':'Löschen'))).'">'.$sAe.'</td>';
  for($j=1;$j<$nSpalten;$j++){
   if($j==ADM_ListenZusagSp&&KAL_ZusageSystem){
    $nZusagAktZ=(isset($aZusageZahl[$sId])?$aZusageZahl[$sId]:'0');
    $nZusagKapZ=(isset($a['KAP'])?(int)$a['KAP']:KAL_ListenZusagZLeer);
    $nZusagFreZ=((int)$nZusagKapZ>0?$nZusagKapZ-$nZusagAktZ:KAL_ListenZusagRLeer);
    echo NL.' <td style="text-align:center;vertical-align:top">'.str_replace('#Z',$nZusagAktZ,str_replace('#K',$nZusagKapZ,str_replace('#R',$nZusagFreZ,KAL_TxListenZusagZMuster))).'</td>';
   }
   $k=$aSpalten[$j]; $t=$kal_FeldType[$k]; $sStil='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'g': $s=fKalBB($s); break; // Text
     case 'm': if(ADM_ListenMemoLaenge==0) $s=fKalBB($s); else{$s=fKalBB(fKalKurzMemo($s,ADM_ListenMemoLaenge));} break; //Memo
     case 'a': case 'k': case 'o': case 'u': break; // so lassen
     case 'd': case '@': $w=trim(substr($s,11)); // Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      if($t=='d'){if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].'&nbsp;'.$s; else $s.='&nbsp;'.$kal_WochenTag[$w];}
      elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      if($j==1&&$bDetail) $s='<a href="detail.php?kal_Num='.$sId.$sQ.($bArchiv?'&amp;kal_Archiv=1':'').'" title="'.((int)$sId>0?'':KAL_TxAendereVmk).'">'.$s.'</a>';
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
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(ADM_LinkSymbol){$v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" title="'.$u.'" />'; $sStil.='text-align:center;';}
       $s.='<a title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="_blank">'.$v.(ADM_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
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
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); // Bild
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'/')+1).'" />'; $sStil.='text-align:center;';
      }else $s=fKalKurzName(substr($s,strpos($s,'|')+1));
      break;
     case 'f':
      if(ADM_DateiSymbol){
       $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); // Datei
       if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil.='text-align:center;';
       $v='<img src="'.$sHttp.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.KAL_TxDatei.'" />';
      }else $v=fKalKurzName($s);
      $s='<a href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'">'.$v.'</a>';
      break;
     case 'x': break;
     case 'p': $s=str_repeat('*',strlen($s)/2); break;
    }
   }else $s='&nbsp;';
   if(($w=$kal_SpaltenStil[$k])||$sStil) $sStil=' style="'.$sStil.$w.'"';
   echo NL.' <td valign="top"'.$sStil.'>'.$s.'</td>';
  }
  echo NL.'</tr>';
 }
?>

<tr class="admTabl">
 <td align="right"><input class="admCheck" type="checkbox" name="kal_AllU" value="1" onClick="fSelAll(this.checked)" /></td>
 <td align="center"><?php if(file_exists('loeschen.php')){?><input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" name="LschBtnU" width="16" height="16" border="0" title="markierte Termine löschen" /><?php }else echo '&nbsp;'?></td>
 <td align="center"><?php if($bKopiere){?><input type="image" src="<?php echo $sHttp?>grafik/icon_KopieAlle.gif" name="JKopBtnU" width="12" height="13" border="0" title="markierte Termine ins nächste Jahr kopieren" /><?php }else echo '&nbsp;'?></td>
 <td colspan="<?php echo $nSpalten+($bZusageZ||$bZusageE?1:0)+(ADM_ListenZusagSp>0&&KAL_ZusageSystem?1:0)?>"><a href="<?php echo ADM_Hilfe?>LiesMich.htm#6.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" align="top" border="0" title="Hilfe"></a>&nbsp;</td>
</tr>
</table>
<?php foreach($aQ as $k=>$v) echo '<input type="hidden" name="kal_'.$k.'" value="'.$v.'" />'.NL?>
</form>

<form name="NavigFrm" action="liste.php" method="post">
<input type="hidden" name="kal_Archiv" value="<?php echo ($bArchiv?'1':'')?>" />
<?php
 if(ADM_ListenLaenge){
  $sNavigator=substr_replace($sNavigator,'<td style="white-space:nowrap;text-align:center;width:90%;"><input type="checkbox" class="admRadio" name="kal_Onl" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeOnl?' checked="checked"':'').' /> online-Termine &nbsp; <input type="checkbox" class="admRadio" name="kal_Ofl" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeOfl?' checked="checked"':'').' /> offline-Termine &nbsp; <input type="checkbox" class="admRadio" name="kal_Vmk" value="1" onclick="fSubmitNavigFrm();"'.($bZeigeVmk?' checked="checked"':'').' /> Terminvorschläge</td>'."\n  ",strpos($sNavigator,'<td style="width:18px'),0);
  echo $sNavigator.NL;
 }
 reset($aQ);
 foreach($aQ as $k=>$v) echo '<input type="hidden" name="kal_'.$k.'" value="'.$v.'" />'.NL;
?>
</form>

<?php
echo fSeitenFuss();

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fKalNavigator($nStart,$nCount,$nListenLaenge,$sQry,$bArchiv){
 $nPgs=ceil($nCount/$nListenLaenge); $nPag=ceil($nStart/$nListenLaenge);
 $s ='<td style="width:18px;text-align:center;white-space:nowrap;"><a href="liste.php?'.substr($sQry.'&amp;kal_Start=',5).'1'.($bArchiv?'&amp;kal_Archiv=1':'').'" title="Anfang">|&lt;</a></td>';
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 for($i=$nAnf;$i<=$nEnd;$i++){
  if($i!=$nPag) $nPg=$i; else $nPg='<b>'.$i.'</b>';
  $s.=NL.'  <td style="width:18px;text-align:center;white-space:nowrap;">&nbsp;<a href="liste.php?'.substr($sQry.'&amp;kal_Start=',5).(($i-1)*$nListenLaenge+1).($bArchiv?'&amp;kal_Archiv=1':'').'" title="'.'">'.$nPg.'</a>&nbsp;</td>';
 }
 $s.=NL.'  <td style="width:18px;text-align:center;white-space:nowrap;"><a href="liste.php?'.substr($sQry.'&amp;kal_Start=',5).(max($nPgs-1,0)*$nListenLaenge+1).($bArchiv?'&amp;kal_Archiv=1':'').'" title="Ende">&gt;|</a></td>';
 $X =NL.'<table style="width:100%;margin-top:8px;margin-bottom:8px;" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td style="white-space:nowrap;">Seite '.$nPag.'/'.$nPgs.'</td>';
 $X.=NL.'  '.$s;
 $X.=NL.' </tr>'.NL.'</table>'.NL;
 return $X;
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