### Composer

composer是php的依赖包管理工具，类似与java的maven和node.js的npm工具，主要通过运行命令行或者修改配置文件composer.json来安装所需要的依赖包，是个比较方便的工具。下面简单总结归纳一下composer常用的命令和重要概念👇

##### require

如果要下载想要的依赖包，则在composer.json里面的require自动列出想要的依赖包👇

```
{
    "require": {
        "monolog/monolog": "1.2.*"
    }
}
```

然后composer install即可下载里面的包

同时这也可以作为一个命令，这个命令的作用是将想要的依赖引入项目，比较快速的一个方法。例子👇

```shell
composer require monolog/monolog
```

##### require-dev

require-dev安装的包主要用于开发或者测试用，这个是额外列出的包。requrie安装包则是正式部署时需要的。用法基本和require一样

```json
{
    "require-dev": {
        "monolog/monolog": "1.2.*"
    }
}
```

然后composer require --no-dev可以跳过dev里面列出的依赖包

##### repositories

这个是仓库镜像源，一般用于配置国内的镜像源来加速下载依赖。

用的最多的命令就是全局配置镜像源👇

```shell
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

或者composer.json里面配置数组方式：

```json
{
    "repositories": [
         {
             "type": "composer",
             "url": "http://packages.foo.com"
         }
    ]
}
```

##### autoload

这个是通过映射命名空间来自动加载文件用的，主要用于正式部署。用这个自动加载功能之后基本上就不需要再去用很多的require_once或include_once来手动加载各种.php文件，需要使用时只需引入一个/vendor/autoload.php文件即可。

在compoer.json配置文件的结构如下：

```json
{
    "autoload": {
        "psr-4":{},
        "psr-0":{},
        "classmap"{},
        "files":{},
    }
}
```

根据里面字段对应的规则引入想要加载的文件即可，最后通过下面命令即可加载对应位置的文件👇

```shell
composer dump-autoload
```

最后只需在php代码的初始化部分中加入这一行即可引入自动加载的文件👇

```php
require 'vendor/autoload.php';
```

##### autoload-dev

autoload-dev自动加载映射，一般多用于测试和开发，用法和autoload一样

可以执行composer dump-autoload --no-dev命令来忽略autoload-dev指定的命名空间；

##### install

在composer.json里面配置好require字段的依赖包

```json
{
    "require": {
        "monolog/monolog": "1.2.*"
    }
}
```

然后执行composer install即可安装对应的依赖包

##### update&remove&search&show

- update 命令用于更新项目里所有的包，或者指定的某些包

- remove用于移除包

- search用于搜索包，这会输出包和详细的描述信息
- show列出当前项目使用到的包的信息

```shell
#更新所有依赖
composer update

#更新指定的包[还可以更新多个或者通过通配符更新对应的版本]
composer update monolog/monolog


#移除对应的依赖
composer remove monolg/monolog

#搜索依赖
composer search monolog

#列出当前项目所用到的包信息
composer show
```

##### dump-autoload

当使用了composer的autoload自动加载功能之后要用这个命令才能使得文件加载进来

```shell
composer dump-autoload
```

最好加上--optimize-autoloader这个参数，这个参数是用来优化自动加载的

##### composer.json

这个文件包含了项目的依赖和其它的一些元数据，这个文件根据自己的需求去配置就好。

##### composer.lock

这是个锁文件。安装完依赖之后，composer会生成一个composer.lock文件，里面包含了安装时的各种确切版本号。后面如果还要composer install将会检查所文件是否存在，如果存在则下载.lock文件里面的版本，如果没有则去.json文件下载里面的版本

