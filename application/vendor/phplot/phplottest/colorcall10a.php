<?php
# $Id: colorcall10a.php 1001 2011-08-08 02:22:55Z lbayuk $
# Color callback - data-data-error line plot with callback
# See the script named below for details.
$plot_type = 'lines';
function pick_color($unused_img, $unused_passthru, $row, $col)
{
    return $row + $col;
}
require 'colorcall10.php';
