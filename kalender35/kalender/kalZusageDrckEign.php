<?php
function fKalSeite(){ //eigene Zusagen als Liste drucken
 global $kal_NutzerFelder;
 array_splice($kal_NutzerFelder,1,1); $nFelder=count($kal_NutzerFelder);
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

 $aZusagen=array(); $aIdx=array(); $sQ=''; $sRefDat=date('Y-m-d'); //Zusagen holen
 if($nSort=(int)(isset($_GET['kal_Sort'])?$_GET['kal_Sort']:(isset($_POST['kal_Sort'])?$_POST['kal_Sort']:0))) $sQ.='&amp;kal_Sort='.$nSort;
 if($nAbst=(int)(isset($_GET['kal_Abst'])?$_GET['kal_Abst']:(isset($_POST['kal_Abst'])?$_POST['kal_Abst']:0))) $sQ.='&amp;kal_Abst='.$nAbst;
 if($nFilt=(int)(isset($_GET['kal_Filter'])?$_GET['kal_Filter']:(isset($_POST['kal_Filter'])?$_POST['kal_Filter']:0))) $sQ.='&amp;kal_Filter='.$nFilt;
 if($bSes){
  if(!KAL_SQL){ //Textdaten
   $aZ=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aZ);
   for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
    $a=explode(';',rtrim($aZ[$i])); $s=fKalDeCode($a[8]); $b=true;
    if($nFilt==1) $b=($a[2]>=$sRefDat); elseif($nFilt==2) $b=($a[2]<$sRefDat);//kommend-abgelaufen
    if($a[7]==$nNId||$s==$sNutzerEml)if($b){$a[8]=$s; $aZusagen[]=$a; $aIdx[]=($nSort<=0?(int)$a[0]:strtolower($a[$nSort]).$a[3].sprintf('%04d',$a[0]));}
  }}elseif($DbO){ //SQL
   //if(KAL_ZusageListeStatus==1) $s='1'; elseif(KAL_ZusageListeStatus==2) $s='1" OR aktiv="2'; else $s='1" OR aktiv>"!';
   $sF=''; if($nFilt==1) $sF=' AND(datum>="'.$sRefDat.'")'; elseif($nFilt==2) $sF=' AND(datum<"'.$sRefDat.'")';//kommend-abgelaufen
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
 if(!$bSes){$Et=KAL_TxSessionUngueltig; $Es='Fehl';} elseif(!$Et){$Et=KAL_TxNzUebersicht; $Es='Meld';}
 $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';
 $X.="\n".'<div class="kalDrTab">';

 //Kopfzeile ausgeben
 $X.="\n".' <div class="kalTbZlDr">'."\n".'  <div class="kalTbDr" style="text-align:center">Nr.</div>';
 if(!$bSta=in_array(6,$aSpalten)) $X.="\n".'  <div class="kalTbDr" style="width:12px;">&nbsp;</div>';
 for($i=($aSpalten[0]==0?1:0);$i<$nSpalten;$i++){
  $k=$aSpalten[$i]; $sStil=''; if($k<8&&$k!=4||$k==$nAnzahlPos) $sStil=' style="text-align:center;"';
  $X.="\n".'  <div class="kalTbDr"'.$sStil.'>'.$kal_ZusageFelder[$k].'</div>';
 }

 //alle Datenzeilen ausgeben
 if($sVStil=KAL_ListeVertikal) $sVStil='vertical-align:'.$sVStil.';'; $sCSS='Druck'; $nFarb=1;
 foreach($aIdx as $n=>$xx){
  $a=$aZusagen[$n]; $sId=$a[0]; $sSta=$a[6]; $sCSS='Druck';
  if(KAL_DruckLFarbig){$sCSS='Dat'.$nFarb; if(--$nFarb<=0) $nFarb=2;} //Farben alternieren
  if($sSta=='1') $sSta='Grn'; elseif($sSta=='0') $sSta='Rot'; elseif($sSta=='2') $sSta='RtGn'; elseif($sSta=='-') $sSta='RotX'; elseif($sSta=='*') $sSta='RtGnX';
  $sSta='<img class="kalPunkt" src="'.KAL_Url.'grafik/punkt'.$sSta.'.gif" alt="Punkt">';
  $sZl="\n".'  <div class="kalTbDr" style="'.$sVStil.'text-align:right;">'.sprintf('%0'.KAL_NummerStellen.'d',$a[0]).'</div>';
  if(!$bSta) $sZl.="\n".'  <div class="kalTbDr" style="'.$sVStil.'text-align:center;">'.$sSta.'</div>';
  for($i=($aSpalten[0]==0?1:0);$i<$nSpalten;$i++){ //alle Spalten
   $k=$aSpalten[$i]; $s=$a[$k]; $sStil=$sVStil;
   if(strlen($s)>0){
    switch ($k){
     case 1: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //Nummer
     case 2: $s=fKalAnzeigeDatum($s); break; //Datum
     case 5: $s=fKalAnzeigeDatum($s).substr($s,10); break; //Buchung
     case 6: $s=$sSta; break;
     case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //User
     default: $s=fKalDt(str_replace('`,',';',$s));
    }//switch
    if($k<8&&$k!=4||$k==$nAnzahlPos) $sStil.='text-align:center;';
   }else $s='&nbsp;';
   if(!empty($sStil)) $sStil=' style="'.$sStil.'"';
   $sZl.="\n".'  <div class="kalTbDr"'.$sStil.'>'.$s.'</div>';
  }
  $X.="\n".' </div><div class="kalTbZlDr"> '.$sZl;
 }
 if(count($aZusagen)<=0) $X.="\n".' </div><div class="kalTbZlDr">'."\n".'  <div class="kalTbDr" colspan="'.($nSpalten+1+($bSta?0:1)-($aSpalten[0]==0?1:0)).'" style="text-align:center;">'.fKalTx(KAL_TxKeineZusagen).'</div>';
 $X.="\n".' </div>';
 $X.="\n".'</div>';
 return $X;
}


?>