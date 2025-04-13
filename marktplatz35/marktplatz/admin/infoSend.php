<?php
 include 'hilfsFunktionen.php';
 header('Content-Type: text/html; charset=ISO-8859-1');
?><html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<title>Marktplatz:: Information versenden</title>
<link rel="stylesheet" type="text/css" href="<?php echo(file_exists('autoren.css')?'autoren':'admin')?>.css">
<link rel="stylesheet" type="text/css" href="<?php echo MPPFAD?>mpStyles.css">
</head>

<body>
<div id="seite"><div id="rahmen">
<div id="kopf">
<h1><img src="_markt.gif" width="16" height="24" border="0" align="bottom" alt=""> Marktplatz-Script: Information versenden</h1>
</div>
<div align="center">

<?php
 $aStru=array();
 if(isset($_GET['seg'])){$nSegNo=(int)$_GET['seg']; $sSegNo=sprintf('%02d',$nSegNo); $aS=explode(';',MP_Segmente); $sSegNam=$aS[$nSegNo];}
 else{$nSegNo=0; $sSegNo='00'; $sSegNam='leeres Muster-Segment'; $aS=array();}

 $H=''; $U=''; $sId=''; $sInf=''; $sBtr=''; $sTxt=''; $sVon=''; $sLnk='';
 $bInf=true; $bBtr=true; $bTxt=true; $bVon=true; $bCap=true; $bOK=true; $bDo=true;

 $nFelder=0; $aStru=array(); $aFN=array(); $aFT=array(); $aDF=array(); $aND=array();
 $aZS=array(); $aAW=array(); $aKW=array(); $aSW=array();
 if(!MP_SQL){//Text
  $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); fMpEntpackeStruktur(); $nFelder=count($aFN);
 }elseif($DbO){//SQL
  if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
   $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
   if($i==1){$aStru=explode("\n",$a[1]); fMpEntpackeStruktur(); $nFelder=count($aFN);}
  }else $Meld=MP_TxSqlFrage;
 }else $Meld=MP_TxSqlVrbdg;
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}

 if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
  if(isset($_GET['mp_Num'])){
   $sId=$_GET['mp_Num']; $Meld=MP_TxInfoMeld; $MTyp='Meld';
   $sBtr=MP_TxInfoBtr; $sTxt=str_replace('#',fMpURL(),str_replace('\n ',"\n",MP_TxInfoTxt));
  }
 }else{ //POST
  if(isset($_POST['mp_Num'])){
   $sId=$_POST['mp_Num']; reset($_POST);
   foreach($_POST as $k=>$v) if($k!='mp_Num'){
    $s=str_replace('"',"'",@strip_tags(stripslashes(str_replace("\r",'',trim($v)))));
    if($k=='mp_iInf'){
     if($sInf=$s){
      if(!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($sInf))) {$bInf=false; $bOK=false;}
     }else{$bInf=false; $bOK=false;}
    }elseif($k=='mp_iBtr'){
     if($sBtr=$s){if($p=strpos($sBtr,"\n")) $sBtr=rtrim(substr($sBtr,0,$p));} else{$bBtr=false; $bOK=false;}
    }elseif($k=='mp_iTxt'){
     if(!$sTxt=$s){$bTxt=false; $bOK=false;}
    }elseif($k=='mp_iVon'){
     if($sVon=$s){if($p=strpos($sVon,"\n")) $sVon=rtrim(substr($sVon,0,$p));} else{$bVon=false; $bOK=false;}
    }
   }
  }
 }

 if($sId>''){ //Inseratedaten holen
  if(!MP_SQL){ //Textdatei
   $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD); $s=$sId.';'; $l=strlen($s);
   for($i=1;$i<$nSaetze;$i++){ //über alle Datensätze
    if(substr($aD[$i],0,$l)==$s){$a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); break;}
   }
  }else{ //SQL-Daten
   if($rR=@$DbO->query('SELECT * FROM '.str_replace('%',$sSegNo,MP_SqlTabI).' WHERE nr='.$sId)){
    $a=$rR->fetch_row(); array_splice($a,1,1); $rR->close();
   }else $Meld=MP_TxSqlFrage;
  }

  $nFelder=count($aFN); $nFarb=1; if(MP_InfoNDetail) $aDF=$aND; //Inseratedetails aufbereiten
  $H.="\n".'<div class="mpTabl">';
  for($i=1;$i<$nFelder;$i++)if(!(strpos($aFN[$i],'META-')===0)){
   $t=$aFT[$i]; $u='';
   if($aDF[$i]>0&&$t!='p'&&$t!='c'){
    if($s=str_replace('\n ',NL,str_replace('`,',';',$a[$i]))){$u=$s;
     switch($t){
      case 't': case 'a': case 'k': case 'o': case 'u': break; //Text/Aufzählung/Kategorie so lassen
      case 'm': $s=fMpBB($s); $u=''; break;
      case 'd': case '@': $w=substr($s,10); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3; if($t=='@') $s.=$w; $u=$s; break;
      case 'z': $u=$s.' '.MP_TxUhr; $s.=' '.MP_TxUhr; break; //Uhrzeit
      case 'i': $s=(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$s); $u=$s; break;
      case 'w': //Währung
       if($s>0||!MP_PreisLeer){
        $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
        if(MP_Waehrung){$u=$s.' '.MP_Waehrung; $s.='&nbsp;'.MP_Waehrung;}
       }else if(MP_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
       break;
      case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y'){$s=MP_TxJa; $u=MP_TxJa;}elseif($s=='N'){$s=MP_TxNein; $u=MP_TxNein;}
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $u=$s;
       break;
      case 'l': //Link
       $aI=explode('|',$s); $s=$aI[0]; $u=$s; $v=(isset($aI[1])?$aI[1]:$s);
       $s='<a class="mpText" href="'.(strpos($s,'@')?'mailto:':(($p=strpos($s,'tp'))&&strpos($s,'://')>$p||strpos('#'.$s,'tel:')==1?'':'http://').$s.'" target="_blank').'">'.$v.'</a>';
       break;
      case 's':
       $p=array_search($s,$aSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
       $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
       $s='<img src="'.$sHttp.$s.'" '.(isset($aI[3])?$aI[3]:'').' align="middle" border="0" alt="'.$u.'" />&nbsp;'.$u;
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $u=MP_Www.$s;
       $s='<img src="'.$sHttp.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" title="'.substr($s,strpos($s,'/')+1).'" />';
       break;
      case 'f': //Datei
       $u=MP_Www.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s;
       $s='<a href="'.$sHttp.MP_Bilder.$sBldDir.$sId.$sBldSeg.'~'.$s.'" target="_blank">'.$s.'</a>';
       break;
      default: {$s=''; $u='';}
     }//switch
    }
    if(strlen($u)>0) $U.="\n".strtoupper($aFN[$i]).': '.$u;
    if(strlen($s)>0){
     $H.="\n".'<div class="mpTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
     $H.="\n".' <div class="mpTbSpi">'.$aFN[$i].'</div>';
     $H.="\n".' <div class="mpTbSp2">'.$s."</div>\n</div>";
    }
   }
  }
  $H.="\n</div>\n";
  $sLnk=fMpDetailURL($sId,$nSegNo);
  if($_SERVER['REQUEST_METHOD']=='POST'){ //Info versenden
   if($bOK){
    $Ht='<!DOCTYPE html>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.$sHttp.'mpStyles.css">
</head>
<body class="mpEMail">
<p>'.str_replace("\n","</p>\n<p>",str_replace("\n\n","\n",$sTxt)).'</p>
<p>'.$sVon.'</p>
<p><a href="'.$sLnk.'">'.substr($sLnk,strpos($sLnk,':')+3).'</a></p>
<p style="margin-bottom:2px;">Inseratedetails:</p>
';
    require_once(MPPFAD.'class.htmlmail.php'); $Mailer=new HtmlMail();
    if(MP_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=MP_SmtpHost; $Mailer->SmtpPort=MP_SmtpPort; $Mailer->SmtpAuth=MP_SmtpAuth; $Mailer->SmtpUser=MP_SmtpUser; $Mailer->SmtpPass=MP_SmtpPass;}
    $s=MP_MailFrom; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
    $Mailer->AddTo($sInf); $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($sInf);
    if(strlen(MP_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(MP_EnvelopeSender);
    $Mailer->PlainText=$sTxt."\n".$sVon."\n\n".$sLnk."\n\n".trim($U);
    $Mailer->HtmlText=str_replace("\r",'',$Ht).$H."\n</body>\n</html>";
    if($Mailer->Send()){
     $Meld=MP_TxSendeErfo; $MTyp='Erfo'; $bDo=false;
    }else{$Meld=MP_TxSendeFehl; $bOK=false;}
   }else $Meld=MP_TxEingabeFehl;
  }//POST
 }//sId

 $X= NL.'<p class="adm'.$MTyp.'">'.$Meld.'</p>';
 $X.=NL.'<form class="admForm" action="infoSend.php?seg='.$nSegNo.'" method="post">';
 $X.=NL.'<input type="hidden" name="mp_Num" value="'.$sId.'" />';
 $X.=NL.'<table class="admTabl" style="width:96%" border="0" cellpadding="2" cellspacing="1">';
 $X.=NL.'<tr class="admTabl"><td>Empfänger</td><td><div'.($bInf?'':' class="admFehl"').'><input type="text" name="mp_iInf" value="'.$sInf.'" style="width:99%" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td>Betreff</td><td><div'.($bBtr?'':' class="admFehl"').'><input type="text" name="mp_iBtr" value="'.$sBtr.'" style="width:99%" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Mitteilung</td><td><div'.($bTxt?'':' class="admFehl"').'><textarea name="mp_iTxt" rows="5" style="width:99%;height:100px;">'.$sTxt.'</textarea></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td>Absender</td><td><div'.($bVon?'':' class="admFehl"').'><input type="text" name="mp_iVon" value="'.$sVon.'" style="width:99%" /></div></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Link</td><td><textarea readonly="readonly" style="width:99%;height:4em;border-style:none; border-width:0;">'.$sLnk.'</textarea></td></td></tr>';
 $X.=NL.'<tr class="admTabl"><td valign="top">Details</td><td>'.$H.'</td></tr>';
 $X.=NL.'</table>';
 if($bDo) $X.=NL.'<p class="admSubmit"><input class="admSubmit" type="submit" value="Senden" title="Senden" /></p>';
 $X.=NL.'</form>'.NL;
 echo $X;

?>
</div>
<div id="zeitangabe">--- <?php echo date('d.m.Y, H:i:s')?> ---</div>
</div></div>
</body>
</html>

<?php
function fMpEntpackeStruktur(){//Struktur interpretieren
 global $aStru,$aFN,$aFT,$aDF,$aND,$aZS,$aAW,$aKW,$aSW;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $aFT[0]='i'; $aFT[1]='d';
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]=substr($aDF[0],14,1);
 $aND=explode(';',rtrim($aStru[8])); $aND[0]=substr($aND[0],14,1);
 $aZS=explode(';',rtrim($aStru[6])); $aZS[0]='';
 $aAW=explode(';',rtrim($aStru[16])); $aAW[0]=''; $aAW[1]='';
 $s=rtrim($aStru[17]); if(strlen($s)>14) $aKW=explode(';',substr_replace($s,';',14,0)); $aKW[0]='';
 $s=rtrim($aStru[18]); if(strlen($s)>14) $aSW=explode(';',substr_replace($s,';',14,0)); $aSW[0]='';
 return true;
}

function fMpURL(){
 $s='http'.($_SERVER['SERVER_PORT']!='443'?'':'s').'://';
 if(isset($_SERVER['HTTP_HOST'])) $s.=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s.=$_SERVER['SERVER_NAME']; else $s.='localhost';
 return $s;
}
function fMpDetailURL($sNr,$nSeg){
 $sLnk=(MP_InfoLink==''?'http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.MP_Www.'marktplatz.php?':MP_InfoLink.(!strpos(MP_InfoLink,'?')?'?':'&')).'mp_Segment='.$nSeg.'&mp_Aktion=detail&mp_Nummer='.$sNr;
 if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=fKalURL().$sLnk;
 return $sLnk;
}

function fMpBB($s){ //BB-Code zu HTML wandeln
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
?>