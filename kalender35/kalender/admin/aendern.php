<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Termin �ndern','<script type="text/javascript">
 function GeoWin(){geoWin=window.open("about:blank","geowin","width='.(min(max(KAL_GMapBreit,500),725)+50).',height=700,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");geoWin.focus();}
</script>
<script type="text/javascript" src="eingabe.js"></script>'.(ADM_TCalPicker?'
<link rel="stylesheet" type="text/css" href="'.$sHttp.'tcal.css" />
<script type="text/javascript" src="'.$sHttp.'tcal.js"></script>
<script type="text/javascript">
 A_TCALCONF.format='."'".fKalTCalFormat()."'".';
 A_TCALCONF.weekdays=['."'".implode("','",$kal_WochenTag)."'".'];
 A_TCALCONF.months=['."'".str_replace(';',"','",KAL_TxLMonate)."'".'];
 A_TCALCONF.prevmonth='."'".KAL_TxVorige.KAL_TxDeklMo.' '.KAL_TxMonat."'".';
 A_TCALCONF.nextmonth='."'".KAL_TxNaechste.KAL_TxDeklMo.' '.KAL_TxMonat."'".';
 A_TCALCONF.prevyear='."'".KAL_TxVorige.KAL_TxDeklJh.' '.KAL_TxJahr."'".';
 A_TCALCONF.nextyear='."'".KAL_TxNaechste.KAL_TxDeklJh.' '.KAL_TxJahr."'".';
 A_TCALCONF.yearscroll='.(KAL_TCalYrScroll?'true':'false').';
 A_TIMECONF.starttime='.sprintf('%.2f',KAL_TimeStart).';
 A_TIMECONF.stopptime='.sprintf('%.2f',KAL_TimeStopp).';
 A_TIMECONF.intervall='.sprintf('%.2f',KAL_TimeIvall).';
</script>':''),'TTl');

