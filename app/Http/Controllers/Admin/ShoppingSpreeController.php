<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time, Redirect;
/**
 * 抢购管理
 */
class ShoppingSpreeController extends AuthController{
    public function index(){
        $activityId = Input::get('id');
        $data = ['id'=>$activityId,'type'=>1];
        View::share('param',$data);
        //获取活动配置信息
        $result = $this->requestApi('shoppingspree.get',$data);
        if(empty($result['data'])){
            return Redirect::to('ShoppingSpree/setting');
        }
        
        if($result['code'] == 0){
            View::share('data',$result['data']);
        }
        
        $args = Input::all();
        if($activityId > 0){
            $result = $this->requestApi('system.goods.lists', $args);
            
            foreach ($result['data']['list'] as $key=>$val) {
                if(empty($val['activityGoods']) || $val['activityGoods']['activityId'] != $activityId){
                    unset($result['data']['list'][$key]);
                }
            }
            if ($result['code'] == 0)
                View::share('list', $result['data']['list']);
        }
        
        //获取一级分类
        $cate = $this->requestApi('goods.cate.lists',['status'=>1]);
        $catePid[] = ['id'=>null,'name'=>'全部'];
        foreach ($cate['data'] as $key => $value) {
            $catePid[] = $value;
        }
        
        //获取二级分类
        if($args['catePid'] > 0){
            $args2['pid'] = $args['catePid'];
            $args2['status'] = 1;
            $cate = $this->requestApi('goods.cate.selectSecond',$args2);
            $cateId[] = ['id'=>null,'name'=>'全部'];
            foreach ($cate['data'] as $key => $value) {
                $cateId[] = $value;
            }
            View::share("cateId", $cateId);
        }
        
        View::share('cate', $cate);
        View::share("catePid", $catePid);
        View::share('excel',http_build_query($args));
        return $this->display();
    }
    /**
     * 抢购活动设置
     */
    public function setting(){
        $args = Input::all();
        $args['type'] = 1;
        $result = $this->requestApi('shoppingspree.get',$args);
        if($result['code'] == 0){
            View::share('data',$result['data']);
        }
        return $this->display();
    }
    
    public function save(){
        $args = Input::all();
        !empty($args['startTime']) ? $args['startTime'] = Time::toTime($args['startTime']) : null;
        !empty($args['endTime']) ? $args['endTime'] = Time::toTime($args['endTime']) : null;
        if($args['id']){
            $result = $this->requestApi('shoppingspree.update',$args);
        }else{
            $result = $this->requestApi('shoppingspree.create',$args);
        }
        if($result['code'] > 0){
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('ShoppingSpree/setting',['id'=>$result['data']['id']]), $result['data']);
    }
    /**
     * 设置抢购活动的状态
     */
    public function setStaus(){
        $args = Input::all();
        $args['status'] = ($args['type'] == 'open') ? 1 : 0;
        $result = $this->requestApi('shoppingspree.setStatus',$args);
        exit(json_encode($result));
    }
    /**
     * 设置活动的抢购价格
     */
    public function setPrice(){
        $args = Input::all();
        $result = $this->requestApi('shoppingspree.setPrice',$args);
        exit(json_encode($result));
    }
    
    /**
     * 查看活动详情
     */
    public function detail(){
        $args = Input::all();
        
        //获取一级分类
        $cate = $this->requestApi('goods.cate.lists',['status'=>1]);
        $goodsCate[] = ['id'=>0,'name'=>'选择一级分类'];
        foreach ($cate['data'] as $key => $value) {
            $goodsCate[] = $value;
        }
        
        //获取适用肤质
        $fitSkin = $this->requestApi('fitskin.lists',['status'=>1]);
        
        //获取服务承诺
        $promise = $this->requestApi('promise.lists',['status'=>1]);
        
        //获取技师等级
        $staffLevel = $this->requestApi('seller.creditrank.lists');
        
        //获取服务
        if ( !empty($args['id']) ) {
            $args['id'] = $args['id'];
            $result = $this->requestApi('system.goods.get',$args);
            View::share('data', $result['data']);
        }
        
        View::share("goodsCate", $goodsCate);
        View::share("fitSkin", $fitSkin['data']);
        View::share("promise", $promise['data']);
        View::share('staffLevel', $staffLevel['data']);
        return $this->display();
    }
    
    /**
     * 添加服务项目
     */
    public function addService(){
        $args = Input::all();
        $result = $this->requestApi('system.goods.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        
        //获取一级分类
        $cate = $this->requestApi('goods.cate.lists',['status'=>1]);
        $catePid[] = ['id'=>null,'name'=>'全部'];
        foreach ($cate['data'] as $key => $value) {
            $catePid[] = $value;
        }
        
        //获取二级分类
        if($args['catePid'] > 0){
            $args2['pid'] = $args['catePid'];
            $args2['status'] = 1;
            $cate = $this->requestApi('goods.cate.selectSecond',$args2);
            $cateId[] = ['id'=>null,'name'=>'全部'];
            foreach ($cate['data'] as $key => $value) {
                $cateId[] = $value;
            }
            View::share("cateId", $cateId);
        }
        View::share("catePid", $catePid);
        View::share('param',$args);
        return $this->display();
    }
    
    public function doAddService(){
        $args = Input::all();
        $result = $this->requestApi('shoppingspree.setservice',$args);
        exit(json_encode($result));
    }
}  