<?php
function fFraBewerten($bVonFrage){ //Seiteninhalt

 $sAktion='ende'; $X="\n";
 if($bVonFrage){//von Fragen kommend
  $sAntwort='#;'.FRA_FertigAntwort; $sVerlauf=FRA_FertigVerlauf; $sSes=FRA_Session; $nStart=(int)FRA_Zeit;
  $sZeit=time()-(int)FRA_Zeit; if($sZeit<3600) $sZeit=date('i:s',$sZeit); else $sZeit=date('H:i:s',$sZeit);
 }else{//von Erfassen kommend
  $sAntwort='#;'.FRA_Antwort; $sVerlauf=FRA_Verlauf; $sSes=FRA_FertigSession; $sZeit=FRA_Zeit;
  $nStart=(isset($_POST['fra_Start'])?(int)$_POST['fra_Start']:time());
 }

 $nFragen=(strlen($sAntwort)>2?substr_count($sAntwort,';'):0); $Meld=FRA_TxBewertungHier;
 //optionales Urkundenmodul
 if(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'B')){
  $sUkM='</p><p class="fraMeld">'.FRA_TxUkErhalt;
  while($n=strpos($sUkM,'#')){
   $l=strlen($sUkM); $e=$l;
   for($i=$n+2;$i<$l;$i++) if(substr($sUkM,$i,1)<'0'){$e=$i; break;}
   $sUkM=substr_replace($sUkM,'</a>',$e,0); $sUkM=substr_replace($sUkM,'<a class="fraText" href="urkunde.php?ses='.$sSes.(FRA_Ablauf?'&abl='.FRA_Ablauf:'').'" target="_uk">',$n,1);
  }
 }else $sUkM='';
 //Ende Urkundenmodul
 $Meld='<p class="fraMeld">'.str_replace('#Z',$nFragen,fFraTx(defined('FRA_UeberZeitLimit')?FRA_TxZeitLimit:$Meld.$sUkM)).'</p>';

 $bSQLOpen=false; $aL=array(); $aP=array(); $aZ=array(); $aK=array(); $aTP=array(0); //Datenbasis vorbereiten
 if(!FRA_SQL){
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',$aD[$i]); $n=$a[0];
   if(strpos($sAntwort,';'.$n.':')>0){
    $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
    for($j=8;$j<17;$j++){
     $t=trim($a[$j]);
     if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
     if(!empty($t)) $nA++; else break;
    }
    $aZ[$n]=$nA;
 }}}else{ //SQL
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){
   $bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
    while($a=$rR->fetch_row()){ $n=$a[0];
     if(strpos($sAntwort,';'.$n.':')>0){
      $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
      for($j=8;$j<17;$j++){
       $t=trim($a[$j]);
       if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
       if(!empty($t)) $nA++; else break;
      }
      $aZ[$n]=$nA;
    }}
    $rR->close();
   }else $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlFrage).'</p>';
  }else $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlVrbdg).'</p>';
 }

 $nR=0; $nF=0; $nP=0; $nA=0; $nV=0; $nS=0; $aV=array(); $aKt=array(); //Richtig,Falsch,Punkte,Antwortversuche,Verschiebungen,punktSumme
 $sAntwort=str_replace(';','|',substr($sAntwort,2)); $aA=explode('|',$sAntwort); //Antwortkette untersuchen
 if($nFragen>0) foreach($aA as $t){
  $k=strpos($t,':'); $i=substr($t,0,$k); $t=substr($t,++$k); $a=array('r'=>0,'f'=>0,'p'=>0,'a'=>0,'v'=>0);
  if($t==$aL[$i]){$a['r']=1; $nR++; if(FRA_PositivWertung) $a['p']=$aP[$i];} //komplett richtig
  elseif($t=='-'){$a['f']=1; $nF++; if(!FRA_PositivWertung) $a['p']=$aP[$i];} //Zeitlimit
  else{ //nicht richtig
   $a['f']=1; $nF++; $nW=0;
   if(FRA_TeilWertung>0){ //Teilantworten untersuchen
    $sA='*,'.$t; $sL='*,'.$aL[$i]; $nZ=$aZ[$i];
    for($j=1;$j<=$nZ;$j++){
     if(FRA_TeilWertung==1){if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nW++;} //nur Richtige
     else{if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||strpos($sL,','.$j)<=0&&strpos($sA,','.$j)<=0) $nW++;} //auch Falsche
    }
    switch(FRA_TeilWertung){
     case 1: //nur Richtige
      if(FRA_PunkteTeilen) $a['p']=$aP[$i]*$nW/substr_count($sL,',');
      else{for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $a['p']+=$aTP[$i][$j];}
      break;
     case 2: //jede Entscheidung anteilig
      if(FRA_PunkteTeilen){if(substr_count($sA,',')<$nZ) $a['p']=$aP[$i]*$nW/$nZ;}
      else{
       $nMinP=$aP[$i]; $nFak=0;
       for($j=1;$j<=$nZ;$j++) if($aTP[$i][$j]>0) $nMinP=min($nMinP,$aTP[$i][$j]);
       for($j=1;$j<=$nZ;$j++){if($aTP[$i][$j]==0) $aTP[$i][$j]=$nMinP; $nFak+=$aTP[$i][$j];} $nFak=$aP[$i]/max($nFak,1);
       for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $a['p']+=$nFak*$aTP[$i][$j];
      }
      break;
     case 3: if(substr_count($sA,',')<$nZ){$nW=$nW/$nZ; if($nW>0.99) $a['p']=$aP[$i]; elseif($nW>=0.5) $a['p']=0.5*$aP[$i];} break; //50%-Regel
     case 4: if(substr_count($sA,',')<$nZ){$nW=$nZ-$nW; if($nW==0) $a['p']=$aP[$i]; elseif($nW==1) $a['p']=0.5*$aP[$i];} break; //0-1-2-Fehler -> 100%-50%-0%
    }
    if(FRA_TeilWertung>1) if(substr_count($sA,',')>=$nZ) $a['p']=0; //abstrafen
    if(!FRA_PositivWertung) $a['p']=$aP[$i]-$a['p'];
   }elseif(!FRA_PositivWertung) $a['p']=$aP[$i]; //komplett Fehlerpunkte
  }
  $aV[$i]=$a; $nP+=$a['p']; $nS+=$aP[$i];
  //pro Kategorie summieren
  $t=$aK[$i]; if(!isset($aKt[$t])) $aKt[$t]=array('r'=>0,'f'=>0,'p'=>0,'s'=>0,'z'=>0);
  if($a['r']) ++$aKt[$t]['r']; else ++$aKt[$t]['f']; ++$aKt[$t]['z'];
  $aKt[$t]['p']+=$a['p']; $aKt[$t]['s']+=$aP[$i];
 }
 $sVerlauf=str_replace(';','|',$sVerlauf); $aA=explode('|',$sVerlauf); //Verlaufskette fuer Antwortversuche/Verschiebungen
 if($nFragen>0) foreach($aA as $t){$i=(int)$t; if(isset($aV[$i])){$aV[$i]['a']++; $nA++; if(strpos($t,'-')){$aV[$i]['v']++; $nV++;}}}

 $sPerson=''; $sEml=''; $nEml=-1; $aF=array(); $aNF=array(); $aU=NULL; $aUsr=NULL; $bNutzer=false; $bTln=false; //Teilnehmerdaten holen
 if($sNr=substr($sSes,4,5)){//wenn nicht anonym
  if($bNutzer=substr($sNr,0,1)<'9'){//Benutzer
   $sNr=(int)$sNr; $aNF=explode(';',FRA_NutzerFelder); $aF=array($aNF[0],$aNF[2],$aNF[4]); $nEml=2;
   if(!FRA_SQL){//Textdatei
    $aN=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $n=count($aN)-1; $sNr.=';'; $l=strlen($sNr);
    for($i=$n;$i>0;$i--) if(substr($aN[$i],0,$l)==$sNr){
     $aUsr=explode(';',rtrim($aN[$i]));
     $aU[0]=$aUsr[0]; $aU[1]=fFraDeCodeB($aUsr[2]); $aUsr[2]=$aU[1]; $aU[2]=fFraDeCodeB($aUsr[4]); $aUsr[4]=$aU[2];
     break;
    }
   }elseif($bSQLOpen) if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sNr.'"')){
    if($aUsr=$rR->fetch_row()){$aU[0]=$aUsr[0]; $aU[1]=$aUsr[2]; $aU[2]=$aUsr[4];} $rR->close();
   }
  }else{//Teilnehmer
   $aN=@file(FRA_Pfad.'temp/'.substr($sSes,0,9).'.ses'); if(is_array($aN)) $aU=explode(';',$aN[0]); $aUsr=$aU; $bTln=true;
   $aF=explode(';',FRA_TeilnehmerFelder); $aNF=$aF; for($i=count($aF)-1;$i>=0;$i--) if(stristr($aF[$i],'mail')) $nEml=$i;
  }
  $n=count($aF); if($nEml>=0) $sEml=$aU[$nEml];
  for($i=0;$i<$n;$i++) $sPerson.=($aF[$i]!='GUELTIG_BIS'?$aF[$i]:(FRA_TxNutzerFrist>''?FRA_TxNutzerFrist:$aF[$i])).': '.trim($aU[$i])."\n";
 }

 $sTestName=(defined('FRA_TestFolgeName')&&FRA_TestFolgeName?FRA_TestFolgeName:''); $sVorText=$sNachText='';
 if(FRA_Zeichensatz==2) $sTestName=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sTestName);
 if($sTestName&&(FRA_BldVorAw||FRA_BldNachAw||FRA_TlnVorAw||FRA_TlnNachAw)){ // Vor- oder Nachtext zur Folge holen
  if(!FRA_SQL){ //Textdateien
   $a=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $p=strlen($sTestName)+1;
   if(is_array($a)) foreach($a as $t) if(substr($t,0,$p)==$sTestName.';'){ //gefunden
    $a=explode(';',rtrim($t)); if(isset($a[6])&&$a[6]) $sVorText=$a[6]; if(isset($a[7])&&$a[7]) $sNachText=$a[7];
    break;
   }
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SELECT Folge,VorAw,NachAw FROM '.FRA_SqlTabT.' WHERE Folge="'.$sTestName.'"')){
    if($a=$rR->fetch_row()){if(isset($a[1])&&$a[1]) $sVorText=str_replace("\r\n",'\n ',$a[1]); if(isset($a[2])&&$a[2]) $sNachText=str_replace("\r\n",'\n ',$a[2]);} $rR->close();
   }
  }//SQL
 }
 $sTestName=($sTestName?$sTestName:(isset($_POST['fra_Spontantest'])&&$_POST['fra_Spontantest']==1?FRA_TxSpontanFolge:FRA_TxStandardTest));
 $sVorText=str_replace('#T',$sTestName,str_replace('#D',date('d.m.Y, H:i').' Uhr',$sVorText)); $n=count($aNF); $s='#'.$sVorText;
 for($i=0;$i<$n;$i++) if(strpos($s,'{'.$aNF[$i].'}')) $sVorText=str_replace('{'.$aNF[$i].'}',$aUsr[$i],$sVorText);

 if($sVorText&&FRA_BldVorAw) $X.="\n<div>".str_replace('\n ','<br />',$sVorText)."</div>\n"; //Bildschirmauswertung
 $X.="\n".'<table class="fraBwrt" border="0" cellpadding="0" cellspacing="0">';
 if(FRA_BldTestName) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxTestName).'</td><td class="fraBwrt">'.fFraTx($sTestName).'</td></tr>';
 if(FRA_BldAnzahlO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxAnzahl).'</td><td class="fraBwrt">'.$nFragen.'</td></tr>';
 if(FRA_BldZeitO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxZeit).'</td><td class="fraBwrt">'.$sZeit.'</td></tr>';
 if($nFragen>0){
  if(FRA_BldRichtigeO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxRichtige).'</td><td class="fraBwrt">'.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)</td></tr>';
  if(FRA_BldFalscheO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxFalsche).'</td><td class="fraBwrt">'.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)</td></tr>';
  if(FRA_BldPunkteO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxPunkte).'</td><td class="fraBwrt">'.rund($nP).' '.fFraTx(FRA_TxVon).' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)</td></tr>';
  if(FRA_BldVerbalO){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxErgebnis).'</td><td class="fraBwrt">'.fFraTx($s).'</td></tr>';
  }
  if(FRA_BldVersucheO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxVersuche).'</td><td class="fraBwrt">'.$nA.'</td></tr>';
  if(FRA_BldAuslassenO) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxAuslassen).'</td><td class="fraBwrt">'.$nV.'</td></tr>';
  reset($aV); $i=0;
  if(FRA_BldErgebnis||FRA_BldPunkte||FRA_BldVersuche||FRA_BldAuslassen) foreach($aV as $k=>$j){
   $s=''; $i++;
   if(FRA_BldAlleNr||!$j['r']){
    if(FRA_BldErgebnis) $s=fFraTx($j['r']?FRA_TxRichtig:FRA_TxFalsch).'; ';
    if(FRA_BldPunkte) $s.=rund($j['p']).' '.fFraTx(FRA_TxVon).' '.$aP[$k].' '.fFraTx(FRA_TxPunkte).'; ';
    if(FRA_BldVersuche) $s.=$j['a'].' '.fFraTx(FRA_TxVersuche).'; '; if(FRA_BldAuslassen)$s.=$j['v'].' '.fFraTx(FRA_TxAuslassen).'; ';
    $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxFrage).' '.$i.' ('.fFraTx(FRA_TxNr).$k.')</td><td class="fraBwrt">'.substr($s,0,-2).'</td></tr>';
  }}
  if(FRA_BldAnzahlU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxAnzahl).'</td><td class="fraBwrt">'.$nFragen.'</td></tr>';
 }
 if(FRA_BldZeitU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxZeit).'</td><td class="fraBwrt">'.$sZeit.'</td></tr>';
 if($nFragen>0){
  if(FRA_BldKatErgebnis||FRA_BldKatFehlErgb||FRA_BldKatPunkte){
   if(FRA_BldKatSumme){ //zu Hauptkategorien zusammenfassen
    reset($aKt); $aKS=array(); foreach($aKt as $k=>$j) if(!strpos($k,'#')) $aKS[$k]=$j;
    reset($aKt); foreach($aKt as $k=>$j) if($p=strpos($k,'#')){
     $k=substr($k,0,$p); if(!isset($aKS[$k])){$aKS[$k]['r']=0; $aKS[$k]['f']=0; $aKS[$k]['p']=0; $aKS[$k]['s']=0; $aKS[$k]['z']=0;}
     $aKS[$k]['r']+=$j['r']; $aKS[$k]['f']+=$j['f']; $aKS[$k]['p']+=$j['p']; $aKS[$k]['s']+=$j['s']; $aKS[$k]['z']+=$j['z'];
    }
   }else $aKS=$aKt;
   foreach($aKS as $k=>$j){ //Kategoriesummen ausgeben
    $X.="\n".' <tr><td class="fraBwrt" style="vertical-align:top">'.fFraTx(FRA_TxKategorie).'<br>'.fFraTx(str_replace('#','<br>&nbsp;-',$k)).'</td><td class="fraBwrt">';
    if(FRA_BldKatErgebnis) $X.='<div class="fraNorm">'.fFraTx(FRA_TxRichtige).': '.$j['r'].' ('.round(100*$j['r']/max($j['z'],1)).'%)</div>';
    if(FRA_BldKatFehlErgb) $X.='<div class="fraNorm">'.fFraTx(FRA_TxFalsche).': '.$j['f'].' ('.round(100*$j['f']/max($j['z'],1)).'%)</div>';
    if(FRA_BldKatPunkte) $X.='<div class="fraNorm">'.fFraTx(FRA_TxPunkte).': '.rund($j['p']).' '.fFraTx(FRA_TxVon).' '.$j['s'].' ('.round(100*$j['p']/max($j['s'],1)).'%)</div>';
    $X.='</td></tr>';
  }}
  if(FRA_BldRichtigeU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxRichtige).'</td><td class="fraBwrt">'.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)</td></tr>';
  if(FRA_BldFalscheU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxFalsche).'</td><td class="fraBwrt">'.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)</td></tr>';
  if(FRA_BldPunkteU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxPunkte).'</td><td class="fraBwrt">'.rund($nP).' '.fFraTx(FRA_TxVon).' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)</td></tr>';
  if(FRA_BldVerbalU){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxErgebnis).'</td><td class="fraBwrt">'.fFraTx($s).'</td></tr>';
  }
  if(FRA_BldVersucheU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxVersuche).'</td><td class="fraBwrt">'.$nA.'</td></tr>';
  if(FRA_BldAuslassenU) $X.="\n".' <tr><td class="fraBwrt">'.fFraTx(FRA_TxAuslassen).'</td><td class="fraBwrt">'.$nV.'</td></tr>';
 }
 $X.="\n".'</table>';
 if($sNachText&&FRA_BldNachAw) $X.="\n<div>".str_replace('\n ','<br />',$sNachText)."</div>\n";

 if(strlen($sSes)>0&&is_array($aU)&&$nFragen>0){ //Ergebnisdatei-Eintragung
  $sMTx=(FRA_DatZeitO||FRA_ZntZeitO?$sZeit:'').';'.(FRA_DatAnzahlO||FRA_ZntAnzahlO?$nFragen:'').';'.(FRA_DatRichtigeO||FRA_ZntRichtigeO?$nR:'').';'.(FRA_DatFalscheO||FRA_ZntFalscheO?$nF:'').';'.(FRA_DatPunkteO||FRA_ZntPunkteO?round($nP,2):'').';'.(FRA_DatVersucheO||FRA_ZntVersucheO?$nA:'').';'.(FRA_DatAuslassenO||FRA_ZntAuslassenO?$nV:'').';';
  /* ksort($aV); */ reset($aV); $i=1;
  if(FRA_DatFrageNr||FRA_ZntFrageNr||FRA_DatErgebnis||FRA_ZntErgebnis||FRA_DatPunkte||FRA_ZntPunkte||FRA_DatVersuche||FRA_ZntVersuche||FRA_DatAuslassen||FRA_ZntAuslassen) foreach($aV as $k=>$j){$s='';
   if(FRA_DatFrageNr||FRA_ZntFrageNr) $s=$k.':';
   if(FRA_DatErgebnis||FRA_ZntErgebnis) $s.=$j['r']?'r,':'f,'; if(FRA_DatPunkte||FRA_ZntPunkte) $s.=round($j['p'],2).',';
   if(FRA_DatVersuche||FRA_ZntVersuche) $s.=$j['a'].','; if(FRA_DatAuslassen||FRA_ZntAuslassen) $s.=$j['v'].',';
   $sMTx.=substr($s,0,-1).'|';
  }
  if(substr($sMTx,-1)=='|') $sMTx=substr($sMTx,0,-1); $sMTx.=';';
  if(FRA_DatAntwort) $sMTx.=$sAntwort; $sMTx.=';';
  if(FRA_DatVerlauf) $sMTx.=$sVerlauf; $sMTx.=';';
  $sMTx.=$sTestName.';';
  if(substr($sMTx,0,10)!=';;;;;;;;;;') if(!FRA_SQL){//Textdatei
   $aE=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis);
   $aE[0]='Eintrag;Datum;Dauer;Anzahl;Richtige;Falsche;Punkte;Versuche;Auslassungen;Bewertung;Antwortkette;Verlaufskette;Testfolge;Benutzer'."\n";
   $sZl=((int)substr($aE[count($aE)-1],0,8)+1).';'.date(FRA_Datumsformat,$nStart).';'.$sMTx;
   if($n=count($aF)){$sZl.=($bNutzer?sprintf('%05d',$aU[0]):$aU[0]); for($i=1;$i<$n;$i++) $sZl.=';'.trim($aU[$i]);}
   if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Ergebnis,'w')){
    fwrite($f,str_replace("\r",'',rtrim(implode('',$aE)))."\n".trim($sZl)."\n"); fclose($f);
   }else $Meld='<p class="fraFehl">'.str_replace('#',FRA_Daten.FRA_Ergebnis,fFraTx(FRA_TxDateiRechte)).'</p>';
  }elseif($bSQLOpen){
   $aE=explode(';',$sMTx); $sZl='';
   if($n=count($aF)){$sZl=($bNutzer?sprintf('%05d',$aU[0]):$aU[0]); for($i=1;$i<$n;$i++) $sZl.=';'.trim($aU[$i]);}
    if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabE.' (Datum,Dauer,Anzahl,Richtige,Falsche,Punkte,Versuche,Auslassungen,Bewertung,Antwortkette,Verlaufskette,Testfolge,Benutzer) VALUES("'.date('Y-m-d H:i',$nStart).'","'.$aE[0].'","'.$aE[1].'","'.$aE[2].'","'.$aE[3].'","'.$aE[4].'","'.$aE[5].'","'.$aE[6].'","'.$aE[7].'","'.$aE[8].'","'.$aE[9].'","'.$aE[10].'","'.$sZl.'")')){
    if($DbO->affected_rows!=1) $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlEinfg).'</p>';
   }else $Meld='<p class="fraFehl">'.fFraTx(FRA_TxSqlEinfg).'</p>';
  }
 }

 //Teilnehmer-E-Mail
 if($sEml&&preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sEml))&&$nFragen>0){$sMTx='';
  if(FRA_TlnPersonO) $sMTx.="\n".$sPerson;
  if(FRA_TlnTestName) $sMTx.="\n".FRA_TxTestName.': '.$sTestName;
  if(FRA_TlnAnzahlO) $sMTx.="\n".FRA_TxAnzahl.': '.$nFragen;
  if(FRA_TlnZeitO) $sMTx.="\n".FRA_TxZeit.': '.$sZeit;
  if(FRA_TlnRichtigeO) $sMTx.="\n".FRA_TxRichtige.': '.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)';
  if(FRA_TlnFalscheO) $sMTx.="\n".FRA_TxFalsche.': '.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)';
  if(FRA_TlnPunkteO) $sMTx.="\n".FRA_TxPunkte.': '.rund($nP).' '.FRA_TxVon.' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)';
  if(FRA_TlnVerbalO){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $sMTx.="\n".FRA_TxErgebnis.': '.$s;
  }
  if(FRA_TlnVersucheO) $sMTx.="\n".FRA_TxVersuche.': '.$nA;
  if(FRA_TlnAuslassenO) $sMTx.="\n".FRA_TxAuslassen.': '.$nV;
  reset($aV); $i=0;
  if(FRA_TlnErgebnis||FRA_TlnPunkte||FRA_TlnVersuche||FRA_TlnAuslassen) foreach($aV as $k=>$j){
   $s=''; $i++;
   if(FRA_TlnAlleNr||!$j['r']){
    if(FRA_TlnErgebnis) $s=($j['r']?FRA_TxRichtig:FRA_TxFalsch).'; ';
    if(FRA_TlnPunkte) $s.=rund($j['p']).' '.FRA_TxVon.' '.$aP[$k].' '.FRA_TxPunkte.'; ';
    if(FRA_TlnVersuche) $s.=$j['a'].' '.FRA_TxVersuche.'; ';
    if(FRA_TlnAuslassen) $s.=$j['v'].' '.FRA_TxAuslassen.'; ';
    $sMTx.="\n ".FRA_TxFrage.' '.$i.' ('.FRA_TxNr.$k.'): '.substr($s,0,-2);
  }}
  if(FRA_TlnAnzahlU) $sMTx.="\n".FRA_TxAnzahl.': '.$nFragen;
  if(FRA_TlnZeitU) $sMTx.="\n".FRA_TxZeit.': '.$sZeit;
  if(FRA_TlnKatErgebnis||FRA_TlnKatFehlErgb||FRA_TlnKatPunkte){
   if(FRA_TlnKatSumme){ //zu Hauptkategorien zusammenfassen
    reset($aKt); $aKS=array(); foreach($aKt as $k=>$j) if(!strpos($k,'#')) $aKS[$k]=$j;
    reset($aKt); foreach($aKt as $k=>$j) if($p=strpos($k,'#')){
     $k=substr($k,0,$p); if(!isset($aKS[$k])){$aKS[$k]['r']=0; $aKS[$k]['f']=0; $aKS[$k]['p']=0; $aKS[$k]['s']=0; $aKS[$k]['z']=0;}
     $aKS[$k]['r']+=$j['r']; $aKS[$k]['f']+=$j['f']; $aKS[$k]['p']+=$j['p']; $aKS[$k]['s']+=$j['s']; $aKS[$k]['z']+=$j['z'];
    }
   }else $aKS=$aKt;
   foreach($aKS as $k=>$j){ //Kategoriesummen ausgeben
   $sMTx.="\n".FRA_TxKategorie.' '.str_replace('#','->',$k).': ';
    if(FRA_TlnKatErgebnis) $sMTx.="\n ".FRA_TxRichtige.': '.$j['r'].' ('.round(100*$j['r']/max($j['z'],1)).'%)';
    if(FRA_TlnKatFehlErgb) $sMTx.="\n ".FRA_TxFalsche.': '.$j['f'].' ('.round(100*$j['f']/max($j['z'],1)).'%)';
    if(FRA_TlnKatPunkte) $sMTx.="\n ".FRA_TxPunkte.': '.rund($j['p']).' '.FRA_TxVon.' '.$j['s'].' ('.round(100*$j['p']/max($j['s'],1)).'%)';
  }}
  if(FRA_TlnRichtigeU) $sMTx.="\n".FRA_TxRichtige.': '.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)';
  if(FRA_TlnFalscheU) $sMTx.="\n".FRA_TxFalsche.': '.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)';
  if(FRA_TlnPunkteU) $sMTx.="\n".FRA_TxPunkte.': '.rund($nP).' '.FRA_TxVon.' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)';
  if(FRA_TlnVerbalU){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $sMTx.="\n".FRA_TxErgebnis.': '.$s;
  }
  if(FRA_TlnVersucheU) $sMTx.="\n".FRA_TxVersuche.': '.$nA;
  if(FRA_TlnAuslassenU) $sMTx.="\n".FRA_TxAuslassen.': '.$nV;
  if(FRA_TlnAntwort) $sMTx.="\n".FRA_TxAntwSq.': '.$sAntwort;
  if(FRA_TlnVerlauf) $sMTx.="\n".FRA_TxLaufSq.': '.$sVerlauf;
  if(FRA_TlnPersonU) $sMTx.="\n\n".$sPerson;
  if(strlen($sMTx)>0){
   if($sVorText&&FRA_TlnVorAw) $sMTx=str_replace('\n ',"\n",$sVorText)."\n".$sMTx;
   elseif(FRA_TlnVorspann){
    $sVorText=str_replace('#T',$sTestName,str_replace('#D',date('d.m.Y, H:i').' Uhr',FRA_TlnVorspann)); $n=count($aNF); $s='#'.$sVorText;
    for($i=0;$i<$n;$i++) if(strpos($s,'{'.$aNF[$i].'}')) $sVorText=str_replace('{'.$aNF[$i].'}',$aUsr[$i],$sVorText);
    $sMTx=str_replace('\n ',"\n",$sVorText)."\n".$sMTx;
   }
   if($sNachText&&FRA_TlnNachAw) $sMTx.="\n\n".str_replace('\n ',"\n",$sNachText);
   else if(FRA_TlnAbspann) $sMTx.="\n\n".str_replace('\n ',"\n",FRA_TlnAbspann);

   //optionales Urkundenmodul
   if(defined('FRA_UkErhalt')&&strstr(FRA_UkErhalt,'M')){
    require_once(FRA_Pfad.'class.plainmailpdf.php'); $Mailer=new PlainMailPdf();
    $bUkOK=true; $sUkErr='Fehler beim Erzeugen des PDF-Anhangs!'; require_once(FRA_Pfad.'urkunde.inc.php');
    if($bUkOK){
     $Mailer->PdfName=FRA_UkDatei.'.pdf'; $Mailer->Attachment=$pdf->Output(FRA_UkDatei.'.pdf','S');;
    }else{
     $Mailer->PdfName='Fehler.txt'; $Mailer->Attachment=$sUkErr;
    }
   }else{
    //Ende Urkundenmodul
    require_once(FRA_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
   }
   $Mailer->AddTo($sEml); $Mailer->SetReplyTo($sEml);
   if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
   $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
   $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
   $Mailer->Subject=FRA_TlnBetreff; $Mailer->Text=$sMTx; $Mailer->Send();
  }
 }

 $sMTx=''; //Administrator-e-Mail
 if(FRA_AdmPersonO) $sMTx.="\n".$sPerson;
 if(FRA_AdmTestName) $sMTx.="\n".FRA_TxTestName.': '.$sTestName;
 if(FRA_AdmAnzahlO) $sMTx.="\n".FRA_TxAnzahl.': '.$nFragen;
 if(FRA_AdmZeitO) $sMTx.="\n".FRA_TxZeit.': '.$sZeit;
 if($nFragen>0){
  if(FRA_AdmRichtigeO) $sMTx.="\n".FRA_TxRichtige.': '.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)';
  if(FRA_AdmFalscheO) $sMTx.="\n".FRA_TxFalsche.': '.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)';
  if(FRA_AdmPunkteO) $sMTx.="\n".FRA_TxPunkte.': '.rund($nP).' '.FRA_TxVon.' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)';
  if(FRA_AdmVerbalO){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $sMTx.="\n".FRA_TxErgebnis.': '.$s;
  }
  if(FRA_AdmVersucheO) $sMTx.="\n".FRA_TxVersuche.': '.$nA;
  if(FRA_AdmAuslassenO) $sMTx.="\n".FRA_TxAuslassen.': '.$nV;
  reset($aV); $i=0;
  if(FRA_AdmErgebnis||FRA_AdmPunkte||FRA_AdmVersuche||FRA_AdmAuslassen) foreach($aV as $k=>$j){
   $s=''; $i++;
   if(FRA_AdmAlleNr||!$j['r']){
    if(FRA_AdmErgebnis) $s=($j['r']?FRA_TxRichtig:FRA_TxFalsch).'; ';
    if(FRA_AdmPunkte) $s.=rund($j['p']).' '.FRA_TxVon.' '.$aP[$k].' '.FRA_TxPunkte.'; ';
    if(FRA_AdmVersuche) $s.=$j['a'].' '.FRA_TxVersuche.'; '; if(FRA_AdmAuslassen)$s.=$j['v'].' '.FRA_TxAuslassen.'; ';
    $sMTx.="\n ".FRA_TxFrage.' '.$i.' ('.FRA_TxNr.$k.'): '.substr($s,0,-2);
  }}
  if(FRA_AdmAnzahlU) $sMTx.="\n".FRA_TxAnzahl.': '.$nFragen;
 }
 if(FRA_AdmZeitU) $sMTx.="\n".FRA_TxZeit.': '.$sZeit;
 if($nFragen>0){
  if(FRA_AdmKatErgebnis||FRA_AdmKatFehlErgb||FRA_AdmKatPunkte){
   if(FRA_AdmKatSumme){ //zu Hauptkategorien zusammenfassen
    reset($aKt); $aKS=array(); foreach($aKt as $k=>$j) if(!strpos($k,'#')) $aKS[$k]=$j;
    reset($aKt); foreach($aKt as $k=>$j) if($p=strpos($k,'#')){
     $k=substr($k,0,$p); if(!isset($aKS[$k])){$aKS[$k]['r']=0; $aKS[$k]['f']=0; $aKS[$k]['p']=0; $aKS[$k]['s']=0; $aKS[$k]['z']=0;}
     $aKS[$k]['r']+=$j['r']; $aKS[$k]['f']+=$j['f']; $aKS[$k]['p']+=$j['p']; $aKS[$k]['s']+=$j['s']; $aKS[$k]['z']+=$j['z'];
    }
   }else $aKS=$aKt;
   foreach($aKS as $k=>$j){ //Kategoriesummen ausgeben
   $sMTx.="\n".FRA_TxKategorie.' '.str_replace('#','->',$k).': ';
    if(FRA_AdmKatErgebnis) $sMTx.="\n ".FRA_TxRichtige.': '.$j['r'].' ('.round(100*$j['r']/max($j['z'],1)).'%)';
    if(FRA_AdmKatFehlErgb) $sMTx.="\n ".FRA_TxFalsche.': '.$j['f'].' ('.round(100*$j['f']/max($j['z'],1)).'%)';
    if(FRA_AdmKatPunkte) $sMTx.="\n ".FRA_TxPunkte.': '.rund($j['p']).' '.FRA_TxVon.' '.$j['s'].' ('.round(100*$j['p']/max($j['s'],1)).'%)';
  }}
  if(FRA_AdmRichtigeU) $sMTx.="\n".FRA_TxRichtige.': '.$nR.' ('.round(100*$nR/max($nFragen,1)).'%)';
  if(FRA_AdmFalscheU) $sMTx.="\n".FRA_TxFalsche.': '.$nF.' ('.round(100*$nF/max($nFragen,1)).'%)';
  if(FRA_AdmPunkteU) $sMTx.="\n".FRA_TxPunkte.': '.rund($nP).' '.FRA_TxVon.' '.$nS.' ('.round(100*$nP/max($nS,1)).'%)';
  if(FRA_AdmVerbalU){
   if(FRA_VerbalPunkte) $p=round(100*$nP/max($nS,1)); else $p=round(100*$nR/max($nFragen,1));
   $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,FRA_VerbalTx0)))));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',$nFragen,str_replace('#P',rund($nP),str_replace('#G',$nS,constant('FRA_VerbalTx'.$i))))));
   $sMTx.="\n".FRA_TxErgebnis.': '.$s;
  }
  if(FRA_AdmVersucheU) $sMTx.="\n".FRA_TxVersuche.': '.$nA;
  if(FRA_AdmAuslassenU) $sMTx.="\n".FRA_TxAuslassen.': '.$nV;
 }
 if(FRA_AdmAntwort) $sMTx.="\n".FRA_TxAntwSq.': '.$sAntwort;
 if(FRA_AdmVerlauf) $sMTx.="\n".FRA_TxLaufSq.': '.$sVerlauf;
 if(FRA_AdmPersonU) $sMTx.="\n\n".$sPerson;
 if(strlen($sMTx)>0){
  if(FRA_AdmVorspann){
   $sVorText=str_replace('#T',$sTestName,str_replace('#D',date('d.m.Y, H:i').' Uhr',FRA_AdmVorspann)); $n=count($aNF); $s='#'.$sVorText;
   for($i=0;$i<$n;$i++) if(strpos($s,'{'.$aNF[$i].'}')) $sVorText=str_replace('{'.$aNF[$i].'}',$aUsr[$i],$sVorText);
   $sMTx=str_replace('\n ',"\n",$sVorText)."\n".$sMTx;
  }
  if(FRA_AdmAbspann) $sMTx.="\n\n".str_replace('\n ',"\n",FRA_AdmAbspann);
  require_once(FRA_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(FRA_Empfaenger); if($sEml) $Mailer->SetReplyTo($sEml);
  if(FRA_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=FRA_SmtpHost; $Mailer->SmtpPort=FRA_SmtpPort; $Mailer->SmtpAuth=FRA_SmtpAuth; $Mailer->SmtpUser=FRA_SmtpUser; $Mailer->SmtpPass=FRA_SmtpPass;}
  $s=FRA_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->SetFrom($s,$t); if(strlen(FRA_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(FRA_EnvelopeSender);
  $Mailer->Subject=FRA_AdmBetreff; $Mailer->Text=$sMTx; $Mailer->Send();
 }

 if(FRA_NutzerTests&&$aU&&$sNr>0){ //TestZuordnungen
  if(!FRA_SQL){ //Textdateien
   $aZ=@file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nZhl=count($aZ); $s=(int)$sNr.';'; $l=strlen($s);
   for($j=1;$j<$nZhl;$j++) if(substr($aZ[$j],0,$l)==$s){ //Nutzerzuordnung gefunden
    $sZw='#;'.rtrim(substr($aZ[$j],$l)).';'; $nZz=$j;
    break;
  }}elseif($bSQLOpen){ //bei SQL
   if(FRA_NutzerTests) if($rR=$DbO->query('SELECT Nummer,Tests FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$sNr.'"')){
    if($aZ=$rR->fetch_row()) $sZw='#;'.$aZ[1].';'; $rR->close(); //Nutzerzuordnung gefunden
  }}
  if(isset($sZw)&&($p=strpos($sZw,$sTestName))){ //TestZuordnungen herunterzaehlen
   $p=$p+strlen($sTestName)+1; $w=substr($sZw,$p); $w=substr($w,0,strpos($w,';'));
   if($q=strpos($w,'x')){
    $l=strlen($w);
    $n=sprintf('%0d',trim(substr($w,max(0,(int)strrpos($w,' ')))));
    $t=sprintf('%0d',max(0,(int)$n-1));
    $w=substr_replace($w,$t,$q-strlen($n),strlen($n));
    $sZw=substr_replace($sZw,$w,$p,$l); $sZw=substr($sZw,2,-1);
    if(!FRA_SQL){ //Textdateien
     $aZ[$nZz]=(int)$sNr.';'.$sZw."\n";
     if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Zuweisung,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aZ)))."\n"); fclose($f);
     }
    }elseif($bSQLOpen){ //bei SQL
     $DbO->query('UPDATE IGNORE '.FRA_SqlTabZ.' SET Tests="'.$sZw.'" WHERE Nummer="'.$sNr.'"');
 }}}}

 if(FRA_BewertungsSeite){ //Formularausgabe
 $X.="\n".' <form name="fraForm" class="fraForm" action="'.FRA_Self.'" method="post">
  <input type="hidden" name="fra_Aktion" value="'.(FRA_LoesungsSeite&&($nR<$nFragen||!FRA_LoesungsFalsche)?'zeige':'ende').'" />
  <input type="hidden" name="fra_Session" value="'.$sSes.'" />
  <input type="hidden" name="fra_Antwort" value="'.str_replace('|',';',$sAntwort).'" />
  <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
  <input type="hidden" name="fra_Anzahl" value="'.$nFragen.'" />'.rtrim("\n  ".FRA_Hidden).'
  <p><input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxWeiter).'" /></p>
 </form>'."\n";

 }else{//Abschlusseite
  if(FRA_LoesungsSeite){include(FRA_Pfad.'fraZeigen.php'); $Meld=''; $X=fFraZeige($nFragen);}
  else{include(FRA_Pfad.'fraFertig.php'); $Meld=''; $X=fFraFertig($nFragen);}
 }
 if(defined('FRA_KeinLogin')) $Meld.="\n".'<p class="fraMeld">'.fFraTx(FRA_TxLoginNicht).'</p>';
 elseif(defined('FRA_KeinRegister')) $Meld.="\n".'<p class="fraMeld">'.fFraTx(FRA_TxRegistNicht).'</p>';
 return $Meld.$X;
}

