<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Validator, Session, Response, Cache;

/**
 * 楼宇
 */
class PropertyBuildingController extends PropertyController {
	/**
	 * 楼宇列表
	 */
	public function index() {
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
    public function create(){
        $sellerId = Input::get('sellerId');
        $seller = $this->requestApi('seller.get', ['id'=>$sellerId]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $sellerId);
        return $this->display('edit');
    }

    /**
     * 编辑楼宇
     */
    public function edit(){
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
    public function save() {
        $args = Input::all();
        if ($args['sellerId'] > 0) {
            $data = $this->requestApi('propertybuilding.save', $args);
            $url = u('PropertyBuilding/index',['sellerId'=> $args['sellerId']]);
            if( $data['code'] > 0 ) {
                return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
            }
            return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

        }
        
    }


    /**
     * 删除楼宇
     */
    public function destroy(){
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.delete', ['id' => $args['id']]);
        $url = u('PropertyBuilding/index',['sellerId'=> $args['sellerId']]);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /**
     * 导出到excel
     */
    public function export() {
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
}
