<?php
# $Id: missing1.php 1001 2011-08-08 02:22:55Z lbayuk $
# PHPlot test: Lines with missing data - 1
# This is a parameterized test. See the script named at the bottom for details.
$tp = array(
  'suffix' => " (lines, DrawBrokenLines)",   # Title part 2
  'DBLines' => True,        # DrawBrokenLines: True or False or NULL to omit
  'PType' => 'lines',    # Plot Type: lines, linepoints, squared
  );
require 'missing.php';
