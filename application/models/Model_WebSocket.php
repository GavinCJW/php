<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_WebSocket extends CI_Model {
    public function __construct(){
        parent::__construct();

        set_time_limit(0);
    }

    public function aaa(){
        $ws = new WebSocket("127.0.0.1",2000);
    }

    public function ws(){
        $ws = new WS("127.0.0.1",2000);
        $name = [];
        $n = 5;
        while($n--){
            $socket = $ws->get();
            if(!$socket['code'])
                return $socket;

            foreach ($socket['data'] as $key => $value) {
                file_put_contents('/websocket_debug.log', "socket:".$value. "\r\n", FILE_APPEND);
                if ($ws->connect($value)['code']) {
                    continue;
                }else{
                    $ret = $ws->receive($value);
                    file_put_contents('/websocket_debug.log', "ret:".json_encode($ret). "\r\n", FILE_APPEND);
                    if ($ret['code'] == 8) {
                        $this->disconnect($value);
                        $msg = [
                            'type' => 'logout',
                            'content' => $name[(int)$value],
                        ];
                        $msg = $this->dealMsg($value,$msg,$name);

                        $ws->broadcast($msg);
                    } else {
                        if ($ws->HSP($value,$ret['buffer'])) {
                            $ws->send(['type' => 'handshake','content' => 'done',],$value);
                            continue;
                        }else{
                            $msg = $ret['msg'];
                            $msg = $this->dealMsg($value,$msg,$name);

                            $ws->broadcast($msg);
                        }
                    }
                }

                
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
    //GUID-webSocket特定ID
    const GUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
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

    public function get(){
        $write = $except = NULL;
        $socket = array_column($this->sockets, 'id');
        $read_num = socket_select($socket, $write, $except, NULL);
        // select作为监视函数,参数分别是(监视可读,可写,异常,超时时间),返回可操作数目,出错时返回false;
        if (false === $read_num) {
            die("socket_select() failed");
        }else{
            $ret['code'] = true;
            $ret['data'] = $socket;
        }
        return $ret;
    }

    public function connect($socket) {
        if ($socket == $this->master) {
            $client = socket_accept($this->master);//进程堵塞，直到新的连接进来
                
            if (false === $client) {
                $ret['code'] = false;
                $ret['msg'] = socket_strerror($err_code);
            } else {
                socket_getpeername($client, $ip, $port);
                $this->sockets[(int)$client] = [
                    'id' => $client,
                    'status' => false,
                    'ip' => $ip,
                    'port' => $port,
                ];
                $ret['code'] = true;
                $ret['data'] = $this->sockets[(int)$client];
            }
            
        }else{
            $ret['code'] = false;
        }
        return $ret;
    }

    public function disconnect($socket) {
        unset($this->sockets[(int)$socket]);
        return true;
    }

    /*
        服务端发送数据，socket为套接字id(数组)
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

        $msg = $this->encodeData($msg);
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
        $ret['code'] = @socket_recv($socket, $buffer, 2048, 0);
        $ret['msg'] = $this->decodeData($buffer);
        $ret['buffer'] = $buffer;
        return $ret;
    }

    /*
        发送握手协议
    */
    public function HSP($socket, $buffer) {
        if($this->sockets[(int)$socket]['status'])
            return false;
        // 获取到客户端的Sec-WebSocket-Key
        $sec_ws_key = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:'));
        $key = trim(substr($sec_ws_key, 18, strpos($sec_ws_key, "\r\n")));

        $upgrade_key = base64_encode(sha1($key.self::GUID, true));// websocket返回的Sec-WebSocket-Accept生成法
        $upgrade_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $upgrade_message .= "Upgrade: websocket\r\n";
        $upgrade_message .= "Sec-WebSocket-Version: 13\r\n";
        $upgrade_message .= "Connection: Upgrade\r\n";
        $upgrade_message .= "Sec-WebSocket-Accept:" . $upgrade_key . "\r\n\r\n";
        file_put_contents('/websocket_debug.log', "vvvvvvvvvv:".$socket. "\r\n", FILE_APPEND);
        socket_write($socket, $upgrade_message, strlen($upgrade_message));// 向socket里写入握手协议
        $this->sockets[(int)$socket]['status'] = true;
        return true;
    }

    /*
        广播消息
    */
    public function broadcast($msg){
        $msg = $this->encodeData(json_encode($msg));
        foreach ($this->sockets as $value) {
            if($value['id'] != $this->master)
                file_put_contents('/websocket_debug.log', "vvvv:".$value['id']. "\r\n", FILE_APPEND);
                socket_write($value['id'], $msg, strlen($msg));
        }
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
    private function encodeData($msg) {
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

class WebSocket {
    const LISTEN_SOCKET_NUM = 10;

    /**
     * @var array $sockets
     *    [
     *      (int)$socket => [
     *                        info
     *                      ]
     *      ]
     *  todo 解释socket与file号对应
     */
    private $sockets = [];
    private $master;

    public function __construct($host, $port) {

        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)   
            or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1)  
            or die("socket_option() failed");
        socket_bind($this->master, $host, $port)                    
            or die("socket_bind() failed");
        socket_listen($this->master, self::LISTEN_SOCKET_NUM)                               
            or die("socket_listen() failed");

        $this->sockets[0] = ['resource' => $this->master];
        $pid = get_current_user();
        $debug = "server: {$this->master} started,pid: {$pid}";
        
        while (true) {
            try {
                $this->doServer();
            } catch (\Exception $e) {
                $e->getCode();
                $e->getMessage();
            }
        }
    }

    private function doServer() {
        $write = $except = NULL;
        $sockets = array_column($this->sockets, 'resource');
        $read_num = socket_select($sockets, $write, $except, NULL);
        // select作为监视函数,参数分别是(监视可读,可写,异常,超时时间),返回可操作数目,出错时返回false;
        if (false === $read_num) {
            $err_code = socket_last_error();
            $err_msg = socket_strerror($err_code);
            return;
        }
        foreach ($sockets as $socket) {
            // 如果可读的是服务器socket,则处理连接逻辑
            if ($socket == $this->master) {
                $client = socket_accept($this->master);
                // 创建,绑定,监听后accept函数将会接受socket要来的连接,一旦有一个连接成功,将会返回一个新的socket资源用以交互,如果是一个多个连接的队列,只会处理第一个,如果没有连接的话,进程将会被阻塞,直到连接上.如果用set_socket_blocking或socket_set_noblock()设置了阻塞,会返回false;返回资源后,将会持续等待连接。
                if (false === $client) {
                    $err_code = socket_last_error();
                    $err_msg = socket_strerror($err_code);
                    continue;
                } else {
                    self::connect($client);
                    continue;
                }
            } else {
                // 如果可读的是其他已连接socket,则读取其数据,并处理应答逻辑
                $bytes = @socket_recv($socket, $buffer, 2048, 0);
                file_put_contents('/websocket_debug.log', "this->recv_msg:".$buffer. "\r\n", FILE_APPEND);
                if ($bytes < 9) {
                    $recv_msg = self::parse($buffer);
                    $recv_msg = $this->disconnect($socket);
                } else {
                    if (!$this->sockets[(int)$socket]['handshake']) {

                        self::handShake($socket, $buffer);
                        continue;
                    } else {
                        $recv_msg = self::parse($buffer);
                    }
                }
                $msg = self::dealMsg($socket, $recv_msg);

                $this->broadcast($msg);
            }
        }
    }

    /**
     * 将socket添加到已连接列表,但握手状态留空;
     *
     * @param $socket
     */
    public function connect($socket) {
        socket_getpeername($socket, $ip, $port);
        $socket_info = [
            'resource' => $socket,
            'uname' => '',
            'handshake' => false,
            'ip' => $ip,
            'port' => $port,
        ];
        $this->sockets[(int)$socket] = $socket_info;
        $debug = array_merge(['socket_connect'], $socket_info);
    }

    /**
     * 客户端关闭连接
     *
     * @param $socket
     *
     * @return array
     */
    private function disconnect($socket) {
        $recv_msg = [
            'type' => 'logout',
            'content' => $this->sockets[(int)$socket]['uname'],
        ];
        unset($this->sockets[(int)$socket]);

        return $recv_msg;
    }

    /**
     * 用公共握手算法握手
     *
     * @param $socket
     * @param $buffer
     *
     * @return bool
     */
    public function handShake($socket, $buffer) {
        // 获取到客户端的升级密匙
        $line_with_key = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
        $key = trim(substr($line_with_key, 0, strpos($line_with_key, "\r\n")));

        // 生成升级密匙,并拼接websocket升级头
        $upgrade_key = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));// 升级key的算法
        $upgrade_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $upgrade_message .= "Upgrade: websocket\r\n";
        $upgrade_message .= "Sec-WebSocket-Version: 13\r\n";
        $upgrade_message .= "Connection: Upgrade\r\n";
        $upgrade_message .= "Sec-WebSocket-Accept:" . $upgrade_key . "\r\n\r\n";

        socket_write($socket, $upgrade_message, strlen($upgrade_message));// 向socket里写入升级信息
        $this->sockets[(int)$socket]['handshake'] = true;

        socket_getpeername($socket, $ip, $port);
        $debug = array(['hand_shake',$socket,$ip, $port]);

        // 向客户端发送握手成功消息,以触发客户端发送用户名动作;
        $msg = [
            'type' => 'handshake',
            'content' => 'done',
        ];
        $msg = $this->build(json_encode($msg));
        socket_write($socket, $msg, strlen($msg));
        return true;
    }

    /**
     * 解析数据
     *
     * @param $buffer
     *
     * @return bool|string
     */
    private function parse($buffer) {
        $decoded = '';
        $len = ord($buffer[1]) & 127;
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
        file_put_contents('/websocket_debug.log', "decoded:".$decoded. "\r\n", FILE_APPEND);
        return json_decode($decoded, true);
    }

    /**
     * 将普通信息组装成websocket数据帧
     *
     * @param $msg
     *
     * @return string
     */
    private function build($msg) {
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
    private function dealMsg($socket, $recv_msg) {
        $msg_type = $recv_msg['type'];
        $msg_content = $recv_msg['content'];
        $response = [];

        switch ($msg_type) {
            case 'login':
                $this->sockets[(int)$socket]['uname'] = $msg_content;
                // 取得最新的名字记录
                $user_list = array_column($this->sockets, 'uname');
                $response['type'] = 'login';
                $response['content'] = $msg_content;
                $response['user_list'] = $user_list;
                break;
            case 'logout':
                $user_list = array_column($this->sockets, 'uname');
                $response['type'] = 'logout';
                $response['content'] = $msg_content;
                $response['user_list'] = $user_list;
                break;
            case 'user':
                $uname = $this->sockets[(int)$socket]['uname'];
                $response['type'] = 'user';
                $response['from'] = $uname;
                $response['content'] = $msg_content;
                break;
        }

        return $this->build(json_encode($response));
    }

    /**
     * 广播消息
     *
     * @param $data
     */
    private function broadcast($data) {
        foreach ($this->sockets as $socket) {
            if ($socket['resource'] == $this->master) {
                continue;
            }
            socket_write($socket['resource'], $data, strlen($data));
        }
    }

    
}

