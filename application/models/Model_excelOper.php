<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Model_excelOper extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		set_time_limit(1800);
		ini_set('memory_limit', '1024M');
		require_once( FCPATH.'data/PHPExcel-1.8/PHPExcel.php'); 
	}
	
	//获取excel表的内容
	public function importExecl($filename,$data){
		$reader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($filename))->setReadDataOnly(true)->load($filename);
		$sheetCount = $reader->getSheetCount();
		$ret = array();
		
		foreach ($data as $key => $val){
			if ($val['sheet'] < $sheetCount){
				$sheet = $reader->getSheet($val['sheet']);//获取excel中的某个表
				$number = $sheet->getHighestRow() + 1 - $val['row'];
				for ($i=0; $i < $number; $i++) {
					foreach ($val['fieldKey'] as $k=>$v){
						$ret[$key][$i][$v] = trim($sheet->getCellByColumnAndRow($k,$i+$val['row'])->getValue());
					}
				}
			}
		}
		return $ret;
	}
	
	//将数据写入excel文件
	public function exportExcel($filename,$data,$fieldDes,$dataDes){
		$workbook = new PHPExcel();
		$boxFormat = array(
				'font' => array(
						'name' => 'Arial',
						'size' => '10',
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap'       => false,
						'indent'     => 0,
				)
		);

		foreach ($fieldDes as $key => $val){
			$workbook->setActiveSheetIndex($key);
			$worksheet = $workbook->getActiveSheet();

			foreach ($val as $k => $v){
				$worksheet->setCellValueByColumnAndRow($k,1,$v);
				$worksheet->getStyleByColumnAndRow($k,1)->applyFromArray($boxFormat);
			}

			foreach ($data as $kk => $vv){
				foreach ($dataDes[$key] as $kkk => $vvv ) {
					if (isset($vv[$vvv])){
						$worksheet->setCellValueByColumnAndRow($kkk,$kk+2,$vv[$vvv]);
						$worksheet->getStyleByColumnAndRow($kkk,$kk+2)->applyFromArray( $boxFormat );
					}
				}
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		date_default_timezone_set("Asia/Shanghai");
		header('Content-Disposition: attachment;filename="'.$filename.'('.date('Y-m-d',time()).').xls"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($workbook, 'Excel5')->save('php://output');
		$this->clearSpreadsheetCache();
	}
}
