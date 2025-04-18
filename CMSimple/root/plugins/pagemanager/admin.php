<?php // utf-8-marker: äöü

if (!defined('CMSIMPLE_VERSION') || preg_match('#/pagemanager/admin.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/**
 * Back-End of Pagemanager.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker
 */

/*
Bugfix for CMSimple 5.9: ge-webdesign.de 2023-01
ready for php 8: ge-webdesign.de 2021
adapted for CMSimple 5: ge-webdesign.de 2020
adapted for CMSimple 4: ge-webdesign.de 2012-2020 
*/

if (!defined('CMSIMPLE_VERSION')) 
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// activate backend template, if exists
if(file_exists($pth['folder']['templates'].'__cmsimple_backend__/template.htm') && isset($pagemanager))
{
	$pth['folder']['template'] = $pth['folder']['templates'].'__cmsimple_backend__/';
	$pth['file']['template'] = $pth['folder']['template'].'template.htm';
	$pth['file']['stylesheet'] = $pth['folder']['template'].'stylesheet.css';
	$pth['folder']['menubuttons'] = $pth['folder']['template'].'menu/';
	$pth['folder']['templateimages'] = $pth['folder']['template'].'images/';
	$cf['meta']['robots'] = 'noindex, nofollow';
}

if($adm && stristr($_SERVER['QUERY_STRING'],'&pagemanager') && !stristr($_SERVER['QUERY_STRING'],'&edit'))header('Location: ./?&pagemanager&edit');

define('PAGEMANAGER_VERSION', 'CMSimple_4.8.2');

$plugin_cf['pagemanager']['verbose']="true";
$plugin_cf['pagemanager']['toolbar_show']="true";
$plugin_cf['pagemanager']['toolbar_vertical']="false";
$plugin_cf['pagemanager']['treeview_theme']="cmsimple";
$plugin_cf['pagemanager']['treeview_animation']="200";

if($cf['pagemanager']['pagedata_attribute'] != 'published')
{
	$plugin_cf['pagemanager']['pagedata_attribute'] = 'linked_to_menu';
}
else
{
	$plugin_cf['pagemanager']['pagedata_attribute'] = 'published';
}

// Reads content.htm and sets $pagemanager_h.

function pagemanager_rfc() 
{
    global $pth, $tx, $cf, $pagemanager_h, $pagemanager_no_rename;

    $c = array();
    $pagemanager_h = array();
    $u = array();
    $l = array();
    $empty = 0;
    $duplicate = 0;

    $content = file_get_contents($pth['file']['content']);
    $stop = $cf['menu']['levels'];
    $split_token = '#@CMSIMPLE_SPLIT@#';


    $content = preg_split('~</body>~i', $content);

	if($cf['use']['h1only_pagesplitting'] == 'true')
	{
		$content = preg_replace('~<h1.*class=".*_level[1-6]_page_.*"~i', $split_token . '$0', $content[0]);
	}
	else
	{
		$content = preg_replace('~<h[1-' . $stop . ']~i', $split_token . '$0', $content[0]);
	}

    $content = explode($split_token, $content);
    array_shift($content);

    foreach ($content as $page) 
	{
		$c[] = $page;

		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			preg_match('~<h1.*class=".*_level([1-6])_page_.*".*>(.*)</h~isU', $page, $temp);
		}
		else
		{
			preg_match('~<h([1-' . $stop . ']).*>(.*)</h~isU', $page, $temp);
		}

		$l[] = $temp[1];
		$temp_h[] = trim(strip_tags($temp[2]));
		$pagemanager_no_rename[] = preg_match('/.*?<.*?/isU', $temp[2]);
	}

    $cl = count($c);
    $s = -1;

    if ($cl == 0) 
	{
		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			$c[] = '<h1 class="_level1_page_">' . $tx['toc']['newpage'] . '</h1>';
		}
		else
		{
			$c[] = '<h1>' . $tx['toc']['newpage'] . '</h1>';
		}

		$pagemanager_h[] = trim(strip_tags($tx['toc']['newpage']));
		$pagemanager_no_rename[] = preg_match('/.*?<.*?/isU', $tx['toc']['newpage']);
		$l[] = 1;
		$s = 0;
		return;
    }

    foreach ($temp_h as $i => $pagemanager_heading) 
	{
        if ($pagemanager_heading == '') 
		{
            $empty++;
            $pagemanager_heading = $tx['toc']['empty'] . ' ' . $empty;
        }
		$pagemanager_h[$i] = $pagemanager_heading;
    }
}


