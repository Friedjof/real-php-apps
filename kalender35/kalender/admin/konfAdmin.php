<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Administratorbereich anpassen','','KAd');

$nTxNr=0; $nTxMax=0; $sATxKz=''; $sATxBt=''; $sATxMt=''; $aTxKz=array(); $aTxBt=array(); $aTxMt=array(); //Alternativtext holen
if(!KAL_SQL){
 $aT=file(KAL_Pfad.KAL_Daten.KAL_AdminTexte); $nTxte=count($aT);
 for($i=1;$i<$nTxte;$i++){
  $s=$aT[$i]; $k=(int)substr($s,0,4); $nTxMax=max($k,$nTxMax);
  if(substr($s,5,1)=='a'){
   $a=explode(';',rtrim($s));
   $aTxKz[$k]=str_replace('`,',';',$a[2]); $aTxBt[$k]=str_replace('`,',';',$a[3]); $aTxMt[$k]=str_replace('`,',';',$a[4]);
  }
 }
}elseif($DbO){ //SQL
 if($rR=$DbO->query('SELECT * FROM '.KAL_SqlTabA.' WHERE typ="a" ORDER BY id')){
  while($a=$rR->fetch_row()){
   $k=(int)$a[0]; $aTxKz[$k]=$a[2]; $aTxBt[$k]=$a[3]; $aTxMt[$k]=str_replace("\n",'\n ',str_replace("\r",'',$a[4]));
  }$rR->close();
 }else $Msg='<p class="admFehl">'.KAL_TxSqlFrage.'</p>';
}

