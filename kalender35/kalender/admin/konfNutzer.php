<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Benutzerfunktionen','','KNz');

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
 array_splice($kal_NutzerFelder,1,1); array_splice($kal_NutzerPflicht,1,1); $nFelder=count($kal_NutzerFelder);
 $ksTxNutzerLogin=KAL_TxNutzerLogin; $ksTxNutzerNamePass=KAL_TxNutzerNamePass; $ksTxNutzerFalsch=KAL_TxNutzerFalsch;
 $ksTxNutzerOK=KAL_TxNutzerOK; $ksTxNutzerLogout=KAL_TxNutzerLogout; $ksSessionZeit=KAL_SessionZeit;
 $ksTxNutzerPruefe=KAL_TxNutzerPruefe; $ksTxNutzerGeaendert=KAL_TxNutzerGeaendert; $ksPasswortSenden=KAL_PasswortSenden;
 $ksNutzerZentrum=KAL_NutzerZentrum; $ksTxNZentrZeigen=KAL_TxNZentrZeigen; $ksTxNZentrStart=KAL_TxNZentrStart; $ksNutzerZentrSel=KAL_NutzerZentrSel;
 $ksNutzerAendern=KAL_NutzerAendern; $ksTxNDatAendern=KAL_TxNDatAendern;
 $ksTxEingabeFehl=KAL_TxEingabeFehl; $ksTxNutzerVergeben=KAL_TxNutzerVergeben; $ksTxNutzerNeu=KAL_TxNutzerNeu;
 $ksTxNutzerNameMail=KAL_TxNutzerNameMail; $ksTxNutzerSend=KAL_TxNutzerSend; $ksTxNutzerDatBtr=KAL_TxNutzerDatBtr; $ksTxNutzerDaten=KAL_TxNutzerDaten;
 $ksTxAktivieren=KAL_TxAktivieren; $ksTxAktiviert=KAL_TxAktiviert; $ksTxAktivFehl=KAL_TxAktivFehl;
 $ksNutzerNeuErlaubt=KAL_NutzerNeuErlaubt; $ksFreischaltWin=KAL_FreischaltWin; $ksFreischaltAdmin=KAL_FreischaltAdmin;
 $ksNutzerNeuMail=KAL_NutzerNeuMail; $ksTxNutzerNeuBtr=KAL_TxNutzerNeuBtr; $ksTxNutzerNeuTxt=KAL_TxNutzerNeuTxt;
 $ksNutzerNeuAdmMail=KAL_NutzerNeuAdmMail; $ksEmpfNutzer=KAL_EmpfNutzer; $ksTxNutzNeuAdmBtr=KAL_TxNutzNeuAdmBtr; $ksTxNutzNeuAdmTxt=KAL_TxNutzNeuAdmTxt;
 $ksNutzerAktivMail=KAL_NutzerAktivMail; $ksTxNutzerAktivBtr=KAL_TxNutzerAktivBtr; $ksTxNutzerAktivTxt=KAL_TxNutzerAktivTxt;
 $ksNutzerAendAdmMail=KAL_NutzerAendAdmMail; $ksTxNutzAendAdmBtr=KAL_TxNutzAendAdmBtr; $ksTxNutzAendAdmTxt=KAL_TxNutzAendAdmTxt;
 $ksNutzerDSE1=KAL_NutzerDSE1; $ksNutzerDSE2=KAL_NutzerDSE2; $ksLoginCaptcha=KAL_LoginCaptcha;
 $ksNVerstecktSehen=KAL_NVerstecktSehen; $ksNListeAnders=KAL_NListeAnders; $ksNDetailAnders=KAL_NDetailAnders;
 $ksNEingabeLogin=KAL_NEingabeLogin; $ksNAendernFremde=KAL_NAendernFremde; $ksNEingabeAnders=KAL_NEingabeAnders;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen;
 array_splice($kal_NutzerFelder,1,1); array_splice($kal_NutzerPflicht,1,1); $nFelder=count($kal_NutzerFelder);
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $nDatenFeldZahl=max((int)txtVar('DatenFeldZahl'),4);
 $aNeu=array('Nummer','aktiv'); $aPfl=array(0,0,1,1,1);
 for($i=2;$i<=$nDatenFeldZahl;$i++){$aNeu[$i]=txtVar('F'.$i); if($i>4) $aPfl[$i]=(isset($_POST['P'.$i])?$_POST['P'.$i]:'');}
 $s=txtVar('TxNutzerLogin'); if(fSetzKalWert($s,'TxNutzerLogin','"')) $bNeu=true;
 $s=txtVar('TxNutzerNamePass'); if(fSetzKalWert($s,'TxNutzerNamePass','"')) $bNeu=true;
 $s=txtVar('TxNutzerFalsch'); if(fSetzKalWert($s,'TxNutzerFalsch','"')) $bNeu=true;
 $s=txtVar('TxNutzerOK'); if(fSetzKalWert($s,'TxNutzerOK','"')) $bNeu=true;
 $s=txtVar('TxNutzerLogout'); if(fSetzKalWert($s,'TxNutzerLogout','"')) $bNeu=true;
 $s=min(max(txtVar('SessionZeit'),5),300); if(fSetzKalWert($s,'SessionZeit','')) $bNeu=true;
 $s=txtVar('TxNutzerPruefe'); if(fSetzKalWert($s,'TxNutzerPruefe','"')) $bNeu=true;
 $s=txtVar('TxNutzerGeaendert'); if(fSetzKalWert($s,'TxNutzerGeaendert','"')) $bNeu=true;
 $s=txtVar('TxEingabeFehl'); if(fSetzKalWert($s,'TxEingabeFehl','"')) $bNeu=true;
 $s=txtVar('TxNutzerVergeben'); if(fSetzKalWert($s,'TxNutzerVergeben','"')) $bNeu=true;
 $s=txtVar('TxNutzerNeu'); if(fSetzKalWert($s,'TxNutzerNeu','"')) $bNeu=true;
 $s=txtVar('TxNutzerNameMail'); if(fSetzKalWert($s,'TxNutzerNameMail','"')) $bNeu=true;
 $s=txtVar('TxNutzerSend'); if(fSetzKalWert($s,'TxNutzerSend','"')) $bNeu=true;
 $s=txtVar('TxNutzerDatBtr'); if(fSetzKalWert($s,'TxNutzerDatBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerDaten')))); if(fSetzKalWert($s,'TxNutzerDaten',"'")) $bNeu=true;
 $s=txtVar('TxAktivieren'); if(fSetzKalWert($s,'TxAktivieren','"')) $bNeu=true;
 $s=txtVar('TxAktiviert'); if(fSetzKalWert($s,'TxAktiviert','"')) $bNeu=true;
 $s=txtVar('TxAktivFehl'); if(fSetzKalWert($s,'TxAktivFehl','"')) $bNeu=true;
 $s=(int)txtVar('NutzerNeuErlaubt'); if(fSetzKalWert(($s?true:false),'NutzerNeuErlaubt','')) $bNeu=true;
 $s=(int)txtVar('NutzerZentrum'); if(fSetzKalWert(($s?true:false),'NutzerZentrum','')) $bNeu=true;
 $s=(int)txtVar('NutzerZentrSel'); if(fSetzKalWert(($s?true:false),'NutzerZentrSel','')) $bNeu=true;
 $s=txtVar('TxNZentrZeigen'); if(fSetzKalWert($s,'TxNZentrZeigen',"'")) $bNeu=true;
 $s=txtVar('TxNZentrStart'); if(fSetzKalWert($s,'TxNZentrStart','"')) $bNeu=true;
 $s=(int)txtVar('NutzerAendern'); if(fSetzKalWert(($s?true:false),'NutzerAendern','')) $bNeu=true;
 $s=txtVar('TxNDatAendern'); if(fSetzKalWert($s,'TxNDatAendern',"'")) $bNeu=true;
 $s=(int)txtVar('PasswortSenden'); if(fSetzKalWert(($s?true:false),'PasswortSenden','')) $bNeu=true;
 $s=txtVar('FreischaltWin'); if(fSetzKalWert($s,'FreischaltWin',"'")) $bNeu=true;
 $s=(int)txtVar('FreischaltAdmin'); if(fSetzKalWert(($s&&strpos('x'.$_POST['TxNutzerNeuTxt'],'#L')?true:false),'FreischaltAdmin','')) $bNeu=true;
 $s=(int)txtVar('NutzerNeuMail'); if(fSetzKalWert(($s?true:false),'NutzerNeuMail','')) $bNeu=true;
 $s=txtVar('TxNutzerNeuBtr'); if(fSetzKalWert($s,'TxNutzerNeuBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerNeuTxt')))); if(fSetzKalWert($s,'TxNutzerNeuTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerNeuAdmMail'); if(fSetzKalWert(($s?true:false),'NutzerNeuAdmMail','')) $bNeu=true;
 $s=txtVar('EmpfNutzer'); if(fSetzKalWert($s,'EmpfNutzer',"'")) $bNeu=true;
 $s=txtVar('TxNutzNeuAdmBtr'); if(fSetzKalWert($s,'TxNutzNeuAdmBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzNeuAdmTxt')))); if(fSetzKalWert($s,'TxNutzNeuAdmTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerAendAdmMail'); if(fSetzKalWert(($s?true:false),'NutzerAendAdmMail','')) $bNeu=true;
 $s=txtVar('TxNutzAendAdmBtr'); if(fSetzKalWert($s,'TxNutzAendAdmBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzAendAdmTxt')))); if(fSetzKalWert($s,'TxNutzAendAdmTxt',"'")) $bNeu=true;
 $s=(int)txtVar('NutzerAktivMail'); if(fSetzKalWert(($s?true:false),'NutzerAktivMail','')) $bNeu=true;
 $s=txtVar('TxNutzerAktivBtr'); if(fSetzKalWert($s,'TxNutzerAktivBtr','"')) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerAktivTxt')))); if(fSetzKalWert($s,'TxNutzerAktivTxt',"'")) $bNeu=true;
 $v=txtVar('NutzerDSE1'); if(fSetzKalWert(($v?true:false),'NutzerDSE1','')) $bNeu=true;
 $v=txtVar('NutzerDSE2'); if(fSetzKalWert(($v?true:false),'NutzerDSE2','')) $bNeu=true;
 $v=(int)txtVar('LoginCaptcha'); if(fSetzKalWert(($v?true:false),'LoginCaptcha','')) $bNeu=true;
 $v=(int)txtVar('NVerstecktSehen');if(fSetzKalWert(($v?true:false),'NVerstecktSehen','')) $bNeu=true;
 $v=(int)txtVar('NListeAnders'); if(fSetzKalWert(($v?true:false),'NListeAnders','')) $bNeu=true;
 $v=(int)txtVar('NDetailAnders');if(fSetzKalWert(($v?true:false),'NDetailAnders','')) $bNeu=true;
 $v=(int)txtVar('NEingabeLogin');if(fSetzKalWert(($v?true:false),'NEingabeLogin','')) $bNeu=true;
 $v=(int)txtVar('NAendernFremde'); if(fSetzKalWert(($v?true:false),'NAendernFremde','')) $bNeu=true;
 $v=(int)txtVar('NEingabeAnders'); if(fSetzKalWert(($v?true:false),'NEingabeAnders','')) $bNeu=true;
 if($bNeu||$aNeu!=$kal_NutzerFelder||$aPfl!=$kal_NutzerPflicht){ //geändert
  if($nDatenFeldZahl<ADM_NutzerFelder) fSetzAdmWert($nDatenFeldZahl,'NutzerFelder',''); //Korrektur
  $aTmp=$aNeu; array_splice($aTmp,1,0,'Session'); fSetz0Array(($aTmp),'NutzerFelder','"');
  $aTmp=$aPfl; array_splice($aTmp,1,0,0); fSetz0Array(($aTmp),'NutzerPflicht','');
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Einstellungen für das Formular der Benutzerverwaltung wurden gespeichert.</p>';
   if(!KAL_SQL&&$aNeu!=$kal_NutzerFelder){ //bei Textdatei
    $aD=file(KAL_Pfad.KAL_Daten.KAL_Nutzer); $s=$aD[0]; $s=substr($s,0,strpos($s,';'));
    if(substr($s,0,7)!='Nummer_'){$nNutzerZahl=count($aD); $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5)); $s='Nummer_'.$nMx;}
    $s.=';Session;aktiv'; for($i=2;$i<=$nDatenFeldZahl;$i++) $s.=';'.str_replace(';','`,',$aNeu[$i]); $aD[0]=$s.NL;
    if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
    else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Nutzer.'</i> konnte nicht geschrieben werden!</p>';
   }elseif(KAL_SQL&&($nDatenFeldZahl+1)!=$nFelder){ //bei SQL
    if($DbO){
     if($nDatenFeldZahl>=$nFelder) for($i=$nFelder;$i<=$nDatenFeldZahl;$i++) $DbO->query('ALTER TABLE '.KAL_SqlTabN.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""'); //mehr Felder
     else for($i=$nFelder;$i>$nDatenFeldZahl;$i--) $DbO->query('ALTER TABLE '.KAL_SqlTabN.' DROP dat_'.$i); //weniger Felder
    }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>';
   }//SQL
   $nFelder=$nDatenFeldZahl+1; $kal_NutzerFelder=$aNeu; $kal_NutzerPflicht=$aPfl;
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$Msg){
 if($bNutzer) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Formular der Benutzeranmeldung.</p>';
 else $Msg='<p class="admFehl">Die Benutzerverwaltung ist momentan inaktiv!</p>';
}
echo $Msg.NL;
?>

