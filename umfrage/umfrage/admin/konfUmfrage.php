<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ablauf-Einstellungen','','UVU');

if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false; $aUf=array();
  for($i=29;$i>0;$i--) if(($k=txtVar('UmF_'.$i))&&($v=txtVar('UmB_'.$i))){
   $aUn[$k]=(txtVar('UmN_'.$i)=='1'?'1':'0'); $aUt[$k]=(txtVar('UmT_'.$i)=='1'?'1':'0');
   $aUf[$k]=(txtVar('UmS_'.$i)=='1'?'0':'1').';'.$aUn[$k].';'.$aUt[$k].';'.str_replace(';',',',$v).';';
  }
  for($i=1;$i<=26;$i++){
   $k=chr(64+$i); $s=(isset($aUf[$k])?$aUf[$k]:';;;;'); $t=constant('UMF_Umfr'.$k); ${'usUMF_Umfr'.$k}=$t;
   $bCode=($s!=';;;;')&&(UMF_NutzerMitCode&&$aUn[$k]||UMF_TeilnehmerMitCode&&$aUt[$k]);
   if(substr($t,0,strlen($s))!=$s){$s.=($bCode?rand(1001,9998):''); if(fSetzUmfWert($s,'Umfr'.$k,"'")){$bNeu=true; ${'usUMF_Umfr'.$k}=$s;}}
  }
  $v=(int)txtVar('NutzerNormUmfrage'); if(fSetzUmfWert(($v?true:false),'NutzerNormUmfrage','')) $bNeu=true;
  $v=(int)txtVar('TeilnehmerNormUmfrage'); if(fSetzUmfWert(($v?true:false),'TeilnehmerNormUmfrage','')) $bNeu=true;
  $v=max(min((int)txtVar('StdUmfrCode'),9999),0); if(fSetzUmfWert($v,'StdUmfrCode','')) $bNeu=true;
  $v=txtVar('TxStandardUmf'); if(fSetzUmfWert($v,'TxStandardUmf',"'")) $bNeu=true;
  $v=(int)txtVar('UmfrUnscharf'); if(fSetzUmfWert(($v?true:false),'UmfrUnscharf','')) $bNeu=true;
  $v=(int)txtVar('NutzerAlleUmfrage'); if(fSetzUmfWert(($v?true:false),'NutzerAlleUmfrage','')) $bNeu=true;
  $v=(int)txtVar('TeilnehmerAlleUmfrage'); if(fSetzUmfWert(($v?true:false),'TeilnehmerAlleUmfrage','')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> durfte nicht geschrieben werden (Rechteproblem)!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Umfrage-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Einstellungen für die vorbereiteten Umfragen bleiben unverändert.</p>';
}else{ //GET
 $sMeld='<p class="admMeld">Organisieren Sie hier die Bedingungen für vorbereitete Umfragen.</p>'; $usStdUmfrCode=UMF_StdUmfrCode;
 $usUmfrUnscharf=UMF_UmfrUnscharf; $usNutzerAlleUmfrage=UMF_NutzerAlleUmfrage; $usTeilnehmerAlleUmfrage=UMF_TeilnehmerAlleUmfrage;
 $usNutzerNormUmfrage=UMF_NutzerNormUmfrage; $usTeilnehmerNormUmfrage=UMF_TeilnehmerNormUmfrage; $usTxStandardUmf=UMF_TxStandardUmf;
 for($i=1;$i<=26;$i++){$k=chr(64+$i); ${'usUMF_Umfr'.$k}=constant('UMF_Umfr'.$k);}
}

//Scriptausgabe
echo $sMeld.NL;
$sZl=''; $sOptU=''; for($i=1;$i<=26;$i++) $sOptU.='<option value="'.chr($i+64).'">Umfrage '.chr($i+64).'</option>'; $i=0;
$sZl=NL.'   <tr class="admTabl">
   <td style="text-align:center">Standardumfrage</td>
   <td style="text-align:center">--</td>
   <td style="text-align:center"><input class="admCheck" type="checkbox" name="NutzerNormUmfrage" value="1"'.($usNutzerNormUmfrage?' checked="checked"':'').' /></td>
   <td style="text-align:center"><input class="admCheck" type="checkbox" name="TeilnehmerNormUmfrage" value="1"'.($usTeilnehmerNormUmfrage?' checked="checked"':'').' /></td>
   <td style="text-align:center"><input type="text" name="StdUmfrCode" value="'.($usStdUmfrCode?$usStdUmfrCode:'').'" size="4" maxlength="4" style="width:3.5em" /></td>
   <td><input type="text" name="TxStandardUmf" value="'.$usTxStandardUmf.'" size="80" maxlength="80" style="width:99%" /></td>
  </tr>';