if($_SERVER['REQUEST_METHOD']=='GET'){ //GET
 $amMitLogin=ADM_MitLogin; $amSessionsAgent=ADM_SessionsAgent; $amSessionsIPAddr=ADM_SessionsIPAddr;
 $amAdmin=ADM_Admin; $amPasswort=fKalDeCode(ADM_Passwort); $amBreite=ADM_Breite; $amHilfe=ADM_Hilfe; $amSqlZs=ADM_SqlZs;
 $amAuthLogin=ADM_AuthLogin; $amAuthor=ADM_Author; $amAuthPass=fKalDeCode(ADM_AuthPass); $amAuthCronJob=ADM_AuthCronJob;
 $amRueckwaerts=ADM_Rueckwaerts; $amArchivRueckwaerts=ADM_ArchivRueckwaerts; $amZeigeAltes=ADM_ZeigeAltes;
 $amListenLaenge=ADM_ListenLaenge; $amListenMemoLaenge=ADM_ListenMemoLaenge; $amListenIndex=ADM_ListenIndex;
 $amZusageTrmListe=ADM_ZusageTrmListe; $amListenZusagSp=ADM_ListenZusagSp; $amTCalPicker=ADM_TCalPicker;
 $amZeigeOnline=ADM_ZeigeOnline; $amZeigeOffline=ADM_ZeigeOffline; $amZeigeVormerk=ADM_ZeigeVormerk;
 $amBildVorschau=ADM_BildVorschau; $amDateiSymbol=ADM_DateiSymbol; $amLinkSymbol=ADM_LinkSymbol; $amSymbSymbol=ADM_SymbSymbol;
 $amNutzerLaenge=ADM_NutzerLaenge; $amNutzerFelder=ADM_NutzerFelder; $amDetailInfo=ADM_DetailInfo;
 $amNutzerKontakt=ADM_NutzerKontakt; $amNutzerBetreff=ADM_NutzerBetreff;
}else if($_SERVER['REQUEST_METHOD']=='POST'){ //POST
 $sWerte=str_replace("\r",'',trim(implode('',file(KAL_Pfad.'kalWerte.php')))); $bNeu=false;
 $v=(int)txtVar('MitLogin'); if(setzAdmWert(($v?true:false),'MitLogin','')) $bNeu=true;
 $v=(int)txtVar('SessionsAgent'); if(setzAdmWert(($v?true:false),'SessionsAgent','')) $bNeu=true;
 $v=(int)txtVar('SessionsIPAddr'); if(setzAdmWert(($v?true:false),'SessionsIPAddr','')) $bNeu=true;
 $v=strtolower(txtVar('Admin')); if(setzAdmWert($v,'Admin',"'")) $bNeu=true;
 $v=(isset($_POST['Passwort'])?'#'.trim($_POST['Passwort']):''); $amPasswort=fKalDeCode(ADM_Passwort);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('Passwort'))); if(setzAdmWert(fKalEnCode($v),'Passwort',"'")) $bNeu=true; $amPasswort=$v;
 }elseif(!strpos($Msg,'Administratorpasswort')) $Msg.='<p class="admFehl">Das Administratorpasswort darf kein &quot; oder &gt; enthalten!</p>';
 $v=(int)txtVar('AuthLogin'); if(setzAdmWert(($v?true:false),'AuthLogin','')) $bNeu=true;
 $v=strtolower(txtVar('Author')); if(setzAdmWert($v,'Author',"'")) $bNeu=true;
 $v=(isset($_POST['AuthPass'])?'#'.trim($_POST['AuthPass']):''); $amAuthPass=fKalDeCode(ADM_AuthPass);
 if(!strpos($v,'"')&&!strpos($v,'>')){
  $v=str_replace('/',':',stripslashes(txtVar('AuthPass'))); if(setzAdmWert(fKalEnCode($v),'AuthPass',"'")) $bNeu=true; $amAuthPass=$v;
 }elseif(!strpos($Msg,'Autorenpasswort')) $Msg.='<p class="admFehl">Das Autorenpasswort darf kein &quot; oder &gt; enthalten!</p>';
 $v=(int)txtVar('AuthCronJob'); if(setzAdmWert(($v?true:false),'AuthCronJob','')) $bNeu=true;
 $v=max(min((int)txtVar('Breite'),1800),600); if(setzAdmWert($v,'Breite','')) $bNeu=true;
 $v=txtVar('SqlZs'); if(SetzAdmWert($v,'SqlZs',"'")) $bNeu=true;
 $v=txtVar('Hilfe'); if(substr($v,-1,1)!='/') $v.='/'; if(substr($v,0,8)!='https://'&&substr($v,0,7)!='http://') $v='https://'.$v; if(setzAdmWert(($v),'Hilfe',"'")) $bNeu=true;
 $v=(int)txtVar('ListenIndex'); if(setzAdmWert($v,'ListenIndex','')) $bNeu=true;
 $v=(int)txtVar('Rueckwaerts'); if(setzAdmWert(($v&&$amListenIndex==1?true:false),'Rueckwaerts','')) $bNeu=true;
 $v=(int)txtVar('ArchivRueckwaerts'); if(setzAdmWert(($v&&$amListenIndex==1?true:false),'ArchivRueckwaerts','')) $bNeu=true;
 $v=(int)txtVar('ZeigeAltes'); if(setzAdmWert(($v?true:false),'ZeigeAltes','')) $bNeu=true;
 $v=(int)txtVar('ZeigeOnline'); if(setzAdmWert(($v?true:false),'ZeigeOnline','')) $bNeu=true;
 $v=(int)txtVar('ZeigeOffline'); if(setzAdmWert(($v?true:false),'ZeigeOffline','')) $bNeu=true;
 $v=(int)txtVar('ZeigeVormerk'); if(setzAdmWert(($v?true:false),'ZeigeVormerk','')) $bNeu=true;
 $v=max((int)txtVar('ListenLaenge'),0); if(setzAdmWert($v,'ListenLaenge','')) $bNeu=true;
 $v=max((int)txtVar('ListenMemoLaenge'),0); if(setzAdmWert($v,'ListenMemoLaenge','')) $bNeu=true;
 $v=(int)txtVar('TCalPicker'); if(setzAdmWert(($v?true:false),'TCalPicker','')) $bNeu=true;
 $v=(int)txtVar('ListenZusagSp'); if(setzAdmWert($v,'ListenZusagSp','')) $bNeu=true;
 $v=(int)txtVar('ZusageTrmListe'); if(setzAdmWert(($v?true:false),'ZusageTrmListe','')) $bNeu=true;
 $v=txtVar('BildVorschau');if(setzAdmWert(($v?true:false),'BildVorschau','')) $bNeu=true;
 $v=txtVar('DateiSymbol'); if(setzAdmWert(($v?true:false),'DateiSymbol','')) $bNeu=true;
 $v=txtVar('LinkSymbol');  if(setzAdmWert(($v?true:false),'LinkSymbol','')) $bNeu=true;
 $v=txtVar('SymbSymbol');  if(setzAdmWert(($v?true:false),'SymbSymbol','')) $bNeu=true;
 $v=(int)txtVar('DetailInfo'); if(setzAdmWert($v,'DetailInfo','')) $bNeu=true;
 $v=min(count($kal_NutzerFelder)-2,max((int)txtVar('NutzerFelder'),4)); if(setzAdmWert($v,'NutzerFelder','')) $bNeu=true;
 $v=max((int)txtVar('NutzerLaenge'),1); if(setzAdmWert($v,'NutzerLaenge','')) $bNeu=true;
 $v=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('NutzerKontakt')))); if(setzAdmWert($v,'NutzerKontakt',"'")) $bNeu=true;
 $v=str_replace('  ',' ',txtVar('NutzerBetreff')); if(setzAdmWert($v,'NutzerBetreff','"')) $bNeu=true;
 if($bNeu){ //Speichern
  if($f=fopen(KAL_Pfad.'kalWerte.php','w')){
   fwrite($f,rtrim(str_replace("\n\n\n","\n\n",str_replace("\r",'',$sWerte))).NL); fclose($f);
   $Msg.='<p class="admErfo">Die Administrator-Einstellungen wurden gespeichert.</p>';
  }else $Msg.='<p class="admFehl">In die Datei <i>kalWerte.php</i> konnte nicht geschrieben werden!</p>';
 }

 if($nTxNr=(int)txtVar('ATxNr')){ $bNeu=false;
  $sATxBt=str_replace("'",'´',txtVar('ATxBt')); $sATxMt=str_replace("\n",'\n ',str_replace("\r",'',str_replace("'",'´',txtVar('ATxMt'))));
  if($sATxKz=txtVar('ATxKz')){
   if(!empty($sATxBt)&&!empty($sATxMt)){ //eintragen
    if($nTxNr>0){ //aendern
     if(!KAL_SQL){ //Text
      $sTxNr=sprintf('%04d;',$nTxNr);
      for($i=1;$i<$nTxte;$i++) if(substr($aT[$i],0,5)==$sTxNr){ //gefunden
       $sTxNr.='a;'.str_replace(';','`,',$sATxKz).';'.str_replace(';','`,',$sATxBt).';'.str_replace(';','`,',$sATxMt);
       if(rtrim($aT[$i])!=$sTxNr){$aT[$i]=$sTxNr.NL; $bNeu=true;} break;
      }
     }elseif($DbO){ //SQL
      if($DbO->query('UPDATE IGNORE '.KAL_SqlTabA.' SET kennung="'.$sATxKz.'",betreff="'.$sATxBt.'",inhalt="'.str_replace('\n ',"\r\n",$sATxMt).'" WHERE id='.(int)$nTxNr)){
       if($DbO->affected_rows) $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde geändert.</p>';
      }else $Msg.='<p class="admFehl">MySQL-Speicherfehler beim Alternativtext!</p>';
     }
    }else{ //neu
     if(!KAL_SQL){ //Text
      $aT[]=sprintf('%04d;',++$nTxMax).'a;'.str_replace(';','`,',$sATxKz).';'.str_replace(';','`,',$sATxBt).';'.str_replace(';','`,',$sATxMt).NL;
      $aTxKz[$nTxMax]=$sATxKz; $aTxBt[$nTxMax]=$sATxBt; $aTxMt[$nTxMax]=$sATxMt; $nTxNr=$nTxMax; $bNeu=true;
     }elseif($DbO){ //SQL
      if($DbO->query('INSERT IGNORE INTO '.KAL_SqlTabA.' (typ,kennung,betreff,inhalt) VALUES ("a","'.$sATxKz.'","'.$sATxBt.'","'.str_replace('\n ',"\r\n",$sATxMt).'")')){
       if($nTxMax=$DbO->insert_id){
        $aTxKz[$nTxMax]=$sATxKz; $aTxBt[$nTxMax]=$sATxBt; $aTxMt[$nTxMax]=$sATxMt; $nTxNr=$nTxMax;
        $Msg.='<p class="admErfo">Die neue alternative E-Mail-Vorlage wurde gespeichert.</p>';
       }
       else $Msg.='<p class="admFehl">MySQL-Einfügefehler beim Alternativtext!</p>';
      }else $Msg.='<p class="admFehl">MySQL-Einfügefehler bei Alternativtext!</p>';
     }
    }
    if($bNeu) if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_AdminTexte,'w')){ //bei Text neu schreiben
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aT))).NL); fclose($f);
     $Msg.='<p class="admErfo">Die'.($nTxNr<0?' neuen':'').' alternative E-Mail-Vorlage'.($nTxNr>0?'-'.$nTxNr:'').' wurde gespeichert.</p>';
    }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_AdminTexte.'</i>',KAL_TxDateiRechte).'</p>';
   }else $Msg='<p class="admFehl">Bitte Betreff und Text zum'.($nTxNr<0?' neuen':'').' alternativen Kontakt'.($nTxNr>0?'-'.$nTxNr:'').' angeben!</p>';
  }elseif(empty($sATxBt)&&empty($sATxMt)&&$nTxNr>0){ //loeschen
   if(!KAL_SQL){ //Text
    $sTxNr=sprintf('%04d;',$nTxNr);
    for($i=1;$i<$nTxte;$i++) if(substr($aT[$i],0,5)==$sTxNr){ //gefunden
     $aT[$i]='';
     if($f=@fopen(KAL_Pfad.KAL_Daten.KAL_AdminTexte,'w')){ //neu schreiben
      fwrite($f,rtrim(str_replace("\r",'',implode('',$aT))).NL); fclose($f);
      $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde gelöscht.</p>';
      unset($aTxKz[$nTxNr]); unset($aTxBt[$nTxNr]); unset($aTxMt[$nTxNr]); $nTxNr=0;
     }else $Msg.='<p class="admFehl">'.str_replace('#','<i>'.KAL_Daten.KAL_AdminTexte.'</i>',KAL_TxDateiRechte).'</p>';
    }
   }elseif($DbO){ //SQL
    if($DbO->query('DELETE FROM '.KAL_SqlTabA.' WHERE typ="a" AND id='.(int)$nTxNr)){
     if($DbO->affected_rows) $Msg.='<p class="admErfo">Die alternative E-Mail-Vorlage-'.$nTxNr.' wurde gelöscht.</p>';
    }else $Msg.='<p class="admFehl">MySQL-Löschfehler bei Alternativtext!</p>';
   }
  }else $Msg='<p class="admFehl">Bitte Kontaktkürzel zum'.($nTxNr<0?' neuen':'').' alternativen Kontakt'.($nTxNr>0?'-'.$nTxNr:'').' angeben!</p>';
 }
 if(!$Msg) $Msg='<p class="admMeld">Die Administrator-Einstellungen bleiben unverändert.</p>';
}

