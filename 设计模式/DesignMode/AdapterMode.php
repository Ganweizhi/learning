<?php
namespace App\DesignMode;
echo '<pre>';
/**
 * 适配器模式
 * Interface Target.
 */
interface Target
{
    public function Request(): void;
}

/**
 * 适配者类
 * Class Adaptee.
 */
class Adaptee
{
    public function adapteeRequest(): void
    {
        echo '我是被适配者'.__CLASS__.PHP_EOL;
    }
}

/**
 * 适配器类
 * Class Adapter
 */
class Adapter implements Target
{
    private Adaptee $adaptee;

    public function setAdaptee(Adaptee $adaptee): void
    {
        $this->adaptee=$adaptee;
    }

    public function Request(): void
    {
        echo "我是适配器类".__CLASS__."，下面将调用适配者类Adaptee".PHP_EOL;
        $this->adaptee->adapteeRequest();
    }
}

//Client
$adaptee = new Adaptee();
$adapter=new Adapter();
$adapter->setAdaptee($adaptee);
$adapter->Request();


