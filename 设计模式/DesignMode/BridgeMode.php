<?php
namespace App\DesignMode;
echo '<pre>';

/**
 * 桥接模式，将实现部分与抽象部分分离出来
 *
 * Interface implementor
 * @package App\DesignMode
 */
interface implementor
{
    public function operation();
}

/**
 * implementor接口的实现类ConcreteImplementor1
 * Class ConcreteImplementor1
 * @package App\DesignMode
 */
class ConcreteImplementor1 implements implementor
{

    public function operation()
    {
        echo "这是具体实现类".__CLASS__.PHP_EOL;
    }
}

/**
 * implementor接口的实现类ConcreteImplementor2
 * Class ConcreteImplementor2
 * @package App\DesignMode
 */
class ConcreteImplementor2 implements implementor
{

    public function operation()
    {
        echo "这是具体实现类".__CLASS__.PHP_EOL;
    }
}

/**
 * Class Abstraction
 * @package App\DesignMode
 */
abstract class Abstraction
{
    protected $impl;

    public function __construct(implementor $impl)
    {
        $this->impl=$impl;
    }

    abstract public function realOperation();
}

/**
 * Class ConcreteClass
 * @package App\DesignMode
 */
class ConcreteClass extends Abstraction
{

    public function realOperation()
    {
        $this->impl->operation();
    }
}

//Client[实现了父类与子类的抽象与实现的分离]
$implementor1 = new ConcreteImplementor1();
$implementor2 = new ConcreteImplementor2();

$concreteClass1 = new ConcreteClass($implementor1);
$concreteClass1->realOperation();

$concreteClass2 = new ConcreteClass($implementor2);
$concreteClass2->realOperation();

