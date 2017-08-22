<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page,Response,Redirect,Cache,Request;
/**
 * 我的
 */
class SellerController extends AuthController {

    public function __construct() {
        parent::__construct();
        View::share('active',"seller");
        if(in_array($this->role,[2,4])){
            return Redirect::to(u('Index/index'))->send();
        }
        View::share('show_top_preloader',true);
    }

    /**
     * 首页信息
     */
    public function index() {
        View::share('title','店铺');
        $result = $this->requestApi('shop.info');
        View::share('seller',$result['data']);
         return $this->display();
    }

    /**
     * 首页信息
     */
    public function goodslists() {

        $args = Input::all();
        View::share('title','商品管理');
        $args['type']  =  $args['type'] ? $args['type'] : 1;
        $result = $this->requestApi('goodscate.lists',$args);
        View::share('goods',$result['data']);
        View::share('status',$args['status']);
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function goods() {
        $args = Input::all();
        if(!$args['status']){
            $args['status'] = 1;
        }

        View::share('nav_back_url',u('Seller/goodslists') );
        $args['page'] = $args['page']?$args['page']:1;
        View::share('title','商品管理');
        $result = $this->requestApi('goods.lists',$args);
        View::share('goods',$result['data']);
        View::share('status',$args['status']);
        View::share('id',$args['id']);
        View::share('page',$args['page']);
        unset($args['page']);
        View::share('args',$args);
        if($args['tpl']){
            return $this->display('service_'.$args['tpl']);
        }
        View::share('ajaxurl_page',"_".$args['status']);
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function seller() {
        $args = Input::all();
        $args['type']  =  $args['type']? $args['type']:2;
        View::share('title','商品管理');
        $result = $this->requestApi('goodscate.lists',$args);
        View::share('goods',$result['data']);
        if( count($result['data']) == 20){
            View::share('show_preloader',true);
        }
        unset($args['page']);
        View::share('args',$args);
        if($args['tpl']){
            return $this->display('seller_'.$args['tpl']);
        }
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function selleritme() {
        $args = Input::all();
        $args['type']  =  $args['type']? $args['type']:2;
        $result = $this->requestApi('goodscate.lists',$args);
        return Response::json($result['data']);
    }

    /**
     * 首页信息
     */
    public function goodsitme() {
        $args = Input::all();
        $args['type']  =  $args['type'] ? $args['type'] : 1;
        $result = $this->requestApi('goodscate.lists',$args);
        return Response::json($result['data']);
    }

    /**
     * 员工详细
     */
    public function info() {
        $args = Input::all();
        View::share('title','店铺信息');
        $result = $this->requestApi('shop.info');
        View::share('nav_back_url',u('Seller/index'));
        View::share('seller',$result['data']);
        if($args['tpl']){
            return $this->display('info_'.$args['tpl']);
        }
        return $this->display();
    }

    /**
     * 货到付款
     */
    public function isDelivery() {
        $result = $this->requestApi('shop.isDelivery',Input::all());
        return Response::json($result);
    }

    /**
     * 营业状态
     */
    public function isStatus() {
        $result = $this->requestApi('shop.isStatus',Input::all());
        return Response::json($result);
    }

    /**
     * 营业状态
     */
    public function isDel() {
        $result = $this->requestApi('goodscate.del',Input::all());
        return Response::json($result);
    }

    /**
     * 添加分类
     */
    public function add() {

        View::share('title','添加分类');
        View::share('type',2);
        View::share('msg','添加服务');
        View::share('nav_back_url',u('Seller/seller'));
        View::share('csss',"#seller_seller_view");
        $result = $this->requestApi('seller.trade');
        View::share('trade',$result['data']);
        return $this->display('edit');
    }

    /**
     * 添加分类
     */
    public function goodsadd() {
        View::share('title','添加分类');
        View::share('type',1);
        View::share('msg','添加商品');
        View::share('nav_back_url',u('Seller/goodslists'));
        $result = $this->requestApi('seller.trade');
        View::share('trade',$result['data']);
        return $this->display('edit');
    }
    /**
     * 编辑分类
     */
    public function goodsedit() {
        $result = $this->requestApi('goodscate.getById',Input::all());
        View::share('data',$result['data']);
        $result = $this->requestApi('seller.trade');
        View::share('trade',$result['data']);
        View::share('title','编辑分类 ');
        View::share('type',Input::get('type'));
        View::share('id',Input::get('id'));
        if(Input::get('type') == 1){
            View::share('nav_back_url',u('Seller/goodslists'));
        }else{
            View::share('nav_back_url',u('Seller/seller'));
        }
        View::share('msg','更新');
        return $this->display('edit');
    }
    /**
     * 编辑分类
     */
    public function saveedit() {
        $data = Input::all();
        $result = $this->requestApi('goodscate.edit',$data);
        return Response::json($result);
    }
    /**
     * 编辑分类
     */
    public function service() {
        View::share('title', '服务列表');
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        if(!$args['status']){
            $args['status'] = 1;
        }
        $result = $this->requestApi('goods.lists',$args);
        View::share('goods',$result['data']);
        View::share('status',$args['status']);
        View::share('id',$args['id']);
        View::share('page',$args['page']);
        unset($args['page']);
        View::share('args',$args);
        View::share('nav_back_url',u('Seller/seller'));
        if($args['tpl']){
            return $this->display('service_'.$args['tpl']);
        }
        View::share('ajaxurl_page',"_".$args['status']);
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function ajaxservice() {
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        if(!$args['status']){
            $args['status'] = 1;
        }
        $result = $this->requestApi('goods.lists',$args);
        return Response::json($result['data']);
    }

    /**
     * 上下架商品
     */
    public  function opgoods(){
        $args = Input::get();
        $args['ids'] = explode(',',$args['goodsId']);
        $result = $this->requestApi('goods.op',$args);
        return Response::json($result);
    }

    /**
     * 编辑分类
     */
    public function preview() {
        $args = Input::all();
        $result = $this->requestApi('goods.detail',['id'=>$args['id']]);
        if($result['code'] == 0){
            if($result['unit'] == 1 && $result['type'] == 2){
                $result['duration'] = $result['duration'] * 60;
            }
            View::share('data',$result);
            View::share('title', $result['seller']['name']);
        }
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function account() {
        $args = Input::all();
        $args['status'] = $args['status']?$args['status']:0;
        $args['type'] = $args['type']?$args['type']:1;
        $args['page'] = $args['page']?$args['page']:1;
        $result = $this->requestApi('shop.account',$args);
        View::share('account',$result['data']);

        $result = $this->requestApi('shop.info');
        View::share('balance',$result['data']['balance']);

        View::share('ajaxurl_page',"_".$args['status']);

        if($args['tpl']){
            return $this->display('account_'.$args['tpl']);
        }
        View::share('acut',$args);
        unset($args['page']);
        View::share('args',$args);
        View::share('title', '我的账单');
        return $this->display();
    }
    /**
     * 编辑分类
     */
    public function ajaxaccount() {
        $args = Input::all();
        $args['type'] = 1;
        $result = $this->requestApi('shop.account',$args);
        return Response::json($result['data']);
    }

    /**
     * 编辑分类
     */
    public function ajaxwithdrawlog() {
        $args = Input::all();
        $args['type'] = 2;
        $args['status'] = 2;
        $args['page'] = $args['page']?$args['page']:2;
        $result = $this->requestApi('shop.account',$args);
        return Response::json($result['data']);
    }

    /**
     * 我要提现
     */
    public function carry() {
        $args = Input::all();
        View::share('title', '提现');
        $result = $this->requestApi('user.bankinfo');
        View::share('bank',$result['data']);
        $result = $this->requestApi('shop.info');
        View::share('money',$result['data']['balance']);
        if($args['tpl']){
            return $this->display('carry_'.$args['tpl']);
        }
        return $this->display();
    }
    /**
     * 提现
     */
    public function withdraw() {
        $args = Input::all();
        $result = $this->requestApi('user.withdraw',$args);
        return Response::json($result);
    }
    /**
     * 提现记录
     */
    public function withdrawlog() {
        $args = Input::all();
        $args['type'] = $args['type']?$args['type']:2;
        $args['status'] = $args['status']?$args['status']:2;
        $result = $this->requestApi('shop.account',$args);
        View::share('acut',$args);
        unset($args['page']);
        View::share('args',$args);
        View::share('account',$result['data']);
        View::share('title', '提现记录');

        if($args['tpl']){
            return $this->display('withdrawlog_'.$args['tpl']);
        }
        if( count($result['data']) == 20){
            View::share('show_preloader',true);
        }
        return $this->display();
    }
    /**
     * 提现记录
     */
    public function analysis()
    {
        $args = Input::all();
        $args['days'] = $args['days'] ? $args['days'] : 1;
        $result = $this->requestApi('order.statistics',$args);
        View::share('args', $args);
        View::share('data', $result['data']);
        View::share('title', '经营分析');
        View::share('ajaxurl_page',"_".$args['days']);
        return $this->display();
    }
    /**
     * 提现记录
     */
    public function recharge()
    {
        View::share('title', '我要充值');
        $payments = $this->getPayments();
//        print_r($payments);exit;
        unset($payments['cashOnDelivery']);
        unset($payments['balancePay']);
        unset($payments['unionpay']);
        unset($payments['unionapp']);
        unset($payments['weixinJs']);
        unset($payments['weixin']);
        unset($payments['weixinSeller']);
        View::share('payments', $payments);
        $nav_back_url = u('Seller/account');
        View::share('nav_back_url', $nav_back_url);
        return $this->display();
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
            return Redirect::to(u('Seller/pay',['money' => $args['money']]));
        }

        if (!isset($args['payment'])) {
            $args['payment'] = Session::get('pay_payment');
            $args['openId'] = Session::get('wxpay_open_id');
        }
        $args['extend']['url'] = Request::fullUrl();
        $args['extend']['url'] = "http://www.niusns.com/payment/o2o.php";

        if (!empty($args['openId'])) {
            $args['extend']['openId'] = $args['openId'];
        }
        $pay = $this->requestApi('seller.recharge', $args);
        if($pay['code'] == 0){
            if (isset($pay['data']['payRequest']['html'])) {
                echo $pay['data']['payRequest']['html'];
                exit;
            }
        }
    }
    public function createpaylog()
    {
        $args = Input::all();
        $pay = $this->requestApi('seller.recharge', $args);
        die(json_encode($pay["data"]));
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
        if($_COOKIE["app"] == "true")
        {
            $type = 'app';
        }

        $payments = Cache::get('payments');
        return $payments[$type];
    }

    /**
     * 添加商品
     */
    public function addnew()
    {
        $args = Input::all();
        if($args['type'] == 2){
            $title = "添加服务";
            $url = u('Seller/service',['id'=>$args['tradeId'],'type'=>$args['type']]);
            $css = "#seller_service_view_1";
        }else{
            $title = "添加商品";
            $args['type'] = 1;
            $url = u('Seller/goods',['id'=>$args['tradeId'],'type'=>$args['type']]);
            $css = "#seller_goods_view_1";
        }
        View::share('css',$css );
        View::share('nav_back_url',$url );
        View::share('title', $title);
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 编辑商品
     */
    public function editnew()
    {
        $args = Input::all();
        if($args['type'] == 2){
            $title = "编辑服务";
            $url = u('Seller/service',['id'=>$args['tradeId'], 'type'=>$args['type']]);
            $css = "#seller_service_view_1";
        }else{
            $title = "编辑商品";
            $args['type'] = 1;
            $url = u('Seller/goods',['id'=>$args['tradeId'], 'type'=>$args['type']]);
            $css = "#seller_goods_view_1";
        }
        View::share('nav_back_url',$url );
        View::share('css',$css );
        $result = $this->requestApi('goods.detail',['id'=>$args['id']]);
        if($result['code'] == 0){
            if($result['unit'] == 1 && $result['type'] == 2){
                $result['duration'] = $result['duration'] * 60;
            }
            $result['brief'] = strip_tags($result['brief']);
            View::share('data',$result);
        }
        View::share('title',$title);
        View::share('type',$args['type']);
        View::share('args', $args);
        return $this->display('addnew');
    }

    /**
     * 添加商品
     */
    public function goodsSave()
    {
        $args = Input::all();
        $args['staffs'] = explode(',',$args['staffId']);
        $arr = [];
        foreach($args['norms'] as $v){
            $arr[] = $v['name'];
        }
        if (count($arr) != count(array_unique($arr))) {
            return Response::json(['code'=>-1,'msg'=>'商品型号不能重复']);
        }
        $result = $this->requestApi('goods.edit',$args);
        return Response::json($result);
    }

    /**
     * 阅读单条消息
     */
    public function msgshow() {
        $args = Input::all();
        if( !is_array($args['id']) ) {
            $args['id'] = (int)$args['id'];
        }
        $this->requestApi('msg.read',$args);
        $list = $this->requestApi('msg.getdata',$args);
        View::share('nav', 'msg');
        View::share('data',$list);
        return $this->display();
    }

    /**
     * 批量阅读消息
     */
    public function readMsg() {
        $args = Input::all();
        if( !is_array($args['id']) ) {
            $args['id'] = (int)$args['id'];
        }
        $result = $this->requestApi('msg.read',$args);
        die(json_encode($result));
    }

    /**
     * 批量删除消息
     */
    public function deleteMsg() {
        $args = Input::all();
        if( !is_array($args['id']) ) {
            $args['id'] = (int)$args['id'];
        }
        $result = $this->requestApi('msg.delete',$args);
        die(json_encode($result));
    }

    /**
     * 意见反馈
     */
    public function feedback() {
        View::share('title', '意见反馈');
        View::share('nav_back_url', u('Mine/index'));
        return $this->display();
    }

    /**
     * 增加意见反馈
     */
    public function addfeedback() {
        $content = strip_tags(Input::get('content'));
        $result = $this->requestApi('feedback.create',['content'=>$content,'deviceType'=>'wap']);
        die(json_encode($result));
    }


    /**
     * [reg 修改密码]
     */
    public function repwd() {
        View::share('staff', $this->staff);
        View::share('title', '修改密码');
        View::share('nav_back_url', u('Mine/index'));
        return $this->display();
    }

    /**
     * [doreg 执行修改密码]
     */
    public function dorepwd() {
        $data = Input::all();
        $data['type'] = 'repwd';
        $result = $this->requestApi('user.repwd',$data);
        if($result['code'] == 0){
            Session::set('staff','');
            $this->setSecurityToken(null);
        }
        die(json_encode($result));
    }
    /**
     * [ 营业时间]
     */
    public function time() {
        $data = Input::all();
        View::share('title', '营业时间');
        $result = $this->requestApi('shop.time',$data);
        View::share('data',$result['data']);
        return $this->display();
    }

    /**
     * [ 修改营业时间]
     */
    public function savetime() {
        $data = Input::all();
        View::share('title', '营业时间');
        $result = $this->requestApi('shop.savetime',$data);
        return Response::json($result);
    }

    /**
     * [评价列表]
     */
    public function evaluation() {
        $data = Input::all();
        $data['type'] = $data['type']?$data['type']:1;
        $data['page'] = $data['page']?$data['page']:1;
        View::share('title', '评价管理');
        $result = $this->requestApi('seller.evalist',$data);
        View::share('evaluation',$result['data']);
        unset($data['page']);
        View::share('args',$data);
        if( count($result['data']['eva']) == 20){
            View::share('show_preloader',true);
        }
        View::share('ajaxurl_page',"_".$data['type']);
        if($data['tpl']){
            return $this->display('rate_'.$data['tpl']);
        }
        return $this->display();
    }
    /**
     * [评价列表]
     */
    public function ajaxevaluation() {
        $data = Input::all();
        $data['type'] = $data['type']?$data['type']:1;
        $data['page'] = $data['page']?$data['page']:1;
        $result = $this->requestApi('seller.evalist',$data);
        return Response::json($result['data']);
    }

    /**
     * [评价回复]
     */
    public function saveevaluation() {
        $data = Input::all();
        $result = $this->requestApi('seller.evareply',$data);
        return Response::json($result);
    }

    /**
     * [店铺简介]
     */
    public function brief() {
        View::share('title', '店铺简介');
        return $this->display();
    }

    /**
     * [店铺公告]
     */
    public function announcement() {
        $result = $this->requestApi('shop.info');
        View::share('data',$result['data']);
        View::share('title', '店铺公告');
        return $this->display();
    }

    /**
     * [保存店铺简介]
     */
    public function savebrief() {
        $brief = Input::get('brief');
        $args['shopdatas']['brief'] =$brief;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }
    /**
     * [保存店铺公告]
     */
    public function savearticle() {
        $article = Input::get('article');
        $args['shopdatas']['article'] = $article;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }

    /**
     * [配送时间]
     */
    public function delivery() {
        View::share('title', '配送时间');
        return $this->display();
    }
    /**
     * [配送时间]
     */
    public function savedelivery() {
        $data = Input::all();
        $args['shopdatas']['deliveryTime'] = $data;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }
    /**
     * [店铺名称]
     */
    public function name() {
        View::share('title', '店铺名称');
        return $this->display();
    }
    /**
     * [联系电话]
     */
    public function tel() {
        View::share('title', '联系电话');
        return $this->display();
    }
    /**
     * [起送价]
     */
    public function serviceFee() {
        View::share('title', '起送价');
        return $this->display();
    }



    /**
     * [保存店铺公告]
     */
    public function savename() {
        $name = Input::get('name');
        $args['shopdatas']['name'] = $name;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }

    /**
     * [保存电话]
     */
    public function savetel() {
        $name = Input::get('tel');
        $args['shopdatas']['tel'] = $name;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result);
    }

    /**
     * [保存电话]
     */
    public function savefee() {
        $name = Input::get('serviceFee');
        $args['shopdatas']['serviceFee'] = $name;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }
    /**
     * [保存配送费]
     */
    public function savedeliveryfee() {
        $name = Input::get('deliveryFee');
        $args['shopdatas']['deliveryFee'] = $name;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }

    /**
     * [保存电话]
     */
    public function deliveryFee() {
        View::share('title', '配送费');
        return $this->display();
    }

    /**
     * [保存电话]
     */
    public function map() {

        $result = $this->requestApi('shop.info');
        View::share('data',$result['data']);
        View::share('title', '服务范围');
        return $this->display();
    }
    /**
     * 选择配送人员
     */
    public function staff() {
        $type = (int)Input::get('type');
        $staffId = explode(',',Input::get('staffId'));
        $title = $type == 1 ? '选择配送人员' : '选择服务人员';
        $result = $this->requestApi('order.stafflist',['type'=>$type]);
        View::share('list', $result['data']);
        View::share('title', $title);
        View::share('staffId', $staffId);
        return $this->display();
    }

    /**
     * 服务范围
     */
    public function sellermap() {
        $result = $this->requestApi('shop.sellermap', Input::all());
        return Response::json($result['data']);
    }
    /**
     * 城市
     */
    public function region() {
        View::share('title', '所在城市');
        $result = $this->requestApi('config.getOpenCitys');
        View::share('region', $result['data']);
        return $this->display();
    }


    /**
     * 城市
     */
    public function sellerbrief() {
        View::share('title', '店铺简介');
        View::share('brief', 'brief');
        return $this->display();
    }

    /**
     * [保存配送费]
     */
    public function savecity() {
        $data = Input::get('data');
        $args['shopdatas'] = $data;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }

    /**
     * [保存logo]
     */
    public function savelogo() {
        $data = Input::all();
        $args['shopdatas'] = $data;
        $result = $this->requestApi('shop.edit',$args);
        return Response::json($result['data']);
    }
}
