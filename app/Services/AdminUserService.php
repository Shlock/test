<?php namespace YiZan\Services;

use YiZan\Models\AdminUser;
use YiZan\Models\AdminUserCity;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Validator, Exception, Lang;

class AdminUserService extends BaseService 
{
    /**
     * 管理员列表
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          管理员信息
     */
    public static function getAdminUserlist($page, $pageSize) {
        $list = AdminUser::with('role.access');
        
        $totalCount = $list->count();
        
		$list = $list->orderBy('id', 'DESC')
			->skip(($page - 1) * $pageSize)
			->take($pageSize)
			->get()
			->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 根据登录名称获取管理员
     * @param  string $name 登录名称
     * @return array          管理员信息
     */
	public static function getByName($name) 
    {
		return AdminUser::where('name', $name)
            ->with('role.access')
            ->first();
	}
    /**
     * 添加管理员
     * @param  string $name 登录名称
     * @param  string $pwd 密码
     * @param  int $rid 角色编号
     */
    public static function saveAdminUser($id, $name, $pwd, $rid, $cityIds)
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'rid'           => ['gt:0', 'exists:admin_role,id'],
        );
        
        $messages = array (
            'rid.gt'        => 10107,   // 分组不存在
            'rid.exists'    => 10107,   // 分组不存在
        );

        if ($id < 1) {
            $rules['name']              = ['required', 'unique:admin_user'];
            $rules['pwd']               = ['required'];
            $messages['name.required']  = 10101;   // 帐号名称不能为空
            $messages['name.unique']    = 10106;   // 帐号已存在
            $messages['pwd.required']   = 10103;   // 密码不能为空
        }

        $validator = Validator::make([
                'name'  => $name,
                'pwd'   => $pwd,
                'rid'   => $rid
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if ($id > 0) {//管理员不存在
            $adminUser = AdminUser::find($id);
            if (!$adminUser) {
                $result['code'] = 10102;
                return $result;
            }
        } else {
            $adminUser = new AdminUser;
            $adminUser->name        = $name;
            $adminUser->create_time = UTC_TIME;
        }

        if (!empty($pwd)) {
            $crypt  = String::randString(6);
            $pwd    = md5(md5($pwd) . $crypt);

            $adminUser->crypt   = $crypt;
            $adminUser->pwd     = $pwd;
        }

        DB::beginTransaction();
        try {
            $adminUser->rid = $rid;
            $adminUser->save();

            //城市
            AdminUserCity::where('admin_user_id', $adminUser->id)->delete();
            $cityIds = is_array($cityIds) ? $cityIds : [];
            foreach($cityIds as $cityId){
                $adminUserCity                 = new AdminUserCity();
                $adminUserCity->admin_user_id  = $adminUser->id;
                $adminUserCity->city_id        = $cityId;
                $adminUserCity->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 根据id获取管理员
     * @param  int $id 登录名称
     * @return array   管理员信息
     */
	public static function getById($id) {
		return AdminUser::where('id', $id)
            ->with('role.access', 'citys.city')
            ->first();
	}

    /**
     * 删除管理员
     * @param  int $id 管理员编号
     */
    public static function deleteAdminUser($id) {
        AdminUser::where('id', $id)->delete();
    }

    /**
     * 更新密码
     * @param  int $id 管理员编号
     * @param  string $oldPwd 旧密码
     * @param  string $newPwd 新密码
     */
    public static function updateAdminUserPassword($id, $oldPwd, $newPwd) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_user_repwd')
        );

        $rules = array(
            'oldPwd' => ['required'],
            'newPwd' => ['required'],
        );
        
        $messages = array (
            'oldPwd.required'       => 10108,   // 原密码不正确
            'newPwd.required'       => 10109,   // 新密码不能为空
        );

        $validator = Validator::make([
                'oldPwd' => $oldPwd,
                'newPwd' => $newPwd,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $adminUser = AdminUser::find($id);
        if (!$adminUser) {
            $result['code'] = 10102;
            return $result;
        }

        //登录密码错误
        if ($adminUser->pwd != md5(md5($oldPwd) . $adminUser->crypt)) {
            $result['code'] = 10108;
            return $result;
        }

        $crypt  = String::randString(6);
        $pwd    = md5(md5($newPwd) . $crypt);

        $adminUser->crypt   = $crypt;
        $adminUser->pwd     = $pwd;
        $adminUser->save();
        return $result; 
    }
}
