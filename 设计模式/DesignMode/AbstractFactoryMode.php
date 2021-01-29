<?php
namespace App\DesignMode;
echo '<pre>';

/**
 * 抽象工厂接口AbstractFactory
 * Interface AbstractFactory
 * @package App\DesignMode
 */
interface AbstractFactory{

    public function createProductA();
    public function createProductB();
}

/**
 * 抽象工厂接口AbstractFactory的实现类
 * Class ConcreteAbstractFactory1
 * @package App\DesignMode
 */
class ConcreteAbstractFactory1 implements AbstractFactory{

    public function createProductA()
    {
        return new ProductA1();
    }

    public function createProductB()
    {
        return new ProductB1();
    }
}

/**
 * 抽象工厂接口AbstractFactory的实现类
 * Class ConcreteAbstractFactory2
 * @package App\DesignMode
 */
class ConcreteAbstractFactory2 implements AbstractFactory{

    public function createProductA()
    {
        return new ProductA2();
    }

    public function createProductB()
    {
        return new ProductB2();
    }
}

/**
 * A产品接口
 * Interface AbstractProductA
 * @package App\DesignMode
 */
interface AbstractProductA
{
    public function showA();
}

/**
 * 实现类产品A1
 * Class ProductA1
 * @package App\DesignMode
 */
class ProductA1 implements AbstractProductA
{

    public function showA()
    {
        echo "这是产品A1".PHP_EOL;
    }
}

/**
 * 实现类产品A2
 * Class ProductA2
 * @package App\DesignMode
 */
class ProductA2 implements AbstractProductA
{

    public function showA()
    {
        echo "这是产品A2".PHP_EOL;
    }
}



/**
 * B产品接口
 * Interface AbstractProductB
 * @package App\DesignMode
 */
interface AbstractProductB
{
    public function showB();
}

/**
 * 实现类产品B1
 * Class ProductB1
 * @package App\DesignMode
 */
class ProductB1 implements AbstractProductB
{

    public function showB()
    {
        echo "这是产品B1".PHP_EOL;
    }
}

/**
 * 实现类产品B2
 * Class ProductB2
 * @package App\DesignMode
 */
class ProductB2 implements AbstractProductB
{

    public function showB()
    {
        echo "这是产品B2".PHP_EOL;
    }
}

//Client
$factory1 = new ConcreteAbstractFactory1();
$factory2 = new ConcreteAbstractFactory2();

$productA1 = $factory1->createProductA();
$productB1 = $factory1->createProductB();
$productA2 = $factory2->createProductA();
$productB2 = $factory2->createProductB();

$productA1->showA();
$productA2->showA();
$productB1->showB();
$productB2->showB();

