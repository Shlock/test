<?php

namespace YiZan\Http\Controllers\Wap;

use YiZan\Http\Controllers\YiZanViewController;
use View,
    Input,
    Cache,
    Request,
    Time;

abstract class BaseController extends YiZanViewController {

    /**
     * API调用类型
     * @var string
     */
    protected $apiType = 'buyer';

    /**
     * 调用模板
     * @var string
     */
    protected $tpl = 'wap.';

    /**
     * 会员信息
     * @var array
     */
    protected $user;

    /**
     * 会员编号
     * @var int
     */
    protected $userId;

    /**
     *  城市编号
     *  @var int
     */
    protected $cityId = 0;

    /**
     * 商家编号
     *  @var int
     */
    protected $sellerId = 0;

    /**
     * 初始化信息
     */
    public function __construct() {

        parent::__construct();
        $this->tpl .= 'community';
        //设置会员
        $this->setUser(Session::get('user'));

        //设置城市
        $this->setCity((int) Session::get('cityId'));

        //是否显示默认顶部导航条
        $action_val = CONTROLLER_NAME . '.' . ACTION_NAME;
        //登录之后跳转地址
        if (!Input::ajax() && CONTROLLER_NAME != 'User' && $this->userId < 1) {
            Session::put('return_url', u(CONTROLLER_NAME . '/' . ACTION_NAME, Input::all()));
            Session::save();
        }
        //获取购物车消息等数量
        if (!Input::ajax() && $this->userId > 0) {
            $counts = self::requestApi('msg.status');
            if ($counts['code'] == 0) {
                View::share('counts', $counts['data']);
            }
        }
        $urltype = Input::get('urltype');
        if (!empty($urltype)) {
            if ($urltype == 1) {
                $nav_back_url = u('Index/index');
            } elseif ($urltype == 2) {
                $nav_back_url = "javascript:history.back(-1);";
            }
            View::share('nav_back_url', $nav_back_url);
        }


        $top_filter = array('Usercenter.coupon', 'Index.index', 'Usercenter.address');
        $is_show_top = !in_array($action_val, $top_filter);
        if (Input::get('agent') == 'm') {
            $is_show_top = false;
        }
        View::share('is_show_top', $is_show_top);
        View::share('site_config', $this->getConfig());
    }

    /**
     * 系统加载Conifg信息
     * @return [type] [description]
     */
    protected function configInit($data) {
        parent::configInit($data);

        Cache::forever('payments', $data['payments']);

        $citys = [];
        $default_city = null;
        if ($data['citys']) {
            foreach ($data['citys'] as $city) {
                if ($default_city == null) {
                    $default_city = $city;
                }
                $citys[$city['id']] = $city;
            }
        }
        Cache::forever('service_citys', $citys);
        Session::put('default_city', $default_city);
        Session::save();
    }

    /**
     * 系统加载基础Token信息
     * @return [type] [description]
     */
    protected function baseTokenInit($result) {
        parent::baseTokenInit($result);

        $city = $result['data']['city'];
        if ($city) {
            $this->setCity($city['id']);
        }
    }

    /**
     * 设置会员
     * @param array $user 会员信息
     */
    protected function setUser($user) {
        if (!empty($user)) {
            $this->user = $user;
            $this->userId = $user['id'];

            View::share('loginUserId', $this->userId);
            Session::put('user', $user);
            Session::save();
        } // 15123380391
    }

    /**
     * 获取支持的支付方式
     * @return [type] [description]
     */
    protected function getPayments() {
        $type = 'web';
        if (preg_match("/\sMicroMessenger\/\\d/is", Request::header('USER_AGENT'))) {
            $type = 'wxweb';
        }
        $payments = Cache::get('payments');
        return $payments[$type];
    }

    /**
     * 获取开通的城市
     * @return [type] [description]
     */
    protected function getServiceCitys() {
        return Cache::get('service_citys');
    }

    /**
     * 获取预约数据
     * @return [type] [description]
     */
    protected function getReservationData() {
        if (!Session::has('reservation_data')) {
            $reservation_data = [
                'address' => [
                    'address' => '',
                    'mapPoint' => ['x' => 0, 'y' => 0],
                    'mapPointStr' => '',
                ],
                'name' => '',
                'cateid' => 0,
                'tel' => '',
                'date' => '',
                'timelen' => 1,
                'staff' => 0,
                'goods' => 0,
            ];
            Session::put('reservation_data', $reservation_data);
        } else {
            $reservation_data = Session::get('reservation_data');
        }

        //如果没有预约地址，则取会员设置的默认地址
        if (empty($reservation_data['address']) && $this->user) {
            $reservation_data['address'] = $this->user['address'];
        }

        //如果没有预约名称，则取会员设置的名称
        if (empty($reservation_data['name']) && $this->user) {
            $reservation_data['name'] = $this->user['name'];
        }

        //如果没有预约地址，则取会员设置的手机号
        if (empty($reservation_data['tel']) && $this->user) {
            $reservation_data['tel'] = $this->user['mobile'];
        }

        //如果没有预约时间，则取当前的时间
        $min_reservation_time = UTC_HOUR + SERVICE_TIME_SPAN;
        $min_reservation_day = Time::toDayTime($min_reservation_time);
        //如果小于当天接单时间
        if ($min_reservation_time - $min_reservation_day < DEFAULT_BEGIN_ORDER_DATE) {
            $min_reservation_time = $min_reservation_day + DEFAULT_BEGIN_ORDER_DATE;
        } elseif ($min_reservation_time - $min_reservation_day > DEFAULT_END_ORDER_DATE) {
            $min_reservation_time = $min_reservation_day + 86400 + DEFAULT_BEGIN_ORDER_DATE;
        }
        if (!isset($reservation_data['date']) || Time::toTime($reservation_data['date']) < $min_reservation_time) {
            $reservation_data['date'] = Time::toDate($min_reservation_time, 'Y-m-d H:i');
        }
        return $reservation_data;
    }

    /**
     * 保存预约数据
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function saveReservationData($data) {
        Session::put('reservation_data', $data);
    }

    /**
     * 设置城市
     * @param int $city 城市编号
     */
    protected function setCity($cityId) {
        if ($cityId > 0) {
            $service_citys = $this->getServiceCitys();
            if (isset($service_citys[$cityId])) {
                Session::put('default_city', $service_citys[$cityId]);
                $this->cityId = $cityId;
                Session::put('cityId', $cityId);
            }
        } else {
            $default_city = Session::get('default_city');
            if ($default_city) {
                $this->cityId = $default_city['id'];
                Session::put('cityId', $this->cityId);
            }
        }
    }

    /**
     * 调用API
     * @param  string 	$method 接口名称
     * @param  array  	$args   参数
     * @param  array  	$data   提交数据
     * @return array          	API返回数据
     */
    protected function requestApi($method, $args = [], $data = []) {
        $data['userId'] = $this->userId;
        $data['cityId'] = $this->cityId;
        return parent::requestApi($method, $args, $data);
    }

}
