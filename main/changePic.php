<?php
        include_once ("../kernel.php");
        /*
	$SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
               */
	$target_path = "";
	$new_name = "";
	$out = "";
	$scr = '';
	$mysql = new mysql_class;
	$persenel_id=(isset($_REQUEST['persenel_id']))?(int)$_REQUEST['persenel_id']:-1;
                if(isset($_FILES['uploadedfile']) && ($persenel_id>0 || $persenel_id==-666) )
                 {
                        $tmp_target_path = "../img";
                        $ext = explode('.',basename( $_FILES['uploadedfile']['name']));
                        $ext = $ext[count($ext)-1];
                        if(strtolower($ext)=='jpg' || strtolower($ext)=='png' || $persenel_id==-666 ) 
                        {
                                $target_path =$tmp_target_path."/".basename( $_FILES['uploadedfile']['name']);
                                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
                                {
					
					$new_name = $target_path;
					//$query="UPDATE `persenel` SET `img` = '$target_path' WHERE `id` ='$persenel_id'";
               				//$mysql->ex_sqlx($query);
                                        $out = "فایل با موفقیت ذخیره گردید"; 
					//$scr = "<script>opener.refresh_page();window.close();</script>";
                                }
                                else
                                {
                                        $out =  "در ذخیره فایل مشکل پیش آمده است لطفا مجددا سعی نمایید .";
					echo $out;
                                }
                        }
             }

	
?>
<html>
        <head>
                <link rel="SHORTCUT ICON" href="img/icon.ico">
                <meta name="keywords" content="" />
                <meta name="description" content="" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="language" content="en" />
                <link href="../css/style2.css" rel="stylesheet" type="text/css" />
		<style>
			.cabinet
			{
				width: 79px;
				height: 22px;
				background: url(../img/btn-choose-file.gif) 0 0 no-repeat;
				display: block;
				overflow: hidden;
				cursor: pointer;
			}
			.file_1
			{
				position: relative;
				height: 100%;
				width: auto;
				opacity: 0;
				-moz-opacity: 0;
				filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
			</style>
			<script>
				function change_path(path)
				{
					document.getElementById("path_name").value = path;
				}
			</script>
        </head>
        <body style="width:100%;height:100%;">
		<br/>
		<br/>
	<?php
		if(isset($_FILES['uploadedfile']))
			$file_name = basename($_FILES['uploadedfile']['name']);
		else
			$file_name = "";
	?>
	<div class="main_th3" id="content" align="center">
			<form method="post" id="frm1"  enctype="multipart/form-data" >	
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
				<lable class="cabinet"> 
    					<input type="file" class="file" name="uploadedfile" id="uploadedfile" onchange="change_path(this.value);"/>
				</lable>
				<input type="text" id="path_name"/>
				<button >درج فایل</button>
			</form>
	</div>
	<script> 
			var mess = "<?php echo $out;?>";
			if (mess != "")
				alert(mess);
	</script>
	<?php echo $scr; ?>
        </body>
</html>
