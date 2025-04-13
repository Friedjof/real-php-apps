<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminstruktur anpassen','','KTs');

$P0=''; $F0=''; $E0=''; $T0=''; $sZiel=''; $nLsch=0; $sHide='';
$nFelder=count($kal_FeldName); $aV=file(KAL_Pfad.KAL_Daten.KAL_Vorgaben);
$aVBoxFelder= explode(',',KAL_VBoxFelder); $aVBoxNFelder=explode(',',KAL_VBoxNFelder); $aVBoxLinkFld=explode(',',KAL_VBoxLinkFld); $aVBoxFldStil=explode(',',KAL_VBoxFldStil);
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); include('feldtypenInc.php');
 if(!KAL_SQL){ //Textdatei
  if(is_writable(KAL_Pfad.KAL_Daten.KAL_Termine)) $sZiel='Txt';
  else $Msg='<p class="admFehl">In die Termindatei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden.</p>';
 }else{ //SQL
  if($DbO) $sZiel='Sql';
  else $Msg='<p class="admFehl">'.KAL_TxSqlVrbdg.'</p>';
 }
 if($sZiel){
  if(is_writable(KAL_Pfad.KAL_Daten.KAL_Vorgaben)){
   if($nLsch=(isset($_POST['kalLsch'])?(int)$_POST['kalLsch']:0)){ //Feld loeschen
    if($nLsch!=(isset($_POST['OkLsch'])?$_POST['OkLsch']:'')){ //Sicherheitsabfrage
     $Msg='<p class="admFehl">Das Feld an Position-'.$nLsch.' (<i>'.$kal_FeldName[$nLsch].'</i>) wirklich löschen?</p>';
     $sHide='<input type="hidden" name="OkLsch" value="'.$nLsch.'" />';
    }else{ //nun loeschen
     $sNam=$kal_FeldName[$nLsch];
     if($j=$kal_ListenFeld[$nLsch]) for($i=1;$i<$nFelder;$i++) if($kal_ListenFeld[$i]>$j) --$kal_ListenFeld[$i];
     if($j=$kal_NListenFeld[$nLsch]) for($i=1;$i<$nFelder;$i++) if($kal_NListenFeld[$i]>$j) --$kal_NListenFeld[$i];
     if($j=$kal_AktuelleFeld[$nLsch]) for($i=1;$i<$nFelder;$i++) if($kal_AktuelleFeld[$i]>$j) --$kal_AktuelleFeld[$i];
     if($j=$kal_LaufendeFeld[$nLsch]) for($i=1;$i<$nFelder;$i++) if($kal_LaufendeFeld[$i]>$j) --$kal_LaufendeFeld[$i];
     if($j=$kal_NeueFeld[$nLsch]) for($i=1;$i<$nFelder;$i++) if($kal_NeueFeld[$i]>$j) --$kal_NeueFeld[$i];
     if($j=$aVBoxFelder[$nLsch]) for($i=1;$i<$nFelder;$i++) if($aVBoxFelder[$i]>$j) --$aVBoxFelder[$i];
     if($j=$aVBoxNFelder[$nLsch]) for($i=1;$i<$nFelder;$i++) if($aVBoxNFelder[$i]>$j) --$aVBoxNFelder[$i];
     array_splice($kal_FeldName,$nLsch,1); fSetzArray($kal_FeldName,'FeldName','"'); array_splice($kal_FeldType,$nLsch,1); fSetzArray($kal_FeldType,'FeldType',"'");
     array_splice($kal_ListenFeld,$nLsch,1); fSetzArray($kal_ListenFeld,'ListenFeld',''); array_splice($kal_NListenFeld,$nLsch,1); fSetzArray($kal_NListenFeld,'NListenFeld','');
     array_splice($kal_SortierFeld,$nLsch,1); fSetzArray($kal_SortierFeld,'SortierFeld',''); array_splice($kal_LinkFeld,$nLsch,1); fSetzArray($kal_LinkFeld,'LinkFeld','');
     array_splice($kal_DetailFeld,$nLsch,1); fSetzArray($kal_DetailFeld,'DetailFeld',''); array_splice($kal_NDetailFeld,$nLsch,1); fSetzArray($kal_NDetailFeld,'NDetailFeld','');
     array_splice($kal_SuchFeld,$nLsch,1); fSetzArray($kal_SuchFeld,'SuchFeld',''); array_splice($kal_PflichtFeld,$nLsch,1); fSetzArray($kal_PflichtFeld,'PflichtFeld','');
     array_splice($kal_EingabeFeld,$nLsch,1); fSetzArray($kal_EingabeFeld,'EingabeFeld',''); array_splice($kal_NEingabeFeld,$nLsch,1);fSetzArray($kal_NEingabeFeld,'NEingabeFeld',''); array_splice($kal_EingabeLang,$nLsch,1); fSetzArray($kal_EingabeLang,'EingabeLang','');
     array_splice($kal_KopierFeld,$nLsch,1); fSetzArray($kal_KopierFeld,'KopierFeld',''); array_splice($kal_NKopierFeld,$nLsch,1);fSetzArray($kal_NKopierFeld,'NKopierFeld','');
     array_splice($kal_SpaltenStil,$nLsch,1); fSetzArray($kal_SpaltenStil,'SpaltenStil',"'"); array_splice($kal_ZeilenStil,$nLsch,1); fSetzArray($kal_ZeilenStil,'ZeilenStil',"'");
     array_splice($kal_AktuelleFeld,$nLsch,1); fSetzArray($kal_AktuelleFeld,'AktuelleFeld',''); array_splice($kal_LaufendeFeld,$nLsch,1); fSetzArray($kal_LaufendeFeld,'LaufendeFeld',''); array_splice($kal_NeueFeld,$nLsch,1); fSetzArray($kal_NeueFeld,'NeueFeld','');
     array_splice($kal_AktuelleLink,$nLsch,1); fSetzArray($kal_AktuelleLink,'AktuelleLink',''); array_splice($kal_LaufendeLink,$nLsch,1); fSetzArray($kal_LaufendeLink,'LaufendeLink',''); array_splice($kal_NeueLink,$nLsch,1); fSetzArray($kal_NeueLink,'NeueLink','');
     array_splice($kal_AktuelleStil,$nLsch,1); fSetzArray($kal_AktuelleStil,'AktuelleStil',"'");array_splice($kal_LaufendeStil,$nLsch,1); fSetzArray($kal_LaufendeStil,'LaufendeStil',"'");array_splice($kal_NeueStil,$nLsch,1); fSetzArray($kal_NeueStil,'NeueStil',"'");
     array_splice($aVBoxFelder,$nLsch,1); array_splice($aVBoxNFelder,$nLsch,1); array_splice($aVBoxLinkFld,$nLsch,1); array_splice($aVBoxFldStil,$nLsch,1);
     $sLF=''; $sNF=''; $sLk=''; $sFS=''; for($i=0;$i<($nFelder-1);$i++){$sLF.=(isset($aVBoxFelder[$i])?$aVBoxFelder[$i]:'0').','; $sNF.=(isset($aVBoxNFelder[$i])?$aVBoxNFelder[$i]:'0').','; $sLk.=(isset($aVBoxLinkFld[$i])?$aVBoxLinkFld[$i]:'0').','; $sFS.=(isset($aVBoxFldStil[$i])?$aVBoxFldStil[$i]:'').',';}
     fSetzKalWert(substr($sLF,0,-1),'VBoxFelder',"'"); fSetzKalWert(substr($sNF,0,-1),'VBoxNFelder',"'"); fSetzKalWert(substr($sLk,0,-1),'VBoxLinkFld',"'"); fSetzKalWert(substr($sFS,0,-1),'VBoxFldStil',"'");
     if(KAL_MDetail1Fld>0&&KAL_MDetail1Fld>=$nLsch){if(KAL_MDetail1Fld>$nLsch) fSetzKalWert(KAL_MDetail1Fld-1,'MDetail1Fld','');else fSetzKalWert(-1,'MDetail1Fld','');}
     if(KAL_MDetail2Fld>0&&KAL_MDetail2Fld>=$nLsch){if(KAL_MDetail2Fld>$nLsch) fSetzKalWert(KAL_MDetail2Fld-1,'MDetail2Fld','');else fSetzKalWert(-1,'MDetail2Fld','');}
     if(KAL_MDetail3Fld>0&&KAL_MDetail3Fld>=$nLsch){if(KAL_MDetail3Fld>$nLsch) fSetzKalWert(KAL_MDetail3Fld-1,'MDetail3Fld','');else fSetzKalWert(-1,'MDetail3Fld','');}
     if(KAL_MDetail4Fld>0&&KAL_MDetail4Fld>=$nLsch){if(KAL_MDetail4Fld>$nLsch) fSetzKalWert(KAL_MDetail4Fld-1,'MDetail4Fld','');else fSetzKalWert(-1,'MDetail4Fld','');}
     if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $nFelder=count($kal_FeldName);
      $Msg='<p class="admErfo">Das Feld <i>'.$sNam.'</i> an Position-'.$nLsch.' wurde gelöscht.</p>';
      if($sZiel=='Txt'){ //Text
       $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
       $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
       for($i=1;$i<$nSaetze;$i++){ //vorhandene Termine
        $a=explode(';',rtrim($aD[$i])); $s=$a[0]; $nId=max($nId,(int)$s); $s.=';'.$a[1]; array_splice($a,1,1);
        for($j=1;$j<=$nFelder;$j++) if($j!=$nLsch) $s.=';'.$a[$j];
        if(isset($a[$nFelder+1])&&($w=$a[$nFelder+1])) $s.=';'.$w; $aD[$i]=$s.NL;
       }
       $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
       else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';
      }elseif($DbO){ //SQL
       if(!$DbO->query('ALTER TABLE '.KAL_SqlTabT.' DROP kal_'.$nLsch))
        $Msg.='<p class="admFehl">Das Feld konnte nicht aus der MySQL-Tabelle <i>'.KAL_SqlTabT.'</i> gelöscht werden!</p>';
       for($i=$nLsch;$i<$nFelder;$i++) $DbO->query('ALTER TABLE '.KAL_SqlTabT.' CHANGE kal_'.($i+1).' kal_'.$i.' '.$aSql[$kal_FeldType[$i]]);
      }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>';
      array_splice($aV,$nLsch,1); $nLsch=0;
      if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aV[$i])?rtrim($aV[$i]).NL:NL)); fclose($f);}
      else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
     }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
    }
   }elseif($F0=(isset($_POST['F0'])?str_replace(';',':',str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['F0']))))):'')){ //neues Feld
    if(!$P0=min((isset($_POST['P0'])?$_POST['P0']:0),$nFelder)) $P0=$nFelder; $E0=(isset($_POST['E0'])?str_replace(';','`,',str_replace('"',"'",stripslashes(trim($_POST['E0'])))):''); $bWrnTyp=false;
    if($T0=(isset($_POST['T0'])?$_POST['T0']:'')){
     if(!in_array($F0,$kal_FeldName)){
      if($P0>1){
       if($T0=='k'||$T0=='s') if(in_array($T0,$kal_FeldType)) $bWrnTyp=true;
       array_splice($kal_FeldName,$P0,0,$F0); fSetzArray($kal_FeldName,'FeldName','"'); array_splice($kal_FeldType,$P0,0,$T0); fSetzArray($kal_FeldType,'FeldType',"'");
       array_splice($kal_ListenFeld,$P0,0,0); fSetzArray($kal_ListenFeld,'ListenFeld',''); array_splice($kal_NListenFeld,$P0,0,0); fSetzArray($kal_NListenFeld,'NListenFeld','');
       array_splice($kal_SortierFeld,$P0,0,0); fSetzArray($kal_SortierFeld,'SortierFeld',''); array_splice($kal_LinkFeld,$P0,0,0); fSetzArray($kal_LinkFeld,'LinkFeld','');
       array_splice($kal_DetailFeld,$P0,0,1); fSetzArray($kal_DetailFeld,'DetailFeld',''); array_splice($kal_NDetailFeld,$P0,0,1); fSetzArray($kal_NDetailFeld,'NDetailFeld','');
       array_splice($kal_SuchFeld,$P0,0,0); fSetzArray($kal_SuchFeld,'SuchFeld',''); array_splice($kal_PflichtFeld,$P0,0,0); fSetzArray($kal_PflichtFeld,'PflichtFeld','');
       array_splice($kal_EingabeFeld,$P0,0,1); fSetzArray($kal_EingabeFeld,'EingabeFeld',''); array_splice($kal_NEingabeFeld,$P0,0,1); fSetzArray($kal_NEingabeFeld,'NEingabeFeld',''); array_splice($kal_EingabeLang,$P0,0,0); fSetzArray($kal_EingabeLang,'EingabeLang','');
       array_splice($kal_KopierFeld,$P0,0,1); fSetzArray($kal_KopierFeld,'KopierFeld',''); array_splice($kal_NKopierFeld,$P0,0,1); fSetzArray($kal_NKopierFeld,'NKopierFeld','');
       array_splice($kal_SpaltenStil,$P0,0,''); fSetzArray($kal_SpaltenStil,'SpaltenStil',"'"); array_splice($kal_ZeilenStil,$P0,0,''); fSetzArray($kal_ZeilenStil,'ZeilenStil',"'");
       array_splice($kal_AktuelleFeld,$P0,0,0); fSetzArray($kal_AktuelleFeld,'AktuelleFeld',''); array_splice($kal_LaufendeFeld,$P0,0,0); fSetzArray($kal_LaufendeFeld,'LaufendeFeld',''); array_splice($kal_NeueFeld,$P0,0,0); fSetzArray($kal_NeueFeld,'NeueFeld','');
       array_splice($kal_AktuelleLink,$P0,0,0); fSetzArray($kal_AktuelleLink,'AktuelleLink',''); array_splice($kal_LaufendeLink,$P0,0,0); fSetzArray($kal_LaufendeLink,'LaufendeLink',''); array_splice($kal_NeueLink,$P0,0,0); fSetzArray($kal_NeueLink,'NeueLink','');
       array_splice($kal_AktuelleStil,$P0,0,'');fSetzArray($kal_AktuelleStil,'AktuelleStil',"'");array_splice($kal_LaufendeStil,$P0,0,'');fSetzArray($kal_LaufendeStil,'LaufendeStil',"'");array_splice($kal_NeueStil,$P0,0,'');fSetzArray($kal_NeueStil,'NeueStil',"'");
       array_splice($aVBoxFelder,$P0,0,0); array_splice($aVBoxNFelder,$P0,0,0); array_splice($aVBoxLinkFld,$P0,0,0); array_splice($aVBoxFldStil,$P0,0,'');
       $sLF=''; $sNF=''; $sLk=''; $sFS=''; for($i=0;$i<($nFelder+1);$i++){$sLF.=(isset($aVBoxFelder[$i])?$aVBoxFelder[$i]:'0').','; $sNF.=(isset($aVBoxNFelder[$i])?$aVBoxNFelder[$i]:'0').','; $sLk.=(isset($aVBoxLinkFld[$i])?$aVBoxLinkFld[$i]:'0').','; $sFS.=(isset($aVBoxFldStil[$i])?$aVBoxFldStil[$i]:'').',';}
       fSetzKalWert(substr($sLF,0,-1),'VBoxFelder',"'"); fSetzKalWert(substr($sNF,0,-1),'VBoxNFelder',"'"); fSetzKalWert(substr($sLk,0,-1),'VBoxLinkFld',"'"); fSetzKalWert(substr($sFS,0,-1),'VBoxFldStil',"'");
       if(KAL_MDetail1Fld>0&&KAL_MDetail1Fld>=$P0) fSetzKalWert(KAL_MDetail1Fld+1,'MDetail1Fld','');
       if(KAL_MDetail2Fld>0&&KAL_MDetail2Fld>=$P0) fSetzKalWert(KAL_MDetail2Fld+1,'MDetail2Fld','');
       if(KAL_MDetail3Fld>0&&KAL_MDetail3Fld>=$P0) fSetzKalWert(KAL_MDetail3Fld+1,'MDetail3Fld','');
       if(KAL_MDetail4Fld>0&&KAL_MDetail4Fld>=$P0) fSetzKalWert(KAL_MDetail4Fld+1,'MDetail4Fld','');
       if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
        fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
        $Msg='<p class="admErfo">Das neue Feld wurde '.($P0==$nFelder?'an':'ein').'gefügt.</p>';
        if($sZiel=='Txt'){ //Text
         $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
         $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
         for($i=1;$i<$nSaetze;$i++){
          $s=rtrim($aD[$i]); $nId=max($nId,(int)substr($s,0,strpos($s,';')));
          if($P0<$nFelder||substr_count($s,';')>$nFelder){$q=0; for($j=0;$j<=$P0;$j++){$p=strpos($s,';',$q); $q=++$p;} $aD[$i]=substr_replace($s,';',$p,0).NL;}
          else $aD[$i]=$s.";\n";
         }
         $s='Nummer_'.$nId.';online'; for($i=1;$i<=$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
         if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
         else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';
        }elseif($DbO){ //SQL
         if($P0<$nFelder) for($i=$nFelder-1;$i>=$P0;$i--) $DbO->query('ALTER TABLE '.KAL_SqlTabT.' CHANGE kal_'.$i.' kal_'.($i+1).' '.$aSql[$kal_FeldType[$i+1]]);
         if(!$DbO->query('ALTER TABLE '.KAL_SqlTabT.' ADD kal_'.$P0.' '.$aSql[$T0].' AFTER kal_'.($P0-1)))
          $Msg.='<p class="admFehl">Das Feld konnte nicht in die MySQL-Tabelle <i>'.KAL_SqlTabT.'</i> geschrieben werden!</p>';
        }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>';
        for($i=0;$i<$nFelder;$i++) $aV[$i]=(isset($aV[$i])?trim($aV[$i]).NL:NL); array_splice($aV,$P0,0,$E0.NL);
        if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){for($i=0;$i<=$nFelder;$i++) fwrite($f,rtrim($aV[$i]).NL); fclose($f);}
        else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
        if($bWrnTyp) $Msg.='<p class="admFehl">Warnung: Es ist nicht sinnvoll 2 Felder vom Typ <i>'.$aTyp[$T0].'</i> zu vereinbaren!</p>';
        $P0=''; $F0=''; $E0=''; $T0=''; $nFelder=count($kal_FeldName);
       }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
      }else $Msg='<p class="admFehl">Die Feldposition muss größer als 1 sein!</p>';
     }else $Msg='<p class="admFehl">Ein Feld mit dem Namen <i>'.$F0.'</i> existiert bereits!</p>';
    }else $Msg='<p class="admFehl">Bitte Feldtyp zum neuen Feld <i>'.$F0.'</i> auswählen!</p>';
   }else{ //Aenderung
    $aAltType=$kal_FeldType; $bNeu=false; $bNam=false; $bTyp=false; $bErk=false; $sWrnTyp=''; $aEncr=array(); $aDecr=array();
    for($i=1;$i<$nFelder;$i++){
     $sNam=(isset($_POST['F'.$i])?str_replace(';',':',str_replace('"',"'",stripslashes(@strip_tags(trim($_POST['F'.$i]))))):''); $sTyp=(isset($_POST['T'.$i])?$_POST['T'.$i]:'');
     $sErk=(isset($_POST['E'.$i])?str_replace(';','`,',str_replace('"',"'",stripslashes(trim($_POST['E'.$i])))):'');
     if($sNam!=$kal_FeldName[$i]&&$sNam>'') if(array_search($sNam,$kal_FeldName)===false) {$kal_FeldName[$i]=$sNam; $bNam=true; $bNeu=true;}
     else $Msg='<p class="admFehl">Ein Feld mit dem Namen <i>'.$sNam.'</i> existiert bereits!</p>';
     if($sTyp!=$kal_FeldType[$i]) if($i>1){
      if($sTyp=='k'||$sTyp=='s'){if(in_array($sTyp,$kal_FeldType)) $sWrnTyp=$sTyp;} //mehrere Kategorien/Symbole
      elseif(($sTyp=='e'&&$kal_FeldType[$i]!='c'||$sTyp=='c'&&$kal_FeldType[$i]!='e')&&$sZiel=='Txt') $aEncr[]=$i; // verschlüsseln
      elseif(($kal_FeldType[$i]=='e'&&$sTyp!='c'||$kal_FeldType[$i]=='c'&&$sTyp!='e')&&$sZiel=='Txt') $aDecr[]=$i; //entschlüsseln
      $kal_FeldType[$i]=$sTyp; $bTyp=true; $bNeu=true;
     }
     $sAltErk=trim($aV[$i]); $p=strpos($sAltErk,';'); if(!($p===false)) $sAltErk=substr($sAltErk,0,$p);
     if($sErk!=$sAltErk){$aV[$i]=$sErk.($p===false?'':(isset($aV[$i])?substr(trim($aV[$i]),$p):'')).NL; $bErk=true;}
    }
    if($bNeu){ //Name oder Typ
     if($bNam) fSetzArray($kal_FeldName,'FeldName','"'); if($bTyp) fSetzArray($kal_FeldType,'FeldType',"'");
     if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
      fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
      $Msg='<p class="admErfo">Die Änderungen an der Terminstruktur wurden gespeichert!</p>'; $bNeu=false; $bFhl=false;
      if($sWrnTyp) $Msg.='<p class="admFehl">Warnung: Es ist nicht sinnvoll 2 Felder vom Typ <i>'.$aTyp[$sWrnTyp].'</i> zu vereinbaren!</p>';
      if($sZiel=='Txt'){ //Text
       if($bNam){ //Datenkopfzeile ändern
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
        $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
        for($i=1;$i<$nSaetze;$i++){$s=substr($aD[$i],0,16); $nId=max($nId,(int)substr($s,0,strpos($s,';')));}
        $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
        if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
        else{$bErk=false; $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';}
       }
       if($bTyp&&(count($aEncr)>0||count($aDecr)>0)){//E-Mail-Felder codieren
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
        $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); reset($aEncr); reset($aDecr);
         foreach($aEncr as $n) $a[$n+1]=fKalEnCode($a[$n+1]); //+1 wegen online
         foreach($aDecr as $n) $a[$n+1]=fKalDeCode($a[$n+1]);
         $aD[$i]=implode(';',$a).NL;
        }
        $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
        if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
        else{$bErk=false; $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';}
       }
      }elseif($DbO){ // SQL
       if($bTyp) for($i=2;$i<$nFelder;$i++) if($aAltType[$i]!=$kal_FeldType[$i]){ //Typ ändern
        if(!$DbO->query('ALTER TABLE '.KAL_SqlTabT.' CHANGE kal_'.$i.' kal_'.$i.' '.$aSql[$kal_FeldType[$i]])) $bFhl=true;
       }
       if($bFhl) $Msg.='<p class="admFehl">Das Feld in der MySQL-Tabelle <i>'.KAL_SqlTabT.'</i> konnte nicht geändert werden!</p>';
      }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>';
     }else{$bErk=false; $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';}
    }
    if($bErk) if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){ //Erklärung
     for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aV[$i])?trim($aV[$i]).NL:NL)); fclose($f);
     if(!$Msg) $Msg='<p class="admErfo">Die Änderungen an den Erklärungen wurden gespeichert.</p>';
    }else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
    if(!$bNeu){ //Reihenfolge
     $aNeu=array(); $aPos=array(0,1); for($i=2;$i<$nFelder;$i++) $aPos[$i]=$i;
     for($i=2;$i<$nFelder;$i++){ //neue Nummern testen
      $p=(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0); if($p!=$i) if($p>1){$aPos[$i]=$p+($p>$i?0.1:-0.1); $bNeu=true;}
     }
     if($bNeu){ //umnumerieren
      asort($aPos); reset($aPos); foreach($aPos as $k=>$xx) $aNeu[]=$k; //korrigieren
      $aFN=$kal_FeldName; $aFT=$kal_FeldType; $aLF=$kal_ListenFeld; $aNLF=$kal_NListenFeld; $aSF=$kal_SortierFeld;
      $aL2=$kal_LinkFeld; $aDF=$kal_DetailFeld; $aNDF=$kal_NDetailFeld; $aS2=$kal_SuchFeld; $aPF=$kal_PflichtFeld;
      $aEF=$kal_EingabeFeld; $aNEF=$kal_NEingabeFeld; $aEL=$kal_EingabeLang; $aKF=$kal_KopierFeld; $aNKF=$kal_NKopierFeld;
      $aSS=$kal_SpaltenStil; $aZS=$kal_ZeilenStil;
      $aAF=$kal_AktuelleFeld; $aAL=$kal_AktuelleLink; $aAS=$kal_AktuelleStil;
      $aL3=$kal_LaufendeFeld; $aLL=$kal_LaufendeLink; $aLS=$kal_LaufendeStil;
      $aNF=$kal_NeueFeld; $aNL=$kal_NeueLink; $aNS=$kal_NeueStil;
      $aVF=$aVBoxFelder; $aVN=$aVBoxNFelder; $aVL=$aVBoxLinkFld; $aVS=$aVBoxFldStil;
      for($i=2;$i<$nFelder;$i++){
       $j=$aNeu[$i];
       $kal_FeldName[$i]=$aFN[$j]; $kal_FeldType[$i]=$aFT[$j]; $kal_ListenFeld[$i]=$aLF[$j]; $kal_NListenFeld[$i]=$aNLF[$j];
       $kal_SortierFeld[$i]=$aSF[$j]; $kal_LinkFeld[$i]=$aL2[$j]; $kal_DetailFeld[$i]=$aDF[$j]; $kal_NDetailFeld[$i]=$aNDF[$j];
       $kal_SuchFeld[$i]=$aS2[$j]; $kal_PflichtFeld[$i]=$aPF[$j]; $kal_EingabeFeld[$i]=$aEF[$j]; $kal_NEingabeFeld[$i]=$aNEF[$j]; $kal_EingabeLang[$i]=$aEL[$j];
       $kal_KopierFeld[$i]=$aKF[$j]; $kal_NKopierFeld[$i]=$aNKF[$j]; $kal_SpaltenStil[$i]=$aSS[$j]; $kal_ZeilenStil[$i]=$aZS[$j];
       $kal_AktuelleFeld[$i]=sprintf('%0d',$aAF[$j]); $kal_AktuelleLink[$i]==sprintf('%0d',$aAL[$j]); $kal_AktuelleStil[$i]=$aAS[$j];
       $kal_LaufendeFeld[$i]=sprintf('%0d',$aL3[$j]); $kal_LaufendeLink[$i]==sprintf('%0d',$aLL[$j]); $kal_LaufendeStil[$i]=$aLS[$j];
       $kal_NeueFeld[$i]=sprintf('%0d',$aNF[$j]); $kal_NeueLink[$i]==sprintf('%0d',$aNL[$j]); $kal_NeueStil[$i]=$aNS[$j];
       $aVBoxFelder[$i]=sprintf('%0d',$aVF[$j]); $aVBoxNFelder[$i]=sprintf('%0d',$aVN[$j]); $aVBoxLinkFld[$i]=sprintf('%0d',$aVL[$j]); $aVBoxFldStil[$i]=$aVS[$j];
      }
      fSetzArray($kal_FeldName,'FeldName','"'); fSetzArray($kal_FeldType,'FeldType',"'");
      fSetzArray($kal_ListenFeld,'ListenFeld',''); fSetzArray($kal_NListenFeld,'NListenFeld','');
      fSetzArray($kal_SortierFeld,'SortierFeld',''); fSetzArray($kal_LinkFeld,'LinkFeld','');
      fSetzArray($kal_DetailFeld,'DetailFeld',''); fSetzArray($kal_NDetailFeld,'NDetailFeld','');
      fSetzArray($kal_SuchFeld,'SuchFeld',''); fSetzArray($kal_PflichtFeld,'PflichtFeld','');
      fSetzArray($kal_EingabeFeld,'EingabeFeld',''); fSetzArray($kal_NEingabeFeld,'NEingabeFeld',''); fSetzArray($kal_EingabeLang,'EingabeLang','');
      fSetzArray($kal_KopierFeld,'KopierFeld',''); fSetzArray($kal_NKopierFeld,'NKopierFeld','');
      fSetzArray($kal_SpaltenStil,'SpaltenStil',"'"); fSetzArray($kal_ZeilenStil,'ZeilenStil',"'");
      fSetzArray($kal_AktuelleFeld,'AktuelleFeld',''); fSetzArray($kal_LaufendeFeld,'LaufendeFeld',''); fSetzArray($kal_NeueFeld,'NeueFeld','');
      fSetzArray($kal_AktuelleLink,'AktuelleLink',''); fSetzArray($kal_LaufendeLink,'LaufendeLink',''); fSetzArray($kal_NeueLink,'NeueLink','');
      fSetzArray($kal_AktuelleStil,'AktuelleStil',"'");fSetzArray($kal_LaufendeStil,'LaufendeStil',"'");fSetzArray($kal_NeueStil,'NeueStil',"'");
      $sLF=''; $sNF=''; $sLk=''; $sFS=''; for($i=0;$i<$nFelder;$i++){$sLF.=$aVBoxFelder[$i].','; $sNF.=$aVBoxNFelder[$i].','; $sLk.=$aVBoxLinkFld[$i].','; $sFS.=$aVBoxFldStil[$i].',';}
      fSetzKalWert(substr($sLF,0,-1),'VBoxFelder',"'"); fSetzKalWert(substr($sNF,0,-1),'VBoxNFelder',"'"); fSetzKalWert(substr($sLk,0,-1),'VBoxLinkFld',"'"); fSetzKalWert(substr($sFS,0,-1),'VBoxFldStil',"'");
      $aFNeu=array_flip($aNeu);
      if(KAL_MDetail1Fld>0&&$aFNeu[KAL_MDetail1Fld]!=KAL_MDetail1Fld) fSetzKalWert($aFNeu[KAL_MDetail1Fld],'MDetail1Fld','');
      if(KAL_MDetail2Fld>0&&$aFNeu[KAL_MDetail2Fld]!=KAL_MDetail2Fld) fSetzKalWert($aFNeu[KAL_MDetail2Fld],'MDetail2Fld','');
      if(KAL_MDetail3Fld>0&&$aFNeu[KAL_MDetail3Fld]!=KAL_MDetail3Fld) fSetzKalWert($aFNeu[KAL_MDetail3Fld],'MDetail3Fld','');
      if(KAL_MDetail4Fld>0&&$aFNeu[KAL_MDetail4Fld]!=KAL_MDetail4Fld) fSetzKalWert($aFNeu[KAL_MDetail4Fld],'MDetail4Fld','');
      if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
       fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
       if(!$Msg) $Msg='<p class="admErfo">Die geänderte Feldreihenfolge wurde gespeichert.</p>';
       if($sZiel=='Txt'){ //Text
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD);
        $nId=0; $s=$aD[0]; if(substr($s,0,7)=='Nummer_') $nId=(int)substr($s,7,strpos($s,';')); //Auto-ID-Nr holen
        for($i=1;$i<$nSaetze;$i++){ //vorhandene Termine
         $a=explode(';',rtrim($aD[$i])); $nId=max($nId,(int)$a[0]);
         $s=$a[0].';'.$a[1].';'.$a[2]; array_splice($a,1,1);
         for($j=2;$j<$nFelder;$j++) $s.=';'.$a[$aNeu[$j]];
         if(isset($a[$nFelder])&&($w=$a[$nFelder])) $s.=';'.$w;
         $aD[$i]=$s.NL;
        }
        $s='Nummer_'.$nId.';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $aD[0]=$s.NL;
        if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
        else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';
       }elseif($DbO){ //SQL
        $DbO->query('DROP TABLE IF EXISTS kal_temp_tab'); $s=''; for($i=2;$i<$nFelder;$i++) $s.=',kal_'.$aNeu[$i];
        if($DbO->query('CREATE TABLE kal_temp_tab (PRIMARY KEY (id)) SELECT id,online,kal_1'.$s.',periodik FROM '.KAL_SqlTabT)){
         $DbO->query('ALTER  TABLE kal_temp_tab CHANGE id id int(11) AUTO_INCREMENT, COMMENT="Kalender-Termine"');
         for($i=2;$i<$nFelder;$i++) $DbO->query('ALTER TABLE kal_temp_tab CHANGE kal_'.$aNeu[$i].' k'.$i.' '.$aSql[$kal_FeldType[$i]]);
         for($i=2;$i<$nFelder;$i++) $DbO->query('ALTER TABLE kal_temp_tab CHANGE k'.$i.' kal_'.$i.' '.$aSql[$kal_FeldType[$i]]);
         $DbO->query('DROP TABLE IF EXISTS '.KAL_SqlTabT);
         $DbO->query('ALTER TABLE kal_temp_tab RENAME '.KAL_SqlTabT); $DbO->query('OPTIMIZE TABLE '.KAL_SqlTabT);
        }else $Msg.='<p class="admFehl">Die MySQL-Tabelle <i>'.KAL_SqlTabT.'</i> konnte nicht umgespeichert werden!</p>';
       }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>';
       $a=array(); $a[0]=trim($aV[0]).NL; $a[1]=trim($aV[1]).NL; for($i=2;$i<$nFelder;$i++) $a[$i]=trim(isset($aV[$aNeu[$i]])?$aV[$aNeu[$i]]:'').NL;
       if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){$aV=$a; for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aV[$i])?trim($aV[$i]).NL:NL)); fclose($f);}
       else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
      }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
     } //ändern
    } //Reihenfolge
   } //fertig
  }else $Msg='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
 }//$sZiel
 if(!$Msg) $Msg='<p class="admMeld">Die Terminstruktur bleibt unverändert.</p>';
}

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Passen Sie die Terminstruktur entsprechend Ihren Wünschen an.</p>';
echo $Msg.NL;
?>

