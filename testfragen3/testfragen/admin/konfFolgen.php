<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('gespeicherte Testfolgen bearbeiten','','KTa');

$sFlgNam=(isset($_GET['flg'])?$_GET['flg']:''); $sFlgInh=''; $sAktivG=''; $sAktivB=''; $sProSeite='1'; $sProSeiteN=''; $sVorAusw=$sNachAusw='';
$sFlgAlt=(isset($_POST['FlgAlt'])?$_POST['FlgAlt']:$sFlgNam); $sAktG=''; $sAktB=''; $sDel='#*?';
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
  $sFlgInh=str_replace(';',',',str_replace('.',',',str_replace('  ',' ',str_replace('  ',' ',str_replace("\n",' ',str_replace("\r",'',txtVar('FlgInh')))))));
  $sProSeite=txtVar('ProSeite'); if(strlen($sProSeite)>0) $sProSeite=(int)$sProSeite; $sProSeiteN=(int)txtVar('ProSeiteN'); if($sProSeiteN<=0) $sProSeiteN='';
  $sAktivG=(int)txtVar('AktivG'); $sAktivB=(int)txtVar('AktivB');
  $sVorAusw=str_replace("\r",'',str_replace(';',',',txtVar('VorAusw')));
  $sNachAusw=str_replace("\r",'',str_replace(';',',',txtVar('NachAusw')));
  if($sFlgNam=str_replace(';',',',txtVar('FlgNam'))){
   if(!FRA_SQL){ //Textdatei
    $aFlg=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $bFound=false;
    if(is_array($aFlg)) array_shift($aFlg); else $aFlg=array(); $nFlg=count($aFlg); $sFN=$sFlgAlt.';'; $l=strlen($sFN);
    for($i=0;$i<$nFlg;$i++) if(substr($aFlg[$i],0,$l)==$sFN){ //gefunden
     if($sFlgInh){
      $aFlg[$i]=$sFlgNam.';'.str_replace(' ','',$sFlgInh).';'.($sProSeite!=2?$sProSeite:$sProSeiteN).';'.$sAktivG.';'.$sAktivB.';'.(($sAktivG||$sAktivG)?rand(1000,9999):'0').';'.str_replace("\n",'\n ',$sVorAusw).';'.str_replace("\n",'\n ',$sNachAusw)."\n";
      $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> wurde gespeichert.</p>'; $sFlgAlt=$sFlgNam;
     }else{$aFlg[$i]=''; $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> wurde gelöscht.</p>'; $sFlgAlt='';}
     $bFound=true; break;
    }
    if(!$bFound){ //neue
     if($sFlgInh){
      $aFlg[]=$sFlgNam.';'.str_replace(' ','',$sFlgInh).';'.($sProSeite!=2?$sProSeite:$sProSeiteN).';'.$sAktivG.';'.$sAktivB.';'.(($sAktivG||$sAktivG)?rand(1000,9999):'0').';'.str_replace("\n",'\n ',$sVorAusw).';'.str_replace("\n",'\n ',$sNachAusw)."\n";
      $sMeld='<p class="admErfo">Die neue Fragenfolge <i>'.$sFlgNam.'</i> wurde angelegt.</p>'; $sFlgAlt=$sFlgNam;
     }else{$sMeld='<p class="admFehl">Bitte eine Bildungsvorschrift zu neuen Fragenfolge <i>'.$sFlgNam.'</i> eintragen!</p>'; $sFlgAlt='';}
    }
    natcasesort($aFlg); reset($aFlg);
    $s='Folge;Fragen;ProSeite;GAktiv;BAktiv;Code;VorAuswertung;NachAuswertung'.NL; foreach($aFlg as $v) if(strlen($v)>0) $s.=rtrim($v).NL;
    if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Folgen,'w')){fwrite($f,$s); fclose($f);}
    else $sMeld='<p class="admFehl">In die Datei <i>'.FRA_Daten.FRA_Folgen.'</i> konnte nicht geschrieben werden!</p>';
   }elseif($DbO){//SQL
    if($rR=$DbO->query('SELECT Folge FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgAlt.'"')){
     if(($rR->num_rows==1)&&($a=$rR->fetch_row())) $bFound=true; else $bFound=false; $rR->close();
    }
    if($bFound){ //aendern
     if($sFlgInh){
      if($DbO->query('UPDATE IGNORE '.FRA_SqlTabT.' SET Folge="'.$sFlgNam.'",Fragen="'.str_replace(' ','',$sFlgInh).'",ProSeite="'.($sProSeite!=2?$sProSeite:$sProSeiteN).'",GAktiv="'.$sAktivG.'",BAktiv="'.$sAktivB.'",Code="'.(($sAktivG||$sAktivG)?rand(1000,9999):'0').'",VorAw="'.str_replace("\n","\r\n",$sVorAusw).'",NachAw="'.str_replace("\n","\r\n",$sNachAusw).'" WHERE Folge="'.$sFlgAlt.'"')){
       $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> wurde gespeichert.</p>'; $sFlgAlt=$sFlgNam;
      }else $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> konnte nicht geändert werden.</p>';
     }else{
      if($DbO->query('DELETE FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgAlt.'" LIMIT 1')){
       $sMeld='<p class="admErfo">Die Testfragenfolge <i>'.$sFlgNam.'</i> wurde gelöscht.</p>'; $sFlgAlt='';
      }else $sMeld='<p class="admFehl">Die Fragenfolge <i>'.$sFlgNam.'</i> konnte nicht gelöscht werden.</p>';
     }
    }else{ //neue
     if($sFlgInh){
      if($DbO->query('INSERT IGNORE INTO '.FRA_SqlTabT.' (Folge,Fragen,ProSeite,GAktiv,BAktiv,Code,VorAw,NachAw) VALUES("'.$sFlgNam.'","'.str_replace(' ','',$sFlgInh).'","'.($sProSeite!=2?$sProSeite:$sProSeiteN).'","'.$sAktivG.'","'.$sAktivB.'","'.(($sAktivG||$sAktivG)?rand(1000,9999):'0').'","'.str_replace("\n","\r\n",$sVorAusw).'","'.str_replace("\n","\r\n",$sNachAusw).'")')){
       $sMeld='<p class="admErfo">Die neue Fragenfolge <i>'.$sFlgNam.'</i> wurde angelegt.</p>'; $sFlgAlt=$sFlgNam;
      }else{$sMeld='<p class="admErfo">Die neue Fragenfolge <i>'.$sFlgNam.'</i> konnte nicht eingetragen werden.</p>'; $sFlgAlt='';}
     }else{$sMeld='<p class="admFehl">Bitte eine Bildungsvorschrift zu neuen Fragenfolge <i>'.$sFlgNam.'</i> eintragen!</p>'; $sFlgAlt='';}
    }
   }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
  }else $sMeld='<p class="admFehl">Geben Sie einen Namen für die Testfolge an.</p>';
}elseif(($sAktG=(isset($_GET['aktG'])?$_GET['aktG']:''))||($sAktB=(isset($_GET['aktB'])?$_GET['aktB']:''))){
 if(!FRA_SQL){
  $aFlg=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nFlg=count($aFlg); $sFN=$sFlgNam.';'; $l=strlen($sFN);
  for($i=0;$i<$nFlg;$i++) if(substr($aFlg[$i],0,$l)==$sFN){ //gefunden
   $a=explode(';',rtrim($aFlg[$i])); $sG=(isset($a[3])?$a[3]:'0'); $sB=(isset($a[4])?$a[4]:'0'); $sC=(isset($a[5])?$a[5]:'0'); if(!isset($a[6])) $a[6]=''; if(!isset($a[7])) $a[7]='';
   if($sAktG){if($sAktG=='on'){$sG='1'; $sC=rand(1000,9999);}else{$sG='0'; if($sB!='1') $sC='0';}}
   elseif($sAktB){if($sAktB=='on'){$sB='1'; $sC=rand(1000,9999);}else{$sB='0'; if($sG!='1') $sC='0';}}
   $aFlg[$i]=$a[0].';'.$a[1].';'.$a[2].';'.$sG.';'.$sB.';'.$sC.';'.$a[6].';'.$a[7]."\n"; $aFlg[0]='Folge;Fragen;ProSeite;GAktiv;BAktiv;Code;VorAuswertung;NachAuswertung'."\n";
   if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Folgen,'w')){
    for($j=0;$j<$nFlg;$j++) fwrite($f,rtrim($aFlg[$j])."\n"); fclose($f);
    $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> wurde '.(($sAktG.$sAktB)!='on'?'de':'').'aktiviert.</p>';
   }else $sMeld='<p class="admFehl">In die Datei <i>'.FRA_Daten.FRA_Folgen.'</i> konnte nicht geschrieben werden!</p>';
   break;
  }
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT Folge,GAktiv,BAktiv,Code FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgNam.'"')){
   if(($rR->num_rows==1)&&($a=$rR->fetch_row())){
    $sG=$a[1]; $sB=$a[2]; $sC=$a[3];
    if($sAktG){if($sAktG=='on'){$sG='1'; $sC=rand(1000,9999);}else{$sG='0'; if($sB!='1') $sC='0';}}
    elseif($sAktB){if($sAktB=='on'){$sB='1'; $sC=rand(1000,9999);}else{$sB='0'; if($sG!='1') $sC='0';}}
    if($DbO->query('UPDATE IGNORE '.FRA_SqlTabT.' SET GAktiv="'.$sG.'",BAktiv="'.$sB.'",Code="'.$sC.'" WHERE Folge="'.$sFlgNam.'"'))
     $sMeld='<p class="admErfo">Die Fragenfolge <i>'.$sFlgNam.'</i> wurde '.(($sAktG.$sAktB)!='on'?'de':'').'aktiviert.</p>';
    else $sMeld='<p class="admFehl">Die Fragenfolge <i>'.$sFlgNam.'</i> konnte nicht geändert werden.</p>';
   }$rR->close();
  }else $sMeld='<p class="admFehl">MySQL-Abfrage-Fehler bei Folge <i>'.$sFlgNam.'</i>.</p>';
 }
}elseif(isset($_GET['del'])){
 if($_GET['del']=='ok'){
  if(!FRA_SQL){
   $aFlg=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nFlg=count($aFlg); $sFN=$sFlgNam.';'; $l=strlen($sFN);
   for($i=0;$i<$nFlg;$i++) if(substr($aFlg[$i],0,$l)==$sFN){ //gefunden
    $aFlg[$i]=''; $aFlg[0]='Folge;Fragen;ProSeite;GAktiv;BAktiv;Code;VorAuswertung;NachAuswertung'."\n";
    if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Folgen,'w')){
     for($j=0;$j<$nFlg;$j++) if($aFlg[$j]>'') fwrite($f,rtrim($aFlg[$j])."\n"); fclose($f);
     $sMeld='<p class="admErfo">Die Testfragenfolge <i>'.$sFlgNam.'</i> wurde gelöscht.</p>'; $sFlgNam='';
    }else $sMeld='<p class="admFehl">In die Datei <i>'.FRA_Daten.FRA_Folgen.'</i> konnte nicht geschrieben werden!</p>';
    break;
   }
  }elseif($DbO){ //SQL
   if($DbO->query('DELETE FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgNam.'" LIMIT 1')){
    $sMeld='<p class="admErfo">Die Testfragenfolge <i>'.$sFlgNam.'</i> wurde gelöscht.</p>'; $sFlgNam='';
   }else $sMeld='<p class="admFehl">Die Fragenfolge <i>'.$sFlgNam.'</i> konnte nicht gelöscht werden.</p>';
  }
 }else{
  $sMeld='<p class="admFehl">Die Testfragenfolge <i>'.$sFlgNam.'</i> wirklich löschen?</p>'; $sDel=$sFlgNam;
 }
}elseif($sFlgNam) $sMeld='<p class="admMeld">Bearbeiten Sie die Testfragenfolge <i>'.$sFlgNam.'</i>.</p>';

