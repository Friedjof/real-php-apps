<?php

define('KAL_CSV_KeinExport','META-KEY;META-DES');
define('KAL_CSV_MemoKuerzen',0);

//--- ab hier nichts mehr veraendern ----

error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF); 

$sPfad=(isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:(isset($_SERVER['PATH_TRANSLATED'])?$_SERVER['PATH_TRANSLATED']:'./kalCSVExport.php'));
$sPfad=str_replace("\\",'/',str_replace("\\\\",'/',$sPfad)); $sPfad=substr($sPfad,0,strrpos($sPfad,'/'));
@include $sPfad.'/kalWerte.php';

if(defined('KAL_Version')){
 $nFelder=count($kal_FeldName); $sKeinExport='#;'.KAL_CSV_KeinExport.';#'; $sEx='"Nummer"'; $sMsg='';
 for($i=1;$i<$nFelder;$i++){
  $sFn=$kal_FeldName[$i]; if(!strpos($sKeinExport,';'.$sFn.';')) $sEx.=';"'.$sFn.'"';
 }
 $sEx.="\n";

 if(!KAL_SQL){ //Textdaten
  $aD=file(KAL_Pfad.KAL_Daten.KAL_Termine); $nSaetze=count($aD); $b=false;
  for($j=1;$j<$nSaetze;$j++){ //ueber alle Datensaetze
   $a=explode(';',rtrim($aD[$j]));
   if($a[1]=='1'||KAL_AendernLoeschArt==3&&$a[1]=='3'){//online
    array_splice($a,1,1); $sEx.='"'.$a[0].'"';
    for($i=1;$i<$nFelder;$i++) if(!strpos($sKeinExport,';'.$kal_FeldName[$i].';')){
     $s=str_replace('`,',',',$a[$i]); $t=$kal_FeldType[$i];
     switch($t){
      case 'd': if(strlen($s)>0) $s=fKalAnzeigeDatum($s); //Datum
       break;
      case '@': if(strlen($s)>0) $s=fKalAnzeigeDatum($s).(strlen($s)>10?', '.substr($s,11,5):''); //Eintragsdatum
       break;
      case 'm': if(KAL_CSV_MemoKuerzen) $s=fKalMemoKurz($s,KAL_CSV_MemoKuerzen);
       break;
      case 'w': $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); //Waehrung
       break;
      case 'e': case 'c': $s=fKalDeCode($s); //eMail und Kontakt
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J'||$s=='Y') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w) if($nP=strpos($w,'|')) $s.=substr($w,0,$nP).', '; $s=substr($s,0,-2);
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,'');
       else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'p': $s='*****';
       break;
     }
     $sEx.=';"'.$s.'"';
    }$sEx.="\n";
  }}
  if($nSaetze<=1) $sMsg='Keine Termindaten in '.KAL_Pfad.KAL_Daten.KAL_Termine.' gefunden!';
 }elseif($DbO=@new mysqli(KAL_SqlHost,KAL_SqlUser,KAL_SqlPass,KAL_SqlDaBa)) if(!mysqli_connect_errno()){//SQL
  if(KAL_SqlCharSet) $DbO->set_charset(KAL_SqlCharSet);
  $sF=''; $sS=''; $aNr=array(0); $k=1;
  for($i=1;$i<$nFelder;$i++) if(!strpos($sKeinExport,';'.$kal_FeldName[$i].';')){$aNr[$i]=$k++; $sF.=',kal_'.$i; if($i<4) $sS.='kal_'.$i.',';}
  if($rR=$DbO->query('SELECT id'.$sF.' FROM '.KAL_SqlTabT.' WHERE (online="1"'.(KAL_AendernLoeschArt!=3?'':' OR online="3"').') ORDER BY '.$sS.'id')){
   while($a=$rR->fetch_row()){$sEx.='"'.$a[0].'"';
    for($i=1;$i<$nFelder;$i++) if(!strpos($sKeinExport,';'.$kal_FeldName[$i].';')){
     $s=str_replace(';',',',$a[$aNr[$i]]); $t=$kal_FeldType[$i];
     switch($t){
      case 't': case 'g': $s=str_replace("\r\n",'\n ',$s);
       break;
      case 'm': $s=str_replace("\r\n",'\n ',$s); //Memo
       if(KAL_CSV_MemoKuerzen) $s=fKalMemoKurz($s,KAL_CSV_MemoKuerzen);
       break;
      case 'd': if(strlen($s)>0) $s=fKalAnzeigeDatum($s); //Datum
       break;
      case '@': if(strlen($s)>0) $s=fKalAnzeigeDatum($s).(strlen($s)>10?', '.substr($s,11,5):''); //Eintragsdatum
       break;
      case 'w': $s=number_format((float)$s,KAL_Dezimalstellen,KAL_Dezimalzeichen,KAL_Tausendzeichen); //Waehrung
       break;
      case 'j': case '#': case 'v': $s=strtoupper(substr($s,0,1)); //Ja/Nein
       if($s=='J') $s=KAL_TxJa; elseif($s=='N') $s=KAL_TxNein;
       break;
      case 'b': $s=substr($s,1+strpos($s,'|')); //Bild
       break;
      case 'l': //Link
       $aL=explode('||',$s); $s='';
       foreach($aL as $w) if($nP=strpos($w,'|')) $s.=substr($w,0,$nP).', '; $s=substr($s,0,-2);
       break;
      case 'n': case '1': case '2': case '3': case 'r': //Zahl
       if($t!='r') $s=number_format((float)$s,(int)$t,KAL_Dezimalzeichen,'');
       else $s=str_replace('.',KAL_Dezimalzeichen,$s);
       break;
      case 'p': $s='*****';
       break;
     }
     $sEx.=';"'.$s.'"';
    }$sEx.="\n";
   }$rR->close();
  }else $sMsg=KAL_TxSqlFrage;
 }else $sMsg=KAL_TxSqlVrbdg;

 if(empty($sMsg)){ //Ausgabe
  header('Content-Type: text/csv; charset=ISO-8859-1');
  echo $sEx;
 }else echo $sMsg;
}else echo 'Konfigurationsdatei unter '.$sPfad.'/kalWerte.php nicht gefunden!';

function fKalDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fKalAnzeigeDatum($w){ //sichtbares Datum
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=substr($w,0,4);
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalMemoKurz($s,$nL=80){
 if(strlen($s)>$nL){
  $v='#'.substr($s,0,$nL);
  if($p=strrpos($v,'[')){ //BB-Code enthalten
   if(strrpos($v,']')<$p) $v=substr($v,0,$p); //angeschnittenen Codes streichen
   $p=0; $aTg=array();
   while($p=strpos($v,'[',++$p)){ //Codes erkennen
    if($q=strpos($v,']',++$p)){$t=substr($v,$p,$q-$p); $aTg[]=(($q=strpos($t,'='))?substr($t,0,$q):$t);}
   }
   $n=count($aTg)-1;
   for($i=$n;$i>=0;$i--){ //Codes durchsuchen
    $s=$aTg[$i];
    if(substr($s,0,1)!='/'){
     $bFnd=false;
     for($j=$i;$j<=$n;$j++) if($aTg[$j]=='/'.$s){$aTg[$j]='#'; $bFnd=true; break;}
     if(!$bFnd) $v.='[/'.$s.']'; //fehlenden Code anhaengen
  }}}
  return substr($v,1).'....';
 }else return $s;
}
?>