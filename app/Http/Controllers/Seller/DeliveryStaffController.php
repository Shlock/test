<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, Response, Redirect;
/**
 * 员工
 */
class DeliveryStaffController extends StaffController {
    protected $staffType;
    public function __construct() {
        parent::__construct();
        $this->staffType = 1;
    }
}
