<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Vorgabewerte des Marktsegmentes definieren');

 $aStru=array(); $aFN=array(); $aFT=array(); $aAW=array(); $aKW=array(); $aSW=array(); $sTyp='';
 if(isset($_GET['fld'])) $nFld=(int)$_GET['fld']; else $nFld=0; $mpSymbolTyp=MP_SymbolTyp;
 if($nFld>0){
  if(!MP_SQL){//Text
   $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); $sZiel='Txt';
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
    if($i==1){$aStru=explode("\n",$a[1]); $sZiel='Sql';}
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
  if($sZiel){//Struktur ist geholt
   fMpEntpackeStruktur($aStru); $nFelder=count($aFN); $sTyp=$aFT[$nFld];
   if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
    $Meld='Legen Sie die Vorgabewerte für das '.($sTyp=='a'?'Auswahl':($sTyp=='k'?'Kategorie':($sTyp=='s'?'Symbol':($sTyp=='t'?'Text':($sTyp=='m'?'Memo':($sTyp=='o'?'PLZ':'???')))))).'-Feld <i>'.$aFN[$nFld].'</i> fest.'; $MTyp='Meld';
   }else{ //POST
    if(isset($_POST['symbtyp'])&&($v=$_POST['symbtyp'])){ // Symboltyp umstellen
     $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php'))));
     if(fSetzMPWert($v,'SymbolTyp',"'")){
      if($f=fopen(MP_Pfad.'mpWerte.php','w')){
       fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
       $Meld='Der Symboltyp wurde geändert.'; $MTyp='Erfo';
      }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
     }else $Meld='Der Symboltyp konnte nicht gesetzt werden.';
    }else{//Vorgabewerte
     $aAlt=array(''); $aTxt=array(); $sAlt='';
     if($sTyp=='a'){if(isset($aAW[$nFld])&&($s=rtrim($aAW[$nFld]))) $aAlt=explode('|','|'.$s);}
     elseif($sTyp=='t'||$sTyp=='o'||$sTyp=='m'){if(isset($aAW[$nFld])&&($s=trim($aAW[$nFld]))) $sAlt=$s;}
     elseif($sTyp=='k') $aAlt=$aKW; elseif($sTyp=='s') $aAlt=$aSW;
     $sTxt=str_replace('"',"'",str_replace(';','',str_replace("\r",'',stripslashes(@strip_tags(trim($_POST['txt']))))));
     if($sTyp=='t'||$sTyp=='o'||$sTyp=='m'){ //t,m,o
      if($sTyp=='m') $sTxt=str_replace("\n",'\n ',$sTxt);
      if($sTxt!=$sAlt){
       $aAW[$nFld]=$sTxt;
       $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
       if($Meld=='OK'){ //Strukturinfo ist geändert
        $MTyp='Erfo'; $Meld='Die '.($sTyp=='t'?'Text':($sTyp=='o'?'PLZ':($sTyp=='m'?'Memo':'???'))).'-Werte wurden gespeichert.'; $aStru=$aTmp;
       }
      }else{$MTyp='Meld'; $Meld='Die Werte bleiben unverändert.';}
     }else{ // a,k,s
      if($sTxt>'') $aTxt=explode(NL,str_replace("\n\n",NL,NL.$sTxt)); $aTxt[0]=$aAlt[0]; $nZhl=min(count($aTxt),17575);
      if($aTxt!=$aAlt){
       if($sTyp=='a'){$s=''; for($i=1;$i<$nZhl;$i++) $s.='|'.str_replace('|','/',$aTxt[$i]); $aAW[$nFld]=substr($s,1);}
       elseif($sTyp=='k') $aKW=$aTxt; elseif($sTyp=='s') $aSW=$aTxt;
       $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
       if($Meld=='OK'){ //Strukturinfo ist geändert
        $MTyp='Erfo'; $Meld='Die '.($sTyp=='a'?'Auswahl':($sTyp=='k'?'Kategorie':($sTyp=='s'?'Symbol':'???'))).'-Werte wurden gespeichert.'; $aStru=$aTmp;
       }
      }else{$MTyp='Meld'; $Meld='Die Werte bleiben unverändert.';}
     }
    }
   }//POST
  }else $Meld='Strukturinformationen konnten nicht gelesen werden!';
 }else $Meld='fehlerhafter Seitenaufruf ohne gültige Feldnummer!';
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;

 $sTxt=''; $sKnr=''; $sZhl='A'; $sZl1=''; $sZl2=''; $aTxt=array('');
 if($sTyp=='a'){if(isset($aAW[$nFld])&&($s=$aAW[$nFld])) $aTxt=explode('|','|'.$s);}
 elseif($sTyp=='t'||$sTyp=='o'||$sTyp=='m'){if(isset($aAW[$nFld])&&($s=$aAW[$nFld])) $aTxt[]=str_replace('\n ',"\n",$s);}
 elseif($sTyp=='k') $aTxt=$aKW; elseif($sTyp=='s') $aTxt=$aSW;
 $nZhl=count($aTxt); $nHei=max(round(1.2*min($nZhl+5,35)),15);
 for($i=1;$i<$nZhl;$i++){
  $sTxt.=$aTxt[$i].NL; $sKnr.=$sZl2.$sZl1.$sZhl.NL;
  if($sZhl<'Z') $sZhl++;
  else{
   $sZhl='A';
   if($sZl1<='') $sZl1='A';
   elseif($sZl1<'Z') $sZl1++;
   else{$sZl1='A'; if($sZl2<='') $sZl2='A'; else $sZl2++;}
  }
 }
