<?php

 $sRelPfad='../';

/* -------------------------------------------------------------
 das ist die relative Pfadangabe,
 die vom Admin-Ordner aus auf das Programmverzeichnis marktplatz
 verweist mit einem / am Ende
 Die Angabe ist zu ändern, wenn der Admin-Ordner NICHT wie üblich
 direkt unterhalb von marktplatz als marktplatz/admin liegt.
 Beispiel: $Pfad='../';
---------------------------------------------------------------- */

 //error_reporting(E_ALL ^ E_NOTICE);
 error_reporting(E_ALL);

 @include $sRelPfad.'mpWerte.php';

 define('NL',"\n"); $Meld=''; $MTyp='Fehl'; $DbC=NULL;
 if(MP_SQL) if($DbC=@mysql_connect(MP_SqlHost,MP_SqlUser,MP_SqlPass)){
  if(strlen(AM_SqlZs)>0) mysql_query("SET NAMES '".AM_SqlZs."'",$DbC);
  if(!@mysql_select_db(MP_SqlDaBa,$DbC)){mysql_close($DbC); $DbC=NULL;}
 }
?>
