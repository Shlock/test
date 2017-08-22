<?php
namespace YiZan\Http\Controllers\Wap;
use YiZan\Utils\Image;
use Input, View, Redirect, Request, Time, Response, Cache, YiZan\Utils\String,Log;
use YiZan\Models\System\User;
use YiZan\Models\OnlineLog;
use YiZan\Models\Order;

/**
 * 用户中心控制器
 */
class UserCenterController extends UserAuthController {
    public function __construct()
    {
        parent::__construct();
        // $result = $this->requestApi('msg.status');
        // View::share('counts',$result['data']);
        View::share('nav','mine');
    }
    /**
     * 用户中心首页
     */
    public function index() {
        View::share('user',$this->user);

        $detailUser = User::find($this->user['id']);

        view::share('isExtensionWorker', $detailUser->is_extension_worker);

        $balance_result = $this->requestApi('user.balance');


        View::share('balance', $balance_result['data']['balance']);
        $result = $this->requestApi('seller.check', ['id'=>$this->userId]);
        View::share('seller',$result['data']);
        View::share('title',"- 用户中心");

        if($result['code'] == 0){
            View::share('share_active',$result['data']);

            if(!empty($result['data'])){
                $desc = $result['data']['brief'];
                View::share('desc',$desc);

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
                $weixin_arrs = $this->requestApi('Useractive.getweixin',array('url' => $url));

                if($weixin_arrs['code'] == 0){
                    View::share('weixin',$weixin_arrs['data']);
                }
                $link_url = u('UserCenter/obtaincoupon');
                View::share('link_url',$link_url);

            }
        }
        return $this->display();
    }

