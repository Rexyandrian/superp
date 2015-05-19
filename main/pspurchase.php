<?php 
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die('error');
	include_once('../class/parser.php');
	
  	//var_dump($array);
//echo("<br /><br /><h1>");
//echo $array["resultObj"]["result"];
//echo("</h1>")
	if(isset($_GET['tref']) && isset($_GET['iN']) && isset($_GET['iD']))
	{
		$iN = (int)$_GET['iN'];
		$iD = trim($_GET['iD']);
		$result = post2https($_GET['tref'],'https://epayment.bankpasargad.com/CheckTransactionResult.aspx');
		$bank_out = makeXMLTree($result);
		//var_dump($bank_out);
		if($bank_out["resultObj"]["result"]=="True" && $iN==(int)$bank_out["resultObj"]['invoiceNumber'] && $iD==trim($bank_out['resultObj']['invoiceDate']) )
		{
			$pardakht = new pardakht_class((int)$bank_out['resultObj']['invoiceNumber']);
			$pardakht->bank_out = serialize($bank_out);
			$sanad_record_id = sanad_class::getLastSanad_record_id();
			$sanad_record_id_ticket = $sanad_record_id;
			//-------------ticket ----------
			$res_tmp =explode(',',$pardakht->sanad_record_id);
			$ghimat_kharid = 0;
			$ticket_ids = array();
			$ticket_error = FALSE;
			$ticket_ids = array();
			$shenavar = array();
			$tedad = 0;
			for($i=0;$i<count($res_tmp);$i++)
			{
				$reserve_tmp = new reserve_tmp_class($res_tmp[$i]);
				if($reserve_tmp->info!='' && $reserve_tmp->info!=null)
				{
					$info = $reserve_tmp->info['info']; 
					$parvaz =  $reserve_tmp->info['parvaz'];
					if($parvaz->is_shenavar)
						$shenavar[] = $parvaz;
					foreach($info as $ticket)
					{
						$ticket->sanad_record_id = $sanad_record_id;
						if(!$ticket->add($res_tmp[$i],$ticket_id))
							$ticket_error = TRUE;
						$ticket_ids[] = $ticket_id;
						if((int)$ticket->adult!=2)
							$tedad++;
					}
				}
				else
				{
					/*-------------------kiab--------*/
					//pay_class::revers($SaleOrderId,$SaleReferenceId);
					die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است !<br/><a href="index.php" >بازگشت</a></center></body></html>');
				}
			}
			if($ticket_error)
			{
				/*-------------------kiab--------*/
				ticket_class::clearTickets();
				//pay_class::revers($SaleOrderId,$SaleReferenceId);
				die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است !!<br/><a href="index.php" >بازگشت</a></center></body></html>');
			}
			else
			{
				$customer = new customer_class($info[0]->customer_id);
				$customer->buyTicket($sanad_record_id,$pardakht->mablagh,FALSE);
				$pardakht->update($sanad_record_id);
				//-------------- shenavar sanad------------
				$sanad_record_id = sanad_class::getLastSanad_record_id();
				$conf = new conf;
				$user_id = (isset($_SESSION[$conf->app.'_user_id']))?(int)$_SESSION[$conf->app.'_user_id']:-1;
				foreach($shenavar as $par)
					parvaz_det_class::sanad_shenavar_kharid($par,$tedad,$sanad_record_id,$user_id);			
				//Sabte sanade pardakht parvaz.------------	
				$sanad_record_id = sanad_class::getLastSanad_record_id();
				$tozihat = ' بابت خرید نقدی بلیت به شماره سند '.$sanad_record_id_ticket;
				customer_class::pardakht($sanad_record_id,$info[0]->customer_id,$pardakht->mablagh,$tozihat,$user_id);
			}
			$mysql = new mysql_class;
			foreach($res_tmp as $tmpid)
				$mysql->ex_sqlx("delete from `reserve_tmp` where `id` = ".$tmpid);
			//$rev = pay_class::settle($SaleOrderId,$SaleReferenceId);
			$rahgiri = pardakht_class::getBarcode($pardakht->id);
			$out = '<script langauge="javascript" >window.location = "finalticket2.php?ticket_type=0&sanad_record_id='.$sanad_record_id_ticket.'&rahgiri='.$rahgiri.'"</script>';
		}
		else
			$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، حداکثر تا سه روز کاری وجه به حساب شما بازگشت داده می شود
			<br/>
			<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، حداکثر تا سه روز کاری وجه به حساب شما بازگشت داده می شود.
			<br/>
			<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="css/style.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/tavanir.js"></script>
		<style>
		td { text-align: center; }
		</style>
		<title>
		</title>
	</head>
	<body style="background: #B5D3FF;padding-bottom: 0px;">
		<div align="center" style="background: #B5D3FF;margin:20px;" >
			<?php
				if($out == '')				
					echo "در حال تولید بلیت‌ها لطفاً منتظر بمانید";
				else
					echo $out; 
			?>
			<br/>
			<br/>
		</div>
		</center>
	</body>
</html>