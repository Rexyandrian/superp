<?php
	include_once('../kernel.php');
	$slide = new slide_class;
	$index = ((isset($_REQUEST['index']))?(int)$_REQUEST['index']:0);
	echo $slide->loadData($index);
?>