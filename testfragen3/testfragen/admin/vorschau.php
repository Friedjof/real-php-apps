<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Vorschau','<link rel="stylesheet" type="text/css" href="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.'fraStyle.css">','FFl');

$sQs=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
if(!$p=strpos($sQs,'&amp;nr=')) if(!$p=strpos($sQs,'&nr=')) $p=strpos($sQs,'nr='); if($sQs=substr($sQs,0,$p)) $sQs='?'.$sQs;
if(isset($_GET['nr'])&&($nNr=(int)$_GET['nr'])){

 $aF=array(); //Frage holen
 if(!FRA_SQL){
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){$sZl=$aD[$i]; $p=strpos($sZl,';'); if(substr($sZl,0,$p)==$nNr){$aF=explode(';',rtrim($sZl)); break;}}
  for($i=4;$i<19;$i++) $aF[$i]=(isset($aF[$i])?str_replace('`,',';',$aF[$i]):''); $nSaetze--;
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' WHERE Nummer='.$nNr)){
   $aF=$rR->fetch_row(); $rR->close();
   for($i=4;$i<19;$i++) $aF[$i]=(isset($aF[$i])?str_replace("\n",'\n ',str_replace("\r",'',$aF[$i])):'');
   if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.FRA_SqlTabF)){
    $a=$rR->fetch_row(); $rR->close(); $nSaetze=(int)$a[0];
   }
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';

 if(count($aF)!=19&&count($aF)!=18) if(!$sMeld) $sMeld='<p class="admFehl">'.str_replace('#N',$nNr,FRA_TxNichtGefunden).'</p>';

 $aFChk=array(0,false,false,false,false,false,false,false,false,false); $s=$aF[5];
 while($i=strpos($s,',')){$aFChk[(int)substr($s,0,$i)]=true; $s=substr($s,++$i);} $aFChk[(int)$s]=true;
 $aF[3]=str_replace('#',' -&gt; ',$aF[3]); //Unterkategorien
 $sBtn='<input type="submit" class="fraScha" style="background-image:url(http:'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'//'.FRA_Www.'schalter.gif)" value="'.FRA_TxAntworte.'" />';
 $X="\n".'<div class="fraText">'; //TextBlock Anfang
 if(FRA_ZeigeNummer) $sZlN="\n".' <div class="fraFrNr">'.fFraTx(FRA_TxFrage).' '.sprintf('%'.FRA_NummerStellen.'d/%'.FRA_NummerStellen.'d',$nNr,$nSaetze).'</div>';
 if(FRA_ZeigeNummer=='oben') $X.=$sZlN;
 if(FRA_ZeigeKategorie=='oben'&&$t=trim($aF[3])) $X.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
 $X.="\n".' <div class="fraFrag">'.fFraBB(fFraTx(trim(FRA_TxVorFrage.' '.$aF[4]))).'</div>';
 if(FRA_ZeigeBemerkung=='oben'&&($t=rtrim($aF[17]))) $X.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
 if(FRA_ZeigeKategorie=='unten'&&$t=trim($aF[3])) $X.="\n".' <div class="fraKatg">'.fFraBB(fFraTx(trim(FRA_TxKategorie.' '.$t))).'</div>';
 $i=0; if(strpos($aF[5],',')==false&&FRA_RadioButton) $s='radio'; else $s='checkbox';
 while(($t=$aF[++$i+7])) if($i<10){//Antwortenschleife
  if($nP=strpos($t,'|#')) $t=substr($t,0,$nP).' ('.substr($t,$nP+2).' Pkt)';
  $X.="\n".' <div class="fraAntw"><input class="fraAntw" type="'.$s.'" name="fra_Antw[]" value="'.$i.($aFChk[$i]?'" checked="checked':'').'" />&nbsp;'.fFraBB(fFraTx($t)).'</div>';
 }
 if(FRA_Auslassen&&!FRA_LernModus) $X.="\n".' <div class="fraAusl"><input class="fraAntw" type="checkbox" name="fra_Auslassen" value="1" />&nbsp;'.fFraBB(str_replace('#N',$nNr,fFraTx(FRA_TxAnsEnde))).'</div>';
 if(FRA_ZeigeBemerkung=='unten'&&($t=rtrim($aF[17]))) $X.="\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>';
 if(FRA_ZeigeAntwZahl||FRA_ZeigePunkte||FRA_ZeigeVersuche){$t='';
  if(FRA_ZeigeKategorie=='info') if($aF[3]) $t=fFraTx(trim(FRA_TxKategorie.' '.$aF[3])).', ';
  if(FRA_ZeigeAntwZahl) $t.=(substr_count($aF[5],',')+1).' '.fFraTx(FRA_TxRichtige).', ';
  if(FRA_ZeigePunkte) $t.=$aF[6].' '.fFraTx(FRA_TxPunkte).', ';
  if(FRA_ZeigeVersuche) $t.=(FRA_AntwortVersuche?FRA_AntwortVersuche:'x').' '.fFraTx(FRA_TxVersuche).', ';
  $X.="\n".' <div class="fraInfo">('.substr($t,0,-2).')</div>';
 }
 if(FRA_ZeigeNummer=='unten') $X.=$sZlN;
 $X.="\n</div>\n";//TextBlock Ende

 if(FRA_LayoutTyp>0){//BildLayout Anfang
  if(!$sBld=$aF[7]) if(FRA_BildErsatz) $sBld=FRA_BildErsatz; $aV=explode(' ',$sBld); $sBld=$aV[0]; $sExt=strtolower(substr($sBld,strrpos($sBld,'.')+1));
  if($sExt=='jpg'||$sExt=='png'||$sExt=='gif'||$sExt=='jpeg'){
    $sBld=FRA_Bilder.$sBld; $aI=@getimagesize(FRA_Pfad.$sBld);
    $sBld='<img src="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.$sBld.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.fFraTx(FRA_TxFrage).'-'.$nNr.'" title="'.fFraTx(FRA_TxFrage).'-'.$nNr.'" />';
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
  if(FRA_LayoutBildText) $X=$sBld.$X; else $X.=$sBld; //Bild vor Text
  if(FRA_LayoutTyp>1) $X.="\n".'<div class="fraClrB"></div>';
 }
 if(FRA_Offenlegen){ //Anmerkung
  if(FRA_ZeigeBemerkung=='aufdecken') $t=rtrim($aF[17]); else $t='';
  if(FRA_ZeigeBemerkung=='selektiv'){if($t=rand(0,1)){if(!$t=rtrim($aF[18])) $t=rtrim($aF[17]);} else $t=rtrim($aF[17]);}
  if($t) $X.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>";
 }

 $X="\n\n".'<div class="fraBlock"><!-- Block_0 -->'.$X."\n".'</div><!-- /Block_0 -->';

 /* //wozu??
 if(FRA_Offenlegen&&FRA_ZeigeBemerkung=='aufdecken'&&$t=rtrim($aF[17]))
  $X.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>\n";
 if(FRA_Offenlegen&&FRA_ZeigeBemerkung=='aufdecken'&&isset($aF[18])&&$t=rtrim($aF[18]))
  $X.="\n".'<div class="fraOffn">'."\n".' <div class="fraAnmk">'.fFraBB(fFraTx($t)).'</div>'."\n</div>\n";
 */

 $X.='<div class="fraScha"><input type="button" class="fraScha" style="background-image:url(http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.'schalter.gif)" value="'.FRA_TxAntworte.'" /></div>';

}else $nNr='UNBEKANNT';
?>

