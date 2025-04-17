<?php // utf8-marker: äöü 

if (!defined('CMSIMPLE_VERSION') || preg_match('#jquery/jquery.inc.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/* 
=========================================================
Adapted for CMSimple 4.0 and higher: 
Gert Ebersbach 2013
ge-webdesign.de
Last Change: 2019-01 for CMSimple 4.7.8
=========================================================
based on "jQuery for CMSimple" version 1.3 2011-07-27
author: Holger Irmler - cmsimple.holgerirmler.de
*/

function include_jQuery($path='') 
{
	global $pth, $cf, $hjs, $adm;
	
	if(!defined('JQUERY')) 
	{
		if($path == '') 
		{
			if(isset($_REQUEST['pagemanager']) && $adm)
			{
				$path = $pth['folder']['plugins'] . 'jquery/lib/jquery/jquery_cmsimplecore.js';
			}
			else
			{
				$path = $pth['folder']['plugins'] . 'jquery/lib/jquery/' . $cf['jquery']['file_core'];
			}
			
			if(!file_exists($path)) 
			{
				e('missing', 'file', $path);
				return;
			}
		}
		
		if(!strstr($hjs,$cf['jquery']['file_core']) && !strstr($hjs,'jquery_cmsimplecore.js'))
		{
			$hjs = '<script src="' . $path . '"></script>' . "\n" . $hjs; 
		}
	}
}

function include_jQueryUI($path='') 
{
	global $pth, $cf, $hjs, $adm;
	
	if($path == '') 
	{
		if(isset($_REQUEST['pagemanager']) && $adm)
		{
			$path = $pth['folder']['plugins'] . 'jquery/lib/jquery_ui/jquery-ui_cmsimplecore.js';
		}
		else
		{
			$path = $pth['folder']['plugins'] . 'jquery/lib/jquery_ui/' . $cf['jquery']['file_ui'];
		}
		
		if(!file_exists($path)) 
		{
			e('missing', 'file', $path);
			return;
		}
	}
	
	if(!strstr($hjs,$cf['jquery']['file_ui']) && !strstr($hjs,'jquery-ui_cmsimplecore.js'))
	{
		$hjs.= '<script src="' . $path . '"></script>' . "\n";
	}
	
	if(file_exists($pth['folder']['template'].'jquery_ui/jquery_ui.css')) 
	{
		//load a complete custom ui-theme
		$hjs .= "\n".tag('link rel="stylesheet" type="text/css" media="screen" href="' . $pth['folder']['template'] . 'jquery_ui/jquery_ui.css"');
	} 
	else 
	{
		//load the default theme
		if(!strstr($hjs,$cf['jquery']['file_css']) && !isset($_REQUEST['pagemanager']))
		{
			$hjs = tag('link rel="stylesheet" type="text/css" media="screen" href="' . $pth['folder']['plugins'] . 'jquery/lib/jquery_ui/css/' . $cf['jquery']['file_css'] . '"') . "\n" . $hjs;
		}
		
		//include a custom css-file to overwrite single selectors
		if(file_exists($pth['folder']['template'].'jquery_ui/stylesheet.css')) 
		{
			$hjs.= "\n" . tag('link rel="stylesheet" type="text/css" media="screen" href="' . $pth['folder']['template'] . 'jquery_ui/stylesheet.css"');
		}
		
		//include pagemanager jquery css file
		if(!strstr($hjs,'jqueryui_cmsimplecore.css') && isset($_REQUEST['pagemanager']) && $adm)
		{
			$hjs.= "\n" . '<link rel="stylesheet" type="text/css" media="screen" href="' . $pth['folder']['plugins'] . 'jquery/lib/jquery_ui/css/cmsimplecore/jqueryui_cmsimplecore.css">' . "\n";
		}
	}
}

function include_jQueryPlugin($name='', $path='') 
{
	global $hjs, $jQueryPlugins;
	
	if(!isset($jQueryPlugins)) 
	{
		$jQueryPlugins = array();
	}
	
	if($name != '') 
	{
		if(!file_exists($path)) 
		{
			e('missing', 'file', $path);
			return;
		}
		$name = strtolower($name);
		if (!in_array($name, $jQueryPlugins)) 
		{
			$hjs.= '<script src="' . $path . '"></script>' . "\n";
			$jQueryPlugins[] .= $name;
		}
	}
}
?>