<form action="konfTermine.php" method="post">
<?php echo $sHide; $aE=explode(';',trim($aV[1]))?>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl">
 <td class="admSpa1" style="text-align:center"><b>Pos.</b></td>
 <td><b>Feldbezeichnung</b></td>
 <td><b>Erklärung</b> <span class="admMini">(nur bei Bedarf, wird im Eingabeformular angezeigt)</span></td>
 <td><div><b>Feldtyp</b></div><img src="<?php echo $sHttp?>grafik/pixel.gif" width="115" height="1" border="0" alt=""></td>
 <td><input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" border="0" title="ausgewähltes Feld löschen" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="text-align:center"><div style="width:22px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">0</div></td>
 <td><div style="width:130px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">Nummer</div></td>
 <td><div style="width:365px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">--</div></td>
 <td><select name="Tx" size="1" style="width:130px;"><option value="i">Zählnummer</option></select>&nbsp;<a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.3.Nummer" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td>&nbsp;</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="text-align:center"><div style="width:22px;padding:1px;border-style:solid;border-width:1px;border-color:#AAAAAA;">1</div></td>
 <td><input type="text" name="F1" value="<?php echo $kal_FeldName[1];?>" size="20" style="width:130px" /></td>
 <td><input type="text" name="E1" value="<?php echo str_replace('`,',';',$aE[0])?>" size="20" style="width:365px" /></td>
 <td><select name="T1" size="1" style="width:130px;"><option value="d">Datum</option></select></td>
 <td>&nbsp;</td>
