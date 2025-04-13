<?php
class Captcha{ // neue Version im PHP-5-Stil
/*--(c) J. Hummel------------------------------------------------------------
 ->MakeCaptcha()                       erzeugt die Grafik
 ->PublicKey                           enthaelt den PublicKey
 ->PrivateKey                          enthaelt den PrivatenKey
 ->PictureName                         liefert den Bildnamen als URL
 ->KeyFile              captcha.csv    Name der Schluesseldatei
 ->BgColor              #F0F0F0        Hintergrundfarbe der Grafik
 ->TxColor              #000099        Textfarbe der Grafik
 ->TestKey(PrivateKey)                 prueft die 5 Zeichen Schluessellaenge
 ->ValidKey(PrivateKey,PublicKey)      vergleicht die Schluessel
 ->DeleteCaptcha(PrivateKey,PublicKey) loescht Grafik und Schluessel
 ---------------------------------------------------------------------------*/

 var $PublicKey='';
 var $PrivateKey='';
 var $PictureName='';
 var $FilePath='';
 var $FileURL='';
 var $NameLength=7;
 var $ImageExt='C.jpg';
 var $KeyFile='captcha.csv';
 var $TxColor='#000099'; var $BgColor='#F0F0F0';

 function __construct($Path='',$URL='',$SubDir='',$Keys='captcha.csv'){ //Konstruktor
  if(strlen($SubDir)>0) if(substr($SubDir,-1)!='/') $SubDir.='/';
  $this->FilePath=$Path.$SubDir; $this->FileURL=$URL.$SubDir; $this->KeyFile=$Keys;
 }

 function MakeCaptcha($TCol='',$BCol=''){ //Grafik erzeugen und Namen liefern
  $Res='Err'; $Cod=chr(rand(65,90)); if($Cod=='O') $Cod=chr(rand(65,77));
  $Cod.=substr(time()+rand(9,13),-4); $Pub=md5($Cod); $this->PublicKey=$Pub;
  if($Img=imagecreatetruecolor(120,24)){
   if(!$BCol) $BCol=$this->BgColor; if(!$TCol) $TCol=$this->BgColor;
   $Col=ImageColorAllocate($Img,hexdec(substr($BCol,1,2)),hexdec(substr($BCol,3,2)),hexdec(substr($BCol,5,2)));
   imagefill($Img,0,0,$Col);
   $Col=ImageColorAllocate($Img,hexdec(substr($TCol,1,2)),hexdec(substr($TCol,3,2)),hexdec(substr($TCol,5,2)));
   for($i=0;$i<5;$i++) imagestring($Img,5,$i*23+rand(4,12),rand(2,8),substr($Cod,$i,1),$Col);
   $Nam=substr($Pub,0,$this->NameLength).$this->ImageExt;
   if(is_writable($this->FilePath)&&@imagejpeg($Img,$this->FilePath.$Nam,80)){
    $this->PictureName=$this->FileURL.$Nam;
    if(file_exists($this->FilePath.$this->KeyFile)){
     $aC=file($this->FilePath.$this->KeyFile); $Csv=''; $nTime=time(); $RefTime=strval($nTime-3600);
     if(is_array($aC)&&($Cnt=count($aC))) for($i=0;$i<$Cnt;$i++){
      $sLn=rtrim($aC[$i]);
      if(substr($sLn,0,10)>$RefTime) $Csv.=$sLn."\n"; elseif(file_exists($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt)) unlink($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt);
     }
     if(is_writable($this->FilePath.$this->KeyFile)&&($f=fopen($this->FilePath.$this->KeyFile,'w'))){
      fwrite($f,$Csv.$nTime.';'.$Pub."\n"); fclose($f); $Res='OK';
     }else $Res.='_KeyFile';
    }else $Res.='_KeyFile';
   }else $Res.='_SaveImg';
   imagedestroy($Img);
  }else $Res.='_GD2Lib';
  return $Res;
 }

 function SetCaptcha($Priv,$Pub){ //vorhandenes Captcha reaktivieren
  $this->PrivateKey=$Priv; $this->PublicKey=$Pub;
  if($Pub) $this->PictureName=$this->FileURL.substr($Pub,0,$this->NameLength).$this->ImageExt;
 }

 function TestKey(){ //Laenge des PrivatKey testen
  if(strlen($this->PrivateKey)==5) return true; else return false;
 }

 function ValidKey(){ //Gueltigkeit der Keys pruefen
  $Res=false;
  if(strlen($this->PrivateKey)==5){
   if(md5($this->PrivateKey)==$this->PublicKey){
    if(file_exists($this->FilePath.$this->KeyFile)) $aC=file($this->FilePath.$this->KeyFile); else $aC=array();
    if(is_array($aC)&&($Cnt=count($aC))){
     for($i=0;$i<$Cnt;$i++) if(substr(rtrim($aC[$i]),11)==$this->PublicKey){$Res=true; break;}
    }
   }
  }
  return $Res;
 }

 function DeleteCaptcha($bChk=true){ //gueltiges Captcha loeschen
  $Res=false;
  if(($bChk&&strlen($this->PrivateKey)==5&&md5($this->PrivateKey)==$this->PublicKey)||(!$bChk&&strlen($this->PublicKey)==32)){
   if(file_exists($this->FilePath.$this->KeyFile)){
    $aC=file($this->FilePath.$this->KeyFile); $RefTime=strval(time()-900); $Csv='';
    if(is_array($aC)&&($Cnt=count($aC))){
     for($i=0;$i<$Cnt;$i++){
      $sLn=rtrim($aC[$i]);
      if(substr($sLn,11)==$this->PublicKey){$Res=true; if(file_exists($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt)) unlink($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt); $this->PrivateKey='';}
      else{
       if(substr($sLn,0,10)>$RefTime) $Csv.=$sLn."\n"; elseif(file_exists($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt)) unlink($this->FilePath.substr($sLn,11,$this->NameLength).$this->ImageExt);
      }
     }
     if(is_writable($this->FilePath.$this->KeyFile)&&($f=fopen($this->FilePath.$this->KeyFile,'w'))){fwrite($f,$Csv); fclose($f);}
    }
   }
  }
  return $Res;
 }

}
?>