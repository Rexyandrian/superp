<?php
	session_start();
	include_once("../kernel.php");
	if ( !( isset($_SESSION[conf::app.'_user_id']) && isset($_SESSION[conf::app.'_typ']) && isset($_REQUEST['sanad_record_id']) )   )
	{
		die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	}
//////////////////////////////////////
require_once('../config/lang/eng.php');
require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('farzaneh habib');
$pdf->SetTitle('بلیط الکترونیکی');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();
/////////////////////////////////////
$blit_text = "";
$first_tmpmatn = array();
$last_tmpmatn = array();
	$tmp = $_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
	$tmp = substr($tmp,0,-15);
	$buffer =array();
	$sanad_record_id =(int) $_REQUEST['sanad_record_id'];
	mysql_class::ex_sql("select `id`,`shomare` from `ticket` where `sanad_record_id`='$sanad_record_id' and `en`=1 group by `shomare` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$curl_handle=curl_init();
			curl_setopt($curl_handle,CURLOPT_URL,"http://$tmp/eticket.php?shomare=".$r['shomare']."&id=".$r['id']);
			curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
			curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
			$buffer[]= curl_exec($curl_handle);
			curl_close($curl_handle);
		}

		foreach($buffer as $key=>$value)
		{
			$blit_text .= $value;
		}
	$first_tmpmatn = explode("<div",$blit_text);
$tmp = $first_tmpmatn[1];
	$last_tmpmatn = explode("</body>",$tmp);
$html = $last_tmpmatn[0];

$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('بلیط الکترونیکی', 'I');
?>

