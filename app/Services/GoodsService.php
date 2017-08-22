<?php namespace YiZan\Services;

use YiZan\Models\Goods;
use YiZan\Models\GoodsCate;
use YiZan\Models\GoodsExtend;
use YiZan\Models\GoodsSeller;
use YiZan\Models\SellerStaff;
use YiZan\Models\StaffAppoint;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\GoodsStaff;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang;

class GoodsService extends BaseService {
	/**
	 * 根据编号获取服务
	 * @param  integer $goodsId 服务编号
	 * @param  integer $userId  会员编号
	 * @return object            服务信息
	 */
	public static function getById($goodsId, $userId) {
		//DB::connection()->enableQueryLog();
		$goods = Goods::with('extend');

		if ($userId > 0) {
			$goods->with(['collect' => function($query) use($userId) {
					$query->where('user_id', $userId);
				}]);
		}
		$goods = $goods->find($goodsId);

		if(!$goods) {
			return false;
		}

		if ($goods->seller_id > 0) {
			$goods->sale_status = GoodsSeller::where('goods_id', $goodsId)->pluck('sale_status');
		} else {
			$goods->sale_status = STATUS_ENABLED;
		}
		
		$goods->staffCount = 0;
		if ($goods) {
			$list = GoodsStaff::where('goods_staff.goods_id', $goodsId)
							->join('seller_staff', function($join) {
					            $join->on('seller_staff.id', '=', 'goods_staff.staff_id')
					            	 ->where('seller_staff.status', '=', STATUS_ENABLED);//已审核
					        })
					        ->join('seller', function($join) {
					            $join->on('seller.id', '=', 'goods_staff.seller_id')
					            	 ->where('seller.status', '=', 1)//已审核
					            	 ->where('seller.business_status', '=', 1);//可接单
					        });

			$goods->staffCount = $list->count();
			if ($goods->staffCount == 1) {
			    $staff_id = $list->pluck('staff_id');
			    $goods->staff = SellerStaff::with('seller', 'extend')->find($staff_id);
			}
		}
		return $goods;
	}