    /**
     * 计算两点地理坐标之间的距离
     * @param  Decimal $longitude1 起点经度
     * @param  Decimal $latitude1  起点纬度
     * @param  Decimal $longitude2 终点经度 
     * @param  Decimal $latitude2  终点纬度
     * @param  Int     $unit       单位 1:米 2:公里
     * @param  Int     $decimal    精度 保留小数位数
     * @return Decimal
     */
    private function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=1, $decimal=2){

        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926;

        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;

        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI /180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if($unit==2){
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);

    }
    /**
     * 我的推广数据
     */
    public function extension(){
        $begin = Input::get('begin');
        $end = Input::get('end');

        if(empty($begin) || empty($end)){
            $end = date('Y-m-d', time()+3600*24);
            $begin = date('Y-m-d', time()-3600*24*7);
        }

        $uid =  $this->user['id'];
        
        $unixBeginTime = strtotime($begin);
        $unixEndTime = strtotime($end);

        // 分享注册用户数的获取
        $extensionUsers = User::where('recommend_uid','=', $uid)
                                ->where('reg_time','>=',$unixBeginTime)
                                ->where('reg_time','<=',$unixEndTime)->get();


        // 有效用户数获取
        $detailUser = User::find($this->user['id']);
        $extensionLat = $detailUser->extension_lat;
        $extensionLng = $detailUser->extension_lng;
        $extensionRange = $detailUser->extension_range;

        $validUsers = array();
        if(!$extensionUsers->isEmpty()){
            foreach ($extensionUsers as $key => $user) {
                $distance = $this->getDistance($extensionLat,$extensionLng,$user->reg_lat,$user->reg_lng);
                if($distance <= $extensionRange * 1000){
                    $validUsers[] = $user;
                }
            }
        }

        $activeUserNum = array();
        if(count($extensionUsers)){
            foreach ($extensionUsers as $key => $user) {
                $onlineInfo = OnlineLog::where('uid','=',$user->id)->get();
                if(count($onlineInfo)){
                    $activeUserNum[] = $user;
                }
            }
        }

        $weixinActiveUserNum = array(); 
        if(count($extensionUsers)){
            foreach ($extensionUsers as $key => $user) {
                $onlineInfo = OnlineLog::where('uid','=',$user->id)
                                        ->where('device_type','=','web')->get();
                if(count($onlineInfo)){
                    $weixinActiveUserNum[] = $user;
                }
            }
        }

        $buyUserNum = array();
        if(count($extensionUsers)){
             foreach ($extensionUsers as $key => $user) {
                $order = Order::where('user_id','=',$user->id)
                                ->where('pay_time','!=',0)->get();
                if(count($order)){
                    $buyUserNum[] = $user;
                }
            }
        }

        view::share('begin',$begin);
        view::share('end',$end);
        view::share('extensionUserNum',count($extensionUsers));
        view::share('validUserNum',count($validUsers));
        view::share('activeUserNum',count($activeUserNum));
        view::share('weixinActiveUserNum',count($weixinActiveUserNum));
        view::share('buyUserNum',count($buyUserNum));
        return $this->display();
    }

    /**
     * 用户信息
     */
    public function info() {
        View::share('user',$this->user);
        View::share('title',"- 用户信息");
        return $this->display();
    }

    /**
     * 更改用户信息
     */
    public function updateinfo() {
        $name = trim(Input::get('name'));
        $avatar = trim(Input::get('avatar'));
        $result = $this->requestApi('user.info.update',array('name'=>$name,'avatar'=>$avatar));
        if ($result['code'] == 0){
            $this->setUser($result['data']);
        }
        return Response::json($result);
    }


    /**
     * 修改昵称
     */
    public function nick(){
        View::share('user',$this->user);
        return $this->display();
    }
    /**
     * 我的优惠券
     */
    // public function coupon() {
    // 	$args = Input::all();
    // 	$list = $this->requestApi('user.promotion.lists',$args);
    // 	if($list['code'] == 0)
    // 		View::share('list',$list['data']);
    // 	View::share('args',$args);
    // 	View::share('is_show_top',false);
    // 	View::share('title',"- 优惠券");
    // 	if(Input::ajax()){
    // 		return $this->display('coupon_item');
    // 	}else{
    // 		return $this->display();
    // 	}
    // }

    /**
     * 优惠券兑换
     */
    // public function excoupon() {
    // 	$sn = Input::get('sn');
    // 	$result = $this->requestApi('user.promotion.exchange',array('sn'=>$sn));
    // 	die(json_encode($result));

    // }

    /**
     * 分享奖励
     */
    public function share() {
        View::share('title',"- 分享");
        return $this->display();
    }

    /**
     * 优惠券领取
     */
    public function recoupon() {
        $id = Input::get('id');
        $result = $this->requestApi('user.promotion.receive',array('id'=>$id));
        die(json_encode($result));

    }


    /**
     * 我的收藏
     */
    public function collect() {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] = 1;
        }
        $list = $this->requestApi('collect.lists',$args);
        //print_r($list);
        View::share('args',$args);
        View::share('title',"- 收藏");
        if($list['code'] == 0)
            View::share('list',$list['data']);
        return $this->display();

    }

    /**
     * 取消店铺服务收藏
     */
    public function delcollect() {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] == 1;
        }
        $result = $this->requestApi('collect.delete',$args);
        return Response::json($result);
    }

    //添加收藏
    public function addcollect() {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] == 1;
        }
        $result = $this->requestApi('collect.create',$args);
        return Response::json($result);
    }

    /**
     * 我的常用地址
     */
    public function address() {
        $args = Input::all();
        $list = $this->requestApi('user.address.lists',$args);
        View::share('list',$list['data']);
        View::share('args',$args);
        View::share('title',"- 常用地址");
        View::share('nav_back_url', u('UserCenter/index'));
        Session::set('address_info', null);
        Session::save();
        if(Input::ajax()){
            return $this->display('address_item');
        }else{
            return $this->display();
        }
    }

    /**
     * 常用地址详情
     */
    public function addressdetail() {
        $address_info = Session::get('address_info');
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            if ($address_info) {
                $data['data']['detailAddress'] = $address_info['detailAddress'];
                $data['data']['mapPoint'] = $address_info['mapPoint'];
            }
            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            View::share('data', $address_info);
            View::share('title',"- 添加地址");
        }

        return $this->display();
    }
    /**
     * 确定订单时新增地址
     */
    public function addresssdetail() {
        $address_info = Session::get('address_info');
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            if ($address_info) {
                $data['data']['detailAddress'] = $address_info['detailAddress'];
                $data['data']['mapPoint'] = $address_info['mapPoint'];
            }
            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            View::share('data', $address_info);
            View::share('title',"- 添加地址");
        }
        return $this->display();
    }

    /**
     * 选择地址
     */
    public function addressmap() {
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            //print_r($data);
            View::share('data', $data['data']);
        }
        return $this->display();
    }

    public function saveMap() {
        $address_info = Session::get('address_info');
        $address_info['detailAddress'] = Input::get('address');
        $address_info['mapPoint'] = Input::get('mapPoint');

        Session::put('address_info', $address_info);
        Session::save();
        return $this->success('成功');
    }

    public function saveAddrData() {
        $address_info = Input::all();
        Session::put('address_info', $address_info);
        Session::save();
        return $this->success('成功');
    }

    /**
     * 搜索地址
     */
    public function addrsearch() {
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            View::share('data', $data['data']);
        }
        return $this->display();
    }

    /**
     * 获取城市数据
     */
    public function getcity() {
        $data = $this->requestApi('app.init');
        die(json_encode($data['data']['province']));
    }

    /**
     * 常用地址操作
     */
    public function saveaddress() {
        $data = Input::all();
        $result = $this->requestApi('user.address.create',$data);
        Session::set('address_info', null);
        Session::save();
        die(json_encode($result));
    }
    /**
     * 删除地址
     */
    public function deladdress() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.delete',array('id'=>$id));
        die(json_encode($result));

    }

    /**
     * 设置默认地址
     */
    public function setdefault() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.setdefault',array('id'=>$id));
        if ((int)Input::get('change') == 1) {
            Session::forget('defaultAddress');
            Session::save();
        }
        die(json_encode($result));
    }

    /**
     * 我的消息
     */
    public function message() {
        $args = Input::all();
        $list = $this->requestApi('msg.lists',['page' => $args['page']]);
        if($list['code'] == 0)
            View::share('list',$list['data']);
        View::share('args',$args);
        if(Input::ajax()){
            return $this->display('message_item');
        }else{
            View::share('nav','message');
            View::share('title',"- 消息");
            return $this->display();
        }
    }
    /**
     * 我的消息
     */
    public function msgshow() {;
        $args = Input::get();
        $list = $this->requestApi('msg.getdata',['sellerId'=>$args['sellerId'],'page' => $args['page']]);
        if($list['code'] == 0){
            View::share('data',$list['data']);
        }
        View::share('args',$args);
        if(Input::ajax()){
            return $this->display('msgshow_item');
        }else{
            View::share('msgshow','yes');
            View::share('nav','message');
            View::share('title',"- 消息内容");
            return $this->display();
        }
    }
    /**
     * 删除我的消息
     */
    public function delmessage() {
        $id = Input::get('id');
        $result = $this->requestApi('msg.delete',array('id'=>$id));
        die(json_encode($result));
    }

    /**
     * [readmsg 读消息]
     * @return [type] [description]
     */
    public function readmsg() {
        $messageid = Input::get('mid');
        $result = $this->requestApi('msg.read',array('id' => $messageid));
        die(json_encode($result));
    }
    /**
     * 意见反馈
     */
    public function feedback() {
        View::share('title','- 意见反馈');
        return $this->display();
    }

    /**
     * 增加意见反馈
     */
    public function addfeedback() {
        $content = strip_tags(Input::get('content'));
        $result = $this->requestApi('feedback.create',array('content'=>$content,'deviceType'=>'wap'));
        die(json_encode($result));
    }

    /**
     * 用户登出
     */
    public function logout() {
        Session::set('user','');
        Session::set('reservation_data','');
        Session::set('orderData', '');
        Session::save();
        $this->setSecurityToken(null);
        return Redirect::to(u('UserCenter/index'));
    }

    /**
     * 用户下单
     */
    public function order(){
        $option = Input::all();
        if (!isset($option['duration'])) {
            $option['duration'] = 0;
        } else {
            $option['duration'] = $option['duration'] * 3600;
        }
        $result = $this->requestApi('order.create',$option);
        echo json_encode($result);
        exit;
        if(Input::ajax()){
            $result = $this->requestApi('order.create',$option);
            echo json_encode($result);
        } else {
            $result = $this->requestApi('order.detail',$option);
            if($result['code'] == 0){
                View::share('order',$result['data']);
                return $this->display();
            } else {
                $this->error($result['msg']);
            }
        }

    }

    /*
    * 举报历史
    */
    public function report() {
        $list = $this->requestApi('Ordercomplain.lists');
        //var_dump($list);
        if($list['code'] == 0)
            View::share('list',$list['data']);
        return $this->display();
    }

    /*
    * 举报详情
    */
    public function reportdetail() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('Ordercomplain.get',array('complainId'=>$id));
        if($result['code'] == 0)
            $data = $result['data'];
        $data['image'] = explode(',', $data['images']);
        View::share('data',$data);
        return $this->display();
    }

    /*
    * app举报详情
    */
    public function appreportdetail() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('Ordercomplain.get',array('complainId'=>$id));
        if($result['code'] == 0){
            $data = $result['data'];
            $data['image'] = explode(',', $data['images']);
            View::share('data',$data);
            View::share('is_show_top',false);
        }
        return $this->display();
    }

    /**
     * 我的电话
     */
    public function mobile() {
        $list = $this->requestApi('user.mobile.lists');
        View::share('list', $list['data']);
        View::share('title', '- 我的电话');
        View::share('nav_back_url',u('UserCenter/index'));
        return $this->display();
    }
    /**
     * 确认订单时选择电话
     */
    public function mobiles() {
        $list = $this->requestApi('user.mobile.lists');
        View::share('list', $list['data']);
        View::share('title', '- 我的电话');
        Session::put("url",u('Order/createmoreinfo') );
        Session::save();
        View::share('nav_back_url',Session::get('url'));
        return $this->display();
    }

    /**
     * 删除地址
     */
    public function delmobile() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.mobile.delete',array('mobileId'=>$id));
        return Response::json($result);
    }

    /**
     * 设置默认地址
     */
    public function setdefaultmobile() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.mobile.setdefault',array('mobileId'=>$id));
        return Response::json($result);
    }

    /**
     * 电话详情
     */
    public function mobiledetail() {
        View::share('title', '- 新增电话');
        return $this->display();
    }
    /**
     * 确定订单时新增电话
     */
    public function mobilesdetail() {
        View::share('title', '- 新增电话');
        return $this->display();
    }
    /**
     * 保存电话
     */
    public function savemobile() {
        $data = Input::get();
        $result = $this->requestApi('user.mobile.create', $data);
        return Response::json($result);
    }

    /**
     * 修改密码
     */
    public function updatepwd() {
        View::share('user', $this->user);
        View::share('title', '- 修改密码');
        return $this->display();
    }

    /**
     * 更新密码
     */
    public function savepwd() {
        $args = Input::get();
        unset($args['pwds']);
        $result = $this->requestApi('user.repwd', $args);
        if ($result['code'] == 0) {
            Session::set('user','');
            Session::set('reservation_data','');
            $this->setSecurityToken(null);
        }
        return Response::json($result);
    }


    /**
     * 生成验证码
     */
    public function verify() {
        $mobile = Input::get('mobile');
        $result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile));
        die(json_encode($result));
    }


    /**
     * 关于我们
     */
    public function aboutus() {
        $aboutus = $this->getConfig('aboutus');
        View::share('aboutus', $aboutus);
        return $this->display();
    }

    public function userhelp() {
        $userhelp = $this->getConfig('wap_order_notice');
        View::share('userhelp', $userhelp);
        return $this->display();
    }

    /**
     * 邀请好友
     */
    public function invite() {
        View::share('title', '- 邀请好友');
        return $this->display();
    }

    public function config() {
        View::share('title', '- 设置');
        return $this->display();
    }

    /**
     * 换绑手机号(验证当前手机)
     */
    public function verifymobile() {
        View::share('user',$this->user);
        return $this->display();
    }

    /**
     * 换绑手机号(验证当前手机)
     */
    public function doverifymobile() {
        $args = [
            'mobile' => Input::get('mobile'),
            'verifyCode' => Input::get('code')
        ];
        $result = $this->requestApi('user.info.verifymobile',$args);
        return Response::json($result);
    }


    /**
     * 换绑手机号(验证新手机)
     */
    public function changemobile() {
        if ($_SERVER['HTTP_REFERER'] != u('UserCenter/verifymobile')) {
            $this->error('请先验证手机号码');
        }
        return $this->display();
    }

    /**
     * 换绑手机号(验证新手机)
     */
    public function dochangemobile() {
        $args = [
            'oldMobile' => $this->user['mobile'],
            'mobile' => Input::get('mobile'),
            'verifyCode' => Input::get('code')
        ];
        $result = $this->requestApi('user.updatemobile',$args);
        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 修改密码
     */
    public function repwd() {
        return $this->display();
    }

    /**
     * 执行修改密码
     */
    public function dorepwd() {
        $args = [
            'oldPwd' => Input::get('oldpwd'),
            'pwd' => Input::get('pwd')
        ];
        $result = $this->requestApi('user.renewpwd',$args);
        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 分享出去的页面
     */
    public function obtaincoupon(){

        $args = Input::all();


        // $user_info = Session::get('wxpay_userinfo');
        // if(!empty($user_info)){
        //     $args['openId'] = $user_info['openId'];
        // }else{
            // if(empty($args['openId'])){
            //     $url = $_SERVER['REQUEST_URI'];
            //     if (empty($url)) {
            //         return $this->error('参数错误');
            //     }

            //     $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
            //     if (!$result || $result['code'] != 0) {
            //         return $this->error('获取微信配置信息失败', $url);
            //     }

            //     $payment = $result['data'];
            //     $config = $payment['config'];

            //     Session::put('authorize_return_url', $url);

            //     $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
            //         '&redirect_uri='.urlencode(u('UserCenter/accesstoken'))
            //         .'&response_type=code&scope=snsapi_userinfo&state=YZ#wechat_redirect';

            //     return Redirect::to($url);
            // }
        // }


        if(!empty($args['activityId'])){
            $data['code'] = 0; //有活动
            $activity = $this->requestApi('activity.getshare',array('orderId'=>$args['orderId'],'activityId'=>$args['activityId']));

            if(empty($activity['data'])){
                $data['code'] = 3; //没有活动
                //没有活动 强行找出这个活动 (需要背景图片)
                $activity = $this->requestApi('activity.get',array('activityId'=>$args['activityId']));
            }else{ //有活动

                //判断活动是否完了
                $activity_count = count($activity['data']['logs']);
                if($activity_count == $activity['data']['sharePromotionNum']){
                    $data['code'] = 2; //活动已经完了
                    //没有活动 强行找出这个活动 (需要背景图片)
                    //$activity = $this->requestApi('Activity.get',array('activityId'=>$args['activityId']));

                }else{
                    if(!empty($this->userId)){
                        //判断用户是否领取
                        //有用户Id 把优惠券给这用户 且生成一条记录
                        $promotion = $this->requestApi('user.promotion.send',array('userId'=>$this->userId,'orderId'=>$args['orderId'],'activityId'=>$args['activityId'],'promotionId'=>$activity['data']['promotion'][0]['promotionId']));

                        $data['code'] = 1; //恭喜你领取了活动
                        //领取了再查一次
                        $activity = $this->requestApi('activity.getshare',array('orderId'=>$args['orderId'],'activityId'=>$args['activityId']));
                        //获取名称
                        $is_have_user = 0;
                        $name = '';
                        foreach($activity['data']['logs'] as $v){
                            if($v['userId'] == $this->userId){
                                $is_have_user = 1;
                                $name = $v['user']['name'];
                                break;
                            }
                        }
                    }else{
                        //没有用户Id
                    }
                }

            }
        }else{
            $data['code'] = 3; //没有活动
        }

        View::share('args',$args);
        View::share('data',$data);
        View::share('name',$name);
        View::share('activity',$activity['data']);
        View::share('is_show_top',false);

        return $this->display('obtain_coupon');
    }

    public function docheckmobile(){
        $args = Input::all();
        $user_info = Session::get('wxpay_userinfo');
        $args['nickname'] = $user_info['nickname'];
        $args['avatar'] = $user_info['headimgurl'];

        $result = $this->requestApi('user.checkuser',$args);

        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url','');
            Session::save();
        }
        return Response::json($result);
    }


    public function accesstoken() {
        $code = $_REQUEST['code'];

        $url = Session::get('authorize_return_url');
        if (empty($code)) {
            return $this->error('授权失败', $url);
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);
        if (!$result) {
            return $this->error('授权失败', $url);
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], $url);
        }

        $openid = $result['openid'];
        $wxurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        Session::put('wxpay_userinfo',$result);
        Session::save();

        $openid = $result['openid'];
        $url = $url.'&openId='.$openid;

        return Redirect::to($url);
    }

    /**
     * 我的余额
     */
    public function balance() {
        $args = Input::all();
        $balance_result = $this->requestApi('user.balance');
        View::share('balance', $balance_result['data']['balance']);
        $result = $this->requestApi('user.getbalance', $args);
        //print_r($result);

        View::share('data', $result['data']);
        View::share('args', $args);
        if (Input::ajax()) {
            return $this->display('balance_item');
        } else {
            return $this->display();
        }
    }

    public function recharge() {
        $payments = $this->getPayments();
        unset($payments['cashOnDelivery']);
        unset($payments['balancePay']);
        View::share('payments', $payments);
        return $this->display();
    }

    /**
     * [wxpay 微信支付]
     */
    public function wxpay(){
        $args = Input::all();
        $url = u('UserCenter/pay',$args);
        $openid = Session::get('wxpay_open_id');
        if(empty($openid)){
        $url = u('Weixin/authorize', ['url' => urlencode($url)]);
        }else{
        $url .= '&openId='.$openid;
        }
        return Redirect::to($url);
    }

    /**
     * 充值
     */
    public function pay() {
        $args = Input::all();
        if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
            Session::put('wxpay_open_id', $args['openId']);
            Session::put('pay_payment', 'weixinJs');
            Session::save();
            return Redirect::to(u('UserCenter/pay',['money' => $args['money']]));
        }

        if (!isset($args['payment'])) {
            $args['payment'] = Session::get('pay_payment');
            $args['openId'] = Session::get('wxpay_open_id');
        }
        $args['extend']['url'] = Request::fullUrl();

        if (!empty($args['openId'])) {
            $args['extend']['openId'] = $args['openId'];
        }

        $pay = $this->requestApi('user.charge', $args);
        if($pay['code'] == 0){
            if (isset($pay['data']['payRequest']['html'])) {
                echo $pay['data']['payRequest']['html'];
                exit;
            }
            View::share('pay',$pay['data']['payRequest']);
        }

        $result = $this->requestApi('user.getbalance');
        View::share('data', $result['data']);

        View::share('payment',$args['payment']);
        return $this->display('wxpay');
    }

}
