<?php
function fKalSeite(){ //Nutzerzentrum

 $Et=''; $Es='Fehl';

 //SQL-Verbindung oeffnen
 $DbO=NULL;
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $Et=KAL_TxSqlVrbdg;}
 }

 //Session pruefen
 $bSes=false; $sSession=substr(KAL_Session,0,29); if(defined('KAL_NeuSession')&&KAL_NeuSession>'') $sSession=KAL_NeuSession;
 if($sSes=substr($sSession,17,12)){
  $nId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }
  }else $Et=KAL_TxSessionZeit;
 }else{$Et=KAL_TxSessionUngueltig; $sSession='';}

 if(!$Et){$Et=KAL_TxNZentrStart; $Es='Meld';}
 //Tabellenanfang
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>
 <div class="kalTabl kalTbNz">';
 if($bSes){
  if(($s=KAL_LinkOEing)||($s=KAL_LinkUEing)) /* Eingeben */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=eingabe'.$sSession.'&amp;kal_Zentrum=1">'.fKalTx($s).'</a></div></div>';
  if(($s=KAL_LinkOAend)||($s=KAL_LinkUAend)) /* Aendern  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=liste'.$sSession.'&amp;kal_Zentrum=1&amp;kal_Aendern=1&amp;kal_Kopieren=1">'.fKalTx($s.' / '.KAL_TxNtUebersicht).'</a></div></div>';
  if(KAL_ListenErinn>=0||KAL_DetailErinn>=0) /* Erinnerungen  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nerinn'.$sSession.'&amp;kal_Zentrum=1">'.fKalTx(KAL_TxNeUebersicht).'</a></div></div>';
  if(KAL_ListenBenachr>=0||KAL_DetailBenachr>=0) /* Benachrichtigungen  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nbenachr'.$sSession.'&amp;kal_Zentrum=1">'.fKalTx(KAL_TxNnUebersicht).'</a></div></div>';
  if(KAL_ZusageSystem&&KAL_ZentrumEigeneZusage) /* eigene Zusagen  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusageneliste'.$sSession.(KAL_ZusageLstFilter>0?'&amp;kal_Filter='.KAL_ZusageLstFilter:'').'&amp;kal_Zentrum=1">'.fKalTx(KAL_TxNzUebersicht).'</a>'.(KAL_Zusagen&&KAL_ZUser!='D'.'em'.'o'?'':' ('.'D'.'em'.'o'.')').'</div></div>';
  if(KAL_ZusageSystem&&KAL_ZentrumFremdeZusage) /* fremde Zusagen  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=nzusagenfliste'.$sSession.'&amp;kal_Zentrum=1">'.fKalTx(KAL_TxNfUebersicht).'</a>'.(KAL_Zusagen&&KAL_ZUser!='D'.'em'.'o'?'':' ('.'D'.'em'.'o'.')').'</div></div>';
  if(KAL_NutzerAendern) /* Nutzerdaten  */ $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=ndaten'.$sSession.'&amp;kal_Zentrum=1">'.fKalTx(KAL_TxNDatAendern).'</a></div></div>';
 }
 //Logout
 $X.='
  <div class="kalTbZl1"><div class="kalTbSpa"><a class="kalDetl" href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion=login'.$sSession.'">'.fKalTx((strlen(KAL_LinkOLogx)>0?KAL_LinkOLogx:(strlen(KAL_LinkULogx)>0?KAL_LinkULogx:'Logout')).'/'.(strlen(KAL_LinkOLogi)>0?KAL_LinkOLogi:(strlen(KAL_LinkULogi)>0?KAL_LinkULogi:'Login'))).'</a></div></div>';
 $X.="\n".' </div>'."\n";

 return $X;
}
?>