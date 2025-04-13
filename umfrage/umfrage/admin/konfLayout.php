<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Layout-Einstellungen','','KLy');

$umfStyle=UMF_CSSDatei; if(!file_exists(UMF_Pfad.$umfStyle)) $umfStyle='umfStyle.css';
if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo=''; $Ms2=''; $bCSSFirst=true;
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(UMF_Pfad.'umfWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('Schablone'); if(fSetzUmfWert($v,'Schablone',"'")) $bNeu=true;
  if($v&&$v!='umfSeite.htm'&&!file_exists(UMF_Pfad.$v)){
   if(is_writeable(UMF_Pfad)){
    if(@copy(UMF_Pfad.'umfSeite.htm',UMF_Pfad.$v)) $Ms2.=fMFehl('Die Schablone <i>'.$v.'</i> wurde angelegt. Bitte manuell anpassen!');
    else $Ms2.=fMFehl('Die Schablone '.$v.' durfte nicht angelegt werden. Bitte manuell anlegen!');
   }else $Ms2.=fMFehl('Die Schablone '.$v.' durfte nicht gespeichert werden. Bitte manuell anlegen!');
  }
  $v=txtVar('CSSDatei'); if(fSetzUmfWert($v,'CSSDatei',"'")){$bNeu=true; $umfStyle=$usCSSDatei;}
  if($v&&$v!='umfStyle.css'&&!file_exists(UMF_Pfad.$v)){
   if(is_writeable(UMF_Pfad)){
    if(@copy(UMF_Pfad.'umfStyle.css',UMF_Pfad.$v)) $Ms2.=fMFehl('Die Datei <i>'.$v.'</i> wurde angelegt. Bitte individuell anpassen!');
    else{$Ms2.=fMFehl('Die Datei '.$v.' durfte nicht angelegt werden. Bitte manuell anlegen!'); $umfStyle='umfStyle.css';}
   }else{$Ms2.=fMFehl('Die Datei '.$v.' durfte nicht gespeichert werden. Bitte manuell anlegen!'); $umfStyle='umfStyle.css';}
  }
  $v=(int)txtVar('Layout'); if(fSetzUmfWert($v,'Layout','')) $bNeu=true;
  $v=max((int)txtVar('BildKB'),1); if(fSetzUmfWert($v,'BildKB','')) $bNeu=true;
  $v=max((int)txtVar('BildW'),8); if(fSetzUmfWert($v,'BildW','')) $bNeu=true;
  $v=max((int)txtVar('BildH'),8); if(fSetzUmfWert($v,'BildH','')) $bNeu=true;
  if(isset($_POST['BildErsLsch'])&&$_POST['BildErsLsch']=='1') $v=''; else $v=(isset($_POST['BildErsatz'])?$_POST['BildErsatz']:''); if(fSetzUmfWert($v,'BildErsatz',"'")) $bNeu=true;
  $ImN=(isset($_FILES['BildErsNeu'])?str_replace(' ','_',basename($_FILES['BildErsNeu']['name'])):''); $ImE=strtolower(strrchr($ImN,'.'));//Bildersatz
  if($ImE=='.jpg'||$ImE=='.gif'||$ImE=='.jpeg'||$ImE=='.png'){
   $i=$_FILES['BildErsNeu']['size'];
   if($i<=$usBildKB*1024){
    $aIm=@getimagesize($_FILES['BildErsNeu']['tmp_name']);
    if($aIm[0]<=$usBildW&&$aIm[1]<=$usBildH){//direkt speichern
     if(copy($_FILES['BildErsNeu']['tmp_name'],UMF_Pfad.UMF_Bilder.$ImN)){
      fSetzUmfWert($ImN,'BildErsatz',"'"); $bNeu=true; $Ms2.='</p><p class="admErfo">Das neue ErsatzBild wurde hochgeladen!';
     }else $Ms2.='</p><p class="admFehl">Das ErsatzBild durfte nicht gespeichert werden!';
    }else{//verkleinern
     if($ImE=='.jpg'||$ImE=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['BildErsNeu']['tmp_name']);
     elseif($ImE=='.gif') $Src=ImageCreateFromGIF($_FILES['BildErsNeu']['tmp_name']);
     elseif($ImE=='.png') $Src=ImageCreateFromPNG($_FILES['BildErsNeu']['tmp_name']);
     if($Src){
      $ImN=substr($ImN,0,-strlen($ImE)).'.jpg'; $Sx=ImageSX($Src); $Sy=ImageSY($Src);
      $Dw=min($usBildW,$Sx); if($Sx>$usBildW) $Dh=round($usBildW/$Sx*$Sy); else $Dh=$Sy;
      if($Dh>$usBildH){$Dw=round($usBildH/$Dh*$Dw); $Dh=$usBildH;}
      $Dst=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dst,0,0,ImageColorAllocate($Dst,255,255,255));
      ImageCopyResampled($Dst,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
      if(ImageJPEG($Dst,UMF_Pfad.UMF_Bilder.$ImN)){
       fSetzUmfWert($ImN,'BildErsatz',"'"); $bNeu=true; $Ms2.='</p><p class="admErfo">Das neue ErsatzBild wurde hochgeladen!';
      }else $Ms2.='</p><p class="admFehl">Das ErsatzBild durfte nicht gespeichert werden!';
      imagedestroy($Dst); imagedestroy($Src); unset($Dst); unset($Src);
     }else $Ms2.='</p><p class="admFehl">Das ErsatzBild <i>'.$ImN.'</i> konnte nicht eingelesen werden!';
    }
   }else $Ms2.='</p><p class="admFehl">ErsatzBilder mit <i>'.$i.' KByte</i> Größe sind nicht erlaubt!';
  }elseif(substr($ImE,0,1)=='.') $Ms2.='</p><p class="admFehl">ErsatzBilder mit der Endung <i>'.$ImE.'</i> sind nicht erlaubt!';
  $v=(int)txtVar('RadioButton'); if(fSetzUmfWert(($v?true:false),'RadioButton','')) $bNeu=true;
  $v=txtVar('ZeigeNummer'); if(fSetzUmfWert($v,'ZeigeNummer',"'")) $bNeu=true;
  $v=txtVar('NummerStellen'); if(fSetzUmfWert($v,'NummerStellen',"'")) $bNeu=true;
  $v=txtVar('NummernText'); if(fSetzUmfWert($v,'NummernText',"'")) $bNeu=true;
  $v=txtVar('ZeigeBemerkung'); if(fSetzUmfWert($v,'ZeigeBemerkung',"'")) $bNeu=true;
  $v=txtVar('ZeigeBemerkng2'); if(fSetzUmfWert($v,'ZeigeBemerkng2',"'")) $bNeu=true;
  $v=txtVar('TxVorFrage'); if(fSetzUmfWert($v,'TxVorFrage','"')) $bNeu=true;

  $v=txtVar('NachAbstimmen'); if(fSetzUmfWert($v,'NachAbstimmen',"'")) $bNeu=true;
  $v=txtVar('TxGrafik'); if(fSetzUmfWert($v,'TxGrafik','"')) $bNeu=true;
  $v=txtVar('GrafikBalken'); if(fSetzUmfWert(($v?true:false),'GrafikBalken','')) $bNeu=true;
  $v=max((int)txtVar('GrafikMaximum'),16); if(fSetzUmfWert($v,'GrafikMaximum','')) $bNeu=true;
  $v=max((int)txtVar('GrafikDicke'),3); if(fSetzUmfWert($v,'GrafikDicke','')) $bNeu=true;
  $v=txtVar('GrafikFrage'); if(fSetzUmfWert($v,'GrafikFrage',"'")) $bNeu=true;
  $v=txtVar('GrafikWerte'); if(fSetzUmfWert($v,'GrafikWerte',"'")) $bNeu=true;
  if($usGrafikBalken){if($usGrafikWerte=='oben'||$usGrafikWerte=='unten') if(fSetzUmfWert('rechts','GrafikWerte',"'")) $bNeu=true;}
  else{if($usGrafikWerte=='links'||$usGrafikWerte=='rechts') if(fSetzUmfWert('oben','GrafikWerte',"'")) $bNeu=true;}
  $v=(int)txtVar('GrafikProzente'); if(fSetzUmfWert(($v?true:false),'GrafikProzente','')) $bNeu=true;
  $v=(int)txtVar('GrafikTlnAnz'); if(fSetzUmfWert(($v?true:false),'GrafikTlnAnz','')) $bNeu=true;
  $v=txtVar('TxTeilnehmer'); if(fSetzUmfWert($v,'TxTeilnehmer','"')) $bNeu=true;

  $DivW=max((int)$_POST['DivW'],250); $WDiv=(int)$_POST['WDiv'];
  $DivT=max((int)$_POST['DivT'],150);$TDiv=(int)$_POST['TDiv'];
  $ScrM=max((int)$_POST['ScrM'],250);$MScr=(int)$_POST['MScr'];
  $DivG=max((int)$_POST['DivG'],50); $GDiv=(int)$_POST['GDiv'];

  if($bCSSFirst&&($DivW!=$WDiv||$DivT!=$TDiv||$ScrM!=$MScr||$DivG!=$GDiv)){
   $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',trim(implode('',file(UMF_Pfad.$umfStyle)))));
   if($DivW!=$WDiv) if($p=strpos($t,'div.umfGsmt')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)){
    $p=strpos($t,'}',$p); if($o=strpos($t,';',$q)) if($o>$p) $o=0;
    if(!$o) if($o=strpos($t,"\r",$q)) if($o>$p) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$p;
    $t=substr_replace($t,'max-width:'.$DivW.'px',$q,$o-$q);
   }
   if($DivT!=$TDiv) if($p=strpos($t,'div.umfTxBl')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)){
    $p=strpos($t,'}',$p); if($o=strpos($t,';',$q)) if($o>$p) $o=0;
    if(!$o) if($o=strpos($t,"\r",$q)) if($o>$p) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$p;
    $t=substr_replace($t,'max-width:'.$DivT.'px',$q,$o-$q);
   }
   if($ScrM!=$MScr) if($p=strpos($t,'media screen')) if($q=strpos($t,'width',$p)) if($o=strpos($t,')',$q)) if($q<$o){
    $t=substr_replace($t,'width:'.$ScrM.'px',$q,$o-$q);
   }
   if($DivG!=$GDiv) if($p=strpos($t,'table.umfGraf')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)){
    $p=strpos($t,'}',$p); if($o=strpos($t,';',$q)) if($o>$p) $o=0;
    if(!$o) if($o=strpos($t,"\r",$q)) if($o>$p) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$p;
    $t=substr_replace($t,'max-width:'.$DivG.'px',$q,$o-$q);
   }
   if($f=fopen(UMF_Pfad.$umfStyle,'w')){
    fwrite($f,$t); fclose($f); $GDiv=$DivG; $TDiv=$DivT; $MScr=$ScrM; $GDiv=$DivG; $bCSSFirst=false;
    $Meld=fMErfo('Die neuen Ausgabebreiten wurde gespeichert.');
   }else $Ms2.=fMFehl(str_replace('#',$umfStyle,UMF_TxDateiRechte));
  }
  if($bNeu){//Speichern
   if($f=fopen(UMF_Pfad.'umfWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.=fMFehl('In die Datei <i>umfWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden (Rechteproblem)!');
  }elseif(!$bCSSFirst) $sMeld=fMErfo('Die neuen Ausgabebreiten wurde gespeichert.');
 }//while
 if($sErfo) $sMeld.=fMErfo('Die Layout-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.');
 else $sMeld.=fMMeld('Die Layout-Einstellungen bleiben unverändert.');
 $sMeld.=$Ms2;
}else{
 $sMeld=fMMeld('Stellen Sie die Layouteinstellungen des Umfrage-Scripts passend ein.');
 if($umfStyle!=UMF_CSSDatei) $sMeld.=fMFehl('Die eingetragene CSS-Datei <i>'.UMF_CSSDatei.'</i> ist nicht verfügbar. Ersatzweise wird <i>'.$umfStyle.'</i> verwendet!');
 $usSchablone=UMF_Schablone; $usCSSDatei=UMF_CSSDatei; $usLayout=UMF_Layout;
 $usBildKB=UMF_BildKB; $usBildW=UMF_BildW; $usBildH=UMF_BildH; $usBildErsatz=UMF_BildErsatz;
 $usRadioButton=UMF_RadioButton; $usZeigeNummer=UMF_ZeigeNummer; $usNummerStellen=UMF_NummerStellen; $usNummernText=UMF_NummernText;
 $usZeigeBemerkung=UMF_ZeigeBemerkung; $usZeigeBemerkng2=UMF_ZeigeBemerkng2; $usTxVorFrage=UMF_TxVorFrage;
 $usNachAbstimmen=UMF_NachAbstimmen; $usTxGrafik=UMF_TxGrafik;
 $usGrafikBalken=UMF_GrafikBalken; $usGrafikMaximum=UMF_GrafikMaximum; $usGrafikDicke=UMF_GrafikDicke;
 $usGrafikFrage=UMF_GrafikFrage; $usGrafikWerte=UMF_GrafikWerte; $usGrafikProzente=UMF_GrafikProzente;
 $usGrafikTlnAnz=UMF_GrafikTlnAnz; $usTxTeilnehmer=UMF_TxTeilnehmer;
 $t=str_replace("\r",'',trim(implode('',file(UMF_Pfad.$umfStyle)))); //Stylebreite holen
 if($p=strpos($t,'div.umfGsmt')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)) if($p=strpos($t,':',$q)) $DivW=(int)substr($t,$p+1,10); $WDiv=$DivW;
 if($p=strpos($t,'div.umfTxBl')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)) if($p=strpos($t,':',$q)) $DivT=(int)substr($t,$p+1,10); $TDiv=$DivT;
 if($p=strpos($t,'media screen')) if($q=strpos($t,'width',$p)) if($q<strpos($t,'}',$p)) if($p=strpos($t,':',$q)) $ScrM=(int)substr($t,$p+1,10); $MScr=$ScrM;
 if($p=strpos($t,'table.umfGraf')) if($q=strpos($t,'max-width',$p)) if($q<strpos($t,'}',$p)) if($p=strpos($t,':',$q)) $DivG=(int)substr($t,$p+1,10); $GDiv=$DivG;
}

