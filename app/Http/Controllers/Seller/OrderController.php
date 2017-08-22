<?php

namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Order;
use YiZan\Http\Requests\OrderCreatePostRequest;
use View,
    Input,
    Lang,
    Route,
    Page,
    Form,
    Format,
    Response,
    Time;

/**
 * 订单管理
 */
class OrderController extends AuthController {

    public function index() {
        $post = Input::all();
        !empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
        !empty($post['mobile']) ? $args['mobile'] = strval($post['mobile']) : null;
        !empty($post['beginTime']) ? $args['beginTime'] = Time::toTime(strval($post['beginTime'])) : 0;
        !empty($post['endTime']) ? $args['endTime'] = (Time::toTime(strval($post['endTime'])) + 24 * 60 * 60 - 1 ) : 0;
        $args['status'] = isset($post['status']) ? intval($post['status']) : 0;
        $args['sn'] = trim($post['sn']);
        $args['name'] = trim($post['name']);
        $args['staffName'] = trim($post['staffName']);
        $args['orderType'] = 1;
        $result = $this->requestApi('order.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        View::share('args', $args);
        View::share('excel', http_build_query($args));
        View::share('searchUrl', u('Order/index', ['status' => $post['status'], 'nav' => $post['nav']]));
        return $this->display();
    }

    /**
     * 订单详细
     */
    public function detail() {
        $args['orderId'] = Input::get('orderId');
        if ($args['orderId'] > 0) {
            $result = $this->requestApi('order.detail', $args);
            if ($result['code'] == 0) {
                View::share('data', $result['data']);
                View::share('staff', $result['data']['staffList']);
            }
        }
        
        //获取员工列表
        /* $result = $this->requestApi('sellerstaff.lists', ['pageSize' => 1000]);
          if( $result['code'] == 0 ){
          View::share('staff', $result['data']['list']);
          } */
        return $this->display();
    }

    /**
     * 订单状态更新
     */
    public function status() {
        $args = Input::all();
        $result = $this->requestApi('order.updatestatus', $args);
        return Response::json($result);
    }

    /**
     * 订单修改
     */
    public function refundRemark() {
        $post = Input::all();
        if (!empty($post['id'])) {
            $args['orderId'] = intval($post['id']);
        } else {
            return $this->error(Lang::get('admin.code.23000'));
        }

        if (!empty($post['status'])) {
            $args['status'] = $post['status'];
        } else {
            return $this->error(Lang::get('admin.code.23004'));
        }

        //退款需要添加备注
        if ($post['status'] == ORDER_STATUS_REFUND_AUDITING) {
            if (!empty($post['remark'])) {
                $args['content'] = strval(trim($post['remark']));
            } else {
                return $this->error(Lang::get('admin.code.23001'));
            }
        }
        //拒绝需要添加备注
        if ($post['status'] == ORDER_STATUS_CANCEL_SELLER) {
            if (!empty($post['refuseContent'])) {
                $args['content'] = strval(trim($post['refuseContent']));
            } else {
                return $this->error(Lang::get('admin.code.50201'));
            }
        }

        $result = $this->requestApi('order.updatestatus', $args);

        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Order/detail', ['id' => $args['orderId']]));
    }

    /**
     * 删除订单
     */
    public function destroy() {
        $args = Input::all();
        if (!empty($args['id'])) {
            $result = $this->requestApi('order.delete', $args);
        }
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Order/index'), $result['data']);
    }

    //指派人员
    public function designate() {
        $args = Input::all();
        $result = $this->requestApi('order.designate', $args);
        return Response::json($result);
    }

    /*
     * 随机指派
     */

    public function reassign() {
        $args = Input::all();
        $result = $this->requestApi('order.ranupdate', $args);
        return Response::json($result);
    }

    /**
     * 导出到excel
     */
    public function export() {
        require_once base_path() . '/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('订单列表');

        $sheet->setCellValue('A1', "订单号");
        $sheet->setCellValue('B1', "客户姓名");
        $sheet->setCellValue('C1', "客户电话");
        $sheet->setCellValue('D1', "客户地址");
        $sheet->setCellValue('E1', "订单金额");
        $sheet->setCellValue('F1', "订单状态");

        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        $args = Input::all();
        // $args = [];
        $args['page'] = 1;
        $args['pageSize'] = 999999;
        $result = $this->requestApi('order.lists', $args);
        $i = 2;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A' . $i, "SN:" . $value['sn']);
            $sheet->setCellValue('B' . $i, $value['name']);
            $sheet->setCellValue('C' . $i, $value['mobile'] . ' ');
            $sheet->setCellValue('D' . $i, $value['address']);
            $sheet->setCellValue('E' . $i, $value['totalFee']);
            $sheet->setCellValue('F' . $i, $value['orderStatusStr']);
            $i++;
        }

        $name = iconv("utf-8", "gb2312", "订单列表详细");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        header("Expires: 0");
        $execl = \PHPExcel_IOFactory::createWriter($execl, 'Excel2007');
        $execl->save('php://output');
    }

}
