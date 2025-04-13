<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Datenbasis einstellen','','KDb');

$aSegmente=explode(';',MP_Segmente); $aAnordnung=explode(';',MP_Anordnung); $Mld2=''; $Mld3=''; $MldB='';
$mpSQL=MP_SQL; $mpSqlHost=MP_SqlHost; $mpSqlDaBa=MP_SqlDaBa; $mpSqlUser=MP_SqlUser; $mpSqlPass=MP_SqlPass; $mpSqlCharSet=MP_SqlCharSet;
$mpSqlTabI=MP_SqlTabI; $mpSqlTabS=MP_SqlTabS; $mpSqlTabN=MP_SqlTabN; $mpDaten=MP_Daten; $mpBilder=MP_Bilder; $mpBldTrennen=MP_BldTrennen;
$mpInserate=MP_Inserate; $mpStruktur=MP_Struktur; $mpNutzer=MP_Nutzer;
$mpBenachr=MP_Benachr; $mpMailAdr=MP_MailAdr; $mpSqlTabB=MP_SqlTabB; $mpSqlTabM=MP_SqlTabM; $bBldNeu=false; $bBldTrn=false;
if($_SERVER['REQUEST_METHOD']!='POST'){//GET
 $Meld='Kontrollieren oder ändern Sie die Einstellungen zur Datenbasis des Marktplatz-Scripts.'; $MTyp='Meld';
}else{//POST
 $aNF=explode(';',MP_NutzerFelder); $nNutzFelder=count($aNF)-1;
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false; $bOK=true;
 if(!$_POST['Sql']){ //->Text
  $mpSQL=false; $mpNutzer=txtVar('Nutzer'); $mpBenachr=txtVar('Benachr'); $mpMailAdr=txtVar('MailAdr');
  if(($mpDaten=txtVar('Daten'))&&($mpInserate=txtVar('Inserate'))&&($mpStruktur=txtVar('Struktur'))){
   if(substr($mpDaten,0,1)=='/') $mpDaten=substr($mpDaten,1); if(substr($mpDaten,-1,1)!='/') $mpDaten.='/';
   if(MP_SQL){ //SQL->Text
    $DbO=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa);
    if(!mysqli_connect_errno()){if(AM_SqlZs) $DbO->set_charset(AM_SqlZs);}else $DbO=NULL;
    if($DbO){
      if($rR=$DbO->query('SELECT nr,struktur FROM '.MP_SqlTabS.' ORDER BY nr')){
       while($a=$rR->fetch_row()) $aStru[(int)$a[0]]=rtrim($a[1]); $rR->close();
       $sFehlStru=''; $sFehlSpei=''; $sFehlLese=''; $sFehlSchr=''; $bOK=true;
       if($s=str_replace("\r",'',$aStru[0])){
        if($f=fopen(MP_Pfad.$mpDaten.'00'.$mpStruktur,'w')){fwrite($f,str_replace('/n/','\n ',$s).NL); fclose($f);}
        else{$sFehlSpei.=', leeres Mustersegment'; $bOK=false;}
       }else{$sFehlStru.=', leeres Mustersegment'; $bOK=false;}
       if($bOK) for($i=1;$i<100;$i++) if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
        if($s=str_replace("\r",'',$aStru[$i])){
         $sNr=sprintf('%02d',$i); $sNam=$aSegmente[$i];
         if($f=fopen(MP_Pfad.$mpDaten.$sNr.$mpStruktur,'w')){
          fwrite($f,str_replace('/n/','\n ',$s).NL); fclose($f);
          $a=explode("\n",$s); $aFN=explode(';',rtrim($a[0])); $aFT=explode(';',rtrim($a[1])); $nFelder=count($aFN);
          if($rR=$DbO->query('SELECT MAX(nr) FROM '.str_replace('%',$sNr,MP_SqlTabI))){$a=$rR->fetch_row(); $rR->close();}
          if($rR=$DbO->query('SELECT * FROM '.str_replace('%',$sNr,MP_SqlTabI).' ORDER BY mp_1'.($nFelder>2?',mp_2'.($nFelder>3?',mp_3':''):'').',nr')){
           $s='Nummer_'.(int)$a[0].';online'; for($j=1;$j<$nFelder;$j++) $s.=';'.$aFN[$j];
           while($a=$rR->fetch_row()){
            $s.=NL.$a[0].';'.$a[1]; array_splice($a,1,1);
            for($j=1;$j<$nFelder;$j++) $s.=';'.($aFT[$j]!='c'&&$aFT[$j]!='e'?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$j]))):fMpEnCode($a[$j]));
           }$rR->close();
           if($f=fopen(MP_Pfad.$mpDaten.$sNr.$mpInserate,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
           else{$sFehlSchr.=', '.$sNam; $bOK=false;}
          }else{$sFehlLese.=', '.$sNam; $bOK=false;}
         }else{$sFehlSpei.=', '.$sNam; $bOK=false;}
        }else{$sFehlStru.=', '.$sNam; $bOK=false;}
       }
       if($sFehlStru) $Mld2.='</p><p class="admFehl">Die Strukturdaten zum Segment <i>'.substr($sFehlStru,2).'</i> konnten nicht gelesen werden.';
       if($sFehlSpei) $Mld2.='</p><p class="admFehl">Die Strukturdatei zum Segment <i>'.substr($sFehlSpei,2).'</i> konnte nicht gespeichert werden.';
       if($sFehlLese) $Mld2.='</p><p class="admFehl">Die Inseratedaten zum Segment <i>'.substr($sFehlLese,2).'</i> konnten nicht gelesen werden';
       if($sFehlSchr) $Mld2.='</p><p class="admFehl">Die Inseratedatei zum Segment <i>'.substr($sFehlSchr,2).'</i> konnte nicht gespeichert werden.';
       if($bOK){//Nutzer umschreiben
        if($rR=$DbO->query('SELECT MAX(nr) FROM '.MP_SqlTabN)){$a=$rR->fetch_row(); $rR->close();}
        if($rR=$DbO->query('SELECT * FROM '.MP_SqlTabN.' ORDER BY nr')){
         $s='Nummer_'.(int)$a[0].';Session;aktiv'; for($i=2;$i<=$nNutzFelder;$i++) $s.=';'.$aNF[$i];
         while($a=$rR->fetch_row()){
          $b=array_splice($a,1,1); $s.=NL.$a[0].';'.$b[0].';'.$a[1].';'.fMpEnCode($a[2]).';'.$a[3].';'.fMpEnCode($a[4]);
          for($i=5;$i<$nNutzFelder;$i++) $s.=';'.str_replace(';','`,',str_replace('\"','"',$a[$i]));
         }$rR->close();
         if($f=fopen(MP_Pfad.$mpDaten.$mpNutzer,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
         else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Datei <i>'.$mpDaten.$mpNutzer.'</i> konnte nicht geschrieben werden.';}
        }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.MP_SqlTabN.'</i>!';}
       }
       if($bOK){//Benachrichtigungen umschreiben
        if($rR=$DbO->query('SELECT inserat,email FROM '.MP_SqlTabB.' ORDER BY nr DESC')){$s='#Inserat;eMail';//Benachrichtigungen
         while($a=$rR->fetch_row()){$s.=NL.$a[0].';'.fMpEnCode($a[1]);}$rR->close();
         if($f=fopen(MP_Pfad.$mpDaten.$mpBenachr,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
         else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Datei <i>'.$mpDaten.$mpBenachr.'</i> konnte nicht geschrieben werden.';}
        }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.MP_SqlTabB.'</i>!';}
        if($rR=$DbO->query('SELECT email FROM '.MP_SqlTabM.' ORDER BY nr DESC')){$s='#eMail';//eMail-Adressen
         while($a=$rR->fetch_row()){
          $t=$a[0]; if(!$p=strpos($t,';')) $s.=NL.fMpEnCode($t); else $s.=NL.substr($t,0,$p).';'.fMpEnCode(substr($t,$p+1));
         }$rR->close();
         if($f=fopen(MP_Pfad.$mpDaten.$mpMailAdr,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
         else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Datei <i>'.$mpDaten.$mpMailAdr.'</i> konnte nicht geschrieben werden.';}
        }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.MP_SqlTabM.'</i>!';}
       }
      }else $Mld2='</p><p class="admFehl">Abfragefehler bei MySQL-Tabelle <i>'.MP_SqlTabS.'</i>!';
      $DbO->close(); $DbO=NULL; $bNeu=$bOK;
    }else $Mld2='</p><p class="admFehl">Keine MySQL-Verbindung mit den vorliegenden Zugangsdaten!';
   }else{ //Text->Text
    $sFehlStru=''; $sFehlDate='';
    if($mpStruktur!=MP_Struktur||$mpDaten!=MP_Daten) for($i=0;$i<100;$i++) if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
     $sNr=sprintf('%02d',$i); if($i>0) $sNam=$aSegmente[$i]; else $sNam='leeres Mustersegment';
     $aD=file(MP_Pfad.MP_Daten.$sNr.MP_Struktur);
     if(is_array($aD)&&count($aD)>2&&($f=fopen(MP_Pfad.$mpDaten.$sNr.$mpStruktur,'w'))){
      fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bNeu=true;
     }else{$sFehlStru.=', '.$sNam; $bOK=false;}
    }
    if($sFehlStru) $Mld2.='</p><p class="admFehl">Die Strukturdatei zum Segment <i>'.substr($sFehlStru,2).'</i> konnte nicht umgespeichert werden.';
    if($bOK&&($mpInserate!=MP_Inserate||$mpDaten!=MP_Daten)) for($i=1;$i<100;$i++) if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
     $sNr=sprintf('%02d',$i); $sNam=$aSegmente[$i];
     $aD=file(MP_Pfad.MP_Daten.$sNr.MP_Inserate);
     if(is_array($aD)&&count($aD)>0&&($f=fopen(MP_Pfad.$mpDaten.$sNr.$mpInserate,'w'))){
      fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bNeu=true;
     }else{$sFehlDate.=', '.$sNam; $bOK=false;}
    }
    if($sFehlDate) $Mld2.='</p><p class="admFehl">Die Inseratedatei zum Segment <i>'.substr($sFehlDate,2).'</i> konnte nicht umgespeichert werden.';
    if($bOK&&($mpNutzer!=MP_Nutzer||$mpDaten!=MP_Daten)){
     $aD=file(MP_Pfad.MP_Daten.MP_Nutzer);
     if(is_array($aD)&&count($aD)>0&&($f=fopen(MP_Pfad.$mpDaten.$mpNutzer,'w'))){
      fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bNeu=true;
     }else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Benutzerdatei <i>'.$mpDaten.$mpNutzer.'</i> konnte nicht umgespeichert werden.';}
    }
    if($bOK&&($mpBenachr!=MP_Benachr||$mpDaten!=MP_Daten)){
     $aD=file(MP_Pfad.MP_Daten.MP_Benachr);
     if(is_array($aD)&&count($aD)>0&&($f=fopen(MP_Pfad.$mpDaten.$mpBenachr,'w'))){
      fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bNeu=true;
     }else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Benachrichtigungsdatei <i>'.$mpDaten.$mpBenachr.'</i> konnte nicht umgespeichert werden.';}
    }
    if($bOK&&($mpMailAdr!=MP_MailAdr||$mpDaten!=MP_Daten)){
     $aD=file(MP_Pfad.MP_Daten.MP_MailAdr);
     if(is_array($aD)&&count($aD)>0&&($f=fopen(MP_Pfad.$mpDaten.$mpMailAdr,'w'))){
      fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); $bNeu=true;
     }else{$bOK=false; $Mld2.='</p><p class="admFehl">Die Adressdatei <i>'.$mpDaten.$mpMailAdr.'</i> konnte nicht umgespeichert werden.';}
    }
   }//Text->Text
   if($bNeu&&$bOK){//Werte setzen
    fSetzMPWert(false,'SQL',''); fSetzMPWert($mpDaten,'Daten',"'"); fSetzMPWert($mpNutzer,'Nutzer',"'");
    fSetzMPWert($mpInserate,'Inserate',"'"); fSetzMPWert($mpStruktur,'Struktur',"'");
    fSetzMPWert($mpBenachr,'Benachr',"'"); fSetzMPWert($mpMailAdr,'MailAdr',"'");
   }
  }else $Meld='Speicherpfad und Dateinamen der Textdateien dürfen nicht leer sein!';
 }else{ //->SQL
  $mpSqlHost=txtVar('SqlHost'); $mpSqlDaBa=txtVar('SqlDaBa'); $mpSqlUser=txtVar('SqlUser'); $mpSqlPass=txtVar('SqlPass');
  $mpSqlTabI=txtVar('SqlTabI'); $mpSqlTabS=txtVar('SqlTabS'); $mpSqlTabN=txtVar('SqlTabN');
  $mpSqlTabB=txtVar('SqlTabB'); $mpSqlTabM=txtVar('SqlTabM'); $mpSqlCharSet=txtVar('SqlCharSet'); $mpSQL=true;
  if(strpos('#'.$mpSqlTabI,'%')){
   if(isset($DbO)&&!empty($DbO)) $DbO->close();
   $bPwNeu=($mpSqlHost==MP_SqlHost&&$mpSqlUser==MP_SqlUser&&$mpSqlPass!=MP_SqlPass&&$mpSqlDaBa==MP_SqlDaBa);
   $DbO=@new mysqli($mpSqlHost,$mpSqlUser,$mpSqlPass,$mpSqlDaBa);
   if(!mysqli_connect_errno()){if(AM_SqlZs) $DbO->set_charset(AM_SqlZs);}else $DbO=NULL;
   if($DbO){ // neue Verbindung
     include('feldtypenInc.php');
     $sN=', aktiv CHAR(1) NOT NULL DEFAULT "", benutzer VARCHAR(25) NOT NULL DEFAULT "", passwort VARCHAR(32) NOT NULL DEFAULT "", email VARCHAR(127) NOT NULL DEFAULT ""';
     for($i=5;$i<$nNutzFelder;$i++) $sN.=', dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""';
     if(MP_SQL){ //SQL->SQL
      $bSqlNeu=($mpSqlHost!=MP_SqlHost||$mpSqlUser!=MP_SqlUser||$mpSqlPass!=MP_SqlPass||$mpSqlDaBa!=MP_SqlDaBa); $DbA=NULL;
      if($mpSqlTabS!=MP_SqlTabS||$mpSqlTabI!=MP_SqlTabI||$mpSqlTabN!=MP_SqlTabN||$mpSqlTabB!=MP_SqlTabB||$mpSqlTabM!=MP_SqlTabM||$bSqlNeu){
       if($bSqlNeu){
        $DbA=@new mysqli(MP_SqlHost,MP_SqlUser,MP_SqlPass,MP_SqlDaBa); //alte Verbindung
        if(!mysqli_connect_errno()){if(AM_SqlZs) $DbO->set_charset(AM_SqlZs);}else $DbA=NULL;
       }else $DbA=$DbO;
       if($DbA){
        if($mpSqlTabS!=MP_SqlTabS||$bSqlNeu){ //Struktur neu
         $aD=array(); $sFehlSpei='';
         if($rR=$DbA->query('SELECT nr,struktur FROM '.MP_SqlTabS.' ORDER BY nr')){
          while($a=$rR->fetch_row()) $aD[(int)$a[0]]=rtrim($a[1]); $rR->close();
         }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.MP_SqlHost.':'.MP_SqlDaBa.'.'.MP_SqlTabS.'</i>!';}
         if($bOK){
          $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabS);
          if($DbO->query('CREATE TABLE '.$mpSqlTabS.' (nr INT(3) NOT NULL DEFAULT "0",struktur text NOT NULL, PRIMARY KEY (nr)) COMMENT="Maktplatz-Segmentstrukturen"')){
           for($i=0;$i<100;$i++){
            $sStru=''; $sNam='leeres Mustersegment';
            if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
             $bNeu=true; if($i>0) $sNam=$aSegmente[$i]; if(isset($aD[$i])) $sStru=str_replace('"','\"',$aD[$i]);
            }
            if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabS.' VALUES('.$i.',"'.$sStru.'")')){$bOK=false; $sFehlSpei.=', '.sNam;}
           }
           if($sFehlSpei) $Mld2.='</p><p class="admFehl">Die Strukturinformation für das Segment <i>'.substr($sFehlStru,2).'</i> konnte nicht eingefügt werden.';
          }else{$bOK=false; $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabS.'</i> konnte nicht angelegt werden!';}
         }
        }//Struktur
        if($bOK) if($mpSqlTabI!=MP_SqlTabI||$bSqlNeu){//Inserate neu
         $sFehlLese=''; $sFehlCrea=''; $sFehlSpei='';
         for($i=1;$i<100;$i++) if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
          $sNr=sprintf('%02d',$i); $aD=array(); $a=array();
          if($rR=$DbO->query('SELECT nr,struktur FROM '.$mpSqlTabS.' WHERE nr='.$i)){
           $a=$rR->fetch_row(); $rR->close();
          }
          $a=explode("\n",$a[1]); $aFT=explode(';',rtrim($a[1])); $nFelder=count($aFT);
          if($rR=$DbA->query('SELECT * FROM '.str_replace('%',$sNr,MP_SqlTabI).' WHERE online="1" ORDER BY mp_1,nr')){
           while($a=$rR->fetch_row()){
            $s='"'.$a[0].'","1","'.$a[2].'"'; for($j=3;$j<=$nFelder;$j++) $s.=',"'.str_replace('"','\"',$a[$j]).'"'; $aD[]=$s;
           }$rR->close();
          }else{$bOK=false; $FehlLese.=', '.$aSegmente[$i];}
          if($bOK){
           $sTab=str_replace('%',$sNr,$mpSqlTabI); $DbO->query('DROP TABLE IF EXISTS '.$sTab);
           $s=''; for($j=2;$j<$nFelder;$j++) $s.=', mp_'.$j.' '.$aSql[$aFT[$j]]; $nSaetze=count($aD);
           if($DbO->query('CREATE TABLE '.$sTab.'(nr INT(11) NOT NULL auto_increment, online CHAR(1) NOT NULL DEFAULT "0", mp_1 char(10) NOT NULL DEFAULT ""'.$s.', PRIMARY KEY (nr), KEY mp_1 (mp_1)) COMMENT="Markplatz-Inserate-'.$sNr.'"')){
            for($j=0;$j<$nSaetze;$j++) if(!$DbO->query('INSERT IGNORE INTO '.$sTab.' VALUES('.$aD[$j].')')) $bOK=false;
            if($bOK) $bNeu=true; else $FehlSpei.=', '.$aSegmente[$i];
           }else{$bOK=false; $sFehlCrea.=', '.$aSegmente[$i];}
          }
         }
         if($sFehlLese) $Mld2.='</p><p class="admFehl">Die Inserate des Segments <i>'.substr($sFehlLese,2).'</i> konnte nicht eingelesen werden.';
         if($sFehlCrea) $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle des Segments <i>'.substr($sFehlLese,2).'</i> konnte nicht angelegt werden.';
         if($sFehlSpei) $Mld2.='</p><p class="admFehl">Die Inserate des Segments <i>'.substr($sFehlLese,2).'</i> konnte nicht vollständig übernommen werden.';
        }//Inserate
        if($bOK) if($mpSqlTabN!=MP_SqlTabN||$bSqlNeu){//Nutzer neu
         $aD=array();
         if($rR=$DbA->query('SELECT * FROM '.MP_SqlTabN.' ORDER BY nr')){
          while($a=$rR->fetch_row()){
           $s='"'.$a[0].'"'; //array_splice($a,1,1);
           for($i=1;$i<=$nNutzFelder;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aD[]=$s;
          }$rR->close();
         }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.MP_SqlHost.':'.MP_SqlDaBa.'.'.MP_SqlTabN.'</i>!';}
         if($bOK){
          $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabN); $nSaetze=count($aD);
          if($DbO->query('CREATE TABLE '.$mpSqlTabN.' (nr INT(11) NOT NULL auto_increment, session CHAR(8) NOT NULL DEFAULT ""'.$sN.', PRIMARY KEY (nr)) COMMENT="Marktplatz-Benutzer"')){
           for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabN.' VALUES('.$aD[$i].')')) $bOK=false;
           if($bOK) $bNeu=true; else $Mld2.='</p><p class="admFehl">Die Benutzer konnten nicht vollständig nach <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabN.'</i> übernommen werden.';
          }else $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabN.'</i> konnte nicht angelegt werden!';
         }
        }//Nutzer
        if($bOK) if($mpSqlTabB!=MP_SqlTabB||$bSqlNeu){//Benachrichtigungen neu
         $aD=array();
         if($rR=$DbA->query('SELECT * FROM '.MP_SqlTabB.' ORDER BY nr')){
          while($a=$rR->fetch_row()) $aD[]='"'.$a[0].'","'.$a[1].'","'.$a[2].'"'; $rR->close();
         }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.MP_SqlHost.':'.MP_SqlDaBa.'.'.MP_SqlTabB.'</i>!';}
         if($bOK){
          $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabB); $nSaetze=count($aD);
          if($DbO->query('CREATE TABLE '.$mpSqlTabB.' (nr INT(11) NOT NULL auto_increment,inserat VARCHAR(10) NOT NULL DEFAULT "",email VARCHAR(127) NOT NULL DEFAULT "", PRIMARY KEY (nr)) COMMENT="Marktplatz-Benachrichtigungen"')){
           for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabB.' VALUES('.$aD[$i].')')) $bOK=false;
           if($bOK) $bNeu=true; else $Mld2.='</p><p class="admFehl">Die Benachrichtigungen konnten nicht vollständig nach <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabB.'</i> übernommen werden.';
          }else $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabB.'</i> konnte nicht angelegt werden!';
         }
        }//Benachrichtigungen
        if($bOK) if($mpSqlTabM!=MP_SqlTabM||$bSqlNeu){//MailAdressen neu
         $aD=array();
         if($rR=$DbA->query('SELECT * FROM '.MP_SqlTabM.' ORDER BY nr')){
          while($a=$rR->fetch_row()) $aD[]='"'.$a[0].'","'.$a[1].'"'; $rR->close();
         }else{$bOK=false; $Mld2.='</p><p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.MP_SqlHost.':'.MP_SqlDaBa.'.'.MP_SqlTabM.'</i>!';}
         if($bOK){
          $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabM); $nSaetze=count($aD);
          if($DbO->query('CREATE TABLE '.$mpSqlTabM.' (nr INT(11) NOT NULL auto_increment,email VARCHAR(127) NOT NULL DEFAULT "", PRIMARY KEY (nr)) COMMENT="Marktplatz-Mailadressen"')){
           for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabM.' VALUES('.$aD[$i].')')) $bOK=false;
           if($bOK) $bNeu=true; else $Mld2.='</p><p class="admFehl">Die Mailadressen konnte nicht vollständig nach <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabM.'</i> übernommen werden.';
          }else $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabM.'</i> konnte nicht angelegt werden!';
         }
        }//MailAdressen
       }elseif($bPwNeu){ // nur PW geaendert
        $Mld2='</p><p class="admErfo">Das neue Passwort wurde akzeptiert</i>!'; fSetzMPWert($mpSqlPass,'SqlPass',"'"); $bNeu=true;
       }else $Mld2='</p><p class="admFehl">Kein Zugriff auf die bisherige Datenbank <i>'.MP_SqlHost.':'.MP_SqlDaBa.'</i>!';
      }//keine Aenderung
     }else{//Text->SQL
      $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabS); $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabN);
      $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabB); $DbO->query('DROP TABLE IF EXISTS '.$mpSqlTabM);
      if($DbO->query('CREATE TABLE '.$mpSqlTabS.' (nr INT(3) NOT NULL DEFAULT "0",struktur text NOT NULL, PRIMARY KEY (nr)) COMMENT="Maktplatz-Segmentstrukturen"')){
       $sFehlStru=''; $sFehlSpei=''; $sFehlCrea=''; $sFehlLese=''; $sFehlEinf='';
       for($i=0;$i<100;$i++){
        $sNr=sprintf('%02d',$i); $sStru=''; $sNam='leeres Mustersegment';
        if(isset($aSegmente[$i])&&!empty($aSegmente[$i])&&$aSegmente[$i]!='LEER'){
         $aStru=file(MP_Pfad.MP_Daten.$sNr.MP_Struktur); if($i>0) $sNam=$aSegmente[$i];
         if(is_array($aStru)&&count($aStru)>15){
          if($i>0){
           $DbO->query('DROP TABLE IF EXISTS '.str_replace('%',$sNr,$mpSqlTabI));
           $aFT=explode(';',rtrim($aStru[1])); $nFelder=count($aFT);
           $s=''; for($j=2;$j<$nFelder;$j++) $s.=', mp_'.$j.' '.$aSql[$aFT[$j]];
           if($DbO->query('CREATE TABLE '.str_replace('%',$sNr,$mpSqlTabI).'(nr INT(11) NOT NULL auto_increment, online char(1) NOT NULL DEFAULT "0", mp_1 char(10) NOT NULL DEFAULT ""'.$s.', PRIMARY KEY (nr), KEY mp_1 (mp_1)) COMMENT="Markplatz-Inserate-'.$sNr.'"')){
            $aD=file(MP_Pfad.MP_Daten.$sNr.MP_Inserate); $bGelesen=true;
            if(is_array($aD)&&($nSaetze=count($aD))){//Daten einlesen
             for($j=1;$j<$nSaetze;$j++){
              $a=explode(';',rtrim($aD[$j])); $s='"'.$a[0].'","'.$a[1].'"'; array_splice($a,1,1);
              for($k=1;$k<$nFelder;$k++) $s.=',"'.($aFT[$k]!='c'&&$aFT[$k]!='e'?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$k]))):fMpDeCode($a[$k])).'"';
              if(!$DbO->query('INSERT IGNORE INTO '.str_replace('%',$sNr,$mpSqlTabI).' VALUES('.$s.')')) $bGelesen=false;
             }
             if(!$bGelesen) $sFehlEinf.=', '.$sNam;
            }else $sFehlLese.=', '.$sNam;
           }else{$sFehlCrea.=', '.$sNam; $bOK=false;}
          }
          $sStru=str_replace('"','\"',str_replace("\n","\r\n",str_replace("\r",'',str_replace('\n ','/n/',rtrim(implode('',$aStru))))));
         }else{$sFehlStru.=', '.$sNam; $bOK=false;}
        }
        if($DbO->query('INSERT IGNORE INTO '.$mpSqlTabS.' VALUES('.$i.',"'.$sStru.'")')) $bNeu=true; else {$sFehlSpei.=', '.sNam; $bOK=false;}
       }
       if($sFehlStru) $Mld2.='</p><p class="admFehl">Die Strukturdatei für das Segment <i>'.substr($sFehlStru,2).'</i> konnte nicht eingelesen werden.';
       if($sFehlSpei) $Mld2.='</p><p class="admFehl">Die Strukturinformation für das Segment <i>'.substr($sFehlStru,2).'</i> konnte nicht eingefügt werden.';
       if($sFehlCrea) $Mld2.='</p><p class="admFehl">Die MySQL-Tabelle für das Segment <i>'.substr($sFehlCrea,2).'</i> konnte nicht angelegt werden.';
       if($sFehlLese) $Mld3.='</p><p class="admFehl">Die Inserate des Segments <i>'.substr($sFehlLese,2).'</i> konnte nicht eingelesen werden.';
       if($sFehlEinf) $Mld3.='</p><p class="admFehl">Nicht alle Inserate des Segments <i>'.substr($sFehlEinf,2).'</i> konnten übernommen werden.';
       if($bOK){//Nutzer umschreiben
        if($DbO->query('CREATE TABLE '.$mpSqlTabN.' (nr INT(11) NOT NULL auto_increment, session CHAR(8) NOT NULL DEFAULT ""'.$sN.', PRIMARY KEY (nr)) COMMENT="Marktplatz-Benutzer"')){
         $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $nSaetze=count($aD); $bGelesen=true;
         for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i])); $b=array_splice($a,1,1); $s='"'.$a[0].'","'.$b[0].'","'.$a[1].'","'.fMpDeCode($a[2]).'","'.$a[3].'","'.fMpDeCode($a[4]).'"';
          for($j=5;$j<$nNutzFelder;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('`,',';',$a[$j])):'').'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabN.' VALUES('.$s.')')) $bGelesen=false;
         }
         if(!$bGelesen) $Mld3.='</p><p class="admFehl">Nicht alle Benutzerdatensätze konnten nach <i>'.$mpSqlTabN.'</i> übernommen werden.';
        }else{$Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabN.'</i> konnte nicht angelegt werden!'; $bOK=false;}
       }
       if($bOK){//Benachrichtigungen umschreiben
        if($DbO->query('CREATE TABLE '.$mpSqlTabB.' (nr INT(11) NOT NULL auto_increment,inserat VARCHAR(10) NOT NULL DEFAULT "",email VARCHAR(127) NOT NULL DEFAULT "", PRIMARY KEY (nr)) COMMENT="Marktplatz-Benachrichtigungen"')){
         $aD=file(MP_Pfad.MP_Daten.MP_Benachr); $nSaetze=count($aD); $bGelesen=true;
         for($i=1;$i<$nSaetze;$i++){
          $a=explode(';',rtrim($aD[$i])); $s='"'.($nSaetze-$i).'","'.$a[0].'","'.fMpDeCode($a[1]).'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabB.' VALUES('.$s.')')) $bGelesen=false;
         }
         if(!$bGelesen) $Mld3.='</p><p class="admFehl">Nicht alle Benachrichtigungsdatensätze konnten nach <i>'.$mpSqlTabB.'</i> übernommen werden.';
        }else{$Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabB.'</i> konnte nicht angelegt werden!'; $bOK=false;}
       }
       if($bOK){//Mailadressen umschreiben
        if($DbO->query('CREATE TABLE '.$mpSqlTabM.' (nr INT(11) NOT NULL auto_increment,email VARCHAR(127) NOT NULL DEFAULT "", PRIMARY KEY (nr)) COMMENT="Marktplatz-Mailadressen"')){
         $aD=file(MP_Pfad.MP_Daten.MP_MailAdr); $nSaetze=count($aD); $bGelesen=true;
         for($i=1;$i<$nSaetze;$i++){$s=rtrim($aD[$i]);
         if(!$p=strpos($s,';')) $s='"'.($nSaetze-$i).'","'.fMpDeCode($s).'"'; else $s='"'.($nSaetze-$i).'","'.substr($s,0,$p).';'.fMpDeCode(substr($s,$p+1)).'"';
          if(!$DbO->query('INSERT IGNORE INTO '.$mpSqlTabM.' VALUES('.$s.')')) $bGelesen=false;
         }
         if(!$bGelesen) $Mld3.='</p><p class="admFehl">Nicht alle E-Mail-Adressen konnten nach <i>'.$mpSqlTabM.'</i> übernommen werden.';
        }else{$Mld2.='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabM.'</i> konnte nicht angelegt werden!'; $bOK=false;}
       }
      }else $Mld2='</p><p class="admFehl">Die MySQL-Tabelle <i>'.$mpSqlHost.':'.$mpSqlDaBa.'.'.$mpSqlTabS.'</i> konnte nicht angelegt werden!';
     }//-->SQL
     if($bNeu&&$bOK){//Werte setzen
      fSetzMPWert($mpSqlHost,'SqlHost',"'"); fSetzMPWert($mpSqlDaBa,'SqlDaBa',"'"); fSetzMPWert($mpSqlUser,'SqlUser',"'"); fSetzMPWert($mpSqlPass,'SqlPass',"'");
      fSetzMPWert($mpSqlTabS,'SqlTabS',"'"); fSetzMPWert($mpSqlTabI,'SqlTabI',"'"); fSetzMPWert($mpSqlTabN,'SqlTabN',"'");
      fSetzMPWert($mpSqlTabB,'SqlTabB',"'"); fSetzMPWert($mpSqlTabM,'SqlTabM',"'"); fSetzMPWert(true,'SQL',''); fSetzMPWert($mpSqlCharSet,'SqlCharSet',"'");
     }
     $DbO->close(); $DbO=NULL;
   }else{
    if($bPwNeu) $Mld2='</p><p class="admFehl">Keine MySQL-Verbindung mit dem angegebenen Passwort <i>'.$mpSqlPass.'</i>!';
    else $Mld2='</p><p class="admFehl">Keine MySQL-Verbindung mit den angegebenen Zugangsdaten!';
   }
  }else $Meld='Der SQL-Tabellenname für Inserate muss einen Platzhalter % enthalten!';
  if($mpSqlCharSet!=MP_SqlCharSet){if(fSetzMPWert($mpSqlCharSet,'SqlCharSet',"'")) $bNeu=true;}
 }
 if(!$mpBilder=txtVar('Bilder')) $mpBilder='bilder';
 if(substr($mpBilder,0,1)=='/') $mpBilder=substr($mpBilder,1); if(substr($mpBilder,-1,1)!='/') $mpBilder.='/';
 if($mpBilder!=$mpDaten&&$mpBilder!=MP_CaptchaPfad&&$mpBilder!='grafik/'){
  if(is_writable(MP_Pfad.substr($mpBilder,0,-1))){
   if(fSetzMPWert($mpBilder,'Bilder',"'")){$bNeu=true; $bBldNeu=true;}
  }else $MldB='</p><p class="admFehl">Der vorgesehene Bilder-Ordner ist nicht beschreibbar!';
 }else $MldB='</p><p class="admFehl">Der vorgesehene Name für den Bilder-Ordner ist nicht möglich!';
 $mpBldTrennen=txtVar('BldTrennen'); if(fSetzMPWert(($mpBldTrennen?true:false),'BldTrennen','')){$bNeu=true; $bBldTrn=true;}

 if($bNeu&&$bOK){//Speichern
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld='Die Einstellungen zur Datenbasis wurden gespeichert.'; $MTyp='Erfo';
  }else{$Meld='In die Datei <i>mpWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!'; $mpSQL=MP_SQL;}
 }elseif($Mld2||$Mld3){
  $Meld='Das Umspeichern der Datenbasis ist leider fehlgeschlagen!'; $mpSQL=MP_SQL;
 }elseif(empty($Meld)){$Meld='Die Einstellungen zur Datenbasis bleiben unverändert.'; $MTyp='Meld';}

 if($bBldTrn){//Bildertrennung ändern
  $aSeg=explode(';',MP_Segmente); $nSeg=count($aSeg);
  if($mpBldTrennen){//Bilder trennen
   $aBld=array(); $aDir=array(); $aMk=array(); $bMkErr=false; $bErr=false;
   if($f=opendir(MP_Pfad.MP_Bilder)){
    while($s=readdir($f)) if(substr($s,0,1)!='.'&&!is_dir($s)){
     if($n=(int)$s){$aBld[]=$s; if($s=substr($n,-2)) $aDir[$s]=true;}
    }
    closedir($f);
   }
   for($i=1;$i<$nSeg;$i++) if(!empty($aSeg[$i])){
    $s=sprintf('%02d',$i);
    if(isset($aDir[$s])) if(!file_exists(MP_Pfad.$mpBilder.$s)) if(mkdir(MP_Pfad.$mpBilder.$s,0777)) $aMk[$s]=true; else $bMkErr=true;
   }
   foreach($aBld as $s){
    $n=(int)$s; $u=substr($n,-2); $l=strlen($n);
    if(isset($aMk[$u])){
     if(@copy(MP_Pfad.MP_Bilder.$s,MP_Pfad.$mpBilder.$u.'/'.substr_replace($s,'',$l-2,2))) @unlink(MP_Pfad.MP_Bilder.$s);
     else $bErr=true;
    }
   }
   if($bMkErr) $MldB.='</p><p class="admFehl">Nicht alle Bilderunterordner konnten angelegt werden!';
   if($bErr) $MldB.='</p><p class="admFehl">Nicht alle Bilder konnten umgespeichert werden!';
  }else{//Bilder zusammenführen
   $aBld=array(); $aDir=array(); $bErr=false;
   if($f=opendir(MP_Pfad.MP_Bilder)){
    while($s=readdir($f)) if(substr($s,0,1)!='.'&&strlen($s)==2) if($n=(int)$s) $aDir[$s]=true;
    closedir($f);
   }
   for($i=1;$i<$nSeg;$i++) if(!empty($aSeg[$i])){
    $u=sprintf('%02d',$i); $aBld=array();
    if(isset($aDir[$u])){
     if($f=opendir(MP_Pfad.MP_Bilder.$u)) while($s=readdir($f)) if(substr($s,0,1)!='.') $aBld[]=$s;
     closedir($f);
    }
    foreach($aBld as $s) if($n=(int)$s){
     $l=strlen($n);
     if(@copy(MP_Pfad.MP_Bilder.$u.'/'.$s,MP_Pfad.$mpBilder.substr_replace($s,$u,$l,0))) @unlink(MP_Pfad.MP_Bilder.$u.'/'.$s);
     else $bErr=true;
    }
    @rmdir(MP_Pfad.MP_Bilder.$u);
   }
  }
 }elseif($bBldNeu){//Bilder umspeichern
  $bErr=false;
  if($f=opendir(MP_Pfad.MP_Bilder)){
   $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.') $a[]=$s; closedir($f);
   foreach($a as $s) if(!@copy(MP_Pfad.MP_Bilder.$s,MP_Pfad.$mpBilder.$s)) $bErr=true;
  }
  if($bErr) $MldB.='</p><p class="admFehl">Nicht alle Bilder konnten umgespeichert werden!';
 }
}//POST

