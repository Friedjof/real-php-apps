<?php
function fFraSeite(){
 $Meld=''; $MTyp='Fehl'; $bNutzSes=false; $bTlnSes=false;

 if($sSes=FRA_Session){
  $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n&&substr($sSes,9)>=(time()>>8)){if(substr($sSes,4,1)<'9') $bNutzSes=true; else $bTlnSes=true;} else $sSes='';
 }

 if(FRA_Drucken&&(FRA_DruckGast||$bNutzSes||$bTlnSes)){
  $aSuch=explode(';',FRA_DruckSuche); $aSpa=explode(';',FRA_DruckSpalten); $aDr=array();
  $aTx=array(FRA_TxFrage.' '.FRA_TxNr,FRA_TxKategorie,FRA_TxFrage,FRA_TxLoesung,FRA_TxPunkte,FRA_TxBild,FRA_TxAntwort,FRA_TxBemerkung.'-1',FRA_TxBemerkung.'-2');
  if($_SERVER['REQUEST_METHOD']=='POST'){
   for($i=0;$i<9;$i++){ //Parameter holen
    $aDr[$i]=sprintf('%0d',(isset($_POST['fraDru'.$i])?$_POST['fraDru'.$i]:0));
    if($s=(isset($_POST['fraDr'.$i.'A'])?$_POST['fraDr'.$i.'A']:'')) $aFA[$i]=$s;
    if($s=(isset($_POST['fraDr'.$i.'B'])?$_POST['fraDr'.$i.'B']:'')) $aFB[$i]=$s;
    if($s=(isset($_POST['fraDr'.$i.'C'])?$_POST['fraDr'.$i.'C']:'')) $aFC[$i]=$s;
   }
   $s=(isset($_POST['fraDruN'])?$_POST['fraDruN']:0); if($aDr[0]&&$s) $aDr[0]=$s;

   $bSQLOpen=false; //Datenbasis vorbereiten
   if(FRA_SQL){ //SQL
    $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
    if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $Meld=FRA_TxSqlVrbdg;
   }

   $aD=array(); $aTmp=array(); $aIdx=array(); //Daten holen
   if(!FRA_SQL){ //Textdaten
    $aD=@file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD); $aK=array(0,3,4,5,6,7,20,17,18);
    for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
     $a=explode(';',rtrim($aD[$i])); $sNr=(int)$a[0]; $b=true; $a[20]=''; for($j=8;$j<17;$j++) if($s=$a[$j]) $a[20].=$s.' ';
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
      $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=str_replace('`,',';',$a[3]); //Nr,Kat
      $aTmp[$sNr][2]=str_replace('\n ',"\n",str_replace('`,',';',$a[4])); $aTmp[$sNr][3]=$a[5]; $aTmp[$sNr][4]=$a[6]; $aTmp[$sNr][5]=$a[7]; //Fra,Lsg,Pkt,Bld
      if($aDr[6]=='1') for($k=1;$k<=9;$k++) if($s=str_replace('\n ',"\n",str_replace('`,',';',$a[7+$k]))) $aTmp[$sNr][6][$k]=$s; //Antw
      if($aDr[7]=='1') $aTmp[$sNr][7]=str_replace('\n ',"\n",str_replace('`,',';',$a[17])); //Anm1
      if($aDr[8]=='1') $aTmp[$sNr][8]=(isset($a[18])?str_replace('\n ',"\n",str_replace('`,',';',$a[18])):''); //Anm2
      $aIdx[$sNr]=$i;
     }
    }$aD=array();
   }elseif($bSQLOpen){ //SQL-Daten
    $s=''; $t=''; $aK=array('Nummer','Kategorie','Frage','Loesung','Punkte','Bild','Antwort','Anmerkung','Anmerkung2');
    if(isset($aFA)&&is_array($aFA)) foreach($aFA as $j=>$v){ //Suchfiltern 1-2
     if($j>0){
      if($j!=6){ //keine Antwort
       $sF=$aK[$j]; $s.=' AND('.$sF.' LIKE "%'.$v.'%"'; if($w=(isset($aFB[$j])?$aFB[$j]:'')) $s.=' OR '.$sF.' LIKE "%'.$w.'%"'; $s.=')';
      }else{ //Antwort
       $w=(isset($aFB[6])?$aFB[6]:'');
       for($k=1;$k<=9;$k++){$t.=' OR Antwort'.$k.' LIKE "%'.$v.'%"'; if($w) $t.=' OR Antwort'.$k.' LIKE "%'.$w.'%"';}
       $s.=' AND('.substr($t,4).')';
      }
     }else{if($w=(isset($aFB[0])?$aFB[0]:0)) $s.=' AND Nummer BETWEEN "'.$v.'" AND "'.$w.'"'; else $s.=' AND Nummer="'.$v.'"';}
    }
    if(isset($aFC)&&is_array($aFC)) foreach($aFC as $j=>$v){ //Suchfiltern 3
     if($j!=6) $s.=' AND NOT('.$aK[$j].' LIKE "%'.$v.'%")';
     else for($k=1;$k<=9;$k++) $s.=' AND NOT(Antwort'.$k.' LIKE "%'.$v.'%")';
    }
    if($rR=$DbO->query('SELECT Nummer,Kategorie,Frage,Loesung,Punkte,Bild'.($aDr[6]=='1'?',Antwort1,Antwort2,Antwort3,Antwort4,Antwort5,Antwort6,Antwort7,Antwort8,Antwort9':'').($aDr[7]=='1'?',Anmerkung':'').($aDr[8]=='1'?',Anmerkung2':'').' FROM '.FRA_SqlTabF.($s?' WHERE '.substr($s,4):'').' ORDER BY Nummer')){
     $i=0;
     while($a=$rR->fetch_row()){
      $sNr=(int)$a[0]; $aTmp[$sNr]=array($sNr); $aTmp[$sNr][1]=$a[1]; //Nr,Kat
      $aTmp[$sNr][2]=str_replace("\r",'',$a[2]); $aTmp[$sNr][3]=$a[3]; $aTmp[$sNr][4]=$a[4]; $aTmp[$sNr][5]=$a[5]; //Fra,Lsg,Pkt,Bld
      if($aDr[6]=='1') for($k=1;$k<=9;$k++) $aTmp[$sNr][6][$k]=str_replace("\r",'',$a[5+$k]);
      if($aDr[7]=='1') $aTmp[$sNr][7]=str_replace("\r",'',($aDr[6]=='1'?$a[15]:$a[6])); //Anm1
      if($aDr[8]=='1') $aTmp[$sNr][8]=(isset($a[6+($aDr[6]=='1'?9:0)+($aDr[7]=='1'?1:0)])?str_replace("\r",'',$a[6+($aDr[6]=='1'?9:0)+($aDr[7]=='1'?1:0)]):''); //Anm2
      $aIdx[$sNr]=++$i;
     }$rR->close();
    }else $Meld=FRA_TxSqlFrage;
   }

   if(FRA_DruckRueckw) arsort($aIdx);
   reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i]; $nNr=0;

   if(!$Meld){$MTyp='Meld'; if(!(isset($aFA)||isset($aFB))) $Meld=FRA_TxDruckGanzeListe; else $Meld=FRA_TxDruckFilterListe;}
   $X='
