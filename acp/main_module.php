<?php
/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\acp;

class main_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $template, $request, $phpbb_root_path, $user, $cache, $config, $phpEx;

		$user->add_lang_ext('marttiphpbb/stopforumspamreport', 'acp');
		add_form_key('marttiphpbb/stopforumspamreport');

		switch ($mode)
		{
			case 'apikey':

				$this->tpl_name = 'apikey';
				$this->page_title = $user->lang('ACP_STOPFORUMSPAMREPORT_APIKEY');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key('marttiphpbb/stopforumspamreport'))
					{
						trigger_error('FORM_INVALID');
					}
					
					$config->set('stopforumspamreport_apikey', $request->variable('stopforumspamreport_apikey', ''));

					trigger_error($user->lang('ACP_STOPFORUMSPAMREPORT_APIKEY_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'U_ACTION'							=> $this->u_action,
					'STOPFORUMSPAMREPORT_APIKEY'		=> $config['stopforumspamreport_apikey'],
				));

				break;
		}
	}
}
