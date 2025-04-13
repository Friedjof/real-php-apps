<?php
//Update: wird innerhalb von konfUpdate.php aufgerufen

$aKonf=array(); $h=opendir(UMFPFAD); while($sF=readdir($h)) if(substr($sF,0,8)=='umfWerte'&&substr($sF,8,1)!='0'&&strpos($sF,'.php')>0) $aKonf[]=(int)substr($sF,8); closedir($h); sort($aKonf); if($aKonf[0]==0) $aKonf[0]='';
$sKey=rand(100000,999999); $sErrDB='';
foreach($aKonf as $k=>$sKonf){ //alle Konfigurationen
 $sWerte=str_replace("\r",'',trim(implode('',file(UMFPFAD.'umfWerte'.$sKonf.'.php')))); $bNeu=false;

 if(!strpos($sWerte,'ADU_TeilnahmeExport')){ //TeilnahmeExport 26.11.16
  if($p=strpos($sWerte,"define('ADU_NutzerLaenge'")){
   $sWerte=substr_replace($sWerte,"define('ADU_TeilnahmeExport','1;1;0;0;1;0');\n",$p,0); $bNeu=true;
  }
 }
 if(!strpos($sWerte,'ADU_ErgebnisExport')){ //ErgebnisExport 03.12.16
  if($p=strpos($sWerte,"define('ADU_TeilnahmeLaenge'")){
   $sWerte=substr_replace($sWerte,"define('ADU_ErgebnisExport','1;0;0;1');\n",$p,0); $bNeu=true;
  }
 }
 if(!strpos($sWerte,"define('UMF_DSELink'")){ // 05.05.18 Datenschutzerklaerung
  if($p=strpos($sWerte,"define('UMF_Empfaenger'")){
   $sWerte=substr_replace($sWerte,"define('UMF_DSELink','datenschutz.html');\ndefine('UMF_DSETarget','_blank');\ndefine('UMF_DSEPopUp',false);\ndefine('UMF_DSEPopupW',900);\ndefine('UMF_DSEPopupH',600);\ndefine('UMF_DSEPopupX',5);\ndefine('UMF_DSEPopupY',5);\n",$p,0); $bNeu=true;
  }
  if($p=strpos($sWerte,"define('UMF_TxCaptchaFehl'")) if($p=strpos($sWerte,"\n",$p+1)){
   $sWerte=substr_replace($sWerte,"\ndefine('UMF_TxDSE1',\"Ich habe die [L]Datenschutzerklärung[/L] gelesen und stimme ihr zu.\");\ndefine('UMF_TxDSE2',\"Ich bin mit der Verarbeitung und Speicherung meiner persönlichen Daten im Rahmen der Datenschutzerklärung einverstanden.\");",$p,0); $bNeu=true;
  }
  if($p=strpos($sWerte,"define('UMF_PasswortSenden'")){
   $sWerte=substr_replace($sWerte,"define('UMF_NutzerDSE1',false);\ndefine('UMF_NutzerDSE2',false);\n",$p,0); $bNeu=true;
  }
  if($p=strpos($sWerte,"define('UMF_NachRegisterWohin'")){
   $sWerte=substr_replace($sWerte,"define('UMF_TeilnehmerDSE1',false);\ndefine('UMF_TeilnehmerDSE2',false);\n",$p,0); $bNeu=true;
  }
 }
 if(!strpos($sWerte,"define('SMTP_No_TLS'")){ // 30.05.18 SMTP_No_TLS
  if($p=strpos($sWerte,"define('UMF_SmtpAuth'")){
   $sWerte=substr_replace($sWerte,"if(!defined('SMTP_No_TLS')) define('SMTP_No_TLS',true);\n",$p,0); $bNeu=true;
  }
 }
 if(!strpos($sWerte,'UMF_CSSDatei')){ //spezielle CSS-Datei 09.05.21
  if($p=strpos($sWerte,"define('UMF_Layout'")){
   $sWerte=substr_replace($sWerte,"define('UMF_CSSDatei','umfStyle.css');\n",$p,0); $bNeu=true;
  }
 }
 if(!strpos($sWerte,'UMF_GrafikOhneLogin')){ //Grafik auch ohne Login 03.07.21
  if($p=strpos($sWerte,"define('UMF_GrafikTlnAnz'")) if($p=strpos($sWerte,"\n",$p+1)){
   $sWerte=substr_replace($sWerte,"\ndefine('UMF_GrafikOhneLogin',false);",$p,0); $bNeu=true;
  }
 }

 //Abschluss
 if(fSetzUmfWert($umfVersion,'Version',"'")) $bNeu=true;
 if($bNeu){ //in umfWerte speichern
  if($f=fopen(UMFPFAD.'umfWerte'.$sKonf.'.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte)))."\n"); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
  }else $sMeld.='<p class="admFehl">Feherursache: In die Datei <i>umfWerte'.$sKonf.'.php</i> durfte nicht geschrieben werden (Dateirechteproblem)! <a href="'.ADU_Hilfe.'LiesMich.htm#1.2" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
 }
}//while alle Konfigurationen

$sCSS=str_replace("\r",'',trim(implode('',file(UMFPFAD.'umfStyle.css')))); $bNeu=false;

if($p=strpos($sCSS,"div.umfText{")){ // Textblock umbenennen
 $sCSS=substr_replace($sCSS,"div.umfTxBl",$p,11); $bNeu=true;
}

if($bNeu){ //CSS speichern
 if($f=fopen(UMFPFAD.'umfStyle.css','w')){
  fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCSS)))."\n"); fclose($f);
  $sMeld.=fMErfo('Die Style-Datei <i>umfStyle.css</i> wurde aktualisiert.');
 }else $sMeld.=fMFehl('In die Datei umfStyle.css durfte nicht geschrieben werden!');
}
?>