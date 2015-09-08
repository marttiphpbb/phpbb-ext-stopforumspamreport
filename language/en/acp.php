<?php

/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
 
$lang = array_merge($lang, array(

	'ACP_STOPFORUMSPAMREPORT_APIKEY'			=> 'Apikey',
	'ACP_STOPFORUMSPAMREPORT_APIKEY_EXPLAIN'	=> 'The apikey from stopforumspam.com',
	'ACP_STOPFORUMSPAMREPORT_APIKEY_SAVED'		=> 'The apikey was succesfully stored.',
	'ACP_STOPFORUMSPAMREPORT_REPORT'			=> 'Report user to stopforumspam.com',
	'ACP_STOPFORUMSPAMREPORT_REPORT_EXPLAIN'	=> 'Be sure to have understood the %1$sTerms of Service of service of stopforumspam.com%2$s before reporting a user.
		In order to report, the spambot has to have posted a least one obvious spammessage in the board.
		Only spambots with validated email address can be reported. %3$s	
		An apikey obtained from %4$sstopforumspam.com%5$s is needed for reporting.',
	'ACP_STOPFORUMSPAMREPORT_EMAIL_NOT_VALIDATED'	=> '<strong>The email address of this user is not validated.</strong>',
));
