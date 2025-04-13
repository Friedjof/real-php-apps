<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusage ändern','<script type="text/javascript">
 function NumWin(sURL){numWin=window.open(sURL,"nummer","width=430,height=560,left=3,top=9,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");numWin.focus();}
</script>
','ZZl');

$nFelder=count($kal_FeldName); $nSumZ=0; $nKapaz=0; $nKapLim=0;
$aZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
$aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); if(strpos(KAL_ZusageFeldTyp,'a')) $aZusageAuswahl=explode(';',KAL_ZusageAuswahl);
$nAnzahlPos=(int)array_search('ANZAHL',$aZusageFelder); if($nKapazPos=(int)array_search('KAPAZITAET',$kal_FeldName)) $nKapazPos++;
if(!$sQ=$_SERVER['QUERY_STRING']) $sQ='kal_Num=0&amp;'.(isset($_POST['kal_Qry'])?$_POST['kal_Qry']:''); $sQLst='';
if($p=strpos($sQ,'kal_',8)) $sQ=substr($sQ,$p); else $sQ=''; $aZ=array(); $aT=array(); $aE=array(); $aFehl=array(); $bMail=false;

if($sZId=(isset($_GET['kal_Num'])?$_GET['kal_Num']:(isset($_POST['kal_Num'])?$_POST['kal_Num']:''))){
 $aZ=array(); $aT=array(); $nDZl=0; // Daten holen
 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $s=$sZId.';'; $p=strlen($s); //Zusage holen
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){$nDZl=$i; $aZ=explode(';',rtrim($aD[$i])); $aZ[8]=fKalDeCode($aZ[8]); break;}
  if($sNr=$aZ[1]){ //TerminNr
   if($nAnzahlPos){ //Zusagen summieren
    $v=$sNr.';'; $l=strlen($v);
    for($i=1;$i<$nSaetze;$i++){
     $s=$aD[$i]; $p=strpos($s,';'); if(substr($s,$p+1,$l)==$v){$aE=explode(';',rtrim($s)); $nSumZ+=$aE[$nAnzahlPos];}
   }}
   $aE=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aE); $s=$sNr.';'; $p=strlen($s); //Termin holen
   for($i=1;$i<$nSaetze;$i++) if(substr($aE[$i],0,$p)==$s){
    $aT=explode(';',rtrim($aE[$i]));
    if($nKapazPos){$nKapaz=$aT[$nKapazPos]; if($p=strpos($nKapaz,'(')){$nKapLim=(int)substr($nKapaz,$p+1); $nKapaz=(int)$nKapaz;}}
    break;
   }
  }
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' WHERE nr='.$sZId)){ //Zusage
   $aZ=$rR->fetch_row(); $rR->close();
   if($sNr=$aZ[1]){
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id='.$sNr)){ //Termin
     $aT=$rR->fetch_row(); $rR->close();
     if($nKapazPos){$nKapaz=$aT[$nKapazPos]; if($p=strpos($nKapaz,'(')){$nKapLim=(int)substr($nKapaz,$p+1); $nKapaz=(int)$nKapaz;}}
     if($nAnzahlPos) if($rR=$DbO->query('SELECT COUNT(nr),SUM(dat_'.$nAnzahlPos.') FROM '.KAL_SqlTabZ.' WHERE termin='.$sNr)){ //Zusagensumme
      $a=$rR->fetch_row(); $rR->close(); $nSumZ=$a[1];
     }
    }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';

 if($_SERVER['REQUEST_METHOD']!='POST'){
  if(isset($aZ[2])) $aZ[2]=fKalAnzeigeDatum($aZ[2]);
 }else{//POST
  $aZusagePflicht=explode(';',KAL_ZusagePflicht); $aE=$aZ;
  if($s=(int)$_POST['kal_F1']) $aE[1]=$s;
  $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F2'])))))); $aE[2]=$s;
  if($s=fKalErzeugeDatum($s)) $aE[2]=substr($s,0,10); else $aFehl[2]=true;
  $s=stripslashes(@strip_tags(str_replace("\n",'',str_replace("\r",'',str_replace('"','',trim($_POST['kal_F3']))))));
  if($s){$a=explode(':',str_replace('.',':',$s)); $aE[3]=sprintf('%02d:%02d',$a[0],(isset($a[1])?$a[1]:0));}
  $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F4'])))))); if($s) $aE[4]=str_replace(';','`,',$s); else $aFehl[4]=true;
  $aE[6]=(isset($_POST['kal_F6'])&&$_POST['kal_F6']>''?substr($_POST['kal_F6'],0,1):'0');
  $aE[7]=sprintf('%0'.KAL_NummerStellen.'d',stripslashes(@strip_tags(trim($_POST['kal_F7']))));
  for($i=8;$i<=$nZusageFelder;$i++){
   $s=str_replace(';','`,',stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F'.$i])))))));
   $aE[$i]=$s; if((strlen($s)==0)&&$aZusagePflicht[$i]) $aFehl[$i]=true;
   if($aZusageFeldTyp[$i]=='w'){
    $s=(float)str_replace(KAL_Dezimalzeichen,'.',str_replace(KAL_Tausendzeichen,'',$s));
    if($s!=0||!KAL_PreisLeer){
     $aE[$i]=number_format($s,KAL_Dezimalstellen,'.',''); $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,'');
    }else{$s=''; $aE[$i]=$s;}
   }
  }
  if(count($aFehl)==0){ //keineFehler
   if($aZ!=$aE){ //geaendert
    if(KAL_Zusagen){
     if(!KAL_SQL){ //Textdaten
      $sEml=$aE[8]; $aE[8]=fKalEnCode($sEml); $s=$aE[0]; for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.$aE[$i]; $aE[8]=$sEml;
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
       $aD[$nDZl]=$s.NL; fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);
       $Msg='<p class="admErfo">Die Zusagedaten wurden geändert!</p>'; $bMail=true;
      }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
     }elseif($DbO){
      $s='termin="'.$aE[1].'",datum="'.$aE[2].'",zeit="'.$aE[3].'",veranstaltung="'.str_replace('`,',';',$aE[4]).'",aktiv="'.$aE[6].'",benutzer="'.$aE[7].'",email="'.$aE[8].'"';
      for($i=9;$i<=$nZusageFelder;$i++) $s.=',dat_'.$i.'="'.str_replace('`,',';',$aE[$i]).'"';
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabZ.' SET '.$s.' WHERE nr='.(int)$sZId)){
       $Msg='<p class="admErfo">Die Zusagedaten wurden geändert!</p>'; $bMail=true;
      }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
     }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
     if($nAnzahlPos) $nSumZ+=$aE[$nAnzahlPos]-$aZ[$nAnzahlPos];
     // ToDo: LimitMail
    }else $Msg='<p class="admFehl">N'.'ur i'.'n der Vo'.'l'.'lve'.'rs'.'ion des Zu'.'sagemo'.'d'.'uls!</p>';
   }else $Msg='<p class="admMeld">Die Zusagedaten bleiben unverändert!</p>';
  }else $Msg='<p class="admFehl">Korrigieren Sie die markierten Felder!</p>';
  if(!isset($aFehl[2])) $aE[2]=fKalAnzeigeDatum($aE[2]); $aZ=$aE;
 }//POST
}else $Msg='<p class="admFehl">Ungültiger Seitenaufruf ohne Zusagenummer!</p>';

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Ändern der Terminzusage-Nr. <i>'.sprintf('%0'.KAL_NummerStellen.'d',$sZId).'</i></p>';
echo $Msg.NL;

