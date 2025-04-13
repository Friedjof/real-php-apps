<?php
include 'hilfsFunktionen.php'; $ksListenKateg=KAL_ListenKateg; $ksMonatsKateg=KAL_MonatsKateg;
echo fSeitenKopf('Farbeinstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=380,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KFa');

if(file_exists(KALPFAD.'kalStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(KALPFAD.'kalStyles.css')))); $bNeu=false;
 $sNavHgI=fLiesHgImg('ul.kalNavi li'); $sDNvHgI=fLiesHgImg('a.kalDetR'); // muss auch bei POST bleiben wegen Vergleich
 if($_SERVER['REQUEST_METHOD']=='GET'){
  $sPageH=fLiesHGFarb('body.kalSeite'); $sMailH=fLiesHGFarb('body.kalEMail'); $sMailF=fLiesFarbe('body.kalEMail');
  $sTBoxW=fLiesMaxWeite('div.kalBox');
  $sTBoxF=fLiesFontSize('div.kalBox');
  $sTBoxG=fLiesFontSize('div.kalBox',2);
  $sTBoxS=fLiesScreenW('gesamte Ausgabe');
  $sTListW=fLiesScreenW('Terminliste mit Spalten');
  $sTMonaW=fLiesScreenW('Monatsliste mit Spalten');
  $sPMeld=fLiesFarbe('p.kalMeld'); $sPErfo=fLiesFarbe('p.kalErfo'); $sPFehl=fLiesFarbe('p.kalFehl');
  //Aktivitaetslinks
  $sAMnuOF=fLiesFarbe('ul.kalMnuO a');       $sAMnuOH=fLiesHGFarb('ul.kalMnuO a');
  $sAMnuOA=fLiesFarbe('ul.kalMnuO a:hover'); $sAMnuOI=fLiesHGFarb('ul.kalMnuO a:hover');
  $sAMnuOR=fLiesRahmFarb('ul.kalMnuO li');   $sAMnuOL=fLiesRahmArt('ul.kalMnuO li');
  $sAMnuOV=fLiesContent('ul.kalMnuO a::before'); $sAMnuON=fLiesContent('ul.kalMnuO a::after');
  $sAMnuOZ=fLiesHGFarb('ul.kalMnuO');        $sAMnuOB=fLiesRahmFarb('ul.kalMnuO');
  $sAMnuUF=fLiesFarbe('ul.kalMnuU a');       $sAMnuUH=fLiesHGFarb('ul.kalMnuU a');
  $sAMnuUA=fLiesFarbe('ul.kalMnuU a:hover'); $sAMnuUI=fLiesHGFarb('ul.kalMnuU a:hover');
  $sAMnuUR=fLiesRahmFarb('ul.kalMnuU li');   $sAMnuUL=fLiesRahmArt('ul.kalMnuU li');
  $sAMnuUV=fLiesContent('ul.kalMnuU a::before'); $sAMnuUN=fLiesContent('ul.kalMnuU a::after');
  $sAMnuUZ=fLiesHGFarb('ul.kalMnuU');   $sAMnuUB=fLiesRahmFarb('ul.kalMnuU');
  //Navigator (Liste)
  $sNavLnk=fLiesFarbe('ul.kalNavi a');     $sNavLkA=fLiesFarbe('ul.kalNavi a:hover');
  $sNaviHg=fLiesHGFarb('ul.kalNavi li');   $sNavHgI=fLiesHgImg('ul.kalNavi li');
  $sNaviRF=fLiesRahmFarb('ul.kalNavi li'); $sNaviRA=fLiesRahmArt('ul.kalNavi li');
  //Navigatorzeile
  $sNavLHg=fLiesHGFarb('div.kalNavL'); $sNavLRF=fLiesRahmFarb('div.kalNavL'); $sNavLRA=fLiesRahmArt('div.kalNavL');
  $sNavLSz=fLiesFarbe('div.kalSZhl');
  //Navigator (Detail)
  $sDNvLnk=fLiesFarbe('a.kalDetV,a.kalDetR');    $sDNvLkA=fLiesFarbe('a.kalDetR:hover');
  $sDNviHg=fLiesHGFarb('a.kalDetV,a.kalDetR');   $sDNvHgI=fLiesHgImg('a.kalDetR');
  $sDNviRF=fLiesRahmFarb('a.kalDetV,a.kalDetR'); $sDNviRA=fLiesRahmArt('a.kalDetV,a.kalDetR');
  //Navigatorzeile
  $sDNvLHg=fLiesHGFarb('div.kalNavD'); $sDNvLRF=fLiesRahmFarb('div.kalNavD'); $sDNvLRA=fLiesRahmArt('div.kalNavD');
  $sDNvLSz=fLiesFarbe('div.kalNavD');
  // Tabelle generell
  $sTablR=fLiesRahmArt('div.kalTbSpa'); $sTablF=fLiesRahmFarb('div.kalTbSpa');
  //Terminliste
  $sTKopfF=fLiesFarbe('div.kalTbZl0'); $sTKopfH=fLiesHGFarb('div.kalTbZl0');
  $sTbZl1F=fLiesFarbe('div.kalTbZl1'); $sTbZl1H=fLiesHGFarb('div.kalTbZl1');
  $sTbZl2F=fLiesFarbe('div.kalTbZl2'); $sTbZl2H=fLiesHGFarb('div.kalTbZl2');
  $sTLfndF=fLiesFarbe('div.kalTbZlLfdE'); $sTLfndH=fLiesHGFarb('div.kalTbZlLfdE');
  $sTAktuF=fLiesFarbe('div.kalTbZlAktE'); $sTAktuH=fLiesHGFarb('div.kalTbZlAktE');
  $sTKatAF=fLiesFarbe('div.kalTrmKatA'); $sTKatAH=fLiesHGFarb('div.kalTrmKatA');
  $sTKatBF=fLiesFarbe('div.kalTrmKatB'); $sTKatBH=fLiesHGFarb('div.kalTrmKatB');
  $sTKatCF=fLiesFarbe('div.kalTrmKatC'); $sTKatCH=fLiesHGFarb('div.kalTrmKatC');
  $sTKatDF=fLiesFarbe('div.kalTrmKatD'); $sTKatDH=fLiesHGFarb('div.kalTrmKatD');
  $sTKatEF=fLiesFarbe('div.kalTrmKatE'); $sTKatEH=fLiesHGFarb('div.kalTrmKatE');
  $sTKatFF=fLiesFarbe('div.kalTrmKatF'); $sTKatFH=fLiesHGFarb('div.kalTrmKatF');
  $sTKatGF=fLiesFarbe('div.kalTrmKatG'); $sTKatGH=fLiesHGFarb('div.kalTrmKatG');
  $sADetl=fLiesFarbe('a.kalDetl:link'); $sADetA=fLiesFarbe('a.kalDetl:hover');
  $sPText=fLiesFarbe('p.kalText'); $sLText=fLiesFarbe('li.kalText');
  $sAText=fLiesFarbe('a.kalText:link'); $sATexA=fLiesFarbe('a.kalText:hover');
  $sTZlTrnF=fLiesFarbe('div.kalTbZlT'); $sTZlTrnH=fLiesHGFarb('div.kalTbZlT');
  $sVBldR=fLiesRahmArt('div.kalVBld'); $sVBldF=fLiesRahmFarb('div.kalVBld');
  // Monatskalender
  $sTMonKF=fLiesFarbe('div.kalTZ0M');  $sTMonKH=fLiesHGFarb('div.kalTZ0M');
  $sTMonWF=fLiesFarbe('div.kalTbSpW'); $sTMonWH=fLiesHGFarb('div.kalTbSpW');
  $sTMonZF=fLiesFarbe('div.kalTbSpT'); $sTMonZH=fLiesHGFarb('div.kalTbSpT');
  $sTMonXF=fLiesFarbe('div.kalGrey');  $sTMonXH=fLiesHGFarb('div.kalGrey');
  $sTMDatF=fLiesFarbe('div.kalMDat');  $sTMDatH=fLiesHGFarb('div.kalMDat');
  $sTMonHF=fLiesFarbe('div.kalMHte');  $sTMonHH=fLiesHGFarb('div.kalMHte');
  $sTMonFF=fLiesFarbe('div.kalMFtg');  $sTMonFH=fLiesHGFarb('div.kalMFtg');
  $sAMDatN=fLiesFarbe('a.kalMDat:link'); $sAMDaAN=fLiesFarbe('a.kalMDat:hover');
  $sAMDatH=fLiesFarbe('a.kalMHte:link'); $sAMDaAH=fLiesFarbe('a.kalMHte:hover');
  $sAMDatF=fLiesFarbe('a.kalMFtg:link'); $sAMDaAF=fLiesFarbe('a.kalMFtg:hover');
  $sTMDetF=fLiesFarbe('div.kalMDet');  $sTMDetH=fLiesHGFarb('div.kalMDet');
  $sAMDetN=fLiesFarbe('a.kalMDet:link'); $sAMDeAN=fLiesFarbe('a.kalMDet:hover');
  // Vorschaubox
  $sDVBoxR=fLiesRahmFarb('div#kalVBox'); $sDVBoxL=fLiesRahmArt('div#kalVBox');
  $sDVBoxF=fLiesFarbe('div#kalVBox');    $sDVBoxH=fLiesHGFarb('div#kalVBox');
  // Filterzeile
  $sDFiltR=fLiesRahmFarb('div.kalFilt'); $sDFiltL=fLiesRahmArt('div.kalFilt');
  $sDFiltF=fLiesFarbe('div.kalFilt');    $sDFiltH=fLiesHGFarb('div.kalFilt');
  $sIFiltR=fLiesRahmFarb('input.kalSFlt');$sIFiltL=fLiesRahmArt('input.kalSFlt');
  $sIFiltF=fLiesFarbe('input.kalSFlt');  $sIFiltH=fLiesHGFarb('input.kalSFlt');
  // Eingaben
  $sIEingR=fLiesRahmFarb('input.kalEing');$sIEingL=fLiesRahmArt('input.kalEing'); // 2x wegen File
  $sIEingF=fLiesFarbe('input.kalEing');   $sIEingH=fLiesHGFarb('input.kalEing'); // Farbe 2x
  $sDFehlR=fLiesRahmFarb('div.kalFhlt');
  $sIEingZ=fLiesHoehe('textarea.kalEing',2);
 }elseif($_SERVER['REQUEST_METHOD']=='POST'){
  $sPageH=fNewCol('PageH'); if(fSetzHGFarb($sPageH,'body.kalSeite')) $bNeu=true;
  $sMailF=fNewCol('MailF'); if($sMailF=='transparent') $sMailF='#666666'; if(fSetzeFarbe($sMailF,'body.kalEMail')) $bNeu=true;
  $sMailH=fNewCol('MailH'); if($sMailH=='transparent') $sMailH='#bbbbbb'; if(fSetzHGFarb($sMailH,'body.kalEMail')) $bNeu=true;

  $sTBoxW=fTxtSiz('TBoxW'); if(fSetzeMaxWeite(($sTBoxW?$sTBoxW:'auto'),'div.kalBox')) $bNeu=true;

  $sTBoxF=fTxtSiz('TBoxF'); if(fSetzeFontSize($sTBoxF,'div.kalBox')) $bNeu=true;
  $sTBoxG=fTxtSiz('TBoxG'); if(fSetzeFontSize($sTBoxG,'div.kalBox',2)) $bNeu=true;
  $sTBoxS=fTxtSiz('TBoxS'); if(fSetzScreenW($sTBoxS,'gesamte Ausgabe')) $bNeu=true;

  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'Terminliste mit Spalten')) $bNeu=true;
  $sTMonaW=fTxtSiz('TMonaW'); if(fSetzScreenW($sTMonaW, 'Monatsliste mit Spalten')) $bNeu=true;
  $sPMeld=fNewCol('PMeld'); if(fSetzeFarbe($sPMeld,'p.kalMeld')) $bNeu=true;
  $sPErfo=fNewCol('PErfo'); if(fSetzeFarbe($sPErfo,'p.kalErfo')) $bNeu=true;
  $sPFehl=fNewCol('PFehl'); if(fSetzeFarbe($sPFehl,'p.kalFehl')) $bNeu=true;
  //Aktivitaetslinks oben
  $sAMnuOF=fNewCol('AMnuOF'); if(fSetzeFarbe($sAMnuOF,'ul.kalMnuO a')) $bNeu=true;
  $sAMnuOH=fNewCol('AMnuOH'); if(fSetzHGFarb($sAMnuOH,'ul.kalMnuO a')) $bNeu=true;
  $sAMnuOA=fNewCol('AMnuOA'); if(fSetzeFarbe($sAMnuOA,'ul.kalMnuO a:hover')) $bNeu=true;
  $sAMnuOI=fNewCol('AMnuOI'); if(fSetzHGFarb($sAMnuOI,'ul.kalMnuO a:hover')) $bNeu=true;
  $sAMnuOR=fNewCol('AMnuOR'); if(fSetzRahmFarb($sAMnuOR,'ul.kalMnuO li'))  $bNeu=true;
  $sAMnuOL=$_POST['AMnuOL'];  if(fSetzeRahmArt($sAMnuOL,'ul.kalMnuO li')) $bNeu=true;
  $sAMnuOV=$_POST['AMnuOV'];  if(fSetzContent('ul.kalMnuO a::before',$sAMnuOV)) $bNeu=true;
  $sAMnuON=$_POST['AMnuON'];  if(fSetzContent('ul.kalMnuO a::after',$sAMnuON)) $bNeu=true;
  $sAMnuOZ=fNewCol('AMnuOZ'); if(fSetzHGFarb($sAMnuOZ,'ul.kalMnuO')) $bNeu=true;
  $sAMnuOB=fNewCol('AMnuOB'); if(fSetzRahmFarb($sAMnuOB,'ul.kalMnuO'))  $bNeu=true;
  //Aktivitaetslinks unten
  $sAMnuUF=fNewCol('AMnuUF'); if(fSetzeFarbe($sAMnuUF,'ul.kalMnuU a')) $bNeu=true;
  $sAMnuUH=fNewCol('AMnuUH'); if(fSetzHGFarb($sAMnuUH,'ul.kalMnuU a')) $bNeu=true;
  $sAMnuUA=fNewCol('AMnuUA'); if(fSetzeFarbe($sAMnuUA,'ul.kalMnuU a:hover')) $bNeu=true;
  $sAMnuUI=fNewCol('AMnuUI'); if(fSetzHGFarb($sAMnuUI,'ul.kalMnuU a:hover')) $bNeu=true;
  $sAMnuUR=fNewCol('AMnuUR'); if(fSetzRahmFarb($sAMnuUR,'ul.kalMnuU li'))  $bNeu=true;
  $sAMnuUL=$_POST['AMnuUL'];  if(fSetzeRahmArt($sAMnuUL,'ul.kalMnuU li')) $bNeu=true;
  $sAMnuUV=$_POST['AMnuUV'];  if(fSetzContent('ul.kalMnuU a::before',$sAMnuUV)) $bNeu=true;
  $sAMnuUN=$_POST['AMnuUN'];  if(fSetzContent('ul.kalMnuU a::after',$sAMnuUN)) $bNeu=true;
  $sAMnuUZ=fNewCol('AMnuUZ'); if(fSetzHGFarb($sAMnuUZ,'ul.kalMnuU')) $bNeu=true;
  $sAMnuUB=fNewCol('AMnuUB'); if(fSetzRahmFarb($sAMnuUB,'ul.kalMnuU'))  $bNeu=true;
  //Navigator (Liste)
  $sNavLnk=fNewCol('NavLnk'); if(fSetzeFarbe($sNavLnk,'ul.kalNavi a')) $bNeu=true;
  $sNavLkA=fNewCol('NavLkA'); if(fSetzeFarbe($sNavLkA,'ul.kalNavi a:hover')) $bNeu=true;
  $sNaviHg=fNewCol('NaviHg'); if(fSetzHGFarb($sNaviHg,'ul.kalNavi li')) $bNeu=true;
  $s=(fTxtSiz('NavHgI')?'a':'p'); // HG_Bild
  if($s!=$sNavHgI){
   $sNavHgI=$s; $s=($s=='a'?true:false); $bNeu=true;
   fSetzeHgImg('ul.kalNavi li',$s); fSetzeHgImg('ul.kalNavi li.kalNavL',$s); fSetzeHgImg('ul.kalNavi li.kalNavR',$s);
  }
  $sNaviRF=fNewCol('NaviRF'); if(fSetzRahmFarb($sNaviRF,'ul.kalNavi li'))  $bNeu=true;
  $sNaviRA=$_POST['NaviRA'];  if(fSetzeRahmArt($sNaviRA,'ul.kalNavi li')) $bNeu=true;
  //Navigatorzeile
  $sNavLHg=fNewCol('NavLHg'); if(fSetzHGFarb($sNavLHg,'div.kalNavL')) $bNeu=true;
  $sNavLRF=fNewCol('NavLRF'); if(fSetzRahmFarb($sNavLRF,'div.kalNavL'))  $bNeu=true;
  $sNavLRA=$_POST['NavLRA'];  if(fSetzeRahmArt($sNavLRA,'div.kalNavL')) $bNeu=true;
  $sNavLSz=fNewCol('NavLSz'); if(fSetzeFarbe($sNavLSz,'div.kalSZhl')) $bNeu=true; //Seitenzaehler
  //Navigator (Detail)
  $sDNvLnk=fNewCol('DNvLnk'); if(fSetzeFarbe($sDNvLnk,'a.kalDetV,a.kalDetR')) $bNeu=true;
  $sDNvLkA=fNewCol('DNvLkA'); if(fSetzeFarbe($sDNvLkA,'a.kalDetR:hover')) $bNeu=true;
  $sDNviHg=fNewCol('DNviHg'); if(fSetzHGFarb($sDNviHg,'a.kalDetV,a.kalDetR')) $bNeu=true;
  $s=(fTxtSiz('DNvHgI')?'a':'p'); // HG_Bild
  if($s!=$sDNvHgI){
   $sDNvHgI=$s; $s=($s=='a'?true:false); $bNeu=true;
   fSetzeHgImg('a.kalDetR',$s); fSetzeHgImg('a.kalDetV',$s);
   if($p=strpos($sCss,'a.kalDetV::before')) if($e=strpos($sCss,'}',$p)) $sCss=substr_replace($sCss,"a.kalDetV::before{content:'".($s?'':'>>')."';}",$p,$e+1-$p);
   if($p=strpos($sCss,'a.kalDetR::after'))  if($e=strpos($sCss,'}',$p)) $sCss=substr_replace($sCss,"a.kalDetR::after{content:'".($s?'':'<<')."';}",$p,$e+1-$p);
  }
  $sDNviRF=fNewCol('DNviRF'); if(fSetzRahmFarb($sDNviRF,'a.kalDetV,a.kalDetR'))  $bNeu=true;
  $sDNviRA=$_POST['DNviRA'];  if(fSetzeRahmArt($sDNviRA,'a.kalDetV,a.kalDetR')) $bNeu=true;
  //Navigatorzeile
  $sDNvLHg=fNewCol('DNvLHg'); if(fSetzHGFarb($sDNvLHg,'div.kalNavD')) $bNeu=true;
  $sDNvLRF=fNewCol('DNvLRF'); if(fSetzRahmFarb($sDNvLRF,'div.kalNavD')) $bNeu=true;
  $sDNvLRA=$_POST['DNvLRA'];  if(fSetzeRahmArt($sDNvLRA,'div.kalNavD')) $bNeu=true;
  $sDNvLSz=fNewCol('DNvLSz'); if(fSetzeFarbe($sDNvLSz,'div.kalNavD')) $bNeu=true; //Seitenzaehler
  // Tabellenrahmen generell
  $sTablF=fNewCol('TablF'); $sTablR=$_POST['TablR'];
  if($sTablF!=fLiesRahmFarb('div.kalTbSpa')){
   //if(fSetzRahmFarb($sTablF,'div.kalTabl')) $bNeu=true; // Rahmen werden durch die Zellen bestimmt
   if(fSetzRahmFarbB($sTablF,'div.kalTbZl0')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.kalTbZL0')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.kalTbZL0')) $bNeu=true;
   if(fSetzRahmFarbB($sTablF,'div.kalTbZl1')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.kalTbZL1')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.kalTbZL1')) $bNeu=true;
   if(fSetzRahmFarbB($sTablF,'div.kalTbZl2')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.kalTbZL2')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.kalTbZL2')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbSpa')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbZlT')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.kalTbZlT',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbLst')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.kalTbLst',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbSp2')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.kalTbSp2',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTZ0M'))  $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbZlM')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbSpT')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.kalTbSpK')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.kalTbSpK',2)) $bNeu=true;
   if(fSetzRahmFarbL($sTablF,'div.kalTbSp0')) $bNeu=true;
  }
  if($sTablR!=fLiesRahmArt('div.kalTbSpa')){
   //if(fSetzeRahmArt($sTablR,'div.kalTabl')) $bNeu=true; // Rahmen werden durch die Zellen bestimmt
   if(fSetzeRahmArtB($sTablR,'div.kalTbZl0')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.kalTbZL0')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.kalTbZL0')) $bNeu=true;
   if(fSetzeRahmArtB($sTablR,'div.kalTbZl1')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.kalTbZL1')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.kalTbZL1')) $bNeu=true;
   if(fSetzeRahmArtB($sTablR,'div.kalTbZl2')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.kalTbZL2')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.kalTbZL2')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbSpa')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbZlT')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.kalTbZlT',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbLst')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.kalTbLst',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbSp2')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.kalTbSp2',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTZ0M'))  $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbZlM')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbSpT')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.kalTbSpK')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.kalTbSpK',2)) $bNeu=true;
   if(fSetzeRahmArtL($sTablR,'div.kalTbSp0')) $bNeu=true;
  }
  // Terminliste
  $sTKopfF=fNewCol('TKopfF'); if(fSetzeFarbe($sTKopfF,'div.kalTbZl0')) $bNeu=true; $sTKopfH=fNewCol('TKopfH'); if(fSetzHGFarb($sTKopfH,'div.kalTbZl0')) $bNeu=true; if(fSetzeFarbe($sTKopfF,'div.kalTbZL0')) $bNeu=true; if(fSetzHGFarb($sTKopfH,'div.kalTbZL0')) $bNeu=true;
  $sTbZl1F=fNewCol('TbZl1F'); if(fSetzeFarbe($sTbZl1F,'div.kalTbZl1')) $bNeu=true; $sTbZl1H=fNewCol('TbZl1H'); if(fSetzHGFarb($sTbZl1H,'div.kalTbZl1')) $bNeu=true; if(fSetzeFarbe($sTbZl1F,'div.kalTbZL1')) $bNeu=true; if(fSetzHGFarb($sTbZl1H,'div.kalTbZL1')) $bNeu=true;
  $sTbZl2F=fNewCol('TbZl2F'); if(fSetzeFarbe($sTbZl2F,'div.kalTbZl2')) $bNeu=true; $sTbZl2H=fNewCol('TbZl2H'); if(fSetzHGFarb($sTbZl2H,'div.kalTbZl2')) $bNeu=true; if(fSetzeFarbe($sTbZl2F,'div.kalTbZL2')) $bNeu=true; if(fSetzHGFarb($sTbZl2H,'div.kalTbZL2')) $bNeu=true;
  $sTLfndF=fNewCol('TLfndF'); if(fSetzeFarbe($sTLfndF,'div.kalTbZlLfdE')) $bNeu=true; $sTLfndH=fNewCol('TLfndH'); if(fSetzHGFarb($sTLfndH,'div.kalTbZlLfdE')) $bNeu=true;
  $sTAktuF=fNewCol('TAktuF'); if(fSetzeFarbe($sTAktuF,'div.kalTbZlAktE')) $bNeu=true; $sTAktuH=fNewCol('TAktuH'); if(fSetzHGFarb($sTAktuH,'div.kalTbZlAktE')) $bNeu=true;
  $sTKatAF=fNewCol('TKatAF'); if(fSetzeFarbe($sTKatAF,'div.kalTrmKatA')) $bNeu=true; $sTKatAH=fNewCol('TKatAH'); if(fSetzHGFarb($sTKatAH,'div.kalTrmKatA')) $bNeu=true;
  $sTKatBF=fNewCol('TKatBF'); if(fSetzeFarbe($sTKatBF,'div.kalTrmKatB')) $bNeu=true; $sTKatBH=fNewCol('TKatBH'); if(fSetzHGFarb($sTKatBH,'div.kalTrmKatB')) $bNeu=true;
  $sTKatCF=fNewCol('TKatCF'); if(fSetzeFarbe($sTKatCF,'div.kalTrmKatC')) $bNeu=true; $sTKatCH=fNewCol('TKatCH'); if(fSetzHGFarb($sTKatCH,'div.kalTrmKatC')) $bNeu=true;
  $sTKatDF=fNewCol('TKatDF'); if(fSetzeFarbe($sTKatDF,'div.kalTrmKatD')) $bNeu=true; $sTKatDH=fNewCol('TKatDH'); if(fSetzHGFarb($sTKatDH,'div.kalTrmKatD')) $bNeu=true;
  $sTKatEF=fNewCol('TKatEF'); if(fSetzeFarbe($sTKatEF,'div.kalTrmKatE')) $bNeu=true; $sTKatEH=fNewCol('TKatEH'); if(fSetzHGFarb($sTKatEH,'div.kalTrmKatE')) $bNeu=true;
  $sTKatFF=fNewCol('TKatFF'); if(fSetzeFarbe($sTKatFF,'div.kalTrmKatF')) $bNeu=true; $sTKatFH=fNewCol('TKatFH'); if(fSetzHGFarb($sTKatFH,'div.kalTrmKatF')) $bNeu=true;
  $sTKatGF=fNewCol('TKatGF'); if(fSetzeFarbe($sTKatGF,'div.kalTrmKatG')) $bNeu=true; $sTKatGH=fNewCol('TKatGH'); if(fSetzHGFarb($sTKatGH,'div.kalTrmKatG')) $bNeu=true;
  $sADetl=fNewCol('ADetl');   if(fSetzeFarbe($sADetl,'a.kalDetl:link')) $bNeu=true;  $sADetA=fNewCol('ADetA');   if(fSetzeFarbe($sADetA,'a.kalDetl:hover')) $bNeu=true;
  $sPText=fNewCol('PText'); if(fSetzeFarbe($sPText,'p.kalText')) $bNeu=true; $sLText=fNewCol('LText'); if(fSetzeFarbe($sLText,'li.kalText')) $bNeu=true;
  $sAText=fNewCol('AText'); if(fSetzeFarbe($sAText,'a.kalText:link')) $bNeu=true; $sATexA=fNewCol('ATexA'); if(fSetzeFarbe($sATexA,'a.kalText:hover')) $bNeu=true;
  $sTZlTrnF=fNewCol('TZlTrnF'); if(fSetzeFarbe($sTZlTrnF,'div.kalTbZlT')) $bNeu=true; $sTZlTrnH=fNewCol('TZlTrnH'); if(fSetzHGFarb($sTZlTrnH,'div.kalTbZlT')) $bNeu=true;
  $sVBldF=fNewCol('VBldF'); if(fSetzRahmFarb($sVBldF,'div.kalVBld')) $bNeu=true; $sVBldR=$_POST['VBldR'];  if(fSetzeRahmArt($sVBldR,'div.kalVBld')) $bNeu=true;
  // Monatskalender
  $sTMonKF=fNewCol('TMonKF'); if(fSetzeFarbe($sTMonKF,'div.kalTZ0M')) $bNeu=true; $sTMonKH=fNewCol('TMonKH'); if(fSetzHGFarb($sTMonKH,'div.kalTZ0M')) $bNeu=true;
  $sTMonWF=fNewCol('TMonWF'); if(fSetzeFarbe($sTMonWF,'div.kalTbSpW')) $bNeu=true; $sTMonWH=fNewCol('TMonWH'); if(fSetzHGFarb($sTMonWH,'div.kalTbSpW')) $bNeu=true;
  $sTMonZF=fNewCol('TMonZF'); if(fSetzeFarbe($sTMonZF,'div.kalTbSpT')) $bNeu=true; $sTMonZH=fNewCol('TMonZH'); if(fSetzHGFarb($sTMonZH,'div.kalTbSpT')) $bNeu=true;
  $sTMonXF=fNewCol('TMonXF'); if(fSetzeFarbe($sTMonXF,'div.kalGrey')) $bNeu=true; $sTMonXH=fNewCol('TMonXH'); if(fSetzHGFarb($sTMonXH,'div.kalGrey')) $bNeu=true;
  $sTMDatF=fNewCol('TMDatF'); if(fSetzeFarbe($sTMDatF,'div.kalMDat')) $bNeu=true; $sTMDatH=fNewCol('TMDatH'); if(fSetzHGFarb($sTMDatH,'div.kalMDat')) $bNeu=true;
  $sTMonHF=fNewCol('TMonHF'); if(fSetzeFarbe($sTMonHF,'div.kalMHte')) $bNeu=true; $sTMonHH=fNewCol('TMonHH'); if(fSetzHGFarb($sTMonHH,'div.kalMHte')) $bNeu=true;
  $sTMonFF=fNewCol('TMonFF'); if(fSetzeFarbe($sTMonFF,'div.kalMFtg')) $bNeu=true; $sTMonFH=fNewCol('TMonFH'); if(fSetzHGFarb($sTMonFH,'div.kalMFtg')) $bNeu=true;
  $sAMDatN=fNewCol('AMDatN'); if(fSetzeFarbe($sAMDatN,'a.kalMDat:link')) $bNeu=true; $sAMDaAN=fNewCol('AMDaAN'); if(fSetzeFarbe($sAMDaAN,'a.kalMDat:hover')) $bNeu=true;
  $sAMDatH=fNewCol('AMDatH'); if(fSetzeFarbe($sAMDatH,'a.kalMHte:link')) $bNeu=true; $sAMDaAH=fNewCol('AMDaAH'); if(fSetzeFarbe($sAMDaAH,'a.kalMHte:hover')) $bNeu=true;
  $sAMDatF=fNewCol('AMDatF'); if(fSetzeFarbe($sAMDatF,'a.kalMFtg:link')) $bNeu=true; $sAMDaAF=fNewCol('AMDaAF'); if(fSetzeFarbe($sAMDaAF,'a.kalMFtg:hover')) $bNeu=true;
  $sTMDetF=fNewCol('TMDetF'); if(fSetzeFarbe($sTMDetF,'div.kalMDet')) $bNeu=true; $sTMDetH=fNewCol('TMDetH'); if(fSetzHGFarb($sTMDetH,'div.kalMDet')) $bNeu=true;
  $sAMDetN=fNewCol('AMDetN'); if(fSetzeFarbe($sAMDetN,'a.kalMDet:link')) $bNeu=true; $sAMDeAN=fNewCol('AMDeAN'); if(fSetzeFarbe($sAMDeAN,'a.kalMDet:hover')) $bNeu=true;
  // Vorschaubox
  $sDVBoxR=fNewCol('DVBoxR'); if(fSetzRahmFarb($sDVBoxR,'div#kalVBox')) $bNeu=true;
  $sDVBoxL=$_POST['DVBoxL'];  if(fSetzeRahmArt($sDVBoxL,'div#kalVBox')) $bNeu=true;
  $sDVBoxF=fNewCol('DVBoxF'); if(fSetzeFarbe($sDVBoxF,'div#kalVBox')) $bNeu=true;  $sDVBoxH=fNewCol('DVBoxH'); if(fSetzHGFarb($sDVBoxH,'div#kalVBox')) $bNeu=true;
  // Filterzeile
  $sDFiltR=fNewCol('DFiltR'); if(fSetzRahmFarb($sDFiltR,'div.kalFilt')) $bNeu=true;
  $sDFiltL=$_POST['DFiltL'];  if(fSetzeRahmArt($sDFiltL,'div.kalFilt')) $bNeu=true;
  $sDFiltF=fNewCol('DFiltF'); if(fSetzeFarbe($sDFiltF,'div.kalFilt')) $bNeu=true;  $sDFiltH=fNewCol('DFiltH'); if(fSetzHGFarb($sDFiltH,'div.kalFilt')) $bNeu=true;
  $sIFiltR=fNewCol('IFiltR'); if(fSetzRahmFarb($sIFiltR,'input.kalSFlt')) $bNeu=true; if(fSetzRahmFarb($sIFiltR,'select.kalIFlt')) $bNeu=true;
  $sIFiltL=$_POST['IFiltL'];  if(fSetzeRahmArt($sIFiltL,'input.kalSFlt')) $bNeu=true; if(fSetzeRahmArt($sIFiltL,'select.kalIFlt')) $bNeu=true;
  $sIFiltF=fNewCol('IFiltF'); if(fSetzeFarbe($sIFiltF,'input.kalSFlt')) $bNeu=true;$sIFiltH=fNewCol('IFiltH'); if(fSetzHGFarb($sIFiltH,'input.kalSFlt')) $bNeu=true;
                              if(fSetzeFarbe($sIFiltF,'select.kalIFlt')) $bNeu=true;                           if(fSetzHGFarb($sIFiltH,'select.kalIFlt')) $bNeu=true;
  // Eingaben
  $sIEingR=fNewCol('IEingR'); if(fSetzRahmFarb($sIEingR,'input.kalEing')) $bNeu=true;
                              if(fSetzRahmFarb($sIEingR,'input[type=file].kalEing')) $bNeu=true;
  $sIEingL=$_POST['IEingL'];  if(fSetzeRahmArt($sIEingL,'input.kalEing')) $bNeu=true;
                              if(fSetzeRahmArt($sIEingL,'input[type=file].kalEing')) $bNeu=true;
  $sIEingF=fNewCol('IEingF'); if(fSetzeFarbe($sIEingF,'input.kalEing')) $bNeu=true;
                              if(fSetzeFarbe($sIEingF,'input[type=file].kalEing')) $bNeu=true;
  $sIEingH=fNewCol('IEingH'); if(fSetzHGFarb($sIEingH,'input.kalEing')) $bNeu=true;
  $sDFehlR=fNewCol('DFehlR'); if(fSetzRahmFarb($sDFehlR,'div.kalFhlt')) $bNeu=true;
  $sIEingZ=fTxtSiz('IEingZ'); if(fSetzeHoehe($sIEingZ,'textarea.kalEing',2)) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(KALPFAD.'kalStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Msg='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Msg='<p class="admFehl">In die Datei <i>kalStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Msg) $Msg='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
  $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNw2=false;
  $v=(int)txtVar('ListenKateg'); if(fSetzKalWert(($v?true:false),'ListenKateg','')) $bNw2=true;
  $v=(int)txtVar('MonatsKateg'); if(fSetzKalWert(($v?true:false),'MonatsKateg','')) $bNw2=true;
  if($v&&$bNw2&&KAL_MonZusagHGBl=='Det') fSetzKalWert('Dat','MonZusagHGBl',"'"); // Hintergrund umschalten
  if($bNw2){//Speichern
   if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    if(!$bNeu) $Msg='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Msg.='<p class="admFehl">In die Datei <i>kalWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
  }
 }//POST
}else $Msg.='<p class="admFehl">Setup-Fehler: Die Datei <i>kalStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die wesentlichen Farbeinstellungen.</p>';
echo $Msg.NL;
$sIcon=$sHttp.'grafik/icon_Aendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<p>Die folgenden Farben sowie Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> editieren.</p>
<p class="admMini"><u>Hinweis</u>: Über diese Formulare sind nach wie vor wesentliche Elemente des Aussehens der Kalender-Scripts einstellbar, jedoch nicht mehr alle Details der neuen CSS-Layouts ab Herbst 2022. Für Feinheiten ist eventuell Handarbeit in der CSS-Datei <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> kalStyles.css</a> notwendig.</p>
<form name="farbform" action="konfFarben.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern das Kalender-Script <i>eigenständig</i> läuft und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die <b>Gesamtbreite</b> der Ausgabe des Kalender-Scripts wird durch eine unsichtbare Box festgelegt. Diese Box nimmt die volle zur Verfügung stehende Anzeigebreite in Anspruch (98%), kann aber auf eine absolute Höchstbreite begrenzt werden.</td></tr>
<tr class="admTabl">
 <td>max. Ausgabebreite</td>
 <td colspan="3"><input type="text" name="TBoxW" value="<?php echo $sTBoxW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> oder <i>%</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 600...1000px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die <b>Basisschriftgröße</b> des Kalender-Scripts wird ebenfalls in dieser unsichtbare Box festgelegt. Es kann eine Schriftgröße für schmale Displays und eine für breite Display gesetzt werden.</td></tr>
