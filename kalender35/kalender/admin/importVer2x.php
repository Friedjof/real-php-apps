<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Terminimport','','Im2');

$bAltOK=false; $sAltAdr='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $bImp=false; $MsC=''; $bAltCC=false; $bFehlB=false; $bFehlD=false;
 if($sAltAdr=stripslashes(trim($_POST['AltAdr']))){
  if(substr($sAltAdr,-1,1)!='/'&&substr($sAltAdr,-1,1)!="\\") $sAltAdr.='/';
  if(!$p=strpos($sAltAdr,'://')){ //local
   if($aD=@file($sAltAdr.'termine.txt')){ //lokale Datei gelesen
    if(!$aE=@file($sAltAdr.'eingabe.txt'))
     $MsC.='<p class="admFehl">Aus <i>'.$sAltAdr.'eingabe.txt</i> können derzeit jedoch keine Eingabehilfen bzw. vordefinierten Auswahlwerte gelesen werden.</p>';
    if(strpos(rtrim($aD[1]).';',';p;')>0){ //Passwortfeld enthalten
     if(($s=strstr(@join('',@file($sAltAdr.'werte.php')),'$CryptCode'))&&($p=strpos($s,'='))){
      $nAltCC=(int)substr($s,$p+1,2); $bAltCC=true; //alten CryptCode holen
     }else $MsC.='<p class="admFehl">Aus <i>'.$sAltAdr.'werte.php</i> kann derzeit jedoch kein Entschlüsselungscode für Passworte gelesen werden.</p>';
    }
   }else $Msg='<p class="admFehl">Unter <i>'.$sAltAdr.'termine.txt</i> kann keine Datei gelesen werden!</p>';
  }else{ //remote
   $s=substr($sAltAdr,$p+3); $errNo=0; $errStr='';
   if($p=strpos($s,'/')){$sAltH=substr($s,0,$p); $sAltP=substr($s,$p);}else{$sAltH=$s; $sAltP='/';}
   if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
    fputs($Sck,'GET '.$sAltP."termine.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
    while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
    if($aD=explode("\n",str_replace("\r",'',trim(strstr($s,"\r\n\r\n"))))){
     if($Sck=@fsockopen($sAltH,80,$errNo,$errStr,20)){
      fputs($Sck,'GET '.$sAltP."eingabe.txt HTTP/1.0\r\nHost: ".$sAltH."\r\nAccept: */*\r\n\r\n"); $s='';
      while(!feof($Sck)) $s.=fgets($Sck,128); fclose($Sck);
      if(!$aE=explode("\n",str_replace("\r",'',trim(strstr($s,"\r\n\r\n")))))
       $MsC.='<p class="admFehl">Aus <i>http://'.$sAltH.$sAltP.'eingabe.txt</i> können derzeit jedoch keine Eingabehilfen bzw. vordefinierten Auswahlwerte gelesen werden.</p>';
     }else $MsC.='<p class="admFehl">Unter <i>http://'.$sAltH.'</i> kann derzeit keine Datei <i>eingabe.txt</i> geöffnet werden!</p>';
    }else $Msg='<p class="admFehl">Unter <i>http://'.$sAltH.$sAltP.'termine.txt</i> kann derzeit keine Datei gelesen werden!</p>';
   }else $Msg='<p class="admFehl">Unter <i>http://'.$sAltH.'</i> kann derzeit keine Datei geöffnet werden!</p><p>'.$errNo.' '.$errStr.'</p>';
  }
  if(is_array($aD)&&count($aD)>0){ //termine.txt eingelesen
   $H1=rtrim(array_shift($aD)); $H2=rtrim(array_shift($aD)); $H3=rtrim(array_shift($aD)); $nSaetze=count($aD);
   if(substr($H1,0,6)=='Datum;'||substr($H1,0,1)=='D;'){ //Termindatei ist gültig
    $bAltOK=true; $aF=explode(';',$H1); $aT=explode(';',$H2); $aS=explode(';',$H3); $nFelder=count($aF);
    if(!isset($_POST['NeuImp'])||$_POST['NeuImp']!='1'){ //erst nachfragen
     $bImp=true; $Msg='<p class="admMeld">Sollen die Termine von <i>'.$sAltAdr.'termine.txt</i> <span style="color:#BB0033;">jetzt</span> importiert werden?</p>'.$MsC;
     $t=''; $s=''; $k=0; $nZl=min(8,$nSaetze); $Msg.=NL.'<p>insgesamt '.$nSaetze.' Termine</p>';
     $Msg.=NL.'<table class="admTabl" border="0" cellpadding="3" cellspacing="1">'.NL.' <tr class="admTabl">';
     for($i=0;$i<$nFelder;$i++){ //Kopfzeile, max. 8 Spalten
      $sT=strtolower($aT[$i]);
      if($sT=='t'||$sT=='d'||$sT=='a'||$sT=='z'||$sT=='b'||$sT=='w'||$sT=='n'||$sT=='j'){
       if(++$k<9) $Msg.='<td><b>'.html_entity_decode(str_replace(chr(127),';',$aF[$i])).'</b></td>';
      }
     }
     $Msg.='</tr>';
     for($j=0;$j<$nZl;$j++){ //erste 8 Termine
      $a=explode(';',rtrim($aD[$j])); $Msg.=NL.' <tr class="admTabl">'; $k=0;
      for($i=0;$i<$nFelder;$i++){ //über alle Felder, max. 8 Spalten
       $sT=strtolower($aT[$i]); $s=html_entity_decode(str_replace("\r",'',str_replace(chr(127),';',trim($a[$i]))));
       switch($sT){
        case 't': case 'a': case 'z': case 'w': case 'b': case 'n': if(!$s) $s='&nbsp;'; if(++$k<9) $Msg.='<td>'.$s.'</td>'; break;
        case 'd': if($s) $s=fKalAnzeigeDatum('20'.substr($s,0,8)); else $s='&nbsp;'; if(++$k<9) $Msg.='<td>'.$s.'</td>'; break;
        case 'j': case '#': if($s!='J') $s='N'; if(++$k<9) $Msg.='<td>'.$s.'</td>'; break;
       }
      }//erste Spalten
      $Msg.='</tr>';
     }//erste Termine
     if($nSaetze>$nZl) $Msg.=NL.' <tr class="admTabl"><td colspan="'.$k.'">usw.......</td></tr>'; //noch mehr Termine
     $Msg.=NL.'</table>'.NL;
    }else{ //jetzt importieren
     if(is_writable(KAL_Pfad.'/kalWerte.php')){
      $kal_FeldName=array('Nummer',$aF[0]); $kal_FeldType=array('i','d'); $kal_ListenFeld=array(0,1); $kal_NListenFeld=array(0,1);
      $kal_SortierFeld=array(0,1); $kal_LinkFeld=array(0,1); $kal_DetailFeld=array(1,1); $kal_NDetailFeld=array(1,1);
      $kal_SuchFeld=array(0,2); $kal_EingabeFeld=array(0,1); $kal_NEingabeFeld=array(0,1); $kal_EingabeLang=array(0,0); $kal_KopierFeld=array(0,1); $kal_NKopierFeld=array(0,1);
      $kal_PflichtFeld=array(0,1);
      $kal_SpaltenStil=array('',''); $kal_ZeilenStil=array('','');
      $kal_AktuelleFeld=array(0,1); $kal_AktuelleLink=array(0,1); $kal_AktuelleStil=array('','');
      $kal_LaufendeFeld=array(0,1); $kal_LaufendeLink=array(0,1); $kal_LaufendeStil=array('','');
      $kal_NeueFeld=array(0,1); $kal_NeueLink=array(0,1); $kal_NeueStil=array('','');
      $j=1; $aV=array(''); $a=explode(';',$aE[0]); $aV[1]=trim($a[0]); //Vorgaben
      for($i=1;$i<$nFelder;$i++){
       $t=strtolower($aT[$i]); $kal_FeldName[]=$aF[$i]; $kal_FeldType[]=($t!='o'?$t:'f'); $kal_ListenFeld[]=($aT[$i]<'a'?++$j:0); $kal_NListenFeld[]=($aT[$i]<'a'?$j:0);
       $kal_AktuelleFeld[]=0; $kal_LaufendeFeld[]=0; $kal_NeueFeld[]=0; $kal_SortierFeld[]=0; $kal_LinkFeld[]=0;
       $kal_AktuelleLink[]=0; $kal_LaufendeLink[]=0; $kal_NeueLink[]=0; $kal_DetailFeld[]=($t!='p'?1:0); $kal_NDetailFeld[]=($t!='p'?1:0); $kal_SuchFeld[]=($t!='p'&&$t!='z'&&$t!='b'&&$t!='o'?2:0);
       $kal_PflichtFeld[]=($aS[$i]>''?1:0); $kal_EingabeFeld[]=1; $kal_NEingabeFeld[]=1; $kal_EingabeLang[]=0; $kal_KopierFeld[]=1; $kal_NKopierFeld[]=1; $kal_SpaltenStil[]=''; $kal_ZeilenStil[]='';
       $kal_AktuelleStil[]=''; $kal_LaufendeStil[]=''; $kal_NeueStil[]='';
       if($t!='a') $s=''; else if($s=html_entity_decode(str_replace(chr(127),';',trim($aE[$i])))) $s=';'.$s;
       $aV[$i+1]=html_entity_decode(str_replace(chr(127),';',trim($a[$i]))).$s;
      }
      $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php'))));
      fSetzIArray($kal_FeldName,'FeldName','"'); fSetzIArray($kal_FeldType,'FeldType',"'");
      fSetzIArray($kal_ListenFeld,'ListenFeld',''); fSetzIArray($kal_NListenFeld,'NListenFeld',''); fSetzIArray($kal_AktuelleFeld,'AktuelleFeld',''); fSetzIArray($kal_LaufendeFeld,'LaufendeFeld',''); fSetzIArray($kal_NeueFeld,'NeueFeld','');
      fSetzIArray($kal_SortierFeld,'SortierFeld',''); fSetzIArray($kal_LinkFeld,'LinkFeld',''); fSetzIArray($kal_AktuelleLink,'AktuelleLink',''); fSetzIArray($kal_LaufendeLink,'LaufendeLink',''); fSetzIArray($kal_NeueLink,'NeueLink','');
      fSetzIArray($kal_DetailFeld,'DetailFeld',''); fSetzIArray($kal_NDetailFeld,'NDetailFeld','');
      fSetzIArray($kal_SuchFeld,'SuchFeld',''); fSetzIArray($kal_PflichtFeld,'PflichtFeld','');
      fSetzIArray($kal_EingabeFeld,'EingabeFeld',''); fSetzIArray($kal_NEingabeFeld,'NEingabeFeld',''); fSetzIArray($kal_EingabeLang,'EingabeLang','');
      fSetzIArray($kal_KopierFeld,'KopierFeld',''); fSetzIArray($kal_NKopierFeld,'NKopierFeld','');
      fSetzIArray($kal_SpaltenStil,'SpaltenStil',"'"); fSetzIArray($kal_ZeilenStil,'ZeilenStil',"'"); fSetzIArray($kal_AktuelleStil,'AktuelleStil',"'"); fSetzIArray($kal_LaufendeStil,'LaufendeStil',"'"); fSetzIArray($kal_NeueStil,'NeueStil',"'");
      $t=''; $s=''; $k=0;
      if(!KAL_SQL){ //Textdatei
       if(is_writable(KAL_Pfad.KAL_Daten.KAL_Termine)){
         if(is_writable(KAL_Pfad.KAL_Daten.KAL_Vorgaben)){
          if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){ //alte Bilder löschen
           $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') $a[]=$s; closedir($f);
           foreach($a as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
          }
          if($f=fopen(KAL_Pfad.'kalWerte.php','w')){ //Konfiguration schreiben
           fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
          }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
          for($j=0;$j<$nSaetze;$j++){ //über alle Termine
           $a=explode(';',rtrim($aD[$j])); $sZ=sprintf('%d',++$k).';1';
           for($i=0;$i<$nFelder;$i++){ //über alle Felder
            $sT=strtolower($aT[$i]); $s=html_entity_decode(str_replace("\r",'',str_replace(chr(127),';',trim($a[$i]))));
            switch($sT){
             case 't': case 'a': case 'z': case 'l': break;
             case 'm': $s=str_replace('|',"\n",$s); break;
             case 'd': if($s){$s='20'.substr($s,0,8); $s.=rtrim(@date(' w',@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))));} break;
             case 'j': case '#': if($s!='J') $s='N'; break;
             case 'w': if(!$s=str_replace(',','.',$s)) $s='0.00'; break;
             case 'n': if(!$s=str_replace(',','.',$s)) $s='0'; break;
             case 'b':
              if($s){
               $UpEx=strtolower(strrchr($s,'.'));
               if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($sAltAdr.'bilder/gross/'.$s);
               elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($sAltAdr.'bilder/gross/'.$s);
               elseif($UpEx=='.png')$Src=ImageCreateFromPNG($sAltAdr.'bilder/gross/'.$s);
               if(!empty($Src)){
                $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($s,0,-1*strlen($UpEx));
                if($Sx>KAL_VorschauBreit||$Sy>KAL_VorschauBreit){ //Vorschau verkleinern
                 $Dw=min(KAL_VorschauBreit,$Sx);
                 if($Sx>KAL_VorschauBreit) $Dh=round(KAL_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
                 if($Dh>KAL_VorschauHoch){$Dw=round(KAL_VorschauHoch/$Dh*$Dw); $Dh=KAL_VorschauHoch;}
                 $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
                 ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
                 if(@imagejpeg($Dest,KAL_Pfad.KAL_Bilder.$k.'-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|'; else{$bFehlB=true; $v='|';}
                 imagedestroy($Dest); unset($Dest);
                }else{
                 if(@copy($sAltAdr.'bilder/gross/'.$s,KAL_Pfad.KAL_Bilder.$k.'-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|'; else{$bFehlB=true; $v='|';}
                }
                if($Sx>KAL_BildBreit||$Sy>KAL_BildBreit){ //Bild verkleinern
                 $Dw=min(KAL_BildBreit,$Sx);
                 if($Sx>KAL_BildBreit) $Dh=round(KAL_BildBreit/$Sx*$Sy); else $Dh=$Sy;
                 if($Dh>KAL_BildHoch){$Dw=round(KAL_BildHoch/$Dh*$Dw); $Dh=KAL_BildHoch;}
                 $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
                 ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
                 @imagejpeg($Dest,KAL_Pfad.KAL_Bilder.$k.'_'.$UpBa.'.jpg');
                 $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
                }else{$v.=$UpBa.$UpEx; @copy($sAltAdr.'bilder/gross/'.$s,KAL_Pfad.KAL_Bilder.$k.'_'.$UpBa.$UpEx);}
                imagedestroy($Src); unset($Src); $s=$v;
               }else{$s=''; $bFehlB=true;}
              }break;
             case 'o': if($s) if(!copy($sAltAdr.'objekte/'.$s,KAL_Pfad.KAL_Bilder.$k.'~'.$s)){$s=''; $bFehlD=true;} break;
             case 'p': if($s) if($bAltCC) $s=fKalEnCode(DeCryptOldPw($s,$nAltCC)); else $s=''; break;
            }//switch $sT
            $sZ.=';'.str_replace(NL,'\n ',str_replace(';','`,',$s));
           }//alle Felder
           $t.=NL.$sZ;
          }//alle Sätze
          $sZ='Nummer_'.$k.';online'; for($i=0;$i<$nFelder;$i++) $sZ.=';'.$aF[$i]; $sZ.=';Periodik'; //Kopfzeile
          if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Termine,'w')){fwrite($f,$sZ.$t.NL); fclose($f);
           $Msg='<p class="admErfo">Die Termindatei <i>'.$sAltAdr.'termine.txt</i> wurde erfolgreich importiert!</p>';
          }else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden!</p>';
          if($bFehlB) $Msg.='<p class="admFehl">Leider konnten nicht alle Bilder importiert werden!</p>';
          if($bFehlD) $Msg.='<p class="admFehl">Leider konnten nicht alle Dateianhänge importiert werden!</p>';
          if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){for($i=0;$i<$nFelder;$i++) fwrite($f,rtrim($aV[$i]).NL); fclose($f);}
          else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
         }else $Msg='<p class="admFehl">In die Hilfsdatei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden.</p>';
       }else $Msg='<p class="admFehl">In die Termindatei <i>'.KAL_Daten.KAL_Termine.'</i> konnte nicht geschrieben werden.</p>';
      }elseif($DbO){ //SQL
       if(is_writable(KAL_Pfad.KAL_Daten.KAL_Vorgaben)){
        if($f=opendir(KAL_Pfad.substr(KAL_Bilder,0,-1))){ //alte Bilder löschen
         $a=array(); while($s=readdir($f)) if(substr($s,0,1)!='.'&&$s!='index.html') $a[]=$s; closedir($f);
         foreach($a as $s) @unlink(KAL_Pfad.KAL_Bilder.$s);
        }
        if($f=fopen(KAL_Pfad.'kalWerte.php','w')){ //Konfiguration schreiben
         fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
        }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
        $DbO->query('DROP TABLE IF EXISTS '.KAL_SqlTabT); $bSqlOK=true; include('feldtypenInc.php'); //SQL-Tabelle anlegen
        $sF=', online char(1) NOT NULL DEFAULT ""'; for($i=0;$i<$nFelder;$i++) $sF.=', kal_'.($i+1).' '.$aSql[$kal_FeldType[$i+1]];
        if($DbO->query('CREATE TABLE '.KAL_SqlTabT.' (id int(11) NOT NULL auto_increment'.$sF.',periodik varchar(20) NOT NULL DEFAULT "", PRIMARY KEY (id)) COMMENT="Kalender-Termine"')){
         for($j=0;$j<$nSaetze;$j++){ //über alle Termine
          $a=explode(';',rtrim($aD[$j])); $sZ=sprintf('%d',++$k).',"1"';
          for($i=0;$i<$nFelder;$i++){ //über alle Felder
           $sT=strtolower($aT[$i]); $s=html_entity_decode(str_replace("\r",'',str_replace(chr(127),';',trim($a[$i]))));
           switch($sT){
            case 't': case 'a': case 'z': case 'l': break;
            case 'm': $s=str_replace('|',"\r\n",$s); break;
            case 'd': if($s){$s='20'.substr($s,0,8); $s.=rtrim(@date(' w',@mktime(12,0,0,substr($s,5,2),substr($s,8,2),substr($s,0,4))));} break;
            case 'j': case '#': if($s!='J') $s='N'; break;
            case 'w': case 'n': if(!$s=str_replace(',','.',$s)) $s='0'; break;
            case 'b':
             if($s){
              $UpEx=strtolower(strrchr($s,'.'));
              if($UpEx=='.jpg'||$UpEx=='.jpeg') $Src=ImageCreateFromJPEG($sAltAdr.'bilder/gross/'.$s);
              elseif($UpEx=='.gif')$Src=ImageCreateFromGIF($sAltAdr.'bilder/gross/'.$s);
              elseif($UpEx=='.png')$Src=ImageCreateFromPNG($sAltAdr.'bilder/gross/'.$s);
              if(!empty($Src)){
               $Sx=ImageSX($Src); $Sy=ImageSY($Src); $UpBa=substr($s,0,-1*strlen($UpEx));
               if($Sx>KAL_VorschauBreit||$Sy>KAL_VorschauBreit){ //Vorschau verkleinern
                $Dw=min(KAL_VorschauBreit,$Sx);
                if($Sx>KAL_VorschauBreit) $Dh=round(KAL_VorschauBreit/$Sx*$Sy); else $Dh=$Sy;
                if($Dh>KAL_VorschauHoch){$Dw=round(KAL_VorschauHoch/$Dh*$Dw); $Dh=VorschauHoch;}
                $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
                ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
                if(@imagejpeg($Dest,KAL_Pfad.KAL_Bilder.$k.'-'.$UpBa.'.jpg',100)) $v=$UpBa.'.jpg|'; else{$bFehlB=true; $v='|';}
                imagedestroy($Dest); unset($Dest);
               }else{
                if(@copy($sAltAdr.'bilder/gross/'.$s,KAL_Pfad.KAL_Bilder.$k.'-'.$UpBa.$UpEx)) $v=$UpBa.$UpEx.'|'; else{$bFehlB=true; $v='|';}
               }
               if($Sx>KAL_BildBreit||$Sy>KAL_BildBreit){ //Bild verkleinern
                $Dw=min(KAL_BildBreit,$Sx);
                if($Sx>KAL_BildBreit) $Dh=round(KAL_BildBreit/$Sx*$Sy); else $Dh=$Sy;
                if($Dh>KAL_BildHoch){$Dw=round(KAL_BildHoch/$Dh*$Dw); $Dh=KAL_BildHoch;}
                $Dest=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dest,0,0,ImageColorAllocate($Dest,255,255,255));
                ImageCopyResampled($Dest,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
                @imagejpeg($Dest,KAL_Pfad.KAL_Bilder.$k.'_'.$UpBa.'.jpg');
                $v.=$UpBa.'.jpg'; imagedestroy($Dest); unset($Dest);
               }else{$v.=$UpBa.$UpEx; @copy($sAltAdr.'bilder/gross/'.$s,KAL_Pfad.KAL_Bilder.$k.'_'.$UpBa.$UpEx);}
               imagedestroy($Src); unset($Src); $s=$v;
              }else{$s=''; $bFehlB=true;}
             }break;
            case 'o': if($s) if(!copy($sAltAdr.'objekte/'.$s,KAL_Pfad.KAL_Bilder.$k.'~'.$s)){$s=''; $bFehlD=true;} break;
            case 'p': if($s) if($bAltCC) $s=fKalEnCode(DeCryptOldPw($s,$nAltCC)); else $s=''; break;
           }//switch $sT
           $sZ.=',"'.str_replace('"','\"',$s).'"';
          }//alle Felder
          if(!$DbO->query('INSERT IGNORE INTO '.KAL_SqlTabT.' VALUES('.$sZ.',"")')) $bSqlOK=false;
         }//alle Termine
         if($bSqlOK) $Msg='<p class="admErfo">Die Termindatei <i>'.$sAltAdr.'termine.txt</i> wurde erfolgreich eingelesen!</p>';
         else $Msg.='<p class="admFehl">Die Termindatei <i>'.$sAltAdr.'termine.txt</i> wurde nur teilweise eingelesen</p>';
         if($bFehlB) $Msg.='<p class="admFehl">Leider konnten nicht alle Bilder importiert werden!</p>';
         if($bFehlD) $Msg.='<p class="admFehl">Leider konnten nicht alle Dateianhänge importiert werden!</p>';
         if($f=fopen(KAL_Pfad.KAL_Daten.KAL_Vorgaben,'w')){for($i=0;$i<$nFelder;$i++) fwrite($f,rtrim($aV[$i]).NL); fclose($f);}
         else $Msg.='<p class="admFehl">In die Datei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden!</p>';
        }else $Msg='<p class="admFehl">Die MySQL-Tabelle <i>'.KAL_SqlTabT.'</i> konnte nicht angelegt werden!</p>';
       }else $Msg='<p class="admFehl">In die Hilfsdatei <i>'.KAL_Daten.KAL_Vorgaben.'</i> konnte nicht geschrieben werden.</p>';
      }else echo '<p class="admFehl">MySQL-Datenbank nicht geöffnet!</p>'; //SQL
     }else $Msg='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
    }//importieren
   }else $Msg='<p class="admFehl">Die Datei <i>'.$sAltAdr.'termine.txt</i> hat nicht das erwartete Format!</p><p>Sie beginnt mit: '.htmlentities(substr($H1.$H2.$H3.$aD[0].$aD[1].$aD[2],0,88),ENT_COMPAT,'ISO-8859-1').'</p>';
  }else if(!$Msg) $Msg='<p class="admFehl">Die Datei <i>'.$sAltAdr.'termine.txt</i> ist leer!</p>';
 }else $Msg='<p class="admFehl">Geben Sie den Ort des alten Kalender-Scripts Version 2.x an!</p>';
}

