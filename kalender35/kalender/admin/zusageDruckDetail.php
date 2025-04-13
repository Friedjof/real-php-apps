<?php
include 'hilfsFunktionen.php'; //Zusagen im Detail drucken
header('Content-Type: text/html; charset=ISO-8859-1');

$sKalHtmlVor=''; $sKalHtmlNach=''; $sKalHtmlNach=implode('',(file_exists('druckSeite.htm')?file('druckSeite.htm'):array())); $sZId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:0);
if($p=strpos($sKalHtmlNach,'{Inhalt}')){
 $sKalHtmlVor=substr($sKalHtmlNach,0,$p); $sKalHtmlNach=substr($sKalHtmlNach,$p+8); //Seitenkopf, Seitenfuss
 $sKalHtmlVor=str_replace('../grafik',$sHttp.'grafik',str_replace('{Titel}','Detailinformationen zur Zusage-Nr. '.sprintf('%0'.KAL_NummerStellen.'d',$sZId),$sKalHtmlVor));
 $sKalHtmlNach=str_replace('../grafik',$sHttp.'grafik',$sKalHtmlNach);
}else{$sKalHtmlVor='<p style="color:#AA0033;">HTML-Layout-Schablone <i>druckSeite.htm</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}

echo $sKalHtmlVor."\n";

if($sZId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:0)){
 $kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');

 $aZ=array(); $aT=array(); // Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sZId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDeCode($aZ[8]); break;}
  if((isset($aZ[1])&&$sNr=$aZ[1])||($sNr=$sTrmNr)){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sNr.';'; $p=strlen($s);
   for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$aT=explode(';',rtrim($aD[$i])); break;}
  }
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr='.$sZId)){
   $aZ=$rR->fetch_row(); $rR->close();
   if((isset($aZ[1])&&$sNr=$aZ[1])||($sNr=$sTrmNr)){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id='.$sNr)){
     $aT=$rR->fetch_row(); $rR->close();
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';

 if(count($aZ)){
  $sSta=$aZ[6]; $sImgSta='Grn.gif" title="gültig'; $sTxSta='';
  if($sSta=='0'){$sImgSta='Rot.gif" title="vorgemerkt'; $sTxSta.='vorgemerkt';}
  elseif($sSta=='2'){$sImgSta='RtGn.gif" title="bestätigt'; $sTxSta.='bestätigt';}
  elseif($sSta=='-'){$sImgSta='RotX.gif" title="Widerruf vorgemerkt'; $sTxSta.='Widerruf vorgemerkt';}
  elseif($sSta=='*'){$sImgSta='RtGnX.gif" title="Widerruf bestätigt'; $sTxSta.='Widerruf bestätigt';}
  elseif($sSta=='7'){$sImgSta='Glb.gif" title="Warteliste'; $sTxSta.='auf der Warteliste';}
  else $sTxSta.='gültig';
?>

<table class="druck" style="width:90%;margin-top:12px;" border="0" cellpadding="1" cellspacing="0">
<tr class="druck">
 <td class="druck" width="20%">Zusage-Nr.</td>
 <td class="druck"><?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[0]);?></td>
</tr>
<tr class="druck">
 <td class="druck">Status</td>
 <td class="druck"><?php echo '<img src="'.$sHttp.'grafik/punkt'.$sImgSta.'" width="12" height="12" border="0"> <span style="color:#555555">'.$sTxSta;?></span></td>
</tr>
<tr class="druck">
 <td class="druck">Buchungszeit</td>
 <td class="druck"><?php echo fKalAnzeigeDatum($aZ[5]).','.substr($aZ[5],10).' '.KAL_TxUhr ?></td>
</tr>
<tr class="druck">
 <td class="druck">Terminzeit</td>
 <td class="druck"><?php echo fKalAnzeigeDatum($aZ[2]); if($aZ[3]) echo ', '.$aZ[3].' '.KAL_TxUhr ?></td>
</tr>
<tr class="druck">
 <td class="druck">Veranstaltung</td>
 <td class="druck"><?php echo str_replace('`,',';',$aZ[4]) ?></td>
</tr>
<tr class="druck">
 <td class="druck">Benutzer</td>
 <td class="druck"><?php if($aZ[7]>'0') echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[7]); else echo 'unbekannt'?></td>