$aFlg=NULL;
if(!FRA_SQL){$aFlg=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); array_shift($aFlg);}
elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT Folge,Fragen,ProSeite,GAktiv,BAktiv,Code,VorAw,NachAw FROM '.FRA_SqlTabT.' ORDER BY Folge')){
  while($aR=$rR->fetch_row()) $aFlg[]=$aR[0].';'.$aR[1].';'.$aR[2].';'.$aR[3].';'.$aR[4].';'.$aR[5].';'.$aR[6].';'.$aR[7]; $rR->close();
 }
}

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Erstellen oder bearbeiten Sie eine Testfragenfolge.</p>';
echo $sMeld.NL;

if(is_array($aFlg)) foreach($aFlg as $s) if(substr($s,0,strpos($s,';'))==$sFlgNam){
 $a=explode(';',rtrim($s)); $sFlgInh=$a[1]; $sProSeite=$a[2];
 $sAktivG=(isset($a[3])?$a[3]:'1'); $sAktivB=(isset($a[4])?$a[4]:'1');
 $sVorAusw=(isset($a[6])?str_replace('\n ',"\n",$a[6]):''); $sNachAusw=(isset($a[7])?str_replace('\n ',"\n",$a[7]):'');
}
?>

<form name="fraFolge" action="konfFolgen.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<input type="hidden" name="FlgAlt" value="<?php echo $sFlgAlt?>" />
<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1" style="width:12em;"></td>
 <td></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Inhalt und Eigenschaften der Fragenfolge</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:12em;">Name der<br>Fragenfolge</td>
 <td><input type="text" name="FlgNam" value="<?php echo $sFlgNam?>" size="12" style="width:12em;" />
 <span class="admMini"><?php if(!$sFlgAlt){?>Erzeugen Sie eine neue Folge oder überschreiben Sie eine existierende.<?php }else{?><u>Hinweis</u>: Die bisherige Folge <i><?php echo $sFlgAlt?></i> wird überschrieben!<?php }?></span>
 <div class="admMini" style="margin-top:3px">Fragenfolgen mit einem ~ am Namensanfang werden im Benutzerzentrum generell versteckt und nicht angezeigt.</div>
