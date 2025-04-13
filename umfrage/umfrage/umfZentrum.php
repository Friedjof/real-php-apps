<?php
if(!function_exists('fUmfSeite') ){ //bei direktem Aufruf
 function fUmfSeite(){return fUmfZentrum(true);}
}

function fUmfZentrum($bDirekt){ //Seiteninhalt
 $sMeld=''; $MTyp='Fehl'; $DbO=NULL; $bSes=false; $sNam='???'; $aF=array(); $aC=array();

 if($sSes=($bDirekt?UMF_Session:UMF_NeuSession)){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $bSes=true;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(UMF_Nutzerverwaltung=='vorher'||UMF_Registrierung=='vorher') $Meld=UMF_TxNutzerLogin;

 if($bSes){
  if(UMF_SQL){ // SQL oeffnen
   $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa);
   if(!mysqli_connect_errno()){if(UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);}else{$DbO=NULL; $Meld=UMF_TxSqlVrbdg;}
  }

  if(UMF_NutzerFrist>0){
   $aNutzFld=explode(';',UMF_NutzerFelder); $nGueltPos=array_search('GUELTIG_BIS',$aNutzFld);
  }else $nGueltPos=0;

  if(!UMF_SQL){ //Nutzer und Umfragen holen
   $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=((int)$sNId).';'; $n=strlen($s);
   for($i=1;$i<$nSaetze;$i++){
    if(substr($aD[$i],0,$n)==$s){ //gefunden
     $aN=explode(';',$aD[$i]); $sNam=fUmfDeCode($aN[2]);
     if($nGueltPos==0||!isset($aN[$nGueltPos])||$aN[$nGueltPos]==''||$aN[$nGueltPos]>=date('Y-m-d')){ //gueltig
      if(UMF_NutzerUmfragen){
       $a=@file(UMF_Pfad.UMF_Daten.UMF_Zuweisung); $nZhl=count($a); $s=(int)$sNId.';'; $l=strlen($s);
       for($j=1;$j<$nZhl;$j++) if(substr($a[$j],0,$l)==$s){ //Nutzerzuordnung gefunden
        $sZw=rtrim(substr($a[$j],$l)); break;
     }}}else $Meld=UMF_TxNutzerAblauf.' - '.UMF_TxPassiv; //ungueltig
     break;
   }}
  }elseif($DbO){ //bei SQL
   if($rR=$DbO->query('SELECT Nummer,Benutzer'.($nGueltPos>0?',dat_'.$nGueltPos:'').' FROM '.UMF_SqlTabN.' WHERE Nummer="'.((int)$sNId).'"')){
    $a=$rR->fetch_row(); $rR->close(); if($a[0]==(int)$sNId) $sNam=$a[1];
    if($nGueltPos==0||$a[2]==''||$a[2]>=date('Y-m-d')){ //gueltig
     if(UMF_NutzerUmfragen) if($rR=$DbO->query('SELECT Nummer,Umfragen FROM '.UMF_SqlTabZ.' WHERE Nummer="'.$sNId.'"')){
      if($a=$rR->fetch_row()) $sZw=$a[1]; $rR->close(); //Nutzerzuordnung gefunden
     }
    }else $Meld=UMF_TxNutzerAblauf.' - '.UMF_TxPassiv; //ungueltig
  }}//SQL

  if(isset($sZw)){ //Nutzerzuordnungen abarbeiten
   $bNutzerNormUmfrage=false; $bNutzerAlleUmfrage=false;
   for($i=1;$i<=26;$i++){$k=chr(64+$i); $s=constant('UMF_Umfr'.$k); if(substr($s,0,1)!=';'){$a=explode(';',$s); if($a[1]){$aF[$k]=$a[3]; $aC[$k]=($a[4]?true:false);}}}
   if(strlen($sZw)){ // Nutzer hat Zuordnungen
    $sZw='#;'.$sZw.';'; $sHeute=date('Y-m-d');
    if($p=strpos($sZw,';0:')){ //Standardumfrage 0
     $w=substr($sZw,$p+3); $w=substr($w,0,strpos($w,';'));
     if(substr($w,0,3)=='bis'){if(substr($w,3)>=$sHeute) $bNutzerNormUmfrage=true;} elseif(substr($w,0,2)=='ab'){if(substr($w,2)<=$sHeute) $bNutzerNormUmfrage=true;}
     elseif(substr($w,0,2)=='am'){if(substr($w,2)==$sHeute) $bNutzerNormUmfrage=true;} elseif(strlen($w)==0||$w>'0x') $bNutzerNormUmfrage=true;
    }
    foreach($aF as $k=>$v) if($p=strpos($sZw,';'.$k.':')){ //Umfragen A..Z
     $w=substr($sZw,$p+3); $w=substr($w,0,strpos($w,';')); $bUfr=false;
     if(substr($w,0,3)=='bis'){if(substr($w,3)>=$sHeute) $bUfr=true;} elseif(substr($w,0,2)=='ab'){if(substr($w,2)<=$sHeute) $bUfr=true;}
     elseif(substr($w,0,2)=='am'){if(substr($w,2)==$sHeute) $bUfr=true;} elseif(strlen($w)==0||$w>'0x') $bUfr=true; elseif(substr($w,0,1)=='0') $aF[$k]='';
     if($bUfr) $bNutzerAlleUmfrage=true; else $aF[$k]='';
    }else $aF[$k]='';
  }}else{ // ohne Nutzerzuordnung
   $bNutzerNormUmfrage=UMF_NutzerNormUmfrage;
   if($bNutzerAlleUmfrage=UMF_NutzerAlleUmfrage){
    for($i=1;$i<=26;$i++){$k=chr(64+$i); $s=constant('UMF_Umfr'.$k); if(substr($s,0,1)!=';'){$a=explode(';',$s); if($a[1]){$aF[$k]=$a[3]; $aC[$k]=($a[4]?true:false);}}}
  }}

  if(empty($Meld)){
   if(!defined('UMF_AktivCodeErr')) $Meld=UMF_TxFuer.' &quot;'.$sNam.'&quot;';
   else $Meld='<span style="color:#b02">'.UMF_TxAktivCodeNoetig.'</span>'; $MTyp='Meld';
  }

  $X='<p class="umfMeld" style="font-size:1.2em">'.fUmfTx(UMF_TxBenutzerzentrum).'</p>';
  $X.="\n".'<p class="umf'.$MTyp.'">'.fUmfTx($Meld).'</p>'; $nNr=0;
  $X.="\n".'<table class="umfMenu" border="0" cellpadding="0" cellspacing="0">';
  if($MTyp!='Fehl'){
   if($bNutzerNormUmfrage) $X.=fUmfMenuZeile(UMF_TxStandardUmf,'frage',$sSes,'',UMF_StdUmfrCode>0);
   if($bNutzerAlleUmfrage){reset($aF); foreach($aF as $k=>$s) if($s) $X.=fUmfMenuZeile($s.' ('.$k.')','frage',$sSes,'umf_Umfrage='.$k,$aC[$k]);}
   if(UMF_NutzerErgebnis) $X.=fUmfMenuZeile(UMF_TxErgebnisListe,'ergebnis',$sSes);
   if(UMF_NutzerErgebnis&&UMF_NutzerGrafik) $X.=fUmfMenuZeile(UMF_TxAuswerteGrafik,'grafik',$sSes);
   if(UMF_NutzerErgebnis&&$bNutzerAlleUmfrage){reset($aF); foreach($aF as $k=>$s) if($s) $X.=fUmfMenuZeile($s.' ('.$k.')','grafik',$sSes,'umf_Umfrage='.$k);}
   if(UMF_NutzerDrucken) $X.=fUmfMenuZeile(UMF_TxDrucken,'drucken',$sSes);
   if(UMF_NutzerAendern) $X.=fUmfMenuZeile(UMF_TxNutzerAendern,'benutzer',$sSes);
  }
  $X.=fUmfMenuZeile(UMF_TxAbmelden,'login');
  $X.="\n</table>";
 }else $X=' <p class="umfFehl">'.fUmfTx($Meld)."</p>\n"; //Sessionsproblem
 return $X;
}

