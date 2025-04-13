<?php
 global $nSegNo,$sSegNo,$sSegNam;
 if(file_exists('hilfsFunktionen.php')) include 'hilfsFunktionen.php';
 echo fSeitenKopf('Segmente bearbeiten','<script type="text/javascript">
 function bldWin(sURL){bWin=window.open(sURL,"sbild","width=300,height=275,left=1,top=9,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");bWin.focus();}
</script>','KSg');

$mpSegmente=MP_Segmente; $mpAnordnung=MP_Anordnung; $nLsch=0; $UpNr='';
if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 if(MP_Pfad>''){
  $Meld='Bearbeiten Sie die aktuellen Segmente Ihres Marktplatzes.'; $MTyp='Meld';
 }else $Meld='Bitte zuerst die Pfade im Setup einstellen!';
}else{//POST
 if(isset($_POST['BldUp'])){//Sinnbild hochladen
  if(isset($_POST['BldNum'])&&($UpNr=$_POST['BldNum'])){
   $UpNa=fMpDateiname(basename($_FILES['BldNa']['name'])); $UpEx=strtolower(strrchr($UpNa,'.')); $UpNr=sprintf('%02d',$UpNr);
   if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
    $aF=array();
    if($f=opendir(MP_Pfad.MP_Daten)){
     while($s=readdir($f)) if(substr($s,0,3)==$UpNr.'_') $aF[]=$s; closedir($f);
    }
    foreach($aF as $s) @unlink(MP_Pfad.MP_Daten.$s);
    if(@copy($_FILES['BldNa']['tmp_name'],MP_Pfad.MP_Daten.$UpNr.'_'.$UpNa)){
     $Meld='Das Bild zum Segment <i>'.$UpNr.'</i> wurde abgelegt.'; $MTyp='Erfo';
    }else $Meld='Das Bild zum Segment <i>'.$UpNr.'</i> konnte nicht im Ordner <i>'.MP_Daten.'</i> abgelegt werden.';
   }else $Meld='Es müssen Bilder vom Typ JPG, GIF oder PNG hochgeladen werden.';
  }else $Meld='Bitte immer auch die interne Segment-Nr. angeben!';
 }elseif(isset($_POST['BldDl'])){//Sinnbild loeschen
  if(isset($_POST['BldNum'])&&($UpNr=$_POST['BldNum'])){
   $UpNr=sprintf('%02d',$UpNr);
   if(isset($_POST['BldDel'])&&($UpDl=$_POST['BldDel'])){
    $aF=array();
    if($f=opendir(MP_Pfad.MP_Daten)){
     while($s=readdir($f)) if(substr($s,0,3)==$UpNr.'_') $aF[]=$s; closedir($f);
    }
    if(count($aF)){
     foreach($aF as $s) @unlink(MP_Pfad.MP_Daten.$s);
     $Meld='Die Bilder zum Segment <i>'.$UpNr.'</i> wurden gelöscht.'; $MTyp='Erfo';
    }else{$Meld='Es sind keine Bilder im Segment <i>'.$UpNr.'</i> zu löschen.'; $MTyp='Meld';}
   }else $Meld='Bitte auswählen, dass Sie wirklich Bilder löschen wollen!';
  }else $Meld='Bitte immer auch die interne Segment-Nr. angeben!';
 }elseif(isset($_POST['Lsch'])&&($nLsch=$_POST['Lsch'])){ //Segment loeschen
  $aS=explode(';',$mpSegmente); $sLsch=sprintf('%02d',$nLsch);
  if(!isset($_POST['LNun'])||($nLsch!=$_POST['LNun'])){
   $Meld='Das Segment <i>'.$aS[$nLsch].'</i> wirklich komplett löschen?';
  }else{ //nun loeschen
   $Meld='Das Segment <i>'.$aS[$nLsch].'</i> wurde gelöscht.';
   $aS[$nLsch]=''; $aA=explode(';',$mpAnordnung); $nAltPos=$aA[$nLsch]; $aA[$nLsch]='0';
   $n=count($aA); for($i=1;$i<$n;$i++) if($aA[$i]>$nAltPos) --$aA[$i];
   $mpSegmente=implode(';',$aS); $mpAnordnung=implode(';',$aA);
   while(substr($mpAnordnung,-2,2)==';0'){$mpAnordnung=substr($mpAnordnung,0,-2); $mpSegmente=substr($mpSegmente,0,-1);}
   $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php'))));
   fSetzMPWert($mpSegmente,'Segmente','"'); fSetzMPWert($mpAnordnung,'Anordnung',"'");
   if(file_exists(MP_Pfad.'mpWerte.php')&&is_writable(MP_Pfad.'mpWerte.php')&&($f=fopen(MP_Pfad.'mpWerte.php','w'))){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $MTyp='Erfo';
    if(MP_SQL) if($DbO){//SQL
     $DbO->query('DROP TABLE IF EXISTS '.str_replace('%',$sLsch,MP_SqlTabI));
     $DbO->query('UPDATE IGNORE '.MP_SqlTabS.' SET struktur="" WHERE nr='.$nLsch);
    }else{$Meld=MP_TxSqlVrbdg; $MTyp='Fehl';}
    if($f=opendir(MP_Pfad.MP_Daten)){$a=array();//Daten des Segments
     while($s=readdir($f))if(substr($s,0,2)==$sLsch) $a[]=$s; closedir($f); clearstatcache();
     foreach($a as $s) @unlink(MP_Pfad.MP_Daten.$s);
    }
    if(MP_BldTrennen){$sBldDir=$sLsch.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sLsch;}
    if($f=opendir(MP_Pfad.substr(MP_Bilder.$sBldDir,0,-1))){$a=array();//Bilder
     while($s=readdir($f)) if($i=(int)$s) if(MP_BldTrennen) $a[]=$s; elseif(substr($i,-2)==$sLsch) $a[]=$s;
     closedir($f); clearstatcache();
     foreach($a as $s) @unlink(MP_Pfad.MP_Bilder.$sBldDir.$s); if(MP_BldTrennen) @rmdir(MP_Pfad.MP_Bilder.$sLsch);
   }}else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
  }
 }elseif($sNeuNam=fSegNam($_POST['nNeu'])){//Segment neu anlegen
  $aS=explode(';',$mpSegmente); $aA=explode(';',$mpAnordnung); asort($aA); reset($aA); $nPos=0; $nNeuNr=100;
  foreach($aA as $k=>$v) if($v!=0) ++$nPos; elseif($k!=0) $nNeuNr=min($k,$nNeuNr);
  $nNeuNr=min(++$nPos,$nNeuNr); $aS[$nNeuNr]=$sNeuNam; ksort($aA); reset($aA);
  if($nPNeu=(int)$_POST['pNeu']){
   $nPNeu=min($nPNeu,$nPos);
   if($nPNeu!=$nPos){$n=count($aA); for($i=1;$i<$n;$i++) if($aA[$i]>=$nPNeu) ++$aA[$i];}
   $aA[$nNeuNr]=$nPNeu;
  }else $aA[$nNeuNr]=$nPos;
  $mpSegmente=implode(';',$aS); $mpAnordnung=implode(';',$aA);
  $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php'))));
  fSetzMPWert($mpSegmente,'Segmente','"'); fSetzMPWert($mpAnordnung,'Anordnung',"'");
  if(file_exists(MP_Pfad.'mpWerte.php')&&is_writable(MP_Pfad.'mpWerte.php')&&($f=fopen(MP_Pfad.'mpWerte.php','w'))){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);//Segmentname eingetragen
   $Meld='Das Segment <i>'.$sNeuNam.'</i> wurde angelegt.'; $sNeuNr=sprintf('%02d',$nNeuNr);
   if($sNeuNam!='LEER'){
    if(!MP_SQL){//Text
     if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)&&($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur,'r'))){
      $s=rtrim(fgets($f)).NL; fclose($f); $s='Nummer_0;online'.strstr($s,';');//Feldnamen gelesen
      if(is_writable(MP_Pfad.MP_Daten)&&@copy(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur,MP_Pfad.MP_Daten.$sNeuNr.MP_Struktur)){//Struktur
       if($f=fopen(MP_Pfad.MP_Daten.$sNeuNr.MP_Inserate,'w')){//Inserate
        fwrite($f,$s); fclose($f); $MTyp='Erfo';
       }else $Meld=str_replace('#',MP_Daten.$sNeuNr.MP_Inserate,MP_TxDateiRechte);
      }else $Meld=str_replace('#',MP_Daten.$sNeuNr.MP_Struktur,MP_TxDateiRechte);
     }else $Meld='Die Mustervorlage '.MP_Daten.$sSegNo.MP_Struktur.' kann nicht gelesen werden.';
    }elseif($DbO){//SQL
     if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr='.$nSegNo)){
      $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
      if($i==1&&strlen($a[1])>2){
       if($DbO->query('UPDATE IGNORE '.MP_SqlTabS.' SET struktur="'.str_replace('"','\"',rtrim($a[1])).'" WHERE nr='.$nNeuNr)){
        $DbO->query('DROP TABLE IF EXISTS '.str_replace('%',$sNeuNr,MP_SqlTabI));
        $a=explode("\n",rtrim($a[1])); $aFT=explode(';',rtrim($a[1])); $nFelder=count($aFT); include('feldtypenInc.php');
        $s=''; for($i=2;$i<$nFelder;$i++) $s.=', mp_'.$i.' '.$aSql[$aFT[$i]];
        if($DbO->query('CREATE TABLE '.str_replace('%',$sNeuNr,MP_SqlTabI).'(nr int(11) NOT NULL auto_increment, online char(1) NOT NULL DEFAULT "0", mp_1 char(10) NOT NULL DEFAULT ""'.$s.', PRIMARY KEY (nr), KEY mp_1 (mp_1)) COMMENT="Markplatz-Inserate-'.$sNeuNr.'"')){
         $MTyp='Erfo';
        }else $Meld=MP_TxSqlEinfg;
       }else $Meld=MP_TxSqlAendr;
      }else $Meld=MP_TxSqlFrage;
     }else $Meld=MP_TxSqlFrage;
    }else $Meld=MP_TxSqlVrbdg;
    if(MP_BldTrennen&&$MTyp=='Erfo'&&!file_exists(MP_Pfad.MP_Bilder.$sNeuNr)) if(!@mkdir(MP_Pfad.MP_Bilder.$sNeuNr,0777)) $Meld.='</p><p class="admFehl">Der Bilderunterordner <i>'.MP_Bilder.$sNeuNr.'</i> jedoch konnte nicht angelegt werden.';
   }
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }elseif(isset($_POST['SegAend'])){//Segment aendern
  reset($_POST); $aS=explode(';',$mpSegmente); $aA=explode(';',$mpAnordnung); $aN=array(0); $nMx=0; $bNeu=false;
  foreach($_POST as $k=>$v){
   $sF=substr($k,0,1); $n=(int)substr($k,1,2);
   if($sF=='N'&&!empty($v)){//Name
    $v=fSegNam($v);
    if(isset($_POST['U'.substr($k,1,2)])) $v='*'.$v; elseif(isset($_POST['O'.substr($k,1,2)])) $v='~'.$v;
    if($aS[$n]!=$v&&$aS[$n]!='LEER'){$aS[$n]=$v; $bNeu=true;}
   }
   elseif($sF=='P'){//Position
    if(!empty($v)){if($v==$aA[$n]) $aN[$n]=(int)$v; elseif($v<$aA[$n]) $aN[$n]=$v-0.5; else $aN[$n]=$v+0.5;}
    else $aN[$n]=$aA[$n];
    $nMx=max($n,$nMx);
   }elseif($sF=='U'){//nur fuer Benutzer
    if(substr($aS[$n],0,1)=='~'){$aS[$n]=substr($aS[$n],1); $bNeu=true;}
    if(substr($aS[$n],0,1)!='*'){$aS[$n]='*'.$aS[$n]; $bNeu=true;}
   }elseif($sF=='O'){//offline
    if(substr($aS[$n],0,1)=='*'){$aS[$n]=substr($aS[$n],1); $bNeu=true;}
    if(substr($aS[$n],0,1)!='~'){$aS[$n]='~'.$aS[$n]; $bNeu=true;}
   }
  }
  for($i=1;$i<=$nMx;$i++) if(!isset($aN[$i])) $aN[$i]=0; asort($aN); reset($aN); $i=0;//Positionen behandeln
  foreach($aN as $k=>$v) if($v>0) $aN[$k]=++$i; else $aN[$k]=0; ksort($aN);
  if($aA!=$aN){$aA=$aN; $bNeu=true;}
  if($bNeu){//Aenderungen speichern
   $mpSegmente=implode(';',$aS); $mpAnordnung=implode(';',$aA);
   $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php'))));
   fSetzMPWert($mpSegmente,'Segmente','"'); fSetzMPWert($mpAnordnung,'Anordnung',"'");
   if(file_exists(MP_Pfad.'mpWerte.php')&&is_writable(MP_Pfad.'mpWerte.php')&&($f=fopen(MP_Pfad.'mpWerte.php','w'))){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    $Meld='Die Änderungen an den Segmenten wurden eingetragen.'; $MTyp='Erfo';
   }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }}
 if(empty($Meld)){$Meld='Die Segmente bleiben unverändert.'; $MTyp='Meld';}
}

