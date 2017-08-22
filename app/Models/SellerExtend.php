<?php namespace YiZan\Models;

class SellerExtend extends Base {
	protected $primaryKey = 'seller_id';

	protected $visible = ['seller_id','lock_money','total_money','money','use_money', 'goods_avg_price', 'order_count', 'creditRank', 'comment_total_count', 'comment_good_count', 
		'comment_neutral_count', 'comment_bad_count', 'comment_specialty_avg_score', 'comment_communicate_avg_score', 
		'comment_punctuality_avg_score'];

	public function creditRank(){
        return $this->belongsTo('YiZan\Models\SellerCreditRank', 'credit_rank_id', 'id');
    }
}
