### 注意事项【这里打了⭐的是比较重要】

- metadata_service表单提交url和数据源url要必须一样的才行【因为本来就是编辑该数据库后又从该数据库中拿记录】

- 新建表单的时候一般是不开自动创建表单enable_list，成功创建表单之后才开启enable_list，然后会在数据列表页面自动生成对应的数据列表，还会自动填充数据源路径和将数据补充到列表中【配置表单数据源和提交路径的时候要换成metadata_service】

  - http ://business-flow-service.cde19b8e2c21340deb0f8dfefa46728a0.cn-shenzhen.alicontainer.com/form/metadata_service?form_code=security_incidents&order_type=security_incidents&table_code=security_incidents
  - http ://business-flow-service.cde19b8e2c21340deb0f8dfefa46728a0.cn-shenzhen.alicontainer.com/form/metadata_service?form_code=vulnerability_management&order_type=vulnerability_management&table_code=vulnerability_management

  上面👆的form_code是为了获取对应表单组件的key，table_code是备用的

- ⭐设计表单时注意事项：

  1. 改组件key
  2. 改表单数据源URL和表单提交URL的地址
  3. 提交按钮上的流程Code要关联
  4. 还原记得将触发类型改为“还原表单”
  5. 对于自动填充的输入框要开启“禁用”和关闭“允许收集”按钮
  6. 对于组件要开校验设置
  7. 表单设计完成后在“编辑”按钮中开启“允许创建数据列表”，然后将数据源配置的数据源路径换成你想要的

- 数据列表Code和表单Code可以重复的，因为到最后跳转的前缀路径是不一致的，因此可以重复

- 在表单中心中设计了表单之后开启生成在对应的数据列表中就会出现和表单对应的数据表字段

- 已内置的全局变量【全局变量是在实例启动的时候初始化的参数，在实例内可直接获取；局部变量是在实例运行中所产生的变量】有如下：

  ```json
  ${{{starter}}} // 获取启动该实例的用户ID
  ${{{form_code}}} // 获取启动该实例的表单code
  ${{{form_id}}}//获取启动该流程实例的表单id
  $((xid))//全局事务ID
  ```

- 流程要填表单Code，表单要填流程Code，相互填写是为了要获取彼此的数据

- 提交表单按钮提交时默认自带了一个完成标识，该字段是status，默认值为"submit"；提交表单按钮还有一个可以关联流程Code的地方，填写了对应的流程Code之后点击提交表单按钮就能生成对应模型的一个流程实例。

- 用户任务和审批任务B中的“数据映射”就是用target_id和target_code来映射对应的表单（分别对应form_id和form_code）

- 服务任务就是用来调用内置好的php处理类，但目前内置好的处理类不多，根据文档中要求传入的参数即可使用内置类

- 在这个keewood当中，element就代表着各种任务（用户任务，服务任务那些东西）

- 在表单属性中有一个输入框是“数据来源”，里面有sources，read，edit等，read主要是可以通过字段来自动填充对应的表单，edit就是用来编辑对应的数据库表的。

- 那些已经通过”数据来源“填充的key输入框是不用进行提交的，可以把这个key的“允许收集”按钮关闭。

- ⭐在当前流程中要读取某个任务节点的值时，要看具体的数据结构才能获得该变量，比如获取某个用户任务对应表单中的节点值

  ![image-20210305135715105](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20210305135715105.png)

  这里就要用下面的格式才能获取到表单中对应的key值【通过节点Code来获取不同节点的变量值】

  ```
  节点Code.formData.data.变量名
  ```

  但其实formData和response字段在json结构的同一层次。👇

  ![image-20210308120047742](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20210308120047742.png)

- 表单中心或数据列表中的的组件为all资源意思是只要具备这个组件的权限操作即可，比如某个表单的资源类型为all，设计这个表单所需权限为design，那只要用户有button.design或者dictionary.design或者其它xxx.design的操作权限那就可以操作这个表单。【权限无论是什么字符串都可以，一般都是规范为  **资源名.操作**  ，当然不这么写也可以】

- 在流程设计中，需要给个完成标识中表单操作的对应权限才能编辑该表单【该权限由管理员那边给的】，记得对应的表单的资源类型也要改成管理员给你的资源类型。

- 流程设计中的用户任务要开启“预创建"【不开启预创建的话就无法在完成这个节点后在另一个数据列表产出一条记录】，目的是将流程实例ID流转入表单，与这个流程绑定在一起，这样整个流程就可以获取到这个流程ID，而且如果要结束整个流程就需要这个流程ID【基本所有的用户任务表单都要开预创建按钮】

- 关于审批任务，它的完成标识有3个，分别是agree，reject，recall

- ⭐如果要获取某个任务节点的变量，一般是需要用Variable类的实例先set设置成局部变量或者全局变量才能在后面的任务节点中通过脚本获取得到该变量

