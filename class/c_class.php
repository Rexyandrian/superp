<?php
	class c_class{
		public $html = "";
		public $forbidden_colors = array('#CCFFCC');
		public function __construct($addr)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_REFERER,"$addr/Default.aspx");
			curl_setopt($ch, CURLOPT_URL, "$addr/Flsform.aspx?log=2948454");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$out = curl_exec($ch);
			curl_close($ch);
			$this->html = $out;
			//echo $out;
		}
		public function findFlights($add=0)
		{
			$out = array();
			$tmp = explode("id=\"GridView1\"",$this->html);
			if(count($tmp)==2)
			{
				$lines = explode("\n",$tmp[1]);
				$flight_color = '';
				for($i = 1;$i < count($lines);$i++)
				{
					if(trim($lines[$i])!='' && strpos($lines[$i],"<tr")!==FALSE)
					{
						$line_tmp = trim($lines[$i]);
						$line_tmp_x = explode('bgcolor="',$line_tmp);
						if(count($line_tmp_x)==2)
						{
							$line_tmp_xx = explode('"',$line_tmp_x[1]);
							$flight_color = $line_tmp_xx[0];
						}
					}
					if(trim($lines[$i])!='' && strpos($lines[$i],"<td")!==FALSE)
					{
						$line = explode("</font>",$lines[$i]);
						$out_small = array();
						foreach($line as $line_tmp)
							$out_small[]=strip_tags($line_tmp);
						//$out_small[4+$add]!='FULL' && && trim($out_small[1+$add])!='تور' && trim($out_small[1+$add])!='دومسیر' && trim($out_small[1+$add])!='دومسير' && trim($out_small[1+$add])!='تلفنی' 
						if((int)$out_small[4+$add]>0 && trim($out_small[1+$add])=='')
						{
							$out_little = array();
							$h = new havapeima_class;
							$h->loadByName($out_small[12+$add]);
							$out_little['havapeima'] = $h->id;
							$s = new shahr_class;
							//$out_little['class'] = $out_small[12];
							//$out_little['day'] = $out_small[11];
							$d = explode(' ',$out_small[8+$add]);
							$d = explode(' ',audit_class::hamed_pdateBack('13'.$d[0]));
							$out_little['date'] = $d[0];
							$out_little['saat'] = $out_small[7+$add];
							$s->loadByName($out_small[6+$add]);
							$out_little['mabda'] = $s->id;;
							$s->loadByName($out_small[5+$add]);
							$out_little['maghsad'] = $s->id;
							$out_little['tedad'] = ($out_small[4+$add]!='FULL')?$out_small[4+$add]:0;
							$out_little['ghimat'] = str_replace(',','',$out_small[3+$add]);
							$out_little['karmozd'] = trim(str_replace('%','',$out_small[2+$add]));
							$out_little['ghimat'] = ((100-$out_little['karmozd'])/100)*$out_little['ghimat'];
							$out_little['ghimat'] = 1000*(ceil((int)$out_little['ghimat']/1000));
							$out_little['color'] = $flight_color;
							$out_little['noe'] = $out_small[1+$add];
							$p = new parvaz_class;
							$p->shomare = $out_small[11+$add];
							$p->name = '';
							$p->mabda_id = $out_little['mabda'];
							$p->maghsad_id = $out_little['maghsad'];
							$p->havapiema_id = $out_little['havapeima'];
							$p->ghimat_def = $out_little['ghimat'];
							$p->zarfiat_def = $out_little['tedad'];
							$p->saat_def = $out_little['saat'];
							$p->poor_def = $out_small[2+$add];
							$p->typ_def = 0;
							$p->mablagh_kharid_def = 0;
							$p->customer_id_det = -1;
							$p->rang = '';
							$p->is_shenavar = 0;
							$p->ghimat_ticket=$out_little['ghimat'];
							$p->color = $flight_color;
							$p->noe = $out_little['noe'];
							$p->id = $p->add();
							$out_little['parvaz'] = $p->id;
							//$out_little['masir_mokhalef'] = $out_small[2];
							//$out_little['tarikh_mokhalef'] = $out_small[1];
							unset($out_little['mabda']);
							unset($out_little['maghsad']);
							unset($out_little['havapeima']);
							$out_little['tozihat'] = $out_small[0];
							//echo "proccessing ".$out_small[11+$add]."\n";
							//echo "Noe = '".$out_little['noe']."' # '".trim($out_little['noe'])."'\n";
							if($out_little['noe']!=trim($out_little['noe']))//!in_array(strtoupper($out_little['color']),$this->forbidden_colors))
							{
								//echo "Added\n";
								$out[] = $out_little;
							}
/*
							else
							{
								//var_dump($out_small);
								//echo "NotAdded\n";
							}
*/
						}
					}
				}
			}
			return($out);
		}
	}
?>
