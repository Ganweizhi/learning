### Hyperf

下面的内容具体总结一下每个部分的注意事项和我对一些坑的记录处理，排版的不太好，后面会慢慢整理好，而不会长篇幅地写代码。



###### 1.

ERROR是完全终止的，代码环境自身的问题；Exception是逻辑错误，一般都是手动抛出throw Exception才可见的
那三种可以恢复的错误用set_error_handle函数处理，致命错误Fatal_error用try{}catch(Error $e){}捕获
异常可以用set_exception_handle()函数处理或者用try{}catch(Exception $e){}捕获

###### 2.

foreach循环中是把数组中元素的地址指针传给临时变量，使得数组指针和临时变量指向同一个地址，临时变量的修改也会导致数组元素的变化

###### 3.

PSR7接口Psr\Http\Message\ServerRequestInterface继承了PSR7的接口RequestInterface。
Hyperf中的接口Hyperf\HttpServer\Contract\RequestInterface继承了接口Psr\Http\Message\ServerRequestInterface。
**类Hyperf\HttpServer\Request实现了接口Hyperf\HttpServer\Contract\RequestInterface**。

**而请求类Hyperf\HttpServer\Request是来自协程上下文中的Context::get(ServerRequestInterface::class)。每个Hyperf\HttpServer\Contract\RequestInterface对应每个Hyperf\HttpServer\Request代理对象，每个代理对象都是从协程上下文中获取PSR-7 Request请求对象【即每个代理对象代理的是每个对应的PSR-7 Request对象】**

###### 4.

在phpstorm的终端使用php bin/hyperf.php start不起效，因为主机的php版本和docker里的php版本不一致，只能去docker容器里面去启动，
基本那些gen:model的命令也都是只能在容器中启动，phpstorm中的终端命令不起作用

###### 5.

要在window环境下让hyperf容器和mysql容器网络连接只能创建一个共同的docker network【但是用docker-compose也可以的，docker-compose下的编排容器会在同一个默认网络network下，因此可以用容器别名访问容器】

###### 6.一个User有多个Role【使用这个关系时要先在数据库表中建好表的关系】

```php
class User extends Model
{
    public function role()
    {
        return $this->hasMany(Role::class, 'user_id', 'id');
    }
}
```


那个hasMany的意思是Role类中有外键user_id，参照User类的主键。【Role类中有外键user_id对应User类的属性id】hasOne中也是一样的

```php
Role.php
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
后面两个参数和hasMany一致即可
```



###### 7.

协程内部禁止使用全局变量，以免发生数据错乱；
协程使用 use 关键字引入外部变量到当前作用域禁止使用引用，以免发生数据错乱；
不能使用类静态变量 Class::$array / 全局变量 $_array / 全局对象属性 $object->array / 其他超全局变量 $GLOBALS 等保存协程上下文内容，以免发生数据错乱；
协程之间通讯必须使用通道（Channel）；
不能在多个协程间共用一个客户端连接，以免发生数据错乱；可以使用连接池实现；
在 Swoole\Server 中，客户端连接应当在 onWorkerStart 中创建；
在 Swoole\Process 中，客户端连接应当在 Swoole\Process->start 后，子进程的回调函数中创建；
必须在协程内捕获异常，不得跨协程捕获异常；
在 __get / __set 魔术方法中不能有协程切换。

###### 8.

异步非阻塞操作就是遇到阻塞时不会阻塞，而是继续往下执行或者执行定义好的回调函数，而本应阻塞得到的结果result在后面获得即可，有些异步操作可以让结果在自己想要的地方返回。yield+generator也是属于异步非阻塞操作，比如当代码执行到foreach循环有yield的地方时就会退出循环生成一个generator，
继续执行下面的代码，然后在后面的代码需要使用该生成器才会返回结果，因此属于异步非阻塞操作

###### 9.

传统的PHP-FPM模式（一个CGI请求对应一个worker进程）与Hyperf的Swoole协程模式（一个worker进程处理多个HTTP请求，每个请求对应一个协程）不一样，记住。
Swoole的每个work进程（其实这里的一个进程可以看作就是一个线程，因为swoole就是单进程的）内有多个协程，但只有一个协程工作（并发方式，遇到阻塞时进程内的协程调度器使该协程让出使用，切换其他协程）。而且记住一个进程（可以看作线程）内的**全局变量**会被全部协程共享，可能造成数据混乱，也就意味着如果使用了**全局变量**来储存状态可能会被多个协程所使用，也就是说不同的请求之间可能会混淆数据，这里的**全局变量**指的是 `$_GET/$_POST/$_REQUEST/$_SESSION/$_COOKIE/$_SERVER`等`$_`开头的变量、`global` 变量，以及 `static` 静态属性。如果不想用全局变量的话那就用协程上下文Context来保存数据，每个协程都可以有各自的上下文数据，随着请求结束（就是协程结束）而自动销毁。

