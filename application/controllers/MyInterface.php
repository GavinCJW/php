<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyInterface extends CI_Controller {

	//#MyInterface
//897761051ef87b1a2cc96a1663f2e8f0/MyInterface/test_start
public function test(){
	$input = $this->input->get();
	$fields = array();
		foreach ($fields as $val)
			if (!isset($input[$val]))
				return false;
	echo '{"a":"test","b":"test"}';
}
//897761051ef87b1a2cc96a1663f2e8f0/MyInterface/test_end
	
}