if($p=strpos('#'.$sQ,'kal_Lst')){
 if(substr($sQ,(--$p)-5,1)=='&') $i=5; elseif(substr($sQ,$p-1,1)=='&') $i=1; else $i=0;
 $bZurTrmLst=true; $sQL=substr_replace($sQ,'',$p-$i,9+$i);
}else{$bZurTrmLst=false; $sQL=$sQ;}

if(count($aZ)){
 $sSta=$aZ[6]; $sImgSta='Grn.gif" title="gültig';
 if($sSta=='0'){$sImgSta='Rot.gif" title="vorgemerkt';}
 elseif($sSta=='2'){$sImgSta='RtGn.gif" title="bestätigt';}
 elseif($sSta=='-'){$sImgSta='RotX.gif" title="Widerruf vorgemerkt';}
 elseif($sSta=='*'){$sImgSta='RtGnX.gif" title="Widerruf bestätigt';}
 elseif($sSta=='7'){$sImgSta='Glb.gif" title="Warteliste';}
?>

<form name="ZusageForm" action="zusageAendern.php" method="post">
<input type="hidden" name="kal_Num" value="<?php echo $sZId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Zusage-Nr.</td>
 <td><?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[0])?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Termin-Nr.</td>
 <td><input style="width:7em" type="text" name="kal_F1" value="<?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[1])?>" readonly="readonly" /> <a href="zusageAendTrmNr.php?id=<?php echo $aZ[1]?>" target="nummer" onclick="javascript:NumWin(this.href);return false;"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Termin-Nummer &auml;ndern"></a> <span class="admMini">Nr. <?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[1])?> nur bei Umbuchung auf einen anderen Termin ändern</span></td>