//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.$Meld.$Mld2.$Mld3.$MldB.'</p>'.NL;
$bNutzer=false; if((isset($aFT)&&in_array('u',$aFT))||MP_NListeAnders||MP_NDetailAnders||MP_NEingabeAnders||MP_NVerstecktSehen) $bNutzer=true;
?>

<form action="konfDaten.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Marktplatz-Script speichert die Inseratedaten in tabellarischer Form auf dem Webserver,
um daraus bei jeder Anforderung dynamisch eine Ausgabeseite zu generieren.
</td></tr>
<tr class="admTabl">
 <td valign="top" style="padding-top:5px;">Datenbasis</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td width="130" valign="top"><input class="admRadio" type="radio" name="Sql" value="0"<?php if(!$mpSQL) echo ' checked="checked"';?> /> Textdatei</td>
    <td style="padding-bottom:8px;">Standardmäßig werden zum Speichern einfache Textdateien verwendet.
Diese Methode ist schnell und ressourcenschonend.
Allerdings muss das Script dazu die Berechtigung besitzen, in eine solche Inseratedatei bzw. Benutzerdatei schreiben zu dürfen.
Eine solche Schreibberechtigung stellt auf einigen wenigen ungeschickt konfigurierten Servern
unter extrem seltenen Bedingungen ein gewisses Sicherheitsrisiko dar.</td>
   </tr>
   <tr>
    <td width="130" valign="top"><input class="admRadio" type="radio" name="Sql" value="1"<?php if($mpSQL) echo ' checked="checked"';?> /> MySQL-Tabelle</td>
    <td>Abweichend davon können die Daten auch in Tabellen einer MySQL-Datenbank gepeichert werden.
