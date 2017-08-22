<?php
namespace YiZan\Utils;

use Lang;

class Format {
	public static function time($val, $code = '', $data = [], $attrs = []) {
        return Time::toDate($val);
    }

    public static function image($val, $code = '', $data = [], $attrs = []) {
        $href = $val;
        $src  = $val;
        if (isset($attrs['iscut']) && $attrs['iscut'] == 1) {
            $src  = $val.'@60w_60h_1e_1c_1o.jpg';
        }
        return "<a href='{$href}' target='_blank'><img src='{$src}' style='max-width:60px; max-height:60px;' /></a>";
    }
    
    public static function status($val, $code, $data = [], $attrs = []){
        $status = (int)$val;
        $title  = $title0 = $title1 = '点击切换';
        if (!isset($attrs['lang'])) {
            $attrs['lang'] = 'statusset';
        } 

        $title = Lang::get('admin.'.$attrs['lang'].'.'.$val);
        $title0 = Lang::get('admin.'.$attrs['lang'].'.0');
        $title1 = Lang::get('admin.'.$attrs['lang'].'.1');

        $attrs = ' title="'.$title.'" data-status-0="'.$title0.'" data-status-1="'.$title1.'"';
        switch($status){
            case '0':
                return '<i '.$attrs.' class="fa fa-lock table-status table-status0" status="1" field="'.$code.'"> </i>';
            break;
            case '1':
                return '<i '.$attrs.' class="fa fa-check text-success table-status table-status1" status="0" field="'.$code.'"> </i>';
            break;
            default:
                return '';
            break;
        }
    }
}