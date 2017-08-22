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
	
	"goods" => [
		'name' => '服务',
		'icon' => 'th-large',
		'url'  => 'Goods/index',
		'controllers' => [  
			'Goods' => [
				'name' => '通用服务库',
				'icon' => 'tasks',
				'url'  => 'Goods/index',
				'actions' => [
					'index' 	=> ['name' => '服务列表','expand' => ['search']],
					'create' 	=> ['name' => '添加服务'],
					'edit' 		=> ['name' => '编辑服务'],
					'destroy' 	=> ['name' => '删除服务'],
				]
			],
			'SellerGoods' => [
				'name' => '商家服务库',
				'icon' => 'bars',
				'url'  => 'SellerGoods/index',
				'actions' => [
					'index' 	=> ['name' => '服务列表','expand' => ['search']],
					'create' 	=> ['name' => '添加服务'],
					'edit' 		=> ['name' => '编辑服务'],
					'lookat'	=> ['name' => '查看服务'],
					'destroy' 	=> ['name' => '删除服务'],
				]
			],
			'GoodsAudit' => [
				'name' => '服务审核',
				'icon' => 'eye-slash',
				'url'  => 'GoodsAudit/index',
				'actions' => [
					'index' 	=> ['name' => '审核列表','expand' => ['search']],
					'detail' 	=> ['name' => '服务详细'],
				]
			],
			'GoodsAccusation' => [
				'name' => '服务举报',
				'icon' => 'check-circle-o',
				'url'  => 'GoodsAccusation/index',
				'actions' => [
					'index' 	=> ['name' => '举报列表'],
					'dispose' 	=> ['name' => '举报处理'],
					'destroy' 	=> ['name' => '删除举报'],
				]
			],
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
			'GoodsCate' => [
				'name' => '分类管理',
				'icon' => 'list-ul',
				'url'  => 'GoodsCate/index',
				'actions' => [
					'index' 	=> ['name' => '分类列表'],
					'create' 	=> ['name' => '添加分类'],
					'edit' 		=> ['name' => '编辑分类'],
					'destroy' 	=> ['name' => '删除分类'],
				]
			], 
		]
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
					'getSellerInfo' => ['name'=>'获取服务人员信息'],
					'getUserInfo' => ['name'=>'获取会员信息'],
					'getUserPromotion' => ['name'=>'获取会员优惠券信息'],
				]
			]
		]
	],
	"seller" => [
		'name' => '人员',
		'icon' => 'user',
		'url'  => 'Seller/index',
		'nodes'=> [ 
			'ServiceApply' => [
				'name' => '服务机构管理',
				'icon' => 'recycle',
				'url'  => 'ServiceApply/index',
				'controllers' => [
					'ServiceApply' => [
						'name' => '机构申请管理',
						'icon' => 'check-circle-o',
						'url'  => 'ServiceApply/index',
						'actions' => [
							'index' 	=> ['name' => '机构申请列表'],
							'edit' 		=> ['name' => '编辑申请'],
							'destroy' 	=> ['name' => '删除申请'],
						]
					],
					'ServiceCertificate' => [
						'name' => '机构资质认证',
						'icon' => 'credit-card',
						'url'  => 'ServiceCertificate/index',
						'actions' => [
							'index' 	=> ['name' => '认证列表'],
							'edit' 		=> ['name' => '编辑认证'],
							'destroy' 	=> ['name' => '删除认证'],
						]
					],  	 
					'Service' => [
						'name' => '服务机构管理',
						'icon' => 'steam',
						'url'  => 'Service/index',
						'actions' => [
							'index' 	=> ['name' => '机构信息列表'],
							'edit' 		=> ['name' => '编辑机构'],
							'create'	=> ['name' => '添加机构'],
							'destroy' 	=> ['name' => '删除机构'],
						]
					],
					'Staff' => [
						'name' => '机构员工管理',
						'icon' => 'check-circle-o',
						'url'  => 'Staff/index',
						'actions' => [
							'index' 	=> ['name' => '员工列表'],
							'create' 	=> ['name' => '添加员工'],
							'edit' 		=> ['name' => '编辑员工'],
							'destroy' 	=> ['name' => '删除员工'],
						]
					], 
				],
			],     
			'sellerApply' => [
				'name' => '人员管理',
				'icon' => 'graduation-cap',
				'url'  => 'UserAppConfig/index',
				'controllers' => [				 	 
					'Seller' => [
						'name' => '人员管理',
						'icon' => 'th-large',
						'url'  => 'Seller/index',
						'actions' => [
							'index' 	=> ['name' => '卖家列表'],
							'create' 	=> ['name' => '创建卖家'],
							'edit' 		=> ['name' => '编辑卖家'],
							'destroy' 	=> ['name' => '删除卖家'], 
							'schedule' 	=> ['name' => '预约时间管理'],
						]
					], 
					'SellerApply' => [
						'name' => '人员申请管理',
						'icon' => 'check-circle-o',
						'url'  => 'SellerApply/index',
						'actions' => [
							'index' 	=> ['name' => '申请列表'],
							'edit' 		=> ['name' => '编辑申请'],
							'destroy' 	=> ['name' => '删除申请'],
						]
					],
					'SellerCertificate' => [
						'name' => '人员资质认证',
						'icon' => 'credit-card',
						'url'  => 'SellerCertificate/index',
						'actions' => [
							'index' 	=> ['name' => '认证列表'],
							'edit' 		=> ['name' => '编辑认证'],
							'destroy' 	=> ['name' => '删除认证'],
						]
					], 
					'SellerCreditRank' => [
						'name' => '人员信誉等级',
						'icon' => 'shirtsinbulk',
						'url'  => 'SellerCreditRank/index',
						'actions' => [
							'index' 	=> ['name' => '等级列表'],
							'create' 	=> ['name' => '创建等级'], 
							'edit' 		=> ['name' => '编辑等级'],
							'destroy' 	=> ['name' => '删除等级'],
						]
					], 
				],
			],  
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
			'complain' => [
				'name' => '机构举报',
				'icon' => 'users',
				'url'  => 'Complain/index',
				'controllers' => [
					'Complain' => [
						'name' => '举报管理',
						'icon' => 'th-large',
						'url'  => 'Complain/index',
						'actions' => [
							'index' 	=> ['name' => '举报列表'],
							'destroy' 	=> ['name' => '删除举报'],
							'dispose' 	=> ['name' => '举报处理'],
						] 
					], 
				]
			],  
		], 
	],
	 "finance" => [
		'name' => '财务',
		'icon' => 'money',
		'url'  => 'UserRefund/index',
		'controllers' => [
			// 'FinanceStatistics' => [
			// 	'name' => '财务概览',
			// 	'icon' => 'bar-chart',
			// 	'url'  => 'UserStatistics/index',
			// 	'actions' => [
			// 		'index' => ['name' => '统计信息'],
			// 	]
			// ],
			'UserRefund' => [
				'name' => '会员退款',
				'icon' => 'user',
				'url'  => 'UserRefund/index',
				'actions' => [
					'index' 	=> ['name' => '退款列表'],
					'edit' 		=> ['name' => '操作退款'],
				]
			],
			'SellerWithdrawMoney' => [
				'name' => '卖家提现',
				'icon' => 'user-secret',
				'url'  => 'SellerWithdrawMoney/index',
				'actions' => [
					'index' 	=> ['name' => '提现列表'],
					'edit' 		=> ['name' => '操作提现'],
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
			'PayLog' => [
				'name' => '支付日志',
				'icon' => 'money',
				'url'  => 'PayLog/index',
				'actions' => [
					'index' 	=> ['name' => '日志列表'], 
				]
			]
		]
	],
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
	"app" => [
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
			// 'sellerapp' => [
			// 	'name' => '服务人员APP',
			// 	'icon' => 'user-secret',
			// 	'url'  => 'SellerAppConfig/index',
			// 	'controllers' => [
			// 		'SellerAppConfig' => [
			// 			'name' => 'APP配置',
			// 			'icon' => 'cog',
			// 			'url'  => 'SellerAppConfig/index',
			// 			'actions' => [
			// 				'index' => ['name' => '编辑配置', 'expand' => ['store']],
			// 			]
			// 		],
			// 		'SellerAppAdvPosition' => [
			// 			'name' => '广告位管理',
			// 			'icon' => 'picture-o',
			// 			'url'  => 'SellerAppAdvPosition/index',
			// 			'actions' => [
			// 				'index' 	=> ['name' => '广告位列表'],
			// 				'create' 	=> ['name' => '创建广告位'],
			// 				'edit' 		=> ['name' => '编辑广告位'],
			// 				'destroy' 	=> ['name' => '删除广告位'],
			// 			]
			// 		],
			// 		'SellerAppAdv' => [
			// 			'name' => '广告管理',
			// 			'icon' => 'picture-o',
			// 			'url'  => 'SellerAppAdv/index',
			// 			'actions' => [
			// 				'index' 	=> ['name' => '广告列表'],
			// 				'create' 	=> ['name' => '创建广告'],
			// 				'edit' 		=> ['name' => '编辑广告'],
			// 				'destroy' 	=> ['name' => '删除广告'],
			// 			]
			// 		],
			// 		'SellerAppMessageSend' => [
			// 			'name' => '信息推送',
			// 			'icon' => 'list',
			// 			'url'  => 'SellerAppMessageSend/index',
			// 			'actions' => [
			// 				'index' 	=> ['name' => '推送列表'],
			// 				'create' 	=> ['name' => '创建推送', 'expand' => ['send']],
			// 				'edit' 		=> ['name' => '编辑推送', 'expand' => ['send']],
			// 				'destroy' 	=> ['name' => '删除菜单'],
			// 			]
			// 		],
			// 		'SellerAppFeedback' => [
			// 			'name' => '意见反馈',
			// 			'icon' => 'comments',
			// 			'url'  => 'SellerAppFeedback/index',
			// 			'actions' => [
			// 				'index' 	=> ['name' => '反馈列表'],
			// 				'edit' 		=> ['name' => '回复反馈'],
			// 				'destroy' 	=> ['name' => '删除反馈'],
			// 			]
			// 		]
			// 	]
			// ],
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
	],
	"system" => [
		'name' => '系统',
		'icon' => 'cogs',
		'url'  => 'Config/index',
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
			// 'AdminLog' => [
			// 	'name' => '日志管理',
			// 	'icon' => 'list',
			// 	'url'  => 'AdminLog/index',
			// 	'actions' => [
			// 		'index' 	=> ['name' => '日志列表'],
			// 		'show' 		=> ['name' => '查看日志'],
			// 		'destroy' 	=> ['name' => '删除日志', 'expand' => ['clear']],
			// 	]
			// ],
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
			]
		]
	]
];