//Scriptausgabe
if(!$Msg) $Msg='<p class="admMeld">Sie können die Termine aus einer älteren Version 2.x des Kalender-Scripts importieren.</p>';
echo $Msg.NL;
?>

<form action="importVer2x.php" method="post">
<?php
if(!$bAltOK){ //GET oder falscher Pfad
?>

<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
<tr class="admTabl"><td class="admSpa2" colspan="2">Tragen Sie den Ort des alten Kalenders Version 2.x
als absoluten lokalen Pfad oder als fernen URL ein.
Ein lokaler Pfad könnte beispielsweise lauten: <i>/var/kunden/webs/www.ein-web.de/htdocs/kalender</i>,
eine ferne Adresse beispielsweise <i>http://www.ein-web.de/kalender</i>.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Importadresse</td>
 <td><input style="width:100%" type="text" name="AltAdr" value="<?php echo $sAltAdr?>" /></td>
</tr>
<tr class="admTabl"><td class="admSpa2" colspan="2"><u>Information</u>:
Dieses Kalender-Script liegt im Pfad
<i><?php echo substr(KAL_Pfad,0,-1)?></i> bzw. unter der Adresse <i><?php echo substr($sHttp,0,-1)?></i>.
</td></tr>
</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>

<?php
}else{ //Pfad OK
 if($bImp){
  echo NL.'<input type=hidden name="AltAdr" value="'.$sAltAdr.'" />';
  echo NL.'<input type=hidden name="NeuImp" value="1" />';
  echo NL.'<p class="admSubmit"><input class="admSubmit" type="submit" value="Importieren"></p>';
 }else echo '<p class="admSubmit">[ OK ]</p>';
}
?>
</form>

