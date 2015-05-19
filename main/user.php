<?php   
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['se'] = $se;
	function loadMoshtari()
	{
		$conf = new conf;
		$mysql = new mysql_class;
		$out=null;
		$se = $GLOBALS['se'];
		if($se->detailAuth('all'))
			$mysql->ex_sql("select `name`,`id` from `customers` where `en` = 1 order by `id`",$q);
		else
			$mysql->ex_sql("select `name`,`id` from `customers` where `en` = 1 and `id`='".(int)$_SESSION[$conf->app.'_customer_id']."' order by id",$q);
		foreach($q as $r)
			$out[(int)$r["id"]]=$r["name"];
		return $out;
	}
	function loadType()
	{
		$mysql = new mysql_class;
		$out=null;
		$mysql->ex_sql("select `name`,`id` from `grop` order by `id`",$q);
		foreach($q as $r)
			$out[$r['id']] = $r['name'];
		return $out;
	}
	function loadGroup()
        {
		$mysql = new mysql_class;
                $out=array();
		$mysql->ex_sql("select `name`,`id` from `customergroup` order by `id`",$q);
		foreach($q as $r)
                        $out[$r["id"]]= $r["name"];
                return $out;
        }
	function edit_item($tb,$id,$field,$value)
        {
		$mysql = new mysql_class;
		$conf = new conf;
		$lastvalue="";
		$mysql->ex_sql("select `$field` from `user` where `id`='$id'",$q);
		if (isset($q[0]))
			$lastvalue=$q[0][$field];
                $arg["toz"]="اصلاح فیلد $field از مقدار $lastvalue به مقدار $value در مدیریت کاربران";
		$arg["user_id"]=$_SESSION[$conf->app."_user_id"];
		$arg["host"]=$_SERVER["REMOTE_ADDR"];
		$arg["page_address"]=$_SERVER["SCRIPT_NAME"];
		$arg["typ"]=2;
		log_class::add($arg);
		if($field=='pass')
			$value = md5($value);
		$mysql->ex_sqlx("update $tb set `$field`='$value' where `id`='$id'");
		return(TRUE);
        }
	function userStatus($session_id)
	{
		$out = "&nbsp;";
		if($session_id != null)
			$out = "<img src=\"../img/check.png\" alt=\"ONLINE\" />";
		return($out);
	}
	function addUser($gname,$table,$fields,$column)
	{
		$out = FALSE;
		$conf = new conf;
		$mysql = new mysql_class;
		$user = $fields['user'];
		$pass = $fields['pass'];
		$grop_id =$fields['group_id'];
		$fname = $fields['fname'];
		$lname = $fields['lname'];
		$customer_id = $fields['customer_id'];
		$typ = $fields['typ'];
		$mysql->ex_sql("select count(`id`) as `cid` from `user` where `user`='$user'",$q);
		if((int)$q[0]['cid']==0 )
		{
		        $ln = $mysql->ex_sqlx("insert into `user` (`user`,`pass`,`group_id`,`fname`,`lname`,`customer_id`,`typ`) values ('$user','".(md5($pass))."','$grop_id','$fname','$lname',$customer_id,$typ)",FALSE);
			$user_id = $mysql->insert_id($ln);
			$mysql->close($ln);
			if($user_id>0)
			{
				$out = TRUE;
				$arg["toz"]="اضافه کردن کاربر به نام $fname - $lname";
				$arg["user_id"]=$_SESSION[$conf->app."_user_id"];
				$arg["host"]=$_SERVER["REMOTE_ADDR"];
				$arg["page_address"]=$_SERVER["SCRIPT_NAME"];
				$arg["typ"]=2;
				log_class::add($arg);

			}
		}
		return($out);
		
	}
	function nothing()
	{
		return('');
	}
	$username = "";
	$mysql = new mysql_class;
	$mysql->ex_sql("select `user` from `user` where `id` = ".$_SESSION[$conf->app."_user_id"],$uq);
	if(isset($uq[0]))
		$username = $uq[0]["user"];
	if($se->detailAuth('all'))
		$where = "1=1";	
	else
		$where = " `id`=".$_SESSION[$conf->app."_user_id"];
	$gname = "gname_user";
        $input =array($gname=>array('table'=>'user','div'=>'main_div_user'));
        $xgrid = new xgrid($input);
        $xgrid->whereClause[$gname] = $where." and `user` <> 'mehrdad' and `user`<>'test' ";
        $xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='مشتری';
	$xgrid->column[$gname][1]['clist'] = loadMoshtari();
	if(!$se->detailAuth('all'))
	{
		$xgrid->column[$gname][1]['access'] = 'a';
		$xgrid->column[$gname][4]['access'] = 'a';
	}
	$xgrid->column[$gname][2]['name'] ='نام';
	$xgrid->column[$gname][3]['name'] ='نام‌خانوادگی';
	$xgrid->column[$gname][4]['name'] ='کاربری';
	$xgrid->column[$gname][5]['name'] ='گذرواژه';
	$xgrid->column[$gname][5]['cfunction']=array('nothing');
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] =($se->detailAuth('all'))?'گروه کاربری':'';
	$xgrid->column[$gname][7]['clist'] = loadType();
	$xgrid->column[$gname][8]['name'] ='';
	$xgrid->column[$gname][9]['name'] ='';
        $xgrid->canAdd[$gname] = $se->detailAuth('all');
	$xgrid->addFunction[$gname] = 'addUser';
        $xgrid->canDelete[$gname] = $se->detailAuth('all');
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->editFunction[$gname] = 'edit_item';
        $out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
        $(document).ready(function(){
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
</script>
<div id="main_div_user"></div>
