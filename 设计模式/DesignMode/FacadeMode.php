<?php
namespace App\DesignMode;
echo '<pre>';

/**
 * 子系统1
 * Class SubSystemOne
 * @package App\DesignMode
 */
class SubSystemOne
{
    public function MethodOne()
    {
        echo '子系统方法一', PHP_EOL;
    }
}

/**
 * 子系统2
 * Class SubSystemOne
 * @package App\DesignMode
 */
class SubSystemTwo
{
    public function MethodTwo()
    {
        echo '子系统方法二', PHP_EOL;
    }
}

/**
 * 子系统3
 * Class SubSystemOne
 * @package App\DesignMode
 */
class SubSystemThree
{
    public function MethodThree()
    {
        echo '子系统方法三', PHP_EOL;
    }
}

/**
 * 子系统4
 * Class SubSystemOne
 * @package App\DesignMode
 */
class SubSystemFour
{
    public function MethodFour()
    {
        echo '子系统方法四', PHP_EOL;
    }
}

/**
 * 外观类，将上面的子系统的方法封装起来
 * Class Facade
 * @package App\DesignMode
 */
class Facade
{
    private SubSystemOne $subSystemOne;
    private SubSystemTwo $subSystemTwo;
    private SubSystemThree $subSystemThree;
    private SubSystemFour $subSystemFour;

    public function __construct()
    {
        $this->subSystemOne = new SubSystemOne();
        $this->subSystemTwo = new SubSystemTwo();
        $this->subSystemThree = new SubSystemThree();
        $this->subSystemFour = new SubSystemFour();
    }

    public function MethodA()
    {
        $this->subSystemOne->MethodOne();
        $this->subSystemTwo->MethodTwo();
    }
    public function MethodB()
    {
        $this->subSystemOne->MethodOne();
        $this->subSystemTwo->MethodTwo();
        $this->subSystemThree->MethodThree();
        $this->subSystemFour->MethodFour();
    }
}

//Client
$facade = new Facade();
$facade->MethodA();
$facade->MethodB();