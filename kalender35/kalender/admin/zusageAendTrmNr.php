<?php
 include 'hilfsFunktionen.php';
 header('Content-Type: text/html; charset=ISO-8859-1');
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Kalender:: Termin-Nummer</title>
<link rel="stylesheet" type="text/css" href="<?php echo(file_exists('autoren.css')?'autoren':'admin')?>.css">
<script type="text/javascript">
function clicked(sNr){
 opener.document.ZusageForm.kal_F1.value=sNr;
 window.close();
 return false;
}
</script>
</head>

<body marginwidth="0" marginheight="0" style="margin:4px;margin-top:8px;">
<div id="seite">
<p class="admMeld"><img src="<?php echo $sHttp?>grafik/knopfX.gif" align="right" onclick="window.close()" width="16" height="16" border="0" title="Fenster schliessen" />Terminauswahl</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="8%" align="center">Nr.</td>
 <td width="12">&nbsp;</td>
 <td>Datum</td>
 <td>Termin</td>
</tr>

<?php
if($nNr=(isset($_GET['id'])?$_GET['id']:0)){
 $nDatF=(KAL_TerminDatumFeld>1?KAL_TerminDatumFeld+1:2);
 $bTimF=KAL_TerminZeitFeld>1; $nTimF=KAL_TerminZeitFeld+1;
 $nVstF=(KAL_TerminVeranstFeld>1?KAL_TerminVeranstFeld+1:2);
 $sRefDat=date('Y-m-d',time()-250000);
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aD[$i]));
   if($a[2]>=$sRefDat){
    $sNr=sprintf('%0'.KAL_NummerStellen.'d',$a[0]);
    if((int)$a[0]!=$nNr){$t1=''; $t2='';}else{$t1='<div class="admFehl">'; $t2='</div>';}
    echo NL.'<tr class="admTabl">';
    echo NL.' <td align="right">'.$t1.$sNr.$t2.'</td>';
    echo NL.' <td><input type="image" onclick="clicked('."'".$sNr."'".')" src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Termin-Nummer '.$sNr.' eintragen" /></td>';
    echo NL.' <td>'.$t1.fKalAnzeigeDatum($a[$nDatF]).($bTimF?'&nbsp;'.$a[$nTimF]:'').$t2.'</td>';
    echo NL.' <td>'.substr($a[$nVstF],0,33).'</td>';
    echo NL.'</tr>';
  }}
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE kal_1>="'.$sRefDat.'" ORDER BY kal_1,kal_2,id')){
   while($a=$rR->fetch_row()){
    $sNr=sprintf('%0'.KAL_NummerStellen.'d',$a[0]);
    if((int)$a[0]!=$nNr){$t1=''; $t2='';}else{$t1='<div class="admFehl">'; $t2='</div>';}
    echo NL.'<tr class="admTabl">';
    echo NL.' <td align="right">'.$t1.$sNr.$t2.'</td>';
    echo NL.' <td><input type="image" onclick="clicked('."'".$sNr."'".')" src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Termin-Nummer '.$sNr.' eintragen" /></td>';
    echo NL.' <td>'.$t1.fKalAnzeigeDatum($a[$nDatF]).($bTimF?'&nbsp;'.$a[$nTimF]:'').$t2.'</td>';
    echo NL.' <td>'.substr($a[$nVstF],0,33).'</td>';
    echo NL.'</tr>';
   }$rR->close();
  }else echo '<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }
}else echo '<p class="admFehl">ungültiger Aufruf ohne Terminnummer</p>';

?>
</table>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
<div id="fuss">&copy; <a href="https://www.kalender-script.de">Kalender-Script</a></div>
</div>
</body>
</html>