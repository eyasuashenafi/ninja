<?php

/**
 * Custom exception for Livestatus errors
 */
class LivestatusException extends Exception {}

/**
 * Livestatus communication handler
 */
class Livestatus
{
	private $auth = false;
	private $sock = false;
	private $config = false;
	private static $instance = false;
	/**
	 * @throws Api_Error_Response
	 * @return filehandle, Livestatus socket
	 */
	private function open_livestatus_socket()
	{
		$ls = $this->config['path'];
		if (strpos($ls, '://') === false)
			$ls .= 'unix://';
		$sock = @fsockopen($ls, null, $errno, $errstr);
		if ($errno)
			throw new LivestatusException("Couldn't open livestatus socket: " . $errstr);
		return $sock;
	}

	/**
	 * @param $socket filehandle Livestatus socket
	 * @param $len int
	 * @return string
	 */
	private function read_socket($socket, $len)
	{
		$offset = 0;
		$res = '';

		while ($offset < $len) {
			$data = @fread($socket, $len - $offset);
			if (empty($data))
				break;
			$res .= $data;
			$offset += strlen($data);
		}
		return $res;
	}

	public static function instance($config = null) {
		if (self::$instance === false)
			return new Livestatus($config);
		else
			return $ls;
	}

	private function __construct($config = null) {
		$this->auth = Nagios_auth_Model::instance();
		$config = $config ? $config : 'livestatus';
		$this->config = Kohana::config('database.'.$config);
		$this->sock = $this->open_livestatus_socket();
	}

	public function __destruct() {
		fclose($this->sock);
	}

	public function query($query, $columns=false) {
		$query = trim($query); // keep track of them newlines
		$start = microtime(true);
		if (!((strpos($query, 'GET host') === 0 && $this->auth->view_hosts_root ) ||
			(strpos($query, 'GET service') === 0 && ($this->auth->view_hosts_root || $this->auth->view_services_root))))
			$query .= "\nAuthUser: {$this->auth->user}";
		if ($columns)
			$query .= "\nColumns: ".implode(' ', $columns);
		$query .= "\nOutputFormat: json\nKeepAlive: on\nResponseHeader: fixed16\n\n";
		@fwrite($this->sock, $query);
		$head = $this->read_socket($this->sock, 16);
		if (empty($head)) {
			throw new LivestatusException("Couldn't read livestatus header");
		}
		$out = $this->read_socket($this->sock, substr($head, 4, 15));
		if (substr($head, 0, 3) !== '200')
			throw new LivestatusException("Invalid request $head: $out");
		else if (empty($out))
			throw new LivestatusException("No output");

		$res = json_decode($out);
		if ($columns) {
			foreach ($res as $rownum => $row) {
				$new_row = array();
				foreach ($row as $idx => $val) {
					$new_row[$columns[$idx]] = $val;
				}
				$res[$rownum] = $new_row;
			}
		}
		$stop = microtime(true);
		if ($this->config['benchmark'] == TRUE)
		{
			Database::$benchmarks[] = array('query' => $query, 'time' => $stop - $start, 'rows' => count($res));
		}
		if ($res === null) {
			throw new LivestatusException("Invalid output");
		}
		return $res;
	}
}
