<?php
require_once __DIR__ . '/vendor/autoload.php'; // Use Composer's autoload

include 'db.php';
session_start();

function fetch_data8($db) {
    $output = '';
    $sql = "SELECT * from inventory_drugs";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $output .= '<tr>
            <td>'.$row["drug_ID"].'</td>
            <td>'.$row["drug_Name"].'</td>
            <td>'.$row["drug_Qnty"].'</td>
            <td>'.$row["drug_Description"].'</td>
            <td>'.$row["drug_Expiration_Date"].'</td>
        </tr>';
    }
    return $output;
}

$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("Drug Inventory");
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage('L');
$content = '
<br><br>
<div align="center">
     Republic of the Philippines<br>
     Province of Cavite<br>
     City of General Trias<br>
     Barangay Pasong Camachile 2
</div>
<br><h2 align="center">List of Drugs</h2><br>
<table border="1" cellspacing="0" cellpadding="3">
<tr>
     <th width="10%">ID</th>
     <th width="20%">Name</th>
     <th width="20%">Quantity</th>
     <th width="33%">Description</th>
     <th width="17%">Expiration Date</th>
</tr>';

$content .= fetch_data8($db);
$content .= '</table><br><br>Prepared By:<br>Signature: ___________________________<br>Name:<br>Position:';


$pdf->writeHTML($content);
$pdf->Output('List of Drugs.pdf', 'I');
