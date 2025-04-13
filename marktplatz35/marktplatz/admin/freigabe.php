<?php
if(file_exists('./mpWerte.php')){include './mpWerte.php'; define('IFront',true);} else if(file_exists('../mpWerte.php')) include '../mpWerte.php'; if(!defined('IFront')) define('IFront',false);
header('Content-Type: text/html; charset='.(defined('MP_Zeichensatz')&&MP_Zeichensatz!=2||!IFront?'ISO-8859-1':'utf-8'));
if(defined('MP_TimeZoneSet')&&strlen(MP_TimeZoneSet)>0) date_default_timezone_set(MP_TimeZoneSet); else date_default_timezone_set('Europe/Berlin');
if(!defined('MP_Url')) define('MP_Url','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(defined('MP_Www')?MP_Www:'localhost'));
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Marktplatz-Script - Inseratevorschlag</title>
<style type="text/css">
body{font-size:100.1%;font-family:Verdana,Arial,Helvetica;}
p,div,table,td,th,li,a,input,textarea,select{font-family:Verdana,Arial,Helvetica;}
div#kopf{font-size:120%;text-align:center;height:32px;margin-bottom:32px;color:#ffffff;background-color:#7777aa;}
p.admMeld{color:#000000;font-weight:bold;text-align:center;}
p.admErfo{color:#008000;font-weight:bold;text-align:center;}
p.admFehl{color:#CC0000;font-weight:bold;text-align:center;}
input[type=submit]{width:160px;}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo MP_Url?>mpStyles.css">
</head>

<body>
<div id="kopf">Marktplatz-Script:: Inseratevorschlag behandeln</div>
<?php
$DbO=NULL; $sMTxt='???'; $sMTyp='Fehl'; $sBtn='??'; $sSta='?'; $X='';
if(defined('MP_Version')){
 if(MP_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
  if(!mysqli_connect_errno()){if(IFront&&MP_SqlCharSet||!IFront&&AM_SqlZs) $DbO->set_charset(IFront&&MP_SqlCharSet?MP_SqlCharSet:AM_SqlZs);}else $DbO=NULL;
 }
 $sId=(isset($_GET['i'])?$_GET['i']:(isset($_POST['id'])?$_POST['id']:'0')); $nCod=(isset($_GET['c'])?(int)$_GET['c']:(isset($_POST['cod'])?(int)$_POST['cod']:0));
 if($sId>'0'&&$nCod!=0){
  if($nCod==fMCod($sId)){
   $sSegNo=substr($sId,0,2); $sId=substr($sId,2); $aS=explode(';',MP_Segmente); $sSegNam=$aS[(int)$sSegNo];
   //Struktur holen
   global $aMpFN,$aMpFT,$aMpLF,$aMpNL,$aMpOF,$aMpLK,$aMpSS,$aMpDF,$aMpND,$aMpZS,$aMpAW,$aMpKW,$aMpSW;
   $nFelder=0; $aStru=array(); $nListenIndex=1;
   $aMpFN=array(); $aMpFT=array(); $aMpLF=array(); $aMpNL=array(); $aMpOF=array(); $aMpLK=array(); $aMpSS=array();
   $aMpDF=array(); $aMpND=array(); $aMpZS=array(); $aMpAW=array(); $aMpKW=array(); $aMpSW=array();
   if(!MP_SQL){ //Text
    if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)) $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); else $aStru=array();
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$sSegNo.'"')){
     $a=$rR->fetch_row(); if($rR->num_rows==1) $aStru=explode("\n",$a[1]); $rR->close();
    }else $Meld=MP_TxSqlFrage;
   }else $Meld=MP_TxSqlVrbdg;
   if(count($aStru)>1){
    $aMpFN=explode(';',rtrim($aStru[0])); $aMpFN[0]=substr($aMpFN[0],14); $nFelder=count($aMpFN);
    if(empty($aMpFN[0])) $aMpFN[0]=MP_TxFld0Nam; if(empty($aMpFN[1])) $aMpFN[1]=MP_TxFld1Nam;
    $aMpFT=explode(';',rtrim($aStru[1])); $nListenIndex=substr($aMpFT[0],14); $aMpFT[0]='i'; $aMpFT[1]='d';
    $aMpLF=explode(';',rtrim($aStru[2])); $aMpLF[0]=substr($aMpLF[0],14,1);
    $aMpNL=explode(';',rtrim($aStru[3])); $aMpNL[0]=substr($aMpNL[0],14,1);
    $aMpOF=explode(';',rtrim($aStru[4])); $aMpOF[0]=substr($aMpOF[0],14,1); $aMpOF[1]='1';
    $aMpLK=explode(';',rtrim($aStru[5])); $aMpLK[0]=substr($aMpLK[0],14,1);
    $aMpSS=explode(';',rtrim($aStru[6])); $aMpSS[0]='';
    $aMpDF=explode(';',rtrim($aStru[7])); $aMpDF[0]=substr($aMpDF[0],14,1);
    $aMpND=explode(';',rtrim($aStru[8])); $aMpND[0]=substr($aMpND[0],14,1);
    $aMpZS=explode(';',rtrim($aStru[9])); $aMpZS[0]='';
    $aMpAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aMpAW[0]=''; $aMpAW[1]='';
    $s=rtrim($aStru[17]); if(strlen($s)>14) $aMpKW=explode(';',substr_replace($s,';',14,0)); $aMpKW[0]='';
    $s=rtrim($aStru[18]); if(strlen($s)>14) $aMpSW=explode(';',substr_replace($s,';',14,0)); $aMpSW[0]='';
   }

   $a=array(); $nIdx=0; $aDel=array(); $sAutorMlTo=''; $sGMap=''; //ListenDaten
   if(!MP_SQL){ //Textdaten
    $aD=(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate)?file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate):array()); $nSaetze=count($aD); $s=$sId.';'; $l=strlen($s);
    for($i=1;$i<$nSaetze;$i++){ //ueber alle Datensaetze
     if($nIdx==0) if(substr($aD[$i],0,$l)==$s){$a=explode(';',rtrim($aD[$i])); $nIdx=$i;}
    }
    if($nIdx==0) fMMeld('Inserat '.$sId.' im Segment '.$sSegNo.' nicht gefunden!');
   }elseif($DbO){
    if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'"')){
     if(!$a=$rR->fetch_row()){fMMeld('Inserate-Datensatz '.$sId.' im Segment '.$sSegNo.' nicht gefunden!'); $a=array();} $rR->close();
    }else fMMeld('Datenbankabfrage gescheitert!');
   }
   if(count($a)){
    $X=''; $sSta=$a[1]; array_splice($a,1,1); $nNDf=MP_NutzerDetailFeld; $nFarb=1;
    if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
    $X.="\n\n".'<div class="mpTabl" style="width:auto;margin-top:32px;">'; //Tabelle

    for($i=0;$i<$nFelder;$i++){
     $t=$aMpFT[$i];
     if(($aMpDF[$i]>0)&&$t!='p'&&substr($aMpFN[$i],0,5)!='META-'&&$aMpFN[$i]!='TITLE'){
      if(($s=$a[$i])||strlen($s)>0){
       switch($t){
        case 't': $s=fMBB(fMDt($s)); break; //Text
        case 'm': $s=fMBB(fMDt($s)); break; //Memo
        case 'a': case 'k': case 'o': $s=fMDt($s); break; //Aufzaehlung/Kategorie so lassen
        case 'd': case '@': //Datum
         $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
         switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
          case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
          case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
         }
         $s=$s1.$v.$s2.$v.$s3; break;
        case 'z': $s.=' '.fMTx(MP_TxUhr); break; //Uhrzeit
        case 'w': //Waehrung
         if(((float)$s)!=0||!MP_PreisLeer){
          $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
          if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung;
         }else if(MP_ZeigeLeeres) $s='&nbsp;'; else $s='';
         break;
        case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
         if($s=='J'||$s=='Y') $s=fMTx(MP_TxJa); elseif($s=='N') $s=fMTx(MP_TxNein);
         break;
        case 'n': case '1': case '2': case '3': case 'r': //Zahl
         if(((float)$s)!=0||!MP_ZahlLeer){
          if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,MP_Tausendzeichen); else $s=str_replace('.',MP_Dezimalzeichen,$s);
         }else if(MP_ZeigeLeeres) $s='&nbsp;'; else $s='';
         break;
        case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); break; //Zaehlnummer
        case 'l': //Link
         $aI=explode('|',$s); $s=$aI[0];
         $v='<img class="mpIcon" src="'.MP_Url.'grafik/'.(strpos($s,'@')?'mail':'iconLink').'.gif" title="'.fMDt($s).'" alt="'.fMDt($s).'" /> ';
         $s='<a class="mpText" title="'.fMDt($s).'" href="'.(strpos($s,'@')?'mailto:'.$s:(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').fMExtLink($s)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(MP_DetailLinkSymbol?'':fMDt(isset($aI[1])?$aI[1]:$s)).'</a>';
         break;
        case 'e': case 'c': //eMail
         if(!MP_SQL) $s=fMDeCode($s); $sAutorMlTo=$s;
         $s='<img class="mpIcon" src="'.MP_Url.'grafik/mail.gif" title="'.fMTx(MP_TxKontakt).'" alt="'.fMTx(MP_TxKontakt).'" /> '.fMDt($s);
         break;
        case 's': $w=$s; //Symbol
         $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
         $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
         $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.fMDt($w).'" alt="'.fMDt($w).'" />';
         break;
        case 'b': //Bild
         $s=substr($s,strpos($s,'|')+1); $v=$s; $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'_'.$s; if(file_exists(MP_Pfad.$s)){$aI=getimagesize(MP_Pfad.$s); $aDel[]=$sBldDir.$sId.$sBldSeg.'_'.$v; $aDel[]=$sBldDir.$sId.$sBldSeg.'-'.$v;}else $aI=array(0,0,0,''); $w=fMDt(substr($s,strpos($s,'_')+1,-4));
         $s='<img class="mpBild" src="'.MP_Url.$s.'" style="max-width:'.(isset($aI[0])?$aI[0]:'16').'px;max-height:'.(isset($aI[1])?$aI[1]:'16').'px;" title="'.$w.'" alt="'.$w.'" />';
         break;
        case 'f': //Datei
         $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fMDt(strtoupper($w).'-'.MP_TxDatei);
         if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
         $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'" /> ';
         if(!MP_DetailDateiSymbol) $v.=fMKurzName($s); $aDel[]=$sBldDir.$sId.$sBldSeg.'~'.$s;
         $s='<a class="mpText" href="'.MP_Url.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$v.'</a>';
         break;
        case 'u': //Benutzer
         if($nId=(int)$s){
          $s=MP_TxAutorUnbekannt;
          if(!MP_SQL){ //Textdaten
           $aN=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aN); $v=$nId.';'; $p=strlen($v);
           for($j=1;$j<$nSaetze;$j++) if(substr($aN[$j],0,$p)==$v){
            $aN=explode(';',rtrim($aN[$j])); array_splice($aN,1,1); $sAutorMlTo=fMDeCode($aN[4]);
            if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt; elseif($nNDf<5&&$nNDf>1) $s=fMDeCode($s);
            break;
           }
          }elseif($DbO){ //SQL-Daten
           if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' WHERE nr="'.$nId.'"')){
            $aN=$rR->fetch_row(); $rR->close();
            if(is_array($aN)){array_splice($aN,1,1); $sAutorMlTo=$aN[4]; if(!$s=$aN[$nNDf]) $s=MP_TxAutorUnbekannt;}
            else $s=MP_TxAutorUnbekannt;
          }}
         }else $s=MP_TxAutor0000;
         $s=fMDt($s); break;
        case 'x': $aI=explode(',',$s); //StreetMap
         if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
          $s='<div class="mpNorm" id="GGeo'.$i.'" style="width:99%;max-width:'.MP_GMapBreit.'px;height:'.MP_GMapHoch.'px;">'.fMTx(MP_TxGMap1Warten).'<br /><a class="mpText" href="javascript:showMap'.$i.'()" title="'.fMTx($aMpFN[$i]).'">'.fMTx(MP_TxGMap2Warten).'</a></div>';
          $sGMap.=(MP_GMapSource=='O'?fMOMap($i,$aI):fMGMap($i,$aI));
         }else $s='&nbsp;';
         break;
        case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
       }//switch
      }elseif($t=='b'&&MP_ErsatzBildGross>''){ //keinBild
       $s='grafik/'.MP_ErsatzBildGross; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img class="mpBild" src="'.MP_Url.$s.'" style="max-width:'.(isset($aI[0])?$aI[0]:'16').'px;max-height:'.(isset($aI[1])?$aI[1]:'16').'px;" alt="" />';
      }elseif(MP_ZeigeLeeres) $s='&nbsp;';
      if(strlen($s)>0){
       $X.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
       $X.="\n".' <div class="mpTbSp1">'.fMDt($aMpFN[$i]).'</div>';
       $X.="\n".' <div class="mpTbSp2" '.($aMpZS[$i]?' style="'.str_replace('`,',';',$aMpZS[$i]).'"':'').'>'.$s."</div>\n</div>";
      }
     }
    }
    $X.="\n".'</div>'; //Tabelle

    $sAct=(isset($_POST['btn'])?$_POST['btn']:'');
    if($sAct=='Freischalten'){
     if(!MP_SQL){ //Textdaten
      $aD[$nIdx]=substr_replace($aD[$nIdx],'1',strlen($sId)+1,1);
      if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){//neu schreiben
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $sMTyp='Erfo'; fMMeld('Das Inserat wurde online geschaltet!'); $sSta='1';
      }else fMMeld('Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht gespeichert werden!');
     }elseif($DbO){
      if($DbO->query('UPDATE IGNORE '.str_replace('%',$sSegNo,MP_SqlTabI).' SET online="1" WHERE nr="'.$sId.'" LIMIT 1')){
       if($DbO->affected_rows){$sMTyp='Erfo'; fMMeld('Das Inserat wurde online geschaltet!'); $sSta='1';}
      }else fMMeld('Datenbankaktualisierung gescheitert!');
     }
    }elseif($sAct=='Loeschen'){
     if(!MP_SQL){ //Textdaten
      $aD[$nIdx]='';
      if($f=@fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){//neu schreiben
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $sMTyp='Erfo'; fMMeld('Das Inserat wurde gelöscht!'); $sSta='1';
      }else fMMeld('Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht gespeichert werden!');
     }elseif($DbO){
      if($DbO->query('DELETE FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr="'.$sId.'" LIMIT 1')){
       if($DbO->affected_rows){$sMTyp='Erfo'; fMMeld('Das Inserat wurde gelöscht!'); $sSta='1';}
      }else fMMeld('Datenbankaktualisierung gescheitert!');
     }
     if($sMTyp=='Erfo') foreach($aDel as $s) if(file_exists(MP_Pfad.MP_Bilder.$s)) @unlink(MP_Pfad.MP_Bilder.$s); // Bilder/Anhang löeschen
    }
    if($sSta!='1'){
     $sMTyp='Meld'; fMMeld('Das Inserat hat den Status <i>'.($sSta=='2'?'vorgemerkt':'offline').'</i>. Jetzt freischalten oder löschen?');
     $sBtn='Freischalten"> &nbsp; <input type="submit" name="btn" value="Loeschen';
    }else{
     if($sMTyp!='Erfo'){$sMTyp='Meld'; fMMeld('Das Inserat ist bereits online.');}
     elseif(MP_FreischaltMail&&$sAutorMlTo!=''){ //Freischaltmail
      $sMlTxA=strtoupper(MP_TxNummer).': '.$sSegNo.'/'.sprintf('%0'.MP_NummerStellen.'d',$sId);
      if($sAct=='Loeschen') $sMlTxA='Das folgende Inserat wurde vom Webmaster gelöscht!'."\n\n".$sMlTxA;
      for($i=1;$i<$nFelder;$i++){
       $t=$aMpFT[$i]; if(!MP_SQL) if($t=='e'||$t=='c'||$t=='p') $a[$i]=fMDeCode($a[$i]);
       if($a[$i]) $sMlTxA.="\n".strtoupper($aMpFN[$i]).': '.trim(str_replace('`,',';',str_replace('\n ',"\n",$a[$i])));
      }
      if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
      require_once(MP_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sAutorMlTo);
      if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
      $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
      $Mailer->SetFrom($s,$t); $Mailer->Subject=str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),MP_TxFreischaltBtr)));
      if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
      $Mailer->Text=str_replace('#D',$sMlTxA,str_replace('#A',$sWww,str_replace('#S',$sSegNam,str_replace('#N',(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId),str_replace('\n ',"\n",MP_TxFreischaltTxt)))));
      $Mailer->Send();
     }$sBtn='OK';
    }
    if(!empty($sGMap)){ // StreetMap initialisieren
     if(MP_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.MP_Url.'maps/leaflet.css" />'."\n".$X."\n\n".'<script type="text/javascript" src="'.MP_Url.'maps/leaflet.js"></script>';
     else $X.="\n\n".'<script type="text/javascript" src="'.MP_GMapURL.'"></script>';
     $X.="\n".'<script type="text/javascript">'.$sGMap."\n".'</script>';
    }
   } // gefunden
  }else fMMeld('unerlaubter Aufruf!');
 }else fMMeld('unvollständiger Aufruf!');
}else fMMeld('Datei <i>mpWerte.php</i> nicht gefunden!');

