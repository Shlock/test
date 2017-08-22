<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 服务
 */
class GoodsReportController extends AuthController {
	/**
	 * 服务管理-服务列表
	 */
	public function index() {
        $post = Input::all();
        !empty($post['beginDate']) ? $args['beginDate'] = strval($post['beginDate']) : null;
        !empty($post['endDate']) ? $args['endDate'] = strval($post['endDate']) : null;
        if(@$args['beginDate'] && @$args['endDate']){
            $type = null;
        }else{
            $type = 0;
        }
        $args['type'] = isset($post['type']) ? intval($post['type']) : $type;
        $args['numOrder'] = isset($post['numOrder']) ? intval($post['numOrder']) : 0;
        $args['priceOrder'] = isset($post['priceOrder']) ? intval($post['priceOrder']) : 0;
        $args['page'] = isset($post['page']) ? intval($post['page']) : 1;
        $result = $this->requestApi('statistics.goodsreport',$args);
//        print_r($result);
        if($result['code']==0){
            View::share('data',$result['data']);
        }
        // dd($result);
        View::share('args',$args);
        return $this->display();
	}

}
