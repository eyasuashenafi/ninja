<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="widget movable collapsable removable closeconfirm" id="widget-network_health">
	<div class="widget-header">
		<strong>Network Outages</strong>
	</div>
	<div class="widget-editbox">
		<!--Edit the widget here-->
	</div>
	<div class="widget-content">
		<!--This is widget content:<br /><br />-->
	<?php if (!$user_has_access) { ?>

		N/A

		<?php } else { ?>

		<?php	# @@@FIXME this (below) should be a link to outages controller (when there is one)	?>

		<?php echo $total_blocking_outages; ?> Outages

		<?php

			if (!empty($arguments)) {
				foreach ($arguments as $arg) {
					echo $arg."<br />";
				}
			}
		} // end if user_has_access
?>
	</div>
</div>

