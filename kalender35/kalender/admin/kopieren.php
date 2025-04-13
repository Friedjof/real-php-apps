<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Termin kopieren','<script type="text/javascript">
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

$nFelder=count($kal_FeldName); $bOK=false; $sFehl=''; $sZ=''; $sF=''; $sPCode=''; $sPeri=''; $sQ=''; $sOnl='0';
$M1Day=''; $M2Day=''; $M3Day=''; $M4Day=''; $M5Day=''; $M6Day=''; $M7Day=''; $M8Day='';$M9Day=''; $WdhDat=''; $WdhMal=''; $bMitBild=false;
$aFehl=array(); $aW=array(); $aOh=array(); $aOa=array(); $aOs=array(); $aWTag=array(); $aWT14=array(); $aIds=array();

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET Daten holen
 $sQ=$_SERVER['QUERY_STRING']; $sId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:'');
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
  for($i=1;$i<$nSaetze;$i++){
   $s=rtrim($aD[$i]); $p=strpos($s,';');
   if($sId==substr($s,0,$p)){
    $aW=explode(';',str_replace('\n ',NL,$s)); $sOnl=$aW[1]; array_splice($aW,1,1);
    break;
   }
  }
 }elseif($DbO){ //SQL-Daten
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
   $aW=$rR->fetch_row(); $rR->close(); $sOnl=$aW[1]; array_splice($aW,1,1);
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 for($i=1;$i<$nFelder;$i++)
  if($kal_FeldType[$i]=='d') $aW[$i]='';
  elseif($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f'){$aOa[$i]=$aW[$i]; $aOh[$i]=''; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p);}
  elseif($kal_FeldType[$i]=='w'||$kal_FeldType[$i]=='n'||$kal_FeldType[$i]=='1'||$kal_FeldType[$i]=='2'||$kal_FeldType[$i]=='3'||$kal_FeldType[$i]=='r') $aW[$i]=str_replace('.',KAL_Dezimalzeichen,$aW[$i]);
  elseif(($kal_FeldType[$i]=='e'||$kal_FeldType[$i]=='c')&&!KAL_SQL||$kal_FeldType[$i]=='p') $aW[$i]=fKalDeCode($aW[$i]);
  elseif($kal_FeldType[$i]=='@'){if($kal_FeldName[$i]!='ZUSAGE_BIS') $aW[$i]=fKalAnzeigeDatum(date('Y-m-d')).date(' H:i'); elseif($aW[$i]) $aW[$i]=trim(fKalAnzeigeDatum($aW[$i]).strstr($aW[$i],' '));}
}else{ //POST Formularauswertung
 $sId=(isset($_POST['kal_Num'])?$_POST['kal_Num']:''); $sQ=(isset($_POST['kal_Qry'])?$_POST['kal_Qry']:'');
 $bUtf8=((isset($_POST['kal_JSSend'])||$_POST['kal_Utf8']=='1')?true:false);
 $sOnl=(isset($_POST['kal_Onl'])?$_POST['kal_Onl']:'');
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
    if($s) if($v=fKalErzeugeDatum($s)) $s=fKalAnzeigeDatum($v); else $aFehl[$i]=true; break;
   case '@': //EintragsDatum
    if($kal_FeldName[$i]!='ZUSAGE_BIS'){
     $v=date('Y-m-d H:i'); $s=fKalAnzeigeDatum($v).date(' H:i');
    }elseif($s){
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
    if($s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aFehl[$i]=true;
    if(!KAL_SQL) $v=fKalEnCode($s); break;
   case 'l': //Link oder E-Mail
    $v=$s; break;
   case 'b': //Bild
    if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i]; //kal_Up: neue Datei; kal_Dl: zu löschen
    $UpNaJS=(isset($_POST['kal_UpNa_'.$i])?fKalDateiname(basename($_POST['kal_UpNa_'.$i])):'');
    $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):'');
    if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
    if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
     if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_BildMaxKByte)||KAL_BildMaxKByte<=0){
      if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['kal_Up'.$i]['tmp_name']);
      elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['kal_Up'.$i]['tmp_name']);
      elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['kal_Up'.$i]['tmp_name']);
      if(!empty($Src)){
       if($sAlt=$aOh[$i]){ //alte Uploads weg
        $p=strpos($sAlt,'|'); @unlink(KAL_Pfad.'temp/-'.substr($sAlt,0,$p)); @unlink(KAL_Pfad.'temp/_'.substr($sAlt,$p+1)); $aOh[$i]='';
       }
       $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $sAlt='#|'.implode('|',$aOh); $sZhl='A';
       if(strpos($sAlt,'|'.$UpBa.$UpEx)){while(strpos($sAlt,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelname
       if($Sx>KAL_VorschauBreit||$Sy>KAL_VorschauHoch){ //Vorschau verkleinern
        $Dw=min(KAL_VorschauBreit,$Sx);
        if($Sx>KAL_VorschauBreit) $Dh=round(KAL_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
        if($Dh>KAL_VorschauHoch){$Dw=round(KAL_VorschauHoch/$Dh*$Dw); $Dh=KAL_VorschauHoch;}
        $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
        ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
        if(@imagejpeg($Dest,KAL_Pfad.'temp/-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|';
        else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',KAL_TxDateiRechte);}
        imagedestroy($Dest); unset($Dest);
       }else{
        if(@copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|';
        else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',KAL_TxDateiRechte);}
       }
       if($Sx>KAL_BildBreit||$Sy>KAL_BildHoch){ //Bild verkleinern
        $Dw=min(KAL_BildBreit,$Sx);
        if($Sx>KAL_BildBreit) $Dh=round(KAL_BildBreit/$Sx*$Sy); else $Dh=$Sy;
        if($Dh>KAL_BildHoch){$Dw=round(KAL_BildHoch/$Dh*$Dw); $Dh=KAL_BildHoch;}
        $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
        ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
        @imagejpeg($Dest,KAL_Pfad.'temp/_'.$UpBa.'.jpg');
        $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
       }else{$v.=$UpBa.$UpEx; @copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/_'.$UpBa.$UpEx);}
       imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,KAL_TxBildOeffnen);}
     }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_BildMaxKByte,KAL_TxBildGroesse);}
    }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
     $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxBildTyp);
    }elseif($s>'') if(isset($_POST['kal_Dl'.$i])&&$_POST['kal_Dl'.$i]){ //hochgeladenes Bild löschen
     $p=strrpos($s,'.'); @unlink(KAL_Pfad.'temp/-'.$s); @unlink(KAL_Pfad.'temp/_'.$s);
     if(strtolower(substr($s,$p))!='.jpg'){
      @unlink(KAL_Pfad.'temp/-'.substr($s,0,$p).'.jpg'); @unlink(KAL_Pfad.'temp/_'.substr($s,0,$p).'.jpg');
     }
     $s=''; $v=''; $aOh[$i]='';
    }
    $aOs[$i]=$v; break;
   case 'f': //Datei
    if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i];
    $UpNa=(isset($_FILES['kal_Up'.$i])?fKalDateiname(basename($_FILES['kal_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
    if($UpEx&&$UpEx!='.php'&&$UpEx!='.php3'&&$UpEx!='.php5'&&$UpEx!='.pl'){
     if($_FILES['kal_Up'.$i]['size']<=(1024*KAL_DateiMaxKByte)){
      if($aOh[$i]>''){@unlink(KAL_Pfad.'temp/'.$aOh[$i]); $aOh[$i]='';} //alten Upload weg
      $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $u='#|'.implode('|',$aOh); $sZhl='A';
      if(strpos($u,'|'.$UpBa.$UpEx)){while(strpos($u,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelnamen
      if(@copy($_FILES['kal_Up'.$i]['tmp_name'],KAL_Pfad.'temp/'.$UpBa.$UpEx)){$s=$UpBa.$UpEx; $v=$s; $aOh[$i]=$v;}
      else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',KAL_TxDateiRechte);}
     }else{$aFehl[$i]=true; $sFehl=str_replace('#',KAL_DateiMaxKByte,KAL_TxDateiGroesse);}
    }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
     $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),KAL_TxDateiTyp);
    }elseif($s>'') if(isset($_POST['kal_Dl'.$i])&&$_POST['kal_Dl'.$i]){ //hochgeladene Datei löschen
     @unlink(KAL_Pfad.'temp/'.$s); $s=''; $v=''; $aOh[$i]='';
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
   else{$sZ.=',"'.str_replace(NL,"\r\n",str_replace('\n ',NL,str_replace('"','\"',$v))).'"'; $sF.=',kal_'.$i;}
  }else{$aFehl[$i]=true; if(!KAL_SQL) $sZ.=';';}
 }
 if($sPeri=(isset($_POST['kal_Periode'])?$_POST['kal_Periode']:'')){ //Periodik auswerten
  if($WdhDat=(isset($_POST['kal_WdhDat'])?trim($_POST['kal_WdhDat']):'')){
   if($v=substr(fKalErzeugeDatum($WdhDat),0,10)){$WdhDat=fKalAnzeigeDatum($v); $sPCode=$sPeri.'|'.$v.'|';}else $aFehl['W']=true;
  }elseif($WdhMal=(isset($_POST['kal_WdhMal'])?(int)$_POST['kal_WdhMal']:0)){$sPCode=$sPeri.'|'.$WdhMal.'|';}else{$aFehl['W']=true; $WdhMal='';}
  if($sPeri=='A'){//mehrtaegig
  }elseif($sPeri=='B'){//woechentlich
   $aWTag=(isset($_POST['kal_WTag'])?$_POST['kal_WTag']:'');
   if(is_array($aWTag)){foreach($aWTag as $v) $sPCode.=substr($v,1);}else{$aFehl['B']=true; $aWTag=array();}
  }elseif($sPeri=='C'){//14-taegig
   $aWT14=(isset($_POST['kal_WT14'])?$_POST['kal_WT14']:'');
   if(is_array($aWT14)){foreach($aWT14 as $v) $sPCode.=substr($v,1);}else{$aFehl['C']=true; $aWT14=array();}
  }elseif($sPeri=='D'){//monatlich-1
   if($M1Day=(isset($_POST['kal_M1Day'])?(int)$_POST['kal_M1Day']:0)){if($M1Day<32) $sPCode.=$M1Day; else $aFehl['D']=true;}else{$aFehl['D']=true; $M1Day='';}
   if($M2Day=(isset($_POST['kal_M2Day'])?(int)$_POST['kal_M2Day']:0)){if($M2Day<32) $sPCode.='|'.$M2Day; else $aFehl['D']=true;}else{$M2Day=''; $sPCode.='|0';}
   $M3Day=(isset($_POST['kal_M3Day'])?$_POST['kal_M3Day']:''); $sPCode.='|'.$M3Day;
  }elseif($sPeri=='E'){//monatlich-2
   if($M4Day=(isset($_POST['kal_M4Day'])?(int)$_POST['kal_M4Day']:0)) $sPCode.=$M4Day; else{$aFehl['E']=true; $M4Day='';}
   $M5Day=(isset($_POST['kal_M5Day'])?$_POST['kal_M5Day']:'');
   if(strlen($M5Day)) {$M5Day=(int)$M5Day; $sPCode.='|'.$M5Day;}else{$aFehl['E']=true; $M5Day='';}
   $M6Day=(isset($_POST['kal_M6Day'])?$_POST['kal_M6Day']:''); $sPCode.='|'.$M6Day;
  }elseif($sPeri=='F'){//jaehrlich
   if($Jahr=(isset($_POST['kal_Jahr'])?$_POST['kal_Jahr']:'')) $sPCode.='0';
   else{
    if($M7Day=(isset($_POST['kal_M7Day'])?(int)$_POST['kal_M7Day']:0)) $sPCode.=$M7Day; else{$aFehl['F']=true; $M7Day='';}
    $M8Day=(isset($_POST['kal_M8Day'])?$_POST['kal_M8Day']:''); if(strlen($M8Day)) {$M8Day=(int)$M8Day; $sPCode.='|'.$M8Day;}else{$aFehl['F']=true; $M8Day='';}
    $M9Day=(isset($_POST['kal_M9Day'])?$_POST['kal_M9Day']:''); if(strlen($M9Day)) {$M9Day=(int)$M9Day; $sPCode.='|'.$M9Day;}else{$aFehl['F']=true; $M9Day='';}
   }
  }
 }
 if($sFehl==''){ //alles OK, eintragen
  if(count($aFehl)==0){
   if(!KAL_SQL){ //ohne SQL
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $aTmp=array();
    $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
    for($i=1;$i<$nSaetze;$i++){
     $s=rtrim($aD[$i]); $p=strpos($s,';'); $nId=max($nId,abs(substr($s,0,$p)));
     $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
    }
    $aTmp[(++$nId).';'.$sOnl]=substr($sZ,1); $aIds[]=$nId; //Termin anhaengen
    $aZ=explode(';',$nId.$sZ);
    if($sPCode) if($aWdhDat=fKalWdhDat(substr($sZ,1,10),$sPCode)){ //Wiederholungen
     $sZ=substr($sZ,13); reset($aWdhDat); foreach($aWdhDat as $v){$aTmp[(++$nId).';'.$sOnl]=$v.$sZ; $aIds[]=$nId;}
    }
    $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik';
    $aD=array(); $aD[0]=$s.NL; asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v.NL;
    if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){//neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bOK=true;
    }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Termine.'</i>',KAL_TxDateiRechte).'</p>';
   }elseif($DbO){ //bei SQL
    if(!strpos($sF,',periodik')){$sF.=',periodik'; $sZ.=',""';}
    if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online'.$sF.') VALUES("'.$sOnl.'"'.$sZ.')')){
     if($nId=$DbO->insert_id){
      $aIds[]=$nId; $bOK=true; $aZ=explode('","',$nId.'"'.substr($sZ,0,-1));
      if($sPCode) if($aWdhDat=fKalWdhDat(substr($sZ,2,10),$sPCode)){ // Wiederholungen
       $sZ=substr($sZ,14); reset($aWdhDat);
       foreach($aWdhDat as $v) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online'.$sF.') VALUES("'.$sOnl.'","'.$v.$sZ.')')){
        if($nId=$DbO->insert_id) $aIds[]=$nId;
       }
      }
     }else $Msg='<p class="admFehl">'.KAL_TxSqlEinfg.'</p>';
    }else $Msg='<p class="admFehl">'.KAL_TxSqlEinfg.'</p>';
   }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
   if($bOK){ // Daten gespeichert
    $Msg='<p class="admErfo">'.KAL_TxKopiereErfo.'</p>';
    for($i=1;$i<$nFelder;$i++){
     if($UpNa=(isset($aOh[$i])?$aOh[$i]:'')){ //neue Bilder und Dateien umspeichern
      if($kal_FeldType[$i]=='b'){
       $p=strpos($UpNa,'|'); $UpNa=substr($UpNa,0,$p);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/-'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'-'.$UpNa)) $bOK=false;
       @unlink(KAL_Pfad.'temp/-'.$UpNa); $UpNa=substr($aOh[$i],$p+1);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/_'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'_'.$UpNa)) $bOK=false;
       @unlink(KAL_Pfad.'temp/_'.$UpNa);
      }elseif($kal_FeldType[$i]=='f'){
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.'temp/'. $UpNa,KAL_Pfad.KAL_Bilder.$j.'~'.$UpNa)) $bOK=false;
       @unlink(KAL_Pfad.'temp/'.$UpNa);
      }
     }elseif(($sAlt=(isset($aOs[$i])?$aOs[$i]:''))&&$aOs[$i]==$aOa[$i]){ //alte Bilder und Dateien kopieren
      if($kal_FeldType[$i]=='b'){
       $p=strpos($sAlt,'|'); $UpNa=substr($sAlt,0,$p);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$sId.'-'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'-'.$UpNa)) $bOK=false;
       $UpNa=substr($sAlt,$p+1);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$sId.'_'.$UpNa,KAL_Pfad.KAL_Bilder.$j.'_'.$UpNa)) $bOK=false;
      }elseif($kal_FeldType[$i]=='f'){
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$sId.'~'.$sAlt,KAL_Pfad.KAL_Bilder.$j.'~'.$sAlt)) $bOK=false; $UpNa=$sAlt;
      }
     }
     if(!$bOK) $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Bilder.$UpNa.'</i>',KAL_TxDateiRechte).'</p>'; $bOK=true;
    }
    if($sOnl=='1'&&KAL_MailListeEintrag&&KAL_MailListeAdr!=''){ //Mailingliste
     $sBtr=KAL_TxMailListeBtr; $sMTx=strtoupper(KAL_TxNummer).': '.$aZ[0]; $sWww=fKalWww();
     for($i=1;$i<$nFelder;$i++) if($kal_EingabeFeld[$i]&&$kal_FeldType[$i]!='c'&&$kal_FeldType[$i]!='e'&&$kal_FeldType[$i]!='p'||$kal_FeldType[$i]=='u'){
      if($kal_FeldType[$i]!='u'){
       $sFN=$kal_FeldName[$i]; if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
       $sMTx.=NL.strtoupper($sFN).': '.fKalPlainText($aZ[$i],$kal_FeldType[$i],true);
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',fKalPlainText($aZ[$i],$kal_FeldType[$i],true),$sBtr);
      }elseif($aZ[$i]>'0000'){
       $s=KAL_TxAutorUnbekannt; if(!$k=$kal_NDetailFeld[$i]) if(!$k=KAL_NNutzerListFeld) if(!$k=$kal_DetailFeld[$i]) $k=KAL_NutzerListFeld;
       if($k>1){
        if(isset($aN)&&is_array($aN)){ //Nutzer schon geholt
         if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt; elseif(!KAL_SQL&&$k<5&&$k>1) $s=fKalDeCode($s);
        }elseif(!KAL_SQL){ //Nutzerdaten holen
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $s=((int)$aZ[$i]).';'; $p=strlen($s);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$s){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt; elseif($k<5&&$k>1) $s=fKalDeCode($s);
          break;
        }}elseif($DbO){ //SQL
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$aZ[$i].'"')){
          $aN=$rR->fetch_row(); $rR->close(); array_splice($aN,1,1); if(!$s=$aN[$k]) $s=KAL_TxAutorUnbekannt;
       }}}
       $sMTx.=NL.strtoupper($kal_FeldName[$i]).': '.$s;
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}',$s,$sBtr);
      }else{
       $sMTx.=NL.strtoupper($kal_FeldName[$i]).': Administrator/Autor';
       if(strpos($sBtr,'{'.$kal_FeldName[$i].'}')) $sBtr=str_replace('{'.$kal_FeldName[$i].'}','Administrator/Autor',$sBtr);
      }
     }
     $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,$sBtr));
     $sMTx=str_replace('#D',$sMTx,str_replace('#A',$sWww,str_replace('\n ',"\n",KAL_TxMailListeTxt)));
     require_once(KALPFAD.'class.plainmail.php'); $Mailer=new PlainMail();
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $sS=KAL_Sender; if($p=strpos($sS,'<')){$sF=substr($sS,0,$p); $sS=substr(substr($sS,0,-1),$p+1);} else $sF='';
     $Mailer->AddTo(KAL_MailListeAdr); $Mailer->Subject=$sBtr; $Mailer->SetFrom($sS,$sF);
     $s=KAL_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetReplyTo($s,$t);
     if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender); $Mailer->Text=$sMTx; $Mailer->Send();
    }
   }
  }else $Msg='<p class="admFehl">'.KAL_TxEingabeFehl.'</p>';
 }else $Msg='<p class="admFehl">'.$sFehl.'</p>';
}//POST
$aVg=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); //Hinweise und Kategorien holen
if(!$Msg) $Msg='<p class="admMeld">'.KAL_TxKopiereMeld.'</p>';

