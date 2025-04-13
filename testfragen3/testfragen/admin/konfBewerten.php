<?php
include 'hilfsFunktionen.php'; $bAlleKonf=false; $sKonfAlle='';
echo fSeitenKopf('Bewertungsregeln festlegen','<script language="JavaScript" type="text/javascript">
function druWin(href){dWin=window.open(href,"druck","width=820,height=570,left=5,top=5,menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dWin.focus(); return true;}
</script>','KBr');

if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  if(fSetzFraWert((isset($_POST['PositivWertung'])&&$_POST['PositivWertung']?true:false),'PositivWertung','')) $bNeu=true;
  $v=(int)txtVar('TeilWertung'); if(fSetzFraWert($v,'TeilWertung','')) $bNeu=true;
  $v=(int)txtVar('PunkteTeilen'); if(fSetzFraWert(($v?true:false),'PunkteTeilen','')) $bNeu=true;
  $v=txtVar('TxZwischenwertung'); if(fSetzFraWert($v,'TxZwischenwertung','"')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVorAw')?true:false),'BldVorAw','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVorAw')?true:false),'TlnVorAw','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnPersonO')?true:false),'TlnPersonO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmPersonO')?true:false),'AdmPersonO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldTestName')?true:false),'BldTestName','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnTestName')?true:false),'TlnTestName','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmTestName')?true:false),'AdmTestName','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAnzahlO')?true:false),'BldAnzahlO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAnzahlO')?true:false),'TlnAnzahlO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAnzahlO')?true:false),'AdmAnzahlO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntAnzahlO')?true:false),'ZntAnzahlO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatAnzahlO')?true:false),'DatAnzahlO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldZeitO')?true:false),'BldZeitO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnZeitO')?true:false),'TlnZeitO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmZeitO')?true:false),'AdmZeitO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntZeitO')?true:false),'ZntZeitO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatZeitO')?true:false),'DatZeitO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldRichtigeO')?true:false),'BldRichtigeO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnRichtigeO')?true:false),'TlnRichtigeO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmRichtigeO')?true:false),'AdmRichtigeO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntRichtigeO')?true:false),'ZntRichtigeO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatRichtigeO')?true:false),'DatRichtigeO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldFalscheO')?true:false),'BldFalscheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnFalscheO')?true:false),'TlnFalscheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmFalscheO')?true:false),'AdmFalscheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntFalscheO')?true:false),'ZntFalscheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatFalscheO')?true:false),'DatFalscheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldPunkteO')?true:false),'BldPunkteO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnPunkteO')?true:false),'TlnPunkteO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmPunkteO')?true:false),'AdmPunkteO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntPunkteO')?true:false),'ZntPunkteO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatPunkteO')?true:false),'DatPunkteO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVerbalO')?true:false),'BldVerbalO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVerbalO')?true:false),'TlnVerbalO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVerbalO')?true:false),'AdmVerbalO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntVerbalO')?true:false),'ZntVerbalO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatVerbalO')?true:false),'DatVerbalO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatVerbalL')?true:false),'DatVerbalL','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVersucheO')?true:false),'BldVersucheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVersucheO')?true:false),'TlnVersucheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVersucheO')?true:false),'AdmVersucheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntVersucheO')?true:false),'ZntVersucheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatVersucheO')?true:false),'DatVersucheO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAuslassenO')?true:false),'BldAuslassenO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAuslassenO')?true:false),'TlnAuslassenO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAuslassenO')?true:false),'AdmAuslassenO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntAuslassenO')?true:false),'ZntAuslassenO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatAuslassenO')?true:false),'DatAuslassenO','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAlleNr')?true:false),'BldAlleNr','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAlleNr')?true:false),'TlnAlleNr','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAlleNr')?true:false),'AdmAlleNr','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntFrageNr')?true:false),'ZntFrageNr','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatFrageNr')?true:false),'DatFrageNr','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntAntwort')?true:false),'ZntAntwort','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntLoesung')?true:false),'ZntLoesung','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldErgebnis')?true:false),'BldErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnErgebnis')?true:false),'TlnErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmErgebnis')?true:false),'AdmErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntErgebnis')?true:false),'ZntErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatErgebnis')?true:false),'DatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldPunkte')?true:false),'BldPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnPunkte')?true:false),'TlnPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmPunkte')?true:false),'AdmPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntPunkte')?true:false),'ZntPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatPunkte')?true:false),'DatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVersuche')?true:false),'BldVersuche','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVersuche')?true:false),'TlnVersuche','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVersuche')?true:false),'AdmVersuche','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntVersuche')?true:false),'ZntVersuche','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatVersuche')?true:false),'DatVersuche','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAuslassen')?true:false),'BldAuslassen','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAuslassen')?true:false),'TlnAuslassen','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAuslassen')?true:false),'AdmAuslassen','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntAuslassen')?true:false),'ZntAuslassen','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatAuslassen')?true:false),'DatAuslassen','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldKatErgebnis')?true:false),'BldKatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnKatErgebnis')?true:false),'TlnKatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmKatErgebnis')?true:false),'AdmKatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntKatErgebnis')?true:false),'ZntKatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatKatErgebnis')?true:false),'DatKatErgebnis','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldKatFehlErgb')?true:false),'BldKatFehlErgb','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnKatFehlErgb')?true:false),'TlnKatFehlErgb','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmKatFehlErgb')?true:false),'AdmKatFehlErgb','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntKatFehlErgb')?true:false),'ZntKatFehlErgb','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatKatFehlErgb')?true:false),'DatKatFehlErgb','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldKatPunkte')?true:false),'BldKatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnKatPunkte')?true:false),'TlnKatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmKatPunkte')?true:false),'AdmKatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntKatPunkte')?true:false),'ZntKatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatKatPunkte')?true:false),'DatKatPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldKatSumme')?true:false),'BldKatSumme','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnKatSumme')?true:false),'TlnKatSumme','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmKatSumme')?true:false),'AdmKatSumme','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ZntKatSumme')?true:false),'ZntKatSumme','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatKatSumme')?true:false),'DatKatSumme','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAnzahlU')?true:false),'BldAnzahlU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAnzahlU')?true:false),'TlnAnzahlU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAnzahlU')?true:false),'AdmAnzahlU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldZeitU')?true:false),'BldZeitU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnZeitU')?true:false),'TlnZeitU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmZeitU')?true:false),'AdmZeitU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldRichtigeU')?true:false),'BldRichtigeU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnRichtigeU')?true:false),'TlnRichtigeU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmRichtigeU')?true:false),'AdmRichtigeU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldFalscheU')?true:false),'BldFalscheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnFalscheU')?true:false),'TlnFalscheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmFalscheU')?true:false),'AdmFalscheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldPunkteU')?true:false),'BldPunkteU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnPunkteU')?true:false),'TlnPunkteU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmPunkteU')?true:false),'AdmPunkteU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVerbalU')?true:false),'BldVerbalU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVerbalU')?true:false),'TlnVerbalU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVerbalU')?true:false),'AdmVerbalU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldVersucheU')?true:false),'BldVersucheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVersucheU')?true:false),'TlnVersucheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVersucheU')?true:false),'AdmVersucheU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('BldAuslassenU')?true:false),'BldAuslassenU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAuslassenU')?true:false),'TlnAuslassenU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAuslassenU')?true:false),'AdmAuslassenU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnAntwort')?true:false),'TlnAntwort','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmAntwort')?true:false),'AdmAntwort','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatAntwort')?true:false),'DatAntwort','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnVerlauf')?true:false),'TlnVerlauf','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmVerlauf')?true:false),'AdmVerlauf','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatVerlauf')?true:false),'DatVerlauf','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnPersonU')?true:false),'TlnPersonU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('AdmPersonU')?true:false),'AdmPersonU','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatPersonL')?true:false),'DatPersonL','')) $bNeu=true;
  if(fSetzFraWert((txtVar('DatPersonD')?true:false),'DatPersonD','')) $bNeu=true;
  $v=txtVar('DatPersonN'); if(fSetzFraWert($v,'DatPersonN',"'")) $bNeu=true;
  $v=txtVar('DatPersonT'); if(fSetzFraWert($v,'DatPersonT',"'")) $bNeu=true;
  if(fSetzFraWert((txtVar('BldNachAw')?true:false),'BldNachAw','')) $bNeu=true;
  if(fSetzFraWert((txtVar('TlnNachAw')?true:false),'TlnNachAw','')) $bNeu=true;
  $v=(int)txtVar('BewertungsSeite'); if(fSetzFraWert(($v?true:false),'BewertungsSeite','')) $bNeu=true;
  $v=txtVar('TxBewertungHier'); if(fSetzFraWert($v,'TxBewertungHier','"')) $bNeu=true;
  $v=txtVar('TxZeitLimit'); if(fSetzFraWert($v,'TxZeitLimit','"')) $bNeu=true;
  $v=(int)txtVar('VerbalAb1'); if(fSetzFraWert($v,'VerbalAb1','')) $bNeu=true; $v=(int)txtVar('VerbalAb2'); if(fSetzFraWert($v,'VerbalAb2','')) $bNeu=true;
  $v=(int)txtVar('VerbalAb3'); if(fSetzFraWert($v,'VerbalAb3','')) $bNeu=true; $v=(int)txtVar('VerbalAb4'); if(fSetzFraWert($v,'VerbalAb4','')) $bNeu=true;
  $v=(int)txtVar('VerbalAb5'); if(fSetzFraWert($v,'VerbalAb5','')) $bNeu=true; $v=(int)txtVar('VerbalAb6'); if(fSetzFraWert($v,'VerbalAb6','')) $bNeu=true;
  $v=txtVar('VerbalTx1'); if(fSetzFraWert($v,'VerbalTx1','"')) $bNeu=true;$v=txtVar('VerbalTx2'); if(fSetzFraWert($v,'VerbalTx2','"')) $bNeu=true;
  $v=txtVar('VerbalTx3'); if(fSetzFraWert($v,'VerbalTx3','"')) $bNeu=true;$v=txtVar('VerbalTx4'); if(fSetzFraWert($v,'VerbalTx4','"')) $bNeu=true;
  $v=txtVar('VerbalTx5'); if(fSetzFraWert($v,'VerbalTx5','"')) $bNeu=true;$v=txtVar('VerbalTx6'); if(fSetzFraWert($v,'VerbalTx6','"')) $bNeu=true;
  $v=txtVar('VerbalTx0'); if(fSetzFraWert($v,'VerbalTx0','"')) $bNeu=true; $v=txtVar('VerbalPunkte'); if(fSetzFraWert(((int)$v?true:false),'VerbalPunkte','')) $bNeu=true;
  if(fSetzFraWert((txtVar('ErgDruckTempl')?true:false),'ErgDruckTempl','')) $bNeu=true;
  $v=txtVar('TxErgDruckKopf'); if(fSetzFraWert($v,'TxErgDruckKopf','"')) $bNeu=true; $v=txtVar('TxErgDruckFuss'); if(fSetzFraWert($v,'TxErgDruckFuss','"')) $bNeu=true;
  $v=(int)txtVar('LoesungsSeite'); if(fSetzFraWert(($v?true:false),'LoesungsSeite','')) $bNeu=true;
  $v=(int)txtVar('LoesungsFalsche'); if(fSetzFraWert(($v?true:false),'LoesungsFalsche','')) $bNeu=true;
  $v=max((int)txtVar('LoesungsAnmk'),(int)txtVar('LoesungsAnm2')); if(fSetzFraWert(($v?$v:false),'LoesungsAnmk','')) $bNeu=true;
  $v=txtVar('TxZeigeLoesung'); if(fSetzFraWert($v,'TxZeigeLoesung',"'")) $bNeu=true;
  $v=txtVar('TxZeigeWertung'); if(fSetzFraWert($v,'TxZeigeWertung',"'")) $bNeu=true;
  $v=txtVar('TxZeigeAdmPkte'); if(fSetzFraWert($v,'TxZeigeAdmPkte',"'")) $bNeu=true;
  $v=txtVar('TxZeigeRichtig'); if(fSetzFraWert($v,'TxZeigeRichtig',"'")) $bNeu=true;
  $v=txtVar('TxZeigeUnnuetz'); if(fSetzFraWert($v,'TxZeigeUnnuetz',"'")) $bNeu=true;
  $v=txtVar('TxZeigeFehlt'); if(fSetzFraWert($v,'TxZeigeFehlt',"'")) $bNeu=true;
  $v=txtVar('TxZeigeLeer'); if(fSetzFraWert($v,'TxZeigeLeer',"'")) $bNeu=true;
  $v=txtVar('TlnBetreff'); if(fSetzFraWert($v,'TlnBetreff','"')) $bNeu=true;
  $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TlnVorspann')))); if(fSetzFraWert($v,'TlnVorspann',"'")) $bNeu=true;
  $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('TlnAbspann')))); if(fSetzFraWert($v,'TlnAbspann',"'")) $bNeu=true;
  $v=txtVar('AdmBetreff'); if(fSetzFraWert($v,'AdmBetreff','"')) $bNeu=true;
  $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('AdmVorspann')))); if(fSetzFraWert($v,'AdmVorspann',"'")) $bNeu=true;
  $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'',txtVar('AdmAbspann')))); if(fSetzFraWert($v,'AdmAbspann',"'")) $bNeu=true;
  $v=txtVar('Empfaenger'); if(fSetzFraWert($v,'Empfaenger','"')) $bNeu=true;
  $v=txtVar('Sender'); if(fSetzFraWert($v,'Sender',"'")) $bNeu=true;
  $v=txtVar('EnvelopeSender'); if(fSetzFraWert($v,'EnvelopeSender',"'")) $bNeu=true;
  if($bNeu){ //Speichern
   if(isset($_POST['KonfAlle'])&&$_POST['KonfAlle']=='1'||!$bAlleKonf){
    if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
     fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
    }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
   }else{$sMeld='<p class="admFehl">Wollen Sie die Änderung wirklich für <i>alle</i> Konfigurationen vornehmen?</p>'; $sKonfAlle='1';}
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Bewertungs-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 elseif(!$bNeu) $sMeld.='<p class="admMeld">Die Bewertungs-Einstellungen bleiben unverändert.</p>';
}else{//GET
 $fsPositivWertung=FRA_PositivWertung; $fsTeilWertung=FRA_TeilWertung; $fsTxZwischenwertung=FRA_TxZwischenwertung;
 $fsBldVorAw=FRA_BldVorAw; $fsTlnVorAw=FRA_TlnVorAw; $fsBldNachAw=FRA_BldNachAw; $fsTlnNachAw=FRA_TlnNachAw;
 $fsTlnPersonO=FRA_TlnPersonO; $fsAdmPersonO=FRA_AdmPersonO; if($fsTeilWertung===true) $fsTeilWertung=1;
 $fsBldTestName=FRA_BldTestName; $fsTlnTestName=FRA_TlnTestName; $fsAdmTestName=FRA_AdmTestName;
 $fsBldAnzahlO=FRA_BldAnzahlO; $fsTlnAnzahlO=FRA_TlnAnzahlO; $fsAdmAnzahlO=FRA_AdmAnzahlO; $fsDatAnzahlO=FRA_DatAnzahlO; $fsZntAnzahlO=FRA_ZntAnzahlO;
 $fsBldZeitO=FRA_BldZeitO; $fsTlnZeitO=FRA_TlnZeitO; $fsAdmZeitO=FRA_AdmZeitO; $fsDatZeitO=FRA_DatZeitO; $fsZntZeitO=FRA_ZntZeitO;
 $fsBldRichtigeO=FRA_BldRichtigeO; $fsTlnRichtigeO=FRA_TlnRichtigeO; $fsAdmRichtigeO=FRA_AdmRichtigeO; $fsDatRichtigeO=FRA_DatRichtigeO; $fsZntRichtigeO=FRA_ZntRichtigeO;
 $fsBldFalscheO=FRA_BldFalscheO; $fsTlnFalscheO=FRA_TlnFalscheO; $fsAdmFalscheO=FRA_AdmFalscheO; $fsDatFalscheO=FRA_DatFalscheO; $fsZntFalscheO=FRA_ZntFalscheO;
 $fsBldPunkteO=FRA_BldPunkteO; $fsTlnPunkteO=FRA_TlnPunkteO; $fsAdmPunkteO=FRA_AdmPunkteO; $fsDatPunkteO=FRA_DatPunkteO; $fsZntPunkteO=FRA_ZntPunkteO;
 $fsBldVerbalO=FRA_BldVerbalO; $fsTlnVerbalO=FRA_TlnVerbalO; $fsAdmVerbalO=FRA_AdmVerbalO; $fsDatVerbalO=FRA_DatVerbalO; $fsDatVerbalL=FRA_DatVerbalL; $fsZntVerbalO=FRA_ZntVerbalO;
 $fsBldVersucheO=FRA_BldVersucheO; $fsTlnVersucheO=FRA_TlnVersucheO; $fsAdmVersucheO=FRA_AdmVersucheO; $fsDatVersucheO=FRA_DatVersucheO; $fsZntVersucheO=FRA_ZntVersucheO;
 $fsBldAuslassenO=FRA_BldAuslassenO; $fsTlnAuslassenO=FRA_TlnAuslassenO; $fsAdmAuslassenO=FRA_AdmAuslassenO; $fsDatAuslassenO=FRA_DatAuslassenO; $fsZntAuslassenO=FRA_ZntAuslassenO;
 $fsDatFrageNr=FRA_DatFrageNr; $fsZntFrageNr=FRA_ZntFrageNr; $fsZntAntwort=FRA_ZntAntwort; $fsZntLoesung=FRA_ZntLoesung;
 $fsBldErgebnis=FRA_BldErgebnis; $fsTlnErgebnis=FRA_TlnErgebnis; $fsAdmErgebnis=FRA_AdmErgebnis; $fsDatErgebnis=FRA_DatErgebnis; $fsZntErgebnis=FRA_ZntErgebnis;
 $fsBldPunkte=FRA_BldPunkte; $fsTlnPunkte=FRA_TlnPunkte; $fsAdmPunkte=FRA_AdmPunkte; $fsDatPunkte=FRA_DatPunkte; $fsZntPunkte=FRA_ZntPunkte;
 $fsBldVersuche=FRA_BldVersuche; $fsTlnVersuche=FRA_TlnVersuche; $fsAdmVersuche=FRA_AdmVersuche; $fsDatVersuche=FRA_DatVersuche; $fsZntVersuche=FRA_ZntVersuche;
 $fsBldAuslassen=FRA_BldAuslassen; $fsTlnAuslassen=FRA_TlnAuslassen; $fsAdmAuslassen=FRA_AdmAuslassen; $fsDatAuslassen=FRA_DatAuslassen; $fsZntAuslassen=FRA_ZntAuslassen;
 $fsBldAlleNr=FRA_BldAlleNr; $fsTlnAlleNr=FRA_TlnAlleNr; $fsAdmAlleNr=FRA_AdmAlleNr;
 $fsBldKatErgebnis=FRA_BldKatErgebnis; $fsTlnKatErgebnis=FRA_TlnKatErgebnis; $fsAdmKatErgebnis=FRA_AdmKatErgebnis; $fsDatKatErgebnis=FRA_DatKatErgebnis; $fsZntKatErgebnis=FRA_ZntKatErgebnis;
 $fsBldKatFehlErgb=FRA_BldKatFehlErgb; $fsTlnKatFehlErgb=FRA_TlnKatFehlErgb; $fsAdmKatFehlErgb=FRA_AdmKatFehlErgb; $fsDatKatFehlErgb=FRA_DatKatFehlErgb; $fsZntKatFehlErgb=FRA_ZntKatFehlErgb;
 $fsBldKatPunkte=FRA_BldKatPunkte; $fsTlnKatPunkte=FRA_TlnKatPunkte; $fsAdmKatPunkte=FRA_AdmKatPunkte; $fsDatKatPunkte=FRA_DatKatPunkte; $fsZntKatPunkte=FRA_ZntKatPunkte;
 $fsBldKatSumme=FRA_BldKatSumme; $fsTlnKatSumme=FRA_TlnKatSumme; $fsAdmKatSumme=FRA_AdmKatSumme; $fsDatKatSumme=FRA_DatKatSumme; $fsZntKatSumme=FRA_ZntKatSumme;
 $fsBldAnzahlU=FRA_BldAnzahlU; $fsTlnAnzahlU=FRA_TlnAnzahlU; $fsAdmAnzahlU=FRA_AdmAnzahlU;
 $fsBldZeitU=FRA_BldZeitU; $fsTlnZeitU=FRA_TlnZeitU; $fsAdmZeitU=FRA_AdmZeitU;
 $fsBldRichtigeU=FRA_BldRichtigeU; $fsTlnRichtigeU=FRA_TlnRichtigeU; $fsAdmRichtigeU=FRA_AdmRichtigeU;
 $fsBldFalscheU=FRA_BldFalscheU; $fsTlnFalscheU=FRA_TlnFalscheU; $fsAdmFalscheU=FRA_AdmFalscheU;
 $fsBldPunkteU=FRA_BldPunkteU; $fsTlnPunkteU=FRA_TlnPunkteU; $fsAdmPunkteU=FRA_AdmPunkteU;
 $fsBldVerbalU=FRA_BldVerbalU; $fsTlnVerbalU=FRA_TlnVerbalU; $fsAdmVerbalU=FRA_AdmVerbalU;
 $fsBldVersucheU=FRA_BldVersucheU; $fsTlnVersucheU=FRA_TlnVersucheU; $fsAdmVersucheU=FRA_AdmVersucheU;
 $fsBldAuslassenU=FRA_BldAuslassenU; $fsTlnAuslassenU=FRA_TlnAuslassenU; $fsAdmAuslassenU=FRA_AdmAuslassenU;
 $fsTlnAntwort=FRA_TlnAntwort; $fsAdmAntwort=FRA_AdmAntwort; $fsDatAntwort=FRA_DatAntwort;
 $fsTlnVerlauf=FRA_TlnVerlauf; $fsAdmVerlauf=FRA_AdmVerlauf; $fsDatVerlauf=FRA_DatVerlauf;
 $fsTlnPersonU=FRA_TlnPersonU; $fsAdmPersonU=FRA_AdmPersonU; $fsDatPersonL=FRA_DatPersonL; $fsDatPersonD=FRA_DatPersonD; $fsDatPersonN=FRA_DatPersonN; $fsDatPersonT=FRA_DatPersonT;
 $fsBewertungsSeite=FRA_BewertungsSeite; $fsTxBewertungHier=FRA_TxBewertungHier; $fsTxZeigeLoesung=FRA_TxZeigeLoesung; $fsTxZeitLimit=FRA_TxZeitLimit;
 $fsVerbalAb1=FRA_VerbalAb1; $fsVerbalAb2=FRA_VerbalAb2; $fsVerbalAb3=FRA_VerbalAb3; $fsVerbalAb4=FRA_VerbalAb4; $fsVerbalAb5=FRA_VerbalAb5; $fsVerbalAb6=FRA_VerbalAb6;
 $fsVerbalTx1=FRA_VerbalTx1; $fsVerbalTx2=FRA_VerbalTx2; $fsVerbalTx3=FRA_VerbalTx3; $fsVerbalTx4=FRA_VerbalTx4; $fsVerbalTx5=FRA_VerbalTx5; $fsVerbalTx6=FRA_VerbalTx6;
 $fsVerbalTx0=FRA_VerbalTx0; $fsVerbalPunkte=FRA_VerbalPunkte; $fsPunkteTeilen=FRA_PunkteTeilen;
 $fsTxErgDruckKopf=FRA_TxErgDruckKopf; $fsTxErgDruckFuss=FRA_TxErgDruckFuss; $fsErgDruckTempl=FRA_ErgDruckTempl;
 $fsTlnBetreff=FRA_TlnBetreff; $fsTlnVorspann=FRA_TlnVorspann; $fsTlnAbspann=FRA_TlnAbspann;
 $fsAdmBetreff=FRA_AdmBetreff; $fsAdmVorspann=FRA_AdmVorspann; $fsAdmAbspann=FRA_AdmAbspann;
 $fsEmpfaenger=FRA_Empfaenger; $fsSender=FRA_Sender; $fsEnvelopeSender=FRA_EnvelopeSender;
 $fsLoesungsSeite=FRA_LoesungsSeite; $fsLoesungsFalsche=FRA_LoesungsFalsche; $fsLoesungsAnmk=FRA_LoesungsAnmk; $fsTxZeigeWertung=FRA_TxZeigeWertung; $fsTxZeigeAdmPkte=FRA_TxZeigeAdmPkte;
 $fsTxZeigeRichtig=FRA_TxZeigeRichtig; $fsTxZeigeUnnuetz=FRA_TxZeigeUnnuetz; $fsTxZeigeFehlt=FRA_TxZeigeFehlt; $fsTxZeigeLeer=FRA_TxZeigeLeer;
}//GET