?>

<form action="konfVorgaben.php?<?php echo ($nSegNo>0?'seg='.$nSegNo.'&':''); echo 'fld='.$nFld ?>" method="post">
<table class="admTabl" border="0" cellpadding="5" cellspacing="1">
<tr class="admTabl"><td align="center"><b>Nr.</b></td><td><b>Vorgabewert</b> <span class="admMini">(max. <?php if($sTyp=='t'||$sTyp=='o'||$sTyp=='m') {?>1 Eintrag<?php }else{?>675 Einträge<?php }?>)</span></td></tr>
<tr class="admTabl">
 <td valign="top">
  <textarea class="admEing" name="knr" style="width:55px;height:<?php echo $nHei?>em;" readonly="readonly"><?php echo rtrim($sKnr)?></textarea>
 </td>
 <td width="96%" valign="top">
  <textarea class="admEing" name="txt" style="width:99%;height:<?php echo $nHei?>em;""><?php echo rtrim($sTxt)?></textarea>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php
if($sTyp=='a'){ //bei Auswahl
?>
<br>
<div class="admBox">Es können maximal 675 Vorgabewerte von A...Z, AA...AZ, BA...BZ, CA...ZZ verwendet werden.
Die Numerierung erfolgt automatisch, es sind lediglich die Vorgabewerte für das Auswahlfeld <i><?php if($nFld) echo $aFN[$nFld]?></i> untereinander einzutragen.</div>

<?php
}elseif($sTyp=='t'||$sTyp=='m'||$sTyp=='o'){ //bei Text, Memo, PLZ
?>

<br>
<div class="admBox">Es kann ein Vorgabewerte für dieses Feld eingetragen werden.
Entweder als ein normaler <?php if($sTyp=='m') echo 'auch mehrzeiliger' ?> Text
oder als Name eines Datenfeldes aus den <a href="konfNutzer.php">Benutzerfunktionen</a> eingeschlossen in geschweifte Klammern { } wie beispielsweise {Telefon} oder {Ort} oder {PLZ}.
<br><br>
Dieser Text wird automatisch im Eingabeformular beim Inserateeintrag vorgegeben, kann aber bei jedem Inserat überschrieben oder gelöscht werden.</div>

<?php
}elseif($sTyp=='k'){ //bei Kategorie
?>

<br>
<div class="admBox">Es können maximal 675 Kategorien von A...Z, AA...AZ, BA...BZ, CA...ZZ verwendet werden.
Beachten Sie, dass zu jeder von Ihnen hier vereinbarten Kategorie A...ZZ auch eine korrespondierende CSS-Klasse
mit dem Namen <i>div.mpKatX</i> (wobei X für das Kategorienkürzel steht) in der CSS-Datei <i>mpStyles.css</i> definiert sein muss,
damit die farbige Unterscheidung der Kategorien funktioniert.</div>

<?php
}elseif($sTyp=='s'){ //bei Symbol
 $aS=array();
 if($f=opendir(MP_Pfad.'/grafik')){
  while($s=readdir($f)) if(substr($s,0,6)=='symbol') $aS[]=$s;
  closedir($f); sort($aS); $nZhl=count($aS);
 }
?>

<p style="margin-top:32px;">Folgende Symbole sind momentan im Ordner <i>/grafik</i> vorrätig:</p>
<table class="admTabl" style="width:8%;" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td><b>Dateiname</b></td><td><b>Symbol</b></td></tr>
<?php
 for($i=0;$i<$nZhl;$i++){
  $s=$aS[$i]; if(file_exists(MP_Pfad.'grafik/'.$s)) $aI=getimagesize(MP_Pfad.'grafik/'.$s); else $aI=array(0,0,0,'');
  echo NL.'<tr class="admTabl"><td>'.$s.'</td><td align="center"><img src="'.MPPFAD.'grafik/'.$s.'" '.(isset($aI[3])?$aI[3]:'').' alt="" border="0" /></td></tr>';
 }
?>

</table>
<form name="konfTyp" action="konfVorgaben.php?<?php echo ($nSegNo>0?'seg='.$nSegNo.'&':''); echo 'fld='.$nFld ?>" method="post">
<p>Momentan werden nur Symbole mit der Endung <select name="symbtyp" class="admEing" style="width:50px;" onchange="document.forms['konfTyp'].submit()"><option value="jpg"<?php if($mpSymbolTyp=='jpg') echo 'selected=" selected"'?>>.jpg</option><option value="gif"<?php if($mpSymbolTyp=='gif') echo 'selected=" selected"'?>>.gif</option><option value="png"<?php if($mpSymbolTyp=='png') echo 'selected=" selected"'?>>.png</option></select> verwendet.</p>
</form><br>
<div class="admBox">Andere oder weitere Symbole müssen bei Bedarf manuell im Ordner <i>/grafik</i> abgelegt werden.
Es können maximal 675 Symbole von A...Z, AA...AZ, BA...BZ, CA...ZZ verwendet werden.
Beachten Sie beim Anlegen eigener Symbole die Großschreibweise der Kennbuchstaben A...ZZ im Dateinamen.</div>

<?php
}

