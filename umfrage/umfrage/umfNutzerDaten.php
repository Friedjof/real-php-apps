<?php
function fUmfSeite(){
 $Meld=''; $MTyp='Fehl'; $X=''; $DbO=NULL; $bSes=false; $aN=array(); $aW=array(); $aFehl=array(); $bAbgelaufen=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 $aNutzFld=explode(';',UMF_NutzerFelder); $nNutzerFelder=count($aNutzFld); $aNutzPflicht=explode(';',UMF_NutzerPflicht);

 if($sSes=(UMF_Session)){
  $n=(int)substr(UMF_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
  if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
   $sNId=substr($sSes,4,5); $nNId=(int)$sNId; $bSes=true;
  }else $Meld=UMF_TxSessionZeit; else $Meld=UMF_TxSessionUngueltig;
 }elseif(UMF_Nutzerverwaltung!='') $Meld=UMF_TxNutzerLogin;

 if($bSes){
  $aW[0]=$nNId; $aW[1]=0; $sNam='???';

  if(UMF_SQL){ //SQL-Verbindung oeffnen
   $DbO=@new mysqli(UMF_SqlHost,UMF_SqlUser,UMF_SqlPass,UMF_SqlDaBa);
   if(!mysqli_connect_errno()){if(UMF_SqlCharSet) $DbO->set_charset(UMF_SqlCharSet);}else{$Msg=UMF_TxSqlVrbdg; $DbO=NULL;}
  }

  if($_SERVER['REQUEST_METHOD']=='POST'){
   for($i=2;$i<$nNutzerFelder;$i++) if(isset($_POST['umf_F'.$i])){ //Eingabefelder
    $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['umf_F'.$i])))); if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n));
    $aW[$i]=(UMF_Zeichensatz==0?$s:(UMF_Zeichensatz==2?iconv('UTF-8','ISO-8859-1',$s):html_entity_decode($s)));
    if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
   }else $aW[$i]='';
   $aW[2]=strtolower($aW[2]); if(strlen($aW[2])<4||strlen($aW[2])>25) $aFehl[2]=true; if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Nutzer/Pass
   if(!preg_match('/^([0-9a-z~_\-]+\.)*[0-9a-z~_\-]+@[0-9a-zäöü_\-]+(\.[0-9a-zäöü_\-]+)*\.[a-z]{2,16}$/',strtolower($aW[4]))) $aFehl[4]=true; //eMail
   if(UMF_NutzerDSE1) if(isset($_POST['umf_DSE1'])&&$_POST['umf_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
   if(UMF_NutzerDSE2) if(isset($_POST['umf_DSE2'])&&$_POST['umf_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
   if(count($aFehl)==0){
    if(!UMF_SQL){ //Textdateien
     $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $sBen='#;'; $k=0;
     for($i=1;$i<$nSaetze;$i++){$a=explode(';',$aD[$i],4);
      if($a[0]!=$nNId) $sBen.=fUmfDeCode($a[2]).';'; else{$aN=explode(';',rtrim($aD[$i])); $k=$i;} //Nutzer gefunden
     }
     if($k>0){ //gefunden
      $aW[0]=$aN[0]; $aW[1]=$aN[1]; $aN[2]=fUmfDeCode($aN[2]); $aN[3]=fUmfDeCode($aN[3]); $aN[4]=fUmfDeCode($aN[4]);
      for($j=5;$j<$nNutzerFelder;$j++){
       $aN[$j]=(isset($aN[$j])?str_replace('`,',';',$aN[$j]):'');
       if($aNutzFld[$j]=='GUELTIG_BIS'){
        $aW[$j]=$aN[$j]; if(UMF_NutzerFrist>0&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
       }
      }
      if($aN!=$aW){ //veraendert
       if($aN[2]==$aW[2]||!strpos($sBen,';'.$aW[2].';')){ //Benutzername unveraendert oder frei
        $s=$nNId.';'.$aW[1].';'.fUmfEnCode($aW[2]).';'.fUmfEnCode($aW[3]).';'.fUmfEnCode($aW[4]);
        for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
        if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Nutzer,'w')){
         fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Meld=UMF_TxNutzerGeaendert; $MTyp='Erfo';
        }else $Meld=str_replace('#',UMF_TxBenutzer,UMF_TxDateiRechte);
       }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
      }else{$Meld=UMF_TxNutzerUnveraendert; $MTyp='Meld';} //unverändert
     }else $Meld=UMF_TxNutzerFalsch;
    }elseif($DbO){ //bei SQL
     if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.$nNId.'"')){
      $i=$rR->num_rows; $aN=$rR->fetch_row(); $rR->close();
      if($i==1){ //gefunden
       $aW[0]=$aN[0]; $aW[1]=$aN[1]; $aN[3]=fUmfDeCode($aN[3]); $s='';
       if($aN[2]!=$aW[2]) $s.=', Benutzer="'.$aW[2].'"'; if($aN[3]!=$aW[3]) $s.=', Passwort="'.fUmfEnCode($aW[3]).'"';
       if($aN[4]!=$aW[4]) $s.=', eMail="'.$aW[4].'"';
       for($j=5;$j<$nNutzerFelder;$j++){
        if(!isset($aN[$j])) $aN[$j]='';
        if($aNutzFld[$j]=='GUELTIG_BIS'){
         $aW[$j]=$aN[$j]; if(UMF_NutzerFrist>0&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
        }
        if($aN[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.$aW[$j].'"';
       }
       if(!empty($s)){ //veraendert
        if($aN[2]!=$aW[2]){ //Benutzname
         if($rR=$DbO->query('SELECT Nummer FROM '.UMF_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
          $i=$rR->num_rows; $rR->close();
         }else $i=1;
        }else $i=0;
        if($i==0){ //Benutzername unveraendert oder frei
         if($DbO->query('UPDATE IGNORE '.UMF_SqlTabN.' SET '.substr($s,2).' WHERE Nummer='.$nNId)){
          $Meld=UMF_TxNutzerGeaendert; $MTyp='Erfo';
         }else $Meld=UMF_TxSqlAendr;
        }else{$Meld=UMF_TxNutzerVergeben; $aFehl[2]=true;}
       }else{$Meld=UMF_TxNutzerUnveraendert; $MTyp='Meld';} //unverändert
      }else $Meld=UMF_TxNutzerFalsch;
     }else $Meld=UMF_TxSqlFrage;
    }//SQL
   }else $Meld=UMF_TxEingabeFehl;
  }else{ //GET
   if(!UMF_SQL){ //Textdateien
    $aD=file(UMF_Pfad.UMF_Daten.UMF_Nutzer); $nSaetze=count($aD); $s=$nNId.';'; $n=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     if(substr($aD[$i],0,$n)==$s){ //Nutzer gefunden
      $aW=explode(';',rtrim($aD[$i])); $aW[2]=fUmfDeCode($aW[2]); $sNam=$aW[2]; $aW[3]=fUmfDeCode($aW[3]); $aW[4]=fUmfDeCode($aW[4]);
      for($j=5;$j<$nNutzerFelder;$j++){
       $aW[$j]=str_replace('`,',';',$aW[$j]);
       if($aNutzFld[$j]=='GUELTIG_BIS'&&UMF_NutzerFrist>0&&isset($aW[$j])&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
      }
      break;
    }}
   }elseif($DbO){ //bei SQL
    if($rR=$DbO->query('SELECT * FROM '.UMF_SqlTabN.' WHERE Nummer="'.$nNId.'"')){
     $aW=$rR->fetch_row(); $rR->close(); if($aW[0]==$nNId){$sNam=$aW[2]; $aW[3]=fUmfDeCode($aW[3]);}else $aW=array();
     if(UMF_NutzerFrist>0&&($p=array_search('GUELTIG_BIS',$aNutzFld))&&isset($aW[$p])&&$aW[$p]>''&&$aW[$p]<date('Y-m-d')) $bAbgelaufen=true;
   }}
   if(empty($sMeld)){$Meld=UMF_TxFuer.' &quot;'.$sNam.'&quot;'; $MTyp='Meld';}
  } // GET
 } // Sessionsproblem

 $X='<p class="umfMeld" style="font-size:1.2em">'.fUmfTx(UMF_TxNutzerAendern).'</p>'."\n";
 $X.="\n".'<p class="umf'.$MTyp.'">'.fUmfTx($Meld).'</p>'; $nSp=3;
 if(UMF_DSEPopUp&&(UMF_NutzerDSE1||UMF_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.UMF_DSEPopupW.',height='.UMF_DSEPopupH.',left='.UMF_DSEPopupX.',top='.UMF_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 if(isset($aW[1])){
  if($aW[1]=='1'&&!$bAbgelaufen){$s=''; $t='Grn';}else{$s=UMF_TxNicht.' '; $t='Rot';}
  $X.='
 <form class="umfForm" action="'.UMF_Self.'" method="post">
 <input type="hidden" name="umf_Aktion" value="benutzer" />
 <input type="hidden" name="umf_Session" value="'.$sSes.'" />'.rtrim("\n ".UMF_Hidden).'
 <table class="umfLogi" border="0" cellpadding="0" cellspacing="0">
  <tr class="umfTr">
   <td class="umfLogi">'.fUmfTx(UMF_TxNutzerNr).'</td>
   <td class="umfLogi">'.($nNId!=''?sprintf('%05d ',$nNId):'').'<img src="'.UMF_Url.'punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fUmfTx($s.UMF_TxAktiv).'"><input type="hidden" name="umf_F1" value="'.$aW[1].'" />'.($aW[1]=='1'?($bAbgelaufen?' <span class="umfMini">('.fUmfTx(UMF_TxNutzerAblauf).')</span>':''):' <span class="umfMini">('.fUmfTx($s.UMF_TxAktiv).')</span>').'</td>
  </tr>
  <tr class="umfTr">
   <td class="umfLogi">'.fUmfTx(UMF_TxBenutzername).'*<div class="umfNorm"><span class="umfMini">'.fUmfTx(UMF_TxNutzerRegel).'</span></div></td>
   <td class="umfLogi"><div'.(isset($aFehl[2])&&$aFehl[2]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F2" value="'.fUmfTx($aW[2]).'" maxlength="25" /></div></td>
  </tr>
  <tr class="umfTr">
   <td class="umfLogi">'.fUmfTx(UMF_TxPasswort).'*<div class="umfNorm"><span class="umfMini">'.fUmfTx(UMF_TxPassRegel).'</span></div></td>
   <td class="umfLogi"><div'.(isset($aFehl[3])&&$aFehl[3]?' class="umfFehl"':'').'><input class="umfLogi" type="password" name="umf_F3" value="'.fUmfTx($aW[3]).'" maxlength="16" /></div></td>
  </tr>
  <tr class="umfTr">
   <td class="umfLogi">'.fUmfTx(UMF_TxMailAdresse).'*</td>
   <td class="umfLogi"><div'.(isset($aFehl[4])&&$aFehl[4]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F4" value="'.fUmfTx($aW[4]).'" maxlength="100" /></div></td>
  </tr>';
 for($i=5;$i<$nNutzerFelder;$i++){
  if($aNutzFld[$i]!='GUELTIG_BIS') $bNutzerFrist=false; else{$bNutzerFrist=true; if(UMF_TxNutzerFrist) $aNutzFld[$i]=UMF_TxNutzerFrist;}
  $X.='
  <tr class="umfTr">
   <td class="umfLogi">'.fUmfTx($aNutzFld[$i]).($aNutzPflicht[$i]?'*':'').'</td>
   <td class="umfLogi"><div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="umfFehl"':'').'><input class="umfLogi" type="text" name="umf_F'.$i.'" value="'.fUmfTx($aW[$i]).($bNutzerFrist?'" style="width:8em;" readonly="readonly':'').'" maxlength="255" /></div></td>
  </tr>';
 }
 if(UMF_NutzerDSE1) $X.="\n".'<tr><td class="umfLogi" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE1?'Fehl':'Norm').'">'.fUmfDSEFld(1,$bDSE1).'</div></td></tr>';
 if(UMF_NutzerDSE2) $X.="\n".'<tr><td class="umfLogi" style="text-align:right">*</td><td class="umfLogi"><div class="umf'.($bErrDSE2?'Fehl':'Norm').'">'.fUmfDSEFld(2,$bDSE2).'</div></td></tr>';
 $X.='
  <tr class="umfTr"><td class="umfLogi">&nbsp;</td><td class="umfLogi" style="text-align:right;">* <span class="umfMini">'.fUmfTx(UMF_TxPflicht).'</span></td></tr>
 </table>
 <input type="submit" class="umfScha" value="'.fUmfTx(UMF_TxSenden).'" title="'.fUmfTx(UMF_TxSenden).'" />
 </form>
 ';
 }//isset($aW[1]
 $X.='<p>[ <a class="umfMenu" href="'.UMF_Self.(strpos(UMF_Self,'?')?'&amp;':'?').'umf_Aktion=zentrum&amp;umf_Session='.$sSes.'">'.fUmfTx(UMF_TxBenutzerzentrum).'</a> ]</p>';
 return $X;
}

function fUmfDSEFld($z,$bCheck=false){
 $s='<a class="umfText" href="'.UMF_DSELink.'"'.(UMF_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(UMF_DSETarget?' target="'.UMF_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fUmfTx($z!=2?UMF_TxDSE1:UMF_TxDSE2)));
 return '<input class="umfCheck" type="checkbox" name="umf_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}
function fUmfEnCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fUmfDeCode($w){
 $nCod=(int)substr(UMF_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>
