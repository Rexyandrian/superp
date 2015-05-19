<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
	{
		$out=jdate('Y/n/j',strtotime($str));
                return enToPerNums($out);
	}
	$parvaz_det_id = ((isset($_REQUEST["parvaz_det_id"]))?(int)$_REQUEST["parvaz_det_id"]:-1);
        $parvaz = new parvaz_det_class($parvaz_det_id);
	$par = new parvaz_class($parvaz->parvaz_id);
        $zarfiat = ((isset($_REQUEST["zarfiat"]))?(int)$_REQUEST["zarfiat"]:$parvaz->zarfiat);
	$out = '';
	if(isset($_REQUEST["zarfiat"]))
	{
		$mysql = new mysql_class;
		//die($parvaz->zarfiat." ".$parvaz->getZarfiat().' '.$zarfiat);
		if($zarfiat > $parvaz->zarfiat)
			$parvaz->kharidParvaz($zarfiat - $parvaz->zarfiat);
		else if($zarfiat < $parvaz->zarfiat)
		{
			$parvaz->mablagh_kharid = -1 * $parvaz->mablagh_kharid;
			$parvaz->kharidParvaz($zarfiat < $parvaz->zarfiat);
		}
		if( ($parvaz->zarfiat - $parvaz->getZarfiat())<=$zarfiat)
		{
			$out = $mysql->ex_sqlx("update `parvaz_det` set `zarfiat` = $zarfiat where `id` = $parvaz_det_id");
			$arg["toz"]=' تغییر ظرفیت پرواز شماره '.$parvaz->shomare.' تاریخ '.jdate("d / m / Y",strtotime($parvaz->tarikh)).' از مقدار '.$parvaz->zarfiat.' به مقدار '.$zarfiat;
			$arg["user_id"]=$_SESSION[$conf->app."_user_id"];
			$arg["host"]=$_SERVER["REMOTE_ADDR"];
			$arg["page_address"]=$_SERVER["SCRIPT_NAME"];
			$arg["typ"]=1;
			log_class::add($arg);
		}
		else
			$out='zakhire_err';
		die($out);
	}
?>
<script>
	$("#sabt").click(function(){
		var zarfiat_pre =<?php echo $zarfiat; ?>;
		var zarfiat = $("#zarfiat").val();
		var parvaz_det_id = $("#parvaz_det_id").val();
		$("#sabt").attr('disabled','disabled');
		$("#msg_div").removeClass();
		$("#msg_div").show();
		$("#msg_div").html('<img src="../img/status_fb.gif" >');
		if(parseInt(zarfiat_pre)!=parseInt(zarfiat))
			$.get("zarfiat.php?parvaz_det_id="+parvaz_det_id+"&zarfiat="+zarfiat+"&r="+Math.random(),function(result){
				result = trim(result);
				if(result=='ok')
				{
					$("#msg_div").html('');
					$("#msg_div").hide();
					$("#msg_div").html('ثبت با موفقیت انجام شد');
					$("#msg_div").addClass('msg');
					$("#msg_div").fadeIn(1500);
				}
				else if(result='zakhire_err')
				{
					$("#msg_div").html('');
					$("#msg_div").hide();
					$("#msg_div").html('تغییر ظرفیت به کمتر از ذخیره امکان پذیر نمی‌باشد در صورت نیاز ابتدا ذخیره ها را کاهش دهید');
					$("#msg_div").addClass('notice');
					$("#msg_div").fadeIn(1500);
				}
			});
		else
		{
			alert('مقدار ظرفیت تغییر نکرده است');
			$("#msg_div").html('');
		}
		$("#sabt").removeAttr('disabled');
		
	});
	$("#msg_div").click(function(){
		$("#msg_div").fadeOut(1500);
	});
</script>
<div align="center" style="margin-top:30px;">
	<p>
تعیین ظرفیت جهت پرواز 
		<?php echo $par->shomare." به تاریخ ".hamed_pdate($parvaz->tarikh); ?>
	</p>
	<input type="text" id="zarfiat" name="zarfiat" value="<?php echo $zarfiat;?>" />
	<input type="hidden" id="parvaz_det_id" name="parvaz_det_id" value="<?php echo $parvaz_det_id;?>" />
	<button id="sabt" >ثبت</button>
</div>
<div id="msg_div" align="center" style="cursor:pointer;" > 
</div>
