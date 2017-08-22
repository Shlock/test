<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Route, Page, Response, Request;

/**
 * 楼宇
 */
class PropertyBuildingController extends AuthController {
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
        return $this->display();
    }

    /**
     * 添加楼宇
     */
    public function create(){
        return $this->display('edit');
    }

    /**
     * 编辑楼宇
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.get', $args); 
        //print_r($data);
        View::share('data', $data['data']);
        return $this->display();
    }

    /**
     * 保存楼宇
     */
    public function save() {
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.save', $args);
        if ($args['id'] > 0) {
           $url = u('PropertyBuilding/edit',['id'=>$args['id']]);
        } else {
            $url = u('PropertyBuilding/create');
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    }


    /**
     * 删除楼宇
     */
    public function destroy(){
        $args = Input::all();
        $data = $this->requestApi('propertybuilding.delete', ['id' => $args['id']]);
        $url = u('PropertyBuilding/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


    public function roomindex() {
        $args = Input::all();
        $list = $this->requestApi('propertyroom.lists', $args); 
        //print_r($list);
        if( $list['code'] == 0 ){
            View::share('list', $list['data']['list']);
        }
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display();
    }

    /**
     * 添加房间
     */
    public function roomcreate(){
        $args = Input::all();
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display('roomedit');
    }

    /**
     * 编辑房间
     */
    public function roomedit(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.get', $args); 
        View::share('data', $data['data']);
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display();
    }

    /**
     * 保存房间
     */
    public function roomsave() {
        $args = Input::all();
        $data = $this->requestApi('propertyroom.save', $args);

        if ($args['id'] > 0) {
           $url = u('PropertyBuilding/roomindex',['buildId'=>$args['buildId']]);
        } else {
            $url = u('PropertyBuilding/roomcreate',['buildId'=>$args['buildId']]);
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    }


    /**
     * 删除房间
     */
    public function roomdestroy(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.delete', ['id' => $args['id']]);
        $url = u('PropertyBuilding/roomindex',['buildId'=>$args['buildId']]);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /*
    * 房间号导入
    */
    public function import() {
        View::share('buildId', Input::get('buildId'));
        return $this->display();
    }

    public function importsave() {
        $buildId = Input::get('buildId');
        $file = Request::file('csvfile');
        $args = [];

        if ($file->getClientOriginalExtension() != 'csv') {
            return $this->error('请选择正确的CSV文件格式！');
        }
        if (empty ($file)) { 
            return $this->error('请选择要导入的CSV文件！');
        } 
        
        if ($file->isValid()) {
            $filename = $file -> getRealPath();
            $handle = fopen($filename, 'r'); 
            $dataout = array(); 
            $n = 0; 
            while ($data = fgetcsv($handle, 10000)) { 
                $num = count($data); 
                for ($i = 0; $i < $num; $i++) { 
                    $dataout[$n][$i] = $data[$i]; 
                } 
                $n++; 
            } 
            array_shift($dataout);
            //print_r($dataout);
            $len_result = count($dataout); 
            if($len_result < 1){ 
                return $this->error('没有任何数据！');
            } 
            foreach ($dataout as $key => $value) {
                $args['build'][]            = $value[0];
                $args['roomNum'][]          = $value[1];
                $args['owner'][]            = iconv('gb2312', 'utf-8', $value[2]);
                $args['mobile'][]           = $value[3];
                $args['propertyFee'][]      = $value[4];
                $args['structureArea'][]    = $value[5];
                $args['roomArea'][]         = $value[6];
                $args['intakeTime'][]       = $value[7];
                $args['remark'][]           = iconv('gb2312', 'utf-8', $value[8]);
            }
            //var_dump($args);
            fclose($handle);
        }
        
        $result = $this->requestApi('propertyroom.import', $args);
        $url = u('PropertyBuilding/roomindex',['buildId'=>$buildId]);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg'] ? $result['msg']: $result['msg'] = Lang::get('admin.code.98009'), $url );
        }
        return $this->success($result['msg']?$result['msg']:$result['msg'] = Lang::get('admin.code.98005'), $url , $result['data']);
    }

}
