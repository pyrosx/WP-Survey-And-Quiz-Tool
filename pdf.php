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

require_once 'fpdf.php';

$display_name = strtoupper($_POST['display_name']);
$completed_date = $_POST['completed_date'];

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
$pdf->Cell(0,0,"On ".date('l jS \of F Y',strtotime($completed_date)),0,1,"C");
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
