<?php
class PlainMail{
/*------------------------------------------------
 ->To
 ->AddTo(Address,Name)
 ->ClearTo()
 ->Cc
 ->AddCc(Address,Name)
 ->Bcc
 ->AddBcc(Address,Name)
 ->SetFrom(Address,Name)
 ->SetReturnPath(Address)
 ->SetReplyTo(Address,Name)
 ->SetEnvelopeSender(EnvelopeSender)
 ->Subject
 ->Text
 ->Send()
 ->Smtp      //Default: false
 ->SmtpHost  //Default: localhost
 ->SmtpPort  //Default: 25
 ->SmtpAuth  //Default: false
 ->SmtpUser
 ->SmtpPass
 ------------------------------------------------*/

 var $To =''; var $ToH=''; var $Cc =''; var $Bcc='';
 var $From=''; var $Frm=''; var $RetPath=''; var $ReplyTo=''; var $EnvSndr='';
 var $Subject='kein Betreff'; var $Text='';
 var $Smtp=false; var $SmtpHost='localhost'; var $SmtpPort=25;
 var $SmtpAuth=false; var $SmtpUser=''; var $SmtpPass='';

 function ClearTo(){$this->To=''; $this->ToH='';}

 function AddTo($eml,$name=''){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   if(!empty($this->To)){$this->To.=','; $this->ToH.=",\r\n ";}
   $this->To.=$eml; $this->ToH.=trim($this->EncodeName(trim($name)).' <'.$eml.'>');
   return true;
  }else return false;
 }

 function AddCc($eml,$name=''){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   if(!empty($this->Cc)) $this->Cc.=', ';
   $this->Cc.=trim($this->EncodeName(trim($name)).' <'.$eml.'>');
   return true;
  }else return false;
 }

 function AddBcc($eml,$name=''){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   if(!empty($this->Bcc)) $this->Bcc.=',';
   $this->Bcc.=$eml;
   return true;
  }else return false;
 }

 function SetFrom($eml,$name=''){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   $this->Frm=$eml;
   $this->From=trim($this->EncodeName(trim($name)).' <'.$eml.'>');
   return true;
  }else return false;
 }

 function SetReturnPath($eml){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   $this->RetPath='<'.$eml.'>';
   return true;
  }else return false;
 }

 function SetReplyTo($eml,$name=''){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   $this->ReplyTo=trim($this->EncodeName(trim($name)).' <'.$eml.'>');
   return true;
  }else return false;
 }

 function SetEnvelopeSender($eml){
  if(($eml=trim($eml))&&($p=strpos($eml,'@'))&&(strrpos($eml,'.')>$p)){
   $this->EnvSndr=$eml;
   return true;
  }else return false;
 }

 function EncodeSubject($In){
  if(preg_match('/[\000-\011\013\014\016-\037\075\077\137\177-\377]/',$In)>0){//Zeichen: =?_
   $In=preg_replace_callback('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/',function($aM){return sprintf('=%02X',ord($aM[0]));},$In);
   $In='=?ISO-8859-1?Q?'.str_replace(' ','_',$In).'?=';
  }
  return $In;
 }

 function EncodeName($In){
  if(preg_match('/[\000-\011\013\014\016-\037\075\077\137\177-\377]/',$In)>0){//Zeichen: =?_
   $In=preg_replace_callback('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/',function($aM){return sprintf('=%02X',ord($aM[0]));},$In);
   $In='=?ISO-8859-1?Q?'.str_replace(' ','_',$In).'?=';
  }elseif(strpos($In,' ')>0) $In='"'.$In.'"';
  return $In;
 }

 function Send(){
  if(!empty($this->To)){
   return(!$this->Smtp?$this->PhpSend():$this->SmtpSend());
  }else return false;
 }

 function PhpSend(){
  if(isset($_SERVER['REMOTE_HOST'])) $RHo=$_SERVER['REMOTE_HOST']; else $RHo='phpmail';
  if(isset($_SERVER['HTTP_HOST'])) $LHo=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $LHo=$_SERVER['SERVER_NAME']; else $LHo='localhost';
  if(substr($LHo,0,4)=='www.') $LHo=substr($LHo,4); elseif(substr($LHo,0,5)=='shop.') $LHo=substr($LHo,5);
  $Tm=date('Z'); if($Tm<0) $TS='-'; else $TS='+'; $Tm=abs($Tm); $Tm=date("D, j M Y H:i:s ").$TS.sprintf('%04d',($Tm/3600)*100+($Tm%3600)/60);
  $Hd='Received: from '.$RHo.' (['.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1').'])';
  $Hd.="\r\n\tby ".$LHo.' with PHP-PlainMail; '.$Tm;
  $Hd.="\r\nDate: ".$Tm; $UId=md5(uniqid(time()));
  //$Hd.="\r\nTo: ".$this->ToH; //funktioniert nur auf wenigen Servern
  if(!empty($this->Cc))      $Hd.="\r\nCc: ".$this->Cc;
  if(!empty($this->Bcc))     $Hd.="\r\nBcc: ".$this->Bcc;
  if(!empty($this->From))    $Hd.="\r\nFrom: ".$this->From;
  if(!empty($this->RetPath)) $Hd.="\r\nReturn-Path: ".$this->RetPath; //wird teils ersetzt
  if(!empty($this->ReplyTo)) $Hd.="\r\nReply-To: ".$this->ReplyTo;
  $Hd.="\r\nMessage-ID: <".$UId.'@'.$LHo.'>';
  $Hd.="\r\nContent-Type: text/plain; charset=\"iso-8859-1\"";
  $Hd.="\r\nContent-Transfer-Encoding: 8bit";
  $Hd.="\r\nX-Mailer: PHP-PlainMail";
  if(empty($this->EnvSndr)) return @mail($this->To,$this->EncodeSubject($this->Subject),wordwrap(str_replace("\n","\r\n",str_replace("\r",'',$this->Text)),76,"\r\n"),$Hd);
  else return @mail($this->To,$this->EncodeSubject($this->Subject),wordwrap(str_replace("\n","\r\n",str_replace("\r",'',$this->Text)),76,"\r\n"),$Hd,'-f '.$this->EnvSndr);
 }

 function SmtpSend(){
  if(isset($_SERVER['HTTP_HOST'])) $LHo=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $LHo=$_SERVER['SERVER_NAME']; else $LHo='localhost';
  if(substr($LHo,0,4)=='www.') $LHo=substr($LHo,4); elseif(substr($LHo,0,5)=='shop.') $LHo=substr($LHo,5);
  $Tm=date('Z'); if($Tm<0) $TS='-'; else $TS='+'; $Tm=abs($Tm); $Tm=date("D, j M Y H:i:s ").$TS.sprintf('%04d',($Tm/3600)*100+($Tm%3600)/60);
  require_once(dirname(__FILE__).'/class.smtp.php'); $smtp=new smtp_class(); $bRes=false;
  if($smtp->open($this->SmtpHost,$this->SmtpPort)){
   if($smtp->ready()){
    if($smtp->login($this->SmtpUser,$this->SmtpPass,$this->SmtpAuth)){
     if($smtp->mailfrom($this->Frm)){
      $aTo=explode(',',$this->To); $bRcptto=false;
      if(is_array($aTo)&&($n=count($aTo))) for($i=0;$i<$n;$i++) if($smtp->rcptto($aTo[$i])) $bRcptto=true;
      if($bRcptto){
       if($smtp->data()){
        $smtp->send('Date: '.$Tm); $UId=md5(uniqid(time()));
        if(!empty($this->From))    $smtp->send('From: '.$this->From);
        $smtp->send('Subject: '.$this->EncodeSubject($this->Subject));
        $smtp->send('To: '.$this->ToH);
        if(!empty($this->Cc))      $smtp->send('Cc: '.$this->Cc);
        if(!empty($this->RetPath)) $smtp->send('Return-Path: '.$this->RetPath);
        if(!empty($this->ReplyTo)) $smtp->send('Reply-To: '.$this->ReplyTo);
        $smtp->send('Message-ID: <'.$UId.'@'.$LHo.'>');
        $smtp->send('Content-Type: text/plain; charset="iso-8859-1"');
        $smtp->send('Content-Transfer-Encoding: 8bit');
        $smtp->send('X-Mailer: PHP-PlainMail via SMTP');
        $smtp->send('');
        $smtp->send(wordwrap(str_replace("\n","\r\n",str_replace("\r",'',$this->Text)),76,"\r\n"));
        $bRes=$smtp->complete();
    }}}}
    $smtp->quit();
   }
   $smtp->close();
  }
  return $bRes;
 }
}
?>