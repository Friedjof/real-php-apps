<?php
use setasign\Fpdi\Fpdi;

include 'hilfsFunktionen.php'; $sUkErr='fehlerhafter Aufruf!'; $bUkOK=false; $bSQLOpen=false;

if($nErgZl=trim(isset($_GET['nr'])?$_GET['nr']:'')){
 if(defined('FRA_Version')){
  if((isset($_GET['adm'])?$_GET['adm']:'#')==substr(substr(FRA_Schluessel,0,-1),1)){
   $bUkOK=true;
   if(FRA_SQL) if(isset($DbO)&&$DbO) $bSQLOpen=true; else{$sUkErr='Keine Verbindung zur SQL-Datenbank!'; $bUkOK=false;}
  }else $sUkErr='Illegaler unautorisierter Aufruf!';
 }else $sUkErr='fehlerhafter Aufruf ohne Konfigurationsdatei.';
}else $sUkErr='fehlerhafter Aufruf ohne ErgebnisNummer.';

if($bUkOK){ //PDF erzeugen
 if(!file_exists(FRA_Pfad.FRA_Urkunde)){$sUkErr='Urkundenvorlage "'.FRA_Urkunde.'" nicht gefunden!'; $bUkOK=false;}
 if(!file_exists(FRA_Pfad.'fpdf/fpdf.php')){$sUkErr='Bibliothek "FPDF" nicht installiert!'; $bUkOK=false;}
 if(!file_exists(FRA_Pfad.'fpdi/autoload.php')){$sUkErr='Bibliothek "FPDI" nicht installiert!'; $bUkOK=false;}
}

if($bUkOK){
 $aUkDr=array(); $aUkV=array(); $aUkUsr=array(); $sUkUsr=''; $aP=array(); $aE=array();
 $aH=explode('}{',substr(substr(FRA_UkDruck,0,-1),1)); foreach($aH as $s) $aUkDr[]=explode('|',$s);
 if(count($aUkDr)){// Druckposizionen vorhanden

  if(!FRA_SQL){//Fragen-Punkte holen
   $aE=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nUkS=count($aE);
   for($i=1;$i<$nUkS;$i++){
    $aH=explode(';',$aE[$i],8); $aP[(int)$aH[0]]=(int)$aH[6];
   }
  }elseif($bSQLOpen){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
    while($aH=$rR->fetch_row()) $aP[(int)$aH[0]]=(int)$aH[6]; $rR->close();
   }
  }

  if(!FRA_SQL){//Ergebnisliste holen
   $aE=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis);
  }elseif($bSQLOpen) if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' ORDER BY Eintrag')){
   while($aH=$rR->fetch_row()) $aE[]=$aH; $rR->close();
  }else{$sUkErr='Ergebnisliste kann nicht gelesen werden'; $bUkOK=false;}

  if($bUkOK){
   $bUkOK=false;
   for($i=count($aE)-1;$i>=0;$i--){//Ergebniszeile suchen
    if(!FRA_SQL) $aH=explode(';',trim($aE[$i]),14); else $aH=$aE[$i];
    if($aH[0]==$nErgZl){//Ergebniszeile gefunden
     if($sU=(isset($aH[13])?trim($aH[13]):'')){
      if($sU<'A'){//Benutzer
       $aU=explode(';',$sU); $sUId=sprintf('%d',$aU[0]); $aNF=explode(';',FRA_NutzerFelder); $aU=array();
       if(!FRA_SQL){//Textdatei
        $aU=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $n=count($aU)-1; $sUId.=';'; $l=strlen($sUId);
        for($i=$n;$i>0;$i--) if(substr($aU[$i],0,$l)==$sUId){//gefunden
         $aUkUsr=explode(';',rtrim($aU[$i])); $aUkUsr[2]=fFraDeCodeU($aUkUsr[2]); $aUkUsr[3]='**'; $aUkUsr[4]=fFraDeCodeU($aUkUsr[4]); $sUkUsr=(int)$aUkUsr[0].';'.$aUkUsr[2].';'.$aUkUsr[4];
         break;
        }
       }elseif($bSQLOpen) if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sUId.'"')){
        if($aUkUsr=$rR->fetch_row()){$aUkUsr[3]='**'; $sUkUsr=(int)$aUkUsr[0].';'.$aUkUsr[2].';'.$aUkUsr[4];} $rR->close();
       }
       $aUkV['B:Nummer']=$aUkUsr[0]; $aUkV['B:Benutzer']=$aUkUsr[2]; $aUkV['B:e-Mail']=$aUkUsr[4]; for($i=5;$i<count($aNF);$i++) $aUkV['B:'.$aNF[$i]]=(isset($aUkUsr[$i])&&$aUkUsr[$i]?$aUkUsr[$i]:FRA_UkFehlUDat);
      }else{//Teilnehmer
       $aUkUsr=explode(';',$sU); $aNF=explode(';',FRA_TeilnehmerFelder);
       for($i=0;$i<count($aNF);$i++) $aUkV['T:'.$aNF[$i]]=(isset($aUkUsr[$i])&&$aUkUsr[$i]?$aUkUsr[$i]:FRA_UkFehlUDat);
      }

      $nUkFra=(isset($aH[3])?(int)$aH[3]:0); $nUkP=(isset($aH[6])?(int)$aH[6]:0); $nUkR=(isset($aH[4])?(int)$aH[4]:0); $nUkF=(isset($aH[5])?(int)$aH[5]:0); $nUkS=0;
      $aA=explode('|',(isset($aH[9])?$aH[9]:'')); foreach($aA as $s){$n=(int)substr($s,0,strpos($s,':')); $nUkS+=(isset($aP[$n])?$aP[$n]:0);}
      $s=(isset($aH[1])?$aH[1]:''); if(FRA_SQL) $s=date(FRA_Datumsformat,mktime((int)substr($s,11,2),(int)substr($s,14,2),(int)substr($s,17,2),(int)substr($s,5,2),(int)substr($s,8,2),(int)substr($s,0,4)));
      $aUkV['Testdatum']=trim(substr($s,0,strpos($s,' '))); $aUkV['Testzeit']=trim(strstr($s,' '));
      if(strlen($aUkV['Testdatum'])==8) $aUkV['Testdatum']=substr_replace($aUkV['Testdatum'],'20',6,0);
      $aUkV['Antwortzeit']=(isset($aH[2])?$aH[2]:'0:00');
      $aUkV['Testfolgenname']=(isset($aH[12])?$aH[12]:'Standardtest');
      $aUkV['Fragenanzahl']=$nUkFra;
      $aUkV['Richtige']=$nUkR;
      $aUkV['Falsche']=(isset($aH[5])?$aH[5]:'0');
      $aUkV['Punkte']=$nUkP;
      $aUkV['PunkteMoeglich']=$nUkS;
      $aUkV['ProzentPunkte']=round(100*$nUkP/max($nUkS,1));
      $aUkV['ProzentRichtig']=round(100*$nUkR/max($nUkFra,1));
      if(FRA_VerbalPunkte) $p=$aUkV['ProzentPunkte']; else $p=$aUkV['ProzentRichtig'];
      $s=str_replace('#R',$nUkR,str_replace('#F',$nUkF,str_replace('#A',$nUkFra,str_replace('#P',rundU($nUkP),str_replace('#G',$nUkS,FRA_VerbalTx0)))));
      for($k=6;$k>0;$k--) if(($n=constant('FRA_VerbalAb'.$k))&&$p>=$n) $s=str_replace('#R',$nUkR,str_replace('#F',$nUkF,str_replace('#A',$nUkFra,str_replace('#P',rundU($nUkP),str_replace('#G',$nUkS,constant('FRA_VerbalTx'.$k))))));
      $aUkV['Verbalwertung']=$s;
      $aUkV['Versuche']=(isset($aH[7])?$aH[7]:'0');
      $aUkV['Auslassungen']=(isset($aH[8])?$aH[8]:'0');
      $bUkOK=true; break;


     }else $sUkErr='Kein Benutzerdaten eingetragen!';
    }
   }
   if(!$bUkOK) $sUkErr='Kein passender Eintrag in der Ergebnisliste gefunden.';
  }
  $aUkV['Druckdatum']=date('d.m.Y'); $aUkV['Druckzeit']=date('H:i');
 }
}
//print_r ($aUkV);

