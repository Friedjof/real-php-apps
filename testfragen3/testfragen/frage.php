<?php
error_reporting(E_ALL);

$sFraSelf=(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'frage.php'));
$sFraQS=(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
$sFraHtmlVor=''; $sFraHtmlNach=''; $bFraOK=true; $sFraQry=''; $sFraHid=''; //Seitenkopf, Seitenfuß, Status

if(substr($sFraSelf,-9)=='frage.php'){ //direkter Aufruf
 if($fraAblauf=strstr($sFraQS,'fra_Ablauf=')) $fraAblauf=(int)substr($fraAblauf,11,2);
 elseif(isset($_POST['fra_Ablauf'])) $fraAblauf=(int)$_POST['fra_Ablauf']; else $fraAblauf='';
 include('fraWerte'.$fraAblauf.'.php'); define('FRA_Ablauf',$fraAblauf);
 if(defined('FRA_Version')){
  header('Content-Type: text/html; charset='.(FRA_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);
  $sFraSchablone=FRA_Schablone; if(isset($_POST['fra_Aktion'])&&$_POST['fra_Aktion']=='drucken'||isset($_GET['fra_Aktion'])&&$_GET['fra_Aktion']=='druckErgebnis') $sFraSchablone=FRA_DruckSchablone;
  $sFraCss=(defined('FRA_CSSDatei')?FRA_CSSDatei:'fraStyle.css'); if(!file_exists(FRA_Pfad.$sFraCss)) $sFraCss='fraStyle.css';
  if(strlen($sFraSchablone)>0){ //mit Seitenschablone
   $sFraHtmlNach=@implode('',@file(FRA_Pfad.$sFraSchablone));
   if($p=strpos($sFraHtmlNach,'{Inhalt}')){
    $sFraHtmlVor=substr($sFraHtmlNach,0,$p); $sFraHtmlNach=substr($sFraHtmlNach,$p+8); //Seitenkopf, Seitenfuss
    if($sFraCss!='fraStyle.css') if($p=strpos($sFraHtmlVor,'fraStyle.css')) $sFraHtmlVor=substr_replace($sFraHtmlVor,$sFraCss,$p,12);
   }else{$sFraHtmlVor='<p style="color:#A03;">HTML-Layout-Schablone <i>'.$sFraSchablone.'</i> nicht gefunden oder fehlerhaft!</p>'; $sFraHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.FRA_Http.$sFraCss.'>'."\n\n";
  }
 }else{$bFraOK=false; echo "\n".'<p style="color:#C03;">Konfiguration <i>fraWerte'.$fraAblauf.'.php</i> nicht gefunden oder fehlerhaft!</p>';}
}else{ //Aufruf per include
 if(defined('FRA_Version')){
  define('FRA_Http','http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www);
 }else{ //Variablen nicht includiert
  $bFraOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>fraWerte.php</i> wurde nicht includiert!</b></p>';
 }
}

if($bFraOK){ //Konfiguration eingelesen
 if(defined('FRA_WarnMeldungen')&&!FRA_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED); if(FRA_SQL) mysqli_report(MYSQLI_REPORT_OFF);
 if(strlen(FRA_TimeZoneSet)>0) date_default_timezone_set(FRA_TimeZoneSet); $bFraStatistik=FRA_Statistik;
 //geerbte GET/POST-Parameter aufbewahren und einige Ablaufparameter ermitteln
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['fra_Aktion'])) $sFraAktion=$_GET['fra_Aktion'];
  else{ //Erstaufruf ohne Aktion
   $sFraAktion='frage'; if(FRA_Nutzerverwaltung=='vorher') $sFraAktion='login'; elseif(FRA_Registrierung=='vorher') $sFraAktion='erfassen';
  }
  if(isset($_GET['fra_Session'])&&$sFraAktion!='login'){ // Session grob pruefen
   $sFraSes=$_GET['fra_Session']; $nFra=(int)substr(FRA_Schluessel,-2); for($iFra=strlen($sFraSes)-1;$iFra>=2;$iFra--) $nFra+=(int)substr($sFraSes,$iFra,1);
   if(hexdec(substr($sFraSes,0,2))!=$nFra||(substr($sFraSes,9)+round(FRA_MaxSessionZeit/2))<(time()>>8)) $sFraSes=''; // vierfache Sessionszeit
  }else $sFraSes=''; define('FRA_Session',$sFraSes);
  define('FRA_Antwort',(isset($_GET['fra_Antwort'])?$_GET['fra_Antwort']:''));
  define('FRA_Verlauf',(isset($_GET['fra_Verlauf'])?$_GET['fra_Verlauf']:''));
  define('FRA_Folge',(isset($_GET['fra_Folge'])?$_GET['fra_Folge']:''));
  define('FRA_Zeit',(isset($_GET['fra_Zeit'])?$_GET['fra_Zeit']:''));
  define('FRA_TestZeit',(isset($_GET['fra_TestZeit'])?(int)$_GET['fra_TestZeit']:0));
  define('FRA_TestFolgeName',(isset($_GET['fra_Folgename'])?$_GET['fra_Folgename']:'')); echo FRA_TestFolgeName;
  define('FRA_TestKategorie',(isset($_GET['fra_Kategorie'])?$_GET['fra_Kategorie']:''));
  if(isset($_GET['fra_Spontantest'])) define('FRA_TestSpontan',true);
  reset($_GET);
  foreach($_GET as $sFraK=>$sFraV) if(substr($sFraK,0,4)!='fra_'){
   $sFraQry.='&amp;'.$sFraK.'='.rawurlencode($sFraV);
   $sFraHid.='<input type="hidden" name="'.$sFraK.'" value="'.$sFraV.'" />';
  }
 }else{ //bei POST
  if(isset($_POST['fra_Aktion'])) $sFraAktion=$_POST['fra_Aktion']; else $sFraAktion='frage';
  if(isset($_POST['fra_Session'])&&$sFraAktion!='login'){ // Session grob pruefen
   $sFraSes=$_POST['fra_Session']; $nFra=(int)substr(FRA_Schluessel,-2); for($iFra=strlen($sFraSes)-1;$iFra>=2;$iFra--) $nFra+=(int)substr($sFraSes,$iFra,1);
   if(hexdec(substr($sFraSes,0,2))!=$nFra||((int)substr($sFraSes,9)+(int)round(FRA_MaxSessionZeit/2))<(time()>>8)) $sFraSes=''; // vierfache Sessionszeit
  }else $sFraSes=''; define('FRA_Session',$sFraSes);
  define('FRA_Antwort',(isset($_POST['fra_Antwort'])?$_POST['fra_Antwort']:''));
  define('FRA_Verlauf',(isset($_POST['fra_Verlauf'])?$_POST['fra_Verlauf']:''));
  define('FRA_Folge',(isset($_POST['fra_Folge'])?$_POST['fra_Folge']:''));
  define('FRA_Zeit',(isset($_POST['fra_Zeit'])?$_POST['fra_Zeit']:''));
  define('FRA_TestZeit',(isset($_POST['fra_TestZeit'])?(int)$_POST['fra_TestZeit']:0));
  define('FRA_TestFolgeName',(isset($_POST['fra_Folgename'])?$_POST['fra_Folgename']:''));
  define('FRA_TestKategorie',(isset($_POST['fra_Kategorie'])?$_POST['fra_Kategorie']:''));
  if(isset($_POST['fra_Spontantest'])) define('FRA_TestSpontan',true);
  reset($_POST); $aFraQS=(empty($sFraQS)?NULL:explode('&',$sFraQS)); $aFraQKeys=array();
  if(is_array($aFraQS)) foreach($aFraQS as $sFraQS) if(substr($sFraQS,0,4)!='fra_') if(is_string($sFraQS)){
   if(!$nFraP=strpos($sFraQS,'=')) !$nFraP=strlen($sFraQS);
   $sFraQry.='&amp;'.$sFraQS; $aFraQKeys[]=rawurldecode(substr($sFraQS,0,$nFraP));
   $sFraHid.='<input type="hidden" name="'.rawurldecode(substr($sFraQS,0,$nFraP)).'" value="'.rawurldecode(substr($sFraQS,$nFraP+1)).'" />';
  }
  foreach($_POST as $sFraK=>$sFraV) if(substr($sFraK,0,4)!='fra_'&&!in_array($sFraK,$aFraQKeys)){
   $sFraQry.='&amp;'.$sFraK.'='.rawurlencode($sFraV);
   $sFraHid.='<input type="hidden" name="'.$sFraK.'" value="'.$sFraV.'" />';
  }
 }
 if(!empty($fraAblauf)){$sFraQry.='&amp;fra_Ablauf='.$fraAblauf; $sFraHid='<input type="hidden" name="fra_Ablauf" value="'.$fraAblauf.'" />'.$sFraHid;}
 define('FRA_Self',$sFraSelf.(strlen($sFraQry)!=0?$sFraQry='?'.substr($sFraQry,5):'')); define('FRA_Hidden',$sFraHid);
 if(FRA_Antwort==''&&FRA_Session==''){ //pruefen auf illegalen Erstaufruf mit eingegebener fra_Aktion
  if($sFraAktion!='statistik'&&(substr($sFraAktion,0,2)!='ok')){if(FRA_Nutzerverwaltung=='vorher'&&$sFraAktion!='erfassen') $sFraAktion='login'; elseif(FRA_Registrierung=='vorher') $sFraAktion='erfassen';}
  elseif(!FRA_StatOffen) $bFraStatistik=false;
 }

 //Aktionen - Programmverteiler
 switch($sFraAktion){
  case 'frage': include(FRA_Pfad.'fraFrage.php'); break;
  case 'login': include(FRA_Pfad.'fraLogin.php'); break;
  case 'zeige': include(FRA_Pfad.'fraZeigen.php'); break;
  case 'erfassen': include(FRA_Pfad.'fraErfassen.php'); break;
  case 'zentrum': include(FRA_Pfad.'fraZentrum.php'); break;
  case 'auswahl': include(FRA_Pfad.'fraTestAuswahl.php'); break;
  case 'benutzer': include(FRA_Pfad.'fraNutzerDaten.php'); break;
  case 'ergebnis': include(FRA_Pfad.'fraErgebnis'.(isset($_GET['fra_Detail'])?'Detail':'Liste').'.php'); break;
  case 'drucken': include(FRA_Pfad.'fraDrucken.php'); break;
  case 'druckErgebnis': include(FRA_Pfad.'fraDruckErg.php'); break;
  case 'statistik': if($bFraStatistik) include(FRA_Pfad.'fraStatistik.php'); break;
  case 'ende': include(FRA_Pfad.'fraFertig.php'); break;
  default: if(substr($sFraAktion,0,2)=='ok') include(FRA_Pfad.'fraFreischalt.php'); //Freischaltung
 }

 //Beginn der Ausgabe
 echo $sFraHtmlVor."\n".'<div class="fraBox">'."\n"; include(FRA_Pfad.'fraVersion.php');
 if(FRA_Version!=$fraVersion||strlen(FRA_Www)==0) echo "\n".'<p class="fraFehl">'.fFraTx(FRA_TxSetupFehlt).'</p>'."\n";

 //Seiteninhalt
 if(function_exists('fFraSeite')) echo fFraSeite(); else echo FRA_TxZeigeLeer;

 //Ende der Ausgabebox und evt. Seitenfuß
 echo "\n</div><!-- /Box -->\n".$sFraHtmlNach;
}
echo "\n";

function fFraTx($sTx){ //TextKodierung
 if(FRA_Zeichensatz<=0) $s=$sTx; elseif(FRA_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8//TRANSLIT',$sTx); else $s=htmlentities($sTx,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br />',$s);
}
function fFraDt($sTx){ //DatenbankKonvertierung FRA_ZeichnsNorm
 $s=$sTx;
 return $s;
}
function fFraBB($v){//BB-Code zu HTML
 $p=strpos($v,'<oembed '); // CKEditor <oembed> ersetzen
 while(!($p===false)){
  if(($e=strpos($v,'</oembed>',$p))&&($u=strpos($v,'url',$p))&&($u<$e)){$v=substr_replace($v,'src',$u,3); $v=substr_replace($v,'',++$p,1); $v=substr_replace($v,'',++$e,1);}
  $p=strpos($v,'<oembed ',$p+1);
 }
 $p=strpos($v,'['); // BB-Code
 while(!($p===false)){
  $t=substr($v,$p,10);
  if(substr($t,0,3)=='[b]') $v=substr_replace($v,'<b>',$p,3); elseif(substr($t,0,4)=='[/b]') $v=substr_replace($v,'</b>',$p,4);
  elseif(substr($t,0,3)=='[i]') $v=substr_replace($v,'<i>',$p,3); elseif(substr($t,0,4)=='[/i]') $v=substr_replace($v,'</i>',$p,4);
  elseif(substr($t,0,3)=='[u]') $v=substr_replace($v,'<u>',$p,3); elseif(substr($t,0,4)=='[/u]') $v=substr_replace($v,'</u>',$p,4);
  elseif(substr($t,0,7)=='[color='){$w=substr($v,$p+7,9); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="color:'.$w.';">',$p,8+strlen($w));}
  elseif(substr($t,0,6)=='[size='){ $w=substr($v,$p+6,4); $w=substr($w,0,strpos($w,']')); $v=substr_replace($v,'<span style="font-size:'.(10+($w)).'0%;">',$p,7+strlen($w));}
  elseif(substr($t,0,8)=='[/color]')$v=substr_replace($v,'</span>',$p,8);
  elseif(substr($t,0,7)=='[/size]') $v=substr_replace($v,'</span>',$p,7);
  elseif(substr($t,0,8)=='[center]'){$v=substr_replace($v,'<p class="fraText" style="text-align:center">',$p,8);if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,7)=='[right]') {$v=substr_replace($v,'<p class="fraText" style="text-align:right">',$p,7); if(substr($v,$p-6,6)=='<br />') $v=substr_replace($v,'',$p-6,6);}
  elseif(substr($t,0,9)=='[/center]') {$v=substr_replace($v,'</p>',$p,9); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); if(substr($v,$p+4,6)=='<br />') $v=substr_replace($v,'',$p+4,6);}
  elseif(substr($t,0,5)=='[sup]') $v=substr_replace($v,'<sup>',$p,5); elseif(substr($t,0,6)=='[/sup]') $v=substr_replace($v,'</sup>',$p,6);
  elseif(substr($t,0,5)=='[sub]') $v=substr_replace($v,'<sub>',$p,5); elseif(substr($t,0,6)=='[/sub]') $v=substr_replace($v,'</sub>',$p,6);
  elseif(substr($t,0,5)=='[url]'){
   $m=$p+5; if(!$e=min(strpos($v,'[',$m),strpos($v,' ',$m))) $e=strpos($v,'[',$m);
   if(substr($v,$e,1)==' ') $v=substr_replace($v,'">',$e,1); else $v=substr_replace($v,'">'.substr($v,$m,$e-$m),$e,0);
   $v=substr_replace($v,'<a class="fraText" target="_blank" href="'.(substr($v,$m,4)!='http'?'http'.'://':''),$p,5);
  }elseif(substr($t,0,6)=='[/url]') $v=substr_replace($v,'</a>',$p,6);
  elseif(substr($t,0,5)=='[img]'){
   $e=strpos($v,'[',$p+5); $w=substr($v,$p+5,$e-($p+5)); $a=NULL; $u='';
   if(strpos($w,'://')){ //URL
    if(!$a=@getimagesize($w)) if($e=strpos($w,FRA_Www)) $a=@getimagesize(FRA_Pfad.substr($w,$e+strlen(FRA_Www)));
   }else{ //nur Pfad
    if(substr($w,0,1)=='/'){ //absoluter Pfad
     $u=$_SERVER['DOCUMENT_ROOT']; if(!strpos($w,substr($u,strpos($u,'/')+1)).'/') $u.=$w; $a=@getimagesize($u); $u='';
    }else $a=@getimagesize($w); //relativer Pfad
   }
   $w='<img class="fraText" '.(is_array($a)?$a[3].' ':'').'src="'.$u; $v=substr_replace($v,$w,$p,5);
  }elseif(substr($t,0,6)=='[/img]') $v=substr_replace($v,'" />',$p,6);
  elseif(substr($t,0,9)=='[youtube '){
   $n=strpos($v,']',$p+9); $w=trim(substr($v,$p+9,$n-($p+9))); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+9); $w=trim(substr($v,++$n,$e-$n));
    $v=substr_replace($v,'<iframe width="'.$a[0].'" height="'.$a[1].'" src="',$p,$l+10);
   }else{$v=substr_replace($v,'',$p+8,$n-($p+8)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($t,0,9)=='[youtube]'){
   $e=strpos($v,'[',$p+9); $w=trim(substr($v,$p+9,$e-($p+9)));
   $v=substr_replace($v,'<iframe src="',$p,9);
  }elseif(substr($t,0,10)=='[/youtube]'){$v=substr_replace($v,'" frameborder="0" allowfullscreen="">Ihr Browser zeigt keine iFrames. Siehe <a href="'.$w.'" target="_new">Youtube</a>.</iframe>',$p,10);}
  elseif(substr($t,0,7)=='[video '){
   $n=strpos($v,']',$p+7); $w=substr($v,$p+7,$n-($p+7)); $l=strlen($w); $a=explode(' ',$w);
   if(isset($a[1])&&(int)$a[1]&&(int)$a[0]){
    $e=strpos($v,'[',$p+7);  $w=substr($v,$n+1,$e-($n+1));
    $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
    $u='<video width="'.$a[0].'" height="'.$a[1].'" controls'.($e?' type="video/'.$u.'"':'').' src="'; $v=substr_replace($v,$u,$p,$l+8);
   }else{$v=substr_replace($v,'',$p+6,$n-($p+6)); $w=''; $p--;} //ungueltige Groesse loeschen
  }elseif(substr($t,0,7)=='[video]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<video controls'.($e?' type="video/'.$u.'"':'').' src="'; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($t,0,8)=='[/video]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Video</a> nicht.</video>',$p,8);
  elseif(substr($t,0,7)=='[audio]'){
   $e=strpos($v,'[',$p+7); $w=substr($v,$p+7,$e-($p+7));
   $u=''; if($e=strrpos($w,'.')) $u=substr($w,$e+1); // Typ-Endung
   $u='<audio controls'.($e?' type="audio/'.$u.'"':'').' src="'; $v=substr_replace($v,$u,$p,7);
  }elseif(substr($t,0,8)=='[/audio]') $v=substr_replace($v,'">Ihr Browser unterstützt das <a href="'.$w.'" target="_new">Audio</a> nicht.</audio>',$p,8);
  elseif(substr($t,0,5)=='[list'){
   if(substr($t,5,2)=='=o'){$w='o';$m=2;}else{$w='u';$m=0;}
   $v=substr_replace($v,'<'.$w.'l class="fraText"><li class="fraText">',$p,6+$m);
   $e=strpos($v,'[/list]',$p+5); $v=substr_replace($v,'</li></'.$w.'l>',$e,7+(substr($v,$e+7,6)=='<br />'?6:0));
   $m=strpos($v,'<br />',$p);
   while($m<$e&&$m>0){$v=substr_replace($v,'</li><li class="fraText">',$m,6); $e+=19; $m=strpos($v,'<br />',$m);}
  }
  $p=strpos($v,'[',$p+1);
 }
 return $v;
}
?>