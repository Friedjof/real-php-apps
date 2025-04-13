<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Detailanzeige','<script type="text/javascript">
 function InfWin(){infWin=window.open("about:blank","infos","width=700,height=530,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");infWin.focus();}
</script>
<link rel="stylesheet" type="text/css" href="'.KALPFAD.'kalStyles.css">'.(KAL_GMapSource=='O'?"\n".'<link rel="stylesheet" type="text/css" href="'.KALPFAD.'maps/leaflet.css" />':''),'TTl');

$nFelder=count($kal_FeldName);
$aD=array(); $aQ=array(); $sQ=''; $nEndeFeld=1; $bArchiv=false; $sSta='1';
$sId=(isset($_POST['kal_Num'])?$_POST['kal_Num']:(isset($_GET['kal_Num'])?$_GET['kal_Num']:''));
if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $sId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'');
 $bZeigeOnl=(isset($_GET['kal_Onl'])?(bool)$_GET['kal_Onl']:ADM_ZeigeOnline);
 $bZeigeOfl=(isset($_GET['kal_Ofl'])?(bool)$_GET['kal_Ofl']:ADM_ZeigeOffline);
 $bZeigeVmk=(isset($_GET['kal_Vmk'])?(bool)$_GET['kal_Vmk']:ADM_ZeigeVormerk);
 if(isset($_GET['kal_Archiv'])&&$_GET['kal_Archiv']) $bArchiv=true;
}else{//POST
 $sId=(isset($_POST['kal_Num'])?$_POST['kal_Num']:'');
 $bZeigeOnl=(isset($_POST['kal_Onl'])?(bool)$_POST['kal_Onl']:false);
 $bZeigeOfl=(isset($_POST['kal_Ofl'])?(bool)$_POST['kal_Ofl']:false);
 $bZeigeVmk=(isset($_POST['kal_Vmk'])?(bool)$_POST['kal_Vmk']:false);
}
if(!($bZeigeOfl||$bZeigeVmk)) $bZeigeOnl=true;
if($bZeigeOnl!=ADM_ZeigeOnline) $sQ.='&amp;kal_Onl='.($bZeigeOnl?'1':'0');
if($bZeigeOfl!=ADM_ZeigeOffline) $sQ.='&amp;kal_Ofl='.($bZeigeOfl?'1':'0');
if($bZeigeVmk!=ADM_ZeigeVormerk) $sQ.='&amp;kal_Vmk='.($bZeigeVmk?'1':'0');


