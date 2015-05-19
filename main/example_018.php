<?php
include_once ('../kernel.php');
require_once('../config/lang/eng.php');
require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('farzaneh habibi');
$pdf->SetTitle('بلیط الکترونیکی');
$pdf->SetSubject('بلیط الکترونیکی');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 018', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$tmpmargin = "10px";
$pdf->SetAutoPageBreak(TRUE, $tmpmargin);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language dependent data:
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';

//set some language-dependent strings
$pdf->setLanguageArray($lg);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '',5);
//echo $htmlpersian;
// add a page
$pdf->AddPage();

// ---------------------------------------------------------

//Close and output PDF document
//echo $htmlpersian;
$id = "2";
	$shomare = "527045";
	//if(isset($_REQUEST['shomare']) && isset($_REQUEST['id']) )	
	if(isset($shomare) && isset($id) )	
	{
		//mysql_class::ex_sql('select `id` from `ticket` where `shomare`='.(int)$_REQUEST["shomare"],$qs);
		mysql_class::ex_sql('select `id` from `ticket` where `shomare`='.(int)$shomare,$qs);
		if(mysql_num_rows($qs)==1)
		{
			$r = mysql_fetch_array($qs);
			$ticket = new ticket_class((int)$r['id']);
			$customer = new customer_class($ticket->customer_id);
			$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
			$parvaz2 = null;
		}
		if(mysql_num_rows($qs)>1)
		{

			//$id = (int)$_REQUEST['id'];
			$ticket = new ticket_class($id);
			//echo (int)ticket_class::loadBargasht(23);
			$ticket2 = new ticket_class((int)ticket_class::loadBargasht($id));
			$customer = new customer_class($ticket->customer_id);
			$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
			$parvaz2 = new parvaz_det_class($ticket2->parvaz_det_id);
		}	
	}