if($bUkOK){ //PDF erzeugen
 require_once(FRA_Pfad.'fpdf/fpdf.php');
 require_once(FRA_Pfad.'fpdi/autoload.php');
 $pdf=new Fpdi();
 $pdf->AddPage();

 $nPageCount=$pdf->setSourceFile(FRA_Pfad.FRA_Urkunde);
 $nTplIdx=$pdf->ImportPage(1);
 $pdf->useImportedPage($nTplIdx,0,0,NULL,NULL,true);

 foreach($aUkDr as $aUkPos){
  $s=$aUkPos[2]; $p=strpos($s,'{');
  while(!($p===false)){
   $e=strpos($s,'}',$p);
   $sVar=substr($s,$p+1,$e-($p+1));
   $s=substr_replace($s,(isset($aUkV[$sVar])?$aUkV[$sVar]:FRA_UkFehlTDat),$p,$e-$p+1);
   $p=strpos($s,'{');
  }
  $pdf->setFont($aUkPos[4],$aUkPos[6],$aUkPos[5]);
  $pdf->setXY($aUkPos[1]-($aUkPos[3]=='C'?60:($aUkPos[3]!='R'?0:120)),$aUkPos[0]);
  $pdf->Cell(120,8,$s,0,0,($aUkPos[3]?$aUkPos[3]:'L'));
 }
}

if($bUkOK){ //PDF erzeugen
 $pdf->Output(FRA_UkDatei.'.pdf','I');
}else{
 header('Content-Type: text/html; charset=ISO-8859-1');
 echo '<!DOCTYPE html>
<html>
<head>
<title>Fehlerseite</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
</head>

<body>
<h2 style="text-align:center">Fehlerseite!</h2>
<p style="text-align:center;color:#e42"><b>'.$sUkErr.'</b></p>
<hr size="1" width="96%">
<p style="text-align:center">'.date('d.m.Y H:i:s').'</p>
</body>
</html>
';
}

function fFraDeCodeU($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
function rundU($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
?>