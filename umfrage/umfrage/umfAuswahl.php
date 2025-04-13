<?php
if(!function_exists('fUmfSeite') ){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfAuswahl(true);}
}

function fUmfAuswahl($bDirekt){ //Seiteninhalt
 $Meld=''; $MTyp='Fehl'; $aF=array(); $aC=array();
 $sSes=($bDirekt?UMF_Session:UMF_NeuSession);
 $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $aT=@file(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses'); if(is_array($aT)) $aT=explode(';',rtrim($aT[0]));
  $sNam=(isset($aT[UMF_TeilnehmerKennfeld-1])&&$aT[UMF_TeilnehmerKennfeld-1]?$aT[UMF_TeilnehmerKennfeld-1]:'?????');

  for($i=1;$i<=26;$i++){$k=chr(64+$i); $s=constant('UMF_Umfr'.$k); if(substr($s,0,1)!=';'){$a=explode(';',$s); if($a[2]){$aF[$k]=$a[3]; $aC[$k]=($a[4]?true:false);}}}

  if(empty($Meld)){
   if(!defined('UMF_AktivCodeErr')) $Meld=UMF_TxFuer.' &quot;'.$sNam.'&quot;';
   else $Meld='<span style="color:#b02">'.UMF_TxAktivCodeNoetig.'</span>'; $MTyp='Meld';
  }
 }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;

 $X='<p class="umfMeld" style="font-size:1.2em">'.fUmfTx(UMF_TxTeilnehmerzentrum).'</p>';
 $X.="\n".'<p class="umf'.$MTyp.'">'.fUmfTx($Meld).'</p>'; $nNr=0;
 $X.="\n".'<table class="umfMenu" border="0" cellpadding="0" cellspacing="0">';
 if($MTyp!='Fehl'){
  if(UMF_TeilnehmerNormUmfrage) $X.=fUmfMenuZeile(UMF_TxStandardUmf,'frage',$sSes,'',UMF_StdUmfrCode>0);
  if(UMF_TeilnehmerAlleUmfrage){reset($aF); foreach($aF as $k=>$s) if($s) $X.=fUmfMenuZeile($s.' ('.$k.')','frage',$sSes,'umf_Umfrage='.$k,$aC[$k]);}
  if(UMF_TeilnehmerDrucken) $X.=fUmfMenuZeile(UMF_TxDrucken,'drucken',$sSes);
 }
 $X.=fUmfMenuZeile(UMF_TxAbmelden,'erfassen');
 $X.="\n</table>";
 return $X;
}

function fUmfMenuZeile($sTxt,$sAct='',$sSes='',$sUmf='',$bCod=false){
 if($sUmf>''&&($a=explode('=',$sUmf))&&isset($a[1])){
  $sHid='<input type="hidden" name="'.$a[0].'" value="'.$a[1].'" />';
  if($bCod&&UMF_TeilnehmerMitCode) $sHid.='<input type="text" name="umf_Code" class="umfLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
  if($sAct=='frage') $sTxt=UMF_TxUmfrage.':<br />'.$sTxt; elseif($sAct=='grafik') $sTxt=UMF_TxErgebnis.':<br />'.$sTxt;
 }elseif($sTxt==UMF_TxStandardUmf&&$bCod&&UMF_TeilnehmerMitCode){
  $sHid='<input type="text" name="umf_Code" class="umfLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
 }else $sHid='';
 if(!$p=strpos(UMF_Self,'umf_Ablauf=')) $sAbl=''; else $sAbl='<input type="hidden" name="umf_Ablauf" value="'.((int)substr(UMF_Self,$p+11,2)).'" />';
 return "\n".'  <tr class="umfTr">
   <td class="umfMenu">'.fUmfTx(trim($sTxt)).'</td>
   <td class="umfMenu umfMen2"><form action="'.UMF_Self.'" method="get"><input type="hidden" name="umf_Aktion" value="'.$sAct.'" />'.$sHid.'<input type="submit" class="umfScha" value="OK" title="OK" />'.($sSes>''?'<input type="hidden" name="umf_Session" value="'.$sSes.'" />':'').'</form></td>
  </tr>';
}
?>