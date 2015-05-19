<?php
	class access_class
	{
		public $id=-1;
		public $group_id=-1;
		public $page_name="";
		public $is_group = TRUE;
		public function __construct($id=-1)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `access` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id=$r['id'];
				$this->group_id=$r['group_id'];
				$this->page_name=$r['page_name'];
				$this->is_group = ((int)$r['is_group']==0)?FALSE:TRUE;
			}
		}
		public function loadByGroup($grp_id)
		{
			$out = array();
			$grp_id = (int)$grp_id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select `id`,`page_name` from `access` where `group_id` = $grp_id and `is_group` = 1",$q);
			foreach($q as $r)
				$out[(int)$r['id']] = $r['page_name'];
			return($out);
		}
		public function loadByUser($user_id)
		{
			$out = array();
			$user_id = (int)$user_id;
			$mysql = new mysql_class;
                        $mysql->ex_sql("select `id`,`page_name` from `access` where `group_id` = $user_id and `is_group` = 0",$q);
                        foreach($q as $r)
                                $out[(int)$r['id']] = $r['page_name'];
                        return($out);
		}
	}
?>