// Returns plugin version information.

function pagemanager_version() 
{
	global $tx;
	return '
<p><a href="./?pagemanager"><b>&laquo; Pagemanager</b></a></p>
<div class="plugintext">
<div class="plugineditcaption">
Pagemanager for CMSimple
</div>
<hr />
<p>' . $tx['message']['plugin_standard1'] . '</p>
<p>' . $tx['message']['plugin_standard2'] . ' <a href="./?file=config&amp;action=array"><b>' . $tx['filetype']['config'] . '</b></a></p>
<hr />
<p>Author: 2011-2012 <a href="https://3-magi.net">Christoph M. Becker</a></p>
<p>Adapted for CMSimple 4.0 and higher: <a href="https://www.ge-webdesign.de/" target="_blank">ge-webdesign.de</a></p>
<p>Pagemanager is based on <a href="https://old.jstree.com/">jsTree</a>.</p>
</div>';
}


/*
Returns the toolbar.

@param  string $image_ext  The image extension (.gif or .png).
@param  string $save_js    The js code for onclick.
@return string	      The (x)html.
*/

function pagemanager_toolbar($image_ext, $save_js) 
{
    global $pth, $plugin_cf, $tx;

    $imgdir = $pth['folder']['plugins'].'pagemanager/images/';
    $horizontal = strtolower($plugin_cf['pagemanager']['toolbar_vertical']) != 'true';
    $res = '<div id="pagemanager-toolbar" class="'.($horizontal ? 'horizontal' : 'vertical').'">'."\n";
    $toolbar = array('save', 'separator', 'expand', 'collapse', 'separator', 'create', 'create_after', 'rename', 'delete', 'separator', 'cut', 'copy','paste', 'paste_after', 'separator', 'help');
    foreach ($toolbar as $tool) 
	{
		$link = ($tool != 'help' ? 'href="#"' : 'href="#"');
		$img = $imgdir . $tool . ($tool != 'separator' || !$horizontal ? '' : '_v') . $image_ext;
		$class = $tool == 'separator' ? 'separator' : 'tool';

		$res.= ($tool != 'separator' ? '<a ' . $link . ' class="pl_tooltip"' . ($tool == 'save' ? ' style="display: none"' : '') . '>' : '');
		$res.= '<img class="' . $class . '" src="' . $img . '"' . ($tool != 'help' ? ' onclick="pagemanager_do(\'' . $tool . '\'); return false;" alt="' . $tx['pagemanager']['op_' . $tool] . '"' : ' alt="' . $tx['pagemanager']['op_' . $tool] . '"') . '><span>' . $tx['pagemanager']['op_' . $tool] . '</span>' . "\n";
		$res.= ($tool != 'separator' ? '</a>' : '');
	}
	$res .= '</div>'."\n";
	return $res;
}


// Instanciate the pagemanager.js template.

