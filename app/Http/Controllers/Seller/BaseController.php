<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Http\Controllers\YiZanViewController;
use View, Response;

class Node
{
    public $mParent = null;
    public $mNode = null;
}

class BaseController extends YiZanViewController {
	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType = 'seller';

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl = 'seller';

	/**
	 * 服务人员信息
	 * @var array
	 */
	protected $seller;

	/**
	 * 服务人员编号
	 * @var int
	 */
	protected $sellerId;

	/**
	 * 树形无限极分类数组
	 * @var Array
	 */
	protected $_cates = [];

	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();
		
		//设置后台管理员
		$this->setSeller(Session::get('seller'));
		View::share('site_config', $this->getConfig());
	}

	/**
	 * 设置后台管理员
	 * @param [type] $user [description]
	 */
	protected function setSeller($seller) {
		if ($seller) {
			$this->seller 	= $seller;
			$this->sellerId = $seller['id'];
			View::share('sellerId',$this->sellerId);
			View::share('seller',$this->seller);
		}

		Session::put('seller', $seller);
	}

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){
		$data['sellerId'] = $this->sellerId;
		return parent::requestApi($method, $args, $data);
	}

	protected function requestApi2($method, $args = [], $data = []){
		return parent::requestApi($method, $args, $data);
	}
	/**
	 * 树形无限极分类显示
	 * @param  int $pid  顶级ID
	 * @param  array  $items 相关参数
	 * @param  int $level  层级
	 * @param  string $levelrel  层级扁平化
	 * @return Array $list 层级多维数组
	 * @return Array $this->_cates 层级二维数组
	 */
	protected function generateTree($pid, $items, $level = 0, $levelrel='')
    {
        $list = [];
        
        $count = count($items);
        
        for($i = 0; $i < $count; $i++)
        {
            for($j = 0; $j < $count; $j++)
            {
                if($items[$j]['pid'] == $items[$i]['id'])
                {
                    $items[$i]['childs'][] = $items[$j];
                }
            }
        }
        /*
		$list = [];
	    foreach($items as $item) {
	    	if ($item['pid'] == $pid) {
	    		$item['level'] = $level;
	    		if($pid==0) {
	    			$item['levelname'] = $item['name'];
	    			$item['levelrel']  = $item['name'];
	    		}else{
	    			$item['levelname'] = str_repeat("&nbsp;&nbsp;&nbsp;", $level)."├".$item['name']; //带有空格的层级关系
	    			$item['levelrel']  = $levelrel.'|'.$item['name'];//带父级及以上的层级关系
	    		}
	    		$this->_cates[] = $item;
	    		$item['childs'] = $this->generateTree($item['id'], $items, $level + 1, $item['levelrel']);
	    		$list[] = $item;
	    	}
	    }
	    return $list;*/
	}

	/**
	 * 修改状态
	 */
	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('common.updateStatus',$args);
		return Response::json($result);
	}
}
