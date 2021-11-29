<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use App\Models\MsgModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    //
    public function  index()
    {
        $data = UserService::getUser();
        $msg = MsgModel::query()
            ->get()->toArray();

        return view('index',['user'=>$data,'msg'=>$msg]);
    }

    public function login(Request $request)
    {
        $name = $request->post('nickname');
        $avatar = $request->post('avatar');
        $token = $request->post('token');
        if (!auth()->attempt(['name'=>$name,'password'=>'123456'])){
            $data = [
                'name'=>$name,
                'password'=>bcrypt(123456),
                'avatar'=>'/static/'.$avatar,
                'token'=>$token,
                'time'=>time()
            ];
            UserModel::query()->create($data);
            auth()->attempt(['name'=>$name,'password'=>'123456']);
        }

        if (auth()->user()){
            $res = UserModel::query()->where(['name'=>$name])->first()->toArray();
            return json_encode(['status'=>100,'msg'=>'登陆成功','data'=>$res]);
        }

    }

}
