<?php
/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\migrations;

class v_0_1_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('stopforumspamreport_apikey', '')),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_STOPFORUMSPAMREPORT'
			)),
			array('module.add', array(
				'acp',
				'ACP_STOPFORUMSPAMREPORT',
				array(
					'module_basename'	=> '\marttiphpbb\stopforumspamreport\acp\main_module',
					'modes'				=> array(
						'apikey',
					),
				),
			)),
		);
	}
}
