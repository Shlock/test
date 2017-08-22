<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, Response, Redirect;
/**
 * 员工
 */
class StaffController extends AuthController {
    protected $staffType;
    public function __construct() {
        parent::__construct();
        //$this->staffType = 2;//服务类型
        if ($this->sellerId < 1) {//如果未登录时,则退出
            Redirect::to(u('Public/login'))->send();
        }
        View::share('login_seller', $this->seller);
        //验证权限
    }
	/**
	 * 员工管理-员工列表
	 */
	public function index() {
        $args = Input::all();
       // $args['type'] = $this->staffType;
       // var_dump($args);
        $result = $this->requestApi('sellerstaff.lists', $args);
       // dd($result);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        
        View::share('seller', $this->seller);
        View::share('args', $args);
		return $this->display();
	}

    /**
     * [create 添加员工]
     */
    public function create(){
        View::share('seller',$this->seller);
        // $type = $this->staffType;
        // View::share('type',$type);
        return $this->display('edit');
    }

    /**
     * [edit 编辑员工]
     */
    public function edit(){
        $args['id'] = (int)Input::get('id');
        if ($args['id'] > 0) {
            $result = $this->requestApi('sellerstaff.get',$args);
            if($result['code'] == 0)
            View::share('data', $result['data']);
        }
        //print_r($result['data']);
        $args['staffId'] = $args['id'];
       // $type = $this->staffType;
        View::share('seller',$this->seller);
       // View::share('type',$type);
        return $this->display();
    }

    /**
     * 查看员工
     */
    public function get() {
        $args['id'] = (int)Input::get('staffId');
        if ($args['id'] < 1) 
            Redirect::to(u('Staff/index'))->send();

        $result = $this->requestApi('sellerstaff.get',$args);
        View::share('data', $result['data']);

        //获取时分秒
        $time = Time::getHouerMinuteSec(true, true, false);
        View::share('time', $time);

        return $this->display();
    }

	public function save() {
        $args = Input::all();
       // $args['type'] = $this->staffType;
        if ($this->seller['type'] == 1) {
           $args['type'] = 0;
        }
        if( (int)$args['id'] > 0 ) {
            $result = $this->requestApi('sellerstaff.update',$args); //更新
        }
        else {
            $result = $this->requestApi('sellerstaff.create',$args);  //创建
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        if ($args['type'] == 1) {
            return $this->success( Lang::get('admin.code.98008'), u('DeliveryStaff/index'), $result['data'] );
        } else {
            return $this->success( Lang::get('admin.code.98008'), u('Staff/index'), $result['data'] );
        }
        
	}

    public function updateStatus() {
        $post = Input::all();
        $args = [
            $post['field'] => $post['val'],
            'id' => $post['id']
        ];
        $result = $this->requestApi('Sellerstaff.updateStatus',$args);
        return Response::json($result);
    }
    /**
     * [destroy 删除员工]
     */
    public function destroy(){
        $args['id'] = (int)Input::get('id');
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('sellerstaff.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Staff/index'), $result['data']);
    }

    /*
    *更新预约时间
    */
    public function updatatime(){
        $args = Input::all();
        $data  = $this->requestApi('Staffstime.update',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Staff/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Staff/index'), $data['data']);
    }
     /*
    *添加预约时间
    */
    public function adddatatime(){
        $args = Input::all();
        $data  = $this->requestApi('Staffstime.add',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Staff/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Staff/index'), $data['data']);
    }
     /**
     * [destroy 删除时间]
     */
    public function deldatatime(){
        $args = Input::all();
        $result = $this->requestApi('Staffstime.delete',$args);
        return Response::json($result);
    }
    /* 
    * 计算星座 string get_zodiac_sign() 
    * 输入：月份，日期 
    * 输出：星座名称或者错误信息 
    */
    function get_zodiac_sign(){ 
        $xz = explode("-", Input::get('time'));
        $month = $xz[1];
        $day = $xz[2];
        // 检查参数有效性 
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) 
        return (false); 
        // 星座名称以及开始日期 
        $signs = array( 
            array( "20" => "水瓶座"), 
            array( "19" => "双鱼座"), 
            array( "21" => "白羊座"), 
            array( "20" => "金牛座"), 
            array( "21" => "双子座"), 
            array( "22" => "巨蟹座"), 
            array( "23" => "狮子座"), 
            array( "23" => "处女座"), 
            array( "23" => "天秤座"), 
            array( "24" => "天蝎座"), 
            array( "22" => "射手座"), 
            array( "22" => "摩羯座") 
        ); 
        list($sign_start, $sign_name) = each($signs[(int)$month-1]); 
        if ($day < $sign_start) 
        list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]); 
        return $sign_name; 
    }
    public function gettimes()
    {
        $args['staffId'] = (int)Input::get('id');
        $staffstime = $this->requestApi('Staffstime.lists',$args);
        return Response::json($staffstime);
    }
    public function showtime()
    {
        $staffstime = $this->requestApi('Staffstime.edit',Input::all());
        return Response::json($staffstime);
    }

}