- 在流程中，用户任务和其关联的表单当中，看上去表单和流程是分开的，实际在后端中它们的数据基本在一块的，所以校验权限那些写在用户任务中，但是在表单点击提交时会校验有没有权限操作【完成标识那一块】

- ⭐无论如何，要想发去一个流程，那就是一定是从一个表单的提交按钮开始发起的（这个提交按钮绑定了关联的流程Code）；但是其实也可以通过一个按钮的js增强脚本来发起一个新的流程。

- ⭐js增强中的表单创建和表单编辑其实就是在无需打开表单的情况下完成一个表单的操作

- ⭐表单的提交按钮中有个“完成标识”比如submit，在点击时会去验证流程设计中对应的用户任务节点的完成标识脚本框是否有相同的完成标识submit，如果没有则无权限点击提交；如果有则再会去验证里面"permission"字段的操作权限，若用户有这个表单对应的资源的"permission"操作权限，则可以提交。

- 在js增强的关于表单的操作中，里面的mapping字段一般都是映射表单字段（key为指定的表单字段，value自己设置）；但是在js增强的跳转路由中,mapping的key为url参数key，value填入当前行数据的某个字段名称，就会自动映射为该字段名的值。

- ⭐在表单的**数据来源**中要获取全局变量时【场景是在数据来源中，其它场景不一定适用】，前面一定要加上**流程Code**；form_id和form_code不同，form_code代表的某一类表单，而form_id代表的是表单生成出来的对象，类似于类和对象的关系，然后可以用这个form_id来通过数据源地址获取对应的数据

- ⭐记住所有的变量（无论是自带的还是自己设置的）的获取都要去看流程实例的每一个任务节点的数据结构来获取最好。

- 在表单中的字段，form_code是存在的，但是form_id是不存在的，一般这个所谓的form_id就是id







### 常用脚本

```php
//引入权限依赖
use App\BusinessFlow\Workflow\Script\Authorization;
//引入变量依赖
use App\BusinessFlow\Workflow\Script\Variable;
//引入消息依赖
use App\BusinessFlow\Workflow\Script\Message;

//分别进行实例化
$variable = make(Variable::class);
$authorization = make(Authorization::class);//这个只能设置哪些角色或者用户有权限操作而不是赋予权限给哪些用户或者角色


// 设置那个用户有权限
$authorization->setUsers('表单Code', [['user_id' => '用户ID的具体值']]);
// 设置那个角色有权限
$authorization->setRoles('表单Code', [['5', '用户ID']]);
// 根据formcode获取有哪些角色权限
$authorization->getRolesByFormCode('表单Code', ['read', 'writer']);
// 根据formcode获取有哪些用户权限
$authorization->getUsersByFormCode('表单Code', ['read', 'writer']);
// 根据用户任务的form，去取form的资源.
$authorization->getResourceByFormCode('表单Code');
// 设置抄送人，仅限审批任务使用
$authorization->setCarbonCopyRecipients([1, 2, 3, 4, 5]);
// 获取用户上级领导 $userId=>"12312312" $level=>1 level可以为空，则获取全部
$authorization->getLeader($userId, $level);

//******************************************************分隔符*******************************


//获取对应的变量值,这里获取的是发起整个流程实例的人对应用户ID
$starter = $variable->get('${{{starter}}}');
// 获取指定的用户任务变量
$variable->getElementVariable('elementId.response.variableName');
// 获取当前用户任务关联的formCode
$variable->getCurrentTaskFormCode();
// 获取该实例由那个formCode启动的
$variable->getStartFromCode();
// 设置全局变量，（设置全局变量时，如果该全局变量已存在，则覆盖旧的数据）
$variable->setGlobalVariable(['variableName1' => 'variableValue1', 'variableName2' => 'variableValue2']);
// 设置局部变量，（设置局部变量时，如果该局部变量已存在，则覆盖旧的数据）
$variable->setVariable(['variableName1' => 'variableValue1', 'variableName2' => 'variableValue2']);

//**************************分隔符***********************************************

//消息发送相关的脚本 
// 实例化消息类
$message = make(Message::class);

/**
 * 通过角色ID发送钉钉消息
 *
 * @param string $title 标题
 * @param string $message 消息体 markdown 格式
 * @param array $rolesId 角色ID
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */
$message->sendDingTalkWorkMessageForRolesId($title,$message,  $rolesId);

/**
 * 通过用户ID发送钉钉消息
 *
 * @param string $title 标题
 * @param string $message 消息体 markdown 格式
 * @param array $userIds 用户ID
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */
$message->sendDingTalkWorkMessageForUserId($title, $message, $userIds);



//***********************************************分隔符***************************************
//分配用户框常用脚本
use App\BusinessFlow\Workflow\Script\Authorization;
use App\BusinessFlow\Workflow\Script\Variable;

$authorization = make(Authorization::class);
$variable=make(Variable::class);

//获取启动该流程实例的发起人用户id
$starter=$variable->get('${{{starter}}}');

// 设置启动该流程的发起人用户有权限操作该表单
$authorization->setUsers(
    'feedback_result', 
    [
        [
            'user_id' => $starter
        ]
    ]
);

//************************************************分隔符*****************************************
//预创建脚本
{
    "create_form_id":${{{form_id}}},
    "create_form_code":${{{form_code}}}
}

//*****************************************分隔符*******************************************
//完成标记
{
    "submit":{
        "is_complete":true,
        "is_save":true,
        "permission":{
            "feedback_record":["testa.add"]
        }
    }
}


//*********************************************分隔符********************************************
//表单设计中的数据来源（一般用来从数据源中获取数据之后自动填充到对应的key或者修改对应的数据源），👇下面是个例子：
{
    "sources": {
        "safety_propaganda": {
            "url": "http://business-flow-service.cde19b8e2c21340deb0f8dfefa46728a0.cn-shenzhen.alicontainer.com/form/metadata_service?form_code=safety_propaganda&order_type=safety_propaganda&table_code=safety_propaganda",
            "method": "POST",
            "body": {
                "id": "$(create_form_id)",
                "type": 2,
                "format": 1
            }
        }
    },
    "read": {
        "propaganda_time": "$[safety_propaganda.data.propaganda_time]",
        "propaganda_type": "$[safety_propaganda.data.propaganda_type]",
        "propaganda_name": "$[safety_propaganda.data.propaganda_name]",
        "propaganda_content": "$[safety_propaganda.data.propaganda_content]"
    }
}


//***********************分割线***********************************************************
//通过HTTP任务来获取表单信息的请求体Body
{
    "id":"${{{form_id}}}",
    "type":2,
    "format":1
}

//**************************************分割线***************************************
//根据行数据的某个字段值可以直接设置某个按钮的有无,js渲染增强
if(ROW['on_off'] == "运行"){    BUTTON_DISPLAY = false}

//关闭表单
$.CancelAction()
```







