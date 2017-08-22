<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use YiZan\Models\DoorOpenLog; 
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 门禁记录
 */
class DoorOpenLogController extends AuthController {
	
	/**
	 * 门禁记录列表
	 */
	public function index() {
		$args = Input::all();  
		$result = $this->requestApi('dooropenlog.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		} 
		return $this->display();
	}

	/**
     * 门禁记录导出
     */
    public function export(){
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('物业公司开门记录列表');

        $sheet->setCellValue('A1', "编号");
        $sheet->setCellValue('B1', "门禁名称");
        $sheet->setCellValue('C1', "房间名称");
        $sheet->setCellValue('D1', "业主姓名");
        $sheet->setCellValue('E1', "联系电话");
        $sheet->setCellValue('F1', "开门时间"); 

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);  
        
        $args = Input::all();
        $args['isTotal'] = 1; 
        $result = $this->requestApi('dooropenlog.lists', $args); 
        $i = 2; 
        foreach ($result['data'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['id']);
            $sheet->setCellValue('B'.$i, $value['door']['name']);
            $sheet->setCellValue('C'.$i, $value['room']['owner']);
            $sheet->setCellValue('D'.$i, $value['puser']['name']);
            $sheet->setCellValue('E'.$i, $value['puser']['mobile']);
            $sheet->setCellValue('F'.$i, Time::toDate($value['createTime'])); 
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "物业公司开门记录列表");
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
