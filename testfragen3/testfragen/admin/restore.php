<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Konfiguration laden','','KLa');

$sMeld=''; $nSchritt=0; $UpNa='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['schritt'])&&($_POST['schritt']=='1'||$_POST['schritt']=='9')){//hochladen und pruefen
  if($_POST['schritt']=='1') $UpNa=str_replace(' ','_',basename($_FILES['UpFile']['name'])); @unlink(FRA_Pfad.'temp/restore.zip');
  if(strtolower(substr($UpNa,-4))=='.zip'){
   if(copy($_FILES['UpFile']['tmp_name'],FRA_Pfad.'temp/restore.zip')){
    $zip=new ZipArchive; $sW=''; $aZ=array();
    if($res=$zip->open(FRA_Pfad.'temp/restore.zip')===true){
     $nZip=$zip->numFiles; $sW=trim($zip->getFromName('_TestfragenSicherung.txt')); $nW=0;
     if(strpos($sW,'Testfragen')>0&&strpos($sW,'Datensicherung')>0){
      for($i=0;$i<$nZip;$i++){$sW=$zip->getNameIndex($i); $aZ[$i]=$sW; if(substr($sW,0,8)=='fraWerte'&&substr($sW,-4)=='.php') $nW=$i;}
      if($nW>0){//fraWerte.php gefunden
       if($sW=trim($zip->getFromIndex($nW))){
        if($p=strpos($sW,"define('FRA_Konfiguration'")){
         $t=substr($sW,strpos($sW,",'",$p)+2,99); $t=substr($t,0,strpos($t,"'")); $nSchritt=2;
         $sMeld='<p class="admMeld">Die Konfiguration <i>'.$aZ[$nW].'</i> (<i>'.$t.'</i>) jetzt einspielen?</p>';
        }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist beschädigt.</p>';
       }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist leer.</p>';
      }else $sMeld='<p class="admFehl">Das ZIP-Archiv <i>'.$UpNa.'</i> enthält keine Parmeter-Datei <i>fraWerte.php</i>.</p>';
     }else $sMeld='<p class="admFehl">Die hochgeladene Datei <i>'.$UpNa.'</i> ist keine Testfragen-Datensicherung.</p>';
     if($nSchritt<=0) $zip->close();
    }else $sMeld='<p class="admFehl">Das hochgeladene ZIP-Archiv <i>'.$UpNa.'</i> konnte nicht geöffnet werden.</p>';
   }else $sMeld='<p class="admFehl">Die Datei konnte nicht ins Verzeichnis <i>'.FRA_Pfad.'temp/</i> hochgeladen werden.</p>';
  }else $sMeld='<p class="admFehl">Bitte ein ZIP-Archiv hochladen statt der aktuellen Datei <i>'.$UpNa.'</i>.</p>';
 }elseif(isset($_POST['schritt'])&&$_POST['schritt']=='2'){//hochladen war OK
  $zip=new ZipArchive; $sW=''; $aZ=array();
  if($res=$zip->open(FRA_Pfad.'temp/restore.zip')===true){
   $nZip=$zip->numFiles; $sW=trim($zip->getFromName('_TestfragenSicherung.txt')); $nW=0;
   if(strpos($sW,'Testfragen')>0&&strpos($sW,'Datensicherung')>0){
    for($i=0;$i<$nZip;$i++){$sW=$zip->getNameIndex($i); $aZ[$i]=$sW; if(substr($sW,0,8)=='fraWerte'&&substr($sW,-4)=='.php') $nW=$i;}
    if($nW>0){//fraWerte.php gefunden
     if($sW=trim($zip->getFromIndex($nW))){
      if($p=strpos($sW,"define('FRA_Konfiguration'")){
       $t=substr($sW,strpos($sW,",'",$p)+2,99); $t=substr($t,0,strpos($t,"'")); $nSchritt=3;
       $sMeld='<p class="admErfo">Die Konfiguration <i>'.$aZ[$nW].'</i> (<i>'.$t.'</i>) wurde so eingespielt.</p>';
      }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist beschädigt.</p>';
     }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist leer.</p>';
    }else $sMeld='<p class="admFehl">Das ZIP-Archiv <i>'.$UpNa.'</i> enthält keine Parmeter-Datei <i>fraWerte.php</i>.</p>';
   }else $sMeld='<p class="admFehl">Die hochgeladene Datei <i>'.$UpNa.'</i> ist keine Testfragen-Datensicherung.</p>';
   if($nSchritt<=0) $zip->close();
  }else $sMeld='<p class="admFehl">Das hochgeladene ZIP-Archiv <i>'.$UpNa.'</i> konnte nicht geöffnet werden.</p>';
 }
}else $sMeld='<p class="admMeld" style="text-align:center">Laden Sie jetzt eine ZIP-Archivdatei mit einer früheren Sicherung hoch.</p>';

