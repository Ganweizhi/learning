### Elasticsearch

这是个可以存大量数据和检索速度很快的搜索框架。elasticsearch的docker搭建在本仓库的Docker&Docker-compose目录内，直接使用即可搭建。

#### index

索引，elasticsearch可以有多个索引，和传统数据库比它就像是数据库中的database

#### type

类型，每个index下可以有多个类型，但是elasticsearch已经开始不再使用type，未来type会消失，只会剩下index和document。它就像是传统数据库中table

#### document

文档，每个type下面又有多个文档，每个文档有多个字段field，真正的数据就保存在里面，它就是像是传统数据库中每个表下面的一行行数据

#### query dsl

这个api是非常多的，要想记住和掌握只有不断练习和发现它们的规律才行，下面根据网上的资料总结了一些

```json
/_search
{
    "query":{  
     // =================================== 全文查询 分词查询 在匹配时会对所查找的关键词进行分词 ===================================
      "match_all": {},  //匹配所有结果,_score为1
      "match_none": {},  //不匹配任何文档
      "match":{   //匹配查询
          "字段1":{
                "query" : "匹配值",
                "operator" : "and",     //从提供的文本中构造一个布尔查询 and或者or ,should可以使用minimum_should_match 参数设置要匹配的可选子句 的最小数量
                "fuzziness": "AUTO",    //模糊性 根据要查询的字段类型进行模糊匹配 
            }
      },
      "multi_match" : {   //多重匹配查询 默认匹配所有字段 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-multi-match-query.html#crossfields-fuzziness
        "query":    "匹配值",
        "fields": [ "字段1", "*字段2" ],   //可选 支持通配符写法,  例:字段1权重为5 可以这样写[ "字段1^5", "*字段2" ]   
        "type":"best_fields",   //multi_match内部执行查询的方式  5个可选值best_fields(默认)，most_fields，cross_fields，phrase，phrase_prefix
      },
      "match_phrase" : {  //匹配词组查询 必须包含所有分词,可调节
          "字段2" : {
              "query" : "匹配值",
              "analyzer" : "my_analyzer",   //指定控制分析器
          }
      },
     "match_phrase_prefix" : {  //匹配词组前缀查询
          "字段3" : {
              "query" : "匹配值",
              "max_expansions" : 10 //控制最后一项将扩展到多少个后缀 默认值50
          }
      },
      "query_string" : { //字符串查询 支持正则表达式 使用查询解析器来解析其内容的查询 
          "default_field" : "字段5",  //可选
          "query" : "匹配值"
      },
    
    //术语查询 整体查询，不进行分词器分析，术语级查询与存储在字段中的确切术语匹配
      "term": {   //完全匹配  字段类型必须是not_analyzed 否则保存的索引是分析过的
        "字段1": {
          "value": "urgent",
          "boost": 2.0    //提升权重  默认是1.0
        }
      },
      "terms":{ //多值完全匹配
        "字段1":["匹配值1", "匹配值2"]  //必须是数组
      },
      "terms_set": {  //词条集查询 返回字段中包含最少数目的精确词条的文档 
          "codes" : {
              "terms" : ["匹配值1", "匹配值2", "匹配值3"], //必需 
              "minimum_should_match_field": "required_matches"    //可选 数字字段,包含返回文档所需的匹配词条数
              "minimum_should_match_script":{ //
                 "source": "Math.min(params.num_terms, doc['required_matches'].value)"
              }
          }
      },
      "range" : { //范围查询  https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-range-query.html
          "字段1" : { // gt(>) get(>=) lt(<) lte(<=) 
              "gte" : 10,
              "lte" : 20,
              "boost" : 2.0 //可选 设置查询的提升值，默认为 1.0
          }
      },
      "exists": { //存在查询  字段不能是null或[]
          "field": "字段1"
      },
      "prefix": { //前缀查询 包含带有指定前缀（未分析）的术语的字段的文档
          "字段1":{
              "value":"匹配值1",
              "boost":2
          }
      },
      "wildcard": { //通配符查询
          "字段1": {
              "value": "ki*y",   //？(单个任意字符), *(多个任意字符)   避免使用*或开头模式?。这会增加查找匹配项所需的迭代次数，并降低搜索性能
              "boost": 1.0,   //用于降低或增加查询的 相关性分数。默认为1.0
              "rewrite": "constant_score"   //用于重写查询的方法 constant_score(默认)   详见https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-multi-term-rewrite.html
          }
      },
      "regexp": {  //正则表达式查询 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-regexp-query.html
          "字段1":{
              "value":"s.*y",
              "boost":1.2
          }
      },
      "fuzzy": { //模糊查询 
          "字段1" : { //如果prefix_length将设置为0，并且max_expansions将设置为较高的数字，则 此查询可能非常繁琐。这可能会导致索引中的每一项都受到检查！
              "value": "ki",
              "boost": 1.0,
              "fuzziness": 2, //模糊性 最大编辑距离。默认为AUTO 更多参数详见 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/common-options.html#fuzziness
              "prefix_length": 0, //不会被“模糊化”的初始字符数 默认0
              "max_expansions": 100, //查询将扩展到的最大术语数 默认50
              "transpositions":false  //是否支持模糊转置（ab→ ba）。默认值为false。
          }
      },
      "type" : { //类型查询 筛选与提供的文档/映射类型匹配的文档。
          "value" : "_doc"
      },
      "ids" : {//id查询  根据其ID返回文档
          "type" : "type表",   //type  
          "values" : ["1", "4", "100"]  //id数组
      },
      =================================== 复合查询 复合查询包装其他复合查询或叶查询，以组合其结果和分数，更改其行为或从查询切换到过滤上下文 ===================================
      "constant_score" : { //恒定分数查询   所有结果分数都是boost的值 
          "filter" : {    //过滤要运行的查询 过滤查询不计算相关性分数。为了提高性能，Elasticsearch自动缓存经常使用的过滤器查询。
              "term" : { "字段1" : "匹配值"}
          },
          "boost" : 1.2 //固定分数
      },
      "bool" : { //布尔查询
          "must":{ //and
              "term":{"user":"kimchy"}
          },
          "filter":{ //and   计分被忽略，并且子句被考虑用于缓存
              "term":{"tag":"tech"}
          },
          "must_not":{  //not
              "range":{
                  "age":{"gte":10,"lte":20}
              }
          },
          "should":[  //or
              {"term":{ "tag":"wow"}},
              {"term":{"tag":"elasticsearch"}}
          ],
          "minimum_should_match":1, //必须匹配的子句的数量或百分比  https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-bool-query.html#bool-min-should-match
          "boost":1
      },
      "dis_max" : { //分离最大化查询，最佳匹配语句  由匹配度最高的字段去计算评分,bool是多个字段综合计算评分  评分越高越靠前
          "tie_breaker" : 0.7,
          "boost" : 1.2,
          "queries" : [
              {"term" : { "age" : 34 }},
              {"term" : { "age" : 35 }}
          ]
      },
      "function_score": { //功能分数查询 允许你修改的由查询检索文档的分数。例如，如果分数函数在计算上很昂贵，并且足以在过滤后的文档集上计算分数，则此功能很有用。 
        "query": { "match_all": {} },
        "boost": "5", 
        "functions": [
            {
                "filter": { "match": { "test": "bar" } },
                "random_score": {}, 
                "weight": 23
            },
            {
                "filter": { "match": { "test": "cat" } },
                "weight": 42
            }
        ],
        "max_boost": 42,  //限制加强函数的最大效果   加强score=min(加强score, max_boost)
        "score_mode": "max",  //决定functions裡面的加强score们怎麽合併 会先执行score_mode，再执行boost_mode，默认值是multiply
        "boost_mode": "multiply", //决定 old_score 和 加强score 如何合併 默认值是multiply
        "min_score" : 42
      },
      "boosting" : {  //通过查询结构调整相关度  返回与positive查询匹配的文档，但减少与negative查询匹配的文档的分数。
        "positive" : {  // 只有这个块里面才匹配到结果集中
            "term" : {
                "字段1" : "匹配值1"
            }
        },
        "negative" : {  //降低文档相关度
             "term" : {
                 "字段1" : "匹配值2"
            }
        },
        "negative_boost" : 0.2
      },
      =================================== 联接查询 ===================================
      "nested" : {  //嵌套查询  索引必须包含一个嵌套字段映射("type" : "nested")
          "path" : "obj1",  //必需  嵌套对象的路径
          "query" : {       //必需  在嵌套对象上运行的查询
              "bool" : {
                  "must" : [
                      { "match" : {"obj1.name" : "blue"} },
                      { "range" : {"obj1.count" : {"gt" : 5}} }
                  ]
              }
          },
          "score_mode" : "avg",   //可选 指示匹配子对象的分数如何影响根父文档的相关性分数  可选值: avg(默认使用所有匹配的子对象的平均相关性得分。),max,min,none(不使用匹配的子对象的相关性分数。该查询为父文档分配得分为0),sum
          "ignore_unmapped":false //可选 是否忽略未映射path且不返回任何文档而不是错误  默认为false。
      },
      "has_child" : { //子查询 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-has-child-query.html
          "type" : "blog_tag",
          "score_mode" : "min",
          "min_children": 2, 
          "max_children": 10, 
          "query" : {
              "term" : {
                  "tag" : "something"
              }
          }
      },
      "has_parent" : {//查询返回关联的父母已匹配的子文档
          "parent_type" : "blog",
          "query" : {
              "term" : {
                  "tag" : "something"
              }
          }
      },
      "parent_id": {  //查找属于特定父级的子级文档
          "type": "my_child",
          "id": "1"
      },
      =================================== Specialized queries 专用查询 该组包含的查询不适合其他组 ===================================
      "more_like_this" : { //查找与指定的文本，文档或文档集合相似的文档 可用于内容推荐 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-mlt-query.html
          "fields" : ["name.first", "name.last"], //分析文本的字段列表。默认_all(所有字段)
          "like" : [  //参数必须  遵循通用语法，可以指定自由格式的文本和/或单个或多个文档
              {
                  "_index" : "marvel",
                  "_type" : "quotes",
                  "doc" : {
                      "name": {
                          "first": "Ben",
                          "last": "Grimm"
                      },
                      "_doc": "You got no idea what I'd... what I'd give to be invisible."
                    }
              },
              {
                  "_index" : "marvel",
                  "_type" : "quotes",
                  "_id" : "2"
              }
          ],
          "min_term_freq" : 1,    //最小术语频率，低于该频率的术语将从输入文档中忽略。默认为2。
          "max_query_terms" : 12  //将被选择的最大查询词数。增大此值可提高准确性，但会降低查询执行速度。默认为 25。
     },
     "percolate" : {  // percolator类型数据(任何含有json对象的列可以被配置成percolator字段,一个索引中只能有一个percolator字段类型)  https://www.elastic.co/guide/en/elasticsearch/reference/6.8/query-dsl-percolate-query.html
          "field" : "字段1", //字段1一般保存的是query语句
          "document" :[
            {"message" : "A new bonsai tree in the office"},
            {"message" : "A new bonsai tree in the office2"}
          ]
      },
      "wrapper": {  //包装器查询 接受任何其他查询作为base64编码字符串的查询。
          "query" : "eyJ0ZXJtIiA6IHsgInVzZXIiIDogIktpbWNoeSIgfX0=" // Base64 encoded string: {"term" : { "user" : "Kimchy" }}
      }
      ===================================   跨度查询 低级别的位置查询，控制对指定字词的顺序和接近程度 ===================================
      "span_term" : { //跨度字词查询
          "字段1":{
              "value":"匹配值1",
              "boost":2   //评分倍数 
          }
      },
      "span_multi":{//跨度多词查询
        "match":{
          "prefix" : { 
              "字段1":{
                  "value":"匹配值1",
                  "boost":2   
              }
           }
        }
      },
      "span_first" : {//跨度优先查询 确定一个单词相对于起始位置的偏移位置
          "match" : {
              "span_term" : { "user" : "kimchy" }
          },
          "end" : 3
      },
      "span_near" : {//跨度近查询 用于确定几个span_term之间的距离，通常用于检索某些相邻的单词，避免在全局跨字段检索而干扰最终的结果
          "clauses" : [
              { "span_term" : { "field" : "value1" } },
              { "span_term" : { "field" : "value2" } },
              { "span_term" : { "field" : "value3" } },
              {
              "field_masking_span": {//允许范围查询通过说谎其搜索字段来参与复合单字段范围查询
                "query": {
                  "span_term": {
                    "text.stems": "fox"
                  }
                },
                "field": "text"
              }
            }
          ],
          "slop" : 12,
          "in_order" : false
      },
      "span_or" : {//跨度或查询 嵌套一些子查询，子查询之间的逻辑关系为 或
          "clauses" : [
              { "span_term" : { "field" : "value1" } },
              { "span_term" : { "field" : "value2" } },
              { "span_term" : { "field" : "value3" } }
          ]
      },
      "span_not" : {//跨度非查询
          "include" : { //定义包含的span查询
              "span_term" : { "field1" : "hoya" }
          },
          "exclude" : { //定义排除的span查询
              "span_near" : {
                  "clauses" : [
                      { "span_term" : { "field1" : "la" } },
                      { "span_term" : { "field1" : "hoya" } }
                  ],
                  "slop" : 0,
                  "in_order" : true
              }
          }
      },
      "span_containing" : {//跨度包含查询 有多个子查询，但是会设定某个子查询优先级更高，作用更大，通过关键字little和big来指定。
          "little" : {
              "span_term" : { "field1" : "foo" }
          },
          "big" : {
              "span_near" : {
                  "clauses" : [
                      { "span_term" : { "field1" : "bar" } },
                      { "span_term" : { "field1" : "baz" } }
                  ],
                  "slop" : 5,
                  "in_order" : true
              }
          }
      },
      "span_within" : {//返回包含在另一个跨度查询中的匹配 与span_containing查询作用差不多，不过span_containing是基于lucene中的SpanContainingQuery，而span_within则是基于SpanWithinQuery。
          "little" : {
              "span_term" : { "field1" : "foo" }
          },
          "big" : {
              "span_near" : {
                  "clauses" : [
                      { "span_term" : { "field1" : "bar" } },
                      { "span_term" : { "field1" : "baz" } }
                  ],
                  "slop" : 5,
                  "in_order" : true
              }
          }
      }
 
    },
    "aggs": { //聚合  TODO
      "models": {
        "terms": { "field": "model" } 
      }
      //================================= 普通聚合
      "显示名":{
          //Metrics(度量/指标) 简单的对过滤出来的数据集进行avg，max操作，是一个单一的数值 
          "Metrics(度量/指标)":{  //max(最大值)/min(最小值)/avg(平均值)/sum(求和)/stats/cardinality(去重) 
            "field"："字段名"
          }
          //Bucket(桶) //相当于分组条件
          "terms":{  //根据字段值分组聚合
            "field"："字段名",
            "size"："整体返回多少个分组",
            "shard_size"："每个分片上返回多少个分组",
            "order"：{} ,  //排序
            "include":[] ,  //包含 正则表达式
            "exclude":[] , //排除 正则表达式
          },
          "percentiles": { //百分比聚合 从小到大累计每个值对应的文档数的占比（占所有命中文档数的百分比）
            "field": "字段名",
            "percents": [1,5,25,50,75,95,99]  //指定占比比例对应的值。默认返回[ 1, 5, 25, 50, 75, 95, 99 ]分位上的值
          },
          "percentile_ranks": { //百分比聚合  小于等于指定值的文档比
            "field":"字段名",
            "values":[10,100,1000000]
          }
      },
      //================================= 带过滤的聚合
      "显示名":{
        "filter": {
          "match":{"operation.keyword":"获取公司(分页)"}
        },
        "aggs": {
          "avgs": {
            "avg": {
              "field": "exTime"
            }
          }
        }
      }
    },
    "post_filter": {  //删除匹配的结果 
       "term": { "color": "red" }
    },
    "sort":[ //排序 
      {
          "字段1":{
              "unmapped_type":"long" , //缺少映射时，指定mapping排序规则
              "missing":"_last" , //缺少字段值时,指定排序值  默认_last,可选 _first或自定义的值
              "order":"asc", //排序规则 可选值: asc(正序),desc(倒序)
              "mode":"avg"  //mode 排序模式,支持按数组或多值字段排序 可选值: min(最小),max(最大) 后面三种只适用数字sum(综合),svg(平均),median(中位数)
          }
      },
      "字段2",
      {"字段3":"desc"}
    ],
    "_source":{   //指定source的列返回   可选值: "字段1"(匹配单个),["字段1","字段2"](匹配多个),false(关闭输出)
        "includes":["字段1","字段2"], //包含 
        "excludes":["字段3","字段4"]  //排除
    }
    "from":0, //分页起始位置 from+size不能超过index.max_result_window配置,默认是10000  建议使用scroll
    "size":10, //页大小
    "stored_fields":[], //显式标记为存储在映射中的字段有关，默认情况下处于关闭状态，通常不建议这样做。而是使用源过滤来选择要返回的原始源文档的子集。
    "script_fields":{}, //允许为每个匹配返回脚本评估（基于不同的字段）
    "docvalue_fields":[ //允许为每个匹配返回字段的doc值表示形式  要访问嵌套字段，docvalue_fields 必须在一个inner_hits块内使用
        {
            "field": "字段1",  //*可以用作通配符 
            "format": "use_field_mapping"  #使用mapping的格式, 日期字段和数字字段可以使用自定义格式 日期可使用格式详见https://www.elastic.co/guide/en/elasticsearch/reference/6.8/mapping-date-format.html，数字字段可使用格式详见https://docs.oracle.com/javase/8/docs/api/java/text/DecimalFormat.html
        }
    ],
    "highlight":{},  //高亮显示 TODO
    "rescore"：{}, //使用查询来调整评分  注意: 如果为查询提供显式sort （而不是_score降序），则将引发错误rescore
    "explain": true,  //解释每个匹配的得分如何计算
    "seq_no_primary_term": true,  //返回每个搜索命中的最后修改的序列号和主要术语  有关更多详细信息，请参见乐观并发控制https://www.elastic.co/guide/en/elasticsearch/reference/6.8/optimistic-concurrency-control.html
    "version": true,  //返回每个搜索命中的版本。
    "indices_boost" : [  //指数提升，跨多个索引搜索时，允许为每个索引配置不同的提升级别
        { "alias1" : 1.4 },
        { "index*" : 1.3 }
    ],
    "min_score": 0.5,   //排除_score小于以下内容中指定的最小值的文档
    "collapse" : {  //根据字段值折叠搜索结果,折叠是通过每个折叠键仅选择排序最靠前的文档来完成的
        "field" : "字段1"    //指定折叠结果集的字段
         "max_concurrent_group_searches": 4  //指定如何在每个组中对文档进行排序
    },
    "search_after": [1463538857, "654323"],  //不是自由跳到随机页面而是并行滚动许多查询的解决方案
    "suggest" : {}，     //提示功能  通过使用提示来根据提供的文本提示外观相似的术语 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-suggesters.html
    "profile": true,  //分析的一种  洞悉搜索请求是如何在低级执行的，从而使用户可以了解为什么某些请求很慢的原因，并采取措施加以改进
 
}
 
 
http参数
scroll=1m  //滚动选项,值是保存查询结果的时间  注意加上这个参数在body里面不能使用from,响应结果会返回_scroll_id，这个响应参数在_search/scroll中使用实现翻页
preference  //将某些搜索路由到某些分片副本集 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-request-preference.html
  保存分页 
  GET /twitter/_search?scroll=1m
  {
      "slice": {   //对于返回大量文档的滚动查询，可以将滚动分为多个切片，这些切片可以独立使用 默认情况下，每次滚动所允许的最大切片数限制为1024。您可以更新index.max_slices_per_scroll索引设置以绕过此限制。
          "id": 0,   //切片的ID
          "max": 2,  //最大切片数
          "field": "date"  //如果切片的数量大于碎片的数量，则切片过滤器在第一次调用时非常慢，这是一个优化方式TODO
      },
      "query": {
          "match" : {
              "title" : "elasticsearch"
          }
      }
  }
 
  使用翻页
  POST  192.168.0.202:9200/_search/scroll   //URL不应包含index 名称
  {
      "scroll" : "1m",  //保持搜索上下文对另一个开放1m，具体指参考时间单位https://www.elastic.co/guide/en/elasticsearch/reference/6.8/common-options.html#time-units
      "scroll_id" : "DXF1ZXJ5QW5kRmV0Y2gBAAAAAAAAAD4WYm9laVYtZndUQlNsdDcwakFMNjU1QQ=="   //scroll_id参数
  }
  删除翻页
  DELETE /_search/scroll/_all  //删除所有翻页
  DELETE /_search/scroll/scroll_id1,scroll_id2   //删除指定翻页多个用,号隔开
  DELETE /_search/scroll      //删除指定翻页
  {
      "scroll_id" : [   //""(单个),[](多个)
        "scroll_id1",
        "scroll_id2"
      ]
  }
/_rank_eval     //排名评估 试验性的，在将来的版本中可能会更改或完全删除     https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-rank-eval.html
/_field_caps?fields=字段名称     //字段评估 字段名称支持通配符表示法(支持*) ，多个用,号隔开
/_explain 无论文档匹配还是不匹配特定查询，这都可以提供有用的反馈。 注意: 不能在根目录使用 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-explain.html
/_validate/query     //无需执行即可验证潜在的昂贵查询
/_count //返回查询的数量   可以配合query使用
      使用q参数(_count?q=属性:值)，传递的查询是使用Lucene查询解析器的查询字符串。可以传递其他参数：
      df    在查询中未定义任何字段前缀时使用的默认字段。
      analyzer    分析查询字符串时要使用的分析器名称。
      default_operator    要使用的默认运算符可以是AND或 OR。默认为OR。
      lenient   如果设置为true，将导致忽略基于格式的错误（例如，向数字字段提供文本）。默认为false。
      analyze_wildcard    是否应分析通配符和前缀查询。默认为false。
      terminate_after  每个分片的最大计，达到此数量时查询执行将提前终止。如果设置，则响应将具有布尔值字段，terminated_early以指示查询执行是否实际上已终止。默认为no terminate_after。
/_search_shards   //返回将针对其执行搜索请求的索引和分片  不能在type下使用
      参数:
      routing 确定要对哪个分片执行请求时要考虑的路由值的逗号分隔列表。
      preference 控制要preference在其中执行搜索请求的分片副本。默认情况下，该操作在分片副本之间是随机的。请参阅首选项 文档以获取所有可接受值的列表。
      local 一个布尔值，是否在本地读取集群状态以便确定在何处分配分片，而不是使用主节点的集群状态。
 
 
太复杂了,暂时不看
inner_hits   https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-request-inner-hits.html
Named Queries https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-request-named-queries-and-filters.html
/_search/template 搜索模板 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/search-template.html
/_msearch/template 多重搜索模板https://www.elastic.co/guide/en/elasticsearch/reference/6.8/multi-search-template.html
 
==============================
 
通配符 _all 匹配所有
======================================  索引  ======================================
PUT /索引名称        //创建索引   详细参数见 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/index-modules.html
{
    "settings" : {
        "number_of_shards" : 3,     //默认 5
        "number_of_replicas" : 2      //默认1 即每个主分片一个副本
    },
    "mappings" : {
        "type名称" : {
            "properties" : {
                "字段1" : { "type" : "text" }
            }
        }
    }
}
PUT /twitter/_settings  //更新索引
{
   "index" : {
        "number_of_replicas" : 2
    }
}
DELETE /索引名称  //删除索引
GET   /索引名称     //获取索引
HEAD  /索引名称   //检查索引（索引）是否存在
POST /索引名称/_open   //开启索引   加上ignore_unavailable=true，可忽略索引不存在的错误
POST /索引名称/_close   //关闭索引
    索引别名  https://www.elastic.co/guide/en/elasticsearch/reference/6.8/indices-aliases.html
======================================  映射  ======================================
PUT 索引名称/_mapping/type名称    //创建映射
{
  "properties": {
    "字段1": {
      "type": "keyword"
    }
  }
}
GET /索引名称/_mapping/type名称   //获取指定索引或type的所有字段映射   type和索引可以不要,都支持通配符
GET 索引名称/_mapping/type名称/field/字段1    //获取指定字段的映射  多个用,号隔开，支持通配符  7.0版本要把ype那一段去掉
HEAD 索引名称/_mapping/type名称   //检查索引中是否存在一个或多个类型。
 
======================================  配置  ======================================
GET /索引名称/_settings     //获取索引配置
 
 
======================================  索引模板  ======================================
索引模板使您可以定义在创建新索引时将自动应用的模板。模板包括 设置和映射
PUT _template/模板名称        //创建模板        注意7.4以后不支持指定索引类型，默认索引类型是_doc,如果需要自定义索引url后面要加上?include_type_name=true
{
  "index_patterns": ["te*", "bar*"],    //匹配值
  "settings": {     
    "number_of_shards": 1
  },
  "order" : 0,      //排序 多个索引模板匹配都匹配时会按order从低到高的覆盖
  "version": 123    //版本 可以是任意数字
  "mappings": {
    "type名称": {
      "_source": {
        "enabled": false
      },
      "properties": {
        "字段1": {
          "type": "keyword"
        },
        "字段2": {
          "type": "date",
          "format": "EEE MMM dd HH:mm:ss Z yyyy"
        }
      }
    }
  }
}
DELETE /_template/模板名称    //删除模板
GET /_template              //获取所有模板
GET  /_template/模板名称    //获取模板   多个用,号隔开支持通配符*
HEAD _template/模板名称     //判断模板是否存在
GET /_template/模板名称?filter_path=*.version   检查模板version，您可以 使用过滤响应，filter_path以将响应限制为version
 
 
======================================  状态  ======================================
GET /_stats     //索引状态    https://www.elastic.co/guide/en/elasticsearch/reference/6.8/indices-stats.html
GET /_segments  //索引片段 获取构建Lucene索引（碎片级别）的低级别细分信息。允许用于提供有关分片和索引状态的更多信息，可能的优化信息，删除时“浪费”的数据等
GET /_recovery  //索引恢复 对正在进行的索引分片恢复的深入了解。可以报​​告特定索引或群集范围的恢复状态
GET /_shard_stores    //索引分片副本信息
    参数: status  可选值: green(所有已分配副本的分片),yellow(至少一个未分配副本),red(未分配主分片的分片)
POST /索引名称/_cache/clear    //清除所有缓存   不指定参数清除所有,
  可选参数 
    query=true(查询缓存)
    request=true(请求缓存)
    fielddata=true(字段数据缓存)
    fields=字段1,字段2(指定字段数据缓存)   
POST /索引名称/_flush    //刷新一个或多个索引 索引的刷新过程可确保当前仅保留在事务日志中的所有数据也将永久保留在Lucene中。这减少了恢复时间 https://www.elastic.co/guide/en/elasticsearch/reference/6.8/indices-flush.html
  可选参数 
    wait_if_ongoing   可选值: true(将阻塞直到可以执行另一次刷新操作为止),默认为false(如果另一个刷新操作已在运行，它将导致在分片级别引发异常)
POST /索引名称/_refresh     //使自上次刷新以来执行的所有操作都可用于搜索
POST /索引名称/_forcemerge  //合并一个或多个索引   https://www.elastic.co/guide/en/elasticsearch/reference/6.8/indices-forcemerge.html
 
 
 
查询DSL
  概念 
  叶子查询子句  寻找一个特定的值, 例: match, term, range
  复合查询子句  组合其他叶查询或复合查询,  例:bool, dis_max
查询上下文  query参数,会计算_score
过滤上下文  filter参数,不会计算_score,且查询结果会缓存
```

