<?php
if(file_exists('hilfsFunktionen.php')) include 'hilfsFunktionen.php';
echo fSeitenKopf('Setup ausführen','','KSu');

$bSetUp=true; $bPath=true; $mpPfad=''; $mpPfadI=''; $mpWww=''; $mpDocRoot=''; $mpRelPfad='';
if(!isset($_SERVER['SCRIPT_FILENAME'])||!($w=$_SERVER['SCRIPT_FILENAME'])) $w=(isset($_SERVER['PATH_TRANSLATED'])?$_SERVER['PATH_TRANSLATED']:'');
$w=str_replace("\\",'/',str_replace("\\\\",'/',$w)); $sPfad=substr($w,0,strrpos($w,'/'));
if(!isset($_SERVER['SCRIPT_NAME'])||!($w=$_SERVER['SCRIPT_NAME'])) $w=(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'');
$sWww=substr($w,0,strrpos($w,'/')); $t=MPPFAD;
while($p=strpos($t,'./')){$t=substr($t,$p+2); $sPfad=substr($sPfad,0,strrpos($sPfad,'/')); $sWww=substr($sWww,0,strrpos($sWww,'/'));}
if(strlen($t>0)) $t.=(substr($t,-1,1)=='/'?'':'/');
$sPfad.='/'.$t; if(!isset($_SERVER['HTTP_HOST'])||!($w=$_SERVER['HTTP_HOST'])) $w=(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:''); $sWww=$w.$sWww.'/'.$t;

