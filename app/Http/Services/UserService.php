<?php


namespace App\Http\Services;


use App\Models\UserModel;

class UserService
{

    /**
     * 用户
     * Username 无梦生
     * Date 2021/5/8
     * @return array
     */
    public static function getUser($status = '')
    {
        $query = UserModel::query();

        if ($status != ''){
            $query->where(['status'=>$status]);
        }
        $data = $query->get()->toArray();
        return $data;
    }

    /**
     * 登陆状态变更
     * @param $id
     * @param int $fd
     * @param int $type
     * Username 无梦生
     * Date 2021/5/8
     * @return int
     */
    public static function onLine($id,$fd = 0,$type = 1)
    {
        $data = UserModel::query()->where(['id'=>$id])->update(['status'=>$type,'fd'=>$fd]);

        return $data;

    }

    /**
     * 清空全部登陆
     * Username 无梦生
     * Date 2021/5/8
     */
    public static function onLineList()
    {
        UserModel::query()->update(['status'=>2,'fd'=>0]);
    }

    /**
     * 获取用户信息
     * @param $id
     * Username 无梦生
     * Date 2021/5/8
     * @return array
     */
    public static  function userInfo($id)
    {
        $data = UserModel::query()->where(['id'=>$id])->first()->toArray();

        return $data;

    }

    /**
     * 获取所有在线的用户
     * Username 无梦生
     * Date 2021/5/8
     */
    public  static function onUser()
    {
        $query = UserModel::query()
            ->where(['status'=>1])
            ->where('fd','!=',0);


        $data = $query->get()->toArray();

        return $data;
    }

    public static function onLineS()
    {
        $data = UserModel::query()->update(['status'=>2,'fd'=>0]);

    }

}
