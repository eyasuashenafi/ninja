<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Showlog controller
 *
 *  op5, and the op5 logo are trademarks, servicemarks, registered servicemarks
 *  or registered trademarks of op5 AB.
 *  All other trademarks, servicemarks, registered trademarks, and registered
 *  servicemarks mentioned herein may be the property of their respective owner(s).
 *  The information contained herein is provided AS IS with NO WARRANTY OF ANY
 *  KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A
 *  PARTICULAR PURPOSE.
 */
class Showlog_Controller extends Authenticated_Controller
{
	private $show;
	private $options = array();

	public function __construct()
	{
		parent::__construct();

		$this->get_options();
	}

	protected function get_options()
	{
		$this->options = $this->input->get();
		if (empty($this->options))
			$this->options = $this->input->post();

		if (isset($this->options['first']) && !empty($this->options['first'])) {
			$this->options['first'] = strtotime($this->options['first']);
		}
		if (isset($this->options['last']) && !empty($this->options['last'])) {
			$this->options['last'] = strtotime($this->options['last']);
		}
		if (!empty($this->options) && !empty($this->options['have_options'])) {
			if (!isset($this->options['host_state_options'])) {
				$this->options['host_state_options'] = array();
			}
			if (!isset($this->options['service_state_options'])) {
				$this->options['service_state_options'] = array();
			}
			return;
		}

		# set default if no options are found
		$this->options = array
			(
			 'state_type' => array('soft' => true, 'hard' => true),
			 'host_state_options' => array('r' => true, 'd' => true, 'u' => true),
			 'service_state_options' => array('r' => true, 'w' => true, 'c' => true, 'u' => true),
			 'hide_initial' => true
			 );

		$auth = Nagios_auth_Model::instance();
		if (!$auth->authorized_for_system_information) {
			$this->options['hide_process'] = 1;
			$this->options['hide_commands'] = 1;
		}
	}

	public function _show_log_entries()
	{
		$user = user::session('username');
		if (!empty($user))
			$this->options['user'] = $user;
		showlog::show_log_entries($this->options);
	}

	public function basic_setup()
	{
		$this->template->js_header = $this->add_view('js_header');
		$this->template->css_header = $this->add_view('css_header');

		$this->xtra_js[] = 'application/media/js/date.js';
		$this->xtra_js[] = 'application/media/js/jquery.datePicker.js';
		$this->xtra_js[] = 'application/media/js/jquery.timePicker.js';
		$this->xtra_js[] = 'application/media/js/jquery.fancybox.min.js';
		$this->xtra_js[] = $this->add_path('reports/js/common.js');
		$this->xtra_js[] = $this->add_path('showlog/js/showlog.js');

		$this->template->js_header->js = $this->xtra_js;

		$this->xtra_css[] = $this->add_path('reports/css/datePicker.css');
		$this->xtra_css[] = $this->add_path('showlog/css/showlog.css');
		$this->xtra_css[] = 'application/media/css/jquery.fancybox.css';
		$this->template->css_header->css = $this->xtra_css;
		$this->js_strings .= reports::js_strings();
		$this->template->inline_js = $this->inline_js;
		$this->template->js_strings = $this->js_strings;

		$host_state_options = array
			(_('Host down') => 'd',
			 _('Host unreachable') => 'u',
			 _('Host recovery') => 'r');
		$service_state_options = array
			(_('Service warning') => 'w',
			 _('Service unknown') => 'u',
			 _('Service critical') => 'c',
			 _('Service recovery') => 'r');

		$this->template->content->host_state_options = $host_state_options;
		$this->template->content->service_state_options = $service_state_options;
	}

	public function showlog()
	{
		$this->template->content = $this->add_view('showlog/showlog');
		$this->basic_setup();
		$this->template->title = _("Reporting » Event Log");

		$auth = Nagios_auth_Model::instance();
		$is_authorized = false;
		if ($auth->authorized_for_system_information) {
			$is_authorized = true;
		}

		$this->template->content->is_authorized = $is_authorized;
		$this->template->content->options = $this->options;
	}
}