if(isset($_REQUEST['ticket_regtime']))
{
	$ticket_regtime =$_REQUEST['ticket_regtime'];
}
else
{
	$ticket_regtime = "";
}
if(isset($_REQUEST['en_masir']))
{
	$en_masir =$_REQUEST['en_masir'];
}
else
{
	$en_masir = "";
}
if(isset($_REQUEST['fa_masir']))
{
	$fa_masir =$_REQUEST['fa_masir'];
}
else
{
	$fa_masir = "";
}
if(isset($_REQUEST['customer_name']))
{
	$customer_name =$_REQUEST['customer_name'];
}
else
{
	$customer_name = "";
}
if(isset($_REQUEST['ticket_user']))
{
	$ticket_user =$_REQUEST['ticket_user'];
}
else
{
	$ticket_user = "";
}
if(isset($_REQUEST['ticket_fname']))
{
	$ticket_fname =$_REQUEST['ticket_fname'];
}
else
{
	$ticket_fname = "";
}
if(isset($_REQUEST['ticket_lname']))
{
	$ticket_lname =$_REQUEST['ticket_lname'];
}
else
{
	$ticket_lname = "";
}
if(isset($_REQUEST['parvaz_saat']))
{
	$parvaz_saat =$_REQUEST['parvaz_saat'];
}
else
{
	$parvaz_saat ="";
}
if(isset($_REQUEST['parvaz_fatarikh']))
{
	$parvaz_fatarikh =$_REQUEST['parvaz_fatarikh'];
}
else
{
	$parvaz_fatarikh = "";
}
if(isset($_REQUEST['parvaz_entarikh']))
{
	$parvaz_entarikh =$_REQUEST['parvaz_entarikh'];
}
else
{
	$parvaz_entarikh = "";
}
if(isset($_REQUEST['parvaz_shomare']))
{
	$parvaz_shomare =$_REQUEST['parvaz_shomare'];
}
else
{
	$parvaz_shomare = "";
}
if(isset($_REQUEST['parvaz_sherkat']))
{
	$parvaz_sherkat =$_REQUEST['parvaz_sherkat'];
}
else
{
	$parvaz_sherkat = "";
}
if(isset($_REQUEST['parvaz_mabda']))
{
	$parvaz_mabda =$_REQUEST['parvaz_mabda'];
}
else
{
	$parvaz_mabda = "";
}
if(isset($_REQUEST['parvaz_maghsad']))
{
	$parvaz_maghsad =$_REQUEST['parvaz_maghsad'];
}
else
{
	$parvaz_maghsad = "";
}
if(isset($_REQUEST['ticket_pic']))
{
	$ticket_pic =$_REQUEST['ticket_pic'];
}
else
{
	$ticket_pic = "";
}
if(isset($_REQUEST['ticket_rahgiri']))
{
	$ticket_rahgiri ="123456";
}
else
{
	$ticket_rahgiri ="";
}
if(isset($_REQUEST['bar_mojaz']))
{
	$bar_mojaz = $_REQUEST['bar_mojaz'];
}
else
{
	$bar_mojaz ="";
}
if(isset($_REQUEST['bar_mojaz2']))
{
	$bar_mojaz2=$_REQUEST['bar_mojaz2'];
}
else
{
	$bar_mojaz2= "";
}
if(isset($_REQUEST['faghed_etebar_ghablaz']))
{
	$faghed_etebar_ghablaz=$_REQUEST['faghed_etebar_ghablaz'];
}
else
{
	$faghed_etebar_ghablaz="";
}
if(isset($_REQUEST['faghed_etebar_ghablaz2']))
{
	$faghed_etebar_ghablaz2=$_REQUEST['faghed_etebar_ghablaz2'];
}
else
{
	$faghed_etebar_ghablaz2="";
}
if(isset($_REQUEST['faghed_etebar_badaz']))
{
	$faghed_etebar_badaz=$_REQUEST['faghed_etebar_badaz'];
}
else
{
	$faghed_etebar_badaz="";
}
if(isset($_REQUEST['faghed_etebar_badaz2']))
{
	$faghed_etebar_badaz2=$_REQUEST['faghed_etebar_badaz2'];
}
else
{
	$faghed_etebar_badaz2="";	
}
if(isset($_REQUEST['mabna_nerkh']))
{
	$mabna_nerkh=$_REQUEST['mabna_nerkh'];
}
else
{
	$mabna_nerkh="";
}
if(isset($_REQUEST['mabna_nerkh2']))
{
	$mabna_nerkh2=$_REQUEST['mabna_nerkh2'];
}
else
{
	$mabna_nerkh2="";
}
if(isset($_REQUEST['$vazeeat']))
{
	$vazeeat=$_REQUEST['$vazeeat'];
}
else
{
	$vazeeat="";
}
if(isset($_REQUEST['vazeeat2']))
{
	$vazeeat2=$_REQUEST['vazeeat2'];
}
else
{
	$vazeeat2="";
}
if(isset($_REQUEST['parvaz_saat2']))
{
	$parvaz_saat2=$_REQUEST['parvaz_saat2'];
}
else
{
	$parvaz_saat2="";
}
if(isset($_REQUEST['parvaz_fatarikh2']))
{
	$parvaz_fatarikh2=$_REQUEST['parvaz_fatarikh2'];
}
else
{
	$parvaz_fatarikh2="";
}
if(isset($_REQUEST['parvaz_entarikh2']))
{
	$parvaz_entarikh2=$_REQUEST['parvaz_entarikh2'];
}
else
{
	$parvaz_entarikh2="";
}
if(isset($_REQUEST['parvaz_shomare2']))
{
	$parvaz_shomare2=$_REQUEST['parvaz_shomare2'];
}
else
{
	$parvaz_shomare2="";
}
if(isset($_REQUEST['void']))
{
	$void=$_REQUEST['void'];
}
else
{
	$void="";
}
if(isset($_REQUEST['cash']))
{
	$cash=$_REQUEST['cash'];
}
else
{
	$cash="";
}
if(isset($_REQUEST['totalfare']))
{
	$totalfare=$_REQUEST['totalfare'];
}
else
{
	$totalfare="";
}
if($parvaz2==null)
{
	$en_masir="masir 1";
	$fa_masir="مسیر ۱";
	$faghed_etebar_badaz2="";
	$ticket = addTicket_print_class::add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2,$faghed_etebar_ghablaz,$faghed_etebar_ghablaz2,$faghed_etebar_badaz,$faghed_etebar_badaz2,$mabna_nerkh,$mabna_nerkh2,$vazeeat,$vazeeat2,$parvaz_saat2,$parvaz_fatarikh2,$parvaz_entarikh2,$parvaz_shomare2,$void,$cash,$totalfare);
	$ticket .= "<br/>";
	$en_masir="masir 2";
	$fa_masir="مسیر ۲";
	$ticket .= addTicket_print_class::add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2,$faghed_etebar_ghablaz,$faghed_etebar_ghablaz2,$faghed_etebar_badaz,$faghed_etebar_badaz2,$mabna_nerkh,$mabna_nerkh2,$vazeeat,$vazeeat2,$parvaz_saat2,$parvaz_fatarikh2,$parvaz_entarikh2,$parvaz_shomare2,$void,$cash,$totalfare);
	//echo $ticket_masir2;
	$ticket .= "<br/>";
	$en_masir="PASSENGER COUPON";
	$fa_masir="کوپن مسافر";
	$ticket .= addTicket_print_class::add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2,$faghed_etebar_ghablaz,$faghed_etebar_ghablaz2,$faghed_etebar_badaz,$faghed_etebar_badaz2,$mabna_nerkh,$mabna_nerkh2,$vazeeat,$vazeeat2,$parvaz_saat2,$parvaz_fatarikh2,$parvaz_entarikh2,$parvaz_shomare2,$void,$cash,$totalfare);
			//echo $ticket_mosafer;
}
else
{
	$en_masir="masir 1";
	$fa_masir="مسیر ۱";
	$faghed_etebar_badaz2="";
	$ticket = addTicket_print_class::add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2,$faghed_etebar_ghablaz,$faghed_etebar_ghablaz2,$faghed_etebar_badaz,$faghed_etebar_badaz2,$mabna_nerkh,$mabna_nerkh2,$vazeeat,$vazeeat2,$parvaz_saat2,$parvaz_fatarikh2,$parvaz_entarikh2,$parvaz_shomare2,$void,$cash,$totalfare);
				//echo $ticket_masir1;
	$ticket .= "<br/>";
	$en_masir="PASSENGER COUPON";
	$fa_masir="کوپن مسافر";
	$ticket .= addTicket_print_class::add($ticket_regtime,$customer_name,$ticket_user,$ticket_fname,$ticket_lname,$parvaz_saat,$parvaz_fatarikh,$parvaz_entarikh,$parvaz_shomare,$parvaz_sherkat,$parvaz_mabda,$parvaz_maghsad,$ticket_pic,$ticket_rahgiri,$en_masir,$fa_masir,$bar_mojaz,$bar_mojaz2,$faghed_etebar_ghablaz,$faghed_etebar_ghablaz2,$faghed_etebar_badaz,$faghed_etebar_badaz2,$mabna_nerkh,$mabna_nerkh2,$vazeeat,$vazeeat2,$parvaz_saat2,$parvaz_fatarikh2,$parvaz_entarikh2,$parvaz_shomare2,$void,$cash,$totalfare);
}

$pdf->WriteHTML($ticket, true, 0, true, 0);
$pdf->Output('example_018.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
