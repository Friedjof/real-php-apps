<?php
 global $nSegNo,$sSegNo,$sSegNam;
 include 'hilfsFunktionen.php';
 echo fSeitenKopf('Struktur und Eigenschaften des Marktsegmentes','','KSe');

 $sNeuNam=''; $nNeuPos=''; $sNeuBem=''; $sNeuBem=''; $sNeuTyp=''; $nFelder=0; include('feldtypenInc.php');
 $aStru=array(); $nListenIndex='1'; $nAdmListenIndex='1'; $sHide=''; $nLsch=0; $sZiel='';
 $aFN=array(); $aFT=array(); $aLF=array(); $aNL=array(); $aOF=array(); $aLK=array();
 $aSS=array(); $aDF=array(); $aND=array(); $aZS=array(); $aSF=array();
 $aEF=array(); $aNE=array(); $aEB=array(); $aPF=array(); $aTZ=array(); $aET=array(); $aAW=array();
 if(MP_Pfad>''){
  if(!MP_SQL){  //Text
   if(file_exists(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur)){
    $aStru=file(MP_Pfad.MP_Daten.$sSegNo.MP_Struktur); $sZiel='Txt';
   }else{$Meld='Bitte zuerst die Pfade im Setup einstellen!'; $aFN=array('',''); $aET=array('','');}
  }elseif($DbO){//SQL
   if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' WHERE nr="'.$nSegNo.'"')){
    $a=$rR->fetch_row(); $i=$rR->num_rows; $rR->close();
    if($i==1){$aStru=explode("\n",$a[1]); $sZiel='Sql';}
   }else $Meld=MP_TxSqlFrage;
  }else $Meld=MP_TxSqlVrbdg;
 }else{$Meld='Bitte zuerst die Pfade im Setup einstellen!'; $aFN[0]=MP_TxFld0Nam; $aFN[1]=MP_TxFld1Nam; $aET[1]='??';}
 if($sZiel){//Struktur ist geholt
  if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
   $Meld='Bearbeiten Sie die Inseratestruktur und Darstellungseigenschaften des Segments <i>'.$sSegNam.'</i>.'; $MTyp='Meld';
  }else{ //POST
   fMpEntpackeStruktur($aStru); $nFelder=count($aFN);

   $nListenIndex=(isset($_POST['IdxListenIndex'])?$_POST['IdxListenIndex']:'1');
   $nAdmListenIndex=(isset($_POST['AdmListenIndex'])?$_POST['AdmListenIndex']:'1');
   if(isset($_POST['mpLsch'])&&($nLsch=(int)$_POST['mpLsch'])){ //Feld löschen
    $sNam=$aFN[$nLsch];
    if(!isset($_POST['OkLsch'])||$nLsch!=$_POST['OkLsch']){ //Sicherheitsabfrage
     $Meld='Das Feld an Position-'.$nLsch.' (<i>'.$sNam.'</i>) wirklich löschen?';
     $sHide='<input type="hidden" name="OkLsch" value="'.$nLsch.'" />';
    }else{ //nun loeschen
     $sLTyp=$aFT[$nLsch];
     if($j=$aLF[$nLsch]) for($i=1;$i<$nFelder;$i++) if($aLF[$i]>$j) --$aLF[$i];
     if($j=$aNL[$nLsch]) for($i=1;$i<$nFelder;$i++) if($aNL[$i]>$j) --$aNL[$i];
     array_splice($aFN,$nLsch,1); array_splice($aFT,$nLsch,1);
     array_splice($aLF,$nLsch,1); array_splice($aNL,$nLsch,1);
     array_splice($aOF,$nLsch,1); array_splice($aLK,$nLsch,1); array_splice($aSS,$nLsch,1);
     array_splice($aDF,$nLsch,1); array_splice($aND,$nLsch,1); array_splice($aZS,$nLsch,1);
     array_splice($aSF,$nLsch,1);
     array_splice($aEF,$nLsch,1); array_splice($aNE,$nLsch,1); array_splice($aEB,$nLsch,1); array_splice($aPF,$nLsch,1); array_splice($aTZ,$nLsch,1);
     array_splice($aET,$nLsch,1); array_splice($aAW,$nLsch,1);
     if($sLTyp=='k') $aStru[17]='Kategorien  : '; elseif($sLTyp=='s') $aStru[18]='Symbole     : ';
     $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
     if($Meld=='OK'){//Strukturinfo ist geändert
      $MTyp='Erfo'; $Meld='Das Feld an Position-'.$nLsch.' (<i>'.$sNam.'</i>) wurde gelöscht.'; $aStru=$aTmp; $nFelder--;
      if($nSegNo>0){
       if(!MP_SQL){//Text
        $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
        $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
        for($i=1;$i<$nSaetze;$i++){ //vorhandene Inserate
         $a=explode(';',rtrim($aD[$i])); $s=$a[0]; $nId=max($nId,(int)$s); $s.=';'.$a[1]; array_splice($a,1,1);
         for($j=1;$j<=$nFelder;$j++) if($j!=$nLsch) $s.=';'.$a[$j]; $aD[$i]=$s.NL;
        }
        $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
        if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);}
        else $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht geändert werden!';
       }elseif($DbO){//SQL
        $sTab=str_replace('%',$sSegNo,MP_SqlTabI);
        if(!$DbO->query('ALTER TABLE '.$sTab.' DROP mp_'.$nLsch))
         $Meld.='</p><p class="admFehl">Das Feld konnte nicht aus der MySQL-Tabelle <i>'.$sTab.'</i> gelöscht werden!';
        for($i=$nLsch;$i<$nFelder;$i++) $DbO->query('ALTER TABLE '.$sTab.' CHANGE mp_'.($i+1).' mp_'.$i.' '.$aSql[$aFT[$i]]);
      }}
      $nLsch=0;
     }
    }//nun loeschen
   }elseif(isset($_POST['fNeu'])&&($sNeuNam=fFldNam($_POST['fNeu']))){ //neues Feld
    if(!$nNeuPos=min((int)$_POST['pNeu'],$nFelder)) $nNeuPos=$nFelder; $sNeuBem=fBemTxt($_POST['eNeu']); $bWrnTyp=false;
    if($sNeuTyp=$_POST['tNeu']){
     if(!in_array($sNeuNam,$aFN)){
      if($nNeuPos>1){
       if($sNeuTyp=='k'||$sNeuTyp=='s') if(in_array($sNeuTyp,$aFT)) $bWrnTyp=true;
       array_splice($aFN,$nNeuPos,0,$sNeuNam); array_splice($aFT,$nNeuPos,0,$sNeuTyp);
       array_splice($aLF,$nNeuPos,0,0); array_splice($aNL,$nNeuPos,0,0);
       array_splice($aOF,$nNeuPos,0,0); array_splice($aLK,$nNeuPos,0,0); array_splice($aSS,$nNeuPos,0,'');
       array_splice($aDF,$nNeuPos,0,($sNeuTyp!='p'&&$sNeuTyp!='c'?1:0));
       array_splice($aND,$nNeuPos,0,1);
       array_splice($aZS,$nNeuPos,0,'');
       array_splice($aSF,$nNeuPos,0,0);
       array_splice($aEF,$nNeuPos,0,1); array_splice($aNE,$nNeuPos,0,1); array_splice($aEB,$nNeuPos,0,0);
       array_splice($aPF,$nNeuPos,0,($sNeuTyp!='k'&&$sNeuTyp!='u'&&$sNeuTyp!='p'&&$sNeuTyp!='@'?0:1));
       array_splice($aTZ,$nNeuPos,0,0);
       array_splice($aET,$nNeuPos,0,$sNeuBem); array_splice($aAW,$nNeuPos,0,'');
       $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
       if($Meld=='OK'){//Strukturinfo ist geändert
        $MTyp='Erfo'; $Meld='Das neue Feld <i>'.$sNeuNam.'</i> wurde '.($nNeuPos==$nFelder?'an':'ein').'gefügt.'; $aStru=$aTmp;
        if($nSegNo>0){
         if(!MP_SQL){//Text
          $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
          $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
          for($i=1;$i<$nSaetze;$i++){ //vorhandene Inserate
           $s=rtrim($aD[$i]); $nId=max($nId,(int)substr($s,0,strpos($s,';')));
           if($nNeuPos<$nFelder){$q=0; for($j=0;$j<=$nNeuPos;$j++){$p=strpos($s,';',$q); $q=++$p;} $aD[$i]=substr_replace($s,';',$p,0).NL;}
           else $aD[$i]=$s.";\n";
          }
          $s='Nummer_'.$nId.';online'; for($i=1;$i<=$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
          if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);}
          else $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht geändert werden!';
         }elseif($DbO){//SQL
          $sTab=str_replace('%',$sSegNo,MP_SqlTabI);
          if($nNeuPos<$nFelder) for($i=$nFelder-1;$i>=$nNeuPos;$i--) $DbO->query('ALTER TABLE '.$sTab.' CHANGE mp_'.$i.' mp_'.($i+1).' '.$aSql[$aFT[$i+1]]);
          if(!$DbO->query('ALTER TABLE '.$sTab.' ADD mp_'.$nNeuPos.' '.$aSql[$sNeuTyp].' AFTER mp_'.($nNeuPos-1)))
           $Meld.='</p><p class="admFehl">Das Feld konnte nicht in die MySQL-Tabelle <i>'.$sTab.'</i> geschrieben werden!';
         }
         if($bWrnTyp) $Meld.='</p><p class="admFehl">Warnung: Es ist nicht sinnvoll 2 Felder vom Typ <i>'.$aTyp[$sNeuTyp].'</i> zu vereinbaren!';
        }
        $sNeuNam=''; $nNeuPos=''; $sNeuTyp=''; $sNeuBem=''; $nFelder=count($aFN);
      }}else $Meld='Die Feldposition zum neuen Feld <i>'.$sNeuNam.'</i> muss größer als 1 sein!';
     }else $Meld='Ein Feld mit dem neuen Namen <i>'.$sNeuNam.'</i> existiert bereits!';
    }else $Meld='Bitte einen Feldtyp zum neuen Feld <i>'.$sNeuNam.'</i> auswählen!';
   }elseif(isset($_POST['Form1'])){ //Änderung
    $bNeu=false; $bNam=false; $bTyp=false; $bErk=false; $sWrnTyp=''; $aEncr=array(); $aDecr=array(); $aAltTyp=$aFT;
    for($i=0;$i<$nFelder;$i++){
     $sNam=fFldNam($_POST['F'.$i]); $sTyp=$_POST['T'.$i]; $sErk=fBemTxt($_POST['E'.$i]); $sAltTyp=$aFT[$i];
     if($sNam!=$aFN[$i]&&$sNam>'') if(array_search($sNam,$aFN)===false){$aFN[$i]=$sNam; $bNam=true; $bNeu=true;}//Feldname
     else $Meld='Ein Feld mit dem Namen <i>'.$sNam.'</i> existiert bereits!';
     if($sTyp!=$sAltTyp&&$i>1){//Feldtyp
      if($sTyp=='k'||$sTyp=='s'){if(in_array($sTyp,$aFT)) $sWrnTyp=$sTyp;}//mehrere Kategorien/Symbole
      elseif(($sTyp=='e'&&$sAltTyp!='c'||$sTyp=='c'&&$sAltTyp!='e')&&$sZiel=='Txt') $aEncr[]=$i;//verschlüsseln
      elseif(($sAltTyp=='e'&&$sTyp!='c'||$sAltTyp=='c'&&$sTyp!='e')&&$sZiel=='Txt') $aDecr[]=$i;//entschlüsseln
      elseif($sAltTyp=='a') $aAW[$i]=''; elseif($sAltTyp=='k') $aStru[17]='Kategorien  : '; elseif($sAltTyp=='s') $aStru[18]='Symbole     : ';
      $aFT[$i]=$sTyp; $bTyp=true; $bNeu=true;
     }
     if($i==1){$sErk=max(1,(int)$sErk); if(($sErk*86400+time())>2147483647) $sErk=floor((2147483647-time())/86400);}
     if($sErk!=$aET[$i]){$aET[$i]=$sErk; $bErk=true; $bNeu=true;}//Bemerkungen
    }
    if($bNeu){//Name oder Typ geändert
     $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
     if($Meld=='OK'){//Strukturinfo ist geändert
      $MTyp='Erfo'; $Meld='Die Änderungen an der Inseratestruktur wurden gespeichert!'; $aStru=$aTmp; $bNeu=false; $bFhl=false;
      if($sWrnTyp) $Meld.='</p><p class="admFehl">Warnung: Es ist nicht sinnvoll 2 Felder vom Typ <i>'.$aTyp[$sWrnTyp].'</i> zu vereinbaren!';
      if($nSegNo>0){
       if(!MP_SQL){//Text
        if($bNam){ //Datenkopfzeile ändern
         $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
         $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
         for($i=1;$i<$nSaetze;$i++){$s=$aD[$i]; $nId=max($nId,(int)substr($s,0,strpos($s,';')));}
         $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
         if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
         else $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht geändert werden!';
        }
        if($bTyp&&(count($aEncr)>0||count($aDecr)>0)){//e-Mail-Felder codieren
         $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
         $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
         for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i])); reset($aEncr); reset($aDecr);
          foreach($aEncr as $n) $a[$n+1]=fMpEnCode($a[$n+1]); foreach($aDecr as $n) $a[$n+1]=fMpDeCode($a[$n+1]);
          $aD[$i]=implode(';',$a).NL;
         }
         $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
         if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);}
         else{$bErk=false; $Meld.='</p><p class="admFehl">In die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht geschrieben werden!';}
        }
       }elseif($DbO){//SQL
        if($bTyp) for($i=3;$i<$nFelder;$i++) if($aAltTyp[$i]!=$aFT[$i]){//Typ ändern
         if(!$DbO->query('ALTER TABLE '.str_replace('%',$sSegNo,MP_SqlTabI).' CHANGE mp_'.$i.' mp_'.$i.' '.$aSql[$aFT[$i]])) $bFhl=true;
        }
        if($bFhl) $Meld.='</p><p class="admFehl">Das Feld in der MySQL-Tabelle <i>'.str_replace('%',$sSegNo,MP_SqlTabI).'</i> konnte nicht geändert werden!';
    }}}}
    if(!$bNeu){//Reihenfolge
     $aNeu=array(); $aPos=array(0,1); for($i=2;$i<$nFelder;$i++) $aPos[$i]=$i;
     for($i=2;$i<$nFelder;$i++){ //neue Nummern testen
      $p=(int)$_POST['P'.$i]; if($p!=$i) if($p>1){$aPos[$i]=$p+($p>$i?0.1:-0.1); $bNeu=true;}
     }
     if($bNeu){ //umnumerieren
      asort($aPos); reset($aPos); foreach($aPos as $k=>$xx) $aNeu[]=$k; //korrigieren
      $tFN=$aFN; $tFT=$aFT; $tLF=$aLF; $tNL=$aNL; $tOF=$aOF; $tLK=$aLK; $tSS=$aSS; $tDF=$aDF; $tND=$aND; $tZS=$aZS; $tSF=$aSF; $tEF=$aEF; $tNE=$aNE; $tEB=$aEB; $tPF=$aPF; $tTZ=$aTZ; $tET=$aET; $tAW=$aAW;
      for($i=2;$i<$nFelder;$i++){
       $j=$aNeu[$i];
       $aFN[$i]=$tFN[$j]; $aFT[$i]=$tFT[$j]; $aLF[$i]=$tLF[$j]; $aNL[$i]=$tNL[$j];
       $aOF[$i]=$tOF[$j]; $aLK[$i]=$tLK[$j]; $aSS[$i]=$tSS[$j]; $aDF[$i]=$tDF[$j]; $aND[$i]=$tND[$j]; $aZS[$i]=$tZS[$j];
       $aSF[$i]=$tSF[$j]; $aEF[$i]=$tEF[$j]; $aNE[$i]=$tNE[$j]; $aEB[$i]=$tEB[$j]; $aPF[$i]=$tPF[$j]; $aTZ[$i]=$tTZ[$j]; $aET[$i]=$tET[$j]; $aAW[$i]=$tAW[$j];
      }
      $aTmp=fMpPackeStruktur($aStru); $Mld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
      if($Mld=='OK'){//Strukturinfo ist geändert
       $MTyp='Erfo'; if(!$Meld) $Meld='Die geänderte Feldreihenfolge wurde gespeichert.'; $aStru=$aTmp;
       if($nSegNo>0){
        if(!MP_SQL){//Text
         $aD=file(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate); $nSaetze=count($aD);
         $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
         for($i=1;$i<$nSaetze;$i++){//vorhandene Inserate
          $a=explode(';',rtrim($aD[$i])); $nId=max($nId,(int)$a[0]);
          $s=$a[0].';'.$a[1].';'.$a[2]; array_splice($a,1,1);
          for($j=2;$j<$nFelder;$j++) $s.=';'.$a[$aNeu[$j]]; $aD[$i]=$s.NL;
         }
         $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$aFN[$i]; $aD[0]=$s.NL;
         if($f=fopen(MP_Pfad.MP_Daten.$sSegNo.MP_Inserate,'w')){fwrite($f,rtrim(implode('',$aD)).NL); fclose($f);}
         else $Meld.='</p><p class="admFehl">Die Datei <i>'.MP_Daten.$sSegNo.MP_Inserate.'</i> konnte nicht geändert werden!';
        }elseif($DbO){//SQL
         $sTab=str_replace('%',$sSegNo,MP_SqlTabI);
         $DbO->query('DROP TABLE IF EXISTS mp_tmp_tab'); $s=''; for($i=2;$i<$nFelder;$i++) $s.=',mp_'.$aNeu[$i];
         if($DbO->query('CREATE TABLE mp_tmp_tab (PRIMARY KEY (nr), KEY mp_1 (mp_1)) SELECT nr,online,mp_1'.$s.' FROM '.$sTab)){
          $DbO->query('ALTER  TABLE mp_tmp_tab CHANGE nr nr int(11) AUTO_INCREMENT, COMMENT="Markplatz-Inserate-'.$sSegNo.'"');
          for($i=2;$i<$nFelder;$i++) $DbO->query('ALTER TABLE mp_tmp_tab CHANGE mp_'.$aNeu[$i].' f'.$i.' '.$aSql[$aFT[$i]]);
          for($i=2;$i<$nFelder;$i++) $DbO->query('ALTER TABLE mp_tmp_tab CHANGE f'.$i.' mp_'.$i.' '.$aSql[$aFT[$i]]);
          $DbO->query('DROP TABLE IF EXISTS '.$sTab); $DbO->query('ALTER TABLE mp_tmp_tab RENAME '.$sTab); $DbO->query('OPTIMIZE TABLE '.$sTab);
         }else $Meld.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$sTab.'</i> konnte nicht umgespeichert werden!';
      }}}else $Meld=$Mld;
   }}}elseif(isset($_POST['Form2'])){ //Formular 2
    $tLF=array(); $tNL=array(); $tSS=array(); $tOF=array(); $tLK=array(); $tSF=array();
    $tDF=array(); $tND=array(); $tZS=array(); $tEF=array(); $tNE=array(); $tEB=array(); $tPF=array(); $tTZ=array();
    for($i=0;$i<$nFelder;$i++){$tOF[$i]=0; $tLK[$i]=0; $tSF[$i]=0; $tDF[$i]=0; $tND[$i]=0; $tZS[$i]=0; $tEF[$i]=0; $tNE[$i]=0; $tEB[$i]=0; $tPF[$i]=0; $tTZ[$i]=0;}
    reset($_POST); $bNeu=false;
    foreach($_POST as $k=>$v){
     $sK=substr($k,0,1); $nNr=(int)substr($k,1);
     if($sK=='L') $tLF[$nNr]=$v; elseif($sK=='N') $tNL[$nNr]=$v;
     elseif($sK=='Y') $tSS[$nNr]=str_replace(';','`,',$v);
     elseif($sK=='O') $tOF[$nNr]=$v; elseif($sK=='K') $tLK[$nNr]=$v; elseif($sK=='S') $tSF[$nNr]=$v;
     elseif($sK=='D') $tDF[$nNr]=$v; elseif($sK=='M') $tND[$nNr]=$v;
     elseif($sK=='Z') $tZS[$nNr]=str_replace(';','`,',$v);
     elseif($sK=='E') $tEF[$nNr]=$v; elseif($sK=='U') $tNE[$nNr]=$v; elseif($sK=='P') $tPF[$nNr]=$v; elseif($sK=='Q') $tTZ[$nNr]=$v;
     elseif($sK=='B') $tEB[$nNr]=(int)$v;
    }
    asort($tLF); reset($tLF); $j=0; foreach($tLF as $k=>$v) if($v>0) if($k>0) $tLF[$k]=++$j; ksort($tLF);
    asort($tNL); reset($tNL); $j=0; foreach($tNL as $k=>$v) if($v>0) if($k>0) $tNL[$k]=++$j; ksort($tNL);
    if($aLF!=$tLF){$aLF=$tLF; $bNeu=true;} if($aNL!=$tNL){$aNL=$tNL; $bNeu=true;}
    if($aSS!=$tSS){$aSS=$tSS; $bNeu=true;} if($aOF!=$tOF){$aOF=$tOF; $bNeu=true;}
    if($aLK!=$tLK){$aLK=$tLK; $bNeu=true;} if($aSF!=$tSF){$aSF=$tSF; $bNeu=true;}
    if($aDF!=$tDF){$aDF=$tDF; $bNeu=true;} if($aND!=$tND){$aND=$tND; $bNeu=true;}
    if($aZS!=$tZS){$aZS=$tZS; $bNeu=true;} if($aEF!=$tEF){$aEF=$tEF; $bNeu=true;}
    if($aNE!=$tNE){$aNE=$tNE; $bNeu=true;} if($aEB!=$tEB){$aEB=$tEB; $bNeu=true;}
    if($aPF!=$tPF){$aPF=$tPF; $bNeu=true;} if($aTZ!=$tTZ){$aTZ=$tTZ; $bNeu=true;}
    if(($nListenIndex.';'.$nAdmListenIndex)!=substr($aStru[1],14,strlen($nListenIndex.';'.$nAdmListenIndex))) $bNeu=true;;
    if($bNeu){
     $aTmp=fMpPackeStruktur($aStru); $Meld=fMpSpeichereStruktur($sSegNo,$aTmp,$DbO);
     if($Meld=='OK'){$MTyp='Erfo'; $Meld='Die neuen Feldeigenschaften wurden gespeichert.'; $aStru=$aTmp;}
   }}

   if(!$Meld){$Meld='Die Inseratestruktureigenschaften bleiben unverändert.'; $MTyp='Meld';}
  }//POST
  fMpEntpackeStruktur($aStru); $nFelder=count($aFN); //Struktur interpretieren
 }//sZiel
 echo '<p class="adm'.$MTyp.'">'.$Meld.'</p>'.NL;