$nFelder=count($kal_FeldName);$bOK=false; $sFehl=''; $sZ=''; $sF=''; $sQ=''; $sOnl='0'; $sOnA='0'; $sODt=''; $sNDt='';
$aFehl=array(); $aW=array(); $aOh=array(); $aOa=array(); $aOs=array(); $bVmk=false; $bLsch=false; $bMitBild=false;

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET Daten holen
 $sQ=$_SERVER['QUERY_STRING']; $sId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'');
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $s=rtrim($aD[$i]); $p=strpos($s,';');
   if($sId==substr($s,0,$p)){
    $aW=explode(';',str_replace('\n ',NL,$s)); $sOnl=$aW[1]; $sOnA=$sOnl; array_splice($aW,1,1);
    break;
   }
  }
 }elseif($DbO){ //SQL-Daten
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
   $aW=$rR->fetch_row(); $rR->close(); $sOnl=$aW[1]; $sOnA=$sOnl; array_splice($aW,1,1);
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 for($i=1;$i<$nFelder;$i++){
  if($kal_FeldType[$i]=='d'){if($aW[$i]) $aW[$i]=fKalAnzeigeDatum($aW[$i]); if($i==1) $sODt=$aW[1];}
  elseif($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f'){$aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p);}
  elseif($kal_FeldType[$i]=='w'||$kal_FeldType[$i]=='n'||$kal_FeldType[$i]=='1'||$kal_FeldType[$i]=='2'||$kal_FeldType[$i]=='3'||$kal_FeldType[$i]=='r') $aW[$i]=str_replace('.',KAL_Dezimalzeichen,$aW[$i]);
  elseif(($kal_FeldType[$i]=='e'||$kal_FeldType[$i]=='c')&&!KAL_SQL) $aW[$i]=fKalDeCode($aW[$i]);
  elseif($kal_FeldType[$i]=='p') $aW[$i]=fKalDeCode($aW[$i]);
  elseif($kal_FeldType[$i]=='@'){if(KAL_EintragszeitNeu&&$kal_FeldName[$i]!='ZUSAGE_BIS') $aW[$i]=fKalAnzeigeDatum(date('Y-m-d')).' '.date('H:i'); elseif($aW[$i]) $aW[$i]=trim(fKalAnzeigeDatum($aW[$i]).strstr($aW[$i],' '));}
 }
 $bVmk=($sOnA=='2'); $bLsch=($sOnA=='3');
}else{ //POST Formularauswertung
 $sId=(isset($_POST['kal_Num'])?$_POST['kal_Num']:''); $sQ=(isset($_POST['kal_Qry'])?$_POST['kal_Qry']:'');
 $bUtf8=((isset($_POST['kal_JSSend'])||$_POST['kal_Utf8']=='1')?true:false);
 $sOnl=(isset($_POST['kal_Onl'])?$_POST['kal_Onl']:'');$sOnA=(isset($_POST['kal_OnA'])?$_POST['kal_OnA']:'');
 $bVmk=($sOnA=='2'); $bLsch=($sOnA=='3'); $sODt=(isset($_POST['kal_ODt'])?$_POST['kal_ODt']:'');
 // Eingaben holen
 for($i=1;$i<$nFelder;$i++) if($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f')
  {$aOh[$i]=(isset($_POST['kal_Oh'.$i])?$_POST['kal_Oh'.$i]:''); $aOa[$i]=(isset($_POST['kal_Oa'.$i])?$_POST['kal_Oa'.$i]:'');} // kal_Oh: hochgeladene; kal_Oa: alte;
 for($i=1;$i<$nFelder;$i++){
  $s=str_replace('~@~','\n ',stripslashes(@strip_tags(str_replace('\n ','~@~',str_replace("\r",'',trim($_POST['kal_F'.$i])))))); $t=$kal_FeldType[$i];
  if(strlen($s)>0||!$kal_PflichtFeld[$i]||$t=='b'||$t=='f'||$t=='@'){
   if($t!='m'&&$t!='g') $s=str_replace('"',"'",$s); if($bUtf8) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); $v=$s; // s:Eingabe, v:Speicherwert
   switch($t){
   case 't': if($kal_FeldName[$i]=='KAPAZITAET') $s=str_replace(' ','',str_replace(' ','',$s)); $v=$s; break;
   case 'm': case 'a': case 'k': case 's': case 'j': case '#': case 'v': case 'g': case 'u': case 'x': //Memo,Kategorie,Auswahl,Ja/Nein,Nutzer,StreetMap
    break;
   case 'd': //Datum
    if($s) if($v=fKalErzeugeDatum($s)){$s=fKalAnzeigeDatum($v); if($i==1) $sNDt=$s;}else $aFehl[$i]=true; break;
   case '@': //EintragsDatum
    if($kal_FeldName[$i]!='ZUSAGE_BIS'){
     if(KAL_EintragszeitNeu){$v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
     else{
      if($s){if($v=fKalErzeugeDatum($s)) $v=substr($v,0,10).strstr($s,' '); else $v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
      else{$v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).strstr($v,' ');}
    }}elseif($s){
     if($v=fKalErzeugeDatum($s)){
      if($p=strpos($s,' ')){
       $a=explode(':',str_replace('.',':',str_replace(',',':',trim(substr($s,$p)))));
       $u=sprintf(' %02d:%02d',(isset($a[0])?$a[0]:0),(isset($a[1])?$a[1]:0));
      }else $u='';
      $s=fKalAnzeigeDatum($v).$u; $v=substr($v,0,10).$u;
     }else $aFehl[$i]=true;
    }elseif($kal_PflichtFeld[$i]) $aFehl[$i]=true;
    break;
   case 'z': //Uhrzeit
    if($s){$a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',(isset($a[0])?$a[0]:0),(isset($a[1])?$a[1]:0)); $v=$s;} break;
   case 'e': case 'c': // E-Mail, Kontakt-E-Mail
    if($s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-z���_-]+(\.[0-9a-z���_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aFehl[$i]=true;
    if(!KAL_SQL) $v=fKalEnCode($s); break;
   case 'l': //Link oder E-Mail
    $v=$s; break;
   case 'b': //Bild
    if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i]; //kal_Up: neue Datei; kal_Dl: zu l�schen
    $UpNaJS=(isset($_POST['kal_UpNa_'.$i])?fKalDateiname(basename($_POST['kal_UpNa_'.$i])):'');
    $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):'');
    if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
    if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
     if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_BildMaxKByte)||KAL_BildMaxKByte<=0){
      if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['kal_Up'.$i]['tmp_name']);
      elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['kal_Up'.$i]['tmp_name']);
      elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['kal_Up'.$i]['tmp_name']);
      if(!empty($Src)){
       // Demo
       imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,KAL_TxBildOeffnen);}
     }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_BildMaxKByte,KAL_TxBildGroesse);}
    }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
     $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxBildTyp);
    }
    $aOs[$i]=$v; break;
   case 'f': //Datei
    if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i];
    $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
    if($UpEx&&$UpEx!='.php'&&$UpEx!='.php3'&&$UpEx!='.php5'&&$UpEx!='.pl'){
      // Demo
    }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
     $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxDateiTyp);
    }
    $aOs[$i]=$v; break;
   case 'w': //Waehrung
    $v=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),KAL_Dezimalstellen,'.','');
    $s=number_format((float)$v,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); break;
   case 'n': case '1': case '2': case '3': //Zahl
    $v=number_format((float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s)),(int)$t,'.','');
    $s=number_format((float)$v,(int)$t,KAL_Dezimalzeichen,''); break;
   case 'r': //Zahl
    $v=str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s));
    $s=str_replace('.',KAL_Dezimalzeichen,$v); break;
   case 'o': //PLZ
    if($s) if(strlen($s)!=KAL_PLZLaenge) $aFehl[$i]=true; break;
   case 'p': $v=fKalEnCode($s); break; //Passwort
   }$aW[$i]=$s;
   if(!KAL_SQL) $sZ.=';'.str_replace(NL,'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
   else $sZ.=',kal_'.$i.'="'.str_replace(NL,"\r\n",str_replace('\n ',NL,str_replace('"','\"',$v))).'"';
  }else{$aFehl[$i]=true; $aW[$i]=''; if(!KAL_SQL) $sZ.=';';}
 }
 if($aW[$nFelder]=(isset($_POST['kal_Per'])?$_POST['kal_Per']:'')) if(!KAL_SQL) $sZ.=';'.$aW[$nFelder];
 if($sFehl==''){ //alles OK, eintragen
  if(count($aFehl)==0){

   $Msg='<p class="admFehl">Demoversion: Der Termin bleibt wie er war!</p>';

  }else $Msg='<p class="admFehl">'.KAL_TxEingabeFehl.'</p>';
 }else $Msg='<p class="admFehl">'.$sFehl.'</p>';
}//POST
$aVg=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); //Hinweise und Kategorien holen
if(!$Msg) $Msg='<p class="admMeld">'.KAL_TxAendereMeld.'</p>';

