<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Farbeinstellungen','<script type="text/javascript">
 function ColWin(){colWin=window.open("about:blank","color","width=280,height=380,left=5,top=5,menubar=no,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");colWin.focus();}
</script>
','KFa');

if(file_exists(MPPFAD.'mpStyles.css')){
 $sCss=str_replace("\r",'',trim(implode('',file(MPPFAD.'mpStyles.css')))); $bNeu=false;
 $sNavHgI=fLiesHgImg('ul.mpNavi li'); $sDNvHgI=fLiesHgImg('a.mpDetR'); // muss auch bei POST bleiben wegen Vergleich
 if($_SERVER['REQUEST_METHOD']=='GET'){
  $sPageH=fLiesHGFarb('body.mpSeite');
  $sTBoxW=fLiesMaxWeite('div.mpBox');
  $sTBoxF=fLiesFontSize('div.mpBox');
  $sTBoxG=fLiesFontSize('div.mpBox',2);
  $sTBoxS=fLiesScreenW('gesamte Ausgabe');
  $sTListW=fLiesScreenW('Inserateliste mit Spalten');
  $sPMeld=fLiesFarbe('p.mpMeld'); $sPErfo=fLiesFarbe('p.mpErfo'); $sPFehl=fLiesFarbe('p.mpFehl');
  //Aktivitaetslinks
  $sAMnuOF=fLiesFarbe('ul.mpMnuO a');       $sAMnuOH=fLiesHGFarb('ul.mpMnuO a');
  $sAMnuOA=fLiesFarbe('ul.mpMnuO a:hover'); $sAMnuOI=fLiesHGFarb('ul.mpMnuO a:hover');
  $sAMnuOR=fLiesRahmFarb('ul.mpMnuO li');   $sAMnuOL=fLiesRahmArt('ul.mpMnuO li');
  $sAMnuOV=fLiesContent('ul.mpMnuO a::before'); $sAMnuON=fLiesContent('ul.mpMnuO a::after');
  $sAMnuOZ=fLiesHGFarb('ul.mpMnuO');        $sAMnuOB=fLiesRahmFarb('ul.mpMnuO');
  $sAMnuUF=fLiesFarbe('ul.mpMnuU a');       $sAMnuUH=fLiesHGFarb('ul.mpMnuU a');
  $sAMnuUA=fLiesFarbe('ul.mpMnuU a:hover'); $sAMnuUI=fLiesHGFarb('ul.mpMnuU a:hover');
  $sAMnuUR=fLiesRahmFarb('ul.mpMnuU li');   $sAMnuUL=fLiesRahmArt('ul.mpMnuU li');
  $sAMnuUV=fLiesContent('ul.mpMnuU a::before'); $sAMnuUN=fLiesContent('ul.mpMnuU a::after');
  $sAMnuUZ=fLiesHGFarb('ul.mpMnuU');        $sAMnuUB=fLiesRahmFarb('ul.mpMnuU');
  //Navigator (Liste)
  $sNavLnk=fLiesFarbe('ul.mpNavi a');     $sNavLkA=fLiesFarbe('ul.mpNavi a:hover');
  $sNaviHg=fLiesHGFarb('ul.mpNavi li');   $sNavHgI=fLiesHgImg('ul.mpNavi li');
  $sNaviRF=fLiesRahmFarb('ul.mpNavi li'); $sNaviRA=fLiesRahmArt('ul.mpNavi li');
  //Navigatorzeile
  $sNavLHg=fLiesHGFarb('div.mpNavL'); $sNavLRF=fLiesRahmFarb('div.mpNavL'); $sNavLRA=fLiesRahmArt('div.mpNavL');
  $sNavLSz=fLiesFarbe('div.mpSZhl');
  //Navigator (Detail)
  $sDNvLnk=fLiesFarbe('a.mpDetV,a.mpDetR');    $sDNvLkA=fLiesFarbe('a.mpDetR:hover');
  $sDNviHg=fLiesHGFarb('a.mpDetV,a.mpDetR');   $sDNvHgI=fLiesHgImg('a.mpDetR');
  $sDNviRF=fLiesRahmFarb('a.mpDetV,a.mpDetR'); $sDNviRA=fLiesRahmArt('a.mpDetV,a.mpDetR');
  //Navigatorzeile
  $sDNvLHg=fLiesHGFarb('div.mpNavD'); $sDNvLRF=fLiesRahmFarb('div.mpNavD'); $sDNvLRA=fLiesRahmArt('div.mpNavD');
  $sDNvLSz=fLiesFarbe('div.mpNavD');
  //Indexseite
  $sTIdxR=fLiesRahmFarb('div.mpIdxZe'); $sTIdxA=fLiesRahmArt('div.mpIdxZe');
  $sTIdxH=fLiesHGFarb('div.mpIdxZe');
  $sAIdxL=fLiesFarbe('a.mpIdx:link'); $sAIdxA=fLiesFarbe('a.mpIdx:hover');
  // Tabelle generell
  $sTablR=fLiesRahmArt('div.mpTbSpa'); $sTablF=fLiesRahmFarb('div.mpTbSpa');
  //Inserateliste
  $sTKopfF=fLiesFarbe('div.mpTbZl0'); $sTKopfH=fLiesHGFarb('div.mpTbZl0');
  $sTbZl1F=fLiesFarbe('div.mpTbZl1'); $sTbZl1H=fLiesHGFarb('div.mpTbZl1');
  $sTbZl2F=fLiesFarbe('div.mpTbZl2'); $sTbZl2H=fLiesHGFarb('div.mpTbZl2');
  $sLKatAF=fLiesFarbe('div.mpLstKatA'); $sLKatAH=fLiesHGFarb('div.mpLstKatA');
  $sLKatBF=fLiesFarbe('div.mpLstKatB'); $sLKatBH=fLiesHGFarb('div.mpLstKatB');
  $sLKatCF=fLiesFarbe('div.mpLstKatC'); $sLKatCH=fLiesHGFarb('div.mpLstKatC');
  $sLKatDF=fLiesFarbe('div.mpLstKatD'); $sLKatDH=fLiesHGFarb('div.mpLstKatD');
  $sLKatEF=fLiesFarbe('div.mpLstKatE'); $sLKatEH=fLiesHGFarb('div.mpLstKatE');
  $sLKatFF=fLiesFarbe('div.mpLstKatF'); $sLKatFH=fLiesHGFarb('div.mpLstKatF');
  $sLKatGF=fLiesFarbe('div.mpLstKatG'); $sLKatGH=fLiesHGFarb('div.mpLstKatG');
  $sADetl=fLiesFarbe('a.mpDetl:link'); $sADetA=fLiesFarbe('a.mpDetl:hover');
  $sPText=fLiesFarbe('p.mpText'); $sLText=fLiesFarbe('li.mpText');
  $sAText=fLiesFarbe('a.mpText:link'); $sATexA=fLiesFarbe('a.mpText:hover');
  $sTZlTrnF=fLiesFarbe('div.mpTrnZl'); $sTZlTrnH=fLiesHGFarb('div.mpTrnZl');
  $sVBldR=fLiesRahmArt('div.mpVBld'); $sVBldF=fLiesRahmFarb('div.mpVBld');
  // Filterzeile
  $sDFiltR=fLiesRahmFarb('div.mpFilt'); $sDFiltL=fLiesRahmArt('div.mpFilt');
  $sDFiltF=fLiesFarbe('div.mpFilt');    $sDFiltH=fLiesHGFarb('div.mpFilt');
  $sIFiltR=fLiesRahmFarb('input.mpSFlt');$sIFiltL=fLiesRahmArt('input.mpSFlt');
  $sIFiltF=fLiesFarbe('input.mpSFlt');  $sIFiltH=fLiesHGFarb('input.mpSFlt');
  // Eingaben
  $sIEingR=fLiesRahmFarb('input.mpEing');$sIEingL=fLiesRahmArt('input.mpEing'); // 2x wegen File
  $sIEingF=fLiesFarbe('input.mpEing');   $sIEingH=fLiesHGFarb('input.mpEing'); // Farbe 2x
  $sDFehlR=fLiesRahmFarb('div.mpFhlt');
  $sIEingZ=fLiesHoehe('textarea.mpEing',2);
 }elseif($_SERVER['REQUEST_METHOD']=='POST'){
  $sPageH=fNewCol('PageH'); if(fSetzHGFarb($sPageH,'body.mpSeite')) $bNeu=true;
  $sTBoxW=fTxtSiz('TBoxW'); if(fSetzeMaxWeite(($sTBoxW?$sTBoxW:'auto'),'div.mpBox')) $bNeu=true;

  $sTBoxF=fTxtSiz('TBoxF'); if(fSetzeFontSize($sTBoxF,'div.mpBox')) $bNeu=true;
  $sTBoxG=fTxtSiz('TBoxG'); if(fSetzeFontSize($sTBoxG,'div.mpBox',2)) $bNeu=true;
  $sTBoxS=fTxtSiz('TBoxS'); if(fSetzScreenW($sTBoxS,'gesamte Ausgabe')) $bNeu=true;

  $sTListW=fTxtSiz('TListW'); if(fSetzScreenW($sTListW,'Inserateliste mit Spalten')) $bNeu=true;
  $sPMeld=fNewCol('PMeld'); if(fSetzeFarbe($sPMeld,'p.mpMeld')) $bNeu=true;
  $sPErfo=fNewCol('PErfo'); if(fSetzeFarbe($sPErfo,'p.mpErfo')) $bNeu=true;
  $sPFehl=fNewCol('PFehl'); if(fSetzeFarbe($sPFehl,'p.mpFehl')) $bNeu=true;
  //Aktivitaetslinks oben
  $sAMnuOF=fNewCol('AMnuOF'); if(fSetzeFarbe($sAMnuOF,'ul.mpMnuO a')) $bNeu=true;
  $sAMnuOH=fNewCol('AMnuOH'); if(fSetzHGFarb($sAMnuOH,'ul.mpMnuO a')) $bNeu=true;
  $sAMnuOA=fNewCol('AMnuOA'); if(fSetzeFarbe($sAMnuOA,'ul.mpMnuO a:hover')) $bNeu=true;
  $sAMnuOI=fNewCol('AMnuOI'); if(fSetzHGFarb($sAMnuOI,'ul.mpMnuO a:hover')) $bNeu=true;
  $sAMnuOR=fNewCol('AMnuOR'); if(fSetzRahmFarb($sAMnuOR,'ul.mpMnuO li'))  $bNeu=true;
  $sAMnuOL=$_POST['AMnuOL'];  if(fSetzeRahmArt($sAMnuOL,'ul.mpMnuO li')) $bNeu=true;
  $sAMnuOV=$_POST['AMnuOV'];  if(fSetzContent('ul.mpMnuO a::before',$sAMnuOV)) $bNeu=true;
  $sAMnuON=$_POST['AMnuON'];  if(fSetzContent('ul.mpMnuO a::after',$sAMnuON)) $bNeu=true;
  $sAMnuOZ=fNewCol('AMnuOZ'); if(fSetzHGFarb($sAMnuOZ,'ul.mpMnuO')) $bNeu=true;
  $sAMnuOB=fNewCol('AMnuOB'); if(fSetzRahmFarb($sAMnuOB,'ul.mpMnuO'))  $bNeu=true;
  //Aktivitaetslinks unten
  $sAMnuUF=fNewCol('AMnuUF'); if(fSetzeFarbe($sAMnuUF,'ul.mpMnuU a')) $bNeu=true;
  $sAMnuUH=fNewCol('AMnuUH'); if(fSetzHGFarb($sAMnuUH,'ul.mpMnuU a')) $bNeu=true;
  $sAMnuUA=fNewCol('AMnuUA'); if(fSetzeFarbe($sAMnuUA,'ul.mpMnuU a:hover')) $bNeu=true;
  $sAMnuUI=fNewCol('AMnuUI'); if(fSetzHGFarb($sAMnuUI,'ul.mpMnuU a:hover')) $bNeu=true;
  $sAMnuUR=fNewCol('AMnuUR'); if(fSetzRahmFarb($sAMnuUR,'ul.mpMnuU li'))  $bNeu=true;
  $sAMnuUL=$_POST['AMnuUL'];  if(fSetzeRahmArt($sAMnuUL,'ul.mpMnuU li')) $bNeu=true;
  $sAMnuUV=$_POST['AMnuUV'];  if(fSetzContent('ul.mpMnuU a::before',$sAMnuUV)) $bNeu=true;
  $sAMnuUN=$_POST['AMnuUN'];  if(fSetzContent('ul.mpMnuU a::after',$sAMnuUN)) $bNeu=true;
  $sAMnuUZ=fNewCol('AMnuUZ'); if(fSetzHGFarb($sAMnuUZ,'ul.mpMnuU')) $bNeu=true;
  $sAMnuUB=fNewCol('AMnuUB'); if(fSetzRahmFarb($sAMnuUB,'ul.mpMnuU'))  $bNeu=true;
  //Navigator (Liste)
  $sNavLnk=fNewCol('NavLnk'); if(fSetzeFarbe($sNavLnk,'ul.mpNavi a')) $bNeu=true;
  $sNavLkA=fNewCol('NavLkA'); if(fSetzeFarbe($sNavLkA,'ul.mpNavi a:hover')) $bNeu=true;
  $sNaviHg=fNewCol('NaviHg'); if(fSetzHGFarb($sNaviHg,'ul.mpNavi li')) $bNeu=true;
  $s=(fTxtSiz('NavHgI')?'a':'p'); // HG_Bild
  if($s!=$sNavHgI){
   $sNavHgI=$s; $s=($s=='a'?true:false); $bNeu=true;
   fSetzeHgImg('ul.mpNavi li',$s); fSetzeHgImg('ul.mpNavi li.mpNavL',$s); fSetzeHgImg('ul.mpNavi li.mpNavR',$s);
  }
  $sNaviRF=fNewCol('NaviRF'); if(fSetzRahmFarb($sNaviRF,'ul.mpNavi li'))  $bNeu=true;
  $sNaviRA=$_POST['NaviRA'];  if(fSetzeRahmArt($sNaviRA,'ul.mpNavi li')) $bNeu=true;
  //Navigatorzeile
  $sNavLHg=fNewCol('NavLHg'); if(fSetzHGFarb($sNavLHg,'div.mpNavL')) $bNeu=true;
  $sNavLRF=fNewCol('NavLRF'); if(fSetzRahmFarb($sNavLRF,'div.mpNavL'))  $bNeu=true;
  $sNavLRA=$_POST['NavLRA'];  if(fSetzeRahmArt($sNavLRA,'div.mpNavL')) $bNeu=true;
  $sNavLSz=fNewCol('NavLSz'); if(fSetzeFarbe($sNavLSz,'div.mpSZhl')) $bNeu=true; //Seitenzaehler
  //Navigator (Detail)
  $sDNvLnk=fNewCol('DNvLnk'); if(fSetzeFarbe($sDNvLnk,'a.mpDetV,a.mpDetR')) $bNeu=true;
  $sDNvLkA=fNewCol('DNvLkA'); if(fSetzeFarbe($sDNvLkA,'a.mpDetR:hover')) $bNeu=true;
  $sDNviHg=fNewCol('DNviHg'); if(fSetzHGFarb($sDNviHg,'a.mpDetV,a.mpDetR')) $bNeu=true;
  $s=(fTxtSiz('DNvHgI')?'a':'p'); // HG_Bild
  if($s!=$sDNvHgI){
   $sDNvHgI=$s; $s=($s=='a'?true:false); $bNeu=true;
   fSetzeHgImg('a.mpDetR',$s); fSetzeHgImg('a.mpDetV',$s);
   if($p=strpos($sCss,'a.mpDetV::before')) if($e=strpos($sCss,'}',$p)) $sCss=substr_replace($sCss,"a.mpDetV::before{content:'".($s?'':'>>')."';}",$p,$e+1-$p);
   if($p=strpos($sCss,'a.mpDetR::after'))  if($e=strpos($sCss,'}',$p)) $sCss=substr_replace($sCss,"a.mpDetR::after{content:'".($s?'':'<<')."';}",$p,$e+1-$p);
  }
  $sDNviRF=fNewCol('DNviRF'); if(fSetzRahmFarb($sDNviRF,'a.mpDetV,a.mpDetR'))  $bNeu=true;
  $sDNviRA=$_POST['DNviRA'];  if(fSetzeRahmArt($sDNviRA,'a.mpDetV,a.mpDetR')) $bNeu=true;
  //Navigatorzeile
  $sDNvLHg=fNewCol('DNvLHg'); if(fSetzHGFarb($sDNvLHg,'div.mpNavD')) $bNeu=true;
  $sDNvLRF=fNewCol('DNvLRF'); if(fSetzRahmFarb($sDNvLRF,'div.mpNavD')) $bNeu=true;
  $sDNvLRA=$_POST['DNvLRA'];  if(fSetzeRahmArt($sDNvLRA,'div.mpNavD')) $bNeu=true;
  $sDNvLSz=fNewCol('DNvLSz'); if(fSetzeFarbe($sDNvLSz,'div.mpNavD')) $bNeu=true; //Seitenzaehler
  // Indexseite
  $sTIdxA=$_POST['TIdxA'];  if(fSetzeRahmArt($sTIdxA,'div.mpIdxZe')) $bNeu=true;
  $sTIdxR=fNewCol('TIdxR'); if(fSetzRahmFarb($sTIdxR,'div.mpIdxZe')) $bNeu=true;
  $sTIdxH=fNewCol('TIdxH'); if(fSetzHGFarb($sTIdxH,'div.mpIdxZe')) $bNeu=true;
  $sAIdxL=fNewCol('AIdxL'); if(fSetzeFarbe($sAIdxL,'a.mpIdx:link')) $bNeu=true;
  $sAIdxA=fNewCol('AIdxA'); if(fSetzeFarbe($sAIdxA,'a.mpIdx:hover')) $bNeu=true;
  // Tabellenrahmen generell
  $sTablF=fNewCol('TablF'); $sTablR=$_POST['TablR'];
  if($sTablF!=fLiesRahmFarb('div.mpTbSpa')){
   //if(fSetzRahmFarb($sTablF,'div.mpTabl')) $bNeu=true; // Rahmen werden durch die Zellen bestimmt
   if(fSetzRahmFarbB($sTablF,'div.mpTbZl0')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.mpTbZL0')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.mpTbZL0')) $bNeu=true;
   if(fSetzRahmFarbB($sTablF,'div.mpTbZl1')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.mpTbZL1')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.mpTbZL1')) $bNeu=true;
   if(fSetzRahmFarbB($sTablF,'div.mpTbZl2')) $bNeu=true;if(fSetzRahmFarbB($sTablF,'div.mpTbZL2')) $bNeu=true; elseif(fSetzRahmFarb($sTablF,'div.mpTbZL2')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbSpa')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbZlT')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.mpTbZlT',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbLst')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.mpTbLst',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbSp2')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.mpTbSp2',2)) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTZ0M'))  $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbZlM')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbSpT')) $bNeu=true;
   if(fSetzRahmFarb($sTablF,'div.mpTbSpK')) $bNeu=true; if(fSetzRahmFarb($sTablF,'div.mpTbSpK',2)) $bNeu=true;
   if(fSetzRahmFarbL($sTablF,'div.mpTbSp0')) $bNeu=true;
  }
  if($sTablR!=fLiesRahmArt('div.mpTbSpa')){
   //if(fSetzeRahmArt($sTablR,'div.mpTabl')) $bNeu=true; // Rahmen werden durch die Zellen bestimmt
   if(fSetzeRahmArtB($sTablR,'div.mpTbZl0')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.mpTbZL0')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.mpTbZL0')) $bNeu=true;
   if(fSetzeRahmArtB($sTablR,'div.mpTbZl1')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.mpTbZL1')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.mpTbZL1')) $bNeu=true;
   if(fSetzeRahmArtB($sTablR,'div.mpTbZl2')) $bNeu=true;if(fSetzeRahmArtB($sTablR,'div.mpTbZL2')) $bNeu=true; elseif(fSetzeRahmArt($sTablR,'div.mpTbZL2')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbSpa')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbZlT')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.mpTbZlT',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbLst')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.mpTbLst',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbSp2')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.mpTbSp2',2)) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTZ0M'))  $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbZlM')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbSpT')) $bNeu=true;
   if(fSetzeRahmArt($sTablR,'div.mpTbSpK')) $bNeu=true; if(fSetzeRahmArt($sTablR,'div.mpTbSpK',2)) $bNeu=true;
   if(fSetzeRahmArtL($sTablR,'div.mpTbSp0')) $bNeu=true;
  }
  // Inserateliste
  $sTKopfF=fNewCol('TKopfF'); if(fSetzeFarbe($sTKopfF,'div.mpTbZl0')) $bNeu=true; $sTKopfH=fNewCol('TKopfH'); if(fSetzHGFarb($sTKopfH,'div.mpTbZl0')) $bNeu=true; if(fSetzeFarbe($sTKopfF,'div.mpTbZL0')) $bNeu=true; if(fSetzHGFarb($sTKopfH,'div.mpTbZL0')) $bNeu=true;
  $sTbZl1F=fNewCol('TbZl1F'); if(fSetzeFarbe($sTbZl1F,'div.mpTbZl1')) $bNeu=true; $sTbZl1H=fNewCol('TbZl1H'); if(fSetzHGFarb($sTbZl1H,'div.mpTbZl1')) $bNeu=true; if(fSetzeFarbe($sTbZl1F,'div.mpTbZL1')) $bNeu=true; if(fSetzHGFarb($sTbZl1H,'div.mpTbZL1')) $bNeu=true;
  $sTbZl2F=fNewCol('TbZl2F'); if(fSetzeFarbe($sTbZl2F,'div.mpTbZl2')) $bNeu=true; $sTbZl2H=fNewCol('TbZl2H'); if(fSetzHGFarb($sTbZl2H,'div.mpTbZl2')) $bNeu=true; if(fSetzeFarbe($sTbZl2F,'div.mpTbZL2')) $bNeu=true; if(fSetzHGFarb($sTbZl2H,'div.mpTbZL2')) $bNeu=true;
  $sLKatAF=fNewCol('LKatAF'); if(fSetzeFarbe($sLKatAF,'div.mpLstKatA')) $bNeu=true; $sLKatAH=fNewCol('LKatAH'); if(fSetzHGFarb($sLKatAH,'div.mpLstKatA')) $bNeu=true;
  $sLKatBF=fNewCol('LKatBF'); if(fSetzeFarbe($sLKatBF,'div.mpLstKatB')) $bNeu=true; $sLKatBH=fNewCol('LKatBH'); if(fSetzHGFarb($sLKatBH,'div.mpLstKatB')) $bNeu=true;
  $sLKatCF=fNewCol('LKatCF'); if(fSetzeFarbe($sLKatCF,'div.mpLstKatC')) $bNeu=true; $sLKatCH=fNewCol('LKatCH'); if(fSetzHGFarb($sLKatCH,'div.mpLstKatC')) $bNeu=true;
  $sLKatDF=fNewCol('LKatDF'); if(fSetzeFarbe($sLKatDF,'div.mpLstKatD')) $bNeu=true; $sLKatDH=fNewCol('LKatDH'); if(fSetzHGFarb($sLKatDH,'div.mpLstKatD')) $bNeu=true;
  $sLKatEF=fNewCol('LKatEF'); if(fSetzeFarbe($sLKatEF,'div.mpLstKatE')) $bNeu=true; $sLKatEH=fNewCol('LKatEH'); if(fSetzHGFarb($sLKatEH,'div.mpLstKatE')) $bNeu=true;
  $sLKatFF=fNewCol('LKatFF'); if(fSetzeFarbe($sLKatFF,'div.mpLstKatF')) $bNeu=true; $sLKatFH=fNewCol('LKatFH'); if(fSetzHGFarb($sLKatFH,'div.mpLstKatF')) $bNeu=true;
  $sLKatGF=fNewCol('LKatGF'); if(fSetzeFarbe($sLKatGF,'div.mpLstKatG')) $bNeu=true; $sLKatGH=fNewCol('LKatGH'); if(fSetzHGFarb($sLKatGH,'div.mpLstKatG')) $bNeu=true;
  $sADetl=fNewCol('ADetl');   if(fSetzeFarbe($sADetl,'a.mpDetl:link')) $bNeu=true;  $sADetA=fNewCol('ADetA');   if(fSetzeFarbe($sADetA,'a.mpDetl:hover')) $bNeu=true;
  $sPText=fNewCol('PText'); if(fSetzeFarbe($sPText,'p.mpText')) $bNeu=true; $sLText=fNewCol('LText'); if(fSetzeFarbe($sLText,'li.mpText')) $bNeu=true;
  $sAText=fNewCol('AText'); if(fSetzeFarbe($sAText,'a.mpText:link')) $bNeu=true; $sATexA=fNewCol('ATexA'); if(fSetzeFarbe($sATexA,'a.mpText:hover')) $bNeu=true;
  $sTZlTrnF=fNewCol('TZlTrnF'); if(fSetzeFarbe($sTZlTrnF,'div.mpTrnZl')) $bNeu=true; $sTZlTrnH=fNewCol('TZlTrnH'); if(fSetzHGFarb($sTZlTrnH,'div.mpTrnZl')) $bNeu=true;
  $sVBldF=fNewCol('VBldF'); if(fSetzRahmFarb($sVBldF,'div.mpVBld')) $bNeu=true; $sVBldR=$_POST['VBldR'];  if(fSetzeRahmArt($sVBldR,'div.mpVBld')) $bNeu=true;
  // Filterzeile
  $sDFiltR=fNewCol('DFiltR'); if(fSetzRahmFarb($sDFiltR,'div.mpFilt')) $bNeu=true;
  $sDFiltL=$_POST['DFiltL'];  if(fSetzeRahmArt($sDFiltL,'div.mpFilt')) $bNeu=true;
  $sDFiltF=fNewCol('DFiltF'); if(fSetzeFarbe($sDFiltF,'div.mpFilt')) $bNeu=true;  $sDFiltH=fNewCol('DFiltH'); if(fSetzHGFarb($sDFiltH,'div.mpFilt')) $bNeu=true;
  $sIFiltR=fNewCol('IFiltR'); if(fSetzRahmFarb($sIFiltR,'input.mpSFlt')) $bNeu=true; if(fSetzRahmFarb($sIFiltR,'select.mpIFlt')) $bNeu=true;
  $sIFiltL=$_POST['IFiltL'];  if(fSetzeRahmArt($sIFiltL,'input.mpSFlt')) $bNeu=true; if(fSetzeRahmArt($sIFiltL,'select.mpIFlt')) $bNeu=true;
  $sIFiltF=fNewCol('IFiltF'); if(fSetzeFarbe($sIFiltF,'input.mpSFlt')) $bNeu=true;$sIFiltH=fNewCol('IFiltH'); if(fSetzHGFarb($sIFiltH,'input.mpSFlt')) $bNeu=true;
                              if(fSetzeFarbe($sIFiltF,'select.mpIFlt')) $bNeu=true;                           if(fSetzHGFarb($sIFiltH,'select.mpIFlt')) $bNeu=true;
  // Eingaben
  $sIEingR=fNewCol('IEingR'); if(fSetzRahmFarb($sIEingR,'input.mpEing')) $bNeu=true;
                              if(fSetzRahmFarb($sIEingR,'input[type=file].mpEing')) $bNeu=true;
  $sIEingL=$_POST['IEingL'];  if(fSetzeRahmArt($sIEingL,'input.mpEing')) $bNeu=true;
                              if(fSetzeRahmArt($sIEingL,'input[type=file].mpEing')) $bNeu=true;
  $sIEingF=fNewCol('IEingF'); if(fSetzeFarbe($sIEingF,'input.mpEing')) $bNeu=true;
                              if(fSetzeFarbe($sIEingF,'input[type=file].mpEing')) $bNeu=true;
  $sIEingH=fNewCol('IEingH'); if(fSetzHGFarb($sIEingH,'input.mpEing')) $bNeu=true;
  $sDFehlR=fNewCol('DFehlR'); if(fSetzRahmFarb($sDFehlR,'div.mpFhlt')) $bNeu=true;
  $sIEingZ=fTxtSiz('IEingZ'); if(fSetzeHoehe($sIEingZ,'textarea.mpEing',2)) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(MPPFAD.'mpStyles.css','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
    $Meld='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Meld='<p class="admFehl">In die Datei <i>mpStyles.css</i> konnte nicht geschrieben werden!</p>';
  }else if(!$Meld) $Meld='<p class="admMeld">Die Farb- und Layouteinstellungen bleiben unverändert.</p>';
  $sWerte=str_replace("\r",'',trim(implode('',file(MP_Pfad.'mpWerte.php')))); $bNw2=false;
  if($bNw2){//Speichern
   if($f=fopen(MP_Pfad.'mpWerte.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
    if(!$bNeu) $Meld='<p class="admErfo">Die geänderten Farb- und Layouteinstellungen wurden gespeichert.</p>';
   }else $Meld.='<p class="admFehl">In die Datei <i>mpWerte.php</i> im Programmverzeichnis konnte nicht geschrieben werden!</p>';
  }
 }//POST
}else $Meld.='<p class="admFehl">Setup-Fehler: Die Datei <i>mpStyles.css</i> im Programmverzeichnis kann nicht gelesen werden!</p>';

