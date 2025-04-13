<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Bereinigung','','Idx');

$nAltZt=time()-21600; // temp bereinigen 6 Stunden alt
if($f=opendir(FRA_Pfad.'temp')){
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(FRA_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $sMeld.='<p class="admMeld">'.$n.' Dateien aus dem Ordner <i>temp/</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(FRA_Pfad.'temp/'.$s);
}
if($f=opendir(FRA_Pfad.FRA_CaptchaPfad)){ // captcha bereinigen
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=FRA_CaptchaDatei) if(filemtime(FRA_Pfad.FRA_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $sMeld.='<p class="admMeld">'.$n.' Dateien aus dem Ordner <i>'.FRA_CaptchaPfad.'</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(FRA_Pfad.FRA_CaptchaPfad.$s);
}
if(!$sMeld) $sMeld='<p class="admMeld">Keine Dateien aus den Ordnern <i>'.FRA_CaptchaPfad.'</i> bzw. <i>temp/</i> zu löschen.</p>';

echo '<div style="text-align:center;margin-top:32px;">'.$sMeld.'</div>'.NL.NL;

echo fSeitenFuss();
?>