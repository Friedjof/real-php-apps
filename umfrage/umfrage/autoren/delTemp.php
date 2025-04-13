<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Bereinigung');

$nAltZt=time()-21600; // temp bereinigen 6 Stunden alt
if($f=opendir(UMF_Pfad.'temp')){
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(UMF_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $sMeld.=fMMeld($n.' Dateien aus dem Ordner <i>temp/</i> gelöscht.');
 foreach($aLsch as $s) @unlink(UMF_Pfad.'temp/'.$s);
}
if($f=opendir(UMF_Pfad.UMF_CaptchaPfad)){ // captcha bereinigen
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=UMF_CaptchaDatei) if(filemtime(UMF_Pfad.UMF_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $sMeld.=fMMeld($n.' Dateien aus dem Ordner <i>'.UMF_CaptchaPfad.'</i> gelöscht.');
 foreach($aLsch as $s) @unlink(UMF_Pfad.UMF_CaptchaPfad.$s);
}
if(!$sMeld) $sMeld=fMMeld('Keine Dateien aus den Ordnern <i>'.UMF_CaptchaPfad.'</i> bzw. <i>temp/</i> zu löschen.');

echo '<div style="text-align:center;margin:32px;">'.$sMeld.'</div>'.NL.NL;

echo fSeitenFuss();
?>