?>

<form action="konfEigenschaft.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<input type="hidden" name="Form1" value="x" />
<input type="hidden" name="IdxListenIndex" value="<?php echo $nListenIndex;?>" />
<input type="hidden" name="AdmListenIndex" value="<?php echo $nAdmListenIndex;?>" />
<?php echo $sHide;?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td align="center" width="5%"><b>Position</b></td>
 <td><b>Feldname</b></td>
 <td><b>Erklärung</b> <span class="admMini">(nur bei Bedarf, wird im Eingabeformular angezeigt)</span></td>
 <td width="8%"><b>Feldtyp</b></td>
 <td align="center" width="1%"><img src="iconLoeschen.gif" width="12" height="13" border="0" alt="löschen" title="löschen"></td>
</tr>
<tr class="admTabl">
 <td align="center"><div style="width:22px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">0</div></td>
 <td><input type="text" name="F0" value="<?php echo $aFN[0];?>" size="20" style="width:125px;" /></td>
 <td><div style="width:100%;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">------ &nbsp; <span class="admMini">Bedeutung: automatische Datensatznummer</span><input type="hidden" name="E0" value="" /></div></td>
 <td style="white-space:nowrap;"><select name="T0" size="1" style="width:130px;"><option value="i">Zählnummer</option></select>&nbsp;<a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td>&nbsp;</td>
