<?php
namespace YiZan\Services;

use YiZan\Models\AdminRole;
use YiZan\Models\AdminUser;
use YiZan\Models\AdminRoleAccess;
use YiZan\Utils\Time;
use DB, Exception, Cache;

/**
 * 管理员组
 */
class AdminRoleService extends BaseService {
	/**
	 * 管理员组列表
	 * @param  int $page     页码
	 * @param  int $pageSize 每页数
	 * @return array          管理员组信息
	 */
	public static function getlist($page, $pageSize) {

		$list = AdminRole::orderBy('id');

		$totalCount = $list->count();

		$list = $list->orderBy('name', 'ASC')
					 ->skip(($page - 1) * $pageSize)
					 ->take($pageSize)
					 ->get()
					 ->toArray();

		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 添加管理员组
	 * @param  string $name   名称
	 * @param  string $access 组权限
	 * @return array   添加信息
	 */
	public static function create($name, $access) {
		$result = array('code' => self::SUCCESS, 'data' => null, 'msg' => '');

		$adminRole = new AdminRole();

		if ($name == false) {
			$result['code'] = 10301; // 名称不能为空

			return $result;
		}

		if ($access == false) {
			$result['code'] = 10302; // 请设置组权限

			return $result;
		}

		$adminRole->name   = $name;
		$adminRole->status = 1;

		$adminRole->save();

		foreach ( $access as $value ) {
			$roleAccess = new AdminRoleAccess();

			$roleAccess->rid    	 = $adminRole->id;
			$roleAccess->controller  = $value["controller"];
			$roleAccess->action 	 = $value["action"];

			$roleAccess->save();
		}

		return $result;
	}

	/**
	 * 根据id获取管理员组
	 * @param  int $id 管理员编号
	 * @return array   管理员组信息
	 */
	public static function getById($id) {
		return AdminRole::where('id', $id)
						->with('access')
						->first();
	}

	/**
	 * 更新管理员组
	 * @param  int    $id     管理员编号
	 * @param  string $name   名称
	 * @param  string $access 组权限
	 * @return array   更新信息
	 */
	public static function update($id, $name, $access) {
		$result = array('code' => self::SUCCESS, 'data' => null, 'msg' => '');

		if ($name == false) {
			$result['code'] = 10301; // 名称不能为空

			return $result;
		}

		if ($access == false) {
			$result['code'] = 10302; // 请设置组权限

			return $result;
		}

		AdminRole::where('id', $id)
				 ->update(["name" => $name]);

		AdminRoleAccess::where("rid", $id)
					   ->delete();

		foreach ( $access as $value ) {
			$roleAccess = new AdminRoleAccess();

			$roleAccess->rid    = $id;
			$roleAccess->controller  = $value["controller"];
			$roleAccess->action = $value["action"];

			$roleAccess->save();
		}

		Cache::forget('_admin_controller_action_navs_'.$id);

		return $result;
	}

	/**
	 * 删除管理员组
	 * @param  int $id 管理员编号
	 * @return array   删除信息
	 */
	public static function delete($id) {
		$result = ['code' => 0, 'data' => null, 'msg' => ""];

		$adminUser = AdminUser::where('rid', $id)
							  ->first();
		if (empty($adminUser)) {
			AdminRole::where('id', $id)
					 ->update(array("status" => 0));
		} else {
			$result['code'] = 60204;
		}

		Cache::forget('_admin_controller_action_navs_'.$id);

		return $result;
	}
}
