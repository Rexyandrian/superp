<?php
	class user_class
	{
		public $id=-1;
		public $customer_id=-1;
		public $fname="";
		public $lname="";
		public $user="";
		public $pass="";
		public $group_id=-1;
		public $typ=-1;
		public $session_id="";
                public $online_date = '0000-00-00 00:00:00';
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `user` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->customer_id=$r['customer_id'];
				$this->fname=$r['fname'];
				$this->lname=$r['lname'];
				$this->user=$r['user'];
				$this->pass=$r['pass'];
				$this->group_id=$r['group_id'];
				$this->typ=$r['typ'];
				$this->session_id=$r['session_id'];
				$this->online_date = $r['online_date'];
			}
		}
                public function refresh()
                {
                        $today = date("Y-m-d H:i:s");
                        mysql_class::ex_sqlx("update `user` set `online_date`='$today' where `id`='".$this->id."'");
                }
		public function isOnline()
		{
			if($this->session_id != null)
				return(TRUE);
			else
				return(FALSE);
		}
                public function sabt_vorood()
                {
                        $today = date("Y-m-d H:i:s");
                        if (!empty($_SERVER['HTTP_CLIENT_IP']))
                        {
                                $ip=$_SERVER['HTTP_CLIENT_IP'];
                        }
                        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                        {
                                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                        }
                        else
                        {
                                $ip=$_SERVER['REMOTE_ADDR'];
                        }
                        //$ip = ip2long($ip);
                        mysql_class::ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','1')");
                        mysql_class::ex_sqlx("update `user` set `online_date`='$today' where `id`=".$this->id);
                }

                public function sabt_khorooj()
                {
                        $today = date("Y-m-d H:i:s");
                        if (!empty($_SERVER['HTTP_CLIENT_IP']))
                        {
                                $ip=$_SERVER['HTTP_CLIENT_IP'];
                        }
                        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                        {
                                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                        }
                        else
                        {
                                $ip=$_SERVER['REMOTE_ADDR'];
                        }
                        //$ip = ip2long($ip);
                        mysql_class::ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','-1')");
                        $this->logout();
                }
                public function logout()
                {
                        //Modfied by M.Mirsamie from '00:00:00' to '0000-00-00 00:00:00'.
                        $temp_date = "0000-00-00 00:00:00";
                        mysql_class::ex_sqlx("update `user` set `online_date`='$temp_date' where `id`='".$this->id."'");
//                      session_destroy();
                }
                public function killUser($id_user)
                {
                        $out = TRUE;
                        $today = date("Y-m-d H:i:s");
                        $online_date = strtotime($this->online_date.' + 15 minute');
                        if(strtotime(date("Y-m-d H:i:s")) >= $online_date)
                        {
                                $this->logout();
                                session_destroy();
                                $out = FALSE;
                        }
                        return $out;
                }
		public function setOnline($sid)
		{
			mysql_class::ex_sqlx("update `user` set `session_id` = '$sid' where `id` = ".$this->id);
		}
		public function getOnlines()
		{
			$out = array();
			mysql_class::ex_sql("select `id` from `user` where not(`session_id` is null)",$q);
			while($r = mysql_fetch_array($q))
			{
				$out[] = (int)$r['id'];
			}
			return($out);
		}
	}
?>
