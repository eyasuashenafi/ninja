<?php defined('SYSPATH') OR die('No direct access allowed.');

$csv_content = array('"'.implode('", "', array(
	$this->_get_summary_variant_by_report_type($report_type),
	'From: '.$used_options->get_date('start_time'),
	'To: '.$used_options->get_date('end_time'),
	'Duration: '.($options['end_time'] - $options['start_time'])
)).'"');

if(self::RECENT_ALERTS == $report_type) {
	// headers
	$csv_content[] = '"'.implode('", "', array(
		'TIME',
		'ALERT TYPE',
		'HOST',
		'SERVICE',
		'STATE TYPE',
		'INFORMATION'
	)).'"';

	// content
	foreach($result as $log_entry) {
		$csv_content[] = '"'.implode('", "', array(
			date($date_format, $log_entry['timestamp']),
			Reports_Model::event_type_to_string($log_entry['event_type'], $log_entry['service_description'] ? 'service' : 'host'),                                                $log_entry['host_name'],
			$log_entry['service_description'] ? $log_entry['service_description'] : 'N/A',
			$log_entry['hard'] ? _('Hard') : _('Soft'),
			$log_entry['output']
		)).'"';
	}
} elseif(self::TOP_ALERT_PRODUCERS == $report_type) {
	// summary of services
	// headers
	$csv_content[] = '"'.implode('", "', array(
		'HOST',
		'SERVICE',
		'ALERT TYPE',
		'TOTAL ALERTS'
	)).'"';

	// content
	foreach($result as $log_entry) {
		$csv_content[] = '"'.implode('", "', array(
			$log_entry['host_name'],
			isset($log_entry['service_description']) ? $log_entry['service_description'] : null,
			Reports_Model::event_type_to_string($log_entry['event_type'], 'service'),
			$log_entry['total_alerts']
		)).'"';
	}
} else {
	// custom settings, even more alert types to choose from;
	// also explains the nested layout of $result
	$header = array(
		'TYPE',
		'HOST',
		'STATE',
		'SOFT ALERTS',
		'HARD ALERTS',
		'TOTAL ALERTS'
	);
	switch($report_type) {
		case self::ALERT_TOTALS_HG:
			$label = _('Hostgroup');
			array_splice($header, 1, 1, 'HOSTGROUP');
			break;
		case self::ALERT_TOTALS_HOST:
			$label = _('Host');
			break;
		case self::ALERT_TOTALS_SERVICE:
			$label = _('Service');
			array_splice($header, 2, 0, 'SERVICE');
			break;
		case self::ALERT_TOTALS_SG:
			$label = _('Servicegroup');
			array_splice($header, 1, 1, 'SERVICEGROUP');
			break;
	}
	$csv_content[] = '"'.implode('", "', $header).'"';
	foreach ($result as $host_name => $ary) {
		$service_name = null;
		if($report_type == self::ALERT_TOTALS_SERVICE) {
			list($host_name, $service_name) = explode(';', $host_name);
		}
		foreach($ary['host'] as $state => $host) {
			$row = array(
				$label,
				$host_name,
				$this->host_state_names[$state],
				$host[0], # soft
				$host[1], # hard
				$host[0] + $host[1] # total
			);
			if($service_name) {
				array_splice($row, 2, 0, $service_name);
			}
		}
		$csv_content[] = '"'.implode('", "', $row).'"';
		foreach($ary['service'] as $state => $service) {
			$row = array(
				$label,
				$host_name,
				$this->service_state_names[$state],
				$service[0], # soft
				$service[1], # hard
				$service[0] + $service[1] # total
			);
			if($service_name) {
				array_splice($row, 2, 0, $service_name);
			}
		}
		$csv_content[] = '"'.implode('", "', $row).'"';
	}
}
