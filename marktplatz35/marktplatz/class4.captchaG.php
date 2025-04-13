<?php
class Captcha{
/*--(c) J. Hummel------------------------------------------------------------
 ->Generate(TxColor,BgColor)           erzeugt die Grafik
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
 var $Type='G';

 function Captcha($sPath='./captcha/',$sKeyfile='captcha.csv'){ //Konstruktor
  $this->Path=$sPath;
  $this->KeyFile=$sPath.$sKeyfile;
  if(defined('CAPTCHA_SALT')) $this->Salt=CAPTCHA_SALT;
 }

 function Generate($sTCol='#000099',$sBCol='#F0F0F0'){ //Grafik erzeugen und Namen liefern
  $Res='Err'; $sCod=chr(rand(65,90)); if($sCod=='O') $sCod=chr(rand(65,77));
  $sCod.=substr(time()+rand(9,13),-4); $sPub=md5($this->Salt.'*'.$sCod); $this->PublicKey=$sPub;
  if($Img=imagecreatetruecolor(120,24)){
   $sCol=ImageColorAllocate($Img,hexdec(substr($sBCol,1,2)),hexdec(substr($sBCol,3,2)),hexdec(substr($sBCol,5,2)));
   imagefill($Img,0,0,$sCol);
   $sCol=ImageColorAllocate($Img,hexdec(substr($sTCol,1,2)),hexdec(substr($sTCol,3,2)),hexdec(substr($sTCol,5,2)));
   for($i=0;$i<5;$i++) imagestring($Img,5,$i*23+rand(4,12),rand(2,8),substr($sCod,$i,1),$sCol);
   $sNam=substr($sPub,0,8).'.jpg';
   if(is_writable($this->Path)&&@imagejpeg($Img,$this->Path.$sNam,80)){
    $this->Question=$sNam;
    if(file_exists($this->KeyFile)){
     $aC=file($this->KeyFile); $sCsv=''; $nTime=time(); $sRefTime=strval($nTime-3600);
     if(is_array($aC)&&($nCnt=count($aC))) for($i=0;$i<$nCnt;$i++){
      $sLn=rtrim($aC[$i]);
      if(substr($sLn,0,10)>$sRefTime) $sCsv.=$sLn."\n"; elseif(file_exists($this->Path.substr($sLn,11,8).'.jpg')) unlink($this->Path.substr($sLn,11,8).'.jpg');
     }
     if(is_writable($this->KeyFile)&&($f=fopen($this->KeyFile,'w'))){
      fwrite($f,$sCsv.$nTime.';'.$sPub."\n"); fclose($f); $Res='OK';
     }else $Res.='_KeyFile';
    }else $Res.='_KeyFile';
   }else $Res.='_SaveImg';
   imagedestroy($Img);
  }else $Res.='_GD2Lib';
  return $Res;
 }

 function Test($sAnswer,$sPublic,$sQuestion){ //Antwort pruefen
  $bResult=false;
  $this->Question=substr($sPublic,0,8).'.jpg';
  $sAnswer=ucfirst($sAnswer); $this->PrivateKey=$sAnswer; $this->PublicKey=$sPublic;
  if(strlen($sAnswer)==5&&$sPublic==md5($this->Salt.'*'.$sAnswer)){ //richtig
   if(file_exists($this->KeyFile)) $aC=file($this->KeyFile); else $aC=array();
   if(is_array($aC)&&($nCnt=count($aC))){
    for($i=0;$i<$nCnt;$i++) if(rtrim(substr($aC[$i],11))==$sPublic){$bResult=true; $this->Question=''; break;}
   }
  }
  return $bResult;
 }

 function Delete(){ //Captcha loeschen
  $bResult=false;
  $sAnswer=$this->PrivateKey; $sPublic=$this->PublicKey;
  if(strlen($sAnswer)==5&&$sPublic==md5($this->Salt.'*'.$sAnswer)){ //richtig
   if(file_exists($this->KeyFile)){
    $aC=file($this->KeyFile); $sCsv=''; $sRefTime=strval(time()-1200);
    if(is_array($aC)&&($nCnt=count($aC))){
     for($i=0;$i<$nCnt;$i++){
     $sLn=rtrim($aC[$i]);
      if(substr($sLn,11)==$sPublic){$this->PrivateKey=''; $bResult=true; if(file_exists($this->Path.substr($sLn,11,8).'.jpg')) unlink($this->Path.substr($sLn,11,8).'.jpg');}
      else{if(substr($sLn,0,10)>$sRefTime) $sCsv.=$sLn."\n"; elseif(file_exists($this->Path.substr($sLn,11,8).'.jpg')) unlink($this->Path.substr($sLn,11,8).'.jpg');}
     }
     if(is_writable($this->KeyFile)&&($f=fopen($this->KeyFile,'w'))){fwrite($f,$sCsv); fclose($f);}
     $nH=opendir(substr($this->Path,0,-1)); $aH=array(); $sRefTime-=2400; //verwaiste Bilder
     while($sF=readdir($nH)) if(substr($sF,-4,4)=='.jpg') $aH[]=$this->Path.$sF; closedir($nH);
     foreach($aH as $sF) if(filemtime($sF)<$sRefTime) unlink($sF); clearstatcache();
    }
   }
  }
  return $bResult;
 }

}
?>