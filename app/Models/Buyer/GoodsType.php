<?php 
namespace YiZan\Models\Buyer;

class GoodsType extends \YiZan\Models\GoodsType
{
    protected $visible = ['id', 'name', 'goods'];
    public function goods(){
        return $this->hasMany('YiZan\Models\Buyer\Goods', 'type_id', 'id');
    }
}
