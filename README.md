# PHP
[PHP文档][1]

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
//(PHP7)允许use同时包含多个类，函数，常量
use some\namespace\{ClassA, ClassB, ClassC as C};
use function some\namespace\{fn_a, fn_b, fn_c};
use const some\namespace\{ConstA, ConstB, ConstC};
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
### 其他
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
>(PHP7)intdiv()除法运算函数
```PHP
  intdiv(10, 3);//3
```
>(PHP7)define()常量函数
```PHP
  define("A",1);
  echo A;//直接使用A，不需要$A
```
>clone 用 clone 克隆出来的对象与原对象没有任何关系，它是把原来的对象从当前的位置重新复制了一份，也就是相当于在内存中新开辟了一块空间。
```PHP
  class MyClone{
    public $a
    public $b
    public function __clone(){
      echo"对象已被克隆";
    }
  }
  $A = new MyClone();
  $B = $A;//将A赋值给B，传递的是一个引用，也就是当B或者A其中一个修改类里的变量$a都会互相影响。
  $C = clone $A;//将A克隆一个复制体将该复制体传递给C，A与C的操作互不影响
  /*使用clone关键字会默认调用类里的__clone()函数*/
  //引申浅克隆与深克隆
  class MyTest{
    public $t = 1;
  }
  $A->a = 0;
  $A->b = new MyTest();
  $D = clone $A;
  $A->a = 1;
  $A->b->t = 0;
  var_dump($A);//1,0
  var_dump($D);//0,0
  /*克隆只会克隆当前对象，当对象里的变量指向对象时，克隆只会生成该变量的对象引用赋值给克隆体*/
  /*深克隆：修改__clone(),当变量多时或动态变化时将无法这样进行深克隆*/
  public function __clone(){
    $b =clone new MyTest();
  }
  /*深克隆，通过序列化与反序列化的方式进行克隆*/
  $E = unserialize(serialize($A));
```
>可变变量,$$
```PHP
  $a = "hello";
  $$a = "world";
  echo "$a ${$a}"; // 等于echo "$a $hello";,不加{}$$a会被解释为$hello字符串输出
  $$a[1] = "world";
  echo ${$a}[1] ;//world
  echo ${$a[1]} ;//报错
  /*
    PHP会优先解释$a为一个变量X，然后在解释$X[1],
    因此如果使用可变变量在数组与变量间的区别需要用大括号将其括起来，来防止歧义
  */
```

