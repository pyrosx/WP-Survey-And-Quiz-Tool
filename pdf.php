<?php

	/**
	 * Handles the fetching and downloading of PDFs
	 * from DocRaptor. 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011 (c), all rights reserved.
	 * @license GPL v2
	 * @package WPSQT
	 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php';
require_once WPSQT_DIR.'lib/fpdf.php';

global $wpdb;

$display_name = $_POST['display_name'];
$completed_date = $_POST['completed_date'];

$pdf = new FPDF("L","mm","A5");
$pdf->AddPage();

$pdf->SetFont('Arial','BU',24);
$pdf->Ln(20);
$pdf->Cell(0,0,"Certificate of Completion",0,1,"C");
$pdf->Ln(20);

$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,0,"Awarded To",0,1,"C");
$pdf->Ln(10);

$pdf->Cell(0,0,$display_name,0,1,"C");
$pdf->Ln(20);

$pdf->SetFont('Arial','I',16);
$pdf->Cell(0,0,"On ".date('l jS \of F Y',$completed_date),0,1,"C");
$pdf->Ln(20);

$pdf->SetFont('Arial','',16);

$pdf->Cell(0,0,"In Recognition of completing the",0,1,"C");
$pdf->Ln(10);
$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,0,"Sushi Izu Food Safety Course",0,1,"C");
$pdf->Ln(10);

$pdf->Output();
//$pdf->Output('Certificate - '.$display_name.'.pdf',"D");
