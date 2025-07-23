<?php
include('db.php');
require_once __DIR__ . '/vendor/autoload.php'; // ✅ Use Composer autoload

function fetch_data8($db) {
    $output = '';
    $sql = "SELECT * 
            FROM resident_detail 
            INNER JOIN resident_vaccinated ON resident_detail.res_ID = resident_vaccinated.res_ID";
    $result = mysqli_query($db, $sql);

    while ($row = mysqli_fetch_array($result)) {
        $output .= '<tr>
            <td>' . htmlspecialchars($row["res_fName"]) . '</td>
            <td>' . htmlspecialchars($row["res_mName"]) . '</td>
            <td>' . htmlspecialchars($row["res_lName"]) . '</td>
            <td>' . htmlspecialchars($row["res_Height"]) . '</td>
            <td>' . htmlspecialchars($row["res_Weight"]) . '</td>
            <td>' . htmlspecialchars($row["vac_Name"]) . '</td>
            <td>' . htmlspecialchars($row["vac_Date"]) . '</td>
        </tr>';
    }

    return $output;
}

// Initialize TCPDF
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("Vaccination List");
$pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

// Content
$content = '
<br><br><br>
<div align="center">
     Republic of the Philippines<br>
     Province of Cavite<br>
     City of General Trias<br>
     Barangay Pasong Camachile 2
</div>
<br><br><h3 align="center">List of Vaccination</h3><br>
<table border="1" cellspacing="0" cellpadding="3">
    <tr>
        <th width="15%">First Name</th>
        <th width="15%">Middle Name</th>
        <th width="15%">Last Name</th>
        <th width="10%">Height (cm)</th>
        <th width="9%">Weight (kg)</th>
        <th width="20%">Name of Vaccine</th>
        <th width="16%">Date of Vaccination</th>
    </tr>';

$content .= fetch_data8($db);
$content .= '</table><br><br><br>
Prepared By:<br>
Signature: ___________________________<br>
Name:<br>
Position:';

$pdf->writeHTML($content);
$pdf->Output('Vaccination_List.pdf', 'I');
?>
