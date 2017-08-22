<?php namespace YiZan\Models;

class SellerMoneyLog extends Base {
	const TYPE_ORDER_PAY 		= 'order_pay';//收入
	const TYPE_ORDER_CONFIRM 	= 'order_confirm';//收入
	const TYPE_ORDER_REFUND 	= 'order_refund';//收入
	const TYPE_APPLY_WITHDRAW 	= 'apply_withdraw';//支出
	const TYPE_WITHDRAW_SUCCESS = 'withdraw_success';//支出
	const TYPE_WITHDRAW_ERROR 	= 'withdraw_error';//收入
	const TYPE_DELIVERY_MONEY 	= 'delivery_money';//货到付款抽成 支出
	const TYPE_SELLER_RECHARGE 	= 'seller_recharge';//商家付款 收入

	protected $appends = array(
        'typeStr',
    );

    public function getTypeStrAttribute() {
        $type = $this->attributes['type'];
        if (in_array($type, ['order_refund','order_pay','order_confirm','withdraw_error','seller_recharge'])){
            $typeStr = '收入';            
        }else{
            $typeStr = '支出';   
        }
        return $typeStr;
    }
}