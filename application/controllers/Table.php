<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Table extends CI_Controller {

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
		 $this->load->view('table.html');
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
	    $data['exportFieldDes'][0] = array('NAME','DATE','PRICE',);
		$data['exportDataDes'][0] = array( 'name', 'date','price');
		$this->load->database();
		$db_content = $this->db->where("status",0)->get('ttt')->result_array();
	
		$this->Model_excelOper->exportExcel('TEST',$db_content,$data['exportFieldDes'],$data['exportDataDes']); 
		echo true;
	} 

	function importExecl(){  
		/*$file = "./123.xlsx";//iconv("utf-8", "gb2312", "./123.xlsx");   //转码  
	    if(empty($file) OR !file_exists($file)) {  
	        die('file not exists!');  
	    } */
	    $this->load->model(array("Model_excelOper") );
    	$importDataDes = array('0' => array( 'sheet'=>0, 'row'=>2,
					'fieldKey' => array(0=>'EmployeeID', 1=>'FName',))
			);
    	$importData = $this->Model_excelOper->importExecl($_FILES['File']['tmp_name'],$importDataDes);
    	echo json_encode($importData);
	}  

	function edit(){
		$post = $this->input->post();
		$this->load->database();
    	echo json_encode($this->db->where('id',$post['id'])->update('ttt',$post));
	}

	public function show(){
		$post = $this->input->post('id');
    	$this->load->database();
    	echo json_encode($this->db->where("id",$post)->get('ttt')->result_array());
    }
}
