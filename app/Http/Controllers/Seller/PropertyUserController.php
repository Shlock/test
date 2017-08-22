<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 业主
 */
class PropertyUserController extends AuthController {

	/**
	 * 业主列表
	 */
	public function index() {
		$args = Input::all(); 
        $args['status'] = 1;//已审核
        $result = $this->requestApi('propertyuser.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        //print_r($result);
		return $this->display();
	}

	/*
	* 查看门禁
	*/
	public function check() {
		$args = Input::all(); 
        $result = $this->requestApi('propertyuser.accesscardlists', $args);
        // print_r($result);
        // exit;
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        $data = $this->requestApi('propertyuser.get', $args);
        View::share('data', $data['data']);
        
        View::share('args', $args);
		return $this->display();
	}

    /**
     * 添加门禁
     */
    public function add(){
        $args = Input::all();  
        $doorIds = $this->requestApi('propertyuser.doorslists', $args);
        //print_r($doorIds);
        View::share('doorIds', $doorIds['data']);
        View::share('args', $args);
        return $this->display();
    }

	/*
	* 编辑门禁
	*/
	public function edit() {
		$args = Input::all(); 
        $data = $this->requestApi('propertyuser.getaccesscard', $args);
        //print_r($data);
        if( $result['code'] == 0 ){
            View::share('data', $data['data']);
        }
        $doorIds = $this->requestApi('propertyuser.doorslists', $args);
        //print_r($doorIds);
        View::share('doorIds', $doorIds['data']);
        View::share('args', $args);
		return $this->display();
	}

	/*
	* 保存门禁
	*/
	public function save() {
		$args = Input::all(); 
        $result = $this->requestApi('propertyuser.save', $args);
        if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertyUser/check',['puserId'=>$args['puserId']]), $result['data']);
	}


	/**
	 * [destroy 删除门禁]
	 */
	public function destroydoor(){
		$args = Input::all();
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('propertyuser.deletedoor',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertyUser/check',['puserId'=>$args['puserId']]), $result['data']);
	}

	/**
	 * [destroy 删除业主]
	 */
	public function destroy(){
		$args = Input::all();
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('propertyuser.delete',['id'=>$args['id']]); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertyUser/index'), $result['data']);
	}

    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('propertyroom.updateStatus',$args);
        return Response::json($result);
    }

	//导出业主
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('业主信息列表');

        $sheet->setCellValue('A1', "物业公司");
        $sheet->setCellValue('B1', "小区名称");
        $sheet->setCellValue('C1', "楼栋号");
        $sheet->setCellValue('D1', "房间号");
        $sheet->setCellValue('E1', "业主");
        $sheet->setCellValue('F1', "电话");
        $sheet->setCellValue('G1', "是否身份认证");
        $sheet->setCellValue('H1', "是否申请门禁");
        $sheet->setCellValue('I1', "物业费(元/月)");

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(30);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        
        $args = Input::all();
        $result = $this->requestApi('propertyuser.lists', $args);
        $Status = [1 => '是', 0 => '否'];
        $AccessStatus = [1 => '是', 0 => '否'];
        $i = 2;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['seller']['name']);
            $sheet->setCellValue('B'.$i, $value['district']['name']);
            $sheet->setCellValue('C'.$i, $value['build']['name']);
            $sheet->setCellValue('D'.$i, $value['room']['roomNum']);
            $sheet->setCellValue('E'.$i, $value['name']);
            $sheet->setCellValue('F'.$i, $value['mobile']);
            $sheet->setCellValue('G'.$i, $Status[$value['status']]);
            $sheet->setCellValue('H'.$i, $AccessStatus[$value['accessStatus']]);
            $sheet->setCellValue('I'.$i, $value['room']['propertyFee']);
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "业主信息");
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
