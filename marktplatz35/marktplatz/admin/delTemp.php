<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Bereinigung');

$nAltZt=time()-(MP_MaxSessionZeit*3600); // temp bereinigen 6 Stunden alt
if($f=opendir(MP_Pfad.'temp')){
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(MP_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $Meld.='<p class="admMeld">'.$n.' Dateien aus <i>temp/</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(MP_Pfad.'temp/'.$s);
}

if($f=opendir(MP_Pfad.MP_CaptchaPfad)){ // captcha bereinigen
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=MP_CaptchaSpeicher) if(filemtime(MP_Pfad.MP_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $Meld.='<p class="admMeld">'.$n.' Dateien aus <i>'.MP_CaptchaPfad.'</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(MP_Pfad.MP_CaptchaPfad.$s);
}
if(!$Meld) $Meld='<p class="admMeld">Keine Dateien aus <i>'.MP_CaptchaPfad.'</i> bzw. <i>temp/</i> zu löschen.</p>';

echo '<div style="text-align:center;margin-top:32px;">'.$Meld.'</div>'.NL.NL;

echo fSeitenFuss();
?>