//Seitenausgabe
if(!is_dir(MP_Pfad.MP_Daten)){$Meld='Bitte zuerst die Pfade im Setup einstellen!'; $MTyp='Fehl';}
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form action="konfSegmente.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<input type="hidden" name="SegAend" value="x" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td align="center" width="8%"><b>Position</b></td>
 <td align="center" width="8%"><b>interne&nbsp;Nr.</b></td>
 <td><b>Segmentname</b></td>
 <td><b>inaktiv&nbsp;/&nbsp;nur<br />für&nbsp;Benutzer</b></td>
 <td><b>Segmentsinnbild</b></td>
 <td align="center" width="8%"><b>löschen</b></td>
</tr>
<?php
 $aS=explode(';',$mpSegmente); $aA=explode(';',$mpAnordnung); asort($aA); reset($aA); $nPos=0; $nNeuNr=100; $sOpt=''; $aBld=array();
 if(MP_Pfad>''){
  if(is_dir(MP_Pfad.MP_Daten)) if($f=opendir(MP_Pfad.MP_Daten)){
   while($s=readdir($f)) if(substr($s,2,1)=='_') $aBld[substr($s,0,2)]=$s; closedir($f);
  }
 }
 foreach($aA as $k=>$v) if($v>0){
  $sNr=sprintf('%02d',$k); $sBa=''; $sBz=''; if($k==$nSegNo){$sBa='<b>'; $sBz='</b>';}
  echo '<tr class="admTabl">'.NL;
  echo ' <td align="center"><input class="admEing" style="width:2em;" type="text" name="P'.$sNr.'" value="'.(++$nPos).'" /></td>'.NL;
  echo ' <td align="center">'.$sBa.$sNr.$sBz.'</td>'.NL;
  echo ' <td><input class="admEing" style="width:'.($aS[$k]!='LEER'?'20':'7').'em;" type="text" name="N'.$sNr.'" value="'.fSegNam($aS[$k]).'" /></input>'.($aS[$k]!='LEER'?'':'<span class="admMini"> (Platzhalter)</span>').'</td>'.NL;
  echo ' <td align="center"><input class="admRadio" type="checkbox" name="O'.$sNr.'" value="1"'.(substr($aS[$k],0,1)=='~'?' checked="checked"':'').' /> / <input class="admRadio" type="checkbox" name="U'.$sNr.'" value="1"'.(substr($aS[$k],0,1)=='*'?' checked="checked"':'').' /></td>'.NL;
  echo ' <td>'.(isset($aBld[$sNr])?'<a href="segmentBild.php?seg='.$sNr.'" onclick="bldWin(this.href)" target="sbild">'.$aBld[$sNr].'</a>':'&nbsp;').'</td>'.NL;
  if($k!=$nSegNo)
  echo ' <td align="center"><input type="image" src="iconLoeschen.gif" width="12" height="13" border="0" title="Segment '.$sNr.' löschen" alt="Segment '.$sNr.' löschen" /> <input class="admRadio" type="radio" name="Lsch" value="'.$k.($nLsch!=$k?'':'" checked="checked" /><input type="hidden" name="LNun" value="'.$k).'" /></td>'.NL;
  else
  echo ' <td>&nbsp;</td>'.NL;
  echo '</tr>'.NL; $sOpt.='<option value="'.$sNr.($UpNr!=$sNr?'':'" selected="selected').'">'.$sNr.'</option>';
 }elseif($k!=0) $nNeuNr=min($k,$nNeuNr);
 $nNeuNr=min(++$nPos,$nNeuNr);
 if($nNeuNr<100){
  $nNeuNr=sprintf('%02d',$nNeuNr);
  echo '<tr class="admTabl">'.NL;
  echo ' <td align="center"><input class="admEing" style="width:2em;" type="text" name="pNeu" value="" /></td>'.NL;
  echo ' <td align="center">neu ('.$nNeuNr.')</td>'.NL;
  echo ' <td colspan="3"><input class="admEing" style="width:20em;" type="text" name="nNeu" value="" /> <span class="admMini">(auf der Basis von: <i>'.fSegNam($sSegNam).'</i>)</span></td>'.NL;
  echo ' <td align="center">&nbsp;</td>'.NL;
  echo '</tr>'.NL;
 }else echo '<tr class="admTabl"><td>&nbsp;</td><td align="center">neu (100)</td><td>keine weiteren Marktsegmente mehr möglich</td><td>&nbsp;</td></tr>'.NL;
