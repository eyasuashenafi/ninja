<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana loader class for PDO
 *
 *
 * @package    NINJA
 * @author     op5 AB
 * @license    GPL
 */
class pdodb_Core {
	public function instance($type='mysql', $database=false, $user=false, $pass=false, $host=false)
	{
		$params = Kohana::config('database.monitor_reports');
		$config = $params['connection'];
		$type = !empty($type) ? $type : $config['type'];
		$database = !empty($database) ? $database : $config['database'];
		$user = !empty($user) ? $user : $config['user'];
		$pass = !empty($pass) ? $pass : $config['pass'];
		$host = !empty($host) ? $host : $config['host'];
		$db = new PDO($type.':host='.$host.';dbname='.$database, $user, $pass);
		return is_object($db) ? $db : false;
	}
}