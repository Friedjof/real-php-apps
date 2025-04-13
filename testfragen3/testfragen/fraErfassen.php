<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){return fFraErfassen(true);}
}

function fFraErfassen($bDirekt){ //Seiteninhalt
 $sAktion='erfassen'; $sSes=FRA_Session; $sBtn=FRA_TxEintragen;
 $aFld=explode(';',';'.FRA_TeilnehmerFelder); $nFelder=count($aFld); $aPfl=explode(';',';'.FRA_TeilnehmerPflicht);
 $X=''; $aDat=array(); $aFehl=array(); $Meld=''; $MTyp='Fehl'; $bCaptcha=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;

 //Captcha behandeln
 $sCapTyp=(isset($_POST['fra_CaptchaTyp'])?$_POST['fra_CaptchaTyp']:FRA_CaptchaTyp); $bCapOk=false; $bCapErr=false;
 if($bCaptcha=FRA_Captcha&&(!(FRA_Nutzerzwang||FRA_TeilnehmerSperre))){
  require_once(FRA_Pfad.'class'.(phpversion()>'5.3'?'':'4').'.captcha'.$sCapTyp.'.php'); $Cap=new Captcha(FRA_Pfad.FRA_CaptchaPfad,FRA_CaptchaDatei);
  if(isset($_POST['fra_CaptchaCode'])){
   if($Cap->Test($_POST['fra_CaptchaAntwort'],$_POST['fra_CaptchaCode'],$_POST['fra_CaptchaFrage'])) $bCapOk=true; else{$bCapErr=true; $aFehl[0]=true;}
  }else{if($sCapTyp!='G') $Cap->Generate(); else $Cap->Generate(FRA_CaptchaTxFarb,FRA_CaptchaHgFarb);}
 }

 if($bDirekt){//direkter Aufruf
  $sAntwort=FRA_Antwort; $sVerlauf=FRA_Verlauf; $sZeit=(defined('FRA_Zeit')?FRA_Zeit:''); $sDat=''; //evt. geerbte Werte
  if($_SERVER['REQUEST_METHOD']!='POST'){$Meld=FRA_TxVorVorErfassen; $MTyp='Meld';} //GET
  else{//POST
   if(!FRA_TeilnehmerSperre){
    for($i=1;$i<$nFelder;$i++){
     $s=str_replace(';',',',str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['fra_Tln'.$i])))));
     if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n)); $aDat[$i]=$s;
     if(FRA_Zeichensatz>0) if(FRA_Zeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//IGNORE',$s); else $s=html_entity_decode($s); $sDat.=';'.$s;
     if($aPfl[$i]==1&&(strlen($s)<=0||(stristr($aFld[$i],'mail')&&!fFraIsEMailAdrE($s)))) $aFehl[$i]=true;
    }
    if(FRA_TeilnehmerDSE1) if(isset($_POST['fra_DSE1'])&&$_POST['fra_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
    if(FRA_TeilnehmerDSE2) if(isset($_POST['fra_DSE2'])&&$_POST['fra_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
    if(count($aFehl)==0){//alles eingetragen
     $nAltZt=time()-(FRA_MaxSessionZeit*3600); //alte temp-Sessions loeschen
     if($f=opendir(FRA_Pfad.'temp')){
      $aLsch=array();
      while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(FRA_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
      closedir($f); foreach($aLsch as $s) @unlink(FRA_Pfad.'temp/'.$s);
     }
     $n=(int)substr(FRA_Schluessel,-2); $sSes=rand(10,99).'9'.rand(1000,8888).((time()>>8)+round(FRA_MaxSessionZeit/4)); //n*256sec=120min
     for($i=strlen($sSes)-1;$i>=0;$i--) $n+=(int)substr($sSes,$i,1); $sSes=dechex($n).$sSes;
     if($f=fopen(FRA_Pfad.'temp/'.substr($sSes,0,9).'.ses','w')){
      fwrite($f,substr($sDat,1)); fclose($f); $Meld=FRA_TxNachVorErfassen; $MTyp='Meld'; $sBtn=FRA_TxWeiter;
      if(FRA_Registrierung=='vorher'||FRA_Nutzerverwaltung=='vorher') $sAktion='frage'; else $sAktion='bewerten';
      if($bCaptcha){$Cap->Delete(); $bCaptcha=false;} //Captcha loeschen
     }else $Meld=str_replace('#','temp/*.ses',FRA_TxDateiRechte);
    }else{
     $Meld=FRA_TxEingabeFehl;
     if(isset($_POST['fra_Start'])&&($s=(int)$_POST['fra_Start'])) $sZeit.='" /><input type="hidden" name="fra_Start" value="'.$s;
    }
   }else $Meld=FRA_TxTeilnehmerSperre;
  }//POST
 }else{ //includierter Aufruf wenn Fragen fertig
  $sSes=FRA_Session; $sAntwort=FRA_FertigAntwort; $sVerlauf=FRA_FertigVerlauf;
  $sZeit=time()-(int)FRA_Zeit; if($sZeit<3600) $sZeit=date('i:s',$sZeit); else $sZeit=date('H:i:s',$sZeit);
  $sZeit.='" /><input type="hidden" name="fra_Start" value="'.FRA_Zeit;
  $Meld=str_replace('#',substr_count($sAntwort,';')+1,FRA_TxVorNachErfassen); $MTyp='Meld';
 }

 if($sAktion=='erfassen'||($sAktion=='frage'&&FRA_NachRegisterWohin=='Daten')){ //Formularausgabe
  if($bCaptcha) $X="
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
   oForm=oFrm; oForm.elements['fra_CaptchaTyp'].value=sTyp; oDate=new Date();
   xmlHttpObject.open('get','".FRA_Http."captcha.php?cod='+sTyp+oDate.getTime());
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
    oForm.elements['fra_CaptchaCode'].value=sResponse.substr(1,32);
    if(sResponse.substr(0,1)!='G'){
     oForm.elements['fra_CaptchaFrage'].value=sQuestion;
     aSpans[nImgId].innerHTML='';
    }else{
     oForm.elements['fra_CaptchaFrage'].value='".FRA_TxCaptchaHilfe."';
     aSpans[nImgId].innerHTML='<img class=\"capImg\" src=\"".FRA_Http.FRA_CaptchaPfad."'+sQuestion+'\" width=\"120\" height=\"24\" border=\"0\" />';
    }
 }}}
