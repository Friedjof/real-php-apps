<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Vorgabewerte anpassen','','KTs');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $nFelder=count($kal_FeldName); $aV=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); $ksSymbolTyp=KAL_SymbolTyp;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $nFelder=count($kal_FeldName); $aV=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben);
 if($v=(isset($_POST['typ'])?$_POST['typ']:'')){ // Symboltyp umstellen
  if(fSetzKalWert($v,'SymbolTyp',"'")){
   if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    $Msg='<p class="admErfo">Der Symboltyp wurde geändert.</p>';
   }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
  }
 }else{// Vorgabewerte
  $ksSymbolTyp=KAL_SymbolTyp;
  if($Id=(isset($_POST['id'])?$_POST['id']:'')){
   $aAlt=explode(';',trim($aV[$Id]));
   $sKat=str_replace('"',"'",str_replace(';','',str_replace("\n\n",NL,str_replace("\r",'',stripslashes(@strip_tags(trim($_POST['kat'])))))));
   if($kal_FeldType[$Id]=='m') $sKat=str_replace("\n",'\n ',$sKat);
   $aKat=explode(NL,NL.$sKat); $aKat[0]=$aAlt[0]; $nKat=min(count($aKat),18277);
   if($aKat!=$aAlt){
    $s=$aKat[0]; $sZhl='A'; $sZl1=''; $sZl2=''; $a=array();
    for($i=1;$i<$nKat;$i++){
     $s.=';'.$aKat[$i]; $a[$aKat[$i]]=$sZl2.$sZl1.$sZhl;
     if(++$sZhl>'Z'||strlen($sZhl)>1){
      $sZhl='A'; if($sZl1>''){if(++$sZl1>'Z'||strlen($sZl1)>1){$sZl1='A'; if($sZl2>'') $sZl2++; else $sZl2='A';}}else $sZl1='A';
    }}
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){
     $aV[$Id]=$s; for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aV[$i])?rtrim($aV[$i]):'').NL); fclose($f);
     $Msg='<p class="admErfo">Die geänderten Vorgabewerte wurden eingetragen.</p>'; $t=$kal_FeldType[$Id];
     if($t=='k') fSetzArrayV($a,'Kategorien','');
     if($t=='s') fSetzArrayV($a,'Symbole','');
     if($t=='k'||$t=='s') if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
     }else $Msg.='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
    }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> durfte nicht geschrieben werden!</p>';
   }else $Msg='<p class="admMeld">Die Vorgabewerte bleiben unverändert.</p>';
  }else $Msg='<p class="admFehl">fehlerhafter Seitenaufruf ohne gültige Feldnummer!</p>';
 }
}

//Seitenausgabe
if($Id=(isset($_GET['id'])?$_GET['id']:(isset($_POST['id'])?$_POST['id']:''))){
 $t=$kal_FeldType[$Id];
 if(!$Msg) $Msg='<p class="admMeld">Legen Sie '.($t=='a'||$t=='k'||$t=='s'?'die Vorgabewerte':'den Vorgabewert').' für das '.($t=='a'?'Auswahl':($t=='k'?'Kategorie':($t=='s'?'Symbol':($t=='j'||$t=='v'?'Ja/Nein':($t=='t'?'Text':($t=='m'?'Memo':'???')))))).'-Feld <i>'.$kal_FeldName[$Id].'</i> fest.</p>';
}else $Msg='<p class="admFehl">fehlerhafter Seitenaufruf ohne gültige Feldnummer!</p>';
echo $Msg.NL;
?>

<form name="konfKat" action="konfVorgaben.php" method="post">
<input type="hidden" name="id" value="<?php echo $Id?>" />
<?php
 $aKat=explode(';',(isset($aV[$Id])?trim($aV[$Id]):'')); $nKat=count($aKat); $sKnr=''; $sKat=''; $sZhl='A'; $sZl1=''; $sZl2=''; $sHid='';
 for($i=1;$i<$nKat;$i++){
  $sKnr.=$sZl2.$sZl1.$sZhl.NL; $sKat.=$aKat[$i].NL;
  if(++$sZhl>'Z'||strlen($sZhl)>1){
   $sZhl='A'; if($sZl1>''){if(++$sZl1>'Z'||strlen($sZl1)>1){$sZl1='A'; if($sZl2>'') $sZl2++; else $sZl2='A';}}else $sZl1='A';
 }}
 $nHei=max(round(1.2*min($nKat+5,35)),15);
 if($t=='j'||$t=='v'||$t=='t'||$t=='m'){ // Sonderbehandlung fuer Text, Memo, Ja/Nein
  if($p=strpos(rtrim($sKat),"\n")) $sKat=substr($sKat,0,$p); $nHei=1.6; $sHid='display:none'; $sKnr='';
  if($t=='m'){$sKat=str_replace('\n ',"\n",$sKat); $nHei=9;}
 }
