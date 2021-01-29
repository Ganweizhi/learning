<?php
namespace App\DesignMode;

echo '<pre>';

/**
 * 责任链模式
 * Class Handler.
 */
abstract class Handler
{
    protected Handler $next;

    public function setNext($next): void
    {
        $this->next = $next;
    }

    abstract public function handlerRequest($request);
}

/**
 * Handler实现者HandlerA
 * Class HandlerA.
 */
class HandlerA extends Handler
{
    public function handlerRequest($request)
    {
        if (is_numeric($request)) {
            return "请求参数是数字:\t".$request.PHP_EOL;
        }

        return $this->next->handlerRequest($request);
    }
}

/**
 * Handler实现者HandlerB
 * Class HandlerB.
 */
class HandlerB extends Handler
{
    public function handlerRequest($request)
    {
        if (is_string($request)) {
            return "请求参数是字符串:\t".$request.PHP_EOL;
        }

        return $this->next->handlerRequest($request);
    }
}

/**
 * Handler实现者HandlerC
 * Class HandlerC.
 */
class HandlerC extends Handler
{
    public function handlerRequest($request)
    {
        return "请求参数是其它类型:\t".gettype($request).PHP_EOL;
    }
}

//Client
$handlerA = new HandlerA();
$handlerB = new HandlerB();
$handlerC = new HandlerC();

$handlerA->setNext($handlerB);
$handlerB->setNext($handlerC);

$requests = ['abc', 1234, 56, 'abc', null, new stdClass(), 456];

foreach ($requests as $request) {
    echo $handlerA->handlerRequest($request);
}
