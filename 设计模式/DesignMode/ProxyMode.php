<?php
namespace App\DesignMode;

echo "<pre>";

/**
 * 目标接口
 * Interface Target
 */
interface Subject{
    public function request();
}

/**
 * 实现的目标类
 * Class RealSubject
 */
class RealSubject implements Subject {

    public function request()
    {
        echo "这是真实的目标对象".__CLASS__.PHP_EOL;
    }
}

/**
 * 代理类接口
 * Class ProxySubject
 */
class ProxySubject implements Subject{

    private Subject $subject;

    public function __construct(Subject $subject)
    {
        $this->subject=$subject;
    }

    public function request()
    {
        echo "这是代理类对象".__CLASS__."，下面将执行真正的目标类".PHP_EOL;
        $this->subject->request();
    }
}

//Client
$realSubject = new RealSubject();
$proxySubject = new ProxySubject($realSubject);
$proxySubject->request();