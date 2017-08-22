<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\SystemConfig;

/**
 * 系统配置信息
 */
class SystemConfigService extends \YiZan\Services\SystemConfigService 
{
    public static function getLists($group) 
    {
        return SystemConfig::where('group_code', 'like', '%,'.$group.',%')
            ->where('is_show', '1')
            ->orderBy("sort")
            ->get()
            ->toArray();
    }

	/**
	 * 更新配置信息
     * @param array $configs 系统配置列表
     * @return array   修改结果
	 */
	public static function update($configs) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
        );
        
        if(is_array($configs))
        {
            foreach ($configs as $config)
            {
                SystemConfig::where('code', $config["code"])->update(
                    [
                        //'name'          => $config["name"],
                        'val'           => $config["val"]/*,
                        'show_type'     => $config["show_type"],
                        'default_vals'  => $config["default_vals"],
                        'default_names' => $config["default_names"],
                        'is_show'       => $config["is_show"],
                        'sort'          => $config["sort"],
                        'tooltip'       => $config["tooltip"]*/
                    ]);
            }
        }
        
        SystemConfig::resetConfig();
        
        return $result;
    }
}
