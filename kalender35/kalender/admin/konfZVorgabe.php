<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Vorgabewerte Zusagenformular anpassen','','KZs');

$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $kal_ZusageAuswahl=explode(';',KAL_ZusageAuswahl); $nFelder=count($kal_ZusageFelder);
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 if($Id=(isset($_POST['id'])?$_POST['id']:'')){
  $sAww=str_replace('"',"'",str_replace('|','/',str_replace(';','',str_replace("\n\n",NL,str_replace("\r",'',stripslashes(@strip_tags(trim($_POST['aww']))))))));
  $aNeu=explode(NL,$sAww); $nNeu=min(count($aNeu),675); $aAlt=explode('|',trim($kal_ZusageAuswahl[$Id]));
  if($aNeu!=$aAlt){
   $s=''; $sZhl='A'; $sZl1=''; $a=array();
   for($i=0;$i<$nNeu;$i++){
    $s.='|'.$aNeu[$i];
    if($sZhl<'Z') $sZhl++; else{$sZhl='A'; if($sZl1>'') $sZl1++; else $sZl1='A';}
   }
   $kal_ZusageAuswahl[$Id]=substr($s,1);
   $s=';;;;;;;;'; for($i=9;$i<$nFelder;$i++) $s.=';'.$kal_ZusageAuswahl[$i];
   $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
   if(fSetzKalWert($s,'ZusageAuswahl',"'")) $bNeu=true;
   if($bNeu){ //geaendert
    if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
     $Msg='<p class="admErfo">Die Einstellungen für das Zusagesystem wurden gespeichert.</p>'; $aF=array();
    }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
   }else $Msg='<p class="admMeld">Die Vorgabewerte wurden nicht verändert.</p>';
  }else $Msg='<p class="admMeld">Die Vorgabewerte bleiben unverändert.</p>';
 }else $Msg='<p class="admFehl">fehlerhafter Seitenaufruf ohne gültige Feldnummer!</p>';
}

//Seitenausgabe
if($Id=(isset($_GET['id'])?$_GET['id']:(isset($_POST['id'])?$_POST['id']:''))){
 if(!$Msg) $Msg='<p class="admMeld">Legen Sie die Vorgabewerte für das Feld <i>'.$kal_ZusageFelder[$Id].'</i> fest.</p>';
}else $Msg='<p class="admFehl">fehlerhafter Seitenaufruf ohne gültige Feldnummer!</p>';
echo $Msg.NL;
?>

<form name="konfAww" action="konfZVorgabe.php" method="post">
<input type="hidden" name="id" value="<?php echo $Id?>" />
<?php
 $aAww=explode('|',trim($kal_ZusageAuswahl[$Id])); $nAww=count($aAww); $sAnr=''; $sAww=''; $sZhl='A'; $sZl1='';
 for($i=0;$i<$nAww;$i++){
  $sAnr.=$sZl1.$sZhl.NL; $sAww.=$aAww[$i].NL;
  if($sZhl<'Z') $sZhl++; else{$sZhl='A'; if($sZl1>'') $sZl1++; else $sZl1='A';}
 }
 $nHei=max(round(1.2*min($nAww+5,35)),15);
?>
<table class="admTabl" border="0" cellpadding="5" cellspacing="1">
<tr class="admTabl"><td align="center"><b>Nr.</b></td><td><b>Vorgabewert</b> <span class="admMini">(max. 675 Einträge)</span></td></tr>
<tr class="admTabl">
 <td valign="top">
  <textarea class="admEing" name="anr" cols="3" rows="10" style="width:45px;height:<?php echo $nHei?>em;" readonly="readonly"><?php echo rtrim($sAnr)?></textarea>
 </td>
 <td width="96%" valign="top">
  <textarea class="admEing" name="aww" cols="50" rows="10" style="height:<?php echo $nHei?>em;""><?php echo rtrim($sAww)?></textarea>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
<p class="admSubmit">[ <a href="konfZusage.php#Felder">zur Zusagenkonfiguration</a> ]</p>

<?php echo fSeitenFuss();?>