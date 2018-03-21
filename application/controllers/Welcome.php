<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){
		//$this->load->database();
		/*select buyplanno, nextinvestdate   from  sale_app_buyplaninfo  where buyplanno = '090005000026' 	and ((firstinvestdate = '20171121' ) or (nextinvestdate <= '20171121' ) ) */
		/*var_dump($this->db->where(array('buyplanno' => '090005000026'))->get('sale_app_buyplaninfo')->row_array());*/
		 $this->load->view('home.html');
	}

	public function do_upload(){
        $config['upload_path']      = 'D:/';
        $config['allowed_types']    = 'gif|jpg|png|txt';
        

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('File'))
        {
            $error = array('error' => $this->upload->display_errors());

            echo json_encode(array('error' => $this->upload->display_errors()));
        }
        else
        {
            //$data = array('upload_data' => $this->upload->data());

            echo json_encode(array('k'=>2222));
        }
    }

    public function data_list(){
    	$this->load->database();
    	echo json_encode($this->db->where("status",0)->get('ttt')->result_array());
    }

    public function delete(){
    	$post = $this->input->post('id'); 
    	$this->load->database();
    	//$this->db->where('id',$post)->update('ttt',array('status' => 1));
    	$this->db->trans_start();
    	foreach ($post as $key => $value) {
    		$this->db->where('id',$value)->update('ttt',array('status' => 1));
    	}
    	echo json_encode($this->db->trans_complete());
    }

    function exportExcel(){  
		$this->load->model(array("Model_excelOper") );
	    $data['exportFieldDes'][0] = array('1,1' => '数据源', '1,2' => '用户ID','1,3' => '金证客户号','1,4' => '基金账号', '1,5' => '客户姓名', '1,6'=>'手机号','1,7' => '赎回份额',
				'1,8' => '赎回时间','1,9' => '状态',  '1,10'=> '赎回确认时间', '1,11' => '基金公司','1,12' => '基金类型', '1,13' => '基金名称',
				'1,14' => '确认净值','1,15' => '实际手续费','1,16' => '理财经理','1,17' => '理财经理工号','1,18' => '区域','1,19' => '城市','1,20' => '强赎标志','1,21' => '强赎原因'
		);
		$data['exportDataDes'][0] = array( 
			'row'=>2,
			'fieldKey' => array(
				1=>'platform', 2=>'XN_account',3=>'CUSTNO',4=>'TAACCOUNTID', 5=>'DEPOSITACCTNAME',6=>'CARDTELNO',7=>'APPLICATIONVOL',8=>'OPERDATE',9=>'STATUS',10=>'TRANSACTIONCFMDATE',11=>'ORGNAME',12=>'FUNDTYPE',13=>'FUNDNAME',14=>'NAV',15=>'CHARGE',16=>'FName',17=>'Fid',18=>'AREA',19=>'CITY',20=>'MRFLAG',21=>'MRREASON'),
			'fieldFormat'=> array(2=>'TEXT')
		);
		$db_content = array();
	
		$this->Model_excelOper->writeExcelContent('赎回明细信息查询',$db_content,$data['exportFieldDes'],$data['exportDataDes']); 
	} 

	function importExecl(){  
		$file = "./123.xlsx";//iconv("utf-8", "gb2312", "./123.xlsx");   //转码  
	    if(empty($file) OR !file_exists($file)) {  
	        die('file not exists!');  
	    } 
	    $this->load->model(array("Model_excelOper") );

		$sheet = 0;
	    $objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象  
	    if(!$objRead->canRead($file)){  
	        $objRead = new PHPExcel_Reader_Excel5();  
	        if(!$objRead->canRead($file)){  
	            die('No Excel!');  
	        }  
	    }  
	  
	    $obj = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($file))->setReadDataOnly(true)->load($file);  //建立excel对象  
	    $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表  
	    $columnCnt = PHPExcel_Cell::columnIndexFromString($currSheet->getHighestColumn());  
	    $rowCnt = $currSheet->getHighestRow();   //获取总行数  
	  
	    $data = array();  
	    for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容  
	        for($_column=0; $_column<=$columnCnt; $_column++){  
	            $cellValue = $currSheet->getCellByColumnAndRow($_column,$_row)->getValue();  
	             //$cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值  
	            if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串  
	                $cellValue = $cellValue->__toString();  
	            }  
	  
	            $data[$_row][$_column] = $cellValue;  
	        }  
	    } 

		$reader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($file))->setReadDataOnly(true)->load($file); 
	  	echo $reader->getSheetCount();

	    echo json_encode($data);  
    	
    	//$importData = $this->Model_excelOper->getExcelContent($_FILES['upload']['tmp_name'],$importDataDes)['content'];
	}   
}
