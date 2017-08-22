<?php 
namespace YiZan\Http\Controllers\Admin;
use YiZan\Http\Requests\Admin\SellerWithdrawMoneyPostRequest;
use Input,View,From ,Lang,Excel;   
/**
 * 服务人员提现处理
 */
class SellerWithdrawMoneyController extends AuthController { 

	/**
	 * 首页
	 */
	public function index() {
		//财务列表接口
		$result = $this->requestApi('seller.withdraw.lists', Input::all()); 
		if( $result['code'] == 0 ) {
			foreach ($result['data']['list'] as $k => $v) {			
				$result['data']['list'][$k]['stat'] = Lang::get( 'admin.WithdrawMoneyStatus.'.$v['status'] ); 
			}
			View::share('list', $result['data']['list']);
		}
		$nav = Input::get('nav');
		if (!isset($nav)) {
			$nav = 1;
		}
		View::share('nav', $nav);
		return $this->display();
	}

	//提现处理
	public function edit(){
		$result = $this->requestApi('seller.withdraw.dispose', Input::all());		 
		/*返回处理*/ 
		if($result['code']==0){
			return $this->success($result['msg']);
		}else{ 
			return $this->error($result['msg']);
		}  
	} 
	//导出提现
	public function export(){ 
		 require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();
		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle('提现资金');

		$sheet->setCellValue('A1', "提现流水号");
		$sheet->setCellValue('B1', "提现金额");
		$sheet->setCellValue('C1', "提现说明");
		$sheet->setCellValue('D1', "提现银行");
		$sheet->setCellValue('E1', "提现银行卡号");
		$sheet->setCellValue('F1', "服务人员");
		$sheet->setCellValue('G1', "服务手机");
		$sheet->setCellValue('H1', "操作管理员");
		$sheet->setCellValue('I1', "操作备注");
		$sheet->setCellValue('J1', "处理时间");
		// Set column widths
		$sheet->getColumnDimension('A')->setAutoSize(200);
		$sheet->getColumnDimension('B')->setAutoSize(500);
		$sheet->getColumnDimension('C')->setAutoSize(500);
		$sheet->getColumnDimension('D')->setAutoSize(500);
		$sheet->getColumnDimension('E')->setAutoSize(500);
		$sheet->getColumnDimension('F')->setAutoSize(500);
		$sheet->getColumnDimension('G')->setAutoSize(300);
		$sheet->getColumnDimension('H')->setAutoSize(250);
		$sheet->getColumnDimension('I')->setAutoSize(400);
		$sheet->getColumnDimension('J')->setAutoSize(250);  
		
		$args = [];
		$result = $this->requestApi('seller.withdraw.lists',$args);   

		$index = 2;
		if($result['code'] == '0'){ 
			foreach($result['data']['list'] as $time => $item){  
				$sheet->setCellValue('A'.$index, $item['sn']);
				$sheet->setCellValue('B'.$index, $item['money']);
				$sheet->setCellValue('C'.$index, $item['content']); 
				$sheet->setCellValue('D'.$index, $item['bank']); 
				$sheet->setCellValue('E'.$index, $item['bankNo']);  
				$sheet->setCellValue('F'.$index, $item['seller']['name']);
				$sheet->setCellValue('G'.$index, $item['seller']['mobile']);
				$sheet->setCellValue('H'.$index, $item['admin']['name']);
				$sheet->setCellValue('I'.$index, $item['disposeRemark']);
				$sheet->setCellValue('J'.$index, $item['disposeTime'] > 0 ? yztime($item['disposeTime']) : ''); 
				$index++; 
			} 
			
			$name = iconv("utf-8", "gb2312", "卖家-提现列表");
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Cache-Control: cache, must-revalidate');
			header ('Pragma: public');
			header("Expires: 0");
			$execl = \PHPExcel_IOFactory::createWriter($execl, 'Excel2007');
			$execl->save('php://output');	
		}else{
			return $this->error($result['msg'], u('SellerWithdrawMoney/index'));
		}
	}
} 