</tr>
<tr class="admTabl">
 <td align="center"><div style="width:22px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">1</div></td>
 <td><input type="text" name="F1" value="<?php echo $aFN[1];?>" size="20" style="width:125px;" /></td>
 <td><input type="text" name="E1" value="<?php echo $aET[1];?>" size="4" style="width:40px;" /> <span class="admMini">Bedeutung: Anzeigedauer für Inserate in Tagen</span>&nbsp;<a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td><select name="T1" size="1" style="width:130px;"><option value="d">Ablaufdatum</option></select></td>
 <td>&nbsp;</td>
</tr>
<?php
 $sFOpt='<option value="0">---</option><option value="1">1</option>';
 $sOpt='<option value="d">Datum</option><option value="z">Uhrzeit</option><option value="@">Eintragszeit</option><option value="t">Text</option><option value="m">Memo</option><option value="a">Auswahl</option><option value="k">Kategorie</option><option value="s">Symbol</option><option value="j">Ja/Nein</option><option value="w">Währung</option><option value="n">Ganzzahl</option><option value="1">Zahl.1</option><option value="2">Zahl.2</option><option value="3">Zahl.3</option><option value="r">Zahl</option><option value="o">Postleitzahl</option><option value="b">Bild</option><option value="x">Straßenkarte</option><option value="f">Datei</option><option value="l">Link</option><option value="e">E-Mail</option><option value="c">Kontakt</option><option value="u">Benutzer</option><option value="p">Passwort</option><option value="v">Inserat verstecken</option>';
 for($i=2;$i<$nFelder;$i++){
  $sFOpt.='<option value="'.$i.'">'.$i.'</option>';
  $t=$aFT[$i];
?>
<tr class="admTabl">
 <td align="center"><input type="text" name="P<?php echo $i;?>" value="<?php echo $i;?>" size="2" style="width:22px;" /></td>
 <td><input type="text" name="F<?php echo $i;?>" value="<?php echo $aFN[$i];?>" size="20" style="width:125px;" /></td>
 <td><input type="text" name="E<?php echo $i;?>" value="<?php echo str_replace('`,',';',$aET[$i])?>" size="20" style="width:99%" /></td>
 <td style="white-space:nowrap;"><select name="T<?php echo $i;?>" size="1" style="width:130px;"><?php echo substr_replace($sOpt,' selected="selected"',strpos($sOpt,'"'.$t.'"')+3,0);?></select><?php if($t=='a'||$t=='k'||$t=='s'||$t=='t'||$t=='m'||$t=='o') echo ' <a href="konfVorgaben.php?'.($nSegNo>0?'seg='.$nSegNo.'&':'').'fld='.$i.'"><img src="iconAendern.gif" width="12" height="13" border="0" title="Vorgabewerte bearbeiten"></a>'; elseif($t=='x') echo ' <a href="konfStreetMap.php'.($nSegNo>0?'?seg='.$nSegNo:'').'"><img src="iconAendern.gif" width="12" height="13" border="0" title="Straßenkarten-Einstellungen bearbeiten"></a>';?></td>
 <td><input class="admRadio" type="radio" name="mpLsch" value="<?php echo $i?>"<?php if($i==$nLsch) echo ' checked="checked"'?> /></td>
</tr>
<?php }?>
<tr class="admTabl">
 <td align="center"><input type="text" name="pNeu" value="<?php echo $nNeuPos;?>" size="2" style="width:22px;" /></td>
 <td><input type="text" name="fNeu" size="20" value="<?php echo $sNeuNam;?>" style="width:125px;" /></td>
 <td><input type="text" name="eNeu" size="20" value="<?php echo str_replace('`,',';',$sNeuBem);?>" style="width:99%" /></td>
 <td><select name="tNeu" size="1" style="width:130px;"><option value=""></option><?php echo str_replace('"'.$sNeuTyp.'"','"'.$sNeuTyp.'" selected="selected"',$sOpt);?></select></td>
 <td width="16" align="center"><input type="image" src="iconLoeschen.gif" width="12" height="13" border="0" alt="löschen" title="ausgewähltes Feld löschen" /></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<div class="admBox"><p><span class="admFehl"><u>Wichtig</u>:</span> Diese Seite enthält im unteren Teil unterhalb dieser Hinweise noch zwei weitere Formulare!! </p>
