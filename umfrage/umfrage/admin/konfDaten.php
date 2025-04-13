<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('Datenbasis einstellen','','SDb');

$bBldNeu=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);
if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $usSQL=UMF_SQL; $usDaten=UMF_Daten; $usBilder=UMF_Bilder;
 $usSqlHost=UMF_SqlHost; $usSqlDaBa=UMF_SqlDaBa; $usSqlUser=UMF_SqlUser; $usSqlPass=UMF_SqlPass;
 $usSqlTabF=UMF_SqlTabF; $usSqlTabE=UMF_SqlTabE; $usSqlTabZ=UMF_SqlTabZ; $usSqlTabN=UMF_SqlTabN; $usSqlTabT=UMF_SqlTabT;
 $usFragen=UMF_Fragen; $usErgebnis=UMF_Ergebnis; $usZuweisung=UMF_Zuweisung; $usNutzer=UMF_Nutzer; $usTeilnahme=UMF_Teilnahme;
 $sTabFraLeer=''; $sTabErgLeer=''; $sTabZuwLeer=''; $sTabNtzLeer=''; $sTabTlnLeer='';
 $sTabSFrLeer=''; $sTabSErLeer=''; $sTabSZwLeer=''; $sTabSNuLeer=''; $sTabSTnLeer='';
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $usSQL=(txtVar('Sql')!='1'?false:true); $usDaten=txtVar('Daten'); $usBilder=txtVar('Bilder');
 $usSqlHost=txtVar('SqlHost'); $usSqlDaBa=txtVar('SqlDaBa'); $usSqlUser=txtVar('SqlUser'); $usSqlPass=txtVar('SqlPass');
 $usSqlTabF=txtVar('SqlTabF'); $usSqlTabE=txtVar('SqlTabE'); $usSqlTabZ=txtVar('SqlTabZ'); $usSqlTabN=txtVar('SqlTabN'); $usSqlTabT=txtVar('SqlTabT');
 $usFragen=txtVar('Fragen'); $usErgebnis=txtVar('Ergebnis'); $usZuweisung=txtVar('Zuweisung'); $usNutzer=txtVar('Nutzer'); $usTeilnahme=txtVar('Teilnahme');
 $umf_NutzerFelder=explode(';',UMF_NutzerFelder); $nNutzFelder=count($umf_NutzerFelder);
 $sTabFraLeer=txtVar('TabFraLeer'); $sTabErgLeer=txtVar('TabErgLeer'); $sTabZuwLeer=txtVar('TabZuwLeer'); $sTabNtzLeer=txtVar('TabNtzLeer'); $sTabTlnLeer=txtVar('TabTlnLeer');
 $sTabSFrLeer=txtVar('TabSFrLeer'); $sTabSErLeer=txtVar('TabSErLeer'); $sTabSZwLeer=txtVar('TabSZwLeer'); $sTabSNuLeer=txtVar('TabSNuLeer'); $sTabSTnLeer=txtVar('TabSTnLeer');
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $bToDo=true; $bNeu=false;
 if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
  foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
 //------
 $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php'))));
 if(!$usSQL){ //->Text
  if(!empty($usDaten)&&!empty($usFragen)&&!empty($usErgebnis)&&!empty($usZuweisung)&&!empty($usNutzer)&&!empty($usTeilnahme)){
   if(substr($usDaten,0,1)=='/') $usDaten=substr($usDaten,1); if(substr($usDaten,-1,1)!='/') $usDaten.='/';
   if($usFragen!=$usErgebnis&&$usFragen!=$usNutzer&&$usErgebnis!=$usNutzer&&$usTeilnahme!=$usNutzer&&$usZuweisung!=$usErgebnis){
    if($bToDo){
     $bToDo=false; $bOK=true; $nFragenZahl=0;
     if(!UMF_SQL){ //Text->Text
      if($usFragen!=UMF_Fragen||$usDaten!=UMF_Daten){ //Fragendatei
       $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen);
       if($f=fopen(UMF_Pfad.$usDaten.$usFragen,'w')){
        if($sTabFraLeer!='1'){$nFragenZahl=max(count($aD)-1,0); $s=str_replace("\r",'',implode('',$aD));}
        else $s='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=',Antwort'.$i;
        fwrite($f,trim($s).NL); fclose($f); $bNeu=true;
        $sMeld.='<p class="admErfo">Die neue Fragendatei <i>'.$usDaten.$usFragen.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Fragendatei <i>'.$usDaten.$usFragen.'</i>.</p>';}
      }
      if(($usErgebnis!=UMF_Ergebnis||$usDaten!=UMF_Daten)&&$bOK){ //Ergebnisdatei
       $aD=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nErgebnisZahl=($sTabErgLeer!='1'?$nFragenZahl:0);
       if($f=fopen(UMF_Pfad.$usDaten.$usErgebnis,'w')){
        fwrite($f,trim($aD[0]).NL);
        for($i=1;$i<=$nErgebnisZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Ergebnisdatei <i>'.$usDaten.$usErgebnis.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Ergebnisdatei <i>'.$usDaten.$usErgebnis.'</i>.</p>';}
      }
      if(($usZuweisung!=UMF_Zuweisung||$usDaten!=UMF_Daten)&&$bOK){ //Zuweisungendatei
       $aD=file(UMF_Pfad.UMF_Daten.UMF_Zuweisung);
       if($f=fopen(UMF_Pfad.$usDaten.$usZuweisung,'w')){
        if($sTabZuwLeer!='1') $s=str_replace("\r",'',implode('',$aD)); else $s='Benutzer;zugewiesene_Umfragen';
        fwrite($f,trim($s).NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Zuweisungsdatei <i>'.$usDaten.$usZuweisung.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Zuweisungsdatei <i>'.$usDaten.$usZuweisung.'</i>.</p>';}
      }
      if(($usNutzer!=UMF_Nutzer||$usDaten!=UMF_Daten)&&$bOK){ //Benutzerdatei
       $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nNutzerZahl=($sTabNtzLeer!='1'?count($aD):1);
       if($f=fopen(UMF_Pfad.$usDaten.$usNutzer,'w')){
        if(substr($aD[0],0,7)!='Nummer_'){
         $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5));
         $s='Nummer_'.$nMx; for($i=1;$i<$nNutzFelder;$i++) $s.=';'.$umf_NutzerFelder[$i]; $aD[0]=$s.NL;
        }
        for($i=0;$i<$nNutzerZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Benutzerdatei <i>'.$usDaten.$usNutzer.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Benutzerdatei <i>'.$usDaten.$usNutzer.'</i>.</p>';}
      }
      if(($usTeilnahme!=UMF_Teilnahme||$usDaten!=UMF_Daten)&&$bOK){ //Teilnahme
       $aD=file(UMF_Pfad.UMF_Daten.UMF_Teilnahme);
       if($f=fopen(UMF_Pfad.$usDaten.$usTeilnahme,'w')){
        if($sTabTlnLeer!='1') $s=str_replace("\r",'',implode('',$aD)); else $s='Datum;Status;Art;Nutzer;Ergebnis';
        fwrite($f,trim($s).NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Teilnahmedatei <i>'.$usDaten.$usTeilnahme.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Benutzerdatei <i>'.$usDaten.$usTeilnahme.'</i>.</p>';}
      }
      $bNeu=$bNeu&&$bOK;
     }else{//SQL->Text
      if($DbO){
       if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' ORDER BY Nummer')){
        $s=''; $aE=array('IP');
        $sKopf='Nummer;aktiv;Umfrage;Frage;Bild;Anmerkung1;Anmerkung2'; for($i=1;$i<=$nAntwAnzahl;$i++) $s.=',Antwort'.$i;
        if($sTabFraLeer!='1') while($a=$rR->fetch_row()){
         $s.=NL.$a[0]; $aE[(int)$a[0]]=$a[0].';0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0'.NL;
         for($i=1;$i<$nAntwAnzahl+7;$i++) $s.=';'.(isset($a[$i])?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i]))):'');
        }
        $rR->close();
        if($f=fopen(UMF_Pfad.$usDaten.$usFragen,'w')){
         fwrite($f,$sKopf.rtrim($s).NL); fclose($f); $bNeu=true;
         $sMeld.='<p class="admErfo">Die Fragendatei <i>'.$usDaten.$usFragen.'</i> wurde gespeichert.</p>';
         if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabE.' ORDER BY Nummer')){ //Ergebnisdatei
          if($a=$rR->fetch_row()) $aE[0]=str_replace('\"','"',trim($a[1])).NL; else $aE[0]="IP\n";
          while($a=$rR->fetch_row()){
           $nNr=(int)$a[0];
           if(isset($aE[$nNr]))
            if($sTabErgLeer!='1') $aE[$nNr]=$nNr.';'.str_replace('\"','"',trim($a[1])).NL; else $aE[$nNr]=$nNr.';0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0'.NL;
          }$rR->close();
          if($f=fopen(UMF_Pfad.$usDaten.$usErgebnis,'w')){
           fwrite($f,rtrim(implode('',$aE)).NL); fclose($f);
          }else{$bNeu=false; $sMeld='<p class="admFehl">Die Datei <i>'.$usDaten.$usErgebnis.'</i> konnte nicht geschrieben werden (Zugriff verweigert).</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Ergebinstabelle <i>'.UMF_SqlTabE.'</i>!</p>';}
         if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabZ.' ORDER BY Benutzer')){ //Zuweisungen
          $s='Benutzer;zugewiesene_Umfragen';
          if($sTabZuwLeer!='1') while($a=$rR->fetch_row()) $s.="\n".$a[0].';'.$a[1]; $rR->close();
          if($f=fopen(UMF_Pfad.$usDaten.$usZuweisung,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Zuweisungsdatei <i>'.$usDaten.$usZuweisung.'</i>.</p>';}
         }
         if($rR=$DbO->query('SELECT MAX(Nummer) FROM '.UMF_SqlTabN)){$a=$rR->fetch_row(); $rR->close();} //Nutzerdatei
         if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' ORDER BY Nummer')){
          $s='Nummer_'.(int)$a[0]; for($i=1;$i<$nNutzFelder;$i++) $s.=';'.$umf_NutzerFelder[$i];
          if($sTabNtzLeer!='1') while($a=$rR->fetch_row()){
           $s.=NL.$a[0].';'.$a[1].';'.fUmfEnCode($a[2]).';'.$a[3].';'.fUmfEnCode($a[4]);
           for($i=5;$i<$nNutzFelder;$i++) $s.=';'.str_replace(';','`,',str_replace('\"','"',$a[$i]));
          }$rR->close();
          if($f=fopen(UMF_Pfad.$usDaten.$usNutzer,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Benutzerdatei <i>'.$usDaten.$usNutzer.'</i>.</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Benutzertabelle <i>'.UMF_SqlTabN.'</i>!</p>';}
         if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabT.' ORDER BY Datum')){ //Teilnahme
          $s='Datum;Status;Art;Nutzer;Ergebnis';
          if($sTabTlnLeer!='1') while($a=$rR->fetch_row()){
           $b=explode(',',$a[4]); $t=array_shift($b); if($a[3]=='T') $t=fUmfEnCode($t); foreach($b as $u) $t.=','.fUmfEnCode($u);
           $s.="\n".$a[1].';'.$a[2].';'.$a[3].';'.$t.';'.$a[5];
          }$rR->close();
          if($f=fopen(UMF_Pfad.$usDaten.$usTeilnahme,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Zuweisungsdatei <i>'.$usDaten.$usZuweisung.'</i>.</p>';}
         }
        }else $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Fragendatei <i>'.$usDaten.$usFragen.'</i>!</p>';
       }else $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Fragentabelle <i>'.UMF_SqlTabF.'</i>!</p>';
      }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung mit den bisherigen Zugangsdaten!</p>';
     }//SQL->Text
    }//bToDo
    if($bNeu){
     fSetzUmfWert(false,'SQL',''); fSetzUmfWert($usDaten,'Daten',"'");
     fSetzUmfWert($usFragen,'Fragen',"'"); fSetzUmfWert($usErgebnis,'Ergebnis',"'"); fSetzUmfWert($usZuweisung,'Zuweisung',"'"); fSetzUmfWert($usNutzer,'Nutzer',"'"); fSetzUmfWert($usTeilnahme,'Teilnahme',"'");
    }
   }else $sMeld.='Die Dateinamen der 5 Dateien <i>'.$usFragen.'</i>, <i>'.$usErgebnis.'</i>, <i>'.$usZuweisung.'</i>, <i>'.$usNutzer.'</i>, <i>'.$usTeilnahme.'</i> müssen sich unterscheiden!';
  }else $sMeld.='Speicherpfad und Dateiname der Fragendatei, Ergebnisdatei, Nutzerdatei usw. dürfen nicht leer sein!';
 }else{ //->SQL
  $bDbConst=($DbO&&$usSqlHost==UMF_SqlHost&&$usSqlDaBa==UMF_SqlDaBa&&$usSqlUser==UMF_SqlUser&&$usSqlPass==UMF_SqlPass);
  if(!$bDbConst&&$DbO){$DbO->close(); $DbO=NULL;}
  if($bDbConst||($DbO=@new mysqli($usSqlHost,$usSqlUser,$usSqlPass,$usSqlDaBa))){ //ZielVerbindung
   if($bDbConst||!mysqli_connect_errno()){
    if(ADU_SqlCharSet) $DbO->set_charset(ADU_SqlCharSet);
    if($bToDo){
     $bToDo=false;
     $sF=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", Umfrage VARCHAR(31) NOT NULL DEFAULT "", Frage TEXT NOT NULL, Bild VARCHAR(128) NOT NULL DEFAULT "", Anmerkung1 TEXT NOT NULL, Anmerkung2 TEXT NOT NULL';
     for($i=1;$i<=$nAntwAnzahl;$i++) $sF.=', Antwort'.$i.' TEXT NOT NULL'; $sF.=', PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Fragen"';
     $sE=' (Nummer INT NOT NULL DEFAULT "0", Inhalt TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Ergebnis"';
     $sZ=' (Benutzer INT NOT NULL DEFAULT "0", Umfragen TEXT NOT NULL, PRIMARY KEY (Benutzer)) COMMENT="UmfrageScript-Zuweisungen"';
     $sN=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", Benutzer VARCHAR(25) NOT NULL DEFAULT "", Passwort VARCHAR(32) NOT NULL DEFAULT "", eMail VARCHAR(100) NOT NULL DEFAULT ""';
     for($i=5;$i<$nNutzFelder;$i++) $sN.=', dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""'; $sN.=', PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Benutzer"';
     $sT=' (Nummer INT NOT NULL auto_increment, Datum CHAR(19) NOT NULL DEFAULT "", Status CHAR(1) NOT NULL DEFAULT "", Art CHAR(1) NOT NULL DEFAULT "", Nutzer TEXT NOT NULL, Ergebnis TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="UmfrageScript-Teilnahme"';
     if(!UMF_SQL){ //Text->SQL
      $bNeu=true;
      $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabF); $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabE); $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabZ); $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabN); $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabT);
      if($DbO->query('CREATE TABLE '.$usSqlTabF.$sF)){ //Fragen
       if($DbO->query('CREATE TABLE '.$usSqlTabE.$sE)){ //Ergebnis
        $DbO->query('INSERT IGNORE INTO '.$usSqlTabE.' VALUES("0","IP")');
        $aD=file(UMF_Pfad.UMF_Daten.UMF_Fragen); $nSaetze=count($aD);
        if($sTabSFrLeer!='1') for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); $s='"'.$a[0].'"';
         for($j=1;$j<$nAntwAnzahl+7;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
         if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabF.' VALUES('.$s.')')) $bNeu=false;
         $DbO->query('INSERT IGNORE INTO '.$usSqlTabE.' VALUES("'.$a[0].'","0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0")');
        }
        if($bNeu){ //Ergebnisse
         $aD=file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $nSaetze=count($aD);
         if(!$DbO->query('UPDATE IGNORE '.$usSqlTabE.' SET Inhalt="'.rtrim($aD[0]).'" WHERE Nummer="0"')) $bNeu=false;
         if($sTabSErLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $s=rtrim($aD[$i]); $nP=strpos($s,';');
          $DbO->query('UPDATE IGNORE '.$usSqlTabE.' SET Inhalt="'.substr($s,$nP+1).'" WHERE Nummer="'.substr($s,0,$nP).'"');
         }
        }
        if($DbO->query('CREATE TABLE '.$usSqlTabZ.$sZ)){ //Zuweisungsdaten
         if($sTabSZwLeer!='1'){$aD=file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); array_shift($aD);}else $aD=array();
         foreach($aD as $s) if($nP=strpos($s,';')){
          if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabZ.' VALUES("'.substr($s,0,$nP).'","'.rtrim(substr($s,$nP+1)).'")')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Zuweisungstabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabZ.'</i> konnte nicht angelegt werden!</p>';}
        if($DbO->query('CREATE TABLE '.$usSqlTabN.$sN)){ //Nutzerdaten
         $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD);
         if($sTabSNuLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i]));
          $s='"'.$a[0].'","'.$a[1].'","'.fUmfDeCode($a[2]).'","'.$a[3].'","'.fUmfDeCode($a[4]).'"';
          for($j=5;$j<$nNutzFelder;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('`,',';',$a[$j])):'').'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabN.' VALUES('.$s.')')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Benutzertabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabN.'</i> konnte nicht angelegt werden!</p>';}
        if($DbO->query('CREATE TABLE '.$usSqlTabT.$sT)){ //Teilnahmedaten
         if($sTabSTnLeer!='1'){$aD=file(UMF_Pfad.UMF_Daten.UMF_Teilnahme); array_shift($aD);}else $aD=array();
         foreach($aD as $s) if($a=explode(';',rtrim($s),5)){
          $b=explode(',',$a[3]); if($a[2]=='N') $b[0]=fUmfEnCode($b[0]); $t=''; foreach($b as $u) $t.=','.fUmfDeCode($u);
          $s=$a[0].'","'.$a[1].'","'.$a[2].'","'.substr($t,1).'","'.$a[4];
          if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabT.' (Datum,Status,Art,Nutzer,Ergebnis) VALUES("'.$s.'")')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Teilnahmetabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabT.'</i> konnte nicht angelegt werden!</p>';}
       }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Ergebnistabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabE.'</i> konnte nicht angelegt werden!</p>';}
       if($bNeu){
        $sMeld.='<p class="admErfo">Die Fragen wurden in die MySQL-Tabelle <i>'.$usSqlTabF.'</i> übernommen.</p>';
       }else $sMeld.='<p class="admFehl">Nicht alle Fragen, Ergebnisse oder Benutzer konnten in die MySQL-Tabelle <i>'.$usSqlTabF.'</i>, <i>'.$usSqlTabN.'</i>, <i>'.$usSqlTabE.'</i> übernommen werden!</p>';
      }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Fragentabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabF.'</i> konnte nicht angelegt werden!</p>';}
     }else{ //SQL->SQL
      $bSqlNeu=($usSqlHost!=UMF_SqlHost||$usSqlUser!=UMF_SqlUser||$usSqlPass!=UMF_SqlPass||$usSqlDaBa!=UMF_SqlDaBa);
      if($usSqlTabF!=UMF_SqlTabF||$usSqlTabE!=UMF_SqlTabE||$usSqlTabN!=UMF_SqlTabN||$bSqlNeu){
       $aD=array(); $aE=array(); $aZ=array(); $aN=array(); $aT=array();
       if(!$bDbConst&&$DbO){$DbO->close(); $DbO=NULL;}
       if($bDbConst||($DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa))){ //alte SQL-Verbindung
        if($bDbConst||!mysqli_connect_errno()){
         if(ADU_SqlCharSet) $DbO->set_charset(ADU_SqlCharSet);
         if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabF.' ORDER BY Nummer')){ //Fragen
          if($sTabSFrLeer!='1') while($a=$rR->fetch_row()){
           $s='"'.$a[0].'"'; for($i=1;$i<$nAntwAnzahl+7;$i++) $s.=',"'.(isset($a[$i])?str_replace('"','\"',$a[$i]):'').'"'; $aD[]=$s;
          }
          $rR->close(); $bNeu=true;
          if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabE.' ORDER BY Nummer')){ //Ergebnisse
           if($sTabSErLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'","'.str_replace('"','\"',$a[1]).'"'; $aE[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Ergebnistabelle <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'.'.UMF_SqlTabE.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabZ.' ORDER BY Benutzer')){ //Zuweisungen
           if($sTabSZwLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'","'.str_replace('"','\"',$a[1]).'"'; $aZ[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Zuweisungsstabelle <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'.'.UMF_SqlTabE.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' ORDER BY Nummer')){ //Nutzer
           if($sTabSNuLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'"'; for($i=1;$i<$nNutzFelder;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aN[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Benutzertabelle <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'.'.UMF_SqlTabN.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabT.' ORDER BY Nummer')){ //Teilnahme
           if($sTabSTnLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'","'.$a[1].'","'.$a[2].'","'.$a[3].'","'.str_replace('"','\"',$a[4]).'","'.str_replace('"','\"',$a[5]).'"'; $aT[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Teilnahmetabelle <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'.'.UMF_SqlTabT.'</i>!</p>';}
         }else $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Fragentabelle <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'.'.UMF_SqlTabF.'</i>!</p>';
        }else $sMeld.='<p class="admFehl">Kein Zugriff auf die bisherige Datenbank <i>'.UMF_SqlHost.':'.UMF_SqlDaBa.'</i>!</p>';
        if(!$bDbConst){$DbO->close(); $DbO=NULL;}
       }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung zur bisherigen MySQL-Datenquelle!</p>';
       if(!$bDbConst) $DbO=@new mysqli($usSqlHost,$usSqlUser,$usSqlPass,$usSqlDaBa);
       if($bDbConst||!mysqli_connect_errno()){
        if(ADU_SqlCharSet) $DbO->set_charset(ADU_SqlCharSet);
        if($bNeu&&($usSqlTabF!=UMF_SqlTabF||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabF); $nSaetze=count($aD); //Fragen neu
         if($DbO->query('CREATE TABLE '.$usSqlTabF.$sF)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabF.' VALUES('.$aD[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Fragen wurden in die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabF.'</i> übernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Fragen konnten in die MySQL-Tabelle <i>'.$usSqlTabF.'</i> übernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabF.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($usSqlTabE!=UMF_SqlTabE||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabE); $nSaetze=count($aE); //Ergebnisse neu
         if($DbO->query('CREATE TABLE '.$usSqlTabE.$sE)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabE.' VALUES('.$aE[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Ergebnisse wurden in die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabE.'</i> übernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Ergebnisse konnten in die MySQL-Tabelle <i>'.$usSqlTabE.'</i> übernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabE.'</i> konnte nicht angelegt werden!</p>';}
        }

        if($bNeu&&($usSqlTabZ!=UMF_SqlTabZ||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabZ); $nSaetze=count($aZ); //Zuweisungen neu
         if($DbO->query('CREATE TABLE '.$usSqlTabZ.$sZ)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabZ.' VALUES('.$aZ[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Zuweisungen wurden in die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabZ.'</i> übernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Zuweisungen konnten in die MySQL-Tabelle <i>'.$usSqlTabZ.'</i> übernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabZ.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($usSqlTabN!=UMF_SqlTabN||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabN); $nSaetze=count($aN); //Nutzer neu
         if($DbO->query('CREATE TABLE '.$usSqlTabN.$sN)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabN.' VALUES('.$aN[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Benutzer wurden in die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabN.'</i> übernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Benutzer konnten in die MySQL-Tabelle <i>'.$usSqlTabN.'</i> übernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabN.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($usSqlTabT!=UMF_SqlTabT||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$usSqlTabT); $nSaetze=count($aT); //Teilnahme neu
         if($DbO->query('CREATE TABLE '.$usSqlTabT.$sT)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$usSqlTabT.' VALUES('.$aT[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Teilnahme wurden in die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabT.'</i> übernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Teilnehmer konnten in die MySQL-Tabelle <i>'.$usSqlTabT.'</i> übernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$usSqlHost.':'.$usSqlDaBa.'.'.$usSqlTabT.'</i> konnte nicht angelegt werden!</p>';}
        }
       }else $sMeld.='<p class="admFehl">Kein Zugriff auf die neue Datenbank <i>'.$usSqlHost.':'.$usSqlDaBa.'</i>!</p>';
      }//keine Aenderung
     }//SQL->SQL
    }//bToDo
    if($bNeu){
     fSetzUmfWert(true,'SQL','');
     fSetzUmfWert($usSqlHost,'SqlHost',"'"); fSetzUmfWert($usSqlDaBa,'SqlDaBa',"'"); fSetzUmfWert($usSqlUser,'SqlUser',"'"); fSetzUmfWert($usSqlPass,'SqlPass',"'");
     fSetzUmfWert($usSqlTabF,'SqlTabF',"'"); fSetzUmfWert($usSqlTabE,'SqlTabE',"'"); fSetzUmfWert($usSqlTabZ,'SqlTabZ',"'"); fSetzUmfWert($usSqlTabN,'SqlTabN',"'"); fSetzUmfWert($usSqlTabT,'SqlTabT',"'");
    }
   }else $sMeld.='<p class="admFehl">Kein Zugriff auf die angegebene Datenbank <i>'.$usSqlHost.':'.$usSqlDaBa.'</i>!</p>';
  }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung mit den angegebenen neuen Zugangsdaten!</p>';
 }
 if(empty($usBilder)) $usBilder='bilder/';
 if(substr($usBilder,0,1)=='/') $usBilder=substr($usBilder,1); if(substr($usBilder,-1,1)!='/') $usBilder.='/';
 if($usBilder!=$usDaten&&$usBilder!=UMF_CaptchaPfad&&$usBilder!='grafik/'){
  if(is_writable(UMF_Pfad.substr($usBilder,0,-1))){
   if(fSetzUmfWert($usBilder,'Bilder',"'")){$bNeu=true; $bBldNeu=true;}
  }else $sMeld.='<p class="admFehl">Der vorgesehene Bilder-Ordner <i>'.substr($usBilder,0,-1).'</i> ist nicht beschreibbar!</p>';
 }else $sMeld.='<p class="admFehl">Der vorgesehene Name <i>'.substr($usBilder,0,-1).'</i> für den Bilder-Ordner ist ungültig!</p>';

 if($bNeu){//Speichern
  if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
  }else $sMeld.='<p class="admFehl">In die Datei <i>umfWerte'.$sKonf.'.php</i> im Programmverzeichnis durfte nicht geschrieben werden!</p>';
 }
 //------
  }//while
  if($sErfo) $sMeld.='<p class="admErfo">Die Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
  else $sMeld.='<p class="admMeld">Die Einstellungen zur Datenbasis bleiben unverändert.</p>';
 }else{$sMeld.='<p class="admFehl">Wollen Sie die Änderung wirklich für <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
}//POST

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen zur Datenbasis des Umfrage-Scripts.</p>';
echo $sMeld.NL;
?>

<form action="konfDaten.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Umfrage-Script speichert die Fragedaten in tabellarischer Form
auf dem Webserver, um daraus bei jeder Anforderung dynamisch eine Ausgabeseite zu generieren.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenbasis</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td width="130" valign="top"><input type="radio" class="admRadio" name="Sql" value="0"<?php if(!$usSQL) echo ' checked="checked"';?> /> Textdatei</td>
    <td style="padding-bottom:8px;">Standardmäßig werden zum Speichern einfache Textdateien verwendet.
Diese Methode ist schnell und ressourcenschonend.
Allerdings muss das Umfrage-Script dazu die Berechtigung besitzen, in eine solche Fragedatei, Ergebnisdatei bzw. Benutzerdatei
schreiben zu dürfen. Eine solche Schreibberechtigung stellt auf einigen wenigen ungeschickt konfigurierten Servern
unter extrem seltenen Bedingungen ein gewisses Sicherheitsrisiko dar.</td>
   </tr>
   <tr>
    <td width="130" valign="top"><input type="radio" class="admRadio" name="Sql" value="1"<?php if($usSQL) echo ' checked="checked"';?> /> MySQL-Tabelle</td>
    <td>Abweichend davon können die Daten auch in Tabellen einer MySQL-Datenbank gepeichert werden.
Diese Methode ist wesentlich ressourcenverbrauchender solange die Fragedatei nur wenige Hundert Fragen enthält.
In Fällen, da mehrere Tausend Fragen in der Datenbasis eingetragen sind oder sehr viele Benutzer angemeldet sind
kann die MySQL-Datenquelle hingegen Geschwindigkeits- oder Sicherheitsvorteile bringen.</td>
   </tr>
  </table></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini">
  <u>Hinweis</u>: Wenn Sie die Datenbasis umschalten werden die Fragen und Ergebnisse
  aus der momentanen Datenquelle auf den neuen Datenspeicher umgeschrieben.
  Etwaig vorhandene ältere Datenspeicher mit selbem Namen aus früheren Umschaltungen werden überschrieben.
  Gleiches gilt für die Benutzerdaten, falls die Benutzerverwaltung aktiv ist.
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in <i>Textdateien</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherordner</td>
 <td><input type="text" name="Daten" value="<?php echo(substr($usDaten,-1,1)=='/'?substr($usDaten,0,-1):$usDaten)?>" style="width:250px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>daten</i>
 <div class="admMini">Unterordner, relativ zum Hauptordner des Umfrage-Scripts. Der Ordner muss bereits existieren. <a href="<?php echo ADU_Hilfe ?>LiesMich.htm#1.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini"><u>Hinweis</u>: Um unbefugte Einblicke in den Datenspeicherordner zu verhindern können Sie diesen Unterordner mit einem serverseitigen .htaccess-Passwortschutz versehen, so wie Sie es hoffentlich bereits für den Administrator-Ordner getan haben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragendatei</td>
 <td><input type="text" name="Fragen" value="<?php echo $usFragen?>" style="width:150px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>fragen.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabFraLeer<?php if($sTabFraLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnisdatei</td>
 <td><input type="text" name="Ergebnis" value="<?php echo $usErgebnis?>" style="width:150px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>ergebnis.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabErgLeer<?php if($sTabErgLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zuweisungsdatei</td>
 <td><input type="text" name="Zuweisung" value="<?php echo $usZuweisung?>" style="width:150px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>zuweisungen.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabZuwLeer<?php if($sTabZuwLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdatei</td>
 <td><input type="text" name="Nutzer" value="<?php echo $usNutzer?>" style="width:150px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>Wählen Sie einen nicht zu erratenden Namen!</i>
 <div><input class="admCheck" type="checkbox" name="TabNtzLeer<?php if($sTabNtzLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Wenn Ihr Umfrage-Script mit Benutzerverwaltung arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnahmedatei</td>
 <td><input type="text" name="Teilnahme" value="<?php echo $usTeilnahme?>" style="width:150px;<?php if($usSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>teilnahme.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabTlnLeer<?php if($sTabTlnLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Wenn Ihr Umfrage-Script mit Benutzerverwaltung arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini"><u>Warnung</u>: Im Datenordner vorhandene Dateien gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in einer <i>MySQL-Datenbank</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Host</td>
 <td><input type="text" name="SqlHost" value="<?php echo $usSqlHost?>" style="width:250px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /> meist: <i>localhost</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Datenbank</td>
 <td><input type="text" name="SqlDaBa" value="<?php echo $usSqlDaBa?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /> (die Datenbank muss unter diesem Namen bereits vorhanden sein)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Benutzer</td>
 <td><input type="text" name="SqlUser" value="<?php echo $usSqlUser?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Passwort</td>
 <td><input type="password" name="SqlPass" value="<?php echo $usSqlPass?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Tabellen</td>
 <td><table border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td><input type="text" name="SqlTabF" value="<?php echo $usSqlTabF?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>umf_fragen</i> für die Fragen</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSFrLeer<?php if($sTabSFrLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabE" value="<?php echo $usSqlTabE?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>umf_ergebnis</i> für die Ergebnisse</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSErLeer<?php if($sTabSErLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabZ" value="<?php echo $usSqlTabZ?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>umf_zuweisung</i> für die Zuweisungen</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSZwLeer<?php if($sTabSZwLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabN" value="<?php echo $usSqlTabN?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>umf_nutzer</i> für die Benutzer</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSNuLeer<?php if($sTabSNuLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabT" value="<?php echo $usSqlTabT?>" style="width:120px;<?php if(!$usSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>umf_teilnahme</i> für die Teilnahme</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSTnLeer<?php if($sTabSTnLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr>
 </table></td>
</tr>
<tr class="admTabl"><td class="admMini" colspan="2"><u>Warnung</u>: In der Datenbank vorhandene Tabellen gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihr Umfrage-Script mit Bildern arbeitet kann der Speicherort für Bilder verlegt werden. (<span class="admMini">Angabe relativ zum Programmordner</span>)</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilder-Ordner</td>
 <td><input type="text" name="Bilder" value="<?php echo(substr($usBilder,-1,1)=='/'?substr($usBilder,0,-1):$usBilder)?>" style="width:250px;" /> Empfehlung: <i>bilder</i> &nbsp; <span class="admMini">(der Ordner muss bereits existieren)</span></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

if($bBldNeu){
 if($f=opendir(UMF_Pfad.UMF_Bilder)){
  $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.') $a[]=$s; closedir($f);
  foreach($a as $s) @copy(UMF_Pfad.UMF_Bilder.$s,UMF_Pfad.$usBilder.$s);
 }
}
?>