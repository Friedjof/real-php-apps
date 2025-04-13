<?php
function fKalSeite(){
 global $kal_FeldName, $kal_FeldType, $kal_SuchFeld, $kal_WochenTag;

 $X='';
 $s='<link rel="stylesheet" type="text/css" href="'.KAL_Url.'tcal.css">
 <script src="'.KAL_Url.'tcal.js"></script>
 <script>
  A_TCALCONF.format='."'".fKalTCalFormat()."'".';
  A_TCALCONF.weekdays=['."'".fKalTx(implode("','",$kal_WochenTag))."'".'];
  A_TCALCONF.months=['."'".fKalTx(str_replace(';',"','",KAL_TxLMonate))."'".'];
  A_TCALCONF.prevmonth='."'".fKalTx(KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat)."'".';
  A_TCALCONF.nextmonth='."'".fKalTx(KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat)."'".';
  A_TCALCONF.prevyear='."'".fKalTx(KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr)."'".';
  A_TCALCONF.nextyear='."'".fKalTx(KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr)."'".';
  A_TCALCONF.yearscroll='.(KAL_TCalYrScroll?'true':'false').';
 </script>';
 if(KAL_TCalPicker) $X.=str_replace("\n ","\n",str_replace("\r",'',trim($s)))."\n";

 //Formular- und Tabellenanfang
 $X.="\n".'<p class="kalMeld">'.fKalTx(KAL_TxSuchMeld).'</p>';
 $X.="\n".'<form class="kalSuch" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden);
 if(isset($_GET['kal_Session'])) $X.="\n".'<input type="hidden" name="kal_Session" value="'.fKalRq1($_GET['kal_Session']).'">';
 if(isset($_GET['kal_Zentrum'])) $X.="\n".'<input type="hidden" name="kal_Zentrum" value="'.fKalRq1($_GET['kal_Zentrum']).'">';
 $X.="\n".'<div class="kalTabl">';

 //max. Spaltenzahl bestimmen
 $nSpalten=0; reset($kal_SuchFeld); foreach($kal_SuchFeld as $k) $nSpalten=max($nSpalten,$k);
 //über alle Felder
 $nFelder=count($kal_FeldName); $nFarb=0; $aTmp=NULL;
 for($i=1;$i<$nFelder;$i++) if($k=$kal_SuchFeld[$i]){
  $X.="\n".' <div class="kalTbZl1">'; $t=$kal_FeldType[$i]; if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  $sFN=$kal_FeldName[$i]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz;
  if($t=='a'||$t=='k'||$t=='s'){ //Optionen bilden für Select-Box
   if(!is_array($aTmp)) $aTmp=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); //Kategorien holen
   $a=explode(';',(isset($aTmp[$i])?trim($aTmp[$i]):'')); array_shift($a);
   $sO='<option value="">-</option>'; foreach($a as $s) $sO.='<option value="'.fKalTx($s).'">'.fKalTx($s).'</option>';
  }
  $sF1=isset($_GET['kal_'.$i.'F1'])?fKalTx(fKalRq($_GET['kal_'.$i.'F1'])):'';
  if($nSpalten==1){ //generell 1-spaltig
   $X.="\n".'  <div class="kalTbSpa kalTbSpL">'.fKalTx($sFN).'</div>'; $s=''; $w='';
   if($t=='w'||$t=='n'||$t=='1'||$t=='u'||$t=='2'||$t=='3'||$t=='r'||$t=='o'||$t=='i'){$w=' style="width:7em;"';}
   elseif($t=='d'||$t=='@'){$w=' style="width:9em;"'; $s=' '.fKalDatumsFormat();}
   if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v'&&$t!='u') $X.="\n".'  <div class="kalTbSpa kalTbSpL"><input class="kalEing"'.$w.' type="text" name="kal_'.$i.'F1" value="'.$sF1.'">'.$s.'</div>';
   elseif($t=='d') $X.="\n".'  <div class="kalTbSpa kalTbSpL"><input class="kalTCal" type="text" name="kal_'.$i.'F1" value="'.$sF1.'">'.$s.'</div>';
   elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.$sF1.'"'); $X.='  <div class="kalTbSpa kalTbSpL"><select class="kalEing" name="kal_'.$i.'F1" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select></div>';}
   elseif($t=='j'||$t=='v') $X.='  <div class="kalTbSpa kalTbSpL"><input class="kalCheck" type="checkbox" name="kal_'.$i.'F1" value="J" '.($sF1=='J'?'checked="checked" ':'').'> '.fKalTx(KAL_TxJa).'&nbsp; '.fKalTx(KAL_TxOder).'&nbsp; <input class="kalCheck" type="checkbox" name="kal_'.$i.'F2" value="N" '.(isset($_GET['kal_'.$i.'F2'])&&$_GET['kal_'.$i.'F2']=='N'?'checked="checked" ':'').'> '.fKalTx(KAL_TxNein).'</div>';
  }else{ //oder mehrspaltig
   $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'; $s=''; $w=''; //Spalte 1
   if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='u'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){$s=' '.KAL_TxWie;}
   elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){$s=' '.KAL_TxIst; if($k>1) $s.=' '.KAL_TxBzwAb; $w=' style="width:7em;"';}
   elseif($t=='j'||$t=='v'){$s=' '.KAL_TxIst;}
   $X.='<div class="kalNorm">'.fKalTx($sFN.$s).'</div><div class="kalNorm">'; //Feldname und Text
   if($t!='d'&&$t!='@') $s=''; else{$w=' style="width:9em;"'; $s=' '.fKalDatumsFormat();}
   if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') $X.='<input class="kalEing"'.$w.' type="text" name="kal_'.$i.'F1" value="'.$sF1.'">'.$s;
   elseif($t=='d') $X.='<input class="kalTCal" type="text" name="kal_'.$i.'F1" value="'.$sF1.'">'.$s;
   elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.$sF1.'"'); $X.='<select class="kalEing" name="kal_'.$i.'F1" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
   $X.='</div></div>';
   $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'; $s=''; $w=''; //Spalte 2
   if($k>1||$t=='j'||$t=='v'){
    $sF2=isset($_GET['kal_'.$i.'F2'])?fKalTx(fKalRq($_GET['kal_'.$i.'F2'])):'';
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='u'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){$s=KAL_TxOder.' '.KAL_TxWie;}
    elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){$s=$sFN.' '.KAL_TxBis; $w=' style="width:7em;"';}
    $X.='<div class="kalNorm">'.fKalTx($s).'</div><div class="kalNorm">';
    if($t!='d'&&$t!='@') $s=''; else{$w=' style="width:9em;"'; $s=' '.fKalDatumsFormat();}
    if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') $X.='<input class="kalEing"'.$w.' type="text" name="kal_'.$i.'F2" value="'.$sF2.'">'.$s;
    elseif($t=='d') $X.='<input class="kalTCal" type="text" name="kal_'.$i.'F2" value="'.$sF2.'">'.$s;
    elseif($t=='a'||$t=='k'||$t=='s') {$j=strpos($sO,'value="'.$sF2.'"'); $X.='<select class="kalEing" name="kal_'.$i.'F2" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
    else $X.='<input class="kalCheck" type="checkbox" name="kal_'.$i.'F1" value="J" '.($sF1=='J'?'checked="checked" ':'').'> '.fKalTx(KAL_TxJa).'&nbsp; '.fKalTx(KAL_TxOder).'&nbsp; <input class="kalCheck" type="checkbox" name="kal_'.$i.'F2" value="N" '.($sF2=='N'?'checked="checked" ':'').'> '.fKalTx(KAL_TxNein);
    $X.='</div>';
   }else $X.='&nbsp;';
   $X.='</div>';
   if($nSpalten>2){ //Spalte 3
    $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'; $s=''; $sF3=isset($_GET['kal_'.$i.'F3'])?fKalTx(fKalRq($_GET['kal_'.$i.'F3'])):'';
    if($k>2){
     if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='u'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){
      $X.='<div class="kalNorm"><span class="kalNoBr">'.fKalTx(KAL_TxAberNicht).'</span></div>';
      if($t!='d'&&$t!='a'&&$t!='k'&&$t!='s') $X.='<div class="kalNorm"><input class="kalEing" type="text" name="kal_'.$i.'F3" value="'.$sF3.'"></div>';
      elseif($t=='d') $X.='<div class="kalNorm"><input class="kalTCal" type="text" name="kal_'.$i.'F3" value="'.$sF3.'"></div>';
      else {$j=strpos($sO,'value="'.$sF3.'"'); $X.='<div class="kalNorm"><select class="kalEing" name="kal_'.$i.'F3" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select></div>';}
     }else $X.='&nbsp;';
    }else $X.='&nbsp;';
    $X.='</div>';
   }
  }//spaltig
  $X.="\n".' </div>';
 }//alle Felder
 if(KAL_SuchArchiv){
  if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  $X.="\n".' <div class="kalTbZl1">';
  $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'.fKalTx(KAL_TxSondersuche).'</div>';
  $X.="\n".'  <div class="kalTbSp1 kalTbSpL"><input class="kalCheck" type="checkbox" name="kal_Archiv" value="@" '.(isset($_GET['kal_Intervall'])&&$_GET['kal_Intervall']=='@'?'checked="checked" ':'').'> '.fKalTx(KAL_TxIm.' '.KAL_TxArchiv).'</div>';
  if($nSpalten>2) $X.="\n".'  <div class="kalTbSp1 kalTbSpL">&nbsp;</div>';
  $X.="\n".' </div>';
 }
 if(KAL_SuchIntervall){
  if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  $X.="\n".' <div class="kalTbZl1">';
  $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'.fKalTx(KAL_TxSuchIntervall).'<div class="kalNorm"><span class="kalMini">'.fKalTx(KAL_TxKeinAnderesDatum).'</span></div></div>';
  $X.="\n".'  <div class="kalTbSp1 kalTbSpL">'.fKalIntervallFilter().'</div>';
  if($nSpalten>2) $X.="\n".'  <div class="kalTbSp1 kalTbSpL">&nbsp;</div>';
  $X.="\n".' </div>';
 }
 $X.="\n".'</div>';
 $X.="\n".'<div class="kalSchalter"><input type="submit" class="kalSchalter" value="'.fKalTx(KAL_TxSuchen).'" title="'.fKalTx(KAL_TxSuchen).'"></div>';
 $X.="\n".'</form>'."\n";
 return $X;
}

