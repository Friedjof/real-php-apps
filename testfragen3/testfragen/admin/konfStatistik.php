<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Bestenliste/Statistik einstellen','','KBS');

$aTlnFldNa=explode(';','#;'.FRA_TeilnehmerFelder); $nTlnFlds=count($aTlnFldNa);
$aNtzFldNa=explode(';','#;'.FRA_NutzerFelder); $nNtzFlds=count($aNtzFldNa);

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $fsStatistik=FRA_Statistik; $fsStatOffen=FRA_StatOffen;
 $aFelder=explode(',',FRA_StatFelder);
 $aNFelder=explode(',',FRA_StatNFelder);
 $aCssStil=explode(',',FRA_StatCssStil);
 $aTlnFld=explode(',','#,'.FRA_StatTlnFld); $aNtzFld=explode(',','#,'.FRA_StatNtzFld);
 $fsStatTlnTrn=FRA_StatTlnTrn; $fsStatNtzTrn=FRA_StatNtzTrn;
 $fsStatSortier1=FRA_StatSortier1; $fsStatSortier2=FRA_StatSortier2;
 $fsStatSortAbsteig=FRA_StatSortAbsteig; $fsStatSrt2Absteig=FRA_StatSrt2Absteig; $fsStatDatumZeit=FRA_StatDatumZeit;
 $fsStatKommaStellen=FRA_StatKommaStellen; $fsStatListenZeilen=FRA_StatListenZeilen;
 $fsTxStatistik=FRA_TxStatistik; $fsTxKeinTlnNam=FRA_TxKeinTlnNam; $fsTxKeinNtzNam=FRA_TxKeinNtzNam;
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=(int)txtVar('Statistik'); if(fSetzFraWert(($v?true:false),'Statistik','')) $bNeu=true;
  $v=(int)txtVar('StatOffen'); if(fSetzFraWert(($v?true:false),'StatOffen','')) $bNeu=true;
  $v=txtVarL('StatTlnTrn'); if(fSetzFraWert($v,'StatTlnTrn',"'")) $bNeu=true;
  $v=txtVarL('StatNtzTrn'); if(fSetzFraWert($v,'StatNtzTrn',"'")) $bNeu=true;
  $v=(int)txtVar('StatSortier1'); if(fSetzFraWert($v,'StatSortier1','')) $bNeu=true;
  $v=(int)txtVar('StatSortier2'); if(fSetzFraWert($v,'StatSortier2','')) $bNeu=true;
  $v=(int)txtVar('StatSortAbsteig'); if(fSetzFraWert(($v?true:false),'StatSortAbsteig','')) $bNeu=true;
  $v=(int)txtVar('StatSrt2Absteig'); if(fSetzFraWert(($v?true:false),'StatSrt2Absteig','')) $bNeu=true;
  $v=(int)txtVar('StatDatumZeit'); if(fSetzFraWert(($v?true:false),'StatDatumZeit','')) $bNeu=true;
  $v=min(max((int)txtVar('StatKommaStellen'),0),5); if(fSetzFraWert($v,'StatKommaStellen','')) $bNeu=true;
  $v=max((int)txtVar('StatListenZeilen'),1); if(fSetzFraWert($v,'StatListenZeilen','')) $bNeu=true;
  $v=''; for($i=1;$i<$nTlnFlds;$i++){$j=(int)txtVar('TlnFeld'.$i); $aTlnFld[$i]=$j; $v.=','.$j;}
  if(fSetzFraWert(substr($v,1),'StatTlnFld',"'")) $bNeu=true;
  $v=''; for($i=1;$i<$nNtzFlds;$i++){$j=(int)txtVar('NtzFeld'.$i); $aNtzFld[$i]=$j; $v.=','.$j;}
  if(fSetzFraWert(substr($v,1),'StatNtzFld',"'")) $bNeu=true;

  $u=''; $v=''; $w='';
  for($i=0;$i<14;$i++){
   $j=(int)txtVar('Felder'.$i); $aFelder[$i]=$j;
   $j=(int)txtVar('NFelder'.$i); $aNFelder[$i]=$j;
   $j=txtVar('CssStil'.$i); $aCssStil[$i]=$j; $w.=','.$j;
  }
  asort($aFelder); reset($aFelder); asort($aNFelder); reset($aNFelder);
  $j=0; foreach($aFelder as $m=>$v) if($v>0) $aFelder[$m]=++$j;
  $j=0; foreach($aNFelder as $m=>$v) if($v>0) $aNFelder[$m]=++$j; $v='';
  for($i=0;$i<14;$i++){$u.=','.$aFelder[$i]; $v.=','.$aNFelder[$i];}
  $u=substr($u,1); $v=substr($v,1); $w=substr($w,1);
  if($u!=FRA_StatFelder) if(fSetzFraWert($u,'StatFelder',"'")) $bNeu=true;
  if($v!=FRA_StatNFelder) if(fSetzFraWert($v,'StatNFelder',"'")) $bNeu=true;
  if($w!=FRA_StatCssStil) if(fSetzFraWert($w,'StatCssStil',"'")) $bNeu=true;

  $v=txtVar('TxStatistik'); if(fSetzFraWert($v,'TxStatistik',"'")) $bNeu=true;
  $v=txtVar('TxKeinTlnNam'); if(fSetzFraWert($v,'TxKeinTlnNam',"'")) $bNeu=true;
  $v=txtVar('TxKeinNtzNam'); if(fSetzFraWert($v,'TxKeinNtzNam',"'")) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Statistik-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Statistik-Einstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$sMeld){
 $sMeld.='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Bestenliste/Statistik.</p>';
}
echo $sMeld.NL;