<p><u>Hinweise</u>: Die Struktur der Inserate ist frei definierbar.
 Lediglich das Feld vom Typ <i>Zählnummer</i> und ein Feld vom Typ <i>Ablaufdatum</i>
 müssen am Anfang der Inseratestruktur vorhanden sein.
 Alle weiteren Felder können weggelassen, umbenannt, umsortiert oder ergänzt werden.</p>
<p>Pro Schalterklick wird immer nur <i>eine</i> Aktion ausgeführt.
 Sie können also entweder ein Feld löschen, ein neues Feld ergänzen/einfügen oder ein vorhandenes Feld abändern.
 Das <i>gleichzeitige</i> Ändern eines bestehenden Feldes <i>und</i> Ergänzen eines neuen Feldes beispielsweise ist nicht möglich.
 Solche unterschiedlichen Aktionen müssen schrittweise nacheinander ausgeführt werden.
 Löschen hat Vorrang vor Ergänzen und dieses vor Ändern.</p>
<p>Zum nachträglichen Ändern der Feldreihenfolge tragen Sie bei dem betreffenden Feld lediglich dessen künftige Wunschposition ein
 und belassen alle anderen Positionsangaben auf den bisherigen Werten. Alle anderen Feldpositionen werden automatisch angepasst.</p>
