<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Zusagesystem-Zusatzmodul','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=360,left=4,top=4,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KZs');

$nTFelder=count($kal_FeldName);
$bInstalliert=file_exists('zusageListe.php')&&file_exists(KAL_Pfad.'kalZusageEintrag.php'); $bVersionOK=(substr(KAL_Version,0,3)>'3.3');
$kal_ZusageFelder=explode(';',KAL_ZusageFelder); $nZusageFelder=count($kal_ZusageFelder); $ksZusageNeu=false; $kal_ZusageFeldTyp=explode(';',KAL_ZusageFeldTyp);
$kal_ZusageQuellen=explode(';',KAL_ZusageQuellen); $kal_ZusagePflicht=explode(';',KAL_ZusagePflicht); $kal_ZusageAuswahl=explode(';',KAL_ZusageAuswahl);

$nTxNr=0; $nTxMax=0; $sATxKz=''; $sATxBt=''; $sATxMt=''; $aTxKz=array(); $aTxBt=array(); $aTxMt=array(); //Alternativtext holen
if(!KAL_SQL){
 $aT=file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $nTxte=count($aT);
 for($i=1;$i<$nTxte;$i++){
  $s=$aT[$i]; $k=(int)substr($s,0,4); $nTxMax=max($k,$nTxMax);
  if(substr($s,5,1)=='z'){
   $a=explode(';',rtrim($s));
   $aTxKz[$k]=str_replace('`,',';',$a[2]); $aTxBt[$k]=str_replace('`,',';',$a[3]); $aTxMt[$k]=str_replace('`,',';',$a[4]);
  }
 }
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' WHERE typ="z" ORDER BY id')){
  while($a=$rR->fetch_row()){
   $k=(int)$a[0]; $aTxKz[$k]=$a[2]; $aTxBt[$k]=$a[3]; $aTxMt[$k]=str_replace("\n",'\n ',str_replace("\r",'',$a[4]));
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $ksZusageSystem=(defined('KAL_ZusageSystem')?KAL_ZusageSystem:false); $ksZUser=KAL_ZUser; $ksZCode=KAL_ZCode;
 $ksDirektZusage=KAL_DirektZusage; $ksZusageFrist=KAL_ZusageFrist; $ksZusageNameFrist=KAL_ZusageNameFrist; $ksZusageBisEnde=KAL_ZusageBisEnde;
 $ksEinKlickLZusage=KAL_EinKlickLZusage; $ksEinKlickDZusage=KAL_EinKlickDZusage; $ksLoeschKlickZusage=KAL_LoeschKlickZusage;
 $ksTxZusageMeld=KAL_TxZusageMeld; $ksTxZusageEintr=KAL_TxZusageEintr; $ksTxZusageGeloescht=KAL_TxZusageGeloescht; $ksTxZusageSperre=KAL_TxZusageSperre; $ksTxZusageKapazEnde=KAL_TxZusageKapazEnde; $ksTxKeineDoppelBuchung=KAL_TxKeineDoppelBuchung; $ksKeineDoppelBuchung=KAL_KeineDoppelBuchung;
 $ksTxNzZusageAenden=KAL_TxNzZusageAenden; $ksTxNzZusageAendOk=KAL_TxNzZusageAendOk;
 $ksTxZEinKlickEFrage=KAL_TxZEinKlickEFrage; $ksTxZEinKlickLFrage=KAL_TxZEinKlickLFrage; $ksTxZEinKlickLoesch=KAL_TxZEinKlickLoesch;
 $ksTxNzLoeschen=KAL_TxNzLoeschen; $ksTxNzGeloescht=KAL_TxNzGeloescht; $ksFormAendZusageInfo=KAL_FormAendZusageInfo;
 $ksTxNzUnveraendert=KAL_TxNzUnveraendert; $ksTxNzZusageStat=KAL_TxNzZusageStat;
 $ksTxZusageBestaetigen=KAL_TxZusageBestaetigen; $ksTxZusageBestaetTxt=KAL_TxZusageBestaetTxt; $ksTxZusageBestaetigt=KAL_TxZusageBestaetigt;
 $ksTxZusageWiderruf=KAL_TxZusageWiderruf; $ksTxZusageWdrrufTxt=KAL_TxZusageWdrrufTxt; $ksTxZusageIstWdrrufen=KAL_TxZusageIstWdrrufen;
 $ksPruefeZusageKapaz=KAL_PruefeZusageKapaz; $ksErlaubeZusageNull=KAL_ErlaubeZusageNull; $ksPruefe1KlickKapaz=KAL_Pruefe1KlickKapaz;
 $ksZusageVormerkErlaubt=KAL_ZusageVormerkErlaubt; $ksZusageVormerkTxtZeile=KAL_ZusageVormerkTxtZeile; $ksTxZusageVormerk=KAL_TxZusageVormerk; $ksTxZusageVmkErlaubt=KAL_TxZusageVmkErlaubt;
 $ksTxZusageIcon=KAL_TxZusageIcon; $ksTxZugesagtIcon=KAL_TxZugesagtIcon; $ksRechneZusagePreis=KAL_RechneZusagePreis; $ksSperreZusagePreis=KAL_SperreZusagePreis;
 $ksListenZusage=KAL_ListenZusage; $ksGastLZusage=KAL_GastLZusage; $ksTxListenZusageTitel=KAL_TxListenZusageTitel;
 $ksListenZusagZ=KAL_ListenZusagZ; $ksGastLZusagZ=KAL_GastLZusagZ; $ksTxListenZusagZTitel=KAL_TxListenZusagZTitel;

 $ksListenZusagS=KAL_ListenZusagS; $ksGastLZusagS=KAL_GastLZusagS; $ksTxListenZusagSTitel=KAL_TxListenZusagSTitel; $ksDetailZusagS=KAL_DetailZusagS;
 $ksZusageStatusRot=KAL_ZusageStatusRot; $ksZusageStatusGlb=KAL_ZusageStatusGlb; $ksZusageStatusSchwelle=KAL_ZusageStatusSchwelle;
 $ksTxZusageStatusRot=KAL_TxZusageStatusRot; $ksTxZusageStatusGlb=KAL_TxZusageStatusGlb; $ksTxZusageStatusGrn=KAL_TxZusageStatusGrn;
 $ksTxDetailZusagZMuster=KAL_TxDetailZusagZMuster; $ksDetailZusagKLeer=KAL_DetailZusagKLeer; $ksDetailZusagRLeer=KAL_DetailZusagRLeer;
 $ksMonatsZusage=KAL_MonatsZusage; $ksGastMoZusage=KAL_GastMoZusage;
 $ksMonatZeigeZusage=KAL_MonatZeigeZusage; $ksGastMZeigeZusage=KAL_GastMZeigeZusage;
 $ksMonatsZusagS=KAL_MonatsZusagS; $ksGastMoZusagS=KAL_GastMoZusagS; $ksMonZusageHG=KAL_MonZusageHG; $ksMonZusagHGBl=KAL_MonZusagHGBl;
 $ksMonatsZusagZ=KAL_MonatsZusagZ; $ksGastMoZusagZ=KAL_GastMoZusagZ; $ksMonZusagZZeigeLeer=KAL_MonZusagZZeigeLeer;
 $ksMonZusagZMuster=KAL_MonZusagZMuster; $ksMonZusagZErsatz=KAL_MonZusagZErsatz;

 $ksListenZusagZLeer=KAL_ListenZusagZLeer; $ksListenZusagRLeer=KAL_ListenZusagRLeer; $ksTxListenZusagZMuster=KAL_TxListenZusagZMuster;
 $ksDetailZusage=KAL_DetailZusage; $ksGastDZusage=KAL_GastDZusage; $ksTxZusageZeile=KAL_TxZusageZeile;
 $ksZusage=KAL_Zusage; $ksSqlTabZ=KAL_SqlTabZ; $ksZusagen=KAL_Zusagen; $ksZusageLink=KAL_ZusageLink;
 $ksZusagePopup=KAL_ZusagePopup; $ksPopupBreit=KAL_PopupBreit; $ksPopupHoch=KAL_PopupHoch; $ksFreischaltWin=KAL_FreischaltWin;
 $ksZusageEintragMail=KAL_ZusageEintragMail; $ksZusageAendernMail=KAL_ZusageAendernMail; $ksZusageFreigabeMail=KAL_ZusageFreigabeMail;
 $ksTxZusageEintrBtr=KAL_TxZusageEintrBtr; $ksTxZusageEintrMTx=KAL_TxZusageEintrMTx;
 $ksTxZusageAendBtr=KAL_TxZusageAendBtr; $ksTxZusageAendMTx=KAL_TxZusageAendMTx;
 $ksTxZusageFreiBtr=KAL_TxZusageFreiBtr; $ksTxZusageFreiMTx=KAL_TxZusageFreiMTx;
 $ksTxZusageLschBtr=KAL_TxZusageLschBtr; $ksTxZusageLschMTx=KAL_TxZusageLschMTx;
 $ksTxZusageInfoBtr=KAL_TxZusageInfoBtr; $ksTxZusageInfoMTx=KAL_TxZusageInfoMTx;
 $ksTxZusageAenABtr=KAL_TxZusageAenABtr; $ksTxZusageAenAMTx=KAL_TxZusageAenAMTx;
 $ksTxZusageKontBtr=KAL_TxZusageKontBtr; $ksTxZusageKontMTx=KAL_TxZusageKontMTx; $ksTxNZusageAlle=KAL_TxNZusageAlle;
 $ksZusageNeuInfoAut=KAL_ZusageNeuInfoAut; $ksZusageBstInfoAut=KAL_ZusageBstInfoAut; $ksZusageAendInfoAut=KAL_ZusageAendInfoAut; $ksZusageLschInfoAut=KAL_ZusageLschInfoAut; $ksZusageLschNzZusag=KAL_ZusageLschNzZusag;
 $ksZusageAendEigene=KAL_ZusageAendEigene; $ksZusageLschEigene=KAL_ZusageLschEigene;
 $ksZusageNeuInfoAdm=KAL_ZusageNeuInfoAdm; $ksEmpfZusage=KAL_EmpfZusage; $ksZusageBstInfoAdm=KAL_ZusageBstInfoAdm; $ksZusageAendInfoAdm=KAL_ZusageAendInfoAdm; $ksZusageLschInfoAdm=KAL_ZusageLschInfoAdm;
 $ksZusageVmkInfoVorbei=KAL_ZusageVmkInfoVorbei; $ksTxZusageVmkVorbeiBtr=KAL_TxZusageVmkVorbeiBtr; $ksTxZusageVmkVorbeiMTx=KAL_TxZusageVmkVorbeiMTx;
 $ksZusageMaxKapInfoAdm=KAL_ZusageMaxKapInfoAdm; $ksZusageGrenzeInfoAdm=KAL_ZusageGrenzeInfoAdm; $ksZusageMaxKapInfoAut=KAL_ZusageMaxKapInfoAut; $ksZusageGrenzeInfoAut=KAL_ZusageGrenzeInfoAut;
 $ksTxZusageMaxKapBtr=KAL_TxZusageMaxKapBtr; $ksTxZusageGrenzeBtr=KAL_TxZusageGrenzeBtr; $ksTxZusageMaxKapMTx=KAL_TxZusageMaxKapMTx;
 $ksZusageAAendInfoZs=KAL_ZusageAAendInfoZs; $ksZusageAAendInfoAu=KAL_ZusageAAendInfoAu;
 $ksZusageNameAnzahl=KAL_ZusageNameAnzahl; $ksZusageNameKapaz=KAL_ZusageNameKapaz; $ksZusageKapazVersteckt=KAL_ZusageKapazVersteckt;
 $ksZusageSelectAnzahl=KAL_ZusageSelectAnzahl; $ksZusageNamentlich=KAL_ZusageNamentlich; $ksZusageMaxNamenOhneKapaz=KAL_ZusageMaxNamenOhneKapaz; $ksZusageMaxNamenMitKapaz=KAL_ZusageMaxNamenMitKapaz;
 $ksTerminDatumFeld=KAL_TerminDatumFeld; $ksTerminZeitFeld=KAL_TerminZeitFeld; $ksTerminVeranstFeld=KAL_TerminVeranstFeld; $ksZusageVeranstLaenge=KAL_ZusageVeranstLaenge;
 $ksTxZusageKapazRest=KAL_TxZusageKapazRest; $ksTxZusageKapazNull=KAL_TxZusageKapazNull; $ksZaehleAktiveZusagen=KAL_ZaehleAktiveZusagen;
 $ksListeZeigeZusage=KAL_ListeZeigeZusage; $ksGastLZeigeZusage=KAL_GastLZeigeZusage;
 $ksDetailZeigeZusage=KAL_DetailZeigeZusage; $ksGastDZeigeZusage=KAL_GastDZeigeZusage;
 $ksZusageFormMitListe=KAL_ZusageFormMitListe; $ksGastZusageFormMitL=KAL_GastZusageFormMitL;
 $ksTxZusagenBisher=KAL_TxZusagenBisher; $ksTxKeineZusagen=KAL_TxKeineZusagen;
 $ksTxZeigeZusageIcon=KAL_TxZeigeZusageIcon; $ksZeigeZusageEml=KAL_ZeigeZusageEml;
 $ksTxZusage1Status=KAL_TxZusage1Status; $ksTxZusage2Status=KAL_TxZusage2Status; $ksTxZusage0Status=KAL_TxZusage0Status; $ksTxZusage3Status=KAL_TxZusage3Status; $ksTxZusage4Status=KAL_TxZusage4Status;
 $ksTxNzUebersicht=KAL_TxNzUebersicht; $ksTxNfUebersicht=KAL_TxNfUebersicht;
 $ksTxZusagenSuchen=KAL_TxZusagenSuchen; $ksTxNfSuchErgebnis=KAL_TxNfSuchErgebnis;
 $ksTxZusageDrckTit=KAL_TxZusageDrckTit; $ksTxZusageDrckTrm=KAL_TxZusageDrckTrm; $ksTxZusageDrckSum=KAL_TxZusageDrckSum;
 $ksZusageAdmDrKonf=KAL_ZusageAdmDrKonf; $ksZentrumEigeneZusage=KAL_ZentrumEigeneZusage; $ksZentrumFremdeZusage=KAL_ZentrumFremdeZusage;
 $aZusageLstFeld=explode(';',KAL_ZusageLstFeld); $aNZusageLstFld=explode(';',KAL_NZusageLstFld);
 $aZusageFrmFeld=explode(';',KAL_ZusageFrmFeld); $ksZusageListeStatus=KAL_ZusageListeStatus;
 $aZusageListAdm=explode(';',KAL_ZusageListAdm); $aZusageDrckAdm=explode(';',KAL_ZusageDrckAdm); $aZusageExptAdm=explode(';',KAL_ZusageExptAdm);
 $amZusageTrmListe=ADM_ZusageTrmListe; $amListenZusagSp=ADM_ListenZusagSp; $amZusageTrmEintrag=ADM_ZusageTrmEintrag;
 $ksZusageAdmLstVstBreit=KAL_ZusageAdmLstVstBreit; $ksZusageAdmLstLaenge=KAL_ZusageAdmLstLaenge; $ksZusageAdmRueckw=KAL_ZusageAdmRueckw;
 $ksZusageAdmLstKommend=KAL_ZusageAdmLstKommend; $ksZusageAdmLstVorbei=KAL_ZusageAdmLstVorbei; $ksZusageLstFilter=KAL_ZusageLstFilter;
 $ksZusageDSE1=KAL_ZusageDSE1; $ksZusageDSE2=KAL_ZusageDSE2;
 $ksCaptcha=KAL_Captcha; $ksAendernCaptcha=KAL_AendernCaptcha; $ksCaptchaHgFarb=KAL_CaptchaHgFarb; $ksCaptchaTxFarb=KAL_CaptchaTxFarb;
 $ksCaptchaTyp=KAL_CaptchaTyp; $ksCaptchaGrafisch=KAL_CaptchaGrafisch; $ksCaptchaNumerisch=KAL_CaptchaNumerisch; $ksCaptchaTextlich=KAL_CaptchaTextlich;
 $ksLoeschGastZusage=KAL_LoeschGastZusage; $ksLoeschNutzerZusage=KAL_LoeschNutzerZusage;
 $ksTxZusageLschFrueher=KAL_TxZusageLschFrueher; $ksTxZusageKeinFrueher=KAL_TxZusageKeinFrueher;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false; $ksZusageNeu=(int)txtVar('ZusageNeu');
 $s=txtVar('ZUser'); if(fSetzKalWert($s,'ZUser',"'")) $bNeu=true; $s=txtVar('ZCode'); if(fSetzKalWert($s,'ZCode',"'")) $bNeu=true;
 $nZusageFeldZahl=max((int)txtVar('ZusageFeldZahl'),8); $sZusageFelder='Nummer;Termin-Nr';
 for($i=2;$i<=$nZusageFeldZahl;$i++){$sZusageFelder.=';'.str_replace(';','`,',txtVar('F'.$i)); $aPfl[$i]=(isset($_POST['P'.$i])?$_POST['P'.$i]:'0');}
 $sZusagePflicht='1;1'; $sZusageQuellen='0;0'; $sZusageFeldTyp='i;i;d;z;t;@;t;n;e'; $sZusageAuswahl=';;;;;;;;'; $aPfl[2]='1'; $aPfl[4]='1'; $aPfl[5]='1';
 for($i=9;$i<=$nZusageFeldZahl;$i++){$s=(isset($_POST['ZFT'.$i])?$_POST['ZFT'.$i]:'t'); $sZusageFeldTyp.=';'.$s; $sZusageAuswahl.=';'.($s!='a'?'':$kal_ZusageAuswahl[$i]);}
 for($i=2;$i<=$nZusageFeldZahl;$i++){$sZusagePflicht.=';'.$aPfl[$i]; $sZusageQuellen.=';'.(isset($_POST['ZusageQuelle'.$i])&&$_POST['ZusageQuelle'.$i]>''?$_POST['ZusageQuelle'.$i]:'0');}
 if(fSetzKalWert($sZusageFelder,'ZusageFelder',"'")) $bNeu=true;
 if(fSetzKalWert($sZusageFeldTyp,'ZusageFeldTyp',"'")) $bNeu=true; if(fSetzKalWert($sZusageAuswahl,'ZusageAuswahl',"'")) $bNeu=true;
 if(fSetzKalWert($sZusagePflicht,'ZusagePflicht',"'")) $bNeu=true; if(fSetzKalWert($sZusageQuellen,'ZusageQuellen',"'")) $bNeu=true;
 $s=($bInstalliert&&$bVersionOK?(int)txtVar('ZusageSystem'):0); if(fSetzKalWert(($s?true:false),'ZusageSystem','')) $bNeu=true;
 $s=(int)txtVar('PruefeZusageKapaz'); if(fSetzKalWert(($s?true:false),'PruefeZusageKapaz','')) $bNeu=true;
 $s=(int)txtVar('ErlaubeZusageNull'); if(fSetzKalWert(($s?true:false),'ErlaubeZusageNull','')) $bNeu=true;
 $s=(int)txtVar('Pruefe1KlickKapaz'); if(fSetzKalWert(($s?true:false),'Pruefe1KlickKapaz','')) $bNeu=true;
 $s=(int)txtVar('ZusageVormerkErlaubt'); if(fSetzKalWert(($s?true:false),'ZusageVormerkErlaubt','')) $bNeu=true;
 $s=(int)txtVar('ZusageVormerkTxtZeile'); if(fSetzKalWert(($s?true:false),'ZusageVormerkTxtZeile','')) $bNeu=true;
 $s=txtVar('TxZusageVormerk'); if(fSetzKalWert($s,'TxZusageVormerk',"'")) $bNeu=true;
 $s=txtVar('TxZusageVmkErlaubt'); if(fSetzKalWert($s,'TxZusageVmkErlaubt',"'")) $bNeu=true;
 $s=(int)txtVar('DirektZusage'); if(fSetzKalWert($s,'DirektZusage','')) $bNeu=true;
 $s=(int)txtVar('EinKlickLZusage'); if(fSetzKalWert(($s?true:false),'EinKlickLZusage','')) $bNeu=true;
 $s=(int)txtVar('EinKlickDZusage'); if(fSetzKalWert(($s?true:false),'EinKlickDZusage','')) $bNeu=true;
 $s=(int)txtVar('LoeschKlickZusage'); if(fSetzKalWert(($s?true:false),'LoeschKlickZusage','')) $bNeu=true;
 $s=(int)txtVar('RechneZusagePreis'); if(fSetzKalWert(($s?true:false),'RechneZusagePreis','')) $bNeu=true;
 $s=(int)txtVar('SperreZusagePreis'); if(fSetzKalWert(($s?true:false),'SperreZusagePreis','')) $bNeu=true;
 $s=txtVar('TxZusageMeld'); if(fSetzKalWert($s,'TxZusageMeld',"'")) $bNeu=true;
 $s=txtVar('TxZusageEintr'); if(fSetzKalWert($s,'TxZusageEintr',"'")) $bNeu=true;
 $s=txtVar('TxZusageGeloescht'); if(fSetzKalWert($s,'TxZusageGeloescht',"'")) $bNeu=true;
 $s=txtVar('TxZusageSperre'); if(fSetzKalWert($s,'TxZusageSperre',"'")) $bNeu=true;
 $s=txtVar('TxZusageKapazEnde'); if(fSetzKalWert($s,'TxZusageKapazEnde',"'")) $bNeu=true;
 $s=txtVar('TxKeineDoppelBuchung'); if(fSetzKalWert($s,'TxKeineDoppelBuchung',"'")) $bNeu=true;
 $s=(int)txtVar('KeineDoppelBuchung'); if(fSetzKalWert(($s?true:false),'KeineDoppelBuchung','')) $bNeu=true;
 $s=txtVar('TxNzZusageAenden'); if(fSetzKalWert($s,'TxNzZusageAenden',"'")) $bNeu=true;
 $s=txtVar('TxNzZusageAendOk'); if(fSetzKalWert($s,'TxNzZusageAendOk',"'")) $bNeu=true;
 $s=txtVar('TxNzLoeschen'); if(fSetzKalWert($s,'TxNzLoeschen',"'")) $bNeu=true;
 $s=txtVar('TxNzGeloescht'); if(fSetzKalWert($s,'TxNzGeloescht',"'")) $bNeu=true;
 $s=txtVar('FormAendZusageInfo'); if(fSetzKalWert($s,'FormAendZusageInfo',"'")) $bNeu=true;
 $s=txtVar('TxNzUnveraendert'); if(fSetzKalWert($s,'TxNzUnveraendert',"'")) $bNeu=true;
 $s=txtVar('TxNzZusageStat'); if(fSetzKalWert($s,'TxNzZusageStat',"'")) $bNeu=true;
 $s=txtVar('TxZEinKlickEFrage'); if(fSetzKalWert($s,'TxZEinKlickEFrage',"'")) $bNeu=true;
 $s=txtVar('TxZEinKlickLFrage'); if(fSetzKalWert($s,'TxZEinKlickLFrage',"'")) $bNeu=true;
 $s=txtVar('TxZEinKlickLoesch'); if(fSetzKalWert($s,'TxZEinKlickLoesch',"'")) $bNeu=true;
 $s=txtVar('ZusageFrist'); if(strlen($s)>0) $s=max((int)$s,0); else $s=-1; if(fSetzKalWert($s,'ZusageFrist','')) $bNeu=true;
 $s=txtVar('ZusageNameFrist'); if(fSetzKalWert($s,'ZusageNameFrist',"'")) $bNeu=true;
 $s=(int)txtVar('ZusageBisEnde'); if(fSetzKalWert(($s?true:false),'ZusageBisEnde','')) $bNeu=true;
 $s=(int)txtVar('ZusagePopup'); if(fSetzKalWert(($s?true:false),'ZusagePopup','')) $bNeu=true;
 $s=max((int)txtVar('PopupBreit'),80); if(fSetzKalWert($s,'PopupBreit','')) $bNeu=true;
 $s=max((int)txtVar('PopupHoch'),50);  if(fSetzKalWert($s,'PopupHoch','')) $bNeu=true;
 $s=txtVar('TxZusageBestaetigen'); if(fSetzKalWert($s,'TxZusageBestaetigen',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageBestaetTxt')))); if(fSetzKalWert($s,'TxZusageBestaetTxt',"'")) $bNeu=true;
 $s=txtVar('TxZusageBestaetigt'); if(fSetzKalWert($s,'TxZusageBestaetigt',"'")) $bNeu=true;
 $s=txtVar('TxZusageWiderruf'); if(fSetzKalWert($s,'TxZusageWiderruf',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageWdrrufTxt')))); if(fSetzKalWert($s,'TxZusageWdrrufTxt',"'")) $bNeu=true;
 $s=txtVar('TxZusageIstWdrrufen'); if(fSetzKalWert($s,'TxZusageIstWdrrufen',"'")) $bNeu=true;
 $s=txtVar('FreischaltWin'); if(fSetzKalWert($s,'FreischaltWin',"'")) $bNeu=true;
 $s=(int)txtVar('ZusageEintragMail'); if(fSetzKalWert(($s?true:false),'ZusageEintragMail','')) $bNeu=true;
 $s=(int)txtVar('ZusageAendernMail'); if(fSetzKalWert(($s?true:false),'ZusageAendernMail','')) $bNeu=true;
 $s=(int)txtVar('ZusageFreigabeMail'); if(fSetzKalWert(($s?true:false),'ZusageFreigabeMail','')) $bNeu=true;
 $s=txtVar('TxZusageEintrBtr'); if(fSetzKalWert($s,'TxZusageEintrBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageEintrMTx')))); if(fSetzKalWert($s,'TxZusageEintrMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageAendBtr'); if(fSetzKalWert($s,'TxZusageAendBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageAendMTx')))); if(fSetzKalWert($s,'TxZusageAendMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageFreiBtr'); if(fSetzKalWert($s,'TxZusageFreiBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageFreiMTx')))); if(fSetzKalWert($s,'TxZusageFreiMTx',"'")) $bNeu=true;
 $s=(int)txtVar('ZusageNeuInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageNeuInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageBstInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageBstInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageAendInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageAendInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageAendEigene'); if(fSetzKalWert(($s?true:false),'ZusageAendEigene','')) $bNeu=true;
 $s=(int)txtVar('ZusageLschInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageLschInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageLschEigene'); if(fSetzKalWert(($s?true:false),'ZusageLschEigene','')) $bNeu=true;
 $s=(int)txtVar('ZusageLschNzZusag'); if(fSetzKalWert(($s?true:false),'ZusageLschNzZusag','')) $bNeu=true;
 $s=(int)txtVar('ZusageNeuInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageNeuInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageBstInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageBstInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageAendInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageAendInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageLschInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageLschInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageMaxKapInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageMaxKapInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageVmkInfoVorbei'); if(fSetzKalWert(($s?true:false),'ZusageVmkInfoVorbei','')) $bNeu=true;
 $s=(int)txtVar('ZusageGrenzeInfoAdm'); if(fSetzKalWert(($s?true:false),'ZusageGrenzeInfoAdm','')) $bNeu=true;
 $s=(int)txtVar('ZusageMaxKapInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageMaxKapInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageGrenzeInfoAut'); if(fSetzKalWert(($s?true:false),'ZusageGrenzeInfoAut','')) $bNeu=true;
 $s=(int)txtVar('ZusageAAendInfoZs'); if(fSetzKalWert(($s?true:false),'ZusageAAendInfoZs','')) $bNeu=true;
 $s=(int)txtVar('ZusageAAendInfoAu'); if(fSetzKalWert(($s?true:false),'ZusageAAendInfoAu','')) $bNeu=true;
 $s=(int)txtVar('ZentrumEigeneZusage'); if(fSetzKalWert(($s?true:false),'ZentrumEigeneZusage','')) $bNeu=true;
 $s=(int)txtVar('ZentrumFremdeZusage'); if(fSetzKalWert(($s?true:false),'ZentrumFremdeZusage','')) $bNeu=true;
 $s=txtVar('EmpfZusage'); if(fSetzKalWert($s,'EmpfZusage',"'")) $bNeu=true;
 $s=txtVar('TxZusageInfoBtr'); if(fSetzKalWert($s,'TxZusageInfoBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageInfoMTx')))); if(fSetzKalWert($s,'TxZusageInfoMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageAenABtr'); if(fSetzKalWert($s,'TxZusageAenABtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageAenAMTx')))); if(fSetzKalWert($s,'TxZusageAenAMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageMaxKapBtr'); if(fSetzKalWert($s,'TxZusageMaxKapBtr',"'")) $bNeu=true;
 $s=txtVar('TxZusageGrenzeBtr'); if(fSetzKalWert($s,'TxZusageGrenzeBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageMaxKapMTx')))); if(fSetzKalWert($s,'TxZusageMaxKapMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageLschBtr'); if(fSetzKalWert($s,'TxZusageLschBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageLschMTx')))); if(fSetzKalWert($s,'TxZusageLschMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageVmkVorbeiBtr'); if(fSetzKalWert($s,'TxZusageVmkVorbeiBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageVmkVorbeiMTx')))); if(fSetzKalWert($s,'TxZusageVmkVorbeiMTx',"'")) $bNeu=true;
 $s=txtVar('TxZusageKontBtr'); if(fSetzKalWert($s,'TxZusageKontBtr',"'")) $bNeu=true;
 $s=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('TxZusageKontMTx')))); if(fSetzKalWert($s,'TxZusageKontMTx',"'")) $bNeu=true;
 $s=txtVar('TxNZusageAlle'); if(fSetzKalWert($s,'TxNZusageAlle',"'")) $bNeu=true;
 $s=txtVar('TxZusageIcon'); if(fSetzKalWert($s,'TxZusageIcon',"'")) $bNeu=true;
 $s=txtVar('TxZugesagtIcon'); if(fSetzKalWert($s,'TxZugesagtIcon',"'")) $bNeu=true;
 $s=txtVar('ListenZusage'); if(fSetzKalWert($s,'ListenZusage','')) $bNeu=true;
 $s=(int)txtVar('GastLZusage'); if(fSetzKalWert(($s?true:false),'GastLZusage','')) $bNeu=true;
 $s=txtVar('TxListenZusageTitel'); if(fSetzKalWert($s,'TxListenZusageTitel',"'")) $bNeu=true;

 $s=(int)txtVar('ListenZusagS'); if(fSetzKalWert($s,'ListenZusagS','')) $bNeu=true;
 $s=(int)txtVar('GastLZusagS'); if(fSetzKalWert(($s?true:false),'GastLZusagS','')) $bNeu=true;
 $s=(int)txtVar('DetailZusagS'); if(fSetzKalWert(($s?true:false),'DetailZusagS','')) $bNeu=true;
 $s=txtVar('TxListenZusagSTitel'); if(fSetzKalWert($s,'TxListenZusagSTitel',"'")) $bNeu=true;
 $s=max((int)txtVar('ZusageStatusRot'),1); if(fSetzKalWert($s,'ZusageStatusRot','')) $bNeu=true;
 $s=max((int)txtVar('ZusageStatusGlb'),$ksZusageStatusRot+1); if(fSetzKalWert($s,'ZusageStatusGlb','')) $bNeu=true;
 $s=(int)txtVar('ZusageStatusSchwelle'); if(fSetzKalWert(($s?true:false),'ZusageStatusSchwelle','')) $bNeu=true;
 $s=txtVar('TxZusageStatusRot'); if(fSetzKalWert($s,'TxZusageStatusRot',"'")) $bNeu=true;
 $s=txtVar('TxZusageStatusGlb'); if(fSetzKalWert($s,'TxZusageStatusGlb',"'")) $bNeu=true;
 $s=txtVar('TxZusageStatusGrn'); if(fSetzKalWert($s,'TxZusageStatusGrn',"'")) $bNeu=true;
 $s=txtVar('TxDetailZusagZMuster'); if(fSetzKalWert($s,'TxDetailZusagZMuster',"'")) $bNeu=true;
 $s=txtVar('DetailZusagKLeer'); if(fSetzKalWert($s,'DetailZusagKLeer',"'")) $bNeu=true;
 $s=txtVar('DetailZusagRLeer'); if(fSetzKalWert($s,'DetailZusagRLeer',"'")) $bNeu=true;
 $v=txtVar('MonatsZusage'); if(fSetzKalWert(($v?true:false),'MonatsZusage','')) $bNeu=true;
 $w=txtVar('GastMoZusage'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoZusage','')) $bNeu=true;
 $v=txtVar('MonatZeigeZusage'); if(fSetzKalWert(($v?true:false),'MonatZeigeZusage','')) $bNeu=true;
 $w=txtVar('GastMZeigeZusage'); if(fSetzKalWert((($v&&$w)?true:false),'GastMZeigeZusage','')) $bNeu=true;
 $v=txtVar('MonZusageHG'); if(fSetzKalWert(($v?true:false),'MonZusageHG','')) $bNeu=true;
 $v=(txtVar('MonatsZusagS')||$v); if(fSetzKalWert(($v?true:false),'MonatsZusagS','')) $bNeu=true;
 $w=txtVar('GastMoZusagS'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoZusagS','')) $bNeu=true;
 $v=txtVar('MonZusagHGBl'); if(fSetzKalWert($v,'MonZusagHGBl',"'")) $bNeu=true;
 if($v=='Det'&&KAL_MonatsKateg) fSetzKalWert(false,'MonatsKateg',''); // Kategorien ausschalten
 $v=txtVar('MonatsZusagZ'); if(fSetzKalWert(($v?true:false),'MonatsZusagZ','')) $bNeu=true;
 $w=txtVar('GastMoZusagZ'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoZusagZ','')) $bNeu=true;
 $v=txtVar('MonZusagZZeigeLeer'); if(fSetzKalWert(($v?true:false),'MonZusagZZeigeLeer','')) $bNeu=true;
 $v=txtVar('MonZusagZErsatz'); if(fSetzKalWert($v,'MonZusagZErsatz',"'")) $bNeu=true;
 $v=txtVar('MonZusagZMuster'); if(fSetzKalWert($v,'MonZusagZMuster',"'")) $bNeu=true;

 $s=txtVar('ListenZusagZ'); if(fSetzKalWert($s,'ListenZusagZ','')) $bNeu=true;
 $s=(int)txtVar('GastLZusagZ'); if(fSetzKalWert(($s?true:false),'GastLZusagZ','')) $bNeu=true;
 $s=txtVar('ListenZusagZLeer'); if(fSetzKalWert($s,'ListenZusagZLeer',"'")) $bNeu=true;
 $s=txtVar('ListenZusagRLeer'); if(fSetzKalWert($s,'ListenZusagRLeer',"'")) $bNeu=true;
 $s=txtVar('TxListenZusagZTitel'); if(fSetzKalWert($s,'TxListenZusagZTitel',"'")) $bNeu=true;
 $s=txtVar('TxListenZusagZMuster'); if(fSetzKalWert($s,'TxListenZusagZMuster',"'")) $bNeu=true;
 $s=txtVar('DetailZusage'); if(fSetzKalWert($s,'DetailZusage','')) $bNeu=true;
 $s=(int)txtVar('GastDZusage'); if(fSetzKalWert(($s?true:false),'GastDZusage','')) $bNeu=true;
 $s=txtVar('TxZusageZeile'); if(fSetzKalWert($s,'TxZusageZeile',"'")) $bNeu=true;
 if(!$s=txtVar('Zusage')) $s='zusagen.txt'; if(fSetzKalWert($s,'Zusage',"'")) $bNeu=true;
 if(!$s=txtVar('SqlTabZ')) $s='kal_zusage'; if(fSetzKalWert($s,'SqlTabZ',"'")) $bNeu=true;
 $s=txtVar('ZusageLink'); if(fSetzKalWert($s,'ZusageLink',"'")) $bNeu=true;
 $s=(int)txtVar('TerminDatumFeld'); if(fSetzKalWert($s,'TerminDatumFeld','')) $bNeu=true;
 $s=(int)txtVar('TerminZeitFeld'); if(fSetzKalWert($s,'TerminZeitFeld','')) $bNeu=true;
 $s=(int)txtVar('TerminVeranstFeld'); if(fSetzKalWert($s,'TerminVeranstFeld','')) $bNeu=true;
 $s=max(min((int)txtVar('ZusageVeranstLaenge'),250),1); if(fSetzKalWert($s,'ZusageVeranstLaenge','')) $bNeu=true;
 $s=txtVar('ZusageNameAnzahl'); if(fSetzKalWert($s,'ZusageNameAnzahl',"'")) $bNeu=true;
 $s=txtVar('TxZusageKapazRest'); if(fSetzKalWert($s,'TxZusageKapazRest',"'")) $bNeu=true;
 $s=txtVar('TxZusageKapazNull'); if(fSetzKalWert($s,'TxZusageKapazNull',"'")) $bNeu=true;
 $s=(int)txtVar('ZaehleAktiveZusagen'); if(fSetzKalWert(($s?true:false),'ZaehleAktiveZusagen','')) $bNeu=true;
 $s=txtVar('ZusageNameKapaz');  if(fSetzKalWert($s,'ZusageNameKapaz',"'")) $bNeu=true;
 $s=(int)txtVar('ZusageSelectAnzahl'); if(fSetzKalWert(($s?true:false),'ZusageSelectAnzahl','')) $bNeu=true;
 $s=(int)txtVar('ZusageNamentlich'); if(fSetzKalWert(($s?true:false),'ZusageNamentlich','')) $bNeu=true;
 $s=min(max((int)txtVar('ZusageMaxNamenOhneKapaz'),1),10); if(fSetzKalWert($s,'ZusageMaxNamenOhneKapaz','')) $bNeu=true;
 $s=min(max((int)txtVar('ZusageMaxNamenMitKapaz'),1),25); if(fSetzKalWert($s,'ZusageMaxNamenMitKapaz','')) $bNeu=true;
 $s=(int)txtVar('ZusageKapazVersteckt'); if(fSetzKalWert(($s?true:false),'ZusageKapazVersteckt','')) $bNeu=true;
 $s=(int)txtVar('ListeZeigeZusage'); if(fSetzKalWert(($s?true:false),'ListeZeigeZusage','')) $bNeu=true;
 $s=(int)txtVar('GastLZeigeZusage'); if(fSetzKalWert(($s?true:false),'GastLZeigeZusage','')) $bNeu=true;
 $s=(int)txtVar('DetailZeigeZusage'); if(fSetzKalWert(($s?true:false),'DetailZeigeZusage','')) $bNeu=true;
 $s=(int)txtVar('GastDZeigeZusage'); if(fSetzKalWert(($s?true:false),'GastDZeigeZusage','')) $bNeu=true;
 $s=(int)txtVar('ZusageFormMitListe'); if(fSetzKalWert(($s?true:false),'ZusageFormMitListe','')) $bNeu=true;
 $s=(int)txtVar('GastZusageFormMitL'); if(fSetzKalWert(($s?true:false),'GastZusageFormMitL','')) $bNeu=true;
 $s=txtVar('TxZeigeZusageIcon'); if(fSetzKalWert($s,'TxZeigeZusageIcon',"'")) $bNeu=true;
 $s=(int)txtVar('ZeigeZusageEml'); if(fSetzKalWert(($s?true:false),'ZeigeZusageEml','')) $bNeu=true;
 $s=(int)txtVar('ZusageListeStatus'); if(fSetzKalWert($s,'ZusageListeStatus','')) $bNeu=true;
 $s=txtVar('TxZusage1Status'); if(fSetzKalWert($s,'TxZusage1Status',"'")) $bNeu=true;
 $s=txtVar('TxZusage2Status'); if(fSetzKalWert($s,'TxZusage2Status',"'")) $bNeu=true;
 $s=txtVar('TxZusage0Status'); if(fSetzKalWert($s,'TxZusage0Status',"'")) $bNeu=true;
 $s=txtVar('TxZusage3Status'); if(fSetzKalWert($s,'TxZusage3Status',"'")) $bNeu=true;
 $s=txtVar('TxZusage4Status'); if(fSetzKalWert($s,'TxZusage4Status',"'")) $bNeu=true;
 $s=txtVar('TxNzUebersicht'); if(fSetzKalWert($s,'TxNzUebersicht',"'")) $bNeu=true;
 $s=txtVar('TxNfUebersicht'); if(fSetzKalWert($s,'TxNfUebersicht',"'")) $bNeu=true;
 $s=txtVar('TxZusagenSuchen'); if(fSetzKalWert($s,'TxZusagenSuchen',"'")) $bNeu=true;
 $s=txtVar('TxNfSuchErgebnis'); if(fSetzKalWert($s,'TxNfSuchErgebnis',"'")) $bNeu=true;
 $s=txtVar('TxZusageDrckTit'); if(fSetzKalWert($s,'TxZusageDrckTit',"'")) $bNeu=true;
 $s=txtVar('TxZusageDrckTrm'); if(fSetzKalWert($s,'TxZusageDrckTrm',"'")) $bNeu=true;
 $s=txtVar('TxZusageDrckSum'); if(fSetzKalWert($s,'TxZusageDrckSum',"'")) $bNeu=true;
 $s=txtVar('TxZusagenBisher'); if(fSetzKalWert($s,'TxZusagenBisher',"'")) $bNeu=true;
 $s=txtVar('TxKeineZusagen'); if(fSetzKalWert($s,'TxKeineZusagen',"'")) $bNeu=true;
 for($i=0;$i<=$nZusageFeldZahl;$i++){
  $aZLF[$i]=(isset($_POST['ZLF'.$i])?(int)$_POST['ZLF'.$i]:0); $aNZF[$i]=(isset($_POST['NZF'.$i])?(int)$_POST['NZF'.$i]:0);
  $aZSF[$i]=(isset($_POST['ZSF'.$i])?(int)$_POST['ZSF'.$i]:0); $aLsA[$i]=(isset($_POST['LsA'.$i])?(int)$_POST['LsA'.$i]:0);
  $aDrA[$i]=(isset($_POST['DrA'.$i])?(int)$_POST['DrA'.$i]:0); $aExA[$i]=(isset($_POST['ExA'.$i])?(int)$_POST['ExA'.$i]:0);
 }
 asort($aZLF); reset($aZLF); $sZLF=''; $j=0; foreach($aZLF as $k=>$v) if($v>0) if($k>0) $aZLF[$k]=++$j;
 asort($aNZF); reset($aNZF); $sNZF=''; $j=0; foreach($aNZF as $k=>$v) if($v>0) if($k>0) $aNZF[$k]=++$j;
 asort($aZSF); reset($aZSF); $sZSF=''; $j=0; foreach($aZSF as $k=>$v) if($v>0) if($k>0) $aZSF[$k]=++$j;
 asort($aLsA); reset($aLsA); $sLsA=''; $j=0; foreach($aLsA as $k=>$v) if($v>0) if($k>0) $aLsA[$k]=++$j;
 asort($aDrA); reset($aDrA); $sDrA=''; $j=0; foreach($aDrA as $k=>$v) if($v>0) if($k>0) $aDrA[$k]=++$j;
 asort($aExA); reset($aExA); $sExA=''; $j=0; foreach($aExA as $k=>$v) if($v>0) if($k>0) $aExA[$k]=++$j;
 for($i=0;$i<=$nZusageFeldZahl;$i++){$sZLF.=';'.$aZLF[$i]; $sNZF.=';'.$aNZF[$i]; $sZSF.=';'.$aZSF[$i]; $sLsA.=';'.$aLsA[$i]; $sDrA.=';'.$aDrA[$i]; $sExA.=';'.$aExA[$i];}
 $s=substr($sZLF,1); $aZusageLstFeld=explode(';',$s); if(fSetzKalWert($s,'ZusageLstFeld',"'")) $bNeu=true;
 $s=substr($sNZF,1); $aNZusageLstFld=explode(';',$s); if(fSetzKalWert($s,'NZusageLstFld',"'")) $bNeu=true;
 $s=substr($sZSF,1); $aZusageFrmFeld=explode(';',$s); if(fSetzKalWert($s,'ZusageFrmFeld',"'")) $bNeu=true;
 $s=substr($sLsA,1); $aZusageListAdm=explode(';',$s); if(fSetzKalWert($s,'ZusageListAdm',"'")) $bNeu=true;
 $s=substr($sDrA,1); $aZusageDrckAdm=explode(';',$s); if(fSetzKalWert($s,'ZusageDrckAdm',"'")) $bNeu=true;
 $s=substr($sExA,1); $aZusageExptAdm=explode(';',$s); if(fSetzKalWert($s,'ZusageExptAdm',"'")) $bNeu=true;
 $s=Cod($ksZUser,$ksZCode); if(fSetzKalWert(($s?true:false),'Zusagen','')) $bNeu=true;
 $s=(int)txtVar('ListenZusagSp'); if(setzAdmWert($s,'ListenZusagSp','')) $bNeu=true;
 $s=(int)txtVar('ZusageTrmListe'); if(setzAdmWert(($s?true:false),'ZusageTrmListe','')) $bNeu=true;
 $s=(int)txtVar('ZusageTrmEintrag'); if(setzAdmWert(($s?true:false),'ZusageTrmEintrag','')) $bNeu=true;
 $s=(int)txtVar('ZusageAdmRueckw'); if(fSetzKalWert(($s?true:false),'ZusageAdmRueckw','')) $bNeu=true;
 $s=(int)txtVar('ZusageAdmLstLaenge'); if(fSetzKalWert($s,'ZusageAdmLstLaenge','')) $bNeu=true;
 $s=max((int)txtVar('ZusageAdmLstVstBreit'),9); if(fSetzKalWert($s,'ZusageAdmLstVstBreit','')) $bNeu=true;
 $s=(int)txtVar('ZusageAdmLstVorbei'); if(fSetzKalWert(($s?true:false),'ZusageAdmLstVorbei','')) $bNeu=true;
 $s=((int)txtVar('ZusageAdmLstKommend')||!$ksZusageAdmLstVorbei); if(fSetzKalWert(($s?true:false),'ZusageAdmLstKommend','')) $bNeu=true;
 $s=(int)txtVar('ZusageAdmDrKonf'); if(fSetzKalWert(($s?true:false),'ZusageAdmDrKonf','')) $bNeu=true;
 $s=(int)txtVar('ZusageLstFilter'); if(fSetzKalWert($s,'ZusageLstFilter','')) $bNeu=true;
 $s=txtVar('ZusageDSE1'); if(fSetzKalWert(($s?true:false),'ZusageDSE1','')) $bNeu=true;
 $s=txtVar('ZusageDSE2'); if(fSetzKalWert(($s?true:false),'ZusageDSE2','')) $bNeu=true;
 $s=txtVar('Captcha'); if(fSetzKalWert(($s?true:false),'Captcha','')) $bNeu=true;
 $s=txtVar('AendernCaptcha'); if(fSetzKalWert(($s?true:false),'AendernCaptcha','')) $bNeu=true;
 $s=txtVar('CaptchaTxFarb'); if(fSetzKalWert($s,'CaptchaTxFarb',"'")) $bNeu=true;
 $s=txtVar('CaptchaHgFarb'); if(fSetzKalWert($s,'CaptchaHgFarb',"'")) $bNeu=true;
 $s=txtVar('CaptchaTyp'); if(fSetzKalWert($s,'CaptchaTyp',"'")) $bNeu=true;
 $s=txtVar('CaptchaGrafisch'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='G'),'CaptchaGrafisch','')) $bNeu=true;
 $s=txtVar('CaptchaNumerisch'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='N'),'CaptchaNumerisch','')) $bNeu=true;
 $s=txtVar('CaptchaTextlich'); if(fSetzKalWert(($s?true:false)||($ksCaptchaTyp=='T'),'CaptchaTextlich','')) $bNeu=true;
 $s=(int)txtVar('LoeschGastZusage'); if(fSetzKalWert(($s?true:false),'LoeschGastZusage','')) $bNeu=true;
 $s=(int)txtVar('LoeschNutzerZusage'); if(fSetzKalWert(($s?true:false),'LoeschNutzerZusage','')) $bNeu=true;
 $s=txtVar('TxZusageLschFrueher'); if(fSetzKalWert($s,'TxZusageLschFrueher',"'")) $bNeu=true;
 $s=txtVar('TxZusageKeinFrueher'); if(fSetzKalWert($s,'TxZusageKeinFrueher',"'")) $bNeu=true;
 if($bNeu){ //geaendert
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Einstellungen für das Zusagesystem wurden gespeichert.</p>'; $aF=array();
   if($ksZusageSystem){//Daten behandeln
    if(!KAL_SQL){ //bei Textdatei
     if(KAL_ZusageSystem==false||KAL_ZusageFelder!=$sZusageFelder||KAL_Zusage!=$ksZusage){//jetzt aktivieren oder Felder/Dateiname anders
      $aD=(file_exists(KAL_Pfad.KAL_Daten.KAL_Zusage)?@file(KAL_Pfad.KAL_Daten.KAL_Zusage):array()); $nZusageZahl=count($aD); $nMx=0;
      if(!$ksZusageNeu) for($i=1;$i<$nZusageZahl;$i++) $nMx=max($nMx,(int)substr($aD[$i],0,5));
      $s='Nummer_'.$nMx; $s.=substr($sZusageFelder,strpos($sZusageFelder,';')); if($ksZusageNeu) $aD=array(); $aD[0]=$s.NL;
      if($f=fopen(KAL_Pfad.KAL_Daten.$ksZusage,'w')){fwrite($f,rtrim(str_replace("\r",'',implode('',$aD))).NL); fclose($f);}
      else $Msg.='<p class="admFehl">Die Datei <i>'.KAL_Daten.$ksZusage.'</i> durfte nicht geschrieben werden!</p>';
     }
    }elseif($DbO){ //bei SQL
     if(KAL_ZusageSystem==false){ //jetzt aktivieren
      if($ksZusageNeu) $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabZ);
      else if($rR=$DbO->query('SHOW FIELDS FROM '.$ksSqlTabZ.' WHERE FIELD LIKE "dat%"')){
       while($aZ=$rR->fetch_row()) if($s=$aZ[0]) $aF[(int)substr($s,4)]=true; $rR->close();
      }
      if(count($aF)){ //alte Tabelle ist noch da, anpassen
       for($i=99;$i>$nZusageFeldZahl;$i--) if(isset($aF[$i])) $DbO->query('ALTER TABLE '.$ksSqlTabZ.' DROP dat_'.$i);
       for($i=9;$i<=$nZusageFeldZahl;$i++) if(!isset($aF[$i])) $DbO->query('ALTER TABLE '.$ksSqlTabZ.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""');
      }else{ //Tabelle neu anlegen
       $s=''; for($i=9;$i<=$nZusageFeldZahl;$i++) $s.='dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT "",';
       if(!$DbO->query('CREATE TABLE '.$ksSqlTabZ.' (nr INT(10) NOT NULL AUTO_INCREMENT,termin INT(10) NOT NULL DEFAULT "0",datum CHAR(10) NOT NULL DEFAULT "",zeit CHAR(5) NOT NULL DEFAULT "",veranstaltung VARCHAR(255) NOT NULL DEFAULT "",buchung CHAR(16) NOT NULL DEFAULT "",aktiv CHAR(1) NOT NULL DEFAULT "",benutzer VARCHAR(8) NOT NULL DEFAULT "",email VARCHAR(128) NOT NULL DEFAULT "",'.$s.' PRIMARY KEY (nr)) COMMENT="Kalender-Reservierungen"'))
        $Msg.='<p class="admFehl">Die MySQL-Tabelle <i>'.$ksSqlTabZ.'</i> konnte nicht erzeugt werden!</p>';
      }
     }else{ //war schon vorher aktiv, nur aendern
      if(KAL_SqlTabZ!=$ksSqlTabZ){ //Name geaendert
       $DbO->query('DROP TABLE IF EXISTS '.$ksSqlTabZ);
       if(!$DbO->query('CREATE TABLE '.$ksSqlTabZ.' (PRIMARY KEY (nr)) COMMENT="Kalender-Reservierungen" SELECT * FROM '.KAL_SqlTabZ))
        $Msg.='<p class="admFehl">Die neue MySQL-Tabelle <i>'.$ksSqlTabZ.'</i> konnte nicht angelegt werden!</p>';
      }
      if(KAL_ZusageFelder!=$sZusageFelder){ //Felder aendern
       if($nZusageFeldZahl>=$nZusageFelder) for($i=$nZusageFelder;$i<=$nZusageFeldZahl;$i++) $DbO->query('ALTER TABLE '.$ksSqlTabZ.' ADD dat_'.$i.' VARCHAR(255) NOT NULL DEFAULT ""'); //mehr Felder
       else for($i=$nZusageFelder;$i>$nZusageFeldZahl;$i--) $DbO->query('ALTER TABLE '.$ksSqlTabZ.' DROP dat_'.$i); //weniger Felder
      }
     }
    }else $Msg.='<p class="admFehl">Keine offene MySQL-Verbindung vorhanden!</p>'; //SQL
   }
   $kal_ZusageFelder=explode(';',$sZusageFelder); $nZusageFelder=count($kal_ZusageFelder); $kal_ZusageFeldTyp=explode(';',$sZusageFeldTyp);
   $kal_ZusageQuellen=explode(';',$sZusageQuellen); $kal_ZusagePflicht=explode(';',$sZusagePflicht);
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
 }

 if($nTxNr=(int)txtVar('ATxNr')){ $bNeu=false;
  $sATxBt=str_replace("'",'´',txtVar('ATxBt')); $sATxMt=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('ATxMt'))));
  if($sATxKz=txtVar('ATxKz')){
   if(!empty($sATxBt)&&!empty($sATxMt)){ //eintragen
    if($nTxNr>0){ //aendern
     if(!KAL_SQL){ //Text
      $sTxNr=sprintf('%04d;',$nTxNr);
      for($i=1;$i<$nTxte;$i++) if(substr($aT[$i],0,5)==$sTxNr){ //gefunden
       $sTxNr.='z;'.str_replace(';','`,',$sATxKz).';'.str_replace(';','`,',$sATxBt).';'.str_replace(';','`,',$sATxMt);
       if(rtrim($aT[$i])!=$sTxNr){$aT[$i]=$sTxNr.NL; $bNeu=true;} break;
      }
     }elseif($DbO){ //SQL
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabA.' SET kennung="'.$sATxKz.'",betreff="'.$sATxBt.'",inhalt="'.str_replace('\n ',"\r\n",$sATxMt).'" WHERE id='.(int)$nTxNr)){
       if($DbO->affected_rows) $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde geändert.</p>';
      }else $Msg.='<p class="admFehl">MySQL-Speicherfehler beim Alternativtext!</p>';
     }
    }else{ //neu
     if(!KAL_SQL){ //Text
      $aT[]=sprintf('%04d;',++$nTxMax).'z;'.str_replace(';','`,',$sATxKz).';'.str_replace(';','`,',$sATxBt).';'.str_replace(';','`,',$sATxMt).NL;
      $aTxKz[$nTxMax]=$sATxKz; $aTxBt[$nTxMax]=$sATxBt; $aTxMt[$nTxMax]=$sATxMt; $nTxNr=$nTxMax; $bNeu=true;
     }elseif($DbO){ //SQL
      if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabA.' (typ,kennung,betreff,inhalt) VALUES ("z","'.$sATxKz.'","'.$sATxBt.'","'.str_replace('\n ',"\r\n",$sATxMt).'")')){
       if($nTxMax=$DbO->insert_id){
        $aTxKz[$nTxMax]=$sATxKz; $aTxBt[$nTxMax]=$sATxBt; $aTxMt[$nTxMax]=$sATxMt; $nTxNr=$nTxMax;
        $Msg.='<p class="admErfo">Die neue alternative E-Mail-Vorlage wurde gespeichert.</p>';
       }
       else $Msg.='<p class="admFehl">MySQL-Einfügefehler beim Alternativtext!</p>';
      }else $Msg.='<p class="admFehl">MySQL-Einfügefehler bei Alternativtext!</p>';
     }
    }
    if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_AdminTexte,'w')){ //bei Text neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aT))).NL); fclose($f);
     $Msg.='<p class="admErfo">Die'.($nTxNr<0?' neuen':'').' alternative E-Mail-Vorlage'.($nTxNr>0?'-'.$nTxNr:'').' wurde gespeichert.</p>';
    }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_AdminTexte.'</i>',KAL_TxDateiRechte).'</p>';
   }else $Msg='<p class="admFehl">Bitte Betreff und Text zum'.($nTxNr<0?' neuen':'').' alternativen Kontakt'.($nTxNr>0?'-'.$nTxNr:'').' angeben!</p>';
  }elseif(empty($sATxBt)&&empty($sATxMt)&&$nTxNr>0){ //loeschen
   if(!KAL_SQL){ //Text
    $sTxNr=sprintf('%04d;',$nTxNr);
    for($i=1;$i<$nTxte;$i++) if(substr($aT[$i],0,5)==$sTxNr){ //gefunden
     $aT[$i]='';
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_AdminTexte,'w')){ //neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aT))).NL); fclose($f);
      $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde gelöscht.</p>';
      unset($aTxKz[$nTxNr]); $nTxNr=0;
     }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_AdminTexte.'</i>',KAL_TxDateiRechte).'</p>';
    }
   }elseif($DbO){ //SQL
    if($DbO->query('DELETE FROM '.KAL_SqlTabA.' WHERE typ="z" AND id='.(int)$nTxNr)){
     if($DbO->affected_rows){
      $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde gelöscht.</p>';
      unset($aTxKz[$nTxNr]); $nTxNr=0;
     }else $Msg.='<p class="admFehl">MySQL-Löschfehler beim Alternativtext!</p>';
    }else $Msg.='<p class="admFehl">MySQL-Löschfehler bei Alternativtext!</p>';
   }
  }else $Msg='<p class="admFehl">Bitte Kontaktkürzel zum'.($nTxNr<0?' neuen':'').' alternativen Kontakt'.($nTxNr>0?'-'.$nTxNr:'').' angeben!</p>';
 }
 if(!$Msg) $Msg='<p class="admMeld">Die Formulareinstellungen bleiben unverändert.</p>';
}
if($ksZusageFrist<0) $ksZusageFrist='';
for($i=2;$i<$nZusageFelder;$i++) $kal_ZusageFelder[$i]=str_replace('`,',';',$kal_ZusageFelder[$i]);

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Zusagesystem. <a href="'.ADM_Hilfe.'LiesMich.htm#4.6" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></p>';
echo $Msg.NL;

