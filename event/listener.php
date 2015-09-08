<?php
/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\event;

use phpbb\auth\auth;
use phpbb\cache\service as cache;
use phpbb\config\config;
use phpbb\db\driver\factory as db;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\twig\twig as template;
use phpbb\user;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/* @var auth */
	protected $auth;

	/* @var cache */
	protected $cache;

	/* @var config */
	protected $config;

	/* @var db */
	protected $db;

	/* @var log */
	protected $log;

	/* @var request */
	protected $request;

	/* @var template */
	protected $template;

	/* @var user */
	protected $user;

	/**
	 * @param auth $auth
	 * @param cache $cache
	 * @param config $config
	 * @param db $db
	 * @param log $log
	 * @param request $request
	 * @param template $template
	 * @param user $user
	*/
	public function __construct(
		auth $auth,
		cache $cache,
		config $config,
		db $db,
		log $log,
		request $request,
		template $template,
		user $user
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_users_overview_before'		=> 'core_acp_users_overview_before',
			'core.user_active_flip_after'			=> 'core_user_active_flip_after',
		);
	}

	/*
	 *
	 */
	public function core_acp_users_overview_before($event)
	{
		$user_row = $event['user_row'];
		$user_id = $user_row['user_id'];

		$email_validated = ($user_row['user_sfsr_email_validated']) ? true : false;

		$this->user->add_lang_ext('marttiphpbb/stopforumspamreport', 'acp');
		$no_validated_email = ($email_validated) ? '' : $this->user->lang('ACP_STOPFORUMSPAMREPORT_EMAIL_NOT_VALIDATED');
		$this->template->assign_vars(array(
			'ACP_STOPFORUMSPAMREPORT_REPORT_EXPLAIN'	=> sprintf($this->user->lang['ACP_STOPFORUMSPAMREPORT_REPORT_EXPLAIN'],
					'<a href="https://stopforumspam.com/legal">', '</a>', $no_validated_email, '<a href="https://stopforumspam.com">', '</a>'),
			'S_STOPFORUMSPAMREPORT_EN'	=> ($email_validated && $this->config['stopforumspamreport_apikey']) ? true : false,
		));
 
		$delete			= $this->request->variable('delete', 0);
		$delete_type	= $this->request->variable('delete_type', '');
		$apikey = $this->config['stopforumspamreport_apikey'];
		
		$this->template->assign_var('STOPFORUMSPAMREPORT_EN', ($apikey) ? true : false);

		if (!$this->auth->acl_get('a_userdel')
			|| !$event['submit']
			|| $this->request->variable('cancel', '')
			|| !$delete
			|| !(in_array($delete_type, array('retain', 'remove')))
		)
		{
			return;
		}

		if ($user_id == ANONYMOUS
			|| $user_row['user_type'] == USER_FOUNDER
			|| $user_id == $this->user->data['user_id']
		)
		{
			return;
		}

		$username = $user_row['username'];
		$ip = $user_row['user_ip'];
		$email = $user_row['user_email'];

		$confirm = ($this->user->lang['YES'] === $this->request->variable('confirm', '', true, \phpbb\request\request_interface::POST));

		if (!$confirm)
		{
			if ($this->request->variable('stopforumspamreport', 0) && $apikey && $email_validated)
			{
				$data = array(
					'username' 	=> $username,
					'ip'		=> $ip,
					'email'		=> $email,
				);
				$this->cache->put('stopforumspamreport_' . $user_id, serialize($data), 900);
			}
			else
			{
				$this->cache->destroy('stopforumspamreport_' . $user_id);
			}

			return;
		}

		$uid = $this->request->variable('confirm_uid', 0);
		$session_id = $this->request->variable('sess', '');
		$confirm_key = $this->request->variable('confirm_key', '');

		$cached = $this->cache->get('stopforumspamreport_' . $user_id);

		if ($uid != $this->user->data['user_id']
			|| $session_id != $this->user->session_id
			|| !$confirm_key
			|| !$this->user->data['user_last_confirm_key']
			|| $confirm_key != $this->user->data['user_last_confirm_key']
			|| !$cached
		)
		{
			$this->cache->destroy('stopforumspamreport_' . $user_id);
			return;
		}

		$cached = unserialize($cached);

		if (!sizeof($cached)
			|| $cached['ip'] != $ip
			|| $cached['username'] != $username
			|| $cached['email'] != $email
		)
		{
			$this->cache->destroy('stopforumspamreport_' . $user_id);
			return;
		}

		$username_enc = urlencode($username);
		$email_enc = urlencode($email);

		$url = 'http://stopforumspam.com/add?ip_addr=' . $ip;
		$url .= '&username=' . $username_enc;
		$url .= '&email=' . $email_enc;
		$url .= '&api_key=' . $apikey;

		$params = array($ip, $username, $email);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_USERAGENT => 'phpBB',
			CURLOPT_TIMEOUT => 8,
		));
		$body = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		if ($info['http_code'] != 200)
		{
			$params[] = $body;
			$this->log->add('admin', $this->user->data['user_id'],
				$this->user->ip, 'LOG_STOPFORUMSPAMREPORT_FAIL', time(), $params);
			return;
		} 

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_STOPFORUMSPAMREPORT', time(), $params);

		return;
	}

	/*
	 *
	 */
	public function core_user_active_flip_after($event)
	{
		$mode = $event['mode'];
		$activated = $event['activated'];
		$reason = $event['reason'];
		$user_id_ary = $event['user_id_ary'];
		$user_id = $user_id_ary[0];
		
		$actkey = $this->request->variable('k', '');
		$uid = $this->request->variable('u', 0);

		if ($mode != 'activate'
			|| $activated != 1
			|| $reason != INACTIVE_MANUAL
			|| sizeof($user_id_ary) != 1
			|| !$user_id
			|| !$uid
			|| $user_id != $uid
			|| !$actkey)
		{
			return;
		}

		$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_sfsr_email_validated = 1 
				WHERE user_id = ' . $user_id . '
					AND user_actkey = ' . $actkey;
		$db->sql_query($sql);
	}
}
