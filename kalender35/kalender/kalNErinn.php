<?php
function fKalSeite(){ //Benutzerdaten
 global $kal_NutzerFelder, $kal_FeldName, $kal_FeldType, $kal_ListenFeld, $kal_SpaltenStil, $kal_Symbole, $kal_WochenTag;

 $Et=''; $Es='Fehl'; $sQ='';

 //Query_Strings für Links vorbereiten
 $sRueckw=(isset($_GET['kal_Rueck'])?fKalRq1($_GET['kal_Rueck']):'');
 $nStart=(isset($_GET['kal_Start'])?(int)$_GET['kal_Start']:(isset($_POST['kal_Start'])?(int)$_POST['kal_Start']:1));
 if($sRueckw=='1'&&!KAL_Rueckwaerts) $sQ.='&amp;kal_Rueck=1';
 elseif($sRueckw==='0'&&KAL_Rueckwaerts) $sQ.='&amp;kal_Rueck=0';

 array_splice($kal_NutzerFelder,1,1); $nFelder=count($kal_NutzerFelder);

 $DbO=NULL; //SQL-Verbindung oeffnen
 if(KAL_SQL){
  $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else{$DbO=NULL; $SqE=KAL_TxSqlVrbdg;}
 }

 //Session pruefen
 $bSes=false; $sSession=substr(KAL_Session,0,29);
 if($sSes=substr($sSession,17,12)){
  $nId=(int)substr($sSes,0,4); $nTm=(int)substr($sSes,4);
  if((time()>>6)<=$nTm){ //nicht abgelaufen
   if(!KAL_SQL){
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD); $nId=$nId.';'; $p=strlen($nId);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nId){
     if(substr($aD[$i],$p,8)==sprintf('%08d',$nTm)){
      $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $bSes=true;
      $a[2]=fKalDeCode($a[2]); $a[3]=fKalDeCode($a[3]); $a[4]=fKalDeCode($a[4]);
      for($j=5;$j<$nFelder;$j++){
       $a[$j]=str_replace('`,',';',$a[$j]);
       if(KAL_LZeichenstz>0) if(KAL_LZeichenstz==2) $a[$j]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$j]); else $a[$j]=html_entity_decode($a[$j]);
      }
     }else $Et=KAL_TxSessionUngueltig;
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'" AND session="'.$nTm.'"')){
     if($rR->num_rows==1){
      $bSes=true; $a=$rR->fetch_row(); array_splice($a,1,1);
      if(KAL_LZeichenstz>0) for($i=2;$i<$nFelder;$i++) if(KAL_LZeichenstz==2) $a[$i]=iconv('UTF-8','ISO-8859-1//TRANSLIT',$a[$i]); else $a[$i]=html_entity_decode($a[$i]);
     }else $Et=KAL_TxSessionUngueltig;
     $rR->close();
    }else $Et=KAL_TxSqlFrage;
   }
  }else $Et=KAL_TxSessionZeit;
 }else $Et=KAL_TxSessionUngueltig;

 $aE=array(); $sEml=(isset($a[4])?trim($a[4]):''); //Erinnerungen holen
 if($bSes){
  $kal_ListenFeld=$GLOBALS['kal_NListenFeld'];
  $sId=(isset($a[0])?$a[0]:''); $i=1;
  if(!KAL_SQL){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD); $sE=';'.$sEml;
   for($i=1;$i<$nSaetze;$i++) if(strpos($aD[$i],$sE)>0){
    $s=substr($aD[$i],0,17); $sD=substr($s,0,10); $s=substr($s,11,6);
    $aE[$sD.'_'.$i]=substr($s,0,strpos($s,';')); $aD[$i]=rtrim($aD[$i])."\n";
   }
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT id,datum,termin FROM '.KAL_SqlTabE.' WHERE email="'.$sEml.'"')){
    while($a=$rR->fetch_row()) if($a[0]>0) $aE[$a[1].'_'.($i++)]=$a[2]; $rR->close();
   }else $Et=KAL_TxSqlFrage;
  }
 }

 $sLsch=''; $aLsch=array();// Erinnerungen loeschen
 if($_SERVER['REQUEST_METHOD']=='POST'){
  $Et=KAL_TxNeUnveraendert; $Es='Meld';
  if(isset($_POST['kal_Lsch_x'])||isset($_POST['kal_Lsch_y'])){
   reset($_POST); $n=0;
   if(isset($_POST['kal_LschNun'])&&$_POST['kal_LschNun']=='1'){
    if(!KAL_SQL){
     foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_')
      if($i=array_search(substr($k,6,10).';'.substr($k,19).';'.$sEml."\n",$aD)){
       $aD[$i]=''; $n++; $s=substr($k,6,15); unset($aE[substr($s,0,strpos($s,'#'))]);
      }
     if($n){
      if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_Erinner,'w')){ //Erinnerungen neu schreiben
       fwrite($f,str_replace("\r",'',rtrim(implode('',$aD)))."\n"); fclose($f);
       $Et=str_replace('#N',$n,KAL_TxNeGeloescht); $Es='Erfo';
      }else{$Et=str_replace('#',KAL_Daten.KAL_Erinner,KAL_TxDateiRechte); $Es='Fehl';}
     }
    }elseif($DbO){ //SQL
     foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_'){
      if($DbO->query('DELETE FROM '.KAL_SqlTabE.' WHERE datum="'.substr($k,6,10).'" AND termin="'.substr($k,strpos($k,'#')+1).'" AND email="'.$sEml.'"')){
       if($DbO->affected_rows>0){$n++; $s=substr($k,6,15); unset($aE[substr($s,0,strpos($s,'#'))]);}
      }
     }
     if($n){$Et=str_replace('#N',$n,KAL_TxNeGeloescht); $Es='Erfo';}
    }
   }else{
    foreach($_POST as $k=>$xx) if(substr($k,0,6)=='kal_L_') $aLsch[substr($k,6)]=true;
    if($n=count($aLsch)){$Et=str_replace('#N',$n,KAL_TxNeLoeschen); $Es='Fehl'; $sLsch='1';}
   }
  }
 }//POST

 $aKalDaten=array(); $aTmp=array(); $aKalSpalten=array(); $nFelder=count($kal_FeldName); //Terminstruktur vorbereiten
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $aKalSpalten[$kal_ListenFeld[$i]]=(($t!='m'&&$t!='g')?$i:-1); //Liste ohne Memos
 }
 $aKalSpalten[0]=0; ksort($aKalSpalten);
 if(in_array(-1,$aKalSpalten)){$j=count($aKalSpalten); for($i=$j-1;$i>0;$i--) if($aKalSpalten[$i]<0) array_splice($aKalSpalten,$i,1);}
 $nSpalten=count($aKalSpalten);

 if($n=count($aE)){ //Termindaten bereitstellen
  if(!KAL_SQL){
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){
    $s=$aD[$i]; $nId=substr($s,0,strpos($s,';')); $k='a';
    while($sW=array_search($nId,$aE)){
     $aTmp[$nId.$k]=array($nId); $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1);
     for($j=1;$j<$nSpalten;$j++) $aTmp[$nId.$k][]=str_replace('\n ',"\n",str_replace('`,',';',$a[$aKalSpalten[$j]]));
     $aTmp[$nId.($k++)][]=$sW; $aE[$sW]='';
    }
   }
  }elseif($DbO){ //SQL
   $t=''; for($j=1;$j<$nSpalten;$j++) $t.=',kal_'.$aKalSpalten[$j];
   if($rR=$DbO->query('SELECT id'.$t.' FROM '.KAL_SqlTabT.' ORDER BY kal_1,id')){
    while($a=$rR->fetch_row()){
     $nId=$a[0]; $k='a';
     while($sW=array_search($nId,$aE)){
      $aTmp[$nId.$k]=array($nId);
      for($j=1;$j<$nSpalten;$j++) $aTmp[$nId.$k][]=str_replace("\r",'',$a[$j]);
      $aTmp[$nId.($k++)][]=$sW; $aE[$sW]='';
     }
    }$rR->close();
   }else $Et=KAL_TxSqlFrage;
  }
  if($sRueckw=='1'||(KAL_Rueckwaerts&&$sRueckw=='')) $aTmp=array_reverse($aTmp);
  $k=0; $nStop=$nStart+KAL_ListenLaenge;
  foreach($aTmp as $i=>$xx) if(++$k<$nStop&&$k>=$nStart) $aKalDaten[]=$aTmp[$i];
 }else{$Et=KAL_TxNeKeineErinn; $Es='Meld';}

 //Seitenausgabe
 if(!$bSes||empty($sId)){$Et=KAL_TxSessionUngueltig; $Es='Fehl';}
 elseif(!$Et){$Et=KAL_TxNeUebersicht; $Es='Meld';}
 $X=' <p class="kal'.$Es.'">'.fKalTx($Et).'</p>';

 if(KAL_NaviOben>0||KAL_NaviUnten>0) $sNavig=fKalNavigator($nStart,count($aTmp),$sQ,'&amp;kal_Aktion=nerinn'.KAL_Session);
 if(KAL_NaviOben) $X.="\n".$sNavig;

 //Daten ausgeben: $i-Index, $j-Spalte, $k-Feld
 $a=array(); $nSpalten=count($aKalSpalten); $kal_FeldName[0]=KAL_TxNr; $bMitID=$kal_ListenFeld[0]>0;

 $X.="\n\n<script>\n function fSelAll(bStat){\n  for(var i=0;i<self.document.DatListe.length;++i)\n   if(self.document.DatListe.elements[i].type==\"checkbox\") self.document.DatListe.elements[i].checked=bStat;\n }\n</script>\n";
 $X.="\n".'<form class="kalForm" name="DatListe" action="'.KAL_Self.(KAL_Query!=''?'?'.substr(KAL_Query,5):'').'" method="post">'.rtrim("\n".KAL_Hidden);
 $X.="\n".'<input type="hidden" name="kal_Aktion" value="nerinn">'."\n".'<input type="hidden" name="kal_Session" value="'.$sSes.'">'."\n".'<input type="hidden" name="kal_Zentrum" value="1">';
 if($nStart>1) $X.="\n".'<input type="hidden" name="kal_Start" value="'.$nStart.'">';
 $X.="\n".'<div class="kalTabl">';

 //Kopfzeile ausgeben
 $X.="\n".' <div class="kalTbZl0">'."\n".'  <div class="kalTbLst">'.fKalTx(KAL_TxErinServ).'</div>'; $aSpTitle=array();
 $t='r'; $w=KAL_TxAufsteigend; $v='&amp;kal_Rueck=';
 if(KAL_Rueckwaerts&&$sRueckw!='0'){$v.='0';}elseif($sRueckw=='1'){$v='';}else{$t='t'; $w=KAL_TxAbsteigend; $v.='1';}
 $t='<img class="kalSorti" src="'.KAL_Url.'grafik/sortier'.$t.'.gif" title="'.fKalTx($w.KAL_TxSortieren).'" alt="'.fKalTx($w.KAL_TxSortieren).'">';
 $t='&nbsp;<a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;kal_Aktion=nerinn'.KAL_Session.$v,5).'">'.$t.'</a>';
 for($j=($bMitID?0:1);$j<$nSpalten;$j++){
  $k=$aKalSpalten[$j]; $sFN=$kal_FeldName[$k];
  if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
  $X.="\n".'  <div class="kalTbLst">'.fKalTx($sFN).($k==1?$t:'').'</div>'; $aSpTitle[$k]=fKalTx($sFN).($k==1?$t:'');
 }
 $X.="\n".' </div>';
 //eventuell Monate holen
 if(KAL_MonatLLang>0) $aMonate=explode(';',';'.(KAL_MonatLLang==2?KAL_TxLMonate:KAL_TxKMonate));
 //alle Datenzeilen ausgeben
 if($sVStil=KAL_ListeVertikal) $sVStil='vertical-align:'.$sVStil.';'; $nKatPos=array_search('k',$kal_FeldType); $nFarb=1;
 if($nStart>1) $sQ='&amp;kal_Start='.$nStart.$sQ;
 foreach($aKalDaten as $a){
  $sId=$a[0];
  $s=$a[$nSpalten]; $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
  switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
   case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
   case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
  }
  $sZl="\n".'  <div class="kalTbLst"><span class="kalTbLst">'.fKalTx(KAL_TxLoeschen).'</span><input class="kalCheck" type="checkbox" name="kal_L_'.$s.'#'.$sId.'" value="1"'.(isset($aLsch[$s.'#'.$sId])?' checked="checked"':'').'>'.$s1.$v.$s2.$v.$s3.'</div>';
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; /* $sStil=$sVStil; */ $sStil=''; $sFS=''; $sKMemo='';
   if(($s=$a[$j])||strlen($s)>0){
    switch($t){
     case 't': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Gastkommentar
     case 'm': if(KAL_ListenMemoLaenge==0) $s=fKalBB(fKalDt($s)); else{$s=fKalBB(fKalDt(fKalKurzMemo($s,KAL_ListenMemoLaenge))); if(substr($s,-4)=='....'){$sKMemo=substr($s,0,-4); $s='....';}} break; //Memo
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie/Postleitzahl
     case 'd': case '@': $w=trim(substr($s,11)); $s=fKalAnzeigeDatum($s); //Datum
      if($t=='d'){
       if(KAL_MitWochentag>0){if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; else $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]);}
      }elseif($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sFS.=' kalTbLsM'; break; //Uhrzeit
     case 'w': //Währung
      if(((float)$s)!=0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sFS.=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein); $sFS.=' kalTbLsM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if(((float)$s)!=0||!KAL_ZahlLeer){
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,KAL_Tausendzeichen); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sFS.=' kalTbLsR';
      }else $s='&nbsp;';
      break;
     case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); $sFS.=' kalTbLsM'; break; //Nummer
     case 'l': //Link
      $aL=explode('||',$s); $s='';
      foreach($aL as $w){
       $aI=explode('|',$w); $w=$aI[0]; $v=fKalDt(isset($aI[1])?$aI[1]:$w); $u=$v;
       if(KAL_LinkSymbol){$v='<img class="kalIcon" src="'.KAL_Url.'grafik/icon'.(strpos($w,'@')&&!strpos($w,'://')?'Mail':'Link').'.gif" title="'.$u.'" alt="'.$u.'">'; $sFS.=' kalTbLsM';}
       $s.='<a class="kalText" title="'.$w.'" href="'.(strpos($w,'@')&&!strpos($w,'://')?'mailto:'.$w:(($p=strpos($w,'tp'))&&strpos($w,'://')>$p||strpos('#'.$w,'tel:')==1?'':'http://').fKalExtLink($w)).'" target="'.(isset($aI[2])?$aI[2]:'_blank').'">'.$v.(KAL_LinkSymbol?'</a>  ':'</a>, ');
      }$s=substr($s,0,-2); break;
     case 'e': //eMail
      $s='<img class="kalIcon" src="'.KAL_Url.'grafik/iconMail.gif" title="'.fKalTx(KAL_TxKontakt).'" alt="'.fKalTx(KAL_TxKontakt).'">';
      $sFS.=' kalTbLsM';
      break;
     case 'u': //Benutzer
      if($nId=(int)$s){
       if(KAL_NutzerListFeld>0){
        $s=KAL_TxAutorUnbekannt;
        if(!KAL_SQL){ //Textdaten
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nNutzerZahl=count($aD); $v=$nId.';'; $p=strlen($v);
         for($n=1;$n<$nNutzerZahl;$n++) if(substr($aD[$n],0,$p)==$v){
          $aN=explode(';',rtrim($aD[$n])); array_splice($aN,1,1);
          if(!$s=$aN[KAL_NutzerListFeld]) $s=KAL_TxAutorUnbekannt; elseif(KAL_NutzerListFeld<5&&KAL_NutzerListFeld>1) $s=fKalDeCode($s); $s=fKalDt($s);
          break;
         }
        }elseif($DbO){ //SQL-Daten
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' WHERE nr="'.$nId.'"')){
          $aN=$rR->fetch_row(); $rR->close();
          if(is_array($aN)){array_splice($aN,1,1); if(!$s=fKalDt($aN[KAL_NutzerListFeld])) $s=KAL_TxAutorUnbekannt;}
          else $s=KAL_TxAutorUnbekannt;
       }}}
      }else $s=KAL_TxAutor0000;
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.fKalDt($w).'" alt="'.fKalDt($w).'">'; $sFS.=' kalTbLsM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $ho=floor((KAL_VorschauHoch-$aI[1])*0.5); $hu=max(KAL_VorschauHoch-($aI[1]+$ho),0);
      if(!KAL_VorschauRahmen) $r=' class="kalTBld"'; else $r=' class="kalVBld" style="width:'.KAL_VorschauBreit.'px;padding-top:'.$ho.'px;padding-bottom:'.$hu.'px;"';
      $w=fKalDt(substr($s,strpos($s,'-')+1,-4));
      $s='<div'.$r.'><img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" title="'.$w.'" alt="'.$w.'"></div>'; $sFS.=' kalTbLsM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3))); $w=fKalDt(strtoupper($w).'-'.KAL_TxDatei);
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' kalTbLsM';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" title="'.$w.'" alt="'.$w.'">';
      $s=$v;
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">';$sFS.=' kalTbLsM';
   }else $s='&nbsp;';
   if($kal_FeldName[$k]=='KAPAZITAET'){if($s>'0') $s=(int)$s; $s.='&nbsp;'; $sFS.=' kalTbLsR';}
   if(($w=$kal_SpaltenStil[$k])) $sStil=' style="'.$sStil.$w.'"';
   $sZl.="\n".'  <div class="kalTbLst'.$sFS.'"'.$sStil.'><span class="kalTbLst">'.$aSpTitle[$k].'</span>'.$s.'</div>';
  }
  $X.="\n".' <div class="kalTbZl'.$nFarb.'">'.$sZl."\n".' </div>'; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
 }
 $X.="\n".' <div class="kalTbZl'.$nFarb.'">';
 $X.="\n".'  <div class="kalTbLst"><span class="kalTbLst">'.fKalTx(KAL_TxAlle.' '.KAL_TxLoeschen).'</span><input class="kalCheck" type="checkbox" name="kal_SelAll" value="1" onClick="fSelAll(this.checked)"><input type="image" class="kalIcon" name="kal_Lsch" src="'.KAL_Url.'grafik/iconLoeschen.gif" style="border:0" title="'.fKalTx(KAL_TxLoeschen).'" alt="'.fKalTx(KAL_TxLoeschen).'"><input type="hidden" name="kal_LschNun" value="'.$sLsch.'"></div>';
 $X.="\n".' </div>';
 $X.="\n".'</div>';
 $X.="\n".'</form>';
 if(KAL_NaviUnten) $X.="\n".$sNavig;
 return $X;
}

