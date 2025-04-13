<?php
function fFraSeite(){
 $aNutzFld=explode(';',FRA_NutzerFelder); $nNutzerFelder=count($aNutzFld); $aNutzPflicht=explode(';',FRA_NutzerPflicht);
 $Meld=''; $MTyp='Fehl'; $sSes=FRA_Session; $aN=array(); $aW=array(); $aFehl=array(); $bAbgelaufen=false; $bDSE1=false; $bDSE2=false; $bErrDSE1=false; $bErrDSE2=false;
 $n=(int)substr(FRA_Schluessel,-2); for($i=strlen($sSes)-1;$i>=2;$i--) $n+=(int)substr($sSes,$i,1);
 if(hexdec(substr($sSes,0,2))==$n) if(substr($sSes,9)>=(time()>>8)){
  $sId=substr($sSes,4,5); $nId=(int)$sId; $aW[0]=$nId; $aW[1]=0; $sNam='???';

  //SQL-Verbindung oeffnen
  $bSQLOpen=false;
  if(FRA_SQL){
   $DbO=@new mysqli(FRA_SqlHost,FRA_SqlUser,FRA_SqlPass,FRA_SqlDaBa);
   if(!mysqli_connect_errno()){$bSQLOpen=true; if(FRA_SqlCharSet) $DbO->set_charset(FRA_SqlCharSet);} else $FehlSQL=FRA_TxSqlVrbdg;
  }

  if($_SERVER['REQUEST_METHOD']=='POST'){
   for($i=2;$i<$nNutzerFelder;$i++) if(isset($_POST['fra_F'.$i])){ //Eingabefelder
    $s=str_replace('"',"'",strip_tags(stripslashes(trim($_POST['fra_F'.$i])))); if($n=strpos($s,"\n")) $s=rtrim(substr($s,0,$n));
    $aW[$i]=(FRA_Zeichensatz==0?$s:(FRA_Zeichensatz==2?iconv('UTF-8','ISO-8859-1//IGNORE',$s):html_entity_decode($s)));
    if($aNutzPflicht[$i]==1&&empty($aW[$i])) $aFehl[$i]=true;
   }else $aW[$i]='';
   $aW[2]=strtolower($aW[2]); if(strlen($aW[2])<4||strlen($aW[2])>25) $aFehl[2]=true; if(strlen($aW[3])<4||strlen($aW[3])>16) $aFehl[3]=true; //Nutzer/Pass
   if(!preg_match('/^([0-9a-z~_\-]+\.)*[0-9a-z~_\-]+@[0-9a-zäöü_\-]+(\.[0-9a-zäöü_\-]+)*\.[a-z]{2,16}$/',strtolower($aW[4]))) $aFehl[4]=true; //eMail
   if(FRA_NutzerDSE1) if(isset($_POST['fra_DSE1'])&&$_POST['fra_DSE1']=='1') $bDSE1=true; else{$bErrDSE1=true; $aFehl['DSE']=true;}
   if(FRA_NutzerDSE2) if(isset($_POST['fra_DSE2'])&&$_POST['fra_DSE2']=='1') $bDSE2=true; else{$bErrDSE2=true; $aFehl['DSE']=true;}
   if(count($aFehl)==0){
    if(!FRA_SQL){ //Textdateien
     $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $sBen='#;'; $k=0;
     for($i=1;$i<$nSaetze;$i++){$a=explode(';',$aD[$i],4);
      if($a[0]!=$nId) $sBen.=fFraDeCode($a[2]).';'; else{$aN=explode(';',rtrim($aD[$i])); $k=$i;} //Nutzer gefunden
     }
     if($k>0){ //gefunden
      $aW[0]=$aN[0]; $aW[1]=$aN[1]; $aN[2]=fFraDeCode($aN[2]); $aN[3]=fFraDeCode($aN[3]); $aN[4]=fFraDeCode($aN[4]);
      for($j=5;$j<$nNutzerFelder;$j++){
       $aN[$j]=(isset($aN[$j])?str_replace('`,',';',$aN[$j]):'');
       if($aNutzFld[$j]=='GUELTIG_BIS'){
        $aW[$j]=$aN[$j]; if(FRA_NutzerFrist>0&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
       }
      }
      if($aN!=$aW){ //veraendert
       if($aN[2]==$aW[2]||!strpos($sBen,';'.$aW[2].';')){ //Benutzername unveraendert oder frei
        $s=$nId.';'.$aW[1].';'.fFraEnCode($aW[2]).';'.fFraEnCode($aW[3]).';'.fFraEnCode($aW[4]);
        for($j=5;$j<$nNutzerFelder;$j++) $s.=';'.str_replace(';','`,',$aW[$j]); $aD[$k]=$s."\n";
        if($f=fopen(FRA_Pfad.FRA_Daten.FRA_Nutzer,'w')){
         fwrite($f,rtrim(str_replace("\r",'',implode('',$aD)))."\n"); fclose($f); $Meld=FRA_TxNutzerGeaendert; $MTyp='Erfo';
        }else $Meld=str_replace('#',FRA_TxBenutzer,FRA_TxDateiRechte);
       }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
      }else{$Meld=FRA_TxNutzerUnveraendert; $MTyp='Meld';} //unverändert
     }else $Meld=FRA_TxNutzerFalsch;
    }elseif($bSQLOpen){ //bei SQL
     if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$sId.'"')){
      $i=$rR->num_rows; $aN=$rR->fetch_row(); $rR->close();
      if($i==1){ //gefunden
       $aW[0]=$aN[0]; $aW[1]=$aN[1]; $aN[3]=fFraDeCode($aN[3]); $s='';
       if($aN[2]!=$aW[2]) $s.=', Benutzer="'.$aW[2].'"'; if($aN[3]!=$aW[3]) $s.=', Passwort="'.fFraEnCode($aW[3]).'"';
       if($aN[4]!=$aW[4]) $s.=', eMail="'.$aW[4].'"';
       for($j=5;$j<$nNutzerFelder;$j++){
        if(!isset($aN[$j])) $aN[$j]='';
        if($aNutzFld[$j]=='GUELTIG_BIS'){
         $aW[$j]=$aN[$j]; if(FRA_NutzerFrist>0&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
        }
        if($aN[$j]!=$aW[$j]) $s.=', dat_'.$j.'="'.$aW[$j].'"';
       }
       if(!empty($s)){ //veraendert
        if($aN[2]!=$aW[2]){ //Benutzname
         if($rR=$DbO->query('SELECT Nummer FROM '.FRA_SqlTabN.' WHERE Benutzer="'.$aW[2].'"')){
          $i=$rR->num_rows; $rR->close();
         }else $i=1;
        }else $i=0;
        if($i==0){ //Benutzername unveraendert oder frei
         if($DbO->query('UPDATE IGNORE '.FRA_SqlTabN.' SET '.substr($s,2).' WHERE Nummer='.$nId)){
          $Meld=FRA_TxNutzerGeaendert; $MTyp='Erfo';
         }else $Meld=FRA_TxSqlAendr;
        }else{$Meld=FRA_TxNutzerVergeben; $aFehl[2]=true;}
       }else{$Meld=FRA_TxNutzerUnveraendert; $MTyp='Meld';} //unverändert
      }else $Meld=FRA_TxNutzerFalsch;
     }else $Meld=FRA_TxSqlFrage;
    }//SQL
   }else $Meld=FRA_TxEingabeFehl;
  }else{ //GET
   if(!FRA_SQL){ //Textdateien
    $aD=file(FRA_Pfad.FRA_Daten.FRA_Nutzer); $nSaetze=count($aD); $s=$nId.';'; $n=strlen($s);
    for($i=1;$i<$nSaetze;$i++){
     if(substr($aD[$i],0,$n)==$s){ //Nutzer gefunden
      $aW=explode(';',rtrim($aD[$i])); $aW[2]=fFraDeCode($aW[2]); $sNam=$aW[2]; $aW[3]=fFraDeCode($aW[3]); $aW[4]=fFraDeCode($aW[4]);
      for($j=5;$j<$nNutzerFelder;$j++){
       $aW[$j]=str_replace('`,',';',$aW[$j]);
       if($aNutzFld[$j]=='GUELTIG_BIS'&&FRA_NutzerFrist>0&&isset($aW[$j])&&$aW[$j]>''&&$aW[$j]<date('Y-m-d')) $bAbgelaufen=true;
      }
      break;
    }}
   }elseif($bSQLOpen){ //bei SQL
    if($rR=$DbO->query('SELECT * FROM '.FRA_SqlTabN.' WHERE Nummer="'.$nId.'"')){
     $aW=$rR->fetch_row(); $rR->close(); if($aW[0]==$nId){$sNam=$aW[2]; $aW[3]=fFraDeCode($aW[3]);}else $aW=array();
     if(FRA_NutzerFrist>0&&($p=array_search('GUELTIG_BIS',$aNutzFld))&&isset($aW[$p])&&$aW[$p]>''&&$aW[$p]<date('Y-m-d')) $bAbgelaufen=true;
    }
   }//SQL
   $Meld=FRA_TxFuer.' &quot;'.$sNam.'&quot;'; $MTyp='Meld';
  }
 }else $Meld=FRA_TxSessionZeit; else $Meld=FRA_TxSessionUngueltig;

 $X='<p class="fraMeld" style="font-size:1.2em">'.fFraTx(FRA_TxNutzerAendern).'</p>'."\n";
 $X.="\n".'<p class="fra'.$MTyp.'">'.fFraTx($Meld).'</p>'; $nSp=3;
 if(FRA_DSEPopUp&&(FRA_NutzerDSE1||FRA_NutzerDSE2)) $X.="\n".'<script type="text/javascript">function DSEWin(sURL){dseWin=window.open(sURL,"dsewin","width='.FRA_DSEPopupW.',height='.FRA_DSEPopupH.',left='.FRA_DSEPopupX.',top='.FRA_DSEPopupY.',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");dseWin.focus();}</script>';
 if(isset($aW[1])){
  if($aW[1]=='1'&&!$bAbgelaufen){$s=''; $t='Grn';}else{$s=FRA_TxNicht.' '; $t='Rot';}
  $X.='
 <form class="fraForm" action="'.FRA_Self.'" method="post">
 <input type="hidden" name="fra_Aktion" value="benutzer" />
 <input type="hidden" name="fra_Session" value="'.$sSes.'" />'.rtrim("\n ".FRA_Hidden).'
 <table class="fraLogi" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td class="fraLogi">'.fFraTx(FRA_TxNutzerNr).'</td>
   <td class="fraLogi">'.($sId!=''?sprintf('%05d ',$sId):'').'<img src="'.FRA_Http.'punkt'.$t.'.gif" width="12" height="12" border="0" title="'.fFraTx($s.FRA_TxAktiv).'"><input type="hidden" name="fra_F1" value="'.$aW[1].'" />'.($aW[1]=='1'?($bAbgelaufen?' <span class="fraMini">('.fFraTx(FRA_TxNutzerAblauf).')</span>':''):' <span class="fraMini">('.fFraTx($s.FRA_TxAktiv).')</span>').'</td>
  </tr>
  <tr>
   <td class="fraLogi">'.fFraTx(FRA_TxBenutzername).'*<div class="fraNorm"><span class="fraMini">'.fFraTx(FRA_TxNutzerRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[2])&&$aFehl[2]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F2" value="'.fFraTx($aW[2]).'" maxlength="25" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi">'.fFraTx(FRA_TxPasswort).'*<div class="fraNorm"><span class="fraMini">'.fFraTx(FRA_TxPassRegel).'</span></div></td>
   <td class="fraLogi"><div'.(isset($aFehl[3])&&$aFehl[3]?' class="fraFehl"':'').'><input class="fraLogi" type="password" name="fra_F3" value="'.fFraTx($aW[3]).'" maxlength="16" /></div></td>
  </tr>
  <tr>
   <td class="fraLogi">'.fFraTx(FRA_TxMailAdresse).'*</td>
   <td class="fraLogi"><div'.(isset($aFehl[4])&&$aFehl[4]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F4" value="'.fFraTx($aW[4]).'" maxlength="100" /></div></td>
  </tr>';
 for($i=5;$i<$nNutzerFelder;$i++){
  if($aNutzFld[$i]!='GUELTIG_BIS') $bNutzerFrist=false; else{$bNutzerFrist=true; if(FRA_TxNutzerFrist) $aNutzFld[$i]=FRA_TxNutzerFrist;}
  $X.='
  <tr>
   <td class="fraLogi">'.fFraTx($aNutzFld[$i]).($aNutzPflicht[$i]?'*':'').'</td>
   <td class="fraLogi"><div'.(isset($aFehl[$i])&&$aFehl[$i]?' class="fraFehl"':'').'><input class="fraLogi" type="text" name="fra_F'.$i.'" value="'.fFraTx($aW[$i]).($bNutzerFrist?'" style="width:8em;" readonly="readonly':'').'" maxlength="255" /></div></td>
  </tr>';
 }
 if(FRA_NutzerDSE1) $X.="\n".'<tr><td class="fraLogi" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE1?'Fehl':'Norm').'">'.fFraDSEFld(1,$bDSE1).'</div></td></tr>';
 if(FRA_NutzerDSE2) $X.="\n".'<tr><td class="fraLogi" style="text-align:right">*</td><td class="fraLogi"><div class="fra'.($bErrDSE2?'Fehl':'Norm').'">'.fFraDSEFld(2,$bDSE2).'</div></td></tr>';
 $X.='
  <tr><td class="fraLogi">&nbsp;</td><td class="fraLogi" style="text-align:right;">* <span class="fraMini">'.fFraTx(FRA_TxPflicht).'</span></td></tr>
 </table>
 <input type="submit" class="fraScha" style="background-image:url('.FRA_Http.'schalter.gif)" value="'.fFraTx(FRA_TxSenden).'" title="'.fFraTx(FRA_TxSenden).'" />
 </form>
 ';
 }//isset($aW[1]
 $X.='<p>[ <a class="fraMenu" href="'.FRA_Self.(strpos(FRA_Self,'?')?'&amp;':'?').'fra_Aktion=zentrum&amp;fra_Session='.$sSes.'">'.fFraTx(FRA_TxBenutzerzentrum).'</a> ]</p>';
 return $X;
}

function fFraDSEFld($z,$bCheck=false){
 $s='<a class="fraText" href="'.FRA_DSELink.'"'.(FRA_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(FRA_DSETarget?' target="'.FRA_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',fFraTx($z!=2?FRA_TxDSE1:FRA_TxDSE2)));
 return '<input class="fraCheck" type="checkbox" name="fra_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').' /> '.$s;
}

function fFraEnCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s='';
 for($k=strlen($w)-1;$k>=0;$k--){$n=ord(substr($w,$k,1))-($nCod+$k); if($n<0) $n+=256; $s.=sprintf('%02X',$n);}
 return $s;
}
function fFraDeCode($w){
 $nCod=(int)substr(FRA_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}
?>