//Aktionen
if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 if(defined('MP_Version')){
  $mpPfad=MP_Pfad; $mpPfadI=MP_Pfad; $mpWww=MP_Www; if($p=strpos($mpWww,'://')) $mpWww=substr($mpWww,$p+3);
  if(defined('MP_DocRoot')){ // neues Setup von 2021 schon eingetragen
   $mpDocRoot=MP_DocRoot; $mpRelPfad=MP_RelPfad;
   if($mpDocRoot&&$mpRelPfad){$mpPfad=$_SERVER[$mpDocRoot].$mpRelPfad; $mpPfadI='$_'."SERVER['".$mpDocRoot."'].'".$mpRelPfad."'";}
  }else{ // Setup beim 1 mal updaten
   if(file_exists(MPPFAD.'mpWerte.php')&&is_writable(MPPFAD.'mpWerte.php')){
    $sWerte=str_replace("\r",'',trim(implode('',file(MPPFAD.'mpWerte.php'))));
    if($p=strpos($sWerte,"define('MP_Daten'")){
     $sWerte=substr_replace($sWerte,"define('MP_DocRoot','');\ndefine('MP_RelPfad','');\n",$p,0);
     if($f=fopen(MPPFAD.'mpWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
     }else $Meld='<p class="admFehl">In die Datei <i>mpWerte.php</i> konnte nicht geschrieben werden!</p>';
    }else $Meld='<p class="admFehl">Die Datei <i>mpWerte.php</i> entspricht nicht der Originalstruktur!</p>';
   }else $Meld='<p class="admFehl">Die Datei <i>mpWerte.php</i> war soeben nicht beschreibbar!</p>';
  }
  $Meld='<p class="admMeld">Überprüfen Sie die Pfad-Einstellungen für das Marktplatz-Script.</p>';
 }else $bPath=false;
}elseif($_POST['Btn']=='Eintragen'){
 $sWerte=str_replace("\r",'',trim(implode('',file(MPPFAD.'mpWerte.php')))); $bNeu=false; $mpDocRoot=MP_DocRoot; $mpRelPfad=MP_RelPfad; $sErfo='';
 $w=txtVar('Www'); if(substr($w,-1,1)!='/') $w.='/'; if($p=strpos($w,'://')) $w=substr($w,$p+3);
 if(fSetzMpWert($w,'Www',"'")) $bNeu=true; else $mpWww=$w;
 $mpDocRoot=txtVar('DocRoot');
 if($mpDocRoot==''||isset($_SERVER[$mpDocRoot])){
  if(fSetzMpWert($mpDocRoot,'DocRoot',"'")) $bNeu=true;
 }else $Meld='<p class="admFehl">Die Servervariable <i>'.$mpDocRoot.'</i> existiert nicht!</p>';
 if($p=txtVar('RelPfad')){if(substr($p,-1,1)!='/') $p.='/'; if(substr($p,0,1)!='/') $p='/'.$p;} $mpRelPfad=$p;
 if(fSetzMpWert($p,'RelPfad',"'")) $bNeu=true;
 $v=txtVar('Pfad'); if(substr($v,-1,1)!='/') $v.='/'; $mpPfad=$v;
 if($mpDocRoot&&$p&&isset($_SERVER[$mpDocRoot])){ // MP_Pfad speziell speichern
  if($n=strpos($sWerte,"define('MP_Pfad'")){
   $m=strpos($sWerte,");",$n); $n+=17;
   $sWerte=substr_replace($sWerte,'$_'."SERVER['".$mpDocRoot."'].'".$p."'",$n,$m-$n);
   $mpPfad=$_SERVER[$mpDocRoot].$p;
  }
 }else if(fSetzMpWert($v,'Pfad',"'")) $bNeu=true; // MP_Pfad klassisch speichern

 $mpPfadI=$mpPfad;
 if($mpDocRoot&&$mpRelPfad&&($n=strpos($sWerte,"define('MP_Pfad'"))){
  $m=strpos($sWerte,");",$n); $n+=17; $mpPfadI=substr($sWerte,$n,$m-$n);
 }

 if(MP_Schluessel<='00'){
  $mpKey=substr(time(),-6); if(fSetzMpWert($mpKey,'Schluessel',"'")) $bNeu=true;
  if(!strpos($sWerte,'//Schluessel:')) if($p=strpos($sWerte,"define('MP_Schluessel'")) if($p=strpos($sWerte,"\n",$p+1)) $sWerte=substr_replace($sWerte,' //Schluessel: '.$mpKey,$p,0);
 }
 if(($p=strpos($sWerte,'CAPTCHA_SALT'))&&($q=strpos($sWerte,"\n",$p))){
  $sWerte=substr_replace($sWerte,"CAPTCHA_SALT','".chr(rand(65,90)).substr(time(),-5)."');",$p,$q-$p);
 }
 if($bNeu){//Speichern
  if(file_exists(MPPFAD.'mpWerte.php')&&is_writable(MPPFAD.'mpWerte.php')&&($f=fopen(MPPFAD.'mpWerte.php','w'))){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='<p class="admErfo">Die Pfadeinstellungen wurden gespeichert.</p>';
  }else $Meld.='<p class="admFehl">In die Datei <i>mpWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Meld.='<p class="admMeld">Die Pfadeinstellungen bleiben unverändert.</p>';
}
//Scriptausgaben
if($mpWww==$sWww&&$mpPfad==$sPfad){ //Pfade stimmen
 if($Meld=='') $Meld='<p class="admMeld">Momentan sind die folgenden Pfade eingestellt, die sehr wahrscheinlich korrekt sind.</p>';
}else{ //Pfade abweichend
 $bSetUp=false;
 $Meld.='<p class="admFehl">Die eingestellten Pfade scheinen <span style="color:#CC0033;">nicht</span> korrekt zu sein. Führen Sie das nachfolgende Setup aus. <a href="'.(defined('AM_Hilfe')?AM_Hilfe:'').'LiesMich.htm#2.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
}
echo $Meld.NL; $Meld='';

if($bPath){ /*Pfad plausibel*/
 if($bSetUp){
  $mpVersion='???'; if(file_exists(MPPFAD.'mpVersion.php')) @include(MPPFAD.'mpVersion.php');
  if(MP_Version<$mpVersion){ //Versionsupdate
   $Meld='<p class="admMeld">Es sind noch Programmneuerungen (<a href="konfUpdate.php">Update</a>) einzupflegen.</p>';
  }else $Meld='<p class="admMeld">Programmneuerungen (Updates) sind aktuell nicht einzupflegen.</p>';
  echo $Meld.NL;
 }
?>

<form name="SetupForm" action="konfSetup.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td colspan="2" class="admSpa2">Die wahrscheinliche Adresse zum Marktplatz-Script wurde als <br><span style="color:#0055AA"><i><?php echo $sWww?></i></span> ermittelt.<br>
 In der Datei <i>mpWerte.php</i> ist momentan <br><i><?php echo $mpWww?$mpWww:'nichts' ?></i> eingetragen.
 Bitte tragen Sie gegebenenfalls die tatsächlich zutreffende Adresse zum Programmverzeichnis des Marktplatz-Scripts ein.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Webadresse</td>
 <td>
  <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-right:2px;"><input type="text" name="Www" value="<?php echo $mpWww?>" style="width:99%" /></td><td width="18" align="right"><img src="iconKopie.gif" onclick="document.SetupForm.Www.value='<?php echo $sWww?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sWww?> übernehmen"></td></tr></table>
  <div class="admMini" style="clear:both;">(ohne <i>http://</i>)</div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Der wahrscheinliche physische Pfad zum Marktplatz-Script
im Dateisystem des Servers wurde als <br><span style="color:#0055AA"><i><?php echo $sPfad?></i></span> ermittelt.<br />
In der Datei <i>mpWerte.php</i> ist momentan <br><i><?php echo $mpPfadI?$mpPfadI:'nichts' ?></i> eingetragen.
Bitte stellen Sie gegebenenfalls den tatsächlich zutreffenden physischen Pfad
zum Programmverzeichnis des Marktplatz-Scripts ein,
beginnend im Wurzelverzeichnis des Servers.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Serverpfad</td>
 <td><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-right:2px;"><input type="text" name="Pfad" value="<?php echo $mpPfad?>" style="width:99%" /></td><td width="18" align="right"><img src="iconKopie.gif" onclick="document.SetupForm.Pfad.value='<?php echo $sPfad?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sPfad?> übernehmen"></td></tr></table></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei einigen Providern ist ein statischer Serverpfad wie oben ermittelt und eingestellt nicht gewünscht.
Statt dessen soll dort eine Kombination aus einer Server-Variablen und einem relativen Pfad genutzt werden.
Tragen Sie in einem solchen Fall diese Kombination ein. Anderenfalls lassen Sie die folgenden 2 Felder leer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br />Serverpfad</td>
 <td>
  <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
   <td style="padding-right:2px;width:40%"><input type="text" name="DocRoot" value="<?php echo $mpDocRoot?>" style="width:95%" /></td>
   <td style="padding-right:2px;"><input type="text" name="RelPfad" value="<?php echo $mpRelPfad?>" style="width:98%" /></td>
  </tr><tr>
   <td style="padding-right:2px;"><div class="admMini">Servervariable meist: <i>DOCUMENT_ROOT</i></div></td>
   <td style="padding-right:2px;"><div class="admMini">relativer Pfad mit / beginnend</div></td>
  </tr></table>
 </td>
</tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Eintragen"></p>
</form>

<?php
}else{ /*Pfad unplausibel*/
?>

<div class="admBox">
<p>Das Administrationsscript kann die Datei <i>mpWerte.php</i> im Programmverzeichnis <i>marktplatz</i> nicht finden.</p>
<p>Wahrscheinlich stimmt die relative Pfadangabe zum Programmverzeichnis <i>marktplatz</i> in der Datei <i>hilfsFunktionen.php</i> im Administrator-Ordner nicht.
Normalerweise muß der Eintrag lauten: <i>MPPFAD='../';</i>
sofern der Ordner <i>admin</i> ein unmittelbarer Unterordner des Programmordners <i>marktplatz</i> ist.</p>
<p>Momentan lautet der Eintrag für den Pfad aber <i><?php echo MPPFAD?></i>
und verweist somit wahrscheinlich auf einen Ordner <i><?php echo realpath($sPfad)?></i> als angenommenen Programmordner.
Dort gibt es aber keine Datei <i>mpWerte.php</i> oder die Dateirechte sind so gesetzt, dass die Datei nicht lesbar ist.
<?php if(substr(MPPFAD,-1,1)!='/'){?>Es fehlt auf alle Fälle das / am Ende der Pfadangabe.<?php }?></p>
<p>Falls Sie den Ordner <i>admin</i> an eine andere Stelle verlagert haben,
müssen Sie die Pfadangabe in der Administrations-Datei <i>hilfsFunktionen.php</i> von Hand mit einem Editor so anpassen,
dass diese korrekt auf das Programmverzeichnis <i>marktplatz</i> verweist.</p>
<p><?php if(file_exists(MPPFAD.'marktplatz.php')){?>Offensichtlich ist aber tatsächlich die Datei <i>mpWerte.php</i>
im Programmverzeichnis <i>marktplatz</i> nicht vorhanden oder nicht lesbar,
denn die Programmdatei <i>marktplatz.php</i> ist im angegebenen Verzeichnis vorhanden.<?php }?></p>
</div>

<?php
}
echo fSeitenFuss();
?>