//Scriptausgabe
echo $Msg.NL; $nBreit=12;
for($i=1;$i<$nFelder;$i++) $nBreit=max($nBreit,strlen($kal_FeldName[$i]));
if($nBreit>25) $nBreit=25; $nBreit=round(0.65*$nBreit,0);
?>

<form name="kalEingabe" action="aendern.php" onsubmit="return formSend()" enctype="multipart/form-data" method="post">
<input type="hidden" name="kal_Dmy" value="xx" />
<input type="hidden" name="kal_Num" value="<?php echo $sId?>" />
<input type="hidden" name="kal_ODt" value="<?php echo $sODt?>" />
<input type="hidden" name="kal_OnA" value="<?php echo $sOnA?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<script type="text/javascript">
 var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";
 if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";
 document.writeln('<input type="hidden" name="kal_Utf8" value="'+sUtf8+'" />');
</script>
<table class="admTabl" style="table-layout:fixed;width:100%" border="0" cellpadding="2" cellspacing="1">
 <tr class="admTabl">
  <td class="admSpa1" style="width:<?php echo $nBreit?>em">Status</td>
  <td>
   <?php if($sOnl!='2'){?><input class="admRadio" type="radio" name="kal_Onl" value="0"<?php if($sOnl<'1') echo ' checked="checked"'?> /> offline &nbsp;
   <input class="admRadio" type="radio" name="kal_Onl" value="1"<?php if($sOnl=='1') echo ' checked="checked"'?> /> online &nbsp; <?php if($sOnl=='3'){?><input class="admRadio" type="radio" name="kal_Onl" value="3"<?php if($sOnl=='3') echo ' checked="checked"'?> /> gel�scht &nbsp;<?php }}
   else{?><input class="admRadio" type="radio" name="kal_Onl" value="2"<?php if($sOnl=='2') echo ' checked="checked"'?> /> vorgemerkt<?php }?>

  </td>
 </tr>
