<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Bereinigung','','Idx');

$nAltZt=time()-21600; // temp bereinigen 6 Stunden alt
if($f=opendir(KAL_Pfad.'temp')){
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') if(filemtime(KAL_Pfad.'temp/'.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $Msg.='<p class="admMeld">'.$n.' Dateien aus dem Ordner <i>temp/</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(KAL_Pfad.'temp/'.$s);
}
if($f=opendir(KAL_Pfad.KAL_CaptchaPfad)){ // captcha bereinigen
 $aLsch=array();
 while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html'&&$s!=KAL_CaptchaSpeicher) if(filemtime(KAL_Pfad.KAL_CaptchaPfad.$s)<$nAltZt) $aLsch[]=$s;
 closedir($f);
 if($n=count($aLsch)) $Msg.='<p class="admMeld">'.$n.' Dateien aus dem Ordner <i>'.KAL_CaptchaPfad.'</i> gelöscht.</p>';
 foreach($aLsch as $s) @unlink(KAL_Pfad.KAL_CaptchaPfad.$s);
}
if(!$Msg) $Msg='<p class="admMeld">Keine Dateien aus den Ordnern <i>'.KAL_CaptchaPfad.'</i> bzw. <i>temp/</i> zu löschen.</p>';

echo '<div style="text-align:center;margin-top:32px;">'.$Msg.'</div>'.NL.NL;

echo fSeitenFuss();
?>