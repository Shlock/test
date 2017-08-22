<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\AdvService;
use YiZan\Services\SellerService;
use YiZan\Services\RegionService;
use YiZan\Services\PaymentService;
use YiZan\Services\SystemConfigService;
use YiZan\Services\SellerCateService;
use YiZan\Models\SellerCate;
use YiZan\Utils\Http;
use Input;
/**
 * 配置
 */
class ConfigController extends BaseController {
    /**
     * 首页轮播广告
     */
    public function banners() {
        $data = AdvService::getAdvByCode('BUYER_INDEX_BANNER', (int)$this->request('cityId'));
        foreach($data as $key => $value)
        {
            if($value['type'] == 4)
            {
                $data[$key]["arg"] = u('Wap#Article/detailapp',array('articleId'=>$value['arg']));
            }
        }

        $this->outputData($data);
    }

    public function test() {
        $data = [
            'mid' => '49a3052a-91df-4c2a-a474-0a80f9ecffd7',
            'sign' => '235645',
            'money' => 100,
            'type' => 'wxpay'
        ];
        $rs = Http::get("http://www.lamic.cn/port/pay/portPayMoney", $data);
        var_dump($rs);
    }

    /**
     * 首页分类
     */
    public function categorys() {
        $data = AdvService::getAdvByCode('BUYER_INDEX_MENU', (int)$this->request('cityId'));

        foreach($data as $key => $value)
        {
            if($value['type'] == 4)
            {
                $data[$key]["arg"] = u('Wap#Article/detailapp',array('articleId'=>$value['arg']));
            }
        }

        $this->outputData($data);
    }

    public function index()
    {
        $is_show_top = true;//判断是否手机
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        if($iphone || $ipad || $android) {
            $is_show_top = false;
        }
        $banner = AdvService::getAdvByCode('BUYER_INDEX_BANNER', (int)$this->request('cityId'));

        foreach($banner as $key => $value)
        {
            if($value['type'] == 1) {
                $sellerCate = SellerCate::find($value['arg']);
                if($sellerCate){
                    $banner[$key]['name'] = $sellerCate->name;
                }
            }
            if($value['type'] == 7) //文章
            {
                $banner[$key]['type'] = '5';
                if ($is_show_top) {
                    $banner[$key]["arg"] = u('Wap#Article/detail',array('id'=>$value['arg']));
                } else {
                    $banner[$key]["arg"] = u('Wap#Article/detailapp',array('id'=>$value['arg']));
                }
            }
        }

        $notice = AdvService::getAdvByCode('BUYER_INDEX_MENU', (int)$this->request('cityId'));

        foreach($notice as $key => $value)
        {
            if($value['type'] == 7) //文章
            {
                $notice[$key]['type'] = '5';
                if ($is_show_top) {
                    $notice[$key]["arg"] = u('Wap#Article/detail',array('id'=>$value['arg']));
                } else {
                    $notice[$key]["arg"] = u('Wap#Article/detailapp',array('id'=>$value['arg']));
                }
            }
        }

        $menu = SellerCateService::getCatesAll((int)$this->request('id'), (int)$this->request('type'));

        $special = [
            [
                'id'=>0,
                'pid'=>0,
                'name'=>'物业',
                'status'=>1,
                'type'=>0,
                'logo'=>asset('wap/community/client/images/special_logo.png'),
                'image'=>asset('wap/community/client/images/special_image.png'),
            ]
        ];

        foreach($menu as $key => $value)
        {
            $menu[$key]['type']     = 1;
            $menu[$key]['arg']      = strval($value['id']);
            $menu[$key]['image']    = $value['logo'];
        }

        //$menu = array_merge($special, $menu);
        $key = 0;
        if(count($menu) > 8){
            for ($i=0; $i < 7; $i++) {
                $key++;
                $menus[$i]['id']       = $menu[$i]['id'];
                $menus[$i]['pid']      = 0;
                $menus[$i]['name']     = $menu[$i]['name'];
                if($menu[$i]['id'] == 0){
                    $menus[$i]['type']     = 0;
                } else {
                    $menus[$i]['type']     = 1;
                }
                $menus[$i]['arg']      = strval($menu[$i]['id']);
                $menus[$i]['image']    = $menu[$i]['logo'];
            }
        } else {
            $menus = $menu;
        }

		$menus[] = [
            'type' => -1,
            'arg' => '0',
            'image' => asset('wap/community/client/images/s9.png'),
            'name' => "全部",
        ];
        // print_r(["banner"=>$banner, "notice"=>$notice, "menu"=>$menus, "sellers"=>$sellers]);
        // exit;
                
        $sellers = [];//SellerService::getRecommendSellers($this->request('mapPoint'));
        $this->outputData(["banner"=>$banner, "notice"=>$notice, "menu"=>$menus, "sellers"=>$sellers]);
    }

    public function seller(){
        $is_show_top = true;//判断是否手机
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        if($iphone || $ipad || $android) {
            $is_show_top = false;
        }

        $notice = AdvService::getAdvByCode('BUYER_JNIWCZKA', (int)$this->request('cityId'));

        foreach($notice as $key => $value)
        {
            if($value['type'] == 7) //文章
            {
                $notice[$key]['type'] = '5';
                if ($is_show_top) {
                    $notice[$key]["arg"] = u('Wap#Article/detail',array('id'=>$value['arg']));
                } else {
                    $notice[$key]["arg"] = u('Wap#Article/detailapp',array('id'=>$value['arg']));
                }
            }
        }
        $this->outputData($notice);
    }

    public function token() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'city'  => RegionService::getOpenCityByIp(CLIENT_IP)
            ]
        ];
        $this->output($result);
    }

    /**
     * Wap初始化
     */
    public function init() {
        $this->createToken();

        $citys  = RegionService::getServiceCitys();
        $userAgent = $this->request('userAgent');

        $wapType = 'web';
        if (preg_match("/\sMicroMessenger\/\\d/is", $userAgent)) {
            $wapType = 'wxweb';
        }

        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'citys'     => $citys,
                'city'      => RegionService::getOpenCityByIp(CLIENT_IP),
                'payments'  => PaymentService::getPaymentTypes(),
                'configs'   => SystemConfigService::getConfigs()
            ]
        ];

        $this->output($result);
    }

    /**
     * 得到配置
     */
    public function configByCode()
    {
        $result = SystemConfigService::getConfigByCode($this->request('code'));

        $this->outputData($result);
    }

    /**
     * 获取支付配置
     */
    public function getpayment() {
        $payment = PaymentService::getPayment($this->request('code'));
        $this->outputData($payment);
    }
}