</tr>

<?php
 $sOpt='<option value="d">Datum</option><option value="z">Uhrzeit</option><option value="@">Eintragszeit</option><option value="t">Text</option><option value="m">Memo</option><option value="a">Auswahl</option><option value="k">Kategorie</option><option value="s">Symbol</option><option value="j">Ja/Nein</option><option value="w">Währung</option><option value="n">Ganzzahl</option><option value="1">Zahl.1</option><option value="2">Zahl.2</option><option value="3">Zahl.3</option><option value="r">Zahl</option><option value="o">Postleitzahl</option><option value="b">Bild</option><option value="x">Straßenkarte</option><option value="f">Datei</option><option value="l">Link</option><option value="#">Zusage</option><option value="e">E-Mail</option><option value="c">Kontakt</option>'./* <option value="g">Gastkommentar</option> */'<option value="u">Benutzer</option><option value="p">Passwort</option><option value="v">Termin verstecken</option>';
 for($i=2;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i];
  $aE=explode(';',(isset($aV[$i])?trim($aV[$i]):''));
?>
<tr class="admTabl">
 <td class="admSpa1" style="text-align:center"><input type="text" name="P<?php echo $i;?>" value="<?php echo $i;?>" size="2" style="width:22px;" /></td>
 <td><input type="text" name="F<?php echo $i;?>" value="<?php echo $kal_FeldName[$i];?>" size="20" style="width:130px" /></td>
 <td><input type="text" name="E<?php echo $i;?>" value="<?php echo str_replace('`,',';',$aE[0])?>" size="20" style="width:365px" /></td>
 <td><select name="T<?php echo $i;?>" size="1" style="width:130px;"><?php echo substr_replace($sOpt,' selected="selected"',strpos($sOpt,'"'.$kal_FeldType[$i].'"')+3,0);?></select><?php if($kal_FeldType[$i]=='a'||$kal_FeldType[$i]=='k'||$kal_FeldType[$i]=='s'||$kal_FeldType[$i]=='j'||$kal_FeldType[$i]=='v'||$kal_FeldType[$i]=='t'||$kal_FeldType[$i]=='m') echo ' <a href="konfVorgaben.php?id='.$i.'"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Vorgabewert'.($kal_FeldType[$i]=='a'||$kal_FeldType[$i]=='k'||$kal_FeldType[$i]=='s'?'e':'').' bearbeiten"></a>'; elseif($kal_FeldType[$i]=='x') echo ' <a href="konfStreetMap.php"><img src="'.$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Karten-Einstellungen bearbeiten"></a>';?></td>
 <td><input class="admRadio" type="radio" name="kalLsch" value="<?php echo $i?>"<?php if($i==$nLsch) echo ' checked="checked"'?> /></td>
