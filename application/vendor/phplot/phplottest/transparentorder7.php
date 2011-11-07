<?php
# $Id: transparentorder7.php 1001 2011-08-08 02:22:55Z lbayuk $
# PHPlot test - transparency - truecolor, set transparent then set data color
require_once 'phplot.php';
$data = array(array('A', 6), array('B', 4), array('C', 2), array('D', 0));
$p = new PHPlot_truecolor;
$p->SetTitle('Truecolor, Set transparent, Set data color');
$p->SetDataValues($data);
$p->SetPlotType('bars');
$p->SetTransparentColor('yellow');
$p->SetDataColors('yellow');
$p->DrawGraph();