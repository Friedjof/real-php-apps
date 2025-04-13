<?php
header('Content-Type: text/html; charset=ISO-8859-1'); include 'hilfsFunktionen.php';
define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);

if(FRA_ErgDruckTempl) $sHtml=@implode('',@file(FRA_Pfad.'fraDrucken.htm')); else $sHtml='';
if($sHtml){
 if($p=strpos($sHtml,'href="fraStyle.css"')) $sHtml=substr_replace($sHtml,'href="'.FRA_Http.'fraStyle.css"',$p,19);
 if($p=strpos($sHtml,'{Inhalt}')){echo substr($sHtml,0,$p); $sHtml=trim(substr($sHtml,$p+8));}else $sHtml='';
}
if(!$sHtml) echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Testfragen-Script - Ergebnis-Druck</title>
<style type="text/css">
<!--
h1,h2,h3,h4,div,p,a,li{font-family:Verdana,Arial,Helvetica;}
-->
</style>
<link rel="stylesheet" type="text/css" href="'.FRA_Http.'fraStyle.css">
</head>

<body>
<h1 style="font-size:1.2em;text-align:center"><img src="_frage.gif" width="16" height="24" border="0" align="bottom" alt=""> Ergebnis-Druck</h1>
';

if($sId=(isset($_GET['nr'])?$_GET['nr']:'')){
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
  }else $sMeld='<p class="fraFehl">'.FRA_TxSqlFrage.'</p>';
 }
 if(!$sMeld) if(count($aE)>4){
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
   }else $sMeld='<p class="fraFehl">'.FRA_TxSqlFrage.'</p>';
  }

  $sMeld=str_replace('#D',$aE[1].' Uhr',str_replace('#N',$sId,FRA_TxErgDruckKopf));
  if(strpos($sMeld,'#T')) if($s=trim($aE[13])){
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
   $sMeld=str_replace('#T',$s,$sMeld);
  }
  $sMeld='<p class="fraMeld">'.fFraTx($sMeld).'</p>';

  $a=explode('|',$aE[10]); $nFragen=count($a); $aA=array(); //Antworten holen
  for($i=0;$i<$nFragen;$i++){$t=$a[$i]; $p=strpos($t,':'); $aA[(int)substr($t,0,$p)]=substr($t,++$p);}
  $a=explode('|',$aE[9]); $nFragen=count($a); $aB=array(); //Bewertungskette
  for($i=0;$i<$nFragen;$i++){$t=$a[$i]; $p=strpos($t,':'); $aB[(int)substr($t,0,$p)]=substr($t,++$p);}

  $X=''; $nPSum=0; $nGSum=0; $nRSum=0; $nFSum=0; $nBlock=0; $nLfdNr=0;
  foreach($aA as $k=>$sAntw){ //ueber alle Antworten
    $bFehl=false; $bOK=false; $aF=(isset($aFn[$k])?$aFn[$k]:array(0,0,0,'','',0,0,'','','','','','','','','','','','','','')); $sLsg=(isset($aF[5])?$aF[5]:'');
    if(isset($aB[$k])&&substr($aB[$k],0,1)=='r') $bOK=true; else $bFehl=true;
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
       $sBld='<img src="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.$sBld.'" '.$aI[3].' border="0" alt="'.fFraTx(FRA_TxFrage).'-'.$k.'" title="'.fFraTx(FRA_TxFrage).'-'.$k.'" />';
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
    $X.="\n".'<div class="fraBlock" style="margin-bottom:1.5em;page-break-inside:avoid"><!-- Block_'.(++$nBlock).' -->'.$Y."\n</div><!-- /Block_".($nBlock).' -->';
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
  $sFuss="\n".'<div>'.fFraTx($sFuss).'</div>';
  echo "\n".'<div class="fraBox">'.$sMeld.$X.$sFuss."\n".'</div>';
 }else echo "\n".'<p class="fraFehl">Keine Daten zum Ergebniseintrag '.$sId.'</p>'.$sMeld;
}else echo "\n".'<p class="fraFehl">Ergebnis-ID fehlt!</p> ';