Diese Methode ist wesentlich ressourcenverbrauchender solange die Inseratedatei nur wenige Hundert Inserate enthält.
In Fällen, da mehrere Tausend Inserate in einem Segment des Markplatz eingetragen sind bzw.
wo Inserate durch mehrere Benutzer quasi gleichzeitig eingetragen und häufig geändert werden
kann die MySQL-Datenquelle hingegen Geschwindigkeits- oder Sicherheitsvorteile bringen.</td>
   </tr>
  </table></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini">
  <u>Hinweis</u>: Wenn Sie die Datenbasis umschalten werden die Inserate
  aus der momentanen Datenquelle mit der momentanen Segment- und Inseratestruktureigenschaften
  auf den neuen Datenspeicher umgeschrieben.
  Etwaig vorhandene ältere Inseratespeicher aus früheren Umschaltungen werden überschrieben.
  Gleiches gilt für die Benutzerdaten, falls die Benutzerverwaltung aktiv ist.<br>
  <u>Hinweis</u>: Solange Sie noch Ihre optimalen Marktstruktureigenschaften suchen und an ihr &quot;herumbasteln&quot;
  sollten Sie die Datenbasis noch nicht auf MySQL-Datenbank umstellen sondern es vorerst bei Text-Datenbasis belassen.
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in <i>Textdateien</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherordner</td>
 <td><input type="text" name="Daten" value="<?php echo(substr($mpDaten,-1,1)=='/'?substr($mpDaten,0,-1):$mpDaten)?>" style="width:250px;<?php if($mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>daten</i>
 <div class="admMini">Unterordner, relativ zum Hauptordner des Marktplatz-Scripts. Der Ordner muss bereits existieren. <a href="<?php echo AM_Hilfe?>LiesMich.htm#1.2" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Inserate<div style="margin-top:24px;">Strukturdatei</div></td>
 <td><table border="0" cellpadding="0" cellspacing="0">
 <tr><td rowspan="2" valign="top"><input type="text" name="Inserate" value="<?php echo $mpInserate?>" style="width:150px;<?php if($mpSQL) echo 'color:#8C8C8C;'?>" />&nbsp;</td><td>Empfehlung: <i>inserate.txt</i></td></tr>
 <tr><td><div class="admMini">wichtiger Hinweis: Der Dateiname darf nicht mit einer Zahl beginnen, da pro Marktsegment eine separate Inseratedatei gekennzeichnet durch eine Zahl vor dem Namen geführt wird.</div></td></tr></table>
 <div><input type="text" name="Struktur" value="<?php echo $mpStruktur?>" style="width:150px;<?php if($mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>struktur.txt</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdatei</td>
 <td><table border="0" cellpadding="0" cellspacing="0">
 <tr><td rowspan="2"><input type="text" name="Nutzer" value="<?php echo $mpNutzer?>" style="width:150px;<?php if($mpSQL||!$bNutzer) echo 'color:#8C8C8C;'?>" />&nbsp;</td><td>Empfehlung: <i>Wählen Sie einen nicht zu erratenden Dateinamen!</i></td></tr>
 <tr><td><div class="admMini">wichtiger Hinweis: Der Dateiname darf nicht mit einer Zahl beginnen.</div></td></tr></table>
 <div class="admMini" style="margin-top:3px;">Wenn Ihr Marktplatz mit Benutzerverwaltung arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td>Benach-<br>richtigungsdatei<br>und Adressdatei</td>
 <td><input type="text" name="Benachr" value="<?php echo $mpBenachr?>" style="width:150px;<?php if($mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>benachr00.txt</i>
 <div><input type="text" name="MailAdr" value="<?php echo $mpMailAdr?>" style="width:150px;<?php if($mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mailadr00.txt</i></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admMini"><u>Warnung</u>: Im Datenordner vorhandene Dateien gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in <i>MySQL-Tabellen</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td>MySQL-Hostname</td>
 <td><input type="text" name="SqlHost" value="<?php echo $mpSqlHost?>" style="width:250px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> meist: <i>localhost</i></td>
