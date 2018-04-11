# php_build
---
**安装PHP：
    
    下载并解压php-7.1.10-nts-Win32-VC14-x64.zip
    
    将php.ini-development或php.ini-production修改为php.ini
    修改extension_dir的路径为php安装路径下的ext
    例：extension_dir = "X:/PHP/ext"
    选择自己需要的插件，只需将extension=xxx.dll前的;去掉即可

设置php为系统服务：
    
    设置php环境变量，在path下添加PHP的目录绝对路径，
    例：X:\PHP;
    打开cmd，执行 php -v 输出php版本号即可

    解压php-service.rar
    将php-service.exe和php-service.xml放入php根目录下，
    打开（管理员模式）CMD，进入到PHP目录下执行，php-service.exe install
    之后打开PHP服务和关闭服务只需输入，net start/stop php即可
    卸载服务为，CMD执行php-service.exe uninstall

安装PHP_Redis扩展：
    
    解压Redis-x64-3.2.100.zip

    打开Windows Service Documentation.docx查看如何设置redis
    如需修改redis配置请修改redis.windows-service.conf或redis.windows.conf，
    具体看以哪个配置文件为启动文件

    将php_redis-3.1.4-7.1-nts-vc14-x64.zip里的php_redis.dll解压至php目录下的ext目录下，
    打开php.ini，添加extension=php_redis.dll
    重启php即可

    用<?php phpinfo(); ?>输出查看redis是否加载成功。
