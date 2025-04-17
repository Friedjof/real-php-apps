<?php
// Setup
define('UMF_Version','3.3_2023-06-10');
define('UMF_Konfiguration','Grundkonfiguration');
define('UMF_Www','localhost');
define('UMF_Pfad','/var/www/html/');
define('UMF_Daten','daten/');
define('UMF_Bilder','bilder/');
define('UMF_Fragen','fragen.txt');
define('UMF_Ergebnis','ergebnis.txt');
define('UMF_Zuweisung','zuweisung.txt');
define('UMF_Nutzer','nutzer0.txt');
define('UMF_Teilnahme','teilnahme.txt');
define('UMF_SQL',false);
define('UMF_SqlHost','mysql');
define('UMF_SqlDaBa','umfrage');
define('UMF_SqlUser','root');
define('UMF_SqlPass','root');
define('UMF_SqlCharSet','');
define('UMF_SqlTabF','umf_fragen');
define('UMF_SqlTabE','umf_ergebnis');
define('UMF_SqlTabZ','umf_zuweisung');
define('UMF_SqlTabN','umf_nutzer');
define('UMF_SqlTabT','umf_teilnahme');
define('UMF_Schluessel','00');

// Administration
define('ADU_MitLogin',true);
define('ADU_Admin','admin');
define('ADU_Passwort','');
define('ADU_AuthLogin',false);
define('ADU_Author','autor');
define('ADU_AuthPass','4C4A50523F');
define('ADU_SessionsAgent',true);
define('ADU_SessionsIPAddr',true);
define('ADU_Hilfe','http://www.server-scripts.de/umfrage/');
define('ADU_SqlCharSet','latin1');
define('ADU_Breite',975);
define('ADU_AntwortZahl',6);
define('ADU_AnmerkZahl',2);
define('ADU_FragenFeldHoehe',5);
define('ADU_AntwortFeldHoehe',2);
define('ADU_AnmerkFeldHoehe',4);
define('ADU_StripSlashes',false);
define('ADU_ListenLaenge',10);
define('ADU_Rueckwaerts',false);
define('ADU_ErgebnisLaenge',10);
define('ADU_ErgebnisRueckw',false);
define('ADU_ErgebnisExport','1;0;0;1');
define('ADU_TeilnahmeLaenge',10);
define('ADU_TeilnahmeRueckw',true);
define('ADU_TeilnahmeExport','1;1;0;0;1;0');
define('ADU_NutzerLaenge',20);
define('ADU_NutzerRueckw',false);
define('ADU_NutzerBetreff','Ihre Umfragebeteiligung');
define('ADU_NutzerKontakt','Sehr geehrte Damen und Herren,\n \n Sie haben im Umfrage-Script.....');
define('ADU_DruckSuch','FNr1:1;FNr2:99;Onl :1;Frg1:e;Frg3:xx;Bem1:e');
define('ADU_DruckFeld','1;0;0;1;1:b:96;1;1;1;;;1;0;Druckliste-Test');

//Allgemeines
define('UMF_Zeichensatz',0); //0:Standard 1:HTML-&-maskiert 2:UTF-8
define('UMF_WarnMeldungen',true);
define('UMF_TimeZoneSet','Europe/Berlin');
define('UMF_Datumsformat','d.m.Y H:i');
define('UMF_Captcha',true);
define('CAPTCHA_SALT','H38497');
define('UMF_CaptchaTyp','G');
define('UMF_CaptchaGrafisch',true);
define('UMF_CaptchaNumerisch',false);
define('UMF_CaptchaTextlich',false);
define('UMF_CaptchaPfad','captcha/');
define('UMF_CaptchaDatei','captcha.csv');
define('UMF_CaptchaBreit',110);
define('UMF_CaptchaHoch',24);
define('UMF_CaptchaHgFarb','#CCCCEE');
define('UMF_CaptchaTxFarb','#000099');
define('UMF_DSELink','datenschutz.html');
define('UMF_DSETarget','_blank');
define('UMF_DSEPopUp',false);
define('UMF_DSEPopupW',900);
define('UMF_DSEPopupH',600);
define('UMF_DSEPopupX',5);
define('UMF_DSEPopupY',5);
define('UMF_Empfaenger','webmaster@domain.de');
define('UMF_Sender','Umfrage-Script <webmaster@domain.de>');
define('UMF_Smtp',false);
define('UMF_SmtpHost','localhost');
define('UMF_SmtpPort',25);
if(!defined('SMTP_No_TLS')) define('SMTP_No_TLS',true);
define('UMF_SmtpAuth',false);
define('UMF_SmtpUser','');
define('UMF_SmtpPass','');
define('UMF_EnvelopeSender','');

