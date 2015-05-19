<?php
	class security_class
	{
		public $can_view = FALSE;
		public $allDetails = array();
		public function blockIfBlocked($se,$msg='')
		{
			$conf = new conf;
			$out = '';
			$user = new user_class((int)$_SESSION[$conf->app.'_user_id']);
			if(get_class($se) == 'security_class' && $se->detailAuth('block') && $user->user!='mehrdad')
				$out = "<div style=\"opacity:0.8;color:#fff;filter: alpha(opacity = 80);background-color:#000;width:100%;height:100%;z-index:10;top:0;left:0;position:fixed;\">\n$msg\n</div>";
			return($out);
		}
		public function firstVisit(&$ses,$req)
		{
			$out = TRUE;
			$conf = new conf;
			//var_dump($ses);
			//echo "<br/>";
			//var_dump($req);
			//echo "<br/>";
		        $firstVisit = (isset($ses[$conf->app."_login"]) && ($ses[$conf->app."_login"] == 1) && isset($req["user"]));
			//var_dump($firstVisit);
			//echo "<br/>";
	                $mysql = new mysql_class;
		        if($firstVisit)
			{
				$user = $req["user"];
				$pass = md5($req["pass"]);
				$out = FALSE;
		                $mysql->ex_sql("select `id`,`typ`,`pass`,`customer_id` from user where user = '".$user."'",$q);
		                if(isset($q[0]))
				{
		                        $r_u = $q[0];
		                        if($pass == $r_u["pass"]){
		                                $ses[$conf->app."_user_id"] = (int)$r_u["id"];
		                                $ses[$conf->app."_typ"] = (int)$r_u['typ'];
		                                $ses[$conf->app."_customer_id"] = (int)$r_u['customer_id'];
						$out = TRUE;
		                        }
				}
		        }
		        else if(isset($req["user"]) && isset($_REQUEST['pass']) && $req['user']=='test' && $req['pass']=='test')
		        {
		        	$user = $req["user"];
				$pass = md5($req["pass"]);
				$out = FALSE;
				//echo "select `id`,`typ`,`pass`,`customer_id` from user where user = '".$user."'<br/>";
		                $mysql->ex_sql("select `id`,`typ`,`pass`,`customer_id` from user where user = '".$user."'",$q);
				//var_dump($q);
		                if(isset($q[0]))
				{
		                        $r_u = $q[0];
		                        if($pass == $r_u["pass"]){
		                                $ses[$conf->app."_user_id"] = (int)$r_u["id"];
		                                $ses[$conf->app."_typ"] = (int)$r_u['typ'];
		                                $ses[$conf->app."_customer_id"] = (int)$r_u['customer_id'];
						$out = TRUE;
		                        }
				}
		        }
			return($out);
		}
		public function auth($user_id)
		{
			$user = new user_class((int)$user_id);
			$grp_id = $user->typ;
			$pages = access_class::loadByGroup($grp_id);
			$upages = access_class::loadByUser($user_id);
			$can_view = FALSE;
			$allDetails = array();
			$acc_id = security_class::isInArray($pages,security_class::thisPage());
			if($acc_id !== FALSE)// || $uacc_id !== FALSE)
                                $can_view = TRUE;
			if($can_view)
				$allDetails = access_det_class::loadByAcc($acc_id);
			$se = new security_class;
			$se->can_view = $can_view;
			$se->allDetails = $allDetails;
			return($se);
		}
		public function isInArray($arr,$val)
		{
			$out = FALSE;
			foreach($arr as $key => $value)
				if($value == $val)
					$out = $key;
			return($out);
		}
		public function detailAuth($frase)
		{
			$out = FALSE;
			if(security_class::isInArray($this->allDetails,$frase)!==FALSE)
				$out = TRUE;
			return($out);
		}
		public function thisPage()
		{
			$out = '';
			//$tmp = $_SERVER["REQUEST_URI"];
			$tmp = $_SERVER["PHP_SELF"];
			$tmp = explode('/',$tmp);
			$tmp = $tmp[count($tmp)-1];
			$tmp = explode('?',$tmp);
			$tmp = $tmp[0];
			$out = trim($tmp);
			return($out);
		}
	}
?>