</tr>
<tr class="admTabl">
 <td>MySQL-Datenbankname</td>
 <td><input type="text" name="SqlDaBa" value="<?php echo $mpSqlDaBa?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> (die Datenbank muss unter diesem Namen bereits vorhanden sein)</td>
</tr>
<tr class="admTabl">
 <td>MySQL-Benutzername</td>
 <td><input type="text" name="SqlUser" value="<?php echo $mpSqlUser?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td>MySQL-Passwort</td>
 <td><input type="password" name="SqlPass" value="<?php echo $mpSqlPass?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /></td>
</tr>
<tr class="admTabl">
 <td valign="top">MySQL-Tabellennamen</td>
 <td><input type="text" name="SqlTabI" value="<?php echo $mpSqlTabI?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mp_%inserate</i> für die Inseratetabelle
 <div><input type="text" name="SqlTabS" value="<?php echo $mpSqlTabS?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mp_struktur</i> für die Struktureigenschaftstabelle</div></td>
</tr>
<tr class="admTabl">
 <td valign="top">MySQL-Zusatztabellen</td>
 <td><input type="text" name="SqlTabN" value="<?php echo $mpSqlTabN?>" style="width:120px;<?php if(!$mpSQL||!$bNutzer) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mp_nutzer</i> für die Benutzertabelle
 <div><input type="text" name="SqlTabB" value="<?php echo $mpSqlTabB?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mp_benachr</i> für die Änderungsbenachrichtigungswünsche</div>
 <div><input type="text" name="SqlTabM" value="<?php echo $mpSqlTabM?>" style="width:120px;<?php if(!$mpSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>mp_mailadr</i> für die freigegebenen Empfängeradressen</div></td>