function fMMeld($sMTxt){echo "\n".'<p class="adm'.$GLOBALS['sMTyp'].'">'.fMTx($sMTxt).'</p>';}
function fMKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}
function fMCod($s){$c=(int)MP_Schluessel; for($i=strlen($s);$i>=0;--$i) $c+=(int)substr($s,$i,1); return $c;}
function fMTx($sTx){ //TextKodierung
 if(!IFront||MP_Zeichensatz<=0) $s=$sTx; elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
function fMDt($s){ //DatenKodierung
 $mpZeichensatz=MP_ZeichnsNorm;
 if(!IFront||MP_Zeichensatz==$mpZeichensatz){if(MP_Zeichensatz!=1) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));}
 else{
  if($mpZeichensatz!=0) if($mpZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1',$s); else $s=html_entity_decode($s);
  if(MP_Zeichensatz<=0) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));
  elseif(MP_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))))); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br />',$s);
}
function fMDeCode($w){
 $nCod=(int)substr(MP_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fMExtLink($s){
 if(!defined('MP_ZeichnsExtLink')||MP_ZeichnsExtLink==0) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode($s)));
 elseif(MP_ZeichnsExtLink==1) $s=str_replace('%2F','/',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s))));
 elseif(MP_ZeichnsExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}
function fMBB($s){ //BB-Code zu HTML wandeln
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

function fMOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
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

function fMGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(MP_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(MP_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fMTx(MP_TxGMapOrt)."'".'});':'
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
?>

<div style="text-align:center">
<form method="POST">
<input type="hidden" name="id" value="<?php echo $sId ?>" />
<input type="hidden" name="cod" value="<?php echo $nCod ?>" />
<p><input type="submit" name="btn" value="<?php echo $sBtn ?>"></p>
</form>
<?php echo $X?>
</div>
</body>
</html>
