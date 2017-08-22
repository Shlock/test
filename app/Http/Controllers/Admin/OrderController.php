<?php

namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Order;
use YiZan\Utils\Time;
use YiZan\Http\Requests\Admin\OrderCreatePostRequest;
use View,
    Input,
    Lang,
    Route,
    Page,
    Form,
    Format,
    Response,
    Cache;

/**
 * 订单管理
 */
class OrderController extends AuthController {

    /**
     * 订单列表
     */
    public function index() {
        $post = Input::all();
        $args = [
            'orderType' => 1,
            'sn' => trim($post['sn']),
            'mobile' => trim($post['mobile']),
            'beginTime' => trim($post['beginTime']) != '' ? Time::toTime($post['beginTime']) : '',
            'endTime' => trim($post['endTime']) != '' ? Time::toTime($post['endTime']) : '',
            'payStatus' => $post['payStatus'] != '-1' ? $post['payStatus'] : '',
            'status' => (int) $post['status'] > 0 ? $post['status'] : 0,
            'sellerName' => trim($post['sellerName']),
            'page' => (int) $post['page']
        ];

        $result = $this->requestApi('order.lists', $args);
        if ($result['code'] == 0) {
            View::share('list', $result['data']['list']);
        }
        View::share('nav', $post['nav']);
        View::share('excel', http_build_query($args));
        View::share('searchUrl', u('Order/index', ['status' => $post['status'], 'nav' => $post['nav']]));
        return $this->display();
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
     * 订单修改
     */
    public function refundRemark() {
        $post = Input::all();
        if (!empty($post['id'])) {
            $args['id'] = intval($post['id']);
        } else {
            return $this->error(Lang::get('admin.code.23000'));
        }

        if (!empty($post['status'])) {
            $args['status'] = $post['status'];
        } else {
            return $this->error(Lang::get('admin.code.23004'));
        }

        //退款需要添加备注
        if ($post['status'] == ORDER_STATUS_REFUND_HANDLE) {
            if (!empty($post['remark'])) {
                $args['content'] = strval(trim($post['remark']));
            } else {
                return $this->error(Lang::get('admin.code.23001'));
            }
        }
        //拒绝需要添加备注
        if ($post['status'] == ORDER_ADMIN_REFUSE_SERVICE) {
            if (!empty($post['refuseContent'])) {
                $args['refuseContent'] = strval(trim($post['refuseContent']));
            } else {
                return $this->error(Lang::get('admin.code.50201'));
            }
        }
        $result = $this->requestApi('order.update', $args);

        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Order/detail', ['id' => $args['id']]));
    }

    /**
     * 订单详细
     */
    public function detail() {
        $args = Input::all();
        $result = $this->requestApi('order.get', $args);
        View::share('data', $result['data']);
        return $this->display();
    }

    /**
     * 删除订单
     */
    public function destroy() {
        $args = Input::all();
        if (!empty($args['id'])) {
            $result = $this->requestApi('order.delete', $args);
        }
        if ($result['code'] == 0) {
            return $this->success($result['msg']);
        } else {
            return $this->error($result['msg']);
        }
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
        $sheet->setCellValue('B1', "服务名称");
        $sheet->setCellValue('C1', "服务人员");
        $sheet->setCellValue('D1', "服务人员电话");
        $sheet->setCellValue('E1', "会员名称");
        $sheet->setCellValue('F1', "会员联系电话");
        $sheet->setCellValue('G1', "订单金额");
        $sheet->setCellValue('H1', "优惠金额");
        $sheet->setCellValue('I1', "支付金额");
        $sheet->setCellValue('J1', "支付状态");
        $sheet->setCellValue('K1', "订单状态");
        $sheet->setCellValue('L1', "下单时间");

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(18);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = [];
        $result = $this->requestApi('order.lists', $args);

        $i = 2;
        $payStatus = [0 => '等待支付', 1 => '已支付'];
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A' . $i, "SN:" . $value['sn']);
            $sheet->setCellValue('B' . $i, $value['goods']['name']);
            $sheet->setCellValue('C' . $i, $value['seller']['name']);
            $sheet->setCellValue('D' . $i, $value['seller']['mobile']);
            $sheet->setCellValue('E' . $i, $value['user']['name']);
            $sheet->setCellValue('F' . $i, $value['user']['mobile']);
            $sheet->setCellValue('G' . $i, $value['totalFee']);
            $sheet->setCellValue('H' . $i, $value['discountFee']);
            $sheet->setCellValue('I' . $i, $value['payFee']);
            $sheet->setCellValue('J' . $i, $payStatus[$value['payStatus']]);
            $sheet->setCellValue('K' . $i, $value['orderStatusStr']);
            $sheet->setCellValue('L' . $i, yztime($value['createTime']));
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
