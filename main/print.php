<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$gr = new jshowGrid_new('user','grid1');
	$gr->intial();
	$gr->executeQuery();
	$out = $gr->getGrid();
	$out = session_id();
?>
<html>
	<head>
		<title>
			آزمایش چاپ
		</title>
		<link rel="stylesheet" href="../css/style.css" type="text/css" />
		<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
	</head>
	<body dir="rtl">
		<?php echo $out; ?>
	</body>
</html>