//Layout Fragen
define('UMF_Schablone','umfSeite.htm');
define('UMF_CSSDatei','umfStyle.css');
define('UMF_Layout',1);
define('UMF_BildKB',500);
define('UMF_BildW',150);
define('UMF_BildH',160);
define('UMF_BildErsatz','fragezeichen.jpg');
define('UMF_RadioButton',false);
define('UMF_ZeigeNummer','oben'); //oben/unten
define('UMF_NummerStellen','0');
define('UMF_NummernText','#N (#I) von #M');
define('UMF_ZeigeBemerkung','oben2'); //oben/unten
define('UMF_ZeigeBemerkng2','unten2'); //oben/unten

//Layout Diagramm
define('UMF_GrafikBalken',true); //Balken/Saeulen
define('UMF_GrafikMaximum',100);
define('UMF_GrafikDicke',20);
define('UMF_GrafikFrage','oben'); //oben/unten
define('UMF_GrafikWerte','rechts'); //oben/unten/links/rechts
define('UMF_GrafikProzente',false);
define('UMF_GrafikTlnAnz',true);
define('UMF_GrafikOhneLogin',false);

//Ablaufeinstellungen
define('UMF_StdUmfrCode',0);
define('UMF_UmfrA',';;;;'); //unscharf;Nutzer,Teilnehmer,Name,Code
define('UMF_UmfrB',';;;;');
define('UMF_UmfrC',';;;;');
define('UMF_UmfrD',';;;;');
define('UMF_UmfrE',';;;;');
define('UMF_UmfrF',';;;;');
define('UMF_UmfrG',';;;;');
define('UMF_UmfrH',';;;;');
define('UMF_UmfrI',';;;;');
define('UMF_UmfrJ',';;;;');
define('UMF_UmfrK',';;;;');
define('UMF_UmfrL',';;;;');
define('UMF_UmfrM',';;;;');
define('UMF_UmfrN',';;;;');
define('UMF_UmfrO',';;;;');
define('UMF_UmfrP',';;;;');
define('UMF_UmfrQ',';;;;');
define('UMF_UmfrR',';;;;');
define('UMF_UmfrS',';;;;');
define('UMF_UmfrT',';;;;');
define('UMF_UmfrU',';;;;');
define('UMF_UmfrV',';;;;');
define('UMF_UmfrW',';;;;');
define('UMF_UmfrX',';;;;');
define('UMF_UmfrY',';;;;');
define('UMF_UmfrZ',';;;;');
define('UMF_UmfrUnscharf',false);
define('UMF_Anonym',false);
define('UMF_IPAdressen',5);
define('UMF_NachAbstimmen','Fertig'); //Fertig/Grafik
define('UMF_GastLog',false);
define('UMF_FertigHtml',false);
define('UMF_FertigMail',true);
define('UMF_GrafikLink','zur Auswertung');
define('UMF_ZentrumLink','zur Umfragenauswahl');
define('UMF_NeuAnfangLink','zum Neuanfang');

//Teilnehmerregistrierung
define('UMF_Registrierung',''); // -,vorher,nachher
define('UMF_TeilnehmerLog',false);
define('UMF_TeilnehmerSperre',false);
define('UMF_TeilnehmerDSE1',false);
define('UMF_TeilnehmerDSE2',false);
define('UMF_NachRegisterWohin','Fragen'); //Daten,Fragen,Auswahl
define('UMF_TeilnehmerMitCode',false);
define('UMF_SofortFrageNachReg',false);
define('UMF_TeilnehmerNormUmfrage',true);
define('UMF_TeilnehmerAlleUmfrage',true);
define('UMF_TeilnehmerDrucken',true);
define('UMF_TeilnehmerKennfeld',1);
define('UMF_TeilnehmerFelder',"Name;Vorname;E-Mail;Telefon, Mobil");
define('UMF_TeilnehmerPflicht',"1;0;0;0");

