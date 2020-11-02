<?php
namespace Phpfw\App\Events;

use Phpfw\Component\Contract\Event\{Event, Listener};

class WelcomeNewlyRegisteredMemberListener implements Listener
{
	public function handle(Event $event)
	{
		return "Welcome ". $event->getMember();
	}
}