//Seitenausgabe
define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);
if(!$sMeld) $sMeld='<p class="admMeld">Legen Sie die Optionen für die automatische Auswertung nach dem Fragendurchlauf fest.</p>';
echo $sMeld.NL;
?>

<form action="konfBewerten.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Nach Beantwortung der Fragen wird eine umfassende Auswertung durchgeführt.
Legen Sie die zu verwendende Bewertungsmethode fest.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungsmethode</td>
 <td><input class="admRadio" type="radio" name="PositivWertung<?php if($fsPositivWertung) echo '" checked="checked'?>" value="1" /> Pluspunkte für richtige Beantwortung (Positivwertung)<br />
 <input class="admRadio" type="radio" name="PositivWertung<?php if(!$fsPositivWertung) echo '" checked="checked'?>" value="0" /> Fehlerpunkte bei fehlerhafter Beantwortung (Negativwertung)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zählmethode<br/>pro Frage<br><br><br><br>Beispiele <a href="<?php echo ADF_Hilfe ?>LiesMich.htm#5.3" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td>
 <table cellpadding="1" cellspacing="0" border="0">
 <tr>
  <td width="18" valign="top"><input class="admRadio" type="radio" name="TeilWertung<?php if($fsTeilWertung<=0) echo '" checked="checked'?>" value="0" /></td>
  <td><i>Alles oder Nichts</i>: Punkte ungeteilt bei komplett richtig beantworteter Fragen vergeben, sonst keine Punkte</td>
 </tr>
 <tr>
  <td width="18" valign="top"><input class="admRadio" type="radio" name="TeilWertung<?php if($fsTeilWertung==4) echo '" checked="checked'?>" value="4" /></td>
  <td><i>Halbe Punktzahl bei einem Fehler</i>: Halbe Punktzahl, wenn nur ein Fehler gemacht wurde, bei mehr als einem Fehler keine Punkte</td>
 </tr>
 <tr>
  <td width="18" valign="top"><input class="admRadio" type="radio" name="TeilWertung<?php if($fsTeilWertung==3) echo '" checked="checked'?>" value="3" /></td>
  <td><i>Halbe Punkte wenn halb richtig</i>: Halbe Punktzahl, wenn mindestens die Hälfte aller Antworten richtig behandelt wurde, darunter keine Punkte</td>
 </tr>
 <tr>
  <td width="18" valign="top"><input class="admRadio" type="radio" name="TeilWertung<?php if($fsTeilWertung==2) echo '" checked="checked'?>" value="2" /></td>
  <td><i>Richtig-Anteil aufrechnen</i>: Jede richtig gewählte zutreffende Antwort und jede nicht gewählte falsche Antwort wird anteilig auf die Gesamtpunktzahl umgelegt</td>
 </tr>
 <tr>
  <td width="18" valign="top"><input class="admRadio" type="radio" name="TeilWertung<?php if($fsTeilWertung==1) echo '" checked="checked'?>" value="1" /></td>
  <td><i>Richtig-Anteil zutreffender Antworten</i>: Nur gewählte zutreffende Antworten werden anteilig gezählt. Fälschlich gewählte nicht zutreffende Antworten werden ignoriert</td>
 </tr>
 </table>
 <div class="mini"><u>Hinweis</u>: Wurde oben die Bewertungsmethode <i>Fehlerpunkte</i> (<i>Negativwertung</i>) gewählt, kehrt sich die Funktionsweise dieser Zählmethoden um.</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Punkteverteilung</td>
 <td><input type="radio" class="admRadio" name="PunkteTeilen" value="1"<?php if($fsPunkteTeilen) echo ' checked="checked"'?> /> Punkte der Frage gleichmäßig auf die Antworten verteilen (Standard)<br>
 <input type="radio" class="admRadio" name="PunkteTeilen" value="0"<?php if(!$fsPunkteTeilen) echo ' checked="checked"'?> /> Punkte pro Antwort individuell eingeben &nbsp; <span class="admMini">(gilt nur für die zwei Methoden <i>Richtig-Anteil aufrechnen</i> und <i>Richtig-Anteil zutreffender Antworten</i>)</span></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Bereits während der Fragenbeantwortung kann der Zwischenstand in folgendem Format eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zwischenstand</td>
 <td><input type="text" name="TxZwischenwertung" value="<?php echo $fsTxZwischenwertung?>" size="90" style="width:99%;">
 <div class="admMini">leer lassen bei Nichtverwendung oder Text wie: &nbsp; <i>bisher #P/#G</i> &nbsp; oder: &nbsp; <i>#F Fehler</i></div>
 <div class="admMini">Platzhalter: #R - Richtige, #F - Falsche, #P - erzielte Punkte, #G - gesamt mögliche Punkte</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Bewertung kann als für den Teilnehmer sichtbare Bildschirmseite,
