<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){return fFraZentrum(true);}
}

function fFraZentrum($bDirekt){ //Seiteninhalt
 $Meld=''; $MTyp='Fehl'; $aF=array(); $aC=array();
 $sSes=($bDirekt?FRA_Session:FRA_NeuSession);
 $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $sId=substr($sSes,4,5); $sNam='???';

  $bSQLOpen=false; //SQL-Verbindung oeffnen
  if(FRA_SQL){
   $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
   if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
  }

  if(FRA_NutzerFrist>0){
   $aNutzFld=explode(';',FRA_NutzerFelder); $nGueltPos=array_search('GUELTIG_BIS',$aNutzFld);
  }else $nGueltPos=0;
  if(!FRA_SQL){ //Textdateien
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=((int)$sId).';'; $n=strlen($s);
   for($i=1;$i<$nSaetze;$i++){
    if(substr($aD[$i],0,$n)==$s){ //gefunden
     $aN=explode(';',$aD[$i]); $sNam=fFraDeCode($aN[2]);
     if($nGueltPos==0||!isset($aN[$nGueltPos])||$aN[$nGueltPos]==''||$aN[$nGueltPos]>=date('Y-m-d')){ //gueltig
      $a=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nZhl=count($a);
      for($i=1;$i<$nZhl;$i++){
       $aZ=explode(';',$a[$i]);
       if(substr($aZ[0],0,1)!='~'){$aF[]=$aZ[0]; $aC[]=(isset($aZ[4])&&$aZ[4]?true:false);}
      }
      if(FRA_NutzerTests){
       $a=@file(FRA_Pfad.FRA_Daten.FRA_Zuweisung); $nZhl=count($a); $s=(int)$sId.';'; $l=strlen($s);
       for($j=1;$j<$nZhl;$j++) if(substr($a[$j],0,$l)==$s){ //Nutzerzuordnung gefunden
        $sZw=rtrim(substr($a[$j],$l)); break;
     }}}else $Meld=FRA_TxNutzerAblauf.' - '.FRA_TxPassiv; //ungueltig
     break;
   }}
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SELECT Nummer,Benutzer'.($nGueltPos>0?',dat_'.$nGueltPos:'').' FROM '.FRA_SqlTabN.' WHERE Nummer="'.((int)$sId).'"')){
    $a=$rR->fetch_row(); $rR->close(); if($a[0]==(int)$sId) $sNam=$a[1];
    if($nGueltPos==0||$a[2]==''||$a[2]>=date('Y-m-d')){ //gueltig
     if($rR=$DbO->query('SELECT Folge,BAktiv FROM '.FRA_SqlTabT.' ORDER BY Folge')){
      while($aR=$rR->fetch_row()) if(substr($aR[0],0,1)!='~'){$aF[]=$aR[0]; $aC[]=($aR[1]?true:false);} $rR->close();
      if(FRA_NutzerTests) if($rR=$DbO->query('SELECT Nummer,Tests FROM '.FRA_SqlTabZ.' WHERE Nummer="'.$sId.'"')){
       if($a=$rR->fetch_row()) $sZw=$a[1]; $rR->close(); //Nutzerzuordnung gefunden
    }}}else $Meld=FRA_TxNutzerAblauf.' - '.FRA_TxPassiv; //ungueltig
  }}//SQL
  if(isset($sZw)){ //Nutzerzuordnungen abarbeiten
   $bNutzerStandardtest=false; $bNutzerSpontaneFolge=false; $bNutzerAlleFolgen=false;
   if(strlen($sZw)){
    $sZw='#;'.$sZw.';'; $sHeute=date('Y-m-d'); $nFolgen=count($aF);
    if($p=strpos($sZw,FRA_TxStandardTest.'=')){
     $w=substr($sZw,$p+strlen(FRA_TxStandardTest)+1); $w=substr($w,0,strpos($w,';'));
     if(substr($w,0,3)=='bis'){if(substr($w,3)>=$sHeute) $bNutzerStandardtest=true;} elseif(substr($w,0,2)=='ab'){if(substr($w,2)<=$sHeute) $bNutzerStandardtest=true;}
     elseif(substr($w,0,2)=='am'){if(substr($w,2)==$sHeute) $bNutzerStandardtest=true;} elseif(strlen($w)==0||$w>'0x') $bNutzerStandardtest=true;
     if($w=='0x'||strpos($w,' 0x')) $bNutzerStandardtest=false;
    }
    if($p=strpos($sZw,FRA_TxSpontanFolge.'=')){
     $w=substr($sZw,$p+strlen(FRA_TxSpontanFolge)+1); $w=substr($w,0,strpos($w,';'));
     if(substr($w,0,3)=='bis'){if(substr($w,3)>=$sHeute) $bNutzerSpontaneFolge=true;} elseif(substr($w,0,2)=='ab'){if(substr($w,2)<=$sHeute) $bNutzerSpontaneFolge=true;}
     elseif(substr($w,0,2)=='am'){if(substr($w,2)==$sHeute) $bNutzerSpontaneFolge=true;} elseif(strlen($w)==0||$w>'0x') $bNutzerSpontaneFolge=true;
     if($w=='0x'||strpos($w,' 0x')) $bNutzerSpontaneFolge=false;
    }
    for($i=0;$i<$nFolgen;$i++) if($p=strpos($sZw,$aF[$i].'=')){
     $w=substr($sZw,$p+strlen($aF[$i])+1); $w=substr($w,0,strpos($w,';')); $bFolge=false;
     if(substr($w,0,3)=='bis'){if(substr($w,3)>=$sHeute) $bFolge=true;} elseif(substr($w,0,2)=='ab'){if(substr($w,2)<=$sHeute) $bFolge=true;}
     elseif(substr($w,0,2)=='am'){if(substr($w,2)==$sHeute) $bFolge=true;} elseif(strlen($w)==0||$w>'0x') $bFolge=true;
     if($w=='0x'||strpos($w,' 0x')) $bFolge=false;
     if($bFolge) $bNutzerAlleFolgen=true; else $aF[$i]='';
    }else $aF[$i]='';
   }
  }else{ //ohne Nutzerzuordnung
   $bNutzerStandardtest=FRA_NutzerStandardtest; $bNutzerSpontaneFolge=FRA_NutzerSpontaneFolge; $bNutzerAlleFolgen=FRA_NutzerAlleFolgen;
  }
  if(empty($Meld)){
   if(!defined('FRA_AktivCodeErr'))$Meld=FRA_TxFuer.' &quot;'.$sNam.'&quot;';
   else $Meld='<span style="color:#b02">'.FRA_TxAktivCodeNoetig.'</span>';
   $MTyp='Meld';
  }
 }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;

 $X='<p class="fraMeld" style="font-size:1.2em">'.fFraTx(FRA_TxBenutzerzentrum).'</p>';
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>'; $nNr=0;
 $X.='
 <table class="fraMenu" border="0" cellpadding="0" cellspacing="0">';
 if($MTyp!='Fehl'){
  if($bNutzerSpontaneFolge&&FRA_FolgeSpontan>'') $X.=fFraMenuZeile(str_replace('#',$nNr,FRA_TxTestNr).' '.FRA_TxSpontanFolge,'frage',$sSes,'fra_Spontantest=1');
  if($bNutzerAlleFolgen) foreach($aF as $k=>$s) if($s>''&&$aC[$k]) $X.=fFraMenuZeile(str_replace('#',++$nNr,FRA_TxTestNr).' '.$s,'frage',$sSes,'fra_Folgename='.$s,$aC[$k]);
  if($bNutzerStandardtest) $X.=fFraMenuZeile(FRA_TxStandardTest,'frage',$sSes);
  if(FRA_NutzerErgebnis) $X.=fFraMenuZeile(FRA_TxErgebnisListe,'ergebnis',$sSes);
  if(FRA_Drucken) $X.=fFraMenuZeile(FRA_Drucken,'drucken',$sSes);
  if(FRA_NutzerStatistik) $X.=fFraMenuZeile(FRA_TxNutzerStatName,'statistik',$sSes);
  if(FRA_NutzerAendern) $X.=fFraMenuZeile(FRA_TxNutzerAendern,'benutzer',$sSes);
 }
 $X.=fFraMenuZeile(FRA_TxAbmelden,'login');
 $X.="\n </table>";
 return $X;
}