##### 10.常用命令汇总

###### 查看全部命令

**php bin/hyperf.php**

###### 启动Hyperf应用程序

**php bin/hyperf.php start**

###### 创建中间件

**php bin/hyperf.php gen:middleware TokenCheckMiddleware**

###### 创建控制器

**php bin/hyperf.php gen:controller Photo/PhotoController**

###### 创建模型

**php bin/hyperf.php gen:model model**

###### 选择连接池创建模型

**php bin/hyperf.php gen:model user --pool=mysql_ucenter**

###### 消息队列创建生产者

**php bin/hyperf.php gen:amqp-producer DemoProducer**

###### 消息队列创建消费者

**php bin/hyperf.php gen:amqp-consumer DemoConsumer**



**11**

有些时候我们可能希望去实现一些更动态的需求时，会希望可以直接获取到 `容器(Container)` 对象，在绝大部分情况下，框架的入口类（比如命令类、控制器、RPC 服务提供者等）都是由 `容器(Container)` 创建并维护的，也就意味着您所写的绝大部分业务代码都是在 `容器(Container)` 的管理作用之下的，也就意味着在绝大部分情况下您都可以通过在 `构造函数(Constructor)` 声明或通过 `@Inject` 注解注入 `Psr\Container\ContainerInterface` 接口类都能够获得 `Hyperf\Di\Container` 容器对象，

###### 12.

对部分组件或客户端进行协程化处理是为了能够在将阻塞转化为异步非阻塞从而能够进行协程切换处理



###### 13.

Task组件主要是用来处理无法协程化的方法函数，而且Task进程是一个进程，其实Task就是将阻塞的函数包在里面，然后多个Task进程运行，遇到阻塞时就切换Task进程，类似达到进程内的协程切换效果，但进程要切换会导致性能上不如协程



###### 14.

Hyperf框架中被容器管理的对象都是单例的，比如在Controller中的一个属性被修改了，后面的请求获取到这个Controller的属性都是最新的而不是未初始化的（Controller是默认被DI容器管理的）



###### 15.

HyperfTest\HttpTestCase继承了原生的PHPUnit\Framework\TestCase，但是封装好了一些方法和属性，因此直接继承他来使用即可



###### 16.

elasticsearch-php中的的未来模式Future其实就是懒加载异步模式，只有在真正要使用返回结果的时候才会去解析这个Future对象，但在解析整个批次请求的时候会发生阻塞，直到结果返回。如果是一个Future数组，那只要解析里面其中一个，其它都会发生解析，之后获取其他Future的响应结果的时候无需等待

![image-20210112105434852](.\pictures\image-20210112105434852.png)



###### 17.

Hyperf里面的注解

/**

*

*/

头和尾不能有任何东西，要写的的东西都在中间写，否则注解不生效



###### 18.

使用模型的save方法时默认会自动插入created_at和updated_at两个字段的值，因此模型对应的表中要有这两个字段才行，不然凡是涉及到修改的行为都会报错或者不生效

###### 19.

模型中save是对于一个模型实例而言的，只要这个模型里面的属性已经填好，那就可以直接save保存到数据库中

destory是静态方法，通过主键能直接删除模型数据

需要增强gen:model命令的脚本在/config/autoload/database.php中添加visitors字段即可，得到的模型字段和属性更多



###### 20.

在一对多那些模型关系中，得到的关联对象在phpstorm中使用是不会出现提示的，不要认为没有提示就是错的



###### 21.

在模型类中只要    use Searchable 就能将mysql中的模型数据同步到elasticsearch，elasticsearch索引名和mysql表名会一致



###### 22。

Hyperf中的redis实际就是原生\Redis的代理类，但是加了协程处理；Redis异步队列则是把Redis中间件作为异步队列，一头生产driver->push另一头消费handle



###### 23.

多对多关系的时候两边对象都要写belongsToMany才能获取到关联的对象们



###### 24.

Router:get和Router::addRoutes这些都是在配置文件config/routes.php中添加







#### Coroutine协程