//Scriptausgabe
echo $Msg.NL; $nBreit=12;
for($i=1;$i<$nFelder;$i++) $nBreit=max($nBreit,strlen($kal_FeldName[$i]));
if($nBreit>25) $nBreit=25; $nBreit=round(0.65*$nBreit,0);
?>

<form name="kalEingabe" action="kopieren.php" onsubmit="return formSend()" enctype="multipart/form-data" method="post">
<input type="hidden" name="kal_Dmy" value="xx" />
<input type="hidden" name="kal_Num" value="<?php echo $sId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<script type="text/javascript">
 var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";
 if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";
 document.writeln('<input type="hidden" name="kal_Utf8" value="'+sUtf8+'" />');
</script>
<table class="admTabl" style="table-layout:fixed;width:100%" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1" style="width:<?php echo $nBreit?>em">Status</td>
 <td><input class="admRadio" type="radio" name="kal_Onl" value="0"<?php if($sOnl<'1') echo ' checked="checked"'?> /> offline &nbsp;
  <input class="admRadio" type="radio" name="kal_Onl" value="1"<?php if($sOnl=='1') echo ' checked="checked"'?> /> online</td>
</tr>
<?php
 for($i=1;$i<$nFelder;$i++){
  $sFN=$kal_FeldName[$i]; $aHlp=explode(';',(isset($aVg[$i])?trim($aVg[$i]):'')); //Hilfetext und etwaige Vorgabewerte
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
  if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td class="admSpa1" style="width:'.$nBreit.'em"><div id="kalLabel'.$i.'">'.$sFN.($kal_PflichtFeld[$i]?'*':'').'</div></td>'; //Feldname
  echo NL.'  <td>'; $sZ=NL.'   <div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="admFehl"':'').'>';
  $t=$kal_FeldType[$i]; $v=(isset($aW[$i])?str_replace('`,',';',$aW[$i]):''); //Feldinhalt
  switch($t){
  case 't': case 'e': case 'c': //Text, E-Mail, Kontakt
   if($t=='t') $v=str_replace(NL,'\n ',str_replace("\r",'',$v));
   echo $sZ.'<input style="width:99%" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="255" /></div>';
   break;
  case 'm': //Memo
   if(KAL_FormatCode) echo NL.'   <div title="'.KAL_TxBB_X.'">'.NL.fKalBBToolbar($i).NL; else echo NL.'   <div>';
   echo $sZ.'<textarea name="kal_F'.$i.'" style="width:99%" cols="80" rows="10">'.$v.'</textarea></div>'.NL.'   </div>';
   break;
  case 'a': case 'k': case 's': //Aufzählung/Kategorie
   reset($aHlp); $sO=''; foreach($aHlp as $w) $sO.='<option value="'.$w.'"'.($v==$w?' selected="selected"':'').'>'.$w.'</option>';
   echo $sZ.'<select name="kal_F'.$i.'" size="1"><option value="">---</option>'.substr($sO,strpos($sO,'<option',9)).'</select></div>';
   break;
  case 'd': //Datum
   echo $sZ.'<input class="kalTCal" style="width:8em" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="10" /> <span class="admMini">'.KAL_TxFormat.' '.fKalDatumsFormat().'</span></div>';
   break;
  case '@': //EintragsDatum
   if($kal_FeldName[$i]!='ZUSAGE_BIS') echo $sZ.$v.'<input type="hidden" name="kal_F'.$i.'" value="'.$v.'" /></div>';
   else echo $sZ.'<input type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" style="width:12em" /> <span class="admMini">'.KAL_TxFormat.' '.fKalDatumsFormat().' '.KAL_TxOder.' '.fKalDatumsFormat().' '.KAL_TxSymbUhr.'</span></div>';
   break;
  case 'z': //Zeit
   echo $sZ.'<input class="kalTime" style="width:8em" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="5" /> <span class="admMini">'.KAL_TxFormat.' '.KAL_TxSymbUhr.'</span></div>';
   break;
  case 'l': //Link
   echo $sZ.'<div title="Format:  Adresse  oder  Adresse|Linktext  oder  Adresse|Linktext|Target  oder  Adresse1|Linktext1|Target1||Adresse2|Linktext2|Target2"><input style="width:99%" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="255" /></div></div>';
   break;
  case 'j': case '#': case 'v': //Ja/Nein
   echo $sZ.'<input class="admRadio" type="radio" name="kal_F'.$i.'" value="J"'.($v!='J'?'':' checked="checked"').' /> '.KAL_TxJa.' &nbsp; <input class="admRadio" type="radio" name="kal_F'.$i.'" value="N"'.($v!='N'?'':' checked="checked"').' /> '.KAL_TxNein.' &nbsp; <input class="admRadio" type="radio" name="kal_F'.$i.'" value=""'.($v!=''?'':' checked="checked"').' /> '.KAL_TxJNLeer.'</div>';
   break;
  case 'w': //Waehrung
   echo $sZ.'<input style="width:7em" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" /> '.KAL_Waehrung.'</div>';
   break;
  case 'n': case 'r': case '1': case '2': case '3': case 'o': //Zahlen
   echo $sZ.'<input style="width:7em" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" />'.($t!='o'?'':' <span class="admMini">'.KAL_PLZLaenge.' '.KAL_TxStellen.'</span>').'</div>';
   break;
  case 'b': //Bild
   echo $sZ.'<input style="width:99%" type="file" name="kal_Up'.$i.'" size="80" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif" /><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>'; $bMitBild=true;
   if($v) echo NL.'   <div style="float:left;"><input class="admCheck" type="checkbox" name="kal_Dl'.$i.'" value="1" /><input type="hidden" name="kal_F'.$i.'" value="'.$v.'" /><input type="hidden" name="kal_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="admMini">'.$v.' '.KAL_TxLoeschen.'</span></div>';
   echo NL.'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="admMini">'.(KAL_BildMaxKByte>0?'(max. '.KAL_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': //Datei
   echo $sZ.'<input style="width:99%" type="file" name="kal_Up'.$i.'" size="80" /><input type="hidden" name="kal_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>';
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
   echo $sZ.'<input style="width:12em" type="text" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" /> <span class="admMini">'.KAL_TxNutzerNr.'</span></div>';
   break;
  case 'p': // Passwort
   echo $sZ.'<input style="width:12em" type="password" name="kal_F'.$i.'" value="'.$v.'" maxlength="16" /> <span class="admMini">'.KAL_TxPassRegel.'</span></div>';
   break;
  }
  if($v=$aHlp[0]) echo NL.'   <div><span class="admMini">'.str_replace('`,',';',$v).'</span></div>'; // Eingabehilfe
  echo NL.'  </td>'.NL.' </tr>';
 }
 //Pflichtfeldzeile
 echo NL.' <tr class="admTabl"><td class="admSpa1">&nbsp;</td><td class="admMini" style="text-align:right;">* <span class="admMini">'.KAL_TxPflicht.'</span></td></tr>';
 //Periodikzeilen
 if(KAL_Periodik){
  $aMonatsNam=explode(';',';'.KAL_TxKMonate);
  echo ' <tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="0"'.($sPeri?'':' checked="checked"').' />&nbsp;'.KAL_TxEinmal.'</td>
  <td><input class="admRadio" type="radio" name="kal_Periode" value="A"'.($sPeri!='A'?'':' checked="checked"').' /> '.KAL_TxTaegig.'</td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="B"'.($sPeri!='B'?'':' checked="checked"').' />&nbsp;'.KAL_TxWoechig.'</td>
  <td><div'.(isset($aFehl['B'])&&$aFehl['B']?' class="admFehl"':'').'>'.KAL_TxImmer;
  for($i=0;$i<7;$i++) echo NL.'    &nbsp; <input class="admCheck" type="checkbox" name="kal_WTag[]" value="W'.$i.'"'.(!in_array('W'.$i,$aWTag)?'':' checked="checked"').' onclick="fSelWt(this.checked,2)" /> '.$kal_WochenTag[$i];
  echo '</div>'.NL.'  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="C"'.($sPeri!='C'?'':' checked="checked"').' />&nbsp;'.KAL_Tx14Taegig.'</td>
  <td><div'.(isset($aFehl['C'])&&$aFehl['C']?' class="admFehl"':'').'>'.KAL_TxImmer;
  for($i=0;$i<7;$i++) echo NL.'    &nbsp; <input class="admCheck" type="Radio" name="kal_WT14[]" value="W'.$i.'"'.(!in_array('W'.$i,$aWT14)?'':' checked="checked"').' onclick="fSelWt(this.checked,3)" /> '.$kal_WochenTag[$i];
  echo '</div>'.NL.'  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="D"'.($sPeri!='D'?'':' checked="checked"').' />&nbsp;'.KAL_TxMonatig.'-1</td>
  <td><div'.(isset($aFehl['D'])&&$aFehl['D']?' class="admFehl"':'').'>'.KAL_TxImmer.' '.KAL_TxAm.'
   <input style="width:2.2em;" type="text" name="kal_M1Day" value="'.$M1Day.'" maxlength="2" onkeyup="fSelWt(this.value,4)" />. '.KAL_TxTag.'  '.KAL_TxUnd.'  '.KAL_TxAm.'
   <input style="width:2.2em;" type="text" name="kal_M2Day" value="'.$M2Day.'" maxlength="2" onkeyup="fSelWt(this.value,4)" />. '.KAL_TxTagDesMonat.'
   in&nbsp;jedem&nbsp;<select style="width:11em;" name="kal_M3Day" onchange="fSelWt(this.value,4)"><option value="1"'.($M3Day!=1?'':' selected="selected"').'>Monat</option><option value="2"'.($M3Day!=2?'':' selected="selected"').'>2. Monat</option><option value="3"'.($M3Day!=3?'':' selected="selected"').'>3. Monat</option><option value="4"'.($M3Day!=4?'':' selected="selected"').'>4. Monat</option><option value="6"'.($M3Day!=6?'':' selected="selected"').'>6. Monat</option></select></div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="E"'.($sPeri!='E'?'':' checked="checked"').' />&nbsp;'.KAL_TxMonatig.'-2</td>
  <td><div'.(isset($aFehl['E'])&&$aFehl['E']?' class="admFehl"':'').'>'.KAL_TxImmer.' '.KAL_TxAm.'
   <select style="width:5.2em;" name="kal_M4Day" onchange="fSelWt(this.value,5)"><option value="">-</option><option value="1"'.($M4Day!=1?'':' selected="selected"').'>1.</option><option value="2"'.($M4Day!=2?'':' selected="selected"').'>2.</option><option value="3"'.($M4Day!=3?'':' selected="selected"').'>3.</option><option value="4"'.($M4Day!=4?'':' selected="selected"').'>4.</option><option value="5"'.($M4Day!=5?'':' selected="selected"').'>5.</option><option value="6"'.($M4Day!=6?'':' selected="selected"').'>'.KAL_TxLetzten.'</option></select>
   <select style="width:4.4em;" name="kal_M5Day" onchange="fSelWt(this.value,5)"><option value="">-</option>';
  for($i=0;$i<7;$i++) echo '<option value="'.$i.'"'.($M5Day!=$i||!is_int($M5Day)?'':' selected="selected"').'>'.$kal_WochenTag[$i].'</option>';
  echo '</select> '.KAL_TxTagDesMonat.'
   in&nbsp;jedem&nbsp;<select style="width:11em;" name="kal_M6Day" onchange="fSelWt(this.value,5)"><option value="1"'.($M6Day!=1?'':' selected="selected"').'>Monat</option><option value="2"'.($M6Day!=2?'':' selected="selected"').'>2. Monat</option><option value="3"'.($M6Day!=3?'':' selected="selected"').'>3. Monat</option><option value="4"'.($M6Day!=4?'':' selected="selected"').'>4. Monat</option><option value="6"'.($M6Day!=6?'':' selected="selected"').'>6. Monat</option></select></div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1"><input class="admRadio" type="radio" name="kal_Periode" value="F"'.($sPeri!='F'?'':' checked="checked"').' />&nbsp;'.KAL_TxJaehrlich.'</td>
  <td><div'.(isset($aFehl['F'])&&$aFehl['F']?' class="admFehl"':'').'>'.KAL_TxImmer.' '.KAL_TxAm.' <input class="admCheck" type="checkbox" name="kal_Jahr" value="1"'.(empty($Jahr)?'':' checked="checked"').' onclick="fSelWt(this.value,6)" /> '.KAL_TxSelbenDatum.' '.KAL_TxOder.' '.KAL_TxAm.'
   <select style="width:5.2em;" name="kal_M7Day" onchange="fSelWt(this.value,6)"><option value="">-</option><option value="1"'.($M7Day!=1?'':' selected="selected"').'>1.</option><option value="2"'.($M7Day!=2?'':' selected="selected"').'>2.</option><option value="3"'.($M7Day!=3?'':' selected="selected"').'>3.</option><option value="4"'.($M7Day!=4?'':' selected="selected"').'>4.</option><option value="5"'.($M7Day!=5?'':' selected="selected"').'>5.</option><option value="6"'.($M7Day!=6?'':' selected="selected"').'>'.KAL_TxVorLetzten.'</option><option value="7"'.($M7Day!=7?'':' selected="selected"').'>'.KAL_TxLetzten.'</option></select>
   <select style="width:4.4em;" name="kal_M8Day" onchange="fSelWt(this.value,6)"><option value="">-</option>';
  for($i=0;$i<7;$i++) echo '<option value="'.$i.'"'.($M8Day!=$i||!is_int($M8Day)?'':' selected="selected"').'>'.$kal_WochenTag[$i].'</option>';
  echo '</select> '.KAL_TxIm.'
   <select style="width:4.4em;" name="kal_M9Day" onchange="fSelWt(this.value,6)"><option value="0">'.KAL_TxJahr.'</option>';
  for($i=1;$i<13;$i++) echo '<option value="'.$i.'"'.($M9Day!=$i||!is_int($M9Day)?'':' selected="selected"').'>'.$aMonatsNam[$i].'</option>';
  echo '</select></div>
  </td>
 </tr><tr class="admTabl">
  <td class="admSpa1" style="padding-left:1.5em;">'.KAL_TxInsgesamt.'</td>
  <td><div'.(isset($aFehl['W'])&&$aFehl['W']?' class="admFehl"':'').'>'.KAL_TxBisZum.'
   <input style="width:6.6em;" type="text" name="kal_WdhDat" value="'.$WdhDat.'" maxlength="10" /> <span class="admMini">('.fKalDatumsFormat().')</span>
   &nbsp; '.KAL_TxOder.'
   <input style="width:2.2em;" type="text" name="kal_WdhMal" value="'.$WdhMal.'" maxlength="3" /> '.KAL_TxMale.'</div>
  </td>
 </tr>';
 }
