<?php
class Captcha{
/*--(c) J. Hummel------------------------------------------------------------
 ->Generate()                          erzeugt die Daten
 ->Test(Answer,Public,Question)        prueft die Schluessel
 ->Delete()                            loescht Schluessel und Eintrag
 ->Question                            enthaelt die Frage
 ->PublicKey                           enthaelt den PublicKey
 ->PrivateKey                          enthaelt den PrivatenKey
 ->Path
 ->KeyFile              captcha.csv    Name der Schluesseldatei
 ---------------------------------------------------------------------------*/
 var $Question='';
 var $PrivateKey='';
 var $PublicKey='';
 var $Salt='08-15';
 var $Path='';
 var $KeyFile='';
 var $Type='N';

 function __construct($sPath='./captcha/',$sKeyfile='captcha.csv'){ //Konstruktor
  $this->Path=$sPath;
  $this->KeyFile=$sPath.$sKeyfile;
  if(defined('CAPTCHA_SALT')) $this->Salt=CAPTCHA_SALT;
 }

 function Generate(){ //Captcha erzeugen und Texte liefern
  $sAnswer=$this->GetData(); //setzt $Question
  $sPublic=md5($this->Salt.'*'.$sAnswer); $this->PublicKey=$sPublic;
  $aC=@file($this->KeyFile); $sCsv=''; $nTime=time(); $sRefTime=strval($nTime-3600);
  if(is_array($aC)&&($nCnt=count($aC))) for($i=0;$i<$nCnt;$i++){
   $sLn=rtrim($aC[$i]); if(substr($sLn,0,10)>$sRefTime) $sCsv.=$sLn."\n"; else @unlink($this->Path.substr($sLn,11,8).'.jpg');
  }
  if($f=fopen($this->KeyFile,'w')){
   fwrite($f,$sCsv.$nTime.';'.$sPublic."\n"); fclose($f); $sResult='OK';
  }else $sResult.='_KeyFile';
  return $sResult;
 }

 function Test($sAnswer,$sPublic,$sQuestion){ //Antwort pruefen
  $bResult=false;
  $this->Question=str_replace('"','&quot;',$sQuestion);
  $this->PrivateKey=$sAnswer; $this->PublicKey=$sPublic;
  if(strlen($sAnswer)&&$sPublic==md5($this->Salt.'*'.$sAnswer)){ //richtig
   if(($aC=@file($this->KeyFile))&&is_array($aC)&&($nCnt=count($aC))){
    for($i=0;$i<$nCnt;$i++) if(rtrim(substr($aC[$i],11))==$sPublic){$bResult=true; break;}
   }
  }
  return $bResult;
 }

 function Delete(){ //Captcha loeschen
  $bResult=false;
  $sAnswer=$this->PrivateKey; $sPublic=$this->PublicKey;
  if(strlen($sAnswer)&&$sPublic==md5($this->Salt.'*'.strtolower($sAnswer))){ //richtig
   $sCsv=''; $sRefTime=strval(time()-900);
   if(($aC=@file($this->KeyFile))&&is_array($aC)&&($nCnt=count($aC))){
    for($i=0;$i<$nCnt;$i++){
     $sLn=rtrim($aC[$i]);
     if(substr($sLn,11)==$sPublic){$this->PrivateKey=''; $bResult=true;}
     else{if(substr($sLn,0,10)>$sRefTime) $sCsv.=$sLn."\n"; else @unlink($this->Path.substr($Ln,11,8).'.jpg');}
    }
    if($f=fopen($this->KeyFile,'w')){fwrite($f,$sCsv); fclose($f);}
   }
  }
  return $bResult;
 }

