<?php
include 'hilfsFunktionen.php'; $sM2='';
echo fSeitenKopf('Layouteinstellungen','<script type="text/javascript">
 function BldWin(){bldWin=window.open("about:blank","bld","width=350,height=350,left=9,top=9,menubar=yes,statusbar=yes,toolbar=no,scrollbars=yes,resizable=yes");bldWin.focus();}
 function RechneLayout(){
  var nLayPix=parseInt(document.layoutForm.DivTextWidth.value)+parseInt(document.layoutForm.BildW.value)+parseInt(document.layoutForm.LayoutReservePixel.value);
  if(document.layoutForm.LayoutTyp.value==1) nLayPix=Math.max(parseInt(document.layoutForm.DivTextWidth.value),parseInt(document.layoutForm.BildW.value));
  if(document.layoutForm.LayoutTyp.value==0) nLayPix=parseInt(document.layoutForm.DivTextWidth.value);
  if(document.layoutForm.LayoutTyp.value>1&&document.getElementById("ResponsiveLayout").checked){
   var sLayTxt="Bei Darstellungsbreiten am Endgerät <i>schmaler als "+nLayPix+" Pixel</i> (<span class=\"admMini\">aktuelle Textblockbreite plus Bildbreite plus Layoutreserve</span>) wird auf einspaltige Darstellung umgeschaltet.";
  }else{var sLayTxt="Für die Darstellung <i>Bild und Fragen untereinander</i> muss die Reihenfolge festgelegt werden.";}
  document.getElementById("FolgeBildText").innerHTML=sLayTxt;
  document.getElementById("GrenzeResponsive").innerHTML=nLayPix;
 }
</script>','KLy');

$fraStyle=FRA_CSSDatei; if(!file_exists(FRA_Pfad.$fraStyle)) $fraStyle='fraStyle.css';
if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 if($fraStyle!=FRA_CSSDatei) $sM2.='<p class="admFehl">Die eingetragene CSS-Datei <i>'.FRA_CSSDatei.'</i> ist nicht verfügbar. Ersatzweise wird <i>'.$fraStyle.'</i> verwendet!</p>';
 $fsSchablone=FRA_Schablone; $fsCSSDatei=FRA_CSSDatei; $fsLayoutTyp=FRA_LayoutTyp; $fsLayoutBildText=FRA_LayoutBildText; $fsResponsiveLayout=FRA_ResponsiveLayout;
 $fsDivTextWidth=FRA_DivTextWidth; $fsLayoutReservePixel=FRA_LayoutReservePixel;
 $fsBildKB=FRA_BildKB; $fsBildW=FRA_BildW; $fsBildH=FRA_BildH; $fsBildErsatz=FRA_BildErsatz;
 $fsRadioButton=FRA_RadioButton; $fsZeigeNummer=FRA_ZeigeNummer; $fsNummerStellen=FRA_NummerStellen; $fsNummernTyp=FRA_NummernTyp;
 $fsZeigeKategorie=FRA_ZeigeKategorie; $fsTxKategorie=FRA_TxKategorie; $fsZeigeBemerkung=FRA_ZeigeBemerkung; $fsZeigeNamen=FRA_ZeigeNamen;
 $fsZeigeAntwZahl=FRA_ZeigeAntwZahl; $fsZeigePunkte=FRA_ZeigePunkte; $fsZeigeVersuche=FRA_ZeigeVersuche; $fsAntwortVersuche=FRA_AntwortVersuche;
 $fsSchalterTxZurueck=FRA_SchalterTxZurueck; $fsSchalterTxGeheZu=FRA_SchalterTxGeheZu; $fsSchalter2Zeilen=FRA_Schalter2Zeilen; $fsTxVorFrage=FRA_TxVorFrage;
}elseif($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $bAlleKonf=(isset($_POST['AlleKonf'])&&$_POST['AlleKonf']=='1'?true:false); $sErfo='';
 $sBldV=(isset($_POST['BildErsatz'])?$_POST['BildErsatz']:''); $bErstesMal=true; $bCSSNeu=false;
 foreach($aKonf as $k=>$sKonf) if($bAlleKonf||(int)$sKonf==KONF){
  $sWerte=str_replace("\r",'',trim(implode('',file(FRA_Pfad.'fraWerte'.$sKonf.'.php')))); $bNeu=false;
  $v=txtVar('Schablone'); if(fSetzFraWert($v,'Schablone',"'")) $bNeu=true;
  if($v&&$v!='fraSeite.htm'&&!file_exists(FRA_Pfad.$v)){
   if(is_writeable(FRA_Pfad)){
    if(@copy(FRA_Pfad.'fraSeite.htm',FRA_Pfad.$v)) $sM2.='<p class="admFehl">Die Schablone <i>'.$v.'</i> wurde angelegt. Bitte manuell anpassen!</p>';
    else $sM2.='<p class="admFehl">Die Schablone '.$v.' durfte nicht angelegt werden. Bitte manuell anlegen!</p>';
   }else $sM2.='<p class="admFehl">Die Schablone '.$v.' durfte nicht gespeichert werden. Bitte manuell anlegen!</p>';
  }
  $v=txtVar('CSSDatei'); if(fSetzFraWert($v,'CSSDatei',"'")){$bNeu=true; $fraStyle=$fsCSSDatei;}
  if($v&&$v!='fraStyle.css'&&!file_exists(FRA_Pfad.$v)){
   if(is_writeable(FRA_Pfad)){
    if(@copy(FRA_Pfad.'fraStyle.css',FRA_Pfad.$v)) $sM2.='<p class="admFehl">Die Datei <i>'.$v.'</i> wurde angelegt. Bitte individuell anpassen!</p>';
    else{$sM2.='<p class="admFehl">Die Datei '.$v.' durfte nicht angelegt werden. Bitte manuell anlegen!</p>'; $fraStyle='fraStyle.css';}
   }else{$sM2.='<p class="admFehl">Die Datei '.$v.' durfte nicht gespeichert werden. Bitte manuell anlegen!</p>'; $fraStyle='fraStyle.css';}
  }
  $v=(int)txtVar('LayoutTyp'); if(fSetzFraWert($v,'LayoutTyp','')){$bNeu=true; $bCSSNeu=true;}
  $v=(int)txtVar('LayoutBildText'); if(fSetzFraWert(($v?true:false),'LayoutBildText','')) $bNeu=true;
  $v=(int)txtVar('ResponsiveLayout'); if(fSetzFraWert(($v?true:false),'ResponsiveLayout','')){$bNeu=true; $bCSSNeu=true;}
  $v=max(min((int)txtVar('LayoutReservePixel'),75),0); if(fSetzFraWert($v,'LayoutReservePixel','')){$bNeu=true; $bCSSNeu=true;}
  $v=max((int)txtVar('DivTextWidth'),1); if(fSetzFraWert($v,'DivTextWidth','')){$bNeu=true; $bCSSNeu=true;}
  $v=max((int)txtVar('BildW'),8); if(fSetzFraWert($v,'BildW','')){$bNeu=true; $bCSSNeu=true;}
  $v=max((int)txtVar('BildH'),8); if(fSetzFraWert($v,'BildH','')) $bNeu=true;
  $v=max((int)txtVar('BildKB'),1); if(fSetzFraWert($v,'BildKB','')) $bNeu=true;
  $ImN=str_replace(' ','_',basename($_FILES['BildErsNeu']['name'])); $ImE=strtolower(strrchr($ImN,'.'));//Bildersatz
  if($bErstesMal){
   $bErstesMal=false; if(isset($_POST['BildErsLsch'])&&$_POST['BildErsLsch']=='1') $sBldV='';
   if($ImE=='.jpg'||$ImE=='.gif'||$ImE=='.jpeg'||$ImE=='.png'){
    $i=$_FILES['BildErsNeu']['size'];
    if($i<=$fsBildKB*1024){
     $aIm=@getimagesize($_FILES['BildErsNeu']['tmp_name']);
     if($aIm[0]<=$fsBildW&&$aIm[1]<=$fsBildH){//direkt speichern
      if(copy($_FILES['BildErsNeu']['tmp_name'],FRA_Pfad.FRA_Bilder.$ImN)){
       $sBldV=$ImN; $bNeu=true; $sM2.='<p class="admErfo">Das neue Ersatzbild <i>'.$ImN.'</i> wurde hochgeladen.</p>';
      }else $sM2.='<p class="admFehl">Das Ersatzbild durfte nicht gespeichert werden!</p>';
     }else{//verkleinern
      if($ImE=='.jpg'||$ImE=='.jpeg') $Src=ImageCreateFromJPEG($_FILES['BildErsNeu']['tmp_name']);
      elseif($ImE=='.gif') $Src=ImageCreateFromGIF($_FILES['BildErsNeu']['tmp_name']);
      elseif($ImE=='.png') $Src=ImageCreateFromPNG($_FILES['BildErsNeu']['tmp_name']);
      if($Src){
       $ImN=substr($ImN,0,-strlen($ImE)).'.jpg'; $Sx=ImageSX($Src); $Sy=ImageSY($Src);
       $Dw=min($fsBildW,$Sx); if($Sx>$fsBildW) $Dh=round($fsBildW/$Sx*$Sy); else $Dh=$Sy;
       if($Dh>$fsBildH){$Dw=round($fsBildH/$Dh*$Dw); $Dh=$fsBildH;}
       $Dst=ImageCreateTrueColor($Dw,$Dh); ImageFill($Dst,0,0,ImageColorAllocate($Dst,255,255,255));
       ImageCopyResampled($Dst,$Src,0,0,0,0,$Dw,$Dh,$Sx,$Sy);
       if(ImageJPEG($Dst,FRA_Pfad.FRA_Bilder.$ImN)){
        $sBldV=$ImN; $bNeu=true; $sM2.='<p class="admErfo">Das neue Ersatzbild <i>'.$ImN.'</i> wurde hochgeladen.</p>';
       }else $sM2.='<p class="admFehl">Das Ersatzbild durfte nicht gespeichert werden!</p>';
       imagedestroy($Dst); imagedestroy($Src); unset($Dst); unset($Src);
      }else $sM2.='<p class="admFehl">Das Ersatzbild <i>'.$ImN.'</i> konnte nicht eingelesen werden!</p>';
     }
    }else $sM2.='<p class="admFehl">Ersatzbilder mit <i>'.$i.' KByte</i> Größe sind nicht erlaubt!</p>';
   }elseif(substr($ImE,0,1)=='.') $sM2.='<p class="admFehl">Ersatzbilder mit der Endung <i>'.$ImE.'</i> sind nicht erlaubt!</p>';
   if($bCSSNeu){
    $bCSSNeu=false; $DivW=0; $BldW=0; $BlkW=0;
    $t=str_replace("\n\n\n","\n\n",str_replace("\r",'',trim(implode('',file(FRA_Pfad.$fraStyle)))));
    if($p=strpos($t,'div.fraText')) if($q=strpos($t,'width',$p)) if($e=strpos($t,'}',$p)) if($q<$e){
     if($q=strpos($t,':',$q)) $nAlt=(int)substr($t,$q+1,5);
     if(($nAlt!=$fsDivTextWidth)&&$q){
      if($o=strpos($t,';',$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\r",$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$e;
      $t=substr_replace($t,':'.$fsDivTextWidth.'px',$q,$o-$q); $bCSSNeu=true;
     }
    }
    if($p=strpos($t,'div.fraBild')) if($q=strpos($t,'width',$p)) if($e=strpos($t,'}',$p)) if($q<$e){
     if($q=strpos($t,':',$q)) $nAlt=(int)substr($t,$q+1,5);
     if(($nAlt!=$fsBildW)&&$q){
      if($o=strpos($t,';',$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\r",$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$e;
      $t=substr_replace($t,':'.$fsBildW.'px',$q,$o-$q); $bCSSNeu=true;
     }
    }
    $nBlckW=$fsDivTextWidth; if($fsLayoutTyp==1) $nBlckW=max($nBlckW,$fsBildW); elseif($fsLayoutTyp>1) $nBlckW+=$fsBildW; $nBlckW+=$fsLayoutReservePixel;
    if($p=strpos($t,'div.fraBlock')) if($q=strpos($t,'width',$p)) if($e=strpos($t,'}',$p)) if($q<$e){
     if($q=strpos($t,':',$q)) $nAlt=(int)substr($t,$q+1,5);
     if(($nAlt!=$nBlckW)&&$q){
      if($o=strpos($t,';',$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\r",$q)) if($o>$e) $o=0; if(!$o) if($o=strpos($t,"\n",$q)) if($o>$p) $o=0; if(!$o) $o=$e;
      $t=substr_replace($t,':'.$nBlckW.'px',$q,$o-$q); $bCSSNeu=true;
     }
    }
    $nBlckW+=20; if(!$fsResponsiveLayout) $nBlckW=100;
    if($p=strpos($t,'media screen')) if($q=strpos($t,'max-width',$p)) if($e=strpos($t,')',$p)) if($q<$e){
     if($q=strpos($t,':',$q)) $nAlt=(int)substr($t,$q+1,5);
     if(($nAlt!=$nBlckW)&&$q){
      $t=substr_replace($t,':'.$nBlckW.'px',$q,$e-$q); $bCSSNeu=true;
     }
    }
    if($p=strpos($t,'div.fraBild')) if($q=strpos($t,'float',$p)) if($e=strpos($t,'}',$p)) if($q<$e){
     if($q=strpos($t,':',$q)){while(substr($t,$q+1,1)==' ') $q++; $sAlt=substr($t,++$q,5);}
     $sFloat='none;'; if($fsLayoutTyp==2) $sFloat='left;'; elseif($fsLayoutTyp==3) $sFloat='right;';
     if($sAlt=='none;'||$sAlt=='left;'||$sAlt=='right') if(($sAlt!=$sFloat)&&$q){
      $t=substr_replace($t,$sFloat,$q,5+($sAlt=='right'?1:0)); $bCSSNeu=true;
      if($p=strpos($t,'div.fraText')) if($p=strpos($t,'div.fraText',++$p)) if($q=strpos($t,'float',$p)) if($e=strpos($t,'}',$p)) if($q<$e){
       if($q=strpos($t,':',$q)){while(substr($t,$q+1,1)==' ') $q++; $sAlt=substr($t,++$q,5);}
       $sFloat='none;'; if($fsLayoutTyp==3) $sFloat='left;'; elseif($fsLayoutTyp==2) $sFloat='right;';
       if($sAlt=='none;'||$sAlt=='left;'||$sAlt=='right') if(($sAlt!=$sFloat)&&$q){
        $t=substr_replace($t,$sFloat,$q,5+($sAlt=='right'?1:0));
       }
      }
     }
    }
    if($bCSSNeu){
     if($f=fopen(FRA_Pfad.$fraStyle,'w')){
      fwrite($f,$t); fclose($f);
      $sM2.='<p class="admErfo">Die neue Ausgabebreite wurde in <i>'.$fraStyle.'</i> gespeichert.</p>';
     }else $sM2.='<p class="admFehl">'.str_replace('#',$fraStyle,FRA_TxDateiRechte).'</p>';
    }else $sM2.='<p class="admMeld">Die Style-Datei <i>'.$fraStyle.'</i> wurde nicht geändert!</p>';
   }
  }
  if(fSetzFraWert($sBldV,'BildErsatz',"'")) $bNeu=true;
  $v=(int)txtVar('RadioButton'); if(fSetzFraWert(($v?true:false),'RadioButton','')) $bNeu=true;
  $v=txtVar('ZeigeNummer'); if(fSetzFraWert($v,'ZeigeNummer',"'")) $bNeu=true;
  $v=txtVar('NummerStellen'); if(fSetzFraWert($v,'NummerStellen',"'")) $bNeu=true;
  $v=(int)txtVar('NummernTyp'); if(fSetzFraWert($v,'NummernTyp','')) $bNeu=true;
  $v=txtVar('ZeigeKategorie'); if(fSetzFraWert($v,'ZeigeKategorie',"'")) $bNeu=true;
  $v=txtVar('TxKategorie'); if(fSetzFraWert($v,'TxKategorie',"'")) $bNeu=true;
  $v=txtVar('ZeigeBemerkung'); if(fSetzFraWert($v,'ZeigeBemerkung',"'")) $bNeu=true;
  $v=txtVar('ZeigeNamen'); if(fSetzFraWert($v,'ZeigeNamen',"'")) $bNeu=true;
  $v=txtVar('ZeigeAntwZahl'); if(fSetzFraWert($v,'ZeigeAntwZahl',"'")) $bNeu=true;
  $v=txtVar('ZeigePunkte'); if(fSetzFraWert($v,'ZeigePunkte',"'")) $bNeu=true;
  $v=txtVar('ZeigeVersuche'); if(fSetzFraWert($v,'ZeigeVersuche',"'")) $bNeu=true;
  $v=(int)txtVar('AntwortVersuche'); if(fSetzFraWert($v,'AntwortVersuche',"'")) $bNeu=true;
  $v=txtVar('SchalterTxZurueck'); if(fSetzFraWert($v,'SchalterTxZurueck',"'")) $bNeu=true;
  $v=txtVar('SchalterTxGeheZu'); if(fSetzFraWert($v,'SchalterTxGeheZu',"'")) $bNeu=true;
  $v=(int)txtVar('Schalter2Zeilen'); if(fSetzFraWert(($v?true:false),'Schalter2Zeilen','')) $bNeu=true;
  $v=txtVar('TxVorFrage'); if(fSetzFraWert($v,'TxVorFrage','"')) $bNeu=true;
  if($bNeu){//Speichern
   if($f=fopen(FRA_Pfad.'fraWerte'.$sKonf.'.php','w')){
    fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f); $sErfo.=', '.($sKonf?$sKonf:'0');
   }else $sMeld.='<p class="admFehl">In die Datei <i>fraWerte'.$sKonf.'.php</i> konnte nicht geschrieben werden!</p>';
  }
 }//while
 if($sErfo) $sMeld.='<p class="admErfo">Die Layout-Einstellungen wurden'.($sErfo!=', 0'?' in Konfiguration'.substr($sErfo,1):'').' gespeichert.</p>';
 else $sMeld.='<p class="admMeld">Die Layout-Einstellungen bleiben unverändert.</p>';
}//POST

//Seitenausgabe
if(!$sMeld) $sMeld='<p class="admMeld">Kontrollieren oder ändern Sie die Layouteinstellungen.</p>';
echo $sMeld.$sM2.NL;
?>

<form name="layoutForm" action="konfLayout.php<?php if(KONF>0)echo'?konf='.KONF?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="BildErsatz" value="<?php echo $fsBildErsatz?>" />
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Ausgaben des Testfragen-Scripts werden (sofern das Script eigenständig
und nicht über den PHP-Befehl <i>include</i> eingebettet läuft) in eine umrahmende HTML-Schablonenseite namens <i>fraSeite.htm</i> eingebettet.
Der Name diese Schablone kann auch abweichen, sofern eine alternative Schablone unter diesem Namen bereitgestellt wird.
Im Ausnahmefall kann der Gebrauch dieser umhüllenden Seite unterbleiben.
Dann erfolgt die Ausgabe jedoch ohne die Verwendung von &lt;head&gt; und &lt;body&gt;.
</td></tr>
<tr class="admTabl">
 <td class="admSpa1">HTML-Schablone</td>
 <td><input type="text" name="Schablone" value="<?php echo $fsSchablone?>" style="width:10em" />
 HTML-Umhüllung <i>fraSeite.htm</i> verwenden &nbsp; <span class="admMini">(dringende Empfehlung: verwenden)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">CSS-Style-Datei</td>
 <td><input type="text" name="CSSDatei" value="<?php echo $fsCSSDatei?>" style="width:10em" />
 Standard CSS-Datei <i>fraStyle.css</i> verwenden oder spezielle Datei<?php if(KONF){?> <i>fraStyle<?php echo KONF?>.css</i><?php }?></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen und Antworten können in 4 unterschiedlichen Anordnungen (Layoutvarianten) dargestellt werden.
Jedes Layout kann über die Farbeinstellungen bzw. durch direkte Bearbeitung der CSS-Datei individualisiert werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutvariante</td>
 <td>
  <select id="LayoutTyp" name="LayoutTyp" size="1" onChange="RechneLayout()">
   <option value="0<?php if($fsLayoutTyp<='0') echo '" selected="selected'?>">Layout ohne Bilder (nur Frage, darunter die Antworten)</option>
   <option value="2<?php if($fsLayoutTyp=='2') echo '" selected="selected'?>">Bild links, Frage mit Antworten rechts davon</option>
   <option value="3<?php if($fsLayoutTyp=='3') echo '" selected="selected'?>">Bild rechts, Frage mit Antworten links davon</option>
   <option value="1<?php if($fsLayoutTyp=='1') echo '" selected="selected'?>">Bild und Frage mit Antworten untereinander</option>
  </select>
 </td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2"><span id="FolgeBildText">Für die Darstellung Bild und Fragen untereinander muss die Reihenfolge festgelegt werden.</span>
In welcher Reihenfolge sollen dann Bild und Fragentext untereinander erscheinen?</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutreihenfolge</td>
 <td><input type="radio" class="admRadio" name="LayoutBildText" value="1<?php if($fsLayoutBildText) echo '" checked="checked'?>"> Bild zuerst, Text danach &nbsp; &nbsp;
 <input type="radio" class="admRadio" name="LayoutBildText" value="0<?php if(!$fsLayoutBildText) echo '" checked="checked'?>"> Text zuerst, Bild danach</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Falls die eingestellten Breiten für Textblock und Bild auf schmalen Anzeigegeräten nicht zur Verfügung stehen kann auf diesen Geräten eine automatische Breitenwahl versucht werden (responsive Layout).
Das würde bei den momentanen Werten bei weniger als <span id="GrenzeResponsive">100</span> Pixeln Ausgabebreite (<span class="admMini">aktuelle Textblockbreite plus Bildbreite plus Layoutreserve</span>) auf dem Display erfolgen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutumschaltung</td>
 <td><input type="radio" class="admRadio" id="ResponsiveLayout" name="ResponsiveLayout" value="1<?php if($fsResponsiveLayout) echo '" checked="checked'?>" onchange="RechneLayout()" onclick="RechneLayout()"> ja &nbsp; &nbsp;
 <input type="radio" class="admRadio" id="ResponsiveLayout" name="ResponsiveLayout" value="0<?php if(!$fsResponsiveLayout) echo '" checked="checked'?>" onchange="RechneLayout()" onclick="RechneLayout()"> nein
 <div class="admMini">Hinweis: das responsive Layout verlangt eine HTML-5 Umgebung für das Script. Dazu ist eventuell zuvor die umhüllende Schablone <i>fraSeite.htm</i> zu modernisieren.</div></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Fragen und Antworten werden unabhängig von der Layoutvariante in einem &lt;div&gt;-Block präsentiert.
Die Breite dieses Text-Blocks (ohne das eventuelle Bild) ist einstellbar.
<div class="admMini">Beachten Sie, dass für die eventuelle Darstellung auf mobilen Geräten nur eine begrenzte Anzeigebreite zur Verfügung steht.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Breite&nbsp;des&nbsp;Textblockes<div>mit der Fragezeile und</div>den Antwortzeilen</td>
 <td><input type="text" id="DivTextWidth" name="DivTextWidth" value="<?php echo $fsDivTextWidth?>" size="4" style="width:35px;" onchange="RechneLayout()" onkeyup="RechneLayout()"> Pixel <span class="admMini">(in allen Konfigurationen)</span> &nbsp; Empfehlung: ca. 450<input type="hidden" name="WDiv" value="<?php echo $WDiv?>" />
 <div class="admMini" style="margin-top:5px;">Noch mehr Layouteinstellungen können Sie direkt in der CSS-Datei <a href="konfCss.php<?php if(KONF) echo '?konf='.KONF ?>"><img src="iconAendern.gif" width="12" height="13" border="0" title="CSS-Datei editieren"></a> <i><?php echo $fraStyle ?></i> bearbeiten.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Layoutreserve</td>
 <td><input type="text" id="LayoutReservePixel" name="LayoutReservePixel" value="<?php echo $fsLayoutReservePixel?>" size="4" style="width:35px;" onchange="RechneLayout()" onkeyup="RechneLayout()"> Pixel &nbsp; (<span class="admMini">Empfehlung: ca. 10 bei üblichen Rahmenbreiten</span>)
 <div class="admMini">Wenn Textblock oder Bild eingerahmt sind sollte dieser Faktor mindestens die Summe der Rahmenbreiten mal 2 sein.</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie ein Layout mit Bild verwenden und zu Ihren Fragen auch Bilder hochladen, werden diese beim Hochladen in der Größe skaliert.
Die Maximalwerte sind einstellbar.
<div class="admMini">Beachten Sie, dass für die eventuelle Darstellung auf mobilen Geräten nur eine begrenzte Anzeigebreite zur Verfügung steht.</div></td></tr>
<tr class="admTabl">
 <td class="admSpa1" rowspan="2">Hochladen<div>von Bildern</div></td>
 <td>maximale Dateigröße der Bildquelle im Original <input type="text" name="BildKB" value="<?php echo $fsBildKB?>" size="3" style="width:32px;"> KByte
 <div class="admMini"><u>Hinweis</u>: die wenigsten Server verkraften mehr als 300 KByte problemfrei</div></td>
<tr class="admTabl">
 <td>Bild beim Hochladen verkleinern auf maximal <input type="text" id="BildW" name="BildW" value="<?php echo $fsBildW?>" size="3" style="width:32px;" onchange="RechneLayout()" onkeyup="RechneLayout()"> Pixel Breite
 bzw. maximal <input type="text" name="BildH" value="<?php echo $fsBildH?>" size="3" style="width:32px;"> Pixel Höhe</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie ein Layout mit Bild verwenden und zu einer Fragen ausnahmsweise kein Bild zur Verfügung steht, kann das Ersatzbild verwendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Ersatzbild</td>
 <td><input type="file" name="BildErsNeu" size="90" style="width:99%;"><br>
 <input type="checkbox" class="admCheck" name="BildErsLsch<?php if(empty($fsBildErsatz)) echo '" checked="checked'?>" value="1"> kein Ersatzbild verwenden &nbsp; - &nbsp;
 aktuelles Ersatzbild: <a href="<?php echo 'http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://'.FRA_Www.FRA_Bilder.$fsBildErsatz?>" target="bld" onclick="BldWin()"><img src="iconVorschau.gif" width="13" height="13" border="0" title="Ersatzbild <?php echo $fsBildErsatz?> anzeigen"></a> <i><?php echo $fsBildErsatz?></i></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zum Auswählen der Antworten werden standardmäßig Kontrollkästchen (Checkboxen) verwendet.
Alternativ können bei Fragen mit genau einer richtigen Antwort auch Radioschalter (Radiobuttons) verwendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Radioschalter</td>
 <td><input type="checkbox" class="admCheck" name="RadioButton<?php if($fsRadioButton) echo '" checked="checked'?>" value="1"> Radioschalter statt Checkboxen bei Fragen mit genau einer richtigen Lösung verwenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Über oder unter den Fragen/Antworten kann eine Zusatzzeile mit der Nummer der aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragennummer</td>
 <td><select name="ZeigeNummer" size="1" style="width:330px;">
   <option value="">keine Fragennummer</option>
   <option value="oben<?php if($fsZeigeNummer=='oben') echo '" selected="selected'?>">Fragennummer oberhalb der Frage</option>
   <option value="unten<?php if($fsZeigeNummer=='unten') echo '" selected="selected'?>">Fragennummer unterhalb der Antworten</option>
  </select> &nbsp; im <select name="NummerStellen" size="1">
   <option value="0">natürlichen</option>
   <option value="02<?php if($fsNummerStellen=='02') echo '" selected="selected'?>">2-stelligen</option>
   <option value="03<?php if($fsNummerStellen=='03') echo '" selected="selected'?>">3-stelligen</option>
   <option value="04<?php if($fsNummerStellen=='04') echo '" selected="selected'?>">4-stelligen</option>
   <option value="05<?php if($fsNummerStellen=='05') echo '" selected="selected'?>">5-stelligen</option>
  </select> Format
  <div style="margin-top:3px;"><input type="radio" class="admRadio" name="NummernTyp<?php if($fsNummernTyp==1) echo '" checked="checked'?>" value="1"> als laufende Nummer &nbsp;
  <input type="radio" class="admRadio" name="NummernTyp<?php if($fsNummernTyp==2) echo '" checked="checked'?>" value="2"> als absolute Nummer &nbsp;
  <input type="radio" class="admRadio" name="NummernTyp<?php if($fsNummernTyp==3) echo '" checked="checked'?>" value="3"> als laufende und absolute Nummer</div>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Über oder unter den Fragen/Antworten kann eine Zusatzzeile mit dem Namen des Benutzers/Teilnehmers gezeigt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Namen zeigen</td>
 <td><select name="ZeigeNamen" size="1" style="width:330px;">
   <option value="">keinen Namenszeile</option>
   <option value="oben<?php if($fsZeigeNamen=='oben') echo '" selected="selected'?>">Name oberhalb der Frage</option>
   <option value="unten<?php if($fsZeigeNamen=='unten') echo '" selected="selected'?>">Name unterhalb der Antworten</option>
  </select>
 </td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Sofern Sie Fragenkategorien verwenden kann über oder unter
den Fragen/Antworten eine Zusatzzeile mit der Fragenkategorie der aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fragenkategorie<p>(sofern verwendet)</p></td>
 <td><select name="ZeigeKategorie" size="1" style="width:330px;">
   <option value="">keine Fragenkategorie anzeigen</option>
   <option value="oben<?php if($fsZeigeKategorie=='oben') echo '" selected="selected'?>">Fragenkategorie oberhalb der Frage</option>
   <option value="unten<?php if($fsZeigeKategorie=='unten') echo '" selected="selected'?>">Fragenkategorie unterhalb der Frage</option>
   <option value="info<?php if($fsZeigeKategorie=='info') echo '" selected="selected'?>">Fragenkategorie in der Infozeile unter den Antworten</option>
  </select>
  <div style="margin-top:3px;">vor der Kategorie das folgende Wort anzeigen:
  <input type="text" name="TxKategorie" value="<?php echo $fsTxKategorie?>" size="15" style="width:150px;" /> <span class="admMini">(z.B. <i>Kategorie</i>)</span></div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Zusätzlich zur Frage und den Antworten kann die Anmerkung zur Frage oberhalb, unterhalb oder beim Aufdecken der Lösung eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Anmerkung</td>
 <td><select name="ZeigeBemerkung" size="1">
   <option value="">Anmerkungen nicht anzeigen</option>
   <option value="oben<?php if($fsZeigeBemerkung=='oben') echo '" selected="selected'?>">Anmerkung-1 im Textblock oberhalb der Antworten</option>
   <option value="unten<?php if($fsZeigeBemerkung=='unten') echo '" selected="selected'?>">Anmerkung-1 im Textblock unterhalb der Antworten</option>
   <option value="aufdecken<?php if($fsZeigeBemerkung=='aufdecken') echo '" selected="selected'?>">Anmerkung-1 beim Aufdecken unterhalb des Textblockes</option>
   <option value="selektiv<?php if($fsZeigeBemerkung=='selektiv') echo '" selected="selected'?>">Anmerkung-1 oder 2 beim Aufdecken je nach richtig/falsch</option>
  </select></td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Unter den Auswahlantworten kann eine Information mit der Anzahl der richtigen Antworten zur aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortanzahl</td>
 <td><input type="checkbox" class="admCheck" name="ZeigeAntwZahl<?php if($fsZeigeAntwZahl) echo '" checked="checked'?>" value="1"> einblenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Unter den Auswahlantworten kann eine Information mit der Anzahl der erreichbaren Punkte zur aktuellen Frage eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Punktezahl</td>
 <td><input type="checkbox" class="admCheck" name="ZeigePunkte<?php if($fsZeigePunkte) echo '" checked="checked'?>" value="1"> einblenden</td>
</tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Unter den Auswahlantworten kann eine Information mit der Anzahl der maximal möglich Antwortversuche zu den Fragen eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Antwortversuche</td>
 <td><input type="checkbox" class="admCheck" name="ZeigeVersuche<?php if($fsZeigeVersuche) echo '" checked="checked'?>" value="1"> einblenden und nach
 max. <input type="text" name="AntwortVersuche" value="<?php echo (!empty($fsAntwortVersuche)?$fsAntwortVersuche:'')?>" size="3" style="width:28px;"> Antwortversuchen zur nächsten Frage gehen
 <div class="admMini" style="padding-left:180px;">(<u>Hinweis</u>: leer lassen für beliebig viele Antwortversuche zur Frage)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Unmittelbar vor der Frage (schon direkt in der Fragezeile) kann ein Zusatzbegriff wie &quot;Frage&quot; eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Vorwort<br />vor der Frage</td>
 <td><input type="text" name="TxVorFrage" value="<?php echo $fsTxVorFrage?>" size="90" style="width:99%;">
 <div class="admMini">leer lassen oder z.B. mit BB-Code: [color=navy][b]Frage[/b][/color]:</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Normalerweise gibt es unten auf der Bildschirmseite nur <i>einen</i> Schalter zum Beantworten der Frage und Weitergehen.
Unter Frage und Auswahlantworten kann aber als Ausnahme eine zusätzliche Zeile mit einem weiteren Schalter zum <i>zurück-Blättern</i> und/oder eine Zeile mit einem Schalter mit der Funktion <i>Gehe zu Frage Nr. XX</i> eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sprungschalter</td>
 <td><input type="text" name="SchalterTxZurueck" value="<?php echo $fsSchalterTxZurueck?>" style="width:15em" size="20"> <span class="admMini">Empfehlung: leer lassen bei Nichtverwendung oder&nbsp; <i>eine Seite rückwärts blättern</i></span><br>
 <input type="text" name="SchalterTxGeheZu" value="<?php echo $fsSchalterTxGeheZu?>" style="width:15em" size="20"> <span class="admMini">Empfehlung: leer lassen bei Nichtverwendung oder&nbsp; <i>Gehe zu Frage Nr:</i></span><br>
 <input type="radio" class="admRadio" name="Schalter2Zeilen" value="0<?php if(!$fsSchalter2Zeilen) echo '" checked="checked'?>"> beide Schalter in einer Zeile &nbsp; <input type="radio" class="admRadio" name="Schalter2Zeilen" value="1<?php if($fsSchalter2Zeilen) echo '" checked="checked'?>"> in zwei Zeilen untereinander</td>
</tr>
</table>
<?php if(MULTIKONF){?>
<p class="admSubmit"><input type="radio" name="AlleKonf" value="1<?php if($bAlleKonf)echo'" checked="checked';?>"> für alle Konfigurationen &nbsp; <input type="radio" name="AlleKonf" value="0<?php if(!$bAlleKonf)echo'" checked="checked';?>"> nur für diese Konfiguration<?php if(KONF>0) echo '-'.KONF;?></p>
<?php }?>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<script type="text/javascript">
 RechneLayout();
</script>

<?php echo fSeitenFuss()?>