<div align="center">
<?php
if(empty($sMeld)) $sMeld='<p class="fraMeld" style="margin-top:32px;">Frage Nummer '.$nNr.'</p>';
echo $sMeld."\n\n".$X."\n";
?>
</div>
<p align="center" style="margin:16px;">[ <a href="liste.php<?php echo $sQs?>">zurück zur Liste</a> ]</p>

<?php
echo fSeitenFuss();

function fFraTx($sTx){ //TextKodierung
 return str_replace('\n ','<br />',$sTx);
}
function fFraBB($v){//BB-Code zu HTML
 $p=strpos($v,'[');
 while(!($p===false)){
  $t=substr($v,$p,10);
  if(substr($t,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($t,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($t,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($t,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($t,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($t,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($t,0,7)=='[color='){$w=substr($v,$p+7,9); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="color:'.$w.';">',$p,8+strlen($w));}
  elseif(substr($t,0,6)=='[size='){ $w=substr($v,$p+6,4); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($w)).'0%;">',$p,7+strlen($w));}
  elseif(substr($t,0,8)=='[/color]')$v=substr_replace($v,'</span>',$p,8);
  elseif(substr($t,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($t,0,8)=='[center]'){$v=substr_replace($v,'<p class="fraText" style="text-align:center">',$p,8);if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,7)=='[right]') {$v=substr_replace($v,'<p class="fraText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,9)=='[/center]') {$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,5)=='[sup]') $v=substr_replace($v,'<sup>',$p,5); elseif(substr($t,0,6)=='[/sup]') $v=substr_replace($v,'</sup>',$p,6);
  elseif(substr($t,0,5)=='[sub]') $v=substr_replace($v,'<sub>',$p,5); elseif(substr($t,0,6)=='[/sub]') $v=substr_replace($v,'</sub>',$p,6);
  elseif(substr($t,0,5)=='[url]'){
   $m=$p+5; if(!$e=min(strpos($v,'[',$m),strpos($v,' ',$m))) $e=strpos($v,'[',$m);
   if(substr($v,$e,1)==' ') $v=substr_replace($v,'">',$e,1); else $v=substr_replace($v,'">'.substr($v,$m,$e-$m),$e,0);
   $v=substr_replace($v,'<a class="fraText" target="_blank" href="'.(substr($v,$m,4)!='http'?'http'.'://':''),$p,5);
  }elseif(substr($t,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($t,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5)); $a=NULL; $u='';
   if(strpos($w,'://')){ //URL
    if(!$a=@getimagesize($w)) if($e=strpos($w,FRA_Www)) $a=@getimagesize(FRA_Pfad.substr($w,$e+strlen(FRA_Www)));
   }else{ //nur Pfad
    if(substr($w,0,1)=='/'){ //absoluter Pfad
     $u=$_SERVER['DOCUMENT_ROOT']; if(!strpos($w,substr($u,strpos($u,'/')+1)).'/') $u.=$w; $a=@getimagesize($u); $u='';
    }else{if(!$a=@getimagesize($w)) if($a=@getimagesize(FRAPFAD.$w)) $u=FRAPFAD;} //relativer Pfad
   }
   $w='<img class="fraText" '.(is_array($a)?$a[3].' ':'').'src="'.$u; $v=substr_replace($v,$w,$p,5);
  }elseif(substr($t,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($t,0,9)=='[youtube '){
   $n=strpos($v,']',$p+9); $w=trim(substr($v,$p+9,$n-($p+9))); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+9); $w=trim(substr($v,++$n,$e-$n));
    $v=substr_replace($v,'<iframe width="'.$a[0].'" height="'.$a[1].'" style="max-width:100%" src="',$p,$l+10);
   }else{$v=substr_replace($v,'',$p+8,$n-($p+8)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($t,0,9)=='[youtube]'){
   $e=strpos($v,'[',$p+9); $w=trim(substr($v,$p+9,$e-($p+9)));
   $v=substr_replace($v,'<iframe style="max-width:100%" src="',$p,9);
  }elseif(substr($t,0,10)=='[/youtube]'){$v=substr_replace($v,'" frameborder="0" allowfullscreen="">Ihr Browser zeigt keine iFrames. Siehe <a href="'.$w.'" target="_new">Youtube</a>.</iframe>',$p,10);}
  elseif(substr($t,0,7)=='[video '){
   $n=strpos($v,']',$p+7); $w=substr($v,$p+7,$n-($p+7)); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+7);  $w=substr($v,$n+1,$e-($n+1));
    $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
    $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
    $u='<video width="'.$a[0].'" height="'.$a[1].'" style="max-width:100%" controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,$l+8);
   }else{$v=substr_replace($v,'',$p+6,$n-($p+6)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($t,0,7)=='[video]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<video style="max-width:100%" controls'.($e?' type="video/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($t,0,8)=='[/video]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Video</a> nicht.</video>',$p,8);
  elseif(substr($t,0,7)=='[audio]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $r=''; if(!strpos($w,'://')) if(substr($w,0,1)!='/') $r=FRAPFAD; $w=$r.$w; //relativer Pfad
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<audio controls'.($e?' type="audio/'.$u.'"':'').' src="'.$r; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($t,0,8)=='[/audio]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Audio</a> nicht.</audio>',$p,8);
  elseif(substr($t,0,5)=='[list'){
   if(substr($t,5,2)=='=o'){$w='o';$m=2;}else{$w='u';$m=0;}
   $v=substr_replace($v,'<'.$w.'l class="fraText"><li class="fraText">',$p,6+$m);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$w.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $m=strpos($v,'<br />',$p);
   while($m<$e&&$m>0){$v=substr_replace($v,'</li><li class="fraText">',$m,6); $e+=19; $m=strpos($v,'<br />',$m);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>