<?php
/*
Konfigurationsdatei f�r den RSS-Feed mit alternativen Einstellungen

Aufruf �ber: http://www.mein-web.de/pdad/kalender/rssfeed.php?version=2

Es m�ssen hier nur die Variablen gesetzt werden, deren Werte von der
Standardkonfiguration in der online-Administration abweichen sollen.
Alle anderen Variablen k�nnen hier auskommentiert bleiben oder gel�scht
werden.
*/

$kal_TxRssTitel='alternativer RSS-Feed-Titel';
//$kal_TxRssBeschreibung='alternative RSS-Feed-Beschreibung';
//$kal_TxRssUrheber='alternatives Copyright';

$aRssFelder=array(1,0,0,7,0,0,0,0,0);
/* Erkl�rung zu $aRssFelder:
   Positionsnummern der Felder, aus denen der Inhalt der <item>-Beitr�ge
   gebildet wird. MUSS genau 9 Zahlen enthalten. F�r nicht ben�tigte
   Platzhalter ist eine Null anzugeben.
   1...3. Zahl: Feldpositionen, aus denen <title> entsteht
   4...6. Zahl: Feldpositionen, aus denen <description> entsteht
   7. Zahl: Feldposition, aus der eventuell <category> entsteht
   8. Zahl: Feldposition, aus der eventuell <author> entsteht
   9. Zahl: Feldposition, aus der eventuell <pubDate> entsteht
*/

//$aRssTrenner=array("","","","");
/* Erkl�rung zu $aRssTrenner:
   Trennzeichen, die zwischen den verwendeten Felder eingef�gt werden sollen.
   Es sind genau 4 Strings einzutragen, notfalls auch Leerstrings.
   Falls die Trenner Sonderzeichen enthalten sollen sind diese HTML-&-maskiert
   und XML-konform anzugeben beipielsweise ein "<br />" als "&lt;br /&gt";
   1. String: Trennzeichen zwischen dem ersten und zweiten Feld bei <title>
   2. String: Trennzeichen zwischen dem zweiten und dritten Feld bei <title>
   3. String: Trennzeichen zwischen dem ersten und zweiten Feld bei <description>
   4. String: Trennzeichen zwischen dem zweiten und dritten Feld bei <description>>
*/

//$aRssFilter=array("","","","","","","","");
/* Erkl�rung zu $aRssFilter:
   Elemente, aus denen ein etwaiger Suchfilter f�r die Beitr�ge gebildet wird.
   Das Array MUSS genau 8 Strings enthalten, notfalls auch Leerstrings.
   1. String: Positionsnummer des ersten Feldes in der Terminstruktur, nach dem gefiltert werden soll
   2. String: erster Suchstring nach dem das erste Suchfeld gefiltert werden soll
   3. String: zweiter Suchstring nach dem �ber eine ODER-Verkn�pfung gefiltert werden soll
   4. String: dritter Suchstring nach dem �ber eine UND_NICHT-Verkn�pfung gefiltert werden soll
   5. String: Positionsnummer des zweiten Feldes in der Terminstruktur, nach dem gefiltert werden soll
   6. String: erster Suchstring nach dem das zweite Suchfeld gefiltert werden soll
   7. String: zweiter Suchstring nach dem �ber eine ODER-Verkn�pfung gefiltert werden soll
   8. String: dritter Suchstring nach dem �ber eine UND_NICHT-Verkn�pfung gefiltert werden soll
*/

//$kal_RssKopfLink='';     // abweichend <link> im Kopf des RSS-Feeds
//$kal_RssImage='';        // abweichendes <image>-Icon f�r den RSS-Feed als kompletter URL
//$kal_RssLink='';         // abweichender <link> im <item>-Bereich
//$kal_RssIntervall='C';   // 1: heute, 2..28: Tage, A..L: 1..12 Monate, @: Archiv
//$kal_RssAbHeute=true;    // true oder false falls Beginn ab Anzeigedauer des Kalenders
//$kal_RssMitEnde=true;    // false oder true damit zweites Datumsfeld als Ende aufgefasst wird
//$kal_RssMitZeit=true;    // false oder true um Uhrzeit zu ber�cksichtigen
//$kal_RssAnzahl=10;       // Anzahl der <item>-Beitr�ge, Null f�r alle
//$kal_RssSortFeld=1;      // Position des Sortierfeldes in der Terminstruktur z.B. 1: nach Datum sortiert
//$kal_RssRueckw=true;     // false oder true f�r R�ckw�rts-Sortierung
//$kal_RssSprache='de-de'; // Sprachangabe f�r den RSS-Feed
//$kal_RssZeichensatz=2;   // 0: ISO-8859-1, 2: UTF-8
//$kal_RssBBFormat=0;      // eventuellen BB-Code umwandeln in  0: blanker Text, 1: so lassen, 2: HTML-Code
?>