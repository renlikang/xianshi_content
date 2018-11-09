<?php

namespace common\services;

use common\lib\Helper;

class ExcelService
{
    /**@var \PHPExcel */
    public $excel = null;
    public $writer = null;
    public $current_line_num = 1;

    public function __construct()
    {
        ini_set('memory_limit', '500M');
        $this->excel = new \PHPExcel();
        $this->writer = new \PHPExcel_Writer_Excel2007($this->excel);
    }

    public function sheet($index = 0, $name = 'Simple')
    {
        $this->excel->setActiveSheetIndex($index);
        $this->excel->getActiveSheet()->setTitle($name);
    }

    public function setHeader($header)
    {
        foreach ($header as $k => $v)
        {
            $this->excel->getActiveSheet()->setCellValue($this->toAlpha($k) . $this->current_line_num, $v);
            $this->excel->getActiveSheet()->getColumnDimension($this->toAlpha($k))->setAutoSize(true);
        }
        $this->current_line_num++;
    }

    public function toAlpha($num)
    {
        $alphabet = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z'
        ];
        array_flip($alphabet);
        if ($num <= 25)
        {
            return $alphabet[$num];
        }
        elseif ($num > 25)
        {
            $dividend = ($num + 1);
            $alpha = '';
            while ($dividend > 0)
            {
                $modulo = ($dividend - 1) % 26;
                $alpha = $alphabet[$modulo] . $alpha;
                $dividend = floor((($dividend - $modulo) / 26));
            }
            return $alpha;
        }
    }

    /**
     * 绘制一行
     * @param $row
     */
    public function setRow($row)
    {
        foreach ($row as $k => $orgCeil)
        {
            $ceil = $this->toAlpha($k) . $this->current_line_num;
            $column = $this->toAlpha($k);
            $this->excel->getActiveSheet()->setCellValue($ceil, $orgCeil['value']);
            if (!empty($orgCeil['width'])) //列宽度，通常表头设置，每行只需要设置一次
            {
                $this->excel->getActiveSheet()->getColumnDimension($column)->setWidth($orgCeil['width']);
            }
            if (!empty($orgCeil['height'])) //行高度
            {
                $this->excel->getActiveSheet()->getRowDimension($this->current_line_num)->setRowHeight($orgCeil['height']);
            }
            else //不设置，根据内容自动调整
            {
                $this->excel->getActiveSheet()->getRowDimension($this->current_line_num)->setRowHeight(-1);
            }
            if (!empty($orgCeil['style'])) //单元格样式
            {
                $this->excel->getActiveSheet()->getStyle($ceil)->applyFromArray(
                    $orgCeil['style']
                );
            }
        }
        $this->current_line_num++;
    }

    public function setOneLine($one_line)
    {
        foreach ($one_line as $k => $v)
        {
            $this->excel->getActiveSheet()->setCellValue($this->toAlpha($k) . $this->current_line_num, $v);
        }
        $this->current_line_num++;
    }

    public function export($name)
    {
        $name = Helper::getIEName($name);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename=' . $name . '.xlsx');
        header("Content-Transfer-Encoding:binary");
        $this->writer->save('php://output');
    }

    /**
     * @param $inputFileName
     * @param int $from,从第几行开始读
     * @param int $workSheet
     * @return array
     * @throws \yii\base\Exception
     */
    public function read($inputFileName, $from = 1, $workSheet = 0)
    {
        $rowData = [];
        try
        {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
            $sheet = $objPHPExcel->getSheet($workSheet);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for ($row = 1; $row <= $highestRow; $row++)
            {
                if($row < $from)
                {
                    continue;
                }
                $rowDatas = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                $rowData[] = $rowDatas[0];
            }

            return $rowData;
        }
        catch (\Exception $e)
        {
            throw new \yii\base\Exception($e->getCode());
        }


    }
}