### C++扩展
>Windows 
  - 借助cygwin，工具生成扩展项目文件夹（请下载cygwin,打开cmd输入"cygcheck -c cygwin"，如果status为ok即安装是否成功）
  - 下载php源码(php-xxxx.tar.gz)并将win32\build\config.w32.h.in复制到main文件夹下并修改名字为config.w32.h
     PHP5的源码是用VC编译的所以带有skeleton.dsp文件可以通过VC++，VS等编译器直接编译，
     如果是PHP7由于不在包含dsp无法直接转换为VC++，VS等编译器可打开的项目，具体操作请参看后续方式
  - 在系统环境变量中将正在使用的PHP的路径添加，然后cmd进入php源码的ext文件夹里（包含ext_skel_win32.php,ext_skel文件和skeleton文件夹），
    执行"php ext_skel_win32.php --extname=xxx"，然后ext文件夹下会出现xxx文件夹，里面包含（php_xxx.dsp(php7不会生成),php_xxx.h,php_xxx.c,xxx.php,config.m4,config.w32）
  - PHP7添加dsp文件，可通过下载PHP5源码，然后复制skeleton文件夹下的skeleton.dsp文件，然后在ext下新建一个create_dsp.php
  ```PHP
  <?php
    $extname='';
    $skel = "skeleton";
    foreach($argv as $arg) {
        if(strtolower(substr($arg, 0, 9)) == "--extname") {
            $extname= substr($arg, 10);
        }
        if(strtolower(substr($arg, 0, 6)) == "--skel") {
            $skel= substr($arg, 7);
        }
    }

    $fp = fopen("$skel/skeleton.dsp","rb");
    if ($fp) {
        $dsp_file =fread($fp, filesize("$skel/skeleton.dsp"));
        fclose($fp);
        $dsp_file =str_replace("extname", $extname, $dsp_file);
        $dsp_file =str_replace("EXTNAME", strtoupper($extname), $dsp_file);
        $fp =fopen("$extname/$extname.dsp", "wb");
        if ($fp) {
            fwrite($fp,$dsp_file);
            fclose($fp);
        }
    }

    $fp =fopen("$extname/$extname.php", "rb");
    if ($fp) {
        $php_file =fread($fp, filesize("$extname/$extname.php"));
        fclose($fp);
        $php_file =str_replace("dl('", "dl('php_", $php_file);
        $fp =fopen("$extname/$extname.php", "wb");
        if ($fp) {
            fwrite($fp,$php_file);
            fclose($fp);
        }
    }
    ?>
  ```
  并在cmd中执行"php create_dsp.php --extname=xxx"，就可以在xxx文件夹下看到xxx.dsp文件了，这时候通过VS打开xxx.dsp转换一下就是WIN32项目了。
  - 在VS中选择链接器，附加库目录，加载你当前使用的PHP下的dev文件夹下的lib，然后生成DLL文件即可，将生成的dll文件放到正在使用的PHP目录下的ext文件里，并在php.ini中添加该扩展，重启PHP。
    可能会遇到编译不同或线程安全的问题，
    线程方面设置(#define ZTS 等于 TS ， #undef ZTS 等于 NTS)，
    编译方面设置（#define PHP_COMPILER_ID "VC14"（VC几看启动php-cgi报错的提示））
  - 最后也可以自己创建一个空的win32项目，然后自己写代码，将源码包添加进包路径里，然后添加正在使用的php的dev文件的lib即可，VS属性文件.props
  ```XML
    <?xml version="1.0" encoding="utf-8"?>
      <Project ToolsVersion="4.0" xmlns="http://schemas.microsoft.com/developer/msbuild/2003">
        <ImportGroup Label="PropertySheets" />
        <PropertyGroup Label="UserMacros" />
        <PropertyGroup />
        <ItemDefinitionGroup>
          <ClCompile>
            <AdditionalIncludeDirectories>
              ..\..;..\..\main;..\..\Zend;..\..\TSRM;..\..\win32;
              %(AdditionalIncludeDirectories)
            </AdditionalIncludeDirectories>
          </ClCompile>
          <Link>
            <AdditionalLibraryDirectories>
              ...\dev;%(AdditionalLibraryDirectories)
            </AdditionalLibraryDirectories>
            <AdditionalDependencies>php7.lib;%(AdditionalDependencies)</AdditionalDependencies>
          </Link>
        </ItemDefinitionGroup>
        <ItemGroup />
    </Project>
  ```
>扩展开发
  ```C++
    //php_test.hpp
    
      #pragma once
      #define ZEND_WIN32
      #define PHP_WIN32
      #define ZEND_DEBUG 0

      #ifndef PHP_TEST_H
      #define PHP_TEST_H

      #ifdef PHP_WIN32  
      #define PHP_TEST_API __declspec(dllexport)//声明为导出函数
      #define _STATIC_ASSERT(expr) typedef char __static_assert_t[ (expr)?(expr):1 ]  
      #elif defined(__GNUC__) && __GNUC__ >= 4
      #define PHP_PHPTest_API __attribute__ ((visibility("default")))
      #else  
      #define PHP_PHPTest_API
      #define _STATIC_ASSERT(expr) typedef char __static_assert_t[ (expr) ]  
      #endif  

      //线程安全
      #ifdef ZTS
      #include "TSRM.h"
      #define TEST_G(v) TSRMG(test_globals_id, zend_test_globals *, v)
      #else
      #define TEST_G(v) (test_globals.v)
      #endif

      #ifndef PHP_TEST_VERSION
      #define PHP_TEST_VERSION NO_VERSION_YET
      #endif 

      extern "C" {
      #include "zend_config.w32.h"
      #include "php.h"
      #include "ext/standard/info.h"
      }

      PHP_MINIT_FUNCTION(test);
      PHP_MSHUTDOWN_FUNCTION(test);
      PHP_RINIT_FUNCTION(test);
      PHP_RSHUTDOWN_FUNCTION(test);
      PHP_MINFO_FUNCTION(test);

      // PHP_FUNCTION  只用来声明函数的名称，置于函数的参数将在cpp中定义 
      PHP_FUNCTION(test_array);//数组反转
      #endif/* PHP_TEST_MAIN_H*/
      
      //php_test.cpp
      
      #include <vector>
      #include <string>
      #include "php_test.hpp"
      #include "php_util.hpp"
      using namespace std;

      zend_function_entry test_functions[] = {
        PHP_FE(test_array, NULL)
        PHP_FE_END
      };//自定义的函数

      extern zend_module_entry test_module_entry = {
      #if ZEND_MODULE_API_NO >= 20010901
        STANDARD_MODULE_HEADER,//扩展头信息
      #endif
        "test",//扩展名
        test_functions,//扩展函数
        PHP_MINIT(test),
        PHP_MSHUTDOWN(test),
        PHP_RINIT(test),
        PHP_RSHUTDOWN(test),
        PHP_MINFO(test),
      #if ZEND_MODULE_API_NO >= 20010901
        PHP_TEST_VERSION,//版本
      #endif
        STANDARD_MODULE_PROPERTIES
      };

      ZEND_GET_MODULE(test);

      PHP_MINIT_FUNCTION(test){return SUCCESS;}
      PHP_MSHUTDOWN_FUNCTION(test){return SUCCESS;}
      PHP_RINIT_FUNCTION(test){return SUCCESS;}
      PHP_RSHUTDOWN_FUNCTION(test){return SUCCESS;}
      PHP_MINFO_FUNCTION(test){
        php_info_print_table_start();
        php_info_print_table_header(2, "test support", "enabled");
        php_info_print_table_end();
      }
      
      PHP_FUNCTION(test_array){
        zval *arr = NULL;
        /*
        获取参数,可以获取多个参数，"s|a"表示必传strng类型,可选类型数组
        php 代码 C/C++
        boolean b zend_bool
        long l long
        double d double
        string s char*, int
        resource r zval*
        array a zval*
        object o zval*
        zval z zval*
        */
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "a", &arr) == FAILURE)
          RETURN_NULL();

        auto arr_hash = Z_ARRVAL_P(arr);//将zval *转换成zend_array *
        //自定义函数将zend_array *转换成std::vector<std::map<std::string, std::string>>
        auto ret = ZEND_ARRAY_TO_MAP(Z_ARRVAL_P(arr));
        if (ret.empty()) {
          php_error(E_WARNING, "array key => value must be STRING , LONG , DOUBLE");
          RETURN_NULL();
        }
        array_init_size(return_value, ret.size());//初始化内部返回变量return_value
        for (auto map : ret) {
          for (auto val : map) {
            //两种方式向return_value添加数据
            /*zval temp;
            ZVAL_STRING(&temp , val.first.data());
            zend_hash_update(Z_ARRVAL_P(return_value), strpprintf(0, val.second.data()), &temp);*/
            add_assoc_string(return_value, val.second.data(), (char *)val.first.data());
          }
        }
      }
      /*
        可以在php_test.cpp头加入  #define PHP_COMPILER_ID "VC14" 
        也可以在预编译上加入PHP_COMPILER_ID="VC14" 
      */
  ```
  
## PHP编程思想
### AOP
> 面向切面编程，将次要的业务（日志，事务，监控）等通过切面的方式进行编程，降低代码的冗余和方便代码的维护扩展
```PHP

  interface Business {
      public function before($data);
      public function after($data);
  };
  
  class A implements Business {
      public function before($data){...}
      public function after($data){...}
  }
  
  class B implements Business {
      public function before($data){...}
      public function after($data){...}
  }
  
  ...
  
  class AOP{
    public static function before($business,$data = null){
          foreach ($business as $val) {
            $a = new $val();
            $a->before($data);
          } 
    }
      public static function after($business,$data = null){
          foreach ($business as $val) {
            $a = new $val();
            $a->after($data);
          } 
    }
  }
  
  $Temp = array("A","B");
  AOP::before($Temp);
  ...//业务代码
  AOP::after($Temp);
```


  [1]: http://www.php.net/manual/zh/
