<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Umfragedaten exportieren','','ZFe');

if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['fnr'])&&$_POST['fnr']) $aFld[]=0; if(isset($_POST['onl'])&&$_POST['onl']) $aFld[]=1; if(isset($_POST['umf'])&&$_POST['umf']) $aFld[]=2;
 if(isset($_POST['fra'])&&$_POST['fra']) $aFld[]=3; if(isset($_POST['bld'])&&$_POST['bld']) $aFld[]=4;
 if(isset($_POST['bem'])&&$_POST['bem']) $aFld[]=5; if(isset($_POST['b2m'])&&$_POST['b2m']) $aFld[]=6; $nAntwAnzahl=max(20,ADU_AntwortZahl);
 for($i=1;$i<=$nAntwAnzahl;$i++) if(isset($_POST['aw'.$i])&&$_POST['aw'.$i]) $aFld[]=6+$i;
 $Onl=(isset($_POST['onl1'])?$_POST['onl1']:'').(isset($_POST['onl2'])?$_POST['onl2']:''); if(strlen($Onl)!=1) $Onl='';
 $a=array('Nummer','aktiv','Umfrage','Frage','Bild','Anmerkung1','Anmerkung2'); for($i=1;$i<=$nAntwAnzahl;$i++) $a[]='Antwort'.$i;
 $Umf=(isset($_POST['ufr'])?$_POST['ufr']:''); $nFlds=count($aFld)-1; $sDat=''; for($i=0;$i<$nFlds;$i++) $sDat.=$a[$aFld[$i]].';'; $sDat.=$a[$aFld[$nFlds]].NL;

 if(!UMF_SQL){//Text
  $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nCnt=count($aD);
  for($i=1;$i<$nCnt;$i++){
   $a=explode(';',rtrim($aD[$i])); $bOk=true;
   if(!empty($Onl)) if($Onl=='1'&&$a[1]!='1'||$Onl=='-'&&$a[1]!='0') $bOk=false;
   if(!empty($Umf)&&$bOk) if($Umf!=$a[2]) $bOk=false;
   if($bOk){for($j=0;$j<$nFlds;$j++) $sDat.=$a[$aFld[$j]].';'; $sDat.=(isset($a[$aFld[$nFlds]])?$a[$aFld[$nFlds]]:'').NL;} //Datensatz gueltig
  }
 }elseif($DbO){//SQL
  $sF=''; if(!empty($Umf)) $sF.=' AND Umfrage="'.$Umf.'"';
  if(!empty($Onl)) $sF.=' AND aktiv="'.($Onl=='1'?'1':'0').'"';
  if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.($sF>''?' WHERE'.substr($sF,4):'').' ORDER BY Nummer')){
   while($a=$rR->fetch_row()){
    for($j=0;$j<$nFlds;$j++) $sDat.=(isset($a[$aFld[$j]])?str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$a[$aFld[$j]]))):'').';';
    $sDat.=(isset($a[$aFld[$nFlds]])?str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$a[$aFld[$nFlds]]))):'').NL;
   }
   $rR->close();
  }else $sMeld='<p class="admFehl">'.UMF_TxSqlFrage.'</p>';
 }else $sMeld='<p class="admFehl">'.UMF_TxSqlVrbdg.'</p>';
 if($nFlds>0&&substr_count($sDat,NL)>1){
  $i=sprintf('%02d',date('s'));
  if($f=fopen(UMF_Pfad.'temp/fragen_'.$i.'.csv','w')){
   $sMeld.='<p class="admErfo" style="margin:32px;text-align:center;">Die Fragen wurden als <a href="http://'.UMF_Www.'temp/fragen_'.$i.'.csv"><i>fragen_'.$i.'.csv</i></a> exportiert!</p>';
   fwrite($f,$sDat); fclose($f); $MTyp='Erfo';
  }else $sMeld='<p class="admFehl">'.str_replace('#','temp/fragen_'.$i.'.csv',UMF_TxDateiRechte).'</p>';
 }else $sMeld='Keine Daten zu exportieren!';
 echo $sMeld.NL;
}else{ //GET
 for($i=59;$i>=0;$i--) if(file_exists(UMF_Pfad.'temp/fragen_'.sprintf('%02d',$i).'.csv')) unlink(UMF_Pfad.'temp/fragen_'.sprintf('%02d',$i).'.csv');
?>
<p class="admMeld">Stellen Sie die Daten für den Export zusammen.</p>

<form name="fraExport" action="fragenExport.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1">Datenfelder</td><td>
   <div><input class="admCheck" type="checkbox" name="fnr" value="1" checked="checked" /> Nummer (Fragenummer)</div>
   <div><input class="admCheck" type="checkbox" name="onl" value="1" /> aktiv</div>
   <div><input class="admCheck" type="checkbox" name="umf" value="1" /> Umfrage</div>
   <div><input class="admCheck" type="checkbox" name="fra" value="1" checked="checked" /> Frage</div>
   <div><input class="admCheck" type="checkbox" name="bld" value="1" /> Bildname</div>
   <div><input class="admCheck" type="checkbox" name="aw1" value="1" checked="checked" /> Antwort-1 &nbsp; &nbsp; &nbsp; (Antwortanzahl bis maximal 20 einzustellen unter <a href="konfAdmin.php<?php if(KONF>0)echo'?konf='.KONF?>">Admin-Einstellungen</a>)<b></b>
   <?php for($i=2;$i<=ADU_AntwortZahl;$i++){?><div><input class="admCheck" type="checkbox" name="aw<?php echo $i?>" value="1<?php if($i<4) echo '" checked="checked'?>" /> Antwort-<?php echo $i?></div><?php }?>
   <div><input class="admCheck" type="checkbox" name="bem" value="1" checked="checked" /> Anmerkung-1</div>
   <div><input class="admCheck" type="checkbox" name="b2m" value="1" checked="checked" /> Anmerkung-2</div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Fragenauswahl</td>
  <td>
   <input type="checkbox" class="admCheck" name="onl1" value="1"> nur aktivierte Fragen<br />
   <input type="checkbox" class="admCheck" name="onl2" value="-"> nur deaktivierte Fragen<br />
   <select name="ufr" size="1"><option value=""></option><option value="">alle Umfragen</option><?php for($i=1;$i<=26;$i++) echo '<option value="'.chr($i+64).'">Umfrage '.chr($i+64).'</option>';?></select>
  </td>
 </tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Exportieren"></p>
</form>

<?php }?>

<p><u>Hinweis</u>:</p>
<ul>
<li>Die Fragen werden im Semikolon-getrennten CSV-Format exportiert und können beispielsweise mit MS-Excel<sup>&reg;</sup> weiter bearbeitet werden.</li>
</ul>

<?php echo fSeitenFuss();?>