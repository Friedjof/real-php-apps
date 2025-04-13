<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Benutzerverwaltung einstellen','','KNt');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $aFelder=explode(';',UMF_NutzerFelder); $aPflicht=explode(';',UMF_NutzerPflicht); $nFelder=count($aFelder);
 for($i=0;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
 $usNutzerverwaltung=UMF_Nutzerverwaltung; $usNutzerzwang=UMF_Nutzerzwang; $usNutzerfreigabe=UMF_Nutzerfreigabe;
 $usNutzerSperre=UMF_NutzerSperre; $usNutzerMitCode=UMF_NutzerMitCode; $usTxNutzerSperre=UMF_TxNutzerSperre; $usMaxSessionZeit=UMF_MaxSessionZeit;
 $usTxNutzerLogin=UMF_TxNutzerLogin; $usTxLoginErfassen=UMF_TxLoginErfassen; $usTxNutzerNamePass=UMF_TxNutzerNamePass; $usTxNutzerFalsch=UMF_TxNutzerFalsch;
 $usTxNutzerOK=UMF_TxNutzerOK; $usNachLoginWohin=UMF_NachLoginWohin;
 $usTxLoginNicht=UMF_TxLoginNicht; $usNutzerLog=UMF_NutzerLog;
 $usNutzerNormUmfrage=UMF_NutzerNormUmfrage; $usNutzerAlleUmfrage=UMF_NutzerAlleUmfrage; $usNutzerErgebnis=UMF_NutzerErgebnis; $usZntErgebnisRueckw=UMF_ZntErgebnisRueckw;
 $usNutzerUmfragen=UMF_NutzerUmfragen; $usNutzerFrist=UMF_NutzerFrist; $usTxNutzerFrist=UMF_TxNutzerFrist;
 $usNutzerAendern=UMF_NutzerAendern; $usTxNutzerPruefe=UMF_TxNutzerPruefe; $usTxNutzerGeaendert=UMF_TxNutzerGeaendert;
 $usNutzerGrafik=UMF_NutzerGrafik; $usNutzerDrucken=UMF_NutzerDrucken; $usGrafikOhneLogin=UMF_GrafikOhneLogin;
 $usTxEingabeFehl=UMF_TxEingabeFehl; $usTxNutzerVergeben=UMF_TxNutzerVergeben; $usTxNutzerNeu=UMF_TxNutzerNeu;
 $usTxNutzerNameMail=UMF_TxNutzerNameMail; $usTxNutzerSend=UMF_TxNutzerSend; $usTxNutzerDatBtr=UMF_TxNutzerDatBtr; $usTxNutzerDaten=UMF_TxNutzerDaten;
 $usTxAktivieren=UMF_TxAktivieren; $usTxAktiviert=UMF_TxAktiviert; $usTxAktivFehl=UMF_TxAktivFehl;
 $usNutzerNeuErlaubt=UMF_NutzerNeuErlaubt; $usTxLoginNeu=UMF_TxLoginNeu;
 $usNutzerNeuMail=UMF_NutzerNeuMail; $usTxNutzerNeuBtr=UMF_TxNutzerNeuBtr; $usTxNutzerNeuTxt=UMF_TxNutzerNeuTxt;
 $usNutzerNeuAdmMail=UMF_NutzerNeuAdmMail; $usTxNutzNeuAdmBtr=UMF_TxNutzNeuAdmBtr; $usTxNutzNeuAdmTxt=UMF_TxNutzNeuAdmTxt;
 $usNutzerAktivMail=UMF_NutzerAktivMail; $usTxNutzerAktivBtr=UMF_TxNutzerAktivBtr; $usTxNutzerAktivTxt=UMF_TxNutzerAktivTxt;
 $usPasswortSenden=UMF_PasswortSenden; $usTxLoginVergessen=UMF_TxLoginVergessen;
 $usNutzerDSE1=UMF_NutzerDSE1; $usNutzerDSE2=UMF_NutzerDSE2;
 $usCaptcha=UMF_Captcha; $usRegistrierung=UMF_Registrierung;
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $bToDo=true;
 $aFelder=explode(';',UMF_NutzerFelder); $aPflicht=explode(';',UMF_NutzerPflicht); $nFelder=count($aFelder);
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $nFeldAnzahl=max((int)txtVar('FeldAnzahl'),4); $nFelder=substr_count(UMF_NutzerFelder,';')+1;
  $aFelder=array('Nummer','aktiv'); $sFelder='Nummer;aktiv'; $aPflicht=array(0,0,1,1,1); $sPflicht='0;0;1;1;1';
  for($i=2;$i<=$nFeldAnzahl;$i++){
   $aFelder[$i]=txtVar('F'.$i); $sFelder.=';'.str_replace(';','`,',$aFelder[$i]);
   if($i>4){$aPflicht[$i]=(!empty($aFelder[$i])?(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0):0); $sPflicht.=';'.(!empty($aFelder[$i])?$aPflicht[$i]:''); }
  }
  if(fSetzUmfWert($sFelder,'NutzerFelder','"')){$bNeu=true; $bFelderNeu=true;}else $bFelderNeu=false;
  if(fSetzUmfWert($sPflicht,'NutzerPflicht','"')) $bNeu=true;
  $s=(int)txtVar('Nutzerzwang'); if(fSetzUmfWert(($s?true:false),'Nutzerzwang','')) $bNeu=true;
  $s=(int)txtVar('Nutzerfreigabe'); if(fSetzUmfWert(($s?true:false),'Nutzerfreigabe','')) $bNeu=true;
  $s=(int)txtVar('NutzerSperre'); if(fSetzUmfWert(($s?true:false),'NutzerSperre','')) $bNeu=true;
  $s=txtVar('TxNutzerSperre'); if(fSetzUmfWert($s,'TxNutzerSperre',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerMitCode'); if(fSetzUmfWert(($s?true:false),'NutzerMitCode','')) $bNeu=true;
  $s=txtVar('Nutzerverwaltung'); if($s=='') if($usNutzerzwang) $s='vorher'; if(fSetzUmfWert($s,'Nutzerverwaltung',"'")) $bNeu=true;
  $s=UMF_Registrierung; if($usNutzerzwang) $s=''; elseif($usNutzerverwaltung>'') $s=$usNutzerverwaltung; if(fSetzUmfWert($s,'Registrierung',"'")) $bNeu=true; //Registrierung angleichen
  $s=txtVar('TxNutzerLogin'); if(fSetzUmfWert($s,'TxNutzerLogin','"')) $bNeu=true;
  $s=txtVar('TxLoginErfassen'); if(fSetzUmfWert($s,'TxLoginErfassen','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxLoginNicht')))); if(fSetzUmfWert($s,'TxLoginNicht',"'")) $bNeu=true;
  $s=min(max((int)txtVar('MaxSessionZeit'),60),300); if(fSetzUmfWert($s,'MaxSessionZeit','')) $bNeu=true;
  $v=(int)txtVar('NutzerLog'); if(fSetzUmfWert(($v?true:false),'NutzerLog','')) $bNeu=true;
  $s=txtVar('NachLoginWohin'); if(fSetzUmfWert($s,'NachLoginWohin',"'")) $bNeu=true;
  $s=min(max((int)txtVar('NutzerFrist'),0),4500); if(fSetzUmfWert($s,'NutzerFrist','')) $bNeu=true;
  $s=txtVar('TxNutzerFrist'); if(fSetzUmfWert($s,'TxNutzerFrist',"'")) $bNeu=true;
  $s=(int)txtVar('GrafikOhneLogin'); if(fSetzUmfWert(($s?true:false),'GrafikOhneLogin','')) $bNeu=true;
  $s=(int)txtVar('NutzerUmfragen'); if(fSetzUmfWert(($s?true:false),'NutzerUmfragen','')) $bNeu=true;
  $s=(int)txtVar('NutzerNormUmfrage'); if(fSetzUmfWert(($s?true:false),'NutzerNormUmfrage','')) $bNeu=true;
  $s=(int)txtVar('NutzerAlleUmfrage'); if(fSetzUmfWert(($s?true:false),'NutzerAlleUmfrage','')) $bNeu=true;
  $s=(int)txtVar('NutzerErgebnis'); if(fSetzUmfWert(($s?true:false),'NutzerErgebnis','')) $bNeu=true;
  $s=(int)txtVar('NutzerAendern'); if(fSetzUmfWert(($s?true:false),'NutzerAendern','')) $bNeu=true;
  $v=(int)txtVar('NutzerGrafik'); if(fSetzUmfWert(($v>0?true:false),'NutzerGrafik','')) $bNeu=true;
  $v=(int)txtVar('NutzerDrucken'); if(fSetzUmfWert(($v>0?true:false),'NutzerDrucken','')) $bNeu=true; if($v&&strlen(UMF_TxDrucken)==0) fSetzUmfWert('Drucken','TxDrucken',"'");
  $s=(int)txtVar('ZntErgebnisRueckw'); if(fSetzUmfWert(($s?true:false),'ZntErgebnisRueckw','')) $bNeu=true;
  $s=txtVar('TxNutzerNamePass'); if(fSetzUmfWert($s,'TxNutzerNamePass','"')) $bNeu=true;
  $s=txtVar('TxNutzerFalsch'); if(fSetzUmfWert($s,'TxNutzerFalsch','"')) $bNeu=true;
  $s=txtVar('TxNutzerOK'); if(fSetzUmfWert($s,'TxNutzerOK','"')) $bNeu=true;
  $s=txtVar('TxNutzerPruefe'); if(fSetzUmfWert($s,'TxNutzerPruefe','"')) $bNeu=true;
  $s=txtVar('TxNutzerGeaendert'); if(fSetzUmfWert($s,'TxNutzerGeaendert','"')) $bNeu=true;
  $s=txtVar('TxEingabeFehl'); if(fSetzUmfWert($s,'TxEingabeFehl','"')) $bNeu=true;
  $s=txtVar('TxNutzerVergeben'); if(fSetzUmfWert($s,'TxNutzerVergeben','"')) $bNeu=true;
  $s=txtVar('TxNutzerNeu'); if(fSetzUmfWert($s,'TxNutzerNeu','"')) $bNeu=true;
  $s=txtVar('TxNutzerNameMail'); if(fSetzUmfWert($s,'TxNutzerNameMail','"')) $bNeu=true;
  $s=txtVar('TxNutzerSend'); if(fSetzUmfWert($s,'TxNutzerSend','"')) $bNeu=true;
  $s=txtVar('TxNutzerDatBtr'); if(fSetzUmfWert($s,'TxNutzerDatBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerDaten')))); if(fSetzUmfWert($s,'TxNutzerDaten',"'")) $bNeu=true;
  $s=txtVar('TxAktivieren'); if(fSetzUmfWert($s,'TxAktivieren','"')) $bNeu=true;
  $s=txtVar('TxAktiviert'); if(fSetzUmfWert($s,'TxAktiviert','"')) $bNeu=true;
  $s=txtVar('TxAktivFehl'); if(fSetzUmfWert($s,'TxAktivFehl','"')) $bNeu=true;
  $s=(int)txtVar('NutzerNeuErlaubt'); if(fSetzUmfWert(($s?true:false),'NutzerNeuErlaubt','')) $bNeu=true;
  $s=txtVar('TxLoginNeu'); if(fSetzUmfWert($s,'TxLoginNeu','"')) $bNeu=true;
  $s=(int)txtVar('NutzerNeuMail'); if(fSetzUmfWert(($s?true:false),'NutzerNeuMail','')) $bNeu=true;
  $s=txtVar('TxNutzerNeuBtr'); if(fSetzUmfWert($s,'TxNutzerNeuBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerNeuTxt')))); if(fSetzUmfWert($s,'TxNutzerNeuTxt',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerNeuAdmMail'); if(fSetzUmfWert(($s?true:false),'NutzerNeuAdmMail','')) $bNeu=true;
  $s=txtVar('TxNutzNeuAdmBtr'); if(fSetzUmfWert($s,'TxNutzNeuAdmBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzNeuAdmTxt')))); if(fSetzUmfWert($s,'TxNutzNeuAdmTxt',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerAktivMail'); if(fSetzUmfWert(($s?true:false),'NutzerAktivMail','')) $bNeu=true;
  $s=txtVar('TxNutzerAktivBtr'); if(fSetzUmfWert($s,'TxNutzerAktivBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerAktivTxt')))); if(fSetzUmfWert($s,'TxNutzerAktivTxt',"'")) $bNeu=true;
  $s=(int)txtVar('PasswortSenden'); if(fSetzUmfWert(($s?true:false),'PasswortSenden','')) $bNeu=true;
  $s=txtVar('TxLoginVergessen'); if(fSetzUmfWert($s,'TxLoginVergessen','"')) $bNeu=true;
  $v=txtVar('NutzerDSE1'); if(fSetzUmfWert(($v?true:false),'NutzerDSE1','')) $bNeu=true;
  $v=txtVar('NutzerDSE2'); if(fSetzUmfWert(($v?true:false),'NutzerDSE2','')) $bNeu=true;
  $v=(int)txtVar('Captcha'); if(fSetzUmfWert(($v?true:false),'Captcha','')) $bNeu=true;
  if($bNeu){ //geaendert
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    if($bToDo){
     $bToDo=false;
     if(!UMF_SQL&&$bFelderNeu){ //bei Textdatei
      $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $s=$aD[0]; $s=substr($s,0,strpos($s,';'));
      if(substr($s,0,7)!='Nummer_'){$nNutzerZahl=count($aD); $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5)); $s='Nummer_'.$nMx;}
      $aD[0]=$s.substr($sFelder,6).NL;
      if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
      else $sMeld.='<p class="admFehl">In die Datei <i>'.UMF_Daten.UMF_Nutzer.'</i> konnte nicht geschrieben werden!</p>';
     }elseif(UMF_SQL&&$nFeldAnzahl!=$nFelder){ //bei SQL
      if($DbO){
       if($nFeldAnzahl>$nFelder){ //mehr Felder
        for($i=$nFelder;$i<=$nFeldAnzahl;$i++) $DbO->query('ALTER TABLE '.UMF_SqlTabN.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""');
       }else{ //weniger Felder
        for($i=$nFelder;$i>$nFeldAnzahl;$i--) $DbO->query('ALTER TABLE '.UMF_SqlTabN.' DROP dat_'.$i);
       }
      }else $sMeld.='<p class="admFehl">Keine MySQL-Verbindung mit den vorliegenden Zugangsdaten!</p>';
     }//SQL
    }//bToDo
    $nFelder=$nFeldAnzahl+1;
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Benutzer-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Benutzer-Einstellungen bleiben unverändert.</p>';

}//POST