	/**
     * [getList description]
     * @param  [int] $cityId       城市编号
	 * @param  [type] $page        页码
	 * @param  [type] $order       排序类型
	 * @param  [type] $sort        排序方式
	 * @param  [type] $keywords    关键字
	 * @param  [type] $sellerId    卖家编号
	 * @param  [type] $appointTime 预约时间
	 * @param  [type] $mapPoint    地图坐标
	 * @param  [type] $categoryId  分类编号
	 * @param  [type] $status      状态
	 * @return [type]              服务数组
	 */
	public static function getList($city, $page, $order, $sort, $keywords = '', $sellerId = 0, 
		$appointTime = '', $mapPoint = '', $categoryId = 0, $staffId = 0, $tagId = 0, $userId = 0) {
		
        if($city == null) {
            return [];
        }

        $keywords = empty($keywords) ? '' : String::strToUnicode($keywords,'+');

        $tablePrefix = DB::getTablePrefix();
        
        $goods_table 					= DB::getTablePrefix().'goods';
        $goods_staff_table 				= DB::getTablePrefix().'goods_staff';
        $staff_map_table 				= DB::getTablePrefix().'staff_map';
        $seller_district_table 			= DB::getTablePrefix().'seller_district';

        
        $staff_appoint_table 			= DB::getTablePrefix().'staff_appoint';
        $seller_staff_district_table 	= DB::getTablePrefix().'seller_staff_district';
		$staff_service_time_table 		= DB::getTablePrefix().'staff_service_time';
        

        //筛选服务机构所在城市
		$city_field = self::getRegionFieldName($city['level']);
		$ciyt_id 	= $city['id'];

		$categoryIds = [];
		if ($categoryId > 0) {
			$categoryIds[] = $categoryId;
			$categoryList = GoodsCate::where('pid', $categoryId)->where('status', STATUS_ENABLED)->get();
			foreach ($categoryList as $categoryItem) {
				$categoryIds[] = $categoryItem->id;
			}
		}

        $list = Goods::select('goods.*')
					->where(function($query) use($keywords, $categoryIds, $goods_table) {
						//启用状态时
						$query->where('goods.status', STATUS_ENABLED);
						//筛选关键字
						if (!empty($keywords)) {
							$query->whereRaw("MATCH({$goods_table}.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)");
						}
						//筛选服务分类
						if (count($categoryIds) > 0) {
							$query->whereIn('goods.cate_id', $categoryIds);
						}
					})
					->join('goods_seller', function($join) use($sellerId) {
						//有服务机构发布时
				        $join->on('goods_seller.goods_id', '=', 'goods.id')
				        	->where('goods_seller.sale_status', '=', STATUS_ENABLED);//上架状态
				        if ($sellerId > 0) {
				        	$join->where('goods_seller.seller_id', '=', $sellerId);
				        }
			        })
			        ->join('seller', function($join) use($city_field, $ciyt_id) {
			        	//服务机构为启用，并且开业，并且在所选城市
				        $join->on('seller.id', '=', 'goods_seller.seller_id')
				        	->where('seller.status', '=', STATUS_ENABLED)
				        	->where('seller.business_status', '=', STATUS_ENABLED)
				        	->where('seller.'.$city_field, '=', $ciyt_id);
			        })
			        ->join('goods_staff', function($join) use($staffId) {
			        	//有服务人员提供服务时
				        $join->on('goods_staff.goods_id', '=', 'goods.id');
				        //当有服务人员编号时
				        if ($staffId > 0) {
				        	$join->where('goods_staff.staff_id', '=', $staffId);
				        }
			        })->join('seller_staff', function($join) {
			        	//服务人员启用时
				        $join->on('seller_staff.id', '=', 'goods_staff.staff_id')
				        	->where('seller_staff.status', '=', STATUS_ENABLED);
			        });

		//预约范围
		$mapPoint = Helper::foramtMapPoint($mapPoint);
		$mapPoint = $mapPoint ? str_replace(',', ' ', $mapPoint) : '';
		if ($mapPoint) {
			$list->where(function($query) use($staffId, $mapPoint, $staff_map_table, $seller_district_table, $goods_staff_table) {
				//在服务人员自定义范围内
				$query->whereExists(function($query) use($staffId, $mapPoint, $staff_map_table, $goods_staff_table) {
					$query->select(DB::raw(1))
	                      ->from('staff_map');
	                if ($staffId > 0) {
		                $query->where('staff_map.staff_id', '=', $staffId);
		            } else {
		            	$query->where('staff_map.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"));
		            }
		            $query->whereRaw("ST_Contains(".$staff_map_table.".map_pos,GeomFromText('Point({$mapPoint})'))");
				});
				//或者在服务人员的商圈范围内
				$query->orWhereExists(function($query) use($staffId, $mapPoint, $seller_district_table, $goods_staff_table) {
					$query->select(DB::raw(1))
	                      ->from('seller_staff_district');
	                if ($staffId > 0) {
	                	$query->where('seller_staff_district.staff_id', $staffId);
		            } else {
		            	$query->where('seller_staff_district.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"));
		            }
		            $query->join('seller_district', function($join) use($mapPoint, $seller_district_table) {
						$join->on('seller_district.id', '=', 'seller_staff_district.district_id')
							->on(new Expression(''), new Expression(''), new Expression("ST_Contains(".$seller_district_table.".map_pos,GeomFromText('Point({$mapPoint})'))"), 'and', false);
					});
				});
			});
		}

		//有标签时
		if ($tagId > 0) {
			$list->join('goods_tag_related', function($join) use($tagId) {
				$join->on('goods_tag_related.goods_id', '=', 'goods.id')
					->where('goods_tag_related.tag_id', '=', $tagId);
			});
		}
        
		$sort = $sort == 1 ? 'desc' : 'asc';
		switch ($order) {
			case 3://价格排序
				$list->orderBy('goods.price', $sort);
			break;

			case 2://销量排序
				$list->orderBy('goods.sales_volume', $sort);
			break;
		}

		$list->orderBy('goods.sort', 'asc')
			->orderBy('goods.id', 'desc')
			->groupBy('goods.id');

		//当有会员编号时
		if ($userId > 0) {
			$list = $list->with(['collect' => function($query) use($userId) {
					$query->where('user_id', $userId);
				}]);
		}

		//DB::connection()->enableQueryLog();
		$list = $list->skip(($page - 1) * 20)->take(20)->get()->toArray();
		//print_r(DB::getQueryLog());exit;
		return $list;
	}

	/**
	 * 根据编号获取服务
	 * @param  integer $goodsId 服务编号
	 * @return int            	状态码
	 */
	public static function setBrowse($goodsId) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.set_goods_browse')
		);

	    $bln = GoodsExtend::where('goods_id', $goodsId)->increment('browse_count');
	    if ($bln) {
			return $result;
		} else {
			$result['code'] = 40001;
			return $result;
		}
	}