function pagemanager_instanciateJS($image_ext) 
{
    global $pth, $plugin_cf, $cf, $tx;

    $js = rf($pth['folder']['plugins'].'pagemanager/pagemanager.js');

	preg_match_all('/<<<PT_(.*)>>>/', $js, $options);
	foreach ($options[1] as $opt)
	{
		$pagemanager_tx[$opt] = addcslashes($tx['pagemanager'][$opt],"\0'\"\\\f\n\r\t\v");
	}

// replace config variables
	$js = str_replace('<<<PC_verbose>>>', 'true', $js);
	$js = str_replace('<<<PC_treeview_animation>>>', '200', $js);
	$js = str_replace('<<<PC_treeview_theme>>>', 'cmsimple', $js);
	
// replace language variables
	$js = str_replace('<<<PT_button_cancel>>>', $pagemanager_tx["button_cancel"], $js);
	$js = str_replace('<<<PT_button_delete>>>', $pagemanager_tx["button_delete"], $js);
	$js = str_replace('<<<PT_button_ok>>>', $pagemanager_tx["button_ok"], $js);
	
	$js = str_replace('<<<PT_error_cant_rename>>>', $pagemanager_tx["error_cant_rename"], $js);
	$js = str_replace('<<<PT_error_offending_extension>>>', $pagemanager_tx["error_offending_extension"], $js);
	
	$js = str_replace('<<<PT_message_confirm_deletion>>>', $pagemanager_tx["message_confirm_deletion"], $js);
	$js = str_replace('<<<PT_message_confirm_leave>>>', $pagemanager_tx["message_confirm_leave"], $js);
	$js = str_replace('<<<PT_message_delete_last>>>', $pagemanager_tx["message_delete_last"], $js);
	$js = str_replace('<<<PT_message_menu_level>>>', $pagemanager_tx["message_menu_level"], $js);
	$js = str_replace('<<<PT_message_no_selection>>>', $pagemanager_tx["message_no_selection"], $js);
	$js = str_replace('<<<PT_message_warning_leave>>>', $pagemanager_tx["message_warning_leave"], $js);
	
	$js = str_replace('<<<PT_op_create>>>', $pagemanager_tx["op_create"], $js);
	$js = str_replace('<<<PT_op_create_after>>>', $pagemanager_tx["op_create_after"], $js);
	$js = str_replace('<<<PT_op_rename>>>', $pagemanager_tx["op_rename"], $js);
	$js = str_replace('<<<PT_op_delete>>>', $pagemanager_tx["op_delete"], $js);
	$js = str_replace('<<<PT_op_cut>>>', $pagemanager_tx["op_cut"], $js);
	$js = str_replace('<<<PT_op_copy>>>', $pagemanager_tx["op_copy"], $js);
	$js = str_replace('<<<PT_op_paste>>>', $pagemanager_tx["op_paste"], $js);
	$js = str_replace('<<<PT_op_paste_after>>>', $pagemanager_tx["op_paste_after"], $js);
	
	$js = str_replace('<<<PT_treeview_loading>>>', $pagemanager_tx["treeview_loading"], $js);
	$js = str_replace('<<<PT_treeview_new>>>', $pagemanager_tx["treeview_new"], $js);
	
// replace the rest
	if($cf['use']['h1only_pagesplitting'] == 'true')
	{
		$js = str_replace('<<<MENU_LEVELS>>>', '6', $js);
	}
	else
	{
		$js = str_replace('<<<MENU_LEVELS>>>', $cf['menu']['levels'], $js);
	}
    $js = str_replace('<<<TOC_DUPL>>>', $tx['toc']['dupl'], $js);
    $js = str_replace('<<<IMAGE_EXT>>>', $image_ext, $js);
    $js = str_replace('<<<IMAGE_DIR>>>', $pth['folder']['plugins'].'pagemanager/images/', $js);

    return '<!-- initialize jstree -->'."\n" . '<script type="text/javascript">' . "\n" . '/* <![CDATA[ */' . $js . '/* ]]> */' . "\n" . '</script>'."\n";
}


// Emits the page administration (X)HTML.

