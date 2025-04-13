<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false;
echo fSeitenKopf('Benutzerverwaltung einstellen','<style type="text/css">#LoginGraueSchrift {color:#BBBBBB;} #LoginSchwarzeSchrift {color:000000;}</style>
<script type="text/javascript">
 var bSchwarz='.($_SERVER['REQUEST_METHOD']=='POST'?($_POST['NachLoginWohin']>'DatenX'?'false':'true'):(FRA_NachLoginWohin>'DatenX'?'false':'true')).'
 function setzeGrau(bGrau){
  if(bGrau==bSchwarz){
   bSchwarz=!bGrau;
   if(bGrau) document.getElementById("LoginSchwarzeSchrift").id="LoginGraueSchrift";
   else document.getElementById("LoginGraueSchrift").id="LoginSchwarzeSchrift";
  }
 }
 function registerWenn(s){
  document.getElementById("RegWenn").style.display=(s=="nachher"?"table-row":"none");
  return false;
 }
</script>','KBn');

if($_SERVER['REQUEST_METHOD']!='POST'){ //GET
 $aFelder=explode(';',FRA_NutzerFelder); $aPflicht=explode(';',FRA_NutzerPflicht); $nFelder=count($aFelder);
 for($i=0;$i<$nFelder;$i++) $aFelder[$i]=str_replace('`,',';',$aFelder[$i]);
 $fsNutzerverwaltung=FRA_Nutzerverwaltung; $fsNutzerzwang=FRA_Nutzerzwang; $fsNutzerfreigabe=FRA_Nutzerfreigabe;
 $fsNutzerSperre=FRA_NutzerSperre; $fsNutzerMitCode=FRA_NutzerMitCode; $fsTxNutzerSperre=FRA_TxNutzerSperre; $fsMaxSessionZeit=FRA_MaxSessionZeit;
 $fsTxNutzerLogin=FRA_TxNutzerLogin; $fsTxLoginErfassen=FRA_TxLoginErfassen; $fsTxNutzerNamePass=FRA_TxNutzerNamePass; $fsTxNutzerFalsch=FRA_TxNutzerFalsch;
 $fsTxNutzerOK=FRA_TxNutzerOK; $fsNachLoginWohin=FRA_NachLoginWohin; $fsSofortFrageNachDaten=(FRA_NachLoginWohin=='DatenKorr');
 $fsLoginGrenze=FRA_LoginGrenze; $fsLoginWenn=FRA_LoginWenn; $fsTxLoginNicht=FRA_TxLoginNicht;
 $fsNutzerStandardtest=FRA_NutzerStandardtest; $fsNutzerAlleFolgen=FRA_NutzerAlleFolgen; $fsNutzerSpontaneFolge=FRA_NutzerSpontaneFolge; $fsNutzerErgebnis=FRA_NutzerErgebnis; $fsZntErgebnisRueckw=FRA_ZntErgebnisRueckw;
 $fsNutzerTests=FRA_NutzerTests; $fsNutzerFrist=FRA_NutzerFrist; $fsTxNutzerFrist=FRA_TxNutzerFrist;
 $fsNutzerAendern=FRA_NutzerAendern; $fsTxNutzerPruefe=FRA_TxNutzerPruefe; $fsTxNutzerGeaendert=FRA_TxNutzerGeaendert;
 $fsNutzerLoesung=FRA_NutzerLoesung; $fsNutzerLsgAlle=FRA_NutzerLsgAlle; $fsNutzerStatistik=FRA_NutzerStatistik; $fsTxNutzerStatName=FRA_TxNutzerStatName;
 $fsTxEingabeFehl=FRA_TxEingabeFehl; $fsTxNutzerVergeben=FRA_TxNutzerVergeben; $fsTxNutzerNeu=FRA_TxNutzerNeu;
 $fsTxNutzerNameMail=FRA_TxNutzerNameMail; $fsTxNutzerSend=FRA_TxNutzerSend; $fsTxNutzerDatBtr=FRA_TxNutzerDatBtr; $fsTxNutzerDaten=FRA_TxNutzerDaten;
 $fsTxAktivieren=FRA_TxAktivieren; $fsTxAktiviert=FRA_TxAktiviert; $fsTxAktivFehl=FRA_TxAktivFehl;
 $fsNutzerNeuErlaubt=FRA_NutzerNeuErlaubt; $fsTxLoginNeu=FRA_TxLoginNeu;
 $fsNutzerNeuMail=FRA_NutzerNeuMail; $fsTxNutzerNeuBtr=FRA_TxNutzerNeuBtr; $fsTxNutzerNeuTxt=FRA_TxNutzerNeuTxt;
 $fsNutzerNeuAdmMail=FRA_NutzerNeuAdmMail; $fsTxNutzNeuAdmBtr=FRA_TxNutzNeuAdmBtr; $fsTxNutzNeuAdmTxt=FRA_TxNutzNeuAdmTxt;
 $fsNutzerAktivMail=FRA_NutzerAktivMail; $fsTxNutzerAktivBtr=FRA_TxNutzerAktivBtr; $fsTxNutzerAktivTxt=FRA_TxNutzerAktivTxt;
 $fsPasswortSenden=FRA_PasswortSenden; $fsTxLoginVergessen=FRA_TxLoginVergessen;
 $fsNutzerDSE1=FRA_NutzerDSE1; $fsNutzerDSE2=FRA_NutzerDSE2;
 $fsCaptcha=FRA_Captcha; $fsRegistrierung=FRA_Registrierung;
}else{ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $bToDo=true;
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $nFeldAnzahl=max((int)txtVar('FeldAnzahl'),4); $nFelder=substr_count(FRA_NutzerFelder,';')+1;
  $aFelder=array('Nummer','aktiv'); $sFelder='Nummer;aktiv'; $aPflicht=array(0,0,1,1,1); $sPflicht='0;0;1;1;1';
  for($i=2;$i<=$nFeldAnzahl;$i++){
   $aFelder[$i]=txtVar('F'.$i); $sFelder.=';'.str_replace(';','`,',$aFelder[$i]);
   if($i>4){$aPflicht[$i]=(!empty($aFelder[$i])?(isset($_POST['P'.$i])?(int)$_POST['P'.$i]:0):0); $sPflicht.=';'.(!empty($aFelder[$i])?$aPflicht[$i]:''); }
  }
  if(fSetzFraWert($sFelder,'NutzerFelder','"')){$bNeu=true; $bFelderNeu=true;}else $bFelderNeu=false;
  if(fSetzFraWert($sPflicht,'NutzerPflicht','"')) $bNeu=true;
  $s=(int)txtVar('Nutzerzwang'); if(fSetzFraWert(($s?true:false),'Nutzerzwang','')) $bNeu=true;
  $s=(int)txtVar('Nutzerfreigabe'); if(fSetzFraWert(($s?true:false),'Nutzerfreigabe','')) $bNeu=true;
  $s=(int)txtVar('NutzerSperre'); if(fSetzFraWert(($s?true:false),'NutzerSperre','')) $bNeu=true;
  $s=txtVar('TxNutzerSperre'); if(fSetzFraWert($s,'TxNutzerSperre',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerMitCode'); if(fSetzFraWert(($s?true:false),'NutzerMitCode','')) $bNeu=true;
  $s=txtVar('Nutzerverwaltung'); if($s=='') if($fsNutzerzwang) $s='vorher'; if(fSetzFraWert($s,'Nutzerverwaltung',"'")) $bNeu=true;
  $s=FRA_Registrierung; if($fsNutzerzwang) $s=''; elseif($fsNutzerverwaltung>'') $s=$fsNutzerverwaltung; if(fSetzFraWert($s,'Registrierung',"'")) $bNeu=true; //Registrierung angleichen
  $s=txtVar('TxNutzerLogin'); if(fSetzFraWert($s,'TxNutzerLogin','"')) $bNeu=true;
  $s=txtVar('TxLoginErfassen'); if(fSetzFraWert($s,'TxLoginErfassen','"')) $bNeu=true;
  $v=txtVar('LoginWenn'); if(fSetzFraWert($v,'LoginWenn',"'")) $bNeu=true;
  $v=max((int)txtVar('LoginGrenze'),1); if(fSetzFraWert($v,'LoginGrenze','')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxLoginNicht')))); if(fSetzFraWert($s,'TxLoginNicht',"'")) $bNeu=true;
  $s=min(max((int)txtVar('MaxSessionZeit'),60),300); if(fSetzFraWert($s,'MaxSessionZeit','')) $bNeu=true;
  $s=txtVar('NachLoginWohin'); if($s=='Daten') $s.=(txtVar('SofortFrageNachDaten')?'Korr':'Best'); if(fSetzFraWert($s,'NachLoginWohin',"'")) $bNeu=true;
  $s=min(max((int)txtVar('NutzerFrist'),0),4500); if(fSetzFraWert($s,'NutzerFrist','')) $bNeu=true;
  $s=txtVar('TxNutzerFrist'); if(fSetzFraWert($s,'TxNutzerFrist',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerTests'); if(fSetzFraWert(($s?true:false),'NutzerTests','')) $bNeu=true;
  $s=(int)txtVar('NutzerStandardtest'); if(fSetzFraWert(($s?true:false),'NutzerStandardtest','')) $bNeu=true;
  $s=(int)txtVar('NutzerAlleFolgen'); if(fSetzFraWert(($s?true:false),'NutzerAlleFolgen','')) $bNeu=true;
  $s=(int)txtVar('NutzerSpontaneFolge'); if(fSetzFraWert(($s?true:false),'NutzerSpontaneFolge','')) $bNeu=true;
  $s=(int)txtVar('NutzerErgebnis'); if(fSetzFraWert(($s?true:false),'NutzerErgebnis','')) $bNeu=true;
  $s=(int)txtVar('NutzerAendern'); if(fSetzFraWert(($s?true:false),'NutzerAendern','')) $bNeu=true;
  $s=(int)txtVar('NutzerLoesung'); if(fSetzFraWert(($s?true:false),'NutzerLoesung','')) $bNeu=true;
  $v=(int)txtVar('NutzerLsgAlle'); if(fSetzFraWert(($s>0&&$v>0?true:false),'NutzerLsgAlle','')) $bNeu=true;
  $v=(int)txtVar('NutzerStatistik'); if(fSetzFraWert(($v>0?true:false),'NutzerStatistik','')) $bNeu=true;
  $v=txtVar('TxNutzerStatName'); if(fSetzFraWert(($v?$v:'Statistik'),'TxNutzerStatName',"'")) $bNeu=true;
  $s=(int)txtVar('ZntErgebnisRueckw'); if(fSetzFraWert(($s?true:false),'ZntErgebnisRueckw','')) $bNeu=true;
  $s=txtVar('TxNutzerNamePass'); if(fSetzFraWert($s,'TxNutzerNamePass','"')) $bNeu=true;
  $s=txtVar('TxNutzerFalsch'); if(fSetzFraWert($s,'TxNutzerFalsch','"')) $bNeu=true;
  $s=txtVar('TxNutzerOK'); if(fSetzFraWert($s,'TxNutzerOK','"')) $bNeu=true;
  $s=txtVar('TxNutzerPruefe'); if(fSetzFraWert($s,'TxNutzerPruefe','"')) $bNeu=true;
  $s=txtVar('TxNutzerGeaendert'); if(fSetzFraWert($s,'TxNutzerGeaendert','"')) $bNeu=true;
  $s=txtVar('TxEingabeFehl'); if(fSetzFraWert($s,'TxEingabeFehl','"')) $bNeu=true;
  $s=txtVar('TxNutzerVergeben'); if(fSetzFraWert($s,'TxNutzerVergeben','"')) $bNeu=true;
  $s=txtVar('TxNutzerNeu'); if(fSetzFraWert($s,'TxNutzerNeu','"')) $bNeu=true;
  $s=txtVar('TxNutzerNameMail'); if(fSetzFraWert($s,'TxNutzerNameMail','"')) $bNeu=true;
  $s=txtVar('TxNutzerSend'); if(fSetzFraWert($s,'TxNutzerSend','"')) $bNeu=true;
  $s=txtVar('TxNutzerDatBtr'); if(fSetzFraWert($s,'TxNutzerDatBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerDaten')))); if(fSetzFraWert($s,'TxNutzerDaten',"'")) $bNeu=true;
  $s=txtVar('TxAktivieren'); if(fSetzFraWert($s,'TxAktivieren','"')) $bNeu=true;
  $s=txtVar('TxAktiviert'); if(fSetzFraWert($s,'TxAktiviert','"')) $bNeu=true;
  $s=txtVar('TxAktivFehl'); if(fSetzFraWert($s,'TxAktivFehl','"')) $bNeu=true;
  $s=(int)txtVar('NutzerNeuErlaubt'); if(fSetzFraWert(($s?true:false),'NutzerNeuErlaubt','')) $bNeu=true;
  $s=txtVar('TxLoginNeu'); if(fSetzFraWert($s,'TxLoginNeu','"')) $bNeu=true;
  $s=(int)txtVar('NutzerNeuMail'); if(fSetzFraWert(($s?true:false),'NutzerNeuMail','')) $bNeu=true;
  $s=txtVar('TxNutzerNeuBtr'); if(fSetzFraWert($s,'TxNutzerNeuBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerNeuTxt')))); if(fSetzFraWert($s,'TxNutzerNeuTxt',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerNeuAdmMail'); if(fSetzFraWert(($s?true:false),'NutzerNeuAdmMail','')) $bNeu=true;
  $s=txtVar('TxNutzNeuAdmBtr'); if(fSetzFraWert($s,'TxNutzNeuAdmBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzNeuAdmTxt')))); if(fSetzFraWert($s,'TxNutzNeuAdmTxt',"'")) $bNeu=true;
  $s=(int)txtVar('NutzerAktivMail'); if(fSetzFraWert(($s?true:false),'NutzerAktivMail','')) $bNeu=true;
  $s=txtVar('TxNutzerAktivBtr'); if(fSetzFraWert($s,'TxNutzerAktivBtr','"')) $bNeu=true;
  $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TxNutzerAktivTxt')))); if(fSetzFraWert($s,'TxNutzerAktivTxt',"'")) $bNeu=true;
  $s=(int)txtVar('PasswortSenden'); if(fSetzFraWert(($s?true:false),'PasswortSenden','')) $bNeu=true;
  $s=txtVar('TxLoginVergessen'); if(fSetzFraWert($s,'TxLoginVergessen','"')) $bNeu=true;
  $v=txtVar('NutzerDSE1'); if(fSetzFraWert(($v?true:false),'NutzerDSE1','')) $bNeu=true;
  $v=txtVar('NutzerDSE2'); if(fSetzFraWert(($v?true:false),'NutzerDSE2','')) $bNeu=true;
  $v=(int)txtVar('Captcha'); if(fSetzFraWert(($v?true:false),'Captcha','')) $bNeu=true;
  if($bNeu){ //geaendert
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    if($bToDo){
     $bToDo=false;
     if(!FRA_SQL&&$bFelderNeu){ //bei Textdatei
      $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $s=$aD[0]; $s=substr($s,0,strpos($s,';'));
      if(substr($s,0,7)!='Nummer_'){$nNutzerZahl=count($aD); $nMx=0; for($i=1;$i<$nNutzerZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5)); $s='Nummer_'.$nMx;}
      $aD[0]=$s.substr($sFelder,6).NL;
      if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
      else $sMeld.='<p class="admFehl">In die Datei <i>'.FRA_Daten.FRA_Nutzer.'</i> konnte nicht geschrieben werden!</p>';
     }elseif(FRA_SQL&&$nFeldAnzahl!=($nFelder-1)){ //bei SQL
      if($DbO){
       if($nFeldAnzahl>($nFelder-1)){ //mehr Felder
        for($i=$nFelder;$i<=$nFeldAnzahl;$i++) $DbO->query('ALTER TABLE '.FRA_SqlTabN.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""');
       }else{ //weniger Felder
        for($i=$nFelder;$i>$nFeldAnzahl;$i--) $DbO->query('ALTER TABLE '.FRA_SqlTabN.' DROP dat_'.$i);
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
 if(!$fsNutzerverwaltung) $sMeld.='<p class="admFehl">Die Benutzerverwaltung ist momentan inaktiv!</p>';
}
echo $sMeld.NL;
?>

<form name="NtzForm" action="konfNutzer.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Das Testfragen-Script kann mit einer Benutzerverwaltung gekoppelt sein.
 In diesem Falle werden Besucher (die entweder <i>unangemeldete Gäste</i> bzw. anderenfalls für die Dauer eines Tests
 <i>registrierte Teilnehmer</i> sind) von <i>angemeldeteten Benutzern</i> unterschieden.<br />
 Auf dieser Seite wird <i>nur</i> das Verhalten bezüglich der Benutzerverwaltung eingestellt.
 Die alternative <a href="konfTeilnehmer.php<?php if(KONF>0)echo'?konf='.KONF?>">Teilnehmerregistrierung</a> ist momentan <u><?php echo (strlen($fsRegistrierung)>0?'ein':'aus')?></u>geschaltet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzersperre</td>
 <td colspan="2"><input type="radio" class="admRadio" name="NutzerSperre" value="0"<?php if(!$fsNutzerSperre) echo' checked="checked"'?> /> Testdurchführung für angemeldete Benutzer erlaubt&nbsp;
  <input type="radio" class="admRadio" name="NutzerSperre" value="1"<?php if($fsNutzerSperre) echo' checked="checked"'?> /> Durchführung für Benutzer gesperrt
  <div><input type="text" name="TxNutzerSperre" value="<?php echo $fsTxNutzerSperre?>" style="width:99%;" /></div>
  <div class="admMini">Muster: <i>Der Zugang für Benutzer ist momentan gesperrt.</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerzwang</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerzwang" value="1"<?php if($fsNutzerzwang) echo' checked="checked"'?> /> Testdurchführung nur für angemeldete Benutzer&nbsp;
  <input type="radio" class="admRadio" name="Nutzerzwang" value="0"<?php if(!$fsNutzerzwang) echo' checked="checked"'?> /> Testdurchführung auch für Gäste/Teilnehmer</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerverwaltung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerverwaltung" value=""<?php if(!$fsNutzerverwaltung) echo' checked="checked"'?> onclick="registerWenn(this.value)" /> ohne Benutzeranmeldung<br>
  <input type="radio" class="admRadio" name="Nutzerverwaltung" value="vorher"<?php if($fsNutzerverwaltung=='vorher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> Benutzeranmeldung vor dem Test &nbsp;
  <input type="radio" class="admRadio" name="Nutzerverwaltung" value="nachher"<?php if($fsNutzerverwaltung=='nachher') echo' checked="checked"'?> onclick="registerWenn(this.value)" /> Benutzerlogin nach dem Test</td>
</tr>
<tr class="admTabl" id="RegWenn" style="display:<?php echo($fsNutzerverwaltung=='nachher'?'table-row':'none')?>">
 <td class="admSpa1">bedingtes<br>Benutzerlogin<div class="admMini">(gilt nur, falls Login<br>nach dem Test)</div>
 <div style="margin-top:7.4em">Abweise-Meldung</div><div class="admMini">(Benutzer-Login)</div></td>
 <td colspan="2"><input type="radio" class="admRadio" name="LoginWenn" value="PProz<?php if($fsLoginWenn=='PProz') echo'" checked="checked'?>" /> nur Login, wenn mindestens <?php echo $fsLoginGrenze?>% der Punkte erreicht wurden (Positivwertung)<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="PPkte<?php if($fsLoginWenn=='PPkte') echo'" checked="checked'?>" /> nur Login, wenn mindestens <?php echo $fsLoginGrenze?> Punkte erreicht wurden (Positivwertung)<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="NProz<?php if($fsLogintWenn=='NProz') echo'" checked="checked'?>" /> nur Login, wenn höchstens <?php echo $fsLoginGrenze?>% der Fehlerpunkte zustande kamen (Negativwertung)<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="NPkte<?php if($fsLoginWenn=='NPkte') echo'" checked="checked'?>" /> nur Login, wenn höchstens <?php echo $fsLoginGrenze?> Fehlerpunkte zustande kamen (Negativwertung)<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="PRtig<?php if($fsLoginWenn=='PRtig') echo'" checked="checked'?>" /> nur Login, wenn mindestens <?php echo $fsLoginGrenze?> richtige Antworten erreicht wurden<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="PFehl<?php if($fsLoginWenn=='PFehl') echo'" checked="checked'?>" /> nur Login, wenn höchstens <?php echo $fsLoginGrenze?> falsche Antworten zustande kamen<br>
  <input type="radio" class="admRadio" name="LoginWenn" value="<?php if(empty($fsLoginWenn)) echo'" checked="checked'?>" /> unabhängig vom Ergebnis immer anmelden
  <div><input type="text" name="LoginGrenze" value="<?php echo $fsLoginGrenze?>" size="3" style="width:3em" /> Grenzwert in <i>Prozent</i> oder in <i>Punkten</i> oder in <i>Antworten</i></div>
  <textarea name="TxLoginNicht" rows="4" style="width:99%;height:4.5em;"><?php echo str_replace('\n ',"\n",$fsTxLoginNicht)?></textarea>
  <div class="admMini"><u>Muster</u>: Sie haben weniger als <?php echo $fsLoginGrenze?> Punkte erreicht. Ihr Versuch wird nicht gewertet.</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Loginverhalten<div class="admMini">gilt nur, falls <br>Benutzeranmeldung <br><i>vor</i> dem Test</div></td>
 <td colspan="2">
 <input type="radio" class="admRadio" name="NachLoginWohin" value="Daten"<?php if($fsNachLoginWohin<'DatenX') echo' checked="checked"'?> onclick="setzeGrau(false)" onkeyup="setzeGrau(false)" /> nach dem Login die momentanen Benutzerdaten zur Kontrolle/Korrektur anzeigen
 <div id="Login<?php echo $fsNachLoginWohin>'DatenX'?'Grau':'Schwarz'?>eSchrift" style="padding-left:18px;">
 <input type="radio" class="admRadio" name="SofortFrageNachDaten" value="0"<?php if($fsNachLoginWohin=='DatenBest') echo' checked="checked"'?> /> nach einer Korrektur die Benutzerdaten noch einmal zur Bestätigung anzeigen<br />
 <input type="radio" class="admRadio" name="SofortFrageNachDaten" value="1"<?php if($fsNachLoginWohin=='DatenKorr') echo' checked="checked"'?> /> nach der Kontrolle/Korrektur der Benutzerdaten sofort zu den Fragen
 </div>
 <input type="radio" class="admRadio" name="NachLoginWohin" value="FragenA"<?php if($fsNachLoginWohin=='FragenA') echo' checked="checked"'?> onclick="setzeGrau(true)" onkeyup="setzeGrau(true)" /> nach dem Login als Benutzer sofort zu den Fragen laut genereller <i>Testfragenauswahl</i><br />
 <input type="radio" class="admRadio" name="NachLoginWohin" value="FragenB"<?php if($fsNachLoginWohin=='FragenB') echo' checked="checked"'?> onclick="setzeGrau(true)" onkeyup="setzeGrau(true)" /> nach dem Login als Benutzer sofort zum individuellen Test laut<i> Benutzer und Tests</i><br />
 <input type="radio" class="admRadio" name="NachLoginWohin" value="Zentrum"<?php if($fsNachLoginWohin=='Zentrum') echo' checked="checked"'?> onclick="setzeGrau(true)" onkeyup="setzeGrau(true)" /> nach dem Login zum Benutzerzentrum</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzercode</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerMitCode" value="1"<?php if($fsNutzerMitCode) echo' checked="checked"'?> /> Testdurchführung für Benutzer nur nach Eingabe eines 4-stelligen Aktiv-Codes
 <div class="admMini"><u>Empfehlung</u>: nicht einschalten, nur in seltenen Situationen sinnvoll</div>
 <div class="admMini"><u>Erklärung</u>:Beispielsweise könnten mehrere Gruppen/Klassen parallel unterschiedliche Tests bearbeiten ohne die Gefahr, dass einzelnen Teilnehmer den falschen Test starten, da jeder Gruppe/Klasse nur der Aktiv-Code bekanntgegeben wird, der für ihren Test aktuell gültig ist.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">max. Sitzungszeit</td>
 <td colspan="2"><input type="text" name="MaxSessionZeit" value="<?php echo $fsMaxSessionZeit?>" size="2" /> 60...300 Minuten &nbsp; <span class="admMini">(nur falls Teilnehmerverwaltung/Benutzerverwaltung aktiv)</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Benutzer können nach dem Login und nach einem absolvierten Test
in das Benutzerzentrum (Benutzermenü) geführt werden, von dem aus sie weitere Aktionen auswählen können. Was soll im Benutzerzentrum angeboten werden?</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Testzuordnung<br>zum Benutzer</td>
 <td colspan="2">
  <input type="radio" class="admRadio" name="NutzerTests" value="1"<?php if($fsNutzerTests) echo' checked="checked"'?> /> pro Benutzer sollen individuell Tests angeboten werden können<br>
  <input type="radio" class="admRadio" name="NutzerTests" value="0"<?php if(!$fsNutzerTests) echo' checked="checked"'?> /> für alle Benutzer sollen einheitlich nachfolgende Tests angeboten werden
 </td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern keine individuellen Testzuordnungen zu einem Benutzer vorliegen:</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standardtest</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerStandardtest" value="1"<?php if($fsNutzerStandardtest) echo' checked="checked"'?> /> Benutzern soll der Standardtestablauf angeboten werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Spontanfolge</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerSpontaneFolge" value="1"<?php if($fsNutzerSpontaneFolge) echo' checked="checked"'?> /> die momentane <i>spontane Fragenfolge</i> aus der <i>Testfragenauswahl</i> anbieten</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenfolgen</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="NutzerAlleFolgen" value="1"<?php if($fsNutzerAlleFolgen) echo' checked="checked"'?> /> Liste der <i>gespeicherten Fragenfolgen</i> aus der <i>Testfragenauswahl</i> anbieten</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Ausserdem sollen im Benutzerzentrum angeboten werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnisliste</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerErgebnis" value="1"<?php if($fsNutzerErgebnis) echo' checked="checked"'?> /> Benutzer sollen ihre bisherigen Ergebnisse und Bewertungen einsehen können
 <div style="padding-left:15px"><input type="radio" class="admRadio" name="ZntErgebnisRueckw" value=""<?php if(!$fsZntErgebnisRueckw) echo ' checked="checked"'?> /> aufsteigend &nbsp; <input type="radio" class="admRadio" name="ZntErgebnisRueckw" value="1"<?php if($fsZntErgebnisRueckw) echo ' checked="checked"'?> /> absteigend &nbsp; nach dem Testdatum </div>
 <div style="margin-top:3px"><input type="checkbox" class="admCheck" name="NutzerLoesung" value="1"<?php if($fsNutzerLoesung) echo' checked="checked"'?> /> dabei auch die Lösungsseiten zur Einsicht anbieten</div>
 <div style="padding-left:15px"><input type="checkbox" class="admCheck" name="NutzerLsgAlle" value="1"<?php if($fsNutzerLsgAlle) echo' checked="checked"'?> /> und zwar komplett einschließlich richtig beantworteter Fragen</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bestenliste oder<br>Statistik</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerStatistik" value="1"<?php if($fsNutzerStatistik) echo' checked="checked"'?> /> Benutzer sollen im Benutzerzentrum die Bestenliste/Statistik
 als <input type="text" name="TxNutzerStatName" value="<?php echo $fsTxNutzerStatName?>" size="12 style="width:12em;" /> einsehen können
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2">
 <input type="checkbox" class="admCheck" name="NutzerAendern" value="1"<?php if($fsNutzerAendern) echo' checked="checked"'?> /> Benutzer sollen im Benutzerzentrum ihre Benutzerdaten ändern können
 </td>
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
 <td colspan="2"><input type="text" name="NutzerFrist" value="<?php echo ($fsNutzerFrist<=0?'':$fsNutzerFrist)?>" style="width:9em;" /><div class="admMini"><i>Leer</i> lassen oder <i>Anzahl der Tage</i>, die bei einem neuen Benutzer automatisch eingetragen werden sollen.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">angezeigter<br>Feldname</td>
 <td colspan="2"><input type="text" name="TxNutzerFrist" value="<?php echo $fsTxNutzerFrist?>" style="width:9em;" /> <span class="admMini"><u>Empfehlung</u>: <i>gültig bis</i></span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für die Benutzeranmeldung (Loginformular) werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Start</td>
 <td colspan="2"><input type="text" name="TxNutzerLogin" value="<?php echo $fsTxNutzerLogin?>" style="width:99%;" /><div class="admMini">Muster: <i>Melden Sie sich für die Testbenutzung an!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Fehler</td>
 <td colspan="2"><input type="text" name="TxNutzerNamePass" value="<?php echo $fsTxNutzerNamePass?>" style="width:99%;" /><div class="admMini">Muster: <i>Bitte Benutzernamen und Passwort angeben!</i></div>
 <input type="text" name="TxNutzerFalsch" value="<?php echo $fsTxNutzerFalsch?>" style="width:99%;" /><div class="admMini">Muster: <i>Ein Benutzer mit diesen Daten ist nicht verzeichnet!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Login-Erfolg</td>
 <td colspan="2"><input type="text" name="TxNutzerOK" value="<?php echo $fsTxNutzerOK?>" style="width:99%;" /><div class="admMini">Muster: <i>Sie sind nun angemeldet und können die gewünschte Aktion ausführen.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular mit den Benutzerdaten werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerdaten</td>
 <td colspan="2"><input type="text" name="TxNutzerPruefe" value="<?php echo $fsTxNutzerPruefe?>" style="width:99%;" /><div class="admMini">Muster: <i>Prüfen und bestätigen Sie bitte Ihre Benutzerdaten!</i></div>
 <input type="text" name="TxNutzerGeaendert" value="<?php echo $fsTxNutzerGeaendert?>" style="width:99%;" /><div class="admMini">Muster: <i>Die geänderten Benutzerdaten wurden eingetragen!</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls außer der Benutzeranmeldung (Loginformular) auch und zusätzlich nur die einfache Teilnehmerregistrierung angeboten werden soll (bei <i>Benutzerzwang</i> ausgeschaltet):</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerregistrierung</td>
 <td colspan="2"><input type="text" name="TxLoginErfassen" value="<?php echo $fsTxLoginErfassen?>" style="width:99%;" /><div class="admMini">Muster: <i>Registrierung nur für diesen Testdurchlauf.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Versand eines vergessenen Passwortes werden folgende Einstellungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortformular</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="PasswortSenden" value="1"<?php if($fsPasswortSenden) echo' checked="checked"'?> /> Formularbereich zum Zusenden vergessener Passwörter im Login-Formular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Formularkopf</td>
 <td colspan="2"><input type="text" name="TxLoginVergessen" value="<?php echo $fsTxLoginVergessen?>" style="width:99%;" />
 <div class="admMini">Muster: <i>vergessenes Passwort zusenden</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Passwortmeldungen</td>
 <td colspan="2"><input type="text" name="TxNutzerNameMail" value="<?php echo $fsTxNutzerNameMail?>" style="width:99%;" /><div class="admMini">Muster: <i>Bitte Benutzernamen oder E-Mail-Adresse angeben!</i></div>
 <input type="text" name="TxNutzerSend" value="<?php echo $fsTxNutzerSend?>" style="width:99%;" /><div class="admMini">Muster: <i>Die Zugangsdaten wurden soeben versandt!</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Versandbetreff<div style="margin-top:20px;">Versandtext</div></td>
 <td colspan="2"><input type="text" name="TxNutzerDatBtr" value="<?php echo $fsTxNutzerDatBtr?>" style="width:99%;" /><div class="admMini">Muster: <i>Zugangsdaten bei #</i></div>
 <textarea name="TxNutzerDaten" style="width:99%;height:8em;"><?php echo str_replace('\n ',"\n",$fsTxNutzerDaten)?></textarea></div><div class="admMini">Muster: <i>Sie haben soeben Ihre Zugangsdaten zum Testfragen-Script auf #A angefordert. Diese lauten: lfd. Nummer: #N Benutzer: #B Passwort: #P</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Im öffentlichen Anmeldeformular für Benutzer (Loginformular) kann auch ein Bereich zum Neuanlegen eines Benutzers vorhanden sein, über den Gäste einen Benutzerzugang selbst beantragen können.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Neuanmeldung</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerNeuErlaubt" value="1"<?php if($fsNutzerNeuErlaubt) echo' checked="checked"'?> /> Formularbereich zur Neuanmeldung für Gäste im Loginformular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Formularkopf</td>
 <td colspan="2"><input type="text" name="TxLoginNeu" value="<?php echo $fsTxLoginNeu?>" style="width:99%;" />
 <div class="admMini">Muster: <i>Benutzerzugang jetzt beantragen</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über dem Formular für eine Neuanmeldung werden folgende Meldungen verwendet:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerneuanmeldung</td>
 <td colspan="2"><input type="text" name="TxEingabeFehl" value="<?php echo $fsTxEingabeFehl?>" style="width:99%;" /><div class="admMini">Muster: <i>Ergänzen Sie bei den rot markierten Feldern!</i></div>
 <input type="text" name="TxNutzerVergeben" value="<?php echo $fsTxNutzerVergeben?>" style="width:99%;" /><div class="admMini">Muster: <i>Dieser Benutzername ist bereits vergeben!</i></div>
 <input type="text" name="TxNutzerNeu" value="<?php echo $fsTxNutzerNeu?>" style="width:99%;" /><div class="admMini">Muster: <i>Die Benutzerdaten wurden eingetragen und der Webmaster informiert!</i><br />oder: <i>Vielen Dank! Sie erhalten eine Bestätigung per E-Mail.</i><br />oder: <i>Vielen Dank! Sie sind nun als Benutzer angemeldet.</i></div></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Nach dem Anlegen eines neuen Benutzers muss dieser üblicherweise erst gesondert freigeschaltet werden. Das erfolgt entweder durch den Administrator oder als Selbstfreischaltung durch den neuen Benutzer über einen Link in einer automatisch versandten E-Mail. Als Ausnahme kann die Freischaltung bereits im Zuge der Neuanmeldung erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerfreischaltung</td>
 <td colspan="2"><input type="radio" class="admRadio" name="Nutzerfreigabe" value="0"<?php if(!$fsNutzerfreigabe) echo' checked="checked"'?> /> gesonderte Freischaltung nötig (<i>Standard</i>) &nbsp;
  <input type="radio" class="admRadio" name="Nutzerfreigabe" value="1"<?php if($fsNutzerfreigabe) echo' checked="checked"'?> /> Freischaltung direkt bei der Erfassung</td>
</tr>


<tr class="admTabl"><td colspan="3" class="admSpa2">Beim Anlegen neuer Benutzer können E-Mails versandt werden. Diese gehen an die Webmasteradresse und/oder an den neuen Benutzer.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Nutzer<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuMail" value="1"<?php if($fsNutzerNeuMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzerNeuBtr" value="<?php echo $fsTxNutzerNeuBtr?>" style="width:99%;" /><div class="admMini">Muster: <i>Ihre Anmeldung bei #</i></div>
 <textarea name="TxNutzerNeuTxt" style="width:99%;height:8em;"><?php echo str_replace('\n ',"\n",$fsTxNutzerNeuTxt)?></textarea></div><div class="admMini">Muster: <i>Ihre Anmeldung bei #A wurde registriert. Hier Ihre Anmeldedaten: #D</i>&nbsp; oder<br/><i>Ihre Anmeldung bei #A wurde registriert. Bitte bestätigen Sie die Anmeldung über den Link #L<br />Hier Ihre Anmeldedaten: #D</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail an Admin<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerNeuAdmMail" value="1"<?php if($fsNutzerNeuAdmMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzNeuAdmBtr" value="<?php echo $fsTxNutzNeuAdmBtr?>" style="width:99%;" /><div class="admMini">Muster: <i>neuer Testfragen-Script-Benutzer Nr. #</i></div>
 <textarea name="TxNutzNeuAdmTxt" style="width:99%;height:5em;"><?php echo str_replace('\n ',"\n",$fsTxNutzNeuAdmTxt)?></textarea></div><div class="admMini">Muster: <i>Ein neuer Testfragen-Script-Benutzer Nr. #N hat sich wie folgt angemeldet: #D</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierung-Mail<div style="margin-top:5px;">Betreff</div><div style="margin-top:15px;">Text</div></td>
 <td colspan="2"><div><input class="admCheck" type="checkbox" name="NutzerAktivMail" value="1"<?php if($fsNutzerAktivMail) echo' checked="checked"'?> /> E-Mail versenden</div>
 <input type="text" name="TxNutzerAktivBtr" value="<?php echo $fsTxNutzerAktivBtr?>" style="width:99%;" /><div class="admMini">Muster: <i>Zugang aktiviert bei #</i></div>
 <textarea name="TxNutzerAktivTxt" style="width:99%;height:5em;"><?php echo str_replace('\n ',"\n",$fsTxNutzerAktivTxt)?></textarea></div><div class="admMini">Muster: <i>Ihr Benutzerzugang bei #A wurde soeben vom Webmaster freigeschaltet. Hier Ihre Anmeldedaten: #D</i></td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Falls neue Benutzer sich nach einer Registrierung durch den per E-Mail zugesandten Freischaltlink <a href="<?php echo ADF_Hilfe ?>LiesMich.htm#2.5" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> selbst aktivieren können, wird über dem Aktivierungsformular angezeigt:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Aktivierungsmeldungen</td>
 <td colspan="2"><input type="text" name="TxAktivieren" value="<?php echo $fsTxAktivieren?>" style="width:99%;" /><div class="admMini">Muster: <i>Benutzerzugang jetzt aktivieren?</i></div>
 <input type="text" name="TxAktiviert" value="<?php echo $fsTxAktiviert?>" style="width:99%;" /><div class="admMini">Muster: <i>Ihr Benutzerzugang wurde aktiviert!</i></div>
 <input type="text" name="TxAktivFehl" value="<?php echo $fsTxAktivFehl?>" style="width:99%;" /><div class="admMini">Muster: <i>Der Freischaltcode ist ungültig!</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Nutzerdaten-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="NutzerDSE1" value="1"<?php if($fsNutzerDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="NutzerDSE2" value="1"<?php if($fsNutzerDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Registrierung/Anmeldung von Benutzern und Versand vergessener Passworte über ein Captcha absichern?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="Captcha" value="1"<?php if($fsCaptcha) echo' checked="checked"'?> /> verwenden</td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen</p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>