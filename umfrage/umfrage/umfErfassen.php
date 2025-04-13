<?php
if(!function_exists('fUmfSeite') ){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfErfassen(true);}
}

function fUmfErfassen($bDirekt){ //Seiteninhalt
 $sAktion='erfassen'; $sSes=UMF_Session; $sBtn=UMF_TxEintragen; global $DbO;
 $aFld=explode(';',';'.UMF_TeilnehmerFelder); $nFelder=count($aFld); $aPfl=explode(';',';'.UMF_TeilnehmerPflicht);
 $X=''; $aDat=array(); $aFehl=array(); $Meld=''; $MTyp='Fehl'; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

 //Captcha behandeln
 $sCapTyp=(isset($_POST['umf_CaptchaTyp'])?$_POST['umf_CaptchaTyp']:UMF_CaptchaTyp); $bCapOk=false; $bCapErr=false;
 if($bCaptcha=UMF_Captcha&&(!(UMF_Nutzerzwang||UMF_TeilnehmerSperre))){
  require_once(UMF_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(UMF_Pfad.UMF_CaptchaPfad,UMF_CaptchaDatei);
  if(isset($_POST['umf_CaptchaCode'])){
   if($Cap->Test($_POST['umf_CaptchaAntwort'],$_POST['umf_CaptchaCode'],$_POST['umf_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(UMF_CaptchaTxFarb,UMF_CaptchaHgFarb);}
 }

 if($bDirekt){ //direkter Aufruf vor den Fragen
  $sAntwort=UMF_Antwort; $sDat=''; //evt. geerbte Werte
  if($_SERVER['REQUEST_METHOD']!='POST'){$Meld=UMF_TxVorVorErfassen; $MTyp='Meld';} //GET
  else{//POST
   if(!UMF_TeilnehmerSperre){
    for($i=1;$i<$nFelder;$i++){
     $s=str_replace(';',',',str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['umf_Tln'.$i])))));
     if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n)); $aDat[$i]=$s;
     if(UMF_Zeichensatz>0) if(UMF_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s); $sDat.=';'.$s;
     if($aPfl[$i]==1&&(strlen($s)<=0||(stristr($aFld[$i],'mail')&&!fUmfIsEMailAdrE($s)))) $aFehl[$i]=true;
    }
    if(UMF_TeilnehmerDSE1) if(isset($_POST['umf_DSE1'])&&$_POST['umf_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(UMF_TeilnehmerDSE2) if(isset($_POST['umf_DSE2'])&&$_POST['umf_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(count($aFehl)==0){ //alles eingetragen
     $Meld=UMF_TxNachVorErfassen; $MTyp='Meld'; $sBtn=UMF_TxWeiter;
     if(UMF_NachRegisterWohin=='Daten'){
      if(empty($sSes)) $aFehl['Dat']=1; //erstmals, nicht weiter
      else{ // Daten vergleichen
       if($a=@file(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses')){
        if(rtrim($a[0])!=substr($sDat,1)){@unlink(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses'); $sSes=''; $aFehl['Ses']=1;}
       }else{$sSes=''; $aFehl['Ses']=1;}
     }}
     if(empty($sSes)){ // Session anlegen
      $nAltZt=time()-(UMF_MaxSessionZeit*3600); $aLsch=array(); //alte temp-Sessions loeschen
      if($f=opendir(UMF_Pfad.'temp')){
       while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(UMF_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
       closedir($f); foreach($aLsch as $s) @unlink(UMF_Pfad.'temp/'.$s);
      }
      $n=(int)substr(UMF_Schluessel,-2); $sSes=rand(10,99).'9'.rand(1000,8888).((time()>>8)+round(UMF_MaxSessionZeit/4)); //n*256sec=120min
      for($i=strlen($sSes)-1;$i>=0;$i--) $n+=(int)substr($sSes,$i,1); $sSes=dechex($n).$sSes;
      if($f=fopen(UMF_Pfad.'temp/'.substr($sSes,0,9).'.ses','w')){fwrite($f,substr($sDat,1)); fclose($f);}
      else{$Meld=str_replace('#','temp/*.ses',UMF_TxDateiRechte); $sSes='';}
     }
     if(count($aFehl)==0&&!empty($sSes)){ // Registrierung fertig
      if(UMF_Registrierung=='vorher'||UMF_Nutzerverwaltung=='vorher') $sAktion=(UMF_NachRegisterWohin!='Auswahl'?'frage':'auswahl'); else{$sAktion='xx'; $sAktion='grafik';}
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
     }
    }else $Meld=UMF_TxEingabeFehl;
   }else $Meld=UMF_TxTeilnehmerSperre;
  }//POST
 }else{ // !$bDirekt includierter Aufruf nach den Fragen
  $sSes=UMF_Session; $sAntwort=UMF_FertigAntwort;
  $Meld=str_replace('#',substr_count($sAntwort,';')+1,UMF_TxVorNachErfassen); $MTyp='Meld';
 }

 if($sAktion=='erfassen'){ //Formularausgabe
  $X="\n".'<p class="umf'.$MTyp.'">'.fUmfTx($Meld).'</p>';
  if(!UMF_TeilnehmerSperre){
   if(UMF_DSEPopUp&&(UMF_TeilnehmerDSE1||UMF_TeilnehmerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.UMF_DSEPopupW.',height='.UMF_DSEPopupH.',left='.UMF_DSEPopupX.',top='.UMF_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
   $X.='
   <form name="umfForm" class="umfForm" action="'.UMF_Self.'" method="post">
   <input type="hidden" name="umf_Aktion" value="'.$sAktion.'" />
   <input type="hidden" name="umf_Session" value="'.$sSes.'" />'.(!UMF_Umfrage?'':'
   <input type="hidden" name="umf_Umfrage" value="'.UMF_Umfrage.'" />').(!defined('UMF_Gespeichert')?'':'
   <input type="hidden" name="umf_Gespeichert" value="'.UMF_Gespeichert.'" />').'
   <input type="hidden" name="umf_Antwort" value="'.$sAntwort.'" />'.rtrim("\n   ".UMF_Hidden).'
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">';
   for($i=1;$i<$nFelder;$i++) $X.="\n".'    <tr class="umfTr">
     <td class="umfLogi">'.fUmfTx(str_replace('`,',';',$aFld[$i])).(empty($aPfl[$i])?'':'*').'</td>
     <td class="umfLogi"><div'.(isset($aFehl[$i])?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_Tln'.$i.'" value="'.(isset($aDat[$i])?$aDat[$i]:'').'" size="25" /></div></td>
    </tr>';
   if(UMF_TeilnehmerDSE1) $X.="\n".'<tr><td class="umfLogi" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE1?'Fehl':'Norm').'">'.fUmfDSEFld(1,$bDSE1).'</div></td></tr>';
   if(UMF_TeilnehmerDSE2) $X.="\n".'<tr><td class="umfLogi" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE2?'Fehl':'Norm').'">'.fUmfDSEFld(2,$bDSE2).'</div></td></tr>';
   if($bCaptcha){ //Captcha-Zeile
    $X.="\n".'    <tr class="umfTr">
     <td class="umfLogi umf15Bs capCell">'.fUmfTx(UMF_TxCaptchaFeld).'</td>
     <td class="umfLogi capCell">
      <input name="umf_CaptchaFrage" type="hidden" value="'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'" />
      <input name="umf_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="umf_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      <span class="capQuest">'.fUmfTx($Cap->Type!='G'?$Cap->Question:UMF_TxCaptchaHilfe).'</span>
      <div'.($bCapErr?' class="umfFehl"':'').'>
       <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.UMF_Url.UMF_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
       <input class="umfLogi capAnsw" name="umf_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" /><span class="umfNoBr">'.(UMF_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxZahlenCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxTextCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').(UMF_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fUmfTx(str_replace('#',UMF_TxGrafikCaptcha,UMF_TxCaptchaNeu)).'">&nbsp;</button>':'').'</span>
      </div>
     </td>
    </tr>';
   }
   $X.='
    <tr class="umfTr">
     <td class="umfLogi"><span class="umfMini">&nbsp;</span></td>
     <td class="umfLogi" style="text-align:right"><span class="umfMini">* '.fUmfTx(UMF_TxPflicht).'</span></td>
    </tr>
   </table>
   <div class="umfScha"><input type="submit" class="umfScha" value="'.fUmfTx($sBtn).'" /></div>
   </form>';
   if($bCaptcha) $X.=fJSCapCode();
  }else{ //TeilnehmerSperre
   $X.='
   <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
    <tr class="umfTr">
     <td class="umfLogi"><p class="umfFehl">'.fUmfTx(UMF_TxTeilnehmerSperre).'</p></td>
    </tr>
   </table>';
  }
 }elseif($sAktion=='frage'){ // nach Erfassen am Anfang
  define('UMF_NeuSession',$sSes);
  include UMF_Pfad.'umfFrage.php'; return fUmfFrage(false);
 }elseif($sAktion=='auswahl'){ // nach Erfassen am Anfang
  define('UMF_NeuSession',$sSes);
  include UMF_Pfad.'umfAuswahl.php'; return fUmfAuswahl(false);
 }else{ //nach den Fragen
  if(!UMF_SQL) $DbO=NULL;
  else if(!isset($DbO)){$DbO=NULL;
   if($DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa)){
    if(!mysqli_connect_errno()){if(defined('UMF_SqlCharSet')&&UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
   }else $Meld=UMF_TxSqlVrbdg;
  }
  if((defined('UMF_Gespeichert')?UMF_Gespeichert:0)){$Meld=UMF_TxAbgestimmt; $MTyp='Erfo';}else{$Meld=UMF_TxGleicheAdresse; $MTyp='Fehl';}
  if(UMF_TeilnehmerLog){include_once UMF_Pfad.'umfTeilnahme.php'; fLogTln((defined('UMF_Gespeichert')?UMF_Gespeichert:0),'T',$sAntwort,$sSes,$DbO);}
  if(UMF_NachAbstimmen=='Fertig'){ //zum Fertigtext
   if(!UMF_FertigHtml){
    if(strlen(UMF_TxFertigText)>0) $X='  <p>'.fUmfBB(fUmfTx(UMF_TxFertigText))."</p>\n";
    if(strlen(UMF_GrafikLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:'').'">'.fUmfTx(UMF_GrafikLink)."</a> ]</span>\n";
    if(strlen(UMF_ZentrumLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').'">'.fUmfTx(UMF_ZentrumLink)."</a> ]</span>\n";
    if(strlen(UMF_NeuAnfangLink)>0) $X.='  <span class="umfNoBr">[ <a class="umfLink" href="'.UMF_Self.'">'.fUmfTx(UMF_NeuAnfangLink)."</a> ]</span>\n";
    if(strlen($X)>0) $X="\n".' <div class="umfFrtg">'."\n".$X." </div>";
   }else{
    if($X=@implode('',file(UMF_Pfad.'umfFertig.inc.htm'))){
     $X=str_replace('{Grafik}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion=grafik'.($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:''),$X);
     $X=str_replace('{Zentrum}',UMF_Self.(strpos(UMF_Self,'?')>0?'&amp;':'?').'umf_Aktion='.(substr($sSes,4,1)!='9'?'zentrum':'auswahl').($sSes?'&amp;umf_Session='.$sSes:'').(UMF_Umfrage?'&amp;umf_Umfrage='.UMF_Umfrage:''),$X);
     $X=str_replace('{Neuanfang}',UMF_Self,$X);
    }else $X=' <p class="umfFehl">Schablone <i>umfFertig.inc.htm</i> fehlt!</p>'."\n";
   }
   $X=(!empty($Meld)?' <p class="umf'.$MTyp.'">'.fUmfTx($Meld)."</p>\n":'').$X;
  }else{include UMF_Pfad.'umfGrafik.php'; $X=fUmfGrafik($Meld,$MTyp);} //zur Grafik
 }
 return $X;
}

function fUmfIsEMailAdrE($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}
function fUmfDSEFld($z,$bCheck=false){
 $s='<a class="umfText" href="'.UMF_DSELink.'"'.(UMF_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(UMF_DSETarget?' target="'.UMF_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fUmfTx($z!=2?UMF_TxDSE1:UMF_TxDSE2)));
 return '<input class="umfCheck" type="checkbox" name="umf_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

if(!function_exists('fJSCapCode')){function fJSCapCode(){
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
   xmlHttpObject.open('get','".UMF_Url."captcha.php?cod='+sTyp+oDate.getTime());
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
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".UMF_Url.UMF_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>";
}}
?>
