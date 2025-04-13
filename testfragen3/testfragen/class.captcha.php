<?php
class Captcha{
/*--(c) J. Hummel------------------------------------------------------------
 ->MakeCaptcha()                       erzeugt die Grafik
 ->PublicKey                           enthält den PublicKey
 ->PrivateKey                          enthält den PrivatenKey
 ->PictureName                         liefert den Bildnamen als URL
 ->KeyFile              captcha.csv    Name der Schlüsseldatei
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

 function Captcha($Path='',$URL='',$SubDir='',$Keys='captcha.csv'){ //Konstruktor
  if(strlen($SubDir)>0) if(substr($SubDir,-1)!='/') $SubDir.='/';
  $this->FilePath=$Path.$SubDir; $this->FileURL=$URL.$SubDir; $this->KeyFile=$Keys;
 }

 function MakeCaptcha($TCol='',$BCol=''){ //Grafik erzeugen und Namen liefern
  $Res='Err'; $Cod=chr(rand(65,90)).substr(time()+rand(9,13),-4); $Pub=md5($Cod); $this->PublicKey=$Pub;
  if($Img=imagecreatetruecolor(120,24)){
   if(!$BCol) $BCol=$this->BgColor; if(!$TCol) $TCol=$this->BgColor;
   $Col=ImageColorAllocate($Img,hexdec(substr($BCol,1,2)),hexdec(substr($BCol,3,2)),hexdec(substr($BCol,5,2)));
   imagefill($Img,0,0,$Col);
   $Col=ImageColorAllocate($Img,hexdec(substr($TCol,1,2)),hexdec(substr($TCol,3,2)),hexdec(substr($TCol,5,2)));
   for($i=0;$i<5;$i++) imagestring($Img,5,$i*23+rand(4,12),rand(2,8),$Cod{$i},$Col);
   $Nam=substr($Pub,0,$this->NameLength).$this->ImageExt;
   if(imagejpeg($Img,$this->FilePath.$Nam,80)){
    $this->PictureName=$this->FileURL.$Nam;
    $Csv=''; $nTime=time(); $RefTime=strval($nTime-3600);
    if(($aC=@file($this->FilePath.$this->KeyFile))&&is_array($aC)&&($Cnt=count($aC))) for($i=0;$i<$Cnt;$i++){
     $Ln=rtrim($aC[$i]);
     if(substr($Ln,0,10)>$RefTime) $Csv.=$Ln."\n"; else @unlink($this->FilePath.substr($Ln,11,$this->NameLength).$this->ImageExt);
    }
    if($f=fopen($this->FilePath.$this->KeyFile,'w')){
     fwrite($f,$Csv.$nTime.';'.$Pub."\n"); fclose($f); $Res='OK';
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

 function TestKey(){ //Länge des PrivatKey testen
  if(strlen($this->PrivateKey)==5) return true; else return false;
 }

 function ValidKey(){ //Gültigkeit der Keys prüfen
  $Res=false;
  if(strlen($this->PrivateKey)==5){
   if(md5($this->PrivateKey)==$this->PublicKey){
    $aC=@file($this->FilePath.$this->KeyFile);
    if(is_array($aC)&&($Cnt=count($aC))){
     for($i=0;$i<$Cnt;$i++) if(substr(rtrim($aC[$i]),11)==$this->PublicKey){$Res=true; break;}
    }
   }
  }
  return $Res;
 }

 function DeleteCaptcha($bChk=true){ //gültiges Captcha löschen
  $Res=false;
  if(($bChk&&strlen($this->PrivateKey)==5&&md5($this->PrivateKey)==$this->PublicKey)||(!$bChk&&strlen($this->PublicKey)==32)){
   $RefTime=strval(time()-900); $Csv='';
   if(($aC=@file($this->FilePath.$this->KeyFile))&&is_array($aC)&&($Cnt=count($aC))){
    for($i=0;$i<$Cnt;$i++){
     $Ln=rtrim($aC[$i]);
     if(substr($Ln,11)==$this->PublicKey){$Res=true; @unlink($this->FilePath.substr($Ln,11,$this->NameLength).$this->ImageExt); $this->PrivateKey='';}
     else{
      if(substr($Ln,0,10)>$RefTime) $Csv.=$Ln."\n"; else @unlink($this->FilePath.substr($Ln,11,$this->NameLength).$this->ImageExt);
     }
    }
    if($f=fopen($this->FilePath.$this->KeyFile,'w')){fwrite($f,$Csv); fclose($f);}
   }
  }
  return $Res;
 }

}
?>