for($i=0;$i<$nFelder;$i++){ //Abfrageparameter aufbereiten
 $t=$kal_FeldType[$i];
 $s=(isset($_POST['kal_'.$i.'F1'])?$_POST['kal_'.$i.'F1']:(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:''));
 if(strlen($s)){$sQ.='&amp;kal_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s; if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else $a1Filt[$i]=fKalNormDatum($s);}
 $s=(isset($_POST['kal_'.$i.'F2'])?$_POST['kal_'.$i.'F2']:(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else $a2Filt[$i]=fKalNormDatum($s);
  if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='0';}
  elseif($t=='j'||$t=='v') if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='';
 }
 $s=(isset($_POST['kal_'.$i.'F3'])?$_POST['kal_'.$i.'F3']:(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:''));
 if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;kal_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
 if($t=='d'&&$nEndeFeld==1&&KAL_EndeDatum) $nEndeFeld=$i; //2.Datum
}
if($i=(isset($_POST['kal_Start'])?$_POST['kal_Start']:(isset($_GET['kal_Start'])?$_GET['kal_Start']:''))) $sQ.='&amp;kal_Start='.$i;

$aW=array(); $nPassend=0; $nPos=0; $nVorg=0; $nNachf=0; $bGefunden=false; $bVmk=false; $bLsch=false; //Daten bereitstellen
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensätze
  $a=explode(';',rtrim($aD[$i])); $bVmr=($a[1]=='2'); $bLschr=($a[1]=='3'); $sSt=$a[1];
  $b=($a[1]=='1'&&$bZeigeOnl||$a[1]=='3'&&KAL_AendernLoeschArt==3&&$bZeigeOnl||$a[1]=='0'&&$bZeigeOfl||$bVmr&&$bZeigeVmk); array_splice($a,1,1);
  if(isset($a1Filt)&&is_array($a1Filt)){
   reset($a1Filt);
   foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
    $t=$kal_FeldType[$j]; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
     if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
    }elseif($t=='d'){ //Datum
     $s=substr($a[$j],0,10); //$s Datensatzdatum
     if($j==1&&KAL_EndeDatum){ //Termindatum
      if(!$sEndeDatum=substr($a[$nEndeFeld],0,10)) $sEndeDatum=$s;
      if(empty($w)){if($s>$v||$sEndeDatum<$v) $b=false;} elseif($s>$w||$sEndeDatum<$v) $b=false;
     }else{if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;} //sonstiges Datum
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
  if($b&&isset($a3Filt)&&is_array($a3Filt)){ //Suchfiltern 3
   reset($a3Filt); foreach($a3Filt as $j=>$v)
    if($kal_FeldType[$j]!='o'){if(stristr(str_replace('`,',';',$a[$j]),$v)){$b=false; break;}}
    else{if(substr($a[$j],0,strlen($v))==$v){$b=false; break;}}
  }
  if($b){ //Datensatz gültig
   $nId=(int)$a[0]; $nPassend++;
   if(!$bGefunden){
    $nPos++;
    if($nId!=$sId) $nVorg=$nId;
    else{$bGefunden=true; $bVmk=$bVmr; $bLsch=$bLschr; $sSta=$sSt; for($j=0;$j<$nFelder;$j++) $aW[]=str_replace('\n ',NL,str_replace('`,',';',$a[$j]));}
   }elseif($nNachf==0) $nNachf=$nId;
  }
 }//alle Datensätze
}elseif($DbO){ //SQL
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  $s.=' AND(kal_'.$j; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); $t=($kal_FeldType[$j]); //$v Suchwort1, $w Suchwort2
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
   $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.$w.'%"';
  }elseif($t=='d'){
   if($j==1&&KAL_EndeDatum){ //Termindatum
    if(empty($w)){$s.='<"'.$v.'~" AND kal_'.$nEndeFeld.'>"'.$v.'" OR kal_'.$j.' LIKE "'.$v.'%"';} // nur 1 Wert
    else{$s.=' BETWEEN "'.$v.'" AND "'.$w.'~" OR kal_'.$nEndeFeld.' BETWEEN "'.$v.'" AND "'.$w.'~"';}
   }else{if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';} //sonstiges Datum
  }elseif($t=='@'){
   if(empty($w)) $s.=' LIKE "'.$v.'%"'; else $s.=' BETWEEN "'.$v.'" AND "'.$w.'~"';
  }elseif($t=='i'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='w'){
   $v=str_replace(',','.',$v);
   if(strlen($w)) $s.=' BETWEEN "'.$v.'" AND "'.str_replace(',','.',$w).'"'; else $s.='="'.$v.'"';
  }elseif($t=='o'){
   $s.=' LIKE "'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "'.$w.'%"';
  }elseif($t=='j'||$t=='v'){$v.=$w; if(strlen($v)==1) $s.=($v=='J'?'=':'<>').'"J"'; else $s.='<>"@"';}
  $s.=')';
 }
 if(isset($a3Filt)&&is_array($a3Filt)) foreach($a3Filt as $j=>$v){ //Suchfiltern 3
  $t=$kal_FeldType[$j];
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x')
   $s.=' AND NOT(kal_'.$j.' LIKE "%'.$v.'%")';
  elseif($t=='o') $s.=' AND NOT(kal_'.$j.' LIKE "'.$v.'%")';
 }
 $o=''; if($bZeigeOnl) $o.=' OR online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"'); if($bZeigeOfl) $o.=' OR online="0"'; if($bZeigeVmk) $o.=' OR online="2"';
 $o=substr($o,4); $i=substr_count($o,'OR'); if($i==1) $o='('.$o.')'; elseif($i==2) $o='online>""';
 if($rR=$DbO->query('SELECT id FROM '.KAL_SqlTabT.' WHERE '.$o.$s.' ORDER BY kal_1'.($nFelder>1?',kal_2'.($nFelder>2?',kal_3':''):'').',id')){
  while($a=$rR->fetch_row()){
   $nId=(int)$a[0]; $nPassend++;
   if(!$bGefunden){
    $nPos++;
    if($nId!=$sId) $nVorg=$nId; else $bGefunden=true;
   }elseif($nNachf==0) $nNachf=$nId;
  }$rR->close();
 }
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
  $aW=$rR->fetch_row(); $rR->close(); $sSta=$aW[1]; $bVmk=($aW[1]=='2'); $bLsch=($aW[1]=='3'); array_splice($aW,1,1);
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';//SQL
if($nVorg==0) $nVorg=$sId; if($nNachf==0) $nNachf=$sId; if(ADM_Rueckwaerts){$j=$nVorg; $nVorg=$nNachf; $nNachf=$j;}
if(!$Msg) $Msg='<p class="admMeld">'.KAL_TxDetail.'</p>';

