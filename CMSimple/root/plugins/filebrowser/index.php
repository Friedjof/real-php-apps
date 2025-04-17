<?php

if (!defined('CMSIMPLE_VERSION') || preg_match('#/filebrowser/index.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

/* utf-8 marker: äöü */
if(!$adm) {return true;}

if(!isset($_SESSION)){session_start();}

$temp = './';
$editor_fb = new CMSimpleFileBrowser();
$editor_fb->setBrowseBase(CMSIMPLE_BASE);
$editor_fb->setBrowserPath($pth['folder']['plugins'] . 'filebrowser/');
$editor_fb->setMaxFileSize('images', $cf['images']['maxsize']);
$editor_fb->setMaxFileSize('downloads', $cf['downloads']['maxsize']);


$_SESSION['fb_browser'] = $editor_fb;
$_SESSION['fb_session'] = session_id();
$_SESSION['fb_sn'] = $sn;
$_SESSION['fb_sl'] = $sl;
$_SESSION['fb_snForCsrf'] = $snForCsrf;


if($subsite_folder == '/')
{
	$_SESSION['subsite_folder'] = '';
}
else
{
	$_SESSION['subsite_folder'] = $subsite_folder;
}

if($pth['folder']['base'] != './' && !is_writable('./userfiles'))
{
	$_SESSION['subsite_folder_link'] = $subsite_folder;
}
else
{
	$_SESSION['subsite_folder_link'] = '';
}

if(is_writable('./userfiles'))
{
	$_SESSION['subsite_folder_userfiles'] = $_SESSION['fb_sn'];
}
else
{
	if(CMSIMPLE_ROOT != '/')
	{
		$_SESSION['subsite_folder_userfiles'] = CMSIMPLE_ROOT;
	}
	else
	{
		$_SESSION['subsite_folder_userfiles'] = '';
	}
}

// outcomment following lines for development only 

//echo '$_SESSION[\'subsite_folder\']: ' . $_SESSION['subsite_folder'] . '<br>';
//echo '$_SESSION[\'subsite_folder_userfiles\']: ' . $_SESSION['subsite_folder_userfiles'] . '<br>';

/*
if(is_writable('./userfiles'))
{
	echo 'userfiles folder writable<br><br>';
}
else
{
	echo 'userfiles folder <b>NOT</b> writable<br><br>';
}

echo '$subsite_folder : ' . $subsite_folder . '<br>';
echo '$_SESSION[\'fb_sn\'] : ' . $_SESSION['fb_sn'] . '<br>';
echo '$_SESSION[\'subsite_folder\'] : ' . $_SESSION['subsite_folder'] . '<br>';
echo '$_SESSION[\'subsite_folder_link\'] : ' . $_SESSION['subsite_folder_link'] . '<br>';
echo '$_SESSION[\'subsite_folder_userfiles\'] : ' . $_SESSION['subsite_folder_userfiles'] . '<br>';
echo 'CMSIMPLE_ROOT : ' . CMSIMPLE_ROOT . '<br>';
*/

?>