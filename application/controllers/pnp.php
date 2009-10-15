<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Tactical overview controller
 * Requires authentication
 *
 * @package    NINJA
 * @author     op5 AB
 * @license    GPL
 * @copyright 2009 op5 AB
 *  op5, and the op5 logo are trademarks, servicemarks, registered servicemarks
 *  or registered trademarks of op5 AB.
 *  All other trademarks, servicemarks, registered trademarks, and registered
 *  servicemarks mentioned herein may be the property of their respective owner(s).
 *  The information contained herein is provided AS IS with NO WARRANTY OF ANY
 *  KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A
 *  PARTICULAR PURPOSE.
 */
class Pnp_Controller extends Authenticated_Controller {

	public $model = false;

	public function __construct()
	{
		parent::__construct();
		$this->model = new Current_status_Model();
	}

	public function index($host=false, $srv=false)
	{
		$host = urldecode($this->input->get('host', $host));
		$srv = urldecode($this->input->get('srv', $srv));

		$target_link = 'index.php';
		if (!empty($host))
				$target_link .= '?host='.$host;
		if (!empty($srv)) {
			$target_link .= '&srv='.$srv;
		} else {
			$target_link .= '&srv=_HOST_';
		}

		$this->template->content = '<iframe src="'.config::get('config.pnp4nagios_path').''.$target_link.'" style="width: 100%; height: 600px" frameborder="0" id="iframe"></iframe>';
		$this->template->title = $this->translate->_('Reporting » PNP');
		$this->template->js_header = $this->add_view('js_header');
		$this->template->disable_refresh = true;
		$this->xtra_js = array($this->add_path('/js/iframe-adjust.js'));
		$this->template->js_header->js = $this->xtra_js;
	}
}