<?php
	class havapeima_class
	{
		public function __construct($id = -1)
		{
			$id = (int)$id;
			if($id>0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from havapeima where id = '$id'",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id = $id;
					$this->name = $r["name"];
				}
			}
		}
		public function loadByName($name='')
		{
			if(trim($name)!='')
			{
				$name = trim($name);
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from havapeima where name='$name'",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id = $q[0]['id'];
					$this->name = $q[0]["name"];
				}
				else
				{
					$ln = $mysql->ex_sqlx("insert into havapeima (name) values ('$name') ",FALSE);
					$this->id = $mysql->insert_id($ln);
					$this->name = $name;
					$mysql->close($ln);
				}
			}
		}
	}
?>
