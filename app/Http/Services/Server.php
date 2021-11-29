<?php


use App\Http\Services\UserService;
use App\Http\Services\MsgService;
use Swoole\WebSocket\Server as Servers;


class Server
{
    private $sys = [
        'socket_listen_address' => '0.0.0.0',  //服务器监听的地址
        'socket_listen_port' => 9507,  //服务器监听的端口
        'server_address' => '127.0.0.1', //前端页面WS连接地址，一般填写服务器地址即可
        'server_port' => 9501, //前端页面WS连接地址，填写需要连接的端口
    ];

    private $server;
    private static $userInfo;

    private $clients = array();

    public function __construct()
    {
//        require("../Http/Services/UserService.php");
        require("./start.php");


        $this->clients = array();
    }

    public function start()
    {
        //获取互斥锁 文件锁

        $this->server = new Servers ($this->sys['socket_listen_address'], $this->sys['socket_listen_port']);
        $this->server->set(array(
            'daemonize' => true,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1,
            'task_worker_num' => 8,
            'worker_num' => 1
        ));
        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $this->server->on('open', [
            $this,
            'onOpen'
        ]);

        $this->server->on('message', [
            $this,
            'onMessage'
        ]);

        $this->server->on('close', [
            $this,
            'onClose'
        ]);
        //回调
        $this->server->on('task', [
            $this,
            'broadcast'
        ]);
        $this->server->on('Finish', array(
            $this,
            'onFinish'));
        $this->server->start();
    }

    public function onOpen($ws, $request)
    {
        $msg = "新用户 {$request->fd} 加入";

//        $user = [
//            'id' => $request->fd,
//            'nickname' => '匿名用户'
//        ];

        //将连接信息存放到fd的全局数组中( $request->fd 为连接id )
//        $GLOBALS['fd'][$request->fd] = $user;
        //控制台 调试输出 可以删除
        \Illuminate\Support\Facades\Log::info($msg);
        echo $msg."\n";

        $ws->push($request->fd,$this->myResult('open', ['msg' => $msg]));
    }