//Scriptausgabe
echo $sMeld.NL;
?>

<form name="layoutform" action="konfLayout.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="BildErsatz" value="<?php echo $usBildErsatz?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgaben des Umfrage-Scripts werden (sofern das Script eigenständig
und nicht über den PHP-Befehl <i>include</i> eingebettet läuft) in eine umrahmende HTML-Schablonenseite namens <i>umfSeite.htm</i> eingebettet.
Im Ausnahmefall kann der Gebrauch dieser umhüllenden Seite unterbleiben.
Dann erfolgt die Ausgabe jedoch ohne die Verwendung von &lt;head&gt; und &lt;body&gt;.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">HTML-Schablone</td>
 <td><input type="text" name="Schablone" value="<?php echo $usSchablone?>" style="width:10em" />
 HTML-Umhüllung <i>umfSeite.htm</i> verwenden &nbsp; <span class="admMini">(Empfehlung: <i>unbedingt</i> verwenden)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">CSS-Style-Datei</td>
 <td><input type="text" name="CSSDatei" value="<?php echo $usCSSDatei?>" style="width:10em" />
 Standard CSS-Datei <i>umfStyle.css</i> verwenden oder spezielle Datei<?php if(KONF){?> <i>umfStyle<?php echo KONF?>.css</i><?php }?></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen und Antworten können in 4 unterschiedlichen Anordnungen (Layoutvarianten) dargestellt werden.
