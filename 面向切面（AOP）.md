# PHP
---
## PHP编程思想
> AOP
>> 面向切面编程，将次要的业务（日志，事务，监控）等通过切面的方式进行编程，降低代码的冗余和方便代码的维护扩展
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