//Benutzerverwaltung
define('UMF_MaxSessionZeit',65);
define('UMF_Nutzerverwaltung',''); // -,vorher,nachher
define('UMF_NutzerLog',false);
define('UMF_Nutzerzwang',false);
define('UMF_NutzerSperre',false);
define('UMF_NutzerMitCode',false);
define('UMF_NutzerFelder',"Nummer;aktiv;Benutzer;Passwort;E-Mail;Anrede;Vorname + Name;PLZ Ort;Strasse;Telefon, Mobil;Fax;GUELTIG_BIS");
define('UMF_NutzerPflicht',"0;0;1;1;1;0;1;1;0;0;0;0");
define('UMF_NutzerFrist',0);
define('UMF_Nutzerfreigabe',false);
define('UMF_NutzerNeuErlaubt',true);
define('UMF_NutzerNeuAdmMail',true);
define('UMF_NutzerNeuMail',true);
define('UMF_NutzerAktivMail',true);
define('UMF_NutzerDSE1',false);
define('UMF_NutzerDSE2',false);
define('UMF_PasswortSenden',true);
define('UMF_NachLoginWohin','Zentrum'); //DatenBest,DatenKorr,FragenA,FragenB,Zentrum
define('UMF_NutzerUmfragen',true);

//Benutzerzentrum
define('UMF_NutzerNormUmfrage',true);
define('UMF_NutzerAlleUmfrage',true);
define('UMF_NutzerErgebnis',true);
define('UMF_NutzerDrucken',false);
define('UMF_NutzerGrafik',false);
define('UMF_NutzerAendern',true);
define('UMF_ZntErgebnisRueckw',true);
define('UMF_ZntAntwort',true);
define('UMF_ZntLoesung',true);
define('UMF_ZntAnzahlO',true);
define('UMF_ZntRichtigeO',true);
define('UMF_ZntFalscheO',true);
define('UMF_ZntPunkteO',true);
define('UMF_ZntVerbalO',true);
define('UMF_ZntVersucheO',false);
define('UMF_ZntAuslassenO',false);
define('UMF_ZntFrageNr',true);
define('UMF_ZntErgebnis',true);
define('UMF_ZntPunkte',true);
define('UMF_ZntVersuche',false);
define('UMF_ZntAuslassen',false);
define('UMF_ZntKatErgebnis',true);
define('UMF_ZntKatFehlErgb',false);
define('UMF_ZntKatPunkte',true);
define('UMF_ZntKatSumme',true);

//Druckeinstellungen
define('UMF_DruckSchablone','');
define('UMF_DruckSpalten','1;1;1;1;1;1;1'); // Nr;Ufr;Fra;Bld;Aw;B1;B2
define('UMF_DruckSuchSpalten',3);
define('UMF_DruckSuche','1;1;1;1;1;1;1');
define('UMF_DruckRueckw',false);
define('UMF_DruckBildW',100);
define('UMF_DruckGast',true);

/* Sprachfloskeln */
// feste Begriffe
define('UMF_TxNr','Nr.');
define('UMF_TxVon','von');
define('UMF_TxFuer','f�r');
define('UMF_TxOder','oder');
define('UMF_TxNicht','nicht');
define('UMF_TxBis','bis');
define('UMF_TxWie','wie');
define('UMF_TxOderWie','oder wie');
define('UMF_TxIstOderAb','ist oder ab');
define('UMF_TxAberNichtWie','aber nicht wie');
define('UMF_TxDatum','Datum/Zeit');
define('UMF_TxZeit','Zeit');
define('UMF_TxAktiv','aktiviert');
define('UMF_TxKorrig','korrigieren');
define('UMF_TxUmfrage','Umfrage');
define('UMF_TxUmfr','Umfr.');
define('UMF_TxFrage','Frage');
define('UMF_TxAntwort','Antwort');
define('UMF_TxAnzahl','Fragenanzahl');
define('UMF_TxBild','Bild');
define('UMF_TxBemerkung','Anmerkung');
define('UMF_TxDrucken','Drucken');
define('UMF_TxDruckSperre','Das Drucken ist an dieser Stelle nicht erlaubt!');
define('UMF_TxDruckMeld','Stellen Sie Ihre Druckliste zusammen!');
define('UMF_TxDruckGanzeListe','Gesamt-Umfragenliste');
define('UMF_TxDruckFilterListe','Auszug aus der Umfragenliste');
define('UMF_TxDruckFilter','Auswahl der zu druckenden Fragen anhand folgender Filterbedingungen:');
define('UMF_TxDruckSpalten','In der Druckliste sollen folgende Spalten erscheinen:');
define('UMF_TxDruckNrOriginal','Original-Nummer');
define('UMF_TxDruckNrCronolog','chronologisch');

