<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){return fFraFrage(true);}
}

function fFraFrage($bDirekt){ //Seiteninhalt
 $X=''; $Meld=''; $MTyp='Fehl'; $sAktion='frage'; $bSessOK=true;

 $bSQLOpen=false; $DbO=NULL; $aD=NULL; //Datenbasis vorbereiten
 if(!FRA_SQL){$aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);}
 else{ //SQL
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
 }

 $sFolge=FRA_Folge; $sAntwort=FRA_Antwort; $sVerlauf=FRA_Verlauf; $sZeit=FRA_Zeit; $sNutzer=''; $bVonForm=true; $bAufdecken=false; $nAuslassen=0; $bOk=true;
 if(strlen($sFolge)>0){
  $nFragen=substr_count($sFolge.(strlen($sAntwort)>0?';'.$sAntwort:''),';')+1;
  $nFraProSeite=(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:FRA_ProSeite);
  $sNutzer=(isset($_POST['fra_Nutzer'])?$_POST['fra_Nutzer']:'???');
 }else{ //erstmaliger Aufruf, Folge generieren, Zeit starten
  include FRA_Pfad.'fraAuswahl.php'; $bVonForm=false; $MTyp='Meld';
  $nFraProSeite=((isset($sProSeite)&&strlen($sProSeite)>0)?$sProSeite:FRA_ProSeite);
  if(FRA_ZeigeNamen&&strlen(($bDirekt?FRA_Session:FRA_NeuSession))>0){ //Nutzernamen holen
   if(substr(($bDirekt?FRA_Session:FRA_NeuSession),4,1)!='9'){ //Benutzer
    if(!FRA_SQL){ //Textdateien
     $a=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nN=count($a); $s=((int)substr(($bDirekt?FRA_Session:FRA_NeuSession),4,5)).';'; $n=strlen($s);
     for($i=1;$i<$nN;$i++) if(substr($a[$i],0,$n)==$s){$a=explode(';',$a[$i]); $sNutzer=fFraDeCode($a[2]); break;}
    }elseif($bSQLOpen){ //bei SQL
     if($rR=$DbO->query('SELECT Nummer,Benutzer FROM '.FRA_SqlTabN.' WHERE Nummer="'.((int)substr(($bDirekt?FRA_Session:FRA_NeuSession),4,5)).'"')){
      $a=$rR->fetch_row(); $rR->close(); if($a[0]==(int)substr(($bDirekt?FRA_Session:FRA_NeuSession),4,5)) $sNutzer=$a[1];
    }}
   }else{ //Teilnehmer
    $a=@file(FRA_Pfad.'temp/'.substr(($bDirekt?FRA_Session:FRA_NeuSession),0,9).'.ses'); if(is_array($a)) $a=explode(';',rtrim($a[0]));
    $sNutzer=(isset($a[FRA_TeilnehmerKennfeld-1])&&$a[FRA_TeilnehmerKennfeld-1]?$a[FRA_TeilnehmerKennfeld-1]:'????');
  }}
  if(!FRA_LernModus) $Meld=str_replace('#Z',$nFragen,FRA_TxBeginn);
  else{
   $Meld=str_replace('#Z',($nFraProSeite>0?min($nFraProSeite,$nFragen):$nFragen),str_replace('#N','1',FRA_TxLernen));
   if($nFragen<=$nFraProSeite||$nFraProSeite==0) $sAktion='ende" />'."\n".'  <input type="hidden" name="fra_Anzahl" value="'.$nFragen;
  }

 }

 if($bSessOK){ // Session in der fraAuswahl noch nicht abgelaufen
 if(!defined('FRA_AktivCodeErr')){ //keine Aktivcodesperre
 //-------
 $nZeitLimit=(FRA_TestZeit>0?FRA_TestZeit:FRA_ZeitLimit);
 if(isset($_POST['fra_Aufgedeckt'])&&$_POST['fra_Aufgedeckt']>'0'){$bAufdecken=true; $bVonForm=false; --$nFragen;} //Loesung war aufgedeckt
 $nProSeite=($nFraProSeite>0?$nFraProSeite:$nFragen);
 if(!FRA_LernModus){ //normaler Antwortaufruf
  $sMeldHandle=''; $sMeldNochMal=''; $sMeldHalb=''; $sMeldStimmt=''; $sMeldAnders='';
  if(isset($_POST['fra_Zurueck'])&&(strlen($_POST['fra_Zurueck'])>'1')){ //Zurueck blaettern
   $bVonForm=false; $nZurck=$nProSeite;
   while(($nZurck--)>0){
    if($p=strrpos($sAntwort,';')){$nF=(int)substr($sAntwort,$p+1); $sAntwort=substr($sAntwort,0,$p);}else{$nF=(int)$sAntwort; $sAntwort='';}
    if($nF) $sFolge=$nF.';'.$sFolge;
  }}
  if(isset($_POST['fra_FrageNr'])&&($nSuchNr=(int)$_POST['fra_FrageNr'])){ //Gehezu
   $bVonForm=false; $sSuchFolge='#;'.$sFolge.';';
   if($p=strpos($sSuchFolge,';'.$nSuchNr.';')) $sFolge=substr(substr($sSuchFolge,$p+1),0,-1);else $Meld=str_replace('#N',$nSuchNr,FRA_TxNichtGefunden);
  }

  if($bVonForm){ //Auswertung
   $nProSeite=(isset($_POST['fra_Anzahl'])?(int)$_POST['fra_Anzahl']:0); $sAltFolge=$sFolge; $sAltAntwort=$sAntwort;
   for($i=0;$i<$nProSeite;$i++) if($sNr=sprintf('%d',(int)$sFolge)){ //pro Frage
    $nLfdNr=$nFragen-substr_count($sFolge,';'); $sFolge=substr($sFolge,strlen($sNr)+1); //$bAufdecken=false;
    $aFChk[$i]=array(0,false,false,false,false,false,false,false,false,false); $k=$i-$nAuslassen;
    $aF=fFraHoleFrage($sNr,$aD,$bSQLOpen,$DbO); $sLsg=$aF[5]; $aA=NULL; //Frage holen
    if(isset($_POST['fra_Antw'.$i])) $aA=$_POST['fra_Antw'.$i]; //Antwortarray holen
    if(isset($_POST['fra_Auslassen'.$i])&&$_POST['fra_Auslassen'.$i]>'0'){ //Frage ausgelassen
     $sFolge.=(strlen($sFolge)>0?';':'').$sNr; $sAltFolge='#;'.$sAltFolge.';'.$sNr; $sVerlauf.=($sVerlauf?';':'').$sNr.'-'; $nAuslassen++;
     if($p=strpos($sAltFolge,';'.$sNr.';')) $sAltFolge=substr_replace($sAltFolge,'',++$p,strlen($sNr)+1); $sAltFolge=substr($sAltFolge,2);
    }elseif(!is_array($aA)||count($aA)==0){ //Antwort leer
     $sVerlauf.=($sVerlauf?';':'').$sNr; $aFehlHandle[$k]=true; $bOk=false; $sMeldHandle.=', '.$nLfdNr;
    }else{ //Antwort vorhanden
     $bFOk=true; sort($aA); $sAw=''; for($j=0;$j<count($aA);$j++){$sAw.=((int)$aA[$j]).','; $aFChk[$k][$aA[$j]]=true;} $sAw=substr($sAw,0,-1);
     $sVerlauf.=($sVerlauf?';':'').$sNr.':'.$sAw; $nVersuch=substr_count(';'.$sVerlauf,';'.$sNr.':');
     if(FRA_PruefeAntw) if($sLsg!=$sAw) if(FRA_AntwortVersuche==0||$nVersuch<FRA_AntwortVersuche){
      $aFehlPruefe[$k]=true; $bFOk=false; $bOk=false; $sMeldNochMal.=', '.$nLfdNr; //Antwort falsch, naechster Versuch
     }
     if(FRA_PruefeAnzahl) if(strlen($sLsg)!=strlen($sAw)) if(FRA_AntwortVersuche==0||$nVersuch<FRA_AntwortVersuche){
      $aFehlPruefe[$k]=true; $bFOk=false; $bOk=false; $sMeldHalb.=', '.$nLfdNr; //Antwortanzahl falsch, naechster Versuch
     }
     if($bFOk){ //Frage ist abgearbeitet
      $sAntwort.=($sAntwort?';':'').$sNr.':'.$sAw;
      if($nZeitLimit<=0||(time()-$sZeit<=$nZeitLimit)||empty($sFolge)){ //nicht abgelaufen
       if(FRA_Offenlegen&&($sLsg!=$sAw||!FRA_OffenNurFalsche)){ //alte Frage soll aufdeckt werden
        $bAufdecken=true; $bOk=false;
        if($sLsg==$sAw){$aFehl[$k]=false; $sMeldStimmt.=', '.$nLfdNr;}else{$aFehl[$k]=true; $sMeldAnders.=', '.$nLfdNr;}
      }}else{ //abgelaufen
       $aRst=explode(';',$sFolge); $nRst=count($aRst); $sFolge='T';
       for($j=0;$j<$nRst;$j++) $sAntwort.=';'.$aRst[$j].':-'; //$sVerlauf.=';'.$aRst[$j].'-';
     }}
    } //Antwort
   } //pro Frage
   if($bOk){
    for($i=0;$i<$nProSeite;$i++) $aFChk[$i]=array(0,false,false,false,false,false,false,false,false,false);
   }else{
    if($sMeldHandle){$aFehl=$aFehlHandle; $bAufdecken=false; $Meld=str_replace('#Z',$nProSeite,str_replace('#N',substr($sMeldHandle,2),FRA_TxHandle));}
    elseif($sMeldNochMal){$aFehl=$aFehlPruefe; $bAufdecken=false; $Meld=str_replace('#Z',$nProSeite,str_replace('#N',substr($sMeldNochMal,2),FRA_TxNochMal));}
    elseif($sMeldHalb){$aFehl=$aFehlPruefe; $bAufdecken=false; $Meld=str_replace('#Z',$nProSeite,str_replace('#N',substr($sMeldHalb,2),FRA_TxHalb));}
    elseif($sMeldAnders) $Meld=str_replace('#Z',$nProSeite,str_replace('#N',substr($sMeldAnders,2),FRA_TxAnders));
    elseif($sMeldStimmt){$Meld=str_replace('#Z',$nProSeite,str_replace('#N',substr($sMeldStimmt,2),FRA_TxStimmt)); $MTyp='Erfo';}
    $sFolge=$sAltFolge; if(!$bAufdecken) $sAntwort=$sAltAntwort; $nProSeite-=$nAuslassen;
   }
  }elseif($bAufdecken){ //Aufgedeckt
   $nProSeite=(isset($_POST['fra_Anzahl'])?(int)$_POST['fra_Anzahl']:($nFraProSeite>0?$nFraProSeite:$nFragen));
   for($i=0;$i<$nProSeite;$i++) if($sNr=sprintf('%d',(int)$sFolge)) $sFolge=substr($sFolge,strlen($sNr)+1); $bAufdecken=false;
   $nProSeite=($nFraProSeite>0?$nFraProSeite:$nFragen);
  }
 }elseif($bVonForm){//Lernmodusantwort
  for($nBlock=0;$nBlock<$nProSeite;$nBlock++) if($sNr=sprintf('%d',(int)$sFolge)){ //pro Frage
   $sVerlauf.=($sVerlauf?';':'').$sNr; $sAntwort.=($sAntwort?';#':'#'); $sFolge=substr($sFolge,strlen($sNr)+1);
  }
  $nLfdNr=substr_count($sAntwort,';')+2; $nRest=substr_count($sFolge,';')+1;
  if($nRest<$nProSeite){$nProSeite=$nRest; $sAktion='ende" />'."\n".'  <input type="hidden" name="fra_Anzahl" value="'.$nFragen;}
  $Meld=str_replace('#Z',$nProSeite,str_replace('#N',$nLfdNr,FRA_TxLernen)); $MTyp='Meld';
 }

 //Fragedarstellung
 $aF=array(); $bNochFragen=false; $bBewertet=false;
 if($sNr=sprintf('%d',(int)$sFolge)){ //noch mindestens eine Frage
  $aF=fFraHoleFrage($sNr,$aD,$bSQLOpen,$DbO);
  if(isset($aF[4])&&!empty($aF[4])){$bNochFragen=true; $nProSeite=min($nProSeite,substr_count($sFolge,';')+1);}
  elseif(!$Meld) $Meld=str_replace('#N',$sNr,(isset($aF['Fehl'])?$aF['Fehl']:FRA_TxNichtGefunden));
 }elseif(!$Meld) if($sFolge!='T') $Meld=FRA_TxKeineNummer; else{define('FRA_UeberZeitLimit',true); $Meld=str_replace('#Z',$nFragen,FRA_TxZeitLimit);}

 if($bNochFragen){ //mindestens noch eine Frage vorhanden
  $sRestZeit=''; $sZwischenWert=''; $nSofortKlick=0;
  for($nBlock=0;$nBlock<$nProSeite;$nBlock++){ //pro Frage
   if($nBlock>0){
    $bNochFragen=false; $aF=array(); $sNr=''; $p=0;
    if(substr_count($sFolge,';')>=$nBlock){
     for($i=0;$i<$nBlock;$i++) $p=strpos($sFolge,';',++$p);
     if($p>0) if($sNr=sprintf('%d',substr($sFolge,++$p))) $aF=fFraHoleFrage($sNr,$aD,$bSQLOpen,$DbO);
     if(isset($aF[4])&&!empty($aF[4])) $bNochFragen=true;
    }
   }
   if($bNochFragen){
    $nLfdNr=$nFragen+$nBlock-substr_count($sFolge,';');
    if(!$Meld){$Meld=str_replace('#Z',$nProSeite,str_replace('#N',$nLfdNr,FRA_TxNormal)); $MTyp='Meld';}
    if($bAufdecken||FRA_LernModus){ //Loesung anzeigen
     $aFChk[$nBlock]=array(0,false,false,false,false,false,false,false,false,false); $s=$aF[5];
     while($i=strpos($s,',')){$aFChk[$nBlock][(int)substr($s,0,$i)]=true; $s=substr($s,++$i);} $aFChk[$nBlock][(int)$s]=true;
    }

    $Y="\n".'<div class="fra'.(isset($aFehl[$nBlock])?($aFehl[$nBlock]?'TxtF':'TxtR'):'Text').'">'; //TextBlock Anfang
    if(FRA_ZeigeNummer) $sZlN=' <div class="fraFrNr">'.(FRA_NummernTyp!=2?fFraTx(FRA_TxFrage).' '.sprintf('%'.FRA_NummerStellen.'d/%'.FRA_NummerStellen.'d',$nLfdNr,$nFragen):'').(FRA_NummernTyp>2?', &nbsp; ':'').(FRA_NummernTyp>1?fFraTx(FRA_TxFrage.'-'.FRA_TxNr).' '.$sNr:'').'</div>';
    if(FRA_ZeigeNummer=='oben') $Y.="\n".$sZlN;
    if(FRA_ZeigeNamen=='oben'){$Y.="\n".' <div class="fraInfo">'.fFraTx(FRA_TxBenutzer.': '.$sNutzer).'</div>';}
    if(FRA_ZeigeKategorie=='oben'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
    $Y.="\n".' <div class="fraFrag">'.fFraBB(fFraTx(trim(FRA_TxVorFrage.' '.$aF[4]))).'</div>';
    if(FRA_ZeigeBemerkung=='oben'&&($t=rtrim($aF[17]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
    if(FRA_ZeigeKategorie=='unten'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
    $i=0; $aAw=array(); $aNr=array(); $s='checkbox'; $nSofortKlick=0;
    if(strpos($aF[5],',')==false){if(FRA_RadioButton) $s='radio'; if($nProSeite<=1) $nSofortKlick=FRA_SofortBeiKlick;}
    while($t=$aF[++$i+7]) if($i<10){//Antwortenschleife
     if($p=strpos($t,'|#')) $t=substr($t,0,$p);
     if($nSofortKlick!=2) $aAw[]=' <div class="fraAntw" onclick="toggleInp('.$nBlock.','.$i.','.($nSofortKlick==1?'true':'false').');"><input class="fraAntw" type="'.$s.'" onclick="clickInp('.($nSofortKlick==1?'true':'false').')" id="fraAntw'.$nBlock.'_'.$i.'" name="fra_Antw'.$nBlock.'[]" value="'.$i.((isset($aFChk[$nBlock])&&$aFChk[$nBlock][$i])?'" checked="checked':'').'" />&nbsp;'.fFraBB(fFraTx($t)).'</div>';
     else $aAw[]=' <div class="fraAntw"><input class="fraASch" type="submit" name="fra_Antw'.$nBlock.'[]" value="'.$i.' '.fFraBTx($t).'" /></div>';
     $aNr[]=$i;
    }
    $nAw=count($aAw); $sAwFolge=''; $sAwOrgF=(isset($_POST['fra_AwFolge'.$nBlock])?$_POST['fra_AwFolge'.$nBlock]:'');
    if(FRA_ZufallsAntwort) while($nAw>0){
     if(((!$bOk&&isset($aFehl[$nBlock])&&$aFehl[$nBlock])||$bAufdecken)&&($n=strlen($sAwOrgF))){$i=substr($sAwOrgF,$n-($nAw--),1); $Y.="\n".$aAw[$i-1]; $sAwFolge=$sAwOrgF;} //vorherige Antwortreihenfolge
     else{$i=rand(0,--$nAw); $Y.="\n".$aAw[$i]; array_splice($aAw,$i,1); $sAwFolge.=$aNr[$i]; array_splice($aNr,$i,1);} //zufaellige Antwortreihenfolge
    }
    else for($i=0;$i<$nAw;$i++) $Y.="\n".$aAw[$i]; //natuerliche Antwortreihenfolge
    $Y.='<input type="hidden" name="fra_AwFolge'.$nBlock.'" value="'.$sAwFolge.'" />';
    if(FRA_Auslassen&&!(FRA_LernModus||$bAufdecken)) $Y.="\n".' <div class="fraAusl" onclick="toggleAusl('.$nBlock.','.($nSofortKlick>=1?'true':'false').')"><input class="fraAntw" type="checkbox" id="fraAusl'.$nBlock.'" name="fra_Auslassen'.$nBlock.'" onclick="clickInp('.($nSofortKlick==1?'true':'false').')" value="1" />&nbsp;'.fFraBB(str_replace('#N',$nLfdNr,fFraTx(FRA_TxAnsEnde))).'</div>';
    if((FRA_ZeigeBemerkung=='unten'||(FRA_HilfeBemerkung&&$sMeldNochMal>'')||(FRA_LernModus&&(FRA_LernBemerkung==1||FRA_LernBemerkung==2)))&&($t=rtrim($aF[17]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
    if(FRA_LernModus&&FRA_LernBemerkung==2&&($t=rtrim($aF[18]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
    if(FRA_ZeigeAntwZahl||FRA_ZeigePunkte||FRA_ZeigeVersuche){$t='';
     if(FRA_ZeigeKategorie=='info') if($aF[3]) $t=fFraTx(trim(FRA_TxKategorie.' '.$aF[3])).', ';
     if(FRA_ZeigeAntwZahl) $t.=(substr_count($aF[5],',')+1).' '.fFraTx(FRA_TxRichtige).', ';
     if(FRA_ZeigePunkte) $t.=$aF[6].' '.fFraTx(FRA_TxPunkte).', ';
     if(FRA_ZeigeVersuche) $t.=(FRA_AntwortVersuche?FRA_AntwortVersuche:'x').' '.fFraTx(FRA_TxVersuche).', ';
     $Y.="\n".' <div class="fraInfo">('.substr($t,0,-2).')</div>';
    }
    if(FRA_ZeigeNamen=='unten'){$Y.="\n".' <div class="fraInfo">'.fFraTx(FRA_TxBenutzer.': '.$sNutzer).'</div>';}
    $aP=array(0); $aTP=array(0); $aL=array(0); $aZ=array(0);
    if(FRA_RestZeit&&$nZeitLimit){
     $i=max($nZeitLimit+$sZeit-time(),0); $nH=floor($i/3600); $nM=floor($i/60)%60; $nS=$i%60;
     $sRestZeit=', '.fFraTx(FRA_TxRestZeit).'&nbsp;'.($nH<=0?sprintf('%02d:%02d',$nM,$nS):sprintf('%02d:%02d:%02d',$nH,$nM,$nS));
    }
    $bZwischenwertung=(FRA_TxZwischenwertung&&strlen($sAntwort)>0&&!FRA_LernModus&&!$bBewertet);
    if($bZwischenwertung){ //Zwischenwertung
     $sKette='#;'.$sAntwort; $bBewertet=true;
     if(!FRA_SQL){
      for($i=1;$i<$nSaetze;$i++){
       $s=$aD[$i]; $n=(int)substr($s,0,strpos($s,';'));
       if(strpos($sKette,';'.$n.':')>0){
        $a=explode(';',$s); $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
        for($j=8;$j<17;$j++){
         $t=trim($a[$j]);
         if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
         if(!empty($t)) $nA++; else break;
        }
        $aZ[$n]=$nA;
     }}}elseif($bSQLOpen){
      if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
       while($a=$rR->fetch_row()){
        $n=(int)$a[0];
        if(strpos($sKette,';'.$n.':')>0){
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
     $aA=explode(';',$sAntwort); $nR=0; $nF=0; $nP=0; $nS=0; //Antwortkette untersuchen
     if($nFragen>0) foreach($aA as $t){
      $k=strpos($t,':'); $i=substr($t,0,$k); $t=substr($t,++$k); $nS+=$aP[$i]; $nTP=0;
      if($t==$aL[$i]){$nR++; if(FRA_PositivWertung) $nP+=$aP[$i];} //richtig
      else{//nicht richtig
       $nF++; $nW=0;
       if(FRA_TeilWertung>0){ //Teilantworten untersuchen
        $sA='*,'.$t; $sL='*,'.$aL[$i]; $nZ=$aZ[$i];
        for($j=1;$j<=$nZ;$j++){
         if(FRA_TeilWertung==1){if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nW++;} //nur Richtige
         else{if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $nW++;} //auch Falsche
        }
        switch(FRA_TeilWertung){
         case 1: //nur Richtige bewerten
          if(FRA_PunkteTeilen) $nTP=$aP[$i]*$nW/substr_count($sL,',');
          else{for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nTP+=$aTP[$i][$j];}
          break;
         case 2: //jede Entscheidung anteilig
          if(FRA_PunkteTeilen){if(substr_count($sA,',')<$nZ) $nTP=$aP[$i]*$nW/$nZ;}
          else{
           $nMinP=$aP[$i]; $nFak=0;
           for($j=1;$j<=$nZ;$j++) if($aTP[$i][$j]>0) $nMinP=min($nMinP,$aTP[$i][$j]);
           for($j=1;$j<=$nZ;$j++){if($aTP[$i][$j]==0) $aTP[$i][$j]=$nMinP; $nFak+=$aTP[$i][$j];} $nFak=$aP[$i]/max($nFak,1);
           for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $nTP+=$nFak*$aTP[$i][$j];
          }
          break;
         case 3: if(substr_count($sA,',')<$nZ){$nW=$nW/$nZ; if($nW>0.99) $nTP=$aP[$i]; elseif($nW>=0.5) $nTP=0.5*$aP[$i];} break; //50%-Regel
         case 4: if(substr_count($sA,',')<$nZ){$nW=$nZ-$nW; if($nW==0) $nTP=$aP[$i]; elseif($nW==1) $nTP=0.5*$aP[$i];} break; //0-1-2-Fehler -> 100%-50%-0%
        }
        if(FRA_TeilWertung>1) if(substr_count($sA,',')>=$nZ) $nTP=0; //abstrafen
        if(FRA_PositivWertung) $nP+=$nTP; else $nP+=$aP[$i]-$nTP;
       }elseif(!FRA_PositivWertung) $nP+=$aP[$i]; //Fehlerpunkte
     }}
     $sZwischenWert= ', '.fFraTx(str_replace('#R',$nR,str_replace('#F',$nF,str_replace('#A',($bAufdecken||FRA_LernModus?$nLfdNr:$nLfdNr-1),str_replace('#P', str_replace('.',FRA_Dezimalzeichen,round($nP,1)),str_replace('#G',$nS,FRA_TxZwischenwertung))))));
    }
    if(strlen($sRestZeit)>2||strlen($sZwischenWert)>2) $Y.="\n".' <div class="fraFrNr" style="float:right">'.substr($sZwischenWert.$sRestZeit,2).'</div>';
    if(FRA_ZeigeNummer=='unten') $Y.="\n".$sZlN;
    $Y.="\n</div>";//TextBlock Ende

    if(FRA_LayoutTyp>0){//BildLayout Anfang
     if(!$sBld=$aF[7]) if(FRA_BildErsatz) $sBld=FRA_BildErsatz; $aV=explode(' ',$sBld); $sBld=$aV[0]; $sExt=strtolower(substr($sBld,strrpos($sBld,'.')+1));
     if($sExt=='jpg'||$sExt=='png'||$sExt=='gif'||$sExt=='jpeg'){
      $sBld=FRA_Bilder.$sBld; $aI=@getimagesize(FRA_Pfad.$sBld);
      $sBld='<img src="'.FRA_Http.$sBld.'" '.(isset($a[3])?$a[3]:'').' border="0" alt="'.fFraTx(FRA_TxFrage).'-'.$sNr.'" title="'.fFraTx(FRA_TxFrage).'-'.$sNr.'" />';
     }elseif($sExt=='mp4'||$sExt=='ogg'||$sExt=='ogv'||$sExt=='webm'){
      $VidW=(isset($aV[1])?(int)$aV[1]:0); $VidH=(isset($aV[2])?(int)$aV[2]:0); if($sExt=='ogv') $sExt='ogg';
      $sBld='<video controls type="video/'.$sExt.'" src="'.$sBld.'" style="'.($VidW?'width:'.$VidW.'px;':'').($VidH?'height:'.$VidH.'px;':'').'max-width:100%" title="'.$sBld.'">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></video>';
     }elseif($sExt=='mp3'||$sExt=='ogg'){
      $sBld='<audio controls type="audio/'.$sExt.'" src="'.$sBld.'" style="max-width:100%" title="'.$sBld.'">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></audio>';
     }elseif(strpos($sBld,'youtube.com/')){
      $VidW=(isset($aV[1])?(int)$aV[1]:0); $VidH=(isset($aV[2])?(int)$aV[2]:0);
      $sBld='<iframe src="'.$sBld.'" style="'.($VidW?'width:'.$VidW.'px;':'').($VidH?'height:'.$VidH.'px;':'').'max-width:100%" frameborder="0" allowfullscreen="">Quelle: <a href="'.$sBld.'">'.$sBld.'</a></iframe>';
     }else $sBld='&nbsp;';
     $sBld="\n".'<div class="fraBild">'."\n ".$sBld."\n</div>";
     if(FRA_LayoutBildText) $Y=$sBld.$Y; else $Y.=$sBld; //Bild vor Text
     if(FRA_LayoutTyp>1) $Y.="\n".'<div class="fraClrB"></div>';
    }
    if(FRA_Offenlegen&&$bAufdecken||(FRA_LernModus&&(FRA_LernBemerkung==3||FRA_LernBemerkung==4))){$t=''; //Anmerkung
     if(FRA_ZeigeBemerkung=='aufdecken') $t=rtrim($aF[17]);
     if(FRA_ZeigeBemerkung=='selektiv'){if(isset($aFehl[$nBlock])&&$aFehl[$nBlock]) $t=rtrim($aF[18]); else $t=rtrim($aF[17]);}
     if(FRA_LernModus&&FRA_LernBemerkung>=3) $t=rtrim($aF[17]);
     if($t) $Y.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>";
     if(FRA_LernModus&&FRA_LernBemerkung==4)
      if($t=rtrim($aF[18])) $Y.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>";
    }
    $X.="\n\n".'<div class="fraBlock"><!-- Block_'.($nBlock+1).' -->'.$Y."\n</div><!-- /Block_".($nBlock+1).' -->';
   }//bNochFragen
  }//proFrage

  if($nSofortKlick<=0){
   $sBtn=fFraTx($bAufdecken||FRA_LernModus?FRA_TxNaechste:FRA_TxAntworte); //Formularseitenausgabe
   $sBtn='<div class="fraScha"><input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.$sBtn.'" /></div>';
  }else $sBtn='';
  $X='<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>'."\n".'<form name="fraForm" class="fraForm" action="'.FRA_Self.'" method="post">
  <input type="hidden" name="fra_Aktion" value="'.$sAktion.'" />
  <input type="hidden" name="fra_Session" value="'.($bDirekt?FRA_Session:FRA_NeuSession).'" />'.(FRA_TestFolgeName?"\n".'  <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />':'').(isset($sNeuFolgeName)?"\n".'  <input type="hidden" name="fra_Folgename" value="'.$sNeuFolgeName.'" />':'').(defined('FRA_TestSpontan')?"\n".'  <input type="hidden" name="fra_Spontantest" value="1" />':'').(FRA_TestKategorie?"\n".'  <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />':'').(FRA_TestZeit?"\n".'  <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />':'').'
  <input type="hidden" name="fra_Nutzer" value="'.$sNutzer.'" />
  <input type="hidden" name="fra_Folge" value="'.$sFolge.'" />
  <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
  <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
  <input type="hidden" name="fra_ProSeite" value="'.$nFraProSeite.'" />
  <input type="hidden" name="fra_Anzahl" value="'.$nBlock.'" />
  <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />
  <input type="hidden" name="fra_Aufgedeckt" value="'.($bAufdecken?'1':'').'" />'.rtrim("\n  ".FRA_Hidden).$X."\n\n".$sBtn."\n";

  $bGeheZu=strlen(FRA_SchalterTxGeheZu)>0; $bZuruck=strlen(FRA_SchalterTxZurueck)>0;
  if($bGeheZu||$bZuruck){
   $X.='<div class="fraScha">';
   if($bGeheZu) $X.='<span style="white-space:nowrap">'.fFraTx(FRA_SchalterTxGeheZu).' <input class="fraLogi" style="width:3em;margin-right:4px;" type="text" name="fra_FrageNr" /> <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_SchalterSuchen).'" /></span>';
   if($bGeheZu&&$bZuruck){if(FRA_Schalter2Zeilen) $X.='</div><div class="fraScha">'; else $X.=' &nbsp; ';}
   if($bZuruck) $X.='<span style="white-space:nowrap">'.fFraTx(FRA_SchalterTxZurueck).' <input name="fra_Zurueck" type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_SchalterZurueck).'" /></span>';
   $X.="</div>\n";
  }

  $X.="\n".'</form>'.fJSInpCode()."\n";

 }else{ //garkeine Frage mehr, fertig
  if(!isset($FehlSQL)){
   if(!FRA_LernModus){
    define('FRA_FertigAntwort',$sAntwort); define('FRA_FertigVerlauf',$sVerlauf); $Meld=''; $bRegistrierung=true; $bLogin=true;
    if(FRA_Nutzerverwaltung=='nachher'&&FRA_LoginWenn>''||FRA_Registrierung=='nachher'&&FRA_RegistWenn>''){
     include FRA_Pfad.'fraBewerten.php'; $s=fFraAusreichend($DbO);
     if(FRA_Nutzerverwaltung=='nachher'&&FRA_LoginWenn>'') if(!$bLogin=(substr($s,0,1)=='1')) define('FRA_KeinLogin',true);
     if(FRA_Registrierung=='nachher'&&FRA_RegistWenn>'') if(!$bRegistrierung=(substr($s,1,1)=='1')) define('FRA_KeinRegister',true);
    }
    if(strlen(FRA_Session)==0&&FRA_Nutzerverwaltung=='nachher'&&$bLogin){include FRA_Pfad.'fraLogin.php'; $X=fFraLogin(false);}
    elseif(strlen(FRA_Session)==0&&FRA_Registrierung=='nachher'&&$bRegistrierung){include FRA_Pfad.'fraErfassen.php'; $X=fFraErfassen(false);}
    else{include_once FRA_Pfad.'fraBewerten.php'; $X=fFraBewerten(true);}
   }else{include FRA_Pfad.'fraFertig.php'; $X=fFraFertig($nFragen);}
  }else $X=' <p class="fraFehl">'.fFraTx($FehlSQL)."</p>\n";
 }
 //-------
 }else{ //falscher AktivCode
  if(substr(($bDirekt?FRA_Session:FRA_NeuSession),4,1)!='9'){include FRA_Pfad.'fraZentrum.php'; $X=fFraZentrum(true);}
  if(substr(($bDirekt?FRA_Session:FRA_NeuSession),4,1)=='9'){include FRA_Pfad.'fraTestAuswahl.php'; $X=fFraTestAuswahl(true);}
 }
 }else $X='<p class="fraFehl">'.fFraTx(isset($MeldS)?$MeldS:FRA_TxSessionUngueltig).'</p>'; // Sessionfehler in fraAuswahl.php

 return $X;
}

function fJSInpCode(){
 return "
<script type=\"text/javascript\">
 var iChkClicked=false;
 function clickInp(bSubmit){iChkClicked=true; if(bSubmit) document.forms['fraForm'].submit();}
 function toggleInp(nBlk,nId,bSubmit){
  if(!iChkClicked){var iChk=document.getElementById('fraAntw'+nBlk+'_'+nId); iChk.checked=!iChk.checked;}
  iChkClicked=false;
  if(bSubmit) document.forms['fraForm'].submit();
 }
 function toggleAusl(nBlk,bSubmit){
  if(!iChkClicked){var iChk=document.getElementById('fraAusl'+nBlk); iChk.checked=!iChk.checked;}
  iChkClicked=false;
  if(bSubmit) document.forms['fraForm'].submit();
 }
</script>";
}

function fFraHoleFrage($Nr,$aD,$bSQL,$DbO){
 $aF=array();
 if(!$bSQL){
  $s=$Nr.';'; $n=strlen($s); $nSaetze=count($aD); $bOK=false;
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$n)==$s){$aF=explode(';',rtrim($aD[$i])); $bOK=true; break;}
  if($bOK) for($i=4;$i<19;$i++) $aF[$i]=(isset($aF[$i])?str_replace('`,',';',$aF[$i]):'');
  else $aF['Fehl']=FRA_TxNichtGefunden;
 }else{
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' WHERE Nummer="'.(int)$Nr.'"')){
   if($aF=$rR->fetch_row()) for($i=4;$i<19;$i++) $aF[$i]=(isset($aF[$i])?str_replace("\n",'\n ',str_replace("\r",'',$aF[$i])):'');
   else $aF['Fehl']=FRA_TxNichtGefunden;
   $rR->close();
  }else $aF['Fehl']=FRA_TxSqlFrage;
 }
 return $aF;
}
function fFraBTx($sTx){ //TextKodierung
 if(FRA_Zeichensatz<=0) $s=$sTx; elseif(FRA_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8//TRANSLIT',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n '," \n",$s);
}
?>