	public static function incrementField($goodsId, $field) {
		Goods::where('id',$goodsId)->increment($field);
	}

	public static function incrementExtend($goodsId, $field, $num = 1) {
		GoodsExtend::where('goods_id',$goodsId)->increment($field, $num);
	}

	public static function decrementExtend($goodsId, $field, $num = 1) {
        
        $extend = GoodsExtend::where('goods_id',$goodsId)->first();
        if($extend == true && $extend->$field > 0) {
            GoodsExtend::where('goods_id',$goodsId)->decrement($field, $num);
        }
	}

	public static function updateComment($goodsId, $credit, $specialtyScore = 0, $communicateScore = 0, $punctualityScore = 0) {
		$extend = GoodsExtend::where('goods_id',$goodsId)->first();
		$extend->comment_total_count++;
		/*$extend->comment_specialty_total_score += $specialtyScore;
		$extend->comment_specialty_avg_score = $extend->comment_specialty_total_score / $extend->comment_total_count;

		$extend->comment_communicate_total_score += $communicateScore;
		$extend->comment_communicate_avg_score = $extend->comment_communicate_total_score / $extend->comment_total_count;

		$extend->comment_punctuality_total_score += $punctualityScore;
		$extend->comment_punctuality_avg_score = $extend->comment_punctuality_total_score / $extend->comment_total_count;*/

		switch($credit) {
			case 'good'://好评
				$extend->comment_good_count++;
			break;

			case 'neutral'://中评
				$extend->comment_neutral_count++;
			break;

			case 'bad'://差评
				$extend->comment_bad_count++;
			break;
		}
		$extend->save();
	}
    /**
     * 删除评价重新统计
     * @param int $goodsId 服务编号
     * @param string $credit 评价类型
     * @param double $specialtyScore 专业总分
     * @param double $communicateScore 沟通总分
     * @param double $punctualityScore 守时总分
     */
    public static function deleteComment($goodsId, $credit, $specialtyScore = 0, $communicateScore = 0, $punctualityScore = 0) {
		$extend = GoodsExtend::where('goods_id',$goodsId)->first();
		$extend->comment_total_count--;
		/*$extend->comment_specialty_total_score -= $specialtyScore;
		$extend->comment_specialty_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_specialty_total_score / $extend->comment_total_count;

		$extend->comment_communicate_total_score -= $communicateScore;
		$extend->comment_communicate_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_communicate_total_score / $extend->comment_total_count;

		$extend->comment_punctuality_total_score -= $punctualityScore;
		$extend->comment_punctuality_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_punctuality_total_score / $extend->comment_total_count;*/

		switch($credit) {
			case 'good'://好评
				$extend->comment_good_count--;
                break;

			case 'neutral'://中评
				$extend->comment_neutral_count--;
                break;

			case 'bad'://差评
				$extend->comment_bad_count--;
                break;
		}
		$extend->save();
	}
	/**
	 * 自动分配服务人员
	 * @param  \stdClass    $goods       服务
	 * @param  string $appointTime 预约时间
	 * @param  string $mapPoint    地图定位坐标
	 * @param  string $duration    预约时长
	 * @param  string $staffId     服务人员编号
	 * @return [type] [description]
	 */
	public static function autoAllocationStaff($goods, $appointTime, $mapPoint, $duration, $isToStore = 0) {
		DB::connection()->enableQueryLog();
		$list = GoodsStaff::where('goods_staff.goods_id', $goods->id);
		$list->join('seller_staff', function($join) {
	            $join->on('seller_staff.id', '=', 'goods_staff.staff_id')
	            	 ->where('seller_staff.status', '=', 1);//已审核
	        })
	        ->join('seller', function($join) {
	            $join->on('seller.id', '=', 'goods_staff.seller_id')
	            	 ->where('seller.status', '=', 1)//已审核
	         		 ->where('seller.business_status', '=', 1);//可接单
	        });

	    //获取可提供服务的服务人员数量
	    $staff_count = $list->count();
	    if ($staff_count < 1) { //没有可分配的人员提供服务
	    	return -60025;
	    }

	    //预约时间
        $tablePrefix = DB::getTablePrefix();
        $goods_staff_table = DB::getTablePrefix().'goods_staff';
        $seller_staff_table = DB::getTablePrefix().'seller_staff';
        $staff_map_table = DB::getTablePrefix().'staff_map';
        $seller_district_table = DB::getTablePrefix().'seller_district';

		$beginTime = $appointTime - SERVICE_DELAY_BEGIN_TIME;
        $endTime = $appointTime + $duration + SERVICE_DELAY_END_TIME;
		$list->whereNotExists(function($query) use($goods_staff_table, $beginTime, $endTime) {
			$query->select(DB::raw(1))
                  ->from('staff_appoint')
                  ->where('staff_appoint.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"))
                  ->where('appoint_time', '>=', $beginTime)
                  ->where('appoint_time', '<', $endTime)
                  ->where(function($query1) {
						$query1->whereIn('status', [
			            		StaffAppoint::REFUSE_APPOINT_STATUS, //拒绝接单
			            		StaffAppoint::HAVING_APPOINT_STATUS  //有单
			            	])
			            	->orWhere('is_leave', '=', 1);//为请假时
					});
		});

        $staff_count = $list->count();
        if ($staff_count < 1) { //服务人员在该时间段内忙碌，不能接受预约
	    	return -60024;
	    }

	    //获取预约的每天对应周及开始结束时间
		$weeks = Time::getWeekHoursByTime($appointTime, $appointTime + $duration);
		//是否预约的时间段在上班
		$list->whereExists(function($query) use($weeks, $goods_staff_table) {
				$query->select(DB::raw(1))
                      ->from('staff_service_time')
                      ->where('staff_service_time.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"))
                      ->having(new Expression('COUNT(*)'), '>=', count($weeks));
                
                $query->where(function($query1) use($weeks) {
						foreach ($weeks as $week => $times) {
							$query1->orWhere(function($query1) use($week, $times) {
									$query1->where('staff_service_time.week', '=', $week)
										->where('staff_service_time.begin_time', '<=', $times['begin'])
										->where('staff_service_time.end_stime', '>=', $times['end']);
								});
						}
					});
			});
		$staff_count = $list->count();
        if ($staff_count < 1) { //服务人员在该时间段内休息，不能接受预约
	    	return -60024;
	    }

	    $list->select('goods_staff.staff_id');
	    if ($isToStore == 0) {//如果为上门服务
	        $mapPoint = Helper::foramtMapPoint($mapPoint);
			$mapPoint = $mapPoint ? str_replace(',', ' ', $mapPoint) : '';
			//预约范围
			$list->where(function($query) use($mapPoint, $goods_staff_table, $staff_map_table, $seller_district_table) {
				//在服务人员自定义范围内
				$query->whereExists(function($query) use($mapPoint, $goods_staff_table, $staff_map_table) {
					$query->select(DB::raw(1))
	                      ->from('staff_map')
	                      ->where('staff_map.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"))
	                      ->whereRaw("ST_Contains(".$staff_map_table.".map_pos,GeomFromText('Point({$mapPoint})'))");
				});
				//或者在服务人员的商圈范围内
				$query->orWhereExists(function($query) use($mapPoint, $goods_staff_table, $seller_district_table) {
					$query->select(DB::raw(1))
	                      ->from('seller_staff_district')
	                      ->where('seller_staff_district.staff_id', '=', new Expression("{$goods_staff_table}.staff_id"));

		            $query->join('seller_district', function($join) use($mapPoint, $seller_district_table) {
						$join->on('seller_district.id', '=', 'seller_staff_district.district_id')
							->on(new Expression(''), new Expression(''), new Expression("ST_Contains(".$seller_district_table.".map_pos,GeomFromText('Point({$mapPoint})'))"), 'and', false);
					});
				});
			});
			$staff_count = $list->count();
	        if ($staff_count < 1) { //不在服务范围内
		    	return -60012;
		    }

			//添加距离字段
			$list->addSelect(DB::raw("ST_Distance({$seller_staff_table}.map_point,GeomFromText('POINT({$mapPoint})')) AS map_distance"));

			//距离最近的优先
			$list->orderBy('map_distance', 'asc');
		}
		
		$list->join('seller_staff_extend', 'seller_staff_extend.staff_id', '=', 'goods_staff.staff_id');
		//接单数最少的优先
		$list->orderBy('seller_staff_extend.order_count', 'asc');
		//好评最多的优先
		$list->orderBy('seller_staff_extend.comment_good_count', 'desc');

		return (int)$list->pluck('staff_id');
	}

	

	/**
	 * 获取详细
	 */
	public static function carget($goodsId) {
		// DB::connection()->enableQueryLog();
		$list = Goods::select('goods.*')->where('id',$goodsId);
		$list = $list->first();

		// print_r( DB::getQueryLog() );
		// die;
		return $list;
	}

	/**
	 * 餐厅菜品列表
	 * @param  [type] $restaurantId  [餐厅id]
	 * @param  [type] $name     [description]
	 * @param  [type] $status   [description]
	 * @param  [type] $page     [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public static function goodslist($restaurantId, $name, $status, $page, $pageSize) {
		$list = Goods::where('restaurant_id',$restaurantId)->where('type',1)->orderBy('id', 'desc');

		if(!empty($name)) {
			$list->where('name', 'like', '%'.$name.'%');
		}

		if($status > 0) {
			$list->where('status', $status - 1);
		}

		$totalCount = $list->count();
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('type','seller')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}

	/**
	 * [joinService 更改菜品的参与服务]
	 * @param  [type] $id          [菜品ID]
	 * @param  [type] $joinService [1:即时送餐 2：预约午餐 3：同时参加]
	 * @return [array]             [返回数组]
	 */
	public static function joinService($id, $joinService) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api_system.success.update_info')
		);

		if($id < 1) {
			$result['code'] = 10000;
			$result['msg'] = Lang::get('api_system.code.10000');
			return $result;
		}

	    $data = [
	    	'join_service' => $joinService,
	    ];

	   	$bln = Goods::where('id', $id)->update($data);

	    if ($bln) {
			return $result;
		} else {
			$result['code'] = 40001;
			return $result;
		}
	}

	/**
	 * [updown 菜品上下架]
	 * @param  [type] $id     [菜品id]
	 * @param  [type] $status [菜品状态]
	 * @return [type]         [description]
	 */
	public static function updown($id, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		);

		if($id < 1) {
			$result['code'] = 78000;
			$result['msg'] = Lang::get('api.code.78000');//菜品参数擦错误
			return $result;
		}

		if(!in_array($status, [0,1])) {
			$result['code'] = 78001;
			$result['msg'] = Lang::get('api.code.78001');//菜品状态错误
			return $result;
		}

	    $data = [
	    	'status' => $status,
	    ];

	   	$bln = Goods::where('id', $id)->update($data);

	    if ($bln) {
			return $result;
		} else {
			$result['code'] = 40001;
			return $result;
		}
	}
}