<table class="fraBlnd" style="width:99%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td><p class="fra'.$MTyp.'">'.$Meld.'</p></td>
  <td style="width:64px;vertical-align:top;background-image:url('.FRA_Http.'drucken.gif);background-repeat:no-repeat;"><a href="javascript:window.print()"><img src="'.FRA_Http.'pix.gif" width="64" height="16" border="0" alt="drucken"></a></td>
 </tr>
</table>

<table class="fraDru" border="0" cellpadding="0" cellspacing="0">
 <tr class="fraDru">';
   if($aDr[0]>'0') $X.='<td class="fraDru" align="center" width="1%">'.fFraTx(FRA_TxNr).'</td>';
   if(FRA_Kategorien>''&&$aDr[1]=='1') $X.='<td class="fraDru">'.fFraTx($aTx[1]).'</td>';
   if($aDr[2]=='1'||$aDr[6]=='1'||$aDr[7]=='1') $X.='<td class="fraDru">'.fFraTx(rtrim(($aDr[2]=='1'?$aTx[2].' ':'').($aDr[6]=='1'?$aTx[6].' ':'').($aDr[7]=='1'?$aTx[7].' ':'').($aDr[8]=='1'?$aTx[8]:'')))."</td>\n";
   if($aDr[3]=='1'||$aDr[4]=='1') $X.='<td class="fraDru" align="center" width="1%">'.($aDr[3]=='1'?fFraTx($aTx[3]):'').($aDr[4]=='1'?'<div class="fraDru">'.fFraTx($aTx[4]).'</div>':'')."</td>\n";
   if(FRA_LayoutTyp>0&&$aDr[5]=='1') $X.='<td class="fraDru">Bild</td>';
   $X.='
 </tr>
