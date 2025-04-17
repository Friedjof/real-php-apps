<?php /* utf-8 marker: äöü */

require_once './classes/filebrowser_view.php';
require_once './classes/filebrowser.php';
global $cf;
include ('../../cmsimple/config.php');

if (!isset($_SESSION)) { session_start(); }

//if(!isset($_SESSION['fb_sn']))die('fatal error');
//echo 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];

if(isset($_SESSION['fb_sn']))$fbsn = $_SESSION['fb_sn'];

$fb_access = FALSE;
if (isset($_SESSION['fb_sn']) && $_SESSION['fb_session'] === session_id()) $fb_access = TRUE;
if ($fb_access === FALSE) die('no access');

$base = './../../';
$browser = $_SESSION['fb_browser'];
$browser->setBrowseBase($base);

//$_GET['base'] = isset($_GET['base']) ? str_replace(array('../', './', '<', '>', '(', ')', ';', ':'), '', $_GET['base']) : '';
//$_SESSION['fb_browse_base'] = $_GET['base'];

if (isset($_GET['type']) && $_GET['type'] === 'file') $_GET['prefix'] = '?&amp;download=';

//$my_prefix = $_GET['type'] === 'file' ? '?&amp;download=' : $_GET['prefix'];
//var_dump($_SESSION);

$fb_type = null;

if (isset($_GET['type'])) 
{
	$fb_type = $_GET['type'];
	if ($fb_type == 'image') {$fb_type = 'images';}
	if ($fb_type == 'file') {$fb_type = 'downloads';}
}

if ($fb_type && array_key_exists($fb_type, $browser->baseDirectories)) {
    $browser->linkType = $fb_type;
    
    if(isset($_GET['prefix'])){$browser->setLinkPrefix($_GET['prefix']);}
    $browser->linkType = $fb_type;

	$src = $_GET;
	$src['type'] = $fb_type;
	unset($src['subdir']);
	// the following is a simplyfied http_build_query()
	$dst = array();
	foreach ($src as $key => $val) {$dst[] = urlencode($key) . '=' . urlencode($val);}
	$dst = implode('&', $dst);
	$browser->setlinkParams($dst);

	$browser->baseDirectory = $browser->baseDirectories[$fb_type];
	$browser->currentDirectory = $browser->baseDirectories[$fb_type];

	if (isset($_GET['subdir'])) {
	$subdir = str_replace(array('../', './', '?', '<', '>', ':'), '', $_GET['subdir']);

		if (strpos($subdir, $browser->currentDirectory) === 0) {
		$browser->currentDirectory = rtrim($subdir, '/') . '/';
		}
	}

    if (isset($_POST['upload']))$browser->uploadFile();
    if (isset($_POST['createFolder']))$browser->createFolder();
    if (isset($_POST['renameFile']))$browser->renameFile();
 
    $browser->readDirectory();

    if(isset($_GET['editor']))$jsFile = 'editorhooks/' . basename($_GET['editor']) . '/script.php';

    $script = 'xxx';
    if (isset($jsFile) && file_exists($jsFile)) include $jsFile;
    $test = '';
   
 //$test .= print_r($_SERVER, true);

    $browser->view->partials['script'] = $script;
    $browser->view->partials['test'] = $test;
    $browser->browserPath = '';
    echo $browser->render('editorbrowser');
}
else  die('fatal error');
?>