//Seitenausgabe
if(!$sMeld){
 $sMeld.='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für die Benutzerverwaltung.</p>';
 if(!$usNutzerverwaltung) $sMeld.='<p class="admFehl">Die Benutzerverwaltung ist momentan inaktiv!</p>';
}
echo $sMeld.NL;
?>

<form name="NtzForm" action="konfNutzer.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Das Umfrage-Script kann mit einer Benutzerverwaltung gekoppelt sein.
 In diesem Falle werden Besucher (die entweder <i>unangemeldete Gäste</i> bzw. anderenfalls für die Dauer einer Umfrage
 <i>registrierte Teilnehmer</i> sind) von <i>angemeldeteten Benutzern</i> unterschieden.<br />
 Auf dieser Seite wird <i>nur</i> das Verhalten bezüglich der Benutzerverwaltung eingestellt.
 Die alternative <a href="konfTeilnehmer.php<?php if(KONF>0)echo'?konf='.KONF?>">Teilnehmerregistrierung</a> ist momentan <u><?php echo (strlen($usRegistrierung)>0?'ein':'aus')?></u>geschaltet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzersperre</td>
 <td colspan="2"><input type="radio" class="admRadio" name="NutzerSperre" value="0"<?php if(!$usNutzerSperre) echo' checked="checked"'?> /> Umfragen für angemeldete Benutzer erlaubt&nbsp;
  <input type="radio" class="admRadio" name="NutzerSperre" value="1"<?php if($usNutzerSperre) echo' checked="checked"'?> /> Durchführung für Benutzer gesperrt
  <div><input type="text" name="TxNutzerSperre" value="<?php echo $usTxNutzerSperre?>" style="width:98%;" /></div>
  <div class="admMini">Muster: <i>Der Zugang für Benutzer ist momentan gesperrt.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerzwang</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerzwang" value="1"<?php if($usNutzerzwang) echo' checked="checked"'?> /> Umfragen nur für angemeldete Benutzer&nbsp;
  <input type="radio" class="admRadio" name="Nutzerzwang" value="0"<?php if(!$usNutzerzwang) echo' checked="checked"'?> /> Umfragen auch für Gäste/Teilnehmer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerverwaltung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerverwaltung" value=""<?php if(!$usNutzerverwaltung) echo' checked="checked"'?> /> ohne Benutzeranmeldung<br>
  <input type="radio" class="admRadio" name="Nutzerverwaltung" value="vorher"<?php if($usNutzerverwaltung=='vorher') echo' checked="checked"'?> /> Benutzeranmeldung vor der Umfrage &nbsp;
  <input type="radio" class="admRadio" name="Nutzerverwaltung" value="nachher"<?php if($usNutzerverwaltung=='nachher') echo' checked="checked"'?> /> Benutzerlogin nach der Umfrage</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Loginverhalten</td>
 <td colspan="2">
 <input type="radio" class="admRadio" name="NachLoginWohin" value="Daten"<?php if($usNachLoginWohin<'DatenX') echo' checked="checked"'?> /> nach dem Login die Benutzerdaten zur Kontrolle/Korrektur anzeigen<br />
 <input type="radio" class="admRadio" name="NachLoginWohin" value="FragenA"<?php if($usNachLoginWohin=='FragenA') echo' checked="checked"'?> /> nach dem Login als Benutzer zur Umfrage laut genereller <i>Umfrageauswahl</i><br />
 <input type="radio" class="admRadio" name="NachLoginWohin" value="FragenB"<?php if($usNachLoginWohin=='FragenB') echo' checked="checked"'?> /> nach dem Login als Benutzer zur individuellen Umfrage laut <i>Benutzer und Umfragen</i><br />
 <input type="radio" class="admRadio" name="NachLoginWohin" value="Zentrum"<?php if($usNachLoginWohin=='Zentrum') echo' checked="checked"'?> /> nach dem Login zum Benutzerzentrum</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Auswertegrafik</td>
 <td colspan="2"><input type="checkbox" class="admRadio" name="GrafikOhneLogin" value="1"<?php if($usGrafikOhneLogin) echo' checked="checked"'?> /> Auswertegrafik immer ohne Login / vorbei am Login abrufbar</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzercode</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerMitCode" value="1"<?php if($usNutzerMitCode) echo' checked="checked"'?> /> Umfragedurchführung für Benutzer nur nach Eingabe eines 4-stelligen Aktiv-Codes
 <div class="admMini"><u>Empfehlung</u>: nicht einschalten, nur in seltenen Situationen sinnvoll</div>
 <div class="admMini"><u>Erklärung</u>:Beispielsweise könnten mehrere Gruppen/Klassen parallel unterschiedliche Umfragen bearbeiten ohne die Gefahr, dass einzelnen Teilnehmer die falsche Umfrage starten, da jeder Gruppe/Klasse nur der Aktiv-Code bekanntgegeben wird, der für ihre Umfrage aktuell gültig ist.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">max. Sitzungszeit</td>
 <td colspan="2"><input type="text" name="MaxSessionZeit" value="<?php echo $usMaxSessionZeit?>" size="2" /> 60...300 Minuten &nbsp; <span class="admMini">(nur falls Teilnehmerverwaltung/Benutzerverwaltung aktiv)</span></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Nach der Abstimmung werden die Antworten in jedem Fall in der Ergebnisliste anonym aufsummiert.
