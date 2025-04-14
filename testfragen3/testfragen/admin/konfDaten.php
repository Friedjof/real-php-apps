<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('Datenbasis einstellen','','KDb');

$bBldNeu=false;
if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $fsSQL=FRA_SQL; $fsDaten=FRA_Daten; $fsBilder=FRA_Bilder;
 $fsSqlHost=FRA_SqlHost; $fsSqlDaBa=FRA_SqlDaBa; $fsSqlUser=FRA_SqlUser; $fsSqlPass=FRA_SqlPass;
 $fsSqlTabF=FRA_SqlTabF; $fsSqlTabE=FRA_SqlTabE; $fsSqlTabN=FRA_SqlTabN; $fsSqlTabT=FRA_SqlTabT; $fsSqlTabZ=FRA_SqlTabZ;
 $fsFragen=FRA_Fragen; $fsFolgen=FRA_Folgen; $fsErgebnis=FRA_Ergebnis; $fsNutzer=FRA_Nutzer; $fsZuweisung=FRA_Zuweisung;
 $sTabFraLeer=''; $sTabNtzLeer=''; $sTabErgLeer=''; $sTabFolLeer=''; $sTabZuwLeer='';
 $sTabSFrLeer=''; $sTabSNuLeer=''; $sTabSErLeer=''; $sTabSFoLeer=''; $sTabSZuLeer='';
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $fsSQL=(txtVar('Sql')!='1'?false:true); $fsDaten=txtVar('Daten'); $fsBilder=txtVar('Bilder');
 $fsSqlHost=txtVar('SqlHost'); $fsSqlDaBa=txtVar('SqlDaBa'); $fsSqlUser=txtVar('SqlUser'); $fsSqlPass=txtVar('SqlPass');
 $fsSqlTabF=txtVar('SqlTabF'); $fsSqlTabE=txtVar('SqlTabE'); $fsSqlTabN=txtVar('SqlTabN'); $fsSqlTabT=txtVar('SqlTabT'); $fsSqlTabZ=txtVar('SqlTabZ');
 $fsFragen=txtVar('Fragen'); $fsErgebnis=txtVar('Ergebnis'); $fsNutzer=txtVar('Nutzer'); $fsZuweisung=txtVar('Zuweisung'); $fsFolgen=txtVar('Folgen');
 $fra_NutzerFelder=explode(';',FRA_NutzerFelder); $nNutzFelder=count($fra_NutzerFelder);
 $sTabFraLeer=txtVar('TabFraLeer'); $sTabNtzLeer=txtVar('TabNtzLeer'); $sTabErgLeer=txtVar('TabErgLeer'); $sTabFolLeer=txtVar('TabFolLeer'); $sTabZuwLeer=txtVar('TabZuwLeer');
 $sTabSFrLeer=txtVar('TabSFrLeer'); $sTabSNuLeer=txtVar('TabSNuLeer'); $sTabSErLeer=txtVar('TabSErLeer'); $sTabSFoLeer=txtVar('TabSFoLeer'); $sTabSZuLeer=txtVar('TabSZuLeer');
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $bToDo=true; $bNeu=false;
 if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
  foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
 //------
 $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php'))));
 if(!$fsSQL){ //->Text
  if(!empty($fsDaten)&&!empty($fsFragen)&&!empty($fsErgebnis)){
   if(substr($fsDaten,0,1)=='/') $fsDaten=substr($fsDaten,1); if(substr($fsDaten,-1,1)!='/') $fsDaten.='/';
   if($fsFragen!=$fsErgebnis&&$fsFragen!=$fsNutzer&&$fsFragen!=$fsFolgen&&$fsErgebnis!=$fsNutzer&&$fsErgebnis!=$fsFolgen&&$fsNutzer!=$fsFolgen&&$fsZuweisung!=$fsFolgen){
    if($bToDo){
     $bToDo=false; $bOK=true;
     if(!FRA_SQL){ //Text->Text
      if($fsFragen!=FRA_Fragen||$fsDaten!=FRA_Daten){ //Fragendatei
       if($f=fopen(FRA_Pfad.$fsDaten.$fsFragen,'w')){
        $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen);
        if($sTabFraLeer!='1') $s=str_replace("\r",'',implode('',$aD)); else $s=$aD[0];
        fwrite($f,trim($s).NL); fclose($f); $bNeu=true;
        $sMeld.='<p class="admErfo">Die neue Fragendatei <i>'.$fsDaten.$fsFragen.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Fragendatei <i>'.$fsDaten.$fsFragen.'</i>.</p>';}
      }
      if(($fsErgebnis!=FRA_Ergebnis||$fsDaten!=FRA_Daten)&&$bOK){ //Ergebnisdatei
       $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nErgebnisZahl=($sTabErgLeer!='1'?count($aD):1);
       if($f=fopen(FRA_Pfad.$fsDaten.$fsErgebnis,'w')){
        $aD[0]='Eintrag;Datum;Dauer;Anzahl;Richtige;Falsche;Punkte;Versuche;Auslassungen;Bewertung;Antwortkette;Verlaufskette;Testfolge;Benutzer'.NL;
        for($i=0;$i<$nErgebnisZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Ergebnisdatei <i>'.$fsDaten.$fsErgebnis.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Ergebnisdatei <i>'.$fsDaten.$fsErgebnis.'</i>.</p>';}
      }
      if(($fsNutzer!=FRA_Nutzer||$fsDaten!=FRA_Daten)&&$bOK){ //Benutzerdatei
       $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nNutzerZahl=($sTabNtzLeer!='1'?count($aD):1);
       if($f=fopen(FRA_Pfad.$fsDaten.$fsNutzer,'w')){
        if(substr($aD[0],0,7)!='Nummer_'){
         $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5));
         $s='Nummer_'.$nMx; for($i=1;$i<$nNutzFelder;$i++) $s.=';'.$fra_NutzerFelder[$i]; $aD[0]=$s.NL;
        }
        for($i=0;$i<$nNutzerZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Benutzerdatei <i>'.$fsDaten.$fsNutzer.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Benutzerdatei <i>'.$fsDaten.$fsNutzer.'</i>.</p>';}
      }
      if(($fsFolgen!=FRA_Folgen||$fsDaten!=FRA_Daten)&&$bOK){ //Testfolgen
       $aD=file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nFolgenZahl=($sTabFolLeer!='1'?count($aD):0);
       if($f=fopen(FRA_Pfad.$fsDaten.$fsFolgen,'w')){fwrite($f,'Folge;Fragen;ProSeite;GAktiv;BAktiv;Code;VorAuswertung;NachAuswertung'.NL);
        for($i=1;$i<$nFolgenZahl;$i++) if($s=rtrim($aD[$i])){
         if(substr_count($s,';')<7){
          $a=explode(';',$s); $s=$a[0].';'.$a[1].';'.(isset($a[2])?$a[2]:'1').';'.(isset($a[3])?$a[3]:'1').';'.(isset($a[4])?$a[4]:'1').';'.(isset($a[5])?$a[5]:rand(1000,9999)).';'.(isset($a[6])?$a[6]:'').';'.(isset($a[7])?$a[7]:'');
         }
         fwrite($f,$s.NL);
        }
        fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Folgendatei <i>'.$fsDaten.$fsFolgen.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Folgendatei <i>'.$fsDaten.$fsFolgen.'</i>.</p>';}
      }
      if(($fsZuweisung!=FRA_Zuweisung||$fsDaten!=FRA_Daten)&&$bOK){ //Zuweisungen
       $aD=file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nZuweisZahl=($sTabZuwLeer!='1'?count($aD):0);
       if($f=fopen(FRA_Pfad.$fsDaten.$fsZuweisung,'w')){
        fwrite($f,'Benutzer;zugewiesene_Tests'.NL); for($i=1;$i<$nZuweisZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); $bNeu=true;
        if(!strpos($sMeld,'Die neue')) $sMeld.='<p class="admErfo">Die neue Zuweisungsdatei <i>'.$fsDaten.$fsZuweisung.'</i> wurde gespeichert.</p>';
       }else{$bOK=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der neuen Zuweisungsdatei <i>'.$fsDaten.$fsZuweisung.'</i>.</p>';}
      }
      $bNeu=$bNeu&&$bOK;
     }else{//SQL->Text
      if($DbO){
       if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
        $s=''; $sKopf='Nummer;aktiv;versteckt;Kategorie;Frage;Loesung;Punkte;Bild;Antwort1;Antwort2;Antwort3;Antwort4;Antwort5;Antwort6;Antwort7;Antwort8;Antwort9;Anmerkung;Anmerkung2';
        if($sTabFraLeer!='1') while($a=$rR->fetch_row()){
         $s.=NL.$a[0]; for($i=1;$i<19;$i++) $s.=';'.(isset($a[$i])?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i]))):'');
        }
        $rR->close();
        if($f=fopen(FRA_Pfad.$fsDaten.$fsFragen,'w')){
         fwrite($f,$sKopf.rtrim($s).NL); fclose($f); $bNeu=true;
         $sMeld.='<p class="admErfo">Die Fragendatei <i>'.$fsDaten.$fsFragen.'</i> wurde gespeichert.</p>';
         if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' ORDER BY Eintrag')){ //Ergebnisdatei
          $s='Eintrag;Datum;Dauer;Anzahl;Richtige;Falsche;Punkte;Versuche;Auslassungen;Bewertung;Antwortkette;Verlaufskette;Testfolge;Benutzer';
          if($sTabErgLeer!='1') while($a=$rR->fetch_row()){
           $sD=$a[1]; $sD=date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)));
           $s.=NL.$a[0].';'.$sD; for($i=2;$i<14;$i++) $s.=';'.str_replace('\"','"',$a[$i]);
          }$rR->close();
          if($f=fopen(FRA_Pfad.$fsDaten.$fsErgebnis,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Ergebnisdatei <i>'.$fsDaten.$fsErgebnis.'</i>.</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Ergebinstabelle <i>'.FRA_SqlTabE.'</i>!</p>';}
         if($rR=$DbO->query('SELECT MAX(Nummer) FROM '.FRA_SqlTabN)){$a=$rR->fetch_row(); $rR->close();} //Nutzerdatei
         if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' ORDER BY Nummer')){
          $s='Nummer_'.(int)$a[0]; for($i=1;$i<$nNutzFelder;$i++) $s.=';'.$fra_NutzerFelder[$i];
          if($sTabNtzLeer!='1') while($a=$rR->fetch_row()){
           $s.=NL.$a[0].';'.$a[1].';'.fFraEnCode($a[2]).';'.$a[3].';'.fFraEnCode($a[4]);
           for($i=5;$i<$nNutzFelder;$i++) $s.=';'.str_replace(';','`,',str_replace('\"','"',$a[$i]));
          }$rR->close();
          if($f=fopen(FRA_Pfad.$fsDaten.$fsNutzer,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Benutzerdatei <i>'.$fsDaten.$fsNutzer.'</i>.</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Benutzertabelle <i>'.FRA_SqlTabN.'</i>!</p>';}
         if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabT.' ORDER BY Folge')){$s='Folge;Fragen;ProSeite;GAktiv;BAktiv;Code;VorAuswertung;NachAuswertung'.NL; //Testfolgen
          if($sTabFolLeer!='1') while($a=$rR->fetch_row()) if(!empty($a[1])) $s.=$a[0].';'.$a[1].';'.(isset($a[2])?$a[2]:'1').';'.(isset($a[3])?$a[3]:'1').';'.(isset($a[4])?$a[4]:'1').';'.(isset($a[5])?$a[5]:rand(1000,9999)).';'.(isset($a[6])?str_replace("\n",'\n ',str_replace("\r",'',$a[6])):'').';'.(isset($a[7])?str_replace("\n",'\n ',str_replace("\r",'',$a[7])):'').NL; $rR->close();
          if($f=fopen(FRA_Pfad.$fsDaten.$fsFolgen,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Folgendatei <i>'.$fsDaten.$fsFolgen.'</i>.</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Folgentabelle <i>'.FRA_SqlTabT.'</i>!</p>';}
         if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabZ.' ORDER BY Nummer')){$s='Benutzer;zugewiesene_Tests'.NL; //Zuweisungen
          if($sTabZuwLeer!='1') while($a=$rR->fetch_row()) $s.=$a[0].';'.(isset($a[1])?$a[1]:'').NL; $rR->close();
          if($f=fopen(FRA_Pfad.$fsDaten.$fsZuweisung,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
          else{$bNeu=false; $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Zuweisungsdatei <i>'.$fsDaten.$fsZuweisung.'</i>.</p>';}
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Zuweisungstabelle <i>'.FRA_SqlTabZ.'</i>!</p>';}
        }else $sMeld.='<p class="admFehl">Kein Zugriffsrecht beim Schreiben der Fragendatei <i>'.$fsDaten.$fsFragen.'</i>!</p>';
       }else $sMeld.='<p class="admFehl">Abfragefehler in der MySQL-Fragentabelle <i>'.FRA_SqlTabF.'</i>!</p>';
      }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung mit den bisherigen Zugangsdaten!</p>';
     }//SQL->Text
    }//bToDo
    if($bNeu){
     fSetzFraWert(false,'SQL',''); fSetzFraWert($fsDaten,'Daten',"'");
     fSetzFraWert($fsFragen,'Fragen',"'"); fSetzFraWert($fsNutzer,'Nutzer',"'"); fSetzFraWert($fsZuweisung,'Zuweisung',"'");
     fSetzFraWert($fsErgebnis,'Ergebnis',"'"); fSetzFraWert($fsFolgen,'Folgen',"'");
    }
   }else $sMeld.='Die Dateinamen der 5 Dateien <i>'.$fsFragen.'</i>, <i>'.$fsErgebnis.'</i>, <i>'.$fsNutzer.'</i>, <i>'.$fsFolgen.'</i>, <i>'.$fsZuweisung.'</i> m�ssen sich unterscheiden!';
  }else $sMeld.='Speicherpfad und Dateiname der Fragendatei d�rfen nicht leer sein!';
 }else{ //->SQL
  $bDbConst=($DbO&&$fsSqlHost==FRA_SqlHost&&$fsSqlDaBa==FRA_SqlDaBa&&$fsSqlUser==FRA_SqlUser&&$fsSqlPass==FRA_SqlPass);
  if($bPwNeu=($fsSqlHost==FRA_SqlHost&&$fsSqlUser==FRA_SqlUser&&$fsSqlPass!=FRA_SqlPass&&$fsSqlDaBa==FRA_SqlDaBa)) $bToDo=false;
  if(!$bDbConst&&$DbO){@$DbO->close(); $DbO=NULL;}
  if($bDbConst||($DbO=@new mysqli($fsSqlHost,$fsSqlUser,$fsSqlPass,$fsSqlDaBa))){ //ZielVerbindung
   if($bDbConst||!mysqli_connect_errno()){
    if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
    if($bToDo){
     $bToDo=false;
     $sF=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", versteckt CHAR(1) NOT NULL DEFAULT "", Kategorie VARCHAR(128) NOT NULL DEFAULT "", Frage TEXT NOT NULL, Loesung VARCHAR(16) NOT NULL DEFAULT "", Punkte INT(3) NOT NULL DEFAULT "0", Bild VARCHAR(128) NOT NULL DEFAULT ""';
     for($i=1;$i<=9;$i++) $sF.=', Antwort'.$i.' TEXT NOT NULL'; $sF.=', Anmerkung TEXT NOT NULL, Anmerkung2 TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="FrageScript-Fragen"';
     $sN=' (Nummer INT NOT NULL auto_increment, aktiv CHAR(1) NOT NULL DEFAULT "", Benutzer VARCHAR(25) NOT NULL DEFAULT "", Passwort VARCHAR(32) NOT NULL DEFAULT "", eMail VARCHAR(100) NOT NULL DEFAULT ""';
     for($i=5;$i<$nNutzFelder;$i++) $sN.=', dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""'; $sN.=', PRIMARY KEY (Nummer)) COMMENT="FrageScript-Benutzer"';
     $sE=' (Eintrag INT NOT NULL auto_increment, Datum VARCHAR(20) NOT NULL DEFAULT "", Dauer CHAR(8) NOT NULL DEFAULT "", Anzahl INT(3) NOT NULL DEFAULT "0", Richtige INT(3) NOT NULL DEFAULT "0", Falsche INT(3) NOT NULL DEFAULT "0", Punkte INT(3) NOT NULL DEFAULT "0", Versuche INT(3) NOT NULL DEFAULT "0", Auslassungen INT(3) NOT NULL DEFAULT "0", Bewertung TEXT NOT NULL, Antwortkette TEXT NOT NULL, Verlaufskette TEXT NOT NULL, Testfolge VARCHAR(63) NOT NULL DEFAULT "", Benutzer TEXT NOT NULL, PRIMARY KEY (Eintrag)) COMMENT="FrageScript-Ergebnis"';
     $sT=' (Folge VARCHAR(50) NOT NULL DEFAULT "", Fragen TEXT NOT NULL, ProSeite VARCHAR(8) NOT NULL DEFAULT "", GAktiv CHAR(1) NOT NULL DEFAULT "", BAktiv CHAR(1) NOT NULL DEFAULT "", Code INT(4) NOT NULL DEFAULT "0", VorAw TEXT NOT NULL, NachAw TEXT NOT NULL, PRIMARY KEY (Folge)) COMMENT="FrageScript-Testfolgen"';
     $sZ=' (Nummer INT NOT NULL auto_increment, Tests TEXT NOT NULL, PRIMARY KEY (Nummer)) COMMENT="FrageScript-Testzuweisungen"';
     if(!FRA_SQL){ //Text->SQL
      $bNeu=true;
      $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabF); $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabE);
      $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabN); $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabT); $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabZ);
      if($DbO->query('CREATE TABLE '.$fsSqlTabF.$sF)){
       $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
       if($sTabSFrLeer!='1') for($i=1;$i<$nSaetze;$i++){
        $a=explode(';',rtrim($aD[$i])); $s='"'.$a[0].'"';
        for($j=1;$j<19;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
        if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabF.' VALUES('.$s.')')) $bNeu=false;
       }
       if($bNeu){ //Ergebnisse, Nutzerdaten und Testfolgen
        if($DbO->query('CREATE TABLE '.$fsSqlTabE.$sE)){ //Ergebnisse
         $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD);
         $aF=explode(':',str_replace('-',':',str_replace('.',':',str_replace(' ',':',strtolower(FRA_Datumsformat))))); $aF=array_flip($aF);
         if($sTabSErLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i]),14);
          $aZ=explode(':',str_replace('-',':',str_replace('.',':',str_replace(' ',':',$a[1])))); $s=(int)$aZ[$aF['y']];
          if($s<2000) $s+=2000; $s.='-'.(sprintf('%02d-%02d %02d:%02d',$aZ[$aF['m']],$aZ[$aF['d']],$aZ[$aF['h']],$aZ[$aF['i']]));
          if(isset($aF['s'])&&$aF['s']) $s.=':'.sprintf('%02d',$aZ[$aF['s']]); $s='"'.$a[0].'","'.$s.'"'; for($j=2;$j<14;$j++) $s.=',"'.$a[$j].'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabE.' VALUES('.$s.')')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Ergebnistabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabE.'</i> konnte nicht angelegt werden!</p>';}
        if($DbO->query('CREATE TABLE '.$fsSqlTabN.$sN)){ //Nutzerdaten
         $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD);
         if($sTabSNuLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i]));
          $s='"'.$a[0].'","'.$a[1].'","'.fFraDeCode($a[2]).'","'.$a[3].'","'.fFraDeCode($a[4]).'"';
          for($j=5;$j<$nNutzFelder;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('`,',';',$a[$j])):'').'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabN.' VALUES('.$s.')')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Benutzertabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabN.'</i> konnte nicht angelegt werden!</p>';}
        if($DbO->query('CREATE TABLE '.$fsSqlTabT.$sT)){ //Testfolgen
         $aD=file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nSaetze=count($aD);
         if($sTabSFoLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $s=rtrim($aD[$i]); $a=explode(';',$s);
          $s=$a[0].';'.$a[1].';'.(isset($a[2])?$a[2]:'1').';'.(isset($a[3])?$a[3]:'1').';'.(isset($a[4])?$a[4]:'1').';'.(isset($a[5])?$a[5]:rand(1000,9999)).';'.(isset($a[6])?str_replace('\n ',"\r\n",$a[6]):'').';'.(isset($a[7])?str_replace('\n ',"\r\n",$a[7]):'');
          if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabT.' VALUES("'.str_replace(';','","',$s).'")')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Folgentabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabT.'</i> konnte nicht angelegt werden!</p>';}
        if($DbO->query('CREATE TABLE '.$fsSqlTabZ.$sZ)){ //Zuweisungen
         $aD=file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nSaetze=count($aD);
         if($sTabSZuLeer!='1') for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i]),2);
          if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabZ.' VALUES("'.$a[0].'","'.$a[1].'")')) $bNeu=false;
         }
        }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Zuweisungstabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabZ.'</i> konnte nicht angelegt werden!</p>';}
       }
       if($bNeu){
        $sMeld.='<p class="admErfo">Die Fragen wurden in die MySQL-Tabelle <i>'.$fsSqlTabF.'</i> �bernommen.</p>';
       }else $sMeld.='<p class="admFehl">Nicht alle Fragen, Ergebnisse, Benutzer oder Testfolgen konnten in die MySQL-Tabelle <i>'.$fsSqlTabF.'</i>, <i>'.$fsSqlTabN.'</i>, <i>'.$fsSqlTabE.'</i>, <i>'.$fsSqlTabT.'</i>, <i>'.$fsSqlTabZ.'</i> �bernommen werden!</p>';
      }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Fragentabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabF.'</i> konnte nicht angelegt werden!</p>';}
     }else{ //SQL->SQL
      $bSqlNeu=($fsSqlHost!=FRA_SqlHost||$fsSqlUser!=FRA_SqlUser||$fsSqlPass!=FRA_SqlPass||$fsSqlDaBa!=FRA_SqlDaBa);
      if($fsSqlTabF!=FRA_SqlTabF||$fsSqlTabE!=FRA_SqlTabE||$fsSqlTabN!=FRA_SqlTabN||$fsSqlTabT!=FRA_SqlTabT||$fsSqlTabZ!=FRA_SqlTabZ||$bSqlNeu){
       $aD=array(); $aE=array(); $aT=array(); $aN=array(); $aZ=array();
       if(!$bDbConst&&$DbO){$DbO->close(); $DbO=NULL;}
       if($bDbConst||($DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa))){ //alte SQL-Verbindung
        if($bDbConst||!mysqli_connect_errno()){
         if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
         if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){ //Fragen
          if($sTabSFrLeer!='1') while($a=$rR->fetch_row()){
           $s='"'.$a[0].'"'; for($i=1;$i<19;$i++) $s.=',"'.(isset($a[$i])?str_replace('"','\"',$a[$i]):'').'"'; $aD[]=$s;
          }
          $rR->close(); $bNeu=true;
          if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' ORDER BY Eintrag')){ //Ergebnisse
           if($sTabSErLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'"'; for($i=1;$i<14;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aE[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Ergebnistabelle <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'.'.FRA_SqlTabE.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' ORDER BY Nummer')){ //Nutzer
           if($sTabSNuLeer!='1') while($a=$rR->fetch_row()){
            $s='"'.$a[0].'"'; for($i=1;$i<$nNutzFelder;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aN[]=$s;
           }$rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Benutzertabelle <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'.'.FRA_SqlTabN.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabT.' ORDER BY Folge')){ //Testfolgen
           if($sTabSFoLeer!='1') while($a=$rR->fetch_row()) if(!empty($a[1])) $aT[]='"'.$a[0].'","'.$a[1].'","'.(isset($a[2])?$a[2]:'1').'","'.(isset($a[3])?$a[3]:'1').'","'.(isset($a[4])?$a[4]:'1').'","'.(isset($a[5])?$a[5]:rand(1000,9999)).'","'.(isset($a[6])?$a[6]:'').'","'.(isset($a[7])?$a[7]:'').'"';
           $rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Folgentabelle <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'.'.FRA_SqlTabT.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabZ.' ORDER BY Nummer')){ //Zuweisungen
           if($sTabSZuLeer!='1') while($a=$rR->fetch_row()) $aZ[]='"'.$a[0].'","'.(isset($a[1])?$a[1]:'').'"';
           $rR->close();
          }else{$bNeu=false; $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Zuweisungstabelle <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'.'.FRA_SqlTabZ.'</i>!</p>';}
         }else $sMeld.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Fragentabelle <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'.'.FRA_SqlTabF.'</i>!</p>';
        }else $sMeld.='<p class="admFehl">Kein Zugriff auf die bisherige Datenbank <i>'.FRA_SqlHost.':'.FRA_SqlDaBa.'</i>!</p>';
        if(!$bDbConst){@$DbO->close(); $Dbo=NULL;}
       }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung zur bisherigen MySQL-Datenquelle!</p>';
       if(!$bDbConst) $DbO=@new mysqli($fsSqlHost,$fsSqlUser,$fsSqlPass,$fsSqlDaBa);
       if($bDbConst||!mysqli_connect_errno()){
        if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);
        if($bNeu&&($fsSqlTabF!=FRA_SqlTabF||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabF); $nSaetze=count($aD); //Fragen neu
         if($DbO->query('CREATE TABLE '.$fsSqlTabF.$sF)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabF.' VALUES('.$aD[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Fragen wurden in die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabF.'</i> �bernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Fragen konnten in die MySQL-Tabelle <i>'.$fsSqlTabF.'</i> �bernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabF.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($fsSqlTabE!=FRA_SqlTabE||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabE); $nSaetze=count($aE); //Ergebnisse neu
         if($DbO->query('CREATE TABLE '.$fsSqlTabE.$sE)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabE.' VALUES('.$aE[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Ergebnisse wurden in die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabE.'</i> �bernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Ergebnisse konnten in die MySQL-Tabelle <i>'.$fsSqlTabE.'</i> �bernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabE.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($fsSqlTabN!=FRA_SqlTabN||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabN); $nSaetze=count($aN); //Nutzer neu
         if($DbO->query('CREATE TABLE '.$fsSqlTabN.$sN)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabN.' VALUES('.$aN[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Benutzer wurden in die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabN.'</i> �bernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Benutzer konnten in die MySQL-Tabelle <i>'.$fsSqlTabN.'</i> �bernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabN.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($fsSqlTabT!=FRA_SqlTabT||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabT); $nSaetze=count($aT); //Testfolgen neu
         if($DbO->query('CREATE TABLE '.$fsSqlTabT.$sT)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabT.' VALUES('.$aT[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Folgen wurden in die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabT.'</i> �bernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Folgen konnten in die MySQL-Tabelle <i>'.$fsSqlTabT.'</i> �bernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabT.'</i> konnte nicht angelegt werden!</p>';}
        }
        if($bNeu&&($fsSqlTabZ!=FRA_SqlTabZ||$bSqlNeu)){
         $DbO->query('DROP TABLE IF EXISTS '.$fsSqlTabZ); $nSaetze=count($aZ); //Testfolgen neu
         if($DbO->query('CREATE TABLE '.$fsSqlTabZ.$sZ)){
          for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$fsSqlTabZ.' VALUES('.$aZ[$i].')')) $bNeu=false;
          if($bNeu){
           $sMeld.='<p class="admErfo">Die Zuweisungen wurden in die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabZ.'</i> �bernommen.</p>';
          }else $sMeld.='<p class="admFehl">Nicht alle Folgen konnten in die MySQL-Tabelle <i>'.$fsSqlTabZ.'</i> �bernommen werden!</p>';
         }else{$bNeu=false; $sMeld.='<p class="admFehl">Die MySQL-Tabelle <i>'.$fsSqlHost.':'.$fsSqlDaBa.'.'.$fsSqlTabZ.'</i> konnte nicht angelegt werden!</p>';}
        }
       }else $sMeld.='<p class="admFehl">Kein Zugriff auf die neue Datenbank <i>'.$fsSqlHost.':'.$fsSqlDaBa.'</i>!</p>';
      }//keine Aenderung
     }//SQL->SQL
    }//bToDo
    if($bNeu||$bPwNeu){
     fSetzFraWert(true,'SQL',''); if($bPwNeu) $sMeld.='<p class="admErfo">Das neue Passwort wurde akzeptiert.</p>';
     fSetzFraWert($fsSqlHost,'SqlHost',"'"); fSetzFraWert($fsSqlDaBa,'SqlDaBa',"'"); fSetzFraWert($fsSqlUser,'SqlUser',"'"); fSetzFraWert($fsSqlPass,'SqlPass',"'"); $bNeu=true;
     fSetzFraWert($fsSqlTabF,'SqlTabF',"'"); fSetzFraWert($fsSqlTabE,'SqlTabE',"'"); fSetzFraWert($fsSqlTabN,'SqlTabN',"'"); fSetzFraWert($fsSqlTabT,'SqlTabT',"'"); fSetzFraWert($fsSqlTabZ,'SqlTabZ',"'");
    }
   }elseif($bPwNeu) $sMeld.='<p class="admFehl">Kein Zugriff auf die Datenbank mit dem Passwort <i>'.$fsSqlPass.'</i>!</p>';
   else $sMeld.='<p class="admFehl">Kein Zugriff auf die angegebene Datenbank <i>'.$fsSqlHost.':'.$fsSqlDaBa.'</i>!</p>';
  }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung mit den angegebenen neuen Zugangsdaten!</p>';
 }
 if(empty($fsBilder)) $fsBilder='bilder/';
 if(substr($fsBilder,0,1)=='/') $fsBilder=substr($fsBilder,1); if(substr($fsBilder,-1,1)!='/') $fsBilder.='/';
 if($fsBilder!=$fsDaten&&$fsBilder!=FRA_CaptchaPfad&&$fsBilder!='grafik/'){
  if(is_writable(FRA_Pfad.substr($fsBilder,0,-1))){
   if(fSetzFraWert($fsBilder,'Bilder',"'")){$bNeu=true; $bBldNeu=true;}
  }else $sMeld.='<p class="admFehl">Der vorgesehene Bilder-Ordner <i>'.substr($fsBilder,0,-1).'</i> ist nicht beschreibbar!</p>';
 }else $sMeld.='<p class="admFehl">Der vorgesehene Name <i>'.substr($fsBilder,0,-1).'</i> f�r den Bilder-Ordner ist ung�ltig!</p>';

 if($bNeu){//Speichern
  if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
  }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> im Programmverzeichnis durfte nicht geschrieben werden!</p>';
 }
 //------
  }//while
  if($sErfo) $sMeld.='<p class="admErfo">Die Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
  else $sMeld.='<p class="admMeld">Die Einstellungen zur Datenbasis bleiben unver�ndert.</p>';
 }else{$sMeld.='<p class="admFehl">Wollen Sie die �nderung wirklich f�r <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
}//POST

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder �ndern Sie die Einstellungen zur Datenbasis des Testfragen-Scripts.</p>';
echo $sMeld.NL;
?>

