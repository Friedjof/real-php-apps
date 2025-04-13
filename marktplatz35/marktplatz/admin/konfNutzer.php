<?php
global $nSegNo,$sSegNo,$sSegNam;
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzereinstellungen','','KNz');

$aNF=explode(';',MP_NutzerFelder); $aNP=explode(';',MP_NutzerPflicht); array_splice($aNF,1,1); array_splice($aNP,1,1); $nFelder=count($aNF);
$bNutzer=MP_NListeAnders||MP_NDetailAnders||MP_NEingabeAnders||MP_NVerstecktSehen;
if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $Meld='Kontrollieren oder �ndern Sie die Einstellungen f�r das Formular der Benutzeranmeldung. <a href="'.AM_Hilfe.'LiesMich.htm#2.13" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a>'; $MTyp='Meld';
 $mpTxNutzerLogin=MP_TxNutzerLogin; $mpTxNutzerNamePass=MP_TxNutzerNamePass; $mpTxNutzerFalsch=MP_TxNutzerFalsch;
 $mpTxNutzerOK=MP_TxNutzerOK; $mpTxNutzerLogout=MP_TxNutzerLogout;
 $mpTxNutzerPruefe=MP_TxNutzerPruefe; $mpTxNutzerGeaendert=MP_TxNutzerGeaendert; $mpSessionZeit=MP_SessionZeit;
 $mpTxEingabeFehl=MP_TxEingabeFehl; $mpTxNutzerVergeben=MP_TxNutzerVergeben; $mpTxNutzerNeu=MP_TxNutzerNeu;
 $mpTxNutzerNameMail=MP_TxNutzerNameMail; $mpTxNutzerSend=MP_TxNutzerSend; $mpTxNutzerDatBtr=MP_TxNutzerDatBtr; $mpTxNutzerDaten=MP_TxNutzerDaten;
 $mpTxAktivieren=MP_TxAktivieren; $mpTxAktiviert=MP_TxAktiviert; $mpTxAktivFehl=MP_TxAktivFehl;
 $mpNutzerNeuErlaubt=MP_NutzerNeuErlaubt; $mpPasswortSenden=MP_PasswortSenden; $mpFreischaltWin=MP_FreischaltWin; $mpFreischaltAdmin=MP_FreischaltAdmin;
 $mpNutzerNeuMail=MP_NutzerNeuMail; $mpTxNutzerNeuBtr=MP_TxNutzerNeuBtr; $mpTxNutzerNeuTxt=MP_TxNutzerNeuTxt;
 $mpNutzerNeuAdmMail=MP_NutzerNeuAdmMail; $mpTxNutzNeuAdmBtr=MP_TxNutzNeuAdmBtr; $mpTxNutzNeuAdmTxt=MP_TxNutzNeuAdmTxt;
 $mpNutzerAktivMail=MP_NutzerAktivMail; $mpTxNutzerAktivBtr=MP_TxNutzerAktivBtr; $mpTxNutzerAktivTxt=MP_TxNutzerAktivTxt;
 $mpNutzerLoeschen=MP_NutzerLoeschen; $mpTxNutzerLschMeld=MP_TxNutzerLschMeld; $mpTxNutzerLschBtrN=MP_TxNutzerLschBtrN; $mpTxNutzerLschTxtN=MP_TxNutzerLschTxtN; $mpTxNutzerLschBtrA=MP_TxNutzerLschBtrA; $mpTxNutzerLschTxtA=MP_TxNutzerLschTxtA;

 $mpLoginCaptcha=MP_LoginCaptcha; $mpTxGuthaben=MP_TxGuthaben; $mpGuthabenNeu=MP_GuthabenNeu;
 $mpNVerstecktSehen=MP_NVerstecktSehen; $mpNListeAnders=MP_NListeAnders; $mpNDetailAnders=MP_NDetailAnders;
 $mpNEingabeLogin=MP_NEingabeLogin; $mpNAendernFremde=MP_NAendernFremde; $mpNEingabeAnders=MP_NEingabeAnders;
 $mpTxAgbFeld=MP_TxAgbFeld; $mpTxAgbText=MP_TxAgbText; $mpAgbLink=MP_AgbLink;
 $mpNutzerDSE1=MP_NutzerDSE1; $mpNutzerDSE2=MP_NutzerDSE2;
 $mpAgbPopup=MP_AgbPopup; $mpAgbBreit=MP_AgbBreit; $mpAgbHoch=MP_AgbHoch; $mpAgbZiel=MP_AgbZiel;
 $mpWarnFristNeu=MP_WarnFristNeu; $mpTxWarnen=MP_TxWarnen; $mpTxWarnenBtr=MP_TxWarnenBtr; $mpTxWarnenTxt=MP_TxWarnenTxt;
}else{//POST
 $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNeu=false;
 $nDatenFeldZahl=max((int)txtVar('DatenFeldZahl'),4);
 $aNeu=array('Nummer','aktiv'); $aPfl=array(0,0,1,1,1);
 for($i=2;$i<=$nDatenFeldZahl;$i++){$aNeu[$i]=str_replace(';','`,',txtVar('F'.$i)); if($i>4) $aPfl[$i]=(isset($_POST['P'.$i])?'1':'0');}
 $s=txtVar('TxNutzerLogin'); if(fSetzMPWert($s,'TxNutzerLogin','"')) $bNeu=true;
 $s=txtVar('TxNutzerNamePass'); if(fSetzMPWert($s,'TxNutzerNamePass','"')) $bNeu=true;
 $s=txtVar('TxNutzerFalsch'); if(fSetzMPWert($s,'TxNutzerFalsch','"')) $bNeu=true;
 $s=txtVar('TxNutzerOK'); if(fSetzMPWert($s,'TxNutzerOK','"')) $bNeu=true;
 $s=txtVar('TxNutzerLogout'); if(fSetzMPWert($s,'TxNutzerLogout','"')) $bNeu=true;
 $s=min(max(txtVar('SessionZeit'),5),300); if(fSetzMPWert($s,'SessionZeit','')) $bNeu=true;
 $s=txtVar('TxNutzerPruefe'); if(fSetzMPWert($s,'TxNutzerPruefe','"')) $bNeu=true;
 $s=txtVar('TxNutzerGeaendert'); if(fSetzMPWert($s,'TxNutzerGeaendert','"')) $bNeu=true;
 $s=txtVar('TxEingabeFehl'); if(fSetzMPWert($s,'TxEingabeFehl','"')) $bNeu=true;
 $s=txtVar('TxNutzerVergeben'); if(fSetzMPWert($s,'TxNutzerVergeben','"')) $bNeu=true;
 $s=txtVar('TxNutzerNeu'); if(fSetzMPWert($s,'TxNutzerNeu','"')) $bNeu=true;
 $s=txtVar('TxNutzerNameMail'); if(fSetzMPWert($s,'TxNutzerNameMail','"')) $bNeu=true;
 $s=txtVar('TxNutzerSend'); if(fSetzMPWert($s,'TxNutzerSend','"')) $bNeu=true;
 $s=txtVar('TxNutzerDatBtr'); if(fSetzMPWert($s,'TxNutzerDatBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerDaten')))); if(fSetzMPWert($s,'TxNutzerDaten',"'")) $bNeu=true;
 $s=txtVar('TxAktivieren'); if(fSetzMPWert($s,'TxAktivieren','"')) $bNeu=true;
 $s=txtVar('TxAktiviert'); if(fSetzMPWert($s,'TxAktiviert','"')) $bNeu=true;
 $s=txtVar('TxAktivFehl'); if(fSetzMPWert($s,'TxAktivFehl','"')) $bNeu=true;
 $s=(int)txtVar('NutzerNeuErlaubt'); if(fSetzMPWert(($s?true:false),'NutzerNeuErlaubt','')) $bNeu=true;
 $s=(int)txtVar('PasswortSenden'); if(fSetzMPWert(($s?true:false),'PasswortSenden','')) $bNeu=true;
 $s=txtVar('FreischaltWin'); if(fSetzMPWert($s,'FreischaltWin',"'")) $bNeu=true;
 $s=(int)txtVar('FreischaltAdmin'); if(fSetzMPWert(($s&&strpos('x'.$_POST['TxNutzerNeuTxt'],'#L')?true:false),'FreischaltAdmin','')) $bNeu=true;
 $s=(int)txtVar('NutzerNeuMail'); if(fSetzMPWert(($s?true:false),'NutzerNeuMail','')) $bNeu=true;
 $s=txtVar('TxNutzerNeuBtr'); if(fSetzMPWert($s,'TxNutzerNeuBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerNeuTxt')))); if(fSetzMPWert($s,'TxNutzerNeuTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerNeuAdmMail'); if(fSetzMPWert(($s?true:false),'NutzerNeuAdmMail','')) $bNeu=true;
 $s=txtVar('TxNutzNeuAdmBtr'); if(fSetzMPWert($s,'TxNutzNeuAdmBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzNeuAdmTxt')))); if(fSetzMPWert($s,'TxNutzNeuAdmTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerAktivMail'); if(fSetzMPWert(($s?true:false),'NutzerAktivMail','')) $bNeu=true;
 $s=txtVar('TxNutzerAktivBtr'); if(fSetzMPWert($s,'TxNutzerAktivBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerAktivTxt')))); if(fSetzMPWert($s,'TxNutzerAktivTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerLoeschen'); if(fSetzMPWert($s,'NutzerLoeschen','')) $bNeu=true;
 $s=txtVar('TxNutzerLschMeld'); if(fSetzMPWert($s,'TxNutzerLschMeld','"')) $bNeu=true;
 $s=txtVar('TxNutzerLschBtrN'); if(fSetzMPWert($s,'TxNutzerLschBtrN','"')) $bNeu=true;
 $s=txtVar('TxNutzerLschBtrA'); if(fSetzMPWert($s,'TxNutzerLschBtrA','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerLschTxtN')))); if(fSetzMPWert($s,'TxNutzerLschTxtN',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerLschTxtA')))); if(fSetzMPWert($s,'TxNutzerLschTxtA',"'")) $bNeu=true;
 $s=txtVar('TxGuthaben'); if(fSetzMPWert($s,'TxGuthaben',"'")) $bNeu=true;
 $s=min(max((int)txtVar('GuthabenNeu'),0),9999); if(fSetzMPWert(sprintf('%0d',$s),'GuthabenNeu','')) $bNeu=true;
 $v=(int)txtVar('LoginCaptcha'); if(fSetzMPWert(($v?true:false),'LoginCaptcha','')) $bNeu=true;
 $v=(int)txtVar('NVerstecktSehen');if(fSetzMPWert(($v?true:false),'NVerstecktSehen','')) $bNeu=true;
 $v=(int)txtVar('NListeAnders'); if(fSetzMPWert(($v?true:false),'NListeAnders','')) $bNeu=true;
 $v=(int)txtVar('NDetailAnders');if(fSetzMPWert(($v?true:false),'NDetailAnders','')) $bNeu=true;
 $v=(int)txtVar('NEingabeLogin');if(fSetzMPWert(($v?true:false),'NEingabeLogin','')) $bNeu=true;
 $v=(int)txtVar('NAendernFremde'); if(fSetzMPWert(($v?true:false),'NAendernFremde','')) $bNeu=true;
 $v=(int)txtVar('NEingabeAnders'); if(fSetzMPWert(($v?true:false),'NEingabeAnders','')) $bNeu=true;
 $v=txtVar('NutzerDSE1'); if(fSetzMPWert(($v?true:false),'NutzerDSE1','')) $bNeu=true;
 $v=txtVar('NutzerDSE2'); if(fSetzMPWert(($v?true:false),'NutzerDSE2','')) $bNeu=true;
 $s=txtVar('TxAgbFeld'); if(fSetzMPWert($s,'TxAgbFeld','"')) $bNeu=true;
 $s=txtVar('TxAgbText'); if(fSetzMPWert($s,'TxAgbText','"')) $bNeu=true;
 $s=str_replace("'",'',txtVar('AgbLink')); if(fSetzMPWert($s,'AgbLink',"'")) $bNeu=true;
 $s=(int)txtVar('AgbPopup'); if(fSetzMPWert(($s?true:false),'AgbPopup','')) $bNeu=true;
 $s=min(max(txtVar('AgbBreit'),50),1999); if(fSetzMPWert($s,'AgbBreit','')) $bNeu=true;
 $s=min(max(txtVar('AgbHoch' ),50),1999); if(fSetzMPWert($s,'AgbHoch', '')) $bNeu=true;
 $s=str_replace("'",'',txtVar('AgbZiel')); if(fSetzMPWert($s,'AgbZiel',"'")) $bNeu=true;
 $s=min(max((int)txtVar('WarnFristNeu'),1),99); if(fSetzMPWert($s,'WarnFristNeu','')) $bNeu=true;
 $s=str_replace("'",'',txtVar('TxWarnen')); if(fSetzMPWert($s,'TxWarnen',"'")) $bNeu=true;
 $s=txtVar('TxWarnenBtr'); if(fSetzMPWert($s,'TxWarnenBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxWarnenTxt')))); if(fSetzMPWert($s,'TxWarnenTxt',"'")) $bNeu=true;
 if($bNeu||$aNeu!=$aNF||$aPfl!=$aNP){ //Speichern
  if($nDatenFeldZahl<AM_NutzerFelder) fSetzAdmWert($nDatenFeldZahl,'NutzerFelder',''); //Korrektur
  $aTmp=$aNeu; array_splice($aTmp,1,0,'Session'); fSetzMPWert(implode(';',$aTmp),'NutzerFelder',"'"); //fSetzArray(($aTmp),'NutzerFelder','"');
  $aTmp=$aPfl; array_splice($aTmp,1,0,0); fSetzMPWert(implode(';',$aTmp),'NutzerPflicht',"'");
  if($f=fopen(MP_Pfad.'mpWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Meld.='Der ge�nderten Benutzereinstellungen wurden gespeichert.'; $MTyp='Erfo';
   if(!MP_SQL&&$aNeu!=$aNF){ //bei Textdatei
    $aD=file(MP_Pfad.MP_Daten.MP_Nutzer); $s=$aD[0]; $s=substr($s,0,strpos($s,';'));
    if(substr($s,0,7)!='Nummer_'){$nNutzerZahl=count($aD); $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5)); $s='Nummer_'.$nMx;}
    $s.=';Session;aktiv'; for($i=2;$i<=$nDatenFeldZahl;$i++) $s.=';'.str_replace(';','`,',$aNeu[$i]); $aD[0]=$s.NL;
    if($f=fopen(MP_Pfad.MP_Daten.MP_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
    else $Meld.='<p class="admFehl">In die Datei <i>'.MP_Daten.MP_Nutzer.'</i> konnte nicht geschrieben werden!</p>';
   }elseif($DbO&&($nDatenFeldZahl+1)!=$nFelder){ //bei SQL
    if($nDatenFeldZahl>=$nFelder){ //mehr Felder
     for($i=$nFelder;$i<=$nDatenFeldZahl;$i++) $DbO->query('ALTER TABLE '.MP_SqlTabN.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""');
    }else{ //weniger Felder
     for($i=$nFelder;$i>$nDatenFeldZahl;$i--) $DbO->query('ALTER TABLE '.MP_SqlTabN.' DROP dat_'.$i);
    }
   } //SQL
   $nFelder=$nDatenFeldZahl+1; $aNF=$aNeu; $aNP=$aPfl;
  }else $Meld=str_replace('#','mpWerte.php',MP_TxDateiRechte);
 }else{$Meld='Die Benutzereinstellungen bleiben unver�ndert.'; $MTyp='Meld';}
}
//Seitenausgabe
echo '<p class="adm'.$MTyp.'">'.trim($Meld).'</p>'.NL;
?>

<form action="konfNutzer.php<?php if($nSegNo) echo '?seg='.$nSegNo?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3">Der Marktplatz kann mit einer Benutzerverwaltung gekoppelt sein.
In diesem Falle werden Besucher in <i>unangemeldete G�ste</i> und <i>angemeldetet Benutzer</i> unterschieden.
Wenn in der Inseratestruktur eines Marktsegments ein Feld vom Typ <i>Benutzer</i> enthalten ist, wird die Benutzerverwaltung standardm��ig aktiviert.
<?php if(MP_NListeAnders||MP_NDetailAnders||MP_NEingabeAnders) echo 'Da bei Inserateliste, Detailanzeige oder Eingabeformular momentan f�r Benutzer eine von G�sten abweichende Funktion eingestellt ist, ist die Benutzerverwaltung offensichtlich aktiv.'?></td></tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">In der Benutzerverwaltung k�nnen folgenden Informationen �ber registrierte Benutzer gesammelt werden.</td></tr>
<tr class="admTabl">
 <td>Datenfeldanzahl</td>
 <td colspan="2"><input type="text" name="DatenFeldZahl" value="<?php echo ($nFelder-1)?>" size="2" /> Anzahl der Datenfelder in der Benutzerverwaltung &nbsp; <span class="admMini">(Empfehlung: 5 ... max. 15)</span></td>
</tr>

<tr class="admTabl"><td class="admSpa1"><b>Datenfeld</b></td><td width="16%" style="white-space:nowrap;"><b>Bezeichnung&nbsp;/&nbsp;Pflichtfeld</b></td><td><b>Hinweis</b></td></tr>
<tr class="admTabl"><td class="admSpa1">1. Status</td><td>aktiv</td><td>�ber dieses Feld k�nnen Sie registrierte Benutzer freigeben bzw. sperren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap;">2. Benutzername</td>
 <td><input type="text" name="F2" value="<?php echo str_replace('`,',';',$aNF[2])?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td rowspan="3" valign="top"><p>Auch wenn Sie diese 3 Felder anders benennen bleiben deren Funktion als <i>Benutzername</i>, <i>Passwort</i> und <i>E-Mail-Adresse</i> erhalten.</p></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap;">3. Passwort</td>
 <td><input type="text" name="F3" value="<?php echo str_replace('`,',';',$aNF[3])?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap;">4. E-Mail-Adresse</td>
 <td><input type="text" name="F4" value="<?php echo str_replace('`,',';',$aNF[4])?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap;">5. Feld</td>
 <td><input type="text" name="F5" value="<?php echo str_replace('`,',';',(isset($aNF[5])?$aNF[5]:''))?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P5" value="1"<?php if((isset($aNP[5])?$aNP[5]:false)) echo' checked="checked"'?> /></td>
 <td rowspan="<?php echo $nFelder-5?>" valign="top"><p>z.B. Anrede, Name, Anschrift, Telefon, Fax usw.</p></td>
</tr>

<?php  for($i=6;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1"><?php echo $i?>. Feld</td>
 <td><input type="text" name="F<?php echo $i?>" value="<?php echo str_replace('`,',';',$aNF[$i])?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P<?php echo $i?>" value="1"<?php if($aNP[$i]) echo' checked="checked"'?> /></td>
</tr>
<?php }?>
<tr class="admTabl">
 <td class="admSpa1">L�schfeld</td>
 <td colspan="2">aktuell wird <i><?php if($mpNutzerLoeschen<1) echo 'k' ?>eine</i> zus�tzliche L�schzeile eingeblendet. &nbsp; <span class="admMini">(siehe unten bei <i>Kontenl�schung</i>)</span></td>
</tr>

<tr class="admTabl"><td colspan="3">Wenn Sie ab dem f�nften Feld irgendwo ein Feld mit der Bezeichnung <i>HABEN</i> (exakt diese Schreibweise einhalten) verwenden so wird dieses Feld als Guthaben/Kredit f�r das Aufgeben neuer Inserate verwendet.
Als Administrator k�nnen Sie dem Benutzer eine <i>HABEN</i> als Anzahl von einzugebenden Inseraten in beliebiger H�he zuweisen.
Bei jedem Inserat, das der Benutzer anlegt wird dessen <i>HABEN</i>-Konto um 1 verringert.
Bei einem <i>HABEN</i> von Null wird ein weiteres Aufgeben von neuen Inseraten solange gesperrt,
bis der Administrator dem Benutzer erneut ein Guthaben zuweist.
Alternativ k�nnen Sie in das <i>HABEN</i>-Feld auch ein Endedatum f�r den Zeitraum eintragen, bis zu dem der Benutzer l�ngstens Inserate eingeben darf.
(Die Beantragung und Zuweisung von <i>HABEN</i> ist momentan noch Handarbeit und nicht automatisiert. Noch nicht fertig!!)</td></tr>
<tr class="admTabl">
 <td>HABEN-Begriff</td>
 <td colspan="2"><input class="admEing" type="text" name="TxGuthaben" value="<?php echo $mpTxGuthaben?>" maxlength="25" style="width:100px;" /> <span class="admMini">Empfehlung: <i>Guthaben</i> (wird dem Benutzer so angezeigt)</span></td>
</tr>
<tr class="admTabl">
 <td>Start-Guthaben</td>
 <td colspan="2"><input class="admEing" type="text" name="GuthabenNeu" value="<?php echo $mpGuthabenNeu?>" style="width:100px;" /> f�r neue Benutzer &nbsp; <span class="admMini">Empfehlung: <i>1....5</i></span></td>
</tr>

<tr class="admTabl"><td colspan="3">Wenn Sie ab dem f�nften Feld irgendwo ein Feld mit der Bezeichnung <i>WARNEN</i> (exakt diese Schreibweise einhalten) verwenden so wird dieses Feld als Frist f�r eine Warnmeldung an den Benutzer wegen des bevorstehenden Ablaufs seiner Inserate verwendet.
Als Administrator geben Sie hier eine Standardfrist in Tagen f�r das <i>WARNEN</i> vor, die der Benutzer aber selbst ver�ndern kann.<br>
Dieser Paramter funktioniert jedoch nur dann, wenn Sie t�glich einmal die Datei <i>mpCronJob.php</i> laufen lassen.</td></tr>
<tr class="admTabl">
 <td>WARNEN-Begriff</td>
 <td colspan="2"><input class="admEing" type="text" name="TxWarnen" value="<?php echo $mpTxWarnen?>" maxlength="25" style="width:100px;" /> <span class="admMini">Empfehlung: <i>Warnfrist</i> (wird dem Benutzer so angezeigt)</span></td>
</tr>
<tr class="admTabl">
 <td>Standard-Frist</td>
 <td colspan="2"><input class="admEing" type="text" name="WarnFristNeu" value="<?php echo $mpWarnFristNeu?>" style="width:100px;" /> Standardwert f�r neue Benutzer &nbsp; <span class="admMini">Empfehlung: <i>1....30</i> Tage</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Als zus�tzliche letzte Zeile im Formular der Benutzerdaten kann eine Zeile eingeblendet werden,
die ein Kontrollk�stchen mit Ausf�llzwang enth�lt. Dieses kann beispielsweise f�r eine Einverst�ndniserkl�rung mit <i>Allgemeinen Gesch�ftsbedingungen (AGB)</i> benutzt werden.
Alternativ hierzu k�nnen Sie ein solches K�stchen unter dem Eingabeformular f�r Inserate plazieren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Best�tigungsfeld</td>
 <td colspan="2"><input class="admEing" type="text" name="TxAgbFeld" value="<?php echo $mpTxAgbFeld?>" maxlength="25" style="width:100px;" /> <span class="admMini">Empfehlung: <i>AGB</i> oder <i>Best�tigung</i> oder leer lassen bei Nichtverwendung</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Beschreibung<br>(hinter dem K�stchen)</td>
 <td colspan="2"><input class="admEing" style="width:220px;" type="text" name="TxAgbText" value="<?php echo $mpTxAgbText?>" maxlength="250" /> <span class="admMini">Muster: <i>Ich akzeptiere die [AGB].</i></span>
 <div class="admMini"><u>Hinweis</u>: Der Text mu� [ ] enthalten f�r den Teil des Textes, der auf die AGB verlinkt.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisadresse</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="AgbLink" value="<?php echo $mpAgbLink?>" />
 <div class="admMini">funktionierende Verweisadresse auf die AGB-Seite, meist einschlie�lich <i>http://</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Verweis als<br>Popup-Fenster</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="AgbPopup" value="1"<?php if($mpAgbPopup) echo' checked="checked"'?> /> &nbsp;
 <input class="admEing" type="text" name="AgbBreit" value="<?php echo $mpAgbBreit?>" maxlength="4" style="width:40px;" /> px Fensterbreite, &nbsp;
 <input class="admEing" type="text" name="AgbHoch" value="<?php echo $mpAgbHoch?>" maxlength="4" style="width:40px;" /> px Fensterh�he
 <div>eventuell Zielfenster/Target <input class="admEing" type="text" name="AgbZiel" value="<?php echo $mpAgbZiel?>" maxlength="32" style="width:100px;" /></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">�ber dem Formular f�r die Benutzeranmeldung (Loginformular) werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Start</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerLogin" value="<?php echo $mpTxNutzerLogin?>" /><div class="admMini">Muster: <i>Melden Sie sich f�r die Marktplatzbenutzung an!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Fehler</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerNamePass" value="<?php echo $mpTxNutzerNamePass?>" /><div class="admMini">Muster: <i>Bitte Benutzernamen und Passwort angeben!</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerFalsch" value="<?php echo $mpTxNutzerFalsch?>" /><div class="admMini">Muster: <i>Ein Benutzer mit diesen Daten ist nicht verzeichnet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Erfolg<div style="margin-top:20px;">Logout-Erfolg</div></td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerOK" value="<?php echo $mpTxNutzerOK?>" /><div class="admMini">Muster: <i>Sie sind nun angemeldet und k�nnen die gew�nschte Aktion ausf�hren.</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerLogout" value="<?php echo $mpTxNutzerLogout?>" /><div class="admMini">Muster: <i>Sie wurden erfolgreich abgemeldet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sitzungsdauer</td>
 <td colspan="2"><input type="text" name="SessionZeit" value="<?php echo $mpSessionZeit?>" style="width:4em;" /> Minuten &nbsp; <span class="admMini">(Empfehlung: 40 Minuten)</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">�ber dem Formular mit den Benutzerdaten werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerPruefe" value="<?php echo $mpTxNutzerPruefe?>" /><div class="admMini">Muster: <i>Pr�fen und best�tigen Sie bitte Ihre Benutzerdaten!</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerGeaendert" value="<?php echo $mpTxNutzerGeaendert?>" /><div class="admMini">Muster: <i>Die ge�nderten Benutzerdaten wurden eingetragen!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">�ber dem Formular f�r eine Neuanmeldung werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzer-<br>neuanmeldung</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxEingabeFehl" value="<?php echo $mpTxEingabeFehl?>" /><div class="admMini">Muster: <i>Erg�nzen Sie bei den rot markierten Feldern!</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerVergeben" value="<?php echo $mpTxNutzerVergeben?>" /><div class="admMini">Muster: <i>Dieser Benutzername ist bereits vergeben!</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerNeu" value="<?php echo $mpTxNutzerNeu?>" /><div class="admMini">Muster: <i>Die Benutzerdaten wurden eingetragen und der Webmaster informiert!</i><br />oder: <i>Vielen Dank! Sie erhalten eine Best�tigung per E-Mail.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls neue Benutzer sich nach einer Registrierung durch den per E-Mail zugesandten Freischaltlink <a href="<?php echo AM_Hilfe?>LiesMich.htm#2.3" target="hilfe" onclick="hlpWin(this.href);return false"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> selbst aktivieren k�nnen, wird �ber dem Aktivierungsformular angezeigt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierungs-<br>meldungen</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxAktivieren" value="<?php echo $mpTxAktivieren?>" /><div class="admMini">Muster: <i>Benutzerzugang jetzt aktivieren?</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxAktiviert" value="<?php echo $mpTxAktiviert?>" /><div class="admMini">Muster: <i>Ihr Benutzerzugang wurde aktiviert!</i><br />oder: <i>Ihre Anmeldung wurde akzeptiert. Der Webmaster wird Sie demn�chst freischalten.</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxAktivFehl" value="<?php echo $mpTxAktivFehl?>" /><div class="admMini">Muster: <i>Der Freischaltcode ist ung�ltig!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">F�r den Versand eines vergessenen Passwortes werden folgende Einstellungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortformular</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerNameMail" value="<?php echo $mpTxNutzerNameMail?>" /><div class="admMini">Muster: <i>Bitte Benutzernamen oder E-Mail-Adresse angeben!</i></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerSend" value="<?php echo $mpTxNutzerSend?>" /><div class="admMini">Muster: <i>Die Zugangsdaten wurden soeben versandt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Versandbetreff<div style="margin-top:20px;">Versandtext</div></td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerDatBtr" value="<?php echo $mpTxNutzerDatBtr?>" /><div class="admMini">Muster: <i>Zugangsdaten bei #A</i></div>
 <textarea class="admEing" name="TxNutzerDaten" style="height:8em;"><?php echo str_replace('\n ',"\n",$mpTxNutzerDaten)?></textarea></div><div class="admMini">Muster: <i>Sie haben soeben Ihre Zugangsdaten zum Marktplatz auf #A angefordert. Diese lauten: lfd. Nummer: #N Benutzer: #B Passwort: #P</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Im �ffentlichen Anmeldeformular f�r Benutzer (Loginformular) kann auch ein Bereich zum Neuanlegen eines Benutzers vorhanden sein, �ber den G�ste einen Benutzerzugang beantragen k�nnen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Neuanmeldung</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerNeuErlaubt" value="1"<?php if($mpNutzerNeuErlaubt) echo' checked="checked"'?> /> Neuanmeldung f�r G�ste im Anmeldeformular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Passwort senden</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="PasswortSenden" value="1"<?php if($mpPasswortSenden) echo' checked="checked"'?> /> Zusenden vergessener Passworte im Loginformular einblenden</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Beim Anlegen neuer Benutzer k�nnen E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an den neuen Benutzer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Nutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:18px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuMail" value="1"<?php if($mpNutzerNeuMail) echo' checked="checked"'?> /> E-Mail versenden, eventuell inclusive Selbstfreischaltlink &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerNeuBtr" value="<?php echo $mpTxNutzerNeuBtr?>" /><div class="admMini">Muster: <i>Ihre Anmeldung bei #A</i></div>
 <textarea class="admEing" name="TxNutzerNeuTxt" style="height:8em;"><?php echo str_replace('\n ',"\n",$mpTxNutzerNeuTxt)?></textarea></div><div class="admMini">Muster: <i>Ihre Anmeldung bei #A wurde registriert. Hier Ihre Anmeldedaten: #D</i>&nbsp; oder<br/><i>Ihre Anmeldung bei #A wurde registriert. Bitte best�tigen Sie die Anmeldung �ber den Link #L<br />Hier Ihre Anmeldedaten: #D</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td colspan="2">Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($mpFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (mpSeite.htm)</option><option value="Popup<?php if($mpFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (mpPopup.htm)</option><option value="Freischalt<?php if($mpFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (mpFreischalt.htm)</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Selbstfreischaltung</td>
 <td colspan="2">
  <input class="admRadio" type="radio" name="FreischaltAdmin" value="0"<?php if(!$mpFreischaltAdmin) echo' checked="checked"'?> /> die Selbstfreischaltung �ber den Link in der E-Mail aktiviert das Benutzerkonto sofort<br />
  <input class="admRadio" type="radio" name="FreischaltAdmin" value="1"<?php if($mpFreischaltAdmin) echo' checked="checked"'?> /> die Selbstfreischaltung �ber den Link muss abschlie�end vom Admin best�tigt werden
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">E-Mail an Admin<div style="margin-top:5px;">Betreff</div><div style="margin-top:18px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuAdmMail" value="1"<?php if($mpNutzerNeuAdmMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzNeuAdmBtr" value="<?php echo $mpTxNutzNeuAdmBtr?>" /><div class="admMini" style="margin-bottom:3px">Muster: <i>neuer Marktplatz-Benutzer Nr. #N</i></div>
 <textarea class="admEing" name="TxNutzNeuAdmTxt" style="height:5em;"><?php echo str_replace('\n ',"\n",$mpTxNutzNeuAdmTxt)?></textarea></div><div class="admMini">Muster: <i>Ein neuer Marktplatz-Benutzer Nr. #N hat sich wie folgt angemeldet: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierung-Mail<div style="margin-top:5px;">Betreff</div><div style="margin-top:18px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerAktivMail" value="1"<?php if($mpNutzerAktivMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerAktivBtr" value="<?php echo $mpTxNutzerAktivBtr?>" /><div class="admMini" style="margin-bottom:3px">Muster: <i>Zugang aktiviert bei #A</i></div>
 <textarea class="admEing" name="TxNutzerAktivTxt" style="height:5em;"><?php echo str_replace('\n ',"\n",$mpTxNutzerAktivTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Benutzerzugang bei #A wurde soeben vom Webmaster freigeschaltet. Hier Ihre Anmeldedaten: #D</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Unter dem Benutzerdatenformular kann eine Zeile zum L�schen des Benutzerkontos eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Konten-<br />l�schung</td>
 <td colspan="2">
  <div><input class="admRadio" type="radio" name="NutzerLoeschen" value="0"<?php if($mpNutzerLoeschen<=1) echo' checked="checked"'?> /> L�schm�glichkeit <i>nicht</i> anbieten</div>
  <div><input class="admRadio" type="radio" name="NutzerLoeschen" value="1"<?php if($mpNutzerLoeschen==1) echo' checked="checked"'?> /> L�schm�glichkeit anbieten, der Administrator vollendet den L�schauftrag</div>
  <div><input class="admRadio" type="radio" name="NutzerLoeschen" value="2"<?php if($mpNutzerLoeschen==2) echo' checked="checked"'?> /> L�schm�glichkeit anbieten, das L�schen erfolgt direkt sofort</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Best�tigungs-<br>Meldung</td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxNutzerLschMeld" value="<?php echo $mpTxNutzerLschMeld?>" /><div class="admMini" style="margin-top:2px">Muster: <i>Der Administrator wurde �ber Ihren L�schwunsch informiert</i> &nbsp; oder &nbsp; <i>Ihr Konto wurde gel�scht</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">L�schantrags-Mail<div style="margin-top:5px;">Betreff</div><div style="margin-top:18px">Text</div></td>
 <td colspan="2"><div style="margin-top;3px;margin-bottom:5px">sofern der Administrator das L�schen des Benutzerkontos vollenden soll, erh�lt er folgende E-Mail:</div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerLschBtrA" value="<?php echo $mpTxNutzerLschBtrA?>" /><div class="admMini" style="margin-bottom:3px">Muster: <i>Nutzerzugang Nr. #N l�schen</i></div>
 <textarea class="admEing" name="TxNutzerLschTxtA" style="height:3em;"><?php echo str_replace('\n ',"\n",$mpTxNutzerLschTxtA)?></textarea></div><div class="admMini">Muster: <i>Der Benutzer Nummer #N mit dem Benutzernamen #U und der E-Mail-Adresse #E m�chte sein Konto l�schen lassen.</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">L�schbest�tigung<div style="margin-top:5px;">Betreff</div><div style="margin-top:18px">Text</div></td>
 <td colspan="2"><div style="margin-top;3px;margin-bottom:5px">sofern das L�schen des Benutzerkontos erfolgt ist, erh�lt der ehemalige Benutzer folgende E-Mail:</div>
 <input class="admEing" style="width:99%" type="text" name="TxNutzerLschBtrN" value="<?php echo $mpTxNutzerLschBtrN?>" /><div class="admMini" style="margin-bottom:3px">Muster: <i>Nutzerzugang bei #A gel�scht</i> &nbsp; &nbsp; &nbsp;(oder leer lassen f�r nicht versenden)</div>
 <textarea class="admEing" name="TxNutzerLschTxtN" style="height:5em;"><?php echo str_replace('\n ',"\n",$mpTxNutzerLschTxtN)?></textarea></div><div class="admMini">Muster: <i>Hallo #U, Ihr Benutzerkonto mit der Benutzernummer #N und der E-Mail-Adresse #E sowie alle Ihre Inserate auf der Webseite #A wurden soeben vom Administrator gel�scht.</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschl�giger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerDSE1" value="1"<?php if($mpNutzerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollk�stchen zur Datenschutzerkl�rung einblenden<br /><input class="admCheck" type="checkbox" name="NutzerDSE2" value="1"<?php if($mpNutzerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollk�stchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Men�punkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Neuanmeldung von Benutzern und Versand vergessener Passworte �ber ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzercaptcha</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="LoginCaptcha" value="1"<?php if($mpLoginCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>
<tr class="admTabl">
 <td colspan="3"><span class="admMini"><u>Hinweis</u>: Diese besondere Captcha-Einstellung gilt unabh�ngig von der Captcha-Aktivierung in den sonstigen Formularen, die Sie unter <i>Allgemeines</i> oder <i>Eingabeformular</i> vornehmen k�nnen. Es werden jedoch die gleichen Captcha-Farben wie beim allgemeinen Captcha verwendet.</span></td>
</tr>

<tr class="admTabl">
 <td colspan="3" class="admSpa2">Kurz vor Ablauf eines Inserates k�nnen �ber den Cron-Job <a href="<?php echo MPPFAD ?>mpCronJob.php?mp=<?php echo MP_Schluessel?>" target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten">mpCronJob.php</a> Warnungen an den Inserenten versandt werden,
 die auf den baldigen Ablauf hinweisen.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Warn-Betreff<div style="margin-top:18px">Text</div></td>
 <td colspan="2"><input class="admEing" style="width:99%" type="text" name="TxWarnenBtr" value="<?php echo $mpTxWarnenBtr?>" /><div class="admMini">Muster: <i>Inserateablauf bei #A</i></div>
 <textarea class="admEing" name="TxWarnenTxt" style="height:5em;"><?php echo str_replace('\n ',"\n",$mpTxWarnenTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Inserat bei #A im Segment #S l�uft demn�chst ab und wird dann nicht l�nger angezeigt: #D Direktlink&nbsp;#L</i></td>
</tr>

<tr class="admTabl">
 <td colspan="3" class="admSpa2">F�r Benutzer k�nnen in der Tabellenansicht, der Detailansicht und bei der Inserateeingabe und beim Inserate�ndern einige besondere Rechte gelten.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">versteckte Inserate</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NVerstecktSehen" value="1"<?php if($mpNVerstecktSehen) echo ' checked="checked"'?> /> angemeldete Benutzer sollen versteckte Inserate sehen k�nnen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NListeAnders" value="1"<?php if($mpNListeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Tabellenspalten sehen als G�ste (siehe Inserateliste)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Detailansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NDetailAnders" value="1"<?php if($mpNDetailAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Datenzeilen sehen als G�ste (siehe Detailanzeige)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabeformular</td>
 <td colspan="2"><div><input type="checkbox" class="admCheck" name="NEingabeLogin" value="1"<?php if($mpNEingabeLogin) echo ' checked="checked"'?> /> <i>nur</i> angemeldete Benutzer sollen Eingaben und �nderungen ausf�hren k�nnen</div>
 <div><input type="checkbox" class="admCheck" name="NAendernFremde" value="1"<?php if($mpNAendernFremde) echo ' checked="checked"'?> /> angemeldete Benutzer sollen auch fremde Inserate �ndern d�rfen <span class="admMini">(Empfehlung: <i>nein</i>)</span></div>
 <div><input type="checkbox" class="admCheck" name="NEingabeAnders" value="1"<?php if($mpNEingabeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Eingabefelder sehen als G�ste (siehe Eingabeformular)</div></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Speichern"></p>
</form>

<div class="admBox"><u>Hinweis:</u> Momentan ist die Benutzerverwaltung offensichtlich <span<?php echo ($bNutzer?'>':' style="color:#DD0022;">nicht')?></span> aktiviert.</div>

<?php
echo fSeitenFuss();

function fSetzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n};
 if($t=="'") $w=str_replace("'",'�',$w); ${'am'.$n}=$w;
 if($w!=constant('AM_'.$n)){
  $p=strpos($sWerte,'AM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'AM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>