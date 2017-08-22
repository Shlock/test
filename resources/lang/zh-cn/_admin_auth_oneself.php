<?php

return [

	/*
	|--------------------------------------------------------------------------
	| 后台授权菜单
	|--------------------------------------------------------------------------
	*/
	"general" => [
		'code' => 'Index',
		'name' => '概况',
		'icon' => 'th-list',
		'url'  => 'Index/index',
		'controllers' => [
			'Index' => [
				'name' => '统计概览',
				'icon' => 'bar-chart',
				'url'  => 'Index/index',
				'actions' => [
					'index' 	=> ['name' => '统计信息'],
				]
			],
		],
	],
    "order" => [
        'name' => '订单',
        'icon' => 'credit-card',
        'url'  => 'OrderStatistics/index',
        'controllers' => [
            'OrderStatistics' => [
                'name' => '订单统计',
                'icon' => 'bar-chart',
                'url'  => 'OrderStatistics/index',
                'actions' => [
                    'index' => ['name' => '统计信息'],
                ]
            ],
            'OrderConfig' => [
                'name' => '参数配置',
                'icon' => 'cog',
                'url'  => 'OrderConfig/index',
                'actions' => [
                    'index' => ['name' => '编辑配置', 'expand' => ['store']],
                ]
            ],
            'Order' => [
                'name' => '订单管理',
                'icon' => 'th-large',
                'url'  => 'Order/index',
                'actions' => [
                    'index' 	=> ['name' => '订单列表'],
                    'createlist'=> ['name' => '创建订单'],
                    'destroy' 	=> ['name' => '删除订单'],
                    'detail' 	=> ['name' => '订单详细'],
                    'export'	=> ['name' => '导出到Excel'],
                    'refundRemark' 	=> ['name' => '退款备注'],
                    'getGoodsInfo' => ['name'=>'获取服务信息'],
                    'getSellerInfo' => ['name'=>'获取服务机构信息'],
                    'getUserInfo' => ['name'=>'获取会员信息'],
                    'getUserPromotion' => ['name'=>'获取会员优惠券信息'],
                ]
            ],
            //'OrderComplain' => [
            //    'name' => '订单举报',
            //    'icon' => 'ban',
            //    'url'  => 'OrderComplain/index',
            //    'actions' => [
            //       'index' 	=> ['name' => '举报列表'],
            //        'destroy' 	=> ['name' => '删除举报'],
            //        'dispose' 	=> ['name' => '举报处理'],
            //    ]
            //]
        ]
    ],
	"goods" => [
		'name' => '服务',
		'icon' => 'th-large',
		'url'  => 'Service/index',
		'controllers' => [
            'Service' => [
                'name' => '服务管理',
                'icon' => 'reorder',
                'url'  => 'Service/index',
                'actions' => [
                    'index'     => ['name' => '服务列表'],
                    'edit'      => ['name' => '编辑服务'],
                    'destroy'   => ['name' => '删除服务'],
                    'create'    => ['name' => '添加服务']
                ]
            ],
            'Goods' => [
                'name' => '菜品管理',
                'icon' => 'stack-overflow',
                'url'  => 'Goods/index',
                'actions' => [
                    'index'     => ['name' => '菜品列表'],
                    'edit'      => ['name' => '编辑菜品'],
                    'destroy'   => ['name' => '删除菜品'],
                ]
            ],
            'GoodsType' => [
                'name' => '菜品分类',
                'icon' => 'stack-overflow',
                'url'  => 'GoodsType/index',
                'actions' => [
                    'index'     => ['name' => '分类列表'],
                    'edit'      => ['name' => '编辑分类'],
                    'destroy'   => ['name' => '删除分类'],
                ]
            ],
// 			'District' => [
// 				'name' => '小区管理',
// 				'icon' => 'building-o',
// 				'url'  => 'District/index',
// 				'actions' => [
// 					'index' 	=> ['name' => '小区列表','expand' => ['search']],
// 					'createDistrict' 	=> ['name' => '添加小区'],
// 					'edit' 		=> ['name' => '编辑小区'],
// 					'lookat'	=> ['name' => '查看小区'],
// 					'destroy' 	=> ['name' => '删除小区'],
// 					'lookStaff' => ['name' => '查看人员'],
// 					'addStaff' 	=> ['name' => '添加人员'],
// 				]
// 			],
			'OrderRate' => [
				'name' => '评价管理',
				'icon' => 'comments',
				'url'  => 'OrderRate/index',
				'actions' => [
					'index' 	=> ['name' => '评价列表'],
					'rateReply' => ['name' => '评价回复'],
					'destroy' 	=> ['name' => '删除评价'],
				]
			],
		]
	],
	"seller" => [
		'name' => '人员',
		'icon' => 'user',
		'url'  => 'RestaurantApply/index',
        'controllers' => [
            'RestaurantApply' => [
                'name' => '餐厅申请管理',
                    'icon' => 'meh-o',
                    'url'  => 'RestaurantApply/index',
                    'actions' => [
                        'index'     => ['name' => '餐厅申请列表'],
                        'edit'      => ['name' => '编辑申请'],
                        'destroy'   => ['name' => '删除申请'],
                    ]
            ],
            'Seller' => [
                'name' => '服务站管理',
                    'icon' => 'signal',
                    'url'  => 'Seller/index',
                    'actions' => [
                        'index'     => ['name' => '服务站列表'],
                        'edit'      => ['name' => '编辑服务站'],
                        'updateStatus'  => ['name' => '锁定/解锁'],
                        'destroy'   => ['name' => '删除服务站'],
                    ]
            ],
            'Restaurant' => [
                'name' => '餐厅管理',
                'icon' => 'glass',
                'url'  => 'Restaurant/index',
                'actions' => [
                    'index'     => ['name' => '餐厅信息列表'],
                    'edit'      => ['name' => '编辑机构'],
                    'create'    => ['name' => '添加机构'],
                    'destroy'   => ['name' => '删除机构'],
                ]
            ],

            'Staff' => [
                'name' => '人员管理',
                'icon' => 'check-circle-o',
                'url'  => 'Staff/index',
                'actions' => [
                    'index'     => ['name' => '员工列表'],
                    'create'    => ['name' => '添加员工'],
                    'edit'      => ['name' => '编辑员工'],
                    'destroy'   => ['name' => '删除员工'],
                ]
            ],
            'User' => [
                'name' => '会员管理',
                'icon' => 'user',
                'url'  => 'User/index',
                'actions' => [
                    'index'     => ['name' => '会员列表'],
                    'create'    => ['name' => '创建会员'],
                    'edit'      => ['name' => '编辑会员'],
                    'destroy'   => ['name' => '删除会员'],
                ]
            ]
        ]
	],
    "finance" => [
        'name' => '财务',
        'icon' => 'money',
        'url'  => 'Payment/index',
        'controllers' => [
            'PayLog' => [
                'name' => '资金日志',
                'icon' => 'th-large',
                'url'  => 'PayLog/index',
                'actions' => [
                    'index' 	=> ['name' => '方式列表'],
                    'edit' 		=> ['name' => '编辑方式'],
                    'update' 		=> ['name' => '编辑方式'],
                ]
            ],
            'UserRefund' => [
                'name' => '会员退款',
                'icon' => 'user',
                'url'  => 'UserRefund/index',
                'actions' => [
                    'index' 	=> ['name' => '退款列表'],
                    'edit' 		=> ['name' => '操作退款'],
                ]
            ],
            'SellerWithdraw' => [
                'name' => '商家提现',
                'icon' => 'money',
                'url'  => 'SellerWithdraw/index',
                'actions' => [
                    'index' 	=> ['name' => '提现列表'],
                ]
            ],
            'Payment' => [
                'name' => '支付方式',
                'icon' => 'th-large',
                'url'  => 'Payment/index',
                'actions' => [
                    'index' 	=> ['name' => '方式列表'],
                    'edit' 		=> ['name' => '编辑方式'],
                    'update' 		=> ['name' => '编辑方式'],
                ]
            ]
        ]
    ],
    /*"marketing"=>[
        'name' => '营销',
        'icon' => 'volume-up',
        'url'  => 'ActivityTicket/index',
        'controllers'=> [
            'ActivityTicket' => [
                'name' => '促销活动管理',
                'icon' => 'tags',
                'url'  => 'ActivityTicket/index',
                'actions' => [
                    'index' 	=> ['name' => '活动列表'],
                    'create' 	=> ['name' => '新建促销活动'],
                    'edit' 		=> ['name' => '编辑促销活动'],
                    'destroy' 	=> ['name' => '删除促销活动'],

                ]
            ],
            'Promotion' => [
                'name' => '优惠券管理',
                'icon' => 'th-large',
                'url'  => 'Promotion/index',
                'actions' => [
                    'index' 	=> ['name' => '优惠券列表'],
                    'create' 		=> ['name' => '添加优惠券'],
                    'edit' 		=> ['name' => '编辑优惠券'],
                    'sendsn' 		=> ['name' => '发放优惠券'],
                    'sendsnlist' =>  ['name' => '发放列表'],
                    'updatestatus' 		=> ['name' => '更新优惠券状态'],
                    'destroy' 		=> ['name' => '删除优惠券'],
                    'addCarTicket' => ['name' => '添加洗车券'],
                ]
            ],
            'PromotionSn' => [
                'name' => '优惠券发放管理',
                'icon' => 'th-large',
                'url'  => 'PromotionSn/index',
                'actions' => [
                    'index' 	=> ['name' => '发放列表'],
                    'destroy' 		=> ['name' => '删除优惠券'],
                ]
            ],

        ],
    ],*/
	"report" => [
		'name' => '报表',
		'icon' => 'bar-chart',
		'url'  => 'SellerMoneyLog/index',
		'controllers' => [
			 'SellerMoneyLog' => [
				'name' => '资金流水',
				'icon' => 'check-circle-o',
				'url'  => 'SellerMoneyLog/index',
				'actions' => [
					'index' 	=> ['name' => '流水列表'], 
				]
			],
		]
	],

	"system" => [
		'name' => '系统',
		'icon' => 'cogs',
		'url'  => 'UserAppConfig/index',
        'nodes' => [
            'userapp' => [
                'name' => '买家APP',
                'icon' => 'user',
                'url'  => 'UserAppConfig/index',
                'controllers' => [
                    'UserAppConfig' => [
                        'name' => 'APP配置',
                        'icon' => 'cog',
                        'url'  => 'UserAppConfig/index',
                        'actions' => [
                            'index' => ['name' => '编辑配置', 'expand' => ['store']],
                            'edit' => ['name' => '编辑配置'],
                        ]
                    ],
                    'UserAppAdvPosition' => [
                        'name' => '广告位管理',
                        'icon' => 'picture-o',
                        'url'  => 'UserAppAdvPosition/index',
                        'actions' => [
                            'index' 	=> ['name' => '广告位列表'],
                            'create' 	=> ['name' => '创建广告位'],
                            'edit' 		=> ['name' => '编辑广告位'],
                            'destroy' 	=> ['name' => '删除广告位'],
                        ]
                    ],
                     'UserAppAdv' => [
                         'name' => '广告管理',
                         'icon' => 'picture-o',
                         'url'  => 'UserAppAdv/index',
                         'actions' => [
                             'index' 	=> ['name' => '广告列表'],
                             'create' 	=> ['name' => '创建广告'],
                             'edit' 		=> ['name' => '编辑广告'],
                             'destroy' 	=> ['name' => '删除广告'],
                         ]
                     ],
                    'UserAppMessageSend' => [
                        'name' => '信息推送',
                        'icon' => 'list',
                        'url'  => 'UserAppMessageSend/index',
                        'actions' => [
                            'index' 	=> ['name' => '推送列表'],
                            'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
                            'edit' 		=> ['name' => '编辑推送', 'expand' => ['send']],
                            'send' 		=> ['name' => '推送',  'expand' => ['send']],
                            'destroy' 	=> ['name' => '删除菜单'],
                        ]
                    ],
                    'UserAppFeedback' => [
                        'name' => '意见反馈',
                        'icon' => 'comments',
                        'url'  => 'UserAppFeedback/index',
                        'actions' => [
                            'index' 	=> ['name' => '反馈列表'],
                            'edit' 		=> ['name' => '回复反馈'],
                            'destroy' 	=> ['name' => '删除反馈'],
                        ]
                    ]
                ]
            ],
            'staff' => [
                'name' => '服务人员APP',
                'icon' => 'user-secret',
                'url'  => 'StaffAppConfig/index',
                'controllers' => [
                    'StaffAppConfig' => [
                        'name' => 'APP配置',
                        'icon' => 'cog',
                        'url'  => 'StaffAppConfig/index',
                        'actions' => [
                            'index' => ['name' => '编辑配置', 'expand' => ['store']],
                        ]
                    ],
                    'StaffAppAdvPosition' => [
                        'name' => '广告位管理',
                        'icon' => 'picture-o',
                        'url'  => 'StaffAppAdvPosition/index',
                        'actions' => [
                            'index' 	=> ['name' => '广告位列表'],
                            'create' 	=> ['name' => '创建广告位'],
                            'edit' 		=> ['name' => '编辑广告位'],
                            'destroy' 	=> ['name' => '删除广告位'],
                        ]
                    ],
//                     'StaffAppAdv' => [
//                         'name' => '广告管理',
//                         'icon' => 'picture-o',
//                         'url'  => 'StaffAppAdv/index',
//                         'actions' => [
//                             'index' 	=> ['name' => '广告列表'],
//                             'create' 	=> ['name' => '创建广告'],
//                             'edit' 		=> ['name' => '编辑广告'],
//                             'destroy' 	=> ['name' => '删除广告'],
//                         ]
//                     ],
                    'StaffAppMessageSend' => [
                        'name' => '信息推送',
                        'icon' => 'list',
                        'url'  => 'StaffAppMessageSend/index',
                        'actions' => [
                            'index' 	=> ['name' => '推送列表'],
                            'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
                            'edit' 		=> ['name' => '编辑推送', 'expand' => ['send']],
                            'destroy' 	=> ['name' => '删除菜单'],
                        ]
                    ],
                    'StaffAppFeedback' => [
                        'name' => '意见反馈',
                        'icon' => 'comments',
                        'url'  => 'StaffAppFeedback/index',
                        'actions' => [
                            'index' 	=> ['name' => '反馈列表'],
                            'edit' 		=> ['name' => '回复反馈'],
                            'destroy' 	=> ['name' => '删除反馈'],
                        ]
                    ]
                ]
            ] ,
        ],
		'controllers' => [
			'Config' => [
				'name' => '系统配置',
				'icon' => 'cog',
				'url'  => 'Config/index',
				'actions' => [
					'index' => ['name' => '系统配置', 'expand' => ['store']],
					'create' => ['name' => '添加配置'],
					'edit'=> ['name' => '编辑配置'],
					'destroy'=> ['name' => '删除配置'],
				]
			],
			'Article' => [
				'name' => '文章管理',
				'icon' => 'file-text-o',
				'url'  => 'Article/index',
				'actions' => [
					'index' 	=> ['name' => '文章列表'],
					'create' 	=> ['name' => '添加文章'],
					'edit' 		=> ['name' => '编辑文章'],
					'destroy' 	=> ['name' => '删除文章'],
				]
			],
			'ArticleCate' => [
				'name' => '文章分类',
				'icon' => 'list-ul',
				'url'  => 'ArticleCate/index',
				'actions' => [
					'index' 	=> ['name' => '分类列表'],
					'create' 	=> ['name' => '创建分类'],
					'edit' 		=> ['name' => '编辑分类'],
					'destroy' 	=> ['name' => '删除分类'],
				]
			],
			'AdminUser' => [
				'name' => '管理员',
				'icon' => 'user',
				'url'  => 'AdminUser/index',
				'actions' => [
					'index' 	=> ['name' => '管理员列表'],
					'create' 	=> ['name' => '创建管理员'],
					'edit' 		=> ['name' => '编辑管理员'],
					'destroy' 	=> ['name' => '删除管理员'],
					'repwd'		=> ['name' => '修改密码'],
				]
			],
			'AdminRole' => [
				'name' => '管理员组',
				'icon' => 'users',
				'url'  => 'AdminRole/index',
				'actions' => [
					'index' 	=> ['name' => '组列表'],
					'create' 	=> ['name' => '创建组'],
					'edit' 		=> ['name' => '编辑组'],
					'destroy' 	=> ['name' => '删除组'],
				]
			],
			'Cache' => [
				'name' => '缓存管理',
				'icon' => 'cog',
				'url'  => 'Cache/index',
				'actions' => [
					'index' => ['name' => '更新缓存', 'expand' => ['clear']],
					'clear' => ['name' => '清除服务器缓存'],
					'local' => ['name' => '清除本地缓存'],
				]
			],
            'RoomConfig' => [
                'name' => '送餐配置',
                'icon' => 'truck',
                'url'  => 'RoomConfig/index',
                'actions' => [
                    'index' => ['name' => '送餐配置']
                ]
            ],
			'City' => [
				'name' => '城市管理',
				'icon' => 'list',
				'url'  => 'City/index',
				'actions' => [
					'index' 	=> ['name' => '开通城市列表'],
					'create' 	=> ['name' => '添加开通城市'],
					'destroy' 	=> ['name' => '删除开通城市'],
					'edit' 	=> ['name' => '编辑开通城市'],
				]
			],
			'WapModule' => [
				'name' => '首页模块管理',
				'icon' => 'cubes',
				'url'  => 'WapModule/index',
				'actions' => [
					'index' 	=> ['name' => '模块列表'],
					'create' 	=> ['name' => '添加模块'],
					'destroy' 	=> ['name' => '删除模块'],
					'edit' 	=> ['name' => '编辑模块'],
				]
			],
		]
	]
];
