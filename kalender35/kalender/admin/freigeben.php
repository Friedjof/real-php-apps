<?php
if(file_exists('./kalWerte.php')){include './kalWerte.php'; define('IFront',true);} else if(file_exists('../kalWerte.php')) include '../kalWerte.php'; if(!defined('IFront')) define('IFront',false);
header('Content-Type: text/html; charset='.(defined('KAL_Zeichensatz')&&KAL_Zeichensatz!=2||!IFront?'ISO-8859-1':'utf-8'));
if(defined('KAL_TimeZoneSet')&&strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet); else date_default_timezone_set('Europe/Berlin');
if(!defined('KAL_Url')) define('KAL_Url','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.(defined('KAL_Www')?KAL_Www:'localhost'));
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Kalender-Script - Terminvorschlag</title>
<style type="text/css">
body{font-size:100.1%;font-family:Verdana,Arial,Helvetica;}
p,div,table,td,th,li,a,input,textarea,select{font-family:Verdana,Arial,Helvetica;}
div#kopf{font-size:120%;text-align:center;height:32px;margin-bottom:32px;color:#ffffff;background-color:#7777aa;}
p.admMeld{color:#000000;font-weight:bold;text-align:center;}
p.admErfo{color:#008000;font-weight:bold;text-align:center;}
p.admFehl{color:#CC0000;font-weight:bold;text-align:center;}
input[type=submit]{width:160px;}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo KAL_Url?>kalStyles.css">
</head>

<body>
<div id="kopf">Kalender-Script:: Terminvorschlag behandeln</div>
<?php
$DbO=NULL; $sMTxt='???'; $sMTyp='Fehl'; $sBtn='??'; $sSta='?'; $X=''; $sGMap=''; $nSaetze=0;
if(defined('KAL_Version')){
 if(KAL_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet||ADM_SqlZs) $DbO->set_charset(ADM_SqlZs?ADM_SqlZs:KAL_SqlCharSet);}else $DbO=NULL;
 }
 $sIdN=(isset($_GET['i'])?$_GET['i']:(isset($_POST['id'])?$_POST['id']:'0')); $nCod=(isset($_GET['c'])?(int)$_GET['c']:(isset($_POST['cod'])?(int)$_POST['cod']:0));
 if($sIdN>'0'&&$nCod!=0){
  if($nCod==fKCod($sIdN)){
   $a=array(); $sId=substr($sIdN,1,-1); $nIdx=0; $sAutorMlTo=''; $sGMap=''; //ListenDaten
   if(!KAL_SQL){ //Textdaten
    $aD=(file_exists(KAL_Pfad.KAL_Daten.KAL_Termine)?file(KAL_Pfad.KAL_Daten.KAL_Termine):araay()); $nSaetze=count($aD); $s=$sId.';'; $l=strlen($s);
    for($i=1;$i<$nSaetze;$i++){ // gefunden
     if($nIdx==0) if(substr($aD[$i],0,$l)==$s){$a=explode(';',rtrim($aD[$i])); $nIdx=$i;}
    }
    if($nIdx==0) fKMeld('Termin '.$sId.' nicht gefunden!');
   }elseif($DbO){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'"')){
     if(!$a=$rR->fetch_row()){fKMeld('Termin-Datensatz '.$sId.' nicht gefunden!'); $a=array();} $rR->close();
    }else fKMeld('Datenbankabfrage gescheitert!');
   }else fKMeld('KAL_TxSqlVrbdg');
   if(count($a)){
    $sSta=$a[1]; array_splice($a,1,1); /* $nNDf=KAL_NutzerDetailFeld; */ $nFelder=count($kal_FeldName); $kal_FeldName[0]=KAL_TxNummer; $aObj=array();
    $sDat=substr($a[1],0,10); $sPC=(isset($a[$nFelder])?$a[$nFelder]:'');
    $X="\n\n".'<div class="mpTabl" style="width:auto;margin-left:auto;margin-right:auto;margin-top:32px;">'; $nFarb=1; //Tabelle

    for($i=0;$i<$nFelder;$i++){
     $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
     if($kal_DetailFeld[$i]>0&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){
      if(($s=$a[$i])||strlen($s)>0){
       switch($t){
        case 't': case 'm': case 'g': $s=fKBB(fKDt($s)); break; //Text/Memo//Gastkommentar
        case 'a': case 'k': case 'o': $s=fKDt($s); break; //Aufzaehlung/Kategorie so lassen
        case 'd': case '@': $w=trim(substr($s,11)); //Datum
         $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
         if(KAL_MonatDLang>0&&$t=='d'){$aMonate=explode(';',';'.(KAL_MonatDLang==2?KAL_TxLMonate:KAL_TxKMonate)); $s2=fKTx($aMonate[(int)$s2]);}
         switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
          case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
          case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
         }
         $s=$s1.$v.$s2.$v.$s3;
         if($t=='d'){
          if(KAL_MonatDLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
          if($i==1) if($nP=strpos($X,'#',strpos($X,'class="kalMeld"'))) $X=substr_replace($X,$s,$nP,1);
          if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKTx($kal_WochenTag[$w]); else $s=fKTx($kal_WochenTag[$w]);
          if($i==1&&(int)$a[0]<0) $s='<span title="'.fKTx(KAL_TxAendereVmk).'">'.$s.' *</span>';
         }else if($w) $s.='&nbsp;'.$w;
         break;
        case 'z': $s.=' '.fKTx(KAL_TxUhr); break; //Uhrzeit
        case 'w': //Waehrung
         if(((float)$s)!=0||!KAL_PreisLeer){
          $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung;
         }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
         break;
        case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
         if($s=='J'||$s=='Y') $s=fKTx(KAL_TxJa); elseif($s=='N') $s=fKTx(KAL_TxNein);
         break;
        case '#': if(KAL_ZusageSystem) $s=strtoupper(substr($s,0,1)); else $s=''; //Zusage
         if($s=='J'||$s=='Y'){
          $s=$sZusag;
         }elseif($s=='N') $s=fKTx(KAL_TxNein); else $s='&nbsp;';
         break;
        case 'n': case '1': case '2': case '3': case 'r': //Zahl
         if(((float)$s)!=0||!KAL_ZahlLeer){
          if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
         }else if(KAL_ZeigeLeeres) $s='&nbsp;'; else $s='';
         break;
        case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); if((int)$a[0]<0) $s='<span title="'.fKTx(KAL_TxAendereVmk).'">'.$s.' *</span>'; break; //Zaehlnummer
        case 'l': //Link
         $aL=explode('||',$s); $s='';
         foreach($aL as $w){
          $aI=explode('|',$w); $w=$aI[0]; $u=fKDt(isset($aI[1])?$aI[1]:$w);
          $v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'" /> ';
          $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
         }$s=substr($s,0,-2); break;
        case 'e': case 'c': //eMail
         if(!KAL_SQL) $s=fKDeCode($s); $sAutorMlTo=$s;
         $s='<img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKTx(KAL_TxKontakt).'" alt="'.fKTx(KAL_TxKontakt).'" /> '.fKTx($s);
         break;
        case 's': $w=$s; //Symbol
         $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
         $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' border="0" title="'.fKDt($w).'" alt="'.fKDt($w).'" />';
         break;
        case 'b': //Bild
         $s=substr($s,strpos($s,'|')+1); $s=KAL_Bilder.$sId.'_'.$s; if(!$aI=@getimagesize(KAL_Pfad.$s)){$aI[0]=10; $aI[1]=10;} $w=fKDt(substr($s,strpos($s,'_')+1,-4));
         $s='<img class="kalBild" src="'.KAL_Url.$s.'" style="max-width:'.$aI[0].'px;max-height:'.$aI[1].'px;" title="'.$w.'" alt="'.$w.'" />';
         break;
        case 'f': //Datei
         $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKDt(strtoupper($w).'-'.KAL_TxDatei);
         if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat';
         $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'" /> ';
         if(!KAL_DetailDateiSymbol) $v.=fKKurzName($s);
         $s='<a class="kalText" href="'.KAL_Url.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$v.'</a>';
         break;
        case 'u': //Benutzer
         if($nId=(int)$s){
          $s=KAL_TxAutorUnbekannt;
          if(!KAL_SQL){ //Textdaten
           $aU=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nStze=count($aU); $v=$nId.';'; $p=strlen($v);
           for($j=1;$j<$nStze;$j++) if(substr($aU[$j],0,$p)==$v){
            $aN=explode(';',rtrim($aU[$j])); array_splice($aN,1,1); $sAutorMlTo=fKDeCode($aN[4]);
            if(!$s=$aN[$kal_DetailFeld[$i]]) $s=KAL_TxAutorUnbekannt; elseif($kal_DetailFeld[$i]<5&&$kal_DetailFeld[$i]>1) $s=fKDeCode($s);
            break;
           }
          }elseif($DbO){ //SQL-Daten
           if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
            $aN=$rR->fetch_row(); $rR->close();
            if(is_array($aN)){array_splice($aN,1,1); $sAutorMlTo=$aN[4]; if(!$s=$aN[$kal_DetailFeld[$i]]) $s=KAL_TxAutorUnbekannt;}
            else $s=KAL_TxAutorUnbekannt;
          }}
         }else $s=KAL_TxAutor0000;
         break;
        case 'x': $aI=explode(',',$s); //StreetMap
         if(isset($aI[4])&&isset($aI[1])&&$aI[4]>0){ //Koordinaten vorhanden
          $s='<div class="kalNorm" id="GGeo'.$i.'" style="width:99%;max-width:'.KAL_GMapBreit.'px;height:'.KAL_GMapHoch.'px;">'.fKTx(KAL_TxGMap1Warten).'<br /><a class="kalText" href="javascript:showMap'.$i.'()" title="'.fKTx($sFN).'">'.fKTx(KAL_TxGMap2Warten).'</a></div>';
          $sGMap.=(KAL_GMapSource=='O'?fKOMap($i,$aI):fKGMap($i,$aI));
         }else $s='&nbsp;';
         break;
        case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
       }//switch
      }elseif($t=='b'&&KAL_ErsatzBildGross>''){ //keinBild
       $s='grafik/'.KAL_ErsatzBildGross; $aI=@getimagesize(KAL_Pfad.$s); $s='<img class="kalBild" src="'.KAL_Url.$s.'" style="max-width:'.$aI[0].'px;max-height:'.$aI[1].'px;" border="0" alt="" />';
      }elseif(KAL_ZeigeLeeres) $s='&nbsp;';
      if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt) $s=''; elseif($s>'0') $s=(int)$s;}
      elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
      if(strlen($s)>0){
       $X.="\n".' <div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
       $X.="\n".'  <div class="kalTbSp1">'.fKTx($sFN).'</div>';
       $X.="\n".'  <div class="kalTbSp2"'.($kal_ZeilenStil[$i]?' style="'.$kal_ZeilenStil[$i].'"':'').'>'.$s."</div>\n </div>";
      }
     }
    }// $nFelder
    $X.="\n".'</div>'; //Tabelle

    $sAct=(isset($_POST['btn'])?$_POST['btn']:'');
    if($sAct=='Freischalten'){
     $sZ=''; for($j=2;$j<$nFelder;$j++){$sZ.=';'.$a[$j]; if($kal_FeldType[$j]=='b'||$kal_FeldType[$j]=='f') $aObj[$j]=$a[$j];}
     $aD[$nIdx]=$sId.';1;'.$a[1].$sZ."\n"; $aIds=array();
     if($sPC) if($aWdhDat=fKWdhDat(substr($a[1],0,10),$sPC)){ //Wiederholungen
      if(!KAL_SQL){
       $aTmp=array(); $nMxId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nMxId=(int)substr($s,8,strpos($s,';')); //Auto-ID-Nr holen
       for($i=1;$i<$nSaetze;$i++){
        $s=rtrim($aD[$i]); $p=strpos($s,';'); $nANr=(int)substr($s,0,$p); $nMxId=max($nMxId,$nANr);
        $aTmp[substr($s,0,$p+2)]=substr($s,$p+3);
       }
       foreach($aWdhDat as $v){$aTmp[(++$nMxId).';1']=$v.$sZ; $aIds[]=$nMxId;}
       $aD=array(); $s='Nummer_'.$nMxId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $aD[0]=$s.";Periodik\n";
       asort($aTmp); reset($aTmp); foreach($aTmp as $k=>$v){$aD[]=$k.';'.$v."\n";}
      }elseif($DbO){ //SQL
       $sF=''; for($i=1;$i<$nFelder;$i++) $sF.='kal_'.$i.','; $sF.='periodik'; $sZ='",';
       for($i=2;$i<$nFelder;$i++){$sZ.='"'.$a[$i].'",'; if($kal_FeldType[$i]=='b'||$kal_FeldType[$i]=='f') $aObj[$i]=$a[$i];}
       foreach($aWdhDat as $v) if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' (online,'.$sF.') VALUES("1","'.$v.$sZ.'"")')){
        if($nId=$DbO->insert_id) $aIds[]=$nId;
       }
      }
     }
     if(!KAL_SQL){ //Textdaten speichern
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
       $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Die Freischaltung wurde gespeichert.'); $sSta='1';
      }else fKMeld('Die Freischaltung konnte nicht gespeichert werden!');
     }else{
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabT.' SET online="1" WHERE id='.$sId)){
       $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Die Freischaltung wurde gespeichert.'); $sSta='1';
      }else fKMeld('Die Freischaltung konnte nicht gespeichert werden!');
     }
     if($sBtn=='OK'&&count($aIds)>0) for($i=2;$i<$nFelder;$i++){ //Bilder und Dateien kopieren
      if($sONa=(isset($aObj[$i])?$aObj[$i]:'')){
       if($kal_FeldType[$i]=='b'){
        $p=strpos($sONa,'|'); $sONa=substr($sONa,0,$p);
        reset($aIds); foreach($aIds as $j) if(!copy(KAL_Pfad.KAL_Bilder.$sId.'-'.$sONa,KAL_Pfad.KAL_Bilder.$j.'-'.$sONa)) $sBtn=' OK ';
        $sONa=substr($aObj[$i],$p+1);
        reset($aIds); foreach($aIds as $j) if(!copy(KAL_Pfad.KAL_Bilder.$sId.'_'.$sONa,KAL_Pfad.KAL_Bilder.$j.'_'.$sONa)) $sBtn=' OK ';
       }elseif($kal_FeldType[$i]=='f'){
        reset($aIds); foreach($aIds as $j) if(!@copy(KAL_Pfad.KAL_Bilder.$sId.'~'.$sONa,KAL_Pfad.KAL_Bilder.$j.'~'.$sONa)) $sBtn=' OK ';
       }
       if($sBtn!='OK'){$sMTyp='Fehl'; fKMeld('Die Bilder/Anhänge konnten nicht kopiert werden.');}
      }
     }
    }elseif($sAct=='Loeschen'){
     if(!KAL_SQL){ //Textdaten
      if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){//neu schreiben
       $aD[$nIdx]=''; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f);
       $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Der Terminvorschlag wurde gelöscht.'); $sSta='1';
      }else fKMeld('Die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht gespeichert werden!');
     }elseif($DbO){
      if($DbO->query('DELETE FROM '.KAL_SqlTabT.' WHERE id="'.$sId.'" AND online<>"1" LIMIT 1')){
       $sBtn='OK'; $sMTyp='Erfo'; fKMeld('Der Terminvorschlag wurde gelöscht.'); $sSta='1';
      }else fKMeld('Datenbankaktualisierung gescheitert!');
     }
     if((in_array('b',$kal_FeldType)||in_array('f',$kal_FeldType))&&$sMTyp=='Erfo') if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){
      $aF=array(); $l=strlen($sId); while($s=readdir($f)) if($sId==substr($s,0,$l)) if(substr($s,$l,1)=='-'||substr($s,$l,1)=='_'||substr($s,$l,1)=='~') $aF[]=$s; closedir($f);
      foreach($aF as $s) if(file_exists(KAL_Pfad.KAL_Bilder.$s)) @unlink(KAL_Pfad.KAL_Bilder.$s);
     }
    }
    if($sSta!='1'){
     $sMTyp='Meld'; fKMeld('Das Inserat hat den Status <i>'.($sSta=='2'?'vorgemerkt':'offline').'</i>. Jetzt freischalten oder löschen?');
     $sBtn='Freischalten"> &nbsp; <input type="submit" name="btn" value="Loeschen';
    }else{
     if($sMTyp!='Erfo'){$sMTyp='Meld'; fKMeld('Das Inserat ist bereits online.');}
     elseif(KAL_FreischaltMail&&$sAutorMlTo!=''){ //Freischaltmail
      $sMlTxA=strtoupper(KAL_TxNummer).': '.sprintf('%0'.KAL_NummerStellen.'d',$sId);
      if($sAct=='Loeschen') $sMlTxA='Der folgende Termin wurde vom Webmaster gelöscht!'."\n\n".$sMlTxA;
      for($i=1;$i<$nFelder;$i++){
       $t=$kal_FeldType[$i]; if(!KAL_SQL) if($t=='e'||$t=='c'||$t=='p') $a[$i]=fKDeCode($a[$i]);
       if($a[$i]) $sMlTxA.="\n".strtoupper($kal_FeldName[$i]).': '.trim(str_replace('`,',';',str_replace('\n ',"\n",$a[$i])));
      }
      if(isset($_SERVER['HTTP_HOST'])) $sWww=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $sWww=$_SERVER['SERVER_NAME']; else $sWww='localhost';
      require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail(); $Mailer->AddTo($sAutorMlTo);
      if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
      $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
      $Mailer->SetFrom($s,$t); if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
      $Mailer->Subject=str_replace('#A',$sWww,str_replace('#N',sprintf('%0'.KAL_NummerStellen.'d',$sId),KAL_TxFreischaltBtr));
      $Mailer->Text=str_replace('#D',$sMlTxA,str_replace('#A',$sWww,str_replace('#N',sprintf('%0'.KAL_NummerStellen.'d',$sId),str_replace('\n ',"\n",KAL_TxFreischaltTxt))));
      $Mailer->Send();
     }$sBtn='OK';
    }
    // StreetMap initialisieren
    if(!empty($sGMap)){
     if(KAL_GMapSource=='O') $X="\n".'<link rel="stylesheet" type="text/css" href="'.KAL_Url.'maps/leaflet.css" />'."\n".$X."\n\n".'<script type="text/javascript" src="'.KAL_Url.'maps/leaflet.js"></script>';
     else $X.="\n\n".'<script type="text/javascript" src="'.KAL_GMapURL.'"></script>';
     $X.="\n".'<script type="text/javascript">'.$sGMap."\n".'</script>';
    }

   }// gefunden
  }else fKMeld('unerlaubter Aufruf!');
 }else fKMeld('unvollständiger Aufruf!');
}else fKMeld('Datei <i>kalWerte.php</i> nicht gefunden!');

