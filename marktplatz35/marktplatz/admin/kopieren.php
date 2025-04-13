<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Inserat kopieren','<script type="text/javascript">
 function geoWin(sURL){gWin=window.open(sURL,"geown","width='.(min(max(MP_GMapBreit,500),725)+50).',height=700,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");gWin.focus();}
</script>
<script src="eingeben.js" type="text/javascript"></script>','IIe');

$aStru=array(); $bMitBild=false; $nDestSegNo=$nSegNo; $sDestSegNo=$sSegNo;
if($nSegNo!=0){ //Segment gewählt

 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aDF=array(); $aND=array();
 $aPF=array(); $aTZ=array(); $aET=array(); $aAW=array(); $aKW=array(); $aSW=array(); $sOnl='0'; $bSelf=true; // selbes Zielsegment
 if(!MP_SQL){//Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
   $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
   if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;} $sDestBldDir=$sBldDir; $sDestBldSeg=$sBldSeg;

 $aW=array(); $aV=array(); $aFehl=array();
 $bOK=false; $sFehl=''; $sZ=''; $sF='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET Daten holen
  $sQ=$_SERVER['QUERY_STRING']; $sId=$_GET['mp_Num'];
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){
    $s=$aD[$i]; $p=strpos($s,';');
    if($sId==substr($s,0,$p)){
     $aW=explode(';',str_replace('\n ',NL,rtrim($s))); $sOnl=$aW[1]; array_splice($aW,1,1);
     break;
    }
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'" AND online="1"')){
    $aW=$rR->fetch_row(); $rR->close(); $sOnl=$aW[1]; array_splice($aW,1,1);
   }else $Meld=MP_TxSqlFrage;
  }
  for($i=1;$i<$nFelder;$i++){
   $aOh[$i]=''; $aOa[$i]=''; $aOs[$i]='';
   if(isset($aW[$i])) if($aFT[$i]=='d'){if($aW[$i]) $aW[$i]=fMpAnzeigeDatum($aW[$i]);}
   elseif($aFT[$i]=='b'||$aFT[$i]=='f'){$aOa[$i]=$aW[$i]; if($p=strpos($aW[$i],'|')) $aW[$i]=substr($aW[$i],1+$p);}
   elseif($aFT[$i]=='w'||$aFT[$i]=='n'||$aFT[$i]=='1'||$aFT[$i]=='2'||$aFT[$i]=='3'||$aFT[$i]=='r') $aW[$i]=str_replace('.',MP_Dezimalzeichen,$aW[$i]);
   elseif(($aFT[$i]=='e'||$aFT[$i]=='c')&&!MP_SQL) $aW[$i]=fMpDeCode($aW[$i]);
   elseif($aFT[$i]=='p') $aW[$i]=fMpDeCode($aW[$i]);
   elseif($aFT[$i]=='@') $aW[$i]=fMpAnzeigeDatum(date('Y-m-d')).date(' H:i');
  }
  $aW[1]=fMpAnzeigeDatum(date('Y-m-d',min(86400*$aET[1]+time(),2147483647)));
  $nDestSegNo=$nSegNo; $sDestSegNo=$sSegNo;
 }else{//POST
  $sId=$_POST['mp_Num']; $sQ=$_POST['mp_Qry'];
  $bUtf8=((isset($_POST['mp_JSSend'])||$_POST['mp_Utf8']=='1')?true:false);
  $nDestSegNo=$_POST['mp_Segment']; $sDestSegNo=sprintf('%02d',$nDestSegNo); $bSelf=($nDestSegNo==$nSegNo);  //selbes Zielsegment
  if(!$bSelf){if(MP_BldTrennen){$sDestBldDir=$sDestSegNo.'/'; $sDestBldSeg='';}else{$sDestBldDir=''; $sDestBldSeg=$sDestSegNo;}}

  $sOnl=(isset($_POST['mp_Onl'])?$_POST['mp_Onl']:'');
  for($i=1;$i<$nFelder;$i++){
   $aW[$i]=''; $aV[$i]=''; $aOs[$i]='';
   if($aFT[$i]!='b'&&$aFT[$i]!='f'){$aOh[$i]=''; $aOa[$i]='';}
   else{$aOh[$i]=isset($_POST['mp_Oh'.$i])?$_POST['mp_Oh'.$i]:''; $aOa[$i]=isset($_POST['mp_Oa'.$i])?$_POST['mp_Oa'.$i]:'';} // mp_Oh: hochgeladene; mp_Oa: alte;
  }
  for($i=1;$i<$nFelder;$i++){
   $s=str_replace('~@~','\n ',stripslashes(@strip_tags(str_replace('\n ','~@~',str_replace("\r",'',trim($_POST['mp_F'.$i]))))));
   $t=$aFT[$i];
   if(strlen($s)>0||!$aPF[$i]||$t=='b'||$t=='f'||$t=='@'){
    if($t!='m') $s=str_replace('"',"'",$s); if($bUtf8) $s=iconv('UTF-8','ISO-8859-1',$s); $v=$s; // s:Eingabe, v:Speicherwert
    switch($t){
    case 't': case 'm': case 'a': case 'k': case 's': case 'j': case 'v': case 'x': //Text,Memo,Kategorie,Auswahl,Ja/Nein,Nutzer,StreetMap
     break;
    case 'd': //Datum
     if($s) if($v=fMpErzeugeDatum($s)) $s=fMpAnzeigeDatum($v); else $aFehl[$i]=true; break;
    case '@': //EintragsDatum
     $v=date('Y-m-d H:i'); $s=fMpAnzeigeDatum($v).date(' H:i'); break;
    case 'z': //Uhrzeit
     if($s){$a=explode(':',str_replace('.',':',str_replace(',',':',$s))); $s=sprintf('%02d:%02d',$a[0],$a[1]); $v=$s;} break;
    case 'e': case 'c': // e-Mail, Kontakt-e-Mail
     if($s) if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aFehl[$i]=true;
     if(!MP_SQL) $v=fMpEnCode($s); break;
    case 'l': //Link oder e-Mail
     if($p=strpos(strtolower(substr($s,0,7)),'ttp://')){$s=substr($s,$p+6); $v=$s;} break;
    case 'b': //Bild
     if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i];
     $UpNaJS=(isset($_POST['mp_UpNa_'.$i])?fMpDateiname(basename($_POST['mp_UpNa_'.$i])):'');
     $UpNa=(isset($_FILES['mp_Up'.$i])?fMpDateiname(basename($_FILES['mp_Up'.$i]['name'])):'');
     if($UpNa=='blob') $UpNa=$UpNaJS; $UpEx=($UpNaJS?'.jpg':strtolower(strrchr($UpNa,'.')));
     if($UpEx=='.jpg'||$UpEx=='.gif'||$UpEx=='.png'||$UpEx=='.jpeg'){ //neue Datei
      if($_FILES['mp_Up'.$i]['size']<=(1024*MP_BildMaxKByte)||MP_BildMaxKByte<=0){
       if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['mp_Up'.$i]['tmp_name']);
       elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($_FILES['mp_Up'.$i]['tmp_name']);
       elseif($UpEx=='.png')$Src=ImageCreateFromPNG($_FILES['mp_Up'.$i]['tmp_name']);
       if(!empty($Src)){
        if($sAlt=$aOh[$i]){ //alte Uploads weg
         $p=strpos($sAlt,'|'); @unlink(MP_Pfad.'temp/-'.substr($sAlt,0,$p)); @unlink(MP_Pfad.'temp/_'.substr($sAlt,$p+1)); $aOh[$i]='';
        }
        $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $sAlt='#|'.implode('|',$aOh); $sZhl='A';
        if(strpos($sAlt,'|'.$UpBa.$UpEx)){while(strpos($sAlt,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelname
        if($Sx>MP_VorschauBreit||$Sy>MP_VorschauHoch){ //Vorschau verkleinern
         $Dw=min(MP_VorschauBreit,$Sx);
         if($Sx>MP_VorschauBreit) $Dh=round(MP_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>MP_VorschauHoch){$Dw=round(MP_VorschauHoch/$Dh*$Dw); $Dh=MP_VorschauHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         if(@imagejpeg($Dest,MP_Pfad.'temp/-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
         imagedestroy($Dest); unset($Dest);
        }else{
         if(@copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|';
         else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
        }
        if($Sx>MP_BildBreit||$Sy>MP_BildHoch){ //Bild verkleinern
         $Dw=min(MP_BildBreit,$Sx);
         if($Sx>MP_BildBreit) $Dh=round(MP_BildBreit/$Sx*$Sy); else $Dh=$Sy;
         if($Dh>MP_BildHoch){$Dw=round(MP_BildHoch/$Dh*$Dw); $Dh=MP_BildHoch;}
         $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
         ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
         @imagejpeg($Dest,MP_Pfad.'temp/_'.$UpBa.'.jpg');
         $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
        }else{$v.=$UpBa.$UpEx; @copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/_'.$UpBa.$UpEx);}
        imagedestroy($Src); unset($Src); $s=$UpBa.$UpEx; $aOh[$i]=$v;
       }else{$aFehl[$i]=true; $sFehl=str_replace('#',$UpNa,MP_TxBildOeffnen);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',MP_BildMaxKByte,MP_TxBildGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),MP_TxBildTyp);
     }elseif($s>'') if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]!=''){ //hochgeladenes Bild löschen
      $p=strrpos($s,'.'); @unlink(MP_Pfad.'temp/-'.$s); @unlink(MP_Pfad.'temp/_'.$s);
      if(strtolower(substr($s,$p))!='.jpg'){
       @unlink(MP_Pfad.'temp/-'.substr($s,0,$p).'.jpg'); @unlink(MP_Pfad.'temp/_'.substr($s,0,$p).'.jpg');
      }
      $s=''; $v=''; $aOh[$i]='';
     }
     $aOs[$i]=$v; break;
    case 'f': //Datei
     if($aOh[$i]>'') $v=$aOh[$i]; else $v=$aOa[$i];
     $UpNa=(isset($_FILES['mp_Up'.$i])?fMpDateiname(basename($_FILES['mp_Up'.$i]['name'])):''); $UpEx=strtolower(strrchr($UpNa,'.'));
     if($UpEx&&$UpEx!='.php'&&$UpEx!='.php3'&&$UpEx!='.php5'&&$UpEx!='.pl'){
      if($_FILES['mp_Up'.$i]['size']<=(1024*MP_DateiMaxKByte)){
       if($aOh[$i]>''){@unlink(MP_Pfad.'temp/'.$aOh[$i]); $aOh[$i]='';} //alten Upload weg
       $UpBa=substr($UpNa,0,-1*strlen($UpEx)); $u='#|'.implode('|',$aOh); $sZhl='A';
       if(strpos($u,'|'.$UpBa.$UpEx)){while(strpos($u,'|'.$UpBa.$sZhl.$UpEx)) $sZhl++; $UpBa.=$sZhl;} //Doppelnamen
       if(@copy($_FILES['mp_Up'.$i]['tmp_name'],MP_Pfad.'temp/'.$UpBa.$UpEx)){$s=$UpBa.$UpEx; $v=$s; $aOh[$i]=$v;}
       else{$aFehl[$i]=true; $sFehl=str_replace('#','<i>temp/'.$UpNa.'</i>',MP_TxDateiRechte);}
      }else{$aFehl[$i]=true; $sFehl=str_replace('#',MP_DateiMaxKByte,MP_TxDateiGroesse);}
     }elseif(substr($UpEx,0,1)=='.'){ //falsche Endung
      $aFehl[$i]=true; $sFehl=str_replace('#',substr($UpEx,1),MP_TxDateiTyp);
     }elseif($s>'') if(isset($_POST['mp_Dl'.$i])&&$_POST['mp_Dl'.$i]!=''){ //hochgeladene Datei löschen
      @unlink(MP_Pfad.'temp/'.$s); $s=''; $v=''; $aOh[$i]='';
     }
     $aOs[$i]=$v; break;
    case 'w': //Währung
     $v=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),MP_Dezimalstellen,'.','');
     $s=number_format((float)$v,MP_Dezimalstellen,MP_Dezimalzeichen,''); break;
    case 'n': case '1': case '2': case '3': //Zahl
     $v=number_format((float)str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s)),(int)$t,'.','');
     $s=number_format((float)$v,(int)$t,MP_Dezimalzeichen,''); break;
    case 'r': //Zahl
     $v=str_replace(MP_Dezimalzeichen,'.',str_replace(MP_Tausendzeichen,'',$s));
     $s=str_replace('.',MP_Dezimalzeichen,$v); break;
    case 'o': //PLZ
     if($s) if(MP_PLZLaenge>0&&strlen($s)!=MP_PLZLaenge) $aFehl[$i]=true; break;
    case 'u': //Benutzernummer
     if(strlen($s)) $s=sprintf('%04d',$s); $v=$s; break;
    case 'p': $v=fMpEnCode($s); break; //Passwort
    }
    $aW[$i]=$s;
    if($bSelf){ // selbes Segment
     if(!MP_SQL) $sZ.=';'.str_replace(NL,'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
     else{$sZ.=',"'.str_replace(NL,"\r\n",str_replace('\n ',NL,str_replace('"','\"',$v))).'"'; $sF.=',mp_'.$i;}
    }else{ // anderes Segment
     if(!MP_SQL) $aV[$i]=str_replace(NL,'\n ',str_replace("\r",'',str_replace(';','`,',$v)));
     else $aV[$i]=str_replace(NL,"\r\n",str_replace('\n ',NL,str_replace('"','\"',$v)));
    }
   }else{$aFehl[$i]=true; if(!MP_SQL) $sZ.=';';}
  }

  if(!$bSelf){ // anderes Segment
   $nDFelder=0; $aDStru=array(); $aDFN=array(); $aDFT=array(); $aDDF=array(); $aDND=array(); //Zielstruktur holen
   $aDPF=array(); $aDTZ=array(); $aDET=array(); $aDAW=array(); $aDKW=array(); $aDSW=array();
   if(!MP_SQL){ //Text
    $aDStru=file(MP_Pfad.MP_Daten.$sDestSegNo.MP_Struktur); fMpEntpackeDStruktur(); $nDFelder=count($aDFN);
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nDestSegNo.'"')){
     $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
     if($i==1){$aDStru=explode("\n",$a[1]); fMpEntpackeDStruktur(); $nDFelder=count($aDFN);}
   }}
   $sZ=''; $sF=''; if(!MP_SQL) $sZ.=';'.$aV[1]; else{$sZ.=',"'.$aV[1].'"'; $sF.=',mp_1';} $bOK=false;
   for($i=2;$i<$nDFelder;$i++)
    if(($j=array_search($aDFN[$i],$aFN))&&$aDFT[$i]==$aFT[$j]){ // passendes Feld gefunden
     if(!MP_SQL) $sZ.=';'.$aV[$j]; else{$sZ.=',"'.$aV[$j].'"'; $sF.=',mp_'.$i;} $bOK=true;
    }else{if(!MP_SQL) $sZ.=';'; else{$sZ.=',""'; $sF.=',mp_'.$i;}}
   if(!$bOK) if(!$sFehl) $sFehl=MP_TxKeinKopieFeld; $bOK=false;
  }

  if(empty($sFehl)){ //alles OK, eintragen
   if(count($aFehl)==0){
    $aLsch=array(); if(!$sRefDat=@date('Y-m-d',time()-86400*MP_HalteAltesNochTage)) $sRefDat='';
    if(!MP_SQL){ //ohne SQL
     $aD=file(MP_Pfad.MP_Daten.$sDestSegNo.MP_Inserate); $nSaetze=count($aD); $aTmp=array();
     $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     for($i=1;$i<$nSaetze;$i++){
      $s=$aD[$i]; $p=strpos($s,';'); $nNr=(int)substr($s,0,$p); $nId=max($nId,$nNr);
      if(substr($s,$p+3,10)>=$sRefDat) $aTmp[$nNr.substr($s,$p,2)]=substr(rtrim($s),$p+3);
      else $aLsch[(int)($nNr.$sDestSegNo)]=true;
     }
     $aD=array(); $aTmp[(++$nId).';'.$sOnl]=substr($sZ,1); //Inserat anhaengen
     $s='Nummer_'.$nId.';online'; for($i=1;$i<($bSelf?$nFelder:$nDFelder);$i++) $s.=';'.($bSelf?$aFN[$i]:$aDFN[$i]); $aD[0]=$s.NL;
     asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v) $aD[]=$k.';'.$v.NL;
     if($f=@fopen(MP_Pfad.MP_Daten.$sDestSegNo.MP_Inserate,'w')){//neu schreiben
      fwrite($f,rtrim(implode('',$aD)).NL); fclose($f); $bOK=true;
     }else $Meld=str_replace('#','<i>'.MP_Daten.$sDestSegNo.MP_Inserate.'</i>',MP_TxDateiRechte);
    }elseif($DbO){ //bei SQL
     if($DbO->query('INSERT IGNORE INTO '.str_replace('%',$sDestSegNo,MP_SqlTabI).' (online'.$sF.') VALUES("'.$sOnl.'"'.$sZ.')')){
      if($nId=$DbO->insert_id){
       $bOK=true;
       if($rR=$DbO->query('SELECT nr FROM '.str_replace('%',$sDestSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"')){
        while($a=$rR->fetch_row()) $aLsch[(int)($a[0].$sDestSegNo)]=true; $rR->close();
        $DbO->query('DELETE FROM '.str_replace('%',$sDestSegNo,MP_SqlTabI).' WHERE mp_1<"'.$sRefDat.'"');
       }
      }else $Meld=MP_TxSqlEinfg;
     }else $Meld=MP_TxSqlEinfg;
    }//SQL
    if($bOK){ //Daten gespeichert
     $Meld=MP_TxEingabeErfo; $MTyp='Erfo';
     if(count($aLsch)>0&&(in_array('b',$aFT)||in_array('f',$aFT))){ //veraltete Bilder und Dateien
      if($f=opendir(MP_Pfad.substr(MP_Bilder.$sDestBldDir,0,-1))){$aD=array();
       while($s=readdir($f)) if($i=(int)$s){
        if(MP_BldTrennen){if(isset($aLsch[$i])) $aD[]=$s;}
        elseif(substr($i,-2)==$sDestSegNo) if(isset($aLsch[(int)substr($i,0,-2)])) $aD[]=$s;
       }
       closedir($f); foreach($aD as $s) @unlink(MP_Pfad.MP_Bilder.$sDestBldDir.$s);
      }
     }
     for($i=1;$i<$nFelder;$i++){
      if(isset($aOh[$i])&&($UpNa=$aOh[$i])){ //neue Bilder und Dateien umspeichern
       if($aFT[$i]=='b'){
        $p=strpos($UpNa,'|'); $UpNa=substr($UpNa,0,$p);
        if(!@copy(MP_Pfad.'temp/-'.$UpNa,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'-'.$UpNa)) $bOK=false;
        @unlink(MP_Pfad.'temp/-'.$UpNa); $UpNa=substr($aOh[$i],$p+1);
        if(!@copy(MP_Pfad.'temp/_'.$UpNa,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'_'.$UpNa)) $bOK=false;
        @unlink(MP_Pfad.'temp/_'.$UpNa);
       }elseif($aFT[$i]=='f'){
        if(!@copy(MP_Pfad.'temp/'. $UpNa,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'~'.$UpNa)) $bOK=false;
        @unlink(MP_Pfad.'temp/'.$UpNa);
       }
      }elseif(($sAlt=$aOs[$i])&&$aOs[$i]==$aOa[$i]){ //alte Bilder und Dateien kopieren
       if($aFT[$i]=='b'){
        $p=strpos($sAlt,'|'); $UpNa=substr($sAlt,0,$p);
        if(!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$UpNa,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'-'.$UpNa)) $bOK=false;
        $UpNa=substr($sAlt,$p+1);
        if(!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sId.$sBldSeg.'_'.$UpNa,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'_'.$UpNa)) $bOK=false;
       }elseif($aFT[$i]=='f'){
        if(!@copy(MP_Pfad.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$sAlt,MP_Pfad.MP_Bilder.$sDestBldDir.$nId.$sDestBldSeg.'~'.$sAlt)) $bOK=false; $UpNa=$sAlt;
       }
      }
      if(!$bOK) $Meld.='</p><p class="admFehl">'.str_replace('#','<i>'.MP_Bilder.$sDestBldDir.$UpNa.'</i>',MP_TxDateiRechte); $bOK=true;
     }
     if(MP_MailListeAdmEint&&MP_MailListeAdr!=''){ //an Liste
      $sNutzerMailing='';
      if(($p=array_search('u',$aFT))&&$aW[$p]>'0000'){ //Benutzerdaten suchen
       $nNId=(int)$aW[$p];
       if(!MP_SQL){
        $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $p=strlen($s);
        for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){
         $aN=explode(';',rtrim($aD[$i])); array_splice($aN,1,1);
         if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld]))
          $sNutzerMailing=MP_NutzerMailListeFeld<5&&MP_NutzerMailListeFeld>1?fMpDeCode($aN[MP_NutzerMailListeFeld]):$aN[MP_NutzerMailListeFeld];
         break;
       }}elseif($DbO){ //SQL
        if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nNId.'"')){
         if($rR->num_rows>0){
          $aN=$rR->fetch_row(); array_splice($aN,1,1);
          if(MP_NutzerMailListeFeld&&isset($aN[MP_NutzerMailListeFeld])) $sNutzerMailing=$aN[MP_NutzerMailListeFeld];
         }$rR->close();
      }}}elseif($p>0&&$aW[$p]=='0000'){$sNutzerMailing=MP_TxAutor0000;}
      $aFL=MP_MailListeNDetail?$aND:$aDF;
      $sMlTxL=strtoupper(MP_TxNummer).': '.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$nId);
      for($i=1;$i<$nFelder;$i++){$t=$aFT[$i];
       if($aFL[$i]&&$t!='c'&&$t!='e'&&$t!='p') $sMlTxL.="\n".strtoupper($aFN[$i]).': '.trim(fMpPlainText($aW[$i],$t,true).($t!='u'?'':' '.$sNutzerMailing));
      }
      if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo(MP_MailListeAdr);
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,MP_TxMailListeBtr);
      $s=MP_KeineAntwort; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetReplyTo($s,$t); if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTxL,str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('\n ',"\n",MP_TxMailListeTxt))));
      $Mailer->Send();
     }
    }//bOK
   }else $Meld=MP_TxEingabeFehl;
  }else $Meld=$sFehl;
 }//POST
 if(!$Meld){$Meld='Kopieren und erneuern Sie das Inserat im Segment <i>'.$sSegNam.'</i> oder in einem anderen Segment.'; $MTyp='Meld';}
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL; $nBreit=12;
 for($i=1;$i<$nFelder;$i++) $nBreit=max($nBreit,strlen($aFN[$i]));
 if($nBreit>25) $nBreit=25; $nBreit=round(0.65*$nBreit,0);

 $aS=explode(';',MP_Segmente); $aA=explode(';',MP_Anordnung); asort($aA); reset($aA); $sSegOpt='';
 foreach($aA as $k=>$v) if($v>0&&isset($aS[$k])&&$aS[$k]!='LEER') $sSegOpt.='<option value="'.$k.($k!=$nDestSegNo?'':'" selected="selected').'">'.(substr($aS[$k],0,1)!='~'&&substr($aS[$k],0,1)!='*'?$aS[$k]:substr($aS[$k],1)).'</option>';
