<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusage neu anlegen','','ZZl');

$nFelder=count($kal_FeldName); $nSumZ=0; $nKapaz=0; $nKapGrenze=0; $nZId=0; $aT=array(); $aZ=array(0); $aFehl=array(); $bMail=false; $bDo=true;
$aZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';'); $bKapMaximum=false; $bKapGrenze=false;
$aZusageFeldTyp=explode(';',KAL_ZusageFeldTyp); if(strpos(KAL_ZusageFeldTyp,'a')) $aZusageAuswahl=explode(';',KAL_ZusageAuswahl);
$nAnzahlPos=(int)array_search('ANZAHL',$aZusageFelder); if($nKapazPos=(int)array_search('KAPAZITAET',$kal_FeldName)) $nKapazPos++;

if(!$sQ=$_SERVER['QUERY_STRING']) $sQ='kal_Num=0&amp;'.(isset($_POST['kal_Qry'])?$_POST['kal_Qry']:''); $sQLst='';
if($p=strpos($sQ,'kal_',8)) $sQ=substr($sQ,$p); else $sQ='';

if($sTId=(isset($_GET['kal_Trm'])?$_GET['kal_Trm']:(isset($_POST['kal_Trm'])?$_POST['kal_Trm']:''))){
 if(!KAL_SQL){ //Textdaten holen
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $s=$sTId.';'; $l=strlen($s); //Termin holen
  for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$l)==$s){
   $aT=explode(';',rtrim($aD[$i]));
   if($nKapazPos){$nKapaz=$aT[$nKapazPos]; if($p=strpos($nKapaz,'(')){$nKapGrenze=(int)substr($nKapaz,$p+1); $nKapaz=(int)$nKapaz;}}
   break;
  }
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD); $aZ[0]=(int)substr($aD[0],7,9)+1;
  if($nAnzahlPos>0){ //Zusagen summieren
   for($i=1;$i<$nSaetze;$i++){
    $t=$aD[$i]; $p=strpos($t,';'); if(substr($t,$p+1,$l)==$s){$a=explode(';',rtrim($t)); $nSumZ+=$a[$nAnzahlPos];}
   }
  }
 }elseif($DbO){
  if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' WHERE id="'.$sTId.'"')){ //Termin
   $aT=$rR->fetch_row(); $rR->close();
   if($nKapazPos){$nKapaz=$aT[$nKapazPos]; if($p=strpos($nKapaz,'(')){$nKapGrenze=(int)substr($nKapaz,$p+1); $nKapaz=(int)$nKapaz;}}
   if($nAnzahlPos) if($rR=$DbO->query('SELECT COUNT(nr),SUM(dat_'.$nAnzahlPos.') FROM '.KAL_SqlTabZ.' WHERE termin='.$sTId)){ //Zusagensumme
    $a=$rR->fetch_row(); $rR->close(); $nSumZ=$a[1];
   }
   if($rR=$DbO->query('SELECT COUNT(nr),MAX(nr) FROM '.KAL_SqlTabZ)){ //Zusagennr
    $a=$rR->fetch_row(); $rR->close(); $aZ[0]=(int)$a[1]+1;
   }
  }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
 }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 if(count($aT)>0){ //vorbefuellen
  array_splice($aT,1,1); $aZ[1]=$sTId; $kal_ZusageQuellen=explode(';',KAL_ZusageQuellen);
  $aZ[2]=fKalAnzeigeDatum($aT[(KAL_TerminDatumFeld>0?KAL_TerminDatumFeld:1)]);
  $aZ[3]=(KAL_TerminZeitFeld>0?$aT[KAL_TerminZeitFeld]:'');
  $aZ[4]=(KAL_TerminVeranstFeld>0?str_replace('\n ',' ',str_replace("\n",' ',str_replace("\r",'',str_replace('`,',';',$aT[KAL_TerminVeranstFeld])))):'');
  if(strlen($aZ[4])>KAL_ZusageVeranstLaenge) $aZ[4]=substr($aZ[4],0,KAL_ZusageVeranstLaenge).'...';
  $aZ[5]=date('Y-m-d H:i'); $aZ[6]='1'; $aZ[7]=sprintf(' %0'.KAL_NummerStellen.'d',0); $aZ[8]='';
  for($i=9;$i<=$nZusageFelder;$i++){
   $aZ[$i]='';
   if(strpos($kal_ZusageQuellen[$i],'T')){$j=(int)substr($kal_ZusageQuellen[$i],0,-1); $aZ[$i]=(isset($aT[$j])?$aT[$j]:'');}
  }
 }else for($i=0;$i<=$nZusageFelder;$i++) $aZ[$i]='';
 if($_SERVER['REQUEST_METHOD']=='POST'){ //Formular auswerten
  $aZusagePflicht=explode(';',KAL_ZusagePflicht); $aE=$aZ;
  $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F2'])))))); $aE[2]=$s;
  if($s=fKalErzeugeDatum($s)) $aE[2]=substr($s,0,10); else $aFehl[2]=true;
  if(isset($aZ[3])&&$aZ[3]>''){
   $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F3'])))))); $aE[3]=$s;
   $a=explode(':',$s);
   if($a[0]>''&&(int)$a[0]<24&&$a[1]>''&&(int)$a[1]<60) $aE[3]=sprintf('%02d:%02d',$a[0],$a[1]); elseif ($aZusagePflicht[3]) $aFehl[3]=true;
  }
  $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F4'])))))); if($s) $aE[4]=str_replace(';','`,',$s); else $aFehl[4]=true;
  $aE[6]=(isset($_POST['kal_F6'])&&$_POST['kal_F6']>''?substr($_POST['kal_F6'],0,1):'1');
  $aE[7]=sprintf('%0'.KAL_NummerStellen.'d',stripslashes(@strip_tags(trim($_POST['kal_F7']))));
  $s=stripslashes(@strip_tags(str_replace("\n",' ',str_replace("\r",'',str_replace('"',"'",trim($_POST['kal_F8'])))))); $aE[8]=$s;
  if(empty($s)&&$aZusagePflicht[8]) $aFehl[8]=true;
  elseif($s>''&&!preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $aFehl[8]=false;
  for($i=9;$i<=$nZusageFelder;$i++){
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
   if(KAL_Zusagen){
    if(!KAL_SQL){ //Textdaten
     $s=rtrim($aD[0]); if(substr($s,0,7)=='Nummer_') $nZId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
     else for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,12); $nZId=max((int)substr($s,0,strpos($s,';')),$nZId);}
     $s='Nummer_'.(++$nZId); for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.str_replace(';','`,',$aZusageFelder[$i]); $aD[0]=$s."\n";
     $sEml=$aE[8]; $aE[8]=fKalEnCode($sEml); $s=$nZId; $aE[0]=$s; for($i=1;$i<=$nZusageFelder;$i++) $s.=';'.$aE[$i]; $aE[8]=$sEml;
     if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Zusage,'w')){
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL.$s.NL); fclose($f);
      $Msg='<p class="admErfo">Die Zusagedaten wurden gespeichert!</p>'; $bMail=true; $bDo=false;
     }else $Msg='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_Zusage.'</i>',KAL_TxDateiRechte).'</p>';
    }elseif($DbO){ // SQL
     $sF='termin,datum,zeit,veranstaltung,buchung,aktiv,benutzer,email';
     $sV='"'.$aE[1].'","'.$aE[2].'","'.$aE[3].'","'.str_replace('`,',';',$aE[4]).'","'.$aE[5].'","'.$aE[6].'","'.$aE[7].'","'.$aE[8].'"';
     for($i=9;$i<=$nZusageFelder;$i++){$sV.=',"'.str_replace('"','\"',str_replace('`,',';',$aE[$i])).'"'; $sF.=',dat_'.$i;}
     if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' ('.$sF.') VALUES('.$sV.')')){
      if($nZId=$DbO->insert_id){
       $aE[0]=$nZId; $Msg='<p class="admErfo">Die Zusagedaten wurden eingespeichert!</p>'; $bMail=true; $bDo=false;
      }else $Msg='<p class="admFehl">'.KAL_TxSqlEinfg.'</p>';;
     }else $Msg='<p class="admFehl">'.KAL_TxSqlAendr.'</p>';
    }else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
    if($nAnzahlPos){
     $nSumZAlt=$nSumZ; $nSumZ+=(int)$aE[$nAnzahlPos]-(int)$aZ[$nAnzahlPos];
     if($nSumZAlt<$nKapaz&&$nSumZ>=$nKapaz) $bKapMaximum=true;
     elseif($nSumZAlt<$nKapGrenze&&$nSumZ>=$nKapGrenze) $bKapGrenze=true;
    }
   }else $Msg='<p class="admFehl">N'.'ur i'.'n der Vo'.'l'.'lve'.'rs'.'ion des Zu'.'sagemo'.'d'.'uls!</p>';
  }else $Msg='<p class="admFehl">Korrigieren Sie die markierten Felder!</p>';
  if(!isset($aFehl[2])) $aE[2]=fKalAnzeigeDatum($aE[2]); $aZ=$aE;
 }

 //Scriptausgabe
 if(!$Msg) $Msg='<p class="admMeld">Tragen Sie eine Terminzusage zum Termin <i>'.sprintf('%0'.KAL_NummerStellen.'d',$sTId).'</i> ein.</p>';
 echo $Msg.NL;

 if($p=strpos('#'.$sQ,'kal_Lst')){
  if(substr($sQ,(--$p)-5,1)=='&') $i=5; elseif(substr($sQ,$p-1,1)=='&') $i=1; else $i=0;
  $bZurTrmLst=true; $sQL=substr_replace($sQ,'',$p-$i,9+$i);
 }else{$bZurTrmLst=false; $sQL=$sQ;}

 $sSta=$aZ[6]; $sImgSta='Grn.gif" title="gültig'; if($sSta=='0'){$sImgSta='Rot.gif" title="vorgemerkt';} if($sSta=='7'){$sImgSta='Glb.gif" title="Warteliste';}
 if(count($aT)>0){
?>

<form name="ZusageListe" action="zusageEingabe.php" method="post">
<input type="hidden" name="kal_Trm" value="<?php echo $sTId?>" />
<input type="hidden" name="kal_Qry" value="<?php echo $sQ?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Zusage-Nr.</td>
 <td><span style="color:#555555"><?php echo sprintf(' %0'.KAL_NummerStellen.'d',$aZ[0])?></span></td>
</tr>
<tr class="admTabl">
 <td width="10%">Terminzeit</td>
 <td><div<?php if(isset($aFehl[2])) echo ' class="admFehl"'?>><input style="width:7em;color:#555555;" type="text" name="kal_F2" value="<?php echo $aZ[2]?>" />&nbsp;<span class="admMini"><?php echo KAL_TxFormat.' '.fKalDatumsFormat()?></span> &nbsp; <?php if(isset($aZ[3])&&$aZ[3]>''){?><input style="width:7em;color:#555555;" type="text" name="kal_F3" value="<?php echo $aZ[3]?>" />&nbsp;<span class="admMini"><?php echo KAL_TxFormat.' '.KAL_TxSymbUhr?></span><?php }?></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">Veranstaltung</td>
 <td><div<?php if(isset($aFehl[4])) echo ' class="admFehl"'?>><input style="width:100%;color:#555555;" type="text" name="kal_F4" value="<?php echo str_replace('`,',';',$aZ[4]) ?>" /></div></td>
</tr>
<tr class="admTabl">
 <td width="10%">Buchungszeit</td>
 <td><span style="color:#555555"><?php echo fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10) ?></span></td>
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
 <input class="admRadio<?php if($aZ[6]=='7') echo '" checked="checked'?>" type="radio" name="kal_F6" value="7" > Warteliste</td>
