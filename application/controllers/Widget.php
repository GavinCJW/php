<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Widget extends CI_Controller {

	function __construct(){
        parent::__construct();
    }

	public function index(){
		$this->load->view('widget.html');
	}

	public function test(){
		$this->load->model(array("Model_webSocket") );

		$ws = $this->Model_WebSocket->ws();

	}

	
}