?>

<form name="mpEingabe" action="kopieren.php?seg=<?php echo $nSegNo?>" onsubmit="return formSend()" enctype="multipart/form-data" method="post"><input type="hidden" name="mp_Dmy" value="xx" />
<input type="hidden" name="mp_Num" value="<?php echo $sId?>" />
<input type="hidden" name="mp_Qry" value="<?php echo $sQ?>" />
<script type="text/javascript">
 var sCharSet=document.inputEncoding.toUpperCase(); var sUtf8="0";
 if(sCharSet.indexOf("UNI")>=0 || sCharSet.indexOf("UTF")>=0) sUtf8="1";
 document.writeln('<input type="hidden" name="mp_Utf8" value="'+sUtf8+'" />');
</script>
<table class="admTabl" style="table-layout:fixed;width:100%;" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1" style="width:<?php echo $nBreit?>em">Zielsegment</td>
 <td><select class="" style="" name="mp_Segment" size="1"><?php echo $sSegOpt?></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="width:<?php echo $nBreit?>em">Status</td>
 <td><input class="admRadio" type="radio" name="mp_Onl" value="0"<?php if($sOnl<'1') echo ' checked="checked"'?> /> offline &nbsp;
  <input class="admRadio" type="radio" name="mp_Onl" value="1"<?php if($sOnl=='1') echo ' checked="checked"'?> /> online</td>
