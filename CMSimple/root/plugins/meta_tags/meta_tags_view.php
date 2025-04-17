<?php

if (!defined('CMSIMPLE_VERSION') || preg_match('#/meta_tags/meta_tags_view.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/* utf8-marker = äöüß */

// $sn => ./ ge-webdesign.de 2024-01
// prepared for php 7.2 ge-webdesign.de 2018-07
// adapted for CMSimple 4 and 5 ge-webdesign.de 2012-2023

/**  
removed deprecated function create_function()

 * Meta-Tags - module meta_tags_view
 *
 * Creates the menu for the user to change
 * meta-tags (description, keywords, title
 * and robots) per page.
 *
 * @author Martin Damken
 * @link http://www.zeichenkombinat.de
 * @version 1.0.02 
 * @package pluginloader
 * @subpackage meta_tags
 */ 
/**
 * meta_tags_view()
 * 
 * @param array $page Gets cleaned of unallowed 
 * doublequotes, that will destroy input-fields
 * @return string $view Returns the created view
 */
function meta_tags_view($page){
	global $cf, $su, $tx, $pth, $csrfSession;

	$lang = $tx['meta_tags'];
	$help_icon = '<img src="' . $pth['folder']['base'] . 'css/icons/help_icon.gif" alt="" class="helpicon">';
	
	$my_fields = array('title', 'description', 'keywords', 'robots');

	$view ="\n".'<form action="./?'.$su.'" method="post" id="meta_tags">';	
	$view .= "\n\t".'<p><b>'.$lang['form_title'].'</b></p>';
	if($cf['use']['csrf_protection'] == 'true') $view .= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";

	foreach($my_fields as $field){
		$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_'.$field]))) . '</span></a>';
		$view .= "\n\t".'<label for = "'.$field.'"><span class = "mt_label">'.$lang[$field] .'</span></label><br>';
		$view .= "\n\t\t".'<input type="text" size="50" name="'.$field.'" id="'.$field.'" value="'. str_replace('"','&quot;',$page[$field]) . '"><hr>';
	}
	$view .= "\n\t".'<input name="save_page_data" type="hidden">';
	$view .= "\n\t".'<div style="text-align: right;">';
	$view .= "\n\t\t".'<input type="submit" class="submit" style="float: right; margin-right: 0; cursor: pointer;" value="'.$lang['submit'].'"><br>';
	$view .= "\n\t".'</div>';
	$view .= "\n".'</form>';
	return $view;
}
?>