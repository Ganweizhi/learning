# Docker&Docker-compose

Docker比较重要，基本所有的第三方服务软件比如mysql，redis，rabbitmq，elasticsearch，nginx还有很多都可以通过docker直接完成，很方便。容器直接的通信问题与解决方法如下👇：

- 但是如果是使用docker命令一个一个启动所需要的容器服务的时候一定要记得用docker network connect将它们塞进同一个网络下，不然容器与容器统计无法通信，因为docker启动的容器是相互隔离的；
- 如果要更方便地解决这种问题，我认为直接使用docker-compose脚本方便得多，因为docker-compose里面可以定义很多服务，然后一旦使用docker-compose up启动时会默认生成一个名字为default的docker网络，所以里面的容器就可以相互通信访问。
- 又或者使用手动的方法，通过`docker inspect 容器名`字可以查看得到该容器的ip地址，然后其他容器通过这个ip地址就可以访问到它。但是不推荐这种方法，因此每次生成新的容器时ip都是动态的。

先总结一下我平时使用最多的docker命令👇：【一般来说容器名或者容器ID是互通】

```dockerfile
docker info 		#输出环境信息
docker --version 	#输出版本信息
docker inspect 容器名 #输出该容器所有的详细信息

#容器
docker ps			#输出所有正在运行的容器信息
docker ps -a		#输出所有的容器【正在运行和没有运行的】
docker ps -l		#输出最新启动的容器信息
docker los 容器名    #查看容器日志信息

#docker网络
docker network create 网络名   #创建网络
docker network ls 			  #列出所有的网络
docker inspect 网络名          #打印该网络的详细信息
docker network connect 网络名 容器名 #将容器加入到网络中
docker network disconnect 网络名 容器名 #将容器移出网络
docker network rm 网络名				#删除网络

docker images		#列出所有镜像
docker pull	镜像名称  #拉取镜像
docker push 仓库名	   #推送到仓库，一般是私服的仓库
docker rmi 镜像名	   #删除镜像
docker exec -it 容器名 /bin/bash  #进入容器

#docker运行命令
docker stop 容器名		#停止容器
docker start 容器名	#启动被停止的容器
docker restart 容器名  #重启了容器
docker rm -f 容器名		  #强制删除容器，包括在运行的
docker run -d		     		       #放到后台运行
		   -p 宿主机端口:容器端口	      #设置映射端口
		   -v 宿主机目录:容器目录		  #数据卷挂载
		   --hostname 容器主机名			 #设置容器主机名字,这个可以作为被其他容器进行通信的名字
		   -e 容器的配置项名字			   #设置启动后的容器配置环境
		   --name 容器名字				 #设置容器的名称
		   --network 网络名			 #指定网络
		   -it /bin/bash				#启动后进入容器		 

# 重启docker服务
net stop com.docker.service
net start com.docker.service
```

下面是我自己整理的常用的docker命令或docker-compose脚本，这些都是我自己成功搭建运行过的命令，因此应该是无错误的👇【记住若采用docker命令模式时一定要把需要通信的容器放入同一个网络下才可以，不然报错】：

## Mysql:

- docker命令

```shell
docker run -d --name mysql \
-e MYSQL_ROOT_PASSWORD=root \
-e MYSQL_USER=gwz \
-e MYSQL_PASSWORD=gwz \
-v ./data:/var/lib/mysql \
-p 3305:3306 -d mysql:8.0.15 \
--default-authentication-plugin=mysql_native_password \
--character-set-server=utf8mb4 \
--collation-server=utf8mb4_unicode_ci
```

- docker-compose脚本:

```shell
version: '3.1'
services:
  database:
    image: mysql
    container_name: mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
    command:
      --default-authentication-plugin=mysql_native_password
      --character-set-server=utf8mb4
      --collation-server=utf8mb4_general_ci
      --explicit_defaults_for_timestamp=true
      --lower_case_table_names=1
    ports:
      - 3306:3306
    volumes:
      - ./data:/var/lib/mysql

```



## Redis

### 单机版

- docker命令

```
docker run -p 6379:6379 -v ./data:/data \
--name redis -d redis:5.0.8 redis-server \
--appendonly yes
```

