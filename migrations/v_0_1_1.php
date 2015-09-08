<?php
/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\migrations;

class v_0_1_1 extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_columns'        => array(
				$this->table_prefix . 'users'        => array(
					'user_sfsr_email_validated'  => array('BOOL', false),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'        => array(
				$this->table_prefix . 'users'        => array(
					'user_sfsr_email_validated',
				),
			),
		);
	}
}