';
   foreach($aD as $a){ //Datenzeilen ausgeben
    $sNr=$a[0]; $sR=','.$a[3];
    $X.=' <tr class="fraDru" style="page-break-inside:avoid">'."\n";
    if($aDr[0]>'0') $X.='  <td class="fraDru" align="center" width="1%">'.sprintf('%'.FRA_NummerStellen.'d',($aDr[0]<'2'?$sNr:++$nNr)).'</td>'."\n";
    if(FRA_Kategorien>''&&$aDr[1]=='1') $X.='  <td class="fraDru">'.($a[1]?str_replace('#',' -&gt; ',$a[1]):'').'</td>'."\n";
    if($aDr[2]=='1'||$aDr[6]=='1'||$aDr[7]=='1'){
     $X.='  <td class="fraDru">'."\n";
     if($aDr[2]=='1') $X.='   <div class="fraDru">'.fFraBB(fFraTx($a[2]))."</div>\n";
     if($aDr[6]=='1'){
      $aA=array('#'); $aR=array(false); $sR='#,'.$a[3].',';
      for($i=1;$i<=9;$i++) if(isset($a[6][$i])&&($s=$a[6][$i])){$aA[]=$s; $aR[]=(strpos($sR,','.$i.',')?true:false);}
      $nAw=count($aA); $m=$nAw; $sR='';
      for($i=1;$i<$nAw;$i++){
       if(FRA_DruckZufallsAw){$k=rand(1,--$m); $s=$aA[$k]; if($aR[$k]) $sR.=','.$i; array_splice($aA,$k,1); array_splice($aR,$k,1);}
       else{$s=$aA[$i]; $sR=','.$a[3];}
       if($nP=strpos($s,'|#')) $s=substr($s,0,$nP).' ('.substr($s,$nP+2).' Pkt)';
       $X.='   <div class="fraDru">('.$i.') '.fFraBB(fFraTx($s))."</div>\n";
      }
     }
     if($aDr[7]=='1') if($s=$a[7]) $X.='   <div class="fraDru">(*) '.fFraBB(fFraTx($s))."</div>\n";
     if($aDr[8]=='1') if($s=(isset($a[8])?$a[8]:'')) $X.='   <div class="fraDru">(#) '.fFraBB(fFraTx($s))."</div>\n";
     $X.='  </td>'."\n";
    }
    if($aDr[3]=='1'||$aDr[4]=='1') $X.='  <td class="fraDru" align="center">'.($aDr[3]=='1'?substr($sR,1):'').'<div class="fraDru">'.($aDr[4]=='1'?$a[4]:'').'</div></td>'."\n";
    if(FRA_LayoutTyp>0&&$aDr[5]=='1'){
     if(!$sBld=$a[5]) if(FRA_BildErsatz) $sBld=FRA_BildErsatz; $aV=explode(' ',$sBld); $sBld=$aV[0]; $sExt=strtolower(substr($sBld,strrpos($sBld,'.')+1));
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
     $X.='  <td class="fraDru">'.($sBld?$sBld:'&nbsp;').'</td>'."\n";
    }
    $X.=' </tr>'."\n";
   }
   $X.='
   </table>';

  }else{ //GET - Einstellformular
   $X='
<script type="text/javascript">
 function druWin(){dWin=window.open("about:blank","druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
</script>

<p class="fraMeld">'.fFraTx(FRA_TxDrucken).'</p>
<form class="fraForm" action="'.FRA_Self.'" target="druck" onsubmit="druWin()" method="post">
<input type="hidden" name="fra_Aktion" value="drucken" />
<input type="hidden" name="fra_Session" value="'.$sSes.'" />
<table class="fraDrck" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td class="fraDrck" colspan="'.FRA_DruckSuchSpalten.'">'.fFraTx(FRA_TxDruckFilter).'</td>
 </tr>
 <tr>
  <td class="fraDrck"><div class="fraNorm">'.fFraTx($aTx[0].' '.(FRA_DruckSuchSpalten==1?FRA_TxWie:FRA_TxIstOderAb)).'</div><input class="fraDrck" type="text" name="fraDr0A" value="" size="20" /></td>';
   if(FRA_DruckSuchSpalten>1) $X.='
  <td class="fraDrck"><div class="fraNorm">'.fFraTx(FRA_TxBis).'</div><input class="fraDrck" type="text" name="fraDr0B" value="" size="20" /></td>';
   if(FRA_DruckSuchSpalten>2) $X.='
  <td class="fraDrck">&nbsp;</td>';
   $X.='
 </tr>';
   for($i=1;$i<9;$i++) if($aSuch[$i]>'0') {$X.='
 <tr>
  <td class="fraDrck"><div class="fraNorm">'.fFraTx($aTx[$i].' '.FRA_TxWie).'</div><input class="fraDrck" type="text" name="fraDr'.$i.'A" value="" size="20" /></td>';
   if(FRA_DruckSuchSpalten>1) $X.='
  <td class="fraDrck"><div class="fraNorm">'.fFraTx(FRA_TxOderWie).'</div><input class="fraDrck" type="text" name="fraDr'.$i.'B" value="" size="20" /></td>';
   if(FRA_DruckSuchSpalten>2) $X.='
  <td class="fraDrck"><div class="fraNorm">'.fFraTx(FRA_TxAberNichtWie).'</div><input class="fraDrck" type="text" name="fraDr'.$i.'C" value="" size="20" /></td>';
   $X.='
 </tr>';
 }
   $X.='
 <tr>
  <td class="fraDrck" colspan="'.FRA_DruckSuchSpalten.'">'.fFraTx(FRA_TxDruckSpalten).'</td>
 </tr>
 <tr>
  <td class="fraDrck">';
   for($i=0;$i<9;$i++) if($aSpa[$i]>'0'&&($i!=5||FRA_LayoutTyp>0)){
    $X.="\n   ".'<div class="fraNorm"><input type="checkbox" name="fraDru'.$i.'" value="1"'.(isset($aSpa[$i])?' checked="checked"':'').' />'.fFraTx($aTx[$i]).'</div>';
    if($i==0) $X.="\n   ".'<div class="fraNorm" style="padding-left:1.4em"><input type="radio" name="fraDruN" value="1"'.($aSpa[$i]=='1'?' checked="checked"':'').' />'.fFraTx(FRA_TxDruckNrOriginal).'</div>'."\n   ".'<div class="fraNorm" style="padding-left:1.4em"><input type="radio" name="fraDruN" value="2"'.($aSpa[$i]=='2'?' checked="checked"':'').' />'.fFraTx(FRA_TxDruckNrCronolog).'</div>';
   }
   $X.='
  </td>';
   if(FRA_DruckSuchSpalten>1) $X.='
  <td class="fraDrck">&nbsp;</td>';
   if(FRA_DruckSuchSpalten>2) $X.='
  <td class="fraDrck">&nbsp;</td>';
   $X.='
 </tr>
</table>
<input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_Drucken).'" title="'.fFraTx(FRA_Drucken).'" />
</form>
';
   if($bNutzSes) $X.='
<p>[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=zentrum&amp;fra_Session='.FRA_Session.'">'.fFraTx(FRA_TxBenutzerzentrum).'</a> ]</p>';
   if($bTlnSes) $X.='
<p>[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=auswahl&amp;fra_Session='.FRA_Session.'">'.fFraTx(FRA_TxTeilnehmerzentrum).'</a> ]</p>';


  }
 }else $X='keine Berechtigung zum Drucken oder Sitzungszeit abgelaufen';

 return $X;
}
?>