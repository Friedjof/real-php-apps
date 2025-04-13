<?php
function fKalSeite(){ //Listendruck
 global $kal_FeldName, $kal_FeldType, $kal_ListenFeld, $kal_SortierFeld, $kal_LinkFeld, $kal_SpaltenStil,
  $kal_Kategorien, $kal_Symbole, $kal_WochenTag,
  $aKalDaten, $aKalSpalten, $oKalDbO;

 //Sortierung
 $nIndex=(isset($_GET['kal_Index'])?(int)$_GET['kal_Index']:KAL_ListenIndex);

 // Meldung ausgeben
 $X="\n".str_replace('#M',KAL_Saetze,str_replace('#N',count($aKalDaten),KAL_Meldung));

 //eigene Layoutzeile prüfen
 if($bEigeneZeilen=KAL_EigeneDruckZeilen&&file_exists(KAL_Pfad.'kalDruckListenZeile.htm')){
  $sEigeneZeile=@implode('',@file(KAL_Pfad.'kalDruckListenZeile.htm')); $s=strtolower($sEigeneZeile);
  if(empty($sEigeneZeile)||strpos($s,'<body')>0||strpos($s,'<head')>0) $bEigeneZeilen=false;
 }

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#');
 if(($n=array_search('u',$kal_FeldType))&&$kal_ListenFeld[$n]>0){
  if(!KAL_SQL){ //Textdaten
   $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fKalDeCode($a[2]); $a[4]=fKalDeCode($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO=$oKalDbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;} $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $ksNutzerListFeld=(KAL_NListeAnders&&KAL_SessionOK?KAL_NNutzerListFeld:KAL_NutzerListFeld);
 }

 //eventuell Monate holen
 if(KAL_MonatLLang>0) $aMonate=explode(';',';'.(KAL_MonatLLang==2?KAL_TxLMonate:KAL_TxKMonate));
 if(KAL_MonatsTrenner>0){$sMonTrn=''; if(KAL_MonatsTrenner>1) $aMonTrn=explode(';',';'.KAL_TxLMonate);}
 $bMonTrn=false; $bWocTrn=false; $bTagTrn=false; $sJhrTrn=''; $sWocTrn=''; $sTagTrn='';

 //Daten ausgeben: $i-Index, $j-Spalte, $k-Feld
 $a=array(); $nSpalten=count($aKalSpalten); $nSpAuslass=0; $kal_FeldName[0]=KAL_TxNr; $bMitID=$kal_ListenFeld[0]>0; $sFS='';
 $X.="\n\n".'<div class="kalDrTab">';

 //Kopfzeile ausgeben
 if(!$bEigeneZeilen){ //Standardlayout
  $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?'0 kalTbZlDr':'Dr').'">';
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; $sFN=$kal_FeldName[$k]; $sFS=$kal_FeldType[$k];
   if($sFS=='d'||$sFS=='t'||$sFS=='m'||$sFS=='a'||$sFS=='k'||$sFS=='o') $sFS='L'; elseif($sFS=='w'||$sFS=='n'||$sFS=='1'||$sFS=='2'||$sFS=='3'||$sFS=='r') $sFS='R'; else $sFS='M';
   if($sFN=='KAPAZITAET'&&strlen(KAL_ZusageNameKapaz)>0) $sFN=KAL_ZusageNameKapaz; elseif($sFN=='ZUSAGE_BIS'&&strlen(KAL_ZusageNameFrist)>0) $sFN=KAL_ZusageNameFrist;
   if(($t!='e'||KAL_DruckLMailOffen)&&($t!='l'||!KAL_LinkSymbol))
    $X.="\n".'  <div class="kalTbDr kalTbSp'.$sFS.'">'.fKalTx($sFN).'</div>';
   else $nSpAuslass++;
  }
  $X.="\n".' </div>';
 }elseif(file_exists(KAL_Pfad.'kalDruckListenKopf.htm')){ //eigene Kopfzeile
  $r=@implode('',@file(KAL_Pfad.'kalDruckListenKopf.htm')); $s=strtolower($r); $p=0;
  while($p=strpos($r,'{',$p+1)) if($i=strpos($r,'}',$p+1)){
   $r=substr_replace($r,$t,$p,$i-$p+1);
  }
  if(!strpos($s,'<body')&&!strpos($s,'<head')) $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?'0 kalTbZlDr':'Dr').'">'."\n".$r."\n </div>";
 }

 //alle Datenzeilen ausgeben
 if($sVStil=KAL_ListeVertikal) $sVStil='vertical-align:'.$sVStil.';'; $nKatPos=array_search('k',$kal_FeldType); $nFarb=1; $sCSS='';
 foreach($aKalDaten as $a){
  $sZl=''; $sId=$a[0];
  if(KAL_DruckLFarbig){
   $sCSS=$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
   if($nKatPos>0) if(isset($a['Kat'])) if($j=$a['Kat']) $sCSS='Kat'.$j; //Kategorie aus Zusatzspalte
   if(KAL_Laufendes&&strpos(KAL_LaufendeId,';'.$sId.';')>0) $sCSS='LfdE'; //laufendes Ereignis
   elseif(KAL_Aktuelles&&strpos(KAL_AktuelleId,';'.$sId.';')>0) $sCSS='AktE'; //aktuelles Ereignis
  }
  if($bEigeneZeilen) $sZl=$sEigeneZeile; //eigenes Zeilenlayout
  for($j=($bMitID?0:1);$j<$nSpalten;$j++){ //alle Spalten
   $k=$aKalSpalten[$j]; $t=$kal_FeldType[$k]; /* $sStil=$sVStil; */ $sStil=''; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': case 'g': $s=fKalBB(fKalDt($s)); break; //Text/Gastkommentar
     case 'm': if(KAL_DruckLMemoLaenge==0) $s=fKalBB(fKalDt($s)); else $s=fKalBB(fKalDt(fKalKurzMemo($s,KAL_DruckLMemoLaenge))); break; //Memo
     case 'a': case 'k': case 'o': $s=fKalDt($s); break; //Aufzählung/Kategorie/Postleitzahl
     case 'd': case '@': $w=trim(substr($s,11)); //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(KAL_Jahrhundert?substr($s,0,4):substr($s,2,2));
      if($k==1){$sMon=$s2; $sJhr=substr($s,0,4);} if(KAL_MonatLLang>0&&$t=='d') $s2=fKalTx($aMonate[(int)$s2]);
      switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3;
      if($t=='d'){
       if(KAL_MonatLLang&&KAL_Datumsformat==1) $s=str_replace($s2.'.','&nbsp;'.$s2.'&nbsp;',$s);
       if(KAL_MitWochentag) if(KAL_MitWochentag<2) $s=fKalTx($kal_WochenTag[$w]).'&nbsp;'.$s; elseif(KAL_MitWochentag==2) $s.='&nbsp;'.fKalTx($kal_WochenTag[$w]); else $s=fKalTx($kal_WochenTag[$w]);
       if($k==1&&$nIndex==1){
        if(KAL_MonatsTrenner>0&&($sMon!=$sMonTrn||$sJhr!=$sJhrTrn)){$bMonTrn=true; $sMonTrn=$sMon; $sJhrTrn=$sJhr;}
        if(KAL_WochenTrenner>0){$sWoc=date('W',@mktime(8,0,0,$sMon,$s1,$sJhr)); if($sWoc!=$sWocTrn||$sJhr!=$sJhrTrn){$bWocTrn=true; $sWocTrn=$sWoc; $sJhrTrn=$sJhr;}}
        if(KAL_TagesTrenner>0&&$s!=$sTagTrn){$bTagTrn=true; $sTagTrn=$s;}
       }
      }elseif($kal_FeldName[$k]=='ZUSAGE_BIS') if($w) $s.='&nbsp;'.$w;
      break;
     case 'z': $sFS.=' kalTbSpM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!KAL_PreisLeer){
       $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); if(KAL_Waehrung) $s.='&nbsp;'.KAL_Waehrung; $sFS.=' kalTbSpR';
      }else $s='&nbsp;';
      break;
     case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fKalTx(KAL_TxJa); elseif($s=='N') $s=fKalTx(KAL_TxNein); $sFS.=' kalTbSpM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,''); else $s=str_replace('.',KAL_Dezimalzeichen,$s); $sFS.=' kalTbSpR';
      break;
     case 'i': $s=sprintf('%0'.KAL_NummerStellen.'d',$s); $sFS.=' kalTbSpM'; break; //Nummer
     case 'l': //Link
      $aL=explode('||',$s); $s=''; $sFS.=' kalTbSpM';
      foreach($aL as $w){
       $aI=explode('|',$w); $s.=fKalDt(isset($aI[1])?$aI[1]:$aI[0]).', ';
      }$s=substr($s,0,-2); break;
     case 'e': if(!KAL_SQL) $s=fKalDeCode($s); $sFS.=' kalTbSpM'; break; //eMail
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=KAL_TxAutorUnbekannt;
       for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
        if(!$s=$aNutzer[$n][$ksNutzerListFeld]) $s=KAL_TxAutorUnbekannt; $s=fKalDt($s);
        break;
      }}else $s=KAL_TxAutor0000;
      break;
     case 's': $w=$s; //Symbol
      $s='grafik/symbol'.$kal_Symbole[$s].'.'.KAL_SymbolTyp; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="Icon">'; $sFS.=' kalTbSpM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=KAL_Bilder.$sId.'-'.$s; $aI=@getimagesize(KAL_Pfad.$s);
      $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="Bild">'; $sFS.=' kalTbSpM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' kalTbSpM';
      $v='<img class="kalIcon" src="'.KAL_Url.'grafik/datei'.$v.'.gif" alt="Icon">';
      $s=$v;
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&KAL_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.KAL_ErsatzBildKlein; $aI=@getimagesize(KAL_Pfad.$s); $s='<img src="'.KAL_Url.$s.'" '.$aI[3].' style="border:0" alt="kein Bild">'; $sFS.=' kalTbSpM';
   }else $s='&nbsp;';
   if($kal_FeldName[$k]=='KAPAZITAET'){if($s>'0') $s=(int)$s; $s.='&nbsp;'; $sFS.=' kalTbSpR';}
   if(($w=$kal_SpaltenStil[$k])) $sStil=' style="'.$w.'"';
   if(!$bEigeneZeilen){
    if(($t!='e'||KAL_DruckLMailOffen)&&($t!='l'||!KAL_LinkSymbol)) $sZl.="\n".'  <div class="kalTbDr'.$sFS.'"'.$sStil.'>'.$s.'</div>';
   }else $sZl=str_replace('{'.$kal_FeldName[$k].'}',$s,$sZl); //eigenes Zeilenlayout
  }
  if($bMonTrn){$bMonTrn=false; $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?'T kalTbRow':'Dr').'"><div class="kalTbDr">'.(KAL_MonatsTrenner<2?'&nbsp;':fKalTx($aMonTrn[(int)$sMon])).(KAL_MonatsTrenner>2?'&nbsp;'.$sJhr:'').'</div></div>';}
  if($bWocTrn){$bWocTrn=false; $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?'T kalTbRow':'Dr').'"><div class="kalTbDr">'.(KAL_WochenTrenner<2?'&nbsp;':$sWocTrn.'.&nbsp;'.fKalTx(KAL_TxWoche)).(KAL_WochenTrenner>2?'&nbsp;'.$sJhr:'').'</div></div>';}
  if($bTagTrn){$bTagTrn=false; $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?'T kalTbRow':'Dr').'"><div class="kalTbDr">'.(KAL_TagesTrenner<2?'&nbsp;':fKalTx($sTagTrn)).'</div></div>';}
  if(!$bEigeneZeilen){
   $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?$sCSS.' kalTbZlDr':'Dr').'">'."\n".$sZl."\n".' </div>';
  }else{
   $sZl=str_replace('{Nummer}',($bMitID?sprintf('%0'.KAL_NummerStellen.'d',$sId):''),$sZl);
   $sZl=str_replace('{Info}','',str_replace('{Aendern}','',str_replace('{Kopieren}','',str_replace('{Erinnern}','',str_replace('{Nachricht}','',$sZl)))));
   for($j=count($kal_FeldName)-1;$j>=0;$j--) if(!(strpos($sZl,'{'.$kal_FeldName[$j].'}')===false)) $sZl=str_replace('{'.$kal_FeldName[$j].'}','&nbsp;',$sZl);
   $X.="\n".' <div class="kalTbZl'.(KAL_DruckLFarbig?$sCSS.' kalTbZlDr':'Dr').'">'."\n".$sZl."\n </div>";
  }
 }
 $X.="\n</div>";
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