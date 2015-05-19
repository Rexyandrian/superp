<?php
	class addTicket_class
	{
		public function add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,	$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2="",$faghed_etebar_ghablaz="",$faghed_etebar_ghablaz2="",$faghed_etebar_badaz="",$faghed_etebar_badaz2="",$mabna_nerkh="",$mabna_nerkh2="",$vazeeat="OK",$vazeeat2="",$parvaz_saat2="",$parvaz_fatarikh2="",$parvaz_entarikh2="",$parvaz_shomare2="",$void="",$cash="",$totalfare="")
		{
			$out = "<table style='border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%;' border='0' >
			<tbody>
				<tr>
					<td style='width:7cm;text-align:left;font-size:11px;' >".$parvaz_shomare.".&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style='font-size:7px;direction:ltr;text-align:right;'  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
		<table style='border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;' border='2' >
		<tbody>
		<tr>
			<td align='center' valign='top' rowspan='3' colspan='4' style='width:4cm;' >
				<table width='100%'>
					<tr>
						<td style='font-size:7px;text-align:center;vertical-align:top;'>
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style='font-size:7px;text-align:left;vertical-align:top;'>
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan='3' style='direction:ltr;font-size:10px;'>".$ticket_regtime."</td>
					</tr>
					<tr>
						<td colspan='3' style='font-size:12px;' >
							<b>".$customer_name."</b>
						</td>
					</tr>
					<tr>
						<td colspan='3' style='font-size:8px;' >".$ticket_user."</td>
					</tr>
					<tr>
						<td style='font-size:7px;text-align:right;vertical-align:bottom;'>
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style='font-size:7px;text-align:left;vertical-align:bottom;'>
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan='3' style='height:0.8cm;font-size:7px;'  >
			<b>".$en_masir."<br />".$fa_masir."
			</b>
			</td>
			<td style='font-size: 6px;width:10%;text-align:left;vertical-align:top;'>
				ORIGIN/DESTINATION
			</td>
			<td style='text-align: left;height:0.8cm;' colspan='6'>
				<table style='font-size: 6px;width:100%;'>
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width='20px' >
									<br/>سامانه‌رزرواسیون‌بهار www.gcom.ir
						</td>
						<td>
								<img src='../img/arm_gray.png' width='25px' ><br/>
								رادان
						</td>
						<td style='font-size: 6px;width:10%;text-align:left;vertical-align:top;'>
							ISSUED BY
						</td>
						<td style='font-size: 5px;width:30%;text-align:left;' >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan='12' style='height:0.8cm;'>
				&nbsp;
			</td>
		</tr>
		<tr>		
			<td style='text-align: center;font-size:13px;' colspan='10'>
				<table width='100%' cellspacing='0' cellpadding='0' >
					<tr style='height:100%' >
						<td style='text-align: right;font-size:7px;vertical-align:top;'>
							نام مسافر(غیر قابل انتقال) :
						</td>		
							
						<td style='text-align:left;direction:ltr;font-size:7px;vertical-align:top;'>
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr style='height:0.7cm;'>
						<td colspan='2' style='font-size:13px;vertical-align:top;' >
							<b>".$ticket_fname.' '.$ticket_lname."</b>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr valign='top' style='height:0.8cm;' >
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>ALLOW<br/>بارمجاز</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
			</td>
			<td >
			<p style='text-align: center;'><span style='font-size: 7px;'>NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>FARE BASIS<br/>مبنای نرخ‬‫</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>STATUS<br/>وضعیت‬‫</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>TIME<br/>زمان‬‫</span></p>
			</td>
			<td colspan='2'>
			<p style='text-align: center;'><span style='font-size: 7px;'>DATE<br/>تاریخ</span></p>
			</td>
			<td colspan='2'>
			<p style='text-align: center;'><span style='font-size: 7px;'>FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>CARRIER<br/>حمل کننده</span></p>
			</td>
			<td>
			<p style='text-align: center;'><span style='font-size: 7px;'>‬‫
				<table width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td style='text-align: right;font-size:7px;vertical-align:top;'>
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style='text-align:left;direction:ltr;font-size:7px;vertical-align:top;'>
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</span>
			</p>
			</td>
			<td>
				<p style='text-align: center;'><span style='font-size: 7px;'>
					X/O
				</p>
			</td>

		</tr>
		<tr style='height:0.8cm;' >
			<td>
				<table width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td style='text-align: left;font-size: 6px;'>
						&nbsp;
						</td>
						<td style='text-align: left;font-size: 6px;'>
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style='text-align: right;font-size: 6px;'>
						وزن
							<br/>
							WT
						</td>
						<td style='text-align: left;font-size: 6px;'>
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style='text-align: center;'><b>".$bar_mojaz."</b></td>
			<td>".$faghed_etebar_ghablaz."</td>
			<td>".$faghed_etebar_badaz."</td>
			<td>".$mabna_nerkh."</td>
			<td><b>".$vazeeat."</b></td>
			<td><b>".$parvaz_saat."</b></td>
			<td colspan='2'>
				<table width='100%' height='100%' cellspacing='0' cellpadding='0' border='0'>
					<tr>
						<td  style='font-size:12px;' >
							<b>".$parvaz_fatarikh."</b>
						</td>
						<td style='border-right-style:solid;font-size:10px;'>
							<b>".$parvaz_entarikh."</b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan='2'>
				<table width='100%' height='100%' cellspacing='0' cellpadding='0' border='0'>
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style='border-right-style:solid;font-size:10px;'>
							<b>".$parvaz_shomare."</b>
						</td>
					</tr>
				</table>
			</td>
			<td style='font-size:12px;' >
				<b>".$parvaz_sherkat."</b>
			</td>
			<td >
				<table style='text-align: center;font-size: 6px;width:100%;' >
					<tr>
						<td style='direction:rtl;text-align:right' >
			از:
						</td>
						<td style='direction:ltr;text-align:left' >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style='font-size:12px;' >
							<b>".$parvaz_mabda."</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align='center' style='height:0.7cm;' >
			<td>
				<table width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td style='text-align: left;font-size: 6px;'>
						&nbsp;
						</td>
						<td style='text-align: left;font-size: 6px;'>
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style='text-align: right;font-size: 6px;'>
						وزن
							<br/>
							WT
						</td>
						<td style='text-align: left;font-size: 6px;'>
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style='text-align: center;'>".$bar_mojaz2."</td>
			<td>".$faghed_etebar_ghablaz2."</td>
			<td>".$faghed_etebar_badaz2."</td>
			<td>".$mabna_nerkh2."</td>
			<td><b>".$vazeeat2."</b></td>
			<td><b>".$parvaz_saat2."</b></td>
			<td colspan='2'>
				".$parvaz_fatarikh2."
			</td>
			<td colspan='2'>
				".$parvaz_entarikh2."
			</td>
			<td>".$parvaz_shomare2."</td>
			<td >
				<table style='text-align: center;font-size: 6px;width:100%;' >
					<tr>
						<td style='direction:rtl;text-align:right;' >
			به:
						</td>
						<td style='direction:ltr;text-align:left' >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style='font-size:12px;' >
							<b>".$parvaz_maghsad."</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style='height:0.8cm;' >
			<td style='text-align: center;' colspan='8' rowspan='3'>".$ticket_pic."</td>
			<td colspan='4'>
				<table style='text-align: center;font-size: 6px;width:100%;height:100%;' >
					<tr>
						<td style='direction:rtl;text-align:right;vertical-align:top;' >
			کد گروه:
						</td>
						<td style='direction:ltr;text-align:left;vertical-align:top;' >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style='text-align: center;font-size: 6px;width:100%;' >
					<tr>
						<td style='direction:rtl;text-align:right' >
			به:
						</td>
						<td style='direction:ltr;text-align:left' >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style='font-size: 10px;' >
							<b>".$void."</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='4'>
				<table style='text-align: center;font-size: 5px;width:100%;height:100%;' >
					<tr>
						<td style='direction:rtl;text-align:right;vertical-align:top;' >
			طرز پرداخت:
						</td>
						<td style='direction:ltr;text-align:left;vertical-align:top;' >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan='2' align='center' style='font-size: 10px;'>
							<b>".$cash."</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<span style='font-size:10px;text-align:left;'><b>".$totalfare."</b></span>
				<br/>	
				----
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style='height:0.7cm;' >
		<td colspan='5'>
			<table style='text-align: center;font-size: 5px;width:100%;' >
				<tr>
					<td style='direction:rtl;text-align:right' >
		رفرنس
					</td>
					<td style='direction:ltr;text-align:left' >
		PNR
					</td>
				</tr>
				<tr>
					<td colspan='2' style='font-size: 10px;' >
					<b>".$ticket_rahgiri."</b>					
					</td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		</table>";
			return $out;
		}
	}
?>
