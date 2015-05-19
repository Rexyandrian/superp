<?php
	class menu_class
	{
		public function loadTopMenu($se_key=array())
		{
			$out = '<table  class="topMenu"><tr>';
			$sekey = '';
			if(is_array($se_key))
				$sekey = "'".implode("','",$se_key)."'";
			else
				$sekey = "'".(string)$se_key."'";
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `menu_table` where `se_key` in ($sekey) group by `link_address` order by `order` ",$q);
			foreach($q as $r)
				$out .= "<td id=\"".$r['html_id']."\" align=\"center\"><img title='".$r['name']."' width=\"63px\" id=\"".$r['link_address']."\" src=\"".$r['img']."\" /></td>";
			$out .= '</tr></table>';
			return($out);
		}
		public function loadSideMenu($se_key,$id_css,&$firstMenu,$position='right')
		{
			$pos= 2;
			if($position=='left')
				$pos = 1;
			$firstMenu = '';
			$b = TRUE;
			$out = '<table width="100%" id="'.$id_css.'" >';
			if(count($se_key)>0)
			{
				$se_key = implode("','",$se_key);
				$mysql = new mysql_class;
				$se_key = "'".$se_key."'";
				$mysql->ex_sql("select * from `menu_table` where `position`='$pos' and `se_key` in ($se_key) group by `link_address` order by `order` ",$q);
				foreach($q as $r)
				{
					if($b)
					{
						$firstMenu = $r['link_address'];
						$b = FALSE;
					}
					$out .=
						'<tr id="'.$r['html_id'].'">
							<td class="menu_td1" >
								<div style="height:52px;" >
									<img src="../img/'.$r['img'].'" class="menu_img" width="50px" height="50px" title="'.$r['name'].'" id="'.$r['name'].'" name="'.$r['link_address'].'">
								</div>
							</td>
						</tr>
	'."\n";
				}
			}
			$out.=($pos==1)?'<tr id="menu_logout">
				<td class="menu_td1" >
					<div>
						<img src="../img/exit.png" width="50" class="menu_img" width="50px" title="خروج" id="خروج" name="login.php" >
					</div>
				</td>
			</tr>':'';
			$out .='</table>';
			return $out;
		}
	}
?>