for($i=1;$i<=26;$i++){
 $k=chr(64+$i); $a=explode(';',${'usUMF_Umfr'.$k});
 if($s=$a[3]) $sZl.=NL.'   <tr class="admTabl">
  <td style="text-align:center"><select name="UmF_'.$i.'" size="1"><option value="">---</option>'.(str_replace('value="'.$k.'"','value="'.$k.'" selected="selected"',$sOptU)).'</select></td>
  <td style="text-align:center"><input class="admCheck" name="UmS_'.$i.'" type="checkbox" value="1"'.($a[0]==='0'?' checked="checked"':'').' /></td>
  <td style="text-align:center"><input class="admCheck" name="UmN_'.$i.'" type="checkbox" value="1"'.($a[1]=='1'?' checked="checked"':'').' /></td>
  <td style="text-align:center"><input class="admCheck" name="UmT_'.$i.'" type="checkbox" value="1"'.($a[2]=='1'?' checked="checked"':'').' /></td>
  <td style="text-align:center">'.$a[4].'</td>
  <td><input name="UmB_'.$i.'" type="text" value="'.$s.'" size="80" maxlength="80" style="width:99%" /></td>
 </tr>';
}
for($i=27;$i<30;$i++){
 $sZl.=NL.'   <tr class="admTabl">';
 $sZl.=NL.'    <td style="text-align:center"><select name="UmF_'.$i.'" size="1"><option value="">---</option>'.$sOptU.'</select></td>';
 $sZl.=NL.'    <td style="text-align:center"><input class="admCheck" name="UmS_'.$i.'" type="checkbox" value="1" /></td>';
 $sZl.=NL.'    <td style="text-align:center"><input class="admCheck" name="UmN_'.$i.'" type="checkbox" value="1" /></td>';
 $sZl.=NL.'    <td style="text-align:center"><input class="admCheck" name="UmT_'.$i.'" type="checkbox" value="1" /></td>';
 $sZl.=NL.'    <td>&nbsp;</td>';
 $sZl.=NL.'    <td><input name="UmB_'.$i.'" type="text" value="" size="80" maxlength="80" style="width:99%" /></td>';
 $sZl.=NL.'   </tr>';
}
?>

<form name="umfrageform" action="konfUmfrage.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Programm erlaubt es, bis zu 27 unterschiedliche Umfragen pro Konfiguration zu veranstalten. Das sind die Standardumfrage plus die Umfragen-A...Z. Dazu können die Fragen über einen Kennbuchtaben A...Z einer der 26 vorbereiteten Umfrage-A...Z zugeordnet sein. Hier können Sie solche speziellen Umfragen vorbereiten und verwalten. Sie müssen jedoch nicht zwangsweise alle Umfragen per Kennbuchstaben hier vorbereiten.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Umfrage-<br />eigenschaften</td>
 <td>
  <table class="admTabl" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl">
    <td rowspan="2" style="text-align:center"><b>Kennung</b></td>
    <td rowspan="2"><b>streng</b></td>
    <td colspan="2" style="text-align:center"><b>erlaubt für</b></td>
    <td rowspan="2" style="text-align:center"><b>Code</b></td>
    <td rowspan="2"><b>Umfragetitel</b></td>
   </tr><tr class="admTabl">
    <td><b>Nutzer</b></td>
    <td><b>Teiln.</b></td>
   </tr><?php echo $sZl?>
  </table>
 <div class="admMini">Hinweis: Nur die Kennbuchstaben A...Z eintragen und betiteln, die als vorbereitete Umfragen existieren sollen.</div>
 <div class="admMini">Hinweis: Der Aktiv-Code zur Umfrage wird automatisch generiert, sobald <i>Code</i> unter <i>Benutzerfunktionen</i> bzw. <i>Teilnehmerfunktionen</i> aktiviert ist und die Umfrage hier für Benutzer oder Teilnehmer erlaubt wird.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">generelle<br />Schärfe bei der<br />Fragenauswahl</td>
 <td><input type="radio" class="admRadio" name="UmfrUnscharf" value="0<?php if(!$usUmfrUnscharf) echo '" checked="checked'?>" /> streng (scharf) &nbsp; &nbsp; <input type="radio" class="admRadio" name="UmfrUnscharf" value="1<?php if($usUmfrUnscharf) echo '" checked="checked'?>" /> unscharf (tolerant)
 <div class="admMini">Erklärung: Bei <i>streng</i> gehören <i>nur</i> die Fragen aus der Fragenliste zur Umfrage, die den jeweiligen Kennbuchstaben haben. Bei <i>unscharf</i> gehören noch die Fragen zusätzlich zur Umfrage, die gänzlich <i>ohne</i> Kennbuchstaben also Umfragenzuordnung sind.</div>
 <div class="admMini">Hinweis: Diese generelle Einstellung gilt nur für die Umfragen die nicht in obiger Liste vereinbart sind.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Umfrageangebot<br />für Benutzer</td>
 <td><input type="checkbox" class="admCheck" name="NutzerAlleUmfrage" value="1<?php if($usNutzerAlleUmfrage) echo '" checked="checked'?>" /> alle obigen Umfragen sollen im Benutzerzentrum allen Benutzer angeboten werden, sofern für den Benutzer nichts individuelles unter <i>Benutzer &amp; Umfragen</i> vereinbar wurde.
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Umfrageangebot<br />für Teilnehmer</td>
 <td><input type="checkbox" class="admCheck" name="TeilnehmerAlleUmfrage" value="1<?php if($usTeilnehmerAlleUmfrage) echo '" checked="checked'?>" /> alle obigen Umfragen sollen in der Umfrageauswahlliste jedem Teilnehmer angeboten werden.
 </td>
</tr>

</table>

<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>



<?php echo fSeitenFuss();?>