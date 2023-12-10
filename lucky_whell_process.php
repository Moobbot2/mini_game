<?php

require 'vendor/autoload.php'; // Đường dẫn tới autoload.php của PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy giá trị từ form
    $email = $_POST["email"];
    $gif = $_POST["gif"];

    // Lấy thời gian hiện tại
    $currentTime = date('Y-m-d H:i:s');

    // Kiểm tra xem tệp Excel đã tồn tại chưa, nếu chưa thì tạo mới
    $filePath = "lucky_whell.xlsx";
    if (!file_exists($filePath)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Thời gian ấn');
        $sheet->setCellValue('D1', 'Phần quà');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    // Check if the email has already received a gift
    if (hasReceivedGift($filePath, $email)) {
        echo "Email has already received a gift.";
    } else {
        // Ghi dữ liệu vào tệp Excel
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $lastRow = $sheet->getHighestRow() + 1;
        $sheet->setCellValue('A' . $lastRow, $lastRow - 1); // Giảm 1 để bắt đầu từ 1
        $sheet->setCellValue('B' . $lastRow, $email);
        $sheet->setCellValue('C' . $lastRow, $currentTime);
        $sheet->setCellValue('D' . $lastRow, $gif);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        echo "Lưu kết quả thành công!";
    }
} else {
    // Nếu không phải là phương thức POST, trả về lỗi
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Phương thức không được phép.";
}

// Function to check if the email has received a gift
function hasReceivedGift($filePath, $email)
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();

    // Loop through rows to check if the email has already received a gift
    for ($row = 2; $row <= $highestRow; $row++) {
        $cellValue = $sheet->getCell('B' . $row)->getValue();
        if ($cellValue == $email) {
            return true; // Email has already received a gift
        }
    }

    return false; // Email has not received a gift
}
