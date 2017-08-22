<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\DoorAccess; 
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 门禁
 */
class DoorAccessController extends AuthController {
	
	/**
	 * 门禁列表
	 */
	public function index() {
		$args = Input::all();  
		$result = $this->requestApi('dooraccess.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		} 
		return $this->display();
	}

	/**
	 * 创建
	 */
	public function create(){ 
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$this->sellerId]);
        View::share('buildIds', $list['data']['list']);
		return $this->display('edit');
	}
 	

	/**
	 * 编辑
	 */
	public function edit(){
		$args = Input::all();
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$this->sellerId]);
        View::share('buildIds', $list['data']['list']);
    	$result = $this->requestApi('dooraccess.get', $args); 
    	View::share('data', $result['data']);
		return $this->display();
	}

	/**
	 * 保存
	 */
	public function save(){
    	$args = Input::all();
        $args['districtId'] = $this->seller['district']['id'];
    	$result = $this->requestApi('dooraccess.save', $args);  
    	if($result['code'] == 0){
    		return $this->success($result['msg']);
    	}
    	return $this->error($result['msg']);
	}
    /**
     * 删除
     */
    public function destroy(){
        $args = Input::all();
        $result = $this->requestApi('dooraccess.delete', $args); 
        if($result['code'] == 0){
            return $this->success($result['msg']);
        }
        return $this->error($result['msg']);
    }

    /**
     * 门禁导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('物业公司门禁列表');

        $sheet->setCellValue('A1', "编号");
        $sheet->setCellValue('B1', "门禁名称");
        $sheet->setCellValue('C1', "门禁编号");
        $sheet->setCellValue('D1', "楼栋");
        $sheet->setCellValue('E1', "备注"); 

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(20);   
        
        $args = Input::all();
        $args['isTotal'] = 1;  
        $result = $this->requestApi('dooraccess.lists', $args); 
        $i = 2; 
        foreach ($result['data'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['id']);
            $sheet->setCellValue('B'.$i, $value['name']);
            $sheet->setCellValue('C'.$i, $value['pid']);
            $sheet->setCellValue('D'.$i, $value['build']['name']);
            $sheet->setCellValue('E'.$i, $value['remark']); 
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "物业公司门禁列表");
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