Darüberhinaus können die Daten in einer zusätzlichen Teilnahmeliste aus Datum/Uhrzeit, Personendaten und Antwortfolge abgelegt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnahmeliste</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerLog" value="1"<?php if($usNutzerLog) echo '" checked="checked'?>" /> Abstimmung von Benutzern aufzeichnen</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Die Benutzer können nach dem Login und nach einer absolvierten Umfrage
in das Benutzerzentrum (Benutzermenü) geführt werden, von dem aus sie weitere Aktionen auswählen können. Was soll im Benutzerzentrum angeboten werden?</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Umfragenzuordnung<br>zum Benutzer</td>
 <td colspan="2">
  <input type="radio" class="admRadio" name="NutzerUmfragen" value="1"<?php if($usNutzerUmfragen) echo' checked="checked"'?> /> pro Benutzer sollen individuell Umfragen angeboten werden können<br>
  <input type="radio" class="admRadio" name="NutzerUmfragen" value="0"<?php if(!$usNutzerUmfragen) echo' checked="checked"'?> /> für alle Benutzer sollen einheitlich nachfolgende Umfragen angeboten werden
 </td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern keine individuellen Umfragezuordnungen zu einem Benutzer vorliegen:</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardumfrage</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerNormUmfrage" value="1"<?php if($usNutzerNormUmfrage) echo' checked="checked"'?> /> Benutzern soll die Standardumfrage angeboten werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">vorbereitete<br />Umfragen</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerAlleUmfrage" value="1"<?php if($usNutzerAlleUmfrage) echo' checked="checked"'?> /> Liste der <i>vorbereiteten Umfragen</i> anbieten</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Ausserdem sollen im Benutzerzentrum angeboten werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnisliste</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerErgebnis" value="1"<?php if($usNutzerErgebnis) echo' checked="checked"'?> /> Benutzer sollen bisherigen Abstimmungsergebnisse einsehen können
 <div style="padding-left:15px"><input type="radio" class="admRadio" name="ZntErgebnisRueckw" value=""<?php if(!$usZntErgebnisRueckw) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input type="radio" class="admRadio" name="ZntErgebnisRueckw" value="1"<?php if($usZntErgebnisRueckw) echo ' checked="checked"'?> /> absteigend &nbsp; nach dem Umfragedatum </div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Auswertegrafik</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerGrafik" value="1"<?php if($usNutzerGrafik) echo' checked="checked"'?> /> Benutzer sollen die Grafische Auswertung einsehen können
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerAendern" value="1"<?php if($usNutzerAendern) echo' checked="checked"'?> /> Benutzer sollen im Benutzerzentrum ihre Benutzerdaten ändern können
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Drucken erlauben</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerDrucken" value="1"<?php if($usNutzerDrucken) echo' checked="checked"'?> /> Liste der Fragen und Antworten in der Druckversion anbieten</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">In der Benutzerverwaltung können folgenden Informationen über anzumeldende Benutzer gesammelt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenfeldanzahl</td>
 <td colspan="2"><input type="text" name="FeldAnzahl" value="<?php echo ($nFelder-1)?>" size="2" /> maximale Anzahl der Datenfelder in der Benutzerverwaltung &nbsp; <span class="admMini">(Empfehlung: 5 ... max. 15)</span></td>
