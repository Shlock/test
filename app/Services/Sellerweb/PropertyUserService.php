<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\DoorAccess;
use YiZan\Models\PuserDoor;
use YiZan\Models\DoorOpenLog;
use YiZan\Services\DoorAccessService;
use YiZan\Services\PushMessageService;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang, Exception;


class PropertyUserService extends \YiZan\Services\PropertyUserService {
	
	public static function getLists($sellerId, $name, $build, $roomNum, $mobile, $page, $pageSize, $status){
		//DB::connection()->enableQueryLog();
		$list = PropertyUser::orderBy('property_user.id', 'DESC')
							->where('property_user.seller_id', $sellerId)
							->where('property_user.build_id', '>', 0)
							->where('property_user.room_id', '>', 0);
		
		if($name == true){
			$list->where('property_user.name', 'like', '%'.$name.'%');
		}
		if($mobile == true){
			$list->where('property_user.mobile', 'like', '%'.$mobile.'%');
		}
		if ($build == true ) {
			$list->join('property_building', function($join) use($build){
				$join->on('property_building.id', '=', 'property_user.build_id')
					->where('property_building.name', 'like', "%{$build}%");
			});
		}

		if ($roomNum == true ) {
			$list->join('property_room', function($join) use($roomNum){
				$join->on('property_room.id', '=', 'property_user.room_id')
					->where('property_room.room_num', 'like', "%{$roomNum}%");
			});
		}

		if($status > 0){ //审核状态
			$list->where('property_user.status', $status);
		}
		
    	$totalCount = $list->count();
 		
 		$list = $list->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('district', 'seller', 'build', 'room')
		             ->get()
		             ->toArray();
		     //  print_r($list);
		           //  print_r(DB::getQueryLog());exit;
    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	/*
	* 获取可用门禁列表
	*/
	public static function getdoorsLists($sellerId){
		$list = DoorAccess::orderBy('id', 'DESC')
					 ->where('seller_id', $sellerId)
		             ->get()
		             ->toArray();

		return $list;
	}

	/*
	* 获取业主门禁列表
	*/
	public static function getTotalLists($puserId, $sellerId, $page, $pageSize){
		//DB::connection()->enableQueryLog();
		$list = PuserDoor::where('puser_door.puser_id', $puserId)
					->join('door_access', function($join) use($sellerId) {
						$join->on('door_access.id', '=','puser_door.door_id')
							 ->where('door_access.seller_id', '=', $sellerId);
					});

		$totalCount = $list->count();
		$list = $list->select('puser_door.*')
					 ->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('puser', 'door')
		             ->get()
		             ->toArray();

		// print_r($list);
		// print_r(DB::getQueryLog());exit;
		return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function get($id){
		$data = PuserDoor::where('id', $id)
					 ->with('puser', 'door')
		             ->first();
		return $data;
	}

	public static function getPuser($puserId){
		return PropertyUser::where('id', $puserId)->with('build', 'room')->first();

	}

	public static function checkPuser($puserId){
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		];	
		$data = PropertyUser::where('id', $puserId)->with('build', 'room')->first();
		if (!$data) {
			$result['code'] = 80209;
			return $result;
		}
		if ($data->name != $data->room->owner) {
			//$result['code'] = 80212;
			$result['msg'] = '与' .$data->build->name. '#'. $data->room->room_num . '的业主姓名不匹配，请电话联系业主再次审核';
			return $result;
		}
		if ($data->mobile != $data->room->mobile) {
			//$result['code'] = 80213;
			$result['msg'] = '与' .$data->build->name. '#'. $data->room->room_num . '的业主电话不匹配，请电话联系业主再次审核';
			return $result;
		}
		$result['msg'] = '与' .$data->build->name. '#'. $data->room->room_num . '的业主信息匹配，请电话联系业主再次审核';
		
		return $result;
	}

	public static function save($id, $puserId, $doorId, $sellerId, $endTime){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		];	
		
		$endTime = Time::toTime($endTime);
		if($endTime < 1){
			$result['code'] = 80205;
			return $result;
		}
		if((int)$puserId < 1){
			$result['code'] = 80206;
			return $result;
		}
		if((int)$sellerId < 1){
			$result['code'] = 80202;
			return $result;
		} 
		$puser = PropertyUser::where('id', $puserId)->first();
		DB::beginTransaction();
		$isOpenProperty = Config::get('app.is_open_property');
		try {
			if(is_array($doorId)){
				foreach ($doorId as $did) {
					$doorInfo = PuserDoor::where('door_id', $did)
										 ->where('puser_id', $puserId)
										 ->first();
					//如果系统开启了物业功能的话 访问妙兜接口
		            if($isOpenProperty){
						$pdoor = DoorAccess::find($did);
		            	$doors = DoorAccessService::keyApply($pdoor->pid, $puser->mobile, $endTime, '', '', '', '', ''); 
						if ($doors['code'] == '0') {
							if($doorInfo){
								$doorInfo->end_time		= $endTime; 
								$doorInfo->community 	= $doors['msg'][0]['community'];
								$doorInfo->app_key 		= $doors['msg'][0]['app_key'];
								$doorInfo->lock_id 		= $doors['msg'][0]['lock_id'];
								$doorInfo->save();
							} else {
								$doorInfo = new PuserDoor();
								$doorInfo->puser_id		= $puserId;
								$doorInfo->door_id		= $did;
								$doorInfo->end_time		= $endTime;
								$doorInfo->community 	= $doors['msg'][0]['community'];
								$doorInfo->app_key 		= $doors['msg'][0]['app_key'];
								$doorInfo->lock_id 		= $doors['msg'][0]['lock_id'];
								$doorInfo->save();
							}
						} else {
							$result['msg'] = $pdoor->name.'PID为'.$pdoor->pid . '的锁添加钥匙失败';
							throw new Exception("Error Processing Request", 1); 
						}
		            } else {
		            	if($doorInfo){
							$doorInfo->end_time		= $endTime;  
							$doorInfo->save();
						} else {
							$doorInfo = new PuserDoor();
							$doorInfo->puser_id		= $puserId;
							$doorInfo->door_id		= $did;
							$doorInfo->end_time		= $endTime; 
							$doorInfo->save();
						}
		            }
					
				}
			} else {
				$doorInfo = PuserDoor::where('door_id', $doorId)
									 ->where('puser_id', $puserId)
									 ->first();
				//如果系统开启了物业功能的话 访问妙兜接口
	            if($isOpenProperty){
					$pdoor = DoorAccess::find($doorId);
					$doors = DoorAccessService::keyApply($pdoor->pid, $puser->mobile, $endTime, '', '', '', '', ''); 
					if ($doors['code'] == '0') {
						if($doorInfo){
							$doorInfo->end_time		= $endTime; 
							$doorInfo->community 	= $doors['msg'][0]['community'];
							$doorInfo->app_key 		= $doors['msg'][0]['app_key'];
							$doorInfo->lock_id 		= $doors['msg'][0]['lock_id'];
							$doorInfo->save();
						} else {
							$doorInfo = new PuserDoor();
							$doorInfo->puser_id		= $puserId;
							$doorInfo->door_id		= $doorId;
							$doorInfo->end_time		= $endTime;
							$doorInfo->community 	= $doors['msg'][0]['community'];
							$doorInfo->app_key 		= $doors['msg'][0]['app_key'];
							$doorInfo->lock_id 		= $doors['msg'][0]['lock_id'];
							$doorInfo->save();
						}
					} else {
						$result['msg'] = $pdoor->name.'PID为'.$pdoor->pid . '的锁添加钥匙失败'; 
						throw new Exception("Error Processing Request", 1);
					}
	            } else {
	            	if($doorInfo){
						$doorInfo->end_time		= $endTime;  
						$doorInfo->save();
					} else {
						$doorInfo = new PuserDoor();
						$doorInfo->puser_id		= $puserId;
						$doorInfo->door_id		= $did;
						$doorInfo->end_time		= $endTime; 
						$doorInfo->save();
					}
	            }
			}  
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$result['code'] = 99999;
		}
		return $result; 
	}


