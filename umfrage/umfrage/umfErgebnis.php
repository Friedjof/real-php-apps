<?php
function fUmfSeite(){
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false; $nAntwAnzahl=max(20,ADU_AntwortZahl);

 if($sSes=(UMF_Session)){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $nNId=(int)$sNId; $bSes=true;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(UMF_Nutzerverwaltung!='') $Meld=UMF_TxNutzerLogin;

 if($bSes){
  if(!UMF_SQL){ //Ergebnisse holen
   $aTmp=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); array_shift($aTmp);
   foreach($aTmp as $s){ //ueber alle Ergebnissaetze
    $p=strpos($s,';'); $aE[(int)substr($s,0,$p)]=rtrim(substr($s,++$p));
   }
   $aTmp=@file(UMF_Pfad.UMF_Daten.UMF_Fragen); array_shift($aTmp); $sL='0'; for($i=1;$i<$nAntwAnzahl;$i++) $sL.=';0';
   foreach($aTmp as $s){ //ueber alle Fragensaetze
    $a=explode(';',rtrim($s)); $nNr=(int)$a[0];
    $aD[$nNr]=array($nNr); //Nr
    $aD[$nNr][1]=str_replace('\n ',"\n",str_replace('`,',';',$a[3])); //Fra
    $aD[$nNr][2]=(isset($aE[$nNr])?$aE[$nNr]:$sL); //Erg
    $nA=6; while(isset($a[++$nA])&&strlen($a[$nA])>0); $aD[$nNr][3]=$nA-6;
   }
  }else{ //SQL-Daten
   if($DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa)){
    if(!mysqli_connect_errno()){if(defined('UMF_SqlCharSet')&&UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
    if($DbO){
     if($rR=$DbO->query('SELECT f.*,'.UMF_SqlTabE.'.Inhalt FROM '.UMF_SqlTabF.' AS f LEFT JOIN '.UMF_SqlTabE.' ON f.Nummer='.UMF_SqlTabE.'.Nummer ORDER BY f.Nummer')){
      while($a=$rR->fetch_row()){
       $sNr=(int)$a[0]; $aD[$sNr]=array($sNr); //Nr
       $aD[$sNr][1]=str_replace("\r",'',$a[3]); $aD[$sNr][2]=$a[count($a)-1]; //Fra,Erg
       $nA=6; while(isset($a[++$nA])&&strlen($a[$nA])>0); $aD[$sNr][3]=$nA-6;
      }$rR->close();
     }else $sMeld=UMF_TxSqlFrage;
    }else $Meld=UMF_TxSqlDaBnk;
   }else $Meld=UMF_TxSqlVrbdg;
 }}

 //Ausgabe
 $X.="\n".'<p class="umf'.$MTyp.'">'.fUmfTx($Meld).'</p>';
 $X.='
 <table class="umfMenu" border="0" cellpadding="2" cellspacing="1">
  <tr class="umfTr">
   <td class="umfMenu"><b>'.fUmfTx(UMF_TxNr).'</b></td>
   <td class="umfMenu"><b>'.fUmfTx(UMF_TxFrage).'</b></td>
   <td class="umfMenu">&nbsp;</td>
   <td class="umfMenu"><b>'.fUmfTx(UMF_TxTeilnehmer).'</b></td>
   <td class="umfMenu"><b>'.fUmfTx(UMF_TxErgebnis).'</b></td>
  </tr>';
 foreach($aD as $a){
  $sNr=$a[0]; $sE=$a[2].';0;0'; $n=$a[3]-1;
  $nG=0; $p=-1; for($i=0;$i<$n;$i++){$nG+=(int)substr($sE,++$p); $p=strpos($sE,';',$p);}
  $X.='
  <tr class="umfTr">
   <td class="umfMenu">'.sprintf('%'.UMF_NummerStellen.'d',$sNr).'</td>
   <td class="umfMenu">'.fUmfBB(fUmfTx($a[1])).'</td>
   <td class="umfMenu"><a href="'.UMF_Self.(strpos(UMF_Self,'?')?'&amp;':'?').'umf_Aktion=grafik&amp;umf_Zeige='.$sNr.'&amp;umf_Session='.$sSes.'"><img src="iconVorschau.gif" width="13" height="13" border="0" title="'.fUmfTx(UMF_TxErgebnisDetails).'"></a></td>
   <td class="umfMenu">'.$nG.'</td>
   <td class="umfMenu">'.substr($sE,0,$p).'</td>
  </tr>';
 }

 $X.="\n".'</table>';

 $X.="\n".'<p>[ <a class="umfMenu" href="'.UMF_Self.(strpos(UMF_Self,'?')?'&amp;':'?').'umf_Aktion=zentrum&amp;umf_Session='.$sSes.'">'.fUmfTx(UMF_TxBenutzerzentrum).'</a> ]</p>';
 return $X;
}