echo fSeitenFuss();

function fMpEntpackeStruktur($aStru){//Struktur interpretieren
 global $aFN,$aFT,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14);
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}
function fMpPackeStruktur($aStru){
 global $aAW,$aKW,$aSW;
 $a=$aStru; for($i=0;$i<16;$i++) $a[$i]=rtrim($a[$i]);
 $aAW[0]='AuswahlWerte: '; $aAW[1]=''; $a[16]=rtrim(implode(';',$aAW)); //Auswahlwerte
 $aKW[0]='Kategorien  : '; $a[17]=substr_replace(rtrim(implode(';',$aKW)),'',14,1); //Kategorien
 $aSW[0]='Symbole     : '; $a[18]=substr_replace(rtrim(implode(';',$aSW)),'',14,1); //Symbole
 return $a;
}
function fMpSpeichereStruktur($sNr,$aTmp,$DbO){
 if(!MP_SQL){//Text
  if($f=fopen(MP_Pfad.MP_Daten.$sNr.MP_Struktur,'w')){
   fwrite($f,rtrim(implode("\n",$aTmp))."\n"); fclose($f); $E='OK';
  }else $E=str_replace('#',MP_Daten.$sNr.MP_Struktur,MP_TxDateiRechte);
 }elseif($DbO){//SQL
  if(isset($aTmp[16])) $aTmp[16]=str_replace('\n ','/n/',$aTmp[16]);
  if($DbO->query('UPDATE IGNORE '.MP_SqlTabS.' SET struktur="'.str_replace('"','\"',rtrim(implode("\r\n",$aTmp))).'" WHERE nr='.((int)$sNr))){
   $E='OK';
  }else $E=MP_TxSqlAendr;
 }else $E=MP_TxSqlVrbdg;
 return $E;
}
?>