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
        function loadCust()
        {
                $out = '<select id="customer_id" ><option value="-1" >همه</option>'."\n";
                $mysql = new mysql_class;
                $mysql->ex_sql("select `id`,`name` from `customers` order by `name` ",$q);
                foreach($q as $r)
                        $out.='<option value="'.$r['id'].'" >'.$r['name'].'</option>'."\n";
                $out .='</select>';
                return $out;
        }
        function loadCustomerName($id)
        {
                $id = (int)$id;
                $cu = new customer_class($id);
                return($cu->name);
        }
        function loadUserName($id)
        {
                $id = (int)$id;
                $cu = new user_class($id);
                return($cu->fname." ".$cu->lname);
        }
        function loadPDate($dt)
        {
                if($dt != '0000-00-00 00:00:00')
                        $out = jdate("d / m / Y",strtotime($dt));
                else
                        $out = '';
                return($out);
        }
        function loadTozihat($sanad_record_id)
        {
                $out = '----';
		$mysql = new mysql_class;
                $mysql->ex_sql("select * from `ticket` where `sanad_record_id` = $sanad_record_id ",$q);
                foreach($q as $i => $r)
                {
                        $customer_id = (int)$r["customer_id"];
                        $par = new parvaz_det_class($r['parvaz_det_id']);
			if($i == 0)
	                        $out = '<div class=".lessBlock" style="cursor:pointer;" onclick="toggleMore(this);">مسافر: <b>'.$r['fname'].' '.$r['lname'].'</b> پرواز: <b>'.enToPerNums($par->shomare).'</b> تاریخ: <b>'.loadPDate($par->tarikh).'</b></div><div class=".moreBlock" style="display:none;">';
			else
				$out .= $r['fname'].' '.$r['lname']."<br/>\n";
                }
		if($out != '----')
			$out .= "</div>";
                return($out);
        }
	function loadAdult($id)
	{
		$out = '----';
		if($id == 0)
			$out = 'بزرگسال';
		else if($id == 1)
			$out = 'کودک';
		else if($id == 2)
			$out = 'نوزاد';
		return($out);
	}
	function loadParvaz($pid)
	{
		$p = new parvaz_det_class((int)$pid);
		return($p->shomare.'<br/>('.(loadPDate($p->tarikh)).' | '.$p->saat.')');
	}
	function loadGender($g)
	{
		$out = '----';
		if((int)$g == 0)
			$out = 'زن';
		else if((int)$g == 1)
			$out = 'مرد';
		return($out);
	}
	function ticketJam($where='')
	{
		$out = array("sum_mablagh"=>0,"sum_tour_mablagh"=>0,"sum_poorsant_kol"=>0,"sum_poorsant_mablagh"=>0);
		$mysql = new mysql_class;
		$mysql->ex_sql("select sum(`mablagh`) as `sm` , sum(`tour_mablagh`) as `st`,sum(`poorsant`*(`mablagh`+`tour_mablagh`)/100) as `pm1` ,sum(`poorsant`*(`mablagh`)/100) as `pm2` from `ticket` ".(($where != '')?" where $where":''),$q);
		if(isset($q[0]))
			$out = array("sum_mablagh"=>(int)$q[0]['sm'],"sum_tour_mablagh"=>(int)$q[0]['st'],"sum_poorsant_kol"=>(int)$q[0]['pm1'],"sum_poorsant_mablagh"=>(int)$q[0]['pm2']);
		return($out);
		
	}
	function pateBack($pdat)
	{
		$out = audit_class::hamed_pdateBack($pdat);
		$tmp = explode(' ',$out);
		return($tmp[0]);
	}
	function moni($inp)
	{
		$inp = (int)$inp;
		$out = ($inp < 0)?'(':'';
		$out .= monize(abs($inp));
		$out .= ($inp < 0)?')':'';
		return($out);
	}
	function loadTicket($id)
	{
            $ti = new ticket_class($id);
            $out = '<a class="msg"  target="_blank" href="../pdf/'.($ti->refer.  str_replace('/','', $ti->seldate)).'.pdf" >چاپ</a>';
            return($out);
        }
        function loadParvaz_det($inp)
        {
            $out='---';
            if($inp!='')
            {
                $parvaz_det = unserialize($inp);
                $ag = new agency_class($parvaz_det->customer_id);
                $out ='<span style="font-size:80%" >'. 'شماره:'.$parvaz_det->flnum.' تاریخ:'.jdate("Y/m/d",strtotime($parvaz_det->tarikh)).' آژانس:'.$ag->name.' مسیر:'.$parvaz_det->strsource.' -> '.$parvaz_det->strdest.'</span>';
            }
            return($out);
        }
	$customer_id = (isset($_REQUEST['customer_id']))?(int)$_REQUEST['customer_id']:-1;
	$tday = perToEnNums(jdate("Y/m/d"));
	$tdayG = date("Y-m-d");
	$aztarikh = (isset($_REQUEST['aztarikh']))?pateBack($_REQUEST['aztarikh']):$tdayG;
	$tatarikh = (isset($_REQUEST['tatarikh']))?pateBack($_REQUEST['tatarikh']):$tdayG;
	if(isset($_REQUEST['jam']) && $_REQUEST['jam'] == 'true')
	{
		$su = ticketJam("`en`=1 and (date(`regtime`) >= '$aztarikh' and date(`regtime`) <= '$tatarikh') ".(($customer_id>0)?" and `customer_id` = $customer_id ":''));
		die("جمع مبلغ بلیت : ".monize($su['sum_mablagh'])." جمع مبلغ تور : ".monize($su['sum_tour_mablagh']).'<br/>'.'جمع کمیسیون از کل بلیت : '.monize($su['sum_poorsant_kol']).' جمع کمیسیون از مبلغ بلیت : '.monize($su['sum_poorsant_mablagh']));
	}
        $gname = "gname_ticket_gozaresh";
        $input =array($gname=>array('table'=>'ticket','div'=>'main_div_ticket_gozaresh'));
        $xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "`en`=1 and (date(`regtime`) >= '$aztarikh' and date(`regtime`) <= '$tatarikh') ".(($customer_id>0)?" and `customer_id` = $customer_id ":'');
        //echo "`en`=1 and (date(`regtime`) >= '$aztarikh' and date(`regtime`) <= '$tatarikh') ".(($customer_id>0)?" and `customer_id` = $customer_id ":'');
	$xgrid->column[$gname][0]['name'] = '';
	$xgrid->column[$gname][1]['name'] = 'نام';
	$xgrid->column[$gname][1]['sort'] = 'true';
	$xgrid->column[$gname][2]['name'] = 'نام‌خانوادگی';
	$xgrid->column[$gname][2]['sort'] = 'true';
	$xgrid->column[$gname][3]['name'] = 'تلفن همراه';
	$xgrid->column[$gname][4]['name'] = 'بزرگسال';
	$xgrid->column[$gname][4]['sort'] = 'true';
	$xgrid->column[$gname][4]['cfunction'] = array('loadAdult');
	//$xgrid->column[$gname][5]['name'] = 'شماره سند';
        $xgrid->column[$gname][5]['name'] = '';
	$xgrid->column[$gname][5]['sort'] = 'true';
	$xgrid->column[$gname][6]['name'] = '';
	//$xgrid->column[$gname][6]['cfunction'] = array('loadParvaz');
	$xgrid->column[$gname][6]['sort'] = 'true';
	$xgrid->column[$gname][7]['name'] = 'مشتری';
	$xgrid->column[$gname][7]['cfunction'] = array('loadCustomerName');
	$xgrid->column[$gname][8]['name'] = 'کاربر';
	$xgrid->column[$gname][8]['cfunction'] = array('loadUserName');
	$xgrid->column[$gname][9]['name'] = 'شماره';
	$xgrid->column[$gname][9]['sort'] = 'true';
        $xgrid->column[$gname][10]['name'] = '';
        $xgrid->column[$gname][11]['name'] = '';
        $xgrid->column[$gname][12]['name'] = 'زمان ثبت';
	$xgrid->column[$gname][12]['sort'] = 'true';
	$xgrid->column[$gname][12]['cfunction'] = array('loadPDate');
        $xgrid->column[$gname][13]['name'] = 'مبلغ';
	$xgrid->column[$gname][13]['cfunction'] = array('moni');
	//$xgrid->column[$gname][14]['name'] = 'کمیسیون';
        $xgrid->column[$gname][14]['name'] = '';
        $xgrid->column[$gname][15]['name'] = 'جنسیت';
	$xgrid->column[$gname][15]['cfunction'] = array('loadGender');
        //$xgrid->column[$gname][16]['name'] = 'مبلغ تور';
        $xgrid->column[$gname][16]['name'] = '';
	$xgrid->column[$gname][16]['cfunction'] = array('moni');
	$xgrid->column[$gname][17] = $xgrid->column[$gname][0];
	$xgrid->column[$gname][17]['name'] = 'چاپ';
	$xgrid->column[$gname][17]['cfunction'] =array('loadticket');
        $xgrid->column[$gname][18]['name'] = 'ایمیل';
        $xgrid->column[$gname][19]['name'] = '';
        $xgrid->column[$gname][20]['name'] = 'کدملی';
        $xgrid->column[$gname][21]['name'] = '';
        $xgrid->column[$gname][22]['name'] = '';
        $xgrid->column[$gname][23]['name'] = '';
        $xgrid->column[$gname][24]['name'] = '';
        $xgrid->column[$gname][25]['name'] = 'پرواز';
        $xgrid->column[$gname][25]['cfunction'] =array('loadParvaz_det');
        $out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
	function calcJam()
	{
		$("#jam").html('');
		$("#jam").load("sales_admin_ticket.php?customer_id="+$("#customer_id").val()+"&aztarikh="+$("#aztarikh").val()+"&tatarikh="+$("#tatarikh").val()+"&jam=true&r="+Math.random()+"&");
	}
        $(document).ready(function(){
                $("#sbit").click(function(){
			calcJam();
                        gArgs['gname_ticket_gozaresh']['eRequest']={'customer_id': $("#customer_id").val(),'aztarikh':$("#aztarikh").val(),'tatarikh':$("#tatarikh").val()};
                        grid['gname_ticket_gozaresh'].init(gArgs['gname_ticket_gozaresh']);
                });
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
</script>
	از : <input type="text" class="dateValue" id="aztarikh" name="aztarikh" value="<?php echo $tday; ?>"/>
	تا : <input type="text" class="dateValue" id="tatarikh" name="tatarikh" value="<?php echo $tday; ?>"/>
<?php
	echo "مشتری : ".loadCust();
?>
	<button id="sbit">انتخاب</button>
<div id="main_div_ticket_gozaresh"></div>
<div id="jam">
<?php
	$su = ticketJam($xgrid->whereClause[$gname]); 
	echo("جمع مبلغ بلیت : ".monize($su['sum_mablagh'])." جمع مبلغ تور : ".monize($su['sum_tour_mablagh']).'<br/>'.'جمع کمیسیون از کل بلیت : '.monize($su['sum_poorsant_kol']).' جمع کمیسیون از مبلغ بلیت : '.monize($su['sum_poorsant_mablagh']));
?>
</div>

