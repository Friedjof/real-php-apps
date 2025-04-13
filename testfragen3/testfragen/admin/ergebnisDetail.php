<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Ergebnisdetail','','EEl');

 $aE=array(); $nG=1; $aK=array();
 $nId=(isset($_GET['nr'])?$_GET['nr']:'');
 $sQ=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
 $sQ=str_replace('&','&amp;',substr($sQ,0,strpos($sQ,'nr=')-1));
 if(!FRA_SQL){ //Ergebnis holen
  $aTmp=file(FRA_Pfad.FRA_Daten.FRA_Ergebnis); $nSaetze=count($aTmp); $s=$nId.';'; $p=strlen($s);
  for($i=1;$i<$nSaetze;$i++) if(substr($aTmp[$i],0,$p)==$s){
   $aE=explode(';',rtrim($aTmp[$i]),14); break;
  }
 }elseif($DbO){ //SQL
  if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabE.' WHERE Eintrag="'.$nId.'"')){
   $aE=$rR->fetch_row(); $rR->close(); $sD=$aE[1];
   $aE[1]=date(FRA_Datumsformat,mktime((int)substr($sD,11,2),(int)substr($sD,14,2),(int)substr($sD,17,2),(int)substr($sD,5,2),(int)substr($sD,8,2),(int)substr($sD,0,4)));
  }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
 }
 if(!$sMeld) if(count($aE)>4){
  $sMeld='<p class="admMeld">Ergebniseintrag Nummer '.$nId.'</p>'; $sAntwort='#|'.$aE[10]; $aL=array(); $aP=array(); $nG=0;
  if(!FRA_SQL){ //Loesungen und Punkte holen
   $aD=file(FRA_Pfad.FRA_Daten.FRA_Fragen); $nSaetze=count($aD);
   for($i=1;$i<$nSaetze;$i++){
    $s=$aD[$i];
    if(strpos($sAntwort,'|'.substr($s,0,strpos($s,';')).':')>0){
     $a=explode(';',$aD[$i]); $n=(int)$a[0]; $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nG+=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
     for($j=8;$j<17;$j++){
      $t=trim($a[$j]);
      if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
      if(!empty($t)) $nA++; else break;
     }
     $aZ[$n]=$nA;
   }}
  }elseif($DbO){ //SQL
   if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabF.' ORDER BY Nummer')){
    while($a=$rR->fetch_row()){ $n=$a[0];
     if(strpos($sAntwort,'|'.$n.':')>0){
      $aK[$n]=$a[3]; $aL[$n]=$a[5]; $aP[$n]=$a[6]; $nG+=$a[6]; $nA=0; if(!FRA_PunkteTeilen) $aTP[$n]=array();
      for($j=8;$j<17;$j++){
       $t=trim($a[$j]);
       if(!FRA_PunkteTeilen) $aTP[$n][$j-7]=(($p=strpos($t,'|#'))?(int)substr($t,$p+2):0);
       if(!empty($t)) $nA++; else break;
      }
      $aZ[$n]=$nA;
     }
    }$rR->close();
   }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
  }
  $a=explode('|',$aE[10]); $nZl=count($a); $aA=array(); //Antworten holen
  for($i=0;$i<$nZl;$i++){$t=$a[$i]; $p=strpos($t,':'); $aA[(int)substr($t,0,$p)]=substr($t,++$p);}
  $a=explode('|',$aE[9]); $nZl=count($a); $aKt=array(); //Fragenkette
  $sFD ='<table class="admTabl" style="width:auto" border="0" cellpadding="2" cellspacing="1">';
  $sFD.='<tr class="admTabl"><td>Frage-Nr.</td><td>Antwort</td><td>Lösung</td><td>Ergebnis</td><td>Punkte</td><td>Versuche</td><td>Auslassungen</td></tr>';
  for($i=0;$i<$nZl;$i++){
   $t=$a[$i]; $p=strpos($t,':'); $n=substr($t,0,$p); $b=explode(',',substr($t,$p+1));
   $sFD.='<tr class="admTabl"><td style="text-align:center">'.$n.'</td>';
   $sFD.='<td style="text-align:center">'.(isset($aA[$n])?$aA[$n]:'').'</td><td style="text-align:center">'.(isset($aL[$n])?$aL[$n]:'').'</td>';
   $sFD.='<td style="text-align:center">'.(isset($b[0])&&$b[0]=='r'?'richtig':'falsch').'</td><td style="text-align:center">'.(isset($b[1])?rund($b[1]):0).'/'.(isset($aP[$n])?$aP[$n]:'').'</td>';
   $sFD.='<td style="text-align:center">'.(isset($b[2])?$b[2]:'').'</td><td style="text-align:center">'.(isset($b[3])?$b[3]:'').'</td></tr>';
   if(FRA_DatKatErgebnis||FRA_DatKatFehlErgb||FRA_DatKatPunkte){ //pro Kategorie summieren
    $t=(isset($aK[$n])?$aK[$n]:'#');
    if(!isset($aKt[$t])) $aKt[$t]=array('r'=>0,'f'=>0,'p'=>0,'s'=>0,'z'=>0);
    if($b[0]=='r') ++$aKt[$t]['r']; else ++$aKt[$t]['f']; ++$aKt[$t]['z'];
    $aKt[$t]['p']+=(isset($b[1])?$b[1]:0); $aKt[$t]['s']+=(isset($aP[$n])?$aP[$n]:0);
  }}
  $sFD.='</table>';
  if(FRA_DatKatErgebnis||FRA_DatKatFehlErgb||FRA_DatKatPunkte){ //Kategorieauswertung
   if(FRA_DatKatSumme){ //zu Hauptkategorien zusammenfassen
    reset($aKt); $aKS=array(); foreach($aKt as $k=>$j) if(!strpos($k,'#')) $aKS[$k]=$j;
    reset($aKt); foreach($aKt as $k=>$j) if($p=strpos($k,'#')){
     $k=substr($k,0,$p); if(!isset($aKS[$k])){$aKS[$k]['r']=0; $aKS[$k]['f']=0; $aKS[$k]['p']=0; $aKS[$k]['s']=0; $aKS[$k]['z']=0;}
     $aKS[$k]['r']+=$j['r']; $aKS[$k]['f']+=$j['f']; $aKS[$k]['p']+=$j['p']; $aKS[$k]['s']+=$j['s']; $aKS[$k]['z']+=$j['z'];
    }
   }else $aKS=$aKt;
   $sKD ='<table class="admTabl" style="width:auto" border="0" cellpadding="2" cellspacing="1">'; $sKK='';
   $sKD.='<tr class="admTabl"><td>Kategorie</td>'.(FRA_DatKatErgebnis?'<td>Richtig</td>':'').(FRA_DatKatFehlErgb?'<td>Falsch</td>':'').(FRA_DatKatPunkte?'<td>Punkte</td>':'').'</tr>';
   foreach($aKS as $k=>$j){ //Kategoriesummen ausgeben
    $sKD.='<tr class="admTabl"><td>'.str_replace('#','<br>&nbsp;-',$k).'</td>'.(FRA_DatKatErgebnis?'<td>'.$j['r'].' ('.round(100*$j['r']/max($j['z'],1)).'%)</td>':'').(FRA_DatKatFehlErgb?'<td>'.$j['f'].' ('.round(100*$j['f']/max($j['z'],1)).'%)</td>':'').(FRA_DatKatPunkte?'<td>'.rund($j['p']).' '.FRA_TxVon.' '.$j['s'].' ('.round(100*$j['p']/max($j['s'],1)).'%)</td>':'').'</tr>';
    if($k>'') $sKK.=str_replace('#','-',$k).', ';
   }
   $sKD.='</table>'; if(strlen($sKK)>64) $sKK=substr($sKK,0,64).'...';
  }else{$sKD=''; $sKK='';}

  reset($aA); //Antwortkette
  $sAD ='<table class="admTabl" style="width:auto" border="0" cellpadding="2" cellspacing="1">';
  $sAD.='<tr class="admTabl"><td>Frage-Nr.</td><td>Antwort</td></tr>';
  foreach($aA as $n=>$t){
   $sAD.='<tr class="admTabl"><td style="text-align:center">'.$n.'</td><td>'.str_replace(',',' und ',$t).'</td></tr>';
  }
  $sAD.='</table>';
  $a=explode('|',$aE[11]); $nZl=count($a); //Verlaufskette
  $sVD ='<table class="admTabl" style="width:auto" border="0" cellpadding="2" cellspacing="1">';
  $sVD.='<tr class="admTabl"><td>Frage-Nr.</td><td>Antwort</td></tr>';
  for($i=0;$i<$nZl;$i++){
   $t=$a[$i]; if(!$p=strpos($t,':')){if($p=strpos($t,'-')) $s='ausgelassen'; else{$p=strlen($t); $s='keine Antwort';}}
   else $s=str_replace(',',' und ',substr($t,$p+1));
   $sVD.='<tr class="admTabl"><td style="text-align:center">'.substr($t,0,$p).'</td><td>'.$s.'</td></tr>';
  }
  $sVD.='</table>';
  $a=explode(';',$aE[13]); $nZl=count($a); //Nutzerdaten
  if($nNutzer=(int)$a[0]){ //Benutzer
   if(!FRA_SQL){ //Benutzerdaten holen
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $nNutzer.=';'; $p=strlen($nNutzer);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$nNutzer){
     $a=explode(';',rtrim($aD[$i])); $a[2]=fFraDeCode($a[2]); $a[4]=fFraDeCode($a[4]);
     break;
    }
   }else{ //SQL
    if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$nNutzer.'"')){
     $a=$rR->fetch_row(); $rR->close();
    }else $sMeld='<p class="admFehl">'.FRA_TxSqlFrage.'</p>';
   }
   $aF=explode(';',FRA_NutzerFelder); $a[0]=sprintf('%05d',$a[0]); array_splice($aF,3,1); array_splice($aF,1,1); array_splice($a,3,1); array_splice($a,1,1);
  }else{ //nur Teilnehmer
   $aF=explode(';',FRA_TeilnehmerFelder);
  }
  $sND ='<table class="admTabl" style="width:auto" border="0" cellpadding="2" cellspacing="1">'; $nZl=count($aF);
  for($i=0;$i<$nZl;$i++){
   $sND.='<tr class="admTabl"><td>'.($aF[$i]!='GUELTIG_BIS'?$aF[$i]:(FRA_TxNutzerFrist>''?FRA_TxNutzerFrist:$aF[$i])).'</td><td>'.(!empty($a[$i])?$a[$i]:'&nbsp;').'</td></tr>';
  }
  $sND.='</table>';
 }else $sMeld='<p class="admFehl">Keine Daten zum Ergebniseintrag '.$nId.'</p>';
