# PHP
---
## PHP基础
### 闭包
> 闭包：（匿名函数），PHP是用C写的，lambda表达式，在C中lambda不使用外部变量时（即[]），才可以转换为函数，而使用[=],[&],[=,&x],[x,&]则无法转换为函数，只能作为lambda表达式使用，根据这些，我们其实就可以理解闭包的概念了。
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
### 文件解析
>文件解析:
样本文件(ini)格式：
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
    		if($value == "")
    			continue;
    		$pos = strpos($value, ';');
    		if($pos !== false) {
    			$value = substr($value,0,$pos);
    			if(strpos($value, '=') === false)
    				continue;
    		}
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
    	    if($value == "")
    			continue;
    		$pos = strpos($value, ';');
    		if($pos !== false) {
    			$value = substr($value,0,$pos);
    			if(strpos($value, '=') === false)
    				continue;
    		}
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
### 命名空间与引入
>include&require
```
require:
    执行到require（）时，只会读取一次文件，故常放在程序开头，文件引入后PHP会将原文件重新编译，让引入文件成为原文件的一部分。
    require():无条件包含，如果文件不存在，会报出一个Fatal Error。
    require遇到错误时，直接报错并停止运行程序。
include:
    执行到include（）时，每次都会读取文件，故常用于流程控制的区段，如条件判断或循环中。
    include() : 有条件包含，如果文件不存在，会给出一个warning。
    include遇到错误时（引用的文件不存在），PHP只是报错，但程序会继续运行。
require_once & include_once:
    相当于C++中的#pragma once,在导入文件时会检查是否重复加载，防止文件重复加载
```
>namespace&use
```PHP
namespace Test\A;
class Hello{
    function __construct(){
            echo 'hello world!';
        }
}
//加入命名空间必须要使用\
new \Test\A\Hello();//输出 hello world!;
new \Hello(); //代码报错：Fatal error: Class 'Hello' not found
use Test\A
new A\Hello();//输出 hello world!;
use Test\A as B
new B\Hello();//输出 hello world!;
//使用use可以省略最开始\符号
//use Test\A 相当于 use Test\A as A
//也可以指定use命名空间下的类
use Test\A\Hello as A
new A();//输出 hello world!;
```
### 运算符
>(PHP7) 太空船操作符(组合比较符<=>)用于比较两个表达式。当$a小于、等于或大于$b时它分别返回-1、0或1。 比较的原则是沿用 PHP 的常规比较规则进行的。
```PHP
  echo 1 <=> 1; // 0
  echo 1 <=> 2; // -1
  echo 2 <=> 1; // 1
```
>(PHP7)null合并运算符,由于日常使用中存在大量同时使用三元表达式和 isset()的情况， 我们添加了null合并运算符 (??) 这个语法糖。
如果变量存在且值不为NULL， 它就会返回自身的值，否则返回它的第二个操作数。
```PHP
$user = $test['user'] ?? 'nobody';//相当于$user = isset($test['user']) ? $test['user'] : 'nobody';
```
### 生成器
>Iterator
```PHP
  Iterator extends Traversable {
    /* Methods */
    abstract public mixed current ( void );
    abstract public scalar key ( void );
    abstract public void next ( void );
    abstract public void rewind ( void );
    abstract public boolean valid ( void );
  }
  //当一个实现了Iterator接口的对象，被foreach遍历时，会自动调用这些方法。调用的循序是：
  //rewind()[next()第一次调用为rewind，之后都会调用next] -> valid() -> current() -> key()
  class myIterator implements Iterator {  
    protected $key;
    protected $val;
    protected $count;

    public function __construct($count){
      $this->count = $count;
      var_dump(__METHOD__); 
    }

    public function rewind(){
      $this->key = 0;
      $this->val = 0;
      var_dump(__METHOD__);  
    }

    public function next(){
      $this->key ++;
      $this->val ++;
      var_dump(__METHOD__);  
    }

    public function current(){
      var_dump(__METHOD__); 
      return $this->val; 
    }

    public function key(){
      var_dump(__METHOD__); 
      return $this->key; 
    }

    public function valid(){
      var_dump(__METHOD__); 
      return $this->key < $this->count; 
    }
  }

  foreach(new myIterator(3) as $key => $value) {  
    var_dump($key, $value);  
    echo '---------------------------'."\n";
  }  
  /*输出结果：
  string(23) "myIterator::__construct"
  string(18) "myIterator::rewind"
  string(17) "myIterator::valid"
  string(19) "myIterator::current"
  string(15) "myIterator::key"
  int(0)
  int(0)
  ---------------------------
  string(16) "myIterator::next"
  string(17) "myIterator::valid"
  string(19) "myIterator::current"
  string(15) "myIterator::key"
  int(1)
  int(1)
  ---------------------------
  string(16) "myIterator::next"
  string(17) "myIterator::valid"
  string(19) "myIterator::current"
  string(15) "myIterator::key"
  int(2)
  int(2)
  ---------------------------
  string(16) "myIterator::next"
  string(17) "myIterator::valid"
  string(19) "myIterator::current"
  string(15) "myIterator::key"
  int(3)
  int(3)
  ---------------------------
  string(16) "myIterator::next"
  string(17) "myIterator::valid"
  */
```
>yield
 1. yield只能用于函数内部，在非函数内部运用会抛出错误。
 2. 如果函数包含了yield关键字的，那么函数执行后的返回值永远都是一个Generator对象。
 3. 如果函数内部同事包含yield和return 该函数的返回值依然是Generator对象，但是return将会终止生成器继续执行。
 4. Generator类实现了Iterator接口。
 5. 可以通过返回的Generator对象内部的方法，获取到函数内部yield后面表达式的值。
 6. 可以通过Generator的send方法给yield 关键字赋一个值。
 7. 一旦返回的Generator对象被遍历完成，便不能调用他的rewind方法来重置
 8. Generator对象不能被clone关键字克隆
