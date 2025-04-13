<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Setup ausführen','','SSu');

$bSetUp=true; $bPath=true; $usPfad=''; $usWww='';
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
  $sMeld='<p class="admMeld">Überprüfen Sie die Pfad-Einstellungen für das Umfrage-Script.</p>';
 }else $bPath=false;
}elseif($_POST['Btn']=='Eintragen'){
 $v=txtVar('Pfad'); if(substr($v,-1,1)!='/') $v.='/'; $sErfo='';
 $w=txtVar('Www');  if(substr($w,-1,1)!='/') $w.='/'; if($p=strpos($w,'://')) $w=substr($w,$p+3);
 $aKonf=array();$h=opendir(UMFPFAD); while($sF=readdir($h)) if(substr($sF,0,8)=='umfWerte'&&substr($sF,8,1)!='0'&&strpos($sF,'.php')>0) $aKonf[]=(int)substr($sF,8); closedir($h); sort($aKonf); if($aKonf[0]==0) $aKonf[0]='';
 foreach($aKonf as $k=>$sKonf){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMFPFAD.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  if(fSetzUmfWert($v,'Pfad',"'")) $bNeu=true; else $usPfad=$v;
  if(fSetzUmfWert($w,'Www',"'")) $bNeu=true; else $usWww=$w;
  if(UMF_Schluessel<='00'){
   $usKey=substr(time(),-6); if(fSetzUmfWert($usKey,'Schluessel',"'")) $bNeu=true;
   if(!strpos($sWerte,'//Schluessel:')) if($p=strpos($sWerte,"define('UMF_Schluessel'")) if($p=strpos($sWerte,"\n",$p+1)) $sWerte=substr_replace($sWerte,' //Schluessel: '.$usKey,$p,0);
  }
  if($bNeu){//Speichern
   if($f=fopen(UMFPFAD.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">Keine Berechtigung zum Schreiben in die Datei <i>umfWerte'.$sKonf.'.php</i>!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Pfad-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Pfad-Einstellungen bleiben unverändert.</p>';
}

//Scriptausgaben
if($usWww==$sWww&&$usPfad==$sPfad){ //Pfade stimmen
 if($sMeld=='') $sMeld='<p class="admMeld">Momentan sind die folgenden Pfade eingestellt, die sehr wahrscheinlich korrekt sind.</p>';
}else{ //Pfade abweichend
 $bSetUp=false;
 $sMeld.='<p class="admFehl">Die eingestellten Pfade scheinen <span style="color:#CC0033;">nicht</span> korrekt zu sein. Führen Sie das nachfolgende Setup aus. <a href="'.ADU_Hilfe.'LiesMich.htm#2.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
}
echo $sMeld.NL; $sMeld='';

if($bPath){ /*Pfad plausibel*/
 if($bSetUp){
  @include(UMFPFAD.'umfVersion.php');
  if(UMF_Version<$umfVersion){ //Versionsupdate
   $sMeld='<p class="admMeld">Es sind noch Programmneuerungen (<a href="konfUpdate.php">Update</a>) einzupflegen.</p>';
  }else $sMeld='<p class="admMeld">Programmneuerungen (Updates) sind aktuell nicht einzupflegen.</p>';
  echo $sMeld.NL;
 }
?>

<form name="setupForm" action="konfSetup.php" method="post">
<table class="admTabl" style="table-layout:fixed;width:100%" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td style="width:6em"><img src="<?php echo UMFPFAD?>pix.gif" width="1" height="1" border="0" alt=""></td>
 <td><img src="<?php echo UMFPFAD?>pix.gif" width="1" height="1" border="0" alt=""></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die wahrscheinliche Adresse zum Umfrage-Script wurde als <span style="color:#0055AA"><i><?php echo $sWww?></i></span> ermittelt.<br />
In der Datei <i>umfWerte.php</i> ist momentan <i><?php echo $usWww?$usWww:'nichts' ?></i> eingetragen.
Bitte stellen Sie gegebenenfalls die tatsächlich zutreffende Adresse zum Programmverzeichnis des Umfrage-Scripts ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:6em">Adresse</td>
 <td><div style="float:right;padding-top:3px"><img src="iconKopie.gif" onclick="document.setupForm.Www.value='<?php echo $sWww?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sWww?> übernehmen"></div>
 <div style="width:97%"><input type="text" name="Www" value="<?php echo $usWww?>" style="width:98%" /></div>
 <div class="admMini" style="clear:both;">(ohne <i>http://</i>)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Der wahrscheinliche physische Pfad zum Umfrage-Script wurde als <span style="color:#0055AA"><i><?php echo $sPfad?></i></span> ermittelt.<br />
In der Datei <i>umfWerte.php</i> ist momentan <i><?php echo $usPfad?$usPfad:'nichts' ?></i> eingetragen.
Bitte stellen Sie gegebenenfalls den tatsächlich zutreffenden physischen Pfad zum Programmverzeichnis des Umfrage-Scripts ein, beginnend im Wurzelverzeichnis des Servers.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:6em">Adresse</td>
 <td><div style="float:right;padding-top:3px"><img src="iconKopie.gif" onclick="document.setupForm.Pfad.value='<?php echo $sPfad?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sPfad?> übernehmen"></div>
 <div style="width:97%"><input type="text" name="Pfad" value="<?php echo $usPfad?>" style="width:98%" /></div></td>
</tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Eintragen"></p>
</form>

<?php
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