## 集群版+哨兵集群

- docker-compose的redis集群脚本

```shell
version: '3.1'
services:
  master:
    image: redis:5.0.7
    container_name: redis-master
    network_mode: "host"
    ports:
      - 6379
    volumes:
      - ./dataMaster:/data
    restart: always
    command: redis-server --port 6379

  slave1:
    image: redis:5.0.7
    container_name: redis-slave-1
    network_mode: "host"
    ports:
      - 6380
    volumes:
      - ./dataSlave1:/data
    restart: always
    command: redis-server --port 6380 --slaveof [master所在主机ip] 6379

  slave2:
    image: redis:5.0.7
    container_name: redis-slave-2
    network_mode: "host"
    ports:
      - 6381
    volumes:
      - ./dataSlave2:/data
    restart: always
    command: redis-server --port 6381 --slaveof [master所在主机ip] 6379
```

上面[master所在主机ip]根据实际填写即可

- sentinel集群脚本【我使用了主机模式】

```yaml
version: '3.1'
services:
  sentinel1:
    image: redis:5.0.7
    container_name: redis-sentinel-1
    command: redis-sentinel /usr/local/etc/redis/sentinel.conf
    restart: always
    network_mode: "host"
    ports:
      - 26379
    volumes:
      - ./sentinel1.conf:/usr/local/etc/redis/sentinel.conf
 
  sentinel2:
    image: redis:5.0.7
    container_name: redis-sentinel-2
    command: redis-sentinel /usr/local/etc/redis/sentinel.conf
    restart: always
    network_mode: "host"
    ports:
      - 26380
    volumes:
      - ./sentinel2.conf:/usr/local/etc/redis/sentinel.conf
 
  sentinel3:
    image: redis:5.0.7
    container_name: redis-sentinel-3
    command: redis-sentinel /usr/local/etc/redis/sentinel.conf
    restart: always
    network_mode: "host"
    ports:
      - 26381
    volumes:
      - ./sentinel3.conf:/usr/local/etc/redis/sentinel.conf
```

上面的sentinel.conf的模板文件如下👇，需要根据自己实际修改port和[master-ip]，然后改成sentinel1.conf、sentinel2.conf、sentinel3.conf的配置文件：

```shell
port 26379
dir /tmp
# 自定义集群名，其中[master-ip]为 redis-master 的 ip，6379 为 redis-master 的端口，2 为最小投票数（因为有 3 台 Sentinel 所以可以设置成 2）
sentinel monitor mymaster [master-ip] 6379 2
sentinel down-after-milliseconds mymaster 30000
sentinel parallel-syncs mymaster 1
sentinel failover-timeout mymaster 180000
sentinel deny-scripts-reconfig yes
```



## Elasticsearch

- docker命令

```shell
docker run --name elasticsearch -p 9200:9200 -p 9300:9300 \
-e "discovery.type=single-node" \
-e ES-JAVA-OPTS="-Xms64m -Xmx128m" \
-v ./config/elasticsearch.yml:/usr/share/elasticsearch/config/elasticsearch.yml \
-v ./elasticsearch/data:/usr/share/elasticsearch/data \
-v ./elasticsearch/plugins:/usr/share/elasticsearch/plugins \
-d elasticsearch:7.4.2

```

## Kibana

- docker命令

```shell
docker run -d --name kibana -e ELASTICSEARCH_HOSTS=http://elasticsearch:9200 -p 5699:5601 kibana:7.4.2
```

## rabbit

### 单机版

- docker命令

```shell
docker run -d --name rabbitmq -e RABBITMQ_DEFAULT_USER=admin -e RABBITMQ_DEFAULT_PASS=admin \
-p 5672:5672 -p 15672:15672 -v \
./rabbbitmq/etc:/etc/rabbitmq -v \
./rabbbitmq/log:/var/log/rabbitmq \
rabbitmq:management
```

1.启动完成后要使用**docker exec -it rabbitmq /bin/bash**命令进入容器

2.然后输入**rabbitmq-plugins enable rabbitmq_management**才能开启UI界面

- docker-compose脚本：

