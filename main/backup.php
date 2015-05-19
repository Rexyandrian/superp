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
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = $conf->auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	$tim = perToEnNums(jdate("Y-m-d_H-i-s",strtotime(date("Y-m-d H:i:s"))));
	backup_class::backup(getcwd().'/backup/'.$conf->app.'_'.$tim.'.sqz.gz');
?>
<html>
	<body>
		<script language="javascript">
			window.location = 'backup/<?php echo $conf->app.'_'.$tim.'.sqz.gz';?>';
		</script>
	</body>
</html>