function fFraMenuZeile($sTxt,$sAct='',$sSes='',$sTst='',$bCod=false){
 if($sAct=='frage'&&$sTst>''&&($a=explode('=',$sTst))&&isset($a[1])){
  $sHid='<input type="hidden" name="'.$a[0].'" value="'.fFraTx($a[1]).'" />';
  if(FRA_NutzerMitCode&&$bCod){
   $sHid.='<input type="text" name="fra_Code" class="fraLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
  }
 }else $sHid='';
 if(!$p=strpos(FRA_Self,'fra_Ablauf=')) $sAbl=''; else $sAbl='<input type="hidden" name="fra_Ablauf" value="'.((int)substr(FRA_Self,$p+11,2)).'" />';
 return "\n".'  <tr>
   <td class="fraMenu">'.fFraTx(trim($sTxt)).'</td>
   <td class="fraMenu" style="text-align:right"><form action="'.FRA_Self.'" method="get">'.FRA_Hidden.$sAbl.'<input type="hidden" name="fra_Aktion" value="'.$sAct.'" />'.$sHid.'<input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="OK" title="OK" />'.($sSes>''?'<input type="hidden" name="fra_Session" value="'.$sSes.'" />':'').'</form></td>
  </tr>';
}

if(!function_exists('fFraDeCode')){
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}}
?>