</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bildungsvorschrift<br />für die Fragenfolge<div class="admMini" style="margin-top:6px;">(eine leere Bildungs-<br>vorschrift löscht die<br>angegebene Folge)</div></td>
 <td>
 <div><textarea class="admAntw" name="FlgInh" cols="100" rows="4" style="width:99%;height:5em;"><?php echo $sFlgInh?></textarea></div>
 <div class="admMini">- einfach die Fragenummern hintereinander durch Komma getrennt aufzählen,<br />- bei Bedarf mit Bereichsangaben von Fragenummern von-bis als <i>21-33, 45-51</i><br />- oder eine kategoriebezogene Folge definieren als <i>5x Kategoriename-1, 3x Kategorie_B, 8x Kategorie-3</i><br />- gern auch alles gemischt</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierungsstatus</td>
 <td><input type="checkbox" class="admCheck" name="AktivG" value="1<?php if($sAktivG) echo '" checked="checked'?>" /> aktiviert für Gäste &nbsp; &nbsp; <input type="checkbox" class="admCheck" name="AktivB" value="1<?php if($sAktivB) echo '" checked="checked'?>" /> aktiviert für Benutzer
 <div class="admMini" style="margin-top:5px">Da momentan der Parameter <i>Testdurchführung für Teilnehmer nur nach Eingabe eines 4-stelligen Aktiv-Codes</i> <?php echo (FRA_TeilnehmerMitCode?'ein':'aus')?>geschaltet ist
 und der Paramenter <i>Testdurchführung für Benutzer nur nach Eingabe eines 4-stelligen Aktiv-Codes</i> <?php echo (FRA_NutzerMitCode?'ein':'aus')?>geschaltet ist
 werden untenstehende Aktiv-Codes <?php if(!(FRA_NutzerMitCode||FRA_TeilnehmerMitCode)) echo '<i>nicht</i> '?>berücksichtigt.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:12em;">Aufteilung der<br>Fragenfolge<br>pro Seite</td>
 <td>
  <div><input type="radio" class="admRadio" name="ProSeite" value="<?php if($sProSeite=='') echo '" checked="checked'?>" /> aktuelle Standardanzahl <i><?php echo (FRA_ProSeite?FRA_ProSeite:'alle') ?></i> laut Ablaufeinstellungen verwenden</div>
  <div><input type="radio" class="admRadio" name="ProSeite" value="1<?php if($sProSeite==1) echo '" checked="checked'?>" /> eine Frage pro Bildschirmseite</div>
  <div><input type="radio" class="admRadio" name="ProSeite" value="2<?php if($sProSeite>1)  echo '" checked="checked'?>" /> mehrere Fragen pro Seite und zwar <input type="text" name="ProSeiteN" value="<?php echo ($sProSeite<2?'':$sProSeite)?>" size="3" style="width:2.5em;" /> Fragen</div>
  <div><input type="radio" class="admRadio" name="ProSeite" value="0<?php if($sProSeite=='0') echo '" checked="checked'?>" /> alle Fragen auf einer Seite</div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Vor bzw. nach den Auswertungsdetails auf dem Bildschirm bzw. in der E-Mail an den Teilnehmer können folgende ergänzende Texte verwendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Vorspann<br />für die Auswertung<div class="admMini" style="margin-top:6px;">(optional)</div></td>
 <td>
  <div><textarea class="admAntw" name="VorAusw" cols="100" rows="4" style="width:99%;height:5em;"><?php echo $sVorAusw?></textarea></div>
  <div class="admMini">Platzhalter: #T - Testfolgenname &nbsp; &nbsp; #D - Datum &nbsp; &nbsp; {Benutzer-Datenfeldname}</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Abspann<br />für die Auswertung<div class="admMini" style="margin-top:6px;">(optional)</div></td>
 <td>
  <div><textarea class="admAntw" name="NachAusw" cols="100" rows="4" style="width:99%;height:5em;"><?php echo $sNachAusw?></textarea></div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
