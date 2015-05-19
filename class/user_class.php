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
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `user` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$this->id=$q[0]['id'];
				$this->customer_id=$q[0]['customer_id'];
				$this->fname=$q[0]['fname'];
				$this->lname=$q[0]['lname'];
				$this->user=$q[0]['user'];
				$this->pass=$q[0]['pass'];
				$this->group_id=$q[0]['group_id'];
				$this->typ=$q[0]['typ'];
				$this->session_id=$q[0]['session_id'];
				$this->online_date = $q[0]['online_date'];
			}
		}
		public function loadByUser($user)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `user` where `user` = '$user'",$q);
			if(isset($q[0]))
			{
				$this->id=$q[0]['id'];
				$this->customer_id=$q[0]['customer_id'];
				$this->fname=$q[0]['fname'];
				$this->lname=$q[0]['lname'];
				$this->user=$q[0]['user'];
				$this->pass=$q[0]['pass'];
				$this->group_id=$q[0]['group_id'];
				$this->typ=$q[0]['typ'];
				$this->session_id=$q[0]['session_id'];
				$this->online_date = $q[0]['online_date'];
			}
		}
		public function is_authonticated($enc_pass,$user_name="")
		{
			ticket_class::clearTickets();
			$out = FALSE;
			$conf = new conf;
			$webSault = ($conf->webSault != '')?$conf->webSault:'_1';
			$fpass = encrypt_class::decrypt($enc_pass).$webSault;
			//echo "enc_pass = '$enc_pass',dec_pass = '$fpass'";
			$mysql = new mysql_class;
			//echo "select pass from `user` where `user` = '$user_name'";
			$mysql->ex_sql("select pass from `user` where `user` = '$user_name'",$q);
			if(isset($q[0]))
			{
				$pass = $q[0]['pass'];
				$out = (md5($fpass) == $pass);
			}
			return($out);
		}
                public function refresh()
                {
			$mysql = new mysql_class;
                        $today = date("Y-m-d H:i:s");
                        $mysql->ex_sqlx("update `user` set `online_date`='$today' where `id`='".$this->id."'");
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
			$mysql = new mysql_class;
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
                        $mysql->ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','1')");
                        $mysql->ex_sqlx("update `user` set `online_date`='$today' where `id`=".$this->id);
                }

                public function sabt_khorooj()
                {
			$mysql = new mysql_class;
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
                        $mysql->ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','-1')");
                        $this->logout();
                }
                public function logout()
                {
			$mysql = new mysql_class;
                        //Modfied by M.Mirsamie from '00:00:00' to '0000-00-00 00:00:00'.
                        $temp_date = "0000-00-00 00:00:00";
                        $mysql->ex_sqlx("update `user` set `online_date`='$temp_date' where `id`='".$this->id."'");
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
			$mysql = new mysql_class;
			$mysql->ex_sqlx("update `user` set `session_id` = '$sid' where `id` = ".$this->id);
		}
		public function getOnlines()
		{
			$mysql = new mysql_class;
			$out = array();
			$mysql->ex_sql("select `id` from `user` where not(`session_id` is null)",$q);
			foreach($q as $r)
			{
				$out[] = (int)$r['id'];
			}
			return($out);
		}
	}
?>