function fKMeld($sMTxt){echo "\n".'<p class="adm'.$GLOBALS['sMTyp'].'">'.$sMTxt.'</p><br />';}
function fKKurzName($s){$i=strlen($s); if($i<=25) return $s; else return substr_replace($s,'...',16,$i-22);}
function fKCod($s){$c=(int)KAL_Schluessel; for($i=strlen($s);$i>=0;--$i) $c+=(int)substr($s,$i,1); return $c;}

function fKNDat($s){return substr($s,8,2).'.'.substr($s,5,2).'.'.substr($s,0,4);}

function fKTx($s){ //TextKodierung
 if(!IFront||KAL_Zeichensatz==0) $s=str_replace('"','&quot;',$s); elseif(KAL_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',$s)); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
function fKDt($s){ //DatenKodierung
 $kalZeichensatz=KAL_LZeichenstz;
 if(!IFront||KAL_Zeichensatz==$kalZeichensatz){if(KAL_Zeichensatz!=1) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));}
 else{
  if($kalZeichensatz!=0) if($kalZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  if(KAL_Zeichensatz==0) $s=str_replace('"','&quot;',str_replace(chr(150),'-',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s)))));
  elseif(KAL_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(150),'-',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s)))))); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br />',$s);
}

function fKEnCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fKDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fKAnzeigeDatum($w){ //sichtbares Datum
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKWdhDat($sBeg,$sCod){
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

function fKPlainText($s,$t,$bMemo=false){
 if($s) switch($t){
  case 'm':  //Memo
   if(KAL_BenachrMitMemo||$bMemo){
    $s=str_replace('\n ',"\n",$s); $l=strlen($s)-1;
    for($k=$l;$k>=0;$k--) if(substr($s,$k,1)=='[') if($p=strpos($s,']',$k))
     $s=substr_replace($s,'',$k,$p+1-$k);
   }else $s=''; break;
  case 'd': if($s=='..') $s=''; break;
  case '@': $s=fKAnzeigeDatum($s).substr($s,10); break;
  case 'l': case 'b': $aI=explode('|',$s); $s=$aI[0]; break;
  default: $s=str_replace('\n ',"\n",$s);
 }
 return $s;
}
//BB-Code zu HTML wandeln
function fKBB($s){
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

function fKTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if(($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')||$t=='v'){
   if($u=$s){
    switch($t){
     case 't': $s=fKBB($s); $u=@strip_tags($s); break; //Text
     case 'm': if(KAL_InfoMitMemo) $u=@strip_tags(fKBB($s)); else $u=''; break; //Memo
     case 'a': case 'k': case 'o': break;  //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $u=fKAnzeigeDatum($s); $w=trim(substr($s,11)); //Datum
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
         $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKDeCode($aN[4]);
         if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKDeCode($s);
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
      $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1); if(isset($aN[4])) $sAutorEml=fKDeCode($aN[4]);
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
function fKExtLink($s){
 if(!defined('KAL_ZSatzExtLink')||KAL_ZSatzExtLink==0) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode($s))));
 elseif(KAL_ZSatzExtLink==1) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s)))));
 elseif(KAL_ZSatzExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fKOMap($n,$a){ //JavaScriptcode zu OpenStreetMap
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

function fKGMap($n,$a){ //JavaScriptcode zu Google-Map
return '
 function showMap'.$n.'(){
  window.clearInterval(showTm'.$n.');'.(KAL_GMapV3?'
  var mapLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[0],$a[1]).');
  var poiLatLng'.$n.'=new google.maps.LatLng('.sprintf('%.15f,%.15f',$a[2],$a[3]).');
  var mapOption'.$n.'={zoom:'.$a[4].',center:mapLatLng'.$n.',panControl:true,mapTypeControl:'.(KAL_GMapTypeControl?'true':'false').',streetViewControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map'.$n.'=new google.maps.Map(document.getElementById('."'".'GGeo'.$n."'".'),mapOption'.$n.');
  var poi'.$n.'=new google.maps.Marker({position:poiLatLng'.$n.',map:map'.$n.',title:'."'".fKTx(KAL_TxGMapOrt)."'".'});':'
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

?>

<div align="center">
<form method="POST">
<input type="hidden" name="id" value="<?php echo $sIdN ?>" />
<input type="hidden" name="cod" value="<?php echo $nCod ?>" />
<p><input type="submit" name="btn" value="<?php echo $sBtn ?>"></p>
</form>
<?php echo $X?>
</div>
</body>
</html>