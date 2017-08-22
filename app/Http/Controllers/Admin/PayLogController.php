<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View;

/**
 * 支付日志
 */
class PayLogController extends AuthController {  
	/**
	 * 日志列表
	 */
	public function index() {
        $args = Input::all();
		$result = $this->requestApi('user.paylog.lists',$args);  
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
        //获取支付方式
        $payment_result = $this->requestApi('payment.lists');

        View::share('payments', $payment_result['data']);
        return $this->display();
	}

    public function export(){
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('会员支付列表');

        $sheet->setCellValue('A1', "昵称");
        $sheet->setCellValue('B1', "手机");
        $sheet->setCellValue('C1', "订单SN");
        $sheet->setCellValue('D1', "服务费用");
        $sheet->setCellValue('E1', "流水号");
        $sheet->setCellValue('F1', "支付方式");
        $sheet->setCellValue('G1', "创建时间");
        $sheet->setCellValue('H1', "支付状态");
        $sheet->setCellValue('I1', "支付时间");

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = [];
        $args['isTotal'] = 1;
        $result = $this->requestApi('user.paylog.lists', $args);

        $i = 2;

        foreach ($result['data'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['user']['name']);
            $sheet->setCellValue('B'.$i, $value['user']['mobile']);
            $sheet->setCellValue('C'.$i, "'". $value['order']['sn']. "'");
            $sheet->setCellValue('D'.$i, $value['money']);
            $sheet->setCellValue('E'.$i, "'".$value['sn']."'");
            $sheet->setCellValue('F'.$i, $value['paymentType']);
            $sheet->setCellValue('G'.$i, yztime( $value['createTime'] ));
            $sheet->setCellValue('H'.$i, $value['order']['orderStatusStr']);
            $sheet->setCellValue('I'.$i, yztime( $value['payTime'] ));
            $i++;
        }

        $name = iconv("utf-8", "gb2312", "会员支付列表");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        header("Expires: 0");
        $execl = \PHPExcel_IOFactory::createWriter($execl, 'Excel2007');
        $execl->save('php://output');
    }
}
