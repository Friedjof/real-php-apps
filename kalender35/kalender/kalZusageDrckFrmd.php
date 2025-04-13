<?php
function fKalSeite(){ //fremde Zusagen zum Termin als Liste drucken
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag, $kal_NutzerFelder;

 $sTId=fKalRq1(isset($_GET['kal_Nummer'])?$_GET['kal_Nummer']:(isset($_POST['kal_Nummer'])?$_POST['kal_Nummer']:'0')); $aT=array();
 $Et=''; $Es='Fehl'; $sHid=''; $bSesOK=false; $bNtzZ=false; $bErlaubt=true; $nNPos=array_search('u',$kal_FeldType); $sUT='';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 if($sSes=substr(KAL_Session,17,12)){ //Session prufen
  $nNId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4); $k=0;
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   $k=KAL_NNutzerListFeld; if($k<=0) $k=KAL_NutzerListFeld;
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSesOK=true;
     break;
    }
    if(!$bSesOK) $Et=KAL_TxSessionUngueltig;
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nNId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSesOK=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=$SqE;
  }else $Et=KAL_TxSessionZeit;
 }

 if($bSesOK&&(isset($_GET['kal_Zusagen'])||isset($_POST['kal_Zusagen']))&&(isset($_GET['kal_Zentrum'])||isset($_POST['kal_Zentrum']))) $bNtzZ=true;
 if($bNtzZ&&$nNPos>0&&!KAL_NAendernFremde){ //Terminbesitzer pruefen
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $k=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$k)==$s){
    $aT=explode(';',rtrim($aD[$i])); array_splice($aT,1,1); if((int)$aT[$nNPos]!=$nNId) $bErlaubt=false;
    break;
   }
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){
    $aT=$rR->fetch_row(); $rR->close(); if(count($aT)>1){array_splice($aT,1,1); if((int)$aT[$nNPos]!=$nNId) $bErlaubt=false;}
 }}}

 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nAnzahlPos=-1; $nZusSum=0; //Felder aufbereiten
 $kal_ZusageLstFeld=explode(';',(!$bSesOK?KAL_ZusageLstFeld:KAL_NZusageLstFld)); $nZusageFelder=substr_count(KAL_ZusageFelder,';');

 $nSort=(int)(isset($_GET['kal_Sort'])?$_GET['kal_Sort']:(isset($_POST['kal_Sort'])?$_POST['kal_Sort']:0));
 $nAbst=(int)(isset($_GET['kal_Abst'])?$_GET['kal_Abst']:(isset($_POST['kal_Abst'])?$_POST['kal_Abst']:0));
 $sQry=($nSort?'&amp;kal_Sort='.$nSort:'').($nAbst?'&amp;kal_Abst='.$nAbst:''); $bSuchDat=false; $sSuchTxt='';
 if($s=fKalRq(isset($_GET['kal_ZSuch'])?$_GET['kal_ZSuch']:(isset($_POST['kal_ZSuch'])?$_POST['kal_ZSuch']:''))){ //3-Suchparameter
  $sSuchTxt=$s;
  $sDSep=(KAL_Datumsformat==1?'.':(KAL_Datumsformat==2||KAL_Datumsformat==3?'/':'-'));
  if(($p=strpos($s,$sDSep))&&($p=strpos($s,$sDSep,$p+1))&&strlen($s)<11){ //Separator 2x enthalten
   $sSuch=fKalNormDatum($s); if(!strpos($sSuch,'00',2)) $bSuchDat=true; else $sSuch=substr($sSuch,strpos($sSuch,'-'));
  }else $sSuch=$s;
 }else $sSuch='';

 $aSpalten=array(); $a=array(); if($kal_ZusageLstFeld[0]>'0') $a[0]=0; //Anzeigespalten aufbereiten
 for($i=1;$i<=$nZusageFelder;$i++){
  $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]); if($s=$kal_ZusageLstFeld[$i]) $a[$i]=(int)$s;
  if($kal_ZusageFelder[$i]=='ANZAHL'){$nAnzahlPos=$i; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$i]=KAL_ZusageNameAnzahl;}
 }
 if($bNtzZ&&$bErlaubt){unset($a[0]); unset($a[6]); if($nAnzahlPos>=0) $a[$nAnzahlPos]=0.6; $a[8]=0.8;} //E-Mail
 asort($a); reset($a); foreach($a as $i=>$xx) $aSpalten[]=$i; $nSpalten=count($aSpalten);

 $aZusagen=array(); $aIdx=array(); //Zusagen holen

 if($bErlaubt){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=';'.$sTId.';'; $k=strlen($s);
   for($i=1;$i<$nSaetze;$i++){ $sZl=$aD[$i]; //ueber alle Datensaetze
    if(substr($sZl,strpos($sZl,';'),$k)==$s){
     $a=explode(';',rtrim($aD[$i])); $a[8]=fKalDeCode($a[8]); $sSta=$a[6]; $nZusSum+=($nAnzahlPos>0?$a[$nAnzahlPos]:1); $bOk=true;
     if($bNtzZ||KAL_ZusageListeStatus==1&&$sSta=='1'||KAL_ZusageListeStatus==0||KAL_ZusageListeStatus==2&&$sSta>'0'){
      if(!empty($sSuch)){
       if(!$bSuchDat){
        $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
       }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
      }
      if($bOk){$aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
     }
   }}
  }elseif($DbO){ //SQL
   if(KAL_ZusageListeStatus==1) $s='1'; elseif(KAL_ZusageListeStatus==2) $s='1" OR aktiv="2'; else $s='1" OR aktiv>"!';
   if($bNtzZ) $s='1" OR aktiv>"!';
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'" AND (aktiv="'.$s.'") ORDER BY nr')){
    while($a=$rR->fetch_row()){
     $sSta=$a[6]; $nZusSum+=($nAnzahlPos>0?$a[$nAnzahlPos]:1); $bOk=true;
     if(!empty($sSuch)){
      if(!$bSuchDat){
       $bOk=false; for($j=2;$j<=$nZusageFelder;$j++) if(stristr($a[$j],$sSuch)) $bOk=true;
      }else if($a[2]!=$sSuch&&substr($a[5],0,10)!=$sSuch) $bOk=false;
     }
     if($bOk){$aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
    }$rR->close();
   }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }
  if($nAbst<=0) asort($aIdx); else arsort($aIdx); reset($aIdx);
 }

 if(!$Et){if(!$sSuchTxt) $Et=KAL_TxZusagenBisher; else $Et=KAL_TxNfSuchErgebnis; $Es='Meld'; if(isset($aT[1])) $Et.=': '.fKalAnzeigeDatum($aT[1]);} //Seitenausgabe
 $X="\n".' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

 $X.="\n".'<div class="kalDrTab">';

 //Kopfzeile ausgeben
 $X.="\n".' <div class="kalTbZlDr">'; if($sVStil=KAL_ListeVertikal) $sVStil='vertical-align:'.$sVStil.';'; $nFarb=1; $sCSS='Druck';
 if($bNtzZ&&$bErlaubt) $X.="\n".'  <div class="kalTbDr" style="text-align:center;">Nr.</div>';
 for($i=0;$i<$nSpalten;$i++){
  $k=$aSpalten[$i];$sStil=''; if($k<4||$k==5||$k==6||$k==$nAnzahlPos) $sStil=' style="text-align:center;"';
  $X.="\n".'  <div class="kalTbDr"'.$sStil.'>'.$kal_ZusageFelder[$k].'</div>';
 }

 foreach($aIdx as $n=>$xx){ //alle Datenzeilen
  $sZl=''; $sSta=''; $sCSS='Druck';
  if(KAL_DruckLFarbig){$sCSS='Dat'.$nFarb; if(--$nFarb<=0) $nFarb=2;} //Farben alternieren
  $a=$aZusagen[$n]; $s=$a[6]; if($s=='1') $sSta='Grn'; elseif($s=='0') $sSta='Rot'; elseif($s=='2') $sSta='RtGn'; elseif($s=='-') $sSta='RotX'; elseif($s=='*') $sSta='RtGnX';
  $sSta='<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$sSta.'.gif" alt="Punkt">';
  if($bNtzZ&&$bErlaubt) $sZl="\n".'  <div class="kalTbDr" style="white-space:nowrap;'.$sVStil.'"'.'>'.$sSta.'&nbsp;'.sprintf('%0'.KAL_NummerStellen.'d',$a[0]).'</div>';
  for($i=0;$i<$nSpalten;$i++){ //alle Spalten
   $k=$aSpalten[$i]; $s=$a[$k]; $sStil=$sVStil;
   if(strlen($s)>0){
    switch ($k){
     case 0: case 1: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //Nummer
     case 2: $s=fKalAnzeigeDatum($s); break; //Datum
     case 5: $s=fKalAnzeigeDatum($s).substr($s,10); break; //Buchung
     case 6: $s=$sSta; break; //Status
     default: $s=fKalDt(str_replace('`,',';',$s));
    }//switch
    if($k<4||$k==5||$k==6||$k==$nAnzahlPos) $sStil.='text-align:center;';
   }else $s='&nbsp;';
   if(!empty($sStil)) $sStil=' style="'.$sStil.'"';
   $sZl.="\n".'  <div class="kalTbDr"'.$sStil.'>'.$s.'</div>'; //Standardlayout
  }
  $X.="\n".' </div><div class="kalTbZlDr"> '.$sZl;
 }

 if(count($aZusagen)<=0) $X.="\n".' </div><div class="kalTbZlDr">'."\n".'  <div class="kalTbDr" colspan="'.$nSpalten.'" style="text-align:center;">'.fKalTx($bErlaubt?KAL_TxKeineZusagen:KAL_TxNummerFremd).'</div>';
 if($bNtzZ&&$bErlaubt){
  if(strpos('*'.KAL_TxZusageKapazNull,'#Z')>0) $sZl=str_replace('#Z',sprintf('%0d',$nZusSum),KAL_TxZusageKapazNull); else $sZl=KAL_TxNfUebersicht.': '.sprintf('%0d',$nZusSum);
  $X.="\n".' </div><div class="kalTbZlDr">'."\n".'  <div class="kalTbDr" colspan="'.($nSpalten+1).'" style="text-align:left;">'.fKalTx($sZl).'</div>';
 }
 $X.="\n </div>\n</div>";

 return $X;
}

?>