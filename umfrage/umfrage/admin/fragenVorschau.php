<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Fragen-Vorschau','<link rel="stylesheet" type="text/css" href="'.UMFPFAD.'umfStyle.css">','UFl');

$sMeld=''; $X=''; $nFragen=0; $bCaptcha=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);
$sQs=$_SERVER['QUERY_STRING']; if(!$p=strpos($sQs,'&amp;nr=')) if(!$p=strpos($sQs,'&nr=')) $p=strpos($sQs,'nr='); if($sQs=substr($sQs,0,$p)) $sQs='?'.$sQs;
if($nNr=(isset($_GET['nr'])?(int)$_GET['nr']:0)){
 $aF=array(); //Frage holen
 if(!UMF_SQL){
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nFragen=count($aD);
  for($i=1;$i<$nFragen;$i++){
   $s=substr($aD[$i],0,10); $p=strpos($s,';');
   if(substr($s,0,$p)==$nNr){ // gefunden
    $aF=explode(';',rtrim($aD[$i])); for($j=3;$j<$nAntwAnzahl+7;$j++) $aF[$j]=str_replace('`,',';',$aF[$j]); break;
  }}
 }else{//SQL
  if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' WHERE Nummer='.$nNr)){
   $aF=$rR->fetch_row(); $rR->close();
   if($rR=$DbO->query('SELECT COUNT(Nummer) FROM '.UMF_SqlTabF.' WHERE aktiv="1"')){
    if($a=$rR->fetch_row()) $nFragen=$a[0]; $rR->close();
   }
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
 }
 if(count($aF)>9){
   $sBtn="\n".'<div class="umfScha"><input type="reset" class="umfScha" value="'.fUmfTx(UMF_TxAbstimmen).'" /></div>';
   $sZnr="\n".' <div class="umfFrNr">'.fUmfTx(UMF_TxFrage).' '.str_replace('#N',sprintf('%'.UMF_NummerStellen.'d',$nNr),str_replace('#I',sprintf('%'.UMF_NummerStellen.'d',$aF[0]),str_replace('#M',sprintf('%'.UMF_NummerStellen.'d',$nFragen),UMF_NummernText))).'</div>';
   $sAm1=($aF[5]?"\n".' <div class="umfAnmk">'.fUmfBB(fUmfTx($aF[5])).'</div>':'');
   $sAm2=($aF[6]?"\n".' <div class="umfAnmk">'.fUmfBB(fUmfTx($aF[6])).'</div>':'');

   $X="\n".'<div class="umfTxBl"><!-- TextBlock -->'; //TextBlock Anfang
   if(UMF_ZeigeBemerkung=='oben2') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben2') $X.=$sAm2;
   if(UMF_ZeigeNummer=='oben') $X.=$sZnr;
   if(UMF_ZeigeBemerkung=='oben3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben3') $X.=$sAm2;
   $X.="\n".' <div class="umfFrag">'.fUmfBB(fUmfTx(trim(UMF_TxVorFrage.' '.$aF[3]))).'</div>';
   if(UMF_ZeigeBemerkung=='oben4') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='oben4') $X.=$sAm2;
   $i=0; if(UMF_RadioButton) $s='radio'; else $s='checkbox';
   while(++$i<=$nAntwAnzahl&&($t=(isset($aF[6+$i])?$aF[6+$i]:''))){//Antwortenschleife
    $X.="\n".' <div class="umfAntw"><input class="umfAntw" type="'.$s.'" name="umf_Antw[]" value="'.$i.'" />&nbsp;'.fUmfBB(fUmfTx($t)).'</div>';
   }
   if(UMF_ZeigeBemerkung=='unten1') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten1') $X.=$sAm2;
   if(UMF_ZeigeNummer=='unten') $X.=$sZnr;
   if(UMF_ZeigeBemerkung=='unten2') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten2') $X.=$sAm2;

   $sCapTyp=(isset($_POST['umf_CaptchaTyp'])?$_POST['umf_CaptchaTyp']:UMF_CaptchaTyp); $bCapOk=false; $bCapErr=false;
   if($bCaptcha=UMF_Captcha&&(!(UMF_Nutzerzwang||UMF_TeilnehmerSperre))){ //Captcha-Zeile
    require_once(UMF_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
    if(isset($_POST['umf_CaptchaCode'])){
     if($Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
    }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);}
    $X.="\n".'    <div class="umfCapt">
     <div>'.fUmfTx(UMF_TxCaptchaFeld).':</div>
     <div>
      <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
      <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
      <div'.($bCapErr?' class="umfFehl"':'').'>
       <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="http://'.UMF_Www.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
       <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
      </div>
     </div>
    </div>';
   }

   if(UMF_Layout==0) {
    if(UMF_ZeigeBemerkng2=='oben1'&&$t=$aF[6]) $X=$sAm2.$X; if(UMF_ZeigeBemerkung=='oben1'&&$t=$aF[5]) $X=$sAm1.$X;
    $X.="\n ".$sBtn;
    if(UMF_ZeigeBemerkung=='unten3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten3') $X.=$sAm2;
   }
   $X.="\n</div><!-- /TextBlock -->"; //TextBlock Ende

   if(UMF_Layout>0){ // BildLayout Anfang
    if($sBld=$aF[4]) $sBld=UMF_Bilder.$sBld; elseif(UMF_BildErsatz) $sBld=UMF_Bilder.UMF_BildErsatz;
    if($sBld){
     $a=@getimagesize(UMF_Pfad.$sBld);
     $sBld='<img class="umfBild" src="http://'.UMF_Www.$sBld.'" '.$a[3].' border="0" alt="'.fUmfTx(UMF_TxFrage).'-'.$nNr.'" title="'.fUmfTx(UMF_TxFrage).'-'.$nNr.'" />';
    }else $sBld='&nbsp;';
    if(UMF_Layout==1){ // Bild links
     $X="\n".'<div class="umfBldL"><!-- Bild links -->'."\n ".$sBld."\n</div><!-- /Bild -->\n".'<div class="umfTBlR"><!-- rechter Block -->'.$X."\n</div><!-- /rechter Block -->\n".'<div class="umfClrB"></div><!-- Floaten aufheben -->'.$sBtn;
    }elseif(UMF_Layout==2){ // Bild rechts
     $X="\n".'<div class="umfTBlL"><!-- linker Block -->'.$X."\n".'</div><!-- /linker Block -->'."\n".'<div class="umfBldR"><!-- Bild rechts -->'."\n ".$sBld."\n".'</div><!-- /Bild -->'."\n".'<div class="umfClrB"></div><!-- Floaten aufheben -->'.$sBtn;
    }else{ // Bild oben
     $X="\n".'<div class="umfBldO"><!-- Bild -->'."\n ".$sBld."\n".'</div><!-- /Bild -->'.$X.$sBtn;
    }
    if(UMF_ZeigeBemerkng2=='oben1'&&$t=rtrim($aF[6])) $X=$sAm2.$X; if(UMF_ZeigeBemerkung=='oben1'&&$t=rtrim($aF[5])) $X=$sAm1.$X;
    if(UMF_ZeigeBemerkung=='unten3') $X.=$sAm1; if(UMF_ZeigeBemerkng2=='unten3') $X.=$sAm2;
    $X="\n".'<div class="umfGsmt"><!-- Gesamt -->'.$X."\n</div><!-- /Gesamt -->\n";
   }
   $X="\n".'<form name="umfForm" class="umfForm" method="get">'.$X."</form>\n";
   if($bCaptcha) $X.=fJSCapCode();
 }else if(!$sMeld) $sMeld='<p class="admFehl">Frage '.$nNr.' nicht gefunden!</p>';
 if(!$sMeld) $sMeld='<p class="umfMeld">Frage Nummer '.$nNr.'</p>';
}else $nNr='UNBEKANNT';
?>

