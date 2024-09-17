<?php
require 'vendor/autoload.php'; // โหลด mPDF

use Mpdf\Mpdf;

session_start();
include 'db_connect.php';

$sql = "SELECT * FROM leave_requests";
$result = $conn->query($sql);

$mpdf = new Mpdf();
$mpdf->WriteHTML('<h1>Leave Requests</h1>');

$html = '<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Approval Level 1</th>
            <th>Approval Level 2</th>
            <th>Approval Level 3</th>
            <th>Approval Level 4</th>
        </tr>
    </thead>
    <tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['id']) . '</td>
        <td>' . htmlspecialchars($row['username']) . '</td>
        <td>' . htmlspecialchars($row['start_date']) . '</td>
        <td>' . htmlspecialchars($row['end_date']) . '</td>
        <td>' . htmlspecialchars($row['reason']) . '</td>
        <td>' . htmlspecialchars($row['approval_level_1']) . '</td>
        <td>' . htmlspecialchars($row['approval_level_2']) . '</td>
        <td>' . htmlspecialchars($row['approval_level_3']) . '</td>
        <td>' . htmlspecialchars($row['approval_level_4']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';
$mpdf->WriteHTML($html);

// ส่งไฟล์ให้ดาวน์โหลด
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="leave_requests.pdf"');
header('Cache-Control: max-age=0');
$mpdf->Output('php://output', 'D');
exit();
?>