<form action="konfDaten.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Testfragen-Script speichert die Fragedaten in tabellarischer Form
auf dem Webserver, um daraus bei jeder Anforderung dynamisch eine Ausgabeseite zu generieren.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datenbasis</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td width="130" valign="top"><input type="radio" class="admRadio" name="Sql" value="0"<?php if(!$fsSQL) echo ' checked="checked"';?> /> Textdatei</td>
    <td style="padding-bottom:8px;">Standardm��ig werden zum Speichern einfache Textdateien verwendet.
Diese Methode ist schnell und ressourcenschonend.
Allerdings muss das Testfragen-Script dazu die Berechtigung besitzen, in eine solche Fragedatei, Ergebnisdatei bzw. Benutzerdatei
schreiben zu d�rfen. Eine solche Schreibberechtigung stellt auf einigen wenigen ungeschickt konfigurierten Servern
unter extrem seltenen Bedingungen ein gewisses Sicherheitsrisiko dar.</td>
   </tr>
   <tr>
    <td width="130" valign="top"><input type="radio" class="admRadio" name="Sql" value="1"<?php if($fsSQL) echo ' checked="checked"';?> /> MySQL-Tabelle</td>
    <td>Abweichend davon k�nnen die Daten auch in Tabellen einer MySQL-Datenbank gepeichert werden.
