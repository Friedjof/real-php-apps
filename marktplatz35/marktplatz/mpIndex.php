<?php
function fMpSeite(){
 $X=''; $bSes=false;
 //Schnellsuchfilter
 if(MP_SuchQFilter>0) $sSuchFlt=fMpSuchFilter(isset($_GET['mp_Such'])?fMpRq($_GET['mp_Such']):(isset($_POST['mp_Such'])?fMpRq($_POST['mp_Such']):''),(MP_SuchQFilter%2?'L':'R'));
 if(MP_SuchQFilter==1||MP_SuchQFilter==2||MP_SuchQFilter==3||MP_SuchQFilter==4) $X.="\n".$sSuchFlt;
 if($s=fMpTx(MP_TxIndexSeite)) $X.='<p class="mpMeld">'.$s.'</p>';
 if(MP_SuchQFilter==5||MP_SuchQFilter==6||MP_SuchQFilter==7||MP_SuchQFilter==8) $X.="\n".$sSuchFlt;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Session pruefen
 if(!$sSes=MP_Session) if(defined('MP_NeuSession')) $sSes=MP_NeuSession;
 if(MP_NListeAnders||MP_NDetailAnders||MP_NEingabeLogin||MP_NEingabeAnders) if($sSes>''){
  $sId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!MP_SQL){
    if(file_exists(MP_Pfad.MP_Daten.MP_Nutzer)) $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); else $aD=array(); $nSaetze=count($aD); $sId=$sId.';'; $p=strlen($sId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$sId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Meld=MP_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.MP_SqlTabN.' WHERE nr="'.$sId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Meld=MP_TxSessionUngueltig;
    }else $Meld=MP_TxSqlFrage;
   }
  }else $Meld=MP_TxSessionZeit;
 }
 $X.="\n".'<div class="mpIdxBx">'; //."\n".' <div class="mpIdxTr">';
 $aSeg=array(); $aHlp=array(); $aIcon=array();
 $aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); $nSeg=0;
 if(MP_IndexIcons) if($f=opendir(MP_Pfad.MP_Daten)){
  while($s=readdir($f)) if(substr($s,2,1)=='_') $aIcon[substr($s,0,2)]=$s; closedir($f);
 }
 while($n=array_search(++$nSeg,$aA)){
  $sSg=$aS[$n];
  if(MP_IndexZaehler){
   $sRefDat=date('Y-m-d');
   if(!MP_SQL){
    if(file_exists(MP_Pfad.MP_Daten.sprintf('%02d',$n).MP_Inserate)) $aTmp=file(MP_Pfad.MP_Daten.sprintf('%02d',$n).MP_Inserate); else $aTmp=array(); $nSaetze=max(is_array($aTmp)?count($aTmp)-1:0,0); $nUng=0;
    for($i=1;$i<=$nSaetze;$i++){
     $s=rtrim(substr($aTmp[$i],0,30)); $nP=strpos($s,';');
     if(substr($s,$nP+1,1)!='1'||substr($s,$nP+3,10)<$sRefDat) $nUng++;
    }$nSaetze-=$nUng;
   }elseif($DbO){
    $nSaetze=0;
    if($rR=$DbO->query('SELECT COUNT(nr) FROM '.str_replace('%',sprintf('%02d',$n),MP_SqlTabI).' WHERE online="1" AND mp_1>="'.$sRefDat.'"')){
     if($a=$rR->fetch_row()) $nSaetze=$a[0]; $rR->close();
    }
   }
   if(substr($sSg,0,1)!='~'&&(substr($sSg,0,1)!='*'||$bSes)) $aSeg[$n]=(substr($sSg,0,1)!='*'?$sSg:substr($sSg,1)).' ('.$nSaetze.')';
  }else if(substr($sSg,0,1)!='~'&&(substr($sSg,0,1)!='*'||$bSes)) $aSeg[$n]=(substr($sSg,0,1)!='*'?$sSg:substr($sSg,1));
  $aHlp[$n]=(substr($sSg,0,1)!='*'?$sSg:substr($sSg,1));
 }
 if(!defined('MP_IndexFuellen')) define('MP_IndexFuellen',true);
 if(MP_IndexInZeilen){ //zeilenweise
  $z=0; $nSp=1;
  foreach($aSeg as $k=>$v){
   if(++$z>MP_IndexSpalten) $z=1; $bNotLeer=(strpos($v,'LEER')===false);
   if(MP_IndexIcons&&isset($aIcon[sprintf('%02d',$k)])){
    $sIc=MP_Daten.$aIcon[sprintf('%02d',$k)]; $aIc=@getimagesize(MP_Pfad.$sIc);
    $sIc='<img src="'.MP_Url.$sIc.'" '.$aIc[3].' border="0" style="vertical-align:'.MP_IndexVertikal.'" alt="'.fMpTx($aHlp[$k]).'" title="'.fMpTx($aHlp[$k]).'">';
    if($bNotLeer) $sIc='<a class="mpIdx" href="'.fMpHref('liste',(MP_ListenLaenge?'1':''),'','',$k).'">'.$sIc.'</a>';
    if(MP_IndexIconLinks) $sIc.='&nbsp;'; else $sIc='<div>'.$sIc.'</div>';
   }else $sIc='';
   $X.="\n".' <div class="mpIdxIt">'."\n".'  <div class="mpIdxZe">'.$sIc.($bNotLeer?'<a class="mpIdx" href="'.fMpHref('liste',(MP_ListenLaenge?'1':''),'','',$k).'"><span class="mpNoBr">'.fMpTx($v).'</span></a>':'&nbsp;').'</div>'."\n </div>";
  }
  if(MP_IndexFuellen) while($z++<MP_IndexSpalten) $X.="\n".' <div class="mpIdxIt">'."\n".'  <div class="mpIdxZe">&nbsp;</div>'."\n </div>"; ; //auffuellen
 }else{ //spaltenweise
  $z=0; $nSp=1; $X.="\n".' <div class="mpIdxIt">';
  foreach($aSeg as $k=>$v){
   if(++$z>MP_IndexZeilen){$X.="\n".' </div>'."\n".' <div class="mpIdxIt">'; $z=1; $nSp++;} $bNotLeer=(strpos($v,'LEER')===false);
   if(MP_IndexIcons&&isset($aIcon[sprintf('%02d',$k)])){
    $sIc=MP_Daten.$aIcon[sprintf('%02d',$k)]; $aIc=@getimagesize(MP_Pfad.$sIc);
    $sIc='<img src="'.MP_Url.$sIc.'" '.$aIc[3].' border="0" style="vertical-align:'.MP_IndexVertikal.'" alt="'.fMpTx($aHlp[$k]).'" title="'.fMpTx($aHlp[$k]).'">';
    if($bNotLeer) $sIc='<a class="mpIdx" href="'.fMpHref('liste',(MP_ListenLaenge?'1':''),'','',$k).'">'.$sIc.'</a>';
    if(MP_IndexIconLinks) $sIc.='&nbsp;'; else $sIc='<div>'.$sIc.'</div>';
   }else $sIc='';
   $X.="\n".'  <div class="mpIdxWr"><div class="mpIdxZe">'.$sIc.($bNotLeer?'<a class="mpIdx" href="'.fMpHref('liste',(MP_ListenLaenge?'1':''),'','',$k).'"><span class="mpNoBr">'.fMpTx($v).'</span></a>':'&nbsp;').'</div></div>';
  }
  if(MP_IndexFuellen) while($z++<MP_IndexZeilen) $X.="\n".'  <div class="mpIdxWr"><div class="mpIdxZe">&nbsp;</div></div>'; //auffuellen
  $X.="\n".' </div>';
 } //zeilen-/spaltenweise
 $X.="\n</div>";
 if(MP_SuchQFilter>=9) $X.="\n".$sSuchFlt;
 return str_replace("\n ","\n",str_replace("\r",'','
 <style type="text/css">
 div.mpIdxBx{
  display:flex; flex-direction:row; flex-wrap:wrap;
  width:100%; max-width:calc('.(MP_IndexInZeilen?MP_IndexSpalten:$nSp).' * ('.MP_IndexZelleW.' + 20px)); margin:auto;
  justify-content:center; gap:'.(MP_IndexInZeilen?'5':'0').'px 5px;
 }
 div.mpIdxIt{
  margin:0; padding:0;
  flex-basis:calc('.MP_IndexZelleW.' + 10px);
 }
 div.mpIdxWr{
  margin:0; padding:0; margin-bottom:5px;
 }
 div.mpIdxZe{
  margin:1px; padding:4px;
  width:'.MP_IndexZelleW.'; max-width:'.MP_IndexZelleW.'; height:'.MP_IndexZelleH.'; text-align:'.MP_IndexHorizontal.'; vertical-align:'.MP_IndexVertikal.';
 }
 </style>')).$X;
}

function fMpSuchFilter($s,$sAlign){ //Schnellsuchfilter zeichnen
if(MP_Zeichensatz>0&&$_SERVER['REQUEST_METHOD']=='POST') if(MP_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
return '
<div class="mpFilt">
 <div class="mpSFlt'.$sAlign.'">
 <form class="mpFilt" action="'.fMpHref('suchen').'" method="post">'.rtrim("\n".MP_Hidden).'
 <div class="mpNoBr">'.fMpTx(MP_TxSuchen).' <input class="mpSFlt" name="mp_Such" value="'.fMpTx($s).'"><input type="submit" class="mpKnopf" value="" title="'.fMpTx(MP_TxSuchen).'"></div>
 </form>
 </div><div class="mpClear"></div>
</div>
';
}
?>