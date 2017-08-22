<?php
namespace YiZan\Http\Controllers\Api\Buyer\Shopping;

use YiZan\Services\ShoppingCartService;
use YiZan\Http\Controllers\Api\Buyer\BaseController;
use Lang, Validator;

/**
 * 服务标签
 */
class CartController extends BaseController
{
    /**
     * 加入购物车
     */
    public function save() {
        $result = ShoppingCartService::save(
            $this->userId

        );
        $this->output($result);
    }
}