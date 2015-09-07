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
use phpbb\controller\helper;
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

	/* @var helper */
	protected $helper;

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
	 * @param helper $helper
	 * @param log $log
	 * @param request $request
	 * @param template $template
	 * @param user $user
	*/
	public function __construct(
		auth $auth,
		cache $cache,
		config $config,
		helper $helper,
		log $log,
		request $request,
		template $template,
		user $user
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->helper = $helper;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_users_overview_before'		=> 'core_acp_users_overview_before',
		);
	}

	public function core_acp_users_overview_before($event)
	{
		$this->user->add_lang_ext('marttiphpbb/stopforumspamreport', 'acp');

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

		$user_row = $event['user_row'];
		$user_id = $user_row['user_id'];

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
			if ($this->request->variable('stopforumspamreport', 0) && $apikey)
			{
				$token = sha1(time() . $user_id . $this->user->data['user_password']);
				$data = array(
					'username' 	=> $username,
					'ip'		=> $ip,
					'email'		=> $email,
					'token'		=> $token,
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
			|| !$cached['token']
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
}
