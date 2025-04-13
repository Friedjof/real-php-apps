<?php
function fKalSeite(){ //iCal-Exportseite
 global $kal_FeldName, $kal_FeldType;
 $Et=''; $Es='Fehl'; $sId=''; $Y=''; $sSec=(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s');
 if($nId=(isset($_GET['kal_Nummer'])?(int)$_GET['kal_Nummer']:0)){
  $Y.='
  <div class="kalTabl">
   <div class="kalTbZl1">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="http'.$sSec.'://'.KAL_Www.'kalICal.php?kal_Id='.$nId.'" title="termin_'.$nId.'.ics">'.fKalTx(KAL_TxCalDownload).'</a></div>
    <div class="kalTbSp2">'.str_replace('#D','<i>termin_'.$nId.'.ics</i>',fKalTx(KAL_TxCalTxDnl)).'</div>
   </div>
   <div class="kalTbZl2">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="webcal://'.KAL_Www.'kalICal.php?kal_Id='.$nId.'" title="termin_'.$nId.'.ics">'.fKalTx(KAL_TxCalImport).'</a></div>
    <div class="kalTbSp2">'.fKalTx(KAL_TxCalTxExp).'</div>
   </div>
   <div class="kalTbZl1">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="https://www.google.com/calendar/render?cid='.rawurlencode('http'.(KAL_CalSSLExp?'s':'').'://'.KAL_Www.'kalICal.php?kal_Id='.$nId).'" title="termin_'.$nId.'.ics" target="gcal" style="white-space:nowrap;">'.fKalTx(KAL_TxCalGoogle).'</a></div>
    <div class="kalTbSp2">'.fKalTx(KAL_TxCalTxGoogle).'</div>
   </div>
   <div class="kalTbZl2">
    <div class="kalTbSp1" style="text-align:center">URL:</div>
    <div class="kalTbSp2"><input class="kalEing" value="http'.(KAL_CalSSLUrl?'s':'').'://'.KAL_Www.'kalICal.php?kal_Id='.$nId.'"></div>
   </div>
  </div>';
  if(empty($Et)){$Et=KAL_TxCalMeldExport; $Es='Meld';}
 }elseif(isset($_GET['kal_Lst'])){
  $nId=date('is'); $sQ=$_SERVER['QUERY_STRING'];
  if(strpos($sQ,'&amp;')>0) $sQ=substr($sQ,strpos($sQ,'&amp;kal_',1)); elseif(strpos($sQ,'&')>0) $sQ=substr($sQ,strpos($sQ,'&kal_',1));
  $sQ=str_replace('&amp;kal_Popup=1','',str_replace('&amp;kal_Lst=1','',str_replace('&amp;kal_Zentrum=1','',str_replace('&kal_Popup=1','',str_replace('&kal_Lst=1','',str_replace('&kal_Zentrum=1','',$sQ))))));
  $sQ.=(strlen($sQ)>0?'&':'').'kal_Id='.$nId;
  $Y.='
  <div class="kalTabl">
   <div class="kalTbZl1">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="http'.$sSec.'://'.KAL_Www.'kalICLst.php?'.$sQ.'" title="termine_'.$nId.'.ics">'.fKalTx(KAL_TxCalDownload).'</a></div>
    <div class="kalTbSp2">'.str_replace('#D','<i>termine_'.$nId.'.ics</i>',fKalTx(KAL_TxCalTxDnl)).'</div>
   </div>
   <div class="kalTbZl2">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="webcal://'.KAL_Www.'kalICLst.php?'.$sQ.'" title="termine_'.$nId.'.ics">'.fKalTx(KAL_TxCalImport).'</a></div>
    <div class="kalTbSp2">'.fKalTx(KAL_TxCalTxExp).'</div>
   </div>
   <div class="kalTbZl1">
    <div class="kalTbSp1 kalCalBtn"><a class="kalDetl" href="https://www.google.com/calendar/render?cid='.rawurlencode('http'.(KAL_CalSSLExp?'s':'').'://'.KAL_Www.'kalICLst.php?'.$sQ).'" title="termine_'.$nId.'.ics" target="gcal" style="white-space:nowrap;">'.fKalTx(KAL_TxCalGoogle).'</a></div>
    <div class="kalTbSp2">'.fKalTx(KAL_TxCalTxGoogle).'</div>
   </div>
   <div class="kalTbZl2">
    <div class="kalTbSp1" style="text-align:center">URL:</div>
    <div class="kalTbSp2"><input class="kalEing" type="text" value="http'.(KAL_CalSSLUrl?'s':'').'://'.KAL_Www.'kalICLst.php?'.$sQ.'"></div>
   </div>
  </div>';
  if(empty($Et)){$Et=KAL_TxCalMeldExport; $Es='Meld';}
 }else $Et=KAL_TxNummerUnbek;
 $X ="\n".'<div style="float:right"><a href="javascript:window.close();"><img class="kalIcon" src="grafik/knopfX.gif" alt="close" title="close"></a></div>';
 $X.="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>';
 $X.="\n".'<div style="clear:both"></div>';
 return $X.$Y;
}
?>