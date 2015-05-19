<?php
	class sites_class
	{
		public function load()
		{
			$out = array();
			$my = new mysql_class;
			$my->ex_sql("select id,url_addr,add_ghimat,is_old from sites where en=1 ",$q);
			foreach($q as $r)
				$out[]= array('id'=>(int)$r['id'],'url'=>$r['url_addr'],'is_old'=>$r['is_old'],'ghimat'=>$r['add_ghimat']);
			return($out);
		}
		public function __construct($id = -1)
                {
                        $id = (int)$id;
                        if($id>0)
                        {
                                $mysql = new mysql_class;
                                $mysql->ex_sql("select * from sites where id = '$id'",$q);
                                if(isset($q[0]))
                                {
                                        $r = $q[0];
                                        $this->id = $id;
                                        $this->name = $r['name'];
					$this->url_addr = $r['url_addr'];
					$this->add_ghimat = $r['add_ghimat'];
					$this->en = $r['en'];
					$this->is_old = $r['is_old'];
                                }
                        }
                }
		public function loadByParvaz_det_id($parvaz_det_id)
		{
			$my = new mysql_class;
			$out = -1;
			$my->ex_sql("select sites_id from parvaz_det_sites where parvaz_det_id=$parvaz_det_id",$qp);
			foreach($qp as $r)
				$out = $r['sites_id'];
			return($out);
		}
	}
?>
