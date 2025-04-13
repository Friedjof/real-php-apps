<?php
include 'hilfsFunktionen.php';
header('Content-Type: text/html; charset=ISO-8859-1');

$sKalHtmlVor=''; $sKalHtmlNach=''; $sKalHtmlNach=implode('',(file_exists('druckSeite.htm')?file('druckSeite.htm'):array())); $t='';
if($p=strpos($sKalHtmlNach,'{Inhalt}')){
 $sKalHtmlVor=substr($sKalHtmlNach,0,$p); $sKalHtmlNach=substr($sKalHtmlNach,$p+8); //Seitenkopf, Seitenfuss
 $sKalHtmlVor=str_replace('../grafik',$sHttp.'grafik',str_replace('{Titel}','Benutzer&uuml;bersicht',$sKalHtmlVor));
 $sKalHtmlNach=str_replace('../grafik',$sHttp.'grafik',$sKalHtmlNach);
}else{$sKalHtmlVor='<p style="color:#AA0033;">HTML-Layout-Schablone <i>druckSeite.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}

echo $sKalHtmlVor."\n";

//Daten bereitstellen
$bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
array_splice($kal_NutzerFelder,1,1); $amNutzerFelder=min(ADM_NutzerFelder,count($kal_NutzerFelder)-1); $aTmp=array();
if(!KAL_SQL){ //Textdaten
 $aTmp=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); array_shift($aTmp);
}elseif($DbO){ //SQL
  $s=''; for($j=5;$j<=$amNutzerFelder;$j++) $s.=',dat_'.$j;
  if($rR=$DbO->query('SELECT nr,aktiv,benutzer,passwort,email'.$s.' FROM '.KAL_SqlTabN.' ORDER BY nr')){
   while($a=$rR->fetch_row()){
    $s=$a[0]; for($j=1;$j<=$amNutzerFelder;$j++) $s.=';'.$a[$j]; $aTmp[]=$s;
   }$rR->close();
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
reset($aTmp); $nSaetze=count($aTmp); $aD=array(); foreach($aTmp as $i=>$xx) $aD[]=$aTmp[$i];
if(!$Msg){
 if($bNutzer) $Msg='<p class="admMeld">Benutzerliste</p>';
 else $Msg='<p class="admFehl">Die Benutzerverwaltung ist ohne ein Feld vom Typ <i>Benutzer</i> in der Terminstruktur momentan inaktiv!</p>';
}

//Scriptausgabe
echo $Msg;
?>

<table class="druck" border="0" cellpadding="0" cellspacing="0">
<?php //Kopfzeile
echo  '<tr class="druck">';
echo NL.' <td class="druck" align="center"><b>Nr.</b></td>'.NL.' <td class="druck">&nbsp;</td>'.NL.' <td class="druck"><b>'.$kal_NutzerFelder[2].'</b></td>';
for($j=4;$j<=$amNutzerFelder;$j++){if(!$s=$kal_NutzerFelder[$j]) $s='&nbsp;'; echo NL.' <td class="druck"><b>'.$s.'</b></td>';}
echo NL.'</tr>';
foreach($aD as $a){ //Datenzeilen ausgeben
 $a=explode(';',rtrim($a)); $Id=$a[0]; if(!KAL_SQL) array_splice($a,1,1);
 echo NL.'<tr class="druck">';
 echo NL.' <td class="druck">'.sprintf('%04d',$Id).'</td>';
 $sSta=$a[1];
 if($sSta=='1') $sSta='0"><img src="'.$sHttp.'grafik/punktGrn.gif" width="12" height="12" border="0" title="freigeschaltet">';
 elseif($sSta=='0') $sSta='1"><img src="'.$sHttp.'grafik/punktRot.gif" width="12" height="12" border="0" title="inaktiv">';
 elseif($sSta=='2') $sSta='1"><img src="'.$sHttp.'grafik/punktRtGn.gif" width="12" height="12" border="0" title="best&auml;tigt">';
 echo NL.' <td class="druck" align="center">'.substr($sSta,3).'</td>';
 if($s=$a[2]){if(!KAL_SQL) $s=fKalDeCode($s);}else $s='&nbsp;'; echo NL.' <td class="druck">'.$s.'</td>';
 if($s=$a[4]){if(!KAL_SQL) $s=fKalDeCode($s);}else $s='&nbsp;'; echo NL.' <td class="druck">'.$s.'</td>';
 for($j=5;$j<=$amNutzerFelder;$j++){if(!$s=(isset($a[$j])?$a[$j]:'')) $s='&nbsp;'; echo NL.' <td class="druck">'.(KAL_SQL?$s:str_replace('`,',';',$s)).'</td>';}
 echo NL.'</tr>';
}
echo "\n".'</table>'."\n";
echo "\n".$sKalHtmlNach;
?>