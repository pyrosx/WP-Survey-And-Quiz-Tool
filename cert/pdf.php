<?php
	require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-load.php';
	require_once WPSQT_DIR.'lib/fpdf.php';

//	if (!$_POST || !isset($_POST['display_name']) || empty($_POST['display_name']) || !isset($_POST['completed_date']) || empty($_POST['completed_date'] )) {
//		// $_POST is necessary, don't want people making blank certificates!
//		echo("<h1>NOPES!</h1>");
//		return false;
//	}  
//
//
//	

	if ($_POST) {
	 	if (!isset($_POST['display_name']) || empty($_POST['display_name']) || !isset($_POST['completed_date']) || empty($_POST['completed_date'] )) {
    	// don't want people making blank certificates!
    	echo("<h1>NOPES!</h1>");
    	return false;
		} else {
			// looks good...
			$display_name = strtoupper($_POST['display_name']);
			$completed_date = date('l jS \of F Y',$_POST['completed_date']);
		}
	} else if ($_GET) {
		// better make sure we've come from wordpress admin.... 
		if (!current_user_can('wpsqt-manage')) {
    	echo("<h1>DOUBLE NOPES!</h1>");
    	return false;
		} else {
			// yay!
			$display_name = strtoupper($_GET['display_name']);
			$completed_date = date('l jS \of F Y',$_GET['completed_date']);
		}
	} else {
		// no data at all? wtf?
		exit();
	}
	

	$pdf = new FPDF("L","mm","A4");
	$pdf->SetMargins(0,0,0,0);
	$pdf->SetAutoPageBreak(true, 0);

	$pdf->AddPage();

	$pdf->Image('header.png',0,10);
	$pdf->Ln(70);

	$pdf->SetFont('Arial','B',32);
	$pdf->Cell(0,0,"Certificate of Completion",0,1,"C");
	$pdf->Ln(20);

	$pdf->SetFont('Arial','B',24);
	$pdf->Cell(0,0,"Awarded to",0,1,"C");
	$pdf->Ln(12);

	$pdf->SetTextColor(7,113,186);
	$pdf->SetFont('Arial','B',24);
	$pdf->Cell(0,0,$display_name,0,1,"C");
	$pdf->Ln(16);
	$pdf->SetTextColor(0);

	$pdf->SetFont('Arial','I',18);
	$pdf->Cell(0,0,"On ".$completed_date,0,1,"C");
	$pdf->Ln(16);
	$pdf->Cell(0,0,"In Recognition of completing the",0,1,"C");
	$pdf->Ln(12);
	$pdf->SetFont('Arial','B',18);
	$pdf->Cell(0,0,"Advanced Fresh Concepts Food Safety Course",0,1,"C");
	
	$pdf->Ln(12);


	$offset = 190;
	$pdf->Image('damienblakeneysig.png',$offset);
	$pdf->Ln(8);
	$pdf->SetFont('Arial','BU',12);
	$pdf->Cell($offset,0,"");
	$pdf->Cell(0,0,"Advanced Fresh Concepts International, Inc",0,2,"L");
	$pdf->Ln(6);
	$pdf->Cell($offset,0,"");
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(0,0,"Damien Blakeney",0,2,"L");
	$pdf->Ln(6);
	$pdf->Cell($offset,0,"");
	$pdf->Cell(0,0,"General Manager",0,2,"L");


	$pdf->Output();
	//$pdf->Output('Certificate - '.$display_name.'.pdf',"D");



/*
$pdf = new FPDF("L","mm","A5");
$pdf->SetMargins(0,0,0,0);
$pdf->SetAutoPageBreak(true, 0);
$pdf->AddPage();

$pdf->Image('header.png',20,3,-120);
$pdf->Ln(50);

$pdf->SetFont('Arial','BU',24);
$pdf->Cell(0,0,"Certificate of Completion",0,1,"C");
$pdf->Ln(16);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,0,"Awarded to:",0,1,"C");
$pdf->Ln(8);

$pdf->SetTextColor(7,113,186);
$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,0,$display_name,0,1,"C");
$pdf->Ln(12);
$pdf->SetTextColor(0);

$pdf->SetFont('Arial','I',14);
//$pdf->Cell(0,0,"On ".date('l jS \of F Y',$completed_date),0,1,"C");
$pdf->Cell(0,0,"On ".$completed_date,0,1,"C");
$pdf->Ln(12);
$pdf->Cell(0,0,"In Recognition of completing the",0,1,"C");
$pdf->Ln(8);
$pdf->SetFont('Arial','',16);
$pdf->Cell(0,0,"Sushi Izu Food Safety Course",0,1,"C");
$pdf->Ln(8);


$offset = 130;
$pdf->Image('damienblakeneysig.png',$offset);
$pdf->Ln(2);
$pdf->SetFont('Arial','BU',8);
$pdf->Cell($offset,0,"");
$pdf->Cell(0,0,"Advanced Fresh Concepts International, Inc",0,2,"L");
$pdf->Ln(4);
$pdf->Cell($offset,0,"");
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,0,"Damien Blakeney",0,2,"L");
$pdf->Ln(4);
$pdf->Cell($offset,0,"");
$pdf->Cell(0,0,"General Manager",0,2,"L");


$pdf->Output();
//$pdf->Output('Certificate - '.$display_name.'.pdf',"D");
*/
?>
