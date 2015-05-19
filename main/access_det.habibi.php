<?php	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
        function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$fields['acc_id'] = $_REQUEST['acc_id'];
                $fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
                        $fi.="`$field`,";
                        $valu .="'$value',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
                $query="insert into `access_det` $fi values $valu";
		$mysql = new mysql_class;
		$mysql->ex_sql('select `id` from `access_det` where `acc_id` = '.$fields['acc_id']." and `frase` = '".$fields['frase']."'",$q);
		if(!isset($q[0]))
	                $mysql->ex_sqlx($query);

        }
	function edit_item($id,$feild,$value)
	{
		if($feild == 'frase')
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select `id` from `access_det` where `acc_id` = (select `acc_id` from `access_det` where `id`=$id) and `frase` = '$value'",$q);
	                if(!isset($q[0]))
				$mysql->ex_sqlx("update `access_det` set `frase` = '$value' where `id`=$id");

		}
		else
			$mysql->ex_sqlx("update `access_det` set `$feild` = '$value' where `id`=$id");
	}
	if(!isset($_REQUEST['acc_id']))
		die("<script language=\"javascript\">alert('خطا در استفاده');</script>");
	$acc_id = (int)$_REQUEST['acc_id'];
	$grid = new jshowGrid_new("access_det","grid1");
	$grid->whereClause = " `acc_id` = $acc_id";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'عبارت امنیتی';
	$grid->addFunction = 'add_item';
	$grid->editFunction = 'edit_item';
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>
</html>
