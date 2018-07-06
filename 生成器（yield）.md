# PHP
---
## PHP基础
> 生成器
>>Iterator
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
>>yield
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
```
