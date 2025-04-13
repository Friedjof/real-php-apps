<?php
include 'hilfsFunktionen.php'; //Zusagen zum Termin drucken
header('Content-Type: text/html; charset=ISO-8859-1');

$sKalHtmlVor=''; $sKalHtmlNach=''; $sKalHtmlNach=implode('',(file_exists('druckSeite.htm')?file('druckSeite.htm'):array())); $t='';
if($p=strpos($sKalHtmlNach,'{Inhalt}')){
 $sKalHtmlVor=substr($sKalHtmlNach,0,$p); $sKalHtmlNach=substr($sKalHtmlNach,$p+8); //Seitenkopf, Seitenfuss
 $sKalHtmlVor=str_replace('../grafik',$sHttp.'grafik',str_replace('{Titel}',KAL_TxZusageDrckTit,$sKalHtmlVor));
 $sKalHtmlNach=str_replace('../grafik',$sHttp.'grafik',$sKalHtmlNach);
}else{$sKalHtmlVor='<p style="color:#AA0033;">HTML-Layout-Schablone <i>druckSeite.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}

echo $sKalHtmlVor."\n"; $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);

if($sTId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:0)){
 $aZ=array(); $aT=array(); $aD=array(); //Daten holen
 if(!KAL_SQL){
  $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aTmp); $s=';'.$sTId.';'; $l=strlen($s);
  for($i=1;$i<$nSaetze;$i++){
   $sZ=rtrim($aTmp[$i]); $p=strpos($sZ,';'); if(substr($sZ,$p,$l)==$s){$aZ=explode(';',$sZ); $aZ[8]=fKalDeCode($aZ[8]); $aD[]=$aZ;}
  }
  $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aTmp); $s=$sTId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){$aT=explode(';',rtrim($aTmp[$i])); if(is_array($aT)) array_splice($aT,1,1); break;}
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE termin="'.$sTId.'"')){
   while($aZ=$rR->fetch_row()) $aD[]=$aZ; $rR->close();
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){
    $aT=$rR->fetch_row(); if(is_array($aT)) array_splice($aT,1,1); $rR->close();
   }else $t='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
  }else $t='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $t='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';

 $nKapaz=0; if($nKapazPos=(int)array_search('KAPAZITAET',$kal_FeldName)) if(isset($aT[$nKapazPos])) $nKapaz=(int)$aT[$nKapazPos];
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $nAnzahlPos=0; $nAnzSum=0;
 $kal_DrckFelder=explode(';',(KAL_ZusageAdmDrKonf?KAL_ZusageDrckAdm:KAL_ZusageListAdm)); $bMitNr=($kal_DrckFelder[0]>0); $kal_DrckFelder[0]=0; $aDrckFelder=array_flip($kal_DrckFelder);

 for($j=9;$j<=$nZusageFelder;$j++){
  if($kal_ZusageFelder[$j]=='ANZAHL'){$nAnzahlPos=$j; if(strlen(KAL_ZusageNameAnzahl)>0) $kal_ZusageFelder[$j]=KAL_ZusageNameAnzahl;}
 }

 if(KAL_TxZusageDrckTrm){
  $s=KAL_TxZusageDrckTrm; $a=explode('{',$s);
  if(is_array($a)) for($i=count($a)-1;$i>=0;$i--){
   $sF=$a[$i]; $sF=substr($sF,0,strpos($sF,'}'));
   if($p=array_search($sF,$kal_FeldName)){
    if($kal_FeldType[$p]!='d') $s=str_replace('{'.$sF.'}',$aT[$p],$s);
    else $s=str_replace('{'.$sF.'}',fKalAnzeigeDatum($aT[$p]),$s);
   }
  }
  $t.=NL.'<p>'.$s.'</p>';
 }
 echo $t;

 echo NL.'<table class="druck" border="0" cellpadding="0" cellspacing="0">';
 echo NL.' <tr class="druck">'; //Kopfzeile
 if($bMitNr) echo NL.'  <td class="druck" style="text-align:center"><b>Nr.</b></td>';
 for($j=2;$j<=$nZusageFelder;$j++) if(isset($aDrckFelder[$j])&&($i=$aDrckFelder[$j])){
  $sS=''; if($i<8&&$i!=4||$i==$nAnzahlPos) $sS=' style="text-align:center"';
  if($aZusageFeldTyp[$i]=='w') $sS=' style="text-align:center"';
  echo NL.'  <td class="druck"'.$sS.'><b>'.str_replace('`,',';',$kal_ZusageFelder[$i]).'</b></td>';
 }
 echo NL.' </tr>';
 foreach($aD as $a){ //Datenzeilen ausgeben
  echo NL.' <tr class="druck">';
  if($bMitNr) echo NL.'  <td class="druck" style="text-align:center">'.sprintf('%0'.KAL_NummerStellen.'d',$a[0]).'</td>';
  for($j=2;$j<=$nZusageFelder;$j++) if(isset($aDrckFelder[$j])&&($i=$aDrckFelder[$j])){
   $sS=''; if($i<8&&$i!=4||$i==$nAnzahlPos) $sS=' style="text-align:center"'; if($i==$nAnzahlPos) $nAnzSum+=$a[$i];
   switch($i){
    case 1: $s=sprintf('%0'.KAL_NummerStellen.'d',$a[1]); break;
    case 2: $s=fKalAnzeigeDatum($a[2]); break;
    case 5: $s=fKalAnzeigeDatum(substr($a[5],0,10)).substr($a[5],10); break;
    case 6: $s='<img src="'.$sHttp.'grafik/punkt'.($a[6]=='1'?'Grn':($a[6]=='0'?'Rot':($a[6]=='-'?'RotX':($a[6]=='2'?'RtGn':($a[6]=='*'?'RtGnX':($a[6]=='7'?'Glb':'Rot')))))).'.gif" width="12" height="12" border="0">';  break;
    case 7: $s=sprintf('%0'.KAL_NummerStellen.'d',$a[7]); break;
    case $nAnzahlPos: $s=abs($a[$i]); break;
    default: $s=(isset($a[$i])?str_replace('`,',';',$a[$i]):'');
   }
   if($aZusageFeldTyp[$i]=='w'){$s=(float)$s;if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen).'&nbsp;'.KAL_Waehrung; else $s=''; $sS=' style="text-align:right"';}
   echo NL.'  <td class="druck"'.$sS.'>'.$s.'</td>';
  }
  echo NL.' </tr>';
 }
 echo NL.'</table>';
 if(KAL_TxZusageDrckSum) echo NL.'<p>'.str_replace('#K',$nKapaz,str_replace('#Z',$nAnzSum,str_replace('#B',count($aD),KAL_TxZusageDrckSum))).'</p>';
}else echo NL.'<p class="admFehl">Ungültiger Seitenaufruf ohne Terminummer!</p>';

echo NL.$sKalHtmlNach;
?>