<?php
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
     /*   if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = $conf->auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);*/
	$out = '';
	if(!isset($_REQUEST['from_grp']) || !isset($_REQUEST['to_grp']))
		die($conf->access_deny);
	function loadAccByGrp($to_grp)
	{
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id`,`page_name` from `access` where `group_id`='$to_grp'",$p);
		$befor_acc = array();
		while($p as $t)
			$befor_acc[] = array('id'=>$t['id'],'page_name'=>$t['page_name']);
		return $befor_acc;
	}
	function loadDet($grp_id,$page_name)
	{
		$mysql = new mysql_class;
		$mysql->ex_sql("select `frase` from `access_det` where `acc_id` in ( select `id` from `access` where `group_id`=$grp_id and `page_name`='$page_name')",$q);
		$befor_acc_det = array();
		while($q as $t)
			$befor_acc_det[]=array('frase'=>$t['frase']);
		return $befor_acc_det;
	}
	function setAccByGrp($grp_id,$pages)
	{
		for($i = 0 ;$i<count($pages);$i++)
			$mysql->ex_sqlx("insert into `access` (`group_id`,`page_name`) values('$grp_id','".$pages[$i]['page_name']."') ");
	}
	function setDetByAcc($grp_id,$page_name,$frase)
	{
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id` from `access` where `page_name`='$page_name' and `group_id`='$grp_id' ",$q);
		if(isset($q[0]))
		{
			if($q[0]['id']>0)
				$mysql->ex_sqlx("insert into `access_det` (`acc_id`,`frase`) values('".$r['id']."','$frase') ");
		}
	}
	$from_grp = (int)$_REQUEST['from_grp'];
	$to_grp = (int)$_REQUEST['to_grp'];
	
	$befor_acc_to_grp = loadAccByGrp($to_grp);
	$acc_from_grp = loadAccByGrp($from_grp);
	$acc_to_grp = array();
	for($i = 0; $i<count($acc_from_grp);$i++)
	{
		if(!in_array($acc_from_grp[$i]['page_name'],$befor_acc_to_grp))
			$acc_to_grp[] = $acc_from_grp[$i];
	}
	setAccByGrp($to_grp,$acc_to_grp);
	
	$from_group = access_det_class::loadByGrp($from_grp);
	$to_group = access_det_class::loadByGrp($to_grp);
	//var_dump($to_group);
	for($i= 0 ;$i<count($from_group);$i++)
	{
		if(in_array($from_group[$i],$to_group))
			var_dump($from_group[$i]);
		else		
			setDetByAcc($to_grp,$from_group[$i]['page'],$from_group[$i]['frase']);
	}
	/*$acc_to_grp = loadAccByGrp($to_grp);
	
	
	for($i= 0 ;$i<count($acc_from_grp);$i++)
	{
		$acc_det_from_grp = loadDet($from_grp,$acc_from_grp[$i]['page_name']);
		$befor_det_to_grp = loadDet($to_grp,$acc_to_grp[$i]['page_name']);
		//var_dump($acc_det_from_grp);
		var_dump($befor_det_to_grp );
		for($j=0;$j<count($acc_det_from_grp);$j++)
		{
			if(!in_array($befor_det_to_grp[$j]['sfrase'],$acc_det_from_grp))
				echo "page=".$acc_from_grp[$i]['page_name']." frase=".$acc_det_from_grp[$j]['frase'];
				//setDetByAcc($to_grp,$acc_from_grp[$i]['page_name'],$acc_det_from_grp[$j]['sfrase']);
		}
	}
	//var_dump($acc_det_to_grp);
	/*
	echo "select * from `access` where `group_id` = $from_grp<br/>\n";
	mysql_class::ex_sql("select * from `access` where `group_id` = $from_grp",$q);
	while($r = mysql_fetch_array($q))
	{
		$from_ac_id = $r['id'];
		
		echo "insert into `access` (`group_id`,`page_name`) values ($to_grp,'".$r['page_name']."')<br/>\n";
		//mysql_class::ex_sqlx("insert into `access` (`group_id`,`page_name`) values ($to_grp,'".$r['page_name']."')");
		$qq = null;
		echo "select `id` from `access` where `group_id` = $to_grp and `page_name` = '".$r['page_name']."'<br/>\n";
		//mysql_class::ex_sql("select `id` from `access` where `group_id` = $to_grp and `page_name` = '".$r['page_name']."'",$qq);
		if($rr = mysql_fetch_array($qq))
			$to_ac_id = $rr['id'];
		$qq = null;
		echo "select * from `access_det` where `acc_id` = $from_ac_id <br/>\n";
		//mysql_class::ex_sql("select * from `access_det` where `acc_id` = $from_ac_id",$qq);
		while($rr = mysql_fetch_array($qq))
		{
			echo "insert into `access_det` (`acc_id`,`frase`) values ($to_ac_id,'".$rr['frase']."')<br/>\n";
			//mysql_class::ex_sqlx("insert into `access_det` (`acc_id`,`frase`) values ($to_ac_id,'".$rr['frase']."')");
		}
		*/
	echo("<script language=\"javascript\">alert('DONE');//window.close();</script>");
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
	</body>
</html>