协程，类似于线程，但是和线程不一样的是线程底层是由操作系统控制调度的，而协程可以在代码层面控制。传统的PHP-FPM模式（一个CGI请求对应一个worker进程）与Hyperf的Swoole协程模式（一个worker进程处理多个HTTP请求，每个请求对应一个协程）不一样，这一点要记住。
Swoole的每个work进程（其实这里的一个进程可以看作就是一个线程，因为swoole就是单进程的）内有多个协程，但只有一个协程工作（并发方式，遇到阻塞时进程内的协程调度器使该协程让出使用，切换其他协程）。而且记住一个进程（可以看作线程）内的**全局变量**会被全部协程共享，可能造成数据混乱，也就意味着如果使用了**全局变量**来储存状态可能会被多个协程所使用，也就是说不同的请求之间可能会混淆数据，这里的**全局变量**指的是 `$_GET/$_POST/$_REQUEST/$_SESSION/$_COOKIE/$_SERVER`等`$_`开头的变量、`global` 变量，以及 `static` 静态属性。如果不想用全局变量的话那就用协程上下文Context来保存数据，每个协程都可以有各自的上下文数据，随着请求结束（就是协程结束）而自动销毁。

#### Middleware中间件

中间件，也是很重要的一个组件，影响了请求到响应的整个流程

- ##### 在中间件配置文件定义全局中间件

在配置文件**config/autoload/middlewares.php**中定义中间件，这时配出来的是全局中间件【该server下的所有请求都会进入这个中间件】

- ##### 在路由配置文件配置配置局部中间件

在定义路由配置文件config/route.php定义路由时，在定义路由的方法的最后一个参数 `$options` 都将接收一个数组，可通过传递键值 `middleware` 及一个数组值来定义该路由的中间件

- ##### 通过注解定义局部中间件

@Middleware和@Middlewares都可以注解在类上面和方法上面

- **执行顺序为 全局——>类级别——>方法级别**

- ##### 要实现中间件就实现MiddlewareInterface接口



#### 路由

- ##### 配置文件定义路由：在配置文件config/routes.php中定义路由：

  ##### 1.注册与方法名一致的 HTTP METHOD 的路由 

  Router::get($uri, $callback); 

  Router::post($uri, $callback); 

  Router::put($uri, $callback); 

  Router::patch($uri, $callback); 

  Router::delete($uri, $callback); 

  Router::head($uri, $callback); 

  ##### 2.注册任意 HTTP METHOD 的路由 

  Router::addRoute($httpMethod, $uri, $callback);

##### 		3.路由组的方式

![image-20210204114940211](.\pictures\image-20210204114940211.png)

- ##### 注解定义路由【一般推荐这个】

  1.用在类上上面的有@AutoController和@Controller

  2.用在方法上的一般是@RequestMapping【一般@Controller和@RequestMapping搭配使用】





#### 异常处理

一旦业务流程存在没有捕获的异常，都会被传递到已注册的 `异常处理器(ExceptionHandler)` 去处理。**目前仅支持配置文件形式注册的异常处理器**，步骤如下：

- 定义一个 `类(Class)` 并继承抽象类 ` Hyperf\ExceptionHandler\ExceptionHandler` 并实现其中的抽象方法
- 配置文件位于 `config/autoload/exceptions.php`，将自定义异常处理器配置在对应的 `server`位置。

注意：**若自己配置的异常处理器仍不对该异常进行捕获处理【就是throw之后并没有catch处理】，那么就会交由 Hyperf 的默认异常处理器处理了**



#### 监听事件机制

- 事件Event是指一个类的对象，对象内存有具体状态数据，可被多个监听器监听。
- 监听器Listener监听事件Event的发生，然后做出逻辑回应【定义一个监听器就是实现ListernerInterface接口】，定义完监听器之后要注册这个监听器，因为要被事件调度器EventDispatcher发现，有两种注册的方法👇
  - config/autoload/listeners.php配置文件中注册
  - 在监听器类上面加@Listener注解即可【推荐这种】
- 事件需要由事件调度器EventDispatcher发送才能让监听器Listener监听到。这个EventDispatcher实际是注入了EventDispatcherInterface接口的代理对象，然后调用代理对象的dispatcher($event)方法就可以发送事件



#### 自动化测试【注意运行测试脚本要进入容器运行，不能在容器以外的地方运行包括挂载了对应目录的phpstorm】

hyperf下的测试使用过phpunit.xml脚本来实现的，这个类Hyperf\Testing\Client可以在不启动Server的情况下模拟HTTP请求。

- 主要还是要记住phpunit的用法和常用api；
- 测试类以Test结尾，里面的测试方法以test开头
- 继承TestCase和HttpTestCase类
- 使用测试替身【一个用来代替多个依赖问题的替身】



#### 数据库模型

查询构造器最后一般都是要加->get()方法



#### guzzle客户端

- 简单易用的一个发送请求的客户端，上手一次基本就知道如何使用，官方文档如下👇

https://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html

- 注意：获得结果之后要使用方法getBody()->getContents()，不然会获取不到里面的消息内容



