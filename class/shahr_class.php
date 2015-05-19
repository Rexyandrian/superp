<?php
	class shahr_class
	{
		public function __construct($id = -1)
                {
                        $id = (int)$id;
                        if($id>0)
                        {
                                $mysql = new mysql_class;
                                $mysql->ex_sql("select * from shahr where id = '$id'",$q);
                                if(isset($q[0]))
                                {
                                        $r = $q[0];
                                        $this->id = $id;
                                        $this->name = $r["name"];
					$this->en_name = $r["en_name"];
                                }
                        }
                }
		public function loadByName($name)
		{
			if(trim($name)!='')
                        {
                                $name = trim($name);
                                $mysql = new mysql_class;
				$ln1 = $mysql->ex_sqlx("insert into shahr_tmp (name) values ('$name') ",FALSE);
				$tmp_id = $mysql->insert_id($ln1);
				$mysql->close($ln1);
				$mysql->ex_sql("select name from shahr_tmp where id = $tmp_id",$q1);
				if(isset($q1[0]))
					$name = $q1[0]['name'];
				$mysql->ex_sqlx("delete from shahr_tmp where id = $tmp_id");
                                $mysql->ex_sql("select * from shahr where name='$name'",$q);
                                if(isset($q[0]))
                                {
                                        $r = $q[0];
                                        $this->id = $q[0]['id'];
                                        $this->name = $q[0]["name"];
					$this->en_name = $q[0]["en_name"];
                                }
                                else
                                {
                                        $ln = $mysql->ex_sqlx("insert into shahr (name) values ('$name') ",FALSE);
                                        $this->id = $mysql->insert_id($ln);
                                        $this->name = $name;
                                        $mysql->close($ln);
                                }
                        }
		}
	}
?>