echo $sMeld.NL;

if($nSchritt<=0){//Upload-Formular
?>
<br />
<form action="restore.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="schritt" value="1" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl">
 <td style="width:8em;">ZIP-Datei</td>
 <td><input class="admEing" type="file" name="UpFile" size="80" style="width:99%" /></td>
</tr>
</table>
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration hochladen"></p>
</form><br /><br />

<p class="admMeld" style="text-align:center">oder sicher Sie zuvor die aktuelle Konfiguration</p>
<form action="backup.php<?php if(KONF>0)echo'?konf='.KONF?>" method="get">
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration sichern"></p>
</form><br />

<?php
}elseif($nSchritt>0){//Pruefen
 $bFragen=false; $bFolgen=false; $bBilder=false; $bWerte=true;
 $bVerso=false; $bStyle=false; $bIndex=false; $bSeite=false; $bFertig=false;
 $nBld=0; $sBld=''; $nFragen='keine'; $sDat='????/'; $sFra='???'; $sFol='???'; $nZip=count($aZ);
 $bSQL=false; $sSqlH=''; $sSqlD=''; $sSqlU=''; $sSqlP=''; $bSqlNo=false;

 if($p=strpos($sW,"define('FRA_Bilder'")){//Bilderordner
  $t=substr($sW,strpos($sW,",'",$p)+2,99); $sBld=substr($t,0,strpos($t,"'"));
  if($k=strlen($sBld)) for($i=0;$i<$nZip;$i++) if(substr($aZ[$i],0,$k)==$sBld) $nBld++; $bBilder=$nBld>0;
 }
 if($p=strpos($sW,"define('FRA_Daten'")) {$t=substr($sW,strpos($sW,",'",$p)+2,99); $sDat=substr($t,0,strpos($t,"'"));}
 if($p=strpos($sW,"define('FRA_SQL'")){$t=substr($sW,strpos($sW,",",$p)+1,9); $bSQL=(substr($t,0,4)=='true');}//SQL
 if(!$bSQL){
  if($p=strpos($sW,"define('FRA_Fragen'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFra=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_Folgen'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFol=substr($t,0,strpos($t,"'"));}
 }else{
  if($p=strpos($sW,"define('FRA_SqlHost'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlH=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_SqlDaBa'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlD=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_SqlUser'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlU=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_SqlPass'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlP=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_SqlTabF'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFra=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('FRA_SqlTabT'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFol=substr($t,0,strpos($t,"'"));}
 }

 $bFragen=($nFragen=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sFra.(!$bSQL?'':'.txt'))),NL),0))>0;
 $bFolgen=in_array((!$bSQL?$sDat:'sql/').$sFol.(!$bSQL?'':'.txt'),$aZ);

 $sChk='<input type="checkbox" class="admCheck" name="" value="1" checked="checked" />';
 $sHak='<img src="iconHaken.gif" width="13" height="13" border="0" title="gespeichert">';
 if($nSchritt==2) $sHak='<input type="checkbox" class="admCheck" name="" value="1" checked="checked" />';
 $sNein='<span style="color:#cc0000"><b>nein</b></span>'; $sWerte=$sNein; $sData=$sNein; $sFolg=$sNein;
 $bWrDir=(is_dir(FRA_Pfad)&&is_writable(FRA_Pfad));
 $sWrBld=(is_dir(FRA_Pfad.$sBld)&&is_writable(FRA_Pfad.$sBld)?($bBilder?str_replace('""','"bild"',$sHak):'--'):$sNein); $nBlW=$nBld;

 if(!$bSQL){
  if(file_exists(FRA_Pfad.$sDat.$sFra)){if(is_writable(FRA_Pfad.$sDat.$sFra)) $sData=str_replace('""','"fragen"',$sHak);}
  elseif(is_dir(FRA_Pfad.$sDat)&&is_writable(FRA_Pfad.$sDat)) $sData=str_replace('""','"fragen"',$sHak);
  if(file_exists(FRA_Pfad.$sDat.$sFol)){if(is_writable(FRA_Pfad.$sDat.$sFol)) $sFolg=str_replace('""','"folgen"',$sHak);}
  elseif(is_dir(FRA_Pfad.$sDat)&&is_writable(FRA_Pfad.$sDat)) $sFolg=str_replace('""','"folgen"',$sHak);
 }else{
  $DbOT=@new mysqli($sSqlH,$sSqlU,$sSqlP,$sSqlD);
  if(!mysqli_connect_errno()){$sData=str_replace('""','"fragen"',$sHak); $sFolg=str_replace('""','"folgen"',$sHak);} //SQL geht zu Oeffnen
  else{$DbOT=NULL; $bSQL=false; $bSqlNo=true; //ersatzweise Text-DaBa
   if(file_exists(FRA_Pfad.$sDat.$sFra.'.csv')){if(is_writable(FRA_Pfad.$sDat.$sFra.'.csv')){$sData=str_replace('""','"fragen"',$sHak); $sFra.='.csv';}}
   elseif(is_dir(FRA_Pfad.$sDat)&&is_writable(FRA_Pfad.$sDat)){$sData=str_replace('""','"fragen"',$sHak); $sFra.='.csv';}
   if(file_exists(FRA_Pfad.$sDat.$sFol.'.csv')){if(is_writable(FRA_Pfad.$sDat.$sFol.'.csv')){$sFolg=str_replace('""','"folgen"',$sHak); $sFol.='.csv';}}
   elseif(is_dir(FRA_Pfad.$sDat)&&is_writable(FRA_Pfad.$sDat)){$sFolg=str_replace('""','"folgen"',$sHak); $sFol.='.csv';}
  }
 }

 $bVerso=in_array('fraVersion.php',$aZ); $sVerso=$sNein;
 $bStyle=in_array('fraStyle.css',$aZ); $sStyle=$sNein;
 $bIndex=in_array('index.html',$aZ); $sIndex=$sNein;
 $bSeite=in_array('fraSeite.htm',$aZ); $sSeite=$sNein;
 $bFertig=in_array('fraFertig.inc.htm',$aZ); $sFertig=$sNein;

 if(file_exists(FRA_Pfad.$aZ[$nW])){if(is_writable(FRA_Pfad.$aZ[$nW])) $sWerte=($bWerte?str_replace('""','"werte"',$sHak):'--');} elseif($bWrDir) $sWerte=($bWerte?str_replace('""','"werte"',$sHak):'--');
 if(file_exists(FRA_Pfad.'fraVersion.php')){if(is_writable(FRA_Pfad.'fraVersion.php')) $sVerso=($bVerso?str_replace('""','"version"',$sHak):'--');} elseif($bWrDir) $sVerso=($bVerso?str_replace('""','"version"',$sHak):'--');
 if(file_exists(FRA_Pfad.'fraStyle.css')){if(is_writable(FRA_Pfad.'fraStyle.css')) $sStyle=($bStyle?str_replace('""','"style"',$sHak):'--');} elseif($bWrDir) $sStyle=($bStyle?str_replace('""','"style"',$sHak):'--');
 if(file_exists(FRA_Pfad.'index.html')){if(is_writable(FRA_Pfad.'index.html')) $sIndex=($bIndex?str_replace('""','"index"',$sHak):'--');} elseif($bWrDir) $sIndex=($bIndex?str_replace('""','"index"',$sHak):'--');
 if(file_exists(FRA_Pfad.'fraSeite.htm')){if(is_writable(FRA_Pfad.'fraSeite.htm')) $sSeite=($bSeite?str_replace('""','"seite"',$sHak):'--');} elseif($bWrDir) $sSeite=($bSeite?str_replace('""','"seite"',$sHak):'--');
 if(file_exists(FRA_Pfad.'fraFertig.inc.htm')){if(is_writable(FRA_Pfad.'fraFertig.inc.htm')) $sFertig=($bFertig?str_replace('""','"fertig"',$sHak):'--');} elseif($bWrDir) $sFertig=($bFertig?str_replace('""','"fertig"',$sHak):'--');

 if($nSchritt==3){//speichern
  if($sWerte==$sHak&&isset($_POST['werte'])&&$_POST['werte']==1){//fraWerte.php speichern
   if($p=strpos($sW,"define('FRA_Www'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,FRA_Www,$p,$q-$p);}
   if($p=strpos($sW,"define('FRA_Pfad'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,FRA_Pfad,$p,$q-$p);}
   if($bSqlNo){//SQL zu Text umschreiben
    if($p=strpos($sW,"define('FRA_SQL'")){$p=strpos($sW,",",$p)+1; $sW=substr_replace($sW,'false',$p,4);}
    if($p=strpos($sW,"define('FRA_Fragen'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,$sFra,$p,$q-$p);}
    if($p=strpos($sW,"define('FRA_Folgen'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,$sFol,$p,$q-$p);}
   }
   if($f=@fopen(FRA_Pfad.$aZ[$nW],'w')){//fraWerte.php gespeichert
    fwrite($f,str_replace("\r",'',trim($sW))."\n"); fclose($f);
    if(!$bSQL){
     if($sData==$sHak&&isset($_POST['fragen'])&&$_POST['fragen']==1){
      if($s=$zip->getFromName($sDat.$sFra)){
       if(!@file_put_contents(FRA_Pfad.$sDat.$sFra,$s)) $sData=$sNein;
      }else $sData='nein';
     }else $sData='nein';
     if($sFolg==$sHak&&isset($_POST['folgen'])&&$_POST['folgen']==1&&$sData!=$sNein){
      if($s=$zip->getFromName($sDat.$sFol)){
       if(!@file_put_contents(FRA_Pfad.$sDat.$sFol,$s)) $sFolg=$sNein;
      }else $sFolg='nein';
     }else $sFolg='nein';
    }elseif($DbOT){//SQL
     if($sData==$sHak&&isset($_POST['fragen'])&&$_POST['fragen']==1){
      if($s=$zip->getFromName('sql/'.$sFra.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sFra);
       $sF=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", versteckt CHAR(1) NOT NULL DEFAULT "", Kategorie VARCHAR(128) NOT NULL DEFAULT "", Frage TEXT NOT NULL, Loesung VARCHAR(16) NOT NULL DEFAULT "", Punkte INT(3) NOT NULL DEFAULT "0", Bild VARCHAR(128) NOT NULL DEFAULT ""';
       for($i=1;$i<=9;$i++) $sF.=', Antwort'.$i.' TEXT NOT NULL'; $sF.=', Anmerkung TEXT NOT NULL, Anmerkung2 TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="FrageScript-Fragen"';
       if($DbOT->query('CREATE TABLE '.$sFra.$sF)){//Testfragen
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); $s='"'.$a[0].'"';
         for($j=1;$j<19;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
         if(isset($a[1])) if(!$DbOT->query('INSERT IGNORE INTO '.$sFra.' VALUES('.$s.')'))  ;
        }
       }else $sData='nein';
      }else $sData='nein';
     }else $sData='nein';
     if($sFolg==$sHak&&isset($_POST['folgen'])&&$_POST['folgen']==1&&$sData!=$sNein){
      if($s=$zip->getFromName('sql/'.$sFol.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sFol);
       $sT=' (Folge VARCHAR(50) NOT NULL DEFAULT "", Fragen TEXT NOT NULL, ProSeite VARCHAR(8) NOT NULL DEFAULT "", PRIMARY KEY (Folge)) COMMENT="FrageScript-Testfolgen"';
       if($DbOT->query('CREATE TABLE '.$sFol.$sT)){//Testfolgen
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); $s='"'.$a[0].'"';
         for($j=1;$j<3;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
         if(isset($a[1])) if(!$DbOT->query('INSERT IGNORE INTO '.$sFol.' VALUES('.$s.')'))  ;
        }
       }else $sFolg='nein';
      }else $sFolg='nein';
     }else $sFolg='nein';
    }
    if($sData!=$sNein){
     if($sWrBld==$sHak&&isset($_POST['bilder'])&&$_POST['bilder']==1){
      $k=strlen($sBld); $nBlW=0; //Bilder schreiben
      for($i=0;$i<$nZip;$i++) if(substr($aZ[$i],0,$k)==$sBld){
       if($s=$zip->getFromIndex($i)){
        $t=substr($aZ[$i],$k); if($p=strpos($t,'/')){$t=substr($t,0,$p); if(!is_dir(FRA_Pfad.$sBld.$t)) @mkdir(FRA_Pfad.$sBld.$t);}
        if(@file_put_contents(FRA_Pfad.$aZ[$i],$s)) $nBlW++; else $sWrBld=$sNein;
       }else $sWrBld='nein';
      }//for
     }
     if($sVerso==$sHak&&isset($_POST['version'])&&$_POST['version']==1){
      if($i=array_search('fraVersion.php',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(FRA_Pfad.'fraVersion.php',$s)) $sVerso=$sNein;
       }else $sVerso='nein';
      }else $sVerso='nein';
     }else $sVerso='nein';
     if($sStyle==$sHak&&isset($_POST['style'])&&$_POST['style']==1){
      if($i=array_search('fraStyle.css',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(FRA_Pfad.'fraStyle.css',$s)) $sStyle=$sNein;
       }else $sStyle='nein';
      }else $sStyle='nein';
     }else $sStyle='nein';
     if($sIndex==$sHak&&isset($_POST['index'])&&$_POST['index']==1){
      if($i=array_search('index.html',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(FRA_Pfad.'index.html',$s)) $sIndex=$sNein;
       }else $sIndex='nein';
      }else $sIndex='nein';
     }else $sIndex='nein';
     if($sSeite==$sHak&&isset($_POST['seite'])&&$_POST['seite']==1){
      if($i=array_search('fraSeite.htm',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(FRA_Pfad.'fraSeite.htm',$s)) $sSeite=$sNein;
       }else $sSeite='nein';
      }else $sSeite='nein';
     }else $sSeite='nein';
     if($sFertig==$sHak&&isset($_POST['fertig'])&&$_POST['fertig']==1){
      if($i=array_search('fraFertig.inc.htm',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(FRA_Pfad.'fraFertig.inc.htm',$s)); $sFertig=$sNein;
       }else $sFertig='nein';
      }else $sFertig='nein';
     }else $sFertig='nein';
    }else{$sWrBld='nein'; $sFolg='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}//$sData
   }else{$sWerte=$sNein; $sWrBld='nein'; $sData='nein'; $sFolg='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}//fraWerte.php
  }else{$sWerte='nein'; $sWrBld='nein'; $sData='nein'; $sFolg='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}
  $nSchritt=9;
 }
 $zip->close();
 $sDrin='<img src="iconHaken.gif" width="13" height="13" border="0" title="enthalten">';
?>

<br />
<form action="restore.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="schritt" value="<?php echo $nSchritt?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1" style="text-align:center">enthalten</td>
  <td class="admSpa1" style="text-align:center"><?php echo ($nSchritt==2?'hochladen':'gespeichert')?></td>
  <td>Objekt</td>
  <td>Erklärung</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bFragen?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sData?></td>
  <td>Fragen<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nFragen?> Fragen in der Fragen-Tabelle <i><?php echo (!$bSQL?$sDat.$sFra:$sFra.' (MySQL)')?></i></td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bFolgen?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sFolg?></td>
  <td>Testfolgen<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php ?>Testfolgen in der Folgen-Tabelle <i><?php echo (!$bSQL?$sDat.$sFol:$sFol.' (MySQL)')?></i></td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bBilder?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sWrBld?></td>
  <td><?php echo $nBlW?> Bilder</td>
  <td><?php echo $nBld?> Bilder im Ordner <i><?php echo substr($sBld,0,-1)?></i></td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bWerte?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sWerte?></td>
  <td><?php echo $aZ[$nW]?></td>
  <td>zentrale Parameter- und Einstelldatei</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bVerso?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sVerso?></td>
  <td>fraVersion.php</td>
  <td>Versions-Datei</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bStyle?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sStyle?></td>
  <td>fraStyle.css</td>
  <td>CSS-Styles-Formatierungsdatei</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bIndex?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sIndex?></td>
  <td>index.html</td>
  <td>umhüllendes Frameset</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bSeite?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sSeite?></td>
  <td>fraSeite.htm</td>
  <td>umhüllende HTML-Schablone</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bFertig?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sFertig?></td>
  <td>fraFertig.inc.htm</td>
  <td>Vorlage für Fertig-Meldung</td>
 </tr>
</table>
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration einspielen"></p>
</form><br /><br />

<?php } ?>

<p><u>Hinweis</u>:</p>
<ul>
<li>Das Laden der Konfiguration erfolgt aus einer hochzuladenden ZIP-Archivdatei,
die zu einem früheren Zeitpunkt mit der Konfigurationssicherung des Testfragen-Scripts erzeugt wurde.</li>
<li>Im ZIP-Archiv enthaltene Daten werden über den momentanen Test übergespielt
und überschreiben je nach Konfiguration eventuell die aktuellen Daten.
Deshalb sollte vor einem Hochladen eventuell die jetzige Konfiguration erst einmal gesichert werden,
falls es sich um eine produktiv genutzte Konfiguration handelt.</li>
<?php if($nSchritt==2){?><li>Ein <span style="color:#cc0000"><b>nein</b></span> in der Spalte <i>hochladen</i> bedeutet, dass das Objekt mangels Rechte nicht gespeichert oder nicht überschrieben werden könnte.</li><?php }?>
</ul>

<?php echo fSeitenFuss();?>