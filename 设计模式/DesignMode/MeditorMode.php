<?php
namespace App\DesignMode;
echo "<pre>";

/**
 * 中介者抽象类Mediator
 * Class Mediator
 * @package App\DesignMode
 */
abstract class Mediator
{
    abstract public function send(String $message, Colleague $colleague);
}

/**
 * 中介者抽象类Mediator的实现类ConcreteMediator
 * Class ConcreteMediator
 * @package App\DesignMode
 */
class ConcreteMediator extends Mediator
{
    private $colleague1;
    private $colleague2;

    public function send(String $message, Colleague $colleague)
    {
        echo "中介者".__CLASS__."收到".get_class($colleague)."的消息：".$message.PHP_EOL;
        if ($colleague == $this->colleague1) {
            $this->colleague2->Notify($message);
        } else {
            $this->colleague1->Notify($message);
        }
    }

    /**
     * @param mixed $colleague1
     */
    public function setColleague1($colleague1): void
    {
        $this->colleague1 = $colleague1;
    }

    /**
     * @param mixed $colleague2
     */
    public function setColleague2($colleague2): void
    {
        $this->colleague2 = $colleague2;
    }
}


/**
 * Class Colleague
 * @package App\DesignMode
 */
abstract class Colleague
{
    protected Mediator $mediator;

    public function __construct(Mediator $mediator)
    {
        $this->mediator = $mediator;
    }

    abstract public function send(string $message);

    abstract public function notify(string $message);
}

/**
 * Class ConcreteColleague1
 * @package App\DesignMode
 */
class ConcreteColleague1 extends Colleague
{
    public function send(string $message)
    {
        echo __CLASS__."发送消息:".$message.PHP_EOL;
        $this->mediator->send($message, $this);
    }
    public function notify(string $message)
    {
        echo __CLASS__."得到信息：" . $message, PHP_EOL;
    }
}

/**
 * Class ConcreteColleague2
 * @package App\DesignMode
 */
class ConcreteColleague2 extends Colleague
{
    public function send(string $message)
    {
        echo __CLASS__."发送消息:".$message.PHP_EOL;
        $this->mediator->send($message, $this);
    }
    public function notify(string $message)
    {
        echo __CLASS__."得到信息：" . $message.PHP_EOL;
    }
}

//Client
$mediator = new ConcreteMediator();
//相互依赖关系已经完成
$concreteColleague1 = new ConcreteColleague1($mediator);
$concreteColleague2 = new ConcreteColleague2($mediator);

$mediator->setColleague1($concreteColleague1);
$mediator->setColleague2($concreteColleague2);

$concreteColleague1->send("hello world");
$concreteColleague2->send("hello word");
