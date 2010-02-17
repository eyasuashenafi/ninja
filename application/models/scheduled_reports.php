<?php defined('SYSPATH') OR die('No direct access allowed.');

class Scheduled_reports_Model extends Model
{
	public $db_name = 'monitor_reports';
	const db_name = 'monitor_reports';

	public function delete_scheduled_report($id=false)
	{
		$id = (int)$id;
		if (empty($id)) return false;
		$sql = "DELETE FROM scheduled_reports WHERE id=".$id;
		$db = new Database(self::db_name);
		$db->query($sql);
		return true;
	}

	/**
	*	Delete ALL schedules for a certain report_id and type
	*/
	public function delete_all_scheduled_reports($type='avail',$id=false)
	{
		$type = strtolower($type);
		if ($type != 'avail' && $type != 'sla')
			return false;
		$db = new Database(self::db_name);

		# what report_type_id do we have?
		$sql = "SELECT id FROM scheduled_report_types WHERE identifier=".$db->escape($type);
		$res = $db->query($sql);
		if (!count($res))
			return false;
			# bail out if we can't find report_type

		$row = $res->current();
		$report_type_id = $row->id;
		$sql = "DELETE FROM scheduled_reports WHERE report_type_id=".$report_type_id." AND report_id=".$id;
		try {
			$db->query($sql);
		} catch (Kohana_Database_Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * Fetches all scheduled reports of current report type (avail/sla)
	 *
	 * @param $type string: {avail, sla}
	 * @return res
	 */
	public function get_scheduled_reports($type='avail')
	{
		$type = strtolower($type);
		if ($type != 'avail' && $type != 'sla')
			return false;
		$fieldname = $type == 'avail' ? 'report_name' : 'sla_name';
		$sql = "SELECT
				sr.*,
				rp.periodname,
				r.".$fieldname." AS reportname
			FROM
				scheduled_reports sr,
				scheduled_report_types rt,
				scheduled_report_periods rp,
				".$type."_config r
			WHERE
				rt.identifier='".$type."' AND
				sr.report_type_id=rt.id AND
				rp.id=sr.period_id AND
				sr.report_id=r.id
			ORDER BY
				reportname";
		$db = new Database(self::db_name);
		$res = $db->query($sql);
		return $res ? $res : false;
	}

	/**
	 * Checks if a report is scheduled in autoreports
	 *
	 * @param $id The report id
	 * @param $type string: {avail, sla}
	 * @return Array on success. False on error.
	 */
	public function report_is_scheduled($type='avail', $id=false)
	{
		$type = strtolower($type);
		if ($type != 'avail' && $type != 'sla')
			return false;

		$id = (int)$id;
		if (!$id) return false;
		$res = self::get_scheduled_reports($type);
		if (!$res || count($res)==0) {
			return false;
		}
		$return = false;
		$res->result(false);
		foreach ($res as $row) {
			if ($row['report_id'] == $id) {
				$return[] = $row;
			}
		}
		return $return;
	}

	/**
	 * Get available report periods
	 * @return Database result object on success. False on errors.
	 */
	public function get_available_report_periods()
	{
		$sql = "SELECT * from scheduled_report_periods";
		$db = new Database(self::db_name);
		$res = $db->query($sql);
		return (!$res || count($res)==0) ? false : $res;
	}

	public function fetch_scheduled_field_value($type=false, $id=false, $elem_id=false)
	{
		$id = (int)$id;
		$type = trim($type);
		$elem_id = trim($elem_id);
		if (empty($type) || empty($id) || empty($elem_id)) return false;
		$xajax = get_xajax::instance();
		$objResponse = new xajaxResponse();
		$sql = "SELECT * FROM scheduled_reports WHERE id=".$id;
		$db = new Database(self::db_name);
		$res = $db->query($sql);
		$translate = zend::instance('Registry')->get('Zend_Translate');
		$objResponse->call("show_progress", "progress", $translate->_('Please wait...'));
		$row = $res->current();
		$objResponse->assign($elem_id,"innerHTML", $row->{$type});
		$objResponse->call('setup_hide_content', 'progress');
		return $objResponse;
	}

	/**
	 * Delete a schedule from database
	 *
	 * @param $id int: The id of the report to delete.
	 * @param $context string: Enables us to take different actions
	 * 			depending on where it is called from
	 * @return ajax output
	 */
	public function delete_schedule_ajax($id=false, $context=false)
	{
		$id = (int)$id;
		$xajax = get_xajax::instance();
		$objResponse = new xajaxResponse();

		$translate = zend::instance('Registry')->get('Zend_Translate');
		$objResponse->call("show_progress", "progress", $translate->_('Please wait...'));
		if (!$id) {
			$objResponse->assign("err_msg","innerHTML", $translate->_("Missing ID so nothing to delete"));
			return $objResponse;
		}
		$sql = "DELETE FROM scheduled_reports WHERE id=".$id;
		$db = new Database;(self::db_name);
		$res = $db->query($sql);
		$objResponse->call('hide_progress');
		switch ($context) {
			case 'setup':
				$objResponse->call('remove_deleted_rows', $id);
				break;
			case 'edit':
				$objResponse->call('remove_schedule', $id);
				break;
		}
		return $objResponse;
	}

	public function edit_report($id=false, $rep_type=false, $saved_report_id=false, $period=false, $recipients=false, $filename='', $description='')
	{
		$db 			= new Database(self::db_name);
		$id 			= (int)$id;
		$rep_type 		= (int)$rep_type;
		$saved_report_id = (int)$saved_report_id;
		$period			= (int)$period;
		$recipients 	= trim($recipients);
		$filename		= trim($filename);
		$description	= trim($description);
		$user 			= Auth::instance()->get_user()->username;

		if (!$rep_type || !$saved_report_id || !$period || empty($recipients)) return false;

		// some users might use ';' to separate email adresses
		// just replace it with ',' and continue
		$recipients = str_replace(';', ',', $recipients);
		$rec_arr = explode(',', $recipients);
		if (!empty($rec_arr)) {
			foreach ($rec_arr as $recipient) {
				if (trim($recipient)!='') {
					$checked_recipients[] = trim($recipient);
				}
			}
			$recipients = implode(', ', $checked_recipients);
		}

		if ($id) {
			// UPDATE
			$sql = "UPDATE scheduled_reports SET user=".$db->escape($user).", report_type_id=".$rep_type.", report_id=".$saved_report_id.",
				recipients=".$db->escape($recipients).", period_id=".$period.", filename=".$db->escape($filename).", description=".$db->escape($description)." WHERE id=".$id;
		} else {
			$sql = "INSERT INTO scheduled_reports (user, report_type_id, report_id, recipients, period_id, filename, description)
				VALUES(".$db->escape($user).", ".$rep_type.", ".$saved_report_id.", ".$db->escape($recipients).", ".$period.", ".$db->escape($filename).", ".$db->escape($description).");";
		}

		try {
			$res = $db->query($sql);
		} catch (Kohana_Database_Exception $e) {
			return false;
		}

		if (!$id) {
			$id = (int)$res->insert_id();
		}
		return $id;
	}

	/**
	 * Update specific field for certain scheduled report
	 * Called from reports_Controller::save_schedule_item() through ajax
	 *
	 * @param $id int: The id of the report.
	 * @param $field string: The report field to update.
	 * @param $value string: The new value.
	 * @return true on succes. false on errors.
	 */
	public function update_report_field($id=false, $field=false, $value=false)
	{
		$id = (int)$id;
		$field = trim($field);
		$value = trim($value);
		$db = new Database(self::db_name);
		$sql = "UPDATE scheduled_reports SET `".$field."`= ".$db->escape($value)." WHERE id=".$id;
		try {
			$res = $db->query($sql);
		} catch (Kohana_Database_Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * Get the type of a report.
	 *
	 * @param $id The id of the report.
	 * @return Report type on success. False on errors.
	 */
	public function get_typeof_report($id=false)
	{
		$sql = "SELECT t.identifier FROM scheduled_reports sr, scheduled_report_types t WHERE ".
			"sr.id=".(int)$id." AND t.id=sr.report_type_id";
		$db = new Database(self::db_name);
		try {
			$res = $db->query($sql);
		} catch (Kohana_Database_Exception $e) {
			return false;
		}

		return count($res)!=0 ? $res->current()->identifier : false;
	}

	/**
	 * Get the id of a named report
	 *
	 * @param $identifier string: The name of the report
	 * @return False on errors. Id of the report on success.
	 */
	public function get_report_type_id($identifier=false)
	{
		$db = new Database(self::db_name);
		$sql = "SELECT id FROM scheduled_report_types WHERE identifier=".$db->escape($identifier).";";
		try {
			$res = $db->query($sql);
		} catch (Kohana_Database_Exception $e) {
			return false;
		}

		return count($res)!=0 ? $res->current()->id : false;
	}

	/**
	*	Fetch info on all defined report types, i.e all
	* 	types we can schedule
	*/
	public function get_all_report_types()
	{
		$db = new Database(self::db_name);
		$sql = "SELECT * FROM scheduled_report_types ORDER BY id";
		$res = $db->query($sql);
		return count($res) != 0 ? $res : false;
	}

	/**
	 * Fetch all info for a specific schedule.
	 * This includes all relevant data about both schedule
	 * and the report.
	 *
	 * @param $schedule_id The id of the schedule we're interested in.
	 * @return False on errors. Array with schedule-info on succes.
	 */
	public function get_scheduled_data($schedule_id=false)
	{
		$schedule_id = (int)$schedule_id;
		if (!$schedule_id) {
			return false;
		}

		$type = self::get_typeof_report($schedule_id);

		switch ($type) {
			case 'avail':
				$sql = "SELECT sr.user, sr.recipients, sr.filename, c.* FROM ".
					"scheduled_reports sr, avail_config c ".
					"WHERE sr.id=".$schedule_id." AND ".
					"c.id=sr.report_id";
				break;
			case 'sla':
				$sql = "SELECT sr.user, sr.recipients, sr.filename, c.* FROM ".
					"scheduled_reports sr, sla_config c ".
					"WHERE sr.id=".$schedule_id." AND ".
					"c.id=sr.report_id";
				break;
			default: return false;
		}

		$db = new Database(self::db_name);
		$res = $db->query($sql);
		$return = false;
		if (count($res) != 0) {
			$return = $res->result(false)->current();
			$id = $return['id'];
			$object_info = Saved_reports_Model::get_config_objects($type, $id);
			$objects = false;
			if (count($object_info) != 0) {
				foreach ($object_info as $row) {
					$objects[] = $row->name;
				}
			}
			$return['objects'] = $objects;

		} else {
			return false;
		}
		return $return;
	}

	/**
	 * Fetch info on reports to be sent for specific
	 * period (daily/weekly/monthly)
	 *
	 * @param $period_str string: { daily, weekly, monthly }
	 * @return Array of schedules for the specific period type
	 */
	public function get_period_schedules($period_str=false)
	{
		$period_str = trim(ucfirst($period_str));
		$db = new Database(self::db_name);

		$sql = "SELECT rt.identifier, r.id FROm scheduled_report_types rt, scheduled_reports r, scheduled_report_periods p ".
			"WHERE p.periodname=".$db->escape($period_str)." AND r.period_id=p.id AND rt.id=r.report_type_id";
		$res = $db->query($sql);
		return count($res) != 0 ? $res : false;
	}
}