//Scriptausgabe
echo $Msg.NL; $sGMap='';
?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" style="margin-top:10px;">
<tr>
 <td width="30" align="center"><a href="<?php echo 'detail.php?kal_Num='.$nVorg.$sQ.($bArchiv?'&amp;kal_Archiv=1':'')?>">&lt;&lt;</a></td>
 <td align="center"><?php echo KAL_TxTermin.' '.$nPos.'/'.$nPassend?></td>
 <td width="30" align="center"><a href="<?php echo 'detail.php?kal_Num='.$nNachf.$sQ.($bArchiv?'&amp;kal_Archiv=1':'')?>">&gt;&gt;</a></td>
</tr>
</table>

<form name="kalDetail" action="detail.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $sId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<?php
 echo NL.' <tr class="admTabl">';
 echo NL.'  <td>Status</td><td><img src="'.$sHttp.'grafik/punkt'.($sSta=='1'?'Grn':($sSta=='0'?'Rot':($sSta=='2'?'RtGn':'Glb'))).'.gif" width="12" height="12" border="0" title="'.($sSta=='1'?'online':($sSta=='0'?'offline':($sSta=='2'?'Terminvorschlag':'Löschen'))).'"> '.($sSta=='1'?'online':($sSta=='0'?'offline':($sSta=='2'?'Terminvorschlag':'Löschen'))).'</td>';
 echo NL.' </tr>';
 for($i=0;$i<$nFelder;$i++){
  if(ADM_DetailInfo==$i){
   echo NL.' <tr class="admTabl">';
   echo NL.'  <td width="10%">Info senden</td>';
   echo NL.'  <td><a href="infoSend.php?id='.$sId.'" target="infos" onclick="InfWin()"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" style="margin-right:4px;" title="Info senden" />Information versenden</a></td>';
   echo NL.' </tr>';
  }
  $sFN=$kal_FeldName[$i];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
  if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td width="10%" style="vertical-align:top;padding-top:5px;">'.$sFN; // Feldname
  if($kal_PflichtFeld[$i]) echo '*'; echo '</td>';
  $t=$kal_FeldType[$i];
  if($s=str_replace('`,',';',trim($aW[$i]))){ // Feldinhalt
   switch($t){
    case 't': case 'm': case 'g': $s=fKalBB($s); break; // Text/Memo
    case 'a': case 'k': case 'o': case 'u': break; // so lassen
    case 'd': case '@': $w=trim(substr($s,11)); // Datum
     $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
     switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
      case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
      case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
     }
     $s=$s1.$v.$s2.$v.$s3;
     if($t=='d'){if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].' '.$s; else $s.=' '.$kal_WochenTag[$w];}
     elseif($w) $s.=', '.$w;
     break;
    case 'z': $s.=' '.KAL_TxUhr; break; // Uhrzeit
    case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break;
    case 'w': // Währung
     if($s>0||!KAL_PreisLeer){
      $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
      if(KAL_Waehrung) $s.=' '.KAL_Waehrung;
     }else $s='&nbsp;';
     break;
    case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
     if($s=='J') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
     break;
    case 'n': case '1': case '2': case '3': case 'r': // Zahl
     if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
     break;
    case 'l':
     $aL=explode('||',$s); $s='';
     foreach($aL as $w){
      $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w);
      $v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" style="margin-right:4px;" title="'.$u.'" />';
      $s.='<a title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="_blank">'.$v.$u.'</a>, ';
     }$s=substr($s,0,-2); break;
    case 'e':
     if(!KAL_SQL) $s=fKalDeCode($s);
     $s='<a href="mailto:'.$s.'" target="_blank"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.'"></a>&nbsp;<a href="mailto:'.$s.'" target="_blank">'.$s.'</a>';
     break;
    case 'c':
     if(!KAL_SQL) $s=fKalDeCode($s);
     if(file_exists('eingabeKontakt.php')) $s='<a href="eingabeKontakt.php?kal_Num='.$aW[0].$sQ.'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="'.$s.'"> '.$s.'</a>';
     break;
    case 's': $w=$s;
     $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
     $s='<img src="'.$sHttp.$s.'" '.$aI[3].' align="middle" border="0" alt="'.$w.'" />&nbsp;'.$w;
     break;
    case 'b':
     $s=substr($s,strpos($s,'|')+1); $s=KAL_Bilder.$sId.'_'.$s; $aI=@getimagesize(KAL_Pfad.$s); // Bild
     $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,7).'" />';
     break;
    case 'f':
     $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); // Datei
     if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
     $v='<img src="'.$sHttp.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.KAL_TxDatei.'" />';
     $s='<a href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'">'.$v.'</a> <a href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'">'.$sId.'~'.$s.'</a>';
     break;
    case 'x': $aI=explode(',',$s); //StreetMap
     if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
      $s='<div id="GGeo'.$i.'" style="width:'.KAL_GMapBreit.'px;height:'.KAL_GMapHoch.'px;">'.KAL_TxGMap1Warten.'<br /><a class="kalText" href="javascript:showMap'.$i.'()" title="'.$kal_FeldName[$i].'">'.KAL_TxGMap2Warten.'</a></div>';
      $sGMap.=(KAL_GMapSource=='O'?fKalOMap($i,$aI):fKalGMap($i,$aI));
     }else $s='&nbsp;';
     break;
    case 'p': $s='<span title="'. fKalDeCode($s).'">'.str_repeat('*',strlen($s)/2).'</span>'; break;
   }
  }else $s='&nbsp;';
  if($w=$kal_ZeilenStil[$i]) $w=' style="'.$w.'"';
  echo NL.'  <td'.$w.'>'.$s.'</td>';
  echo NL.' </tr>';
 }
 if(ADM_DetailInfo>=$nFelder){
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td width="10%">Info senden</td>';
  echo NL.'  <td><a href="infoSend.php?id='.$sId.'" target="infos" onclick="InfWin()"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" style="margin-right:4px;" title="Info senden" />Information versenden</a></td>';
  echo NL.' </tr>';
 }
 if(KAL_DetailZusage>0&&KAL_ZusageSystem){
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td width="10%">'.KAL_TxZusageIcon.'</td>';
  echo NL.'  <td><a href="zusageEingabe.php?kal_Trm='.$sId.'"><img src="'.$sHttp.'grafik/icon_Zusagen.gif" align="top" width="12" height="13" border="0" title="Zusage neu eingeben" style="margin-right:4px;" />Zusage eintragen</a></td>';
  echo NL.' </tr>';
 }
