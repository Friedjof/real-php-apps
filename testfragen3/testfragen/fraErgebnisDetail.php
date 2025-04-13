<?php
function fFraSeite(){
 $Meld=''; $MTyp='Fehl'; $sSes=FRA_Session; $aE=array();
 $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $sId=substr($sSes,4,5); $sNam='???';
  if(isset($_GET['fra_Detail'])&&($nNr=(int)$_GET['fra_Detail'])){

   $bSQLOpen=false; //SQL-Verbindung oeffnen
   if(FRA_SQL){
    $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
    if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
   }

   if(!FRA_SQL){ //Textdateien
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=((int)$sId).';'; $n=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     if(substr($aD[$i],0,$n)==$s){//Nutzer gefunden
      $aN=explode(';',$aD[$i],4); $sNam=fFraDeCode($aN[2]);
      break;
    }}
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD); $sId=$nNr.';'; $n=strlen($sId);
    for($i=1;$i<$nSaetze;$i++){
     if(substr($aD[$i],0,$n)==$sId){//Ergebnis gefunden
      $aE=explode(';',$aD[$i],14);
      break;
    }}
    $sAntwort='#|'.$aE[10]; $aL=array(); $aP=array(); $nG=0;
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
    for($i=1;$i<$nSaetze;$i++){
     $s=$aD[$i];
     if(strpos($sAntwort,'|'.substr($s,0,strpos($s,';')).':')>0){
      $a=explode(';',$aD[$i],8); $n=(int)$a[0]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nG+=$a[6];
    }}
   }elseif($bSQLOpen){ //bei SQL
    if($rR=$DbO->query('SELECT Nummer,Benutzer FROM '.FRA_SqlTabN.' WHERE Nummer="'.((int)$sId).'"')){
     $a=$rR->fetch_row(); $rR->close(); if($a[0]==(int)$sId) $sNam=$a[1];
    }
    if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' WHERE Eintrag="'.$nNr.'"')){
     $aE=$rR->fetch_row(); $rR->close();
    }
    $sAntwort='#|'.$aE[10]; $aL=array(); $aP=array(); $nG=0;
    if($rR=$DbO->query('SELECT Nummer,Loesung,Punkte FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
     while($a=$rR->fetch_row()){$n=$a[0]; if(strpos($sAntwort,'|'.$n.':')>0){$aL[$n]=$a[1]; $aP[$n]=$a[2]; $nG+=$a[2];}}
     $rR->close();
    }
   }//SQL
   $Meld=FRA_TxFuer.' &quot;'.$sNam.'&quot;'; $MTyp='Meld';
  }else $Meld='';
 }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;

 $X='<p class="fraMeld" style="font-size:1.2em">'.fFraTx(FRA_TxErgebnisDetails).'</p>'."\n";
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>';
 $X.='
 <table class="fraBwrt" border="0" cellpadding="0" cellspacing="0">';
 if(count($aE)>1){
  $sD=$aE[1];
  $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxErgebnis.'-'.FRA_TxNr).'</td><td class="fraBwrt">'.sprintf('%05d',$nNr).'</td>'."\n  </tr>";
  $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxDatum).'</td><td class="fraBwrt">'.(!FRA_SQL?$sD:date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)))).'</td>'."\n  </tr>";
  $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxTestName).'</td><td class="fraBwrt">'.fFraTx($aE[12]?$aE[12]:FRA_TxStandardTest).'</td>'."\n  </tr>";
  if(FRA_DatZeitO&&FRA_ZntZeitO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxZeit).'</td><td class="fraBwrt">'.$aE[2].'</td>'."\n  </tr>";
  if(FRA_DatAnzahlO&&FRA_ZntAnzahlO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxAnzahl).'</td><td class="fraBwrt">'.$aE[3].'</td>'."\n  </tr>";
  if(FRA_DatRichtigeO&&FRA_ZntRichtigeO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxRichtig).'</td><td class="fraBwrt">'.$aE[4].' &nbsp; ('.($aE[3]>0?rund(100*$aE[4]/$aE[3]):'??').'%)</td>'."\n  </tr>";
  if(FRA_DatFalscheO&&FRA_ZntFalscheO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxFalsch).'</td><td class="fraBwrt">'.$aE[5].' &nbsp; ('.($aE[3]>0?rund(100*$aE[5]/$aE[3]):'??').'%)</td>'."\n  </tr>";
  if(FRA_DatPunkteO&&FRA_ZntPunkteO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxPunkte).'</td><td class="fraBwrt">'.rund($aE[6]).' '.fFraTx(FRA_TxVon).' '.$nG.' &nbsp; ('.($nG>0?rund(100*$aE[6]/$nG):'?').'%)</td>'."\n  </tr>";
  if(FRA_DatVerbalO){
   if(FRA_VerbalPunkte) $p=round(100*$aE[6]/max($nG,1)); else $p=round(100*$aE[4]/max($aE[3],1));
   $s=str_replace('#R',$aE[4],str_replace('#F',$aE[5],str_replace('#A',$aE[3],str_replace('#P',rund($aE[6]),str_replace('#G',$nG,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$aE[4],str_replace('#F',$aE[5],str_replace('#A',$aE[3],str_replace('#P',rund($aE[6]),str_replace('#G',$nG,constant('FRA_VerbalTx'.$i))))));
   $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxErgebnis).'</td><td class="fraBwrt">'.fFraTx($s).'</td>'."\n  </tr>";
  }
  if(FRA_DatVersucheO&&FRA_ZntVersucheO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxVersuche).'</td><td class="fraBwrt">'.$aE[7].'</td>'."\n  </tr>";
  if(FRA_DatAuslassenO&&FRA_ZntAuslassenO) $X.="\n  <tr>\n".'   <td class="fraBwrt">'.fFraTx(FRA_TxAuslassen).'</td><td class="fraBwrt">'.$aE[8].'</td>'."\n  </tr>";
  if(FRA_DatAntwort&&(FRA_ZntFrageNr||FRA_ZntAntwort||FRA_ZntLoesung||FRA_ZntErgebnis||FRA_ZntPunkte||FRA_ZntVersuche||FRA_ZntAuslassen)){
   $a=explode('|',$aE[10]); $nZl=count($a); $aA=array(); //Antwortkette
   for($i=0;$i<$nZl;$i++){$t=$a[$i]; $p=strpos($t,':'); $aA[(int)substr($t,0,$p)]=substr($t,++$p);}
   $a=explode('|',$aE[9]); $nZl=count($a); //Fragendetailkette
   $sFD ='<table class="fraBwrt" style="margin:0px" border="0" cellpadding="0" cellspacing="0"><tr>';
   $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxFrage.'-'.FRA_TxNr).'</td>';
   if(FRA_ZntAntwort) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxAntwort).'</td>';
   if(FRA_ZntLoesung) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxLoesung).'</td>';
   if(FRA_ZntErgebnis) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxErgebnis).'</td>';
   if(FRA_ZntPunkte) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxPunkte).'</td>';
   if(FRA_ZntVersuche) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxVersuche).'</td>';
   if(FRA_ZntAuslassen) $sFD.='<td class="fraBwrt">'.fFraTx(FRA_TxAuslassen).'</td>';
   $sFD.='</tr>';
   for($i=0;$i<$nZl;$i++){
    $t=$a[$i]; $p=strpos($t,':'); $n=substr($t,0,$p); $b=explode(',',substr($t,$p+1));
    $sFD.='<tr><td class="fraBwrt" style="text-align:center">'.$n.'</td>';
    if(FRA_ZntAntwort) $sFD.='<td class="fraBwrt" style="text-align:center">'.$aA[$n].'</td>';
    if(FRA_ZntLoesung) $sFD.='<td class="fraBwrt" style="text-align:center">'.(isset($aL[$n])?$aL[$n]:'???').'</td>';
    if(FRA_ZntErgebnis) $sFD.='<td class="fraBwrt" style="text-align:center">'.fFraTx($b[0]=='r'?FRA_TxRichtig:FRA_TxFalsch).'</td>';
    if(FRA_ZntPunkte) $sFD.='<td class="fraBwrt" style="text-align:center">'.(isset($b[1])?rund($b[1]):'?').'/'.(isset($aP[$n])?$aP[$n]:'??').'</td>';
    if(FRA_ZntVersuche) $sFD.='<td class="fraBwrt" style="text-align:center">'.(isset($b[2])?$b[2]:'??').'</td>';
    if(FRA_ZntAuslassen) $sFD.='<td class="fraBwrt" style="text-align:center">'.(isset($b[3])?$b[3]:'??').'</td>';
    $sFD.='</tr>';
   }
   $sFD.='</table>';
   $X.="\n  <tr>\n".'   <td class="fraBwrt" style="vertical-align:top;">'.fFraTx(FRA_TxAntwSq).'</td><td class="fraBwrt">'.$sFD.'</td>'."\n  </tr>";
  }

 }
 $X.='
  <tr>
   <td class="fraBwrt" colspan="2" style="text-align:center;">[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=ergebnis&amp;fra_Session='.$sSes.'">'.fFraTx(FRA_TxErgebnisListe).'</a> ]</td>
  </tr>
 </table>';

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