<?php
 for($i=1;$i<$nFelder;$i++){
  $sFN=$kal_FeldName[$i]; $aHlp=explode(';',(isset($aVg[$i])?trim($aVg[$i]):'')); //Hilfetext und etwaige Vorgabewerte
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
  if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td class="admSpa1" style="width:'.$nBreit.'em"><div id="kalLabel'.$i.'">'.$sFN.($kal_PflichtFeld[$i]?'*':'').'</div></td>'; //Feldname
  echo NL.'  <td>'; $sZ=NL.'   <div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="admFehl"':'').'>';
  $t=$kal_FeldType[$i]; $v=str_replace('`,',';',$aW[$i]); //Feldinhalt
  switch($t){
  case 't': case 'e': case 'c': //Text, E-Mail, Kontakt
   if($t=='t') $v=str_replace(NL,'\n ',str_replace("\r",'',$v));
   echo $sZ.'<input style="width:99%" type="text" name="kal_F'.$i.'" value="'.$v.'" /></div>';
   break;
  case 'm': //Memo
   if(KAL_FormatCode) echo NL.'   <div title="'.KAL_TxBB_X.'">'.NL.fKalBBToolbar($i).NL; else echo NL.'   <div>';
   echo $sZ.'<textarea name="kal_F'.$i.'" style="width:99%" cols="80" rows="10">'.$v.'</textarea></div>'.NL.'   </div>';
   break;
  case 'a': case 'k': case 's': //Aufz�hlung/Kategorie
   reset($aHlp); $sO=''; foreach($aHlp as $w) $sO.='<option value="'.$w.'"'.($v==$w?' selected="selected"':'').'>'.$w.'</option>';
   echo $sZ.'<select name="kal_F'.$i.'" size="1"><option value="">---</option>'.substr($sO,strpos($sO,'<option',9)).'</select></div>';
   break;
  case 'd': //Datum
   echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="10" class="kalTCal" style="width:8em" /> <span class="admMini">'.KAL_TxFormat.' '.fKalDatumsFormat().'</span></div>';
   break;
  case '@': //EintragsDatum
   if($kal_FeldName[$i]!='ZUSAGE_BIS') echo $sZ.$v.'<input type="hidden" name="kal_F'.$i.'" value="'.$v.'" /></div>';
   else echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:12em" /> <span class="admMini">'.KAL_TxFormat.' '.fKalDatumsFormat().' '.KAL_TxOder.' '.fKalDatumsFormat().' '.KAL_TxSymbUhr.'</span></div>';
   break;
  case 'z': //Zeit
   echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="5" class="kalTime" style="width:8em" /> <span class="admMini">'.KAL_TxFormat.' '.KAL_TxSymbUhr.'</span></div>';
   break;
  case 'l': //Link
   echo $sZ.'<div title="Format:  Adresse  oder  Adresse|Linktext  oder  Adresse|Linktext|Target  oder  Adresse1|Linktext1|Target1||Adresse2|Linktext2|Target2"><input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="255" style="width:99%" /></div></div>';
   break;
  case 'j': case '#': case 'v': //Ja/Nein
   echo $sZ.'<input class="admRadio" type="radio" name="kal_F'.$i.'" value="J"'.($v!='J'?'':' checked="checked"').' /> '.KAL_TxJa.' &nbsp; <input class="admRadio" type="radio" name="kal_F'.$i.'" value="N"'.($v!='N'?'':' checked="checked"').' /> '.KAL_TxNein.' &nbsp; <input class="admRadio" type="radio" name="kal_F'.$i.'" value=""'.($v!=''?'':' checked="checked"').' /> '.KAL_TxJNLeer.'</div>';
   break;
  case 'w': //Waehrung
   echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:7em" /> '.KAL_Waehrung.'</div>';
   break;
  case 'n': case 'r': case '1': case '2': case '3': case 'o': //Zahlen
   echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:7em" />'.($t!='o'?'':' <span class="admMini">'.KAL_PLZLaenge.' '.KAL_TxStellen.'</span>').'</div>';
   break;
  case 'b': //Bild
   echo $sZ.'<input type="file" name="kal_Up'.$i.'" size="80" style="width:99%" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif" /><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>'; $bMitBild=true;
   if($v) echo NL.'   <div style="float:left;"><input class="admCheck" type="checkbox" name="kal_Dl'.$i.'" value="1" /><input type="hidden" name="kal_F'.$i.'" value="'.$v.'" /><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="admMini">'.$v.' '.KAL_TxLoeschen.'</span></div>';
   echo NL.'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="admMini">'.(KAL_BildMaxKByte>0?'(max. '.KAL_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': //Datei
   echo $sZ.'<input type="file" name="kal_Up'.$i.'" size="80" style="width:99%" /><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>';
   if($v) echo NL.'   <div style="float:left;"><input class="admCheck" type="checkbox" name="kal_Dl'.$i.'" value="1" /><input type="hidden" name="kal_F'.$i.'" value="'.$v.'" /><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="admMini">'.$v.' '.KAL_TxLoeschen.'</span></div>';
   echo NL.'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="admMini">(max. '.KAL_DateiMaxKByte.' KByte)</span></div>';
   break;
  case 'x': //StreetMap
   echo $sZ.'<div style="width:14px;float:left;"><a href="'.$sHttp.(KAL_GMapSource=='O'?'openstreet':'google').'map.php?'.$i.($v?','.$v:'').'" target="geowin" onclick="javascript:GeoWin();"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Koordinaten bearbeiten"></a></div>';
   echo '<div style="margin-left:15px"><input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="255" style="width:99%" /></div></div>';
   break;
  case 'g': //Gastkommentar
   if(KAL_FormatCode) echo NL.'   <div title="'.KAL_TxBB_X.'">'.NL.fKalBBToolbar($i).NL; else echo NL.'   <div>';
   echo $sZ.'<textarea name="kal_F'.$i.'" style="width:99%" cols="80" rows="10">'.$v.'</textarea></div>'.NL.'   </div>';
   break;
  case 'u': // Benutzername
   echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:12em" /> <span class="admMini">'.KAL_TxNutzerNr.'</span></div>';
   break;
  case 'p': // Passwort
   echo $sZ.'<input type="password" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:12em" /> <span class="admMini">'.KAL_TxPassRegel.'</span></div>';
   break;
  }
  if($v=$aHlp[0]) echo NL.'   <div><span class="admMini">'.str_replace('`,',';',$v).'</span></div>'; // Eingabehilfe
  echo NL.'  </td>'.NL.' </tr>';
 }
 if($sOnl=='2'){
  $v=(isset($aW[$nFelder])?$aW[$nFelder]:'');
  $a=explode('|',$v); $s=(isset($a[1])?$a[1]:''); if(strpos($s,'-')) $t=' bis '.fKalAnzeigeDatum($s); else $t=', noch '.((int)$s).' mal'; $s=$a[0];
  if($s=='A') $s='t�glich'; elseif($s=='B') $s='w�chentl.'; elseif($s=='C') $s='14-t�gig'; elseif($s=='D'||$s=='E') $s='monatlich'; elseif($s=='F') $s='j�hrlich';
  echo NL.' <tr class="admTabl"><td class="admSpa1">Periodik</td><td><input style="width:8em" name="kal_Per" value="'.$v.'" /> <span class="admMini">'.$s.$t.'</span></td></tr>';
 }
 //Pflichtfeldzeile
 echo NL.' <tr class="admTabl"><td class="admSpa1">&nbsp;</td><td class="admMini" style="text-align:right;">* <span class="admMini">'.KAL_TxPflicht.'</span>'.($sOnl!='2'?'<input type="hidden" name="kal_Per" value="'.(isset($aW[$nFelder])?$aW[$nFelder]:'').'" />':'').'</td></tr>';
