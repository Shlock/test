<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Menu;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 营销管理
 */
class MenuController extends AuthController{
    /**
     * 首页
     */
    public function index(){
        $post = Input::all();

        !empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;

        $result = $this->requestApi('Menu.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        return $this->display();
    }

    /**
     * 创建活动
     */
    public function create(){
        $result = $this->requestApi('city.lists');
        $cate2 = [
            "id" => 0,
            "pid" => 0,
            "name" => "选择城市",
            "sort" => 100,
            "status" => 1,
            "level" => 0,
            "levelname" => "选择城市"
        ];
        array_unshift($result['data'],$cate2);
        View::share('city', $result['data']);

        //商家分类
        $list = $this->requestApi('seller.cate.catesall');
        if($list['code'] == 0) {
            $sellerCate[] = [
                'id' => 0,
                'name' => '请选择',
                'childs' => [],
            ];
            foreach ($list['data'] as $key => $value) {
                $sellerCate[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'childs' => $value['childs'],
                ];
            }
            View::share('sellerCate', $sellerCate);
        }
        //商品
        $list = $this->requestApi('system.goods.lists');
        if($list['code'] == 0) {
            $goods[] = [
                'id' => 0,
                'name' => '请选择'
            ];
            foreach ($list['data']['list'] as $key => $value) {
                $goods[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                ];
            }
            View::share('service', $goods);
        }
        //文章
        $list = $this->requestApi('article.lists');
        if($list['code'] == 0) {
            View::share('article', $list['data']['list']);
        }
        return $this->display();
    }

    /**
     * 添加活动
     */
    public function save(){
        $args = Input::all();

        $result = $this->requestApi('Menu.update',$args); //更新
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success( Lang::get('admin.code.98008'), u('Menu/index'), $result['data'] );
    }

    /**
     * 编辑活动
     */
    public function edit(){
        $result = $this->requestApi('city.lists');
        $cate2 = [
            "id" => 0,
            "pid" => 0,
            "name" => "选择城市",
            "sort" => 100,
            "status" => 1,
            "level" => 0,
            "levelname" => "选择城市"
        ];
        array_unshift($result['data'],$cate2);
        View::share('city', $result['data']);

        //商家分类
        $list = $this->requestApi('seller.cate.catesall');
        if($list['code'] == 0) {
            $sellerCate[] = [
                'id' => 0,
                'name' => '请选择',
                'childs' => [],
            ];
            foreach ($list['data'] as $key => $value) {
                $sellerCate[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'childs' => $value['childs'],
                ];
            }
            View::share('sellerCate', $sellerCate);
        }
        //商品
        $list = $this->requestApi('system.goods.lists');
        if($list['code'] == 0) {
            $goods[] = [
                'id' => 0,
                'name' => '请选择'
            ];
            foreach ($list['data']['list'] as $key => $value) {
                $goods[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                ];
            }
            View::share('service', $goods);
        }
        //文章
        $list = $this->requestApi('article.lists');
        if($list['code'] == 0) {
            View::share('article', $list['data']['list']);
        }

        $args = Input::all();
        $result = $this->requestApi('Menu.get', $args);
        View::share('data', $result['data']);

        return $this->display('create');
    }


    /**
     * [destroy]
     */
    public function destroy(){
        $args['id'] = (int)Input::get('id');
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('Menu.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Menu/index'), $result['data']);
    }

    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('Menu.updateStatus',$args);
        $result = array (
            'status'    => true,
            'data'      => Input::input('val'),
            'msg'       => null
        );
        return Response::json($result);
    }

}  