<div class="admBox"><p>Hinweise:</p>
<p>Termine können aus einem lokalen Pfad des selben Servers oder von einem fernen Server importiert werden.
Beim Import vom selben Server ist der absolute Pfad anzugeben. Ein solcher Import verläuft vollständig.
Der Import vom fernen Server erfolgt über den URL des alten Kalender-Scripts.
Allerdings können dabei die eventuell in den Terminen vorhandenen verschlüsselten Passworte <i>nicht</i> importiert werden.
Ein lokaler Import ist also stets vorzuziehen.</p>
<p>Bei einem Import von Terminen aus der Version 2.x werden die alte Terminstruktur und die alten Termindaten übernommen.
Die momentane Terminstruktur in Version 3.x wird überschrieben
und alle eventuell vorhandenen Termineinträge sowie Bilder und Dateianhänge der Version 3.x werden gelöscht.</p>
<p>Es werden nur die Terminstruktur und die Termindaten importiert,
<i>nicht</i> die Einstellungen zur Kalenderfunktion, zu den Farben oder zum Layout.
Alle Einstellungen wie z.B. Anzeigedauer, Wochentagsanzeige, Währungszeichen, Bildgrößen usw.
werden nicht von der Version 2.x übernommen sondern bleiben mit deren momentanen Werten erhalten.
Setzen Sie also z.B. die Parameter für die Bildgröße <i>vor</i> dem Import auf gewünschte Werte.</p>
<p>Falls Ihr Kalender in der Version 2.x sehr viele Bilder enthält kann der Import je nach Leistung des Servers länger dauern.
Auf üblichen Servern wird aber die Ausführungszeit für ein PHP-Script vom Provider auf meist maximal 30 Sekunden begrenzt.
Überschreitet die Importdauer dieses Zeitlimit, wird Ihr Server den Import abbrechen.
Verluste an den Daten dieser Kalenderversion 3.x sind bei solch einem unkontrollierten Abbruch nicht auszuschließen!
Die Daten des alten Kalenders Version 2.x werden jedoch keinesfalls beschädigt.</p>
</div>

<?php
echo fSeitenFuss();

function fSetzIArray($a,$n,$t){
 global $sWerte;
 $p=strpos($sWerte,'$kal_'.$n.'='); $e=strpos($sWerte,');',$p); $p=strpos($sWerte,'array(',$p);
 if($p>0&&$e>$p){
  $k=count($a); $s=$t.$a[0].$t; for($i=1;$i<$k;$i++) $s.=','.$t.$a[$i].$t;
  $sWerte=substr_replace($sWerte,'array('.$s,$p,$e-$p); return true;
 }else return false;
}

function DeCryptOldPw($Pw,$CC){
 $j=0; for($k=strlen($Pw)/2-1;$k>=0;$k--) $Out.=chr($CC+($j++)+hexdec(substr($Pw,$k+$k,2)));
 return $Out;
}
?>