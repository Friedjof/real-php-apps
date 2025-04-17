<?php // utf8-marker = äöü

/*
================================================== 
This file is a part of CMSimple 5.19
Released: 2025-03-14
Project website: www.cmsimple.org
================================================== 
CMSimple COPYRIGHT INFORMATION

(c) Gert Ebersbach - mail@ge-webdesign.de

CMSimple is released under the GPL3 licence. 
You may not remove copyright information from the files. 
Any modifications will fall under the copyleft conditions of GPL3.

GPL 3 Deutsch: http://www.gnu.de/documents/gpl.de.html
GPL 3 English: http://www.gnu.org/licenses/gpl.html

HISTORY:
-------------------------------------------------- 
2020-04-21 CMSimple 5 - www.cmsimple.org
(c) Gert Ebersbach - mail@ge-webdesign.de
-------------------------------------------------- 
2012-11-11 CMSimple 4 - www.cmsimple.org
(c) Gert Ebersbach - mail@ge-webdesign.de
-------------------------------------------------- 
CMSimple_XH 1.5.3
2012-03-19
based on CMSimple version 3.3 - December 31. 2009
For changelog, downloads and information please see http://www.cmsimple-xh.com
-------------------------------------------------- 
CMSimple version 3.3 - December 31. 2009
Small - simple - smart
(c) 1999-2009 Peter Andreas Harteg - peter@harteg.dk 

END CMSimple COPYRIGHT INFORMATION
================================================== 
*/

if (preg_match('/adm.php/i', $_SERVER['SCRIPT_NAME'])){die('Access Denied');}

$pth['folder']['toolbars'] = $pth['folder']['base'] . 'plugins/' . $cf['editor']['external'] . '/inits/';
$pth['folder']['jquery'] = $pth['folder']['base'] . 'plugins/jquery/lib/jquery';
$pth['folder']['jquery_ui'] = $pth['folder']['base'] . 'plugins/jquery/lib/jquery_ui';
$pth['folder']['jquery_ui_css'] = $pth['folder']['base'] . 'plugins/jquery/lib/jquery_ui/css';


// delete old config variables

if(isset($cf['security']['type'])) unset($cf['security']['type']);
if(isset($cf['filebrowser']['show_images_permanent'])) unset($cf['filebrowser']['show_images_permanent']);
if(isset($cf['meta']['publisher'])) unset($cf['meta']['publisher']);
if(isset($cf['site']['full_settings_menu'])) unset($cf['site']['full_settings_menu']);
if(isset($cf['filebrowser']['extensions_downloads'])) unset($cf['filebrowser']['extensions_downloads']);
if(isset($cf['filebrowser']['extensions_images'])) unset($cf['filebrowser']['extensions_images']);
if(isset($cf['filebrowser']['extensions_media'])) unset($cf['filebrowser']['extensions_media']);


// Functions used for adm

function selectlist($fn, $regm, $regr) 
{
    global $k1, $k2, $v2, $o, $pth, $cf, $tx;
	
	$options = array();
    $o .= "\n" . '<select name="' . $k1 . '_' . $k2 . '" style="border: 1px solid #ccc; margin: 2px 0;">';
    if (is_dir($pth['folder'][$fn]) && $fd = opendir($pth['folder'][$fn])) 
	{
        while (($p = readdir($fd)) == true) 
		{
            if (preg_match($regm, $p)) 
			{
                $v = preg_replace($regr, "\\1", $p);
				
				if ($k1 . $k2 == 'editortinymce_toolbar')
				{
					$v = str_replace('init_', '', $v);
				}
				
                $options[$v] = ($v == $v2);
            }
        }
        closedir($fd);
    }
    
	if(is_array($options))ksort($options, SORT_STRING);

    foreach ($options as $option => $selected)
    {
		if
		(
		 // template fallback (hide standard templates) and hide default.php in languages
			$option != '__fallback__' 
			&& $option != '__maintenance__' 
			&& $option != '__cmsimple_backend__' 
			&& $option != '__cmsimple_filebrowser__' 
			&& $option != 'default'
		)
		{
			$o .= "\n" . '<option style="padding: 1px 8px 1px 3px;" value="' . $option . '"';
			if ($selected)
			{
				$o .= ' selected="selected"';
			}
		$o .= '>' . $option . '</option>';
		}
        
    }
    $o .= "\n" . '</select>';
}

// Adm functionality

