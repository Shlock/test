<?php namespace YiZan\Models;
use YiZan\Models\Order;
use YiZan\Utils\Time;

class SellerStaff extends Base {
    protected $visible = [
        'id', 
        'name',
        'mobile',
        'avatar',
        'photos',
        'brief',
        'address',
        'map_point',
        'map_point_str',
        'map_pos',
        'map_pos_str',
        'status',
        'extend',
        'province',
        'city',
        'area',
        'sort',
        'seller',
        'user',
        'collect',
        'sex',
        'birthday',
        'age',
        'sexStr',
        'now_order',
        'goods',
        'authentication',
        'recruitment',
        'hobbies',
        'constellation',
        'business_district',
        'district',
        'districtName',
        'job_number',
        'card_number',
        'begin_time',
        'end_time',
        'order_status',
        'type',
        'authenticate_img',
        'seller_id',
		'user_id'
    ];

    protected $appends = array('age','sexStr');

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }

    public function extend(){
        return $this->belongsTo('YiZan\Models\SellerStaffExtend', 'id', 'staff_id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\User','user_id','id');
    }

    public function collect(){
        return $this->belongsTo('YiZan\Models\UserCollectStaff', 'id', 'staff_id');
    }

    public function province()
    {
        return $this->belongsTo('YiZan\Models\Region', 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo('YiZan\Models\Region', 'area_id', 'id');
    }

    public function district()
    {
        return $this->hasMany('YiZan\Models\SellerStaffDistrict', 'staff_id', 'id');
    }

    public function getMapPointAttribute() {
        if (!isset($this->attributes['map_point_str'])) {
            return ['x'=>0,'y'=>0];
        }
        $point = explode(',', $this->attributes['map_point_str']);
        return ['x'=>$point[0],'y'=>$point[1]];
    }

    public function getMapPosAttribute() {
        $pos    = [];
        $points = !isset($this->attributes['map_pos_str']) ? [] : explode('|', $this->attributes['map_pos_str']);
        foreach ($points as $point) {
            $point  = explode(',', $point);
            $pos[]  = ['x'=>$point[0],'y'=>$point[1]];
        }
        return $pos;
    }

    public function getPhotosAttribute() {
        if (!isset($this->attributes['photos']) || empty($this->attributes['photos'])) {
            return [];
        }
        return explode(',', $this->attributes['photos']);
    }

    public function getAgeAttribute() {
        if (!isset($this->attributes['birthday']) || 
            $this->attributes['birthday'] > UTC_DAY) {
            return 0;
        }
        
        $birth_year  = Time::toDate($this->attributes['birthday'], 'Y');
        $birth_month = Time::toDate($this->attributes['birthday'], 'm');
        $birth_date  = Time::toDate($this->attributes['birthday'], 'd');

        $now_year    = Time::toDate(UTC_DAY, 'Y');
        $now_month   = Time::toDate(UTC_DAY, 'm');
        $now_date    = Time::toDate(UTC_DAY, 'd');

        $age = $now_year - $birth_year;
        if ($now_year > $birth_year) {
            if ($now_month == $birth_month) {
                if ($now_date > $birth_date) {  
                    $age++;  
                }
            } elseif ($now_month > $birth_month) {  
                $age++;  
            }
        }
        return $age;
    }

    public function getSexStrAttribute() {
        if (!isset($this->attributes['sex'])) {
            return '';
        }
        return $this->attributes['sex'] == 1 ? '男' : '女';
    }
}
