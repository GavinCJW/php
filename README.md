# php_build

##Bootstrap&CodeIgniter

    

> Bootstrap:

 - [.bootstrap-table][1]
 - [.bootstrap-editable][2]
 - [.bootstrap-datetimepicker][3]
 - [.jquery-print][4]
 - [.jquery.qrcode][5]
 - [.fileinput][6]
 - [.chart][7]

> CodeIgniter:

 - [.PHPEXCEL][8]
 - [.MPDF60][9]
        
----------

结构
 1. Home：

        测试专用，新增的功能都会在这里先进行测试，该页面无迹可寻...
        
 2. Widgets：

        WebSocket:
            执行websocket会堵塞整个PHP进程，导致后续的请求都将无法执行，由于Windows无法使用pcntl_fork()所以暂时无法解决堵塞问题，待后续解决

 3. Charts

        提供图表（条形，折线，扇形）
![图表例图][10]

 4. Tables
    
        表格（提供表格的使用，附带打印，导出EXCEL，二维码等）
![表格例图][11]

 5. Forms
 
        ......

 6. System&Interface

        自动化生成简易化接口
        

 7. ...

        To be continued!
        
            


  [1]: https://github.com/wenzhixin/bootstrap-table
  [2]: https://github.com/wenzhixin/bootstrap-table/tree/develop/dist/extensions/editable
  [3]: https://github.com/uxsolutions/bootstrap-datepicker
  [4]: https://github.com/DoersGuild/jQuery.print
  [5]: https://github.com/jeromeetienne/jquery-qrcode
  [6]: https://github.com/kartik-v/bootstrap-fileinput
  [7]: https://github.com/chartjs/Chart.js
  [8]: https://github.com/PHPOffice/PHPExcel
  [9]: https://github.com/IanNBack/mpdf
  [10]: https://img-blog.csdn.net/20160328172653629
  [11]: http://images2015.cnblogs.com/blog/459756/201511/459756-20151120095245624-1492388740.png