Diese Methode ist wesentlich ressourcenverbrauchender solange die Fragedatei nur wenige Hundert Fragen enth�lt.
In F�llen, da mehrere Tausend Fragen in der Datenbasis eingetragen sind oder sehr viele Benutzer angemeldet sind
kann die MySQL-Datenquelle hingegen Geschwindigkeits- oder Sicherheitsvorteile bringen.</td>
   </tr>
  </table></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini">
  <u>Hinweis</u>: Wenn Sie die Datenbasis umschalten werden die Fragen und Ergebnisse
  aus der momentanen Datenquelle auf den neuen Datenspeicher umgeschrieben.
  Etwaig vorhandene �ltere Datenspeicher mit selbem Namen aus fr�heren Umschaltungen werden �berschrieben.
  Gleiches gilt f�r die Benutzerdaten, falls die Benutzerverwaltung aktiv ist.
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">F�r die etwaige Datenspeicherung in <i>Textdateien</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherordner</td>
 <td><input type="text" name="Daten" value="<?php echo(substr($fsDaten,-1,1)=='/'?substr($fsDaten,0,-1):$fsDaten)?>" style="width:250px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>daten</i>
 <div class="admMini">Unterordner, relativ zum Hauptordner des Testfragen-Scripts. Der Ordner muss bereits existieren. <a href="<?php echo ADF_Hilfe ?>LiesMich.htm#1.1" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini"><u>Hinweis</u>: Um unbefugte Einblicke in den Datenspeicherordner zu verhindern k�nnen Sie diesen Unterordner mit einem serverseitigen .htaccess-Passwortschutz versehen, so wie Sie es hoffentlich bereits f�r den Administrator-Ordner getan haben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragendatei</td>
 <td><input type="text" name="Fragen" value="<?php echo $fsFragen?>" style="width:150px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>fragen.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabFraLeer<?php if($sTabFraLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnisdatei</td>
 <td><input type="text" name="Ergebnis" value="<?php echo $fsErgebnis?>" style="width:150px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>ergebnis.csv</i>
 <div><input class="admCheck" type="checkbox" name="TabErgLeer<?php if($sTabErgLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdatei</td>
 <td><input type="text" name="Nutzer" value="<?php echo $fsNutzer?>" style="width:150px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>W�hlen Sie einen nicht zu erratenden Namen!</i>
 <div><input class="admCheck" type="checkbox" name="TabNtzLeer<?php if($sTabNtzLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Wenn Ihr Testfragen-Script mit Benutzerverwaltung arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zuweisung<br>Benutzer-&gt;Folgen</td>
 <td><input type="text" name="Zuweisung" value="<?php echo $fsZuweisung?>" style="width:150px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>zuweisungen.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabZuwLeer<?php if($sTabZuwLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Wenn Ihr Testfragen-Script mit Zuordnung zwischen Benutzer und Testfolgen arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">gespeicherte<br>Testfolgen</td>
 <td><input type="text" name="Folgen" value="<?php echo $fsFolgen?>" style="width:150px;<?php if($fsSQL) echo 'color:#8C8C8C;'?>" /> Vorschlag: <i>testfolgen.txt</i>
 <div><input class="admCheck" type="checkbox" name="TabFolLeer<?php if($sTabFolLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen</div>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini"><u>Warnung</u>: Im Datenordner vorhandene Dateien gleichen Namens werden ohne R�ckfrage �berschrieben!</td></tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">F�r die etwaige Datenspeicherung in einer <i>MySQL-Datenbank</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Host</td>
 <td><input type="text" name="SqlHost" value="<?php echo $fsSqlHost?>" style="width:250px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /> meist: <i>localhost</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Datenbank</td>
 <td><input type="text" name="SqlDaBa" value="<?php echo $fsSqlDaBa?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /> (die Datenbank muss unter diesem Namen bereits vorhanden sein)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Benutzer</td>
 <td><input type="text" name="SqlUser" value="<?php echo $fsSqlUser?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Passwort</td>
 <td><input type="password" name="SqlPass" value="<?php echo $fsSqlPass?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Tabellen</td>
 <td><table border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td><input type="text" name="SqlTabF" value="<?php echo $fsSqlTabF?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>fra_fragen</i> f�r die Testfragen</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSFrLeer<?php if($sTabSFrLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabE" value="<?php echo $fsSqlTabE?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>fra_ergebnis</i> f�r die Ergebnisse</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSErLeer<?php if($sTabSErLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabN" value="<?php echo $fsSqlTabN?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>fra_nutzer</i> f�r die Benutzer</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSNuLeer<?php if($sTabSNuLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabZ" value="<?php echo $fsSqlTabZ?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>fra_Zuweisung</i> f�r Testzuweisungen</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSZuLeer<?php if($sTabSZuLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr><tr>
   <td><input type="text" name="SqlTabT" value="<?php echo $fsSqlTabT?>" style="width:120px;<?php if(!$fsSQL) echo 'color:#8C8C8C;'?>" /></td>
   <td>&nbsp;Vorschlag: <i>fra_folgen</i> f�r die Testfolgen</td>
   <td>&nbsp;(<input class="admCheck" type="checkbox" name="TabSFoLeer<?php if($sTabSFoLeer=='1') echo'" checked="checked'?>" value="1" /> als leere Tabelle neu anlegen)</td>
  </tr>
 </table></td>
</tr>
<tr class="admTabl"><td class="admMini" colspan="2"><u>Warnung</u>: In der Datenbank vorhandene Tabellen gleichen Namens werden ohne R�ckfrage �berschrieben!</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihr Testfragen-Script mit Bildern arbeitet kann der Speicherort f�r Bilder verlegt werden. (<span class="admMini">Angabe relativ zum Programmordner</span>)</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilder-Ordner</td>
 <td><input type="text" name="Bilder" value="<?php echo(substr($fsBilder,-1,1)=='/'?substr($fsBilder,0,-1):$fsBilder)?>" style="width:250px;" /> Empfehlung: <i>bilder</i> &nbsp; <span class="admMini">(der Ordner muss bereits existieren)</span></td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur f�r diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> f�r alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

if($bBldNeu){
 if($f=opendir(FRA_Pfad.FRA_Bilder)){
  $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.') $a[]=$s; closedir($f);
  foreach($a as $s) @copy(FRA_Pfad.FRA_Bilder.$s,FRA_Pfad.$fsBilder.$s);
 }
}
?>