//Seitenausgabe
if(!$Meld) $Meld='<p class="admMeld">Kontrollieren oder ändern Sie die wesentlichen Farbeinstellungen.</p>';
echo $Meld.NL;
$sIcon='iconAendern.gif" width="12" height="13" border="0" title="Farbe bearbeiten';
?>

<p>Die folgenden Farben sowie Gestaltungsattribute können Sie auch direkt in der CSS-Datei <a href="konfCss.php"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> mpStyles.css</a> editieren.</p>
<form name="farbform" action="konfFarben.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="5" class="admSpa2">Der <b>Seitenhintergrund</b> wird (sofern das Marktplatz-Script <i>eigenständig</i> läuft und nicht per PHP-include eingebunden wurde) in folgender Farbe dargestellt:</td></tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="PageH" value="<?php echo $sPageH?>" style="width:70px">
 <a href="<?php echo fColorRef('PageH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sPageH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die <b>Gesamtbreite</b> der Ausgabe des Marktplatz-Scripts wird durch eine unsichtbare Box festgelegt. Diese Box nimmt die volle zur Verfügung stehende Anzeigebreite in Anspruch (98%), kann aber auf eine absolute Höchstbreite begrenzt werden.</td></tr>
<tr class="admTabl">
 <td>max. Ausgabebreite</td>
 <td colspan="3"><input type="text" name="TBoxW" value="<?php echo $sTBoxW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> oder <i>%</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 600...1000px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">Die <b>Basisschriftgröße</b> des Marktplatz-Scripts wird ebenfalls in dieser unsichtbare Box festgelegt. Es kann eine Schriftgröße für schmale Displays und eine für breite Display gesetzt werden.</td></tr>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Für <b>Meldungstexte</b> über den Formularen und Listen des Marktplatzes werden folgende Farben verwendet:</td></tr>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Die obere <b>Navigationszeile</b> mit Links wie '[Liste] [Drucken] [Suchen]' über dem Marktplatz hat das Aussehen:</td></tr>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Die untere <b>Navigationszeile</b> mit Links wie '[Liste] [Drucken] [Suchen]' unter dem Marktplatz hat das Aussehen:</td></tr>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Über oder unter der <i>Inserateliste</i> kann eine <b>Navigationszeile</b> mit Links zum Blättern '|&lt; 1 2 3 4 5 &gt;|' angezeigt werden.</td></tr>
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
 <td style="white-space:nowrap">oder <input class="admCheck" type="checkbox" name="NavHgI" value="a"<?php if($sNavHgI=='a') echo ' checked="checked"'?> /> Hintergrundgrafik <img style="vertical-align:-5px" src="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo MP_Www?>/grafik/naviRechts.gif" width="20" height="20" border="0" alt=""></td>
 <td class="admMini">Empfehlung: Grafik verwenden</td>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Auf den Seiten <i>Inseratedetails</i> kann eine <b>Navigationszeile</b> mit Links zum Blättern '&lt;&lt; &nbsp; &gt;&gt;' angezeigt werden.</td></tr>
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
 <td style="white-space:nowrap">oder <input class="admCheck" type="checkbox" name="DNvHgI" value="a"<?php if($sDNvHgI=='a') echo ' checked="checked"'?> /> Hintergrundgrafik <img style="vertical-align:-5px" src="http<?php if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443') echo 's'?>://<?php echo MP_Www?>/grafik/naviRechts.gif" width="20" height="20" border="0" alt=""></td>
 <td class="admMini">Empfehlung: Grafik verwenden</td>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">
