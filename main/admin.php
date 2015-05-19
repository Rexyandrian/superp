<?php
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
       	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	function add_item($gname,$table,$fields,$col)
	{
/*
		$conf = new conf;
		function fieldToId($col,$fieldName)
		{
			$out = -1;
			foreach($col as $id=>$f)
				if($f['fieldname']==$fieldName)
					$out = $id;
			return $out;
		}
		$fields['app']=$conf->app;
		$fields['content']="<span class='msg'>".$fields['content']."</span>";
		$fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
			$f_id = fieldToId($col,$field);
			$fn = (isset($col[$f_id]['cfunction']) && isset($col[$f_id]['cfunction'][1]))?$col[$f_id]['cfunction'][1]:'';
                        $fi.="`$field`,";
                        $valu .="'".(($fn!='')?$fn($value):$value)."',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
		$query="insert into `$table` $fi values $valu";
		$mysql = new mysql_class;
		$mysql->ex_sqlx($query);
*/
		$conf = new conf;
		$fields['app']=$conf->app;
		$content='<span class="tabligh">'.$fields['content'].'</span>';
		$mysql = new mysql_class;
		$mysql->ex_sqlx("insert into `$table` (`content`, `app`) values ('$content','".$fields['app']."')");
		return(TRUE);
	}
	$gname = 'grid_admin';
	$input =array($gname=>array('table'=>'admin','div'=>'main_div_admin'));
	$xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='متن پیام';
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][3]['name'] ='';
	$xgrid->addFunction[$gname] ='add_item';
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
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
<div id="main_div_admin" >

</div>