function fKalDatumsFormat(){
 $s1=KAL_TxSymbTag; $s2=KAL_TxSymbMon; $s3=(KAL_Jahrhundert?KAL_TxSymbJhr:'').KAL_TxSymbJhr;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=KAL_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=KAL_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
function fKalTCalFormat(){
 $s1='d'; $s2='m'; $s3=(KAL_Jahrhundert?'Y':'y');
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3='d'; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2='d'; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalIntervallFilter(){ //Intervallfilter zeichnen
$sIntervall=KAL_Intervall; $sIvWerte=KAL_IvWerte; $s=' selected="selected"';
if(isset($_GET['kal_Session'])){$sIntervall=KAL_NIntervall; $sIvWerte=KAL_NIvWerte;}
if($sIntervall>'-') if(isset($_GET['kal_Intervall'])) $sIntervall=fKalRq1($_GET['kal_Intervall']);
if(strlen($sIvWerte)>0){
$r='
   <select class="kalEing kalAuto" name="kal_Intervall" size="1">'."\n";
if(strpos($sIvWerte,'0')>0) $r.='    <option value="0"'.($sIntervall=='0'?$s:'').'>'.fKalTx(KAL_TxAlle.' '.KAL_TxTermine).'</option>'."\n";
if(strpos($sIvWerte,'1')>0) $r.='    <option value="1"'.($sIntervall=='1'?$s:'').'>1 '.fKalTx(KAL_TxTag).'</option>'."\n";
if(strpos($sIvWerte,'3')>0) $r.='    <option value="3"'.($sIntervall=='3'?$s:'').'>3 '.fKalTx(KAL_TxTage).'</option>'."\n";
if(strpos($sIvWerte,'7')>0) $r.='    <option value="7"'.($sIntervall=='7'?$s:'').'>1 '.fKalTx(KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'4')>0) $r.='    <option value="14"'.($sIntervall=='14'?$s:'').'>2 '.fKalTx(KAL_TxWochen).'</option>'."\n";
if(strpos($sIvWerte,'A')>0) $r.='    <option value="A"'.($sIntervall=='A'?$s:'').'>1 '.fKalTx(KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'C')>0) $r.='    <option value="C"'.($sIntervall=='C'?$s:'').'>3 '.fKalTx(KAL_TxMonate).'</option>'."\n";
if(strpos($sIvWerte,'F')>0) $r.='    <option value="F"'.($sIntervall=='F'?$s:'').'>6 '.fKalTx(KAL_TxMonate).'</option>'."\n";
if(strpos($sIvWerte,'L')>0) $r.='    <option value="L"'.($sIntervall=='L'?$s:'').'>1 '.fKalTx(KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'a')>0) $r.='    <option value="a"'.($sIntervall=='a'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'d')>0) $r.='    <option value="d"'.($sIntervall=='d'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'g')>0) $r.='    <option value="g"'.($sIntervall=='g'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'j')>0) $r.='    <option value="j"'.($sIntervall=='j'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'m')>0) $r.='    <option value="m"'.($sIntervall=='m'?$s:'').'>'.fKalTx(KAL_TxDiese.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'c')>0) $r.='    <option value="c"'.($sIntervall=='c'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'f')>0) $r.='    <option value="f"'.($sIntervall=='f'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'i')>0) $r.='    <option value="i"'.($sIntervall=='i'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'l')>0) $r.='    <option value="l"'.($sIntervall=='l'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'o')>0) $r.='    <option value="o"'.($sIntervall=='o'?$s:'').'>'.fKalTx(KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'b')>0) $r.='    <option value="b"'.($sIntervall=='b'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklWo.' '.KAL_TxWoche).'</option>'."\n";
if(strpos($sIvWerte,'e')>0) $r.='    <option value="e"'.($sIntervall=='e'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat).'</option>'."\n";
if(strpos($sIvWerte,'h')>0) $r.='    <option value="h"'.($sIntervall=='h'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklQu.' '.KAL_TxQuartal).'</option>'."\n";
if(strpos($sIvWerte,'k')>0) $r.='    <option value="k"'.($sIntervall=='k'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklHJ.' '.KAL_TxHalbJahr).'</option>'."\n";
if(strpos($sIvWerte,'n')>0) $r.='    <option value="n"'.($sIntervall=='n'?$s:'').'>'.fKalTx(KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr).'</option>'."\n";
if(strpos($sIvWerte,'@')>0) $r.='    <option value="@"'.($sIntervall=='@'?$s:'').'>'.fKalTx(KAL_TxArchiv).'</option>'."\n";
$r.='   </select>
';} else $r='';
return $r;
}
?>