<p class="admSubmit">[ <a href="konfFolgen.php<?php if(KONF>0)echo'?konf='.KONF?>"><img src="iconTestwahl.gif" width="12" height="13" border="0" alt="neue Folge anlegen" title="neue Folge anlegen"> neue Testfragenfolge anlegen</a> ]</p>
</form>

<p class="admMeld">Übersicht bisher gespeicherter Fragenfolgen</p>
<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td rowspan="2" style="width:10em;">Folgenname</td>
 <td rowspan="2" style="width:16px;">&nbsp;</td>
 <td colspan="2" style="width:9em;text-align:center;">aktiviert für</td>
 <td rowspan="2" style="width:7em;text-align:center;">Aktiv-Code</td>
 <td rowspan="2">Bildungsvorschrift</td>
 <td rowspan="2" style="width:16px;">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td style="width:4em;text-align:center;">Gäste</td>
 <td style="width:4em;text-align:center;">Nutzer</td>
</tr>
<?php
if(is_array($aFlg)) foreach($aFlg as $s){
 $a=explode(';',rtrim($s)); $sL='<a href="konfFolgen.php?'.(KONF>0?'konf='.KONF.'&':'').'flg='.rawurlencode($a[0]);
 $sG=(isset($a[3])?($a[3]!='0'?'Grn':'Rot'):'Grn'); $sB=(isset($a[4])?($a[4]!='0'?'Grn':'Rot'):'Grn');
 $sG=$sL.'&aktG='.($sG!='Grn'?'on':'off').'"><img src="punkt'.$sG.'.gif" width="12" height="12" border="0" alt="'.($sG!='Grn'?'de':'').'aktiviert, jetzt '.($sG!='Grn'?'':'de').'aktivieren" title="'.($sG!='Grn'?'de':'').'aktiviert, jetzt '.($sG!='Grn'?'':'de').'aktivieren"></a>';
 $sB=$sL.'&aktB='.($sB!='Grn'?'on':'off').'"><img src="punkt'.$sB.'.gif" width="12" height="12" border="0" alt="'.($sB!='Grn'?'de':'').'aktiviert, jetzt '.($sB!='Grn'?'':'de').'aktivieren" title="'.($sB!='Grn'?'de':'').'aktiviert, jetzt '.($sB!='Grn'?'':'de').'aktivieren"></a>';
 $sD=$sL.'&del='.($a[0]!=$sDel?'x':'ok').'"><img src="iconLoeschen.gif" width="12" height="13" border="0" alt="'.$a[0].' löschen" title="'.$a[0].' löschen"></a>';
 $sE=$sL.'"><img src="iconAendern.gif" width="12" height="13" border="0" alt="'.$a[0].' bearbeiten" title="'.$a[0].' bearbeiten"></a>';
 $a[5]=(isset($a[5])?($a[5]!='0'?$a[5]:'-'):'-');
 echo NL.'<tr class="admTabl">'.NL.' <td>'.$a[0].'</td>'.NL.' <td>'.$sE.'</td>';
 echo NL.' <td style="text-align:center">'.$sG.'</td>'.NL.' <td style="text-align:center">'.$sB.'</td>';
 echo NL.' <td style="text-align:center">'.$a[5].'</td>'.NL.' <td>'.$a[1].'</td>'.NL.' <td>'.$sD.'</td>'.NL.'</tr>';
}
?>
</table>

<?php echo fSeitenFuss();?>