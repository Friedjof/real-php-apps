<?php
 //Fragenfolge einmalig zu Beginn bilden
 $sFolge=''; $nFragen=0; $sZeit=time(); $bSessOK=true; //Rueckgabewerte
 $sFlgNam=''; $sFlgInh=''; $aNr=array(); $aF=array(); $aR=array(); $nAlle=0; $t='';
 // FRA_TestFolgeName: Parameter fra_Folgename=name
 // FRA_TestSpontan:   Parameter fra_Spontantest=1
 // FRA_TestKategorie: Parameter fra_Kategorie=name
 // FRA_BFolgeName:    Benutzerfolge nach Login
 // FRA_FolgeName:     gespeicherte Folge
 // FRA_FolgeSpontan:  gespeicherte Spontanfolge
 // FRA_KategorieFilter: gespeicherte Kategorie
 if(!$sFlgNam=FRA_TestFolgeName) if(!defined('FRA_TestSpontan')||!($sFlgInh=FRA_FolgeSpontan)) if(!FRA_TestKategorie) if(!$sFlgNam=fFraTx(defined('FRA_BFolgeName')?FRA_BFolgeName:FRA_FolgeName)) $sFlgInh=FRA_FolgeSpontan;
 if($sFlgNam){ //Folgename gegeben, Inhalt holen
  $sFlgInh=''; if(FRA_TestFolgeName=='') $sNeuFolgeName=$sFlgNam;  echo $sFlgNam;
  if(FRA_Zeichensatz==2) $sFlgNam=iconv('UTF-8','ISO-8859-1//TRANSLIT',$sFlgNam);
  if(!FRA_SQL){
   $aF=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $p=strlen($sFlgNam)+1;
   if(is_array($aF)) foreach($aF as $t) if(substr($t,0,$p)==$sFlgNam.';'){ //gefunden
    $aR=explode(';',rtrim($t)); $sFlgInh=$aR[1]; $sProSeite=(isset($aR[2])?(strlen($aR[2])>0?(int)$aR[2]:''):'');
    if(FRA_NutzerMitCode&&strlen(FRA_Session)&&substr(FRA_Session,4,1)!='9') if(isset($aR[4])&&$aR[4]>'0') if($aR[5]!=(isset($_GET['fra_Code'])?$_GET['fra_Code']:'#')) define('FRA_AktivCodeErr',true);
    if(FRA_TeilnehmerMitCode&&substr(FRA_Session,4,1)=='9') if(isset($aR[3])&&$aR[3]>'0') if($aR[5]!=(isset($_GET['fra_Code'])?$_GET['fra_Code']:'#')) define('FRA_AktivCodeErr',true);
    break;
   }
  }elseif($bSQLOpen){//SQL-Daten
   if($rR=$DbO->query('SELECT Folge,Fragen FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgNam.'"')){
    if($aR=$rR->fetch_row()) $sFlgInh=$aR[1]; $rR->close();
    if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabT.' WHERE Folge="'.$sFlgNam.'"')){
     if($aR=$rR->fetch_row()){
      $sProSeite=(isset($aR[2])?(strlen($aR[2])>0?(int)$aR[2]:''):'');
      if(FRA_NutzerMitCode&&strlen(FRA_Session)&&substr(FRA_Session,4,0)!='9') if(isset($aR[4])&&$aR[4]>'0') if($aR[5]!=(isset($_GET['fra_Code'])?$_GET['fra_Code']:'#')) define('FRA_AktivCodeErr',true);
      if(FRA_TeilnehmerMitCode&&substr(FRA_Session,4,0)=='9') if(isset($aR[3])&&$aR[3]>'0') if($aR[5]!=(isset($_GET['fra_Code'])?$_GET['fra_Code']:'#')) define('FRA_AktivCodeErr',true);
     }$rR->close();
  }}}
 }

 if($sFlgInh){ //Folge bilden
  $aNr=explode(',',str_replace(';',',',str_replace(' ','',$sFlgInh))); $nAlle=count($aNr);
 }
