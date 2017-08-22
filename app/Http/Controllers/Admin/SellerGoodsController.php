<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\SellerGoods;
use YiZan\Http\Requests\Admin\GoodsCreatePostRequest;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response;
/**
 * 服务
 */
class SellerGoodsController extends AuthController {
	/**
	 * 服务管理-服务列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['name']) 		 ?  $args['name']        = strval($post['name']) : null;
		!empty($post['sellerName']) ?  $args['sellerName'] = strval($post['sellerName']) : null;
		!empty($post['cateId'])    ?  $args['cateId']    = intval($post['cateId']) : null;
		!empty($post['page']) 	  ?  $args['page'] 	   = intval($post['page']) : $args['page'] = 1;
		$args['status'] = 0; //禁用+通过
		$result = $this->requestApi('goods.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		$_cate = $this->getcate();
		$cate2 = [ 
			"id" => '',
			"pid" => '',
			"name" => "全部分类",
			"sort" => '',
			"status" => '',
			"level" => '',
			"levelname" => "全部分类",
			"levelrel" => ""
		];
		array_unshift($_cate, $cate2);
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		View::share('excel',http_build_query($args));
		return $this->display();
	}

	public function search() {
		$post = Input::all();
		!empty($post['name']) 		 ?  $args['name']        = strval($post['name']) : null;
		!empty($post['sellerName']) ?  $args['sellerName'] = strval($post['sellerName']) : null;
		!empty($post['cateId'])    ?  $args['cateId']    = intval($post['cateId']) : null;
		!empty($post['page']) 	  ?  $args['page'] 	   = intval($post['page']) : $args['page'] = 1;
		$args['pageSize'] = 10;
		$args['status'] = 2;
		$result = $this->requestApi('goods.lists', $args);
		if($result['code']>0) {
			return $this->error($result['msg']);
		}
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		$cate2 = [ 
			"id" => '',
			"pid" => '',
			"name" => "全部分类",
			"sort" => '',
			"status" => '',
			"level" => '',
			"levelname" => "全部分类",
			"levelrel" => ""
		];
		array_unshift($cate, $cate2);
		View::share('list', $result['data']['list']);
		View::share('cate', $cate);

		return $this->display();
	}

	/**
	 * 服务管理-添加，更新服务详细
	 */
	public function edit() {
		$args = Input::all();
		//编辑
		if ( !empty($args['id']) ) {
			$args['id'] = $args['id'];
			$result = $this->requestApi('goods.get',$args);
			if ($result['code'] == 0){
				$result['data']['duration'] /= 3600;
				View::share('data', $result['data']);
			}
		}
		$_cate = $this->getcate();
		$cate2 = [ 
			"id" => 0,
			"pid" => 0,
			"name" => "选择分类",
			"sort" => 0,
			"status" => 0,
			"level" => 0,
			"levelname" => "选择分类",
			"levelrel" => ""
		];
		array_unshift($_cate,$cate2); 
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		} 
		View::share('cate', $cate);
		return $this->display();
	}

	public function create() {
		$_cate = $this->getcate();
		$cate2 = [ 
			"id" => 0,
			"pid" => 0,
			"name" => "选择分类",
			"sort" => 0,
			"status" => 0,
			"level" => 0,
			"levelname" => "选择分类",
			"levelrel" => ""
		];
		array_unshift($_cate,$cate2); 
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		$city = $this->requestApi('city.lists');
		if( $city['code'] == 0 ) {
			View::share('city', $city['data']);
		}
		return $this->display('edit');
	}

	/**
	 * 服务管理-添加，更新服务处理
	 */
	public function save(GoodsCreatePostRequest $request) {
		$args = Input::all();
		$args['duration'] *= 3600;
		//价格验证
		if( $args['priceType'] == 1 ) {
			if( empty($args['duration']) ) 
				return $this->error( Lang::get('admin.code.21011') );
			if( empty($args['price']) ) 
				return $this->error( Lang::get('admin.code.21003') );
		}
		else if( $args['priceType'] == 2 ) {
			if( empty($args['_price']) )
				return $this->error( Lang::get('admin.code.21003') );
			$args['price'] = $args['_price'];
		}
		else{
			//类型错误
			return $this->error( Lang::get('admin.code.21020') );
		}
		unset($args['_price']);
		
		if( !empty($args['id']) ) {
			$result = $this->requestApi('goods.update',$args); //更新
		}
		else {
			$result = $this->requestApi('goods.create',$args);  //创建
		}
		
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('goods/index'), $result['data']);
	}

	/**
	 * 服务管理-删除服务
	 */
	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('goods.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('goods/index'), $result['data']);
		
	}

	//获取分类
	public function getcate() {
		$result = $this->requestApi('goods.cate.lists');
		if($result['code']==0) {
			$this->generateTree(0,$result['data']);
		}
		//生成树形
		$cate = $this->_cates;
		return $cate;
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('goods.updateStatus',$args);
		return Response::json($result);
	}

	/**
	 * 查看
	 */
	public function lookat() {
		$args = Input::all();

		if( $args['id'] > 0 )
			$result = $this->requestApi('goods.get',$args);

		if ($result['code'] == 0) {
			$result['data']['duration'] /= 3600;
			$_cate = $this->getcate();
			$cate = [];
			foreach ($_cate as $key => $value) {
				$cate[$value['id']] = $value;
			}
			if ($result['data']['cate']['id']) {
				$result['data']['cate'] = $cate[$result['data']['cate']['id']]['levelname'];
			}
			else {
				$result['data']['cate'] = "--分类不存在--";
			}
			
			View::share('data', $result['data']);
		}

		return $this->display();
	}

	/**
	 * 导出到excel
	 */
	public function export() {
		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle('服务列表');

		$sheet->setCellValue('A1', "服务名称");
		$sheet->setCellValue('B1', "服务描述");
		$sheet->setCellValue('C1', "服务分类");
		$sheet->setCellValue('D1', "服务价格");
		$sheet->setCellValue('E1', "市场价格");
		$sheet->setCellValue('F1', "服务时长");
		$sheet->setCellValue('G1', "服务人员");
		$sheet->setCellValue('H1', "服务人员电话");
		$sheet->setCellValue('I1', "服务人员描述");
		$sheet->setCellValue('J1', "服务人员地址");

		$sheet->getColumnDimension('A')->setWidth(25);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(10);
		$sheet->getColumnDimension('E')->setWidth(10);
		$sheet->getColumnDimension('F')->setWidth(10);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->getStyle('A')->getAlignment()->setWrapText(true);
		$sheet->getStyle('B')->getAlignment()->setWrapText(true);
		$sheet->getStyle('C')->getAlignment()->setWrapText(true);
		$sheet->getStyle('I')->getAlignment()->setWrapText(true);
		
		$args = [];
		$args = Input::all();
		$result = $this->requestApi('goods.lists', $args);
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		$i = 2;
		foreach ($result['data']['list'] as $key => $value) {
			$cateLayer = $value['cate']['id'] > 0 ? $cate[$value['cate']['id']]['levelrel'] : null;
			$sheet->setCellValue('A'.$i, $value['name']);
			$sheet->setCellValue('B'.$i, $value['brief']);
			$sheet->setCellValue('C'.$i, $cateLayer);
			$sheet->setCellValue('D'.$i, $value['price']);
			$sheet->setCellValue('E'.$i, $value['marketPrice']);
			$sheet->setCellValue('F'.$i, $value['duration']/3600);
			$sheet->setCellValue('G'.$i, $value['seller']['name']);
			$sheet->setCellValue('H'.$i, $value['seller']['mobile']);
			$sheet->setCellValue('I'.$i, $value['seller']['brief']);
			$sheet->setCellValue('J'.$i, $value['seller']['address']);
			$i++;
		}		

		$name = iconv("utf-8", "gb2312", "服务列表详细");
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
