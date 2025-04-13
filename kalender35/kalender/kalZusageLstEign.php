<?php
function fKalSeite(){ //Liste eigener Zusagen
 global $kal_NutzerFelder;
 array_splice($kal_NutzerFelder,1,1); $nFelder=count($kal_NutzerFelder); $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');


 $Et=''; $Es='Fehl';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 $bSes=false; $sSession=substr(KAL_Session,0,29); $nNId=0; $sNutzerEml=''; //Session pruefen
 if($sSes=substr($sSession,17,12)){
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nNId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $bSes=true;
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
      $bSes=true; $a=$rR->fetch_row(); array_splice($a,1,1);
      if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]); $sNutzerEml=$a[4];
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }else $Et=KAL_TxSessionUngueltig;

 $sLsch='0'; $aLsch=array(); // Zusagen loeschen
 if($_SERVER['REQUEST_METHOD']=='POST'&&$bSes){
  $Et=KAL_TxNzUnveraendert; $Es='Meld';
  if((isset($_POST['kal_Lsch_x'])||isset($_POST['kal_Lsch_y']))&&KAL_ZusageLschEigene){
   reset($_POST); $n=0;
   if(isset($_POST['kal_LschNun'])&&$_POST['kal_LschNun']=='1'){
    $sLnk=(KAL_ZusageLink==''?KAL_Self.'?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;')).substr(KAL_Query.'&amp;',5).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='; //+Id
    if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
    require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
    $sBtr=str_replace('#A',fKalHost(),KAL_TxZusageLschBtr); $sTxt=KAL_TxZusageLschMTx;
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t);
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
    if(!KAL_SQL){
     foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_') $aLsch[(int)substr($k,6)]=true;
     if(count($aLsch)>0){
      $aE=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nESaetze=count($aE);
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD);
      for($i=1;$i<$nSaetze;$i++){
       if(($nId=(int)$aD[$i])&&isset($aLsch[$nId])){ //Zusage gefunden
        $aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDecode($aZ[8]); $aD[$i]=''; $n++;
        $s=$aZ[1].';'; $p=strlen($s); $aT=array();
        for($j=1;$j<$nESaetze;$j++) if(substr($aE[$j],0,$p)==$s){ //Termin gefunden
         $aT=explode(';',rtrim($aE[$j])); array_splice($aT,1,1); break;
        }
        if(KAL_ZusageLschInfoAut||KAL_ZusageLschInfoAdm){ //EMail
         $sUZ=fKalZusagePlainText($aZ,$kal_ZusageFelder); $a=fKalTerminPlainText($aT); $sUT=$a[0]; $sKontaktEml=$a[1];
         $Mailer->SetReplyTo($aZ[8]);
         $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk).$aT[0],str_replace('\n ',"\n",$sTxt))));
         if(KAL_ZusageLschInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
         if(KAL_ZusageLschInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();}
      }}}
      if($n>0) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){ //Zusagen neu schreiben
       fwrite($f,str_replace("\r",'',rtrim(implode('',$aD)))."\n"); fclose($f);
       $Et=str_replace('#N',$n,KAL_TxNzGeloescht); $Es='Erfo';
      }else{$Et=str_replace('#',KAL_Daten.KAL_Zusage,KAL_TxDateiRechte); $Es='Fehl';}
     }
    }elseif($DbO){ //SQL
     foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_'){
      $nId=(int)substr($k,6); $aZ=array(); $aT=array();
      if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr="'.$nId.'" AND benutzer="'.$nNId.'"')){
       $aZ=$rR->fetch_row(); $rR->close();
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$aZ[1].'"')){
        $aT=$rR->fetch_row(); array_splice($aT,1,1); $rR->close();
      }}
      if($DbO->query('DELETE FROM '.KAL_SqlTabZ.' WHERE nr="'.$nId.'" AND benutzer="'.$nNId.'"')) if($DbO->affected_rows>0) $n++;
      if(KAL_ZusageLschInfoAut||KAL_ZusageLschInfoAdm){ //EMail
       $sUZ=fKalZusagePlainText($aZ,$kal_ZusageFelder); $a=fKalTerminPlainText($aT,$DbO); $sUT=$a[0]; $sKontaktEml=$a[1];
       $Mailer->SetReplyTo($aZ[8]);
       $Mailer->Text=str_replace('#D',trim($sUT),str_replace('#Z',trim($sUZ),str_replace('#A',str_replace('&amp;','&',$sLnk).$aT[0],str_replace('\n ',"\n",$sTxt))));
       if(KAL_ZusageLschInfoAdm){$Mailer->AddTo(strpos(KAL_EmpfZusage,'@')>0?KAL_EmpfZusage:KAL_Empfaenger); $Mailer->Send(); $Mailer->ClearTo();}
       if(KAL_ZusageLschInfoAut&&!empty($sKontaktEml)){$Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();}
      }
     }
     if($n){$Et=str_replace('#N',$n,KAL_TxNzGeloescht); $Es='Erfo';}
    }
   }else{
    foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_') $aLsch[(int)substr($k,6)]=true;
    if($n=count($aLsch)){$Et=str_replace('#N',$n,KAL_TxNzLoeschen); $Es='Fehl'; $sLsch='1';}
 }}}//POST

 $aZusagen=array(); $aIdx=array(); $sQ=''; $sQK=''; $sF=''; $sRefDat=date('Y-m-d'); //Zusagen holen
 if($nFilt=(int)(isset($_GET['kal_Filter'])?$_GET['kal_Filter']:(isset($_POST['kal_Filter'])?$_POST['kal_Filter']:0))) $sQK='&amp;kal_Filter='.$nFilt; $sQ.=$sQK;
 if($nSort=(int)(isset($_GET['kal_Sort'])?$_GET['kal_Sort']:(isset($_POST['kal_Sort'])?$_POST['kal_Sort']:0))) $sQ.='&amp;kal_Sort='.$nSort;
 if($nAbst=(int)(isset($_GET['kal_Abst'])?$_GET['kal_Abst']:(isset($_POST['kal_Abst'])?$_POST['kal_Abst']:0))) $sQ.='&amp;kal_Abst='.$nAbst;
 if($bSes){
  if(!KAL_SQL){ //Textdaten
   $aZ=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aZ);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aZ[$i])); $s=fKalDeCode($a[8]); $b=true;
    if($nFilt==1) $b=($a[2]>=$sRefDat); elseif($nFilt==2) $b=($a[2]<$sRefDat);//kommend-abgelaufen
    if($a[7]==$nNId||$s==$sNutzerEml)if($b){$a[8]=$s; $aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
  }}elseif($DbO){ //SQL
   //if(KAL_ZusageListeStatus==1) $s='1'; elseif(KAL_ZusageListeStatus==2) $s='1" OR aktiv="2'; else $s='1" OR aktiv>"!';
   if($nFilt==1) $sF=' AND(datum>="'.$sRefDat.'")'; elseif($nFilt==2) $sF=' AND(datum<"'.$sRefDat.'")';//kommend-abgelaufen
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE (benutzer="'.$nNId.'" OR email="'.$sNutzerEml.'")'.$sF.' ORDER BY nr')){
    while($a=$rR->fetch_row()){
     $aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));
    }$rR->close();
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }
  if($nAbst<=0) asort($aIdx); else arsort($aIdx); reset($aIdx);
 }

 $nAnzahlPos=-1; //Felder aufbereiten
 $kal_ZusageLstFeld=explode(';',KAL_ZusageLstFeld); if($bSes) $kal_ZusageLstFeld=explode(';',KAL_NZusageLstFld);
 $aSpalten=array(); $aTmp=array(); if($kal_ZusageLstFeld[0]>'0') $aTmp[]=0;
 for($i=1;$i<=$nZusageFelder;$i++){
  $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]); if($s=$kal_ZusageLstFeld[$i]) $aTmp[$i]=(int)$s;
  if($kal_ZusageFelder[$i]=='ANZAHL'){$nAnzahlPos=$i; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$i]=KAL_ZusageNameAnzahl;}
 }
 asort($aTmp); reset($aTmp); foreach($aTmp as $i=>$xx) $aSpalten[]=$i; $nSpalten=count($aSpalten);

 //Seitenausgabe
 if(!$bSes){$Et=KAL_TxSessionUngueltig; $Es='Fehl';} elseif(!$Et){$Et=KAL_TxNzUebersicht.(KAL_Zusagen&&KAL_ZUser!=chr(68).'e'.'mo'?'':' (D'.'e'.'mov'.'er'.'si'.'on'.')'); $Es='Meld';}
 $X="\n".'<p class="kal'.$Es.'">'.fKalTx(count($aIdx)?$Et:KAL_TxKeineZusagen).'</p>';
 if(KAL_DetailPopup&&!defined('KAL_KalWin')) $X.="\n\n".'<script>function KalWin(sURL){kalWin=window.open(sURL,"kalwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.KAL_PopupX.',top='.KAL_PopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");kalWin.focus();}</script>';
 $X.="\n\n<script>\n function fSelAll(bStat){\n  for(var i=0;i<self.document.DatListe.length;++i)\n   if(self.document.DatListe.elements[i].type==\"checkbox\") self.document.DatListe.elements[i].checked=bStat;\n }\n function fSubmitFrm(){document.DatListe.method='get';document.DatListe.submit();}\n</script>";
 $X.="\n".'<form class="kalForm" name="DatListe" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden);
 $X.="\n".'<input type="hidden" name="kal_Aktion" value="nzusageneliste">'."\n".'<input type="hidden" name="kal_Sort" value="'.$nSort.'">'."\n".'<input type="hidden" name="kal_Abst" value="'.$nAbst.'">'."\n".'<input type="hidden" name="kal_Session" value="'.$sSes.'">'."\n".'<input type="hidden" name="kal_Zentrum" value="1">';
 $X.="\n".'<div class="kalTabl">';

 //Kopfzeile ausgeben
 $t='e'; $w=''; $v=''; $aSpTitle=array(); // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
 if($nSort<=0) if($nAbst<=0){$t='t'; $w=KAL_TxAbsteigend; $v='&amp;kal_Abst=1';}else{$t='r'; $w=KAL_TxAufsteigend;}
 $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
 $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusageneliste&amp;kal_Session='.$sSes.$v.$sQK.'&amp;kal_Zentrum=1" title="'.fKalTx($w.KAL_TxSortieren).'">'.$t.'</a>';
 $X.="\n".' <div class="kalTbZl0">'."\n".'  <div class="kalTbLst">Nr.'.$t.'</div>'; $nFarb=1;
 if(KAL_ZusageAendEigene) $X.="\n".'  <div class="kalTbLst" title="'.fKalTx(KAL_TxAendern).'">&nbsp;</div>';
 if(!$bSta=in_array(6,$aSpalten)) $X.="\n".'  <div class="kalTbLst">&nbsp;</div>';
 for($i=($aSpalten[0]==0?1:0);$i<$nSpalten;$i++){
  $k=$aSpalten[$i]; $sStil=' kalTbLsL'; if($k<8&&$k!=4||$k==$nAnzahlPos) $sStil=' kalTbLsM'; $t='';
  if($k==2||$k==4){
   $t='e'; $w=''; $v=''; // $t-Iconart, $v-Rueckwaerts, $w-Text: ab-/aufsteigend
   if($nSort==$k) if($nAbst<=0){$t='t'; $w=KAL_TxAbsteigend; $v='&amp;kal_Abst=1';}else{$t='r'; $w=KAL_TxAufsteigend;}
   $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
   $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusageneliste&amp;kal_Session='.$sSes.$v.'&kal_Sort='.$k.$sQK.'&amp;kal_Zentrum=1" title="'.fKalTx($w.KAL_TxSortieren).'">'.$t.'</a>';
  }
  $X.="\n".'  <div class="kalTbLst'.$sStil.'">'.fKalTx($kal_ZusageFelder[$k]).$t.'</div>'; $aSpTitle[$k]=fKalTx($kal_ZusageFelder[$k]).$t;
 }

 //alle Datenzeilen ausgeben
 foreach($aIdx as $n=>$xx){
  $a=$aZusagen[$n]; $sId=$a[0]; $sSta=$a[6];
  if($sSta=='1') $sSta='Grn.gif" title="'.fKalTx(KAL_TxZusage1Status); //Status
  elseif($sSta=='0') $sSta='Rot.gif" title="'.fKalTx(KAL_TxZusage0Status);
  elseif($sSta=='2') $sSta='RtGn.gif" title="'.fKalTx(KAL_TxZusage2Status);
  elseif($sSta=='-') $sSta='RotX.gif" title="'.fKalTx(KAL_TxZusage3Status);
  elseif($sSta=='*') $sSta='RtGnX.gif" title="'.fKalTx(KAL_TxZusage4Status);
  elseif($sSta=='7') $sSta='Glb.gif" title="'.fKalTx(KAL_TxZusage7Status);
  $sSta='<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$sSta.'" alt="Punkt">';
  $sZl="\n".'  <div class="kalTbLst"><span class="kalTbLst">'.fKalTx($kal_ZusageFelder[0]).'</span>'.(KAL_ZusageLschEigene?'<input class="kalCheck" type="checkbox" name="kal_L_'.$sId.'_'.$a[1].'_'.$a[2].'" value="1"'.(isset($aLsch[$sId])?' checked="checked"':'').'> ':'').sprintf('%0'.KAL_NummerStellen.'d',$a[0]).'</div>';
  if(KAL_ZusageAendEigene) $sZl.="\n".'  <div class="kalTbLst kalTbLsM"><span class="kalTbLst">'.fKalTx(KAL_TxAendern).'</span><a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusageaendern&amp;kal_Session='.$sSes.'&amp;kal_Nummer='.$sId.$sQ.'&amp;kal_Zentrum=1" title="'.fKalTx(KAL_TxAendern).'"><img class="kalIcon" src="'.KAL_Url.'grafik/iconBearbeiten.gif" title="'.fKalTx(KAL_TxAendern).'" alt="'.fKalTx(KAL_TxAendern).'"></a></div>';
  if(!$bSta) $sZl.="\n".'  <div class="kalTbLst kalTbLsM">'.$sSta.'</div>';
  for($i=($aSpalten[0]==0?1:0);$i<$nSpalten;$i++){ //alle Spalten
   $k=$aSpalten[$i]; $s=$a[$k]; $sStil=' kalTbLsL'; if($k<8&&$k!=4||$k==$nAnzahlPos) $sStil=' kalTbLsM';
   if(strlen($s)>0){
    switch ($k){
     case 1: $s='<a class="kalDetl" href="'.(KAL_DetailPopup?KAL_Url.'kalender.php?':KAL_Self.'?'.substr(KAL_Query.'&amp;',5)).'kal_Aktion=detail&amp;kal_Session='.$sSes.(KAL_DetailPopup?'&amp;kal_Popup=1':'').'&amp;kal_Nummer='.$s.$sQ.'&amp;kal_Zentrum=1" title="'.fKalTx(KAL_TxDetail).'"'.(KAL_DetailPopup?' target="kalwin" onclick="KalWin(this.href);return false;"':'').'>'.sprintf('%0'.KAL_NummerStellen.'d',$s).'</a>'; break; //Nummer
     case 2: $s=fKalAnzeigeDatum($s); break; //Datum
     case 5: $s=fKalAnzeigeDatum($s).substr($s,10); break; //Buchung
     case 6: $s=$sSta; break;
     case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //User
     default: $s=fKalDt(str_replace('`,',';',$s));
    }//switch
    if($k==$nAnzahlPos&&$a[6]<'0') $s='-'.abs((int)$s);
   }else $s='&nbsp;';
   $sZl.="\n".'  <div class="kalTbLst'.$sStil.'"><span class="kalTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>';
  }
  $X.="\n".' </div><div class="kalTbZl'.$nFarb.'"> '.$sZl; if(--$nFarb<=0) $nFarb=2;
 }
 $X.="\n".' </div><div class="kalTbZl'.$nFarb.'">';
 $X.="\n".'  <div class="kalTbLst">';
 if(KAL_ZusageLschEigene) $X.="\n".'   <span class="kalTbLst">'.fKalTx(KAL_TxLoeschen).'</span><input class="kalCheck" type="checkbox" name="kal_SelAll" value="1" title="'.fKalTx(KAL_TxAlle).'" onClick="fSelAll(this.checked)"> <input type="image" class="kalIcon" name="kal_Lsch" src="'.KAL_Url.'grafik/iconLoeschen.gif" title="'.fKalTx(KAL_TxLoeschen).'" alt="'.fKalTx(KAL_TxLoeschen).'"><input type="hidden" name="kal_LschNun" value="'.$sLsch.'">'; else $X.='&nbsp;';
 $X.="\n".'  </div>';
 $X.="\n".' </div>';
 $X.="\n".'</div>';
 $X.="\n".' <div class="kalSchalter"><input class="kalRadio" type="Radio" name="kal_Filter" value="0" onclick="fSubmitFrm()"'.(!$nFilt?' checked="checked"':'').'> '.fKalTx(KAL_TxAlle).' &nbsp; <input class="kalRadio" type="Radio" name="kal_Filter" value="1" onclick="fSubmitFrm()"'.($nFilt==1?' checked="checked"':'').'> '.fKalTx(KAL_TxTerminKommt).' &nbsp; <input class="kalRadio" type="radio" name="kal_Filter" value="2" onclick="fSubmitFrm()"'.($nFilt==2?' checked="checked"':'').'> '.fKalTx(KAL_TxTerminVorbei).'</div>';
 $X.="\n".'</form>';
 return $X;
}

