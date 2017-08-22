<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\AdminLog;
use View, Input, Lang;

/**
 * 操作日志
 */
class AdminLogController extends AuthController { 
	public function index() {
		$post = Input::all();
		!empty($post['beginDate']) ? $args['beginDate'] = $post['beginDate'] : null;
		!empty($post['endDate']) ? $args['endDate']		= $post['endDate']   : null;
		!empty($post['adminId']) ? $args['adminId'] 	= $post['adminId']   : null;
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		!empty($post['pageSize']) ? $args['pageSize'] = intval($post['pageSize']) : $args['pageSize'] = 20;
		$result = $this->requestApi('admin.log.lists', $args); 
		if($result['code']==0) {
			View::share('list', $result['data']['list']);
		}
		View::share('excel',http_build_query($args));
		return $this->display();
	} 
	public function destroy() {
		$data = $this->requestApi('admin.log.delete',Input::all());
		if( $data['code'] > 0 ) {
			return $this->error($data['msg']);
		}
		return $this->success(Lang::get('admin.code.98005') , u('AdminLog/index'), $data['data']);
	}

	public function clear() {
		$data = $this->requestApi('admin.log.clear', Input::all());
		if( $data['code'] == 0 ) {
			return $this->error($data['msg'], u('adminlog/index'));
		}
		return $this->success(Lang::get('admin.code.98008') , u('adminlog/index'), $data['data']);
	}

	/**
	 * 导出到excel
	 */
	public function export() {
		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle('日志列表');

		$sheet->setCellValue('A1', "操作人员");
		$sheet->setCellValue('B1', "操作模块");
		$sheet->setCellValue('C1', "操作名");
		$sheet->setCellValue('D1', "操作结果");
		$sheet->setCellValue('E1', "IP");
		$sheet->setCellValue('F1', "时间");
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getStyle('A')->getAlignment()->setWrapText(true);
		$sheet->getStyle('B')->getAlignment()->setWrapText(true);
		$sheet->getStyle('C')->getAlignment()->setWrapText(true);
		$sheet->getStyle('D')->getAlignment()->setWrapText(true);
		$sheet->getStyle('E')->getAlignment()->setWrapText(true);
		$sheet->getStyle('F')->getAlignment()->setWrapText(true);
		
		$args = [];
		$args = Input::all();
		$result = $this->requestApi('admin.log.lists', $args);

		$i = 2;
		foreach ($result['data']['list'] as $key => $value) {
			$sheet->setCellValue('A'.$i, $value['admin']['name']);
			$sheet->setCellValue('B'.$i, $value['api']);
			$sheet->setCellValue('C'.$i, $value['request']);
			$sheet->setCellValue('D'.$i, $value['status'] ? '成功' : '失败');
			$sheet->setCellValue('E'.$i, $value['ip']);
			$sheet->setCellValue('F'.$i, yztime( $value['logTime']) );
			$i++;
		}		

		$name = iconv("utf-8", "gb2312", "系统日志");
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