<?php

namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use View,
    Input,
    Lang,
    Route,
    Page,
    Validator,
    Session,
    DB,
    Response,
    Redirect;

/**
 * 员工
 */
class StaffController extends AuthController {

    /**
     * 员工管理-员工列表
     */
    public function index() {
        $args = Input::all();
        //获取服务站信息
        $seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        //获取员工列表
        $result = $this->requestApi('sellerstaff.lists', $args);
        //print_r($result);
        if ($result['code'] == 0) {
            View::share('list', $result['data']['list']);
        }
        return $this->display();
    }

    /**
     * [create 添加员工]
     */
    public function create() {
        $args = Input::all();
        $seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);
        // var_dump($seller);
        if ($seller['code'] == 0) {
            View::share('seller', $seller['data']);
        }

        return $this->display('edit');
    }

    /**
     * [edit 编辑员工]
     */
    public function edit() {
        $args = Input::all();

        if ($args['id'] < 1)
            Redirect::to(u('Staff/index'))->send();
        $seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);
        // var_dump($seller);
        if ($seller['code'] == 0) {
            View::share('seller', $seller['data']);
        }
        $result = $this->requestApi('sellerstaff.get', $args);
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
        }
        return $this->display();
    }

    /**
     * [save 添加/编辑员工操作]
     */
    public function save() {
        $args = Input::all();
        // var_dump($args);
        // exit;
        $result = $this->requestApi('sellerstaff.update', $args);

        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Staff/index', ['sellerId' => $args['sellerId']]), $result['data']);
    }

    /**
     * [destroy 删除员工]
     */
    public function destroy() {
        $args['id'] = (int) Input::get('id');
        if ($args['id'] > 0) {
            $result = $this->requestApi('sellerstaff.delete', $args);
        }
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Staff/index'), $result['data']);
    }

    public function updateStatus() {
        $post = Input::all();
        $args = [
            $post['field'] => $post['val'],
            'id' => $post['id']
        ];
        $result = $this->requestApi('Sellerstaff.updateStatus', $args);
        return Response::json($result);
    }

    /*
     * 更新预约时间
     */

    public function updatatime() {

        $args = Input::all();
        $data = $this->requestApi('Staffstime.update', $args);
        if ($data['code'] > 0) {
            return $this->error($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98006'), url('Staff/index'));
        }
        return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98005'), url('Staff/index'), $data['data']);
    }

    /*
     * 添加预约时间
     */

    public function adddatatime() {
        $args = Input::all();
        $data = $this->requestApi('Staffstime.add', $args);
//        var_dump($args);
        if ($data['code'] > 0) {
            return $this->error($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98006'), url('Staff/index'));
        }
        return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98005'), url('Staff/index'), $data['data']);
    }

    /*
     * 获取
     */

    public function schedule() {
        $args = Input::all();
        $hours = $this->requestApi('schedule.staffLists', $args);
        return Response::json($hours);
    }

    /*
     * 计算星座 string get_zodiac_sign() 
     * 输入：月份，日期 
     * 输出：星座名称或者错误信息 
     */

    function get_zodiac_sign() {
        $xz = explode("-", Input::get('time'));
        $month = $xz[1];
        $day = $xz[2];
        // 检查参数有效性 
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
            return (false);
        // 星座名称以及开始日期 
        $signs = array(
            array("20" => "水瓶座"),
            array("19" => "双鱼座"),
            array("21" => "白羊座"),
            array("20" => "金牛座"),
            array("21" => "双子座"),
            array("22" => "巨蟹座"),
            array("23" => "狮子座"),
            array("23" => "处女座"),
            array("23" => "天秤座"),
            array("24" => "天蝎座"),
            array("22" => "射手座"),
            array("22" => "摩羯座")
        );
        list($sign_start, $sign_name) = each($signs[(int) $month - 1]);
        if ($day < $sign_start)
            list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
        return $sign_name;
    }

    /**
     * 搜索员工
     */
    public function search() {
        /* 获取员工接口 */
        $list = $this->requestApi('Sellerstaff.searchs', Input::all());
        return Response::json($list['data']);
    }

    public function showtime() {
        $staffstime = $this->requestApi('Staffstime.edit', Input::all());
        return Response::json($staffstime);
    }

    /**
     * [destroy 删除时间]
     */
    public function deldatatime() {
        $args = Input::all();
        $result = $this->requestApi('Staffstime.delete', $args);
        return Response::json($result);
    }

    public function gettimes() {
        $args['staffId'] = (int) Input::get('id');
        $staffstime = $this->requestApi('Staffstime.lists', $args);
        return Response::json($staffstime);
    }

}
