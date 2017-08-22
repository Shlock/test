<?php 
namespace YiZan\Models;

/**
 * 车型下的车系
 */
class CarSeries extends Base 
{
	protected $visible = ['id','brand_id','name','pinyin','is_hot','listorder','initials','brand'];
    protected  $appends = ['initials'];

    public function getInitialsAttribute() {
        if (!isset($this->attributes['pinyin'])) {
            return  '';
        }
        return strtoupper(substr($this->attributes['pinyin'],0,1));
    }
    /*车型*/

	public function brand(){

        return $this->belongsTo('YiZan\Models\CarBrand', 'brand_id', 'id');

    }
}