<tr class="admTabl">
 <td>Schriftgröße auf schmalen Displays</td>
 <td colspan="3"><input type="text" name="TBoxF" value="<?php echo $sTBoxF?>" style="width:70px"> (Maßeinheit <i>%</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: ca. 75%</td>
</tr>
<tr class="admTabl">
 <td>Schriftgröße auf breiten Displays</td>
 <td colspan="3"><input type="text" name="TBoxG" value="<?php echo $sTBoxG?>" style="width:70px"> (Maßeinheit <i>%</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: ca. 80%</td>
</tr>
<tr class="admTabl">
 <td>Umschaltschwelle<br>in der Displaybreite</td>
 <td colspan="3"><input type="text" name="TBoxS" value="<?php echo $sTBoxS?>" style="width:70px"> (Maßeinheit <i>em</i> oder <i>px</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: ca. 50em<br>(50 em entspricht ca. 800px)</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Für <b>Meldungstexte</b> über den Formularen und Listen des Kalenders werden folgende Farben verwendet:</td></tr>
<tr class="admTabl">
 <td>Meldungstextfarbe</td>
 <td colspan="2"><input type="text" name="PMeld" value="<?php echo $sPMeld?>" style="width:70px">
 <a href="<?php echo fColorRef('PMeld')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPMeld?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #000000 (schwarz)</td>
</tr>
<tr class="admTabl">
 <td>Erfolgstextfarbe</td>
 <td colspan="2"><input type="text" name="PErfo" value="<?php echo $sPErfo?>" style="width:70px">
 <a href="<?php echo fColorRef('PErfo')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPErfo?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #008800 (grün)</td>
</tr>
<tr class="admTabl">
 <td>Fehlertextfarbe</td>
 <td colspan="2"><input type="text" name="PFehl" value="<?php echo $sPFehl?>" style="width:70px">
 <a href="<?php echo fColorRef('PFehl')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPFehl?>;background-color:#F7F7F7;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #bb0033 (rot)</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Die obere <b>Navigationszeile</b> mit Links wie '[Liste] [Drucken] [Suchen]' über dem Kalender hat das Aussehen:</td></tr>
<tr class="admTabl">
 <td>Link (normal)</td>
 <td><input type="text" name="AMnuOF" value="<?php echo $sAMnuOF?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOF')?>"><img src="<?php echo $sIcon?>"></a> Linkfarbe</td>
 <td><input type="text" name="AMnuOH" value="<?php echo $sAMnuOH?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center" rowspan="4"><?php echo fMusterLink($sAMnuOF,$sAMnuOA,$sAMnuOH,$sAMnuOI,$sAMnuOR)?></td>
 <td class="admMini">Empfehlung: blau</td>
</tr>
<tr class="admTabl">
 <td>Link (aktiv)</td>
 <td><input type="text" name="AMnuOA" value="<?php echo $sAMnuOA?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOA')?>"><img src="<?php echo $sIcon?>"></a> Linkfarbe</td>
 <td><input type="text" name="AMnuOI" value="<?php echo $sAMnuOI?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOI')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td class="admMini">Empfehlung: rot</td>
</tr>
<tr class="admTabl">
 <td>Umrahmung</td>
 <td><select name="AMnuOL" style="width:8.4em" size="1"><?php echo fRahmenArten($sAMnuOL)?></select></td>
 <td><input type="text" name="AMnuOR" value="<?php echo $sAMnuOR?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td>Linktrennung</td>
 <td><input type="text" name="AMnuOV" value="<?php echo $sAMnuOV?>" style="width:2em"> Zeichen vor dem Link</td>
 <td><input type="text" name="AMnuON" value="<?php echo $sAMnuON?>" style="width:2em"> Zeichen nach dem Link</td>
 <td class="admMini">Empfehlung [ ] oder leer</td>
</tr>
<tr class="admTabl">
 <td>Umhüllung</td>
 <td><input type="text" name="AMnuOZ" value="<?php echo $sAMnuOZ?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOZ')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td><input type="text" name="AMnuOB" value="<?php echo $sAMnuOB?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuOB')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center"><?php echo fMusterRahmen('#000000',$sAMnuOZ,$sAMnuOB)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Die untere <b>Navigationszeile</b> mit Links wie '[Liste] [Drucken] [Suchen]' unter dem Kalender hat das Aussehen:</td></tr>
<tr class="admTabl">
 <td>Link (normal)</td>
 <td><input type="text" name="AMnuUF" value="<?php echo $sAMnuUF?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUF')?>"><img src="<?php echo $sIcon?>"></a> Linkfarbe</td>
 <td><input type="text" name="AMnuUH" value="<?php echo $sAMnuUH?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center" rowspan="4"><?php echo fMusterLink($sAMnuUF,$sAMnuUA,$sAMnuUH,$sAMnuUI,$sAMnuUR)?></td>
 <td class="admMini">Empfehlung: blau</td>
</tr>
<tr class="admTabl">
 <td>Link (aktiv)</td>
 <td><input type="text" name="AMnuUA" value="<?php echo $sAMnuUA?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUA')?>"><img src="<?php echo $sIcon?>"></a> Linkfarbe</td>
 <td><input type="text" name="AMnuUI" value="<?php echo $sAMnuUI?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUI')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td class="admMini">Empfehlung: rot</td>
</tr>
<tr class="admTabl">
 <td>Umrahmung</td>
 <td><select name="AMnuUL" style="width:8.4em" size="1"><?php echo fRahmenArten($sAMnuUL)?></select></td>
 <td><input type="text" name="AMnuUR" value="<?php echo $sAMnuUR?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td>Linktrennung</td>
 <td><input type="text" name="AMnuUV" value="<?php echo $sAMnuUV?>" style="width:2em"> Zeichen vor dem Link</td>
 <td><input type="text" name="AMnuUN" value="<?php echo $sAMnuUN?>" style="width:2em"> Zeichen nach dem Link</td>
 <td class="admMini">Empfehlung [ ] oder leer</td>
</tr>
<tr class="admTabl">
 <td>Umhüllung</td>
 <td><input type="text" name="AMnuUZ" value="<?php echo $sAMnuUZ?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUZ')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td><input type="text" name="AMnuUB" value="<?php echo $sAMnuUB?>" style="width:70px"> <a href="<?php echo fColorRef('AMnuUB')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center"><?php echo fMusterRahmen('#000000',$sAMnuUZ,$sAMnuUB)?></td>
 <td class="admMini">&nbsp;</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Über oder unter der <i>Terminliste</i> kann eine <b>Navigationszeile</b> mit Links zum Blättern '|&lt; 1 2 3 4 5 &gt;|' angezeigt werden.</td></tr>
<tr class="admTabl">
 <td>Linkfarbe</td>
 <td><input type="text" name="NavLnk" value="<?php echo $sNavLnk?>" style="width:70px"> <a href="<?php echo fColorRef('NavLnk')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="NavLkA" value="<?php echo $sNavLkA?>" style="width:70px"> <a href="<?php echo fColorRef('NavLkA')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td rowspan="3" align="center"><?php echo fMusterLink($sNavLnk,$sNavLkA,$sNaviHg,$sNaviHg,$sNaviRF)?></td>
 <td class="admMini">Empfehlung: schwarz/rot</td>
</tr>
<tr class="admTabl">
 <td>Linkrahmen</td>
 <td><select name="NaviRA" style="width:8.4em" size="1"><?php echo fRahmenArten($sNaviRA)?></select></td>
 <td><input type="text" name="NaviRF" value="<?php echo $sNaviRF?>" style="width:70px"> <a href="<?php echo fColorRef('NaviRF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">Empfehlung: kein Rahmen</td>
</tr>
<tr class="admTabl">
 <td>Linkhintergrund</td>
 <td style="white-space:nowrap"><input type="text" name="NaviHg" value="<?php echo $sNaviHg?>" style="width:70px"> <a href="<?php echo fColorRef('NaviHg')?>"><img src="<?php echo $sIcon?>"></a> falls sichtbar</td>
 <td style="white-space:nowrap">oder <input class="admCheck" type="checkbox" name="NavHgI" value="a"<?php if($sNavHgI=='a') echo ' checked="checked"'?> /> Hintergrundgrafik <img style="vertical-align:-5px" src="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>/grafik/naviRechts.gif" width="20" height="20" border="0" alt=""></td>
 <td class="admMini">Empfehlung: je nach Layoutstil</td>
</tr>
<tr class="admTabl">
 <td>gesamte<br />Navigationszeile</td>
 <td><input type="text" name="NavLHg" value="<?php echo $sNavLHg?>" style="width:70px"> <a href="<?php echo fColorRef('NavLHg')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td><input type="text" name="NavLSz" value="<?php echo $sNavLSz?>" style="width:70px"> <a href="<?php echo fColorRef('NavLSz')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td rowspan="2" align="center"><?php echo fMusterLink($sNavLSz,$sNavLSz,($sNavLHg!='transparent'?$sNavLHg:'#efefef'),($sNavLHg!='transparent'?$sNavLHg:'#efefef'),$sNavLRF)?></td>
 <td class="admMini">Textfarbe wird beim Seitenzähler verwendet</td>
</tr>
<tr class="admTabl">
 <td>Zeilenrahmen</td>
 <td><select name="NavLRA" style="width:8.4em" size="1"><?php echo fRahmenArten($sNavLRA)?></select></td>
 <td><input type="text" name="NavLRF" value="<?php echo $sNavLRF?>" style="width:70px"> <a href="<?php echo fColorRef('NavLRF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">Empfehlung: kein Rahmen</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Auf den Seiten <i>Termindetails</i> und <i>Monatsblatt</i> kann eine <b>Navigationszeile</b> mit Links zum Blättern '&lt;&lt; &nbsp; &gt;&gt;' angezeigt werden.</td></tr>
<tr class="admTabl">
 <td>Linkfarbe</td>
 <td><input type="text" name="DNvLnk" value="<?php echo $sDNvLnk?>" style="width:70px"> <a href="<?php echo fColorRef('DNvLnk')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="DNvLkA" value="<?php echo $sDNvLkA?>" style="width:70px"> <a href="<?php echo fColorRef('DNvLkA')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td rowspan="3" align="center"><?php echo fMusterLink($sDNvLnk,$sDNvLkA,$sDNviHg,$sDNviHg,$sDNviRF)?></td>
 <td class="admMini">Empfehlung: schwarz/rot</td>
</tr>
<tr class="admTabl">
 <td>Linkrahmen</td>
 <td><select name="DNviRA" style="width:8.4em" size="1"><?php echo fRahmenArten($sDNviRA)?></select></td>
 <td><input type="text" name="DNviRF" value="<?php echo $sDNviRF?>" style="width:70px"> <a href="<?php echo fColorRef('DNviRF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">Empfehlung: kein Rahmen</td>
</tr>
<tr class="admTabl">
 <td>Linkhintergrund</td>
 <td style="white-space:nowrap"><input type="text" name="DNviHg" value="<?php echo $sDNviHg?>" style="width:70px"> <a href="<?php echo fColorRef('DNviHg')?>"><img src="<?php echo $sIcon?>"></a> falls sichtbar</td>
 <td style="white-space:nowrap">oder <input class="admCheck" type="checkbox" name="DNvHgI" value="a"<?php if($sDNvHgI=='a') echo ' checked="checked"'?> /> Hintergrundgrafik <img style="vertical-align:-5px" src="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>/grafik/naviRechts.gif" width="20" height="20" border="0" alt=""><?php if(file_exists(KALPFAD.'grafik/pfeilR.png')){ ?> bzw. <img style="vertical-align:-4px;padding:3px;border:1px solid #999999;" src="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo KAL_Www?>/grafik/pfeilR.png" width="17" height="10" border="0" alt=""><?php }?></td>
 <td class="admMini">Empfehlung: je nach Layoutstil</td>
</tr>
<tr class="admTabl">
 <td>gesamte<br />Navigationszeile</td>
 <td><input type="text" name="DNvLHg" value="<?php echo $sDNvLHg?>" style="width:70px"> <a href="<?php echo fColorRef('DNvLHg')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td><input type="text" name="DNvLSz" value="<?php echo $sDNvLSz?>" style="width:70px"> <a href="<?php echo fColorRef('DNvLSz')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td rowspan="2" align="center"><?php echo fMusterLink($sDNvLSz,$sDNvLSz,($sDNvLHg!='transparent'?$sDNvLHg:'#efefef'),($sDNvLHg!='transparent'?$sDNvLHg:'#efefef'),$sDNvLRF)?></td>
 <td class="admMini">Textfarbe wird beim Seitenzähler verwendet</td>
</tr>
<tr class="admTabl">
 <td>Zeilenrahmen</td>
 <td><select name="DNvLRA" style="width:8.4em" size="1"><?php echo fRahmenArten($sDNvLRA)?></select></td>
 <td><input type="text" name="DNvLRF" value="<?php echo $sDNvLRF?>" style="width:70px"> <a href="<?php echo fColorRef('DNvLRF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td class="admMini">Empfehlung: kein Rahmen</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Im Kalender-Script werden auf jeder Seite <b>Tabellen</b> zur Darstellung des Hauptinhaltes verwendet.</td></tr>
<tr class="admTabl">
 <td>Tabellenrahmen</td>
 <td><select name="TablR" style="width:8.4em" size="1"><?php echo fRahmenArten($sTablR)?></select></td>
 <td style="white-space:nowrap"><input type="text" name="TablF" value="<?php echo $sTablF?>" style="width:70px"> <a href="<?php echo fColorRef('TablF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1" style="margin-left:auto;margin-right:auto;"><tr><td style="color:<?php echo $sTablF;?>;background-color:#eee;border:1px <?php echo $sTablR.' '.$sTablF;?>">Rahmen</td></tr></table></td>
 <td class="admMini">Empfehlung: Rahmen</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Die Termine in der <b>Terminliste</b> werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Terminfelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Terminliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 500...800px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In der <b>Terminliste</b> werden folgende Farben verwendet.</td></tr>
<tr class="admTabl">
 <td>Tabellenkopfzeile</td>
 <td><input type="text" name="TKopfF" value="<?php echo $sTKopfF?>" style="width:70px"> <a href="<?php echo fColorRef('TKopfF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKopfH" value="<?php echo $sTKopfH?>" style="width:70px"> <a href="<?php echo fColorRef('TKopfH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKopfF,$sTKopfH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td style="white-space:nowrap;width:1%;">Datenzeile ungerade</td>
 <td><input type="text" name="TbZl1F" value="<?php echo $sTbZl1F?>" style="width:70px"> <a href="<?php echo fColorRef('TbZl1F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TbZl1H" value="<?php echo $sTbZl1H?>" style="width:70px"> <a href="<?php echo fColorRef('TbZl1H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTbZl1F,$sTbZl1H)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Datenzeile gerade</td>
 <td><input type="text" name="TbZl2F" value="<?php echo $sTbZl2F?>" style="width:70px"> <a href="<?php echo fColorRef('TbZl2F')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TbZl2H" value="<?php echo $sTbZl2H?>" style="width:70px"> <a href="<?php echo fColorRef('TbZl2H')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTbZl2F,$sTbZl2H)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>laufendes Ereignis</td>
 <td><input type="text" name="TLfndF" value="<?php echo $sTLfndF?>" style="width:70px"> <a href="<?php echo fColorRef('TLfndF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TLfndH" value="<?php echo $sTLfndH?>" style="width:70px"> <a href="<?php echo fColorRef('TLfndH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTLfndF,$sTLfndH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>aktuelles Ereignis</td>
 <td><input type="text" name="TAktuF" value="<?php echo $sTAktuF?>" style="width:70px"> <a href="<?php echo fColorRef('TAktuF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TAktuH" value="<?php echo $sTAktuH?>" style="width:70px"> <a href="<?php echo fColorRef('TAktuH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTAktuF,$sTAktuH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie A</td>
 <td><input type="text" name="TKatAF" value="<?php echo $sTKatAF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatAF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatAH" value="<?php echo $sTKatAH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatAH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatAF,$sTKatAH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie B</td>
 <td><input type="text" name="TKatBF" value="<?php echo $sTKatBF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatBF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatBH" value="<?php echo $sTKatBH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatBH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatBF,$sTKatBH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie C</td>
 <td><input type="text" name="TKatCF" value="<?php echo $sTKatCF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatCF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatCH" value="<?php echo $sTKatCH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatCH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatCF,$sTKatCH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie D</td>
 <td><input type="text" name="TKatDF" value="<?php echo $sTKatDF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatDF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatDH" value="<?php echo $sTKatDH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatDH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatDF,$sTKatDH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie E</td>
 <td><input type="text" name="TKatEF" value="<?php echo $sTKatEF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatEF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatEH" value="<?php echo $sTKatEH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatEH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatEF,$sTKatEH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie F</td>
 <td><input type="text" name="TKatFF" value="<?php echo $sTKatFF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatFF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatFH" value="<?php echo $sTKatFH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatFH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatFF,$sTKatFH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Terminkategorie G</td>
 <td><input type="text" name="TKatGF" value="<?php echo $sTKatGF?>" style="width:70px"> <a href="<?php echo fColorRef('TKatGF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TKatGH" value="<?php echo $sTKatGH?>" style="width:70px"> <a href="<?php echo fColorRef('TKatGH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTKatGF,$sTKatGH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl">
 <td>&nbsp;</td><td colspan="4"><input class="admCheck" type="checkbox" name="ListenKateg" value="1<?php if($ksListenKateg) echo '" checked="checked'?>" /> die Kategoriefarben sollen verwendet werden</td>
</tr>
<tr class="admTabl"><td colspan="5"><div class="admMini">Noch mehr Terminkategorien bitte von Hand direkt in der <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> CSS-Datei</a> im Abschnitt <i>weiterer Terminkategorien</i> ändern und ergänzen!</div></td></tr>

<tr class="admTabl">
 <td>Links zu den<br>Termindetails</td>
 <td><input type="text" name="ADetl" value="<?php echo $sADetl?>" style="width:70px"> <a href="<?php echo fColorRef('ADetl')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="ADetA" value="<?php echo $sADetA?>" style="width:70px"> <a href="<?php echo fColorRef('ADetA')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sADetl,$sADetA,$sTbZl1H)?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Zwischen den Monaten/Wochen/Tagen der Terminliste kann eine Trennzeile eingefügt sein.</td></tr>
<tr class="admTabl">
 <td>Trennzeile</td>
 <td><input type="text" name="TZlTrnF" value="<?php echo $sTZlTrnF?>" style="width:70px"> <a href="<?php echo fColorRef('TZlTrnF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TZlTrnH" value="<?php echo $sTZlTrnH?>" style="width:70px"> <a href="<?php echo fColorRef('TZlTrnH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTZlTrnF,$sTZlTrnH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Eventuelle Vorschaubilder in der Terminliste können unter dem Menüpunkt <i>Eingabeformular</i> mit einem Rahmen versehen werden. Falls dieser Rahmen aktiviert wurde, kann er formatiert werden. </td></tr>
<tr class="admTabl">
 <td>Bilderrahmen</td>
 <td><select name="VBldR" style="width:8.4em" size="1"><?php echo fRahmenArten($sVBldR)?></select></td>
 <td style="white-space:nowrap"><input type="text" name="VBldF" value="<?php echo $sVBldF?>" style="width:70px"> <a href="<?php echo fColorRef('VBldF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1" style="margin-left:auto;margin-right:auto;"><tr><td style="color:#bbaabb;background-color:#fff;border:1px <?php echo $sVBldR.' '.$sVBldF;?>">&nbsp;&nbsp; Bild &nbsp;&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Text in Memofeldern in der Terminliste, den Termindetails oder dem Monatsblatt kann Absätze, Aufzählungen und externe Links enthalten.</td></tr>
<tr class="admTabl">
 <td>Textabsätze</td>
 <td colspan="2"><input type="text" name="PText" value="<?php echo $sPText?>" style="width:70px">
 <a href="<?php echo fColorRef('PText')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sPText?>;background-color:#F7F7F7;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #000000 (schwarz)</td>
</tr>
<tr class="admTabl">
 <td>Aufzählungen</td>
 <td colspan="2"><input type="text" name="LText" value="<?php echo $sLText?>" style="width:70px">
 <a href="<?php echo fColorRef('LText')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sLText?>;background-color:#F7F7F7;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: #000000 (schwarz)</td>
</tr>
<tr class="admTabl">
 <td>externe Links</td>
 <td><input type="text" name="AText" value="<?php echo $sAText?>" style="width:70px"> <a href="<?php echo fColorRef('AText')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="ATexA" value="<?php echo $sATexA?>" style="width:70px"> <a href="<?php echo fColorRef('ATexA')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sAText,$sATexA)?></td>
 <td class="admMini">Empfehlung: schwarz/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Die Termine in der <b>Monatsliste</b> werden auf breiten Monitoren als Tabelle mit 7 nebeneinanderliegenden Wochentagen dargestellt. Auf schmalen Displays erscheinen die Wochentage in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Spaltenbreite, von der Schriftgröße und vom Inhalt in Ihrer Monattabelle ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Monatsumschaltung</td>
 <td colspan="3"><input type="text" name="TMonaW" value="<?php echo $sTMonaW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 550...700px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Im <b>Monatskalenderblatt</b> werden folgende Farben verwendet.</td></tr>
<tr class="admTabl">
 <td>Monatskopfzeile</td>
 <td><input type="text" name="TMonKF" value="<?php echo $sTMonKF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonKF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonKH" value="<?php echo $sTMonKH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonKH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonKF,$sTMonKH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>WochenNrSpalte</td>
 <td><input type="text" name="TMonWF" value="<?php echo $sTMonWF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonWF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonWH" value="<?php echo $sTMonWH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonWH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonWF,$sTMonWH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>ganze Tageszelle</td>
 <td><input type="text" name="TMonZF" value="<?php echo $sTMonZF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonZF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonZH" value="<?php echo $sTMonZH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonZH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonZF,$sTMonZH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>monatsfremde<br>Tageszelle</td>
 <td><input type="text" name="TMonXF" value="<?php echo $sTMonXF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonXF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonXH" value="<?php echo $sTMonXH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonXH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonXF,$sTMonXH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>neutrales<br>Tagesdatum</td>
 <td><input type="text" name="TMDatF" value="<?php echo $sTMDatF?>" style="width:70px"> <a href="<?php echo fColorRef('TMDatF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMDatH" value="<?php echo $sTMDatH?>" style="width:70px"> <a href="<?php echo fColorRef('TMDatH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMDatF,($sTMDatH!='transparent'?$sTMDatH:$sTMonZH))?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Tagesdatum-Link<br>auf Termindetail</td>
 <td><input type="text" name="AMDatN" value="<?php echo $sAMDatN?>" style="width:70px"> <a href="<?php echo fColorRef('AMDatN')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="AMDaAN" value="<?php echo $sAMDaAN?>" style="width:70px"> <a href="<?php echo fColorRef('AMDaAN')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sAMDatN,$sAMDaAN,($sTMDatH!='transparent'?$sTMDatH:$sTMonZH))?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
