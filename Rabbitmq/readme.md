### Rabbitmq

比较常用的一个消息队列，👇下面简单总结一下常用概念，如更多用法和高级操作一般都是参考比较权威的书籍



#### 1.Producer & Consumer

Producer就是消息生产者，用来生成消息的；Consumer就是消息消费者。



#### 2.Queue

消息队列，提供了FIFO的处理机制，具有缓存消息的能力。rabbitmq中队列消息可以设置为持久化，临时或者自动删除。

1. 设置为持久化的队列，queue中的消息会在本地硬盘存储一份，防止系统重启后数据丢失
2. 设置为临时队列，queue中的数据在系统重启之后就会丢失
3. 设置为自动删除(auto-delete)的队列，当不存在用户连接到server，队列中的数据会被自动删除

#### 3.Exchange

Exchange类似于数据通信网络中的交换机，提供消息路由策略。rabbitmq中producer不是通过信道直接将消息发送给queue，而是先t通过channel发送给Exchange。一个Exchange可以和多个Queue进行绑定，producer在传递消息的时候，会传递一个ROUTING_KEY，Exchange会根据这个ROUTING_KEY按照设置好的特定路由算法【常用有四种】，将消息路由给指定的queue，最后consumer通过channel去消费queue中的消息数据。和Queue一样，Exchange也可设置为持久化，临时或者自动删除。

Exchange有4种类型：direct(默认)，fanout, topic, 和headers，不同类型的Exchange转发消息的策略有所区别：

1. Direct
   直接交换器，工作方式类似于单播，Exchange会将消息发送完全匹配ROUTING_KEY的Queue
2. fanout
   广播是式交换器，不管消息的ROUTING_KEY设置为什么，Exchange都会将消息转发给所有绑定的Queue。
3. topic
   主题交换器，工作方式类似于组播，Exchange会将消息转发和ROUTING_KEY匹配模式相同的所有队列，比如，ROUTING_KEY为user.stock的Message会转发给绑定匹配模式为 * .stock,user.stock， * . * 和#.user.stock.#的队列。（ * 表是匹配一个任意词组【必须一个，多了少了都不行】，#表示匹配0个或多个词组）
4. headers
   消息体的header匹配（ignore）

#### 4.Channel

通道，消费者与生产者通过tcp连接到vhost之后会生成若干channel通道，主要用来传递消息



#### 5.Binding

绑定就是将一个特定的 Exchange 和一个特定的 Queue 绑定起来。Exchange 和Queue的绑定可以是多对多的关系。



#### 6.virtual host

在rabbitmq server上可以创建多个虚拟的broker，叫做virtual hosts 。每一个vhost本质上是一个mini-rabbitmq server，分别管理各自的exchange，和bindings。vhost相当于物理的server，可以为不同应用提供边界隔离，使得应用安全的运行在不同的vhost实例上，相互之间不会干扰【可以用linux和docker之间的关系来比喻。server就像是linux，vhost就像是docker容器，它们相互隔离而且运行在linux之上】。producer和consumer连接rabbit server需要指定一个vhost。默认的vhost默认是/guest



#### 7.ack/confirm

在总结这两个概念之前我先简单归纳一下通信过程rabbitmq的通信过程👇：

假设P1和C1注册了相同的Broker，Exchange和Queue。P1发送的消息最终会被C1消费。基本的通信流程大概如下所示：

1. P1生产消息，通过Channel发送给服务器端的Exchange
2. Exchange收到消息，根据消息中的ROUTING_KEY，将消息转发给匹配的Queue
3. Queue收到消息，通过Channel将消息发送给订阅者C1
4. C1收到消息，发送ACK给队列Queue确认收到消息
5. Queue收到ACK，删除队列中缓存的此条消息

下面就简单说一下ack确认机制的过程👇：

Consumer收到消息时需要显式的向rabbit broker发送basic.ack消息或者consumer订阅消息时设置auto_ack参数为true【自动确认】。队列Queue收到确认ack之后就会删除队列中对应的消息。在通信过程中，队列Queue对ACK的处理有以下几种情况：

1. 如果consumer接收了消息，发送ack，rabbitmq会删除队列中这个消息，然后发送另一条消息给consumer。
2. 如果consumer接受了消息, 但在发送ack之前断开连接，rabbitmq会认为这条消息没有被deliver，在consumer再次连接的时候，这条消息会被redeliver重新投递。
3. 如果consumer接受了消息，但是程序中有bug，忘记了ack，rabbitmq不会重复发送消息，断开连接之后重连才会再次重新投递。
4. rabbitmq2.0.0和之后的版本支持consumer reject拒绝某条（类）消息，可以通过设置requeue参数中的reject为true达到目的，那么rabbitmq将会把消息发送给下一个注册的consumer。