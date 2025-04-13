<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('Testinhalt auswählen','','KTa');

$fsNameFolge=''; $fsNeueFolge=''; $fsProSeite=1; $fsProSeiteN='';
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $bNeu=false;

 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('FolgeName'); if(fSetzFraWert($v,'FolgeName',"'")) $bNeu=true;
  $v=str_replace(';',',',str_replace('.',',',str_replace(' ','',str_replace(' ','',txtVar('FolgeSpontan'))))); if(fSetzFraWert($v,'FolgeSpontan',"'")) $bNeu=true;
  $v=(int)txtVar('AuchInaktive'); if(fSetzFraWert(($v?true:false),'AuchInaktive','')) $bNeu=true;
  $v=(int)txtVar('AuchVersteckte'); if(fSetzFraWert(($v?true:false),'AuchVersteckte','')) $bNeu=true;
  $v=txtVar('KategorieFilter'); if(fSetzFraWert($v,'KategorieFilter',"'")) $bNeu=true;
  $v=(int)txtVar('WenigerFragen'); if(fSetzFraWert($v,'WenigerFragen','')) $bNeu=true;
  $v=(int)txtVar('ZeitLimitM')*60+(int)txtVar('ZeitLimitS'); if(fSetzFraWert($v,'ZeitLimit','')) $bNeu=true;
  $v=txtVar('Folge');
  if(fSetzFraWert(($v<='0'?true:false),'Zufallsfolge','')) $bNeu=true;
  if(fSetzFraWert(($v=='2'?true:false),'Rueckwaerts','')) $bNeu=true;
  $v=(int)txtVar('MischKategorie'); if(fSetzFraWert(($v?true:false),'MischKategorie','')) $bNeu=true;
  if($bNeu){ //Speichern
   if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
    if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
   }else{$sMeld='<p class="admFehl">Wollen Sie die Änderung wirklich für <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Testinhalte wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 elseif(!$bNeu) $sMeld.='<p class="admMeld">Die Testinhalte bleiben unverändert.</p>';
}else{ //GET
 $fsFolgeName=FRA_FolgeName; $fsFolgeSpontan=FRA_FolgeSpontan; $fsKategorieFilter=FRA_KategorieFilter;
 $fsAuchInaktive=FRA_AuchInaktive; $fsAuchVersteckte=FRA_AuchVersteckte; $fsZeitLimit=FRA_ZeitLimit;
 $fsWenigerFragen=FRA_WenigerFragen; $fsZufallsfolge=FRA_Zufallsfolge; $fsRueckwaerts=FRA_Rueckwaerts;
 $fsMischKategorie=FRA_MischKategorie;
}
$fsFolge=(isset($fsZufallsfolge)&&$fsZufallsfolge?'0':(isset($fsRueckwaerts)&&$fsRueckwaerts?'2':'1')); $sJSDFolge=''; $sSelFolge='&nbsp;';

$aOpt=array(); $sOpt=''; $sJSFolge=''; $sJSFolgeN=''; //gespeicherte Folgen holen
if(!FRA_SQL){$aOpt=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); array_shift($aOpt);}
elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT Folge,Fragen,ProSeite FROM '.FRA_SqlTabT.' ORDER BY Folge')){
  while($aR=$rR->fetch_row()) $aOpt[]=$aR[0].';'.$aR[1].';'.$aR[2]; $rR->close();
 }
}
if(is_array($aOpt)) foreach($aOpt as $s) if($p=strpos($s,';')){
 $sFN=substr($s,0,$p); $sFW=trim(substr($s,$p+1)); $nPS=1;
 if($p=strpos($sFW,';')){$nPS=trim(substr($sFW,$p+1)); if(strlen($nPS)>0) $nPS=(int)$nPS; $sFW=substr($sFW,0,$p);}
 $sOpt.='<option value="'.$sFN.'"'.($sFN!=$fsFolgeName?'':' selected="selected"').'>'.$sFN.'</option>';
 $sJSDFolge.=" aDFolgen['".$sFN."']='".str_replace(',',', ',$sFW)."';".NL;
 if(strlen($sFW)>64) $sFW=substr($sFW,0,63).'...'; if($sFN==$fsFolgeName) $sSelFolge=$sFW;
 $sJSFolge.=" aFolgen['".$sFN."']='".$sFW."';".NL;
}

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie die inhaltlichen Festlegungen für den Testablauf.</p>';
echo $sMeld.NL;
?>

