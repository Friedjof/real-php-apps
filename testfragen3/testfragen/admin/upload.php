<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Dateien hochladen','<script type="text/javascript">
 function BldWin(href){bldWin=window.open(href,"bld","width=850,height=680,left=5,top=5,menubar=yes,statusbar=yes,toolbar=no,scrollbars=yes,resizable=yes");bldWin.focus();}
</script>','Upl');

$fsUploadDir=FRA_UploadDir; $sLschName=''; $sKonf=''; if(KONF>0) $sKonf=KONF;
if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['laden'])){ //Upload
  $sUpDir=trim($_POST['UpPfad']);
  while(substr($sUpDir,-1,1)=='/') $sUpDir=substr($sUpDir,0,-1); $sUpDir.='/'; while(substr($sUpDir,0,1)=='/') $sUpDir=substr($sUpDir,1);
  if($sUpDir!=$fsUploadDir){
   $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
   if(fSetzFraWert($sUpDir,'UploadDir',"'")) $bNeu=true; $fsUploadDir=$sUpDir;
   if($bNeu){//Speichern
    if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
  }}}
  if(is_dir(FRA_Pfad.$fsUploadDir)){
   if(is_writable(FRA_Pfad.$fsUploadDir)){
    if($UpNa=str_replace(' ','_',basename($_FILES['UpFile']['name']))){
     $UpNa=str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',$UpNa)))))));
     $UpNa=str_replace('Ã„','Ae',str_replace('Ã¤','ae',str_replace('Ã–','Oe',str_replace('Ã¶','oe',str_replace('Ãœ','Ue',str_replace('Ã¼','ue',str_replace('ÃŸ','ss',$UpNa)))))));
     if(copy($_FILES['UpFile']['tmp_name'],FRA_Pfad.$fsUploadDir.$UpNa)){
      $sMeld='<p class="admErfo">Die Datei wurde als <i>'.$fsUploadDir.$UpNa.'</i> gespeichert.</p>';
     }else $sMeld='<p class="admFehl">Die Datei konnte nicht gespeichert werden.</p>';
    }else $sMeld='<p class="admMeld">Bitte geben Sie eine Datei an.</p>';
   }else $sMeld='<p class="admFehl">In den Ordner <i>'.$fsUploadDir.'</i> durfte nicht gespeichert werden.</p>';
  }else $sMeld='<p class="admFehl">Der Ordner <i>'.$fsUploadDir.'</i> existiert nicht.</p>';
 }elseif(isset($_POST['loeschen'])){ //Loeschen
  if($fsUploadDir!=''&&$fsUploadDir!='admin/'&&$fsUploadDir!='autoren/'&&$fsUploadDir!=FRA_Daten&&$fsUploadDir!=FRA_CaptchaPfad){
   foreach($_POST as $k=>$v) if(substr($k,0,2)=='dl'){$sLschName=$_POST['dn'.(int)substr($k,2,4)]; break;}
   if($sLschName!=''&&$sLschName==$_POST['lschName']){
    if(@unlink(FRA_Pfad.$fsUploadDir.$sLschName)) $sMeld='<p class="admErfo">Die Datei <i>'.$sLschName.'</i> wurde gelöscht.</p>';
    else $sMeld='<p class="admFehl">Die Datei <i>'.$sLschName.'</i> durfte nicht gelöscht werden.</p>'; $sLschName='';
   }else $sMeld='<p class="admFehl">Die Datei <i>'.$sLschName.'</i> wirklich löschen?</p>';
  }else $sMeld='<p class="admFehl">In diesem Ordner darf so nicht gelöscht werden.</p>';
 }
}else{ //GET
 $sMeld='<p class="admMeld">Laden Sie eine Datei auf den Server.</p>';
}
echo $sMeld.NL;
?>