als E-Mail an den Teilnehmer, als E-Mail an den Webmaster und als Eintrag in die Resultateliste erfolgen.
Die Elemente für jede dieser vier Auswertemöglichkeiten sind variierbar und kombinierbar.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Auswerteelemente<br>und deren Reihenfolge</td>
 <td style="padding:5px;">
  <table class="admTabl" width="100%" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl">
    <td width="30%" style="vertical-align:bottom">Element</td>
    <td align="center" style="vertical-align:bottom" width="14%">Bildschirm</td>
    <td align="center" style="vertical-align:bottom" width="14%">E-Mail Teilnehmer</td>
    <td align="center" style="vertical-align:bottom" width="14%">E-Mail Webmaster</td>
    <td align="center" style="vertical-align:bottom" width="14%">Ergebnisliste Administrator</td>
    <td align="center" style="vertical-align:bottom" width="14%">Ergebnisliste Benutzer-Zentrum</td>
   </tr><tr class="admTabl">
    <td>Vortext gemäß Formular<br />gespeicherteTestfragenfolgen</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVorAw"<?php if($fsBldVorAw) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVorAw"<?php if($fsTlnVorAw) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Teilnehmerdaten gemäß<br />Formular oder Nutzerdatei</td>
    <td align="center">&nbsp;</td>
    <td align="center">*<input class="admCheck" type="checkbox" name="TlnPersonO"<?php if($fsTlnPersonO) echo ' checked="checked"'?> value="1">*</td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmPersonO"<?php if($fsAdmPersonO) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Testfolgenname</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldTestName"<?php if($fsBldTestName) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnTestName"<?php if($fsTlnTestName) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmTestName"<?php if($fsAdmTestName) echo ' checked="checked"'?> value="1"></td>
    <td align="center" style="vertical-align:middle;"><img src="iconHaken.gif" width="13" height="13" border="0" alt=""></td>
    <td align="center" style="vertical-align:middle;"><img src="iconHaken.gif" width="13" height="13" border="0" alt=""></td>
   </tr><tr class="admTabl">
    <td>Fragenanzahl</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldAnzahlO"<?php if($fsBldAnzahlO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnAnzahlO"<?php if($fsTlnAnzahlO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAnzahlO"<?php if($fsAdmAnzahlO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatAnzahlO"<?php if($fsDatAnzahlO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntAnzahlO"<?php if($fsZntAnzahlO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Antwortzeit gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldZeitO"<?php if($fsBldZeitO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnZeitO"<?php if($fsTlnZeitO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmZeitO"<?php if($fsAdmZeitO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatZeitO"<?php if($fsDatZeitO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntZeitO"<?php if($fsZntZeitO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Richtige gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldRichtigeO"<?php if($fsBldRichtigeO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnRichtigeO"<?php if($fsTlnRichtigeO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmRichtigeO"<?php if($fsAdmRichtigeO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatRichtigeO"<?php if($fsDatRichtigeO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntRichtigeO"<?php if($fsZntRichtigeO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Falsche gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldFalscheO"<?php if($fsBldFalscheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnFalscheO"<?php if($fsTlnFalscheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmFalscheO"<?php if($fsAdmFalscheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatFalscheO"<?php if($fsDatFalscheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntFalscheO"<?php if($fsZntFalscheO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Punkte gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldPunkteO"<?php if($fsBldPunkteO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnPunkteO"<?php if($fsTlnPunkteO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmPunkteO"<?php if($fsAdmPunkteO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatPunkteO"<?php if($fsDatPunkteO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntPunkteO"<?php if($fsZntPunkteO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>verbale Wertung</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVerbalO"<?php if($fsBldVerbalO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVerbalO"<?php if($fsTlnVerbalO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVerbalO"<?php if($fsAdmVerbalO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatVerbalO"<?php if($fsDatVerbalO) echo ' checked="checked"'?> value="1" title="in den Ergebnisdetails"> / <input class="admCheck" type="checkbox" name="DatVerbalL"<?php if($fsDatVerbalL) echo ' checked="checked"'?> value="1" title="in der Ergebnisliste"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntVerbalO"<?php if($fsZntVerbalO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Versuche gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVersucheO"<?php if($fsBldVersucheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVersucheO"<?php if($fsTlnVersucheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVersucheO"<?php if($fsAdmVersucheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatVersucheO"<?php if($fsDatVersucheO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntVersucheO"<?php if($fsZntVersucheO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>Auslassungen gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldAuslassenO"<?php if($fsBldAuslassenO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnAuslassenO"<?php if($fsTlnAuslassenO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAuslassenO"<?php if($fsAdmAuslassenO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatAuslassenO"<?php if($fsDatAuslassenO) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntAuslassenO"<?php if($fsZntAuslassenO) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>für jede Frage oder<br>nur bei falscher Antwort</td>
    <td align="center"><input class="admRadio" type="radio" name="BldAlleNr"<?php if($fsBldAlleNr) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="BldAlleNr"<?php if(!$fsBldAlleNr) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="TlnAlleNr"<?php if($fsTlnAlleNr) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="TlnAlleNr"<?php if(!$fsTlnAlleNr) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="AdmAlleNr"<?php if($fsAdmAlleNr) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="AdmAlleNr"<?php if(!$fsAdmAlleNr) echo ' checked="checked"'?> value="0"></td>
    <td align="center"></td>
    <td align="center"></td>
   </tr><tr class="admTabl">
    <td>- Frage-Nr</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatFrageNr"<?php if($fsDatFrageNr) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntFrageNr"<?php if($fsZntFrageNr) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- eingegebene Antwort</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntAntwort"<?php if($fsZntAntwort) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- Lösung</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntLoesung"<?php if($fsZntLoesung) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- richtig/falsch</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldErgebnis"<?php if($fsBldErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnErgebnis"<?php if($fsTlnErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmErgebnis"<?php if($fsAdmErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatErgebnis"<?php if($fsDatErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntErgebnis"<?php if($fsZntErgebnis) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- Punkte</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldPunkte"<?php if($fsBldPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnPunkte"<?php if($fsTlnPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmPunkte"<?php if($fsAdmPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatPunkte"<?php if($fsDatPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntPunkte"<?php if($fsZntPunkte) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- Versuche</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVersuche"<?php if($fsBldVersuche) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVersuche"<?php if($fsTlnVersuche) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVersuche"<?php if($fsAdmVersuche) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatVersuche"<?php if($fsDatVersuche) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntVersuche"<?php if($fsZntVersuche) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- Auslassungen</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldAuslassen"<?php if($fsBldAuslassen) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnAuslassen"<?php if($fsTlnAuslassen) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAuslassen"<?php if($fsAdmAuslassen) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatAuslassen"<?php if($fsDatAuslassen) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntAuslassen"<?php if($fsZntAuslassen) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td colspan="6">pro Kategorie (falls verwendet)</td>
   <tr class="admTabl">
    <td>- Richtige</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldKatErgebnis"<?php if($fsBldKatErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnKatErgebnis"<?php if($fsTlnKatErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmKatErgebnis"<?php if($fsAdmKatErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatKatErgebnis"<?php if($fsDatKatErgebnis) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntKatErgebnis"<?php if($fsZntKatErgebnis) echo ' checked="checked"'?> value="1"></td>
   <tr class="admTabl">
    <td>- Falsche</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldKatFehlErgb"<?php if($fsBldKatFehlErgb) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnKatFehlErgb"<?php if($fsTlnKatFehlErgb) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmKatFehlErgb"<?php if($fsAdmKatFehlErgb) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatKatFehlErgb"<?php if($fsDatKatFehlErgb) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntKatFehlErgb"<?php if($fsZntKatFehlErgb) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- Punkte</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldKatPunkte"<?php if($fsBldKatPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnKatPunkte"<?php if($fsTlnKatPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmKatPunkte"<?php if($fsAdmKatPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatKatPunkte"<?php if($fsDatKatPunkte) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="ZntKatPunkte"<?php if($fsZntKatPunkte) echo ' checked="checked"'?> value="1"></td>
   </tr><tr class="admTabl">
    <td>- nur Hauptkategorien oder<br>- auch alle Unterkategorien</td>
    <td align="center"><input class="admRadio" type="radio" name="BldKatSumme"<?php if($fsBldKatSumme) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="BldKatSumme"<?php if(!$fsBldKatSumme) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="TlnKatSumme"<?php if($fsTlnKatSumme) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="TlnKatSumme"<?php if(!$fsTlnKatSumme) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="AdmKatSumme"<?php if($fsAdmKatSumme) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="AdmKatSumme"<?php if(!$fsAdmKatSumme) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="DatKatSumme"<?php if($fsDatKatSumme) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="DatKatSumme"<?php if(!$fsDatKatSumme) echo ' checked="checked"'?> value="0"></td>
    <td align="center"><input class="admRadio" type="radio" name="ZntKatSumme"<?php if($fsZntKatSumme) echo ' checked="checked"'?> value="1"><br><input class="admRadio" type="radio" name="ZntKatSumme"<?php if(!$fsZntKatSumme) echo ' checked="checked"'?> value="0"></td>
   </tr><tr class="admTabl">
    <td>Fragenanzahl</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldAnzahlU"<?php if($fsBldAnzahlU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnAnzahlU"<?php if($fsTlnAnzahlU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAnzahlU"<?php if($fsAdmAnzahlU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Antwortzeit gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldZeitU"<?php if($fsBldZeitU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnZeitU"<?php if($fsTlnZeitU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmZeitU"<?php if($fsAdmZeitU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Richtige gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldRichtigeU"<?php if($fsBldRichtigeU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnRichtigeU"<?php if($fsTlnRichtigeU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmRichtigeU"<?php if($fsAdmRichtigeU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Falsche gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldFalscheU"<?php if($fsBldFalscheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnFalscheU"<?php if($fsTlnFalscheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmFalscheU"<?php if($fsAdmFalscheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Punkte gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldPunkteU"<?php if($fsBldPunkteU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnPunkteU"<?php if($fsTlnPunkteU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmPunkteU"<?php if($fsAdmPunkteU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>verbale Wertung</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVerbalU"<?php if($fsBldVerbalU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVerbalU"<?php if($fsTlnVerbalU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVerbalU"<?php if($fsAdmVerbalU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Versuche gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldVersucheU"<?php if($fsBldVersucheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnVersucheU"<?php if($fsTlnVersucheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVersucheU"<?php if($fsAdmVersucheU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Auslassungen gesamt</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldAuslassenU"<?php if($fsBldAuslassenU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnAuslassenU"<?php if($fsTlnAuslassenU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAuslassenU"<?php if($fsAdmAuslassenU) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>gesamte Antwortkette</td>
    <td align="center">&nbsp;</td>
    <td align="center">(<input class="admCheck" type="checkbox" name="TlnAntwort"<?php if($fsTlnAntwort) echo ' checked="checked"'?> value="1">)</td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmAntwort"<?php if($fsAdmAntwort) echo ' checked="checked"'?> value="1"></td>
    <td align="center" style="vertical-align:middle;"><img src="iconHaken.gif" width="13" height="13" border="0" alt=""><input type="hidden" name="DatAntwort" value="1"></td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>gesamte Verlaufskette</td>
    <td align="center">&nbsp;</td>
    <td align="center">(<input class="admCheck" type="checkbox" name="TlnVerlauf"<?php if($fsTlnVerlauf) echo ' checked="checked"'?> value="1">)</td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmVerlauf"<?php if($fsAdmVerlauf) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatVerlauf"<?php if($fsDatVerlauf) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Teilnehmerdaten gemäß<br />Formular oder Nutzerdatei</td>
    <td align="center">&nbsp;</td>
    <td align="center">*<input class="admCheck" type="checkbox" name="TlnPersonU"<?php if($fsTlnPersonU) echo ' checked="checked"'?> value="1">*</td>
    <td align="center"><input class="admCheck" type="checkbox" name="AdmPersonU"<?php if($fsAdmPersonU) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="DatPersonL"<?php if($fsDatPersonL) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
   </tr><tr class="admTabl">
    <td>Nachtext gemäß Formular<br />gespeicherte Testfragenfolgen</td>
    <td align="center"><input class="admCheck" type="checkbox" name="BldNachAw"<?php if($fsBldNachAw) echo ' checked="checked"'?> value="1"></td>
    <td align="center"><input class="admCheck" type="checkbox" name="TlnNachAw"<?php if($fsTlnNachAw) echo ' checked="checked"'?> value="1"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
   </tr>
  </table>
  <p class="admMini">* <u>Sicherheitshinweis</u>: Wenn Sie diese Funktion aktivieren könnten über das Testfragen-Script eventuell auch E-Mails mit unerwünschtem Inhalt an fremde Personen versandt werden. (Risiko eher gering)</p>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmerdaten<br />in der Ergebnisliste<br>des Administrators<div class="admMini">(falls aktiviert)</div></td>
 <td><div style="margin-bottom:4px"><input class="admRadio" type="radio" name="DatPersonD<?php if(!$fsDatPersonD) echo '" checked="checked'?>" value="0" /> nicht aufbereitet &nbsp;
 <input class="admRadio" type="radio" name="DatPersonD<?php if($fsDatPersonD) echo '" checked="checked'?>" value="1" /> aufbereitet nach folgendem Muster:</div>
 <div><span style="display:inline-block;width:7em">Benutzer:</span><input type="text" name="DatPersonN" value="<?php echo $fsDatPersonN?>" size="50" style="width:30em"></div>
 <div><span style="display:inline-block;width:7em">Teilnehmer:</span><input type="text" name="DatPersonT" value="<?php echo $fsDatPersonT?>" size="50" style="width:30em"></div>
 <div class="admMini">als Platzhalter Feldnamen verwenden: <u>Muster</u> &nbsp; {Vorname} {Name}, {Ort}</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Nach Abschluss des Beantwortens aller Fragen
und nach erfolgter automatischer Bewertung kann eine ausführliche Bewertungsseite (Bildschirmauswertung mit obigen Detailinformationen) mit dem Testergebnis angezeigt werden
oder sofort zur Lösungsseite bzw. zu einer ergebnisneutralen Abschlusseite gesprungen werden.
Die automatische Bewertung/Ergebnisspeicherung erfolgt unabhängig davon in jedem Fall.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungsseite</td>
 <td><input class="admRadio" type="radio" name="BewertungsSeite<?php if($fsBewertungsSeite) echo '" checked="checked'?>" value="1" /> Bewertungsseite anzeigen &nbsp;
 <input class="admRadio" type="radio" name="BewertungsSeite<?php if(!$fsBewertungsSeite) echo '" checked="checked'?>" value="0" /> ohne Ergebnisanzeige sofort weiter</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über der Bewertungsseite auf dem Bildschirm erscheint eine Meldung.
Sie soll den Teilnehmer gegebenenfalls auch informieren, welcher Art die Auswertung ist, falls keine sichtbare Bildschirmauswertung stattfindet.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnismeldung</td>
 <td><input type="text" name="TxBewertungHier" value="<?php echo $fsTxBewertungHier?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: Sie haben bei den #Z Fragen folgendes Resultat erreicht!<br />
 oder alternativ: Ihre Ergebnisse wurden an den Webmaster gesandt!</div></td>
<tr class="admTabl">
 <td class="admSpa1">Abbruchmeldung</td>
 <td><input type="text" name="TxZeitLimit" value="<?php echo $fsTxZeitLimit?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: Die Bearbeitungszeit für #Z Fragen ist abgelaufen. Hier Ihr Ergebnis.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern oben die verbale Bewertung aktiviert ist können Sie hier Regeln dafür festlegen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungsregeln</td>
 <td>
  <table class="admTabl" style="width:100%" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl">
    <td width="1%">Regelgrenze</td>
    <td>verbaler Wertungstext</td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb1" value="<?php echo ($fsVerbalAb1>0?$fsVerbalAb1:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx1" value="<?php echo $fsVerbalTx1?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb2" value="<?php echo ($fsVerbalAb2>0?$fsVerbalAb2:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx2" value="<?php echo $fsVerbalTx2?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb3" value="<?php echo ($fsVerbalAb3>0?$fsVerbalAb3:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx3" value="<?php echo $fsVerbalTx3?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb4" value="<?php echo ($fsVerbalAb4>0?$fsVerbalAb4:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx4" value="<?php echo $fsVerbalTx4?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb5" value="<?php echo ($fsVerbalAb5>0?$fsVerbalAb5:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx5" value="<?php echo $fsVerbalTx5?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td>ab <input type="text" name="VerbalAb6" value="<?php echo ($fsVerbalAb6>0?$fsVerbalAb6:'')?>" size="3" style="width:3em">%</td>
    <td><input type="text" name="VerbalTx6" value="<?php echo $fsVerbalTx6?>" size="40" style="width:350px"></td>
   </tr>
   <tr class="admTabl">
    <td style="vertical-align:middle">unter dem</td>
    <td><input type="text" name="VerbalTx0" value="<?php echo $fsVerbalTx0?>" size="40" style="width:350px"></td>
   </tr>
  </table>
  <div>&nbsp;Beispiel: &nbsp; &nbsp; &nbsp; <i>80%: gute Leistung &nbsp; 50%: bestanden #P/#G &nbsp; unter dem: nicht bestanden</i></div>
  <div class="admMini">&nbsp;Platzhalter: &nbsp; #R-Richtige, #F-Falsche, #A-Fragenanzahl, #P-erzielte Punkte, #G-gesamt mögliche Punkte</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungsmaß</td>
 <td>die verbalen Bewertungen ableiten von&nbsp; <input class="admRadio" type="radio" name="VerbalPunkte<?php if($fsVerbalPunkte) echo '" checked="checked'?>" value="1" /> Punkten oder&nbsp; <input class="admRadio" type="radio" name="VerbalPunkte<?php if(!$fsVerbalPunkte) echo '" checked="checked'?>" value="0" /> Richtigen</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern der Teilnehmer eine E-Mail mit den Ergebnissen erhält kann diese variiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Betreffzeile<br />Teilnehmernachricht</td>
 <td><input type="text" name="TlnBetreff" value="<?php echo $fsTlnBetreff?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: Auswertung oder Ergebnis</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorspanntext<br>Teilnehmernachricht</td>
 <td><textarea name="TlnVorspann" cols="90" rows="4" style="width:99%;"><?php echo str_replace('\n ',"\n",$fsTlnVorspann)?></textarea>
 <div class="admMini">Platzhalter: &nbsp; #T - Testfolgenname &nbsp; &nbsp; #D - Datum &nbsp; &nbsp; {Benutzer-Datenfeldname}</div>
 <div class="admMini">Textvorschlag: Sehr geehrte Damen und Herren,<br />
 Sie haben soeben das Testfragen-Script benutzt und dabei folgendes Resultat erreicht:</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Abspanntext<br>Teilnehmernachricht</td>
 <td><textarea name="TlnAbspann" cols="90" rows="4" style="width:99%;"><?php echo str_replace('\n ',"\n",$fsTlnAbspann)?></textarea>
 <div class="admMini">Textvorschlag: eine Grußformel + (automatische Benachrichtigung)</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern der Administrator eine E-Mail mit den Ergebnissen erhält kann diese variiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Empfängeradresse<br>des Webmasters</td>
 <td><input type="text" name="Empfaenger" value="<?php echo $fsEmpfaenger?>" size="90" style="width:99%;">
 <div class="admMini">reine E-Mail-Adresse name@domain.de <i>ohne</i> Real-Namen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Betreffzeile<br>Webmasternachricht</td>
 <td><input type="text" name="AdmBetreff" value="<?php echo $fsAdmBetreff?>" size="90" style="width:99%;"></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorspanntext<br>Webmasternachricht</td>
 <td><textarea name="AdmVorspann" cols="90" rows="4" style="width:99%;"><?php echo str_replace('\n ',"\n",$fsAdmVorspann)?></textarea></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Abspanntext<br>Webmasternachricht</td>
 <td><textarea name="AdmAbspann" cols="90" rows="4" style="width:99%;"><?php echo str_replace('\n ',"\n",$fsAdmAbspann)?></textarea></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Für den Versand und Empfang der Auswertenachrichten sind E-Mail-Adressen notwendig.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Absenderadresse</td>
 <td><input type="text" name="Sender" value="<?php echo $fsSender?>" size="90" style="width:99%">
 <div class="admMini">Absendernamen und E-Mail-Adresse möglich in der Form: Absender &lt;name@domain.de&gt;</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Envelope-<br>Absenderadresse</td>
 <td><input type="text" name="EnvelopeSender" value="<?php echo $fsEnvelopeSender?>" size="90" style="width:99%">
 <div class="admMini">leer lassen (nur ausfüllen mit reiner E-Mail-Adresse name@domain.de wenn Ihr Provider eine Envelope-Absenderadresse als sendmail-Parameter -f ausdrücklich verlangt)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Nach Abschluss des Beantwortens aller Fragen
und nach erfolgter Bewertung kann eine Lösungsseite angezeigt werden, die alle Fragen und Antworten nebst Anmerkungen zu Lern- und Kontrollzwecken aufdeckt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Lösungsseite</td>
 <td><input class="admCheck" type="checkbox" name="LoesungsSeite"<?php if($fsLoesungsSeite) echo ' checked="checked"'?> value="1" /> Lösungsseite() mit allen Antworten anzeigen<br>
 <input class="admCheck" type="checkbox" name="LoesungsFalsche"<?php if($fsLoesungsFalsche) echo ' checked="checked"'?> value="1" /> dabei nur die falsch gelösten Fragen darbieten<br>
 <input class="admCheck" type="checkbox" name="LoesungsAnmk"<?php if($fsLoesungsAnmk==1) echo ' checked="checked"'?> value="1" /> Anmerkung-1 unterhalb der Antworten pro Frage anzeigen<br>
 <input class="admCheck" type="checkbox" name="LoesungsAnm2"<?php if($fsLoesungsAnmk==2) echo ' checked="checked"'?> value="2" /> Anmerkung-1 oder Anmerkung-2 je nach richtig/falsch anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Lösungstext</td>
 <td><input type="text" name="TxZeigeLoesung" value="<?php echo $fsTxZeigeLoesung?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Lösung zur Frage #N</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenbewertung</td>
 <td><input type="text" name="TxZeigeWertung" value="<?php echo $fsTxZeigeWertung?>" size="90" style="width:99%;">
 <div class="admMini">leer lassen bei Nichtverwendung oder Text wie: &nbsp; <i>#P von #G Punkten</i> &nbsp; oder: &nbsp; <i>#P Fehlerpunkte</i></div>
 <div class="admMini">Platzhalter: #P - erzielte Punkte, #G - mögliche Punkte</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Punkteanzeige in der <br />Administratorliste</td>
 <td><input type="text" name="TxZeigeAdmPkte" value="<?php echo $fsTxZeigeAdmPkte?>" size="90" style="width:99%;">
 <div class="admMini">leer lassen bei reiner Punktezahl oder Text wie: &nbsp; <i>#P/#G (#W)</i> &nbsp; oder: &nbsp; <i>#P von #G</i></div>
 <div class="admMini">Platzhalter: #P - erzielte Punkte, #G - mögliche Punkte, #W - Prozente</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Bewertungstexte</td>
 <td><div><img src="<?php echo FRA_Http?>hakenGrn.gif" title="<?php echo $fsTxZeigeRichtig?>" width="16" height="16" border="0" /> <input type="text" name="TxZeigeRichtig" value="<?php echo $fsTxZeigeRichtig?>" size="40" style="width:20em" /></div>
 <div><img src="<?php echo FRA_Http?>kreuzRot.gif" title="<?php echo $fsTxZeigeUnnuetz?>" width="16" height="16" border="0" /> <input type="text" name="TxZeigeUnnuetz" value="<?php echo $fsTxZeigeUnnuetz?>" size="40" style="width:20em" /></div>
 <div><img src="<?php echo FRA_Http?>kreisSchw.gif" title="<?php echo $fsTxZeigeFehlt?>" width="16" height="16" border="0" /> <input type="text" name="TxZeigeFehlt" value="<?php echo $fsTxZeigeFehlt?>" size="40" style="width:20em" /></div>
 <div><img src="<?php echo FRA_Http?>kaestchen.gif" title="<?php echo $fsTxZeigeLeer?>" width="16" height="16" border="0" /> <input type="text" name="TxZeigeLeer" value="<?php echo $fsTxZeigeLeer?>" size="40" style="width:20em" /></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Jeder Test kann vom Administrator und/oder Teilnehmer mit detaillierten Ergebnissen ausgedruckt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Druckschablone</td>
 <td><input class="admCheck" type="checkbox" name="ErgDruckTempl"<?php if($fsErgDruckTempl) echo ' checked="checked"'?> value="1" /> HTML-Schablone <a href="<?php echo FRA_Http?>fraDrucken.htm" onclick="druWin(this.href)" target="druck">fraDrucken.htm</a> als Umhüllung der Druckseite verwenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kopfzeile <br />über dem Fragenblock</td>
 <td><input type="text" name="TxErgDruckKopf" value="<?php echo $fsTxErgDruckKopf?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Testergebnis von Teilnehmer #T am #D</i></div>
 <div class="admMini">Platzhalter: #N Ergebnis-Eintrag, #D Datum + Uhrzeit, #T Teilnehmer </div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fußzeile <br />unter dem Fragenblock</td>
 <td><input type="text" name="TxErgDruckFuss" value="<?php echo $fsTxErgDruckFuss?>" size="90" style="width:99%;">
 <div class="admMini">Textvorschlag: <i>Gesamt #P von #G Punkten, #W Prozent</i></div>
 <div class="admMini">Platzhalter: <br>#P erzielte Punkte, #G mögliche Punkte, #W Prozente, #R Richtige, #F Falsche, #A Fragenanzahl, #B Bewertung, #Z Zeit</i></div>
 </td>
</tr>

</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?> &nbsp; <input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen<input type="hidden" name="KonfAlle" value="<?php echo $sKonfAlle;?>" /></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>