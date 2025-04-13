<?php
function fFraSeite(){ //Selbst-Freischaltung
 $X=FRA_TxAktivFehl; $MTyp='Fehl'; $bOK=false; $sAkt=''; $sEml='';

 //SQL-Verbindung öffnen
 $bSQLOpen=false;
 if(FRA_SQL){
  $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
  if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
 }

 $sAkt=(isset($_GET['fra_Aktion'])?$_GET['fra_Aktion']:'').(isset($_POST['fra_Aktion'])?$_POST['fra_Aktion']:'');
 if($sId=fFraValidId($sAkt)){
  if($_SERVER['REQUEST_METHOD']!='POST'){ //GET pruefen
   if(!FRA_SQL){ //Textdateien
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr($aD[$i],0,$p+1)==$s.'0'){$X=FRA_TxAktivieren; $a=explode(';',$aD[$i]); $sEml=fFraDeCode($a[4]);}
     elseif(substr($aD[$i],0,$p+1)==$s.'1'){$X=FRA_TxAktiviert; $sAkt='login';}
     $MTyp='Meld'; $bOK=true; break;
    }
   }elseif($bSQLOpen){ //SQL
    if($rR=$DbO->query('SELECT Nummer,aktiv,eMail FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sId.'"')){
     if($a=$rR->fetch_row()) if($a[0]==$sId&&$a[1]=='0'){$X=FRA_TxAktivieren; $sEml=$a[2];}
     elseif($a[0]==$sId&&$a[1]=='1'){$X=FRA_TxAktiviert; $sAkt='login';}
     $rR->close(); $MTyp='Meld'; $bOK=true;
    }else $X=FRA_TxSqlFrage;
   }else $X=$Msg; //SQL
  }else{ //POST freischalten
   if(!FRA_SQL){ //Textdateien
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=$sId.';'; $p=strlen($s);
    for($i=1;$i<$nSaetze;$i++) if(substr($aD[$i],0,$p)==$s){ //gefunden
     if(substr($aD[$i],0,$p+1)==$s.'0'){
      $aD[$i]=$sId.';1'.substr(rtrim($aD[$i]),$p+1)."\n"; $bOK=true;
      if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
       fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $X=FRA_TxAktiviert; $MTyp='Erfo'; $sAkt='login';
      }else{$X=str_replace('#',fFraTx(FRA_TxBenutzer),FRA_TxDateiRechte);}
     }
     break;
    }
   }elseif($bSQLOpen){ //SQL
    if($rR=$DbO->query('SELECT Nummer,aktiv FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sId.'"')){
     $i=$rR->num_rows; $a=$rR->fetch_row(); $rR->close();
     if($a[0]==$sId&&$a[1]=='0'){$bOK=true;
      if($DbO->query('UPDATE IGNORE '.FRA_SqlTabN.' SET aktiv="1" WHERE Nummer="'.$sId.'"')){$X=FRA_TxAktiviert; $MTyp='Erfo'; $sAkt='login';}
      else $X=FRA_TxSqlAendr;
     }
    }else $X=FRA_TxSqlFrage;
   }//SQL
  }//POST
 }
 //Formular- und Tabellenanfang
 $X=' <p class="fra'.$MTyp.'">'.fFraTx($X).'</p>
 <form name="fraForm" class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="'.$sAkt.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" style="margin:16px;" cellpadding="0" cellspacing="0">
  <tr><td class="fraLogi" style="padding:8px;text-align:center;">'.fFraTx($sAkt!='login'?$sEml.'\n '.FRA_TxPassiv:FRA_TxLoginLogin.'\n '.FRA_TxOder.'\n '.FRA_TxLoginVergessen.'?').'</td></tr>
 </table>';
 if($bOK) $X.="\n".' <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxWeiter).'" title="'.fFraTx(FRA_TxWeiter).'" />'; else $X.='&nbsp;';
 $X.="\n  </form>\n";
 return $X;
}

function fFraValidId($s){
 $nCod=(int)substr(FRA_Schluessel,-2); $t=substr($s,4); for($k=strlen($t)-1;$k>=0;$k--) $nCod+=(int)substr($t,$k,1);
 if(sprintf('%02x',$nCod)==substr($s,2,2)) return substr($s,8); else return false;
}
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>