?>
<script type="text/javascript">
var bFragenDetails=false; bKategorieDetails=false; bAntwortDetails=false; bVerlaufsDetails=false; bNutzerDetails=false;
var ImPlus=new Image(); ImMinu=new Image(); ImPlus.src='iconVorschau.gif'; ImMinu.src='iconVorschZu.gif';
function fragenDetails(){
 if(bFragenDetails){
  bFragenDetails=false;
  document.getElementById('VorschFrag').src=ImPlus.src; document.getElementById('VorschFrag').title='Einzelheiten anzeigen';
  document.getElementById('DetFrag').innerHTML='<?php echo $aE[9]?>';
 }else{
  bFragenDetails=true;
  document.getElementById('VorschFrag').src=ImMinu.src; document.getElementById('VorschFrag').title='Einzelheiten verbergen';
  document.getElementById('DetFrag').innerHTML='<?php echo $sFD?>';
 }
}
function kategorieDetails(){
 if(bKategorieDetails){
  bKategorieDetails=false;
  document.getElementById('VorschKatg').src=ImPlus.src; document.getElementById('VorschKatg').title='Einzelheiten anzeigen';
  document.getElementById('DetKatg').innerHTML='<?php echo $sKK ?>';
 }else{
  bKategorieDetails=true;
  document.getElementById('VorschKatg').src=ImMinu.src; document.getElementById('VorschKatg').title='Einzelheiten verbergen';
  document.getElementById('DetKatg').innerHTML='<?php echo $sKD ?>';
 }
}
function antwortDetails(){
 if(bAntwortDetails){
  bAntwortDetails=false;
  document.getElementById('VorschAntw').src=ImPlus.src; document.getElementById('VorschAntw').title='Einzelheiten anzeigen';
  document.getElementById('DetAntw').innerHTML='<?php echo $aE[10]?>';
 }else{
  bAntwortDetails=true;
  document.getElementById('VorschAntw').src=ImMinu.src; document.getElementById('VorschAntw').title='Einzelheiten verbergen';
  document.getElementById('DetAntw').innerHTML='<?php echo $sAD?>';
 }
}
function verlaufsDetails(){
 if(bVerlaufsDetails){
  bVerlaufsDetails=false;
  document.getElementById('VorschVerl').src=ImPlus.src; document.getElementById('VorschVerl').title='Einzelheiten anzeigen';
  document.getElementById('DetVerl').innerHTML='<?php echo $aE[11]?>';
 }else{
  bVerlaufsDetails=true;
  document.getElementById('VorschVerl').src=ImMinu.src; document.getElementById('VorschVerl').title='Einzelheiten verbergen';
  document.getElementById('DetVerl').innerHTML='<?php echo $sVD?>';
 }
}
function nutzerDetails(){
 if(bNutzerDetails){
  bNutzerDetails=false;
  document.getElementById('VorschNutz').src=ImPlus.src; document.getElementById('VorschNutz').title='Einzelheiten anzeigen';
  document.getElementById('DetNutz').innerHTML='<?php echo $aE[1]?>';
 }else{
  bNutzerDetails=true;
  document.getElementById('VorschNutz').src=ImMinu.src; document.getElementById('VorschNutz').title='Einzelheiten verbergen';
  document.getElementById('DetNutz').innerHTML='<?php echo $sND?>';
 }
}
</script>