<tr class="admTabl">
 <td>Tagesdatum &quot;Heute&quot;</td>
 <td><input type="text" name="TMonHF" value="<?php echo $sTMonHF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonHF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonHH" value="<?php echo $sTMonHH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonHH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonHF,$sTMonHH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Heutedatum-Link<br>auf Termindetail</td>
 <td><input type="text" name="AMDatH" value="<?php echo $sAMDatH?>" style="width:70px"> <a href="<?php echo fColorRef('AMDatH')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="AMDaAH" value="<?php echo $sAMDaAH?>" style="width:70px"> <a href="<?php echo fColorRef('AMDaAH')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sAMDatH,$sAMDaAH,($sTMonHH!='transparent'?$sTMonHH:$sTMonZH))?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
<tr class="admTabl">
 <td>Tagesdatum &quot;Feiertag&quot;</td>
 <td><input type="text" name="TMonFF" value="<?php echo $sTMonFF?>" style="width:70px"> <a href="<?php echo fColorRef('TMonFF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMonFH" value="<?php echo $sTMonFH?>" style="width:70px"> <a href="<?php echo fColorRef('TMonFH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMonFF,$sTMonFH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Feiertagdatum-Link<br>auf Termindetail</td>
 <td><input type="text" name="AMDatF" value="<?php echo $sAMDatF?>" style="width:70px"> <a href="<?php echo fColorRef('AMDatF')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="AMDaAF" value="<?php echo $sAMDaAF?>" style="width:70px"> <a href="<?php echo fColorRef('AMDaAF')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sAMDatF,$sAMDaAF,($sTMonFH!='transparent'?$sTMonFH:$sTMonZH))?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl">
 <td rowspan="2">Container mit<br>Termindetails</td>
 <td><input type="text" name="TMDetF" value="<?php echo $sTMDetF?>" style="width:70px"> <a href="<?php echo fColorRef('TMDetF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TMDetH" value="<?php echo $sTMDetH?>" style="width:70px"> <a href="<?php echo fColorRef('TMDetH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTMDetF,($sTMDetH!='transparent'?$sTMDetH:$sTMonZH))?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td colspan="4"><input class="admCheck" type="checkbox" name="MonatsKateg" value="1<?php if($ksMonatsKateg) echo '" checked="checked'?>" /> statt dessen die Kategoriefarben aus obiger Terminliste verwenden</td>
