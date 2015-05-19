<?php
	include_once("../kernel.php");
	$grid = new jshowGrid_new("ticket","grid");
//	$grid->loadQueryField=true;
//	$grid->query="select fname from user";
/*        $grid->columnHeaders[0] = null;
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
        $grid->columnHeaders[14] = null;*/
        $grid->intial();
        $grid->executeQuery();
        $outgrid = $grid->getGrid();
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
				echo $outgrid;
			?>
			<br/>
		</div>
	</body>
</html>