//Seitenausgabe
if(!$Msg) $Msg='<p class="admMeld">Kontrollieren oder ändern Sie die Einstellungen für den Administrator-Bereich.</p>';
echo $Msg.NL;

echo "\n<script type=\"text/javascript\">
 var aKz=new Array(); var aBt=new Array(); var aMt=new Array();
 function fATxNr(n){
  document.getElementById('ABtNr').innerHTML=(n>0?n:(n<0?'*':''));
  document.getElementById('AMtNr').innerHTML=(n>0?n:(n<0?'*':''));
  document.getElementById('ATxKz').value=aKz[n];
  document.getElementById('ATxBt').value=aBt[n];
  document.getElementById('ATxMt').value=aMt[n];
 }
 aKz[0]='erst Nr. wählen'; aBt[0]='erst Nr. wählen'; aMt[0]='erst Nr. wählen';";
foreach($aTxKz as $k=>$v){ echo"
 aKz[".$k."]='".$aTxKz[$k]."';
 aBt[".$k."]='".$aTxBt[$k]."';
 aMt[".$k."]='".str_replace('\n ','\n',$aTxMt[$k])."';";
}
echo "\n aKz[-1]=''; aBt[-1]='neuer Betreff'; aMt[-1]='neuer Text....';\n</script>\n";
asort($aTxKz); reset($aTxKz);

