<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadCustomer($inp)
	{
		$cust = new customer_class((int)$inp);
		return($cust->name);
	}
	function hamed_pdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	$parvaz_det_id = ((isset($_REQUEST["parvaz_det_id"]))?(int)$_REQUEST["parvaz_det_id"]:-1);
        $parvaz = new parvaz_det_class($parvaz_det_id);
	$grid = new jshowGrid_new('reserve_tmp','grid');
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'تاریخ';
	$grid->columnHeaders[3] = 'تعداد';
	$grid->columnHeaders[4] = 'مشتری';
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	$grid->columnFunctions[2] = "hamed_pdate";
	$grid->columnFunctions[4] = "loadCustomer";
	$grid->canEdit = FALSE;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
