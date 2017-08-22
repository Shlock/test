<?php namespace YiZan\Services;
use YiZan\Models\ShoppingCart;
use YiZan\Models\Goods;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;

class ShoppingCartService extends BaseService
{
    /**
     * 加入购物车
     * @param int $userId 会员编号
     */
	public static function save($userId){
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.add')
        ];




        return $result;
    }
}