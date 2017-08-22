<?php namespace YiZan\Handlers\Events;

use YiZan\Events\UserEvnet;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class UserEventHandler {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  UserEvnet  $event
	 * @return void
	 */
	public function handle(UserEvnet $event)
	{
		//
	}

}