function pagemanager_edit() 
{
    global $hjs, $pth, $o, $h, $l, $cf, $plugin, $plugin_cf, $tx, $u, $pagemanager_h, $pagemanager_no_rename, $pd_router, $csrfSession;

    include_once($pth['folder']['plugins'].'jquery/jquery.inc.php');
    include_jQuery();
    include_jQueryUI();
    include_jQueryPlugin('jsTree', $pth['folder']['plugins'] . 'pagemanager/jstree/jquery.jstree.js');
	include_jQueryPlugin('cookies', $pth['folder']['plugins'] . 'pagemanager/jstree/jquery.cookie.js');

    $image_ext = (file_exists($pth['folder']['plugins'].'pagemanager/images/help.png')) ? '.png' : '.gif';

    pagemanager_rfc();

    $bo = '';

    $swo = '
<div id="pagemanager-structure-warning" class="cmsimplecore_warning">
<p>' . $tx['pagemanager']['error_structure_warning'] . '</p>
<p><a href="javascript:pagemanager_confirmStructureWarning();">' . $tx['pagemanager']['error_structure_confirmation'] . '</a></p>
</div>' . "\n";

	$save_js = 'jQuery(\'#pagemanager-xml\')[0].value =' . ' jQuery(\'#pagemanager\').jstree(\'get_xml\', \'nest\', -1,new Array(\'id\', \'title\', \'pdattr\'))';
	$bo.= '<form id="pagemanager-form" action="./?&amp;pagemanager&amp;edit" method="post">';
	if($cf['use']['csrf_protection'] == 'true') $bo.= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";

    $bo.= strtolower($plugin_cf['pagemanager']['toolbar_show']) == 'true' ? pagemanager_toolbar($image_ext, $save_js) : '';

    // output the treeview of the page structure
    // uses ugly hack to clean up irregular page structure
    $irregular = FALSE;
    $pd = $pd_router->find_page(0);
	

	$bo.= '
	<script>
	function pm_pageLink(pm_pageLinkTarget){window.location.href = pm_pageLinkTarget;}
	</script>
	';


    $bo.= '
<!-- page structure -->
<div id="pagemanager" ondblclick="jQuery(\'#pagemanager\').jstree(\'toggle_node\');">
<ul>
<li id="pagemanager-0" title="' . str_replace('"','&quot;',$pagemanager_h[0]) . '"' . ' pdattr="' . ($pd[$plugin_cf['pagemanager']['pagedata_attribute']] == '' ? '1' : $pd[$plugin_cf['pagemanager']['pagedata_attribute']]) . '"' . ($pagemanager_no_rename[0] ? ' class="pagemanager-no-rename"' : '') . '><a>'.$pagemanager_h[0].'</a><span class="pagelink" onclick="pm_pageLink(\'?' . $u[0] . '\')">&raquo;</span>';
    $stack = array();
    for ($i = 1; $i < count($h); $i++) 
	{
		$ldiff = $l[$i] - $l[$i-1];
		if ($ldiff <= 0) 
		{ // same level or decreasing
			$bo .= '</li>'."\n";
			if ($ldiff != 0 && count($stack) > 0) 
			{
				$jdiff = array_pop($stack);
				if ($jdiff + $ldiff > 0) 
				{
					array_push($stack, $jdiff + $ldiff);
					$ldiff = 0;
				} 
				else 
				{
					$ldiff += $jdiff - 1;
				}
			}
			for ($j = $ldiff; $j < 0; $j++)
			{
				$bo .= '</ul></li>'."\n";
			}
		}
		else 
		{ // level increasing
			if ($ldiff > 1) 
			{
				array_push($stack, $ldiff);
				$irregular = TRUE;
			}
			$bo .= "\n".'<ul>'."\n";
		}
		$pd = $pd_router->find_page($i);
		
		$bo.= '<li id="pagemanager-' . $i . '"' . ' title="' . str_replace('"','&quot;',$pagemanager_h[$i]) . '"' . ' pdattr="' . ($pd[$plugin_cf['pagemanager']['pagedata_attribute']] == '' ? '1' : $pd[$plugin_cf['pagemanager']['pagedata_attribute']]) . '"' . ($pagemanager_no_rename[$i] ? ' class="pagemanager-no-rename"' : '') . '><a>' . $pagemanager_h[$i] . '</a><span class="pagelink" onclick="pm_pageLink(\'?' . $u[$i] . '\')">&raquo;</span>';
	}
	$bo .= '</ul>
</div>'."\n";

	if ($irregular)
	{
		$o .= $swo;
	}

	$o .= $bo;
	$o .= pagemanager_instanciateJS($image_ext);

    // HACK?: send 'edit' as query param to prevent the last if clause in
    //		rfc() to insert #CMSimple hide#
	$o.= '<input type="hidden" name="admin" value="">
<input type="hidden" name="action" value="plugin_save">
<input type="hidden" name="xml" id="pagemanager-xml" value="">
<input id="pagemanager-submit" type="submit" class="submit" value="'.ucfirst($tx['action']['save']).'" onclick="'.$save_js.'" style="display: none">
</form>
<div id="pagemanager-footer">&nbsp;</div>
';

	$o .= '
<div id="pagemanager-confirmation" title="' . $tx['pagemanager']['message_confirm'] . '?"></div>
<div id="pagemanager-alert" title="' . $tx['pagemanager']['message_information'] . '"></div>
';
}


// Handles start elements of jsTree's xml result.

function pagemanager_start_element_handler($parser, $name, $attribs) 
{
	global $o, $pagemanager_state;
	if ($name == 'ITEM') 
	{
		$pagemanager_state['level']++;
		$pagemanager_state['id'] = $attribs['ID'] == '' ? '' : preg_replace('/(copy_)?pagemanager-([0-9]*)/', '$2', $attribs['ID']);
		$pagemanager_state['title'] = str_replace('&quot;','"',htmlspecialchars($attribs['TITLE'],ENT_QUOTES,'UTF-8'));
		$pagemanager_state['pdattr'] = $attribs['PDATTR'];
		$pagemanager_state['num']++;
    }
}


// Handles end elements of jsTree's xml result.

function pagemanager_end_element_handler($parser, $name) 
{
	global $pagemanager_state;
	if ($name == 'ITEM')
	{
		$pagemanager_state['level']--;
	}
}


// Handles character data of jsTree's xml result.

function pagemanager_cdata_handler($parser, $data) 
{
	global $pth, $c, $h, $cf, $pagemanager_fp, $pagemanager_state, $pagemanager_pd,$pd_router, $plugin_cf;
	$data = htmlspecialchars($data,ENT_QUOTES,'UTF-8');
	if (isset($c[$pagemanager_state['id']])) // existing pages
	{
		$cnt = $c[$pagemanager_state['id']];

		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			$cnt = preg_replace('/<h1(.*)class="(.*)_level[1-6]_page_(.*)"(.*)>' . '((<[^>]*>)*)[^<]*((<[^>]*>)*)<\/h1>/i', 
'<h1$1class="$2_level' . $pagemanager_state['level'] . '_page_$3">${5}'.addcslashes($pagemanager_state['title'], '$\\').'$7' . '</h1>', $cnt, 1);
		}
		else
		{
			$cnt = preg_replace('/<h[1-' . $cf['menu']['levels'].']([^>]*)>' . '((<[^>]*>)*)[^<]*((<[^>]*>)*)<\/h[1-' . $cf['menu']['levels'] . ']([^>]*)>/i',
 '<h' . $pagemanager_state['level'].'$1>${2}'.addcslashes($pagemanager_state['title'], '$\\').'$4' . '</h'.$pagemanager_state['level'] . '$6>', $cnt, 1);
		}

		fwrite($pagemanager_fp, rmnl($cnt."\n"));
	}
	else // new page
	{
		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			fwrite($pagemanager_fp, '<h1 class="_level' . $pagemanager_state['level'] . '_page_">' . $pagemanager_state['title'] . '</h1>
<p><img src="' . $pth['folder']['base'] . 'userfiles/images/flags/en.gif" alt=""><br>This is a new CMSimple page.<br>Please never format, change or edit the page title above, as it is systemically relevant for CMSimple. Please use the page manager for this.</p>
<p>You can overwrite this content now.</p>
<p><img src="' . $pth['folder']['base'] . 'userfiles/images/flags/de.gif" alt=""><br>Dies ist eine neue CMSimple-Seite.<br>Bitte formatieren, ändern oder bearbeiten Sie niemals den Seitentitel ganz oben, da er für CMSimple systemrelevant ist. Bitte verwenden Sie dazu den Pagemanager.</p>
<p>Sie können diesen Inhalt jetzt überschreiben.</p>
');
		}
		else
		{
			fwrite($pagemanager_fp, '<h' . $pagemanager_state['level'] . '>' . $pagemanager_state['title'] . '</h'.$pagemanager_state['level'] . '><p>...</p>' . "\n");
		}
	}

	if ($pagemanager_state['id'] == '') 
	{
		$pd = $pd_router->new_page(array());
	} 
	else 
	{
		$pd = $pd_router->find_page($pagemanager_state['id']);
	}
	$pd['url'] = uenc($pagemanager_state['title']);
	$pd[$plugin_cf['pagemanager']['pagedata_attribute']] = $pagemanager_state['pdattr'];
	$pagemanager_pd[] = $pd;
}


