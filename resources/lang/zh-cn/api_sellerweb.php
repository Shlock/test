<?php

return 
[
	'success' => 
	[
		'schedule_update' => '状态更新成功',
        'add' => '添加成功',
        'update' => '更新成功',
        'delete' => '删除成功',
        'success' => '操作成功',
	],
	'code' =>
    [
        '10003' => '短信发送失败',
        '10101' => '手机号码不能为空',
        '10102' => '手机号码不正确',
        '10103' => '验证码不能为空',
        '10104' => '验证码不正确',
        '10105' => '密码不能为空',
        '10106' => '密码错误，密码由6~20位的字符组成',
        '10107' => '名称不能为空',
        '10108' => '名称格式不对，名称由2-30位字符组成',
        '10109' => '出生年月不能为空',
        '10110' => '头像不能为空',
        '10111' => '请选择所在省',
        '10112' => '请选择所在市',
        '10113' => '请选择所在区',
        '10114' => '身份证号码不能为空',
        '10115' => '身份证格式不对',
        '10116' => '身份证正面不能为空',
        '10117' => '身份证背面不能为空',
        '10118' => '手机号码已存在',
        '10119' => '注册失败，请稍候再试或者联系客服',
        '10120' => '手机号码未注册',
        '10121' => '两次新密码不一致',
        '10122' => '证件号码不对',
        '10123' => '手机号码未注册',
        '10124' => '登录密码错误',
        '10125' => '服务人员不存在',
        '10126' => '请输入地址',
        '10127' => '请选择地图定位',
        '10128' => '地图定位错误',
        '10129' => '请选择服务范围',
        '10130' => '服务范围错误',
        '10131' => '找不到卖家信息',
        '10132' => '您暂时还未通过审核',
        '10133' => '您的账户已经被锁定，请联系客服',
        '10150' => '银行不能为空',
        '10151' => '银行卡号不能为空',
        '10152' => '提款金额必须大于0',
        '10153' => '提款金额大于可提款金额',
        '10154' => '请完善银行卡信息',
        '10155' => '提款失败',
        '10156' => '请输入正确的银行卡号',
        '10157' => '户主名不能为空',
        '11123' => '未找到餐厅',
        '10201' => '保存头像失败',
        '10202' => '保存头像失败',
        '10203' => '保存身份证正面图片失败',
        '10204' => '保存身份证背面图片失败',
        '10205' => '公司名称不能为空',
        '10206' => '公司营业执照不能为空',
        '10207' => '公司营业执照相片不能为空',
        '10208' => '卡号户主不能为空',
        '21030' => '添加门禁失败，此门禁PID已被添加',
        
        '30028' => '不存在此门禁',
        '30029' => '请输入门禁名称',
        '30030' => '请输入设备标签',
        '30031' => '请输入安装人姓名',
        '30032' => '请输入安装人电话',
        '30033' => '注册设备失败',
        '30034' => '申请钥匙失败',

        '30101' => '父分类不存在',
        '30102' => '名称不能为空',
        '30201' => '请选择服务人员',
        '30202' => '名称不能为空',
        '30203' => '请设置正确的价格',
        '30204' => '请设置正确的门店价格',
        '30205' => '门店价格大于服务价格',
        '30206' => '请选择服务分类',
        '30207' => '简介不能为空',
        '30208' => '请上传服务图片',
        '30209' => '请设置服务时长',
        '30210' => '服务时长不能小于1小时',
        '30211' => '服务人员不存在',
        '30212' => '服务分类不存在',
        '30213' => '保存图片失败',
        '30214' => '系统服务不存在',
        '30215' => '服务不存在',
        '30216' => '不允许重复添加系统服务',
        '30217' => '添加服务失败',
        '30301' => '举报不存在',
        '30302' => '处理结果不能为空',
        '30401' => '订单评价不存在',
        '30402' => '回复内容不能为空',
        '30501' => '身份认证不存在',
        '30502' => '备注信息不能为空（未通过时必填）',
        '30601' => '请输入手机号码',
        '30602' => '手机号码格式错误',
        '30603' => '手机号码已被注册',
        '30604' => '请输入名称',
        '30605' => '请上传LOGO图片',
        '30606' => 'LOGO图片保存失败',
        '30607' => '请选择所在省',
        '30608' => '所在省不存在',
        '30609' => '请选择所在市',
        '30610' => '所在市不存在',
        '30611' => '请选择所在县',
        '30612' => '所在县不存在',
        '30613' => '请输入地址',
        '30614' => '请选择地图定位',
        '30615' => '地图定位错误',
        '30616' => '请选择服务范围',
        '30617' => '服务范围错误',
        '30618' => '请输入真实姓名',
        '30619' => '请输入身份证号码',
        '30620' => '请输入正确的身份证号码',
        '30621' => '身份证号码已存在',
        '30622' => '请上传身份证正面图片',
        '30623' => '身份证正面图片保存失败',
        '30624' => '请上传身份证背面图片',
        '30625' => '身份证背面图片保存失败',
        '30626' => '请上传资质认证图片',
        '30627' => '资质认证图片保存失败',
        '30628' => '个人相册图片保存失败',
        '30901' => '名称不能为空',
        '30902' => '名称已存在',
        '30903' => '图标不能为空',
        '30904' => '图标保存失败',
        '30905' => '请设置最低信誉分',
        '30906' => '最低信誉分跟其他等级冲突',
        '30907' => '请设置最高信誉分',
        '30908' => '最高信誉分不能小于最低信誉分',
        '30909' => '最高信誉分跟其他等级冲突', 
        '30629' => '服务人员不存在',
        '30630' => '请输入小区',
        '50101' => '订单不存在',
        '50102' => '请输入处理结果',
        '50103' => '订单状态错误',
        '50104' => '该订单不允许删除',
        '50201' => '该员工不存在',
        '50202' => '请输入员工姓名',
        '50203' => '请输入正确的电话号码',
        '50204' => '请输入员工地址',
        '50205' => '请选择员工机构',
        '50206' => '密码不能为空',
        '50207' => '会员不存在',
        '50208' => '机构不存在',
        '50209' => '头像不能为空',
        '50210' => '请选择省份',
        '50211' => '请选择城市',
        '50212' => '请选择区县',
        '50213' => '请选择地图定位坐标',
        '50214' => '地图定位错误',
        '50215' => '手机号码已注册为员工',
        '50216' => '头像保存失败',
        '50217' => '相册保存失败',
        '50218' => '密码输入错误',
        '50219' => '请选择正确的出生日期',
        '50220' => '服务范围错误',
        '50221' => '有不存在的小区',
        '50222' => '结束时间不能小于开始时间',
        '50223' => '不存在的商家服务',

        '50224' => '商品名称不能为空',
        '50225' => '商品图片不能为空',
        '50226' => '商品描述不能为空',
        '50227' => '商品价格不能为空',
        '50228' => '商品价格不能错误',
        '50229' => '输入长度限制',

        '51000' => '分类名称不能为空',
        '51001' => '请选择所属行业分类',
        '51002' => '分类不存在',

        '50301' => '员工编号为空',
        '50302' => '状态不合法',
        '50303' => '小时列表不能为空',
        '50304' => '状态更新失败',

        '50401' => '请输入小区名称',
        '50402' => '请选择所在省',
        '50403' => '请选择所在市',
        '50404' => '请选择所在县',
        '50405' => '请选择服务范围',
        '50406' => '服务范围错误',
        '50407' => '添加小区失败',
        '50508' => '删除小区失败',

        '50601' => '请假记录不存在',
        '50602' => '删除失败',
        '50603' => '状态不合法',
        '50604' => '操作失败',


        '50701' => '时间参数错误',
        '50702' => '存在已设置过的时间',
        '50703' => '服务时间设置失败',
        '50704' => '服务时间记录不存在',
        '50705' => '删除服务时间失败',
        '50706' => '创建商家支付日志失败',
        '50707' => '支付方式不存在',

        '60000' => '名称不能为空',
        '60001' => '负责人不能为空',
        '60002' => '联系电话不能为空',
        '60003' => '开始营业时间不能为空',
        '60004' => '结束营业时间不能为空',
        '60005' => '请上传营业执照图片',
        '60006' => '请输入营业执照号',
        '60007' => '营业执照有效时间不能为空',
        '60008' => '手机号格式不正确',
        '60009' => '常驻地址不能为空',

        '60101' => '请输入标题',
        '60103' => '请输入公告内容',

        '60010' => '服务站参数错误',
        '60011' => '请选择分类',
        '60012' => '餐厅参数错误',
        '60013' => '菜品名称不能为空',
        '60014' => '请上传菜品图片',
        '60015' => '请选择参与服务',
        '60016' => '现价必须为数字',
        '60017' => '现价必须大于0',
        '60018' => '原价必须为数字',
        '60019' => '原价必须大于0',
        '60020' => '状态错误',
        '60021' => '状态错误',
        '60022' => '排序必须为数字',
        '60023' => '排序不能为空',

        '60024' => '菜品必须处于下架状态才能编辑',
        '60025' => '菜品正在审核暂不能编辑',
        
        '80101' => '订单编号不合法',
        '80102' => '服务人员不存在',
        '80103' => '不在服务人员服务时间段内',
        '80104' => '指派失败',
        '80105' => '暂无合适的服务人员指派',
        '80106' => '订单不存在',
            '80107' => '状态还不能指派',
            '80108' => '服务内容不能为空',
            '80109' => '服务金额不能为空',
            '80110' => '状态还不能指派',
            '80111' => '指定人员不能为空',
            '80112' => '订单编号不能为空',
            '80113' => '服务金额必须是数字',
            '80114' => '不在服务人员服务范围内',
            '80115' => '服务人员在请假期间,不能指派',
        '80000' => '指派成功',

        '80201' => '楼栋号不能为空',
        '80202' => '物业公司不能为空',
        '80203' => '此楼栋不存在',
        '80204' => '房间号不能为空',
        '80205' => '门禁截止时间不能为空',
        '80206' => '业主不能为空',
        '80207' => '业主已有该门禁钥匙',
        '80208' => '此门禁不存在',
        '80209' => '此业主不存在',
        '80210' => '此业主已经审核过',
        '80211' => '此房间已存在',
        '80212' => '与业主姓名不匹配，请联系业主再次核对',
        '80213' => '与业主电话不匹配，请联系业主再次核对',
        '80214' => '拒绝原因不能为空',
        '80215' => '此报修项不存在',
        '80216' => '此报修正在处理中或已处理',
        '80217' => '物业费不能为空',
        '80218' => '导入失败，请检查导入文件是否正确无误',
        '80219' => '房间号与楼栋号不匹配',
        '80220' => '此楼栋号已存在',
        '80221' => '费用不能为空',
        '80222' => '日期不能为空',
        '80223' => '此缴费项不存在',
        
        '80300' => '查询时间不能大于90天',
        
        '99900' => '操作失败',
        '99996' => '需要登录才能调用此接口',
        '99997' => 'TOKEN错误',
        '99998' => '安全错误',
        '99999' => '程序处理错误',
	]
];