<p><u>Empfehlung</u>: Solange Sie noch Ihre optimale Inseratestruktureigenschaften suchen und an ihr &quot;herumbasteln&quot;
 sollten Sie die Datenbasis noch nicht auf MySQL-Datenbank umgestellt haben sondern solche Umbauten unter der Text-Datenbasis vornehmen.</p>
</div>

<div class="admBox"><p>Die nachfolgenden zwei Formulare regeln die Darstellung der definierten Inseratefelder
des Marktsegmentes <i><?php echo $sSegNam;?></i> in den einzelnen Seiten des Marktplatz-Scripts.
Nachdem Sie Änderungen an der obenstehenden Inseratestruktur vorgenommen haben
sollten Sie die Darstellungseigenschaften der Felder auf Sinnhaftigkeit überprüfen, da dieses nicht vollautomatisch erfolgt.</p>
</div>

<form style="margin-top:16px;" action="konfEigenschaft.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<input type="hidden" name="Form2" value="x" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%" valign="bottom"><b>Inseratefeld</b>&nbsp;</td>
 <td width="10%"><b>Listenspalte für Gäste/Benutzer</b></td>
 <td><b>optionale<br />CSS-Styles</b> <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td width="9%" align="center"><b>Sortier-<br>feld</b></td>
 <td width="9%" align="center"><b>Link-<br>feld</b></td>
 <td width="9%" align="center"><b>Such-<br>feld</b></td>
