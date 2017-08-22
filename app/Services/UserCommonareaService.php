<?php 
namespace YiZan\Services;

use YiZan\Models\UserCommonarea;
use Lang, DB, Validator;

/**
 *会员常用小区
 */
class UserCommonareaService extends BaseService
{
    /**
     * 根据编号获取会员常用小区列表
     * @param  integer $userId 会员编号
     * @return array           地址数组
     */
    public static function getCommonareaList($userId) {
        return UserCommonarea::where('user_id', $userId)
            ->with('district')
            ->orderBy('id','desc')
            ->get()->toArray();
    }

    /**
     * 删除会员常用小区
     * @param integer $userId    会员编号
     * @return array             处理结果
     */
    public static function deleteCommonarea($userId) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.delete_info')
        );
        $bln = UserCommonarea::where('user_id', $userId)
                            ->delete();
        if ($bln) {
            return $result;
        } else {
            $result['code'] = 10208;
            $result['msg'] = Lang::get('api.code.10208');
            return $result;
        }
    }

    /**
     * 创建会员常用小区
     * @param  integer $userId   会员编号
     * @param  string  $districtId  小区ID
     * @return array             处理结果
     */
    public static function addCommonarea($userId, $districtId) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        );

        $rules = array(
            'userId'  => ['required'],
            'districtId' => ['required','number']
        );

        $messages = array(
            'userId.required'  => '77000',
            'districtId.required' => '60312',
            'districtId.number'    => '60312'
        );

        $validator = Validator::make([
                'user_id'   => $userId,
                'district_id'  => $districtId
            ], $rules, $messages);
        if (!$validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $count = UserCommonarea::where('user_id', $userId)->where('district_id',$districtId)->first();
        if ($count) {//存在
            return $result;
        }

        $UserCommonarea = new UserCommonarea;
        $UserCommonarea->user_id = $userId;
        $UserCommonarea->district_id = $districtId;

        if ($UserCommonarea->save()) {
            $result['data'] = $UserCommonarea->toArray();
        } else {
            $result['code'] = 10205;
        }
        return $result;
    }

    
}