 function GetData(){
  function a1(){return rand(1,9);}
  function a12(){return rand(1,9)*(rand(0,1)?1:10);}
  function a23(){return rand(1,9)*(rand(0,1)?10:100);}
  function a13(){return rand(1,9)*(rand(0,1)?1:100);}
  function a123(){return rand(1,9)*pow(10,rand(0,2));}
  function wort($n){
   $a=array(0=>0,1=>'',1=>'eins',2=>'zwei',3=>'drei',4=>'vier',5=>'fünf',6=>'sechs',7=>'sieben',8=>'acht',9=>'neun',10=>'zehn',11=>'elf',12=>'zwölf',20=>'zwanzig',22=>'zweiundzwanzig',30=>'dreißig',32=>'zweiunddreißig',40=>'vierzig',42=>'zweiundvierzig',50=>'fünfzig',52=>'zweiundfünfzig',60=>'sechzig',62=>'zweiundsechzig',70=>'siebzig',72=>'zweiundsiebzig',80=>'achtzig',82=>'zweiundachtzig',90=>'neunzig',92=>'zweiundneunzig',100=>'einhundert',102=>'einhundertundzwei',200=>'zweihundert',202=>'zweihundertundzwei',300=>'dreihundert',302=>'dreihundertundzwei',400=>'vierhundert',402=>'vierhundertundzwei',500=>'fünfhundert',502=>'fünfhundertundzwei',600=>'sechshundert',602=>'sechshundertundzwei',700=>'siebenhundert',702=>'siebenhundertundzwei',800=>'achthundert',802=>'achthundertundzwei',900=>'neunhundert',902=>'neunhundertundzwei');
   return $a[$n];
  }

  switch(rand(0,6)){
  case 0: case 1:{ //Addition
   $z1=a123(); $z2=($z1<10||$z1>99?a12():a13()); $sAnswer=$z1+$z2;
   $z1=(rand(0,2)?wort($z1):$z1); $z2=(rand(0,1)?wort($z2):$z2);
   $q[]='Wieviel ist '.$z1.' + '.$z2.'?';
   $q[]='Wieviel ist '.$z1.' plus '.$z2.'?';
   $q[]='Berechnen Sie: '.$z1.' plus '.$z2.'.';
   $q[]='Lösen Sie: '.$z1.' plus mit '.$z2.'.';
   $q[]='Lösen Sie: '.$z1.' zuzüglich '.$z2.'.';
   $q[]='Rechnen Sie plus für '.$z1.' und '.$z2.'.';
   $q[]='Die Addition von '.$z1.' mit '.$z2.' ergibt:';
   $q[]='Addieren Sie '.$z1.' und '.$z2.'.';
   $q[]='Addieren Sie eine '.$z1.' mit '.$z2.'.';
   $q[]='Addiert ergeben '.$z1.' und '.$z2.' genau:';
   $q[]=ucfirst($z1).' addiert zu '.$z2.' ist:';
   $q[]=ucfirst($z1).' mit '.$z2.' addiert ergibt:';
   $q[]='Zählen Sie '.$z1.' mit '.$z2.' zusammen.';
   $q[]='Wieviel sind '.$z1.' und '.$z2.' zusammen?';
   $q[]='Wie groß sind '.$z1.' und '.$z2.' zusammen?';
   $q[]=ucfirst($z1).' mit '.$z2.' zusammengezählt ergibt:';
   $q[]=ucfirst($z1).' und '.$z2.' zusammengerechnet sind:';
   $q[]='Wieviel ist die Summe von '.$z1.' und '.$z2.'?';
   $q[]='Wie groß ist die Summe aus '.$z1.' mit '.$z2.'?';
   $q[]='Die Summe aus '.$z1.' und der Zahl '.$z2.' ist:';
   $q[]='Zusammen sind '.$z1.' und '.$z2.' genau:';
   $q[]='Wie groß sind '.$z1.' mit '.$z2.' in Summe?';
  } break;
  case 1:{ //Nachfolger
   $z1=(rand(0,1)?a123():a123()+2); $sAnswer=$z1+1; $z1=(rand(0,4)?wort($z1):$z1);
   $q[]='Der Nachfolger von '.$z1.' lautet:';
   $q[]='Nachfolger hinter der Zahl '.$z1.' ist:';
   $q[]=ucfirst($z1).' hat als Nachfolger die:';
   $q[]='Die nächste Zahl nach '.$z1.' ist:';
   $q[]='Direkt nach '.$z1.' folgt welche Zahl?';
   $q[]='Direkt auf '.$z1.' folgt die Zahl:';
   $q[]='Nach der '.$z1.' kommt welche Zahl?';
   $q[]='Nach '.$z1.' kommt als nächste Zahl die:';
   $q[]='Eins mehr als '.$z1.' ist:';
   $q[]=ucfirst($z1).' erhöht um eins ist:';
   $q[]=ucfirst($z1).' vermehrt um 1 ist:';
   $q[]=ucfirst($z1).' vergrößert um eins ist:';
  } break;
  case 2:{ //Vorgaenger
   $z1=(rand(0,1)?a123():a123()+2); if($z1<=1) $z1=11; $sAnswer=$z1-1; $z1=(rand(0,4)?wort($z1):$z1);
   $q[]='Der Vorgänger von '.$z1.' heißt:';
   $q[]='Der Vorgänger der Zahl '.$z1.' ist:';
   $q[]='Der Vorgänger für die '.$z1.' lautet:';
   $q[]='Vor der '.$z1.' kommt die Zahl:';
   $q[]='Die Zahl unmittelbar vor '.$z1.' lautet:';
   $q[]='Die nächstkleinere Zahl vor '.$z1.' heißt:';
   $q[]='Eins weniger als '.$z1.' ist:';
   $q[]='Vermindern Sie '.$z1.' um eins.';
   $q[]='Verringern Sie '.$z1.' um die Zahl 1.';
   $q[]='Verkleinern Sie '.$z1.' um eins.';
   $q[]='Subtrahieren Sie von '.$z1.' eine Eins.';
   $q[]='Subtrahieren Sie 1 von '.$z1.'.';
   $q[]=ucfirst($z1).' reduziert um eins ist:';
   $q[]=ucfirst($z1).' subtrahiert um 1 ist:';
   $q[]=ucfirst($z1).' vermindert um 1 ist:';
   $q[]=ucfirst($z1).' verringert um 1 ist:';
   $q[]=ucfirst($z1).' weniger eins ist:';
   $q[]=ucfirst($z1).' verkleinert um eins ist:';
   $q[]=ucfirst($z1).' minus 1 ergibt:';
  } break;
  case 3:{ //Doppelt
   $z1=a12(); $sAnswer=$z1+$z1; $z1=(rand(0,3)?wort($z1):$z1);
   $q[]='Das Doppelte von '.$z1.' ist:';
   $q[]='Das Zweifache von '.$z1.' ist:';
   $q[]='Verdoppeln Sie '.$z1.':';
   $q[]='Verzweifachen Sie '.$z1.':';
   $q[]='Die Verdopplung von '.$z1.' ist:';
   $q[]='Die Zahl '.$z1.' doppelt genommen ist:';
   $q[]='Die Zahl '.$z1.' zweifach genommen ist:';
   $q[]=ucfirst($z1).' verdoppelt ergibt:';
   $q[]=ucfirst($z1).' verzweifacht ergibt:';
   $q[]=ucfirst($z1).' mal 2 ergibt:';
   $q[]=ucfirst($z1).' mal zwei ist:';
   $q[]=ucfirst($z1).' mit sich selbst addiert ist:';
  } break;
  case 4:{ //Laenge
   $z1=(rand(0,1)?a23():a23()+2); $sAnswer=strlen(sprintf('%d',$z1)); $z1=(rand(0,3)?wort($z1):$z1);
   $q[]='Wie viele Stellen hat die Zahl '.$z1.'?';
   $q[]='Wie groß ist die Stellenzahl von '.$z1.'?';
   $q[]='Wieviel stellig ist die Zahl '.$z1.'?';
   $q[]='Wie viele Ziffern hat die Zahl '.$z1.'?';
   $q[]='Die Zahl '.$z1.' hat wie viele Stellen?';
   $q[]='Die Zahl '.$z1.' hat wie viele Ziffern?';
   $q[]='Besteht die '.$z1.' aus 1, 2 oder 3 Stellen?';
   $q[]='Umfasst die Zahl '.$z1.' 1, 2 oder 3 Ziffern?';
   $q[]='Besteht '.$z1.' aus 1, 2 oder 3 Ziffern?';
   $q[]='Umfasst '.$z1.' nun 1, 2 oder 3 Stellen?';
   $q[]='Die Stellenanzahl von '.$z1.' ist 1, 2 oder 3?';
  } break;
  case 5:{ //groesser
   $z1=a123(); $z2=a123(); while($z1==$z2) $z2=a123();
   $sAnswer=($z1>$z2?$z1:$z2); $z1=(rand(0,2)?wort($z1):$z1); $z2=(rand(0,2)?wort($z2):$z2);
   $q[]='Welche Zahl ist größer: '.$z1.' oder '.$z2.'?';
   $q[]='Was ist mehr: '.$z1.' oder '.$z2.'?';
   $q[]=ucfirst($z1).' oder '.$z2.'. Welche Zahl ist größer?';
   $q[]=ucfirst($z1).' oder '.$z2.'. Was ist mehr?';
   $q[]='Ist '.$z1.' oder '.$z2.' die größere Zahl?';
   $q[]='Ist '.$z1.' oder '.$z2.' mehr?';
  } break;
  case 6:{ //kleiner
   $z1=a123(); $z2=a123(); while($z1==$z2) $z2=a123();
   $sAnswer=($z1<$z2?$z1:$z2); $z1=(rand(0,2)?wort($z1):$z1); $z2=(rand(0,2)?wort($z2):$z2);
   $q[]='Welche Zahl ist kleiner: '.$z1.' oder '.$z2.'?';
   $q[]='Was ist weniger: '.$z1.' oder '.$z2.'?';
   $q[]=ucfirst($z1).' oder '.$z2.'. Welche Zahl ist kleiner?';
   $q[]=ucfirst($z1).' oder '.$z2.'. Was ist weniger?';
   $q[]='Ist '.$z1.' oder '.$z2.' die kleinere Zahl?';
   $q[]='Ist '.$z1.' oder '.$z2.' weniger?';
  } break;
  }//switch

  $sQuestion=$q[rand(0,count($q)-1)];
  $this->Question=$sQuestion;
  return sprintf('%0d',$sAnswer);
 }
}
?>