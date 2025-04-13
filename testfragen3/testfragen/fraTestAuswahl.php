<?php
if(!function_exists('fFraSeite') ){ //bei direktem Aufruf
 function fFraSeite(){return fFraTestAuswahl(true);}
}

function fFraTestAuswahl($bDirekt){ //Seiteninhalt
 $Meld=''; $MTyp='Fehl'; $aF=array(); $aC=array();
 $sSes=($bDirekt?FRA_Session:FRA_NeuSession);
 $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $aT=@file(FRA_Pfad.'temp/'.substr($sSes,0,9).'.ses'); if(is_array($aT)) $aT=explode(';',rtrim($aT[0]));
  $sNam=(isset($aT[FRA_TeilnehmerKennfeld-1])&&$aT[FRA_TeilnehmerKennfeld-1]?$aT[FRA_TeilnehmerKennfeld-1]:'?????');

  $bSQLOpen=false; //SQL-Verbindung oeffnen
  if(FRA_SQL){
   $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
   if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
  }

  if(!FRA_SQL){ //Textdateien
   $a=@file(FRA_Pfad.FRA_Daten.FRA_Folgen); $nZhl=count($a);
   for($i=1;$i<$nZhl;$i++){
    $aZ=explode(';',$a[$i]);
    if(substr($aZ[0],0,1)!='~'){$aF[]=$aZ[0]; $aC[]=(isset($aZ[3])&&$aZ[3]?true:false);}
   }
  }elseif($bSQLOpen){ //bei SQL
   if($rR=$DbO->query('SELECT Folge,GAktiv FROM '.FRA_SqlTabT.' ORDER BY Folge')){
    while($aR=$rR->fetch_row()) if(substr($aR[0],0,1)!='~'){$aF[]=$aR[0]; $aC[]=($aR[1]?true:false);} $rR->close();
   }
  }//SQL

  if(empty($Meld)){
   if(!defined('FRA_AktivCodeErr')) $Meld=FRA_TxFuer.' &quot;'.$sNam.'&quot;';
   else $Meld='<span style="color:#b02">Geben Sie den korrekten Aktiv-Code an!</span>';
   $MTyp='Meld';
  }
 }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;

 $X='<p class="fraMeld" style="font-size:1.2em">'.fFraTx(FRA_TxTeilnehmerzentrum).'</p>';
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>'; $nNr=0;

 $X.='
 <table class="fraMenu" border="0" cellpadding="0" cellspacing="0">';
 if($MTyp!='Fehl'){
  if(FRA_TeilnehmerSpontaneFolge&&FRA_FolgeSpontan>'') $X.=fFraMenuZeile(str_replace('#',$nNr,FRA_TxTestNr).' '.FRA_TxSpontanFolge,'frage',$sSes,'fra_Spontantest=1');
  if(FRA_TeilnehmerAlleFolgen) foreach($aF as $k=>$s) if($s>''&&$aC[$k]) $X.=fFraMenuZeile(str_replace('#',++$nNr,FRA_TxTestNr).' '.$s,'frage',$sSes,'fra_Folgename='.$s,$aC[$k]);
  if(FRA_TeilnehmerStandardtest) $X.=fFraMenuZeile(FRA_TxStandardTest,'frage',$sSes);
  if(FRA_TeilnehmerDrucken) $X.=fFraMenuZeile(FRA_Drucken,'drucken',$sSes);
 }
 $X.=fFraMenuZeile(FRA_TxAbmelden,'erfassen');
 $X.="\n </table>";
 return $X;
}

function fFraMenuZeile($sTxt,$sAct='',$sSes='',$sTst='',$bCod=false){
 if($sAct=='frage'&&$sTst>''&&($a=explode('=',$sTst))&&isset($a[1])){
  $sHid='<input type="hidden" name="'.$a[0].'" value="'.$a[1].'" />';
  if(FRA_TeilnehmerMitCode&&$bCod){
   $sHid.='<input type="text" name="fra_Code" class="fraLogi" style="width:3.5em;" size="4" />&nbsp;&nbsp;&nbsp;';
  }
 }else $sHid='';
 return "\n".'  <tr>
   <td class="fraMenu">'.fFraTx(trim($sTxt)).'</td>
   <td class="fraMenu" style="text-align:right"><form action="'.FRA_Self.'" method="get">'.FRA_Hidden.'<input type="hidden" name="fra_Aktion" value="'.$sAct.'" />'.$sHid.'<input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="OK" title="OK" />'.($sSes>''?'<input type="hidden" name="fra_Session" value="'.$sSes.'" />':'').'</form></td>
  </tr>';
}
?>