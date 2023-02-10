<?php

	require_once ("../../includes/admin.init.php");
	require_once('../../TCPDF-master/examples/tcpdf_include.php');
	
	$resellerID = (!empty($_REQUEST["resellerID"])) ? $_REQUEST["resellerID"] : '';
	$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : '';

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {


}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Pic’Verre');
$pdf->SetTitle("{$orderFile}");
$pdf->SetSubject("{$orderType}");
$pdf->SetKeywords('');
$pdf->setPrintHeader(false);

// set header and footer fonts
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->setCellHeightRatio(0.9);

// set margins
$pdf->SetMargins(0, 0, 0);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

// set style for barcode
$style = array(
    'border' => 0,
    'vpadding' => 0,
    'hpadding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' =>1 // height of a single module in points
);


$xpos = 10;
$ypos = 20;
$i=0;


$deposits_rq="SELECT * FROM resell WHERE resellerID={$resellerID} AND dateCreation='{$date}' ";	
$deposits_rs=mysqli_query($connexion, $deposits_rq) or die(mysqli_error($connexion));
while($deposits=mysqli_fetch_array($deposits_rs)){
	
	$i++;

	$cpromo =  strtoupper($deposits['code']);
	$pdf->write2DBarcode('mon-compte.pic-verre.fr/inscription.php?s='.$cpromo, 'QRCODE,L', $xpos, $ypos , 30, 30, $style, 'N');
	$pdf->MultiCell(30, 20, '<font size ="-1"><strong>www.pic-verre.fr/<br>inscription</strong></font>', 0, 'C', false, 1, $xpos, $ypos-8, true, 0, true, true, 0, 'T', false);
	$pdf->MultiCell(30, 20, 'CODE PROMO<br><strong><font size ="+3">'.$cpromo.'</font></strong>', 0, 'C', false, 1, $xpos, $ypos+32, true, 0, true, true, 0, 'T', false);

	if(!($i%4)){
		$xpos = 10;
		$ypos += 55;
	}else{
		$xpos += 50;
	}
	
	if(!($i%20)){
		$pdf->AddPage();
		$xpos = 10;
		$ypos = 20;
		$i=0;
		
	}
	
	
}

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output("sticker.pdf", "I");

?>
