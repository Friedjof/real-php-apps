<?php
function fMpSeite(){ //Detaildruck
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 $Meld=''; $MTyp='Fehl'; $bSes=false; $bOkD=false; $sGMap='';

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(MP_SQL){
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(MP_SqlCharSet) $DbO->set_charset(MP_SqlCharSet);}else{$DbO=NULL; $Meld=MP_TxSqlVrbdg;}
 }

 //Struktur holen
 $nFelder=0; $aStru=array();
 $aMpFN=array(); $aMpFT=array(); $aMpDF=array(); $aMpND=array(); $aMpZS=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
 if(!MP_SQL){ //Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur);
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.MP_Segment.'"')){
   $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(count($aStru)>1){
  $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
  if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
  $aMpFT=explode(';',rtrim($aStru[1])); $aMpFT[0]='i'; $aMpFT[1]='d';
  $aMpDF=explode(';',rtrim($aStru[7])); $aMpDF[0]=substr($aMpDF[0],14,1);
  $aMpND=explode(';',rtrim($aStru[8])); $aMpND[0]=substr($aMpND[0],14,1);
  $aMpZS=explode(';',rtrim($aStru[9])); $aMpZS[0]='';
  $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]=''; $aMpAW[1]='';
  $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
  $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
 }

 $a=array(); $sIntervallAnfang=date('Y-m-d');
 if($sId=(isset($_GET['mp_Nummer'])?(int)$_GET['mp_Nummer']:0)){
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD); $s=$sId.';1;'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$a=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); array_splice($a,1,1); $bOkD=true; break;}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'" AND online="1"')){
    if($a=$rR->fetch_row()){array_splice($a,1,1); $bOkD=true;} $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }//SQL
  if($bOkD){
   $sAblaufDat=$a[1]; $sIntervallAnfang=date('Y-m-d'); $sIntervallEnde='9';
   if(MP_SuchArchiv) if(isset($_GET['mp_Archiv'])){$sIntervallEnde=$sIntervallAnfang; $sIntervallAnfang='00';} //Archivsuche
   if($sAblaufDat<$sIntervallAnfang||$sAblaufDat>$sIntervallEnde) $bOkD=false;
  }
 }

 //Session pruefen
 if(MP_NDetailAnders) if(($sSes=MP_Session)||(defined('MP_NeuSession')&&($sSes=MP_NeuSession))){
  $nId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!MP_SQL){
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $nId=$nId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Meld=MP_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Meld=MP_TxSessionUngueltig;
    }else $Meld=MP_TxSqlFrage;
   }
  }else $Meld=MP_TxSessionZeit;
 }

 // Meldung ausgeben
 if($Meld=='')
  if($bOkD){$Meld=str_replace('#S',MP_SegName,str_replace('#N',sprintf('%0'.MP_NummerStellen.'d',$a[0]),MP_TxDetails)); $MTyp='Meld';}
  else $Meld=str_replace('#',$sId,MP_TxNummerUngueltig);
 $X="\n".'<p class="mp'.$MTyp.'">'.fMpTx($Meld).'</p>';

 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
 $nFelder=count($aMpFN); $nFarb=1; $nNDf=MP_NutzerDetailFeld;
 if($bSes){$nNDf=MP_NNutzerDetailFeld; if(MP_NDetailAnders) $aMpDF=$aMpND;}
 $X.="\n\n".'<div class="mpDrTab">'; //Tabelle
 if($bOkD) for($i=0;$i<$nFelder;$i++){
  $t=$aMpFT[$i];
  if($aMpDF[$i]>0&&$t!='p'&&$t!='c'&&substr($aMpFN[$i],0,5)!='META-'&&$aMpFN[$i]!='TITLE'){
   if($bOkD&&($s=str_replace('`,',';',$a[$i]))){
    switch($t){
     case 't': case 'm': $s=fMpBB(fMpDt($s)); break; //Text/Memo//Gastkommentar
     case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; break;
     case 'z': $s.=' '.fMpTx(MP_TxUhr); break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung;
      }else if(MP_ZeigeLeeres) $s='&nbsp;'; else $s='';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein);
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s);
      break;
     case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); break; //Zaehlnummer
     case 'l': //Link
      $aI=explode('|',$s);
      $s='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" alt="" /> ';
      if(!MP_DetailLinkSymbol) $s.=fMpDt(isset($aI[1])?$aI[1]:$aI[0]);
      break;
     case 'e': //eMail
      $s='<img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" alt="" /> '.(MP_DruckDMailOffen?' '.(MP_SQL?$s:fMpDeCode($s)):'');
      break;
     case 's': $w=$s; //Symbol
      $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
      $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.fMpDt($w).'" alt="'.fMpDt($w).'" />';
      break;
     case 'b': //Bild
      $s=substr($s,strpos($s,'|')+1); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'_'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $w=fMpDt(substr($s,strpos($s,'_')+1,-4));
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.$w.'" alt="'.$w.'" />';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
      $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" alt="" /> ';
      if(!MP_DetailDateiSymbol) $v.=fMpKurzName($s);
      $s=$v;
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=MP_TxAutorUnbekannt;
       if(!MP_SQL){ //Textdaten
        $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
        for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
         if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt; elseif($nNDf<5&&$nNDf>1) $s=fMpDeCode($s);
         break;
        }
       }elseif($DbO){ //SQL-Daten
        if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
         $aN=$rR->fetch_row(); $rR->close();
         if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt;}
         else $s=MP_TxAutorUnbekannt;
       }}
      }else $s=MP_TxAutor0000;
      $s=fMpDt($s); break;
     case 'x': $aI=explode(',',$s); //StreetMap
      if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
       $s='<div class="mpNorm" id="GGeo'.$i.'" style="width:'.MP_GMapBreit.'px;height:'.MP_GMapHoch.'px;">'.fMpTx(MP_TxGMap1Warten).'</div>';
       $sGMap.=(MP_GMapSource=='O'?fMpOMap($i,$aI):fMpGMap($i,$aI));
      }else $s='&nbsp;';
      break;
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }//switch
   }elseif($t=='b'&&MP_ErsatzBildGross>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildGross; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />';
   }elseif(MP_ZeigeLeeres) $s='&nbsp;';
   if(strlen($s)>0){
    if(!MP_DruckDFarbig) $sCSS='mpTbZlDr'; else{$sCSS='mpTbZl'.$nFarb; if(--$nFarb<=0) $nFarb=2;}
    $X.="\n".'<div class="'.$sCSS.'">';
    $X.="\n".' <div class="mpTbDr1">'.fMpTx($aMpFN[$i]).'</div>';
    $X.="\n".' <div class="mpTbDr2"'.($aMpZS[$i]?' style="'.$aMpZS[$i].'"':'').'>'.$s."</div>\n</div>";
   }
  }
 }else{$X="\n".'<p class="mpFehl">'.fMpTx($Meld).'</p>'; define('MP_410Gone',true);}
 $X.="\n".'</div>'; //Tabelle

 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(MP_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.MP_Url.'maps/leaflet.css" />'."\n".$X."\n\n".'<script type="text/javascript" src="'.MP_Url.'maps/leaflet.js"></script>';
  else $X.="\n\n".'<script type="text/javascript" src="'.MP_GMapURL.'"></script>';
  $X.="\n".'<script type="text/javascript">'.$sGMap."\n".'</script>';
 }
 if(MP_CanoLink&&(!MP_DruckPopup&&!MP_DetailPopup||MP_CanoPopup)){$sC=fMpHref('detail','1',$sId,'',MP_Segment,true); /* $p=strrpos($sC,'/'); if(!($p===false)) $sC=substr($sC,$p+1); */ define('MP_Canonical',str_replace('&amp;','&',$sC));}
 return $X;
}

function fMpKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fMpOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
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
 map'.$n.'.on(\'click\',onMapAction); map'.$n.'.on(\'zoomstart\',onMapAction); map'.$n.'.on(\'movestart\',onMapAction);';
}

function fMpGMap($n,$a){ //JavaScriptcode zu Google-Map
return (MP_GMapV3?'
 var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
 var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
 var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:false,streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
 var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
 var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fMpTx(MP_TxGMapOrt)."'".'});':'
 if(GBrowserIsCompatible()){
  map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
  map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
  map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
  map'.$n.'.addControl(new GSmallMapControl());
 }');
}
?>