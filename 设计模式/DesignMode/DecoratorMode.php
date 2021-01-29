<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 装饰者模式
 * Interface Component.
 */
interface Component
{
    public function operation();
}

/**
 * Component接口的实现类
 * Class ConcreteComponent.
 */
class ConcreteComponent implements Component
{
    public function operation()
    {
        echo '这是Component接口的实现类'.__CLASS__."\t";
    }
}

/**
 * 抽象装饰器
 * Class AbstractDecorator
 */
abstract class AbstractDecorator implements Component
{
    protected Component $component;

    public function __construct(Component $component)
    {
        $this->component=$component;
    }
}

/**
 * 具体装饰器类DecoratorA
 * Class DecoratorA
 */
class DecoratorA extends AbstractDecorator
{
    public function operation()
    {
        $this->component->operation();
        echo "这是装饰器".__CLASS__."\t";
    }
}

/**
 * 具体装饰器类DecoratorB
 * Class DecoratorB
 */
class DecoratorB extends AbstractDecorator
{
    public function operation()
    {
        $this->component->operation();
        echo "这是装饰器".__CLASS__;
    }
}

//Client
$concreteComponent = new ConcreteComponent();
$concreteComponent->operation();
echo PHP_EOL;
$concreteComponent = new DecoratorA($concreteComponent);
$concreteComponent->operation();
echo PHP_EOL;
$concreteComponent = new DecoratorB($concreteComponent);
$concreteComponent->operation();
echo PHP_EOL;