</tr>

<?php }?>

<tr class="admTabl">
 <td class="admSpa1" style="text-align:center"><input type="text" name="P0" value="<?php echo $P0;?>" size="2" style="width:22px;" /></td>
 <td><input type="text" name="F0" size="20" value="<?php echo $F0;?>" style="width:130px" /></td>
 <td><input type="text" name="E0" size="20" value="<?php echo $E0;?>" style="width:365px" /></td>
 <td><select name="T0" size="1" style="width:130px;"><option value=""></option><?php echo str_replace('"'.$T0.'"','"'.$T0.'" selected="selected"',$sOpt);?></select></td>
 <td width="16" align="center"><input type="image" src="<?php echo $sHttp?>grafik/iconLoeschen.gif" width="16" height="16" border="0" title="ausgewähltes Feld löschen" /></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php if(ADM_Breite<880){?>
<div class="admBox">
<p class="admFehl">Warnung!!</p><p>Sie haben eine Ausgabenbreite mit weniger als 880 Pixeln eingestellt.
Dadurch kann es passieren, dass die Schaltflächen am rechten Rand dieses Formulars
nicht sichtbar und somit nicht funktionsfähig sind.
Vergrößern Sie im Menüpunkt <a href="konfAdmin-php">Admin-Einstellungen</a>
die Breite der Ausgabe im Administratorbereich auf die empfohlenen 950 Pixel oder mehr.</p>
</div><?php }?>
<div class="admBox">
<p>Die Struktur der Termine ist frei definierbar.
Lediglich das Feld <i>Nummer</i> vom Typ <i>Zählnummer</i> und ein Feld vom Typ <i>Datum</i>
müssen am Anfang der Terminstruktur vorhanden sein.
Alle weiteren Felder können weggelassen, umbenannt, umsortiert oder ergänzt werden.</p>
<p>Pro Schalterklick wird immer nur <i>eine</i> Aktion ausgeführt.
Sie können also entweder ein Feld löschen, ein neues Feld ergänzen/einfügen oder ein vorhandenes Feld abändern.
Das <i>gleichzeitige</i> Ändern eines bestehenden Feldes <i>und</i> Ergänzen eines neuen Feldes beispielsweise ist nicht möglich.
Solche unterschiedlichen Aktionen müssen schrittweise nacheinander ausgeführt werden.
Löschen hat Vorrang vor Ergänzen und dieses vor Ändern.</p>
<p>Zum nachträglichen Ändern der Feldreihenfolge tragen Sie bei dem betreffenden Feld lediglich dessen künftige Wunschposition ein
und belassen alle anderen Positionsangaben auf den bisherigen Werten.</p>
<p><u><i>Wichtig</i></u>: Wenn Sie die Reihenfolge der Felder nachträglich ändern,
müssen Sie bei einigen Zusatzfunktionen des Kalender-Scripts diese geänderte Reihenfolge manuell einarbeiten.
Das betrifft das Zusagesystem, den RSS-Feed und den iCal-Export.</p>
<p>Ein Ausnahme stellt ein Feld mit der Feldbezeichnung <i>Wichtig</i> vom Typ <i>Ja/Nein</i> dar.
Wird ein solches Feld verwendet und Termine in diesem auf <i>Ja</i> gesetzt
so werden solche Termine in der Terminliste unabhängig vom Datum auch stets ausserhalb des Filterintervalls aufgelistet.</p>
<p>Eine nächste Ausnahme sind Felder vom Typ <i>Text</i> mit den besonderen Namen <i>TITLE</i>, <i>META-KEY</i> und <i>META-DES</i>.
Deren Inhalt wird über die Platzhalter <i>{TITLE}</i>, <i>{META-KEY}</i> bzw. <i>{META-DES}</i>
in der HTML-Schablone <i>kalSeite.htm</i> als <i>&lt;title&gt;-Tag</i>, als<i> meta-keywords</i> und als <i>meta-description</i> interpretiert.</p>
<p>Eine weitere Ausnahme sind Felder mit dem Feldnamen <i>KAPAZITAET</i> vom Typ <i>Text</i> sowie <i>ZUSAGE_BIS</i> vom Typ <i>Eintragszeit</i>.
Beide Felder würden vom Zusatzmodul <i>Zusagesystem</i> als maximale Platzkapazität der Veranstaltung (eventuell einschließlich Schwellenwert) bzw. als Anmeldeschluss zur Veranstaltung interpretiert.</p>
<p><u>Empfehlung</u>: Solange Sie noch Ihre optimale Terminstruktur suchen und an ihr &quot;herumbasteln&quot;
sollten Sie die Datenbasis noch nicht auf MySQL-Datenbank umgestellt haben sondern solche Umbauten unter der Text-Datenbasis vornehmen.</p>
</div>

<?php echo fSeitenFuss()?>