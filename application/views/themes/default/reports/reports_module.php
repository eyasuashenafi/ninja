<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<br />
<div id="error" style="margin:10px"><?php echo isset($error_msg) ? $error_msg : '' ?><br /><br />


<?php
if (isset($missing_objects))
	echo "<strong>"._('Missing objects').":</strong><br />";

echo isset($info) ? $info : '';
?>
</div>
