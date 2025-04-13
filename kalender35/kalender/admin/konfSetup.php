<?php
if(file_exists('hilfsFunktionen.php')) include 'hilfsFunktionen.php';
echo fSeitenKopf('Setup ausführen','','KSu');

$bSetUp=true; $bPath=true; $ksPfad=''; $ksPfadI=''; $ksWww=''; $ksDocRoot=''; $ksRelPfad='';
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
  $ksPfad=KAL_Pfad; $ksPfadI=KAL_Pfad; $ksWww=KAL_Www; if($p=strpos($ksWww,'://')) $ksWww=substr($ksWww,$p+3);

  $amMitLogin=ADM_MitLogin; $amSessionsAgent=ADM_SessionsAgent; $amSessionsIPAddr=ADM_SessionsIPAddr;
  $amAdmin=ADM_Admin; $amPasswort=fKalDeCode(ADM_Passwort); $amBreite=ADM_Breite; $amHilfe=ADM_Hilfe; $amSqlZs=ADM_SqlZs;
  $amAuthLogin=ADM_AuthLogin; $amAuthor=ADM_Author; $amAuthPass=fKalDeCode(ADM_AuthPass);

  if(defined('KAL_DocRoot')){ // neues Setup von 2021 schon eingetragen
   $ksDocRoot=KAL_DocRoot; $ksRelPfad=KAL_RelPfad;
   if($ksDocRoot&&$ksRelPfad){$ksPfad=$_SERVER[$ksDocRoot].$ksRelPfad; $ksPfadI='$_'."SERVER['".$ksDocRoot."'].'".$ksRelPfad."'";}
  }else{ // Setup beim 1 mal updaten
   if(file_exists(KALPFAD.'kalWerte.php')&&is_writable(KALPFAD.'kalWerte.php')){
    $sWerte=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalWerte.php'))));
    if($p=strpos($sWerte,"define('KAL_Daten'")){
     $sWerte=substr_replace($sWerte,"define('KAL_DocRoot','');\ndefine('KAL_RelPfad','');\n",$p,0);
     if($f=fopen(KALPFAD.'kalWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
     }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
    }else $Msg='<p class="admFehl">Die Datei <i>kalWerte.php</i> entspricht nicht der Originalstruktur!</p>';
   }else $Msg='<p class="admFehl">Die Datei <i>kalWerte.php</i> war soeben nicht beschreibbar!</p>';
  }
  $sMsg='<p class="admMeld">Überprüfen Sie die Pfad-Einstellungen für das Kalender-Script.</p>';
 }else $bPath=false;
}elseif($_POST['Btn']=='Eintragen'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalWerte.php')))); $bNeu=false; $ksDocRoot=KAL_DocRoot; $ksRelPfad=KAL_RelPfad; $sErfo='';
 $w=txtVar('Www');  if(substr($w,-1,1)!='/') $w.='/'; if($p=strpos($w,'://')) $w=substr($w,$p+3);
 if(fSetzKalWert($w,'Www',"'")) $bNeu=true; else $ksWww=$w;
 $ksDocRoot=txtVar('DocRoot');
 if($ksDocRoot==''||isset($_SERVER[$ksDocRoot])){
  if(fSetzKalWert($ksDocRoot,'DocRoot',"'")) $bNeu=true;
 }else $Msg='<p class="admFehl">Die Servervariable <i>'.$ksDocRoot.'</i> existiert nicht!</p>';
 if($p=txtVar('RelPfad')){if(substr($p,-1,1)!='/') $p.='/'; if(substr($p,0,1)!='/') $p='/'.$p;} $ksRelPfad=$p;
 if(fSetzKalWert($p,'RelPfad',"'")) $bNeu=true;
 $v=txtVar('Pfad'); if(substr($v,-1,1)!='/') $v.='/'; $ksPfad=$v;
 if($ksDocRoot&&$p&&isset($_SERVER[$ksDocRoot])){ // KAL_Pfad speziell speichern
  if($n=strpos($sWerte,"define('KAL_Pfad'")){
   $m=strpos($sWerte,");",$n); $n+=18;
   $sWerte=substr_replace($sWerte,'$_'."SERVER['".$ksDocRoot."'].'".$p."'",$n,$m-$n);
   $ksPfad=$_SERVER[$ksDocRoot].$p;
  }
 }else if(fSetzKalWert($v,'Pfad',"'")) $bNeu=true; // KAL_Pfad klassisch speichern

 $ksPfadI=$ksPfad;
 if($ksDocRoot&&$ksRelPfad&&($n=strpos($sWerte,"define('KAL_Pfad'"))){
  $m=strpos($sWerte,");",$n); $n+=18; $ksPfadI=substr($sWerte,$n,$m-$n);
 }

 $ksKey=KAL_Schluessel;
 if(KAL_Schluessel<='00'){
  $ksKey=substr(time(),-6); if(fSetzKalWert($ksKey,'Schluessel',"'")) $bNeu=true;
  if(!strpos($sWerte,'//Schluessel:')) if($p=strpos($sWerte,"define('KAL_Schluessel'")) if($p=strpos($sWerte,"\n",$p+1)) $sWerte=substr_replace($sWerte,' //Schluessel: '.$ksKey,$p,0);
 }

 $v=(int)txtVar('MitLogin'); if(setzAdmWert(($v?true:false),'MitLogin','')) $bNeu=true;
 $v=(int)txtVar('SessionsAgent'); if(setzAdmWert(($v?true:false),'SessionsAgent','')) $bNeu=true;
 $v=(int)txtVar('SessionsIPAddr'); if(setzAdmWert(($v?true:false),'SessionsIPAddr','')) $bNeu=true;
 $v=strtolower(txtVar('Admin')); if(setzAdmWert($v,'Admin',"'")) $bNeu=true;
 $v=(isset($_POST['Passwort'])?'#'.trim($_POST['Passwort']):''); $amPasswort=fKalDeCode(ADM_Passwort,$ksKey);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('Passwort'))); if(setzAdmWert(fKalEnCode($v,$ksKey),'Passwort',"'")) $bNeu=true; $amPasswort=$v;
 }elseif(!strpos($Msg,'Administratorpasswort')) $Msg.='<p class="admFehl">Das Administratorpasswort darf kein &quot; oder &gt; enthalten!</p>';
 $v=(int)txtVar('AuthLogin'); if(setzAdmWert(($v?true:false),'AuthLogin','')) $bNeu=true;
 $v=strtolower(txtVar('Author')); if(setzAdmWert($v,'Author',"'")) $bNeu=true;
 $v=(isset($_POST['AuthPass'])?'#'.trim($_POST['AuthPass']):''); $amAuthPass=fKalDeCode(ADM_AuthPass,$ksKey);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('AuthPass'))); if(setzAdmWert(fKalEnCode($v,$ksKey),'AuthPass',"'")) $bNeu=true; $amAuthPass=$v;
 }elseif(!strpos($Msg,'Autorenpasswort')) $Msg.='<p class="admFehl">Das Autorenpasswort darf kein &quot; oder &gt; enthalten!</p>';

 if($bNeu){//Speichern
  if($f=fopen(KALPFAD.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg.='<p class="admErfo">Die Einstellungen wurden gespeichert.</p>';
  }else $Msg.='<p class="admFehl">Keine Berechtigung zum Schreiben in die Datei <i>kalWerte.php</i>!</p>';
 }else $Msg.='<p class="admMeld">Die Pfad- und Zugangs-Einstellungen bleiben unverändert.</p>';
}

