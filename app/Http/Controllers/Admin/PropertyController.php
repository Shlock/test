<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 物业
 */
class PropertyController extends AuthController {

	/**
	 * 物业列表
	 */
	public function index() {
		$args = Input::all();  
        $result = $this->requestApi('seller.propertylists', $args); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	}

	/**
	 * [create 添加物业]
	 */
	public function create(){
		return $this->display('edit');
	}
	
	/**
	 * [edit 物业板块]
	 */
	public function edit(){
		$args = Input::all();
        $result = $this->requestApi('seller.get', $args);
        if ($seller['code'] == 0) {
            View::share('data', $result['data']);
        }
		return $this->display();
	}

	/**
	 * [save 添加/编辑板块]
	 */
	public function save(){
		$args = Input::all();
		// if($args['id'] > 0){
		// 	$result = $this->requestApi('seller.updateproperty',$args);
		// } else {
			$result = $this->requestApi('seller.createproperty',$args);
		//}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('Property/index'));
	}

	/**
	 * [destroy 删除物业]
	 */
	public function destroy(){
		$args['id'] = (int)Input::get('id');
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('seller.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('Property/index'), $result['data']);
	}

    public function updateStatus() {
        $args = Input::all();
        $args['status'] = $args['val'];
        $result = $this->requestApi('seller.updatestatus',$args);
        $result = array (
            'status'    => true,
            'data'      => $args['val'],
            'msg'       => null
        );
        return Response::json($result);
    }

	/**
     * 导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('物业公司列表');

        $sheet->setCellValue('A1', "编号");
        $sheet->setCellValue('B1', "公司名称");
        $sheet->setCellValue('C1', "小区名称");
        $sheet->setCellValue('D1', "联系人");
        $sheet->setCellValue('E1', "联系电话");
        $sheet->setCellValue('F1', "状态"); 

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);  
        
        $args = Input::all();
        $args['isTotal'] = 1;
        $result = $this->requestApi('seller.propertylists', $args);

        $i = 2; 
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['id']);
            $sheet->setCellValue('B'.$i, $value['name']);
            $sheet->setCellValue('C'.$i, $value['district']['name']);
            $sheet->setCellValue('D'.$i, $value['contacts']);
            $sheet->setCellValue('E'.$i, $value['mobile']);
            $sheet->setCellValue('F'.$i, Lang::get('admin.property.'.$value['status'])); 
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "物业公司列表");
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

    /**
     * 门禁记录
     */
    public function dooropenlog(){
    	$args = Input::all();  
    	$seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);
    	if(empty($seller['data'])){
    		return $this->error('参数错误');
    	}
    	View::share('seller', $seller['data']);
    	$args['sellerId'] = $seller['data']['id'];
    	$args['districtId'] = $seller['data']['district']['id']; 
        $result = $this->requestApi('dooropenlog.lists', $args);  
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
    }

    /**
     * 门禁记录导出
     */
    public function dooropenlogexport(){
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

    /**
     *  门禁列表
     */
    public function dooraccess(){
    	$args = Input::all(); 
    	$seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);
    	if(empty($seller['data'])){
    		return $this->error('参数错误');
    	}
    	View::share('seller', $seller['data']);
    	$args['sellerId'] = $seller['data']['id'];
    	$args['districtId'] = $seller['data']['district']['id']; 
        $result = $this->requestApi('dooraccess.lists', $args);  
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
    }

