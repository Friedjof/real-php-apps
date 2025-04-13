<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Konfiguration laden','','ZRe');

$sMeld=''; $nSchritt=0; $UpNa='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['schritt'])&&($_POST['schritt']=='1'||$_POST['schritt']=='9')){//hochladen und pruefen
  if($_POST['schritt']=='1') $UpNa=str_replace(' ','_',basename($_FILES['UpFile']['name'])); @unlink(UMF_Pfad.'temp/restore.zip');
  if($UpNa){
   if(strtolower(substr($UpNa,-4))=='.zip'){
    if(copy($_FILES['UpFile']['tmp_name'],UMF_Pfad.'temp/restore.zip')){
     $zip=new ZipArchive; $sW=''; $aZ=array();
     if($res=$zip->open(UMF_Pfad.'temp/restore.zip')===true){
      $nZip=$zip->numFiles; $sW=trim($zip->getFromName('_UmfrageSicherung.sav')); $nW=0;
      if(strpos($sW,'Umfrage')>0&&strpos($sW,'Datensicherung')>0){
       for($i=0;$i<$nZip;$i++){$sW=$zip->getNameIndex($i); $aZ[$i]=$sW; if(substr($sW,0,8)=='umfWerte'&&substr($sW,-4)=='.php') $nW=$i;}
       if($nW>0){//umfWerte.php gefunden
        if($sW=trim($zip->getFromIndex($nW))){
         if($p=strpos($sW,"define('UMF_Konfiguration'")){
          $t=substr($sW,strpos($sW,",'",$p)+2,99); $t=substr($t,0,strpos($t,"'")); $nSchritt=2;
          $sMeld='<p class="admMeld">Dateien der Konfiguration <i>'.$aZ[$nW].'</i> (<i>'.$t.'</i>) jetzt einspielen?</p>';
         }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist beschädigt.</p>';
        }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist leer.</p>';
       }else $sMeld='<p class="admFehl">Das ZIP-Archiv <i>'.$UpNa.'</i> enthält keine Parmeter-Datei <i>umfWerte.php</i>.</p>';
      }else $sMeld='<p class="admFehl">Die hochgeladene Datei <i>'.$UpNa.'</i> ist keine Umfragen-Datensicherung.</p>';
      if($nSchritt<=0) $zip->close();
     }else $sMeld='<p class="admFehl">Das hochgeladene ZIP-Archiv <i>'.$UpNa.'</i> konnte nicht geöffnet werden.</p>';
    }else $sMeld='<p class="admFehl">Die Datei konnte nicht ins Verzeichnis <i>'.UMF_Pfad.'temp/</i> hochgeladen werden.</p>';
   }else $sMeld='<p class="admFehl">Bitte ein ZIP-Archiv hochladen statt der aktuellen Datei <i>'.$UpNa.'</i>.</p>';
  }else $sMeld='<p class="admFehl">Bitte zuerst ein ZIP-Archiv hochladen!</p>';
 }elseif(isset($_POST['schritt'])&&$_POST['schritt']=='2'){//hochladen war OK
  $zip=new ZipArchive; $sW=''; $aZ=array();
  if($res=$zip->open(UMF_Pfad.'temp/restore.zip')===true){
   $nZip=$zip->numFiles; $sW=trim($zip->getFromName('_UmfrageSicherung.sav')); $nW=0;
   if(strpos($sW,'Umfrage')>0&&strpos($sW,'Datensicherung')>0){
    for($i=0;$i<$nZip;$i++){$sW=$zip->getNameIndex($i); $aZ[$i]=$sW; if(substr($sW,0,8)=='umfWerte'&&substr($sW,-4)=='.php') $nW=$i;}
    if($nW>0){//umfWerte.php gefunden
     if($sW=trim($zip->getFromIndex($nW))){
      if($p=strpos($sW,"define('UMF_Konfiguration'")){
       $t=substr($sW,strpos($sW,",'",$p)+2,99); $t=substr($t,0,strpos($t,"'")); $nSchritt=3;
       $sMeld='<p class="admErfo">Die Konfiguration <i>'.$aZ[$nW].'</i> (<i>'.$t.'</i>) wurde so eingespielt.</p>';
      }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist beschädigt.</p>';
     }else $sMeld='<p class="admFehl">Die Datei <i>'.$aZ[$nW].'</i> im Archiv <i>'.$UpNa.'</i> ist leer.</p>';
    }else $sMeld='<p class="admFehl">Das ZIP-Archiv <i>'.$UpNa.'</i> enthält keine Parmeter-Datei <i>umfWerte.php</i>.</p>';
   }else $sMeld='<p class="admFehl">Die hochgeladene Datei <i>'.$UpNa.'</i> ist keine Umfragen-Datensicherung.</p>';
   if($nSchritt<=0) $zip->close();
  }else $sMeld='<p class="admFehl">Das hochgeladene ZIP-Archiv <i>'.$UpNa.'</i> konnte nicht geöffnet werden.</p>';
 }
}else $sMeld='<p class="admMeld" style="text-align:center">Laden Sie jetzt eine ZIP-Archivdatei mit einer früheren Sicherung hoch.</p>';

