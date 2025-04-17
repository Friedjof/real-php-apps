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

// if(isset($_GET['login'])) $login = false;
// if(isset($_GET['xyz'])) $login = true;

global $pth;

if (preg_match('/login.php/i', $_SERVER['SCRIPT_NAME']) || preg_match('/\/2author\//i', $sn) || preg_match('/\/2lang\//i', $sn) || preg_match('/\/2site\//i', $sn) || preg_match('/\/2sit2lang\//i', $sn))
	die('Access Denied');

require $pth['folder']['cmsimple'] . 'PasswordHash.php'; 
$cmsimple_pwHasher = new PasswordHash(8, true);

if(isset($cf['security']['type']))
{
	unset($cf['security']['type']);
}

function gc($s) 
{
	if(isset($_COOKIE[$s])) return $_COOKIE[$s];
}

function logincheck() 
{
	global $cf;
	return (gc('passwd') == $cf['security']['password']);
}

function writelog($m) 
{
	global $pth, $e;
	
	$newLogfilePHP = '<?php // utf-8 marker: äöü
if(!defined(\'CMSIMPLE_VERSION\') || preg_match(\'/log.php/i\', $_SERVER[\'SCRIPT_NAME\'])){die(\'No direct access\');} ?>
==============================
';
	
	$logfileContent = str_replace($newLogfilePHP,'',file_get_contents($pth['file']['log']));
	
	if ($fh = fopen($pth['file']['log'], "w")) 
	{
		fwrite($fh,$newLogfilePHP . $m . $logfileContent);
		fclose($fh);
	}
}

function loginforms() 
{
	global $adm, $cf, $print, $hjs, $tx, $onload, $f, $o, $u, $s;

	if ($f == 'login') 
	{
		$cf['meta']['robots'] = "noindex";
		$onload.= "self.focus();document.login.passwd.focus();";
		$f = $tx['menu']['login'];
		$o.= '
<div id="cmsimple_loginformBG" class="cmsimple_loginformBG">
<div id="cmsimple_loginform" class="cmsimple_loginform">
<div class="cmsimple_close"><a href="./?' . $u[$s] . '">X</a></div>
<h1>' . $tx['menu']['login'] . '</h1>
<div>
' . str_replace('<br><br><br><br>','<br><br>', str_replace("\r",'<br>',str_replace("\n",'<br>',$tx['login']['warning']))) . '
</div>
<form id="login" name="login" action="./?' . $u[$s] . '" method="post">
<input type="hidden" name="login" value="true">
<input type="hidden" name="selected" value="' . $u[$s] . '">
' . $tx['login']['user_optional'] . ': <br>
<input type="text" name="user" id="user" value=""><br>
' . $tx['login']['password'] . ':<br>
<input type="password" name="passwd" id="passwd" value=""><br>
<br>
<input type="submit" name="submit" id="submit" class="submit" value="' . $tx['login']['login'] . '">
</form>
</div>
</div>
';
//		$s = -1;
	}
}


// LOGIN & BACKUP

$adm = (gc('status') == 'adm' && logincheck());

if ($login && $passwd == '' && !$adm) 
{
	$login = null;
	$f = 'login';
}

// timeout in seconds
$setup_timeOut = 600;
$cmsimpleLogin = 'closed';

if(file_exists('./setupControl.php'))
{
	if(function_exists('fileatime'))
	{
		$setupFileCreated = fileatime('./setupControl.php');
	}
	else
	{
		$setupFileCreated = filectime('./setupControl.php');
	}
}

if(file_exists('./setupControl.php') && (time() - $setupFileCreated < $setup_timeOut)) $cmsimpleLogin = 'open';
if(!$cmsimple_pwHasher->CheckPassword('test', $cf['security']['password'])) $cmsimpleLogin = 'open';
if(file_exists('./setupControl.php') && (time() - $setupFileCreated > $setup_timeOut)) unlink('./setupControl.php');

if ($login && !$adm) 
{
	if ($cmsimple_pwHasher->CheckPassword($passwd, $cf['security']['password']) && $cmsimpleLogin !== 'closed')
	{
		setcookie('status', 'adm', 0);
		setcookie('passwd', $cf['security']['password'], 0);
		$adm = true;
		$edit = true;
		if(!isset($_SESSION) && isset($_POST['passwd']) && $adm == true){session_start();}
		$_SESSION[$csrfSession] = uniqid('', true);
		writelog(date("Y-m-d H:i:s") . " from " . sv('REMOTE_ADDR') . " logged_in: $sn" . ' - "' . strip_tags($_POST['user']) ."\"\n");
		chmod('setupControl.php', 0777);
		unlink('./setupControl.php');
	}
	else
	{
		writelog(date("Y-m-d H:i:s")." from ".sv('REMOTE_ADDR')." login failed: $sn ##### \"" . strip_tags($_POST['user']) . "\" ##### \n");
		$o = '
<div id="cmsimple_loginformBG" class="cmsimple_loginformBG">
<div id="cmsimple_loginform" class="cmsimple_loginform">
<div class="cmsimple_close"><a href="./?' . htmlspecialchars(strip_tags($_SERVER['QUERY_STRING']), ENT_QUOTES, 'UTF-8') . '&login">X</a></div>
<p><br><b>' . $tx['login']['wrong_password'] . '</b></p>
</div>
</div>
';
	}
} 
else if ($logout && $adm) 
{
	unset($_SESSION[$csrfSession]);
	
	$backupDate = date("Ymd_His");
	$fn = $backupDate . '_content.php'; // 4.5
	if (copy($pth['file']['content'], './backups/cmsimple/' . $fn)) 
	{
		$o .= '<p>' . ucfirst($tx['filetype']['backup']) . ' ' . $fn . ' ' . $tx['result']['created'] . '</p>';
		$fl = array();
		$fd = opendir('./backups/cmsimple/');
		while (($p = readdir($fd)) == true) 
		{
			if (preg_match("/\d{3}\_content.php/", $p) || preg_match("/\d{3}\_content.htm/", $p)) // 4.5
				$fl[] = $p;
		}
		if ($fd == true)
			closedir($fd);
		sort($fl, SORT_STRING);
		$v = count($fl) - $cf['backup']['numberoffiles'];
		for ($i = 0; $i < $v; $i++) 
		{
			if (unlink('./backups/cmsimple/' . '/' . $fl[$i]))
				$o .= '<p>' . ucfirst($tx['filetype']['backup']) . ' ' . $fl[$i] . ' ' . $tx['result']['deleted'] . '</p>';
			else
				e('cntdelete', 'backup', $fl[$i]);
		}
	}
	else
	{
		e('cntsave', 'backup', $fn);
	}


// SAVE function for pagedata.php added

	if (file_exists($pth['folder']['content'] . 'pagedata.php')) 
	{
		$fn = $backupDate . '_pagedata.php';
		if (copy($pth['file']['pagedata'], './backups/cmsimple/' . $fn)) 
		{
			$o .= '<p>' . ucfirst($tx['filetype']['backup']) . ' ' . $fn . ' ' . $tx['result']['created'] . '</p>';
			$fl = array();
			$fd = opendir('./backups/cmsimple/');
			while (($p = readdir($fd)) == true) 
			{
				if (preg_match("/\d{3}\_pagedata.php/", $p))
					$fl[] = $p;
			}
			if ($fd == true)
				closedir($fd);
			sort($fl, SORT_STRING);
			$v = count($fl) - $cf['backup']['numberoffiles'];
			for ($i = 0; $i < $v; $i++) 
			{
				if (unlink('./backups/cmsimple/' . $fl[$i]))
					$o .= '<p>' . ucfirst($tx['filetype']['backup']) . ' ' . $fl[$i] . ' ' . $tx['result']['deleted'] . '</p>';
				else
					e('cntdelete', 'backup', $fl[$i]);
			}
		}
		else
		{
			e('cntsave', 'backup', $fn);
		}
	}

// END save function for pagedata.php


    $adm = false;
	setcookie('status', '', 0);
	setcookie('passwd', '', 0);
	$o .= '<p class="cmsimplecore_warning" style="text-align: center; font-weight: 700; padding: 8px;">' . $tx['login']['loggedout'] . '</p>';
}


// SETTING FUNCTIONS AS PERMITTED

if ($adm) 
{
    if ($edit)
        setcookie('mode'.$sn, 'edit', 0);
    if ($normal)
        setcookie('mode'.$sn, '', 0);
    if (gc('mode'.$sn) == 'edit' && !$normal)
        $edit = true;
} 
else 
{
	if (gc('status') != '')
        setcookie('status', '', 0);
    if (gc('passwd') != '')
        setcookie('passwd', '', 0);
    if (gc('mode'.$sn) == 'edit')
        setcookie('mode'.$sn, '', 0);
}


// DELETE OLD BACKUPS FROM CONTENT FOLDER

if($login && $adm) 
{
	// create backup folders, if not exists
	if(!is_dir('./backups/'))
	{
		mkdir('./backups/', 0777, true);
		chmod('./backups/', 0777);
	}

	if(!is_dir('./backups/cmsimple/'))
	{
		mkdir('./backups/cmsimple/', 0777, true);
		chmod('./backups/cmsimple/', 0777);
	}
	
	// new backups array
	if(count(scandir('./backups/cmsimple/')) > 0)
	{
		$nbf = opendir('./backups/cmsimple/');
		while(($newBackupFile = readdir($nbf)) == true) 
		{
			if(preg_match("/_content.php/", $newBackupFile) || preg_match("/_pagedata.php/", $newBackupFile))
			{
				$newBackups[] = $newBackupFile;
			}
		}
	}

	// delete old backups
	if(isset($newBackups) && count($newBackups) > 10) 
	{
		$delobf = opendir($pth['folder']['content']);
		while(($oldBackupFile = readdir($delobf)) == true) 
		{
			if(preg_match("/_content.php/", $oldBackupFile) || preg_match("/\d{3}\_content.htm/", $oldBackupFile) || preg_match("/_pagedata.php/", $oldBackupFile))
			{
				unlink($pth['folder']['content'] . $oldBackupFile);
			}
		}
	}
}
?>