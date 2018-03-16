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
    	echo json_encode($this->db->get('user')->result_array());
    }
}
