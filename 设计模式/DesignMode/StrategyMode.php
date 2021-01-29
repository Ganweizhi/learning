<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 策略模式
 * 策略接口Strategy
 * Interface Strategy
 */
interface Strategy{
    public function algorithm();
}

/**
 * 实现类StrategyA
 * Class StrategyA
 */
class StrategyA implements Strategy{

    public function algorithm()
    {
        echo "这是策略模式实现类".__CLASS__.PHP_EOL;
    }
}

/**
 * 实现类StrategyB
 * Class StrategyB
 */
class StrategyB implements Strategy{

    public function algorithm()
    {
        echo "这是策略模式实现类".__CLASS__.PHP_EOL;
    }
}

/**
 * 实现类StrategyC
 * Class StrategyC
 */
class StrategyC implements Strategy{

    public function algorithm()
    {
        echo "这是策略模式实现类".__CLASS__.PHP_EOL;
    }
}

/**
 * 定义算法抽象和实现
 * Class StrategyContext
 */
class StrategyContext{

    private Strategy $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy=$strategy;
    }

    public function algorithm()
    {
        $this->strategy->algorithm();
    }

}

//Client
$strategyA = new StrategyA();
$strategyB = new StrategyB();
$strategyC = new StrategyC();

$strategyContextA = new StrategyContext($strategyA);
$strategyContextB = new StrategyContext($strategyB);
$strategyContextC = new StrategyContext($strategyC);

$strategyContextA->algorithm();
$strategyContextB->algorithm();
$strategyContextC->algorithm();
