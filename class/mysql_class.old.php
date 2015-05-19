<?php

	class mysql_class

	{

		public $host = conf::host;

		public $db = conf::db;

		public $user =conf::user;

		public $pass = conf::pass;

		public function ex_sql($sql,&$q){

			$host = conf::host;

			$db =conf::db;

			$user = conf::user;

			$pass = conf::pass;

			$out = "ok";

			$q = NULL;

			$conn = mysql_pconnect($host,$user,$pass);

			if(!($conn==FALSE)){

				mysql_query("SET time_zone='".conf::date_off."'",$conn);

				if(!(mysql_select_db($db,$conn)==FALSE)){

					mysql_query("SET NAMES 'utf8'");

					$q = mysql_query($sql,$conn);		

				}else{

					$out = "Select DB Error.";

				}		

			}else{

				$out = "Connect MySql Error.";

			}

			return($out);

		}

		public function ex_sqlx($sql){

                        $host = conf::host;

                        $db =conf::db;

                        $user = conf::user;

                        $pass = conf::pass;

			$out = "ok";

			$q = NULL;

			$conn = mysql_pconnect($host,$user,$pass);

			if(!($conn==FALSE)){

				mysql_query("SET time_zone='".conf::date_off."'",$conn);

				if(!(mysql_select_db($db,$conn)==FALSE)){

					mysql_query("SET NAMES 'utf8'");

					mysql_query($sql,$conn);		

				}else{

					$out = "Select DB Error.";

				}		

			}else{

				$out = "Connect MySql Error.";

			}

			return($out);

		}



	}

?>