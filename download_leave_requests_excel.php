<?php
require 'vendor/autoload.php'; // โหลด PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();
include 'db_connect.php';

$sql = "SELECT * FROM leave_requests";
$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดชื่อหัวตาราง
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Username');
$sheet->setCellValue('C1', 'Start Date');
$sheet->setCellValue('D1', 'End Date');
$sheet->setCellValue('E1', 'Reason');
$sheet->setCellValue('F1', 'Approval Level 1');
$sheet->setCellValue('G1', 'Approval Level 2');
$sheet->setCellValue('H1', 'Approval Level 3');
$sheet->setCellValue('I1', 'Approval Level 4');

$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, $row['id']);
    $sheet->setCellValue('B' . $rowIndex, $row['username']);
    $sheet->setCellValue('C' . $rowIndex, $row['start_date']);
    $sheet->setCellValue('D' . $rowIndex, $row['end_date']);
    $sheet->setCellValue('E' . $rowIndex, $row['reason']);
    $sheet->setCellValue('F' . $rowIndex, $row['approval_level_1']);
    $sheet->setCellValue('G' . $rowIndex, $row['approval_level_2']);
    $sheet->setCellValue('H' . $rowIndex, $row['approval_level_3']);
    $sheet->setCellValue('I' . $rowIndex, $row['approval_level_4']);
    $rowIndex++;
}

$writer = new Xlsx($spreadsheet);

// ส่งไฟล์ให้ดาวน์โหลด
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="leave_requests.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
?>