<script type="text/javascript">
 function hlpWin(sURL){hWin=window.open(sURL,"hilfe","width=995,height=580,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");hWin.focus();}
 var ImPlus=new Image(); var ImMinu=new Image(); ImPlus.src='iconVorschau.gif'; ImMinu.src='iconVorschZu.gif';
 var sFolgenName='<?php echo $fsFolgeName?>'; var bZeigeDetails=false;
 aFolgen=new Array();
 aFolgen['']='';
<?php echo $sJSFolge?>
 aDFolgen=new Array();
 aDFolgen['']='';
<?php echo $sJSDFolge?>
 function zeigeFolge(sName){
  sFolgenName=sName;
  for(var i=document.links.length-1;i>0;i--) if(document.links[i].name=='konfFolgen'){
   var sLink=document.links[i].href;
   var nPos=sLink.lastIndexOf('&');
   if(nPos>0) sLink=sLink.substr(0,nPos)+'&flg='+encodeURI(sName);
   else sLink+=(sLink.lastIndexOf('?')>0?'&':'?')+'flg='+encodeURI(sName);
   document.links[i].href=sLink;
   break;
  }
  document.getElementById('DetailFolge').innerHTML=aFolgen[sFolgenName];
 }
 function FolgeDetails(bZeigen){
  if(bZeigen==false) bZeigeDetails=true;
  if(bZeigeDetails){
   bZeigeDetails=false;
   document.getElementById('Detailschalter').src=ImPlus.src; document.getElementById('Detailschalter').title='komplette Folge anzeigen';
   document.getElementById('DetailFolge').innerHTML=aFolgen[sFolgenName];
  }else{
   bZeigeDetails=true;
   document.getElementById('Detailschalter').src=ImMinu.src; document.getElementById('Detailschalter').title='Einzelheiten verbergen';
   document.getElementById('DetailFolge').innerHTML=aDFolgen[sFolgenName];
  }
 }
</script>

<form name="fraForm" action="konfTest.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen für den Test können auf Wunsch bestimmten vorgegebenen Teilmengen des Fragenvorrates entsprechen.
 Anderenfalls werden die Testaufgaben automatisch der Gesamtmenge der Fragen entnommen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">gespeicherte<br>Fragenfolge</td>
 <td>
  <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
   <td width="8%"><select name="FolgeName" onchange="zeigeFolge(this.value)" onkeyup="zeigeFolge(this.value)" size="1" style="width:16em;"><option value=""></option><?php echo $sOpt?></select></td>
   <td style="white-space:nowrap;padding-left:3px;padding-right:3px;">
    <img src="iconVorschau.gif" id="Detailschalter" onclick="FolgeDetails(true)" width="13" height="13" border="0" style="vertical-align:middle;" alt="komplette Folge zeigen" title="komplette Folge zeigen">
    <a name="konfFolgen" href="konfFolgen.php<?php if(KONF>0)echo'?konf='.KONF?>"><img src="iconAendern.gif" width="12" height="13" border="0" style="vertical-align:middle;" alt="Fragenfolge bearbeiten" title="Fragenfolge bearbeiten"> [ gespeicherte Fragenfolgen bearbeiten ]</a>
   </td>
  </tr></table>
  <div id="DetailFolge" style="font-size:80%;color:#654;"><?php echo $sSelFolge;?></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">spontane<br>Fragenfolge</td>
 <td><input type="text" name="FolgeSpontan" value="<?php echo $fsFolgeSpontan?>" size="120" style="width:99%;" />
 <div class="admMini">einfach die Fragenummern hintereinander durch Komma getrennt aufzählen oder/und<br />
 eine kategoriebezogene Folge definieren wie: <i>5x Kategoriename-1; 3x Kategorie_B; 8x Kategorie-3</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kategoriefilter</td>
 <td><input type="text" name="KategorieFilter" value="<?php echo $fsKategorieFilter?>" size="20" style="width:16em;" />
 <div class="admMini">einfach ein Begriff (auch Teilbegriff/Teilsilbe) aus den angelegten Fragenkategorien</div></td>
