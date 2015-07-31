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
	function loadVazTyp()
	{
		$out = array();
		$out[0] ='غیر فعال'; 
		$out[1] ='فعال'; 
		return($out);
	}
	function loadVazTyp2()
	{
		$out = array();
		$out[0] ='جدید'; 
		$out[1] ='قدیمی'; 
		return($out);
	}
	$gname = 'grid_customer_daryaft';
	$input =array($gname=>array('table'=>'agency','div'=>'main_div_customer_daryaft'));
	$xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='کد مقیم';
        $xgrid->column[$gname][2]['name'] ='نام';
        $xgrid->column[$gname][3]['name'] ='سایت';
	$xgrid->column[$gname][4]['name'] ='روکشی';
        $xgrid->column[$gname][5]['name'] ='وضعیت';
        $xgrid->column[$gname][5]['clist'] =  loadVazTyp();
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canAdd[$gname] = FALSE;
	$xgrid->canDelete[$gname] = FALSE;
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
<div id="main_div_customer_daryaft" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >

</div>
