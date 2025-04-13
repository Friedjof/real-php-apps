<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Update einspielen','','SUp');

$bPath=true; $bSetUp=true; $bUpdate=false; $usPfad=''; $usWww='';
if(!isset($_SERVER['SCRIPT_FILENAME'])||!($w=$_SERVER['SCRIPT_FILENAME'])) $w=(isset($_SERVER['PATH_TRANSLATED'])?$_SERVER['PATH_TRANSLATED']:'');
$w=str_replace("\\",'/',str_replace("\\\\",'/',$w)); $sPfad=substr($w,0,strrpos($w,'/'));
if(!isset($_SERVER['SCRIPT_NAME'])||!($w=$_SERVER['SCRIPT_NAME'])) $w=(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'');
$sWww=substr($w,0,strrpos($w,'/')); $t=UMFPFAD;
while($p=strpos($t,'./')){$t=substr($t,$p+2); $sPfad=substr($sPfad,0,strrpos($sPfad,'/')); $sWww=substr($sWww,0,strrpos($sWww,'/'));}
if(strlen($t>0)) $t.=(substr($t,-1,1)=='/'?'':'/');
$sPfad.='/'.$t; if(!isset($_SERVER['HTTP_HOST'])||!($w=$_SERVER['HTTP_HOST'])) $w=(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:''); $sWww=$w.$sWww.'/'.$t;

//Aktionen
if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 if(defined('UMF_Version')){
  $usPfad=UMF_Pfad; $usWww=UMF_Www; if($p=strpos($usWww,'://')) $usWww=substr($usWww,$p+3);
 }else $bPath=false;
}elseif($_POST['Btn']=='Update'){
 $usPfad=UMF_Pfad; $usWww=UMF_Www; if($p=strpos($usWww,'://')) $usWww=substr($usWww,$p+3); $bUpdate=true;
}

//Scriptausgaben
if($usWww!=$sWww||$usPfad!=$sPfad){ //Pfade stimmen nicht
 if($bPath) $bSetUp=false;
 $sMeld.='<p class="admFehl">Die eingestellten Pfade scheinen <span style="color:#CC0033;">nicht</span> korrekt zu sein. Führen Sie zuerst das <a href="konfSetup.php">Setup</a> aus. <a href="'.ADU_Hilfe.'LiesMich.htm#2.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
}
echo $sMeld.NL; $sMeld='';

if($bPath){ /*Pfad plausibel*/
 @include(UMFPFAD.'umfVersion.php'); $umfWerteVersion=UMF_Version;
 if($bSetUp&&UMF_Version<$umfVersion||$bUpdate){ //Versionsupdate
  if(file_exists('update.php')){
   $sErfo=''; $sErrDB=''; include('update.php');
   if($sErfo){$umfWerteVersion=$umfVersion; $sMeld.='<p class="admErfo">Das Update <i>'.$umfVersion.'</i> wurde soeben'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' eingepflegt.</p>';}
   else $sMeld.='<p class="admMeld">Es waren keine Update-Änderungen abzuspeichern.</p>';
   $sMeld.=$sErrDB;
  }else $sMeld='<p class="admFehl">Es fehlt das Programm <i>update.php</i>. Die neuesten Änderungen können nicht vorgenommen werden.</p>';
 }else $sMeld='<p class="admMeld">Programmneuerungen (Updates) waren soeben nicht einzupflegen.</p>';
 echo $sMeld.NL;
 echo '<div class="admBox">Versionsdatei <i>umfVersion.php</i> -&gt; Stand '.$umfVersion.'<br />Wertedatei &nbsp; &nbsp; <i>umfWerte.php</i> &nbsp; -&gt; Stand '.$umfWerteVersion.'</div>';
 if($bSetUp){
?>

<form name="updateForm" style="margin-top:40em;" action="konfUpdate.php" method="post">
<div class="admBox">Die Version <?php echo $umfVersion?> scheint bereits korrekt installiert zu sein. Soll dennoch versucht werden, das aktuelle Update <?php echo $umfVersion?> in die Datei <i>umfWerte.php</i> noch einmal einzuspielen?</div>
<p class="admSubmit"><input class="admSubmit" type="submit" name="Btn" value="Update"></p>
</form>

<?php
 }
}else{ /*Pfad unplausibel*/
?>

<div class="admBox">
<p>Das Administrationsscript kann die Datei <i>umfWerte.php</i> im Programmverzeichnis <i>umfrage</i> nicht finden.</p>
<p>Wahrscheinlich stimmt die relative Pfadangabe zum Programmverzeichnis <i>umfrage</i> in der Datei <i>hilfsFunktionen.php</i> im Administrator-Ordner nicht.
Normalerweise muß der Eintrag lauten: <i>UMFPFAD='../';</i>
sofern der Ordner <i>admin</i> ein unmittelbarer Unterordner des Programmordners <i>umfrage</i> ist.</p>
<p>Momentan lautet der Eintrag für den Pfad aber <i><?php echo UMFPFAD?></i>
und verweist somit wahrscheinlich auf einen Ordner <i><?php echo realpath($sPfad)?></i> als angenommenen Programmordner.
Dort gibt es aber keine Datei <i>umfWerte.php</i> oder die Dateirechte sind so gesetzt, dass die Datei nicht lesbar ist.
<?php if(substr(UMFPFAD,-1,1)!='/'){?>Es fehlt auf alle Fälle das / am Ende der Pfadangabe.<?php }?></p>
<p>Falls Sie den Ordner <i>admin</i> an eine andere Stelle verlagert haben,
müssen Sie die Pfadangabe in der Administrations-Datei <i>hilfsFunktionen.php</i> von Hand mit einem Editor so anpassen,
dass diese korrekt auf das Programmverzeichnis <i>umfrage</i> verweist.</p>
<p><?php if(file_exists(UMFPFAD.'umfrage.php')){?>Offensichtlich ist aber tatsächlich die Datei <i>umfWerte.php</i>
im Programmverzeichnis <i>umfrage</i> nicht vorhanden oder nicht lesbar,
denn die Programmdatei <i>umfrage.php</i> ist im angegebenen Verzeichnis vorhanden.<?php }?></p>
</div>

<?php
}
echo fSeitenFuss();?>