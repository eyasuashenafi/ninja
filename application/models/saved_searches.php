<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 *	Saved searches model
 * 	Responsible for handling saved searches
 */
class Saved_searches_Model extends Model
{
	const tablename = 'ninja_saved_searches'; /**< Name of saved searches table */

	/**
	*	Save a search after validating that it doesn't
	*	already exist. Update if it does.
	*/
	public function save_search($query=false, $name=false, $description=false, $id=false)
	{
		$query = trim($query);
		$name = trim($name);
		$description = trim($description);

		if (empty($query) || empty($name)) {
			return false;
		}

		if (!empty($id) && self::get_search_by_id($id) != false) {
			# update report
			self::update_search($id, $query, $name, $description);
			return $id;
		}

		if($previously_stored_searches_same_query = self::get_search_by_query($query)) {
			foreach($previously_stored_searches_same_query as $previously_stored_search_same_query) {
				self::remove_search($previously_stored_search_same_query->id);
			}
		}

		$db = Database::instance();
		$user = Auth::instance()->get_user()->username;
		$sql = "INSERT INTO ".self::tablename." (username, search_name, search_query, search_description) ".
			"VALUES(".$db->escape($user).", ".$db->escape($name).", ".$db->escape($query).", ".$db->escape($description).")";

		$res = $db->query($sql);
		return $res->insert_id();
	}

	/**
	*	Update a saved search
	*/
	public function update_search($id=false, $query=false, $name=false, $description=false)
	{
		$db = Database::instance();
		$user = Auth::instance()->get_user()->username;
		$sql = "UPDATE ".self::tablename." SET username=".$db->escape($user).", search_name=".$db->escape($name).
			", search_query=".$db->escape($query).", search_description=".$db->escape($description)." ".
			"WHERE id=".(int)$id;

		$res = $db->query($sql);
		return true;
	}

	/**
	*	Get a saved search by id
	*/
	public function get_search_by_id($id=false)
	{
		$id = (int)$id;
		if (!$id) {
			return false;
		}

		$db = Database::instance();
		$user = Auth::instance()->get_user()->username;
		$sql = 'SELECT * FROM '.self::tablename.' WHERE id='.$id.' AND username = '.$db->escape($user);
		$res = $db->query($sql);
		return $res ? $res : false;
	}

	/**
	*	Get a saved search by query
	*/
	public function get_search_by_query($query)
	{
		$db = Database::instance();
		$user = Auth::instance()->get_user()->username;
		$sql = 'SELECT * FROM '.self::tablename.' WHERE search_query ='.$db->escape($query).' AND username = '.$db->escape($user);
		$res = $db->query($sql);
		return $res ? $res : false;
	}

	/**
	*	Fetch all the saved searches for current user
	*/
	public static function get_saved_searches()
	{
		$db = Database::instance();
		$user = Auth::instance()->get_user()->username;

		$sql = "SELECT * FROM ".self::tablename." WHERE username=".$db->escape($user)." ORDER BY search_name";
		$res = $db->query($sql);
		return $res ? $res : false;
	}

	/**
	*	Delete a saved search
	*/
	public function remove_search($id=false)
	{
		$id = trim($id);
		$id = (int)$id;
		if (self::get_search_by_id($id) !== false) {
			$db = Database::instance();
			$user = Auth::instance()->get_user()->username;
			$sql = "DELETE FROM ".self::tablename." WHERE id=".$id." AND username=".$db->escape($user);
			$res = $db->query($sql);
			return true;
		}
		return false;
	}
}
