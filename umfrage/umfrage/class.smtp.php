<?php
class smtp_class{

 var $socket=0;
 var $responsestring='';
 var $responsecode=0;
 var $errno=0;
 var $errstr='';

 function open($host,$port){
  return ($this->socket=@fsockopen($host,$port,$this->errno,$this->errstr,20));
 }

 function close(){fclose($this->socket);}

 function send($line){
  fputs($this->socket,$line."\r\n");
 }

 function receive($code=0){
  $this->responsestring=''; $this->responsecode=0; $rcv='?';
  while(substr($rcv,3,1)!=' '){
   $rcv=fgets($this->socket,1024);
   $this->responsestring.=$rcv;
  }
  $this->responsecode=(int)substr($rcv,0,3);
  return $this->responsecode==$code;
 }

 function ready(){
  return $this->receive(220);
 }

 function mailfrom($frm){
  $this->send('MAIL FROM:<' .$frm.'>');
  return $this->receive(250);
 }

 function rcptto($to){
  $this->send('RCPT TO:<'.$to.'>');
  return $this->receive(250);
 }

 function data(){
  $this->send('DATA');
  return $this->receive(354);
 }

 function complete(){
  $this->send('.');
  return $this->receive(250);
 }

 function quit(){
  $this->send('QUIT');
 }

 function login($sUser,$sPass,$bAuth){
  $bRes=false;
  $sHst=(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'localhost')));
  if(($sLocalAddr=@gethostbyname($sHst))!==$sHst) if(($sName=@gethostbyaddr($sLocalAddr))!==$sLocalAddr) $sHst=$sName;
  if(substr($sHst,0,4)=='www.') $sHst=substr($sHst,4); elseif(substr($sHst,0,5)=='shop.') $sHst=substr($sHst,5);
  $this->send('EHLO '.$sHst);
  if(!$bRes=$this->receive(250)){
   if($this->responsecode!=503){
    $this->send('HELO '.$sHst);
    if(!$bRes=$this->receive(250)) if($this->responsecode==503) $bRes=true; //schon angemeldet
   }else $bRes=true; //schon angemeldet
  }
  if($bRes){
   $bRes=false; $bTLS=false; $sMethods='';
   if(!empty($sUser)&&!empty($sPass)){
    $aRcv=explode("\n",str_replace("\r",'',$this->responsestring)); $nRcv=count($aRcv);
    for($i=0;$i<$nRcv;$i++) if(substr($aRcv[$i],4,8)=='STARTTLS') $bTLS=true; elseif(substr($aRcv[$i],4,4)=='AUTH') $sMethods='#'.rtrim(substr($aRcv[$i],8));
    if($bTLS&&(!(defined('SMTP_No_TLS')&&SMTP_No_TLS))){ // TLS starten
     $this->send('STARTTLS');
     if($bRes=$this->receive(220)){
      if(@stream_socket_enable_crypto($this->socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT)){
       $this->send('EHLO '.$sHst); // ReSend EHLO
       if($bRes=$this->receive(250)){
        $aRcv=explode("\n",str_replace("\r",'',$this->responsestring)); $nRcv=count($aRcv);
        for($i=0;$i<$nRcv;$i++) if(substr($aRcv[$i],4,4)=='AUTH'){$sMethods='#'.rtrim(substr($aRcv[$i],8)); break;}
      }}else $bRes=false;
    }}
    if($bAuth&&$sMethods){ // mit Authentifizierung
     $sMethod='PLAIN'; $nM=9999;
     if(($n=strpos($sMethods,'PLAIN'))&&$n<$nM){$sMethod='PLAIN'; $nM=$n;}
     if(($n=strpos($sMethods,'LOGIN'))&&$n<$nM){$sMethod='LOGIN'; $nM=$n;}
     if(($n=strpos($sMethods,'CRAM-MD5'))&&$n<$nM){$sMethod='CRAM-MD5';}
     if($sMethod=='PLAIN') $bRes=$this->loginPlain($sUser,$sPass);
     elseif($sMethod=='LOGIN') $bRes=$this->loginLogin($sUser,$sPass);
     elseif($sMethod=='CRAM-MD5') $bRes=$this->loginCram($sUser,$sPass);
  }}}
  return $bRes;
 }

 function loginPlain($sUser,$sPass){
  $bRes=false;
  $this->send('AUTH PLAIN');
  if($bRes=$this->receive(334)){
   $this->send(base64_encode("\0".$sUser."\0".$sPass));
   $bRes=$this->receive(235);
  }else if($this->responsecode==503) $bRes=true; //schon angemeldet
  return $bRes;
 }

 function loginLogin($sUser,$sPass){
  $bRes=false;
  $this->send('AUTH LOGIN');
  if($bRes=$this->receive(334)){
   $this->send(base64_encode($sUser));
   if($bRes=$this->receive(334)){
    $this->send(base64_encode($sPass));
    $bRes=$this->receive(235);
   }
  }else if($this->responsecode==503) $bRes=true; //schon angemeldet
  return $bRes;
 }

 function loginCram($sUser,$sPass){
  $bRes=false;
  $this->send('AUTH CRAM-MD5');
  if($bRes=$this->receive(334)){
   $sPass=(strlen($sPass)<64)?str_pad($sPass,64,chr(0)):((strlen($sPass)>64)?pack('H32',md5($sPass)):$sPass);
   $aRcv=explode("\n",str_replace("\r",'',$this->responsestring));
   $md5_challenge=base64_decode(rtrim(substr($aRcv[0],4)));
   $md5_digest=md5((substr($sPass,0,64)^str_repeat(chr(0x5C),64)).(pack('H32',md5((substr($sPass,0,64)^str_repeat(chr(0x36),64)).$md5_challenge))));
   $base64_method_cram_md5=base64_encode($sUser.' '.$md5_digest);
   $this->send($base64_method_cram_md5);
   $bRes=$this->receive(235);
  }else if($this->responsecode==503) $bRes=true; //schon angemeldet
  return $bRes;
 }

}
?>