</tr>
<tr class="admTabl">
 <td width="10%">Terminzeit</td>
 <td><div<?php if(isset($aFehl[2])) echo ' class="admFehl"'?>><input style="width:7em" type="text" name="kal_F2" value="<?php echo $aZ[2]?>" />&nbsp;<span class="admMini"><?php echo KAL_TxFormat.' '.fKalDatumsFormat()?></span> &nbsp; <input style="width:7em" type="text" name="kal_F3" value="<?php echo $aZ[3]?>" />&nbsp;<span class="admMini"><?php echo KAL_TxFormat.' '.KAL_TxSymbUhr?></span></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">Veranstaltung</td>
 <td><div<?php if(isset($aFehl[4])) echo ' class="admFehl"'?>><input style="width:99.5%" type="text" name="kal_F4" value="<?php echo str_replace('`,',';',$aZ[4]) ?>" /></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">Buchungszeit</td>
 <td><?php echo fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10) ?></td>
</tr>
<tr class="admTabl">
 <td width="10%">Benutzer</td>
 <td><div<?php if(isset($aFehl[7])) echo ' class="admFehl"'?>><input style="width:7em" type="text" name="kal_F7" value="<?php echo sprintf('%0'.KAL_NummerStellen.'d',$aZ[7]) ?>" /></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">E-Mail</td>
 <td><div<?php if(isset($aFehl[8])) echo ' class="admFehl"'?>><input style="width:50%" type="text" name="kal_F8" value="<?php echo $aZ[8] ?>" /></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">Status <img src="<?php echo $sHttp?>grafik/punkt<?php echo $sImgSta?>" width="12" height="12" border="0"></td>
 <td><input class="admRadio<?php if($aZ[6]=='1') echo '" checked="checked'?>" type="radio" name="kal_F6" value="1" > gültig/bestätigt &nbsp;
 <input class="admRadio<?php if($aZ[6]=='0') echo '" checked="checked'?>" type="radio" name="kal_F6" value="0" > vorgemerkt/unbestätigt &nbsp;
 <input class="admRadio<?php if($aZ[6]=='-') echo '" checked="checked'?>" type="radio" name="kal_F6" value="-" > Widerruf vorgemerkt<?php if(KAL_DirektZusage==2){?><br>
 <input class="admRadio<?php if($aZ[6]=='2') echo '" checked="checked'?>" type="radio" name="kal_F6" value="2" > teilbestätigt (vom Besucher) &nbsp;
 <input class="admRadio<?php if($aZ[6]=='*') echo '" checked="checked'?>" type="radio" name="kal_F6" value="*" > Widerruf teilbestätigt (vom Besucher)<?php }?> &nbsp;
 <input class="admRadio<?php if($aZ[6]=='7') echo '" checked="checked'?>" type="radio" name="kal_F6" value="7" > Warteliste</td>