Jedes Layout kann über die Farbeinstellungen bzw. durch direkte Bearbeitung der CSS-Datei individualisiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutvariante</td>
 <td>
  <select name="Layout" size="1">
   <option value="0<?php if($usLayout<='0') echo '" selected="selected'?>">Layout ohne Bilder (nur Textblock aus Frage und Antworten)</option>
   <option value="1<?php if($usLayout=='1') echo '" selected="selected'?>">Bild links (Textblock mit Frage und Antworten rechts davon)</option>
   <option value="2<?php if($usLayout=='2') echo '" selected="selected'?>">Bild rechts (Textblock mit Frage und Antworten links davon)</option>
   <option value="3<?php if($usLayout=='3') echo '" selected="selected'?>">Bild oben (Textblock mit Frage und Antworten darunter)</option>
  </select>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei eingestellter Layoutvariante Bildblock und Textblock werden beide Blöcke mit einem gemeinsamen Rahmen umrahmt. Welche maximale Breite soll dieser Gesamtrahmen erhalten?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Gesamtbreite<br />des Layouts</td>
 <td><input type="text" name="DivW" value="<?php echo $DivW?>" size="3" style="width:3.5em;"> Pixel <input type="hidden" name="WDiv" value="<?php echo $WDiv?>" /> &nbsp; &nbsp; <span class="admMini"><i>Empfehlung</i>: ca. 500 Pixel</span>
 <div class="admMini"><i>Richtwert</i>: Summe aus unten folgender Textblockbreite <?php echo $DivT?> und Bildbreite <?php echo $usBildW?> plus kleine Reserve</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Bei eingestellter Layoutvariante Bildblock und Textblock nebeneinander kann es auf schmalen Displays zu Platzproblemen kommen. Auf solchen schmalen Displays kann sich das Layout selbstständig auf eine Darstellung mit Bildblock und Textblock untereinander statt nebeneinander anpassen (responsives Design). Bei welcher Displaybreite soll diese automatische Umschaltung erfolgen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Umschaltgrenze<br />schmale Displays</td>
 <td><input type="text" name="ScrM" value="<?php echo $ScrM?>" size="3" style="width:3.5em;"> Pixel<input type="hidden" name="MScr" value="<?php echo $MScr?>" /> &nbsp; &nbsp; <span class="admMini"><i>Empfehlung</i>: ca. 500 Pixel</span>
 <div class="admMini"><i>Richtwert</i>: Summe aus folgender Textblockbreite <?php echo $DivT?> und Bildbreite <?php echo $usBildW?> plus kleine Reserve</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen und Antworten werden unabhängig von der Layoutvariante in einem Textblock präsentiert.
