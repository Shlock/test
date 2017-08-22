<?php 
namespace YiZan\Models\Www;

class GoodsType extends \YiZan\Models\GoodsType
{
    protected $visible = ['id', 'name', 'goods'];
    public function goods(){
        return $this->hasMany('YiZan\Models\Www\Goods', 'type_id', 'id');
    }
}
