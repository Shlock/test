<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect, Request;
/**
 * 物业费
 */
class PropertyFeeController extends AuthController {

	/**
	 * 物业费列表
	 */
	public function index() {
		$args = Input::all(); 
        $result = $this->requestApi('propertyfee.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        //print_r($result);
        View::share('args', $args);
		return $this->display();
	}

    public function create(){
        $args = Input::all();
        $list = $this->requestApi('propertybuilding.lists');
        View::share('buildIds', $list['data']['list']);
       // $roomlist = $this->requestApi('propertyroom.lists'); 
      //  View::share('roomIds', $roomlist['data']['list']);
        return $this->display('edit');
    }

    /**
     * 编辑
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('propertyfee.get', $args); 
        View::share('data', $data['data']);
        $list = $this->requestApi('propertybuilding.lists');
        View::share('buildIds', $list['data']['list']);
        $roomlist = $this->requestApi('propertyroom.lists', ['buildId'=>$data['data']['buildId']]); 
        View::share('roomIds', $roomlist['data']['list']);
        return $this->display();
    }

    public function save() {
        $args = Input::all();
        $data = $this->requestApi('propertyfee.save', $args);

        if ($args['id'] > 0) {
           $url = u('PropertyFee/index');
        } else {
            $url = u('PropertyFee/create');
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    }

    /*
    * 缴费
    */
    public function payfee(){
        $args = Input::all();
        $data = $this->requestApi('propertyfee.get', $args); 
        View::share('data', $data['data']);

        return $this->display();
    }

    public function paysave() {
        $args = Input::all();
        $data = $this->requestApi('propertyfee.paysave', $args);

        $url = u('PropertyFee/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    }


	/**
	 * [destroy 删除物业费]
	 */
	public function destroy(){
		$args = Input::all();
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('propertyfee.delete',['id'=>$args['id']]); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertyFee/index'), $result['data']);
	}


    public function searchroom() {
        $args = Input::all();
        $result = $this->requestApi('propertyroom.lists',$args);
        return Response::json($result);
    }

    public function import() {
        return $this->display();
    }

    public function importsave() {
        $file = Request::file('csvfile');
        $args = [];
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
                $args['name'][]             = iconv('gb2312', 'utf-8', $value[2]);
                $args['payableFee'][]       = $value[3];
                $args['payableTime'][]      = $value[4];
                $args['payFee'][]           = $value[5];
                $args['payTime'][]          = $value[6];
            }
            //var_dump($args);
            fclose($handle);
        }
        
        $result = $this->requestApi('propertyfee.import', $args);
        $url = u('PropertyFee/index');
        if( $result['code'] > 0 ) {
            return $this->error($result['msg'] ? $result['msg']: $result['msg'] = Lang::get('admin.code.98009'), $url );
        }
        return $this->success($result['msg']?$result['msg']:$result['msg'] = Lang::get('admin.code.98005'), $url , $result['data']);
    }


}