Die <b>Startseite</b> mit der Segmentübersicht kann im Layout angepasst werden.<br/>
Die Segmente in der Übersicht erhalten einen farbigen <i>Rahmen</i>, farbige <i>Gitternetzlinien</i> und einen farbigen <i>Zellenhintergrund</i>.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Rahmenfarbe<br />und Gitternetz</td>
 <td><select name="TIdxA" style="width:8.4em" size="1"><?php echo fRahmenArten($sTIdxA)?></select> Linien</td>
 <td><input type="text" name="TIdxR" value="<?php echo $sTIdxR?>" style="width:70px">
 <a href="<?php echo fColorRef('TIdxR')?>"><img src="<?php echo $sIcon?>"></a> Farbe</td>
 <td align="center"><table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="1"><tr><td style="border:1px <?php echo $sTIdxA?> <?php echo $sTIdxR?>;color:<?php echo $sTIdxR?>;background-color:<?php echo $sTIdxH?>;padding:2px;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Hintergrundfarbe</td>
 <td colspan="2"><input type="text" name="TIdxH" value="<?php echo $sTIdxH?>" style="width:70px">
 <a href="<?php echo fColorRef('TIdxH')?>"><img src="<?php echo $sIcon?>"></a></td>
 <td align="center"><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:#bfc3bd;background-color:<?php echo $sTIdxH?>;">&nbsp;<b>Muster</b>&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2"><i>Verweise</i> auf der Übersichtsseite zu den Marktplatzsegmenten sollen wie folgt dargestellt werden:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Linkfarbe</td>
 <td><input type="text" name="AIdxL" value="<?php echo $sAIdxL?>" style="width:70px"> <a href="<?php echo fColorRef('AIdxL')?>"><img src="<?php echo $sIcon?>"></a> normal</td>
 <td><input type="text" name="AIdxA" value="<?php echo $sAIdxA?>" style="width:70px"> <a href="<?php echo fColorRef('AIdxA')?>"><img src="<?php echo $sIcon?>"></a> aktiviert</td>
 <td align="center"><table bgcolor="<?php echo $sTIdxR?>" border="0" cellpadding="2" cellspacing="1"><tr><td style="color:<?php echo $sAIdxL?>;background-color:<?php echo $sTIdxH?>;" onmouseover="this.style.color='<?php echo $sAIdxA?>'" onmouseout="this.style.color='<?php echo $sAIdxL?>'">&nbsp;Muster&nbsp;</td></tr></table></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Im Marktplatz-Script werden auf jeder Seite <b>Tabellen</b> zur Darstellung des Hauptinhaltes verwendet.</td></tr>
