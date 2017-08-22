<?php

namespace YiZan\Http\Controllers\Admin;

use View,
    Input,
    Form,
    Validator,
    Lang,
    Response;

/**
 * 服务人员管理
 */
class SellerController extends AuthController {
    /*
     * 服务服务站
     * 提交参数
     */

    public function index() {
        $args = Input::all();
        //服务人员列表接口
        $list = $this->requestApi('seller.lists', $args);
        if ($list['code'] == 0) {
            $totalCount = $list['data']['totalCount'];
            View::share('list', $list['data']['list']);
        } else {
            return $this->error($list['msg']);
        }
        return $this->display();
    }

    /*
     * 	创建服务站
     * 	SellerRequest $request
     */

    public function create() {
        $districtIds = $this->requestApi('district.lists');
        if ($districtIds['code'] == 0)
            View::share('districtIds', $districtIds['data']['list']);
        return $this->display('edit');
    }

    /**
     * 	获取服务站
     * 	添加/编辑服务站
     * 	SellerPostRequest $request
     * 	request
     */
    public function edit() {
        $args = Input::all();
        $data = $this->requestApi('seller.get', $args);
        if ($data['code'] == 0)
            View::share('data', $data['data']);

        $districtIds = $this->requestApi('district.lists');
        if ($districtIds['code'] == 0)
            View::share('districtIds', $districtIds['data']['list']);

        return $this->display();
    }

    //SellerPostRequest $request
    public function update() {
        $args = Input::all();

        if ((int) $args['id'] < 1) {
            $url = u('Seller/create');
            $data = $this->requestApi('seller.create', $args);
        } else {
            $url = u('Seller/edit', ['id' => $args['id']]);
            $data = $this->requestApi('seller.update', $args);
        }
        if ($data['code'] == 0) {
            return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98008'), $url);
        } else {
            return $this->error($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98009'));
        }
    }

    /**
     * 删除服务站
     */
    public function destroy() {
        $data = $this->requestApi('seller.delete', Input::all());
        /* 返回处理 */
        $url = u('Seller/index');
        if ($data['code'] > 0) {
            return $this->error($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98006'), $url);
        }
        return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98005'), $url, $data['data']);
    }

    /**
     * 获取员工信息
     */
    public function search() {
        /* 获取会员接口 */
        $list = $this->requestApi('sellerstaff.search', Input::all());
        // print_r($list);exit;
        return Response::json($list['data']);
    }

}