Die maximale Breite dieses Textblocks (ohne das eventuelle Bild) ist einstellbar.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Breite&nbsp;des&nbsp;Textblockes<div>mit der Fragezeile und</div>den Antwortzeilen</td>
 <td><input type="text" name="DivT" value="<?php echo $DivT?>" size="4" style="width:3.5em;"> Pixel <input type="hidden" name="TDiv" value="<?php echo $TDiv?>" /> &nbsp; &nbsp; <span class="admMini"><i>Empfehlung</i>: ca. 350 Pixel</span>
 <div class="admMini" style="margin-top:5px;">Noch mehr Layouteinstellungen können Sie direkt in der CSS-Datei <a href="konfCss.php<?php if(KONF) echo '?konf='.KONF ?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei editieren"></a> <i><?php echo $umfStyle ?></i> bearbeiten.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie ein Layout mit Bild verwenden und zu Ihren Fragen auch Bilder hochladen, werden diese beim Hochladen in der Größe skaliert.
Die Maximalwerte sind einstellbar.</td></tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="2">Hochladen<div>von Bildern</div></td>
 <td>Bild beim Hochladen verkleinern auf maximal <input type="text" name="BildW" value="<?php echo $usBildW?>" size="3" style="width:3.5em;"> Pixel Breite
 bzw. maximal <input type="text" name="BildH" value="<?php echo $usBildH?>" size="3" style="width:3.5em;"> Pixel Höhe</td>
