<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Report;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB;
/**
 * 报表
 */
class ReportController extends AuthController {

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
        $args['page'] = isset($post['page']) ? intval($post['page']) : 0;

        $result = $this->requestApi('statistics.revenue',$args);
//        if($result['data']['code'] > 0){
//            return $this->error($result['data']['msg']);
//        }

        if(!empty($args['endDate']) && !empty($args['beginDate'])){
            $endDate = Time::toTime($args['endDate']);
            $beginDate = Time::toTime($args['beginDate']);
            $args['rs'] = $endDate-$beginDate;
        }else{
            $args['rs'] = 1;
        }

        View::share('data',$result['data']);
        View::share('list', $result['data']['list']);
        View::share('args',$args);
		View::share('date',Time::toDate(UTC_TIME,'m-d'));
		return $this->display();
	} 
}
