<?php
function fFraSeite(){
 $X=''; $Meld=''; $MTyp='Fehl'; $bSes=false;
 if($sSes=FRA_Session){
  $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $bSes=true;
  }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;
 }

 $bSQLOpen=false; //SQL-Verbindung oeffnen
 if(FRA_SQL){
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
 }

 $a=explode(',',($bSes?FRA_StatNFelder:FRA_StatFelder)); $aFlds=array(0); for($i=0;$i<=13;$i++) $aFlds[$a[$i]]=$i; ksort($aFlds);
 $aZ=array(); $aIdx=array(); $nFlds=count($aFlds); $i=0;
 if(!FRA_SQL){
  $aD=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aD); $sHd=rtrim($aD[0]);
  for($i=1;$i<$nSaetze;$i++){
   $a=explode(';',rtrim($aD[$i]),14);
   $b=array();
   for($j=1;$j<$nFlds;$j++) $b[$j]=(isset($aFlds[$j])?$a[$aFlds[$j]]:0);
   $aZ[$i]=$b;
   $s=$a[FRA_StatSortier1]; $s=(FRA_StatSortier1==6?sprintf('%08.3f',$s):(FRA_StatSortier1>2||FRA_StatSortier1==0?sprintf('%06d',$s):(FRA_StatSortier1==1?fFraNormDat($s):$s)));
   $t=$a[FRA_StatSortier2]; $t=(FRA_StatSortier2==6?sprintf('%08.3f',$t):(FRA_StatSortier2>2||FRA_StatSortier2==0?sprintf('%06d',$t):(FRA_StatSortier2==1?fFraNormDat($t):$t)));
   if(FRA_StatSortAbsteig!=FRA_StatSrt2Absteig){
    if(FRA_StatSortier2==2){$a=explode(':',$t); $t=''; foreach($a as $u) $t.=sprintf('%02d',60-$u).':'; $t=substr($t,0,-1);} //Zeit
    elseif(FRA_StatSortier2==6) $t=sprintf('%08.3f',9999.999-$t); //Punkte
    elseif(FRA_StatSortier2>2||FRA_StatSortier2==0) $t=sprintf('%06d',999999-$t); //Zahl
    elseif(FRA_StatSortier2==1){$t=str_replace(':','-',str_replace(' ','-',$t)); $a=explode('-',(substr(FRA_Datumsformat,0,5)=='d.m.Y'?substr($t,2):$t)); $t=''; foreach($a as $u) $t.=sprintf('%02d',99-$u).'-'; $t=substr($t,0,-1);}
   }
   $aIdx[$i]=$s.chr(255).$t.chr(255).sprintf('%06d',$a[0]);
  }
 }elseif($bSQLOpen){
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE)){
   while($a=$rR->fetch_row()){
    $b=array(); for($j=1;$j<$nFlds;$j++) if(isset($aFlds[$j])) $b[$j]=$a[$aFlds[$j]]; else $b[$j]=''; $aZ[++$i]=$b;
    $s=$a[FRA_StatSortier1]; $s=(FRA_StatSortier1==6?sprintf('%08.3f',$s):(FRA_StatSortier1>2||FRA_StatSortier1==0?sprintf('%06d',$s):(FRA_StatSortier1==1?fFraNormDat($s):$s)));
    $t=$a[FRA_StatSortier2]; $t=(FRA_StatSortier2==6?sprintf('%08.3f',$t):(FRA_StatSortier2>2||FRA_StatSortier2==0?sprintf('%06d',$t):(FRA_StatSortier2==1?fFraNormDat($t):$t)));
    if(FRA_StatSortAbsteig!=FRA_StatSrt2Absteig){
     if(FRA_StatSortier2==2){$a=explode(':',$t); $t=''; foreach($a as $u) $t.=sprintf('%02d',60-$u).':'; $t=substr($t,0,-1);} //Zeit
     elseif(FRA_StatSortier2==6) $t=sprintf('%08.3f',9999.999-$t); //Punkte
     elseif(FRA_StatSortier2>2||FRA_StatSortier2==0) $t=sprintf('%06d',999999-$t); //Zahl
     elseif(FRA_StatSortier2==1){$t=str_replace(':','-',str_replace(' ','-',$t)); $a=explode('-',(substr(FRA_Datumsformat,0,5)=='d.m.Y'?substr($t,2):$t)); $t=''; foreach($a as $u) $t.=sprintf('%02d',99-$u).'-'; $t=substr($t,0,-1);}
    }
    $aIdx[$i]=$s.chr(255).$t.chr(255).sprintf('%06d',$a[0]);
   }$rR->close();
  }else $Meld=FRA_TxSqlFrage;
  $sHd='Eintrag;Datum;Dauer;Anzahl;Richtige;Falsche;Punkte;Versuche;Auslassungen;Bewertung;Antwortkette;Verlaufskette;Testfolge;Benutzer';
 }
 if(FRA_StatSortAbsteig) arsort($aIdx); else asort($aIdx); reset($aIdx);
 $aTlnFlds=explode(',',FRA_StatTlnFld); $nTlnFlds=count($aTlnFlds); $aCss=explode(',',FRA_StatCssStil);
 $aNtzFlds=explode(',',FRA_StatNtzFld); $nNtzFlds=count($aNtzFlds); $aN=array();
 if(strpos('#,'.FRA_StatNtzFld,',1')>0){ //Benutzerdaten holen
  if(!FRA_SQL){
   $a=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nNtz=count($a);
   for($i=1;$i<$nNtz;$i++){
    $s=''; $b=explode(';',rtrim($a[$i]));
    for($k=0;$k<$nNtzFlds;$k++) if($aNtzFlds[$k]=='1'&&!empty($b[$k])) $s.=(strlen($s)>0?FRA_StatNtzTrn:'').($k!=2&&$k!=4?$b[$k]:fFraDeCode($b[$k]));
    $aN[(int)$b[0]]=$s;
   }
  }elseif($bSQLOpen){
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN)){
    while($b=$rR->fetch_row()){
     $s=''; for($k=0;$k<$nNtzFlds;$k++) if($aNtzFlds[$k]=='1'&&!empty($b[$k])) $s.=(strlen($s)>0?FRA_StatNtzTrn:'').$b[$k];
     $aN[(int)$b[0]]=$s;
    }$rR->close();
  }}
 }

 $sT="\n".'<table class="fraStat" cellpadding="0" cellspacing="0" border="0">';
 $sT.="\n".' <tr>'; $a=explode(';',rtrim($sHd)); // Kopfzeile
 for($j=1;$j<$nFlds;$j++){
  $sS='text-align:center;'; if(isset($aFlds[$j])) if($aFlds[$j]==13) $sS='text-align:left;'; elseif($aFlds[$j]==6) $sS='text-align:right;';
  $sT.="\n  ".'<td class="fraStat" style="'.$sS.'"><b>'.(isset($aFlds[$j])?$a[$aFlds[$j]]:0).'</b></td>';
 }
 $sT.="\n ".'</tr>';

 $nSaetze=(FRA_StatListenZeilen>0?FRA_StatListenZeilen:999); //alle Datenzeilen
 foreach($aIdx as $i=>$xx) if($nSaetze-->0){
  $sT.="\n ".'<tr>'; $a=$aZ[$i];
  for($j=1;$j<$nFlds;$j++){
   $s=trim($a[$j]); $sS='text-align:center;'; $sCss='';
   if(isset($aFlds[$j])&&($n=$aFlds[$j])){
    $sCss=$aCss[$n];
    switch($n){
     case 0: $s=sprintf('%06d',$s); break;
     case 1:
      $s=(!FRA_SQL?$s:date(FRA_Datumsformat,mktime((int)substr($s,11,2),(int)substr($s,14,2),(int)substr($s,17,2),(int)substr($s,5,2),(int)substr($s,8,2),(int)substr($s,0,4))));
      if(!FRA_StatDatumZeit) $s=trim(substr($s,0,strpos($s,' ')));
      break;
     case 6: $s=sprintf('%0.'.FRA_StatKommaStellen.'f',$s); $sS='text-align:right;'; break;
     case 13: $b=explode(';',$s); $sS='text-align:left;'; // Teilnehmer
      if($s=$b[0]){
       $k=strlen($s); $bNum=true; while(--$k>0) if($s[$k]>'9'||$s[$k]<'0'){$k=-1; $bNum=false;}
       if($bNum){ //Benutzer
        $k=(int)$s; $s=(isset($aN[$k])?fFraTx($aN[$k]):fFraTx(FRA_TxKeinNtzNam));
       }else{ //Teilnehmer
        $s=''; for($k=0;$k<$nTlnFlds;$k++) if($aTlnFlds[$k]=='1'&&!empty($b[$k])) $s.=(strlen($s)>0?FRA_StatTlnTrn:'').fFraTx($b[$k]);
       }
      }else $s=fFraTx(FRA_TxKeinTlnNam);
     break;
   }}
   $sT.="\n  ".'<td class="fraStat" style="'.$sS.$sCss.'">'.$s.'</td>';
  }
  $sT.="\n ".'</tr>';
 }
 $sT.="\n".'</table>';

 //Seitenausgabe
 if(empty($Meld)){$Meld=FRA_TxStatistik; $MTyp='Meld';}
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>';
 if(FRA_Statistik) $X.=$sT; else $X.=fFraTx(FRA_TxZeigeLeer);
 if($bSes) $X.='<p>[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=zentrum&amp;fra_Session='.$sSes.'">'.fFraTx(FRA_TxBenutzerzentrum).'</a> ]</p>';
 return $X;
}

function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function fFraNormDat($s){
 $sD=substr(FRA_Datumsformat,0,5);
 switch($sD){
  case 'd.m.y': $s=substr($s,6,2).'-'.substr($s,3,2).'-'.substr($s,0,2).substr($s,8); break;
  case 'd.m.Y': $s=substr($s,6,4).'-'.substr($s,3,2).'-'.substr($s,0,2).substr($s,10); break;
 }
 return $s;
}
?>