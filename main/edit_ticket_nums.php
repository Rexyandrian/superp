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
	$msg="";
	$costumer_id = ((isset($_REQUEST["id"]))?(int)$_REQUEST["id"]:-1);

	$cust = new customer_class($costumer_id );
	if(isset($_REQUEST["aval"]))
	{
		$aval = (int)$_REQUEST["aval"];
		$akhar = ((isset($_REQUEST["akhar"]))?(int)$_REQUEST["akhar"]:-1);
		$cust->addTicketNumber($aval,$akhar);
var_dump($cust);
		$msg ="با موفقیت افزوده شد";
		die($_REQUEST['aval'].','.$_REQUEST['akhar']);
	}
	
?>
<head>
<script src="../js/jquery.js"></script>
</head>
<script>
function send_ok()
	{
		var inps = {};
		var tmp,val,nam;
		$.each($(".tagInfo"),function (id,field){
			val = $(field).val();
			nam = $(field).prop("name");
			inps[nam] = val;
		});
		tmp = $.param(inps);
		$.get("edit_ticket_nums.php?"+tmp+"&r="+Math.random(),function(result){
			if(result!='')
				alert('ثبت  به درستی انجام شد');
			else
				alert('در ثبت  مشکلی پیش آمده دوباره تلاش کنید');
			closeDialog();
		});
	}
</script>
		<div align="center">
			<br/>
			<br/>
				<table style="color:#000000;" >
					<tr>
						<th colspan="3" >ورود شماره تیکت های مربوط به <?php echo $cust->name; ?></th>
					</tr>
					<tr>
						<td>
							<br/>
							<br/>
							 از شماره:
							<input type="text" value="" name="aval" class="inp1 tagInfo" >
						</td>
						<td>
							<br/> 
							<br/>
							تا شماره:
							<input type="text" value="" name="akhar" class="inp1 tagInfo" >
						</td>
						<td> 
							<br/>
                                                        <br/>
							<input type="button" onclick="send_ok();"  value="ثبت"  class="inp1" >
						</td>
					</tr>
				</table>
				<br><br><br>
				<table style="color:#000000;background:#FFF8DC;" >
					<tr>
						<th cospan="2" > شماره تیکت های فعلی مربوط به <?php echo $cust->name; ?></th>
					</tr>
					<tr>
						<td>
							<?php 
								$shom = $cust->ticket_numbers;
								$out = "";
								foreach($shom as $i=>$shomare)
									{	
										$out .= $shomare." و " ;
									}
								$out = substr($out,0,-3);
								echo $out;
							?>
						</td>
					</tr>
				</table>
		</div>
	<?php echo $msg; ?>
