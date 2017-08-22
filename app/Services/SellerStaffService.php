<?php namespace YiZan\Services;

use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\SellerCreditRank;
use YiZan\Models\StaffAppoint;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB;

class SellerStaffService extends BaseService {
	/**
	 * 获取服务人员
	 * 根据编号获取员工
	 * @param  integer $id 		员工编号
	 * @return array            员工信息
	 */
	public static function getById($id) {
		return SellerStaff::find($id);
	}

    /**
     * 根据编号获取员工
     * @param  integer $userId  会员编号
     * @return object           员工信息
     */
	public static function getByUserId($userId) {
		return SellerStaff::where('user_id', $userId)->first();
	}

	/**
	 * 获取员工信息
	 * @param  [type]  $sellerId [description]
	 * @param  integer $userId   [description]
	 * @return [type]            [description]
	 */
	public static function getStaff($staffId, $userId = 0) 
    {
    	$staff = SellerStaff::with('extend', 'seller', 'district.district');

		if ($userId > 0) {
			$staff = $staff->with(['collect' => function($query) use($userId) {
					$query->where('user_id', $userId);
				}]);
		}
        $staff = $staff->find($staffId);

        //待完善
        if ($staff) {
        	$tablePrefix = DB::getTablePrefix();
            $sellerId = $staff->seller_id;

        	$staff = $staff->toArray();
        	foreach ($staff['district'] as $key=>$val) {
                $staff['district'][$key] = $val['district'];
            }
            
            $list = Goods::select('goods.*')
            			->where('goods.status', STATUS_ENABLED)
            			->join('goods_seller', function($join) use($sellerId) {
							//有服务机构发布时
					        $join->on('goods_seller.goods_id', '=', 'goods.id')
					        	->where('goods_seller.sale_status', '=', STATUS_ENABLED)//上架状态
					        	->where('goods_seller.seller_id', '=', $sellerId);
				        })
				        ->join('goods_staff', function($join) use($staffId) {
				        	//有服务人员提供服务时
					        $join->on('goods_staff.goods_id', '=', 'goods.id')
					        	->where('goods_staff.staff_id', '=', $staffId);
				        });
			//当有会员编号时
			if ($userId > 0) {
				$list = $list->with(['collect' => function($query) use($userId) {
						$query->where('user_id', $userId);
					}]);
			}
			$staff['goods'] = $list->get()->toArray();
        }
        return $staff;
	}

