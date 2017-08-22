<?php namespace YiZan\Services;

use YiZan\Models\UserAddress;
use YiZan\Models\User;

use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use YiZan\Utils\Helper;
use YiZan\Utils\Time;

use YiZan\Services\SmsService;
use YiZan\Services\RegionService;

use DB, Lang, Validator, Exception;

class UserAddressService extends BaseService {

    public static function getAddress($userId) {
        $address = UserAddress::where('user_id', $userId)
            ->with('province', 'city', 'area')
            ->orderBy('is_default','desc')
            ->orderBy('id','asc')
            ->first();
        if ($address) {
            return $address->toArray();
        } else {
            return null;
        }
    }

    /**
     * 根据编号获取会员常用地址列表
     * @param  integer $userId 会员编号
     * @return array           地址数组
     */
    public static function getAddressList($userId,$page = 1) {
        $list = UserAddress::where('user_id', $userId)
            ->with('province', 'city', 'area')
            ->orderBy('is_default','desc')
            ->orderBy('id','asc')
            ->skip(($page - 1) * 20)->take(20)
            ->get()->toArray();
        return $list;
    }

    /**
     * 创建会员常用地址
     * @param  integer $userId   会员编号
     * @param  string  $address  地址
     * @param  string  $mapPoint 定位
     * @return array             处理结果
     */
    public static function createAddress($userId,$id,$detailAddress, $mapPoint, $provinceId, $cityId, $areaId, $name, $mobile, $doorplate) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> null,
        );

        $user = User::where('id', $userId)->first();
        if (!$user) {
            $result['code'] = 77000;
            return $result;
        }
        $rules = array(
            'address'  		 => ['required'],
            'mapPoint' 		 => ['required'],
            'name' 	   		 => ['required'],
            'mobile'         => ['required','regex:/^1[0-9]{10}$/'],
            //'doorplate'		 => ['required'],
        );

        $messages = array(
            'address.required'	=> '10201',
            'mapPoint.required'	=> '10202',
            'name.required'		=> '10209',
            'mobile.required'	=> '10210',
            'mobile.regex'		=> '10213',
            //'doorplate.required'=> '10211',
        );

        $validator = Validator::make([
            'address' 	=> $detailAddress,
            'mapPoint' 	=> $mapPoint,
            'name' 		=> $name,
            'mobile' 	=> $mobile,
            //'doorplate' => $doorplate,
        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        /*
	    $count = UserAddress::where('user_id', $userId)->count();
	    if ($count > 4) {//不能超过5个
	    	$result['code'] = 10204;
	    	return $result;
	    }*/
        if($id > 0){
            $userAddress = UserAddress::find($id);
            $userAddress->is_default = $userAddress->is_default;
            $userAddress->user_id = $userAddress->user_id;
            $result['msg'] = Lang::get('api.success.update_info');
        } else {
            $userAddress = new UserAddress;
            $userAddress->is_default = 0;
            $userAddress->user_id = $userId;
            $result['msg'] = Lang::get('api.success.create_user_address');
        }
        
        $userAddress->address = $detailAddress . $doorplate;
        $userAddress->detail_address = $detailAddress;
        // $userAddress->address = $detailAddress;
        // $userAddress->detail_address = $detailAddress . $doorplate;

        $userAddress->map_point_str = $mapPoint;
        $userAddress->map_point = DB::raw("GeomFromText('POINT(".str_replace(',', ' ', $mapPoint).")')");
        $userAddress->province_id = $provinceId;
        $userAddress->city_id = $cityId;
        $userAddress->area_id = $areaId;
        $userAddress->name = $name;
        $userAddress->mobile = $mobile;
        $userAddress->doorplate = $doorplate;

        DB::beginTransaction();
        try{
            $userAddress->save();
            DB::commit();
            $result['data'] = $userAddress->toArray();
        }catch(Exception $e ){
            DB::rollback();
            $result['code'] = 10205;
        }
        return $result;
    }

    /**
     * 常用地址设为默认
     * @param integer $userId    会员编号
     * @param integer $addressId 地址编号
     * @return array             处理结果
     */
    public static function setDefaultAddress($userId, $addressId) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.set_user_default_address')
        );

        DB::beginTransaction();
        try {
            UserAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->update(array('is_default' => 1));
            $bln = true;
            DB::commit();
        } catch (Exception $e) {
            $bln = false;
            DB::rollback();
            $result['code'] = 10206;
        }
        if ($bln) {
            $status = UserAddress::where('user_id', $userId)
                ->where('id', '<>', $addressId)
                ->update(array('is_default' => 0));
        }
        return $result;
    }

    /**
     * 常用地址删除
     * @param integer $userId    会员编号
     * @param integer $addressId 地址编号
     * @return array             处理结果
     */
    public static function deleteAddress($userId, $addressId) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.default_user_address')
        );
        DB::beginTransaction();
        try {
            UserAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->delete();
            $bln = true;
            DB::commit();
        } catch (Exception $e) {
            $bln = false;
            DB::rollback();
            $result['code'] = 10207;
        }
        return $result;
    }

    /**
     * 获取地址详情
     * @param int $userId 会员编号
     * @param int $addressId 地址编号
     */
    public static function getById($userId, $addressId){
        $list = UserAddress::where('user_id', $userId)
        					->with('province', 'city', 'area')
                            ->where('id', $addressId)
                            ->first();
        if ($list) {
        	$list = $list->toArray();
        	//$list['detailAddress'] = $list['address'] ;
        }
        //$list['address'] = $list['province']['name']."  ".$list['city']['name']."   ".$list['area']['name']."    ".$list['address'] .$list['doorplate'];
        return $list;
    }
    /**
     * 获取地址详情
     * @param int $userId 会员编号
     * @param int $addressId 地址编号
     */
    public static function getByDefault($userId){
        $list = UserAddress::where('user_id', $userId)
            ->orderBy('is_default','desc')
            ->first();
        if($list->id != false){
            if($list->is_default == 1){
                return $list;
            }else{
                $lists = self::getAddress($userId);
                UserAddress::where('user_id', $userId)
                    ->where('id',$lists['id'])
                    ->update(array('is_default' => 1));
                return $lists;
            }
        }
        //$list['address'] = $list['province']['name']."  ".$list['city']['name']."   ".$list['area']['name']."    ".$list['address'] .$list['doorplate'];
        return null;
    }
}
