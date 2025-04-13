<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Datenbasis einstellen','','KDb');
if($DbO) $DbO->close(); $DbO=NULL; $bBldNeu=false;
if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
  $ksSQL=KAL_SQL; $ksSqlHost=KAL_SqlHost; $ksSqlDaBa=KAL_SqlDaBa; $ksSqlUser=KAL_SqlUser; $ksSqlPass=KAL_SqlPass;
  $ksSqlTabT=KAL_SqlTabT; $ksSqlTabN=KAL_SqlTabN; $ksDaten=KAL_Daten; $ksBilder=KAL_Bilder; $ksTermine=KAL_Termine; $ksNutzer=KAL_Nutzer; $ksAdminTexte=KAL_AdminTexte;
  $ksSqlTabE=KAL_SqlTabE; $ksSqlTabB=KAL_SqlTabB; $ksSqlTabM=KAL_SqlTabM; $ksErinner=KAL_Erinner; $ksBenachr=KAL_Benachr; $ksMailAdr=KAL_MailAdr; $ksSqlTabA=KAL_SqlTabA;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $nFelder=count($kal_FeldName); array_splice($kal_NutzerFelder,1,1); $nNutzFelder=count($kal_NutzerFelder); $nZusageFelder=substr_count(KAL_ZusageFelder,';');
 if(!$_POST['Sql']){ //->Text
  $ksSqlHost=KAL_SqlHost; $ksSqlDaBa=KAL_SqlDaBa; $ksSqlUser=KAL_SqlUser; $ksSqlPass=KAL_SqlPass; $ksSQL=false;
  $ksNutzer=inpVar('Nutzer'); $ksErinner=inpVar('Erinner'); $ksBenachr=inpVar('Benachr'); $ksMailAdr=inpVar('MailAdr'); $ksAdminTexte=inpVar('AdminTexte');
  $ksSqlTabT=KAL_SqlTabT; $ksSqlTabN=KAL_SqlTabN; $ksSqlTabE=KAL_SqlTabE; $ksSqlTabB=KAL_SqlTabB; $ksSqlTabM=KAL_SqlTabM; $ksSqlTabA=KAL_SqlTabA;
  if(($ksDaten=inpVar('Daten'))&&($ksTermine=inpVar('Termine'))){
   if(substr($ksDaten,0,1)=='/') $ksDaten=substr($ksDaten,1); if(substr($ksDaten,-1,1)!='/') $ksDaten.='/';
   if($ksTermine!=KAL_Vorgaben){
    if(KAL_SQL){ //SQL->Text
     $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
     if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else $DbO=NULL;
     if($DbO){
       if($rR=$DbO->query('SELECT MAX(id) FROM '.KAL_SqlTabT)){$a=$rR->fetch_row(); $rR->close();}
       if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
        $s='Nummer_'.(int)$a[0].';online'; for($i=1;$i<$nFelder;$i++) $s.=';'.$kal_FeldName[$i]; $s.=';Periodik'; $sKopf=$s;
        while($a=$rR->fetch_row()){
         $s.=NL.$a[0].';'.$a[1]; array_splice($a,1,1);
         for($i=1;$i<$nFelder;$i++) $s.=';'.($kal_FeldType[$i]!='c'&&$kal_FeldType[$i]!='e'?str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i]))):fKalEnCode($a[$i]));
         if(isset($a[$nFelder])&&($w=$a[$nFelder])) $s.=';'.$w;
        }$rR->close();
        if($f=fopen(KAL_Pfad.$ksDaten.$ksTermine,'w')){
         fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert(false,'SQL',''); fSetzKalWert($ksTermine,'Termine',"'"); $bNeu=true;
         $Msg='<p class="admErfo">Die neue Termindatei <i>'.$ksDaten.$ksTermine.'</i> wurde gespeichert.</p>';
         if($ksDaten!=KAL_Daten){ // bei Pfadwechsel Vorgaben umspeichern
          if($f=fopen(KAL_Pfad.$ksDaten.KAL_Vorgaben,'w')){
           $aTmp=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aTmp[$i])?rtrim($aTmp[$i]).NL:NL)); fclose($f);
           fSetzKalWert($ksDaten,'Daten',"'");
          }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden.</p>';}
         }
         if($bNeu){//Nutzertabellen
          if($rR=$DbO->query('SELECT MAX(nr) FROM '.KAL_SqlTabN)){$a=$rR->fetch_row(); $rR->close();}
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' ORDER BY nr')){
           $s='Nummer_'.(int)$a[0].';Session;aktiv'; for($i=2;$i<$nNutzFelder;$i++) $s.=';'.$kal_NutzerFelder[$i];
           while($a=$rR->fetch_row()){
            $b=array_splice($a,1,1); $s.=NL.$a[0].';'.$b[0].';'.$a[1].';'.fKalEnCode($a[2]).';'.$a[3].';'.fKalEnCode($a[4]);
            for($i=5;$i<$nNutzFelder;$i++) $s.=';'.str_replace(';','`,',str_replace('\"','"',$a[$i]));
           }$rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.$ksNutzer,'w')){fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert($ksNutzer,'Nutzer',"'");}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksNutzer.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabN.'</i>!</p>';}
         }
         if(KAL_ZusageSystem&&$bNeu){ //Zusagetabelle
          if($rR=$DbO->query('SELECT MAX(nr) FROM '.KAL_SqlTabZ)){$a=$rR->fetch_row(); $rR->close();}
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' ORDER BY nr')){
           $s='Nummer_'.((int)$a[0]).substr(KAL_ZusageFelder,strpos(KAL_ZusageFelder,';'));
           while($a=$rR->fetch_row()){
            $s.=NL.$a[0].';'.$a[1].';'.$a[2].';'.$a[3].';'.str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[4]))).';'.$a[5].';'.$a[6].';'.$a[7].';'.fKalEnCode($a[8]);
            for($i=9;$i<=$nZusageFelder;$i++) $s.=';'.str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[$i])));
           }$rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.KAL_Zusage,'w')){fwrite($f,rtrim($s).NL); fclose($f);}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.KAL_Zusage.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabZ.'</i>!</p>';}
         }
         if($bNeu){//Erinnerungs- und Benachrichtigungstabellen
          if($rR=$DbO->query('SELECT datum,termin,email FROM '.KAL_SqlTabE.' ORDER BY datum,termin DESC')){//Erinnerungen
           $s='#Datum;Termin;eMail'; while($a=$rR->fetch_row()) $s.=NL.$a[0].';'.$a[1].';'.$a[2]; $rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.$ksErinner,'w')){fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert($ksErinner,'Erinner',"'");}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksErinner.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabE.'</i>!</p>';}
          if($rR=$DbO->query('SELECT termin,email FROM '.KAL_SqlTabB.' ORDER BY termin DESC')){//Benachrichtigungen
           $s='#Termin;eMail'; while($a=$rR->fetch_row()) $s.=NL.$a[0].';'.$a[1]; $rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.$ksBenachr,'w')){fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert($ksBenachr,'Benachr',"'");}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksBenachr.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabB.'</i>!</p>';}
          if($rR=$DbO->query('SELECT email FROM '.KAL_SqlTabM.' ORDER BY id DESC')){//eMail-Adressen
           $s='#eMail'; while($a=$rR->fetch_row()) $s.=NL.$a[0]; $rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.$ksMailAdr,'w')){fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert($ksMailAdr,'MailAdr',"'");}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksMailAdr.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabM.'</i>!</p>';}
          if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' ORDER BY id')){//Admin-Texte
           $s='Nr;Typ;Kennung;Betreff;Inhalt'; while($a=$rR->fetch_row()) $s.=NL.sprintf('%04d',$a[0]).';'.$a[1].';'.$a[2].';'.str_replace(';','`,',str_replace('\"','"',$a[3])).';'.str_replace(';','`,',str_replace("\r\n",'\n ',str_replace('\"','"',$a[4]))); $rR->close();
           if($f=fopen(KAL_Pfad.$ksDaten.$ksAdminTexte,'w')){fwrite($f,rtrim($s).NL); fclose($f); fSetzKalWert($ksAdminTexte,'AdminTexte',"'");}
           else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksAdminTexte.'</i> konnte nicht geschrieben werden.</p>';}
          }else{$bNeu=false; $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabA.'</i>!</p>';}

         }
        }else $Msg='<p class="admFehl">Die neue Termindatei <i>'.$ksDaten.$ksTermine.'</i> konnte nicht geschrieben werden.</p>';
       }else $Msg='<p class="admFehl">Abfragefehler in der MySQL-Tabelle <i>'.KAL_SqlTabT.'</i>!</p>';
       $DbO->close(); $DbO=NULL;
     }else $Msg='<p class="admFehl">Keine MySQL-Verbindung mit den vorliegenden Zugangsdaten!</p>';
    }else{ //Text->Text
     if($ksTermine!=KAL_Termine||$ksDaten!=KAL_Daten){
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine);
      if($f=fopen(KAL_Pfad.$ksDaten.$ksTermine,'w')){
       fwrite($f,trim(str_replace("\r",'',implode('',$aD))).NL); fclose($f); fSetzKalWert($ksTermine,'Termine',"'"); $bNeu=true;
       $Msg='<p class="admErfo">Die neue Termindatei <i>'.$ksDaten.$ksTermine.'</i> wurde gespeichert.</p>';
       if($ksDaten!=KAL_Daten){ //bei Pfadwechsel Vorgaben umspeichern
        if($f=fopen(KAL_Pfad.$ksDaten.KAL_Vorgaben,'w')){
         $aTmp=@file(KAL_Pfad.KAL_Daten.KAL_Vorgaben); for($i=0;$i<$nFelder;$i++) fwrite($f,(isset($aTmp[$i])?rtrim($aTmp[$i]).NL:NL)); fclose($f);
         fSetzKalWert($ksDaten,'Daten',"'");
        }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden.</p>';}
       }
      }else $Msg='<p class="admFehl">Die neue Termindatei <i>'.$ksDaten.$ksTermine.'</i> konnte nicht geschrieben werden.</p>';
     }
     if($ksNutzer!=KAL_Nutzer||($ksDaten!=KAL_Daten&&$bNeu)){ //Benutzerverwaltung
      if($f=fopen(KAL_Pfad.$ksDaten.$ksNutzer,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nNutzerZahl=count($aD);
       if(substr($aD[0],0,7)!='Nummer_'){
        $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5));
        $s='Nummer_'.$nMx.';Session;aktiv'; for($i=2;$i<$nNutzFelder;$i++) $s.=';'.$kal_NutzerFelder[$i]; $aD[0]=$s.NL;
       }
       for($i=0;$i<$nNutzerZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); fSetzKalWert($ksNutzer,'Nutzer',"'");
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Benutzerdatei <i>'.$ksDaten.$ksNutzer.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksNutzer.'</i> konnte nicht geschrieben werden.</p>';}
     }
     if(KAL_ZusageSystem&&$ksDaten!=KAL_Daten&&$bNeu){ //Zusagetabelle
      if($f=fopen(KAL_Pfad.$ksDaten.KAL_Zusage,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nZusageZahl=count($aD);
       if(substr($aD[0],0,7)!='Nummer_'){
        $nMx=0; for($i=1;$i<$nZusageZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5));
        $s='Nummer_'.substr(KAL_ZusageFelder,strpos(KAL_ZusageFelder,';')); $aD[0]=$s.NL;
       }
       for($i=0;$i<$nZusageZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f);
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Zusagedatei <i>'.$ksDaten.KAL_Zusage.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.KAL_Zusage.'</i> konnte nicht geschrieben werden.</p>';}
     }
     if($ksErinner!=KAL_Erinner||($ksDaten!=KAL_Daten&&$bNeu)){ //Erinnerungen
      if($f=fopen(KAL_Pfad.$ksDaten.$ksErinner,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_Erinner); $aD[0]="#Datum;Termin;eMail\n"; $nZahl=count($aD);
       for($i=0;$i<$nZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); fSetzKalWert($ksErinner,'Erinner',"'");
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Erinnerungsdatei <i>'.$ksDaten.$ksErinner.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksErinner.'</i> konnte nicht geschrieben werden.</p>';}
     }
     if($ksBenachr!=KAL_Benachr||($ksDaten!=KAL_Daten&&$bNeu)){ //Benachrichtigungen
      if($f=fopen(KAL_Pfad.$ksDaten.$ksBenachr,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_Benachr); $aD[0]="#Termin;eMail\n"; $nZahl=count($aD);
       for($i=0;$i<$nZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); fSetzKalWert($ksBenachr,'Benachr',"'");
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Benachrichtigungsdatei <i>'.$ksDaten.$ksBenachr.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksBenachr.'</i> konnte nicht geschrieben werden.</p>';}
     }
     if($ksMailAdr!=KAL_MailAdr||($ksDaten!=KAL_Daten&&$bNeu)){ //eMail-Adressen
      if($f=fopen(KAL_Pfad.$ksDaten.$ksMailAdr,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $aD[0]="#eMail\n"; $nZahl=count($aD);
       for($i=0;$i<$nZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); fSetzKalWert($ksMailAdr,'MailAdr',"'");
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Adressdatei <i>'.$ksDaten.$ksMailAdr.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksMailAdr.'</i> konnte nicht geschrieben werden.</p>';}
     }

     if($ksAdminTexte!=KAL_AdminTexte||($ksDaten!=KAL_Daten&&$bNeu)){ //Admin-Texten
      if($f=fopen(KAL_Pfad.$ksDaten.$ksAdminTexte,'w')){
       $aD=@file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $aD[0]="Nr;Typ;Kennung;Betreff;Inhalt\n"; $nZahl=count($aD);
       for($i=0;$i<$nZahl;$i++) if($s=rtrim($aD[$i])) fwrite($f,$s.NL); fclose($f); fSetzKalWert($ksAdminTexte,'AdminTexte',"'");
       $bNeu=true; if(!$Msg) $Msg='<p class="admErfo">Die neue Erinnerungsdatei <i>'.$ksDaten.$ksAdminTexte.'</i> wurde gespeichert.</p>';
      }else{$bNeu=false; $Msg='<p class="admFehl">Die Datei <i>'.$ksDaten.$ksAdminTexte.'</i> konnte nicht geschrieben werden.</p>';}
     }

    }
   }else $Msg='<p class="admFehl">Der Dateiname der Termindatei darf nicht <i>'.KAL_Vorgaben.'</i> lauten!</p>';
  }else $Msg='<p class="admFehl">Speicherpfad und Dateiname der Termindatei dürfen nicht leer sein!</p>';
 }else{ //->SQL
  $ksSqlHost=inpVar('SqlHost'); $ksSqlDaBa=inpVar('SqlDaBa'); $ksSqlUser=inpVar('SqlUser'); $ksSqlPass=inpVar('SqlPass'); $ksSQL=true;
  $ksSqlTabT=inpVar('SqlTabT'); $ksSqlTabN=inpVar('SqlTabN'); $ksSqlTabE=inpVar('SqlTabE'); $ksSqlTabB=inpVar('SqlTabB'); $ksSqlTabM=inpVar('SqlTabM'); $ksSqlTabA=inpVar('SqlTabA');
  $ksDaten=KAL_Daten; $ksTermine=KAL_Termine; $ksNutzer=KAL_Nutzer; $ksErinner=KAL_Erinner; $ksBenachr=KAL_Benachr; $ksMailAdr=KAL_MailAdr; $ksAdminTexte=KAL_AdminTexte;
  $bPwNeu=($ksSqlHost==KAL_SqlHost&&$ksSqlUser==KAL_SqlUser&&$ksSqlPass!=KAL_SqlPass&&$ksSqlDaBa==KAL_SqlDaBa);
  $DbO=@new mysqli($ksSqlHost,$ksSqlUser,$ksSqlPass,$ksSqlDaBa);
  if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else $DbO=NULL;
  if($DbO){
    include('feldtypenInc.php');
    $sF=', online CHAR(1) NOT NULL DEFAULT ""'; for($i=1;$i<$nFelder;$i++) $sF.=', kal_'.$i.' '.$aSql[$kal_FeldType[$i]];
    $sN=', aktiv CHAR(1) NOT NULL DEFAULT "", benutzer CHAR(25) NOT NULL DEFAULT "", passwort VARCHAR(32) NOT NULL DEFAULT "", email VARCHAR(100) NOT NULL DEFAULT ""';
    for($i=5;$i<$nNutzFelder;$i++) $sN.=', dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""';
    if(KAL_SQL){ //SQL->SQL
     $bSqlNeu=($ksSqlHost!=KAL_SqlHost||$ksSqlUser!=KAL_SqlUser||$ksSqlPass!=KAL_SqlPass||$ksSqlDaBa!=KAL_SqlDaBa);
     if($ksSqlTabT!=KAL_SqlTabT||$ksSqlTabN!=KAL_SqlTabN||$ksSqlTabE!=KAL_SqlTabE||$ksSqlTabB!=KAL_SqlTabB||$ksSqlTabM!=KAL_SqlTabM||$ksSqlTabA!=KAL_SqlTabA||$bSqlNeu){
      $DbO->close(); $DbO=NULL; $aD=array(); $aN=array(); $aR=array(); $bGelesen=false;
      $DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa);
      if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else $DbO=NULL;
      if($DbO&&!$bPwNeu){
        if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabT.' ORDER BY kal_1'.($nFelder>2?',kal_2'.($nFelder>3?',kal_3':''):'').',id')){
         while($a=$rR->fetch_row()){
          $s='"'.$a[0].'"'; for($i=1;$i<=$nFelder+1;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aD[]=$s;
         }$rR->close(); $bGelesen=true;
        }else $Msg='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabT.'</i>!</p>';
        if($bGelesen){
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabN.' ORDER BY nr')){
          while($a=$rR->fetch_row()){
           $s='"'.$a[0].'"'; /* array_splice($a,1,1); */ for($i=1;$i<=$nNutzFelder;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aN[]=$s;
          }$rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabN.'</i>!</p>';}
        }
        if(KAL_ZusageSystem&&$bGelesen){ //Zusagen lesen
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabZ.' ORDER BY nr')){
          while($a=$rR->fetch_row()){
           $s='"'.$a[0].'"'; for($i=1;$i<=$nZusageFelder;$i++) $s.=',"'.str_replace('"','\"',$a[$i]).'"'; $aR[]=$s;
          }$rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabZ.'</i>!</p>';}
        }
        if($bGelesen){
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabE.' ORDER BY id')){
          while($a=$rR->fetch_row()) $aE[]='"'.$a[0].'","'.$a[1].'","'.$a[2].'","'.$a[3].'"'; $rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabE.'</i>!</p>';}
        }
        if($bGelesen){
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabB.' ORDER BY id')){
          while($a=$rR->fetch_row()) $aB[]='"'.$a[0].'","'.$a[1].'","'.$a[2].'"'; $rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabB.'</i>!</p>';}
        }
        if($bGelesen){
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabM.' ORDER BY id')){
          while($a=$rR->fetch_row()) $aM[]='"'.$a[0].'","'.$a[1].'"'; $rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabM.'</i>!</p>';}
        }
        if($bGelesen){
         if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' ORDER BY id')){
          while($a=$rR->fetch_row()) $aA[]='"'.sprintf('%04d',$a[0]).'","'.$a[1].'","'.$a[2].'","'.$a[3].'","'.$a[4].'"'; $rR->close();
         }else{$bGelesen=false; $Msg.='<p class="admFehl">Abfragefehler in der bisherigen MySQL-Tabelle <i>'.KAL_SqlHost.':'.KAL_SqlDaBa.'.'.KAL_SqlTabA.'</i>!</p>';}
        }
        $DbO->close(); $DbO=NULL;
      }else $Msg='<p class="admFehl">Keine MySQL-Verbindung zur bisherigen MySQL-Datenquelle!</p>';

      $DbO=@new mysqli($ksSqlHost,$ksSqlUser,$ksSqlPass,$ksSqlDaBa);
      if(!mysqli_connect_errno()){if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);}else $DbO=NULL;
      if($DbO&&!$bPwNeu){
       if($bGelesen&&($ksSqlTabT!=KAL_SqlTabT||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabT); $nSaetze=count($aD); //Termine neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabT.' (id int(11) NOT NULL auto_increment'.$sF.',periodik varchar(20) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Termine"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabT.' VALUES('.$aD[$i].')')) $bGelesen=false;
         if($bGelesen){
          $Msg='<p class="admErfo">Die Termine wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabT.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Termine konnten in die MySQL-Tabelle <i>'.$ksSqlTabT.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabT.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bGelesen&&($ksSqlTabN!=KAL_SqlTabN||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabN); $nSaetze=count($aN); //Nutzer neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabN.' (nr int(11) NOT NULL auto_increment, session CHAR(8) NOT NULL DEFAULT ""'.$sN.', PRIMARY KEY (nr)) COMMENT="Kalender-Benutzer"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabN.' VALUES('.$aN[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die Benutzer wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabN.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Benutzer konnten in die MySQL-Tabelle <i>'.$ksSqlTabN.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabN.'</i> konnte nicht angelegt werden!</p>';
       }
       if(KAL_ZusageSystem&&$bGelesen&&$bSqlNeu){
        $DbO->query('DROP TABLE IF EXISTS '.KAL_SqlTabZ); $nSaetze=count($aR); //Zusagen neu
        $s=''; for($i=9;$i<=$nZusageFelder;$i++) $s.='dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT "",';
        if($DbO->query('CREATE TABLE '.KAL_SqlTabZ.' (nr INT(10) NOT NULL AUTO_INCREMENT,termin INT(10) NOT NULL DEFAULT "0",datum CHAR(10) NOT NULL DEFAULT "",zeit CHAR(5) NOT NULL DEFAULT "",veranstaltung VARCHAR(255) NOT NULL DEFAULT "",buchung CHAR(16) NOT NULL DEFAULT "",aktiv CHAR(1) NOT NULL DEFAULT "",benutzer VARCHAR(8) NOT NULL DEFAULT "",email VARCHAR(128) NOT NULL DEFAULT "",'.$s.' PRIMARY KEY (nr)) COMMENT="Kalender-Reservierungen"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' VALUES('.$aR[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die Zusagen wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.KAL_SqlTabZ.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Zusagen konnten in die MySQL-Tabelle <i>'.KAL_SqlTabZ.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.KAL_SqlTabZ.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bGelesen&&($ksSqlTabE!=KAL_SqlTabE||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabE); $nSaetze=count($aE); //Erinnerungen neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabE.' (id int(11) NOT NULL auto_increment, datum varchar(10) NOT NULL DEFAULT "", termin int(11) NOT NULL DEFAULT "0", email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Erinnerungen"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabE.' VALUES('.$aE[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die Erinnerungen wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabE.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Erinnerungen konnten in die MySQL-Tabelle <i>'.$ksSqlTabE.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabE.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bGelesen&&($ksSqlTabB!=KAL_SqlTabB||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabB); $nSaetze=count($aB); //Benachrichtigungen neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabB.' (id int(11) NOT NULL auto_increment,termin int(11) NOT NULL DEFAULT "0",email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Benachrichtigungen"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabB.' VALUES('.$aB[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die Benachrichtigungen wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabB.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Benachrichtigungen konnten in die MySQL-Tabelle <i>'.$ksSqlTabB.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabB.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bGelesen&&($ksSqlTabM!=KAL_SqlTabM||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabM); $nSaetze=count($aM); //eMail-Adressen neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabM.' (id int(11) NOT NULL auto_increment,email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Mailadressen"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabM.' VALUES('.$aM[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die eMail-Adressen wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabM.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle eMail-Adressen konnten in die MySQL-Tabelle <i>'.$ksSqlTabM.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabM.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bGelesen&&($ksSqlTabA!=KAL_SqlTabA||$bSqlNeu)){
        $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabA); $nSaetze=count($aA); //AdminTexte neu
        if($DbO->query('CREATE TABLE '.$ksSqlTabA.' (id int(11) NOT NULL auto_increment,typ char(1) NOT NULL DEFAULT "",kennung varchar(31) NOT NULL DEFAULT "",betreff varchar(255) NOT NULL DEFAULT "",inhalt text NOT NULL, PRIMARY KEY (id)) COMMENT="Kalender-AdminTexte"')){
         for($i=0;$i<$nSaetze;$i++) if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabA.' VALUES('.$aA[$i].')')) $bGelesen=false;
         if($bGelesen){
          if(!$Msg) $Msg='<p class="admErfo">Die Admin-Texte wurden in die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabA.'</i> übernommen.</p>';
         }else $Msg='<p class="admFehl">Nicht alle Admin-Texte konnten in die MySQL-Tabelle <i>'.$ksSqlTabA.'</i> übernommen werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabA.'</i> konnte nicht angelegt werden!</p>';
       }
       if($bNeu=$bGelesen){
        fSetzKalWert($ksSqlHost,'SqlHost',"'"); fSetzKalWert($ksSqlDaBa,'SqlDaBa',"'"); fSetzKalWert($ksSqlUser,'SqlUser',"'");
        fSetzKalWert($ksSqlPass,'SqlPass',"'"); fSetzKalWert($ksSqlTabT,'SqlTabT',"'"); fSetzKalWert($ksSqlTabN,'SqlTabN',"'");
        fSetzKalWert($ksSqlTabE,'SqlTabE',"'"); fSetzKalWert($ksSqlTabB,'SqlTabB',"'"); fSetzKalWert($ksSqlTabM,'SqlTabM',"'");
       }
      }elseif($DbO&&$bPwNeu){
       $Msg='<p class="admErfo">Das neue Passwort wurde akzeptiert und gespeichert.</p>'; fSetzKalWert($ksSqlPass,'SqlPass',"'"); $bNeu=true;
      }else $Msg='<p class="admFehl">Kein Zugriff auf die neue Datenbank <i>'.$ksSqlHost.':'.$ksSqlDaBa.'</i>!</p>';
     } //keine Änderung
    }else{ //Text->SQL
     $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabT); $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabN);
     $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabE); $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabB);
     $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabM); $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabA);
     if($DbO->query('CREATE TABLE '.$ksSqlTabT.' (id int(11) NOT NULL auto_increment'.$sF.',periodik varchar(20) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Termine"')){
      $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $bGelesen=true;
      for($i=1;$i<$nSaetze;$i++){
       $a=explode(';',rtrim($aD[$i]));
       if(isset($a[1])){
        $s='"'.$a[0].'","'.$a[1].'"'; array_splice($a,1,1);
        for($j=1;$j<$nFelder;$j++) $s.=',"'.($kal_FeldType[$j]!='c'&&$kal_FeldType[$j]!='e'?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):fKalDeCode($a[$j])).'"';
        $s.=',"'.(isset($a[$nFelder])&&$a[$nFelder]>''?$a[$nFelder]:'').'"';
        if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabT.' VALUES('.$s.')')) $bGelesen=false;
      }}
      if($bGelesen){ //Nutzerdaten
       if($DbO->query('CREATE TABLE '.$ksSqlTabN.' (nr int(11) NOT NULL auto_increment, session CHAR(8) NOT NULL DEFAULT ""'.$sN.', PRIMARY KEY (nr)) COMMENT="Kalender-Benutzer"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); $b=array_splice($a,1,1); $s='"'.$a[0].'","'.$b[0].'","'.$a[1].'","'.fKalDeCode($a[2]).'","'.$a[3].'","'.fKalDeCode($a[4]).'"';
         for($j=5;$j<$nNutzFelder;$j++) $s.=',"'.str_replace('"','\"',str_replace('`,',';',$a[$j])).'"';
         if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabN.' VALUES('.$s.')')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabN.'</i> konnte nicht angelegt werden!</p>';}
      }
      if(KAL_ZusageSystem&&$bGelesen){ //Zusagedaten
       $s=''; for($i=9;$i<=$nZusageFelder;$i++) $s.='dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT "",'; $DbO->query('DROP TABLE IF EXISTS '.KAL_SqlTabZ);
       if($DbO->query('CREATE TABLE '.KAL_SqlTabZ.' (nr INT(10) NOT NULL AUTO_INCREMENT,termin INT(10) NOT NULL DEFAULT "0",datum CHAR(10) NOT NULL DEFAULT "",zeit CHAR(5) NOT NULL DEFAULT "",veranstaltung VARCHAR(255) NOT NULL DEFAULT "",buchung CHAR(16) NOT NULL DEFAULT "",aktiv CHAR(1) NOT NULL DEFAULT "",benutzer VARCHAR(8) NOT NULL DEFAULT "",email VARCHAR(128) NOT NULL DEFAULT "",'.$s.' PRIMARY KEY (nr)) COMMENT="Kalender-Reservierungen"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Zusage); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i]));
         $s='"'.$a[0].'","'.$a[1].'","'.$a[2].'","'.$a[3].'","'.str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[4]))).'","'.$a[5].'","'.$a[6].'","'.$a[7].'","'.fKalDeCode($a[8]).'"';
         for($j=9;$j<=$nZusageFelder;$j++) $s.=',"'.(isset($a[$j])?str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[$j]))):'').'"';
         if(!$DbO->query('INSERT IGNORE INTO '.KAL_SqlTabZ.' VALUES('.$s.')')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.KAL_SqlTabZ.'</i> konnte nicht angelegt werden!</p>';}
      }
      if($bGelesen){
       if($DbO->query('CREATE TABLE '.$ksSqlTabE.' (id int(11) NOT NULL auto_increment, datum varchar(10) NOT NULL DEFAULT "", termin int(11) NOT NULL DEFAULT "0", email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Erinnerungen"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Erinner); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabE.' VALUES('.$i.',"'.$a[0].'","'.$a[1].'","'.$a[2].'")')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabE.'</i> konnte nicht angelegt werden!</p>';}
       if($DbO->query('CREATE TABLE '.$ksSqlTabB.' (id int(11) NOT NULL auto_increment, termin int(11) NOT NULL DEFAULT "0", email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Benachrichtigungen"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_Benachr); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabB.' VALUES('.$i.',"'.$a[0].'","'.$a[1].'")')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabB.'</i> konnte nicht angelegt werden!</p>';}
       if($DbO->query('CREATE TABLE '.$ksSqlTabM.' (id int(11) NOT NULL auto_increment, email varchar(127) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Mailadressen"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_MailAdr); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $s=rtrim($aD[$i]); if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabM.' VALUES('.$i.',"'.$s.'")')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabM.'</i> konnte nicht angelegt werden!</p>';}
       if($DbO->query('CREATE TABLE '.$ksSqlTabA.' (id int(11) NOT NULL auto_increment,typ char(1) NOT NULL DEFAULT "",kennung varchar(31) NOT NULL DEFAULT "",betreff varchar(255) NOT NULL DEFAULT "",inhalt text NOT NULL, PRIMARY KEY (id)) COMMENT="Kalender-AdminTexte"')){
        $aD=file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $nSaetze=count($aD);
        for($i=1;$i<$nSaetze;$i++){
         $a=explode(';',rtrim($aD[$i])); if(!$DbO->query('INSERT IGNORE INTO '.$ksSqlTabA.' VALUES('.$i.',"'.$a[1].'","'.$a[2].'","'.str_replace('`,',';',$a[3]).'","'.str_replace('"','\"',str_replace('\n ',"\r\n",str_replace('`,',';',$a[4]))).'")')) $bGelesen=false;
        }
       }else{$bGelesen=false; $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabA.'</i> konnte nicht angelegt werden!</p>';}
       if($bGelesen){
        $Msg='<p class="admErfo">Die Termine wurden in die MySQL-Tabelle <i>'.$ksSqlTabT.'</i> übernommen.</p>';
        fSetzKalWert($ksSqlHost,'SqlHost',"'"); fSetzKalWert($ksSqlDaBa,'SqlDaBa',"'"); fSetzKalWert($ksSqlUser,'SqlUser',"'");
        fSetzKalWert($ksSqlPass,'SqlPass',"'"); fSetzKalWert($ksSqlTabT,'SqlTabT',"'"); fSetzKalWert($ksSqlTabN,'SqlTabN',"'");
        fSetzKalWert($ksSqlTabE,'SqlTabE',"'"); fSetzKalWert($ksSqlTabB,'SqlTabB',"'"); fSetzKalWert($ksSqlTabM,'SqlTabM',"'"); fSetzKalWert($ksSqlTabA,'SqlTabA',"'");
        fSetzKalWert(true,'SQL',''); $bNeu=true;
       }else $Msg.='<p class="admFehl">Nicht alle Daten konnten in die MySQL-Tabelle <i>'.$ksSqlTabE.'</i>,<i>'.$ksSqlTabB.'</i> bzw. <i>'.$ksSqlTabM.'</i> übernommen werden!</p>';
      }else $Msg.='<p class="admFehl">Nicht alle Daten konnten in die MySQL-Tabelle <i>'.$ksSqlTabT.'</i>, <i>'.$ksSqlTabN.'</i>'.(KAL_ZusageSystem?' bzw. <i>'.KAL_SqlTabZ.'</i>':'').' übernommen werden!</p>';
     }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlHost.':'.$ksSqlDaBa.'.'.$ksSqlTabT.'</i> konnte nicht angelegt werden!</p>';
    }
    $DbO->close(); $DbO=NULL;
  }else{
   if($bPwNeu) $Msg='<p class="admFehl">Keine MySQL-Verbindung mit dem angegebenen Passwort <i>'.$ksSqlPass.'</i>!</p>';
   else $Msg='<p class="admFehl">Keine MySQL-Verbindung mit den angegebenen Zugangsdaten!</p>';
  }
 }
 if(!$ksBilder=inpVar('Bilder')) $ksBilder='bilder';
 if(substr($ksBilder,0,1)=='/') $ksBilder=substr($ksBilder,1); if(substr($ksBilder,-1,1)!='/') $ksBilder.='/';
 if($ksBilder!=$ksDaten&&$ksBilder!=KAL_CaptchaPfad&&$ksBilder!='grafik/'){
  if(is_writable(KAL_Pfad.substr($ksBilder,0,-1))){
   if(fSetzKalWert($ksBilder,'Bilder',"'")){$bNeu=true; $bBldNeu=true;}
  }else $Msg='<p class="admFehl">Der vorgesehene Bilder-Ordner ist nicht beschreibbar!</p>';
 }else $Msg='<p class="admFehl">Der vorgesehene Name für den Bilder-Ordner ist ungültig!</p>';

 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   if(!$Msg) $Msg='<p class="admErfo">Die Einstellungen zur Datenbasis wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
 }else if(!$Msg) $Msg='<p class="admMeld">Die Einstellungen zur Datenbasis bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen zur Datenbasis des Kalender-Scripts.</p>';
echo $Msg.NL;
$bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
?>

<form action="konfDaten.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Kalender-Script speichert die Termindaten in tabellarischer Form auf dem Webserver,
um daraus bei jeder Anforderung dynamisch eine Ausgabeseite zu generieren.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenbasis</td>
 <td>
  <table border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td width="130" valign="top"><input class="admRadio" type="radio" name="Sql" value="0"<?php if(!$ksSQL) echo ' checked="checked"';?> /> Textdatei</td>
    <td style="padding-bottom:8px;">Standardmäßig werden zum Speichern einfache Textdateien verwendet.
Diese Methode ist schnell und ressourcenschonend.
Allerdings muss das Script dazu die Berechtigung besitzen, in eine solche Termindatei bzw. Benutzerdatei schreiben zu dürfen.
Eine solche Schreibberechtigung stellt auf einigen wenigen ungeschickt konfigurierten Servern
unter extrem seltenen Bedingungen ein gewisses Sicherheitsrisiko dar.</td>
   </tr>
   <tr>
    <td width="130" valign="top"><input class="admRadio" type="radio" name="Sql" value="1"<?php if($ksSQL) echo ' checked="checked"';?> /> MySQL-Tabelle</td>
    <td>Abweichend davon können die Daten auch in Tabellen einer MySQL-Datenbank gepeichert werden.
Diese Methode ist wesentlich ressourcenverbrauchender solange die Termindatei nur wenige Hundert Termine enthält.
In Fällen, da mehrere Tausend Termine im Kalender eingetragen sind bzw.
wo Termine durch mehrere Benutzer quasi gleichzeitig eingetragen und häufig geändert werden
kann die MySQL-Datenquelle hingegen Geschwindigkeits- oder Sicherheitsvorteile bringen.</td>
   </tr>
  </table></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini">
  <u>Hinweis</u>: Wenn Sie die Datenbasis umschalten werden die Termine
  aus der momentanen Datenquelle mit der momentanen Terminstruktur
  auf den neuen Datenspeicher umgeschrieben.
  Etwaig vorhandene ältere Terminspeicher aus früheren Umschaltungen werden überschrieben.
  Gleiches gilt für die Benutzerdaten, falls die Benutzerverwaltung aktiv ist.<br>
  <u>Hinweis</u>: Solange Sie noch Ihre optimale Terminstruktur suchen und an ihr &quot;herumbasteln&quot;
  sollten Sie die Datenbasis noch nicht auf MySQL-Datenbank umstellen sondern es vorerst bei Text-Datenbasis belassen.
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in <i>Textdateien</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Speicherordner</td>
 <td><input type="text" name="Daten" value="<?php echo(substr($ksDaten,-1,1)=='/'?substr($ksDaten,0,-1):$ksDaten)?>" style="width:250px;<?php if($ksSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>daten</i>
 <div class="admMini">Unterordner, relativ zum Hauptordner des Kalender-Scripts. Der Ordner muss bereits existieren. <a href="<?php echo ADM_Hilfe?>LiesMich.htm#1.2" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termindatei</td>
 <td><input type="text" name="Termine" value="<?php echo $ksTermine?>" style="width:150px;<?php if($ksSQL) echo 'color:#8C8C8C;'?>" /> &nbsp; Empfehlung: <i>termine.txt</i>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdatei</td>
 <td><input type="text" name="Nutzer" value="<?php echo $ksNutzer?>" style="width:150px;<?php if($ksSQL||!$bNutzer) echo 'color:#8C8C8C;'?>" /> &nbsp; Empfehlung: <i>Wählen Sie einen nicht zu erratenden Dateinamen!</i>
 <div class="admMini">Wenn Ihr Kalender mit Benutzerverwaltung arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungsdatei<div style="margin-top:3px;margin-bottom:3px;">Benach-<br />richtigungsdatei</div>und Adressdatei</td>
 <td><div style="width:155px;float:left;"><input type="text" name="Erinner" value="<?php echo $ksErinner?>" style="width:150px;<?php if($ksSQL||(KAL_ListenErinn<=0&&KAL_DetailErinn<=0)) echo 'color:#8C8C8C;'?>" /><br />
 <input type="text" name="Benachr" value="<?php echo $ksBenachr?>" style="width:150px;<?php if($ksSQL||(KAL_ListenBenachr<=0&&KAL_DetailBenachr<=0)) echo 'color:#8C8C8C;'?>" /><br />
 <input type="text" name="MailAdr" value="<?php echo $ksMailAdr?>" style="width:150px;<?php if($ksSQL||(KAL_ListenErinn<=0&&KAL_DetailErinn<=0&&KAL_ListenBenachr<=0&&KAL_DetailBenachr<=0)) echo 'color:#8C8C8C;'?>" /></div>
 <div style="margin-left:160px;">Empfehlung: <i>Wählen Sie nicht zu erratende Namen<br />oder schützen Sie den Speicherordner vor neugierigen Zugriffen!</i>
 <div class="admMini">Wenn Ihr Kalender mit Erinnerungsservice oder Benachrichtigungsservice arbeiten soll, muss das PHP-Script Schreibberechtigung auf die angegebenen Dateien im angegebenen Speicherordner besitzen.</div></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Administrator-<br>E-Mailtexte</td>
 <td><input type="text" name="AdminTexte" value="<?php echo $ksAdminTexte?>" style="width:150px;<?php if($ksSQL) echo 'color:#8C8C8C;'?>" /> &nbsp; Empfehlung: <i>texteAdmin.txt</i>
 <div class="admMini">Das PHP-Script muss Schreibberechtigung auf die angegebene Datei im angegebenen Speicherordner besitzen.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admMini"><u>Warnung</u>: Im Datenordner vorhandene Dateien gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für die etwaige Datenspeicherung in <i>MySQL-Tabellen</i> gelten die folgenden Einstellungen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Hostname</td>
 <td><input type="text" name="SqlHost" value="<?php echo $ksSqlHost?>" style="width:250px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> meist: <i>localhost</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Datenbankname</td>
 <td><input type="text" name="SqlDaBa" value="<?php echo $ksSqlDaBa?>" style="width:120px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> (die Datenbank muss unter diesem Namen bereits vorhanden sein)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Benutzername</td>
 <td><input type="text" name="SqlUser" value="<?php echo $ksSqlUser?>" style="width:120px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> (passend zu obiger Datenbank)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Passwort</td>
 <td><input type="password" name="SqlPass" value="<?php echo $ksSqlPass?>" style="width:120px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> (passend zu obiger Datenbank)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Tabellennamen</td>
 <td><input type="text" name="SqlTabT" value="<?php echo $ksSqlTabT?>" style="width:120px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_termine</i> für die Termintabelle
 <div><input type="text" name="SqlTabN" value="<?php echo $ksSqlTabN?>" style="width:120px;<?php if(!$ksSQL||!$bNutzer) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_nutzer</i> für die Benutzertabelle</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-<br />Zusatztabellen</td>
 <td><input type="text" name="SqlTabE" value="<?php echo $ksSqlTabE?>" style="width:120px;<?php if(!$ksSQL||(KAL_ListenErinn<=0&&KAL_DetailErinn<=0)) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_erinner</i> für die Erinnerungstabelle
 <div><input type="text" name="SqlTabB" value="<?php echo $ksSqlTabB?>" style="width:120px;<?php if(!$ksSQL||(KAL_ListenBenachr<=0&&KAL_DetailBenachr<=0)) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_benachr</i> für die Benachrichtigungstabelle</div>
 <div><input type="text" name="SqlTabM" value="<?php echo $ksSqlTabM?>" style="width:120px;<?php if(!$ksSQL||(KAL_ListenErinn<=0&&KAL_DetailErinn<=0&&KAL_ListenBenachr<=0&&KAL_DetailBenachr<=0)) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_mailadr</i> für die E-Mail-Adressen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">MySQL-Admin-<br />Textetabelle</td>
 <td><input type="text" name="SqlTabA" value="<?php echo $ksSqlTabA?>" style="width:120px;<?php if(!$ksSQL) echo 'color:#8C8C8C;'?>" /> Empfehlung: <i>kal_texteadmin</i> für die Administrator-E-Mailtexte</td>
</tr>

<tr class="admTabl"><td class="admMini" colspan="2"><u>Warnung</u>: In der Datenbank vorhandene Tabellen gleichen Namens werden ohne Rückfrage überschrieben!</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls Ihr Kalender mit Bildern und/oder Dateianhängen arbeitet kann der Speicherordner für Bilder/Anhänge verlegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bilder-Ordner</td>
 <td><input type="text" name="Bilder" value="<?php echo(substr($ksBilder,-1,1)=='/'?substr($ksBilder,0,-1):$ksBilder)?>" style="width:250px;" /> Empfehlung: <i>bilder</i>   &nbsp; <span class="admMini">(der Ordner muss bereits existieren)</span></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
if($bBldNeu){
 if($f=opendir(KAL_Pfad.KAL_Bilder)){
  $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.') $a[]=$s; closedir($f);
  foreach($a as $s) @copy(KAL_Pfad.KAL_Bilder.$s,KAL_Pfad.$ksBilder.$s);
 }
}

echo fSeitenFuss();

function inpVar($Var){return (isset($_POST[$Var])?str_replace(' ','_',str_replace("'",'',str_replace('"','',stripslashes(trim($_POST[$Var]))))):'');}
?>