    public function onMessage($ws,$request)
    {
        //获取当前连接的用户信息
//        $user = $GLOBALS['fd'][$request->fd];
        //控制台 调试输出 可以删除
        // var_dump($request->data);


        //将用户发送上来的数据格式化成数组（客户端上传的json数据）
        $data = json_decode($request->data,true);


        //判断是否登录操作（模拟的用于绑定用户信息）
        if($data['a'] == 'login'){

            //组装用户信息
//            $myUser = $user;
            self::$userInfo = $data['userInfo'];
            self::$userInfo['fd'] = $request->fd;
            $myUser['id'] = self::$userInfo['id'];
            $myUser['name'] = self::$userInfo['name'];
            $myUser['avatar'] = self::$userInfo['avatar'];
            var_dump(\App\Http\Swoole\Redis::getRedis()->del('45c48cce2e2d7fbdea1afc51c7c6ad26'));

            //将用户信息根据token存放入全局数组中

//            $GLOBALS['id'][$myUser['id']] = $myUser;
            //控制台 调试输出 可以删除

            //将用户信息根据连接id存放入全局数组中
//            $GLOBALS['fd'][$request->fd] = $myUser;
            $msg = " {$myUser['name']} 加入聊天室 ";


            echo $msg."\n";
            //获取当前所有用户信息
//            $userlist = $GLOBALS['fd'];
            UserService::onLine(self::$userInfo['id'],$request->fd,1);//修改状态
            $userlist = UserService::getUser(1);
//            echo "用户列表\n";
            //将所有用户列表推送给客户端

            $ws->push($request->fd,$this->myResult('userlist',$userlist));

            //将用户登录信息推送到所有在线客户端
            foreach ($userlist as $toUser){
                var_dump($toUser);
                // echo "用户列表\n";
                // var_dump($toUser);
                //不用推送给自己
                if($toUser['id'] != $myUser['id']) {
                    $ws->push($toUser['fd'], $this->myResult('login', ['msg' => $msg, 'userInfo' => $myUser]));
                }
            }

        }



        //客户端发送信息操作
        if($data['a'] == 'msg'){
            //获取客户端信息发送给谁 0表示 所有人
            $to_uid = $data['to_uid'];
            $uid = $data['uid'];
            //客户端发送的消息
            $msg = $data['msg'];

            //获取当前用户信息
            $myUser = UserService::userInfo($uid);

            //将信息推送给自己
            $ws->push($request->fd,$this->myResult('msg',['msg'=>$msg,'nickname'=>$myUser['name'],'avatar'=>$myUser['avatar'],'add_time'=>date('Y-m-d H:i:s')]));

            //控制台 调试输出 可以删除
            echo "自己：\n";
//            var_dump($myUser);

            if($to_uid){
                //控制台 调试输出 可以删除
//                var_dump('token:'.$to_uid);

                //获取全中的用户信息（发送给谁的信息）
                $toUser = UserService::userInfo($to_uid);

                //控制台 调试输出 可以删除
//                var_dump('ws_uid:');
//                var_dump($toUser);

                //将信息推送到指定用户客户端
            if($toUser['fd'] != 0){
                    $on_msg = [
                        'uid' => $myUser['id'],
                        'to_uid' => $to_uid,
                        'content' => $msg,
                    ];
                    MsgService::saveMsg($on_msg);
                    $ws->push($toUser['fd'],$this->myResult('msg',['msg'=>$msg,'nickname'=>$myUser['name'],'avatar'=>$myUser['avatar'],'add_time'=>date('Y-m-d H:i:s')]));
                }

            }else{
                $onUserList = UserService::onUser();

                //将信息发送给所有在线客户端
                foreach ($onUserList as $toUser){
                    //用户列表中排除自己
                    if($toUser['id'] != $myUser['id']){
                        $on_msg = [
                            'uid' => $myUser['id'],
                            'to_uid' => $toUser['id'],
                            'content' => $msg,
                        ];
                        MsgService::saveMsg($on_msg);
                        $ws->push($toUser['fd'],$this->myResult('msg',['msg'=>$msg,'nickname'=>$myUser['name'],'avatar'=>$myUser['avatar'],'add_time'=>date('Y-m-d H:i:s')]));
                    }
                }

            }

        }

        if ($data['a'] == 'close'){
            $user = $data;
            echo "close\n";
            UserService::onLine(self::$userInfo['id'],0,2);//修改状态

            $ws->close($request->fd);
        }


//        echo $msg."\n";
    }

    public function onFinish($server, $task_id, $data)
    {
        UserService::onLineList();//修改状态

    }

    public function broadcast($server, $task_id, $from_id, $frame)
    {

    }

    public function onClose($ws,$ws_uid)
    {


        $msg = "{$ws_uid} 关闭连接";
//
//
//        //获取断开连接的用户信息
//        $myUser = $GLOBALS['fd'][$ws_uid];
//        if($myUser['token']){
//            $msg = $myUser['nickname'].'退出聊天室';
//
//            //将断开连接的用户信息推送给所有在线客户端
//            foreach ($GLOBALS['fd'] as $toUser) {
//                //用户列表中排除自己
//                if ($toUser['id'] != $myUser['id']) {
//                    $ws->push($toUser['id'], $this->$this->myResult('logout', ['msg' => $msg, 'userInfo' => $myUser]));
//                }
//
//            }
//            //将全局用户数组（token）中删除断开连接的用户
//            unset($GLOBALS['tokens'][$myUser['token']]);
//        }
//
//        //将全局用户数组（连接id）中删除断开连接的用户
//        unset($GLOBALS['fd'][$ws_uid]);

        //控制台 调试输出 可以删除
        echo $msg."\n";
    }

    public function myResult($type,$data,$code=200,$msg='请求成功'){
        $result = [
            'code'=>$code,
            'msg'=>$msg,
            'type'=>$type,
            'data'=>$data
        ];
        return json_encode($result);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        echo 456;
    }



}

$Server = new Server ();
$Server->start();




