</script>\n";

  $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>';

  if(!FRA_TeilnehmerSperre){
  if(FRA_DSEPopUp&&(FRA_TeilnehmerDSE1||FRA_TeilnehmerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.FRA_DSEPopupW.',height='.FRA_DSEPopupH.',left='.FRA_DSEPopupX.',top='.FRA_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
  $X.='
  <form name="fraForm" class="fraForm" action="'.FRA_Self.'" method="post">
   <input type="hidden" name="fra_Aktion" value="'.$sAktion.'" />
   <input type="hidden" name="fra_Session" value="'.$sSes.'" />
   <input type="hidden" name="fra_Antwort" value="'.$sAntwort.'" />
   <input type="hidden" name="fra_Verlauf" value="'.$sVerlauf.'" />
   <input type="hidden" name="fra_ProSeite" value="'.(isset($_POST['fra_ProSeite'])?$_POST['fra_ProSeite']:'').'" />
   <input type="hidden" name="fra_Folgename" value="'.FRA_TestFolgeName.'" />
   <input type="hidden" name="fra_Kategorie" value="'.FRA_TestKategorie.'" />
   <input type="hidden" name="fra_TestZeit" value="'.FRA_TestZeit.'" />
   <input type="hidden" name="fra_Zeit" value="'.$sZeit.'" />'.rtrim("\n   ".FRA_Hidden).'
  <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">';

  for($i=1;$i<$nFelder;$i++) $X.="\n".'   <tr>
    <td class="fraLogi">'.fFraTx(str_replace('`,',';',$aFld[$i])).(empty($aPfl[$i])?'':'*').'</td>
    <td class="fraLogi"><div'.(isset($aFehl[$i])?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_Tln'.$i.'" value="'.(isset($aDat[$i])?$aDat[$i]:'').'" size="25" /></div></td>
   </tr>';
  if(FRA_TeilnehmerDSE1) $X.="\n".'<tr><td class="fraLogi" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE1?'Fehl':'Norm').'">'.fFraDSEFld(1,$bDSE1).'</div></td></tr>';
  if(FRA_TeilnehmerDSE2) $X.="\n".'<tr><td class="fraLogi" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE2?'Fehl':'Norm').'">'.fFraDSEFld(2,$bDSE2).'</div></td></tr>';
  if($bCaptcha){ //Captcha-Zeile
   $X.='
   <tr>
    <td class="fraLogi capCell" style="padding-top:6px;vertical-align:top;">'.fFraTx(FRA_TxCaptchaFeld).'</td>
    <td class="fraLogi capCell">
     <input class="fraLogi capQuest" name="fra_CaptchaFrage" type="text" value="'.fFraTx($Cap->Type!='G'?$Cap->Question:FRA_TxCaptchaHilfe).'" />
     <div'.($bCapErr?' class="fraFehl"':'').' style="white-space:nowrap">
      <span class="capImg">'.($Cap->Type!='G'||$bCapOk?'':'<img class="capImg" src="'.FRA_Http.FRA_CaptchaPfad.$Cap->Question.'" width="120" height="24" border="0" />').'</span>
      <input class="fraLogi capAnsw" name="fra_CaptchaAntwort" type="text" value="'.(isset($Cap->PrivateKey)?$Cap->PrivateKey:'').'" size="15" />
      <input name="fra_CaptchaCode" type="hidden" value="'.$Cap->PublicKey.'" />
      <input name="fra_CaptchaTyp" type="hidden" value="'.$Cap->Type.'" />
      '.(FRA_CaptchaNumerisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'N'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxZahlenCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(FRA_CaptchaTextlich?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'T'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxTextCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
      '.(FRA_CaptchaGrafisch?'<button type="button" class="capReload" onclick="reCaptcha(this.form,'."'G'".');return false;" title="'.fFraTx(str_replace('#',FRA_TxGrafikCaptcha,FRA_TxCaptchaNeu)).'">&nbsp;</button>':'').'
     </div>
    </td>
   </tr>';
  }
  $X.='
   <tr>
    <td class="fraLogi"><span class="fraMini">&nbsp;</span></td>
    <td class="fraLogi" style="text-align:right;"><span class="fraMini">* '.fFraTx(FRA_TxPflicht).'</span></td>
   </tr>
  </table>
  <p><input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx($sBtn).'" /></p>
  </form>';
  }else{
  $X.='
  <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td class="fraLogi"><p class="fraFehl">'.fFraTx(FRA_TxTeilnehmerSperre).'</p></td>
   </tr>
  </table>';
  }
 }elseif($sAktion=='frage'){
  define('FRA_NeuSession',$sSes);
  if(FRA_NachRegisterWohin=='Zentrum'){include FRA_Pfad.'fraTestAuswahl.php'; return fFraTestAuswahl(false);}
  else{include FRA_Pfad.'fraFrage.php'; return fFraFrage(false);} //Fragen
 }else{//nach den Fragen sind die Daten fertig
  define('FRA_FertigSession',$sSes); include(FRA_Pfad.'fraBewerten.php'); $X=fFraBewerten(false);
 }
 return $X;
}

function fFraDSEFld($z,$bCheck=false){
 $s='<a class="fraText" href="'.FRA_DSELink.'"'.(FRA_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(FRA_DSETarget?' target="'.FRA_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fFraTx($z!=2?FRA_TxDSE1:FRA_TxDSE2)));
 return '<input class="fraCheck" type="checkbox" name="fra_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

function fFraIsEMailAdrE($sTx){
 return preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sTx));
}
?>