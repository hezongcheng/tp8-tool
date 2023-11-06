<?php

namespace app\admin\unit;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class OfficeSheet
{
    /**
     * @Desc:将单元格转换成数组
     * @param $file string 文件
     * @return array
     * @author: hzc
     * @Time: 2023/8/11 11:30
     */
    public static function cellsToArray($file)
    {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $dataArray = $worksheet->toArray();
        return $dataArray;
    }

    /**
     * @Desc:处理带有合并单元格转换成数组
     * @param $file string 文件
     * @return array
     * @author: hzc
     * @Time: 2023/8/11 11:36
     */
    public static function mergedCellsToArray($file)
    {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $dataArray = [];

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($row = 1; $row <= $highestRow; ++$row) {
            $rowData = [];

            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $mergedRange = $worksheet->getMergeCells();

                if (isset($mergedRange[$cell->getCoordinate()])) {
                    $mergedValue = $worksheet->rangeToArray($mergedRange[$cell->getCoordinate()])[0][0];
                    $rowData[$col] = $mergedValue;
                } else {
                    $rowData[$col] = $cell->getValue();
                }
            }

            $dataArray[] = $rowData;
        }

        return $dataArray;
    }

    /**
     * @Desc:导出数组到 Excel 文件
     * @param $data array 数据数组
     * @param $tableHeaders array 数据表头
     * @param $filename string 文件名称（不带后缀）
     * @return void
     * @author: hzc
     * @Time: 2023/8/11 11:31
     */
    public static function exportArrayToExcel($data,$tableHeaders, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();


        // 将表头数据写入第一行单元格
        foreach ($tableHeaders as $colIndex => $headerValue) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex + 1) . '1'; // 第一行为表头
            $worksheet->setCellValue($cellCoordinate, $headerValue);
        }

        // 数据从第二行开始
        $startRow = 2;

        // 将数据写入单元格
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $colIndex => $cellValue) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + $startRow);
                $worksheet->setCellValue($cellCoordinate, $cellValue);
            }
        }

        // 保存为 Excel 文件
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // 设置 HTTP 头部信息
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'.Xlsx"');
        $writer->save('php://output');
    }
}