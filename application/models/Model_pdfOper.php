<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Model_excelOper extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		require_once(FCPATH.'data/MPDF60/mpdf.php');
	}

	public function html_to_pdf($url,$path,$data = array()){
		if(empty($url)||empty($path))
			return false;

	  	$mpdf=new mPDF('utf-8','','','',0,0,16,16);
    	$mpdf->useAdobeCJK = true;
    	$mpdf->showWatermarkText = true;
		$mpdf->autoScriptToLang = true;  
		$mpdf->autoLangToFont = true;  
	    $mpdf->SetDisplayMode('fullpage');  
	    $strContent = file_get_contents($url);

	    foreach ($data as $key => $value) {
	    	$strContent = str_replace($key,$value,$strContent);
	    }

	    $mpdf->WriteHTML($strContent);
	    //$mpdf->Output(); //直接输出pdf内容
	    $mpdf->Output($path,'f');//保存成pdf文件 
	    return true;
	}

}