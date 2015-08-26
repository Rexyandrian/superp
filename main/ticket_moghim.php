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
        if(isset($_REQUEST['rwaitlog']))
        {
            $rwaitlog = $_REQUEST['rwaitlog'];
            $refer = $_REQUEST['refer'];
            $seldate = str_replace('/','',$_REQUEST['seldate']) ;
            $etick = moghim_class::printEticket($rwaitlog);
            if(isset($etick->printEticketResult))
            {
                file_put_contents("../pdf/$refer".$seldate.".pdf", fopen("http://".$conf->moghim_ip."/ereports/$refer".$seldate.".pdf", 'r'));
                $ou = "../pdf/$refer".$seldate.".pdf";
            }
            else
            {
                $ou = "err";
            }
            die($ou);
        }    
?>
<div>
    <input type="text" id="rwaitlog" placeholder="rwaitlog" >
    <input type="text" id="refer" placeholder="refer" >
    <input type="text" id="seldate" placeholder="seldate" >
    <a class="btn btn-success" onclick="getTicket()" >
        ارسال درخواست
    </a>
    <div id="khoon" ></div>
</div>
<script>
    function getTicket()
    {
        var ob = {
            "rwaitlog": $("#rwaitlog").val(),
            "refer": $("#refer").val(),
            "seldate": $("#seldate").val()
        };
        $("#khoon").html('<img src="../img/status_fb.gif" >');
        $.get("ticket_moghim.php",ob,function(res){
            console.log(res);
            $("#khoon").html("<a target='_blank' class='btn btn-danger' href='"+res+"' >دانلود</a>");
        });
    }
</script>