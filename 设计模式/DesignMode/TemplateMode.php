<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 模板方法模式
 * Interface TemplateInterface.
 */
abstract class AbstractTemplate
{
    public function templateMethod()
    {
        $this->operationA();
        $this->operationB();
    }

    abstract public function operationA();

    abstract public function operationB();
}

/**
 * 实现抽象模板类的实现类ConcreteClassA
 * Class ConcreteClassA
 */
class ConcreteClassA extends AbstractTemplate{

    public function operationA()
    {
        echo "这是实现类".__CLASS__."的方法".__METHOD__.PHP_EOL;
    }

    public function operationB()
    {
        echo "这是实现类".__CLASS__."的方法".__METHOD__.PHP_EOL;
    }
}

/**
 * 实现抽象模板类的实现类ConcreteClassB
 * Class ConcreteClassB
 */
class ConcreteClassB extends AbstractTemplate{

    public function operationA()
    {
        echo "这是实现类".__CLASS__."的方法".__METHOD__.PHP_EOL;
    }

    public function operationB()
    {
        echo "这是实现类".__CLASS__."的方法".__METHOD__.PHP_EOL;
    }
}

//Client
$concreteClassA = new ConcreteClassA();
$concreteClassA->templateMethod();
echo "***************分割符********************".PHP_EOL;
$concreteClassB = new ConcreteClassB();
$concreteClassB->templateMethod();