// Saves content.htm manually and pagedata.php via $pd_router->model->refresh()

function pagemanager_save($xml) 
{
	csrfProtection();
	global $pth, $tx, $pd_router, $pagemanager_state, $pagemanager_fp, $pagemanager_pd;
	$pagemanager_pd = array();
	$parser = xml_parser_create('UTF-8');
	xml_set_element_handler($parser, 'pagemanager_start_element_handler','pagemanager_end_element_handler');
	xml_set_character_data_handler($parser, 'pagemanager_cdata_handler');
	$pagemanager_state['level'] = 0;
	$pagemanager_state['num'] = -1;
	if ($pagemanager_fp = fopen($pth['file']['content'], 'w')) 
	{
		fputs($pagemanager_fp, '<?php // utf8-marker = äöü
if(!defined(\'CMSIMPLE_VERSION\') || preg_match(\'/content.php/i\', $_SERVER[\'SCRIPT_NAME\']))
{
	die(\'No direct access\');
}
?>
');
		xml_parse($parser, $xml, TRUE);
		fclose($pagemanager_fp);
		$pd_router->model->refresh($pagemanager_pd);
	} 
	else
	{
		e('cntwriteto', 'content', $pth['file']['content']);
	}
	rfc(); // is neccessary, if relocation fails!
}


// Plugin administration
 
if (isset($pagemanager)) 
{
	// check requirements (RELEASE-TODO)
	define('PAGEMANAGER_PHP_VERSION', '4.3.0');
	

	
	if (version_compare(PHP_VERSION, PAGEMANAGER_PHP_VERSION) < 0)
	{
		$e.= '<li>'.sprintf($tx['pagemanager']['error_phpversion'], PAGEMANAGER_PHP_VERSION).'</li>'."\n";
	}
	
	foreach (array('pcre', 'xml') as $ext) 
	{
		if (!extension_loaded($ext))
		{
			$e.= '<li>'.sprintf($tx['pagemanager']['error_extension'], $ext).'</li>'."\n";
		}
	}
	
	if (!file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php'))
	{
		$e.= '<li>'.$tx['pagemanager']['error_jquery'].'</li>'."\n";
	}
	
	if (strtolower($tx['meta']['codepage']) != 'utf-8') 
	{
		$e.= '<li>'.$tx['pagemanager']['error_encoding'].'</li>'."\n";
	}

	initvar('admin');
	initvar('action');

	if (!isset($pmplugininfo)) 
	{
		$o.='<p><a href="./?pagemanager&amp;pmplugininfo"><b>Plugin Info &raquo;</b></a></p>';
	}
	
	if ($action == 'plugin_save') 
	{
		csrfProtection();
		pagemanager_save(stsl($_POST['xml']));
	}
	
	if (isset($pmplugininfo)) 
	{
		$o.= pagemanager_version();
	}
	else
	{
		$o.= '<p style="font-size: 14px;"><img src="' . $pth['folder']['base'] . 'plugins/pagemanager/images/showpage.gif" style="float: left; margin: 3px 8px 0 0;" alt="">&raquo; ';
		if($plugin_cf['pagemanager']['pagedata_attribute'] == 'linked_to_menu')
		{
			$o.= $tx['pagemanager']['show_in_menu'];
		}
		else
		{
			$o.= $tx['pagemanager']['publish_page'];
		}
		$o.= '</p>';
//		$o.= '<p>' . $tx['pagemanager']['text_doubleclick'] . '</p>';
		$o.= pagemanager_edit();
	}
}

?>