<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 简单工厂模式
 * Class FactoryMode.
 */
class Factory
{
    public static function createProduct(string $type): Product
    {
        $product = null;

        switch ($type) {
            case 'A':
                $product = new ProductA();
                break;
            case 'B':
                $product = new ProductB();
                break;
        }
        return $product;
    }
}

/**
 * 产品接口
 * Interface Product.
 */
interface Product
{
    public function show();
}

/**
 * 实现类产品A
 * Class ProductA.
 */
class ProductA implements Product
{
    public function show()
    {
        echo '这是产品A'.PHP_EOL;
    }
}

/**
 * 实现类产品B
 * Class ProductB.
 */
class ProductB implements Product
{
    public function show()
    {
        echo '这是产品B'.PHP_EOL;
    }
}

//Client
$productA = Factory::createProduct('A');
$productA->show();
$productB = Factory::createProduct('B');
$productB->show();

