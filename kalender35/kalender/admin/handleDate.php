<?php  header('Content-Type: text/html; charset=ISO-8859-1') ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Kalender-Script - Terminvorschlag</title>
<style type="text/css">
body{font-size:100.1%;font-family:Verdana,Arial,Helvetica;}
p,div,table,td,th,li,a,input,textarea,select{font-size:86%;font-family:Verdana,Arial,Helvetica;}
div#kopf{font-size:120%;text-align:center;height:32px;color:#ffffff;background-color:#7777aa;}
p.admMeld{color:#000000;font-weight:bold;text-align:center;}
p.admErfo{color:#008000;font-weight:bold;text-align:center;}
p.admFehl{color:#CC0000;font-weight:bold;text-align:center;}
input[type=submit]{width:200px;}
</style>
</head>

<body>
<div id="kopf">Kalender-Script:: Terminvorschlag behandeln</div>
<?php
@include '../kalWerte.php'; $DbO=NULL; $sMTxt='???'; global $sMTyp; $sMTyp='Fehl'; $sBtn='??'; $sSta='?';
if(phpversion()>='5.1.0') if(defined('KAL_TimeZoneSet')) if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
if(KAL_SQL){ //SQL-Verbindung oeffnen
 $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
 if(!mysqli_connect_errno()){if(KAL_SqlCharSet||ADM_SqlZs) $DbO->set_charset(ADM_SqlZs?ADM_SqlZs:KAL_SqlCharSet);}else $DbO=NULL;
}
$nId=(isset($_GET['id'])?(int)$_GET['id']:0); $sCod=(isset($_GET['c'])?$_GET['c']:'');
if($nId!=0&&!empty($sCod)){

 //$n=substr(KAL_Schluessel,-3,2);

 $sDat='???'; $aT=array(); $aIds=array(); $aObj=array(); $nTid=0;  $sPC=''; $sZ='';
 $nFelder=count($kal_FeldName);
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$nId.';'; $l=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$l)==$s){ // gefunden
   if($aT=explode(';',rtrim($aD[$i]))){
    $sSta=$aT[1]; array_splice($aT,1,1); $sDat=substr($aT[1],0,10); $sPC=(isset($aT[$nFelder])?$aT[$nFelder]:'');
    for($j=2;$j<$nFelder;$j++){$sZ.=';'.$aT[$j]; if($kal_FeldType[$j]=='b'||$kal_FeldType[$j]=='f') $aObj[$j]=$aT[$j];}
   }
   $nTid=$i; $aD[$nTid]=$aT[0].';1;'.$aT[1].$sZ."\n";
   break;
  }
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$nId.'"')){
   if($aT=$rR->fetch_row()){$sSta=$aT[1]; array_splice($aT,1,1); $sDat=substr($aT[1],0,10); $sPC=(isset($aT[$nFelder])?$aT[$nFelder]:'');}$rR->close();
  }else fKMeld('KAL_TxSqlFrage');
 }else fKMeld('KAL_TxSqlVrbdg');
 if($sSta!='?'){
  if(true||$sSta!='1'){
   if(isset($_GET['del'])){ //loeschen
    if(!KAL_SQL){ //Textdaten
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){
      $aD[$nTid]=''; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
      $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Der Terminvorschlag wurde gelöscht.');
     }else fKMeld('Die Löschung konnte nicht gespeichert werden!');
    }else{
     if($DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE id="'.$nId.'" AND online<>"1" LIMIT 1')){
      $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Der Terminvorschlag wurde gelöscht.');
     }else fKMeld('Die Löschung konnte nicht gespeichert werden!');
    }
    if((in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))&&$sBtn=='OK'){ //Bilder und Dateien weg
     if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
      $aD=array(); while($s=readdir($f)) if($nId==(int)$s) $aD[]=$s; closedir($f);
      foreach($aD as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
    }}
    if($sBtn=='OK'){
     require_once(KAL_Pfad.'class.plainmail.php'); $sKontaktEml=''; $sWww=fKalWww();
     if((strpos(KAL_TxZusageLschMTx,'#D')>0)&&count($aT)>0){ //Termindaten aufbereiten
      $aD=fKalTerminPlainText($aT,$DbO); $sTDat=$aD[0]; $sKontaktEml=$aD[1];
     }
     $sMTx=str_replace('#D',$sTDat,str_replace('\n ',"\n",KAL_TxZusageLschMTx));
     $Mailer=new PlainMail();
     if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
     $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
     $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageLschBtr);
     $Mailer->Text=str_replace('#Z',trim($sZDat),str_replace('#A',$sLnk.$a[1],$sMTx));
     if(KAL_ZusageLschInfoAut&&$sKontaktEml>''){ //Mail an Terminautor
      $Mailer->AddTo($sKontaktEml); $Mailer->Send(); $Mailer->ClearTo();
     }

    }
   }elseif(isset($_GET['set'])){ // freischalten
    if($sPC=(isset($aT[$nFelder])?$aT[$nFelder]:'')){ //Wiederholungen
     if($aWdhDat=fKalWdhDat(substr($aT[1],0,10),$sPC)){ //Wiederholungen
      if(!KAL_SQL){
       $aTmp=array(); $nMxId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nMxId=(int)substr($s,8,strpos($s,';')); //Auto-ID-Nr holen
       for($i=1;$i<$nSaetze;$i++){
        $s=rtrim($aD[$i]); $p=strpos($s,';'); $nANr=(int)substr($s,0,$p); $nMxId=max($nMxId,$nANr);
        $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
       }
       foreach($aWdhDat as $v){$aTmp[(++$nMxId).';1']=$v.$sZ; $aIds[]=$nMxId;}
       $aD=array(); $s='Nummer_'.$nMxId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $aD[0]=$s.";Periodik\n";
       asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v){$aD[]=$k.';'.$v."\n";}
      }else{ //SQL
       $sF=''; for($i=1;$i<$nFelder;$i++) $sF.='kal_'.$i.','; $sF.='periodik';
       for($i=2;$i<$nFelder;$i++){$sZ.='"'.$aT[$i].'",'; if($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f') $aObj[$i]=$aT[$i];}
       foreach($aWdhDat as $v) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online,'.$sF.') VALUES("1","'.$v.'",'.$sZ.'"")')){
        if($sId=$DbO->insert_id) $aIds[]=$sId;
       }
      }
     }
    }
    if(!KAL_SQL){ //Textdaten speichern
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
      $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Die Freischaltung wurde gespeichert.');
     }else fKMeld('Die Freischaltung konnte nicht gespeichert werden!');
    }else{
     if($DbO->query('UPDATE IGNORE '.KAL_SqlTabT.' SET online="1" WHERE id='.$nId)){
      $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Die Freischaltung wurde gespeichert.');
     }else fKMeld('Die Freischaltung konnte nicht gespeichert werden!');
    }
    if($sBtn=='OK'&&count($aIds)>0) for($i=2;$i<$nFelder;$i++){ //Bilder und Dateien kopieren
     if($sONa=(isset($aObj[$i])?$aObj[$i]:'')){
      if($kal_FeldType[$i]=='b'){
       $p=strpos($sONa,'|'); $sONa=substr($sONa,0,$p);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$nId.'-'.$sONa,KAL_Pfad.KAL_Bilder.$j.'-'.$sONa)) $sBtn=' OK ';
       $sONa=substr($aObj[$i],$p+1);
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$nId.'_'.$sONa,KAL_Pfad.KAL_Bilder.$j.'_'.$sONa)) $sBtn=' OK ';
      }elseif($kal_FeldType[$i]=='f'){
       reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$nId.'~'.$sONa,KAL_Pfad.KAL_Bilder.$j.'~'.$sONa)) $sBtn=' OK ';
      }
      if($sBtn!='OK'){$sMTyp='Fehl'; fKMeld('Die Bilder/Anhänge konnten nicht kopiert werden.');}
     }
    }
    //Mails


   }else{ //nachfragen
    $sMTyp='Meld'; fKMeld('Den Termin '.fKNDat($sDat).' jetzt freischalten oder löschen?');
    $sBtn='Termin freischalten" name="set"><br /><br /><br />oder<br /><br /><br /><input type="submit" value="Termin löschen" name="del';
   }
  }else fKMeld('Termin ist bereits freigeschaltet!');
 }else fKMeld('Termin nicht gefunden!');
}else fKMeld('unvollständiger Aufruf!');


