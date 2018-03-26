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

}