echo $sMeld.NL;

if($nSchritt<=0){//Upload-Formular
?>
<br />
<form action="zipRestore.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="schritt" value="1" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl">
 <td style="width:8em;">ZIP-Datei</td>
 <td><input class="admEing" type="file" name="UpFile" size="80" style="width:98%" /></td>
</tr>
</table>
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration hochladen"></p>
</form><br /><br />

<p class="admMeld" style="text-align:center">oder sicher Sie zuvor die aktuelle Konfiguration</p>
<form action="zipBackup.php<?php if(KONF>0)echo'?konf='.KONF?>" method="get">
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration sichern"></p>
</form><br />

<?php
}elseif($nSchritt>0){//Pruefen
 $bFragen=true; $bErgebnis=true; $bZuweisung=true; $bNutzer=true; $bTeilnahme=true; $bBilder=false; $bWerte=true;
 $bVerso=false; $bStyle=false; $bIndex=false; $bSeite=false; $bFertig=false;
 $nBld=0; $sBld=''; $nFragen='keine'; $sDat='????/'; $sFra='???'; $sFol='???'; $nZip=count($aZ);
 $bSQL=false; $sSqlH=''; $sSqlD=''; $sSqlU=''; $sSqlP=''; $bSqlNo=false;

 if($p=strpos($sW,"define('UMF_Bilder'")){//Bilderordner
  $t=substr($sW,strpos($sW,",'",$p)+2,99); $sBld=substr($t,0,strpos($t,"'"));
  if($k=strlen($sBld)) for($i=0;$i<$nZip;$i++) if(substr($aZ[$i],0,$k)==$sBld) $nBld++; $bBilder=$nBld>0;
 }
 if($p=strpos($sW,"define('UMF_Daten'")) {$t=substr($sW,strpos($sW,",'",$p)+2,99); $sDat=substr($t,0,strpos($t,"'"));}
 if($p=strpos($sW,"define('UMF_SQL'")){$t=substr($sW,strpos($sW,",",$p)+1,9); $bSQL=(substr($t,0,4)=='true');}//SQL
 if(!$bSQL){
  if($p=strpos($sW,"define('UMF_Fragen'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFra=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_Ergebnis'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sErg=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_Zuweisung'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sZuw=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_Nutzer'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sNtz=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_Teilnahme'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sTln=substr($t,0,strpos($t,"'"));}
 }else{
  if($p=strpos($sW,"define('UMF_SqlHost'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlH=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlDaBa'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlD=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlUser'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlU=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlPass'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sSqlP=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlTabF'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sFra=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlTabE'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sErg=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlTabZ'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sZuw=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlTabN'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sNtz=substr($t,0,strpos($t,"'"));}
  if($p=strpos($sW,"define('UMF_SqlTabT'")){$t=substr($sW,strpos($sW,",'",$p)+2,99); $sTln=substr($t,0,strpos($t,"'"));}
 }

 $bFragen=($nFra=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sFra.(!$bSQL?'':'.txt'))),NL),0))>0;
 $bErgebnis=($nErg=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sErg.(!$bSQL?'':'.txt'))),NL)-1,0))>0;
 $bZuweisung=($nZuw=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sZuw.(!$bSQL?'':'.txt'))),NL),0))>0;
 $bNutzer=($nNtz=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sNtz.(!$bSQL?'':'.txt'))),NL),0))>0;
 $bTeilnahme=($nTln=max(substr_count(trim($zip->getFromName((!$bSQL?$sDat:'sql/').$sTln.(!$bSQL?'':'.txt'))),NL),0))>0;

 $sChk='<input type="checkbox" class="admCheck" name="" value="1" checked="checked" />';
 $sHak='<img src="iconHaken.gif" width="13" height="13" border="0" title="gespeichert">';
 if($nSchritt==2) $sHak='<input type="checkbox" class="admCheck" name="" value="1" checked="checked" />';
 $sNein='<span style="color:#cc0000"><b>nein</b></span>'; $sWerte=$sNein; $sFraCB=$sErgCB=$sZuwCB=$sNtzCB=$sTlnCB=$sNein;
 $bWrDir=(is_dir(UMF_Pfad)&&is_writable(UMF_Pfad));
 $sWrBld=(is_dir(UMF_Pfad.$sBld)&&is_writable(UMF_Pfad.$sBld)?($bBilder?str_replace('""','"bild"',$sHak):'--'):$sNein); $nBlW=$nBld;

 if(!$bSQL){
  if(file_exists(UMF_Pfad.$sDat.$sFra)){if(is_writable(UMF_Pfad.$sDat.$sFra)) $sFraCB=str_replace('""','"fragen"',$sHak);}
  elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)) $sFraCB=str_replace('""','"fragen"',$sHak);
  if(file_exists(UMF_Pfad.$sDat.$sErg)){if(is_writable(UMF_Pfad.$sDat.$sErg)) $sErgCB=str_replace('""','"ergebnis"',$sHak);}
  elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)) $sErgCB=str_replace('""','"ergebnis"',$sHak);
  if(file_exists(UMF_Pfad.$sDat.$sZuw)){if(is_writable(UMF_Pfad.$sDat.$sZuw)) $sZuwCB=str_replace('""','"zuweisung"',$sHak);}
  elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)) $sZuwCB=str_replace('""','"zuweisung"',$sHak);
  if(file_exists(UMF_Pfad.$sDat.$sNtz)){if(is_writable(UMF_Pfad.$sDat.$sNtz)) $sNtzCB=str_replace('""','"nutzer"',$sHak);}
  elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)) $sNtzCB=str_replace('""','"nutzer"',$sHak);
  if(file_exists(UMF_Pfad.$sDat.$sTln)){if(is_writable(UMF_Pfad.$sDat.$sTln)) $sTlnCB=str_replace('""','"teilnahme"',$sHak);}
  elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)) $sTlnCB=str_replace('""','"teilnahme"',$sHak);
 }else{
  $DbOT=@new mysqli($sSqlH,$sSqlU,$sSqlP,$sSqlD);
  if(!mysqli_connect_errno()){ //SQL geht zu Oeffnen
   $sFraCB=str_replace('""','"fragen"',$sHak);
   $sErgCB=str_replace('""','"ergebnis"',$sHak);
   $sZuwCB=str_replace('""','"zuweisung"',$sHak);
   $sNtzCB=str_replace('""','"nutzer"',$sHak);
   $sTlnCB=str_replace('""','"teilnahme"',$sHak);
  }else{$DbOT=NULL; $bSQL=false; $bSqlNo=true; //ersatzweise Text-DaBa
   if(file_exists(UMF_Pfad.$sDat.$sFra.'.csv')){if(is_writable(UMF_Pfad.$sDat.$sFra.'.csv')){$sFraCB=str_replace('""','"fragen"',$sHak); $sFra.='.csv';}}
   elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)){$sFraCB=str_replace('""','"fragen"',$sHak); $sFra.='.csv';}
   if(file_exists(UMF_Pfad.$sDat.$sErg.'.csv')){if(is_writable(UMF_Pfad.$sDat.$sErg.'.csv')){$sErgCB=str_replace('""','"ergebnis"',$sHak); $sFol.='.csv';}}
   elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)){$sErgCB=str_replace('""','"ergebnis"',$sHak); $sErg.='.csv';}
   if(file_exists(UMF_Pfad.$sDat.$sZuw.'.csv')){if(is_writable(UMF_Pfad.$sDat.$sZuw.'.csv')){$sZuwCB=str_replace('""','"zuweisung"',$sHak); $sFol.='.csv';}}
   elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)){$sZuwCB=str_replace('""','"zuweisung"',$sHak); $sZuw.='.csv';}
   if(file_exists(UMF_Pfad.$sDat.$sNtz.'.csv')){if(is_writable(UMF_Pfad.$sDat.$sNtz.'.csv')){$sNtzCB=str_replace('""','"nutzer"',$sHak); $sFol.='.csv';}}
   elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)){$sNtzCB=str_replace('""','"nutzer"',$sHak); $sNtz.='.csv';}
   if(file_exists(UMF_Pfad.$sDat.$sTln.'.csv')){if(is_writable(UMF_Pfad.$sDat.$sTln.'.csv')){$sTlnCB=str_replace('""','"teilnahme"',$sHak); $sFol.='.csv';}}
   elseif(is_dir(UMF_Pfad.$sDat)&&is_writable(UMF_Pfad.$sDat)){$sTlnCB=str_replace('""','"teilnahme"',$sHak); $sTln.='.csv';}
  }
 }

 $bVerso=in_array('umfVersion.php',$aZ); $sVerso=$sNein;
 $bStyle=in_array('umfStyle.css',$aZ); $sStyle=$sNein;
 $bIndex=in_array('index.html',$aZ); $sIndex=$sNein;
 $bSeite=in_array('umfSeite.htm',$aZ); $sSeite=$sNein;
 $bFertig=in_array('umfFertig.inc.htm',$aZ); $sFertig=$sNein;

 if(file_exists(UMF_Pfad.$aZ[$nW])){if(is_writable(UMF_Pfad.$aZ[$nW])) $sWerte=($bWerte?str_replace('""','"werte"',$sHak):'--');} elseif($bWrDir) $sWerte=($bWerte?str_replace('""','"werte"',$sHak):'--');
 if(file_exists(UMF_Pfad.'umfVersion.php')){if(is_writable(UMF_Pfad.'umfVersion.php')) $sVerso=($bVerso?str_replace('""','"version"',$sHak):'--');} elseif($bWrDir) $sVerso=($bVerso?str_replace('""','"version"',$sHak):'--');
 if(file_exists(UMF_Pfad.'umfStyle.css')){if(is_writable(UMF_Pfad.'umfStyle.css')) $sStyle=($bStyle?str_replace('""','"style"',$sHak):'--');} elseif($bWrDir) $sStyle=($bStyle?str_replace('""','"style"',$sHak):'--');
 if(file_exists(UMF_Pfad.'index.html')){if(is_writable(UMF_Pfad.'index.html')) $sIndex=($bIndex?str_replace('""','"index"',$sHak):'--');} elseif($bWrDir) $sIndex=($bIndex?str_replace('""','"index"',$sHak):'--');
 if(file_exists(UMF_Pfad.'umfSeite.htm')){if(is_writable(UMF_Pfad.'umfSeite.htm')) $sSeite=($bSeite?str_replace('""','"seite"',$sHak):'--');} elseif($bWrDir) $sSeite=($bSeite?str_replace('""','"seite"',$sHak):'--');
 if(file_exists(UMF_Pfad.'umfFertig.inc.htm')){if(is_writable(UMF_Pfad.'umfFertig.inc.htm')) $sFertig=($bFertig?str_replace('""','"fertig"',$sHak):'--');} elseif($bWrDir) $sFertig=($bFertig?str_replace('""','"fertig"',$sHak):'--');

 if($nSchritt==3){//speichern
  if($sWerte==$sHak&&isset($_POST['werte'])&&$_POST['werte']==1){//umfWerte.php speichern
   if($p=strpos($sW,"define('UMF_Www'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,UMF_Www,$p,$q-$p);}
   if($p=strpos($sW,"define('UMF_Pfad'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,UMF_Pfad,$p,$q-$p);}
   if($bSqlNo){//SQL zu Text umschreiben
    if($p=strpos($sW,"define('UMF_SQL'")){$p=strpos($sW,",",$p)+1; $sW=substr_replace($sW,'false',$p,4);}
    if($p=strpos($sW,"define('UMF_Fragen'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,$sFra,$p,$q-$p);}
    if($p=strpos($sW,"define('UMF_Folgen'")){$p=strpos($sW,",'",$p)+2; $q=strpos($sW,"'",$p); $sW=substr_replace($sW,$sFol,$p,$q-$p);}
   }
   if($f=@fopen(UMF_Pfad.$aZ[$nW],'w')){//umfWerte.php gespeichert
    fwrite($f,str_replace("\r",'',trim($sW))."\n"); fclose($f);
    if(!$bSQL){
     if($sFraCB==$sHak&&isset($_POST['fragen'])&&$_POST['fragen']==1){
      if($s=$zip->getFromName($sDat.$sFra)){
       if(!@file_put_contents(UMF_Pfad.$sDat.$sFra,$s)) $sFraCB=$sNein;
      }else $sFraCB='nein';
     }else $sFraCB='nein';
     if($sErgCB==$sHak&&isset($_POST['ergebnis'])&&$_POST['ergebnis']==1){
      if($s=$zip->getFromName($sDat.$sErg)){
       if(!@file_put_contents(UMF_Pfad.$sDat.$sErg,$s)) $sErgCB=$sNein;
      }else $sErgCB='nein';
     }else $sErgCB='nein';
     if($sZuwCB==$sHak&&isset($_POST['zuweisung'])&&$_POST['zuweisung']==1){
      if($s=$zip->getFromName($sDat.$sZuw)){
       if(!@file_put_contents(UMF_Pfad.$sDat.$sZuw,$s)) $sZuwCB=$sNein;
      }else $sZuwCB='nein';
     }else $sZuwCB='nein';
     if($sNtzCB==$sHak&&isset($_POST['nutzer'])&&$_POST['nutzer']==1){
      if($s=$zip->getFromName($sDat.$sNtz)){
       if(!@file_put_contents(UMF_Pfad.$sDat.$sNtz,$s)) $sNtzCB=$sNein;
      }else $sNtzCB='nein';
     }else $sNtzCB='nein';
     if($sTlnCB==$sHak&&isset($_POST['teilnahme'])&&$_POST['teilnahme']==1){
      if($s=$zip->getFromName($sDat.$sTln)){
       if(!@file_put_contents(UMF_Pfad.$sDat.$sTln,$s)) $sTlnCB=$sNein;
      }else $sTlnCB='nein';
     }else $sTlnCB='nein';
    }elseif($DbOT){//SQL
     if($sFraCB==$sHak&&isset($_POST['fragen'])&&$_POST['fragen']==1){
      if($s=$zip->getFromName('sql/'.$sFra.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sFra); $nAntwAnzahl=max(20,ADU_AntwortZahl);
       $sF=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", Umfrage VARCHAR(31) NOT NULL DEFAULT "", Frage TEXT NOT NULL, Bild VARCHAR(128) NOT NULL DEFAULT "", Anmerkung1 TEXT NOT NULL, Anmerkung2 TEXT NOT NULL';
       for($i=1;$i<=$nAntwAnzahl;$i++) $sF.=', Antwort'.$i.' TEXT NOT NULL'; $sF.=', PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Fragen"';
       if($DbOT->query('CREATE TABLE '.$sFra.$sF)){ //Fragen
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]));
         if(isset($a[1])){
          $s='"'.$a[0].'"'; for($j=1;$j<$nAntwAnzahl+7;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
          if(!$DbOT->query('INSERT IGNORE INTO '.$sFra.' VALUES('.$s.')')) $sFraCB='teilweise';
         }
        }
       }else $sFraCB='nein';
      }else $sFraCB='nein';
     }else $sFraCB='nein';
     if($sErgCB==$sHak&&isset($_POST['ergebnis'])&&$_POST['ergebnis']==1){
      if($s=$zip->getFromName('sql/'.$sErg.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sErg);
       $sF=' (Nummer INT NOT NULL DEFAULT "0", Inhalt TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Ergebnis"';
       if($DbOT->query('CREATE TABLE '.$sErg.$sF)){ //Ergebnisse
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]),2);
         if(isset($a[1])){
          $s='"'.$a[0].'","'.str_replace('"','\"',$a[1]).'"';
          if(!$DbOT->query('INSERT IGNORE INTO '.$sErg.' VALUES('.$s.')')) $sErgCB='teilweise';
         }
        }
       }else $sErgCB='nein';
      }else $sErgCB='nein';
     }else $sErgCB='nein';
     if($sZuwCB==$sHak&&isset($_POST['zuweisung'])&&$_POST['zuweisung']==1){
      if($s=$zip->getFromName('sql/'.$sZuw.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sZuw);
       $sF=' (Benutzer INT NOT NULL DEFAULT "0", Umfragen TEXT NOT NULL, PRIMARY KEY (Benutzer)) COMMENT="UmfrageScript-Zuweisungen"';
       if($DbOT->query('CREATE TABLE '.$sZuw.$sF)){ //Zuweisungen
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]),2);
         if(isset($a[1])){
          $s='"'.$a[0].'","'.str_replace('"','\"',$a[1]).'"';
          if(!$DbOT->query('INSERT IGNORE INTO '.$sZuw.' VALUES('.$s.')')) $sZuwCB='teilweise';
        }}
       }else $sZuwCB='nein';
      }else $sZuwCB='nein';
     }else $sZuwCB='nein';
     if($sNtzCB==$sHak&&isset($_POST['nutzer'])&&$_POST['nutzer']==1){
      if($s=$zip->getFromName('sql/'.$sNtz.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sNtz); $nNtzFld=0;
       if($p=strpos($sW,"define('UMF_NutzerFelder'")){
        $p=strpos($sW,',"',$p)+2; $q=strpos($sW,'"',$p); $a=explode(';',substr($sW,$p,$q-$p)); $nNtzFld=count($a);
       }
       $sF=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", Benutzer VARCHAR(25) NOT NULL DEFAULT "", Passwort VARCHAR(32) NOT NULL DEFAULT "", eMail VARCHAR(100) NOT NULL DEFAULT ""';
       for($i=5;$i<$nNtzFld;$i++) $sF.=', dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""'; $sF.=', PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Benutzer"';
       if($DbOT->query('CREATE TABLE '.$sNtz.$sF)){ //Nutzer
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]));
         if(isset($a[1])){
          $s='"'.$a[0].'","'.$a[1].'","'.str_replace('"','\"',$a[2]).'","'.$a[3].'","'.$a[4].'"';
          for($j=5;$j<$nNtzFld;$j++) $s.=',"'.str_replace('\n ',"\r\n",str_replace('"','\"',$a[$j])).'"';
          if(!$DbOT->query('INSERT IGNORE INTO '.$sNtz.' VALUES('.$s.')')) $sNtzCB='teilweise';
         }
        }
       }else $sNtzCB='nein';
      }else $sNtzCB='nein';
     }else $sNtzCB='nein';
     if($sTlnCB==$sHak&&isset($_POST['teilnahme'])&&$_POST['teilnahme']==1){
      if($s=$zip->getFromName('sql/'.$sTln.'.txt')){
       $DbOT->query('DROP TABLE IF EXISTS '.$sTln);
       $sF=' (Nummer INT NOT NULL auto_increment, Datum CHAR(19) NOT NULL DEFAULT "", Status CHAR(1) NOT NULL DEFAULT "", Art CHAR(1) NOT NULL DEFAULT "", Nutzer TEXT NOT NULL, Ergebnis TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Teilnahme"';
       if($DbOT->query('CREATE TABLE '.$sTln.$sF)){ //Teilnahme
        $aD=explode("\n",$s); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]),5);
         if(isset($a[1])){
          $s='"'.$a[0].'","'.$a[1].'","'.$a[2].'","'.str_replace('"','\"',$a[3]).'","'.str_replace('"','\"',$a[4]).'"';
          if(!$DbOT->query('INSERT IGNORE INTO '.$sTln.' (Datum,Status,Art,Nutzer,Ergebnis) VALUES('.$s.')')) $sTlnCB='teilweise';
        }}
       }else $sTlnCB='nein';
      }else $sTlnCB='nein';
     }else $sTlnCB='nein';
    }//SQL
    if(!strstr($sFraCB,'nein')){ //Fragen ohne Fehler
     if($sWrBld==$sHak&&isset($_POST['bild'])&&$_POST['bild']==1){
      $k=strlen($sBld); $nBlW=0; //Bilder schreiben
      for($i=0;$i<$nZip;$i++) if(substr($aZ[$i],0,$k)==$sBld){
       if($s=$zip->getFromIndex($i)){
        $t=substr($aZ[$i],$k); if($p=strpos($t,'/')){$t=substr($t,0,$p); if(!is_dir(UMF_Pfad.$sBld.$t)) @mkdir(UMF_Pfad.$sBld.$t);}
        if(@file_put_contents(UMF_Pfad.$aZ[$i],$s)) $nBlW++; else $sWrBld=$sNein;
       }else $sWrBld='nein';
      }//for
     }else $sWrBld='nein';
     if($sVerso==$sHak&&isset($_POST['version'])&&$_POST['version']==1){
      if($i=array_search('umfVersion.php',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(UMF_Pfad.'umfVersion.php',$s)) $sVerso=$sNein;
       }else $sVerso='nein';
      }else $sVerso='nein';
     }else $sVerso='nein';
     if($sStyle==$sHak&&isset($_POST['style'])&&$_POST['style']==1){
      if($i=array_search('umfStyle.css',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(UMF_Pfad.'umfStyle.css',$s)) $sStyle=$sNein;
       }else $sStyle='nein';
      }else $sStyle='nein';
     }else $sStyle='nein';
     if($sIndex==$sHak&&isset($_POST['index'])&&$_POST['index']==1){
      if($i=array_search('index.html',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(UMF_Pfad.'index.html',$s)) $sIndex=$sNein;
       }else $sIndex='nein';
      }else $sIndex='nein';
     }else $sIndex='nein';
     if($sSeite==$sHak&&isset($_POST['seite'])&&$_POST['seite']==1){
      if($i=array_search('umfSeite.htm',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(UMF_Pfad.'umfSeite.htm',$s)) $sSeite=$sNein;
       }else $sSeite='nein';
      }else $sSeite='nein';
     }else $sSeite='nein';
     if($sFertig==$sHak&&isset($_POST['fertig'])&&$_POST['fertig']==1){
      if($i=array_search('umfFertig.inc.htm',$aZ)){
       if($s=$zip->getFromIndex($i)){
        if(!@file_put_contents(UMF_Pfad.'umfFertig.inc.htm',$s)) $sFertig=$sNein;
       }else $sFertig='nein';
      }else $sFertig='nein';
     }else $sFertig='nein';
    }else{$sWrBld='nein'; $sFolg='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}//$sData
   }else{$sWerte=$sNein; $sWrBld='nein'; $sFraCB=$sErgCB=$sZuwCB=$sNtzCB=$sTlnCB='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}//fraWerte.php
  }else{$sWerte='nein'; $sWrBld='nein'; $sFraCB=$sErgCB=$sZuwCB=$sNtzCB=$sTlnCB='nein'; $sVerso='nein'; $sStyle='nein'; $sIndex='nein'; $sSeite='nein'; $sFertig='nein';}
  $nSchritt=9;
 }
 $zip->close();
 $sDrin='<img src="iconHaken.gif" width="13" height="13" border="0" title="enthalten">';
