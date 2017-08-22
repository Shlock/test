<?php
namespace YiZan\Services;

use YiZan\Models\Menu;
use DB, Exception,Validator;

/**
 * 管理员组
 */
class MenuService extends BaseService {

    /**
     * 列表
     * @param string $clientType 类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          广告信息
     */
    public static function getList($page,$pageSize)
    {
        //刷新活动
        $list = Menu::orderBy('id', 'desc');

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }



    /**
     * 根据编号获取活动
     * @param  integer $id 活动编号
     * @return array       活动信息
     */
    public static function getById($id) {
        if($id < 1){
            return false;
        }
        return Menu::find($id);
    }

    /**
     * 删除活动
     */
    public static function delete($id){
        if($id < 1){
            return false;
        }

        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> ''
        );

        return Menu::where('id',$id)->delete();
    }

    /**
     * 修改活动
     */
    public static function update($id,$name,$cityId,$menuIcon,$type,$arg,$sort,$status){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'cityId'        => ['required'],
            'menuIcon'        => ['required'],
            'type'        => ['required'],
            'arg'        => ['required']
        );

        $messages = array(
            'name.required'     => 10301,
            'cityId.required'   => 41420,
            'menuIcon.required'    => 41313,
            'type.required'        => 41421,
            'arg.required'        => 41422,
        );

        $validator = Validator::make([
            'name'          => $name,
            'cityId'        => $cityId,
            'menuIcon'    => $menuIcon,
            'type'        => $type,
            'arg'        => $arg
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $sort = empty($sort) ? 100:$sort;

        if(!empty($id)){
            $Menu = Menu::where('id',$id)->first();
            $Menu->id = $id;
            $Menu->name = $name;
            $Menu->city_id = $cityId;
            $Menu->menu_icon = $menuIcon;
            $Menu->arg = $arg;
            $Menu->type = $type;
            $Menu->sort = $sort;
            $Menu->status = $status;
            $Menu->save();
        }else{
            $Menu = new Menu();
            $Menu->name = $name;
            $Menu->city_id = $cityId;
            $Menu->menu_icon = $menuIcon;
            $Menu->arg = $arg;
            $Menu->type = $type;
            $Menu->sort = $sort;
            $Menu->status = $status;
            $Menu->create_time = UTC_TIME;
            $Menu->save();
        }

        return $result;
    }

    public function updateStatus($id,$status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if ($id < 1) {
            $result['code'] = 30214;
            return $result;
        }
        $status = $status > 0 ? 1 : 0;

        Menu::where('id',$id)->update(['status' => $status]);
        return $result;
    }

}