</tr>
<?php
$sSortIndex=''; $sAdmSortIndex='';
for($i=0;$i<$nFelder;$i++){
 $sSortIndex.='<option value="'.$i.(($i!=$nListenIndex||$i==1)?'':'" selected="selected').'">'.sprintf('%02d',$i).') '.$aFN[$i].'</option>';
 $sAdmSortIndex.='<option value="'.$i.(($i!=$nAdmListenIndex||$i==1)?'':'" selected="selected').'">'.sprintf('%02d',$i).') '.$aFN[$i].'</option>';
 echo '<tr class="admTabl">'.NL; $t=$aFT[$i];
 echo ' <td>'.sprintf('%02d',$i).')&nbsp;'.$aFN[$i].'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div></td>'.NL;
 if($i>0) //FeldPosition
 echo ' <td align="center"><select name="L'.$i.'" size="1" style="width:42px;">'.str_replace('"'.$aLF[$i].'"','"'.$aLF[$i].'" selected="selected"',$sFOpt).'</select> / <select name="N'.$i.'" size="1" style="width:42px;">'.str_replace('"'.$aNL[$i].'"','"'.$aNL[$i].'" selected="selected"',$sFOpt).'</select></td>'.NL;
 else
 echo ' <td align="center"><select name="L0" size="1" style="width:42px;"><option value="0">---</option><option value="1"'.($aLF[0]?' selected="selected"':'').'>0</option></select> / <select name="N0" size="1" style="width:42px;"><option value="0">---</option><option value="1"'.($aNL[0]?' selected="selected"':'').'>0</option></select></td>'.NL;
 echo ' <td><input type="text" name="Y'.$i.'" style="width:99%" value="'.str_replace('`,',';',$aSS[$i]).'" /></td>'.NL; //Style
 if($t!='b'&&$t!='z'&&$t!='f'&&$t!='x'&&$t!='e'&&$t!='c'&&$t!='p')//Sortierfeld
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="O'.$i.'"'.($aOF[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 else echo ' <td align="center">--<input type="hidden" name="O'.$i.'" value="0" /></td>'.NL;
 if($t!='l'&&$t!='f'&&$t!='x'&&$t!='e'&&$t!='c'&&$t!='p')//Linkfeld
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="K'.$i.'"'.($aLK[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 else echo ' <td align="center">--<input type="hidden" name="K'.$i.'" value="0" /></td>'.NL;
 if($t!='b'&&$t!='f'&&$t!='x'&&$t!='e'&&$t!='c'&&$t!='p') //Suchfeld
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="S'.$i.'"'.($aSF[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 else echo ' <td align="center">--<input type="hidden" name="S'.$i.'" value="0" /></td>'.NL;
 echo '</tr>'.NL;
}
?>
<tr class="admTabl"><td colspan="6">&nbsp;</td></tr>
<tr class="admTabl">
 <td>Standard-<br>Sortierung</td>
 <td width="10%"><select name="IdxListenIndex" style="width:100px;"><option value="1">Standard</option><?php echo $sSortIndex;?></select></td>
 <td class="admMini" colspan="4">gilt für Inserateliste und Inseratedetails, solange der Besucher keine andere Sortierung wählt</td>
</tr>
<tr class="admTabl">
 <td>Listen-<br>Sortierung</td>
 <td width="10%"><select name="AdmListenIndex" style="width:100px;"><option value="1">Standard</option><?php echo $sAdmSortIndex;?></select></td>
 <td class="admMini" colspan="4">gilt für Inserateliste und Inseratedetails im Admin-Bereich</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>