</tr>
<?php
 for($i=1;$i<$nFelder;$i++){
  echo NL.' <tr class="admTabl">';
  echo NL.'  <td class="admSpa1" style="width:'.$nBreit.'em"><div id="mpLabel'.$i.'">'.$aFN[$i].($aPF[$i]?'*':'').'</div></td>'; //Feldname
  echo NL.'  <td>'; $sZ=NL.'   <div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="admFehl"':'').'>';
  $t=$aFT[$i]; $v=isset($aW[$i])?str_replace('`,',';',$aW[$i]):''; //Feldinhalt
  switch($t){
  case 't': case 'e': case 'c': //Text, e-Mail, Kontakt
   if($t=='t') $v=str_replace(NL,'\n ',str_replace("\r",'',$v));
   echo $sZ.'<input class="admEing" style="width:99%" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="255" /></div>';
   break;
  case 'm': //Memo
   if(MP_FormatCode) echo NL.'   <div title="'.MP_TxBB_X.'">'.NL.fMpBBToolbar('mp_F'.$i).NL; else echo NL.'   <div>';
   echo $sZ.'<textarea class="admEing" name="mp_F'.$i.'" cols="80" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onfocus="initInsertions('."'mp_F".$i."'".');">'.$v.'</textarea></div>'.NL.'   </div>';
   break;
  case 'a': //Aufzaehlung
   $aHlp=isset($aAW[$i])?explode('|','|'.$aAW[$i]):array(''); $nW=15; $sO=''; foreach($aHlp as $w){$sO.='<option value="'.$w.'"'.($v==$w?' selected="selected"':'').'>'.$w.'</option>'; $nW=max(strlen($w),$nW);}
   echo $sZ.'<select class="admEing" style="width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 'k': //Kategorie
   reset($aKW); $sO=''; $nW=15; foreach($aKW as $w){$sO.='<option value="'.$w.'"'.($v==$w?' selected="selected"':'').'>'.$w.'</option>'; $nW=max(strlen($w),$nW);}
   echo $sZ.'<select class="admEing" style="width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 's': //Symbol
   reset($aSW); $sO=''; $nW=15; foreach($aSW as $w){$sO.='<option value="'.$w.'"'.($v==$w?' selected="selected"':'').'>'.$w.'</option>'; $nW=max(strlen($w),$nW);}
   echo $sZ.'<select class="admEing" style="width:'.(ceil($nW*0.8)).'em;" name="mp_F'.$i.'" size="1">'.$sO.'</select></div>';
   break;
  case 'd': //Datum
   echo $sZ.'<input class="admEing" style="width:7em;" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="10" /> <span class="admMini">'.MP_TxFormat.' '.fMpDatumsFormat().'</span></div>';
   break;
  case '@': echo $sZ.$v.'</div>'; break; //EintragsDatum
  case 'z': //Zeit
   echo $sZ.'<input class="admEing" style="width:7em;" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="5" /> <span class="admMini">'.MP_TxFormat.' '.MP_TxSymbUhr.'</span></div>';
   break;
  case 'l': //Link
   echo $sZ.'<div title="Format:  Adresse  oder  Adresse|Linktext  oder  Adresse|Linktext|Target "><input class="admEing" style="width:99%" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="255" /></div></div>';
   break;
  case 'j': case 'v': //Ja/Nein
   echo $sZ.'<input class="admRadio" type="radio" name="mp_F'.$i.'" value="J"'.($v!='J'?'':' checked="checked"').' /> '.MP_TxJa.' &nbsp; <input class="admRadio" type="radio" name="mp_F'.$i.'" value="N"'.($v!='N'?'':' checked="checked"').' /> '.MP_TxNein.'</div>';
   break;
  case 'w': //Waehrung
   echo $sZ.'<input class="admEing" style="width:7em;" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="16" /> '.MP_Waehrung.'</div>';
   break;
  case 'n': case 'r': case '1': case '2': case '3': case 'o': //Zahlen
   echo $sZ.'<input class="admEing" style="width:7em;" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="16" />'.($t!='o'?'':' <span class="admMini">'.(MP_PLZLaenge>0?MP_PLZLaenge.' '.MP_TxStellen:'').'</span>').'</div>';
   break;
  case 'b': //Bild
   echo $sZ.'<input class="admEing" style="width:99%" size="80" type="file" name="mp_Up'.$i.'" onchange="loadImgFile(this)" accept="image/jpeg, image/png, image/gif" /><input type="hidden" name="mp_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>'; $bMitBild=true;
   if($v) echo NL.'   <div style="float:left;"><input class="admCheck" type="checkbox" name="mp_Dl'.$i.'" value="1" /><input type="hidden" name="mp_F'.$i.'" value="'.$v.'" /><input type="hidden" name="mp_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="admMini">'.$v.' '.MP_TxLoeschen.'</span></div>';
   echo NL.'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="admMini">'.(MP_BildMaxKByte>0?'(max. '.MP_BildMaxKByte.' KByte)':'&nbsp;').'</span></div>';
   break;
  case 'f': //Datei
   echo $sZ.'<input class="admEing" style="width:99%" size="80" type="file" name="mp_Up'.$i.'" onchange="loadDatFile(this)" /><input type="hidden" name="mp_Oa'.$i.'" value="'.(isset($aOa[$i])?$aOa[$i]:'').'" /></div>';
   if($v) echo NL.'   <div style="float:left;"><input class="admCheck" type="checkbox" name="mp_Dl'.$i.'" value="1" /><input type="hidden" name="mp_F'.$i.'" value="'.$v.'" /><input type="hidden" name="mp_Oh'.$i.'" value="'.(isset($aOh[$i])?$aOh[$i]:'').'" /> <span class="admMini">'.$v.' '.MP_TxLoeschen.'</span></div>';
   echo NL.'   <div style="text-align:right;padding:1px;line-height:1.4em;"><span class="admMini">(max. '.MP_DateiMaxKByte.' KByte)</span></div>';
   break;
  case 'x':
   echo $sZ.'<div style="text-align:right;float:right;padding-top:2px;"><a href="'.MPPFAD.(MP_GMapSource=='O'?'openstreet':'google').'map.php?'.$i.($v?','.$v:'').'" target="geown" onclick="geoWin(this.href);return false;"><img src="iconAendern.gif" width="12" height="13" border="0" title="Koordinaten bearbeiten" alt="Koordinaten bearbeiten"></a></div><div style="margin-right:18px;"><input class="admEing" style="width:99%" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="255" /></div></div>';
   break;
  case 'u': // Benutzername
   echo $sZ.'<input class="admEing" style="width:7em;" type="text" name="mp_F'.$i.'" value="'.$v.'" maxlength="16" /> <span class="admMini">'.MP_TxNutzerNr.' 0000 für Administrator</span></div>';
   break;
  case 'p': // Passwort
   echo $sZ.'<input class="admEing" style="width:12em;" type="password" name="mp_F'.$i.'" value="'.$v.'" maxlength="16" /> <span class="admMini">'.MP_TxPassRegel.'</span></div>';
   break;
  }
  if(isset($aET[$i])&&($v=$aET[$i])&&$i>1) echo NL.'   <div><span class="admMini">'.str_replace('`,',';',$v).'</span></div>'; // EingabeHilfsText
  echo NL.'  </td>'.NL.' </tr>';
 }
 //Pflichtfeldzeile
 echo NL.' <tr class="admTabl"><td class="admMini">&nbsp;</td><td class="admMini" style="text-align:right;">* <span class="admMini">'.MP_TxPflicht.'</span></td></tr>';
