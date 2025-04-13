<?php
function fUmfSeite(){
 $X=UMF_TxAktivFehl; $MTyp='Fehl'; $bOK=false; $sAkt=''; $sEml='';

 if(UMF_SQL){ //SQL-Verbindung oeffnen
  $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa);
  if(!mysqli_connect_errno()){if(UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);} else $DbO=NULL;
 }

 $sAkt=(isset($_GET['umf_Aktion'])?$_GET['umf_Aktion']:'').(isset($_POST['umf_Aktion'])?$_POST['umf_Aktion']:'');
 if($sId=fUmfValidId($sAkt)){
  if($_SERVER['REQUEST_METHOD']!='POST'){ //GET pruefen
   if(!UMF_SQL){ //Textdateien
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr($aD[$i],0,$p+1)==$s.'0'){$X=UMF_TxAktivieren; $a=explode(';',$aD[$i]); $sEml=fUmfDeCode($a[4]);}
     elseif(substr($aD[$i],0,$p+1)==$s.'1'){$X=UMF_TxAktiviert; $sAkt='login';}
     $MTyp='Meld'; $bOK=true; break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT Nummer,aktiv,eMail FROM '.UMF_SqlTabN.' WHERE Nummer="'.$sId.'"')){
     if($a=$rR->fetch_row()) if($a[0]==$sId&&$a[1]=='0'){$X=UMF_TxAktivieren; $sEml=$a[2];}
     elseif($a[0]==$sId&&$a[1]=='1'){$X=UMF_TxAktiviert; $sAkt='login';}
     $rR->close(); $MTyp='Meld'; $bOK=true;
    }else $X=UMF_TxSqlFrage;
   }else $X=$Msg; //SQL
  }else{ //POST freischalten
   if(!UMF_SQL){ //Textdateien
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr($aD[$i],0,$p+1)==$s.'0'){
      $aD[$i]=$sId.';1'.substr(rtrim($aD[$i]),$p+1)."\n"; $bOK=true;
      if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $X=UMF_TxAktiviert; $MTyp='Erfo'; $sAkt='login';
      }else{$X=str_replace('#',fUmfTx(UMF_TxBenutzer),UMF_TxDateiRechte);}
     }
     break;
    }
   }elseif($DbO){ //SQL
    if($rR=$DbO->query('SELECT Nummer,aktiv FROM '.UMF_SqlTabN.' WHERE Nummer="'.$sId.'"')){
     $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
     if($a[0]==$sId&&$a[1]=='0'){$bOK=true;
      if($DbO->query('UPDATE IGNORE '.UMF_SqlTabN.' SET aktiv="1" WHERE Nummer="'.$sId.'"')){$X=UMF_TxAktiviert; $MTyp='Erfo'; $sAkt='login';}
      else $X=UMF_TxSqlAendr;
     }
    }else $X=UMF_TxSqlFrage;
   }//SQL
  }//POST
 }

 //Formular- und Tabellenanfang
 $X=' <p class="umf'.$MTyp.'">'.fUmfTx($X).'</p>
 <form name="umfForm" class="umfForm" action="'.UMF_Self.'" method="post">
 <input type="hidden" name="umf_Aktion" value="'.$sAkt.'" />'.rtrim("\n ".UMF_Hidden).'
 <table class="umfLogi" border="0" style="margin:16px;" cellpadding="0" cellspacing="0">
  <tr><td class="umfLogi" style="padding:8px;text-align:center;">'.fUmfTx($sAkt!='login'?$sEml.'\n '.UMF_TxPassiv:UMF_TxLoginLogin.'\n '.UMF_TxOder.'\n '.UMF_TxLoginVergessen.'?').'</td></tr>
 </table>';
 if($bOK) $X.="\n".' <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxWeiter).'" title="'.fUmfTx(UMF_TxWeiter).'" />'; else $X.='&nbsp;';
 $X.="\n  </form>\n";
 return $X;
}

function fUmfValidId($s){
 $nCod=(int)substr(UMF_Schluessel,-2); $t=substr($s,4); for($k=strlen($t)-1;$k>=0;$k--) $nCod+=(int)(substr($t,$k,1));
 if(sprintf('%02x',$nCod)==substr($s,2,2)) return substr($s,8); else return false;
}
function fUmfDeCode($w){
 $nCod=(int)substr(Umf_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>