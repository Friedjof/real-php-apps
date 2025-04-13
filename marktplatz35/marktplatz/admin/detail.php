<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inseratedetail','<script type="text/javascript">
 function infWin(sURL){iWin=window.open(sURL,"infos","width=700,height=530,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");iWin.focus();}
</script>
<link rel="stylesheet" type="text/css" href="'.MPPFAD.'mpStyles.css" />'.(MP_GMapSource=='O'?"\n".'<link rel="stylesheet" type="text/css" href="'.MPPFAD.'maps/leaflet.css" />':''),'IIl');

$aStru=array();
if($nSegNo!=0){ //Segment gewählt

 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aDF=array(); $aND=array();
 $aZS=array(); $aPF=array(); $aAW=array(); $aKW=array(); $aSW=array();
 if(!MP_SQL){//Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
   $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
   if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

 $sQ=''; $sGMap=''; $bGefunden=false; $aW=array(); $nPassend=0; $nPos=0; $nVorg=0; $nNachf=0;
 $a1Filt=array(); $a2Filt=array(); $a3Filt=array(); $bOhneGrenze=false;

 if($_SERVER['REQUEST_METHOD']!='POST'){//GET
  $sId=(isset($_GET['mp_Num'])?$_GET['mp_Num']:'');
  $bZeigeOnl=(isset($_GET['mp_Onl'])?(bool)$_GET['mp_Onl']:AM_ZeigeOnline);
  $bZeigeOfl=(isset($_GET['mp_Ofl'])?(bool)$_GET['mp_Ofl']:AM_ZeigeOffline);
  $bZeigeVmk=(isset($_GET['mp_Vmk'])?(bool)$_GET['mp_Vmk']:AM_ZeigeVormerk);
  if(isset($_GET['mp_Archiv'])&&$_GET['mp_Archiv']) $bArchiv=true;
 }else{//POST
  $sId=(isset($_POST['mp_Num'])?$_POST['mp_Num']:'');
  $bZeigeOnl=(isset($_POST['mp_Onl'])?(bool)$_POST['mp_Onl']:false);
  $bZeigeOfl=(isset($_POST['mp_Ofl'])?(bool)$_POST['mp_Ofl']:false);
  $bZeigeVmk=(isset($_POST['mp_Vmk'])?(bool)$_POST['mp_Vmk']:false);
 }
 if(!($bZeigeOfl||$bZeigeVmk)) $bZeigeOnl=true;
 if($bZeigeOnl!=AM_ZeigeOnline) $sQ.='&amp;mp_Onl='.($bZeigeOnl?'1':'0');
 if($bZeigeOfl!=AM_ZeigeOffline) $sQ.='&amp;mp_Ofl='.($bZeigeOfl?'1':'0');
 if($bZeigeVmk!=AM_ZeigeVormerk) $sQ.='&amp;mp_Vmk='.($bZeigeVmk?'1':'0');

 for($i=0;$i<$nFelder;$i++){ //Abfrageparameter aufbereiten
  $t=$aFT[$i];
  $s=(isset($_GET['mp_'.$i.'F1'])?$_GET['mp_'.$i.'F1']:(isset($_POST['mp_'.$i.'F1'])?$_POST['mp_'.$i.'F1']:''));
  if(strlen($s)){$sQ.='&mp_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s; if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else $a1Filt[$i]=fMpNormDatum($s); if($i<=1) $bOhneGrenze=true;}
  $s=(isset($_GET['mp_'.$i.'F2'])?$_GET['mp_'.$i.'F2']:(isset($_POST['mp_'.$i.'F2'])?$_POST['mp_'.$i.'F2']:''));
  if(strlen($s)){
   $sQ.='&mp_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else $a2Filt[$i]=fMpNormDatum($s); if($i==1) $bOhneGrenze=true;
   if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){if(empty($a1Filt[$i])) $a1Filt[$i]='0';}
   elseif($t=='j'||$t=='v') if(empty($a1Filt[$i])) $a1Filt[$i]='';
  }
  $s=(isset($_GET['mp_'.$i.'F3'])?$_GET['mp_'.$i.'F3']:(isset($_POST['mp_'.$i.'F3'])?$_POST['mp_'.$i.'F3']:''));
  if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&mp_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
 }
 if($i=(isset($_GET['mp_Seite'])?$_GET['mp_Seite']:(isset($_POST['mp_Seite'])?$_POST['mp_Seite']:''))) $sQ.='&mp_Seite='.$i;
 $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='99';  $bVmk=false;
 if(isset($_GET['mp_Archiv'])||(isset($_POST['mp_Archiv'])&&$_POST['mp_Archiv'])){
  $bArchiv=true; $sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';
 }else $bArchiv=false;
 if($bOhneGrenze){$sIntervallAnfang='00'; $sIntervallEnde='99'; $bArchiv=false;}

 if(!MP_SQL){ //Textdaten bereitstellen
  $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){ //über alle Datensätze
   $a=explode(';',rtrim($aD[$i])); $sAblaufDat=$a[2]; $bVmr=($a[1]=='2');
   $b=($a[1]=='1'&&$bZeigeOnl||$a[1]=='0'&&$bZeigeOfl||$bVmr&&$bZeigeVmk); array_splice($a,1,1);
   $b=$b&&(AM_ZeigeAltes||$sAblaufDat>=$sIntervallAnfang); //laufendes Inserat
   if($b&&$bArchiv) if($sAblaufDat>$sIntervallEnde) $b=false; //Archivfilter
   if(is_array($a1Filt)){
    reset($a1Filt);
    foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
     $t=$aFT[$j]; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
     if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
      if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
      if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
     }elseif($t=='d'){ //Datum
      if(empty($w)){if($a[$j]!=$v) $b=false;} elseif($a[$j]<$v||$a[$j]>$w) $b=false;
     }elseif($t=='@'){ //EintragsDatum
      $s=substr($a[$j],0,10);
      if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
     }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
      $v=floatval(str_replace(',','.',$v)); $w=floatval(str_replace(',','.',$w));
      $s=floatval(str_replace(',','.',$a[$j]));
      if($w<=0){if($s!=$v) $b=false;} else{if($s<$v||$s>$w) $b=false;}
     }elseif($t=='o'){
      if($k=strlen($w)){if(substr($a[$j],0,$k)==$w) $b2=true; else $b2=false;} else $b2=false;
      if(!(substr($a[$j],0,strlen($v))==$v||$b2)) $b=false;
     }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1){$w=$a[$j]; if(($v=='J'&&$w!='J')||($v=='N'&&$w=='J')) $b=false;}}
    }
   }
   if($b&&is_array($a3Filt)){ //Suchfiltern 3
    reset($a3Filt); foreach($a3Filt as $j=>$v)
     if($aFT[$j]!='o'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}}
     else{if(substr($a[$j],0,strlen($v))==$v){$b=false; break;}}
   }
   if($b){ //Datensatz gültig
    $nId=(int)$a[0]; $nPassend++;
    if(!$bGefunden){
     $nPos++;
     if($nId!=$sId) $nVorg=$nId;
     else{$bGefunden=true; $bVmk=$bVmr; for($j=0;$j<$nFelder;$j++) $aW[]=str_replace('\n ',NL,str_replace('`,',';',$a[$j]));}
    }elseif($nNachf==0) $nNachf=$nId;
   }
  }//alle Datensätze
 }else{ //SQL
  if($sIntervallAnfang>'00'&&!AM_ZeigeAltes) $s=' AND mp_1>"'.$sIntervallAnfang.'"';
  elseif($bArchiv) $s=' AND mp_1<"'.$sIntervallEnde.'"'; else $s='';
  if(is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
   $s.=' AND(mp_'.$j; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); $t=($aFT[$j]); //$v Suchwort1, $w Suchwort2
   if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
    $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR mp_'.$j.' LIKE "%'.$w.'%"';
   }elseif($t=='d'||$t=='@'){
    if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
   }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
    $v=str_replace(',','.',$v);
    if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
   }elseif($t=='o'){
    $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR mp_'.$j.' LIKE "'.$w.'%"';
   }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
   $s.=')';
  }
  if(is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
   $t=$aFT[$j];
   if($t=='t'||$t=='m'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
    $s.=' AND NOT(mp_'.$j.' LIKE "%'.$v.'%")';
   elseif($t=='o') $s.=' AND NOT(mp_'.$j.' LIKE "'.$v.'%")';
  }
  $o=''; if($bZeigeOnl) $o.=' OR online="1"'; if($bZeigeOfl) $o.=' OR online="0"'; if($bZeigeVmk) $o.=' OR online="2"';
  $o=substr($o,4); $i=substr_count($o,'OR'); if($i==1) $o='('.$o.')'; elseif($i==2) $o='online>""';
  if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE '.$o.$s.' ORDER BY mp_1'.($nFelder>1?',mp_2'.($nFelder>2?',mp_3':''):'').',nr')){
   while($a=$rR->fetch_row()){
    $nId=(int)$a[0]; $nPassend++;
    if(!$bGefunden){
     $nPos++;
     if($nId!=$sId) $nVorg=$nId; else $bGefunden=true;
    }elseif($nNachf==0) $nNachf=$nId;
   }$rR->close();
  }
  if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'"')){
   $aW=$rR->fetch_row(); $rR->close(); $bVmk=($aW[1]=='2'); array_splice($aW,1,1);
  }else $Meld=MP_TxSqlFrage;
 }//SQL
 if($nVorg==0) $nVorg=$sId; if($nNachf==0) $nNachf=$sId;
 if(AM_Rueckwaerts&&!$bArchiv||AM_ArchivRueckwaerts&&$bArchiv){$j=$nVorg; $nVorg=$nNachf; $nNachf=$j;}

 if(!$Meld){$Meld=MP_TxDetail; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;
?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" style="margin-top:10px;">
<tr>
 <td width="30" align="center"><a href="<?php echo 'detail.php?seg='.$nSegNo.'&mp_Num='.$nVorg.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">&lt;&lt;</a></td>
 <td align="center"><?php echo MP_TxInserat.' '.$nPos.'/'.$nPassend?></td>
 <td width="30" align="center"><a href="<?php echo 'detail.php?seg='.$nSegNo.'&mp_Num='.$nNachf.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">&gt;&gt;</a></td>
</tr>
</table>

<form name="mpDetail" action="detail.php?seg=<?php echo $nSegNo?>" method="post">
<input type="hidden" name="mp_Num" value="<?php echo $sId?>" />
<input type="hidden" name="mp_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 for($i=0;$i<$nFelder;$i++) if(substr($aFN[$i],0,5)!='META-'&&$aFN[$i]!='TITLE'){
  if(AM_DetailInfo==$i){
   echo NL.' <tr class="admTabl">';
   echo NL.'  <td width="10%">Info senden</td>';
   echo NL.'  <td><a href="infoSend.php?seg='.$nSegNo.'&mp_Num='.$sId.'" target="infos" onclick="infWin('."'".'infoSend.php?seg='.$nSegNo.'&mp_Num='.$sId."'".');return false;"><img src="'.MPPFAD.'grafik/iconInfo.gif" width="16" height="16" border="0" style="margin-right:4px;" title="Info senden" />Information versenden</a></td>';
   echo NL.' </tr>';
  }
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td style="width:10%;white-space:nowrap;vertical-align:top;padding-top:5px;">'.$aFN[$i]; // Feldname
  if($aPF[$i]) echo '*'; echo '</td>';
  $t=$aFT[$i];
  if($s=trim($aW[$i])){ // Feldinhalt
   switch($t){
    case 't': case 'm': $s=fMpBB($s); break; // Text/Memo
    case 'a': case 'k': case 'o': case 'u': break; // so lassen
    case 'd': case '@': $w=substr($s,10); // Datum
     $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
     switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
      case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
      case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
     }
     $s=$s1.$v.$s2.$v.$s3; if($t=='@') $s.=$w; break;
    case 'z': $s.=' '.MP_TxUhr; break; // Uhrzeit
    case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); break;
    case 'w': // Währung
     if($s>0||!MP_PreisLeer){
      $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
      if(MP_Waehrung) $s.=' '.MP_Waehrung;
     }else $s='&nbsp;';
     break;
    case 'j': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
     if($s=='J') $s=MP_TxJa; elseif($s=='N') $s=MP_TxNein;
     break;
    case 'n': case '1': case '2': case '3': case 'r': // Zahl
     if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s);
     break;
    case 'l':
     $aI=explode('|',$s); $s=$aI[0]; $v=(isset($aI[1])?$aI[1]:$s);
     $v='<img src="'.MPPFAD.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" width="16" height="16" border="0" title="'.$v.'">';
     $s='<a href="'.(strpos($s,'@')?'mailto:':(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://')).$s.'" target="_blank">'.$v.'</a>&nbsp;<a href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMpExtLink($s)).'" target="_blank">'.(isset($aI[1])?$aI[1]:$s).'</a>';
     break;
    case 'e':
     if(!MP_SQL) $s=fMpDeCode($s);
     $s='<a href="mailto:'.$s.'" target="_blank"><img src="'.MPPFAD.'grafik/mail.gif" width="16" height="16" border="0" title="'.$s.'"></a>&nbsp;<a href="mailto:'.$s.'" target="_blank">'.$s.'</a>';
     break;
    case 'c':
     if(!MP_SQL) $s=fMpDeCode($s);
     if(file_exists('eingabeKontakt.php')) $s='<a href="eingabeKontakt.php?seg='.$nSegNo.'&mp_Num='.$aW[0].$sQ.'"><img src="'.MPPFAD.'grafik/iconAendern.gif" width="12" height="13" border="0" title="'.$s.'"> '.$s.'</a>';
     break;
    case 's': $w=$s;
     $p=array_search($s,$aSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
     $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; $aI=@getimagesize(MP_Pfad.$s);
     $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' align="middle" border="0" alt="'.$w.'" />&nbsp;'.$w;
     break;
    case 'b':
     $s=substr($s,strpos($s,'|')+1); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'_'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); // Bild
     $s='<img src="'.MPPFAD.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.substr($s,strpos($s,'/')+1).'" />';
     break;
    case 'f':
     $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); // Datei
     if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
     $v='<img src="'.MPPFAD.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.MP_TxDatei.'" />';
     $s='<a href="'.MPPFAD.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'">'.$v.'</a> <a href="'.MPPFAD.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'">'.$sId.'~'.$s.'</a>';
     break;
    case 'x': $aI=explode(',',$s); //StreetMap
     if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
      $s='<div id="GGeo'.$i.'" style="width:'.MP_GMapBreit.'px;height:'.MP_GMapHoch.'px;">'.MP_TxGMap1Warten.'<br /><a class="admText" href="javascript:showMap'.$i.'()" title="'.$aFN[$i].'">'.MP_TxGMap2Warten.'</a></div>';
      $sGMap.=(MP_GMapSource=='O'?fMpOMap($i,$aI):fMpGMap($i,$aI));
     }else $s='&nbsp;';
     break;
    case 'p': $s='<span title="'. fMpDeCode($s).'">'.str_repeat('*',strlen($s)/2).'</span>'; break;
   }
  }else $s='&nbsp;';
  if($w=$aZS[$i]) $w=' style="'.$w.'"';
  echo NL.'  <td'.$w.'>'.$s.'</td>';
  echo NL.' </tr>';
 }
 if(AM_DetailInfo==$nFelder){
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td width="10%">Info senden</td>';
  echo NL.'  <td><a href="infoSend.php?seg='.$nSegNo.'&mp_Num='.$sId.'" target="infos" onclick="infWin('."'".'infoSend.php?seg='.$nSegNo.'&mp_Num='.$sId."'".');return false;"><img src="'.$sHttp.'grafik/mail.gif" width="16" height="16" border="0" style="margin-right:4px;" title="Info senden" />Information versenden</a></td>';
  echo NL.' </tr>';
 }
