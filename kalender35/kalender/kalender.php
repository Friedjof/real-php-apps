<?php
error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_OFF);
$sKalSelf=(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'./kalender.php')));
$sKalHttp='http'.(!isset($_SERVER['SERVER_PORT'])||$_SERVER['SERVER_PORT']!='443'?'':'s').'://';
$bKalPopup=isset($_GET['kal_Popup'])||isset($_POST['kal_Popup']);
$sKalHtmlVor=''; $sKalHtmlNach=''; $bKalOK=true; $GLOBALS['oKalDbO']=NULL; //Seitenkopf, Seitenfuss, Status

if(!strstr($sKalSelf,'/kalender.php')){ //includierter Aufruf
 if(!defined('KAL_Version')){ //Variablen nicht includiert
  $bKalOK=false; echo "\n".'<p style="color:red;"><b>Konfiguration <i>kalWerte.php</i> wurde nicht includiert!</b></p>';
 }
}else{ //Script laeuft allein als kalender.php
 @include('kalWerte.php');
 if(defined('KAL_Version')){
  header('Content-Type: text/html; charset='.(KAL_Zeichensatz!=2?'ISO-8859-1':'utf-8'));
  if(KAL_Schablone){ //mit Seitenschablone
   $bKalDruck=isset($_GET['kal_Aktion'])&&substr($_GET['kal_Aktion'],0,5)=='druck'; $bKalFrei=false;
   if(isset($_GET['kal_Aktion'])&&(substr($_GET['kal_Aktion'],0,1)=='o'||substr($_GET['kal_Aktion'],0,7)=='zusage_')||isset($_POST['kal_Aktion'])&&(substr($_POST['kal_Aktion'],0,1)=='o'||substr($_POST['kal_Aktion'],0,7)=='zusage_')) if(KAL_FreischaltWin=='Freischalt'){$bKalFrei=true; $bKalPopup=true;}elseif(KAL_FreischaltWin=='Popup') $bKalPopup=true;
   $sKalHtmlVor=(!$bKalFrei?(!$bKalDruck?(!$bKalPopup?'kalSeite.htm':'kalPopup.htm'):'kalDruck.htm'):'kalFreischalt.htm');
   $sKalHtmlNach=(file_exists(KAL_Pfad.$sKalHtmlVor)?implode('',file(KAL_Pfad.$sKalHtmlVor)):'');
   if($nKalJ=strpos($sKalHtmlNach,'{Inhalt}')){
    $sKalHtmlVor=substr($sKalHtmlNach,0,$nKalJ); $sKalHtmlNach=substr($sKalHtmlNach,$nKalJ+8); //Seitenkopf, Seitenfuﬂ
   }else{$sKalHtmlVor='<p style="color:#AA0033;">HTML-Layout-Schablone <i>'.$sKalHtmlVor.'</i> nicht gefunden oder fehlerhaft!</p>'; $sKalHtmlNach='';}
  }else{ //ohne Seitenschablone
   echo "\n\n".'<link rel="stylesheet" type="text/css" href="'.$sKalHttp.KAL_Www.'kalStyles.css">'."\n\n";
  }
 }else{$bKalOK=false; echo "\n".'<p style="color:red;">Konfiguration <i>kalWerte.php</i> nicht gefunden oder fehlerhaft!</p>';}
}
if($bKalOK){ //Konfiguration eingelesen
 if(!KAL_WarnMeldungen) error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
 if(phpversion()>='5.1.0') if(strlen(KAL_TimeZoneSet)>0) date_default_timezone_set(KAL_TimeZoneSet);
 if(!defined('KAL_Url')) define('KAL_Url',$sKalHttp.KAL_Www); define('KAL_Self',$sKalSelf);
 //geerbte GET/POST-Parameter aufbewahren und einige Kalenderparameter ermitteln
 $sKalQry=''; $sKalHid=''; $sKalSession=''; $sKalZentrum=''; $sKalSuchParam=''; $sKalIndex=''; $sKalRueck=''; $sKalStart=''; $sKalMonat=''; $sKalWoche=''; $sKalNummer=''; $sKalSort=''; $sKalAbst=''; $sKalFilt='';
 if($_SERVER['REQUEST_METHOD']!='POST'){ //bei GET
  if(isset($_GET['kal_Aktion'])) $sKalAktion=fKalRq1($_GET['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_GET['kal_Session'])&&$sKalAktion!='login') $sKalSession='&amp;kal_Session='.fKalRq1($_GET['kal_Session']);
  if(isset($_GET['kal_Zentrum'])) $sKalZentrum='&amp;kal_Zentrum=1';
  if(isset($_GET['kal_Index'])) $sKalIndex='&amp;kal_Index='.fKalRq1($_GET['kal_Index']);
  if(isset($_GET['kal_Rueck'])) $sKalRueck='&amp;kal_Rueck='.fKalRq1($_GET['kal_Rueck']);
  if(isset($_GET['kal_Monat'])) if($_GET['kal_Monat']>'0') $sKalMonat='&amp;kal_Monat='.fKalRq1($_GET['kal_Monat']);
  if(isset($_GET['kal_Woche'])) if($_GET['kal_Woche']>'0') $sKalWoche='&amp;kal_Woche='.fKalRq1($_GET['kal_Woche']);
  if(isset($_GET['kal_Start'])) if($_GET['kal_Start']>'1') $sKalStart='&amp;kal_Start='.fKalRq1($_GET['kal_Start']);
  if(isset($_GET['kal_Nummer'])) $sKalNummer='&amp;kal_Nummer='.fKalRq1($_GET['kal_Nummer']);
  if(isset($_GET['kal_Sort'])) $sKalSort='&amp;kal_Sort='.fKalRq1($_GET['kal_Sort']);
  if(isset($_GET['kal_Abst'])) $sKalAbst='&amp;kal_Abst='.fKalRq1($_GET['kal_Abst']);
  if(isset($_GET['kal_Filter'])&&$_GET['kal_Filter']>'0') $sKalFilt='&amp;kal_Filter='.(int)fKalRq1($_GET['kal_Filter']);
  if(isset($_GET['kal_ZSuch'])) $sKalSuchParam='&amp;kal_ZSuch='.rawurlencode(fKalRq($_GET['kal_ZSuch']));
  reset($_GET);
  if(!defined('KAL_Query')) foreach($_GET as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_'){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }elseif(strrpos($sKalK,'F')>4||$sKalK=='kal_Intervall') $sKalSuchParam.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
 }else{ //bei POST
  if(isset($_POST['kal_Aktion'])) $sKalAktion=fKalRq1($_POST['kal_Aktion']); else $sKalAktion='liste';
  if(isset($_POST['kal_Session'])&&$_POST['kal_Session']!='') $sKalSession='&amp;kal_Session='.fKalRq1($_POST['kal_Session']);
  if(isset($_POST['kal_Zentrum'])&&$_POST['kal_Zentrum']!='') $sKalZentrum='&amp;kal_Zentrum=1';
  if(isset($_POST['kal_Index'])) $sKalIndex='&amp;kal_Index='.fKalRq1($_POST['kal_Index']);
  if(isset($_POST['kal_Rueck'])) $sKalRueck='&amp;kal_Rueck='.fKalRq1($_POST['kal_Rueck']);
  if(isset($_POST['kal_Monat'])) if($_POST['kal_Monat']>'0') $sKalMonat='&amp;kal_Monat='.fKalRq1($_POST['kal_Monat']);
  if(isset($_POST['kal_Woche'])) if($_POST['kal_Woche']>'0') $sKalWoche='&amp;kal_Woche='.fKalRq1($_POST['kal_Woche']);
  if(isset($_POST['kal_Start'])) if($_POST['kal_Start']>'1') $sKalStart='&amp;kal_Start='.fKalRq1($_POST['kal_Start']);
  if(isset($_POST['kal_Nummer'])) $sKalNummer='&amp;kal_Nummer='.fKalRq1($_POST['kal_Nummer']);
  if(isset($_POST['kal_Sort'])&&$_POST['kal_Sort']) $sKalSort='&amp;kal_Sort='.fKalRq1($_POST['kal_Sort']);
  if(isset($_POST['kal_Abst'])&&$_POST['kal_Abst']) $sKalAbst='&amp;kal_Abst='.fKalRq1($_POST['kal_Abst']);
  if(isset($_POST['kal_Filter'])&&$_POST['kal_Filter']>'0') $sKalFilt='&amp;kal_Filter='.(int)fKalRq1($_POST['kal_Filter']);
  if(isset($_POST['kal_ZSuch'])) $sKalSuchParam='&amp;kal_ZSuch='.rawurlencode(fKalRq($_POST['kal_ZSuch']));
  reset($_POST); // $sKalQS=$_SERVER['QUERY_STRING']; ????
  if(!defined('KAL_Query')) foreach($_POST as $sKalK=>$sKalV) if(substr($sKalK,0,4)!='kal_') if(is_string($sKalV)){
   $sKalQry.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
   $sKalHid.='<input type="hidden" name="'.$sKalK.'" value="'.$sKalV.'">';
  }elseif((strrpos($sKalK,'F')>4&&!strpos($sKalK,'Captcha'))||$sKalK=='kal_Intervall') $sKalSuchParam.='&amp;'.$sKalK.'='.rawurlencode($sKalV);
 }
 if(!defined('KAL_Query')) define('KAL_Query',$sKalQry); if(!defined('KAL_Hidden')) define('KAL_Hidden',$sKalHid);
 if(!defined('KAL_Session')) define('KAL_Session',$sKalSession.$sKalZentrum); define('KAL_Popup',$bKalPopup);
 if(!defined('KAL_Aktion')) define('KAL_Aktion',$sKalAktion);

 //Aktionen - Programmverteiler
 $sKalAendAktion='liste'; $sKalAendParam=(KAL_ListenAendern<0||(!KAL_GastLAendern&&$sKalSession=='')?'&amp;kal_Aendern=1':'');
 $sKalAendParam.=(KAL_ListenAendKopieren&&(KAL_ListenKopieren<0||(!KAL_GastLKopieren&&$sKalSession==''))?'&amp;kal_Kopieren=1':'');
 switch($sKalAktion){
  case 'liste': include(KAL_Pfad.'kalDaten.php'); fKalDaten(true,true); include(KAL_Pfad.'kalListe.php'); break; //Terminliste
  case 'monat': include(KAL_Pfad.'kalMonat.php'); $sKalMonat='&amp;kal_Monat='.fKalInitMon(); break; //Terminliste Mo
  case 'woche': include(KAL_Pfad.'kalWoche.php'); $sKalWoche='&amp;kal_Woche='.fKalInitWoc(); break; //Terminliste Wo
  case 'detail':
   include(KAL_Pfad.'kalDaten.php'); fKalDaten(false,true); include(KAL_Pfad.'kalDetail.php');
   $sKalAendAktion='aendern'; $sKalAendParam=$sKalNummer; break; //Detail
  case 'suche': include(KAL_Pfad.'kalSuche.php'); break; //Suchformular
  case 'eingabe': include(KAL_Pfad.'kalEingabe.php'); break; //Termineingabe
  case 'aendern': include(KAL_Pfad.'kalAendern.php'); break; //Termin‰nderungen
  case 'kopieren': include(KAL_Pfad.'kalKopieren.php'); break; //Terminkopie
  case 'druck': include(KAL_Pfad.'kalDaten.php'); fKalDaten(true,false); include(KAL_Pfad.'kalDruck.php'); break; // ListenDruck
  case 'druckmonat': include(KAL_Pfad.'kalDruckMonat.php'); break; // Monatsdruck
  case 'druckwoche': include(KAL_Pfad.'kalDruckWoche.php'); break; // Wochendruck
  case 'drucken': include(KAL_Pfad.'kalDrucken.php'); break; // Detaildruck
  case 'kontakt': include(KAL_Pfad.'kalKontakt.php'); break; // Klick auf E-Mail-Feld
  case 'info': include(KAL_Pfad.'kalInfo.php'); break; // tell-a-friend-Funktion
  case 'erinnern': include(KAL_Pfad.'kalErinnern.php'); break; // Erinnerungs-Service
  case 'nachricht': include(KAL_Pfad.'kalNachricht.php'); break; // Benachrichtigungs-Service
  case 'zusagen': include(KAL_Pfad.'kalZusageEintrag.php'); break; // Zusage-Eintrag
  case 'zusagezeigen': include(KAL_Pfad.'kalZusageZeigen.php'); break; // Zusagen-Liste
  case 'nzentrum': include(KAL_Pfad.'kalNZentrum.php'); break; // Benutzerzentrum
  case 'nerinn': include(KAL_Pfad.'kalNErinn.php'); break; // Erinnerungsliste
  case 'nbenachr': include(KAL_Pfad.'kalNNachr.php'); break; // Benachrichtigungsliste
  case 'export': include(KAL_Pfad.'kalExport.php'); break; // iCal-Export
  case 'nzusagenfliste': include(KAL_Pfad.'kalZusageLstFrmd.php'); break; // fremde Zusagenliste
  case 'nzusageneliste': include(KAL_Pfad.'kalZusageLstEign.php'); break; // eigene Zusagenliste
  case 'nzusageaendern': include(KAL_Pfad.'kalZusageAendern.php'); break; // Zusage aendern
  case 'nzusagekontakt': include(KAL_Pfad.'kalZusageKontakt.php'); break; // alle Zusager ansprechen
  case 'druckfzusagen': include(KAL_Pfad.'kalZusageDrckFrmd.php'); break; // fremde Zusagen drucken
  case 'druckezusagen': include(KAL_Pfad.'kalZusageDrckEign.php'); break; // eigene Zusagen drucken
  case 'ndaten': include(KAL_Pfad.'kalNDaten.php'); break; // Benutzerdaten
  case 'login': include(KAL_Pfad.'kalLogin.php'); if($sKalV=fKalTestLogin()) $sKalSession=$sKalV; break; // Benutzerlogin
  default:
   if(substr($sKalAktion,0,1)=='o') include(KAL_Pfad.'kalFreischalt.php'); //Freischaltung
   elseif(substr($sKalAktion,0,7)=='zusage_') include(KAL_Pfad.'kalZusageFreigabe.php'); //ZusageFreigabe
   else include(KAL_Pfad.'kalKeineAktion.php');
 }

 if(defined('KAL_SuchParam')){ //kommt von fKalDaten()
  $sKalSuchParam=KAL_SuchParam;
  if($nKalJ=strpos($sKalSuchParam,'kal_Intervall=[]')) $sKalSuchParam=substr_replace($sKalSuchParam,'',$nKalJ-5,21);
  elseif($nKalJ=strpos($sKalSuchParam,'kal_Intervall=%5B%5D')) $sKalSuchParam=substr_replace($sKalSuchParam,'',$nKalJ-5,25);
 }
 if(defined('KAL_MetaKey')) $sKalKey=KAL_MetaKey; else $sKalKey=KAL_TxAMetaKey;
 if(defined('KAL_MetaDes')) $sKalDes=KAL_MetaDes; else $sKalDes=KAL_TxAMetaDes;
 if(defined('KAL_MetaTit')) $sKalTit=KAL_MetaTit; else $sKalTit=KAL_TxAMetaTit;

 //Beginn der Ausgabe
 $sKalHtmlVor=str_replace('{META-KEY}',$sKalKey,str_replace('{META-DES}',$sKalDes,str_replace('{TITLE}',$sKalTit,$sKalHtmlVor)));
 echo $sKalHtmlVor."\n".'<div class="kalBox">'."\n"; include(KAL_Pfad.'kalVersion.php');
 if(KAL_Version!=$kalVersion||strlen(KAL_Www)==0) echo "\n".'<p class="kalFehl">'.fKalTx(KAL_TxSetupFehlt).'</p>'."\n";

 //Aktivitaetslinks oben
 if(!defined('KAL_LinkLstLst')) define('KAL_LinkLstLst',false);
 $sKal=''; $sKalSes=$sKalSession; if($sKalSession) $sKalSession.=$sKalZentrum; if(KAL_LinkUMona||KAL_LinkOMona||KAL_LinkUWoch||KAL_LinkOWoch) $sKalSPo1F1=fKalStrip1F1($sKalSuchParam);
 if(KAL_LinkOList&&(!KAL_LnkOListN||$sKalSession)&&(KAL_LinkLstLst||$sKalAktion!='liste')) $sKal.="\n".'<li><a href="'.KAL_Self.(KAL_Query.$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam?'?'.substr(KAL_Query.($sKalAktion!='liste'?$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam:$sKalSession),5):'').'">'.fKalTx(KAL_LinkOList).'</a></li>';
 if(KAL_LinkODrck&&(!KAL_LnkODrckN||$sKalSession))
  if(KAL_DruckPopup){
   if($sKalAktion=='liste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druck'.$sKalSession.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
   if($sKalAktion=='monat') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckmonat'.$sKalSession.$sKalSuchParam.$sKalMonat.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
   if($sKalAktion=='woche') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckwoche'.$sKalSession.$sKalSuchParam.$sKalWoche.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
   if($sKalAktion=='detail') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=drucken'.$sKalSession.$sKalNummer.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
   if($sKalAktion=='nzusageneliste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckezusagen'.$sKalSession.$sKalSort.$sKalFilt.$sKalAbst.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
   if($sKalAktion=='zusagezeigen') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckfzusagen'.$sKalSession.$sKalNummer.$sKalSort.$sKalAbst.$sKalSuchParam.(isset($_GET['kal_Zusagen'])?'&amp;kal_Zusagen=1':'').'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkODrck).'</a></li>';
  }else{
   if($sKalAktion=='liste') $sKal.=fKalLink('druck',KAL_LinkODrck,$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='monat') $sKal.=fKalLink('druckmonat',KAL_LinkODrck,$sKalSession.$sKalSuchParam.$sKalMonat.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='woche') $sKal.=fKalLink('druckwoche',KAL_LinkODrck,$sKalSession.$sKalSuchParam.$sKalWoche.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='detail') $sKal.=fKalLink('drucken',KAL_LinkODrck,$sKalSession.$sKalNummer.$sKalStart.$sKalMonat.$sKalWoche.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='nzusageneliste') $sKal.=fKalLink('druckezusagen',KAL_LinkODrck,$sKalSession.$sKalNummer.$sKalSort.$sKalFilt.$sKalAbst.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='zusagezeigen') $sKal.=fKalLink('druckfzusagen',KAL_LinkODrck,$sKalSession.$sKalNummer.$sKalSort.$sKalAbst.$sKalSuchParam.(isset($_GET['kal_Zusagen'])?'&amp;kal_Zusagen=1':'').(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
  }
 if(KAL_LinkOMona&&(!KAL_LnkOMonaN||$sKalSession)) $sKal.=fKalLink('monat',KAL_LinkOMona,$sKalMonat.$sKalSession.($sKalSPo1F1?$sKalSPo1F1:$sKalSuchParam));
 if(KAL_LinkOWoch&&(!KAL_LnkOWochN||$sKalSession)) $sKal.=fKalLink('woche',KAL_LinkOWoch,$sKalWoche.$sKalSession.($sKalSPo1F1?$sKalSPo1F1:$sKalSuchParam));
 if(KAL_LinkOSuch&&(!KAL_LnkOSuchN||$sKalSession)) $sKal.=fKalLink('suche',KAL_LinkOSuch,$sKalSession.$sKalSuchParam);
 if(KAL_LinkOExpt&&(!KAL_LnkOExptN||$sKalSession))
  if(KAL_CalPopup){
   if($sKalAktion=='liste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=export'.$sKalSession.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Lst=1&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;">'.fKalTx(KAL_LinkOExpt).'</a></li>';
   if($sKalAktion=='detail') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=export'.$sKalSession.$sKalNummer.'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;">'.fKalTx(KAL_LinkOExpt).'</a></li>';
  }else{
   if($sKalAktion=='liste') $sKal.=fKalLink('export',KAL_LinkOExpt,$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Lst=1'.(KAL_CalPopup?'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;':''));
   if($sKalAktion=='detail') $sKal.=fKalLink('export',KAL_LinkOExpt,$sKalSession.$sKalNummer.$sKalStart.$sKalMonat.$sKalWoche.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_CalPopup?'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;':''));
  }
 if(KAL_LinkOEing&&(!KAL_LnkOEingN||$sKalSession)) $sKal.=fKalLink('eingabe',KAL_LinkOEing,$sKalSession);
 if(KAL_LinkOAend&&(!KAL_LnkOAendN||$sKalSession)) $sKal.=fKalLink($sKalAendAktion,KAL_LinkOAend,$sKalSession.$sKalAendParam);
 if(KAL_LinkOLogi&&(!KAL_LnkOLogiN||$sKalSession))
  if(empty($sKalZentrum)||$sKalAktion=='nzentrum'||$sKalAktion=='login')
   $sKal.=fKalLink('login',($sKalSession==''||!defined('KAL_LinkOLogx')?KAL_LinkOLogi:KAL_LinkOLogx),($sKalSession==''||!defined('KAL_LinkOLogx')?$sKalSession:$sKalSes));
  else $sKal.=fKalLink('nzentrum',KAL_TxNZentrum,$sKalSession);
 if(!$bKalPopup&&strlen($sKal)>0) echo "\n".'<ul class="kalMnuO">'.$sKal."\n".'</ul>'."\n";

 // Druck als Popup-Fenster
 if(KAL_DruckPopup&&substr($sKalAktion,0,5)!='druck') echo "\n".'<script>function PrnWin(sURL){prnWin=window.open(sURL,"prnwin","width='.KAL_PopupBreit.',height='.KAL_PopupHoch.',left='.(KAL_PopupX+4).',top='.(KAL_PopupY+4).',menubar=yes,statusbar=no,toolbar=no,scrollbars=yes,resizable=yes");prnWin.focus();}</script>'."\n";

 //Seiteninhalt
 if($sKalAktion!='login') echo fKalSeite()."\n"; else echo fKalLogSeite()."\n";

 //Aktivitaetslinks unten
 $sKal='';
 if(KAL_LinkUList&&(!KAL_LnkUListN||$sKalSession)&&($sKalAktion!='liste')) $sKal.="\n".'<li><a href="'.KAL_Self.(KAL_Query.$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam?'?'.substr(KAL_Query.($sKalAktion!='liste'?$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam:$sKalSession),5):'').'">'.fKalTx(KAL_LinkUList).'</a></li>';
 if(KAL_LinkUDrck&&(!KAL_LnkUDrckN||$sKalSession))
  if(KAL_DruckPopup){
   if($sKalAktion=='liste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druck'.$sKalSession.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
   if($sKalAktion=='monat') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckmonat'.$sKalSession.$sKalSuchParam.$sKalMonat.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
   if($sKalAktion=='woche') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckwoche'.$sKalSession.$sKalSuchParam.$sKalWoche.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
   if($sKalAktion=='detail') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=drucken'.$sKalSession.$sKalNummer.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
   if($sKalAktion=='nzusageneliste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckezusagen'.$sKalSession.$sKalSort.$sKalFilt.$sKalAbst.'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
   if($sKalAktion=='zusagezeigen') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=druckfzusagen'.$sKalSession.$sKalNummer.$sKalSort.$sKalAbst.$sKalSuchParam.(isset($_GET['kal_Zusagen'])?'&amp;kal_Zusagen=1':'').'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;">'.fKalTx(KAL_LinkUDrck).'</a></li>';
  }else{
   if($sKalAktion=='liste') $sKal.=fKalLink('druck',KAL_LinkUDrck,$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='monat') $sKal.=fKalLink('druckmonat',KAL_LinkUDrck,$sKalSession.$sKalSuchParam.$sKalMonat.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='woche') $sKal.=fKalLink('druckwoche',KAL_LinkUDrck,$sKalSession.$sKalSuchParam.$sKalWoche.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='detail') $sKal.=fKalLink('drucken',KAL_LinkUDrck,$sKalSession.$sKalNummer.$sKalStart.$sKalMonat.$sKalWoche.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='nzusageneliste') $sKal.=fKalLink('druckezusagen',KAL_LinkUDrck,$sKalSession.$sKalNummer.$sKalSort.$sKalFilt.$sKalAbst.(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
   if($sKalAktion=='zusagezeigen') $sKal.=fKalLink('druckfzusagen',KAL_LinkUDrck,$sKalSession.$sKalNummer.$sKalSort.$sKalAbst.$sKalSuchParam.(isset($_GET['kal_Zusagen'])?'&amp;kal_Zusagen=1':'').(KAL_DruckPopup?'&amp;kal_Popup=1" target="prnwin" onclick="PrnWin(this.href);return false;':''));
  }
 if(KAL_LinkUMona&&(!KAL_LnkUMonaN||$sKalSession)) $sKal.=fKalLink('monat',KAL_LinkUMona,$sKalMonat.$sKalSession.($sKalSPo1F1?$sKalSPo1F1:$sKalSuchParam));
 if(KAL_LinkUWoch&&(!KAL_LnkUWochN||$sKalSession)) $sKal.=fKalLink('woche',KAL_LinkUWoch,$sKalWoche.$sKalSession.($sKalSPo1F1?$sKalSPo1F1:$sKalSuchParam));
 if(KAL_LinkUSuch&&(!KAL_LnkUSuchN||$sKalSession)) $sKal.=fKalLink('suche',KAL_LinkUSuch,$sKalSession.$sKalSuchParam);
 if(KAL_LinkUExpt&&(!KAL_LnkUExptN||$sKalSession))
  if(KAL_CalPopup){
   if($sKalAktion=='liste') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=export'.$sKalSession.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Lst=1&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;">'.fKalTx(KAL_LinkUExpt).'</a></li>';
   if($sKalAktion=='detail') $sKal.="\n".'<li><a href="'.KAL_Url.'kalender.php?kal_Aktion=export'.$sKalSession.$sKalNummer.'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;">'.fKalTx(KAL_LinkUExpt).'</a></li>';
  }else{
   if($sKalAktion=='liste') $sKal.=fKalLink('export',KAL_LinkUExpt,$sKalSession.$sKalStart.$sKalIndex.$sKalRueck.$sKalSuchParam.'&amp;kal_Lst=1'.(KAL_CalPopup?'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;':''));
   if($sKalAktion=='detail') $sKal.=fKalLink('export',KAL_LinkUExpt,$sKalSession.$sKalNummer.$sKalStart.$sKalMonat.$sKalWoche.$sKalIndex.$sKalRueck.$sKalSuchParam.(KAL_CalPopup?'&amp;kal_Popup=1" target="expwin" onclick="ExpWin(this.href);return false;':''));
  }
 if(KAL_LinkUEing&&(!KAL_LnkUEingN||$sKalSession)) $sKal.=fKalLink('eingabe',KAL_LinkUEing,$sKalSession);
 if(KAL_LinkUAend&&(!KAL_LnkUAendN||$sKalSession)) $sKal.=fKalLink($sKalAendAktion,KAL_LinkUAend,$sKalSession.$sKalAendParam);
 if(KAL_LinkULogi&&(!KAL_LnkULogiN||$sKalSession))
  if(empty($sKalZentrum)||$sKalAktion=='nzentrum'||$sKalAktion=='login')
   $sKal.=fKalLink('login',($sKalSession==''||!defined('KAL_LinkULogx')?KAL_LinkULogi:KAL_LinkULogx),($sKalSession==''||!defined('KAL_LinkULogx')?$sKalSession:$sKalSes));
  else $sKal.=fKalLink('nzentrum',KAL_TxNZentrum,$sKalSession);
 if(!$bKalPopup&&strlen($sKal)>0) echo "\n".'<ul class="kalMnuU">'.$sKal."\n".'</ul>'."\n";

 //Ende der Ausgabebox und evt. Seitenfuss
 echo "\n".'</div>'."\n".$sKalHtmlNach;
}
echo "\n";

function fKalLink($sAktion,$sLinkTx,$sParam){
 return "\n".'<li><a href="'.KAL_Self.'?'.substr(KAL_Query.'&amp;',5).'kal_Aktion='.$sAktion.$sParam.'">'.fKalTx($sLinkTx).'</a></li>';
}

function fKalDSEFld($z,$bCheck=false){
 $s='<a class="kalText" href="'.KAL_DSELink.'"'.(KAL_DSEPopUp?' target="dsewin" onclick="DSEWin(this.href)"':(KAL_DSETarget?' target="'.KAL_DSETarget.'"':'')).'>';
 $s=str_replace('[L]',$s,str_replace('[/L]','</a>',str_replace('{{','<',str_replace('}}','>',fKalTx(str_replace('<','{{',str_replace('>','}}',($z!=2?KAL_TxDSE1:KAL_TxDSE2))))))));
 return '<input class="kalCheck" type="checkbox" name="kal_DSE'.$z.'" value="1"'.($bCheck?' checked="checked"':'').'> '.$s;
}

function fKalTx($s){ //TextKodierung
 if(KAL_Zeichensatz==0) $s=str_replace('"','&quot;',$s); elseif(KAL_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',$s)); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 return str_replace('\n ','<br>',$s);
}
function fKalDt($s){ //DatenKodierung
 $kalZeichensatz=KAL_LZeichenstz; if(KAL_Popup) $kalZeichensatz=KAL_LZsatzPopup;
 if(KAL_Zeichensatz==$kalZeichensatz){if(KAL_Zeichensatz!=1) $s=str_replace('"','&quot;',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s))));}
 else{
  if($kalZeichensatz!=0) if($kalZeichensatz==2) $s=iconv('UTF-8','ISO-8859-1//TRANSLIT',$s); else $s=html_entity_decode($s);
  if(KAL_Zeichensatz==0) $s=str_replace('"','&quot;',str_replace(chr(150),'-',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s)))));
  elseif(KAL_Zeichensatz==2) $s=iconv('ISO-8859-1','UTF-8',str_replace('"','&quot;',str_replace(chr(150),'-',str_replace(chr(132),'&quot;',str_replace(chr(147),'&quot;',str_replace(chr(128),'&euro;',$s)))))); else $s=htmlentities($s,ENT_COMPAT,'ISO-8859-1');
 }
 return str_replace('\n ','<br>',$s);
}
function fKalExtLink($s){
 if(!defined('KAL_ZSatzExtLink')||KAL_ZSatzExtLink==0) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode($s))));
 elseif(KAL_ZSatzExtLink==1) $s=str_replace('%2F','/',str_replace('%3F','?',str_replace('%3A',':',rawurlencode(iconv('ISO-8859-1','UTF-8',$s)))));
 elseif(KAL_ZSatzExtLink==2) $s=iconv('ISO-8859-1','UTF-8',$s);
 return $s;
}

function fKalRq($sTx){ //Eingaben reinigen
 return stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
}
function fKalRq1($sTx){ //nur 1. Wort der Eingaben
 $sTx=stripslashes(str_replace('"',"'",@strip_tags(trim($sTx))));
 if($nP=strpos($sTx,' ')) $sTx=substr($sTx,0,$nP);
 return $sTx;
}
function fKalStrip1F1($s){
 if(strpos($s,'1F1=')) if($n=strpos('#'.$s,'&amp;kal_1F1='))
  if($q=strpos($s,'&amp;',$n--)) return substr_replace($s,'',$n,$q-$n); else return substr($s,0,$n);
}

function fKalHost(){
 if(isset($_SERVER['HTTP_HOST'])) $s=$_SERVER['HTTP_HOST']; elseif(isset($_SERVER['SERVER_NAME'])) $s=$_SERVER['SERVER_NAME']; else $s='localhost';
 return $s;
}

function fKalDeCode($w){
 $nCod=(int)substr(KAL_Schluessel,-2); $s=''; $j=0;
 for($k=strlen($w)/2-1;$k>=0;$k--){$i=$nCod+($j++)+hexdec(substr($w,$k+$k,2)); if($i>255) $i-=256; $s.=chr($i);}
 return $s;
}

function fKalAnzeigeDatum($w){
 $s1=substr($w,8,2); $s2=substr($w,5,2); $s3=(KAL_Jahrhundert?substr($w,0,4):substr($w,2,2));
 switch(KAL_Datumsformat){ //0:yy-mm-dd 1:dd.mm.yy 2:mm/dd/yy 3:dd/mm/yy 4:dd-mm-yy
  case 0: $t='-'; $s1=$s3; $s3=substr($w,8,2); break; case 1: $t='.'; break;
  case 2: $t='/'; $s1=$s2; $s2=substr($w,8,2); break; case 3: $t='/'; break; case 4: $t='-'; break;
 }
 return $s1.$t.$s2.$t.$s3;
}

function fKalBB($s){ //BB-Code zu HTML wandeln
 $v=str_replace("\n",'<br>',str_replace("\n ",'<br>',str_replace("\r",'',$s))); $p=strpos($v,'['); $aT=array('b'=>0,'i'=>0,'u'=>0,'span'=>0,'p'=>0,'a'=>0);
 while(!($p===false)){
  $Tg=substr($v,$p,9);
  if(substr($Tg,0,3)=='[b]'){$v=substr_replace($v,'<b>',$p,3); $aT['b']++;}elseif(substr($Tg,0,4)=='[/b]'){$v=substr_replace($v,'</b>',$p,4); $aT['b']--;}
  elseif(substr($Tg,0,3)=='[i]'){$v=substr_replace($v,'<i>',$p,3); $aT['i']++;}elseif(substr($Tg,0,4)=='[/i]'){$v=substr_replace($v,'</i>',$p,4); $aT['i']--;}
  elseif(substr($Tg,0,3)=='[u]'){$v=substr_replace($v,'<u>',$p,3); $aT['u']++;}elseif(substr($Tg,0,4)=='[/u]'){$v=substr_replace($v,'</u>',$p,4); $aT['u']--;}
  elseif(substr($Tg,0,7)=='[color='){$o=substr($v,$p+7,9); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="color:'.$o.'">',$p,8+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,8)=='[/color]'){$v=substr_replace($v,'</span>',$p,8); $aT['span']--;}
  elseif(substr($Tg,0,6)=='[size='){$o=substr($v,$p+6,4); $o=substr($o,0,strpos($o,']')); $v=substr_replace($v,'<span style="font-size:'.(100+(int)$o*14).'%">',$p,7+strlen($o)); $aT['span']++;} elseif(substr($Tg,0,7)=='[/size]'){$v=substr_replace($v,'</span>',$p,7); $aT['span']--;}
  elseif(substr($Tg,0,8)=='[center]'){$v=substr_replace($v,'<p class="kalText" style="text-align:center">',$p,8); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,9)=='[/center]'){$v=substr_replace($v,'</p>',$p,9); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,7)=='[right]'){$v=substr_replace($v,'<p class="kalText" style="text-align:right">',$p,7); $aT['p']++; if(substr($v,$p-4,4)=='<br>') $v=substr_replace($v,'',$p-4,4);} elseif(substr($Tg,0,8)=='[/right]'){$v=substr_replace($v,'</p>',$p,8); $aT['p']--; if(substr($v,$p+4,4)=='<br>') $v=substr_replace($v,'',$p+4,4);}
  elseif(substr($Tg,0,5)=='[url]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="'.(!strpos(substr($v,$o,9),'://')&&!strpos(substr($v,$o-1,6),'tel:')?'http://':''),$p,5); $aT['a']++;
  }elseif(substr($Tg,0,6)=='[/url]'){$v=substr_replace($v,'</a>',$p,6); $aT['a']--;}
  elseif(substr($Tg,0,6)=='[link]'){
   $o=$p+6; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'">',$l,1); else $v=substr_replace($v,'">'.substr($v,$o,$l-$o),$l,0);
   $v=substr_replace($v,'<a class="kalText" target="_blank" href="',$p,6); $aT['a']++;
  }elseif(substr($Tg,0,7)=='[/link]'){$v=substr_replace($v,'</a>',$p,7); $aT['a']--;}
  elseif(substr($Tg,0,5)=='[img]'){
   $o=$p+5; if(!$l=min(strpos($v,'[',$o),strpos($v,' ',$o))) $l=strpos($v,'[',$o);
   if(substr($v,$l,1)==' ') $v=substr_replace($v,'" alt="',$l,1); else $v=substr_replace($v,'" alt="',$l,0);
   $v=substr_replace($v,'<img src="',$p,5);
  }elseif(substr($Tg,0,6)=='[/img]') $v=substr_replace($v,'" style="border:0">',$p,6);
  elseif(substr($Tg,0,5)=='[list'){
   if(substr($Tg,5,2)=='=o'){$q='o';$l=2;}else{$q='u';$l=0;}
   $v=substr_replace($v,'<'.$q.'l class="kalText"><li class="kalText">',$p,6+$l);
   $n=strpos($v,'[/list]',$p+5); if(substr($v,$n+7,4)=='<br>') $l=4; else $l=0; $v=substr_replace($v,'</'.$q.'l>',$n,7+$l);
   $l=strpos($v,'<br>',$p);
   while($l<$n&&$l>0){$v=substr_replace($v,'</li><li class="kalText">',$l,4); $n+=19; $l=strpos($v,'<br>',$l);}
  }
  $p=strpos($v,'[',$p+1);
 }
 foreach($aT as $q=>$p) if($p>0) for($l=$p;$l>0;$l--) $v.='</'.$q.'>';
 return $v;
}
?>