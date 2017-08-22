<?php

return [

	/*
	|--------------------------------------------------------------------------
	| 服务人员后台授权菜单
	|--------------------------------------------------------------------------
	*/

    "property" => [
        'name' => '物业',
        'icon' => 'folder-o',
        'url'  => 'PropertyUser/index',
        'controllers' => [
            'SystemConfig' => [
                'name' => '系统设置',
                'icon' => 'laptop',
                'url'  => 'SystemConfig/index',
                'actions' => [
                    'index' => ['name' => '商家列表'],
                    'changepwd'  => ['name' => '修改密码'],
                ]
            ], 
            'PropertyBuilding' => [
                'name' => '房产管理',
                'icon' => 'list-ul',
                'url'  => 'PropertyBuilding/index',
                'actions' => [
                    'index'     => ['name' => '楼宇列表'],
                    'create'    => ['name' => '添加楼宇'],
                    'edit'      => ['name' => '编辑楼宇'],
                    'destroy'   => ['name' => '删除楼宇'],
                    'roomindex'     => ['name' => '房间列表'],
                    'roomcreate'    => ['name' => '添加房间'],
                    'roomedit'      => ['name' => '编辑房间'],
                    'roomdestroy'   => ['name' => '删除房间'],
                    'import'    => ['name' => 'CSV导入'],
                ]
            ],
            'PropertyUser' => [
                'name' => '业主信息',
                'icon' => 'laptop',
                'url'  => 'PropertyUser/index',
                'actions' => [
                    'index' => ['name' => '业主列表'],
                    'check' => ['name' => '门禁列表'],
                    'edit' => ['name' => '编辑门禁'],
                    'destroy' => ['name' => '删除'],
                    'destroydoor' => ['name' => '删除门禁'],
                    'export' => ['name' => '导出'],
                ]
            ],
            'PuserApply' => [
                'name' => '业主身份审核',
                'icon' => 'cart-plus',
                'url'  => 'PuserApply/index',
                'actions' => [
                    'index' => ['name' => '业主认证列表'],
                    'edit'  => ['name' => '详情']
                ]
            ],
            'Repair' => [
                'name' => '报修管理',
                'icon' => 'glass',
                'url'  => 'Repair/index',
                'actions' => [
                    'index' => ['name' => '列表'],
                    'edit'  => ['name' => '详情']
                ]
            ], 
            'Article' => [
                'name' => '公告管理',
                'icon' => 'tasks',
                'url'  => 'Article/index',
                'actions' => [
                    'index'     => ['name' => '公告列表'],
                    'create'    => ['name' => '添加公告'],
                    'edit'      => ['name' => '公告编辑'],
                    'destroy'   => ['name' => '删除公告'],
                ]
            ],
            // 'PropertyFee' => [
            //     'name' => '费用管理',
            //     'icon' => 'leanpub',
            //     'url'  => 'PropertyFee/index',
            //     'actions' => [
            //         'index' => ['name' => '费用列表'],
            //         'create' => ['name' => '添加费用'],
            //         'edit' => ['name' => '编辑费用'],
            //         'destroy' => ['name' => '删除费用'],
            //     ]
            // ],
        ],
       
        'nodes'=> [
            /* 'PropertyBuilding' => [
                'name' => '房产管理',
                'icon' => 'laptop',
                'url'  => 'PropertyBuilding/index',
                'controllers' => [
                    'PropertyBuilding' => [
                        'name' => '楼宇管理',
                        'icon' => 'list-ul',
                        'url'  => 'PropertyBuilding/index',
                        'actions' => [
                            'index'     => ['name' => '楼宇列表'],
                            'create'    => ['name' => '添加楼宇'],
                            'edit'      => ['name' => '编辑楼宇'],
                            'destroy'   => ['name' => '删除楼宇'],
                            'roomindex'     => ['name' => '房间列表'],
                            'roomcreate'    => ['name' => '添加房间'],
                            'roomedit'      => ['name' => '编辑房间'],
                            'roomdestroy'   => ['name' => '删除房间'],
                        ]
                    ],
                    'PropertyRoom' => [
                        'name' => '房间管理',
                        'icon' => 'list-ul',
                        'url'  => 'PropertyRoom/index',
                        'actions' => [
                            'index'     => ['name' => '房间列表'],
                            'create'    => ['name' => '添加房间'],
                            'edit'      => ['name' => '编辑房间'],
                            'destroy'   => ['name' => '删除房间'],
                        ]
                    ],
                ]
            ],
            
            'Article' => [
                'name' => '公告管理',
                'icon' => 'laptop',
                'url'  => 'Article/index',
                'controllers' => [
                    'Article' => [
                        'name' => '公告管理',
                        'icon' => 'tasks',
                        'url'  => 'Article/index',
                        'actions' => [
                            'index'     => ['name' => '公告列表'],
                            'create'    => ['name' => '添加公告'],
                            'edit'      => ['name' => '公告编辑'],
                            'destroy'   => ['name' => '删除公告'],
                        ]
                    ],
                ]
            ],*/
           /* 'DoorAccess' => [
                'name' => '门禁管理',
                'icon' => 'laptop',
                'url'  => 'DoorAccess/index',
                'controllers' => [
                    'DoorAccess' => [
                        'name' => '门禁管理',
                        'icon' => 'tasks',
                        'url'  => 'DoorAccess/index',
                        'actions' => [
                            'index'     => ['name' => '商家门禁'],
                            'create'    => ['name' => '添加门禁'],
                            'edit'      => ['name' => '门禁编辑'],
                            'destroy'   => ['name' => '删除门禁'],
                        ]
                    ],
                    'DoorOpenLog' => [
                        'name' => '门禁使用记录',
                        'icon' => 'tasks',
                        'url'  => 'DoorOpenLog/index',
                        'actions' => [
                            'index'     => ['name' => '使用记录列表'],
                        ]
                    ],
                ]
            ],*/
        ],
    ],

   "funds" => [
        'name' => '资金',
        'icon' => 'money',
        'url'  => 'Funds/index',
        'controllers' => [
            'Funds' => [
                'name' => '资金管理',
                'icon' => 'money',
                'url'  => 'Funds/index',
                'actions' => [
                    'index' => ['name' => '资金'],
                ]
            ],
            'Bank' => [
                'name' => '银行卡管理',
                'icon' => 'cc-discover',
                'url'  => 'Bank/index',
                'actions' => [
                    'index' => ['name' => '资金'],
                    'withdraw' => ['name' => '提现'],
                    'changebankcard' => ['name' => '更换绑定银行卡'],
                ]
            ],
        ]
    ],

];