?>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Ausführen"></p>
</form>

<p class="admMeld">Hier ändern Sie die Sinnbilder für die Segmente.</p>
<form action="konfSegmente.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="BldUp" value="x" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td style="width:10%;white-space:nowrap;"><b>interne Segment-Nr.</b></td>
 <td style="white-space:nowrap;"><b>Bild hochladen</b></td>
</tr>
<tr class="admTabl" height="32px;">
 <td align="center"><select class="admEing" name="BldNum" size="1"><option value="">-</option><?php echo $sOpt?></select></td>
 <td><input class="admEing" type="file" name="BldNa" size="80" style="width:99%" /></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Hochladen"></p>
</form>

<form action="konfSegmente.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<input type="hidden" name="BldDl" value="x" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td style="width:10%;white-space:nowrap;"><b>interne Segment-Nr.</b></td>
 <td style="white-space:nowrap;"><b>Bild löschen</b></td>
</tr>
<tr class="admTabl" height="32px;">
 <td align="center"><select class="admEing" name="BldNum" size="1"><option value="">-</option><?php echo $sOpt?></select></td>
 <td><input class="admCheck" type="checkbox" name="BldDel" /> löschen</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="L&ouml;schen"></p>
</form>

<div class="admbox"><p>Die Anzahl der Segmente des Marktplatzes ist auf maximal 99 begrenzt.</p>
<p>Jedes Segment sollte einen kurzen und aussagekräftigen Namen haben.
 Dieser Name kann jederzeit nachträglich geändert werden.</p>
