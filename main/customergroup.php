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
	$gname = 'grid1';
	$input =array($gname=>array('table'=>'customergroup','div'=>'main_div'));
	$xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='نام';
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canAdd[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/grid.js"></script>
<script type="text/javascript" >
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	function changeWerc(ser)
	{
		var werc ='';
		$.each($('.'+ser),function(id,field)
		{
			werc +=((werc=='')?' where ':' and ')+" (`"+field.id+"` like '|"+trim(field.value)+"|') ";
		});

		var ggname ='<?php echo $gname; ?>';
		whereClause[ggname] = encodeURIComponent(werc);
		grid[ggname].init(gArgs[ggname]);
	}
</script>
<div align="center" style="margin:7px;" id="main_div"  >
</div>