?>

</table>
</form>
<table width="100%" border="0" cellpadding="2" cellspacing="1" style="margin-top:10px;">
<tr>
 <td width="30" align="center"><a href="<?php echo 'detail.php?kal_Num='.$nVorg.$sQ.($bArchiv?'&amp;kal_Archiv=1':'')?>">&lt;&lt;</a></td>
 <td align="center">[ <a href="<?php echo (!($bVmk||$bLsch)?'liste':(!$bLsch?'freigabe':'terminLoeschung')).'.php?'.substr($sQ.($bArchiv?'&amp;kal_Archiv=1':''),5)?>">zurück zur Liste</a> ]</td>
 <td width="30" align="center"><a href="<?php echo 'detail.php?kal_Num='.$nNachf.$sQ.($bArchiv?'&amp;kal_Archiv=1':'')?>">&gt;&gt;</a></td>
</tr>
</table>

<?php
 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(KAL_GMapSource=='O') echo '<script type="text/javascript" src="'.KALPFAD.'maps/leaflet.js"></script>';
  else echo '<script type="text/javascript" src="'.KAL_GMapURL.'"></script>';
  echo "\n".'<script type="text/javascript">'.$sGMap."\n".'</script>'."\n";
 }
echo fSeitenFuss();

function fKalNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

function fKalOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');
  var mbAttr=\'Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>\';
  var mbUrl=\'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='.KAL_SMapCode.'\';
  var sat=L.tileLayer(mbUrl,{id:\'mapbox/satellite-v9\',tileSize:512,zoomOffset:-1,attribution:mbAttr});
  var osm=L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\',{attribution:\'&copy OpenStreetMap\',maxZoom:19});
  var map'.$n.'=L.map(\'GGeo'.$n.'\',{center:['.sprintf('%.15f,%.15f',$a[0],$a[1]).'],zoom:'.$a[4].(KAL_SMap2Finger?',dragging:!L.Browser.mobile,tap:!L.Browser.mobile':'').',scrollWheelZoom:false,layers:[osm]});
  if('.(KAL_SMapTypeControl?'true':'false').'){var baseLayers={\'Karte\':osm,\'Satellit\':sat}; var layerControl=L.control.layers(baseLayers).addTo(map'.$n.');}
  var marker=L.marker(['.sprintf('%.15f,%.15f',$a[2],$a[3]).'],{opacity:0.75'.(KAL_TxGMapOrt>''?",title:'".KAL_TxGMapOrt."'":'').'}).addTo(map'.$n.');
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,KAL_GMapWarten)+$n).');';
}

function fKalGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(KAL_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(KAL_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".KAL_TxGMapOrt."'".'});':'
  if(GBrowserIsCompatible()){
   map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
   map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
   map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
   map'.$n.'.addControl(new GSmallMapControl());'.(KAL_GMapTypeControl?'
   map'.$n.'.addControl(new GMapTypeControl());':'').'
  }').'
 }
 var showTm'.$n.'=window.setInterval('."'".'showMap'.$n.'()'."'".','.(1000*max(1,KAL_GMapWarten)+$n).');';
}

//BB-Code zu HTML wandeln
function fKalBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" border="0" />',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="kalText"><li class="kalText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,6)=='<br />') $l=6; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br />',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="kalText">',$l,6); $n+=19; $l=strpos($v,'<br />',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}
?>