//Scriptausgaben
if($ksWww==$sWww&&$ksPfad==$sPfad){ //Pfade stimmen
 if($Msg=='') $Msg='<p class="admMeld">Momentan sind die folgenden Pfade eingestellt, die sehr wahrscheinlich korrekt sind.</p>';
}else{ //Pfade abweichend
 $bSetUp=false;
 $Msg.='<p class="admFehl">Die eingestellten Pfade scheinen <span style="color:#CC0033;">nicht</span> korrekt zu sein. Führen Sie das nachfolgende Setup aus. <a href="'.ADM_Hilfe.'LiesMich.htm#2.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
}
echo $Msg.NL; $Msg='';

if($bPath){ /*Pfad plausibel*/
 if($bSetUp){
  @include(KALPFAD.'kalVersion.php');
  if(KAL_Version<$kalVersion){ //Versionsupdate
   $sMsg='<p class="admMeld">Es sind noch Programmneuerungen (<a href="konfUpdate.php">Update</a>) einzupflegen.</p>';
  }else $sMsg='<p class="admMeld">Programmneuerungen (Updates) sind aktuell nicht einzupflegen.</p>';
  echo $sMsg.NL;
 }
?>

<form name="setupForm" action="konfSetup.php" method="post">
<table class="admTabl" style="width:99%" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die wahrscheinliche Adresse zum Kalender-Script wurde als <br><span style="color:#0055AA"><i><?php echo $sWww?></i></span> ermittelt.<br />
In der Datei <i>kalWerte.php</i> ist momentan <br><i><?php echo $ksWww?$ksWww:'nichts' ?></i> eingetragen.<br>
Bitte stellen Sie gegebenenfalls die tatsächlich zutreffende Adresse zum Programmverzeichnis des Kalender-Scripts ein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Adresse</td>
 <td><div style="float:right;padding-top:3px"><img src="<?php echo KALPFAD?>grafik/icon_Kopie.gif" onclick="document.setupForm.Www.value='<?php echo $sWww?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sWww?> übernehmen"></div>
 <div style="width:96%"><input type="text" name="Www" value="<?php echo $ksWww?>" style="width:100%" /></div>
 <div class="admMini" style="clear:both;">(ohne <i>http://</i>)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Der wahrscheinliche physische Pfad zum Kalender-Script wurde als <br><span style="color:#0055AA"><i><?php echo $sPfad?></i></span> ermittelt.<br />
In der Datei <i>kalWerte.php</i> ist momentan <br><i><?php echo $ksPfadI?$ksPfadI:'nichts' ?></i> eingetragen.<br>
Bitte stellen Sie gegebenenfalls den tatsächlich zutreffenden physischen Pfad zum Programmverzeichnis des Kalender-Scripts ein, beginnend im Wurzelverzeichnis des Servers.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Adresse</td>
 <td><div style="float:right;padding-top:3px"><img src="<?php echo KALPFAD?>grafik/icon_Kopie.gif" onclick="document.setupForm.Pfad.value='<?php echo $sPfad?>'" width="12" height="13" border="0" title="ermittelten Wert <?php echo $sPfad?> übernehmen"></div>
 <div style="width:96%"><input type="text" name="Pfad" value="<?php echo $ksPfad?>" style="width:100%" /></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei einigen Providern ist ein statischer Serverpfad wie oben ermittelt und eingestellt nicht gewünscht.
Statt dessen soll dort eine Kombination aus einer Server-Variablen und einem relativen Pfad genutzt werden.
Tragen Sie in einem solchen Fall diese Kombination ein. Anderenfalls lassen Sie die folgenden 2 Felder leer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer Serverpfad</td>
 <td>
  <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
   <td style="padding-right:2px;width:40%"><input type="text" name="DocRoot" value="<?php echo $ksDocRoot?>"style="width:95%" /></td>
   <td style="padding-right:2px;"><input type="text" name="RelPfad" value="<?php echo $ksRelPfad?>" style="width:98%" /></td>
  </tr><tr>
   <td style="padding-right:2px;"><div class="admMini">Servervariable meist: <i>DOCUMENT_ROOT</i></div></td>
   <td style="padding-right:2px;"><div class="admMini">relativer Pfad mit / beginnend</div></td>
  </tr></table>
 </td>
</tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Eintragen"></p>

<table class="admTabl" style="width:99%" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Zugangsschutz zur gesamten Administration sollte bevorzugt serverseitig erfolgen.
Im Kundenmenü nahezu jedes heutigen Servers findet sich ein Menüpunkt wie <i>Zugangsschutz</i>, <i>geschütze Ordner</i> o.ä.
Alternativ dazu läßt sich der Zugangsschutz oft über eine .htaccess-Datei im Admin-Ordner einrichten.
<p>Auch könnten Sie dem Administrations-Unterordner einen anderen Namen als /admin/ geben, um zufällige Besuche zu verhindern und die Sicherheit zu erhöhen.</p></td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sollte ein serverseitiger Schutz des Admin-Ordners auf Ihrem Server nicht möglich oder nicht gewollt sein,
können Sie den scriptseitigen Zugangs-Schutz zur Administration einschalten.
Dieser scriptseitige Schutz bietet <i>nicht</i> die selbe hohe Sicherheit wie der serverseitige Schutz.
Ausserdem müssen Sie hierfür Sitzungs-Cookies in Ihrem Browser zulassen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>der Administration</td>
 <td><input type="radio" class="admRadio" name="MitLogin" value=""<?php if(!$amMitLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="MitLogin" value="1"<?php if($amMitLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Administrator <input type="text" name="Admin" value="<?php echo $amAdmin?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="Passwort" value="<?php echo $amPasswort?>" style="width:10em" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sitzungsüberwachung</td>
 <td><div><input type="checkbox" class="admRadio" name="SessionsAgent" value="1"<?php if($amSessionsAgent) echo ' checked="checked"'?> /> Browserkennung überwachen</div>
 <div><input type="checkbox" class="admRadio" name="SessionsIPAddr" value="1"<?php if($amSessionsIPAddr) echo ' checked="checked"'?> /> IP-Adresse überwachen</div>
 <div class="admMini">ausschalten verringert die Sicherheit bei scriptseitigem Zugangsschutz zur Administration</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Auch für den Autorenbereich im entsprechenden Unterordner kann bei dessen Verwendung ein scriptseitiger Zugangsschutz eingerichtet werden.
Allerdings ist auch hier der serverseitige Schutz die zu bevorzugende Variante.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>des Autorenbereichs</td>
 <td><input type="radio" class="admRadio" name="AuthLogin" value=""<?php if(!$amAuthLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="AuthLogin" value="1"<?php if($amAuthLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Autorname <input type="text" name="Author" value="<?php echo $amAuthor?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="AuthPass" value="<?php echo $amAuthPass?>" style="width:10em" /></td>
</tr>
</table>
<p class="admSubmit" style="margin-bottom:32px;"><input class="admSubmit" type="submit" name="Btn" value="Eintragen"></p>
</form>

<?php
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

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADM_'.$n)){
  $p=strpos($sWerte,'ADM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>