<?php echo $sMeld.NL;?>

<table class="admTabl" style="table-layout:fixed;" border="0" cellpadding="4" cellspacing="1">
 <tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Ergebniseintrag</td>
  <td><?php echo sprintf('%05d',$aE[0])?></td></tr>
 <tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Datum</td>
  <td><?php echo $aE[1]?></td>
 </tr><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Testfolge</td>
  <td><?php echo ($aE[12]?$aE[12]:FRA_TxStandardTest)?></td>
 </tr><?php   if(FRA_DatZeitO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Dauer</td>
  <td><?php echo ($aE[2]?$aE[2]:'0').' &nbsp; ('; if(strlen($aE[2])>5) echo 'Stunden:'; echo 'Minuten:Sekunden)' ?></td>
 </tr><?php } if(FRA_DatAnzahlO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Fragenanzahl</td>
  <td><?php echo $aE[3] ?></td>
 </tr><?php } if(FRA_DatRichtigeO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Richtige</td>
  <td><?php echo $aE[4] ?></td>
 </tr><?php } if(FRA_DatFalscheO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Falsche</td>
  <td><?php echo $aE[5] ?></td>
 </tr><?php } if(FRA_DatPunkteO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Punkte</td>
  <td><?php echo rund($aE[6]).' von '.$nG.' &nbsp; ('.str_replace('.',',',rund(100*$aE[6]/max($nG,1))).'%)' ?></td>
 </tr><?php } if(FRA_DatVerbalO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Bewertung</td>
  <td><?php
   $s=str_replace('#R',$aE[4],str_replace('#F',$aE[5],str_replace('#A',$aE[3],str_replace('#P',$aE[6],str_replace('#G',$nG,FRA_VerbalTx0)))));
   if(FRA_VerbalPunkte) $p=round(100*$aE[6]/max($nG,1)); else $p=round(100*$aE[4]/max($aE[3],1));
   for($i=6;$i>0;$i--) if(($n=constant('FRA_VerbalAb'.$i))&&$p>=$n) $s=str_replace('#R',$aE[4],str_replace('#F',$aE[5],str_replace('#A',$aE[3],str_replace('#P',$aE[6],str_replace('#G',$nG,constant('FRA_VerbalTx'.$i))))));
   echo $s;
  ?></td>
 </tr><?php } if(FRA_DatVersucheO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Antwortversuche</td>
  <td><?php echo $aE[7] ?></td>
 </tr><?php } if(FRA_DatAuslassenO){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;">Auslassungen</td>
  <td><?php echo $aE[8] ?></td>
 </tr><?php } if($aE[9]){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;"><div style="width:125px;float:left;">Fragenbewertung</div><div style="margin-left:125px;text-align:right;"><img src="iconVorschau.gif" id="VorschFrag" onclick="fragenDetails()" width="13" height="13" border="0" alt="Einzelheiten" title="Einzelheiten zeigen"></div></td>
  <td id="DetFrag"><?php echo $aE[9] ?></td>
 </tr><?php } if(FRA_DatKatErgebnis||FRA_DatKatFehlErgb||FRA_DatKatPunkte){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;"><div style="width:125px;float:left;">Kategoriebewertung</div><div style="margin-left:125px;text-align:right;"><img src="iconVorschau.gif" id="VorschKatg" onclick="kategorieDetails()" width="13" height="13" border="0" alt="Einzelheiten" title="Einzelheiten zeigen"></div></td>
  <td id="DetKatg"><?php echo $sKK ?></td>
 </tr><?php } if($aE[10]){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;"><div style="width:125px;float:left;">Antwortkette</div><div style="margin-left:125px;text-align:right;"><img src="iconVorschau.gif" id="VorschAntw" onclick="antwortDetails()" width="13" height="13" border="0" alt="Einzelheiten" title="Einzelheiten zeigen"></div></td>
  <td id="DetAntw"><?php echo $aE[10] ?></td>
 </tr><?php } if($aE[11]){?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;"><div style="width:125px;float:left;">Verlaufskette</div><div style="margin-left:125px;text-align:right;"><img src="iconVorschau.gif" id="VorschVerl" onclick="verlaufsDetails()" width="13" height="13" border="0" alt="Einzelheiten" title="Einzelheiten zeigen"></div></td>
  <td id="DetVerl"><?php echo $aE[11] ?></td>
 </tr><?php }?><tr class="admTabl">
  <td style="width:14em;vertical-align:top;"><div style="width:125px;float:left;">Teilnehmer/Benutzer</div><div style="margin-left:125px;text-align:right;"><img src="iconVorschau.gif" id="VorschNutz" onclick="nutzerDetails()" width="13" height="13" border="0" alt="Einzelheiten" title="Einzelheiten zeigen"></div></td>
  <td id="DetNutz"><?php echo $aE[13] ?></td>
 </tr>
</table>
<p style="text-align:center;margin:16px;">[ <a href="ergebnisListe.php?<?php echo $sQ?>">zur Ergebnisliste</a> ]</p>

<?php
echo fSeitenFuss();

function rund($r){
 return str_replace('.',FRA_Dezimalzeichen,round($r,1));
}
?>