<?php
include('../kernel.php');
include('../simplejson.php');
$r = new reserve_tmp_class(345);
var_dump((unserialize($r->parvaz_det_info)));