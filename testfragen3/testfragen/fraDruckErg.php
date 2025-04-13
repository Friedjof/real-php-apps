<?php
function fFraSeite(){
 $X=''; $Meld=''; $MTyp='Fehl'; $bNutzSes=false; $bTlnSes=false;

 if($sSes=FRA_Session){
  $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n&&substr($sSes,9)>=(time()>>8)){if(substr($sSes,4,1)<'9') $bNutzSes=true; else $bTlnSes=true;} else $sSes='';
 }

 if(FRA_NutzerLoesung&&FRA_Drucken&&($bNutzSes||$bTlnSes)){ // erlaubt

  $bSQLOpen=false; //SQL-Verbindung oeffnen 
  if(FRA_SQL){
   $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
   if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
  }

  if($sId=(isset($_GET['fra_Nr'])?$_GET['fra_Nr']:'')){
   $aE=array(); $aFn=array();
   if(!FRA_SQL){ //Ergebnis holen
    $aTmp=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aTmp); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){
     $aE=explode(';',rtrim($aTmp[$i]),14); break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' WHERE Eintrag="'.$sId.'"')){
     $aE=$rR->fetch_row(); $rR->close(); $sD=$aE[1];
     $aE[1]=date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)));
    }else $Meld='<p class="fraFehl">'.FRA_TxSqlFrage.'</p>';
   }
   if(!$Meld) if(count($aE)>4){
    $sAntwort='#|'.$aE[10];
    if(!FRA_SQL){ //Fragen holen
     $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
     for($i=1;$i<$nSaetze;$i++){
      $s=$aD[$i];
      if(strpos($sAntwort,'|'.substr($s,0,strpos($s,';')).':')>0){
       $a=explode(';',rtrim($s)); $n=$a[0]; $aFn[(int)$n]=$a;
     }}
    }elseif($DbO){ //SQL
     if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
      while($a=$rR->fetch_row()){
       $n=$a[0]; if(strpos($sAntwort,'|'.$n.':')>0) $aFn[(int)$n]=$a;
      }$rR->close();
     }else $Meld='<p class="fraFehl">'.FRA_TxSqlFrage.'</p>';
    }

    $Meld=str_replace('#D',$aE[1].' Uhr',str_replace('#N',$sId,FRA_TxErgDruckKopf));
    if(strpos($Meld,'#T')) if($s=trim($aE[13])){
     $aFnN=explode(';',FRA_NutzerFelder); $aFnT=explode(';',FRA_TeilnehmerFelder); $nFnN=count($aFnN); $nFnT=count($aFnT);
     if($a=explode(';',$s)){
      if($n=(int)$a[0]){ //Benutzer
       if(!FRA_SQL){ //Textdatei
        $aTmp=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nUsr=count($aTmp);
        for($i=1;$i<$nUsr;$i++){
         $s=$aTmp[$i];
         if($n==(int)$s){ //gefunden
          $aU=explode(';',rtrim($s)); $s=' '.FRA_DatPersonN;
          for($j=0;$j<$nFnN;$j++) if(strpos($s,'{'.$aFnN[$j].'}')) $s=str_replace('{'.$aFnN[$j].'}',$aU[$j],$s);
          break;
        }}
       }elseif($DbO){ //SQL
        if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$n.'"')){
         if($aU=$rR->fetch_row()){
          $s=' '.FRA_DatPersonN;
          for($i=0;$i<$nFnN;$i++) if(strpos($s,'{'.$aFnN[$i].'}')) $s=str_replace('{'.$aFnN[$i].'}',$aU[$i],$s);
         }$rR->close();
      }}}else{ //Teilnehmer
       $s=' '.FRA_DatPersonT; for($i=0;$i<$nFnT;$i++) if(strpos($s,'{'.$aFnT[$i].'}')) $s=str_replace('{'.$aFnT[$i].'}',$a[$i],$s);
     }}
     $Meld=str_replace('#T',$s,$Meld);
    }
    $Meld='<p class="fraMeld">'.fFraTx($Meld).'</p>';

    $a=explode('|',$aE[10]); $nFragen=count($a); $aA=array(); //Antworten holen
    for($i=0;$i<$nFragen;$i++){$t=$a[$i]; $p=strpos($t,':'); $aA[(int)substr($t,0,$p)]=substr($t,++$p);}
    $a=explode('|',$aE[9]); $nFragen=count($a); $aB=array(); //Bewertungskette
    for($i=0;$i<$nFragen;$i++){$t=$a[$i]; $p=strpos($t,':'); $aB[(int)substr($t,0,$p)]=substr($t,++$p);}

    $nPSum=0; $nGSum=0; $nRSum=0; $nFSum=0; $nBlock=0; $nLfdNr=0;
    foreach($aA as $k=>$sAntw){ //ueber alle Antworten
      $bFehl=false; $bOK=false; $aF=(isset($aFn[$k])?$aFn[$k]:array(0,0,0,'','',0,0,'','','','','','','','','','','','','','')); $sLsg=(isset($aF[5])?$aF[5]:'');
      if(substr($aB[$k],0,1)=='r') $bOK=true; else $bFehl=true;
      $Y="\n".'<div class="fra'.($bFehl?'TxtF':($bOK?'TxtR':'Text')).'">'; //TextBlock Anfang
      if(FRA_ZeigeNummer) $sZlN=' <div class="fraFrNr">'.(FRA_NummernTyp!=2?fFraTx(FRA_TxFrage).' '.sprintf('%'.FRA_NummerStellen.'d/%'.FRA_NummerStellen.'d',++$nLfdNr,$nFragen):'').(FRA_NummernTyp>2?', &nbsp; ':'').(FRA_NummernTyp>1?fFraTx(FRA_TxFrage.'-'.FRA_TxNr).' '.$k:'').'</div>';
      if(FRA_ZeigeNummer=='oben') $Y.="\n".$sZlN;
      if(FRA_ZeigeKategorie=='oben'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
      $Y.="\n".' <div class="fraFrag">'.fFraBB(fFraTx(trim(FRA_TxVorFrage.' '.$aF[4]))).'</div>';
      if(FRA_ZeigeBemerkung=='oben'&&($t=rtrim($aF[17]))) $Y.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
      if(FRA_ZeigeKategorie=='unten'&&($t=trim($aF[3]))) $Y.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
      for($i=1;$i<=9;$i++){$aLsg[$i]=(strpos('#,'.$sLsg,','.$i)>0); $aAntw[$i]=(strpos('#,'.$sAntw,','.$i)>0);} $i=0; $nZ=0;
      while(($t=$aF[++$i+7])&&$i<=9){ //Antwortenschleife
       $nZ=$i; if($p=strpos($t,'|#')){$aTP[$i]=(int)substr($t,$p+2); $t=substr($t,0,$p);}else $aTP[$i]=0;
       if($aAntw[$i]){if($aLsg[$i]){$s='hakenGrn'; $l=FRA_TxZeigeRichtig;}else{$s='kreuzRot'; $l=FRA_TxZeigeUnnuetz;}}
       else if($aLsg[$i]){$s='kreisSchw'; $l=FRA_TxZeigeFehlt;}else{$s='kaestchen'; $l=FRA_TxZeigeLeer;}
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

      $nP=$aF[6]; $nGSum+=$nP; $nW=0; $nTP=0;
      if($sLsg==$sAntw) $nRSum++; //komplett richtig
      else{ $nFSum++; //nicht richtig
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
      $sZeigeWert=fFraTx(str_replace('#P',rund($nP,1),str_replace('#G',$aF[6],FRA_TxZeigeWertung))); $nPSum+=round($nP,1);

      if(strlen($sZeigeWert)>0) $Y.="\n".' <div class="fraFrNr" style="float:right">'.$sZeigeWert.'</div>';
      if(FRA_ZeigeNummer=='unten') $Y.="\n".$sZlN;
      $Y.="\n</div>";//TextBlock Ende

      if(FRA_LayoutTyp>0){//BildLayout Anfang
       if(!$sBld=$aF[7]) if(FRA_BildErsatz) $sBld=FRA_BildErsatz; $aV=explode(' ',$sBld); $sBld=$aV[0]; $sExt=strtolower(substr($sBld,strrpos($sBld,'.')+1));
       if($sExt=='jpg'||$sExt=='png'||$sExt=='gif'||$sExt=='jpeg'){
        $sBld=FRA_Bilder.$sBld; $aI=@getimagesize(FRA_Pfad.$sBld);
        $sBld='<img src="'.FRA_Http.$sBld.'" '.(isset($a[3])?$a[3]:'').' border="0" alt="'.fFraTx(FRA_TxFrage).'-'.$k.'" title="'.fFraTx(FRA_TxFrage).'-'.$k.'" />';
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
      $X.='<!-- Block_'.(++$nBlock).' -->'."\n".'<div class="fraBlock" style="margin-bottom:1.5em;page-break-inside:avoid">'.$Y."\n</div><!-- /Block_".($nBlock).' -->';
    }

    $sFuss=str_replace('#G',$nGSum,str_replace('#P',rund($nPSum),str_replace('#Z',$aE[2],FRA_TxErgDruckFuss)));
    $sFuss=str_replace('#A',$nFragen,str_replace('#R',$nRSum,str_replace('#F',$nFSum,$sFuss)));
    if(strpos($sFuss,'#W')) $sFuss=str_replace('#W',rund(100*$nPSum/max($nGSum,1),1),$sFuss);
    if(strpos($sFuss,'#B')){
     if(FRA_VerbalPunkte) $p=round(100*$nPSum/max($nGSum,1)); else $p=round(100*$nRSum/max($nFragen,1));
     $s=str_replace('#R',$nRSum,str_replace('#F',$nFSum,str_replace('#A',$nFragen,str_replace('#P',rund($nPSum),str_replace('#G',$nGSum,FRA_VerbalTx0)))));
     for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$nRSum,str_replace('#F',$nFSum,str_replace('#A',$nFragen,str_replace('#P',rund($nPSum),str_replace('#G',$nGSum,constant('FRA_VerbalTx'.$i))))));
     $sFuss=str_replace('#B',$s,$sFuss);
    }

    $X='
<table class="fraBlnd" style="width:99%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><p class="fra'.$MTyp.'">'.$Meld.'</p></td>
  <td style="width:64px;vertical-align:top;background-image:url('.FRA_Http.'drucken.gif);background-repeat:no-repeat;"><a href="javascript:window.print()"><img src="'.FRA_Http.'pix.gif" width="64" height="16" border="0" alt="drucken"></a></td>
 </tr>
</table>'."\n".$X."\n".'<p class="fraMeld">'.fFraTx($sFuss).'</p>'."\n";

   }else $X="\n".'<p class="fraFehl">Keine Daten zum Ergebniseintrag '.$sId.'</p>'.$Meld;
  }else $X="\n".'<p class="fraFehl">unvollständiger Aufruf ohne Ergebnis-ID!</p>';
 }else $X="\n".'<p class="fraFehl">keine Berechtigung zum Drucken oder Sitzung abgelaufen</p>';
 return $X;
}

function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
?>