?>

</table>
<p class="admSubmit"><?php if(!$bOK){?><input class="admSubmit" type="submit" value="Eintragen"><?php }?></p>
</form>

<?php
 if(file_exists('liste.php')) echo '<p class="admSubmit">[ <a href="liste.php?'.$sQ.'">zurück zur Liste</a> ]</p>'.NL.NL;
}else echo '<p class="admMeld">Im leeren Muster-Segment gibt es keine Inserate. Bitte wählen Sie zuerst ein Segment.</p>';

if($bMitBild && MP_BildResize){
 echo "\n".'<script src="'.$sHttp.'mpEingabeBild.js" type="text/javascript"></script>';
 echo "\n".'<script type="text/javascript">';
 echo "\n".' sPostURL="kopieren.php?seg='.$nSegNo.'";';
 echo "\n".' nBildBreit='.MP_BildBreit.'; nBildHoch='.MP_BildHoch.';';
 echo "\n".' nThumbBreit='.MP_ThumbBreit.'; nThumbHoch='.MP_ThumbHoch.';';
 echo "\n".'</script>'."\n";
}else{
 echo "\n".'<script type="text/javascript">';
 echo "\n".' function formSend(){return true;} // normales Senden ohne Bilder;';
 echo "\n".' function loadDatFile(inputField){return false;}';
 echo "\n".' function loadImgFile(inputField){return false;}';
 echo "\n".'</script>'."\n";
}