</tr>
<tr class="admTabl"><td colspan="2"><div class="admMini">Hinweise: Eine angegebene <i>gespeicherte Fragenfolge</i>
 hat Vorrang vor einer <i>spontane Fragenfolge</i> und diese vor einem <i>Kategoriefilter</i>.
 Bei den beiden Fragenfolgen werden die Attribute <i>aktiv</i> und <i>versteckt</i> bei nummernmäßig angegebenen Fragen ignoriert
 und die direkt angegebenen Fragen unabhängig von diesen Attribut im Test angeboten.
 Bei einem Kategoriefilter werden die Attribute <i>aktiv</i> und <i>versteckt</i> der Fragen hingegen berücksichtigt.</div></td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Jede Frage besitzt die zwei Attribute <i>aktiv</i> und <i>versteckt</i>, die über die Fragenliste gesetzt werden.
Normalerweise werden inaktiven Fragen in Tests nicht herangezogen.
Versteckte Fragen erscheinen nur für angemeldete Benutzer, nicht jedoch für Gäste oder registrierte Teilnehmer.
Dieses Standardverhalten kann jedoch ausser Kraft gesetzt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">inaktive Fragen</td>
 <td><input type="checkbox" class="admCheck" name="AuchInaktive<?php if($fsAuchInaktive) echo '" checked="checked'?>" value="1"> auch deaktivierte Fragen im Test verwenden &nbsp; <span class="admMini">(Empfehlung: nicht üblich)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">versteckte Fragen</td>
 <td><input type="checkbox" class="admCheck" name="AuchVersteckte<?php if($fsAuchVersteckte) echo '" checked="checked'?>" value="1"> auch versteckte Fragen für Gäste verwenden &nbsp; <span class="admMini">(Empfehlung: nicht üblich)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die angegebene Fragenmenge muss nicht im vollen Umfang angeboten werden.
 Es kann auch nur eine reduzierte Anzahl von Fragen im Test präsentiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">maximale<br />Testfragenanzahl</td>
 <td><input type="text" name="WenigerFragen" value="<?php echo ($fsWenigerFragen>0?$fsWenigerFragen:'')?>" size="4" style="width:4em;" />
 <span class="admMini">(Anzahl oder leer lassen, wenn alle gewählten Fragen im Test erscheinen sollen)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Testablauf kann durch ein Zeitlimit begrenzt werden.
Ist die vorgegebene Zeitspanne abgelaufen wird der Test mit der nächsten Antwort abgebrochen und zu Ende/Auswertung gegangen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zeitlimit</td>
 <td><input type="text" name="ZeitLimitM" value="<?php echo (!empty($fsZeitLimit)?floor($fsZeitLimit/60):'')?>" size="3" style="width:28px;">min :
 <input type="text" name="ZeitLimitS" value="<?php echo (!empty($fsZeitLimit)?sprintf('%02d',$fsZeitLimit % 60):'')?>" size="3" style="width:28px;">sec &nbsp;
 <span class="admMini">(bei unbegrenzter Zeit einfach leer lassen)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen für den Test werden der angegebenen Fragenmenge in einer einstellbaren Reihenfolge entnommen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenreihenfolge</td>
 <td><input type="radio" class="admRadio" name="Folge" value="0<?php if($fsFolge=='0') echo '" checked="checked'?>" /> zufällige Reihenfolge &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="Folge" value="1<?php if($fsFolge=='1') echo '" checked="checked'?>" /> aufsteigende Reihenfolge &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="Folge" value="2<?php if($fsFolge=='2') echo '" checked="checked'?>" /> absteigende Reihenfolge</td>
</tr><tr class="admTabl">
 <td class="admSpa1">Kategoriemischung</td>
 <td><input type="radio" class="admRadio" name="MischKategorie" value="1<?php if($fsMischKategorie) echo '" checked="checked'?>" /> Fragen aller Kategorien vermischen &nbsp;
 <input type="radio" class="admRadio" name="Mischkategorie" value="0<?php if(!$fsMischKategorie) echo '" checked="checked'?>" /> nicht mischen, eine Kategorie nach der anderen
 <div>(gilt nur für kategoriebezogenen Fragenfolgen)</div></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>