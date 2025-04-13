<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Import von Version 2.x','<script type="text/javascript">
 function fSelAll(bStat){
  for(var i=0;i<self.document.InserateListe.length;++i)
   if(self.document.InserateListe.elements[i].type=="checkbox") self.document.InserateListe.elements[i].checked=bStat;
 }
</script>','Im2');

 $sAltAdr=''; $bAltOK=false; $bSegmente=false; $bLokal=true; $aAltSeg=array(); $bOK=true;
 if($_SERVER['REQUEST_METHOD']=='POST'){
  if($sAltAdr=stripslashes(trim($_POST['AltAdr']))){
   if(substr($sAltAdr,-1,1)!='/'&&substr($sAltAdr,-1,1)!="\\") $sAltAdr.='/';
   if(!$p=strpos($sAltAdr,'://')){ //local
    if(file_exists($sAltAdr.'markt.htm')){
     if(file_exists($sAltAdr.'struktur.txt')){
      $H=opendir(substr($sAltAdr,0,-1));
      while($F=readdir($H)) if($F!='.'&&$F!='..'&&$F!='admin'&&$F!='setup'&&is_dir($sAltAdr.$F)&&file_exists($sAltAdr.$F.'/daten.txt')) $aAltSeg[$F]=true;
      closedir($H); $bAltOK=true;
     }else $Meld='Unter der angegebenen Adresse ist keine Datei <i>struktur.txt</i> zu finden.';
    }else $Meld='Unter der angegebenen Adresse ist keine Datei <i>markt.htm</i> zu finden.';
   }else{ //remote
    $s=substr($sAltAdr,$p+3); $errNo=0; $errStr='';
    if($p=strpos($s,'/')){$sAltH=substr($s,0,$p); $sAltP=substr($s,$p);}else{$sAltH=$s; $sAltP='/';}
    if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
     fputs($Sck,'GET '.$sAltP."markt.htm HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
     while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
     if(strpos($s,'{Inhalt}')){
      if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
       fputs($Sck,'GET '.$sAltP."struktur.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
       while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
       if(strpos($s,"\nD;")){
        if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
         fputs($Sck,'GET '.$sAltP."index.php HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
         while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck); $p=0; $bLokal=false; $bAltOK=true;
         if(strpos($s,'liste.php')){
          while($p=strpos($s,'liste.php?grp=',$p)) if($q=strpos($s,'"',++$p)) $aAltSeg[urldecode(substr($s,$p+13,$q-($p+13)))]=true;
         }else $Meld='Unter der angegebenen Adresse antwortet <i>index.php</i> nicht wie erwartet.';
        }else $Meld='Zu <i>http://'.$sAltH.'</i> kann derzeit keine Verbindung hergestellt werden!';
       }else $Meld='Unter der angegebenen Adresse ist keine Datei <i>struktur.txt</i> zu finden.';
      }else $Meld='Zu <i>http://'.$sAltH.'</i> kann derzeit keine Verbindung hergestellt werden!';
     }else $Meld='Unter der angegebenen Adresse ist keine Datei <i>markt.htm</i> zu finden.';
    }else $Meld='Zu <i>http://'.$sAltH.'</i> kann derzeit keine Verbindung hergestellt werden!</p><p>'.$errNo.' '.$errStr.'';
   }//remote
  }else $Meld='Geben Sie einen Ort des alten Markt-Scripts Version 2.x an!';

  if(count($aAltSeg)){//alte Segmente gefunden
   if(!isset($_POST['seg'])){
    $Meld='Bitte wählen Sie nun die zu importierenden Segmente des alten Marktes aus.'; $MTyp='Meld';
   }else{
    $aAltSeg=$_POST['seg']; $bSegmente=true; $nAltCC=-1; $Meld='Importergebnis:'; $MTyp='Meld';
    if($bLokal) if(($sWerte=strstr(@join('',@file($sAltAdr.'werte.php')),'$CryptCode'))&&($p=strpos($sWerte,'=')))
     $nAltCC=(int)substr($sWerte,$p+1,2); //alten CryptCode holen
    $aSegmente=explode(';',MP_Segmente); $aAnordnung=explode(';',MP_Anordnung); $nNeuSeg=count($aSegmente);
    $nNeuPos=0; for($i=0;$i<$nNeuSeg;$i++) $nNeuPos=max($aAnordnung[$i],$nNeuPos);
    $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php'))));
  }}elseif($bAltOK) $Meld='Es wurden keine Segmente im alten Marktplatz gefunden.';

  if($bSegmente) for($i=0,$nSegZahl=count($aAltSeg);$i<$nSegZahl;$i++) if($bOK){ //importieren
   $sSeg=$aAltSeg[$i]; $sSub=''; $aD=array(); $aE=array();
   if($bLokal){//lokal
    $sSub=$sSeg; $aD=file($sAltAdr.$sSub.'/daten.txt'); $aE=file($sAltAdr.$sSub.'/eingabe.txt');
   }else{//remote
    for($j=0,$n=strlen($sSeg);$j<$n;$j++){$c=substr($sSeg,$j,1);
     if($c<='~') $sSub.=$c; else{$c=iconv('ISO-8859-1','UTF-8',$c); for($k=0,$m=strlen($c);$k<$m;$k++) $sSub.='%'.strtoupper(dechex(ord(substr($c,$k,1))));}
    }
    if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
     fputs($Sck,'GET '.$sAltP.$sSub."/daten.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
     while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
     if(strpos($s,"\nD;")){
      $aD=explode("\n",str_replace("\r",'',trim(strstr($s,"\r\n\r\n"))));
      if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
       fputs($Sck,'GET '.$sAltP.$sSub."/eingabe.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
       while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck); $aE=explode("\n",str_replace("\r",'',trim(strstr($s,"\r\n\r\n"))));
   }}}}//remote
   if(isset($aD[1])&&substr($aD[1],0,2)=='D;'){//Daten gelesen
    $s=str_replace('c','p',str_replace('p','w',str_replace('o','f',str_replace('z','r',str_replace('i','n',strtolower(rtrim($aD[1])))))));
    $aFN=explode(';',rtrim($aD[0])); $aFT=explode(';',$s); $aET=explode(';',rtrim($aE[0])); $nFelder=count($aFN);
    $sStru=    'FeldName    : Nummer'; for($j=0;$j<$nFelder;$j++) $sStru.=';'.$aFN[$j];
    $sStru.=NL.'FeldTyp     : i';   for($j=0;$j<$nFelder;$j++) $sStru.=';'.$aFT[$j];
    $sStru.=NL.'ListenFeld  : 1;1'; $k=1; for($j=1;$j<$nFelder;$j++) $sStru.=';'.(($aFT[$j]=='t'||$aFT[$j]=='w'||$aFT[$j]=='b')?++$k:'0');
    $sStru.=NL.'NutzerLF    : 1;1'; $k=1; for($j=1;$j<$nFelder;$j++) $sStru.=';'.(($aFT[$j]=='t'||$aFT[$j]=='w'||$aFT[$j]=='b')?++$k:'0');
    $sStru.=NL.'SortierFeld : 0;0'; for($j=1;$j<$nFelder;$j++) $sStru.=';0';
    $sStru.=NL.'LinkFeld    : 0;0'; for($j=1;$j<$nFelder;$j++) $sStru.=';'.($aFT[$j]=='b'?'1':'0');
    $sStru.=NL.'SpaltenStil : ';    for($j=0;$j<$nFelder;$j++) $sStru.=';';
    $sStru.=NL.'DetailFeld  : 1';   for($j=0;$j<$nFelder;$j++) $sStru.=';'.($aFT[$j]!='p'?'1':'0');
    $sStru.=NL.'NutzerDF    : 1';   for($j=0;$j<$nFelder;$j++) $sStru.=';'.($aFT[$j]!='p'?'1':'0');
    $sStru.=NL.'ZeilenStil  : ';    for($j=0;$j<$nFelder;$j++) $sStru.=';';
    $sStru.=NL.'SuchFeld    : 1';   for($j=0;$j<$nFelder;$j++) $sStru.=';'.(($aFT[$j]!='b'&&$aFT[$j]!='f'&&$aFT[$j]!='p')?'1':'0'); ;
    $sStru.=NL.'EingabeFeld : 1';   for($j=0;$j<$nFelder;$j++) $sStru.=';1';
    $sStru.=NL.'NutzerEF    : 1';   for($j=0;$j<$nFelder;$j++) $sStru.=';1';
    $sStru.=NL.'PflichtFeld : 1;1'; for($j=1;$j<$nFelder;$j++) $sStru.=';'.($aFT[$j]!='p'?'0':'1');
    $sStru.=NL.'TrennZeile  : 0';   for($j=0;$j<$nFelder;$j++) $sStru.=';0';
    $sStru.=NL.'EingabeTexte: ;28'; for($j=1;$j<$nFelder;$j++) $sStru.=';'.$aET[$j];
    $sStru.=NL.'AuswahlWerte: ';    for($j=0;$j<$nFelder;$j++){$sStru.=';'; if($aFT[$j]=='a') $sStru.=str_replace(';','|',rtrim($aE[$j])); }
    $sStru.=NL.'Kategorien  : ';
    $sStru.=NL.'Symbole     : ';
    $sStru.=NL.'EingabeLang : 0';   for($j=0;$j<$nFelder;$j++) $sStru.=';0';
    $sNeuSeg=sprintf('%02d',$nNeuSeg); $aBld=array(); $aObj=array(); $nSaetze=count($aD); $z=1; $sRefDat=date('y-m-d'); $bSQLFehl=false;
    if(!MP_SQL){$aTmp=array(); //Text
     for($j=3;$j<$nSaetze;$j++){
      $a=explode(';',rtrim($aD[$j]));
      if($a[0]>=$sRefDat){$sZl='20'.$a[0];
       for($k=1;$k<$nFelder;$k++){$sZl.=';';
        if($s=$a[$k]){$t=$aFT[$k]; $p=0;
         switch($t){
          case 'b': $sZl.=fMpDateiname($s.'|'.$s); $aBld[$z]=$s; break; case 'f': $sZl.=fMpDateiname($s); $aObj[$z]=$s; break;
          case 'p': $sZl.=fMpEnCode($nAltCC>=0?DeCryptOldPw($s,$nAltCC):'???'); break;
          default:{
           while($p=strpos($s,'[size=',$p)){
            $p+=6; $n=(int)substr($s,$p,2);
            if($n<=9) $s=substr_replace($s,'80',$p,1);
            elseif($n<=10) $s=substr_replace($s,'90',$p,2);
            elseif($n>=14) $s=substr_replace($s,'120',$p,2);
            elseif($n>=12) $s=substr_replace($s,'110',$p,2);
            else $s=substr_replace($s,'100',$p,2);
           }
           $sZl.=str_replace('|','\n ',str_replace('"',"'",str_replace(chr(127),'`,',$s)));
       }}}}
       $aTmp[$z++]=$sZl;
     }}
     asort($aTmp); reset($aTmp); $aD=array('x'); foreach($aTmp as $k=>$v) $aD[]=$k.';1;'.$v.NL;
     $sZl='Nummer_'.count($aTmp).';online'; for($j=0;$j<$nFelder;$j++) $sZl.=';'.$aFN[$j]; $aD[0]=$sZl.NL;
     if($f=fopen(MP_Pfad.MP_Daten.$sNeuSeg.MP_Struktur,'w')){
      fwrite($f,trim($sStru)); fclose($f); $aSegmente[]=$sSeg; $aAnordnung[]=++$nNeuPos;
      if($f=fopen(MP_Pfad.MP_Daten.$sNeuSeg.MP_Inserate,'w')){fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);}
       else{$bOK=false; $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sNeuSeg.MP_Inserate.'</i> durfte nicht angelegt werden!';}
     }else{$bOK=false; $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sNeuSeg.MP_Struktur.'</i> durfte nicht angelegt werden!';}
    }elseif($DbO){//SQL
     $DbO->query('DROP TABLE IF EXISTS '.str_replace('%',$sNeuSeg,MP_SqlTabI)); include('feldtypenInc.php');
     $s=''; for($k=1;$k<$nFelder;$k++) $s.=', mp_'.($k+1).' '.$aSql[$aFT[$k]];
     if($DbO->query('CREATE TABLE '.str_replace('%',$sNeuSeg,MP_SqlTabI).'(nr int(11) NOT NULL auto_increment, online char(1) NOT NULL DEFAULT "0", mp_1 char(10) NOT NULL DEFAULT ""'.$s.', PRIMARY KEY (nr), KEY mp_1 (mp_1)) COMMENT="Markplatz-Inserate-'.$sNeuSeg.'"')){
      for($j=3;$j<$nSaetze;$j++){
       $a=explode(';',rtrim($aD[$j]));
       if($a[0]>=$sRefDat){
        $sZl='"'.($z).'","1","20'.$a[0].'"';
        for($k=1;$k<$nFelder;$k++){
         if($s=$a[$k]){$t=$aFT[$k]; $p=0;
          switch($t){
           case 'b': $aBld[$z]=$s; $s=fMpDateiname($s); $s.='|'.$s; break; case 'f': $aObj[$z]=$s; $s=fMpDateiname($s); break;
           case 'p': $s=fMpEnCode($nAltCC>=0?DeCryptOldPw($s,$nAltCC):'???'); break;
           default:{
            while($p=strpos($s,'[size=',$p)){
             $p+=6; $n=(int)substr($s,$p,2);
             if($n<=9) $s=substr_replace($s,'80',$p,1);
             elseif($n<=10) $s=substr_replace($s,'90',$p,2);
             elseif($n>=14) $s=substr_replace($s,'120',$p,2);
             elseif($n>=12) $s=substr_replace($s,'110',$p,2);
             else $s=substr_replace($s,'100',$p,2);
            }
            $s=str_replace('|',"\r\n",str_replace(chr(127),';',$s));
         }}}
         $sZl.=',"'.str_replace('"','\"',$s).'"';
        }
        $z++; if(!$DbO->query('INSERT IGNORE INTO '.str_replace('%',$sNeuSeg,MP_SqlTabI).' VALUES('.$sZl.')')) $bSQLFehl=true;
      }}
     }else{$bOK=false; $Meld.='</p><p class="admFehl">Die Inseratetabelle-<i>'.$sNeuSeg.'</i> konnte nicht angelegt werden!';}
     if($DbO->query('UPDATE IGNORE '.MP_SqlTabS.' SET struktur="'.str_replace('"','\"',trim($sStru)).'" WHERE nr='.$nNeuSeg)){
      $aSegmente[]=$sSeg; $aAnordnung[]=++$nNeuPos;
     }else{$bOK=false; $Meld.='</p><p class="admFehl">Die Strukturinformation <i>'.$sNeuSeg.'</i> konnte nicht gespeichert werden!';}
    }//SQL
    if($bOK){
     if(MP_BldTrennen){
      $sBldDir=$sNeuSeg.'/'; $sBldSeg='';
      if(!file_exists(MP_Pfad.MP_Bilder.$sNeuSeg)) if(!mkdir(MP_Pfad.MP_Bilder.$sNeuSeg,0777)){
       $bOK=false; $Meld.='</p><p class="admFehl">Der Bilderunterordner <i>'.MP_Bilder.$sNeuSeg.'</i> konnte nicht angelegt werden!';
     }}else{$sBldDir=''; $sBldSeg=$sNeuSeg;}
    }
    if($bOK) if($p=strpos($sWerte,"define('MP_Anordnung'")) if($q=strpos($sWerte,"\n",$p)){ //Zwischenspeichern
     $sWerte=substr_replace($sWerte,"define('MP_Anordnung','".implode(';',$aAnordnung)."');",$p,$q-$p);
     if($p=strpos($sWerte,"define('MP_Segmente'")) if($q=strpos($sWerte,"\n",$p))
      $sWerte=substr_replace($sWerte,"define('MP_Segmente',\"".implode(';',$aSegmente).'");',$p,$q-$p);
     if($f=fopen(MP_Pfad.'mpWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
      $Meld.='</p><p class="admErfo">Das Segment <i>'.$sSeg.'</i> aus dem alten Markt wurde umgespeichert.';
     }else{$bOK=false; $Meld.='</p><p class="admFehl">'.str_replace('#','mpWerte.php',MP_TxDateiRechte);}
    }
    if($bSQLFehl) $Meld.='</p><p class="admFehl">Nicht alle Inserate im Segment <i>'.$sSeg.' ('.$sNeuSeg.')</i> konnten umgespeichert werden!';
    $bFehl=false; //Bilder holen
    if($bOK) foreach($aBld as $k=>$s){
     $UpEx=strtolower(strrchr($s,'.'));
     if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($sAltAdr.$sSub.'/gross/'.$s);
     elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($sAltAdr.$sSub.'/gross/'.$s);
     elseif($UpEx=='.png')$Src=ImageCreateFromPNG($sAltAdr.$sSub.'/gross/'.$s);
     if(!empty($Src)){
      $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=fMpDateiname(substr($s,0,-1*strlen($UpEx)));
      if($Sx>MP_VorschauBreit||$Sy>MP_VorschauHoch){ //Vorschau verkleinern
       $Dw=min(MP_VorschauBreit,$Sx);
       if($Sx>MP_VorschauBreit) $Dh=round(MP_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
       if($Dh>MP_VorschauHoch){$Dw=round(MP_VorschauHoch/$Dh*$Dw); $Dh=MP_VorschauHoch;}
       $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
       ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
       if(!@imagejpeg($Dest,MP_Pfad.MP_Bilder.$sBldDir.$k.$sBldSeg.'-'.$UpBa.'.jpg',100)) $bFehl=true;
       imagedestroy($Dest); unset($Dest);
      }else{
       if(!@copy($sAltAdr.$sSub.'/gross/'.$s,MP_Pfad.MP_Bilder.$sBldDir.$k.$sBldSeg.'-'.$UpBa.$UpEx)) $bFehl=true;
      }
      if($Sx>MP_BildBreit||$Sy>MP_BildHoch){ //Bild verkleinern
       $Dw=min(MP_BildBreit,$Sx);
       if($Sx>MP_BildBreit) $Dh=round(MP_BildBreit/$Sx*$Sy); else $Dh=$Sy;
       if($Dh>MP_BildHoch){$Dw=round(MP_BildHoch/$Dh*$Dw); $Dh=MP_BildHoch;}
       $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
       ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
       @imagejpeg($Dest,MP_Pfad.MP_Bilder.$sBldDir.$k.$sBldSeg.'_'.$UpBa.'.jpg');
       imagedestroy($Dest); unset($Dest);
      }else{@copy($sAltAdr.$sSub.'/gross/'.$s,MP_Pfad.MP_Bilder.$sBldDir.$k.$sBldSeg.'_'.$UpBa.$UpEx);}
      imagedestroy($Src); unset($Src);
     }else $bFehl=true;
    }
    if($bFehl) $Meld.='</p><p class="admFehl">Nicht alle Bilder im Segment <i>'.$sSeg.'</i> konnten kopiert werden.';
    $bFehl=false;
    if($bOK) foreach($aObj as $k=>$s)
     if(!copy($sAltAdr.$sSub.'/objekte/'.$s,MP_Pfad.MP_Bilder.$sBldDir.$k.$sBldSeg.'~'.fMpDateiname($s))) $bFehl=true;
    if($bFehl) $Meld.='</p><p class="admFehl">Nicht alle Dateianhänge im Segment <i>'.$sSeg.'</i> konnten kopiert werden.';
    if(++$nNeuSeg>99){$bOK=false; $Meld.='</p><p class="admFehl">Die maximale Anzahl von 99 Segmenten ist erreicht!';}
   }else $Meld.='</p><p class="admFehl">Die Daten im alten Segment <i>'.$sSeg.'</i> entsprechen nicht dem erwarteten Format.';
  }//importieren
 }//POST

 if(!$Meld){$Meld='Sie können Segmente und Inserate aus einer älteren Version 2.x des Markt-Scripts importieren.'; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>';
?>

<form action="importVer2.php" method="post">

<?php
if(!$bAltOK){ //noch kein gültiger Pfad
?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">Tragen Sie den Ort des alten Marktplatzes Version 2.x
als absoluten lokalen Pfad oder als fernen URL ein.
Ein lokaler Pfad könnte beispielsweise lauten: <i>/var/kunden/webs/www.ein-web.de/htdocs/markt</i>,
eine ferne Adresse beispielsweise <i>http://www.mein-web.de/markt</i>.
</td></tr>
<tr class="admTabl">
 <td width="1%">Importadresse</td>
 <td><input class="admEing" style="width:98%" type="text" name="AltAdr" value="<?php echo $sAltAdr?>" /></td>
</tr>
<tr class="admTabl"><td class="admSpa2" colspan="2"><u>Information</u>:
Dieses Marktplatz-Script 3.x liegt im absoluten lokalen Pfad
<i><?php echo substr(MP_Pfad,0,-1)?></i> bzw. unter der URL-Adresse <i><?php echo substr(MP_Www,0,-1)?></i>.
</td></tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>

<?php
 }elseif(!$bSegmente){ //noch keine Segmente ausgewählt
?>

<input type="hidden" name="AltAdr" value="<?php echo $sAltAdr?>" />
<div style="text-align:center">
<table class="admTabl" style="width:8%" align="center" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td width="1%"><b>importieren</b></td><td style="white-space:nowrap;"><b>Segmentname im alten Markt-Script</b></td>
 </tr>
<?php
 foreach($aAltSeg as $sSeg=>$xx){
  echo ' <tr class="admTabl">'.NL;
  echo '  <td align="center"><input class="admCheck" type="checkbox" name="seg[]" value="'.$sSeg.'" /></td>'.NL;
  echo '  <td style="white-space:nowrap;">'.$sSeg.'</td>'.NL;
  echo' </tr>'.NL;
 }
?>
</table>
</div>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>

<?php
 }else{ //Import
?>

<?php
 }
?>

</form>

<div class="admBox"><p>Hinweise:</p>
<p>Marktsegmente und Inserate können aus einem lokalen Pfad des selben Servers oder von einem fernen Server importiert werden.
Beim Import vom selben Server ist der absolute Pfad anzugeben. Ein solcher Import verläuft vollständig.
Der Import vom fernen Server erfolgt über den URL des alten Marktplatz-Scripts.
Allerdings können dabei die eventuell in den Inseraten vorhandenen verschlüsselten Passworte <i>nicht</i> importiert werden.
Ein lokaler Import ist also stets vorzuziehen.</p>
<p>Bei einem Import von Segmenten und Inseraten aus der Version 2.x werden die alte Inseratestruktur und die alten Inseratedaten übernommen.
Die zu importierenden Marktsegmente aus Version 2.x werden im Marktplatz 3.x neu angelegt, selbst wenn es bereits ein gleichnamiges Martsegment geben sollte.</p>
<p>Es werden nur die Inseratestruktur und die Inseratedaten importiert,
<i>nicht</i> die kompletten Segmenteigenschaften oder Einstellungen zu den Marktpatzfunktionen, zu den Farben oder zum Layout.
Alle Einstellungen wie z.B. Anzeigedauer, Pflichtfelder, Sortierfelder, Währungszeichen, Bildgrößen usw.
werden nicht von der Version 2.x übernommen sondern bleiben mit deren momentanen Werten erhalten.
Setzen Sie also z.B. die Parameter für die Bildgröße <i>vor</i> dem Import auf gewünschte Werte
<i>und</i> bearbeiten Sie die Segmenteigenschaften <i>jedes einzelnen importierten Segmentes</i> unbedingt nach.</p>
<p>Falls Ihr Markt in der Version 2.x sehr viele Bilder enthält kann der Import je nach Leistung des Servers länger dauern.
Auf üblichen Servern wird aber die Ausführungszeit für ein PHP-Script vom Provider auf meist maximal 30 Sekunden begrenzt.
Überschreitet die Importdauer dieses Zeitlimit, wird Ihr Server den Import abbrechen.
Verluste an den Daten dieser Marktplatzversion 3.x sind bei solch einem unkontrollierten Abbruch nicht auszuschließen!
Importieren Sie bei vielen Bildern am besten jeweils immer nur ein oder wenige Segmente auf einmal.
Die Daten des alten Marktes Version 2.x werden jedoch keinesfalls beschädigt.</p>
</div>

<?php
echo fSeitenFuss();

function DeCryptOldPw($Pw,$CC){
 $m=0; $Out=''; for($k=strlen($Pw)/2-1;$k>=0;$k--) $Out.=chr($CC+($m++)+hexdec(substr($Pw,$k+$k,2)));
 return $Out;
}

function fMpDateiname($s){
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}
?>