</tr>
<?php
 for($i=9;$i<=$nZusageFelder;$i++){
  $sNam=str_replace('`,',';',$aZusageFelder[$i]); $t=$aZusageFeldTyp[$i]; $sWidth='100%'; $sKap='';
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
<?php if($bDo){ ?><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p><?php } ?>
</form><br>
<?php
 }else echo '<p class="admFehl">Termin nicht gefunden!</p>';
}
?>
<p align="center">[ <a href="<?php echo($bZurTrmLst?'l':'zusageL')?>iste.php<?php echo($sQL?'?'.$sQL:'')?>">zurück zur Liste</a> ]</p>

<p class="admMeld">Detailinformationen zum Termin-Nr. <i><?php echo ($sTId?sprintf('%0'.KAL_NummerStellen.'d',$sTId):'???')?></i></p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%">Termin-Nr.</td>
 <td><?php echo (count($aT)>1?sprintf('%0'.KAL_NummerStellen.'d',$aT[0]):'Termin nicht vorhanden!')?></td>
</tr>
<?php
if(count($aT)>1){
  if(KAL_InfoNDetail) $kal_DetailFeld=$kal_NDetailFeld; $nFarb=1;
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

if(!$bDo&&(KAL_ZusageEintragMail||KAL_ZusageNeuInfoAut)){ //Eintragsmails
 $aD=fKalZusageText($aE,$aZusageFelder,$aZusageFeldTyp); $sZDat=$aD[0]; $sHZ=$aD[1]; $sWww=fKalWww();
 $aD=fKalTerminText($aT,$DbO); $sTDat=$aD[0]; $sHT=$aD[1]; $sKontaktEml=$aD[2];
 require_once(KAL_Pfad.'class.htmlmail.php'); $Mailer=new HtmlMail();
 if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
 $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t=''; $Mailer->SetFrom($s,$t);
 if(KAL_ZusageEintragMail){ //an Zusagenden
  $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageEintrBtr); $sTxt=KAL_TxZusageEintrMTx; $sLkF='';
  for($i=2;$i<=$nZusageFelder;$i++) $sTxt=str_replace('{'.$aZusageFelder[$i].'}',$aE[$i],$sTxt);

  if($sSta=='0'){
   $sLnk=(KAL_ZusageLink==''?$sHttp.'kalender.php?':KAL_ZusageLink.(!strpos(KAL_ZusageLink,'?')?'?':'&amp;'));
   if(strpos($sLnk,'ttp')!=1||strpos($sLnk,'://')===false) $sLnk=substr(KAL_Url,0,strpos(KAL_Url,':')).'://'.fKalHost().$sLnk;
   srand((double)microtime()*1000000); $sCod=rand(100,255); $nS=9;
   $s=KAL_Schluessel.$sCod.$nZId; for($i=strlen($s)-1;$i>=0;$i--) $nS+=substr($s,$i,1);
   $sLkF=$sLnk.'kal_Aktion=zusage_'.sprintf('%02X',$nS).sprintf('%02X',$sCod).$nZId;
  }

  $sHtml=str_replace("\r",'','<!DOCTYPE HTML>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
 <link rel="stylesheet" type="text/css" href="'.$sHttp.'kalStyles.css">
</head>
<body class="kalSeite">');

  $sHTx="\n<div style=\"margin-top:12px;\">\n".str_replace('\n ',"\n</div>\n<div style=\"margin-top:12px;\">\n",$sTxt)."\n</div>\n";
  $sHTx=str_replace('#L','<a href="'.$sLkF.'">'.$sLkF.'</a>',$sHTx);
  $sHTx=str_replace('#A',$sWww,$sHTx);
  $sHTx=str_replace('#D',trim($sHT),str_replace('#Z',trim($sHZ),$sHTx));
  $sHTx=str_replace("\r",'',$sHtml)."\n".str_replace(' style="width:100%"','',str_replace(' style="width:15%"','',$sHTx))."\n</body>\n</html>";

  $Mailer->PlainText=str_replace('\n ',"\n",str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#L',$sLkF,str_replace('#A',$sWww,str_replace('\n ',"\n",$sTxt))))));
  $Mailer->HtmlText=$sHTx; //ISO
  $Mailer->AddTo($aZ[8]); $Mailer->SetReplyTo($aZ[8]); $Mailer->Send(); $Mailer->ClearTo();
 }
 if(KAL_ZusageNeuInfoAut&&$sKontaktEml){ //an Terminautor
  $Mailer->Subject=str_replace('#A',$sWww,KAL_TxZusageInfoBtr); $sTxt=KAL_TxZusageInfoMTx;
  $Mailer->Text=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",$sTxt))));
  $Mailer->AddTo($sKontaktEml); $Mailer->SetReplyTo($sKontaktEml); $Mailer->Send();
 }
 if($bKapMaximum&&(KAL_ZusageMaxKapInfoAut&&!empty($sKontaktEml))||$bKapGrenze&&(KAL_ZusageGrenzeInfoAut&&!empty($sKontaktEml))){ //Kapazitaets-Mail an Admin / Besitzer
  $sBtr=str_replace('#A',$sWww,($bKapMaximum?KAL_TxZusageMaxKapBtr:KAL_TxZusageGrenzeBtr)); $sTxt=KAL_TxZusageMaxKapMTx;
  require_once(KAL_Pfad.'class.plainmail.php'); $Mailer=new PlainMail();
  if(KAL_Smtp){$Mailer->Smtp=true; $Mailer->SmtpHost=KAL_SmtpHost; $Mailer->SmtpPort=KAL_SmtpPort; $Mailer->SmtpAuth=KAL_SmtpAuth; $Mailer->SmtpUser=KAL_SmtpUser; $Mailer->SmtpPass=KAL_SmtpPass;}
  $s=KAL_Sender; if($p=strpos($s,'<')){$t=substr($s,0,$p); $s=substr(substr($s,0,-1),$p+1);} else $t='';
  $Mailer->Subject=$sBtr; $Mailer->SetFrom($s,$t); $Mailer->SetReplyTo($aE[8]);
  if(strlen(KAL_EnvelopeSender)>0) $Mailer->SetEnvelopeSender(KAL_EnvelopeSender);
  $sTxt=str_replace('#N',$nSumZ,str_replace('#K',$nKapaz,$sTxt));
  $Mailer->Text=str_replace('#D',trim($sTDat),str_replace('#Z',$sZDat,str_replace('#A',$sWww,str_replace('\n ',"\n",$sTxt))));
  if($bKapMaximum){$Mailer->AddTo($sKontaktEml); $Mailer->Send();} elseif($bKapGrenze){$Mailer->AddTo($sKontaktEml); $Mailer->Send();}
 }
} //Ende

function fKalZusageText($aZ,$aZusageFelder,$aZusageFeldTyp){
 $nZusageFelder=count($aZusageFelder); $nFarb=1;
 $sZ= 'ID-'.sprintf('%04d',$aZ[0]).': '.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10);
 $sZ.="\n".strtoupper(str_replace('`,',';',$aZusageFelder[2])).': '.$aZ[2];
 $sH= "\n".'<div class="kalTabl">';
 $sH.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
 $sH.="\n".' <div class="kalTbSp1"><b>ID-'.sprintf('%04d',$aZ[0]).'</b></div>';
 $sH.="\n".' <div class="kalTbSp2">'.fKalAnzeigeDatum($aZ[5]).substr($aZ[5],10)."</div>\n</div>";
 $sH.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
 $sH.="\n".' <div class="kalTbSp1">'.$aZusageFelder[2].'</div>';
 $sH.="\n".' <div class="kalTbSp2">'.$aZ[2]."</div>\n</div>";
 for($i=3;$i<$nZusageFelder;$i++){
  if($i!=5&&$i!=6&&($s=trim($aZ[$i]))){
   $sFN=str_replace('`,',';',$aZusageFelder[$i]);
   if($sFN=='ANZAHL') if(strlen(KAL_ZusageNameAnzahl)>0) $sFN=KAL_ZusageNameAnzahl;
   if($i!=7) $s=str_replace('`,',';',$s); else $s=sprintf('%04d',$s);
   if($aZusageFeldTyp[$i]=='w'){
    $s=(float)$s;
    if($s!=0||!KAL_PreisLeer) $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen).' '.KAL_Waehrung; else $s='';
   }
   $sZ.="\n".strtoupper($sFN).': '.$s;
   $sH.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
   $sH.="\n".' <div class="kalTbSp1">'.$sFN.'</div>';
   $sH.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
  }
 }
 $sH.="\n</div>\n";
 return array($sZ,$sH);
}