    /**
     * 添加/编辑门禁页面
     */
    public function dooredit(){
    	$args = Input::all();
    	$seller = $this->requestApi('seller.get', ['id' => $args['sellerId']]);   
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$args['sellerId']]);
        View::share('buildIds', $list['data']['list']);
    	if(empty($seller['data'])){
    		return $this->error('参数错误');
    	}
    	View::share('seller', $seller['data']);
    	if(Input::get('id') > 0){
        	$result = $this->requestApi('dooraccess.get', $args); 
            View::share('data', $result['data']);
    	}
    	return $this->display('dooredit');
    }

    /**
     * 添加门禁
     */
    public function doorsave(){
    	$args = Input::all();
    	$result = $this->requestApi('dooraccess.save', $args);  
    	if($result['code'] == 0){
    		return $this->success($result['msg']);
    	}
    	return $this->error($result['msg']);
    } 

    /**
     * 删除门禁
     */
    public function doordelete(){
    	$result = $this->requestApi('dooraccess.delete', $args); 
    	if($result['code'] == 0){
    		return $this->success($result['msg']);
    	}
    	return $this->error($result['msg']);
    }

	/**
     * 门禁导出到excel
     */
    public function doorexport() {
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


    /**
     * 楼宇列表
     */
    public function buildingindex() {
        $args = Input::all();
        $list = $this->requestApi('propertybuilding.lists', $args); 
        //print_r($list);

        if( $list['code'] == 0 ){
            View::share('list', $list['data']['list']);
        }

        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        return $this->display();
    }

    /**
     * 添加楼宇
     */
    public function buildingcreate(){
        $sellerId = Input::get('sellerId');
        $seller = $this->requestApi('seller.get', ['id'=>$sellerId]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $sellerId);
        return $this->display('buildingedit');
    }

    /**
     * 编辑楼宇
     */
    public function buildingedit(){
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.get', $args); 
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        View::share('data', $data['data']);
        return $this->display();
    }

    /**
     * 保存楼宇
     */
    public function buildingsave() {
        $args = Input::all();
        if ($args['sellerId'] > 0) {
            $data = $this->requestApi('propertybuilding.save', $args);
            $url = u('Property/buildingindex',['sellerId'=> $args['sellerId']]);
            if( $data['code'] > 0 ) {
                return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
            }
            return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

        }
        
    }


    /**
     * 删除楼宇
     */
    public function buildingdestroy(){
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.delete', ['id' => $args['id']]);
        $url = u('Property/buildingindex',['sellerId'=> $args['sellerId']]);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /**
     * 导出到excel
     */
    public function buildingexport() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('楼宇信息列表');

        $sheet->setCellValue('A1', "物业公司");
        $sheet->setCellValue('B1', "小区名称");
        $sheet->setCellValue('C1', "楼栋号");
        $sheet->setCellValue('D1', "备注");

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        
        $args = Input::all();
        $result = $this->requestApi('propertybuilding.lists', $args);

        $i = 2;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['seller']['name']);
            $sheet->setCellValue('B'.$i, $value['district']['name']);
            $sheet->setCellValue('C'.$i, $value['name']);
            $sheet->setCellValue('D'.$i, $value['remark']);
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "楼宇信息");
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

    /**
     * 房间列表
     */
    public function roomindex() {
        $args = Input::all();
        $list = $this->requestApi('propertyroom.lists', $args); 
        //print_r($list);

        if( $list['code'] == 0 ){
            View::share('list', $list['data']['list']);
        }
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 添加房间
     */
    public function roomcreate(){
        $sellerId = Input::get('sellerId');
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$sellerId]);
        $build = $this->requestApi('propertybuilding.get', ['id'=>Input::get('buildId')]); 
        //print_r($list);
        $seller = $this->requestApi('seller.get', ['id'=>$sellerId]);
        View::share('seller', $seller['data']);
        View::share('buildIds', $list['data']['list']);
        View::share('sellerId', $sellerId);
        View::share('build', $build['data']);
        return $this->display('roomedit');
    }

    /**
     * 编辑房间
     */
    public function roomedit(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.get', $args); 
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$args['sellerId']]);
        $build = $this->requestApi('propertybuilding.get', ['id'=>Input::get('buildId')]); 
        //print_r($list);
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('buildIds', $list['data']['list']);
        View::share('sellerId', $args['sellerId']);
        View::share('data', $data['data']);
        View::share('build', $build['data']);
        return $this->display();
    }

    /**
     * 保存房间
     */
    public function roomsave() {
        $args = Input::all();
        // var_dump($args);
        // exit;
        if ($args['sellerId'] > 0) {
            $data = $this->requestApi('propertyroom.save', $args);
            $url = u('Property/roomindex',['sellerId'=> $args['sellerId'], 'buildId'=>$args['buildId']]);
            if( $data['code'] > 0 ) {
                return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
            }
            return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

        }
        
    }


    /**
     * 删除房间
     */
    public function roomdestroy(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.delete', ['id' => $args['id']]);
        $url = u('Property/roomindex',['sellerId'=> $args['sellerId'], 'buildId'=>$args['buildId']]);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /**
     * 导出到excel
     */
    public function roomexport() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('房间信息列表');

        $sheet->setCellValue('A1', "物业公司");
        $sheet->setCellValue('B1', "小区名称");
        $sheet->setCellValue('C1', "楼栋号");
        $sheet->setCellValue('D1', "房间号");
        $sheet->setCellValue('E1', "业主名称");
        $sheet->setCellValue('F1', "电话");
        $sheet->setCellValue('G1', "备注");

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(40);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        
        $args = Input::all();
        $result = $this->requestApi('propertyroom.lists', $args);

        $i = 2;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['seller']['name']);
            $sheet->setCellValue('B'.$i, $value['district']['name']);
            $sheet->setCellValue('C'.$i, $value['build']['name']);
            $sheet->setCellValue('D'.$i, $value['roomNum']);
            $sheet->setCellValue('E'.$i, $value['owner']);
            $sheet->setCellValue('F'.$i, $value['mobile']);
            $sheet->setCellValue('G'.$i, $value['remark']);
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "房间信息");
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

    /**
     * 业主列表
     */
    public function puserindex() {
        $args = Input::all(); 
        $result = $this->requestApi('propertyuser.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        //print_r($result);
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        return $this->display();
    }

    /*
    * 查看门禁
    */
    public function pusercheck() {
        $args = Input::all(); 
        $result = $this->requestApi('propertyuser.accesscardlists', $args);
        // print_r($result);
        // exit;
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        
        View::share('args', $args);
        return $this->display();
    }

    /*
    * 添加、编辑门禁
    */
    public function puseredit() {
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
    public function pusersave() {
        $args = Input::all(); 
        //var_dump($args);
        // exit;
        $result = $this->requestApi('propertyuser.save', $args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Property/pusercheck',['puserId'=>$args['puserId'], 'sellerId'=>$args['sellerId']]), $result['data']);
    }


    /**
     * [destroy 删除门禁]
     */
    public function puserdestroydoor(){
        $args = Input::all();
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('propertyuser.deletedoor',$args); 
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Property/pusercheck',['puserId'=>$args['puserId'], 'sellerId'=>$args['sellerId']]), $result['data']);
    }

    /**
     * [destroy 删除业主]
     */
    public function puserdestroy(){
        $args = Input::all();
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('propertyuser.delete',['id'=>$args['id']]); 
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Property/puserindex',['sellerId'=>$args['sellerId']]), $result['data']);
    }

    //导出业主
    public function puserexport() {
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

    /*
    * 公告管理
    */
    public function articleindex() {
        $args = Input::all();
        $result = $this->requestApi('property.articlelists', $args);

        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('args', $args);
        View::share('list', $result['data']['list']); 
        return $this->display();
    }

    /**
     * 编辑详细
     */
    public function articleedit() { 
        $args = Input::all();
        $result = $this->requestApi('property.articleget', $args);
        View::share('data', $result['data']); 
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        return $this->display();
    }

    /**
     * 编辑处理
     */
    public function articlecreate() { 
        $args = Input::all();
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        return $this->display('articleedit');
    } 

    public function articlesave() {
        $args = Input::all();
        // var_dump($args);
        // exit;
        $result = $this->requestApi('property.articlesave',$args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Property/articleindex',['sellerId'=>$args['sellerId']]), $result['data']);
    }

    /**
     * 
     */
    public function articledestroy() {
        $args = Input::all();

        if( !empty( $args['id'] ) ) {
            $result = $this->requestApi('property.articledelete',$args); 
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005') , u('Property/articleindex',['sellerId'=>$args['sellerId']]), $result['data']);
    }

    /**
     * 报修管理
     */
    public function repairindex(){
        $args = Input::all();
        !empty($args['nav'])            ? $nav                 = $args['nav'] : $nav = 1;
        //报修列表
       // var_dump($args);
        $result = $this->requestApi('repair.lists', $args); 
       // print_r($result);
        View::share('list', $result['data']['list']);  
        //获取物业信息
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        View::share('nav', $nav);
        return $this->display();
    }

    /**
     * 报修详情
     */
    public function repairdetail(){
        $args = Input::all();
        $result = $this->requestApi('repair.get', $args);    
        View::share('data', $result['data']); 
        return $this->display();
    } 

    public function repairsave() {
        $args = Input::all(); 
        $result = $this->requestApi('repair.save', $args);
        return Response::json($result);
    }  

    /**
     * 导出到excel
     */
    public function repairexport() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('报修列表');

        $sheet->setCellValue('A1', "物业公司");
        $sheet->setCellValue('B1', "小区");
        $sheet->setCellValue('C1', "楼栋号");
        $sheet->setCellValue('D1', "房间号");
        $sheet->setCellValue('E1', "业主");
        $sheet->setCellValue('F1', "电话");
        $sheet->setCellValue('G1', "内容");
        $sheet->setCellValue('H1', "状态");

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(100);
        $sheet->getColumnDimension('H')->setWidth(40);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        
        $args = Input::all(); 
        $args['isTotal'] = 1;  
        $result = $this->requestApi('repair.lists', $args); 
        $i = 2;
        foreach ($result['data'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['seller']['name']);
            $sheet->setCellValue('B'.$i, $value['district']['name']);
            $sheet->setCellValue('C'.$i, $value['build']['name']);
            $sheet->setCellValue('D'.$i, $value['room']['roomNum']);
            $sheet->setCellValue('E'.$i, $value['puser']['name']);
            $sheet->setCellValue('F'.$i, $value['puser']['mobile']);
            $sheet->setCellValue('G'.$i, $value['content']);
            $sheet->setCellValue('H'.$i, Lang::get('api.repair_status.'.$value['status']));
            $i++;
        }       

        $name = iconv("utf-8", "gb2312", "报修列表");
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