<tr class="admTabl">
 <td>Tabellenrahmen</td>
 <td><select name="TablR" style="width:8.4em" size="1"><?php echo fRahmenArten($sTablR)?></select></td>
 <td style="white-space:nowrap"><input type="text" name="TablF" value="<?php echo $sTablF?>" style="width:70px"> <a href="<?php echo fColorRef('TablF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1" style="margin-left:auto;margin-right:auto;"><tr><td style="color:<?php echo $sTablF;?>;background-color:#eee;border:1px <?php echo $sTablR.' '.$sTablF;?>">Rahmen</td></tr></table></td>
 <td class="admMini">Empfehlung: Rahmen</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Die Inserate in der <b>Inserateliste</b> werden auf breiten Monitoren als Tabelle mit nebeneinanderliegenden Spalten dargestellt. Auf schmalen Displays erscheinen die Inseratefelder in Zeilen untereinander. Bei welcher Breite soll das Umschalten zwischen diesen beiden Layouts erfolgen?
 <div class="admMini">Hinweis: Der konkrete Wert hängt von der Anzahl der Felder und deren Feldtyp in Ihrer Inserateliste ab und ist auszuprobieren.</div></td></tr>
<tr class="admTabl">
 <td>Listenumschaltung</td>
 <td colspan="3"><input type="text" name="TListW" value="<?php echo $sTListW?>" style="width:70px"> (Maßeinheit <i>px</i> oder <i>em</i> <i>mit</i> angeben!)</td>
 <td class="admMini">Empfehlung: 500...800px</td>