echo "\n".'<hr style="border:0;border-top:1px solid #999">';
echo "\n".'<p><span class="fraMini"><span class="fraMini">'.date('d.m.Y, H:i:s').'</span></span></p>';
echo "\n".$sHtml."\n";
if(!$sHtml) echo "</body>\n</html>\n";

function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
function fFraTx($s){
 $s=str_replace('`,',';',$s);
 return str_replace('\n ','<br />',$s);
}
function fFraBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,10);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));}
  elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($o)).'0%">',$p,7+strlen($o));}
  elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="fraText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="fraText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[sup]') $v=substr_replace($v,'<sup>',$p,5); elseif(substr($Tg,0,6)=='[/sup]') $v=substr_replace($v,'</sup>',$p,6);
  elseif(substr($Tg,0,5)=='[sub]') $v=substr_replace($v,'<sub>',$p,5); elseif(substr($Tg,0,6)=='[/sub]') $v=substr_replace($v,'</sub>',$p,6);
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; $e=strpos($v,'[',$o); $l=strpos($v,' ',$o); if($l<$e&&$l>$p) $e=$l; if(substr($v,$e,1)!=' ') $l=5; else $l-=$p;
   $v=substr_replace($v,'',$p,$l);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'',$p,6);
  elseif(substr($Tg,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5)); $a=NULL; $u='';
   if(strpos($w,'://')){ //URL
    if(!$a=@getimagesize($w)) if($e=strpos($w,FRA_Www)) $a=@getimagesize(FRA_Pfad.substr($w,$e+strlen(FRA_Www)));
   }else{ //nur Pfad
    if(substr($w,0,1)=='/'){ //absoluter Pfad
     $u=$_SERVER['DOCUMENT_ROOT']; if(!strpos($w,substr($u,strpos($u,'/')+1)).'/') $u.=$w; $a=@getimagesize($u); $u='';
    }else{$w=FRAPFAD.$w; $a=@getimagesize($w); $u=FRAPFAD;} //relativer Pfad
   }
   $w='<img class="fraText" '.(is_array($a)?$a[3].' ':'').'src="'.$u; $v=substr_replace($v,$w,$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($Tg,0,9)=='[youtube '){
   $n=strpos($v,']',$p+9); $w=trim(substr($v,$p+9,$n-($p+9))); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+9); $w=trim(substr($v,++$n,$e-$n));
    $v=substr_replace($v,'<iframe width="'.$a[0].'" height="'.$a[1].'" src="',$p,$l+10);
   }else{$v=substr_replace($v,'',$p+8,$n-($p+8)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($Tg,0,9)=='[youtube]'){
   $e=strpos($v,'[',$p+9); $w=trim(substr($v,$p+9,$e-($p+9)));
   $v=substr_replace($v,'<iframe src="',$p,9);
  }elseif(substr($Tg,0,10)=='[/youtube]'){$v=substr_replace($v,'" frameborder="0" allowfullscreen="">Ihr Browser zeigt keine iFrames. Siehe <a href="'.$w.'" target="_new">Youtube</a>.</iframe>',$p,10);}
  elseif(substr($Tg,0,7)=='[video '){
   $n=strpos($v,']',$p+7); $w=substr($v,$p+7,$n-($p+7)); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+7);  $w=substr($v,$n+1,$e-($n+1));
    $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
    $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
    $u='<video width="'.$a[0].'" height="'.$a[1].'" controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,$l+8);
   }else{$v=substr_replace($v,'',$p+6,$n-($p+6)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($Tg,0,7)=='[video]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<video controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($Tg,0,8)=='[/video]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Video</a> nicht.</video>',$p,8);
  elseif(substr($Tg,0,7)=='[audio]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<audio controls'.($e?' type="audio/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($Tg,0,8)=='[/audio]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Audio</a> nicht.</audio>',$p,8);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="fraText"><li class="fraText">',$p,6+$l);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$q.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $l=strpos($v,'<br />',$p);
   while($l<$e&&$l>0){$v=substr_replace($v,'</li><li class="fraText">',$l,6); $e+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>