<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_modir = $se->detailAuth('all');
	function loadParvazDets($parvaz_det_id)
	{
		$out = null;
		$no = date("Y-m-d");
		$par = new parvaz_det_class((int)$parvaz_det_id);
		mysql_class::ex_sql("select `parvaz_det`.`id`,`parvaz`.`shomare`,`parvaz_det`.`tarikh`,`parvaz_det`.`saat` from `parvaz_det` left join `parvaz` on (`parvaz_det`.`parvaz_id` = `parvaz`.`id`) where `parvaz_det`.`en`=1 and `parvaz_det`.`id` <> $parvaz_det_id and `parvaz_det`.`tarikh`>='$no' and `parvaz`.`mabda_id`=".$par->maghsad_id." and `parvaz`.`maghsad_id`=".$par->mabda_id." order by `tarikh`,`saat`,`parvaz_id`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['shomare'].'('.perToEnNums(audit_class::hamed_pdate($r['tarikh'])).' '.$r['saat'].')'] = $r['id'];
		return($out);
	}
	function add_item()
	{
		$parvaz_det_id = $GLOBALS['parvaz_det_id'];
		mysql_class::ex_sql('select `id` from `parvaz_jid` where `parvaz_det_id` = '.$parvaz_det_id.' and `jid` = '.$_REQUEST['new_jid'],$q);
		if(!($r=mysql_fetch_array($q)))
		{
			mysql_class::ex_sqlx('insert into `parvaz_jid` (`parvaz_det_id`,`jid`) values ('.$parvaz_det_id.','.$_REQUEST['new_jid'].')');
		}
	}
	function edit_item($id,$field,$value)
	{
		$parvaz_det_id = $GLOBALS['parvaz_det_id'];
		mysql_class::ex_sql('select `id` from `parvaz_jid` where `parvaz_det_id` = '.$parvaz_det_id.' and `jid` = '.$value,$q);
		if(!($r=mysql_fetch_array($q)))
                {
			mysql_class::ex_sqlx('update `parvaz_jid` set `jid` = '.$value.' where `id` = '.$id);
		}
	}
	$parvaz_det_id = (int)$_REQUEST['parvaz_det_id'];
	$GLOBALS['parvaz_det_id'] = $parvaz_det_id;
	$grid = new jshowGrid_new("parvaz_jid","grid1");
	$grid->whereClause=" `parvaz_det_id` = $parvaz_det_id";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "پرواز برگشت";
	$grid->columnLists[2] = loadParvazDets($parvaz_det_id);
	$grid->canAdd = $is_modir;
        $grid->canDelete = $is_modir;
        $grid->canEdit = $is_modir;
	$grid->addFunction = "add_item";
	$grid->editFunction = "edit_item";
//	$grid->deleteFunction = "delete_item";
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
