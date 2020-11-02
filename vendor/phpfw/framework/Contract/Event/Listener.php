<?php
namespace Phpfw\Component\Contract\Event;

interface Listener
{
	public function handle(Event $event);
}