<form action="konfNutzer.php" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="3">Der Kalender kann mit einer Benutzerverwaltung gekoppelt sein.
In diesem Falle werden Besucher in <i>unangemeldete Gäste</i> und <i>angemeldetet Benutzer</i> unterschieden.
<?php echo(in_array('u',$kal_FeldType)?'Da':'Wenn')?> in der Terminstruktur ein Feld vom Typ <i>Benutzer</i> enthalten ist, wird die Benutzerverwaltung standardmäßig aktiviert.
<?php if(KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders) echo 'Da bei Terminliste, Detailanzeige oder Eingabeformular momentan für Benutzer eine von Gästen abweichende Funktion eingestellt ist, ist die Benutzerverwaltung aktiviert.'?></td></tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">In der Benutzerverwaltung können folgenden Informationen über registrierte Benutzer gesammelt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenfeldanzahl</td>
 <td colspan="2"><input type="text" name="DatenFeldZahl" value="<?php echo ($nFelder-1)?>" size="2" style="width:50px;" /> maximale Anzahl der Datenfelder in der Benutzerverwaltung &nbsp; <span class="admMini">(Empfehlung: 5 ... max. 15)</span></td>
</tr>
<tr class="admTabl"><td class="admSpa1"><b>Datenfeld</b></td><td class="admSpa1"><b>Bezeichnung / Pflichtfeld</b></td><td class="admSpa1" style="width:80%"><b>Hinweis</b></td></tr>
<tr class="admTabl"><td class="admSpa1">1. Status</td><td>aktiv</td><td>Über dieses Feld können Sie registrierte Benutzer freigeben bzw. sperren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">2. Benutzername</td>
 <td><input type="text" name="F2" value="<?php echo $kal_NutzerFelder[2]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td rowspan="3" valign="top"><p>Auch wenn Sie diese 3 Felder anders benennen bleiben deren Funktion als <i>Benutzername</i>, <i>Passwort</i> und <i>E-Mail-Adresse</i> erhalten.</p></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">3. Passwort</td>
 <td><input type="text" name="F3" value="<?php echo $kal_NutzerFelder[3]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">4. E-Mail-Adresse</td>
 <td><input type="text" name="F4" value="<?php echo $kal_NutzerFelder[4]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">5. Feld</td>
 <td><input type="text" name="F5" value="<?php echo $kal_NutzerFelder[5]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P5" value="1"<?php if($kal_NutzerPflicht[5]) echo' checked="checked"'?> /></td>
 <td rowspan="<?php echo $nFelder-4?>" valign="top"><p>z.B. Anrede, Name, Anschrift, Telefon, Fax usw.</p></td>