</tr>
<tr class="admTabl">
 <td>maximale Dateigröße der Bildquelle im Original <input type="text" name="BildKB" value="<?php echo $usBildKB?>" size="3" style="width:3.5em;"> KByte
 <div class="admMini"><u>Hinweis</u>: die wenigsten PHP-Einstellungen auf Mietservern verkraften mehr als 1 MByte problemfrei</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie ein Layout mit Bild verwenden und zu einer Fragen ausnahmsweise kein Bild zur Verfügung steht, kann das Ersatzbild verwendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ersatzbild</td>
 <td><div style="margin-bottom:4px"><input type="file" name="BildErsNeu" size="90"></div>
 <input type="checkbox" class="admCheck" name="BildErsLsch<?php if(empty($usBildErsatz)) echo '" checked="checked'?>" value="1"> kein Ersatzbild verwenden &nbsp; - &nbsp;
 aktuelles Ersatzbild: <a href="<?php echo UMFPFAD.UMF_Bilder.$usBildErsatz?>" target="bld" onclick="BldWin()"><img src="iconVorschau.gif" width="13" height="13" border="0" title="Ersatzbild <?php echo $usBildErsatz?> anzeigen"></a> <i><?php echo $usBildErsatz?></i></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Zum Auswählen der Antworten werden standardmäßig Radioschalter (Radiobuttons) verwendet.
 Alternativ können auch Kontrollkästchen (Checkboxen) verwendet werden, damit mehrere Antworten pro Frage auswählbar sind.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortauswahl</td>
 <td><input type="radio"  class="admRadio" name="RadioButton<?php if($usRadioButton) echo '" checked="checked'?>" value="1"> Radioschalter verwenden &nbsp;
 <input type="radio"  class="admRadio" name="RadioButton<?php if(!$usRadioButton) echo '" checked="checked'?>" value="0"> Checkboxen verwenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Unmittelbar vor der Frage (schon direkt in der Fragezeile) kann ein Zusatzbegriff wie &quot;Frage&quot; eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Vorwort<br />vor der Frage</td>
 <td><input type="text" name="TxVorFrage" value="<?php echo $usTxVorFrage?>" size="90" style="width:98%;">
 <div class="admMini">leer lassen oder z.B. mit BB-Code: [color=navy][b]Frage[/b][/color]:</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über oder unter den Fragen/Antworten kann eine Zusatzzeile mit der Nummer der aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragennummer</td>
 <td><select name="ZeigeNummer" size="1">
   <option value="">keine Fragennummer</option>
   <option value="oben<?php if($usZeigeNummer=='oben') echo '" selected="selected'?>">Fragennummer oberhalb der Frage</option>
   <option value="unten<?php if($usZeigeNummer=='unten') echo '" selected="selected'?>">Fragennummer unterhalb der Antworten</option>
  </select> &nbsp; im
  <select name="NummerStellen" size="1">
   <option value="0">natürlichen</option>
   <option value="2<?php if($usNummerStellen=='2') echo '" selected="selected'?>">2-stelligen</option>
   <option value="3<?php if($usNummerStellen=='3') echo '" selected="selected'?>">3-stelligen</option>
   <option value="4<?php if($usNummerStellen=='4') echo '" selected="selected'?>">4-stelligen</option>
  </select> Format</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Nummerndarstellung</td>
 <td><input type="text" name="NummernText" value="<?php echo $usNummernText?>" size="12" style="width:12em;">
  <div class="admMini">Platzhalter:<br>#N - laufende Nummer der Frage in der Umfrage &nbsp; &nbsp; #I - ID-Nummer der Frage in der Fragendatenbank<br>#M - Anzahl der Fragen in der Umfrage</div>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Zusätzlich zur Frage und den Antworten können die Anmerkungen zur Frage oberhalb oder unterhalb eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkung-1</td>
 <td><select name="ZeigeBemerkung" size="1">
   <option value="">Anmerkung-1 nicht anzeigen</option>
   <option value="oben1<?php if($usZeigeBemerkung=='oben1') echo '" selected="selected'?>">Anmerkung-1 oberhalb von Bildblock und Textblock</option>
   <option value="oben2<?php if($usZeigeBemerkung=='oben2') echo '" selected="selected'?>">Anmerkung-1 oberhalb von Fragenummer und Frage</option>
   <option value="oben3<?php if($usZeigeBemerkung=='oben3') echo '" selected="selected'?>">Anmerkung-1 oberhalb der Frage aber unterhalb der Fragenummer</option>
   <option value="oben4<?php if($usZeigeBemerkung=='oben4') echo '" selected="selected'?>">Anmerkung-1 oberhalb der Antworten aber unterhalb der Frage</option>
   <option value="unten1<?php if($usZeigeBemerkung=='unten1') echo '" selected="selected'?>">Anmerkung-1 unterhalb der Antworten aber oberhalb der Fragenummer</option>
   <option value="unten2<?php if($usZeigeBemerkung=='unten2') echo '" selected="selected'?>">Anmerkung-1 unterhalb der Antworten und der Fragenummer aber oberhalb der Schaltfläche</option>
   <option value="unten3<?php if($usZeigeBemerkung=='unten3') echo '" selected="selected'?>">Anmerkung-1 unterhalb von Bildblock, Textblock und Schaltfläche</option>
  </select></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkung-2</td>
 <td><select name="ZeigeBemerkng2" size="1">
   <option value="">Anmerkung-2 nicht anzeigen</option>
   <option value="oben1<?php if($usZeigeBemerkng2=='oben1') echo '" selected="selected'?>">Anmerkung-2 oberhalb von Bildblock und Textblock</option>
   <option value="oben2<?php if($usZeigeBemerkng2=='oben2') echo '" selected="selected'?>">Anmerkung-2 oberhalb von Fragenummer und Frage</option>
   <option value="oben3<?php if($usZeigeBemerkng2=='oben3') echo '" selected="selected'?>">Anmerkung-2 oberhalb der Frage aber unterhalb der Fragenummer</option>
   <option value="oben4<?php if($usZeigeBemerkng2=='oben4') echo '" selected="selected'?>">Anmerkung-2 oberhalb der Antworten aber unterhalb der Frage</option>
   <option value="unten1<?php if($usZeigeBemerkng2=='unten1') echo '" selected="selected'?>">Anmerkung-2 unterhalb der Antworten aber oberhalb der Fragenummer</option>
   <option value="unten2<?php if($usZeigeBemerkng2=='unten2') echo '" selected="selected'?>">Anmerkung-2 unterhalb der Antworten und der Fragenummer aber oberhalb der Schaltfläche</option>
   <option value="unten3<?php if($usZeigeBemerkng2=='unten3') echo '" selected="selected'?>">Anmerkung-2 unterhalb von Bildblock, Textblock und Schaltfläche</option>
  </select></td>
