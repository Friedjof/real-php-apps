<?php
function fUmfSeite(){
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false; $bNutzSes=false; $bTlnSes=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);

 if($sSes=UMF_Session){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $bSes=true; if(substr($sSes,4,1)<'9') $bNutzSes=true; else $bTlnSes=true;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(UMF_Nutzerverwaltung!=''||UMF_Registrierung!='') $Meld=UMF_TxNutzerLogin;

 if(UMF_TxDrucken&&(UMF_DruckGast||$bNutzSes||$bTlnSes)&&$Meld==''){
  $aSuch=explode(';',UMF_DruckSuche); $aSpa=explode(';',UMF_DruckSpalten); $aDr=array();
  $aTx=array(UMF_TxFrage.' '.UMF_TxNr,UMF_TxUmfr,UMF_TxFrage,UMF_TxBild,UMF_TxAntwort,UMF_TxBemerkung.'-1',UMF_TxBemerkung.'-2');
  if($_SERVER['REQUEST_METHOD']=='POST'){
   for($i=0;$i<7;$i++){ //Parameter holen
    $aDr[$i]=sprintf('%0d',(isset($_POST['umfDru'.$i])?$_POST['umfDru'.$i]:0));
    if($s=(isset($_POST['umfDr'.$i.'A'])?$_POST['umfDr'.$i.'A']:'')) $aFA[$i]=$s;
    if($s=(isset($_POST['umfDr'.$i.'B'])?$_POST['umfDr'.$i.'B']:'')) $aFB[$i]=$s;
    if($s=(isset($_POST['umfDr'.$i.'C'])?$_POST['umfDr'.$i.'C']:'')) $aFC[$i]=$s;
   }
   $s=(isset($_POST['umfDruN'])?$_POST['umfDruN']:0); if($aDr[0]&&$s) $aDr[0]=$s;

   if(UMF_SQL){ //SQL
    $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa);
    if(!mysqli_connect_errno()){if(UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);}else{$DbO=NULL; $Meld=UMF_TxSqlVrbdg;}
   }

   $aD=array(); $aTmp=array(); $aIdx=array(); //Daten holen
   if(!UMF_SQL){ //Textdaten
    $aD=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nSaetze=count($aD); $aK=array(0,2,3,4,27,5,6);
    for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
     $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true; $sA=''; for($j=7;$j<$nAntwAnzahl+7;$j++) if($s=(isset($a[$j])?$a[$j]:'')) $sA.='||'.$s; $a[$nAntwAnzahl+7]=$sA;
     if(isset($aFA)&&is_array($aFA)){reset($aFA); //Suchfiltern 1,2
      foreach($aFA as $j=>$v) if($b&&$j>0){
       if($w=(isset($aFB[$j])?$aFB[$j]:'')){if(stristr((isset($a[$aK[$j]])?str_replace('`,',';',$a[$aK[$j]]):''),$w)) $b2=true; else $b2=false;} else $b2=false;
       if(!(stristr((isset($a[$aK[$j]])?str_replace('`,',';',$a[$aK[$j]]):''),$v)||$b2)) $b=false;
      }else{if($w=(isset($aFB[0])?$aFB[0]:0)){if($a[0]<$v||$a[0]>$w) $b=false;}elseif($a[0]!=$v) $b=false;}
     }
     if($b&&isset($aFC)&&is_array($aFC)){ //Suchfiltern 3
      reset($aFC); foreach($aFC as $j=>$v) if(stristr(str_replace('`,',';',$a[$aK[$j]]),$v)){$b=false; break;}
     }
     if($b){ //Datensatz gueltig
      $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[2]; //Nr,Ufr
      $aTmp[$sNr][2]=str_replace('\n ',"\n",str_replace('`,',';',$a[3])); $aTmp[$sNr][3]=$a[4]; //Fra,Bld
      $aTmp[$sNr][4]=str_replace('\n ',"\n",str_replace('`,',';',$a[$nAntwAnzahl+7])); //Antw
      $aTmp[$sNr][5]=str_replace('\n ',"\n",str_replace('`,',';',$a[5])); //Anm1
      $aTmp[$sNr][6]=str_replace('\n ',"\n",str_replace('`,',';',$a[6])); //Anm2
      $aIdx[$sNr]=$i;
     }
    }$aD=array();
   }elseif($DbO){ //SQL-Daten
    $s=''; $t=''; $aK=array('Nummer','Umfrage','Frage','Bild','Antwort','Anmerkung1','Anmerkung2');
    if(isset($aFA)&&is_array($aFA)) foreach($aFA as $j=>$v){ //Suchfiltern 1-2
     if($j>0){
      if($j!=4){ //keine Antwort
       $sF=$aK[$j]; $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($aFB[$j])?$aFB[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
      }else{ //Antwort
       $w=(isset($aFB[4])?$aFB[4]:'');
       for($k=1;$k<=$nAntwAnzahl;$k++){$t.=' OR Antwort'.$k.' LIKE "%'.$v.'%"'; if($w) $t.=' OR Antwort'.$k.' LIKE "%'.$w.'%"';}
       $s.=' AND('.substr($t,4).')';
      }
     }else{if($w=(isset($aFB[0])?$aFB[0]:0)) $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Nummer="'.$v.'"';}
    }
    if(isset($aFC)&&is_array($aFC)) foreach($aFC as $j=>$v){ //Suchfiltern 3
     if($j!=4) $s.=' AND NOT('.$aK[$j].' LIKE "%'.$v.'%")';
     else for($k=1;$k<=$nAntwAnzahl;$k++) $s.=' AND NOT(Antwort'.$k.' LIKE "%'.$v.'%")';
    }
    $sF=''; for($i=1;$i<=$nAntwAnzahl;$i++) $sF.=',Antwort'.$i;
    if($rR=$DbO->query('SELECT Nummer,Umfrage,Frage,Bild,Anmerkung1,Anmerkung2'.($aDr[4]=='1'?$sF:'').' FROM '.UMF_SqlTabF.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
     $i=0;
     while($a=$rR->fetch_row()){
      $sNr=(int)$a[0]; $sA=''; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,Ufr
      $aTmp[$sNr][2]=str_replace("\r",'',$a[2]); $aTmp[$sNr][3]=$a[3]; //Fra,Bld
      if($aDr[4]=='1') for($k=1;$k<=$nAntwAnzahl;$k++) if($s=str_replace("\r",'',(isset($a[5+$k])?$a[5+$k]:''))) $sA.='||'.$s; $aTmp[$sNr][4]=$sA;
      $aTmp[$sNr][5]=str_replace("\r",'',$a[4]); $aTmp[$sNr][6]=str_replace("\r",'',$a[5]); //Anm
      $aIdx[$sNr]=++$i;
     }$rR->close();
    }else $Meld=UMF_TxSqlFrage;
   }

   if(UMF_DruckRueckw) arsort($aIdx);
   reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i]; $nNr=0;

   if(!$Meld){$MTyp='Meld'; if(!(isset($aFA)||isset($aFB))) $Meld=UMF_TxDruckGanzeListe; else $Meld=UMF_TxDruckFilterListe;}
   $X='
<table class="umfBlnd" style="width:99%" border="0" cellpadding="0" cellspacing="0">
 <tr class="umfBlnd">
  <td class="umfBlnd"><p class="umf'.$MTyp.'">'.$Meld.'</p></td>
  <td class="umfBlnd umfDrHd"><a href="javascript:window.print()"><img src="'.UMF_Url.'pix.gif" width="64" height="16" border="0" alt="drucken"></a></td>
 </tr>
</table>

<table class="umfDru" border="0" cellpadding="0" cellspacing="0">
 <tr class="umfDru">';
   if($aDr[0]>'0') $X.="\n".'  <td class="umfDru" style="text-align:center" width="1%">'.fUmfTx(UMF_TxNr).'</td>';
   if($aDr[1]=='1') $X.="\n".'  <td class="umfDru" style="text-align:center">'.fUmfTx($aTx[1]).'</td>';
   if($aDr[2]=='1'||$aDr[5]=='1'||$aDr[6]=='1') $X.="\n".'  <td class="umfDru">'.fUmfTx(rtrim(($aDr[2]=='1'?$aTx[2].' ':'').($aDr[5]=='1'?' &nbsp; (*) '.$aTx[5].' ':'').($aDr[6]=='1'?' &nbsp; (#) '.$aTx[6].' ':''))).'</td>';
   if($aDr[4]=='1') $X.="\n".'  <td class="umfDru">'.($aDr[4]=='1'?fUmfTx($aTx[4]):'').'</td>';
   if(UMF_Layout>0&&$aDr[3]=='1') $X.="\n".'  <td class="umfDru" style="text-align:center">'.fUmfTx($aTx[3]).'</td>';
   $X.='
 </tr>';
   foreach($aD as $a){ //Datenzeilen ausgeben
    $sNr=$a[0]; $sR=','.$a[3];
    $X.="\n".' <tr class="umfDru">';
    if($aDr[0]>'0') $X.="\n".'  <td class="umfDru" style="text-align:center" width="1%">'.sprintf('%'.UMF_NummerStellen.'d',($aDr[0]<'2'?$sNr:++$nNr)).'</td>';
    if($aDr[1]=='1') $X.="\n".'  <td class="umfDru" style="text-align:center">'.($a[1]?str_replace('#',' -&gt; ',$a[1]):'').'</td>';
    if($aDr[2]=='1'||$aDr[5]=='1'||$aDr[6]=='1'){
     $X.="\n".'  <td class="umfDru">';
     if($aDr[2]=='1') $X.="\n".'   <div class="umfDru">'.fUmfBB(fUmfTx($a[2])).'</div>';
     if($aDr[5]=='1') if($s=$a[5]) $X.="\n".'   <div class="umfDru">(*) '.fUmfBB(fUmfTx($s)).'</div>';
     if($aDr[6]=='1') if($s=$a[6]) $X.="\n".'   <div class="umfDru">(#) '.fUmfBB(fUmfTx($s)).'</div>';
     $X.="\n".'  </td>';
    }
    if($aDr[4]=='1') $X.="\n".'  <td class="umfDru"><ol style="margin:0;padding-left:1.6em"><li>'.str_replace('||','</li><li>',fUmfTx(substr($a[4],2))).'</li></ol></td>';
    if(UMF_Layout>0&&$aDr[3]=='1'){
     if($sBld=$a[3]){
      $sBld=UMF_Bilder.$sBld; $aI=@getimagesize(UMF_Pfad.$sBld);
      if(UMF_DruckBildW>0&&$aI[0]>UMF_DruckBildW) $aI[3]='width="'.UMF_DruckBildW.'" height="'.ceil($aI[1]*UMF_DruckBildW/$aI[0]).'"';
      $sBld='<img src="'.UMF_Url.$sBld.'" '.$aI[3].' border="0" alt="'.fUmfTx(UMF_TxFrage).'-'.$sNr.'" title="'.fUmfTx(UMF_TxFrage).'-'.$sNr.'" />';
     }
     $X.="\n".'  <td class="umfDru" style="text-align:center">'.($sBld?$sBld:'&nbsp;').'</td>';
    }
    $X.="\n".' </tr>';
   }
   $X.="\n".'</table>';
  }else{ //GET - Einstellformular
   $X='
<script type="text/javascript">
 function druWin(){dWin=window.open("about:blank","druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
</script>

<p class="umfMeld">'.fUmfTx(UMF_TxDruckMeld).'</p>
<form class="umfForm" action="'.UMF_Self.'" target="druck" onsubmit="druWin()" method="post">
<input type="hidden" name="umf_Aktion" value="drucken" />
<input type="hidden" name="umf_Session" value="'.$sSes.'" />
<table class="umfDrck" border="0" cellpadding="0" cellspacing="0">
 <tr class="umfTr">
  <td class="umfDrck" colspan="'.UMF_DruckSuchSpalten.'">'.fUmfTx(UMF_TxDruckFilter).'</td>
 </tr>
 <tr class="umfTr">
  <td class="umfDrck"><div class="umfNorm">'.fUmfTx($aTx[0].' '.(UMF_DruckSuchSpalten==1?UMF_TxWie:UMF_TxIstOderAb)).'</div><input class="umfDrck" type="text" name="umfDr0A" value="" size="20" /></td>';
   if(UMF_DruckSuchSpalten>1) $X.='
  <td class="umfDrck"><div class="umfNorm">'.fUmfTx(UMF_TxBis).'</div><input class="umfDrck" type="text" name="umfDr0B" value="" size="20" /></td>';
   if(UMF_DruckSuchSpalten>2) $X.='
  <td class="umfDrck">&nbsp;</td>';
   $X.='
 </tr>';
   for($i=1;$i<7;$i++) if($aSuch[$i]>'0'){
    $X.='
 <tr class="umfTr">
  <td class="umfDrck"><div class="umfNorm">'.fUmfTx($aTx[$i].' '.UMF_TxWie).'</div><input class="umfDrck" type="text" name="umfDr'.$i.'A" value="" size="20" /></td>';
   if(UMF_DruckSuchSpalten>1) $X.='
  <td class="umfDrck"><div class="umfNorm">'.fUmfTx(UMF_TxOderWie).'</div><input class="umfDrck" type="text" name="umfDr'.$i.'B" value="" size="20" /></td>';
   if(UMF_DruckSuchSpalten>2) $X.='
  <td class="umfDrck"><div class="umfNorm">'.fUmfTx(UMF_TxAberNichtWie).'</div><input class="umfDrck" type="text" name="umfDr'.$i.'C" value="" size="20" /></td>';
   $X.='
 </tr>';
   }
   $X.='
 <tr class="umfTr">
  <td class="umfDrck" colspan="'.UMF_DruckSuchSpalten.'">'.fUmfTx(UMF_TxDruckSpalten).'</td>
 </tr>
 <tr class="umfTr">
  <td class="umfDrck">';
   for($i=0;$i<7;$i++) if($aSpa[$i]>'0'&&($i!=3||UMF_Layout>0)){
    $X.="\n   ".'<div class="umfNorm"><input type="checkbox" name="umfDru'.$i.'" value="1"'.(isset($aSpa[$i])?' checked="checked"':'').' />'.fUmfTx($aTx[$i]).'</div>';
    if($i==0) $X.="\n   ".'<div class="umfNorm" style="padding-left:1.4em"><input type="radio" name="umfDruN" value="1"'.($aSpa[$i]=='1'?' checked="checked"':'').' />'.fUmfTx(UMF_TxDruckNrOriginal).'</div>'."\n   ".'<div class="umfNorm" style="padding-left:1.4em"><input type="radio" name="umfDruN" value="2"'.($aSpa[$i]=='2'?' checked="checked"':'').' />'.fUmfTx(UMF_TxDruckNrCronolog).'</div>';
   }
   $X.='
  </td>';
   if(UMF_DruckSuchSpalten>1) $X.='
  <td class="umfDrck">&nbsp;</td>';
   if(UMF_DruckSuchSpalten>2) $X.='
  <td class="umfDrck">&nbsp;</td>';
   $X.='
 </tr>
</table>
<input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxDrucken).'" title="'.fUmfTx(UMF_TxDrucken).'" />
</form>
';
   if($bNutzSes) $X.="\n".'<p>[ <a class="umfMenu" href="'.UMF_Self.(strpos(UMF_Self,'?')?'&amp;':'?').'umf_Aktion=zentrum&amp;umf_Session='.UMF_Session.'">'.fUmfTx(UMF_TxBenutzerzentrum).'</a> ]</p>';
   if($bTlnSes)  $X.="\n".'<p>[ <a class="umfMenu" href="'.UMF_Self.(strpos(UMF_Self,'?')?'&amp;':'?').'umf_Aktion=auswahl&amp;umf_Session='.UMF_Session.'">'.fUmfTx(UMF_TxTeilnehmerzentrum).'</a> ]</p>';
  }
 }else $X="\n".'<p class="umf'.$MTyp.'">'.$Meld.'</p><p class="umfFehl">'.fUmfTx(UMF_TxDruckSperre).'</p>';
 return $X;
}
?>