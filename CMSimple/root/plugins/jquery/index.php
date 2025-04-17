<?php // utf8-marker: äöü 

if (!defined('CMSIMPLE_VERSION') || preg_match('#/jquery/index.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/* 
=========================================================
Adapted for CMSimple 4.0 and higher: 
Gert Ebersbach 2013
ge-webdesign.de
=========================================================
based on "jQuery for CMSimple" version 1.3 2011-07-27
author: Holger Irmler - cmsimple.holgerirmler.de
*/

if (!defined('CMSIMPLE_VERSION') || strstr($_SERVER['SCRIPT_NAME'],'/jquery/index.php')) 
{
    die('no direct access');
}

if($cf['jquery']['autoload'] == "1")
{
	include_once($pth['folder']['plugins'].'jquery/jquery.inc.php');
	include_jQuery();
	include_jQueryUI();
}
?>