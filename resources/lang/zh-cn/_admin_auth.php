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
					'index' 	=> ['name' => '统计信息', 'show_menu' => 1],
				]
			],
		],
		
	],
	"sellergoods" => [
		'name' => '商家',
		'icon' => 'th-large',
		'url'  => 'Service/index',
		'nodes'=> [
            'sellermanage' => [
				'name' => '商家管理',
				'icon' => 'user',
				'url'  => 'Service/index',
				'controllers' => [
					'Service' => [
						'name' => '商家管理',
						'icon' => 'tasks',
						'url'  => 'Service/index',
						'actions' => [
		                    'index' 	=> ['name' => '商家列表', 'show_menu' => 1],
		                    'create'	=> ['name' => '添加商家'],
		                    'edit'		=> ['name' => '商家编辑', 'expand' => ['banksave', 'delbank', 'gettimes', 'showtime', 'updatetime', 'addtime', 'deldatatime']],
		                    'destroy' 	=> ['name' => '删除商家'],
		                    'export'	=> ['name' => '导出到Excel'],

		                    'cateLists' => ['name'=>'分类列表'],
		                    'cateedit' => ['name'=>'分类编辑', 'expand' => ['catesave']],

		                    'serviceLists' 	=> ['name' => '服务列表'],
		                    'serviceEdit' => ['name'=>'服务编辑', 'expand' => ['serviceSave']],

		                    'goodsLists' => ['name'=>'商品列表'],
		                    'goodsEdit' => ['name'=>'商品编辑', 'expand' => ['goodsSave']],
						]
					],
					'Staff' => [
						'name' => '人员管理',
						'icon' => 'list-ul',
						'url'  => 'Staff/index',
						'actions' => [
							'index' 	=> ['name' => '人员列表', 'expand' => ['search'], 'show_menu' => 1],
							'create' 	=> ['name' => '添加人员'],
							'edit' 		=> ['name' => '编辑人员'],
							'destroy' 	=> ['name' => '删除人员'],
						]
					],
				]
			],
        ],
		'controllers' => [
            'SellerApply' => [
				'name' => '商家审核',
				'icon' => 'comments',
				'url'  => 'SellerApply/index',
				'actions' => [
						'index' 	=> ['name' => '商家审核列表', 'show_menu' => 1],
						'edit' 		=> ['name' => '编辑商家审核'],
						'destroy' 	=> ['name' => '删除商家审核'],
					]
			],
			'SellerCate' => [
				'name' => '商家分类',
				'icon' => 'comments',
				'url'  => 'SellerCate/index',
				'actions' => [
						'index' 	=> ['name' => '分类列表', 'show_menu' => 1],
						'create' 	=> ['name' => '添加分类'],
						'edit' 		=> ['name' => '编辑分类'],
						'destroy' 	=> ['name' => '删除分类'],
					]
			],
		]

	],
	
	"propertymanage" => [
		'name' => '物业',
		'icon' => 'th-large',
		'url'  => 'Property/index',
        'nodes'=> [
            'propertygs' => [
				'name' => '物业公司管理',
				'icon' => 'user',
				'url'  => 'Property/index',
				'controllers' => [
					'Property' => [
						'name' => '物业公司',
						'icon' => 'tasks',
						'url'  => 'Property/index',
						'actions' => [
		                    'index' 			=> ['name' => '物业公司列表'],
		                    'create'			=> ['name' => '添加物业公司'],
		                    'edit'				=> ['name' => '物业公司编辑'],
		                    'destroy' 			=> ['name' => '删除物业公司'], 
		                    'export'			=> ['name' => '导出到Excel'],

		                    'dooropenlog' 		=> ['name' => '门禁记录'],
		                    'dooraccess' 		=> ['name' => '门禁列表'], 
		                    'dooredit' 			=> ['name' => '添加门禁','expand' => ['doorsave']],  

		                    'buildingindex' 	=> ['name' => '楼宇列表'],
							'buildingcreate' 	=> ['name' => '添加楼宇'],
							'buildingedit' 		=> ['name' => '编辑楼宇','expand' => ['buildingsave']],
							'buildingdestroy' 	=> ['name' => '删除楼宇'],

							'roomindex' 		=> ['name' => '房间列表'],
							'roomcreate' 		=> ['name' => '添加房间'],
							'roomedit' 			=> ['name' => '编辑房间','expand' => ['roomsave']],
							'roomdestroy' 		=> ['name' => '删除房间'],

							'puserindex' 		=> ['name' => '业主列表'],
							'pusercheck' 		=> ['name' => '查看门禁'],
							'pusercreate'		=> ['name' => '添加业主门禁'],
							'puseredit' 		=> ['name' => '编辑门禁','expand' => ['pusersave']],
							'puserdestroyaccess'=> ['name' => '删除门禁'],
							'puserdestroy' 		=> ['name' => '删除业主'],

							'repairindex' 		=> ['name' => '报修管理'],
							'repairdetail' 		=> ['name' => '报修详情'],
							'repairsave' 		=> ['name' => '保存报修'], 

							'articleindex' 		=> ['name' => '公告列表'],
							'articlecreate' 	=> ['name' => '添加公告'],
							'articleedit' 		=> ['name' => '编辑公告','expand' => ['articlesave']],
							'articledestroy' 	=> ['name' => '删除公告'],
						]
					],
					'PropertyApply' => [
						'name' => '物业审核',
						'icon' => 'comments',
						'url'  => 'PropertyApply/index',
						'actions' => [
							'index' 	=> ['name' => '商家审核列表'], 
		                    'detail'	=> ['name' => '物业公司信息'],
						]
					], 
				]
			],
        ],
		
		'controllers' => [
            'RepairType' => [
                'name' => '报修类型管理',
                'icon' => 'comments',
                'url'  => 'RepairType/index',
                'actions' => [
                    'index' 	=> ['name' => '报修类型列表', 'show_menu' => 1],
                    'create' 	=> ['name' => '添加报修类型'],
                    'edit' 		=> ['name' => '编辑报修类型'],
                    'destroy' 	=> ['name' => '删除报修类型'],
                ]
            ], 
		] 
	],
	"order" => [
		'name' => '订单',
		'icon' => 'credit-card',
		'url'  => 'OrderStatistics/index',
        'nodes'=> [
            'ordermanage' => [
                'name' => '订单管理',
                'icon' => 'th-large',
                'url'  => 'Order/index',
                'controllers' => [
                    'Order' => [
                        'name' => '商品订单',
                        'icon' => 'th-large',
                        'url'  => 'Order/index',
                        'actions' => [
                            'index' 	=> ['name' => '商品订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '编辑商品订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '商品订单导出Excel'],
                            'destroy' 	=> ['name' => '删除商品订单'],
                        ]
                    ],
                    'ServiceOrder' => [
                        'name' => '服务订单',
                        'icon' => 'th-large',
                        'url'  => 'ServiceOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '服务订单列表', 'show_menu' => 1],
                            'detail' 	=> ['name' => '编辑服务订单', 'expand' => ['reassign', 'refundRemark']],
                            'export'	=> ['name' => '服务订单导出Excel'],
                            'destroy' 	=> ['name' => '删除服务订单'],
                        ]
                    ],

                    /*'RefundOrder' => [
                        'name' => '退款订单',
                        'icon' => 'th-large',
                        'url'  => 'RefundOrder/index',
                        'actions' => [
                            'index' 	=> ['name' => '订单列表'],
                            'createlist'=> ['name' => '创建订单'],
                            'destroy' 	=> ['name' => '删除订单'],
                            'detail' 	=> ['name' => '订单详细'],
                            'export'	=> ['name' => '导出到Excel'],
                            'refundRemark' 	=> ['name' => '退款备注'],

                        ]
                    ],*/

                ],
            ],
        ],
		'controllers' => [
            'OrderRate' => [
                'name' => '评价管理',
                'icon' => 'comments',
                'url'  => 'OrderRate/index',
                'actions' => [
                    'index' 	=> ['name' => '评价列表', 'show_menu' => 1],
                    'detail' 	=> ['name' => '编辑评价', 'expand' => ['saveRate']],
                    'rateReply' => ['name' => '评价回复'],
                    'destroy' 	=> ['name' => '删除评价'],
                ]
            ],
			 'OrderStatistics' => [
			 	'name' => '订单统计',
			 	'icon' => 'bar-chart',
			 	'url'  => 'OrderStatistics/index',
			 	'actions' => [
			 		'index' => ['name' => '统计信息', 'show_menu' => 1],
			 	]
			 ],
			'OrderConfig' => [
				'name' => '参数配置',
				'icon' => 'cog',
				'url'  => 'OrderConfig/index',
				'actions' => [
					'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['save']],
				]
			],

			/*'OrderComplain' => [
				'name' => '订单举报',
				'icon' => 'ban',
				'url'  => 'OrderComplain/index',
				'actions' => [
					'index' 	=> ['name' => '举报列表'],
					'destroy' 	=> ['name' => '删除举报'],
					'dispose' 	=> ['name' => '举报处理'],
				]
			]*/
		]
	],  
    "Promotion" => [
        'name' => '营销',
        'icon' => 'money',
        'url'  => 'Promotion/index',
        'controllers' => [
            'Promotion' => [
                'name' => '优惠券管理',
                'icon' => 'th-large',
                'url'  => 'Promotion/index',
                'actions' => [
                    'index' 	=> ['name' => '优惠券列表'],
                    'create' => ['name' => '添加优惠券'],
                    'edit' 		=> ['name' => '编辑优惠券'],
                    'sendsn' 		=> ['name' => '发放优惠券'],
                    'sendsnlist' =>  ['name' => '发放列表'],
                    'updatestatus' 		=> ['name' => '更新优惠券状态'],
                    'destroy' 		=> ['name' => '删除优惠券'],
					'searchUser' 		=> ['name' => '会员搜索'],
					'send' 		=> ['name' => '发放'],
                ]
            ],
            'PromotionSn' => [
                'name' => '优惠券发放管理',
                'icon' => 'th-large',
                'url'  => 'PromotionSn/index',
                'actions' => [
                    'index' 	=> ['name' => '发放列表'],
                    'destroy' 	=> ['name' => '删除优惠券'],
                ]
            ],
            'Activity' => [
                'name' => '营销管理',
                'icon' => 'th-large',
                'url'  => 'Activity/index',
                'actions' => [
                    'index' 	=> ['name' => '营销列表'],
                    'create' 	=> ['name' => '添加营销类型'],
                    'add' 	    => ['name' => '添加营销'],
                    'edit' 		=> ['name' => '查看营销'],
                    'destroy' 	=> ['name' => '删除营销'],
                ]
            ],
            /*
            'ActivityReg' => [
                'name' => '注册活动',
                'icon' => 'th-large',
                'url'  => 'ActivityReg/index',
                'actions' => [
                    'index' 	=> ['name' => '活动列表'],
                    'create' 	=> ['name' => '添加活动'],
                    'edit' 	=> ['name' => '编辑活动'],
                    'save' => ['name' => '保存活动']
                ]
            ],*/
                /*'ShoppingSpree' => [
                    'name' => '活动管理',
                    'icon' => 'th-large',
                    'url'  => 'ShoppingSpree/setting',
                    'actions' => [
                        'index' 	=> ['name' => '活动列表'],
                        'edit' 		=> ['name' => '查看活动'],
                        'destroy' 	=> ['name' => '删除活动'],
                    ]
                ]*/
        ]
    ],
	"user" => [
		'name' => '会员',
		'icon' => 'user',
		'url'  => 'User/index',
		'nodes'=> [ 
			'user' => [
				'name' => '会员管理',
				'icon' => 'user',
				'url'  => 'User/index',
				'controllers' => [
					'User' => [
						'name' => '会员管理',
						'icon' => 'th-large',
						'url'  => 'User/index',
						'actions' => [
							'index' 	=> ['name' => '会员列表'],
							'create' 	=> ['name' => '创建会员'],
							'edit' 		=> ['name' => '编辑会员'],
							'destroy' 	=> ['name' => '删除会员'],
						] 
					], 
				]
			],
            'Friend' => [
                'name' => '生活圈管理',
                'icon' => 'sellsy',
                'url'  => 'ForumPlate/index',
                'controllers' => [
                    'ForumPlate' => [
                        'name' => '板块管理',
                        'icon' => 'cubes',
                        'url'  => 'ForumPlate/index',
                        'actions' => [
                            'index' 	=> ['name' => '板块列表'],
                            'create' 	=> ['name' => '添加板块'],
                            'destroy' 	=> ['name' => '删除板块'],
                            'edit' 		=> ['name' => '编辑板块'],
                        ]
                    ],
                    'ForumPosts' => [
                        'name' => '帖子管理',
                        'icon' => 'list',
                        'url'  => 'ForumPosts/index',
                        'actions' => [
                            'index' 	=> ['name' => '帖子列表'],
                            'destroy' 	=> ['name' => '删除帖子'],
                            'edit' 		=> ['name' => '编辑帖子'],
                            'detail' 	=> ['name' => '帖子详情'],
                        ]
                    ],
                    'PostsCheck' => [
                        'name' => '发帖审核',
                        'icon' => 'eye',
                        'url'  => 'PostsCheck/index',
                        'actions' => [
                            'index' 	=> ['name' => '审核帖子列表'],
                        ]
                    ],
                    'KeyWords' => [
                        'name' => '关键字',
                        'icon' => 'info',
                        'url'  => 'KeyWords/index',
                        'actions' => [
                            'index' 	=> ['name' => '关键字过滤'],
                        ]
                    ],
                    'ForumComplain' => [
                        'name' => '帖子举报',
                        'icon' => 'recycle',
                        'url'  => 'ForumComplain/index',
                        'actions' => [
                            'index' 	=> ['name' => '帖子举报管理'],
                        ]
                    ],
                    'ForumMessage' => [
		                'name' => '论坛消息',
		                'icon' => 'envelope',
		                'url'  => 'ForumMessage/index',
		                'actions' => [
		                    'index' 	=> ['name' => '消息列表'],
		                    'destroy' 	=> ['name' => '删除消息'],
		                ]
		            ],
                ]
            ],
		],
 
	],
	/* 
	"report" => [
		'name' => '报表',
		'icon' => 'bar-chart',
		'url'  => 'SellerStatistics/index',
		'controllers' => [
			'SellerStatistics' => [
			 	'name' => '人员统计',
			 	'icon' => 'user-plus',
			 	'url'  => 'SellerStatistics/index',
			 	'actions' => [
			 		'index' => ['name' => '统计信息'],
			 		'performance' => ['name' => '业绩统计'],
			 	]
			 ],

			 'Performance' => [
			 	'name' => '人员业绩',
			 	'icon' => 'line-chart',
			 	'url'  => 'Performance/index',
			 	'actions' => [
			 		'index' => ['name' => '业绩排行榜'],
			 		'bonus' => ['name' => '抽成排行榜'],
			 		'sellerperformance' => ['name' => '卖家业绩查看'],
			 		'staffperformance' => ['name' => '员工业绩查看'],
			 	]
			 ],
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
	*/
	"finance" => [
		'name' => '财务',
		'icon' => 'money',
		'url'  => 'SellerWithdraw/index',
		'controllers' => [
			'Payment' => [
                'name' => '支付方式',
                'icon' => 'th-large',
                'url'  => 'Payment/index',
                'actions' => [
                    'index' 	=> ['name' => '方式列表', 'show_menu' => 1],
                    'edit' 		=> ['name' => '编辑方式'],
                    'update'	=> ['name' => '编辑方式'],
                ]
            ],
			'PayLog' => [
				'name' => '支付日志',
				'icon' => 'money',
				'url'  => 'PayLog/index',
				'actions' => [
					'index' 	=> ['name' => '日志列表', 'show_menu' => 1], 
				]
			],
			'SellerPayLog' => [
				'name' => '商家支付日志',
				'icon' => 'money',
				'url'  => 'SellerPayLog/index',
				'actions' => [
					'index' 	=> ['name' => '商家支付列表', 'show_menu' => 1, 'expand' =>['search']], 
				]
			],
            'UserRefund' => [
                'name' => '会员退款',
                'icon' => 'user',
                'url'  => 'UserRefund/index',
                'actions' => [
                    'index' 	=> ['name' => '退款列表', 'show_menu' => 1],
                    // 'edit' 		=> ['name' => '操作退款'],
                    'dispose' 	=> ['name' => '操作退款'],
                ]
            ],
            'SellerWithdraw' => [
                'name' => '商家提现',
                'icon' => 'cc-discover',
                'url'  => 'SellerWithdraw/index',
                'actions' => [
                    'index' 	=> ['name' => '提现列表', 'show_menu' => 1],
                    'edit'		=> ['name' => '提现处理'],
                    'dispose'	=> ['name' => '处理提现']
                ]
            ],
            'BusinessStatistics' => [
                'name' => '商家营业统计',
                'icon' => 'list-ol',
                'url'  => 'BusinessStatistics/index',
                'actions' => [
                    'index' 		=> ['name' => '商家营业统计', 'show_menu' => 1], 
                    'monthAccount'	=> ['name' => '对帐单'],
                    'dayAccount'	=> ['name' => '明细']
                ]
            ],
            'PlatformStatistics' => [
                'name' => '平台数据统计',
                'icon' => 'th-list',
                'url'  => 'PlatformStatistics/index',
                'actions' => [
                    'index' 	=> ['name' => '平台数据统计', 'show_menu' => 1], 
                ]
            ],
           /* 
			'UserRefund' => [
				'name' => '会员退款',
				'icon' => 'user',
				'url'  => 'UserRefund/index',
				'actions' => [
					'index' 	=> ['name' => '退款列表'],
					'edit' 		=> ['name' => '操作退款'],
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
			],*//*
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
			],*/
		]
	],
	/*"app" => [
		'name' => '手机APP',
		'icon' => 'android',
		'url'  => 'UserAppConfig/index',
		'nodes'=> [
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
					/*'UserAppIndexMenu' => [
						'name' => '首页菜单管理',
						'icon' => 'th-large',
						'url'  => 'UserAppIndexMenu/index',
						'actions' => [
							'index' 	=> ['name' => '菜单列表'],
							'create' 	=> ['name' => '创建菜单'],
							'edit' 		=> ['name' => '编辑菜单'],
							'update' => ['name' => '保存'],
							'destroy' 	=> ['name' => '删除菜单'],
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
	'sellerapp' => [
		'name' => '服务人员APP',
		'icon' => 'user-secret',
		'url'  => 'SellerAppConfig/index',
		'controllers' => [
			'SellerAppConfig' => [
				'name' => 'APP配置',
				'icon' => 'cog',
				'url'  => 'SellerAppConfig/index',
				'actions' => [
					'index' => ['name' => '编辑配置', 'expand' => ['store']],
				]
			],
			'SellerAppAdvPosition' => [
				'name' => '广告位管理',
				'icon' => 'picture-o',
				'url'  => 'SellerAppAdvPosition/index',
				'actions' => [
					'index' 	=> ['name' => '广告位列表'],
					'create' 	=> ['name' => '创建广告位'],
					'edit' 		=> ['name' => '编辑广告位'],
					'destroy' 	=> ['name' => '删除广告位'],
				]
			],
			'SellerAppAdv' => [
				'name' => '广告管理',
				'icon' => 'picture-o',
				'url'  => 'SellerAppAdv/index',
				'actions' => [
					'index' 	=> ['name' => '广告列表'],
					'create' 	=> ['name' => '创建广告'],
					'edit' 		=> ['name' => '编辑广告'],
					'destroy' 	=> ['name' => '删除广告'],
				]
			],
			'SellerAppMessageSend' => [
				'name' => '信息推送',
				'icon' => 'list',
				'url'  => 'SellerAppMessageSend/index',
				'actions' => [
					'index' 	=> ['name' => '推送列表'],
					'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
					'edit' 		=> ['name' => '编辑推送', 'expand' => ['send']],
					'destroy' 	=> ['name' => '删除菜单'],
				]
			],
			'SellerAppFeedback' => [
				'name' => '意见反馈',
				'icon' => 'comments',
				'url'  => 'SellerAppFeedback/index',
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
			'StaffAppAdv' => [
				'name' => '广告管理',
				'icon' => 'picture-o',
				'url'  => 'StaffAppAdv/index',
				'actions' => [
					'index' 	=> ['name' => '广告列表'],
					'create' 	=> ['name' => '创建广告'],
					'edit' 		=> ['name' => '编辑广告'],
					'destroy' 	=> ['name' => '删除广告'],
				]
			],
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
	] 
		]
	],*/
	"system" => [
		'name' => '系统',
		'icon' => 'cogs',
		'url'  => 'Config/index',
	    'nodes'=> [
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
	                        'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['edit']],
	                    ]
	                ],
	                'UserAppAdvPosition' => [
	                    'name' => '广告位管理',
	                    'icon' => 'picture-o',
	                    'url'  => 'UserAppAdvPosition/index',
	                    'actions' => [
	                        'index' 	=> ['name' => '广告位列表', 'show_menu' => 1],
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
	                        'index' 	=> ['name' => '广告列表', 'show_menu' => 1],
	                        'create' 	=> ['name' => '创建广告'],
	                        'edit' 		=> ['name' => '编辑广告'],
	                        'destroy' 	=> ['name' => '删除广告'],
	                    ]
	                ],
                    /*'Menu' => [
                        'name' => '首页菜单',
                        'icon' => 'picture-o',
                        'url'  => 'Menu/index',
                        'actions' => [
                            'index' 	=> ['name' => '菜单列表', 'show_menu' => 1],
                            'create' 	=> ['name' => '创建菜单'],
                            'edit' 		=> ['name' => '编辑菜单'],
                            'destroy' 	=> ['name' => '删除菜单'],
                        ]
                    ],*/
	                'UserAppMessageSend' => [
	                    'name' => '信息推送',
	                    'icon' => 'list',
	                    'url'  => 'UserAppMessageSend/index',
	                    'actions' => [
	                        'index' 	=> ['name' => '推送列表', 'show_menu' => 1],
	                        'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
	                        'destroy' 	=> ['name' => '删除推送'],
	                    ]
	                ],
	                'UserAppFeedback' => [
	                    'name' => '意见反馈',
	                    'icon' => 'comments',
	                    'url'  => 'UserAppFeedback/index',
	                    'actions' => [
	                        'index' 	=> ['name' => '反馈列表', 'show_menu' => 1],
	                        'edit' 		=> ['name' => '回复反馈'],
	                        'destroy' 	=> ['name' => '删除反馈'],
	                    ]
	                ]
	             ],
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
	                        'index' => ['name' => '编辑配置', 'show_menu' => 1, 'expand' => ['store']],
	                    ]
	                ],
	                'StaffAppMessageSend' => [
	                    'name' => '信息推送',
	                    'icon' => 'list',
	                    'url'  => 'StaffAppMessageSend/index',
	                    'actions' => [
	                        'index' 	=> ['name' => '推送列表', 'show_menu' => 1],
	                        'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
	                        'destroy' 	=> ['name' => '删除推送'],
	                    ]
	                ],
	                'StaffAppFeedback' => [
	                    'name' => '意见反馈',
	                    'icon' => 'comments',
	                    'url'  => 'StaffAppFeedback/index',
	                    'actions' => [
	                        'index' 	=> ['name' => '反馈列表', 'show_menu' => 1],
	                        'edit' 		=> ['name' => '回复反馈'],
	                        'destroy' 	=> ['name' => '删除反馈'],
	                    ]
	                ]
	            ]
	        ]
	    ],
		'controllers' => [
			'Config' => [
				'name' => '系统配置',
				'icon' => 'cogs',
				'url'  => 'Config/index',
				'actions' => [
					'index' => ['name' => '系统管理', 'show_menu' => 1, 'expand' => ['save']]
				]
			],
			'Article' => [
				'name' => '文章管理',
				'icon' => 'file-text-o',
				'url'  => 'Article/index',
				'actions' => [
					'index' 	=> ['name' => '文章列表', 'show_menu' => 1],
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
					'index' 	=> ['name' => '分类列表', 'show_menu' => 1],
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
					'index' 	=> ['name' => '管理员列表', 'show_menu' => 1],
					'create' 	=> ['name' => '创建管理员'],
					'edit' 		=> ['name' => '编辑管理员'],
					'destroy' 	=> ['name' => '删除管理员'],
					'repwd'		=> ['name' => '修改密码', 'expand' => ['checkRepwd']],
				]
			],
			'AdminRole' => [
				'name' => '管理员组',
				'icon' => 'users',
				'url'  => 'AdminRole/index',
				'actions' => [
					'index' 	=> ['name' => '组列表', 'show_menu' => 1],
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
					'index' => ['name' => '更新缓存', 'show_menu' => 1, 'expand' => ['clear', 'local']],
				]
			],
			'City' => [
				'name' => '城市管理',
				'icon' => 'list',
				'url'  => 'City/index',
				'actions' => [
					'index' 	=> ['name' => '开通城市列表', 'show_menu' => 1],
					'create' 	=> ['name' => '添加开通城市', 'expand' => ['isdefault']],
					'destroy' 	=> ['name' => '删除开通城市'],
				]
			],
			'District' => [
				'name' => '小区管理',
				'icon' => 'delicious',
				'url'  => 'District/index',
				'actions' => [
					'index' 	=> ['name' => '小区列表', 'show_menu' => 1],
					'create' 	=> ['name' => '小区城市'],
					'destroy' 	=> ['name' => '删除小区'],
					'edit' 	=> ['name' => '编辑小区'],
				]
			],
		]
	]
];