define('UMF_TxVorFrage',"[color=navy][b]Frage[/b][/color]:");
define('UMF_TxWeiter','Weiter'); //??
define('UMF_TxAbstimmen','Abstimmen');
define('UMF_TxBeantworten','Beantworten Sie nun bitte diese #. Frage.');
define('UMF_TxAntwortFehlt',"Bitte beantworten Sie die Frage!");
define('UMF_TxAbgestimmt','Vielen Dank f�r Ihre Teilnahme.');
define('UMF_TxGleicheAdresse','Wiederholte Abstimmungen werden nicht akzeptiert!');
define('UMF_TxAnonymeAdresse','Anonymisierte Abstimmungen werden nicht akzeptiert!');
define('UMF_TxFertigText','Alle Abstimmungsergebnisse werden [i]vertraulich[/i] behandelt und [i]anonym[/i] gespeichert. ....');
define('UMF_TxGrafik',"Bisher wurde so abgestimmt:");
define('UMF_TxTeilnehmer',"Meinungen");
define('UMF_TxCaptchaFeld','Sicherheitscode');
define('UMF_TxCaptchaNeu','neues #Captcha anfordern');
define('UMF_TxZahlenCaptcha','Zahlen-');
define('UMF_TxTextCaptcha','Text-');
define('UMF_TxGrafikCaptcha','Grafik-');
define('UMF_TxCaptchaHilfe','Bitte den Buchstaben und die 4 Ziffern eingeben');
define('UMF_TxCaptchaFehl','Bitte geben Sie den Sicherheitscode korrekt ein!');
define('UMF_TxDSE1',"Ich habe die [L]Datenschutzerkl�rung[/L] gelesen und stimme ihr zu.");
define('UMF_TxDSE2',"Ich bin mit der Verarbeitung und Speicherung meiner pers�nlichen Daten im Rahmen der Datenschutzerkl�rung einverstanden.");

//Ablauf-E-Mail
define('UMF_TxFertigMlBtr',"neue Abstimmung im Umfragescript");
define('UMF_TxFertigMlTxt','Im Umfragescript wurde soeben folgende Antwort eingetragen:\n \n #');

//Teilnehmerregistrierung
define('UMF_TxLoginErfassen',"Registrierung nur f�r diesen einen Umfragedurchlauf.");
define('UMF_TxTeilnehmerSperre','Der Zugang f�r Teilnehmer ist momentan gesperrt.');
define('UMF_TxVorVorErfassen',"Vor Beginn der Umfrage m�ssen Sie sich erst registrieren.");
define('UMF_TxNachVorErfassen',"Ihre Daten wurden folgenderma�en erfasst.");
define('UMF_TxVorNachErfassen',"Sie haben alle # Fragen abgearbeitet. Tragen Sie Ihre Daten ein.");
define('UMF_TxRegistNicht','');