function fFraAusreichend($DbO){
 $sAntwort='#;'.FRA_FertigAntwort; $sVerlauf=FRA_FertigVerlauf; $nFragen=(strlen($sAntwort)>2?substr_count($sAntwort,';'):0);
 $aL=array(); $aP=array(); $aZ=array(); $aTP=array(0); //Datenbasis vorbereiten
 if(!FRA_SQL){
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',$aD[$i]); $n=$a[0];
   if(strpos($sAntwort,';'.$n.':')>0){
    $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
    for($j=8;$j<17;$j++){
     $t=trim($a[$j]);
     if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
     if(!empty($t)) $nA++; else break;
    }
    $aZ[$n]=$nA;
 }}}elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
   while($a=$rR->fetch_row()){ $n=$a[0];
    if(strpos($sAntwort,';'.$n.':')>0){
     $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
     for($j=8;$j<17;$j++){
      $t=trim($a[$j]);
      if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
      if(!empty($t)) $nA++; else break;
     }
     $aZ[$n]=$nA;
   }}
   $rR->close();
 }}
 $nR=0; $nF=0; $nP=0; $nS=0; //Richtig,Falsch,Punkte,punktSumme
 $sAntwort=str_replace(';','|',substr($sAntwort,2)); $aA=explode('|',$sAntwort); //Antwortkette untersuchen
 if($nFragen>0) foreach($aA as $t){
  $k=strpos($t,':'); $i=substr($t,0,$k); $t=substr($t,++$k); $a=array('r'=>0,'f'=>0,'p'=>0);
  if($t==$aL[$i]){$a['r']=1; $nR++; if(FRA_PositivWertung) $a['p']=$aP[$i];} //komplett richtig
  elseif($t=='-'){$a['f']=1; $nF++; if(!FRA_PositivWertung) $a['p']=$aP[$i];} //Zeitlimit
  else{ //nicht richtig
   $a['f']=1; $nF++; $nW=0;
   if(FRA_TeilWertung>0){ //Teilantworten untersuchen
    $sA='*,'.$t; $sL='*,'.$aL[$i]; $nZ=$aZ[$i];
    for($j=1;$j<=$nZ;$j++){
     if(FRA_TeilWertung==1){if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nW++;} //nur Richtige
     else{if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||strpos($sL,','.$j)<=0&&strpos($sA,','.$j)<=0) $nW++;} //auch Falsche
    }
    switch(FRA_TeilWertung){
     case 1: //nur Richtige
      if(FRA_PunkteTeilen) $a['p']=$aP[$i]*$nW/substr_count($sL,',');
      else{for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $a['p']+=$aTP[$i][$j];}
      break;
     case 2: //jede Entscheidung anteilig
      if(FRA_PunkteTeilen){if(substr_count($sA,',')<$nZ) $a['p']=$aP[$i]*$nW/$nZ;}
      else{
       $nMinP=$aP[$i]; $nFak=0;
       for($j=1;$j<=$nZ;$j++) if($aTP[$i][$j]>0) $nMinP=min($nMinP,$aTP[$i][$j]);
       for($j=1;$j<=$nZ;$j++){if($aTP[$i][$j]==0) $aTP[$i][$j]=$nMinP; $nFak+=$aTP[$i][$j];} $nFak=$aP[$i]/max($nFak,1);
       for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $a['p']+=$nFak*$aTP[$i][$j];
      }
      break;
     case 3: if(substr_count($sA,',')<$nZ){$nW=$nW/$nZ; if($nW>0.99) $a['p']=$aP[$i]; elseif($nW>=0.5) $a['p']=0.5*$aP[$i];} break; //50%-Regel
     case 4: if(substr_count($sA,',')<$nZ){$nW=$nZ-$nW; if($nW==0) $a['p']=$aP[$i]; elseif($nW==1) $a['p']=0.5*$aP[$i];} break; //0-1-2-Fehler -> 100%-50%-0%
    }
    if(FRA_TeilWertung>1) if(substr_count($sA,',')>=$nZ) $a['p']=0; //abstrafen
    if(!FRA_PositivWertung) $a['p']=$aP[$i]-$a['p'];
   }elseif(!FRA_PositivWertung) $a['p']=$aP[$i]; //komplett Fehlerpunkte
  }
  $nP+=$a['p']; $nS+=$aP[$i];
 }
 $r='0'; $l='0';
 switch(FRA_LoginWenn){
  case 'PProz': if(100*$nP/max($nS,1)>=FRA_LoginGrenze) $l='1'; break;
  case 'NProz': if(100*$nP/max($nS,1)<=FRA_LoginGrenze) $l='1'; break;
  case 'PPkte': if($nP>=FRA_LoginGrenze) $l='1'; break;
  case 'NPkte': if($nP<=FRA_LoginGrenze) $l='1'; break;
  case 'PRtig': if($nR>=FRA_LoginGrenze) $l='1'; break;
  case 'PFehl': if($nF<=FRA_LoginGrenze) $l='1'; break;
 }
 switch(FRA_RegistWenn){
  case 'PProz': if(100*$nP/max($nS,1)>=FRA_RegistGrenze) $r='1'; break;
  case 'NProz': if(100*$nP/max($nS,1)<=FRA_RegistGrenze) $r='1'; break;
  case 'PPkte': if($nP>=FRA_RegistGrenze) $r='1'; break;
  case 'NPkte': if($nP<=FRA_RegistGrenze) $r='1'; break;
  case 'PRtig': if($nR>=FRA_RegistGrenze) $r='1'; break;
  case 'PFehl': if($nF<=FRA_RegistGrenze) $r='1'; break;
 }
 return $l.$r;
}

function fFraDeCodeB($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
?>