<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemInterface extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->database();
    }

	public function index(){
		$this->load->view('interface.html');
	}

	public function list(){
		$data = $this->db->get('automation')->result_array();
		foreach ($data as $key => &$value) {
			$value['type'] = $value['type'] == 0 ? 'GET' : 'POST';
			$value['dataType'] = $value['dataType'] == 0 ? 'JSON' : 'ARRAY';
		}
    	echo json_encode($data);
	}

	public function insert(){
		$post = $this->input->post();
		$this->load->database();
		$post['sign'] = md5(time());
		$post['url'] = "/MyInterface/".$post['name'];
		$this->appand_file($this->content($post));
		unset($post['name']);
		echo json_encode($this->db->insert('automation', $post));
	}

	public function show(){
		$post = $this->input->post('id'); 
		$automation = $this->db->where('id',$post)->get('automation')->row_array();
		$sign = "//".$automation['sign'].$automation['url'];
		$ret = $this->sign($sign);
		$ret = str_replace($sign."_start".PHP_EOL , '' , $ret);
		echo $ret;
	}

	public function delete(){
		$post = $this->input->post('id'); 
		if(!is_array($post))
			$post = array($post);

		$this->db->trans_start();
    	foreach ($post as $key => $value) {
    		$automation = $this->db->where('id',$value)->get('automation')->row_array();
    		$this->db->where('id',$value)->delete('automation');
			$sign = "//".$automation['sign'].$automation['url'];
			$this->remove_file($sign);
    	}
    	echo json_encode($this->db->trans_complete());		
	}

	public function edit(){
		$post = $this->input->post();
		$automation = $this->db->where('id',$post['id'])->get('automation')->row_array();
		$post['sign'] = $automation['sign'];
		$post['url'] = $automation['url'];
		$post['name'] = str_replace('/MyInterface/','',$post['url']);
		$sign = "//".$automation['sign'].$automation['url'];
		$this->update_file($this->content($post),$sign);
		unset($post['name']);
		echo json_encode($this->db->where('id',$post['id'])->update('automation',$post));
	}

	private function content($data){
		$title = $data['sign'].$data['url'];
		$type = '$input = $this->input->'.($data['type'] == 1 ? 'post' : 'get').'();';
		
		$input_content = json_decode($data['data'],true);
		$input_temp = "";
		foreach ($input_content as $key => $value) {
			$input_temp .= "'".$key."',";
		}
		$input = '$fields = array('.$input_temp.');';

		$result = $data['dataType'] == 0 ? "echo '".$data['result']."';" : ("var_dump(json_decode('".$data['result']."',true));");

		$content = "//".$title."_start".PHP_EOL;
		$content .= "public function ".$data['name']."(){".PHP_EOL;
		$content .= "\t".$type.PHP_EOL;
		$content .= "\t".$input.PHP_EOL."\t\t".'foreach ($fields as $val)'.PHP_EOL."\t\t\t".'if (!isset($input[$val]))'.PHP_EOL."\t\t\t\treturn false;".PHP_EOL;
		$content .= "\t".$result.PHP_EOL;
		$content .= "}".PHP_EOL;
		$content .= "//".$title."_end";
		return $content;
	}

	private function sign($sign){
		$str = file_get_contents(FCPATH.'application/controllers/MyInterface.php');
		$start = strpos($str,$sign."_start");
		$length = strpos($str,$sign."_end") - $start;
		$ret = mb_substr($str,$start,$length,"UTF-8");
		return $ret;
	}

	private function appand_file($content){
		$str = file_get_contents(FCPATH.'application/controllers/MyInterface.php');
		$str = str_replace("//#MyInterface","//#MyInterface".PHP_EOL.$content,$str);
		file_put_contents(FCPATH.'application/controllers/MyInterface.php', $str);
	}

	private function update_file($content , $sign){
		$ret = $this->sign($sign).$sign."_end";
		$str = file_get_contents(FCPATH.'application/controllers/MyInterface.php');
		$str = str_replace($ret,$content,$str);
		file_put_contents(FCPATH.'application/controllers/MyInterface.php', $str);
	}

	private function remove_file($sign){
		$ret = $this->sign($sign).$sign."_end".PHP_EOL;
		$str = file_get_contents(FCPATH.'application/controllers/MyInterface.php');
		$str = str_replace($ret, '' , $str);
		file_put_contents(FCPATH.'application/controllers/MyInterface.php', $str);
	}

}