?>

</table>
<p class="admSubmit"><?php if(!$bOK){?><input class="admSubmit" type="submit" value="Eintragen"><?php }?></p>
</form>
<?php
if(file_exists('liste.php')) echo '<p class="admSubmit">[ <a href="liste.php?'.$sQ.'">zurück zur Liste</a> ]</p>'.NL.NL;

if($bMitBild && KAL_BildResize){
 echo "\n".'<script src="'.$sHttp.'kalEingabeBild.js" type="text/javascript"></script>';
 echo "\n".'<script type="text/javascript">';
 echo "\n".' sPostURL="kopieren.php";';
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
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}

function fKalTCalFormat(){
 $s1='d'; $s2='m'; $s3='Y';
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3='d'; break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2='d'; break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalWdhDat($sBeg,$sCod){
 $aTmp=explode('|',$sCod); $sTyp=(isset($aTmp[0])?$aTmp[0]:''); $sEnd=(isset($aTmp[1])?$aTmp[1]:''); $sP1=(isset($aTmp[2])?$aTmp[2]:''); $sP2=(isset($aTmp[3])?(int)$aTmp[3]:0); $sP3=(isset($aTmp[4])?(int)$aTmp[4]:0);
 if(strpos($sEnd,'-')>0){// bis Enddatum
  $s=date('Y-m-d',KAL_MaxPeriode*86400+time()); if($sEnd>$s) $sEnd=$s;
  $nMax=3653;
 }else{ // n-Mal
  $nMax=min($sEnd,KAL_MaxWiederhol);
  $sEnd=date('Y-m-d',KAL_MaxPeriode*86400+time());
 }
 $bDo=true; $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),substr($sBeg,0,4)); $bMon1=false; $bMon11=false; $aDat=array(); $Dat=''; if(!isset($aTmp[4])) $aTmp[4]='0';
 while($bDo){
  switch($sTyp){
   case 'A': //mehrtaegig
    $nAkt+=86400; $Dat=date('Y-m-d w',$nAkt);
    break;
   case 'B': //woechentlich
    $nAkt+=86400; while(strpos($sP1,@date('w',$nAkt))===false) $nAkt+=86400; $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'C': //14-taegig
    if(@date('w',$nAkt)==$sP1) $nAkt+=(14*86400); else while(@date('w',$nAkt)!=$sP1) $nAkt+=86400;
    $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'D': //monatlich-1
    $m=$sP3;
    do{
     $nAkt+=86400; $n=(int)@date('d',$nAkt);
     while($n!=(int)$sP1&&$n!=(int)$sP2){$nAkt+=86400; $n=(int)@date('d',$nAkt);}
     if($n==(int)$sP1&& --$m<=0) $bMon1=true; elseif($n==(int)$sP2&&!$bMon11) $bMon1=true; $bMon11=true; //allererste Fundstelle ist 2.Datum
    }while(!$bMon1);
    $Dat=@date('Y-m-d w',$nAkt); if($n==(int)$sP2||$sP2==0) $bMon1=false; // Monat erledigt
    break;
   case 'E': //monatlich-2
    $m=$sP3;
    do{
     if($sP1<5) $Dat=date('Y-m-',$nAkt).(1+7*($sP1-1)); /* 1...4 */ else $Dat=date('Y-m-t',$nAkt); //5. oder letzter
     $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
     if($sP1<5){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400;} //1...4
     else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400;} //5. oder letzter
     $Dat=@date('Y-m-d w',$nAkt);
     $s=date('Y-m-t',$nAkt); $nAkt=@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))+86400;
     if(substr($Dat,0,10)<=$sBeg) $Dat=''; if($sP1==5) if((int)substr($Dat,8,2)<29) $Dat='';
     if($Dat) if(--$m>0) $Dat=''; // Monate auslassen
    }while(!$Dat);
    break;
   case 'F': //jaehrlich
    if($sP1<='0'){//fester Termin
     $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),date('Y',$nAkt)+1);
     while(date('m-d',$nAkt)>substr($sBeg,5,5)) $nAkt-=86400; $Dat=@date('Y-m-d w',$nAkt);
    }else{//variables Datum
     do{
      $sJ=date('Y',$nAkt); if(!$sM=(int)$aTmp[4]) if($sP1<6) $sM=1; else $sM=12; //Monat des Jahres
      $Dat=$sJ.'-'.sprintf('%02d',$sM).'-'.($sP1<6?'01':date('t',@mktime(12,0,0,$sM,1,$sJ)));
      $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
      if($sP1<6){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400; $nAkt+=(7*($sP1-1)*86400);} //1...5
      else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400; if($sP1==6) $nAkt-=(7*86400);} //(vor)letzter
      $Dat=@date('Y-m-d w',$nAkt); $nAkt=@mktime(12,0,0,1,2,1+(int)$sJ);
      if(substr($Dat,0,10)<=$sBeg||substr($Dat,0,4)!=$sJ||($aTmp[4]>'0'&&$sM!=(int)substr($Dat,5,2))) $Dat='';
     }while(!$Dat);
    }
    break;
  }
  if($nMax-->0&&substr($Dat,0,10)<=$sEnd){if($Dat) $aDat[]=$Dat;} else $bDo=false;
 }
 if(count($aDat)>0) return $aDat; else return false;
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

function fKalPlainText($s,$t,$bMemo=false){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||$bMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'd': if($s!='..') $s=fKalAnzeigeDatum($s); else $s=''; break;
  case '@': $s=fKalAnzeigeDatum($s).substr($s,10);
  case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  case 'l': $aL=explode('||',$s); $s=''; foreach($aL as $w){$aI=explode('|',$w); $s.=$aI[0].', ';} $s=substr($s,0,-2); break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}
function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>