if ($adm) 
{
	// restore backup
	if (isset($_GET['restore']) && isset($_GET['restore_pf']) && $_GET['securityCode'] == $_SESSION[$csrfSession])
	{
		$o = '
	<h4>Restore Backup</h4>
	';

		if (file_exists('./backups/cmsimple/' . $_GET['restore_cf'] . '.php'))
		{
			$restoreDate = date("Ymd_His");
			
			// creates backup of content.php and pagedata.php
			copy ($pth['file']['content'], './backups/cmsimple/' . $restoreDate . '-backup-by-restore' . '_content.php');
			copy ($pth['file']['pagedata'], './backups/cmsimple/' . $restoreDate . '-backup-by-restore' . '_pagedata.php');
			
			// restore chosen backup
			copy ('./backups/cmsimple/' . $_GET['restore_cf'] . '.php', $pth['folder']['content'] . 'content.php');
			copy ('./backups/cmsimple/' . $_GET['restore_pf'] . '.php', $pth['folder']['content'] . 'pagedata.php');
			
			if(strstr($_GET['restore_cf'], '-backup-by-restore_'))
			{
				// delete chosen backup
				unlink ('./backups/cmsimple/' . $_GET['restore_cf'] . '.php');
				if(file_exists('./backups/cmsimple/' . $_GET['restore_pf'] . '.php'))unlink ('./backups/cmsimple/' . $_GET['restore_pf'] . '.php');
			}

			$o .= '<hr>

	<a href="./?backups">&laquo; ' . $tx['restore']['back_to'] . '</a>
	';

			header("Location: ./?restored&restore_cf=" . $_GET['restore_cf'] . "&restore_pf=" . $_GET['restore_pf']);
		}
	}

	if (isset($_GET['restored']))
	{
		$o = '
	<h4>' . $tx['restore']['restore_backups'] . '</h4>
	';

		$o .= '<br><p><b>' . $tx['restore']['backups_created'] . ':</b></p>
	<ul>
	<li>content.php</li>
	<li>pagedata.php</li>
	</ul>

	<br><p><b>' . $tx['restore']['backups_restored'] . ':</b></p>
	<ul>
	<li>' . $_GET['restore_cf'] . '.php => content.php</li>
	<li>' . $_GET['restore_pf'] . '.php => pagedata.php</li>
	</ul>

	<br>

	<p><a href="./?backups"><b>&laquo; ' . $tx['restore']['back_to'] . '</b></a></p>
	<hr>
	';
	}
	// END restore backup


	// delete backup
	if (isset($_GET['delete_pfbackup']) && isset($_GET['delete_cfbackup']) && $_GET['securityCode'] == $_SESSION[$csrfSession])
	{
		$o = '
	<h4>' . $tx['restore']['headline_deleted_backup'] . '</h4>
	';

		if (file_exists('./backups/cmsimple/' . $_GET['delete_pfbackup'] . '.php')) unlink ('./backups/cmsimple/' . $_GET['delete_pfbackup'] . '.php');
		if (file_exists('./backups/cmsimple/' . $_GET['delete_cfbackup'] . '.php')) unlink ('./backups/cmsimple/' . $_GET['delete_cfbackup'] . '.php');

			$o .= '<hr>

	<a href="./?backups">&laquo; ' . $tx['restore']['back_to'] . '</a>
	';

			header("Location: ./?backup_deleted&deleted_cfbackup=" . $_GET['delete_cfbackup'] . "&deleted_pfbackup=" . $_GET['delete_pfbackup']);
	}

	if (isset($_GET['backup_deleted']))
	{
		$o = '
	<h4>' . $tx['restore']['headline_deleted_backup'] . ':</h4>
	';

		$o .= '
	<div class="cmsimplecore_warning" style="float: left; padding: 2px 16px;">
	<p><b>' . $_GET['deleted_cfbackup'] . '.php</b> ' . $tx['result']['deleted'] . '</p>
	<p><b>' . $_GET['deleted_pfbackup'] . '.php</b> ' . $tx['result']['deleted'] . '</p>
	</div>

	<br style="clear: both;">

	<p><a href="./?backups"><b>&laquo; ' . $tx['restore']['back_to'] . '</b></a></p>
	<hr>
	';
	}
	// END delete backup


	// delete other file in content folder
	if (isset($_GET['delete_of']) && $_GET['securityCode'] == $_SESSION[$csrfSession])
	{
		$o = '
	<h4>' . $tx['restore']['headline_delete_file'] . '</h4>
	';

		if (file_exists('./backups/cmsimple/' . $_GET['delete_of']))
		{
			unlink ('./backups/cmsimple/' . $_GET['delete_of']);
		}

	$o = '<a href="./?backups">&laquo; ' . $tx['restore']['back_to'] . '</a>
	<br style="clear: both;">
	<hr>
	';

		header("Location: ./?deleted_of&deleted_of=" . $_GET['delete_of']);
	}

	if (isset($_GET['deleted_of']))
	{
		$o .= '
	<h4>' . $tx['restore']['headline_delete_file'] . '</h4>
	<p class="cmsimplecore_warning" style="float: left; padding: 5px 16px;"><b>' . $_GET['deleted_of'] . ' </b> ' . $tx['result']['deleted'] . '</p>
	<br style="clear: both;">
	<br>
	<p><a href="./?backups"><b>&laquo; ' . $tx['restore']['back_to'] . '</b></a></p>
	<hr>
	';
	}
	// END delete other file in content folder

    if ($validate)
        $f = 'validate';
    if ($settings)
        $f = 'settings';
    if (isset($sysinfo))
        $f = 'sysinfo';
    if (isset($phpinfo))
        $f = 'phpinfo';
    if ($file)
        $f = 'file';
    if ($userfiles)
        $f = 'userfiles';
    if ($images || $function == 'images')
        $f = 'images';
    if ($downloads || $function == 'downloads')
        $f = 'downloads';
    if ($function == 'save')
        $f = 'save';

    if ($f == 'settings' || $f == 'images' || $f == 'downloads' || $f == 'validate' || $f == 'sysinfo' || $f == 'phpinfo') 
    {
        $title = $tx['title'][$f];
        $o .= "\n\n" . '<h1>' . $title . '</h1>' . "\n";
    }


// System Info, Version Check and Help Links

	if($cmsimple_pwHasher->CheckPassword('test', $cf['security']['password']))
	{
		$o = '
<div class="cmsimplecore_warning" style="clear: both; text-align: center;">
<p>' . $tx['message']['password_default'] . ' ' . $tx['adminmenu']['settings'] . '&nbsp;=>&nbsp;<a href="./?file=config&action=array"><b>' . $tx['adminmenu']['configuration'] . '</b></a></p>
</div>
' . $o;
	}

	// CMSimple info

	if ($f == 'sysinfo') 
	{
		if($cf['site']['allow_versionsinfo'] == 'true')
		{
			if (function_exists('curl_version'))
			{
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, str_replace('http://','https://',CMSIMPLE_VERSIONSINFO));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				$cmsimple_current_version = explode(',', curl_exec($curl));
				curl_close($curl);
			}
			else
			{
				$cmsimple_current_version = explode(',', file_get_contents(str_replace('http://','https://',CMSIMPLE_VERSIONSINFO)));
			}
		}
		
		$o.='<hr />' . "\n";
		
		// CMSimple versioninfo output
		
        $o.= '<h4>' . $tx['sysinfo']['version'] . ':</h4>' . "\n";
        $o.= '<ul>' . "\n" . '<li>' . CMSIMPLE_VERSION . '&nbsp;&nbsp;Released: ' . CMSIMPLE_DATE . '</li>' . "\n" . '</ul>' . "\n" . "\n";
		
		if($cf['site']['allow_versionsinfo'] == 'true')
		{
			if ($cmsimple_current_version[1] == CMSIMPLE_RELEASE)
			{
				$o.='<p style="font-weight: 700; color: #060;">' . $tx['version']['cmsimple_ok'] . '</p>' . "\n";
			}
			
			if ($cmsimple_current_version[1] > CMSIMPLE_RELEASE)
			{
				$o.='<p style="font-weight: 700; color: #c60;">' . $tx['version']['update_available'] . ': &nbsp; <a href="https://www.cmsimple.org/en/?Downloads___CMSimple">' . $tx['version']['info_download'] . '&nbsp;&raquo;</a></p>' . "\n";
			}
			
			if ($cmsimple_current_version[1] < CMSIMPLE_RELEASE)
			{
				$o.='<p style="font-weight: 700; color: #c00;">' . $tx['version']['version_check_failed'] . '</p>' . "\n";
			}
		}

		$o.='<hr />' . "\n";
		
		// plugins versioninfo

		$o.='<h4>' . $tx['sysinfo']['plugins'] . ':</h4>' . "\n" . "\n";
		$o.='<p><span style="background: #090; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['active'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$o.='<span style="background: #eb0; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['hidden'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$o.='<span style="background: #f00; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['disabled'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$o.='<a href="./?&amp;cmsimple_pluginmanager"><b>Pluginmanager &raquo;</b></a></p>';
		
		$o.= '<table class="sysinfo">' . "\n";
		
		foreach($pluginlist_array as $pluginlist_item)
		{
			$o.= '<tr>' . "\n" . '<td>';
			
			if(stristr(file_get_contents('./content/_disabled_plugins.txt'), '|||' . $pluginlist_item . '|||'))
			{
				$o.= '<span style="background: #f00; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			elseif(stristr(file_get_contents('./content/_disabled_plugins.txt'), '§' . $pluginlist_item . '§'))
			{
				$o.= '<span style="background: #eb0; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			else
			{
				$o.= '<span style="background: #090; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			
			$o.= '<b>' . $pluginlist_item . '</b></td>' . "\n";
			
			if (file_exists($pth['folder']['plugins'] . $pluginlist_item . '/version.nfo') && 
			$pluginlist_item != 'jquery' && 
			$pluginlist_item != 'filebrowser' && 
			$pluginlist_item != 'pagemanager' && 
			$pluginlist_item != 'page_params' && 
			$pluginlist_item != 'meta_tags' && 
			$pluginlist_item != 'tinymce' &&
			$cf['site']['allow_versionsinfo'] == 'true')
			{
				// define pluginversion user array
				$cmsimple_pluginversion_user = explode(',', trim(file_get_contents($pth['folder']['plugins'] . $pluginlist_item . '/version.nfo')));
				
				
				// define pluginversion provider array
				if (function_exists('curl_version'))
				{
					$curl = curl_init();
					
					if(strpos($cmsimple_pluginversion_user[6],'ge-webdesign.de'))
					{
						curl_setopt($curl, CURLOPT_URL, str_replace('http://','https://',$cmsimple_pluginversion_user[6]));
					}
					else
					{
						curl_setopt($curl, CURLOPT_URL, $cmsimple_pluginversion_user[6]);
					}
					
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
					$cmsimple_pluginversion_provider = explode(',', curl_exec($curl));
					curl_close($curl);
				}
				else
				{
					if(strpos($cmsimple_pluginversion_user[6],'ge-webdesign.de'))
					{
						$cmsimple_pluginversion_provider = explode(',', file_get_contents(str_replace('http://','https://',$cmsimple_pluginversion_user[6])));
					}
					else
					{
						$cmsimple_pluginversion_provider = explode(',', file_get_contents($cmsimple_pluginversion_user[6]));
					}
				}
				
				if($cmsimple_pluginversion_user[1] == $cmsimple_pluginversion_provider[1])
				{
					$o.= '<td><span style="color: #060;"> ' . $tx['version']['current_version_installed'] . ' </span>(' . $cmsimple_pluginversion_provider[2] . ')</td>' . "\n" . '<td></td>' . "\n";
				}
				
				if($cmsimple_pluginversion_user[1] < $cmsimple_pluginversion_provider[1])
				{
					$o.= '<td><span style="color: #c60;"><b> ' . $tx['version']['update_available'] . ' </b></span></td>' . "\n" . '<td><a href="' . htmlspecialchars(strip_tags($cmsimple_pluginversion_provider[5]), ENT_QUOTES, 'UTF-8') . '">' . $tx['version']['info_download'] . '&nbsp;&raquo;</a></td>' . "\n";
				}
				
				if($cmsimple_pluginversion_user[1] > $cmsimple_pluginversion_provider[1])
				{
					$o.= '<td><span style="color: #c00;"> ' . $tx['version']['version_check_failed'] . ' </span></td>' . "\n" . '<td></td>' . "\n";
				}
			}
			else
			{
				if($pluginlist_item == 'jquery' || $pluginlist_item == 'filebrowser' || $pluginlist_item == 'pagemanager' || $pluginlist_item == 'page_params' || $pluginlist_item == 'meta_tags' || $pluginlist_item == 'tinymce')
				{
					$o.= '<td><span style="font-style: italic; color: #909;">' . $tx['version']['standard_plugin'] . '</span></td>' . "\n" . '<td></td>' . "\n";
				}
				else
				{
					if($cf['site']['allow_versionsinfo'] == 'true')
					{
						$o.= '<td> ' . $tx['version']['no_versionsinfo'] . '</td>' . "\n" . '<td></td>' . "\n";
					}
					else
					{
						$o.= '<td>&nbsp;</td>' . "\n" . '<td></td>' . "\n";
					}
				}
			}
			$o.= '</tr>' . "\n" . "\n";
		}
		
		$o.='</table>' . "\n";

		$o.='<hr />' . "\n";
		
		// php version info
		
        $o.= '<p><b>' . $tx['sysinfo']['php_version'] . '</b></p>' . "\n" . '<ul>' . "\n" . '<li>' . phpversion() . '</li>' . "\n" . '<li><a href="./?&phpinfo" target="blank"><b>' . $tx['sysinfo']['phpinfo_link'] . '</b></a> &nbsp; ' . $tx['sysinfo']['phpinfo_hint'] . '</li>' . "\n" . '</ul>' . "\n" . "\n";

		$o.='<hr />' . "\n";
		
		// help and info links
		
        $o.='<h4>' . $tx['sysinfo']['helplinks'] . '</h4>' . "\n" . "\n";
        $o.='<ul>
<li><a href="https://www.cmsimple.org/">cmsimple.org &raquo;</a></li>
<li><a href="https://www.cmsimple.org/forum/">CMSimple Forum &raquo;</a></li>
</ul>' . "\n" . "\n";
		$o.='<hr />' . "\n";
    }

// PHP Info

    if ($f == 'phpinfo') {
        phpinfo();
        exit;
    }


// SETTINGS

	$cmsimpleAdmin = '';
	if(file_exists('./content/cmsimpleAdmin.php'))
	{
		include('./content/cmsimpleAdmin.php');
		$cmsimpleAdmin = md5($cmsimpleAdminUser); 
		if(isset($_POST['user'])){$_SESSION['cmsimpleAdmin'] = md5($_POST['user']);}
	}

	if ($f == 'settings') 
	{
		// CoAuthors
		if($cf['site']['allow_config'] == 'true' || isset($_SESSION['cmsimpleAdmin']) && $_SESSION['cmsimpleAdmin'] == $cmsimpleAdmin)
		{
			$o .= '
<p>' . $tx['settings']['warning'] . '</p>
<ul>
<li><a href="./?file=config&amp;action=array">' . $tx['action']['edit'] . ' ' . $tx['filetype']['config'] . ' &raquo;</a></li>
<li><a href="./?file=language&amp;action=array">' . $tx['action']['edit'] . ' ' . $tx['filetype']['language'] . ' &raquo;</a></li>
<li><a href="./?file=template&amp;action=edit">' . $tx['action']['edit'] . ' ' . $tx['filetype']['template'] . ' &raquo;</a></li>
<li><a href="./?file=stylesheet&amp;action=edit">' . $tx['action']['edit'] . ' ' . $tx['filetype']['stylesheet'] . ' &raquo;</a></li>
<li><a href="?backups">' . $tx['restore']['headline_backups'] . ' &raquo;</a></li>
<li><a href="./?file=log&amp;action=view" target="_blank">' . $tx['action']['view'] . ' ' . $tx['filetype']['log'] . ' &raquo;</a> ' . $tx['sysinfo']['phpinfo_hint'] . '</li>';
			if($cf['site']['allow_linkcheck'] == 'true')
			$o .= '<li><a href="./?&amp;validate">' . ucfirst($tx['adminmenu']['validate']) . '</a></li>';
			$o .= '<li><a href="./?&amp;sysinfo">' . ucfirst($tx['adminmenu']['sysinfo']) . '</a></li>
</ul>
';
		}
		else
		{
			$o .= '
<p class="cmsimplecore_warning" style="text-align: center;">No permission - keine Berechtigung</p>
';
		}
	}


// changed backup-area, added pagedata.php download, removed edit-funktion, added backupexplain3

	if (isset($backups))
	{
		$o .= '
<h4>' . $tx['restore']['headline_backups'] . '</h4>
<p>' . $tx['settings']['backupexplain3'] . '</p>
';

		// restore - show content.php and pagedata.php
		foreach (array('content', 'pagedata') as $i)
		{
			$o .= '
<p><a href="./?file=' . $i . amp() . 'action=view" title="' . $tx['restore']['view_file'] . '">' . ucfirst($tx['filetype'][$i]) . '</a>' . ' (' . (round((filesize($pth['folder']['content'] . $i . '.php')) / 102.4) / 10) . ' KB) | 
<a href="./?file=' . $i . amp() . 'action=download"><b>' . $tx['action']['download'] . ' &raquo;</b></a> 
</p>' . "\n";
		}
		$o .= '
<hr>
<br>
<b>' . $tx['restore']['backup_files'] . ':</b>
<p>' . $tx['settings']['backupexplain1'] . '</p>
<p>' . $tx['settings']['backupexplain2'] . '</p>
<p>' . $tx['settings']['warning'] . '</p>
<hr>
';

		// show backup files
		$fs = sortdir('./backups/cmsimple/');
		$backup_files = array();

		// create backup files array
		foreach ($fs as $bf_elements)
		{
			if (preg_match("/\d{3}_content\.php/", $bf_elements))
			{
				$backup_files[] = $bf_elements;
				$backup_files[] = str_ireplace('content','pagedata',$bf_elements);
			}
		}

		if(count($backup_files) > 0)
		{
			foreach ($backup_files as $p)
			{
				$o .= '
<p>
<a href="./?file=' . $p . amp() . 'action=view" title="' . $tx['restore']['view_file'] . '">' . $p . '</a> (' . (round((filesize('./backups/cmsimple/' . $p)) / 102.4) / 10) . ' KB)
</p>';

				if (preg_match("/\d{3}_pagedata\.php/", $p))
				{
					$o .= '
<p>
<a href="?delete_backup&delete_pfbackup=' . str_ireplace('.php','',$p) . '&delete_cfbackup=' . str_ireplace('pagedata','content',str_ireplace('.php','',$p)) . '&securityCode=' . $_SESSION[$csrfSession] . '" onClick="return confirm(\'' . $tx['restore']['confirm_delete_backup'] . '\')"> <img src="' . $pth['folder']['base'] . 'css/icons/delete.gif" style="margin-bottom: -3px;" alt=""> <b>' . $tx['restore']['delete_backup'] . '</b></a> &nbsp;|&nbsp; <a href="?restore&restore_pf=' . str_ireplace('.php','',$p) . '&restore_cf=' . str_ireplace('pagedata','content',str_ireplace('.php','',$p)) . '&securityCode=' . $_SESSION[$csrfSession] . '" onClick="return confirm(\'' . $tx['restore']['confirm_restore_backup'] . '\')"> <img src="' . $pth['folder']['base'] . 'css/icons/restore.gif" style="margin-bottom: -3px;" alt=""> <b>' . $tx['restore']['restore_backup'] . '</b></a>
</p>
';
				}

				if (preg_match("/\d{3}_pagedata/", $p)) $o .= '<hr>' . "\n";
			}
		}
		else
		{
			$o .= '
<p>' . $tx['restore']['no_backups_in_content_folder'] . '</p>
<hr>';
		}

		// create other files array
		$other_files = array();
		foreach ($fs as $of_elements)
		{
			if (!preg_match("/\d{3}_content\.php|\d{3}_pagedata\.php|.htaccess/", $of_elements) && !is_dir('./backups/cmsimple/' . $of_elements) && $of_elements != 'content.php' && $of_elements != 'pagedata.php' && strpos($of_elements, '.') != 0)
			{
				$other_files[] = $of_elements;
			}
		}
		
		$o .= '
<br>
<b>' . $tx['restore']['other_files'] . ':</b>'; 

		if (count($other_files) > 0)
		{
			$o .= '
<hr>
';

			foreach ($other_files as $of)
			{
				if(preg_match("/\d{3}-backup-by-restore_content\.php/", $of) && file_exists('./backups/cmsimple/' . str_replace('content','pagedata',$of)))
				{
					$o .= '
<div style="background: #d6d6d0; padding: 1px 12px 3px 12px; margin: 6px 0;">';
				}

				$o .= '
<p>
<a href="?delete_of&delete_of=' . $of . '&securityCode=' . $_SESSION[$csrfSession] . '" title="' . $tx['restore']['delete_file'] . '" onClick="return confirm(\'' . $tx['restore']['confirm_delete_file'] . '\')"><img src="' . $pth['folder']['base'] . 'css/icons/delete.gif" style="margin-bottom: -3px;" alt=""></a>&nbsp; 
<a href="./?restoreFileView&file=' . $of . amp() . 'action=view" title="' . $tx['restore']['view_file'] . '">' . $of . '</a> (' . (round((filesize('./backups/cmsimple/' . $of)) / 102.4) / 10) . ' KB)';

				$o .= '
</p>
';
				if (preg_match("/\d{3}-backup-by-restore_pagedata\.php/", $of) && file_exists('./backups/cmsimple/' . str_replace('pagedata','content',$of)))
				{
					$o .= '
<p><a href="?delete_backup&delete_pfbackup=' . str_ireplace('.php','',$of) . '&delete_cfbackup=' . str_ireplace('pagedata','content',str_ireplace('.php','',$of)) . '&securityCode=' . $_SESSION[$csrfSession] . '" onClick="return confirm(\'' . $tx['restore']['confirm_delete_backup'] . '\')"> <img src="' . $pth['folder']['base'] . 'css/icons/delete.gif" style="margin-bottom: -3px;" alt=""> <b>' . $tx['restore']['delete_backup'] . '</b></a> 
&nbsp;|&nbsp;<a href="?restore&restore_pf=' . str_ireplace('.php','',$of) . '&restore_cf=' . str_ireplace('pagedata','content',str_ireplace('.php','',$of)) . '&securityCode=' . $_SESSION[$csrfSession] . '" onClick="return confirm(\'' . $tx['restore']['confirm_restore_backup'] . '\')"> <img src="' . $pth['folder']['base'] . 'css/icons/restore.gif" style="margin-bottom: -3px;" alt=""> <b>' . $tx['restore']['restore_backup'] . '</b></a>
</p>';
				}

				if(preg_match("/\d{3}-backup-by-restore_pagedata\.php/", $of) && file_exists('./backups/cmsimple/' . str_replace('pagedata','content',$of)))
				{
					$o .= '
</div>
';
				}
			}
		$o .= '<hr>
';
		}
		else
		{
			$o .= '
<p>' . $tx['restore']['no_other_files_in_content_folder'] . '</p>
<hr>
';
		}
	}

// END modified backup-area

    if ($f == 'file') 
	{
        if (preg_match("/\d{3}_content\.php|\d{3}_pagedata\.php/", $file) || isset($restoreFileView))
            $pth['file'][$file] = $pth['folder']['content'] . $file;
		if (isset($pth['file'][$file]) && $pth['file'][$file] != '') 
		{
            if ($action == 'view' && $cf['site']['allow_config'] == 'true' || $action == 'view' && isset($_SESSION['cmsimpleAdmin']) && $cmsimpleAdmin == $_SESSION['cmsimpleAdmin']) 
			{
                header('Content-Type: text/plain');
                echo rmnl(rf($pth['file'][$file]));
                exit;
            }
            if ($action == 'download') 
			{
                download($pth['file'][$file]);
            } 
			else 
			{
                initvar('form');
                if ($action == 'array')
                    $form = 'array';
                if ($form == 'array') 
				{
                    if ($file == 'language')
                        $a = 'tx';

                    if ($file == 'config') 
					{
                        $a = 'cf';
                    }
					
                    if ($file == 'plugin_config') 
					{
                        $a = 'plugin_cf';
                    }
					
                    if ($file == 'plugin_language') 
					{
                        $a = 'plugin_tx';
                    }
                }
                if ($action == 'save') 
				{
					// csrf protection
					csrfProtection();
					
					if ($form == 'array') 
					{
                        $text = "<?php\n";
                        $text.= "/* utf8-marker = äöüß */\n";
                        foreach ($GLOBALS[$a] as $k1 => $v1) 
						{
                            if (is_array($v1)) 
							{
                                foreach ($v1 as $k2 => $v2) 
								{
                                    if (!is_array($v2)) 
									{
                                        initvar($k1 . '_' . $k2);
                                        $GLOBALS[$a][$k1][$k2] = stsl($GLOBALS[$k1 . '_' . $k2]);
                                        if (($k1 == 'security' && $GLOBALS[$a][$k1][$k2] != '') && $k2 == 'password') 
										{
                                            if ($GLOBALS[$a][$k1][$k2] != stsl($_POST[$k1 . '_password_old'])) 
											{
                                                $GLOBALS[$a][$k1][$k2] = $cmsimple_pwHasher->HashPassword($GLOBALS[$a][$k1][$k2]);
                                            }
                                        }
                                        $text .= '$' . $a . '[\'' . $k1 . '\'][\'' . $k2 . '\']="' . addcslashes($GLOBALS[$a][$k1][$k2], "\0..\37\"\$\\") . '";' . "\n";
                                    }
                                }
                            }
                        }
                        $text .= '?>';
                    }
                    else
					{
                        $text = stsl($text);
					}
					
                    if ($fh = fopen($pth['file'][$file], "w")) 
					{
                        fwrite($fh, $text);
                        fclose($fh);
						if (!include($pth['file']['language']))
						{
							die('Language file ' . $pth['file']['language'] . ' missing');
						}
                    }
                    else
                        e('cntwriteto', $file, $pth['file'][$file]);
                }
                $title = ucfirst($tx['action']['edit']) . ' ' . (isset($tx['filetype'][$file]) ? $tx['filetype'][$file] : $file);
				
				
                $o .= '<p style="width: 100%; float: left; height: 1px;"></p><br /><h1>' . $title . '</h1>' . "\n";
				
				if (isset($_SESSION['cmsimpleAdmin']) && $_SESSION['cmsimpleAdmin'] == $cmsimpleAdmin)
				{
					$o.= '<p style="color: #900;"><b>Logged in as Admin</b></p>';
				}
				
				// warning if not exists own config.php of a subsite or second language
				if($file == 'config' && !file_exists('./config.php') && !file_exists('./cmsimple/config.php'))
				{
					$o.='<p class="cmsimplecore_warning" style="text-align: center;">' . $tx['message']['own_configfile'] . '</p>';
				}
				
				if(isset($_POST['cmsimpleDataStored']))
				{
					$o.='<p class="cmsimplecore_warning" style="text-align: center; max-width: 320px; margin-bottom: 16px;">' . $tx['message']['file_saved'] . '</p>' . "\n";
					$o .= '<ul style="font-weight: 700;">' . "\n";
					$o .= '<li><a href="./?file=config&amp;action=array">' . $tx['action']['edit'] . ' ' . $tx['filetype']['config'] . ' &raquo;</a></li>' . "\n";
					$o .= '<li><a href="./?file=language&amp;action=array">' . $tx['action']['edit'] . ' ' . $tx['filetype']['language'] . ' &raquo;</a></li>' . "\n";
					$o .= '<li><a href="./?file=template&amp;action=edit">' . $tx['action']['edit'] . ' ' . $tx['filetype']['template'] . ' &raquo;</a></li>' . "\n";
					$o .= '<li><a href="./?file=stylesheet&amp;action=edit">' . $tx['action']['edit'] . ' ' . $tx['filetype']['stylesheet'] . ' &raquo;</a></li>' . "\n";
					$o .= '</ul>' . "\n";
				}
				else
				{
					$o .= '<form action="./' . (isset($plugin) ? '?' . amp() . $plugin : '') . '" method="post" autocomplete="off">';
					
					$o .= '<input type="hidden" name="cmsimpleDataStored" value="cmsimpleDataStored">' . "\n";
					if($cf['use']['csrf_protection'] == 'true') $o .= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";
					
					// CoAuthors
					if (($form == 'array' && $cf['site']['allow_config'] == 'true') || ($form == 'array' && isset($_SESSION['cmsimpleAdmin']) && $cmsimpleAdmin == $_SESSION['cmsimpleAdmin'])) 
					{
						$o .= '<input type="submit" class="submit" value="' . ucfirst($tx['action']['save']) . '">' . "\n";
						$o .= '<table width="100%" cellpadding="1" cellspacing="0" border="0">' . "\n";
						foreach ($GLOBALS[$a] as $k1 => $v1) 
						{
							if (!isset($plugin) || $k1 == $plugin) 
							{
								if($file=='config' || $file=='language')
								{
									$o .= '<tr>' . "\n" . '<td colspan="2"><h4>' . ucfirst($k1) . '</h4></td>' . "\n" . '</tr>' . "\n";
								}
								if (is_array($v1))
								{
									foreach ($v1 as $k2 => $v2)
									{
										if (!is_array($v2)) 
										{
											$o .= '<tr>' . "\n" . '<td valign="top" style="min-width: 250px;">';
											if (isset($tx['help'][$k1 . '_' . $k2]) && ($a == 'cf'))
											$o .= '<a href="#" class="pl_tooltip">' . '<img src = "' . $pluginloader_cfg['folder_pluginloader'] . 'css/help_icon.png" alt="" class="helpicon">' . '<span style="padding: 10px 9px 12px 9px;">' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$tx['help'][$k1 . '_' . $k2]))) . '</span></a>' . "\n";
											$o .= "\n" . ucfirst($k2) . ':</td>' . "\n" . '<td style="width: 90%;">';
											if ($k1 == 'security' && $k2 == 'password') 
											{
												$o .= '<input type="hidden" name="' . $k1 . '_' . $k2 . '_old" value="' . $v2 . '">';
											}
											if ($k1 . $k2 == 'securitytype') 
											{
												$o .= 'Deprecated - not in use anymore.';
											}
											
											else if ($k1 . $k2 == 'securitypassword')
											{
												$o .='<input type="password" class="cmsimplecore_settings" name="' . $k1 . '_' . $k2 . '" value="' . $cf['security']['password'] . '">';
											}
											
											else if ($k1 . $k2 == 'languagedefault')
											{
												selectlist('language', "/.php/i", "/.php/i");
											}
											else if ($k1 . $k2 == 'sitetemplate')
											{
												selectlist('templates', "/^[^\.]*$/i", "/^([^\.]*)$/i");
											}
											else if ($k1 . $k2 == 'editortinymce_toolbar')
											{
												selectlist('toolbars', "/.js/i", "/.js/i");
											}
											else if ($k1 . $k2 == 'jqueryfile_core')
											{
												selectlist('jquery', "/.js/i", "/nothing/i");
											}
											else if ($k1 . $k2 == 'jqueryfile_ui')
											{
												selectlist('jquery_ui', "/.js/i", "/nothing/i");
											}

											// use input fields only in CMS config
											else if ($a == 'cf')
											{
												$o .= '<input type="text" class="cmsimplecore_settings" name="' . $k1 . '_' . $k2 . '" value="' . htmlspecialchars($v2, ENT_COMPAT, 'UTF-8') . '" size="30">' . "\n";
											}

											// height of textarea depending on text length
											else if (strlen($v2) < 50)
											{
												$o .= '<textarea rows="2" cols="30" class="cmsimplecore_settings cmsimplecore_settings_short" name="' . $k1 . '_' . $k2 . '">' . htmlspecialchars($v2, ENT_NOQUOTES, 'UTF-8') . "</textarea>\n";
											}
											else
											{
												$o .= '<textarea rows="2" cols="30" class="cmsimplecore_settings" name="' . $k1 . '_' . $k2 . '">' . htmlspecialchars($v2, ENT_NOQUOTES, 'UTF-8') . "</textarea>\n";
											}
											$o .= '</td>' . "\n" . '</tr>' . "\n";
										}
									}
								}
							}
						}
						$o .= '</table>' . "\n" . '<input type="hidden" name="form" value="' . $form . '">' . "\n";
					}
					else
					{
						if($cf['site']['allow_config'] == 'true' || isset($_SESSION['cmsimpleAdmin']) && $cmsimpleAdmin == $_SESSION['cmsimpleAdmin']) // CoAuthors
						{
							$o.= '<p><b>' . $tx['adminmenu']['template'] . ':</b> ' . $cf['site']['template'] . '</p>';
							$o.= '<textarea rows="25" cols="50" name="text" class="cmsimplecore_file_edit">' . rf($pth['file'][$file]) . '</textarea>';
						}
						else
						{
							$o .= '<p class="cmsimplecore_warning" style="text-align: center;">No permission - keine Berechtigung</p>' . "\n";
						}
					}
					
					
					if (isset($admin) && $admin && $cf['site']['allow_config'] == 'true' || (isset($admin) && $admin && $cmsimpleAdmin == $_SESSION['cmsimpleAdmin'])) // CoAuthors
					{
						$o .= '<input type="hidden" name="admin" value="' . $admin . '">' . "\n";
					}
					
					if ($cf['site']['allow_config'] == 'true' || isset($_SESSION['cmsimpleAdmin']) && $cmsimpleAdmin == $_SESSION['cmsimpleAdmin']) // CoAuthors
					{
						$o .= '<input type="hidden" name="file" value="' . $file . '">' . "\n" . '<input type="hidden" name="action" value="save">' . "\n" . ' <input type="submit" class="submit" style="margin-top:1em;" value="' . ucfirst($tx['action']['save']) . '">' . "\n" . '</form>' . "\n";
					}
				}
            }
        }
    }