function fKalZusagePlainText($aZ,$kal_ZusageFelder){
 $nZusageFelder=count($kal_ZusageFelder); $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 $sZ='ID-'.sprintf('%04d',$aZ[0]).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
 $sZ.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[2])).': '.fKalAnzeigeDatum($aZ[2]);
 for($i=3;$i<$nZusageFelder;$i++){
  if($i!=5&&$i!=6&&($s=trim($aZ[$i]))){
   $sFN=str_replace('`,',';',$kal_ZusageFelder[$i]);
   if($sFN=='ANZAHL') if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl;
   $sZ.="\n".strtoupper($sFN).': '.($i!=7?($kal_ZusageFeldTyp[$i]!='w'||!KAL_Waehrung?str_replace('`,',';',$s):number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,'').' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR')):sprintf('%04d',$s));
  }
 }
 return $sZ;
}

function fKalTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
   if($u=$s){
    switch($t){
     case 't': $u=@strip_tags(fKalBB(fKalDt($s))); break; //Text
     case 'a': case 'k': case 'o': break;
     case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKalBB(fKalDt($s))); else $u=''; break; //Memo
     case 'd': case '@': $w=trim(substr($s,11)); $u=fKalAnzeigeDatum($s); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
      }elseif($w) $u.=' '.$w;
      break;
     case 'z': $u=$s.' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $u=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.(KAL_Waehrung!='&#8364;'?KAL_Waehrung:'EUR');
      }else $u='';
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
?>