?>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
if(file_exists(($sOnl<'2'?'liste':($sOnl=='2'?'freigabe':'terminLoeschung')).'.php')) echo '<p class="admSubmit">[ <a href="'.($sOnl<'2'?'liste':($sOnl=='2'?'freigabe':'terminLoeschung')).'.php?'.$sQ.'">zur�ck zur Liste</a> ]</p>'.NL.NL;

if($bMitBild && KAL_BildResize){
 echo "\n".'<script src="'.$sHttp.'kalEingabeBild.js" type="text/javascript"></script>';
 echo "\n".'<script type="text/javascript">';
 echo "\n".' sPostURL="aendern.php";';
 echo "\n".' nBildBreit='.KAL_BildBreit.'; nBildHoch='.KAL_BildHoch.';';
 echo "\n".' nThumbBreit='.KAL_ThumbBreit.'; nThumbHoch='.KAL_ThumbHoch.';';
 echo "\n".'</script>'."\n";
}else{
 echo "\n".'<script type="text/javascript">';
 echo "\n".' function formSend(){return true;} // normales Senden ohne Bilder;';
 echo "\n".' function loadDatFile(inputField){return false;}';
 echo "\n".' function loadImgFile(inputField){return false;}';
 echo "\n".'</script>'."\n";
}