</tr>
<?php
 for($i=9;$i<=$nZusageFelder;$i++){
  $sNam=str_replace('`,',';',$aZusageFelder[$i]); $t=$aZusageFeldTyp[$i]; $sWidth='99.5%'; $sKap='';
  if($t=='n'||$t=='j') $sWidth='7em';
  if($sNam=='ANZAHL'&&strlen(KAL_ZusageNameAnzahl)>0){$sNam=KAL_ZusageNameAnzahl; $sWidth='7em'; $sKap=' '.$nSumZ.'/'.$nKapaz;}
  if($t!='a'&&$t!='j'){
   if($t=='n') $sWidth='7em';
   elseif($t=='w'){
    $s=(float)$aZ[$i];
    if($s!=0||!KAL_PreisLeer) $aZ[$i]=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,''); else $aZ[$i]='';
    $sKap=' '.KAL_Waehrung; $sWidth='7em';
   }
   $sInp='<input name="kal_F'.$i.'" type="text" value="'.str_replace('`,',';',$aZ[$i]).'" style="width:'.$sWidth.'" />'.$sKap;
  }else{
   if($t=='a'){
    $aAww=explode('|',trim($aZusageAuswahl[$i])); $nAww=count($aAww); $sAwo='';
    for($j=0;$j<$nAww;$j++) $sAwo.='<option value="'.$aAww[$j].($aZ[$i]==str_replace('`,',';',$aAww[$j])?'" selected="selected':'').'">'.$aAww[$j].'</option>';
   }elseif($t=='j') $sAwo='<option value="J'.($aZ[$i]=='J'?'" selected="selected':'').'">'.KAL_TxJa.'</option><option value="N'.($aZ[$i]=='N'?'" selected="selected':'').'">'.KAL_TxNein.'</option>';
   $sInp='<select name="kal_F'.$i.'" style="min-width:7em" size="1"><option value="">---</option>'.$sAwo.'</select>';
  }
?>
<tr class="admTabl">
 <td width="10%"><?php echo $sNam ?></td>
 <td><div<?php if(isset($aFehl[$i])) echo ' class="admFehl"'?>><?php echo $sInp;?></div></td>
</tr>
<?php }?>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form><br>
<?php }?>
<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]</p>

<p class="admMeld">Detailinformationen zum Termin-Nr. <i><?php echo (isset($aZ[1])?sprintf('%0'.KAL_NummerStellen.'d',$aZ[1]):'???')?></i></p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Termin-Nr.</td>
 <td><?php echo (count($aT)>1?sprintf('%0'.KAL_NummerStellen.'d',$aT[0]):'Termin nicht vorhanden!')?></td>
</tr>
<?php
if(count($aT)>1){
  array_splice($aT,1,1); if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld;
  for($i=1;$i<$nFelder;$i++){
   $t=$kal_FeldType[$i];
   if(($kal_DetailFeld[$i]>0&&$t!='p'&&$kal_FeldName[$i]!='TITLE'&&substr($kal_FeldName[$i],0,5)!='META-')||$t=='v'){
    if($s=str_replace('\n ',NL,str_replace('`,',';',$aT[$i]))){
     switch($t){
      case 't': $s=fKalBB($s); break; //Text
      case 'a': case 'k': case 'o': break; //Aufzählung/Kategorie so lassen
      case 'd': case '@': $w=trim(substr($s,11)); //Datum
       $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
       switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
        case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
        case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
       }
       $s=$s1.$v.$s2.$v.$s3;
       if($t=='d'){
        if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=$kal_WochenTag[$w].' '.$s; else $s.=' '.$kal_WochenTag[$w];}
       }else{$s.=' '.$w;}
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
       foreach($aL as $w){
        $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w);
        $v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" style="margin-right:4px;" title="'.$u.'" />';
        $s.='<a title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="_blank">'.$v.$u.'</a>, ';
       }$s=substr($s,0,-2); break;
      case 'e':
       if(!KAL_SQL) $s=fKalDeCode($s);
       $s='<a href="mailto:'.$s.'" target="_blank"><img src="'.$sHttp.'grafik/iconMail.gif" width="16" height="16" border="0" title="'.$s.'"></a>&nbsp;<a href="mailto:'.$s.'" target="_blank">'.$s.'</a>';
       break;
      case 'c':
       if(!KAL_SQL) $s=fKalDeCode($s);
       if(file_exists('eingabeKontakt.php')) $s='<a href="eingabeKontakt.php?kal_Num='.$aT[0].($sQ?'&amp;'.$sQ:'').'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="'.$s.'"> '.$s.'</a>';
       break;
      case 'b': //Bild
       $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$aT[0].'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
       $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.substr($s,strpos($s,'-')+1,-4).'" />';
       break;
      case 'f': //Datei
       $s='<a class="kalText" href="'.$sHttp.KAL_Bilder.$aT[0].'~'.$s.'" target="_blank">'.$s.'</a>'; break;
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
     echo "\n".'<tr class="admTabl">';
     echo "\n".' <td width="10%" valign="top">'.$kal_FeldName[$i].'</td>';
     echo "\n".' <td>'.$s."</td>\n</tr>";
    }
   }
  }
}?>
</table><br>
<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]</p>