//Navigator zum Blaettern
function fKalNavigator($nStart,$nCount,$sQry,$sSes){
 $nPgs=ceil($nCount/KAL_ListenLaenge); $nPag=ceil($nStart/KAL_ListenLaenge);
 $nAnf=$nPag-4; if($nAnf<=0) $nAnf=1; $nEnd=$nAnf+9; if($nEnd>$nPgs){$nEnd=$nPgs; $nAnf=$nEnd-9; if($nAnf<=0) $nAnf=1;}
 $X ="\n".'<div class="kalNavL">';
 $X.="\n".'<div class="kalSZhl">'.fKalTx(KAL_TxSeite).' '.$nPag.'/'.$nPgs.'</div>';
 $X.="\n".'<div class="kalNavi"><ul class="kalNavi">';
 $X.='<li class="kalNavL"><a href="'.KAL_Self.(KAL_Query.$sSes.$sQry?'?':'').substr(KAL_Query.$sSes.$sQry,5).'" title="'.fKalTx(KAL_TxAnfang).'">|&lt;</a></li>';
 $sL='<li><a href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.'&amp;',5).'kal_Start=';
 for($i=$nAnf;$i<=$nEnd;$i++){
  $X.=$sL.(($i-1)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxSeite).$i.'">'.($i!=$nPag?$i:'<b>'.$i.'</b>').'</a></li>';
 }
 $X.='<li class="kalNavR"><a href="'.KAL_Self.'?'.substr(KAL_Query.$sSes.'&amp;',5).'kal_Start='.(max($nPgs-1,0)*KAL_ListenLaenge+1).$sQry.'" title="'.fKalTx(KAL_TxEnde).'">&gt;|</a></li>';
 $X.='</ul></div>';
 $X.="\n".'<div class="kalClear"></div>';
 $X.="\n".'</div>';
 return $X;
}

//Text mit BB-Code einkuerzen
function fKalKurzMemo($s,$nL=80){
 if(strlen($s)>$nL){
  $v='#'.substr($s,0,$nL);
  if($p=strrpos($v,'[')){ //BB-Code enthalten
   if(strrpos($v,']')<$p) $v=substr($v,0,$p); //angeschnittenen Codes streichen
   $p=0; $aTg=array();
   while($p=strpos($v,'[',++$p)){ //Codes erkennen
    if($q=strpos($v,']',++$p)){$t=substr($v,$p,$q-$p); $aTg[]=(($q=strpos($t,'='))?substr($t,0,$q):$t);}
   }
   $n=count($aTg)-1;
   for($i=$n;$i>=0;$i--){ //Codes durchsuchen
    $s=$aTg[$i];
    if(substr($s,0,1)!='/'){
     $bFnd=false;
     for($j=$i;$j<=$n;$j++) if($aTg[$j]=='/'.$s){$aTg[$j]='#'; $bFnd=true; break;}
     if(!$bFnd) $v.='[/'.$s.']'; //fehlenden Code anhaengen
  }}}
  return substr($v,1).'....';
 }else return $s;
}
?>