</tr>
</table><br />

<p class="admMeld">Layouteinstellungen der grafischen Auswertung</p>
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Darstellung der Umfrageergebnisse erfolgt auf Wunsch in einem Diagramm.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">nach dem Abstimmen</td>
 <td><input type="radio"  class="admRadio" name="NachAbstimmen" value="Grafik<?php if($usNachAbstimmen=='Grafik') echo '" checked="checked'?>" /> Diagramm-Seite sofort anzeigen &nbsp;
 <input type="radio"  class="admRadio" name="NachAbstimmen" value="Fertig<?php if($usNachAbstimmen=='Fertig') echo '" checked="checked'?>" /> erst einmal Fertig-Textseite anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Diagrammüberschrift</td>
 <td><input type="text" name="TxGrafik" value="<?php echo $usTxGrafik?>" size="90" style="width:98%;">
 <div class="admMini">Textvorschlag: Bisher wurde so abgestimmt:</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Diagrammtyp</td>
 <td><input type="radio"  class="admRadio" name="GrafikBalken<?php if($usGrafikBalken) echo '" checked="checked'?>" value="1"> Balkendiagramm &nbsp; &nbsp;
 <input type="radio"  class="admRadio" name="GrafikBalken<?php if(!$usGrafikBalken) echo '" checked="checked'?>" value="0"> Säulendiagramm</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Grafik-Skalierung</td>
 <td><input type="text" name="GrafikMaximum" value="<?php echo $usGrafikMaximum?>" size="4" style="width:3.5em;">
 Pixel für die höchste Säule bzw. den längsten Balken &nbsp; <span class="admMini">(Empfehlung: max. 200)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap">Grafik-Dicke</td>
 <td><input type="text" name="GrafikDicke" value="<?php echo $usGrafikDicke?>" size="4" style="width:3.5em;">
 Pixel für die Dicke der Säule bzw. der Balken &nbsp; <span class="admMini">(Empfehlung: ca. 20)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1" style="white-space:nowrap">Breite des Diagramms</td>
 <td><input type="text" name="DivG" value="<?php echo $DivG?>" size="4" style="width:3.5em;"> Pixel &nbsp; &nbsp; Empfehlung: ca. 300<input type="hidden" name="GDiv" value="<?php echo $GDiv?>" />
 <div class="admMini">Noch mehr Layouteinstellungen können Sie direkt in der CSS-Datei <a href="konfCss.php<?php if(KONF) echo '?konf='.KONF ?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei editieren"></a> <i><?php echo $umfStyle ?></i> bearbeiten.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Das Diagramm kann mit der Fragestellung
