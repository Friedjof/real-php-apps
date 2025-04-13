<?php
function fKalSeite(){ //Detaildruck
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_ZeilenStil,
  $kal_Kategorien, $kal_Symbole, $kal_WochenTag, $aKalDaten;

 $Et=''; $Es='Fehl'; $bSes=false; $bOK=false;

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 if($sId=(isset($_GET['kal_Nummer'])?fKalRq1($_GET['kal_Nummer']):'0')){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$a=explode(';',str_replace('\n ',"\n",rtrim($aD[$i]))); $bOK=($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'); array_splice($a,1,1); break;}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'" AND (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').')')){
    if($a=$rR->fetch_row()){array_splice($a,1,1); $bOK=true;} $rR->close();
   }else $Et=KAL_TxSqlFrage;
  }else $Et=$SqE;//SQL
 }

 //Session pruefen
 if(KAL_NDetailAnders) if($sSes=substr(KAL_Session,17,12)){
  $nId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)) $bSes=true; else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,session FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows>0) $bSes=true; else $Et=KAL_TxSessionUngueltig; $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }
  }else $Et=KAL_TxSessionZeit;
 }

 // Meldung ausgeben
 if($Et=='') if($bOK){$Et=str_replace('#',fKalAnzeigeDatum($a[1]),KAL_TxDetails); $Es='Meld';} else $Et=str_replace('#',$sId,KAL_TxKeinDatensatz);
 $X="\n".'<p class="kal'.$Es.'">'.fKalTx($Et).'</p>'; $sGMap='';

 if($bSes&&KAL_NDetailAnders) $kal_DetailFeld=$GLOBALS['kal_NDetailFeld'];
 $nFelder=count($kal_FeldName); $kal_FeldName[0]=KAL_TxNummer; $nFarb=1;
 if(!KAL_EigeneDruckDetails) $X.="\n\n".'<div class="kalDrTab">';
 else if(!$Dtl=(file_exists(KAL_Pfad.'kalDruckDetail.htm')?join('',file(KAL_Pfad.'kalDruckDetail.htm')):'')) $Dtl='<p style="color:red;font-weight:bold;">'.fKalTx(str_replace('#','kalDruckDetail.htm',KAL_TxKeinDetail)).'</p>';
 for($i=0;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if(($kal_DetailFeld[$i]>0||KAL_EigeneDetails)&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
   if($bOK&&($s=str_replace('`,',';',$a[$i]))){
    switch($t){
     case 't': case 'm': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Memo//Gastkommentar
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie so lassen
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      if(KAL_MonatDLang>0&&$t=='d'){$aMonate=explode(';',';'.(KAL_MonatDLang==2?KAL_TxLMonate:KAL_TxKMonate)); $s2=fKalTx($aMonate[(int)$s2]);}
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      if($t=='d'){
       if(KAL_MonatDLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
       if($i==1) if($nP=strpos($X,'#',strpos($X,'class="kalMeld"'))) $X=substr_replace($X,$s,$nP,1);
       if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
      }elseif($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $s.=' '.fKalTx(KAL_TxUhr); break; //Uhrzeit
     case 'w': //Währung
      if($s>0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung;
      }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein);
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
      break;
     case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); break; //Zählnummer
     case 'l': //Link
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalDt(isset($aI[1])?$aI[1]:$w);
       $s.='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" alt="'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'"> '.(!KAL_DetailLinkSymbol?$v.', ':'  ');
      }$s=substr($s,0,-2); break;
     case 'e': //eMail
      $s='<img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" alt="Mail"> '.(KAL_DruckDMailOffen?' '.(KAL_SQL?$s:fKalDeCode($s)):'');
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="Icon">';
      break;
     case 'b': //Bild
      $s=substr($s,strpos($s,'|')+1); $s=KAL_Bilder.$sId.'_'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="Bild">';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" alt="Icon">';
      if(!KAL_DetailDateiSymbol) $v.=fKalKurzName($s);
      $s=$v;
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=KAL_TxAutorUnbekannt;
       if(!KAL_SQL){ //Textdaten
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
        for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
         if(!$s=$aN[$kal_DetailFeld[$i]]) $s=KAL_TxAutorUnbekannt; elseif($kal_DetailFeld[$i]<5&&$kal_DetailFeld[$i]>1) $s=fKalDeCode($s); $s=fKalDt($s);
         break;
        }
       }elseif($DbO){ //SQL-Daten
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
         $aN=$rR->fetch_row(); $rR->close();
         if(is_array($aN)){
          array_splice($aN,1,1); if(!$s=fKalDt($aN[$kal_DetailFeld[$i]])) $s=KAL_TxAutorUnbekannt;
         }else $s=KAL_TxAutorUnbekannt;
      }}}else $s=KAL_TxAutor0000;
      break;
     case 'x': $aI=explode(',',$s); //StreetMap
      if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
       $s='<div class="kalNorm" id="GGeo'.$i.'" style="width:99%;max-width:'.KAL_GMapBreit.'px;height:'.KAL_GMapHoch.'px;">'.fKalTx(KAL_TxGMap1Warten).'</div>';
       $sGMap.=(KAL_GMapSource=='O'?fKalOMap($i,$aI):fKalGMap($i,$aI));
      }else $s='&nbsp;';
      break;
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }//switch
   }elseif($t=='b'&&KAL_ErsatzBildGross>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildGross; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sStil.='text-align:center;';
   }elseif(KAL_ZeigeLeeres) $s='&nbsp;';
   if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt) $s=''; elseif($s>'0') $s=(int)$s;}
   elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(!KAL_EigeneDruckDetails){ //Standardlayout
    if(strlen($s)>0){
     $X.="\n".'<div class="kalTbZl'.(KAL_DruckDFarbig?$nFarb:'Dr').'">'; if(--$nFarb<=0) $nFarb=2;
     $X.="\n".' <div class="kalTb'.(KAL_DruckDFarbig?'Sp1':'Dr1').'">'.fKalTx($sFN).'</div>';
     $X.="\n".' <div class="kalTb'.(KAL_DruckDFarbig?'Sp2':'Dr').'"'.($kal_ZeilenStil[$i]?' style="'.$kal_ZeilenStil[$i].'"':'').'>'.$s.'</div>';
     $X.="\n".'</div>';
    }
   }else $Dtl=str_replace('{'.$kal_FeldName[$i].'}',$s,$Dtl); //eigene Details
  }
 }
 if(!KAL_EigeneDruckDetails) $X.="\n".'</div>'; else $X.="\n".$Dtl;

 // StreetMap initialisieren
 if(!empty($sGMap)){
  if(KAL_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.KAL_Url.'maps/leaflet.css">'."\n".$X."\n\n".'<script src="'.KAL_Url.'maps/leaflet.js"></script>';
  else $X.="\n\n".'<script src="'.KAL_GMapURL.'"></script>';
  $X.="\n".'<script>'.$sGMap."\n".'</script>';
 }

 return $X;
}