echo fSeitenFuss();

function fKalDateiname($s){
 $s=str_replace('�','Ae',str_replace('�','Oe',str_replace('�','Ue',str_replace('�','ss',str_replace('�','ae',str_replace('�','oe',str_replace('�','ue',$s)))))));
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 return str_replace('�','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}

function fKalTCalFormat(){
 $s1='d'; $s2='m'; $s3='Y';
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3='d'; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2='d'; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}

function fKalPlainText($s,$t,$aN=array()){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||count($aN)<=0){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  case 'l': $aL=explode('||',$s); $s=''; foreach($aL as $w){$aI=explode('|',$w); $s.=$aI[0].', ';} $s=substr($s,0,-2); break;
  case 'u':
   if(KAL_NutzerBenachrFeld>0&&is_array($aN)){
    if($s>'0000'){$sN=$s; if(!$s=$aN[KAL_NutzerBenachrFeld]) $s=$sN;}else $s=KAL_TxAutor0000;
   }
   break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}
function fKalBBToolbar($Nr){
 $sHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 $X =NL.'<table class="admTool" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Bold',   0,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Italic', 2,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Uline',  4,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Center', 6,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Right',  8,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Enum',  10,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Number',12,$sHttp).'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Nr,'Link',  16,$sHttp).'</td>';
 $X.=NL.'  <td><img class="admTool" src="'.$sHttp.KAL_Www.'grafik/tbColor.gif" style="margin-right:0;cursor:default;" title="'.KAL_TxBB_O.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="kal_Col'.$Nr.'" onChange="fCol('.$Nr.',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.KAL_TxBB_O.'">
    <option value="">-</option>
    <option style="color:black" value="black">Abc9</option>
    <option style="color:red;" value="red">Abc9</option>
    <option style="color:violet;" value="violet">Abc9</option>
    <option style="color:brown;" value="brown">Abc9</option>
    <option style="color:yellow;" value="yellow">Abc9</option>
    <option style="color:green;" value="green">Abc9</option>
    <option style="color:lime;" value="lime">Abc9</option>
    <option style="color:olive;" value="olive">Abc9</option>
    <option style="color:cyan;" value="cyan">Abc9</option>
    <option style="color:blue;" value="blue">Abc9</option>
    <option style="color:navy;" value="navy">Abc9</option>
    <option style="color:gray;" value="gray">Abc9</option>
    <option style="color:silver;" value="silver">Abc9</option>
    <option style="color:white;background-color:#999999" value="white">Abc9</option>
   </select>
  </td>';
 $X.=NL.'  <td><img class="admTool" src="'.$sHttp.KAL_Www.'grafik/tbSize.gif" style="margin-right:0;cursor:default;" title="'.KAL_TxBB_S.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="kal_Siz'.$Nr.'" onChange="fSiz('.$Nr.',this.options[this.selectedIndex].value); this.selectedIndex=0;" title="'.KAL_TxBB_S.'">
    <option value="">-</option>
    <option value="+3">&nbsp;+3</option>
    <option value="+2">&nbsp;+2</option>
    <option value="+1">&nbsp;+1</option>
    <option value="-1">&nbsp;- 1</option>
    <option value="-2">&nbsp;- 2</option>
   </select>
  </td>';
 $X.=NL.' </tr>';
 $X.=NL.'</table>';
 return $X;
}
function fDrawToolBtn($Nr,$vImg,$nTag,$sHttp){
 return '<img class="admTool" src="'.$sHttp.KAL_Www.'grafik/tb'.$vImg.'.gif" onClick="fFmt('.$Nr.','.$nTag.')" style="background-image:url('.$sHttp.KAL_Www.'grafik/tool.gif);" title="'.constant('KAL_TxBB_'.substr($vImg,0,1)).'" />';
}
?>