</tr>
<tr class="druck">
 <td class="druck">E-Mail</td>
 <td class="druck"><?php echo $aZ[8] ?></td>
</tr>
<?php
 $aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
 for($i=9;$i<=$nZusageFelder;$i++){
  $s=(isset($aZ[$i])?str_replace('`,',';',$aZ[$i]):''); $t=$aZusageFeldTyp[$i];
  if($t=='w'){
   $s=(float)$s;
   if($s>0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen).' '.KAL_Waehrung; else $s='';
  }elseif($t=='j'){if($s=='J') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;}
?>
<tr class="druck">
 <td class="druck"><?php $sNam=str_replace('`,',';',$kal_ZusageFelder[$i]); if($sNam=='ANZAHL'&&strlen(KAL_ZusageNameAnzahl)>0) $sNam=KAL_ZusageNameAnzahl; echo $sNam ?></td>
 <td class="druck"><?php echo (strlen($s)>0?$s:'&nbsp;')?></td>
</tr>
<?php }?>
</table>

<p class="admMeld">Detailinformationen zum Termin-Nr. <i><?php echo sprintf('%0'.KAL_NummerStellen.'d',(isset($aZ[1])?$aZ[1]:$sTrmNr))?></i></p>
<table class="druck" style="width:90%;margin-top:12px;" border="0" cellpadding="1" cellspacing="0">
<tr class="druck">
 <td class="druck" width="20%">Termin-Nr.</td>
 <td class="druck"><?php echo (count($aT)>1?sprintf('%0'.KAL_NummerStellen.'d',$aT[0]):'Termin nicht vorhanden!')?></td>
</tr>
<?php
if(count($aT)>1){
  $nFelder=count($kal_FeldName); array_splice($aT,1,1); if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i];
   if(($kal_DetailFeld[$i]>0&&$t!='p'&&$kal_FeldName[$i]!='TITLE'&&substr($kal_FeldName[$i],0,5)!='META-')||$t=='v'){
    if($s=str_replace('\n ',NL,str_replace('`,',';',$aT[$i]))){
     switch($t){
      case 't': $s=fKalBB($s); break; //Text
      case 'a': case 'k': case 'o': break; //Aufzaehlung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].' '.$s; else $s.=' '.$kal_WochenTag[$w];}
       }else{$s.=', '.$w.' '.KAL_TxUhr;}
       break;
      case 'z': $s.=' '.KAL_TxUhr; break; //Uhrzeit
      case 'w': //Waehrung
       $s=(float)$s;
       if($s>0||!KAL_PreisLeer){
        $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
        if(KAL_Waehrung) $s.=' '.KAL_Waehrung;
       }else if(KAL_ZeigeLeeres) $s=' '; else $s='';
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w){$aI=explode('|',$w); $s.=$aI[0].'<br>';}
       $s=substr($s,0,-4); break;
      case 'e': case 'c':
       if(!KAL_SQL) $s=fKalDeCode($s);
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$aT[0].'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'-')+1,-4).'" />';
       break;
      case 'f': //Datei
       break;
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
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr='.$nId)){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=$aN[KAL_NutzerInfoFeld]) $s=KAL_TxAutorUnbekannt;}
          else $s=KAL_TxAutorUnbekannt;
        }}
       }else $s=KAL_TxAutor0000;
       break;
      default: $s='';
     }//switch
    }
    if(strlen($s)>0){
     $sFN=$kal_FeldName[$i];
     if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
     if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
     echo "\n".'<tr class="druck">';
     echo "\n".' <td class="druck" valign="top">'.$sFN.'</td>';
     echo "\n".' <td class="druck">'.$s."</td>\n</tr>";
    }
   }
  }
}?>
</table>

<?php
 }
}else echo NL.'<p class="admFehl">Ungültiger Seitenaufruf ohne Terminummer!</p>';

echo NL.$sKalHtmlNach;

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