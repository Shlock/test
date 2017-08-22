<?php namespace YiZan\Models\Buyer;

class GoodsTagRelated extends \YiZan\Models\GoodsTagRelated { 
	protected $visible = ['goods', 'tag'];

	public function goods(){
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }

    public function tag(){
        return $this->belongsTo('YiZan\Models\GoodsTag', 'tag_id', 'id');
    }
 
}
