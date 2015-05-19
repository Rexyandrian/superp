<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$tim = perToEnNums(jdate("Y-m-d_H-i-s",strtotime(date("Y-m-d H:i:s"))));
	backup_class::backup(getcwd().'/backup/'.conf::app.'_'.$tim.'.sqz.gz');
?>
<html>
	<body>
		<script language="javascript">
			window.location = 'backup/<?php echo conf::app.'_'.$tim.'.sqz.gz';?>';
		</script>
	</body>
</html>