</tr>

<tr class="admTabl"><td class="admSpa1"><b>Datenfeld</b></td><td><b>Bezeichnung&nbsp;/&nbsp;Pflichtfeld</b></td><td><b>Hinweis</b></td></tr>
<tr class="admTabl">
 <td class="admSpa1">0. Nummer</td>
 <td><span style="width:100px;">Nummer</span> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="13" height="13" border="0" title="Pflichtfeld"></td>
 <td>fortlaufende Benutzernummer bis höchstens 9999</td></tr>
<tr class="admTabl">
 <td class="admSpa1">1. Status</td>
 <td><span style="width:100px;">aktiv</span> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="13" height="13" border="0" title="Pflichtfeld"></td>
 <td>zum Freigeben bzw. Sperren registrierter Benutzer</td></tr>
<tr class="admTabl">
 <td class="admSpa1">2. Benutzername</td>
 <td><input type="text" name="F2" value="<?php echo $aFelder[2]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="13" height="13" border="0" title="Pflichtfeld"></td>
 <td rowspan="3" valign="top"><p>Auch wenn Sie diese 3 Felder anders benennen bleiben deren Funktion als <i>Benutzername</i>, <i>Passwort</i> und <i>E-Mail-Adresse</i> erhalten.</p></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">3. Passwort</td>
 <td><input type="text" name="F3" value="<?php echo $aFelder[3]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="13" height="13" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">4. E-Mail-Adresse</td>
 <td><input type="text" name="F4" value="<?php echo $aFelder[4]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="iconHaken.gif" width="13" height="13" border="0" title="Pflichtfeld"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">5. Feld</td>
 <td><input type="text" name="F5" value="<?php echo $aFelder[5]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P5" value="1"<?php if($aPflicht[5]) echo' checked="checked"'?> /></td>
 <td rowspan="<?php echo $nFelder-4?>" valign="top"><p>z.B. Anrede, Name, Anschrift, Telefon, Fax usw.</p></td>
