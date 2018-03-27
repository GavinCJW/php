<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function index(){
		$this->load->view('interface.html');
	}

	public function list(){
		$this->load->database();
    	echo json_encode($this->db->get('automation')->result_array());
	}

	public function insert(){
		$post = $this->input->post();
		$this->load->database();
		echo json_encode($this->db->insert('automation', $post));
	}

	public function pdf(){
		require_once( FCPATH.'application/libraries/MPDF60/mpdf.php' );

	  	$mpdf=new mPDF('utf-8','','','',0,0,16,16);
    	$mpdf->useAdobeCJK = true;
    	$mpdf->showWatermarkText = true;
		$mpdf ->autoScriptToLang = true;  
		$mpdf ->autoLangToFont = true;  
	    $mpdf->SetDisplayMode('fullpage');  
	    $strContent = file_get_contents('http://10.10.17.75:9098/elenote.html');
	    $strContent = str_replace("#year","2017",$strContent);
	    $strContent = str_replace("#moon","03",$strContent);
	    $strContent = str_replace("#day","27",$strContent);
	    $strContent = str_replace("#username","乔峰",$strContent);
	    $strContent = str_replace("#id","XCFHJ20180327",$strContent);
	    //var_dump($strContent);
	    $mpdf->WriteHTML($strContent);
	    //$mpdf->Output(); //直接输出pdf内容
	    $mpdf->Output('/tmp.pdf',true);//保存成pdf文件 

	   /* require_once( FCPATH.'application/libraries/TCPDF-master/tcpdf.php' );
	    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);   
		   
		
		   
		//设置字体   
		$pdf->SetFont('stsongstdlight', '', 14);   
		   
		$pdf->AddPage();   
		   
		$str1 = file_get_contents(FCPATH.'application/views/A.html');
		   
		$pdf->WriteHTML($str1);   
		   
		//输出PDF   
		$pdf->Output();   */
	}

	public function sign(){

		$data = array(
			'partnerId'=>'XCF',
			'certInfo'=>array(
				'id'=>'441412345678901234',
				'name'=>'乔峰',
				'hash'=>sha1(file_get_contents(FCPATH.'123.pdf'))),
			'signFileInfos'=>array(
				array(
					'URL'=>'http://10.10.17.77:8080/123.pdf',
					'contractId'=>'XCFHJ20180327',
					'companySigns'=>array(
						array(
							'signId'=>'1',
							'position'=>'AUTO_ADD:0,-1,0,0,255,yifang)|(0,'
						),
					),
					'userSigns'=>array(
						array(
							'name'=>'乔峰',
							'position'=>'AUTO_ADD:0,-1,0,0,255,jiafang)|(0,'
						),
					)
				),
			)
		);
		$data = json_encode($data);
		$secretKey='utsG0W13kKPI0CYPofd8J8eYUlry2D2k';
		//$sign = md5('data='.$data.'&secretKey='.$secretKey);
		$url = 'http://10.17.2.131:8008/esignature-web/sign?signature='.md5('data='.$data.'&secretKey='.$secretKey);
		$header=array(  
			"Accept: application/json",  
			"Content-Type: application/json;charset=utf-8",  
		);  
		$result = json_decode($this->comm_curl($url,$data,$header),true);
		foreach ($result['signFiles'] as $key => $value) {
			echo $value;
			if(!$this->curlDownFile($value,$key.'.pdf'))
				echo false;
		}
		echo true;
		//echo json_encode($data);
	}

	function comm_curl($url, $arr=array() , $header=array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		if (!empty($arr)){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		}
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}


	function curlDownFile($url, $filename = '', $save_path = './') {
	    $file = file_get_contents($url);

	    // 保存文件到制定路径
	    file_put_contents($filename, $file);
	    return true;
	}


}