echo fSeitenFuss();

function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aDF,$aND,$aPF,$aTZ,$aET,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]='1';
 $aND=explode(';',rtrim($aStru[8])); $aND[0]='1';
 $aPF=explode(';',rtrim($aStru[13])); $aPF[0]='1'; $aPF[1]='1';
 $aTZ=explode(';',rtrim($aStru[14])); $aTZ[0]='0';
 $aET=explode(';',rtrim($aStru[15])); $aET[0]='';  //$aET[1]='';
 $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}
function fMpEntpackeDStruktur(){//Struktur interpretieren
 global $aDStru,$aDFN,$aDFT,$aDDF,$aDND,$aDPF,$aDTZ,$aDET,$aDAW,$aDKW,$aDSW;
 $aDFN=explode(';',rtrim($aDStru[0])); $aDFN[0]=substr($aDFN[0],14); if(empty($aDFN[0])) $aDFN[0]=MP_TxFld0Nam; if(empty($aDFN[1])) $aDFN[1]=MP_TxFld1Nam;
 $aDFT=explode(';',rtrim($aDStru[1])); $aDFT[0]='i'; $aDFT[1]='d';
 $aDDF=explode(';',rtrim($aDStru[7])); $aDDF[0]='1';
 $aDND=explode(';',rtrim($aDStru[8])); $aDND[0]='1';
 $aDPF=explode(';',rtrim($aDStru[13])); $aDPF[0]='1'; $aDPF[1]='1';
 $aDTZ=explode(';',rtrim($aDStru[14])); $aDTZ[0]='0';
 $aDET=explode(';',rtrim($aDStru[15])); $aDET[0]='';  //$aDET[1]='';
 $aDAW=explode(';',str_replace('/n/','\n ',rtrim($aDStru[16]))); $aDAW[0]=''; $aDAW[1]='';
 $s=rtrim($aDStru[17]); if(strlen($s)>14) $aDKW=explode(';',substr_replace($s,';',14,0)); $aDKW[0]='';
 $s=rtrim($aDStru[18]); if(strlen($s)>14) $aDSW=explode(';',substr_replace($s,';',14,0)); $aDSW[0]='';
 return true;
}

