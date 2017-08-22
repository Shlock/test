<?php
//确认提现
define('TPL_VERSION', 20151015);

//通用状态
//-----------通用状态-----------
//禁用
define('STATUS_DISABLED', 0);
//启用
define('STATUS_ENABLED', 1);
//未通过
define('STATUS_NOT_BY', -1);
//待审核
define('STATUS_AUDITING', -2);


//-----------商家提现状态-----------
//确认提现
define('STATUS_WITHDRAW_PASS', 1);
//拒绝提现
define('STATUS_WITHDRAW_REFUSE', 2);
//未审核
define('STATUS_WITHDRAW_STAY', 0);

//------------------订餐订单状态-------------------//
/**
 *下单  100 ---- 用户取消
 */
define('ORDER_STATUS_BEGIN_USER', 100);

/**
 *付款   ---- 用户取消
 */
define('ORDER_STATUS_PAY_SUCCESS', 101);
/**
 * 开始服务与开始配送
 */
define('ORDER_STATUS_START_SERVICE', 105);
/**
 *货到付款   ---- 用户取消
 */
define('ORDER_STATUS_PAY_DELIVERY', 110);

/**
 *商家确认并接单
*/
define('ORDER_STATUS_AFFIRM_SELLER', 102);

/**
 *服务人员确认完成
 */
define('ORDER_STATUS_FINISH_STAFF', 200);

/**
 *系统自动确认完成
 */
define('ORDER_STATUS_FINISH_SYSTEM', 201);

/**
 *会员确认完成
 */
define('ORDER_STATUS_FINISH_USER', 202);

/**
 *会员取消订单  -- 删除
*/
define('ORDER_STATUS_CANCEL_USER', 300);


/**
 *支付超时取消订单 -- 删除
 */
define('ORDER_STATUS_CANCEL_AUTO', 301);

/**
 *商家拒绝取消订单
 */
define('ORDER_STATUS_CANCEL_SELLER', 302);


/**
 *总后台拒绝取消订单 -- 删除
 */
define('ORDER_STATUS_CANCEL_ADMIN', 303);


/**
 * 会员删除订单
 */
 define('ORDER_STATUS_USER_DELETE', 500);

 
  /**
 * 商家删除订单
 */
 
 define('ORDER_STATUS_SELLER_DELETE', 501);
 
  /**
 * 总后台删除订单
 */
 define('ORDER_STATUS_ADMIN_DELETE', 502);

//-----------------------end---------------------//

 /**
  * 退款审核中，举报
  */
  define('ORDER_STATUS_REFUND_AUDITING', 400);
  /**
  * 取消且退款中，举报，删除
  */
  define('ORDER_STATUS_CANCEL_REFUNDING', 401);
  /**
  * 退款处理中，举报
  */
  define('ORDER_STATUS_REFUND_HANDLE', 402);
  /**
  * 退款失败，举报，删除
  */
  define('ORDER_STATUS_REFUND_FAIL', 403);
  /**
  * 退款成功，举报，删除
  */
  define('ORDER_STATUS_REFUND_SUCCESS', 404);
  /**
  * 商家同意退款
  */
  define('ORDER_REFUND_SELLER_AGREE', 405);
  /**
   * 商家拒绝退款
   */
   define('ORDER_REFUND_SELLER_REFUSE', 406);
   /**
   * 总后台同意退款，举报，删除
   */
  define('ORDER_REFUND_ADMIN_AGREE', 407);
  /**
  * 总后台拒绝退款，举报，删除
  */
  define('ORDER_REFUND_ADMIN_REFUSE', 408);

  /**
   *未支付
  */
  define('ORDER_PAY_STATUS_NO', 0);
  /**
   *已支付
  */
  define('ORDER_PAY_STATUS_YES', 1);

//----------------------end-----------------------

//-----------预约------------
//服务时长
define('SERVICE_TIME_LEN', 3600);
//服务时间间隔
define('SERVICE_TIME_SPAN', 3600);
//服务最多预约天数
define('SERVICE_APPOINT_DAY', 4);
//每次预约把开始时间提前
define('SERVICE_DELAY_BEGIN_TIME', 0);
//每次预约把结束时间延后
define('SERVICE_DELAY_END_TIME', 0);
//开始接单时间
define('DEFAULT_BEGIN_ORDER_DATE', 0); // 9时 10 * 60 * 60
//默认结束接单时间
define('DEFAULT_END_ORDER_DATE', 86400); // 21时 21 * 60 * 60
?>