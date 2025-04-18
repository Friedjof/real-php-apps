<?php

if (!defined('CMSIMPLE_VERSION') || preg_match('#/page_params/page_params_view.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/* utf8-marker = äöü */

// $sn => ./ ge-webdesign.de 2024-01
// adapted for CMSimple 4 and 5 ge-webdesign.de 2012-2023

/**
 * Page-Parameters - module page_params_view
 *
 * Creates the menu for the user to change
 * page-parameters per page.
 *
 * @author Martin Damken
 * @link http://www.zeichenkombinat.de
 * @version 1.0.06
 * @package pluginloader
 * @subpackage page_params
 */
 
/**
 * page_params_view()
 * 
 * @param array $page Gets cleaned of unallowed 
 * doublequotes, that will destroy input-fields
 * @return string $view Returns the created view
 * @global string $sn Scriptname (base-directory)
 * @global string $su Selected-URL (query-string)
 * @global array $tx Plugin-texts
 * @global array $tx CMSimple-texts
 * @global array $pth CMSimple-pathes
 * @global array $cf CMsimple-config-settings
 */
function page_params_view($page){
	global $sn, $su, $tx, $pth, $cf, $csrfSession;
	
	$strChecked = ' checked';
	$strDisabled = ' disabled';
	$strSelected = ' selected';
	if ($cf['xhtml']['endtags'] == 'true'){
		$strChecked .= '="checked"';
		$strDisabled .= '="disabled"';
		$strSelected .= '="selected"';
	}
	
	$lang = $tx['page_params'];
	$help_icon = '<img src="' . $pth['folder']['base'] . 'css/icons/help_icon.gif" alt="" class="helpicon">';

	$view ="\n".'<form action="./?'.$su.'" method="post" id = "page_params" name = "page_params">';	
	$view .= "\n\t".'<p><b>'.$lang['form_title'].'</b></p>';
	if($cf['use']['csrf_protection'] == 'true') $view .= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";

############# alternative heading #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_heading']))) . '</span></a>';
	$view .= "\n\t".'<span class = "pp_label">'.$lang['heading'].'</span><br>';

	
	$checked = '';
	if($page['show_heading'] == '1'){
		$checked = $strChecked;
	}
	$js = " onclick=\"window.document.page_params.heading.disabled = false;\"";

	$view .= "\n\t\t" . '<input type="radio" name="show_heading" value="1" id = "show_heading_yes"' . $js . $checked . '><label for="show_heading_yes">'.$lang['yes'].'</label>';
	$checked = '';
	$disabled = '';
	if($page['show_heading'] !== '1'){
		$checked = $strChecked;
		$disabled = $strDisabled;
	}
	$js = " onclick=\"window.document.page_params.heading.disabled = true;\"";
    $view .= "\n\t\t".'<input type="radio" name="show_heading" value="0" id = "show_heading_no"'.$checked . $js . '><label for="show_heading_no">'.$lang['no'].'</label><br>';
	$view .= "\n\t\t".'<input type="text" size = "50" name="heading" id = "other_heading" value="'. str_replace('"', '&quot;', $page['heading']).'"'.$disabled . '<br>';
	$view .= "\n\t".'<hr>';
	
############# alternative sitetitle #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_sitetitle']))) . '</span></a>';
	$view .= "\n\t".'<span class = "pp_label">'.$lang['sitetitle'].'</span><br>';

	
	$checked = '';
	if($page['show_sitetitle'] == '1'){
		$checked = $strChecked;
	}
	$js = " onclick=\"window.document.page_params.sitetitle.disabled = false;\"";

	$view .= "\n\t\t".'<input type="radio" name="show_sitetitle" value="1" id = "show_sitetitle_yes"'.$js. $checked . '><label for="show_sitetitle_yes">'.$lang['yes'].'</label>';
	$checked = '';
	$disabled = '';
	if($page['show_sitetitle'] !== '1'){
		$checked = $strChecked;
		$disabled = $strDisabled;
	}
	$js = " onclick=\"window.document.page_params.sitetitle.disabled = true;\"";
    $view .= "\n\t\t".'<input type="radio" name="show_sitetitle" value="0" id = "show_sitetitle_no"'.$checked .$js.'><label for="show_sitetitle_no">'.$lang['no'].'</label><br>';
	$view .= "\n\t\t".'<input type="text" size = "50" name="sitetitle" id = "other_sitetitle" value="'. str_replace('"', '&quot;', $page['sitetitle']).'"'.$disabled.'><br>';
	$view .= "\n\t".'<hr>';
	
############# published #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_published']))) . '</span></a>';
	$view .= "\n\t".'<span class = "pp_label">'.$lang['published'] .'</span><br>';
	
	$checked = '';
	if($page['published'] !== '0'){
		$checked = $strChecked;
	}
	$view .= "\n\t\t".'<input type="radio" name="published" value="1" id = "published_yes"'.$checked.'><label for="published_yes">'.$lang['yes'].'</label>';
	$checked = '';
	if($page['published'] == '0'){
		$checked = $strChecked;
	}
    $view .= "\n\t\t".'<input type="radio" name="published" value="0" id = "published_no"'.$checked.'><label for="published_no">'.$lang['no'].'</label><br>';
    $view .= "\n\t".'<hr>';

############# linked to menu #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_linked_to_menu']))) . '</span></a>';
	$view .= "\n\t".'<span class = "pp_label">'.$lang['linked_to_menu'] .'</span><br>';
	
	$checked = '';
	if($page['linked_to_menu'] !== '0'){
		$checked = $strChecked;
	}
	$view .= "\n\t\t".'<input type="radio" name="linked_to_menu" value="1" id = "linked_to_menu_yes"'.$checked.'><label for="linked_to_menu_yes">'.$lang['yes'].'</label>';
	$checked = '';
	if($page['linked_to_menu'] == '0'){
		$checked = $strChecked;
	}
    $view .= "\n\t\t".'<input type="radio" name="linked_to_menu" value="0" id = "linked_to_menu_no"'.$checked.'><label for="linked_to_menu_no">'.$lang['no'].'</label><br>';
    $view .= "\n\t".'<hr>';
	
############# template chooser #############
	if(isset($page['template']) && trim($page['template']) !== '') {
		$selected = '';
		$template = $page['template'];
	} else {
		$selected = $strSelected;
	}
	$handle = opendir($pth['folder']['templates']);

        $templates_select = "\n".'<select name="template">';
	$templates_select .= "\n\t".'<option value="0"'. $selected.'>'.$tx['page_params']['use_default_template'].'</option>';
	$templates = array();
	while(false !== ($file = readdir($handle))) {   // einsammeln
		if
		(
			is_dir($pth['folder']['templates'].$file) && 
			strpos($file, '.') !== 0 && 
			strpos($file, '__') !== 0 || 
			$file == '__cmsimple_default__') 
			{
				$templates[] = $file;
			}
		}
	natcasesort($templates);                         // sortieren
	foreach($templates as $file){                   // options schreiben
		$selected = '';
		if(isset($template) && $file == $template) {$selected = $strSelected;  }
		$templates_select .= "\n\t".'<option value="'.$file.'"'. $selected.'>'.$file.'</option>';
        }
	$templates_select .= "\n".'</select>';
	$view .= "\n"."\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_template']))) . '</span></a>';
	$view .= "\n\t".'<span class="pp_label">'.$lang['template'].'</span>'.'<br>'.$templates_select .'<br>';
	$view .= "\n\t".'<hr>';

############# last edit #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_last_edit']))) . '</span></a>';
	$view .= "\n\t".'<span class="pp_label">'.$lang['show_last_edit'].'</span>';
	$view .=  '<br>';
	$checked = '';
	if($page['show_last_edit'] == '1'){
		$checked = $strChecked;
	}
	$view .= "\n\t\t".'<input type="radio" name="show_last_edit" value="1" id = "last_edit_yes"'.$checked.'><label for="last_edit_yes">'.$lang['yes'].'</label>';
	$checked = '';
	if($page['show_last_edit'] !== '1'){
		$checked = $strChecked;
	}
    $view .= "\n\t\t".'<input type="radio" name="show_last_edit" value="0" id="last_edit_no"'.$checked.'><label for="last_edit_no">'.$lang['no'].'</label><br>';
	
	if($page['last_edit'] !== ''){
		$view .= "\n\t\t".'&nbsp;&nbsp;('.$lang['last_edit'];
		$view .=  date($tx['lastupdate']['dateformat'],(int)$page['last_edit']).')';
	}
	$view .= "\n\t".'<hr>';

############# header_location #############
	$view .= "\n\t".'<a class="pl_tooltip" href="#">'.$help_icon.'<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$lang['hint_header_location']))) . '</span></a>';
	$view .= "\n\t".'<span class = "pp_label">'.$lang['header_location'].'</span><br>';
	
	$checked = '';
	if($page['use_header_location'] == '1'){
		$checked = $strChecked;
	}
	$js = " onclick=\"window.document.page_params.header_location.disabled = false;\"";

	$view .= "\n\t\t".'<input type="radio" name="use_header_location" value="1" id="header_location_yes"'.$js. $checked.'><label for="header_location_yes">'.$lang['yes'].'</label>';
	$checked = '';
	$disabled = '';
	if($page['use_header_location'] !== '1'){
		$checked = $strChecked;
		$disabled = $strDisabled;
	}
	$js = " onclick=\"window.document.page_params.header_location.disabled = true;\"";
    $view .= "\n\t\t".'<input type="radio" name="use_header_location" value="0" id="header_location_no"'.$checked .$js.'><label for="header_location_no">'.$lang['no'].'</label><br>';
	$view .= "\n\t\t".'<input type="text" size="50" name="header_location" id="other_header_location" value="'. str_replace('"', '&quot;', $page['header_location']).'"'.$disabled . '><br>';
	$view .= "\n\t";
################################################

	$view .= "\n\t".'<input name = "save_page_data" type = "hidden">';
	$view .= "\n\t".'<div style="text-align: right">';
	$view .= "\n\t\t".'<input type="submit" class="submit" style="float: right; margin-right: 0; cursor: pointer;" value="'.$lang['submit'].'"><br>';
	$view .= "\n\t".'</div>';
	$view .= "\n".'</form>';
	return $view;
}
?>