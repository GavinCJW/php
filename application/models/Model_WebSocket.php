<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_WebSocket extends CI_Model {
    public function __construct(){
        parent::__construct();

        set_time_limit(0);
    }

    public function ws(){
        $ws = new WS("127.0.0.1",2000);
        $name = [];
        while(true){
            $socket = $ws->get();
            foreach ($socket as $key => $value) {
                if ($ws->connect($value)) 
                    continue;
                $ret = $ws->receive($value);
                if ($ret['code'] == 8) {
                    $ws->disconnect($value);
                    $msg = $this->dealMsg($value,['type' => 'logout','content' => $name[(int)$value]],$name);                        
                } else {
                    if ($ws->HSP($value,$ret['buffer'])) {                            
                        $ws->send(['type' => 'handshake','content' => 'done'],$value);
                        continue;
                    }
                    $msg = $this->dealMsg($value,json_decode($ret['msg'],true),$name);
                } 
                $ws->send($msg);
            }
        }
    }

    /**
     * 拼装信息
     *
     * @param $socket
     * @param $recv_msg
     *          [
     *          'type'=>user/login
     *          'content'=>content
     *          ]
     *
     * @return string
     */
    private function dealMsg($socket, $recv_msg, &$name) {
        $msg_type = $recv_msg['type'];
        $msg_content = $recv_msg['content'];
        $response = [];

        switch ($msg_type) {
            case 'login':
                $name[(int)$socket] = $msg_content;
                // 取得最新的名字记录
                $response['type'] = 'login';
                $response['content'] = $msg_content;
                $response['user_list'] = $name;
                break;
            case 'logout':
                unset($name[(int)$socket]);
                $response['type'] = 'logout';
                $response['content'] = $msg_content;
                $response['user_list'] = $name;
                break;
            case 'user':
                $uname = $name[(int)$socket];
                $response['type'] = 'user';
                $response['from'] = $uname;
                $response['content'] = $msg_content;
                break;
        }

        return $response;
    }
}

class WS{
    const LISTEN_SOCKET_NUM = 10;
    const GUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
    const RECEIVE_SOCKET_NUM = 2048;
    /*
        socket:['id':套接字(int),'status':'状态'(bool),'host','port']
    */
    private $sockets = [];
    private $server;

    public function __construct($host , $port){
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)   
            or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1)  
            or die("socket_option() failed");
        socket_bind($this->master, $host, $port)                    
            or die("socket_bind() failed");
        socket_listen($this->master, self::LISTEN_SOCKET_NUM)                               
            or die("socket_listen() failed");
        $this->sockets[(int)$this->master] = [
            'id' => $this->master,
            'status' => true,
            'host' => $host,
            'port' => $port
        ];
    }

    /*
        获取监视队列下的所有活动socket
    */
    public function get(){
        $write = $except = NULL;
        $socket = array_column($this->sockets, 'id');
        $read_num = socket_select($socket, $write, $except, NULL);
        // select作为监视函数,参数分别是(监视可读,可写,异常,超时时间),返回可操作数目,出错时返回false;
        if (false === $read_num) {
            die("socket_select() failed");
        }
        return $socket;
    }

    /*
        当socket等于服务器时，表示有一个新的连接接入，产生一个新的socket，来绑定这个客户端。
    */
    public function connect($socket) {
        if ($socket == $this->master) {
            $client = socket_accept($this->master);//进程堵塞，直到新的连接进来
            if (false === $client) 
                die("socket_accept() failed");

            socket_getpeername($client, $ip, $port);
            $this->sockets[(int)$client] = [
                'id' => $client,
                'status' => false,
                'ip' => $ip,
                'port' => $port,
            ];
            return true;   
        }
        return false;
    }

    /*
        当客户端关闭连接时，将该socket从socket列表中删除
        (客户端关闭时会自动发送一串8字符的乱码，可以以此为依据，设置什么时候调用该方法)
    */
    public function disconnect($socket) {
        unset($this->sockets[(int)$socket]);
        return true;
    }

    /*
        服务端发送数据
        允许该msg为（数组，对象或JSON；最好直接为JSON）
        允许socket为一个socket，也可为一个socket数组，当socket不传或为NULL时，默认为广播
    */
    public function send($msg,$socket = NULL){
        $is_json = function ($data) {
            if(!is_string($data))
                return false;
            json_decode($data);
            return (json_last_error() == JSON_ERROR_NONE);
        };

        if($socket == NULL)
            $socket = array_column($this->sockets, 'id');

        if(!is_array($socket))
            $socket = array($socket);

        if(!$is_json($msg))
            $msg = json_encode($msg);

        $msg = $this->encodeData($msg);//转为数据帧
        foreach ($socket as $key => $value) {
            if ($value != $this->master) {
                socket_write($value, $msg, strlen($msg));
            }
        }
        return true;
    }

    /*
        接收数据
    */
    public function receive($socket){
        $ret['code'] = @socket_recv($socket, $buffer, self::RECEIVE_SOCKET_NUM, 0);
        $ret['msg'] = $this->decodeData($buffer);//解析为字符串
        $ret['buffer'] = $buffer;
        return $ret;
    }

    /*
        发送握手协议
        判断当该socket已握手连接即不在握手，否则进行握手
    */
    public function HSP($socket, $buffer) {
        if($this->sockets[(int)$socket]['status'])
            return false;
        // 获取到客户端的Sec-WebSocket-Key
        $line_with_key = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
        $key = trim(substr($line_with_key, 0, strpos($line_with_key, "\r\n")));

        //WebSocket特定GUID：258EAFA5-E914-47DA-95CA-C5AB0DC85B11，且新密钥必须通过sha加密和base64编码传递
        $accept_key = base64_encode(sha1($key . self::GUID, true));
        $accept_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $accept_message .= "Upgrade: websocket\r\n";
        $accept_message .= "Sec-WebSocket-Version: 13\r\n";
        $accept_message .= "Connection: Upgrade\r\n";
        $accept_message .= "Sec-WebSocket-Accept:" . $accept_key . "\r\n\r\n";

        socket_write($socket, $accept_message, strlen($accept_message));// 返回接受信息
        $this->sockets[(int)$socket]['status'] = true;
        return true;
    }

    /*
        解析webSocket数据帧
    */
    private function decodeData($buffer) {
        $decoded = '';
        $len = ord($buffer[1]) & 127;//获取数据帧长度
        //处理特殊长度126和127，mark:掩码
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }
    
    /*
        生成webSocket数据帧
    */
    public function encodeData($msg) {
        $frame = [];
        $frame[0] = '81';
        $len = strlen($msg);
        $s = dechex($len);
        if ($len < 126) {
            $frame[1] = $len < 16 ? '0' . $s : $s;
        } else if ($len < 65025) {
            $frame[1] = '7e' . str_repeat('0', 4 - strlen($s)) . $s;
        } else {
            $frame[1] = '7f' . str_repeat('0', 16 - strlen($s)) . $s;
        }
        $frame[2] = '';
        for ($i = 0; $i < $len; $i++) {
            $frame[2] .= dechex(ord($msg{$i}));
        }
        $encode = implode('', $frame);
        //转换为十六进制字符串，先高位半字节
        return pack("H*", $encode);
    }

}

    