    /**
     * 删除门禁
     * @param int $id;  服务编号
     * @return [type] [description]
     */
    public static function deleteDoor($id){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $door = PuserDoor::find($id);
        if (!$door) {
        	$result['code'] = 80208;
            return $result;
        }
        
        DoorOpenLog::where('door_id', $door->id)->where('puser_id', $door->puser_id)->delete();
		PuserDoor::where('id',$id)->delete();
        // print_r(DB::getQueryLog());exit;
        return $result;
    }

	public static function delete($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '删除成功'
		];

		$puser = PropertyUser::find($id);
        if (!$puser) {
        	$result['code'] = 80209;
            return $result;
        }
        PuserDoor::where('puser_id', $id)->delete();
        DoorOpenLog::where('puser_id', $id)->delete();
		PropertyUser::where('id', $id)->delete();

		return $result;
	}

	/*
	* 更改审核状态
	*/
	public static function updateStatus($id, $status, $content){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '成功'
		];

		$puser = PropertyUser::where('id', $id)->with('district', 'seller', 'build', 'room')->first();
        if (!$puser) {
        	$result['code'] = 80209;
            return $result;
        }
        if ($puser->status != 0) {
        	$result['code'] = 80210;
            return $result;
        }
        if ($status == -1 && empty($content)) {
        	$result['code'] = 80214;
            return $result;
        }
		if ($status == 1) {
			$push_title = "小区身份认证审核通过";
			$push_content = "恭喜您已通过".$puser->district->name."审核。您可以进入物业管理进行操作了";
		} elseif ($status == -1) {
			$push_title = "小区身份认证审核失败";
			$push_content = "很抱歉，您提交的".$puser->district->name."的身份信息不正确，未通过审核。请前往物业页面重新验证";
		}
		
		DB::beginTransaction();
		try {
			PropertyUser::where('id', $id)->update(['status'=>$status, 'content'=>$content]);
			PushMessageService::create('buyer', $push_title, $push_content, 1, $puser->user_id, '', 1);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$result['code'] = 99999;
		}
		return $result;
	}

}
