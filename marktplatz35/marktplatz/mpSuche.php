<?php
function fMpSeite(){
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Struktur holen
 $nFelder=0; $aStru=array();
 $aMpFN=array(); $aMpFT=array(); $aMpSF=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
 if(!MP_SQL){ //Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.MP_Segment.'"')){
   $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(count($aStru)>1){
  $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
  if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
  $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
  $aMpOF=explode(';',rtrim($aStru[4])); $aMpOF[0]=substr($aMpOF[0],14,1); $aMpOF[1]='1';
  $aMpSF=explode(';',rtrim($aStru[10])); $aMpSF[0]=substr($aMpSF[0],14,1);
  $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]='';  $aMpAW[1]='';
  $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
  $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
 }

 //Formular- und Tabellenanfang
 $X ="\n".'<p class="mpMeld">'.fMpTx(str_replace('#S',MP_SegName,MP_TxSuchMeld)).'</p>';
 $X.="\n".'<form class="mpSuch" action="'.fMpHref('liste').'" method="post">'.rtrim("\n".MP_Hidden);
 $X.="\n".'<div class="mpTabl">';

 //ueber alle Felder
 $nFelder=count($aMpFN); $nFarb=0; $aTmp=NULL;
 for($i=0;$i<$nFelder;$i++) if($aMpSF[$i]>'0'){
  $X.="\n".' <div class="mpTbZl1">'; $t=$aMpFT[$i]; if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  if($t=='a'||$t=='k'||$t=='s'){ //Optionen bilden fuer Select-Box
   if($t=='a') $a=explode('|','|'.$aMpAW[$i]); elseif($t=='k') $a=$aMpKW; elseif($t=='s') $a=$aMpSW; else $a=array();
   $sO=''; foreach($a as $s) $sO.='<option value="'.fMpTx($s).'">'.fMpTx($s).'</option>';
  }
  $sF1=isset($_GET['mp_'.$i.'F1'])?fMpTx(fMpRq($_GET['mp_'.$i.'F1'])):'';
  if(MP_SuchSpalten==1){ //generell 1-spaltig
   $X.="\n".'  <div class="mpTbSpa mpTbSpL">'.fMpTx($aMpFN[$i]).'</div>'; $s=''; $w='';
   if($t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='o'||$t=='i'){$w=' mpEin7';}
   elseif($t=='d'||$t=='@'){$w='  mpEin7'; $s=' '.fMpDatumsFormat();}
   if($t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') $X.="\n".'  <div class="mpTbSpa mpTbSpL"><input class="mpEing'.$w.'" type="text" name="mp_'.$i.'F1" value="'.$sF1.'" />'.$s.'</div>';
   elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.$sF1.'"'); $X.='  <div class="mpTbSpa mpTbSpL"><select class="mpEing" name="mp_'.$i.'F1" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select></div>';}
   elseif($t=='j'||$t=='v') $X.='  <div class="mpTbSpa mpTbSpL"><input class="mpCheck" type="checkbox" name="mp_'.$i.'F1" value="J" '.($sF1=='J'?'checked="checked" ':'').'/> '.fmpTx(MP_TxJa).'&nbsp; '.fMpTx(MP_TxOder).'&nbsp; <input class="mpCheck" type="checkbox" name="mp_'.$i.'F2" value="N" '.(isset($_GET['mp_'.$i.'F2'])&&$_GET['mp_'.$i.'F2']=='N'?'checked="checked" ':'').'/> '.fMpTx(MP_TxNein).'</div>';
  }else{ //oder mehrspaltig
   $X.="\n".'  <div class="mpTbSp1 mpTbSpL">'; $s=''; $w=''; //Spalte 1
   if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){$s=' '.MP_TxWie;}
   elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){$s=' '.MP_TxIst; if(MP_SuchSpalten>1) $s.=' '.MP_TxBzwAb; $w=' mpEin7';}
   elseif($t=='j'||$t=='v'){$s=' '.MP_TxIst;}
   $X.='<div class="mpNorm">'.fMpTx($aMpFN[$i].$s).'</div><div class="mpNorm">'; //Feldname und Text
   if($t!='d'&&$t!='@') $s=''; else $s=' '.fMpDatumsFormat();
   if($t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') $X.='<input class="mpEing'.$w.'" type="text" name="mp_'.$i.'F1" value="'.$sF1.'" />'.$s;
   elseif($t=='a'||$t=='k'||$t=='s'){$j=strpos($sO,'value="'.$sF1.'"'); $X.='<select class="mpEing" name="mp_'.$i.'F1" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
   $X.='</div></div>';
   $X.="\n".'  <div class="mpTbSp1 mpTbSpL">'; $s=''; $w=''; //Spalte 2
   if(MP_SuchSpalten>1||$t=='j'||$t=='v'){
    $sF2=isset($_GET['mp_'.$i.'F2'])?fMpTx(fMpRq($_GET['mp_'.$i.'F2'])):'';
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){$s=MP_TxOder.' '.MP_TxWie;}
    elseif($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){$s=$aMpFN[$i].' '.MP_TxBis; $w=' mpEin7';}
    $X.='<div class="mpNorm">'.fMpTx($s).'</div><div class="mpNorm">';
    if($t!='d'&&$t!='@') $s=''; else $s=' '.fMpDatumsFormat();
    if($t!='a'&&$t!='k'&&$t!='s'&&$t!='j'&&$t!='v') $X.='<input class="mpEing'.$w.'" type="text" name="mp_'.$i.'F2" value="'.$sF2.'" />'.$s;
    elseif($t=='a'||$t=='k'||$t=='s') {$j=strpos($sO,'value="'.$sF2.'"'); $X.='<select class="mpEing" name="mp_'.$i.'F2" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select>';}
    else $X.='<input class="mpCheck" type="checkbox" name="mp_'.$i.'F1" value="J" '.($sF1=='J'?'checked="checked" ':'').'/> '.fMpTx(MP_TxJa).'&nbsp; '.fMpTx(MP_TxOder).'&nbsp; <input class="mpCheck" type="checkbox" name="mp_'.$i.'F2" value="N" '.($sF2=='N'?'checked="checked" ':'').'/> '.fMpTx(MP_TxNein);
    $X.='</div>';
   }else $X.='&nbsp;';
   $X.='</div>';
   if(MP_SuchSpalten>2){ //Spalte 3
    $X.="\n".'  <div class="mpTbSp1 mpTbSpL">'; $s=''; $sF3=isset($_GET['mp_'.$i.'F3'])?fMpTx(fMpRq($_GET['mp_'.$i.'F3'])):'';
    if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='o'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='x'){
     $X.='<div class="mpNorm">'.fMpTx(MP_TxAberNicht).'</div>';
     if($t!='a'&&$t!='k'&&$t!='s') $X.='<div class="mpNorm"><input class="mpEing" type="text" name="mp_'.$i.'F3" value="'.$sF3.'" /></div>';
     else {$j=strpos($sO,'value="'.$sF3.'"'); $X.='<div class="mpNorm"><select class="mpEing" name="mp_'.$i.'F3" size="1">'.($j?substr_replace($sO,'selected="selected" ',$j,0):$sO).'</select></div>';}
    }else $X.='&nbsp;';
    $X.='</div>';
   }
  }//spaltig
  $X.="\n".' </div>';
 }//alle Felder
 if(MP_SuchArchiv){
  if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  $X.="\n".' <div class="mpTbZl1">';
  $X.="\n".'  <div class="mpTbSp1 mpTbSpL">'.fMpTx(MP_TxSondersuche).'</div>';
  $X.="\n".'  <div class="mpTbSp1 mpTbSpL"><input class="mpCheck" type="checkbox" name="mp_Archiv" value="1" '.(isset($_GET['mp_Archiv'])?'checked="checked" ':'').'/> '.fMpTx(MP_TxIm.' '.MP_TxArchiv).'</div>';
  if(MP_SuchSpalten>2) $X.="\n".'  <div class="mpTbSp1 mpTbSpL">&nbsp;</div>';
  $X.="\n".' </div>';
 }
 if(MP_SuchSortiert){
  $sOpt=''; $nSel=(isset($_GET['mp_Index'])?fMpRq1($_GET['mp_Index']):'');
  for($i=0;$i<$nFelder;$i++) if($aMpOF[$i]>'0') $sOpt.='<option value="'.$i.($i==$nSel?'" selected="selected':'').'">'.fMpTx($aMpFN[$i]).'</option>';
  if(++$nFarb>2) $nFarb=1; $sCSS='Dat'.$nFarb;
  $X.="\n".' <div class="mpTbZl1">';
  $X.="\n".'  <div class="mpTbSp1 mpTbSpL">'.fMpTx(MP_TxSortieren).'</div>';
  $X.="\n".'  <div class="mpTbSp1 mpTbSpL"><select class="mpEing" name="mp_Index"><option value="">--</option>'.$sOpt.'</select><br /><input class="mpCheck" type="checkbox" name="mp_Rueck" value="1" '.(isset($_GET['mp_Rueck'])?'checked="checked" ':'').'/> '.fMpTx(MP_TxRueckwaerts).'</div>';
  if(MP_SuchSpalten>2) $X.="\n".'  <div class="mpTbSp1 mpTbSpL">&nbsp;</div>';
  $X.="\n".' </div>';
 }
 $X.="\n".'</div>'; //Tabelle
 $X.="\n".'<div class="mpSchalter"><input type="submit" class="mpSchalter" value="'.fMpTx(MP_TxSuchen).'" title="'.fMpTx(MP_TxSuchen).'" /></div>';
 $X.="\n".'</form>'."\n";
 return $X;
}

function fMpDatumsFormat(){
 $s1=MP_TxSymbTag; $s2=MP_TxSymbMon; $s3=(MP_Jahrhundert?MP_TxSymbJhr:'').MP_TxSymbJhr;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=MP_TxSymbTag; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=MP_TxSymbTag; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}
?>