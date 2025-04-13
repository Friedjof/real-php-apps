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
 var $Type='T';

 function __construct($sPath='./captcha/',$sKeyfile='captcha.csv'){ //Konstruktor
  $this->Path=$sPath;
  $this->KeyFile=$sPath.$sKeyfile;
  if(defined('CAPTCHA_SALT')) $this->Salt=CAPTCHA_SALT;
 }

 function Generate(){ //Captcha erzeugen und Texte liefern
  $sAnswer=$this->GetData(); //setzt $Question
  $sPublic=md5($this->Salt.'*'.strtolower($sAnswer)); $this->PublicKey=$sPublic;
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
  if(strlen($sAnswer)&&$sPublic==md5($this->Salt.'*'.strtolower($sAnswer))){ //richtig
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
  // Tiere
  $q[]='Hund;Welches Haustier bellt?';
  $q[]='Hund;Welches Haustier gehorcht treu seinem Herrn?';
  $q[]='Hund;Häufigstes Haustier neben der Katze ist der:';
  $q[]='Katze;Welches Haustier macht miau?';
  $q[]='Katze;Vor welchem Haustier sollte die Maus flüchten?';
  $q[]='Katze;Welches Tier jagt im Haus die Mäuse?';
  $q[]='Katze;Wer jagt die Mäuse in Scheune und Garten?';
  $q[]='Katze;Häufigstes Haustier neben dem Hund ist die:';
  $q[]='Kater;Die männliche Katze ist ein:';
  $q[]='Schwein;Welches Tier grunzt?';
  $q[]='Ferkel;Neu geborene Hausschweine nennt man:';
  $q[]='Kuh;Welches Tier macht muh?';
  $q[]='Kuh;Von welchem Tier kommt die Milch?';
  $q[]='Pferd;Welches Tier wiehert?';
  $q[]='Pferd;Auf welchem Tier reitet der Reiter?';
  $q[]='Pferd;Welches Tier zieht die Kutsche?';
  $q[]='Pferd;Welches Tier läuft auf der Galopprennbahn?';
  $q[]='Ziege;Welches Tier meckert?';
  $q[]='Schaf;Welches Tier gibt die Wolle?';
  $q[]='Huhn;Welches Tier legt die Eier?';
  $q[]='Hahn;Welcher Vogel kräht am Morgen?';
  $q[]='Ente;Welcher Vogel außer der Gans schnattert?';
  $q[]='Vogel;Welches Tier fliegt und zwitschert?';
  $q[]='Vogel;Welches Tier hat Flügel und Federn?';
  $q[]='Vogel;Welches Tier hat Federn und Flügel?';
  $q[]='Biene;Welches Insekt liefert den Honig?';
  $q[]='Hase;Welches langohrige Tier hoppelt über das Feld?';
  $q[]='Hase;Im Märchen gibt es den Wettlauf zwischen Igel und:';
  $q[]='Igel;Im Märchen gibt es den Wettlauf zwischen Hase und:';
  $q[]='Igel;Welches heimische Säugetier hat Stacheln?';
  $q[]='Hirsch;Welches Tier im Wald trägt ein Geweih?';
  $q[]='Fisch;Welches Tier zieht der Angler an Land?';
  $q[]='Fisch;Welches Tier hängt an der Angel?';
  $q[]='Frosch;Wer quakt im Teich?';
  $q[]='Giraffe;Welches Tier hat den längsten Hals?';
  $q[]='Elefant;Welches Tier hat einen Rüssel?';
  $q[]='Elefant;Welches Tier hat lange weiße Stoßzähne?';
  $q[]='Löwe;Wer gilt als König unter den Tieren Afrikas?';
  $q[]='Nashorn;Welches Tier hat ein Horn auf der Nase?';
  $q[]='Krokodil;Welche große Echse lebt im Nil?';
  $q[]='Pinguin;Welcher Vogel trägt einen Frack?';
  // Farben
  $q[]='gelb;Welche Farbe hat die Sonne?';
  $q[]='gelb;Welche Farbe hat ein reifes Weizenfeld?';
  $q[]='rot;Welche Farbe hat das Blut?';
  $q[]='rot;In welcher Farbe blüht der Klatschmohn?';
  $q[]='rot;Welche Farbe haben reife Erdbeeren?';
  $q[]='grün;Welche Farbe hat frisches Gras?';
  $q[]='grün;Welche Farbe haben die Blätter am Baum?';
  $q[]='grün;Welche Farbe hat Spinat?';
  $q[]='blau;In welcher Farbe strahlt ein wolkenloser Himmel?';
  $q[]='braun;Welche Farbe hat Kakao?';
  $q[]='braun;Welche Farbe hat gewöhnliche Schokolade?';
  $q[]='schwarz;Welche Farbe hat Kohle?';
  $q[]='schwarz;Welche Farbe hat der Ruß?';
  $q[]='schwarz;Welche Farbe haben Autoreifen üblicherweise?';
  // Maerchen
  $q[]='Hase;Im Märchen gibt es den Wettlauf zwischen Igel und:';
  $q[]='Igel;Im Märchen gibt es den Wettlauf zwischen Hase und:';
  $q[]='Wolf;Welches Tier ist böse zu Rotkäppchen?';
  $q[]='Wolf;Wer frisst im Märchen die 7 Geißlein?';
  $q[]='Rosenrot;Wie heißt die Schwester von Schneeweißchen?';
  $q[]='Bär;Welches große Tier wohnt bei Schneeweißchen und Rosenrot?';
  $q[]='Gretel;Wie heißt im Märchen die Schwester von Hänsel?';
  $q[]='Gretel;Wer schiebt im Märchen die Hexe in den Ofen?';
  $q[]='Hexe;Wer hält Hänsel und Gretel gefangen?';
  $q[]='Hexe;Wer füttert im Märchen den Hänsel fett?';
  $q[]='Hexe;Wen schiebt Gretel in den Backofen?';
  $q[]='Hexe;Welche Märchenfigur wohnt im Knusperhäuschen?';
  $q[]='Holle;Die Goldmarie bekommt Ihren Lohn von Frau:';
  $q[]='Holle;Die Pechmarie bekommt Ihr Pech von Frau:';
  $q[]='Holle;Wie heißt die Frau, die Schnee aus den Betten schüttelt?';
  $q[]='Brunnen;Wohinein fallen die Spule und Goldmarie?';
  $q[]='Prinz;Wer weckt Dornröschen aus dem Schlaf?';
  $q[]='Königin;Welche böse Frau will Schneewittchen vergiften?';
  $q[]='Königin;Wer neben Schneewittchen ist die Schönste im Land?';
  $q[]='Jäger;Wer muß Schneewittchen in den Wald bringen?';
  $q[]='Jäger;Wer soll Schneewittchen im Auftrag töten?';
  $q[]='Schneewittchen;Wer wohnt bei den 7 Zwergen?';
  $q[]='Schneewittchen;Wer wird von der Königin mit einem Kamm vergiftet?';
  $q[]='Schneewittchen;Wen vergiftet die Königin mit einem Apfel?';

  $a=explode(';',$q[rand(0,count($q)-1)],2);
  $this->Question=str_replace('"','&quot;',$a[1]);
  return $a[0];
 }
}
?>