$bNutzer=in_array('u',$kal_FeldType)||KAL_NListeAnders||KAL_NDetailAnders||KAL_NEingabeAnders||KAL_NVerstecktSehen; $nFelder=count($kal_FeldName);
$sSortOpt='';
for($i=0;$i<$nFelder;$i++){
 $t=$kal_FeldType[$i];
 if($t!='z'&&$t!='v'&&$t!='l'&&$t!='e'&&$t!='b'&&$t!='x'&&$t!='f'&&$t!='c'&&$t!='p') $sSortOpt.='<option value="'.$i.'"'.($amListenIndex!=$i||$i==1?'':' selected="selected"').'>'.$kal_FeldName[$i].'</option>';
}
?>

<form action="konfAdmin.php" method="post">
<table class="admTabl" border="0" cellpadding="2" cellspacing="1">
<tr class="admTabl"><td colspan="2" class="admSpa2">Der Zugangsschutz zur gesamten Administration sollte bevorzugt serverseitig erfolgen.
Im Kundenmenü nahezu jedes heutigen Servers findet sich ein Menüpunkt wie <i>Zugangsschutz</i>, <i>geschütze Ordner</i> o.ä.
Alternativ dazu läßt sich der Zugangsschutz oft über eine .htaccess-Datei im Admin-Ordner einrichten.
<p>Auch könnten Sie dem Administrations-Unterordner einen anderen Namen als /admin/ geben, um zufällige Besuche zu verhindern und die Sicherheit zu erhöhen.</p></td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Sollte ein serverseitiger Schutz des Admin-Ordners auf Ihrem Server nicht möglich oder nicht gewollt sein,
können Sie den scriptseitigen Zugangs-Schutz zur Administration einschalten.
Dieser scriptseitige Schutz bietet <i>nicht</i> die selbe hohe Sicherheit wie der serverseitige Schutz.
Ausserdem müssen Sie hierfür Sitzungs-Cookies in Ihrem Browser zulassen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>der Administration</td>
 <td><input type="radio" class="admRadio" name="MitLogin" value=""<?php if(!$amMitLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="MitLogin" value="1"<?php if($amMitLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Administrator <input type="text" name="Admin" value="<?php echo $amAdmin?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="Passwort" value="<?php echo $amPasswort?>" style="width:10em" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Sitzungsüberwachung</td>
 <td><div><input type="checkbox" class="admRadio" name="SessionsAgent" value="1"<?php if($amSessionsAgent) echo ' checked="checked"'?> /> Browserkennung überwachen</div>
 <div><input type="checkbox" class="admRadio" name="SessionsIPAddr" value="1"<?php if($amSessionsIPAddr) echo ' checked="checked"'?> /> IP-Adresse überwachen</div>
 <div class="admMini">ausschalten verringert die Sicherheit bei scriptseitigem Zugangsschutz zur Administration</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Auch für den Autorenbereich im entsprechenden Unterordner kann bei dessen Verwendung ein scriptseitiger Zugangsschutz eingerichtet werden.
Allerdings ist auch hier der serverseitige Schutz die zu bevorzugende Variante.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">scriptseitiger Schutz<br>des Autorenbereichs</td>
 <td><input type="radio" class="admRadio" name="AuthLogin" value=""<?php if(!$amAuthLogin) echo ' checked="checked"'?> /> ausgeschaltet &nbsp; <input type="radio" class="admRadio" name="AuthLogin" value="1"<?php if($amAuthLogin) echo ' checked="checked"'?> /> eingeschaltet
 <div class="admMini">Empfehlung: <i>ausgeschaltet</i> und einen serverseitigen Schutz organisieren</div>
 Autorname <input type="text" name="Author" value="<?php echo $amAuthor?>" style="width:10em" /> &nbsp;
 Passwort <input type="password" name="AuthPass" value="<?php echo $amAuthPass?>" style="width:10em" /></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Autorenberechtigung</td>
 <td><input type="checkbox" class="admCheck" name="AuthCronJob" value="1"<?php if($amAuthCronJob) echo ' checked="checked"'?> /> Autoren dürfen den Cron-Job <a href="<?php echo KALPFAD?>kalCronJob.php?kal=<?php echo KAL_Schluessel?>" target="hilfe" onclick="hlpWin(this.href);return false;" title="bearbeiten"><img src="<?php echo KALPFAD?>grafik/icon_Aendern.gif" width="12" height="13" border="0" alt="aufrufen"> kalCronJob.php</a> per Link von Hand aufrufen.</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die folgenden Einstellungen für den Administratorbereich gelten genauso für den Autorenbereich, falls dieser benutzt wird.</td></tr>
<tr class="admTabl"><td colspan="2" class="admSpa2">Die Anzeigebreite der Administratorseiten kann auf Ihren Bildschirm abgestimmt werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Fensterbreite der<br>Administration</td>
 <td><input type="text" name="Breite" value="<?php echo $amBreite?>" style="width:4em" /> Pixel &nbsp;
 <span class="admMini">Empfehlung: nicht unter 920</span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Administrationsseiten greifen bei Verwendung der MySQL-Datenbank statt der Textdatenbank auf diese mit dem Standard-Zeichensatz zu, d.h. ohne extra einen bestimmten Zeichensatz einzustellen.
Falls Ihre Serverumgebung es aus irgendwelchen Gründen erfordert, können Sie für die MySQL-Verbindung der Administration auch einen anderen Zeichensatz erzwingen.
Das entspricht einem MySQL-Befehl <i>mysqli_set_charset()</i> für jede SQL-Verbindung in der Administration.</td></tr>
<tr class="admTabl">
 <td style="white-space:nowrap">MySQL-Zeichensatz<br>der Administration</td>
 <td><input type="text" name="SqlZs" value="<?php echo $amSqlZs;?>" size="10" /> <span class="admMini">Empfehlung: meist <i>leer lassen</i>, nur notfalls <i>latin1</i> aber eigentlich fast nie <i>utf8</i> bzw. <i>utf8mb4</i></span></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die für die Administrationsseiten verwendete online-Hilfe <i>LiesMich.htm</i> liegt in folgendem Verzeichnis:</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Pfad zur<br>online-Hilfe</td>
 <td><input type="text" name="Hilfe" value="<?php echo $amHilfe?>" style="width:100%" />
 <div class="admMini">aktuell: <i>https://www.server-scripts.de/kalender/</i> &nbsp; &nbsp; &nbsp; (zur <a class="admMini" href="<?php echo $amHilfe?>LiesMich.htm" target="hilfe" onclick="hlpWin(this.href);return false;"><i>Hilfe-Datei</i></a>)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Normalerweise wird die Terminliste für Administratoren/Autoren chronologisch nach aufsteigendem Datum (spätere Termine unten) sortiert.
Abweichend kann die Terminliste für Administratoren/Autoren standardmäßig auch nach einem anderen Feld oder absteigender Sortierfolge (spätere Termine oben) geordnet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Sortierfeld</td>
 <td><select name="ListenIndex"><option value="1">Standard</option><?php echo $sSortOpt;?></select> <span class="admMini">Empfehlung: Standard (nach Datum)</span></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listensortierung</td>
 <td><input type="radio" class="admRadio" name="Rueckwaerts" value=""<?php if(!$amRueckwaerts) echo ' checked="checked"'?> /> vorwärts &nbsp; <input type="radio" class="admRadio" name="Rueckwaerts" value="1"<?php if($amRueckwaerts) echo ' checked="checked"'?> /> rückwärts</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Archivsortierung</td>
 <td><input type="radio" class="admRadio" name="ArchivRueckwaerts" value=""<?php if(!$amArchivRueckwaerts) echo ' checked="checked"'?> /> vorwärts &nbsp; <input type="radio" class="admRadio" name="ArchivRueckwaerts" value="1"<?php if($amArchivRueckwaerts) echo ' checked="checked"'?> /> rückwärts</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">abgelaufene Termine</td>
 <td><input type="radio" class="admRadio" name="ZeigeAltes" value="1"<?php if($amZeigeAltes) echo ' checked="checked"'?> /> anzeigen &nbsp; <input type="radio" class="admRadio" name="ZeigeAltes" value=""<?php if(!$amZeigeAltes) echo ' checked="checked"'?> /> ausblenden und nur als Archiv anzeigen</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Terminarten in<br />der Terminliste<br />des Administrators</td>
 <td><input type="checkbox" class="admRadio" name="ZeigeOnline" value="1"<?php if($amZeigeOnline) echo ' checked="checked"'?> /> online-Termine anzeigen
 <div><input type="checkbox" class="admRadio" name="ZeigeOffline" value="1"<?php if($amZeigeOffline) echo ' checked="checked"'?> /> offline-Termine anzeigen</div>
 <div><input type="checkbox" class="admRadio" name="ZeigeVormerk" value="1"<?php if($amZeigeVormerk) echo ' checked="checked"'?> /> Vormerk-Termine anzeigen</div>
 <div class="admMini">Hinweis: Diese Terminarten werden in der Liste als Standard ausgegeben. Abweichende Ausgaben sind über das Termin-Suchformular einstellbar.</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge<br />des Administrators</td>
 <td><input type="text" name="ListenLaenge" value="<?php echo $amListenLaenge?>" size="2" /> Terminzeilen auf einer Listenseite der Terminübersicht
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Termine pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Zusagenspalte</td>
 <td><input class="admCheck" type="checkbox" name="ZusageTrmListe" value="1<?php if($amZusageTrmListe) echo '" checked="checked'?>" /> Zusagenspalte <img src="<?php echo KALPFAD?>grafik/icon_Lupe.gif" width="12" height="13" border="0" title="<?php echo KAL_TxZeigeZusageIcon ?>"> in der Terminliste des Administrators einblenden &nbsp; <span class="admMini">Empfehlung: aktivieren</span></td>
</tr>

<tr class="admTabl">
 <td class="admSpa1">Zusagensumme</td>
 <td>Zusagensummenspalte vor Spalte <select name="ListenZusagSp" size="1"><option value="-1">--</option><?php for($i=1;$i<$nFelder;$i++) echo '<option value="'.$i.'"'.($amListenZusagSp==$i?' selected="selected"':'').'>'.$i.'</option>'?></select> in der Administrator-Terminliste zusätzlich einblenden</td>
</tr>


<tr class="admTabl">
 <td class="admSpa1">Bildvorschau</td>
 <td><input type="radio" class="admRadio" name="BildVorschau" value="1"<?php if($amBildVorschau) echo ' checked="checked"'?> /> Bilder als Vorschaubild &nbsp; <input type="radio" class="admRadio" name="BildVorschau" value=""<?php if(!$amBildVorschau) echo ' checked="checked"'?> /> nur als Dateiname</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Dateianhänge</td>
 <td><input type="radio" class="admRadio" name="DateiSymbol" value="1"<?php if($amDateiSymbol) echo ' checked="checked"'?> /> Dateianhänge als Symbol &nbsp; <input type="radio" class="admRadio" name="DateiSymbol" value=""<?php if(!$amDateiSymbol) echo ' checked="checked"'?> /> als Dateiname</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Linkdarstellung</td>
 <td><input type="radio" class="admRadio" name="LinkSymbol" value=""<?php if(!$amLinkSymbol) echo ' checked="checked"'?> /> Links und E-Mails sichtbar ausgeschrieben &nbsp; <input type="radio" class="admRadio" name="LinkSymbol" value="1"<?php if($amLinkSymbol) echo ' checked="checked"'?> /> nur als Klick-Symbol</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Symbolfelder</td>
 <td><input type="radio" class="admRadio" name="SymbSymbol" value=""<?php if(!$amSymbSymbol) echo ' checked="checked"'?> /> Werte von Symbolfeldern ausgeschrieben &nbsp; <input type="radio" class="admRadio" name="SymbSymbol" value="1"<?php if($amSymbSymbol) echo ' checked="checked"'?> /> nur als Symbol</td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Memofeldlänge<br />abschneiden</td>
 <td><input type="text" name="ListenMemoLaenge" value="<?php echo $amListenMemoLaenge?>" size="2" /> in der Terminliste für Administratoren/Autoren nach soviel Zeichen abschneiden....
 <div class="admMini">Empfehlung: 50 oder 80 oder 0 für ungekürzt</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Datums- und<br>Zeitpicker</td>
 <td><input class="admCheck" type="checkbox" name="TCalPicker" value="1<?php if($amTCalPicker) echo '" checked="checked'?>" /> Datums- und Zeitpicker verwenden</td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Auf den Seiten der Termindetails kann für den Administrator eine Zusatzzeile
mit der Funktion <i>Information versenden</i> (<i>sag's einem Freund</i>) eingeblendet werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Infofunktion</td>
 <td>zusätzliche Infozeile vor Zeile <select name="DetailInfo" size="1"><option value="-1">--</option><?php for($i=0;$i<=$nFelder;$i++) echo '<option value="'.$i.'"'.($amDetailInfo==$i?' selected="selected"':'').'>'.$i.'</option>'?></select></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Die Liste der Benutzerübersicht im Administrationsbereich kann individuell angepasst werden.<?php if(!$bNutzer){?> <span style="color:#771100;">Die Benutzerverwaltung ist ohne ein Feld vom Typ <i>Benutzer</i> in der Terminstruktur jedoch momentan inaktiv.</span><?php }?></td></tr>
<tr class="admTabl">
 <td class="admSpa1">Listenlänge</td>
 <td><input type="text" name="NutzerLaenge" value="<?php echo $amNutzerLaenge?>" size="2"<?php if(!$bNutzer) echo ' style="color:#8C8C8C;"'?> /> Zeilen mit Benutzerdaten auf einer Listenseite der Benutzerübersicht
 <div class="admMini">Empfehlung: 25 oder 10 oder 50 Termine pro Seite</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Spaltenanzahl</td>
 <td><input type="text" name="NutzerFelder" value="<?php echo $amNutzerFelder?>" size="2"<?php if(!$bNutzer) echo ' style="color:#8C8C8C;"'?> /> Felder in der Tabelle der Benutzerübersicht
 <div class="admMini">Empfehlung: die ersten 5...7 Felder der Benutzerdaten sind meist ausreichend</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Der Administrator kann aus der Benutzerübersicht heraus mit den Benutzern per E-Mail Kontakt aufnehmen.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerbetreff</td>
 <td><input style="width:100%" type="text" name="NutzerBetreff" value="<?php echo $amNutzerBetreff?>" /><div class="admMini">(Dieser Text wird als überschreibbarer Betreff für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">Benutzerkontakt</td>
 <td><textarea name="NutzerKontakt" cols="80" rows="8" style="height:9em;"><?php echo str_replace('\n ',"\n",$amNutzerKontakt)?></textarea><div class="admMini">(Dieser Text wird als überschreibbare Standardvorlage für E-Mails an die Benutzer verwendet.)</div></td>
</tr>

<tr class="admTabl"><td colspan="2" class="admSpa2">Für umfangreichere Kommunikation können alternative E-Mail-Vorlagetexte bereitgehalten werden.</td></tr>
<tr class="admTabl">
 <td class="admSpa1">Kontaktkürzel</td>
 <td><select name="ATxNr" size="1" onchange="fATxNr(this.value)"><option value="0">--</option><option value="-1<?php if($nTxNr<0) echo '" selected="selected';?>">0000: *NeuText</option><?php foreach($aTxKz as $k=>$v) echo '<option value="'.$k.($nTxNr!=$k?'':'" selected="selected').'">'.sprintf('%04d: ',$k).$v.'</option>'?></select> &nbsp;
 <input style="width:10em" type="text" name="ATxKz" id="ATxKz" value="<?php echo ($nTxNr!=0?$sATxKz:'erst Nr. wählen')?>" /> </td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br>Benutzerbetreff <span id="ABtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td><input style="width:100%" type="text" name="ATxBt" id="ATxBt" value="<?php echo ($nTxNr!=0?$sATxBt:'erst Nr. wählen')?>" /><div class="admMini">(Dieser Text wird als überschreibbarer Betreff für E-Mails an die Benutzer verwendet.)</div></td>
</tr>
<tr class="admTabl">
 <td class="admSpa1">alternativer<br>Benutzerkontakt <span id="AMtNr"><?php if($nTxNr>0) echo $nTxNr?></span></td>
 <td><textarea name="ATxMt" id="ATxMt" cols="80" rows="8" style="height:9em;"><?php echo ($nTxNr!=0?str_replace('\n ',"\n",$sATxMt):'erst Nr. wählen')?></textarea>
 <div class="admMini">(Dieser Text wird als überschreibbare Standardvorlage für E-Mails an die Benutzer verwendet.)</div>
 <div class="admMini" style="margin-top:6px">Zum Löschen einer Alternativvorlage sind sowohl das Kürzel als auch der Betreff und Text zu leeren.</div></td>
</tr>

</table>
<p class="admSubmit"><input class="admSubmit" type="submit" value="Eintragen"></p>
</form>

<?php
echo fSeitenFuss();

function setzAdmWert($w,$n,$t){
 global $sWerte, ${'am'.$n}; ${'am'.$n}=$w;
 if($w!=constant('ADM_'.$n)){
  $p=strpos($sWerte,'ADM_'.$n."',"); $e=strpos($sWerte,');',$p);
  if($p>0&&$e>$p){//Zeile gefunden
   $sWerte=substr_replace($sWerte,'ADM_'.$n."',".$t.(!is_bool($w)?$w:($w?'true':'false')).$t,$p,$e-$p); return true;
  }else return false;
 }else return false;
}
?>