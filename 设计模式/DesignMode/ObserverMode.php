<?php
namespace App\DesignMode;

echo "<pre>";
/**
 * 观察者模式
 * Interface Observer.
 */
interface Observer
{
    public function update(ConcreteObserverSubject $obj);
}

/**
 * 观察者接口的实现类ConcreteObserver
 * Class ConcreteObserver.
 */
class ConcreteObserver implements Observer
{
    public function update(ConcreteObserverSubject $obj)
    {
        echo '观察者'.__CLASS__."收到通知，收到被观察者的内容是{$obj->getState()}".PHP_EOL;
    }
}

/**
 * 被观察的对象
 * Class ObserverSubject.
 */
class ObserverSubject
{
    protected $observers = [];

    protected $state = '';

    public function add(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function delete(Observer $observer): void
    {
        $position = 0;
        foreach ($this->observers as $obs) {
            if ($observer === $obs) {
                array_splice($this->observers, $position, 1);
            }
            ++$position;
        }
    }

    public function notify()
    {
        echo "被观察的对象".__CLASS__."发出通知,现在内容是".$this->state.PHP_EOL;
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}

/**
 * 继承ObserverSubject的子类
 * Class ConcreteObserverSubject.
 */
class ConcreteObserverSubject extends ObserverSubject
{
    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}

//Client
$subject = new ConcreteObserverSubject();
$subject->setState("gwz");
$observer = new ConcreteObserver();
$subject->add($observer);
echo "开始观察者模式：".PHP_EOL;
$subject->notify();