</tr>

<?php  for($i=6;$i<$nFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1"><?php echo $i?>. Feld</td>
 <td><input type="text" name="F<?php echo $i?>" value="<?php echo $aFelder[$i]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P<?php echo $i?>" value="1"<?php if($aPflicht[$i]) echo' checked="checked"'?> /></td>
</tr>

<?php }?>

<tr class="admTabl"><td colspan="3" class="admSpa2"><u>Hinweis</u>: Sofern die Benutzerdaten ein Feld mit der Bezeichnung <i>GUELTIG_BIS</i> (in genau der Schreibweise) enthalten wird dieses als Ablaufdatum der Benutzermitgliedschaft interpretiert. Das Feld kann dann wie folgt behandelt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">automatische<br>Benutzerfrist</td>
 <td colspan="2"><input type="text" name="NutzerFrist" value="<?php echo ($usNutzerFrist<=0?'':$usNutzerFrist)?>" style="width:9em;" /><div class="admMini"><i>Leer</i> lassen oder <i>Anzahl der Tage</i>, die bei einem neuen Benutzer automatisch eingetragen werden sollen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">angezeigter<br>Feldname</td>
 <td colspan="2"><input type="text" name="TxNutzerFrist" value="<?php echo $usTxNutzerFrist?>" style="width:9em;" /> <span class="admMini"><u>Empfehlung</u>: <i>gültig bis</i></span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für die Benutzeranmeldung (Loginformular) werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Start</td>
 <td colspan="2"><input type="text" name="TxNutzerLogin" value="<?php echo $usTxNutzerLogin?>" style="width:98%;" /><div class="admMini">Muster: <i>Melden Sie sich für die Umfragedurchführung an!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Fehler</td>
 <td colspan="2"><input type="text" name="TxNutzerNamePass" value="<?php echo $usTxNutzerNamePass?>" style="width:98%;" /><div class="admMini">Muster: <i>Bitte Benutzernamen und Passwort angeben!</i></div>
 <input type="text" name="TxNutzerFalsch" value="<?php echo $usTxNutzerFalsch?>" style="width:98%;" /><div class="admMini">Muster: <i>Ein Benutzer mit diesen Daten ist nicht verzeichnet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Erfolg</td>
 <td colspan="2"><input type="text" name="TxNutzerOK" value="<?php echo $usTxNutzerOK?>" style="width:98%;" /><div class="admMini">Muster: <i>Sie sind nun angemeldet und können die gewünschte Aktion ausführen.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular mit den Benutzerdaten werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2"><input type="text" name="TxNutzerPruefe" value="<?php echo $usTxNutzerPruefe?>" style="width:98%;" /><div class="admMini">Muster: <i>Prüfen und bestätigen Sie bitte Ihre Benutzerdaten!</i></div>
 <input type="text" name="TxNutzerGeaendert" value="<?php echo $usTxNutzerGeaendert?>" style="width:98%;" /><div class="admMini">Muster: <i>Die geänderten Benutzerdaten wurden eingetragen!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls außer der Benutzeranmeldung (Loginformular) auch und zusätzlich nur die einfache Teilnehmerregistrierung angeboten werden soll (bei <i>Benutzerzwang</i> ausgeschaltet):</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerregistrierung</td>
 <td colspan="2"><input type="text" name="TxLoginErfassen" value="<?php echo $usTxLoginErfassen?>" style="width:98%;" /><div class="admMini">Muster: <i>Registrierung nur für diesen einen Umfragedurchlauf.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Versand eines vergessenen Passwortes werden folgende Einstellungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortformular</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="PasswortSenden" value="1"<?php if($usPasswortSenden) echo' checked="checked"'?> /> Formularbereich zum Zusenden vergessener Passwörter im Login-Formular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Formularkopf</td>
 <td colspan="2"><input type="text" name="TxLoginVergessen" value="<?php echo $usTxLoginVergessen?>" style="width:98%;" />
 <div class="admMini">Muster: <i>vergessenes Passwort zusenden</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortmeldungen</td>
 <td colspan="2"><input type="text" name="TxNutzerNameMail" value="<?php echo $usTxNutzerNameMail?>" style="width:98%;" /><div class="admMini">Muster: <i>Bitte Benutzernamen oder E-Mail-Adresse angeben!</i></div>
 <input type="text" name="TxNutzerSend" value="<?php echo $usTxNutzerSend?>" style="width:98%;" /><div class="admMini">Muster: <i>Die Zugangsdaten wurden soeben versandt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Versandbetreff<div style="margin-top:20px;">Versandtext</div></td>
 <td colspan="2"><input type="text" name="TxNutzerDatBtr" value="<?php echo $usTxNutzerDatBtr?>" style="width:98%;" /><div class="admMini">Muster: <i>Zugangsdaten bei #</i></div>
 <textarea name="TxNutzerDaten" style="width:98%;height:8em;"><?php echo str_replace('\n ',"\n",$usTxNutzerDaten)?></textarea></div><div class="admMini">Muster: <i>Sie haben soeben Ihre Zugangsdaten zum Umfrage-Script auf #A angefordert. Diese lauten: lfd. Nummer: #N Benutzer: #B Passwort: #P</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Im öffentlichen Anmeldeformular für Benutzer (Loginformular) kann auch ein Bereich zum Neuanlegen eines Benutzers vorhanden sein, über den Gäste einen Benutzerzugang selbst beantragen können.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Neuanmeldung</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerNeuErlaubt" value="1"<?php if($usNutzerNeuErlaubt) echo' checked="checked"'?> /> Formularbereich zur Neuanmeldung für Gäste im Loginformular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Formularkopf</td>
 <td colspan="2"><input type="text" name="TxLoginNeu" value="<?php echo $usTxLoginNeu?>" style="width:98%;" />
 <div class="admMini">Muster: <i>Benutzerzugang jetzt beantragen</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für eine Neuanmeldung werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerneuanmeldung</td>
 <td colspan="2"><input type="text" name="TxEingabeFehl" value="<?php echo $usTxEingabeFehl?>" style="width:98%;" /><div class="admMini">Muster: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div>
 <input type="text" name="TxNutzerVergeben" value="<?php echo $usTxNutzerVergeben?>" style="width:98%;" /><div class="admMini">Muster: <i>Dieser Benutzername ist bereits vergeben!</i></div>
 <input type="text" name="TxNutzerNeu" value="<?php echo $usTxNutzerNeu?>" style="width:98%;" /><div class="admMini">Muster: <i>Die Benutzerdaten wurden eingetragen und der Webmaster informiert!</i><br />oder: <i>Vielen Dank! Sie erhalten eine Bestätigung per E-Mail.</i><br />oder: <i>Vielen Dank! Sie sind nun als Benutzer angemeldet.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Nach dem Anlegen eines neuen Benutzers muss dieser üblicherweise erst gesondert freigeschaltet werden. Das erfolgt entweder durch den Administrator oder als Selbstfreischaltung durch den neuen Benutzer über einen Link in einer automatisch versandten E-Mail. Als Ausnahme kann die Freischaltung bereits im Zuge der Neuanmeldung erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerfreischaltung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerfreigabe" value="0"<?php if(!$usNutzerfreigabe) echo' checked="checked"'?> /> gesonderte Freischaltung nötig (<i>Standard</i>) &nbsp;
  <input type="radio" class="admRadio" name="Nutzerfreigabe" value="1"<?php if($usNutzerfreigabe) echo' checked="checked"'?> /> Freischaltung direkt bei der Erfassung</td>
