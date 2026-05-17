<?php
namespace Helpers;

class ExcelHelper
{
    public static function export(array $data, string $filename): void
    {
        // Убираем .xlsx или .xls, добавляем .xls
        $filename = preg_replace('/\.xlsx?$/i', '', $filename) . '.xls';
        
        // Заголовки для Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<?mso-application progid="Excel.Sheet"?>' . PHP_EOL;
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . PHP_EOL;
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . PHP_EOL;
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . PHP_EOL;
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . PHP_EOL;
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . PHP_EOL;
        
        echo '<Worksheet ss:Name="Sheet1">' . PHP_EOL;
        echo '<Table>' . PHP_EOL;

        // Заголовки (жирным)
        echo '<Row>' . PHP_EOL;
        foreach ($data['headers'] as $header) {
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header, ENT_XML1, 'UTF-8') . '</Data></Cell>' . PHP_EOL;
        }
        echo '</Row>' . PHP_EOL;

        // Данные
        foreach ($data['rows'] as $row) {
            echo '<Row>' . PHP_EOL;
            foreach ($row as $cell) {
                $type = is_numeric($cell) ? 'Number' : 'String';
                echo '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8') . '</Data></Cell>' . PHP_EOL;
            }
            echo '</Row>' . PHP_EOL;
        }

        echo '</Table>' . PHP_EOL;
        echo '</Worksheet>' . PHP_EOL;
        echo '</Workbook>';
        
        exit;
    }
}