function fUmfMenuZeile($sTxt,$sAct='',$sSes='',$sUmf='',$bCod=false){
 if($sUmf>''&&($a=explode('=',$sUmf))&&isset($a[1])){
  $sHid='<input type="hidden" name="'.$a[0].'" value="'.$a[1].'" />';
  if($bCod&&UMF_NutzerMitCode) $sHid.='<input type="text" name="umf_Code" class="umfLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
  if($sAct=='frage') $sTxt=UMF_TxUmfrage.':<br />'.$sTxt; elseif($sAct=='grafik') $sTxt=UMF_TxErgebnis.':<br />'.$sTxt;
 }elseif($sTxt==UMF_TxStandardUmf&&$bCod&&UMF_NutzerMitCode){
  $sHid='<input type="text" name="umf_Code" class="umfLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
 }else $sHid='';
 if(!$p=strpos(UMF_Self,'umf_Ablauf=')) $sAbl=''; else $sAbl='<input type="hidden" name="umf_Ablauf" value="'.((int)substr(UMF_Self,$p+11,2)).'" />';
 return "\n".' <tr class="umfTr">
  <td class="umfMenu">'.fUmfTx(trim($sTxt)).'</td>
  <td class="umfMenu umfMen2"><form action="'.UMF_Self.'" method="get">'.$sAbl.'<input type="hidden" name="umf_Aktion" value="'.$sAct.'" />'.$sHid.'<input type="submit" class="umfScha" value="OK" title="OK" />'.($sSes>''?'<input type="hidden" name="umf_Session" value="'.$sSes.'" />':'').'</form></td>
 </tr>';
}
if(!function_exists('fUmfDeCode')){
function fUmfDeCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}}
?>