```yaml
version: '3.1'
services:
  rabbitmq:
    restart: always
    image: rabbitmq:management
    container_name: rabbitmq
    ports:
      - 5672:5672
      - 15672:15672
    environment:
      TZ: Asia/Shanghai
      RABBITMQ_DEFAULT_USER: rabbit
      RABBITMQ_DEFAULT_PASS: rabbit
    volumes:
      - ./data:/var/lib/rabbitmq
```

## 集群版+haproxy负载均衡

- docker命令

```shell
#节点1
docker run -d --hostname my-rabbit1 --name rabbit1 -p 5672:5672 -p 15672:15672 -e RABBITMQ_ERLANG_COOKIE='rabbitcookie' rabbitmq:management

#节点2
docker run -d --hostname my-rabbit2 --name rabbit2 -p 5673:5672 -p 15673:15672 -e RABBITMQ_ERLANG_COOKIE='rabbitcookie' --link rabbit1:my-rabbit1 rabbitmq:management

#节点3
docker run -d --hostname my-rabbit3 --name rabbit3 -p 5674:5672 -p 15674:15672 -e RABBITMQ_ERLANG_COOKIE='rabbitcookie' --link rabbit1:my-rabbit1 --link rabbit2:my-rabbit2 rabbitmq:management
```

- 启动完成后需要分别进入这三个容器并输入👇：

```shell
#进入节点1
docker exec -it rabbit1 bash
rabbitmqctl stop_app
rabbitmqctl reset
rabbitmqctl start_app
exit

#进入节点2
docker exec -it rabbit2 bash
rabbitmqctl stop_app
rabbitmqctl reset
rabbitmqctl join_cluster --ram rabbit@my-rabbit1
rabbitmqctl start_app
exit

#进入节点3
docker exec -it rabbit3 bash
rabbitmqctl stop_app
rabbitmqctl reset
rabbitmqctl join_cluster --ram rabbit@my-rabbit1
rabbitmqctl start_app
exit
```

然后集群搭建完成

- 最后可以加入haproxy负载均衡

```shell
docker run -d --name haproxy  -p 8090:80 -p 5677:5677 -p 8001:8001 -v ./rabbbitmq/haproxy/haproxy.cfg:/usr/local/etc/haproxy/haproxy.cfg haproxy
```

haproxy.cfg配置文件如下【最后一定要有一个空行，不然报错】

```
global
  daemon
  maxconn 256

defaults
  mode http
  timeout connect 5000ms
  timeout client 5000ms
  timeout server 5000ms

#监听5677端口转发到rabbitmq服务
listen rabbitmq_cluster
  bind 0.0.0.0:5677
  option tcplog
  mode tcp
  balance leastconn
  server rabbit1 rabbit1:5672 check inter 2s rise 2 fall 3
  server rabbit2 rabbit2:5672 check inter 2s rise 2 fall 3
  server rabbit3 rabbit3:5672 check inter 2s rise 2 fall 3

#haproxy的客户页面
listen http_front
  bind 0.0.0.0:80
  stats uri /haproxy?stats

#监听8011端口转发到rabbitmq的客户端
listen rabbitmq_admin
  bind 0.0.0.0:8001
  server rabbit1 rabbit1:15672 check inter 2s rise 2 fall 3
  server rabbit2 rabbit2:15672 check inter 2s rise 2 fall 3
  server rabbit3 rabbit3:15672 check inter 2s rise 2 fall 3

```

## Mino文件系统

- docker命令

```
docker run -d --name mino --restart=always -p 9000:9000 -e MINIO_ACCESS_KEY=admin -e MINIO_SECRET_KEY=12345678 \
-v ./mino/data:/data \
-v ./mino/config:/root/.minio minio/minio server /data
```



## Jenkins【这个镜像是中文社区版的】

- docker-compose脚本

```yaml
version: '3.1'
services:
  jenkins:
    restart: always
    image: jenkinszh/jenkins-zh:latest
    container_name: jenkins
    ports:
      # 发布端口
      - 8080:8080
      # 基于 JNLP 的 Jenkins 代理通过 TCP 端口 50000 与 Jenkins master 进行通信
      - 50000:50000
    environment:
      TZ: Asia/Shanghai
    volumes:
      - ./data:/var/jenkins_home
```

