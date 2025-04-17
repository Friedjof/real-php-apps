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

 
/* 
History:
2012-02-18  GE removed inline css to core.css, added outer div with id for better styling by template
2011-08-30  captcha on/off + change in error messages by svasti, code improvement by cmb
2010-06-12  Bob for XH 1.2 : Mail header subject localized
2009-09-18  GE for CMSimple_XH
2008-11-19  JB for 32SE added captcha, senders phone and name
*/

if (preg_match('/mailform.php/i',$_SERVER['SCRIPT_NAME']))die('Access Denied');

$o.= "\n" . '<div id="cmsimple_mailform" class="cmsimple_mailform">' .  "\n";

// optional text after mailform (hhidden page/newsbox)
$o.= newsbox('CMSimpleMFC1') . "\n";

$title = $tx['title'][$f];
$o .= '<h1>' . $title . '</h1>' . "\n";

// optional text after mailform (hhidden page/newsbox)
$o.= newsbox('CMSimpleMFC2') . "\n";

initvar('sendername');
initvar('senderphone');
initvar('sender');
initvar('getlast');
initvar('cap');
initvar('mailform');

function check_for_linebreaks($field)
{
	if(preg_match("/%0A|\\r|%0D|\\n|%00|\\0|%09|\\t|%01|%02|%03|%04|%05|%06|%07|%08|%09|%0B|%0C|%0E|%0F|%10|%11|%12|%13/i", $field))
	{
		return true;
	}
	else
	{
		return false;
	}
}

$t = '';

if ($action == 'send')
{
	$msg = ($tx['mailform']['sendername'] . ": " . stsl($sendername) . " - " . stsl($sender) . "\n" . $tx['mailform']['senderphone'] . ": " . stsl($senderphone) . "\n\n" . stsl($mailform));
	$CMSimpleMailformSubject = $tx['menu']['mailform'] . ' ' . sv('SERVER_NAME');

	
// MAIL DELIVERY and MESSAGES
	
	if ($getlast != $cap && trim($cf['mailform']['captcha']) == 'true')
	{
		$e .= '<li>' . $tx['mailform']['captchafalse'] . '</li>';
	}
	
	if ($mailform == '')
	{
		$e .= '<li>' . $tx['mailform']['mustwritemessage'] . '</li>';
	}
	
	if (!(preg_match('!^[^@]+@[^@|^\s]+$!', $sender)))
	{
		$e .= '<li>' . $tx['mailform']['notaccepted'] . '</li>';
	}
	
	if (check_for_linebreaks($sendername) == true || check_for_linebreaks($senderphone) == true || check_for_linebreaks($sender) == true || check_for_linebreaks($getlast) == true || check_for_linebreaks($cap) == true || check_for_linebreaks($CMSimpleMailformSubject) == true || check_for_linebreaks($cf['mailform']['email']) == true)
	{
		die('<p>No attacks please ... go back with the back button of your browser</p>');
	}
	
	if (!$e && !(mail($cf['mailform']['email'], '=?UTF-8?B?' . base64_encode($CMSimpleMailformSubject) . '?=', $msg, "From: CMSimple Mailform <" . $cf['mailform']['email'] . ">\r\n" . "Reply-To: " . stsl($sender) . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n")))
	{
		$e .= '<li>' . $tx['mailform']['notsend'] . '</li>' . "\n";
	}
	else
	{
		$t = '<p>' . $tx['mailform']['send'] . '</p>' . "\n";
	}
}

if(isset($_REQUEST['cmsimplemailformsent']))
{
	$o.= '
<p class="cmsimplecore_message">' . $tx['mailform']['send'] . '</p>
<p class="cmsimpleform_backlink"><a href="./?&mailform">' . $tx['title']['mailform'] . ' &raquo;</a></p>' . "\n";
}

// MAILFORM

if (($t == '' || $e != '') && !isset($_REQUEST['cmsimplemailformsent']))
{
// JB+ add captcha
	srand((double)microtime()*1000000);
	$random=rand(10000,99999);

	$o .= '<form action="./" method="post">' . "\n";

	$o .= '<input type="hidden" name="function" value="mailform">' . "\n";

	if (trim($cf['mailform']['captcha']) == 'true')
	{
		$o .= '<input type="hidden" name="getlast" value="' . $random . '">' . "\n";
	}
	$o .= '<input type="hidden" name="action" value="send">' . "\n";

// fields before textarea 
	$o .= '<div>' . "\n" . $tx['mailform']['sendername'].': <br>' . "\n"
	. '<input type="text" class="text" size="35" name="sendername" value="' . htmlspecialchars(stsl($sendername), ENT_COMPAT, 'UTF-8').'">' . "\n"
	. '</div>' . "\n"
	. '<div>' . "\n" . $tx['mailform']['senderphone'].': <br>' . "\n"
	. '<input type="text" class="text" size="35" name="senderphone" value="' . htmlspecialchars(stsl($senderphone), ENT_COMPAT, 'UTF-8').'">' . "\n" . '</div>' . "\n"
	. '<div>' . "\n" .  $tx['mailform']['sender'].': <br>' . "\n"
	. '<input type="text" class="text" size="35" name="sender" value="' . htmlspecialchars(stsl($sender), ENT_COMPAT, 'UTF-8').'">' . "\n"
	. '</div>' . "\n<br>\n";

// textarea
	$o .= '<textarea rows="12" cols="40" name="mailform">' . "\n";
	if ($mailform != 'true') $o .= htmlspecialchars(stsl($mailform), ENT_COMPAT, 'UTF-8') . "\n";
	$o .= '</textarea>' . "\n";

// captcha
    if (trim($cf['mailform']['captcha']) == 'true')
	{
		$o .= '<p>' .  $tx['mailform']['captcha'] . '</p>' . "\n" 
. '<input type="text" name="cap" class="captchainput">' . "\n" 
. '<span class="captcha_code">' . "\n" 
.  $random . '</span>' . "\n";
	}

// sendbutton
	$o .= '<div style="clear: both;">' . "\n" 
. '<input type="submit" class="submit" value="' . $tx['mailform']['sendbutton'] . '">' . "\n" 
. '</div>' . "\n";
	$o .= '</form>' . "\n";
}
else 
{
	if(!isset($_REQUEST['cmsimplemailformsent']))header('Location: ./?&mailform&cmsimplemailformsent');
}

// optional text after mailform (hhidden page/newsbox)
$o.= '</div>
<div style="clear: both;">&nbsp;</div>' . "\n";
$o.= newsbox('CMSimpleMFC3') . "\n";

?>