echo "\n<script type=\"text/javascript\">
 var aKz=new Array(); var aBt=new Array(); var aMt=new Array();
 function fATxNr(n){
  document.getElementById('ABtNr').innerHTML=(n>0?n:(n<0?'*':''));
  document.getElementById('AMtNr').innerHTML=(n>0?n:(n<0?'*':''));
  document.getElementById('ATxKz').value=aKz[n];
  document.getElementById('ATxBt').value=aBt[n];
  document.getElementById('ATxMt').value=aMt[n];
 }
 aKz[0]='erst Nr. wählen'; aBt[0]='erst Nr. wählen'; aMt[0]='erst Nr. wählen';";
foreach($aTxKz as $k=>$v){ echo"
 aKz[".$k."]='".$aTxKz[$k]."';
 aBt[".$k."]='".$aTxBt[$k]."';
 aMt[".$k."]='".str_replace('\n ','\n',$aTxMt[$k])."';";
}
echo "\n aKz[-1]=''; aBt[-1]='neuer Betreff'; aMt[-1]='neuer Text....';\n</script>\n";
asort($aTxKz); reset($aTxKz);
?>

<form name="farbform" action="konfZusage.php" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td colspan="4">Der Kalender kann mit diesem Modul <i>Terminzusage/Reservierung/Bestellung</i> gekoppelt sein.
Das Modul gehört nicht zum Standardumfang des Kalender-Scripts und ist gesondert zu <a href="<?php echo ADM_Hilfe?>LiesMich.htm#1.1.Zusage" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a> aktivieren und bei dauerhaftem Gebrauch gesondert kostenpflichtig zu lizenzieren.
<?php if(!$bVersionOK){?>Das Zusatzmodul setzt die <b>Version 3.4</b> des Kalender-Scripts voraus, die Sie noch nicht verwenden.
<?php }elseif($bInstalliert){?>Sie nutzen dieses Modul momentan in der <?php if(!$ksZusagen){?>eingeschränkten <b>Demo-Version</b>.<?php }elseif($ksZUser!='Demo'){?>Vollversion.<?php }else{?>Testversion.<?php }}?>
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagesystem</td>
 <td colspan="3">
  <input class="admRadio" type="radio" name="ZusageSystem" value="1"<?php if($ksZusageSystem) echo' checked="checked"'?> /> aktiviert &nbsp; &nbsp;
  <input class="admRadio" type="radio" name="ZusageSystem" value="0"<?php if(!$ksZusageSystem) echo' checked="checked"'?> /> ausgeschaltet &nbsp; (<?php echo($bInstalliert?'aber':'<i>nicht</i>')?> installiert)
  <div><input class="admCheck" type="checkbox" name="ZusageNeu" value="1"<?php if($ksZusageNeu) echo' checked="checked"'?> /> beim Aktivschalten alle alten Zusagedaten leeren/löschen?</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vollversion</td>
 <td colspan="3">
  Benutzername <input style="width:10em" type="text" name="ZUser" value="<?php echo $ksZUser?>" /> &nbsp;
  Lizenzcode <input style="width:10em" type="text" name="ZCode" value="<?php echo $ksZCode?>" /> &nbsp;
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Für die Besucher das Kalenders ist das Zusagesystem über einen Klickschalter innerhalb der Terminliste oder einen Klickschalter innerhalb der Detailanzeige zum Termin erreichbar.
Der <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Klickschalter kann wahlweise zu jedem Termin als zusätzliche Spalte in der Terminliste bzw. als zusätzliche Zeile in den Termindetails angeboten werden.
Er kann aber auch über ein zusätzliches Datenfeld innerhalb der Terminstruktur erzeugt werden,
welches beim jeweiligen Termin auf <i>ja</i> oder <i>nein</i> gesetzt wird um nur für bestimmte Termine die Zusagefunktion zu ermöglichen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schalterbeschriftung</td>
 <td colspan="3"><img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">
  <input style="width:10em" type="text" name="TxZusageIcon" value="<?php echo $ksTxZusageIcon?>" />
  <span class="admMini">Empfehlung: <i>Zusage</i> oder <i>Reservierung</i> oder <i>Bestellung</i></span><br>
  <img src="<?php echo $sHttp?>grafik/iconZugesagt.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZugesagtIcon?>">
  <input style="width:10em" type="text" name="TxZugesagtIcon" value="<?php echo $ksTxZugesagtIcon?>" />
  <span class="admMini">Empfehlung: <i>schon zugesagt</i> oder <i>schon zugesagt, jetzt absagen</i></span>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="3">Terminliste</td>
 <td colspan="3">zusätzliche Zusagenstatusspalte (<img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusRot?>"><img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGlb?>"><img src="<?php echo $sHttp?>grafik/punktGrn.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGrn?>">-Auslastungsgrad) vor Spalte <select name="ListenZusagS" size="1"><option value="-1">--</option><?php for($i=1;$i<$nTFelder;$i++) echo '<option value="'.$i.'"'.($ksListenZusagS==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLZusagS" value="1"<?php if($ksGastLZusagS) echo ' checked="checked"'?> />&nbsp;auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="TxListenZusagSTitel" value="<?php echo $ksTxListenZusagSTitel?>" style="width:8em;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>
<tr class="admTabl">
 <td colspan="3">zusätzliche <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Zusagespalte vor Spalte <select name="ListenZusage" size="1"><option value="-1">--</option><?php for($i=1;$i<$nTFelder;$i++) echo '<option value="'.$i.'"'.($ksListenZusage==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLZusage" value="1"<?php if($ksGastLZusage) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="TxListenZusageTitel" value="<?php echo $ksTxListenZusageTitel?>" style="width:8em;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div></td>
</tr>
<tr class="admTabl">
 <td colspan="3">zusätzl. Zusagensummenspalte vor Spalte <select name="ListenZusagZ" size="1"><option value="-1">--</option><?php for($i=1;$i<$nTFelder;$i++) echo '<option value="'.$i.'"'.($ksListenZusagZ==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> einblenden,
 <input type="checkbox" class="admCheck" name="GastLZusagZ" value="1"<?php if($ksGastLZusagZ) echo ' checked="checked"'?> /> auch für unangemeldete Gäste
 <div>Spaltentitel <input type="text" name="TxListenZusagZTitel" value="<?php echo $ksTxListenZusagZTitel?>" style="width:8em;" /> <span class="admMini">Empfehlung: <i>leer lassen</i></span></div>
 <div>Spalteninhalt <input type="text" name="TxListenZusagZMuster" value="<?php echo $ksTxListenZusagZMuster?>" style="width:7em;" /> <span class="admMini">Muster: <i>#Z/#K</i> &nbsp; (#Z: Zusagen bisher, #K: Kapazität, #R restliche freie Plätze)</span></div>
 <div>Darstellung fehlender Kapazitätsangaben <input type="text" name="ListenZusagZLeer" value="<?php echo $ksListenZusagZLeer?>" style="width:2em;" /> <span class="admMini">Empfehlung: - (oder leer lassen)</span></div>
 <div>Darstellung fehlender Restplatzangaben &nbsp; <input type="text" name="ListenZusagRLeer" value="<?php echo $ksListenZusagRLeer?>" style="width:2em;" /> <span class="admMini">Empfehlung: - (oder leer lassen)</span></div>
 <div style="margin-top:5px;">zusätzl. Zusagensummenspalte vor Spalte <select name="ListenZusagSp" size="1"><option value="-1">--</option><?php for($i=1;$i<$nTFelder;$i++) echo '<option value="'.$i.'"'.($amListenZusagSp==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> in der Admin-Terminliste einblenden</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="3">Detailanzeige</td>
 <td colspan="3">zusätzliche <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Zusagezeile vor Zeile <select name="DetailZusage" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nTFelder;$i++) echo '<option value="'.$i.'"'.($ksDetailZusage==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxZusageZeile" value="<?php echo $ksTxZusageZeile?>" size="15" style="width:8em;" /> einblenden,
 <input type="checkbox" class="admCheck" name="GastDZusage" value="1"<?php if($ksGastDZusage) echo ' checked="checked"'?> />&nbsp;auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td colspan="3"><input type="checkbox" class="admCheck" name="DetailZusagS" value="1"<?php if($ksDetailZusagS) echo ' checked="checked"'?> /> Zusagenstatus (<img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusRot?>"><img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGlb?>"><img src="<?php echo $sHttp?>grafik/punktGrn.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGrn?>">-Auslastungsgrad) am Zeilenanfang anzeigen</td>
</tr>
<tr class="admTabl">
 <td colspan="3">Zusagensummen als <input type="text" name="TxDetailZusagZMuster" value="<?php echo $ksTxDetailZusagZMuster?>" style="width:16em" /> in der Zusagezeile darstellen
 <div class="admMini">Muster: <i>#Z Zusagen, #R Plätze frei</i> &nbsp; (#Z: Zusagen bisher, #K: Kapazität, #R restliche freie Plätze)</div>
 <div>Darstellung fehlender Kapazitätsangaben <input type="text" name="DetailZusagKLeer" value="<?php echo $ksDetailZusagKLeer?>" style="width:5em;" /> <span class="admMini">Empfehlung: leer lassen oder <i>unbegrenzt</i> oder <i>??</i></span></div>
 <div>Darstellung fehlender Restplatzangaben &nbsp; <input type="text" name="DetailZusagRLeer" value="<?php echo $ksDetailZusagRLeer?>" style="width:5em;" /> <span class="admMini">Empfehlung: leer lassen oder <i>einige</i></span></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="3">Monatsblatt</td>
 <td colspan="3">
 <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="Zusagen">
 <input type="checkbox" class="admCheck" name="MonatsZusage" value="1"<?php if($ksMonatsZusage) echo ' checked="checked"'?> /> <span style="width:202px;display:inline-block;">Termin zusagen einblenden</span>
 <input type="checkbox" class="admCheck" name="GastMoZusage" value="1"<?php if($ksGastMoZusage) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="Zusagen zeigen">
 <input type="checkbox" class="admCheck" name="MonatZeigeZusage" value="1"<?php if($ksMonatZeigeZusage) echo ' checked="checked"'?> /> <span style="width:202px;display:inline-block;">Zusagen zeigen einblenden</span>
 <input type="checkbox" class="admCheck" name="GastMZeigeZusage" value="1"<?php if($ksGastMZeigeZusage) echo ' checked="checked"'?> /> auch für Gäste<br>
 </td>
</tr>
<tr class="admTabl">
 <td colspan="3">
 <input type="checkbox" class="admCheck" name="MonatsZusagS" value="1"<?php if($ksMonatsZusagS) echo ' checked="checked"'?> /> <span style="width:222px;display:inline-block;"><img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="top" title="<?php echo KAL_TxZusageStatusRot?>"><img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="top" title="<?php echo KAL_TxZusageStatusGlb?>"><img src="<?php echo $sHttp?>grafik/punktGrn.gif" width="12" height="12" border="0" align="top" title="<?php echo KAL_TxZusageStatusGrn?>">-Auslastungsgrad anzeigen</span>
 <input type="checkbox" class="admCheck" name="GastMoZusagS" value="1"<?php if($ksGastMoZusagS) echo ' checked="checked"'?> /> auch für Gäste<br>
 <input type="checkbox" class="admCheck" name="MonZusageHG" value="1"<?php if($ksMonZusageHG) echo ' checked="checked"'?> /> dabei anstatt der Symbole <img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="bottom" title="<?php echo KAL_TxZusageStatusRot?>"><img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="bottom" title="<?php echo KAL_TxZusageStatusGlb?>"><img src="<?php echo $sHttp?>grafik/punktGrn.gif" width="12" height="12" border="0" align="bottom" title="<?php echo KAL_TxZusageStatusGrn?>"> die Hintergrundgrafiken <img src="<?php echo $sHttp?>grafik/blockRot.jpg" width="32" height="32" border="0" align="middle" title="blockRot.jpg">&nbsp;<img src="<?php echo $sHttp?>grafik/blockGlb.jpg" width="32" height="32" border="0" align="middle" title="blockGlb.jpg">&nbsp;<img src="<?php echo $sHttp?>grafik/blockGrn.jpg" width="32" height="32" border="0" align="middle" title="blockGrn.jpg"> verwenden<br>
 Hintergrund für die <input type="radio" class="admRadio" name="MonZusagHGBl" value="Dat"<?php if($ksMonZusagHGBl=='Dat') echo ' checked="checked"'?> /> ganze Tageszelle  &nbsp; oder für den <input type="radio" class="admRadio" name="MonZusagHGBl" value="Det"<?php if($ksMonZusagHGBl=='Det') echo ' checked="checked"'?> /> Detailblock
 <div class="admMini">Hinweis: Die drei Hintergrundfragiken <i>blockRot.jpg</i>, <i>blockGlb.jpg</i> und <i>blockGrn.jpg</i> können mit jedem Grafikprogramm selbst gestaltet oder einfach ausgetauscht werden.</div></td>
</tr>
<tr class="admTabl">
 <td colspan="3">
 <input type="checkbox" class="admCheck" name="MonatsZusagZ" value="1"<?php if($ksMonatsZusagZ) echo ' checked="checked"'?> /> Zusagensummenzeile <span class="admMini">(mit Kapazität, freien Plätze usw.)</span> &nbsp;
 <input type="checkbox" class="admCheck" name="GastMoZusagZ" value="1"<?php if($ksGastMoZusagZ) echo ' checked="checked"'?> /> auch für Gäste<br>
 <input type="text" name="MonZusagZMuster" value="<?php echo $ksMonZusagZMuster?>" size="25" style="width:160px;margin-top:3px;" /> <span class="admMini">(#Z: Zusagen bisher, #K: Kapazität, #R restliche freie Plätze)</span><br>
 <input type="checkbox" class="admCheck" name="MonZusagZZeigeLeer" value="1"<?php if($ksMonZusagZZeigeLeer) echo ' checked="checked"'?> /> falls keine Kapazität zum Termin eingetragen leere Summenzeile trotzdem darstellen<br>
 <input type="checkbox" class="admCheck" name="x" value="1"<?php if($ksMonZusagZErsatz) echo ' checked="checked"'?> /> falls keine Kapazität zum Termin eingetragen Summenzeile
 als <input type="text" name="MonZusagZErsatz" value="<?php echo $ksMonZusagZErsatz?>" size="12" style="width:80px" /> darstellen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagefeld</td>
 <td colspan="3">ein Zusagefeld in der <a href="konfTermine.php">Terminstruktur</a> ist momentan <span style="color:#551111"><?php echo (in_array('#',$kal_FeldType)?'':'nicht ')?>aktiv</span></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Das Zusageformular wird normalerweise im selben Fenster wie die Terminübersicht bzw. Detaildarstellung präsentiert.
Abweichend davon kann das Formulare in einem sich öffnenden Popup-Fenster dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Formulardarstellung</td>
 <td colspan="3" style="padding-top:5px;"><input type="radio" class="admRadio" name="ZusagePopup" value=""<?php if(!$ksZusagePopup) echo ' checked="checked"'?> /> im Hauptfenster &nbsp; &nbsp; <input type="radio" class="admRadio" name="ZusagePopup" value="1"<?php if($ksZusagePopup) echo ' checked="checked"'?>/> als Popup-Fenster &nbsp; &nbsp; (<span class="admMini">Empfehlung: Hauptfenster</span>)
 <div><input type="text" name="PopupBreit" value="<?php echo $ksPopupBreit?>" size="4" style="width:36px;" /> Pixel Popup-Fensterbreite &nbsp; &nbsp; <input type="text" name="PopupHoch" value="<?php echo $ksPopupHoch?>" size="4" style="width:36px;" /> Pixel Popup-Fensterhöhe &nbsp; <span class="admMini">(gilt für alle Popup-Fenster)</span> <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.4.Popup" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></div></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Zur Einhaltung einschlägiger Datenschutzbestimmungen kann es sinnvoll ein, unter dem Zusagen-Eingabeformuar gesonderte Einwilligungszeilen zum Datenschutz einzublenden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenschutz-<br />bestimmungen</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageDSE1" value="1"<?php if($ksZusageDSE1) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenschutzerklärung einblenden<br /><input class="admCheck" type="checkbox" name="ZusageDSE2" value="1"<?php if($ksZusageDSE2) echo' checked="checked"'?> /> Zeile mit Kontrollkästchen zur Datenverarbeitung und -speicherung einblenden<div class="admMini">Hinweis: Der konkrete Wortlaut dieser beiden Zeilen kann im Menüpunkt <a href="konfAllgemein.php#DSE">Allgemeines</a> eingestellt werden.</div></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Zur Absicherung gegen Missbrauch durch Automaten/Roboter ist in allen Formularen ein Captcha vorgesehen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Captcha</td>
 <td colspan="3"><div><input class="admCheck" type="checkbox" name="Captcha" value="1"<?php if($ksCaptcha) echo' checked="checked"'?> /> verwenden,
 bevorzugter Captchatyp: <select name="CaptchaTyp" size="1"><option value="G<?php if($ksCaptchaTyp=='G') echo '" selected="selected';?>">grafisches Captcha</option><option value="N<?php if($ksCaptchaTyp=='N') echo '" selected="selected';?>">mathematisches Captcha</option><option value="T<?php if($ksCaptchaTyp=='T') echo '" selected="selected';?>">textliches Captcha</option></select></div>
 <div style="margin-top:5px;margin-bottom:5px;">Alternativen anbieten:
 <input class="admCheck" type="checkbox" name="CaptchaGrafisch" value="1"<?php if($ksCaptchaGrafisch) echo' checked="checked"'?> /> grafisches Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaNumerisch" value="1"<?php if($ksCaptchaNumerisch) echo' checked="checked"'?> /> mathemat. Captcha &nbsp;
 <input class="admCheck" type="checkbox" name="CaptchaTextlich" value="1"<?php if($ksCaptchaTextlich) echo' checked="checked"'?> /> textliches Captcha</div>
 Grafikmuster <span style="color:<?php echo $ksCaptchaTxFarb?>;background-color:<?php echo $ksCaptchaHgFarb?>;padding:2px;border-color:#223344;border-style:solid;border-width:1px;"><b>X1234</b></span> &nbsp; &nbsp;
 Textfarbe <input type="text" name="CaptchaTxFarb" value="<?php echo $ksCaptchaTxFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($ksCaptchaTxFarb,1)?>&fld=CaptchaTxFarb" target="color" onClick="javascript:ColWin()"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a> &nbsp; &nbsp;
 Hintergrundfarbe <input type="text" name="CaptchaHgFarb" value="<?php echo $ksCaptchaHgFarb?>" style="width:70px" />
 <a href="colors.php?col=<?php echo substr($ksCaptchaHgFarb,1)?>&fld=CaptchaHgFarb" target="color" onClick="javascript:ColWin()"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten"></a>
 <div><input class="admCheck" type="checkbox" name="AendernCaptcha" value="1"<?php if($ksAendernCaptcha) echo' checked="checked"'?> /> Captcha auch verwenden im Änderungsformular für angemeldete Besucher</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Das Zusagesystem/Reservierungssystem/Bestellsystem kann für den zusagenden Besucher
als Direktbuchung mit sofortigem Eintrag in das Zusagesystem
<i>oder</i> als Buchungsvormerkung mit Versand eines Freischaltlinks per E-Mail-Nachricht an den zusagenden Besucher zwecks Zusage-Bestätigung
<i>oder</i> als Buchungsvormerkung mit Freischaltung durch Webmaster/Autoren funktionieren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusageart</td>
 <td colspan="3">
  <input class="admRadio" type="radio" name="DirektZusage" value="1"<?php if($ksDirektZusage==1) echo' checked="checked"'?> /> Direktzusage mit sofortiger Buchung<br>
  <input class="admRadio" type="radio" name="DirektZusage" value="0"<?php if($ksDirektZusage==0) echo' checked="checked"'?> /> Vormerkung mit Freischaltung durch den Besteller per E-Mail oder den Webmaster/Autoren<br>
  <input class="admRadio" type="radio" name="DirektZusage" value="2"<?php if($ksDirektZusage==2) echo' checked="checked"'?> /> Vormerkung mit Bestätigung durch den Besteller per E-Mail plus durch den Webmaster/Autoren<br>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">1-Klick-Zusagen<a href="<?php echo ADM_Hilfe?>LiesMich.htm#4.6.EinKlick" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a><div class="admMini"><br />Empfehlung:<br /> nur sehr selten<br /> zu verwenden</div></td>
 <td colspan="3"><div><input class="admCheck" type="checkbox" name="EinKlickLZusage" value="1"<?php if($ksEinKlickLZusage) echo' checked="checked"'?> /> 1-Klick-Zusagen in der Terminliste für angemeldete Benutzer verwenden</div>
  <div><input class="admCheck" type="checkbox" name="EinKlickDZusage" value="1"<?php if($ksEinKlickDZusage) echo' checked="checked"'?> /> 1-Klick-Zusagen in den Termindetails für angemeldete Benutzer verwenden</div>
  <div><input class="admCheck" type="checkbox" name="LoeschKlickZusage" value="1"<?php if($ksLoeschKlickZusage) echo' checked="checked"'?> /> Zusagen dort auch mit einem Klick widerrufen</div>
  <div><input class="admCheck" type="checkbox" name="Pruefe1KlickKapaz" value="1"<?php if($ksPruefe1KlickKapaz) echo' checked="checked"'?> /> Kapazität auch bei 1-Klick-Zusagen prüfen</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagefrist</td>
 <td colspan="3">Zusagen nur zulassen bis spätestens <input type="text" name="ZusageFrist" value="<?php echo $ksZusageFrist?>" size="4" style="width:36px;" /> Tage vor dem Termin<br>
  <span class="admMini">Hinweis: leer lassen, falls keinerlei Befristung</span><br>
  Zusagefrist bezieht sich auf <input class="admRadio" type="radio" name="ZusageBisEnde" value="0"<?php if(!$ksZusageBisEnde) echo' checked="checked"'?> /> Terminanfang oder <input class="admRadio" type="radio" name="ZusageBisEnde" value="1"<?php if($ksZusageBisEnde) echo' checked="checked"'?> /> Terminende
  <div style="margin-top:6px"><u>Hinweis</u>: Falls Ihre <a href="konfTermine.php">Terminstruktur</a> ein Feld mit dem Feldnamen <i>ZUSAGE_BIS</i> (exakt diese Schreibweise) vom Feldtyp <i>Eintragszeit</i> enthält (momentan <?php echo (($p=array_search('ZUSAGE_BIS',$kal_FeldName))&&($kal_FeldType[$p]=='@')?'':'<span style="color:#551111">nicht</span> ')?> der Fall) und dieses Feld beim jeweiligen Termin ausgefüllt ist, wird statt obiger fester Zusagefrist in Tagen der zum Termin eingetragene Zeitpunkt als Anmeldeschluß herangezogen.</div>
  Feld <i>ZUSAGE_BIS</i> anzeigen/darstellen als <input type="text" name="ZusageNameFrist" value="<?php echo $ksZusageNameFrist?>" size="4" style="width:9em;" />
 </td>
</tr>

<tr>
 <td colspan="4" style="background-color:#ffffff;"><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Eintragen einer Zusage sollen für Besucher folgende Meldungen erscheinen und folgende Aktivitäten ausgelöst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div>Aufforderung</div><div style="margin-top:20px;">Eintragsmeldung</div><div style="margin-top:20px;">Warteliste</div><div style="margin-top:20px;">Ablehnung</div><div style="margin-top:20px;">ausgebucht</div><div style="margin-top:14px;">Doppelbuchung<br>prüfen <input class="admCheck" type="checkbox" name="KeineDoppelBuchung" value="1<?php if($ksKeineDoppelBuchung) echo '" checked="checked';?>" /></div><div style="margin-top:9px;">vom Nutzer<br>selbst widerrufen</div></td>
 <td colspan="3">
  <input style="width:99%" type="text" name="TxZusageMeld" value="<?php echo $ksTxZusageMeld?>" /><div class="admMini">Muster: <i>Melden Sie sich jetzt für diese Veranstaltung an.</i></div>
  <input style="width:99%" type="text" name="TxZusageEintr" value="<?php echo $ksTxZusageEintr?>" /><div class="admMini">Muster: <i>Ihre Zusage wurde eingetragen.</i> &nbsp; oder &nbsp; <i>Ihre Zusage wurde vorgemerkt.</i></div>
  <input style="width:99%" type="text" name="TxZusageVormerk" value="<?php echo $ksTxZusageVormerk?>" /><div class="admMini">Muster: <i>Die Zusage wurde auf die Warteliste gesetzt.</i></div>
  <input style="width:99%" type="text" name="TxZusageSperre" value="<?php echo $ksTxZusageSperre?>" /><div class="admMini">Muster: <i>Keine Anmeldung mehr möglich!</i></div>
  <input style="width:99%" type="text" name="TxZusageKapazEnde" value="<?php echo $ksTxZusageKapazEnde?>" /><div class="admMini">Muster: <i>Die Veranstaltung ist bereits ausgebucht.</i></div>
  <input style="width:99%" type="text" name="TxKeineDoppelBuchung" value="<?php echo $ksTxKeineDoppelBuchung?>" /><div class="admMini" style="margin-bottom:5px">Muster: <i>Unter dieser E-Mail-Adresse wurde bereits früher zugesagt.</i></div>
  <input style="width:99%" type="text" name="TxZusageGeloescht" value="<?php echo $ksTxZusageGeloescht?>" /><div class="admMini">Muster: <i>Ihre Zusage wurde widerrufen.</i></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">1-Klick-Meldungen</td>
 <td colspan="3">
  <input style="width:99%" type="text" name="TxZEinKlickEFrage" value="<?php echo $ksTxZEinKlickEFrage?>" /><div class="admMini">Muster: <i>Zum Termin #T jetzt zusagen?</i></div>
  <input style="width:99%" type="text" name="TxZEinKlickLFrage" value="<?php echo $ksTxZEinKlickLFrage?>" /><div class="admMini">Muster: <i>Die Zusage #Z jetzt löschen?</i></div>
  <input style="width:99%" type="text" name="TxZEinKlickLoesch" value="<?php echo $ksTxZEinKlickLoesch?>" /><div class="admMini">Muster: <i>Die Zusage #Z wurde gelöscht.</i></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusage-E-Mail</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageEintragMail" value="1"<?php if($ksZusageEintragMail) echo' checked="checked"'?> /> E-Mail an den Zusagenden beim Eintrag versenden &nbsp; (<span class="admMini">Empfehlung: <i>versenden</i></span>)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagebetreff<div style="margin-top:20px;">Zusagetext</div></td>
 <td colspan="3"><input style="width:100%" type="text" name="TxZusageEintrBtr" value="<?php echo $ksTxZusageEintrBtr?>" /><div class="admMini">Muster: <i>Re: ihre Terminzusage bei #A</i></div>
 <textarea name="TxZusageEintrMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageEintrMTx)?></textarea></div>
 <div class="admMini">Muster für Zusage-Direkteintrag: <br><i>Sie haben eine Zusage zu einem Termin unter #A vorgenommen. <br>Diese Zusage wurde von soeben verbucht.<br>Ihre Zusagedaten lauteten: #Z <br>Sie betreffen den Termin: #D</i></div><br>
 <div class="admMini">Muster für E-Mail-Freischaltung: <br><i>Hallo {Anrede} {Name}, <br>Sie haben soeben eine Zusage zu einem Termin unter #A vorgenommen. <br>Diese Zusage müssen Sie über den folgenden Link bestätigen, ehe sie wirksam wird. <br>#L <br>Ihre Zusagedaten lauteten: #Z <br>Sie betreffen den Termin: #D</i></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Bei einer eventuellen Selbstfreischaltung / einem eventuellen Selbstwiderruf der Zusage durch den Zusagenden sollen folgende Meldungen erscheinen und folgende Aktivitäten ausgelöst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1"><div>Freischalt-<br>aufforderung</div><div style="margin-top:90px;">Freischaltmeldung</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageBestaetigen" value="<?php echo $ksTxZusageBestaetigen?>" /><div class="admMini">Muster: <i>Terminzusage jetzt bestätigen?</i></div>
  <textarea name="TxZusageBestaetTxt" rows="3" cols="80" style="height:4em"><?php echo str_replace('\n ',"\n",$ksTxZusageBestaetTxt)?></textarea>
  <div class="admMini">Muster: <i>die Zusage vom #Z<br>für den Termin am #T<br>zur Veranstaltung #V freischalten?</i></div>
  <input style="width:100%" type="text" name="TxZusageBestaetigt" value="<?php echo $ksTxZusageBestaetigt?>" /><div class="admMini">Muster: <i>Ihre Terminzusage wurde bestätigt.</i> oder <i>Ihre Terminzusage wurde aktiviert und der Webmaster informiert.</i></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1"><div>Widerrufs-<br>aufforderung</div><div style="margin-top:90px;">Widerrufsmeldung</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageWiderruf" value="<?php echo $ksTxZusageWiderruf?>" /><div class="admMini">Muster: <i>Terminzusage jetzt widerrufen?</i></div>
  <textarea name="TxZusageWdrrufTxt" rows="3" cols="80" style="height:4em"><?php echo str_replace('\n ',"\n",$ksTxZusageWdrrufTxt)?></textarea>
  <div class="admMini">Muster: <i>die früheren Zusagen von #N<br> zum Termin am #T<br> zur Veranstaltung #V widerrufen?</i></div>
  <input style="width:100%" type="text" name="TxZusageIstWdrrufen" value="<?php echo $ksTxZusageIstWdrrufen?>" /><div class="admMini">Muster: <i>Ihre frühere Terminzusage wurde widerrufen.</i> oder <i>Ihre fühere Terminzusage wurde widerrufen und der Webmaster informiert.</i></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Freischaltfenster</td>
 <td colspan="3">Die Selbstfreischaltungsseite soll auf folgender HTML-Schablone basieren:<br />
 <select name="FreischaltWin" size="1" ><option value=Standard"<?php if($ksFreischaltWin=="Standard") echo '" selected="selected';?>">Standardschablone (kalSeite.htm)</option><option value="Popup<?php if($ksFreischaltWin=="Popup") echo '" selected="selected';?>">Popupschablone (kalPopup.htm)</option><option value="Freischalt<?php if($ksFreischaltWin=="Freischalt") echo '" selected="selected';?>">Freischaltungsschablone (kalFreischalt.htm)</option></select></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Ändern einer Zusage im Benutzerzentrum soll folgende Meldung erscheinen und folgende Aktivität ausgelöst werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungserlaubnis</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageAendEigene" value="1"<?php if($ksZusageAendEigene) echo' checked="checked"'?> /> eigene Zusagen können im Benutzerzentrum geändert werden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1"><div>Änderungs-<br>aufforderung</div><div style="margin-top:8px;">Bestätigung</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxNzZusageAenden" value="<?php echo $ksTxNzZusageAenden?>" /><div class="admMini">Muster: <i>Ändern Sie jetzt die Zusagedetails.</i></div>
  <input style="width:100%" type="text" name="TxNzZusageAendOk" value="<?php echo $ksTxNzZusageAendOk?>" /><div class="admMini">Muster: <i>Die Zusage wurde geändert und der Webmaster informiert.</i> &nbsp; oder<br><i>Sie müssen diese Änderungen über die erhaltene E-Mail freischalten.</i></div>
 </td>
</tr><tr class="admTabl">
 <td class="admSpa1">Ändern-E-Mail</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageAendernMail" value="1"<?php if($ksZusageAendernMail) echo' checked="checked"'?> /> E-Mail an den Zusagenden beim Ändern versenden &nbsp; (<span class="admMini">Empfehlung: <i>nicht versenden</i></span>)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagebetreff<div style="margin-top:20px;">Zusagetext</div></td>
 <td colspan="3"><input style="width:100%" type="text" name="TxZusageAendBtr" value="<?php echo $ksTxZusageAendBtr?>" /><div class="admMini">Muster: <i>Re: Änderung ihrer Terminzusage bei #A</i></div>
 <textarea name="TxZusageAendMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageAendMTx)?></textarea></div>
 <div class="admMini">Muster für Zusage-Direkteintrag: <br><i>Sie haben Ihre Zusage zu einem Termin unter #A geändert.<br>Ihre Zusagedaten lauteten nun: #Z <br>Sie betreffen den Termin: #D</i></div><br>
 <div class="admMini">Muster für E-Mail-Freischaltung: <br><i>Hallo {Anrede} {Name}, <br>Sie haben soeben Ihre Zusage zu einem Termin unter #A geändert. <br>Diese Änderung müssen Sie über den folgenden Link bestätigen, ehe sie wirksam wird. <br>#L <br>Ihre geänderten Zusagedaten lauteten: #Z <br>Sie betreffen den Termin: #D</i></div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1"><div>Löscherlaubnis</div><div style="margin-top:12px;">Löschaufforderung</div><div style="margin-top:20px;">Bestätigungen</div></td>
 <td colspan="3">
  <div style="margin-bottom:6px"><input class="admCheck" type="checkbox" name="ZusageLschEigene" value="1"<?php if($ksZusageLschEigene) echo' checked="checked"'?> /> eigene Zusagen können im Benutzerzentrum gelöscht werden</div>
  <input style="width:100%" type="text" name="TxNzLoeschen" value="<?php echo $ksTxNzLoeschen?>" /><div class="admMini">Muster: <i>Wollen Sie die #N markierten Zusagen wirklich löschen?</i></div>
  <input style="width:100%" type="text" name="TxNzGeloescht" value="<?php echo $ksTxNzGeloescht?>" /><div class="admMini">Muster: <i>#N Zusagen wurden gelöscht.</i></div>
  <input style="width:100%" type="text" name="TxNzUnveraendert" value="<?php echo $ksTxNzUnveraendert?>" /><div class="admMini">Muster: <i>Ihre Zusagen bleiben unverändert.</i></div>
  <input style="width:100%" type="text" name="TxNzZusageStat" value="<?php echo $ksTxNzZusageStat?>" /><div class="admMini">Muster: <i>Der Status der Zusage #N wurde geändert.</i></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Eintragen und/oder Freigeben einer Zusage kann eine Nachricht an den Terminautor und/oder den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Infozeitpunkt<div style="margin-top:50px;">alternative Adresse</div></td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="ZusageNeuInfoAut" value="1"<?php if($ksZusageNeuInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) beim Eintragen einer Zusage</div>
  <div><input class="admCheck" type="checkbox" name="ZusageBstInfoAut" value="1"<?php if($ksZusageBstInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) bei Freischaltung einer Zusage</div>
  <div><input class="admCheck" type="checkbox" name="ZusageNeuInfoAdm" value="1"<?php if($ksZusageNeuInfoAdm) echo' checked="checked"'?> /> an den Webmaster beim Eintragen einer Zusage</div>
  <div><input class="admCheck" type="checkbox" name="ZusageBstInfoAdm" value="1"<?php if($ksZusageBstInfoAdm) echo' checked="checked"'?> /> an den Webmaster bei Selbstfreischaltung einer Zusage durch den Zusagenden</div>
  <input type="text" name="EmpfZusage" value="<?php echo $ksEmpfZusage?>" style="width:220px" /> <span class="admMini">leer lassen oder E-Mail-Adresse des Zusagenverwalters</span>
  <div class="admMini">(Wird bei Zusagenaktionen anstatt <i><?php echo KAL_Empfaenger?></i> für die E-Mails an den Webmaster verwendet.)</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Rückmeldebetreff<div style="margin-top:20px;">Rückmelde-<br>information<br>an Webmaster<br>und/oder<br>Terminautor</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageInfoBtr" value="<?php echo $ksTxZusageInfoBtr?>" /><div class="admMini">Muster: <i>neue Terminzusage bei #A</i></div>
  <textarea name="TxZusageInfoMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageInfoMTx)?></textarea></div><div class="admMini">Muster: <i>Unter #A wurde soeben eine neue Terminzusage eingetragen. <br>Die Zusage lautet: #Z <br>Sie betrifft den Termin: #D<br>Damit sind #N von #K Plätzen belegt.</i></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Ändern einer Zusage kann eine Nachricht an den Zusagenden, Terminautor und/oder den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Infozeitpunkt</td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="ZusageAendInfoAut" value="1"<?php if($ksZusageAendInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) beim Ändern im Benutzerzentrum</div>
  <div><input class="admCheck" type="checkbox" name="ZusageAendInfoAdm" value="1"<?php if($ksZusageAendInfoAdm) echo' checked="checked"'?> /> an den Webmaster beim Ändern durch den Zusagenden im Benutzerzentrum</div>
  <div><input class="admCheck" type="checkbox" name="ZusageAAendInfoZs" value="1"<?php if($ksZusageAAendInfoZs) echo' checked="checked"'?> /> an den Zusagenden beim Ändern durch den Administrator/berechtigten Autor</div>
  <div><input class="admCheck" type="checkbox" name="ZusageAAendInfoAu" value="1"<?php if($ksZusageAAendInfoAu) echo' checked="checked"'?> /> an den Terminautor (falls bekannt) beim Ändern durch Administrator/berechtigten Autor</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsbetreff<div style="margin-top:20px;">Änderungsinformation<br>an Webmaster<br>und/oder<br>Terminautor</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageAenABtr" value="<?php echo $ksTxZusageAenABtr?>" /><div class="admMini">Muster: <i>Terminzusage bei #A geändert</i></div>
  <textarea name="TxZusageAenAMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageAenAMTx)?></textarea></div><div class="admMini">Muster: <i>Unter #A wurde soeben eine Terminzusage geändert. <br>Die neue Zusage lautete: #Z <br>Sie betrifft den Termin: #D<br>Damit sind #N von #K Plätzen belegt.</i></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Beim nachträglicher Änderung an einem Termin kann eine Nachricht an die Zusagenden erfolgen. Der Inhalt der Nachricht über die Terminänderung oder Terminlöschung wird auf der Formulareite <i>Benachrichtigungen</i> unter <i>Benachrichtigungs-E-Mail</i> eingestellt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Änderungsnachricht</td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="FormAendZusageInfo" value="1"<?php if($ksFormAendZusageInfo) echo' checked="checked"'?> /> Zusagende über Terminänderung informieren</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Erreichen eines Schwellenwertes und/oder der Kapazitätsgrenze kann eine Nachricht an den Terminautor und/oder den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Infozeitpunkt</td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="ZusageMaxKapInfoAdm" value="1"<?php if($ksZusageMaxKapInfoAdm) echo' checked="checked"'?> /> an den Webmaster beim Erreichen der Kapazitätsgrenze</div>
  <div><input class="admCheck" type="checkbox" name="ZusageGrenzeInfoAdm" value="1"<?php if($ksZusageGrenzeInfoAdm) echo' checked="checked"'?> /> an den Webmaster beim Erreichen eines Schwellenwertes</div>
  <div><input class="admCheck" type="checkbox" name="ZusageMaxKapInfoAut" value="1"<?php if($ksZusageMaxKapInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) beim Erreichen der Kapazitätsgrenze</div>
  <div><input class="admCheck" type="checkbox" name="ZusageGrenzeInfoAut" value="1"<?php if($ksZusageGrenzeInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) beim Erreichen eines Schwellenwertes</div>
  <div class="admMini" style="margin-top:5px"><u>Hinweis</u>: Die Maximalkapazität ist in das Feld <i>KAPAZITAET</i> vom Typ <i>Text</i> oder <i>Ganzzahl</i> der Terminstruktur einzutragen.
  Ein zusätzlicher Schwellenwert wäre ebenfalls in dieses Feld <i>KAPAZITAET</i> in Klammern nach dem Muster <i><nobr>20(15)</nobr></i> als <i><nobr>Kapazitätsgrenze(Schwellenwert)</nobr></i> einzutragen.</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Kapazitätsbetreff<div style="margin-top:20px;">Schwellenbetreff</div><div style="margin-top:20px;">Warninformation<br>an Webmaster<br>und/oder<br>Terminautor</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageMaxKapBtr" value="<?php echo $ksTxZusageMaxKapBtr?>" /><div class="admMini">Muster: <i>Zusagen bei #A voll ausgebucht</i></div>
  <input style="width:100%" type="text" name="TxZusageGrenzeBtr" value="<?php echo $ksTxZusageGrenzeBtr?>" /><div class="admMini">Muster: <i>Zusagenschwelle bei #A erreicht</i></div>
  <textarea name="TxZusageMaxKapMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageMaxKapMTx)?></textarea></div><div class="admMini">Muster: <i>Unter #A wurde soeben eine Terminzusage verarbeitet.<br>Damit ist die Grenze #N von #K Zusagen erreicht.<br>Die Zusage lautete: #Z <br>Sie betraf den Termin: #D</i></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim Löschen einer Zusage kann eine Nachricht an den Terminautor und/oder den Webmaster erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Infozeitpunkt</td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="ZusageLschInfoAdm" value="1"<?php if($ksZusageLschInfoAdm) echo' checked="checked"'?> /> an den Webmaster beim Löschen einer Zusage durch den Zusagenden im Benutzerzentrum</div>
  <div><input class="admCheck" type="checkbox" name="ZusageLschInfoAut" value="1"<?php if($ksZusageLschInfoAut) echo' checked="checked"'?> /> an den Terminautor (sofern bekannt) beim Löschen durch den Zusagenden im Benutzerzentrum</div>
  <div><input class="admCheck" type="checkbox" name="ZusageLschNzZusag" value="1"<?php if($ksZusageLschNzZusag) echo' checked="checked"'?> /> an den Zusagenden beim Löschen einer Zusage durch den Terminbesitzer im Benutzerzentrum</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Löschbetreff<div style="margin-top:20px;">Löschinformation<br>an Webmaster<br>und/oder<br>Terminautor</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageLschBtr" value="<?php echo $ksTxZusageLschBtr?>" /><div class="admMini">Muster: <i>Terminzusage bei #A gelöscht</i></div>
  <textarea name="TxZusageLschMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageLschMTx)?></textarea></div><div class="admMini">Muster: <i>Unter #A wurde soeben eine Terminzusage widerrufen. <br>Die Zusage lautete: #Z <br>Sie betraf den Termin: #D<br>Damit sind noch #N von #K Plätzen gebucht.</i></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Beim automatischen Hochstufen einer Zusage von der Warteliste wegen Absage/Löschen einer anderen Zusage kann eine Nachricht an den Zusagenden auf der Warteliste verschickt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Wartelistenänderung</td>
 <td colspan="3">
  <div><input class="admCheck" type="checkbox" name="ZusageVmkInfoVorbei" value="1"<?php if($ksZusageVmkInfoVorbei) echo' checked="checked"'?> /> an den Zusagenden auf der Warteliste</div>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Hochstufungsbetreff<div style="margin-top:20px;">Hochstufungstext</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageVmkVorbeiBtr" value="<?php echo $ksTxZusageVmkVorbeiBtr?>" /><div class="admMini">Muster: <i>Re: Zusage bei #A hochgestuft</i></div>
  <textarea name="TxZusageVmkVorbeiMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageVmkVorbeiMTx)?></textarea></div><div class="admMini">Muster: <i>Ihre frühere Zusage bei #A wurde von der Warteliste genommen und hochgestuft. Sie sind jetzt verbindlich angemeldet. <br>Die Zusage lautet: #Z <br>Sie betrifft den Termin: #D</i></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Auch bei Freischaltung der Zusage durch den Administrator/Autor kann eine Nachricht an den Zusagenden erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Freigabe-E-Mail</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageFreigabeMail" value="1"<?php if($ksZusageFreigabeMail) echo' checked="checked"'?> /> E-Mail an den Zusagenden bei Freigabe durch den Administrator/Autoren versenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Webmaster-<br>freigabebetreff<div style="margin-top:6px;">Freigabetext</div></td>
 <td colspan="3"><input style="width:100%" type="text" name="TxZusageFreiBtr" value="<?php echo $ksTxZusageFreiBtr?>" /><div class="admMini">Muster: <i>Re: Terminzusage bei #A akzetiert</i></div>
 <textarea name="TxZusageFreiMTx" rows="8" cols="80" style="height:8em"><?php echo str_replace('\n ',"\n",$ksTxZusageFreiMTx)?></textarea></div>
 <div class="admMini">Muster für Webmaster-Freischaltung: <br><i>Sie haben eine Zusage zu einem Termin unter #A vorgenommen. <br>Diese Zusage wurde von soeben vom Webmaster akzeptiert.<br>Ihre Zusagedaten lauteten: #Z <br>Sie betreffen den Termin: #D</i></div>
 </td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Webmaster und Autoren können aus der Zusagenliste heraus unabhängig vom Eintrags- oder Freischaltzeitpunkt jederzeit eine E-Mail-Nachricht an den Zusagenden versenden. Das Kontaktformular ist mit folgenden überschreibbaren Texten vorausgefüllt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rückfragebetreff<div style="margin-top:20px;">Rückfragetext</div></td>
 <td colspan="3"><input style="width:100%" type="text" name="TxZusageKontBtr" value="<?php echo $ksTxZusageKontBtr?>" /><div class="admMini">Muster: <i>Re: Ihre Terminzusage bei #A</i></div>
 <textarea name="TxZusageKontMTx" rows="6" cols="80" style="height:6em"><?php echo str_replace('\n ',"\n",$ksTxZusageKontMTx)?></textarea></div><div class="admMini">Muster: <i>Sie haben auf unserer Webseite #A am #Z eine Terminzusage zum Termin #D gemacht....</i><br />oder weitere Feldnamen des Zusageneintragsformulars: <i>Sehr geehrte{r} {Anrede} {Name}, Sie haben zugesagt zu {Veranstaltung}...</i></div></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Für umfangreichere Kommunikation können alternative E-Mail-Vorlagetexte nebst Platzhaltern bereitgehalten werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kontaktkürzel</td>
 <td colspan="3"><select name="ATxNr" size="1" onchange="fATxNr(this.value)"><option value="0">--</option><option value="-1<?php if($nTxNr<0) echo '" selected="selected';?>">0000: *NeuText</option><?php foreach($aTxKz as $k=>$v) echo '<option value="'.$k.($nTxNr!=$k?'':'" selected="selected').'">'.sprintf('%04d: ',$k).$v.'</option>'?></select> &nbsp;
 <input style="width:10em" type="text" name="ATxKz" id="ATxKz" value="<?php echo ($nTxNr!=0?$sATxKz:'erst Nr. wählen')?>" /> </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br>Benutzerbetreff <span id="ABtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td colspan="3"><input style="width:100%" type="text" name="ATxBt" id="ATxBt" value="<?php echo ($nTxNr!=0?$sATxBt:'erst Nr. wählen')?>" /><div class="admMini">(Dieser Text wird als überschreibbarer Betreff für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br>Benutzerkontakt <span id="AMtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td colspan="3"><textarea name="ATxMt" id="ATxMt" cols="80" rows="8" style="height:9em;"><?php echo ($nTxNr!=0?str_replace('\n ',"\n",$sATxMt):'erst Nr. wählen')?></textarea>
 <div class="admMini">(Dieser Text wird als überschreibbare Standardvorlage für E-Mails an die Benutzer verwendet.)</div>
 <div class="admMini" style="margin-top:6px">Zum Löschen einer Alternativvorlage sind sowohl das Kürzel als auch der Betreff und Text zu leeren.</div></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Benutzer können aus dem Benutzerzentrum oder aus der Zusagenliste heraus die Zusagenden kontaktieren.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sammel-E-Mail</td>
 <td colspan="3"><input style="width:100%" type="text" name="TxNZusageAlle" value="<?php echo $ksTxNZusageAlle?>" /><div class="admMini">Muster: <i>Nachricht an alle Zusager zu diesem Termin</i></div></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">In allen E-Mails bezüglich Terminzusagen wird bei den Termindetails ein Link auf den Termin angegeben.
Das Ziel dieses Verweises kann das Kalenderscript <i>kalender.php</i> sein oder bei includierten Aufrufen auch das einbettende Script.<br />
Wenn das Zusagenformular in einem Popupfenster angezeigt wird,
wird als Verweisziel für die Termindetails in der E-Mail normalerweise das Kalenderscript <i>kalender.php</i> verwendet.
Sie können jedoch ein anderes Verweisziel für die die Termindetails in den E-Mails des Zusagensystems angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td colspan="3"><input type="text" name="ZusageLink" value="<?php echo $ksZusageLink?>" style="width:100%" />
 <div class="admMini">leer lassen oder Scriptname (mit absolutem Web-Pfad ohne Domainangabe oder auch als vollständigerer URL inklusive http://)</div></td>
</tr>

<tr>
 <td colspan="4" style="background-color:#ffffff;"><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"><a name="Felder">&nbsp;</a></p></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Speicherung der Zusagen</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagedatei<div class="admMini">oder</div>MySQL-Tabelle</td>
 <td colspan="3"><div>
  <input style="width:10em;<?php if(KAL_SQL) echo 'color:#8C8C8C;'?>" type="text" name="Zusage" value="<?php echo $ksZusage?>" />
  <span class="admMini">Empfehlung: <i>zusagen.txt</i> oder <i>reservierung.txt</i> oder <i>bestellungen.txt</i></span></div>
  <div><input style="width:10em;<?php if(!KAL_SQL) echo 'color:#8C8C8C;'?>" type="text" name="SqlTabZ" value="<?php echo $ksSqlTabZ?>" />
  <span class="admMini">Empfehlung: <i>kal_zusage</i> oder <i>kal_bestell</i></span></div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Das Zusageneintragsformular im Besucherbereich ist einstellbar und soll folgende Datenfelder bekommen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Datenfeldanzahl</td>
 <td colspan="3"><input type="text" name="ZusageFeldZahl" value="<?php echo ($nZusageFelder-1)?>" size="2" style="width:50px;" /> maximale Anzahl der Datenfelder im Zusageneintragsformular&nbsp; <span class="admMini">(Empfehlung: max. 15)</span></td>
</tr>
<tr class="admTabl"><td class="admSpa1"><b>Datenfeld</b></td><td class="admSpa1"><b>Bezeichnung / Pflichtfeld</b></td><td colspan="2" class="admSpa1" style="width:80%"><b>Datenherkunft</b> für eine automatische Vorbelegung</td></tr>
<tr class="admTabl"><td class="admSpa1">1. Termin-Nr</td><td><?php echo $kal_ZusageFelder[1]?></td><td colspan="2">interne Terminnummer in der Termindatei</td></tr>
<tr class="admTabl"><td>feste Felder</td><td colspan="3">Auch wenn Sie die nächsten 3 Felder anders benennen bleiben deren Funktion als <i>Datum</i> der Veranstaltung, <i>Uhrzeit</i> der Veranstaltung und <i>Veranstaltungsname</i> erhalten.</td></tr>
<?php
 $sOpt=''; $sOptD=''; $sOptZ='';
 for($i=1;$i<$nTFelder;$i++){
  $sFt=$kal_FeldType[$i]; $sFn=$kal_FeldName[$i];
  if($sFt=='d'||$sFt=='@') $sOptD.='<option value="'.$i.'">'.$sFn.'</option>';
  elseif($sFt=='z') $sOptZ.='<option value="'.$i.'">'.$sFn.'</option>';
  elseif($sFt!='b'&&$sFt!='f'&&$sFt!='x'&&$sFt!='p'&&$sFt!='v'&&$sFn!='TITLE'&&substr($sFn,0,5)!='META-'&&$sFn!='KAPAZITAET') $sOpt.='<option value="'.$i.'">'.$sFn.'</option>';
 }
?>
<tr class="admTabl">
 <td class="admSpa1">2. Datum</td>
 <td><input type="text" name="F2" value="<?php echo $kal_ZusageFelder[2]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td colspan="2"><select name="TerminDatumFeld" size="1"><option value="0">--</option><?php echo str_replace('value="'.$ksTerminDatumFeld.'"','value="'.$ksTerminDatumFeld.'" selected="selected"',$sOptD)?></select> Herkunft des Veranstaltungsdatums</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">3. Uhrzeit</td>
 <td><input type="text" name="F3" value="<?php echo $kal_ZusageFelder[3]?>" size="16" style="width:100px;" /></td>
 <td colspan="2"><select name="TerminZeitFeld" size="1"><option value="0">--</option><?php echo str_replace('value="'.$ksTerminZeitFeld.'"','value="'.$ksTerminZeitFeld.'" selected="selected"',$sOptZ)?></select> Herkunft der Veranstaltungszeit</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">4. Veranstaltung</td>
 <td valign="top"><input type="text" name="F4" value="<?php echo $kal_ZusageFelder[4]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td colspan="2"><select name="TerminVeranstFeld" size="1"><option value="0">--</option><?php echo str_replace('value="'.$ksTerminVeranstFeld.'"','value="'.$ksTerminVeranstFeld.'" selected="selected"',$sOpt)?></select> Herkunft des Veranstaltungsnamens
 <div>Länge der Bezeichnung kürzen auf <input type="text" name="ZusageVeranstLaenge" value="<?php echo $ksZusageVeranstLaenge?>" size="3" style="width:3em;" /> Zeichen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">5. Zusagezeit</td>
 <td><input type="text" name="F5" value="<?php echo $kal_ZusageFelder[5]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td colspan="2">Zeitpunkt des Eintrags der Zusage/Bestellung/Reservierung</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">6. Status</td>
 <td>unsichtbares Feld<input type="hidden" name="F6" value="aktiv" /> &nbsp;&nbsp; <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td colspan="2">die Zusage zum Termin ist gültig/aktiv/bestätigt</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">7. Benutzer</td>
 <td>unsichtbares Feld<input type="hidden" name="F7" value="Benutzer" /> &nbsp;&nbsp; <img src="<?php echo $sHttp?>grafik/haken.gif" width="11" height="11" border="0" title="Pflichtfeld"></td>
 <td colspan="2">interne Benutzernummer (sofern vorhanden)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">8. E-Mail-Adresse</td>
 <td><input type="text" name="F8" value="<?php echo $kal_ZusageFelder[8]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P8" value="1"<?php if($kal_ZusagePflicht[8]) echo' checked="checked"'?> /></td>
 <td colspan="2">Adresse des Zusagenden</td>
</tr>
<tr class="admTabl"><td class="admSpa1"><b>freie Datenfelder</b></td><td class="admSpa1" style="width:16%"><b>Bezeichnung / Pflichtfeld</b><br>z.B. Anrede, Name, ANZAHL,<br>Anschrift, Telefon usw.</td><td class="admSpa1" style="width:27%"><b>Datenherkunft</b><br>für eine automatische Vor-<br>belegung, falls angemeldet</td><td class="admSpa1" style="width:27%"><b>Feldtyp</b></td></tr>
<?php
$aNF=$kal_NutzerFelder; array_splice($aNF,1,1); $nNutzerFelder=count($aNF); $sOpt='<option value="2">'.$aNF[2].'</option>';
for($i=5;$i<$nNutzerFelder;$i++) $sOpt.='<option value="'.$i.'N">'.$aNF[$i].'</option>'; $sOpt.='<option value="">--Termindatei--</option>';
for($i=1;$i<$nTFelder;$i++){
 $sFt=$kal_FeldType[$i]; $sFn=$kal_FeldName[$i];
 if($sFt!='b'&&$sFt!='f'&&$sFt!='p'&&$sFt!='v'&&$sFn!='TITLE'&&substr($sFn,0,5)!='META-'&&$sFn!='KAPAZITAET') $sOpt.='<option value="'.$i.'T">'.$sFn.'</option>';
}
$sOFld='<option value="t">Text</option><option value="a">Auswahl</option><option value="n">Zahl</option><option value="w">Währung</option><option value="j">Ja/Nein</option>';
for($i=9;$i<$nZusageFelder;$i++){?>
<tr class="admTabl">
 <td class="admSpa1"><?php echo $i?>. Feld</td>
 <td><input type="text" name="F<?php echo $i?>" value="<?php echo $kal_ZusageFelder[$i]?>" size="16" style="width:100px;" /> &nbsp; &nbsp; &nbsp;
 <input type="checkbox" class="admCheck" name="P<?php echo $i?>" value="1"<?php if($kal_ZusagePflicht[$i]) echo' checked="checked"'?> /></td>
 <td><?php if($kal_ZusageFelder[$i]!='ANZAHL'){?><select name="ZusageQuelle<?php echo $i?>" size="1"><option value="0">--</option><option value="">--Benutzerdatei--</option><?php echo ((!$j=$kal_ZusageQuellen[$i])?$sOpt:str_replace('value="'.$j.'"','value="'.$j.'" selected="selected"',$sOpt))?></select><?php }else{?>&nbsp;<input type="hidden" name="ZusageQuelle<?php echo $i?>" value="0"><?php }?></td>
 <td><select name="ZFT<?php echo $i?>" size="1"><?php echo str_replace('value="'.$kal_ZusageFeldTyp[$i].'"','value="'.$kal_ZusageFeldTyp[$i].'" selected="selected"',$sOFld)?></select><?php if($kal_ZusageFeldTyp[$i]=='a') echo ' <a href="konfZVorgabe.php?id='.$i.'"><img src="'.$sHttp.'grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="Vorgabewerte bearbeiten" /><a>'?></td>
</tr>
<?php }?>
<tr class="admTabl"><td colspan="4" class="admSpa2">Das Eintragsformular kann ein Kontrollkästchen <i>Löschen früherer Zusagen</i> enthalten, in dem Gäste oder auch Benutzer ankreuzen können um fühere Zusagen zum Termin zu widerrufen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">frühere Zusagen<br>widerrufen</td>
 <td colspan="3"><input type="checkbox" class="admCheck" name="LoeschGastZusage" value="1"<?php if($ksLoeschGastZusage) echo' checked="checked"'?> /> Kontollkästchen für Gäste im Zusageneintragsformular einblenden<br>
 <input type="checkbox" class="admCheck" name="LoeschNutzerZusage" value="1"<?php if($ksLoeschNutzerZusage) echo' checked="checked"'?> /> Kontollkästchen für Benutzer im Zusageneintragsformular einblenden</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Beschriftung<br>des Kästchens</td>
 <td colspan="3"><input type="text" name="TxZusageLschFrueher" value="<?php echo $ksTxZusageLschFrueher?>" size="80" style="width:100%;" /><div class="admMini"><i>Empfehlung</i>: frühere Zusagen zu diesem Termin löschen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Fehlermeldung</td>
 <td colspan="3"><input type="text" name="TxZusageKeinFrueher" value="<?php echo $ksTxZusageKeinFrueher?>" size="80" style="width:100%;" /><div class="admMini"><i>Empfehlung</i>: Es liegen keine früheren Zusagen von Ihnen vor.</div></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2"><div class="admMini"><u>Hinweis</u>: Wenn Ihr Zusageformular ab dem 9. Feld ein Feld namens <i>ANZAHL</i> (exakt diese Schreibweise) enthält so wird dieses Feld als die gewünschte Anzahl der zu bestellenden/gebuchten/zugesagten Plätze aufgefasst und mit den weiteren Zusagen für diesen Termin zu einer Gesamtplatzzahl summiert.
Falls dann noch die Terminstruktur ein Datenfeld vom Typ <i>Text</i> oder <i>Ganzzahl</i> mit dem Feldnamen <i>KAPAZITAET</i> (exakt diese Schreibweise) enthält (momentan <?php echo (($p=array_search('KAPAZITAET',$kal_FeldName))&&($kal_FeldType[$p]=='n'||$kal_FeldType[$p]=='t')?'':'nicht ')?> der Fall) so wird im Zusageformular die Anzahl der noch freien restlichen Plätze berechnet und beim Ausfüllen des Zusageformulars durch die Besucher überwacht.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Darstellung des<br>Spezialfeldes<br><i>KAPAZITAET</i></td>
 <td colspan="3"> Ein etwaig in der Terminstruktur enthaltenes Feld <i>KAPAZITAET</i> vom Typ <i>Text</i> oder <i>Ganzzahl</i> soll angezeigt werden als<br>
 <input type="text" name="ZusageNameKapaz" value="<?php echo $ksZusageNameKapaz?>" size="20" style="width:14em;" /><br>
 <input type="checkbox" class="admCheck" name="ZusageKapazVersteckt" value="1"<?php if($ksZusageKapazVersteckt) echo' checked="checked"'?> /> das Feld <i>KAPAZITAET</i> soll für Besucher in den Termindetails <i>nicht</i> sichtbar sein</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenstatus<br>(Auslastungsgrad)</td>
 <td colspan="3">
   <div><img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusRot?>"> beschriftet mit <input type="text" name="TxZusageStatusRot" value="<?php echo $ksTxZusageStatusRot?>" size="20" style="width:14em;" /> unterhalb von <input type="text" name="ZusageStatusRot" value="<?php echo $ksZusageStatusRot?>" size="3" style="width:2em;" /> freien Plätzen</div>
   <div><img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGlb?>"> beschriftet mit <input type="text" name="TxZusageStatusGlb" value="<?php echo $ksTxZusageStatusGlb?>" size="20" style="width:14em;" /> unterhalb von <input type="text" name="ZusageStatusGlb" value="<?php echo $ksZusageStatusGlb?>" size="3" style="width:2em;" /> freien Plätzen</div>
   <div><img src="<?php echo $sHttp?>grafik/punktGrn.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGrn?>"> beschriftet mit <input type="text" name="TxZusageStatusGrn" value="<?php echo $ksTxZusageStatusGrn?>" size="20" style="width:14em;" /> ab <span style="background-color:#fff;border-style:solid;border-width:1px;padding-left:8px;padding-right:8px"><?php echo $ksZusageStatusGlb?></span> freien Plätzen aufwärts</div>
   <div style="margin-top:5px"><input type="checkbox" class="admCheck" name="ZusageStatusSchwelle" value="1"<?php if($ksZusageStatusSchwelle) echo' checked="checked"'?> /> statt obiger Grenzen <?php echo $ksZusageStatusRot.'/'.$ksZusageStatusGlb?> falls vorhanden den <i>Schwellenwert</i> aus dem Feld KAPAZITAET für <img src="<?php echo $sHttp?>grafik/punktGlb.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusGlb?>">&nbsp;(<?php echo $ksTxZusageStatusGlb?>) benutzen, dabei <img src="<?php echo $sHttp?>grafik/punktRot.gif" width="12" height="12" border="0" align="bottom" title="<?php echo $ksTxZusageStatusRot?>">&nbsp;(<?php echo $ksTxZusageStatusRot?>) immer fest mit 0 freien Plätzen </div>
  </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Darstellung des<br>Spezialfeldes<br><i>ANZAHL</i></td>
 <td colspan="3">Das Feld <i>ANZAHL</i> im Zusageneintragsformular soll dargestellt werden als<br>
 <input class="admRadio" type="radio" name="ZusageSelectAnzahl<?php if(!$ksZusageSelectAnzahl) echo '" checked="checked'?>" value="0" /> Eingabefeld &nbsp; <input class="admRadio" type="radio" name="ZusageSelectAnzahl<?php if($ksZusageSelectAnzahl) echo '" checked="checked'?>" value="1" /> aufklappbare Auswahlbox
 <div style="margin-top:3px">Maximalzahl pro Zusage in der aufklappbaren Auswahlbox bei <i>ANZAHL</i></div>
 <input type="text" name="ZusageMaxNamenMitKapaz" value="<?php echo $ksZusageMaxNamenMitKapaz?>" size="20" style="width:2.2em;" /> bei Terminen <i>mit</i> Kapazität &nbsp; &nbsp;
 <input type="text" name="ZusageMaxNamenOhneKapaz" value="<?php echo $ksZusageMaxNamenOhneKapaz?>" size="20" style="width:2.2em;" /> bei Terminen <i>ohne</i> Kapazität
 <div style="margin-top:3px">Das Feld <i>ANZAHL</i> soll abweichend beschriftet/angezeigt werden als</div>
 <input type="text" name="ZusageNameAnzahl" value="<?php echo $ksZusageNameAnzahl?>" size="20" style="width:14em;" />
 <div style="margin-top:3px">Neben dem Feld <i>ANZAHL</i> im Zusageneintragsformular soll folgender Text erscheinen</div>
 <input type="text" name="TxZusageKapazRest" value="<?php echo $ksTxZusageKapazRest?>" style="width:99%" /><br>
 <span class="admMini"><u>Beispiel</u>: <i>noch #R von #K Plätzen frei, #Z Zusagen bisher</i> &nbsp; oder leer lassen</span>
 <div style="margin-top:5px;"><input type="checkbox" class="admCheck" name="PruefeZusageKapaz" value="1"<?php if($ksPruefeZusageKapaz) echo' checked="checked"'?> /> beim Eintragen der <i>ANZAHL</i> soll die noch freie Rest-<i>KAPAZITAET</i> überprüft werden</div>
 <div style="margin-top:5px;"><input type="checkbox" class="admCheck" name="ZusageVormerkErlaubt" value="1"<?php if($ksZusageVormerkErlaubt) echo' checked="checked"'?> /> Überbuchungen über die Kapazität hinaus als Eintragungen in eine Vormerkliste zulassen</div>

 <div><input type="text" name="TxZusageVmkErlaubt" value="<?php echo $ksTxZusageVmkErlaubt?>" size="80" style="width:99%;" /></div>
 <div><span class="admMini"><u>Muster</u>: <i>Sie können sich auf die Warteliste setzten lassen.</i></span> &nbsp; &nbsp; <input type="checkbox" class="admCheck" name="ZusageVormerkTxtZeile" value="1"<?php if($ksZusageVormerkTxtZeile) echo' checked="checked"'?> /> als eigene Zeile</div>

 <div style="margin-top:5px;margin-bottom:5px;"><input type="checkbox" class="admCheck" name="ErlaubeZusageNull" value="1"<?php if($ksErlaubeZusageNull) echo' checked="checked"'?> /> beim Eintragen der <i>ANZAHL</i> soll es auch möglich sein, <i>0</i> Plätze einzutragen <span class="admMini">(nicht zu empfehlen)</span></div>
 Alternativer Text neben dem Feld <i>ANZAHL</i> im Formular, falls keine KAPAZITAET zum Termin eingetragen ist
 <input type="text" name="TxZusageKapazNull" value="<?php echo $ksTxZusageKapazNull?>" style="width:99%" /><br>
 <span class="admMini"><u>Beispiel</u>: <i>bisher #Z Zusagen</i> &nbsp; oder leer lassen</span>
 <div style="margin-top:5px;"><input type="radio" class="admCheck" name="ZaehleAktiveZusagen" value="1"<?php if($ksZaehleAktiveZusagen) echo' checked="checked"'?> /> nur aktive/bestätigte Zusagen zählen &nbsp;
 <input type="radio" class="admCheck" name="ZaehleAktiveZusagen" value="0"<?php if(!$ksZaehleAktiveZusagen) echo' checked="checked"'?> /> alle (auch unbestätigte, stornierte) Zusagen zählen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Behandlung des<br>Eingabefeldes<br>vom Typ <i>Währung</i><div class="admMini">(falls vorhanden)</div></td>
 <td colspan="3"><input type="checkbox" class="admCheck" name="RechneZusagePreis" value="1"<?php if($ksRechneZusagePreis) echo' checked="checked"'?> /> automatisch aus der eingegebenen Platzanzahl den Gesamtpreis berechnen
  <div class="admMini">Die Berechnung erfolgt aus dem vom Termin übernommenen Preis mal der im Zusageformular eingetragenen Platzanzahl. Jedoch nur, sofern unten die <i>Mehrplatzzusagen</i> ohne Aufsplitten in Einzelzusagen aktiv sind.</div>
  <div style="margin-top:6px">
  <input class="admRadio" type="radio" name="SperreZusagePreis<?php if($ksSperreZusagePreis) echo '" checked="checked'?>" value="1" /> für Zusagende nicht veränderbar &nbsp;
  <input class="admRadio" type="radio" name="SperreZusagePreis<?php if(!$ksSperreZusagePreis) echo '" checked="checked'?>" value="0" /> für Zusagende frei beschreibbar</div>
  <div class="admMini">Ein aus den Termindaten übernommener Preis kann überschrieben werden oder ist gesperrt. Lediglich bei fehlendem Preis im Termin könnte in jedem Fall ein Preis in der Zusage eingetragen werden.</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Zusagen können sofern das Feld <i>ANZAHL</i> vorhanden ist auch mehrere Plätze umfassen.
Dabei erfolgt normalerweise <i>eine</i> Zusage mit der eingetragenen Gesamt-Platzzahl.
Abweichend können aber auch Zusagen mit mehreren Platzwünschen aufgesplittet werden in die entsprechende Anzahl von separaten Einzelzusagen.
Dabei werden dann pro Zusage alle Formularfelder wie Name oder E-Mail usw. wiederholt einzeln abgefragt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusage aufsplitten</td>
 <td colspan="3">
 <input class="admRadio" type="radio" name="ZusageNamentlich<?php if(!$ksZusageNamentlich) echo '" checked="checked'?>" value="0" /> Mehrplatzzusagen &nbsp; <input class="admRadio" type="radio" name="ZusageNamentlich<?php if($ksZusageNamentlich) echo '" checked="checked'?>" value="1" /> aufsplitten in Einzelzusagen &nbsp; <span class="admMini"><u>Empfehlung</u>: Mehrplatzzusagen</span></td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Besucher können die von anderen Besuchern abgegebenen Zusagen zum jeweiligen Termin eventuell einsehen.
Entweder auf einer separaten Seite, die nach einem Klick auf den <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZeigeZusageIcon?>">-Klickschalter
in der Terminliste bzw. in den Termindetails (rechts vom <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Zusage-Eingabeschalter) erscheint oder/und
in einer Übersichtsliste, die direkt unter dem Eintragsformular für neue Zusagen erscheint.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schalterbeschriftung</td>
 <td colspan="3"><img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZeigeZusageIcon?>">
  <input style="width:10em" type="text" name="TxZeigeZusageIcon" value="<?php echo $ksTxZeigeZusageIcon?>" />
  <span class="admMini">Empfehlung: <i>Zusagen auflisten</i> oder <i>Bestellung zeigen</i></span>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenliste<br>für Besucher</td>
 <td colspan="3">
  <input type="checkbox" class="admCheck" name="ListeZeigeZusage" value="1"<?php if($ksListeZeigeZusage) echo' checked="checked"'?> /> Zusagenliste über <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZeigeZusageIcon?>">-Klickschalter (neben dem <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Eingabeschalter) in der Terminliste anbieten<br>
  <input type="checkbox" class="admCheck" name="GastLZeigeZusage" value="1"<?php if($ksGastLZeigeZusage) echo' checked="checked"'?> /> auch für unangemeldete Gäste<br>
  <input type="checkbox" class="admCheck" name="DetailZeigeZusage" value="1"<?php if($ksDetailZeigeZusage) echo' checked="checked"'?> /> Zusagenliste über <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZeigeZusageIcon?>">-Klickschalter (neben dem <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="<?php echo $ksTxZusageIcon?>">-Eingabeschalter) in den Termindetails anbieten<br>
  <input type="checkbox" class="admCheck" name="GastDZeigeZusage" value="1"<?php if($ksGastDZeigeZusage) echo' checked="checked"'?> /> auch für unangemeldete Gäste<br>
  <input type="checkbox" class="admCheck" name="ZusageFormMitListe" value="1"<?php if($ksZusageFormMitListe) echo' checked="checked"'?> /> Zusagenliste direkt unter dem Zusageneintragsformular darstellen<br>
  <input type="checkbox" class="admCheck" name="GastZusageFormMitL" value="1"<?php if($ksGastZusageFormMitL) echo' checked="checked"'?> /> auch für unangemeldete Gäste
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Positionen im<br>Zusageneintrags-<br>formular sowie in<br>der Zusagenliste<br>für Gäste oder<br>angemeldete<br>Besucher</td>
 <td colspan="3">
  <table class="admTabl" style="width:auto;" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl"><td><b>Feldname</b></td><td align="center"><b>Zeile im Eintragsformular</b></td><td align="center"><b>Tabellenspalte für Gäste</b></td><td align="center"><b>für angemeldete Benutzer</b></td></tr>
   <tr class="admTabl">
    <td><?php echo $kal_ZusageFelder[0]?></td>
    <td>&nbsp;</td>
    <td align="center"><select name="ZLF0" size="1" style="width:50px;"><option value="0">--</option><option value="1"<?php if($aZusageLstFeld[0]) echo ' selected="selected"'?>>0</option></select></td>
    <td align="center"><select name="NZF0" size="1" style="width:50px;"><option value="0">--</option><option value="1"<?php if($aNZusageLstFld[0]) echo ' selected="selected"'?>>0</option></select></td>
   </tr>
<?php
 $sOpt=''; for($i=1;$i<$nZusageFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>'; $sOp2=substr($sOpt,strpos($sOpt,'<option value="2">'));
 $nZLF=(isset($aZusageLstFeld[1])?$aZusageLstFeld[1]:'0'); $nNZF=(isset($aNZusageLstFld[1])?$aNZusageLstFld[1]:'0'); $nZSF=(isset($aZusageFrmFeld[1])?$aZusageFrmFeld[1]:'0');
 for($i=1;$i<$nZusageFelder;$i++){
  $nZLF=(isset($aZusageLstFeld[$i])?$aZusageLstFeld[$i]:'0'); $nNZF=(isset($aNZusageLstFld[$i])?$aNZusageLstFld[$i]:'0'); $nZSF=(isset($aZusageFrmFeld[$i])?$aZusageFrmFeld[$i]:'0');
  $sFN=$kal_ZusageFelder[$i]; if($sFN=='ANZAHL
  ') if(strlen($ksZusageNameAnzahl)>0) $sFN=$ksZusageNameAnzahl;
  echo "\n".'   <tr  class="admTabl">';
  echo "\n".'    <td>'.$sFN.'</td>';
  echo "\n".'    <td align="center">'.($i!=1&&$i!=5&&$i!=6&&$i!=7?'<select name="ZSF'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nZSF.'"','value="'.$nZSF.'" selected="selected"',$sOpt).'</select>':'&nbsp;').'</td>';
  echo "\n".'    <td align="center"><select name="ZLF'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nZLF.'"','value="'.$nZLF.'" selected="selected"',$sOpt).'</select></td>';
  echo "\n".'    <td align="center"><select name="NZF'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nNZF.'"','value="'.$nNZF.'" selected="selected"',$sOpt).'</select></td>';
  echo "\n".'   </tr>';
 }
?>
  </table>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-Adresse</td>
 <td colspan="3"><input type="checkbox" class="admRadio" name="ZeigeZusageEml" value="1"<?php if($ksZeigeZusageEml) echo' checked="checked"'?> /> E-Mail-Adresse in den Zusagelisten im Besucherbereich öffentlich anzeigen<div class="admMini">Empfehlung: <i>nicht</i> anzeigen</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">sofern der<br>Zusagenstatus<br>in der Liste<br>angezeigt wird</td>
 <td colspan="3">
  <input type="radio" class="admRadio" name="ZusageListeStatus" value="1"<?php if($ksZusageListeStatus==1) echo' checked="checked"'?> /> nur gültige/bestätigte/freigeschaltete Zusagen anzeigen als <img src="<?php echo $sHttp?>grafik/punktGrn.gif" title="<?php echo $ksTxZusage1Status?>" width="12" height="12" border="0"><input style="width:10em" type="text" name="TxZusage1Status" value="<?php echo $ksTxZusage1Status?>" /><br>
  <input type="radio" class="admRadio" name="ZusageListeStatus" value="2"<?php if($ksZusageListeStatus==2) echo' checked="checked"'?> /> auch bestätigte aber noch nicht freigeschaltete anzeigen als <img src="<?php echo $sHttp?>grafik/punktRtGn.gif" title="<?php echo $ksTxZusage2Status?>" width="12" height="12" border="0"><input style="width:10em" type="text" name="TxZusage2Status" value="<?php echo $ksTxZusage2Status?>" /><br>
  <input type="radio" class="admRadio" name="ZusageListeStatus" value="0"<?php if($ksZusageListeStatus==0) echo' checked="checked"'?> /> auch unbestätigte Zusagen anzeigen als <img src="<?php echo $sHttp?>grafik/punktRot.gif" title="<?php echo $ksTxZusage0Status?>" width="12" height="12" border="0"><input style="width:10em" type="text" name="TxZusage0Status" value="<?php echo $ksTxZusage0Status?>" /> und<br>
  <span style="display:inline-block;width:1.15em;">&nbsp;</span> auch zum Widerruf vorgemerkte Zusagen anzeigen als <img src="<?php echo $sHttp?>grafik/punktRotX.gif" title="<?php echo $ksTxZusage3Status?>" width="12" height="12" border="0"><input style="width:10em" type="text" name="TxZusage3Status" value="<?php echo $ksTxZusage3Status?>" /> und<br>
  <span style="display:inline-block;width:1.15em;">&nbsp;</span> auch zum Widerruf teilbestätigte Zusagen anzeigen als <img src="<?php echo $sHttp?>grafik/punktRtGnX.gif" title="<?php echo $ksTxZusage4Status?>" width="12" height="12" border="0"><input style="width:10em" type="text" name="TxZusage4Status" value="<?php echo $ksTxZusage4Status?>" />
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenüberschrift</td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusagenBisher" value="<?php echo $ksTxZusagenBisher?>" /><div class="admMini">Muster: <i>Bisher wurden folgende Zusagen gebucht:</i></div>
  <input style="width:100%" type="text" name="TxKeineZusagen" value="<?php echo $ksTxKeineZusagen?>" /><div class="admMini">Muster: <i>noch keine Zusagen zu diesem Termin eingetragen</i></div>
  <input style="width:100%" type="text" name="TxNzUebersicht" value="<?php echo $ksTxNzUebersicht?>" /><div class="admMini">Muster: <i>Übersicht eigener Zusagen</i></div>
  <input style="width:100%" type="text" name="TxNfUebersicht" value="<?php echo $ksTxNfUebersicht?>" /><div class="admMini">Muster: <i>Übersicht fremder Zusagen</i></div>
  <input style="width:100%" type="text" name="TxZusagenSuchen" value="<?php echo $ksTxZusagenSuchen?>" /><div class="admMini">Muster: <i>Zusagen durchsuchen</i></div>
  <input style="width:100%" type="text" name="TxNfSuchErgebnis" value="<?php echo $ksTxNfSuchErgebnis?>" /><div class="admMini">Muster: <i>Suchergebnis in Zusagen</i></div>
 </td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">Im Benutzerzentrum können die Liste eigener Zusagen (die vom Benutzer selbst zugesagten Termine) und die Liste fremder Zusagen (die von anderen Benutzern zu eigenen Termine getroffenen Zusagen) verlinkt werden</td></tr>
<tr class="admTabl">
 <td class="admSpa1">eigene Zusagen</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZentrumEigeneZusage" value="1<?php if($ksZentrumEigeneZusage) echo '" checked="checked'?>" /> Zusagenliste eigener Zusagen im Benutzerzentrum verlinken</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">fremde Zusagen</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZentrumFremdeZusage" value="1<?php if($ksZentrumFremdeZusage) echo '" checked="checked'?>" /> Zusagenliste fremder Zusagen im Benutzerzentrum verlinken</td>
</tr>
<tr class="admTabl"><td colspan="4" class="admSpa2">In der Liste eigener Zusagen im Benutzerzentrum kann per Voreinstellung nach Zusagen für kommente Termine bzw. für abgelaufene Termine gefiltert werden. Diese Voreinstellung kann der Benutzer jedoch bei Bedarf überschreiben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenfilter</td>
 <td colspan="3">
 <div><select name="ZusageLstFilter" size="1"><option value="">alle Zusagen auflisten</option><option value="1<?php if($ksZusageLstFilter==1) echo '" selected="selected'?>">nur Zusagen zu kommenden Terminen</option><option value="2<?php if($ksZusageLstFilter==2) echo '" selected="selected'?>">nur Zusagen zu abelaufenen Terminen</option></select>
 </td>
</tr>

<tr>
 <td colspan="4" style="background-color:#ffffff;"><p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">In der Terminliste für Administrator/Autoren kann eine <img src="<?php echo KALPFAD?>grafik/icon_Lupe.gif" width="12" height="13" border="0" title="<?php echo KAL_TxZeigeZusageIcon ?>"> Zusatzspalte mit Verweis auf die Zusagenliste bzw. eine <img src="<?php echo KALPFAD?>grafik/icon_Zusagen.gif" width="12" height="13" border="0" title="<?php echo KAL_TxZusageIcon ?>"> Zusatzspalte zum Zusageneintrag eingeblendet werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenspalte</td>
 <td colspan="3"><input class="admCheck" type="checkbox" name="ZusageTrmListe" value="1<?php if($amZusageTrmListe) echo '" checked="checked'?>" /> Zusagenspalte in der Terminliste des Administrators einblenden &nbsp; <span class="admMini">Empfehlung: aktivieren</span><br>
 <input class="admCheck" type="checkbox" name="ZusageTrmEintrag" value="1<?php if($amZusageTrmEintrag) echo '" checked="checked'?>" /> Eintragsspalte in der Terminliste des Administrators einblenden &nbsp; <span class="admMini">Empfehlung: aktivieren</span></td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Die Darstellung der Terminzusagen in der Zusagenliste für Administrator/Autoren erfolgt mit folgenden Werten:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenliste<br>für Administratoren<br>und Terminautoren</td>
 <td colspan="3"><input type="radio" class="admCheck" name="ZusageAdmRueckw" value="0"<?php if(!$ksZusageAdmRueckw) echo' checked="checked"'?> /> vorwärts sortieren &nbsp;
 <input type="radio" class="admCheck" name="ZusageAdmRueckw" value="1"<?php if($ksZusageAdmRueckw) echo' checked="checked"'?> /> rückwärts sortieren<br>
 <input type="text" name="ZusageAdmLstLaenge" value="<?php echo $ksZusageAdmLstLaenge?>" size="2" style="width:50px;" /> Zeilen in der Zusagenliste für Administrator/Autoren<br>
 <input type="text" name="ZusageAdmLstVstBreit" value="<?php echo $ksZusageAdmLstVstBreit?>" size="2" style="width:50px;" /> Zeichen, nach denen der Veranstaltungsname gekürzt wird&nbsp; <span class="admMini">(Empfehlung: max. 80)</span><br>
 <div><input type="checkbox" class="admRadio" name="ZusageAdmLstKommend" value="1"<?php if($ksZusageAdmLstKommend) echo ' checked="checked"'?> /> Zusagen zu kommende Terminen auflisten</div>
 <div><input type="checkbox" class="admRadio" name="ZusageAdmLstVorbei" value="1"<?php if($ksZusageAdmLstVorbei) echo ' checked="checked"'?> /> Zusagen zu vergangenen Terminen auflisten</div>
 </td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Positionen in der<br>Zusagenliste und<br>Druckliste und<br>Exportliste des<br>Administrators</td>
 <td colspan="3">
  <table class="admTabl" style="width:auto;" border="0" cellpadding="2" cellspacing="1">
   <tr class="admTabl"><td><b>Feldname</b></td><td align="center"><b>Listenspalte für Admin</b></td><td align="center"><b>Druckspalte für Admin</b></td><td align="center"><b>Exportspalte für Admin</b></td></tr>
   <tr class="admTabl">
    <td><?php echo $kal_ZusageFelder[0]?></td>
    <td align="center"><input name="LsA0" value="1" type="hidden">0&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
    <td align="center"><select name="DrA0" size="1" style="width:50px;"><option value="0">--</option><option value="1"<?php if($aZusageDrckAdm[0]) echo ' selected="selected"'?>>0</option></select></td>
    <td align="center"><select name="ExA0" size="1" style="width:50px;"><option value="0">--</option><option value="1"<?php if($aZusageExptAdm[0]) echo ' selected="selected"'?>>0</option></select></td>
   </tr>
<?php
  $nLsA=(isset($aZusageListAdm[1])?$aZusageListAdm[1]:'0'); $nDrA=(isset($aZusageDrckAdm[1])?$aZusageDrckAdm[1]:'0'); $nExA=(isset($aZusageExptAdm[1])?$aZusageExptAdm[1]:'0');
?>
   <tr class="admTabl">
    <td><?php echo $kal_ZusageFelder[1]?></td>
    <td align="center"><input name="LsA1" value="1" type="hidden">1&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
    <td align="center"><select name="DrA1" size="1" style="width:50px;"><option value="0">--</option><?php echo str_replace('value="'.$nDrA.'"','value="'.$nDrA.'" selected="selected"',$sOpt)?></select></td>
    <td align="center"><select name="ExA1" size="1" style="width:50px;"><option value="0">--</option><?php echo str_replace('value="'.$nExA.'"','value="'.$nExA.'" selected="selected"',$sOpt)?></select></td>
   </tr>
<?php
 for($i=2;$i<$nZusageFelder;$i++){
  $nLsA=(isset($aZusageListAdm[$i])?$aZusageListAdm[$i]:'0'); $nDrA=(isset($aZusageDrckAdm[$i])?$aZusageDrckAdm[$i]:'0'); $nExA=(isset($aZusageExptAdm[$i])?$aZusageExptAdm[$i]:'0');
  $sFN=$kal_ZusageFelder[$i]; if($sFN=='ANZAHL
  ') if(strlen($ksZusageNameAnzahl)>0) $sFN=$ksZusageNameAnzahl;
  echo "\n".'   <tr  class="admTabl">';
  echo "\n".'    <td>'.$sFN.'</td>';
  echo "\n".'    <td align="center"><select name="LsA'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nLsA.'"','value="'.$nLsA.'" selected="selected"',$sOp2).'</select></td>';
  echo "\n".'    <td align="center"><select name="DrA'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nDrA.'"','value="'.$nDrA.'" selected="selected"',$sOpt).'</select></td>';
  echo "\n".'    <td align="center"><select name="ExA'.$i.'" size="1" style="width:50px;"><option value="0">--</option>'.str_replace('value="'.$nExA.'"','value="'.$nExA.'" selected="selected"',$sOpt).'</select></td>';
  echo "\n".'   </tr>';
 }
?>
  </table>
 </td>
</tr>

<tr class="admTabl"><td colspan="4" class="admSpa2">Auch die Zusagenübersicht zum Termin für Administratoren und berechtigte Autoren kann gedruckt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Drucktitel<div style="margin-top:25px;">Terminzeile</div><div style="margin-top:25px;">Druckspalten</div><div style="margin-top:12px;">Summenzeile</div></td>
 <td colspan="3">
  <input style="width:100%" type="text" name="TxZusageDrckTit" value="<?php echo $ksTxZusageDrckTit?>" /><div class="admMini" style="margin-bottom:6px;"><i>aktuelle Zusagenübersicht</i></div>
  <input style="width:100%" type="text" name="TxZusageDrckTrm" value="<?php echo $ksTxZusageDrckTrm?>" /><div class="admMini" style="margin-bottom:6px;">Terminzeile (Feldnamen der gewünschten Felder aufzählen): <i>{Datum} {Zeit}, {Ort}</i></div>
  <div style="margin-top:6px;margin-bottom:6px;"><input type="radio" class="admRadio" name="ZusageAdmDrKonf" value="1"<?php if($ksZusageAdmDrKonf) echo' checked="checked"'?> /> Druckspalten wie oben eingestellt &nbsp;
  <input type="radio" class="admRadio" name="ZusageAdmDrKonf" value="0"<?php if(!$ksZusageAdmDrKonf) echo' checked="checked"'?> /> Druckspalten wie bei Listenspalten</div>
  <input style="width:100%" type="text" name="TxZusageDrckSum" value="<?php echo $ksTxZusageDrckSum?>" /><div class="admMini">Druck-Summe: <i>#B Buchungen, #Z Zusagen bei #K Plätzen</i></div>
 </td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php if(!$ksZusagen){?>
<div class="admBox">
<u>Hinweis:</u> Das Terminzusagesystem/Reservierungssystem/Bestellsystem ist ein kostenpflichtiges Zusatzmodul zum Kalender-Script ab Version 3.4.
Eine eingeschränkte Demo-Version des Moduls kann jederzeit zu Testzwecken installiert und deinstalliert werden.
Für eine uneingeschränkte Funktion ist eine separate Freischaltung zum Modul anzufordern.
</div>

<?php
}
echo fSeitenFuss();

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADM_'.$n)){
  $p=strpos($sWerte,'ADM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>