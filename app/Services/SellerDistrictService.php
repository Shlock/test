<?php namespace YiZan\Services;
 
use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaffDistrict;
use YiZan\Models\SellerStaff;
use Illuminate\Database\Query\Expression;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Validator, Lang;

/**
 * 商圈服务
 */
class SellerDistrictService extends BaseService { 

	/**
	 * [save 保存商圈]
	 * @param  [int] 	$sellerId   [服务&服务人员编号]
	 * @param  [int] 	$districtId [商圈编号]
	 * @param  [string] $name       [商圈名称]
     * @param  [string] $mapPoint   [商圈坐标]
     * @param  [string] $mapPos     [商圈范围]
	 * @param  [int] 	$provinceId [省编号]
	 * @param  [int] 	$cityId     [市编号]
     * @param  [int]    $areaId     [区编号]
     * @param  [string] $address [定位地址]
	 * @return [array]              [结果代码]
	 */
	public static function save($sellerId, $districtId, $name,$mapPoint, $mapPos, $provinceId, $cityId, $areaId, $address) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.add')
        );

        $rules = array(
        	'name'			=> ['required'],
            'provinceId'    => ['min:1'],
            'cityId'        => ['min:1'],
            'areaId'        => ['min:1'],  
            'mapPos'        => ['required'], 
        );

        $messages = array(
        	'name.required'		=> 50401,	//请输入商圈名称
            'provinceId.min'    => 50402,   // 请选择所在省
            'cityId.min'        => 50403,   // 请选择所在市
            'areaId.min'        => 50404,   // 请选择所在县  
            'mapPos.required'   => 50405,    // 请选择服务范围 
        );

        $validator = Validator::make([
                'provinceId'    => $provinceId,
                'cityId'        => $cityId,
                'areaId'        => $areaId,  
                'mapPos'        => $mapPos, 
            ], $rules, $messages);
        
        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint) {
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($mapPos);
        
        if (!$mapPos) {
            $result['code'] = 50406;    // 服务范围错误
            return $result;
        }

        if($districtId > 0){
        	$seller_district = SellerDistrict::find($districtId);
    	} else {
        	$seller_district = new SellerDistrict();
    	} 
        $seller_district->seller_id 	= $sellerId;
        $seller_district->province_id 	= $provinceId;
        $seller_district->city_id 		= $cityId;
        $seller_district->area_id 		= $areaId;
        $seller_district->name          = $name;
        $seller_district->map_point     = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"); 
        $seller_district->map_point_str =  $mapPoint;
        $seller_district->map_pos 		= DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller_district->map_pos_str 	= $mapPos["str"];
        $seller_district->address       = $address;

        try{
        	$seller_district->save();
        }catch(Exception $e ){
        	$result['code'] = 50407;
        }

        return $result;
	} 

	/**
	 * [delete 删除商圈]
	 * @param  [int] $sellerId   [服务人员编号]
	 * @param  [int] $districtId [商圈编号]
	 * @return [array]           [结果代码]
	 */
	public static function delete($sellerId, $districtId){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.delete')
        ); 

        try{
	        SellerDistrict::where('seller_id', $sellerId)
	        			->where('id', $districtId)
	        			->delete();
        }catch(Exception $e){
        	$result['code'] = 50508;
        }
        return $result;
	} 

    /**
     * [lists 商圈列表]
     * @param  [int] $sellerId      [编号]
     * @param  [string] $keywords   [关键字]
     * @param  [int] $page          [分页]
     * @param  [int] $pageSize      [分页数量]
     * @return [array]              [数组]
     */
    public static function lists($sellerId, $keywords, $page, $pageSize){
        $list = SellerDistrict::where('seller_id', $sellerId)
                            ->where('name', 'like', '%'.$keywords.'%');
        $totalCount = $list->count();
        
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->with("province","city","area")
            ->get()
            ->toArray();
         
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [get 获取商圈]
     * @param  [int] $sellerId [服务人员编号]
     * @param  [int] $districtId       [编号]
     * @return [array]           [商圈信息]
     */
    public static function get($sellerId, $districtId){ 
        $district = SellerDistrict::where('seller_id', $sellerId)
                                ->where('id', $districtId)
                                ->first();
        return $district ? $district->toArray() : null;
    }

    /**
     * [districtlists 小区列表]
     * @param  [type] $name   [小区名称]
     * @param  [type] $provinceId [市区Id]
     * @param  [type] $cityId     [县ID]
     * @param  [type] $addressId  [街道ID]
     * @param  [type] $page       [分页]
     * @param  [type] $pageSize   [分页数量]
     * @return [array]            [数组]
     */
    public static function districtlists($name=null, $provinceId=null, $cityId=null, $address=null, $page, $pageSize){
        // DB::connection()->enableQueryLog();

        $list = SellerDistrict::select();
        if(!empty($name))
            $list->where('name', 'like', '%'.$name.'%');
        if(!empty($provinceId))
            $list->where('province_id', $provinceId);
        if(!empty($cityId))
            $list->where('city_id', $cityId);
        if(!empty($address))
            $list->where('address', 'like', '%'.$address.'%');   
        $list->orderBy('id','desc');          

        $totalCount = $list->count();
       
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->with("province","city","area","districtStaffCount")
            ->get()
            ->toArray();

        // dd(DB::getQueryLog());
        return ["list"=>$list, "totalCount"=>$totalCount];
    }


    /**
     * [create 新建小区]
     * @param  [int]    $sellerId   [服务&服务人员编号]
     * @param  [int]    $districtId [商圈编号]
     * @param  [string] $name       [商圈名称]
     * @param  [string] $mapPoint   [商圈坐标]
     * @param  [string] $mapPos     [商圈范围]
     * @param  [int]    $provinceId [省编号]
     * @param  [int]    $cityId     [市编号]
     * @param  [int]    $areaId     [区编号]
     * @param  [string] $address [定位地址]
     * @return [array]              [结果代码]
     */
    public static function districtcreate($districtId, $sellerId, $provinceId, $cityId, $areaId, $address, $name, $row, $mapPoint, $mapPos) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.add')
        );

        $rules = array(
            'provinceId'    => ['min:1'],
            'cityId'        => ['min:1'],
            'areaId'        => ['min:1'], 
            'address'       => ['required'],
            'name'          => ['required'],
        );

        $messages = array(
            'provinceId.min'    => 50402,   // 请选择所在省
            'cityId.min'        => 50403,   // 请选择所在市 
            'address.required'  => 50401,   //请输入商圈名称
            'name.required'     => 50401,   //请输入商圈名称
        );

        $validator = Validator::make([
                'provinceId'    => $provinceId,
                'cityId'        => $cityId,
                'address'       => $address,
                'name'          => $name,
            ], $rules, $messages);

        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint) {
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($mapPos);
        
        if (!$mapPos) {
            $result['code'] = 50406;    // 服务范围错误
            return $result;
        }

        if($districtId > 0){
            $seller_district = SellerDistrict::find($districtId);
        } else {
            $seller_district = new SellerDistrict();
        } 
        $seller_district->seller_id     = $sellerId;
        $seller_district->province_id   = $provinceId;
        $seller_district->city_id       = $cityId;
        $seller_district->area_id       = $areaId;
        $seller_district->name          = $name;
        $seller_district->map_point     = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"); 
        $seller_district->map_point_str = $mapPoint;
        $seller_district->map_pos       = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller_district->map_pos_str   = $mapPos["str"];
        $seller_district->address       = $address;
        $seller_district->row           = $row;


        try{
            $seller_district->save();
        }catch(Exception $e ){
            $result['code'] = 50407;
        }

        return $result;
    } 

    /**
     * 删除小区
     * @param  [type] $districtId [小区ID]
     * @return [type]             [数组]
     */
    public static function districtdelete($districtId) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.delete')
        ); 

        try{
            SellerDistrict::where('id', $districtId)
                        ->delete();
        }catch(Exception $e){
            $result['code'] = 50508;
        }
        return $result;
    }

    /**
     * 查看员工
     * @param  [type] $districtId [小区ID]
     * @param  [type] $name       [员工姓名]
     * @param  [type] $mobile     [员工电话]
     * @param  [type] $page       [分页]
     * @param  [type] $pageSize   [分页大小]
     * @return [array]            [返回数组]
     */
    public static function lookstaff($districtId, $name=null, $mobile=null, $page, $pageSize) {
        $list = SellerStaff::select('seller_staff.*')
                        ->join('seller_staff_district', function($join) use($districtId) {
                            $join->on('seller_staff_district.staff_id', '=', 'seller_staff.id')
                                ->where('seller_staff_district.district_id', '=', $districtId);
                        });     

        if (!empty($name)) {
            $list->where('seller_staff.name', 'like', '%'.$name.'%');
        }
        if(!empty($mobile)) {
            $list->where('seller_staff.mobile', 'like', '%'.$mobile.'%');
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

     /**
     * 查看小区所有员工
     * @param  [type] $districtId [小区ID]
     * @return [array]            [返回数组]
     */
    public static function lookstaffall($districtId) {
        $list = SellerStaff::select('seller_staff.*')
                        ->join('seller_staff_district', function($join) use($districtId) {
                            $join->on('seller_staff_district.staff_id', '=', 'seller_staff.id')
                                ->where('seller_staff_district.district_id', '=', $districtId);
                        });     

        $totalCount = $list->count();
        $list = $list->get()
                    ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [getstaff 获取小区员工列表]
     * @param  [type] $districtId  [小区ID]
     * @param  [type] $name        [员工姓名]
     * @param  [type] $mobile      [员工电话]
     * @param  [type] $responsible [是否当前小区 1=负责 2=不负责]
     * @param  [type] $page        [分页]
     * @param  [type] $pageSize    [分页大小]
     * @return [type]              [返回数组]
     */
    public static function getstaff($districtId, $name=null, $mobile=null, $responsible=2, $page, $pageSize) {
        $list = SellerStaff::select('seller_staff.*');
        if($responsible == 1){
            $list->addSelect('seller_district.name as districtName')
                ->join('seller_staff_district', function($join) use($districtId) {
                    $join->on('seller_staff_district.staff_id', '=', 'seller_staff.id')
                        ->where('seller_staff_district.district_id', '=', $districtId);
                })
                ->join('seller_district','seller_district.id', '=', 'seller_staff_district.district_id'); 
        }else{
            $list->whereNotExists(function($query) use($districtId) {
                    $seller_staff_table = DB::getTablePrefix().'seller_staff';
                    $query->select(DB::raw(1))
                          ->from('seller_staff_district')
                          ->where('seller_staff_district.district_id', '=', $districtId)
                          ->where('seller_staff_district.staff_id', '=', new Expression("{$seller_staff_table}.id"));
                });
        }

        if (!empty($name)) {
            $list->where('seller_staff.name', 'like', '%'.$name.'%');
        }
        if(!empty($mobile)) {
            $list->where('seller_staff.mobile', 'like', '%'.$mobile.'%');
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->groupBy('seller_staff.id')
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }


    /**
     * [save 添加人员到小区] 
     * @param  [int]    $staffId     [员工编号]
     * @param  [int]    $districtIds  [小区编号]
     * @return [array]               [结果代码]
     */
    public static function addresponsible($sellerId, $staffId, $districtId) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.add')
        );

        if(count($districtId) < 1) {
            $result['code'] = 10001;//小区不能为空
            return $result;
        }

        if(count($staffId) < 1) {
            $result['code'] = 10006;//服务人员不能为空
            return $result;
        }

        $district_count = SellerDistrict::where('id', $districtId)->get();
        if(!$district_count){
            $result['code'] = 10002;//有不存在的小区
            return $result;
        }
        $district_has = SellerStaffDistrict::where('staff_id', $staffId)
                                            ->where('district_id',$districtId)
                                            ->where('seller_id',$sellerId)
                                            ->get()
                                            ->toArray();                                
        if($district_has) {
            $result['code'] = 10007;//信息已经存在
            return $result;
        }

        $seller_district = new SellerStaffDistrict();
        $seller_district->seller_id     = $sellerId;
        $seller_district->staff_id      = $staffId;
        $seller_district->district_id   = $districtId;

        try{
            $seller_district->save();
        }catch(Exception $e ){
            $result['code'] = 10003;
        }

        return $result;
    } 


    /**
     * [delete 删除商圈]
     * @param  [int] $sellerId   [服务人员编号]
     * @param  [int] $districtId [商圈编号]
     * @return [array]           [结果代码]
     */
    public static function removeresponsible($sellerId, $staffId, $districtId){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.delete')
        ); 

        try{
            SellerStaffDistrict::where('seller_id', $sellerId)
                        ->where('staff_id', $staffId)
                        ->where('district_id',$districtId)
                        ->delete();
        }catch(Exception $e){
            $result['code'] = 10003;
        }
        return $result;
    } 

    /**
     * [searchresponsible description]
     * @param  [type] $city     [当前城市]
     * @param  [type] $sellerId [商家ID]
     * @param  [type] $keywords [小区关键字]
     * @param  [type] $mapPoint [当前定位点]
     * @param  [type] $page     [分页]
     * @param  [type] $pageSize [分页参数]
     * @return [array]          [返回数组]
     */
    public static function searchresponsible($city, $sellerId, $keywords=Null, $mapPoint=Null, $page, $pageSize){
        // DB::connection()->enableQueryLog();
        $list = SellerDistrict::select('seller_district.*');
        if(!empty($city)) {
            $city_field = self::getRegionFieldName($city['level']);
            
            $ciyt_id    = $city['id'];
        
            $list->where($city_field, '=', $ciyt_id);
        }

        //如果有定位点 则根据定位搜索 否则根据关键字搜索
        if($mapPoint) {
            $mapPoint = empty($appointMapPoint) ? Helper::foramtMapPoint($mapPoint) : Helper::foramtMapPoint($appointMapPoint);
            $mapPoint = $mapPoint ? str_replace(',', ' ', $mapPoint) : '';
            $list->addSelect(DB::raw("ST_Distance(".env('DB_PREFIX')."seller_district.map_point,GeomFromText('POINT({$mapPoint})')) AS map_distance"));
        }else{
            $list->where(function($query) use($keywords){
                    $query->where('name', 'like', '%'.$keywords.'%')
                          ->orWhere('address', 'like', '%'.$keywords.'%');  
                });
        }
        
        $totalCount = $list->count();

        //排序
        if($mapPoint){
           $list->orderBy('map_distance', 'ASC');
        }

        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->get()
            ->toArray();
        // print_r(DB::getQueryLog());die;
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [changestaffdistrict 更换小区]
     * @param  [type] $sellerId       [商家ID]
     * @param  [type] $staffId        [员工ID]
     * @param  [type] $districtId_old [旧小区ID]
     * @param  [type] $districtId_new [新小区ID]
     * @param  [type] $page           [分页]
     * @param  [type] $pageSize       [分页大小]
     * @return [array]                [返回数据]
     */
    public static function changestaffdistrict($sellerId, $staffId, $districtId_old, $districtId_new, $page, $pageSize) {
        // DB::connection()->enableQueryLog();
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.update')
        );

         // 没有找到相关信息，或信息已被删除  不存在
        $has = SellerStaffDistrict::where('seller_id', $sellerId)
                            ->where('staff_id', $staffId)
                            ->where('district_id', $districtId_old)
                            ->first();

        if(!$has) {
            $result['code'] = 50222;   
            return $result;
        }

        //该服务人员已存在该小区
        $has = SellerStaffDistrict::where('seller_id', $sellerId)
                            ->where('staff_id', $staffId)
                            ->where('district_id', $districtId_new)
                            ->first();

        if($has) {
            $result['code'] = 50223;   
            return $result;
        }

        //替换原小区到新小区
        $update = SellerStaffDistrict::where('seller_id', $sellerId)
                            ->where('staff_id', $staffId)
                            ->where('district_id', $districtId_old)
                            ->update([
                                    'seller_id' => $sellerId, 
                                    'staff_id'=>$staffId, 
                                    "district_id"=>$districtId_new
                                ]);
        if($update === false) {
            $result['code'] = 10201;    // 更新失败
            return $result;
        }

        // print_r(DB::getQueryLog());die;
        return $result;

    }

}
