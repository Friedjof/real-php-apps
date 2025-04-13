<?php
include 'hilfsFunktionen.php';
echo fSeitenKopf('Teilnehmerkennungen','','ETk');

$MTyp='Fehl'; $DDl='';  //Listenaktionen

if($_SERVER['REQUEST_METHOD']=='POST'){
 foreach($_POST as $k=>$xx) if(substr($k,0,3)=='del'&&strpos($k,'x')>0) $nDel=(int)substr($k,3); reset($_POST);
 if($nDel>0){ //löschen
  if($nDel==$_POST['ddl']){
   if(!UMF_SQL){ //Textdaten
    $aE=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $aD=explode(';',rtrim($aE[0]));
    array_splice($aD,$nDel,1); $aE[0]=trim(implode(';',$aD)).NL;
    if($f=fopen(UMF_Pfad.UMF_Daten.UMF_Ergebnis,'w')){
     fwrite($f,rtrim(str_replace("\r",'',implode('',$aE))).NL); fclose($f);
     $sMeld='Der Eintrag wurde gelöscht.'; $MTyp='Erfo';
    }else $sMeld=str_replace('#',UMF_Daten.UMF_Ergebnis,UMF_TxDateiRechte);
   }elseif($DbO){ //SQL-Daten
    if($rR=$DbO->query('SELECT Inhalt FROM '.UMF_SqlTabE.' WHERE Nummer="0"')){
     $aE=$rR->fetch_row(); $rR->close(); $aD=explode(';',rtrim($aE[0])); array_splice($aD,$nDel,1);
     if($DbO->query('UPDATE IGNORE '.UMF_SqlTabE.' SET Inhalt="'.trim(implode(';',$aD)).'" WHERE Nummer="0"')){
      $sMeld='Der Eintrag wurde gelöscht.'; $MTyp='Erfo';
     }else $sMeld=UMF_TxSqlAendr;
    }
   }
  }else{$DDl=$nDel; $sMeld='Den Eintrag Nummer '.$nDel.' wirklich löschen?';}
 }else{$sMeld='Die Einträge bleiben unverändert'; $MTyp='Meld';}
}else{$sMeld='Die letzten <a href="konfAblauf.php">'.UMF_IPAdressen.' Teilnehmer</a> hinterließen folgende Kennungen:'; $MTyp='Meld';} //GET

if(!UMF_SQL){ //Textdaten
 $aE=@file(UMF_Pfad.UMF_Daten.UMF_Ergebnis); $aD=explode(';',rtrim($aE[0]));
}elseif($DbO){ //SQL-Daten
 if($rR=$DbO->query('SELECT Inhalt FROM '.UMF_SqlTabE.' WHERE Nummer="0"')){
  $aE=$rR->fetch_row(); $rR->close(); $aD=explode(';',rtrim($aE[0]));
 }
}

echo '<p class="adm'.$MTyp.'">'.$sMeld.'</p>';
?>

<form name="umfListe" action="ipListe.php<?php if(KONF>0)echo'?konf='.KONF?>" method="post">
<table class="admTabl" border="0" cellpadding="3" cellspacing="1">
 <tr class="admTabl">
  <td>Nr</td>
  <td style="width:99%">Eintrag (IP-Adresse, Browsertyp)</td>
  <td>&nbsp;</td>
 </tr>
<?php for($i=1;$i<=UMF_IPAdressen;$i++){ ?>
 <tr class="admTabl">
  <td style="text-align:center;"><?php echo $i?></td>
  <td><?php echo (isset($aD[$i])?$aD[$i]:'&nbsp;')?></td>
  <td><input type="image" src="iconLoeschen.gif" name="del<?php echo $i?>" width="12" height="13" border="0" title="Eintrag <?php echo $i?> löschen" /></td>
 </tr>
<?php }?>
</table>
<input type="hidden" name="ddl" value="<?php echo $DDl?>" />
</form>

<?php echo fSeitenFuss();?>