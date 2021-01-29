<?php

namespace App\DesignMode;

echo '<pre>';

/**
 * 建造者模式
 * Class BuilderMode
 * @package App\DesignMode
 */
class Product
{
    private $parts = [];

    public function add(string $part): void
    {
        $this->parts[] = $part;
    }

    public function show(): void
    {
        echo '产品创建'.PHP_EOL;
        foreach ($this->parts as $part) {
            echo $part.PHP_EOL;
        }
    }
}


/**
 * Builder接口
 * Interface Builder
 * @package App\DesignMode
 */
interface Builder
{
    public function buildPartA(): void;
    public function buildPartB(): void;
    public function getResult(): Product;
}

/**
 * Builder接口的具体实现类ConcreteBuilder1
 * Class ConcreteBuilder1
 * @package App\DesignMode
 */
class ConcreteBuilder1 implements Builder{

    private Product $product;

    public function __construct(Product $product)
    {
        $this->product=$product;
    }

    public function buildPartA(): void
    {
        $this->product->add('A1部件');
    }

    public function buildPartB(): void
    {
        $this->product->add('B1部件');
    }

    public function getResult(): Product
    {
        return $this->product;
    }
}

/**
 * Builder接口的具体实现类ConcreteBuilder2
 * Class ConcreteBuilder2
 * @package App\DesignMode
 */
class ConcreteBuilder2 implements Builder{

    private Product $product;

    public function __construct(Product $product)
    {
        $this->product=$product;
    }

    public function buildPartA(): void
    {
        $this->product->add('A2部件');
    }

    public function buildPartB(): void
    {
        $this->product->add('B2部件');
    }

    public function getResult(): Product
    {
        return $this->product;
    }
}

/**
 * 构造者
 * Class BuilderDirector
 * @package App\DesignMode
 */
class BuilderDirector{

    public function construct(Builder $builder): void
    {
        $builder->buildPartA();
        $builder->buildPartB();
    }
}

//Client
$product = new Product();
$director = new BuilderDirector();

$builder1 = new ConcreteBuilder1($product);
$director->construct($builder1);
$builder1->getResult()->show();

echo "********************分隔符**************************".PHP_EOL;

$builder2 = new ConcreteBuilder2($product);
$director->construct($builder2);
$builder2->getResult()->show();




