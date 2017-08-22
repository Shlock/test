<?php 
namespace YiZan\Models;

/**
 * 车型
 */
class CarBrand extends Base 
{
	protected $visible = ['id','name','logo','ename','pinyin','initials','series'];
    protected  $appends = ['initials'];

    public function getInitialsAttribute() {
        if (!isset($this->attributes['pinyin'])) {
            return  '';
        }
        return strtoupper(substr($this->attributes['pinyin'],0,1));
    }
    public function series(){
        return $this->belongsTo('YiZan\Models\CarSeries', 'id', 'brand_id');
    }
}