?>

</table>
</form>
<table width="100%" border="0" cellpadding="2" cellspacing="1" style="margin-top:10px;">
<tr>
 <td width="30" align="center"><a href="<?php echo 'detail.php?seg='.$nSegNo.'&mp_Num='.$nVorg.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">&lt;&lt;</a></td>
 <td align="center">[ <a href="<?php echo (!$bVmk?'liste':'freigabe').'.php?seg='.$nSegNo.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">zurück zur Liste</a> ]</td>
 <td width="30" align="center"><a href="<?php echo 'detail.php?seg='.$nSegNo.'&mp_Num='.$nNachf.$sQ.($bArchiv?'&mp_Archiv=1':'')?>">&gt;&gt;</a></td>
</tr>
</table>

<?php
 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(MP_GMapSource=='O') echo '<script type="text/javascript" src="'.MPPFAD.'maps/leaflet.js"></script>';
  else echo '<script type="text/javascript" src="'.MP_GMapURL.'"></script>';
  echo "\n".'<script type="text/javascript">'.$sGMap."\n".'</script>'."\n";
 }
}else echo '<p class="admMeld">Im leeren Muster-Segment gibt es keine Inserate. Bitte wählen Sie zuerst ein Segment.</p>';

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aDF,$aND,$aZS,$aPF,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]=substr($aDF[0],14,1);
 $aND=explode(';',rtrim($aStru[8])); $aND[0]=substr($aND[0],14,1);
 $aZS=explode(';',rtrim($aStru[6])); $aZS[0]='';
 $aPF=explode(';',rtrim($aStru[13])); $aPF[0]='1'; $aPF[1]='1';
 $aAW=explode(';',rtrim($aStru[16])); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}

function fMpNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fMpOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');
  var mbAttr=\'Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>\';
  var mbUrl=\'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='.MP_SMapCode.'\';
  var sat=L.tileLayer(mbUrl,{id:\'mapbox/satellite-v9\',tileSize:512,zoomOffset:-1,attribution:mbAttr});
  var osm=L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\',{attribution:\'&copy OpenStreetMap\',maxZoom:19});
  var bDrag=true; if('.(MP_SMap2Finger?'true':'false').') bDrag=!L.Browser.mobile;
  var map'.$n.'=L.map(\'GGeo'.$n.'\',{center:['.sprintf('%.15f,%.15f',$a[0],$a[1]).'],zoom:'.$a[4].(MP_SMap2Finger?',dragging:!L.Browser.mobile,tap:!L.Browser.mobile':'').',scrollWheelZoom:false,layers:[osm]});
  if('.(MP_SMapTypeControl?'true':'false').'){var baseLayers={\'Karte\':osm,\'Satellit\':sat}; var layerControl=L.control.layers(baseLayers).addTo(map'.$n.');}
  var marker=L.marker(['.sprintf('%.15f,%.15f',$a[2],$a[3]).'],{opacity:0.75'.(MP_TxGMapOrt>''?",title:'".MP_TxGMapOrt."'":'').'}).addTo(map'.$n.');
  var mapCenter=map'.$n.'.getCenter(); var nF=Math.pow(2,'.$a[4].'); mapCenter.lng+=153.6/nF; mapCenter.lat-=64/nF;
  var tooltip=L.tooltip().setLatLng(mapCenter).setContent(\'Verschieben der Karte mit 2 Fingern!\').addTo(map'.$n.'); if(bDrag) map'.$n.'.closeTooltip(tooltip);
  function onMapAction(e){map'.$n.'.closeTooltip(tooltip);}
  map'.$n.'.on(\'click\',onMapAction); map'.$n.'.on(\'zoomstart\',onMapAction); map'.$n.'.on(\'movestart\',onMapAction);
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,MP_GMapWarten)+$n).');';
}

function fMpGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(MP_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(MP_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".MP_TxGMapOrt."'".'});':'
  if(GBrowserIsCompatible()){
   map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
   map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
   map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
   map'.$n.'.addControl(new GSmallMapControl());'.(MP_GMapTypeControl?'
   map'.$n.'.addControl(new GMapTypeControl());':'').'
  }').'
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,MP_GMapWarten)+$n).');';
}

function fMpExtLink($s){
 if(!defined('MP_ZeichnsExtLink')||MP_ZeichnsExtLink==0) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode($s)));
 elseif(MP_ZeichnsExtLink==1) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s))));
 elseif(MP_ZeichnsExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fMpBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.$o.'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="mpText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="mpText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="mpText" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="mpText"><li class="mpText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="mpText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }return $v;
}
?>
