<?php

	require_once ("../../includes/admin.init.php");
	require_once('../../TCPDF-master/examples/tcpdf_include.php');
	
	$orderID = (!empty($_REQUEST["orderID"])) ? $_REQUEST["orderID"] : '';
	
	$orderInfos = orderInfosPrint($orderID);
	$orderTable = orderTablePrint($orderID);
	$orderType = strtoupper(orderType($orderID));
	$orderRef = orderRef($orderID);
	
	$orderFile = "picverre_{$orderRef}";
	
	$html='
	<p style="text-align:center">
	<img src="https://assets.pic-verre.fr/img/logo_pv-800.png" alt="" width="100" height="100" border="0" /><br>
	<strong color="#2fac66">SAS PIC’VERRE</strong><br><span style="font-size: x-small;">49 rue Paul Louis Lande, 33000 Bordeaux<br>Tél. 07.67.75.02.12<br>www.pic-verre.fr<br>contact@pic-verre.fr</span></p>
	<br>
	<h3 color="#2fac66">'.$orderType.'</h3>'.$orderInfos.'<br>
	<h3 color="#2fac66">DETAIL DE LA COMMANDE</h3>'.$orderTable;
	
	if($orderType=="DEVIS"){
		$html.='<p>Très attachés à vous apporter le meilleur service, veuillez agréer l’expression de nos salutations distinguées.<br><br>Cordialement, l’équipe Pic’Verre.</p>';
	}


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'SAS Pic’Verre au capital de 1000€ Euros - Entreprise de l’Économie Sociale et Solidaire - Siret : 84841832300020 ', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
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

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// print a block of text using Write()
$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output("{$orderFile}.pdf", "I");	
?>
