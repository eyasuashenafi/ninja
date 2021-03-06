<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Handle notifications for hosts and services
 */
class Notifications_Model extends Model {

	public $sort_field ='next_check'; /**< The field to sort on */
	public $sort_order='ASC'; /**< The sort order */
	public $where=''; /**< Arbitrary SQL to add to filtering */

	/**
	*	Fetch scheduled events
	*
	*/
	public function show_notifications($num_per_page=false, $offset=false, $count=false)
	{

		$db = Database::instance();
		$auth = Nagios_auth_Model::instance();

		$num_per_page = (int)$num_per_page;

		# only use LIMIT when NOT counting
		if ($offset !== false)
			$offset_limit = $count === true ? "" : " LIMIT " . $num_per_page." OFFSET ".$offset;
		else
			$offset_limit = '';

		if (substr($this->where, 0, 4) == ' AND') {
			$this->where = preg_replace('/^ AND/', '', $this->where);
		}
		$where_string = (!empty($this->where)) ? 'WHERE '.$this->where : '';

		if (!empty($where_string)) {
			$where_string .= " AND ";
		} else {
			$where_string .= " WHERE ";
		}

		$where_string .= " contact_name IS NOT NULL AND notification.command_name IS NOT NULL ";

		$fields = " notification.host_name, notification.service_description, notification.start_time, ".
			"notification.end_time, notification.reason_type, notification.state, notification.contact_name, ".
			"notification.notification_type, notification.output, notification.command_name ";

		$where_order = " ORDER BY ".$this->sort_field." ".$this->sort_order.$offset_limit;

		$sql = "SELECT ".($count === true ? 'count(1) AS cnt' : $fields)." FROM notification ".$where_string;

		# query for limited contact
		# make joins with contact_access to get all notifications
		# for user's hosts and services
		if (!$auth->view_hosts_root) {
			$sql_host = "SELECT ".($count === true ? 'count(1)' : 'notification.id')." FROM notification ".
				"INNER JOIN host ON host.host_name=notification.host_name ".
				"INNER JOIN contact_access ON contact_access.host=host.id ".
				"WHERE contact_access.contact=".$auth->id." AND ".
				"notification.service_description IS NULL ".
				"AND notification.command_name IS NOT NULL";

			if (!$auth->view_services_root)
				$sqlsvc = "SELECT ".($count === true ? 'count(1)' : 'notification.id')." FROM notification ".
					"INNER JOIN host ON host.host_name=notification.host_name ".
					"INNER JOIN service ON service.host_name=notification.host_name AND service.service_description=notification.service_description ".
					"INNER JOIN contact_access ON contact_access.service=service.id ".
					"WHERE contact_access.contact=".$auth->id." AND ".
					"notification.command_name IS NOT NULL";
			else
				$sqlsvc = 'SELECT '.($count === true ? 'count(1)' : 'notification.id').' FROM notification WHERE notification.service_description IS NOT NULL';
			if ($count !== true)
				$sql = "SELECT $fields FROM notification WHERE id IN ($sql_host) OR id IN ($sqlsvc)";
			else
				$sql = "SELECT ($sql_host) + ($sqlsvc) AS cnt";
		}

		if ($count !== true)
			$sql .= $where_order;
		$result = $db->query($sql);
		if ($count === true) {
			$row = $result->current();
			return $row->cnt;
		}
		return $result->count() ? $result: false;
	}

	/**
	*	Wrapper method to fetch no of hosts in the scheduling queue
	*/
	public function count_notifications()
	{
		return self::show_notifications(false, false, true);
	}

}