//echo $sFlgInh;
 if(strpos($sFlgInh,'-')>0){ //Bereichsangaben
  $aNb=array();
  for($k=0;$k<$nAlle;$k++) if(($t=$aNr[$k])&&($p=strpos($t,'-'))){
   $n1=(int)substr($t,0,$p); $n2=(int)substr($t,$p+1);
   if($n2>$n1&&$n1>0){$aNh=array(); for($j=$n1;$j<=$n2;$j++) $aNh[]=$j; $aNb[$k]=$aNh;}
  }
  krsort($aNb); foreach($aNb as $j=>$aNh) array_splice($aNr,$j,1,$aNh); $nAlle=count($aNr);
 }

 if(strpos($sFlgInh,'x')>0) for($k=0;$k<$nAlle;$k++) if(($t=$aNr[$k])&&($p=strpos($t,'x'))){ //t: Kategorienelement
  $nFragen=(int)substr($t,0,$p); $sKat=substr($t,$p+1); $aK=array(); //pro Kategorie
  if(!FRA_SQL){ //Textdaten
   for($i=1;$i<$nSaetze;$i++){
    $aF=explode(';',substr($aD[$i],0,250));
    if($aF[1]=='1'||FRA_AuchInaktive) if($aF[2]!='1'||strlen(FRA_Session)>0||FRA_AuchVersteckte) if(strpos('#'.$aF[3],$sKat)>0) $aK[]=$aF[0];
   }
  }elseif($bSQLOpen){ //SQL-Daten
   if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabF.' WHERE aktiv'.(!FRA_AuchInaktive?'="1"':'<"2"').(strlen(FRA_Session)==0&&!FRA_AuchVersteckte?' AND versteckt<>"1"':'').' AND Kategorie LIKE "%'.$sKat.'%" ORDER BY Nummer')){
    while($aF=$rR->fetch_row()) $aK[]=$aF[0]; $rR->close();
   }
  }
  $nVorrat=count($aK); $nFragen=min($nFragen,$nVorrat--); $aF=array(); //Fragen pro Kategorie nach aN
  for($j=0;$j<$nFragen;$j++){
   if(FRA_Zufallsfolge){$n=rand(0,$nVorrat-$j); $t=$aK[$n]; array_splice($aK,$n,1);} else $t=$aK[$j];
   if(!in_array($t,$aNr)) $aF[]=$t; //keine Doppelten
  }
  array_splice($aNr,$k,1,$aF); $nAlle=count($aNr);
 }

 if(count($aNr)<=0){ //keine externe Folge also Folge hier bilden
  if(!$sKatFlt=FRA_TestKategorie) $sKatFlt=FRA_KategorieFilter;
  if(!FRA_SQL){ //Textdaten
   for($i=1;$i<$nSaetze;$i++){
    $aF=explode(';',substr($aD[$i],0,250));
    if($aF[1]=='1'||FRA_AuchInaktive) if($aF[2]!='1'||strlen(FRA_Session)>0||FRA_AuchVersteckte) if(strlen($sKatFlt)==0||!(strpos($aF[3],$sKatFlt)===false)) $aNr[]=$aF[0];
   }
  }elseif($bSQLOpen){ //SQL-Daten
   if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabF.' WHERE aktiv'.(!FRA_AuchInaktive?'="1"':'<"2"').(strlen(FRA_Session)==0&&!FRA_AuchVersteckte?' AND versteckt<>"1"':'').(strlen($sKatFlt)==0?'':' AND Kategorie LIKE "%'.$sKatFlt.'%"').' ORDER BY Nummer')){
    while($aF=$rR->fetch_row()) $aNr[]=$aF[0]; $rR->close();
   }
  }
 }else{ //externe Folge auf Vorhandensein und Inaktivitaet der Fragen pruefen
  $sNr='#;'; $nFragen=count($aNr)-1;
  if(!FRA_SQL){ //Textdaten
   for($i=1;$i<$nSaetze;$i++){
    $aF=explode(';',substr($aD[$i],0,250));
    if($aF[1]=='1'||FRA_AuchInaktive) if($aF[2]!='1'||strlen(FRA_Session)>0||FRA_AuchVersteckte) $sNr.=$aF[0].';';
   }
  }elseif($bSQLOpen){ //SQL-Daten
   if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabF.' WHERE aktiv'.(!FRA_AuchInaktive?'="1"':'<"2"').(strlen(FRA_Session)==0&&!FRA_AuchVersteckte?' AND versteckt<>"1"':'').' ORDER BY Nummer')){while($aF=$rR->fetch_row()) $sNr.=$aF[0].';'; $rR->close();}
  }
  for($i=$nFragen;$i>=0;$i--) if(strpos($sNr,';'.$aNr[$i].';')==false) array_splice($aNr,$i,1);
 }

 $nFragen=count($aNr); $nAlle=$nFragen; if(FRA_WenigerFragen) $nFragen=min($nFragen,FRA_WenigerFragen);
 if(FRA_Zufallsfolge&&(FRA_MischKategorie||empty($sFlgNam))){ //zufaellige Reihenfolge
  $k=count($aNr)-1; $j=max(0,FRA_WenigerFragen?$k-FRA_WenigerFragen+1:0); mt_srand($sZeit);
  for($i=$k;$i>=$j;$i--){$aF=array_splice($aNr,mt_rand(0,$i),1); $sFolge.=$aF[0].';';}
 }elseif(!FRA_Rueckwaerts) for($i=0;$i<$nFragen;$i++) $sFolge.=$aNr[$i].';'; //natuerliche Reihenfolge
 else{$j=$nAlle-$nFragen; for($i=$nAlle-1;$i>=$j;$i--) $sFolge.=$aNr[$i].';';} //absteigende Reihenfolge
 $sFolge=substr($sFolge,0,-1);

 if($sSes=FRA_Session){ //Session pruefen
  $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))!=$n){$MeldS=FRA_TxSessionUngueltig; $bSessOK=false;}
  if(substr($sSes,9)<(time()>>8)){$MeldS=FRA_TxSessionZeit; $bSessOK=false;}
 }

if(!function_exists('fFraDeCode')){
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}}
?>