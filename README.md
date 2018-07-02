# PHP
---
## PHP基础
> 闭包
>> 闭包：（匿名函数），PHP是用C写的，lambda表达式，在C中lambda不使用外部变量时（即[]），才可以转换为函数，而使用[=],[&],[=,&x],[x,&]则无法转换为函数，只能作为lambda表达式使用，根据这些，我们其实就可以理解闭包的概念了。
```PHP
举一个例子：
    function callback($callback){
      $callback();
    };
    //直接使用匿名函数
    callback(function(){
    print "hello world!";
    });
    ******输出结果：hello world!******
    //使用外部变量（use（$xxx）,use相当于[]，$xxx指定的外部变量其作用为=,传递一个复制体，&$xxx其作用为&，传递一个引用）
    $msg = "hello everyone!";  
    $copy = function () use ($msg) {  
      print $msg;  
    };  
    $quote = function () use (&$msg) {
      print $msg;
    };
    $msg = "hello, everybody!";  
    callback($copy);
    callback($quote);
    ******输出结果：hello everyone!******
    ******输出结果：hello everybody!******
```
>文件解析
>>.ini文件解析:
样本文件格式：
[aa]
ab=12
ac=123
ad=1234
[bb]
bb=22
bc=343
```PHP
1:
    fuction (){
        var_dump(parse_ini_file("xxx/xxx.ini",true));
    }
2：
    function(){
        $file = fopen("xxx/xxx,ini", "r") or die("Unable to open file!");
        $data = array();
    	while(!feof($file)){
    		$tmp = trim(fgets($file),PHP_EOL);
    		if(preg_match('/\[.*]/', $tmp)){
    			$d = &$data[trim($tmp,"[]")];
    		}else{
    			$temp = explode("=",trim($tmp,PHP_EOL));
    			$d[trim($temp[0]," ")] = trim($temp[1]," ");
    		}
    	}
    	unset($d);
		var_dump ($data);
    }
3：
    function(){
        $file = file_get_contents("a.ini");

    	$tmp = explode(PHP_EOL,$file);
    	$data = array();
    	foreach ($tmp as $key => $value) {
    		if(preg_match('/\[.*]/', $value)){
    			$d = &$data[trim($value,"[]")];
    		}else{
    			$temp = explode("=",trim($value,PHP_EOL));
    			$d[trim($temp[0]," ")] = trim($temp[1]," ");
    		}
    	}
    	unset($d);
		var_dump ($data);
    }

    输出结果：
        array(2) {
            ["aa"]=> array(3) { 
                ["ab"]=> string(2) "12" 
                ["ac"]=> string(3) "123"
                ["ad"]=> string(4) "1234" 
            } 
            ["bb"]=> array(2) {
                ["bb"]=> string(2) "22" 
                ["bc"]=> string(3) "343"
            } 
        }
    
```
>>.txt文件解析
