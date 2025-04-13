<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){
  $nFragen=(isset($_POST['fra_Anzahl'])?(int)$_POST['fra_Anzahl']:(isset($_GET['fra_Anzahl'])?(int)$_GET['fra_Anzahl']:0));
  return fFraZeige($nFragen);
 }
}

function fFraZeige($nFragen){
 $X=''; $Meld=''; $MTyp='Fehl';

 $bSQLOpen=false; $DbO=NULL; $aD=NULL; //Datenbasis vorbereiten
 if(!FRA_SQL){$aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);}
 else{ //SQL
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
 }

 $sFolge=((defined('FRA_FertigAntwort')&&FRA_FertigAntwort>'')?FRA_FertigAntwort:FRA_Antwort);
 $nProSeite=(FRA_ProSeite>0?FRA_ProSeite:$nFragen);
 if(isset($_POST['fra_ProSeite'])){$s=$_POST['fra_ProSeite']; if(strlen($s)>0) $nProSeite=($s>'0'?(int)$s:$nFragen);}
 $nAlleLsg=(isset($_POST['fra_AlleLsg'])?(int)$_POST['fra_AlleLsg']:(isset($_GET['fra_AlleLsg'])?(int)$_GET['fra_AlleLsg']:0));

 //Fragedarstellung
 $aF=array(); $bNochFragen=false; $nNochFrgn=0; $nLfdNr=(isset($_POST['fra_LfdNr'])?(int)$_POST['fra_LfdNr']:0);
 if($sNr=sprintf('%d',(int)$sFolge)){$sFolge.=';'; $nNochFrgn=substr_count($sFolge,';');} //noch mindestens eine Frage
 elseif(!$Meld) if($sFolge!='T') $Meld=FRA_TxKeineNummer;
 if($nNochFrgn>0){ //mindestens noch eine Frage vorhanden
  for($nBlock=0;$nBlock<$nProSeite;$nBlock++){ //pro Frage
   $aF=array(); $aTP=array(0); $bZeigeFrage=false; $bFehl=false; $bOK=false;
   while(strlen($sFolge)>1&&!$bZeigeFrage) if($p=strpos($sFolge,';')){
    if($sNr=sprintf('%d',(int)$sFolge)) $aF=fFraHole1Frage($sNr,$aD,$bSQLOpen,$DbO); $sAntw=substr($sFolge,0,$p); $nLfdNr++;
    $sFolge=substr($sFolge,$p+1); $sAntw=substr($sAntw,strpos($sAntw,':')+1); $sLsg=(isset($aF[5])?$aF[5]:'');
    if(isset($aF[4])&&($bZeigeFrage=!empty($aF[4]))){
     if($nAlleLsg>0||!FRA_LoesungsFalsche){if($sLsg==$sAntw) $bOK=true; else $bFehl=true;} elseif($sLsg==$sAntw) $bZeigeFrage=false;
    }
   }
   if($bZeigeFrage){
    $Y="\n".'<div class="fra'.($bFehl?'TxtF':($bOK?'TxtR':'Text')).'">'; //TextBlock Anfang
    if(FRA_ZeigeNummer) $sZlN=' <div class="fraFrNr">'.(FRA_NummernTyp!=2?fFraTx(FRA_TxFrage).' '.sprintf('%'.FRA_NummerStellen.'d/%'.FRA_NummerStellen.'d',$nLfdNr,$nFragen):'').(FRA_NummernTyp>2?', &nbsp; ':'').(FRA_NummernTyp>1?fFraTx(FRA_TxFrage.'-'.FRA_TxNr).' '.$sNr:'').'</div>';
    if(FRA_ZeigeNummer=='oben') $Y.="\n".$sZlN;
    if(FRA_ZeigeKategorie=='oben'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
    $Y.="\n".' <div class="fraFrag">'.fFraBB(fFraTx(trim(FRA_TxVorFrage.' '.$aF[4]))).'</div>';
    if(FRA_ZeigeBemerkung=='oben'&&($t=rtrim($aF[17]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
    if(FRA_ZeigeKategorie=='unten'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
    for($i=1;$i<=9;$i++){$aLsg[$i]=(strpos('#,'.$sLsg,','.$i)>0); $aAntw[$i]=(strpos('#,'.$sAntw,','.$i)>0);} $i=0;
    while(($t=$aF[++$i+7])&&$i<=9){ //Antwortenschleife
     $nZ=$i; if($p=strpos($t,'|#')){$aTP[$i]=(int)substr($t,$p+2); $t=substr($t,0,$p);}else $aTP[$i]=0;
     if($aAntw[$i]){if($aLsg[$i]){$s='hakenGrn'; $l=FRA_TxZeigeRichtig;}else{$s='kreuzRot'; $l=FRA_TxZeigeUnnuetz;}}
     else if($aLsg[$i]){$s='hakenBlk'; $l=FRA_TxZeigeFehlt;}else{$s='kaestchen'; $l=FRA_TxZeigeLeer;}
     $Y.="\n".' <div class="fraAntw"><img src="'.FRA_Http.$s.'.gif" title="'.fFraTx($l).'" alt="'.fFraTx($l).'" width="16" height="16" border="0" />'.'&nbsp;'.fFraBB(fFraTx($t)).'</div>';
    }
    if(FRA_ZeigeBemerkung=='unten'&&($t=rtrim($aF[17]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
    if(FRA_ZeigeAntwZahl||FRA_ZeigePunkte||FRA_ZeigeVersuche){$t='';
     if(FRA_ZeigeKategorie=='info') if($aF[3]) $t=fFraTx(trim(FRA_TxKategorie.' '.$aF[3])).', ';
     if(FRA_ZeigeAntwZahl) $t.=(substr_count($aF[5],',')+1).' '.fFraTx(FRA_TxRichtige).', ';
     if(FRA_ZeigePunkte) $t.=$aF[6].' '.fFraTx(FRA_TxPunkte).', ';
     if(FRA_ZeigeVersuche) $t.=(FRA_AntwortVersuche?FRA_AntwortVersuche:'x').' '.fFraTx(FRA_TxVersuche).', ';
     $Y.="\n".' <div class="fraInfo">('.substr($t,0,-2).')</div>';
    }

    $nP=$aF[6]; $nW=0; $nTP=0;
    if($sLsg==$sAntw) $nP=$aF[6]; //komplett richtig
    else{ //nicht richtig
     if(FRA_TeilWertung>0){ //Teilantworten untersuchen
      $sA='*,'.$sAntw; $sL='*,'.$sLsg;
      for($j=1;$j<=$nZ;$j++){
       if(FRA_TeilWertung==1){if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nW++;} //nur Richtige
       else{if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $nW++;} //auch Falsche
      }
      switch(FRA_TeilWertung){
       case 1: //nur Richtige bewerten
        if(FRA_PunkteTeilen) $nTP=$nP*$nW/substr_count($sL,',');
        else{for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0) $nTP+=$aTP[$j];}
        break;
       case 2: //jede Entscheidung anteilig
        if(FRA_PunkteTeilen){if(substr_count($sA,',')<$nZ) $nTP=$nP*$nW/$nZ;}
        else{
         $nMinP=$nP; $nFak=0;
         for($j=1;$j<=$nZ;$j++) if($aTP[$j]>0) $nMinP=min($nMinP,$aTP[$j]);
         for($j=1;$j<=$nZ;$j++){if($aTP[$j]==0) $aTP[$j]=$nMinP; $nFak+=$aTP[$j];} $nFak=$nP/max($nFak,1);
         for($j=1;$j<=$nZ;$j++) if(strpos($sL,','.$j)>0&&strpos($sA,','.$j)>0||!strpos($sL,','.$j)&&!strpos($sA,','.$j)) $nTP+=$nFak*$aTP[$j];
        }
        break;
       case 3: if(substr_count($sA,',')<$nZ){$nW=$nW/$nZ; if($nW>0.99) $nTP=$nP; elseif($nW>=0.5) $nTP=0.5*$nP;} break; //50%-Regel
       case 4: if(substr_count($sA,',')<$nZ){$nW=$nZ-$nW; if($nW==0) $nTP=$nP; elseif($nW==1) $nTP=0.5*$nP;} break; //0-1-2-Fehler -> 100%-50%-0%
      }
      if(FRA_TeilWertung>1) if(substr_count($sA,',')>=$nZ) $nTP=0; //abstrafen
      if(FRA_PositivWertung) $nP=$nTP; else $nP-=$nTP;
     }elseif(!FRA_PositivWertung) $nP=$nP; //Fehlerpunkte
    }
    $sZeigeWert=fFraTx(str_replace('#P',str_replace('.',FRA_Dezimalzeichen,round($nP,1)),str_replace('#G',$aF[6],FRA_TxZeigeWertung)));

    if(strlen($sZeigeWert)>0) $Y.="\n".' <div class="fraFrNr" style="float:right">'.$sZeigeWert.'</div>';
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
    if(FRA_LoesungsAnmk){ //Anmerkung
     if(FRA_LoesungsAnmk==1) $t=rtrim($aF[17]); else $t='';
     if(FRA_LoesungsAnmk==2) if($bFehl) $t=rtrim($aF[18]); else $t=rtrim($aF[17]);
     if($t) $Y.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>";
    }
    $X.="\n\n".'<p class="fraMeld">'.fFraTx(str_replace('#Z',$nProSeite,str_replace('#N',$nLfdNr,FRA_TxZeigeLoesung))).'</p>';
    $X.="\n".'<div class="fraBlock"><!-- Block_'.($nBlock+1).' -->'.$Y."\n</div><!-- /Block_".($nBlock+1).' -->';
   } //bZeigeFrage
  } //proFrage
  if(FRA_LoesungsFalsche){
   $sTFolge=$sFolge; $aF=array(); $bZeigeFrage=false;
   while(strlen($sTFolge)>1&&!$bZeigeFrage) if($p=strpos($sTFolge,';')){
    if($sNr=sprintf('%d',(int)$sTFolge)) $aF=fFraHole1Frage($sNr,$aD,$bSQLOpen,$DbO); $sAntw=substr($sTFolge,0,$p);
    $sTFolge=substr($sTFolge,$p+1); $sAntw=substr($sAntw,strpos($sAntw,':')+1); $sLsg=(isset($aF[5])?$aF[5]:'');
    if(isset($aF[4])&&($bZeigeFrage=!empty($aF[4]))) if($nAlleLsg<=0&&$sLsg==$sAntw) $bZeigeFrage=false;
   }
   if(!$bZeigeFrage) $sFolge='';
  }
 } //nFragen

 //Formularseitenausgabe
 $X.="\n\n".'<form name="fraForm" class="fraForm" action="'.FRA_Self.'" method="post">
  <input type="hidden" name="fra_Aktion" value="'.(strlen($sFolge)>1?'zeige':'ende').'" />
  <input type="hidden" name="fra_Session" value="'.FRA_Session.'" />
  <input type="hidden" name="fra_Antwort" value="'.substr($sFolge,0,-1).'" />
  <input type="hidden" name="fra_AlleLsg" value="'.$nAlleLsg.'" />
  <input type="hidden" name="fra_LfdNr" value="'.$nLfdNr.'" />
  <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
  <input type="hidden" name="fra_Anzahl" value="'.$nFragen.'" />'.rtrim("\n  ".FRA_Hidden).'
  <p><input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxWeiter).'" /></p>'."\n</form>\n";

 return $X;
}

function fFraHole1Frage($Nr,$aD,$bSQL,$DbO){
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