// linkcheck

    if ($f == 'validate') 
	{
        $o .= check_links();
    }
}


if ($s == -1 && !$f && $o == '' && $su == '') 
{
    $s = 0;
    $hs = 0;
}
// END linkcheck

// SAVE

if ($adm && $f == 'save') 
{
	csrfProtection();

    $ss = $s;
    $text = preg_replace('/<p>({{{PLUGIN:.*?|{{{function:.*?|{{{f:.*?|{{{p:.*?|#CMSimple .*?)<\/p>/is', '<div>$1</div>', $text);
    $c[$s] = $text;

    if ($s == 0)
	{
		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			if (!preg_match("~^<h1.*?class=\"_level[1-6]_page_\"[^>]*?>.*?</h1>~i", rmanl($c[0])) && !preg_match("/^(<p[^>]*>)?(\&nbsp;| |<br \/>)?(<\/p>)?$/i", rmanl($c[0])))
			{
				$c[0] = '<h1>' . $tx['toc']['missing'] . '</h1>' . "\n" . $c[0];
			}
		}
		else
		{
			if (!preg_match("/^<h1[^>]*>.*<\/h1>/i", rmanl($c[0])) && !preg_match("/^(<p[^>]*>)?(\&nbsp;| |<br \/>)?(<\/p>)?$/i", rmanl($c[0])))
			{
				$c[0] = '<h1>' . $tx['toc']['missing'] . '</h1>' . "\n" . $c[0];
			}
		}
	}

    $title = ucfirst($tx['filetype']['content']);