<div align="center">
<?php echo $sMeld; echo $X?>
</div>
<p align="center" style="margin:32px;">[ <a href="fragenListe.php<?php echo $sQs?>">zurück zur Liste</a> ]</p>

<?php
echo fSeitenFuss();

function fUmfTx($sTx){ //TextKodierung
 return str_replace('\n ','<br />',$sTx);
}
function fUmfBB($v){//BB-Code zu HTML
 $p=strpos($v,'[');
 while(!($p===false)){
  $t=substr($v,$p,9);
  if(substr($t,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($t,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($t,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($t,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($t,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($t,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($t,0,7)=='[color='){$w=substr($v,$p+7,9); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="color:'.$w.';">',$p,8+strlen($w));}
  elseif(substr($t,0,6)=='[size='){ $w=substr($v,$p+6,4); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($w)).'0%;">',$p,7+strlen($w));}
  elseif(substr($t,0,8)=='[/color]')$v=substr_replace($v,'</span>',$p,8);
  elseif(substr($t,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($t,0,8)=='[center]'){$v=substr_replace($v,'<p class="umfText" style="text-align:center">',$p,8);if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,7)=='[right]') {$v=substr_replace($v,'<p class="umfText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0)=='[/center]') {$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,5)=='[url]'){
   $m=$p+5; if(!$e=min(strpos($v,'[',$m),strpos($v,' ',$m))) $e=strpos($v,'[',$m);
   if(substr($v,$e,1)==' ') $v=substr_replace($v,'">',$e,1); else $v=substr_replace($v,'">'.substr($v,$m,$e-$m),$e,0);
   $v=substr_replace($v,'<a class="umfText" target="_blank" href="http://',$p,5);
  }elseif(substr($t,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($t,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5));
   if(substr($w,0,1)=='/') if($e=strrpos($w,'/')) if($e=strpos(UMF_Pfad,substr($w,0,$e+1))) $w=substr(UMF_Pfad,0,$e).$w;
   if(($a=@getimagesize($w))&&is_array($a)) $w='<img class="umfText" '.$a[3].' src="'; else $w='<pic a="';
   $v=substr_replace($v,$w,$p,5);
  }elseif(substr($t,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($t,0,5)=='[list'){
   if(substr($t,5,2)=='=o'){$w='o';$m=2;}else{$w='u';$m=0;}
   $v=substr_replace($v,'<'.$w.'l class="umfText"><li class="umfText">',$p,6+$m);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$w.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $m=strpos($v,'<br />',$p);
   while($m<$e&&$m>0){$v=substr_replace($v,'</li><li class="umfText">',$m,6); $e+=19; $m=strpos($v,'<br />',$m);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
function fJSCapCode(){
return "
<script type=\"text/javascript\">
 IE=document.all&&!window.opera; DOM=document.getElementById&&!IE; var ieBody=null; //Browserweiche
 var xmlHttpObject=null; var oForm=null;
 if(typeof XMLHttpRequest!='undefined') xmlHttpObject=new XMLHttpRequest();
 if(!xmlHttpObject){
  try{xmlHttpObject=new ActiveXObject('Msxml2.XMLHTTP');}
  catch(e){
   try{xmlHttpObject=new ActiveXObject('Microsoft.XMLHTTP');}
   catch(e){xmlHttpObject=null;}
 }}
 function reCaptcha(oFrm,sTyp){
  if(xmlHttpObject){
   oForm=oFrm; oForm.elements['umf_CaptchaTyp'].value=sTyp; oDate=new Date();
   xmlHttpObject.open('get','http://".UMF_Www."captcha.php?cod='+sTyp+oDate.getTime());
   xmlHttpObject.onreadystatechange=showResponse;
   xmlHttpObject.send(null);
 }}
 function showResponse(){
  if(xmlHttpObject){
   if(xmlHttpObject.readyState==4){
    var sResponse=xmlHttpObject.responseText;
    var sQuestion=sResponse.substring(33,sResponse.length-1);
    var aSpans=oForm.getElementsByTagName('span');
    var nImgId=0; for(var i=0;i<aSpans.length;i++) if(aSpans[i].className=='capImg'){nImgId=i; break;}
    var nQstId=0; for(var i=0;i<aSpans.length;i++) if(aSpans[i].className=='capQuest'){nQstId=i; break;}
    oForm.elements['umf_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['umf_CaptchaFrage'].value=sQuestion;
     aSpans[nQstId].innerHTML=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['umf_CaptchaFrage'].value='".UMF_TxCaptchaHilfe."';
     aSpans[nQstId].innerHTML='".UMF_TxCaptchaHilfe."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"http://".UMF_Www.UMF_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>";
}
?>