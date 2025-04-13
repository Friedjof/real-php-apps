<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminexport','','Exp');

$nFelder=count($kal_FeldName); $nNtzFelder=count($kal_NutzerFelder);
if(defined('KAL_ExportFld')){$aF=explode(';',KAL_ExportFld); $nExpNtzFld1=KAL_ExpNtzFld1; $nExpNtzFld2=KAL_ExpNtzFld2; $sExpNtzTrn=KAL_ExpNtzTrn;}
else{$aF=array(); $nExpNtzFld1='0'; $nExpNtzFld2=''; $sExpNtzTrn='';}

for($i=59;$i>=0;$i--) if(file_exists(KAL_Pfad.'temp/termine_'.sprintf('%02d',$i).'.csv')) unlink(KAL_Pfad.'temp/termine_'.sprintf('%02d',$i).'.csv');
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sExportFld=''; $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 for($i=0;$i<$nFelder;$i++){
  if(txtVar('F'.$i)){$aF[$i]=true; $sExportFld.=';1';}else{$aF[$i]=false; $sExportFld.=';0';}
 }
 $sExportFld=substr($sExportFld,1); $nExpNtzFld1=txtVar('ENF1'); $nExpNtzFld2=txtVar('ENF2');
 $sExpNtzTrn=(isset($_POST['ENT'])?substr(str_replace(';',',',str_replace('  ',' ',$_POST['ENT'])),0,6):'');

 if(!defined('KAL_ExportFld')){
  if($p=strpos($sWerte,'$kal_NEingabeFeld=')) if($p=strpos($sWerte,"\n",$p+1)) $sWerte=substr_replace($sWerte,"\ndefine('KAL_ExportFld','".$sExportFld."');\ndefine('KAL_ExpNtzFld1','".$nExpNtzFld1."');\ndefine('KAL_ExpNtzFld2','".$nExpNtzFld2."');\ndefine('KAL_ExpNtzTrn','".$sExpNtzTrn."');",$p,0);
  $bNeu=true;
 }else{
  if(fSetzKalWert($sExportFld,'ExportFld',"'")) $bNeu=true;   if(fSetzKalWert($sExpNtzTrn,'ExpNtzTrn',"'")) $bNeu=true;
  if(fSetzKalWert($nExpNtzFld1,'ExpNtzFld1',"'")) $bNeu=true; if(fSetzKalWert($nExpNtzFld2,'ExpNtzFld2',"'")) $bNeu=true;
 }
 if($bNeu){
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }

 $sEx='Nummer'; for($i=1;$i<$nFelder;$i++) if($aF[$i]) $sEx.=';'.$kal_FeldName[$i]; $sEx.=NL; $aU=array(); $aU[0]=array_fill(0,32,'Admin'); $aU[0][0]=0;
 if(!KAL_SQL){ //Textdaten
  if(in_array('u',$kal_FeldType)){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD);
   for($j=1;$j<$nSaetze;$j++){ //ueber alle Nutzersaetze
    $a=explode(';',rtrim($aD[$j])); $a[3]=fKalDeCode($a[3]); $a[5]=fKalDeCode($a[5]); $aU[(int)$a[0]]=$a;
  }}
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $b=false;
  for($j=1;$j<$nSaetze;$j++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$j]));
   if($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'){ //online
    array_splice($a,1,1); $sEx.=$a[0];
    for($i=1;$i<$nFelder;$i++) if($aF[$i]){
     $s=(isset($a[$i])?str_replace('`,',',',$a[$i]):''); $t=$kal_FeldType[$i];
     switch($t){
      case 'd': if(strlen($s)>0) $s=fKalAnzeigeDatum($s); //Datum
       break;
      case '@': if(strlen($s)>0) $s=fKalAnzeigeDatum($s).(strlen($s)>10?', '.substr($s,11,5):''); //Eintragsdatum
       break;
      case 'w': $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); //Waehrung
       break;
      case 'e': case 'c': $s=fKalDeCode($s); //eMail und Kontakt
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,'');
       else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'u':
       $nN=(int)$s;
       if(strlen($s)&&isset($aU[$nN])){
        if($nExpNtzFld1) $s=$aU[$nN][$nExpNtzFld1];
        if(strlen($nExpNtzFld2)) $s.=(strlen($sExpNtzTrn)?$sExpNtzTrn:' ').$aU[$nN][$nExpNtzFld2];
       }break;
     }
     $sEx.=';'.$s;
    }$sEx.=NL;
  }}
 }elseif($DbO){ //SQL
  if(in_array('u',$kal_FeldType)){
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' ORDER BY nr')){
    while($a=$rR->fetch_row()) $aU[(int)$a[0]]=$a; $rR->close();
   }
  }
  $sF=''; $sS=''; for($i=1;$i<$nFelder;$i++){$sF.=',kal_'.$i; if($i<4) $sS.='kal_'.$i.',';}
  if($rR=$DbO->query('SELECT id'.$sF.' FROM '.KAL_SqlTabT.' WHERE online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').' ORDER BY '.$sS.'id')){
   while($a=$rR->fetch_row()){$sEx.=$a[0];
    for($i=1;$i<$nFelder;$i++) if($aF[$i]){
     $s=str_replace(';',',',$a[$i]); $t=$kal_FeldType[$i];
     switch($t){
      case 't': case 'm': case 'g': $s=str_replace("\r\n",'\n ',$s); //Memo
       break;
      case 'd': if(strlen($s)>0) $s=fKalAnzeigeDatum($s); //Datum
       break;
      case '@': if(strlen($s)>0) $s=fKalAnzeigeDatum($s).(strlen($s)>10?', '.substr($s,11,5):''); //Eintragsdatum
       break;
      case 'w': $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); //Waehrung
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,'');
       else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'u':
       $nN=(int)$s;
       if(strlen($s)&&isset($aU[$nN])){
        if($nExpNtzFld1) $s=$aU[$nN][$nExpNtzFld1];
        if(strlen($nExpNtzFld2)) $s.=(strlen($sExpNtzTrn)?$sExpNtzTrn:' ').$aU[$nN][$nExpNtzFld2];
       }break;
     }
     $sEx.=';'.$s;
    }$sEx.=NL;
   }$rR->close();
  }else $Msg.='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg.='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>'; //SQL
 $sExNa='termine_'.date('s').'.csv';
 if($f=fopen(KAL_Pfad.'temp/'.$sExNa,'w')){
  fwrite($f,$sEx); fclose($f);
  $Msg.='<p class="admErfo">Die Exportdatei liegt unter <a href="'.$sHttp.'temp/'.$sExNa.'" target="hilfe" onclick="hlpWin(this.href);return false;" style="font-style:italic;">'.$sExNa.'</a> zum Herunterladen bereit.</p>';
 }else $Msg.='<p class="admFehl">'.str_replace('#','<i>temp/'.$sExNa.'</i>',KAL_TxDateiRechte).'</p>';
}//POST

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Exportieren Sie die vorhandenen Termine in eine Datei im CSV-Format.</p>';
echo '<div style="text-align:center">'.$Msg.'</div>'.NL;
?>
<form action="export.php" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1" style="width:auto;margin-left:auto;margin-right:auto;margin-top:12px">
 <tr class="admTabl"><td>exportieren</td><td style="min-width:20em">Feld</td></tr>
