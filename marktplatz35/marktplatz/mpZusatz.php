<?php
function fMpSeite(){
 $sSeite=(defined('MP_Zusatz')?MP_Zusatz:'??');
 if(!$X=@implode('',(file_exists(MP_Pfad.'mpZusatz'.$sSeite.'.htm')?file(MP_Pfad.'mpZusatz'.$sSeite.'.htm'):array(''))))
  $X='<p class="mpFehl">'.str_replace('#',$sSeite,MP_ZusatzHtm).'</p>';
 return $X;
}