<?php
/**
 * Помощник для экспорта в Excel
 * Файл: src/Helpers/ExcelHelper.php
 */

namespace Helpers;

class ExcelHelper {
    
    /**
     * Экспорт массива данных в Excel (.xlsx формат)
     */
    public static function export(array $data, string $filename = 'export.xlsx') {
        // Заголовки для Excel 2007+
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");
        
        // Создаём Excel XML
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        echo 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        echo 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        echo 'xmlns:html="http://www.w3.org/TR/REC-html40">';
        
        // Стили
        echo '<Styles>';
        echo '<Style ss:ID="header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#E94560" ss:Pattern="Solid"/></Style>';
        echo '<Style ss:ID="number"><NumberFormat ss:Format="0"/></Style>';
        echo '</Styles>';
        
        echo '<Worksheet ss:Name="Sheet1">';
        echo '<Table>';
        
        // Заголовки столбцов
        if (!empty($data['headers'])) {
            echo '<Row ss:StyleID="header">';
            foreach ($data['headers'] as $header) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header, ENT_XML1) . '</Data></Cell>';
            }
            echo '</Row>';
        }
        
        // Данные
        if (!empty($data['rows'])) {
            foreach ($data['rows'] as $row) {
                echo '<Row>';
                foreach ($row as $cell) {
                    if (is_numeric($cell)) {
                        echo '<Cell ss:StyleID="number"><Data ss:Type="Number">' . htmlspecialchars($cell) . '</Data></Cell>';
                    } else {
                        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell, ENT_XML1) . '</Data></Cell>';
                    }
                }
                echo '</Row>';
            }
        }
        
        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        
        exit;
    }
    
    /**
     * Экспорт в CSV (с разделением по ячейкам)
     */
    public static function exportCsv(array $data, string $filename = 'export.csv') {
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        $output = fopen('php://output', 'w');
        
        // Добавляем BOM для корректного отображения кириллицы
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Заголовки
        if (!empty($data['headers'])) {
            fputcsv($output, $data['headers'], ';');  // Точка с запятой как разделитель
        }
        
        // Данные
        if (!empty($data['rows'])) {
            foreach ($data['rows'] as $row) {
                fputcsv($output, $row, ';');  // Точка с запятой как разделитель
            }
        }
        
        fclose($output);
        exit;
    }
}
?>