function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
return '
 var mbAttr=\'Karten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Bilder &copy; <a href="https://www.mapbox.com/">Mapbox</a>\';
 var mbUrl=\'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='.KAL_SMapCode.'\';
 var sat=L.tileLayer(mbUrl,{id:\'mapbox/satellite-v9\',tileSize:512,zoomOffset:-1,attribution:mbAttr});
 var osm=L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\',{attribution:\'&copy OpenStreetMap\',maxZoom:19});
 var bDrag=true; if('.(KAL_SMap2Finger?'true':'false').') bDrag=!L.Browser.mobile;
 var map'.$n.'=L.map(\'GGeo'.$n.'\',{center:['.sprintf('%.15f,%.15f',$a[0],$a[1]).'],zoom:'.$a[4].(KAL_SMap2Finger?',dragging:bDrag,tap:bDrag':'').',scrollWheelZoom:false,layers:[osm]});
 if('.(KAL_SMapTypeControl?'true':'false').'){var baseLayers={\'Karte\':osm,\'Satellit\':sat}; var layerControl=L.control.layers(baseLayers).addTo(map'.$n.');}
 var marker=L.marker(['.sprintf('%.15f,%.15f',$a[2],$a[3]).'],{opacity:0.75'.(KAL_TxGMapOrt>''?",title:'".fKalTx(KAL_TxGMapOrt)."'":'').'}).addTo(map'.$n.');
 var mapCenter=map'.$n.'.getCenter(); var nF=Math.pow(2,'.$a[4].'); mapCenter.lng+=153.6/nF; mapCenter.lat-=64/nF;
 var tooltip=L.tooltip().setLatLng(mapCenter).setContent(\'Verschieben der Karte mit 2 Fingern!\').addTo(map'.$n.'); if(bDrag) map'.$n.'.closeTooltip(tooltip);
 function onMapAction(e){map'.$n.'.closeTooltip(tooltip);}
 map'.$n.'.on(\'click\',onMapAction); map'.$n.'.on(\'zoomstart\',onMapAction); map'.$n.'.on(\'movestart\',onMapAction);';
}

function fKalGMap($n,$a){ //JavaScriptcode zu Google-Map
return (KAL_GMapV3?'
 var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
 var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
 var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:false,streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
 var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
 var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fKalTx(KAL_TxGMapOrt)."'".'});':'
 if(GBrowserIsCompatible()){
  map'.$n.'=new GMap2(document.getElementById('."'".'GGeo'.$n."'".'));
  map'.$n.'.setCenter(new GLatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).'),'.$a[4].');
  map'.$n.'.addOverlay(new GMarker(new GLatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).')));
  map'.$n.'.addControl(new GSmallMapControl());
 }');
}
?>