### API接口

```json
//
/form/fields/values
{
    "id":"表单ID",
    "values":{
        "key":"键值，这里是系统配置值",
        "value":"改变其对应的值",
        "action":"overwrite"
    }
}


```






### 疑问【打了⭐就是已经解决的问题】

- 这下面的的是什么意思？![image-20210301153627428](C:\Users\Administrator\AppData\Roaming\Typora\typora-user-images\image-20210301153627428.png)

- ⭐”流程分类“的产生也是用字典中生成吗？【解决，在数据字典中查找“页面类型”或者“流程类型就可以找到分类数据】

- ⭐万一弄错了想要删除怎么办？因为大部分没有删除按钮【解决，不能删除，只能重新建一个，把错的扔到“其他页面”分类即可】

- ⭐在流程中，里面的节点是不是一般都是的对应不同的表单？【解决，看任务类型，如果是用户任务一般有表单，如果是确认那些一般都是用脚本任务】

- ⭐现在目前没数据库链接如何配置数据源【解决，keewood系中系统的文档中有的，直接在表单设计的编辑种开启“创建数据列表”的按钮就能自动生成对应的数据库和数据列表】

- ⭐还有一些例子是一个流程节点就作为一个流程，这种情况可以的嘛？【解决，这种情况有时候是有的，视业务情况而定】

- ⭐单据服务order_service和元数据服务metadata_service有什么不同？【解决，单据服务存的是表单中一行一行的数据；而元数据服务存的是表单组件那些属性字段】

- ⭐图片上传框可以上传多个图片，那是否可以直接一个按钮显示所有图片？【解决，只要显示全部图片就可以，不用一个按钮显示，直接生成的数据中都自带有了】

- 安全运营项目所需要的数据字典都是我们开发人员配吗？

- ⭐那些下拉框组件如何和数据字典的记录关联【解决，组件属性下面有个“数据源链接”，那里可以填写数据字典的Code】

- ⭐是否全部输入框都要设置校验规则？【解决，是需求方决定的，需求方要要验证哪个就加哪个】

- ⭐表单设计中“表单属性”的”数据来源“和“表单数据源URL”有什么区别？【解决，数据来源主要就是填充数据到对应的key来用的，表单数据源URL就是用来获取元数据服务或者单据服务用的】

- ⭐如何启动一个流程来进行测试看看？流程中可以填表单Code，表单的提交表单中可以设置流程Code，那到底应该弄哪个？主要是不知道流程里面的节点任务如何流转下去？【解决，流程要填表单Code，表单要填流程Code，两个都要彼此填写才能获取彼此的信息；流程里的节点任务是通过完成标识流转下去的】

