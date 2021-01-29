<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 单例方法模式
 * Class SingleMode
 */
class Singleton
{
    public function request(): void
    {
        echo '这是单例方法模式'.__CLASS__.PHP_EOL;
    }
}

/**
 * 单例工厂
 * Class SingleFactory
 */
class SingleFactory{
    private static $singleton;

    public static function createSingleton(): \Singleton
    {
        if (self::$singleton==null){
            self::$singleton=new Singleton();
        }
        return self::$singleton;
    }

}

//Client
$singleton1 = SingleFactory::createSingleton();
$singleton2 = SingleFactory::createSingleton();
var_dump($singleton1);
var_dump($singleton2);
//最终编号一致，则是同一个对象