</tr>
<tr class="admTabl">
 <td>Detail-Link<br>auf Termindetail</td>
 <td><input type="text" name="AMDetN" value="<?php echo $sAMDetN?>" style="width:70px"> <a href="<?php echo fColorRef('AMDetN')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="AMDeAN" value="<?php echo $sAMDeAN?>" style="width:70px"> <a href="<?php echo fColorRef('AMDeAN')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sAMDetN,$sAMDeAN,($sTMDetH!='transparent'?$sTMDetH:$sTMonZH))?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>
<tr class="admTabl"><td colspan="5"><div class="admMini">Weitere Einstellungen am Monatskalenderblatt bitte von Hand direkt in der <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> CSS-Datei</a> im Abschnitt <i>Monatsliste</i> ändern!</div></td></tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Beim Überfahren eines Termins in der Monatsliste mit der Maus kann eine <b>Vorschaubox</b> engeblendet werden. In der Vorschaubox werden folgende Einstellungen verwendet.</td></tr>
<tr class="admTabl">
 <td>Umrahmung</td>
 <td><select name="DVBoxL" style="width:8.4em" size="1"><?php echo fRahmenArten($sDVBoxL)?></select></td>
 <td><input type="text" name="DVBoxR" value="<?php echo $sDVBoxR?>" style="width:70px"> <a href="<?php echo fColorRef('DVBoxR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center" rowspan="2"><table border="0" cellpadding="0" cellspacing="2" style="background-color:<?php echo $sDVBoxH?>;border-style:<?php echo $sDVBoxL?>;border-color:<?php echo $sDVBoxR?>;border-width:2px;border-radius:6px;"><tr><td style="color:<?php echo $sDVBoxF?>;background-color:<?php echo $sDVBoxH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" rowspan="2">Hinweis: Die Textfarbe im Innen&shy;bereich wird zu&shy;meist vom Box&shy;inhalt über&shy;deckt.</td>