- ⭐用户任务中的完成标识里面有些字段是什么意思，比如"reply"，"finish"，"submit"?【解决，这些也是完成标识位】;里面的is_save字段的有无会导致有什么区别？【解决，这个字段是用来保存数据用的】

- ⭐文档上面的流程设计之后还有最后一步为api接口开发？【解决，按需求即可，只有一些才需要代码开发】

- ⭐预创建到底是什么意思？【解决，是在打开表单之前就传一些变量的值比如form_code和form_id给表单，就是自己自定义一个不会显示的表单字段和值作为key-value来给当前节点关联的表单使用】

- ⭐数据映射到底是什么？【解决，数据映射这个功能目前少用，这个在历史版本上有作用，不过暂时不用管】

- ⭐正常的流程启动过程是怎么样的？【解决，就是在完成一个节点之后，就会自动流转到下一个节点，对应节点的数据列表会多一条记录，然后完成之后又会继续在接下来的节点对应的数据列表中不停添加记录下去】

- ⭐数据列表中的日期组件渲染和数据类型不一致时会报错？【解决，数据列表中的日期组件中配置数据源时渲染组件和数据类型都要设置成文本和字符串才能保存成功和正常显示。】

- 文档中的全局变量不全，在一些流程实例中看到的全局变量在文档中是查不到的？

- ⭐在流程设计中，分配用户的那个框是用来分配权限给用户来操作这个表单，但是究竟是分配了什么权限？【解决，分配用户这个脚本框是用来指定某些指定的用户或者角色才能操作这个表单，而操作这个表单所需的指定资源和操作权限是由后台管理员给予指定的用户的】

- ⭐在数据列表中点击了下一步按钮之后跳转页面要刷新才能显示，如何解决？【解决，因为路径后面出现了个空格，注意在复制路径的时候要注意后面有没有带空格】

- 那些接口API到底在哪里查看？比如/form/fields/values到底什么接口？

- 自动填充表单的key时如果是数据字典时，则会显示的是该字典的数据值而不是字符串值，如何解决？

- ⭐由于大部分流程都是由一个表单发起的，但是这样的话这个表单就不在这个流程实例中了，也就无法通过节点Code来获取对应的表单信息了，那怎么才能获取到这个表单的信息？【解决，要使用到节点的前面再部署一个HTTP任务节点，然后通过form_id和数据源URL来获取对应的表单信息；又或者直接用一个脚本任务将form_id保存到变量中，然后在另一个表单采用数据来源的方式传入该form_id即可获取】

- ⭐下发消息的类怎么才能成功发送消息？【解决，主要是要用户ID要写对才能发送，之前写错是因为写错用户ID了】

- 表单设计中的进程进度组件怎么使用，文档说了等于没说？

- ⭐按钮点击创建新流程的js增强怎么用，该js增强中填写了表单code之后会报权限的错误？【解决，权限错误是因为没有完成标识为空，要填上完成标识才可以，**又或者因为mapping为空，导致当前行的全部数据映射进对应的表单Code，其中#user_id，#creator是不可以映射的，所以报错**；这个js增强的意思是将通过点击按钮，将当前行的数据映射到你绑定的表单Code的字段中，就可以在对应的数据列表中增加一条数据，同时开启脚本中绑定了的流程Code所对应的流程；还有js增强中的行操作中，创建表单、查看表单、编辑表单都只是打开对应而已】【**这种可以通过当前行数据映射到另一个表单的js增强行为可以作为另一种自动填充数据的方式**】

- ⭐一些场景中比如“确认”这种，是使用审批还是直接http脚本解决更好？【解决，按需求方的要求来即可】

- 表单中的关联选择框如何使用？

- ⭐流程中有没有任务节点可以发送定期消息（即定时消息）？【解决，使用分支节点和悬挂任务来进行判断循环即可】

- user_id和creator是什么关系？两者是否一样？

  
  
  
  
  
  
  
  
  ### 完成情况：
  
  1. 表单设计基本完成【有一个小需求是关于那个操作流程的】
     1. 提交按钮关联的流程Code基本完成
  2. 数据列表基本完成
     1. 数据源配置基本完成
  3. 流程设计基本完成
     1. 都打了草稿，但仍有流程逻辑感觉不太对



### 思路：

1. 流程的发起都是需要第一个表单的触发，触发完成之后可以在对应的数据列表中新建一个列的操作按钮，这个按钮用来跳转到下一个流程节点对应的数据列表（使用js增强的路由功能）
2. 可以得知我的账户没有all资源的testa.add操作权限，只有test1资源的testa.add操作权限
3. 可以确认数据列表中的每条数据的ID就是form_id，但是以按钮发起流程就一定要创建一条表单记录，如何做到？







