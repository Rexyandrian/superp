<?php
	include_once("../kernel.php");
        session_start();
        $GLOBALS["jam_bed"] = 0;
        $GLOBALS["jam_bes"] = 0;
        $GLOBALS["jam_man"] = 0;
/*        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }
*/
	if (isset($_REQUEST['moshtari']))
        {
                $moshtari=$_REQUEST['moshtari'];
        }
        else
        {
                $moshtari=-1;
        }
        if (isset($_REQUEST['parvaz_det_id']))
        {
                $parvaz_id=$_REQUEST['parvaz_det_id'];
        }
        else
        {
                $parvaz_id="";
        }
	$combo="<form name='frmmoshtari' id='frmmoshtari'>";
	$combo .= "<select class=\"inp1\" name=\"moshtari\" id=\"moshtari\" onchange=\"document.getElementById('frmmoshtari').submit();\">";
	$combo .= "<option value=\"-1\">همه</option>";
	mysql_class::ex_sql("select distinct(`customer_id`) from `ticket` where `parvaz_det_id`='$parvaz_id'",$q);
	while ($r=mysql_fetch_array($q))
        {
		$coustomer=$r['customer_id'];
		mysql_class::ex_sql("select * from `customers` where `id`='$coustomer'",$qq);
		while ($row=mysql_fetch_array($qq))
		{
			$id=$row['id'];
			$name=$row['name'];
			if ($row['id']==$moshtari)
			{
				$sel="selected='selected'";
			}
			else
			{
				$sel="";
			}
			$combo .= "<option value=\"$id\" $sel>$name</option>";
		}
	}
	$combo .= "</select>";
	$combo .= "<input type=\"hidden\" name=\"parvaz_det_id\" value=\"$parvaz_id\"/>";
	$combo .= "</form>";
	function loadCustomers($cust)
        {
                $cust = (int)$cust;
                $out = null;
		if ($cust!=-1)
		{
	                mysql_class::ex_sql("select * from `customers` where `id`='$cust' order by `name`",$q);
	                while($r = mysql_fetch_array($q))
        	        {
                	        $out[$r["name"]] = (int)$r["id"];
	                }
		}
		if ($cust==-1)
		{
			mysql_class::ex_sql("select * from `customers`",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $out[$r["name"]] = (int)$r["id"];
                        }
		}
                return($out);
        }
	mysql_class::ex_sql("select sum(`mablagh`) as `jam` from `ticket` where `parvaz_det_id`='$parvaz_id' and `en`=1 ",$qq);
	$jamkol=0;
	if ($r = mysql_fetch_array($qq))
	{
		$jamkol=$r['jam'];
	}
	$grid = new jshowGrid_new("ticket","grid1");
	$grid->whereClause = "order by `customer_id`";
	if ($moshtari!=-1)
	{
		$grid->whereClause = "`customer_id`='$moshtari' and `parvaz_det_id`='$parvaz_id'  and `en`=1";
	}
	if ($moshtari==-1)
        {
                $grid->whereClause = "`en`=1 and `parvaz_det_id`='$parvaz_id' order by `customer_id`";
        }

//      $grid->addFeild("id");
//      $grid->addFeild("id");
//      $grid->addFeild("id");
//      $grid->fieldList[5] = "id";
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
        $grid->columnHeaders[2] = null;
        $grid->columnHeaders[3] = null;
        $grid->columnHeaders[4] = null;
        $grid->columnHeaders[5] = null;
        $grid->columnHeaders[6] = null;
        $grid->columnHeaders[7] = "فروشنده";
        $grid->columnHeaders[8] = null;
        $grid->columnHeaders[9] = null;
        $grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
        $grid->columnHeaders[12] = null;
        $grid->columnHeaders[13] = "مبلغ";
        $grid->columnHeaders[14] = null;
        $grid->columnLists[7] = loadCustomers($moshtari);
/*                        $grid->columnFunctions[5] = "loadTozihat";
                        $grid->columnFunctions[8] = "loadBed";
                        $grid->columnFunctions[9] = "loadBes";
                        $grid->columnFunctions[10] = "loadMande";
                        $grid->columnFunctions[4] = "hamed_pdate";
                        $grid->columnCallBackFunctions[4] = "hamed_pdateBack";
                        $grid->columnAccesses[1] = 0;
                        $grid->whereClause = " `typ`<>0 and `tarikh` >= '$saztarikh 00:00:00' and `tarikh` <='$statarikh 23:59:59'";
*/
        $grid->footer="<td class=\"showgrid_row_odd\"></td><td class=\"showgrid_row_odd\" align='left' ></td><td class=\"showgrid_row_odd\"  id='jam_mablagh' align='center'>جمع:</td><td class=\"showgrid_row_odd\"  id='jam_m' align='center'  ></td><td id=\"jam_p\" align=\"center\" class=\"showgrid_row_odd\"></td>";
        $grid->canAdd = FALSE;
        $grid->canEdit = FALSE;
        $grid->canDelete = FALSE;
        $grid->intial();
        $grid->executeQuery();
        $outgrid = $grid->getGrid();
 //       $customer = new customer_class($customer_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <!-- Style Includes -->
                <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

                <link type="text/css" href="../css/style.css" rel="stylesheet" />

<!-- JavaScript Includes -->
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <link rel="stylesheet" type="text/css" media="all" href="../css/skins/aqua/theme.css" title="Aqua" />
                <style type="text/css">
                .calendar {
                        direction: rtl;
                }

                #flat_calendar_1, #flat_calendar_2{
                        width: 200px;
                }
                .example {
                        padding: 10px;
                }

                .display_area {
                        background-color: #FFFF88
                }
                </style>
                <script type="text/javascript" src="../js/tavanir.js"></script>
                <script type="text/javascript" src="../js/jalali.js"></script>
                <script type="text/javascript" src="../js/calendar.js"></script>
                <script type="text/javascript" src="../js/calendar-setup.js"></script>
                <script type="text/javascript" src="../js/lang/calendar-fa.js"></script>
                <title>
              سامانه مدیریت آژانس مسافرتی 
                </title>
        </head>
        <body>
                <div align="center">
                        <br/>
			<?php
				echo $combo;
				echo "<br/>";
				echo $outgrid;
			?>
			<br/>
			<h2> جمع کل: <?php echo monize($jamkol);?></h2>
			<script type="text/javascript">
				var jam=parseInt(document.getElementById('sum_mablagh').innerHTML,10);
				var sump=document.getElementById('sum_poorsant').innerHTML;
				document.getElementById('jam_m').innerHTML=FixNums(monize2(jam));
			//	document.getElementById('jam_p').innerHTML=sump;
			</script>
		</div>
	</body>
</html>