</tr>


<tr class="admTabl"><td colspan="3" class="admSpa2">Beim Anlegen neuer Benutzer können E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an den neuen Benutzer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Nutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuMail" value="1"<?php if($usNutzerNeuMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzerNeuBtr" value="<?php echo $usTxNutzerNeuBtr?>" style="width:98%;" /><div class="admMini">Muster: <i>Ihre Anmeldung bei #</i></div>
 <textarea name="TxNutzerNeuTxt" style="width:98%;height:8em;"><?php echo str_replace('\n ',"\n",$usTxNutzerNeuTxt)?></textarea></div><div class="admMini">Muster: <i>Ihre Anmeldung bei #A wurde registriert. Hier Ihre Anmeldedaten: #D</i>&nbsp; oder<br/><i>Ihre Anmeldung bei #A wurde registriert. Bitte bestätigen Sie die Anmeldung über den Link #L<br />Hier Ihre Anmeldedaten: #D</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Admin<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuAdmMail" value="1"<?php if($usNutzerNeuAdmMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzNeuAdmBtr" value="<?php echo $usTxNutzNeuAdmBtr?>" style="width:98%;" /><div class="admMini">Muster: <i>neuer Umfrage-Script-Benutzer Nr. #</i></div>
 <textarea name="TxNutzNeuAdmTxt" style="width:98%;height:5em;"><?php echo str_replace('\n ',"\n",$usTxNutzNeuAdmTxt)?></textarea></div><div class="admMini">Muster: <i>Ein neuer Umfrage-Script-Benutzer Nr. #N hat sich wie folgt angemeldet: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierung-Mail<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerAktivMail" value="1"<?php if($usNutzerAktivMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzerAktivBtr" value="<?php echo $usTxNutzerAktivBtr?>" style="width:98%;" /><div class="admMini">Muster: <i>Zugang aktiviert bei #</i></div>
 <textarea name="TxNutzerAktivTxt" style="width:98%;height:5em;"><?php echo str_replace('\n ',"\n",$usTxNutzerAktivTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Benutzerzugang bei #A wurde soeben vom Webmaster freigeschaltet. Hier Ihre Anmeldedaten: #D</i></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls neue Benutzer sich nach einer Registrierung durch den per E-Mail zugesandten Freischaltlink selbst aktivieren können, wird über dem Aktivierungsformular angezeigt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierungsmeldungen</td>
 <td colspan="2"><input type="text" name="TxAktivieren" value="<?php echo $usTxAktivieren?>" style="width:98%;" /><div class="admMini">Muster: <i>Benutzerzugang jetzt aktivieren?</i></div>
 <input type="text" name="TxAktiviert" value="<?php echo $usTxAktiviert?>" style="width:98%;" /><div class="admMini">Muster: <i>Ihr Benutzerzugang wurde aktiviert!</i></div>
 <input type="text" name="TxAktivFehl" value="<?php echo $usTxAktivFehl?>" style="width:98%;" /><div class="admMini">Muster: <i>Der Freischaltcode ist ungültig!</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerDSE1" value="1"<?php if($usNutzerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="NutzerDSE2" value="1"<?php if($usNutzerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Registrierung/Anmeldung von Benutzern und Versand vergessener Passworte über ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="Captcha" value="1"<?php if($usCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>