<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Activity;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 注册活动
 */
class ActivityRegController extends AuthController{

    public function index(){
        $args = Input::all();
        $args['startTime'] = !empty($args['startTime']) ? Time::toTime($args['startTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
        $args['type'] = 1;
        $result = $this->requestApi('Activity.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        return $this->display();
    }


    public function create(){

        //获取所有的优惠券
        $promotion = $this->requestApi('Promotion.getPromotionLists');
        $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
        foreach ($promotion['data'] as $key => $value) {
            $promotionList[] = $value;
        }
        View::share("promotionList", $promotionList);
        return $this->display('edit');

    }

    public function edit(){
        $args = Input::get();
        $args['type'] = 1;
        $result = $this->requestApi('Activity.activity', $args);

        //获取所有的优惠券
        $promotion = $this->requestApi('Promotion.getPromotionLists');
        $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
        foreach ($promotion['data'] as $key => $value) {
            $promotionList[] = $value;
        }
        View::share("promotionList", $promotionList);
        View::share("data", $result['data']);

        return $this->display();

    }

    /**
     * 添加或编辑注册活动
     */
    public function save(){
        $args = Input::all();

        $args['type'] = 2;
        $result = $this->requestApi('Activity.save',$args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success( Lang::get('admin.code.98008'));
    }

    /**
     * 修改状态
     */
    public function updateStatus() {
        $args = Input::all();
        $args['ref_module'] = 'Activity';
        $result = $this->requestApi('common.updateStatus',$args);
        return Response::json($result);
    }

   

}  