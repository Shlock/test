<?php 
namespace YiZan\Models;

class SellerCate extends Base 
{
   	//protected $visible = ['id', 'type', 'name', 'sort', 'status', 'seller', 'logo'];

   	public function seller(){
        return $this->hasMany('YiZan\Models\SellerCateRelated', 'cate_id', 'id');
    }

    public function childs(){
    	return $this->hasMany('YiZan\Models\SellerCate', 'pid', 'id');
    }
}