</tr>
<tr class="admTabl">
 <td>Innenbereich</td>
 <td><input type="text" name="DVBoxF" value="<?php echo $sDVBoxF?>" style="width:70px"> <a href="<?php echo fColorRef('DVBoxF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="DVBoxH" value="<?php echo $sDVBoxH?>" style="width:70px"> <a href="<?php echo fColorRef('DVBoxH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
</tr>
<tr class="admTabl"><td colspan="5"><div class="admMini"><u>Hinweis</u>: Die konkreten Farben für die Darstellung der Detailzeilen in der Vorschaubox werden aus den <i>Datenzeilen gerade/ungerade</i> der Terminliste übernommen</div><div class="admMini">Weitere Einstellungen an der Vorschaubox bitte von Hand direkt in der <a href="konfCss.php"><img src="<?php echo $sHttp?>grafik/icon_Aendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> CSS-Datei</a> ab der Stelle <i>div#kalVBox</i> ändern!</div></td></tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Über der <i>Terminliste</i> und über dem <i>Monatskalenderblatt</i> kann eine <b>Filterzeile</b> mit einem Intervallfilter und/oder einem Suchfilter erscheinen.</td></tr>
<tr class="admTabl">
 <td>Umrahmung</td>
 <td><select name="DFiltL" style="width:8.4em" size="1"><?php echo fRahmenArten($sDFiltL)?></select></td>
 <td><input type="text" name="DFiltR" value="<?php echo $sDFiltR?>" style="width:70px"> <a href="<?php echo fColorRef('DFiltR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center" rowspan="2"><table border="0" cellpadding="0" cellspacing="2" style="background-color:<?php echo $sDFiltH?>;border-style:<?php echo $sDFiltL?>;border-color:<?php echo $sDFiltR?>;border-width:1px;"><tr><td style="color:<?php echo $sDFiltF?>;background-color:<?php echo $sDFiltH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" rowspan="2">Hinweis: Der Rahmen sollte meist unsichtbar sein, der Hintergrund transparent.</td>
</tr>
<tr class="admTabl">
 <td>Innenbereich</td>
 <td><input type="text" name="DFiltF" value="<?php echo $sDFiltF?>" style="width:70px"> <a href="<?php echo fColorRef('DFiltF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="DFiltH" value="<?php echo $sDFiltH?>" style="width:70px"> <a href="<?php echo fColorRef('DFiltH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
</tr>
<tr class="admTabl">
 <td>Rahmen der Felder</td>
 <td><select name="IFiltL" style="width:8.4em" size="1"><?php echo fRahmenArten($sIFiltL)?></select></td>
 <td><input type="text" name="IFiltR" value="<?php echo $sIFiltR?>" style="width:70px"> <a href="<?php echo fColorRef('IFiltR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center" rowspan="2"><table border="0" cellpadding="0" cellspacing="2" style="background-color:<?php echo $sIFiltH?>;border-style:<?php echo $sIFiltL?>;border-color:<?php echo $sIFiltR?>;border-width:1px;"><tr><td style="color:<?php echo $sIFiltF?>;background-color:<?php echo $sIFiltH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" rowspan="2">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td>Farben der Felder</td>
 <td><input type="text" name="IFiltF" value="<?php echo $sIFiltF?>" style="width:70px"> <a href="<?php echo fColorRef('IFiltF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="IFiltH" value="<?php echo $sIFiltH?>" style="width:70px"> <a href="<?php echo fColorRef('IFiltH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">In den Eingabeformularen werden für die <b>Eingabefelder</b> folgende Einstellungen verwendet.</td></tr>
<tr class="admTabl">
 <td>Rahmen der Felder</td>
 <td><select name="IEingL" style="width:8.4em" size="1"><?php echo fRahmenArten($sIEingL)?></select></td>
 <td><input type="text" name="IEingR" value="<?php echo $sIEingR?>" style="width:70px"> <a href="<?php echo fColorRef('IEingR')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td align="center" rowspan="2"><table border="0" cellpadding="0" cellspacing="2" style="background-color:<?php echo $sIEingH?>;border-style:<?php echo $sIEingL?>;border-color:<?php echo $sIEingR?>;border-width:1px;"><tr><td style="color:<?php echo $sIEingF?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini" rowspan="2">&nbsp;</td>
</tr>
<tr class="admTabl">
 <td>Farben der Felder</td>
 <td><input type="text" name="IEingF" value="<?php echo $sIEingF?>" style="width:70px"> <a href="<?php echo fColorRef('IEingF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="IEingH" value="<?php echo $sIEingH?>" style="width:70px"> <a href="<?php echo fColorRef('IEingH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
</tr>

<tr class="admTabl">
 <td>Eingabefehler</td>
 <td colspan="2"><input type="text" name="DFehlR" value="<?php echo $sDFehlR?>" style="width:70px"> <a href="<?php echo fColorRef('DFehlR')?>"><img src="<?php echo $sIcon?>"></a> Farbrahmen bei Eingabefehlern</td>
 <td align="center"><table bgcolor="<?php echo $sDFehlR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sIEingF?>;background-color:<?php echo $sIEingH?>;">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: rot (#bb0066)</td>
</tr>
<tr class="admTabl">
 <td>Memofeldhöhe</td>
 <td colspan="3"><input type="text" name="IEingZ" value="<?php echo $sIEingZ?>" style="width:70px"> (Masseinheit in <i>px</i> oder <i>em</i> mit angeben!)</td>
 <td class="admMini">Empfehlung: 5...10em</td>
</tr>
<tr class="admTabl"><td colspan="5">Einstellungen zum Captcha nehmen Sie unter <a href="konfAllgemein.php">Allgemeines</a> vor.</td></tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Formatierte <b>E-Mails</b> sollen in folgender Grundfarbe versendet werden.</td></tr>
<tr class="admTabl">
 <td>E-Mail Grundfarbe</td>
 <td><input type="text" name="MailF" value="<?php echo $sMailF?>" style="width:70px"> <a href="<?php echo fColorRef('MailF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="MailH" value="<?php echo $sMailH?>" style="width:70px"> <a href="<?php echo fColorRef('MailH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sMailF,$sMailH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function fLiesFarbe($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1); $q+=5; $z=strpos($sCss,';',$q);
   if($q>5&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFarbe($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'color',$p); while(substr($sCss,$q-1,1)=='-') $q=strpos($sCss,'color',$q+1);
   $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesHGFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   if($q=strpos($sCss,'background-color',$p)) $q+=16; $z=strpos($sCss,';',$q);
   if($q>16&&$e>$q&&$z>$q&&$z<$e){
    if(($p=strpos($sCss,'#',$q))&&$e>$p) return substr($sCss,$p,min(7,$z-$p));
    elseif(($p=strpos($sCss,'transparent',$q))&&$e>$p) return 'transparent';
    else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzHGFarb($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $v=':'.$v;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-color',$p); $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q+1),$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p&&$e>$z){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmFarb($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e);
      if($e<$p+14) return trim(substr($sCss,$p,$e-$p)); else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarb($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarbB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzRahmFarbL($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'border-left',$p);
   if($p=strpos($sCss,'px ',$q)){
    if($p=strpos($sCss,' ',$p+5)){
     if($q>0&&$p>$q&&$e>$p&&$e>$q){
      $e=min(strpos($sCss,';',$p),$e); $v=' '.$v;
      if($e<$p+14){
       if(substr($sCss,$p,strlen($v))!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
      }else return false;
     }else return false;
    }else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesRahmArt($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p) return substr($sCss,$p,$l); else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArt($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border',$p); while(substr($sCss,$q,12)=='border-colla') $q=strpos($sCss,'border',$q+1);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArtB($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border-bottom',$p);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeRahmArtL($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1); $l=0;
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p);
   $q=strpos($sCss,'border-left',$p);
   if($p=strpos($sCss,'px ',$q)){$p+=3; $l=strpos($sCss,' ',$p); $l=$l-$p;}
   if($q>0&&$p>$q&&$e>$p){
    if(substr($sCss,$p,$l)!=$v){$sCss=substr_replace($sCss,$v,$p,$l); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesMaxWeite($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'max-width',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeMaxWeite($v,$w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'max-width',$p); $z=strpos($sCss,';',$q); $p=min(strpos($sCss,':',$q)+1,$z);
   if($q>0&&$p>$q&&$e>$p&&$z>=$p){
    if(substr($sCss,$p,$z-$p)!=$v){$sCss=substr_replace($sCss,$v.';',$p,$z-$p+1); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesHoehe($w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'height',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeHoehe($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'height',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}

function fLiesFontSize($w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e; return trim(substr($sCss,$p,min($q,$e)-$p));
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeFontSize($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'font-size',$p); $p=strpos($sCss,':',$q)+1;
   if($q>0&&$p>$q&&$e>$p){
    if(!$q=strpos($sCss,';',$p)) $q=$e;
    if(substr($sCss,$p,min($q,$e)-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,min($q,$e)-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}

function fLiesHgImg($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-image',$p);
   if($q>0&&$e>$q&&$e>$p){
    if(strpos(substr($sCss,$q-8,9),'/*')){
     if(($q=strpos($sCss,';',$q))&&$e>$q){
      $p=strpos(substr($sCss,$q,9),'*/'); if($p>0&&$e>$p) return 'p'; else return false;
     }else return false;
    }else return 'a';
   }else return false;
  }else return false;
 }else return false;
}
function fSetzeHgImg($w,$b=true){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $c=substr($sCss,$p+strlen($w),1);
  if($c=='{'||$c==','||$c==' '||$c=="\n"||$c==':'){
   $e=strpos($sCss,'}',$p); $q=strpos($sCss,'background-image',$p);
   if($q>0&&$e>$q&&$e>$p){
    if($b){ // einschalten
     if(($p=strpos($sCss,';',$q))&&$e>$p){
      if(($d=strpos($sCss,'*/',$p))&&$d<$p+9){
       $sCss=substr_replace($sCss,';',$p,$d-$p+2);
       if(($p=strpos($sCss,'/*',$q-9))&&$q>$p){
        $sCss=substr_replace($sCss,'',$p,$q-$p); return true;
       }else return false;
      }else return false;
     }else return false;
    }else{ // ausschalten
     if(($p=strpos($sCss,';',$q))&&$e>$p){
      if(!strpos(substr($sCss,$q-8,9),'/*')&&(!($d=strpos($sCss,'*/',$p))||$d>$p+9)){
       $sCss=substr_replace($sCss,'; */',$p,1);
       $sCss=substr_replace($sCss,'/* ',$q,0);
      }else return false;
     }else return false;
    }
   }else return false;
  }else return false;
 }else return false;
}
function fLiesScreenW($w){
 global $sCss;
 if($p=strpos($sCss,$w)) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p&&$q>$e) return str_replace(' ','',trim(substr($sCss,++$p,$e-$p))); else return false;
  }else return false;
 }else return false;
}
function fSetzScreenW($v,$w,$n=1){
 global $sCss;
 $p=0; while(($n--)>0) $p=strpos($sCss,$w,$p+1);
 if($p) if($p=strpos($sCss,'media screen and (min-width',$p)){ //Startposition
  if($p=strpos($sCss,':',$p)){
   $e=strpos($sCss,')',$p); $q=strpos($sCss,'{',$p);
   if($e>$p++&&$q>$e){
    if(substr($sCss,$p,$e-$p)!=$v){$sCss=substr_replace($sCss,$v,$p,$e-$p); return true;}else return false;
   }else return false;
  }else return false;
 }else return false;
}
function fLiesContent($w){
 global $sCss;
 if($p=strpos($sCss,$w)){
  $q=strpos($sCss,'content',$p); $q=strpos($sCss,':',$q); $e=min(strpos($sCss,';',$q),strpos($sCss,'}',$p));
  if($q>$p&&$e>$q) return str_replace("'",'',str_replace('"','',trim(substr($sCss,++$q,$e-$q))));
  else return false;
 }else return false;
}
function fSetzContent($w,$v){
 global $sCss; $v="'".trim(str_replace('{','[',str_replace('}',']',str_replace(':','|',str_replace(';','|',str_replace("'",'',str_replace('"','',$v)))))))."'";
 if($p=strpos($sCss,$w)){
  $q=strpos($sCss,'content',$p); $q=strpos($sCss,':',$q); $e=min(strpos($sCss,';',$q),strpos($sCss,'}',$p));
  if($q>$p&&$e>$q){
   if(trim(substr($sCss,++$q,$e-$q))==$v) return false;
   else{$sCss=substr_replace($sCss,$v.(substr($sCss,$e,1)!=';'?';':''),$q,$e-$q); return true;}
  }else return false;
 }else return false;
}
function fColorRef($n){$s=$GLOBALS['s'.$n]; return 'colors.php?col='.($s!='transparent'?substr($s,1):$s).'&fld='.$n.'" target="color" onClick="javascript:ColWin()';}
function fMusterLink($c,$h,$b='#ffffff',$i='#ffffff',$r='#cccccc'){return '<table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$c.';background-color:'.$b.';border:1px solid '.$r.'" onmouseover="this.style.color=\''.$h.'\';this.style.backgroundColor=\''.$i.'\';" onmouseout="this.style.color=\''.$c.'\';this.style.backgroundColor=\''.$b.'\';">&nbsp;Muster&nbsp;</td></tr></table>';}
function fMusterFeld($n,$h){return '<table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:'.$h.';">&nbsp;Muster&nbsp;</td></tr></table>';}
function fMusterRahmen($n='#000000',$h='#eeeeee',$r='#cccccc'){return '<table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:'.$n.';background-color:'.$h.';border:1px solid '.$r.'">&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;</td></tr></table>';}
function fRahmenArten($s){
 return '<option value="none">unsichtbar</option><option value="solid"'.($s!='solid'?'':' selected="selected"').'>volle Linie</option><option value="dotted"'.($s!='dotted'?'':' selected="selected"').'>gepunktet</option><option value="dashed"'.($s!='dashed'?'':' selected="selected"').'>gestrichelt</option>';
}
function fNewCol($Var){
 $s=(isset($_POST[$Var])?strtolower(str_replace('"',"'",stripslashes(trim($_POST[$Var])))):'');
 if(strlen($s)>0&&$s!='transparent'){if(substr($s,0,1)!='#') $s='#'.$s; while(strlen($s)<7) $s.='0';}
 return $s;
}
function fTxtSiz($Var){return (isset($_POST[$Var])?strtolower(str_replace('"',"'",str_replace(',','.',str_replace(' ','',stripslashes(trim($_POST[$Var])))))):'');}
?>