<?php
function fFraSeite(){
 $Meld=''; $MTyp='Fehl'; $sSes=FRA_Session; $aE=array();
 $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $sId=substr($sSes,4,5); $sNam='???';

  $bSQLOpen=false; //SQL-Verbindung oeffnen
  if(FRA_SQL){
   $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
   if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
  }

  if(!FRA_SQL){ //Textdateien
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=((int)$sId).';'; $n=strlen($s);
   for($i=1;$i<$nSaetze;$i++){
    if(substr($aD[$i],0,$n)==$s){ //Nutzer gefunden
     $aN=explode(';',$aD[$i],4); $sNam=fFraDeCode($aN[2]);
     break;
   }}
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD); $s=$sId.';';
   for($i=1;$i<$nSaetze;$i++){
    $a=explode(';',rtrim($aD[$i]),14); if(substr($a[13],0,6)==$s) $aE[(int)$a[0]]=$a;
   }
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SELECT Nummer,Benutzer FROM '.FRA_SqlTabN.' WHERE Nummer="'.((int)$sId).'"')){
    $a=$rR->fetch_row(); $rR->close(); if($a[0]==(int)$sId) $sNam=$a[1];
   }
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' WHERE Benutzer LIKE "'.$sId.';%"')){
    while($a=$rR->fetch_row()) $aE[(int)$a[0]]=$a; $rR->close();
   }
  }//SQL
  $Meld=FRA_TxFuer.' &quot;'.$sNam.'&quot;'; $MTyp='Meld'; $nSaetze=count($aE); if(FRA_ZntErgebnisRueckw) krsort($aE);
 }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;

 $X='<p class="fraMeld" style="font-size:1.2em">'.fFraTx(FRA_TxErgebnisListe).'</p>'."\n";
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>'; $nSp=(FRA_NutzerLoesung?4+(FRA_Drucken?1:0):3);
 $X.='
 <table class="fraBwrt" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraBwrt"><b>'.fFraTx(FRA_TxDatum).'</b></td>
   <td class="fraBwrt" title="'.fFraTx(FRA_TxErgebnisDetails).'">&nbsp;</td>
   '.(FRA_NutzerLoesung?'<td class="fraBwrt" title="'.fFraTx(FRA_TxErgebnisLoesung).'">&nbsp;</td>':'').(FRA_NutzerLoesung&&FRA_Drucken?'<td class="fraBwrt" title="'.fFraTx(FRA_TxErgebnisDrucken).'">&nbsp;</td>':'').'
   './* optionales Urkundenmodul */(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'Z')?'<td class="fraBwrt" title="'.FRA_TxUkDrucken.'">&nbsp;</td>':'')/* Ende Urkundenmodul */.'
   <td class="fraBwrt" style="text-align:center;"><b>'.fFraTx(FRA_TxTestName).'</b></td>';
  if(FRA_DatAnzahlO&&FRA_ZntAnzahlO){$X.="\n".'   <td class="fraBwrt"><b>'.fFraTx(FRA_TxAnzahl).'</b></td>'; $nSp++;}
  if(FRA_DatRichtigeO&&FRA_ZntRichtigeO){$X.="\n".'   <td class="fraBwrt"><b>'.fFraTx(FRA_TxRichtig).'</b></td>'; $nSp++;}
  if(FRA_DatFalscheO&&FRA_ZntFalscheO){$X.="\n".'   <td class="fraBwrt"><b>'.fFraTx(FRA_TxFalsch).'</b></td>'; $nSp++;}
  if(FRA_DatPunkteO&&FRA_ZntPunkteO){$X.="\n".'   <td class="fraBwrt"><b>'.fFraTx(FRA_TxPunkte).'</b></td>'; $nSp++;}
 $X.="\n".'  </tr>';
 foreach($aE as $a){
  $X.="\n".'  <tr>'; $sD=$a[1];
  $X.="\n".'   <td class="fraBwrt">'.(!FRA_SQL?$sD:date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)))).'</td>';
  $X.="\n".'   <td class="fraBwrt"><a href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=ergebnis&amp;fra_Detail='.$a[0].'&amp;fra_Session='.$sSes.'"><img src="'.FRA_Http.'iconVorschau.gif" width="13" height="13" border="0" title="'.fFraTx(FRA_TxErgebnisDetails).'" alt="'.fFraTx(FRA_TxErgebnisDetails).'"></a></td>';
  if(FRA_NutzerLoesung) $X.="\n".'   <td class="fraBwrt"><a href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=zeige&amp;fra_Anzahl='.$a[3].'&amp;fra_Antwort='.str_replace('|',';',$a[10]).(FRA_NutzerLsgAlle?'&amp;fra_AlleLsg=1':'').'&amp;fra_Session='.$sSes.'"><img src="'.FRA_Http.'iconVorschau.gif" width="13" height="13" border="0" title="'.fFraTx(FRA_TxErgebnisLoesung).'" alt="'.fFraTx(FRA_TxErgebnisLoesung).'"></a></td>';
  if(FRA_NutzerLoesung&&FRA_Drucken) $X.="\n".'   <td class="fraBwrt"><a href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=druckErgebnis&amp;fra_Nr='.$a[0].'&amp;fra_Session='.$sSes.'" target="druck" onclick="druWin(this.href)"><img src="'.FRA_Http.'iconDrucken.gif" width="14" height="14" border="0" title="'.fFraTx(FRA_TxErgebnisDrucken).'" alt="'.fFraTx(FRA_TxErgebnisDrucken).'"></a></td>';
  //optionales Urkundenmodul
  if(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'Z')) $X.="\n".'   <td class="fraBwrt"><a href="'.FRA_Http.'urkunde.php?ses='.$sSes.'&nr='.$a[0].(FRA_Ablauf?'&abl='.FRA_Ablauf:'').'" target="_uk" title="'.FRA_UkDatei.'.pdf"><img src="'.FRA_Http.'iconDrucken.gif" width="16" height="14" border="0" title="'.fFraTx(FRA_TxUkDrucken).'"> </a></td>';
  //Ende Urkundenmodul
  $X.="\n".'   <td class="fraBwrt">'.fFraTx($a[12]?$a[12]:FRA_TxStandardTest).'</td>';
  if(FRA_DatAnzahlO&&FRA_ZntAnzahlO) $X.="\n".'   <td class="fraBwrt" style="text-align:center;">'.$a[3].'</td>';
  if(FRA_DatRichtigeO&&FRA_ZntRichtigeO) $X.="\n".'   <td class="fraBwrt" style="text-align:center;">'.$a[4].'</td>';
  if(FRA_DatFalscheO&&FRA_ZntFalscheO) $X.="\n".'   <td class="fraBwrt" style="text-align:center;">'.$a[5].'</td>';
  if(FRA_DatPunkteO&&FRA_ZntPunkteO) $X.="\n".'   <td class="fraBwrt" style="text-align:center;">'.rund($a[6]).'</td>';
  $X.="\n".'  </tr>';
 }
 $X.='
  <tr>
   <td class="fraBwrt" colspan="'.$nSp.'" style="text-align:center;">[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=zentrum&amp;fra_Session='.$sSes.'">'.fFraTx(FRA_TxBenutzerzentrum).'</a> ]</td>
  </tr>
 </table>';

 if(FRA_NutzerLoesung&&FRA_Drucken) $X.='
<script type="text/javascript">
 function druWin(sHRef){dWin=window.open(sHRef,"druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
</script>';

 return $X;
}

function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>