<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Validator, Session, Response, Cache;

/**
 * 房间
 */
class PropertyRoomController extends PropertyController {
	/**
	 * 房间列表
	 */
	public function index() {
        $args = Input::all();
        $list = $this->requestApi('propertyroom.lists', $args); 
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
     * 添加房间
     */
    public function create(){
        $sellerId = Input::get('sellerId');
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$sellerId]);
        //print_r($list);
        $seller = $this->requestApi('seller.get', ['id'=>$sellerId]);
        View::share('seller', $seller['data']);
        View::share('buildIds', $list['data']['list']);
        View::share('sellerId', $sellerId);
        return $this->display('edit');
    }

    /**
     * 编辑房间
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.get', $args); 
        $list = $this->requestApi('propertybuilding.lists', ['sellerId'=>$sellerId]);
        //print_r($list);
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('buildIds', $list['data']['list']);
        View::share('sellerId', $args['sellerId']);
        View::share('data', $data['data']);
        return $this->display();
    }

    /**
     * 保存房间
     */
    public function save() {
        $args = Input::all();
        // var_dump($args);
        // exit;
        if ($args['sellerId'] > 0) {
            $data = $this->requestApi('propertyroom.save', $args);
            $url = u('PropertyRoom/index',['sellerId'=> $args['sellerId']]);
            if( $data['code'] > 0 ) {
                return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
            }
            return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

        }
        
    }


    /**
     * 删除房间
     */
    public function destroy(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.delete', ['id' => $args['id']]);
        $url = u('PropertyRoom/index',['sellerId'=> $args['sellerId']]);
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
}
