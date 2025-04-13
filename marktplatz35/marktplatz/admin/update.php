<?php
//Update: wird innerhalb von konfSetup.php aufgerufen

$sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false; $Meld='';
$Ms2='';

//Abschluss
if(fSetzMPWert($mpVersion,'Version',"'")) $bNeu=true;
if($bNeu&&empty($Meld)){ //in mpWerte speichern
 if($f=fopen(MP_Pfad.'mpWerte.php','w')){
  fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte)))."\n"); fclose($f);
  $Meld='<p class="admErfo">Das Update '.$mpVersion.' wurde eingespielt.</p>';
 }else $Meld='<p class="admFehl">In die Datei mpWerte.php durfte nicht geschrieben werden!</p>';
}else if(empty($Meld)) $Meld='<p class="admMeld">Die Einstellungen bleiben unverändert.</p>';


global $sCSS; $sCSS=str_replace("\r",'',trim(@implode('',@file(MPPFAD.'mpStyles.css')))); $bNeu=false;

if($bNeu){ //CSS speichern
 if($f=fopen(MPPFAD.'mpStyles.css','w')){
  fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCSS)))."\n"); fclose($f);
  $Meld.='<p class="admErfo">Auch die Datei <i>mpStyles.css</i> wurde aktualisiert. <span style="color:red">Bitte Browsercache leeren!!</span></p>';
 }else $Meld.='<p class="admFehl">In die Datei <i>mpStyles.css</i> durfte nicht geschrieben werden!</p>';
}

$Meld.=$Ms2;
?>