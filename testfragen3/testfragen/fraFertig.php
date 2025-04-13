<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){
  $nAnzahl=(isset($_POST['fra_Anzahl'])?$_POST['fra_Anzahl']:'');
  return fFraFertig($nAnzahl);
 }
}

function fFraFertig($nAnzahl){
 $sSelfLink=FRA_Self; if(!$sQueryString=substr(strstr(FRA_Self,'?'),1)) $sQueryString=$_SERVER['QUERY_STRING'];
 if(FRA_NachLoginWohin=='Zentrum'&&FRA_Session!=''&&substr(FRA_Session,4,1)<'9')
  $sSelfLink.=(strpos($sSelfLink,'?')>0?'&amp;':'?').'fra_Aktion=zentrum&fra_Session='.FRA_Session; //zum Zentrum
 elseif(FRA_Nutzerverwaltung>'0'&&FRA_Session!=''&&substr(FRA_Session,4,1)<'9')
  $sSelfLink.=(strpos($sSelfLink,'?')>0?'&amp;':'?').'fra_Session='.FRA_Session; //angemeldet bleiben

 if(!FRA_FertigHtml){
  //optionales Urkundenmodul
  if(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'A')){
   $sUk='</p><p class="fraMeld">'.FRA_TxUkErhalt;
   while($n=strpos($sUk,'#')){
    $l=strlen($sUk); $e=$l;
    for($i=$n+2;$i<$l;$i++) if(substr($sUk,$i,1)<'0'){$e=$i; break;}
    $sUk=substr_replace($sUk,'</a>',$e,0); $sUk=substr_replace($sUk,'<a class="fraText" href="'.FRA_Http.'urkunde.php?ses='.FRA_Session.(FRA_Ablauf?'&abl='.FRA_Ablauf:'').'" target="_uk">',$n,1);
   }
  }else $sUk='';
  //Ende Urkundenmodul
  $X='<p class="fraMeld">'.str_replace('#Z',$nAnzahl,fFraTx(!FRA_LernModus?FRA_TxAllesFertig.$sUk:FRA_TxLernOk)).'</p>
<table class="fraAnmk" border="0" cellpadding="8" cellspacing="0">
 <tr>
  <td class="fraBwrt">
'.fFraBB(fFraTx(FRA_TxFertigText)).'
  </td>
 </tr>
</table>
';
  if(strlen(FRA_TxNeuStart)>0) $X.='<p style="text-align:center;margin:16px;">[ <a class="fraXXXX" href="'.$sSelfLink.'">'.fFraTx(FRA_TxNeuStart).'</a> ]</p>'."\n";
 }else{
  $X=str_replace('#Z',$nAnzahl,str_replace('{Selbst}',$sSelfLink,str_replace('{QueryString}',$sQueryString,implode('',file(FRA_Pfad.'fraFertig.inc.htm')))));
  //optionales Urkundenmodul
  $X=str_replace('{Urkunde}',FRA_Http.'urkunde.php?ses='.FRA_Session.(FRA_Ablauf?'&abl='.FRA_Ablauf:''),$X);
  //Ende Urkundenmodul
 }
 return $X;
}
?>