$sOSort='<option value="0">lfd. Nummer</option><option value="1">Datum</option><option value="2">Dauer</option><option value="3">Anzahl</option><option value="4">Richtige</option><option value="5">Falsche</option><option value="6">Punkte</option><option value="7">Versuche</option><option value="8">Auslassungen</option>';
$sOFlds='<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option>';

?>

<form action="konfStatistik.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Funktion Bestenliste/Statistikseite kann für jedermann aufrufbar,
nur für angemeldete Benutzer sichtbar oder generell deaktiviert und unerreichbar sein.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bestenliste aktivieren</td>
 <td><input type="checkbox" class="admCheck" name="Statistik" value="1<?php if($fsStatistik) echo'" checked="checked'?>" /> Bestenliste/Statistikseite erlauben<br>
 <input type="radio" class="admRadio" name="StatOffen" value="1<?php if($fsStatOffen) echo'" checked="checked'?>" /> für alle (Gäste, Teilnehmer und Benutzer) &nbsp;
 <input type="radio" class="admRadio" name="StatOffen" value="0<?php if(!$fsStatOffen) echo'" checked="checked'?>" /> nur für angemeldete Benutzer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenüberschrift</td>
 <td><input type="text" name="TxStatistik" value="<?php echo $fsTxStatistik?>" style="width:99%" />
 <div class="admMini"><u>Muster</u>: Die bisher besten Resultate lauten:</div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Bestenliste/Statistikseite kann variabel sortiert werden und folgende Inhalte bieten:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listensortierung</td>
 <td><select name="StatSortier1" style="width:100px;" size="1"><?php echo str_replace('value="'.$fsStatSortier1.'"','value="'.$fsStatSortier1.'" selected="selected"',$sOSort)?></select> &nbsp;1. Sortierfeld (Hauptkriterium)<br>
 <div style="margin-top:3px;"><input type="radio" class="admRadio" name="StatSortAbsteig" value="1<?php if($fsStatSortAbsteig) echo'" checked="checked'?>" /> absteigende Reihenfolge &nbsp; <input type="radio" class="admRadio" name="StatSortAbsteig" value="0<?php if(!$fsStatSortAbsteig) echo'" checked="checked'?>" /> aufsteigende Reihenfolge</div>
 <select name="StatSortier2" style="width:100px;" size="1"><?php echo str_replace('value="'.$fsStatSortier2.'"','value="'.$fsStatSortier2.'" selected="selected"',$sOSort)?></select> &nbsp;2. Sortierfeld (bei Gleicheit im Hauptkriterium)
 <div style="margin-top:3px;"><input type="radio" class="admRadio" name="StatSrt2Absteig" value="1<?php if($fsStatSrt2Absteig) echo'" checked="checked'?>" /> absteigende Reihenfolge &nbsp; <input type="radio" class="admRadio" name="StatSrt2Absteig" value="0<?php if(!$fsStatSrt2Absteig) echo'" checked="checked'?>" /> aufsteigende Reihenfolge</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td><input type="text" name="StatListenZeilen" value="<?php echo $fsStatListenZeilen?>" size="3" style="width:3em;" /> Ergebniszeilen <span class="admMini">(Empfehlung: max. <i>20</i> Zeilen)</span></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Listeninhalt</td>
 <td>
  <table class="admTabl" width="100%" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl">
    <td>Feld</td>
    <td>Spalte für<br>Gäste und Teilnehmer</td>
    <td>Spalte für<br>angemeldete Benutzer</td>
    <td>optionaler CSS-Stil</td>
   </tr><tr class="admTabl">
    <td>lfd. Nummer</td>
    <td><select name="Felder0" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[0])?str_replace('value="'.$aFelder[0].'"','value="'.$aFelder[0].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder0" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[0])?str_replace('value="'.$aNFelder[0].'"','value="'.$aNFelder[0].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil0" value="<?php if(isset($aCssStil[0])) echo $aCssStil[0]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Datum/Zeit</td>
    <td><select name="Felder1" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[1])?str_replace('value="'.$aFelder[1].'"','value="'.$aFelder[1].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder1" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[1])?str_replace('value="'.$aNFelder[1].'"','value="'.$aNFelder[1].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil1" value="<?php if(isset($aCssStil[1])) echo $aCssStil[1]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Dauer</td>
    <td><select name="Felder2" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[2])?str_replace('value="'.$aFelder[2].'"','value="'.$aFelder[2].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder2" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[2])?str_replace('value="'.$aNFelder[2].'"','value="'.$aNFelder[2].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil2" value="<?php if(isset($aCssStil[2])) echo $aCssStil[2]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Anzahl</td>
    <td><select name="Felder3" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[3])?str_replace('value="'.$aFelder[3].'"','value="'.$aFelder[3].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder3" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[3])?str_replace('value="'.$aNFelder[3].'"','value="'.$aNFelder[3].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil3" value="<?php if(isset($aCssStil[3])) echo $aCssStil[3]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Richtige</td>
    <td><select name="Felder4" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[4])?str_replace('value="'.$aFelder[4].'"','value="'.$aFelder[4].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder4" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[4])?str_replace('value="'.$aNFelder[4].'"','value="'.$aNFelder[4].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil4" value="<?php if(isset($aCssStil[4])) echo $aCssStil[4]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Falsche</td>
    <td><select name="Felder5" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[5])?str_replace('value="'.$aFelder[5].'"','value="'.$aFelder[5].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder5" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[5])?str_replace('value="'.$aNFelder[5].'"','value="'.$aNFelder[5].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil5" value="<?php if(isset($aCssStil[5])) echo $aCssStil[5]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Punkte</td>
    <td><select name="Felder6" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[6])?str_replace('value="'.$aFelder[6].'"','value="'.$aFelder[6].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder6" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[6])?str_replace('value="'.$aNFelder[6].'"','value="'.$aNFelder[6].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil6" value="<?php if(isset($aCssStil[6])) echo $aCssStil[6]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Versuche</td>
    <td><select name="Felder7" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[7])?str_replace('value="'.$aFelder[7].'"','value="'.$aFelder[7].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder7" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[7])?str_replace('value="'.$aNFelder[7].'"','value="'.$aNFelder[7].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil7" value="<?php if(isset($aCssStil[7])) echo $aCssStil[7]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Auslassungen</td>
    <td><select name="Felder8" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[8])?str_replace('value="'.$aFelder[8].'"','value="'.$aFelder[8].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder8" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[8])?str_replace('value="'.$aNFelder[8].'"','value="'.$aNFelder[8].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil8" value="<?php if(isset($aCssStil[8])) echo $aCssStil[8]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Testfolgen-<br>Name</td>
    <td><select name="Felder12" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[12])?str_replace('value="'.$aFelder[12].'"','value="'.$aFelder[12].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder12" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[12])?str_replace('value="'.$aNFelder[12].'"','value="'.$aNFelder[12].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil12" value="<?php if(isset($aCssStil[12])) echo $aCssStil[12]?>" style="width:250px;" /></td>
   </tr><tr class="admTabl">
    <td>Teilnehmer</td>
    <td><select name="Felder13" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aFelder[13])?str_replace('value="'.$aFelder[13].'"','value="'.$aFelder[13].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><select name="NFelder13" style="width:50px;" size="1"><option value="0">---</option><?php echo (isset($aNFelder[13])?str_replace('value="'.$aNFelder[13].'"','value="'.$aNFelder[13].'" selected="selected"',$sOFlds):$sOFlds)?></select></td>
    <td><input type="text" name="CssStil13" value="<?php if(isset($aCssStil[13])) echo $aCssStil[13]?>" style="width:250px;" /></td>
   </tr>
  </table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datum / Uhrzeit</td>
 <td><input type="radio" class="admRadio" name="StatDatumZeit" value="1<?php if($fsStatDatumZeit) echo '" checked="checked'?>" /> Datumsfeld mit Uhrzeit &nbsp; <input type="radio" class="admRadio" name="StatDatumZeit" value="0<?php if(!$fsStatDatumZeit) echo '" checked="checked'?>" /> Datumsfeld ohne Uhrzeit</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kommastellen bei<br>Punkte</td>
 <td><input type="text" name="StatKommaStellen" value="<?php echo $fsStatKommaStellen?>" size="3" style="width:3em;" /> &nbsp; <span class="admMini">(Empfehlung: 0..2 Kommastellen)</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern das Feld vom Typ <i>Teilnehmer</i> im Listeninhalt aktiviert ist, können folgende Angaben zu den Teilnehmern in der Bestenliste dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerfelder</td>
 <td><?php for($i=1;$i<$nTlnFlds;$i++){?>
  <div><input type="checkbox" class="admCheck" name="TlnFeld<?php echo $i?>" value="1<?php if($aTlnFld[$i]) echo'" checked="checked'?>" /> &nbsp;<?php echo sprintf('%02d',$i)?>. Feld derzeit &nbsp; <i><?php echo $aTlnFldNa[$i]?></i></div>
<?php }?> </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Trennzeichen für<br>Teilnehmerfelder</td>
 <td><input type="text" name="StatTlnTrn" value="<?php echo $fsStatTlnTrn?>" size="4" style="width:4em;" />
 <div class="admMini"><u>Hinweis</u>: als Trennzeichen sind Komma, Bindestrich, Doppelpunkt oder Leerzeichen, aber auch einfache HTML-Zeichen wie Zeilenwechsel &lt;br&gt; oder &lt;br /&gt; zulässig</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerfelder</td>
 <td><?php for($i=1;$i<$nNtzFlds;$i++) if($i!=2&&$i!=4){?>
  <div><input type="checkbox" class="admCheck" name="NtzFeld<?php echo $i?>" value="1<?php if($aNtzFld[$i]) echo'" checked="checked'?>" /> &nbsp;<?php echo sprintf('%02d',$i)?>. Feld derzeit &nbsp; <i><?php echo $aNtzFldNa[$i]?></i></div>
<?php }?> </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Trennzeichen für<br>Benutzerfelder</td>
 <td><input type="text" name="StatNtzTrn" value="<?php echo $fsStatNtzTrn?>" size="4" style="width:4em;" />
 <div class="admMini"><u>Hinweis</u>: als Trennzeichen sind Komma, Bindestrich, Doppelpunkt oder Leerzeichen, aber auch einfache HTML-Zeichen wie Zeilenwechsel &lt;br&gt; oder &lt;br /&gt; zulässig</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ersatztexte</td>
 <td><input type="text" name="TxKeinTlnNam" value="<?php echo $fsTxKeinTlnNam?>" size="10" style="width:10em;" /> unbekannte Teilnehmer <span class="admMini">(Muster: <i>Gast</i>)</span><br>
 <input type="text" name="TxKeinNtzNam" value="<?php echo $fsTxKeinNtzNam?>" size="10" style="width:10em;" /> unbekannte Benutzer <span class="admMini">(Muster: <i>unbekannt</i>)</span>
 </td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function txtVarL($Var){return (isset($_POST[$Var])?str_replace('"',"'",stripslashes($_POST[$Var])):'');}
?>