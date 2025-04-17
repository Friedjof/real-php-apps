<?php /* utf8-marker = äöü */

// timeout in seconds
$setup_timeOut = 600;

if(file_exists('setupControl.php'))
{
	if(function_exists('fileatime'))
	{
		$setup_fileCreated = fileatime('setupControl.php');
	}
	else
	{
		$setup_fileCreated = filectime('setupControl.php');
	}
}

if(file_exists('setupControl.php') && $setup_timeOut - (time() - $setup_fileCreated) > 0)
{
	$remainingSeconds = $setup_timeOut - (time() - $setup_fileCreated);
}
else
{
	$remainingSeconds = 1;
}

if(file_exists('setupControl.php') && (time() - $setup_fileCreated > $setup_timeOut))
{
	chmod('setupControl.php', 0777);
	unlink('setupControl.php');
}

if(!class_exists('PasswordHash') && file_exists('cmsimple/PasswordHash.php'))
{
	$pwHashPath = 'cmsimple/PasswordHash.php';
	$flagsFolder = 'userfiles/images/flags/';
	$configFolder = './cmsimple/';
}

if(!class_exists('PasswordHash') && file_exists('../cmsimple/PasswordHash.php') && (file_exists('cmsimplesubsite.htm') || file_exists('cmsimplelanguage.htm')) && !file_exists('../cmsimplesubsite.htm'))
{
	$pwHashPath = '../cmsimple/PasswordHash.php';
	$flagsFolder = '../userfiles/images/flags/';
	$configFolder = './';
}

if(!class_exists('PasswordHash') && file_exists('../../cmsimple/PasswordHash.php') && file_exists('cmsimplelanguage.htm') && file_exists('../cmsimplesubsite.htm'))
{
	$pwHashPath = '../../cmsimple/PasswordHash.php';
	$flagsFolder = '../../userfiles/images/flags/';
	$configFolder = './';
}

if (is_writable('setupControl.php') && file_exists('index.php')) 
{
	require $pwHashPath;
	$cmsimple_pwHasher = new PasswordHash(8, true);
	
	if (file_exists('cmsimplesubsite.htm') || file_exists('cmsimplelanguage.htm'))
	{
		$fileConfigSetup = './config.php';
	}
	else
	{
		$fileConfigSetup = $configFolder . 'config.php';
	}
	
	$passwordhint = '';
	if (isset($_POST['submit_password']))
	{
		if (strlen($_POST['password']) > 4)
		{
			$hash = $cmsimple_pwHasher->HashPassword($_POST['password']);
			$hash = str_replace('$P$','\$P\$',$hash);
			$content = explode("\n", htmlspecialchars(file_get_contents($fileConfigSetup),ENT_QUOTES,'UTF-8'));
			$content[2] = '$cf[\'security\'][\'password\']="' . $hash . '";';
			$myfile = fopen($fileConfigSetup, "w") or die("Unable to open file!");
			fwrite($myfile, htmlspecialchars_decode(implode("\n", $content),ENT_QUOTES));
			fclose($myfile);
			chmod('setupControl.php', 0777);
			unlink('setupControl.php');
			header("Location: ./?login");
		}
		else 
		{
			if(strlen($_POST['password']) < 5) $passwordhint.= '
<p style="font-size: 15px; color: #900;"><img src="' . $flagsFolder . 'en.gif" alt="flag english"><br><b>Your password must have<br>5 or more characters!</b></p>
<p style="font-size: 15px; color: #900;"><img src="' . $flagsFolder . 'de.gif" alt="flag deutsch"><br><b>Das Passwort muss aus<br>5 oder mehr Zeichen bestehen!</b></p>
';
		}
	}

	echo '<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="utf-8">
<title>Welcome</title>
<meta name="robots" content="noindex, nofollow">
</head>

<body style="background: #333;" onload="countDown(true)">

<script type="text/javascript"> 
function countDown(init)
{
if (init || --document.getElementById( "counter" ).firstChild.nodeValue > 0 )
	window.setTimeout( "countDown()" , 1000 );
};
</script>

<div style="background: #ddd; color: #000; width: 294px; text-align: center; font-family: arial, sans-serif; font-size: 15px; line-height: 1.3em; border: 5px solid #fff; border-radius: 6px; padding: 6px 24px; margin: 24px auto;">
<br><span id="counter" style="font-family: times new roman, serif; font-size: 24px; font-weight: 900; color: #900;">' . $remainingSeconds . '</span>&nbsp; seconds remaining
<p><img src="' . $flagsFolder . 'en.gif" alt="flag english">&nbsp; <b>Enter your new password!</b></p>
<p><img src="' . $flagsFolder . 'de.gif" alt="flag english">&nbsp; <b>Geben Sie Ihr neues Passwort ein!</b></p>
<p>Minimum: 5 characters</p>
<form method="POST">
<input type="password" name="password" style="border: 2px solid #999; border-radius: 3px; padding: 2px 6px 3px 6px;" value="test">
<input type="submit" name="submit_password" value="Submit" style="background: #080; color: #fff; border: 2px solid #080; border-radius: 3px; padding: 1px 6px 2px 6px;">
</form>
<p style="color: #900;">' . $passwordhint . '</p>
</div>
</body>

</html>';
}
else 
{
	echo '<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="utf-8">
<title>Welcome</title>
<meta name="robots" content="noindex, nofollow">
</head>

<body>
<div style="width: 260px; text-align: center; font-family: arial, sans-serif; font-size: 16px; line-height: 1.4em; padding: 36px 12px; margin: 0 auto;">
<p><b>Setup is not active.</b></p>
<p><img src="' . $flagsFolder . 'en.gif" alt="flag english"><br>How you can activate setup, you will find in the readme.php in the Root folder of the CMSimple download.</p>
<p><img src="' . $flagsFolder . 'de.gif" alt="flag english"><br>Wie Sie Setup aktivieren können, finden Sie in der readme.php des CMSimple Downloads.</p>
<p><a href="./">Home &raquo;</a></p>
</div>
</body>

</html>';
}

?>