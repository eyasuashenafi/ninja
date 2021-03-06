<?php defined('SYSPATH') OR die('No direct access allowed.');
$notes_url_target = config::get('nagdefault.notes_url_target', '*');
$action_url_target = config::get('nagdefault.action_url_target', '*');
?>
<div id="content-header"<?php if (isset($noheader) && $noheader) { ?> style="display:none"<?php } ?>>
	<div class="widget left w32" id="page_links">
		<ul>
		<li><?php echo _('View').', '.$label_view_for.':'; ?></li>
		<?php
		if (isset($page_links)) {
			foreach ($page_links as $label => $link) {
				?>
				<li><?php echo html::anchor($link, $label) ?></li>
				<?php
				}
		}
		?>
		</ul>
	</div>
<div class="clearservice"> </div>

	<?php
	if (!empty($widgets)) {
		foreach ($widgets as $widget) {
			echo $widget;
		}
	}
	?>

	<div id="filters" class="left">
	<?php
	if (isset($filters) && !empty($filters)) {
		echo $filters;
	}
	?>
	</div>
    <div class="clearservice"> </div>
</div>

<div class="widget left w98" id="status_group-grid">
<?php echo (isset($pagination)) ? $pagination : ''; ?>
<?php echo form::open('command/multi_action'); ?>
<?php echo html::image($this->add_path('icons/16x16/check-boxes.png'),array('style' => 'margin-bottom: -3px'));?> <a href="#" id="select_multiple_items" style="font-weight: normal"><?php echo _('Select Multiple Items') ?></a>