?>

<br />
<form action="zipRestore.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="schritt" value="<?php echo $nSchritt?>" />
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1" style="text-align:center">enthalten</td>
  <td class="admSpa1" style="text-align:center"><?php echo ($nSchritt==2?'hochladen':'gespeichert')?></td>
  <td>Objekt</td>
  <td>Erklärung</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bFragen?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sFraCB?></td>
  <td>Fragen<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nFra?> Fragen in der Fragen-Tabelle <i><?php echo (!$bSQL?$sDat.$sFra:$sFra.' (MySQL)')?></i></td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><?php echo($bErgebnis?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sErgCB?></td>
  <td>Ergebnisse<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nErg?> Ergebnisse in der Ergebnis-Tabelle <i><?php echo (!$bSQL?$sDat.$sErg:$sErg.' (MySQL)')?></i></td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><?php echo($bZuweisung?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sZuwCB?></td>
  <td>Zuweisungen<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nZuw?> Zuweisungen in der Zuweisungs-Tabelle <i><?php echo (!$bSQL?$sDat.$sZuw:$sZuw.' (MySQL)')?></i></td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><?php echo($bNutzer?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sNtzCB?></td>
  <td>Benutzer<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nNtz?> Benutzern in der Nutzer-Tabelle <i><?php echo (!$bSQL?$sDat.$sNtz:$sNtz.' (MySQL)')?></i></td>
 </tr>
 <tr class="admTabl">
  <td style="text-align:center"><?php echo($bTeilnahme?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sTlnCB?></td>
  <td>Teilnahmen<?php if($bSqlNo) echo ' (als Text)';?></td>
  <td><?php echo $nTln?> Teilnahmen in der Teilnahme-Tabelle <i><?php echo (!$bSQL?$sDat.$sTln:$sTln.' (MySQL)')?></i></td>
 </tr>
 <tr class="admTabl"><td></td><td></td><td></td><td></td></tr>
 <tr class="admTabl">
  <td style="text-align:center"><?php echo($bBilder?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sWrBld?></td>
  <td><?php echo $nBlW?> Bilder</td>
  <td><?php echo $nBld?> Bilder im Ordner <i><?php echo substr($sBld,0,-1)?></i></td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bWerte?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sWerte?></td>
  <td><?php echo $aZ[$nW]?> *</td>
  <td>zentrale Parameter- und Einstelldatei (* zwingend einzuspielen)</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bVerso?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sVerso?></td>
  <td>umfVersion.php</td>
  <td>Versions-Datei</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bStyle?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sStyle?></td>
  <td>umfStyle.css</td>
  <td>CSS-Styles-Formatierungsdatei</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bIndex?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sIndex?></td>
  <td>index.html</td>
  <td>umhüllendes Frameset</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bSeite?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sSeite?></td>
  <td>umfSeite.htm</td>
  <td>umhüllende HTML-Schablone</td>
 </tr><tr class="admTabl">
  <td style="text-align:center"><?php echo($bFertig?$sDrin:'--')?></td>
  <td style="text-align:center"><?php echo $sFertig?></td>
  <td>umfFertig.inc.htm</td>
  <td>Vorlage für Fertig-Meldung</td>
 </tr>
</table>
<p class="admSubmit"><input style="width:18em;" class="admSubmit" type="submit" value="Konfiguration einspielen"></p>
</form><br /><br />

<?php } ?>

<p><u>Hinweis</u>:</p>
<ul>
<li>Das Laden der Konfiguration erfolgt aus einer hochzuladenden ZIP-Archivdatei,
die zu einem früheren Zeitpunkt mit der Konfigurationssicherung des Umfragen-Scripts erzeugt wurde.</li>
<li>Im ZIP-Archiv enthaltene Daten werden über den momentanen Umfragen übergespielt
und überschreiben je nach Konfiguration eventuell die aktuellen Daten.
Deshalb sollte vor einem Hochladen eventuell die jetzige Konfiguration erst einmal gesichert werden,
falls es sich um eine produktiv genutzte Konfiguration handelt.</li>
<?php if($nSchritt==2){?><li>Ein <span style="color:#cc0000"><b>nein</b></span> in der Spalte <i>hochladen</i> bedeutet, dass das Objekt mangels Rechte nicht gespeichert oder nicht überschrieben werden könnte.</li><?php }?>
</ul>

<?php echo fSeitenFuss();?>