function fKalTerminText($aT,$DbO=NULL){ //Termindetails aufbereiten
 global $kal_FeldName, $kal_FeldType, $kal_DetailFeld, $kal_NDetailFeld, $kal_WochenTag, $sHttp;
 $aInfoFld=(KAL_InfoNDetail?$kal_NDetailFeld:$kal_DetailFeld); $nFelder=count($kal_FeldName);
 $sT="\n".strtoupper($kal_FeldName[0]).': '.$aT[0]; $sKontaktEml=''; $sAutorEml=''; $sErsatzEml=''; $sId=$aT[0];
 $sH="\n".'<div class="kalTabl">'; $nFarb=1;
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $s=str_replace('`,',';',$aT[$i]); $sFN=$kal_FeldName[$i];
  if(($aInfoFld[$i]>0&&$t!='p'&&$t!='c'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE')||$t=='v'){
   if($u=$s){
    switch($t){
     case 't': $s=fKalBB($s); $u=@strip_tags($s); break; //Text
     case 'm': if(KAL_InfoMitMemo){$s=fKalBB($s); $u=@strip_tags(fKalBB($s));}else{$s=''; $u='';} break; //Memo
     case 'a': case 'k': case 'o': break;  //Aufzaehlung/Kategorie so lassen
     case 'd': case '@': $u=fKalAnzeigeDatum($s); $w=trim(substr($s,11)); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0) if(KAL_MitWochentag<2) $u=$kal_WochenTag[$w].' '.$u; else $u.=' '.$kal_WochenTag[$w];
      }else{if($w) $u.=' '.$w;}
      $s=$u; break;
     case 'z': $u=$s.' '.KAL_TxUhr; $s.=' '.KAL_TxUhr; break; //Uhrzeit
     case 'w': //Waehrung
      $s=(float)$s;
      if($s>0||!KAL_PreisLeer){
       $s=number_format($s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen);
       if(KAL_Waehrung){$u=$s.' '.KAL_Waehrung; $s.=' '.KAL_Waehrung;}
      }else if(KAL_ZeigeLeeres){$s='&nbsp;'; $u=' ';}else{$s=''; $u='';}
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1));
      if($s=='J'||$s=='Y'){$s=KAL_TxJa; $u=KAL_TxJa;}elseif($s=='N'){$s=KAL_TxNein; $u=KAL_TxNein;}
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $u=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $u=str_replace('.',KAL_Dezimalzeichen,$s); $s=$u;
      break;
     case 'e': //E-Mail
      if($s) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($s))) $sErsatzEml=$s;
      $u=''; $s=''; break;
     case 'l': //Link
      $aL=explode('||',$s); $s=''; $z='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $u=(isset($aI[1])?$aI[1]:$w); $z.=$w.', ';
       $v='<img src="'.$sHttp.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" width="16" height="16" border="0" align="top" style="margin-right:4px;" title="'.$u.'" alt="'.$u.'" />';
       $s.='<a class="kalText" title="'.$u.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:':(($p=strpos($w,'tp'))&&strpos($w,'://')>$p?'':'http://')).$w.'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_DetailLinkSymbol?'</a>  ':$u.'</a>, ');
      }$s=substr($s,0,-2); $u=substr($z,0,-2); break;
      if($u) if(empty($sErsatzEml)) if(preg_match('/^([0-9a-z~_-]+\.)*[0-9a-z~_-]+@[0-9a-zäöü_-]+(\.[0-9a-zäöü_-]+)*\.[a-z]{2,16}$/',strtolower($u))) $sErsatzEml=$u;
      break;
     case 'b':
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s); $u=$sHttp.$s; $w=substr($s,strpos($s,'-')+1,-4);
      $s='<img src="'.$sHttp.$s.'" '.$aI[3].' border="0" title="'.$w.'" alt="'.$w.'" />';
     break; //Bild
     case 'f': //Datei
      $u=$sHttp.KAL_Bilder.$sId.'~'.$s; $s='<a class="kalText" href="'.$sHttp.KAL_Bilder.$sId.'~'.$s.'" target="_blank">'.$s.'</a>'; break;
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
      }else $s=KAL_TxAutor0000; $u=$s;
      break;
     default: {$s=''; $u='';}
    }//switch
   }
   if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz;
   if($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(strlen($u)>0) $sT.="\n".strtoupper($sFN).': '.$u;
   if(strlen($s)>0){
    $sH.="\n".'<div class="kalTbZl'.$nFarb.'">'; if(--$nFarb<=0) $nFarb=2;
    $sH.="\n".' <div class="kalTbSp1">'.$sFN.'</div>';
    $sH.="\n".' <div class="kalTbSp2">'.$s."</div>\n</div>";
   }
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
 $sH.="\n</div>\n";
 if(empty($sKontaktEml)) if($sAutorEml) $sKontaktEml=$sAutorEml; else $sKontaktEml=$sErsatzEml;
 return array($sT,$sH,$sKontaktEml);
}

function fKalWww(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
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