	/**
	 * Summary of getList
	 * @param mixed $city 
	 * @param mixed $page 
	 * @param mixed $order 
	 * @param mixed $sort 
	 * @param mixed $keywords 
	 * @param mixed $appointTime 
	 * @param mixed $mapPoint 
	 * @param mixed $appointMapPoint 
	 * @param mixed $goodsId 
	 * @param mixed $sellerId 
	 * @param mixed $categoryId 
	 * @param mixed $duration 
	 * @return mixed
	 */
	public static function getList($city, $page, $order, $sort, $keywords = '', $appointTime = '', $mapPoint = '', $appointMapPoint = '', $goodsId = 0, $sellerId = 0, $categoryId = 0, $duration = 0) {

		//筛选服务机构所在城市
		$city_field = self::getRegionFieldName($city['level']);
		$ciyt_id 	= $city['id'];

		$tablePrefix = DB::getTablePrefix();
        
        $seller_staff_table		= DB::getTablePrefix().'seller_staff';
        $staff_map_table		= DB::getTablePrefix().'staff_map';
        $seller_district_table	= DB::getTablePrefix().'seller_district';

		$mapPoint = empty($appointMapPoint) ? Helper::foramtMapPoint($mapPoint) : Helper::foramtMapPoint($appointMapPoint);
		$mapPoint = $mapPoint ? str_replace(',', ' ', $mapPoint) : '';
		$list = SellerStaff::with('seller','extend')
						   ->where('seller_staff.status', 1)//已审核
						   ->join('seller', function($join) use($city_field, $ciyt_id, $sellerId) {
					        	//服务机构为启用，并且开业，并且在所选城市
						        $join->on('seller.id', '=', 'seller_staff.seller_id')
						        	->where('seller.status', '=', STATUS_ENABLED)
						        	->where('seller.business_status', '=', STATUS_ENABLED)
						        	->where('seller.'.$city_field, '=', $ciyt_id);
					        })
						   ->join('goods_staff', function($join) use($goodsId) {
					        	//有服务人员提供服务时
						        $join->on('goods_staff.staff_id', '=', 'seller_staff.id');
						        if ($goodsId > 0) {
						        	$join->where('goods_staff.goods_id', '=', $goodsId);
						        }
					        })
						   ->join('goods', function($join) use($categoryId) {
					        	//服务启用时
						        $join->on('goods.id', '=', 'goods_staff.goods_id')
						        	->where('goods.status', '=', STATUS_ENABLED);
						        if ($categoryId > 0) {
						        	$join->where('goods.cate_id', '=', $categoryId);
						        }
					        });

		$keywords = empty($keywords) ? '' : String::strToUnicode($keywords,'+');
		if (!empty($keywords)) {
			$list->whereRaw('MATCH('.DB::getTablePrefix().'seller_staff.name_match) AGAINST(\'' . $keywords . '\' IN BOOLEAN MODE)');
		}

		$sort = $sort == 1 ? 'desc' : 'asc';
		switch ($order) {
			case 1://距离排序
				if ($mapPoint) {
					$list->addSelect(DB::raw("ST_Distance({$seller_staff_table}.map_point,GeomFromText('POINT({$mapPoint})')) AS map_distance"));
					$list->orderBy('map_distance', $sort);
				}
			break;

			case 2://人气排序
				$list->join('seller_staff_extend', 'seller_staff_extend.staff_id', '=', 'seller_staff.id');
				$list->orderBy('seller_staff_extend.order_count', $sort);
			break;

			case 3://好评排序
				$list->join('seller_staff_extend', 'seller_staff_extend.staff_id', '=', 'seller_staff.id');
				$list->orderBy('seller_staff_extend.comment_good_count', $sort);
			break;
		}
		$list->addSelect('seller_staff.id', 'seller_staff.seller_id', 'seller_staff.name', 'seller_staff.avatar', 'seller_staff.sex', 'seller_staff.birthday');
		$list->orderBy('seller_staff.sort', 'asc');
		$list->orderBy('seller_staff.id', 'desc');
		$list->groupBy('seller_staff.id');
        
        //服务范围筛选
		if (!empty($appointMapPoint) && $mapPoint) {
			$list->where(function($query) use($mapPoint, $staff_map_table, $seller_district_table, $seller_staff_table) {
				//在服务人员自定义范围内
				$query->whereExists(function($query) use($mapPoint, $staff_map_table, $seller_staff_table) {
					$query->select(DB::raw(1))
	                      ->from('staff_map')
	                      ->where('staff_map.staff_id', '=', new Expression("{$seller_staff_table}.id"));
		            
		            $query->whereRaw("ST_Contains(".$staff_map_table.".map_pos,GeomFromText('Point({$mapPoint})'))");
				});
				//或者在服务人员的商圈范围内
				$query->orWhereExists(function($query) use($mapPoint, $seller_district_table, $seller_staff_table) {
					$query->select(DB::raw(1))
	                      ->from('seller_staff_district')
	                      ->where('seller_staff_district.staff_id', '=', new Expression("{$seller_staff_table}.id"));
		            
		            $query->join('seller_district', function($join) use($mapPoint, $seller_district_table) {
						$join->on('seller_district.id', '=', 'seller_staff_district.district_id')
							->on(new Expression(''), new Expression(''), new Expression("ST_Contains(".$seller_district_table.".map_pos,GeomFromText('Point({$mapPoint})'))"), 'and', false);
					});
				});
			});
		}

		$appointTime = empty($appointTime) ? 0 : Time::toTime($appointTime);
		//预约时间筛选
		if ($appointTime != 0) {
			//预约开始时间
			$beginTime  = $appointTime - SERVICE_DELAY_BEGIN_TIME;
			//预约结束时间
	        $endTime = $appointTime + $duration + SERVICE_DELAY_END_TIME;

			//预约时间
			$list->whereNotExists(function($query) use($beginTime, $endTime, $seller_staff_table) {
				$query->select(DB::raw(1))
					->from('staff_appoint')
					->where('staff_appoint.staff_id', '=', new Expression("{$seller_staff_table}.id"))
					->where('staff_appoint.appoint_time', '>=', $beginTime)
					->where('staff_appoint.appoint_time', '<', $endTime)
					->where(function($query1) {
						//为接单或禁止接单时
						$query1->whereIn('staff_appoint.status', [StaffAppoint::REFUSE_APPOINT_STATUS, StaffAppoint::HAVING_APPOINT_STATUS])
							->orWhere('staff_appoint.is_leave', '=', 1);//为请假时
					});
			});

			//获取预约的每天对应周及开始结束时间
			$weeks = Time::getWeekHoursByTime($appointTime, $appointTime + $duration);
			//是否预约的时间段在上班
			$list->whereExists(function($query) use($weeks, $seller_staff_table) {
				$query->select(DB::raw(1))
                      ->from('staff_service_time')
                      ->where('staff_service_time.staff_id', '=', new Expression("{$seller_staff_table}.id"))
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
		};
		//DB::connection()->enableQueryLog();
        $data = $list->skip(($page - 1) * 20)->take(20)->get()->toArray();
        //print_r(DB::getQueryLog());exit;
		return $data;
	}

	/**
	 * 检测服务范围
	 * @param  [type] $staffId  [description]
	 * @param  [type] $mapPoint [description]
	 * @return [type]           [description]
	 */
	public static function checkServiceArea($staffId, $mapPoint) {
		$tablePrefix 			= DB::getTablePrefix();
        $staff_map_table		= DB::getTablePrefix().'staff_map';
        $seller_district_table	= DB::getTablePrefix().'seller_district';

		$count = SellerStaff::where('id', $staffId)
					->where(function($query) use($staffId, $mapPoint, $staff_map_table, $seller_district_table) {
						//在服务人员自定义范围内
						$query->whereExists(function($query) use($staffId, $mapPoint, $staff_map_table) {
							$query->select(DB::raw(1))
			                      ->from('staff_map')
			                      ->where('staff_map.staff_id', '=', $staffId)
			                      ->whereRaw("ST_Contains(".$staff_map_table.".map_pos,GeomFromText('Point({$mapPoint})'))");
						});
						//或者在服务人员的商圈范围内
						$query->orWhereExists(function($query) use($staffId, $mapPoint, $seller_district_table) {
							$query->select(DB::raw(1))
			                      ->from('seller_staff_district')
			                      ->where('seller_staff_district.staff_id', $staffId);
				            
				            $query->join('seller_district', function($join) use($mapPoint, $seller_district_table) {
								$join->on('seller_district.id', '=', 'seller_staff_district.district_id')
									->on(new Expression(''), new Expression(''), new Expression("ST_Contains(".$seller_district_table.".map_pos,GeomFromText('Point({$mapPoint})'))"), 'and', false);
							});
						});
					})
					->count();
		return $count > 0;
	}

	/**
	 * 检测预约时间
	 * @param  Goods  $goods       要预约的服务
	 * @param  int    $appointTime 预约开始时间
	 * @return boolean             是否可以预约
	 */
	public static function checkAppointTime($staffId, $appointTime, $duration) {
		//获取预约的每天对应周及开始结束时间
		$weeks = Time::getWeekHoursByTime($appointTime, $appointTime + $duration);
		//DB::connection()->enableQueryLog();
		$appoints = StaffServiceTime::where('staff_id', $staffId)
					->where(function($query) use($weeks) {
						foreach ($weeks as $week => $times) {
							$query->orWhere(function($query1) use($week, $times) {
								$query1->where('week', '=', $week)
									->where('begin_time', '<=', $times['begin'])
									->where('end_stime', '>=', $times['end']);
							});
						}
					});
		$count = $appoints->count();
		//print_r(DB::getQueryLog());exit;
		//有时间不在上班时间范围内
		if ($count < count($weeks)) {
			return false;
		}

		//预约开始时间
		$beginTime  = $appointTime - SERVICE_DELAY_BEGIN_TIME;
		//预约结束时间
        $endTime = $appointTime + $duration + SERVICE_DELAY_END_TIME;
        $appoints = StaffAppoint::where('staff_id', $staffId)
        						->where('appoint_time', '>=', $beginTime)
		         				->where('appoint_time', '<', $endTime)
		         				->where(function($query) {
									//为接单或禁止接单时
									$query->whereIn('status', [
							            		StaffAppoint::REFUSE_APPOINT_STATUS, //拒绝接单
							            		StaffAppoint::HAVING_APPOINT_STATUS  //有单
							            	])
							            	->orWhere('is_leave', '=', 1);//为请假时
								});

		return $appoints->count() == 0;
	}

	/**
	 * 设置服务人员已被预约时间
	 * @param  [type] $sellerId    卖家编号
	 * @param  [type] $appointTime 预约时间
	 * @param  [type] $duration    服务时长
	 * @param  int $orderId    订单编号
	 * @return [type]              更新状态
	 */
	public static function setAppointHour($staffId, $sellerId, $appointTime, $duration, $orderId) {
        $beginTime  = $appointTime - SERVICE_DELAY_BEGIN_TIME;
        $endTime = $appointTime + $duration + SERVICE_DELAY_END_TIME;
        
		for ($beginTime; $beginTime <= $endTime; $beginTime += SERVICE_TIME_SPAN){
            $appoint = StaffAppoint::where('staff_id', $staffId)
                    ->where("appoint_time", $beginTime)
                    ->first();
            
            // 更新
            if($appoint == true){
                StaffAppoint::where('staff_id', $staffId)
                    ->where("appoint_time", $beginTime)
                    ->update(["status"=>StaffAppoint::HAVING_APPOINT_STATUS, "order_id"=>$orderId, 'appoint_week' => Time::toDate($beginTime,'w')]);
            } else { /* 新增 */
                $appoint = new StaffAppoint();
                $appoint->seller_id = $sellerId;
                $appoint->staff_id  = $staffId;
                $appoint->appoint_day = Time::toDayTime($beginTime);
                $appoint->appoint_time = $beginTime;
                $appoint->order_id = $orderId;
                $appoint->status = StaffAppoint::HAVING_APPOINT_STATUS;
                $appoint->appoint_week = Time::toDate($beginTime,'w');
                $appoint->save();
            }
		}
	}

	public static function incrementExtend($staffId, $field, $num = 1) {
		SellerStaffExtend::where('staff_id',$staffId)->increment($field, $num);
	}

	public static function decrementExtend($staffId, $field, $num = 1) {
        $extend = SellerStaffExtend::where('staff_id',$staffId)->first();
        
        if($extend == true && $extend->$field > 0) {
            SellerStaffExtend::where('staff_id',$staffId)->decrement($field, $num);
        }
	}

	public static function updateComment($staffId, $credit, $specialtyScore, $communicateScore, $punctualityScore) {
		$extend = SellerStaffExtend::where('staff_id',$staffId)->first();
		$extend->comment_total_count++;
		$extend->comment_specialty_total_score += $specialtyScore;
		$extend->comment_specialty_avg_score = $extend->comment_specialty_total_score / $extend->comment_total_count;

		$extend->comment_communicate_total_score += $communicateScore;
		$extend->comment_communicate_avg_score = $extend->comment_communicate_total_score / $extend->comment_total_count;

		$extend->comment_punctuality_total_score += $punctualityScore;
		$extend->comment_punctuality_avg_score = $extend->comment_punctuality_total_score / $extend->comment_total_count;

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
     * @param int $staffId 商家员工编号
     * @param string $credit 评价类型
     * @param double $specialtyScore 专业总分
     * @param double $communicateScore 沟通总分
     * @param double $punctualityScore 守时总分
     */
    public static function deleteComment($staffId, $credit, $specialtyScore, $communicateScore, $punctualityScore) 
    {
		$extend = SellerStaffExtend::where('staff_id',$staffId)->first();
		$extend->comment_total_count--;
		$extend->comment_specialty_total_score -= $specialtyScore;
		$extend->comment_specialty_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_specialty_total_score / $extend->comment_total_count;

		$extend->comment_communicate_total_score -= $communicateScore;
		$extend->comment_communicate_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_communicate_total_score / $extend->comment_total_count;

		$extend->comment_punctuality_total_score -= $punctualityScore;
		$extend->comment_punctuality_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_punctuality_total_score / $extend->comment_total_count;

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
     * 地图坐标是否在服务人员服务范围内
     * @param $staffId 员工编号
     * @param $mapPoint 地图坐标
     */
    public static function checkMapPos($staffId, $mapPoint) {
        $result = SellerStaff::where('id', $staffId)
                               ->join('staff_map', 'staff_map.staff_id', '=', 'seller_staff.id')
                               ->whereRaw("ST_Contains(".DB::getTablePrefix()."staff_map.map_pos,GeomFromText('Point({$mapPoint})'))")
                               ->first();
        if (!$result) {
            $result = SellerDistrict::whereIn('id', function($query) use ($staffId){
                        $query->select('district_id')
                            ->from('seller_staff_district')
                            ->where('staff_id',$staffId);
                    })->whereRaw("ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))")->first();
        }
        return $result ? 1 : 0;
    }

    /**
     * 洗车服务人员列表
     * @param int $districtId 小区名称
     * @param int $page 页码
     */
    public static function getCarList($districtId, $page) 
    {
        return SellerStaff::where('status',1)
            ->where('is_del',0)
            ->whereIn('id', function($query) use ($districtId)
            {
                $query->select('staff_id')
                    ->from('seller_staff_district')
                    ->where('district_id', $districtId);
            })
            ->select('id', 'name', 'avatar', 'job_number')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get()
            ->toArray();
    }

    /**
     * 洗车服务人员可预约时间
     * @param int $staffId 员工编号
     */
    public static function getAppointDay($staffId) {
        $staff = self::getById($staffId);
        $list 		= [];
        if ($staff) {
            $week_days	= [];
            $beginTime 	= $beginTimes = UTC_DAY;
            $endTime 	= $beginTime + (SERVICE_APPOINT_DAY + 1) * 86400 - 1;
            $dusyCount 	= ((DEFAULT_END_ORDER_DATE - DEFAULT_BEGIN_ORDER_DATE) / SERVICE_TIME_SPAN + 1) * 2 / 3;
            $serviceTimes = StaffServiceTime::where('staff_id', $staffId)->get()->toArray();
            foreach ($serviceTimes as $serviceTime) {
                $week_days[$serviceTime['week']][] = $serviceTime;
            }
            //不上班时状态
            $no_work_status = 2;

            while($beginTime <= $endTime) {
                $hour 		= $beginTime - Time::toDayTime($beginTime);
                if ($hour >= DEFAULT_BEGIN_ORDER_DATE && //大于等于默认开始接单时间
                    $hour <= DEFAULT_END_ORDER_DATE) {//小于等于默认结束接单时间

                    $day 	= Time::toDate($beginTime, 'm-d');
                    $hour 	= Time::toDate($beginTime, 'H:i');
                    $week 	= Time::toDate($beginTime, "w");
                    //默认为休息状态
                    $status = $no_work_status;
                    if (isset($week_days[$week])) {
                        $end_hour = Time::toDate($beginTime + SERVICE_TIME_SPAN - 1, 'H:i:s');
                        foreach ($week_days[$week] as $week_day) {
                            if ($week_day['beginTime'] <= $hour &&
                                $week_day['endStime']>= $end_hour) {
                                $status = 1; //上班
                                break;
                            }
                        }
                    }
                    //如果日期不存在,则添加日期
                    if(!isset($list[$day])) {
                        $list[$day] = [
                            'day' => $day,
                            'isDusy' => false,
                            'hours' => [],
                            'dusyCount' => 0
                        ];
                    }

                    //当预约的时间小于当前时间时,设为不可预约 2
                    if ($beginTime < UTC_TIME) {
                        $status = $no_work_status; //不可预约
                    }

                    $list[$day]['hours'][$hour] = [
                        'hour'		    => $hour,
                        'appointStatus' => $status,
                        'time'          => $beginTime
                    ];

                    if ($status != 1) {
                        $list[$day]['dusyCount']++;
                    }
                }
                $beginTime += SERVICE_TIME_SPAN;
            };
            $appoints = StaffAppoint::where('staff_id', $staffId)
                ->where('appoint_time', '>=', $beginTimes)
                ->where('appoint_time', '<', $endTime)
                ->orderBy('appoint_time', 'asc')
                ->get()
                ->toArray();

            while (count($appoints) > 0) {
                $appoint 		= array_shift($appoints);
                $appoint_time 	= $appoint['appointTime'];
                $hour 			= $appoint_time - Time::toDayTime($appoint_time);
                $day 			= Time::toDate($appoint_time, 'm-d');

                //如果日期下的预约时间不存在,则添加预约时间
                if(isset($list[$day]['hours'][$hour]) &&
                    $list[$day]['hours'][$hour]['status'] != 1) {
                    continue;
                }

                if ($hour >= DEFAULT_BEGIN_ORDER_DATE && //大于等于默认开始接单时间
                    $hour <= DEFAULT_END_ORDER_DATE) {//小于等于默认结束接单时间
                    //默认为可预约状态
                    $status = 1; //可预约

                    //当预约的时间小于当前时间或拒绝接单时,设为不可预约 2
                    if ($appoint_time < UTC_TIME || //时间小于当前时间
                        $appoint['status'] == StaffAppoint::REFUSE_APPOINT_STATUS) {//拒绝接单
                        $status = $no_work_status; //不可预约
                    } elseif($appoint['status'] == StaffAppoint::HAVING_APPOINT_STATUS) {//有订单
                        $status = 0; //已经预约
                    } elseif($appoint['is_leave'] == 1) {//请假
                        $status = $no_work_status; //请假
                    }

                    if ($status != 1) {
                        $hour = Time::toDate($appoint_time, 'H:i');
                        $list[$day]['hours'][$hour] = [
                            'hour'		    => $hour,
                            'appointStatus' => $status,
                            'time'          => $appoint_time
                        ];
                        $list[$day]['dusyCount']++;
                    }
                }
            };
            foreach($list as $day => $item) {
                if($item['dusyCount'] >= $dusyCount) {
                    $item['isDusy'] = true;
                }
                unset($item['dusyCount']);
                ksort($item['hours']);
                $item['hours'] = array_values($item['hours']);
                $list[$day] = $item;
            }
            ksort($list);
            $list = array_slice(array_values($list), 0, SERVICE_APPOINT_DAY);
        }
        return $list;

    }
}
