<?php	
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
/*
        if (isset($_SESSION[$conf->app.'_user_id']) && isset($_SESSION[$conf->app.'_typ']))
        {
                if (!audit_class::isAdmin($_SESSION[$conf->app.'_typ']))
                {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
                }
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }
*/
	function loadGrooh()
        {
                $out=null;
		$mysql = new mysql_class;
                $mysql->ex_sql("select name,id from grooh order by id",$q);
                foreach($q as $r)
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadGroups()
	{
		$out = array();
		$mysql = new mysql_class;
		$mysql->ex_sql('select `name`,`id` from `grop` where `en`=1 order by `name`',$q);
		foreach($q as $r)
			$out[$r['name']] = (int)$r['id'];
		return ($out);
	}
	function loadPages()
	{
		$out = null;
		if ($handle = opendir('.')) 
		{
			while (false !== ($entry = readdir($handle))) 
			        $out[$entry] = $entry;
			closedir($handle);
		}
		return($out);
	}
	function loadDet($id)
	{
		$fr = access_det_class::loadByAcc($id);
		$fr = implode(' , ',$fr);	
		$out = "$fr<br/><u><span style=\"cursor:pointer;color:blue;\" onclick=\"wopen('access_det.php?acc_id=$id&','',500,400);\">ادامه</span></u>";
		return($out);
	}	
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
                $query="insert into `access` $fi values $valu";
		$mysql = new mysql_class;
                $mysql->ex_sqlx($query);

        }
	$pages = loadPages();
	$grid = new jshowGrid_new("access","grid1");
	$grid->divProperty = '';
	$grid->index_width = '20px';
	$grid->sortEnabled = TRUE;
	$grid->columnHeaders[0] = 'تعریف جزئیات';
	$grid->columnHeaders[1] = 'گروه';
	$grid->columnHeaders[2] = 'نام صفحه';
	$grid->columnLists[1] = loadGroups();
	$grid->columnLists[2] = $pages;
	$grid->columnFilters[1] = TRUE;
	$grid->columnFilters[2] = TRUE;
	$grid->columnFunctions[0] = 'loadDet';
	$grid->columnAccesses[0] = 0;
	$grid->addFunction = 'add_item';
	$grid->pageCount = 30;
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
                <script type="text/javascript" src="../js/tavanir.js"></script>
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
