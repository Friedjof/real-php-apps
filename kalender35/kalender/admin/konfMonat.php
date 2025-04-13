<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Monatsblatt anpassen','','KMo');

$nFelder=count($kal_FeldName);
if($_SERVER['REQUEST_METHOD']=='GET'){
 $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für das Monatskalenderblatt bzw. Wochendatenblatt.</p>';
 $ksTxMonGsmt=KAL_TxMonGsmt; $ksTxMonSuch=KAL_TxMonSuch;
 $ksTxWocGsmt=KAL_TxWocGsmt; $ksTxWocSuch=KAL_TxWocSuch;
 $ksTxMMetaKey=KAL_TxMMetaKey; $ksTxMMetaDes=KAL_TxMMetaDes; $ksTxMMetaTit=KAL_TxMMetaTit;
 $ksTxWMetaKey=KAL_TxWMetaKey; $ksTxWMetaDes=KAL_TxWMetaDes; $ksTxWMetaTit=KAL_TxWMetaTit;
 $ksMNaviOben=KAL_MNaviOben; $ksMNaviUnten=KAL_MNaviUnten; $ksMNaviBild=KAL_MNaviBild; $ksMSuchFilter=KAL_MSuchFilter;
 $ksMonatMLang=KAL_MonatMLang;
 $ksMonWochNr=KAL_MonWochNr; $ksMonTxNr=KAL_MonTxNr; $ksTxMWochNr=KAL_TxMWochNr;
 $ksMonFremd=KAL_MonFremd; $ksMonOhneAltes=KAL_MonOhneAltes;
 $ksMZellenHoehe=KAL_MZellenHoehe; $ksWZellenHoehe=KAL_WZellenHoehe;
 $ksMDatumsformat=KAL_MDatumsformat; $ksMLinkZiel=KAL_MLinkZiel; $ksMLinkLeerNeu=KAL_MLinkLeerNeu;
 $ksMTerminDetail=KAL_MTerminDetail; $ksMEigenesLayout=KAL_MEigenesLayout; $ksMTerminZahl=KAL_MTerminZahl;
 $ksMDetail1Fld=KAL_MDetail1Fld; $ksMDetail2Fld=KAL_MDetail2Fld; $ksMDetail3Fld=KAL_MDetail3Fld; $ksMDetail4Fld=KAL_MDetail4Fld;
 $ksMDetail1Trn=KAL_MDetail1Trn; $ksMDetail2Trn=KAL_MDetail2Trn; $ksMDetail3Trn=KAL_MDetail3Trn;
 $ksMDetailKuerzen=KAL_MDetailKuerzen; $ksMonatsKateg=KAL_MonatsKateg;
 $ksMonatsInfo=KAL_MonatsInfo;$ksGastMoInfo=KAL_GastMoInfo;
 $ksMonatsErinn=KAL_MonatsErinn; $ksGastMoErinn=KAL_GastMoErinn;
 $ksMonatsBenachr=KAL_MonatsBenachr; $ksGastMoBenachr=KAL_GastMoBenachr;
 $ksMonatsICal=KAL_MonatsICal; $ksGastMoICal=KAL_GastMoICal;
 $ksMonatsZusage=KAL_MonatsZusage; $ksGastMoZusage=KAL_GastMoZusage;
 $ksMonatZeigeZusage=KAL_MonatZeigeZusage; $ksGastMZeigeZusage=KAL_GastMZeigeZusage;
 $ksEigeneMDruckZelle=KAL_EigeneMDruckZelle; $ksDruckMFarbig=KAL_DruckMFarbig; $ksDruckLMailOffen=KAL_DruckLMailOffen;
 $ksVBoxMon=KAL_VBoxMon; $ksVBoxWarten=KAL_VBoxWarten; $ksVBoxAutoAus=KAL_VBoxAutoAus; $ksVBoxNrStellen=KAL_VBoxNrStellen; $ksVBoxTxNr=KAL_VBoxTxNr;
 $ksVBoxWidth=KAL_VBoxWidth; $ksVBoxHeight=KAL_VBoxHeight; $ksVBoxHOffset=KAL_VBoxHOffset; $ksVBoxVOffset=KAL_VBoxVOffset;
 $aListenFeld=explode(',',KAL_VBoxFelder); $aListenNFeld=explode(',',KAL_VBoxNFelder); $aLinkFeld=explode(',',KAL_VBoxLinkFld); $aFeldStil=explode(',',KAL_VBoxFldStil);
 $ksTxZusageZeile=KAL_TxZusageZeile; $ksVBoxTxZusagZ=KAL_VBoxTxZusagZ; $ksVBoxTxZusagZMuster=KAL_VBoxTxZusagZMuster;
 $ksVBoxInfo=KAL_VBoxInfo; $ksVBoxGastInfo=KAL_VBoxGastInfo; $ksTxInfoSenden=KAL_TxInfoSenden;
 $ksVBoxErinn=KAL_VBoxErinn; $ksVBoxGastErinn=KAL_VBoxGastErinn; $ksTxErinnService=KAL_TxErinnService;
 $ksVBoxBenachr=KAL_VBoxBenachr; $ksVBoxGastBenachr=KAL_VBoxGastBenachr; $ksTxBenachrService=KAL_TxBenachrService;
 $ksVBoxDruck=KAL_VBoxDruck; $ksVBoxGastDruck=KAL_VBoxGastDruck; $ksTxDrucken=KAL_TxDrucken;
 $ksVBoxICal=KAL_VBoxICal; $ksVBoxGastICal=KAL_VBoxGastICal; $ksTxCalZeile=KAL_TxCalZeile;
 $ksVBoxZusage=KAL_VBoxZusage; $ksVBoxGastZusage=KAL_VBoxGastZusage;
 $ksVBoxZeigeZusage=KAL_VBoxZeigeZusage; $ksVBoxGastZeigeZusage=KAL_VBoxGastZeigeZusage;
 $ksVBoxZusagZ=KAL_VBoxZusagZ; $ksVBoxGastZusagZ=KAL_VBoxGastZusagZ;
 $ksVBoxLeeres=KAL_VBoxLeeres; $ksVBoxLink=KAL_VBoxLink;
 $ksNutzerVBoxFld=KAL_NutzerVBoxFld; $ksNNutzerVBoxFld=KAL_NNutzerVBoxFld; $ksVBoxEigenesLayout=KAL_VBoxEigenesLayout;
}else if($_SERVER['REQUEST_METHOD']=='POST'){
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false; $bCss=false;
 $v=txtVar('TxMonGsmt'); if(fSetzKalWert($v,'TxMonGsmt','"')) $bNeu=true;
 $v=txtVar('TxMonSuch'); if(fSetzKalWert($v,'TxMonSuch','"')) $bNeu=true;
 $v=txtVar('TxMMetaKey'); if(fSetzKalWert($v,'TxMMetaKey','"')) $bNeu=true;
 $v=txtVar('TxMMetaDes'); if(fSetzKalWert($v,'TxMMetaDes','"')) $bNeu=true;
 $v=txtVar('TxMMetaTit'); if(fSetzKalWert($v,'TxMMetaTit','"')) $bNeu=true;
 $v=txtVar('TxWocGsmt'); if(fSetzKalWert($v,'TxWocGsmt','"')) $bNeu=true;
 $v=txtVar('TxWocSuch'); if(fSetzKalWert($v,'TxWocSuch','"')) $bNeu=true;
 $v=txtVar('TxWMetaKey'); if(fSetzKalWert($v,'TxWMetaKey','"')) $bNeu=true;
 $v=txtVar('TxWMetaDes'); if(fSetzKalWert($v,'TxWMetaDes','"')) $bNeu=true;
 $v=txtVar('TxWMetaTit'); if(fSetzKalWert($v,'TxWMetaTit','"')) $bNeu=true;
 $v=(int)txtVar('MNaviOben');  if(fSetzKalWert($v,'MNaviOben','')) $bNeu=true;
 $v=(int)txtVar('MNaviUnten'); if(fSetzKalWert($v,'MNaviUnten','')) $bNeu=true;
 $v=txtVar('MNaviBild'); if(fSetzKalWert(($v?true:false),'MNaviBild','')) $bNeu=true;
 $v=(int)txtVar('MSuchFilter'); if(fSetzKalWert($v,'MSuchFilter','')) $bNeu=true;
 $v=(int)txtVar('MonatMLang'); if(fSetzKalWert($v,'MonatMLang','')) $bNeu=true;
 $v=txtVar('MonWochNr'); if(fSetzKalWert(($v?true:false),'MonWochNr','')) $bNeu=true;
 $v=txtVar('MonTxNr'); if(fSetzKalWert($v,'MonTxNr',"'")) $bNeu=true;
 $v=txtVar('TxMWochNr'); if(fSetzKalWert($v,'TxMWochNr',"'")) $bNeu=true;
 $v=txtVar('MonFremd'); if(fSetzKalWert(($v?true:false),'MonFremd','')) $bNeu=true;
 $v=txtVar('MonOhneAltes'); if(fSetzKalWert(($v?true:false),'MonOhneAltes','')) $bNeu=true;
 $v=(int)txtVar('MDatumsformat'); if(fSetzKalWert($v,'MDatumsformat','')) $bNeu=true;
 $v=(int)txtVar('MLinkZiel'); if(fSetzKalWert($v,'MLinkZiel','')) $bNeu=true;
 $v=(int)txtVar('MLinkLeerNeu'); if(fSetzKalWert(($v?true:false),'MLinkLeerNeu','')) $bNeu=true;
 $v=txtVar('MTerminDetail'); if(fSetzKalWert(($v?true:false),'MTerminDetail','')) $bNeu=true;
 $v=txtVar('MEigenesLayout'); if(fSetzKalWert(($v?true:false),'MEigenesLayout','')) $bNeu=true;
 $v=max((int)txtVar('MTerminZahl'),1); if(fSetzKalWert($v,'MTerminZahl','')) $bNeu=true;
 $v=(int)txtVar('MDetail1Fld'); if(fSetzKalWert($v,'MDetail1Fld','')) $bNeu=true;
 $v=(int)txtVar('MDetail2Fld'); if(fSetzKalWert($v,'MDetail2Fld','')) $bNeu=true;
 $v=(int)txtVar('MDetail3Fld'); if(fSetzKalWert($v,'MDetail3Fld','')) $bNeu=true;
 $v=(int)txtVar('MDetail4Fld'); if(fSetzKalWert($v,'MDetail4Fld','')) $bNeu=true;
 $v=txtVarL('MDetail1Trn'); if(fSetzKalWert($v,'MDetail1Trn',"'")) $bNeu=true;
 $v=txtVarL('MDetail2Trn'); if(fSetzKalWert($v,'MDetail2Trn',"'")) $bNeu=true;
 $v=txtVarL('MDetail3Trn'); if(fSetzKalWert($v,'MDetail3Trn',"'")) $bNeu=true;
 $v=(int)txtVar('MDetailKuerzen'); if(fSetzKalWert($v,'MDetailKuerzen','')) $bNeu=true;
 $v=txtVar('MonatsKateg'); if(fSetzKalWert(($v?true:false),'MonatsKateg','')) $bNeu=true;
 $v=txtVar('MonatsInfo'); if(fSetzKalWert(($v?true:false),'MonatsInfo','')) $bNeu=true;
 $w=txtVar('GastMoInfo'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoInfo','')) $bNeu=true;
 $v=txtVar('MonatsErinn'); if(fSetzKalWert(($v?true:false),'MonatsErinn','')) $bNeu=true;
 $w=txtVar('GastMoErinn'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoErinn','')) $bNeu=true;
 $v=txtVar('MonatsBenachr'); if(fSetzKalWert(($v?true:false),'MonatsBenachr','')) $bNeu=true;
 $w=txtVar('GastMoBenachr'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoBenachr','')) $bNeu=true;
 $v=txtVar('MonatsICal'); if(fSetzKalWert(($v?true:false),'MonatsICal','')) $bNeu=true;
 $w=txtVar('GastMoICal'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoICal','')) $bNeu=true;
 $v=txtVar('MonatsZusage'); if(fSetzKalWert(($v?true:false),'MonatsZusage','')) $bNeu=true;
 $w=txtVar('GastMoZusage'); if(fSetzKalWert((($v&&$w)?true:false),'GastMoZusage','')) $bNeu=true;
 $v=txtVar('MonatZeigeZusage'); if(fSetzKalWert(($v?true:false),'MonatZeigeZusage','')) $bNeu=true;
 $w=txtVar('GastMZeigeZusage'); if(fSetzKalWert((($v&&$w)?true:false),'GastMZeigeZusage','')) $bNeu=true;
 $v=max((int)txtVar('MZellenHoehe'),1); if(fSetzKalWert($v,'MZellenHoehe','')){$bNeu=true; $bCss=true;}
 $v=max((int)txtVar('WZellenHoehe'),1); if(fSetzKalWert($v,'WZellenHoehe','')){$bNeu=true; $bCss=true;}
 $v=txtVar('DruckMFarbig'); if(fSetzKalWert(($v?true:false),'DruckMFarbig','')) $bNeu=true;
 $v=txtVar('EigeneMDruckZelle'); if(fSetzKalWert(($v?true:false),'EigeneMDruckZelle','')) $bNeu=true;
 $v=txtVar('DruckLMailOffen'); if(fSetzKalWert(($v?true:false),'DruckLMailOffen','')) $bNeu=true;
 $v=txtVar('VBoxMon'); if(fSetzKalWert(($v?true:false),'VBoxMon','')) $bNeu=true;
 $v=max(min((int)txtVar('VBoxWarten'),5000),100); if(fSetzKalWert($v,'VBoxWarten','')) $bNeu=true;
 $v=txtVar('VBoxAutoAus'); if(fSetzKalWert(($v?true:false),'VBoxAutoAus','')) $bNeu=true;
 $v=max((int)txtVar('VBoxNrStellen'),1); if(fSetzKalWert($v,'VBoxNrStellen','')) $bNeu=true;
 $v=txtVar('VBoxTxNr'); if(fSetzKalWert($v,'VBoxTxNr',"'")) $bNeu=true;
 $v=max((int)txtVar('VBoxWidth'),50); if(fSetzKalWert($v,'VBoxWidth','')) $bNeu=true;
 $v=max((int)txtVar('VBoxHeight'),50); if(fSetzKalWert($v,'VBoxHeight','')) $bNeu=true;
 $v=(int)txtVar('VBoxHOffset'); if(fSetzKalWert($v,'VBoxHOffset','')) $bNeu=true;
 $v=(int)txtVar('VBoxVOffset'); if(fSetzKalWert($v,'VBoxVOffset','')) $bNeu=true;
 $aListenFeld=array(); $aListenNFeld=array(); $aLinkFeld=array(); $aFeldStil=array();
 for($i=0;$i<$nFelder;$i++){
  $aListenFeld[$i]=(isset($_POST['F'.$i])?(int)$_POST['F'.$i]:0);
  $aListenNFeld[$i]=(isset($_POST['N'.$i])?(int)$_POST['N'.$i]:0);
  $aLinkFeld[$i]=(isset($_POST['L'.$i])?(int)$_POST['L'.$i]:0);
  $aFeldStil[$i]=(isset($_POST['S'.$i])?str_replace("'",'"',stripslashes($_POST['S'.$i])):'');
 }
 asort($aListenFeld); reset($aListenFeld); asort($aListenNFeld); reset($aListenNFeld);
 $j=0; foreach($aListenFeld as $k=>$v) if($v>0) if($k>0) $aListenFeld[$k]=++$j;
 $j=0; foreach($aListenNFeld as $k=>$v) if($v>0) if($k>0) $aListenNFeld[$k]=++$j;
 $sLF=''; $sNF=''; $sLk=''; $sFS='';
 for($i=0;$i<$nFelder;$i++){
  $sLF.=$aListenFeld[$i].','; $sNF.=$aListenNFeld[$i].','; $sLk.=$aLinkFeld[$i].','; $sFS.=$aFeldStil[$i].',';
 }
 $sLF=substr($sLF,0,-1); $sNF=substr($sNF,0,-1); $sLk=substr($sLk,0,-1); $sFS=substr($sFS,0,-1);
 if($sLF!=KAL_VBoxFelder)  if(fSetzKalWert($sLF,'VBoxFelder',"'")) $bNeu=true;
 if($sNF!=KAL_VBoxNFelder) if(fSetzKalWert($sNF,'VBoxNFelder',"'")) $bNeu=true;
 if($sLk!=KAL_VBoxLinkFld) if(fSetzKalWert($sLk,'VBoxLinkFld',"'")) $bNeu=true;
 if($sFS!=KAL_VBoxFldStil) if(fSetzKalWert($sFS,'VBoxFldStil',"'")) $bNeu=true;
 $v=txtVar('VBoxEigenesLayout'); if(fSetzKalWert(($v?true:false),'VBoxEigenesLayout','')) $bNeu=true;
 $v=txtVar('TxInfoSenden'); if(fSetzKalWert($v,'TxInfoSenden','"')) $bNeu=true;
 $v=txtVar('TxErinnService'); if(fSetzKalWert($v,'TxErinnService','"')) $bNeu=true;
 $v=txtVar('TxBenachrService'); if(fSetzKalWert($v,'TxBenachrService','"')) $bNeu=true;
 $v=txtVar('TxDrucken'); if(fSetzKalWert($v,'TxDrucken','"')) $bNeu=true;
 $v=txtVar('TxCalZeile'); if(fSetzKalWert($v,'TxCalZeile',"'")) $bNeu=true;
 $v=txtVar('TxZusageZeile'); if(fSetzKalWert($v,'TxZusageZeile',"'")) $bNeu=true;
 $v=(int)txtVar('VBoxInfo'); if(fSetzKalWert($v,'VBoxInfo','')) $bNeu=true;
 $v=txtVar('VBoxGastInfo'); if(fSetzKalWert(($v?true:false),'VBoxGastInfo','')) $bNeu=true;
 $v=(int)txtVar('VBoxErinn'); if(fSetzKalWert($v,'VBoxErinn','')) $bNeu=true;
 $v=txtVar('VBoxGastErinn'); if(fSetzKalWert(($v?true:false),'VBoxGastErinn','')) $bNeu=true;
 $v=(int)txtVar('VBoxBenachr'); if(fSetzKalWert($v,'VBoxBenachr','')) $bNeu=true;
 $v=txtVar('VBoxGastBenachr'); if(fSetzKalWert(($v?true:false),'VBoxGastBenachr','')) $bNeu=true;
 $v=(int)txtVar('VBoxDruck'); if(fSetzKalWert($v,'VBoxDruck','')) $bNeu=true;
 $v=(int)txtVar('VBoxGastDruck'); if(fSetzKalWert(($v?true:false),'VBoxGastDruck','')) $bNeu=true;
 $v=(int)txtVar('VBoxICal'); if(fSetzKalWert($v,'VBoxICal','')) $bNeu=true;
 $v=(int)txtVar('VBoxGastICal'); if(fSetzKalWert(($v?true:false),'VBoxGastICal','')) $bNeu=true;
 $v=(int)txtVar('VBoxZusage'); if(fSetzKalWert($v,'VBoxZusage','')) $bNeu=true;
 $v=(int)txtVar('VBoxGastZusage'); if(fSetzKalWert(($v?true:false),'VBoxGastZusage','')) $bNeu=true;
 $v=(int)txtVar('VBoxZeigeZusage'); if(fSetzKalWert(($v?true:false),'VBoxZeigeZusage','')) $bNeu=true;
 $v=(int)txtVar('VBoxGastZeigeZusage'); if(fSetzKalWert(($v?true:false),'VBoxGastZeigeZusage','')) $bNeu=true;
 $v=(int)txtVar('VBoxZusagZ'); if(fSetzKalWert($v,'VBoxZusagZ','')) $bNeu=true;
 $v=(int)txtVar('VBoxGastZusagZ'); if(fSetzKalWert(($v?true:false),'VBoxGastZusagZ','')) $bNeu=true;
 $v=txtVar('VBoxLeeres'); if(fSetzKalWert(($v?true:false),'VBoxLeeres','')) $bNeu=true;
 $v=txtVar('VBoxTxZusagZ'); if(fSetzKalWert($v,'VBoxTxZusagZ',"'")) $bNeu=true;
 $v=txtVar('VBoxTxZusagZMuster'); if(fSetzKalWert($v,'VBoxTxZusagZMuster',"'")) $bNeu=true;
 $v=txtVar('VBoxLink'); if(fSetzKalWert($v,'VBoxLink','"')) $bNeu=true;
 $v=(int)txtVar('NutzerVBoxFld'); if(fSetzKalWert($v,'NutzerVBoxFld','')) $bNeu=true;
 $v=(int)txtVar('NNutzerVBoxFld'); if(fSetzKalWert($v,'NNutzerVBoxFld','')) $bNeu=true;
 if($bNeu){//Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg='<p class="admErfo">Die Monatseinstellungen wurden gespeichert.</p>';
  }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> durfte nicht geschrieben werden!</p>';
 }else $Msg='<p class="admMeld">Die Monatseinstellungen bleiben unverändert.</p>';
 if($bCss){//Hoehe geändert
  $sCss=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalStyles.css')))); $bCss=false;
  if($p=strpos($sCss,'div.kalTbSpT')) if($p=strpos($sCss,'div.kalTbSpT',$p+1)){
   $q=strpos($sCss,'}',$p); $p=strpos($sCss,'height:',$p);
   if($p>0&&$p<$q){$q=strpos($sCss,';',$p); $sCss=substr_replace($sCss,'height:'.floor(1.4*$ksMZellenHoehe).'em;',$p,$q+1-$p); $bCss=true;}
  }
  if($p=strpos($sCss,'div.kalTbSpt')) if($p=strpos($sCss,'div.kalTbSpt',$p+1)){
   $q=strpos($sCss,'}',$p); $p=strpos($sCss,'height:',$p);
   if($p>0&&$p<$q){$q=strpos($sCss,';',$p); $sCss=substr_replace($sCss,'height:'.floor(1.4*$ksWZellenHoehe).'em;',$p,$q+1-$p); $bCss=true;}
  }
  if($bCss) if($f=fopen(KAL_Pfad.'kalStyles.css','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sCss))).NL); fclose($f);
   $Msg.='<p class="admErfo">Auch die CSS-Datei wurde geändert.</p>';
  }else $Msg.='<p class="admFehl">In die Datei <i>kalStyles.css</i> durfte nicht geschrieben werden!</p>';
 }
}//POST

$sOpt1Fld=''; $sOpt2Fld=''; $sOpt3Fld=''; $sOpt4Fld=''; if($ksMDetailKuerzen<=0) $ksMDetailKuerzen='';
for($i=0;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i]; $sFN=str_replace('`,',';',$kal_FeldName[$i]);
 if($t=='d'||$t=='@'||$t=='z'||($t=='t'&&$sFN!='TITLE'&&substr($sFN,0,4)!='META')||$t=='a'||$t=='k'||$t=='m'||$t=='s'||$t=='l'||$t=='b'||$t=='j'||$t=='#'||$t=='w'||$t=='o'||$t=='n'||$t=='i'||$t=='e'||$t=='u'){
  $sOpt1Fld.='<option value="'.$i.'"'.($ksMDetail1Fld!=$i?'':' selected="selected"').'>'.$sFN.'</option>';
  $sOpt2Fld.='<option value="'.$i.'"'.($ksMDetail2Fld!=$i?'':' selected="selected"').'>'.$sFN.'</option>';
  $sOpt3Fld.='<option value="'.$i.'"'.($ksMDetail3Fld!=$i?'':' selected="selected"').'>'.$sFN.'</option>';
  $sOpt4Fld.='<option value="'.$i.'"'.($ksMDetail4Fld!=$i?'':' selected="selected"').'>'.$sFN.'</option>';
 }
}
//Seitenausgabe
echo $Msg.NL;
?>

<form action="konfMonat.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Über der Terminliste des Monatsblattes wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">ungefilterte Liste</td>
 <td colspan="2"><input type="text" name="TxMonGsmt" value="<?php echo $ksTxMonGsmt?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Gesamtliste für Monat #M #Y</i> oder <i>Gesamtliste mit #N Terminen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Suchergebnisliste</td>
 <td colspan="2"><input type="text" name="TxMonSuch" value="<?php echo $ksTxMonSuch?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Suchergebnis für #S im Monat #M</i> &nbsp; (#M: Monat, #Y: Jahr, #S: gesuchten Felder, #N: Anzahl)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Monatsseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i>
mit folgenden Texten zusätzlich füllen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxMMetaKey" value="<?php echo $ksTxMMetaKey?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine Veranstaltungen Monat #M</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxMMetaDes" value="<?php echo $ksTxMMetaDes?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Veranstaltungstermine Monat #M</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><input type="text" name="TxMMetaTit" value="<?php echo $ksTxMMetaTit?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine im Monat #M</i></div></td>
</tr>


<tr class="admTabl"><td colspan="3" class="admSpa2">Über der Terminliste des Wochenblattes wird Besuchern folgende Meldung angezeigt.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">ungefilterte Liste</td>
 <td colspan="2"><input type="text" name="TxWocGsmt" value="<?php echo $ksTxWocGsmt?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Gesamtliste für Woche #W #Y</i> oder <i>Gesamtliste mit #N Terminen</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Suchergebnisliste</td>
 <td colspan="2"><input type="text" name="TxWocSuch" value="<?php echo $ksTxWocSuch?>" style="width:100%" />
 <div class="admMini">Empfehlung: <i>Suchergebnis für #S in Woche #W</i> &nbsp; (#W: Woche, #Y: Jahr, #S: gesuchten Felder, #N: Anzahl)</div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern der Kalender eigenständig
mit der umhüllenden HTML-Schablone <i>kalSeite.htm</i> läuft (nicht per PHP-include eingebettet)
kann er die <i>META</i>-Tags <i>keywords</i> und <i>description</i> sowie eine Ergänzung im <i>TITLE</i>-Tag in der Wochenseite
über die Platzhalter <i>{META-KEY}</i>, <i>{META-DES}</i> und <i>{TITLE}</i> der HTML-Schablone <i>kalSeite.htm</i>
mit folgenden Texten zusätzlich füllen:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">meta-keywords<div>{META-KEY}</div></td>
 <td colspan="2"><input type="text" name="TxWMetaKey" value="<?php echo $ksTxWMetaKey?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine Veranstaltungen Woche #W</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">meta-description<div>{META-DES}</div></td>
 <td colspan="2"><input type="text" name="TxWMetaDes" value="<?php echo $ksTxWMetaDes?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Veranstaltungstermine Woche #W</i></div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">title<div>{TITLE}</div></td>
 <td colspan="2"><input type="text" name="TxWMetaTit" value="<?php echo $ksTxWMetaTit?>" style="width:100%" />
 <div class="admMini">Beispiel: <i>Termine in Woche #W</i></div></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Über und/oder unter der Monatsliste/Wochenliste im Besucherbereich
kann eine Navigationsleiste zum seitenweisen Blättern durch die einzelnen Monate/Wochen angezeigt werden.
An welchen Positionen soll eine solche Navigationsleiste erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator oberhalb<br />der Liste</td>
 <td colspan="2"><select name="MNaviOben" size="1" style="width:290px;"><option value="0">obere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksMNaviOben==1) echo ' selected="selected"'?>>oberen Navigator anzeigen</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigator unterhalb<br />der Liste</td>
 <td colspan="2"><select name="MNaviUnten" size="1" style="width:290px;"><option value="0">untere Navigatorleiste nicht anzeigen</option><option value="1"<?php if($ksMNaviUnten==1) echo ' selected="selected"'?>>Navigator unter der Liste anzeigen</option></select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Navigatorstil</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="MNaviBild" value="1"<?php if($ksMNaviBild) echo ' checked="checked"'?> /> die Navigationsleiste soll grafisch unterlegt sein</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Über oder unter Monatsliste/Wochenliste im Besucherbereich kann ein Filter als Eingabefeldfeld für die Schnellsuche dargestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Schnellsuche</td>
 <td colspan="2"><select name="MSuchFilter" size="1" style="width:290px;">
  <option value="0"<?php if($ksMSuchFilter==0) echo ' selected="selected"'?>>Suchfilter nicht anzeigen</option>
  <option value="1"<?php if($ksMSuchFilter==1) echo ' selected="selected"'?>>Suchfilter links über der Meldung</option>
  <option value="2"<?php if($ksMSuchFilter==2) echo ' selected="selected"'?>>Suchfilter mittig über der Meldung</option>
  <option value="3"<?php if($ksMSuchFilter==3) echo ' selected="selected"'?>>Suchfilter rechts über der Meldung</option>
  <option value="4"<?php if($ksMSuchFilter==4) echo ' selected="selected"'?>>Suchfilter links unter der Meldung</option>
  <option value="5"<?php if($ksMSuchFilter==5) echo ' selected="selected"'?>>Suchfilter mittig unter der Meldung</option>
  <option value="6"<?php if($ksMSuchFilter==6) echo ' selected="selected"'?>>Suchfilter rechts unter der Meldung</option>
  <option value="7"<?php if($ksMSuchFilter==7) echo ' selected="selected"'?>>Suchfilter links unter der Liste</option>
  <option value="8"<?php if($ksMSuchFilter==8) echo ' selected="selected"'?>>Suchfilter mittig unter der Liste</option>
  <option value="9"<?php if($ksMSuchFilter==9) echo ' selected="selected"'?>>Suchfilter rechts unter der Liste</option>
 </select>
 </td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Monatsangabe innerhalb der Datumsanzeigen in der Terminliste können als zweistellige Zahl oder als ausgeschriebener Monatsname erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatsformat</td>
 <td colspan="2"><input type="radio" class="admRadio" name="MonatMLang" value="0"<?php if($ksMonatMLang<1) echo ' checked="checked"'?> /> Monat als Zahl &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatMLang" value="1"<?php if($ksMonatMLang==1) echo ' checked="checked"'?>/> Monatsname kurz &nbsp; &nbsp; <input type="radio" class="admRadio" name="MonatMLang" value="2"<?php if($ksMonatMLang==2) echo ' checked="checked"'?>/> Monatsname lang &nbsp; &nbsp; (<span class="admMini">keine Empfehlung</i></span>)</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Vor den Wochen im Monatskalender/Wochenkalender kann die Wochen-Nummer eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Wochen-Nr.</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="MonWochNr" value="1"<?php if($ksMonWochNr) echo' checked="checked"'?>> Wochennummern einblenden<br />
 <input type="text" name="MonTxNr" value="<?php echo $ksMonTxNr?>" style="width:8em" /> als Spaltenüberschrift &nbsp; <span class="admMini">Empfehlung: <i>Nr.</i> oder <i>Wo</i> &nbsp; oder leer lassen</span><br />
 <input type="text" name="TxMWochNr" value="<?php echo $ksTxMWochNr?>" style="width:8em" /> als Textform mit Platzhalter #W  &nbsp; <span class="admMini">Empfehlung: <i>#W</i> oder <i>#W.&lt;br /&gt;Wo</i></span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Wenn ein Monat nicht mit Montag beginnt und nicht mit Sonntag endet entstehen leere Felder im Monatskalender.
Sollen diese Felder leer bleiben oder mit jeweiligen Tagesdatum des angrenzenden Monats aufgefüllt werden?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Monatsgrenzen</td>
 <td colspan="2"><input class="admRadio" type="radio" name="MonFremd" value="0"<?php if(!$ksMonFremd) echo' checked="checked"'?>> nicht auffüllen &nbsp; &nbsp;
 <input class="admRadio" type="radio" name="MonFremd" value="1"<?php if($ksMonFremd) echo' checked="checked"'?>> mit fremden Monatstagen auffüllen</td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Der Monatskalender/Wochenkalender kann nur die gültige Termine anzeigen
(d.h. nur die Termine, die bei den Einstellungen der Terminliste als nicht abgelaufen vereinbart sind)
oder auch abgelaufene Termine darstellen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Terminbereich</td>
 <td colspan="2"><input class="admRadio" type="radio" name="MonOhneAltes" value="0"<?php if(!$ksMonOhneAltes) echo' checked="checked"'?>> alle (auch abgelaufene) Termine &nbsp; &nbsp;
 <input class="admRadio" type="radio" name="MonOhneAltes" value="1"<?php if($ksMonOhneAltes) echo' checked="checked"'?>> nur gültige Termine</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Die Darstellung in den Tages-Zellen des Monatskalender/Wochenkalender kann individuell eingestellt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Zellenhöhe</td>
 <td colspan="2"><input type="text" name="MZellenHoehe" value="<?php echo $ksMZellenHoehe?>" style="width:25px;" /> Zeilen als minimale Höhe der Monatszellen <span class="admMini">(Empfehlung: ca. 4 Zeilen)</span><br><input type="text" name="WZellenHoehe" value="<?php echo $ksWZellenHoehe?>" style="width:25px;" /> Zeilen als minimale Höhe der Wochenzellen <span class="admMini">(Empfehlung: ca. 15 Zeilen)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datumsformat</td>
 <td colspan="2"><select name="MDatumsformat" size="1">
  <option value="0"<?php if($ksMDatumsformat==0) echo ' selected="selected"'?>>TT.</option>
  <option value="1"<?php if($ksMDatumsformat==1) echo ' selected="selected"'?>>TT.MM.</option>
  <option value="2"<?php if($ksMDatumsformat==2) echo ' selected="selected"'?>>TT.MMM</option>
  <option value="3"<?php if($ksMDatumsformat==3) echo ' selected="selected"'?>>TT.MM.JJ</option>
  <option value="4"<?php if($ksMDatumsformat==4) echo ' selected="selected"'?>>mm/dd/yy</option>
  <option value="5"<?php if($ksMDatumsformat==5) echo ' selected="selected"'?>>dd/mm/yy</option>
  <option value="6"<?php if($ksMDatumsformat==6) echo ' selected="selected"'?>>dd-mm-yy</option>
  <option value="7"<?php if($ksMDatumsformat==7) echo ' selected="selected"'?>>yy-mm-dd</option>
 </select> (gilt für das Tagesdatum in der erste Zeile jeder Datumszelle)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Linkziel bei Klick<br>auf Tagesdatum</td>
 <td colspan="2"><input class="admRadio" type="radio" name="MLinkZiel" value="-1"<?php if($ksMLinkZiel=='-1') echo' checked="checked"'?>> Tagesdatum nicht verlinkt<br>
 <input class="admRadio" type="radio" name="MLinkZiel" value="0"<?php if($ksMLinkZiel=='0') echo' checked="checked"'?>> immer Terminliste anzeigen<br>
 <input class="admRadio" type="radio" name="MLinkZiel" value="1"<?php if($ksMLinkZiel=='1') echo' checked="checked"'?>> bei einem Termin <i>Termindetails</i>, bei mehreren Terminen <i>Terminliste</i><br>
 <input class="admRadio" type="radio" name="MLinkZiel" value="2"<?php if($ksMLinkZiel=='2') echo' checked="checked"'?>> immer Termindetails, bei mehreren Terminen irgendeinen davon<br>
 <input class="admCheck" type="checkbox" name="MLinkLeerNeu" value="1"<?php if($ksMLinkLeerNeu) echo' checked="checked"'?>> Tagesdatum leerer Tageszellen auf <i>Termineingabe</i> verlinken
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminanzahl</td>
 <td colspan="2">
  <input type="text" name="MTerminZahl" value="<?php echo $ksMTerminZahl?>" style="width:25px;" /> maximal gezeigte Termine pro Tag, mehr wird mit Punkten .... symbolisiert
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutauswahl</td>
 <td colspan="2">
  <input class="admRadio" type="radio" name="MEigenesLayout" value="0"<?php if(!$ksMEigenesLayout) echo' checked="checked"'?>> Standardlayout verwenden &nbsp;
  <input class="admRadio" type="radio" name="MEigenesLayout" value="1"<?php if($ksMEigenesLayout) echo' checked="checked"'?>> eigenes Layout aus der Schablone <i>kalMonatsZelle.htm</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Detaildarstellung</td>
 <td colspan="2">
  <input class="admRadio" type="radio" name="MTerminDetail" value="1"<?php if($ksMTerminDetail) echo' checked="checked"'?>> Details zu jedem Termin andeuten &nbsp;
  <input class="admRadio" type="radio" name="MTerminDetail" value="0"<?php if(!$ksMTerminDetail) echo' checked="checked"'?>> nur die Anzahl der Termine pro Tag anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorschautext<br>zusammensetzen<br>aus</td>
 <td colspan="2">
 <select name="MDetail1Fld" size="1" style="width:160px;margin-top:2px"><option value="-1">---</option><?php echo $sOpt1Fld?></select> (1. Terminfeld), &nbsp; &nbsp;
 <input type="text" name="MDetail1Trn" value="<?php echo $ksMDetail1Trn?>" style="width:45px;margin-top:2px" /> (1. Trennzeichen)<br>
 <select name="MDetail2Fld" size="1" style="width:160px;margin-top:2px"><option value="-1">---</option><?php echo $sOpt2Fld?></select> (2. Terminfeld), &nbsp; &nbsp;
 <input type="text" name="MDetail2Trn" value="<?php echo $ksMDetail2Trn?>" style="width:45px;margin-top:2px;" /> (2. Trennzeichen)<br>
 <select name="MDetail3Fld" size="1" style="width:160px;margin-top:2px"><option value="-1">---</option><?php echo $sOpt3Fld?></select> (3. Terminfeld), &nbsp; &nbsp;
 <input type="text" name="MDetail3Trn" value="<?php echo $ksMDetail3Trn?>" style="width:45px;margin-top:2px;" /> (3. Trennzeichen)<br>
 <select name="MDetail4Fld" size="1" style="width:160px;margin-top:2px"><option value="-1">---</option><?php echo $sOpt4Fld?></select> (4. Terminfeld)<br>
 <span class="admMini"><u>Hinweis</u>: als Trennzeichen sind Komma, Bindestrich, Doppelpunkt oder Leerzeichen, aber auch einfache HTML-Zeichen wie Zeilenwechsel &lt;br&gt; oder &lt;br /&gt; zulässig</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Feldinhalt kürzen</td>
 <td colspan="2"><input type="text" name="MDetailKuerzen" value="<?php echo $ksMDetailKuerzen?>" style="width:25px;" />
 max. Länge aus Platzgründen pro Feld des Vorschautextes <span class="admMini">(leer lassen für unbegrenzt)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorschautext färben</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="MonatsKateg" value="1"<?php if($ksMonatsKateg) echo ' checked="checked"'?> /> Den Vorschautext in den Kategoreifarben einfärben</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Klickschalter-<br>Icons</td>
 <td colspan="2">
 <img src="<?php echo $sHttp?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info">
 <input type="checkbox" class="admCheck" name="MonatsInfo" value="1"<?php if($ksMonatsInfo) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">Infofunktion</span>
 <input type="checkbox" class="admCheck" name="GastMoInfo" value="1"<?php if($ksGastMoInfo) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconErinnern.gif" width="16" height="16" border="0" align="top" title="Erinnerung">
 <input type="checkbox" class="admCheck" name="MonatsErinn" value="1"<?php if($ksMonatsErinn) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">Erinnerungsfunktion</span>
 <input type="checkbox" class="admCheck" name="GastMoErinn" value="1"<?php if($ksGastMoErinn) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigunh">
 <input type="checkbox" class="admCheck" name="MonatsBenachr" value="1"<?php if($ksMonatsBenachr) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">Benachrichtigungsfunktion</span>
 <input type="checkbox" class="admCheck" name="GastMoBenachr" value="1"<?php if($ksGastMoBenachr) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="iCal-Export">
 <input type="checkbox" class="admCheck" name="MonatsICal" value="1"<?php if($ksMonatsICal) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">iCal-Export</span>
 <input type="checkbox" class="admCheck" name="GastMoICal" value="1"<?php if($ksGastMoICal) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="Zusagen">
 <input type="checkbox" class="admCheck" name="MonatsZusage" value="1"<?php if($ksMonatsZusage) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">Termin zusagen</span>
 <input type="checkbox" class="admCheck" name="GastMoZusage" value="1"<?php if($ksGastMoZusage) echo ' checked="checked"'?> /> auch für Gäste<br>
 <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="Zusagen zeigen">
 <input type="checkbox" class="admCheck" name="MonatZeigeZusage" value="1"<?php if($ksMonatZeigeZusage) echo ' checked="checked"'?> /> <span style="width:180px;display:inline-block;">Zusagen zeigen</span>
 <input type="checkbox" class="admCheck" name="GastMZeigeZusage" value="1"<?php if($ksGastMZeigeZusage) echo ' checked="checked"'?> /> auch für Gäste<br>
 </td>
</tr>
<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Druck des Monatskalenderblattes sind folgenden Einstellungen wählbar.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Drucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="EigeneMDruckZelle" value="0"<?php if(!$ksEigeneMDruckZelle) echo ' checked="checked"'?> /> tabellarisches Standardlayout &nbsp; <input type="radio" class="admRadio" name="EigeneMDruckZelle" value="1"<?php if($ksEigeneMDruckZelle) echo ' checked="checked"'?> /> eigenes Layout aus der Schablone <i>kalDruckMonatsZelle.htm</i></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Standarddrucklayout</td>
 <td colspan="2"><input type="radio" class="admRadio" name="DruckMFarbig" value="0"<?php if(!$ksDruckMFarbig) echo ' checked="checked"'?> /> simpel &nbsp; &nbsp; <input type="radio" class="admRadio" name="DruckMFarbig" value="1"<?php if($ksDruckMFarbig) echo ' checked="checked"'?> /> formatiert (im CSS-Stil der Bildschirm-Terminliste)</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">E-Mail-<br>Druckeinschränkung</td>
 <td colspan="2"><input type="checkbox" class="admCheck" name="DruckLMailOffen" value="1"<?php if($ksDruckLMailOffen) echo ' checked="checked"'?> /> E-Mail-Adressen offen lesbar in der Druckliste darstellen
 <div class="admMini">Empfehlung: möglichst <i>nicht</i> aktivieren, weil auch Roboter/Spider die Druckseite einsehen könnten</div>
 </td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>



<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="3" class="admSpa2">Beim Überfahren eines Termins mit der Maus kann eine Vorschaubox mit Termindetails eingeblendet werden.
Diese kann automatisch verschwinden, sobald die Maus den Termin wieder verläßt oder aber auch erst beim nächsten Mausklick auf die Seite.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Detailvorschau<br>einblenden</td>
 <td colspan="2"><input class="admCheck" type="checkbox" name="VBoxMon" value="1"<?php if($ksVBoxMon) echo' checked="checked"'?>> beim Überfahren eines Termins mit der Maus die Vorschaubox einblenden<br>
 <input type="text" name="VBoxWarten" value="<?php echo $ksVBoxWarten?>" style="width:40px;" /> Verzögerungszeit in Millisekunden bis zum einblenden <span class="admMini">(Empfehlung: ca. 800)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorschauende</td>
 <td colspan="2"><input type="radio" class="admRadio" name="VBoxAutoAus" value="1"<?php if($ksVBoxAutoAus) echo ' checked="checked"'?> /> Vorschaubox automatisch ausblenden &nbsp; <input type="radio" class="admRadio" name="VBoxAutoAus" value="0"<?php if(!$ksVBoxAutoAus) echo ' checked="checked"'?>/> ausblenden bei Mausklick</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Vorschauboxgröße</td>
 <td colspan="2">
  <input type="text" name="VBoxWidth" value="<?php echo $ksVBoxWidth?>" size="3" style="width:32px;" /> px <span style="width:100px;display:inline-block">Boxbreite</span>
  <input type="text" name="VBoxHeight" value="<?php echo $ksVBoxHeight?>" size="3" style="width:32px;" /> px Boxhöhe</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Boxverschiebung</td>
 <td colspan="2">
  <input type="text" name="VBoxHOffset" value="<?php echo $ksVBoxHOffset?>" size="3" style="width:32px;" /> px <span style="width:100px;display:inline-block">waagerecht</span>
  <input type="text" name="VBoxVOffset" value="<?php echo $ksVBoxVOffset?>" size="3" style="width:32px;" /> px senkrecht &nbsp; &nbsp; (<span class="admMini">Standardwert: 0</span>)
  <div class="admMini"><u>Erklärung</u>: In Einzelfällen kann es bei mittels PHP-Befehl <i>include</i> eingebundenen Monatskalendern dazu kommen, dass die Vorschaubox nicht an der Mausposition sondern versetzt erscheint. Dann können Sie versuchen, die Erscheinensposition der Box mit diesen zwei Parametern zu korrigieren.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">&nbsp;</td>
 <td>Vorschauboxzeile für Gäste / für angemeldete Benutzer <a href="<?php echo ADM_Hilfe?>LiesMich.htm#0.0" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
 <td style="width:2%">optionale CSS-Styles <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.6c.CSS" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">laufende Nummer <a href="<?php echo ADM_Hilfe?>LiesMich.htm#2.3.Nummer" target="hilfe" onclick="hlpWin(this.href);return false;"><img src="hilfe.gif" width="13" height="13" border="0" title="Hilfe"></a><div class="admMini">Typ <i>Zählnummer</i></div></td>
 <td><select name="F0" size="1" style="width:42px;"><option value="0">---</option><option value="1"<?php if($aListenFeld[0]==1) echo ' selected="selected"'?>>0</option></select> / <select name="N0" size="1" style="width:42px;"><option value="0">---</option><option value="1"<?php if($aNListenFeld[0]==1) echo ' selected="selected"'?>>0</option></select>
 mit <input type="text" name="VBoxNrStellen" value="<?php echo $ksVBoxNrStellen?>" size="1" style="width:18px;" /> stelliger lfd. Nr.
 als <input type="text" name="VBoxTxNr" value="<?php echo $ksVBoxTxNr?>" size="10" style="width:75px;" /></td>
 <td><input type="text" name="Z0" style="width:220px" value="<?php echo $kal_SpaltenStil[0]?>" /></td>
</tr>
<?php
 include('feldtypenInc.php');
 $sOpt='<option value="0">---</option>'; for($i=1;$i<$nFelder;$i++) $sOpt.='<option value="'.$i.'">'.$i.'</option>';
 for($i=1;$i<$nFelder;$i++){
  $t=$kal_FeldType[$i]; $sFN=$kal_FeldName[$i];
  if(!$k=(isset($aListenFeld[$i])?$aListenFeld[$i]:-1)) $sO=$sOpt; else $sO=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if(!$k=(isset($aListenNFeld[$i])?$aListenNFeld[$i]:-1)) $sN=$sOpt; else $sN=substr_replace($sOpt,'selected="selected" ',strpos($sOpt,'value="'.$k.'"'),0);
  if($i!=1){if($t=='v') $sO='<option value="0">---</option>';} //versteckt
  //else{$sO=substr($sO,strpos($sO,'<option',1)); $sN=substr($sN,strpos($sN,'<option',1));} //Datum
  if($t=='u'){
   array_splice($kal_NutzerFelder,1,1); $nNFz=count($kal_NutzerFelder);
   $sNOpt='<option value="0">--</option><option value="2">'.$kal_NutzerFelder[2].'</option>';
   for($j=4;$j<$nNFz;$j++) $sNOpt.='<option value="'.$j.'">'.$kal_NutzerFelder[$j].'</option>';
  }
?>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:normal;width:0%;"><?php echo sprintf('%02d',$i).')&nbsp;'.$sFN.'<div class="admMini">(Typ <i>'.$aTyp[$t].'</i>)</div>'?></td>
 <td>
<?php if($t!='c'&&$t!='p'&&substr($sFN,0,5)!='META-'&&$sFN!='TITLE'){?>
 <select name="F<?php echo $i?>" size="1" style="width:42px;"><?php echo $sO?></select> / <select name="N<?php echo $i?>" size="1" style="width:42px;"><?php echo $sN?></select> &nbsp; &nbsp; &nbsp;
<?php if($t!='l'&&$t!='e'&&$t!='v'&&$t!='f'&&$t!='#'){?>
 <input type="checkbox" class="admCheck" name="L<?php echo $i?>" value="1"<?php if(isset($aLinkFeld[$i])&&$aLinkFeld[$i]) echo ' checked="checked"'?> /> als&nbsp;Detaillink
<?php } if($t=='u'){ ?>
 <div><select name="NutzerVBoxFld" style="width:140px;"><?php echo str_replace('value="'.($ksNutzerVBoxFld).'"','value="'.($ksNutzerVBoxFld).'" selected="selected"',$sNOpt)?></select> / <select name="NNutzerVBoxFld" style="width:140px;"><?php echo str_replace('value="'.($ksNNutzerVBoxFld).'"','value="'.($ksNNutzerVBoxFld).'" selected="selected"',$sNOpt)?></select></div>
<?php } ?>
 </td>
 <td><input type="text" name="S<?php echo $i?>" style="width:220px;" value="<?php echo (isset($aFeldStil[$i])?$aFeldStil[$i]:'')?>" />
<?php }else{?>
 &nbsp;----<input type="hidden" name="F<?php echo $i?>" value="0" />
 </td>
 <td>&nbsp;
<?php }?>
 </td>
</tr>
<?php }?>
<tr class="admTabl">
 <td class="admSpa1">Zusagensummen-<br>zeile<br>mit dem Inhalt</td>
 <td colspan="2">Summenzeile vor Zeile <select name="VBoxZusagZ" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxZusagZ==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="VBoxTxZusagZ" value="<?php echo $ksVBoxTxZusagZ?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastZusagZ" value="1"<?php if($ksVBoxGastZusagZ) echo ' checked="checked"'?> /> auch für Gäste<br>
 <input type="text" name="VBoxTxZusagZMuster" value="<?php echo $ksVBoxTxZusagZMuster?>" size="25" style="width:160px;margin-top:3px;" /> <span class="admMini">(#Z: Zusagen bisher, #K: Kapazität, #R restliche freie Plätze)</span></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Für den Fall, dass die Vorschaubox erst bei einem Mausklick wieder ausgeblendet wird können Felder wie oben als Detaillink fungieren sowie nachfolgende zusätzliche Klickschalter eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Informations-<br>funktion</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconInfo.gif" width="16" height="16" border="0" align="top" title="Info"> Infozeile vor Zeile <select name="VBoxInfo" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxInfo==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxInfoSenden" value="<?php echo $ksTxInfoSenden?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastInfo" value="1"<?php if($ksVBoxGastInfo) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Erinnerungs-<br>service</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconErinnern.gif" width="16" height="16" border="0" align="top" title="Erinnerung"> Servicezeile vor Zeile <select name="VBoxErinn" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxErinn==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxErinnService" value="<?php echo $ksTxErinnService?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastErinn" value="1"<?php if($ksVBoxGastErinn) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benachrichtigungs-<br>service</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconNachricht.gif" width="16" height="16" border="0" align="top" title="Benachrichtigung"> Servicezeile vor Zeile <select name="VBoxBenachr" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxBenachr==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxBenachrService" value="<?php echo $ksTxBenachrService?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastBenachr" value="1"<?php if($ksVBoxGastBenachr) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Druckzeile</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconDrucken.gif" width="16" height="16" border="0" align="top" title="Drucken"> Druck-Zeile&nbsp; vor Zeile <select name="VBoxDruck" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxDruck==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxDrucken" value="<?php echo $ksTxDrucken?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastDruck" value="1"<?php if($ksVBoxGastDruck) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">iCal-Export</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconExport.gif" width="16" height="16" border="0" align="top" title="iCal-Export"> Servicezeile vor Zeile <select name="VBoxICal" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxICal==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxCalZeile" value="<?php echo $ksTxCalZeile?>" size="15" style="width:100px;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastICal" value="1"<?php if($ksVBoxGastICal) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Termin zusagen</td>
 <td colspan="2"><img src="<?php echo $sHttp?>grafik/iconZusage.gif" width="16" height="16" border="0" align="top" title="Zusagen"> Zusagezeile vor Zeile <select name="VBoxZusage" size="1"><option value="-1">--</option><?php for($i=1;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($ksVBoxZusage==$i?' selected="selected"':'').'>'.$i.'</option>'?></select>
 als <input type="text" name="TxZusageZeile" value="<?php echo $ksTxZusageZeile?>" size="15" style="width:8em;" /> einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastZusage" value="1"<?php if($ksVBoxGastZusage) echo ' checked="checked"'?> /> auch für Gäste<br>
 <input type="checkbox" class="admCheck" name="VBoxZeigeZusage" value="1"<?php if($ksVBoxZeigeZusage) echo ' checked="checked"'?> /> <img src="<?php echo $sHttp?>grafik/iconVorschau.gif" width="16" height="16" border="0" align="top" title="Zusagen zeigen"> Zusatz-Icon für Zusagenliste einblenden,
 <input type="checkbox" class="admCheck" name="VBoxGastZeigeZusage" value="1"<?php if($ksVBoxGastZeigeZusage) echo ' checked="checked"'?> /> auch für Gäste</td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Falls ein beliebiger Termin leere Felder enthält
können diese in der Vorschaubox als Zeilen mit leerem Inhalt angezeigt oder die betreffenden leeren Zeilen aus der Vorschauanzeige ausgeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">leere Zeilen</td>
 <td colspan="2"><input type="radio" class="admRadio" name="VBoxLeeres" value=""<?php if(!$ksVBoxLeeres) echo ' checked="checked"'?> /> leere Zeilen nicht darstellen &nbsp; <input type="radio" class="admRadio" name="VBoxLeeres" value="1"<?php if($ksVBoxLeeres) echo ' checked="checked"'?>/> leere Zeilen anzeigen</td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Layoutauswahl</td>
 <td colspan="2">
  <input class="admRadio" type="radio" name="VBoxEigenesLayout" value="0"<?php if(!$ksVBoxEigenesLayout) echo' checked="checked"'?>> Standardlayout verwenden &nbsp;
  <input class="admRadio" type="radio" name="VBoxEigenesLayout" value="1"<?php if($ksVBoxEigenesLayout) echo' checked="checked"'?>> eigenes Layout aus der Schablone <i>kalTerminBox.htm</i></td>
</tr>

<tr class="admTabl"><td colspan="3" class="admSpa2">Sofern das Monatsblatt
mit der Vorschaubox direkt aufgerufen wird (auch in einem i-Frame)
wird als Verweisziel für die Links aus der Vorschaubox heraus
automatisch das Kalenderscript <i>kalender.php</i> angenommen,
sofern Sie nicht extra ein anderes PHP-Script anstatt des Kalenders hier angeben.<br />
Wenn die Monatsliste in eine Ihrer Seiten per PHP-Befehl <i>include()</i> integriert wurde,
müssen Sie hier hier dieses Script selbst als Verweisziel angeben.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Verweisziel</td>
 <td colspan="2"><input style="width:100%" type="text" name="VBoxLink" value="<?php echo $ksVBoxLink?>" />
 <div class="admMini">leer lassen oder Scriptname, eventuell mit absoluter Pfadangabe oder als vollständiger URL sogar mit QueryString</div></td>
</tr>


</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function txtVarL($Var){return (isset($_POST[$Var])?str_replace('"',"'",stripslashes($_POST[$Var])):'');}
?>