?>
<table class="admTabl" border="0" cellpadding="5" cellspacing="1">
<tr class="admTabl"><td align="center"><b>Nr.</b></td><td><b>Vorgabewert</b> <span class="admMini">(aktuell <?php echo max($nKat-1,0)?> Einträge)</span></td></tr>
<tr class="admTabl">
 <td valign="top">
  <textarea class="admEing" name="knr" cols="4" rows="10" style="width:60px;height:<?php echo $nHei?>em;<?php echo $sHid?>" readonly="readonly"><?php echo rtrim($sKnr)?></textarea>
 </td>
 <td width="96%" valign="top">
  <textarea class="admEing" name="kat" cols="50" rows="10" style="height:<?php echo $nHei?>em;"><?php echo rtrim($sKat)?></textarea><?php if($t=='j'||$t=='v') echo '<div class="admMini">&nbsp;<i>Ja</i> oder <i>Nein</i> oder leer lassen.</div>'?>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>
<p class="admSubmit">[ <a href="konfTermine.php">zur Terminstruktur</a> ]</p>


<?php
if($t=='k'){ //bei Kategorie
?>

<div class="admBox">Es können maximal 675 Kategorien von A...Z, AA...AZ, BA...BZ, CA...ZZ verwendet werden.
Beachten Sie, dass zu jeder von Ihnen hier vereinbarten Kategorie A...ZZ auch eine korrespondierende CSS-Klasse
mit dem Namen <i>div.kalKatX</i> (wobei X für das Kategorienkürzel steht) in der CSS-Datei <i>kalStyles.css</i> definiert sein muss,
damit die farbige Unterscheidung der Kategorien funktioniert.</div>

<?php
}elseif($t=='s'){ //bei Symbol
 $aS=array();
 if($f=opendir(KAL_Pfad.'/grafik')){
  while($s=readdir($f)) if(substr($s,0,6)=='symbol') $aS[]=$s;
  closedir($f); sort($aS); $nKat=count($aS);
 }
?>

<p style="margin-top:32px;">Folgende Symbole sind momentan im Ordner <i>/grafik</i> vorrätig:</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td><b>Dateiname</b></td><td><b>Symbol</b></td></tr>
<?php
 for($i=0;$i<$nKat;$i++){
  $s=$aS[$i]; $aI=@getimagesize(KAL_Pfad.'grafik/'.$s);
  echo NL.'<tr class="admTabl"><td>'.$s.'</td><td align="center"><img src="'.$sHttp.'grafik/'.$s.'" '.$aI[3].' alt="" border="0" /></td></tr>';
 }
?>

</table>
<form name="konfTyp" action="konfVorgaben.php" method="post">
<input type="hidden" name="id" value="<?php echo $Id?>" />
<p>Momentan werden nur Symbole mit der Endung <select name="typ" class="admEing" style="width:50px;" onchange="document.forms['konfTyp'].submit()"><option value="jpg"<?php if($ksSymbolTyp=='jpg') echo 'selected=" selected"'?>>.jpg</option><option value="gif"<?php if($ksSymbolTyp=='gif') echo 'selected=" selected"'?>>.gif</option><option value="png"<?php if($ksSymbolTyp=='png') echo 'selected=" selected"'?>>.png</option></select> verwendet.</p>
</form>
<div class="admBox">Andere oder weitere Symbole müssen bei Bedarf manuell im Ordner <i>/grafik</i> abgelegt werden.
Es können maximal 675 Symbole von A...Z, AA...AZ, BA...BZ, CA...ZZ verwendet werden.
Beachten Sie beim Anlegen eigener Symbole die Großschreibweise der Kennbuchstaben A...ZZ im Dateinamen.</div>

<?php
}
echo fSeitenFuss();

function fSetzArrayV($a,$n,$t){
 global $sWerte;
 $p=strpos($sWerte,'$kal_'.$n.'='); $e=strpos($sWerte,');',$p); $p=strpos($sWerte,'array(',$p); $s='';
 if($p>0&&$e>$p){
  reset($a); foreach($a as $k=>$v) $s.=',"'.$k.'"=>'."'".$v."'"; $s=substr($s,1);
  if(substr($sWerte,$p+6,$e-($p+6))!=$s){
   $sWerte=substr_replace($sWerte,'array('.$s,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>