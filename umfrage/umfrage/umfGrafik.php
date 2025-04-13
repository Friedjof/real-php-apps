<?php
if(!function_exists('fUmfSeite') ){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfGrafik(UMF_TxGrafik,'Meld');}
}

function fUmfGrafik($Meld,$MTyp){
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false; $bOhneLogin=false; $nNr=1; $nId=0;

 if($sSes=UMF_Session){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $bSes=true;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(defined('UMF_GrafikOhneLogin')&&UMF_GrafikOhneLogin){
  $bOhneLogin=true;
 }elseif(UMF_Nutzerverwaltung!=''||UMF_Registrierung!='') $Meld=UMF_TxNutzerLogin;

 if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  if(isset($_POST['umf_Frage'])) $nNr=max((int)$_POST['umf_Frage'],1);
 }else{ //GET
  if(isset($_GET['umf_Zeige'])) $nId=max((int)$_GET['umf_Zeige'],1);
  elseif(isset($_GET['umf_Frage'])) $nNr=max((int)$_GET['umf_Frage'],1);
 }

 $aF=NULL; $nSaetze=0; $aE=NULL; $nFNr=0; $nAntwAnzahl=max(20,ADU_AntwortZahl); //Frage holen
 $bUnscharf=UMF_UmfrUnscharf; if(UMF_Umfrage){$s=substr(constant('UMF_Umfr'.UMF_Umfrage),0,1); if($s=='1') $bUnscharf=true; elseif($s==='0') $bUnscharf=false;}
 if(!UMF_SQL){
  $aTmp=file(UMF_Pfad.UMF_Daten.UMF_Fragen); array_shift($aTmp);
  foreach($aTmp as $s){
   $a=explode(';',$s,4);
   if($a[1]>'0'&&(!UMF_Umfrage||$a[2]==UMF_Umfrage||($bUnscharf&&$a[2]==''))){
    if($nId>0){if($nId==$a[0]){$aF=explode(';',rtrim($s)); $nFNr=(int)$a[0];}} // Zeigen
    elseif(++$nSaetze==$nNr){$aF=explode(';',rtrim($s)); $nFNr=(int)$a[0];} // Grafik
   }
  }
  $aTmp=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nZl=count($aTmp);
  for($i=1;$i<$nZl;$i++) if((int)$aTmp[$i]==$nFNr){$aE=explode(';',rtrim($aTmp[$i])); array_shift($aE); break;}
 }else{ //SQL
  if($DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa)){
   if(!mysqli_connect_errno()){if(defined('UMF_SqlCharSet')&&UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
   if($DbO){
    if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabF.' WHERE aktiv="1"'.(!UMF_Umfrage?'':' AND(Umfrage="'.UMF_Umfrage.'"'.($bUnscharf?' OR Umfrage=""':'').')'))){
     if($a=$rR->fetch_row()) if($nId<=0) $nSaetze=$a[0]; $rR->close();
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' WHERE aktiv="1"'.(!UMF_Umfrage?'':' AND(Umfrage="'.UMF_Umfrage.'"'.($bUnscharf?' OR Umfrage=""':'').')').($nId>0?' AND Nummer="'.$nId.'"':' LIMIT '.($nNr-1).',1'))){
      if($aF=$rR->fetch_row()) $nFNr=$aF[0]; else $nFNr=0; $rR->close();
      if($rR=$DbO->query('SELECT Inhalt FROM '.UMF_SqlTabE.' WHERE Nummer="'.$nFNr.'"')){
       if($a=$rR->fetch_row()) $aE=explode(';',$a[0]); $rR->close();
      }else $Meld=UMF_TxSqlFrage;
     }else $Meld=UMF_TxSqlFrage;
    }else $Meld=UMF_TxSqlFrage;
   }else $Meld=UMF_TxSqlDaBnk;
  }else $Meld=UMF_TxSqlVrbdg;
 }

 if(!$bSes&&UMF_Nutzerverwaltung==''&&UMF_Registrierung==''||$bSes||$bOhneLogin){
  if(is_array($aF)&&is_array($aE)&&count($aF)>2&&count($aE)>1){ //Frage+Antworten gefunden
   $sF=fUmfBB(fUmfTx($aF[3])); $aA=array(); $i=-1; $k=0; $nMx=0; $nSum=0;
   for($i=0;$i<$nAntwAnzahl;$i++){if(isset($aF[7+$i])&&$aF[7+$i]) $aA[$k++]=$aF[7+$i]; $nMx=max($aE[$i],$nMx); $nSum+=$aE[$i];} //Antwortenschleife
   $nZ=count($aA); $nF=UMF_GrafikMaximum/max($nMx,1); $nW=round(100/$nZ); if($nSum==0) $nSum=1;
   $X =' <table class="umfGraf">';
   if(UMF_GrafikBalken){
    $sFrage="\n  <tr class=\"umfTr\">\n"; $sFrage.='   <td class="umfGraF" colspan="'.(UMF_GrafikWerte=='links'||UMF_GrafikWerte=='rechts'?3:2).'">'.$sF.(UMF_GrafikTlnAnz?' ('.$nSum.'&nbsp;'.fUmfTx(UMF_TxTeilnehmer).')':'')."</td>\n  </tr>";
    if(UMF_GrafikFrage=='oben') $X.=$sFrage;
    for($i=0;$i<$nZ;$i++){
     $X.="\n  <tr class=\"umfTr\">";
     $X.="\n".'   <td class="umfGrAB">'.fUmfBB(fUmfTx($aA[$i])).'</td>';
     if(UMF_GrafikWerte=='links') $X.="\n".'   <td class="umfGraE">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
     $X.="\n".'   <td class="umfGrGB"><img src="'.UMF_Url.'balken.gif" width="'.round($nF*$aE[$i]).'" height="'.UMF_GrafikDicke.'" border="0" alt="'.$aE[$i].'" title="'.$aE[$i].'"></td>';
     if(UMF_GrafikWerte=='rechts') $X.="\n".'   <td class="umfGraE">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
     $X.="\n  </tr>";
    }
   }else{ //Saeulengrafik
    $sFrage="\n  <tr class=\"umfTr\">\n"; $sFrage.='   <td class="umfGraF" colspan="'.$nZ.'">'.$sF.(UMF_GrafikTlnAnz?' ('.$nSum.'&nbsp;'.fUmfTx(UMF_TxTeilnehmer).')':'')."</td>\n  </tr>";
    $sWerte="\n  <tr class=\"umfTr\">";
    for($i=0;$i<$nZ;$i++) $sWerte.="\n".'   <td class="umfGraE" style="width:'.$nW.'%">'.(UMF_GrafikProzente?round(100*$aE[$i]/$nSum).'%':$aE[$i]).'</td>';
    $sWerte.="\n  </tr>";
    if(UMF_GrafikFrage=='oben') $X.=$sFrage;
    if(UMF_GrafikWerte=='oben') $X.=$sWerte;
    $X.="\n  <tr class=\"umfTr\">";
    for($i=0;$i<$nZ;$i++) $X.="\n".'   <td class="umfGrGS" style="width:'.$nW.'%"><img src="'.UMF_Url.'saeule.gif" width="'.UMF_GrafikDicke.'" height="'.round($nF*$aE[$i]).'" border="0" alt="'.$aE[$i].'" title="'.$aE[$i].'"></td>';
    $X.="\n  </tr>\n  <tr class=\"umfTr\">";
    for($i=0;$i<$nZ;$i++) $X.="\n".'   <td class="umfGrAS" style="width:'.$nW.'%">'.fUmfBB(fUmfTx($aA[$i])).'</td>';
    $X.="\n  </tr>";
    if(UMF_GrafikWerte=='unten') $X.=$sWerte;
   }
   if(UMF_GrafikFrage=='unten') $X.=$sFrage;
   $X.="\n </table>";
   if($nSaetze>1){
    $Y=fUmfBB(fUmfTx(UMF_TxFrage)).':';
    for($i=1;$i<=$nSaetze;$i++) $Y.='&nbsp; <input class="umfAntw" type="radio" name="umf_Frage" value="'.$i.'"'.($nNr==$i?' checked="checked"':'').' onclick="nummerKlick()" /><a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:'').($bSes?'&amp;umf_Session='.$sSes:'').'&amp;umf_Frage='.$i.'">'.$i.'</a>';
    $X ="\n".'<script type="text/javascript"><!--'."\n function nummerKlick(){document.umfForm.submit();} // -->\n</script>\n\n".' <form name="umfForm" class="umfForm" action="'.UMF_Self.'" method="post">
    <input type="hidden" name="umf_Aktion" value="grafik" /><input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" /><input type="hidden" name="umf_Session" value="'.$sSes.'" />'.rtrim("\n   ".UMF_Hidden)."\n".$X."\n".' <div class="umfGrNr">'.$Y."</div>\n </form>";
   }
   $X=(!empty($Meld)?' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n":'').$X;
  }else{ //keine Frage gefunden
   $X=' <p class="umfFehl">'.fUmfTx(UMF_TxFrageFehlt)."</p>\n";
   if(isset($SqlFehl)) $X.=' <p class="umfFehl">'.fUmfTx($SqlFehl)."</p>\n";
  }
  if($nId) $X.='<span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=ergebnis'.($sSes?'&amp;umf_Session='.$sSes:'').'">'.fUmfTx(UMF_TxErgebnisListe)."</a> ]</span>\n";
  if(strlen(UMF_ZentrumLink)>0&&$sSes) $X.='<span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').'">'.fUmfTx(UMF_ZentrumLink)."</a> ]</span>\n";
 }else $X=' <p class="umfFehl">'.fUmfTx($Meld)."</p>\n"; //Sessionsproblem
 if($nId==0&&strlen(UMF_NeuAnfangLink)>0) $X.='<span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.'">'.fUmfTx(UMF_NeuAnfangLink)."</a> ]</span>\n";
 return $X;
}
?>