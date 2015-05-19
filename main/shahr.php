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
        $gname = "gname_shahr";
        $input =array($gname=>array('table'=>'shahr','div'=>'main_div_shahr'));
        $xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "1=1 order by `name`";
	$xgrid->column[$gname][0]['name'] = '';
	$xgrid->column[$gname][1]['name'] = 'نام‌شهر';
	$xgrid->column[$gname][2]['name'] = 'نام اختصاری';
        $xgrid->canAdd[$gname] = TRUE;
        $xgrid->canDelete[$gname] = TRUE;
        $xgrid->canEdit[$gname] = TRUE;
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
<div id="main_div_shahr"></div>
