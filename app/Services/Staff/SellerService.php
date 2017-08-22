<?php namespace YiZan\Services\Staff;
use YiZan\Models\OrderRate;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\Staff\SellerStaff;
use YiZan\Models\Seller;
use YiZan\Models\Order;
use YiZan\Models\Article;
use YiZan\Models\System\SellerWithdrawMoney;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffServiceTimeSet;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\System\User;
use YiZan\Models\SellerMap;
use YiZan\Models\SellerBank;
use YiZan\Models\UserVerifyCode;
use YiZan\Utils\Helper;
use YiZan\Services\UserService;
use YiZan\Services\System\StaffStimeService;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Lang, Exception, Request, Validator;
class SellerService extends \YiZan\Services\SellerService {

    /**
     * 更改店铺信息
     * @param $sellerId 店铺编号
     * @param $data 店铺信息
     */
    public static function update($sellerId, $data) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api_staff.success.update')
        ];
        //$data = json_decode($data, true);
        // print_r($data);
        // exit; 
        if (empty($data)) {
            $result['code'] = 60008;
            return $result;
        }
        $seller = Seller::where('id', $sellerId)->first();

        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }
        $seller_data = [];
        //头像图片上传
        if (!empty($data['img'])) {
          if ($data['img'] != $seller->logo) {
            $logo = self::moveSellerImage($seller->id, $data['img']);
            $seller_data['logo'] = $logo;
            if (!$logo) {//转移图片失败
                $result['code'] = 30606;
                return $result;
            }
          }
        }
        if($data['name'] == true) {
          $seller_data['name'] = $data['name'];
        }
        if($data['status'] == true) {
          $seller_data['status'] = (int)$data['status'];
        }
        if($data['tel'] == true) {
          $seller_data['service_tel'] = $data['tel'];
        }
        if($data['serviceRange'] == true) {
          $seller_data['address'] = $data['serviceRange'];
        }
        if($data['address'] == true) {
            $seller_data['address'] = $data['address'];
        }
        if($data['addressDetail'] == true) {
            $seller_data['address_detail'] = $data['addressDetail'];
        }
        if($data['provinceId'] == true) {
            $seller_data['province_id'] = $data['provinceId'];
        }
        if($data['cityId'] == true) {
            $seller_data['city_id'] = $data['cityId'];
        }

        $seller_data['area_id'] = $data['areaId'];
        if($data['mapPointStr'] == true) {
            $mapPoint = Helper::foramtMapPoint($data['mapPointStr']);
            $seller_data['map_point_str'] = $mapPoint;
            $seller_data['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");;
        }
        if($data['brief'] == true) {
          $seller_data['brief'] = $data['brief'];
        }

        if (isset($data['serviceFee']) && !empty($data['serviceFee'])) {
          $seller_data['service_fee'] = (float)$data['serviceFee'];
        }
        if (isset($data['deliveryFee']) && !empty($data['deliveryFee'])) {
          $seller_data['delivery_fee'] = (float)$data['deliveryFee'];
        }

        if(is_numeric($data['isCashOnDelivery'])) {
          $seller_data['is_cash_on_delivery'] = $data['isCashOnDelivery'];
        }

        if (!empty($data['article'])) {
            $article = Article::where('seller_id', $sellerId)->first();
            if ($article) {
              Article::where('seller_id', $sellerId)->update(['content'=>$data['article']]);
            } else {
              $data = array(
                  'seller_id' => $sellerId,
                  'content' => $data['article'],
                  'status' => 1,
                );
              Article::insert($data);
            }

        }
        //print_r($seller_data);
        if (!empty($seller_data) && false === Seller::where('id', $sellerId)->update($seller_data)) {
            $result['code'] = 60004;
            return $result;
        }

        if (!empty($data['businessHour'])) {
            $weeks = $data['businessHour']['weeks'];
            $hours = $data['businessHour']['hours'];
            $stime = StaffServiceTimeSet::where('seller_id', $seller->id)->first();
            if (!$stime) { //没有则新增
                StaffStimeService::insert($sellerId, $weeks, $hours);
            } else {  //有则更新
                StaffStimeService::update($sellerId, $stime->id, $weeks, $hours);
            }
        }
        if (!empty($data['deliveryTime'])) {
            $stimes = $data['deliveryTime']['stimes'];
            $etimes = $data['deliveryTime']['etimes'];
            $dtime = SellerDeliveryTime::where('seller_id', $seller->id)->get();
            if ($dtime) {
              SellerDeliveryTime::where('seller_id', $seller->id)->delete();
            }
            foreach ($stimes as $key => $value) {
                  $delivery = new SellerDeliveryTime();
                  $delivery->seller_id     = $seller->id;
                  $delivery->stime         = $value;
                  $delivery->etime         = $etimes[$key];
                  $delivery->save();
            }

        }
        $result = self::getSellerInfo($sellerId);

        return $result;
    }

    public static function getSellerInfo($sellerId) {

        $seller = Seller::where('id', $sellerId)->with('extend', 'deliveryTimes','province','city','area')->first();
        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }

        $stime =  StaffServiceTimeSet::where('seller_id', $sellerId)->first();
        $seller->businessHours = array(
                'weeks' => $stime->week ? $stime->week : [],
                'hours' => $stime->hours ? $stime->hours : [],
            );
        foreach ($seller->deliveryTimes as $key => $value) {
           $stimes[] = $value['stime'];
           $etimes[] = $value['etime'];
        }
        $seller->deliveryTime = array(
                'stimes' => !empty($stimes) ? $stimes : [],
                'etimes' => !empty($etimes) ? $etimes : [],
            );
       // print_r($seller->businessHours);
        $orderstatus = array(
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_SELLER_DELETE,
            ORDER_STATUS_ADMIN_DELETE
        );
        $turnover = Order::where('seller_id', $seller->id)
                            ->whereNotIn('status', [
                                ORDER_STATUS_SELLER_DELETE,
                                ORDER_STATUS_ADMIN_DELETE,
                                ORDER_STATUS_BEGIN_USER

                            ])->where('pay_time','>',0)
                            ->where('cancel_time',NULL)
                            ->where('create_day', UTC_DAY)
                            ->sum('total_fee');
        $ordernum = Order::where('seller_id', $seller->id)->whereNotIn('status', $orderstatus)->where('create_day', UTC_DAY)->count();
        $article = Article::where('seller_id', $seller->id)->pluck('content');
        $result = array(
                    'balance' => $seller->extend->money,
                    'name' => $seller->name,
                    'img' => $seller->logo,
                    'status'=> $seller->status,
                    'deliveryTime' => $seller->deliveryTime,
                    'tel' => $seller->service_tel,
                    'brief' => $seller->brief,
                    'serviceRange' => $seller->address,
                    'address' => $seller->address,
                    'addressDetail' => $seller->address_detail,
                    'provinceId' => $seller->province_id,
                    'cityId' => $seller->city_id,
                    'areaId' => $seller->area_id,
                    'mapPointStr' => $seller->map_point_str,
                    'turnover' => $turnover,
                    'orderNum' => $ordernum,
                    'businessHour' => $seller->businessHours,
                    'article' => $article,
                    'deliveryFee'=>$seller->delivery_fee,
                    'serviceFee'=>$seller->service_fee,
                    'region'=> $seller->area_id > 0 ? $seller->province->name.'-'.$seller->city->name.'-'.$seller->area->name :$seller->province->name.'-'.$seller->city->name,
                    'contacts' => $seller->contacts,
                    'deduct' => $seller->deduct,
                    'isCashOnDelivery' => $seller->is_cash_on_delivery
                );
       // print_r($result);
        return $result;
    }

    /**
     * 获取评价列表
     * @param int $sellerId         商家编号
     * @param int $type             类型[1 未回复， 2 已回复]
     * @param int $page             页码
     * @return list
     */
    public function getOrderRates($sellerId, $type, $page){
        $star = OrderRate::where('seller_id', $sellerId)
                          ->selectRaw('sum(star)/count(*) as score')
                          ->first();

        $unReply = OrderRate::where('seller_id', $sellerId)
                            ->where('reply', '')
                            ->where('reply_time', 0)
                            ->count();

        $reply = OrderRate::where('seller_id', $sellerId)
                          ->where('reply', '<>', '')
                          ->where('reply_time', '>', 0)
                          ->count();

        $score = round($star->score,1);

        $list = OrderRate::where('seller_id', $sellerId);

        if($type == 1){
            $list->where('reply', '')
                 ->where('reply_time', 0);
        }else{
            $list->where('reply', '<>', '')
                ->where('reply_time','>',  0);
        }

        $list = $list->orderBy('id', 'desc')
                     ->with('user')
                     ->skip(($page - 1) * 20)
                     ->take(20)
                     ->get()
                     ->toArray();
        foreach ($list as $k=>$v) {
            $list[$k]['userName'] = $v['user']['name'];
            if ($v['isAno'] == 1) {
                $firstStr = String::msubstr($v['user']['name'], 0, 1, 'utf-8',false);
                $lastStr = String::msubstr($v['user']['name'], -1, 1, 'utf-8',false);
                $list[$k]['userName'] = $firstStr.'***'.$lastStr;
            }

            $list[$k]['avatar'] = $v['user']['avatar'];
            $list[$k]['replyTime'] = Time::toDate($v['replyTime'], 'Y-m-d H:i');
            $list[$k]['createTime'] = Time::toDate($v['createTime'], 'Y-m-d H:i');
            unset($list[$k]['user']);
        }
        return ['score'=>$score,'unReply'=>$unReply,'reply'=>$reply,'eva'=>$list];
    }

    /**
     * 评价回复
     * @param int $sellerId             商家编号
     * @param int $id                   订单评价编号
     * @param int $content              评价内容
     * @return 评价的结果
     */
    public function replyOrderRate($sellerId, $id, $content){

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_order_rate')
        );

        $orderRate = OrderRate::where('seller_id', $sellerId)
                              ->where('id', $id)
                              ->first();

        if(empty($orderRate)){
            $result['code'] = 20003;
            return $result;
        }

        $orderRate->reply = $content;

        $orderRate->reply_time = UTC_TIME;

        $orderRate->save();

        return $result;
    }

    /**
     * 员工列表
     * @param int $sellerId             商家编号
     * @param int $type                 类型
     * @return 评价的结果
     */
    public function getStaffLists($sellerId, $type){
        $list = SellerStaff::where('seller_id', $sellerId)
                           ->whereIn('type', ['0', '3', $type])
                           ->where('order_status', 1)
                           ->whereNotIn('id', function($query) use ($sellerId){
                                $query->select('staff_id')
                                    ->from('staff_leave')
                                    ->where('begin_time', '<=', UTC_TIME)
                                    ->where('end_time', '>=', UTC_TIME)
                                    ->where('is_agree', 1)
                                    ->where('status', 1);
                            })
                           ->get()
                           ->toArray();
        return $list;
    }

    /**
     * 商家账单
     * @param int $sellerId             商家编号
     * @param int $status                 类型,1收入、2提现、3充值
     * @return
     */
    public function getSellerAccount($sellerId, $type, $status = 0, $page){
        $list = [];
        $seller = Seller::where('id', $sellerId)->first();
        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }

        $lists = SellerMoneyLog::where('seller_id', $sellerId)
                               ->where('money', '>', 0);

        if ($type == 1 && $status != 2) {
            if ($status == 1) {
                $lists->where('type', SellerMoneyLog::TYPE_ORDER_CONFIRM);
            } else if($status == 3){
                $lists->where('type', SellerMoneyLog::TYPE_SELLER_RECHARGE);
            } else {
                // $lists->whereIn('type', [
                //     SellerMoneyLog::TYPE_APPLY_WITHDRAW,
                //     SellerMoneyLog::TYPE_ORDER_CONFIRM
                // ])->orWhere(function($query){
                //     $query->where('type', SellerMoneyLog::TYPE_ORDER_PAY)
                //           ->where('status', 1);
                // });

                // 取消待到账的余额
                $lists->whereIn('type', [
                    SellerMoneyLog::TYPE_APPLY_WITHDRAW,
                    SellerMoneyLog::TYPE_ORDER_CONFIRM
                ]);
            }
        } else {
            $lists->where('type', SellerMoneyLog::TYPE_APPLY_WITHDRAW);
        }
        
        $lists = $lists->orderBy('create_time', 'desc')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get()
            ->toArray();
            
        $statusStr = [
            ['moneyColor' => '#FF2D4B', 'statusColor' => '#DE973C', 'statusStr' => '待审核'],
            ['moneyColor' => '#DE973C', 'statusColor' => '#DE973C', 'statusStr' => '已到账'],
            ['moneyColor' => '#FF2D4B', 'statusColor' => '#DE973C', 'statusStr' => '已拒绝'],
        ];
        foreach ($lists as $k => $v) {
            $list[$k] = $statusStr[$v['status']];
            $list[$k]['createTime'] = yzday($v['createTime']);
            $list[$k]['status'] = $v['status'];

            if ($v['type'] == SellerMoneyLog::TYPE_APPLY_WITHDRAW) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '提现';
            } else if($v['type'] == SellerMoneyLog::TYPE_SELLER_RECHARGE){
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = '充值';
            } else {
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = $v['type'] == SellerMoneyLog::TYPE_ORDER_PAY ? '待到账' : '入余额';
            }

            if ($v['type'] == SellerMoneyLog::TYPE_ORDER_PAY) {
                $list[$k]['statusStr'] = '待到账';
            }
        }
        return $list;
    }

    public function setSellerMap($sellerId, $option = array()) {

      if (!empty($option['address']) && !empty($option['mapPoint']) && !empty($option['mapPos'])) {
        $mapPoint = Helper::foramtMapPoint($option['mapPoint']);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($option['mapPos']);
        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        }

        $data = array(
          'address' => $option['address'],
          'map_pos_str' => $mapPos["str"],
          'map_point_str' => $mapPoint,
          'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"),
          'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')")
          );

        Seller::where('id', $sellerId)->update($data);

        SellerMap::where('seller_id',$sellerId)->update([
                'map_pos'=>DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')"),
                'map_point'=>DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')")
                ]);

      }
      $seller = Seller::where('id', $sellerId)->first();
      $result = $seller ? $seller->toArray() : [];
      return $result;
    }

    /**
     * 更新银行卡
     * @param  integer $sellerId   机构或个人编号
     * @param  integer $id         银行信息编号
     * @param  string  $bank       银行名称
     * @param  string  $bankNo     银行卡号
     * @param  string  $mobile     验证手机
     * @param  string  $verifyCode 验证码
     * @return array               处理结果
     */
    public static function saveBankInfo($sellerId,$id, $bank, $bankNo, $mobile,$name, $verifyCode){
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'bank'         => ['required'],
            'bank_no'     => ['required'],
            'mobile' 	 	=> ['required','mobile'],
            'code' 	 	=> ['required','size:6'],
            'name' 	 	=> ['required'],
        );

        $validata = array(
            'seller_id' => $sellerId,
            'bank' 		=> $bank,
            'bank_no'   => $bankNo,
            'mobile'	=> $mobile,
            'name'	    => $name,
            'code'		=> $verifyCode
        );

        $messages = array(
            'bank.required'		=> 10150,	// 请输入银行
            'bank_no.required'	=> 10151,	// 请输入银行卡号
            'mobile.required'		=> 10101,
            'mobile.mobile'		=> 10102,
            'name.required' 		=> 10208,
            'code.required' 		=> 10103,
            'code.size' 			=> 10104,
        );

        $validator = Validator::make($validata, $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $bankObj = new SellerBank();
        if( $id > 0) {
            $check = SellerBank::where('id', $id)->first();
            if (!$check) {
                $result['code'] = 10154;
                return $result;
            }
            $bankObj->where('id',$id);
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $bankObj->seller_id 	= $sellerId;
        $bankObj->bank 			= $bank;
        $bankObj->bank_no 		= $bankNo;
        $bankObj->mobile 		= $mobile;
        $bankObj->name 		    = $name;
        DB::beginTransaction();
        try
        {
            $bankObj->save();
            $result['data'] = $bankObj;
            UserVerifyCode::destroy($verifyCodeId);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 获取银行卡信息
     * @param int $sellerId 商家编号
     * @param int $id 银行卡信息编号
     */
    public static function getBankInfo($sellerId) {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );
        $result['data'] = SellerBank::where('seller_id', $sellerId)->first();
        if (!$result['data']) {
            $result['code'] = 10154;
            return $result;
        }
        $result['data'] = $result['data']->toArray();
        //银行卡号
        $str = '**** **** **** ';
        $bankNolen = strlen($result['data']['bankNo']);
        $bankNo = String::msubstr($result['data']['bankNo'], 0, ($bankNolen-4), 'utf-8',false);
        $result['data']['bankNo'] = preg_replace('/'.$bankNo.'/', $str, $result['data']['bankNo'], 1);
        //户主名称
        $name = $result['data']['name'];
        $firstName = String::msubstr($name, 0, 1, 'utf-8',false);
        $result['data']['name'] = preg_replace('/'.$firstName.'/', '*', $name, 1);

        return $result;
    }

    /**
     * 删除银行卡信息
     * @param int $sellerId 商家编号
     * @param int $id 银行卡信息编号
     */
    public static function delBankInfo($sellerId, $id) {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );
        $bank = SellerBank::where('seller_id', $sellerId)->where('id', $id)->first();
        if (!$bank) {
            $result['code'] = 10154;
            return $result;
        }
        //SellerBank::where('seller_id', $sellerId)->where('id', $id)->delete();
        SellerBank::where('seller_id', $sellerId)->delete();
        return $result;
    }
}
