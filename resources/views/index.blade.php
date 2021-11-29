<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>聊天室</title>
    <link rel="shortcut icon" href="/static/favicon.png">
    <link rel="icon" href="/static/favicon.png" type="image/x-icon">
    <link type="text/css" rel="stylesheet" href="/static/css/style.css">
    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
</head>

<body>
<div class="chatbox">
    <div class="chat_top fn-clear">
        <div class="logo"><img src="/static/images/logo.png" width="190" height="60"  alt=""/></div>
        <div class="uinfo fn-clear">
            <div class="uface"><img src="/static/images/hetu.jpg" width="40" height="40"  alt=""/></div>
            <div class="uname">
                <span>游客</span><i class="fontico down"></i>
                <input type="hidden" id="id" value="">
                <ul class="managerbox">
                    <!--<li><a href="#"><i class="fontico lock"></i>修改密码</a></li>-->
                    <li><a onclick="userAdd()"><i class="fontico logout"></i>登陆</a></li>
                    <li><a onclick="delAdd()"><i class="fontico logout"></i>退出登录</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="chat_message fn-clear">
        <div class="chat_left">
            <div class="message_box" id="message_box">
                <!--<div class="msg_item fn-clear">-->
                <!--<div class="uface"><img src="images/53f44283a4347.jpg" width="40" height="40"  alt=""/></div>-->
                <!--<div class="item_right">-->
                <!--<div class="msg">近日，TIOBE发布了2014年9月的编程语言排行榜，Java、C++跌至历史最低点，前三名则没有变化，依旧是C、Java、Objective-C。</div>-->
                <!--<div class="name_time">猫猫 · 3分钟前</div>-->
                <!--</div>-->
                <!--</div>-->
                <!---->
                <!--<div class="msg_item fn-clear">-->
                <!--<div class="uface"><img src="images/53f442834079a.jpg" width="40" height="40"  alt=""/></div>-->
                <!--<div class="item_right">-->
                <!--<div class="msg">(Visual) FoxPro, 4th Dimension/4D, Alice, APL, Arc, Automator, Awk, Bash, bc, Bourne shell, C++CLI, CFML, cg, CL (OS/400), Clean, Clojure, Emacs Lisp, Factor, Forth, Hack, Icon, Inform, Io, Ioke, J, JScript.NET, LabVIEW, LiveCode, M4, Magic, Max/MSP, Modula-2, Moto, NATURAL, OCaml, OpenCL, Oz, PILOT, Programming Without Coding Technology, Prolog, Pure Data, Q, RPG (OS/400), S, Smalltalk, SPARK, Standard ML, TOM, VBScript, Z shell</div>-->
                <!--<div class="name_time">白猫 · 1分钟前</div>-->
                <!--</div>-->
                <!--</div>-->
                <!---->
                <!--<div class="msg_item fn-clear">-->
                <!--<div class="uface"><img src="images/hetu.jpg" width="40" height="40"  alt=""/></div>-->
                <!--<div class="item_right">-->
                <!--<div class="msg own">那个统计表也不能说明一切</div>-->
                <!--<div class="name_time">河图 · 30秒前</div>-->
                <!--</div>-->
                <!--</div>-->

            </div>
            <div class="write_box">
                <textarea id="message" name="message" class="write_area" placeholder="说点啥吧... （ps: 快捷键Ctrl+Enter键可以发送消息）"></textarea>
                <input type="hidden" name="fromname" id="fromname" value="" />
                <input type="hidden" name="to_uid" id="to_uid" value="0">
                <div class="facebox fn-clear">
                    <div class="expression"></div>
                    <div class="chat_type" id="chat_type">群聊</div>
                    <button name="" class="sub_but">提 交</button>
                </div>
            </div>
        </div>
        <div class="chat_right">
            <ul class="user_list" title="双击用户私聊">
                <li class="fn-clear selected"><em>所有用户</em></li>
                @foreach($user as $val)
                <li class="fn-clear" data-id="{{$val['id']}}">
                    <span>
                        <img src="{{$val['avatar']}}" width="30" height="30"  alt=""/>
                    </span>
                    <em>{{$val['name']}}</em>
                    <small class="online" title="在线"></small>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>