```PHP
  function test(){
    yield 1;
    return;
    yield 2;
  }

  $gen = test();
  foreach ($gen as $key => $value) {
    echo "{$key} - {$value}\n";
  }
  /*输出结果：0-1*/
  foreach ($gen as $k => $v) {
    echo "{$k} - {$v}\n";
  }
  /*错误信息：Cannot traverse an already closed generator*/
  /*
  对于$gen来说，相当于返回了一个生成器对象，使用foreach会隐式的调用rewind,valid,current,key,next等函数。对于一个生成器来说，
  如果它执行了next()就无法在进行rewind()的重新配置，无论你显示还是隐式的调用rewind都会抛出：Cannot rewind a generator that was already run
  如果它执行valid()返回false后，生成器会关闭，那么在这之后再一次遍历这个生成器将会抛出：Cannot traverse an already closed generator
  */
  function test(){
    echo yield "a";
    echo yield "b";
  }

  $gen = test();
  echo $gen->send("c");   
  echo $gen->send("d");
  /*输出结果：cbd*/
  /*
  send(),当生成器没有执行rewind,valid,current,key,next时会隐式调用一遍，
  同时在调用next前传递一个参数将当前的yield的值替换，
  因此可以将current()作为send()的参数传入，$gen->send($gen->current())
  */
  //(PHP7)Generator::getReturn() 
  $gen = (function(){
    echo yield "a";
    echo yield "b";
    return 0;
  })();
  echo $gen->getReturn();  
  /*输出结果：0*/
  //(PHP7)生成器委派
  function gen()
  {
    yield 1;
    yield 2;
    yield from gen2();
  }
  function gen2()
  {
    yield 3;
    yield 4;
  }
```
### 序列化与反序列化
>json_encode&json_decode

  - json_encode():将数组或者对象转换成json字符串
  - json_decode():将json字符串转换为数组对象，当传入第二个参数为true时将返回纯数组
  
>serialize&unserialize

  - serialize():将数组或对象序列化成字符串，（带有该数组和对象的信息，来进行反序列化）
  - unserialize():将序列化后的字符串反序列化回数组或对象
  
>base64_encode&base64_decode

  - base64_encode():进行base64编码序列化
  - base64_decode():进行base64解码反序列化
  
>gzcompress&gzuncompress
压缩函数，需要加载zlib组件

  - gzcompress():将字符串进行压缩，可传递第二个参数选择压缩程度level只可传递(-1 - 9)，默认为6
  - gzuncompress():将字符串解压缩，可传递第二个参数设置解码数据的最大程度length
