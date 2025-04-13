<?php
include 'hilfsFunktionen.php';  //Terminliste drucken
header('Content-Type: text/html; charset=ISO-8859-1');

$sKalHtmlVor=''; $sKalHtmlNach=''; $sKalHtmlNach=implode('',(file_exists('druckSeite.htm')?file('druckSeite.htm'):array())); $t='';
if($p=strpos($sKalHtmlNach,'{Inhalt}')){
 $sKalHtmlVor=substr($sKalHtmlNach,0,$p); $sKalHtmlNach=substr($sKalHtmlNach,$p+8); //Seitenkopf, Seitenfuss
 $sKalHtmlVor=str_replace('../grafik',$sHttp.'grafik',str_replace('{Titel}','Termin&uuml;bersicht',$sKalHtmlVor));
 $sKalHtmlNach=str_replace('../grafik',$sHttp.'grafik',$sKalHtmlNach);
}else{$sKalHtmlVor='<p style="color:#AA0033;">HTML-Layout-Schablone <i>druckSeite.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}

echo $sKalHtmlVor."\n";

$bOK=false; $bLsch=false; $bJKop=false;
$nFelder=count($kal_FeldName); if(KAL_NListeAnders) $kal_ListenFeld=$kal_NListenFeld; $sLschNun=''; $sJKopNun='';

$aD=array(); $aSpalten=array(); $nSpalten=0; $aQ=array(); $sQ=''; $nDatFeld2=0; $bOhneGrenze=false; //Abfrageparameter aufbereiten
for($i=0;$i<$nFelder;$i++){ //Abfrageparameter aufbereiten
 $t=$kal_FeldType[$i]; $aSpalten[$kal_ListenFeld[$i]]=$i;
 $s=(isset($_POST['kal_'.$i.'F1'])?$_POST['kal_'.$i.'F1']:(isset($_GET['kal_'.$i.'F1'])?$_GET['kal_'.$i.'F1']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F1='.urlencode($s); $aQ[$i.'F1']=$s; if($i<=1) $bOhneGrenze=true;
  if($t!='d'&&$t!='@') $a1Filt[$i]=$s; else $a1Filt[$i]=fKalNormDatum($s);
 }
 $s=(isset($_POST['kal_'.$i.'F2'])?$_POST['kal_'.$i.'F2']:(isset($_GET['kal_'.$i.'F2'])?$_GET['kal_'.$i.'F2']:''));
 if(strlen($s)){
  $sQ.='&amp;kal_'.$i.'F2='.urlencode($s); $aQ[$i.'F2']=$s; if($t!='d'&&$t!='@') $a2Filt[$i]=$s; else{$a2Filt[$i]=fKalNormDatum($s); if($i==1) $bOhneGrenze=true;}
  if($t=='d'||$t=='@'||$t=='w'||$t=='n'||$t=='1'||$t=='2'||$t=='3'||$t=='r'||$t=='i'){if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='0';}
  elseif($t=='j'||$t=='v') if(!isset($a1Filt[$i])||empty($a1Filt[$i])) $a1Filt[$i]='';
 }
 $s=(isset($_POST['kal_'.$i.'F3'])?$_POST['kal_'.$i.'F3']:(isset($_GET['kal_'.$i.'F3'])?$_GET['kal_'.$i.'F3']:''));
 if(strlen($s)){$a3Filt[$i]=$s; $sQ.='&amp;kal_'.$i.'F3='.urlencode($s); $aQ[$i.'F3']=$s;}
 if($t=='d'&&$i>1&&$nDatFeld2==0&&KAL_EndeDatum) $nDatFeld2=$i; //2.Datum
}
$sIntervallAnfang=date('Y-m-d',time()-86400*KAL_ZeigeAltesNochTage); $sIntervallEnde='99';
if(isset($_GET['kal_Archiv'])&&$_GET['kal_Archiv']||isset($_POST['kal_Archiv'])&&$_POST['kal_Archiv']){$bArchiv=true; $sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';} else $bArchiv=false;
if($bOhneGrenze){$sIntervallAnfang='00'; $sIntervallEnde='99'; $bArchiv=false;}

if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $bZeigeOnl=(isset($_GET['kal_Onl'])?(bool)$_GET['kal_Onl']:ADM_ZeigeOnline);
 $bZeigeOfl=(isset($_GET['kal_Ofl'])?(bool)$_GET['kal_Ofl']:ADM_ZeigeOffline);
 $bZeigeVmk=(isset($_GET['kal_Vmk'])?(bool)$_GET['kal_Vmk']:ADM_ZeigeVormerk);
}
if(!($bZeigeOfl||$bZeigeVmk)) $bZeigeOnl=true;
if($bZeigeOnl!=ADM_ZeigeOnline) $sQ.='&amp;kal_Onl='.($bZeigeOnl?'1':'0');
if($bZeigeOfl!=ADM_ZeigeOffline) $sQ.='&amp;kal_Ofl='.($bZeigeOfl?'1':'0');
if($bZeigeVmk!=ADM_ZeigeVormerk) $sQ.='&amp;kal_Vmk='.($bZeigeVmk?'1':'0');

$aSpalten[0]=0; $nSpalten=count($aSpalten); $aTmp=array(); $aIdx=array(); //Daten bereitstellen
if(!KAL_SQL){ //Textdaten
 $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
 for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
  $a=explode(';',rtrim($aD[$i])); $sId=(int)$a[0]; $sSta=$a[1];
  $b=($sSta=='1'&&$bZeigeOnl||$sSta=='3'&&KAL_AendernLoeschArt==3&&$bZeigeOnl||$sSta=='0'&&$bZeigeOfl||$sSta=='2'&&$bZeigeVmk); array_splice($a,1,1);
  $sAnfangDat=substr($a[1],0,10); $sEndeDat=$sAnfangDat;
  if(KAL_EndeDatum&&$nDatFeld2>0) if(!$sEndeDat=substr($a[$nDatFeld2],0,10)) $sEndeDat=$sAnfangDat;
  $b=$b&&(ADM_ZeigeAltes||(KAL_EndeDatum?$sEndeDat:$sAnfangDat)>=$sIntervallAnfang); //kommend oder laufend
  if($b&&$bArchiv) if($sAnfangDat>$sIntervallEnde) $b=false; //Archivfilter
  if($b&&isset($a1Filt)&&is_array($a1Filt)){
   reset($a1Filt);
   foreach($a1Filt as $j=>$v) if($b){ //Suchfiltern 1-2
    $t=$kal_FeldType[$j]; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); //$v Suchwort1, $w Suchwort2
    if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
     if(strlen($w)){if(stristr(str_replace('`,',';',$a[$j]),$w)) $b2=true; else $b2=false;} else $b2=false;
     if(!(stristr(str_replace('`,',';',$a[$j]),$v)||$b2)) $b=false;
    }elseif($t=='d'){ //Datum
     $s=substr($a[$j],0,10); //$s Datensatzdatum
     if($j==1&&KAL_EndeDatum){ //Termindatum
      if(!$sEndeDatum=substr($a[$nDatFeld2],0,10)) $sEndeDatum=$s;
      if(empty($w)){if($s>$v||$sEndeDatum<$v) $b=false;} elseif($s>$w||$sEndeDatum<$v) $b=false;
     }else{if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;} //sonstiges Datum
    }elseif($t=='@'){ //EintragsDatum
     $s=substr($a[$j],0,10); if(empty($w)){if($s!=$v) $b=false;} elseif($s<$v||$s>$w) $b=false;
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
   $aTmp[$sId]=array($sId);
   if(ADM_ListenIndex==1) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
   elseif(ADM_ListenIndex>1){ //andere Sortierung
    $s=strtoupper(strip_tags($a[ADM_ListenIndex])); $t=$kal_FeldType[ADM_ListenIndex];
    for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
     if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
    if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
    elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
    $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
   }
   elseif(ADM_ListenIndex==0) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$sId); //nach Nr
   for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace('\n ',NL,str_replace('`,',';',$a[$aSpalten[$j]]));
   $aTmp[$sId][]=$sSta;
  }
 }$aD=array();
}elseif($DbO){ //SQL
 if($sIntervallAnfang>'00'&&!ADM_ZeigeAltes){
  if($nDatFeld2==0||!KAL_EndeDatum) $s=' AND kal_1>"'.$sIntervallAnfang.'"';
  else $s=' AND(kal_'.$nDatFeld2.'>"'.$sIntervallAnfang.'" OR kal_1>"'.$sIntervallAnfang.'")';
 }elseif($bArchiv) $s=' AND kal_1<="'.$sIntervallEnde.'~"'; else $s='';
 if(isset($a1Filt)&&is_array($a1Filt)) foreach($a1Filt as $j=>$v){ //Suchfiltern 1-2
  $s.=' AND(kal_'.$j; $w=(isset($a2Filt[$j])?$a2Filt[$j]:''); $t=($kal_FeldType[$j]); //$v Suchwort1, $w Suchwort2
  if($t=='t'||$t=='m'||$t=='g'||$t=='a'||$t=='k'||$t=='s'||$t=='l'||$t=='e'||$t=='b'||$t=='f'||$t=='c'||$t=='u'||$t=='x'){
   $s.=' LIKE "%'.$v.'%"'; if(strlen($w)) $s.=' OR kal_'.$j.' LIKE "%'.$w.'%"';
  }elseif($t=='d'){
   if($j==1&&KAL_EndeDatum){ //Termindatum
    if(empty($w)){$s.='<"'.$v.'~" AND kal_'.($nDatFeld2==0?1:$nDatFeld2).'>"'.$v.'" OR kal_'.$j.' LIKE "'.$v.'%"';} // nur 1 Wert
    else{$s.=' BETWEEN "'.$v.'" AND "'.$w.'~" OR kal_'.($nDatFeld2==0?1:$nDatFeld2).' BETWEEN "'.$v.'" AND "'.$w.'~"';}
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
 $t=''; $nListenIdx=0; $i=0; $s=str_replace('kal_0','id',$s);
 for($j=1;$j<$nSpalten;$j++){$t.=',kal_'.$aSpalten[$j]; if($aSpalten[$j]==ADM_ListenIndex) $nListenIdx=$j;}
 if($nListenIdx==0&&ADM_ListenIndex>0){$t.=',kal_'.ADM_ListenIndex; $nListenIdx=$j;}
 $o=''; if($bZeigeOnl) $o.=' OR online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"'); if($bZeigeOfl) $o.=' OR online="0"'; if($bZeigeVmk) $o.=' OR online="2"';
 $o=substr($o,4); $i=substr_count($o,'OR'); if($i==1) $o='('.$o.')'; elseif($i==2) $o='online>""';
 if($rR=$DbO->query('SELECT id'.$t.',online FROM '.KAL_SqlTabT.' WHERE '.$o.$s.' ORDER BY kal_1'.($nFelder>1?',kal_2'.($nFelder>2?',kal_3':''):'').',id')){
  while($a=$rR->fetch_row()){
   $sId=(int)$a[0]; $aTmp[$sId]=array($sId); $i++;
   if($nListenIdx==1) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$i); //nach Datum
   elseif($nListenIdx>1){ //andere Sortierung
    $s=strtoupper(strip_tags($a[$nListenIdx])); $t=$kal_FeldType[ADM_ListenIndex];
    for($j=strlen($s)-1;$j>=0;$j--) //BB-Code weg
     if(substr($s,$j,1)=='[') if($v=strpos($s,']',$j)) $s=substr_replace($s,'',$j,++$v-$j);
    if($t=='w') $s=sprintf('%09.2f',1+$s); elseif($t=='n') $s=sprintf('%07d',1+$s);
    elseif($t=='1'||$t=='2'||$t=='3'||$t=='r') $s=sprintf('%010.3f',1+$s);
    $aIdx[$sId]=(strlen($s)>0?$s:' ').chr(255).sprintf('%0'.KAL_NummerStellen.'d',$i);
   }
   elseif(ADM_ListenIndex==0) $aIdx[$sId]=sprintf('%0'.KAL_NummerStellen.'d',$sId); //nach Nr
   for($j=1;$j<$nSpalten;$j++) $aTmp[$sId][]=str_replace("\r",'',$a[$j]); $aTmp[$sId][]=$a[$nSpalten];
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';//SQL
if(ADM_ListenIndex!=1) asort($aIdx); //nach Feldern
elseif(ADM_Rueckwaerts&&!$bArchiv||ADM_ArchivRueckwaerts&&$bArchiv) arsort($aIdx);
reset($aIdx); foreach($aIdx as $i=>$xx) $aD[]=$aTmp[$i];

if(!$Msg){
 if(!$sQ) $Msg='<p class="admMeld">Gesamt-Termin'.($bArchiv?'archiv ':'liste ');
 else $Msg='<p class="admMeld">'.($bArchiv?'Archiv':'Termin').'abfrageergebnis ';
 if($bZeigeOnl) $Msg.='<img src="'.$sHttp.'grafik/punktGrn.gif" width="12" height="12" border="0" alt="online-Termine" title="online-Termine">';
 if($bZeigeOfl) $Msg.='<img src="'.$sHttp.'grafik/punktRot.gif" width="12" height="12" border="0" alt="offline-Termine" title="offline-Termine">';
 if($bZeigeVmk) $Msg.='<img src="'.$sHttp.'grafik/punktRtGn.gif" width="12" height="12" border="0" alt="Terminvorschläge" title="Terminvorschläge">';
 $Msg.='</p>';
}

//Scriptausgabe
echo $Msg;
echo NL.'<table class="druck" border="0" cellpadding="0" cellspacing="0">'.NL;

echo    '<tr class="druck">';
echo NL.' <td class="druck" align="center"><b>Nr.</b></td>'.NL.' <td class="druck">&nbsp;</td>';
for($j=1;$j<$nSpalten;$j++){
 if($aSpalten[$j]!=ADM_ListenIndex) $v=''; else{$v='&nbsp;*'; if((ADM_Rueckwaerts&&!$bArchiv||ADM_ArchivRueckwaerts&&$bArchiv)&&$aSpalten[$j]==1) $v='&nbsp;+';}
 $sFN=$kal_FeldName[$aSpalten[$j]];
 if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
 echo NL.' <td class="druck"><b>'.$sFN.$v.'</b></td>';
}
echo NL.'</tr>';

foreach($aD as $a){ //Datenzeilen ausgeben
 $sId=$a[0]; $sSta=$a[$nSpalten];
 echo NL.'<tr class="druck">';
 echo NL.' <td class="druck" align="right" valign="top">'.sprintf('%0'.KAL_NummerStellen.'d',$sId).'</td>';
 echo NL.' <td class="druck" align="center" valign="top"><img src="'.$sHttp.'grafik/punkt'.($sSta=='1'?'Grn':($sSta=='0'?'Rot':($sSta=='0'?'RtGn':'Glb'))).'.gif" width="12" height="12" border="0" title="'.($sSta=='1'?'online':($sSta=='0'?'offline':($sSta=='2'?'Terminvorschlag':'Löschen'))).'"></td>';
 for($j=1;$j<$nSpalten;$j++){
  $k=$aSpalten[$j]; $t=$kal_FeldType[$k]; $sStil='';
  if($s=$a[$j]){
   switch($t){
    case 't': case 'g': $s=fKalBB($s); break; // Text
    case 'm': if(ADM_ListenMemoLaenge==0) $s=fKalBB($s); else{$s=fKalBB(fKalKurzMemo($s,ADM_ListenMemoLaenge));} break; //Memo
    case 'a': case 'k': case 'o': case 'u': break; // so lassen
    case 'd': case '@': $w=trim(substr($s,11)); // Datum
     $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
     switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
      case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
      case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
     }
     $s=$s1.$v.$s2.$v.$s3;
     if($t=='d'){if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].'&nbsp;'.$s; else $s.='&nbsp;'.$kal_WochenTag[$w];}
     elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
     break;
    case 'z': $sStil.='text-align:center;'; break; // Uhrzeit
    case 'w': // Währung
     if($s>0||!KAL_PreisLeer){
      $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
      if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sStil.='text-align:right;';
     }else $s='&nbsp;';
     break;
    case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); // Ja/Nein
     if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein; $sStil.='text-align:center;';
     break;
    case 'n': case '1': case '2': case '3': case 'r': // Zahl
     if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sStil.='text-align:right;';
     break;
    case 'l':
     $aL=explode('||',$s); $s='';
     foreach($aL as $w){
      $aI=explode('|',$w); $w=$aI[0]; $v=(isset($aI[1])?$aI[1]:$w); $u=$v;
      if(ADM_LinkSymbol){$v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" />'; $sStil.='text-align:center;';}
      $s.=$v.(ADM_LinkSymbol?'  ':', ');
     }$s=substr($s,0,-2); break;
    case 'e': case 'c':
     if(!KAL_SQL) $s=fKalDeCode($s);
     if(ADM_LinkSymbol){
      $v='<img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.'">'; $sStil.='text-align:center;';
     }else $v=$s;
     $s='<a href="mailto:'.$s.'" target="_blank">'.$v.'</a>';
     break;
    case 's': $w=$s;
     if(ADM_SymbSymbol){
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" alt="'.$w.'" />'; $sStil.='text-align:center;';
     }
     break;
    case 'b':
     if(ADM_BildVorschau){
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); // Bild
      $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'/')+1).'" />'; $sStil.='text-align:center;';
     }else $s=fKalKurzName(substr($s,strpos($s,'|')+1));
     break;
    case 'f':
     if(ADM_DateiSymbol){
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); // Datei
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sStil.='text-align:center;';
      $v='<img src="'.$sHttp.'grafik/datei'.$v.'.gif" width="16" height="16" border="0" title="'.strtoupper($w).'-'.KAL_TxDatei.'" />';
     }else $v=fKalKurzName($s);
     $s='<a href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'">'.$v.'</a>';
     break;
    case 'x': break;
    case 'p': $s=str_repeat('*',strlen($s)/2); break;
   }
  }else $s='&nbsp;';
  if($sStil) $sStil=' style="'.$sStil.'"';
  echo NL.' <td class="druck" valign="top"'.$sStil.'>'.$s.'</td>';
 }
 echo NL.'</tr>';
}
echo NL.'</table>'.NL;
echo "\n".$sKalHtmlNach;

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalNormDatum($w){
 $nJ=2; $nM=1; $nT=0;
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $nJ=0; $nM=1; $nT=2; break; case 1: $t='.'; break;
  case 2: $t='/'; $nJ=2; $nM=0; $nT=1; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 $a=explode($t,str_replace('_','-',str_replace(':','.',str_replace(';','.',str_replace(',','.',$w)))));
 return sprintf('%04d-%02d-%02d',strlen($a[$nJ])<=2?$a[$nJ]+2000:$a[$nJ],$a[$nM],$a[$nT]);
}

//Text mit BB-Code einkuerzen
function fKalKurzMemo($s,$nL=80){
 if(strlen($s)>$nL){
  $v='#'.substr($s,0,$nL);
  if($p=strrpos($v,'[')){ //BB-Code enthalten
   if(strrpos($v,']')<$p) $v=substr($v,0,$p); //angeschnittenen Codes streichen
   $p=0; $aTg=array();
   while($p=strpos($v,'[',++$p)){ //Codes erkennen
    if($q=strpos($v,']',++$p)){$t=substr($v,$p,$q-$p); $aTg[]=(($q=strpos($t,'='))?substr($t,0,$q):$t);}
   }
   $n=count($aTg)-1;
   for($i=$n;$i>=0;$i--){ //Codes durchsuchen
    $s=$aTg[$i];
    if(substr($s,0,1)!='/'){
     $bFnd=false;
     for($j=$i;$j<=$n;$j++) if($aTg[$j]=='/'.$s){$aTg[$j]='#'; $bFnd=true; break;}
     if(!$bFnd) $v.='[/'.$s.']'; //fehlenden Code anhaengen
  }}}
  return substr($v,1).'....';
 }else return $s;
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