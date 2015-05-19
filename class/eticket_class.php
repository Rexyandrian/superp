<?php
	class eticket_class
	{
		public $regTime = '0000-00-00 00:00:00';
		public $customer_id = -1;
		public $user_id = -1;
		public $parvaz_det_id = array(-1);
		public $flight_class = array('Y','');
		public $ticket_fname = '';
		public $ticket_lname = 'ناشناخته';
		public $serial = -1;
		public $route = "ROUTE 1<br/>مسیر ۱";
		public $our_logo = '<img src="../img/arm_gcom.png" width="20px" >';
		public $our_logo_text = 'سامانه رزرواسیون بهار www.gcom.ir';
		public $customer_logo = '<img src="../img/arm_gray.png" width="30px" >';
		public $customerlogotext = 'آلفاسیر';
		public $allow = array('20','');
		public $fare_basis = array('OK','');
		public $payment = 'CASH';
		public $fare = '<span style="text-align:left;"><b>TOTAL FARE </b></span>	<br/>';
		public $refrence = '';
		public $isCopon = FALSE;
		public $isAdmin = FALSE;
		private $ticket_ok = FALSE;
		public function __construct($ticket_id = -1)
		{
			$conf = new conf;
			if((int)$ticket_id > 0)
			{
			        $ticket = new ticket_class($ticket_id);
				$this->ticket_ok = ($ticket->getId() > 0);
			        $ticket_back = null;
			        $ticket_back_id = $ticket->loadBargasht();
		        	if($ticket_back_id>0)
                			$ticket_back = new ticket_class($ticket_back_id);
			        $this->regTime = $ticket->regtime;
			        $this->customer_id = $ticket->customer_id;
			        $this->parvaz_det_id = array($ticket->parvaz_det_id);
		        	if($ticket_back != null && $ticket_back->getId() > 0 && $ticket_back->parvaz_det_id>0)
			        {
			                $this->parvaz_det_id[] = $ticket_back->parvaz_det_id;
			                $this->allow[1] = '20';
		        	        $this->flight_class[1] = 'Y';
			        }
			        $this->ticket_fname = $ticket->fname;
			        $this->ticket_lname = $ticket->lname;
		        	$this->serial = $ticket->shomare;
			        $this->refrence = ticket_class::rahgiriToCode($ticket->sanad_record_id,$conf->rahgiri);
			}
		}
		public function loadCity($inp)
	        {
			$conf = new conf;
        	        $inp = (int)$inp;
                	$out = "";
			$mysql = new mysql_class;
	                $mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
        	        if(isset($q[0]))
                        	$out = $q[0]["name"];
        	        return($out);
	        }
	        public function loadSherkat($inp)
        	{
			$conf = new conf;
                	$inp = (int)$inp;
	                $out = "";
			$mysql = new mysql_class;
	                $mysql->ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
        	        if(isset($q[0]))
                	        $out = $q[0]["name"];
	                return($out);
        	}
		public function get()
		{
			$conf = new conf;
			$out = '';
			if(!$this->ticket_ok)
				exit();
			$customer = new customer_class($this->customer_id);
			$parvaz = new parvaz_det_class($this->parvaz_det_id[0]);
			$par = new parvaz_class($parvaz->parvaz_id);
			$this->fare .='<span style="font-size:6px;" >'.monize($par->ghimat_ticket).'ریال </span>';
			$allow0 = $this->allow[0];
			$allow1 = (($this->isCopon)?$this->allow[1]:'');
			$ok = '';
			$saat = '';
			$par2_dates = "";
			$fl2 = '';
			$flight_class0 = $this->flight_class[0];
			$flight_class1 = $this->flight_class[1];
			$city0 = $this->loadCity($parvaz->mabda_id);
			$city1 = $this->loadCity($parvaz->maghsad_id);
			$parvaz_back = ((isset($this->parvaz_det_id[1]))?new parvaz_det_class($this->parvaz_det_id[1]):null);
			$sherkat0 = $this->loadSherkat($parvaz->sherkat_id);
			$b = new barcode_class(ticket_class::rahgiriToCode($this->serial,$conf->rahgiri));
			$barcode =  '<img src="../img/barcodes/'.ticket_class::rahgiriToCode($this->serial,$conf->rahgiri).'.png" alt="'.ticket_class::rahgiriToCode($this->serial,$conf->rahgiri).'" />';
			if($parvaz_back != null && $this->isCopon)
			{
				$sherkat1 = $this->loadSherkat($parvaz_back->sherkat_id);
				$city2 = $this->loadCity($parvaz_back->maghsad_id);
				$ok = 'OK';
				$saat = $parvaz_back->saat;
				$perdate1 = perToEnNums(jdate("m/d",strtotime($parvaz_back->tarikh)));
				$endate1 = date("d M",strtotime($parvaz_back->tarikh));
				$par2_dates = "
                                <table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                                        <tr>
                                                <td  style=\"font-size:12px;\" >
                                                        <b>$perdate1</b>
                                                </td>
                                                <td style=\"border-right-style:solid;font-size:10px;\">
                                                        <b>$endate1</b>
                                                </td>
                                        </tr>
                                </table>";
				$fl2 = "
                                <table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                                        <tr>
                                                <td>
                                                        <b>$flight_class1</b>
                                                </td>
                                                <td style=\"border-right-style:solid;font-size:10px;\">
                                                        <b>$parvaz_back->shomare</b>
                                                </td>
                                        </tr>
                                </table>";
			}
			else
			{
				$sherkat1 = '';
				$city2 = 'VOID';
			}
			$user = new user_class($this->user_id);
			$perdate = perToEnNums(jdate("m/d",strtotime($parvaz->tarikh))); 
			$endate = date("d M",strtotime($parvaz->tarikh)); 
			$this->serialText = ($this->isAdmin)?'<input type="text" style="border-style:none;font-weight:bold;width:100%;text-align:center;" value="'.$this->serial.'" />':$this->serial;
			$this->refrenceText = ($this->isAdmin)?'<input type="text" style="border-style:none;font-weight:bold;width:100%;text-align:center;" value="'.$this->refrence.'" />':$this->refrence;
			$out = <<<et
		<span>Serial(Voucher/ Passenger No):$this->serial</span>
		<table style="border-style: solid;border-collapse: collapse; font-size: 9px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td align="center" valign="top" rowspan="2" colspan="4" style="width:4cm;" >
				<table width="100%">
					<tr>
						<td style="font-size:7px;text-align:center;vertical-align:top;">
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:top;">
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan="3" style="direction:ltr;font-size:10px;">
							$this->regTime
						</td>
					</tr>
					<tr>
						<td colspan="3" style="font-size:12px;" >
							<b>$customer->name</b>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="font-size:8px;" >
							$user->fname $user->lname
						</td>
					</tr>
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:bottom;">
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:bottom;">
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
				$this->route
			</b>
			</td>
			<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
				ORIGIN/DESTINATION
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="6">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								$this->our_logo
									<br/>
								$this->our_logo_text
						</td>
						<td>
								$this->customer_logo
								<br/>
								$this->customerlogotext
						</td>
						<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
							ISSUED BY
						</td>
						<td style="font-size: 5px;width:30%;text-align:left;" >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
<!--
		<tr>
			<td colspan="10" style="height:0.2cm;">
				&nbsp;
			</td>
		</tr>
-->
		<tr>		
			<td style="text-align: center;font-size:13px;" colspan="10">
				<table width="100%" cellspacing="0" cellpadding="0" >
					<tr style="height:100%" >
						<td style="text-align: right;font-size:7px;vertical-align:top;">
							نام مسافر(غیر قابل انتقال) :
						</td>		
							
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr style="height:0.7cm;">
						<td colspan="2" style="font-size:13px;vertical-align:top;" >
							<b>$this->ticket_fname $this->ticket_lname</b>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr valign="top" style="height:0.5cm;" >
			<td >
			<span style="font-size: 7px;">بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span>
			</td>
			<td>
			<span style="font-size: 7px;">ALLOW<br/>بارمجاز</span>
			</td>
			<td>
			<span style="font-size: 7px;">NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span>
			</td>
			<td >
				<span style="font-size: 7px;">NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span>
			</td>
			<td>
				<span style="font-size: 7px;">FARE BASIS<br/>مبنای نرخ‬‫</span>
			</td>
			<td>
				<span style="font-size: 7px;">STATUS<br/>وضعیت‬‫</span>
			</td>
			<td>
				<span style="font-size: 7px;">TIME<br/>زمان‬‫</span>
			</td>
			<td colspan="2">
				<span style="font-size: 7px;">DATE<br/>تاریخ</span>
			</td>
			<td colspan="2">
				<span style="font-size: 7px;">FLIGHT/CLASS<br/>پرواز/کلاس</span>
			</td>
			<td>
				<span style="font-size: 7px;">CARRIER<br/>حمل کننده</span>
			</td>
			<td>
				<table width="100%" cellspacing="0" cellpadding="0" style="font-size: 7px;" >
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</td>
			<td>
				<p style="text-align: center;"><span style="font-size: 7px;">
					X/O
				</p>
			</td>

		</tr>
		<tr style="height:0.8cm;" >
			<td>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;"><b>$allow0</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b>$parvaz->saat</b></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>$perdate</b>
						</td>
						<td style="border-right-style:solid;">
							<b>$endate</b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>$flight_class0</b>
						</td>
						<td style="border-right-style:solid;">
							<b>$parvaz->shomare</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<b>$sherkat0</b>
			</td>
			<td >
				<table style="text-align: center;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;font-size: 6px;" >
			از:
						</td>
						<td style="direction:ltr;text-align:left;font-size: 6px;" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>$city0</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="center" style="height:0.7cm;" >
			<td>
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;"><b>$allow1</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>$ok</b></td>
			<td><b>$saat</b></td>
			<td colspan="2">
$par2_dates
			</td>
			<td colspan="2">
$fl2
			</td>
			<td><b>$sherkat1</b></td>
			<td >
				<table style="text-align: center;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;font-size: 6px;" >
			به:
						</td>
						<td style="direction:ltr;text-align:left;font-size: 6px;" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>$city1</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="8" rowspan="3">
				$barcode
				<?php
				?>
			</td>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			کد گروه:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style="text-align: center;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;font-size: 6px;" >
			به:
						</td>
						<td style="direction:ltr;text-align:left;font-size: 6px;" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>$city2</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="text-align: center;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;font-size: 5px;" >
			طرز پرداخت:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;font-size: 5px;" >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<b>$this->payment</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				$this->fare
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="5">
			<table style="text-align: center;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right;font-size: 5px;" >
		رفرنس
					</td>
					<td style="direction:ltr;text-align:left;font-size: 5px;" >
		Serial
					</td>
				</tr>
				<tr>
					<td width="50%">
					$this->refrenceText
					</td>
					<td>
					$this->serialText
                                        </td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		</table>
et;
			return($out);
		}
	}
?>