function fKMeld($sMTxt){echo "\n".'<p class="adm'.$GLOBALS['sMTyp'].'">'.$sMTxt.'</p><br />';}
function fKNDat($s){return substr($s,8,2).'.'.substr($s,5,2).'.'.substr($s,0,4);}
function fKalKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}

function fKalEnCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fKalDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fKalAnzeigeDatum($w){ //sichtbares Datum
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalWdhDat($sBeg,$sCod){
 $aTmp=explode('|',$sCod); $sTyp=$aTmp[0]; $sEnd=(isset($aTmp[1])?$aTmp[1]:''); $sP1=(isset($aTmp[2])?$aTmp[2]:''); $sP2=(isset($aTmp[3])?(int)$aTmp[3]:0);
 if(strpos($sEnd,'-')>0){// bis Enddatum
  $s=date('Y-m-d',KAL_MaxPeriode*86400+time()); if($sEnd>$s) $sEnd=$s;
  $nMax=3653;
 }else{ // n-Mal
  $nMax=min($sEnd,KAL_MaxWiederhol);
  $sEnd=date('Y-m-d',KAL_MaxPeriode*86400+time());
 }
 $bDo=true; $nAkt=@mktime(12,0,0,substr($sBeg,5,2),substr($sBeg,8,2),substr($sBeg,0,4));
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
    $nAkt+=86400; $n=(int)@date('d',$nAkt);
    while($n!=(int)$sP1&&$n!=(int)$sP2){$nAkt+=86400; $n=(int)@date('d',$nAkt);} $Dat=@date('Y-m-d w',$nAkt);
    break;
   case 'E': //monatlich-2
    do{
     if($sP1<5) $Dat=date('Y-m-',$nAkt).(1+7*($sP1-1)); /* 1...4 */ else $Dat=date('Y-m-t',$nAkt); //5. oder letzter
     $nAkt=@mktime(12,0,0,substr($Dat,5,2),substr($Dat,8,2),substr($Dat,0,4));
     if($sP1<5){while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt+=86400;} //1...4
     else      {while((int)@date('w',$nAkt)!=(int)$sP2) $nAkt-=86400;} //5. oder letzter
     $Dat=@date('Y-m-d w',$nAkt);
     $s=date('Y-m-t',$nAkt); $nAkt=@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))+86400;
     if(substr($Dat,0,10)<=$sBeg) $Dat=''; if($sP1==5) if((int)substr($Dat,8,2)<29) $Dat='';
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
  if(($nMax--)>0&&substr($Dat,0,10)<=$sEnd){if($Dat) $aDat[]=$Dat;} else $bDo=false;
 }
 if(isset($aDat)) return $aDat; else return false;
}

