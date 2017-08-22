<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\SellerComplain;
use YiZan\Services\SellerStaffService;
use YiZan\Utils\Time;
use Lang;

/**
 * 机构员工举报
 */
class StaffComplainService extends \YiZan\Services\StaffComplainService
{
   /**
     * [create 机构员工举报增加]
     * @param  [type] $userId   [用户编号]
     * @param  [type] $staffId [机构员工编号]
     * @param  [type] $content  [举报内容]
     * @return [type]           [description]
     */
    public static function create($userId, $staffId, $content)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.goods_complain_create')
        );
        $staff = SellerStaffService::getById($staffId);
        if ($staffId < 1 || !$staff) {
            $result['code'] = 90001;
            return $result; 
        }
        if ($content == '') {
            $result['code'] = 90002;
            return $result;
        }
        $seller_complain = new SellerComplain;
        $seller_complain->seller_id = $staff->seller_id;
        $seller_complain->seller_staff_id = $staffId;
        $seller_complain->user_id = $userId;
        $seller_complain->content = $content;
        $seller_complain->create_time = UTC_TIME;
        $seller_complain->status = 0;
        $seller_complain->save();
        return $result;

    } 
}
