<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 员工请假
 */
class StaffleaveController extends AuthController {
	/**
	 * 员工请假列表
	 */
	public function index() {
        $post = Input::all();
        $args['page'] = !empty($post['page']) ? (int)$post['page'] : 1;
        $result = $this->requestApi('Staffleave.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	}

    /**
     * [dispose 处理请假]
     */
    public function dispose(){
        $args = [
            'id' => (int)Input::get('id'),
            'agree' => Input::get('agree')
        ];
        $result = $this->requestApi('Staffleave.dispose',$args);
        die(json_encode($result));
    }

    /**
     * [destroy 删除请假]
     */
    public function destroy() {
        $args['id'] = (int)Input::get('id');
        $result = $this->requestApi('Staffleave.delete',$args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success($result['msg'], u('Staffleave/index'), $result['data']);
    }

}
