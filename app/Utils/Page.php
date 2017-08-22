<?php namespace YiZan\Utils;

use Config;

class Page {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $parameter='',$listRows=20, $url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   'page' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   isset($_REQUEST[$this->varPage]) ? max((int)$_REQUEST[$this->varPage], 1) : 1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url;
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    public function nums(){
        $pager = array();
        $pager['total_count'] = $this->totalRows;
        $pager['page'] = $this->nowPage;
        $pager['page_size'] = $this->listRows;
        /* page 总数 */
        $pager['page_count'] = $this->totalPages;
        $p = $this->varPage;
        $url =   $this->url;
        // 分析分页参数
        if(empty($url)){
            $ca = '';
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
                if(isset($parameter['ca'])){
                    $ca = $parameter['ca'];
                    unset($parameter['ca']);
                }
            }elseif(empty($this->parameter)){
                unset($_GET[$this->varPage], $_POST[$this->varPage]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$this->varPage]  =   '__PAGE__';
            $url = u($ca,$parameter);
        }
        $pager['parameter'] = $this->parameter;
        $page_prev  = ($pager['page'] > 1) ? $pager['page'] - 1 : 1;
        $page_next  = ($pager['page'] < $pager['page_count']) ? $pager['page'] + 1 : $pager['page_count'];
        $pager['prev_page'] = $page_prev;
        $pager['next_page'] = $page_next;

        $pager['page_first'] = str_replace('__PAGE__', 1, $url);
        $pager['page_prev']  = str_replace('__PAGE__', $page_prev, $url);
        $pager['page_next']  = str_replace('__PAGE__', $page_next, $url);
        $pager['page_last']  = str_replace('__PAGE__', $this->totalPages, $url);

        $pager['page_nums'] = array();
        if($pager['page_count'] <= $this->rollPage * 2) {
            for ($i=1; $i <= $pager['page_count']; $i++) {
                $pager['page_nums'][] = array('name' => $i,'url' => str_replace('__PAGE__', $i, $url));
            }
        } else {
            if($pager['page'] - $this->rollPage < 2) {
                $temp = $this->rollPage * 2;
                for ($i=1; $i<=$temp; $i++) {
                    $pager['page_nums'][] = array('name' => $i,'url' => str_replace('__PAGE__', $i, $url));
                }
                $pager['page_nums'][] = array('name'=>'...');
                $pager['page_nums'][] = array('name' => $pager['page_count'],'url' => str_replace('__PAGE__', $pager['page_count'], $url));
            } else {
                $pager['page_nums'][] = array('name' => 1, 'url' => str_replace('__PAGE__', 1, $url));
                $pager['page_nums'][] = array('name'=>'...');
                $start = $pager['page'] - $this->rollPage + 1;
                $end   = $pager['page'] + $this->rollPage - 1;

                if($pager['page_count'] - $end > 1) {
                    for ($i=$start;$i<=$end;$i++) {
                        $pager['page_nums'][] = array('name' => $i,'url' => str_replace('__PAGE__', $i, $url));
                    }

                    $pager['page_nums'][] = array('name'=>'...');
                    $pager['page_nums'][] = array('name' => $pager['page_count'],'url' => str_replace('__PAGE__', $pager['page_count'], $url));
                } else {
                    $start = $pager['page_count'] - $this->rollPage * 2 + 1;
                    $end = $pager['page_count'];
                    for ($i=$start;$i<=$end;$i++) {
                        $pager['page_nums'][] = array('name' => $i,'url' => str_replace('__PAGE__', $i, $url));
                    }
                }
            }
        }
        return $pager;
    }
}
