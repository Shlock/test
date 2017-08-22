<?php namespace YiZan\Events;

use YiZan\Events\Event;

use Illuminate\Queue\SerializesModels;

class UserEvnet extends Event {

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}
}