function fKalPlainText($s,$t,$bMemo=false){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||$bMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'd': if($s=='..') $s=''; break;
  case '@': $s=fKalAnzeigeDatum($s).substr($s,10); break;
  case 'l': case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}
//BB-Code zu HTML wandeln
function fKalBB($s){
 $v=str_replace("\n",'<br />',str_replace("\n ",'<br />',str_replace("\r",'',$s))); $p=strpos($v,'[');
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($Tg,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($Tg,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($Tg,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($Tg,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($Tg,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o));} elseif(substr($Tg,0,8)=='[/color]') $v=substr_replace($v,'</span>',$p,8);
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.($o*14+100).'%">',$p,7+strlen($o));} elseif(substr($Tg,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5);
  }elseif(substr($Tg,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6);
  }elseif(substr($Tg,0,7)=='[/link]') $v=substr_replace($v,'</a>',$p,7);
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
 }return $v;
}

function fKalTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if(($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')||$t=='v'){
   if($u=$s){
    switch($t){
     case 't': $s=fKalBB($s); $u=@strip_tags($s); break; //Text
     case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKalBB($s)); else $u=''; break; //Memo
     case 'a': case 'k': case 'o': break;  //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $u=fKalAnzeigeDatum($s); $w=trim(substr($s,11)); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0) if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];
      }else{if($w) $u.=' '.$w;}
      break;
     case 'z': $u=$s.' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $u=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.KAL_Waehrung;
      }else $u='';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); if($s=='J'||$s=='Y') $u=KAL_TxJa; elseif($s=='N') $u=KAL_TxNein; //Ja/Nein
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $u=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $u=str_replace('.',KAL_Dezimalzeichen,$s);
      break;
     case 'e': //E-Mai
      if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
      $u=''; break;
     case 'l': //Link
      $aI=explode('|',$s); $u=$aI[0];
      if($u) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($u))) $sErsatzEml=$u;
      break;
     case 'b': $s=substr($s,0,strpos($s,'|')); $u=KAL_Www.KAL_Bilder.$aT[0].'-'.$s; break; //Bild
     case 'f': $u=KAL_Www.KAL_Bilder.$aT[0].'~'.$s; break; //Datei
     case 'u':
      if($nId=(int)$s){
       $s=KAL_TxAutorUnbekannt;
       if(!KAL_SQL){ //Textdaten
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
        for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
         if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKalDeCode($s);
         break;
        }
       }elseif($DbO){ //SQL-Daten
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
         $aN=$rR->fetch_row(); $rR->close();
         if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4]; if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt;}
         else $s=KAL_TxAutorUnbekannt;
       }}
      }else $s=KAL_TxAutor0000;
      $u=$s; break;
     default: $u='';
    }//switch
   }
   if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
   if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(strlen($u)>0) $sT.="\n".strtoupper($sFN).': '.$u;
  }elseif($t=='c'){if($s) $sKontaktEml=$s;}
  elseif($t=='e'){if($s) $sErsatzEml=$s;}
  elseif($t=='l'){$aI=explode('|',$s); if($s=$aI[0]) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;}
  elseif($t=='u'){
   if($nId=(int)$s){
    if(!KAL_SQL){ //Textdaten
     $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
     for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
      $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKalDeCode($aN[4]);
      break;
     }
    }elseif($DbO){ //SQL-Daten
     if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
      $aN=$rR->fetch_row(); $rR->close(); if(is_array($aN)){array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=$aN[4];}
  }}}}
 }
 if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
 return array($sT,$sKontaktEml);
}

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}
?>

<div style="text-align:center">
<form method="GET">
<input type="hidden" name="id" value="<?php echo $nId ?>" />
<input type="hidden" name="c" value="<?php echo $sCod ?>" />
<input type="submit" value="<?php echo $sBtn ?>">
</form>
</div>
</body>
</html>