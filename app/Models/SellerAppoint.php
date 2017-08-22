<?php namespace YiZan\Models;

class SellerAppoint extends Base 
{
    /**
     * 拒绝接单
     */
    const REFUSE_APPOINT_STATUS = -1;
    /**
     * 可接单
     */
    const ACCEPT_APPOINT_STATUS = 0;
    /**
     * 有单
     */
    const HAVING_APPOINT_STATUS = 1;
    /**
     * 服务时间间隔(秒)
     */
    const SERVICE_SPAN = 3600; // 1小时 60 * 60
    /**
     * 默认开始接单时间(秒)
     * */
    const DEFAULT_BEGIN_ORDER_DATE = 36000; // 10时 8 * 60 * 60
    /**
     * 默认结束接单时间(秒)
     * */
    const DEFAULT_END_ORDER_DATE = 75600; // 21时 22 * 60 * 60
}