<?php
 for($i=0;$i<$nFelder;$i++){
  $u='';
  if($kal_FeldType[$i]=='u'){
   for($j=0;$j<$nNtzFelder;$j++) if($j!=1&&$j!=4) $u.='<option value="'.$j.'">'.str_replace('`,',';',$kal_NutzerFelder[$j]).'</option>';
   $u=' <select name="ENF1" size="1">'.str_replace('value="'.$nExpNtzFld1.'"','value="'.$nExpNtzFld1.'" selected="selected"',$u).'</select> <input tyle="text" name="ENT" value="'.$sExpNtzTrn.'" size="1" style="width:1em" title="evt. Trennzeichen: Komma, Bindestrich, Leerzeichen usw." /> <select name="ENF2" size="1"><option value=""></option>'.str_replace('value="'.$nExpNtzFld2.'"','value="'.$nExpNtzFld2.'" selected="selected"',$u).'</select>';
  }
  echo "\n".' <tr class="admTabl"><td style="text-align:center"><input type="checkbox" name="F'.$i.'" value="1"'.(!isset($aF[$i])||$aF[$i]?' checked="checked"':'').' /></td><td>'.str_replace('`,',';',$kal_FeldName[$i]).$u.'</td></tr>';
 }
?>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Exportieren"></p>
</form>

<?php echo fSeitenFuss()?>