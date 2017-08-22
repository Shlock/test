<?php 
namespace YiZan\Models;

class GoodsType extends Base 
{
    protected $visible = ['id', 'name', 'ico', 'sort', 'goods'];
    public function goods(){
        return $this->hasMany('YiZan\Models\Goods', 'type_id', 'id');
    }
}