// Benutzerverwaltung
define('UMF_TxAnmelden','Anmelden');
define('UMF_TxEintragen','Eintragen');
define('UMF_TxSenden','Senden');
define('UMF_TxBenutzer','Benutzer');
define('UMF_TxBenutzername','Benutzername');
define('UMF_TxGewuenscht','gew�nschter');
define('UMF_TxMailAdresse','E-Mail-Adresse');
define('UMF_TxPasswort','Passwort');
define('UMF_TxNutzerNr','Benutzernummer');
define('UMF_TxNutzerFrist','g�ltig bis');
define('UMF_TxNutzerAblauf','abgelaufen');
define('UMF_TxNutzerRegel','(4...25 Zeichen)');
define('UMF_TxPassRegel','(4...16 Zeichen)');
define('UMF_TxPflicht','Pflichtangabe');
define('UMF_TxLoginLogin','Zugang f�r angemeldete Benutzer');
define('UMF_TxLoginNeu',"Benutzerzugang jetzt beantragen");
define('UMF_TxLoginVergessen',"vergessenes Passwort zusenden");
define('UMF_TxNutzerLogin',"Melden Sie sich f�r die Durchf�hrung der Umfrage an!");
define('UMF_TxNutzerSperre','Der Zugang f�r Benutzer ist momentan gesperrt.');
define('UMF_TxLoginNicht','');
define('UMF_TxNutzerNamePass',"Bitte Benutzernamen und Passwort angeben!");
define('UMF_TxNutzerNameMail',"Bitte Benutzernamen oder E-Mail-Adresse angeben!");
define('UMF_TxEingabeFehl',"Erg�nzen Sie bei den rot markierten Feldern!");
define('UMF_TxNutzerFalsch',"Ein Benutzer mit diesen Daten ist nicht verzeichnet!");
define('UMF_TxNutzerPruefe',"Pr�fen und best�tigen Sie bitte Ihre Benutzerdaten!");
define('UMF_TxNutzerAendere','�ndern Sie jetzt die Benutzerdaten ab!'); //Adm
define('UMF_TxNutzerVergeben',"Dieser Benutzername ist bereits vergeben!");
define('UMF_TxNutzerNeu',"Die Benutzerdaten wurden eingetragen und der Webmaster informiert!");
define('UMF_TxNutzerUnveraendert',"Die Benutzerdaten bleiben unver�ndert!");
define('UMF_TxNutzerGeaendert',"Die ge�nderten Benutzerdaten wurden eingetragen!");
define('UMF_TxNutzerOK',"Sie wurden angemeldet. Beginnen Sie nun mit dem Beantworten der Fragen.");
define('UMF_TxNutzerSend',"Die Zugangsdaten wurden soeben versandt!");
define('UMF_TxSendeFehl','Die Nachricht konnte soeben nicht versandt werden!');
define('UMF_TxNutzerDatBtr',"Zugangsdaten bei #");
define('UMF_TxNutzerDaten','Sehr geehrte Damen und Herren,\n \n Sie haben soeben Ihre Zugangsdaten zum Umfrage-Script auf #A angefordert. Diese lauten:\n \n Benutzernummer: #N\n Benutzer: #B\n Passwort: #P');
define('UMF_TxNutzerNeuBtr',"Ihre Anmeldung bei #");
define('UMF_TxNutzerNeuTxt','Ihre Anmeldung bei #A wurde registriert. Bitte best�tigen Sie die Anmeldung �ber den Link\n \n #L\n \n Hier Ihre Anmeldedaten:\n #D');
define('UMF_TxNutzNeuAdmBtr',"neuer Umfrage-Script-Benutzer Nr. #");
define('UMF_TxNutzNeuAdmTxt','Ein neuer Umfrage-Script-Benutzer Nr. #N hat sich wie folgt angemeldet:\n \n #D');
define('UMF_TxNutzerAktivBtr',"Zugang aktiviert bei #");
define('UMF_TxNutzerAktivTxt','Sehr geehrte Damen und Herren,\n \n Ihr Benutzerzugang bei #A wurde soeben vom Webmaster freigeschaltet.\n \n Hier Ihre Anmeldedaten: \n \n #D');
define('UMF_TxPassiv',"Der Benutzerzugang ist nicht freigeschaltet.");
define('UMF_TxAktivieren',"Benutzerzugang jetzt aktivieren?");
define('UMF_TxAktiviert',"Ihr Benutzerzugang wurde aktiviert!");
define('UMF_TxAktivFehl',"Der Freischaltcode ist ung�ltig!");
define('UMF_TxSessionZeit','Die Sitzungszeit ist abgelaufen - bitte erneut anmelden!');
define('UMF_TxSessionUngueltig','Die Sitzung ist ung�ltig - bitte anmelden!');

//Benutzerzentrum
define('UMF_TxTeilnehmerzentrum','Umfrageauswahl');
define('UMF_TxBenutzerzentrum','Benutzerzentrum');
define('UMF_TxAbmelden','Sitzung beenden');
define('UMF_TxStandardUmf','Standardumfrage');
define('UMF_TxAuswerteGrafik','Auswertegrafik');
define('UMF_TxErgebnis','Ergebnis');
define('UMF_TxErgebnisListe','Ergebnisliste');
define('UMF_TxErgebnisDetails','Details anzeigen');
define('UMF_TxNutzerAendern','Benutzerdaten �ndern');
define('UMF_TxKeinTlnNam','Gast');
define('UMF_TxKeinNtzNam','unbekannt');
define('UMF_TxAktivCodeNoetig','Geben Sie den korrekten Aktiv-Code an!');

// System-Fehler
define('UMF_TxFrageFehlt','Keine passende Frage in der Fragendatei gefunden!');
define('UMF_TxSetupFehlt','Bitte zuerst das Setup/Update in der Administration ausf�hren!');
define('UMF_TxDateiRechte','Unzureichende Schreibrechte beim Speichern von #! (Setup-Fehler)');
define('UMF_TxSqlVrbdg','Entschuldigung - momentan MySQL-Verbindungsfehler');
define('UMF_TxSqlDaBnk','Entschuldigung - momentan MySQL-Datenbankauswahlfehler');
define('UMF_TxSqlFrage','Entschuldigung - momentan MySQL-Abfragefehler');
define('UMF_TxSqlEinfg','Entschuldigung - momentan MySQL-Einf�gefehler');
define('UMF_TxSqlAendr','Entschuldigung - momentan MySQL-�nderungsfehler');
?>