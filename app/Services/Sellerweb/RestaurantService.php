<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\Restaurant;
use YiZan\Models\Sellerweb\User;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB, Lang, Validator;
/**
 * 餐厅管理
 */
class RestaurantService extends \YiZan\Services\RestaurantService {
	/**
	 * 获取餐厅列表
	 * @param  [type] $name     [餐厅名称]
	 * @param  [type] $page     [分页]
	 * @param  [type] $pageSize [分页参数]
	 * @return [type]           [返回数组]
	 */
	public static function lists($sellerId, $name, $page, $pageSize) {
		$tablePrefix = DB::getTablePrefix();

		$list = Restaurant::select('restaurant.*')
				->where('restaurant.seller_id', $sellerId)
				->where('restaurant.dispose_status', 1)
				->selectRaw("(select count(1) from {$tablePrefix}goods where {$tablePrefix}restaurant.id = {$tablePrefix}goods.restaurant_id) num")
				->orderBy('restaurant.id', 'desc');
		if(!empty($name)){
			$list->where('name', 'like', '%'.$name.'%');
		}
		$totalCount = $list->count();
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('seller')
						   ->get()
						   ->toArray();
		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 餐厅审核列表
	 * @param  [type] $name [餐厅名称]
	 * @param  [type] $tel      [机构电话]
	 * @param  [type] $disposeStatus      [状态]
	 * @param  [type] $page        [分页]
	 * @param  [type] $pageSize    [分页参数]
	 * @return [type]              [返回数组]
	 */
	public static function auditLists($sellerId, $name, $disposeStatus, $page, $pageSize) 
    {
		$list = Restaurant::where('seller_id', $sellerId)->orderBy('id', 'desc');
		//等于0查询所有
		if($disposeStatus > 0){
			$list->where('dispose_status',$disposeStatus - 2);
		}
		if(!empty($name)){
			$list->where('name', 'like', '%'.$name.'%');
		}

		$totalCount = $list->count();
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   // ->with('adminuser')
						   ->get()
						   ->toArray();
		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 查看餐厅详细信息
	 * @param  [type] $id [审核ID]
	 * @return [array]    [数组]
	 */
	public static function lookat($id) {
		$result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg' => '',
        );

        if($id < 1) {
        	$result['code'] = 10000;
        	$result['msg'] = Lang::get('api_system.code.10000');
        	return $result;
        }
		$result['data'] = Restaurant::where('id', $id)
							->with('seller')
							->first();
		return $result;
	}
	/**
	 * 根据手机号码获取会员
	 * @param  string $mobile 手机号码
     * @return \stdClass          会员信息
	 */
	public static function getById($userId) {
		return Restaurant::where('user_id', $userId)->first();
	}

	/**
	 * [save description]
	 * @param  [type] $id         餐厅编号
	 * @param  [type] $name       [餐厅名称]
	 * @param  [type] $contacts   [负责人]
	 * @param  [type] $tel        [联系电话1]
	 * @param  [type] $mobile     [联系电话2]
	 * @param  [type] $beginTime  [开始营业时间]
	 * @param  [type] $endTime    [结束营业时间]
	 * @param  [type] $licenseImg [营业执照]
	 * @param  [type] $license    [营业执照号]
	 * @param  [type] $expired    [营业执照有效期]
	 * @param  [type] $address    [常驻地址]
	 * @return [type]             [返回数组]
	 */
	public static function save($id, $name, $logo, $contacts, $tel, $mobile, $beginTime, $endTime, $licenseImg, $license, $expired, $address) {
		$result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg' => Lang::get('api_sellerweb.success.update')
        );

        $rules = array(
            'name'         => ['required'],
            'logo'         => ['required'],
            'contacts'     => ['required'],
            'tel'          => ['required','regex:/^1[0-9]{10}$/'],
            'beginTime'    => ['required'],
            'endTime'      => ['required'],
            'licenseImg'   => ['required'],
            'license'      => ['required'],
            'expired'      => ['required'],
            'address'	   => ['required'],
		);

		$messages = array
        (
            'name.required'       => 60000,    // 名称不能为空
            'logo.required'       => 30605,    // 请上传LOGO图片
            'contacts.required'   => 60001,    // 负责人不能为空
            'tel.required'        => 60002,    // 联系电话不能为空
            'beginTime.required'  => 60003,    // 开始营业时间不能为空
            'endTime.required'    => 60004,    // 结束营业时间不能为空
            'licenseImg.required' => 60005,    // 请上传营业执照图片
            'license.required'    => 60006,    // 请输入营业执照号
            'expired.required'	  => 60007,    // 营业执照有效时间不能为空
            'tel.regex'			  => 60008,	   // 手机号格式不正确
            'address.required'	  => 60009,	   // 常驻地址不能为空
        );

		$validator = Validator::make(
            [		
				'name' => $name,
				'logo'	=> $logo,
				'contacts' => $contacts,
				'tel' => $tel,
				'beginTime' => $beginTime,
				'endTime' => $endTime,
				'licenseImg' => $licenseImg,
				'license' => $license,
				'expired' => $expired,
				'address' => $address,
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

	    //修改user信息
    	$userId = Restaurant::where("id", $id)->pluck('user_id');
        $user['name'] 		 = $name;
        $user['mobile'] 	 = $tel; 
        $user['name_match']  = String::strToUnicode($name . $tel);
        DB::table('User')->where("id", $userId)->update($user);

        //修改餐厅信息
        $data = [
        	'name' => $name, 
        	'logo' => $logo, 
        	'contacts' => $contacts, 
        	'tel' => $tel, 
        	'mobile' => $mobile, 
        	'begin_time' => $beginTime, 
        	'end_time' => $endTime, 
        	'license_img' => $licenseImg, 
        	'license' => $license, 
        	'expired' => Time::toDayTime($expired),
        	'address'	=> $address,
        	'dispose_status' => 0,  //编辑后需要重新审核
        ];
        $res = Restaurant::where("id", $id)->update($data);

        if($res){
        	return $result;
        }else{
        	$result['code'] = 99900;
        	$result['msg'] = Lang::get('api_sellerweb.code.99900'); //更新失败
        	return $result;
        }
	}

	/**
	 * 添加餐厅 编辑餐厅
	 * @param  [type] $sellerId   [服务站ID]
	 * @param  [type] $id         [餐厅id]
	 * @param  [type] $name       [餐厅名称]
	 * @param  [type] $contacts   [负责人]
	 * @param  [type] $tel        [联系电话1]
	 * @param  [type] $mobile     [联系电话2]
	 * @param  [type] $password   [密码]
	 * @param  [type] $beginTime  [开始营业时间]
	 * @param  [type] $endTime    [结束营业时间]
	 * @param  [type] $licenseImg [营业执照]
	 * @param  [type] $license    [营业执照号]
	 * @param  [type] $expired    [营业执照有效期]
	 * @param  [type] $source     [来源 0:web端加盟 1: 服务站添加]
	 * @return [type]             [description]
	 */
	public static function add($sellerId, $id, $name, $logo, $contacts, $tel, $mobile, $password, $beginTime, $endTime, $licenseImg, $license, $expired, $source, $address) {
		$result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg' => '',
        );

        $rules = array(
        	'sellerId'	   => ['min:1'],
            'name'         => ['required'],
            'logo'		   => ['required'],
            'contacts'     => ['required'],
            'tel'          => ['required','regex:/^1[0-9]{10}$/'],
            'beginTime'    => ['required'],
            'endTime'      => ['required'],
            'licenseImg'   => ['required'],
            'license'      => ['required'],
            'expired'      => ['required'],
            'address'      => ['required'],
		);

		$messages = array
        (
        	'sellerId.min'		  => 99996,	   // 需要登录才能调用此接口
            'name.required'       => 60000,    // 名称不能为空
            'logo.required'       => 30605,    // 请上传LOGO图片
            'contacts.required'   => 60001,    // 负责人不能为空
            'tel.required'        => 60002,    // 联系电话不能为空
            'beginTime.required'  => 60003,    // 开始营业时间不能为空
            'endTime.required'    => 60004,    // 结束营业时间不能为空
            'licenseImg.required' => 60005,    // 请上传营业执照图片
            'license.required'    => 60006,    // 请输入营业执照号
            'expired.required'	  => 60007,    // 营业执照有效时间不能为空
            'tel.regex'			  => 60008,	   // 手机号格式不正确
            'address.required'	  => 60009,    //常驻地址不能为空
        );

		$validator = Validator::make(
            [	
            	'sellerId' => $sellerId,
				'name' => $name,
				'logo' => $logo,
				'contacts' => $contacts,
				'tel' => $tel,
				'beginTime' => $beginTime,
				'endTime' => $endTime,
				'licenseImg' => $licenseImg,
				'license' => $license,
				'expired' => $expired,
				'address' => $address,
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

	    if( intval(str_replace(":", "", $endTime)) - intval(str_replace(":", "", $beginTime))  < 1 ) {
	    	$result['code'] = 50222;
	    	return $result;
	    }

	    if(!empty($password)){
	    	$crypt 	= String::randString(6);
	    	$pwd 	= md5(md5($password) . $crypt);
	    }

        if($id < 1) {
        	$Restaurant = new Restaurant();

        	if(empty($password)){
        		$result['code'] = 50206;
        		$result['msg'] = Lang::get('api_sellerweb.code.50206');
        		return $result;
        	}

        	$user = User::where('mobile', $tel)->first();

	    	$data['reg_time']  	 = UTC_TIME;
        	$data['reg_ip']  	 = CLIENT_IP;
        	$data['name']  		 = $name;
        	$data['pwd']	 	 = $pwd;
        	$data['crypt']	 	 = $crypt;
	        $data['group_id']  	 = 1; 
	        $data['mobile']  	 = $tel; 
	        $data['name_match']  = String::strToUnicode($name . $tel);

	        if($user->id > 0){
		        $user->save($data);
        		$Restaurant->user_id = $user->id;
        	}else{
        		$User = new User();
		        $user_id = $User->insertGetId($data); //获取新增用户ID
        		$Restaurant->user_id = $user_id;
        	}
	        
        }else{
        	$Restaurant = Restaurant::where("id", $id)->first();
        	if(!empty($password)){
	        	$data['pwd']	 	 = $pwd;
        		$data['crypt']	 	 = $crypt;
	        }
	        $data['name'] 		 = $name;
	        $data['mobile'] 	 = $tel; 
	        $data['name_match']  = String::strToUnicode($name . $tel);
	        DB::table('User')->where("id", $Restaurant->user_id)->update($data);
        }

        $Restaurant->name           = $name;
        $Restaurant->logo           = $logo;
        $Restaurant->seller_id      = $sellerId;
        $Restaurant->contacts       = $contacts;
        $Restaurant->tel            = $tel;
        $Restaurant->mobile         = $mobile;
        $Restaurant->begin_time     = $beginTime;
        $Restaurant->end_time       = $endTime;
        $Restaurant->license_img    = $licenseImg;
        $Restaurant->license 	    = $license;
        $Restaurant->create_time 	= UTC_TIME;
        $Restaurant->address 		= $address;
        $Restaurant->expired 	    = Time::toDayTime($expired);
        $Restaurant->source   	    = $source ? $source : 0;
        $Restaurant->dispose_status = 0;  //编辑后需要重新审核
       
        $res = $Restaurant->save();

        if($res){
	        $result['msg'] = Lang::get('api_sellerweb.success.success');
        	return $result;
        }else{
        	$result['code'] = 99900;
        	$result['msg'] = Lang::get('api_sellerweb.success.99900'); //更新失败
        	return $result;
        }
	}

	/**
	 * 删除餐厅
	 * @param  [type] $sellerId [服务站ID]
	 * @param  [type] $id       [餐厅ID]
	 * @return [type]           [返回数组]
	 */
	public static function delete($sellerId, $id) {
		$result =	[
				'code'	=> 0,
				'data'	=> null,
				'msg'	=> Lang::get('api_sellerweb.success.delete')
			];

		$res = Restaurant::where('id', $id)->where('seller_id',$sellerId)->delete();

  		if(!$res){
  			$result['code'] = 50602;
  			$result['code'] = Lang::get('api_sellerweb.code.50602');
  		}
        
		return $result;
	}

}