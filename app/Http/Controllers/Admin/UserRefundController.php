<?php

namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Requests\Admin\UserRefundPostRequest;
use View,
    Input,
    Form,
    Lang;

/**
 * 会员退款
 * */
class UserRefundController extends AuthController {

    /**
     * 会员退款列表
     */
    public function index() {
        $userinfo = Input::all();
        $args = array();
        //查询条件 
        !empty($userinfo['nav']) ? $nav = $userinfo['nav'] : $nav = 1;
        !empty($userinfo['user']) ? $args['user'] = strval($userinfo['user']) : null;
        !empty($userinfo['beginTime']) ? $args['beginTime'] = $userinfo['beginTime'] : null;
        !empty($userinfo['endTime']) ? $args['endTime'] = $userinfo['endTime'] : null;
        !empty($userinfo['status']) ? $args['status'] = intval($userinfo['status']) : null;
        !empty($userinfo['page']) ? $args['page'] = intval($userinfo['page']) : $args['page'] = 1;
        $args['mobile'] = trim($userinfo['mobile']);
        $args['orderSn'] = trim($userinfo['orderSn']);
        $result = $this->requestApi('user.refund.lists', $args);
        if ($result['code'] == 0) {
            foreach ($result['data']['list'] as $k => $v) {
                $result['data']['list'][$k]['statu'] = Lang::get('admin.UserRefund.' . $v['status']);
            }
            View::share('list', $result['data']['list']);
        }
        View::share('data', $result['data']);
        View::share('nav', $nav);
        View::share('url', u('UserRefund/index', ['nav' => $nav, 'status' => $args['status']]));
        return $this->display("index");
    }

    /**
     * 会员退款处理
     */
    public function dispose() {
        $args = Input::all();

        $result = $this->requestApi('user.refund.dispose', $args);

        /* 返回处理 */
        if ($result['code'] == 0 &&
                $result["data"] != null) {
            if (is_array($result["data"]["payRequest"]) == true) {
                if (is_numeric($result["data"]["payRequest"]['code'])) {
                    if ($result["data"]["payRequest"]['code'] == 90000) {
                        return $this->success($result["data"]["msg"], u('UserRefund/index'));
                    } else if ($result["data"]["payRequest"]['code'] == 90001) {
                        return $this->error($result["data"]["msg"], u('UserRefund/index'));
                    }
                } else {
                    die($result["data"]["payRequest"]["html"]);
                }
            } else if (intval($result['data']) < 0) {
                die("
                <html lang=\"zh-CN\">
                <head>
	                <meta charset=\"UTF-8\">
                    <script type=\"text/javascript\">
                      window.alert('退款失败，微信收款帐号与微信退款密钥不符合');
                      window.close();           
                    </script>
                </head>
                </html>");
            } else {
                die("
                <html lang=\"zh-CN\">
                <head>
	                <meta charset=\"UTF-8\">
                    <script type=\"text/javascript\">
                      window.opener.location.reload();
                      window.alert('退款成功，请刷新页面查看退款结果');
                      window.close();           
                    </script>
                </head>
                </html>");
            }
        }
    }

}
