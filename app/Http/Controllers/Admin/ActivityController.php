<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Activity;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 营销管理
 */
class ActivityController extends AuthController{
    /**
     * 首页
     */
    public function index(){
        $post = Input::all();

        $args['name'] = !empty($post['name']) ? strval($post['name']) : null;
        $args['status'] = !empty($post['status']) ? strval($post['status']) : 0;
        $args['startTime'] = !empty($post['startTime']) ? strval($post['startTime']) : null;
        $args['endTime'] = !empty($post['endTime']) ? strval($post['endTime']) : null;
        $args['type'] = !empty($post['type']) ? strval($post['type']) : 0;

        $result = $this->requestApi('Activity.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        return $this->display();
    }

    /**
     * 创建活动
     */
    public function create(){
        return $this->display();
    }

    /**
     * 添加活动
     */
    public function add(){
        $args = Input::all();
        if(empty($args['type'])){
            return $this->error('请选择添加类型',u('Activity/create'));
        }
        if($args['type'] == 1){
            return $this->display('share_activity');
        }elseif($args['type'] ==2){

            return $this->display('register_activity');
        }else{
            return $this->display();
        }
    }

    /**
     * 编辑活动
     */
    public function edit(){
        $args = Input::all();
        $result = $this->requestApi('Activity.get', $args);

        //print_r($result);
        if($result['data']['type'] == 1){
            //获取所有的优惠券
            $promotion = $this->requestApi('Promotion.lists');
            $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
            foreach ($promotion['data']['list'] as $key => $value) {
                $promotionList[] = $value;
            }
            View::share("promotionList", $promotionList);

            View::share("data", $result['data']);

            return $this->display('share_activity');
        }elseif($result['data']['type'] ==2){

            //获取所有的优惠券
            $promotion = $this->requestApi('Promotion.lists');
            $promotioncount = count($promotion['data']['list']);
            $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
            foreach ($promotion['data']['list'] as $key => $value) {
                $promotionList[] = $value;
            }
//             print_r($result['data']);
            View::share("promotionList", $promotionList);
            View::share("promotioncount", $promotioncount);
            View::share("data", $result['data']);

            return $this->display('register_activity');
        }else{
            return $this->display();
        }
    }

    /**
     * 添加或编辑分享活动
     */
    public function save_register_activity(){
        $args = Input::all();

        $args['type'] = 2;
        $result = $this->requestApi('Activity.registerUpdate',$args); //更新和创建

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success( Lang::get('admin.code.98008'), u('Activity/index'), $result['data'] );
    }

    /**
     * 获取优惠券
     */
    public function getpromotion(){
        $args = Input::all();

        $promotion = $this->requestApi('Promotion.lists',$args);
        echo json_encode($promotion);
    }

    /**
     * 分享活动
     */
    public function share_activity(){
        //查看是否有分享活动
        $args['type'] = 1;
        $result = $this->requestApi('Activity.activity', $args);

        //获取所有的优惠券
        $promotion = $this->requestApi('Promotion.getPromotionLists');
        $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
        foreach ($promotion['data'] as $key => $value) {
            $promotionList[] = $value;
        }
        View::share("promotionList", $promotionList);

        if(!empty($result['data'])){
            //获取活动的优惠券
            $promotion2 = $this->requestApi('Activity.getPromotionLists');
            View::share("promotionList2", $promotion2['data']);
        }


        View::share("data", $result['data']);

        return $this->display();

    }

    /**
     * 添加或编辑分享活动
     */
    public function save_share_activity(){
        $args = Input::all();

        $args['type'] = 1;
        $result = $this->requestApi('Activity.shareUpdate',$args); //更新

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success( Lang::get('admin.code.98008'), u('Activity/index'), $result['data'] );
    }

    /**
     * [destroy]
     */
    public function destroy(){
        $args['id'] = (int)Input::get('id');
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('Activity.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Activity/index'), $result['data']);
    }

}  