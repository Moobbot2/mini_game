<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function createSpreadsheet($filePath)
{
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
}

function checkMethod()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Phương thức không được phép.";
        exit;
    }
}

function checkEmail($filePath, $email)
{
    if (hasReceivedGift($filePath, $email)) {
        return [
            'status' => false,
            'mess' => "Email đã nhận quà."
        ];
    }

    return [
        'status' => true,
        'mess' => "Email chưa nhận quà."
    ];
}


function hasReceivedGift($filePath, $email)
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();

    for ($row = 2; $row <= $highestRow; $row++) {
        $cellValue = $sheet->getCell('B' . $row)->getValue();
        if ($cellValue == $email) {
            return true;
        }
    }

    return false;
}


// Main code
$filePath = "lucky_whell.xlsx";
createSpreadsheet($filePath);
checkMethod();

$email = $_POST["email"];

$result = checkEmail($filePath, $email);
echo json_encode($result);
