<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Fragen drucken','<script type="text/javascript">
 function druWin(){dWin=window.open("about:blank","druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
</script>','FDr');

$sMeld='';
if(isset($_GET['aktion'])&&$_GET['aktion']=='speichern'){
 $Onl=''; $Vst=''; $sDrS='';
 if($FNr1=(isset($_POST['fnr1'])?$_POST['fnr1']:'')) $sDrS.=';FNr1:'.sprintf('%0d',$FNr1);
 if($FNr2=(isset($_POST['fnr2'])?$_POST['fnr2']:'')) $sDrS.=';FNr2:'.sprintf('%0d',$FNr2);
 if($Onl1=(isset($_POST['onl1'])?$_POST['onl1']:'')) $Onl='1'; $Onl2=(isset($_POST['onl2'])?$_POST['onl2']:''); if($Onl2=='0') $Onl='0'; if($Onl1=='1'&&$Onl2=='0') $Onl='';
 if($Vst1=(isset($_POST['vst1'])?$_POST['vst1']:'')) $Vst='1'; $Vst2=(isset($_POST['vst2'])?$_POST['vst2']:''); if($Vst2=='0') $Vst='0'; if($Vst1=='1'&&$Vst2=='0') $Vst='';
 if(strlen($Onl)>0) $sDrS.=';Onl :'.$Onl; if(strlen($Vst)>0) $sDrS.=';Vst :'.$Vst;
 if($Frg1=(isset($_POST['frg1'])?$_POST['frg1']:'')) $sDrS.=';Frg1:'.str_replace(';','`,',$Frg1);
 if($Frg2=(isset($_POST['frg2'])?$_POST['frg2']:'')) $sDrS.=';Frg2:'.str_replace(';','`,',$Frg2);
 if($Frg3=(isset($_POST['frg3'])?$_POST['frg3']:'')) $sDrS.=';Frg3:'.str_replace(';','`,',$Frg3);
 if($Kat1=(isset($_POST['kat1'])?$_POST['kat1']:'')) $sDrS.=';Kat1:'.str_replace(';','`,',$Kat1);
 if($Kat2=(isset($_POST['kat2'])?$_POST['kat2']:'')) $sDrS.=';Kat2:'.str_replace(';','`,',$Kat2);
 if($Kat3=(isset($_POST['kat3'])?$_POST['kat3']:'')) $sDrS.=';Kat3:'.str_replace(';','`,',$Kat3);
 if($Bem1=(isset($_POST['bem1'])?$_POST['bem1']:'')) $sDrS.=';Bem1:'.str_replace(';','`,',$Bem1);
 if($Bem2=(isset($_POST['bem2'])?$_POST['bem2']:'')) $sDrS.=';Bem2:'.str_replace(';','`,',$Bem2);
 if($Bem3=(isset($_POST['bem3'])?$_POST['bem3']:'')) $sDrS.=';Bem3:'.str_replace(';','`,',$Bem3);
 if($B2m1=(isset($_POST['b2m1'])?$_POST['b2m1']:'')) $sDrS.=';B2m1:'.str_replace(';','`,',$B2m1);
 if($B2m2=(isset($_POST['b2m2'])?$_POST['b2m2']:'')) $sDrS.=';B2m2:'.str_replace(';','`,',$B2m2);
 if($B2m3=(isset($_POST['b2m3'])?$_POST['b2m3']:'')) $sDrS.=';B2m3:'.str_replace(';','`,',$B2m3);
 $aDru[0]=(isset($_POST['drN'])?(int)$_POST['drN']:0); $aDru[1]=(isset($_POST['drA'])?(int)$_POST['drA']:0); $aDru[2]=(isset($_POST['drV'])?(int)$_POST['drV']:0);
 $aDru[3]=(isset($_POST['drK'])?(int)$_POST['drK']:0); $aDru[4]=(isset($_POST['drF'])?(int)$_POST['drF']:0); $aDru[5]=(isset($_POST['drL'])?(int)$_POST['drL']:0);
 $aDru[6]=(isset($_POST['drP'])?(int)$_POST['drP']:0);
 if($aDru[7]=(isset($_POST['drG'])?(int)$_POST['drG']:0)){
  if($s=(isset($_POST['d_G'])?$_POST['d_G']:'')) $aDru[7]='1:'.$s.':'.(isset($_POST['d_B'])?(int)$_POST['d_B']:0);
 }
 $aDru[8]=(isset($_POST['drT'])?(int)$_POST['drT']:0);
 $aDru[9]=(isset($_POST['drB'])?(int)$_POST['drB']:0); $aDru[10]=(isset($_POST['dr2'])?(int)$_POST['dr2']:0);
 $aDru[11]=(isset($_POST['drS'])?(int)$_POST['drS']:0); $aDru[12]=(isset($_POST['drR'])?(int)$_POST['drR']:0); $aDru[13]=(isset($_POST['drZ'])?(int)$_POST['drZ']:0);
 $aDru[14]=(isset($_POST['drH'])?str_replace("'",'´',$_POST['drH']):'');
 if($n=(isset($_POST['drNn'])?(int)$_POST['drNn']:0)) if($aDru[0]) $aDru[0]=$n;
 $sDrF=''; for($i=0;$i<=14;$i++) $sDrF.=';'.$aDru[$i]; $sDrF=substr($sDrF,1); $sDrS=substr($sDrS,1);
 if($sDrF!=ADF_DruckFeld||$sDrS!=ADF_DruckSuch){
  $sKonf=(KONF?KONF:''); $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  if(setzAdmWert($sDrF,'DruckFeld',"'")) $bNeu=true; if(setzAdmWert($sDrS,'DruckSuch',"'")) $bNeu=true;
  if($bNeu){
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    $sMeld='<p class="admErfo">Die Administrator-Einstellungen wurden in der '.($sKonf?'':'Grund-').'Konfiguration'.($sKonf?'-'.$sKonf:'').' gespeichert.</p>';
   }else $sMeld='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
 }}
}else{
 $aDru=explode(';',constant('ADF_DruckFeld')); $aDrS=explode(';',constant('ADF_DruckSuch')); $FNr1=''; $FNr2=''; $Onl=''; $Vst='';
 $Frg1=''; $Frg2=''; $Frg3=''; $Kat1=''; $Kat2=''; $Kat3=''; $Bem1=''; $Bem2=''; $Bem3=''; $B2m1=''; $B2m2=''; $B2m3='';
 if(is_array($aDrS)&&($n=count($aDrS))) foreach($aDrS as $s) if(strpos($s,':')==4) ${trim(substr($s,0,4))}=str_replace('`,',';',substr($s,5));
}

if(!$sMeld) $sMeld='<p class="admMeld">Stellen Sie Ihre Druckliste zusammen!</p>';
echo $sMeld;
?>

<form name="fraEingabe" action="druckListe.php" method="post" target="druck" onsubmit="druWin()">
<?php if(KONF>0) echo '<input type="hidden" name="konf" value="'.KONF.'" />'?>
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td colspan="3" class="admSpa2">Auswahl der zu druckenden Fragen anhand folgender Filterbedingungen:</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Frage-Nummer<br /><input type="text" name="fnr1" value="<?php echo $FNr1?>" style="width:4em;" /> bis <input type="text" name="fnr2" value="<?php echo $FNr2?>" style="width:4em;" /></td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="onl1" value="1"<?php if($Onl=='1') echo ' checked="checked"'?>> nur aktivierte Fragen<br /><input type="checkbox" class="admCheck" name="onl2" value="0"<?php if($Onl=='0') echo ' checked="checked"'?>> nur deaktivierte Fragen</td>
  <td style="width:33%"><input type="checkbox" class="admCheck" name="vst1" value="1"<?php if($Vst=='1') echo ' checked="checked"'?>> nur versteckte Fragen<br /><input type="checkbox" class="admCheck" name="vst2" value="0"<?php if($Vst=='0') echo ' checked="checked"'?>> nur öffentliche Fragen</td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Fragetext wie <input type="text" name="frg1" value="<?php echo $Frg1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="frg2" value="<?php echo $Frg2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="frg3" value="<?php echo $Frg3?>" style="width:99%;" /></td>
 </tr><?php if(FRA_Kategorien>''){$aKat=explode(';',FRA_Kategorien);?>
 <tr class="admTabl">
  <td style="width:34%">Kategorie wie <select name="kat1" size="1" style="width:99%;"><option value=""></option><?php foreach($aKat as $v=>$k) if(!empty($k)){$k=str_replace('`,',';',$k); echo '<option value="'.$k.($k!=$Kat1?'':'" selected="selected').'">'.$k.'</option>';}?></select></td>
  <td style="width:33%">oder wie <input type="text" name="kat2" value="<?php echo $Kat2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="kat3" value="<?php echo $Kat3?>" style="width:99%;" /></td>
 </tr><?php }?>
 <tr class="admTabl">
  <td style="width:34%">Anmerkung wie <input type="text" name="bem1" value="<?php echo $Bem1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="bem2" value="<?php echo $Bem2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="bem3" value="<?php echo $Bem3?>" style="width:99%;" /></td>
 </tr>
 <tr class="admTabl">
  <td style="width:34%">Anmerkung-2 wie <input type="text" name="b2m1" value="<?php echo $B2m1?>" style="width:99%;" /></td>
  <td style="width:33%">oder wie <input type="text" name="b2m2" value="<?php echo $B2m2?>" style="width:99%;" /></td>
  <td style="width:33%">aber nicht wie <input type="text" name="b2m3" value="<?php echo $B2m3?>" style="width:99%;" /></td>
 </tr>
 <tr class="admTabl">
  <td colspan="3" class="admSpa2">Anzeige folgender Elemente in der Druckliste:</td>
 </tr>
 <tr class="admTabl">
  <td colspan="1">
   <div><input type="checkbox" class="admCheck" name="drN" value="1"<?php if($aDru[0]>='1') echo ' checked="checked"'?>> Frage-Nummer</div>
   <div>&nbsp; &nbsp; <input type="radio" class="admCheck" name="drNn" value="1"<?php if($aDru[0]=='1') echo ' checked="checked"'?>> Original-Nummern</div>
   <div>&nbsp; &nbsp; <input type="radio" class="admCheck" name="drNn" value="2"<?php if($aDru[0]=='2') echo ' checked="checked"'?>> chronologische Nummerierung</div>
   <div><input type="checkbox" class="admCheck" name="drA" value="1"<?php if($aDru[1]=='1') echo ' checked="checked"'?>> aktiv</div>
   <div><input type="checkbox" class="admCheck" name="drV" value="1"<?php if($aDru[2]=='1') echo ' checked="checked"'?>> versteckt</div>
   <div><input type="checkbox" class="admCheck" name="drK" value="1"<?php if($aDru[3]=='1') echo ' checked="checked"'?>> Kategorie</div>
   <div><input type="checkbox" class="admCheck" name="drF" value="1"<?php if($aDru[4]=='1') echo ' checked="checked"'?>> Frage</div>
   <div><input type="checkbox" class="admCheck" name="drL" value="1"<?php if($aDru[5]=='1') echo ' checked="checked"'?>> Lösung</div>
   <div><input type="checkbox" class="admCheck" name="drP" value="1"<?php if($aDru[6]=='1') echo ' checked="checked"'?>> Punkte</div>
   <div><input type="checkbox" class="admCheck" name="drG" value="1"<?php if(substr($aDru[7],0,1)=='1') echo ' checked="checked"'?>> Bild</div>
   <div>&nbsp;<input type="radio" class="admCheck" name="d_G" value="n"<?php if(substr($aDru[7],2,1)=='n') echo ' checked="checked"'?>> nur Bildname&nbsp; <input type="radio" class="admCheck" name="d_G" value="b"<?php if(substr($aDru[7],2,1)=='b') echo ' checked="checked"'?>> als Bild <span style="white-space:nowrap"><input type="text" style="width:3em" name="d_B" value="<?php echo substr($aDru[7],4)?>" /> px breit</span></div>
   <div><input type="checkbox" class="admCheck" name="drT" value="1"<?php if($aDru[8]=='1') echo ' checked="checked"'?>> Antwort</div>
   <div><input type="checkbox" class="admCheck" name="drB" value="1"<?php if($aDru[9]=='1') echo ' checked="checked"'?>> Bemerkung</div>
   <div style="float:left;"><input type="checkbox" class="admCheck" name="dr2" value="1"<?php if(isset($aDru[10])&&($aDru[10]=='1')) echo ' checked="checked"'?>> Bemerkung-2</div>
  </td>
  <td colspan="1" style="vertical-align:top;">
   <div><input type="checkbox" class="admCheck" name="drS" value="1"<?php if(isset($aDru[11])&&$aDru[11]=='1') echo ' checked="checked"'?>>Schablone <i>druckListe.htm</i> verwenden</div>
   <div>&nbsp;</div>
   <div><input type="radio" class="admCheck" name="drR" value="0"<?php if(!isset($aDru[12])||$aDru[12]!='1') echo ' checked="checked"'?>>Fragensortierung vorwärts</div>
   <div><input type="radio" class="admCheck" name="drR" value="1"<?php if(isset($aDru[12])&&$aDru[12]=='1') echo ' checked="checked"'?>>Fragensortierung rückwärts</div>
   <div>&nbsp;</div>
   <div><input type="radio" class="admCheck" name="drZ" value="0"<?php if(!isset($aDru[13])||$aDru[13]!='1') echo ' checked="checked"'?>>natürliche Antwortreihenfolge</div>
   <div><input type="radio" class="admCheck" name="drZ" value="1"<?php if(isset($aDru[13])&&$aDru[13]=='1') echo ' checked="checked"'?>>zufällige Antwortreihenfolge</div>
   <div>&nbsp;</div>
   <div>Überschrift:<br><input type="text" style="width:99%" name="drH" value="<?php echo(isset($aDru[14])?$aDru[14]:'')?>" /></div>
  </td>
  <td colspan="1" style="vertical-align:bottom;">
   <div style="float:right;padding-right:5px;padding-bottom:3px;"><input class="admSubmit" type="button" value="Einstellungen speichern" style="width:15em;" onclick="document.forms['fraEingabe'].action='druckSuche.php?aktion=speichern';document.forms['fraEingabe'].target='_self';document.forms['fraEingabe'].submit();" /></div>
  </td>
 </tr>
</table>
<div align="center">
<p class="admSubmit"><input class="admSubmit" type="submit" value="Druckliste"></p>
</div>
</form>

<?php echo fSeitenFuss();

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADF_'.$n)){
  $p=strpos($sWerte,'ADF_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADF_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>