</tr>
<tr class="admTabl"><td colspan="5" class="admSpa2">In der <b>Inserateliste</b> werden folgende Farben verwendet.</td></tr>
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
 <td>Inseratekategorie A</td>
 <td><input type="text" name="LKatAF" value="<?php echo $sLKatAF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatAF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatAH" value="<?php echo $sLKatAH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatAH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatAF,$sLKatAH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie B</td>
 <td><input type="text" name="LKatBF" value="<?php echo $sLKatBF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatBF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatBH" value="<?php echo $sLKatBH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatBH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatBF,$sLKatBH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie C</td>
 <td><input type="text" name="LKatCF" value="<?php echo $sLKatCF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatCF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatCH" value="<?php echo $sLKatCH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatCH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatCF,$sLKatCH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie D</td>
 <td><input type="text" name="LKatDF" value="<?php echo $sLKatDF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatDF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatDH" value="<?php echo $sLKatDH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatDH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatDF,$sLKatDH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie E</td>
 <td><input type="text" name="LKatEF" value="<?php echo $sLKatEF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatEF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatEH" value="<?php echo $sLKatEH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatEH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatEF,$sLKatEH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie F</td>
 <td><input type="text" name="LKatFF" value="<?php echo $sLKatFF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatFF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatFH" value="<?php echo $sLKatFH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatFH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatFF,$sLKatFH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl">
 <td>Inseratekategorie G</td>
 <td><input type="text" name="LKatGF" value="<?php echo $sLKatGF?>" style="width:70px"> <a href="<?php echo fColorRef('LKatGF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="LKatGH" value="<?php echo $sLKatGH?>" style="width:70px"> <a href="<?php echo fColorRef('LKatGH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sLKatGF,$sLKatGH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>
<tr class="admTabl"><td colspan="5"><div class="admMini">Noch mehr Inseratekategorien bitte von Hand direkt in der <a href="konfCss.php"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei ändern"> CSS-Datei</a> im Abschnitt <i>weiterer Inseratekategorien</i> ändern und ergänzen!</div></td></tr>

<tr class="admTabl">
 <td>Links zu den<br>Inseratedetails</td>
 <td><input type="text" name="ADetl" value="<?php echo $sADetl?>" style="width:70px"> <a href="<?php echo fColorRef('ADetl')?>"><img src="<?php echo $sIcon?>"></a> (normal)</td>
 <td><input type="text" name="ADetA" value="<?php echo $sADetA?>" style="width:70px"> <a href="<?php echo fColorRef('ADetA')?>"><img src="<?php echo $sIcon?>"></a> (aktiviert)</td>
 <td align="center"><?php echo fMusterLink($sADetl,$sADetA,$sTbZl1H)?></td>
 <td class="admMini">Empfehlung: blau/rot</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">In den Schnellsuchergebnissen kann zwischen Inserateliste der einzelnen Segmente eine Trennzeile eingefügt sein.</td></tr>
<tr class="admTabl">
 <td>Trennzeile</td>
 <td><input type="text" name="TZlTrnF" value="<?php echo $sTZlTrnF?>" style="width:70px"> <a href="<?php echo fColorRef('TZlTrnF')?>"><img src="<?php echo $sIcon?>"></a> Textfarbe</td>
 <td><input type="text" name="TZlTrnH" value="<?php echo $sTZlTrnH?>" style="width:70px"> <a href="<?php echo fColorRef('TZlTrnH')?>"><img src="<?php echo $sIcon?>"></a> Hintergrund</td>
 <td align="center"><?php echo fMusterFeld($sTZlTrnF,$sTZlTrnH)?></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Eventuelle Vorschaubilder in der Inserateliste können unter dem Menüpunkt <i>Eingabeformular</i> mit einem Rahmen versehen werden. Falls dieser Rahmen aktiviert wurde, kann er formatiert werden. </td></tr>
<tr class="admTabl">
 <td>Bilderrahmen</td>
 <td><select name="VBldR" style="width:8.4em" size="1"><?php echo fRahmenArten($sVBldR)?></select></td>
 <td style="white-space:nowrap"><input type="text" name="VBldF" value="<?php echo $sVBldF?>" style="width:70px"> <a href="<?php echo fColorRef('VBldF')?>"><img src="<?php echo $sIcon?>"></a> Rahmenfarbe</td>
 <td><table bgcolor="#FFFFFF" border="0" cellpadding="2" cellspacing="1" style="margin-left:auto;margin-right:auto;"><tr><td style="color:#bbaabb;background-color:#fff;border:1px <?php echo $sVBldR.' '.$sVBldF;?>">&nbsp;&nbsp; Bild &nbsp;&nbsp;</td></tr></table></td>
 <td class="admMini">keine Empfehlung</td>
</tr>

<tr class="admTabl"><td colspan="5" class="admSpa2">Text in Memofeldern in der Inserateliste oder den Inseratedetails kann Absätze, Aufzählungen und externe Links enthalten.</td></tr>
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

<tr class="admTabl"><td colspan="5" class="admSpa2">Über der <i>Inserateliste</i> kann eine <b>Filterzeile</b> mit einem Intervallfilter und/oder einem Suchfilter erscheinen.</td></tr>
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