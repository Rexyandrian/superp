<?php
	class addTicket_print_class
	{
		public function add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,	$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2="",$faghed_etebar_ghablaz="",$faghed_etebar_ghablaz2="",$faghed_etebar_badaz="",$faghed_etebar_badaz2="",$mabna_nerkh="",$mabna_nerkh2="",$vazeeat="OK",$vazeeat2="",$parvaz_saat2="",$parvaz_fatarikh2="",$parvaz_entarikh2="",$parvaz_shomare2="",$void="",$cash="",$totalfare="")
		{
			$out = '<table style="border-style: solid;border-collapse: collapse;direction: rtl; width:90%;" border="0" >
			<tbody>
				<tr>
					<td style="width:7cm;>'.$parvaz_shomare.'&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style="direction:ltr;"  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
				<table border="1" >
					<tr>
					<td align="center" valign="top" style="width:6cm;" colspan="4">
						<table width="100%" border="0">
							<tr>
								<td style="height:0.8cm;">
	تاریخ و محل صدور :	
								</td>
								<td style="height:0.8cm;">
									&nbsp;
								</td>

								<td style="height:0.8cm;">
									DATE AND PLACE OF ISSUE
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:0.4cm;">'.$ticket_regtime.'</td>
							</tr>
							<tr>
								<td colspan="3" style="height:0.4cm;">
									<b>'.$customer_name.'</b>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="height:0.6cm;">'.$ticket_user.'</td>
							</tr>
							<tr>
								<td style="height:0.4cm;">
	صادر کننده 
								</td>
								<td style="height:0.4cm;">
									&nbsp;
								</td>
								<td style="height:0.4cm;">
									AGENT 
								</td>
							</tr>
						</table>
					</td>
					<td style="width:12cm;">
						<table border="0" width="100%" border="0">
							<tr>
								<td colspan="9" align="center" style="border-bottom-style:solid;border-left-style:solid;">
									<b>
					<br/>'.$en_masir.'<br />'.$fa_masir.'</b>
								</td>
								<td colspan="7" style="border-bottom-style:solid;border-left-style:solid;">
									ORIGIN/DESTINATION
								</td>
								<td colspan="6" style="height:0.8cm;" style="border-bottom-style:solid;">
									<img src="../img/arm_gcom.png" width="20px" >

									<br/>سامانه‌رزرواسیون‌بهار www.gcom.ir
								</td>
								<td colspan="3" style="height:0.8cm;" style="border-bottom-style:solid;">
									<img src="../img/arm_gray.png" width="25px" ><br/>
							
								</td>
								<td colspan="6" style="height:0.8cm;" align="left" style="border-bottom-style:solid;">
									ISSUED BY
								</td>	
								<td colspan="10" style="height:0.8cm;" align="left" style="border-bottom-style:solid;">
									PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
								</td>
							</tr>
							<tr>
								<td  colspan="10" style="border-bottom-style:solid;">
									<br/><br/><br/>&nbsp;
								</td>
								<td colspan="10" style="height:2cm;" style="border-bottom-style:solid;">
									&nbsp;
								</td>
								<td colspan="10" style="height:2cm;" style="border-bottom-style:solid;">
									&nbsp;
								</td>
								<td colspan="11" style="height:2cm;" style="border-bottom-style:solid;">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td  colspan="10" >
									نام مسافر(غیر قابل انتقال) :
								</td>
								<td align="center" colspan="20" >
									<br/>
									<br/>
						<b>'.$ticket_fname.' '.$ticket_lname.'</b>
								</td>
								<td colspan="10" >
									NAME OF PASSENGER :
								</td>
							</tr>
						</table>
					</td>
					</tr>
					<tr valign="top" style="height:0.8cm;">
						<td>
							<p style="text-align: center;"><span >بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
						</td>
						<td>

							<p style="text-align: center;"><span >ALLOW<br/>بارمجاز</span></p>
						</td>‬
						<td>
							<p style="text-align: center;"><span >NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
						</td>
						<td colspan="1">
							<p style="text-align: center;"><span >NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
						</td>
						<td>	
							<table border="1">
								<tr >
									<td colspan="2" style="height:0.9cm;">
										<p style="text-align: center;"><span >FARE BASIS<br/>مبنای نرخ‬‫</span></p>
									</td>
									<td colspan="2">
										<p style="text-align: center;"><span >STATUS<br/>وضعیت‬‫</span></p>
									</td>
									<td colspan="3">
										<p style="text-align: center;"><span >TIME<br/>زمان‬‫</span></p>
									</td>

									<td colspan="3">
										<p style="text-align: center;"><span >DATE<br/>تاریخ</span></p>
									</td>
									<td colspan="3">
										<p style="text-align: center;"><span>FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
									</td>
									<td colspan="2">
										<p style="text-align: center;"><span>CARRIER<br/>حمل کننده</span></p>
									</td>
									
									<td colspan="10" >
								<span>
		برای مسافرت معتبر نیست		NOT GOOD FOR PASSAGE
								</span>
									</td>		
									<td>
									X/O
									</td>
								</tr>
							</table>			
						</td>
					</tr>
					<tr valign="top" style="height:0.6cm;">
						<td>
							<p style="text-align: left;">
								<span >
									PCS
									<br/>
		تعداد
								</span>
							</p>	
							<p style="text-align: right;">
								<span >
		وزن
									<br/>
									WT
								</span>
							</p>	
						</td>
						<td>

							<p style="text-align: center;"><span ><br/><b>'.$bar_mojaz.'</b></span></p>
						</td>‬
						<td>
							<p style="text-align: center;"><span >'.$faghed_etebar_ghablaz.'	
</span></p>
						</td>
						<td>
							<p style="text-align: center;"><span >'.$faghed_etebar_badaz.'</span></p>
						</td>
						<td style="height:0.8cm;">	
							<table border="1">
								<tr>
									<td width="0.91cm" style="height:0.8cm;">
										<p style="text-align: center;"><span >'.$mabna_nerkh.'<br/><br/><br/><br/></span></p>
									</td>
									<td width="0.92cm">
										<p style="text-align: center;"><span ><b>'.$vazeeat.'</b></span></p>
									</td>
									<td width="1.41cm">
										<p style="text-align: center;" align="center"><span ><b><br/>'.$parvaz_saat.'</b>‬‫</span></p>
									</td>

									<td width="1.38cm">
																		<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td align="center">
										<br/><br/><b>'.$parvaz_fatarikh.'</b>
										</td>
										<td style="border-right-style:solid;" align="center">
										<br/><br/><b>'.$parvaz_entarikh.'</b>
<br/><br/><br/>
										</td>
									</tr>
									</table>
									</td>
									<td  width="1.38cm">
										<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td align="center">
							<br/><br/><b>Y</b>

						</td>
						<td align="center" style="border-right-style:solid;">
							<br/><br/><b>'.$parvaz_shomare.'</b><br/><br/><br/>
						</td>
					</tr>
				</table>
									</td>
									<td width="0.93cm" style="height:0.8cm;">
										<p style="text-align: center;"><span><br/><br/><b>'.$parvaz_sherkat.'</b></span></p>
									</td>
									
									<td width="4.6cm" >
								
		<table style="text-align: center;width:100%;" >
					<tr>
						<td >
							<br/><br/>
			از
						</td>
						<td style="direction:ltr;text-align:left" >

			&nbsp;from:
						</td>
					</tr>
					<tr>
						<td colspan="2" >
							<br/><br/>
							<b>'.$parvaz_mabda.'</b>
						</td>
					</tr>
				</table>
									</td>		
									<td width="0.48cm">
									&nbsp;
									</td>
								</tr>
							</table>			
						</td>
					</tr>
					<tr valign="top" >
						<td>
							<p style="text-align: left;">
								<span >
									PCS
									<br/>
		تعداد
								</span>
							</p>	
							<p style="text-align: right;">
								<span >
		وزن
									<br/>
									WT
								</span>
							</p>	
						</td>
						<td>

							<p style="text-align: center;"><span >&nbsp;</span></p>
						</td>‬
						<td>
							<p style="text-align: center;"><span >'.$bar_mojaz2.'</span></p>
						</td>
						<td>
							<p style="text-align: center;"><span >'.$faghed_etebar_ghablaz2.'</span></p>
						</td>
						<td >	
							<table border="1" >
								<tr>
									<td width="0.91cm" >
										<p style="text-align: center;"><span >'.$mabna_nerkh2.'<br/><br/><br/><br/>&nbsp;</span></p>
									</td>
									<td width="0.92cm">
										<p style="text-align: center;"><span >'.$vazeeat2.'</span></p>
									</td>
									<td width="1.41cm">
										<p style="text-align: center;"><span >'.$parvaz_saat2.'‬‫</span></p>
									</td>
									<td width="1.38cm">
										<p style="text-align: center;"><span >
									<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
									<tr>
							<td>'.$parvaz_fatarikh2.'</td>
							<td>'.$parvaz_entarikh2.'</td>
									</tr>
									</table></span></p>
									</td>
									<td  width="1.38cm">
										<p style="text-align: center;">
										<span>
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
								<td>'.$parvaz_shomare2.'</td>
											<td>
											&nbsp;
											</td>
										</tr>
										</table>
										</span>
										</p>
									</td>
									<td width="0.93cm">
										<p style="text-align: center;"><span>&nbsp;</span></p>
									</td>
									<td width="4.6cm" >
									
									<table style="text-align: center;width:100%;height:0.8cm;">
									<tr>
										<td style="direction:rtl;text-align:right" >
			&nbsp;&nbsp;از:
										</td>
										<td style="direction:ltr;text-align:left" >

									from:&nbsp;&nbsp;
										</td>
									</tr>
									<tr>
										<td colspan="2" >
										<b>'.$parvaz_maghsad.'</b>
										</td>
									</tr>
									</table>
									
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr valign="top" style="height:0.5cm;">
						<td width="10.62cm">'.$ticket_pic.'</td>
						
						<td style="height:0.8cm;" width="2.32cm">
							<table border="1">
								<tr>	
									<td width="2.32cm" height="0.8cm">
							<table style="text-align: center;width:100%;"  border="0">
								<tr>
									<td style="direction:rtl;text-align:right" >
	&nbsp;&nbsp;کد گروه:
									</td>
									<td style="direction:ltr;text-align:left" colspan="2">
									TOUR CODE:
									</td>
								</tr>
							</table>
			
									</td>
									<td width="4.6cm">
							<table style="text-align: center;width:100%;"  border="0">
								<tr>
									<td style="direction:rtl;text-align:right" >
			&nbsp;&nbsp;به:
									</td>
									<td style="direction:ltr;text-align:left" >
									&nbsp;&nbsp;to:
									</td>
								</tr>
								<tr>

									<td align="center" colspan="2">
										&nbsp;&nbsp;<b>'.$void.'</b>
									</td>
								</tr>
							</table>
									</td>
								</tr>
								<tr>	
									<td width="2.32cm" height="0.8cm">
						<table style="text-align: center;width:100%;"  border="0">
								<tr>
									<td style="direction:rtl;text-align:right" >
			&nbsp;طرز پرداخت:
									</td>
									<td style="direction:ltr;text-align:left" >
									&nbsp;FORM OF PAYMENT:
									</td>
								</tr>
								<tr>

									<td align="center" colspan="2">
										&nbsp;&nbsp;<b>'.$cash.'</b>
									</td>
								</tr>
							</table>
									</td>
									<td width="4.6cm" align="center">

										<br/><br/><b>'.$totalfare.'</b> 
										<br/>	
				----
									</td>
								</tr>
								<tr>	
									<td style="direction:rtl;text-align:right"><b>
			&nbsp;رفرنس
									</b></td>
									<td width="4.6cm" align="center">
							<table style="text-align: center;width:100%;"  border="0">
								<tr>
									<td style="direction:rtl;text-align:right" colspan="8"><b>
			&nbsp;
									</b></td>
									<td style="direction:ltr;text-align:left" colspan="30"><b>
									&nbsp;PNR
									</b></td>
								</tr>
								<tr>
									<td style="direction:rtl;text-align:right" colspan="8"><b>
			&nbsp;
									</b></td>
									<td align="center" colspan="30">
										&nbsp;&nbsp;<b>'.$ticket_rahgiri.'</b>
									</td>
								</tr>
							</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
			return $out;
		}
	}
?>
