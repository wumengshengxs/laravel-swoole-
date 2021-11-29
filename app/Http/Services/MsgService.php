<?php


namespace App\Http\Services;


use App\Models\MsgModel;

class MsgService
{

    /**
     * 用户信息
     * @param $params
     * Username 无梦生
     * Date 2021/5/8
     */
    public static function saveMsg($params)
    {
         MsgModel::query()->create($params);
    }

    /**
     * 批量添加
     * @param $params
     * Username 无梦生
     * Date 2021/5/8
     */
    public static function saveMgsAll($params)
    {
        foreach ($params as $val){
            MsgModel::query()->create($val);
        }
    }

}