und den vorhandenen Abstimmungswerten beschriftet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragentext</td>
 <td>
  <select name="GrafikFrage" size="1" style="width:16em;">
   <option value="">kein Fragentext</option>
   <option value="oben<?php if($usGrafikFrage=='oben') echo '" selected="selected'?>">oberhalb der Grafik</option>
   <option value="unten<?php if($usGrafikFrage=='unten') echo '" selected="selected'?>">unterhalb der Grafik</option>
  </select>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Abstimmungswerte</td>
 <td>
  <select name="GrafikWerte" size="1" style="width:16em;">
   <option value="">keine Werte anzeigen</option>
   <option value="oben<?php if($usGrafikWerte=='oben') echo '" selected="selected'?>">oberhalb der Säulen</option>
   <option value="unten<?php if($usGrafikWerte=='unten') echo '" selected="selected'?>">unterhalb der Säulen</option>
   <option value="links<?php if($usGrafikWerte=='links') echo '" selected="selected'?>">links neben den Balken</option>
   <option value="rechts<?php if($usGrafikWerte=='rechts') echo '" selected="selected'?>">rechts neben den Balken</option>
  </select>
 </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Ergebnisdarstellung</td>
 <td><input type="radio" class="admRadio" name="GrafikProzente<?php if(!$usGrafikProzente) echo '" checked="checked'?>" value="0"> Werte als absolute Zahlen &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="GrafikProzente<?php if($usGrafikProzente) echo '" checked="checked'?>" value="1"> Werte als Prozentangaben</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Teilnehmeranzahl</td>
 <td><input type="checkbox" class="admRadio" name="GrafikTlnAnz<?php if($usGrafikTlnAnz) echo '" checked="checked'?>" value="1"> Teilnehmeranzahl soll angezeigt werden als
 <input type="text" name="TxTeilnehmer" value="<?php echo $usTxTeilnehmer?>" size="15" style="width:10em;" /> <span class="admMini">(Teilnehmer, Meinungen, Klicks o.ä.)</span>
 </td>
</tr>
</table>

<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php echo fSeitenFuss();?>