<form action="upload.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="laden" value="1" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">
Laden Sie eine Datei zur späteren Verwendung innerhalb der Fragen oder Antworten auf den Server.
</td></tr>
<tr class="admTabl">
 <td>Datei</td>
 <td><input class="admEing" type="file" name="UpFile" size="80" style="width:99%" /></td>
</tr>
<tr class="admTabl">
 <td style="width:1%">Speicherort</td>
 <td style="color:#666666"><?php echo FRA_Pfad?><input class="admEing" type="text" value="<?php echo $fsUploadDir?>" name="UpPfad" size="15" style="width:120px" /></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Hochladen"></p>
</form><br />

<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2"><p>Hinweise:</p>
<p>Die Dateien sind dazu bestimmt, innerhalb der Fragen und Antworttexte
als zusätzliche Symbole, Bilder, Videos, Audios oder Objekte verlinkt oder eingebettet zu werden.</p>
<p>Dateien können in den Bilder-Ordner <i><?php echo substr(FRA_Bilder,0,-1)?></i>, dessen Unterordner oder in einen beliebigen anderen Ordner hochgeladen werden,
sofern die entsprechenden Rechte dazu gesetzt sind.
Bei Benutzung des Bilderordners <i><?php echo substr(FRA_Bilder,0,-1)?></i> ist selbst zu organisieren,
dass die hier hochgeladenen Bilder nicht mit den Bildern aus dem Frageneingabeformular kollidieren.</p>
</td></tr>
</table><br>

<p class="admMeld">Dateiliste</p>
<form action="upload.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="loeschen" value="1" />
<input type="hidden" name="lschName" value="<?php echo $sLschName?>" />
<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl"><td width="17">&nbsp;</td><td><i>Dateiname</i></td><td width="19"></td><td align="right"><i>Größe</i></td><td align="center"><i>Datum</i></td></tr>
<?php
if($H=@opendir(substr(FRA_Pfad.$fsUploadDir,0,-1))){$aH=false;
 while($F=readdir($H)) if($F!='.'&&$F!='..'&&$F!='index.html') $aH[]=$F; closedir($H); clearstatcache();
 if(is_array($aH)){
  natcasesort($aH); $k=1;
  foreach($aH as $H=>$F){
   $sExt=strtolower(substr($F,strrpos($F,'.')+1)); $bExt=$sExt=='jpg'||$sExt=='gif'||$sExt=='png'||$sExt=='pdf'||$sExt=='txt'||$sExt=='htm'||$sExt=='html'||$sExt=='jpeg';
   $sV=''; if(!$bExt) if($sExt=='mp4'||$sExt=='ogg'||$sExt=='ogv'||$sExt=='webm'||$sExt=='mp3') $sV='M';

   echo ' <tr class="admTabl">'."\n";
   echo '  <td><input type="image" name="dl'.$k.'" src="iconLoeschen.gif" width="12" height="13" border="0" title="'.$F.' löschen " /><input type="hidden" name="dn'.($k++).'" value="'.$F.'" />'."</td>\n";
   echo '  <td>'.$F."</td>\n";
   echo '  <td align="center">'.($bExt||$sV?' <a href="'.($bExt?'http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.$fsUploadDir.$F:'upView.php?f='.rawurlencode($fsUploadDir.$F)).'" target="bld" onclick="BldWin(this.href);return false;"><img src="iconVorschau.gif" width="13" height="13" border="0" alt="Vorschau"></a>':'&nbsp;')."</td>\n";
   echo '  <td align="right">'.(!is_Dir(FRA_Pfad.$fsUploadDir.$F)?str_replace('.',',',sprintf('%.1f',filesize(FRA_Pfad.$fsUploadDir.$F)/1024)).' KByte':'Verzeichnis').'</td>'."\n";
   echo '  <td align="center">'.date ("d.m.Y H:i:s",filemtime(FRA_Pfad.$fsUploadDir.$F))."</td>\n";
   echo ' </tr>'."\n";
}}}
?>
</table>
</form>

<?php echo fSeitenFuss();?>