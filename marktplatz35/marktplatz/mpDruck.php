<?php
function fMpSeite(){ //Listendruck
 if(MP_Segment>'') $sSegNo=sprintf('%02d',MP_Segment);
 else return '<p class="mpFehl">'.fMpTx(MP_TxKeinSegment).'</p>';

 global $DbO,$aMpDaten,$aMpSpalten,$aMpFN,$aMpFT,$aMpLF,$aMpNL,$aMpOF,$aMpLK,$aMpSS,$aMpAW,$aMpKW,$aMpSW;

 // Meldung ausgeben
 $X="\n".str_replace('#N',MP_Saetze,MP_Meldung);

 //eventuell Nutzerdaten holen
 $aNutzer=array(0=>'#');
 if(($n=array_search('u',$aMpFT))&&$aMpLF[$n]>0){
  if(!MP_SQL){ //Textdaten
   $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $n=count($aD);
   for($i=1;$i<$n;$i++){
    $a=explode(';',rtrim($aD[$i])); array_splice($a,1,1); $a[2]=fMpDeCode($a[2]); $a[4]=fMpDeCode($a[4]); $aNutzer[]=$a;
   }
  }elseif($DbO){ //SQL-Daten
   if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN)){
    while($a=$rR->fetch_row()){array_splice($a,1,1); $aNutzer[]=$a;}
    $rR->close();
  }}
  $nNutzerZahl=count($aNutzer); $mpNutzerListFeld=(MP_NListeAnders&&MP_SessionOK?MP_NNutzerListFeld:MP_NutzerListFeld);
 }

 //Daten ausgeben: $i-Index, $j-Spalte, $k-Feld
 $a=array(); $nSpalten=count($aMpSpalten); $nSpAuslass=0; $bMitID=$aMpLF[0]>0;
 $X.="\n\n".'<div class="mpDrTab">'; //Tabelle
 $sCss=(MP_DruckLFarbig?'mpTbZl2':'mpTbZlDr');
 $X.="\n".' <div class="'.$sCss.'">'; //Kopfzeile
 for($j=($bMitID?0:1);$j<$nSpalten;$j++){
  $t=$aMpFT[$aMpSpalten[$j]];
  if(($t!='e'||MP_DruckLMailOffen)&&($t!='l'||!MP_LinkSymbol))
   $X.="\n".'  <div class="mpDrKz">'.fMpTx($aMpFN[$aMpSpalten[$j]]).'</div>';
  else $nSpAuslass++;
 }
 $X.="\n".' </div>';

 //alle Datenzeilen ausgeben
 if(MP_BldTrennen){$sBldDir=$sSegNo.'/'; $sBldSeg='';}else{$sBldDir=''; $sBldSeg=$sSegNo;}
 $nKatPos=array_search('k',$aMpFT); $nFarb=1;
 foreach($aMpDaten as $a){
  $sZl=''; $sId=$a[0]; $sCss='mpTbZlDr';
  if(MP_DruckLFarbig){
   $sCss='mpTbZl'.$nFarb; if(--$nFarb<=0) $nFarb=2; //Farben alternieren
   if($nKatPos>0) if(isset($a[$nSpalten])) if($j=$a[$nSpalten]) $sCss.=' mpLstKat'.$j; //Kategorie aus Zusatzspalte
  }
  if($bMitID) $sZl="\n".'  <div class="mpTbDr mpTbSpM" style="'.$aMpSS[0].'">'.(MP_NummerMitSeg?$sSegNo.'/':'').sprintf('%0'.MP_NummerStellen.'d',$sId).'</div>';
  for($j=1;$j<$nSpalten;$j++){ //alle Spalten
   $k=(int)$aMpSpalten[$j]; $t=$aMpFT[$k]; $sStil=''; $sFS='';
   if($s=$a[$j]){
    switch($t){
     case 't': $s=fMpBB(fMpDt($s)); break; //Text
     case 'm': if(MP_DruckLMemoLaenge==0) $s=fMpBB(fMpDt($s)); else $s=fMpBB(fMpKurzMemo(fMpDt($s),MP_DruckLMemoLaenge)); break; //Memo
     case 'a': case 'k': case 'o': $s=fMpDt($s); break; //Aufzaehlung/Kategorie/Postleitzahl
     case 'd': case '@': //Datum
      $s1=substr($s,8,2); $s2=substr($s,5,2); $s3=(MP_Jahrhundert?substr($s,0,4):substr($s,2,2));
      switch(MP_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
       case 0: $v='-'; $s1=$s3; $s3=substr($s,8,2); break; case 1: $v='.'; break;
       case 2: $v='/'; $s1=$s2; $s2=substr($s,8,2); break; case 3: $v='/'; break; case 4: $v='-'; break;
      }
      $s=$s1.$v.$s2.$v.$s3; break;
     case 'z': $sFS.=' mpTbSpM'; break; //Uhrzeit
     case 'w': //Waehrung
      if($s>0||!MP_PreisLeer){
       $s=number_format((float)$s,MP_Dezimalstellen,MP_Dezimalzeichen,MP_Tausendzeichen);
       if(MP_Waehrung) $s.='&nbsp;'.MP_Waehrung; $sFS.=' mpTbSpR';
      }else $s='&nbsp;';
      break;
     case 'j': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
      if($s=='J'||$s=='Y') $s=fMpTx(MP_TxJa); elseif($s=='N') $s=fMpTx(MP_TxNein); $sFS.=' mpTbSpM';
      break;
     case 'n': case '1': case '2': case '3': case 'r': //Zahl
      if($t!='r') $s=number_format((float)$s,(int)$t,MP_Dezimalzeichen,''); else $s=str_replace('.',MP_Dezimalzeichen,$s); $sFS.=' mpTbSpR';
      break;
     case 'l': $aI=explode('|',$s); $s=fMpDt(isset($aI[1])?$aI[1]:$aI[0]); break; //Link
     case 'e': if(!MP_SQL) $s=fMpDeCode($s); break; //eMail
     case 'u': //Benutzer
      if($nId=(int)$s){
       $s=MP_TxAutorUnbekannt;
       for($n=1;$n<$nNutzerZahl;$n++) if($aNutzer[$n][0]==$nId){
        if(!$s=$aNutzer[$n][$mpNutzerListFeld]) $s=MP_TxAutorUnbekannt;
        break;
      }}else $s=MP_TxAutor0000;
      $s=fMpDt($s); break;
     case 's': $w=$s; //Symbol
      $p=array_search($s,$aMpSW); $s=''; if($p1=floor(($p-1)/26)) $s=chr(64+$p1); if(!$p=$p%26) $p=26; $s.=chr(64+$p);
      $s='grafik/symbol'.$s.'.'.MP_SymbolTyp; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,'');
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbSpM';
      break;
     case 'b': //Bild
      $s=substr($s,0,strpos($s,'|')); $s=MP_Bilder.$sBldDir.$sId.$sBldSeg.'-'.$s; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); //Bild
      $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="'.substr($s,strpos($s,'/')+1).'" title="'.substr($s,strpos($s,'/')+1).'" />'; $sFS.=' mpTbSpM';
      break;
     case 'f': //Datei
      $w=substr(strrchr($s,'.'),1); $v=ucfirst(strtolower(substr($w,0,3)));
      if($v!='Doc'&&$v!='Xls'&&$v!='Pdf'&&$v!='Zip'&&$v!='Htm'&&$v!='Jpg'&&$v!='Gif') $v='Dat'; $sFS.=' mpTbSpM';
      $v='<img class="mpIcon" src="'.MP_Url.'grafik/datei'.$v.'.gif" alt="" />';
      $s=$v;
      break;
     case 'x': break; //StreetMap
     case 'p': case 'c': $s=str_repeat('*',strlen($s)/2); break; //Passwort/Kontakt
    }
   }elseif($t=='b'&&MP_ErsatzBildKlein>''){ //keinBild
    $s='grafik/'.MP_ErsatzBildKlein; if(file_exists(MP_Pfad.$s)) $aI=getimagesize(MP_Pfad.$s); else $aI=array(0,0,0,''); $s='<img src="'.MP_Url.$s.'" '.(isset($aI[3])?$aI[3]:'').' border="0" alt="" />'; $sFS.=' mpTbSpM';
   }else $s='&nbsp;';
   if(($w=$aMpSS[$k])) $sStil=' style="'.str_replace('`,',';',$w).'"';
   if(($t!='e'||MP_DruckLMailOffen)&&($t!='l'||!MP_LinkSymbol)) $sZl.="\n".'  <div class="mpTbDr'.$sFS.'"'.$sStil.'>'.$s.'</div>';
  }
  $X.="\n".' <div class="'.$sCss.'">'.$sZl."\n".' </div>';
 }
 $X.="\n".'</div>'; //Tabelle
 if(MP_CanoLink&&(!MP_DruckPopup||MP_CanoPopup)){$sC=fMpHref('liste','1','','',MP_Segment,true); /* $p=strrpos($sC,'/'); if(!($p===false)) $sC=substr($sC,$p+1); */ define('MP_Canonical',str_replace('&amp;','&',$sC));}
 return $X;
}

//Text mit BB-Code einkuerzen
function fMpKurzMemo($s,$nL=80){
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