<?php

namespace App\Services;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

//参考出处：https://blog.csdn.net/mercy_php/article/details/126642046

class ExcelService implements FromArray, WithStyles{

    public $data;
    public $sheetStyle;

    public function __construct (array $data = [], array $sheetStyle = []) {
        $this->data = empty($data) ? [] : $data;
        $this->sheetStyle = empty($sheetStyle) ? [] : $sheetStyle;
    }


    /**
     * @return array
     */
    public function array (): array {
        return empty($this->data) ? [['数据导出失败', '', '']] : $this->data;
    }

    /**
     * 设置格式
     * merge 坐标 [横标1,纵标1,横标2,纵标1] (e.g. [3, 5, 6, 8])
     * @param Worksheet $sheet
     * @throws Exception
     */
    public function styles (Worksheet $sheet): void {
        if (empty($this->sheetStyle) && empty($this->data)) {
            $sheet->mergeCellsByColumnAndRow(1, 1, 3, 1)
                ->getStyleByColumnAndRow(1, 1, 3, 1)
                ->getFont()
                ->setSize(15)
                ->getColor()
                ->setARGB(Color::COLOR_RED);
        }

        foreach ($this->sheetStyle as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'merge':
                    # 合并单元格
                    foreach ($value as $item) {
                        $sheet->mergeCellsByColumnAndRow($item[0], $item[1], $item[2], $item[3]);
                    }
                    break;
                case 'alignment':
                    # 设置文字位置 (Vertical 垂直, Horizontal 水平, WrapText 自动换行)
                    $sheet->getStyleByColumnAndRow(1, 1, count($this->data[0]), count($this->data))
                        ->getAlignment()
                        ->setVertical($item['vertical'] ?? Alignment::VERTICAL_CENTER)
                        ->setHorizontal($item['horizontal'] ?? Alignment::HORIZONTAL_CENTER)
                        ->setWrapText(true);
                    break;
                case 'title':
                    # 表头设置
                    $column = count($this->data[0]);
                    $sheet->mergeCellsByColumnAndRow(1, 1, $column, 1)
                        ->getStyleByColumnAndRow(1, 1, $column, 1)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setWrapText(true);
                    $sheet->getStyleByColumnAndRow(1, 1, $column, 1)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_DOUBLE);
                    break;
                case 'width':
                    # 单元格宽度设置
                    foreach ($value as $k => $item) {
                        $sheet->getColumnDimension($k)->setWidth($item);
                    }
                    break;
                case 'height':
                    # 单元格高度设置
                    foreach ($value as $k => $item) {
                        $sheet->getRowDimension($k + 1)->setRowHeight($item);
                    }
                    break;
                case 'font':
                    # 文字设置 (size 尺寸, color 文字颜色)
                    foreach ($value as $item) {
                        $sheet->getStyleByColumnAndRow($item['cells'][0], $item['cells'][1], $item['cells'][2], $item['cells'][3])
                            ->getFont()
                            ->setSize($item['size'] ?? 10)
                            ->getColor()
                            ->setARGB($item['color'] ?? Color::COLOR_BLACK);
                    }
                    break;
                case 'fill':
                    # 背景样式设置 (fill 背景样式, color 颜色)
                    foreach ($value as $item) {
                        $sheet->getStyleByColumnAndRow($item['cells'][0], $item['cells'][1], $item['cells'][2], $item['cells'][3])
                            ->getFill()
                            ->setFillType($item['type'] ?? Fill::FILL_NONE)
                            ->getStartColor()
                            ->setRGB($item['color'] ?? Color::COLOR_WHITE);
                    }
                    break;
                case 'borders':
                    # 边框样式设置
                    foreach ($value as $item) {
                        $sheet->getStyleByColumnAndRow($item['cells'][0], $item['cells'][1], $item['cells'][2], $item['cells'][3])
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle($item['style'] ?? Border::BORDER_NONE);
                    }
                    break;
                default:
                    break;
            }
        }
    }

}