<?php
echo fSeitenFuss();

if($bMail&&(KAL_ZusageAAendInfoZs||KAL_ZusageAAendInfoAu)){ //Aenderungsmail
 $sZDat=fKalZusagePlainText($aE,$aZusageFelder); $sWww=fKalWww();
 $aD=fKalTerminPlainText($aT,$DbO); $sTDat=$aD[0]; $sKontaktEml=$aD[1];
 require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
 if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
 $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
 $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageAenABtr);
 $Mailer->Text=str_replace('\n ',"\n",str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,KAL_TxZusageAenAMTx))));
 if(KAL_ZusageAAendInfoZs){ //an Zusagenden
  $Mailer->AddTo($aZ[8]); $Mailer->SetReplyTo($aZ[8]); $Mailer->Send(); $Mailer->ClearTo();
 }
 if(KAL_ZusageAAendInfoAu&&$sKontaktEml){ //an Terminautor
  $Mailer->AddTo($sKontaktEml); $Mailer->SetReplyTo($sKontaktEml); $Mailer->Send();
 }
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

function fKalZusagePlainText($aZ,$kal_ZusageFelder){
 $nZusageFelder=count($kal_ZusageFelder);
 $sZ='STATUS: ';
 switch($aZ[6]){
  case '1': $sZ.=KAL_TxZusage1Status; break; case '2': $sZ.=KAL_TxZusage2Status; break; case '0': $sZ.=KAL_TxZusage0Status; break;
  case '-': $sZ.=KAL_TxZusage3Status; break; case '*': $sZ.=KAL_TxZusage4Status; break; case '7': $sZDat.=KAL_TxZusage7Status; break; 
  default: $sZ.='unklar??';
 }
 $sZ.="\n".'ID-'.sprintf('%04d',$aZ[0]).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
 $sZ.="\n".strtoupper(str_replace('`,',';',$kal_ZusageFelder[2])).': '.$aZ[2];
 for($i=3;$i<$nZusageFelder;$i++){
  if($i!=5&&$i!=6&&($s=trim($aZ[$i]))){
   $sFN=str_replace('`,',';',$kal_ZusageFelder[$i]);
   if($sFN=='ANZAHL') if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl;
   $sZ.="\n".strtoupper($sFN).': '.($i!=7?str_replace('`,',';',$s):sprintf('%04d',$s));
  }
 }
 return $sZ;
}

function fKalTerminPlainText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.(isset($aT[0])?$aT[0]:'??'); $sKontaktEml=''; $sAutorEml=''; $sErsatzEml='';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',(isset($aT[$i])?$aT[$i]:'')); $sFN=$kal_FeldName[$i];
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
      $s=(float)$s;
      if($s>0||!KAL_PreisLeer){
       $u=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $u.=' '.KAL_Waehrung;
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