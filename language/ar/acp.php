<?php

/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Translated By : Bassel Taha Alhitary - www.alhitary.net
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

	'ACP_STOPFORUMSPAMREPORT_APIKEY'			=> 'مفتاح الـ Api',
	'ACP_STOPFORUMSPAMREPORT_APIKEY_EXPLAIN'	=> 'مفتاح الـ Api من موقع الخدمة stopforumspam.com',
	'ACP_STOPFORUMSPAMREPORT_APIKEY_SAVED'		=> 'تم حفظ مفتاح الـ Api بنجاح.',
	'ACP_STOPFORUMSPAMREPORT_REPORT'			=> 'التبليغ عن العضو إلى stopforumspam.com ',
	'ACP_STOPFORUMSPAMREPORT_REPORT_EXPLAIN'	=> 'يجب التأكد من أنك قرأت وفهمت %1$sشروط الخدمة من الموقع stopforumspam.com%2$s قبل التبليغ عن العضو.
		لكي تقدم البلاغ , يجب أن تكون هناك مُشاركة مُزعجة واحدة على الأقل في منتداك للعضو المُزعج.
		تستطيع التبليغ عن العضو المُزعج الذي لديه بريد ألكتروني صحيح. %3$s	
		يجب الحصول على مفتاح الـ Api من %4$sstopforumspam.com%5$s لكي تستطيع التبليغ.',
	'ACP_STOPFORUMSPAMREPORT_EMAIL_NOT_VALIDATED'	=> '<strong>لم يتم التحقق من صحة البريد الإلكتروني لهذا العضو.</strong>',
));
