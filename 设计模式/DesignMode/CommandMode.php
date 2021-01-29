<?php

namespace App\DesignMode;
echo '<pre>';

/**
 * 命令模式
 * Class Invoker
 * @package App\DesignMode
 */
class Invoker
{
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function exec(): void
    {
        echo "命令调用者" . __CLASS__ . "执行命令" . PHP_EOL;
        $this->command->execute();
    }

    /**
     * @param Command $command
     */
    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }
}

/**
 * 命令抽象类
 * Class Command
 * @package App\DesignMode
 */
abstract class Command
{
    protected Receiver $receiver;

    public function __construct(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver): void
    {
        $this->receiver = $receiver;
    }

    abstract public function execute(): void;
}


/**
 * 命令抽象类的具体实现类ConcreteCommand
 * Class ConcreteCommand
 * @package App\DesignMode
 */
class ConcreteCommand extends Command
{
    public function execute(): void
    {
        $this->receiver->action();
    }
}

/**
 * 命令接受者
 * Class Receiver
 * @package App\DesignMode
 */
class Receiver
{
    public string $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function action(): void
    {
        echo $this->name . '使得命令实现了！', PHP_EOL;
    }
}

//Client
//准备执行者
$receiver = new Receiver('命令接收者A');
//准备命令
$command = new ConcreteCommand($receiver);
//准备调用者
$invoker = new Invoker($command);

$invoker->exec();