</tr>
<tr class="admTabl"><td class="admMini" colspan="2">Der Name der Insertetabelle muss einen Platzhalter % für die laufende Nummer des Markplatz-Segmentes enthalten, da pro Marktsegment eine separate Inseratetabelle geführt wird.<br /><u>Warnung</u>: In der Datenbank vorhandene Tabellen gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Zeichensatz<br />im Besucherbereich</td>
 <td><input type="text" name="SqlCharSet" value="<?php echo $mpSqlCharSet?>" style="width:11em;" /> <span class="admMini">(Empfehlung: leer lassen oder z.B. <i>latin1</i>, selten auch <i>utf8</i> bzw. <i>utf8mb4</i>)</span>
 <div class="admMini"><u>Erklärung</u>: In <i>zunehmenden</i> Fällen scheint es nötig sein, die MySQL-Datenbankverbindung des Besucherbereiches zwangsweise über den Befehl <span style="white-space:nowrap;"><i>mysqli_set_charset()</i></span> auf einen bestimmten Zeichensatz umzustellen.</div>
 <div class="admMini" style="margin-bottom:2px"><u>Hinweis</u>: Für die <a href="konfAdmin.php">Administration</a>sseiten gibt es eine eigene Einstellung zum MySQL-Datensatz.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihr Marktplatz mit Bildern und/oder Dateianhängen arbeitet kann der Speicherordner für Bilder/Anhänge verlegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilderordner</td>
 <td><input type="text" name="Bilder" value="<?php echo(substr($mpBilder,-1,1)=='/'?substr($mpBilder,0,-1):$mpBilder)?>" style="width:250px;" /> Empfehlung: <i>bilder</i>   &nbsp; <span class="admMini">(der Ordner muss bereits existieren)</span>
  <div><input class="admRadio" type="radio" name="BldTrennen" value="0"<?php if(!$mpBldTrennen) echo ' checked="checked"';?>> Bilder aller Segmente im gemeinsamen Bilderordner</div>
  <div><input class="admRadio" type="radio" name="BldTrennen" value="1"<?php if($mpBldTrennen) echo ' checked="checked"';?>> für jedes Segment einen gesonderten Bilderunterordner &nbsp; <span class="admMini">(nur bei <i>sehr</i> vielen Bildern)</span></div>
 </td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>