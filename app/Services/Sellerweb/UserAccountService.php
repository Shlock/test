<?php

namespace YiZan\Services\Sellerweb;

use YiZan\Models\SellerExtend;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SellerWithdrawMoney;
use YiZan\Models\SellerMoneyLog;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Exception,
    DB,
    Lang,
    Validator,
    App;

class UserAccountService extends \YiZan\Services\BaseService {

    /**
     * 服务人员的可提现金额
     * @return [type] [description]
     */
    public static function getAccount($sellerId) {
        $result = SellerExtend::where('seller_id', $sellerId)
                ->select(DB::Raw('(total_money - money - use_money) as lock_money'), 'money', 'wait_confirm_money')
                ->first();
        // print_r($result->toArray());
        //   exit;
        if (empty($result)) {
            $result['money'] = 0;
            $result['lockMoney'] = 0;
            $result['waitConfirmMoney'] = 0;
        } else {
            $result1['money'] = $result->money;
            $result1['lockMoney'] = round($result->lock_money, 2);
            $result1['lockMoney'] = $result1['lockMoney'] == 0 ? 0 : $result1['lockMoney'];
            $result1['waitConfirmMoney'] = $result->wait_confirm_money;

            $result = $result1;
        }
        return $result;
    }

    /**
     * 服务人员提款申请
     * @return [type] [description]
     */
    public static function createWithdraw($sellerId, $id, $money, $mobile, $verifyCode) {

        //验证服务人员银行卡信息
        $bankinfo = BankInfoService::getBankInfo($sellerId, $id);

        if (empty($bankinfo) || $bankinfo['bank'] == '' || $bankinfo['bankNo'] == '') {
            $result['code'] = 10154;
            return $result;
        }
        //验证服务人员余额是否足够本次提现
        $current_money = self::getAccount($sellerId);

        if ($current_money['money'] < $money) {
            $result['code'] = 10153;
            return $result;
        }

        $data = array(
            'money' => $money,
            'mobile' => $mobile,
            'code' => $verifyCode
        );

        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );

        $messages = array(
            'mobile.required' => 10101,
            'mobile.regex' => 10102,
            'code.required' => 10103,
            'code.size' => 10104,
        );

        $rules = array(
            'money' => ['required'],
            'mobile' => ['required', 'regex:/^1[0-9]{10}$/'],
            'code' => ['required', 'size:6'],
        );

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_WITHDRAW);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        $withdraw = new SellerWithdrawMoney();

        $withdraw->sn = Helper::getSn();
        $withdraw->seller_id = $sellerId;
        $withdraw->money = $money;
        $withdraw->name = $bankinfo['name'];
        $withdraw->bank = $bankinfo['bank'];
        $withdraw->bank_no = $bankinfo['bankNo'];
        $withdraw->content = $content;
        $withdraw->create_time = Time::getTime();
        $withdraw->create_day = Time::getNowDay();

        DB::beginTransaction();
        //插入取款表
        $withdraw_status = $withdraw->save();
        //修改商家可提现金额
        $extend_status = SellerExtend::where('seller_id', $sellerId)->update(array('money' => $current_money['money'] - $money));
        //插入资金流水表
        \YiZan\Services\SellerMoneyLogService::createLog($sellerId, SellerMoneyLog::TYPE_APPLY_WITHDRAW, $withdraw->id, $money, '提款银行：' . $withdraw->bank . ',提款帐号：' . $withdraw->bank_no);

        if ($withdraw_status && $extend_status) {
            UserVerifyCode::destroy($verifyCodeId);
            DB::commit();
        } else {
            DB::rollback();
            $result['code'] = 10155;
            return $result;
        }

        return $result;
    }

    /**
     * 服务人员提款列表
     * @return [type] [description]
     */
    public static function logLists($sellerId, $beginDate, $endDate, $status, $page, $pageSize) {
        if (empty($beginDate)) {
            $beginTime = 0;
        } else {
            $beginTime = Time::toTime($beginDate);
        }

        if (empty($endDate)) {
            $endTime = UTC_DAY;
        } else {
            $endTime = Time::toTime($endDate);
        }

        $queries = SellerMoneyLog::where('seller_id', $sellerId)->orderBy('id', 'desc');
        if ($status > 0) {
            if ($status == 1) {//查询类型为收入的数据
                $queries->whereIn('type', ['order_refund', 'order_pay', 'order_confirm', 'withdraw_error', 'seller_recharge']);
            } else {//查询类型为支出的数据
                $queries->whereIn('type', ['apply_withdraw', 'withdraw_success', 'delivery_money']);
            }
        }
        $queries->whereBetween('create_day', [$beginTime, $endTime]);
        $result['totalCount'] = $queries->count();
        $result['list'] = $queries->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get()
                ->toArray();
        // print_r($result);exit;
        return $result;
    }

}
