<?php namespace YiZan\Models;

class UserVerifyCode extends Base { 
	const TYPE_REG 			= 'reg';//注册
	const TYPE_REPWD 		= 'reg';//找回密码
	const TYPE_WITHDRAW 	= 'reg';//提款
	const TYPE_BANKINFO 	= 'reg';//银行卡信息修改 
}
