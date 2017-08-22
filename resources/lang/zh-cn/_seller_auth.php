<?php

return [

	/*
	|--------------------------------------------------------------------------
	| 服务人员后台授权菜单
	|--------------------------------------------------------------------------
	*/
    
	"general" => [
		'code' => 'Index',
		'name' => '概况',
		'icon' => 'th-list',
		'url'  => 'Index/index',
		'controllers' => [
			'Index' => [
				'name' => '服务概览',
				'icon' => 'bar-chart',
				'url'  => 'GoodsStatistics/index',
				'actions' => [
					'index' 	=> ['name' => '统计信息'],
					'service' 	=> ['name' => '服务器信息'],
					'upload' 	=> ['name' => '上传'],
					'repwd' 	=> ['name' => '修改密码', 'expand' => ['dopwd']], 
					'demo' 		=> ['name' => '演示'],
				]
			],
		],
	],
    
    "service" => [
        'name' => '项目',
        'icon' => 'folder-o',
        'url'  => 'SellerService/index',
        'controllers' => [
            'SellerService' => [
                'name' => '服务管理',
                'icon' => 'laptop',
                'url'  => 'SellerService/index',
                'actions' => [
                    'index' => ['name' => '服务列表'],
                    'create' => ['name' => '添加服务'],
                    'edit' => ['name' => '编辑服务'],
                    'destroy' => ['name' => '删除服务'],
                ]
            ],
            'SellerGoods' => [
                'name' => '商品管理',
                'icon' => 'cart-plus',
                'url'  => 'SellerGoods/index',
                'actions' => [
                    'index' => ['name' => '商品列表'],
                    'create' => ['name' => '添加商品'],
                    'edit' => ['name' => '编辑商品'],
                    'destroy' => ['name' => '删除商品'],
                ]
            ],
            // 'DeliverySet' => [
            //     'name' => '配送设置',
            //     'icon' => 'glass',
            //     'url'  => 'DeliverySet/index',
            //     'actions' => [
            //         'index' => ['name' => '列表']
            //     ]
            // ], 
            'GoodsCate' => [
                'name' => '分类管理',
                'icon' => 'leanpub',
                'url'  => 'GoodsCate/index',
                'actions' => [
                    'index' => ['name' => '分类列表'],
                    'create' => ['name' => '添加分类'],
                    'edit' => ['name' => '编辑分类'],
                    'destroy' => ['name' => '删除分类'],
                ]
            ],
            'Article' => [
                'name' => '公告管理',
                'icon' => 'bell',
                'url'  => 'Article/index',
                'actions' => [
                    'index' => ['name' => '公告列表'],
                    'create' => ['name' => '添加公告'],
                    'edit' => ['name' => '编辑公告'],
                    'destroy' => ['name' => '删除公告'],
                ]
            ],
        ]
    ],
    "staff" => [
        'name' => '人员',
        'icon' => 'user',
        'url'  => 'Staff/index',
        'controllers' => [
            'Staff' => [
                'name' => '人员管理',
                'icon' => 'user',
                'url'  => 'Staff/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'create' => ['name' => '添加人员'],
                    'edit' => ['name' => '编辑人员'],
                    'destroy' => ['name' => '删除人员'],
                ]
            ],
            'StaffSchedule' => [
                'name' => '人员日程',
                'icon' => 'eye-slash',
                'url'  => 'StaffSchedule/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'check' => ['name' => '查看']
                ]
            ],
            // 'DeliveryStaff' => [
            //     'name' => '配送人员管理',
            //     'icon' => 'user',
            //     'url'  => 'DeliveryStaff/index',
            //     'actions' => [
            //         'index' => ['name' => '首页'],
            //         'create' => ['name' => '添加人员'],
            //         'edit' => ['name' => '编辑人员'],
            //         'destroy' => ['name' => '删除人员'],
            //     ]
            // ],
            // 'DeliverySchedule' => [
            //     'name' => '配送人员日程',
            //     'icon' => 'eye-slash',
            //     'url'  => 'DeliverySchedule/index',
            //     'actions' => [
            //         'index' => ['name' => '首页'],
            //         'check' => ['name' => '查看']
            //     ]
            // ],
			'Staffleave' => [
                'name' => '人员请假',
                'icon' => 'user',
                'url'  => 'Staffleave/index',
                'actions' => [
                    'index' => ['name' => '首页'],
                    'agree' => ['name' => '同意'],
                    'destroy' => ['name' => '删除请假'],
                ]
            ],
        ]
    ],
    "order" => [
        'name' => '订单',
        'icon' => 'credit-card',
        'url'  => 'ServiceOrder/index',
        'controllers' => [
            'ServiceOrder' => [
                'name' => '服务类订单',
                'icon' => 'th-large',
                'url'  => 'ServiceOrder/index',
                'actions' => [
                    'index' => ['name' => '订单'],
                    'detail' => ['name' => '订单详细'],
                ]
            ],
            'Order' => [
                'name' => '商品类订单',
                'icon' => 'th-large',
                'url'  => 'Order/index',
                'actions' => [
                    'index' => ['name' => '订单'],
                    'detail' => ['name' => '订单详细'],
                ]
            ],
            /*'RefundOrder' => [
                'name' => '退款类订单',
                'icon' => 'th-large',
                'url'  => 'RefundOrder/index',
                'actions' => [
                    'index' => ['name' => '订单'],
                    'detail' => ['name' => '订单详细'],
                ]
            ],*/
            "Comment" => [
                'name' => '订单评价',
                'icon' => 'comments',
                'url'  => 'Comment/index',
                'controllers' => [
                    'Comment' => [
                        'name' => '评价管理',
                        'icon' => 'comments',
                        'url'  => 'Comment/index',
                        'actions' => [
                            'index' => ['name' => '评价'],
                            'reply' => ['name' => '评价回复'],
                        ]
                    ],
                ]
            ],
        ]
    ],
   "funds" => [
        'name' => '资金',
        'icon' => 'money',
        'url'  => 'Funds/index',
        'controllers' => [
            'Funds' => [
                'name' => '账户结算',
                'icon' => 'money',
                'url'  => 'Funds/index',
                'actions' => [
                    'index' => ['name' => '账户结算'],
                ]
            ],
            'FundCate' => [
                'name' => '分类对账',
                'icon' => 'cubes',
                'url'  => 'FundCate/index',
                'actions' => [
                    'index' => ['name' => '账户结算'],
                ]
            ],
            'BusinessStatistics' => [
                'name' => '我的账单',
                'icon' => 'bars',
                'url'  => 'BusinessStatistics/index',
                'actions' => [
                    'index' => ['name' => '对帐单'],
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
    "report" => [
        'name' => '报表',
        'icon' => 'bar-chart',
        'url'  => 'Report/index',
        'controllers' => [
            'Report' => [
                'name' => '营业统计',
                'icon' => 'bar-chart',
                'url'  => 'Report/index',
                'actions' => [
                    'index' => ['name' => '报表'],
                ]
            ],
            'GoodsReport' => [
                'name' => '商品统计',
                'icon' => 'bar-chart',
                'url'  => 'GoodsReport/index',
                'actions' => [
                    'index' => ['name' => '报表'],
                ]
            ],
        ]
    ],
    
];