function fMpDateiname($s){
 $s=str_replace('Ä','Ae',str_replace('Ö','Oe',str_replace('Ü','Ue',str_replace('ß','ss',str_replace('ä','ae',str_replace('ö','oe',str_replace('ü','ue',$s)))))));
 $s=str_replace('Ã„','Ae',str_replace('Ã–','Oe',str_replace('Ãœ','Ue',str_replace('ÃŸ','ss',str_replace('Ã¤','ae',str_replace('Ã¶','oe',str_replace('Ã¼','ue',$s)))))));
 return str_replace('ï¿½','_',str_replace('%','_',str_replace('&','_',str_replace('=','_',str_replace('+','_',str_replace(' ','_',$s))))));
}

function fMpPlainText($s,$t,$bMemo=false){
 if($s) switch($t){
  case 'm':  //Memo
   if(MP_BenachrMitMemo||$bMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'd': if($s!='..') $s=$s; /* fMpAnzeigeDatum($s); */ else $s=''; break;
  case '@': $s=$s; /* fMpAnzeigeDatum($s).substr($s,10); */ break;
  case 'l': case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}

function fMpBBToolbar($Element){
 $sElNr=substr($Element,strpos($Element,'_'));
 $X =NL.'<table class="admTool" border="0" cellpadding="0" cellspacing="0">';
 $X.=NL.' <tr>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Bold',  "'[b]','[/b]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Italic',"'[i]','[/i]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Uline', "'[u]','[/u]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Center',"'[center]','[/center]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Right', "'[right]','[/right]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Enum',  "'[list]','[/list]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Number',"'[list=o]','[/list]'").'</td>';
 $X.=NL.'  <td>'.fDrawToolBtn($Element,'Link',  "'[url]','[/url]'").'</td>';
 $X.=NL.'  <td><img class="admTool" src="'.MPPFAD.'grafik/tbColor.gif" style="margin-right:0;cursor:default;" title="'.MP_TxBB_O.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="mp_Col'.$sElNr.'" onchange="bbTag('."'".$Element."','[color='+this.form.mp_Col".$sElNr.".options[this.form.mp_Col".$sElNr.".selectedIndex].value+']','[/color]'".');this.form.mp_Col'.$sElNr.'.selectedIndex=0;" title="'.MP_TxBB_O.'">
    <option value=""></option>
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
 $X.=NL.'  <td><img class="admTool" src="'.MPPFAD.'grafik/tbSize.gif" style="margin-right:0;cursor:default;" title="'.MP_TxBB_S.'" /></td>';
 $X.=NL.'  <td>
   <select class="admTool" name="mp_Siz'.$sElNr.'" onchange="bbTag('."'".$Element."','[size='+this.form.mp_Siz".$sElNr.".options[this.form.mp_Siz".$sElNr.".selectedIndex].value+']','[/size]'".');this.form.mp_Siz'.$sElNr.'.selectedIndex=0;" title="'.MP_TxBB_S.'">
    <option value=""></option>
    <option value="80">&nbsp;80%</option>
    <option value="90">&nbsp;90%</option>
    <option value="110">110%</option>
    <option value="120">120%</option>
   </select>
  </td>';
 $X.=NL.' </tr>';
 $X.=NL.'</table>'.NL;
 return $X;
}
function fDrawToolBtn($Element,$vImg,$nTag){
 return '<img class="admTool" src="'.MPPFAD.'grafik/tb'.$vImg.'.gif" onClick="bbTag('."'".$Element."'".','.$nTag.')" style="background-image:url('.MPPFAD.'grafik/tool.gif);" title="'.constant('MP_TxBB_'.substr($vImg,0,1)).'" />';
}
?>