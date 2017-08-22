<?php namespace YiZan\Services\System;

use YiZan\Models\System\User;
use YiZan\Models\System\UserAddress;
use YiZan\Models\System\UserCollectGoods;
use YiZan\Models\System\UserCollectSeller;
use YiZan\Models\System\UserPayLog;
use YiZan\Models\UserRefundLog;
use YiZan\Models\System\UserVerifyCode;
use YiZan\Models\System\Order;
use YiZan\Models\System\OrderPromotion;
use YiZan\Models\System\OrderRate;
use YiZan\Models\System\PromotionSn;

use YiZan\Utils\String;

use DB, Validator;

class UserService extends \YiZan\Services\UserService {
	/**
	 * 获取会员列表
	 * @param  string  $mobile  		  [description]
	 * @param  string  $name     		  [description]
	 * @param  integer $status   		  [description]
	 * @param  integer $page     		  [description]
	 * @param  integer $pageSize		  [description]
	 * @param  string  $recommendUserName [description]
	 * @return [type]            [description]
	 */
	public static function getLists($mobile, $name, $status, $userType, $page, $pageSize,$recommendUserName) {
		$list = User::with('regProvince', 'regCity', 'loginProvince', 'loginCity', 'seller', 'staff','extensionWorker');
		
		if (!empty($name)) {//搜索名称
			$list->where('name','like','%'.$name.'%');
		}

		if (!empty($mobile)) {//搜索手机号
			$list->where('mobile', $mobile);
		}

		if ($status > 0) {//状态
			$list->where('status', $status - 1);
		}

		if (!empty($recommendUserName)) { //推荐人
			$list->whereIn('recommend_uid', function($q) {
			    $q->select('id')->from('user')
			    ->where('name','like','%'.$recommendUserName.'%')
			    ->orWhere('mobile','like','%'.$recommendUserName.'%');
			});
		}

        if ($userType > 0) {
            if ($userType == 1) {
                $list->whereIn('id', function($query) {
                    $query->select('user_id')->from('seller');
                })
                    ->whereIn('id', function($query) {
                        $query->select('user_id')->from('seller_staff');
                    });
            } else {
                $list->whereNotIn('id', function($query) {
                    $query->select('user_id')->from('seller');
                })
                    ->whereNotIn('id', function($query) {
                        $query->select('user_id')->from('seller_staff');
                    });
            }
        }
		$total_count = $list->count();
		$list->orderBy('id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

	/**
	 * 会员搜索
	 * @param  [type] $mobileName 手机或者名称
	 * @return [type]             [description]
	 */
	public static function searchUser($mobileName) {
		$list = User::select('id', 'name', 'mobile')->with(['address' => function($query) {
						$query->orderBy('is_default', 'desc')
							  ->orderBy('id', 'desc')
							  ->groupBy('user_id');
					}]);

		if (!empty($mobileName)) {
            $list->where(function($query) use ($mobileName){
                $query->where('mobile',$mobileName)
                    ->orWhere('name','like','%'.$mobileName.'%');
            });
		}

		return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
	}

	public static function getById($id) {
		return User::with('regProvince', 'regCity', 'loginProvince', 'loginCity')->find($id);
	}

	/**
	 * 更新会员状态
	 * @param  [type] $id     [description]
	 * @param  [type] $status [description]
	 * @return [type]         [description]
	 */
	public static function updateStatus($id, $status) {
		User::where('id', $id)->update(['status' => $status]);
	}

	public static function updateUser($id, $mobile, $name, $pwd, $avatar, $status
		,$isExtensionWorker=0,$extensionAddress='',$extensionLat=0.0,$extensionLng=0.0, $extensionRange=0) {
		$pwd = strval($pwd);

		$result = array(
			'code'	=> 0,
			'data'	=> $mobile,
			'msg'	=> ''
		);
     
		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/','unique:user,mobile,'.$id],
		    'name' 	 => ['required'],
		    'pwd' 	 => ['sometimes','min:5','max:20']
		);

		$messages = array(
		    'mobile.required'	=> '20102',
		    'mobile.regex'		=> '20103',
		    'mobile.unique'		=> '20104',
		    'name.required' 	=> '20105',
		    'pwd.min' 			=> '20106',
		    'pwd.max' 			=> '20106',
		);

		$user = User::find($id);
		if (!$user) {//会员不存在
			$result['code'] = 20101;
			return $result;
		}

		$validator = Validator::make([
				'mobile' => $mobile,
				'name' 	 => $name,
				'pwd' 	 => $pwd
			], $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

	    //当有会员头像时
	    if (!empty($avatar)) {
	    	$avatar = self::moveUserImage($user->id, $avatar);
	    	if (!$avatar) {
	    		$result['code'] = 20107;
	    		return $result;
	    	} else {
	    		$user->avatar = $avatar;
	    	}
	    }

	    if (!empty($pwd)) {
	    	$user->pwd = md5(md5($pwd) . $user->crypt);
	    }

	    $user->mobile 						= $mobile;
	    $user->name 						= $name;
	    $user->status 						= $status;

	    // 新增的推广人员设置相关的内容
	    $user->is_extension_worker 			= $isExtensionWorker;
	    $user->extension_address 			= $extensionAddress;
	    $user->extension_lat 				= $extensionLat;
	    $user->extension_lng 				= $extensionLng;
	    $user->extension_range 				= $extensionRange;


	    $user->save();
	    return $result;
	}

	public static function removeUser($ids) {
		if (!is_array($ids)) {
			$ids = (int)$ids;
			if ($ids < 1) {
				return false;
			}
			$ids = [$ids];
		}
		
		DB::beginTransaction();
		try {
			//删除会员
			User::destroy($ids);
			//删除会员地址表
			UserAddress::whereIn('user_id', $ids)->delete();
			//删除会员收藏服务
			//UserCollectGoods::whereIn('user_id', $ids)->delete();
			//删除会员收藏卖家
			//UserCollectSeller::whereIn('user_id', $ids)->delete();
			//删除会员支付日志
			UserPayLog::whereIn('user_id', $ids)->delete();
			//删除会员退款处理
			UserRefundLog::whereIn('user_id', $ids)->delete();
			//删除会员验证码表
			UserVerifyCode::whereIn('user_id', $ids)->delete();
			//删除订单
			Order::whereIn('user_id', $ids)->delete();
			//删除订单优惠详细
			OrderPromotion::whereIn('user_id', $ids)->delete();
			//删除订单评价
			OrderRate::whereIn('user_id', $ids)->delete();
			//删除优惠发放表
			PromotionSn::whereIn('user_id', $ids)->delete();
			DB::commit();
		} catch (Exception $e) {
    		DB::rollback();
    		return false;
    	}
	    return true;
	}
}
