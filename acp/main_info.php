<?php
/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\marttiphpbb\stopforumspamreport\acp\main_module',
			'title'		=> 'ACP_STOPFORUMSPAMREPORT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'apikey'	=> array(
					'title' => 'ACP_STOPFORUMSPAMREPORT_APIKEY',
					'auth' => 'ext_marttiphpbb/stopforumspamreport && acl_a_board',
					'cat' => array('ACP_STOPFORUMSPAMREPORT'),
				),
			),
		);
	}
}
