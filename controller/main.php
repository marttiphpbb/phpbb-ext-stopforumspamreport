<?php

/**
* phpBB Extension - marttiphpbb stopforumspamreport
* @copyright (c) 2015 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\stopforumspamreport\controller;

use phpbb\auth\auth;
use phpbb\cache\service as cache;
use phpbb\config\db as config;
use phpbb\log\log;
use phpbb\user;

use Symfony\Component\HttpFoundation\Response;

class main
{
	/*
	 * @var auth
	*/
	protected $auth;

	/*
	 * @var cache
	*/
	protected $cache;

	/*
	 * @var config
	*/
	protected $config;

	/*
	 * @var log
	 */
	protected $log;

	/*
	 * @var user
	 */
	protected $user;

	/**
	* @param auth $auth
	* @param cache $cache
	* @param config   $config
	* @param log $log
	* @param user   $user
	*/

	public function __construct(
		auth $auth,
		cache $cache,
		config $config,
		log $log,
		user $user
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->log = $log;
		$this->user = $user;
	}

	/**
	* @param int $user_id 
	* @param string $token
	* @return Response
	*/
	public function report($user_id, $token)
	{
/*		if (!$this->auth->acl_get('a_'))
		{
			return New Response('', 401);
		}*/

		$cached = unserialize($this->cache->get('stopforumspamreport_' . $user_id));
		$this->cache->destroy('stopforumspamreport_' . $user_id);

		if (!sizeof($cached)
			|| !$cached['ip']
			|| !$cached['username']
			|| !$cached['email']
			|| $cached['token'] != $token
		)
		{
			error_log('stopforumspamreport: cache not found or complete.');
			
			return New Response('');
		}

		$apikey = $this->config['stopforumspamreport_apikey'];
		$ip = $cached['ip'];
		$username = $cached['username'];
		$email = $cached['email'];

		if (!$apikey)
		{
			error_log('stopforumspamreport: apikey not present.');
			return New Response('');
		}

		$url = 'http://stopforumspam.com/add?ip_addr=' . $ip;
		$url .= '&username=' . urlencode($username);
		$url .= '&email=' . urlencode($email);
		$url .= '&api_key=' . $apikey;

		$res = file_get_contents($url);
		list($status) = get_headers();
//		$status = (strpos($status, '200')) ? '200' : '403'; 

		error_log($url . ' XXXXXXX ' . $res . $status);

		$params = array($ip, $username, $email);
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_STOPFORUMSPAMREPORT', time(), $params);

		error_log('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
		return new Response('');
	}
}
