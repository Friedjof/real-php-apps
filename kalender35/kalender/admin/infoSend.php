<?php
include 'hilfsFunktionen.php';
header('Content-Type: text/html; charset=ISO-8859-1');
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="expires" content="0">
<title>Kalender:: Information versenden</title>
<link rel="stylesheet" type="text/css" href="<?php echo(file_exists('autoren.css')?'autoren':'admin')?>.css">
<link rel="stylesheet" type="text/css" href="<?php echo KALPFAD?>kalStyles.css">
</head>

<body>
<div id="seite"><div id="rahmen">
<div id="kopf">
<h1><img src="_kalender.gif" width="19" height="25" border="0" align="bottom" alt=""> Kalender-Script: Information versenden</h1>
</div>
<div align="center">

<?php

 $H=''; $U=''; $sId=''; $sInf=''; $sBtr=''; $sTxt=''; $sVon=''; $Es='Fehl';
 $bInf=true; $bBtr=true; $bTxt=true; $bVon=true; $bCap=true; $bOK=true; $bDo=true;

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if(isset($_GET['id'])){
   $sId=$_GET['id']; $Et=KAL_TxInfoMeld; $Es='Meld';
   $sWww=fKalURL(); $sWww=substr($sWww,strpos($sWww,'://')+3); $sBtr=str_replace('#',$sWww,str_replace('#A',$sWww,KAL_TxInfoBtr));
   $sTxt=str_replace('#',fKalURL(),str_replace('#A',fKalURL(),str_replace('\n ',"\n",KAL_TxInfoTxt)));
  }
 }else{ //POST
  if(isset($_POST['id'])){
   $sId=$_POST['id']; reset($_POST);
   foreach($_POST as $k=>$v) if($k!='id'){
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if($k=='kal_iInf'){
     if($sInf=$s){
      if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sInf))) {$bInf=false; $bOK=false;}
     }else{$bInf=false; $bOK=false;}
    }elseif($k=='kal_iBtr'){
     if($sBtr=$s){if($p=strpos($sBtr,"\n")) $sBtr=rtrim(substr($sBtr,0,$p));} else{$bBtr=false; $bOK=false;}
    }elseif($k=='kal_iTxt'){
     if(!$sTxt=$s){$bTxt=false; $bOK=false;}
    }elseif($k=='kal_iVon'){
     if($sVon=$s){if($p=strpos($sVon,"\n")) $sVon=rtrim(substr($sVon,0,$p));} else{$bVon=false; $bOK=false;}
    }
   }
  }
 }
 if($sId>''){ //Termindaten holen
  if(!KAL_SQL){ //Textdatei
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){ //über alle Datensätze
    $a=explode(';',rtrim($aD[$i])); if($sId==$a[0]) break;
   }
  }else{ //SQL-Daten
   if($DbO){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id='.$sId)){
     $a=$rR->fetch_row(); $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }else $Et=KAL_TxSqlVrbdg;
  }
  $nFelder=count($kal_FeldName); array_splice($a,1,1); $nFarb=1; //Termindetails aufbereiten
  if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  $H.="\n".'<div class="kalTabl">';
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i]; $u='';
   if(($kal_DetailFeld[$i]>0&&$t!='p'&&$t!='c')||$t=='v'){
    if($s=$a[$i]){$u=$s;
     switch($t){
      case 't': $s=fKalBB($s); $u=@strip_tags($s); break; //Text/Memo
      case 'm': if(KAL_InfoMitMemo){$s=fKalBB($s); $u=@strip_tags($s);} else{$s=''; $u='';} break; //Memo
      case 'a': case 'k': case 'o': break; //Aufzählung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $u=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];}
       }elseif($w) $u.=' '.$w;
       $s=str_replace(' ','&nbsp;',$u); break;
      case 'z': $u=$s.' '.KAL_TxUhr; $s.=' '.KAL_TxUhr; break; //Uhrzeit
      case 'w': //Währung
       if($s>0||!KAL_PreisLeer){
        $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
        if(KAL_Waehrung){$u=$s.' '.KAL_Waehrung; $s.='&nbsp;'.KAL_Waehrung;}
       }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y'){$s=KAL_TxJa; $u=KAL_TxJa;}elseif($s=='N'){$s=KAL_TxNein; $u=KAL_TxNein;}
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $u=$s;
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s=''; $z='';
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w); $z.=$w.', ';
        $v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" align="top" style="margin-right:4px;" title="'.$u.'" alt="'.$u.'" />';
        $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a> ':$u.'</a>, ');
       }$s=substr($s,0,-2); $u=substr($z,0,-2); break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $u=KAL_Www.$s;
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'-')+1,-4).'" />';
       break;
      case 'f': //Datei
       $u=KAL_Www.KAL_Bilder.$sId.'~'.$s; $s='<a href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$s.'</a>'; break;
      case 'u':
       if($nId=(int)$s){
        $s=KAL_TxAutorUnbekannt;
        if(!KAL_SQL){ //Textdaten
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $v=$nId.';'; $p=strlen($v);
         for($j=1;$j<$nSaetze;$j++) if(substr($aD[$j],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$j])); array_splice($aN,1,1);
          if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerInfoFeld<5&&KAL_NutzerInfoFeld>1) $s=fKalDeCode($s);
          break;
         }
        }else{ //SQL-Daten
         if($DbO){
           if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
            $aN=$rR->fetch_row(); $rR->close();
            if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt;}
            else $s=KAL_TxAutorUnbekannt;
        }}}
       }else $s=KAL_TxAutor0000; $u=$s;
       break;
      default: {$s=''; $u='';}
     }//switch
    }
    if($sFN=='KAPAZITAET'){if(strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; if(KAL_ZusageKapazVersteckt){$s=''; $u='';}elseif($s>'0'){$s=(int)$s; $u=(int)$u;}}
    elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
    if(strlen($u)>0) $U.="\n".strtoupper($sFN).': '.$u;
    if(strlen($s)>0){
     $H.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $H.="\n".' <div class="kalTbSp1">'.$sFN.'</div>';
     $H.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
    }
   }
  }
  $H.="\n</div>\n";
  $sLnk=fKalDetailURL($sId);
  if($_SERVER['REQUEST_METHOD']=='POST'){ //Info versenden
   if($bOK){
    $Ht=str_replace("\n\n","\n",$sTxt);
    if(($nP=strpos($Ht,'http://'))||($nP=strpos($Ht,'https://'))){
     if(!$nE=strpos($Ht,' ',$nP+1)) $nE=strlen($sHt);
     $Ht=substr_replace($Ht,'</a>',$nE,0);
     $Ht=substr_replace($Ht,'<a href="'.substr($Ht,$nP,$nE-$nP).'" target="info">',$nP,(substr($Ht,$nP,5)=='http:'?7:8));
    }
    $Ht='<!DOCTYPE HTML>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.$sHttp.'kalStyles.css">
</head>
<body class="kalSeite">
<p>'.str_replace("\n","</p>\n<p>",$Ht).'</p>
<p>'.$sVon.'</p>
<p><a href="'.str_replace('&amp;','&',$sLnk).'">'.substr($sLnk,strpos($sLnk,':')+3).'</a></p>
<p style="margin-bottom:2px;">Termindetails:</p>
';
    require_once(KALPFAD.'class.htmlmail.php'); $Mailer=new HtmlMail();
    if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
    $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->AddTo($sInf); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sInf);
    if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
    $Mailer->PlainText=$sTxt."\n".$sVon."\n\n".str_replace('&amp;','&',$sLnk)."\n\n".trim($U);
    $Mailer->HtmlText=$Ht.str_replace(' style="width:100%"','',str_replace(' style="width:10%"','',$H))."\n</body>\n</html>";
    if($Mailer->Send()){
     $Et=KAL_TxSendeErfo; $Es='Erfo'; $bDo=false;
    }else{$Et=KAL_TxSendeFehl; $bOK=false;}
   }else{$Et=KAL_TxEingabeFehl; $bOK=false;}
  }//POST
 }//sId

 $X= NL.'<p class="adm'.$Es.'">'.$Et.'</p>';
 $X.=NL.'<form class="admForm" action="infoSend.php" method="post">';
 $X.=NL.'<input type="hidden" name="id" value="'.$sId.'" />';
 $X.=NL.'<table class="admTabl" style="width:96%" border="0" cellpadding="2" cellspacing="1">';
 $X.=NL.'<tr class="admTabl"><td>Empfänger</td><td><div'.($bInf?'':' class="admFehl"').'><input type="text" name="kal_iInf" value="'.$sInf.'" style="width:100%;" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td>Betreff</td><td><div'.($bBtr?'':' class="admFehl"').'><input type="text" name="kal_iBtr" value="'.$sBtr.'" style="width:100%;" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Mitteilung</td><td><div'.($bTxt?'':' class="admFehl"').'><textarea name="kal_iTxt" rows="5" cols="80" style="height:100px;">'.$sTxt.'</textarea></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td>Absender</td><td><div'.($bVon?'':' class="admFehl"').'><input type="text" name="kal_iVon" value="'.$sVon.'" style="width:100%;" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Link</td><td><textarea readonly="readonly" rows="3" cols="80" style="height:4em;border-style:none; border-width:0;">'.$sLnk.'</textarea></td></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Details</td><td>'.$H.'</td></tr>';
 $X.=NL.'</table>';
 if($bDo) $X.=NL.'<p class="admSubmit"><input class="admSubmit" type="submit" value="Senden"></p>';
 $X.=NL.'</form>'.NL;
 echo $X;

?>
</div>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
</div></div>
</body>
</html>

<?php
function fKalURL(){
 $s='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 if(isset($_SERVER['HTTP_HOST'])) $s.=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s.=$_SERVER['SERVER_NAME']; else $s.='localhost';
 return $s;
}
function fKalDetailURL($sNr){
 $sLnk=(KAL_InfoLink==''?'http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://'.KAL_Www.'kalender.php?':KAL_InfoLink.(!strpos(KAL_InfoLink,'?')?'?':'&amp;')).'kal_Aktion=detail&amp;kal_Intervall=%5B%5D&amp;kal_Nummer='.$sNr;
 if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=fKalURL().$sLnk;
 return $sLnk;
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