<link href="/static/toastr/toastr.min.css" rel="stylesheet">
<script src="/static/toastr/toastr.min.js"></script>
<script type="text/javascript">

    //基本配置
    // var messageOpts = {
    //     "positionClass" : "toast-top-center",//弹出窗的位置
    // };
    // toastr.options = messageOpts;
    //
    //
    // //获取用户信息
    var nickname = getName();
    var id = '<?= $_GET['id']?>';
    console.log(id)
    //
    //
    var index = Math.floor((Math.random()*734));
    if(!index) index = 1;
    //
    var userInfo = {
        'token':getToken(),
        'nickname':nickname,
        'avatar':"images/75X75/gs ("+index+").gif",
        'id':0,
    }


    // localStorage.removeItem("dat");

    //连接ws
        var ws = new WebSocket("wss://ttapi.kktpt.com:1216");
        ws.onopen = function (evt) {
            console.log(evt);
        }


    function userAdd(){
        var nickname = getName();

        nickname = prompt("请输入您的昵称",nickname);
        var data = { 'route':'join','send':{"user_id":id,"room_id":"3"}};
        ws.send(JSON.stringify(data));

        // if(!nickname){
        //     toastr.success('返回登陆');
        //     return
        // }
        // userInfo['nickname'] =nickname;
        //
        // $.ajax({
        //     url: '/login',
        //     type: 'post',
        //     data: userInfo,
        //     dataType: 'json',
        //     success: function(res) {
        //         if (res.status == 100){
        //             toastr.success('登陆成功');
        //             $('.uinfo .uface img').attr('src',res.data.avatar);
        //             $('.uinfo .uname span').html(res.data.name);
        //             $('#id').val(res.data.id)
        //             userInfo['id'] = res.data.id;
        //             var data = { 'a':'login','userInfo':res.data };
        //             ws.send(JSON.stringify(data));
        //         }
        //     },
        // });

    }

    function delAdd()
    {
        var data = { 'a':'close','userInfo':userInfo };
        ws.send(JSON.stringify(data));
    }


    //
    ws.onclose = function (evt) {
        toastr.success('连接关闭');
    }

    // ws.onerror = function (evt,e) {
    //     console.log(e);
    // }

    ws.onmessage = function (evt) {

        var info = JSON.parse(evt.data);
        var data = info.data;
        console.log(data)

        //登录
        if(info.type == 'login'){
            toastr.success(data.msg);

            var html ='<li class="fn-clear" data-id="'+data.userInfo['id']+'"><span><img src="'+data.userInfo.avatar+'" width="30" height="30" alt=""></span><em>'+data.userInfo.name+'</em><small class="online" title="在线"></small></li>';
            $(".user_list").append(html);
        }
        if(data.type == 'join'){
           console.log('注册成功')
        }

        //断开连接
        if(info.type == 'logout'){
            toastr.success(data.msg);
            console.log('退出聊天:'+data.msg);
            console.log(data.userInfo);
            $(".user_list li[data-id='"+data.userInfo.token+"']").remove();

        }

        //用户列表
        if(info.type == 'userlist'){
            var box = '';//容器
            $.each(data,function (k,v) {
                if(v.id != userInfo.id){
                    box +='<li class="fn-clear" data-id="'+v.id+'"><span><img src="'+v.avatar+'" width="30" height="30" alt=""></span><em>'+v.name+'</em><small class="online" title="在线"></small></li>';
                }
            });

            console.log(box);
            $(".user_list").html(box);


        }

        //通知信息
        if(info.type == 'notice'){
            toastr.success(data.msg);
        }

        //聊天信息
        if(info.type == 'msg'){
            console.log('信息接收成功')

            // addMessage(data.msg,data.nickname,data.avatar,data.add_time);
        }

    }

    //页面操作
    $(document).ready(function(e) {

        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
        $('.uname').hover(
            function(){
                $('.managerbox').stop(true, true).slideDown(100);
            },
            function(){
                $('.managerbox').stop(true, true).slideUp(100);
            }
        );

        var fromname = $('#fromname').val();
        var to_uid   = 0; // 默认为0,表示发送给所有用户
        var to_uname = '';


        $('.user_list').on('dblclick','li',function(){
            to_uname = $(this).find('em').text();
            to_uid   = $(this).attr('data-id');
            if(to_uname == fromname){
                alert('您不能和自己聊天!');
                return false;
            }
            if(to_uname == '所有用户'){
                $("#toname").val('');
                $('#chat_type').text('群聊');
            }else{
                $("#toname").val(to_uid);
                $('#chat_type').text('您正和 ' + to_uname + ' 聊天');
            }
            $(this).addClass('selected').siblings().removeClass('selected');
            $('#message').focus().attr("placeholder", "您对"+to_uname+"说：");
            return false;
        });

        $('.sub_but').click(function(event){
            var msg = $("#message").val();
            var data = { 'route':'msg','send':{"user_id":id,"room_id":"3"},
                "msg":{"message":msg,"type":"1"}};
            ws.send(JSON.stringify(data));

            // sendMessage(event, fromname, to_uid, to_uname);
            // return false;
        });

        /*按下按钮或键盘按键*/
        $("#message").keydown(function(event){
            var e = window.event || event;
            var k = e.keyCode || e.which || e.charCode;
            //按下ctrl+enter发送消息

            if((event.ctrlKey && (k == 13 || k == 10) )){
                sendMessage(event, fromname, to_uid, to_uname);
            }
        });

    });

    /**
     * 获取token
     * @returns {string}
     */
    function getToken() {
        var d = new Date().getTime();
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (d + Math.random()*16)%16 | 0;
            d = Math.floor(d/16);
            return (c=='x' ? r : (r&0x3|0x8)).toString(16);
        });
        return uuid.replace(/-/g,'');
    }

    /**
     * 发送信息
     * @param event
     * @param from_name
     * @param to_uid
     * @param to_uname
     */
    function sendMessage(event, from_name, to_uid, to_uname){
        console.log(to_uid)
        console.log(to_uname)
        var uid = $("#id").val();
        var msg = $("#message").val();
        if(to_uname != ''){
            msg = '（悄悄话）： ' + msg;
        }

        var data = { 'route':'msg','send':{"user_id":id,"room_id":"3"},
            "msg":{"message":msg,"type":"1"}};
        // var data = { 'a':'msg','uid':uid,'to_uid':to_uid,'msg':msg };
        ws.send(JSON.stringify(data));

        $("#message").val('');
    }

    /**
     * 添加信息到box
     */
    function addMessage(msg,from_name,avatar,add_time) {
        var htmlData =   '<div class="msg_item fn-clear">'
            + '   <div class="uface"><img src="'+avatar+'" width="40" height="40"  alt=""/></div>'
            + '   <div class="item_right">'
            + '     <div class="msg own">' + msg + '</div>'
            + '     <div class="name_time">' + from_name + ' · '+add_time+'</div>'
            + '   </div>'
            + '</div>';
        $("#message_box").append(htmlData);
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
    }

    /**
     * 随机姓名
     */
    function getName(){
        var familyNames = new Array(
            "赵", "钱", "孙", "李", "周", "吴", "郑", "王", "冯", "陈",
            "褚", "卫", "蒋", "沈", "韩", "杨", "朱", "秦", "尤", "许",
            "何", "吕", "施", "张", "孔", "曹", "严", "华", "金", "魏",
            "陶", "姜", "戚", "谢", "邹", "喻", "柏", "水", "窦", "章",
            "云", "苏", "潘", "葛", "奚", "范", "彭", "郎", "鲁", "韦",
            "昌", "马", "苗", "凤", "花", "方", "俞", "任", "袁", "柳",
            "酆", "鲍", "史", "唐", "费", "廉", "岑", "薛", "雷", "贺",
            "倪", "汤", "滕", "殷", "罗", "毕", "郝", "邬", "安", "常",
            "乐", "于", "时", "傅", "皮", "卞", "齐", "康", "伍", "余",
            "元", "卜", "顾", "孟", "平", "黄", "和", "穆", "萧", "尹"
        );
        var givenNames = new Array(
            "子璇", "淼", "国栋", "夫子", "瑞堂", "甜", "敏", "尚", "国贤", "贺祥", "晨涛",
            "昊轩", "易轩", "益辰", "益帆", "益冉", "瑾春", "瑾昆", "春齐", "杨", "文昊",
            "东东", "雄霖", "浩晨", "熙涵", "溶溶", "冰枫", "欣欣", "宜豪", "欣慧", "建政",
            "美欣", "淑慧", "文轩", "文杰", "欣源", "忠林", "榕润", "欣汝", "慧嘉", "新建",
            "建林", "亦菲", "林", "冰洁", "佳欣", "涵涵", "禹辰", "淳美", "泽惠", "伟洋",
            "涵越", "润丽", "翔", "淑华", "晶莹", "凌晶", "苒溪", "雨涵", "嘉怡", "佳毅",
            "子辰", "佳琪", "紫轩", "瑞辰", "昕蕊", "萌", "明远", "欣宜", "泽远", "欣怡",
            "佳怡", "佳惠", "晨茜", "晨璐", "运昊", "汝鑫", "淑君", "晶滢", "润莎", "榕汕",
            "佳钰", "佳玉", "晓庆", "一鸣", "语晨", "添池", "添昊", "雨泽", "雅晗", "雅涵",
            "清妍", "诗悦", "嘉乐", "晨涵", "天赫", "玥傲", "佳昊", "天昊", "萌萌", "若萌"
        );


        var i = Math.floor((Math.random()*familyNames.length));
        var familyName = familyNames[i];
        var j = Math.floor((Math.random()*givenNames.length));
        var givenName = givenNames[i];
        var name = familyName + givenName;


        return name;
    }



</script>
</body>
</html>
