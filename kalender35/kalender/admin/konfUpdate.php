<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Update einspielen','','KUp');

$bPath=true; $bSetUp=true; $bUpdate=false; $ksPfad=''; $ksWww='';
if(!isset($_SERVER['SCRIPT_FILENAME'])||!($w=$_SERVER['SCRIPT_FILENAME'])) $w=(isset($_SERVER['PATH_TRANSLATED'])?$_SERVER['PATH_TRANSLATED']:'');
$w=str_replace("\\",'/',str_replace("\\\\",'/',$w)); $sPfad=substr($w,0,strrpos($w,'/'));
if(!isset($_SERVER['SCRIPT_NAME'])||!($w=$_SERVER['SCRIPT_NAME'])) $w=(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'');
$sWww=substr($w,0,strrpos($w,'/')); $t=KALPFAD;
while($p=strpos($t,'./')){$t=substr($t,$p+2); $sPfad=substr($sPfad,0,strrpos($sPfad,'/')); $sWww=substr($sWww,0,strrpos($sWww,'/'));}
if(strlen($t>0)) $t.=(substr($t,-1,1)=='/'?'':'/');
$sPfad.='/'.$t; if(!isset($_SERVER['HTTP_HOST'])||!($w=$_SERVER['HTTP_HOST'])) $w=(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:''); $sWww=$w.$sWww.'/'.$t;

//Aktionen
if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 if(defined('KAL_Version')){
  $ksPfad=KAL_Pfad; $ksWww=KAL_Www; if($p=strpos($ksWww,'://')) $ksWww=substr($ksWww,$p+3);
 }else $bPath=false;
}elseif($_POST['Btn']=='Update'){
 $ksPfad=KAL_Pfad; $ksWww=KAL_Www; if($p=strpos($ksWww,'://')) $ksWww=substr($ksWww,$p+3); $bUpdate=true;
}

//Scriptausgaben
if($ksWww!=$sWww||$ksPfad!=$sPfad){ //Pfade stimmen nicht
 if($bPath) $bSetUp=false;
 $Msg.='<p class="admFehl">Die eingestellten Pfade scheinen <span style="color:#CC0033;">nicht</span> korrekt zu sein. Führen Sie zuerst das <a href="konfSetup.php">Setup</a> aus. <a href="'.ADM_Hilfe.'LiesMich.htm#2.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
}
echo $Msg.NL; $Msg='';

if($bPath){ /*Pfad plausibel*/
 @include(KALPFAD.'kalVersion.php'); $kalWerteVersion=KAL_Version;
 if($bSetUp&&KAL_Version<$kalVersion||$bUpdate){ //Versionsupdate
  if(file_exists('update.php')){
   include('update.php');
  }else $Msg='<p class="admFehl">Es fehlt das Programm <i>update.php</i>. Die neuesten Änderungen können nicht vorgenommen werden.</p>';
 }else $Msg='<p class="admMeld">Programmneuerungen (Updates) waren soeben nicht einzupflegen.</p>';
 echo $Msg.NL;
 echo '<div class="admBox">Versionsdatei <i>kalVersion.php</i> -&gt; Stand '.$kalVersion.'<br />Wertedatei &nbsp; &nbsp; <i>kalWerte.php</i> &nbsp; -&gt; Stand '.$kalWerteVersion.'</div>';
 if($bSetUp){
?>

<form name="updateForm" style="margin-top:40em;" action="konfUpdate.php" method="post">
<div class="admBox">Die Version <?php echo $kalVersion?> scheint bereits korrekt installiert zu sein. Soll dennoch versucht werden, das aktuelle Update <?php echo $kalVersion?> in die Datei <i>kalWerte.php</i> noch einmal einzuspielen?<br /><span class="admMini"><u>Information</u>: Die Datei <i>admin/update.php</i> mit den Aktualisierungsanweisungen wurde zuletzt am <?php echo date('d.m.Y',@filemtime('./update.php'));?> hochgeladen.</span></div>
<p class="admSubmit"><input class="admSubmit" type="submit" name="Btn" value="Update"></p>
</form>

<?php
 }
}else{ /*Pfad unplausibel*/
?>

<div class="admBox">
<p>Das Administrationsscript kann die Datei <i>kalWerte.php</i> im Programmverzeichnis <i>kalender</i> nicht finden.</p>
<p>Wahrscheinlich stimmt die relative Pfadangabe zum Programmverzeichnis <i>kalender</i> in der Datei <i>hilfsFunktionen.php</i> im Administrator-Ordner nicht.
Normalerweise muß der Eintrag lauten: <i>KALPFAD='../';</i>
sofern der Ordner <i>admin</i> ein unmittelbarer Unterordner des Programmordners <i>kalender</i> ist.</p>
<p>Momentan lautet der Eintrag für den Pfad aber <i><?php echo KALPFAD?></i>
und verweist somit wahrscheinlich auf einen Ordner <i><?php echo realpath($sPfad)?></i> als angenommenen Programmordner.
Dort gibt es aber keine Datei <i>kalWerte.php</i> oder die Dateirechte sind so gesetzt, dass die Datei nicht lesbar ist.
<?php if(substr(KALPFAD,-1,1)!='/'){?>Es fehlt auf alle Fälle das / am Ende der Pfadangabe.<?php }?></p>
<p>Falls Sie den Ordner <i>admin</i> an eine andere Stelle verlagert haben,
müssen Sie die Pfadangabe in der Administrations-Datei <i>hilfsFunktionen.php</i> von Hand mit einem Editor so anpassen,
dass diese korrekt auf das Programmverzeichnis <i>kalender</i> verweist.</p>
<p><?php if(file_exists(KALPFAD.'kalender.php')){?>Offensichtlich ist aber tatsächlich die Datei <i>kalWerte.php</i>
im Programmverzeichnis <i>kalender</i> nicht vorhanden oder nicht lesbar,
denn die Programmdatei <i>kalender.php</i> ist im angegebenen Verzeichnis vorhanden.<?php }?></p>
</div>

<?php
}
echo fSeitenFuss();
?>