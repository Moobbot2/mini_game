<?php

require 'vendor/autoload.php'; // Đường dẫn tới autoload.php của PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy giá trị từ form
    $email = $_POST["email"];

    // Lấy thời gian hiện tại
    $currentTime = date('Y-m-d H:i:s');

    // Kiểm tra xem tệp Excel đã tồn tại chưa, nếu chưa thì tạo mới
    $filePath = "emails_clock.xlsx";
    if (!file_exists($filePath)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('B1', 'Thời gian');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    // Ghi dữ liệu vào tệp Excel
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $lastRow = $sheet->getHighestRow() + 1;
    $sheet->setCellValue('A' . $lastRow, $email);
    $sheet->setCellValue('B' . $lastRow, $currentTime);

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    echo "Xác nhận thành công! Email và thời gian đã được lưu.";
} else {
    // Nếu không phải là phương thức POST, chuyển hướng về trang chính
    header("Location: clock.html");
    exit();
}
