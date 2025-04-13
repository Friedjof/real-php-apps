<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Fragedaten exportieren','','Exp');

if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['fnr'])&&$_POST['fnr']) $aFld[]=0; if(isset($_POST['onl'])&&$_POST['onl']) $aFld[]=1; if(isset($_POST['vst'])&&$_POST['vst']) $aFld[]=2; if(isset($_POST['kat'])&&$_POST['kat']) $aFld[]=3;
 if(isset($_POST['fra'])&&$_POST['fra']) $aFld[]=4; if(isset($_POST['lsg'])&&$_POST['lsg']) $aFld[]=5; if(isset($_POST['pkt'])&&$_POST['pkt']) $aFld[]=6; if(isset($_POST['bld'])&&$_POST['bld']) $aFld[]=7;

 for($i=1;$i<=9;$i++) if(isset($_POST['aw'.$i])&&$_POST['aw'.$i]) $aFld[]=7+$i; if(isset($_POST['bem'])&&$_POST['bem']) $aFld[]=17; if(isset($_POST['b2m'])&&$_POST['b2m']) $aFld[]=18;
 $Onl=(isset($_POST['onl1'])?$_POST['onl1']:'').(isset($_POST['onl2'])?$_POST['onl2']:''); if(strlen($Onl)!=1) $Onl=''; $Vst=(isset($_POST['vst1'])?$_POST['vst1']:'').(isset($_POST['vst2'])?$_POST['vst2']:''); if(strlen($Vst)!=1) $Vst='';
 $a=array('Nummer','aktiv','versteckt','Kategorie','Frage','Loesung','Punkte','Bild','Antwort1','Antwort2','Antwort3','Antwort4','Antwort5','Antwort6','Antwort7','Antwort8','Antwort9','Anmerkung','Anmerkung2');
 $Kat=(isset($_POST['flt'])?$_POST['flt']:''); $nFlds=count($aFld)-1; $sDat=''; for($i=0;$i<$nFlds;$i++) $sDat.=$a[$aFld[$i]].';'; $sDat.=$a[$aFld[$nFlds]].NL;

 if(!FRA_SQL){//Text
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nCnt=count($aD);
  for($i=1;$i<$nCnt;$i++){
   $a=explode(';',rtrim($aD[$i])); $bOk=true;
   if(!empty($Onl)) if($Onl=='1'&&$a[1]!='1'||$Onl=='-'&&$a[1]!='0') $bOk=false;
   if(!empty($Vst)&&$bOk) if($Vst=='-'&&$a[2]!='0'||$Vst=='1'&&$a[2]!='1') $bOk=false;
   if(!empty($Kat)&&$bOk) if($Kat!=$a[3]) $bOk=false;
   if($bOk){for($j=0;$j<$nFlds;$j++) $sDat.=$a[$aFld[$j]].';'; $sDat.=(isset($a[$aFld[$nFlds]])?$a[$aFld[$nFlds]]:'').NL;} //Datensatz gueltig
  }
 }elseif($DbO){//SQL
  $sF=''; if(!empty($Kat)) $sF.=' AND Kategorie="'.$Kat.'"';
  if(!empty($Onl)) $sF.=' AND aktiv="'.($Onl=='1'?'1':'0').'"';
  if(!empty($Vst)) $sF.=' AND versteckt="'.($Vst=='1'?'1':'0').'"';
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.($sF>''?' WHERE'.substr($sF,4):'').' ORDER BY Nummer')){
   while($a=$rR->fetch_row()){
    for($j=0;$j<$nFlds;$j++) $sDat.=str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$a[$aFld[$j]]))).';';
    $sDat.=(isset($a[$aFld[$nFlds]])?str_replace("\n",'\n ',str_replace("\r",'',str_replace(';','`,',$a[$aFld[$nFlds]]))):'').NL;
   }
   $rR->close();
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
 }else $sMeld='<p class="admFehl">'.FRA_TxSqlVrbdg.'</p>';
 if($nFlds>0&&substr_count($sDat,NL)>1){
  $i=sprintf('%02d',date('s'));
  if($f=fopen(FRA_Pfad.'temp/fragen_'.$i.'.csv','w')){
   $sMeld.='<p class="admErfo" style="margin:32px;text-align:center;">Die Fragen wurden als <a href="http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.'temp/fragen_'.$i.'.csv"><i>fragen_'.$i.'.csv</i></a> exportiert!</p>';
   fwrite($f,$sDat); fclose($f); $MTyp='Erfo';
  }else $sMeld='<p class="admFehl">'.str_replace('#','temp/fragen_'.$i.'.csv',FRA_TxDateiRechte).'</p>';
 }else $sMeld='Keine Daten zu exportieren!';
 echo $sMeld.NL;
}else{ //GET
 for($i=59;$i>=0;$i--) if(file_exists(FRA_Pfad.'temp/fragen_'.sprintf('%02d',$i).'.csv')) unlink(FRA_Pfad.'temp/fragen_'.sprintf('%02d',$i).'.csv');
?>
<p class="admMeld">Stellen Sie die Daten für den Export zusammen.</p>

<form name="fraExport" action="export.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1">Datenfelder</td><td>
   <div><input class="admCheck" type="checkbox" name="fnr" value="1" checked="checked" /> Nummer (Fragenummer)</div>
   <div><input class="admCheck" type="checkbox" name="onl" value="1" /> aktiv</div>
   <div><input class="admCheck" type="checkbox" name="vst" value="1" /> versteckt</div>
   <?php if(FRA_Kategorien){?><div><input class="admCheck" type="checkbox" name="kat" value="1" checked="checked" /> Kategorie</div><?php }?>
   <div><input class="admCheck" type="checkbox" name="fra" value="1" checked="checked" /> Frage</div>
   <div><input class="admCheck" type="checkbox" name="lsg" value="1" checked="checked" /> Lösung (Lösungsnummer)</div>
   <div><input class="admCheck" type="checkbox" name="pkt" value="1" /> Punkte</div>
   <?php if(FRA_LayoutTyp>0){?><div><input class="admCheck" type="checkbox" name="bld" value="1" /> Bildname</div><?php }?>
   <?php for($i=1;$i<=ADF_AntwortZahl;$i++){?><div><input class="admCheck" type="checkbox" name="aw<?php echo $i?>" value="1<?php if($i<4) echo '" checked="checked'?>" /> Antwort-<?php echo $i?></div><?php }?>
   <div><input class="admCheck" type="checkbox" name="bem" value="1" checked="checked" /> Anmerkung</div>
   <div><input class="admCheck" type="checkbox" name="b2m" value="1" checked="checked" /> Anmerkung-2</div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1">Fragenauswahl</td><td>
   <table border="0" cellpadding="0" cellspacing="0">
    <tr>
     <td style="padding-right:16px;"><input type="checkbox" class="admCheck" name="onl1" value="1"> nur aktivierte Fragen<br /><input type="checkbox" class="admCheck" name="onl2" value="-"> nur deaktivierte Fragen</td>
     <td style="padding-right:16px;"><input type="checkbox" class="admCheck" name="vst1" value="1"> nur versteckte Fragen<br /><input type="checkbox" class="admCheck" name="vst2" value="-"> nur öffentliche Fragen</td>
     <?php if(FRA_Kategorien){?><td><select name="flt" size="1"><option value="">alle Kategorien</option><?php $aKat=explode(';',FRA_Kategorien); foreach($aKat as $v=>$k) if(!empty($k)){$k=str_replace('`,',';',$k); echo '<option value="'.$k.'">'.$k.'</option>';}?></select></td><?php }?>
    </tr>
   </table>
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