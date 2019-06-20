# jquery-terminal-emulator
JQuery版命令窗模拟器

[vue-terminal-emulator](https://github.com/dongsuo/vue-terminal-emulator "vue-terminal-emulator")的jQuery复刻版


[demo](http://47.96.31.197/terminal/index.html "demo")

### 安装
> 引入lxh-terminal.min.css, jQuery.js, lxh-terminal.min.js 即可


### 使用
```javascript
var terminal = $('.terminal-container').lxhTerminal();
```

### 配置参数说明
|   键名|描述   |格式   |
| :------------ | :------------ | :------------ |
|  title |  命令窗标题 | 填入字符串格式即可，默认“Lxh Terminal”  |
| element  | 命令窗容器css选择器或jQuery对象  |如果是使用jQuery方式加载，此参数会默认使用当前jQuery dom对象。默认“.terminal-container”   |
|  messages |  默认显示的命令行消息数组 ，每个数组成员为一个对象|{content: '消息内容', style: 'info', label: 'info'} |
|  start | 命令行欢迎语  |默认“Welcome to %s.”   |
|   end|   默认消息显示结束后的内容|默认“ [{content: 'Type "help" to get a supporting command list.', style: 'system'}]”   |
| commands  | 支持的命令配置  |{description: '命令描述内容', handle: '命令执行内容'}   |
| width  | 命令窗宽度  |默认“90%”   |
| height  | 命令窗高度  |最大请不要超过800px，默认“500px”   |
| loadingTime  | 默认命令行消息显示的间隔时间  |默认“500”   |

##### message参数对象说明
message对象支持如下3个字段：
- content 必填字段，格式为字符串或一个对象
  传入内容为对象时，支持如下格式 {list: ['第一行数据', '第二行数据'...], title: '可留空'}
- style 选填字段，支持的值为普通字符串和颜色代码，如“success”、“info”、“error”、“system”、“pulple”、“primary”、“#090”等等
- label 选填字段

##### command参数对象说明
- description 必填字段，格式为字符串，当使用“help”命令时显示
- handle 必填字段，此字段支持3种格式

1.字符串格式
```javascript
{
    string: {
        description: '输入“string”运行',
            handle: '这是一条测试命令'
    },
    ...
}
```
2.数组格式（message）
```javascript
{
    array: {
        description: '输入“array”运行',
        handle: [
           {content: '第一行', style: info, label: '1'}, ...
        ]
    },
    ...
}
```
3.函数格式（可用于执行异步任务）
```javascript
{
    function: {
        description: '输入“function”运行',
        handle: function (input, resolve, reject) {
            if (input) {
               // 模拟异步任务，3秒后展示
               setTimeout(function () {
                   resolve([
                       {content: '您输入了：' + input.join(' '), style: 'info'}
                   ]);
               }, 3000)
            } else {
               reject('您没有输入任何内容');
            }
         }
     },
    ...
}
```



