<?php namespace YiZan\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider {
	/**
     * 指定是否延缓提供者加载。
     * @var bool
     */
    protected $defer = true;

	/**
     * 执行注册后的启动服务。
     * @return void
     */
	public function boot()
	{
		//
	}

	/**
     * 在容器中注册绑定。
     * @return void
     */
	public function register()
	{
		//
	}
}