</tr>

<?php  for($i=6;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1"><?php echo $i?>. Feld</td>
 <td><input type="text" name="F<?php echo $i?>" value="<?php echo $kal_NutzerFelder[$i]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P<?php echo $i?>" value="1"<?php if($kal_NutzerPflicht[$i]) echo' checked="checked"'?> /></td>
</tr>

<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für die Benutzeranmeldung (Loginformular) werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Start</td>
 <td colspan="2"><input style="width:100%" type="text" name="TxNutzerLogin" value="<?php echo $ksTxNutzerLogin?>" /><div class="admMini">Muster: <i>Melden Sie sich für die Kalenderbenutzung an!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Fehler</td>
 <td colspan="2"><inpu style="width:100%" type="text" name="TxNutzerNamePass" value="<?php echo $ksTxNutzerNamePass?>" /><div class="admMini">Muster: <i>Bitte Benutzernamen und Passwort angeben!</i></div>
 <input style="width:100%" type="text" name="TxNutzerFalsch" value="<?php echo $ksTxNutzerFalsch?>" /><div class="admMini">Muster: <i>Ein Benutzer mit diesen Daten ist nicht verzeichnet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Erfolg<div style="margin-top:20px;">Logout-Erfolg</div></td>
 <td colspan="2"><input style="width:100%" type="text" name="TxNutzerOK" value="<?php echo $ksTxNutzerOK?>" /><div class="admMini">Muster: <i>Sie sind nun angemeldet und können die gewünschte Aktion ausführen.</i></div>
 <input style="width:100%" type="text" name="TxNutzerLogout" value="<?php echo $ksTxNutzerLogout?>" /><div class="admMini">Muster: <i>Sie wurden erfolgreich abgemeldet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sitzungsdauer</td>
 <td colspan="2"><input type="text" name="SessionZeit" value="<?php echo $ksSessionZeit?>" style="width:4em;" /> Minuten &nbsp; <span class="admMini">(Empfehlung: 40 Minuten)</span></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Im Formular für die Benutzeranmeldung können Zugänge zum Benutzerzentrum oder zu den Benutzerdaten eingeblendet werden</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerzentrum</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerZentrum" value="1"<?php if($ksNutzerZentrum) echo' checked="checked"'?> />Zugangssmöglichkeit zum Benutzerzentrum einblenden, &nbsp;
 <input class="admCheck" type="checkbox" name="NutzerZentrSel" value="1"<?php if($ksNutzerZentrSel) echo' checked="checked"'?> /> standardmäßig vorausgewählt<br />
 <input style="width:100%" type="text" name="TxNZentrZeigen" value="<?php echo $ksTxNZentrZeigen?>" /><div class="admMini">Muster: <i>Benutzerzentrum aufrufen</i></div>
 <input style="width:100%" type="text" name="TxNZentrStart" value="<?php echo $ksTxNZentrStart?>" /><div class="admMini">Muster: <i>Willkommen im Benutzerzentrum!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Daten ändern</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerAendern" value="1"<?php if($ksNutzerAendern) echo' checked="checked"'?> /> Änderungsmöglichkeit für eigene Benutzerdaten einblenden<br />
 <input style="width:100%" type="text" name="TxNDatAendern" value="<?php echo $ksTxNDatAendern?>" /><div class="admMini">Muster: <i>Benutzerdaten ändern</i></div></td>
</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular mit den Benutzerdaten werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2"><input style="width:100%" type="text" name="TxNutzerPruefe" value="<?php echo $ksTxNutzerPruefe?>" /><div class="admMini">Muster: <i>Prüfen und bestätigen Sie bitte Ihre Benutzerdaten!</i></div>
 <input style="width:100%" type="text" name="TxNutzerGeaendert" value="<?php echo $ksTxNutzerGeaendert?>" /><div class="admMini">Muster: <i>Die geänderten Benutzerdaten wurden eingetragen!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für eine Neuanmeldung werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzer-<br />neuanmeldung</td>
 <td colspan="2"><input style="width:100%" type="text" name="TxEingabeFehl" value="<?php echo $ksTxEingabeFehl?>" /><div class="admMini">Muster: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div>
 <input style="width:100%" type="text" name="TxNutzerVergeben" value="<?php echo $ksTxNutzerVergeben?>" /><div class="admMini">Muster: <i>Dieser Benutzername ist bereits vergeben!</i></div>
 <input style="width:100%" type="text" name="TxNutzerNeu" value="<?php echo $ksTxNutzerNeu?>" /><div class="admMini">Muster: <i>Die Benutzerdaten wurden eingetragen und der Webmaster informiert!</i><br />oder: <i>Vielen Dank! Sie erhalten eine Bestätigung per E-Mail.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls neue Benutzer sich nach einer Registrierung durch den per E-Mail zugesandten Freischaltlink <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.12.selbst" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> selbst aktivieren können, wird über dem Aktivierungsformular angezeigt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierungs-<br />meldungen</td>
 <td colspan="2"><input style="width:100%" type="text" name="TxAktivieren" value="<?php echo $ksTxAktivieren?>" /><div class="admMini">Muster: <i>Benutzerzugang jetzt aktivieren?</i></div>
 <input style="width:100%" type="text" name="TxAktiviert" value="<?php echo $ksTxAktiviert?>" /><div class="admMini">Muster: <i>Ihr Benutzerzugang wurde aktiviert!</i><br />oder: <i>Ihre Anmeldung wurde akzeptiert. Der Webmaster wird Sie demnächst freischalten.</i></div>
 <input style="width:100%" type="text" name="TxAktivFehl" value="<?php echo $ksTxAktivFehl?>" /><div class="admMini">Muster: <i>Der Freischaltcode ist ungültig!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Versand eines vergessenen Passwortes werden folgende Einstellungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortformular</td>
 <td colspan="2"><input style="width:100%" type="text" name="TxNutzerNameMail" value="<?php echo $ksTxNutzerNameMail?>" /><div class="admMini">Muster: <i>Bitte Benutzernamen oder E-Mail-Adresse angeben!</i></div>
 <input style="width:100%" type="text" name="TxNutzerSend" value="<?php echo $ksTxNutzerSend?>" /><div class="admMini">Muster: <i>Die Zugangsdaten wurden soeben versandt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Versandbetreff<div style="margin-top:20px;">Versandtext</div></td>
 <td colspan="2"><input style="width:100%" type="text" name="TxNutzerDatBtr" value="<?php echo $ksTxNutzerDatBtr?>" /><div class="admMini">Muster: <i>Zugangsdaten bei #A</i></div>
 <textarea name="TxNutzerDaten" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxNutzerDaten)?></textarea></div><div class="admMini">Muster: <i>Sie haben soeben Ihre Zugangsdaten zum Kalender auf #A angefordert. Diese lauten: lfd. Nummer: #N Benutzer: #B Passwort: #P</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Im öffentlichen Anmeldeformular für Benutzer (Loginformular) kann auch ein Bereich zum Neuanlegen eines Benutzers vorhanden sein, über den Gäste einen Benutzerzugang beantragen können sowie ein Bereich zum Zusenden eines vergessenen Passwortes.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Neuanmeldung</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerNeuErlaubt" value="1"<?php if($ksNutzerNeuErlaubt) echo' checked="checked"'?> /> Neuanmeldung für Gäste im Anmeldeformular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Passwort senden</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="PasswortSenden" value="1"<?php if($ksPasswortSenden) echo' checked="checked"'?> /> Zusenden vergessener Passworte im Loginformular einblenden</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Beim Anlegen neuer Benutzer können E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an den neuen Benutzer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Nutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuMail" value="1"<?php if($ksNutzerNeuMail) echo' checked="checked"'?> /> E-Mail versenden, eventuell inclusive Selbstfreischaltlink &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input style="width:100%" type="text" name="TxNutzerNeuBtr" value="<?php echo $ksTxNutzerNeuBtr?>" /><div class="admMini">Muster: <i>Ihre Anmeldung bei #A</i></div>
 <textarea name="TxNutzerNeuTxt" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxNutzerNeuTxt)?></textarea></div><div class="admMini">Muster: <i>Ihre Anmeldung bei #A wurde registriert. Hier Ihre Anmeldedaten: #D</i>&nbsp; oder<br/><i>Ihre Anmeldung bei #A wurde registriert. Bitte bestätigen Sie die Anmeldung über den Link #L<br />Hier Ihre Anmeldedaten: #D</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td colspan="2">Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($ksFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (kalSeite.htm)</option><option value="Popup<?php if($ksFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (kalPopup.htm)</option><option value="Freischalt<?php if($ksFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (kalFreischalt.htm)</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Selbstfreischaltung</td>
 <td colspan="2">
  <input class="admRadio" type="radio" name="FreischaltAdmin" value="0"<?php if(!$ksFreischaltAdmin) echo' checked="checked"'?> /> die Selbstfreischaltung über den Link in der E-Mail aktiviert das Benutzerkonto sofort<br />
  <input class="admRadio" type="radio" name="FreischaltAdmin" value="1"<?php if($ksFreischaltAdmin) echo' checked="checked"'?> /> die Selbstfreischaltung über den Link muss abschließend vom Admin bestätigt werden
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">E-Mail an Admin<div style="margin-top:5px;white-space:nowrap;">alternative Adresse</div><div style="margin-top:24px;">Betreff</div><div style="margin-top:15px;">Text</div><div style="margin-top:88px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuAdmMail" value="1"<?php if($ksNutzerNeuAdmMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <div><input type="text" name="EmpfNutzer" value="<?php echo $ksEmpfNutzer?>" style="width:220px" /> <span class="admMini">leer lassen oder E-Mail-Adresse des Benutzerverwalters</span></div>
 <div class="admMini" style="margin-bottom:5px">(Wird bei Benutzeraktionen anstatt <i><?php echo KAL_Empfaenger?></i> für die E-Mails an den Webmaster verwendet.)</div>
 <input style="width:100%" type="text" name="TxNutzNeuAdmBtr" value="<?php echo $ksTxNutzNeuAdmBtr?>" /><div class="admMini">Muster: <i>neuer Kalender-Benutzer Nr. #N</i></div>
 <textarea name="TxNutzNeuAdmTxt" rows="5" cols="80" style="height:5em"><?php echo str_replace('\n ',"\n",$ksTxNutzNeuAdmTxt)?></textarea></div><div class="admMini">Muster: <i>Ein neuer Kalender-Benutzer Nr. #N hat sich wie folgt angemeldet: #D</i></div>
 <div style="margin-top:8px;"><input class="admCheck" type="checkbox" name="NutzerAendAdmMail" value="1"<?php if($ksNutzerAendAdmMail) echo' checked="checked"'?> /> E-Mail bei Datenänderung versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input style="width:100%" type="text" name="TxNutzAendAdmBtr" value="<?php echo $ksTxNutzAendAdmBtr?>" /><div class="admMini">Muster: <i>geänderter Kalender-Benutzer Nr. #N</i></div>
 <textarea name="TxNutzAendAdmTxt" rows="5" cols="80" style="height:5em"><?php echo str_replace('\n ',"\n",$ksTxNutzAendAdmTxt)?></textarea></div><div class="admMini">Muster: <i>Der Kalender-Benutzer Nr. #N hat seine Daten wie folgt geändert: #D</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierung-Mail<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerAktivMail" value="1"<?php if($ksNutzerAktivMail) echo' checked="checked"'?> /> E-Mail versenden &nbsp; <span class="admMini">(keine Empfehlung)</i></span></div>
 <input style="width:100%" type="text" name="TxNutzerAktivBtr" value="<?php echo $ksTxNutzerAktivBtr?>" /><div class="admMini">Muster: <i>Zugang aktiviert bei #A</i></div>
 <textarea name="TxNutzerAktivTxt" rows="5" cols="80" style="height:5em"><?php echo str_replace('\n ',"\n",$ksTxNutzerAktivTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Benutzerzugang bei #A wurde soeben vom Webmaster freigeschaltet. Hier Ihre Anmeldedaten: #D</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerDSE1" value="1"<?php if($ksNutzerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="NutzerDSE2" value="1"<?php if($ksNutzerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Neuanmeldung von Benutzern und Versand vergessener Passworte über ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzercaptcha</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="LoginCaptcha" value="1"<?php if($ksLoginCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>
<tr class="admTabl">
 <td colspan="3"><span class="admMini"><u>Hinweis</u>: Diese besondere Captcha-Einstellung gilt unabhängig von der Captcha-Aktivierung in den sonstigen Formularen, die Sie unter <i>Allgemeines</i> oder <i>Eingabeformular</i> vornehmen können. Es werden jedoch die gleichen Captcha-Farben wie beim allgemeinen Captcha verwendet.</span></td>
</tr>

<tr class="admTabl">
 <td colspan="3" class="admSpa2">Für Benutzer können in der Tabellenansicht, der Detailansicht und bei der Termineingabe und beim Terminändern einige besondere Rechte gelten.</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">versteckte Termine</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NVerstecktSehen" value="1"<?php if($ksNVerstecktSehen) echo ' checked="checked"'?> /> angemeldete Benutzer sollen versteckte Termine sehen können</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Tabellenansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NListeAnders" value="1"<?php if($ksNListeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Tabellenspalten sehen als Gäste (siehe Terminliste)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Detailansicht</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NDetailAnders" value="1"<?php if($ksNDetailAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Datenzeilen sehen als Gäste (siehe Detailanzeige)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Eingabeformular</td>
 <td colspan="2"><div><input type="checkbox" class="admCheck" name="NEingabeLogin" value="1"<?php if($ksNEingabeLogin) echo ' checked="checked"'?> /> <i>nur</i> angemeldete Benutzer sollen Eingaben und Änderungen ausführen können</div>
 <div><input type="checkbox" class="admCheck" name="NAendernFremde" value="1"<?php if($ksNAendernFremde) echo ' checked="checked"'?> /> angemeldete Benutzer sollen auch fremde Termine ändern dürfen <span class="admMini">(Empfehlung: <i>nein</i>)</span></div>
 <div><input type="checkbox" class="admCheck" name="NEingabeAnders" value="1"<?php if($ksNEingabeAnders) echo ' checked="checked"'?> /> angemeldete Benutzer sollen andere Eingabefelder sehen als Gäste (siehe Eingabeformular)</div></td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<div class="admBox">
<u>Hinweis:</u> Momentan ist die Benutzerverwaltung <span<?php echo ($bNutzer?'>':' style="color:#DD0022;">nicht')?></span> aktiviert.
<?php if($bNutzer&&in_array('p',$kal_FeldType)){?>
<p><span style="color:#AA0000;"><b>Warnung</b>: Sie haben jedoch <i>gleichzeitig</i> ein Feld vom Typ <i>Passwort</i> definiert.</span>
Das widerspricht eventuell dem Konzept für die Termineingabe.
Informieren Sie sich zu den Möglichkeiten der Termineingabe in der Hilfedatei <img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"> LiesMich.htm.</p>
<?php }?>
</div>

<?php

echo fSeitenFuss();

function fSetzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADM_'.$n)){
  $p=strpos($sWerte,'ADM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
function fSetz0Array($a,$n,$t){
 global $sWerte;
 $p=strpos($sWerte,'$kal_'.$n.'='); $e=strpos($sWerte,');',$p); $p=strpos($sWerte,'array(',$p);
 if($p>0&&$e>$p){
  $k=count($a); $s=$t.$a[0].$t; for($i=1;$i<$k;$i++) $s.=','.$t.(!empty($a[$i])?$a[$i]:'0').$t;
  $sWerte=substr_replace($sWerte,'array('.$s,$p,$e-$p); return true;
 }else return false;
}
?>