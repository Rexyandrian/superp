<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function addItem()
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if(($key != "new_id")&&($key!='new_tarikh'))
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$fields['app']=conf::app;
		$fields['content']="<span style=\'color:#000000;background-color:#4DFF29\'>".$fields['content']."</span>";
		$fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
                        $fi.="`$field`,";
                        $valu .="'$value',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
                $query="insert into `admin` $fi values $valu";
                mysql_class::ex_sqlx($query);

	}
        $grid = new jshowGrid_new("admin","grid1");
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "متن پیام";
        $grid->columnHeaders[2] = null;
        $grid->columnHeaders[3] = null;
	$grid->addFunction="addItem";
        $grid->canEdit= FALSE;
	$grid->intial();
        $grid->executeQuery();
        $out = $grid->getGrid();
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
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>

                <script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
                <script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                </title>
        </head>
        <body>
                <div align="center">
                        <br/>
                        <br/>
                        <?php echo $out;  ?>
                </div>
        </body>
</html>