// 4.5
    if ($fh = fopen($pth['file']['content'], "w")) 
	{
        fwrite($fh, '<?php // utf8-marker = äöü
if(!defined(\'CMSIMPLE_VERSION\') || preg_match(\'/content.php/i\', $_SERVER[\'SCRIPT_NAME\']))
{
	die(\'No direct access\');
}
?>
');
// END 4.5

        foreach ($c as $i) 
		{
			fwrite($fh, rmnl($i . "\n"));
		}
		fclose($fh);
		
		if($cf['use']['h1only_pagesplitting'] == 'true')
		{
			preg_match('~<h1.*?class=".*?_level([1-6])_page_.*?".*?>(.*?)</h1>~isu', $c[$s], $matches);
		}
		else
		{
			preg_match('~<h([1-'.$cf['menu']['levels'].'])[^>]*>(.+?)</h[1-'.$cf['menu']['levels'].']>~isu', $c[$s], $matches);
		}

		if (count($matches) > 0) 
		{
            $temp = explode($cf['uri']['seperator'], $selected);
            array_splice($temp, -1, 1, uenc(rmnl(trim(strip_tags($matches[2])))));
            $su = implode($cf['uri']['seperator'], $temp);
        } 
		else 
		{
            $su = $u[max($s - 1, 0)];
        }
		header("Location: ./?" . $su);
    }
    else
	{
        e('cntwriteto', 'content', $pth['file']['content']);
	}
    $title = '';
}


if ($adm && $edit && (!$f || $f == 'save') && !$download) {
    if (isset($ss))
        if ($s < 0 && $ss < $cl)
            $s = $ss;
    if ($s > -1) {
        $su = $u[$s];

        $editor = $cf['editor']['external'] == '' || init_editor();
        if (!$editor) 
		{
            $e .= '<li>'.sprintf('External editor %s missing', $cf['editor']['external']).'</li>'."\n";
        }
        $o .= '<form method="post" id="ta" action="./">'; 
		if($cf['use']['csrf_protection'] == 'true') $o .= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";
		$o .= '<input type="hidden" name="selected" value="' . $u[$s] . '">'
. '<input type="hidden" name="function" value="save">'
. '<textarea name="text" id="text" class="cmsimple-editor" style="height: ' . $cf['editor']['height'] . 'px; width: 100%;" rows="30" cols="80">'
. htmlspecialchars($c[$s], ENT_NOQUOTES, 'UTF-8')
. '</textarea>';
        if ($cf['editor']['external'] == '' || !$editor) 
		{
            $o .= '<input type="submit" value="' . ucfirst($tx['action']['save']) . '">';
        }
        $o .= '
               </form>
               ';
    }
    else
	{
		if(!isset($pagemanager))$o .= '<p>' . $tx['error']['cntlocateheading'] . '</p>' . "\n";
	}
}

// edit logfile not allowed

if($file == 'log' && $action == 'edit')
{
	$o = '<p><b>No permission to edit logfile!</b></p>';
}

if ($adm && ((isset($images) && $images) 
|| (isset($downloads) && $downloads) 
|| (isset($userfiles) && $userfiles) 
|| (isset($media) && $media) 
|| $edit && (!$f || $f == 'save') && !$download)) 
{
    if ($cf['filebrowser']['external'] && !file_exists($pth['folder']['plugins'].$cf['filebrowser']['external'])) 
	{
        $e .= '<li>'.sprintf('External filebrowser %s missing', $cf['filebrowser']['external']).'</li>'."\n";
    }
}

if ($adm && $f == 'xhpages') 
{
    if ($cf['pagemanager']['external'] && !file_exists($pth['folder']['plugins'].$cf['pagemanager']['external'])) 
	{
        $e .= '<li>'.sprintf('External pagemanager %s missing', $cf['pagemanager']['external']).'</li>'."\n";
    }
}

// CoAuthors - create textfiles from h1 pages

if($cf['use']['h1only_pagesplitting'] == 'true')
{
	$menulevel1_identifierSearchTerm = '_level1_page_';
}
else
{
	$menulevel1_identifierSearchTerm = '</h1>';
}

if($cf['site']['create_content_textfiles'] == 'true' && $f == 'save' && stristr($c[$s],$menulevel1_identifierSearchTerm))
{
	csrfProtection();

	if(is_writable('./userfiles/co_author/'))
	{
		$handle = fopen('./userfiles/co_author/' . $su . '.txt', "w");
		fwrite($handle, $c[$s]);
		fclose($handle);
	}
	else
	{
		if($cf['language']['default'] == $sl)
		{
			$e.='<ul><li>Folder <b>./userfiles/co_author/"</b> not exists or not writable</li></ul>';
		}
		else
		{
			$e.='<ul><li>Folder <b>../' . $sl . '/userfiles/co_author/"</b> not exists or not writable</li></ul>';
		}
	}
}

// END CoAuthors - create textfiles from h1 pages

// pluginmanager (added)

if(isset($_GET['pm_data_saved']))
{
	csrfProtection();
	
	$displug = implode(' ', $_POST);
	if(isset($_SESSION[$csrfSession]))
	{
		$displug = str_replace($_SESSION[$csrfSession],'',$displug);
		$displug = str_replace('  ','',$displug);
		$displug = str_replace($_SESSION[$csrfSession],' ',$displug);
	}
	$handle = fopen($pm_datafile_path, 'w');
	fwrite($handle, $displug);
	fclose($handle);

	$o='<p class="cmsimplecore_warning" style="text-align: center;">' . $pluginloader_tx['pluginmanager']['message_data_saved'] . '</p>';
	$o.='<p>' . $pluginloader_tx['pluginmanager']['linktext_back_to'] . ' <a href="./?&amp;normal&amp;cmsimple_pluginmanager"><b>Pluginmanager &raquo;</b></a></p>';
}

if($adm && isset($cmsimple_pluginmanager))
{
	$o .= cmsimple_pluginmanager();
}

// END pluginmanager (added)

/* 
###################################
   A D M I N   F U N C T I O N S
################################### 
*/

/**
 * collects the links
 * calls the appropriate fucntion to check each link
 * passes the results to
 *
 *
 * @global <array> $c - the cmsimple pages
 * @global <array> $u - the urls
 * @global <array> $h - the headings
 * @global <int> $cl  - the number of pages
 * @global <string> $o - the output string
 */
function check_links() 
{
    global $c, $u, $h, $cl, $o;
    $checkedLinks = 0;
    for ($i = 0; $i < $cl; $i++) 
	{
        preg_match_all('/<a.*?href=["]*([^"]*)["]*.*?>(.*?)<\/a>/i', $c[$i], $pageLinks);
        if (count($pageLinks[1]) > 0) 
		{

// First change for linkcheck page-internal anchors
            foreach ($pageLinks[1] as $link) 
			{
                if (strpos($link, '#') === 0) 
				{
                    $hrefs[$i][] = '?' . $u[$i] . $link;
                } 
				else 
				{
                    $hrefs[$i][] = $link;
                }
            }
// END first change for linkcheck page-internal anchors

            $texts[$i] = $pageLinks[2];
            $checkedLinks += count($pageLinks[1]);
        }
    }
    $hints = array();
    $i = 0;
    foreach ($hrefs as $index => $currentLinks) 
	{
        foreach ($currentLinks as $counter => $link) 
		{
            $parts = parse_url($link);
            
			if(!isset($parts["scheme"])) $parts["scheme"] = '';
//			print_r($parts);
			switch ($parts["scheme"]) 
			{
                case 'http': $status = check_external_link($parts);
                    break;
				case 'https': $status = check_external_httpslink($parts);
                    break;
                case 'mailto': $status = 'mailto';
                    break;
                case '': $status = check_internal_link($parts);
                    break;
                default: $status = 'unknown';
            }
			
			
            if ($status == '200') 
			{
                continue;
            }
            if ($status == '400' || $status == '404' 
			|| $status == '500' || $status == 'internalfail' 
            || $status == 'externalfail' 
			|| $status == 'content not found' 
			|| $status == 'file not found') 
			{
                $hints[$index]['errors'][] = array($status, $link, $texts[$index][$counter]);
                continue;
            }
            $hints[$index]['caveats'][] = array($status, $link, $texts[$index][$counter]);
        }
        $i++;
    }
    return linkcheck_message($checkedLinks, $hints);
}


// new GE 2022-05
function check_internal_link($test) 
{
	global $c,$u,$cl,$sn,$pth,$sl,$cf,$s;  // add $pth to globals
//	echo $s;
//	$s=2;
	if(empty($_SERVER['HTTPS'])) $htpLinkcheck = 'http://'; else $htpLinkcheck = 'https://';

	if(!isset($test['path'])){$testpath = '';}else{$testpath = $test['path'];}
	if(!isset($test['query'])){$testquery = '';}else{$testquery = $test['query'];}

	$urlLinkcheck = fopen ($htpLinkcheck . $_SERVER['SERVER_NAME'] . str_replace($testpath,'',$sn) . $testpath . '?' . $testquery, "r");
	if($urlLinkcheck)
	{
		fclose($urlLinkcheck);
		return 200;
	}
	else return 'internalfail';
}

/**
 * checks web links and returns the status code
 *
 * @param <array> $parts (parsed url)
 * @return <string> status code
 */
function check_external_link($parts) 
{
	set_time_limit(60);
	if(!isset($parts['path'])){$partspath = '';}else{$partspath = $parts['path'];}
	if(!isset($parts['query'])){$partsquery = '';}else{$partsquery = '?' . $parts['query'];}
    $host = $parts['host'] . $partspath . $partsquery;
    $fh = get_headers('http://' . $host);
	if(!isset($fh[0])){$fhnull = '';}else{$fhnull = $fh[0];}
    return substr($fhnull, 9, 3);
}

function check_external_httpslink($parts) 
{
	set_time_limit(60);
	if(!isset($parts['path'])){$partspath = '';}else{$partspath = $parts['path'];}
	if(!isset($parts['query'])){$partsquery = '';}else{$partsquery = '?' . $parts['query'];}
    $host = $parts['host'] . $partspath . $partsquery;
    $fh = get_headers('https://' . $host);
	if(!isset($fh[0])){$fhnull = '';}else{$fhnull = $fh[0];}
	return substr($fhnull, 9, 3);
}

// new linkcheck
/**
 * prepares the html output for the linkcheck results
 *
 * @todo internalization
 *
 * @global <array> $tx
 * @global <array> $h
 * @global <array> $u
 * @param <int> $checkedLinks - number of checked links
 * @param <array> $hints - the errors an warnings
 * @return <string>
 */
function linkcheck_message($checkedLinks, $hints) 
{
    global $tx, $h, $u;
    $html = "\n" . '<p>' . $checkedLinks . ' ' . $tx['link']['checked'] . '</p>' . "\n";
    if (count($hints) == 0) 
	{
        $html .= '<p><b>' . $tx['link']['check_ok'] . '</b></p>' . "\n";
        return $html;
    }
    $html .= '<p><b>' . $tx['link']['check_errors'] . '</b></p>' . "\n";
    $html .= '<p>' . $tx['link']['check'] . '</p>' . "\n";
    foreach ($hints as $page => $problems) 
	{
        $html .= '<hr>' . "\n\n" . '<h4>' . $tx['link']['page'] . '<a href="?' . $u[$page] . '">' . $h[$page] . '</a></h4>' . "\n";
        if (isset($problems['errors'])) 
		{
            $html .= '<h4>' . $tx['link']['errors'] . '</h4>' . "\n" . '<ul>' . "\n";
            foreach ($problems['errors'] as $error) 
			{
                $html .= '<li>' . "\n" . '<b>' . $tx['link']['link'] . '</b><a href="' . $error[1] . '">' . $error[2] . '</a><br>' . "\n";
                $html .= '<b>' . $tx['link']['linked_page'] . '</b>' . $error[1] . '<br>' . "\n";
                if ((int) $error[0]) 
				{
                    $html .= '<b>' . $tx['link']['error'] . '</b>' . $tx['link']['ext_error_page'] . '<br>' . "\n";
                    $html .= '<b>' . $tx['link']['returned_status'] . '</b>' . $error[0];
                }
                if ($error[0] == 'internalfail') 
				{
                    $html .= '<b>' . $tx['link']['error'] . '</b>' . $tx['link']['int_error'];
                }
                if ($error[0] == 'externalfail') 
				{
                    $html .= '<b>' . $tx['link']['error'] . '</b>' . $tx['link']['ext_error_domain'];
                }
                if ($error[0] == 'content not found') 
				{
                    $html .= '<b>' . $tx['link']['error'] . '</b>' . $tx['link']['int_error'];
                }
                $html .= "\n" . '</li>' . "\n";
            }
            $html .= '</ul>' . "\n" . "\n";
        }
        if (isset($problems['caveats'])) 
		{
            $html .= '<h4>' . $tx['link']['hints'] . '</h4>' . "\n" . '<ul>' . "\n";
            foreach ($problems['caveats'] as $notice) 
			{
                $html .= '<li>' . "\n" . '<b>' . $tx['link']['link'] . '</b>' . '<a href="' . $notice[1] . '">' . $notice[2] . '</a><br>' . "\n";
                $html .= '<b>' . $tx['link']['linked_page'] . '</b>' . $notice[1] . '<br>' . "\n";
                if ((int) $notice[0]) 
				{
                    if ((int) $notice[0] >= 300 && (int) $notice[0] < 400) 
					{
                        $html .= '<b>' . $tx['link']['error'] . '</b>' . $tx['link']['redirect'] . '<br>' . "\n";
                    }
                    $html .= '<b>' . $tx['link']['returned_status'] . '</b>' . $notice[0] . "\n";
                } 
				else 
				{
                    if ($notice[0] == 'mailto') 
					{
                        $html .= $tx['link']['email'] . "\n";
                    } 
					else 
					{
                        $html .= $tx['link']['unknown'] . "\n";
                    }
                    $html .= '</li>' . "\n";
                }
            }
            $html .= '</ul>' . "\n";
        }
    }
    return $html;
}

/*
@global <array> $cf
@param <string> $path
@return <array> - contains <array> $urls, <array> $pages, <array> $headings, <array> $levels
*/

function read_content_file($path) 
{

    global $cf, $sl;
    $path = basename($path);
    if ($sl == $cf['language']['default']) 
	{
        $path = './' . $path;
    } 
	else 
	{
        $path = '../' . $path;
    }
    $sep = $cf['uri']['seperator'];
    
	if($cf['use']['h1only_pagesplitting'] == 'true')
	{
		$pattern = '~h1.*?class=".*?_level([1-6])_page_.*?"[^>]*>(.*?)</h~i';
	}
	else
	{
		$pattern = '/<h([1-' . $cf['menu']['levels'] . '])[^>]*>(.*)<\/h/i';
	}

    $content = file_get_contents($path . '/content/content.php');
    if (!$content) 
	{
        return false;
    }
    preg_match_all($pattern, $content, $matches);

    $headings = array();
    $levels = array();
    $urls = array();

    if (count($matches[0]) == 0) 
	{
        return;
    }
    $ancestors = array();
    foreach ($matches[1] as $level) 
	{
        $levels[] = (int) $level;
    }
    $i = 0;
    foreach ($matches[2] as $chapter) 
	{
        $heading = trim(strip_tags($chapter));
        $url = uenc($heading); //in cms.php: handles $tx['urichar']
        $headings[] = $heading;
        $level = $levels[$i];
        $ancestors[$level] = $url;
        $myself = array_slice($ancestors, 0, $level);
        $urls[] = implode($sep, $myself);
        $i++;
    }
    $pages = preg_split($pattern, $content);
    $pages = array_slice($pages, 1); // $pages[0] is the header part - drop it!
    return array($urls, $pages, $headings, $levels);
}


// pluginmanager (added function)

function cmsimple_pluginmanager() 
{
	global $cf, $tx, $pth, $pm_datafile_path, $pluginloader_tx, $disabled_plugins, $csrfSession;

	$pm_output = '<h4>PluginManager</h4>' . "\n";
	
	$pm_output .= '<p class="cmsimplecore_warning" style="text-align: center">' . $pluginloader_tx['pluginmanager']['warning_pluginmanager'] . '</p>' . "\n";
		
	if(!is_writable($pm_datafile_path))
	{
		$pm_output.='<p class="cmsimplecore_warning" style="font-weight: 400; text-align: center;">' . $pluginloader_tx['pluginmanager']['message_datafile1'] . ' <b>./content/_disabled_plugins.txt</b> ' . $pluginloader_tx['pluginmanager']['message_datafile2'] . '</p>';
	}

	// create pluginmanager plugins array

	$all_plugins=opendir($pth['folder']['plugins']);
	if(!$all_plugins) die('Could not open folder' . $all_plugins);
	$pm_plugins_array=array();
	while(false!==($plugin_array_element=readdir($all_plugins)))
	{
		if(!stristr($plugin_array_element, '.') && !stristr($plugin_array_element, '..') && !stristr($plugin_array_element, 'pluginloader'))
		{
			$pm_plugins_array[]='|||'.$plugin_array_element.'|||';
		}
	}
	closedir($all_plugins);
	sort($pm_plugins_array);

	$pm_output.= "\n" . '<form method="post" action="?&pm_data_saved">' . "\n";
	
	if($cf['use']['csrf_protection'] == 'true') $pm_output.= '<input type="hidden" name="csrf_token" value="' . $_SESSION[$csrfSession] . '">' . "\n";
	
	if(count($pm_plugins_array) != 0 && file_exists($pm_datafile_path) && is_writable($pm_datafile_path))
	{
		$pm_output.='<input type="submit" class="submit" value="' . $pluginloader_tx['pluginmanager']['button_save'] . '">' . "\n";
	}

	if(count($pm_plugins_array) == 0)
	{
		$pm_output.='<p class="cmsimplecore_warning" style="font-weight: 700; text-align: center;">No additional Plugin installed.</p>';
	}
	else
	{
		$pm_output.= "\n" . '<div id="cmsimple_pm">' . "\n";
		$pm_output.='<p><span style="background: #090; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['active'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$pm_output.='<span style="background: #eb0; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['hidden'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$pm_output.='<span style="background: #f00; color: #fff; border: 3px solid #fff; padding: 0 6px;">&nbsp;</span> = ' . $tx['sysinfo']['disabled'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$pm_output.='<table>' . "\n";
		$pm_output.='<tr>' . "\n";
		$pm_output.='<th>' . $pluginloader_tx['pluginmanager']['tablehead_plugin'] . '</th>' . "\n";
		$pm_output.='<th>' . $pluginloader_tx['pluginmanager']['tablehead_disable'] . '</th>' . "\n";
		$pm_output.='<th colspan="2">' . $pluginloader_tx['pluginmanager']['tablehead_enable'] . '</th>' . "\n";
		$pm_output.='</tr>' . "\n\n";
		
		foreach($pm_plugins_array as $pluggy)
		{
			if(stristr($disabled_plugins, $pluggy))
			{
				$disable = 'true';
			}
			
			if(!stristr($disabled_plugins, $pluggy))
			{
				$disable = 'false';
			}
			
			if(stristr($disabled_plugins, str_replace('|||', '§', $pluggy)))
			{
				$disable = 'hidden';
			}
			
			$pm_output.='<tr>' . "\n";
			$pm_output.='<td>';
			
			if($disable == 'true')
			{
				$pm_output.= '<span style="background: #f00; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			if($disable == 'hidden')
			{
				$pm_output.= '<span style="background: #eb0; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			if($disable == 'false')
			{
				$pm_output.= '<span style="background: #090; color: #fff; border: 3px solid #fff; padding: 0 6px; margin: 0 8px 0 0;">&nbsp;</span>';
			}
			
			$pm_output.='<b>';
			$pm_output.='<a href="?' . str_replace('|||', '', $pluggy) . '&amp;normal">' . str_replace('|||', '', $pluggy) . '</a>';
			$pm_output.='</b>';
			
			$pm_output.='</td>' . "\n";
			
			if(!stristr($pluggy, '|||pluginloader|||') 
			&& !stristr($pluggy, '|||page_params|||') 
			&& !stristr($pluggy, '|||meta_tags|||') 
//			&& !stristr($pluggy, '|||filebrowser|||') // austauschbar
//			&& !stristr($pluggy, '|||pagemanager|||')  // austauschbar
			&& !stristr($pluggy, '|||jquery|||') 
//				&& !stristr($pluggy, '|||tinymce|||') // austauschbar
			&& !file_exists($pth['folder']['plugins'] . str_replace('|||', '', $pluggy) . '/no_disable.txt'))
			{
				$pm_output.='<td><input type="radio" id="' . $pluggy . '" name="' . $pluggy . '" value="' . $pluggy . '" style="width: 16px; margin: 2px 0 6px 0;" ';
				if($disable == 'true')
				{
					$pm_output.='checked="checked"';
				}
				$pm_output.='> <label for="' . $pluggy . '">' . $pluginloader_tx['pluginmanager']['button_disable'] . '</label></td>' . "\n";
			}
			else
			{
				$pm_output.='<td style="color: #909;"><i>' . $pluginloader_tx['pluginmanager']['hint_standard_plugin'] . '</i></td>' . "\n";
			}
			
			$pm_output.='<td><input type="radio" id="' . $pluggy . '3" name="' . $pluggy . '" value="' . str_replace('|||', '§', $pluggy) . '" style="width: 16px; margin: 2px 0 6px 0;" ';
			if($disable == 'hidden')
			{
				$pm_output.='checked="checked"';
			}
			$pm_output.='> <label for="' . $pluggy . '3">' . $pluginloader_tx['pluginmanager']['button_hide'] . '</label></td>' . "\n";
			
			$pm_output.='<td><input type="radio" id="' . $pluggy . '2" name="' . $pluggy . '" value="" style="width: 16px; margin: 2px 0 6px 0;" ';
			if($disable == 'false')
			{
				$pm_output.='checked="checked"';
			}
			$pm_output.='> <label for="' . $pluggy . '2">' . $pluginloader_tx['pluginmanager']['button_enable'] . '</label></td>' . "\n";
			
			$pm_output.='</tr>' . "\n\n";
		}
		$pm_output.='</table>' . "\n";
		$pm_output .= '</div>' . "\n";
	}
	
	if(count($pm_plugins_array) != 0 && file_exists($pm_datafile_path) && is_writable($pm_datafile_path))
	{
		$pm_output.='<input type="submit" class="submit" value="' . $pluginloader_tx['pluginmanager']['button_save'] . '">' . "\n";
	}
	$pm_output.='</form>' . "\n";

	return $pm_output;
}

// END pluginmanager (added function)

?>