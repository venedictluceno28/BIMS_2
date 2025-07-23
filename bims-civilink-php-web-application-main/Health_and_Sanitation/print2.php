<?php
include 'db.php';
require_once __DIR__ . '/vendor/autoload.php'; // ✅ Composer autoloader

function fetch_data8($db) {
    $output = '';
    $sql = "SELECT * 
            FROM inventory_drugs_release 
            INNER JOIN resident_detail ON inventory_drugs_release.res_ID = resident_detail.res_ID 
            INNER JOIN inventory_drugs ON inventory_drugs_release.drug_ID = inventory_drugs.drug_ID 
            ORDER BY inventory_drugs_release.drgrelease_ID";
    $result = mysqli_query($db, $sql);
    
    while ($row = mysqli_fetch_array($result)) {
        $output .= '<tr>
            <td>' . htmlspecialchars($row["drug_Name"]) . '</td>
            <td>' . htmlspecialchars($row["drgrelease_Qnty"]) . '</td>
            <td>' . htmlspecialchars($row["res_fName"]) . '</td>
            <td>' . htmlspecialchars($row["res_mName"]) . '</td>
            <td>' . htmlspecialchars($row["res_lName"]) . '</td>
            <td>' . htmlspecialchars($row["drgrelease_Date_Record"]) . '</td>
        </tr>';
    }

    return $output;
}

// Initialize TCPDF
$obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$obj_pdf->SetTitle("Drug Distribution List");
$obj_pdf->SetHeaderData('', 0, '', '');
$obj_pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$obj_pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
$obj_pdf->SetAutoPageBreak(TRUE, 10);
$obj_pdf->SetFont('helvetica', '', 12);
$obj_pdf->AddPage();

// Content
$content = '
<br><br><br>
<div align="center">
     Republic of the Philippines<br>
     Province of Cavite<br>
     City of General Trias<br>
     Barangay Pasong Camachile 2
</div>
<br><br><h3 align="center">List of Drug Distribution</h3><br>
<table border="1" cellspacing="0" cellpadding="3">
    <tr>
        <th width="15%">Drug</th>
        <th width="10%">Quantity</th>
        <th width="18%">First Name</th>
        <th width="18%">Middle Name</th>
        <th width="18%">Last Name</th>
        <th width="21%">Date Given</th>
    </tr>';

$content .= fetch_data8($db);
$content .= '</table><br><br><br>
Prepared By:<br>
Signature: ___________________________<br>
Name:<br>
Position:';

// Output PDF
$obj_pdf->writeHTML($content);
$obj_pdf->Output('Drug_Distribution_List.pdf', 'I');