<?php if (count($group_details) && !empty($group_details)) {
	$nacoma_link = nacoma::link()===true;
	foreach ($group_details as $details) { ?>
	<table class="group_grid_table">
		<caption>
			<?php
				if ($nacoma_link)
					echo nacoma::link('configuration/configure/servicegroup/'.urlencode($details->servicegroup_name), 'icons/16x16/nacoma.png', _('Configure this servicegroup')).' &nbsp;';
				echo html::anchor('status/servicegroup/'.$details->servicegroup_name.'?style=detail', html::specialchars($details->alias));
				echo ' <span>('.html::anchor('extinfo/details/?type=servicegroup&host='.urlencode($details->servicegroup_name), html::specialchars($details->servicegroup_name)).')</span>';
			?>
		</caption>
		<tr>
			<th class="no-sort">&nbsp;</th>
			<th class="item_select">&nbsp;</th>
			<th class="no-sort"><?php echo _('Host') ?></th>
			<th class="no-sort"><?php echo _('Services') ?></th>
			<th class="no-sort"><?php echo _('Actions') ?></th>
		</tr>
		<tbody>
		<?php
		$i = 0;
		$result = Group_Model::get_group_info('service', $details->servicegroup_name);
		$prev_host = false;
		$next = false;
		$tmp = 0;
		$j = 0;

		if (count($result) && !empty($result))
		$next = $result->current();
		while ($next) {
			$host = $next;
			$next = $result->next()->valid() ? $result->current() : false;
			if ($host->host_name != $prev_host) {
				$i++;
				$j = 0;
				$tmp = 0;
		?>
		<tr class="<?php echo ($i%2 == 0) ? 'odd' : 'even' ?>">
			<td class="icon bl">
				<?php
					if (!empty($host->host_icon_image)) {
						echo html::anchor('extinfo/details/?type=host&host='.$host->host_name, html::image(Kohana::config('config.logos_path').$host->host_icon_image, array('style' => 'height: 16px; width: 16px', 'alt' => $host->host_icon_image_alt, 'title' => $host->host_icon_image_alt)),array('style' => 'border: 0px'));
					} ?>
			</td>
			<td class="item_select"><?php echo form::checkbox(array('name' => 'object_select[]'), $host->host_name); ?></td>
			<td style="white-space: normal; width: 180px"><?php echo html::anchor('extinfo/details/?type=host&host='.$host->host_name, html::specialchars($host->host_name)) ?></td>
			<td style="white-space: normal; line-height: 20px">
			<?php
			}

			$search = array(Current_status_Model::SERVICE_OK, Current_status_Model::SERVICE_WARNING, Current_status_Model::SERVICE_CRITICAL, Current_status_Model::SERVICE_UNKNOWN, Current_status_Model::SERVICE_PENDING);
			$replace = array('ok','warning','critical','unknown','pending');
			echo (($host->service_state != $tmp && $j != 0) ? '<br />' : '');
			echo (($host->service_state != $tmp || ($host->service_state == 0 && $j == 0)) ? html::image($this->add_path('icons/12x12/shield-'.strtolower(str_replace($search,$replace,$host->service_state)).'.png'), array('alt' => strtolower(str_replace($search,$replace,$host->service_state)), 'title' => strtolower(str_replace($search,$replace,$host->service_state)), 'class' => 'status-default')) : '');
			$service_class = 'status-'.strtolower(Current_status_Model::status_text($host->service_state, 'service'));
			echo (($host->service_state != $tmp || $j == 0) ? '' : ', ').html::anchor('extinfo/details/?type=service&host='.urlencode($host->host_name).'&service='.urlencode($host->service_description), $host->service_description, array('class' => $service_class));
			if ($host->service_state != $tmp)
				$tmp = $host->service_state;
			$j++;
			if (is_object($next) && $host->host_name != $next->host_name) {?>
			</td>
			<?php
				# also each host, under Actions
				#
			?>
			<td style="text-align: left; width: 133px">
				<?php
					if (isset($nacoma_path))
						echo html::anchor('configuration/configure/?type=host&name='.urlencode($host->host_name), html::image($icon_path.'nacoma.png', array('alt' => _('Configure this object using NACOMA (Nagios Configuration Manager)'), 'title' => _('Configure this object using NACOMA (Nagios Configuration Manager)'))),array('style' => 'border: 0px')).'&nbsp;';
					if (isset($pnp_path) && pnp::has_graph($host->host_name))
						echo '<a href="'.$pnp_path.'host='.$host->host_name.'&srv=_HOST_" style="border: 0px">'.html::image($icon_path.'pnp.png', array('alt' => _('Show performance graph'), 'title' => _('Show performance graph'), 'class' => 'pnp_graph_icon')).'</a>&nbsp;';
					echo html::anchor('extinfo/details/?type=host&host='.$host->host_name, html::image($icon_path.'extended-information.gif', array('alt' => _('View Extended Information For This Host'), 'title' => _('View Extended Information For This Host'))), array('style' => 'border: 0px')).'&nbsp;';
					if ( Kohana::config('config.nagvis_path') ) {
						echo html::anchor('statusmap/host/'.$host->host_name, html::image($icon_path.'locate-host-on-map.png', array('alt' => _('Locate Host On Map'), 'title' => _('Locate Host On Map'))), array('style' => 'border: 0px')).'&nbsp;';
					}
					echo html::anchor('status/host/?host='.urlencode($host->host_name), html::image($icon_path.'service-details.gif', array('alt' => _('View Service Details For This Host'), 'title' => _('View Service Details For This Host'))), array('style' => 'border: 0px')).'&nbsp;';
					if (!empty($host->host_action_url)) {
						echo '<a href="'.nagstat::process_macros($host->host_action_url, $host).'" style="border: 0px" target="'.$action_url_target.'">';
						echo html::image($this->add_path('icons/16x16/host-actions.png'), array('alt' => _('Perform extra host actions'), 'title' => _('Perform extra host actions')));
						echo '</a>&nbsp;';
					}
					if (!empty($host->host_notes_url)) {
						echo '<a href="'.nagstat::process_macros($host->host_notes_url, $host).'" style="border: 0px" target="'.$notes_url_target.'">';
						echo html::image($this->add_path('icons/16x16/host-notes.png'), array('alt' => _('View extra host notes'), 'title' => _('View extra host notes')));
						echo '</a>';
					}

				?>
			</td>
		</tr>
		<?php	} elseif (!is_object($next)) { ?>
			</td>
			<?php
				# Print for last object
				#
			?>
			<td style="text-align: left; width: 133px">
				<?php
					if (isset($nacoma_path))
						echo html::anchor('configuration/configure/host/'.$host->host_name, html::image($icon_path.'nacoma.png', array('alt' => _('Configure this object using NACOMA (Nagios Configuration Manager)'), 'title' => _('Configure this object using NACOMA (Nagios Configuration Manager)'))),array('style' => 'border: 0px')).'&nbsp;';
					if (isset($pnp_path) && pnp::has_graph($host->host_name))
						echo '<a href="'.$pnp_path.'host='.$host->host_name.'&srv=_HOST_" style="border: 0px">'.html::image($icon_path.'pnp.png', array('alt' => _('Show performance graph'), 'title' => _('Show performance graph'), 'class' => 'pnp_graph_icon')).'</a>&nbsp;';
					echo html::anchor('extinfo/details/?type=host&host='.urlencode($host->host_name), html::image($icon_path.'extended-information.gif', array('alt' => _('View Extended Information For This Host'), 'title' => _('View Extended Information For This Host'))), array('style' => 'border: 0px')).'&nbsp;';
					if ( Kohana::config('config.nagvis_path') ) {
						echo html::anchor('statusmap/host/'.$host->host_name, html::image($icon_path.'locate-host-on-map.png', array('alt' => _('Locate Host On Map'), 'title' => _('Locate Host On Map'))), array('style' => 'border: 0px')).'&nbsp;';
					}
					echo html::anchor('status/host/'.$host->host_name, html::image($icon_path.'service-details.gif', array('alt' => _('View Service Details For This Host'), 'title' => _('View Service Details For This Host'))), array('style' => 'border: 0px')).'&nbsp;';
					if (!empty($host->host_action_url)) {
						echo '<a href="'.nagstat::process_macros($host->host_action_url, $host).'" style="border: 0px" target="'.$action_url_target.'">';
						echo html::image($this->add_path('icons/16x16/host-actions.png'), array('alt' => _('Perform extra host actions'), 'title' => _('Perform extra host actions')));
						echo '</a>&nbsp;';
					}
					if (!empty($host->host_notes_url)) {
						echo '<a href="'.nagstat::process_macros($host->host_notes_url, $host).'" style="border: 0px" target="'.$notes_url_target.'">';
						echo html::image($this->add_path('icons/16x16/host-notes.png'), array('alt' => _('View extra host notes'), 'title' => _('View extra host notes')));
						echo '</a>';
					}

				?>
			</td>
		</tr>
			<?php } ?>
	<?php
			$prev_host = $host->host_name;
		}	# end each host ?>
		</tbody>
	</table>

<?php }
}	# end each group
else { ?>
	<table class="group_grid_table">
		<thead>
		<tr>
			<th class="no-sort"><?php echo _('Host') ?></th>
			<th class="no-sort"><?php echo _('Services') ?></th>
			<th class="no-sort"><?php echo _('Actions') ?></th>
		</tr>
		</thead>
		<tbody>
		<tr class="even">
			<td colspan="3"><?php echo $error_message ?></td>
		</tr>
		</tbody>
	</table>
	<?php
} ?>
<?php echo form::dropdown(array('name' => 'multi_action', 'class' => 'item_select auto', 'id' => 'multi_action_select'),
		array(
			'' => _('Select action'),
			'SCHEDULE_HOST_DOWNTIME' => _('Schedule downtime'),
			'ACKNOWLEDGE_HOST_PROBLEM' => _('Acknowledge'),
			'REMOVE_HOST_ACKNOWLEDGEMENT' => _('Remove problem acknowledgement'),
			'DISABLE_HOST_NOTIFICATIONS' => _('Disable host notifications'),
			'ENABLE_HOST_NOTIFICATIONS' => _('Enable host notifications'),
			'DISABLE_HOST_SVC_NOTIFICATIONS' => _('Disable notifications for all services'),
			'DISABLE_HOST_CHECK' => _('Disable active checks'),
			'ENABLE_HOST_CHECK' => _('Enable active checks')
			)
		); ?>
	<?php echo form::submit(array('id' => 'multi_object_submit', 'class' => 'item_select', 'value' => _('Submit'))); ?>
	<?php echo form::hidden('obj_type', 'host'); ?>
	<?php echo form::close(); ?>
	<br /><span id="multi_object_submit_progress" class="item_select"></span>
<?php echo (isset($pagination)) ? $pagination : ''; ?>
</div>