<p>Die Reihenfolge der Auflistung der Segmente auf der Startseite des Marktplatzes kann durch die Positionsnummer festgelegt werden.
 Diese kann ebenfalls jederzeit verändert werden.
 Dazu braucht einfach nur die neue Positionsummer des zu verschiebenden Segments eingegeben werden.
 Alle anderen Segmentpositionen ändern sich dann von allein passend.</p>
<p>Segmente können bei Bedarf für Besucher ausgeblendet also inaktiv geschaltet werden
 oder eventuell <i>nur</i> für angemeldete Benutzer eingeblendet also sichtbar werden.</p>
<p>Ein neues Segment wird angelegt, indem in der letzen Formularzeile der gewünschte Segmentname eingetragen wird.
 Eine gewünschte Segmentposition kann ebenfalls angegeben werden.
 Bleibt diese leer wird das neue Segment unten angehängt.
 Die Inseratetabelle des neuen Segmentes wird auf der Basis der Struktur und Eigenschaften des aktuell aktivierten Segments bzw. mit den Muster-Inseratestruktureigenschaften des leeren Segments angelegt.</p>
<p>Der spezielle Segementnamen <i>LEER</i> erzeugt keine neues Segment
 sondern dient lediglich als Platzhalter auf der Startseite um dort eine bestimmte Segemtreihung zu erzwingen.</p>
<p>Pro Klick auf den Formularschalter <i>Ausführen</i> wird immer nur eine Aktion ausgeführt.
 Löschen von Segmenten hat Vorrang vor Neuanlegen und vor Umbenennen/Umnumerieren.</p>
<p>Beim Hochladen der Bilder werden diese in Größe und Typ nicht verändert.
 Es werden Bilder vom Typ JPG, GIF oder PNG akzeptiert.</p>
</div>

<?php
echo fSeitenFuss();

function fSegNam($sIn){return str_replace('~','',str_replace('*','',str_replace(';',',',str_replace('"',"'",stripslashes(trim($sIn))))));}

function fMpDateiname($s){
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}
?>