<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td width="10%" valign="bottom"><b>Inseratefeld</b>&nbsp;</td>
 <td width="10%"><b>Detailzeile für<br />Gäste/Benutzer</b></td>
 <td><b>optionale<br />CSS-Styles</b> <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td width="8%" align="center" colspan="3"><b>Eingabefeld für<br />Gäste/Benutzer</b></td>
 <td width="6%" align="center"><b>Pflicht-<br>feld</b></td>
</tr>
<?php
for($i=0;$i<$nFelder;$i++){
 $t=$aFT[$i]; $sL='';
 switch($t){
   case 't': $sL='255'; break;
   case 'm': $sL='64000'; break;
   case 'n': $sL='9'; break;
   case '1': $sL='10'; break;
   case '2': $sL='11'; break;
   case '3': $sL='12'; break;
   case 'r': $sL='12'; break;
   case 'o': $sL='9'; break;
   case 'l': $sL='255'; break;
   case 'e': case 'c': $sL='127'; break;
   case 'p': $sL='20'; break;
 }
 if($sL) $sL='<input style="width:2.6em;" type="text" name="B'.$i.'" value="'.($aEB[$i]>0?$aEB[$i]:'').'" title="gewünschte Längenbegrenzung eintragen'."\n".'oder leer lassen für max. '.$sL.' Zeichen" />';
 else $sL='<input type="hidden" name="B'.$i.'" value="0" />';
 echo '<tr class="admTabl">'.NL;
 echo ' <td>'.sprintf('%02d',$i).')&nbsp;'.$aFN[$i].'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div></td>'.NL;
 if($t!='c'&&$t!='p') //Detailfeld
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="D'.$i.'"'.($aDF[$i]?' checked="checked"':'').' value="1" /> / <input type="checkbox" class="admCheck" name="M'.$i.'"'.($aND[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 else
 echo ' <td align="center">-- &nbsp;/&nbsp; --<input type="hidden" name="D'.$i.'" value="0" /><input type="hidden" name="M'.$i.'" value="0" /></td>';
 echo ' <td><input type="text" name="Z'.$i.'" style="width:99%" value="'.str_replace('`,',';',$aZS[$i]).'" /></td>'.NL; //Style
 if($i>0){ //Eingabefeld
 echo ' <td align="center" width="1%">'.$sL.'</td>'.NL;
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="E'.$i.'"'.($aEF[$i]?' checked="checked"':'').' value="1" /> / <input type="checkbox" class="admCheck" name="U'.$i.'"'.($aNE[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 echo ' <td align="center" width="1%"><input type="checkbox" class="admCheck" name="Q'.$i.'"'.($aTZ[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 }else{
 echo ' <td align="center" width="1%">Länge<br />max.</td>'.NL;
 echo ' <td align="center">-- &nbsp;/&nbsp; --</td><input type="hidden" name="E'.$i.'" value="1" /><input type="hidden" name="U'.$i.'" value="1" />'.NL;
 echo ' <td align="right" width="1%"><b>T</b>*</td>'.NL;
 }
 if($i>0&&$t!='k'&&$t!='@'&&$t!='p'&&$t!='u') //Pflichtfeld
 echo ' <td align="center"><input type="checkbox" class="admCheck" name="P'.$i.'"'.($aPF[$i]?' checked="checked"':'').' value="1" /></td>'.NL;
 else
 echo ' <td align="center">(<input type="checkbox" class="admCheck" name="p'.$i.'"'.($aPF[$i]?' checked="checked"':'').' value="" /><input type="hidden" name="P'.$i.'" value="1" />)</td>'.NL;
 echo '</tr>'.NL;
}
?>
</table>
<p class="admMini" style="text-align:right">T*: Trennzeile aus rein optischen Gründen unter dem Eingabefeld</p>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<?php
echo fSeitenFuss();

function fFldNam($sIn){return str_replace(';',',',str_replace('"',"'",stripslashes(trim($sIn))));}
function fBemTxt($sIn){return str_replace(';','`,',str_replace('"',"'",stripslashes(trim($sIn))));}

function fMpEntpackeStruktur($aStru){//Struktur interpretieren
 global $aFN,$aFT,$aLF,$aNL,$aOF,$aLK,$aSS,$aDF,$aND,$aZS,$aSF,$aEF,$aNE,$aEB,$aPF,$aTZ,$aET,$aAW,$nListenIndex,$nAdmListenIndex;
 $aFN=explode(';',rtrim($aStru[0])); $aFN[0]=substr($aFN[0],14); if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 $aFT=explode(';',rtrim($aStru[1])); $nListenIndex=substr($aFT[0],14); $nAdmListenIndex=$aFT[1]; $aFT[0]='i'; $aFT[1]='d';
 $aLF=explode(';',rtrim($aStru[2])); $aLF[0]=substr($aLF[0],14,1);
 $aNL=explode(';',rtrim($aStru[3])); $aNL[0]=substr($aNL[0],14,1);
 $aOF=explode(';',rtrim($aStru[4])); $aOF[0]=substr($aOF[0],14,1);
 $aLK=explode(';',rtrim($aStru[5])); $aLK[0]=substr($aLK[0],14,1);
 $aSS=explode(';',rtrim($aStru[6])); $aSS[0]=substr($aSS[0],14);
 $aDF=explode(';',rtrim($aStru[7])); $aDF[0]=substr($aDF[0],14,1);
 $aND=explode(';',rtrim($aStru[8])); $aND[0]=substr($aND[0],14,1);
 $aZS=explode(';',rtrim($aStru[9])); $aZS[0]=substr($aZS[0],14);
 $aSF=explode(';',rtrim($aStru[10]));$aSF[0]=substr($aSF[0],14,1);
 $aEB=explode(';',rtrim($aStru[19])); $aEB[0]='0'; // Feldlaenge
 $aNE=explode(';',rtrim($aStru[12])); $aNE[0]='1';
 $aEF=explode(';',rtrim($aStru[11])); $aEF[0]='1';
 $aPF=explode(';',rtrim($aStru[13])); $aPF[0]='1'; //$aPF[1]='1';
 $aTZ=explode(';',rtrim($aStru[14])); $aTZ[0]='0';
 $aET=explode(';',rtrim($aStru[15])); $aET[0]='';  //$aET[1]='';
 $aAW=explode(';',str_replace('/n/','\n ',rtrim($aStru[16]))); $aAW[0]=''; $aAW[1]='';
 return true;
}
function fMpPackeStruktur($aStru){
 global $aFN,$aFT,$aLF,$aNL,$aOF,$aLK,$aSS,$aDF,$aND,$aZS,$aSF,$aEF,$aNE,$aEB,$aPF,$aTZ,$aET,$aAW,$nListenIndex,$nAdmListenIndex;
 $a=array();
 if(empty($aFN[0])) $aFN[0]=MP_TxFld0Nam; if(empty($aFN[1])) $aFN[1]=MP_TxFld1Nam;
 if(substr($aFN[0],0,14)!='FeldName    : ') $aFN[0]='FeldName    : '.$aFN[0]; $a[0]=implode(';',$aFN);
 $aFT[0]='FeldTyp     : '.$nListenIndex;    $aFT[1]=$nAdmListenIndex; $a[1]=implode(';',$aFT);
 if(substr($aFN[0],0,14)!='ListenFeld  : ') $aLF[0]='ListenFeld  : '.$aLF[0]; $a[2]=implode(';',$aLF);
 if(substr($aFN[0],0,14)!='NutzerLF    : ') $aNL[0]='NutzerLF    : '.$aNL[0]; $a[3]=implode(';',$aNL);
 if(substr($aFN[0],0,14)!='SortierFeld : ') $aOF[0]='SortierFeld : '.$aOF[0]; $a[4]=implode(';',$aOF);
 if(substr($aFN[0],0,14)!='LinkFeld    : ') $aLK[0]='LinkFeld    : '.$aLK[0]; $a[5]=implode(';',$aLK);
 if(substr($aFN[0],0,14)!='SpaltenStil : ') $aSS[0]='SpaltenStil : '.$aSS[0]; $a[6]=implode(';',$aSS);
 if(substr($aFN[0],0,14)!='DetailFeld  : ') $aDF[0]='DetailFeld  : '.$aDF[0]; $a[7]=implode(';',$aDF);
 if(substr($aFN[0],0,14)!='NutzerDF    : ') $aND[0]='NutzerDF    : '.$aND[0]; $a[8]=implode(';',$aND);
 if(substr($aFN[0],0,14)!='ZeilenStil  : ') $aZS[0]='ZeilenStil  : '.$aZS[0]; $a[9]=implode(';',$aZS);
 if(substr($aFN[0],0,14)!='SuchFeld    : ') $aSF[0]='SuchFeld    : '.$aSF[0]; $a[10]=implode(';',$aSF);
 $aEF[0]='EingabeFeld : 1'; $a[11]=implode(';',$aEF);
 $aNE[0]='NutzerEF    : 1'; $a[12]=implode(';',$aNE); //$aPF[1]='1';
 $aPF[0]='PflichtFeld : 1'; $a[13]=implode(';',$aPF); //$aET[1]='';
 $aTZ[0]='TrennZeile  : 0'; $a[14]=implode(';',$aTZ);
 $aET[0]='EingabeTexte: ';  $a[15]=implode(';',$aET); $aAW[1]='';
 $aAW[0]='AuswahlWerte: ';  $a[16]=implode(';',$aAW);
 $a[17]=(isset($aStru[17])?rtrim($aStru[17]):'Kategorien  : ');
 $a[18]=(isset($aStru[18])?rtrim($aStru[18]):'Symbole     : '); //Kategorien, Symbole
 $aEB[0]='EingabeLang : 0'; $a[19]=implode(';',$aEB); //Feldlaenge
 $aFN[0]=substr($aFN[0],14); $aFT[0]='i'; $aFT[1]='d';
 $aLF[0]=substr($aLF[0],14,1); $aNL[0]=substr($aNL[0],14,1);
 $aOF[0]=substr($aOF[0],14,1); $aLK[0]=substr($aLK[0],14,1);
 $aDF[0]=substr($aDF[0],14,1); $aND[0]=substr($aND[0],14,1);
 $aSS[0]=substr($aSS[0],14);   $aZS[0]=substr($aZS[0],14);
 $aSF[0]=substr($aSF[0],14,1); $aEF[0]='1'; $aNE[0]='1'; $aEB[0]='0'; $aPF[0]='1'; $aTZ[0]='0'; $aET[0]=''; $aAW[0]='';
 return $a;
}
function fMpSpeichereStruktur($sNr,$aTmp,$DbO){
 if(!MP_SQL){//Text
  if($f=fopen(MP_Pfad.MP_Daten.$sNr.MP_Struktur,'w')){
   fwrite($f,rtrim(implode("\n",$aTmp))."\n"); fclose($f); $E='OK';
  }else $E=str_replace('#',MP_Daten.$sNr.MP_Struktur,MP_TxDateiRechte);
 }elseif($DbO){//SQL
  if(isset($aTmp[16])) $aTmp[16]=str_replace('\n ','/n/',$aTmp[16]);
  if($DbO->query('UPDATE IGNORE '.MP_SqlTabS.' SET struktur="'.str_replace('"','\"',rtrim(implode("\r\n",$aTmp))).'" WHERE nr='.((int)$sNr))){
   $E='OK';
  }else $E=MP_TxSqlAendr;
 }else $E=MP_TxSqlVrbdg;
 return $E;
}
?>